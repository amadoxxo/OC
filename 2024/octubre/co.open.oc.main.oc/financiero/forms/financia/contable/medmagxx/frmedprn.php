<?php
  namespace openComex;
	ini_set("memory_limit","512M");
	set_time_limit(0);

	$nSwitch = 0;

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
    $cEjProBg = ($_POST['cEjProBg'] != "SI") ? "NO" : $_POST['cEjProBg'];
	}
	
	//Cargando datos dHastaC
	$cTipo         = $_POST['rTipo'];
	$dHastaC       = $_POST['dHastaC'];
	$dDesde        = $_POST['dDesde'];
	$dHasta        = ($_POST['dHasta'] != "") ? $_POST['dHasta'] : $_POST['dHastaC'];
	$nAno          = substr($_POST['dDesde'], 0, 4);
	$gFormato      = $_POST['cFormato'];
	$cFormatoDes   = $_POST['cFormatoDes'];
  $nSecuencia    = $_POST['nSecuencia'];
  
  $vCueRetFue = explode(",",$vSysStr['financiero_cuentas_retefuente']);
  $cCueRetFue = "\"".implode("\",\"", $vCueRetFue)."\"";
  $vCueImpAsu = explode(",",$vSysStr['financiero_cuentas_impuestos_asumidos']);
  $cCueImpAsu = "\"".implode("\",\"", $vCueImpAsu)."\"";
  $vCueRetIva = explode(",",$vSysStr['financiero_cuentas_reteiva']);
  $cCueRetIva = "\"".implode("\",\"", $vCueRetIva)."\"";

	switch ($cTipo) {
		case 1:
			// PINTA POR PANTALLA// ?>
			<html>
				<head>
					<title>Informe de Medios Magneticos</title>
					<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
					<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
					<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
					<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
					<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New  ?>/utility.js'></script>
					<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New  ?>/ajax.js'></script>
					<link rel="stylesheet" type="text/css" href="../../../../../programs/gwtext/resources/css/ext-all.css">
					<script type="text/javascript" src="../../../../../programs/gwtext/adapter/ext/ext-base.js"></script>
					<script type="text/javascript" src="../../../../../programs/gwtext/ext-all.js"></script>
					<script language="JavaScript" src="../../../../../programs/gwtext/conexijs/loading/loading.js"></script>
				</head>
				<script type="text/javascript">
					function f_Datos_Tercero(xTerId,xTerId2){

						var zX    = screen.width;
						var zY    = screen.height;
						var zNx     = (zX-1100)/2;
						var zNy     = (zY-700)/2;
						var zWinPro = 'width=1100,scrollbars=1,height=700,left='+zNx+',top='+zNy;
						var cRuta   = "frdetprn.php";
						var cNomVen = 'zWindow'+Math.ceil(Math.random()*1000);
						zWindow = window.open('',cNomVen,zWinPro);

						document.forms['frgrm']['cTerId'].value  = xTerId;
						document.forms['frgrm']['cTerId2'].value = xTerId2;
						document.forms['frgrm'].target=cNomVen;
						document.forms['frgrm'].action=cRuta;

						document.forms['frgrm'].submit();
					}
				</script>
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
					flush();
		break;
	}

	##Armo el IN del pucidxxx ##
	$cPucId = ""; $vCuenta = array();
	$cTitCue = "";
	for($i=1;$i<=$_POST['nSecuencia'];$i++){
		if($_POST['cPucId'.$i] <> ""){
			$vCuenta[count($vCuenta)] = $_POST['cPucId'.$i];
			$cPucId .= "\"".$_POST['cPucId'.$i]."\" ,";
			$cTitCue .= $_POST['cPucId'.$i].", ";
		}
	}
	$cCuenta = substr($cPucId,0,(strlen($cPucId)-1));
	$cTitCue = substr($cTitCue,0,(strlen($cTitCue)-2));
	//f_Mensaje(__FILE__,__LINE__,$cCuenta );

	$mData = array(); //Datos para pintar

	// f_Mensaje(__FILE__,__LINE__,"CUENTAS".$cTitCue );
	if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
		$cEjePro = 1;
		$nRegistros = 0;
		
		$strPost  = "dHastaC~".$dHastaC."|";
		$strPost .= "dDesde~".$dDesde."|";
		$strPost .= "dHasta~".$dHasta."|";
		$strPost .= "nAno~".$nAno."|";
		$strPost .= "cFormato~".$gFormato."|";
		$strPost .= "cFormatoDes~".$cFormatoDes."|";
		for($i=0; $i<count($vCuenta); $i++){
			$strPost .= "cPucId".($i+1)."~".$vCuenta[$i]."|";
		}
		$strPost .= "cPucId~".$cTitCue."|";
		$strPost .= "nSecuencia~".$nSecuencia."|";
		$strPost .= "rTipo~".$cTipo;
  
    $vParBg['pbadbxxx'] = $cAlfa;                                         	//Base de Datos
    $vParBg['pbamodxx'] = "FACTURACION";                                  	//Modulo
    $vParBg['pbatinxx'] = "MEDIOSMAGNETICOS";                          	    //Tipo Interface
    $vParBg['pbatinde'] = "MEDIOS MAGNETICOS";                              //Descripcion Tipo de Interfaz
    $vParBg['admidxxx'] = "";                                             	//Sucursal
    $vParBg['doiidxxx'] = "";                                             	//Do
    $vParBg['doisfidx'] = "";                                             	//Sufijo
    $vParBg['cliidxxx'] = "";                                             	//Nit
    $vParBg['clinomxx'] = "";                                             	//Nombre Importador
    $vParBg['pbapostx'] = $strPost;																					//Parametros para reconstruir Post
    $vParBg['pbatabxx'] = "";                                             	//Tablas Temporales
    $vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];                    	//Script
    $vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];                        	//cookie
    $vParBg['pbacrexx'] = $nRegistros;                                    	//Cantidad Registros
    $vParBg['pbatxixx'] = 1;                                              	//Tiempo Ejecucion x Item en Segundos
    $vParBg['pbaopcxx'] = "";                                             	//Opciones
    $vParBg['regusrxx'] = $kUser;                                         	//Usuario que Creo Registro
  
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

	if ($cEjePro == 0) {
		##Fin Armo In del pucidxxx ##
		switch ($gFormato) {
			case "1001": // 5295050100

				/**
				 * Ticket 8823-Informacion Exogena
				 * Johana Arboleda Ramos 2014-04-05  09:21
				 * se debe incluir la columna Concepto (vacia), la columna Iva mayor valor del costo o gasto no deducible (valor cero),
				 * la columna Retencion en la fuente practicadas CREE (retencion CREE registrada en el sistema) y
				 * la columna Retencion en la fuente asumidas CREE (valor cero).
				 */

				#Creando tabla temporal de cuentas 2408, 2365, 531520 y 2367
				$cFcoc = "fcod".$nAno;
				$cTabFac = fnCadenaAleatoria();
				$qNewTab  = "CREATE TEMPORARY TABLE IF NOT EXISTS $cAlfa.$cTabFac LIKE $cAlfa.$cFcoc";
				$xNewTab = mysql_query($qNewTab,$xConexion01);

				//Buscando las cuentas de retencion CREE
				$qRetCree  = "SELECT CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx ";
				$qRetCree .= "FROM $cAlfa.fpar0115 ";
				$qRetCree .= "WHERE ";
				$qRetCree .= "pucgruxx LIKE \"23\" AND ";
				$qRetCree .= "pucterxx LIKE \"R\"  AND ";
				$qRetCree .= "pucdesxx LIKE \"%CREE%\" AND ";
				$qRetCree .= "pucdesxx NOT LIKE \"%AUTO%\" AND ";
				$qRetCree .= "regestxx = \"ACTIVO\" ";
				$xRetCree  = f_MySql("SELECT","",$qRetCree,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qRetCree."~".mysql_num_rows($xRetCree));
				$mRetCree = array();
				while ($xRRC = mysql_fetch_array($xRetCree)){
					$mRetCree[count($mRetCree)] = $xRRC['pucidxxx'];
				}

				$cReteCree = "";
				for($nRC=0; $nRC<count($mRetCree); $nRC++) {
					$cReteCree .= "$cAlfa.fcod$nAno.pucidxxx LIKE \"{$mRetCree[$nRC]}\" OR ";
				}
				$cReteCree = substr($cReteCree, 0, strlen($cReteCree)-4);

				$qFcod  = "SELECT * ";
				$qFcod .= "FROM $cAlfa.fcod$nAno ";
				$qFcod .= "WHERE ";
				$qFcod .= "($cAlfa.fcod$nAno.pucidxxx LIKE \"2408%\" OR ";
				$qFcod .= "SUBSTRING($cAlfa.fcod$nAno.pucidxxx,1,4) IN ($cCueRetFue) OR ";
				$qFcod .= "SUBSTRING($cAlfa.fcod$nAno.pucidxxx,1,6) IN ($cCueImpAsu) OR ";
				$qFcod .= "SUBSTRING($cAlfa.fcod$nAno.pucidxxx,1,4) IN ($cCueRetIva)".(($cReteCree != "") ? " OR $cReteCree": "").") AND ";
        $qFcod .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
        
				$qInsert = "INSERT INTO $cAlfa.$cTabFac $qFcod";
				$xInsert = mysql_query($qInsert,$xConexion01);
				#Fin Creando tabla temporal de facturas cabecera

				$qData  = "SELECT ";
				$qData .= "$cAlfa.fcod$nAno.comidxxx,";
				$qData .= "$cAlfa.fcod$nAno.comcodxx,";
				$qData .= "$cAlfa.fcod$nAno.comcscxx,";
				$qData .= "$cAlfa.fcod$nAno.pucidxxx,";
				$qData .= "$cAlfa.fcod$nAno.ctoidxxx,";
				$qData .= "$cAlfa.fcod$nAno.teridxxx,";
				$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx = \"D\",$cAlfa.fcod$nAno.comvlrxx,$cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx ";
				$qData .= "FROM $cAlfa.fcod$nAno ";
				$qData .= "WHERE ";
				$qData .= "$cAlfa.fcod$nAno.pucidxxx IN ($cCuenta) AND ";
				$qData .= "$cAlfa.fcod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND ";
				$qData .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
				$qData .= "GROUP BY $cAlfa.fcod$nAno.teridxxx, $cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
				$qData .= "ORDER BY $cAlfa.fcod$nAno.teridxxx";
				$xData  = f_MySql("SELECT","",$qData,$xConexion01,"");
				// f_Mensaje(__FILE__,__LINE__,$qData." ~ ".mysql_num_rows($xData));

				$mLisCli = array(); $mDatCli = array();
				while ($xDATA = mysql_fetch_array($xData)) {
					if(in_array($xDATA['teridxxx'],$mLisCli) == false) {
						#Trayendo datos teridxxx
						$qDatExt = "SELECT * ";
						$qDatExt .= "FROM $cAlfa.SIAI0150 ";
						$qDatExt .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$xDATA['teridxxx']}\" LIMIT 0,1 ";
						$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
						if(mysql_num_rows($xDatExt) > 0) {
							$xRDE = mysql_fetch_array($xDatExt);
							if ($xRDE['CLIRECOM']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "COMUN";
							}
							if ($xRDE['CLIRESIM']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "SIMPLIFICADO";
							}
							if ($xRDE['CLIGCXXX']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "CONTRIBUYENTE";
							}
							if ($xRDE['CLINRPXX']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "NORESIDENTE";
							}

							//Busco el codigo del pais
							$qCodPai = "SELECT PAIIDNXX FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"{$xRDE['PAIIDXXX']}\" LIMIT 0,1";
							$xCodPai  = f_MySql("SELECT","",$qCodPai,$xConexion01,"");
							if (mysql_num_rows($xCodPai) > 0) {
								$xRCP = mysql_fetch_array($xCodPai);
								$xRDE['PAIIDXXX'] =  ($xRCP['PAIIDNXX'] != "") ? $xRCP['PAIIDNXX'] : $xRDE['PAIIDXXX'];
							}
							//Fin Busco el codigo del pais

							$mDatCli[$xDATA['teridxxx']]['TDIIDXXX'] = $xRDE['TDIIDXXX'];
							$mDatCli[$xDATA['teridxxx']]['CLIAPE1X'] = utf8_encode($xRDE['CLIAPE1X']);
							$mDatCli[$xDATA['teridxxx']]['CLIAPE2X'] = utf8_encode($xRDE['CLIAPE2X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOM1X'] = utf8_encode($xRDE['CLINOM1X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOM2X'] = utf8_encode($xRDE['CLINOM2X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOMXX'] = ($xRDE['TDIIDXXX'] == 31) ? utf8_encode($xRDE['CLINOMXX']) : "";
							$mDatCli[$xDATA['teridxxx']]['CLIDIRXX'] = utf8_encode($xRDE['CLIDIRXX']);
							$mDatCli[$xDATA['teridxxx']]['DEPIDXXX'] = $xRDE['DEPIDXXX'];
							$mDatCli[$xDATA['teridxxx']]['CIUIDXXX'] = $xRDE['CIUIDXXX'];
							$mDatCli[$xDATA['teridxxx']]['PAIIDXXX'] = $xRDE['PAIIDXXX'];

							$mLisCli[] = $xDATA['teridxxx'];
						}
					}

					$nIva = 0; $nPrac = 0; $nAsum = 0; $nComun = 0; $nSimpl = 0; $nDom = 0; $nCreePrac = 0; $nCreeAsum = 0;
					#Calculando valores:
					#Iva mayor del costo o gasto deducible
					#Iva mayor valor del costo o gasto no deducible
					#Retencion en la fuente practicada renta
					#Retencion en la fuente asumida renta
					#Retencion en la fuente practicada iva regimen comun
					#Retencion en la fuente asumida iva regimen simp.
					#Retencion en la fuente practicada iva no domiciliados
					#Retencion en la fuente practicadas CREE
					#Retencion en la fuente asumidas CREE
					#Traigo las cuantas que empiezan por 2408,2365,531520,2367 para le comprobante
					$qFcod  = "SELECT ";
					$qFcod .= "$cAlfa.$cTabFac.pucidxxx, ";
					$qFcod .= "$cAlfa.$cTabFac.tertipxx,";
					$qFcod .= "$cAlfa.$cTabFac.teridxxx, ";
					$qFcod .= "$cAlfa.$cTabFac.tertip2x,";
					$qFcod .= "$cAlfa.$cTabFac.terid2xx, ";
					#para las cuentas 2365 y 2367 los creditos suman, los debitos restan
					$qFcod .= "IF($cAlfa.$cTabFac.commovxx=\"C\",$cAlfa.$cTabFac.comvlrxx,$cAlfa.$cTabFac.comvlrxx*-1) AS comvlrsu,";
					#para la cuentas 2408 y 531520 los creditos restan, los debitos suman
					$qFcod .= "IF($cAlfa.$cTabFac.commovxx=\"C\",$cAlfa.$cTabFac.comvlrxx*-1,$cAlfa.$cTabFac.comvlrxx) AS comvlrre ";
					$qFcod .= "FROM $cAlfa.$cTabFac ";
					$qFcod .= "WHERE ";
					$qFcod .= "$cAlfa.$cTabFac.comidxxx = \"{$xDATA['comidxxx']}\" AND ";
					$qFcod .= "$cAlfa.$cTabFac.comcodxx = \"{$xDATA['comcodxx']}\" AND ";
					$qFcod .= "$cAlfa.$cTabFac.comcscxx = \"{$xDATA['comcscxx']}\" AND ";
					$qFcod .= "$cAlfa.$cTabFac.teridxxx = \"{$xDATA['teridxxx']}\" AND ";
					$qFcod .= "($cAlfa.$cTabFac.pucidxxx LIKE \"2408%\" OR ";
					$qFcod .= "SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,4) IN ($cCueRetFue) OR ";
					$qFcod .= "SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,6) IN ($cCueImpAsu) OR ";
					$qFcod .= "SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,4) IN ($cCueRetIva)".(($cReteCree != "") ? " OR ".str_replace("fcod$nAno", $cTabFac, $cReteCree) : "").") AND ";
					$qFcod .= "$cAlfa.$cTabFac.regestxx = \"ACTIVO\" ";
					$xFcod = mysql_query($qFcod,$xConexion01);
          // f_Mensaje(__FILE__,__LINE__,$mDatCli[$xRF['teridxxx']]['CLICATER']."~".$qFcod." ~ ".mysql_num_rows($xFcod));

					if (mysql_num_rows($xFcod) > 0) {
						while($xRF = mysql_fetch_array($xFcod)) {

							//Verifico si no es una cuenta de retencion Cree
							if (in_array($xRF['pucidxxx'], $mRetCree) == true) {
								$nCreePrac += $xRF['comvlrsu'];
							} else {
								if(substr($xRF['pucidxxx'],0,4) == '2408') {
									$nIva += $xRF['comvlrre'];
								}
								if(in_array(substr($xRF['pucidxxx'],0,4), $vCueRetFue)) {
									$nPrac += $xRF['comvlrsu'];
								}
								if(in_array(substr($xRF['pucidxxx'],0,6), $vCueImpAsu)) {
									$nAsum += $xRF['comvlrre'];
								}
								if(in_array(substr($xRF['pucidxxx'],0,4), $vCueRetIva)) {
									switch ($mDatCli[$xRF['teridxxx']]['CLICATER']) {
										case "CONTRIBUYENTE":
										case "COMUN":
											$nComun += $xRF['comvlrsu'];
										break;
										case "SIMPLIFICADO":
											$nSimpl += $xRF['comvlrsu'];
										break;
										case "NORESIDENTE":
											$nDom += $xRF['comvlrsu'];
										break;
									}
								}
							}
						}
          }

					$mData[$xDATA['teridxxx']]['TDIIDXXX']  = ($mData[$xDATA['teridxxx']]['TDIIDXXX'] <> "")?$mData[$xDATA['teridxxx']]['TDIIDXXX']:$mDatCli[$xDATA['teridxxx']]['TDIIDXXX'];
					$mData[$xDATA['teridxxx']]['teridxxx']  = ($mData[$xDATA['teridxxx']]['teridxxx'] <> "")?$mData[$xDATA['teridxxx']]['teridxxx']:$xDATA['teridxxx'];
					$mData[$xDATA['teridxxx']]['CLIAPE1X']  = ($mData[$xDATA['teridxxx']]['CLIAPE1X'] <> "")?$mData[$xDATA['teridxxx']]['CLIAPE1X']:$mDatCli[$xDATA['teridxxx']]['CLIAPE1X'];
					$mData[$xDATA['teridxxx']]['CLIAPE2X']  = ($mData[$xDATA['teridxxx']]['CLIAPE2X'] <> "")?$mData[$xDATA['teridxxx']]['CLIAPE2X']:$mDatCli[$xDATA['teridxxx']]['CLIAPE2X'];
					$mData[$xDATA['teridxxx']]['CLINOM1X']  = ($mData[$xDATA['teridxxx']]['CLINOM1X'] <> "")?$mData[$xDATA['teridxxx']]['CLINOM1X']:$mDatCli[$xDATA['teridxxx']]['CLINOM1X'];
					$mData[$xDATA['teridxxx']]['CLINOM2X']  = ($mData[$xDATA['teridxxx']]['CLINOM2X'] <> "")?$mData[$xDATA['teridxxx']]['CLINOM2X']:$mDatCli[$xDATA['teridxxx']]['CLINOM2X'];
					$mData[$xDATA['teridxxx']]['CLINOMXX']  = ($mData[$xDATA['teridxxx']]['CLINOMXX'] <> "")?$mData[$xDATA['teridxxx']]['CLINOMXX']:$mDatCli[$xDATA['teridxxx']]['CLINOMXX'];
					$mData[$xDATA['teridxxx']]['CLIDIRXX']  = ($mData[$xDATA['teridxxx']]['CLIDIRXX'] <> "")?$mData[$xDATA['teridxxx']]['CLIDIRXX']:$mDatCli[$xDATA['teridxxx']]['CLIDIRXX'];
					$mData[$xDATA['teridxxx']]['DEPIDXXX']  = ($mData[$xDATA['teridxxx']]['DEPIDXXX'] <> "")?$mData[$xDATA['teridxxx']]['DEPIDXXX']:$mDatCli[$xDATA['teridxxx']]['DEPIDXXX'];
					$mData[$xDATA['teridxxx']]['CIUIDXXX']  = ($mData[$xDATA['teridxxx']]['CIUIDXXX'] <> "")?$mData[$xDATA['teridxxx']]['CIUIDXXX']:$mDatCli[$xDATA['teridxxx']]['CIUIDXXX'];
					$mData[$xDATA['teridxxx']]['PAIIDXXX']  = ($mData[$xDATA['teridxxx']]['PAIIDXXX'] <> "")?$mData[$xDATA['teridxxx']]['PAIIDXXX']:$mDatCli[$xDATA['teridxxx']]['PAIIDXXX'];
					$mData[$xDATA['teridxxx']]['TDIIDXXC']  = ($mData[$xDATA['teridxxx']]['TDIIDXXC'] <> "")?$mData[$xDATA['teridxxx']]['TDIIDXXC']:$mDatCli[$xDATA['teridxxx']]['TDIIDXXX'];
					$mData[$xDATA['teridxxx']]['CLIIDXXC']  = ($mData[$xDATA['teridxxx']]['CLIIDXXC'] <> "")?$mData[$xDATA['teridxxx']]['CLIIDXXC']:$xDATA['teridxxx'];
					$mData[$xDATA['teridxxx']]['CLIAPE1C']  = ($mData[$xDATA['teridxxx']]['CLIAPE1C'] <> "")?$mData[$xDATA['teridxxx']]['CLIAPE1C']:$mDatCli[$xDATA['teridxxx']]['CLIAPE1X'];
					$mData[$xDATA['teridxxx']]['CLIAPE2C']  = ($mData[$xDATA['teridxxx']]['CLIAPE2C'] <> "")?$mData[$xDATA['teridxxx']]['CLIAPE2C']:$mDatCli[$xDATA['teridxxx']]['CLIAPE2X'];
					$mData[$xDATA['teridxxx']]['CLINOM1C']  = ($mData[$xDATA['teridxxx']]['CLINOM1C'] <> "")?$mData[$xDATA['teridxxx']]['CLINOM1C']:$mDatCli[$xDATA['teridxxx']]['CLINOM1X'];
					$mData[$xDATA['teridxxx']]['CLINOM2C']  = ($mData[$xDATA['teridxxx']]['CLINOM2C'] <> "")?$mData[$xDATA['teridxxx']]['CLINOM2C']:$mDatCli[$xDATA['teridxxx']]['CLINOM2X'];
					$mData[$xDATA['teridxxx']]['CLINOMXC']  = ($mData[$xDATA['teridxxx']]['CLINOMXC'] <> "")?$mData[$xDATA['teridxxx']]['CLINOMXC']:$mDatCli[$xDATA['teridxxx']]['CLINOMXX'];
					$mData[$xDATA['teridxxx']]['PAGODEXX'] += $xDATA['comvlrxx'];
					$mData[$xDATA['teridxxx']]['PAGONODE']  = 0;
					$mData[$xDATA['teridxxx']]['IVAXXXXX'] += ($cAlfa == 'MIRCANAX' || $cAlfa == 'TEMIRCANAX' || $cAlfa == 'DEMIRCANAX')? 0 : $nIva;
					$mData[$xDATA['teridxxx']]['IVANOXXX']  = 0;
					$mData[$xDATA['teridxxx']]['PRACXXXX'] += $nPrac;
					$mData[$xDATA['teridxxx']]['ASUMXXXX'] += $nAsum;
					$mData[$xDATA['teridxxx']]['COMUNXXX'] += $nComun;
					$mData[$xDATA['teridxxx']]['SIMPLXXX'] += $nSimpl;
					$mData[$xDATA['teridxxx']]['NDOMXXXX'] += $nDom;
					//Nuevos campos para retecree
					$mData[$xDATA['teridxxx']]['PRACCREE'] += $nCreePrac;
					$mData[$xDATA['teridxxx']]['ASUMCREE'] += $nCreeAsum;
					//Fin Nuevos campos para retecree
				}
			break;
			case "1003":

				/**
				 * Ticket 8823-Informacion Exogena
				 * Johana Arboleda Ramos 2014-04-05  09:43
				 * se debe incluir la columna Concepto (vacia).
				 */

				$qData  = "SELECT ";
				$qData .= "$cAlfa.fcod$nAno.comidxxx,";
				$qData .= "$cAlfa.fcod$nAno.comcodxx,";
				$qData .= "$cAlfa.fcod$nAno.comcscxx,";
				$qData .= "$cAlfa.fcod$nAno.pucidxxx,";
				$qData .= "$cAlfa.fcod$nAno.ctoidxxx,";
				$qData .= "$cAlfa.fcod$nAno.teridxxx,";
				$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx = \"D\",$cAlfa.fcod$nAno.comvlrxx,$cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx, ";
				$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx = \"D\",$cAlfa.fcod$nAno.comvlr01,$cAlfa.fcod$nAno.comvlr01*-1)) AS comvlr01  ";
				$qData .= "FROM $cAlfa.fcod$nAno ";
				$qData .= "WHERE ";
				$qData .= "$cAlfa.fcod$nAno.pucidxxx IN ($cCuenta) AND ";
				$qData .= "$cAlfa.fcod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND ";
				$qData .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
				$qData .= "GROUP BY $cAlfa.fcod$nAno.teridxxx ";
				$qData .= "ORDER BY $cAlfa.fcod$nAno.teridxxx ";
				$xData  = f_MySql("SELECT","",$qData,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qData." ~ ".mysql_num_rows($xData));

				$mLisCli = array(); $mDatCli = array();
				while ($xDATA = mysql_fetch_array($xData)) {
					if(in_array($xDATA['teridxxx'],$mLisCli) == false) {
						#Trayendo datos teridxxx
						$qDatExt  = "SELECT * ";
						$qDatExt .= "FROM $cAlfa.SIAI0150 ";
						$qDatExt .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$xDATA['teridxxx']}\" LIMIT 0,1 ";
						$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");

						if(mysql_num_rows($xDatExt) > 0) {
							$xRDE = mysql_fetch_array($xDatExt);
							if ($xRDE['CLIRECOM']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "COMUN";
							}
							if ($xRDE['CLIRESIM']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "SIMPLIFICADO";
							}
							if ($xRDE['CLIGCXXX']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "CONTRIBUYENTE";
							}
							if ($xRDE['CLINRPXX']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "NORESIDENTE";
							}

							//Busco el codigo del pais
							$qCodPai = "SELECT PAIIDNXX FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"{$xRDE['PAIIDXXX']}\" LIMIT 0,1";
							$xCodPai  = f_MySql("SELECT","",$qCodPai,$xConexion01,"");
							if (mysql_num_rows($xCodPai) > 0) {
								$xRCP = mysql_fetch_array($xCodPai);
								$xRDE['PAIIDXXX'] =  ($xRCP['PAIIDNXX'] != "") ? $xRCP['PAIIDNXX'] : $xRDE['PAIIDXXX'];
							}
							//Fin Busco el codigo del pais

							$mDatCli[$xDATA['teridxxx']]['TDIIDXXX'] = $xRDE['TDIIDXXX'];
							$mDatCli[$xDATA['teridxxx']]['CLIAPE1X'] = utf8_encode($xRDE['CLIAPE1X']);
							$mDatCli[$xDATA['teridxxx']]['CLIAPE2X'] = utf8_encode($xRDE['CLIAPE2X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOM1X'] = utf8_encode($xRDE['CLINOM1X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOM2X'] = utf8_encode($xRDE['CLINOM2X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOMXX'] = ($xRDE['TDIIDXXX'] == 31) ? utf8_encode($xRDE['CLINOMXX']) : "";
							$mDatCli[$xDATA['teridxxx']]['CLIDIRXX'] = utf8_encode($xRDE['CLIDIRXX']);
							$mDatCli[$xDATA['teridxxx']]['DEPIDXXX'] = $xRDE['DEPIDXXX'];
							$mDatCli[$xDATA['teridxxx']]['CIUIDXXX'] = $xRDE['CIUIDXXX'];
							$mDatCli[$xDATA['teridxxx']]['PAIIDXXX'] = $xRDE['PAIIDXXX'];

							$mLisCli[] = $xDATA['teridxxx'];
						}
					}

					$mData[$xDATA['teridxxx']]['TDIIDXXX']  = ($mData[$xDATA['teridxxx']]['TDIIDXXX'] <> "")?$mData[$xDATA['teridxxx']]['TDIIDXXX']:$mDatCli[$xDATA['teridxxx']]['TDIIDXXX'];
					$mData[$xDATA['teridxxx']]['teridxxx']  = ($mData[$xDATA['teridxxx']]['teridxxx'] <> "")?$mData[$xDATA['teridxxx']]['teridxxx']:$xDATA['teridxxx'];
					$mData[$xDATA['teridxxx']]['CLIAPE1X']  = ($mData[$xDATA['teridxxx']]['CLIAPE1X'] <> "")?$mData[$xDATA['teridxxx']]['CLIAPE1X']:$mDatCli[$xDATA['teridxxx']]['CLIAPE1X'];
					$mData[$xDATA['teridxxx']]['CLIAPE2X']  = ($mData[$xDATA['teridxxx']]['CLIAPE2X'] <> "")?$mData[$xDATA['teridxxx']]['CLIAPE2X']:$mDatCli[$xDATA['teridxxx']]['CLIAPE2X'];
					$mData[$xDATA['teridxxx']]['CLINOM1X']  = ($mData[$xDATA['teridxxx']]['CLINOM1X'] <> "")?$mData[$xDATA['teridxxx']]['CLINOM1X']:$mDatCli[$xDATA['teridxxx']]['CLINOM1X'];
					$mData[$xDATA['teridxxx']]['CLINOM2X']  = ($mData[$xDATA['teridxxx']]['CLINOM2X'] <> "")?$mData[$xDATA['teridxxx']]['CLINOM2X']:$mDatCli[$xDATA['teridxxx']]['CLINOM2X'];
					$mData[$xDATA['teridxxx']]['CLINOMXX']  = ($mData[$xDATA['teridxxx']]['CLINOMXX'] <> "")?$mData[$xDATA['teridxxx']]['CLINOMXX']:$mDatCli[$xDATA['teridxxx']]['CLINOMXX'];
					$mData[$xDATA['teridxxx']]['CLIDIRXX']  = ($mData[$xDATA['teridxxx']]['CLIDIRXX'] <> "")?$mData[$xDATA['teridxxx']]['CLIDIRXX']:$mDatCli[$xDATA['teridxxx']]['CLIDIRXX'];
					$mData[$xDATA['teridxxx']]['DEPIDXXX']  = ($mData[$xDATA['teridxxx']]['DEPIDXXX'] <> "")?$mData[$xDATA['teridxxx']]['DEPIDXXX']:$mDatCli[$xDATA['teridxxx']]['DEPIDXXX'];
					$mData[$xDATA['teridxxx']]['CIUIDXXX']  = ($mData[$xDATA['teridxxx']]['CIUIDXXX'] <> "")?$mData[$xDATA['teridxxx']]['CIUIDXXX']:$mDatCli[$xDATA['teridxxx']]['CIUIDXXX'];
					$mData[$xDATA['teridxxx']]['TDIIDXXC']  = ($mData[$xDATA['teridxxx']]['TDIIDXXC'] <> "")?$mData[$xDATA['teridxxx']]['TDIIDXXC']:$mDatCli[$xDATA['teridxxx']]['TDIIDXXX'];
					$mData[$xDATA['teridxxx']]['CLIIDXXC']  = ($mData[$xDATA['teridxxx']]['CLIIDXXC'] <> "")?$mData[$xDATA['teridxxx']]['CLIIDXXC']:$xDATA['teridxxx'];
					$mData[$xDATA['teridxxx']]['CLIAPE1C']  = ($mData[$xDATA['teridxxx']]['CLIAPE1C'] <> "")?$mData[$xDATA['teridxxx']]['CLIAPE1C']:$mDatCli[$xDATA['teridxxx']]['CLIAPE1X'];
					$mData[$xDATA['teridxxx']]['CLIAPE2C']  = ($mData[$xDATA['teridxxx']]['CLIAPE2C'] <> "")?$mData[$xDATA['teridxxx']]['CLIAPE2C']:$mDatCli[$xDATA['teridxxx']]['CLIAPE2X'];
					$mData[$xDATA['teridxxx']]['CLINOM1C']  = ($mData[$xDATA['teridxxx']]['CLINOM1C'] <> "")?$mData[$xDATA['teridxxx']]['CLINOM1C']:$mDatCli[$xDATA['teridxxx']]['CLINOM1X'];
					$mData[$xDATA['teridxxx']]['CLINOM2C']  = ($mData[$xDATA['teridxxx']]['CLINOM2C'] <> "")?$mData[$xDATA['teridxxx']]['CLINOM2C']:$mDatCli[$xDATA['teridxxx']]['CLINOM2X'];
					$mData[$xDATA['teridxxx']]['CLINOMXC']  = ($mData[$xDATA['teridxxx']]['CLINOMXC'] <> "")?$mData[$xDATA['teridxxx']]['CLINOMXC']:$mDatCli[$xDATA['teridxxx']]['CLINOMXX'];
					$mData[$xDATA['teridxxx']]['COMVRL01'] += $xDATA['comvlr01'];
					$mData[$xDATA['teridxxx']]['COMVLRXX'] += $xDATA['comvlrxx'];
				}
			break;
			case "1005":

				/**
				 * Ticket 8823-Informacion Exogena
				 * Johana Arboleda Ramos 2014-04-07  07:50
				 * Reporte 1005-Impuesto a las ventas (descontable)
				 */

				$qData  = "SELECT ";
				$qData .= "$cAlfa.fcod$nAno.comidxxx,";
				$qData .= "$cAlfa.fcod$nAno.comcodxx,";
				$qData .= "$cAlfa.fcod$nAno.comcscxx,";
				$qData .= "$cAlfa.fcod$nAno.pucidxxx,";
				$qData .= "$cAlfa.fcod$nAno.ctoidxxx,";
				$qData .= "$cAlfa.fcod$nAno.teridxxx,";
				$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx = \"C\",$cAlfa.fcod$nAno.comvlrxx,$cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx, ";
				$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx = \"C\",$cAlfa.fcod$nAno.comvlr01,$cAlfa.fcod$nAno.comvlr01*-1)) AS comvlr01  ";
				$qData .= "FROM $cAlfa.fcod$nAno ";
				$qData .= "WHERE ";
				$qData .= "$cAlfa.fcod$nAno.pucidxxx IN ($cCuenta) AND ";
				$qData .= "$cAlfa.fcod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND ";
				$qData .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
				$qData .= "GROUP BY $cAlfa.fcod$nAno.teridxxx ";
				$qData .= "ORDER BY $cAlfa.fcod$nAno.teridxxx ";
				$xData  = f_MySql("SELECT","",$qData,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qData." ~ ".mysql_num_rows($xData));

				$mLisCli = array(); $mDatCli = array();
				while ($xDATA = mysql_fetch_array($xData)) {
					if(in_array($xDATA['teridxxx'],$mLisCli) == false) {
						#Trayendo datos teridxxx
						$qDatExt  = "SELECT * ";
						$qDatExt .= "FROM $cAlfa.SIAI0150 ";
						$qDatExt .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$xDATA['teridxxx']}\" LIMIT 0,1 ";
						$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");

						if(mysql_num_rows($xDatExt) > 0) {
							$xRDE = mysql_fetch_array($xDatExt);
							if ($xRDE['CLIRECOM']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "COMUN";
							}
							if ($xRDE['CLIRESIM']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "SIMPLIFICADO";
							}
							if ($xRDE['CLIGCXXX']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "CONTRIBUYENTE";
							}
							if ($xRDE['CLINRPXX']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "NORESIDENTE";
							}
							//Busco el codigo del pais
							$qCodPai = "SELECT PAIIDNXX FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"{$xRDE['PAIIDXXX']}\" LIMIT 0,1";
							$xCodPai  = f_MySql("SELECT","",$qCodPai,$xConexion01,"");
							if (mysql_num_rows($xCodPai) > 0) {
								$xRCP = mysql_fetch_array($xCodPai);
								$xRDE['PAIIDXXX'] =  ($xRCP['PAIIDNXX'] != "") ? $xRCP['PAIIDNXX'] : $xRDE['PAIIDXXX'];
							}
							//Fin Busco el codigo del pais
							$mDatCli[$xDATA['teridxxx']]['TDIIDXXX'] = $xRDE['TDIIDXXX'];
							$mDatCli[$xDATA['teridxxx']]['CLIAPE1X'] = utf8_encode($xRDE['CLIAPE1X']);
							$mDatCli[$xDATA['teridxxx']]['CLIAPE2X'] = utf8_encode($xRDE['CLIAPE2X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOM1X'] = utf8_encode($xRDE['CLINOM1X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOM2X'] = utf8_encode($xRDE['CLINOM2X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOMXX'] = ($xRDE['TDIIDXXX'] == 31) ? utf8_encode($xRDE['CLINOMXX']) : "";
							$mDatCli[$xDATA['teridxxx']]['CLIDIRXX'] = utf8_encode($xRDE['CLIDIRXX']);
							$mDatCli[$xDATA['teridxxx']]['DEPIDXXX'] = $xRDE['DEPIDXXX'];
							$mDatCli[$xDATA['teridxxx']]['CIUIDXXX'] = $xRDE['CIUIDXXX'];
							$mDatCli[$xDATA['teridxxx']]['PAIIDXXX'] = $xRDE['PAIIDXXX'];

							$mLisCli[] = $xDATA['teridxxx'];
						}
					}

					$mData[$xDATA['teridxxx']]['TDIIDXXX']  = ($mData[$xDATA['teridxxx']]['TDIIDXXX'] <> "")?$mData[$xDATA['teridxxx']]['TDIIDXXX']:$mDatCli[$xDATA['teridxxx']]['TDIIDXXX'];
					$mData[$xDATA['teridxxx']]['teridxxx']  = ($mData[$xDATA['teridxxx']]['teridxxx'] <> "")?$mData[$xDATA['teridxxx']]['teridxxx']:$xDATA['teridxxx'];
					$mData[$xDATA['teridxxx']]['CLIAPE1X']  = ($mData[$xDATA['teridxxx']]['CLIAPE1X'] <> "")?$mData[$xDATA['teridxxx']]['CLIAPE1X']:$mDatCli[$xDATA['teridxxx']]['CLIAPE1X'];
					$mData[$xDATA['teridxxx']]['CLIAPE2X']  = ($mData[$xDATA['teridxxx']]['CLIAPE2X'] <> "")?$mData[$xDATA['teridxxx']]['CLIAPE2X']:$mDatCli[$xDATA['teridxxx']]['CLIAPE2X'];
					$mData[$xDATA['teridxxx']]['CLINOM1X']  = ($mData[$xDATA['teridxxx']]['CLINOM1X'] <> "")?$mData[$xDATA['teridxxx']]['CLINOM1X']:$mDatCli[$xDATA['teridxxx']]['CLINOM1X'];
					$mData[$xDATA['teridxxx']]['CLINOM2X']  = ($mData[$xDATA['teridxxx']]['CLINOM2X'] <> "")?$mData[$xDATA['teridxxx']]['CLINOM2X']:$mDatCli[$xDATA['teridxxx']]['CLINOM2X'];
					$mData[$xDATA['teridxxx']]['CLINOMXX']  = ($mData[$xDATA['teridxxx']]['CLINOMXX'] <> "")?$mData[$xDATA['teridxxx']]['CLINOMXX']:$mDatCli[$xDATA['teridxxx']]['CLINOMXX'];
					$mData[$xDATA['teridxxx']]['CLIDIRXX']  = ($mData[$xDATA['teridxxx']]['CLIDIRXX'] <> "")?$mData[$xDATA['teridxxx']]['CLIDIRXX']:$mDatCli[$xDATA['teridxxx']]['CLIDIRXX'];
					$mData[$xDATA['teridxxx']]['DEPIDXXX']  = ($mData[$xDATA['teridxxx']]['DEPIDXXX'] <> "")?$mData[$xDATA['teridxxx']]['DEPIDXXX']:$mDatCli[$xDATA['teridxxx']]['DEPIDXXX'];
					$mData[$xDATA['teridxxx']]['CIUIDXXX']  = ($mData[$xDATA['teridxxx']]['CIUIDXXX'] <> "")?$mData[$xDATA['teridxxx']]['CIUIDXXX']:$mDatCli[$xDATA['teridxxx']]['CIUIDXXX'];
					$mData[$xDATA['teridxxx']]['TDIIDXXC']  = ($mData[$xDATA['teridxxx']]['TDIIDXXC'] <> "")?$mData[$xDATA['teridxxx']]['TDIIDXXC']:$mDatCli[$xDATA['teridxxx']]['TDIIDXXX'];
					$mData[$xDATA['teridxxx']]['CLIIDXXC']  = ($mData[$xDATA['teridxxx']]['CLIIDXXC'] <> "")?$mData[$xDATA['teridxxx']]['CLIIDXXC']:$xDATA['teridxxx'];
					$mData[$xDATA['teridxxx']]['CLIAPE1C']  = ($mData[$xDATA['teridxxx']]['CLIAPE1C'] <> "")?$mData[$xDATA['teridxxx']]['CLIAPE1C']:$mDatCli[$xDATA['teridxxx']]['CLIAPE1X'];
					$mData[$xDATA['teridxxx']]['CLIAPE2C']  = ($mData[$xDATA['teridxxx']]['CLIAPE2C'] <> "")?$mData[$xDATA['teridxxx']]['CLIAPE2C']:$mDatCli[$xDATA['teridxxx']]['CLIAPE2X'];
					$mData[$xDATA['teridxxx']]['CLINOM1C']  = ($mData[$xDATA['teridxxx']]['CLINOM1C'] <> "")?$mData[$xDATA['teridxxx']]['CLINOM1C']:$mDatCli[$xDATA['teridxxx']]['CLINOM1X'];
					$mData[$xDATA['teridxxx']]['CLINOM2C']  = ($mData[$xDATA['teridxxx']]['CLINOM2C'] <> "")?$mData[$xDATA['teridxxx']]['CLINOM2C']:$mDatCli[$xDATA['teridxxx']]['CLINOM2X'];
					$mData[$xDATA['teridxxx']]['CLINOMXC']  = ($mData[$xDATA['teridxxx']]['CLINOMXC'] <> "")?$mData[$xDATA['teridxxx']]['CLINOMXC']:$mDatCli[$xDATA['teridxxx']]['CLINOMXX'];
					$mData[$xDATA['teridxxx']]['COMVRL01'] += $xDATA['comvlr01'];
					$mData[$xDATA['teridxxx']]['COMVLRXX'] += $xDATA['comvlrxx'];
				}
			break;
			case "1006": // 2408050100

				/**
				 * Ticket 8823-Informacion Exogena
				 * Johana Arboleda Ramos 2014-04-05  09:50
				 * se debe incluir la columna IVA recuperado por operaciones en devoluciones en compras anuladas,
				 * rescindidas o resueltas (con valor cero) y la columna Impuesto al consumo (con valor cero).
				 */

				$qData  = "SELECT ";
				$qData .= "$cAlfa.fcod$nAno.teridxxx,";
				$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx = \"D\",$cAlfa.fcod$nAno.comvlrxx,$cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx ";
				$qData .= "FROM $cAlfa.fcod$nAno ";
				$qData .= "WHERE ";
				$qData .= "$cAlfa.fcod$nAno.pucidxxx IN ($cCuenta) AND ";
				$qData .= "$cAlfa.fcod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND ";
				$qData .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
				$qData .= "GROUP BY $cAlfa.fcod$nAno.teridxxx ";
				$qData .= "ORDER BY $cAlfa.fcod$nAno.teridxxx";
				$xData  = f_MySql("SELECT","",$qData,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qData." ~ ".mysql_num_rows($xData));

				while ($xDATA = mysql_fetch_array($xData)) {
					#Trayendo datos teridxxx
					$qDatExt = "SELECT ";
					$qDatExt .= "$cAlfa.SIAI0150.TDIIDXXX,";
					$qDatExt .= "$cAlfa.SIAI0150.CLIAPE1X,";
					$qDatExt .= "$cAlfa.SIAI0150.CLIAPE2X,";
					$qDatExt .= "$cAlfa.SIAI0150.CLINOM1X,";
					$qDatExt .= "$cAlfa.SIAI0150.CLINOM2X,";
					$qDatExt .= "$cAlfa.SIAI0150.CLINOMXX ";
					$qDatExt .= "FROM $cAlfa.SIAI0150 ";
					$qDatExt .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$xDATA['teridxxx']}\" LIMIT 0,1 ";
					$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
					if(mysql_num_rows($xDatExt) > 0) {
						$xRDE = mysql_fetch_array($xDatExt);
						$xDATA['TDIIDXXX'] = $xRDE['TDIIDXXX'];
						$xDATA['CLIAPE1X'] = utf8_encode($xRDE['CLIAPE1X']);
						$xDATA['CLIAPE2X'] = utf8_encode($xRDE['CLIAPE2X']);
						$xDATA['CLINOM1X'] = utf8_encode($xRDE['CLINOM1X']);
						$xDATA['CLINOM2X'] = utf8_encode($xRDE['CLINOM2X']);
						$xDATA['CLINOMXX'] = ($xRDE['TDIIDXXX'] == 31) ? utf8_encode($xRDE['CLINOMXX']) : "";
					}
					$nInd_Data = count($mData);
					$mData[$nInd_Data] = $xDATA;
				}

			break;
			case "1007": // 4145950100

				/**
				 * Ticket 8823-Informacion Exogena
				 * Johana Arboleda Ramos 2014-04-05  10:06
				 * se debe incluir la columna Concepto (vacia).
				 */

				#Creando tabla temporal de cuentas 4
				$cFcoc = "fcod".$nAno;
				$cTabFac = fnCadenaAleatoria();
				$qNewTab  = "CREATE TEMPORARY TABLE IF NOT EXISTS $cAlfa.$cTabFac LIKE $cAlfa.$cFcoc";
				$xNewTab = mysql_query($qNewTab,$xConexion01);

				$qFcod  = "SELECT * ";
				$qFcod .= "FROM $cAlfa.fcod$nAno ";
				$qFcod .= "WHERE ";
				$qFcod .= "$cAlfa.fcod$nAno.pucidxxx LIKE \"4%\" AND ";
				$qFcod .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";

				$qInsert = "INSERT INTO $cAlfa.$cTabFac $qFcod";
				$xInsert = mysql_query($qInsert,$xConexion01);
				#Fin Creando tabla temporal de facturas cabecera

				$qData  = "SELECT ";
				$qData .= "$cAlfa.fcod$nAno.comidxxx,";
				$qData .= "$cAlfa.fcod$nAno.comcodxx,";
				$qData .= "$cAlfa.fcod$nAno.comcscxx,";
				$qData .= "$cAlfa.fcod$nAno.pucidxxx,";
				$qData .= "$cAlfa.fcod$nAno.ctoidxxx,";
				$qData .= "$cAlfa.fcod$nAno.teridxxx,";
				$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx = \"D\",$cAlfa.fcod$nAno.comvlrxx,$cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx ";
				$qData .= "FROM $cAlfa.fcod$nAno ";
				$qData .= "WHERE ";
				$qData .= "$cAlfa.fcod$nAno.pucidxxx IN ($cCuenta) AND ";
				$qData .= "$cAlfa.fcod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND ";
				$qData .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
				$qData .= "GROUP BY $cAlfa.fcod$nAno.teridxxx, $cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
				$qData .= "ORDER BY $cAlfa.fcod$nAno.teridxxx";
				$xData  = f_MySql("SELECT","",$qData,$xConexion01,"");
				//ff_Mensaje(__FILE__,__LINE__,$qData." ~ ".mysql_num_rows($xData));

				$mLisCli = array(); $mDatCli = array();
				while ($xDATA = mysql_fetch_array($xData)) {
					if(in_array($xDATA['teridxxx'],$mLisCli) == false) {
						#Trayendo datos teridxxx
						$qDatExt  = "SELECT * ";
						$qDatExt .= "FROM $cAlfa.SIAI0150 ";
						$qDatExt .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$xDATA['teridxxx']}\" LIMIT 0,1 ";
						$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");

						if(mysql_num_rows($xDatExt) > 0) {
							$xRDE = mysql_fetch_array($xDatExt);
							if ($xRDE['CLIRECOM']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "COMUN";
							}
							if ($xRDE['CLIRESIM']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "SIMPLIFICADO";
							}
							if ($xRDE['CLIGCXXX']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "CONTRIBUYENTE";
							}
							if ($xRDE['CLINRPXX']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "NORESIDENTE";
							}
							//Busco el codigo del pais
							$qCodPai = "SELECT PAIIDNXX FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"{$xRDE['PAIIDXXX']}\" LIMIT 0,1";
							$xCodPai  = f_MySql("SELECT","",$qCodPai,$xConexion01,"");
							if (mysql_num_rows($xCodPai) > 0) {
								$xRCP = mysql_fetch_array($xCodPai);
								$xRDE['PAIIDXXX'] =  ($xRCP['PAIIDNXX'] != "") ? $xRCP['PAIIDNXX'] : $xRDE['PAIIDXXX'];
							}
							//Fin Busco el codigo del pais
							$mDatCli[$xDATA['teridxxx']]['TDIIDXXX'] = $xRDE['TDIIDXXX'];
							$mDatCli[$xDATA['teridxxx']]['CLIAPE1X'] = utf8_encode($xRDE['CLIAPE1X']);
							$mDatCli[$xDATA['teridxxx']]['CLIAPE2X'] = utf8_encode($xRDE['CLIAPE2X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOM1X'] = utf8_encode($xRDE['CLINOM1X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOM2X'] = utf8_encode($xRDE['CLINOM2X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOMXX'] = ($xRDE['TDIIDXXX'] == 31) ? utf8_encode($xRDE['CLINOMXX']) : "";
							$mDatCli[$xDATA['teridxxx']]['CLIDIRXX'] = utf8_encode($xRDE['CLIDIRXX']);
							$mDatCli[$xDATA['teridxxx']]['DEPIDXXX'] = $xRDE['DEPIDXXX'];
							$mDatCli[$xDATA['teridxxx']]['CIUIDXXX'] = $xRDE['CIUIDXXX'];
							$mDatCli[$xDATA['teridxxx']]['PAIIDXXX'] = $xRDE['PAIIDXXX'];

							$mLisCli[] = $xDATA['teridxxx'];
						}
					}

					$nIpro = 0; $nIcon = 0; $nIfid = 0;
					#Calculando valores:
					#Iva mayor del costo o gasto deducible
					#Retencion en la fuente practicada renta
					#Retencion en la fuente asumida renta
					#Retencion en la fuente practicada iva regimen comun
					#Retencion en la fuente asumida iva regimen simp.
					#Retencion en la fuente practicada iva no domiciliados
					#Traigo las cuantas que empiezan por 2408,2365,531520,2367 para le comprobante
					$qFcod  = "SELECT ";
					$qFcod .= "$cAlfa.$cTabFac.pucidxxx, ";
					$qFcod .= "$cAlfa.$cTabFac.tertipxx,";
					$qFcod .= "$cAlfa.$cTabFac.teridxxx, ";
					$qFcod .= "$cAlfa.$cTabFac.tertip2x,";
					$qFcod .= "$cAlfa.$cTabFac.terid2xx, ";
					$qFcod .= "$cAlfa.$cTabFac.comvlrxx ";
					$qFcod .= "FROM $cAlfa.$cTabFac ";
					$qFcod .= "WHERE ";
					$qFcod .= "$cAlfa.$cTabFac.comidxxx = \"{$xDATA['comidxxx']}\" AND ";
					$qFcod .= "$cAlfa.$cTabFac.comcodxx = \"{$xDATA['comcodxx']}\" AND ";
					$qFcod .= "$cAlfa.$cTabFac.comcscxx = \"{$xDATA['comcscxx']}\" AND ";
					$qFcod .= "$cAlfa.$cTabFac.teridxxx = \"{$xDATA['teridxxx']}\" AND ";
					$qFcod .= "$cAlfa.$cTabFac.pucidxxx LIKE \"4%\" AND ";
					$qFcod .= "$cAlfa.$cTabFac.regestxx = \"ACTIVO\" ";
					$xFcod = mysql_query($qFcod,$xConexion01);

					if (mysql_num_rows($xFcod) > 0) {
						while($xRF = mysql_fetch_array($xFcod)) {
							if(substr($xRF['pucidxxx'],0,4) == '4175' || substr($xRF['pucidxxx'],0,4) == '4275') {
								$nIcon += $xRF['comvlrxx'];
							}else {
								$nIpro += $xRF['comvlrxx'];
							}
							if(substr($xRF['pucidxxx'],0,6) == '424045') {
								$nIfid += $xRF['comvlrxx'];
							}
						}
					}

					$mData[$xDATA['teridxxx']]['TDIIDXXX']  = ($mData[$xDATA['teridxxx']]['TDIIDXXX'] <> "")?$mData[$xDATA['teridxxx']]['TDIIDXXX']:$mDatCli[$xDATA['teridxxx']]['TDIIDXXX'];
					$mData[$xDATA['teridxxx']]['teridxxx']  = ($mData[$xDATA['teridxxx']]['teridxxx'] <> "")?$mData[$xDATA['teridxxx']]['teridxxx']:$xDATA['teridxxx'];
					$mData[$xDATA['teridxxx']]['CLIAPE1X']  = ($mData[$xDATA['teridxxx']]['CLIAPE1X'] <> "")?$mData[$xDATA['teridxxx']]['CLIAPE1X']:$mDatCli[$xDATA['teridxxx']]['CLIAPE1X'];
					$mData[$xDATA['teridxxx']]['CLIAPE2X']  = ($mData[$xDATA['teridxxx']]['CLIAPE2X'] <> "")?$mData[$xDATA['teridxxx']]['CLIAPE2X']:$mDatCli[$xDATA['teridxxx']]['CLIAPE2X'];
					$mData[$xDATA['teridxxx']]['CLINOM1X']  = ($mData[$xDATA['teridxxx']]['CLINOM1X'] <> "")?$mData[$xDATA['teridxxx']]['CLINOM1X']:$mDatCli[$xDATA['teridxxx']]['CLINOM1X'];
					$mData[$xDATA['teridxxx']]['CLINOM2X']  = ($mData[$xDATA['teridxxx']]['CLINOM2X'] <> "")?$mData[$xDATA['teridxxx']]['CLINOM2X']:$mDatCli[$xDATA['teridxxx']]['CLINOM2X'];
					$mData[$xDATA['teridxxx']]['CLINOMXX']  = ($mData[$xDATA['teridxxx']]['CLINOMXX'] <> "")?$mData[$xDATA['teridxxx']]['CLINOMXX']:$mDatCli[$xDATA['teridxxx']]['CLINOMXX'];
					$mData[$xDATA['teridxxx']]['CLIDIRXX']  = ($mData[$xDATA['teridxxx']]['CLIDIRXX'] <> "")?$mData[$xDATA['teridxxx']]['CLIDIRXX']:$mDatCli[$xDATA['teridxxx']]['CLIDIRXX'];
					$mData[$xDATA['teridxxx']]['DEPIDXXX']  = ($mData[$xDATA['teridxxx']]['DEPIDXXX'] <> "")?$mData[$xDATA['teridxxx']]['DEPIDXXX']:$mDatCli[$xDATA['teridxxx']]['DEPIDXXX'];
					$mData[$xDATA['teridxxx']]['CIUIDXXX']  = ($mData[$xDATA['teridxxx']]['CIUIDXXX'] <> "")?$mData[$xDATA['teridxxx']]['CIUIDXXX']:$mDatCli[$xDATA['teridxxx']]['CIUIDXXX'];
					$mData[$xDATA['teridxxx']]['PAIIDXXX']  = ($mData[$xDATA['teridxxx']]['PAIIDXXX'] <> "")?$mData[$xDATA['teridxxx']]['PAIIDXXX']:$mDatCli[$xDATA['teridxxx']]['PAIIDXXX'];
					$mData[$xDATA['teridxxx']]['TDIIDXXC']  = ($mData[$xDATA['teridxxx']]['TDIIDXXC'] <> "")?$mData[$xDATA['teridxxx']]['TDIIDXXC']:$mDatCli[$xDATA['teridxxx']]['TDIIDXXX'];
					$mData[$xDATA['teridxxx']]['CLIIDXXC']  = ($mData[$xDATA['teridxxx']]['CLIIDXXC'] <> "")?$mData[$xDATA['teridxxx']]['CLIIDXXC']:$xDATA['teridxxx'];
					$mData[$xDATA['teridxxx']]['CLIAPE1C']  = ($mData[$xDATA['teridxxx']]['CLIAPE1C'] <> "")?$mData[$xDATA['teridxxx']]['CLIAPE1C']:$mDatCli[$xDATA['teridxxx']]['CLIAPE1X'];
					$mData[$xDATA['teridxxx']]['CLIAPE2C']  = ($mData[$xDATA['teridxxx']]['CLIAPE2C'] <> "")?$mData[$xDATA['teridxxx']]['CLIAPE2C']:$mDatCli[$xDATA['teridxxx']]['CLIAPE2X'];
					$mData[$xDATA['teridxxx']]['CLINOM1C']  = ($mData[$xDATA['teridxxx']]['CLINOM1C'] <> "")?$mData[$xDATA['teridxxx']]['CLINOM1C']:$mDatCli[$xDATA['teridxxx']]['CLINOM1X'];
					$mData[$xDATA['teridxxx']]['CLINOM2C']  = ($mData[$xDATA['teridxxx']]['CLINOM2C'] <> "")?$mData[$xDATA['teridxxx']]['CLINOM2C']:$mDatCli[$xDATA['teridxxx']]['CLINOM2X'];
					$mData[$xDATA['teridxxx']]['CLINOMXC']  = ($mData[$xDATA['teridxxx']]['CLINOMXC'] <> "")?$mData[$xDATA['teridxxx']]['CLINOMXC']:$mDatCli[$xDATA['teridxxx']]['CLINOMXX'];

					$mData[$xDATA['teridxxx']]['IPROXXXX'] += $nIpro;
					$mData[$xDATA['teridxxx']]['ICONXXXX'] += $nIcon;
					$mData[$xDATA['teridxxx']]['IMANXXXX']  = 0;
					$mData[$xDATA['teridxxx']]['IEXPXXXX']  = 0;
					$mData[$xDATA['teridxxx']]['IFIDXXXX'] += $nIfid;
					$mData[$xDATA['teridxxx']]['ITERXXXX']  = 0;
					$mData[$xDATA['teridxxx']]['DEVXXXXX']  = 0;
				}
			break;
			case "1008":

				/**
					* Ticket 8823-Informacion Exogena
					* Johana Arboleda Ramos 2014-04-07  07:50
					* Reporte 1008-Saldo de cuentas por cobrar al 31 de Diciembre- V7
					*/

				$AnoIni = (($nAno-3) <= $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : ($nAno-3);

				##Creacion de la tabla detalle del dia
				$mAux = array();
				for ($nAnio=$AnoIni;$nAnio<=$nAno;$nAnio++) {

					$qDatMov  = "SELECT ";
					$qDatMov .= "$cAlfa.fcod$nAnio.comidcxx, ";
					$qDatMov .= "$cAlfa.fcod$nAnio.comcodcx, ";
					$qDatMov .= "$cAlfa.fcod$nAnio.comcsccx, ";
					$qDatMov .= "$cAlfa.fcod$nAnio.teridxxx, ";
					$qDatMov .= "$cAlfa.fcod$nAnio.pucidxxx, ";
					$qDatMov .= "$cAlfa.fcod$nAnio.comfecxx, ";
					$qDatMov .= "$cAlfa.fcod$nAnio.comfecve, ";
					$qDatMov .= "$cAlfa.fcod$nAnio.commovxx, ";
					$qDatMov .= "SUM(if ($cAlfa.fcod$nAnio.commovxx = \"D\", $cAlfa.fcod$nAnio.comvlrxx, $cAlfa.fcod$nAnio.comvlrxx*-1)) AS saldoxxx ";
					$qDatMov .= "FROM $cAlfa.fcod$nAnio, $cAlfa.fpar0115 ";
					$qDatMov .= "WHERE $cAlfa.fcod$nAnio.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
					$qDatMov .= "$cAlfa.fcod$nAnio.pucidxxx IN ($cCuenta) AND ";
					$qDatMov .= "$cAlfa.fcod$nAnio.comfecxx <= \"$dHasta\" AND ";
					$qDatMov .= "$cAlfa.fcod$nAnio.regestxx = \"ACTIVO\" AND ";
					$qDatMov .= "$cAlfa.fpar0115.pucdetxx = \"C\" ";
					$qDatMov .= "GROUP BY $cAlfa.fcod$nAnio.comidcxx,$cAlfa.fcod$nAnio.comcodcx,$cAlfa.fcod$nAnio.comcsccx,$cAlfa.fcod$nAnio.teridxxx,$cAlfa.fcod$nAnio.pucidxxx ";
					$qDatMov .= "ORDER BY $cAlfa.fcod$nAnio.teridxxx";
					$xDatMov = mysql_query($qDatMov,$xConexion01);
					// f_Mensaje(__FILE__,__LINE__,$qDatMov."~".mysql_num_rows($xDatMov));

					while ($xDATA = mysql_fetch_array($xDatMov)) {
						if ($xDATA['saldoxxx'] != 0) {
							$mAux[$xDATA['teridxxx']]['teridxxx']  = $xDATA['teridxxx'];
							$mAux[$xDATA['teridxxx']]['COMVLRXX'] += $xDATA['saldoxxx'];
						}
					}
				}

				foreach ($mAux as $cKey => $cValue) {
					if ($mAux[$cKey]['COMVLRXX'] != 0) {

						# Traigo el Nombre del Cliente
						$qNomCli  = "SELECT * ";
						$qNomCli .= "FROM $cAlfa.SIAI0150 ";
						$qNomCli .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$mAux[$cKey]['teridxxx']}\" LIMIT 0,1";
						$xNomCli = f_MySql("SELECT","",$qNomCli,$xConexion01,"");
						$vNomCli = mysql_fetch_array($xNomCli);
						# Fin Traigo el Nombre del Cliente

						//Busco el codigo del pais
						$qCodPai = "SELECT PAIIDNXX FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"{$vNomCli['PAIIDXXX']}\" LIMIT 0,1";
						$xCodPai  = f_MySql("SELECT","",$qCodPai,$xConexion01,"");
						if (mysql_num_rows($xCodPai) > 0) {
							$xRCP = mysql_fetch_array($xCodPai);
							$vNomCli['PAIIDXXX'] =  ($xRCP['PAIIDNXX'] != "") ? $xRCP['PAIIDNXX'] : $vNomCli['PAIIDXXX'];
						}
						//Fin Busco el codigo del pais

						$mData[$cKey]['TDIIDXXX']  = ($mData[$cKey]['TDIIDXXX'] <> "")?$mData[$cKey]['TDIIDXXX']:$vNomCli['TDIIDXXX'];
						$mData[$cKey]['teridxxx']  = ($mData[$cKey]['teridxxx'] <> "")?$mData[$cKey]['teridxxx']:$mAux[$cKey]['teridxxx'];
						$mData[$cKey]['CLIAPE1X']  = ($mData[$cKey]['CLIAPE1X'] <> "")?$mData[$cKey]['CLIAPE1X']:$vNomCli['CLIAPE1X'];
						$mData[$cKey]['CLIAPE2X']  = ($mData[$cKey]['CLIAPE2X'] <> "")?$mData[$cKey]['CLIAPE2X']:$vNomCli['CLIAPE2X'];
						$mData[$cKey]['CLINOM1X']  = ($mData[$cKey]['CLINOM1X'] <> "")?$mData[$cKey]['CLINOM1X']:$vNomCli['CLINOM1X'];
						$mData[$cKey]['CLINOM1X']  = ($mData[$cKey]['CLINOM1X'] <> "")?$mData[$cKey]['CLINOM1X']:$vNomCli['CLINOM1X'];
						$mData[$cKey]['CLINOMXX']  = ($mData[$cKey]['CLINOMXX'] <> "")?$mData[$cKey]['CLINOMXX']:(($vNomCli['TDIIDXXX'] == 31) ? $vNomCli['CLINOMXX'] : "");
						$mData[$cKey]['CLIDIRXX']  = ($mData[$cKey]['CLIDIRXX'] <> "")?$mData[$cKey]['CLIDIRXX']:$vNomCli['CLIDIRXX'];
						$mData[$cKey]['DEPIDXXX']  = ($mData[$cKey]['DEPIDXXX'] <> "")?$mData[$cKey]['DEPIDXXX']:$vNomCli['DEPIDXXX'];
						$mData[$cKey]['CIUIDXXX']  = ($mData[$cKey]['CIUIDXXX'] <> "")?$mData[$cKey]['CIUIDXXX']:$vNomCli['CIUIDXXX'];
						$mData[$cKey]['PAIIDXXX']  = ($mData[$cKey]['PAIIDXXX'] <> "")?$mData[$cKey]['PAIIDXXX']:$vNomCli['PAIIDXXX'];
						$mData[$cKey]['COMVLRXX']  = $mAux[$cKey]['COMVLRXX'];
					}
				}

			break;
			case "1009":

				/**
					* Ticket 8823-Informacion Exogena
					* Johana Arboleda Ramos 2014-04-07  07:50
					* Reporte 1009-Saldo de cuentas por pagar al 31 de Diciembre- V7
					*/

				$AnoIni = (($nAno-3) <= $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : ($nAno-3);

				##Creacion de la tabla detalle del dia
				$mAux = array();
				for ($nAnio=$AnoIni;$nAnio<=$nAno;$nAnio++) {

					$qDatMov  = "SELECT ";
					$qDatMov .= "$cAlfa.fcod$nAnio.comidcxx, ";
					$qDatMov .= "$cAlfa.fcod$nAnio.comcodcx, ";
					$qDatMov .= "$cAlfa.fcod$nAnio.comcsccx, ";
					$qDatMov .= "$cAlfa.fcod$nAnio.teridxxx, ";
					$qDatMov .= "$cAlfa.fcod$nAnio.pucidxxx, ";
					$qDatMov .= "$cAlfa.fcod$nAnio.comfecxx, ";
					$qDatMov .= "$cAlfa.fcod$nAnio.comfecve, ";
					$qDatMov .= "$cAlfa.fcod$nAnio.commovxx, ";
					$qDatMov .= "SUM(if ($cAlfa.fcod$nAnio.commovxx = \"D\", $cAlfa.fcod$nAnio.comvlrxx, $cAlfa.fcod$nAnio.comvlrxx*-1)) AS saldoxxx ";
					$qDatMov .= "FROM $cAlfa.fcod$nAnio, $cAlfa.fpar0115 ";
					$qDatMov .= "WHERE $cAlfa.fcod$nAnio.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
					$qDatMov .= "$cAlfa.fcod$nAnio.pucidxxx IN ($cCuenta) AND ";
					$qDatMov .= "$cAlfa.fcod$nAnio.comfecxx <= \"$dHasta\" AND ";
					$qDatMov .= "$cAlfa.fcod$nAnio.regestxx = \"ACTIVO\" AND ";
					$qDatMov .= "$cAlfa.fpar0115.pucdetxx = \"P\" ";
					$qDatMov .= "GROUP BY $cAlfa.fcod$nAnio.comidcxx,$cAlfa.fcod$nAnio.comcodcx,$cAlfa.fcod$nAnio.comcsccx,$cAlfa.fcod$nAnio.teridxxx,$cAlfa.fcod$nAnio.pucidxxx ";
					$qDatMov .= "ORDER BY $cAlfa.fcod$nAnio.teridxxx";
					$xDatMov = mysql_query($qDatMov,$xConexion01);
					// f_Mensaje(__FILE__,__LINE__,$qDatMov."~".mysql_num_rows($xDatMov));

					while ($xDATA = mysql_fetch_array($xDatMov)) {
						if ($xDATA['saldoxxx'] != 0) {
							$mAux[$xDATA['teridxxx']]['teridxxx']  = $xDATA['teridxxx'];
							$mAux[$xDATA['teridxxx']]['COMVLRXX'] += $xDATA['saldoxxx'];
						}
					}
				}

				foreach ($mAux as $cKey => $cValue) {
					if ($mAux[$cKey]['COMVLRXX'] != 0) {

						# Traigo el Nombre del Cliente
						$qNomCli  = "SELECT * ";
						$qNomCli .= "FROM $cAlfa.SIAI0150 ";
						$qNomCli .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$mAux[$cKey]['teridxxx']}\" LIMIT 0,1";
						$xNomCli = f_MySql("SELECT","",$qNomCli,$xConexion01,"");
						$vNomCli = mysql_fetch_array($xNomCli);
						# Fin Traigo el Nombre del Cliente

						//Busco el codigo del pais
						$qCodPai = "SELECT PAIIDNXX FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"{$vNomCli['PAIIDXXX']}\" LIMIT 0,1";
						$xCodPai  = f_MySql("SELECT","",$qCodPai,$xConexion01,"");
						if (mysql_num_rows($xCodPai) > 0) {
							$xRCP = mysql_fetch_array($xCodPai);
							$vNomCli['PAIIDXXX'] =  ($xRCP['PAIIDNXX'] != "") ? $xRCP['PAIIDNXX'] : $vNomCli['PAIIDXXX'];
						}
						//Fin Busco el codigo del pais

						$mData[$cKey]['TDIIDXXX']  = ($mData[$cKey]['TDIIDXXX'] <> "")?$mData[$cKey]['TDIIDXXX']:$vNomCli['TDIIDXXX'];
						$mData[$cKey]['teridxxx']  = ($mData[$cKey]['teridxxx'] <> "")?$mData[$cKey]['teridxxx']:$mAux[$cKey]['teridxxx'];
						$mData[$cKey]['CLIAPE1X']  = ($mData[$cKey]['CLIAPE1X'] <> "")?$mData[$cKey]['CLIAPE1X']:$vNomCli['CLIAPE1X'];
						$mData[$cKey]['CLIAPE2X']  = ($mData[$cKey]['CLIAPE2X'] <> "")?$mData[$cKey]['CLIAPE2X']:$vNomCli['CLIAPE2X'];
						$mData[$cKey]['CLINOM1X']  = ($mData[$cKey]['CLINOM1X'] <> "")?$mData[$cKey]['CLINOM1X']:$vNomCli['CLINOM1X'];
						$mData[$cKey]['CLINOM2X']  = ($mData[$cKey]['CLINOM2X'] <> "")?$mData[$cKey]['CLINOM2X']:$vNomCli['CLINOM2X'];
						$mData[$cKey]['CLINOMXX']  = ($mData[$cKey]['CLINOMXX'] <> "")?$mData[$cKey]['CLINOMXX']:(($vNomCli['TDIIDXXX'] == 31) ? $vNomCli['CLINOMXX'] : "");
						$mData[$cKey]['CLIDIRXX']  = ($mData[$cKey]['CLIDIRXX'] <> "")?$mData[$cKey]['CLIDIRXX']:$vNomCli['CLIDIRXX'];
						$mData[$cKey]['DEPIDXXX']  = ($mData[$cKey]['DEPIDXXX'] <> "")?$mData[$cKey]['DEPIDXXX']:$vNomCli['DEPIDXXX'];
						$mData[$cKey]['CIUIDXXX']  = ($mData[$cKey]['CIUIDXXX'] <> "")?$mData[$cKey]['CIUIDXXX']:$vNomCli['CIUIDXXX'];
						$mData[$cKey]['PAIIDXXX']  = ($mData[$cKey]['PAIIDXXX'] <> "")?$mData[$cKey]['PAIIDXXX']:$vNomCli['PAIIDXXX'];
						$mData[$cKey]['COMVLRXX']  = $mAux[$cKey]['COMVLRXX'];
					}
				}
			break;
			case "1012":

				/**
					* Ticket 8823-Informacion Exogena
					* Johana Arboleda Ramos 2014-04-08  08:10
					* Reporte 1012- Informacion de declaraciones tributarias, acciones, inversiones en bonos titulos valores y cuentas de ahorro y cuentas corrientes  V7
					*/

				$AnoIni = (($nAno-3) <= $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : ($nAno-3);

				##Creacion de la tabla detalle del dia
				$mAux = array();
				for ($nAnio=$AnoIni;$nAnio<=$nAno;$nAnio++) {

					$qDatMov  = "SELECT ";
					$qDatMov .= "$cAlfa.fcod$nAnio.pucidxxx, ";
					$qDatMov .= "SUM(IF($cAlfa.fcod$nAnio.commovxx = \"D\", $cAlfa.fcod$nAnio.comvlrxx, $cAlfa.fcod$nAnio.comvlrxx*-1)) AS saldoxxx ";
					$qDatMov .= "FROM $cAlfa.fcod$nAnio, $cAlfa.fpar0115 ";
					$qDatMov .= "WHERE $cAlfa.fcod$nAnio.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
					$qDatMov .= "$cAlfa.fcod$nAnio.pucidxxx IN ($cCuenta) AND ";
					$qDatMov .= "$cAlfa.fcod$nAnio.comfecxx <= \"$dHasta\" AND ";
					$qDatMov .= "$cAlfa.fcod$nAnio.regestxx = \"ACTIVO\" ";
					$qDatMov .= "GROUP BY $cAlfa.fcod$nAnio.pucidxxx ";
					$qDatMov .= "ORDER BY $cAlfa.fcod$nAnio.pucidxxx";
					$xDatMov = mysql_query($qDatMov,$xConexion01);
					//f_Mensaje(__FILE__,__LINE__,$qDatMov."~".mysql_num_rows($xDatMov));

					while ($xDATA = mysql_fetch_array($xDatMov)) {
						if ($xDATA['saldoxxx'] != 0) {
							$mAux[$xDATA['pucidxxx']]['pucidxxx']  = $xDATA['pucidxxx'];
							$mAux[$xDATA['pucidxxx']]['saldoxxx'] += $xDATA['saldoxxx'];
						}
					}
				}

				foreach ($mAux as $cKey => $cValue) {
					if ($mAux[$cKey]['saldoxxx'] != 0) {
						# Traigo Informacion del Banco
						$qBanco  = "SELECT $cAlfa.fpar0124.bandesxx ";
						$qBanco .= "FROM $cAlfa.fpar0128,$cAlfa.fpar0124 ";
						$qBanco .= "WHERE ";
						$qBanco .= "$cAlfa.fpar0128.banidxxx = $cAlfa.fpar0124.banidxxx AND ";
						$qBanco .= "$cAlfa.fpar0128.pucidxxx = \"{$mAux[$cKey]['pucidxxx']}\" AND ";
						$qBanco .= "$cAlfa.fpar0128.regestxx = \"ACTIVO\" LIMIT 0,1";
						$xBanco  = f_MySql("SELECT","",$qBanco,$xConexion01,"");
						//f_Mensaje(__FILE__,__LINE__,$qBanco." ~ ".mysql_num_rows($xBanco));
						$vBanco  = mysql_fetch_array($xBanco);
						# Traigo Informacion del Banco

						#Trayendo el Nit del Banco
						//Se debe buscar la descripcion del banco dentro de los terceros y traer el primero que coincida
						$qNomCli  = "SELECT * ";
						$qNomCli .= "FROM $cAlfa.SIAI0150 ";
						$qNomCli .= "WHERE CLINOMXX LIKE \"%{$vBanco['bandesxx']}%\" LIMIT 0,1";
						$xNomCli = f_MySql("SELECT","",$qNomCli,$xConexion01,"");
						$vNomCli = mysql_fetch_array($xNomCli);
						#Fin Trayendo el Nit del Banco

						//Busco el codigo del pais
						$qCodPai = "SELECT PAIIDNXX FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"{$vNomCli['PAIIDXXX']}\" LIMIT 0,1";
						$xCodPai  = f_MySql("SELECT","",$qCodPai,$xConexion01,"");
						if (mysql_num_rows($xCodPai) > 0) {
							$xRCP = mysql_fetch_array($xCodPai);
							$vNomCli['PAIIDXXX'] =  ($xRCP['PAIIDNXX'] != "") ? $xRCP['PAIIDNXX'] : $vNomCli['PAIIDXXX'];
						}
						//Fin Busco el codigo del pais

						$mData[$cKey]['TDIIDXXX']  = ($mData[$cKey]['TDIIDXXX'] <> "")?$mData[$cKey]['TDIIDXXX']:$vNomCli['TDIIDXXX'];
						$mData[$cKey]['teridxxx']  = ($mData[$cKey]['teridxxx'] <> "")?$mData[$cKey]['teridxxx']:$vNomCli['CLIIDXXX'];
						$mData[$cKey]['pucidxxx']  = ($mData[$cKey]['pucidxxx'] <> "")?$mData[$cKey]['pucidxxx']:$mAux[$cKey]['pucidxxx'];
						$mData[$cKey]['CLIAPE1X']  = ($mData[$cKey]['CLIAPE1X'] <> "")?$mData[$cKey]['CLIAPE1X']:$vNomCli['CLIAPE1X'];
						$mData[$cKey]['CLIAPE2X']  = ($mData[$cKey]['CLIAPE2X'] <> "")?$mData[$cKey]['CLIAPE2X']:$vNomCli['CLIAPE2X'];
						$mData[$cKey]['CLINOM1X']  = ($mData[$cKey]['CLINOM1X'] <> "")?$mData[$cKey]['CLINOM1X']:$vNomCli['CLINOM1X'];
						$mData[$cKey]['CLINOM2X']  = ($mData[$cKey]['CLINOM2X'] <> "")?$mData[$cKey]['CLINOM2X']:$vNomCli['CLINOM2X'];
						$mData[$cKey]['CLINOMXX']  = ($mData[$cKey]['CLINOMXX'] <> "")?$mData[$cKey]['CLINOMXX']:(($vNomCli['TDIIDXXX'] == 31) ? $vNomCli['CLINOMXX'] : "");
						$mData[$cKey]['PAIIDXXX']  = ($mData[$cKey]['PAIIDXXX'] <> "")?$mData[$cKey]['PAIIDXXX']:$vNomCli['PAIIDXXX'];
						$mData[$cKey]['COMVLRXX']  = $mAux[$cKey]['saldoxxx'];
					}
				}
			break;
			case "1016": // 1380250100
				#Creando tabla temporal de cuentas 2408, 2365, 531520 y 2367
				$cFcoc = "fcod".$nAno;
				$cTabFac = fnCadenaAleatoria();
				$qNewTab  = "CREATE TEMPORARY TABLE IF NOT EXISTS $cAlfa.$cTabFac LIKE $cAlfa.$cFcoc";
				$xNewTab = mysql_query($qNewTab,$xConexion01);

				//Buscando las cuentas de retencion CREE
				$qRetCree  = "SELECT CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx ";
				$qRetCree .= "FROM $cAlfa.fpar0115 ";
				$qRetCree .= "WHERE ";
				$qRetCree .= "pucgruxx LIKE \"23\" AND ";
				$qRetCree .= "pucterxx LIKE \"R\"  AND ";
				$qRetCree .= "pucdesxx LIKE \"%CREE%\" AND ";
				$qRetCree .= "pucdesxx NOT LIKE \"%AUTO%\" AND ";
				$qRetCree .= "regestxx = \"ACTIVO\" ";
				$xRetCree  = f_MySql("SELECT","",$qRetCree,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qRetCree."~".mysql_num_rows($xRetCree));
				$mRetCree = array();
				while ($xRRC = mysql_fetch_array($xRetCree)){
					$mRetCree[count($mRetCree)] = $xRRC['pucidxxx'];
				}

				$cReteCree = "";
				for($nRC=0; $nRC<count($mRetCree); $nRC++) {
					$cReteCree .= "$cAlfa.fcod$nAno.pucidxxx LIKE \"{$mRetCree[$nRC]}\" OR ";
				}
				$cReteCree = substr($cReteCree, 0, strlen($cReteCree)-4);

				$qFcod  = "SELECT * ";
				$qFcod .= "FROM $cAlfa.fcod$nAno ";
				$qFcod .= "WHERE ";
				$qFcod .= "($cAlfa.fcod$nAno.pucidxxx LIKE \"2408%\" OR ";
				$qFcod .= "SUBSTRING($cAlfa.fcod$nAno.pucidxxx,1,4) IN ($cCueRetFue) OR ";
				$qFcod .= "SUBSTRING($cAlfa.fcod$nAno.pucidxxx,1,6) IN ($cCueImpAsu) OR ";
				$qFcod .= "SUBSTRING($cAlfa.fcod$nAno.pucidxxx,1,4) IN ($cCueRetIva)".(($cReteCree != "") ? " OR $cReteCree": "").") AND ";
				$qFcod .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";

				$qInsert = "INSERT INTO $cAlfa.$cTabFac $qFcod";
				$xInsert = mysql_query($qInsert,$xConexion01);
				#Fin Creando tabla temporal de facturas cabecera

				#Buscando el nombre de la DIRECCION DE IMPUESTOS DE ADUANAS
				$qDatExt = "SELECT ";
				$qDatExt .= "$cAlfa.SIAI0150.CLINOMXX AS CLINOMDI ";
				$qDatExt .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"800197268\" LIMIT 0,1 ";
				$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
				if(mysql_num_rows($xDatExt) > 0) {
					$xRDE = mysql_fetch_array($xDatExt);
					$cNomAdu =  $xRDE['CLINOMDI'];
				}

				#Busco si el concepto contable es de anticipo
				$qCtoAnt  = "SELECT ";
				$qCtoAnt .= "ctoidxxx ";
				$qCtoAnt .= "FROM $cAlfa.fpar0119 ";
				$qCtoAnt .= "WHERE  ";
				$qCtoAnt .= "ctoantxx = \"SI\" AND ";
				$qCtoAnt .= "regestxx = \"ACTIVO\" ";
				$xCtoAnt  = f_MySql("SELECT","",$qCtoAnt,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qCtoAnt." ~ ".mysql_num_rows($xCtoAnt));
				$cCtoAnt = "";
				while ($xRPT = mysql_fetch_array($xCtoAnt)) {
					$cCtoAnt .= "{$xRPT['ctoidxxx']},";
				}
				$cCtoAnt = substr($cCtoAnt, 0, strlen($cCtoAnt)-1);

				if ($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") {
					#Busco si el concepto contable es de pago de tributo
					$qPagTri  = "SELECT ";
					$qPagTri .= "ctoidxxx ";
					$qPagTri .= "FROM $cAlfa.fpar0119 ";
					$qPagTri .= "WHERE  ";
					$qPagTri .= "(ctoptaxg = \"SI\" OR ctoptaxl = \"SI\") AND ";
					$qPagTri .= "regestxx = \"ACTIVO\" ";
					$xPagTri  = f_MySql("SELECT","",$qPagTri,$xConexion01,"");
					//f_Mensaje(__FILE__,__LINE__,$qPagTri." ~ ".mysql_num_rows($xPagTri));
					$cPagTri = "";
					while ($xRPT = mysql_fetch_array($xPagTri)) {
						$cPagTri .= "{$xRPT['ctoidxxx']},";
					}
					$cPagTri = substr($cPagTri, 0, strlen($cPagTri)-1);

					//Buscando las L que no son ajustes
					$qCarBa  = "SELECT ";
					$qCarBa .= "CONCAT(comidxxx,\"~\",comcodxx) AS comidxxx ";
					$qCarBa .= "FROM $cAlfa.fpar0117 ";
					$qCarBa .= "WHERE ";
					$qCarBa .= "comidxxx = \"L\" AND ";
					$qCarBa .= "comtipxx != \"AJUSTES\" ";
					$xCarBa = f_MySql("SELECT","",$qCarBa,$xConexion01,"");
					//f_Mensaje(__FILE__,__LINE__,$qCarBa." ~ ".mysql_num_rows($xCarBa));
					$vCarBa = "";
					while ($xRCB = mysql_fetch_array($xCarBa)) {
						$vCarBa[] = $xRCB['comidxxx'];
					}
				}

				$qFpar117  = "SELECT comidxxx, comcodxx ";
				$qFpar117 .= "FROM $cAlfa.fpar0117 ";
				$qFpar117 .= "WHERE ";
				$qFpar117 .= "comtipxx  = \"RCM\"";
				$xFpar117  = f_MySql("SELECT","",$qFpar117,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qFpar117." ~ ".mysql_num_rows($xFpar117));
				$mRCM = array();
				while ($xRF117 = mysql_fetch_array($xFpar117)) {
					$mRCM[count($mRCM)] = "{$xRF117['comidxxx']}~{$xRF117['comcodxx']}";
				}

				/**
					* Ticket 22351: Buscando los comprobantes de nota credito para COLMAS, estos comprobantes deben excluirse
					* Ajuste: 2016-03-10 10:14 se incluye cambio para excluir las notas credito para todas las agencias de aduana
					*/
				$cNotCre  = "";
				switch ($cAlfa) {
					case "COLMASXX":
					case "DECOLMASXX":
					case "TECOLMASXX":
						$cNotCre .= "\"L~044\",";
						$cNotCre .= "\"L~024\",";
						$cNotCre .= "\"L~020\",";
						$cNotCre .= "\"L~016\",";
						$cNotCre .= "\"C~001\",";
						$cNotCre .= "\"C~002\",";
						$cNotCre .= "\"C~003\",";
						$cNotCre .= "\"C~004\",";
					break;
					default:
						//No hace nada
					break;
				}
				$qNotCre  = "SELECT ";
				$qNotCre .= "CONCAT(comidxxx,\"~\",comcodxx) AS comidxxx ";
				$qNotCre .= "FROM $cAlfa.fpar0117 ";
				$qNotCre .= "WHERE ";
				$qNotCre .= "comidxxx = \"C\" AND ";
				$qNotCre .= "comtipxx != \"AJUSTES\" ";
				$xNotCre = f_MySql("SELECT","",$qNotCre,$xConexion01,"");
				while ($xRDB = mysql_fetch_array($xNotCre)) {
					$cNotCre .= "\"{$xRDB['comidxxx']}\",";
				}
				$cNotCre = substr($cNotCre,0,strlen($cNotCre)-1);

				#Buscando datos de detalle
				$qData  = "SELECT ";
				$qData .= "$cAlfa.fcod$nAno.comidxxx,";
				$qData .= "$cAlfa.fcod$nAno.comcodxx,";
				$qData .= "$cAlfa.fcod$nAno.comcscxx,";
				$qData .= "$cAlfa.fcod$nAno.pucidxxx,";
				$qData .= "$cAlfa.fcod$nAno.ctoidxxx,";
				$qData .= "$cAlfa.fcod$nAno.teridxxx,";
				$qData .= "$cAlfa.fcod$nAno.terid2xx,";
				//Comprobante cruce dos
				$qData .= "$cAlfa.fcod$nAno.comidc2x,";
				$qData .= "$cAlfa.fcod$nAno.comcodc2,";
				$qData .= "$cAlfa.fcod$nAno.comcscc2,";
				$qData .= "$cAlfa.fcod$nAno.comseqc2,";
				//$qData .= "SUM($cAlfa.fcod$nAno.comvlr02) AS comvlr02,";
				$qData .= "GROUP_CONCAT(CONCAT(comidc2x,\"-\",comcodc2,\"-\",comcscc2,\"-\",comseqc2) SEPARATOR \"~\") AS cajameno,";
				/**
					* Sumatoria valores
					* Para COLMAS, GLA y ADUACARGA se mantiene igual, ya que ellos tienen su propia logica,
					* para las demas agencias
					* si la base es cero y el iva es cero, la base debe ser igual al valor del comprobante
					*/
				switch ($cAlfa) {
					case "COLMASXX": case "DECOLMASXX": case "TECOLMASXX":
					case "GRUPOGLA": case "DEGRUPOGLA": case "TEGRUPOGLA":
					case "ADUACARX": case "DEADUACARX": case "TEADUACARX":
						$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlr02,$cAlfa.fcod$nAno.comvlr02*-1)) AS comvlr02,";
						$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlr01,$cAlfa.fcod$nAno.comvlr01*-1)) AS comvlr01,";
						$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlrxx,$cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx,";
					break;
					default:
						$nAsiBas = "IF($cAlfa.fcod$nAno.comvlr01 = 0 AND $cAlfa.fcod$nAno.comvlr02 = 0, $cAlfa.fcod$nAno.comvlrxx, $cAlfa.fcod$nAno.comvlr01)";
						$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlr02,$cAlfa.fcod$nAno.comvlr02*-1)) AS comvlr02,";
						$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$nAsiBas,$nAsiBas*-1)) AS comvlr01,";
						$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlrxx,$cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx,";
					break;
				}
				$qData .= "$cAlfa.fcod$nAno.teridxxx AS CLIIDXXC ";
				$qData .= "FROM $cAlfa.fcod$nAno ";
				$qData .= "LEFT JOIN $cAlfa.fcoc$nAno ON $cAlfa.fcod$nAno.comidxxx = $cAlfa.fcoc$nAno.comidxxx AND $cAlfa.fcod$nAno.comcodxx = $cAlfa.fcoc$nAno.comcodxx AND $cAlfa.fcod$nAno.comcscxx = $cAlfa.fcoc$nAno.comcscxx AND $cAlfa.fcod$nAno.comcsc2x = $cAlfa.fcoc$nAno.comcsc2x ";
				$qData .= "WHERE ";
				// $qData .= "$cAlfa.fcod$nAno.comidxxx = \"L\" AND ";
				// $qData .= "$cAlfa.fcod$nAno.comcodxx = \"038\" AND ";
				// $qData .= "(($cAlfa.fcod$nAno.comidxxx = \"L\" AND $cAlfa.fcod$nAno.comcodxx IN (\"016\", \"020\", \"024\", \"044\")) OR ($cAlfa.fcod$nAno.comidxxx = \"C\" AND $cAlfa.fcod$nAno.comcodxx IN (\"016\", \"017\", \"018\", \"019\"))) AND ";
				// $qData .= "$cAlfa.fcod$nAno.comcscxx = \"2015090192\" AND ";
				// $qData .= "$cAlfa.fcod$nAno.terid2xx = \"835001809\" AND ";
				$qData .= "$cAlfa.fcod$nAno.comidxxx != \"F\"          AND ";
				$qData .= "$cAlfa.fcoc$nAno.comintpa != \"SI\"         AND ";
				$qData .= "$cAlfa.fcod$nAno.pucidxxx IN ($cCuenta)     AND ";
				$qData .= "$cAlfa.fcod$nAno.ctoidxxx NOT IN ($cCtoAnt) AND ";
				if (($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") && $cPagTri <> "") {
					$qData .= "$cAlfa.fcod$nAno.ctoidxxx NOT IN ($cPagTri) AND ";
				}
				/**
					* Ticket 22351: Buscando los comprobantes de nota credito para COLMAS, estos comprobantes deben excluirse
					* Ajuste: 2016-03-10 10:14 se incluye cambio para excluir las notas credito para todas las agencias de aduana
					*/
				if ($cNotCre != "") {
					$qData .= "CONCAT($cAlfa.fcod$nAno.comidxxx,\"~\",$cAlfa.fcod$nAno.comcodxx) NOT IN ($cNotCre) AND ";
				}
				$qData .= "$cAlfa.fcod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND ";
				$qData .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
				$qData .= "GROUP BY $cAlfa.fcod$nAno.terid2xx,$cAlfa.fcod$nAno.teridxxx,$cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
				$qData .= "ORDER BY $cAlfa.fcod$nAno.terid2xx,$cAlfa.fcod$nAno.teridxxx,$cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
				$xData  = f_MySql("SELECT","",$qData,$xConexion01,"");
				// f_Mensaje(__FILE__,__LINE__,$qData." ~ ".mysql_num_rows($xData));

				$mLisCli = array(); $mDatCli = array();
				while ($xDATA = mysql_fetch_array($xData)) {

					if(in_array($xDATA['terid2xx'],$mLisCli) == false) {
						#Trayendo datos terid2xx
						$xDatExt  = array();
						$xRDE     = array();
						$qDatExt  = "SELECT * ";
						$qDatExt .= "FROM $cAlfa.SIAI0150 ";
						$qDatExt .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$xDATA['terid2xx']}\" LIMIT 0,1 ";
						$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");

						if(mysql_num_rows($xDatExt) > 0) {
							$xRDE = mysql_fetch_array($xDatExt);
							if ($xRDE['CLIRECOM']=="SI") {
								$mDatCli[$xDATA['terid2xx']]['CLICATER'] = "COMUN";
							}
							if ($xRDE['CLIRESIM']=="SI") {
								$mDatCli[$xDATA['terid2xx']]['CLICATER'] = "SIMPLIFICADO";
							}
							if ($xRDE['CLIGCXXX']=="SI") {
								$mDatCli[$xDATA['terid2xx']]['CLICATER'] = "CONTRIBUYENTE";
							}
							if ($xRDE['CLINRPXX']=="SI") {
								$mDatCli[$xDATA['terid2xx']]['CLICATER'] = "NORESIDENTE";
							}
							//Busco el codigo del pais
							$qCodPai = "SELECT PAIIDNXX FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"{$xRDE['PAIIDXXX']}\" LIMIT 0,1";
							$xCodPai  = f_MySql("SELECT","",$qCodPai,$xConexion01,"");
							if (mysql_num_rows($xCodPai) > 0) {
								$xRCP = mysql_fetch_array($xCodPai);
								$xRDE['PAIIDXXX'] =  ($xRCP['PAIIDNXX'] != "") ? $xRCP['PAIIDNXX'] : $xRDE['PAIIDXXX'];
							}
							//Fin Busco el codigo del pais
							$mDatCli[$xDATA['terid2xx']]['TDIIDXXX'] = $xRDE['TDIIDXXX'];
							$mDatCli[$xDATA['terid2xx']]['CLIAPE1X'] = utf8_encode($xRDE['CLIAPE1X']);
							$mDatCli[$xDATA['terid2xx']]['CLIAPE2X'] = utf8_encode($xRDE['CLIAPE2X']);
							$mDatCli[$xDATA['terid2xx']]['CLINOM1X'] = utf8_encode($xRDE['CLINOM1X']);
							$mDatCli[$xDATA['terid2xx']]['CLINOM2X'] = utf8_encode($xRDE['CLINOM2X']);
							$mDatCli[$xDATA['terid2xx']]['CLINOMXX'] = ($xRDE['TDIIDXXX'] == 31) ? utf8_encode($xRDE['CLINOMXX']) : "";
							$mDatCli[$xDATA['terid2xx']]['CLIDIRXX'] = utf8_encode($xRDE['CLIDIRXX']);
							$mDatCli[$xDATA['terid2xx']]['DEPIDXXX'] = $xRDE['DEPIDXXX'];
							$mDatCli[$xDATA['terid2xx']]['CIUIDXXX'] = $xRDE['CIUIDXXX'];
							$mDatCli[$xDATA['terid2xx']]['PAIIDXXX'] = $xRDE['PAIIDXXX'];

							$mLisCli[] = $xDATA['terid2xx'];
						}
					}

					if(in_array($xDATA['teridxxx'],$mLisCli) == false) {
						#Trayendo datos teridxxx
						$xDatExt = array();
						$xRDE    = array();
						$qDatExt = "SELECT * ";
						$qDatExt .= "FROM $cAlfa.SIAI0150 ";
						$qDatExt .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$xDATA['teridxxx']}\" LIMIT 0,1 ";
						$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");

						if(mysql_num_rows($xDatExt) > 0) {
							$xRDE = mysql_fetch_array($xDatExt);
							if ($xRDE['CLIRECOM']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "COMUN";
							}
							if ($xRDE['CLIRESIM']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "SIMPLIFICADO";
							}
							if ($xRDE['CLIGCXXX']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "CONTRIBUYENTE";
							}
							if ($xRDE['CLINRPXX']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "NORESIDENTE";
							}

							//Busco el codigo del pais
							$qCodPai = "SELECT PAIIDNXX FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"{$xRDE['PAIIDXXX']}\" LIMIT 0,1";
							$xCodPai  = f_MySql("SELECT","",$qCodPai,$xConexion01,"");
							if (mysql_num_rows($xCodPai) > 0) {
								$xRCP = mysql_fetch_array($xCodPai);
								$xRDE['PAIIDXXX'] =  ($xRCP['PAIIDNXX'] != "") ? $xRCP['PAIIDNXX'] : $xRDE['PAIIDXXX'];
							}
							//Fin Busco el codigo del pais

							$mDatCli[$xDATA['teridxxx']]['TDIIDXXX'] = $xRDE['TDIIDXXX'];
							$mDatCli[$xDATA['teridxxx']]['CLIAPE1X'] = utf8_encode($xRDE['CLIAPE1X']);
							$mDatCli[$xDATA['teridxxx']]['CLIAPE2X'] = utf8_encode($xRDE['CLIAPE2X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOM1X'] = utf8_encode($xRDE['CLINOM1X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOM2X'] = utf8_encode($xRDE['CLINOM2X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOMXX'] = ($xRDE['TDIIDXXX'] == 31) ? utf8_encode($xRDE['CLINOMXX']) : "";
							$mDatCli[$xDATA['teridxxx']]['CLIDIRXX'] = utf8_encode($xRDE['CLIDIRXX']);
							$mDatCli[$xDATA['teridxxx']]['DEPIDXXX'] = $xRDE['DEPIDXXX'];
							$mDatCli[$xDATA['teridxxx']]['CIUIDXXX'] = $xRDE['CIUIDXXX'];
							$mDatCli[$xDATA['teridxxx']]['PAIIDXXX'] = $xRDE['PAIIDXXX'];

							$mLisCli[] = $xDATA['teridxxx'];
						}
					}
					$xDATA['CLINOMDI'] = $cNomAdu;

					$nPrac = 0; $nAsum = 0; $nComun = 0; $nSimpl = 0; $nDom = 0; $nCreeAsum = 0; $nCreePrac = 0;
					$cComId = "{$xDATA['teridxxx']}-{$xDATA['terid2xx']}-{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}";
					#Calculando valores:
					#Retencion en la fuente practicada renta
					#Retencion en la fuente asumida renta
					#Retencion en la fuente practicada iva regimen comun
					#Retencion en la fuente asumida iva regimen simp.
					#Retencion en la fuente practicada iva no domiciliados
					#Traigo las cuantas que empiezan por 2365,531520,2367 para le comprobante

					$qFcod  = "SELECT ";
					$qFcod .= "$cAlfa.$cTabFac.*, ";
					#para las cuentas 2365 y 2367 los creditos suman, los debitos restan
					$qFcod .= "IF($cAlfa.$cTabFac.commovxx=\"C\",$cAlfa.$cTabFac.comvlrxx,$cAlfa.$cTabFac.comvlrxx*-1) AS comvlrsu,";
					#para la cuenta 531520 los creditos restan, los debitos suman
					$qFcod .= "IF($cAlfa.$cTabFac.commovxx=\"C\",$cAlfa.$cTabFac.comvlrxx*-1,$cAlfa.$cTabFac.comvlrxx) AS comvlrre ";
					$qFcod .= "FROM $cAlfa.$cTabFac ";
					$qFcod .= "WHERE ";
					$qFcod .= "$cAlfa.$cTabFac.comidxxx = \"{$xDATA['comidxxx']}\" AND ";
					$qFcod .= "$cAlfa.$cTabFac.comcodxx = \"{$xDATA['comcodxx']}\" AND ";
					$qFcod .= "$cAlfa.$cTabFac.comcscxx = \"{$xDATA['comcscxx']}\" AND ";
					switch ($cAlfa) {
						case "ADUACARX":
						case "TEADUACARX":
						case "DEADUACARX":
							//Las validaciones de los terceros se hacen en el while
							$qFcod .= "(SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,4) IN ($cCueRetFue) OR SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,6) IN ($cCueImpAsu) OR SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,4) IN ($cCueRetIva)".(($cReteCree != "") ? " OR ".str_replace("fcod$nAno", $cTabFac, $cReteCree) : "").") AND ";
						break;
						default:
							$qFcod .= "(($cAlfa.$cTabFac.teridxxx = \"{$xDATA['terid2xx']}\" AND $cAlfa.$cTabFac.terid2xx = \"{$xDATA['teridxxx']}\") OR ($cAlfa.$cTabFac.teridxxx = \"{$xDATA['teridxxx']}\" AND $cAlfa.$cTabFac.terid2xx =  \"{$xDATA['terid2xx']}\")) AND ";
							$qFcod .= "(SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,4) IN ($cCueRetFue) OR ";
							$qFcod .= "SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,6) IN ($cCueImpAsu) OR ";
							$qFcod .= "SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,4) IN ($cCueRetIva)".(($cReteCree != "") ? " OR ".str_replace("fcod$nAno", $cTabFac, $cReteCree) : "").") AND ";
						break;
					}
					$qFcod .= "$cAlfa.$cTabFac.regestxx = \"ACTIVO\" ";
					$xFcod = mysql_query($qFcod,$xConexion01);
					//f_Mensaje(__FILE__,__LINE__,$qFcod." ~ ".mysql_num_rows($xFcod));

					if (mysql_num_rows($xFcod) > 0) {
					while($xRF = mysql_fetch_array($xFcod)) {
						$nIncRect = 0;
						//Validaciones de los terceros para ADUACARGA
							if ($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") {

							$nIncRect = 1;
							//Primera condicion: que los terceros del reistro analizado esten contenidos en los terceros de la retencion
							if (($xRF['teridxxx'] == $xDATA['terid2xx'] && $xRF['terid2xx'] == $xDATA['teridxxx']) ||
									($xRF['teridxxx'] == $xDATA['teridxxx'] && $xRF['terid2xx'] == $xDATA['terid2xx'])) {
								$nIncRect = 0;
							} else {
								//si el comprobante es una G y el terid2xx del reistro analizado este en el teridxxx de la retencion,
								//y el teridxxx del registro analizado este en el subcentro de costo de la retencion
								//y la cuenta de retencion empieza por 2365 o 2367
								if (($xRF['comidxxx'] == "G" || ($xRF['comidxxx'] == "L" && in_array($xRF['comidxxx']."~".$xRF['comcodxx'], $vCarBa) == true)) &&
										$xRF['teridxxx'] == $xDATA['terid2xx'] && $xRF['sccidxxx'] == $xDATA['teridxxx'] &&
										(in_array(substr($xRF['pucidxxx'],0,4), $vCueRetFue) || in_array(substr($xRF['pucidxxx'],0,4), $vCueRetIva))) {
									$nIncRect = 0;
								} else {
									//Se busca si el subcentro de costo de la retencion es un DO, se trae el importador
									$qDatDo = "SELECT cliidxxx FROM $cAlfa.sys00121 WHERE docidxxx = \"{$xRF['sccidxxx'] }\" LIMIT 0,1";
									$xDatDo  = f_MySql("SELECT","",$qDatDo,$xConexion01,"");
									//f_Mensaje(__FILE__,__LINE__,$qDatDo." ~ ".mysql_num_rows($xDatDo));

									//si el comprobante es una G y el terid2xx del reistro analizado este en el teridxxx de la retencion,
									//y el teridxxx del registro analizado es el importador del DO
									//y la cuenta empiece por 2365 o 2367
									if (mysql_num_rows($xDatDo) > 0) {
										$xRDD = mysql_fetch_array($xDatDo);
										if (($xRF['comidxxx'] == "G" || ($xRF['comidxxx'] == "L" && in_array($xRF['comidxxx']."~".$xRF['comcodxx'], $vCarBa) == true)) &&
												$xRF['teridxxx'] == $xDATA['terid2xx'] && $xDATA['teridxxx'] == $xRDD['cliidxxx'] &&
												(in_array(substr($xRF['pucidxxx'],0,4), $vCueRetFue) || in_array(substr($xRF['pucidxxx'],0,4), $vCueRetIva))) {
											$nIncRect = 0;
										}
									}
								}
							}
						}

						if ($nIncRect == 0) {

							//Verifico si no es una cuenta de retencion Cree
							if (in_array($xRF['pucidxxx'], $mRetCree) == true) {
									$nCreePrac += $xRF['comvlrsu'];
							} else {
								//Verifico si es un iva practicado
								if(in_array(substr($xRF['pucidxxx'],0,4), $vCueRetFue)) {
									$nPrac += $xRF['comvlrsu'];
								}
								//Verifico si es un iva asumido
								if(in_array(substr($xRF['pucidxxx'],0,6), $vCueImpAsu)) {
									$nAsum += $xRF['comvlrre'];
								}
								//Verifico el tipo de rentencion
								if(in_array(substr($xRF['pucidxxx'],0,4), $vCueRetIva)) {

									if($xRF['tertipxx'] == "CLIPROCX"){
										$cCliId = $xRF['teridxxx'];
									} else {
										$cCliId = $xRF['terid2xx'];
									}

									switch ($mDatCli[$cCliId]['CLICATER']) {
										case "CONTRIBUYENTE":
										case "COMUN":
										$nComun += $xRF['comvlrsu'];
										break;
										case "SIMPLIFICADO":
										$nSimpl += $xRF['comvlrsu'];
										break;
										case "NORESIDENTE":
										$nDom += $xRF['comvlrsu'];
										break;
									}
								} ## if(in_array(substr($xRF['pucidxxx'],0,4), $vCueRetIva)) { ##
							}
						}
						}
					}

					//Para los comprobantes de Reembolso de caja Menor trae el iva del Recibo de Caja Menor
					//Busco el comprobante cruce dos en recibos de caja menor
					if ($xDATA['cajameno'] != "" && $xDATA['comvlr02'] == 0 && in_array("{$xDATA['comidxxx']}~{$xDATA['comcodxx']}", $mRCM) == true) {
						$vRecCaja  = explode("~",$xDATA['cajameno']);
						$cRecCaja  = "";
						for ($nRC=0; $nRC<count($vRecCaja); $nRC++) {
							$cRecCaja .= ($vRecCaja[$nRC] != "") ? "\"{$vRecCaja[$nRC]}\"," : "";
						}
						$cRecCaja = substr($cRecCaja, 0, strlen($cRecCaja)-1);

						if ($cRecCaja != "") {
							$qRecCaja  = "SELECT SUM(IF(commovxx=\"D\",comvlr02,comvlr02*-1)) AS comvlr02 ";
							$qRecCaja .= "FROM $cAlfa.fcme$nAno ";
							$qRecCaja .= "WHERE ";
							$qRecCaja .= "CONCAT(comidxxx,\"-\",comcodxx,\"-\",comcscxx,\"-\",comseqxx) IN ($cRecCaja)";
							$xRecCaja  = f_MySql("SELECT","",$qRecCaja,$xConexion01,"");
							//f_Mensaje(__FILE__,__LINE__,$qRecCaja." ~ ".mysql_num_rows($xRecCaja));
							if (mysql_num_rows($xRecCaja) > 0) {
								$xRRC = mysql_fetch_array($xRecCaja);
								$xDATA['comvlr02'] = $xRRC['comvlr02'];
							}
						}
					}

					if ($xDATA['comvlr01'] == 0) {
					switch ($cAlfa) {
						case "COLMASXX":
						case "DECOLMASXX":
						case "TECOLMASXX":
						$xDATA['comvlr01'] = $xDATA['comvlrxx'];
						$xDATA['comvlr02'] = 0;
						break;
						default:
							$nCal = 0;
							if ($xDATA['comidxxx'] == "G" || $xDATA['comidxxx'] == "L") {
									#Busco si el concepto contable es de pago de tributo
									$qPagTri  = "SELECT ";
									$qPagTri .= "ctoptaxg, ";
									$qPagTri .= "ctoptaxl  ";
									$qPagTri .= "FROM $cAlfa.fpar0119 ";
									$qPagTri .= "WHERE  ";
									$qPagTri .= "pucidxxx = \"{$xDATA['pucidxxx']}\" AND  ";
									$qPagTri .= "ctoidxxx = \"{$xDATA['ctoidxxx']}\" LIMIT 0,1  ";
									$xPagTri  = f_MySql("SELECT","",$qPagTri,$xConexion01,"");
									if (mysql_num_rows($xPagTri) > 0) {
										$xRPT = mysql_fetch_array($xPagTri);
										if($xDATA['comidxxx'] == "G" && $xRPT['ctoptaxg'] == "SI") {
											$xDATA['comvlr01'] = $xDATA['comvlrxx'];
											$xDATA['comvlr02'] = 0;
											$nCal = 1;
										}

									if($xDATA['comidxxx'] == "L" && $xRPT['ctoptaxl'] == "SI") {
											$xDATA['comvlr01'] = $xDATA['comvlrxx'];
											$xDATA['comvlr02'] = 0;
											$nCal = 1;
										}
									}
							}
							if ($nCal == 0) {
								switch ($cAlfa) {
									case "GRUPOGLA": case "DEGRUPOGLA": case "TEGRUPOGLA":
									case "ADUACARX": case "DEADUACARX": case "TEADUACARX":
										#Si el comvrl01x es cero calculo la base
										$xDATA['comvlr01'] = ($xDATA['comvlrxx']/1.16);
										$xDATA['comvlr02'] = ($xDATA['comvlr01']*0.16);
									break;
									default:
										#Si el valor de la base e iva es cero, en la base se envia el valor del comprobante
										$xDATA['comvlr01'] = ($xDATA['comvlr01'] == 0 && $xDATA['comvlr02'] == 0) ? $xDATA['comvlrxx'] : $xDATA['comvlr01'];
									break;
								}
							}
						break;
					}
					}

					#Se incluye para ADUACARGA que en la columna IVA mayor valor del costo o gasto sea siempre de valor cero
					if ($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") {
						$xDATA['comvlr02'] = 0;
					}

					#Validacion para ADUACARGA, se excluye el registro si este tienen valor cero en las siguientes columnas:
					#Pago o abono en cta
					#IVA mayor valor del costo o gasto
					#Retencion en la fuente practicada renta
					#Retencion en la fuente asumida renta
					#Retencion en la fuente practicada iva regimen comun
					#Retencion en la fuente asumida iva regimen simp.
					#Retencion en la fuente practicada iva no domiciliados
					$nIncluir = 0;
					if ($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") {
						$nIncluir = ($xDATA['comvlr01'] == 0 && $xDATA['comvlr02'] == 0 && $nPrac == 0  && $nAsum == 0 && $nComun == 0 && $nSimpl == 0 && $nDom == 0) ? 1 : 0;
					}

					if ($nIncluir == 0) {
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['TDIIDXXX']  = ($mData[$xDATA['terid2xx']]['TDIIDXXX'] <> "")?$mData[$xDATA['terid2xx']]['TDIIDXXX']:$mDatCli[$xDATA['terid2xx']]['TDIIDXXX'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['terid2xx']  = ($mData[$xDATA['terid2xx']]['terid2xx'] <> "")?$mData[$xDATA['terid2xx']]['terid2xx']:$xDATA['terid2xx'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['terid2di']  = ($mData[$xDATA['terid2xx']]['terid2di'] <> "")?$mData[$xDATA['terid2xx']]['terid2di']:$xDATA['terid2xx'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLIAPE1X']  = ($mData[$xDATA['terid2xx']]['CLIAPE1X'] <> "")?$mData[$xDATA['terid2xx']]['CLIAPE1X']:$mDatCli[$xDATA['terid2xx']]['CLIAPE1X'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLIAPE2X']  = ($mData[$xDATA['terid2xx']]['CLIAPE2X'] <> "")?$mData[$xDATA['terid2xx']]['CLIAPE2X']:$mDatCli[$xDATA['terid2xx']]['CLIAPE2X'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLINOM1X']  = ($mData[$xDATA['terid2xx']]['CLINOM1X'] <> "")?$mData[$xDATA['terid2xx']]['CLINOM1X']:$mDatCli[$xDATA['terid2xx']]['CLINOM1X'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLINOM2X']  = ($mData[$xDATA['terid2xx']]['CLINOM2X'] <> "")?$mData[$xDATA['terid2xx']]['CLINOM2X']:$mDatCli[$xDATA['terid2xx']]['CLINOM2X'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLINOMXX']  = ($mData[$xDATA['terid2xx']]['CLINOMXX'] <> "")?$mData[$xDATA['terid2xx']]['CLINOMXX']:$mDatCli[$xDATA['terid2xx']]['CLINOMXX'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLIDIRXX']  = ($mData[$xDATA['terid2xx']]['CLIDIRXX'] <> "")?$mData[$xDATA['terid2xx']]['CLIDIRXX']:$mDatCli[$xDATA['terid2xx']]['CLIDIRXX'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['DEPIDXXX']  = ($mData[$xDATA['terid2xx']]['DEPIDXXX'] <> "")?$mData[$xDATA['terid2xx']]['DEPIDXXX']:$mDatCli[$xDATA['terid2xx']]['DEPIDXXX'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CIUIDXXX']  = ($mData[$xDATA['terid2xx']]['CIUIDXXX'] <> "")?$mData[$xDATA['terid2xx']]['CIUIDXXX']:$mDatCli[$xDATA['terid2xx']]['CIUIDXXX'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['PAIIDXXX']  = ($mData[$xDATA['terid2xx']]['PAIIDXXX'] <> "")?$mData[$xDATA['terid2xx']]['PAIIDXXX']:$mDatCli[$xDATA['terid2xx']]['PAIIDXXX'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['TDIIDXXC']  = ($mData[$xDATA['terid2xx']]['TDIIDXXC'] <> "")?$mData[$xDATA['terid2xx']]['TDIIDXXC']:$mDatCli[$xDATA['teridxxx']]['TDIIDXXX'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLIIDXXC']  = ($mData[$xDATA['terid2xx']]['CLIIDXXC'] <> "")?$mData[$xDATA['terid2xx']]['CLIIDXXC']:$xDATA['teridxxx'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLIAPE1C']  = ($mData[$xDATA['terid2xx']]['CLIAPE1C'] <> "")?$mData[$xDATA['terid2xx']]['CLIAPE1C']:$mDatCli[$xDATA['teridxxx']]['CLIAPE1X'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLIAPE2C']  = ($mData[$xDATA['terid2xx']]['CLIAPE2C'] <> "")?$mData[$xDATA['terid2xx']]['CLIAPE2C']:$mDatCli[$xDATA['teridxxx']]['CLIAPE2X'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLINOM1C']  = ($mData[$xDATA['terid2xx']]['CLINOM1C'] <> "")?$mData[$xDATA['terid2xx']]['CLINOM1C']:$mDatCli[$xDATA['teridxxx']]['CLINOM1X'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLINOM2C']  = ($mData[$xDATA['terid2xx']]['CLINOM2C'] <> "")?$mData[$xDATA['terid2xx']]['CLINOM2C']:$mDatCli[$xDATA['teridxxx']]['CLINOM2X'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLINOMXC']  = ($mData[$xDATA['terid2xx']]['CLINOMXC'] <> "")?$mData[$xDATA['terid2xx']]['CLINOMXC']:$mDatCli[$xDATA['teridxxx']]['CLINOMXX'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['PAGOXXXX'] += $xDATA['comvlr01'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['IVAXXXXX'] += $xDATA['comvlr02'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['PRACXXXX'] += $nPrac;
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['ASUMXXXX'] += $nAsum;
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['COMUNXXX'] += $nComun;
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['SIMPLXXX'] += $nSimpl;
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['NDOMXXXX'] += $nDom;
						//Nuevos campos para retecree
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['PRACCREE'] += $nCreePrac;
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['ASUMCREE'] += $nCreeAsum;
						//Fin Nuevos campos para retecree
					}
				}
			break;
			case "1018":

				//Dentro de las cuentas seleccionadas busco solo aquellas que sean por cobrar
				$cCuenta = "";
				for($nC=0; $nC<count($vCuenta); $nC++) {
					$qCxC  = "SELECT CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx ";
					$qCxC .= "FROM $cAlfa.fpar0115 ";
					$qCxC .= "WHERE ";
					$qCxC .= "CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$vCuenta[$nC]}\" AND ";
					$qCxC .= "pucdetxx = \"C\" LIMIT 0,1";
					$xCxC  = mysql_query($qCxC,$xConexion01);
					//f_Mensaje(__FILE__,__LINE__,$qCxC."~".mysql_num_rows($xCxC));
					if (mysql_num_rows($xCxC) > 0) {
						$xRCxC = mysql_fetch_array($xCxC);
						$cCuenta .= "\"{$xRCxC['pucidxxx']}\",";
					}
				}
				$cCuenta = substr($cCuenta, 0, -1);

				$vTablas = array(); $mAux = array();
				if ($cCuenta != "") {
					for ($nAnio=$vSysStr['financiero_ano_instalacion_modulo'];$nAnio<=$nAno;$nAnio++) {
						$qDatMov  = "SELECT ";
						$qDatMov .= "comidxxx, ";
						$qDatMov .= "comcodxx, ";
						$qDatMov .= "comcscxx, ";
						$qDatMov .= "comidcxx, ";
						$qDatMov .= "comcodcx, ";
						$qDatMov .= "comcsccx, ";
						$qDatMov .= "teridxxx, ";
						$qDatMov .= "terid2xx, ";
						$qDatMov .= "pucidxxx, ";
						$qDatMov .= "comfecxx, ";
						$qDatMov .= "comfecve, ";
						$qDatMov .= "commovxx, ";
						$qDatMov .= "IF(commovxx = \"D\", comvlrxx, comvlrxx*-1) AS saldoxxx ";
						$qDatMov .= "FROM $cAlfa.fcod$nAnio ";
						$qDatMov .= "WHERE  ";
						$qDatMov .= "pucidxxx IN ($cCuenta) AND ";
						$qDatMov .= "comfecxx <= \"$dHasta\" AND ";
						//$qDatMov .= "teridxxx = \"830070083\" AND ";
						$qDatMov .= "regestxx = \"ACTIVO\" ";
						$qDatMov .= "ORDER BY ABS(teridxxx), ABS(terid2xx) ";
						$xDatMov = mysql_query($qDatMov,$xConexion01);
						//echo $qDatMov."~".mysql_num_rows($xDatMov)."<br><br>";

						while ($xDATA = mysql_fetch_array($xDatMov)) {

							if ($xDATA['comidxxx'] != $xDATA['comidcxx'] ||
									$xDATA['comcodxx'] != $xDATA['comcodcx'] ||
									$xDATA['comcscxx'] != $xDATA['comcsccx']) {
								//Se debe buscar el cliente del combropante que se esta cancelando
								for ($nAnioDC=$nAnio;$nAnioDC>=$vSysStr['financiero_ano_instalacion_modulo'];$nAnioDC--) {
									$qDocCru  = "SELECT ";
									$qDocCru .= "terid2xx  ";
									$qDocCru .= "FROM $cAlfa.fcod$nAnioDC ";
									$qDocCru .= "WHERE  ";
									$qDocCru .= "comidxxx = \"{$xDATA['comidcxx']}\" AND ";
									$qDocCru .= "comcodxx = \"{$xDATA['comcodcx']}\" AND ";
									$qDocCru .= "comcscxx = \"{$xDATA['comcsccx']}\" AND ";
									$qDocCru .= "teridxxx = \"{$xDATA['teridxxx']}\" AND ";
									$qDocCru .= "pucidxxx = \"{$xDATA['pucidxxx']}\" AND ";
									$qDocCru .= "regestxx = \"ACTIVO\" LIMIT 0,1";
									$xDocCru = mysql_query($qDocCru,$xConexion01);
									//echo $qDocCru."~".mysql_num_rows($xDocCru)."<br><br>";
									if (mysql_num_rows($xDocCru) > 0) {
										$xRDC = mysql_fetch_array($xDocCru);
										$xDATA['terid2xx'] = $xRDC['terid2xx'];

										$nAnioDC = $vSysStr['financiero_ano_instalacion_modulo']-1;
									}
								}
							}

							$mAux[$xDATA['teridxxx']."~".$xDATA['terid2xx']]['teridxxx']  = $xDATA['teridxxx'];
							$mAux[$xDATA['teridxxx']."~".$xDATA['terid2xx']]['terid2xx']  = $xDATA['terid2xx'];
							$mAux[$xDATA['teridxxx']."~".$xDATA['terid2xx']]['COMVLRXX'] += $xDATA['saldoxxx'];
						}
					}
				}

				##Creacion de la tabla detalle del dia
				foreach ($mAux as $cKey => $cValue) {
					if ($mAux[$cKey]['COMVLRXX'] != 0) {

						# Traigo el Nombre del Cliente
						$qNomCli  = "SELECT * ";
						$qNomCli .= "FROM $cAlfa.SIAI0150 ";
						$qNomCli .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$mAux[$cKey]['teridxxx']}\" LIMIT 0,1";
						$xNomCli = f_MySql("SELECT","",$qNomCli,$xConexion01,"");
						$vNomCli = mysql_fetch_array($xNomCli);
						# Fin Traigo el Nombre del Cliente

						//Busco el codigo del pais
						$qCodPai = "SELECT PAIIDNXX FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"{$vNomCli['PAIIDXXX']}\" LIMIT 0,1";
						$xCodPai  = f_MySql("SELECT","",$qCodPai,$xConexion01,"");
						if (mysql_num_rows($xCodPai) > 0) {
							$xRCP = mysql_fetch_array($xCodPai);
							$vNomCli['PAIIDXXX'] =  ($xRCP['PAIIDNXX'] != "") ? $xRCP['PAIIDNXX'] : $vNomCli['PAIIDXXX'];
						}
						//Fin Busco el codigo del pais

						# Traigo el Nombre del Cliente con terid2xx
						$qNomCl2  = "SELECT * ";
						$qNomCl2 .= "FROM $cAlfa.SIAI0150 ";
						$qNomCl2 .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$mAux[$cKey]['terid2xx']}\" LIMIT 0,1";
						$xNomCl2 = f_MySql("SELECT","",$qNomCl2,$xConexion01,"");
						$vNomCl2 = mysql_fetch_array($xNomCl2);
						//f_Mensaje(__FILE__,__LINE__,$qNomCl2."~".mysql_num_rows($xNomCl2));
						# Fin Traigo el Nombre del Cliente

						//Busco el codigo del pais
						$qCodPa1 = "SELECT PAIIDNXX FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"{$vNomCl2['PAIIDXXX']}\" LIMIT 0,1";
						$xCodPa1  = f_MySql("SELECT","",$qCodPa1,$xConexion01,"");
						if (mysql_num_rows($xCodPa1) > 0) {
							$xRCP1 = mysql_fetch_array($xCodPa1);
							$vNomCl2['PAIIDXXX'] =  ($xRCP1['PAIIDNXX'] != "") ? $xRCP1['PAIIDNXX'] : $vNomCl2['PAIIDXXX'];
						}
						//Fin Busco el codigo del pais

						$mData[$cKey]['TDIIDXXX']  = ($mData[$cKey]['TDIIDXXX'] <> "")?$mData[$cKey]['TDIIDXXX']:$vNomCli['TDIIDXXX'];
						$mData[$cKey]['teridxxx']  = ($mData[$cKey]['teridxxx'] <> "")?$mData[$cKey]['teridxxx']:$mAux[$cKey]['teridxxx'];
						$mData[$cKey]['CLIAPE1X']  = ($mData[$cKey]['CLIAPE1X'] <> "")?$mData[$cKey]['CLIAPE1X']:$vNomCli['CLIAPE1X'];
						$mData[$cKey]['CLIAPE2X']  = ($mData[$cKey]['CLIAPE2X'] <> "")?$mData[$cKey]['CLIAPE2X']:$vNomCli['CLIAPE2X'];
						$mData[$cKey]['CLINOM1X']  = ($mData[$cKey]['CLINOM1X'] <> "")?$mData[$cKey]['CLINOM1X']:$vNomCli['CLINOM1X'];
						$mData[$cKey]['CLINOM2X']  = ($mData[$cKey]['CLINOM2X'] <> "")?$mData[$cKey]['CLINOM2X']:$vNomCli['CLINOM2X'];
						$mData[$cKey]['CLINOMXX']  = ($mData[$cKey]['CLINOMXX'] <> "")?$mData[$cKey]['CLINOMXX']:(($vNomCli['TDIIDXXX'] == 31) ? $vNomCli['CLINOMXX'] : "");
						$mData[$cKey]['CLIDIRXX']  = ($mData[$cKey]['CLIDIRXX'] <> "")?$mData[$cKey]['CLIDIRXX']:$vNomCli['CLIDIRXX'];
						$mData[$cKey]['DEPIDXXX']  = ($mData[$cKey]['DEPIDXXX'] <> "")?$mData[$cKey]['DEPIDXXX']:$vNomCli['DEPIDXXX'];
						$mData[$cKey]['CIUIDXXX']  = ($mData[$cKey]['CIUIDXXX'] <> "")?$mData[$cKey]['CIUIDXXX']:$vNomCli['CIUIDXXX'];
						$mData[$cKey]['PAIIDXXX']  = ($mData[$cKey]['PAIIDXXX'] <> "")?$mData[$cKey]['PAIIDXXX']:$vNomCli['PAIIDXXX'];
						$mData[$cKey]['COMVLRXX']  = $mAux[$cKey]['COMVLRXX'];
						//Datos adicionales para terid2xx
						$mData[$cKey]['TDIIDXXC']  = ($mData[$cKey]['TDIIDXXC'] <> "")?$mData[$cKey]['TDIIDXXC']:$vNomCl2['TDIIDXXX'];
						$mData[$cKey]['terid2xx']  = ($mData[$cKey]['terid2xx'] <> "")?$mData[$cKey]['terid2xx']:$mAux[$cKey]['terid2xx'];
						$mData[$cKey]['CLIAPE1C']  = ($mData[$cKey]['CLIAPE1C'] <> "")?$mData[$cKey]['CLIAPE1C']:$vNomCl2['CLIAPE1X'];
						$mData[$cKey]['CLIAPE2C']  = ($mData[$cKey]['CLIAPE2C'] <> "")?$mData[$cKey]['CLIAPE2C']:$vNomCl2['CLIAPE2X'];
						$mData[$cKey]['CLINOM1C']  = ($mData[$cKey]['CLINOM1C'] <> "")?$mData[$cKey]['CLINOM1C']:$vNomCl2['CLINOM1X'];
						$mData[$cKey]['CLINOM2C']  = ($mData[$cKey]['CLINOM2C'] <> "")?$mData[$cKey]['CLINOM2C']:$vNomCl2['CLINOM2X'];
						$mData[$cKey]['CLINOMXC']  = ($mData[$cKey]['CLINOMXC'] <> "")?$mData[$cKey]['CLINOMXC']:$vNomCl2['CLINOMXX'];
						$mData[$cKey]['CLIDIRXC']  = ($mData[$cKey]['CLIDIRXC'] <> "")?$mData[$cKey]['CLIDIRXC']:$vNomCl2['CLIDIRXX'];
						$mData[$cKey]['DEPIDXXC']  = ($mData[$cKey]['DEPIDXXC'] <> "")?$mData[$cKey]['DEPIDXXC']:$vNomCl2['DEPIDXXX'];
						$mData[$cKey]['CIUIDXXC']  = ($mData[$cKey]['CIUIDXXC'] <> "")?$mData[$cKey]['CIUIDXXC']:$vNomCl2['CIUIDXXX'];
						$mData[$cKey]['PAIIDXXC']  = ($mData[$cKey]['PAIIDXXC'] <> "")?$mData[$cKey]['PAIIDXXC']:$vNomCl2['PAIIDXXX'];
					}
				}

			break;
			case "1027":

				//Dentro de las cuentas seleccionadas busco solo aquellas que sean por pagar
				$cCuenta = "";
				for($nC=0; $nC<count($vCuenta); $nC++) {
					$qCxP  = "SELECT CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx ";
					$qCxP .= "FROM $cAlfa.fpar0115 ";
					$qCxP .= "WHERE ";
					$qCxP .= "CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$vCuenta[$nC]}\" AND ";
					$qCxP .= "pucdetxx = \"P\" LIMIT 0,1";
					$xCxP  = mysql_query($qCxP,$xConexion01);
					//f_Mensaje(__FILE__,__LINE__,$qCxP."~".mysql_num_rows($xCxP));
					if (mysql_num_rows($xCxP) > 0) {
						$xRCxP = mysql_fetch_array($xCxP);
						$cCuenta .= "\"{$xRCxP['pucidxxx']}\",";
					}
				}
				$cCuenta = substr($cCuenta, 0, -1);

				$vTablas = array(); $mAux = array();
				if ($cCuenta != "") {
					for ($nAnio=$vSysStr['financiero_ano_instalacion_modulo'];$nAnio<=$nAno;$nAnio++) {
						$qDatMov  = "SELECT ";
						$qDatMov .= "comidxxx, ";
						$qDatMov .= "comcodxx, ";
						$qDatMov .= "comcscxx, ";
						$qDatMov .= "comidcxx, ";
						$qDatMov .= "comcodcx, ";
						$qDatMov .= "comcsccx, ";
						$qDatMov .= "teridxxx, ";
						$qDatMov .= "terid2xx, ";
						$qDatMov .= "pucidxxx, ";
						$qDatMov .= "comfecxx, ";
						$qDatMov .= "comfecve, ";
						$qDatMov .= "commovxx, ";
						$qDatMov .= "IF(commovxx = \"D\", comvlrxx, comvlrxx*-1) AS saldoxxx ";
						$qDatMov .= "FROM $cAlfa.fcod$nAnio ";
						$qDatMov .= "WHERE  ";
						$qDatMov .= "pucidxxx IN ($cCuenta) AND ";
						$qDatMov .= "comfecxx <= \"$dHasta\" AND ";
						//$qDatMov .= "teridxxx = \"830070083\" AND ";
						// $qDatMov .= "teridxxx = \"51720826\" AND ";
						// $qDatMov .= "comidcxx = \"P\" AND ";
						// $qDatMov .= "comcodcx = \"020\" AND ";
						// $qDatMov .= "comcsccx IN (\"1267\",\"241891\") AND ";
						$qDatMov .= "regestxx = \"ACTIVO\" ";
						$qDatMov .= "ORDER BY ABS(teridxxx), ABS(terid2xx) ";
						$xDatMov = mysql_query($qDatMov,$xConexion01);
						//echo $qDatMov."~".mysql_num_rows($xDatMov)."<br><br>";

						while ($xDATA = mysql_fetch_array($xDatMov)) {
							if ($xDATA['comidxxx'] != $xDATA['comidcxx'] ||
									$xDATA['comcodxx'] != $xDATA['comcodcx'] ||
									$xDATA['comcscxx'] != $xDATA['comcsccx']) {
								//Se debe buscar el cliente del combropante que se esta cancelando
								for ($nAnioDC=$nAnio;$nAnioDC>=$vSysStr['financiero_ano_instalacion_modulo'];$nAnioDC--) {
									$qDocCru  = "SELECT ";
									$qDocCru .= "terid2xx  ";
									$qDocCru .= "FROM $cAlfa.fcod$nAnioDC ";
									$qDocCru .= "WHERE  ";
									$qDocCru .= "comidxxx = \"{$xDATA['comidcxx']}\" AND ";
									$qDocCru .= "comcodxx = \"{$xDATA['comcodcx']}\" AND ";
									$qDocCru .= "comcscxx = \"{$xDATA['comcsccx']}\" AND ";
									$qDocCru .= "teridxxx = \"{$xDATA['teridxxx']}\" AND ";
									$qDocCru .= "pucidxxx = \"{$xDATA['pucidxxx']}\" AND ";
									$qDocCru .= "regestxx = \"ACTIVO\" LIMIT 0,1";
									$xDocCru = mysql_query($qDocCru,$xConexion01);
									//echo $qDocCru."~".mysql_num_rows($xDocCru)."<br><br>";
									if (mysql_num_rows($xDocCru) > 0) {
										$xRDC = mysql_fetch_array($xDocCru);
										$xDATA['terid2xx'] = $xRDC['terid2xx'];

										$nAnioDC = $vSysStr['financiero_ano_instalacion_modulo']-1;
									}
								}
							}

							//echo "{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}~{$xDATA['terid2xx']}~{$xDATA['saldoxxx']}<br>";
							$mAux[$xDATA['teridxxx']."~".$xDATA['terid2xx']]['teridxxx']  = $xDATA['teridxxx'];
							$mAux[$xDATA['teridxxx']."~".$xDATA['terid2xx']]['terid2xx']  = $xDATA['terid2xx'];
							$mAux[$xDATA['teridxxx']."~".$xDATA['terid2xx']]['COMVLRXX'] += $xDATA['saldoxxx'];
						}
					}
				}

				foreach ($mAux as $cKey => $cValue) {
					if ($mAux[$cKey]['COMVLRXX'] != 0) {

						# Traigo el Nombre del Cliente
						$qNomCli  = "SELECT * ";
						$qNomCli .= "FROM $cAlfa.SIAI0150 ";
						$qNomCli .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$mAux[$cKey]['teridxxx']}\" LIMIT 0,1";
						$xNomCli = f_MySql("SELECT","",$qNomCli,$xConexion01,"");
						$vNomCli = mysql_fetch_array($xNomCli);
						# Fin Traigo el Nombre del Cliente

						//Busco el codigo del pais
						$qCodPai = "SELECT PAIIDNXX FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"{$vNomCli['PAIIDXXX']}\" LIMIT 0,1";
						$xCodPai  = f_MySql("SELECT","",$qCodPai,$xConexion01,"");
						if (mysql_num_rows($xCodPai) > 0) {
							$xRCP = mysql_fetch_array($xCodPai);
							$vNomCli['PAIIDXXX'] =  ($xRCP['PAIIDNXX'] != "") ? $xRCP['PAIIDNXX'] : $vNomCli['PAIIDXXX'];
						}
						//Fin Busco el codigo del pais

						# Traigo el Nombre del Cliente con terid2xx
						$qNomCl2  = "SELECT * ";
						$qNomCl2 .= "FROM $cAlfa.SIAI0150 ";
						$qNomCl2 .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$mAux[$cKey]['terid2xx']}\" LIMIT 0,1";
						$xNomCl2 = f_MySql("SELECT","",$qNomCl2,$xConexion01,"");
						$vNomCl2 = mysql_fetch_array($xNomCl2);
						//f_Mensaje(__FILE__,__LINE__,$qNomCl2."~".mysql_num_rows($xNomCl2));
						# Fin Traigo el Nombre del Cliente

						//Busco el codigo del pais
						$qCodPa1 = "SELECT PAIIDNXX FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"{$vNomCl2['PAIIDXXX']}\" LIMIT 0,1";
						$xCodPa1  = f_MySql("SELECT","",$qCodPa1,$xConexion01,"");
						if (mysql_num_rows($xCodPa1) > 0) {
							$xRCP1 = mysql_fetch_array($xCodPa1);
							$vNomCl2['PAIIDXXX'] =  ($xRCP1['PAIIDNXX'] != "") ? $xRCP1['PAIIDNXX'] : $vNomCl2['PAIIDXXX'];
						}
						//Fin Busco el codigo del pais

						$mData[$cKey]['TDIIDXXX']  = ($mData[$cKey]['TDIIDXXX'] <> "")?$mData[$cKey]['TDIIDXXX']:$vNomCli['TDIIDXXX'];
						$mData[$cKey]['teridxxx']  = ($mData[$cKey]['teridxxx'] <> "")?$mData[$cKey]['teridxxx']:$mAux[$cKey]['teridxxx'];
						$mData[$cKey]['CLIAPE1X']  = ($mData[$cKey]['CLIAPE1X'] <> "")?$mData[$cKey]['CLIAPE1X']:$vNomCli['CLIAPE1X'];
						$mData[$cKey]['CLIAPE2X']  = ($mData[$cKey]['CLIAPE2X'] <> "")?$mData[$cKey]['CLIAPE2X']:$vNomCli['CLIAPE2X'];
						$mData[$cKey]['CLINOM1X']  = ($mData[$cKey]['CLINOM1X'] <> "")?$mData[$cKey]['CLINOM1X']:$vNomCli['CLINOM1X'];
						$mData[$cKey]['CLINOM2X']  = ($mData[$cKey]['CLINOM2X'] <> "")?$mData[$cKey]['CLINOM2X']:$vNomCli['CLINOM2X'];
						$mData[$cKey]['CLINOMXX']  = ($mData[$cKey]['CLINOMXX'] <> "")?$mData[$cKey]['CLINOMXX']:(($vNomCli['TDIIDXXX'] == 31) ? $vNomCli['CLINOMXX'] : "");
						$mData[$cKey]['CLIDIRXX']  = ($mData[$cKey]['CLIDIRXX'] <> "")?$mData[$cKey]['CLIDIRXX']:$vNomCli['CLIDIRXX'];
						$mData[$cKey]['DEPIDXXX']  = ($mData[$cKey]['DEPIDXXX'] <> "")?$mData[$cKey]['DEPIDXXX']:$vNomCli['DEPIDXXX'];
						$mData[$cKey]['CIUIDXXX']  = ($mData[$cKey]['CIUIDXXX'] <> "")?$mData[$cKey]['CIUIDXXX']:$vNomCli['CIUIDXXX'];
						$mData[$cKey]['PAIIDXXX']  = ($mData[$cKey]['PAIIDXXX'] <> "")?$mData[$cKey]['PAIIDXXX']:$vNomCli['PAIIDXXX'];
						$mData[$cKey]['COMVLRXX']  = $mAux[$cKey]['COMVLRXX'];
						//Datos adicionales para terid2xx
						$mData[$cKey]['TDIIDXXC']  = ($mData[$cKey]['TDIIDXXC'] <> "")?$mData[$cKey]['TDIIDXXC']:$vNomCl2['TDIIDXXX'];
						$mData[$cKey]['terid2xx']  = ($mData[$cKey]['terid2xx'] <> "")?$mData[$cKey]['terid2xx']:$mAux[$cKey]['terid2xx'];
						$mData[$cKey]['CLIAPE1C']  = ($mData[$cKey]['CLIAPE1C'] <> "")?$mData[$cKey]['CLIAPE1C']:$vNomCl2['CLIAPE1X'];
						$mData[$cKey]['CLIAPE2C']  = ($mData[$cKey]['CLIAPE2C'] <> "")?$mData[$cKey]['CLIAPE2C']:$vNomCl2['CLIAPE2X'];
						$mData[$cKey]['CLINOM1C']  = ($mData[$cKey]['CLINOM1C'] <> "")?$mData[$cKey]['CLINOM1C']:$vNomCl2['CLINOM1X'];
						$mData[$cKey]['CLINOM2C']  = ($mData[$cKey]['CLINOM2C'] <> "")?$mData[$cKey]['CLINOM2C']:$vNomCl2['CLINOM2X'];
						$mData[$cKey]['CLINOMXC']  = ($mData[$cKey]['CLINOMXC'] <> "")?$mData[$cKey]['CLINOMXC']:(($vNomCl2['TDIIDXXX'] == 31) ? $vNomCl2['CLINOMXX'] : "");
						$mData[$cKey]['CLIDIRXC']  = ($mData[$cKey]['CLIDIRXC'] <> "")?$mData[$cKey]['CLIDIRXC']:$vNomCl2['CLIDIRXX'];
						$mData[$cKey]['DEPIDXXC']  = ($mData[$cKey]['DEPIDXXC'] <> "")?$mData[$cKey]['DEPIDXXC']:$vNomCl2['DEPIDXXX'];
						$mData[$cKey]['CIUIDXXC']  = ($mData[$cKey]['CIUIDXXC'] <> "")?$mData[$cKey]['CIUIDXXC']:$vNomCl2['CIUIDXXX'];
						$mData[$cKey]['PAIIDXXC']  = ($mData[$cKey]['PAIIDXXC'] <> "")?$mData[$cKey]['PAIIDXXC']:$vNomCl2['PAIIDXXX'];
					}
				}

			break;
			case "1054": // 1380250100
				#Buscando el nombre de la DIRECCION DE IMPUESTOS DE ADUANAS
				$qDatExt = "SELECT ";
				$qDatExt .= "$cAlfa.SIAI0150.CLINOMXX AS CLINOMDI ";
				$qDatExt .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"800197268\" LIMIT 0,1 ";
				$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
				if(mysql_num_rows($xDatExt) > 0) {
					$xRDE = mysql_fetch_array($xDatExt);
					$cNomAdu =  $xRDE['CLINOMDI'];
				}

				if ($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") {
					#Busco si el concepto contable es de pago de tributo
					$qPagTri  = "SELECT ";
					$qPagTri .= "ctoidxxx ";
					$qPagTri .= "FROM $cAlfa.fpar0119 ";
					$qPagTri .= "WHERE  ";
					$qPagTri .= "(ctoptaxg = \"SI\" OR ctoptaxl = \"SI\") AND ";
					$qPagTri .= "regestxx = \"ACTIVO\" ";
					$xPagTri  = f_MySql("SELECT","",$qPagTri,$xConexion01,"");
					//f_Mensaje(__FILE__,__LINE__,$qPagTri." ~ ".mysql_num_rows($xPagTri));
					$cPagTri = "";
					while ($xRPT = mysql_fetch_array($xPagTri)) {
						$cPagTri .= "{$xRPT['ctoidxxx']},";
					}
					$cPagTri = substr($cPagTri, 0, strlen($cPagTri)-1);
				}

				$qFpar117  = "SELECT comidxxx, comcodxx ";
				$qFpar117 .= "FROM $cAlfa.fpar0117 ";
				$qFpar117 .= "WHERE ";
				$qFpar117 .= "comtipxx  = \"RCM\"";
				$xFpar117  = f_MySql("SELECT","",$qFpar117,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qFpar117." ~ ".mysql_num_rows($xFpar117));
				$mRCM = array();
				while ($xRF117 = mysql_fetch_array($xFpar117)) {
					$mRCM[count($mRCM)] = "{$xRF117['comidxxx']}~{$xRF117['comcodxx']}";
				}

				#Buscando datos de detalle
				$qData  = "SELECT ";
				$qData .= "$cAlfa.fcod$nAno.comidxxx,";
				$qData .= "$cAlfa.fcod$nAno.comcodxx,";
				$qData .= "$cAlfa.fcod$nAno.comcscxx,";
				$qData .= "$cAlfa.fcod$nAno.pucidxxx,";
				$qData .= "$cAlfa.fcod$nAno.ctoidxxx,";
				$qData .= "$cAlfa.fcod$nAno.teridxxx,";
				$qData .= "$cAlfa.fcod$nAno.terid2xx,";
				//Comprobante cruce dos
				$qData .= "$cAlfa.fcod$nAno.comidc2x,";
				$qData .= "$cAlfa.fcod$nAno.comcodc2,";
				$qData .= "$cAlfa.fcod$nAno.comcscc2,";
				$qData .= "$cAlfa.fcod$nAno.comseqc2,";
				//$qData .= "SUM($cAlfa.fcod$nAno.comvlr02) AS comvlr02,";
				//Concatenando consecutivo Dos, para el caso de los comprobantes de caja menor
				$qData .= "GROUP_CONCAT(CONCAT(comidc2x,\"-\",comcodc2,\"-\",comcscc2,\"-\",comseqc2) SEPARATOR \"~\") AS cajameno,";
				//sumatoria valores
				$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlr02,$cAlfa.fcod$nAno.comvlr02*-1)) AS comvlr02,";
				$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlr01,$cAlfa.fcod$nAno.comvlr01*-1)) AS comvlr01,";
				$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlrxx,$cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx,";
				$qData .= "$cAlfa.fcod$nAno.teridxxx AS CLIIDXXC ";
				$qData .= "FROM $cAlfa.fcod$nAno ";
				$qData .= "WHERE ";
				$qData .= "$cAlfa.fcod$nAno.comidxxx NOT IN (\"F\") AND ";
				$qData .= "$cAlfa.fcod$nAno.pucidxxx IN ($cCuenta) AND ";
				if (($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") && $cPagTri <> "") {
					$qData .= "$cAlfa.fcod$nAno.ctoidxxx NOT IN ($cPagTri) AND ";
				}
				$qData .= "$cAlfa.fcod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND ";
				$qData .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
				$qData .= "GROUP BY $cAlfa.fcod$nAno.terid2xx,$cAlfa.fcod$nAno.teridxxx,$cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
				$qData .= "ORDER BY $cAlfa.fcod$nAno.terid2xx,$cAlfa.fcod$nAno.teridxxx,$cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
				$xData  = f_MySql("SELECT","",$qData,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qData." ~ ".mysql_num_rows($xData));

				$mLisCli = array(); $mDatCli = array();
				while ($xDATA = mysql_fetch_array($xData)) {

					if(in_array($xDATA['terid2xx'],$mLisCli) == false) {
						#Trayendo datos terid2xx
						$qDatExt = "SELECT * ";
						$qDatExt .= "FROM $cAlfa.SIAI0150 ";
						$qDatExt .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$xDATA['terid2xx']}\" LIMIT 0,1 ";
						$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");

						if(mysql_num_rows($xDatExt) > 0) {
							$xRDE = mysql_fetch_array($xDatExt);
							if ($xRDE['CLIRECOM']=="SI") {
								$mDatCli[$xDATA['terid2xx']]['CLICATER'] = "COMUN";
							}
							if ($xRDE['CLIRESIM']=="SI") {
								$mDatCli[$xDATA['terid2xx']]['CLICATER'] = "SIMPLIFICADO";
							}
							if ($xRDE['CLIGCXXX']=="SI") {
								$mDatCli[$xDATA['terid2xx']]['CLICATER'] = "CONTRIBUYENTE";
							}
							if ($xRDE['CLINRPXX']=="SI") {
								$mDatCli[$xDATA['terid2xx']]['CLICATER'] = "NORESIDENTE";
							}
							//Busco el codigo del pais
							$qCodPai = "SELECT PAIIDNXX FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"{$xRDE['PAIIDXXX']}\" LIMIT 0,1";
							$xCodPai  = f_MySql("SELECT","",$qCodPai,$xConexion01,"");
							if (mysql_num_rows($xCodPai) > 0) {
								$xRCP = mysql_fetch_array($xCodPai);
								$xRDE['PAIIDXXX'] =  ($xRCP['PAIIDNXX'] != "") ? $xRCP['PAIIDNXX'] : $xRDE['PAIIDXXX'];
							}
							//Fin Busco el codigo del pais
							$mDatCli[$xDATA['terid2xx']]['TDIIDXXX'] = $xRDE['TDIIDXXX'];
							$mDatCli[$xDATA['terid2xx']]['CLIAPE1X'] = utf8_encode($xRDE['CLIAPE1X']);
							$mDatCli[$xDATA['terid2xx']]['CLIAPE2X'] = utf8_encode($xRDE['CLIAPE2X']);
							$mDatCli[$xDATA['terid2xx']]['CLINOM1X'] = utf8_encode($xRDE['CLINOM1X']);
							$mDatCli[$xDATA['terid2xx']]['CLINOM2X'] = utf8_encode($xRDE['CLINOM2X']);
							$mDatCli[$xDATA['terid2xx']]['CLINOMXX'] = ($xRDE['TDIIDXXX'] == 31) ? utf8_encode($xRDE['CLINOMXX']) : "";
							$mDatCli[$xDATA['terid2xx']]['CLIDIRXX'] = utf8_encode($xRDE['CLIDIRXX']);
							$mDatCli[$xDATA['terid2xx']]['DEPIDXXX'] = $xRDE['DEPIDXXX'];
							$mDatCli[$xDATA['terid2xx']]['CIUIDXXX'] = $xRDE['CIUIDXXX'];
							$mDatCli[$xDATA['terid2xx']]['PAIIDXXX'] = $xRDE['PAIIDXXX'];

							$mLisCli[] = $xDATA['terid2xx'];
						}
					}

					if(in_array($xDATA['teridxxx'],$mLisCli) == false) {
					#Trayendo datos teridxxx
						$qDatExt = "SELECT * ";
						$qDatExt .= "FROM $cAlfa.SIAI0150 ";
						$qDatExt .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$xDATA['teridxxx']}\" LIMIT 0,1 ";
						$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");

						if(mysql_num_rows($xDatExt) > 0) {
							$xRDE = mysql_fetch_array($xDatExt);

							if ($xRDE['CLIRECOM']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "COMUN";
							}
							if ($xRDE['CLIRESIM']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "SIMPLIFICADO";
							}
							if ($xRDE['CLIGCXXX']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "CONTRIBUYENTE";
							}
							if ($xRDE['CLINRPXX']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "NORESIDENTE";
							}
							$mDatCli[$xDATA['teridxxx']]['TDIIDXXX'] = $xRDE['TDIIDXXX'];
							$mDatCli[$xDATA['teridxxx']]['CLIAPE1X'] = utf8_encode($xRDE['CLIAPE1X']);
							$mDatCli[$xDATA['teridxxx']]['CLIAPE2X'] = utf8_encode($xRDE['CLIAPE2X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOM1X'] = utf8_encode($xRDE['CLINOM1X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOM2X'] = utf8_encode($xRDE['CLINOM2X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOMXX'] = ($xRDE['TDIIDXXX'] == 31) ? utf8_encode($xRDE['CLINOMXX']) : "";
							$mDatCli[$xDATA['teridxxx']]['CLIDIRXX'] = utf8_encode($xRDE['CLIDIRXX']);
							$mDatCli[$xDATA['teridxxx']]['DEPIDXXX'] = $xRDE['DEPIDXXX'];
							$mDatCli[$xDATA['teridxxx']]['CIUIDXXX'] = $xRDE['CIUIDXXX'];
							$mDatCli[$xDATA['teridxxx']]['PAIIDXXX'] = $xRDE['PAIIDXXX'];

							$mLisCli[] = $xDATA['teridxxx'];
						}
					}
					$xDATA['CLINOMDI'] = $cNomAdu;

					//Para los comprobantes de Reembolso de caja Menor trae el iva del Recibo de Caja Menor
					//Busco el comprobante cruce dos en recibos de caja menor
					if ($xDATA['cajameno'] != "" && $xDATA['comvlr02'] == 0 && in_array("{$xDATA['comidxxx']}~{$xDATA['comcodxx']}", $mRCM) == true) {
						$vRecCaja  = explode("~",$xDATA['cajameno']);
						$cRecCaja  = "";
						for ($nRC=0; $nRC<count($vRecCaja); $nRC++) {
							$cRecCaja .= ($vRecCaja[$nRC] != "") ? "\"{$vRecCaja[$nRC]}\"," : "";
						}
						$cRecCaja = substr($cRecCaja, 0, strlen($cRecCaja)-1);

						if ($cRecCaja != "") {
							$qRecCaja  = "SELECT SUM(IF(commovxx=\"D\",comvlr02,comvlr02*-1)) AS comvlr02 ";
							$qRecCaja .= "FROM $cAlfa.fcme$nAno ";
							$qRecCaja .= "WHERE ";
							$qRecCaja .= "CONCAT(comidxxx,\"-\",comcodxx,\"-\",comcscxx,\"-\",comseqxx) IN ($cRecCaja)";
							$xRecCaja  = f_MySql("SELECT","",$qRecCaja,$xConexion01,"");
							//f_Mensaje(__FILE__,__LINE__,$qRecCaja." ~ ".mysql_num_rows($xRecCaja));
							if (mysql_num_rows($xRecCaja) > 0) {
								$xRRC = mysql_fetch_array($xRecCaja);
								$xDATA['comvlr02'] = $xRRC['comvlr02'];
							}
						}
					}

					if ($xDATA['comvlr01'] == 0) {
					switch ($cAlfa) {
						case "COLMASXX":
						case "DECOLMASXX":
						case "TECOLMASXX":
						$xDATA['comvlr01'] = $xDATA['comvlrxx'];
						$xDATA['comvlr02'] = 0;
						break;
						default:
							$nCal = 0;
							if ($xDATA['comidxxx'] == "G" || $xDATA['comidxxx'] == "L") {
									#Busco si el concepto contable es de pago de tributo
									$qPagTri  = "SELECT ";
									$qPagTri .= "ctoptaxg, ";
									$qPagTri .= "ctoptaxl  ";
									$qPagTri .= "FROM $cAlfa.fpar0119 ";
									$qPagTri .= "WHERE  ";
									$qPagTri .= "pucidxxx = \"{$xDATA['pucidxxx']}\" AND  ";
									$qPagTri .= "ctoidxxx = \"{$xDATA['ctoidxxx']}\" LIMIT 0,1  ";
									$xPagTri  = f_MySql("SELECT","",$qPagTri,$xConexion01,"");
									if (mysql_num_rows($xPagTri) > 0) {
										$xRPT = mysql_fetch_array($xPagTri);
										if($xDATA['comidxxx'] == "G" && $xRPT['ctoptaxg'] == "SI") {
											$xDATA['comvlr01'] = $xDATA['comvlrxx'];
											$xDATA['comvlr02'] = 0;
											$nCal = 1;
										}

									if($xDATA['comidxxx'] == "L" && $xRPT['ctoptaxl'] == "SI") {
											$xDATA['comvlr01'] = $xDATA['comvlrxx'];
											$xDATA['comvlr02'] = 0;
											$nCal = 1;
										}
									}
							}
							if ($nCal == 0) {
								switch ($cAlfa) {
									case "GRUPOGLA": case "DEGRUPOGLA": case "TEGRUPOGLA":
									case "ADUACARX": case "DEADUACARX": case "TEADUACARX":
										#Si el comvrl01x es cero calculo la base
										$xDATA['comvlr01'] = ($xDATA['comvlrxx']/1.16);
										$xDATA['comvlr02'] = ($xDATA['comvlr01']*0.16);
									break;
									default:
										#No se hace nada Se envia lo digitado en la grilla
									break;
								}
							}
						break;
					}
					}

					#Validacion para ADUACARGA, se excluye el registro si este tienen valor cero en las siguientes columnas:
					#Impuesto descontable
					#IVA resultante por devoluciones en ventas anuladas rescindidas o resueltas (esta siempre tiene valor cero)
					$nIncluir = 0;
					if ($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") {
						$nIncluir = ($xDATA['comvlr02'] == 0) ? 1 : 0;
					}

					if ($nIncluir == 0) {
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['TDIIDXXX']  = ($mData[$xDATA['terid2xx']]['TDIIDXXX'] <> "")?$mData[$xDATA['terid2xx']]['TDIIDXXX']:$mDatCli[$xDATA['terid2xx']]['TDIIDXXX'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['terid2xx']  = ($mData[$xDATA['terid2xx']]['terid2xx'] <> "")?$mData[$xDATA['terid2xx']]['terid2xx']:$xDATA['terid2xx'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['terid2di']  = ($mData[$xDATA['terid2xx']]['terid2di'] <> "")?$mData[$xDATA['terid2xx']]['terid2di']:$xDATA['terid2xx'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLIAPE1X']  = ($mData[$xDATA['terid2xx']]['CLIAPE1X'] <> "")?$mData[$xDATA['terid2xx']]['CLIAPE1X']:$mDatCli[$xDATA['terid2xx']]['CLIAPE1X'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLIAPE2X']  = ($mData[$xDATA['terid2xx']]['CLIAPE2X'] <> "")?$mData[$xDATA['terid2xx']]['CLIAPE2X']:$mDatCli[$xDATA['terid2xx']]['CLIAPE2X'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLINOM1X']  = ($mData[$xDATA['terid2xx']]['CLINOM1X'] <> "")?$mData[$xDATA['terid2xx']]['CLINOM1X']:$mDatCli[$xDATA['terid2xx']]['CLINOM1X'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLINOM2X']  = ($mData[$xDATA['terid2xx']]['CLINOM2X'] <> "")?$mData[$xDATA['terid2xx']]['CLINOM2X']:$mDatCli[$xDATA['terid2xx']]['CLINOM2X'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLINOMXX']  = ($mData[$xDATA['terid2xx']]['CLINOMXX'] <> "")?$mData[$xDATA['terid2xx']]['CLINOMXX']:$mDatCli[$xDATA['terid2xx']]['CLINOMXX'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLIDIRXX']  = ($mData[$xDATA['terid2xx']]['CLIDIRXX'] <> "")?$mData[$xDATA['terid2xx']]['CLIDIRXX']:$mDatCli[$xDATA['terid2xx']]['CLIDIRXX'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['DEPIDXXX']  = ($mData[$xDATA['terid2xx']]['DEPIDXXX'] <> "")?$mData[$xDATA['terid2xx']]['DEPIDXXX']:$mDatCli[$xDATA['terid2xx']]['DEPIDXXX'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CIUIDXXX']  = ($mData[$xDATA['terid2xx']]['CIUIDXXX'] <> "")?$mData[$xDATA['terid2xx']]['CIUIDXXX']:$mDatCli[$xDATA['terid2xx']]['CIUIDXXX'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['PAIIDXXX']  = ($mData[$xDATA['terid2xx']]['PAIIDXXX'] <> "")?$mData[$xDATA['terid2xx']]['PAIIDXXX']:$mDatCli[$xDATA['terid2xx']]['PAIIDXXX'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['TDIIDXXC']  = ($mData[$xDATA['terid2xx']]['TDIIDXXC'] <> "")?$mData[$xDATA['terid2xx']]['TDIIDXXC']:$mDatCli[$xDATA['teridxxx']]['TDIIDXXX'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLIIDXXC']  = ($mData[$xDATA['terid2xx']]['CLIIDXXC'] <> "")?$mData[$xDATA['terid2xx']]['CLIIDXXC']:$xDATA['teridxxx'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLIAPE1C']  = ($mData[$xDATA['terid2xx']]['CLIAPE1C'] <> "")?$mData[$xDATA['terid2xx']]['CLIAPE1C']:$mDatCli[$xDATA['teridxxx']]['CLIAPE1X'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLIAPE2C']  = ($mData[$xDATA['terid2xx']]['CLIAPE2C'] <> "")?$mData[$xDATA['terid2xx']]['CLIAPE2C']:$mDatCli[$xDATA['teridxxx']]['CLIAPE2X'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLINOM1C']  = ($mData[$xDATA['terid2xx']]['CLINOM1C'] <> "")?$mData[$xDATA['terid2xx']]['CLINOM1C']:$mDatCli[$xDATA['teridxxx']]['CLINOM1X'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLINOM2C']  = ($mData[$xDATA['terid2xx']]['CLINOM2C'] <> "")?$mData[$xDATA['terid2xx']]['CLINOM2C']:$mDatCli[$xDATA['teridxxx']]['CLINOM2X'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLINOMXC']  = ($mData[$xDATA['terid2xx']]['CLINOMXC'] <> "")?$mData[$xDATA['terid2xx']]['CLINOMXC']:$mDatCli[$xDATA['teridxxx']]['CLINOMXX'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['IMPDXXXX'] += $xDATA['comvlr02'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['IVARXXXX']  = 0;
					}
				}
			break;
			case "5247": // Nuevo 5247 ~ Pagos o Retenciones

				#Creando tabla temporal de cuentas 2408, 2365, 531520 y 2367
				$cFcoc = "fcod".$nAno;
				$cTabFac = fnCadenaAleatoria();
				$qNewTab  = "CREATE TEMPORARY TABLE IF NOT EXISTS $cAlfa.$cTabFac LIKE $cAlfa.$cFcoc";
				$xNewTab = mysql_query($qNewTab,$xConexion01);

				//Buscando las cuentas de retencion CREE
				$qRetCree  = "SELECT CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx ";
				$qRetCree .= "FROM $cAlfa.fpar0115 ";
				$qRetCree .= "WHERE ";
				$qRetCree .= "pucgruxx LIKE \"23\" AND ";
				$qRetCree .= "pucterxx LIKE \"R\"  AND ";
				$qRetCree .= "pucdesxx LIKE \"%CREE%\" AND ";
				$qRetCree .= "pucdesxx NOT LIKE \"%AUTO%\" AND ";
				$qRetCree .= "regestxx = \"ACTIVO\" ";
				$xRetCree  = f_MySql("SELECT","",$qRetCree,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qRetCree."~".mysql_num_rows($xRetCree));
				$mRetCree = array();
				while ($xRRC = mysql_fetch_array($xRetCree)){
					$mRetCree[count($mRetCree)] = $xRRC['pucidxxx'];
				}

				$cReteCree = "";
				for($nRC=0; $nRC<count($mRetCree); $nRC++) {
					$cReteCree .= "$cAlfa.fcod$nAno.pucidxxx LIKE \"{$mRetCree[$nRC]}\" OR ";
				}
				$cReteCree = substr($cReteCree, 0, strlen($cReteCree)-4);

				$qFcod  = "SELECT * ";
				$qFcod .= "FROM $cAlfa.fcod$nAno ";
				$qFcod .= "WHERE ";
				$qFcod .= "($cAlfa.fcod$nAno.pucidxxx LIKE \"2408%\" OR ";
				$qFcod .= "SUBSTRING($cAlfa.fcod$nAno.pucidxxx,1,4) IN ($cCueRetFue) OR ";
				$qFcod .= "SUBSTRING($cAlfa.fcod$nAno.pucidxxx,1,6) IN ($cCueImpAsu) OR ";
				$qFcod .= "SUBSTRING($cAlfa.fcod$nAno.pucidxxx,1,4) IN ($cCueRetIva)".(($cReteCree != "") ? " OR $cReteCree": "").") AND ";
				$qFcod .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";

				$qInsert = "INSERT INTO $cAlfa.$cTabFac $qFcod";
				$xInsert = mysql_query($qInsert,$xConexion01);
				#Fin Creando tabla temporal de facturas cabecera

				#Buscando el nombre de la DIRECCION DE IMPUESTOS DE ADUANAS
				$qDatExt = "SELECT ";
				$qDatExt .= "$cAlfa.SIAI0150.CLINOMXX AS CLINOMDI ";
				$qDatExt .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"800197268\" LIMIT 0,1 ";
				$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
				if(mysql_num_rows($xDatExt) > 0) {
					$xRDE = mysql_fetch_array($xDatExt);
					$cNomAdu =  $xRDE['CLINOMDI'];
				}

				#Busco si el concepto contable es de anticipo
				$qCtoAnt  = "SELECT ";
				$qCtoAnt .= "ctoidxxx ";
				$qCtoAnt .= "FROM $cAlfa.fpar0119 ";
				$qCtoAnt .= "WHERE  ";
				$qCtoAnt .= "ctoantxx = \"SI\" AND ";
				$qCtoAnt .= "regestxx = \"ACTIVO\" ";
				$xCtoAnt  = f_MySql("SELECT","",$qCtoAnt,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qCtoAnt." ~ ".mysql_num_rows($xCtoAnt));
				$cCtoAnt = "";
				while ($xRPT = mysql_fetch_array($xCtoAnt)) {
					$cCtoAnt .= "{$xRPT['ctoidxxx']},";
				}
				$cCtoAnt = substr($cCtoAnt, 0, strlen($cCtoAnt)-1);

				if ($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") {
					#Busco si el concepto contable es de pago de tributo
					$qPagTri  = "SELECT ";
					$qPagTri .= "ctoidxxx ";
					$qPagTri .= "FROM $cAlfa.fpar0119 ";
					$qPagTri .= "WHERE  ";
					$qPagTri .= "(ctoptaxg = \"SI\" OR ctoptaxl = \"SI\") AND ";
					$qPagTri .= "regestxx = \"ACTIVO\" ";
					$xPagTri  = f_MySql("SELECT","",$qPagTri,$xConexion01,"");
					//f_Mensaje(__FILE__,__LINE__,$qPagTri." ~ ".mysql_num_rows($xPagTri));
					$cPagTri = "";
					while ($xRPT = mysql_fetch_array($xPagTri)) {
						$cPagTri .= "{$xRPT['ctoidxxx']},";
					}
					$cPagTri = substr($cPagTri, 0, strlen($cPagTri)-1);

					//Buscando las L que no son ajustes
					$qCarBa  = "SELECT ";
					$qCarBa .= "CONCAT(comidxxx,\"~\",comcodxx) AS comidxxx ";
					$qCarBa .= "FROM $cAlfa.fpar0117 ";
					$qCarBa .= "WHERE ";
					$qCarBa .= "comidxxx = \"L\" AND ";
					$qCarBa .= "comtipxx != \"AJUSTES\" ";
					$xCarBa = f_MySql("SELECT","",$qCarBa,$xConexion01,"");
					//f_Mensaje(__FILE__,__LINE__,$qCarBa." ~ ".mysql_num_rows($xCarBa));
					$vCarBa = "";
					while ($xRCB = mysql_fetch_array($xCarBa)) {
						$vCarBa[] = $xRCB['comidxxx'];
					}
				}

				$qFpar117  = "SELECT comidxxx, comcodxx ";
				$qFpar117 .= "FROM $cAlfa.fpar0117 ";
				$qFpar117 .= "WHERE ";
				$qFpar117 .= "comtipxx  = \"RCM\"";
				$xFpar117  = f_MySql("SELECT","",$qFpar117,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qFpar117." ~ ".mysql_num_rows($xFpar117));
				$mRCM = array();
				while ($xRF117 = mysql_fetch_array($xFpar117)) {
					$mRCM[count($mRCM)] = "{$xRF117['comidxxx']}~{$xRF117['comcodxx']}";
				}

				/**
					* Ticket 22351: Buscando los comprobantes de nota credito para COLMAS, estos comprobantes deben excluirse
					* Ajuste: 2016-03-10 10:14 se incluye cambio para excluir las notas credito para todas las agencias de aduana
					*/
				$cNotCre  = "";
				switch ($cAlfa) {
					case "COLMASXX":
					case "DECOLMASXX":
					case "TECOLMASXX":
						$cNotCre .= "\"L~044\",";
						$cNotCre .= "\"L~024\",";
						$cNotCre .= "\"L~020\",";
						$cNotCre .= "\"L~016\",";
						$cNotCre .= "\"C~001\",";
						$cNotCre .= "\"C~002\",";
						$cNotCre .= "\"C~003\",";
						$cNotCre .= "\"C~004\",";
					break;
					default:
						//No hace nada
					break;
				}
				$qNotCre  = "SELECT ";
				$qNotCre .= "CONCAT(comidxxx,\"~\",comcodxx) AS comidxxx ";
				$qNotCre .= "FROM $cAlfa.fpar0117 ";
				$qNotCre .= "WHERE ";
				$qNotCre .= "comidxxx = \"C\" AND ";
				$qNotCre .= "comtipxx != \"AJUSTES\" ";
				$xNotCre = f_MySql("SELECT","",$qNotCre,$xConexion01,"");
				while ($xRDB = mysql_fetch_array($xNotCre)) {
					$cNotCre .= "\"{$xRDB['comidxxx']}\",";
				}
				$cNotCre = substr($cNotCre,0,strlen($cNotCre)-1);

				#Buscando datos de detalle
				$qData  = "SELECT ";
				$qData .= "$cAlfa.fcod$nAno.comidxxx,";
				$qData .= "$cAlfa.fcod$nAno.comcodxx,";
				$qData .= "$cAlfa.fcod$nAno.comcscxx,";
				$qData .= "$cAlfa.fcod$nAno.pucidxxx,";
				$qData .= "$cAlfa.fcod$nAno.ctoidxxx,";
				$qData .= "$cAlfa.fcod$nAno.teridxxx,";
				$qData .= "$cAlfa.fcod$nAno.terid2xx,";
				//Comprobante cruce dos
				$qData .= "$cAlfa.fcod$nAno.comidc2x,";
				$qData .= "$cAlfa.fcod$nAno.comcodc2,";
				$qData .= "$cAlfa.fcod$nAno.comcscc2,";
				$qData .= "$cAlfa.fcod$nAno.comseqc2,";
				//$qData .= "SUM($cAlfa.fcod$nAno.comvlr02) AS comvlr02,";
				$qData .= "GROUP_CONCAT(CONCAT(comidc2x,\"-\",comcodc2,\"-\",comcscc2,\"-\",comseqc2) SEPARATOR \"~\") AS cajameno,";
				/**
					* Sumatoria valores
					* Para COLMAS, GLA y ADUACARGA se mantiene igual, ya que ellos tienen su propia logica,
					* para las demas agencias
					* si la base es cero y el iva es cero, la base debe ser igual al valor del comprobante
					*/
				switch ($cAlfa) {
					case "COLMASXX": case "DECOLMASXX": case "TECOLMASXX":
					case "GRUPOGLA": case "DEGRUPOGLA": case "TEGRUPOGLA":
					case "ADUACARX": case "DEADUACARX": case "TEADUACARX":
						$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlr02,$cAlfa.fcod$nAno.comvlr02*-1)) AS comvlr02,";
						$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlr01,$cAlfa.fcod$nAno.comvlr01*-1)) AS comvlr01,";
						$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlrxx,$cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx,";
					break;
					default:
						$nAsiBas = "IF($cAlfa.fcod$nAno.comvlr01 = 0 AND $cAlfa.fcod$nAno.comvlr02 = 0, $cAlfa.fcod$nAno.comvlrxx, $cAlfa.fcod$nAno.comvlr01)";
						$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlr02,$cAlfa.fcod$nAno.comvlr02*-1)) AS comvlr02,";
						$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$nAsiBas,$nAsiBas*-1)) AS comvlr01,";
						$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlrxx,$cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx,";
					break;
				}
				$qData .= "$cAlfa.fcod$nAno.teridxxx AS CLIIDXXC ";
				$qData .= "FROM $cAlfa.fcod$nAno ";
				$qData .= "LEFT JOIN $cAlfa.fcoc$nAno ON $cAlfa.fcod$nAno.comidxxx = $cAlfa.fcoc$nAno.comidxxx AND $cAlfa.fcod$nAno.comcodxx = $cAlfa.fcoc$nAno.comcodxx AND $cAlfa.fcod$nAno.comcscxx = $cAlfa.fcoc$nAno.comcscxx AND $cAlfa.fcod$nAno.comcsc2x = $cAlfa.fcoc$nAno.comcsc2x ";
				$qData .= "WHERE ";
		
				$qData .= "$cAlfa.fcod$nAno.comidxxx != \"F\"          AND ";
				$qData .= "$cAlfa.fcoc$nAno.comintpa != \"SI\"         AND ";
				$qData .= "$cAlfa.fcod$nAno.pucidxxx IN ($cCuenta)     AND ";
				$qData .= "$cAlfa.fcod$nAno.ctoidxxx NOT IN ($cCtoAnt) AND ";
				if (($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") && $cPagTri <> "") {
					$qData .= "$cAlfa.fcod$nAno.ctoidxxx NOT IN ($cPagTri) AND ";
				}
				/**
					* Ticket 22351: Buscando los comprobantes de nota credito para COLMAS, estos comprobantes deben excluirse
					* Ajuste: 2016-03-10 10:14 se incluye cambio para excluir las notas credito para todas las agencias de aduana
					*/
				if ($cNotCre != "") {
					$qData .= "CONCAT($cAlfa.fcod$nAno.comidxxx,\"~\",$cAlfa.fcod$nAno.comcodxx) NOT IN ($cNotCre) AND ";
				}
				$qData .= "$cAlfa.fcod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND ";
				$qData .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
				$qData .= "GROUP BY $cAlfa.fcod$nAno.terid2xx,$cAlfa.fcod$nAno.teridxxx,$cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
				$qData .= "ORDER BY $cAlfa.fcod$nAno.terid2xx,$cAlfa.fcod$nAno.teridxxx,$cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
				$xData  = f_MySql("SELECT","",$qData,$xConexion01,"");
				// f_Mensaje(__FILE__,__LINE__,$qData." ~ ".mysql_num_rows($xData));

				$mLisCli = array(); $mDatCli = array();
				while ($xDATA = mysql_fetch_array($xData)) {

					if(in_array($xDATA['terid2xx'],$mLisCli) == false) {
						#Trayendo datos terid2xx
						$xDatExt  = array();
						$xRDE     = array();
						$qDatExt  = "SELECT * ";
						$qDatExt .= "FROM $cAlfa.SIAI0150 ";
						$qDatExt .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$xDATA['terid2xx']}\" LIMIT 0,1 ";
						$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");

						if(mysql_num_rows($xDatExt) > 0) {
							$xRDE = mysql_fetch_array($xDatExt);
							if ($xRDE['CLIRECOM']=="SI") {
								$mDatCli[$xDATA['terid2xx']]['CLICATER'] = "COMUN";
							}
							if ($xRDE['CLIRESIM']=="SI") {
								$mDatCli[$xDATA['terid2xx']]['CLICATER'] = "SIMPLIFICADO";
							}
							if ($xRDE['CLIGCXXX']=="SI") {
								$mDatCli[$xDATA['terid2xx']]['CLICATER'] = "CONTRIBUYENTE";
							}
							if ($xRDE['CLINRPXX']=="SI") {
								$mDatCli[$xDATA['terid2xx']]['CLICATER'] = "NORESIDENTE";
							}
							//Busco el codigo del pais
							$qCodPai = "SELECT PAIIDNXX FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"{$xRDE['PAIIDXXX']}\" LIMIT 0,1";
							$xCodPai  = f_MySql("SELECT","",$qCodPai,$xConexion01,"");
							if (mysql_num_rows($xCodPai) > 0) {
								$xRCP = mysql_fetch_array($xCodPai);
								$xRDE['PAIIDXXX'] =  ($xRCP['PAIIDNXX'] != "") ? $xRCP['PAIIDNXX'] : $xRDE['PAIIDXXX'];
							}
							//Fin Busco el codigo del pais
							$mDatCli[$xDATA['terid2xx']]['TDIIDXXX'] = $xRDE['TDIIDXXX'];
							$mDatCli[$xDATA['terid2xx']]['CLIAPE1X'] = utf8_encode($xRDE['CLIAPE1X']);
							$mDatCli[$xDATA['terid2xx']]['CLIAPE2X'] = utf8_encode($xRDE['CLIAPE2X']);
							$mDatCli[$xDATA['terid2xx']]['CLINOM1X'] = utf8_encode($xRDE['CLINOM1X']);
							$mDatCli[$xDATA['terid2xx']]['CLINOM2X'] = utf8_encode($xRDE['CLINOM2X']);
							$mDatCli[$xDATA['terid2xx']]['CLINOMXX'] = ($xRDE['TDIIDXXX'] == 31) ? utf8_encode($xRDE['CLINOMXX']) : "";
							$mDatCli[$xDATA['terid2xx']]['CLIDIRXX'] = utf8_encode($xRDE['CLIDIRXX']);
							$mDatCli[$xDATA['terid2xx']]['DEPIDXXX'] = $xRDE['DEPIDXXX'];
							$mDatCli[$xDATA['terid2xx']]['CIUIDXXX'] = $xRDE['CIUIDXXX'];
							$mDatCli[$xDATA['terid2xx']]['PAIIDXXX'] = $xRDE['PAIIDXXX'];

							$mLisCli[] = $xDATA['terid2xx'];
						}
					}

					if(in_array($xDATA['teridxxx'],$mLisCli) == false) {
						#Trayendo datos teridxxx
						$xDatExt = array();
						$xRDE    = array();
						$qDatExt = "SELECT * ";
						$qDatExt .= "FROM $cAlfa.SIAI0150 ";
						$qDatExt .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$xDATA['teridxxx']}\" LIMIT 0,1 ";
						$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");

						if(mysql_num_rows($xDatExt) > 0) {
							$xRDE = mysql_fetch_array($xDatExt);
							if ($xRDE['CLIRECOM']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "COMUN";
							}
							if ($xRDE['CLIRESIM']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "SIMPLIFICADO";
							}
							if ($xRDE['CLIGCXXX']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "CONTRIBUYENTE";
							}
							if ($xRDE['CLINRPXX']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "NORESIDENTE";
							}

							//Busco el codigo del pais
							$qCodPai = "SELECT PAIIDNXX FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"{$xRDE['PAIIDXXX']}\" LIMIT 0,1";
							$xCodPai  = f_MySql("SELECT","",$qCodPai,$xConexion01,"");
							if (mysql_num_rows($xCodPai) > 0) {
								$xRCP = mysql_fetch_array($xCodPai);
								$xRDE['PAIIDXXX'] =  ($xRCP['PAIIDNXX'] != "") ? $xRCP['PAIIDNXX'] : $xRDE['PAIIDXXX'];
							}
							//Fin Busco el codigo del pais

							$mDatCli[$xDATA['teridxxx']]['TDIIDXXX'] = $xRDE['TDIIDXXX'];
							$mDatCli[$xDATA['teridxxx']]['CLIAPE1X'] = utf8_encode($xRDE['CLIAPE1X']);
							$mDatCli[$xDATA['teridxxx']]['CLIAPE2X'] = utf8_encode($xRDE['CLIAPE2X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOM1X'] = utf8_encode($xRDE['CLINOM1X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOM2X'] = utf8_encode($xRDE['CLINOM2X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOMXX'] = ($xRDE['TDIIDXXX'] == 31) ? utf8_encode($xRDE['CLINOMXX']) : "";
							$mDatCli[$xDATA['teridxxx']]['CLIDIRXX'] = utf8_encode($xRDE['CLIDIRXX']);
							$mDatCli[$xDATA['teridxxx']]['DEPIDXXX'] = $xRDE['DEPIDXXX'];
							$mDatCli[$xDATA['teridxxx']]['CIUIDXXX'] = $xRDE['CIUIDXXX'];
							$mDatCli[$xDATA['teridxxx']]['PAIIDXXX'] = $xRDE['PAIIDXXX'];

							$mLisCli[] = $xDATA['teridxxx'];
						}
					}
					$xDATA['CLINOMDI'] = $cNomAdu;

					$nPrac = 0; $nAsum = 0; $nComun = 0; $nSimpl = 0; $nDom = 0; $nCreeAsum = 0; $nCreePrac = 0;
					$cComId = "{$xDATA['teridxxx']}-{$xDATA['terid2xx']}-{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}";
					#Calculando valores:
					#Retencion en la fuente practicada renta
					#Retencion en la fuente asumida renta
					#Retencion en la fuente practicada iva regimen comun
					#Retencion en la fuente asumida iva regimen simp.
					#Retencion en la fuente practicada iva no domiciliados
					#Traigo las cuantas que empiezan por 2365,531520,2367 para le comprobante

					$qFcod  = "SELECT ";
					$qFcod .= "$cAlfa.$cTabFac.*, ";
					#para las cuentas 2365 y 2367 los creditos suman, los debitos restan
					$qFcod .= "IF($cAlfa.$cTabFac.commovxx=\"C\",$cAlfa.$cTabFac.comvlrxx,$cAlfa.$cTabFac.comvlrxx*-1) AS comvlrsu,";
					#para la cuenta 531520 los creditos restan, los debitos suman
					$qFcod .= "IF($cAlfa.$cTabFac.commovxx=\"C\",$cAlfa.$cTabFac.comvlrxx*-1,$cAlfa.$cTabFac.comvlrxx) AS comvlrre ";
					$qFcod .= "FROM $cAlfa.$cTabFac ";
					$qFcod .= "WHERE ";
					$qFcod .= "$cAlfa.$cTabFac.comidxxx = \"{$xDATA['comidxxx']}\" AND ";
					$qFcod .= "$cAlfa.$cTabFac.comcodxx = \"{$xDATA['comcodxx']}\" AND ";
					$qFcod .= "$cAlfa.$cTabFac.comcscxx = \"{$xDATA['comcscxx']}\" AND ";
					switch ($cAlfa) {
						case "ADUACARX":
						case "TEADUACARX":
						case "DEADUACARX":
							//Las validaciones de los terceros se hacen en el while
							$qFcod .= "(SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,4) IN ($cCueRetFue) OR SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,6) IN ($cCueImpAsu) OR SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,4) IN ($cCueRetIva)".(($cReteCree != "") ? " OR ".str_replace("fcod$nAno", $cTabFac, $cReteCree) : "").") AND ";
						break;
						default:
							$qFcod .= "(($cAlfa.$cTabFac.teridxxx = \"{$xDATA['terid2xx']}\" AND $cAlfa.$cTabFac.terid2xx = \"{$xDATA['teridxxx']}\") OR ($cAlfa.$cTabFac.teridxxx = \"{$xDATA['teridxxx']}\" AND $cAlfa.$cTabFac.terid2xx =  \"{$xDATA['terid2xx']}\")) AND ";
							$qFcod .= "(SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,4) IN ($cCueRetFue) OR ";
							$qFcod .= "SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,6) IN ($cCueImpAsu) OR ";
							$qFcod .= "SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,4) IN ($cCueRetIva)".(($cReteCree != "") ? " OR ".str_replace("fcod$nAno", $cTabFac, $cReteCree) : "").") AND ";
						break;
					}
					$qFcod .= "$cAlfa.$cTabFac.regestxx = \"ACTIVO\" ";
					$xFcod = mysql_query($qFcod,$xConexion01);
					//f_Mensaje(__FILE__,__LINE__,$qFcod." ~ ".mysql_num_rows($xFcod));

					if (mysql_num_rows($xFcod) > 0) {
					while($xRF = mysql_fetch_array($xFcod)) {
							$nIncRect = 0;
						//Validaciones de los terceros para ADUACARGA
							if ($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") {

								$nIncRect = 1;
								//Primera condicion: que los terceros del reistro analizado esten contenidos en los terceros de la retencion
								if (($xRF['teridxxx'] == $xDATA['terid2xx'] && $xRF['terid2xx'] == $xDATA['teridxxx']) ||
										($xRF['teridxxx'] == $xDATA['teridxxx'] && $xRF['terid2xx'] == $xDATA['terid2xx'])) {
									$nIncRect = 0;
								} else {
									//si el comprobante es una G y el terid2xx del reistro analizado este en el teridxxx de la retencion,
									//y el teridxxx del registro analizado este en el subcentro de costo de la retencion
									//y la cuenta de retencion empieza por 2365 o 2367
									if (($xRF['comidxxx'] == "G" || ($xRF['comidxxx'] == "L" && in_array($xRF['comidxxx']."~".$xRF['comcodxx'], $vCarBa) == true)) &&
												$xRF['teridxxx'] == $xDATA['terid2xx'] && $xRF['sccidxxx'] == $xDATA['teridxxx'] &&
												(in_array(substr($xRF['pucidxxx'],0,4), $vCueRetFue) || in_array(substr($xRF['pucidxxx'],0,4), $vCueRetIva))) {
										$nIncRect = 0;
									} else {
										//Se busca si el subcentro de costo de la retencion es un DO, se trae el importador
										$qDatDo = "SELECT cliidxxx FROM $cAlfa.sys00121 WHERE docidxxx = \"{$xRF['sccidxxx'] }\" LIMIT 0,1";
										$xDatDo  = f_MySql("SELECT","",$qDatDo,$xConexion01,"");
									//f_Mensaje(__FILE__,__LINE__,$qDatDo." ~ ".mysql_num_rows($xDatDo));

										//si el comprobante es una G y el terid2xx del reistro analizado este en el teridxxx de la retencion,
										//y el teridxxx del registro analizado es el importador del DO
										//y la cuenta empiece por 2365 o 2367
										if (mysql_num_rows($xDatDo) > 0) {
											$xRDD = mysql_fetch_array($xDatDo);
											if (($xRF['comidxxx'] == "G" || ($xRF['comidxxx'] == "L" && in_array($xRF['comidxxx']."~".$xRF['comcodxx'], $vCarBa) == true)) &&
													$xRF['teridxxx'] == $xDATA['terid2xx'] && $xDATA['teridxxx'] == $xRDD['cliidxxx'] &&
													(in_array(substr($xRF['pucidxxx'],0,4), $vCueRetFue) || in_array(substr($xRF['pucidxxx'],0,4), $vCueRetIva))) {
												$nIncRect = 0;
											}
										}
									}
								}
							}

							if ($nIncRect == 0) {

								//Verifico si no es una cuenta de retencion Cree
								if (in_array($xRF['pucidxxx'], $mRetCree) == true) {
										$nCreePrac += $xRF['comvlrsu'];
								} else {
									//Verifico si es un iva practicado
								if(in_array(substr($xRF['pucidxxx'],0,4), $vCueRetFue)) {
									$nPrac += $xRF['comvlrsu'];
								}
								//Verifico si es un iva asumido
								if(in_array(substr($xRF['pucidxxx'],0,6), $vCueImpAsu)) {
									$nAsum += $xRF['comvlrre'];
								}
								//Verifico el tipo de rentencion
								if(in_array(substr($xRF['pucidxxx'],0,4), $vCueRetIva)) {

										if($xRF['tertipxx'] == "CLIPROCX"){
										$cCliId = $xRF['teridxxx'];
									} else {
										$cCliId = $xRF['terid2xx'];
									}

									switch ($mDatCli[$cCliId]['CLICATER']) {
										case "CONTRIBUYENTE":
										case "COMUN":
										$nComun += $xRF['comvlrsu'];
										break;
										case "SIMPLIFICADO":
										$nSimpl += $xRF['comvlrsu'];
										break;
										case "NORESIDENTE":
										$nDom += $xRF['comvlrsu'];
										break;
									}
								} ## if(in_array(substr($xRF['pucidxxx'],0,4), $vCueRetIva)) { ##
								}
							}
						}
					}

					//Para los comprobantes de Reembolso de caja Menor trae el iva del Recibo de Caja Menor
					//Busco el comprobante cruce dos en recibos de caja menor
					if ($xDATA['cajameno'] != "" && $xDATA['comvlr02'] == 0 && in_array("{$xDATA['comidxxx']}~{$xDATA['comcodxx']}", $mRCM) == true) {
						$vRecCaja  = explode("~",$xDATA['cajameno']);
						$cRecCaja  = "";
						for ($nRC=0; $nRC<count($vRecCaja); $nRC++) {
							$cRecCaja .= ($vRecCaja[$nRC] != "") ? "\"{$vRecCaja[$nRC]}\"," : "";
						}
						$cRecCaja = substr($cRecCaja, 0, strlen($cRecCaja)-1);

						if ($cRecCaja != "") {
							$qRecCaja  = "SELECT SUM(IF(commovxx=\"D\",comvlr02,comvlr02*-1)) AS comvlr02 ";
							$qRecCaja .= "FROM $cAlfa.fcme$nAno ";
							$qRecCaja .= "WHERE ";
							$qRecCaja .= "CONCAT(comidxxx,\"-\",comcodxx,\"-\",comcscxx,\"-\",comseqxx) IN ($cRecCaja)";
							$xRecCaja  = f_MySql("SELECT","",$qRecCaja,$xConexion01,"");
							//f_Mensaje(__FILE__,__LINE__,$qRecCaja." ~ ".mysql_num_rows($xRecCaja));
							if (mysql_num_rows($xRecCaja) > 0) {
								$xRRC = mysql_fetch_array($xRecCaja);
								$xDATA['comvlr02'] = $xRRC['comvlr02'];
							}
						}
					}

					if ($xDATA['comvlr01'] == 0) {
					switch ($cAlfa) {
						case "COLMASXX":
						case "DECOLMASXX":
						case "TECOLMASXX":
							$xDATA['comvlr01'] = $xDATA['comvlrxx'];
						$xDATA['comvlr02'] = 0;
						break;
						default:
							$nCal = 0;
							if ($xDATA['comidxxx'] == "G" || $xDATA['comidxxx'] == "L") {
									#Busco si el concepto contable es de pago de tributo
									$qPagTri  = "SELECT ";
									$qPagTri .= "ctoptaxg, ";
									$qPagTri .= "ctoptaxl  ";
									$qPagTri .= "FROM $cAlfa.fpar0119 ";
									$qPagTri .= "WHERE  ";
									$qPagTri .= "pucidxxx = \"{$xDATA['pucidxxx']}\" AND  ";
									$qPagTri .= "ctoidxxx = \"{$xDATA['ctoidxxx']}\" LIMIT 0,1  ";
									$xPagTri  = f_MySql("SELECT","",$qPagTri,$xConexion01,"");
									if (mysql_num_rows($xPagTri) > 0) {
										$xRPT = mysql_fetch_array($xPagTri);
										if($xDATA['comidxxx'] == "G" && $xRPT['ctoptaxg'] == "SI") {
											$xDATA['comvlr01'] = $xDATA['comvlrxx'];
											$xDATA['comvlr02'] = 0;
											$nCal = 1;
										}

									if($xDATA['comidxxx'] == "L" && $xRPT['ctoptaxl'] == "SI") {
											$xDATA['comvlr01'] = $xDATA['comvlrxx'];
											$xDATA['comvlr02'] = 0;
											$nCal = 1;
										}
									}
							}
							if ($nCal == 0) {
								switch ($cAlfa) {
									case "GRUPOGLA": case "DEGRUPOGLA": case "TEGRUPOGLA":
									case "ADUACARX": case "DEADUACARX": case "TEADUACARX":
										#Si el comvrl01x es cero calculo la base
										$xDATA['comvlr01'] = ($xDATA['comvlrxx']/1.16);
										$xDATA['comvlr02'] = ($xDATA['comvlr01']*0.16);
									break;
									default:
										#Si el valor de la base e iva es cero, en la base se envia el valor del comprobante
										$xDATA['comvlr01'] = ($xDATA['comvlr01'] == 0 && $xDATA['comvlr02'] == 0) ? $xDATA['comvlrxx'] : $xDATA['comvlr01'];
									break;
								}
							}
						break;
					}
					}

					#Se incluye para ADUACARGA que en la columna IVA mayor valor del costo o gasto sea siempre de valor cero
					if ($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") {
						$xDATA['comvlr02'] = 0;
					}

					#Validacion para ADUACARGA, se excluye el registro si este tienen valor cero en las siguientes columnas:
					#Pago o abono en cta
					#IVA mayor valor del costo o gasto
					#Retencion en la fuente practicada renta
					#Retencion en la fuente asumida renta
					#Retencion en la fuente practicada iva regimen comun
					#Retencion en la fuente asumida iva regimen simp.
					#Retencion en la fuente practicada iva no domiciliados
					$nIncluir = 0;
					if ($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") {
						$nIncluir = ($xDATA['comvlr01'] == 0 && $xDATA['comvlr02'] == 0 && $nPrac == 0  && $nAsum == 0 && $nComun == 0 && $nSimpl == 0 && $nDom == 0) ? 1 : 0;
					}

					

					if ($nIncluir == 0) {
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['TDIIDXXX']  = ($mData[$xDATA['terid2xx']]['TDIIDXXX'] <> "")?$mData[$xDATA['terid2xx']]['TDIIDXXX']:$mDatCli[$xDATA['terid2xx']]['TDIIDXXX'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['terid2xx']  = ($mData[$xDATA['terid2xx']]['terid2xx'] <> "")?$mData[$xDATA['terid2xx']]['terid2xx']:$xDATA['terid2xx'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['terid2di']  = ($mData[$xDATA['terid2xx']]['terid2di'] <> "")?$mData[$xDATA['terid2xx']]['terid2di']:$xDATA['terid2xx'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLIAPE1X']  = ($mData[$xDATA['terid2xx']]['CLIAPE1X'] <> "")?$mData[$xDATA['terid2xx']]['CLIAPE1X']:$mDatCli[$xDATA['terid2xx']]['CLIAPE1X'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLIAPE2X']  = ($mData[$xDATA['terid2xx']]['CLIAPE2X'] <> "")?$mData[$xDATA['terid2xx']]['CLIAPE2X']:$mDatCli[$xDATA['terid2xx']]['CLIAPE2X'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLINOM1X']  = ($mData[$xDATA['terid2xx']]['CLINOM1X'] <> "")?$mData[$xDATA['terid2xx']]['CLINOM1X']:$mDatCli[$xDATA['terid2xx']]['CLINOM1X'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLINOM2X']  = ($mData[$xDATA['terid2xx']]['CLINOM2X'] <> "")?$mData[$xDATA['terid2xx']]['CLINOM2X']:$mDatCli[$xDATA['terid2xx']]['CLINOM2X'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLINOMXX']  = ($mData[$xDATA['terid2xx']]['CLINOMXX'] <> "")?$mData[$xDATA['terid2xx']]['CLINOMXX']:$mDatCli[$xDATA['terid2xx']]['CLINOMXX'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLIDIRXX']  = ($mData[$xDATA['terid2xx']]['CLIDIRXX'] <> "")?$mData[$xDATA['terid2xx']]['CLIDIRXX']:$mDatCli[$xDATA['terid2xx']]['CLIDIRXX'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['DEPIDXXX']  = ($mData[$xDATA['terid2xx']]['DEPIDXXX'] <> "")?$mData[$xDATA['terid2xx']]['DEPIDXXX']:$mDatCli[$xDATA['terid2xx']]['DEPIDXXX'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CIUIDXXX']  = ($mData[$xDATA['terid2xx']]['CIUIDXXX'] <> "")?$mData[$xDATA['terid2xx']]['CIUIDXXX']:$mDatCli[$xDATA['terid2xx']]['CIUIDXXX'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['PAIIDXXX']  = ($mData[$xDATA['terid2xx']]['PAIIDXXX'] <> "")?$mData[$xDATA['terid2xx']]['PAIIDXXX']:$mDatCli[$xDATA['terid2xx']]['PAIIDXXX'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['PAGOXXXX'] += $xDATA['comvlr01'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['IVAXXXXX'] += $xDATA['comvlr02'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['PRACXXXX'] += $nPrac;
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['ASUMXXXX'] += $nAsum;
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['COMUNXXX'] += $nComun;
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['NDOMXXXX'] += $nDom;
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['TDIIDXXC']  = ($mData[$xDATA['terid2xx']]['TDIIDXXC'] <> "")?$mData[$xDATA['terid2xx']]['TDIIDXXC']:$mDatCli[$xDATA['teridxxx']]['TDIIDXXX'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLIIDXXC']  = ($mData[$xDATA['terid2xx']]['CLIIDXXC'] <> "")?$mData[$xDATA['terid2xx']]['CLIIDXXC']:$xDATA['teridxxx'];
					}
				}
			
			break;
			case "5248": // Nuevo 5248 Ingresos
					
				#Buscando datos de detalle
				$qData  = "SELECT ";
				$qData .= "$cAlfa.fcod$nAno.comidxxx,";
				$qData .= "$cAlfa.fcod$nAno.comcodxx,";
				$qData .= "$cAlfa.fcod$nAno.comcscxx,";
				$qData .= "$cAlfa.fcod$nAno.pucidxxx,";
				$qData .= "$cAlfa.fcod$nAno.ctoidxxx,";
				$qData .= "$cAlfa.fcod$nAno.teridxxx AS CLIIDXXC,";
				$qData .= "$cAlfa.fcod$nAno.teridxxx,";
				$qData .= "$cAlfa.fcod$nAno.terid2xx,";
				$qData .= "$cAlfa.fcod$nAno.commovxx,";
				$qData .= "$cAlfa.fcod$nAno.comvlrxx ";
				$qData .= "FROM $cAlfa.fcod$nAno ";
				$qData .= "WHERE ";
				$qData .= "$cAlfa.fcod$nAno.pucidxxx IN ($cCuenta) AND ";
				$qData .= "$cAlfa.fcod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND ";
				$qData .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
				$qData .= "GROUP BY $cAlfa.fcod$nAno.terid2xx,$cAlfa.fcod$nAno.teridxxx ";
				$qData .= "ORDER BY $cAlfa.fcod$nAno.terid2xx,$cAlfa.fcod$nAno.teridxxx";
				$xData  = f_MySql("SELECT","",$qData,$xConexion01,"");
				// f_Mensaje(__FILE__,__LINE__,$qData." ~ ".mysql_num_rows($xData));

				$mLisCli = array(); $mDatCli = array();
				while ($xDATA = mysql_fetch_array($xData)) {
				
					if(in_array($xDATA['terid2xx'],$mLisCli) == false) {
						#Trayendo datos terid2xx
						$xDatExt  = array();
						$xRDE     = array();
						$qDatExt  = "SELECT * ";
						$qDatExt .= "FROM $cAlfa.SIAI0150 ";
						$qDatExt .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$xDATA['terid2xx']}\" LIMIT 0,1 ";
						$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");

						if(mysql_num_rows($xDatExt) > 0) {
							$xRDE = mysql_fetch_array($xDatExt);
					
							//Busco el codigo del pais
							$qCodPai = "SELECT PAIIDNXX FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"{$xRDE['PAIIDXXX']}\" LIMIT 0,1";
							$xCodPai  = f_MySql("SELECT","",$qCodPai,$xConexion01,"");
							if (mysql_num_rows($xCodPai) > 0) {
								$xRCP = mysql_fetch_array($xCodPai);
								$xRDE['PAIIDXXX'] =  ($xRCP['PAIIDNXX'] != "") ? $xRCP['PAIIDNXX'] : $xRDE['PAIIDXXX'];
							}
							//Fin Busco el codigo del pais
							$mDatCli[$xDATA['terid2xx']]['TDIIDXXX'] = $xRDE['TDIIDXXX'];
							$mDatCli[$xDATA['terid2xx']]['CLIAPE1X'] = utf8_encode($xRDE['CLIAPE1X']);
							$mDatCli[$xDATA['terid2xx']]['CLIAPE2X'] = utf8_encode($xRDE['CLIAPE2X']);
							$mDatCli[$xDATA['terid2xx']]['CLINOM1X'] = utf8_encode($xRDE['CLINOM1X']);
							$mDatCli[$xDATA['terid2xx']]['CLINOM2X'] = utf8_encode($xRDE['CLINOM2X']);
							$mDatCli[$xDATA['terid2xx']]['CLINOMXX'] = ($xRDE['TDIIDXXX'] == 31) ? utf8_encode($xRDE['CLINOMXX']) : "";	
							$mDatCli[$xDATA['terid2xx']]['PAIIDXXX'] = $xRDE['PAIIDXXX'];

							$mLisCli[] = $xDATA['terid2xx'];
						}
					}

					if(in_array($xDATA['teridxxx'],$mLisCli) == false) {
						#Trayendo datos teridxxx
						$xDatExt = array();
						$xRDE    = array();
						$qDatExt = "SELECT * ";
						$qDatExt .= "FROM $cAlfa.SIAI0150 ";
						$qDatExt .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$xDATA['teridxxx']}\" LIMIT 0,1 ";
						$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");

						if(mysql_num_rows($xDatExt) > 0) {
							$xRDE = mysql_fetch_array($xDatExt);
						
							//Busco el codigo del pais
							$qCodPai = "SELECT PAIIDNXX FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"{$xRDE['PAIIDXXX']}\" LIMIT 0,1";
							$xCodPai  = f_MySql("SELECT","",$qCodPai,$xConexion01,"");
							if (mysql_num_rows($xCodPai) > 0) {
								$xRCP = mysql_fetch_array($xCodPai);
								$xRDE['PAIIDXXX'] =  ($xRCP['PAIIDNXX'] != "") ? $xRCP['PAIIDNXX'] : $xRDE['PAIIDXXX'];
							}
							//Fin Busco el codigo del pais

							$mDatCli[$xDATA['teridxxx']]['TDIIDXXX'] = $xRDE['TDIIDXXX'];
							$mDatCli[$xDATA['teridxxx']]['CLIAPE1X'] = utf8_encode($xRDE['CLIAPE1X']);
							$mDatCli[$xDATA['teridxxx']]['CLIAPE2X'] = utf8_encode($xRDE['CLIAPE2X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOM1X'] = utf8_encode($xRDE['CLINOM1X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOM2X'] = utf8_encode($xRDE['CLINOM2X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOMXX'] = ($xRDE['TDIIDXXX'] == 31) ? utf8_encode($xRDE['CLINOMXX']) : "";
							$mDatCli[$xDATA['teridxxx']]['CLIDIRXX'] = utf8_encode($xRDE['CLIDIRXX']);
							$mDatCli[$xDATA['teridxxx']]['DEPIDXXX'] = $xRDE['DEPIDXXX'];
							$mDatCli[$xDATA['teridxxx']]['CIUIDXXX'] = $xRDE['CIUIDXXX'];
							$mDatCli[$xDATA['teridxxx']]['PAIIDXXX'] = $xRDE['PAIIDXXX'];

							$mLisCli[] = $xDATA['teridxxx'];
						}
					}
					
					$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['TDIIDXXX']  = ($mData[$xDATA['terid2xx']]['TDIIDXXX'] <> "")?$mData[$xDATA['terid2xx']]['TDIIDXXX']:$mDatCli[$xDATA['terid2xx']]['TDIIDXXX'];
					$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['terid2xx']  = ($mData[$xDATA['terid2xx']]['terid2xx'] <> "")?$mData[$xDATA['terid2xx']]['terid2xx']:$xDATA['terid2xx'];
					$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLIAPE1X']  = ($mData[$xDATA['terid2xx']]['CLIAPE1X'] <> "")?$mData[$xDATA['terid2xx']]['CLIAPE1X']:$mDatCli[$xDATA['terid2xx']]['CLIAPE1X'];
					$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLIAPE2X']  = ($mData[$xDATA['terid2xx']]['CLIAPE2X'] <> "")?$mData[$xDATA['terid2xx']]['CLIAPE2X']:$mDatCli[$xDATA['terid2xx']]['CLIAPE2X'];
					$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLINOM1X']  = ($mData[$xDATA['terid2xx']]['CLINOM1X'] <> "")?$mData[$xDATA['terid2xx']]['CLINOM1X']:$mDatCli[$xDATA['terid2xx']]['CLINOM1X'];
					$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLINOM2X']  = ($mData[$xDATA['terid2xx']]['CLINOM2X'] <> "")?$mData[$xDATA['terid2xx']]['CLINOM2X']:$mDatCli[$xDATA['terid2xx']]['CLINOM2X'];
					$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLINOMXX']  = ($mData[$xDATA['terid2xx']]['CLINOMXX'] <> "")?$mData[$xDATA['terid2xx']]['CLINOMXX']:$mDatCli[$xDATA['terid2xx']]['CLINOMXX'];
					$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['PAIIDXXX']  = ($mData[$xDATA['terid2xx']]['PAIIDXXX'] <> "")?$mData[$xDATA['terid2xx']]['PAIIDXXX']:$mDatCli[$xDATA['terid2xx']]['PAIIDXXX'];
					$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['TDIIDXXC']  = ($mData[$xDATA['terid2xx']]['TDIIDXXC'] <> "")?$mData[$xDATA['terid2xx']]['TDIIDXXC']:$mDatCli[$xDATA['teridxxx']]['TDIIDXXX'];
					$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLIIDXXC']  = ($mData[$xDATA['terid2xx']]['CLIIDXXC'] <> "")?$mData[$xDATA['terid2xx']]['CLIIDXXC']:$xDATA['teridxxx'];
					if($xDATA['commovxx'] == "C"){
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['IPROXXXX'] += $xDATA['comvlrxx']; 
					}elseif($xDATA['commovxx'] == "D"){
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['DEVXXXXX'] += $xDATA['comvlrxx']; 
					}
					$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['TDIIDXXC']  = ($mData[$xDATA['terid2xx']]['TDIIDXXC'] <> "")?$mData[$xDATA['terid2xx']]['TDIIDXXC']:$mDatCli[$xDATA['teridxxx']]['TDIIDXXX'];
					$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLIIDXXC']  = ($mData[$xDATA['terid2xx']]['CLIIDXXC'] <> "")?$mData[$xDATA['terid2xx']]['CLIIDXXC']:$xDATA['teridxxx'];
				
				}
			break;
			case "5249": // NUEVO 5249 ~ Iva Descontable 
				#Buscando el nombre de la DIRECCION DE IMPUESTOS DE ADUANAS
				$qDatExt = "SELECT ";
				$qDatExt .= "$cAlfa.SIAI0150.CLINOMXX AS CLINOMDI ";
				$qDatExt .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"800197268\" LIMIT 0,1 ";
				$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
				if(mysql_num_rows($xDatExt) > 0) {
					$xRDE = mysql_fetch_array($xDatExt);
					$cNomAdu =  $xRDE['CLINOMDI'];
				}

				if ($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") {
					#Busco si el concepto contable es de pago de tributo
					$qPagTri  = "SELECT ";
					$qPagTri .= "ctoidxxx ";
					$qPagTri .= "FROM $cAlfa.fpar0119 ";
					$qPagTri .= "WHERE  ";
					$qPagTri .= "(ctoptaxg = \"SI\" OR ctoptaxl = \"SI\") AND ";
					$qPagTri .= "regestxx = \"ACTIVO\" ";
					$xPagTri  = f_MySql("SELECT","",$qPagTri,$xConexion01,"");
					//f_Mensaje(__FILE__,__LINE__,$qPagTri." ~ ".mysql_num_rows($xPagTri));
					$cPagTri = "";
					while ($xRPT = mysql_fetch_array($xPagTri)) {
						$cPagTri .= "{$xRPT['ctoidxxx']},";
					}
					$cPagTri = substr($cPagTri, 0, strlen($cPagTri)-1);
				}

				$qFpar117  = "SELECT comidxxx, comcodxx ";
				$qFpar117 .= "FROM $cAlfa.fpar0117 ";
				$qFpar117 .= "WHERE ";
				$qFpar117 .= "comtipxx  = \"RCM\"";
				$xFpar117  = f_MySql("SELECT","",$qFpar117,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qFpar117." ~ ".mysql_num_rows($xFpar117));
				$mRCM = array();
				while ($xRF117 = mysql_fetch_array($xFpar117)) {
					$mRCM[count($mRCM)] = "{$xRF117['comidxxx']}~{$xRF117['comcodxx']}";
				}

				#Buscando datos de detalle
				$qData  = "SELECT ";
				$qData .= "$cAlfa.fcod$nAno.comidxxx,";
				$qData .= "$cAlfa.fcod$nAno.comcodxx,";
				$qData .= "$cAlfa.fcod$nAno.comcscxx,";
				$qData .= "$cAlfa.fcod$nAno.pucidxxx,";
				$qData .= "$cAlfa.fcod$nAno.ctoidxxx,";
				$qData .= "$cAlfa.fcod$nAno.teridxxx,";
				$qData .= "$cAlfa.fcod$nAno.terid2xx,";
				//Comprobante cruce dos
				$qData .= "$cAlfa.fcod$nAno.comidc2x,";
				$qData .= "$cAlfa.fcod$nAno.comcodc2,";
				$qData .= "$cAlfa.fcod$nAno.comcscc2,";
				$qData .= "$cAlfa.fcod$nAno.comseqc2,";
				//Concatenando consecutivo Dos, para el caso de los comprobantes de caja menor
				$qData .= "GROUP_CONCAT(CONCAT(comidc2x,\"-\",comcodc2,\"-\",comcscc2,\"-\",comseqc2) SEPARATOR \"~\") AS cajameno,";
				//sumatoria valores
				$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlr02,$cAlfa.fcod$nAno.comvlr02*-1)) AS comvlr02,";
				$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlr01,$cAlfa.fcod$nAno.comvlr01*-1)) AS comvlr01,";
				$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlrxx,$cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx,";
				$qData .= "$cAlfa.fcod$nAno.teridxxx AS CLIIDXXC ";
				$qData .= "FROM $cAlfa.fcod$nAno ";
				$qData .= "WHERE ";
				$qData .= "$cAlfa.fcod$nAno.comidxxx NOT IN (\"F\") AND ";
				$qData .= "$cAlfa.fcod$nAno.pucidxxx IN ($cCuenta) AND ";
				if (($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") && $cPagTri <> "") {
					$qData .= "$cAlfa.fcod$nAno.ctoidxxx NOT IN ($cPagTri) AND ";
				}
				$qData .= "$cAlfa.fcod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND ";
				$qData .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
				$qData .= "GROUP BY $cAlfa.fcod$nAno.terid2xx,$cAlfa.fcod$nAno.teridxxx,$cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
				$qData .= "ORDER BY $cAlfa.fcod$nAno.terid2xx,$cAlfa.fcod$nAno.teridxxx,$cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
				$xData  = f_MySql("SELECT","",$qData,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qData." ~ ".mysql_num_rows($xData));

				$mLisCli = array(); $mDatCli = array();
				while ($xDATA = mysql_fetch_array($xData)) {
				
					if(in_array($xDATA['terid2xx'],$mLisCli) == false) {
						#Trayendo datos terid2xx
						$qDatExt = "SELECT * ";
						$qDatExt .= "FROM $cAlfa.SIAI0150 ";
						$qDatExt .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$xDATA['terid2xx']}\" LIMIT 0,1 ";
						$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");

						if(mysql_num_rows($xDatExt) > 0) {
							$xRDE = mysql_fetch_array($xDatExt);
							if ($xRDE['CLIRECOM']=="SI") {
								$mDatCli[$xDATA['terid2xx']]['CLICATER'] = "COMUN";
							}
							if ($xRDE['CLIRESIM']=="SI") {
								$mDatCli[$xDATA['terid2xx']]['CLICATER'] = "SIMPLIFICADO";
							}
							if ($xRDE['CLIGCXXX']=="SI") {
								$mDatCli[$xDATA['terid2xx']]['CLICATER'] = "CONTRIBUYENTE";
							}
							if ($xRDE['CLINRPXX']=="SI") {
								$mDatCli[$xDATA['terid2xx']]['CLICATER'] = "NORESIDENTE";
							}
							//Busco el codigo del pais
							$qCodPai = "SELECT PAIIDNXX FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"{$xRDE['PAIIDXXX']}\" LIMIT 0,1";
							$xCodPai  = f_MySql("SELECT","",$qCodPai,$xConexion01,"");
							if (mysql_num_rows($xCodPai) > 0) {
								$xRCP = mysql_fetch_array($xCodPai);
								$xRDE['PAIIDXXX'] =  ($xRCP['PAIIDNXX'] != "") ? $xRCP['PAIIDNXX'] : $xRDE['PAIIDXXX'];
							}
							//Fin Busco el codigo del pais
							$mDatCli[$xDATA['terid2xx']]['TDIIDXXX'] = $xRDE['TDIIDXXX'];
							$mDatCli[$xDATA['terid2xx']]['CLIAPE1X'] = utf8_encode($xRDE['CLIAPE1X']);
							$mDatCli[$xDATA['terid2xx']]['CLIAPE2X'] = utf8_encode($xRDE['CLIAPE2X']);
							$mDatCli[$xDATA['terid2xx']]['CLINOM1X'] = utf8_encode($xRDE['CLINOM1X']);
							$mDatCli[$xDATA['terid2xx']]['CLINOM2X'] = utf8_encode($xRDE['CLINOM2X']);
							$mDatCli[$xDATA['terid2xx']]['CLINOMXX'] = ($xRDE['TDIIDXXX'] == 31) ? utf8_encode($xRDE['CLINOMXX']) : "";
					
							$mLisCli[] = $xDATA['terid2xx'];
						}
					}

					if(in_array($xDATA['teridxxx'],$mLisCli) == false) {
					#Trayendo datos teridxxx
						$qDatExt = "SELECT * ";
						$qDatExt .= "FROM $cAlfa.SIAI0150 ";
						$qDatExt .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$xDATA['teridxxx']}\" LIMIT 0,1 ";
						$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");

						if(mysql_num_rows($xDatExt) > 0) {
							$xRDE = mysql_fetch_array($xDatExt);

							if ($xRDE['CLIRECOM']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "COMUN";
							}
							if ($xRDE['CLIRESIM']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "SIMPLIFICADO";
							}
							if ($xRDE['CLIGCXXX']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "CONTRIBUYENTE";
							}
							if ($xRDE['CLINRPXX']=="SI") {
								$mDatCli[$xDATA['teridxxx']]['CLICATER'] = "NORESIDENTE";
							}
							$mDatCli[$xDATA['teridxxx']]['TDIIDXXX'] = $xRDE['TDIIDXXX'];
							$mDatCli[$xDATA['teridxxx']]['CLIAPE1X'] = utf8_encode($xRDE['CLIAPE1X']);
							$mDatCli[$xDATA['teridxxx']]['CLIAPE2X'] = utf8_encode($xRDE['CLIAPE2X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOM1X'] = utf8_encode($xRDE['CLINOM1X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOM2X'] = utf8_encode($xRDE['CLINOM2X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOMXX'] = ($xRDE['TDIIDXXX'] == 31) ? utf8_encode($xRDE['CLINOMXX']) : "";

							$mLisCli[] = $xDATA['teridxxx'];
						}
					}
					$xDATA['CLINOMDI'] = $cNomAdu;

					//Para los comprobantes de Reembolso de caja Menor trae el iva del Recibo de Caja Menor
					//Busco el comprobante cruce dos en recibos de caja menor
					if ($xDATA['cajameno'] != "" && $xDATA['comvlr02'] == 0 && in_array("{$xDATA['comidxxx']}~{$xDATA['comcodxx']}", $mRCM) == true) {
						$vRecCaja  = explode("~",$xDATA['cajameno']);
						$cRecCaja  = "";
						for ($nRC=0; $nRC<count($vRecCaja); $nRC++) {
							$cRecCaja .= ($vRecCaja[$nRC] != "") ? "\"{$vRecCaja[$nRC]}\"," : "";
						}
						$cRecCaja = substr($cRecCaja, 0, strlen($cRecCaja)-1);

						if ($cRecCaja != "") {
							$qRecCaja  = "SELECT SUM(IF(commovxx=\"D\",comvlr02,comvlr02*-1)) AS comvlr02 ";
							$qRecCaja .= "FROM $cAlfa.fcme$nAno ";
							$qRecCaja .= "WHERE ";
							$qRecCaja .= "CONCAT(comidxxx,\"-\",comcodxx,\"-\",comcscxx,\"-\",comseqxx) IN ($cRecCaja)";
							$xRecCaja  = f_MySql("SELECT","",$qRecCaja,$xConexion01,"");
							//f_Mensaje(__FILE__,__LINE__,$qRecCaja." ~ ".mysql_num_rows($xRecCaja));
							if (mysql_num_rows($xRecCaja) > 0) {
								$xRRC = mysql_fetch_array($xRecCaja);
								$xDATA['comvlr02'] = $xRRC['comvlr02'];
							}
						}
					}

					if ($xDATA['comvlr01'] == 0) {
					switch ($cAlfa) {
						case "COLMASXX":
						case "DECOLMASXX":
						case "TECOLMASXX":
						$xDATA['comvlr01'] = $xDATA['comvlrxx'];
						$xDATA['comvlr02'] = 0;
						break;
						default:
							$nCal = 0;
							if ($xDATA['comidxxx'] == "G" || $xDATA['comidxxx'] == "L") {
								#Busco si el concepto contable es de pago de tributo
								$qPagTri  = "SELECT ";
								$qPagTri .= "ctoptaxg, ";
								$qPagTri .= "ctoptaxl  ";
								$qPagTri .= "FROM $cAlfa.fpar0119 ";
								$qPagTri .= "WHERE  ";
								$qPagTri .= "pucidxxx = \"{$xDATA['pucidxxx']}\" AND  ";
								$qPagTri .= "ctoidxxx = \"{$xDATA['ctoidxxx']}\" LIMIT 0,1  ";
								$xPagTri  = f_MySql("SELECT","",$qPagTri,$xConexion01,"");
								if (mysql_num_rows($xPagTri) > 0) {
									$xRPT = mysql_fetch_array($xPagTri);
									if($xDATA['comidxxx'] == "G" && $xRPT['ctoptaxg'] == "SI") {
										$xDATA['comvlr01'] = $xDATA['comvlrxx'];
										$xDATA['comvlr02'] = 0;
										$nCal = 1;
									}

									if($xDATA['comidxxx'] == "L" && $xRPT['ctoptaxl'] == "SI") {
										$xDATA['comvlr01'] = $xDATA['comvlrxx'];
										$xDATA['comvlr02'] = 0;
										$nCal = 1;
									}
								}
							}
							if ($nCal == 0) {
								switch ($cAlfa) {
									case "GRUPOGLA": case "DEGRUPOGLA": case "TEGRUPOGLA":
									case "ADUACARX": case "DEADUACARX": case "TEADUACARX":
										#Si el comvrl01x es cero calculo la base
										$xDATA['comvlr01'] = ($xDATA['comvlrxx']/1.16);
										$xDATA['comvlr02'] = ($xDATA['comvlr01']*0.16);
									break;
									default:
										#No se hace nada Se envia lo digitado en la grilla
									break;
								}
							}
						break;
					}
					}

					#Validacion para ADUACARGA, se excluye el registro si este tienen valor cero en las siguientes columnas:
					#Impuesto descontable
					#IVA resultante por devoluciones en ventas anuladas rescindidas o resueltas (esta siempre tiene valor cero)
					$nIncluir = 0;
					if ($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") {
						$nIncluir = ($xDATA['comvlr02'] == 0) ? 1 : 0;
					}

					if ($nIncluir == 0) {
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['TDIIDXXX']  = ($mData[$xDATA['terid2xx']]['TDIIDXXX'] <> "")?$mData[$xDATA['terid2xx']]['TDIIDXXX']:$mDatCli[$xDATA['terid2xx']]['TDIIDXXX'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['terid2xx']  = ($mData[$xDATA['terid2xx']]['terid2xx'] <> "")?$mData[$xDATA['terid2xx']]['terid2xx']:$xDATA['terid2xx'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['terid2di']  = ($mData[$xDATA['terid2xx']]['terid2di'] <> "")?$mData[$xDATA['terid2xx']]['terid2di']:$xDATA['terid2xx'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLIAPE1X']  = ($mData[$xDATA['terid2xx']]['CLIAPE1X'] <> "")?$mData[$xDATA['terid2xx']]['CLIAPE1X']:$mDatCli[$xDATA['terid2xx']]['CLIAPE1X'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLIAPE2X']  = ($mData[$xDATA['terid2xx']]['CLIAPE2X'] <> "")?$mData[$xDATA['terid2xx']]['CLIAPE2X']:$mDatCli[$xDATA['terid2xx']]['CLIAPE2X'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLINOM1X']  = ($mData[$xDATA['terid2xx']]['CLINOM1X'] <> "")?$mData[$xDATA['terid2xx']]['CLINOM1X']:$mDatCli[$xDATA['terid2xx']]['CLINOM1X'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLINOM2X']  = ($mData[$xDATA['terid2xx']]['CLINOM2X'] <> "")?$mData[$xDATA['terid2xx']]['CLINOM2X']:$mDatCli[$xDATA['terid2xx']]['CLINOM2X'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLINOMXX']  = ($mData[$xDATA['terid2xx']]['CLINOMXX'] <> "")?$mData[$xDATA['terid2xx']]['CLINOMXX']:$mDatCli[$xDATA['terid2xx']]['CLINOMXX'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['IMPDXXXX'] += $xDATA['comvlr02'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['IVARXXXX']  = 0;
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['TDIIDXXC']  = ($mData[$xDATA['terid2xx']]['TDIIDXXC'] <> "")?$mData[$xDATA['terid2xx']]['TDIIDXXC']:$mDatCli[$xDATA['teridxxx']]['TDIIDXXX'];
						$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLIIDXXC']  = ($mData[$xDATA['terid2xx']]['CLIIDXXC'] <> "")?$mData[$xDATA['terid2xx']]['CLIIDXXC']:$xDATA['teridxxx'];
					
					}
				}
			break;
			case "5250": // Nuevo 5250 Iva Generado
				
				$mCtoPCC = array(); // Arreglo cuenta concepto

				#Buscando datos de detalle
				$qData  = "SELECT ";
				$qData .= "$cAlfa.fcod$nAno.comidxxx,";
				$qData .= "$cAlfa.fcod$nAno.comcodxx,";
				$qData .= "$cAlfa.fcod$nAno.comcscxx,";
				$qData .= "$cAlfa.fcod$nAno.pucidxxx,";
				$qData .= "$cAlfa.fcod$nAno.ctoidxxx,";
				$qData .= "$cAlfa.fcod$nAno.teridxxx AS CLIIDXXC,";
				$qData .= "$cAlfa.fcod$nAno.teridxxx,";
				$qData .= "$cAlfa.fcod$nAno.terid2xx,";
				$qData .= "$cAlfa.fcod$nAno.commovxx,";
				$qData .= "$cAlfa.fcod$nAno.comvlr02,";
				$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlrxx,$cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx ";
				// $qData .= "$cAlfa.fcod$nAno.comvlrxx ";
				$qData .= "FROM $cAlfa.fcod$nAno ";
				$qData .= "WHERE ";
				$qData .= "$cAlfa.fcod$nAno.pucidxxx IN ($cCuenta) AND ";
				$qData .= "$cAlfa.fcod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND ";
				$qData .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
				$qData .= "GROUP BY $cAlfa.fcod$nAno.terid2xx ";
				$qData .= "ORDER BY $cAlfa.fcod$nAno.teridxxx,$cAlfa.fcod$nAno.terid2xx";
				$xData  = f_MySql("SELECT","",$qData,$xConexion01,"");
				// f_Mensaje(__FILE__,__LINE__,$qData." ~ ".mysql_num_rows($xData));

				## Busco los PCC en la Tabla fpar0121
				$qCtoP121  = "SELECT ";
				$qCtoP121 .= "$cAlfa.fpar0121.pucidxxx, $cAlfa.fpar0121.ctoidxxx ";
				$qCtoP121 .= "FROM $cAlfa.fpar0121 ";
				$qCtoP121 .= "WHERE $cAlfa.fpar0121.regestxx = \"ACTIVO\"";
				$xCtoP121  = f_MySql("SELECT","",$qCtoP121,$xConexion01,"");
				// f_Mensaje(__FILE__,__LINE__,$qCtoP121." ~ ".mysql_num_rows($xCtoP121));

				while($xRCP121 =  mysql_fetch_array($xCtoP121)){
					$mCtoPCC[count($mCtoPCC)] = "{$xRCP121['pucidxxx']}~{$xRCP121['ctoidxxx']}";
				}

				## Busco los PCC en la Tabla fpar0119
				$qCtoP119  = "SELECT ";
				$qCtoP119 .= "$cAlfa.fpar0119.pucidxxx, $cAlfa.fpar0119.ctoidxxx, $cAlfa.fpar0119.ctopccxx ";
				$qCtoP119 .= "FROM $cAlfa.fpar0119 ";
				$qCtoP119 .= "WHERE $cAlfa.fpar0119.ctopccxx = \"SI\" AND ";
				$qCtoP119 .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\"";
				$xCtoP119  = f_MySql("SELECT","",$qCtoP119,$xConexion01,"");
				// f_Mensaje(__FILE__,__LINE__,$qCtoP119." ~ ".mysql_num_rows($xCtoP119));
				
				while($xRCP119 =  mysql_fetch_array($xCtoP119)){
					$mCtoPCC[count($mCtoPCC)] = "{$xRCP119['pucidxxx']}~{$xRCP119['ctoidxxx']}";
				}

				$mLisCli = array(); $mDatCli = array();
				while ($xDATA = mysql_fetch_array($xData)) {
					
					if(in_array($xDATA['terid2xx'],$mLisCli) == false) {
						#Trayendo datos terid2xx
						$xDatExt  = array();
						$xRDE     = array();
						$qDatExt  = "SELECT * ";
						$qDatExt .= "FROM $cAlfa.SIAI0150 ";
						$qDatExt .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$xDATA['terid2xx']}\" LIMIT 0,1 ";
						$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");

						if(mysql_num_rows($xDatExt) > 0) {
							$xRDE = mysql_fetch_array($xDatExt);
					
							//Busco el codigo del pais
							$qCodPai = "SELECT PAIIDNXX FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"{$xRDE['PAIIDXXX']}\" LIMIT 0,1";
							$xCodPai  = f_MySql("SELECT","",$qCodPai,$xConexion01,"");
							if (mysql_num_rows($xCodPai) > 0) {
								$xRCP = mysql_fetch_array($xCodPai);
								$xRDE['PAIIDXXX'] =  ($xRCP['PAIIDNXX'] != "") ? $xRCP['PAIIDNXX'] : $xRDE['PAIIDXXX'];
							}
							//Fin Busco el codigo del pais
							$mDatCli[$xDATA['terid2xx']]['TDIIDXXX'] = $xRDE['TDIIDXXX'];
							$mDatCli[$xDATA['terid2xx']]['CLIAPE1X'] = utf8_encode($xRDE['CLIAPE1X']);
							$mDatCli[$xDATA['terid2xx']]['CLIAPE2X'] = utf8_encode($xRDE['CLIAPE2X']);
							$mDatCli[$xDATA['terid2xx']]['CLINOM1X'] = utf8_encode($xRDE['CLINOM1X']);
							$mDatCli[$xDATA['terid2xx']]['CLINOM2X'] = utf8_encode($xRDE['CLINOM2X']);
							$mDatCli[$xDATA['terid2xx']]['CLINOMXX'] = ($xRDE['TDIIDXXX'] == 31) ? utf8_encode($xRDE['CLINOMXX']) : "";	
							
							$mLisCli[] = $xDATA['terid2xx'];
						}
					}
					
					if(in_array($xDATA['teridxxx'],$mLisCli) == false) {
						#Trayendo datos teridxxx
						$xDatExt = array();
						$xRDE    = array();
						$qDatExt = "SELECT * ";
						$qDatExt .= "FROM $cAlfa.SIAI0150 ";
						$qDatExt .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$xDATA['teridxxx']}\" LIMIT 0,1 ";
						$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");

						if(mysql_num_rows($xDatExt) > 0) {
							$xRDE = mysql_fetch_array($xDatExt);
						
							//Busco el codigo del pais
							$qCodPai = "SELECT PAIIDNXX FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"{$xRDE['PAIIDXXX']}\" LIMIT 0,1";
							$xCodPai  = f_MySql("SELECT","",$qCodPai,$xConexion01,"");
							if (mysql_num_rows($xCodPai) > 0) {
								$xRCP = mysql_fetch_array($xCodPai);
								$xRDE['PAIIDXXX'] =  ($xRCP['PAIIDNXX'] != "") ? $xRCP['PAIIDNXX'] : $xRDE['PAIIDXXX'];
							}
							//Fin Busco el codigo del pais

							$mDatCli[$xDATA['teridxxx']]['TDIIDXXX'] = $xRDE['TDIIDXXX'];
							$mDatCli[$xDATA['teridxxx']]['CLIAPE1X'] = utf8_encode($xRDE['CLIAPE1X']);
							$mDatCli[$xDATA['teridxxx']]['CLIAPE2X'] = utf8_encode($xRDE['CLIAPE2X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOM1X'] = utf8_encode($xRDE['CLINOM1X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOM2X'] = utf8_encode($xRDE['CLINOM2X']);
							$mDatCli[$xDATA['teridxxx']]['CLINOMXX'] = ($xRDE['TDIIDXXX'] == 31) ? utf8_encode($xRDE['CLINOMXX']) : "";
				
							$mLisCli[] = $xDATA['teridxxx'];
						}
					}

					$nImpGen = 0; $nIvaDev = 0; $nImpCon  = 0;
					if(in_array("{$xDATA['pucidxxx']}~{$xDATA['ctoidxxx']}",$mCtoPCC)){
						
						if($xDATA['commovxx'] == "C"){
							$nImpGen = $xDATA['comvlrxx']; 
						}elseif($xDATA['commovxx'] == "D"){
							$nIvaDev = $xDATA['comvlrxx'];
						}
					
						if(substr($xDATA['pucidxxx'],0,4) == "1380"){
							$nImpCon = $xDATA['comvlrxx'];
						}
			
					}
					
					$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['TDIIDXXX']  = ($mData[$xDATA['terid2xx']]['TDIIDXXX'] <> "")?$mData[$xDATA['terid2xx']]['TDIIDXXX']:$mDatCli[$xDATA['terid2xx']]['TDIIDXXX'];
					$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['terid2xx']  = ($mData[$xDATA['terid2xx']]['terid2xx'] <> "")?$mData[$xDATA['terid2xx']]['terid2xx']:$xDATA['terid2xx'];
					$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLIAPE1X']  = ($mData[$xDATA['terid2xx']]['CLIAPE1X'] <> "")?$mData[$xDATA['terid2xx']]['CLIAPE1X']:$mDatCli[$xDATA['terid2xx']]['CLIAPE1X'];
					$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLIAPE2X']  = ($mData[$xDATA['terid2xx']]['CLIAPE2X'] <> "")?$mData[$xDATA['terid2xx']]['CLIAPE2X']:$mDatCli[$xDATA['terid2xx']]['CLIAPE2X'];
					$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLINOM1X']  = ($mData[$xDATA['terid2xx']]['CLINOM1X'] <> "")?$mData[$xDATA['terid2xx']]['CLINOM1X']:$mDatCli[$xDATA['terid2xx']]['CLINOM1X'];
					$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLINOM2X']  = ($mData[$xDATA['terid2xx']]['CLINOM2X'] <> "")?$mData[$xDATA['terid2xx']]['CLINOM2X']:$mDatCli[$xDATA['terid2xx']]['CLINOM2X'];
					$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLINOMXX']  = ($mData[$xDATA['terid2xx']]['CLINOMXX'] <> "")?$mData[$xDATA['terid2xx']]['CLINOMXX']:$mDatCli[$xDATA['terid2xx']]['CLINOMXX'];
				
					$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['IMPGENXX'] += $nImpGen;
					$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['IVADEXXX'] += $nIvaDev; 
					$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['IMPCONXX'] = $nImpCon;

					$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['TDIIDXXC']  = ($mData[$xDATA['terid2xx']]['TDIIDXXC'] <> "")?$mData[$xDATA['terid2xx']]['TDIIDXXC']:$mDatCli[$xDATA['teridxxx']]['TDIIDXXX'];
					$mData[$xDATA['terid2xx']."~".$xDATA['teridxxx']]['CLIIDXXC']  = ($mData[$xDATA['terid2xx']]['CLIIDXXC'] <> "")?$mData[$xDATA['terid2xx']]['CLIIDXXC']:$xDATA['teridxxx'];
				
				}
			break;
			case "5251": // NUEVO 5251 ~ CXC

				//Dentro de las cuentas seleccionadas busco solo aquellas que sean por cobrar
				$cCuenta = "";
				for($nC=0; $nC<count($vCuenta); $nC++) {
					$qCxC  = "SELECT CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx ";
					$qCxC .= "FROM $cAlfa.fpar0115 ";
					$qCxC .= "WHERE ";
					$qCxC .= "CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$vCuenta[$nC]}\" AND ";
					$qCxC .= "pucdetxx = \"C\" LIMIT 0,1";
					$xCxC  = mysql_query($qCxC,$xConexion01);
					//f_Mensaje(__FILE__,__LINE__,$qCxC."~".mysql_num_rows($xCxC));
					if (mysql_num_rows($xCxC) > 0) {
						$xRCxC = mysql_fetch_array($xCxC);
						$cCuenta .= "\"{$xRCxC['pucidxxx']}\",";
					}
				}
				$cCuenta = substr($cCuenta, 0, -1);

				$vTablas = array(); $mAux = array();
				if ($cCuenta != "") {
					for ($nAnio=$vSysStr['financiero_ano_instalacion_modulo'];$nAnio<=$nAno;$nAnio++) {
						$qDatMov  = "SELECT ";
						$qDatMov .= "comidxxx, ";
						$qDatMov .= "comcodxx, ";
						$qDatMov .= "comcscxx, ";
						$qDatMov .= "comidcxx, ";
						$qDatMov .= "comcodcx, ";
						$qDatMov .= "comcsccx, ";
						$qDatMov .= "teridxxx, ";
						$qDatMov .= "terid2xx, ";
						$qDatMov .= "pucidxxx, ";
						$qDatMov .= "comfecxx, ";
						$qDatMov .= "comfecve, ";
						$qDatMov .= "commovxx, ";
						$qDatMov .= "IF(commovxx = \"D\", comvlrxx, comvlrxx*-1) AS saldoxxx ";
						$qDatMov .= "FROM $cAlfa.fcod$nAnio ";
						$qDatMov .= "WHERE  ";
						$qDatMov .= "pucidxxx IN ($cCuenta) AND ";
						$qDatMov .= "comfecxx <= \"$dHasta\" AND ";
						$qDatMov .= "regestxx = \"ACTIVO\" ";
						$qDatMov .= "ORDER BY ABS(teridxxx), ABS(terid2xx) ";
						$xDatMov = mysql_query($qDatMov,$xConexion01);
						// echo $qDatMov."~".mysql_num_rows($xDatMov)."<br><br>";

						while ($xDATA = mysql_fetch_array($xDatMov)) {

							if ($xDATA['comidxxx'] != $xDATA['comidcxx'] ||
									$xDATA['comcodxx'] != $xDATA['comcodcx'] ||
									$xDATA['comcscxx'] != $xDATA['comcsccx']) {
								//Se debe buscar el cliente del combropante que se esta cancelando
								for ($nAnioDC=$nAnio;$nAnioDC>=$vSysStr['financiero_ano_instalacion_modulo'];$nAnioDC--) {
									$qDocCru  = "SELECT ";
									$qDocCru .= "terid2xx  ";
									$qDocCru .= "FROM $cAlfa.fcod$nAnioDC ";
									$qDocCru .= "WHERE  ";
									$qDocCru .= "comidxxx = \"{$xDATA['comidcxx']}\" AND ";
									$qDocCru .= "comcodxx = \"{$xDATA['comcodcx']}\" AND ";
									$qDocCru .= "comcscxx = \"{$xDATA['comcsccx']}\" AND ";
									$qDocCru .= "teridxxx = \"{$xDATA['teridxxx']}\" AND ";
									$qDocCru .= "pucidxxx = \"{$xDATA['pucidxxx']}\" AND ";
									$qDocCru .= "regestxx = \"ACTIVO\" LIMIT 0,1";
									$xDocCru = mysql_query($qDocCru,$xConexion01);
									//echo $qDocCru."~".mysql_num_rows($xDocCru)."<br><br>";
									if (mysql_num_rows($xDocCru) > 0) {
										$xRDC = mysql_fetch_array($xDocCru);
										$xDATA['terid2xx'] = $xRDC['terid2xx'];

										$nAnioDC = $vSysStr['financiero_ano_instalacion_modulo']-1;
									}
								}
							}

							$mAux[$xDATA['teridxxx']."~".$xDATA['terid2xx']]['teridxxx']  = $xDATA['teridxxx'];
							$mAux[$xDATA['teridxxx']."~".$xDATA['terid2xx']]['terid2xx']  = $xDATA['terid2xx'];
							$mAux[$xDATA['teridxxx']."~".$xDATA['terid2xx']]['COMVLRXX'] += $xDATA['saldoxxx'];
						}
					}
				}

				##Creacion de la tabla detalle del dia
				foreach ($mAux as $cKey => $cValue) {
					if ($mAux[$cKey]['COMVLRXX'] != 0) {

						# Traigo el Nombre del Cliente
						$qNomCli  = "SELECT * ";
						$qNomCli .= "FROM $cAlfa.SIAI0150 ";
						$qNomCli .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$mAux[$cKey]['teridxxx']}\" LIMIT 0,1";
						$xNomCli = f_MySql("SELECT","",$qNomCli,$xConexion01,"");
						$vNomCli = mysql_fetch_array($xNomCli);
						# Fin Traigo el Nombre del Cliente

						//Busco el codigo del pais
						$qCodPai = "SELECT PAIIDNXX FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"{$vNomCli['PAIIDXXX']}\" LIMIT 0,1";
						$xCodPai  = f_MySql("SELECT","",$qCodPai,$xConexion01,"");
						if (mysql_num_rows($xCodPai) > 0) {
							$xRCP = mysql_fetch_array($xCodPai);
							$vNomCli['PAIIDXXX'] =  ($xRCP['PAIIDNXX'] != "") ? $xRCP['PAIIDNXX'] : $vNomCli['PAIIDXXX'];
						}
						//Fin Busco el codigo del pais

						# Traigo el Nombre del Cliente con terid2xx
						$qNomCl2  = "SELECT * ";
						$qNomCl2 .= "FROM $cAlfa.SIAI0150 ";
						$qNomCl2 .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$mAux[$cKey]['terid2xx']}\" LIMIT 0,1";
						$xNomCl2 = f_MySql("SELECT","",$qNomCl2,$xConexion01,"");
						$vNomCl2 = mysql_fetch_array($xNomCl2);
						//f_Mensaje(__FILE__,__LINE__,$qNomCl2."~".mysql_num_rows($xNomCl2));
						# Fin Traigo el Nombre del Cliente

						//Busco el codigo del pais
						$qCodPa1 = "SELECT PAIIDNXX FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"{$vNomCl2['PAIIDXXX']}\" LIMIT 0,1";
						$xCodPa1  = f_MySql("SELECT","",$qCodPa1,$xConexion01,"");
						if (mysql_num_rows($xCodPa1) > 0) {
							$xRCP1 = mysql_fetch_array($xCodPa1);
							$vNomCl2['PAIIDXXX'] =  ($xRCP1['PAIIDNXX'] != "") ? $xRCP1['PAIIDNXX'] : $vNomCl2['PAIIDXXX'];
						}
						//Fin Busco el codigo del pais

						$mData[$cKey]['TDIIDXXX']  = ($mData[$cKey]['TDIIDXXX'] <> "")?$mData[$cKey]['TDIIDXXX']:$vNomCli['TDIIDXXX'];
						$mData[$cKey]['terid2xx']  = ($mData[$cKey]['terid2xx'] <> "")?$mData[$cKey]['terid2xx']:$mAux[$cKey]['terid2xx'];
						$mData[$cKey]['CLIAPE1X']  = ($mData[$cKey]['CLIAPE1X'] <> "")?$mData[$cKey]['CLIAPE1X']:$vNomCli['CLIAPE1X'];
						$mData[$cKey]['CLIAPE2X']  = ($mData[$cKey]['CLIAPE2X'] <> "")?$mData[$cKey]['CLIAPE2X']:$vNomCli['CLIAPE2X'];
						$mData[$cKey]['CLINOM1X']  = ($mData[$cKey]['CLINOM1X'] <> "")?$mData[$cKey]['CLINOM1X']:$vNomCli['CLINOM1X'];
						$mData[$cKey]['CLINOM2X']  = ($mData[$cKey]['CLINOM2X'] <> "")?$mData[$cKey]['CLINOM2X']:$vNomCli['CLINOM2X'];
						$mData[$cKey]['CLINOMXX']  = ($mData[$cKey]['CLINOMXX'] <> "")?$mData[$cKey]['CLINOMXX']:(($vNomCli['TDIIDXXX'] == 31) ? $vNomCli['CLINOMXX'] : "");
						$mData[$cKey]['CLIDIRXX']  = ($mData[$cKey]['CLIDIRXX'] <> "")?$mData[$cKey]['CLIDIRXX']:$vNomCli['CLIDIRXX'];
						$mData[$cKey]['DEPIDXXX']  = ($mData[$cKey]['DEPIDXXX'] <> "")?$mData[$cKey]['DEPIDXXX']:$vNomCli['DEPIDXXX'];
						$mData[$cKey]['CIUIDXXX']  = ($mData[$cKey]['CIUIDXXX'] <> "")?$mData[$cKey]['CIUIDXXX']:$vNomCli['CIUIDXXX'];
						$mData[$cKey]['PAIIDXXX']  = ($mData[$cKey]['PAIIDXXX'] <> "")?$mData[$cKey]['PAIIDXXX']:$vNomCli['PAIIDXXX'];
						$mData[$cKey]['COMVLRXX']  = $mAux[$cKey]['COMVLRXX'];
						$mData[$cKey]['TDIIDXXC']  = ($mData[$cKey]['TDIIDXXC'] <> "")?$mData[$cKey]['TDIIDXXC']:$vNomCl2['TDIIDXXX'];
						$mData[$cKey]['teridxxx']  = ($mData[$cKey]['teridxxx'] <> "")?$mData[$cKey]['teridxxx']:$mAux[$cKey]['teridxxx'];
						
					}
				}

			break;
			case "5252": // NUEVO 5252 ~ CXP

				//Dentro de las cuentas seleccionadas busco solo aquellas que sean por pagar
				$cCuenta = "";
				for($nC=0; $nC<count($vCuenta); $nC++) {
					$qCxP  = "SELECT CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx ";
					$qCxP .= "FROM $cAlfa.fpar0115 ";
					$qCxP .= "WHERE ";
					$qCxP .= "CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$vCuenta[$nC]}\" LIMIT 0,1";
					// $qCxP .= "pucdetxx = \"P\" LIMIT 0,1";
					$xCxP  = mysql_query($qCxP,$xConexion01);
					// f_Mensaje(__FILE__,__LINE__,$qCxP."~".mysql_num_rows($xCxP));
					if (mysql_num_rows($xCxP) > 0) {
						$xRCxP = mysql_fetch_array($xCxP);
						$cCuenta .= "\"{$xRCxP['pucidxxx']}\",";
					}
				}
				$cCuenta = substr($cCuenta, 0, -1);

				$vTablas = array(); $mAux = array();
				if ($cCuenta != "") {
					for ($nAnio=$vSysStr['financiero_ano_instalacion_modulo'];$nAnio<=$nAno;$nAnio++) {
						$qDatMov  = "SELECT ";
						$qDatMov .= "comidxxx, ";
						$qDatMov .= "comcodxx, ";
						$qDatMov .= "comcscxx, ";
						$qDatMov .= "comidcxx, ";
						$qDatMov .= "comcodcx, ";
						$qDatMov .= "comcsccx, ";
						$qDatMov .= "teridxxx, ";
						$qDatMov .= "terid2xx, ";
						$qDatMov .= "pucidxxx, ";
						$qDatMov .= "comfecxx, ";
						$qDatMov .= "comfecve, ";
						$qDatMov .= "commovxx, ";
						$qDatMov .= "IF(commovxx = \"D\", comvlrxx, comvlrxx*-1) AS saldoxxx ";
						$qDatMov .= "FROM $cAlfa.fcod$nAnio ";
						$qDatMov .= "WHERE ";
						$qDatMov .= "pucidxxx IN ($cCuenta) AND ";
						$qDatMov .= "comfecxx <= \"$dHasta\" AND ";
						$qDatMov .= "regestxx = \"ACTIVO\" ";
						$qDatMov .= "ORDER BY ABS(teridxxx), ABS(terid2xx) ";
						$xDatMov = mysql_query($qDatMov,$xConexion01);
						//echo $qDatMov."~".mysql_num_rows($xDatMov)."<br><br>";

						while ($xDATA = mysql_fetch_array($xDatMov)) {
							if ($xDATA['comidxxx'] != $xDATA['comidcxx'] ||
									$xDATA['comcodxx'] != $xDATA['comcodcx'] ||
									$xDATA['comcscxx'] != $xDATA['comcsccx']) {
								//Se debe buscar el cliente del combropante que se esta cancelando
								for ($nAnioDC=$nAnio;$nAnioDC>=$vSysStr['financiero_ano_instalacion_modulo'];$nAnioDC--) {
									$qDocCru  = "SELECT ";
									$qDocCru .= "terid2xx  ";
									$qDocCru .= "FROM $cAlfa.fcod$nAnioDC ";
									$qDocCru .= "WHERE  ";
									$qDocCru .= "comidxxx = \"{$xDATA['comidcxx']}\" AND ";
									$qDocCru .= "comcodxx = \"{$xDATA['comcodcx']}\" AND ";
									$qDocCru .= "comcscxx = \"{$xDATA['comcsccx']}\" AND ";
									$qDocCru .= "teridxxx = \"{$xDATA['teridxxx']}\" AND ";
									$qDocCru .= "pucidxxx = \"{$xDATA['pucidxxx']}\" AND ";
									$qDocCru .= "regestxx = \"ACTIVO\" LIMIT 0,1";
									$xDocCru = mysql_query($qDocCru,$xConexion01);
									//echo $qDocCru."~".mysql_num_rows($xDocCru)."<br><br>";
									if (mysql_num_rows($xDocCru) > 0) {
										$xRDC = mysql_fetch_array($xDocCru);
										$xDATA['terid2xx'] = $xRDC['terid2xx'];

										$nAnioDC = $vSysStr['financiero_ano_instalacion_modulo']-1;
									}
								}
							}

							//echo "{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}~{$xDATA['terid2xx']}~{$xDATA['saldoxxx']}<br>";
							$mAux[$xDATA['teridxxx']."~".$xDATA['terid2xx']]['teridxxx']  = $xDATA['teridxxx'];
							$mAux[$xDATA['teridxxx']."~".$xDATA['terid2xx']]['terid2xx']  = $xDATA['terid2xx'];
							$mAux[$xDATA['teridxxx']."~".$xDATA['terid2xx']]['COMVLRXX'] += $xDATA['saldoxxx'];
						}
					}
				}

				foreach ($mAux as $cKey => $cValue) {
					if ($mAux[$cKey]['COMVLRXX'] != 0) {

						# Traigo el Nombre del Cliente
						$qNomCli  = "SELECT * ";
						$qNomCli .= "FROM $cAlfa.SIAI0150 ";
						$qNomCli .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$mAux[$cKey]['teridxxx']}\" LIMIT 0,1";
						$xNomCli = f_MySql("SELECT","",$qNomCli,$xConexion01,"");
						$vNomCli = mysql_fetch_array($xNomCli);
						# Fin Traigo el Nombre del Cliente

						//Busco el codigo del pais
						$qCodPai = "SELECT PAIIDNXX FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"{$vNomCli['PAIIDXXX']}\" LIMIT 0,1";
						$xCodPai  = f_MySql("SELECT","",$qCodPai,$xConexion01,"");
						if (mysql_num_rows($xCodPai) > 0) {
							$xRCP = mysql_fetch_array($xCodPai);
							$vNomCli['PAIIDXXX'] =  ($xRCP['PAIIDNXX'] != "") ? $xRCP['PAIIDNXX'] : $vNomCli['PAIIDXXX'];
						}
						//Fin Busco el codigo del pais

						# Traigo el Nombre del Cliente con terid2xx
						$qNomCl2  = "SELECT * ";
						$qNomCl2 .= "FROM $cAlfa.SIAI0150 ";
						$qNomCl2 .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$mAux[$cKey]['terid2xx']}\" LIMIT 0,1";
						$xNomCl2 = f_MySql("SELECT","",$qNomCl2,$xConexion01,"");
						$vNomCl2 = mysql_fetch_array($xNomCl2);
						//f_Mensaje(__FILE__,__LINE__,$qNomCl2."~".mysql_num_rows($xNomCl2));
						# Fin Traigo el Nombre del Cliente

						//Busco el codigo del pais
						$qCodPa1 = "SELECT PAIIDNXX FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"{$vNomCl2['PAIIDXXX']}\" LIMIT 0,1";
						$xCodPa1  = f_MySql("SELECT","",$qCodPa1,$xConexion01,"");
						if (mysql_num_rows($xCodPa1) > 0) {
							$xRCP1 = mysql_fetch_array($xCodPa1);
							$vNomCl2['PAIIDXXX'] =  ($xRCP1['PAIIDNXX'] != "") ? $xRCP1['PAIIDNXX'] : $vNomCl2['PAIIDXXX'];
						}
						//Fin Busco el codigo del pais

						$mData[$cKey]['TDIIDXXX']  = ($mData[$cKey]['TDIIDXXX'] <> "")?$mData[$cKey]['TDIIDXXX']:$vNomCli['TDIIDXXX'];
						$mData[$cKey]['teridxxx']  = ($mData[$cKey]['teridxxx'] <> "")?$mData[$cKey]['teridxxx']:$mAux[$cKey]['teridxxx'];
						$mData[$cKey]['CLIAPE1X']  = ($mData[$cKey]['CLIAPE1X'] <> "")?$mData[$cKey]['CLIAPE1X']:$vNomCli['CLIAPE1X'];
						$mData[$cKey]['CLIAPE2X']  = ($mData[$cKey]['CLIAPE2X'] <> "")?$mData[$cKey]['CLIAPE2X']:$vNomCli['CLIAPE2X'];
						$mData[$cKey]['CLINOM1X']  = ($mData[$cKey]['CLINOM1X'] <> "")?$mData[$cKey]['CLINOM1X']:$vNomCli['CLINOM1X'];
						$mData[$cKey]['CLINOM2X']  = ($mData[$cKey]['CLINOM2X'] <> "")?$mData[$cKey]['CLINOM2X']:$vNomCli['CLINOM2X'];
						$mData[$cKey]['CLINOMXX']  = ($mData[$cKey]['CLINOMXX'] <> "")?$mData[$cKey]['CLINOMXX']:(($vNomCli['TDIIDXXX'] == 31) ? $vNomCli['CLINOMXX'] : "");
						$mData[$cKey]['CLIDIRXX']  = ($mData[$cKey]['CLIDIRXX'] <> "")?$mData[$cKey]['CLIDIRXX']:$vNomCli['CLIDIRXX'];
						$mData[$cKey]['DEPIDXXX']  = ($mData[$cKey]['DEPIDXXX'] <> "")?$mData[$cKey]['DEPIDXXX']:$vNomCli['DEPIDXXX'];
						$mData[$cKey]['CIUIDXXX']  = ($mData[$cKey]['CIUIDXXX'] <> "")?$mData[$cKey]['CIUIDXXX']:$vNomCli['CIUIDXXX'];
						$mData[$cKey]['PAIIDXXX']  = ($mData[$cKey]['PAIIDXXX'] <> "")?$mData[$cKey]['PAIIDXXX']:$vNomCli['PAIIDXXX'];
						$mData[$cKey]['COMVLRXX']  = $mAux[$cKey]['COMVLRXX'];
						//Datos adicionales para terid2xx
						$mData[$cKey]['TDIIDXXC']  = ($mData[$cKey]['TDIIDXXC'] <> "")?$mData[$cKey]['TDIIDXXC']:$vNomCl2['TDIIDXXX'];
						$mData[$cKey]['terid2xx']  = ($mData[$cKey]['terid2xx'] <> "")?$mData[$cKey]['terid2xx']:$mAux[$cKey]['terid2xx'];
					}
				}

			break;


		} ## Fion Switch

		switch ($cTipo) {
			case 1:
				// PINTA POR PANTALLA// ?>
						<form name = 'frgrm' action='frmedprn.php' method="post">
						<input type = "hidden"  name = "cTerId"   value = "">
						<input type = "hidden"  name = "cTerId2"  value = "">
						<input type = "hidden"  name = "cFormato" value = "<?php echo $gFormato ?>">
						<input type = "hidden"  name = "dDesde"   value = "<?php echo $dDesde   ?>">
						<input type = "hidden"  name = "dHasta"   value = "<?php echo $dHasta   ?>">
						<textarea id="cCuentas" name = "cCuentas"><?php echo $cCuenta ?></textarea>
						<textarea id="cTitulos" name = "cTitulos"><?php echo $cTitCue ?></textarea>
						<script type="text/javascript">
							document.getElementById('cCuentas').style.display="none";
							document.getElementById('cTitulos').style.display="none";
						</script>
							<center>
								<?php
								switch ($gFormato) {
									case "1001": ?>
										<table width="99%"  cellspacing="0" cellpadding="0" border="0">
											<tr>
												<td>
													<table border="1 cellspacing="0" cellpadding="0" width="99%" align=center>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="4"><b>Informe de Medios Magneticos - Formato 1001</b></font>
															</td>
														</tr>
														<tr bgcolor = 'white' height="30">
															<td class="name" align="left"><font size="3">Cuenta (PUC): <?php echo $cTitCue ?></font></td>
														</tr>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="3">Registros Analizados : <?php echo number_format(count($mData)) ?></font></td>
														</tr>
													</table>

													<table border="1" cellspacing="0" cellpadding="0" width="99%"  align=center>
														<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' height="30">
															<td class="name" width="100" align="center">Concepto</td>
															<td class="name" width="150" align="center">Tipo de documento</td>
															<td class="name" width="150" align="center">Numero identificacion del informado</td>
															<td class="name" width="50" align="center">DV</td>
															<td class="name" width="150" align="center">Primer apellido del informado</td>
															<td class="name" width="150" align="center">Segundo apellido del informado</td>
															<td class="name" width="150" align="center">Primer nombre del informado</td>
															<td class="name" width="150" align="center">Otros nombres del informado</td>
															<td class="name" align="center">Razon social informado</td>
															<td class="name" width="300" align="center">Direccion</td>
															<td class="name" width="150" align="center">Codigo dpto.</td>
															<td class="name" width="100" align="center">Codigo mcp</td>
															<td class="name" width="100" align="center">Pais de residencia o domicilio</td>
															<td class="name" width="150" align="center">Pago o abono en cuenta deducible</td>
															<td class="name" width="150" align="center">Pago o abono en cuenta no deducible</td>
															<td class="name" width="150" align="center">IVA mayor valor del costo o gasto deducible</td>
															<td class="name" width="150" align="center">Iva mayor valor del costo o gasto no deducible</td>
															<td class="name" width="150" align="center">Retencion en la fuente practicada renta</td>
															<td class="name" width="150" align="center">Retencion en la fuente asumida renta</td>
															<td class="name" width="150" align="center">Retencion en la fuente practicada IVA regimen comun</td>
															<td class="name" width="150" align="center">Retencion en la fuente asumida IVA regimen simplificado</td>
															<td class="name" width="150" align="center">Retencion en la fuente practicada IVA no domiciliados</td>
															<td class="name" width="150" align="center">Retencion en la fuente practicadas CREE</td>
															<td class="name" width="150" align="center">Retencion en la fuente asumidas CREE</td>
														</tr>
														<?php foreach ($mData as $i => $cValue) {	?>
															<tr bgcolor = "white" height="30">
																<td class="letra7" align="center">&nbsp;</td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXX'] <> "")?$mData[$i]['TDIIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['teridxxx']<> "")?"<a href=\"javascript:f_Datos_Tercero('{$mData[$i]['teridxxx']}','')\">{$mData[$i]['teridxxx']}</a>": "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['teridxxx']<> "")?f_Digito_Verificacion($mData[$i]['teridxxx']): "&nbsp;";  ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE1X'] <> "") ?  $mData[$i]['CLIAPE1X'] : "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE2X']<> "")?$mData[$i]['CLIAPE2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM1X']<> "")?$mData[$i]['CLINOM1X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM2X']<> "")?$mData[$i]['CLINOM2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOMXX']<> "")?$mData[$i]['CLINOMXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIDIRXX']<> "")?$mData[$i]['CLIDIRXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['DEPIDXXX']<> "")?$mData[$i]['DEPIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['CIUIDXXX']<> "")?$mData[$i]['CIUIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['PAIIDXXX']<> "")?$mData[$i]['PAIIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="right"> <?php echo number_format($mData[$i]['PAGODEXX'],0,",",".") ?></td>
																<td class="letra7" align="right"> <?php echo number_format($mData[$i]['PAGONODE'],0,",",".") ?></td>
																<td class="letra7" align="right"> <?php echo number_format($mData[$i]['IVAXXXXX'],0,",",".") ?></td>
																<td class="letra7" align="right"> <?php echo number_format($mData[$i]['IVANOXXX'],0,",",".") ?></td>
																<td class="letra7" align="right"> <?php echo number_format($mData[$i]['PRACXXXX'],0,",",".") ?></td>
																<td class="letra7" align="right"> <?php echo number_format($mData[$i]['ASUMXXXX'],0,",",".") ?></td>
																<td class="letra7" align="right"> <?php echo number_format($mData[$i]['COMUNXXX'],0,",",".") ?></td>
																<td class="letra7" align="right"> <?php echo number_format($mData[$i]['SIMPLXXX'],0,",",".") ?></td>
																<td class="letra7" align="right"> <?php echo number_format($mData[$i]['NDOMXXXX'],0,",",".") ?></td>
																<td class="letra7" align="right"> <?php echo number_format($mData[$i]['PRACCREE'],0,",",".") ?></td>
																<td class="letra7" align="right"> <?php echo number_format($mData[$i]['ASUMCREE'],0,",",".") ?></td>
															</tr>
														<?php } //Fin While que recorre el cursor de la matriz generada por la consulta. ?>
													</table>
												</td>
											</tr>
										</table><br>
									<?php break;
									case "1003": ?>
										<table width="99%"  cellspacing="0" cellpadding="0" border="0">
											<tr>
												<td>
													<table border="1 cellspacing="0" cellpadding="0" width="99%" align=center>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="4"><b>Informe de Medios Magneticos - Formato 1003</b></font>
															</td>
														</tr>
														<tr bgcolor = 'white' height="30">
															<td class="name" align="left"><font size="3">Cuenta (PUC): <?php echo $cTitCue ?></font></td>
														</tr>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="3">Registros Analizados : <?php echo number_format(count($mData)) ?></font></td>
														</tr>
													</table>

													<table border="1" cellspacing="0" cellpadding="0" width="99%"  align=center>
														<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' height="30">
															<td class="name" width="100" align="center">Concepto</td>
															<td class="name" width="150" align="center">Tipo de documento</td>
															<td class="name" width="150" align="center">Numero identificacion del informado</td>
															<td class="name" width="50" align="center">DV</td>
															<td class="name" width="150" align="center">Primer apellido del informado</td>
															<td class="name" width="150" align="center">Segundo apellido del informado</td>
															<td class="name" width="150" align="center">Primer nombre del informado</td>
															<td class="name" width="150" align="center">Otros nombres del informado</td>
															<td class="name" align="center">Razon social informado</td>
															<td class="name" width="300" align="center">Direccion</td>
															<td class="name" width="150" align="center">Codigo dpto.</td>
															<td class="name" width="100" align="center">Codigo mcp</td>
															<td class="name" width="150" align="center">Valor acum. del pago o abono sujeto a retencion en la fuente</td>
															<td class="name" width="150" align="center">Retencion en la fuente que le practicaron</td>
														</tr>
														<?php foreach ($mData as $i => $cValue) {	?>
															<tr bgcolor = "white" height="30">
																<td class="letra7" align="center">&nbsp;</td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXX'] <> "")?$mData[$i]['TDIIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['teridxxx']<> "")?"<a href=\"javascript:f_Datos_Tercero('{$mData[$i]['teridxxx']}','')\">{$mData[$i]['teridxxx']}</a>": "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['teridxxx']<> "")?f_Digito_Verificacion($mData[$i]['teridxxx']): "&nbsp;";  ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE1X'] <> "") ?  $mData[$i]['CLIAPE1X'] : "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE2X']<> "")?$mData[$i]['CLIAPE2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM1X']<> "")?$mData[$i]['CLINOM1X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM2X']<> "")?$mData[$i]['CLINOM2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOMXX']<> "")?$mData[$i]['CLINOMXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIDIRXX']<> "")?$mData[$i]['CLIDIRXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['DEPIDXXX']<> "")?$mData[$i]['DEPIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['CIUIDXXX']<> "")?$mData[$i]['CIUIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="right"> <?php echo number_format($mData[$i]['COMVRL01'],0,",",".") ?></td>
																<td class="letra7" align="right"> <?php echo number_format($mData[$i]['COMVLRXX'],0,",",".") ?></td>
															</tr>
														<?php } //Fin While que recorre el cursor de la matriz generada por la consulta. ?>
													</table>
												</td>
											</tr>
										</table><br>
									<?php break;
									case "1005": ?>
										<table width="99%"  cellspacing="0" cellpadding="0" border="0">
											<tr>
												<td>
													<table border="1 cellspacing="0" cellpadding="0" width="99%" align=center>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="4"><b>Informe de Medios Magneticos - Formato 1005</b></font></td>
														</tr>
														<tr bgcolor = 'white' height="30">
															<td class="name" align="left"><font size="3">Cuenta (PUC): <?php echo $cTitCue ?></font></td>
														</tr>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="3">Registros Analizados : <?php echo number_format(count($mData)) ?></font></td>
														</tr>
													</table>
													<table border="1" cellspacing="0" cellpadding="0" width="99%"  align=center>
														<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' height="30">
															<td class="name" width="150" align="center">Tipo de Documento</td>
															<td class="name" width="150" align="center">Numero de identificacion del informado</td>
															<td class="name" width="50" align="center">DV</td>
															<td class="name" width="150" align="center">Primer apellido del informado</td>
															<td class="name" width="150" align="center">Segundo apellido del informado</td>
															<td class="name" width="150" align="center">Primer nombre del informado</td>
															<td class="name" width="150" align="center">Otros nombres del informado</td>
															<td class="name" align="center">Razon social informado</td>
															<td class="name" width="150" align="center">Impuesto descontable</td>
															<td class="name" width="150" align="center">IVA resultante por devoluciones en ventas anuladas, rescindidas o resueltas</td>
														</tr>
														<?php foreach ($mData as $i => $cValue) {	?>
															<tr bgcolor = "white" height="30">
																<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXX'] <> "")?$mData[$i]['TDIIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['teridxxx']<> "")?"<a href=\"javascript:f_Datos_Tercero('{$mData[$i]['teridxxx']}','')\">{$mData[$i]['teridxxx']}</a>": "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['teridxxx']<> "")?f_Digito_Verificacion($mData[$i]['teridxxx']): "&nbsp;";  ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE1X'] <> "") ?  $mData[$i]['CLIAPE1X'] : "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE2X']<> "")?$mData[$i]['CLIAPE2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM1X']<> "")?$mData[$i]['CLINOM1X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM2X']<> "")?$mData[$i]['CLINOM2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOMXX']<> "")?$mData[$i]['CLINOMXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="right"> <?php echo number_format($mData[$i]['COMVLRXX'],0,",",".") ?></td>
																<td class="letra7" align="right"> <?php echo number_format(0,0,",",".") ?></td>
															</tr>
														<?php } //Fin While que recorre el cursor de la matriz generada por la consulta. ?>
													</table>
												</td>
											</tr>
										</table><br>
									<?php break;
									case "1006": ?>
										<table width="99%"  cellspacing="0" cellpadding="0" border="0">
											<tr>
												<td>
													<table border="1 cellspacing="0" cellpadding="0" width="99%"  align=center>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="4"><b>Informe de Medios Magneticos - Formato 1006</b></font>
															</td>
														</tr>
														<tr bgcolor = 'white' height="30">
															<td class="name" align="left"><font size="3">Cuenta (PUC): <?php echo $cTitCue; ?></font></td>
														</tr>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="3">Registros Analizados : <?php echo number_format(count($mData)) ?></font></td>
														</tr>
													</table>

													<table border="1" cellspacing="0" cellpadding="0" width="99%"  align=center>
														<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' height="30">
															<td class="name" width="150" align="center">Tipo de Documento</td>
															<td class="name" width="150" align="center">Numero de identificacion del informado</td>
															<td class="name" width="50" align="center">DV</td>
															<td class="name" width="150" align="center">Primer apellido del informado</td>
															<td class="name" width="150" align="center">Segundo apellido del informado</td>
															<td class="name" width="150" align="center">Primer nombre del informado</td>
															<td class="name" width="150" align="center">Otros nombres del informado</td>
															<td class="name" align="center">Razon social informado</td>
															<td class="name" width="150" align="center">Impuesto generado</td>
															<td class="name" width="150" align="center">IVA recuperado por operaciones en devoluciones en compras anuladas, rescindidas o resueltas</td>
															<td class="name" width="150" align="center">Impuesto al consumo</td>
														</tr>
														<?php for ($i=0;$i<count($mData);$i++) { ?>
															<tr bgcolor = "white" height="30">
																<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXX'] <> "")?$mData[$i]['TDIIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['teridxxx']<> "")?"<a href=\"javascript:f_Datos_Tercero('{$mData[$i]['teridxxx']}','')\">{$mData[$i]['teridxxx']}</a>": "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['teridxxx']<> "")?f_Digito_Verificacion($mData[$i]['teridxxx']): "&nbsp;";  ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE1X'] <> "") ?  $mData[$i]['CLIAPE1X'] : "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE2X']<> "")?$mData[$i]['CLIAPE2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM1X']<> "")?$mData[$i]['CLINOM1X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM2X']<> "")?$mData[$i]['CLINOM2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOMXX']<> "")?$mData[$i]['CLINOMXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="right"><?php echo ($mData[$i]['comvlrxx'] <> "")?number_format($mData[$i]['comvlrxx'],0,",","."):"&nbsp;"; ?></td>
																<td class="letra7" align="right"><?php echo number_format(0,0,",",".") ?></td>
																<td class="letra7" align="right"><?php echo number_format(0,0,",",".") ?></td>
															</tr>
														<?php } //Fin While que recorre el cursor de la matriz generada por la consulta. ?>
													</table>
												</td>
											</tr>
										</table><br>
									<?php break;
									case "1007": ?>
										<table width="99%"  cellspacing="0" cellpadding="0" border="0">
											<tr>
												<td>
													<table border="1 cellspacing="0" cellpadding="0" width="99%"  align=center>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="4"><b>Informe de Medios Magneticos - Formato 1007</b></font>
															</td>
														</tr>
														<tr bgcolor = 'white' height="30">
															<td class="name" align="left"><font size="3">Cuenta (PUC): <?php echo $cTitCue; ?></font></td>
														</tr>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="3">Registros Analizados : <?php echo number_format(count($mData)) ?></font></td>
														</tr>
													</table>

													<table border="1" cellspacing="0" cellpadding="0" width="99%"  align=center>
														<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' height="30">
															<td class="name" width="100" align="center">Concepto</td>
															<td class="name" width="150" align="center">Tipo de documento</td>
															<td class="name" width="150" align="center">Numero identificacion del informado</td>
															<td class="name" width="50" align="center">DV</td>
															<td class="name" width="150" align="center">Primer apellido del informado</td>
															<td class="name" width="150" align="center">Segundo apellido del informado</td>
															<td class="name" width="150" align="center">Primer nombre del informado</td>
															<td class="name" width="150" align="center">Otros nombres del informado</td>
															<td class="name" align="center">Razon social informado</td>
															<td class="name" width="150" align="center">Pais de residencia o domicilio</td>
															<td class="name" width="150" align="center">Ingresos brutos recibidos por operaciones propias</td>
															<td class="name" width="150" align="center">Ingresos a traves de consorcios o uniones temporales</td>
															<td class="name" width="150" align="center">Ingresos a traves de contratos de mandato o administracion delegada</td>
															<td class="name" width="150" align="center">Ingresos a traves de exploracion y explotacion de minerales</td>
															<td class="name" width="150" align="center">Ingresos a traves de fiducias</td>
															<td class="name" width="150" align="center">Ingresos recibidos a traves de terceros</td>
															<td class="name" width="150" align="center">Devoluciones, rebajas y descuentos</td>
														</tr>
														<?php foreach ($mData as $i => $cValue) {	?>
															<tr bgcolor = "white" height="30">
																<td class="letra7" align="center">&nbsp;</td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXX'] <> "")?$mData[$i]['TDIIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['teridxxx']<> "")?"<a href=\"javascript:f_Datos_Tercero('{$mData[$i]['teridxxx']}','')\">{$mData[$i]['teridxxx']}</a>": "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['teridxxx']<> "")?f_Digito_Verificacion($mData[$i]['teridxxx']): "&nbsp;";  ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE1X'] <> "") ?  $mData[$i]['CLIAPE1X'] : "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE2X']<> "")?$mData[$i]['CLIAPE2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM1X']<> "")?$mData[$i]['CLINOM1X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM2X']<> "")?$mData[$i]['CLINOM2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOMXX']<> "")?$mData[$i]['CLINOMXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['PAIIDXXX']<> "")?$mData[$i]['PAIIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="right"><?php echo number_format($mData[$i]['IPROXXXX'],0,",",".") ?></td>
																<td class="letra7" align="right"><?php echo number_format($mData[$i]['ICONXXXX'],0,",",".") ?></td>
																<td class="letra7" align="right"><?php echo number_format($mData[$i]['IMANXXXX'],0,",",".") ?></td>
																<td class="letra7" align="right"><?php echo number_format($mData[$i]['IEXPXXXX'],0,",",".") ?></td>
																<td class="letra7" align="right"><?php echo number_format($mData[$i]['IFIDXXXX'],0,",",".") ?></td>
																<td class="letra7" align="right"><?php echo number_format($mData[$i]['ITERXXXX'],0,",",".") ?></td>
																<td class="letra7" align="right"><?php echo number_format($mData[$i]['DEVXXXXX'],0,",",".") ?></td>
															</tr>
														<?php } //Fin While que recorre el cursor de la matriz generada por la consulta. ?>
													</table>
												</td>
											</tr>
										</table><br>
									<?php break;
									case "1008": ?>
										<table width="99%"  cellspacing="0" cellpadding="0" border="0">
											<tr>
												<td>
													<table border="1 cellspacing="0" cellpadding="0" width="99%" align=center>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="4"><b>Informe de Medios Magneticos - Formato 1008</b></font></td>
														</tr>
														<tr bgcolor = 'white' height="30">
															<td class="name" align="left"><font size="3">Cuenta (PUC): <?php echo $cTitCue ?></font></td>
														</tr>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="3">Registros Analizados : <?php echo number_format(count($mData)) ?></font></td>
														</tr>
													</table>

													<table border="1" cellspacing="0" cellpadding="0" width="99%"  align=center>
														<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' height="30">
															<td class="name" width="100" align="center">Concepto</td>
															<td class="name" width="150" align="center">Tipo de documento</td>
															<td class="name" width="150" align="center">Numero identificacion deudor</td>
															<td class="name" width="50" align="center">DV</td>
															<td class="name" width="150" align="center">Primer apellido deudor</td>
															<td class="name" width="150" align="center">Segundo apellido deudor</td>
															<td class="name" width="150" align="center">Primer nombre deudor</td>
															<td class="name" width="150" align="center">Otros nombres deudor</td>
															<td class="name" align="center">Razon social deudor</td>
															<td class="name" width="300" align="center">Direccion</td>
															<td class="name" width="150" align="center">Codigo dpto.</td>
															<td class="name" width="100" align="center">Codigo mcp</td>
															<td class="name" width="150" align="center">Pais de residencia o domicilio</td>
															<td class="name" width="150" align="center">Saldo cuentas por cobrar al 31-12</td>
														</tr>
														<?php foreach ($mData as $i => $cValue) {	?>
															<tr bgcolor = "white" height="30">
																<td class="letra7" align="center">&nbsp;</td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXX'] <> "")?$mData[$i]['TDIIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['teridxxx']<> "")?"<a href=\"javascript:f_Datos_Tercero('{$mData[$i]['teridxxx']}','')\">{$mData[$i]['teridxxx']}</a>": "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['teridxxx']<> "")?f_Digito_Verificacion($mData[$i]['teridxxx']): "&nbsp;";  ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE1X'] <> "") ?  $mData[$i]['CLIAPE1X'] : "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE2X']<> "")?$mData[$i]['CLIAPE2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM1X']<> "")?$mData[$i]['CLINOM1X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM2X']<> "")?$mData[$i]['CLINOM2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOMXX']<> "")?$mData[$i]['CLINOMXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIDIRXX']<> "")?$mData[$i]['CLIDIRXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['DEPIDXXX']<> "")?$mData[$i]['DEPIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['CIUIDXXX']<> "")?$mData[$i]['CIUIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['PAIIDXXX']<> "")?$mData[$i]['PAIIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="right"> <?php echo number_format($mData[$i]['COMVLRXX'],0,",",".") ?></td>
															</tr>
														<?php } //Fin While que recorre el cursor de la matriz generada por la consulta. ?>
													</table>
												</td>
											</tr>
										</table><br>
									<?php break;
									case "1009": ?>
										<table width="99%"  cellspacing="0" cellpadding="0" border="0">
											<tr>
												<td>
													<table border="1 cellspacing="0" cellpadding="0" width="99%" align=center>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="4"><b>Informe de Medios Magneticos - Formato 1009</b></font></td>
														</tr>
														<tr bgcolor = 'white' height="30">
															<td class="name" align="left"><font size="3">Cuenta (PUC): <?php echo $cTitCue ?></font></td>
														</tr>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="3">Registros Analizados : <?php echo number_format(count($mData)) ?></font></td>
														</tr>
													</table>

													<table border="1" cellspacing="0" cellpadding="0" width="99%"  align=center>
														<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' height="30">
															<td class="name" width="100" align="center">Concepto</td>
															<td class="name" width="150" align="center">Tipo de documento</td>
															<td class="name" width="150" align="center">Numero identificacion acreedor</td>
															<td class="name" width="50" align="center">DV</td>
															<td class="name" width="150" align="center">Primer apellido acreedor</td>
															<td class="name" width="150" align="center">Segundo apellido acreedor</td>
															<td class="name" width="150" align="center">Primer nombre acreedor</td>
															<td class="name" width="150" align="center">Otros nombres acreedor</td>
															<td class="name" align="center">Razon social acreedor</td>
															<td class="name" width="300" align="center">Direccion</td>
															<td class="name" width="150" align="center">Codigo dpto.</td>
															<td class="name" width="100" align="center">Codigo mcp</td>
															<td class="name" width="150" align="center">Pais de residencia o domicilio</td>
															<td class="name" width="150" align="center">Saldo cuentas por pagar al 31-12</td>
														</tr>
														<?php foreach ($mData as $i => $cValue) {	?>
															<tr bgcolor = "white" height="30">
																<td class="letra7" align="center">&nbsp;</td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXX'] <> "")?$mData[$i]['TDIIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['teridxxx']<> "")?"<a href=\"javascript:f_Datos_Tercero('{$mData[$i]['teridxxx']}','')\">{$mData[$i]['teridxxx']}</a>": "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['teridxxx']<> "")?f_Digito_Verificacion($mData[$i]['teridxxx']): "&nbsp;";  ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE1X'] <> "") ?  $mData[$i]['CLIAPE1X'] : "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE2X']<> "")?$mData[$i]['CLIAPE2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM1X']<> "")?$mData[$i]['CLINOM1X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM2X']<> "")?$mData[$i]['CLINOM2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOMXX']<> "")?$mData[$i]['CLINOMXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIDIRXX']<> "")?$mData[$i]['CLIDIRXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['DEPIDXXX']<> "")?$mData[$i]['DEPIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['CIUIDXXX']<> "")?$mData[$i]['CIUIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['PAIIDXXX']<> "")?$mData[$i]['PAIIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="right"> <?php echo number_format($mData[$i]['COMVLRXX'],0,",",".") ?></td>
															</tr>
														<?php } //Fin While que recorre el cursor de la matriz generada por la consulta. ?>
													</table>
												</td>
											</tr>
										</table><br>
									<?php break;
									case "1012": ?>
										<table width="99%"  cellspacing="0" cellpadding="0" border="0">
											<tr>
												<td>
													<table border="1 cellspacing="0" cellpadding="0" width="99%" align=center>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="4"><b>Informe de Medios Magneticos - Formato 1012</b></font></td>
														</tr>
														<tr bgcolor = 'white' height="30">
															<td class="name" align="left"><font size="3">Cuenta (PUC): <?php echo $cTitCue ?></font></td>
														</tr>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="3">Registros Analizados : <?php echo number_format(count($mData)) ?></font></td>
														</tr>
													</table>

													<table border="1" cellspacing="0" cellpadding="0" width="99%"  align=center>
														<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' height="30">
															<td class="name" width="100" align="center">Concepto</td>
															<td class="name" width="150" align="center">Tipo de documento</td>
															<td class="name" width="150" align="center">NIT informado</td>
															<td class="name" width="50" align="center">DV</td>
															<td class="name" width="150" align="center">Primer apellido del informado</td>
															<td class="name" width="150" align="center">Segundo apellido del informado</td>
															<td class="name" width="150" align="center">Primer nombre del informado</td>
															<td class="name" width="150" align="center">Otros nombres del informado</td>
															<td class="name" align="center">Razon social informado</td>
															<td class="name" width="300" align="center">Pais de residencia o domicilio</td>
															<td class="name" width="150" align="center">Valor al 31-12 </td>
														</tr>
														<?php foreach ($mData as $i => $cValue) {	?>
															<tr bgcolor = "white" height="30">
																<td class="letra7" align="center">&nbsp;</td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXX'] <> "")?$mData[$i]['TDIIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['teridxxx']<> "")?"<a href=\"javascript:f_Datos_Tercero('{$mData[$i]['pucidxxx']}','')\">{$mData[$i]['teridxxx']}</a>": "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['teridxxx']<> "")?f_Digito_Verificacion($mData[$i]['teridxxx']): "&nbsp;";  ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE1X'] <> "") ?  $mData[$i]['CLIAPE1X'] : "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE2X']<> "")?$mData[$i]['CLIAPE2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM1X']<> "")?$mData[$i]['CLINOM1X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM2X']<> "")?$mData[$i]['CLINOM2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOMXX']<> "")?$mData[$i]['CLINOMXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['PAIIDXXX']<> "")?$mData[$i]['PAIIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="right"> <?php echo number_format($mData[$i]['COMVLRXX'],0,",",".") ?></td>
															</tr>
														<?php } //Fin While que recorre el cursor de la matriz generada por la consulta. ?>
													</table>
												</td>
											</tr>
										</table><br>
									<?php break;
									case "1016": ?>
										<table border="1" width="99%" cellspacing="0" cellpadding="0" align=center>
											<tr bgcolor = "white" height="30">
												<td class="name" align="left" colspan="30"><font size="4"><b>Informe de Medios Magneticos - Formato 1016</font></b>
												</td>
											</tr>
											<tr bgcolor = 'white' height="30">
												<td class="name" align="left" colspan="30"><font size="2">Cuenta (PUC): <?php echo $cTitCue; ?></font></td>
											</tr>
											<tr bgcolor = "white" height="30">
												<td class="name" align="left" colspan="30"><font size="2">Registros Analizados : <?php echo number_format(count($mData)) ?></font></td>
											</tr>
											<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' height="30">
												<td class="name" width="100" align="center">Concepto</td>
												<td class="name" width="050" align="center">Tipo Doc.</td>
												<td class="name" width="150" align="center">Nit</td>
												<td class="name" width="050" align="center">DV</td>
												<td class="name" width="150" align="center">Primer Apellido a quien se le hizo el pago</td>
												<td class="name" width="150" align="center">Segundo Apellido a quien se le hizo el pago</td>
												<td class="name" width="150" align="center">Primer Nombre a quien se le hizo el pago</td>
												<td class="name" width="150" align="center">Segundo Nombre a quien se le hizo el pago</td>
												<td class="name" width="300" align="center">Razon Social a quien se le hizo el pago</td>
												<td class="name" width="300" align="center">Direccion</td>
												<td class="name" width="150" align="center">Departamento</td>
												<td class="name" width="150" align="center">Municipo</td>
												<td class="name" width="150" align="center">Pais</td>
												<td class="name" width="150" align="center">Pago o abono en cta</td>
												<td class="name" width="150" align="center">IVA mayor valor del costo o gasto</td>
												<td class="name" width="150" align="center">Retencion en la fuente practicada renta</td>
												<td class="name" width="150" align="center">Retencion en la fuente asumida renta</td>
												<td class="name" width="150" align="center">Retencion en la fuente practicada iva regimen comun</td>
												<td class="name" width="150" align="center">Retencion en la fuente asumida iva regimen simp.</td>
												<td class="name" width="150" align="center">Retencion en la fuente practicada iva no domiciliados</td>
												<td class="name" width="150" align="center">Retencion en la fuente practicada CREE</td>
												<td class="name" width="150" align="center">Retencion en la fuente asumida CREE</td>
												<td class="name" width="100" align="center">Tipo Doc. del mandante o contratante</td>
												<td class="name" width="150" align="center">Nit del mandante o contratante</td>
												<td class="name" width="050" align="center">DV mandante o contratante</td>
												<td class="name" width="100" align="center">Primer Apellido mandante o contratante</td>
												<td class="name" width="100" align="center">Segundo Apellido mandante o contratante</td>
												<td class="name" width="100" align="center">Primer Nombre mandante o contratante</td>
												<td class="name" width="100" align="center">Segundo Nombre mandante o contratante</td>
												<td class="name" width="300" align="center">Razon Social mandante o contratante</td>
											</tr>
											<?php foreach ($mData as $i => $cValue) {
												##Arreglo para Aducarga y si es el concepto "1330950001" ##
												if(($cAlfa == "ADUACARX" || $cAlfa == "DEADUACARX" || $cAlfa == "TEADUACARX") && $mData[$i]['ctoidxxx'] == "1330950001"){
													$mData[$i]['terid2di'] = "800197268";
													$mData[$i]['CLINOMXX'] = $mData[$i]['CLINOMDI'];
													$mData[$i]['CLINOM1X'] = "";
													$mData[$i]['CLIAPE1X'] = "";
													$mData[$i]['CLIAPE2X'] = "";
													$mData[$i]['CLINOM2X'] = "";
												}
												##Fin Arreglo para Aducarga y si es el concepto "1330950001" ##
												if ($cAlfa == "COLMASXX" || $cAlfa == "TECOLMASXX" || $cAlfa == "DECOLMASXX") {
													//Para colmas cuando el valor de la base es negativo
													//es porque es un ajuste y el documento que se esta ajustando no esta en el periodo selecionado
													$mData[$i]['PAGOXXXX'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['PAGOXXXX'];
													$mData[$i]['IVAXXXXX'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['IVAXXXXX'];
													$mData[$i]['PRACXXXX'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['PRACXXXX'];
													$mData[$i]['ASUMXXXX'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['ASUMXXXX'];
													$mData[$i]['COMUNXXX'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['COMUNXXX'];
													$mData[$i]['SIMPLXXX'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['SIMPLXXX'];
													$mData[$i]['NDOMXXXX'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['NDOMXXXX'];
													$mData[$i]['PRACCREE'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['PRACCREE'];
													$mData[$i]['ASUMCREE'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['ASUMCREE'];
												}
											?>
												<tr bgcolor = "white" height="30">
													<td class="letra7" align="center">&nbsp;</td>
													<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXX'] <> "")?$mData[$i]['TDIIDXXX']: "&nbsp;"; ?></td>
													<td class="letra7" align="center"><?php echo ($mData[$i]['terid2xx']<> "") ?"<a href=\"javascript:f_Datos_Tercero('{$mData[$i]['CLIIDXXC']}','{$mData[$i]['terid2xx']}')\">{$mData[$i]['terid2di']}</a>": "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['terid2di']<> "") ?f_Digito_Verificacion($mData[$i]['terid2di']): "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLIAPE1X'] <> "") ?  $mData[$i]['CLIAPE1X'] : "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLIAPE2X']<> "")?$mData[$i]['CLIAPE2X']: "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLINOM1X']<> "")?$mData[$i]['CLINOM1X']: "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLINOM2X']<> "")?$mData[$i]['CLINOM2X']: "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLINOMXX']<> "")?$mData[$i]['CLINOMXX']: "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLIDIRXX']<> "")?$mData[$i]['CLIDIRXX']: "&nbsp;"; ?></td>
													<td class="letra7" align="center"><?php echo ($mData[$i]['DEPIDXXX']<> "")?$mData[$i]['DEPIDXXX']: "&nbsp;"; ?></td>
													<td class="letra7" align="center"><?php echo ($mData[$i]['CIUIDXXX']<> "")?$mData[$i]['CIUIDXXX']: "&nbsp;"; ?></td>
													<td class="letra7" align="center"><?php echo ($mData[$i]['PAIIDXXX']<> "")?$mData[$i]['PAIIDXXX']: "&nbsp;"; ?></td>
													<td class="letra7" align="right"> <?php echo number_format($mData[$i]['PAGOXXXX'],0,",",".") ?></td>
													<td class="letra7" align="right"> <?php echo number_format($mData[$i]['IVAXXXXX'],0,",",".") ?></td>
													<td class="letra7" align="right"> <?php echo number_format($mData[$i]['PRACXXXX'],0,",",".") ?></td>
													<td class="letra7" align="right"> <?php echo number_format($mData[$i]['ASUMXXXX'],0,",",".") ?></td>
													<td class="letra7" align="right"> <?php echo number_format($mData[$i]['COMUNXXX'],0,",",".") ?></td>
													<td class="letra7" align="right"> <?php echo number_format($mData[$i]['SIMPLXXX'],0,",",".") ?></td>
													<td class="letra7" align="right"> <?php echo number_format($mData[$i]['NDOMXXXX'],0,",",".") ?></td>
													<td class="letra7" align="right"> <?php echo number_format($mData[$i]['PRACCREE'],0,",",".") ?></td>
													<td class="letra7" align="right"> <?php echo number_format($mData[$i]['ASUMCREE'],0,",",".") ?></td>
													<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXC'] <> "") ? $mData[$i]['TDIIDXXC']: "&nbsp;"; ?></td>
													<td class="letra7" align="center"><?php echo ($mData[$i]['CLIIDXXC'] <> "") ? $mData[$i]['CLIIDXXC']: "&nbsp;"; ?></td>
													<td class="letra7" align="center"><?php echo ($mData[$i]['CLIIDXXC'] <> "") ? f_Digito_Verificacion($mData[$i]['CLIIDXXC']): "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLIAPE1C']<> "") ?$mData[$i]['CLIAPE1C']: "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLIAPE2C']<> "") ?$mData[$i]['CLIAPE2C']: "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLINOM1C']<> "") ?$mData[$i]['CLINOM1C']: "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLINOM2C']<> "") ?$mData[$i]['CLINOM2C']: "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLINOMXC']<> "") ?$mData[$i]['CLINOMXC']: "&nbsp;"; ?></td>
												</tr>
											<?php } //Fin While que recorre el cursor de la matriz generada por la consulta. ?>
										</table><br>
									<?php break;
									case "1018": ?>
										<table width="99%"  cellspacing="0" cellpadding="0" border="0">
											<tr>
												<td>
													<table border="1 cellspacing="0" cellpadding="0" width="99%" align=center>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="4"><b>Informe de Medios Magneticos - Formato 1018</b></font></td>
														</tr>
														<tr bgcolor = 'white' height="30">
															<td class="name" align="left"><font size="3">Cuenta (PUC): <?php echo $cTitCue ?></font></td>
														</tr>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="3">Registros Analizados : <?php echo number_format(count($mData)) ?></font></td>
														</tr>
													</table>

													<table border="1" cellspacing="0" cellpadding="0" width="99%"  align=center>
														<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' height="30">
															<td class="name" width="100" align="center">Concepto</td>
															<td class="name" width="150" align="center">Tipo de documento</td>
															<td class="name" width="150" align="center">Numero identificacion deudor</td>
															<td class="name" width="50" align="center">DV</td>
															<td class="name" width="150" align="center">Primer apellido deudor</td>
															<td class="name" width="150" align="center">Segundo apellido deudor</td>
															<td class="name" width="150" align="center">Primer nombre deudor</td>
															<td class="name" width="150" align="center">Otros nombres deudor</td>
															<td class="name" align="center">Razon social deudor</td>
															<td class="name" width="300" align="center">Direccion</td>
															<td class="name" width="150" align="center">Codigo dpto.</td>
															<td class="name" width="100" align="center">Codigo mcp</td>
															<td class="name" width="150" align="center">Pais de residencia o domicilio</td>
															<td class="name" width="150" align="center">Saldo cuentas por cobrar al 31-12</td>
															<!-- Nuevos campos para el reporte 1018 -->
															<td class="name" width="150" align="center">Tipo de Documento Mandante o contratante</td>
															<td class="name" width="150" align="center">Identificacion del mandante (Contratos de mandato)</td>
															<td class="name" width="50" align="center">DV</td>
															<td class="name" width="150" align="center">Primer apellido de mandante o contratante</td>
															<td class="name" width="150" align="center">Segundo apellido de mandante o contratante</td>
															<td class="name" width="150" align="center">Primer nombre del mandante o contratante</td>
															<td class="name" width="150" align="center">Segundo nombre del mandante o contratante</td>
															<td class="name" align="center">Razon social del mandante o contratante</td>
														</tr>
														<?php foreach ($mData as $i => $cValue) {	?>
															<tr bgcolor = "white" height="30">
																<td class="letra7" align="center">&nbsp;</td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXX'] <> "")?$mData[$i]['TDIIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['teridxxx']<> "")?"<a href=\"javascript:f_Datos_Tercero('{$mData[$i]['teridxxx']}','{$mData[$i]['terid2xx']}')\">{$mData[$i]['teridxxx']}</a>": "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['teridxxx']<> "")?f_Digito_Verificacion($mData[$i]['teridxxx']): "&nbsp;";  ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE1X'] <> "") ?  $mData[$i]['CLIAPE1X'] : "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE2X']<> "")?$mData[$i]['CLIAPE2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM1X']<> "")?$mData[$i]['CLINOM1X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM2X']<> "")?$mData[$i]['CLINOM2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOMXX']<> "")?$mData[$i]['CLINOMXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIDIRXX']<> "")?$mData[$i]['CLIDIRXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['DEPIDXXX']<> "")?$mData[$i]['DEPIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['CIUIDXXX']<> "")?$mData[$i]['CIUIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['PAIIDXXX']<> "")?$mData[$i]['PAIIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="right"> <?php echo number_format($mData[$i]['COMVLRXX'],0,",",".") ?></td>
																<!-- Nuevos campos para el reporte 1018 -->
																<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXC'] <> "")?$mData[$i]['TDIIDXXC']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['terid2xx']<> "")?"{$mData[$i]['terid2xx']}": "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['terid2xx']<> "")?f_Digito_Verificacion($mData[$i]['terid2xx']): "&nbsp;";  ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE1C'] <> "") ?  $mData[$i]['CLIAPE1C'] : "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE2C']<> "")?$mData[$i]['CLIAPE2C']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM1C']<> "")?$mData[$i]['CLINOM1C']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM2C']<> "")?$mData[$i]['CLINOM2C']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOMXC']<> "")?$mData[$i]['CLINOMXC']: "&nbsp;"; ?></td>
													</tr>
														<?php } //Fin While que recorre el cursor de la matriz generada por la consulta. ?>
													</table>
												</td>
											</tr>
										</table><br>
									<?php break;
									case "1027": ?>
										<table width="99%"  cellspacing="0" cellpadding="0" border="0">
											<tr>
												<td>
													<table border="1 cellspacing="0" cellpadding="0" width="99%" align=center>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="4"><b>Informe de Medios Magneticos - Formato 1027</b></font></td>
														</tr>
														<tr bgcolor = 'white' height="30">
															<td class="name" align="left"><font size="3">Cuenta (PUC): <?php echo $cTitCue ?></font></td>
														</tr>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="3">Registros Analizados : <?php echo number_format(count($mData)) ?></font></td>
														</tr>
													</table>

													<table border="1" cellspacing="0" cellpadding="0" width="99%"  align=center>
														<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' height="30">
															<td class="name" width="100" align="center">Concepto</td>
															<td class="name" width="150" align="center">Tipo de documento</td>
															<td class="name" width="150" align="center">Numero identificacion deudor</td>
															<td class="name" width="50" align="center">DV</td>
															<td class="name" width="150" align="center">Primer apellido deudor</td>
															<td class="name" width="150" align="center">Segundo apellido deudor</td>
															<td class="name" width="150" align="center">Primer nombre deudor</td>
															<td class="name" width="150" align="center">Otros nombres deudor</td>
															<td class="name" align="center">Razon social deudor</td>
															<td class="name" width="300" align="center">Direccion</td>
															<td class="name" width="150" align="center">Codigo dpto.</td>
															<td class="name" width="100" align="center">Codigo mcp</td>
															<td class="name" width="150" align="center">Pais de residencia o domicilio</td>
															<td class="name" width="150" align="center">Saldo cuentas por cobrar al 31-12</td>
															<!-- Nuevos campos para el reporte 1027 -->
															<td class="name" width="150" align="center">Tipo de Documento Mandante o contratante</td>
															<td class="name" width="150" align="center">Identificacion del mandante (Contratos de mandato) y/o identificacion del patrimonio autonomo</td>
															<td class="name" width="50" align="center">DV</td>
															<td class="name" width="150" align="center">Primer apellido de mandante o contratante</td>
															<td class="name" width="150" align="center">Segundo apellido de mandante o contratante</td>
															<td class="name" width="150" align="center">Primer nombre del mandante o contratante</td>
															<td class="name" width="150" align="center">Segundo nombre del mandante o contratante</td>
															<td class="name" align="center">Razon social del mandante o contratante</td>
														</tr>
														<?php foreach ($mData as $i => $cValue) {	?>
															<tr bgcolor = "white" height="30">
																<td class="letra7" align="center">&nbsp;</td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXX'] <> "")?$mData[$i]['TDIIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['teridxxx']<> "")?"<a href=\"javascript:f_Datos_Tercero('{$mData[$i]['teridxxx']}','{$mData[$i]['terid2xx']}')\">{$mData[$i]['teridxxx']}</a>": "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['teridxxx']<> "")?f_Digito_Verificacion($mData[$i]['teridxxx']): "&nbsp;";  ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE1X'] <> "") ?  $mData[$i]['CLIAPE1X'] : "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE2X']<> "")?$mData[$i]['CLIAPE2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM1X']<> "")?$mData[$i]['CLINOM1X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM2X']<> "")?$mData[$i]['CLINOM2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOMXX']<> "")?$mData[$i]['CLINOMXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIDIRXX']<> "")?$mData[$i]['CLIDIRXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['DEPIDXXX']<> "")?$mData[$i]['DEPIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['CIUIDXXX']<> "")?$mData[$i]['CIUIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['PAIIDXXX']<> "")?$mData[$i]['PAIIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="right"> <?php echo number_format($mData[$i]['COMVLRXX'],0,",",".") ?></td>
																<!-- Nuevos campos para el reporte 1027 -->
																<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXC'] <> "")?$mData[$i]['TDIIDXXC']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['terid2xx']<> "")?"{$mData[$i]['terid2xx']}": "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['terid2xx']<> "")?f_Digito_Verificacion($mData[$i]['terid2xx']): "&nbsp;";  ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE1C'] <> "") ?  $mData[$i]['CLIAPE1C'] : "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE2C']<> "")?$mData[$i]['CLIAPE2C']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM1C']<> "")?$mData[$i]['CLINOM1C']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM2C']<> "")?$mData[$i]['CLINOM2C']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOMXC']<> "")?$mData[$i]['CLINOMXC']: "&nbsp;"; ?></td>
													</tr>
														<?php } //Fin While que recorre el cursor de la matriz generada por la consulta. ?>
													</table>
												</td>
											</tr>
										</table><br>
									<?php break;
									case "1054": ?>
										<table border="1" width="99%" cellspacing="0" cellpadding="0" align=center>
											<tr bgcolor = "white" height="30">
												<td class="name" align="left" colspan="18"><font size="4"><b>Informe de Medios Magneticos - Formato 1054</font></b>
												</td>
											</tr>
											<tr bgcolor = 'white' height="30">
												<td class="name" align="left" colspan="18"><font size="2">Cuenta (PUC): <?php echo $cTitCue; ?></font></td>
											</tr>
											<tr bgcolor = "white" height="30">
												<td class="name" align="left" colspan="18"><font size="2">Registros Analizados : <?php echo number_format(count($mData)) ?></font></td>
											</tr>
											<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' height="30">
												<td class="name" width="050" align="center">Tipo Doc.</td>
												<td class="name" width="150" align="center">Nit del informado</td>
												<td class="name" width="050" align="center">DV del informado</td>
												<td class="name" width="150" align="center">Primer apellido del informado</td>
												<td class="name" width="150" align="center">Segundo apellido del informado</td>
												<td class="name" width="150" align="center">Primer nombre del informado</td>
												<td class="name" width="150" align="center">Otros nombres del informado</td>
												<td class="name" width="300" align="center">Razon social informado</td>
												<td class="name" width="150" align="center">Impuesto descontable</td>
												<td class="name" width="150" align="center">IVA resultante por devoluciones en ventas anuladas rescindidas o resueltas</td>
												<td class="name" width="100" align="center">Tipo Doc.</td>
												<td class="name" width="150" align="center">Nit del mandante o contratante</td>
												<td class="name" width="050" align="center">DV del mandante o contratante</td>
												<td class="name" width="100" align="center">Primer Apellido del mandante o contratante</td>
												<td class="name" width="100" align="center">Segundo Apellido del mandante o contratante</td>
												<td class="name" width="100" align="center">Primer Nombre del mandante o contratante</td>
												<td class="name" width="100" align="center">Segundo Nombre del mandante o contratante</td>
												<td class="name" width="300" align="center">Razon Social del mandante o contratante</td>
											</tr>
											<?php foreach ($mData as $i => $cValue) {
												##Arreglo para Aducarga y si es el concepto "1330950001" ##
												if(($cAlfa == "ADUACARX" || $cAlfa == "DEADUACARX" || $cAlfa == "TEADUACARX") && $mData[$i]['ctoidxxx'] == "1330950001"){
													$mData[$i]['terid2di'] = "800197268";
													$mData[$i]['CLINOMXX'] = $mData[$i]['CLINOMDI'];
													$mData[$i]['CLINOM1X'] = "";
													$mData[$i]['CLIAPE1X'] = "";
													$mData[$i]['CLIAPE2X'] = "";
													$mData[$i]['CLINOM2X'] = "";
												}
												##Fin Arreglo para Aducarga y si es el concepto "1330950001" ##
											?>
												<tr bgcolor = "white" height="30">
													<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXX'] <> "")?$mData[$i]['TDIIDXXX']: "&nbsp;"; ?></td>
													<td class="letra7" align="center"><?php echo ($mData[$i]['terid2xx']<> "") ?"<a href=\"javascript:f_Datos_Tercero('{$mData[$i]['CLIIDXXC']}','{$mData[$i]['terid2xx']}')\">{$mData[$i]['terid2di']}</a>": "&nbsp;"; ?></td>
													<td class="letra7" align="center"><?php echo ($mData[$i]['terid2di']<> "") ?f_Digito_Verificacion($mData[$i]['terid2di']): "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLIAPE1X'] <> "") ?  $mData[$i]['CLIAPE1X'] : "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLIAPE2X']<> "")?$mData[$i]['CLIAPE2X']: "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLINOM1X']<> "")?$mData[$i]['CLINOM1X']: "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLINOM2X']<> "")?$mData[$i]['CLINOM2X']: "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLINOMXX']<> "")?$mData[$i]['CLINOMXX']: "&nbsp;"; ?></td>
													<td class="letra7" align="right"> <?php echo number_format($mData[$i]['IMPDXXXX'],0,",",".") ?></td>
													<td class="letra7" align="right"> <?php echo number_format($mData[$i]['IVARXXXX'],0,",",".") ?></td>
													<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXC'] <> "") ? $mData[$i]['TDIIDXXC']: "&nbsp;"; ?></td>
													<td class="letra7" align="center"><?php echo ($mData[$i]['CLIIDXXC'] <> "") ? $mData[$i]['CLIIDXXC']: "&nbsp;"; ?></td>
													<td class="letra7" align="center"><?php echo ($mData[$i]['CLIIDXXC'] <> "") ? f_Digito_Verificacion($mData[$i]['CLIIDXXC']): "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLIAPE1C']<> "") ?$mData[$i]['CLIAPE1C']: "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLIAPE2C']<> "") ?$mData[$i]['CLIAPE2C']: "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLINOM1C']<> "") ?$mData[$i]['CLINOM1C']: "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLINOM2C']<> "") ?$mData[$i]['CLINOM2C']: "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLINOMXC']<> "") ?$mData[$i]['CLINOMXC']: "&nbsp;"; ?></td>
												</tr>
											<?php } //Fin While que recorre el cursor de la matriz generada por la consulta. ?>
										</table><br>
									<?php break; 

									case "5247": // Nuevo 5247 ?>
										<table border="1" width="99%" cellspacing="0" cellpadding="0" align=center>
											<tr bgcolor = "white" height="30">
												<td class="name" align="left" colspan="30"><font size="4"><b>Pagos o Retenciones - Formato 5247</font></b>
												</td>
											</tr>
											<tr bgcolor = 'white' height="30">
												<td class="name" align="left" colspan="30"><font size="2">Cuenta (PUC): <?php echo $cTitCue; ?></font></td>
											</tr>
											<tr bgcolor = "white" height="30">
												<td class="name" align="left" colspan="30"><font size="2">Registros Analizados : <?php echo number_format(count($mData)) ?></font></td>
											</tr>
											<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' height="30">
												<td class="name" width="100" align="center">Tipo Contrato</td>
												<td class="name" width="100" align="center">Concepto</td>
												<td class="name" width="100" align="center">Tipo Doc.</td>
												<td class="name" width="150" align="center">Numero de identificacion</td>
												<td class="name" width="150" align="center">Primer Apellido del informado</td>
												<td class="name" width="150" align="center">Segundo Apellido del informado</td>
												<td class="name" width="150" align="center">Primer Nombre del informado</td>
												<td class="name" width="150" align="center">Otros Nombres del informado</td>
												<td class="name" width="200" align="center">Razon Social del informado</td>
												<td class="name" width="200" align="center">Direccion</td>
												<td class="name" width="150" align="center">Codigo dpto.</td>
												<td class="name" width="150" align="center">Codigo mcp</td>
												<td class="name" width="150" align="center">Pais</td>
												<td class="name" width="150" align="center">Pago o abono en cta</td>
												<td class="name" width="150" align="center">IVA mayor valor del costo o gasto</td>
												<td class="name" width="150" align="center">Retencion en la fuente practicada renta</td>
												<td class="name" width="150" align="center">Retencion en la fuente asumida renta</td>
												<td class="name" width="150" align="center">Retencion en la fuente practicada iva regimen comun</td>
												<td class="name" width="150" align="center">Retencion en la fuente practicada iva no domiciliados</td>
												<td class="name" width="150" align="center">Identificacion del fideicomiso</td>
												<td class="name" width="150" align="center">Tipo Doc. del mandante o contratante</td>
												<td class="name" width="150" align="center">Nit del mandante o contratante</td>
											</tr>
											<?php foreach ($mData as $i => $cValue) {
												##Arreglo para Aducarga y si es el concepto "1330950001" ##
												if(($cAlfa == "ADUACARX" || $cAlfa == "DEADUACARX" || $cAlfa == "TEADUACARX") && $mData[$i]['ctoidxxx'] == "1330950001"){
													$mData[$i]['terid2di'] = "800197268";
													$mData[$i]['CLINOMXX'] = $mData[$i]['CLINOMDI'];
													$mData[$i]['CLINOM1X'] = "";
													$mData[$i]['CLIAPE1X'] = "";
													$mData[$i]['CLIAPE2X'] = "";
													$mData[$i]['CLINOM2X'] = "";
												}
												##Fin Arreglo para Aducarga y si es el concepto "1330950001" ##
												if ($cAlfa == "COLMASXX" || $cAlfa == "TECOLMASXX" || $cAlfa == "DECOLMASXX") {
													//Para colmas cuando el valor de la base es negativo
													//es porque es un ajuste y el documento que se esta ajustando no esta en el periodo selecionado
													$mData[$i]['PAGOXXXX'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['PAGOXXXX'];
													$mData[$i]['IVAXXXXX'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['IVAXXXXX'];
													$mData[$i]['PRACXXXX'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['PRACXXXX'];
													$mData[$i]['ASUMXXXX'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['ASUMXXXX'];
													$mData[$i]['COMUNXXX'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['COMUNXXX'];
													$mData[$i]['NDOMXXXX'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['NDOMXXXX'];
												}
								
											?>
												<tr bgcolor = "white" height="30">
													<td class="letra7" align="center"></td>
													<td class="letra7" align="center"></td>
													<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXX'] <> "")?$mData[$i]['TDIIDXXX']: "&nbsp;"; ?></td>
													<td class="letra7" align="center"><?php echo ($mData[$i]['terid2xx']<> "") ?"<a href=\"javascript:f_Datos_Tercero('{$mData[$i]['CLIIDXXC']}','{$mData[$i]['terid2xx']}')\">{$mData[$i]['terid2di']}</a>": "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLIAPE1X'] <> "") ?  $mData[$i]['CLIAPE1X'] : "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLIAPE2X']<> "")?$mData[$i]['CLIAPE2X']: "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLINOM1X']<> "")?$mData[$i]['CLINOM1X']: "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLINOM2X']<> "")?$mData[$i]['CLINOM2X']: "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLINOMXX']<> "")?$mData[$i]['CLINOMXX']: "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLIDIRXX']<> "")?$mData[$i]['CLIDIRXX']: "&nbsp;"; ?></td>
													<td class="letra7" align="center"><?php echo ($mData[$i]['DEPIDXXX']<> "")?$mData[$i]['DEPIDXXX']: "&nbsp;"; ?></td>
													<td class="letra7" align="center"><?php echo ($mData[$i]['CIUIDXXX']<> "")?$mData[$i]['CIUIDXXX']: "&nbsp;"; ?></td>
													<td class="letra7" align="center"><?php echo ($mData[$i]['PAIIDXXX']<> "")?$mData[$i]['PAIIDXXX']: "&nbsp;"; ?></td>
													<td class="letra7" align="right"> <?php echo number_format($mData[$i]['PAGOXXXX'],0,",",".") ?></td>
													<td class="letra7" align="right"> <?php echo number_format($mData[$i]['IVAXXXXX'],0,",",".") ?></td>
													<td class="letra7" align="right"> <?php echo number_format($mData[$i]['PRACXXXX'],0,",",".") ?></td>
													<td class="letra7" align="right"> <?php echo number_format($mData[$i]['ASUMXXXX'],0,",",".") ?></td>
													<td class="letra7" align="right"> <?php echo number_format($mData[$i]['COMUNXXX'],0,",",".") ?></td>
													<td class="letra7" align="right"> <?php echo number_format($mData[$i]['NDOMXXXX'],0,",",".") ?></td>
													<td class="letra7" align="center">0</td>
													<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXC'] <> "") ? $mData[$i]['TDIIDXXC']: "&nbsp;"; ?></td>
													<td class="letra7" align="center"><?php echo ($mData[$i]['CLIIDXXC'] <> "") ? $mData[$i]['CLIIDXXC']: "&nbsp;"; ?></td>
												</tr>
											<?php } //Fin While que recorre el cursor de la matriz generada por la consulta. ?>
										</table><br>
									<?php break;

									case "5248": // Nuevo 5248 ?>
										<table width="99%"  cellspacing="0" cellpadding="0" border="0">
											<tr>
												<td>
													<table border="1 cellspacing="0" cellpadding="0" width="99%"  align=center>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="4"><b>Informe de Medios Magneticos - Formato 5248</b></font>
															</td>
														</tr>
														<tr bgcolor = 'white' height="30">
															<td class="name" align="left"><font size="3">Cuenta (PUC): <?php echo $cTitCue; ?></font></td>
														</tr>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="3">Registros Analizados : <?php echo number_format(count($mData)) ?></font></td>
														</tr>
													</table>

													<table border="1" cellspacing="0" cellpadding="0" width="99%"  align=center>
														<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' height="30">
															<td class="name" width="100" align="center">Tipo de contrato</td>
															<td class="name" width="100" align="center">Concepto</td>
															<td class="name" width="150" align="center">Tipo de documento</td>
															<td class="name" width="150" align="center">Numero identificacion del informado</td>
															<td class="name" width="150" align="center">Primer apellido</td>
															<td class="name" width="150" align="center">Segundo apellido</td>
															<td class="name" width="150" align="center">Primer nombre</td>
															<td class="name" width="150" align="center">Otros nombres</td>
															<td class="name" width="200" align="center">Razon social</td>
															<td class="name" width="150" align="center">Pais de residencia o domicilio</td>
															<td class="name" width="150" align="center">Ingreso bruto recibido</td>
															<td class="name" width="150" align="center">Devoluciones, rebajas y descuentos</td>
															<td class="name" width="150" align="center">Identificacion del fideicomiso</td>
															<td class="name" width="150" align="center">Tipo documento del participante en contrato</td>
															<td class="name" width="150" align="center">Identificacion del participante en contrato</td>

														</tr>
														<?php foreach ($mData as $i => $cValue) {	?>
															<tr bgcolor = "white" height="30">
																<td class="letra7" align="center">&nbsp;</td>
																<td class="letra7" align="center">&nbsp;</td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXX'] <> "")?$mData[$i]['TDIIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['terid2xx']<> "")?"<a href=\"javascript:f_Datos_Tercero('{$mData[$i]['terid2xx']}','')\">{$mData[$i]['terid2xx']}</a>": "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE1X'] <> "") ?  $mData[$i]['CLIAPE1X'] : "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE2X']<> "")?$mData[$i]['CLIAPE2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM1X']<> "")?$mData[$i]['CLINOM1X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM2X']<> "")?$mData[$i]['CLINOM2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOMXX']<> "")?$mData[$i]['CLINOMXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['PAIIDXXX']<> "")?$mData[$i]['PAIIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="right"><?php echo number_format($mData[$i]['IPROXXXX'],0,",",".") ?></td>
																<td class="letra7" align="right"><?php echo number_format($mData[$i]['DEVXXXXX'],0,",",".") ?></td>
																<td class="letra7" align="center">0</td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXC'] <> "")?$mData[$i]['TDIIDXXC']: "&nbsp;"?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['CLIIDXXC'] <> "")?$mData[$i]['CLIIDXXC']: "&nbsp;"?></td>
															</tr>
														<?php } //Fin While que recorre el cursor de la matriz generada por la consulta. ?>
													</table>
												</td>
											</tr>
										</table><br>
									<?php break;

									case "5249": // Nuevo 5249 ?>
										<table border="1" width="99%" cellspacing="0" cellpadding="0" align=center>
											<tr bgcolor = "white" height="30">
												<td class="name" align="left" colspan="18"><font size="4"><b>Informe de Medios Magneticos - Formato 5249</font></b>
												</td>
											</tr>
											<tr bgcolor = 'white' height="30">
												<td class="name" align="left" colspan="18"><font size="2">Cuenta (PUC): <?php echo $cTitCue; ?></font></td>
											</tr>
											<tr bgcolor = "white" height="30">
												<td class="name" align="left" colspan="18"><font size="2">Registros Analizados : <?php echo number_format(count($mData)) ?></font></td>
											</tr>
											<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' height="30">
												<td class="name" width="100" align="center">Tipo Contrato</td>
												<td class="name" width="100" align="center">Tipo de documento</td>
												<td class="name" width="150" align="center">Numero de identificacion</td>
												<td class="name" width="150" align="center">Primer apellido del informado</td>
												<td class="name" width="150" align="center">Segundo apellido del informado</td>
												<td class="name" width="150" align="center">Primer nombre del informado</td>
												<td class="name" width="150" align="center">Otros nombres del informado</td>
												<td class="name" width="200" align="center">Razon social informado</td>
												<td class="name" width="150" align="center">Impuesto descontable</td>
												<td class="name" width="150" align="center">IVA descontable por devoluciones en ventas</td>
												<td class="name" width="100" align="center">Tipo documento del participante en contrato</td>
												<td class="name" width="150" align="center">Identificacion del participante en contrato</td>
											</tr>
											<?php foreach ($mData as $i => $cValue) {
												##Arreglo para Aducarga y si es el concepto "1330950001" ##
												if(($cAlfa == "ADUACARX" || $cAlfa == "DEADUACARX" || $cAlfa == "TEADUACARX") && $mData[$i]['ctoidxxx'] == "1330950001"){
													$mData[$i]['terid2di'] = "800197268";
													$mData[$i]['CLIAPE1X'] = "";
													$mData[$i]['CLIAPE2X'] = "";
													$mData[$i]['CLINOM1X'] = "";
													$mData[$i]['CLINOM2X'] = "";
													$mData[$i]['CLINOMXX'] = $mData[$i]['CLINOMDI'];
												}
												##Fin Arreglo para Aducarga y si es el concepto "1330950001" ##
											?>
												<tr bgcolor = "white" height="30">
													<td class="letra7" align="center">&nbsp;</td>
													<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXX'] <> "")?$mData[$i]['TDIIDXXX']: "&nbsp;"; ?></td>
													<td class="letra7" align="center"><?php echo ($mData[$i]['terid2xx']<> "") ?"<a href=\"javascript:f_Datos_Tercero('{$mData[$i]['CLIIDXXC']}','{$mData[$i]['terid2xx']}')\">{$mData[$i]['terid2di']}</a>": "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLIAPE1X'] <> "") ?  $mData[$i]['CLIAPE1X'] : "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLIAPE2X']<> "")?$mData[$i]['CLIAPE2X']: "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLINOM1X']<> "")?$mData[$i]['CLINOM1X']: "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLINOM2X']<> "")?$mData[$i]['CLINOM2X']: "&nbsp;"; ?></td>
													<td class="letra7" align="left">  <?php echo ($mData[$i]['CLINOMXX']<> "")?$mData[$i]['CLINOMXX']: "&nbsp;"; ?></td>
													<td class="letra7" align="right"> <?php echo number_format($mData[$i]['IMPDXXXX'],0,",",".") ?></td>
													<td class="letra7" align="right"> <?php echo number_format($mData[$i]['IVARXXXX'],0,",",".") ?></td>
													<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXC'] <> "") ? $mData[$i]['TDIIDXXC']: "&nbsp;"; ?></td>
													<td class="letra7" align="center"><?php echo ($mData[$i]['CLIIDXXC'] <> "") ? $mData[$i]['CLIIDXXC']: "&nbsp;"; ?></td>
												</tr>
											<?php } //Fin While que recorre el cursor de la matriz generada por la consulta. ?>
										</table><br>
									<?php break; 

									case "5250": // Nuevo 5250 ?>
										<table width="99%"  cellspacing="0" cellpadding="0" border="0">
											<tr>
												<td>
													<table border="1 cellspacing="0" cellpadding="0" width="99%"  align=center>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="4"><b>Informe de Medios Magneticos - Formato 5250</b></font>
															</td>
														</tr>
														<tr bgcolor = 'white' height="30">
															<td class="name" align="left"><font size="3">Cuenta (PUC): <?php echo $cTitCue; ?></font></td>
														</tr>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="3">Registros Analizados : <?php echo number_format(count($mData)) ?></font></td>
														</tr>
													</table>

													<table border="1" cellspacing="0" cellpadding="0" width="99%"  align=center>
														<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' height="30">
															<td class="name" width="100" align="center">Tipo de contrato</td>
															<td class="name" width="150" align="center">Tipo de documento</td>
															<td class="name" width="150" align="center">Numero identificacion</td>
															<td class="name" width="150" align="center">Primer apellido del informado</td>
															<td class="name" width="150" align="center">Segundo apellido del informado</td>
															<td class="name" width="150" align="center">Primer nombre del informado</td>
															<td class="name" width="150" align="center">Otros nombres del informado</td>
															<td class="name" align="center">Razon social del informado</td>
															<td class="name" width="150" align="center">Impuesto generado</td>
															<td class="name" width="150" align="center">Iva generado por devoluciones de compras</td>
															<td class="name" width="150" align="center">Impuesto al consumo</td>
															<td class="name" width="150" align="center">Tipo documento participante en contrato</td>
															<td class="name" width="150" align="center">Identificacion del participante en contrato</td>

														</tr>
														<?php foreach ($mData as $i => $cValue) {	?>
															<tr bgcolor = "white" height="30">
																<td class="letra7" align="center">&nbsp;</td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXX'] <> "")?$mData[$i]['TDIIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['terid2xx']<> "")?"<a href=\"javascript:f_Datos_Tercero('{$mData[$i]['terid2xx']}','')\">{$mData[$i]['terid2xx']}</a>": "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE1X'] <> "") ?  $mData[$i]['CLIAPE1X'] : "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE2X']<> "")?$mData[$i]['CLIAPE2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM1X']<> "")?$mData[$i]['CLINOM1X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM2X']<> "")?$mData[$i]['CLINOM2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOMXX']<> "")?$mData[$i]['CLINOMXX']: "&nbsp;"; ?></td>
												
																<td class="letra7" align="right"><?php echo number_format($mData[$i]['IMPGENXX'],0,",",".") ?></td>
																<td class="letra7" align="right"><?php echo number_format($mData[$i]['IVADEXXX'],0,",",".") ?></td>
																<td class="letra7" align="right"><?php echo number_format($mData[$i]['IMPCONXX'],0,",",".") ?></td>
															
																<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXC'] <> "")?$mData[$i]['TDIIDXXC']: "&nbsp;"?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['CLIIDXXC'] <> "")?$mData[$i]['CLIIDXXC']: "&nbsp;"?></td> 
															</tr>
														<?php } //Fin While que recorre el cursor de la matriz generada por la consulta. ?>
													</table>
												</td>
											</tr>
										</table><br>
									<?php break;

									case "5251": // Nuevo 5251 ?>
										<table width="99%"  cellspacing="0" cellpadding="0" border="0">
											<tr>
												<td>
													<table border="1 cellspacing="0" cellpadding="0" width="99%" align=center>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="4"><b>Informe de Medios Magneticos - Formato 5251</b></font></td>
														</tr>
														<tr bgcolor = 'white' height="30">
															<td class="name" align="left"><font size="3">Cuenta (PUC): <?php echo $cTitCue ?></font></td>
														</tr>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="3">Registros Analizados : <?php echo number_format(count($mData)) ?></font></td>
														</tr>
													</table>

													<table border="1" cellspacing="0" cellpadding="0" width="99%"  align=center>
														<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' height="30">
															<td class="name" width="100" align="center">Tipo de contrato</td>
															<td class="name" width="100" align="center">Concepto</td>
															<td class="name" width="100" align="center">Tipo de documento</td>
															<td class="name" width="150" align="center">Numero identificacion del informado</td>
															<td class="name" width="150" align="center">Primer apellido</td>
															<td class="name" width="150" align="center">Segundo apellido</td>
															<td class="name" width="150" align="center">Primer nombre</td>
															<td class="name" width="150" align="center">Otros nombres</td>
															<td class="name" align="center">Razon social</td>
															<td class="name" width="300" align="center">Direccion</td>
															<td class="name" width="150" align="center">Codigo dpto.</td>
															<td class="name" width="100" align="center">Codigo mcp</td>
															<td class="name" width="150" align="center">Pais de residencia o domicilio</td>
															<td class="name" width="150" align="center">Saldo de la CXC a diciembre 31</td>
															<!-- Nuevos campos para el reporte 1018 -->
															<td class="name" width="150" align="center">Tipo de Documento del participante en contrato</td>
															<td class="name" width="150" align="center">Identificacion del participante en contrato</td>
														</tr>
														<?php foreach ($mData as $i => $cValue) {	?>
															<tr bgcolor = "white" height="30">
																<td class="letra7" align="center">&nbsp;</td>
																<td class="letra7" align="center">&nbsp;</td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXX'] <> "")?$mData[$i]['TDIIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['terid2xx']<> "")?"<a href=\"javascript:f_Datos_Tercero('{$mData[$i]['terid2xx']}','{$mData[$i]['teridxxx']}')\">{$mData[$i]['terid2xx']}</a>": "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE1X'] <> "") ?$mData[$i]['CLIAPE1X'] : "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE2X']<> "")?$mData[$i]['CLIAPE2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM1X']<> "")?$mData[$i]['CLINOM1X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM2X']<> "")?$mData[$i]['CLINOM2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOMXX']<> "")?$mData[$i]['CLINOMXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIDIRXX']<> "")?$mData[$i]['CLIDIRXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['DEPIDXXX']<> "")?$mData[$i]['DEPIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['CIUIDXXX']<> "")?$mData[$i]['CIUIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['PAIIDXXX']<> "")?$mData[$i]['PAIIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="right"> <?php echo number_format($mData[$i]['COMVLRXX'],0,",",".") ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXC'] <> "")?$mData[$i]['TDIIDXXC']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['teridxxx']<> "")?"{$mData[$i]['teridxxx']}": "&nbsp;"; ?></td>

															</tr>
														<?php } //Fin While que recorre el cursor de la matriz generada por la consulta. ?>
													</table>
												</td>
											</tr>
										</table><br>
									<?php break;

									case "5252": // Nuevo 5252 ?>
										<table width="99%"  cellspacing="0" cellpadding="0" border="0">
											<tr>
												<td>
													<table border="1 cellspacing="0" cellpadding="0" width="99%" align=center>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="4"><b>Informe de Medios Magneticos - Formato 5252</b></font></td>
														</tr>
														<tr bgcolor = 'white' height="30">
															<td class="name" align="left"><font size="3">Cuenta (PUC): <?php echo $cTitCue ?></font></td>
														</tr>
														<tr bgcolor = "white" height="30">
															<td class="name" align="left"><font size="3">Registros Analizados : <?php echo number_format(count($mData)) ?></font></td>
														</tr>
													</table>

													<table border="1" cellspacing="0" cellpadding="0" width="99%"  align=center>
														<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' height="30">
															<td class="name" width="100" align="center">Tipo de contrato</td>
															<td class="name" width="100" align="center">Concepto</td>
															<td class="name" width="150" align="center">Tipo de documento</td>
															<td class="name" width="150" align="center">Numero identificacion del informado</td>
															<td class="name" width="150" align="center">Primer apellido</td>
															<td class="name" width="150" align="center">Segundo apellido</td>
															<td class="name" width="150" align="center">Primer nombre</td>
															<td class="name" width="150" align="center">Otros nombres</td>
															<td class="name" align="center">Razon social</td>
															<td class="name" width="300" align="center">Direccion</td>
															<td class="name" width="150" align="center">Codigo dpto.</td>
															<td class="name" width="100" align="center">Codigo mcp</td>
															<td class="name" width="150" align="center">Pais de residencia o domicilio</td>
															<td class="name" width="150" align="center">Saldo de las CXP a diciembre 31</td>
															<td class="name" width="150" align="center">Tipo de Documento del participante en contrato</td>
															<td class="name" width="150" align="center">Identificacion del participante en contrato</td>        	    				
														</tr>
														<?php foreach ($mData as $i => $cValue) {	?>
															<tr bgcolor = "white" height="30">
																<td class="letra7" align="center">&nbsp;</td>
																<td class="letra7" align="center">&nbsp;</td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXX'] <> "")?$mData[$i]['TDIIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['teridxxx']<> "")?"<a href=\"javascript:f_Datos_Tercero('{$mData[$i]['teridxxx']}','{$mData[$i]['terid2xx']}')\">{$mData[$i]['teridxxx']}</a>": "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE1X'] <> "") ?  $mData[$i]['CLIAPE1X'] : "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIAPE2X']<> "")?$mData[$i]['CLIAPE2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM1X']<> "")?$mData[$i]['CLINOM1X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOM2X']<> "")?$mData[$i]['CLINOM2X']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLINOMXX']<> "")?$mData[$i]['CLINOMXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="left"><?php echo ($mData[$i]['CLIDIRXX']<> "")?$mData[$i]['CLIDIRXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['DEPIDXXX']<> "")?$mData[$i]['DEPIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['CIUIDXXX']<> "")?$mData[$i]['CIUIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['PAIIDXXX']<> "")?$mData[$i]['PAIIDXXX']: "&nbsp;"; ?></td>
																<td class="letra7" align="right"> <?php echo number_format($mData[$i]['COMVLRXX'],0,",",".") ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['TDIIDXXC'] <> "")?$mData[$i]['TDIIDXXC']: "&nbsp;"; ?></td>
																<td class="letra7" align="center"><?php echo ($mData[$i]['terid2xx']<> "")?"{$mData[$i]['terid2xx']}": "&nbsp;"; ?></td>
															</tr>
														<?php } //Fin While que recorre el cursor de la matriz generada por la consulta. ?>
													</table>
												</td>
											</tr>
										</table><br>
									<?php break;
										
								} // Fin switch
								?>
							</center>
						</form>
					</body>
				</html>
			<?php
			break;
			case 2:
				// PINTA POR EXCEL //Reporte de Estado de Cuenta Tramites

				$cNomFile = "MEDIOS_MAGNETICOS".$_COOKIE['kUsrId'].date("YmdHis").".xls";

				if ($_SERVER["SERVER_PORT"] != "") {
					$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;
				} else {
					$cFile = "{$OPENINIT['pathdr']}/opencomex/" . $vSysStr['system_download_directory'] . "/" . $cNomFile;
				}
				
				$fOp = fopen($cFile, 'a');

				$header .= 'MEDIOS MAGNETICOS'."\n";
				$header .= "\n";
				$data    = '';

				switch ($gFormato) {
					case "1001":
						$data .= '<table cellspacing="0" cellpadding="0" border="1">';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="24"><b>Informe de Medios Magneticos - Formato 1001</b></td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="24">Cuenta (PUC) :'.$cTitCue.'</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="24">Registros Analizados :'. number_format(count($mData)).'</td>';
							$data .= '</tr>';
								$data .= '<td width="100" align="center">Concepto</td>';
								$data .= '<td width="150" align="center">Tipo de documento</td>';
								$data .= '<td width="150" align="center">Numero identificacion del informado</td>';
								$data .= '<td width="050" align="center">DV</td>';
								$data .= '<td width="150" align="center">Primer apellido del informado</td>';
								$data .= '<td width="150" align="center">Segundo apellido del informado</td>';
								$data .= '<td width="150" align="center">Primer nombre del informado</td>';
								$data .= '<td width="150" align="center">Otros nombres del informado</td>';
								$data .= '<td width="300" align="center">Razon social informado</td>';
								$data .= '<td width="300" align="center">Direccion</td>';
								$data .= '<td width="150" align="center">Codigo dpto.</td>';
								$data .= '<td width="100" align="center">Codigo mcp</td>';
								$data .= '<td width="100" align="center">Pais de residencia o domicilio</td>';
								$data .= '<td width="150" align="center">Pago o abono en cuenta deducible</td>';
								$data .= '<td width="150" align="center">Pago o abono en cuenta no deducible</td>';
								$data .= '<td width="150" align="center">IVA mayor valor del costo o gasto deducible</td>';
								$data .= '<td width="150" align="center">Iva mayor valor del costo o gasto no deducible</td>';
								$data .= '<td width="150" align="center">Retencion en la fuente practicada renta</td>';
								$data .= '<td width="150" align="center">Retencion en la fuente asumida renta</td>';
								$data .= '<td width="150" align="center">Retencion en la fuente practicada IVA regimen comun</td>';
								$data .= '<td width="150" align="center">Retencion en la fuente asumida IVA regimen simplificado</td>';
								$data .= '<td width="150" align="center">Retencion en la fuente practicada IVA no domiciliados</td>';
								$data .= '<td width="150" align="center">Retencion en la fuente practicadas CREE</td>';
								$data .= '<td width="150" align="center">Retencion en la fuente asumidas CREE</td>';
							$data .= '</tr>';

							foreach ($mData as $i => $cValue) {
								$data .= '<tr>';
									$data .= '<td align="center"></td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXX'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['teridxxx'].'</td>';
									$data .= '<td align="center">'.f_Digito_Verificacion($mData[$i]['teridxxx']).'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE1X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM1X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOMXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIDIRXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['DEPIDXXX'].'</td>';
									$data .= '<td align="left"style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CIUIDXXX'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['PAIIDXXX'].'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['PAGODEXX'],0,",",".")).'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['PAGONODE'],0,",",".")).'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['IVAXXXXX'],0,",",".")).'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['IVANOXXX'],0,",",".")).'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['PRACXXXX'],0,",",".")).'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['ASUMXXXX'],0,",",".")).'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['COMUNXXX'],0,",",".")).'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['SIMPLXXX'],0,",",".")).'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['NDOMXXXX'],0,",",".")).'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['PRACCREE'],0,",",".")).'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['ASUMCREE'],0,",",".")).'</td>';
								$data .= '</tr>';
							} //Fin While que recorre el cursor de la matriz generada por la consulta.
						$data .= '</table>';
					break;
					case "1003":
						$data .= '<table cellspacing="0" cellpadding="0" border="1">';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="14"><b>Informe de Medios Magneticos - Formato 1003</b></td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="14">Cuenta (PUC) :'.$cTitCue.'</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="14">Registros Analizados :'. number_format(count($mData)).'</td>';
							$data .= '</tr>';
								$data .= '<td width="100" align="center">Concepto</td>';
								$data .= '<td width="150" align="center">Tipo de documento</td>';
								$data .= '<td width="150" align="center">Numero identificacion del informado</td>';
								$data .= '<td width="050" align="center">DV</td>';
								$data .= '<td width="150" align="center">Primer apellido del informado</td>';
								$data .= '<td width="150" align="center">Segundo apellido del informado</td>';
								$data .= '<td width="150" align="center">Primer nombre del informado</td>';
								$data .= '<td width="150" align="center">Otros nombres del informado</td>';
								$data .= '<td width="300" align="center">Razon social informado</td>';
								$data .= '<td width="300" align="center">Direccion</td>';
								$data .= '<td width="150" align="center">Codigo dpto.</td>';
								$data .= '<td width="100" align="center">Codigo mcp</td>';
								$data .= '<td width="150" align="center">Valor acum. del pago o abono sujeto a retencion en la fuente</td>';
								$data .= '<td width="150" align="center">Retencion en la fuente que le practicaron</td>';
							$data .= '</tr>';
							foreach ($mData as $i => $cValue) {
								$data .= '<tr bgcolor = "white" height="30">';
									$data .= '<td align="center"></td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXX'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['teridxxx'].'</td>';
									$data .= '<td align="center">'.f_Digito_Verificacion($mData[$i]['teridxxx']).'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE1X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM1X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOMXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIDIRXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['DEPIDXXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CIUIDXXX'].'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['COMVRL01'],0,",",".")).'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['COMVLRXX'],0,",",".")).'</td>';
								$data .= '</tr>';
							} //Fin While que recorre el cursor de la matriz generada por la consulta.
						$data .= '</table>';
					break;
					case "1005":
						$data .= '<table cellspacing="0" cellpadding="0" border="1">';
						$data .= '<tr>';
						$data .= '<td align="left" colspan="10"><b>Informe de Medios Magneticos - Formato 1005</b></td>';
						$data .= '</tr>';
						$data .= '<tr>';
						$data .= '<td align="left" colspan="10">Cuenta (PUC) :'.$cTitCue.'</td>';
						$data .= '</tr>';
						$data .= '<tr>';
						$data .= '<td align="left" colspan="10">Registros Analizados :'. number_format(count($mData)).'</td>';
						$data .= '</tr>';
							$data .= '<td width="150" align="center">Tipo de Documento</td>';
							$data .= '<td width="150" align="center">Numero de identificacion del informado</td>';
							$data .= '<td width="050" align="center">DV</td>';
							$data .= '<td width="150" align="center">Primer apellido del informado</td>';
							$data .= '<td width="150" align="center">Segundo apellido del informado</td>';
							$data .= '<td width="150" align="center">Primer nombre del informado</td>';
							$data .= '<td width="150" align="center">Otros nombres del informado</td>';
							$data .= '<td width="300" align="center">Razon social informado</td>';
							$data .= '<td width="150" align="center">Impuesto descontable</td>';
							$data .= '<td width="150" align="center">IVA resultante por devoluciones en ventas anuladas, rescindidas o resueltas</td>';
						$data .= '</tr>';
						foreach ($mData as $i => $cValue) {
							$data .= '<tr bgcolor = "white" height="30">';
								$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXX'].'</td>';
								$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['teridxxx'].'</td>';
								$data .= '<td align="center">'.f_Digito_Verificacion($mData[$i]['teridxxx']).'</td>';
								$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE1X'].'</td>';
								$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE2X'].'</td>';
								$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM1X'].'</td>';
								$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM2X'].'</td>';
								$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOMXX'].'</td>';
								$data .= '<td align="right">'.(number_format($mData[$i]['COMVLRXX'],0,",",".")).'</td>';
								$data .= '<td align="right">'.(number_format(0,0,",",".")).'</td>';
							$data .= '</tr>';
						} //Fin While que recorre el cursor de la matriz generada por la consulta.
						$data .= '</table>';
					break;
					case "1006":
						$data .= '<table cellspacing="0" cellpadding="0" border="1">';
							$data .= '<tr>';
							$data .= '<td align="left" colspan="11"><b>Informe de Medios Magneticos - Formato 1006</b></td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="11">Cuenta (PUC) :'.$cTitCue.'</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="11">Registros Analizados :'. number_format(count($mData)).'</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td width="150" align="center">Tipo de Documento</td>';
								$data .= '<td width="150" align="center">Numero de identificacion del informado</td>';
								$data .= '<td width="050" align="center">DV</td>';
								$data .= '<td width="150" align="center">Primer apellido del informado</td>';
								$data .= '<td width="150" align="center">Segundo apellido del informado</td>';
								$data .= '<td width="150" align="center">Primer nombre del informado</td>';
								$data .= '<td width="150" align="center">Otros nombres del informado</td>';
								$data .= '<td width="300" align="center">Razon social informado</td>';
								$data .= '<td width="150" align="center">Impuesto generado</td>';
								$data .= '<td width="150" align="center">IVA recuperado por operaciones en devoluciones en compras anuladas, rescindidas o resueltas</td>';
								$data .= '<td width="150" align="center">Impuesto al consumo</td>';
							$data .= '</tr>';
							for ($i=0;$i<count($mData);$i++) {
							$data .= '<tr>';
								$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXX'].'</td>';
								$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['teridxxx'].'</td>';
								$data .= '<td align="center">'.f_Digito_Verificacion($mData[$i]['teridxxx']).'</td>';
								$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE1X'].'</td>';
								$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE2X'].'</td>';
								$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM1X'].'</td>';
								$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM2X'].'</td>';
								$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOMXX'].'</td>';
								$data .= '<td align="right">'.number_format($mData[$i]['comvlrxx'],0,",","").'</td>';
								$data .= '<td align="right">'.number_format(0,0,",","").'</td>';
								$data .= '<td align="right">'.number_format(0,0,",","").'</td>';
							$data .= '</tr>';
							} //Fin While que recorre el cursor de la matriz generada por la consulta.
						$data .= '</table>';
					break;
					case "1007":
						$data .= '<table cellspacing="0" cellpadding="0" border="1">';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="17"><b>Informe de Medios Magneticos - Formato 1007</b></td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="17">Cuenta (PUC) :'.$cTitCue.'</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="17">Registros Analizados :'. number_format(count($mData)).'</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td width="100" align="center">Concepto</td>';
								$data .= '<td width="150" align="center">Tipo de documento</td>';
								$data .= '<td width="150" align="center">Numero identificacion del informado</td>';
								$data .= '<td width="050" align="center">DV</td>';
								$data .= '<td width="150" align="center">Primer apellido del informado</td>';
								$data .= '<td width="150" align="center">Segundo apellido del informado</td>';
								$data .= '<td width="150" align="center">Primer nombre del informado</td>';
								$data .= '<td width="150" align="center">Otros nombres del informado</td>';
								$data .= '<td width="300" align="center">Razon social informado</td>';
								$data .= '<td width="150" align="center">Pais de residencia o domicilio</td>';
								$data .= '<td width="150" align="center">Ingresos brutos recibidos por operaciones propias</td>';
								$data .= '<td width="150" align="center">Ingresos a traves de consorcios o uniones temporales</td>';
								$data .= '<td width="150" align="center">Ingresos a traves de contratos de mandato o administracion delegada</td>';
								$data .= '<td width="150" align="center">Ingresos a traves de exploracion y explotacion de minerales</td>';
								$data .= '<td width="150" align="center">Ingresos a traves de fiducias</td>';
								$data .= '<td width="150" align="center">Ingresos recibidos a traves de terceros</td>';
								$data .= '<td width="150" align="center">Devoluciones, rebajas y descuentos</td>';
							$data .= '</tr>';
							foreach ($mData as $i => $cValue) {
								$data .= '<tr>';
									$data .= '<td align="center"></td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXX'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['teridxxx'].'</td>';
									$data .= '<td align="center">'.f_Digito_Verificacion($mData[$i]['teridxxx']).'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE1X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM1X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOMXX'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['PAIIDXXX'].'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['IPROXXXX'],0,",",".")).'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['ICONXXXX'],0,",",".")).'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['IMANXXXX'],0,",",".")).'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['IEXPXXXX'],0,",",".")).'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['IFIDXXXX'],0,",",".")).'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['ITERXXXX'],0,",",".")).'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['DEVXXXXX'],0,",",".")).'</td>';
								$data .= '</tr>';
							} //Fin While que recorre el cursor de la matriz generada por la consulta.
						$data .= '</table>';
					break;
					case "1008":
						$data .= '<table cellspacing="0" cellpadding="0" border="1">';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="14"><b>Informe de Medios Magneticos - Formato 1008</b></td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="14">Cuenta (PUC) :'.$cTitCue.'</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="14">Registros Analizados :'. number_format(count($mData)).'</td>';
							$data .= '</tr>';
								$data .= '<td width="100" align="center">Concepto</td>';
								$data .= '<td width="150" align="center">Tipo de documento</td>';
								$data .= '<td width="150" align="center">Numero identificacion deudor</td>';
								$data .= '<td width="050" align="center">DV</td>';
								$data .= '<td width="150" align="center">Primer apellido deudor</td>';
								$data .= '<td width="150" align="center">Segundo apellido deudor</td>';
								$data .= '<td width="150" align="center">Primer nombre deudor</td>';
								$data .= '<td width="150" align="center">Otros nombres deudor</td>';
								$data .= '<td width="300" align="center">Razon social deudor</td>';
								$data .= '<td width="300" align="center">Direccion</td>';
								$data .= '<td width="150" align="center">Codigo dpto.</td>';
								$data .= '<td width="100" align="center">Codigo mcp</td>';
								$data .= '<td width="150" align="center">Pais de residencia o domicilio</td>';
								$data .= '<td width="150" align="center">Saldo cuentas por cobrar al 31-12</td>';
							$data .= '</tr>';
							foreach ($mData as $i => $cValue) {
								$data .= '<tr bgcolor = "white" height="30">';
									$data .= '<td align="center"></td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXX'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['teridxxx'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.f_Digito_Verificacion($mData[$i]['teridxxx']).'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE1X'] .'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM1X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOMXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIDIRXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['DEPIDXXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CIUIDXXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['PAIIDXXX'].'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['COMVLRXX'],0,",",".")).'</td>';
								$data .= '</tr>';
							} //Fin While que recorre el cursor de la matriz generada por la consulta.
						$data .= '</table>';
					break;
					case "1009":
						$data .= '<table cellspacing="0" cellpadding="0" border="1">';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="14"><b>Informe de Medios Magneticos - Formato 1009</b></td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="14">Cuenta (PUC) :'.$cTitCue.'</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="14">Registros Analizados :'. number_format(count($mData)).'</td>';
							$data .= '</tr>';
								$data .= '<td width="100" align="center">Concepto</td>';
								$data .= '<td width="150" align="center">Tipo de documento</td>';
								$data .= '<td width="150" align="center">Numero identificacion acreedor</td>';
								$data .= '<td width="050" align="center">DV</td>';
								$data .= '<td width="150" align="center">Primer apellido acreedor</td>';
								$data .= '<td width="150" align="center">Segundo apellido acreedor</td>';
								$data .= '<td width="150" align="center">Primer nombre acreedor</td>';
								$data .= '<td width="150" align="center">Otros nombres acreedor</td>';
								$data .= '<td width="300" align="center">Razon social acreedor</td>';
								$data .= '<td width="300" align="center">Direccion</td>';
								$data .= '<td width="150" align="center">Codigo dpto.</td>';
								$data .= '<td width="100" align="center">Codigo mcp</td>';
								$data .= '<td width="150" align="center">Pais de residencia o domicilio</td>';
								$data .= '<td width="150" align="center">Saldo cuentas por pagar al 31-12</td>';
							$data .= '</tr>';
							foreach ($mData as $i => $cValue) {
								$data .= '<tr bgcolor = "white" height="30">';
									$data .= '<td align="center"></td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXX'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['teridxxx'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.f_Digito_Verificacion($mData[$i]['teridxxx']).'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE1X'] .'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM1X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOMXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIDIRXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['DEPIDXXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CIUIDXXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['PAIIDXXX'].'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['COMVLRXX'],0,",",".")).'</td>';
								$data .= '</tr>';
							} //Fin While que recorre el cursor de la matriz generada por la consulta.
						$data .= '</table>';
					break;
					case "1012":
						$data .= '<table cellspacing="0" cellpadding="0" border="1">';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="11"><b>Informe de Medios Magneticos - Formato 1012</b></td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="11">Cuenta (PUC) :'.$cTitCue.'</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="11">Registros Analizados :'. number_format(count($mData)).'</td>';
							$data .= '</tr>';
								$data .= '<td width="100" align="center">Concepto</td>';
								$data .= '<td width="150" align="center">Tipo de documento</td>';
								$data .= '<td width="150" align="center">NIT informado</td>';
								$data .= '<td width="050" align="center">DV</td>';
								$data .= '<td width="150" align="center">Primer apellido del informado</td>';
								$data .= '<td width="150" align="center">Segundo apellido del informado</td>';
								$data .= '<td width="150" align="center">Primer nombre del informado</td>';
								$data .= '<td width="150" align="center">Otros nombres del informado</td>';
								$data .= '<td width="300" align="center">Razon social informado</td>';
								$data .= '<td width="150" align="center">Pais de residencia o domicilio</td>';
								$data .= '<td width="150" align="center">Valor al 31-12</td>';
							$data .= '</tr>';
							foreach ($mData as $i => $cValue) {
								$data .= '<tr bgcolor = "white" height="30">';
									$data .= '<td align="center"></td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXX'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['teridxxx'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.f_Digito_Verificacion($mData[$i]['teridxxx']).'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE1X'] .'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM1X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOMXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['PAIIDXXX'].'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['COMVLRXX'],0,",",".")).'</td>';
								$data .= '</tr>';
							} //Fin While que recorre el cursor de la matriz generada por la consulta.
						$data .= '</table>';
					break;
					case "1016":
						$data .= '<table border="1" cellspacing="0" cellpadding="0" align=center style="margin:5px" width="4650">';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="27">Informe de Medios Magneticos - Formato 1016</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="27">Cuenta (PUC):'.$cTitCue.'</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="27">Registros Analizados : '.count($mData).'</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td width="100" align="center">Concepto</td>';
								$data .= '<td width="050" align="center">Tipo Doc.</td>';
								$data .= '<td width="150" align="center">Nit</td>';
								$data .= '<td width="050" align="center">DV</td>';
								$data .= '<td width="150" align="center">Primer Apellido a quien se le hizo el pago</td>';
								$data .= '<td width="150" align="center">Segundo Apellido a quien se le hizo el pago</td>';
								$data .= '<td width="150" align="center">Primer Nombre a quien se le hizo el pago</td>';
								$data .= '<td width="150" align="center">Segundo Nombre a quien se le hizo el pago</td>';
								$data .= '<td width="300" align="center">Razon Social a quien se le hizo el pago</td>';
								$data .= '<td width="300" align="center">Direccion</td>';
								$data .= '<td width="150" align="center">Departamento</td>';
								$data .= '<td width="150" align="center">Municipo</td>';
								$data .= '<td width="150" align="center">Pais</td>';
								$data .= '<td width="150" align="center">Pago o abono en cta</td>';
								$data .= '<td width="150" align="center">IVA mayor valor del costo o gasto</td>';
								$data .= '<td width="150" align="center">Retencion en la fuente practicada renta</td>';
								$data .= '<td width="150" align="center">Retencion en la fuente asumida renta</td>';
								$data .= '<td width="150" align="center">Retencion en la fuente practicada iva regimen comun</td>';
								$data .= '<td width="150" align="center">Retencion en la fuente asumida iva regimen simp.</td>';
								$data .= '<td width="150" align="center">Retencion en la fuente practicada iva no domiciliados</td>';
								$data .= '<td class="name" width="150" align="center">Retencion en la fuente practicada CREE</td>';
								$data .= '<td class="name" width="150" align="center">Retencion en la fuente asumida CREE</td>';
								$data .= '<td width="100" align="center">Tipo Doc. del mandante o contratante</td>';
								$data .= '<td width="150" align="center">Nit del mandante o contratante</td>';
								$data .= '<td width="050" align="center">DV mandante o contratante</td>';
								$data .= '<td width="100" align="center">Primer Apellido mandante o contratante</td>';
								$data .= '<td width="100" align="center">Segundo Apellido mandante o contratante</td>';
								$data .= '<td width="100" align="center">Primer Nombre mandante o contratante</td>';
								$data .= '<td width="100" align="center">Segundo Nombre mandante o contratante</td>';
								$data .= '<td width="300" align="center">Razon Social mandante o contratante</td>';
							$data .= '</tr>';
							foreach ($mData as $i => $cValue) {
								##Arreglo para Aducarga y si es el concepto "1330950001" ##
								if(($cAlfa == "ADUACARX" || $cAlfa == "DEADUACARX" || $cAlfa == "TEADUACARX") && $mData[$i]['ctoidxxx'] == "1330950001"){
									$mData[$i]['terid2di'] = "800197268";
									$mData[$i]['CLINOMXX'] = $mData[$i]['CLINOMDI'];
									$mData[$i]['CLINOM1X'] = "";
									$mData[$i]['CLIAPE1X'] = "";
									$mData[$i]['CLIAPE2X'] = "";
									$mData[$i]['CLINOM2X'] = "";
								}
								##Fin Arreglo para Aducarga y si es el concepto "1330950001" ##

								if ($cAlfa == "COLMASXX" || $cAlfa == "TECOLMASXX" || $cAlfa == "DECOLMASXX") {
									//Para colmas cuando el valor de la base es negativo
									//es porque es un ajuste y el documento que se esta ajustando no esta en el periodo selecionado
									$mData[$i]['PAGOXXXX'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['PAGOXXXX'];
									$mData[$i]['IVAXXXXX'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['IVAXXXXX'];
									$mData[$i]['PRACXXXX'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['PRACXXXX'];
									$mData[$i]['ASUMXXXX'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['ASUMXXXX'];
									$mData[$i]['COMUNXXX'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['COMUNXXX'];
									$mData[$i]['SIMPLXXX'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['SIMPLXXX'];
									$mData[$i]['NDOMXXXX'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['NDOMXXXX'];
									$mData[$i]['PRACCREE'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['PRACCREE'];
									$mData[$i]['ASUMCREE'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['ASUMCREE'];
								}

								$nDV    = f_Digito_Verificacion($mData[$i]['terid2di']);
								$nPago  = number_format($mData[$i]['PAGOXXXX'],0,",","");
								$nIva   = number_format($mData[$i]['IVAXXXXX'],0,",","");
								$nPrac  = number_format($mData[$i]['PRACXXXX'],0,",","");
								$nAsum  = number_format($mData[$i]['ASUMXXXX'],0,",","");
								$nComun = number_format($mData[$i]['COMUNXXX'],0,",","");
								$nSimpl = number_format($mData[$i]['SIMPLXXX'],0,",","");
								$nNdom  = number_format($mData[$i]['NDOMXXXX'],0,",","");
								$nPracCree = number_format($mData[$i]['PRACCREE'],0,",","");
								$nAsumCree = number_format($mData[$i]['ASUMCREE'],0,",","");
								$nDVC   = f_Digito_Verificacion($mData[$i]['CLIIDXXC']);

								$data .= '<tr>';
									$data .= '<td align="center"></td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXX'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['terid2di'].'</td>';
									$data .= '<td align="center">'.$nDV.'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE1X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM1X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOMXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIDIRXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['DEPIDXXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CIUIDXXX'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['PAIIDXXX'].'</td>';
									$data .= '<td align="right">'.$nPago.'</td>';
									$data .= '<td align="right">'.$nIva.'</td>';
									$data .= '<td align="right">'.$nPrac.'</td>';
									$data .= '<td align="right">'.$nAsum.'</td>';
									$data .= '<td align="right">'.$nComun.'</td>';
									$data .= '<td align="right">'.$nSimpl.'</td>';
									$data .= '<td align="right">'.$nNdom.'</td>';
									$data .= '<td align="right">'.$nPracCree.'</td>';
									$data .= '<td align="right">'.$nAsumCree.'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXC'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIIDXXC'].'</td>';
									$data .= '<td align="center">'.$nDVC.'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE1C'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE2C'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM1C'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM2C'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOMXC'].'</td>';
								$data .= '</tr>';
							} //Fin While que recorre el cursor de la matriz generada por la consulta.
						$data .= '</table>';
					break;
					case "1018":
						$data .= '<table cellspacing="0" cellpadding="0" border="1">';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="22"><b>Informe de Medios Magneticos - Formato 1018</b></td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="22">Cuenta (PUC) :'.$cTitCue.'</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="22">Registros Analizados :'. number_format(count($mData)).'</td>';
							$data .= '</tr>';
								$data .= '<td width="100" align="center">Concepto</td>';
								$data .= '<td width="150" align="center">Tipo de documento</td>';
								$data .= '<td width="150" align="center">Numero identificacion deudor</td>';
								$data .= '<td width="050" align="center">DV</td>';
								$data .= '<td width="150" align="center">Primer apellido deudor</td>';
								$data .= '<td width="150" align="center">Segundo apellido deudor</td>';
								$data .= '<td width="150" align="center">Primer nombre deudor</td>';
								$data .= '<td width="150" align="center">Otros nombres deudor</td>';
								$data .= '<td width="300" align="center">Razon social deudor</td>';
								$data .= '<td width="300" align="center">Direccion</td>';
								$data .= '<td width="150" align="center">Codigo dpto.</td>';
								$data .= '<td width="100" align="center">Codigo mcp</td>';
								$data .= '<td width="150" align="center">Pais de residencia o domicilio</td>';
								$data .= '<td width="150" align="center">Saldo cuentas por cobrar al 31-12</td>';
								//Nuevos campos para el reporte 1018
								$data .= '<td width="150" align="center">Tipo de Documento Mandante o contratante</td>';
								$data .= '<td width="150" align="center">Identificacion del mandante (Contratos de mandato)</td>';
								$data .= '<td width="050" align="center">DV</td>';
								$data .= '<td width="150" align="center">Primer apellido de mandante o contratante</td>';
								$data .= '<td width="150" align="center">Segundo apellido de mandante o contratante</td>';
								$data .= '<td width="150" align="center">Primer nombre del mandante o contratante</td>';
								$data .= '<td width="150" align="center">Segundo nombre del mandante o contratante</td>';
								$data .= '<td width="300" align="center">Razon social del mandante o contratante</td>';
							$data .= '</tr>';
							foreach ($mData as $i => $cValue) {
								$data .= '<tr bgcolor = "white" height="30">';
									$data .= '<td align="center"></td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXX'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['teridxxx'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.f_Digito_Verificacion($mData[$i]['teridxxx']).'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE1X'] .'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM1X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOMXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIDIRXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['DEPIDXXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CIUIDXXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['PAIIDXXX'].'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['COMVLRXX'],0,",",".")).'</td>';
									//Nuevos campos para el reporte 1018
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXC'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['terid2xx'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.f_Digito_Verificacion($mData[$i]['terid2xx']).'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE1C'] .'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE2C'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM1C'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM2C'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOMXC'].'</td>';
									//
								$data .= '</tr>';
							} //Fin While que recorre el cursor de la matriz generada por la consulta.
						$data .= '</table>';
					break;
					case "1027":
						$data .= '<table cellspacing="0" cellpadding="0" border="1">';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="22"><b>Informe de Medios Magneticos - Formato 1027</b></td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="22">Cuenta (PUC) :'.$cTitCue.'</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="22">Registros Analizados :'. number_format(count($mData)).'</td>';
							$data .= '</tr>';
								$data .= '<td width="100" align="center">Concepto</td>';
								$data .= '<td width="150" align="center">Tipo de documento</td>';
								$data .= '<td width="150" align="center">Numero identificacion deudor</td>';
								$data .= '<td width="050" align="center">DV</td>';
								$data .= '<td width="150" align="center">Primer apellido deudor</td>';
								$data .= '<td width="150" align="center">Segundo apellido deudor</td>';
								$data .= '<td width="150" align="center">Primer nombre deudor</td>';
								$data .= '<td width="150" align="center">Otros nombres deudor</td>';
								$data .= '<td width="300" align="center">Razon social deudor</td>';
								$data .= '<td width="300" align="center">Direccion</td>';
								$data .= '<td width="150" align="center">Codigo dpto.</td>';
								$data .= '<td width="100" align="center">Codigo mcp</td>';
								$data .= '<td width="150" align="center">Pais de residencia o domicilio</td>';
								$data .= '<td width="150" align="center">Saldo cuentas por cobrar al 31-12</td>';
								//Nuevos campos para el reporte 1027
								$data .= '<td width="150" align="center">Tipo de Documento Mandante o contratante</td>';
								$data .= '<td width="150" align="center">Identificacion del mandante (Contratos de mandato) y/o identificacion patrimonio autonomo</td>';
								$data .= '<td width="050" align="center">DV</td>';
								$data .= '<td width="150" align="center">Primer apellido de mandante o contratante</td>';
								$data .= '<td width="150" align="center">Segundo apellido de mandante o contratante</td>';
								$data .= '<td width="150" align="center">Primer nombre del mandante o contratante</td>';
								$data .= '<td width="150" align="center">Segundo nombre del mandante o contratante</td>';
								$data .= '<td width="300" align="center">Razon social del mandante o contratante</td>';
							$data .= '</tr>';
							foreach ($mData as $i => $cValue) {
								$data .= '<tr bgcolor = "white" height="30">';
									$data .= '<td align="center"></td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXX'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['teridxxx'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.f_Digito_Verificacion($mData[$i]['teridxxx']).'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE1X'] .'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM1X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOMXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIDIRXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['DEPIDXXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CIUIDXXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['PAIIDXXX'].'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['COMVLRXX'],0,",",".")).'</td>';
									//Nuevos campos para el reporte 1027
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXC'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['terid2xx'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.f_Digito_Verificacion($mData[$i]['terid2xx']).'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE1C'] .'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE2C'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM1C'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM2C'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOMXC'].'</td>';
									//
								$data .= '</tr>';
							} //Fin While que recorre el cursor de la matriz generada por la consulta.
						$data .= '</table>';
					break;
					case "1054":
						$data .= '<table border="1" cellspacing="0" cellpadding="0">';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="18"><font size="4"><b>Informe de Medios Magneticos - Formato 1054</font></b>';
								$data .= '</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="18"><font size="2">Cuenta (PUC): '.$cTitCue.'</font></td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="18"><font size="2">Registros Analizados : '.number_format(count($mData)).'</font></td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td width="050" align="center">Tipo Doc.</td>';
								$data .= '<td width="150" align="center">Nit del informado</td>';
								$data .= '<td width="050" align="center">DV del informado</td>';
								$data .= '<td width="150" align="center">Primer apellido del informado</td>';
								$data .= '<td width="150" align="center">Segundo apellido del informado</td>';
								$data .= '<td width="150" align="center">Primer nombre del informado</td>';
								$data .= '<td width="150" align="center">Otros nombres del informado</td>';
								$data .= '<td width="300" align="center">Razon social informado</td>';
								$data .= '<td width="150" align="center">Impuesto descontable</td>';
								$data .= '<td width="150" align="center">IVA resultante por devoluciones en ventas anuladas rescindidas o resueltas</td>';
								$data .= '<td width="100" align="center">Tipo Doc.</td>';
								$data .= '<td width="150" align="center">Nit del mandante o contratante</td>';
								$data .= '<td width="050" align="center">DV del mandante o contratante</td>';
								$data .= '<td width="100" align="center">Primer Apellido del mandante o contratante</td>';
								$data .= '<td width="100" align="center">Segundo Apellido del mandante o contratante</td>';
								$data .= '<td width="100" align="center">Primer Nombre del mandante o contratante</td>';
								$data .= '<td width="100" align="center">Segundo Nombre del mandante o contratante</td>';
								$data .= '<td width="300" align="center">Razon Social del mandante o contratante</td>';
							$data .= '</tr>';
							foreach ($mData as $i => $cValue) {
								##Arreglo para Aducarga y si es el concepto "1330950001" ##
								if(($cAlfa == "ADUACARX" || $cAlfa == "DEADUACARX" || $cAlfa == "TEADUACARX") && $mData[$i]['ctoidxxx'] == "1330950001"){
									$mData[$i]['terid2di'] = "800197268";
									$mData[$i]['CLINOMXX'] = $mData[$i]['CLINOMDI'];
									$mData[$i]['CLINOM1X'] = "";
									$mData[$i]['CLIAPE1X'] = "";
									$mData[$i]['CLIAPE2X'] = "";
									$mData[$i]['CLINOM2X'] = "";
								}
								##Fin Arreglo para Aducarga y si es el concepto "1330950001" ##

								$data .= '<tr>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXX'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['terid2di'].'</td>';
									$data .= '<td align="center">'.f_Digito_Verificacion($mData[$i]['terid2di']).'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE1X'] .'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM1X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOMXX'].'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['IMPDXXXX'],0,",",".")).'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['IVARXXXX'],0,",",".")).'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXC'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIIDXXC'].'</td>';
									$data .= '<td align="center">'.f_Digito_Verificacion($mData[$i]['CLIIDXXC']).'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE1C'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE2C'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM1C'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM2C'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOMXC'].'</td>';
								$data .= '</tr>';
							} //Fin While que recorre el cursor de la matriz generada por la consulta.
						$data .= '</table>';
					break;

					case "5247": ## Nuevo 5247 ~ Pagos o Retenciones
						$data .= '<table border="1" cellspacing="0" cellpadding="0" align=center style="margin:5px" width="3800">';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="22">Pagos o Retenciones - Formato 5247</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="22">Cuenta (PUC):'.$cTitCue.'</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="22">Registros Analizados : '.count($mData).'</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td width="100" align="center">Tipo contrato</td>';
								$data .= '<td width="100" align="center">Concepto</td>';
								$data .= '<td width="050" align="center">Tipo documento</td>';
								$data .= '<td width="150" align="center">Nit</td>';
								$data .= '<td width="150" align="center">Primer apellido del informado</td>';
								$data .= '<td width="150" align="center">Segundo apellido del informado</td>';
								$data .= '<td width="150" align="center">Primer nombre del informado</td>';
								$data .= '<td width="150" align="center">Otros nombres del informado</td>';
								$data .= '<td width="300" align="center">Razon social del informado</td>';
								$data .= '<td width="300" align="center">Direccion</td>';
								$data .= '<td width="150" align="center">Codigo dpto.</td>';
								$data .= '<td width="150" align="center">Codigo mcp</td>';
								$data .= '<td width="150" align="center">Pais</td>';
								$data .= '<td width="150" align="center">Pago o abono en cta</td>';
								$data .= '<td width="150" align="center">IVA mayor valor del costo o gasto</td>';
								$data .= '<td width="150" align="center">Retencion en la fuente practicada renta</td>';
								$data .= '<td width="150" align="center">Retencion en la fuente asumida renta</td>';
								$data .= '<td width="150" align="center">Retencion en la fuente practicada iva regimen comun</td>';
								$data .= '<td width="150" align="center">Retencion en la fuente practicada iva no domiciliados</td>';
								$data .= '<td width="080" align="center">Nit del fideicomiso</td>';							
								$data .= '<td width="100" align="center">Tipo Doc. del mandante o contratante</td>';
								$data .= '<td width="150" align="center">Nit del mandante o contratante</td>';
							$data .= '</tr>';
							foreach ($mData as $i => $cValue) {
								##Arreglo para Aducarga y si es el concepto "1330950001" ##
								if(($cAlfa == "ADUACARX" || $cAlfa == "DEADUACARX" || $cAlfa == "TEADUACARX") && $mData[$i]['ctoidxxx'] == "1330950001"){
									$mData[$i]['terid2di'] = "800197268";
									$mData[$i]['CLINOMXX'] = $mData[$i]['CLINOMDI'];
									$mData[$i]['CLINOM1X'] = "";
									$mData[$i]['CLIAPE1X'] = "";
									$mData[$i]['CLIAPE2X'] = "";
									$mData[$i]['CLINOM2X'] = "";
								}
								##Fin Arreglo para Aducarga y si es el concepto "1330950001" ##

								if ($cAlfa == "COLMASXX" || $cAlfa == "TECOLMASXX" || $cAlfa == "DECOLMASXX") {
									//Para colmas cuando el valor de la base es negativo
									//es porque es un ajuste y el documento que se esta ajustando no esta en el periodo selecionado
									$mData[$i]['PAGOXXXX'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['PAGOXXXX'];
									$mData[$i]['IVAXXXXX'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['IVAXXXXX'];
									$mData[$i]['PRACXXXX'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['PRACXXXX'];
									$mData[$i]['ASUMXXXX'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['ASUMXXXX'];
									$mData[$i]['COMUNXXX'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['COMUNXXX'];
									$mData[$i]['NDOMXXXX'] = ($mData[$i]['PAGOXXXX'] <= 0) ? 0 : $mData[$i]['NDOMXXXX'];
								}

								$nPago  = number_format($mData[$i]['PAGOXXXX'],0,",","");
								$nIva   = number_format($mData[$i]['IVAXXXXX'],0,",","");
								$nPrac  = number_format($mData[$i]['PRACXXXX'],0,",","");
								$nAsum  = number_format($mData[$i]['ASUMXXXX'],0,",","");
								$nComun = number_format($mData[$i]['COMUNXXX'],0,",","");
								$nNdom  = number_format($mData[$i]['NDOMXXXX'],0,",","");

								$data .= '<tr>';
									$data .= '<td align="center"></td>';
									$data .= '<td align="center"></td>';								
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXX'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['terid2di'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE1X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM1X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOMXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIDIRXX'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['DEPIDXXX'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CIUIDXXX'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['PAIIDXXX'].'</td>';
									$data .= '<td align="right">'.$nPago.'</td>';
									$data .= '<td align="right">'.$nIva.'</td>';
									$data .= '<td align="right">'.$nPrac.'</td>';
									$data .= '<td align="right">'.$nAsum.'</td>';
									$data .= '<td align="right">'.$nComun.'</td>';
									$data .= '<td align="right">'.$nNdom.'</td>';
									$data .= '<td align="center">0</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXC'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIIDXXC'].'</td>';
								$data .= '</tr>';
							} //Fin While que recorre el cursor de la matriz generada por la consulta.
						$data .= '</table>';
					break;

					case "5248": ## Nuevo 5248 - Ingresos
						$data .= '<table cellspacing="0" cellpadding="0" border="1">';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="15"><b>Informe de Medios Magneticos - Formato 5248</b></td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="15">Cuenta (PUC) :'.$cTitCue.'</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="15">Registros Analizados :'. number_format(count($mData)).'</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td width="100" align="center">Tipo de contrato</td>';
								$data .= '<td width="100" align="center">Concepto</td>';
								$data .= '<td width="150" align="center">Tipo de documento</td>';
								$data .= '<td width="150" align="center">Numero identificacion del informado</td>';
								$data .= '<td width="150" align="center">Primer apellido </td>';
								$data .= '<td width="150" align="center">Segundo apellido</td>';
								$data .= '<td width="150" align="center">Primer nombre</td>';
								$data .= '<td width="150" align="center">Otros nombres</td>';
								$data .= '<td width="300" align="center">Razon social</td>';
								$data .= '<td width="150" align="center">Pais de residencia o domicilio</td>';
								$data .= '<td width="150" align="center">Ingresos brutos recibidos</td>';
								$data .= '<td width="150" align="center">Devoluciones, rebajas y descuentos</td>';
								$data .= '<td width="150" align="center">Identificacion del fideicomiso</td>';
								$data .= '<td width="150" align="center">Tipo documento participante en contrato</td>';
								$data .= '<td width="150" align="center">Identificacion del participante en contrato</td>';
							$data .= '</tr>';
							foreach ($mData as $i => $cValue) {
								$data .= '<tr>';
									$data .= '<td align="center"></td>';
									$data .= '<td align="center"></td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXX'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['terid2xx'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE1X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM1X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOMXX'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['PAIIDXXX'].'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['IPROXXXX'],0,",",".")).'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['DEVXXXXX'],0,",",".")).'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">0</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXC'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIIDXXC'].'</td>';
								$data .= '</tr>';
							} //Fin While que recorre el cursor de la matriz generada por la consulta.
						$data .= '</table>';
					break;
					
					case "5249": ## Nuevo 5249 ~ Iva Descontable
						$data .= '<table border="1" cellspacing="0" cellpadding="0">';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="12"><font size="4"><b>Informe de Medios Magneticos - Formato 5249</font></b>';
								$data .= '</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="12"><font size="2">Cuenta (PUC): '.$cTitCue.'</font></td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="12"><font size="2">Registros Analizados : '.number_format(count($mData)).'</font></td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td width="080" align="center">Tipo Contrato</td>';						
								$data .= '<td width="150" align="center">Tipo de documento</td>';
								$data .= '<td width="150" align="center">Numero de identificacion</td>';
								$data .= '<td width="150" align="center">Primer apellido del informado</td>';
								$data .= '<td width="150" align="center">Segundo apellido del informado</td>';
								$data .= '<td width="150" align="center">Primer nombre del informado</td>';
								$data .= '<td width="150" align="center">Otros nombres del informado</td>';
								$data .= '<td width="200" align="center">Razon social informado</td>';
								$data .= '<td width="150" align="center">Impuesto descontable</td>';
								$data .= '<td width="150" align="center">IVA descontable por devoluciones en ventas</td>';
								$data .= '<td width="100" align="center">Tipo Doc.</td>';
								$data .= '<td width="150" align="center">Nit del mandante o contratante</td>';
							$data .= '</tr>';
							foreach ($mData as $i => $cValue) {
								##Arreglo para Aducarga y si es el concepto "1330950001" ##
								if(($cAlfa == "ADUACARX" || $cAlfa == "DEADUACARX" || $cAlfa == "TEADUACARX") && $mData[$i]['ctoidxxx'] == "1330950001"){
									$mData[$i]['terid2di'] = "800197268";
									$mData[$i]['CLIAPE1X'] = "";
									$mData[$i]['CLIAPE2X'] = "";
									$mData[$i]['CLINOM1X'] = "";
									$mData[$i]['CLINOM2X'] = "";
									$mData[$i]['CLINOMXX'] = $mData[$i]['CLINOMDI'];
								}
								##Fin Arreglo para Aducarga y si es el concepto "1330950001" ##

								$data .= '<tr>';
									$data .= '<td align="center">&nbsp;</td>';							
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXX'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['terid2di'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE1X'] .'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM1X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOMXX'].'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['IMPDXXXX'],0,",",".")).'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['IVARXXXX'],0,",",".")).'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXC'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIIDXXC'].'</td>';
								$data .= '</tr>';
							} //Fin While que recorre el cursor de la matriz generada por la consulta.
						$data .= '</table>';
					break;

					case "5250": ## Nuevo 5250 ~ Iva Generado
						$data .= '<table border="1" cellspacing="0" cellpadding="0">';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="13"><font size="4"><b>Informe de Medios Magneticos - Formato 5250</font></b>';
								$data .= '</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="13"><font size="2">Cuenta (PUC): '.$cTitCue.'</font></td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="13"><font size="2">Registros Analizados : '.number_format(count($mData)).'</font></td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td width="100" align="center">Tipo Contrato</td>';						
								$data .= '<td width="150" align="center">Tipo de documento</td>';
								$data .= '<td width="150" align="center">Numero identificacion</td>';
								$data .= '<td width="150" align="center">Primer apellido del informado</td>';
								$data .= '<td width="150" align="center">Segundo apellido del informado</td>';
								$data .= '<td width="150" align="center">Primer nombre del informado</td>';
								$data .= '<td width="150" align="center">Otros nombres del informado</td>';
								$data .= '<td width="300" align="center">Razon social informado</td>';
								$data .= '<td width="150" align="center">Impuesto generado</td>';
								$data .= '<td width="150" align="center">IVA generado por devoluciones de compras</td>'; 
								$data .= '<td width="150" align="center">Impuesto al consumo</td>';
								$data .= '<td width="100" align="center">Tipo documento participante en contrato</td>';
								$data .= '<td width="150" align="center">Identificacion del participante en contrato</td>';
							$data .= '</tr>';
							foreach ($mData as $i => $cValue) {
								$data .= '<tr>';
									$data .= '<td align="center">&nbsp;</td>';							
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXX'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['terid2xx'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE1X'] .'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM1X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOMXX'].'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['IMPGENXX'],0,",",".")).'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['IVADEXXX'],0,",",".")).'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['IMPCONXX'],0,",",".")).'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXC'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIIDXXC'].'</td>';
								$data .= '</tr>';
							} //Fin While que recorre el cursor de la matriz generada por la consulta.
						$data .= '</table>';
					break;

					case "5251": ## Nuevo 5251 ~ CXC
						$data .= '<table cellspacing="0" cellpadding="0" border="1">';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="16"><b>Informe de Medios Magneticos - Formato 5251</b></td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="16">Cuenta (PUC) :'.$cTitCue.'</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="16">Registros Analizados :'. number_format(count($mData)).'</td>';
							$data .= '</tr>';
								$data .= '<td width="100" align="center">Tipo de contrato</td>';
								$data .= '<td width="100" align="center">Concepto</td>';
								$data .= '<td width="100" align="center">Tipo de documento</td>';
								$data .= '<td width="150" align="center">Numero identificacion del informado</td>';
								$data .= '<td width="150" align="center">Primer apellido</td>';
								$data .= '<td width="150" align="center">Segundo apellido</td>';
								$data .= '<td width="150" align="center">Primer nombre</td>';
								$data .= '<td width="150" align="center">Otros nombres</td>';
								$data .= '<td width="300" align="center">Razon social</td>';
								$data .= '<td width="300" align="center">Direccion</td>';
								$data .= '<td width="150" align="center">Codigo dpto.</td>';
								$data .= '<td width="100" align="center">Codigo mcp.</td>';
								$data .= '<td width="150" align="center">Pais de residencia o domicilio</td>';
								$data .= '<td width="150" align="center">Saldo de la CXC a diciembre 31</td>';
								$data .= '<td width="150" align="center">Tipo de Documento del participante en contrato</td>';
								$data .= '<td width="150" align="center">Identificacion del participante en contrato</td>';
							$data .= '</tr>';
							foreach ($mData as $i => $cValue) {
								$data .= '<tr bgcolor = "white" height="30">';
									$data .= '<td align="center"></td>';
									$data .= '<td align="center"></td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXX'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['teridxxx'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE1X'] .'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM1X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOMXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIDIRXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['DEPIDXXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CIUIDXXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['PAIIDXXX'].'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['COMVLRXX'],0,",",".")).'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXC'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['terid2xx'].'</td>';
								$data .= '</tr>';
							} //Fin While que recorre el cursor de la matriz generada por la consulta.
						$data .= '</table>';
					break;

					case "5252": ## Nuevo 5252 ~ CXP
						$data .= '<table cellspacing="0" cellpadding="0" border="1">';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="16"><b>Informe de Medios Magneticos - Formato 5252</b></td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="16">Cuenta (PUC) :'.$cTitCue.'</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td align="left" colspan="16">Registros Analizados :'. number_format(count($mData)).'</td>';
							$data .= '</tr>';
								$data .= '<td width="100" align="center">Tipo de contrato</td>';
								$data .= '<td width="100" align="center">Concepto</td>';
								$data .= '<td width="150" align="center">Tipo de documento</td>';
								$data .= '<td width="150" align="center">Numero identificacion del informado</td>';
								$data .= '<td width="150" align="center">Primer apellido</td>';
								$data .= '<td width="150" align="center">Segundo apellido</td>';
								$data .= '<td width="150" align="center">Primer nombre</td>';
								$data .= '<td width="150" align="center">Otros nombres</td>';
								$data .= '<td width="300" align="center">Razon social</td>';
								$data .= '<td width="300" align="center">Direccion</td>';
								$data .= '<td width="150" align="center">Codigo dpto.</td>';
								$data .= '<td width="100" align="center">Codigo mcp</td>';
								$data .= '<td width="150" align="center">Pais de residencia o domicilio</td>';
								$data .= '<td width="150" align="center">Saldo de la 	CXP a diciembre 31</td>';
								$data .= '<td width="150" align="center">Tipo de Documento del participante en contrato</td>';
								$data .= '<td width="150" align="center">Identificacion del participante en contrato</td>';
							$data .= '</tr>';
							foreach ($mData as $i => $cValue) {
								$data .= '<tr bgcolor = "white" height="30">';
									$data .= '<td align="center"></td>';
									$data .= '<td align="center"></td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXX'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['teridxxx'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE1X'] .'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIAPE2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM1X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOM2X'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLINOMXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CLIDIRXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['DEPIDXXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['CIUIDXXX'].'</td>';
									$data .= '<td align="left" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['PAIIDXXX'].'</td>';
									$data .= '<td align="right">'.(number_format($mData[$i]['COMVLRXX'],0,",",".")).'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['TDIIDXXC'].'</td>';
									$data .= '<td align="center" style="width:150px;mso-number-format:\'\@\'">'.$mData[$i]['terid2xx'].'</td>';
								$data .= '</tr>';
							} //Fin While que recorre el cursor de la matriz generada por la consulta.
						$data .= '</table>';
					break;
				}

				fwrite($fOp, $data);
				fclose($fOp);
				
				if (file_exists($cFile)) {	
					if ($_SERVER["SERVER_PORT"] != "") {
						// Obtener la ruta absoluta del archivo
						$cAbsolutePath = realpath($cFile);
						$cAbsolutePath = substr($cAbsolutePath,0,strrpos($cAbsolutePath, '/'));

						if (in_array(realpath($cAbsolutePath), $vSystem_Path_Authorized)) {
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
					} else{
						$cNomArc = $cNomFile;
					}
				} else {
					$nSwitch = 1;
					if ($_SERVER["SERVER_PORT"] != "") {
						f_Mensaje(__FILE__, __LINE__, "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
					} else {
						$cMsj .= "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.";
					}
				}
			break;
		}//Fin Switch
	}## if ($cEjePro == 0) {

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
?>

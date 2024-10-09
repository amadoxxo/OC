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
		$cEjProBg = ($cEjProBg != "SI") ? "NO" : $cEjProBg;
	}

	if ($_SERVER["SERVER_PORT"] == "") {
		$gAnoIni = $_POST['gAnoIni'];
		$gMesIni = $_POST['gMesIni'];
		$gAnoFin = $_POST['gAnoFin'];
		$gMesFin = $_POST['gMesFin'];
		$gDirId  = $_POST['gDirId'];
		$gTerId  = $_POST['gTerId'];
		$cTipo   = $_POST['cTipo'];	
	}  // fin del if ($_SERVER["SERVER_PORT"] == "")
	
	if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
    $cEjePro  = 1;
    $nRegistros = 0;

		$strPost  = "gAnoIni~".$gAnoIni."|";
		$strPost .= "gMesIni~".$gMesIni."|";
		$strPost .= "gAnoFin~".$gAnoFin."|";
		$strPost .= "gMesFin~".$gMesFin."|";
		$strPost .= "gDirId~".$gDirId."|";
		$strPost .= "gDirNom~".$gDirNom."|";
		$strPost .= "gTerId~".$gTerId."|";
		$strPost .= "gTerNom~".$gTerNom."|";
		$strPost .= "cTipo~".$cTipo;
  
    $vParBg['pbadbxxx'] = $cAlfa;                                         	//Base de Datos
    $vParBg['pbamodxx'] = "FACTURACION";                                  	//Modulo
    $vParBg['pbatinxx'] = "PRODUCTIVIDADPORCLIENTE";                   	    //Tipo Interface
    $vParBg['pbatinde'] = "PRODUCTIVIDAD POR CLIENTE";                      //Descripcion Tipo de Interfaz
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
    //Inicializando datos
    $mASuc = array(); //ajustes por sucursal
    $mACli = array();//Ajustes por cliente
    $mACliSuc = array();//ajustes por cliente y sucursal
    $mTotMov = array(); //valores por sucursal
    $mTot = array(); //totales valor y ajustes
    $mNumDos = array();
    $mClientes = array();
    $nTotalDos = 0;
    $nTotqalPor= 0;
    $mComId = array(); //comprobantes a los que ya se le contadoron los numeros de do para las facturas
    $mComOt = array(); //comprobantes a los que ya se le contadoron los demas comprobantes
    
    //Hago el Select para pintar por Pantalla o en Excel
    $cPerAno = $gAnoIni;

    //Fecha Inicial
    $dFecIni = $gAnoIni."-".$gMesIni."-01";
    $dFecFin = $gAnoFin."-".$gMesFin."-".date ('d', mktime (0, 0, 0, $gMesFin + 1, 0, $cPerAno));
    
    #Creando Tabla temporal para la cabecera
    $cFcoc = "fcoc".$cPerAno;
    $cTabFac = fnCadenaAleatoria();
    $qNewTab  = "CREATE TEMPORARY TABLE IF NOT EXISTS $cAlfa.$cTabFac LIKE $cAlfa.$cFcoc";   
    $xNewTab = mysql_query($qNewTab,$xConexion01); 
    
    $qDatMov  = "SELECT * ";
    $qDatMov .= "FROM $cAlfa.fcoc$cPerAno ";
    $qDatMov .= "WHERE ";
    $qDatMov .= "$cAlfa.fcoc$cPerAno.comfecxx BETWEEN \"$dFecIni\" AND \"$dFecFin\" ";
    if($gTerId<>""){
      $qDatMov .= "AND $cAlfa.fcoc$cPerAno.teridxxx = \"{$gTerId}\" ";
    }
    if($gDirId<>""){
      $qDatMov .= "AND $cAlfa.fcoc$cPerAno.diridxxx = \"{$gDirId}\" ";
    }
    $qDatMov .= "AND $cAlfa.fcoc$cPerAno.regestxx = \"ACTIVO\"  ";

    $qInsert = "INSERT INTO $cAlfa.$cTabFac $qDatMov";
    $xInsert = mysql_query($qInsert,$xConexion01);
    #Fin Creando Tabla temporal para la cabecera
    
    $qDatSuc  = "SELECT $cAlfa.fpar0008.sucidxxx,$cAlfa.fpar0008.ccoidxxx,$cAlfa.fpar0008.sucdesxx ";
    $qDatSuc .= "FROM $cAlfa.fpar0008 ";
    $qDatSuc .= "WHERE ";
    $qDatSuc .= "$cAlfa.fpar0008.regestxx = \"ACTIVO\" ";
    $qDatSuc .= "ORDER BY $cAlfa.fpar0008.sucidxxx ";
    $xDatSuc  = f_MySql("SELECT","",$qDatSuc,$xConexion01,"");
    
    $mDatSuc = array(); $mDatCoi = array(); 
    while ($xRDS = mysql_fetch_array($xDatSuc)){
      $mDatSuc[$xRDS['sucidxxx']] = $xRDS['sucdesxx'];
      $mDatCoi[$xRDS['ccoidxxx']]['contador']++;
      $mDatCoi[$xRDS['ccoidxxx']]['sucidxxx'] = $xRDS['sucidxxx'];
    }
    //$mDatSuc[''] = "SIN SUCURSAL";

    /**
     * SElect para traer comvlrxx por sucursal y cliente, centro de costo y sucursal por cliente
     */
    $qDatMov  = "SELECT ";
    $qDatMov .= "$cAlfa.fcod$cPerAno.pucidxxx,";
    $qDatMov .= "CONCAT($cAlfa.fcod$cPerAno.comidxxx,\"-\",$cAlfa.fcod$cPerAno.comcodxx,\"-\",$cAlfa.fcod$cPerAno.comcscxx) AS comprobat,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comidxxx,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comcodxx,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comcscxx,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comcsc2x,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.sccidxxx,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.teridxxx,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.terid2xx,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.ccoidxxx,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comcsccx,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comseqcx,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comtraxx,";
    $qDatMov .= "IF($cAlfa.fcod$cPerAno.commovxx = \"D\",($cAlfa.fcod$cPerAno.comvlrxx * -1),$cAlfa.fcod$cPerAno.comvlrxx) AS comvlrxx ";
    $qDatMov .= "FROM $cAlfa.fcod$cPerAno ";
    $qDatMov .= "WHERE ";
    $qDatMov .= "$cAlfa.fcod$cPerAno.pucidxxx LIKE \"4%\" AND ";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comfecxx BETWEEN \"$dFecIni\" AND \"$dFecFin\" ";
    if($gTerId<>""){
      $qDatMov .= "AND $cAlfa.fcod$cPerAno.teridxxx = \"{$gTerId}\" ";
    }
    if($gDirId<>""){
      $qDatMov .= "AND $cAlfa.fcoc$cPerAno.diridxxx = \"{$gDirId}\" ";
    }
    $qDatMov .= "AND $cAlfa.fcod$cPerAno.regestxx = \"ACTIVO\"  ";
    $qDatMov .= "ORDER BY $cAlfa.fcod$cPerAno.teridxxx,$cAlfa.fcod$cPerAno.terid2xx";
    $xDatMov  = f_MySql("SELECT","",$qDatMov,$xConexion01,"");
    
    $mDatMov = array();
    $mCliId = array(); $mNomCli = array();
    
    while ($xRDM = mysql_fetch_array($xDatMov)){
      if (in_array($xRDM['teridxxx'],$mCliId) == false) {
        #Buscando nombre del cliente    
        $qCliNom  = "SELECT ";
        $qCliNom .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS clinomxx,";
        $qCliNom .= "CLIVENXX AS clivenxx ";
        $qCliNom .= "FROM $cAlfa.SIAI0150 ";
        $qCliNom .= "WHERE ";
        $qCliNom .= "CLIIDXXX = \"{$xRDM['teridxxx']}\" LIMIT 0,1";
        $xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
        
        if (mysql_num_rows($xCliNom) > 0) {
        $xRCN = mysql_fetch_array($xCliNom);
        $xRDM['clinomxx'] = $xRCN['clinomxx'];
        $xRDM['clivenxx'] = $xRCN['clivenxx'];
        } else {
          $xRDM['clinomxx'] = "CLIENTE SIN NOMBRE";
          $xRDM['clivenxx'] = "";
        }
        
        $mCliId[] = $xRDM['teridxxx'];
        $mNomCli[$xRDM['teridxxx']]['clinomxx'] = $xRDM['clinomxx'];
        $mNomCli[$xRDM['teridxxx']]['clivenxx'] = $xRDM['clivenxx'];
      } else {
        $xRDM['clinomxx'] = $mNomCli[$xRDM['teridxxx']]['clinomxx'];
        $xRDM['clivenxx'] = $mNomCli[$xRDM['teridxxx']]['clivenxx'];
      }
      
    #Buscando nombre de facturar a    
      $qCliNom  = "SELECT ";
      $qCliNom .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS clinomxx ";
      $qCliNom .= "FROM $cAlfa.SIAI0150 ";
      $qCliNom .= "WHERE ";
      $qCliNom .= "CLIIDXXX = \"{$xRDM['terid2xx']}\" LIMIT 0,1";
      $xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
      if (mysql_num_rows($xCliNom) > 0) {
        $xRCN = mysql_fetch_array($xCliNom);
        $xRDM['clinomfa'] = $xRCN['clinomxx'];
      } else {
        $xRDM['clinomfa'] = "CLIENTE SIN NOMBRE";
      }
      
      #Busco registro en cabecera
      if ($xRDM['comidxxx'] == "F") {
        $qMovCab  = "SELECT ";
        $qMovCab .= "comfpxxx,";
        $qMovCab .= "diridxxx ";
        $qMovCab .= "FROM $cAlfa.$cTabFac ";
        $qMovCab .= "WHERE ";
        $qMovCab .= "comidxxx = \"{$xRDM['comidxxx']}\" AND "; 
        $qMovCab .= "comcodxx = \"{$xRDM['comcodxx']}\" AND "; 
        $qMovCab .= "comcscxx = \"{$xRDM['comcscxx']}\" AND "; 
        $qMovCab .= "comcsc2x = \"{$xRDM['comcsc2x']}\" ";
        $xMovCab = mysql_query($qMovCab,$xConexion01);
        if(mysql_num_rows($xMovCab) > 0) {
          $xRMC = mysql_fetch_array($xMovCab);
          $xRDM['comfpxxx'] = $xRMC['comfpxxx'];
          $xRDM['diridxxx'] = $xRMC['diridxxx'];
        }
      }
      
      #Busco sucursal
      if($mDatCoi[$xRDM['ccoidxxx']]['contador'] == 1){
        $xRDM['sucidxxx'] = $mDatCoi[$xRDM['ccoidxxx']]['sucidxxx'];
      } else {
        //busco sucursal en el comfpxxx
        $mAux = explode("|",$xRDM['comfpxxx']);
        for($y=0;$y<count($mAux);$y++){
          if($mAux[$y] <> ""){
            $mAuxDo = explode("~",$mAux[$y]);
            $xRDM['sucidxxx'] = ($mAuxDo[15] <> "")?$mAuxDo[15]:"";
            $y = count($mAux);
          }
        }      
      }
      
      $nCon = 0;//contador de numero de do por comprobante
      //si es un ajuste busco el do en la sys00121 para buscar el nombre del vendedor y la sucursal si no se encontro
      if ($xRDM['comidxxx'] == "F") {
        if(in_array("{$xRDM['comidxxx']}-{$xRDM['comcodxx']}-{$xRDM['comcscxx']}",$mComId) == false){
          $mComId[] = "{$xRDM['comidxxx']}-{$xRDM['comcodxx']}-{$xRDM['comcscxx']}";
            //Calculo No. de Dos
          $mDos = explode("|",$xRDM['comfpxxx']);
          for($i=0;$i<count($mDos);$i++){
            if($mDos[$i]<>""){
              $nCon++;
            }
          }
        }
      } else {
        #Busco si es un do para sumar al contador
        $qDo  = "SELECT ";
        $qDo .= "$cAlfa.sys00121.docidxxx, ";
        $qDo .= "$cAlfa.sys00121.docvenxx, ";
        $qDo .= "$cAlfa.sys00121.sucidxxx ";
        $qDo .= "FROM $cAlfa.sys00121 ";
        $qDo .= "WHERE ";
        $qDo .= "$cAlfa.sys00121.docidxxx = \"{$xRDM['sccidxxx']}\" LIMIT 0,1";
        $xDo  = f_MySql("SELECT","",$qDo,$xConexion01,"");
        if (mysql_num_rows($xDo) == 0) {
          if($xRDM['comtraxx'] <> "") {
            $mAuxCsc = explode("-",$xRDM['comtraxx']);
            
            $qDo  = "SELECT ";
            $qDo .= "$cAlfa.sys00121.docidxxx, ";
            $qDo .= "$cAlfa.sys00121.docvenxx, ";
            $qDo .= "$cAlfa.sys00121.sucidxxx ";
            $qDo .= "FROM $cAlfa.sys00121 ";
            $qDo .= "WHERE ";
            if ($vSysStr['financiero_asignar_centro_de_costo_de_sucursal_comercial_a_do'] == 'SI') {
            $qDo .= "$cAlfa.sys00121.docidxxx = \"{$mAuxCsc[1]}\" AND ";
            $qDo .= "$cAlfa.sys00121.docsufxx = \"{$mAuxCsc[2]}\" LIMIT 0,1";
            } else {
            $qDo .= "$cAlfa.sys00121.sucidxxx = \"{$mAuxCsc[0]}\" AND ";
            $qDo .= "$cAlfa.sys00121.docidxxx = \"{$mAuxCsc[1]}\" AND ";
            $qDo .= "$cAlfa.sys00121.docsufxx = \"{$mAuxCsc[2]}\" LIMIT 0,1";     
            }
            $xDo  = f_MySql("SELECT","",$qDo,$xConexion01,"");
          } else {
            $qDo  = "SELECT ";
            $qDo .= "$cAlfa.sys00121.docidxxx, ";
            $qDo .= "$cAlfa.sys00121.docvenxx, ";
            $qDo .= "$cAlfa.sys00121.sucidxxx ";
            $qDo .= "FROM $cAlfa.sys00121 ";
            $qDo .= "WHERE ";
            $qDo .= "$cAlfa.sys00121.docidxxx = \"{$xRDM['comcsccx']}\" AND ";
            $qDo .= "$cAlfa.sys00121.docsufxx = \"{$xRDM['comseqcx']}\" LIMIT 0,1";
            $xDo  = f_MySql("SELECT","",$qDo,$xConexion01,"");
          }
        }
        
        if (mysql_num_rows($xDo) > 0) {
          if(in_array("{$xRDM['comidxxx']}-{$xRDM['comcodxx']}-{$xRDM['comcscxx']}-{$xRDM['sccidxxx']}",$mComOt) == false){
            $mComOt[] = "{$xRDM['comidxxx']}-{$xRDM['comcodxx']}-{$xRDM['comcscxx']}-{$xRDM['sccidxxx']}";
            $nCon = 1;
          }
          $xRDO = mysql_fetch_array($xDo);
          $xRDM['clivenxx'] = $xRDO['docvenxx'];
          if($xRDM['sucidxxx'] == "") {
            $xRDM['sucidxxx'] = $xRDO['sucidxxx'];
          }
        }
      }
      
      if($xRDM['clivenxx'] <> ""){
        $mCliVen = explode("~",$xRDM['clivenxx']);
        for($i=0;$i<count($mCliVen);$i++){
          if($mCliVen[$i] <> ""){
            $qNomVen  = "SELECT ";
            $qNomVen .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS clivenxx ";
            $qNomVen .= "FROM $cAlfa.SIAI0150 ";
            $qNomVen .= "WHERE $cAlfa.SIAI0150.CLIVENCO = \"SI\" AND $cAlfa.SIAI0150.CLIIDXXX = \"{$mCliVen[$i]}\" LIMIT 0,1 ";
            $xNomVen  = f_MySql("SELECT","",$qNomVen,$xConexion01,"");
            if (mysql_num_rows($xNomVen) > 0) {
              $xRNV = mysql_fetch_array($xNomVen);
              $xRDM['clivenxx'] = $xRNV['clivenxx'];
            } else {
              $xRDM['clivenxx'] = "VENDEDOR SIN NOMBRE [{$mCliVen[$i]}]";
            }
            $i = count($mCliVen);
          }
        }
      }else{
        $xRDM['clivenxx'] = "SIN ASIGNAR";
      }    
      
      $mDatMov[$xRDM['teridxxx']."~".$xRDM['terid2xx']]['clivenxx'] = ($mDatMov[$xRDM['teridxxx']."~".$xRDM['terid2xx']]['clivenxx'] <> "")?$mDatMov[$xRDM['teridxxx']."~".$xRDM['terid2xx']]['clivenxx']:(($xRDM['clivenxx']<>"")?$xRDM['clivenxx']:"&nbsp;");
      $mDatMov[$xRDM['teridxxx']."~".$xRDM['terid2xx']]['clinomxx'] = ($xRDM['clinomxx']<>"")?$xRDM['clinomxx']:"&nbsp;";
      $mDatMov[$xRDM['teridxxx']."~".$xRDM['terid2xx']]['teridxxx'] = ($xRDM['teridxxx']<>"")?$xRDM['teridxxx']:"&nbsp;";
      $mDatMov[$xRDM['teridxxx']."~".$xRDM['terid2xx']]['clinomfa'] = ($xRDM['clinomfa']<>"")?$xRDM['clinomfa']:"&nbsp;";
      $mDatMov[$xRDM['teridxxx']."~".$xRDM['terid2xx']]['terid2xx'] = ($xRDM['terid2xx']<>"")?$xRDM['terid2xx']:"&nbsp;";    
      $mDatMov[$xRDM['teridxxx']."~".$xRDM['terid2xx']][$xRDM['sucidxxx']]['sucidxxx'] = $xRDM['sucidxxx'];
      $mDatMov[$xRDM['teridxxx']."~".$xRDM['terid2xx']][$xRDM['sucidxxx']]['numdosxx'] += $nCon;
      $mDatMov[$xRDM['teridxxx']."~".$xRDM['terid2xx']][$xRDM['sucidxxx']]['comvlrxx'] += ($xRDM['comvlrxx']<>"")?$xRDM['comvlrxx']:0;
      
      //sumando el comvlrxx del cliente
      $mDatMov[$xRDM['teridxxx']."~".$xRDM['terid2xx']]['numdosxx'] += $nCon;
      $mDatMov[$xRDM['teridxxx']."~".$xRDM['terid2xx']]['comvlrto'] += ($xRDM['comvlrxx']<>"")?$xRDM['comvlrxx']:0;
      if(in_array($xRDM['teridxxx'],$mClientes) == false) {
        $mClientes[] = $xRDM['teridxxx'];
      }
      
      $mTotMov[$xRDM['sucidxxx']]['comvlrxx'] += $xRDM['comvlrxx'];
      $mTotMov[$xRDM['sucidxxx']]['numdosxx']  = 0;
    
      $mTot['comvlrxx'] += $xRDM['comvlrxx'];
      
    }
    
    $nCol = (count($mDatSuc)*3)+8;
    $nCol01 = 5;
    if($gTerId<>""){
      $nCol -= 2;
      $nCol01 = 3;
      //$nCol01 = 1;
    }
      
    switch ($cTipo) {
      case 1:
        // PINTA POR PANTALLA//
        ?>
        <html>
          <head>
            <title>Reporte de Productividad por Cliente</title>
            <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
            <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
            <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
            <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
          </head>
          <body>
            <form name = 'frgrm' action='frinpgrf.php' method="POST">
              <center>
                <table border="1" cellspacing="0" cellpadding="0" width="5000" align=center style="margin:5px">
                  <tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">
                    <td class="name" colspan="<?php echo $nCol ?>" align="left">
                      <font size="3">
                        <b>REPORTE DE INFORME DE PRODUCTIVIDAD POR CLIENTE<br>
                        PERIODO: <?php echo "Desde  ".$dFecIni."  Hasta  ".$dFecFin?><br>
                        <?php if($gTerId<>""){
                          //Busco en la base de datos el nombre del cliente
                          $qDatExt  = "SELECT IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS clinomxx ";
                          $qDatExt .= "FROM $cAlfa.SIAI0150 ";
                          $qDatExt .= "WHERE ";
                          $qDatExt .= "CLIIDXXX = \"$gTerId\" AND ";
                          $qDatExt .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
                          $xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
                          if (mysql_num_rows($xDatExt) > 0) {
                            $xRDE = mysql_fetch_array($xDatExt);
                          } else {
                            $xRDE['clinomxx'] = "CLIENTE SIN NOMBRE";
                          }
                          //f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));
                        ?>
                        CLIENTE: <?php echo "[".$gTerId."] ".$xRDE['clinomxx'] ?><br>
                        <?php } ?>

                        <?php if($gDirId<>""){
                          //Busco en la base de datos el nombre del director
                          $qSqlUsr  = "SELECT USRNOMXX ";
                          $qSqlUsr .= "FROM $cAlfa.SIAI0003 ";
                          $qSqlUsr .= "WHERE ";
                          $qSqlUsr .= "USRIDXXX = \"$gDirId\" AND ";
                          $qSqlUsr .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
                          $xSqlUsr = f_MySql("SELECT","",$qSqlUsr,$xConexion01,"");
                          $xRU = mysql_fetch_array($xSqlUsr);
                        ?>
                        DIRECTOR: <?php echo "[".$gDirId."] ".$xRU['USRNOMXX'] ?><br>
                        <?php } ?>
                        </b>
                      </font>
                    </td>
                  </tr>
                  <tr height="20">
                    <td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>VENDEDOR</font></b></td>
                    <?php if($gTerId==""){ ?>
                      <td style="background-color:#0B610B" class="letra8" width="120px" align="center"><b><font color=white>NIT</font></b></td>
                      <td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>PRINCIPAL</font></b></td>
                    <?php } ?>
                    <td style="background-color:#0B610B" class="letra8" width="120px" align="center"><b><font color=white>NIT</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>SECUNDARIO</font></b></td>
                    <?php  foreach ($mDatSuc as $cKey => $cValue) { ?>
                      <td style="background-color:#0B610B" class="letra8" align="center" width="150px"><b><font color=white><?php echo $cValue ?></font></b></td>
                      <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>%</font></b></td>
                      <td style="background-color:#0B610B" class="letra8" align="center" width="80px"><b><font color=white>NO. DOs</font></b></td>
                    <?php } ?>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Total</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>%</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="80px"><b><font color=white>NO. DOs</font></b></td>
                  </tr>
                  <?php foreach ($mDatMov as $cKey => $cValue) {
                    $zColorPro = "#000000";
                    $cColor = "#FFFFFF";
                    ?>
                    <tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">
                      <td class="letra7" align="left" style = "color:<?php echo $zColorPro ?>"><?php echo $mDatMov[$cKey]['clivenxx'] ?></td>
                      <?php if($gTerId==""){ ?>
                        <td class="letra7" align="left" style = "color:<?php echo $zColorPro ?>"><?php echo $mDatMov[$cKey]['teridxxx'] ?></td>
                        <td class="letra7" align="left" style = "color:<?php echo $zColorPro ?>"><?php echo $mDatMov[$cKey]['clinomxx'] ?></td>
                      <?php } ?>
                      <td class="letra7" align="left" style = "color:<?php echo $zColorPro ?>"><?php echo $mDatMov[$cKey]['terid2xx'] ?></td>
                      <td class="letra7" align="left" style = "color:<?php echo $zColorPro ?>"><?php echo $mDatMov[$cKey]['clinomfa'] ?></td>
                      <?php  foreach ($mDatSuc as $cKeySuc => $cValueSuc) {
                        if($cColor == "#FFFFFF"){
                          $cColor = "#F2F2F2";
                        }else{
                          $cColor = "#FFFFFF";
                        }
                        $nCliSuc = $mDatMov[$cKey][$cKeySuc]['comvlrxx'];
                        
                        $nPorMov = ($nCliSuc*100)/($mTotMov[$cKeySuc]['comvlrxx']);
                        $nPorMov = round($nPorMov * 100) / 100; 
                      ?>
                        <td class="letra7" align="right" style = "background-color:<?php echo $cColor ?>;color:<?php echo $zColorPro ?>"><?php  echo (number_format($nCliSuc,0,',','.')<>"")?number_format($nCliSuc,0,',','.'):"&nbsp;" ?></td>
                        <td class="letra7" align="right" style = "background-color:<?php echo $cColor ?>;color:<?php echo $zColorPro ?>"><?php  echo (number_format($nPorMov,2,',','.')<>"")?number_format($nPorMov,2,',','.'):"&nbsp;" ?></td>
                        <td class="letra7" align="center" style = "background-color:<?php echo $cColor ?>;color:<?php echo $zColorPro ?>"><?php echo ($mDatMov[$cKey][$cKeySuc]['numdosxx']<>"")?$mDatMov[$cKey][$cKeySuc]['numdosxx']:"&nbsp;" ?></td>
                      <?php
                        $mTotMov[$cKeySuc]['numdosxx'] += $mDatMov[$cKey][$cKeySuc]['numdosxx'];
                      } 
                      
                      $nCli = $mDatMov[$cKey]['comvlrto'];
                      $nPorMov = ($nCli*100)/($mTot['comvlrxx']);
                      $nPorMov = round($nPorMov * 100) / 100;

                      ?>
                      <td class="letra7" align="right"  style = "background-color:#E3F6CE;color:<?php echo $zColorPro ?>"><?php echo (number_format($nCli,0,',','.')<>"")?number_format($nCli,0,',','.'):"&nbsp;" ?></td>
                      <td class="letra7" align="right"  style = "background-color:#E3F6CE;color:<?php echo $zColorPro ?>"><?php echo (number_format($nPorMov,2,',','.')<>"")?number_format($nPorMov,2,',','.'):"&nbsp;" ?></td>
                      <td class="letra7" align="center" style = "background-color:#E3F6CE;color:<?php echo $zColorPro ?>"><?php echo ($mDatMov[$cKey]['numdosxx']<>"")?$mDatMov[$cKey]['numdosxx']:"&nbsp;"  ?></td>
                    </tr>
                  <?php
                    $nTotalDos  += $mDatMov[$cKey]['numdosxx'];
                    $nTotqalPor += $nPorMov;
                  } ?>
                  <tr style="background-color:#0B610B" height="20" style="padding-left:4px;padding-right:4px">
                    <td class="letra8" align="right" colspan="<?php echo $nCol01 ?>"><b><font color=white>TOTALES</font></b></td>
                    <?php  foreach ($mDatSuc as $cKey => $cValue) { 
                      $nTotAjus = $mTotMov[$cKey]['comvlrxx'];
                      
                      $nPorMov = ($nTotAjus*100)/($mTot['comvlrxx']);
                      $nPorMov = round($nPorMov * 1000) / 1000;
                      ?>
                      <td class="letra8" align="right" width="150px"><b><font color=white><?php echo (number_format($nTotAjus,0,',','.')<>"")?number_format($nTotAjus,0,',','.'):0 ?></font></b></td>
                      <td class="letra8" align="right" width="100px"><b><font color=white><?php echo (number_format(round($nPorMov),3,',','.')<>"")?number_format(round($nPorMov),2,',','.'):0 ?></font></b></td>
                      <td class="letra8" align="center" width="80px"><b><font color=white><?php echo $mTotMov[$cKey]['numdosxx'] ?></font></b></td>
                    <?php
                  } 
                    $nTotal = $mTot['comvlrxx'];
                  ?>
                    <td class="letra8" align="right" width="100px"><b><font color=white><?php echo (number_format($nTotal,0,',','.')<>"")?number_format($nTotal,0,',','.'):0 ?></font></b></td>
                    <td class="letra8" align="right" width="100px"><b><font color=white><?php echo (number_format(round($nTotqalPor),2,',','.')<>"")?number_format(round($nTotqalPor),2,',','.'):0 ?></font></b></td>
                    <td class="letra8" align="center" width="80px"><b><font color=white><?php echo (number_format($nTotalDos,0,',','.')<>"")?number_format($nTotalDos,0,',','.'):0 ?></font></b></td>
                  </tr>
                </table>
              </center>
            </form>
          </body>
        </html>
      <?php
      break;
      case 2: 
        // PINTA POR EXCEL //
        $header .= 'REPORTE DE INFORME DE PRODUCTIVIDAD POR CLIENTE'."\n";
        $header .= "\n";
        $data    = '';
        $title   = "REPORTE_DE_INFORME_DE_PRODUCTIVIDAD_POR_CLIENTE_".$_COOKIE['kUsrId'].date("YmdHis").".xls";
        
        if ($_SERVER["SERVER_PORT"] != "") {
          $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/".$title;
        } else {
          $cFile = "{$OPENINIT['pathdr']}/opencomex/".$vSysStr['system_download_directory']."/".$title;
        }

        $fOp = fopen($cFile,'a');


        $data .= '<table border="1" cellspacing="0" cellpadding="0" width="4500px">';
          $data .= '<tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">';
            $data .= '<td class="name" colspan="'.$nCol.'" align="left">';
              $data .= '<font size="3">';
              $data .= '<b>REPORTE DE INFORME DE PRODUCTIVIDAD POR CLIENTE<br>';
              $data .= 'PERIODO: Desde  '.$dFecIni.'  Hasta  '.$dFecFin.'';
              if($gTerId<>""){

                //Busco en la base de datos el nombre del cliente
                $qDatExt  = "SELECT IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS clinomxx ";
                $qDatExt .= "FROM $cAlfa.SIAI0150 ";
                $qDatExt .= "WHERE ";
                $qDatExt .= "CLIIDXXX = \"$gTerId\" AND ";
                $qDatExt .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
                $xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
                //f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));
                if (mysql_num_rows($xDatExt) > 0) {
                  $xRDE = mysql_fetch_array($xDatExt);
                } else {
                  $xRDE['clinomxx'] = "CLIENTE SIN NOMBRE";
                }
                $data .= '<br>CLIENTE: ['.$gTerId.'] '.$xRDE['clinomxx'].'';
              }
              
              
              if($gDirId<>""){
                //Busco en la base de datos el nombre del director
                $qSqlUsr  = "SELECT USRNOMXX ";
                $qSqlUsr .= "FROM $cAlfa.SIAI0003 ";
                $qSqlUsr .= "WHERE ";
                $qSqlUsr .= "USRIDXXX = \"$gDirId\" AND ";
                $qSqlUsr .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
                $xSqlUsr = f_MySql("SELECT","",$qSqlUsr,$xConexion01,"");
                $xRU = mysql_fetch_array($xSqlUsr);
                $data .= '<br>DIRECTOR: ['.$gDirId.'] '.$xRU['USRNOMXX'].'';
              }
              $data .= '</b>';
              $data .= '</font>';
            $data .= '</td>';
          $data .= '</tr>   ';
          $data .= '<tr height="20">';
            $data .= '<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>VENDEDOR</font></b></td>';
            if($gTerId==""){
              $data .= '<td style="background-color:#0B610B;width:150px" class="letra8" align="center"><b><font color=white>NIT</font></b></td>';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>PRINCIPAL</font></b></td>';
            }
            $data .= '<td style="background-color:#0B610B;width:150px" class="letra8" align="center"><b><font color=white>NIT</font></b></td>';
            $data .= '<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>SECUNDARIO</font></b></td>';
            foreach ($mDatSuc as $cKey => $cValue) {
              $data .= '<td style="background-color:#0B610B;width:120px" class="letra8" align="center"><b><font color=white>'.$cValue.'</font></b></td>';
              $data .= '<td style="background-color:#0B610B;width:100px" class="letra8" align="center"><b><font color=white>%</font></b></td>';
              $data .= '<td style="background-color:#0B610B;width:80px" class="letra8" align="center"><b><font color=white>NO. DOs</font></b></td>';
            }
            $data .= '<td style="background-color:#0B610B;width:120px" class="letra8" align="center"><b><font color=white>TOTAL</font></b></td>';
            $data .= '<td style="background-color:#0B610B;width:100px" class="letra8" align="center"><b><font color=white>%</font></b></td>';
            $data .= '<td style="background-color:#0B610B;width:80px" class="letra8" align="center"><b><font color=white>NO. DOs</font></b></td>';
          $data .= '</tr>';
          foreach ($mDatMov as $cKey => $cValue) {
            $zColorPro = "#000000";
            $cColor = "#FFFFFF";
            $data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
              $data .= '<td class="letra7" align="left" style = "color:'.$zColorPro.'">'.$mDatMov[$cKey]['clivenxx'].'</td>';
              if($gTerId==""){
                $data .= '<td class="letra7" align="left" style = "color:'.$zColorPro.'">'.$mDatMov[$cKey]['teridxxx'].'</td>';
                $data .= '<td class="letra7" align="left" style = "color:'.$zColorPro.'">'.$mDatMov[$cKey]['clinomxx'].'</td>';
              }
              $data .= '<td class="letra7" align="left" style = "color:'.$zColorPro.'">'.$mDatMov[$cKey]['terid2xx'].'</td>';
              $data .= '<td class="letra7" align="left" style = "color:'.$zColorPro.'">'.$mDatMov[$cKey]['clinomfa'].'</td>';
              foreach ($mDatSuc as $cKeySuc => $cValueSuc) {
                if($cColor == "#FFFFFF"){
                  $cColor = "#F2F2F2";
                }else{
                  $cColor = "#FFFFFF";
                }
                
                $nCliSuc = $mDatMov[$cKey][$cKeySuc]['comvlrxx'];
                        
                $nPorMov = ($nCliSuc*100)/($mTotMov[$cKeySuc]['comvlrxx']);
                $nPorMov = round($nPorMov * 100) / 100;  
                
                $nValor01 = (number_format($nCliSuc,0,',','')<>"")?number_format($nCliSuc,0,',',''):"&nbsp;";
                $nValor02 = (number_format($nPorMov,2,',','')<>"")?number_format($nPorMov,2,',',''):"&nbsp;";
                $nValor03 = ($mDatMov[$cKey][$cKeySuc]['numdosxx']<>"")?$mDatMov[$cKey][$cKeySuc]['numdosxx']:"&nbsp;";
                $data .= '<td class="letra7" align="right" style = "background-color:'.$cColor.';color:'.$zColorPro.'">'.$nValor01.'</td>';
                $data .= '<td class="letra7" align="right" style = "background-color:'.$cColor.';color:'.$zColorPro.'">'.$nValor02.'</td>';
                $data .= '<td class="letra7" align="center" style = "background-color:'.$cColor.';color:'.$zColorPro.'">'.$nValor03.'</td>';
                $mTotMov[$cKeySuc]['numdosxx'] += $mDatMov[$cKey][$cKeySuc]['numdosxx'];
              }
              
              $nCli = $mDatMov[$cKey]['comvlrto'];
              $nPorMov = ($nCli*100)/($mTot['comvlrxx']);
              $nPorMov = round($nPorMov * 100) / 100;
              
              $nValor01 = (number_format($nCli,0,',','')<>"")?number_format($nCli,0,',',''):"&nbsp;";
              $nValor02 = (number_format($nPorMov,2,',','')<>"")?number_format($nPorMov,2,',',''):"&nbsp;";
              $nValor03 = ($mDatMov[$cKey]['numdosxx']<>"")?$mDatMov[$cKey]['numdosxx']:"&nbsp;";
              $data .= '<td class="letra7" align="right" width="100px" style = "background-color:#E3F6CE;color:'.$zColorPro.'">'.$nValor01.'</td>';
              $data .= '<td class="letra7" align="right" width="100px" style = "background-color:#E3F6CE;color:'.$zColorPro.'">'.$nValor02.'</td>';
              $data .= '<td class="letra7" align="center" width="80px" style = "background-color:#E3F6CE;color:'.$zColorPro.'">'.$nValor03.'</td>';
            $data .= '</tr>';
            $nTotalDos  += $mDatMov[$cKey]['numdosxx'];
            $nTotqalPor += $nPorMov;
          }
          $data .= '<tr height="20" style="padding-left:4px;padding-right:4px">';
          $data .= '<td style="background-color:#0B610B" class="letra8" align="right" colspan="'.$nCol01.'"><b><font color=white>TOTALES</font></b></td>';
          foreach ($mDatSuc as $cKey => $cValue) {
            $nTotAjus = $mTotMov[$cKey]['comvlrxx'];
            $nPorMov = ($nTotAjus*100)/($mTot['comvlrxx']);
            $nPorMov = round($nPorMov * 1000) / 1000; 
            
            $nValor01 = (number_format($nTotAjus,0,',','')<>"")?number_format($nTotAjus,0,',',''):'0';
            $nValor02 = (number_format(round($nPorMov),2,',','')<>"")?number_format(round($nPorMov),3,',',''):'0';
            $data .= '<td style="background-color:#0B610B" class="letra8" align="right" width="150px"><b><font color=white>'.$nValor01.'</font></b></td>';
            $data .= '<td style="background-color:#0B610B" class="letra8" align="right" width="100px"><b><font color=white>'.$nValor02.'</font></b></td>';
            $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="80px"><b><font color=white>'.$mTotMov[$cKey]['numdosxx'].'</font></b></td>';
          }
          $nTotal = $mTot['comvlrxx'];
          
          $nValor01 = (number_format($nTotal,0,',','')<>"")?number_format($nTotal,0,',',''):'0';
          $nValor02 = (number_format(round($nTotqalPor),2,',','')<>"")?number_format(round($nTotqalPor),2,',',''):'0';
          $nValor03 = (number_format($nTotalDos,0,',','')<>"")?number_format($nTotalDos,0,',',''):'0';
          $data .= '<td style="background-color:#0B610B" class="letra8" align="right" width="100px"><b><font color=white>'.$nValor01.'</font></b></td>';
          $data .= '<td style="background-color:#0B610B" class="letra8" align="right" width="100px"><b><font color=white>'.$nValor02.'</font></b></td>';
          $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="80px"><b><font color=white>'.$nValor03.'</font></b></td>';
          $data .= '</tr>';
        $data .= '</table>';

        fwrite($fOp,$data);
        fclose($fOp);

        if (file_exists($cFile)){
          if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "NO") {
            chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
            $cDownLoadFilename = $cDownLoadFilename !== null ? $cDownLoadFilename : basename($cFile);
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $cDownLoadFilename);
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header("Cache-Control: private",false); // required for certain browsers
            header('Pragma: public');
            
            print $data;					
            exit;
          } else {
            $cNomArc = $title;
          }
        } else {
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


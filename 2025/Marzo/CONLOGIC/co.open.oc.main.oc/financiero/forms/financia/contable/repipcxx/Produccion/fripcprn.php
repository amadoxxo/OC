<?php

  set_time_limit(0);
  ini_set("memory_limit", "512M");

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
    $gAnoIni     = $_POST['gAnoIni'];
    $gMesIni     = $_POST['gMesIni'];
    $gAnoFin     = $_POST['gAnoFin'];
    $gMesFin     = $_POST['gMesFin'];
    $gTerId      = $_POST['gTerId'];
    $gTerNom     = $_POST['gTerNom'];
    $gCcoId      = $_POST['gCcoId'];
    $gDirId      = $_POST['gDirId'];
    $gDirNom     = $_POST['gDirNom'];
    $cTipo       = $_POST['cTipo'];
  }  // fin del if ($_SERVER["SERVER_PORT"] == "")

  //Datos sucursal
  $vSucursal = explode("~",$gCcoId);

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
  $nPerAno = substr($gAnoIni,0,4);

  //Rango de Fechas
  $dFecIni = $gAnoIni."-".$gMesIni."-01";
  $dFecFin = $gAnoFin."-".$gMesFin."-".date ('d', mktime (0, 0, 0, $gMesFin + 1, 0, $gAnoFin));

  #Creando Tabla temporal para la cabecera
  $cFcoc = "fcoc".$nPerAno;
  $cTabFac = fnCadenaAleatoria();
  $qNewTab  = "CREATE TEMPORARY TABLE IF NOT EXISTS $cAlfa.$cTabFac LIKE $cAlfa.$cFcoc";
  $xNewTab = mysql_query($qNewTab,$xConexion01);

  $qDatMov  = "SELECT * ";
  $qDatMov .= "FROM $cAlfa.fcoc$nPerAno ";
  $qDatMov .= "WHERE ";
  $qDatMov .= "$cAlfa.fcoc$nPerAno.comfecxx BETWEEN \"$dFecIni\" AND \"$dFecFin\" ";
  if( $gTerId != "") {
    $qDatMov .= "AND $cAlfa.fcoc$nPerAno.teridxxx = \"{$gTerId}\" ";
  }
  if($vSucursal[0] != "") {
    $qDatMov .= "AND $cAlfa.fcoc$nPerAno.ccoidxxx = \"{$vSucursal[0]}\" ";
  }
  $qDatMov .= "AND $cAlfa.fcoc$nPerAno.regestxx = \"ACTIVO\"  ";

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

  if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
    $cEjePro = 1;
    $nRegistros = 0;
  
    $strPost  = "gAnoIni~".$gAnoIni."|";
    $strPost .= "gMesIni~".$gMesIni."|";
    $strPost .= "gAnoFin~".$gAnoFin."|";
    $strPost .= "gMesFin~".$gMesFin."|";
    $strPost .= "gTerId~".$gTerId."|";
    $strPost .= "gTerNom~".$gTerNom."|";
    $strPost .= "gCcoId~".$gCcoId."|";
    $strPost .= "gCcoTexto~".$vSucursal[2]."|";
    $strPost .= "gDirId~".$gDirId."|";
    $strPost .= "gDirNom~".$gDirNom."|";
    $strPost .= "cTipo~".$cTipo;
  
    $vParBg['pbadbxxx'] = $cAlfa;                                         	//Base de Datos
    $vParBg['pbamodxx'] = "FACTURACION";                                  	//Modulo
    $vParBg['pbatinxx'] = "IPPORCONCEPTOS";                            	    //Tipo Interface
    $vParBg['pbatinde'] = "INGRESOS PROPIOS POR CONCEPTO";                  //Descripcion Tipo de Interfaz
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
    if($nSwitch == 0){
      #Select para traer comvlrxx por sucursal y cliente, centro de costo y sucursal por cliente
      $qDatMov  = "SELECT ";
      $qDatMov .= "$cAlfa.fcod$nPerAno.pucidxxx,";
      $qDatMov .= "$cAlfa.fcod$nPerAno.comidxxx,";
      $qDatMov .= "$cAlfa.fcod$nPerAno.comcodxx,";
      $qDatMov .= "$cAlfa.fcod$nPerAno.comcscxx,";
      $qDatMov .= "$cAlfa.fcod$nPerAno.comcsc2x,";
      $qDatMov .= "$cAlfa.fcod$nPerAno.sccidxxx,";
      $qDatMov .= "$cAlfa.fcod$nPerAno.teridxxx,";
      $qDatMov .= "$cAlfa.fcod$nPerAno.terid2xx,";
      $qDatMov .= "$cAlfa.fcod$nPerAno.ccoidxxx,";
      $qDatMov .= "$cAlfa.fcod$nPerAno.comcsccx,";
      $qDatMov .= "$cAlfa.fcod$nPerAno.comseqcx,";
      $qDatMov .= "$cAlfa.fcod$nPerAno.comtraxx,";
      $qDatMov .= "$cAlfa.fcod$nPerAno.comfecxx,";
      $qDatMov .= "$cAlfa.fcod$nPerAno.comobsxx,";
      $qDatMov .= "$cAlfa.fcod$nPerAno.ctoidxxx,";
      $qDatMov .= "$cAlfa.fcod$nPerAno.commovxx,";
      $qDatMov .= "$cAlfa.fcod$nPerAno.comdocfa, ";
      $qDatMov .= "$cAlfa.fcod$nPerAno.sucidxxx, ";
      $qDatMov .= "$cAlfa.fcod$nPerAno.docidxxx, ";
      $qDatMov .= "$cAlfa.fcod$nPerAno.docsufxx, ";
      $qDatMov .= "IF($cAlfa.fcod$nPerAno.commovxx=\"D\",($cAlfa.fcod$nPerAno.comvlrxx * -1),$cAlfa.fcod$nPerAno.comvlrxx) as comvlrxx, ";
      $qDatMov .= "$cAlfa.fpar0117.comtipxx ";
      $qDatMov .= "FROM $cAlfa.fcod$nPerAno ";
      $qDatMov .= "LEFT JOIN $cAlfa.fpar0117 ON $cAlfa.fcod$nPerAno.comidxxx = $cAlfa.fpar0117.comidxxx AND $cAlfa.fcod$nPerAno.comcodxx = $cAlfa.fpar0117.comcodxx ";
      if($vSysStr['financiero_grupos_contables_reportes_ip'] != ""){
        $qDatMov .= "LEFT JOIN $cAlfa.fpar0115 ON $cAlfa.fcod$nPerAno.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) ";
      } 
      $qDatMov .= "WHERE ";
      $qDatMov .= "($cAlfa.fcod$nPerAno.comctocx = \"IP\" OR ";
      $qDatMov .= "$cAlfa.fpar0117.comtipxx = \"AJUSTES\" OR ";
      $qDatMov .= "(($cAlfa.fcod$nPerAno.comidxxx = \"C\" OR $cAlfa.fcod$nPerAno.comidxxx = \"D\") AND $cAlfa.fpar0117.comtipxx != \"AJUSTES\" AND $cAlfa.fcod$nPerAno.comctocx = \"IP\") ) ";
      $qDatMov .= "AND $cAlfa.fcod$nPerAno.comfecxx BETWEEN \"$dFecIni\" AND \"$dFecFin\" ";
      if ($gTerId != "") {
        $qDatMov .= "AND $cAlfa.fcod$nPerAno.teridxxx = \"{$gTerId}\" ";
      }
      if($vSucursal[0] != "") {
        $qDatMov .= "AND $cAlfa.fcod$nPerAno.ccoidxxx = \"{$vSucursal[0]}\" ";
      }
      if($vSysStr['financiero_grupos_contables_reportes_ip'] != ""){
        $qDatMov .= "AND $cAlfa.fpar0115.pucgruxx IN ({$vSysStr['financiero_grupos_contables_reportes_ip']}) ";
      }  
      $qDatMov .= "AND $cAlfa.fcod$nPerAno.regestxx = \"ACTIVO\"  ";
      $qDatMov .= "ORDER BY $cAlfa.fcod$nPerAno.teridxxx,$cAlfa.fcod$nPerAno.terid2xx ";
      $xDatMov  = f_MySql("SELECT","",$qDatMov,$xConexion01,"");
      //f_Mensaje(__FILE__,__LINE__,$qDatMov."~".mysql_num_rows($xDatMov));

      $mDatMov  = array();
      $mCliId   = array();
      $mNomCli  = array();
      $mTramites= array();

      while ($xRDM = mysql_fetch_array($xDatMov)){
        /**
         * Buscando datos de la fpar0129
        */
        $qfpar119  = "SELECT ";
        $qfpar119 .= "IF($cAlfa.fpar0129.seridxxx != \"\",$cAlfa.fpar0129.seridxxx,\"SINCONCEPTO\") AS seridxxx,";
        $qfpar119 .= "IF($cAlfa.fpar0129.seridxxx != \"\",IF($cAlfa.fpar0129.serdespx != \"\",$cAlfa.fpar0129.serdespx,$cAlfa.fpar0129.serdesxx),\"CONCEPTO SIN DESCRIPCION\") AS serdesxx ";
        $qfpar119 .= "FROM $cAlfa.fpar0129 ";
        $qfpar119 .= "WHERE ";
        $qfpar119 .= "$cAlfa.fpar0129.ctoidxxx = \"{$xRDM['ctoidxxx']}\" LIMIT 0,1";
        $xfpar119  = f_MySql("SELECT","",$qfpar119,$xConexion01,"");
        if (mysql_num_rows($xfpar119) >0) {
          $xRpar119 = mysql_fetch_array($xfpar119);
          $xRDM['seridxxx'] = $xRpar119['seridxxx'];
          $xRDM['serdesxx'] = $xRpar119['serdesxx'];
        } else {
          $xRDM['seridxxx'] = "SINCONCEPTO";
          $xRDM['serdesxx'] = "CONCEPTO SIN DESCRIPCION";
        }

        if (in_array($xRDM['teridxxx'],$mCliId) == false) {
          #Buscando nombre del cliente
          $qCliNom  = "SELECT ";
          $qCliNom .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx, ";
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

          #Busco sucursal
          if($mDatCoi[$xRDM['ccoidxxx']]['contador'] == 1){
            $xRDM['sucidxxx'] = $mDatCoi[$xRDM['ccoidxxx']]['sucidxxx'];
          } else {
            //busco sucursal en el comfpxxx
            $mAux = explode("|",$xRDM['comfpxxx']);
            for($y=0;$y<count($mAux);$y++){
              if($mAux[$y] != ""){
                $mAuxDo = explode("~",$mAux[$y]);
                $xRDM['sucidxxx'] = ($mAuxDo[15] != "") ? $mAuxDo[15] :"";
                $y = count($mAux);
              }
            }
          }
        }

       // Comprobantes tipo AJUSTE
        // Extrae Factura para buscar el director
        if($xRDM['comtipxx'] == "AJUSTES" && $xRDM['comdocfa'] != "" && $xRDM['diridxxx'] == ""){
          $vDocFa = explode('~',$xRDM['comdocfa']);
          $vCscFa = explode('-',$vDocFa[1]);

          if($vDocFa[0] != ""){
            $qCabMov  = "SELECT ";
            $qCabMov .= "diridxxx, ";
            $qCabMov .= "comfpxxx ";
            $qCabMov .= "FROM $cAlfa.fcoc$vDocFa[0] ";
            $qCabMov .= "WHERE ";
            $qCabMov .= "comidxxx = \"$vCscFa[0]\"  AND ";
            $qCabMov .= "comcodxx = \"$vCscFa[1]\" AND ";
            $qCabMov .= "comcscxx = \"$vCscFa[2]\" LIMIT 0,1";
            $xCabMov  = f_MySql("SELECT","",$qCabMov,$xConexion01,"");
            $vCabMov  = mysql_fetch_array($xCabMov);
            //f_Mensaje(__FILE__,__LINE__,$qCabMov." ~ ".mysql_num_rows($xCabMov));
            if (mysql_num_rows($xCabMov) == 1) {
              $xRDM['diridxxx'] = $vCabMov['diridxxx'];
              $xRDM['comfpxxx'] = $vCabMov['comfpxxx'];
            }   

             //Buscar DO asociado a la factura
            $mDoiId = explode("|",$xRDM['comfpxxx']);
            for ($i=0;$i<count($mDoiId);$i++) {
              if($mDoiId[$i] != ""){
                $vDoiId  = explode("~",$mDoiId[$i]);
                $xRDM['comtraxx'] = $vDoiId[15]."-".$vDoiId[2]."-".$vDoiId[3]; 
                $i = count($mDoiId);
              }
            }//if($mDoiId[$i] != ""){

            #Busco sucursal
            if($mDatCoi[$xRDM['ccoidxxx']]['contador'] == 1){
              $xRDM['sucidxxx'] = $mDatCoi[$xRDM['ccoidxxx']]['sucidxxx'];
            } else {
              //busco sucursal en el comfpxxx
              $mAux = explode("|",$xRDM['comfpxxx']);
              for($y=0;$y<count($mAux);$y++){
                if($mAux[$y] != ""){
                  $mAuxDo = explode("~",$mAux[$y]);
                  $xRDM['sucidxxx'] = ($mAuxDo[15] != "")?$mAuxDo[15]:"";
                  $y = count($mAux);
                }
              }            
            }
          }      
        }           

        if ($xRDM['comidxxx'] != "F") {
          $nContador = 0;
          if ($xRDM['sccidxxx'] != "") {
            #Busco si es un do para sumar al contador
            $qDo  = "SELECT ";
            $qDo .= "$cAlfa.sys00121.doctipxx, ";
            $qDo .= "$cAlfa.sys00121.docfobxx, ";
            $qDo .= "$cAlfa.sys00121.sucidxxx, ";
            $qDo .= "$cAlfa.sys00121.docidxxx, ";
            $qDo .= "$cAlfa.sys00121.docsufxx, ";
            $qDo .= "$cAlfa.sys00121.diridxxx, ";
            $qDo .= "$cAlfa.sys00121.docvenxx  ";
            $qDo .= "FROM $cAlfa.sys00121 ";
            $qDo .= "WHERE ";
            $qDo .= "$cAlfa.sys00121.docidxxx = \"{$xRDM['sccidxxx']}\" LIMIT 0,1";
            $xDo  = f_MySql("SELECT","",$qDo,$xConexion01,"");
            $nContador = mysql_num_rows($xDo);
          }

          if ($nContador == 0) {
            if($xRDM['comtraxx'] != "") {
              $mAuxCsc = explode("-",$xRDM['comtraxx']);

              $qDo  = "SELECT ";
              $qDo .= "$cAlfa.sys00121.doctipxx, ";
              $qDo .= "$cAlfa.sys00121.docfobxx, ";
              $qDo .= "$cAlfa.sys00121.sucidxxx, ";
              $qDo .= "$cAlfa.sys00121.docidxxx, ";
              $qDo .= "$cAlfa.sys00121.docsufxx, ";
              $qDo .= "$cAlfa.sys00121.diridxxx, ";
              $qDo .= "$cAlfa.sys00121.docvenxx  ";
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
              //f_Mensaje(__FILE__,__LINE__,$qDo."~".mysql_num_rows($xDo));
            } else {
              $qDo  = "SELECT ";
              $qDo .= "$cAlfa.sys00121.doctipxx, ";
              $qDo .= "$cAlfa.sys00121.docfobxx, ";
              $qDo .= "$cAlfa.sys00121.sucidxxx, ";
              $qDo .= "$cAlfa.sys00121.docidxxx, ";
              $qDo .= "$cAlfa.sys00121.docsufxx, ";
              $qDo .= "$cAlfa.sys00121.diridxxx, ";
              $qDo .= "$cAlfa.sys00121.docvenxx  ";
              $qDo .= "FROM $cAlfa.sys00121 ";
              $qDo .= "WHERE ";
              $qDo .= "$cAlfa.sys00121.docidxxx = \"{$xRDM['comcsccx']}\" AND ";
              $qDo .= "$cAlfa.sys00121.docsufxx = \"{$xRDM['comseqcx']}\" LIMIT 0,1";
              $xDo  = f_MySql("SELECT","",$qDo,$xConexion01,"");
              //f_Mensaje(__FILE__,__LINE__,$qDo."~".mysql_num_rows($xDo));
            }
          }

          $xRDM['doctipxx'] = "";
          $xRDM['docfobxx'] = "";

          if (mysql_num_rows($xDo) > 0) {
            $xRDO = mysql_fetch_array($xDo);

            $xRDM['doctipxx'] = $xRDO['doctipxx'];
            $xRDM['docfobxx'] = $xRDO['docfobxx'];

            $xRDM['clivenxx'] = ($xRDO['docvenxx'] != "") ? $xRDO['docvenxx'] : "";

            if($xRDM['comtraxx'] == "") {
              $xRDM['comtraxx'] = $xRDO['sucidxxx']."-".$xRDO['docidxxx']."-".$xRDO['docsufxx'];
            }

            if($xRDM['diridxxx'] == "") {
              $xRDM['diridxxx'] = $xRDO['diridxxx'];
            }
            if($xRDM['sucidxxx'] == "") {
              $xRDM['sucidxxx'] = $xRDO['sucidxxx'];
            }
          }
        }

        #Buscando Nombre vendedor
        if($xRDM['clivenxx'] != ""){
          $mCliVen = explode("~",$xRDM['clivenxx']);
          $cNomVen = "";
          for($i=0;$i<count($mCliVen);$i++){
            if($mCliVen[$i] != ""){
              $qNomVen  = "SELECT IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clivenxx ";
              $qNomVen .= "FROM $cAlfa.SIAI0150 ";
              $qNomVen .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$mCliVen[$i]}\" LIMIT 0,1 ";
              $xNomVen  = f_MySql("SELECT","",$qNomVen,$xConexion01,"");
              if (mysql_num_rows($xNomVen) > 0) {
                $xRNV = mysql_fetch_array($xNomVen);
                $cNomVen = $xRNV['clivenxx']." [{$mCliVen[$i]}]";
                $i = count($mCliVen);
              }
            }
          }
          if ($cNomVen != "") {
            $xRDM['clivenxx'] = $cNomVen;
          } else {
            $xRDM['clivenxx'] = "VENDEDOR SIN NOMBRE";
          }
        }else{
          $xRDM['clivenxx'] = "SIN ASIGNAR";
        }

        $nBan = 0;
        if($gDirId != ""){
        if ($xRDM['diridxxx'] != $gDirId){
          $nBan = 1;
        }
        }

        if($vSucursal[1] != ""){
        if ($xRDM['sucidxxx'] != $vSucursal[1]){
          $nBan = 1;
        }
        }

        if($nBan == 0) {


          //Agrupo por tercero, factura, sucursal, concepto
          $cFactura = $xRDM['comidxxx']."-".$xRDM['comcodxx']."-".$xRDM['comcscxx'];

          $mDatMov[$xRDM['teridxxx']][$cFactura][$xRDM['sucidxxx']]['clivenxx']  = ($xRDM['clivenxx'] != "") ? $xRDM['clivenxx'] : "&nbsp;";
          $mDatMov[$xRDM['teridxxx']][$cFactura][$xRDM['sucidxxx']]['clinomxx']  = ($xRDM['clinomxx'] != "") ? $xRDM['clinomxx'] : "&nbsp;";
          $mDatMov[$xRDM['teridxxx']][$cFactura][$xRDM['sucidxxx']]['teridxxx']  = ($xRDM['teridxxx'] != "") ? $xRDM['teridxxx'] : "&nbsp;";
          $mDatMov[$xRDM['teridxxx']][$cFactura][$xRDM['sucidxxx']]['sucidxxx']  = ($xRDM['sucidxxx'] != "") ? $xRDM['sucidxxx'] : "&nbsp;";
          $mDatMov[$xRDM['teridxxx']][$cFactura][$xRDM['sucidxxx']]['facturax']  = ($cFactura != "")         ? $cFactura         : "&nbsp;";
          $mDatMov[$xRDM['teridxxx']][$cFactura][$xRDM['sucidxxx']]['comfecxx']  = ($xRDM['comfecxx'] != "") ? $xRDM['comfecxx'] : "&nbsp;";
          $mComtra = explode("-",$xRDM['comtraxx']);
          $mDatMov[$xRDM['teridxxx']][$cFactura][$xRDM['sucidxxx']]['comtraxx']  = ($xRDM['comtraxx'] != "") ? $mComtra[1]."-".$mComtra[2] : "&nbsp;";

          /**
           * Buscando el valor cif
           */
          if (in_array($xRDM['comtraxx'], $mTramites) == false) {
            $mTramites[count($mTramites)] = $xRDM['comtraxx'];

            /**
             * si no se ha hecho busqueda a la sys00121 para traer el tipo de operacion
             * entonces se hace la consulta
             */
            if ($xRDM['doctipxx'] == "") {
              $qDo  = "SELECT ";
              $qDo .= "$cAlfa.sys00121.doctipxx, ";
              $qDo .= "$cAlfa.sys00121.docfobxx  ";
              $qDo .= "FROM $cAlfa.sys00121 ";
              $qDo .= "WHERE ";
              $qDo .= "$cAlfa.sys00121.sucidxxx = \"{$mComtra[0]}\" AND ";
              $qDo .= "$cAlfa.sys00121.docidxxx = \"{$mComtra[1]}\" AND ";
              $qDo .= "$cAlfa.sys00121.docsufxx = \"{$mComtra[2]}\" LIMIT 0,1";
              $xDo  = f_MySql("SELECT","",$qDo,$xConexion01,"");
              //f_Mensaje(__FILE__,__LINE__,$qDo."~".mysql_num_rows($xDo));
              if (mysql_num_rows($xDo) > 0) {
                $xRDO = mysql_fetch_array($xDo);

                $xRDM['doctipxx'] = $xRDO['doctipxx'];
                $xRDM['docfobxx'] = $xRDO['docfobxx'];
              }
            }

            $nCif = 0;
            switch($xRDM['doctipxx']){
              case "IMPORTACION":
                $qDecDat  = "SELECT ";
                $qDecDat .= "SUM($cAlfa.SIAI0206.LIMNETXX) AS LIMNETXX, ";
                $qDecDat .= "SUM($cAlfa.SIAI0206.LIMCIFXX) AS LIMCIFXX, ";
                $qDecDat .= "SUM($cAlfa.SIAI0206.LIMPBRXX) AS LIMPBRXX, ";
                $qDecDat .= "SUM($cAlfa.SIAI0206.LIMPNEXX) AS LIMPNEXX, ";
                $qDecDat .= "SUM($cAlfa.SIAI0206.LIMVLRXX) AS LIMVLRXX, ";//Fob
                $qDecDat .= "SUM($cAlfa.SIAI0206.LIMGRAXX) AS LIMGRA2X, ";
                $qDecDat .= "SUM($cAlfa.SIAI0206.LIMSUBT2) AS LIMSUBT2, ";
                $qDecDat .= "SUM($cAlfa.SIAI0206.LIMFLEXX) AS LIMFLEXX, ";
                $qDecDat .= "SUM($cAlfa.SIAI0206.LIMSEGXX) AS LIMSEGXX ";
                $qDecDat .= "FROM $cAlfa.SIAI0206 ";
                $qDecDat .= "WHERE ";
                $qDecDat .= "$cAlfa.SIAI0206.DOIIDXXX = \"{$mComtra[1]}\" AND ";
                $qDecDat .= "$cAlfa.SIAI0206.DOISFIDX = \"{$mComtra[2]}\" AND ";
                $qDecDat .= "$cAlfa.SIAI0206.ADMIDXXX = \"{$mComtra[0]}\" ";
                $qDecDat .= "GROUP BY $cAlfa.SIAI0206.DOIIDXXX,$cAlfa.SIAI0206.DOISFIDX,$cAlfa.SIAI0206.ADMIDXXX ";
                $xDecDat  = f_MySql("SELECT","",$qDecDat,$xConexion01,"");
                if (mysql_num_rows($xDecDat) > 0) {
                  $vDecDat  = mysql_fetch_array($xDecDat);
                  $nCif = $vDecDat['LIMCIFXX'];
                }
              break;
              case "EXPORTACION":
                $nCif = $xRDM['docfobxx'];
              break;
            }

            $mDatMov[$xRDM['teridxxx']][$cFactura][$xRDM['sucidxxx']]['vlrcifxx']  = $nCif;
          }

          /**
           * Buscando la descripcion del concepto cuando este no es factura ni es ajuste
           */
          if ($xRDM['comidxxx'] != "F" && $xRDM['comtipxx'] == "AJUSTES") {
            $xRDM['seridxxx'] = $xRDM['ctoidxxx'];
          }

          $mDatMov[$xRDM['teridxxx']][$cFactura][$xRDM['sucidxxx']][$xRDM['seridxxx']]['seridxxx']  = ($xRDM['seridxxx'] != "") ? $xRDM['seridxxx'] : "&nbsp;";
          $mDatMov[$xRDM['teridxxx']][$cFactura][$xRDM['sucidxxx']][$xRDM['seridxxx']]['comvlrxx'] += ($xRDM['comvlrxx'] != "") ? $xRDM['comvlrxx'] : "0";

          //sumando el comvlrxx del cliente, factura, sucursal
          $mTotCli[$xRDM['teridxxx']][$cFactura][$xRDM['sucidxxx']]['comvlrxx'] += ($xRDM['comvlrxx'] != "") ? $xRDM['comvlrxx'] : "0";

          //sumando el comvlrxx del concepto
          $mTotCto[$xRDM['seridxxx']]['comvlrxx'] += ($xRDM['comvlrxx'] != "") ? $xRDM['comvlrxx'] : "0";

          if (in_array($xRDM['seridxxx'],$mCtoDes) == false && $xRDM['seridxxx'] != "SINCONCEPTO") {
            $nInd_mCtoDes = count($mCtoDes);
            //if ($xRDM['comidxxx'] != "F") {
            if ($xRDM['comtipxx'] == "AJUSTES") {
            /**
              * Buscado descripcion segun tipo de comprobante
              */
              $cCtoDes = "";

              // Busco la descripcion del concepto
              $qCtoCon  = "SELECT $cAlfa.fpar0119.* ";
              $qCtoCon .= "FROM $cAlfa.fpar0119 ";
              $qCtoCon .= "WHERE ";
              $qCtoCon .= "$cAlfa.fpar0119.ctoidxxx = \"{$xRDM['ctoidxxx']}\" AND ";
              $qCtoCon .= "$cAlfa.fpar0119.pucidxxx = \"{$xRDM['pucidxxx']}\" LIMIT 0,1";
              $xCtoCon  = f_MySql("SELECT","",$qCtoCon,$xConexion01,"");
              //f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
              if (mysql_num_rows($xCtoCon) > 0) {
                $vCtoCon = mysql_fetch_array($xCtoCon);
                $cCtoDes = ($vCtoCon['ctodesx'.strtolower($xRDM['comidxxx'])] != "") ? $vCtoCon['ctodesx'.strtolower($xRDM['comidxxx'])] : $vCtoCon['ctodesxx'];
              } else {
                //Busco en la parametrica de Conceptos Contables Causaciones Automaticas
                $qCtoCon  = "SELECT $cAlfa.fpar0121.* ";
                $qCtoCon .= "FROM $cAlfa.fpar0121 ";
                $qCtoCon .= "WHERE ";
                $qCtoCon .= "$cAlfa.fpar0121.ctoidxxx = \"{$xRDM['ctoidxxx']}\" AND ";
                $qCtoCon .= "$cAlfa.fpar0121.pucidxxx = \"{$xRDM['pucidxxx']}\" LIMIT 0,1";
                $xCtoCon  = f_MySql("SELECT","",$qCtoCon,$xConexion01,"");
                //f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
                if (mysql_num_rows($xCtoCon) > 0) {
                $vCtoCon = mysql_fetch_array($xCtoCon);
                $cCtoDes = $vCtoCon['ctodesxx'];
                }
              }
              $mCtoDes[$xRDM['seridxxx']] = ($cCtoDes != "") ? $cCtoDes : "CONCEPTO SIN DESCRIPCION";
            } else {
              $mCtoDes[$xRDM['seridxxx']] = $xRDM['serdesxx'];
            }
          }
        }
      }

      asort($mCtoDes);
      $nInd_mCtoDes = count($mCtoDes);
      $mCtoDes["SINCONCEPTO"] = "CONCEPTO SIN DESCRIPCION";

      if (count($mDatMov) > 0) {
        if($gTerId==""){
          $nCol = 8;
        }else{
          $nCol = 6;
        }
        $nNumCol = count($mCtoDes)+$nCol+1;

        switch ($cTipo) {
          case 1:
            if ($_SERVER["SERVER_PORT"] != "") {
              // PINTA POR PANTALLA//
              ?>
              <html>
                <head>
                  <title>Reporte de Ingresos Propios por Concepto</title>
                  <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
                  <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
                  <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
                  <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
                </head>
                <body>
                  <form name = 'frgrm' action='frinpgrf.php' method="POST">
                    <center>
                      <table border="1" cellspacing="0" cellpadding="0" width="3200" align=center style="margin-top:5px; margin-left: 5px; margin-right: 5px">
                        <tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">
                          <?php
                          switch($cAlfa){
                            case "TEADIMPEXX":
                            case "DEADIMPEXX":
                            case "ADIMPEXX":
                            // case "DEGRUPOGLA":
                            // case "TEGRUPOGLA":
                              ?>
                              <td class = "name" style="width: 100px;">
                                <img width="100" height="90" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoAdimpex.jpg">
                              </td>
                              <?php
                              $nColRes = 1;
                            break;
                            case "ROLDANLO"://ROLDAN
                            case "TEROLDANLO"://ROLDAN
                            case "DEROLDANLO"://ROLDAN
                              ?>
                              <td class = "name" style="width: 150px;">
                                <img width="150" height="90" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoroldan.png">
                              </td>
                              <?php
                              $nColRes = 1;
                            break;
                            case "GRUMALCO"://GRUMALCO
                            case "TEGRUMALCO"://GRUMALCO
                            case "DEGRUMALCO"://GRUMALCO
                              ?>
                              <td class = "name" style="width: 150px;">
                                <img width="120" height="70" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logomalco.jpg">
                              </td>
                              <?php
                              $nColRes = 1;
                            break;
                            case "ALADUANA"://ALADUANA
                            case "TEALADUANA"://ALADUANA
                            case "DEALADUANA"://ALADUANA
                                ?>
                                <td class = "name" style="width: 150px;">
                                <img width="120" height="60" style="left: 55px;margin-top: 1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoaladuana.jpg">
                                </td>
                                <?php
                                $nColRes = 1;
                            break;
                            case "ANDINOSX"://ANDINOSX
                            case "TEANDINOSX"://ANDINOSX
                            case "DEANDINOSX"://ANDINOSX
                              ?>
                              <td class = "name" style="width: 150px;">
                              <img width="75" height="80" style="left: 30px;margin-top: 1px;position: relative;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoAndinos2.jpeg">
                              </td>
                              <?php
                              $nColRes = 1;
                            break;
                            case "GRUPOALC"://GRUPOALC
                            case "TEGRUPOALC"://GRUPOALC
                            case "DEGRUPOALC"://GRUPOALC
                              ?>
                              <td class = "name" style="width: 150px;">
                              <img width="120" height="60" style="left: 55px;margin-top: 1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoalc.jpg">
                              </td>
                              <?php
                              $nColRes = 1;
                            break;
                            case "AAINTERX"://AAINTERX
                            case "TEAAINTERX"://AAINTERX
                            case "DEAAINTERX"://AAINTERX
                              ?>
                              <td class = "name" style="width: 150px;">
                              <img width="120" height="60" style="left: 55px;margin-top: 1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logointernacional.jpg">
                              </td>
                              <?php
                              $nColRes = 1;
                            break;
                            case "AALOPEZX":
                            case "TEAALOPEZX":
                            case "DEAALOPEZX":
                              ?>
                              <td class = "name" style="width: 150px;">
                              <img width="120" style="left: 55px;margin-top: 1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoaalopez.png">
                              </td>
                              <?php
                              $nColRes = 1;
                            break;
                            case "ADUAMARX"://ADUAMARX
                            case "TEADUAMARX"://ADUAMARX
                            case "DEADUAMARX"://ADUAMARX
                              ?>
                              <td class = "name" style="width: 150px;">
                              <img width="70" height="70" style="left: 55px;margin-top: 1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoaduamar.jpg">
                              </td>
                              <?php
                              $nColRes = 1;
                            break;
                            case "SOLUCION"://SOLUCION
                            case "TESOLUCION"://SOLUCION
                            case "DESOLUCION"://SOLUCION
                              ?>
                              <td class = "name" style="width: 150px;">
                              <img width="150" style="left: 55px;margin-top: 1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logosoluciones.jpg">
                              </td>
                              <?php
                              $nColRes = 1;
                            break;
														case "FENIXSAS"://FENIXSAS
														case "TEFENIXSAS"://FENIXSAS
														case "DEFENIXSAS"://FENIXSAS
															?>
															<td class = "name" style="width: 150px;">
															<img width="150" style="left: 55px;margin-top: 1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logofenix.jpg">
															</td>
															<?php
															$nColRes = 1;
                            break;
                            case "COLVANXX"://COLVANXX
                            case "TECOLVANXX"://COLVANXX
                            case "DECOLVANXX"://COLVANXX
                              ?>
                              <td class = "name" style="width: 150px;">
                              <img width="150" style="left: 55px;margin-top: 1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logocolvan.jpg">
                              </td>
                              <?php
                              $nColRes = 1;
                            break;
                            case "INTERLAC"://INTERLAC
                            case "TEINTERLAC"://INTERLAC
                            case "DEINTERLAC"://INTERLAC
                              ?>
                              <td class = "name" style="width: 150px;">
                              <img width="150" style="left: 55px;margin-top: 1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logointerlace.jpg">
                              </td>
                              <?php
                              $nColRes = 1;
                            break;
														case "DHLEXPRE": //DHLEXPRE
														case "TEDHLEXPRE": //DHLEXPRE
														case "DEDHLEXPRE": //DHLEXPRE
															?>
															<td class = "name" style="width: 150px;">
																<img width="140" height="80" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logo_dhl_express.jpg">
															</td>
															<?php
															$nColRes = 1;
														break;
                            case "KARGORUX": //KARGORUX
                            case "TEKARGORUX": //KARGORUX
                            case "DEKARGORUX": //KARGORUX
                              ?>
                              <td class="name" style="width: 150px;">
                                <img width="120" height="60" style="left: 15px;margin-top:5px;margin-bottom:5px;margin-left:15px;" src="<?php echo $cPlesk_Skin_Directory ?>/logokargoru.jpg">
                              </td>
                              <?php
                              $nColRes = 1;
                            break;
                            case "ALOGISAS": //LOGISTICA
                            case "TEALOGISAS": //LOGISTICA
                            case "DEALOGISAS": //LOGISTICA
                              ?>
                              <td class="name" style="width: 150px;">
                                <img width="140" style="margin-top:5px;margin-bottom:5px;margin-left:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logologisticasas.jpg">
                              </td>
                              <?php
                              $nColRes = 1;
                            break;
                            case "PROSERCO": //PROSERCO
                            case "TEPROSERCO": //PROSERCO
                            case "DEPROSERCO": //PROSERCO
                              ?>
                              <td class="name" style="width: 150px;">
                                <img width="140" style="margin-top:5px;margin-bottom:5px;margin-left:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logoproserco.png">
                              </td>
                              <?php
                              $nColRes = 1;
                            break;
                            case "MANATIAL": //MANATIAL
                            case "TEMANATIAL": //MANATIAL
                            case "DEMANATIAL": //MANATIAL
                              ?>
                              <td class="name" style="width: 150px;">
                                <img width="140" style="margin-top:5px;margin-bottom:5px;margin-left:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logomanantial.jpg">
                              </td>
                              <?php
                              $nColRes = 1;
                            break;
                            case "DSVSASXX":
                            case "DEDSVSASXX":
                            case "TEDSVSASXX":
                              ?>
                              <td class="name" style="width: 150px;">
                                <img width="140" style="margin-top:5px;margin-bottom:5px;margin-left:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logodsv.jpg">
                              </td>
                              <?php
                              $nColRes = 1;
                            break;
                            case "MELYAKXX":    //MELYAK
                            case "DEMELYAKXX":  //MELYAK
                            case "TEMELYAKXX":  //MELYAK
                              ?>
                              <td class="name" style="width: 150px;">
                                <img width="140" style="margin-top:5px;margin-bottom:5px;margin-left:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logomelyak.jpg">
                              </td>
                              <?php
                              $nColRes = 1;
                            break;
                            case "FEDEXEXP":    //FEDEX
                            case "DEFEDEXEXP":  //FEDEX
                            case "TEFEDEXEXP":   //FEDEX
                              ?>
                              <td class="name" style="width: 150px;">
                                <img width="140" style="margin-top:5px;margin-bottom:5px;margin-left:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logofedexexp.jpg">
                              </td>
                              <?php
                              $nColRes = 1;
                            break;
                            case "EXPORCOM":    //EXPORCOMEX
                            case "DEEXPORCOM":  //EXPORCOMEX
                            case "TEEXPORCOM":   //EXPORCOMEX
                              ?>
                              <td class="name" style="width: 150px;">
                                <img width="140" style="margin-top:5px;margin-bottom:5px;margin-left:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logoexporcomex.jpg">
                              </td>
                              <?php
                              $nColRes = 1;
                            break;
                            case "HAYDEARX":    //HAYDEARX
                            case "DEHAYDEARX":  //HAYDEARX
                            case "TEHAYDEARX":  //HAYDEARX
                              ?>
                              <td class="name" style="width: 150px;">
                                <img width="180" style="margin-top:5px;margin-bottom:5px;margin-left:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logohaydear.jpeg">
                              </td>
                              <?php
                              $nColRes = 1;
                            break;
                            case "CONNECTA":   //CONNECTA
                            case "DECONNECTA": //CONNECTA
                            case "TECONNECTA": //CONNECTA
                              ?>
                              <td class="name" style="width: 80px;">
                                <img width="180" style="margin-top:5px;margin-bottom:5px;margin-left:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logoconnecta.jpg">
                              </td>
                              <?php
                              $nColRes = 1;
                            break;
                            case "CONLOGIC":   //CONLOGIC
                            case "DECONLOGIC": //CONLOGIC
                            case "TECONLOGIC": //CONLOGIC
                              ?>
                              <td class="name" style="width: 80px;">
                                <img width="180" style="margin-top:5px;margin-bottom:5px;margin-left:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logoconlogic.jpg">
                              </td>
                              <?php
                              $nColRes = 1;
                            break;
                            case "OPENEBCO":   //OPENEBCO
                            case "DEOPENEBCO": //OPENEBCO
                            case "TEOPENEBCO": //OPENEBCO
                              ?>
                              <td class="name" style="width: 80px;">
                                <img width="180" style="margin-top:5px;margin-bottom:5px;margin-left:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/opentecnologia.JPG">
                              </td>
                              <?php
                              $nColRes = 1;
                            break;
                            default:
                                $nColRes = 0;
                            break;
                          }
                          ?>
                          <td class="name" colspan="<?php echo $nNumCol -$nColRes ?>" align="left">
                            <font size="3">
                              <b>REPORTE DE INGRESOS PROPIOS POR CONCEPTO<br>
                              PERIODO: <?php echo " DE ".$dFecIni." A ".$dFecFin ?><br>
                              <?php if($gTerId != ""){
                                //Busco en la base de datos el nombre del cliente
                                $qDatExt  = "SELECT IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
                                $qDatExt .= "FROM $cAlfa.SIAI0150 ";
                                $qDatExt .= "WHERE ";
                                $qDatExt .= "CLIIDXXX = \"$gTerId\" AND ";
                                $qDatExt .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
                                $xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
                                $xRDE = mysql_fetch_array($xDatExt);
                                //f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));
                              ?>
                              CLIENTE: <?php echo "[".$gTerId."] ".$xRDE['clinomxx'] ?><br>
                              <?php } ?>
                              <?php if ($vSucursal[1] != "") { ?>
                              SUCURSAL: <?php echo "[".$vSucursal[1]."] ".$vSucursal[2] ?><br>
                              <?php } ?>
                              <?php if($gDirId != ""){
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
                      </table>
                      <table border="1" cellspacing="0" cellpadding="0" width="3200" align=center style="margin-left: 5px; margin-right: 5px">
                        <tr height="20">
                          <td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>VENDEDOR</font></b></td>
                          <?php if($gTerId==""){ ?>
                            <td style="background-color:#0B610B" class="letra8" width="100px" align="center"><b><font color=white>NIT</font></b></td>
                            <td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>CLIENTE</font></b></td>
                          <?php } ?>
                            <td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>FACTURA</font></b></td>
                            <td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>FECHA</font></b></td>
                            <td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>SUCURSAL</font></b></td>
                            <td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>DO</font></b></td>
                            <td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>VALOR CIF</font></b></td>
                          <?php  foreach ($mCtoDes as $cKey => $cValue) { ?>
                            <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white><?php echo $cValue ?></font></b></td>
                          <?php } ?>
                          <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>TOTAL</font></b></td>
                        </tr>
                        <?php
                        $nTotal = 0;
                        foreach ($mDatMov as $cKey => $cValue) { //Primer Nivel - Cliente
                          foreach ($cValue as $cKey2 => $cValue2) { //Segundo Nivel - Factua
                            foreach ($cValue2 as $cKey3 => $cValue3) { //Tercer Nivel - Sucursal
                              $zColorPro = "#000000";
                              $cColor = "#FFFFFF";
                              ?>
                              <tr bgcolor = "white" height="20">
                                <td class="letra7" align="left" style = "padding-left:4px;padding-right:4px;color:<?php echo $zColorPro ?>"><?php echo $mDatMov[$cKey][$cKey2][$cKey3]['clivenxx'] ?></td>
                                <?php if($gTerId==""){ ?>
                                  <td class="letra7" align="left" style = "padding-left:4px;padding-right:4px;color:<?php echo $zColorPro ?>"><?php echo $mDatMov[$cKey][$cKey2][$cKey3]['teridxxx'] ?></td>
                                  <td class="letra7" align="left" style = "padding-left:4px;padding-right:4px;color:<?php echo $zColorPro ?>"><?php echo $mDatMov[$cKey][$cKey2][$cKey3]['clinomxx'] ?></td>
                                <?php } ?>
                                  <td class="letra7" align="left"   style = "padding-left:4px;padding-right:4px;color:<?php echo $zColorPro ?>"><?php echo $mDatMov[$cKey][$cKey2][$cKey3]['facturax'] ?></td>
                                  <td class="letra7" align="center" style = "padding-left:4px;padding-right:4px;color:<?php echo $zColorPro ?>"><?php echo $mDatMov[$cKey][$cKey2][$cKey3]['comfecxx'] ?></td>
                                  <td class="letra7" align="center" style = "padding-left:4px;padding-right:4px;color:<?php echo $zColorPro ?>"><?php echo $mDatMov[$cKey][$cKey2][$cKey3]['sucidxxx'] ?></td>
                                  <td class="letra7" align="left"   style = "padding-left:4px;padding-right:4px;color:<?php echo $zColorPro ?>"><?php echo $mDatMov[$cKey][$cKey2][$cKey3]['comtraxx'] ?></td>
                                  <td class="letra7" align="right"  style = "padding-left:4px;padding-right:4px;color:<?php echo $zColorPro ?>"><?php echo number_format($mDatMov[$cKey][$cKey2][$cKey3]['vlrcifxx'],0,',','.') ?></td>
                                <?php  foreach ($mCtoDes as $cKeyCto => $cValueCto) {
                                  if($cColor == "#FFFFFF"){
                                    $cColor = "#F2F2F2";
                                  }else{
                                    $cColor = "#FFFFFF";
                                  }
                                  $nTotCto = $mDatMov[$cKey][$cKey2][$cKey3][$cKeyCto]['comvlrxx'];
                                  ?>
                                  <td class="letra7" align="right" style = "padding-left:4px;padding-right:4px;background-color:<?php echo $cColor ?>;color:<?php echo $zColorPro ?>"><?php  echo (number_format($nTotCto,0,',','.') != "")?number_format($nTotCto,0,',','.'):"&nbsp;" ?></td>
                                <?php }
                                $nTotCli = $mTotCli[$cKey][$cKey2][$cKey3]['comvlrxx'];
                                $nTotal += $nTotCli;
                                ?>
                                <td class="letra7" align="right"  style = "padding-left:4px;padding-right:4px;background-color:#E3F6CE;color:<?php echo $zColorPro ?>"><?php echo (number_format($nTotCli,0,',','.') != "")?number_format($nTotCli,0,',','.'):"&nbsp;" ?></td>
                              </tr>
                            <?php
                            }
                          }
                        } ?>
                        <tr style="background-color:#0B610B" height="20">
                          <td class="letra8" align="right"  style="padding-left:4px;padding-right:4px" colspan="<?php echo $nCol ?>"><b><font color=white>TOTALES</font></b></td>
                          <?php
                          foreach ($mCtoDes as $cKey => $cValue) {

                            $nToCto  = (number_format($mTotCto[$cKey]['comvlrxx'],0,',','.') != "") ? number_format($mTotCto[$cKey]['comvlrxx'],0,',','.') : 0;
                          ?>
                            <td class="letra8" align="right"  style="padding-left:4px;padding-right:4px" width="100px"><b><font color=white><?php echo $nToCto ?></font></b></td>
                          <?php
                        } ?>
                          <td class="letra8" align="right"  style="padding-left:4px;padding-right:4px" width="100px"><b><font color=white><?php echo (number_format($nTotal,0,',','.') != "")?number_format($nTotal,0,',','.'):0 ?></font></b></td>
                        </tr>
                      </table>
                    </center>
                  </form>
                </body>
              </html>
              <?php
            }
          break;
          case 2:
            // PINTA POR EXCEL //
            
            $cNomFile = "REPORTE_DE_INGRESOS_PROPIOS_POR_CONCEPTO_".$_COOKIE['kUsrId'].date("YmdHis").".xls";

            if ($_SERVER["SERVER_PORT"] != "") {
              $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;
            } else {
              $cFile = "{$OPENINIT['pathdr']}/opencomex/" . $vSysStr['system_download_directory'] . "/" . $cNomFile;
            }
            
            $fOp = fopen($cFile, 'a');

            $header .= 'REPORTE DE INGRESOS PROPIOS POR CONCEPTO'."\n";
            $header .= "\n";
            $data    = '';

            $data .= '<table border="1" cellspacing="0" cellpadding="0" align=center style="margin:5px">';
              $data .= '<tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">';
                $data .= '<td class="name" colspan="'.$nNumCol.'" align="left">';
                  $data .= '<font size="3">';
                    $data .= '<b>REPORTE DE INGRESOS PROPIOS POR CONCEPTO<BR>';
                    $data .= 'PERIODO:  DE '.$dFecIni.' A '.$dFecFin .'<br>';
                    if($gTerId != ""){
                      //Busco en la base de datos el nombre del cliente
                      $qDatExt  = "SELECT IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
                      $qDatExt .= "FROM $cAlfa.SIAI0150 ";
                      $qDatExt .= "WHERE ";
                      $qDatExt .= "CLIIDXXX = \"$gTerId\" AND ";
                      $qDatExt .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
                      $xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
                      $xRDE = mysql_fetch_array($xDatExt);
                      //f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));
                      $data .= 'CLIENTE: ['.$gTerId.'] '.$xRDE['clinomxx'].'<br>';
                    }
                    if ($vSucursal[1] != "") {
                    $data .= 'SUCURSAL: ['.$vSucursal[1].'] '.$vSucursal[2].'<br>';
                    }
                    if($gDirId != ""){
                      //Busco en la base de datos el nombre del director
                      $qSqlUsr  = "SELECT USRNOMXX ";
                      $qSqlUsr .= "FROM $cAlfa.SIAI0003 ";
                      $qSqlUsr .= "WHERE ";
                      $qSqlUsr .= "USRIDXXX = \"$gDirId\" AND ";
                      $qSqlUsr .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
                      $xSqlUsr = f_MySql("SELECT","",$qSqlUsr,$xConexion01,"");
                      $xRU = mysql_fetch_array($xSqlUsr);
                      $data .= 'DIRECTOR: ['.$gDirId.'] '.$xRU['USRNOMXX'].'<br>';
                    }
                    $data .= '</b>';
                  $data .= '</font>';
                $data .= '</td>';
              $data .= '</tr>';

              $data .= '<tr height="20">';
                $data .= '<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>VENDEDOR</font></b></td>';
                if($gTerId==""){
                  $data .= '<td style="background-color:#0B610B" class="letra8" width="100px" align="center"><b><font color=white>NIT</font></b></td>';
                  $data .= '<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>CLIENTE</font></b></td>';
                }
                $data .= '<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>FACTURA</font></b></td>';
                $data .= '<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>FECHA</font></b></td>';
                $data .= '<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>SUCURSAL</font></b></td>';
                $data .= '<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>DO</font></b></td>';
                $data .= '<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>VALOR CIF</font></b></td>';
                foreach ($mCtoDes as $cKey => $cValue) {
                  $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>'.$cValue.'</font></b></td>';
                }
                $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>TOTAL</font></b></td>';
              $data .= '</tr>';

              $nTotal = 0;
              foreach ($mDatMov as $cKey => $cValue) { //Primer Nivel - Cliente
                foreach ($cValue as $cKey2 => $cValue2) { //Segundo Nivel - Factua
                  foreach ($cValue2 as $cKey3 => $cValue3) { //Tercer Nivel - Sucursal
                    $zColorPro = "#000000";
                    $cColor = "#FFFFFF";
                    $data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
                      $data .= '<td class="letra7" align="left" style = "color:'.$zColorPro.'">'.$mDatMov[$cKey][$cKey2][$cKey3]['clivenxx'].'</td>';
                      if($gTerId==""){
                        $data .= '<td class="letra7" align="left" style = "color:'.$zColorPro.'">'.$mDatMov[$cKey][$cKey2][$cKey3]['teridxxx'].'</td>';
                        $data .= '<td class="letra7" align="left" style = "color:'.$zColorPro.'">'.$mDatMov[$cKey][$cKey2][$cKey3]['clinomxx'].'</td>';
                      }
                      $data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$mDatMov[$cKey][$cKey2][$cKey3]['facturax'].'</td>';
                      $data .= '<td class="letra7" align="center" style = "color:'.$zColorPro.'">'.$mDatMov[$cKey][$cKey2][$cKey3]['comfecxx'].'</td>';
                      $data .= '<td class="letra7" align="center" style = "color:'.$zColorPro.'">'.$mDatMov[$cKey][$cKey2][$cKey3]['sucidxxx'].'</td>';
                      $data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$mDatMov[$cKey][$cKey2][$cKey3]['comtraxx'].'</td>';
                      $data .= '<td class="letra7" align="right"  style = "color:'.$zColorPro.'">'.(number_format($mDatMov[$cKey][$cKey2][$cKey3]['vlrcifxx'],0,',','.')).'</td>';
                      foreach ($mCtoDes as $cKeyCto => $cValueCto) {
                        if($cColor == "#FFFFFF"){
                        $cColor = "#F2F2F2";
                        }else{
                        $cColor = "#FFFFFF";
                        }
                        $nTotCto = $mDatMov[$cKey][$cKey2][$cKey3][$cKeyCto]['comvlrxx'];
                        $data .= '<td class="letra7" align="right" style = "background-color:'.$cColor.';color:'.$zColorPro.'">'.((number_format($nTotCto,0,',','.') != "")?number_format($nTotCto,0,',','.'):"&nbsp;").'</td>';
                      }
                      $nTotCli = $mTotCli[$cKey][$cKey2][$cKey3]['comvlrxx'];
                      $nTotal += $nTotCli;
                      $data .= '<td class="letra7" align="right"  style = "background-color:#E3F6CE;color:'.$zColorPro.'">'.((number_format($nTotCli,0,',','.') != "")?number_format($nTotCli,0,',','.'):"&nbsp;").'</td>';
                    $data .= '</tr>';
                  }
                }
              }
              $data .= '<tr height="20" style="padding-left:4px;padding-right:4px">';
                $data .= '<td class="letra8" style="background-color:#0B610B" align="right" colspan="'.$nCol.'"><b><font color=white>TOTALES</font></b></td>';
                foreach ($mCtoDes as $cKey => $cValue) {
                  $nToCto  = (number_format($mTotCto[$cKey]['comvlrxx'],0,',','.') != "") ? number_format($mTotCto[$cKey]['comvlrxx'],0,',','.') : 0;
                  $data .= '<td class="letra8" style="background-color:#0B610B" align="right" width="100px"><b><font color=white>'.$nToCto.'</font></b></td>';
                }
                $data .= '<td class="letra8" style="background-color:#0B610B" align="right" width="100px"><b><font color=white>'.((number_format($nTotal,0,',','.') != "")?number_format($nTotal,0,',','.'):0).'</font></b></td>';
              $data .= '</tr>';
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
              } else {
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
      } else {
        if ($_SERVER["SERVER_PORT"] != "") {
          f_Mensaje(__FILE__,__LINE__,"No Se Generaron Registros.");
          switch ($cTipo) {
            case 1:
              // PINTA POR PANTALLA// ?>
              <script languaje = "javascript">
                window.close();
              </script>
            <?php break;
            case 2:
            default:
            break;
          }
        } else {
          $cMsj .= "No Se Generaron Registros.";
        }
      }
    }
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
	} // fin del if ($_SERVER["SERVER_PORT"] == "")
?>

<?php
  /**
   * Genera archivo excel de Reporte Facturas Provisionales
   * @package opencomex
   * @todo ALL
   */

  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors", "1");

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

  /**
   *  Cookie fija
   */
  $kDf = explode("~",$_COOKIE["kDatosFijos"]);
  $kMysqlHost = $kDf[0];
  $kMysqlUser = $kDf[1];
  $kMysqlPass = $kDf[2];
  $kMysqlDb   = $kDf[3];
  $kUser      = $kDf[4];
  $kLicencia  = $kDf[5];
  $swidth     = $kDf[6];
  $kModId     = $_COOKIE["kModId"];
  $kProId     = $_COOKIE["kProId"];

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
  } elseif ($_SERVER["SERVER_PORT"] == "") {
    $gTerId        = $_POST['gTerId'];
		$gTerId2       = $_POST['gTerId2'];
		$gDesde_prov   = $_POST['gDesde_prov'];
		$gHasta_prov   = $_POST['gHasta_prov'];
		$gDesde_def    = $_POST['gDesde_def'];
		$gHasta_def    = $_POST['gHasta_def'];
	}  // fin del if ($_SERVER["SERVER_PORT"] == "")

  $nSwitch = 0;
  $cMsj = "";

  //Asignacion de titulos
  $cProvisional = 'RANGO DE FECHAS (FACT. PROVISIONALES): ';
  $cDefinitiva  = 'RANGO DE FECHAS (FACT. DEFINITIVAS): ' ;

  //Validacion de las fechas
  if($gDesde_prov == "0000-00-00" || $gHasta_prov == "0000-00-00" ||
    $gDesde_def == "0000-00-00" || $gHasta_def == "0000-00-00"){

    $nSwitch = 1;
    $cMsj .= "Debe Ingresar Rango De Fechas Valido. ";
  }

  if($gDesde_prov != "" && $gHasta_prov != "" &&
    $gDesde_def != "" && $gHasta_def != ""){

    $nSwitch = 1;
    $cMsj .= "Debe Ingresar Solo un Rango de Fechas. ";
  }

  //Valido que la Fecha sea del mismo ano
  if(substr($gDesde_prov,0,4) != substr($gHasta_prov,0,4)){
    $nSwitch = 1;
    $cMsj .= "Debe Seleccionar un Rango de Fechas del mismo A&ntilde;o.\n";
  }
  
  //Valido que la Fecha Hasta no sea menor a la Fecha Desde
  if($gDesde_prov != "" && $gHasta_prov != ""){
    $cProvisional .= $gDesde_prov. ' al ' .$gHasta_prov;
    if($gHasta_prov < $gDesde_prov){
      $nSwitch = 1;
      $cMsj .= "La Fecha Hasta Provisional no puede ser menor a la Fecha Desde Provisional.\n";
    }
  }else{
    $cProvisional .= 'NO DEFINIDO';
  }

  //Valido que la Fecha sea del mismo ano
  if(substr($gDesde_def,0,4) != substr($gHasta_def,0,4)){
    $nSwitch = 1;
    $cMsj .= "Debe Seleccionar un Rango de Fechas del mismo A&ntilde;o.\n";
  }

  //Valido que la Fecha Hasta no sea menor a la Fecha Desde
  if($gDesde_def != "" && $gHasta_def != ""){
    $cDefinitiva .= $gDesde_def. ' al ' .$gHasta_def;
    if($gHasta_def < $gDesde_def){
      $nSwitch = 1;
      $cMsj .= "La Fecha Hasta Definitiva no puede ser menor a la Fecha Desde Definitiva.\n";
    }
  }else{
    $cDefinitiva .= 'NO DEFINIDO';
  }
  //Fin Validaciones

  if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
		$cEjePro = 1;
	
		$strPost = "gTerId~" . $gTerId . "|gTerId2~" . $gTerId2 . "|gDesde_prov~" . $gDesde_prov . "|gHasta_prov~" . $gHasta_prov . "|gDesde_def~" . $gDesde_def . "|gHasta_def~" . $gHasta_def;

		$vParBg['pbadbxxx'] = $cAlfa;                           // Base de Datos
		$vParBg['pbamodxx'] = "FACTURACION";                    // Modulo
		$vParBg['pbatinxx'] = "FACTURASPROVISIONALES";          // Tipo Interface
		$vParBg['pbatinde'] = "FACTURAS PROVISIONALES";         // Descripcion Tipo de Interfaz
		$vParBg['admidxxx'] = "";                               // Sucursal
		$vParBg['doiidxxx'] = "";                               // Do
		$vParBg['doisfidx'] = "";                               // Sufijo
		$vParBg['cliidxxx'] = $gTerId;                          // Nit
		$vParBg['clinomxx'] = $xDDE['clinomxx'];                // Nombre Importador
		$vParBg['pbapostx'] = $strPost;													// Parametros para reconstruir Post
		$vParBg['pbatabxx'] = "";                               // Tablas Temporales
		$vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];      // Script
		$vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];          // cookie
		$vParBg['pbacrexx'] = 0;                                // Cantidad Registros
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
			f_Mensaje(__FILE__, __LINE__, $cMsj);
		}
	} // fin del if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0)

  //CONSULTAS
  if ($cEjePro == 0) {
    if($nSwitch == 0){
      //Consultar a la tabla SIAI0150 para $gTerId
      $qCliente  = "SELECT ";
      $qCliente .= "CLIIDXXX,REGESTXX, ";
      $qCliente .= "IF(CLINOMXX != \"\",CLINOMXX,IF((TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X))) != \"\",(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X))),\"SIN NOMBRE\")) AS CLINOMXX ";
      $qCliente .= "FROM $cAlfa.SIAI0150 ";
      $qCliente .= "WHERE ";
      $qCliente .= "CLIIDXXX = \"{$gTerId}\"";
      $xCliente  = f_MySql("SELECT","",$qCliente,$xConexion01,"");
      //f_Mensaje(__FILE__,__LINE__,  $qCliente." ~ ".mysql_num_rows($xCliente ));
      if (mysql_num_rows($xCliente) > 0) {
        $mDatos = array();
        while ($xR = mysql_fetch_array($xCliente)) {
          $mInd_mDatos = count($mDatos); // índice para mDatos
          $nCliente = $xR['CLINOMXX']; //nombre Cliente
        }
      }

      // Indice autoincremental para la matriz
      $nIndItem = 0;

      // Se define el anio desde y hasta para consultar la tabla de cabecera
      $dDesde = ($gDesde_prov != '') ? substr($gDesde_prov, 0, 4) : substr($gDesde_def, 0, 4);
      $dHasta = ($gDesde_prov != '') ? Date('Y') : substr($gDesde_def, 0, 4);

      for ($cAnio = $dDesde; $cAnio<=$dHasta; $cAnio++) {
        // Consulta a la tabla fcocYYYY
        $qCliFac  = "SELECT * ";
        $qCliFac .= "FROM $cAlfa.fcoc$cAnio ";
        $qCliFac .= "WHERE ";
        $qCliFac .= "comidxxx = \"F\" AND ";
        // Validacion si existe Fecha Provisional
        if($gDesde_prov != "" && $gHasta_prov != ""){
          $qCliFac .= "((regestxx = \"PROVISIONAL\" AND ";
          $qCliFac .= "(comfecxx BETWEEN \"{$gDesde_prov}\" AND \"{$gHasta_prov}\")) OR ";
          $qCliFac .= "(regestxx = \"ACTIVO\" ";
          $qCliFac .= "AND (comfprfe != \"\" OR comfprfe != \"0000-00-00\") AND (comfprfe BETWEEN \"{$gDesde_prov}\" AND \"{$gHasta_prov}\"))) ";
        }
        // Validacion si existe Fecha Definitiva
        if($gDesde_def != "" && $gHasta_def != ""){
          $qCliFac .= "regestxx = \"ACTIVO\" AND comfecxx BETWEEN \"{$gDesde_def}\" AND \"{$gHasta_def}\" ";
        }

        if($gTerId != ""){
          $qCliFac .= "AND teridxxx = \"{$gTerId}\" ";
        }
        if($gTerId2 != ""){
          $qCliFac .= "AND terid2xx = \"{$gTerId2}\" ";
        }
        $xCliFac  = f_MySql("SELECT","",$qCliFac,$xConexion01,"");
        //f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));

        while($xRCF = mysql_fetch_array($xCliFac)){
          //Consultar a la tabla SIAI0150 para $gTerId
          $qFacCli  = "SELECT ";
          $qFacCli .= "CLIIDXXX, ";
          $qFacCli .= "IF(CLINOMXX != \"\",CLINOMXX,IF((TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X))) != \"\",(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X))),\"SIN NOMBRE\")) AS CLINOMXX ";
          $qFacCli .= "FROM $cAlfa.SIAI0150 ";
          $qFacCli .= "WHERE ";
          if($gTerId == ""){
            $qFacCli .= "CLIIDXXX =\"{$xRCF['teridxxx']}\"";
          }
          if($gTerId != ""){
            $qFacCli .= "CLIIDXXX =\"{$gTerId}\"";
          }
          $xFacCli = f_MySql("SELECT","",$qFacCli,$xConexion01,"");
          $vFacCli = mysql_fetch_array($xFacCli);
          //f_Mensaje(__FILE__,__LINE__,  $qFacCli." ~ ".mysql_num_rows($xFacCli));

          //Consultar a la tabla SIAI0150 para $gTerId2
          $qFacturar  = "SELECT ";
          $qFacturar .= "CLIIDXXX, ";
          $qFacturar .= "IF(CLINOMXX != \"\",CLINOMXX,IF((TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X))) != \"\",(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X))),\"SIN NOMBRE\")) AS CLINOMXX ";
          $qFacturar .= "FROM $cAlfa.SIAI0150 ";
          $qFacturar .= "WHERE ";
          if($gTerId2 == ""){
            $qFacturar .= "CLIIDXXX =\"{$xRCF['terid2xx']}\"";
          }
          if($gTerId2 != ""){
            $qFacturar .= "CLIIDXXX =\"{$gTerId2}\"";
          }
          $xFacturar = f_MySql("SELECT","",$qFacturar,$xConexion01,"");
          $vFacturar = mysql_fetch_array($xFacturar);
          //f_Mensaje(__FILE__,__LINE__,  $qFacturar." ~ ".mysql_num_rows($xFacturar));

          ##Exploto campo Matriz para traer primer Do, Centro de Costos, Subcentro de costos
          $cDocId  = ""; $cDocSuc = ""; $cDocSuf = ""; $cCcoId = ""; $cSccId = "";
          $mComFp = explode("|",$xRCF['comfpxxx']);
          for ($i=0;$i<count($mComFp);$i++) {
            if($mComFp[$i] != "") {
              $vComFp  = explode("~",$mComFp[$i]);
              if($cDocId == "") {
                $cDocId  = $vComFp[2];
                $cDocSuf = $vComFp[3];
                $cSucId  = $vComFp[15];
                $cCcoId  = $vComFp[16];
                $cSccId  = $vComFp[26];
              }
            }//if($mComFp[$i] != ""){
          }//for ($i=0;$i<count($mComFp);$i++) {

          //Busco Centro de Costo
          $qCcoDes  = "SELECT ";
          $qCcoDes .= "ccoidxxx,";
          $qCcoDes .= "ccodesxx ";
          $qCcoDes .= "FROM $cAlfa.fpar0116 ";
          $qCcoDes .= "WHERE ";
          $qCcoDes .= "ccoidxxx = \"{$cCcoId}\" LIMIT 0,1 ";
          $xCcoDes  = f_MySql("SELECT", "", $qCcoDes, $xConexion01, "");
          $vCcoDes  = mysql_fetch_array($xCcoDes);

          //Busco Centro de Costo
          $qSccDes  = "SELECT ";
          $qSccDes .= "sccidxxx,";
          $qSccDes .= "sccdesxx ";
          $qSccDes .= "FROM $cAlfa.fpar0120 ";
          $qSccDes .= "WHERE ";
          $qSccDes .= "ccoidxxx = \"{$cCcoId}\" AND ";
          $qSccDes .= "sccidxxx = \"{$cSccId}\" LIMIT 0,1 ";
          $xSccDes  = f_MySql("SELECT", "", $qSccDes, $xConexion01, "");
          $vSccDes  = mysql_fetch_array($xSccDes);

          $mImprime[$nIndItem]["docidxxx"]=$cSucId."-".$cDocId."-".$cDocSuf; //Nro DO
          $mImprime[$nIndItem]["ccodesxx"]=$vCcoDes['ccoidxxx']."-".$vCcoDes['ccodesxx']; //CC DO
          $mImprime[$nIndItem]["sccdesxx"]=$vSccDes['sccidxxx']."-".$vSccDes['sccdesxx']; //SC DO
          if($xRCF["regestxx"] == 'PROVISIONAL'){
            $mImprime[$nIndItem]["comfacpr"] = $xRCF["comcscxx"]; // Prefactura
            $mImprime[$nIndItem]["comfecxx"] = $xRCF["comfecxx"]; // Fecha Prefactura
            $mImprime[$nIndItem]["comidxxx"] = "Sin Legalizar";   // Factura
            $mImprime[$nIndItem]["comfecve"] = "";                // Fecha Factura
          }
          if($xRCF["regestxx"] == 'ACTIVO'){
            $comfacpr = explode( '-', $xRCF['comfacpr'] );
            $comidxxx = $xRCF["comidxxx"]."-".$xRCF["comcodxx"]."-".$xRCF["comcscxx"];
            $mImprime[$nIndItem]["comfacpr"] = $comfacpr[2];        // Prefactura
            $mImprime[$nIndItem]["comfecxx"] = $xRCF["comfprfe"];   // Fecha Prefactura
            $mImprime[$nIndItem]["comidxxx"] = $comidxxx;           // Factura
            $mImprime[$nIndItem]["comfecve"] = $xRCF["comfecxx"];   // Fecha Factura
          }
          $mImprime[$nIndItem]["teridxxx"]=$xRCF["teridxxx"];       //Nit
          $mImprime[$nIndItem]["clinomxx"]=$vFacCli["CLINOMXX"];    //Cliente
          $mImprime[$nIndItem]["terid2xx"]=$xRCF["terid2xx"];       //Nit terid2xx
          $mImprime[$nIndItem]["clinomx2"]=$vFacturar["CLINOMXX"];  //Facturar A
          $mImprime[$nIndItem]["comvlr01"]=$xRCF["comvlr01"];       //Anticipo
          $mImprime[$nIndItem]["comvlr02"]=$xRCF["comvlr02"];       // Pagos a Terceros para sumatoria
          $mImprime[$nIndItem]["comvlr03"]=$xRCF["comvlr03"];       // Pagos a Terceros para sumatoria
          $mImprime[$nIndItem]["comifxxx"]=$xRCF["comifxxx"];       // Impuesto Financiero
          $mImprime[$nIndItem]["comipxxx"]=$xRCF["comipxxx"];       //Ingresos Propios
          $mImprime[$nIndItem]["comivaxx"]=$xRCF["comivaxx"];       //Iva
          $mImprime[$nIndItem]["comrftex"]=$xRCF["comrftex"];       //ReteFuente
          $mImprime[$nIndItem]["comarfte"]=$xRCF["comarfte"];       //AutoReteFuente
          $mImprime[$nIndItem]["comrcrex"]=$xRCF["comrcrex"];       //ReteCree
          $mImprime[$nIndItem]["comarcre"]=$xRCF["comarcre"];       //AutoReteCree
          $mImprime[$nIndItem]["comricax"]=$xRCF["comricax"];       //ReteIca
          $mImprime[$nIndItem]["comarica"]=$xRCF["comarica"];       //AutoReteIca
          $mImprime[$nIndItem]["comrivax"]=$xRCF["comrivax"];       //ReteIva
          $mImprime[$nIndItem]["comvlrto"]=round(($xRCF["comvlr01"]*-1)+
            ($xRCF["comvlr02"]+$xRCF["comvlr03"]+$xRCF["comifxxx"])
            +$xRCF["comipxxx"]+$xRCF["comivaxx"]-
            ($xRCF["comrftex"]+$xRCF["comrcrex"]+$xRCF["comricax"]+$xRCF["comrivax"])+
            ($xRCF["comarfte"]+$xRCF["comarcre"]+$xRCF["comarica"]),2)+0; // para el Valor Total

          $nIndItem++;

        }//($gTerId2 = mysql_fetch_array($xDatExt))
      }

      if(count($mImprime) > 0){
        #PINTAR EXCEL //Reporte facturas provisionales
        $nNumCol = 24;

        /**
        * Random para asignar al nombre del archivo
        */
        $cCadenaA  = mt_rand(1000000000, 9999999999);

        $header  .= 'REPORTE DE INGRESOS PROPIOS Y PAGOS A TERCEROS FACTURADOS'."\n";
        $header  .= "\n";
        $cData    = '';
        $cNomFile = "REPORTE_FACTURAS_PROVISIONALES_".$_COOKIE['kUsrId'].date("YmdHis")."_".$cCadenaA.".xls";

        if ($_SERVER["SERVER_PORT"] != "") {
          $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;
        } else {
          $cFile = "{$OPENINIT['pathdr']}/opencomex/" . $vSysStr['system_download_directory'] . "/" . $cNomFile;
        }
    
        if (file_exists($cFile)) {
          unlink($cFile);
        }

        $fOp = fopen($cFile, 'a');

        #Tabla para el reporte
        $cData.= '<table border="1" cellspacing="0" cellpadding="0" align=center style="margin:5px">';

        # Cabecera:
        $cData.= '<tr>';
        $cData.= '<td class="name" align="left" colspan="'.$nNumCol.'">';
        $cData.= '<font size="3"><b>';
        $cData.= $cProvisional.'<br>';
        $cData.= $cDefinitiva;
        $cData.= '</b>';
        $cData.= '</font>';
        $cData.= '</td>';
        $cData.= '</tr>';
        $cData.= '<tr>';
        $cData.= '<td class="name" align="left" colspan="'.($nNumCol-4).'">';
        $cData.= '<b>Cliente: </b>'.$nCliente;
        $cData.= '</td>';
        $cData.= '<td class="name" align="left" colspan="2">';
        $cData.= '<b>Nit: </b>'.($gTerId != "" ? $gTerId."-".f_Digito_Verificacion($gTerId) : "");
        $cData.= '</td>';
        $cData.= '<td class="name" align="left" colspan="2">';
        $cData.= '<b>Fecha Impresi&oacute;n: </b>'.date('Y-m-d');
        $cData.= '</td>';
        $cData.= '</tr>';

        # Columnas
        $cData.= '<tr>';
        $cData.= '<td style="background-color:#0B610B" width="120px" align="left"><b><font color=white>DO</font></b></td>';
        $cData.= '<td style="background-color:#0B610B" width="150px" align="left"><b><font color=white>CC DO</font></b></td>';
        $cData.= '<td style="background-color:#0B610B" width="150px" align="left"><b><font color=white>SC DO</font></b></td>';
        $cData.= '<td style="background-color:#0B610B" width="120px" align="left"><b><font color=white>Numero Prefactura</font></b></td>';
        $cData.= '<td style="background-color:#0B610B" width="120px" align="left"><b><font color=white>Fecha Prefactura</font></b></td>';
        $cData.= '<td style="background-color:#0B610B" width="120px" align="left"><b><font color=white>Factura</font></b></td>';
        $cData.= '<td style="background-color:#0B610B" width="120px" align="left"><b><font color=white>Fecha Factura</font></b></td>';
        $cData.= '<td style="background-color:#0B610B" width="450px" align="left"><b><font color=white>Nit</font></b></td>';
        $cData.= '<td style="background-color:#0B610B" width="150px" align="left"><b><font color=white>Cliente</font></b></td>';
        $cData.= '<td style="background-color:#0B610B" width="400px" align="left"><b><font color=white>Nit</font></b></td>';
        $cData.= '<td style="background-color:#0B610B" width="150px" align="left"><b><font color=white>Facturar A</font></b></td>';
        $cData.= '<td style="background-color:#0B610B" width="150px" align="left"><b><font color=white>Anticipo</font></b></td>';
        $cData.= '<td style="background-color:#0B610B" width="150px" align="left"><b><font color=white>Pagos a Terceros</font></b></td>';
        $cData.= '<td style="background-color:#0B610B" width="80px"  align="left"><b><font color=white>Impuesto Financiero</font></b></td>';
        $cData.= '<td style="background-color:#0B610B" width="150px" align="left"><b><font color=white>Ingresos Propios</font></b></td>';
        $cData.= '<td style="background-color:#0B610B" width="400px" align="left"><b><font color=white>IVA</font></b></td>';
        $cData.= '<td style="background-color:#0B610B" width="150px" align="left"><b><font color=white>ReteFuente</font></b></td>';
        $cData.= '<td style="background-color:#0B610B" width="150px" align="left"><b><font color=white>AutoReteFuente</font></b></td>';
        $cData.= '<td style="background-color:#0B610B" width="150px" align="left"><b><font color=white>ReteCree</font></b></td>';
        $cData.= '<td style="background-color:#0B610B" width="150px" align="left"><b><font color=white>AutoReteCree</font></b></td>';
        $cData.= '<td style="background-color:#0B610B" width="150px" align="left"><b><font color=white>Retelca</font></b></td>';
        $cData.= '<td style="background-color:#0B610B" width="80px"  align="left"><b><font color=white>AutoRetelca</font></b></td>';
        $cData.= '<td style="background-color:#0B610B" width="150px" align="left"><b><font color=white>Retelva</font></b></td>';
        $cData.= '<td style="background-color:#0B610B" width="80px"  align="left"><b><font color=white>Valor Total</font></b></td>';
        $cData.= '</tr>';

        // Columnas
        //Muestro la Matriz con los datos.
        $cColor = "#FFFFFF";

        for($i = 0;$i < count($mImprime);$i++) {
          $cData.= '<tr bgcolor = "white" height="20" style="padding-right:4px;padding-right:4px">';
          $cData.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$mImprime[$i]["docidxxx"].'</td>';  //Nro DO
          $cData.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$mImprime[$i]["ccodesxx"].'</td>';  //CC DO
          $cData.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$mImprime[$i]["sccdesxx"].'</td>';  //SC DO
          $cData.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$mImprime[$i]["comfacpr"].'</td>'; // Prefactura [PROV]
          $cData.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$mImprime[$i]["comfecxx"].'</td>'; // Fecha Prefactura [PROV]
          $cData.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$mImprime[$i]["comidxxx"].'</td>'; // Factura [DEF]
          $cData.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$mImprime[$i]["comfecve"].'</td>'; // Fecha Factura [DEF]
          $cData.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$mImprime[$i]["teridxxx"].'</td>'; //Nit
          $cData.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$mImprime[$i]["clinomxx"].'</td>'; //Cliente
          $cData.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$mImprime[$i]["terid2xx"].'</td>'; //Nit terid2xx
          $cData.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$mImprime[$i]["clinomx2"].'</td>'; //Facturar A
          $cData.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($mImprime[$i]["comvlr01"],2,',','').'</td>'; //Anticipo
          $cData.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($mImprime[$i]["comvlr02"]+$mImprime[$i]["comvlr03"],2,',','').'</td>'; // Pagos a Terceros
          $cData.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($mImprime[$i]["comifxxx"],2,',','').'</td>'; // Impuesto Financiero
          $cData.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($mImprime[$i]["comipxxx"],2,',','').'</td>'; //Ingresos Propios
          $cData.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($mImprime[$i]["comivaxx"],2,',','').'</td>'; //IVA
          $cData.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($mImprime[$i]["comrftex"],2,',','').'</td>'; //ReteFuente
          $cData.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($mImprime[$i]["comarfte"],2,',','').'</td>'; //AutoReteFuente
          $cData.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($mImprime[$i]["comrcrex"],2,',','').'</td>'; //ReteCree
          $cData.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($mImprime[$i]["comarcre"],2,',','').'</td>'; //AutoReteCree
          $cData.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($mImprime[$i]["comricax"],2,',','').'</td>'; //ReteIca
          $cData.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($mImprime[$i]["comarica"],2,',','').'</td>'; //AutoReteIca
          $cData.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($mImprime[$i]["comrivax"],2,',','').'</td>'; //ReteIva
          $cData.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($mImprime[$i]["comvlrto"],2,',','').'</td>'; //Valor Total
          $cData.= '</tr>';
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
          f_Mensaje(__FILE__,__LINE__,"No se encontraron registros.");
        } else {
          $cMsj .= "No se encontraron registros.";
        }
      }
    }// if nSwitch
  } // if cEjePro 

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
	} else {
    if($nSwitch == 1){
      f_Mensaje(__FILE__,__LINE__, $cMsj);
    }
  }
?>

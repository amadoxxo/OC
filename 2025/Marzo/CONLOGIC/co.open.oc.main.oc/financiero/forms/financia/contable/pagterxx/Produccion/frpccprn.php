<?php
  /**
	 * Imprime Concepto de Pagos a Terceros.
	 * --- Descripcion: Permite Imprimir Concepto de Pagos a Terceros.
	 * @author Yulieth Campos <ycampos@opentecnologia.com.co>
	 */

  //ini_set('error_reporting', E_ERROR);
  //ini_set("display_errors","1");

  ini_set("memory_limit","512M");
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

  if ($_SERVER["SERVER_PORT"] == "") {
    $gPeriodo = $_POST['gPeriodo'];
    $gMes     = $_POST['gMes'];
    $gDirId   = $_POST['gDirId'];
    $gTerId   = $_POST['gTerId'];
    $gCotId   = $_POST['gCotId'];
    $gPucId   = $_POST['gPucId'];
    $cTipo    = $_POST['cTipo'];
  }


  if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
    $cEjePro = 1;

    $strPost  = "|gPeriodo~".$gPeriodo;
    $strPost .= "|gMes~".$gMes;
    $strPost .= "|gDirId~".$gDirId;
    $strPost .= "|gTerId~".$gTerId;
    $strPost .= "|gCotId~".$gCotId;
    $strPost .= "|gPucId~".$gPucId;
    $strPost .= "|cTipo~".$cTipo;
    $nRegistros = 0;

    $vParBg['pbadbxxx'] = $cAlfa;                              //Base de Datos
    $vParBg['pbamodxx'] = "FACTURACION";                       //Modulo
    $vParBg['pbatinxx'] = "CONCEPTOSDEPAGOSATERCEROS";         //Tipo Interface
    $vParBg['pbatinde'] = "CONCEPTOS DE PAGOS A TERCEROS";     //Descripcion Tipo de Interfaz
    $vParBg['admidxxx'] = "";                                  //Sucursal
    $vParBg['doiidxxx'] = "";                                  //Do
    $vParBg['doisfidx'] = "";                                  //Sufijo
    $vParBg['cliidxxx'] = "";                                  //Nit
    $vParBg['clinomxx'] = "";                                  //Nombre Importador
    $vParBg['pbapostx'] = $strPost;														 //Parametros para reconstruir Post
    $vParBg['pbatabxx'] = "";                                  //Tablas Temporales
    $vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];         //Script
    $vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];             //cookie
    $vParBg['pbacrexx'] = $nRegistros;                         //Cantidad Registros
    $vParBg['pbatxixx'] = 1;                                   //Tiempo Ejecucion x Item en Segundos
    $vParBg['pbaopcxx'] = "";                                  //Opciones
    $vParBg['regusrxx'] = $kUser;                              //Usuario que Creo Registro
  
    #Incluyendo la clase de procesos en background
    $ObjProBg = new cProcesosBackground();
    $mReturnProBg = $ObjProBg->fnCrearProcesoBackground($vParBg);
  
    #Imprimiendo resumen de todo ok.
    if ($mReturnProBg[0] == "true") {
      f_Mensaje(__FILE__, __LINE__, "Proceso en Background Agendado con Exito."); ?>
      <script languaje = "javascript">
          parent.fmwork.fnRecargar();
      </script>
    <?php 
    } else {
      $nSwitch = 1;
      for ($nR = 1; $nR < count($mReturnProBg); $nR++) {
        $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
        $cMsj .= $mReturnProBg[$nR] . "\n";
      }
      f_Mensaje(__FILE__, __LINE__, $cMsj."Verifique.");
    }
  }  

  if ($cEjePro == 0) {
		if ($nSwitch == 0) {

      switch ($cTipo) {
        case 1:
          // PINTA POR PANTALLA// ?>
          <html>
            <head>
              <title>Reporte de Concepto de Pagos a Terceros</title>
              <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
              <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
              <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
              <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
            </head>
            <body>
              <div id="loading" style="background: white;position: absolute;left: 45%;top: 45%;padding: 2px;height: auto;border: 1px solid #ccc;">
                <div style="background: white;color: #444;font: bold 13px tahoma, arial, helvetica;padding: 10px;margin: 0;height: auto;">
                    <img src="<?php echo $cPlesk_Skin_Directory ?>/loading.gif" width="32" height="32" style="margin-right:8px;float:left;vertical-align:top;"/>
                    openComex<br>
                    <span style="font: normal 10px arial, tahoma, sans-serif;">Cargando...</span>
                </div>
              </div>
        <?php 
        break;
      }

      /**
       * Matriz Auxiliar para los conceptos contables.
       * @var array
       */
      $cCtoPCC  = "";
      $qCtoPCC  = "SELECT * ";
      $qCtoPCC .= "FROM $cAlfa.fpar0119 ";
      $qCtoPCC .= "WHERE ";
      $qCtoPCC .= "ctopccxx = \"SI\" ";
      $xCtoPCC  = mysql_query($qCtoPCC,$xConexion01);
      // echo "<br>".$qCtoPCC."~".mysql_num_rows($xCtoPCC);
      while($xRCAP = mysql_fetch_array($xCtoPCC)) {
        $cCtoPCC .= "\"{$xRCAP['ctoidxxx']}\",";
      }

      $qCtoPCC  = "SELECT * ";
      $qCtoPCC .= "FROM $cAlfa.fpar0121 "; // conceptos contables de causacion automatica
      $xCtoPCC  = mysql_query($qCtoPCC,$xConexion01);
      // echo "<br>".$qCtoPCC."~".mysql_num_rows($xCtoPCC);
      while($xRCAP = mysql_fetch_array($xCtoPCC)) {
        $cCtoPCC .= "\"{$xRCAP['ctoidxxx']}\",";
      }

      $cCtoPCC = substr($cCtoPCC,0,-1);

      //Hago el Select para pintar por Pantalla o en Excel
      $cPerAno = $gPeriodo;

      //Fecha Inicial
      $dFecIni = $gPeriodo."-".$gMes."-01";
      $dFecFin = $gPeriodo."-".$gMes."-".date ('d', mktime (0, 0, 0, $gMes + 1, 0, $cPerAno));

      $qDatSuc  = "SELECT $cAlfa.fpar0008.sucidxxx,$cAlfa.fpar0008.ccoidxxx,$cAlfa.fpar0008.sucdesxx ";
      $qDatSuc .= "FROM $cAlfa.fpar0008 ";
      $qDatSuc .= "WHERE ";
      $qDatSuc .= "$cAlfa.fpar0008.regestxx = \"ACTIVO\" ";
      $qDatSuc .= "ORDER BY $cAlfa.fpar0008.sucidxxx ";
      $xDatSuc  = f_MySql("SELECT","",$qDatSuc,$xConexion01,"");
      $mDatSuc = array();
      //echo $qDatSuc."~".mysql_num_rows($xDatSuc);
      while ($xRDS = mysql_fetch_array($xDatSuc)){
        $mDatSuc[$xRDS['sucidxxx']] = $xRDS['sucdesxx'];
      }

      /******/
      /**
       * *Select para buscar las facturas del periodo
       */

      $qMovCab  = "SELECT  $cAlfa.fcoc$cPerAno.teridxxx,$cAlfa.fcoc$cPerAno.ccoidxxx,CONCAT($cAlfa.fcoc$cPerAno.comidxxx,'-',$cAlfa.fcoc$cPerAno.comcodxx,'-',$cAlfa.fcoc$cPerAno.comcscxx,'-',$cAlfa.fcoc$cPerAno.comcscxx) as facturas ";
      $qMovCab .= "FROM $cAlfa.fcoc$cPerAno ";
      $qMovCab .= "WHERE ";
      $qMovCab .= "$cAlfa.fcoc$cPerAno.comidxxx=\"F\"        AND ";
      $qMovCab .= "$cAlfa.fcoc$cPerAno.regestxx = \"ACTIVO\"  ";
      if($gTerId!=""){
        $qMovCab .= "AND $cAlfa.fcoc$cPerAno.teridxxx = \"{$gTerId}\" ";
      }
      if($gDirId!=""){
        $qMovCab .= "AND $cAlfa.fcoc$cPerAno.diridxxx = \"{$gDirId}\" ";
      }
      $qMovCab .=  "AND $cAlfa.fcoc$cPerAno.comfecxx BETWEEN \"$dFecIni\" AND \"$dFecFin\" ";
      $qMovCab .=  "ORDER BY $cAlfa.fcoc$cPerAno.teridxxx,$cAlfa.fcoc$cPerAno.ccoidxxx,facturas ";
      $xMovCab  = f_MySql("SELECT","",$qMovCab,$xConexion01,"");
      $cFacturas = "";
      while ($xRMC = mysql_fetch_array($xMovCab)){
        $cFacturas .= "\"{$xRMC['facturas']}\",";
      }

      $cFacturas = substr($cFacturas,0,strlen($cFacturas)-1);

      if($cFacturas != ""){

        //select internos para buscar la sucursal
        $nPos01 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx)";
        $nPos02 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos01+1)";
        $nPos03 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos02+1)";
        $nPos04 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos03+1)";
        $nPos05 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos04+1)";
        $nPos06 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos05+1)";
        $nPos07 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos06+1)";
        $nPos08 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos07+1)";
        $nPos09 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos08+1)";
        $nPos10 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos09+1)";
        $nPos11 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos10+1)";
        $nPos12 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos11+1)";
        $nPos13 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos12+1)";
        $nPos14 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos13+1)";
        $nPos15 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos14+1)";

        $qSelSuc = "SELECT COUNT(sucidxxx) FROM $cAlfa.fpar0008 WHERE $cAlfa.fpar0008.ccoidxxx = $cAlfa.fcoc$cPerAno.ccoidxxx ";

        /**
         * select para buscar la descripcion el concepto de la F
         */
        $qConFac  = "SELECT serdespx ";
        $qConFac .= "FROM $cAlfa.fpar0129 ";
        $qConFac .= "WHERE $cAlfa.fpar0129.ctoidxxx = $cAlfa.fcod$cPerAno.ctoidxxx LIMIT 0,1 ";

        /**
         * CASE DE LOS COMPROBANTES
         */
        $qCasCom .= "CASE $cAlfa.fcod$cPerAno.comctoc2 ";
        $qCasCom .= "WHEN 'P' THEN IF($cAlfa.fpar0119.ctodesxp != \"\",$cAlfa.fpar0119.ctodesxp,\"COMPROBANTE SIN NOMBRE\") ";
        $qCasCom .= "WHEN 'G' THEN IF($cAlfa.fpar0119.ctodesxg != \"\",$cAlfa.fpar0119.ctodesxg,\"COMPROBANTE SIN NOMBRE\") ";
        $qCasCom .= "WHEN 'R' THEN IF($cAlfa.fpar0119.ctodesxr != \"\",$cAlfa.fpar0119.ctodesxr,\"COMPROBANTE SIN NOMBRE\") ";
        $qCasCom .= "WHEN 'L' THEN IF($cAlfa.fpar0119.ctodesxl != \"\",$cAlfa.fpar0119.ctodesxl,\"COMPROBANTE SIN NOMBRE\") ";
        $qCasCom .= "WHEN 'F' THEN IF($cAlfa.fpar0119.ctodesxf != \"\",$cAlfa.fpar0119.ctodesxf,IF(($qConFac) != \"\",($qConFac),\"COMPROBANTE SIN NOMBRE\")) ";
        $qCasCom .= "WHEN 'M' THEN IF($cAlfa.fpar0119.ctodesxm != \"\",$cAlfa.fpar0119.ctodesxm,\"COMPROBANTE SIN NOMBRE\") ";
        $qCasCom .= "ELSE IF($cAlfa.fpar0119.ctodesxx != \"\",$cAlfa.fpar0119.ctodesxx,\"COMPROBANTE SIN NOMBRE\") ";
        $qCasCom .= "END ";

        //nombre del cliente
        $cNomCli = "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)))";

        //Busco los PCC en fcod
        $qMovDet  = "SELECT $cAlfa.fcod$cPerAno.ctoidxxx,$cAlfa.fcod$cPerAno.pucidxxx,$cAlfa.fcod$cPerAno.teridxxx,$cAlfa.fcod$cPerAno.comfacxx, ";
        //Codigo para traer descrpcion de concepto de cobro
        $qMovDet .= "CASE $cAlfa.fcod$cPerAno.comidxxx ";
        $qMovDet .= "WHEN 'P' THEN IF($cAlfa.fpar0121.ctodesxx != \"\",$cAlfa.fpar0121.ctodesxx,IF($cAlfa.fpar0119.ctodesxp != \"\",$cAlfa.fpar0119.ctodesxp,IF($cAlfa.fcod$cPerAno.comctoc2 != \"\",($qCasCom),\"COMPROBANTE SIN NOMBRE\")))";
        $qMovDet .= "WHEN 'G' THEN IF($cAlfa.fpar0119.ctodesxg != \"\",$cAlfa.fpar0119.ctodesxg,IF($cAlfa.fcod$cPerAno.comctoc2 != \"\",($qCasCom),\"COMPROBANTE SIN NOMBRE\")) ";
        $qMovDet .= "WHEN 'R' THEN IF($cAlfa.fpar0119.ctodesxr != \"\",$cAlfa.fpar0119.ctodesxr,IF($cAlfa.fcod$cPerAno.comctoc2 != \"\",($qCasCom),\"COMPROBANTE SIN NOMBRE\")) ";
        $qMovDet .= "WHEN 'L' THEN IF($cAlfa.fpar0119.ctodesxl != \"\",$cAlfa.fpar0119.ctodesxl,IF($cAlfa.fcod$cPerAno.comctoc2 != \"\",($qCasCom),\"COMPROBANTE SIN NOMBRE\")) ";
        $qMovDet .= "WHEN 'F' THEN IF($cAlfa.fpar0119.ctodesxf != \"\",$cAlfa.fpar0119.ctodesxf,IF(($qConFac) != \"\",($qConFac),\"COMPROBANTE SIN NOMBRE\")) ";
        $qMovDet .= "WHEN 'M' THEN IF($cAlfa.fpar0119.ctodesxm != \"\",$cAlfa.fpar0119.ctodesxm,IF($cAlfa.fcod$cPerAno.comctoc2 != \"\",($qCasCom),\"COMPROBANTE SIN NOMBRE\")) ";
        $qMovDet .= "ELSE IF($cAlfa.fpar0119.ctodesxx != \"\",$cAlfa.fpar0119.ctodesxx,IF($cAlfa.fcod$cPerAno.comctoc2 != \"\",($qCasCom),\"COMPROBANTE SIN NOMBRE\")) ";
        $qMovDet .= "END AS ctodesxx, ";
        // fin de Codigo para traer descrpcion de concepto de cobro
        $qMovDet .= "$cAlfa.fcod$cPerAno.comvlrxx ";
        $qMovDet .= "FROM $cAlfa.fcod$cPerAno ";
        $qMovDet .= "LEFT JOIN $cAlfa.fpar0119 ON $cAlfa.fcod$cPerAno.ctoidxxx = $cAlfa.fpar0119.ctoidxxx AND $cAlfa.fcod$cPerAno.pucidxxx = $cAlfa.fpar0119.pucidxxx ";
        $qMovDet .= "LEFT JOIN $cAlfa.fpar0121 ON $cAlfa.fcod$cPerAno.ctoidxxx = $cAlfa.fpar0121.ctoidxxx AND $cAlfa.fcod$cPerAno.pucidxxx = $cAlfa.fpar0121.pucidxxx ";
        $qMovDet .= "WHERE ";
        $qMovDet .= "($cAlfa.fpar0119.ctoidxxx = $cAlfa.fcod$cPerAno.ctoidxxx OR $cAlfa.fpar0121.ctoidxxx = $cAlfa.fcod$cPerAno.ctoidxxx) AND ";
        $qMovDet .= "$cAlfa.fcod$cPerAno.ctoidxxx IN ($cCtoPCC) AND ";
        $qMovDet .= "$cAlfa.fcod$cPerAno.comidxxx != \"F\"  ";
        if($gCotId!=""){
          $qMovDet .= "AND $cAlfa.fcod$cPerAno.ctoidxxx = \"$gCotId\" ";
          $qMovDet .= "AND $cAlfa.fcod$cPerAno.pucidxxx = \"$gPucId\" ";
        }
        $qMovDet .= "AND $cAlfa.fcod$cPerAno.comfacxx IN ($cFacturas) ";
        $qMovDet .= "ORDER BY $cAlfa.fcod$cPerAno.ctoidxxx ";
        $xMovDet  = f_MySql("SELECT","",$qMovDet,$xConexion01,"");
        //echo $qMovDet;

        $mDatMov = array();
        $mTotMov = array();
        $nTotal = 0;

        while ($xRMD = mysql_fetch_array($xMovDet)){
          //Busco la sucursal de la factura
          $qMovCab  = "SELECT $cAlfa.fcoc$cPerAno.teridxxx, ";
          $qMovCab .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",IF($cNomCli != \"\",$cNomCli,$cAlfa.SIAI0150.CLINOMXX),\"CLIENTE SIN NOMBRE\") AS clinomxx, ";
          $qMovCab .= "IF(($qSelSuc)= 1, $cAlfa.fpar0008.sucidxxx,SUBSTRING($cAlfa.fcoc$cPerAno.comfpxxx,$nPos15+1,3)) AS sucidxxx ";
          $qMovCab .= "FROM $cAlfa.fcoc$cPerAno ";
          $qMovCab .= "LEFT JOIN $cAlfa.fpar0008 ON $cAlfa.fcoc$cPerAno.ccoidxxx = $cAlfa.fpar0008.ccoidxxx ";
          $qMovCab .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcoc$cPerAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
          $qMovCab .= "WHERE ";
          $qMovCab .= "CONCAT($cAlfa.fcoc$cPerAno.comidxxx,'-',$cAlfa.fcoc$cPerAno.comcodxx,'-',$cAlfa.fcoc$cPerAno.comcscxx,'-',$cAlfa.fcoc$cPerAno.comcscxx) = \"{$xRMD['comfacxx']}\" AND ";
          $qMovCab .= "$cAlfa.fcoc$cPerAno.regestxx = \"ACTIVO\" LIMIT 0,1";

          $xMovCab  = f_MySql("SELECT","",$qMovCab,$xConexion01,"");
          $xRMC = mysql_fetch_array($xMovCab);

          $mDatMov[$xRMC['teridxxx']][$xRMD['ctoidxxx']."~".$xRMD['pucidxxx']]['clinomxx'] = ($xRMC['clinomxx']!="")?$xRMC['clinomxx']:"&nbsp;";
          $mDatMov[$xRMC['teridxxx']][$xRMD['ctoidxxx']."~".$xRMD['pucidxxx']]['teridxxx'] = ($xRMC['teridxxx']!="")?$xRMC['teridxxx']:"&nbsp;";
          $mDatMov[$xRMC['teridxxx']][$xRMD['ctoidxxx']."~".$xRMD['pucidxxx']]['ctoidxxx'] = ($xRMD['ctoidxxx']!="")?$xRMD['ctoidxxx']:"&nbsp;";
          $mDatMov[$xRMC['teridxxx']][$xRMD['ctoidxxx']."~".$xRMD['pucidxxx']]['ctodesxx'] = ($xRMD['ctodesxx']!="")?$xRMD['ctodesxx']:"&nbsp;";
          //sumando el comvlrxx del cliente
          $mDatMov[$xRMC['teridxxx']][$xRMD['ctoidxxx']."~".$xRMD['pucidxxx']]['comvlrto'] += ($xRMD['comvlrxx']!="")?$xRMD['comvlrxx']:"0";
          //total cliente por sucursal
          $mDatMov[$xRMC['teridxxx']][$xRMD['ctoidxxx']."~".$xRMD['pucidxxx']][$xRMC['sucidxxx']]['sucidxxx'] = $xRMC['sucidxxx'];
          $mDatMov[$xRMC['teridxxx']][$xRMD['ctoidxxx']."~".$xRMD['pucidxxx']][$xRMC['sucidxxx']]['comvlrxx'] += ($xRMD['comvlrxx']!="")?$xRMD['comvlrxx']:"0";
          //total sucursal
          $mTotMov[$xRMC['sucidxxx']]['comvlrxx'] += ($xRMD['comvlrxx']!="")?$xRMD['comvlrxx']:"0";
          $nTotal += ($xRMD['comvlrxx']!="")?$xRMD['comvlrxx']:"0";
        }
      }

      if($gTerId!=""){
        //Busco en la base de datos el nombre del cliente
        $qDatExt  = "SELECT IF($cAlfa.SIAI0150.CLINOMXX != \"\",IF($cNomCli != \"\",$cNomCli,$cAlfa.SIAI0150.CLINOMXX),\"CLIENTE SIN NOMBRE\") AS clinomxx ";
        $qDatExt .= "FROM $cAlfa.SIAI0150 ";
        $qDatExt .= "WHERE ";
        $qDatExt .= "CLIIDXXX = \"$gTerId\" AND ";
        $qDatExt .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
        $xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
        $xRDE = mysql_fetch_array($xDatExt);
        //f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));
      }

      if($gCotId!=""){
        //Busco el nombre del concepto contable

        $qConCob  = "SELECT IF(ctodesxx != \"\",ctodesxx,IF(ctodesxp != \"\",ctodesxp,IF(ctodesxg != \"\",ctodesxg,IF(ctodesxr != \"\",ctodesxr,IF(ctodesxl != \"\",ctodesxl,IF(ctodesxf != \"\",ctodesxf,IF(ctodesxm != \"\",ctodesxm,\"CONCEPTO SIN DESCRIPCION\"))))))) AS ctodesxx ";
        $qConCob .= "FROM $cAlfa.fpar0119 ";
        $qConCob .= "WHERE ";
        $qConCob .= "ctoidxxx = \"$gCotId\" AND ";
        $qConCob .= "pucidxxx = \"$gPucId\" AND ";
        $qConCob .= "regestxx = \"ACTIVO\" LIMIT 0,1";
        $xConCob = f_MySql("SELECT","",$qConCob,$xConexion01,"");
        $xRCC = mysql_fetch_array($xConCob);
        //f_Mensaje(__FILE__,__LINE__,$qConCob." ~ ".mysql_num_rows($xConCob));
      }

      if($gDirId!=""){
        //Busco en la base de datos el nombre del director
        $qSqlUsr  = "SELECT USRNOMXX ";
        $qSqlUsr .= "FROM $cAlfa.SIAI0003 ";
        $qSqlUsr .= "WHERE ";
        $qSqlUsr .= "USRIDXXX = \"$gDirId\" AND ";
        $qSqlUsr .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
        $xSqlUsr = f_MySql("SELECT","",$qSqlUsr,$xConexion01,"");
        $xRU = mysql_fetch_array($xSqlUsr);
      }

      if($gTerId==""){
        $nCol = 6;
      }else{
        $nCol = 4;
      }

      switch ($cTipo) {
        case 1:
          // PINTA POR PANTALLA//
          ?>
              <form name = 'frgrm' action='frinpgrf.php' method="POST">
                <center>
                  <table border="1" cellspacing="0" cellpadding="0" width="2300" align=center style="margin:5px">
                    <tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">
                      <?php
                      switch($cAlfa){
                        case "TEADIMPEXX":
                        case "DEADIMPEXX":
                        case "ADIMPEXX":
                        // case "DEGRUPOGLA":
                        // case "TEGRUPOGLA":
                          ?>
                          <td class = "name">
                            <img width="100" height="90" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoAdimpex.jpg">
                          </td>
                          <?php
                          $nColRes = 1;
                        break;

                        case "ROLDANLO"://ROLDAN
                        case "TEROLDANLO"://ROLDAN
                        case "DEROLDANLO"://ROLDAN
                          ?>
                            <td class="name" width="150"><center>
                              <img width="150" height="90" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoroldan.png">
                            </td>

                          <?php
                          $nColRes = 1;
                        break;
                        case "GRUMALCO"://GRUMALCO
                        case "TEGRUMALCO"://GRUMALCO
                        case "DEGRUMALCO"://GRUMALCO
                          ?>
                            <td class="name" width="150"><center>
                              <img width="120" height="70" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logomalco.jpg">
                            </td>
                          <?php
                          $nColRes = 1;
                        break;
                          case "ALADUANA": //ALADUANA
                          case "TEALADUANA": //ALADUANA
                          case "DEALADUANA": //ALADUANA
                          ?>
                            <td class="name" width="150"><center>
                              <img width="150" height="90" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoaladuana.jpg">
                            </td>
                          <?php
                          $nColRes = 1;
                        break;
                        case "ANDINOSX": //ANDINOSX
                        case "TEANDINOSX": //ANDINOSX
                        case "DEANDINOSX": //ANDINOSX
                          ?>
                            <td class="name" width="150"><center>
                              <img width="70" height="60" style="left: 30px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoAndinos2.jpeg">
                            </td>
                          <?php
                        $nColRes = 1;
                        break;
                        case "GRUPOALC": //GRUPOALC
                        case "TEGRUPOALC": //GRUPOALC
                        case "DEGRUPOALC": //GRUPOALC
                          ?>
                            <td class="name" width="150"><center>
                              <img width="150" height="70" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoalc.jpg">
                            </td>
                          <?php
                        $nColRes = 1;
                        break;
                        case "AAINTERX": //AAINTERX
                        case "TEAAINTERX": //AAINTERX
                        case "DEAAINTERX": //AAINTERX
                          ?>
                            <td class="name" width="150"><center>
                              <img width="150" height="90" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logointernacional.jpg">
                            </td>
                          <?php
                          $nColRes = 1;
                        break;
                        case "AALOPEZX":
                        case "TEAALOPEZX":
                        case "DEAALOPEZX":
                          ?>
                            <td class="name" width="150"><center>
                              <img width="140" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoaalopez.png">
                            </td>
                          <?php
                        $nColRes = 1;
                        break;
                        case "ADUAMARX": //ADUAMARX
                        case "TEADUAMARX": //ADUAMARX
                        case "DEADUAMARX": //ADUAMARX
                          ?>
                            <td class="name" width="150"><center>
                              <img width="100" height="100" style="left: 15px;margin-top:2px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoaduamar.jpg">
                            </td>
                          <?php
                        $nColRes = 1;
                        break;
                        case "SOLUCION": //SOLUCION
                        case "TESOLUCION": //SOLUCION
                        case "DESOLUCION": //SOLUCION
                          ?>
                            <td class="name" width="150"><center>
                              <img width="100" style="left: 15px;margin-top:2px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logosoluciones.jpg">
                            </td>
                          <?php
                        $nColRes = 1;
												break;
												case "FENIXSAS": //FENIXSAS
												case "TEFENIXSAS": //FENIXSAS
												case "DEFENIXSAS": //FENIXSAS
													?>
														<td class="name" width="150"><center>
															<img width="130" height="50" style="left: 15px;margin-top:2px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logofenix.jpg">
														</td>
													<?php
												$nColRes = 1;
												break;
												case "COLVANXX": //COLVANXX
												case "TECOLVANXX": //COLVANXX
												case "DECOLVANXX": //COLVANXX
													?>
														<td class="name" width="150"><center>
															<img width="130" height="50" style="left: 15px;margin-top:2px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logocolvan.jpg">
														</td>
													<?php
												$nColRes = 1;
												break;
												case "INTERLAC": //INTERLAC
												case "TEINTERLAC": //INTERLAC
												case "DEINTERLAC": //INTERLAC
													?>
														<td class="name" width="150"><center>
															<img width="130" height="50" style="left: 15px;margin-top:2px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logointerlace.jpg">
														</td>
													<?php
												$nColRes = 1;
												break;
												case "DHLEXPRE": //DHLEXPRE
												case "TEDHLEXPRE": //DHLEXPRE
												case "DEDHLEXPRE": //DHLEXPRE
													?>
														<td class="name" width="150"><center>
															<img width="140" height="80" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logo_dhl_express.jpg">
														</td>
													<?php
													$nColRes = 1;
												break;
                        case "KARGORUX": //KARGORUX
                        case "TEKARGORUX": //KARGORUX
                        case "DEKARGORUX": //KARGORUX
                          ?>
                            <td class="name" width="150"><center>
                              <img width="120" height="60" style="left: 15px;margin-top:5px;margin-bottom:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logokargoru.jpg">
                            </td>
                          <?php
                          $nColRes = 1;
                        break;
                        case "ALOGISAS": //LOGISTICA
                        case "TEALOGISAS": //LOGISTICA
                        case "DEALOGISAS": //LOGISTICA
                          ?>
                            <td class="name" width="150"><center>
                              <img width="140" style="left: 15px;margin-top:5px;margin-bottom:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logologisticasas.jpg">
                            </td>
                          <?php
                          $nColRes = 1;
                        break;
                        case "PROSERCO": //PROSERCO
                        case "TEPROSERCO": //PROSERCO
                        case "DEPROSERCO": //PROSERCO
                          ?>
                            <td class="name" width="150"><center>
                              <img width="140" style="left: 15px;margin-top:5px;margin-bottom:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logoproserco.png">
                            </td>
                          <?php
                          $nColRes = 1;
                        break; 
                        case "MANATIAL": //MANATIAL
                        case "TEMANATIAL": //MANATIAL
                        case "DEMANATIAL": //MANATIAL
                          ?>
                            <td class="name" width="150"><center>
                              <img width="140" style="left: 15px;margin-top:5px;margin-bottom:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logomanantial.jpg">
                            </td>
                          <?php
                          $nColRes = 1;
                        break;
                        case "DSVSASXX":  //DSVSAS
                        case "DEDSVSASXX": //DSVSAS
                        case "TEDSVSASXX": //DSVSAS
                          ?>
                            <td class="name" width="150"><center>
                              <img width="140" style="left: 15px;margin-top:5px;margin-bottom:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logodsv.jpg">
                            </td>
                          <?php
                          $nColRes = 1;
                        break;
                        case "MELYAKXX":    //MELYAK
                        case "DEMELYAKXX":  //MELYAK
                        case "TEMELYAKXX":  //MELYAK
                          ?>
                            <td class="name" width="150"><center>
                              <img width="140" style="left: 15px;margin-top:5px;margin-bottom:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logomelyak.jpg">
                            </td>
                          <?php
                          $nColRes = 1;
                        break;
                        case "FEDEXEXP":    //FEDEX
                        case "DEFEDEXEXP":  //FEDEX
                        case "TEFEDEXEXP":  //FEDEX
                          ?>
                            <td class="name" width="150"><center>
                              <img width="140" style="left: 15px;margin-top:5px;margin-bottom:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logofedexexp.jpg">
                            </td>
                          <?php
                          $nColRes = 1;
                        break;
                        case "EXPORCOM":    //EXPORCOMEX
                        case "DEEXPORCOM":  //EXPORCOMEX
                        case "TEEXPORCOM":  //EXPORCOMEX
                          ?>
                            <td class="name" width="150"><center>
                              <img width="140" style="left: 15px;margin-top:5px;margin-bottom:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logoexporcomex.jpg">
                            </td>
                          <?php
                          $nColRes = 1;
                        break;
												case "HAYDEARX":   //HAYDEARX
												case "DEHAYDEARX": //HAYDEARX
                        case "TEHAYDEARX": //HAYDEARX
                          ?>
                            <td class="name" width="150"><center>
                              <img width="140" height="70" style="left: 15px;margin-top:5px;margin-bottom:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logohaydear.jpeg">
                            </td>
                          <?php
                          $nColRes = 1;
                        break;
												case "CONNECTA":   //CONNECTA
												case "DECONNECTA": //CONNECTA
                        case "TECONNECTA": //CONNECTA
                          ?>
                            <td class="name" width="150"><center>
                              <img width="120" height="85" style="left: 15px;margin-top:5px;margin-bottom:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logoconnecta.jpg">
                            </td>
                          <?php
                          $nColRes = 1;
                        break;
                        case "CONLOGIC":   //CONLOGIC
                        case "DECONLOGIC": //CONLOGIC
                        case "TECONLOGIC": //CONLOGIC
                          ?>
                            <td class="name" width="150"><center>
                              <img width="120" height="85" style="left: 15px;margin-top:5px;margin-bottom:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logoconlogic.jpg">
                            </td>
                          <?php
                          $nColRes = 1;
                        break;
												case "OPENEBCO":   //OPENEBCO
												case "DEOPENEBCO": //OPENEBCO
                        case "TEOPENEBCO": //OPENEBCO
                          ?>
                            <td class="name" width="150"><center>
                              <img width="200" height="85" style="left: 15px;margin-top:5px;margin-bottom:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/opentecnologia.JPG">
                            </td>
                          <?php
                          $nColRes = 1;
                        break;
                        default:
                          $nColRes = 0;
                        break;
                      }
                      ?>
                      <td class="name" colspan="<?php echo count($mDatSuc)+$nCol-$nColRes ?>" align="left" >
                        <font size="3">
                          <b>REPORTE DE CONCEPTO DE PAGOS A TERCEROS<BR>
                          PERIODO: <?php echo " ".$gPeriodo."-".$gMes ?><br>
                          <?php if($gTerId!=""){ ?>
                          CLIENTE: <?php echo "[".$gTerId."] ".$xRDE['clinomxx'] ?><br>
                          <?php } ?>
                          <?php if($gCotId!=""){ ?>
                          CONCEPTO: <?php echo "[".$gCotId."] ".$xRCC['ctodesxx'] ?><br>
                          <?php } ?>

                          <?php if($gDirId!=""){ ?>
                          DIRECTOR: <?php echo "[".$gDirId."] ".$xRU['USRNOMXX'] ?><br>
                          <?php } ?>
                          </b>
                        </font>
                      </td>
                    </tr>
                    <tr height="20">
                      <?php if($gTerId==""){ ?>
                        <td style="background-color:#0B610B" class="letra8" width="100px" align="center"><b><font color=white>NIT</font></b></td>
                        <td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>CLIENTE</font></b></td>
                      <?php } ?>
                        <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>ID CONCEPTO</font></b></td>
                        <td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>CONCEPTO</font></b></td>
                      <?php  foreach ($mDatSuc as $cKey => $cValue) { ?>
                        <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white><?php echo $cValue ?></font></b></td>
                      <?php } ?>
                      <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>TOTAL</font></b></td>
                    </tr>
                    <?php foreach ($mDatMov as $cKey => $cValue) {
                      foreach ($cValue as $cKey2 => $cValue2) {
                        $zColorPro = "#000000";
                        $cColor = "#FFFFFF";
                        ?>
                        <tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">
                          <?php if($gTerId==""){ ?>
                            <td class="letra7" align="left" style = "color:<?php echo $zColorPro ?>"><?php echo $mDatMov[$cKey][$cKey2]['teridxxx'] ?></td>
                            <td class="letra7" align="left" style = "color:<?php echo $zColorPro ?>"><?php echo $mDatMov[$cKey][$cKey2]['clinomxx'] ?></td>
                          <?php } ?>
                            <td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo $mDatMov[$cKey][$cKey2]['ctoidxxx'] ?></td>
                            <td class="letra7" align="left" style = "color:<?php echo $zColorPro ?>"><?php echo $mDatMov[$cKey][$cKey2]['ctodesxx'] ?></td>
                          <?php  foreach ($mDatSuc as $cKeySuc => $cValueSuc) {
                            if($cColor == "#FFFFFF"){
                              $cColor = "#F2F2F2";
                            }else{
                              $cColor = "#FFFFFF";
                            }
                          ?>
                            <td class="letra7" align="right" style = "background-color:<?php echo $cColor ?>;color:<?php echo $zColorPro ?>"><?php  echo (number_format($mDatMov[$cKey][$cKey2][$cKeySuc]['comvlrxx'],0,',','.')!="")?number_format($mDatMov[$cKey][$cKey2][$cKeySuc]['comvlrxx'],0,',','.'):"&nbsp;" ?></td>
                          <?php } ?>
                          <td class="letra7" align="right"  style = "background-color:#E3F6CE;color:<?php echo $zColorPro ?>"><?php echo (number_format($mDatMov[$cKey][$cKey2]['comvlrto'],0,',','.')!="")?number_format($mDatMov[$cKey][$cKey2]['comvlrto'],0,',','.'):"&nbsp;" ?></td>
                        </tr>
                      <?php }
                    } ?>
                    <tr style="background-color:#0B610B" height="20" style="padding-left:4px;padding-right:4px">
                      <td class="letra8" align="right" colspan="<?php echo ($nCol-2) ?>"><b><font color=white>TOTALES</font></b></td>
                      <?php  foreach ($mDatSuc as $cKey => $cValue) { ?>
                        <td class="letra8" align="right" width="100px"><b><font color=white><?php echo (number_format($mTotMov[$cKey]['comvlrxx'],0,',','.')!="")?number_format($mTotMov[$cKey]['comvlrxx'],0,',','.'):0 ?></font></b></td>
                      <?php
                    } ?>
                      <td class="letra8" align="right" width="100px"><b><font color=white><?php echo (number_format($nTotal,0,',','.')!="")?number_format($nTotal,0,',','.'):0 ?></font></b></td>
                    </tr>
                  </table>
                </center>
              </form>
              <script type="text/javascript">document.getElementById('loading').style.display="none";</script>
            </body>
          </html>
        <?php
        break;
        case 2:
          // PINTA POR EXCEL //
          $data = '';
          
          $cNomFile = "REPORTE_DE_CONCEPTO_DE_PAGOS_A_TERCEROS_".$_COOKIE['kUsrId'].date("YmdHis").".xls";

          if ($_SERVER["SERVER_PORT"] != "") {
            $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;
          } else {
            $cFile = "{$OPENINIT['pathdr']}/opencomex/" . $vSysStr['system_download_directory'] . "/" . $cNomFile;
          }

          $fOp = fopen($cFile, 'a');

          $nCol01 = count($mDatSuc)+$nCol-1;
          $ncol02 = ($nCol-2);
          $data .='<table border="1" cellspacing="0" cellpadding="0" width="2300" align=center style="margin:5px">';
            $data .='<tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">';
              $data .='<td class="name" colspan="'.$nCol01.'" align="left">';
                $data .='<font size="3">';
                $data .='<b>REPORTE DE CONCEPTO DE PAGOS A TERCEROS<BR>';
                $data .='PERIODO: '." ".$gPeriodo."-".$gMes.'<br>';
                if($gTerId!=""){
                  $data .='CLIENTE: '."[".$gTerId."] ".$xRDE['clinomxx'].'<br>';
                }
                if($gCotId!=""){
                  $data .='CONCEPTO: '."[".$gCotId."] ".$xRCC['ctodesxx'].'<br>';
                }
                if($gDirId!=""){
                  $data .='DIRECTOR: '."[".$gDirId."] ".$xRU['USRNOMXX'].'<br>';
                }
                $data .='</b>';
                $data .='</font>';
              $data .='</td>';
            $data .='</tr>  ';
            $data .='<tr height="20">';
              if($gTerId==""){
                $data .='<td style="background-color:#0B610B" class="letra8" width="100px" align="center"><b><font color=white>NIT</font></b></td>';
                $data .='<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>CLIENTE</font></b></td>';
              }
              $data .='<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>ID CONCEPTO</font></b></td>';
              $data .='<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>CONCEPTO</font></b></td>';
              foreach ($mDatSuc as $cKey => $cValue) {
                $data .='<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>'.$cValue.'</font></b></td>';
              }
              $data .='<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>TOTAL</font></b></td>';
            $data .='</tr>';
            foreach ($mDatMov as $cKey => $cValue) {
              foreach ($cValue as $cKey2 => $cValue2) {
                $zColorPro = "#000000";
                $cColor = "#FFFFFF";
                $data .='<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
                  if($gTerId==""){
                    $data .='<td class="letra7" align="left" style = "color:'.$zColorPro.'">'.$mDatMov[$cKey][$cKey2]['teridxxx'].'</td>';
                    $data .='<td class="letra7" align="left" style = "color:'.$zColorPro.'">'.$mDatMov[$cKey][$cKey2]['clinomxx'].'</td>';
                  }
                  $data .='<td class="letra7" align="center" style = "color:'.$zColorPro.'">'.$mDatMov[$cKey][$cKey2]['ctoidxxx'].'</td>';
                  $data .='<td class="letra7" align="left" style = "color:'.$zColorPro.'">'.$mDatMov[$cKey][$cKey2]['ctodesxx'].'</td>';
                  foreach ($mDatSuc as $cKeySuc => $cValueSuc) {
                    if($cColor == "#FFFFFF"){
                      $cColor = "#F2F2F2";
                    }else{
                      $cColor = "#FFFFFF";
                    }
                    $nValor01 = (number_format($mDatMov[$cKey][$cKey2][$cKeySuc]['comvlrxx'],0,',','')!="")?number_format($mDatMov[$cKey][$cKey2][$cKeySuc]['comvlrxx'],0,',',''):"&nbsp;";
                    $data .='<td class="letra7" align="right" style = "background-color:'.$cColor.';color:'.$zColorPro.'">'.$nValor01.'</td>';
                  }
                  $nValor01 = (number_format($mDatMov[$cKey][$cKey2]['comvlrto'],0,',','')!="")?number_format($mDatMov[$cKey][$cKey2]['comvlrto'],0,',',''):"&nbsp;";
                  $data .='<td class="letra7" align="right"  style = "background-color:#E3F6CE;color:'.$zColorPro.'">'.$nValor01.'</td>';
                $data .='</tr>';
              }
            }
            $data .='<tr style="background-color:#0B610B" height="20" style="padding-left:4px;padding-right:4px">';
              $data .='<td class="letra8" align="right" colspan="'.$ncol02.'"><b><font color=white>TOTALES</font></b></td>';
              foreach ($mDatSuc as $cKey => $cValue) {
                $nValor01 = (number_format($mTotMov[$cKey]['comvlrxx'],0,',','')!="")?number_format($mTotMov[$cKey]['comvlrxx'],0,',',''):0;
                $data .='<td class="letra8" align="right" width="100px"><b><font color=white>'.$nValor01.'</font></b></td>';
              }
              $nValor01 = (number_format($nTotal,0,',','')!="")?number_format($nTotal,0,',',''):0;
              $data .='<td class="letra8" align="right" width="100px"><b><font color=white>'.$nValor01.'</font></b></td>';
            $data .='</tr>';
          $data .='</table>';

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
        break;
      }//Fin Switch
    }//if ($nSwitch == 0) {
  }//if ($cEjePro == 0) {
?>

<?php
  if ($_SERVER["SERVER_PORT"] == "") {
    /**
     * Se ejecuto por el proceso en background
     * Actualizo el campo de resultado y nombre del archivo
     */
    $vParBg['pbarespr'] = ($nSwitch == 0) ? "EXITOSO" : "FALLIDO";  //Resultado Proceso
    $vParBg['pbaexcxx'] = ((count($mDatMov) > 0) ? $cNomFile : "");  //Nombre Archivos Excel
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

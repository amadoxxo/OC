<?php
  /**
   * Imprime Vista Previa Factura de Venta ANDINOS.
   * --- Descripcion: Permite Imprimir Vista Previa de la Factura de Venta.
   * @author Hair Zabala <hair.zabala@opentecnologia.com.co>
   */

  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");

  include("../../../../libs/php/utility.php");

  $cRoot = $_SERVER['DOCUMENT_ROOT'];
  $cEstiloLetra = 'arial';

  define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
  require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');

  $mPrn = explode("|",$prints);
  for ($nn=0;$nn<count($mPrn);$nn++) {
    if (strlen($mPrn[$nn]) > 0) {
      $vComp = explode("~",$mPrn[$nn]);
      $cComId   = $vComp[0];
      $cComCod  = $vComp[1];
      $cComCsc  = $vComp[3];
      $cComCsc2 = $vComp[3];
      $cRegFCre = $vComp[4];
      $cNewYear = substr($cRegFCre,0,4);
      //$cAno     = substr($cRegFCre,0,4);
    }
  }

  //Busco los comprobantes donde El tipo de Comprobante sea RCM
  $qFpar117  = "SELECT comidxxx, comcodxx ";
  $qFpar117 .= "FROM $cAlfa.fpar0117 ";
  $qFpar117 .= "WHERE ";
  $qFpar117 .= "comtipxx  = \"RCM\" ";
  $xFpar117  = f_MySql("SELECT","",$qFpar117,$xConexion01,"");
  //f_Mensaje(__FILE__,__LINE__,$qFpar117." ~ ".mysql_num_rows($xFpar117));
  $mRCM = array();
  while ($xRF117 = mysql_fetch_array($xFpar117)) {
    $mRCM[count($mRCM)] = "{$xRF117['comidxxx']}~{$xRF117['comcodxx']}";
  }

  # Reconstruyendo POST de DOS, pagos a terceros e ingresos propios
  $qDatDo  = "SELECT * ";
  $qDatDo .= "FROM $cAlfa.$cTabla_DOS ";
  $qDatDo .= "WHERE ";
  $qDatDo .= "cUsrId_DOS = \"{$_COOKIE['kUsrId']}\" AND ";
  $qDatDo .= "cFacId_DOS = \"{$_POST['cFacId']}\" ";
  $qDatDo .= "ORDER BY ABS(cSeq_DOS) ";
  $xDatDo  = f_MySql("SELECT","",$qDatDo,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qDatDo."~".mysql_num_rows($xDatDo));
  $vCampos = array();
  while($xRC = mysql_fetch_field($xDatDo)){
    $vCampos[] = $xRC->name;
  }

  # Armando Matriz de DOs
  $nSecuencia = 1;
  while ($xRDD = mysql_fetch_assoc($xDatDo)) {
    for($n=0; $n<count($vCampos); $n++) {
      $_POST[$vCampos[$n].$nSecuencia] = $xRDD[$vCampos[$n]];
    }
    $nSecuencia++;
  }

  $qPCCA  = "SELECT * ";
  $qPCCA .= "FROM $cAlfa.$cTabla_PCCA ";
  $qPCCA .= "WHERE ";
  $qPCCA .= "cUsrId_PCCA = \"{$_COOKIE['kUsrId']}\" AND ";
  $qPCCA .= "cFacId_PCCA = \"{$_POST['cFacId']}\" ";
  $qPCCA .= "ORDER BY ABS(cComSeq_PCCA) ";
  $xPCCA  = f_MySql("SELECT","",$qPCCA,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qPCCA."~".mysql_num_rows($xPCCA));
  $vCampos = array();
  while($xRC = mysql_fetch_field($xPCCA)){
    $vCampos[] = $xRC->name;
  }
  # Armando Matriz de DOs
  $nSecuencia = 1;
  while ($xRP = mysql_fetch_array($xPCCA)) {
    for($n=0; $n<count($vCampos); $n++) {
      $_POST[$vCampos[$n].$nSecuencia] = $xRP[$vCampos[$n]];
    }
    $nSecuencia++;
  }

  $qIPA  = "SELECT * ";
  $qIPA .= "FROM $cAlfa.$cTabla_IPA ";
  $qIPA .= "WHERE ";
  $qIPA .= "cUsrId_IPA = \"{$_COOKIE['kUsrId']}\" AND ";
  $qIPA .= "cFacId_IPA = \"{$_POST['cFacId']}\" ";
  $qIPA .= "ORDER BY ABS(cComSeq_IPA) ";
  $xIPA  = f_MySql("SELECT","",$qIPA,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qIPA."~".mysql_num_rows($xIPA));
  $vCampos = array();
  while($xRC = mysql_fetch_field($xIPA)){
    $vCampos[] = $xRC->name;
  }
  # Armando Matriz de DOs
  $nSecuencia = 1;
  while ($xRI = mysql_fetch_array($xIPA)) {
    for($n=0; $n<count($vCampos); $n++) {
      $_POST[$vCampos[$n].$nSecuencia] = $xRI[$vCampos[$n]];
    }
    $nSecuencia++;
  }

  /*** Consulto en que bloque se deben imprimir los anticipos PCC o IP ***/
  if( $vCccDat['ccccdant'] == "PCC" ){
    $nAnticipo = 1; // anticipos en pagos a terceros
  }else if($vCccDat['ccccdant'] == "AMBOS" || $vCccDat['ccccdant'] == ""){
    $nAnticipo = 2; // anticipos en ingresos propios
  }

  ## Traigo el CLINOMXX o Razon Social de la Agencia de Aduana Dietrich Para encabezado de factura ##
  $qAgeDat  = "SELECT ";
  $qAgeDat .= "$cAlfa.SIAI0150.CLIIDXXX, ";
  $qAgeDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX, ";
  $qAgeDat .= "$cAlfa.SIAI0150.CLIDIRXX, ";
  $qAgeDat .= "$cAlfa.SIAI0150.CLITELXX, ";
  $qAgeDat .= "$cAlfa.SIAI0150.CLIFAXXX ";
  $qAgeDat .= "FROM $cAlfa.SIAI0150 ";
  $qAgeDat .= "WHERE ";
  $qAgeDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$vSysStr['financiero_nit_agencia_aduanas']}\" AND ";
  $qAgeDat .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
  $xAgeDat  = f_MySql("SELECT","",$qAgeDat,$xConexion01,"");
  $vAgeDat  = mysql_fetch_array($xAgeDat);
  ## Fin Traigo el CLINOMXX o Razon Social de la Agencia de Aduana Dietrich Para encabezado de factura ##

  ##Traigo Datos de la Resolucion ##
  $qResDat  = "SELECT * ";
  $qResDat .= "FROM $cAlfa.fpar0138 ";
  $qResDat .= "WHERE ";
  $qResDat .= "rescomxx LIKE \"%{$cComId}~{$cComCod}%\" AND ";
  $qResDat .= "regestxx = \"ACTIVO\" LIMIT 0,1";
  $xResDat  = f_MySql("SELECT","",$qResDat,$xConexion01,"");
  $nFilRes  = mysql_num_rows($xResDat);
  if ($nFilRes > 0) {
    $vResDat = mysql_fetch_array($xResDat);
  }
  ##Fin Traigo Datos de la Resolucion ##

  ##Busco Ciudad por donde se esta facturando ##
  /*$qCcoDat  = "SELECT * ";
  $qCcoDat .= "FROM $cAlfa.fpar0008 ";
  $qCcoDat .= "WHERE ";
  $qCcoDat .= "$cAlfa.fpar0008.sucidxxx = \"{$_POST['cSucId']}\" AND ";
  $qCcoDat .= "$cAlfa.fpar0008.regestxx = \"ACTIVO\" LIMIT 0,1 ";
  $xCcoDat  = f_MySql("SELECT","",$qCcoDat,$xConexion01,"");
  $nFilCco  = mysql_num_rows($xResDat);
  if ($nFilCco > 0) {
    $vCcoDat = mysql_fetch_array($xCcoDat);
  }*/
  ##Fin Busco Ciudad por donde se esta facturando ##

  ##Consulto en la SIAI0150 Datos del Facturado A: ##
  $qCliDat  = "SELECT ";
  $qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX, ";
  $qCliDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX, ";
  $qCliDat .= "$cAlfa.SIAI0150.CLIDIRXX, ";
  $qCliDat .= "$cAlfa.SIAI0150.CLITELXX, ";
  $qCliDat .= "$cAlfa.SIAI0150.CLIFAXXX, ";
  $qCliDat .= "$cAlfa.SIAI0150.CLINRPXX, ";
  $qCliDat .= "$cAlfa.SIAI0150.PAIIDXXX, ";
  $qCliDat .= "$cAlfa.SIAI0150.DEPIDXXX, ";
  $qCliDat .= "$cAlfa.SIAI0150.CIUIDXXX, ";
  $qCliDat .= "$cAlfa.SIAI0150.CLICONTX, ";
  $qCliDat .= "$cAlfa.SIAI0150.CLIPLAXX  ";
  $qCliDat .= "FROM $cAlfa.SIAI0150 ";
  $qCliDat .= "WHERE ";
  $qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$_POST['cTerIdInt']}\" AND ";
  $qCliDat .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
  $xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
  //f_Mensaje(__FILE__,__LINE__,$qCliDat);
  $nFilCli  = mysql_num_rows($xCliDat);
  if ($nFilCli > 0) {
    $vCliDat = mysql_fetch_array($xCliDat);
  }
  ##Consulto en la SIAI0150 Datos del Facturado A: ##

  ##Traigo Pais del Facturado A ##
  $qPaiDat  = "SELECT PAIDESXX ";
  $qPaiDat .= "FROM $cAlfa.SIAI0052 ";
  $qPaiDat .= "WHERE ";
  $qPaiDat .= "$cAlfa.SIAI0052.PAIIDXXX = \"{$vCliDat['PAIIDXXX']}\" AND ";
  $qPaiDat .= "$cAlfa.SIAI0052.REGESTXX = \"ACTIVO\" ";
  //f_Mensaje(__FILE__,__LINE__,$qPaiDat);
  $xPaiDat  = f_MySql("SELECT","",$qPaiDat,$xConexion01,"");
  $nFilCiu  = mysql_num_rows($xPaiDat);
  if ($nFilCiu > 0) {
    $vPaiDat = mysql_fetch_array($xPaiDat);
  }
  ##Fin Traigo Pais del Facturado A ##

  ##Traigo Ciudad del Facturado A ##
  $qCiuDat  = "SELECT * ";
  $qCiuDat .= "FROM $cAlfa.SIAI0055 ";
  $qCiuDat .= "WHERE ";
  $qCiuDat .= "$cAlfa.SIAI0055.PAIIDXXX = \"{$vCliDat['PAIIDXXX']}\" AND ";
  $qCiuDat .= "$cAlfa.SIAI0055.DEPIDXXX = \"{$vCliDat['DEPIDXXX']}\" AND ";
  $qCiuDat .= "$cAlfa.SIAI0055.CIUIDXXX = \"{$vCliDat['CIUIDXXX']}\" AND ";
  $qCiuDat .= "$cAlfa.SIAI0055.REGESTXX = \"ACTIVO\" ";
  //f_Mensaje(__FILE__,__LINE__,$qCiuDat);
  $xCiuDat  = f_MySql("SELECT","",$qCiuDat,$xConexion01,"");
  $nFilCiu  = mysql_num_rows($xCiuDat);
  if ($nFilCiu > 0) {
    $vCiuDat = mysql_fetch_array($xCiuDat);
  }
  ##Fin Traigo Ciudad del Facturado A ##

  ##Traigo Datos de Contacto del Facturado a ##
  if($vCliDat['CLICONTX'] <> ""){
    $vContactos = explode("~",$vCliDat['CLICONTX']);
    //f_Mensaje(__FILE__,__LINE__,count($vContactos));
    if(count($vContactos) > 1){
      $vIdContacto = $vContactos[1];
    }else{
      $vIdContacto = $vCliDat['CLICONTX'];
    }
  }//if($vCocDat['CLICONTX'] <> ""){

  $qConDat  = "SELECT ";
  $qConDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)))) AS NOMBRE ";
  $qConDat .= "FROM $cAlfa.SIAI0150 ";
  $qConDat .= "WHERE ";
  $qConDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"$vIdContacto\" AND ";
  $qConDat .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" ";
  //f_Mensaje(__FILE__,__LINE__,$vCliDat['CLICONTX']);
  $xConDat  = f_MySql("SELECT","",$qConDat,$xConexion01,"");
  $nFilCon  = mysql_num_rows($xConDat);
  if ($nFilCon > 0) {
    $vConDat = mysql_fetch_array($xConDat);
  }
  ##Fin Traigo Datos de Contacto del Facturado a ##

  ##Traigo Primer Do para traer primer Do y traer Datos de Tasa de Cambio, Documento de Transporte, Bultos, Peso ##
  $cSucId = ""; $cDocId  = ""; $cDocSuf = "";
  $dFecMay = date("Y"); //Fecha
  for ($i=0;$i<$_POST['nSecuencia_Dos'];$i++) {
    if($i == 0) {
      $cDocId   = $_POST['cDosNro_DOS'.($i+1)];
      $cDocSuf  = $_POST['cDosSuf_DOS'.($i+1)];
      $cSucId   = $_POST['cSucId_DOS' .($i+1)];
    }
    $dFecMay = ($dFecMay > substr($_POST['cDosFec_DOS'.($i+1)],0,4)) ? substr($_POST['cDosFec_DOS'.($i+1)],0,4) : $dFecMay;
  }//for ($i=0;$i<$_POST['nSecuencia_Dos'];$i++) {
  $nAnoIniDo = (($dFecMay-1) <  $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : ($dFecMay-1);
  ##Fin Traigo Primer Do para traer primer Do y traer Datos de Tasa de Cambio, Documento de Transporte, Bultos, Peso ##

  ##Busco Ciudad por donde se esta facturando ##
  $qCcoDat  = "SELECT * ";
  $qCcoDat .= "FROM $cAlfa.fpar0008 ";
  $qCcoDat .= "WHERE ";
  $qCcoDat .= "$cAlfa.fpar0008.sucidxxx = \"{$cSucId}\" AND ";
  $qCcoDat .= "$cAlfa.fpar0008.regestxx = \"ACTIVO\" LIMIT 0,1 ";
  $xCcoDat  = f_MySql("SELECT","",$qCcoDat,$xConexion01,"");
  $nFilCco  = mysql_num_rows($xCcoDat);
  if ($nFilCco > 0) {
    $cSucDesc = mysql_fetch_array($xCcoDat);
  }

  ##Busco Do en la sys00121 Tabla de Do's Financieros y traigo Tipo de Operacion ##
  $qDceDat  = "SELECT * ";
  $qDceDat .= "FROM $cAlfa.sys00121 ";
  $qDceDat .= "WHERE ";
  $qDceDat .= "$cAlfa.sys00121.sucidxxx = \"$cSucId\" AND ";
  $qDceDat .= "$cAlfa.sys00121.docidxxx = \"$cDocId\" AND ";
  $qDceDat .= "$cAlfa.sys00121.docsufxx = \"$cDocSuf\" ";
  $xDceDat  = f_MySql("SELECT","",$qDceDat,$xConexion01,"");
  $nFilDce  = mysql_num_rows($xDceDat);
  if ($nFilDce > 0) {
    $vDceDat = mysql_fetch_array($xDceDat);
  }
  ##Fin Busco Do en la sys00121 Tabla de Do's Financieros y traigo Tipo de Operacion ##

  ##Switch para traer Datos de Do Dependiendo del Tipo de Operacion ##
  switch ($vDceDat['doctipxx']){
    case "IMPORTACION":
      ##Traigo Datos de la SIAI0200 DATOS DEL DO ##
      $qDoiDat  = "SELECT * ";
      $qDoiDat .= "FROM $cAlfa.SIAI0200 ";
      $qDoiDat .= "WHERE $cAlfa.SIAI0200.DOIIDXXX = \"$cDocId\" AND ";
      $qDoiDat .= "$cAlfa.SIAI0200.DOISFIDX = \"$cDocSuf\" AND ";
      $qDoiDat .= "$cAlfa.SIAI0200.ADMIDXXX = \"$cSucId\" ";
      //f_Mensaje(__FILE__,__LINE__,$qDoiDat);
      $xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
      $nFilDoi  = mysql_num_rows($xDoiDat);
      if ($nFilDoi > 0) {
        $vDoiDat  = mysql_fetch_array($xDoiDat);
      }
      ##Fin Traigo Datos de la SIAI0200 DATOS DEL DO ##

      ##Traigo Datos de Do SIAI0206 ##
      $qDecDat  = "SELECT ";
      $qDecDat .= "SUBID2XX, ";
      $qDecDat .= "ADMIDXXX, ";
      $qDecDat .= "SUM($cAlfa.SIAI0206.LIMBULXX) AS LIMBULXX, ";
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
      $qDecDat .= "$cAlfa.SIAI0206.DOIIDXXX = \"$cDocId\"  AND ";
      $qDecDat .= "$cAlfa.SIAI0206.DOISFIDX = \"$cDocSuf\" AND ";
      $qDecDat .= "$cAlfa.SIAI0206.ADMIDXXX = \"$cSucId\" ";
      $qDecDat .= "GROUP BY $cAlfa.SIAI0206.DOIIDXXX ";

      //f_Mensaje(__FILE__,__LINE__,$qDecDat);
      $xDecDat  = f_MySql("SELECT","",$qDecDat,$xConexion01,"");
      $nFilDec  = mysql_num_rows($xDecDat);
      if ($nFilDec > 0) {
        $vDecDat  = mysql_fetch_array($xDecDat);
      }
      ##Traigo Datos de Do SIAI0206 ##

      ##Administracion de ingreso##
      $vAdmIng = array();
      if ($vDoiDat['ODIIDXXX'] != "") {
        $qAdmIng  = "SELECT ODIDESXX ";
        $qAdmIng .= "FROM $cAlfa.SIAI0103 ";
        $qAdmIng .= "WHERE ";
        $qAdmIng .= "ODIIDXXX = \"{$vDoiDat['ODIIDXXX']}\" ";
        $qAdmIng .= "LIMIT 0,1 ";
        $xAdmIng  = f_MySql("SELECT","",$qAdmIng,$xConexion01,"");
        $vAdmIng  = mysql_fetch_array($xAdmIng);
      }

      //Busco nombre comercial
      $qDceDat  = "SELECT $cAlfa.SIAI0205.ITENOCXX ";
      $qDceDat .= "FROM $cAlfa.SIAI0205 ";
      $qDceDat .= "WHERE ";
      $qDceDat .= "$cAlfa.SIAI0205.ADMIDXXX = \"$cSucId\" AND ";
      $qDceDat .= "$cAlfa.SIAI0205.DOIIDXXX = \"$cDocId\" AND ";
      $qDceDat .= "$cAlfa.SIAI0205.DOISFIDX = \"$cDocSuf\" LIMIT 0,1";
      $xDceDat  = f_MySql("SELECT","",$qDceDat,$xConexion01,"");
      $nFilDce  = mysql_num_rows($xDceDat);
      if ($nFilDce > 0) {
        $vNomComercial = mysql_fetch_array($xDceDat);
      }

      //Busco transportadora comercial
      $qTraDat  = "SELECT $cAlfa.SIAI0206.TRADESXX ";
      $qTraDat .= "FROM $cAlfa.SIAI0206 ";
      $qTraDat .= "WHERE ";
      $qTraDat .= "$cAlfa.SIAI0206.ADMIDXXX = \"$cSucId\" AND ";
      $qTraDat .= "$cAlfa.SIAI0206.DOIIDXXX = \"$cDocId\" AND ";
      $qTraDat .= "$cAlfa.SIAI0206.DOISFIDX = \"$cDocSuf\" LIMIT 0,1";
      $xTraDat  = f_MySql("SELECT","",$qTraDat,$xConexion01,"");
      $nFilDce  = mysql_num_rows($xTraDat);
      if ($nFilDce > 0) {
        $vTransComercial = mysql_fetch_array($xTraDat);
      }

      ##Cargo Variables para Impresion de Datos de Do ##
      $cTasCam = $vDoiDat['TCATASAX']; //Tasa de Cambio
      $cDocTra = $vDoiDat['DGEDTXXX']; //Documento de Transporte
      $cBultos = $vDoiDat['DGEBULXX']; //Bultos
      $cPesBru = $vDoiDat['DGEPBRXX']; //Peso Bruto
      $nValAdu  = $vDecDat['LIMCIFXX'];
      $cOpera  = "CIF:";
      $cManifi = $vDoiDat['DGEMCXXX'];
      $cPaisOrigen = $vDoiDat['PAIIDXXX'];
      $cPedido = $vDoiDat['DOIPEDXX'];
      $cAduana = $vAdmIng['ODIDESXX'];
      $cLinPro = $vDoiDat['LPRID3XX'];
      $cTraCom = $vTransComercial['TRADESXX'];
      $cFecLev = $vDoiDat['DOIMYLEV'];
      $cNomVen = $vDoiDat['VENNOMXX'];
      ##Fin Cargo Variables para Impresion de Datos de Do ##
    break;
    case "EXPORTACION":
      ## Consulto Datos de Do en Exportaciones tabla siae0199 ##
      $qDexDat  = "SELECT * ";
      $qDexDat .= "FROM $cAlfa.siae0199 ";
      $qDexDat .= "WHERE ";
      $qDexDat .= "$cAlfa.siae0199.dexidxxx = \"$cDocId\" AND ";
      $qDexDat .= "$cAlfa.siae0199.admidxxx = \"$cSucId\" ";
      $xDexDat  = f_MySql("SELECT","",$qDexDat,$xConexion01,"");
      //f_Mensaje(__FILE__,__LINE__,$qDexDat);
      $nFilDex  = mysql_num_rows($xDexDat);
      if ($nFilDex > 0) {
        $vDexDat = mysql_fetch_array($xDexDat);
      }
      ## Fin Consulto Datos de Do en Exportaciones tabla siae0199 ##
      ##Traigo Valor fob, peso neto, peso bruto, bultos de la tabla de Items de Exportaciones siae0201 ##
      $qIteDat  = "SELECT ";
      $qIteDat .= "SUM($cAlfa.siae0201.itefobxx) AS itefobxx, ";
      $qIteDat .= "SUM($cAlfa.siae0201.itepbrxx) AS itepbrxx, ";
      $qIteDat .= "SUM($cAlfa.siae0201.itepnexx) AS itepnexx, ";
      $qIteDat .= "SUM($cAlfa.siae0201.itebulxx) AS itebulxx ";
      $qIteDat .= "FROM $cAlfa.siae0201 ";
      $qIteDat .= "WHERE ";
      $qIteDat .= "$cAlfa.siae0201.dexidxxx =\"$cDocId\" AND ";
      $qIteDat .= "$cAlfa.siae0201.admidxxx = \"$cSucId\" ";
      $xIteDat  = f_MySql("SELECT","",$qIteDat,$xConexion01,"");
      $nFilIte  = mysql_num_rows($xIteDat);
      if ($nFilIte > 0) {
        $vIteDat = mysql_fetch_array($xIteDat);
      }

      ##Trayendo aduana de salida##
      $qAduSal  = "SELECT odiid2xx ";
      $qAduSal .= "FROM $cAlfa.siae0200 ";
      $qAduSal .= "WHERE ";
      $qAduSal .= "$cAlfa.siae0200.dexidxxx = \"$cDocId\" AND ";
      $qAduSal .= "$cAlfa.siae0200.admidxxx = \"$cSucId\" AND ";
      $qAduSal .= "$cAlfa.siae0200.odiid2xx != \"\" LIMIT 0,1 ";
      $xAduSal  = f_MySql("SELECT","",$qAduSal,$xConexion01,"");
      $vAduSal  = mysql_fetch_array($xAduSal);
      if ($vAduSal['odiid2xx'] != "") {
        ##Tayendo descripcion Aduana de salida
        $qDesAdu  = "SELECT ODIDESXX ";
        $qDesAdu .= "FROM $cAlfa.SIAI0103 ";
        $qDesAdu .= "WHERE ";
        $qDesAdu .= "ODIIDXXX = \"{$vAduSal['odiid2xx']}\" ";
        $qDesAdu .= "LIMIT 0,1 ";
        $xDesAdu  = f_MySql("SELECT","",$qDesAdu,$xConexion01,"");
        $vDesAdu  = mysql_fetch_array($xDesAdu);
      }

      ##Cargo Variables para Impresion de Datos de Do ##
      $cTasCam = $vDoiDat['TCATASAX']; //Tasa de Cambio
      $cDocTra = $vDexDat['dexdtrxx']; //Documento de Transporte
      $cBultos = $vIteDat['itebulxx']; //Bultos
      $cPesBru = $vIteDat['itepbrxx']; //Peso Bruto
      $nValAdu  = ($vIteDat['itefobxx']*$vDceDat['doctrmxx']);
      $cOpera  = "FOB"; // FOB
      $cManifi = $vDexDat['dexmanxx']; //Manifiesto
      $cPedido = $vDexDat['dexpedxx'];
      $cAduana = $vDesAdu['ODIDESXX'];
      $cLinPro = $vDoiDat['lprid3xx'];
      $cTraCom = "";
      $cFecLev = "";
      $cNomVen = $vDexDat['vennomxx'];
      ##Fin Cargo Variables para Impresion de Datos de Do ##
    break;
    case "TRANSITO":
      ## Traigo Datos de la SIAI0200 ##
      $qDoiDat  = "SELECT * ";
      $qDoiDat .= "FROM $cAlfa.SIAI0200 ";
      $qDoiDat .= "WHERE ";
      $qDoiDat .= "DOIIDXXX = \"$cDocId\" AND ";
      $qDoiDat .= "DOISFIDX = \"$cDocSuf\" AND ";
      $qDoiDat .= "ADMIDXXX = \"$cSucId\" ";
      $xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
      $nFilDoi  = mysql_num_rows($xDoiDat);
      if ($nFilDoi > 0) {
        $vDoiDat = mysql_fetch_array($xDoiDat);
      }
      ## Fin Consulta a la tabla de Do's ##
      //f_Mensaje(__FILE__,__LINE__,$qDoiDat);

      ## Consulto en la Tabla de Control DTA ##
      $qDtaDat  = "SELECT * ";
      $qDtaDat .= "FROM $cAlfa.dta00200 ";
      $qDtaDat .= "WHERE ";
      $qDtaDat .= "$cAlfa.dta00200.doiidxxx = \"$cDocId\" AND ";
      $qDtaDat .= "$cAlfa.dta00200.admidxxx = \"$cSucId\" ";
      $xDtaDat  = f_MySql("SELECT","",$qDtaDat,$xConexion01,"");
      $nFilDta  = mysql_num_rows($xDtaDat);
      if ($nFilDta > 0) {
        $vDtaDat = mysql_fetch_array($xDtaDat);
      }
      ## Fin consulto en la tabla de Control DTA ##
      ## Consulto en la tabla de Items DTA ##
      $qIteDat  = "SELECT  ";
      $qIteDat .= "SUM($cAlfa.dta00201.itepbrxx) AS itepbrxx, ";
      $qIteDat .= "SUM($cAlfa.dta00201.itebulxx) AS itebulxx ";
      $qIteDat .= "FROM $cAlfa.dta00201 ";
      $qIteDat .= "WHERE ";
      $qIteDat .= "$cAlfa.dta00201.doiidxxx = \"$cDocId\" AND ";
      $qIteDat .= "$cAlfa.dta00201.admidxxx = \"$cSucId\" ";
      $xIteDat  = f_MySql("SELECT","",$qIteDat,$xConexion01,"");
      $nFilIte  = mysql_num_rows($xIteDat);
      if ($nFilIte > 0) {
        $vIteDat = mysql_fetch_array($xIteDat);
      }

      ##Administracion de ingreso##
      $vAdmIng = array();
      if ($vDoiDat['ODIIDXXX'] != "") {
        $qAdmIng  = "SELECT ODIDESXX ";
        $qAdmIng .= "FROM $cAlfa.SIAI0103 ";
        $qAdmIng .= "WHERE ";
        $qAdmIng .= "ODIIDXXX = \"{$vDoiDat['ODIIDXXX']}\" ";
        $qAdmIng .= "LIMIT 0,1 ";
        $xAdmIng  = f_MySql("SELECT","",$qAdmIng,$xConexion01,"");
        $vAdmIng  = mysql_fetch_array($xAdmIng);
      }

      //Busco nombre comercial
      $qDceDat  = "SELECT $cAlfa.SIAI0205.ITENOCXX ";
      $qDceDat .= "FROM $cAlfa.SIAI0205 ";
      $qDceDat .= "WHERE ";
      $qDceDat .= "$cAlfa.SIAI0205.ADMIDXXX = \"$cSucId\" AND ";
      $qDceDat .= "$cAlfa.SIAI0205.DOIIDXXX = \"$cDocId\" AND ";
      $qDceDat .= "$cAlfa.SIAI0205.DOISFIDX = \"$cDocSuf\" LIMIT 0,1";
      $xDceDat  = f_MySql("SELECT","",$qDceDat,$xConexion01,"");
      $nFilDce  = mysql_num_rows($xDceDat);
      if ($nFilDce > 0) {
        $vNomComercial = mysql_fetch_array($xDceDat);
      }

      //Busco transportadora comercial
      $qTraDat  = "SELECT $cAlfa.SIAI0206.TRADESXX ";
      $qTraDat .= "FROM $cAlfa.SIAI0206 ";
      $qTraDat .= "WHERE ";
      $qTraDat .= "$cAlfa.SIAI0206.ADMIDXXX = \"$cSucId\" AND ";
      $qTraDat .= "$cAlfa.SIAI0206.DOIIDXXX = \"$cDocId\" AND ";
      $qTraDat .= "$cAlfa.SIAI0206.DOISFIDX = \"$cDocSuf\" LIMIT 0,1";
      $xTraDat  = f_MySql("SELECT","",$qTraDat,$xConexion01,"");
      $nFilDce  = mysql_num_rows($xTraDat);
      if ($nFilDce > 0) {
        $vTransComercial = mysql_fetch_array($xTraDat);
      }

      ##Cargo Variables para Impresion de Datos de Do ##
      $cTasCam = $vDoiDat['TCATASAX']; //Tasa de Cambio
      $cDocTra = $vDoiDat['DGEDTXXX']; //Documento de Transporte
      $cBultos = $vIteDat['itebulxx']; //Bultos
      $cPesBru = $vIteDat['itepbrxx']; //Peso Bruto
      $nValAdu  = $vDtaDat['dtafobxx'];
      $cOpera  = "CIF"; // CIF
      $cManifi = $vDoiDat['DGEMCXXX'];
      $cPedido = $vDoiDat['DOIPEDXX'];
      $cAduana = $vAdmIng['ODIDESXX'];
      $cLinPro = $vDoiDat['LPRID3XX'];
      $cTraCom = $vTransComercial['TRADESXX'];
      $cFecLev = $vDoiDat['DOIMYLEV'];
      $cNomVen = $vDoiDat['VENNOMXX'];
      ##Fin Cargo Variables para Impresion de Datos de Do ##
    break;
    case "OTROS":
    break;
  }//switch (){
  ##Fin Switch para traer Datos de Do Dependiendo del Tipo de Operacion ##

  #Agrupo Ingresos Propios
  $mIP = array();
  for($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
      $cObs    = "";
      $cCif    = "";
      $nCif    = 0;
      $cAplCif = "NO";

      $cDim    = "";
      $nDim    = 0;
      $cAplDim = "NO";

      $cHor    = "";
      $nHor    = 0;
      $cAplHor = "NO";

      $cPie    = "";
      $nPie    = 0;
      $cAplPie = "NO";

      $cDav    = "";
      $nDav    = 0;
      $cAplDav = "NO";

      $cVuce    = "";
      $nVuce    = 0;
      $cAplVuce = "NO";

      $cCertificados    = "";
      $nCertificados    = 0;
      $cAplCertificados = "NO";

      $cDex    = "";
      $nDex    = 0;
      $cAplDex = "NO";

      $cSerial    = "";
      $nSerial    = 0;
      $cAplSerial = "NO";

      $cArancelaria    = "";
      $nArancelaria    = 0;
      $cAplArancelaria = "NO";

      $cDta      = "";
      $nDta      = 0;
      $cAplDta   = "NO";

      $cItems    = "";
      $nItems    = 0;
      $cAplItems = "NO";

      $cCan      = "";
      $nCan      = 0;
      $cAplCan   = "NO";

      $cFob      = "";
      $nFob      = 0;
      $cAplFob   = "NO";

      $cCon20    = "";
      $nCon20    = 0;
      $cAplCon20 = "NO";

      $cCon40    = "";
      $nCon40    = 0;
      $cAplCon40 = "NO";

      $cCarSue   = "";
      $nCarSue   = 0;
      $cAplCarSue= "NO";

      $cFobAgen = "NO";    // Valor Fob de Agenciamiento para Expo
      $xRDD     = array(); //Inicializando el cursor de Valor Fob

      switch ($_POST['cSerId_IPA'.($i+1)]) {
        case "109":
        $nComObs_IP = stripos($_POST['cComObs_IPA'.($i+1)], "[");
        if($nComObs_IP > 0){
          $mAuxArancelaria = explode("CLASIFICACIONES ARANCELARIAS:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
          $cArancelaria = "";
          if(count($mAuxArancelaria) > 1) {
            $cArancelaria    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxArancelaria[1]);
            $nArancelaria    = $cArancelaria;
            $cAplArancelaria = "SI";
          }
          $cObs = substr(substr($_POST['cComObs_IPA'.($i+1)],0,$nComObs_IP),0,70);
        }else{
          $cObs = substr($_POST['cComObs_IPA'.($i+1)],0,70);
        }
        break;
        case "111":
        $nComObs_IP = stripos($_POST['cComObs_IPA'.($i+1)], "[");
        if($nComObs_IP > 0){
          $mAuxPie = explode("PIEZAS:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
          $cPie = "";
          if(count($mAuxPie) > 1) {
            $cPie    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxPie[1]);
            $nPie    = $cPie;
            $cAplPie = "SI";
          }
          $cObs = substr(substr($_POST['cComObs_IPA'.($i+1)],0,$nComObs_IP),0,70);
        }else{
          $cObs = substr($_POST['cComObs_IPA'.($i+1)],0,70);
        }
        break;
        case "101":
        case "103":
        case "119":
        case "201":
        case "309":
          $nComObs_IP = stripos($_POST['cComObs_IPA'.($i+1)], "[");
          if($nComObs_IP > 0){
            $mAuxHor    = explode("HORAS:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
            $mAuxSerial = explode("CANT SERIALES:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
            $mAuxItems  = explode("ITEMS:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
            $mAuxCan    = explode("CANTIDAD:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));

            $cDim    = "";
            $cSerial = "";
            $cItems  = "";
            if(count($mAuxHor) > 1) {
              $cHor    =str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxHor[1]);
              $nHor    = $cHor;
              $cAplHor = "SI";
            }

            if(count($mAuxSerial) > 1) {
              $cSerial    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxSerial[1]);
              $nSerial    = $cSerial;
              $cAplSerial = "SI";
            }

            if(count($mAuxItems) > 1) {
              $cItems    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxItems[1]);
              $nItems    = $cItems;
              $cAplItems = "SI";
            }

            if(count($mAuxCan) > 1) {
              $cCan    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxCan[1]);
              $nCan    = $cCan;
              $cAplCan = "SI";
            }

            $cObs = substr(substr($_POST['cComObs_IPA'.($i+1)],0,$nComObs_IP),0,70);
          }else{
            $cObs = substr($_POST['cComObs_IPA'.($i+1)],0,70);
          }
        break;
        case "102":
        case "110":
          $nComObs_IP = stripos($_POST['cComObs_IPA'.($i+1)], "[");
          if($nComObs_IP > 0){
            $mAuxDim = explode("DIM:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
            $cDim = "";
            if(count($mAuxDim) > 1) {
              $cDim    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxDim[1]);
              $nDim    = $cDim;
              $cAplDim = "SI";
            }
            $cObs = substr(substr($_POST['cComObs_IPA'.($i+1)],0,$nComObs_IP),0,70);
          }else{
            $cObs = substr($_POST['cComObs_IPA'.($i+1)],0,70);
          }
        break;
        case "103":
        case "148":
        case "156":
          $nComObs_IP = stripos($_POST['cComObs_IPA'.($i+1)], "[");
          if($nComObs_IP > 0){
            $mAuxDav = explode("DAV:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
            $mAuxDavMag = explode("DAV MAGNETICAS:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
            $cDav = "";
            if(count($mAuxDav) > 1 || count($mAuxDavMag) > 1) {
              $cDav    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "",  (count($mAuxDav) > 1) ? $mAuxDav[1]  : $mAuxDavMag[1] );
              $nDav    = $cDav;
              $cAplDav = "SI";
            }
            $cObs = substr(substr($_POST['cComObs_IPA'.($i+1)],0,$nComObs_IP),0,70);
          }else{
            $cObs = substr($_POST['cComObs_IPA'.($i+1)],0,70);
          }
        break;
        case "104":
        case "504":
          $nComObs_IP = stripos($_POST['cComObs_IPA'.($i+1)], "[");
          if($nComObs_IP > 0){
            $mAuxVuce = explode("VUCE:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
            $cVuce = "";
            if(count($mAuxVuce) > 1) {
              $cVuce    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxVuce[1]);
              $nVuce    = $cVuce;
              $cAplVuce = "SI";
            }
            $cObs = substr(substr($_POST['cComObs_IPA'.($i+1)],0,$nComObs_IP),0,70);
          }else{
            $cObs = substr($_POST['cComObs_IPA'.($i+1)],0,70);
          }//if($mComObs_IP[$i][2] != ""){
        break;
        case "200":
          $nComObs_IP = stripos($_POST['cComObs_IPA'.($i+1)], "[");
          if($nComObs_IP > 0){
            $mAuxFob    = explode("FOB:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
            $mAuxFob[0] = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxFob[1]);
            $cObs = substr(substr($_POST['cComObs_IPA'.($i+1)],0,$nComObs_IP),0,70);
          }else{
            $cObs = substr($_POST['cComObs_IPA'.($i+1)],0,70);
          }
        break;
        case "203":
          $nComObs_IP = stripos($_POST['cComObs_IPA'.($i+1)], "[");
          if($nComObs_IP > 0){
            $mAuxCertificados = explode("ORIGEN:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
            $cCertificados = "";
            if(count($mAuxCertificados) > 1) {
              $cCertificados    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxCertificados[1]);
              $nCertificados    = $cCertificados;
              $cAplCertificados = "SI";
            }
            $cObs = substr(substr($_POST['cComObs_IPA'.($i+1)],0,$nComObs_IP),0,70);
          }else{
            $cObs = substr($_POST['cComObs_IPA'.($i+1)],0,70);
          }//if($mComObs_IP[$i][2] != ""){
        break;
        case '201':
        case '204':
        case "202":
          $nComObs_IP = stripos($_POST['cComObs_IPA'.($i+1)], "[");
          if($nComObs_IP > 0){
            $mAuxDex = explode("DEX:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
            $cDex = "";
            if(count($mAuxDex) > 1) {
              $cDex    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxDex[1]);
              $nDex    = $cDex;
              $cAplDex = "SI";
            }
            $cObs = substr(substr($_POST['cComObs_IPA'.($i+1)],0,$nComObs_IP),0,70);
          }else{
            $cObs = substr($_POST['cComObs_IPA'.($i+1)],0,70);
          }
        break;
        case "301":
        case "308":
          $nComObs_IP = stripos($_POST['cComObs_IPA'.($i+1)], "[");
          if($nComObs_IP > 0){
            $mAuxDta = explode("DTA:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
            $cDta = "";
            if(count($mAuxDta) > 1) {
              $cDta    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxDta[1]);
              $nDta    = $cDta;
              $cAplDta = "SI";
            }
            $cObs = substr(substr($_POST['cComObs_IPA'.($i+1)],0,$nComObs_IP),0,70);
          }else{
            $cObs = substr($_POST['cComObs_IPA'.($i+1)],0,70);
          }
        break;
        case "305":
          $nComObs_IP = stripos($_POST['cComObs_IPA'.($i+1)], "[");
          if($nComObs_IP > 0){
            $mAuxCan = explode("Cantidad:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
            $cCan = "";
            if(count($mAuxCan) > 1) {
              $cCan    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxCan[1]);
              $nCan    = $cCan;
              $cAplCan = "SI";
            }
            $cObs = substr(substr($_POST['cComObs_IPA'.($i+1)],0,$nComObs_IP),0,70);
          }else{
            $cObs = substr($_POST['cComObs_IPA'.($i+1)],0,70);
          }
        break;
        case "300":
        case "307":
          $nComObs_IP = stripos($_POST['cComObs_IPA'.($i+1)], "[");
          if($nComObs_IP > 0){
            //Valor FOB - Buscando Posicion TRM en la observacion
            $nPosTrm   = stripos($_POST['cComObs_IPA'.($i+1)], "TRM");
            $mAuxFob   = explode("FOB:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,($nPosTrm-$nComObs_IP)));
            //Contenedores de 20 - Buscando Posicion Contenedores de 40
            $nPosCon40 = stripos($_POST['cComObs_IPA'.($i+1)], "CONTENEDORES DE 40:");
            $nPosCon40 = ($nPosCon40 === false) ? strlen($_POST['cComObs_IPA'.($i+1)]) : $nPosCon40 ;
            $mAuxCon20 = explode("CONTENEDORES DE 20:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,($nPosCon40-$nComObs_IP)));
            //Contenedores de 40
            $mAuxCon40 = explode("CONTENEDORES DE 40:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
            //Carga Suelta
            $mAuxCarSue = explode("UNIDADES DE CARGA SUELTA:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));

            $cFob    = "";
            $cCon20  = "";
            $cCon40  = "";
            $cCarSue = "";
            if(count($mAuxFob) > 1) {
              $cFob    = str_replace(array(".","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxFob[1]);
              $nFob    = $cFob;
              $cAplFob = "SI";
            }
            if(count($mAuxCon20) > 1) {
              $cCon20 = str_replace(array("(",")",".","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxCon20[1]);
              $nCon20 = $cCon20;
              $cAplCon20 = "SI";
            }
            if(count($mAuxCon40) > 1) {
              $cCon40 = str_replace(array("(",")",".","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxCon40[1]);
              $nCon40 = $cCon40;
              $cAplCon40 = "SI";
            }
            if(count($mAuxCarSue) > 1) {
              $cCarSue = str_replace(array("(",")",".","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxCarSue[1]);
              $nCarSue = $cCarSue;
              $cAplCarSue = "SI";
              // f_Mensaje(__FILE__,__LINE__,$nCarSue);
            }
            $cObs = substr(substr($_POST['cComObs_IPA'.($i+1)],0,$nComObs_IP),0,70);
          }else{
            $cObs = substr($_POST['cComObs_IPA'.($i+1)],0,70);
          }
        break;
        default:
          $nComObs_IP = stripos($_POST['cComObs_IPA'.($i+1)], "[");
          if($nComObs_IP > 0){
            $mAuxCif = explode("CIF:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
            $cCif = "";

            if(count($mAuxCif) > 1) {
              $cCif    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxCif[1]);
              $nCif    = $cCif;
              $cAplCif = "SI";
            }
            $cObs = substr(substr($_POST['cComObs_IPA'.($i+1)],0,$nComObs_IP),0,70);
          }else{
            $cObs = substr($_POST['cComObs_IPA'.($i+1)],0,70);
          }
        break;
      }
      switch ($_POST['cSerId_IPA'.($i+1)]) {
        case "200":
        case "203":
          $qDocDat  = "SELECT docfobxx,doctrmxx ";
          $qDocDat .= "FROM $cAlfa.sys00121 ";
          $qDocDat .= "WHERE ";
          $qDocDat .= "docidxxx = \"{$cDocId}\" AND ";
          $qDocDat .= "sucidxxx = \"{$cSucId}\" AND ";
          $qDocDat .= "docsufxx = \"{$cDocSuf}\" LIMIT 0,1 ";
          $xDocDat  = f_MySql("SELECT","",$qDocDat,$xConexion01,"");
          $xRDD = mysql_fetch_array($xDocDat);
          $cFobAgen = "SI";
        break;
      }

    $mIP[$_POST['cComId_IPA'.($i+1)]]['comvlr01'] += $_POST['nComVIva_IPA'.($i+1)];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comvlrxx'] += $_POST['nComVlr_IPA'.($i+1)];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['ctoidxxx']  = $_POST['cComId_IPA'.($i+1)];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comobsxx']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comobsxx'] == "") ? trim($cObs) : $mIP[$_POST['cComId_IPA'.($i+1)]]['comobsxx'];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comcifxx'] += $nCif;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comcifap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comcifap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comcifap']:$cAplCif;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comdimxx'] += $nDim;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comdimap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comdimap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comdimap']:$cAplDim;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comdavxx'] += $nDav;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comdavap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comdavap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comdavap']:$cAplDav;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comvucxx'] += $nVuce;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comvucap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comvucap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comvucap']:$cAplVuce;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comcerxx'] += $nCertificados;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comcerap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comcerap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comcerap']:$cAplCertificados;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comfobxx']  = $cFobAgen;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['docfobxx'] += $xRDD['docfobxx'];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['doctrmxx']  = $xRDD['doctrmxx'];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comhorxx'] += $nHor;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comhorap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comhorap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comhorap']:$cAplHor;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['compiexx'] += $nPie;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['compieap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['compieap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['compieap']:$cAplPie;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comdexxx'] += $nDex;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comdexap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comdexap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comdexap']:$cAplDex;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comserxx'] += $nSerial;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comserap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comserap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comserap']:$cAplSerial;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comaraxx'] += $nArancelaria;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comaraap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comaraap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comaraap']:$cAplArancelaria;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comdtaxx'] += $nDta;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comdtaap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comdtaap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comdtaap']:$cAplDta;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comitexx'] += $nItems;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comiteap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comiteap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comiteap']:$cAplItems;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comcanxx'] += $nCan;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comcanap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comcanap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comcanap']:$cAplCan;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comfob2x'] += $nFob;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comfobap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comfobap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comfobap']:$cAplFob;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comc20xx'] += $nCon20;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comc20ap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comc20ap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comc20ap']:$cAplCon20;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comc40xx'] += $nCon40;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comc40ap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comc40ap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comc40ap']:$cAplCon40;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comcsuxx'] += $nCarSue;
    // f_Mensaje(__FILE__,__LINE__,"Antes: ".$mIP[$_POST['cComId_IPA'.($i+1)]]['comcsuap']);
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comcsuap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comcsuap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comcsuap']:$cAplCarSue;
    // f_Mensaje(__FILE__,__LINE__,"Despues: ".$mIP[$_POST['cComId_IPA'.($i+1)]]['comcsuap']);
  }//for ($k=0;$k<count($mCodDat);$k++) {

  $mIngPro = array();
  foreach ($mIP as $cKey => $mValores) {
    $mIngPro[count($mIngPro)] = $mValores;
  }
  $mValores = array();
  ##Codigo para imprimir los ingresos para terceros ##
  $mIngTer = array();
  for ($i=0;$i<$_POST['nSecuencia_PCCA'];$i++) {
    $vTercero = explode("^",$_POST['cComObs_PCCA'.($i+1)]);
    $mComObs_IP = stripos($_POST['cComObs_PCCA'.($i+1)], "[");

    if (substr($_POST['cComId_PCCA'.($i+1)],0,1) == "4") { //si es el 4xmil mostrarse en ingresos propios
      $nInd_mValores = count($mValores);
      $mValores[$nInd_mValores]['comobsxx'] = trim($vTercero[0]);
      $mValores[$nInd_mValores]['comvlrxx'] = $_POST['nComVlr_PCCA'.($i+1)];
      // $mValores[$nInd_mValores]['puctipej'] = $xRCD['puctipej'];
      $mValores[$nInd_mValores]['ctoidxxx'] = $_POST['cComId_PCCA'.($i+1)];
      $mValores[$nInd_mValores]['comvlr01'] = $_POST['nVlrIva_PCCA'.($i+1)];
      $mValores[$nInd_mValores]['cTerNom']  = trim($vTercero[1]);

    } else {
      /*if (substr_count($_POST['cComObs_PCCA' .($i+1)]," DIAN") > 0 ||
         (trim(substr($_POST['cComObs_PCCA'.($i+1)],0,$mComObs_IP)) == "RECIBO OFICIAL DE PAGO TRIBUTOS ADUANEROS Y SANCIO")  ||
         substr_count($_POST['cComObs_PCCA' .($i+1)],"DECLARACION") > 0 ) { // Encontre la palabra DIAN de pago de impuestos.
         $nInd_mIngTer = count($mIngTer);

         $mIngTer[$nInd_mIngTer]['cComId']   = $_POST['cComId_PCCA'  .($i+1)];
         $mIngTer[$nInd_mIngTer]['cComObs']  = "DERECHOS DE ADUANA Y COMPLEMENTARIOS";
         $mIngTer[$nInd_mIngTer]['cTerNom']  = "DIAN";
         $mIngTer[$nInd_mIngTer]['cComCsc3'] = ((strlen($mIngTer[$nInd_mIngTer]['cComCsc3']) + strlen("/".$_POST['cComCsc3_PCCA'.($i+1)])) <= 40) ? "/".$_POST['cComCsc3_PCCA'.($i+1)] : "";
         $mIngTer[$nInd_mIngTer]['nComVlr']  = $_POST['nComVlr_PCCA' .($i+1)];
         $mIngTer[$nInd_mIngTer]['nBaseIva'] = $_POST['nBaseIva_PCCA'.($i+1)];
         $mIngTer[$nInd_mIngTer]['nVlrIva']  = $_POST['nVlrIva_PCCA'.($i+1)];
       }else{*/
            $vRCM = array();
            if (in_array("{$_POST['cComId3_PCCA'.($i+1)]}~{$_POST['cComCod3_PCCA'.($i+1)]}", $mRCM) == true) {
              $vTramite = explode("-",$_POST['cComTra_PCCA'.($i+1)]);
              $cSucId  = $vTramite[0];
              $cDocId  = "";
              for($nD=1; $nD<count($vTramite)-1; $nD++) {
                $cDocId .= "{$vTramite[$nD]}-";
              }
              $cDocId  = substr($cDocId, 0,-1);
              $cDocSuf = $vTramite[count($vTramite)-1];

              for($iAno=date('Y');$iAno>=$nAnoIniDo;$iAno--) {
                $qComMar  = "SELECT comcsc2x ";
                $qComMar .= "FROM $cAlfa.fcod$iAno ";
                $qComMar .= "WHERE ";
                $qComMar .= "comidxxx = \"{$_POST['cComId3_PCCA' .($i+1)]}\" AND ";
                $qComMar .= "comcodxx = \"{$_POST['cComCod3_PCCA'.($i+1)]}\" AND ";
                $qComMar .= "comcscxx = \"{$_POST['cComCsc3_PCCA'.($i+1)]}\" AND ";
                $qComMar .= "comseqxx = \"{$_POST['cComSeq3_PCCA'.($i+1)]}\" AND ";
                $qComMar .= "ctoidxxx = \"{$_POST['cComId_PCCA'  .($i+1)]}\" AND ";
                $qComMar .= "teridxxx = \"{$_POST['cTerId']}\" AND ";
                $qComMar .= "terid2xx = \"".trim($vTercero[2])."\" AND ";
                $qComMar .= "comidcxx = \"P\"        AND ";
                $qComMar .= "comcodcx = \"001\"      AND ";
                $qComMar .= "comcsccx = \"$cDocId\"  AND ";
                $qComMar .= "comseqcx = \"$cDocSuf\" AND ";
                $qComMar .= "regestxx = \"ACTIVO\" ";
                $xComMar  = f_MySql("SELECT","",$qComMar,$xConexion01,"");
                if(mysql_num_rows($xComMar) > 0) {
                  //f_Mensaje(__FILE__,__LINE__,$qComMar,"~",mysql_num_rows($xComMar));
                  $iAno = $nAnoIniDo-1;
                  $vRCM = mysql_fetch_array($xComMar);
                }
              }
            }

            $nSwitch_Find = 0;
            // for ($j=0;$j<count($mIngTer);$j++) {
            //    //F_Mensaje(__FILE__,__LINE__,$_POST['cComId_PCCA'.($i+1)]. " - ".$mIngTer[$j]['cComId']);
            //   if ($_POST['cComId_PCCA'.($i+1)] == $mIngTer[$j]['cComId'] /*&& trim($vTercero[2]) == $mIngTer[$j]['cTerId']*/) {
            //     $nSwitch_Find = 1;
            //     $mIngTer[$j]['cComCsc3'] .= ((strlen($mIngTer[$nInd_mIngTer]['cComCsc3'])+strlen("/".$_POST['cComCsc3_PCCA'.($i+1)])) <= 40) ? "/".$_POST['cComCsc3_PCCA'.($i+1)] : "";
            //     $mIngTer[$j]['nComVlr']   += $_POST['nComVlr_PCCA'.($i+1)];
            //     $mIngTer[$j]['nBaseIva']  += $_POST['nBaseIva_PCCA'.($i+1)];
            //     $mIngTer[$j]['nVlrIva']   += $_POST['nVlrIva_PCCA'.($i+1)];
            //   }
            // }

            if ($nSwitch_Find == 0) { // No lo encontro en la matriz para pintar en la factura
              $nInd_mIngTer = count($mIngTer);

              // if (substr_count($_POST['cComObs_PCCA' .($i+1)]," DIAN") > 0 ||
            //     (trim(substr($_POST['cComObs_PCCA'.($i+1)],0,$mComObs_IP)) == "RECIBO OFICIAL DE PAGO TRIBUTOS ADUANEROS Y SANCIO")  ||
              // bstr_count($_POST['cComObs_PCCA' .($i+1)],"DECLARACION") > 0 ) {
              //
              // IngTer[$nInd_mIngTer]['cComObs']  = "DERECHOS DE ADUANA Y COMPLEMENTARIOS";
              // gTer[$nInd_mIngTer]['cTerNom']  = "DIAN";
              // } else {
                  $mIngTer[$nInd_mIngTer]['cComObs']  = trim($vTercero[0]);
                  $mIngTer[$nInd_mIngTer]['cTerNom']  = trim($vTercero[1]);
              // }

              $mIngTer[$nInd_mIngTer]['cComId']   = $_POST['cComId_PCCA'  .($i+1)];
              $mIngTer[$nInd_mIngTer]['cTerId']  = trim($vTercero[2]);

              //Si es Tipo RCM
              if (in_array("{$_POST['cComId3_PCCA'.($i+1)]}~{$_POST['cComCod3_PCCA'.($i+1)]}", $mRCM) == true && $vRCM['comcsc2x'] != "") {
                $mIngTer[$nInd_mIngTer]['cComCsc3'] = $vRCM['comcsc2x'];
              }else{
                $mIngTer[$nInd_mIngTer]['cComCsc3'] = $_POST['cComCsc3_PCCA'.($i+1)];
              }

              $mIngTer[$nInd_mIngTer]['nComVlr']  = $_POST['nComVlr_PCCA' .($i+1)];
              $mIngTer[$nInd_mIngTer]['nBaseIva'] = $_POST['nBaseIva_PCCA'.($i+1)];
              $mIngTer[$nInd_mIngTer]['nVlrIva']  = $_POST['nVlrIva_PCCA'.($i+1)];
            }
        /*}//}else{*/
    }
  }//for ($i=0;$i<$_POST['nSecuencia_PCCA'];$i++) {
  ##Fin Codigo para imprimir los ingresos para terceros ##
  /*echo '<pre>';
  print_r($mIngTer);
  echo '</pre>';
  die();*/
  #Fin Agrupo Ingresos Propios
  //////////////////////////////////////////////////////////////////////////

  class PDF extends FPDF {
    function Header() {
      global $cAlfa;   global $cRoot;   global $cPlesk_Skin_Directory;
      global $gCcoId;  global $gSccId;  global $gMesDes; global $gAnoDes; global $gMesHas; global $gAnoHas;
      global $cUsrNom; global $cCcoDes; global $cScoDes; global $nPag;    global $vAgeDat; global $vCocDat;
      global $vResDat; global $cDocTra; global $cTasCam; global $cDocTra; global $cBultos; global $cPesBru;
      global $cDocId;  global $vCiuDat; global $vDceDat; global $cOpera;  global $nValAdu;  global $vCccDat;
      global $vConDat; global $cPaisOrigen; global $cPedido; global $vNomComercial; global $vCliDat; global $vIdContacto;
      global $cEstiloLetra; global $cAduana; global $vPaiDat; global $vSysStr; global $cTraCom; global $cLinPro; global $cFecLev; 
      global $cManifi; global $cNomVen;


      /*** Impresion Datos Generales Factura ***/
      $nPosY = 18;
      $nPosX = 12.5;

      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/copianovalida.jpg',10,50,190,190);
      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoAndinos2.jpeg',$nPosX,$nPosY-13,25,30);

      $this->SetFont($cEstiloLetra,'',11);
      $this->setXY($nPosX+55,$nPosY-9);
      $this->Cell(77,4,utf8_decode("AGENCIA DE ADUANAS ANDINOS S.A.S NIVEL 1"),0,0,'C');
      $this->SetFont($cEstiloLetra,'',8);
      $this->setXY($nPosX+55,$nPosY-5);
      $this->Cell(77,4,utf8_decode("NIT: {$vSysStr['financiero_nit_agencia_aduanas']}"),0,0,'C');
      $this->setXY($nPosX+55,$nPosY-1);
      $this->Cell(77,4,utf8_decode("IVA RÉGIMEN COMÚN DIAN No 0128"),0,0,'C');

      $this->SetFont($cEstiloLetra,'B',8);
      $this->setXY($nPosX+125,$nPosY);
      $this->Cell(77,4,utf8_decode("OFICINA PRINCIPAL"),0,0,'C');
      $this->setXY($nPosX+120,$nPosY+6);
      $this->Cell(77,4,utf8_decode("Carrera 46 No. 62 - 20 APT 202 - EDIFICIO TOWER 62"),0,0,'C');
      $this->setXY($nPosX+125,$nPosY+9);
      $this->Cell(77,4,utf8_decode("Celular: 3165339963"),0,0,'C');
      $this->setXY($nPosX+125,$nPosY+12);
      $this->Cell(77,4,utf8_decode("Barranquilla - Colombia"),0,0,'C');

      #RESOLUCION
      $this->SetFont($cEstiloLetra,'',6);
      $this->setXY($nPosX,$nPosY+14);
      $this->Cell(77,4,utf8_decode("RESOLUCIÓN DE FACTURA N° {$vResDat['residxxx']}"),0,0,'L');
      $this->setXY($nPosX,$nPosY+17);
      $this->Cell(77,4,utf8_decode("FECHA DEL {$vResDat['resfdexx']} AL {$vResDat['resfhaxx']}"),0,0,'L');
      $this->setXY($nPosX,$nPosY+20);
      $this->Cell(77,4,utf8_decode("DEL {$vResDat['resprexx']}-{$vResDat['resdesxx']} Al {$vResDat['resprexx']}-{$vResDat['reshasxx']}"),0,0,'L');
      $this->setXY($nPosX,$nPosY+23);
      $this->Cell(77,4,utf8_decode("NO SOMOS GRANDES CONTRIBUYENTES CODIGO DE INDUSTRIA Y COMERCIO No 7490. Tarifa ICA 9.66*1000 Para Bogotá"),0,0,'L');
      $this->setXY($nPosX,$nPosY+26);
      $this->Cell(77,4,"Tarifa de Industria y Comercio Cali-Buenaventura 10*1000 Tarifa Industria y Comercio Cartagena 8*1000 - Tarifa Ipiales 7*1000",0,0,'L');


      #SEÑORES 
      $this->Rect($nPosX, $nPosY+30, 120, 20); 
      $this->SetFont($cEstiloLetra,'B',8);
      $this->setXY($nPosX,$nPosY+31);
      $this->Cell(16,4,utf8_decode("Señor(es): "),0,0,'L');
      $this->SetFont($cEstiloLetra,'',8);
      $this->Cell(110,4,utf8_decode("{$vCliDat['CLINOMXX']} "),0,0,'L');
      ///////////////////////////////////////////////////////////////
      #NIT
      $this->setXY($nPosX+16,$nPosY+35);
      $this->Cell(110,4,$_POST['cTerIdInt'],0,0,'L');
      $this->setXY($nPosX+16,$nPosY+39);
      #DIRECCION
      $this->Cell(110,4,$vCliDat['CLIDIRXX'],0,0,'L');
      $this->setXY($nPosX+16,$nPosY+43);
      #CIUDAD
      $this->Cell(110,4,$vCiuDat['CIUDESXX'],0,0,'L');
      #TELEFONO
      $this->setXY($nPosX+16,$nPosY+47);
      $this->Cell(110,3,$vCliDat['CLITELXX'],0,0,'L');

      ///////////////////////////////////////////////

      $this->Rect($nPosX+125, $nPosY+30, 65, 8);
      $this->SetFont($cEstiloLetra,'B',8);
      $this->setXY($nPosX+125,$nPosY+30.5);
      $this->Cell(65,4,utf8_decode("FACTURA DE VENTA"),0,0,'C');
      $this->SetFont($cEstiloLetra,'B',8);
      $this->setXY($nPosX+125,$nPosY+34);
      $this->Cell(35,4,"No. {$vCocDat['resprexx']}",0,0,'R');
      $this->SetFont($cEstiloLetra,'',8);
      $this->Cell(30,4,utf8_decode("{$vCocDat['comcscxx']}"),0,0,'L');

      $this->Rect($nPosX+125, $nPosY+38, 65, 5);
      $this->SetFont($cEstiloLetra,'B',7);
      $this->setXY($nPosX+130,$nPosY+39);
      $this->Cell(35,4,utf8_decode("FECHA FAC."),0,0,'L');
      $this->Cell(35,4,utf8_decode("FECHA VENC."),0,0,'L');

      $this->Rect($nPosX+125, $nPosY+43, 65, 7);
      $this->SetFont($cEstiloLetra,'B',6.5);
      $this->setXY($nPosX+130,$nPosY+43.5);
      $this->Cell(35,3,"DD/MM/AAAA",0,0,'L');
      $this->setXY($nPosX+165,$nPosY+43.5);
      $this->Cell(35,3,"DD/MM/AAAA",0,0,'L');
      #FECHA FACTURA 
      $this->SetFont($cEstiloLetra,'',8);
      $this->setXY($nPosX+130,$nPosY+46.5);
      $this->Cell(35,3,(str_replace("-", "/", $_POST['dRegFCre'])),0,0,'L');
      #FECHA DE VENCIMIENTO
      $this->setXY($nPosX+165,$nPosY+46.5);
      $this->Cell(35,3,(str_replace("-", "/", (date('Y-m-d', strtotime("+{$_POST['cTerPla']} day") )))),0,0,'L');

      ////////////////////////////////////////////////////////
      $this->SetFont($cEstiloLetra,'',8);
      $this->setXY($nPosX+8,$nPosY+51);
      $this->Cell(30,3,"MANIFIESTO:",0,0,'L');
      $this->Cell(80,3,$cManifi,0,0,'L');

      ////////////////////////////////////////////////////////////
      $this->setXY($nPosX+8,$nPosY+55);
      $this->Cell(30,3,"BULTOS:",0,0,'L');
      $this->Cell(80,3,number_format($cBultos, 0,'.','.'),0,0,'L');
      ////////////////////////////////////////////////////////////
      $this->setXY($nPosX+8,$nPosY+59);
      $this->Cell(30,3,"CONTENIDO:",0,0,'L');
      $this->Cell(80,3,$cLinPro,0,0,'L');
      ////////////////////////////////////////////////////////////
      $this->SetFont($cEstiloLetra,'',8);
      $this->setXY($nPosX+8,$nPosY+63);
      $this->Cell(30,3,"PEDIDO:",0,0,'L');
      $this->Cell(80,3,$cPedido,0,0,'L');
      ////////////////////////////////////////////////////////////
      $this->setXY($nPosX+8,$nPosY+67);
      $this->Cell(30,3,"OBSERVACIONES:",0,0,'L');
      $this->Cell(75,3,substr($_POST['cComObs'], 0, 49),0,0,'L');
      ////////////////////////////////////////////////////////////
      $this->SetFont($cEstiloLetra,'',8);
      $this->setXY($nPosX+117,$nPosY+51);
      $this->Cell(33,3,"D.O: ",0,0,'L');
      $this->Cell(27,3,$cDocId,0,0,'L');
      ////////////////////////////////////////////////////////////
      $this->SetFont($cEstiloLetra,'',8);
      $this->setXY($nPosX+117,$nPosY+55);
      $this->Cell(33,3,"NAVIERA: ",0,0,'L');
      $this->MultiCell(27,3,$cTraCom,0,'L');
      ////////////////////////////////////////////////////////////
      $posY_naviera = $this->GetY()+2;
      $this->SetFont($cEstiloLetra,'',8);
      $this->setXY($nPosX+110,$posY_naviera);
      $this->Cell(33,3,"FECHA DE DESPACHO: ",0,0,'L');
      $this->Cell(27,3,str_replace("-", "", $cFecLev),0,0,'L');
      ////////////////////////////////////////////////////////////
      $this->SetFont($cEstiloLetra,'',8);
      $this->setXY($nPosX+110,$posY_naviera+4);
      $this->Cell(10,3,"KILOS: ",0,0,'L');
      $this->Cell(23,3,number_format($cPesBru, 3,'.','.'). " KG" ,0,0,'L');

      ///////////////////////////////////////////////////////////
      $this->SetFont($cEstiloLetra,'',8);
      $this->Cell(8,3,"TRM: ",0,0,'L');
      $this->SetFont($cEstiloLetra,'',8);
      $this->Cell(19,3,number_format($cTasCam, 0,'.','.'),0,0,'L');
      ////////////////////////////////////////////////////////////
      $this->SetFont($cEstiloLetra,'',8);
      $this->setXY($nPosX+110,$posY_naviera+8);
      $this->Cell(18,3,"",0,0,'L');
      $this->SetFont($cEstiloLetra,'',7);
      $this->MultiCell(54,3,"",0,'L');
      // $this->Cell(18,3,"COMERCIAL: ",0,0,'L');
      // $this->SetFont($cEstiloLetra,'',7);
      // $this->MultiCell(54,3,$cNomVen,0,'L');

      $this->nPosY = $this->GetY();
      $this->Rect($nPosX, $nPosY+50, 190, ($this->nPosY)-($nPosY+50));

      //RECTANGULO DEL CONTENIDO
      $this->nPosYFoo = (140 - ($this->nPosY - 88)) + $this->nPosY;
      $this->Rect($nPosX, $this->nPosY, 165, 140 - ($this->nPosY - 88));
      $this->Rect($nPosX+165, $this->nPosY, 25, 140 - ($this->nPosY - 88));

      //Datos de Impresion
      $this->SetFont($cEstiloLetra,'',7);
      $cResoliucion = "IMPRESO POR AGENCIA DE ADUANAS ANDINOS S.A.S. NIVEL 1 NIT.: 860.050.097-1 - OPENCOMEX SOFTWARE";
      $this->RotatedText(204,77,$cResoliucion,270);//14,220
      $this->Rotate(0);

    }//Function Header

    // rota la celda
    function RotatedText($x,$y,$txt,$angle){
      //Text rotated around its origin
      $this->Rotate($angle,$x,$y);
      $this->Text($x,$y,$txt);
      $this->Rotate(0);
    }

    // rota la celda
    var $angle=0;
    function Rotate($angle,$x=-1,$y=-1){
      if($x==-1)
        $x=$this->x;
      if($y==-1)
        $y=$this->y;
      if($this->angle!=0)
        $this->_out('Q');
      $this->angle=$angle;
      if($angle!=0) {
        $angle*=M_PI/180;
        $c=cos($angle);
        $s=sin($angle);
        $cx=$x*$this->k;
        $cy=($this->h-$y)*$this->k;
        $this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
      }
    }

    function Footer() {
      global $cRoot;   global $cPlesk_Skin_Directory;   global $cNomCopia; global $nCopia;
      global $nContPage; global $vCocDat; global $mCodDat; global $cSaldo; global $vResDat;
      global $cEstiloLetra; global $gCorreo; global $nb;

      $nPosY = $this->nPosYFoo;
      $nPosX = 12.5;
      
      $this->Rect($nPosX, $nPosY, 140,28);
      $this->Rect($nPosX+140, $nPosY, 50,28);

      $this->setXY($nPosX,$nPosY+2);
      $this->SetFont($cEstiloLetra,'',8);
      $this->Cell(115,3,utf8_decode("RECIBIDO:"),0,0,'C');

      $this->setXY($nPosX,$nPosY+8);
      $this->SetFont($cEstiloLetra,'',8);
      $this->Cell(22,3,utf8_decode("NOMBRE:"),0,0,'L');
      $this->Line(30,$nPosY+11,90,$nPosY+11);

      $this->setXY($nPosX,$nPosY+13);
      $this->SetFont($cEstiloLetra,'',8);
      $this->Cell(22,3,utf8_decode("CC:"),0,0,'L');
      $this->Line(30,$nPosY+16,90,$nPosY+16);

      $this->setXY($nPosX,$nPosY+18);
      $this->SetFont($cEstiloLetra,'',8);
      $this->Cell(22,3,utf8_decode("FECHA:"),0,0,'L');
      $this->Line(30,$nPosY+21,90,$nPosY+21);

      $this->setXY($nPosX,$nPosY+23);
      $this->SetFont($cEstiloLetra,'',6);
      $this->Cell(80,3,utf8_decode("RECIBI A SATISFACCION ACEPTO LA FACTURA Y ME OBLIGO A PAGARLA"),0,0,'L');

      $this->setXY($nPosX+83,$nPosY+22);
      $this->Cell(25,3,utf8_decode("FIRMA Y SELLO"),0,0,'L');

      $this->Line(120,$nPosY,120,$nPosY+28);
      $this->setXY($nPosX+108.5,$nPosY+2);
      $this->SetFont($cEstiloLetra,'B',8);
      $this->MultiCell(30,3,utf8_decode("La presente factura debe ser cancelada a su vencimiento; de lo contrario se causarán los intererses de mora a las tasas máximas permitidas por la ley"),0,'J');

      $this->Line(157,$nPosY+20,198,$nPosY+20);
      $this->setXY($nPosX+143,$nPosY+23);
      $this->SetFont($cEstiloLetra,'',8);
      $this->Cell(45,3,utf8_decode("FIRMA AUTORIZADA"),0,0,'C');

      $this->setXY($nPosX,$nPosY+28);
      $this->SetFont($cEstiloLetra,'',5.5);
      $this->Cell(190,3,utf8_decode("La presente factura de Venta es un Título Valor, según ley 1231 de julio 17 de 2008, excusando el protesto, el aviso de rechazo y la presentación de pago (*Art. 774 del decreto 410 de 1971: Código de Comercio)"),0,0,'C');

      if ($_POST['cComCod'] == "011" || $_POST['cComCod'] == "012") {
        $this->setXY($nPosX,$nPosY+32);
        $this->SetFont($cEstiloLetra,'',8);
        $this->MultiCell(188,4,utf8_decode("Favor efectuar consignación para recaudos de Cartagena y Barranquilla a la cuenta Bancolombia-Ahorros No.23700005593 y enviar soporte al correo diana.villamil@andinossas.com"),0,'C');
      }
      
      $nPosY = 270;
      $this->SetFont('Arial','B',7);
      $this->setXY($nPosX,$nPosY);
      $this->Cell(190,4,'Pag '.$this->PageNo().'/{nb}',0,0,'C');
      
    }

    function Setwidths($w) {
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
      for($i=0;$i<count($data);$i++)
          $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
      $h=4*$nb;
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
        //$this->Rect($x,$y,$w,$h);
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
  }//class PDF extends FPDF {

  $pdf = new PDF('P','mm','Letter');  //Error al invocar la clase
  $pdf->AddFont($cEstiloLetra,'','arial.php');
  $pdf->AliasNbPages();
  $pdf->SetMargins(0,0,0);
  $pdf->SetAutoPageBreak(0,22);
  $pdf->SetFillColor(229,229,229);
  $pdf->AddPage();

  $nPosY = $pdf->nPosY+2;
  $nPosX = 12.5;
  $nPosFin = 200;
  $nb = 1;
  $pyy = $nPosY;

  // echo "<pre>";
  // print_r($mIngTer);die();
  // $mIngTer = array_merge($mIngTer,$mIngTer);
  // $mIngTer = array_merge($mIngTer,$mIngTer);
  // $mIngTer = array_merge($mIngTer,$mIngTer);

  // $mIngPro = array_merge($mIngPro,$mIngPro);
  // $mIngPro = array_merge($mIngPro,$mIngPro);
  // $mIngPro = array_merge($mIngPro,$mIngPro);


  /*** Imprimo Detalle de Pagos a Terceros e Ingresos Propios ***/
  /*** Imprimo Pagos a Terceros ***/
  if(count($mIngTer) > 0 ){//Si la matriz de Pcc tiene registros
    $nBandPcc = 1;
    $nSubTotPcc = 0;

    $pdf->SetFont($cEstiloLetra,'B',8);

    $pdf->setXY($nPosX+5,$pyy);
    $pdf->Cell(110,6," =GASTOS POR CUENTA DEL CLIENTE - TERCEROS =",0,0,'L');
    $pdf->Cell(70,6,"VALOR",0,0,'R');

    $pyy += 6;
    $pdf->setXY($nPosX+5,$pyy);
    $pdf->SetWidths(array(150,0,37));
    $pdf->SetAligns(array("L","L","R"));

    for($i=0;$i<count($mIngTer);$i++){
      $pyy = $pdf->GetY();
      if($pyy > $nPosFin) {//Validacion para siguiente pagina si se excede espacio de impresion
        // $pdf->Rect($nPosX-2, $nPosY-2, 180, $pyy-($nPosY-4));
        $pdf->AddPage();
        $nb++;
        $nPosY = $pdf->nPosY+2;
        $nPosX = 12.5;
        $pyy = $nPosY;
        $pdf->SetFont($cEstiloLetra,'',7);
        $pdf->setXY($nPosX,$nPosY);
      }//if($pyy > $nPosFin) {

      $nSubTotPcc += $mIngTer[$i]['nComVlr'];
      //$cComObs  = explode("^",$mIngTer[$i][2]);

      if($mIngTer[$i]['cComCsc3'] != ""){
        //$cComObsv = str_replace($mIngTer[$i]['cTerNom'], '', $mIngTer[$i]['cComObs'])." ".$mIngTer[$i]['cTerNom'].' FV '.$mIngTer[$i]['cComCsc3'];
        $cConDes = $mIngTer[$i]['cComObs'].' Fact No '.$mIngTer[$i]['cComCsc3'];
      }else{
        $cConDes = $mIngTer[$i]['cComObs'];
      }

      $pdf->SetFont($cEstiloLetra,'',7);
      $pdf->setX($nPosX+3);
      $pdf->Row(array(trim($cConDes),'','$ '.number_format($mIngTer[$i]['nComVlr'],0,',','.')));
    }//for($i=0;$i<count($mIngTer);$i++){

    $pyy = $pdf->GetY();
    if($pyy > $nPosFin) {//Validacion para siguiente pagina si se excede espacio de impresion
      // $pdf->Rect($nPosX-2, $nPosY-2, 180, $pyy-($nPosY-4));
      $pdf->AddPage();
      $nb++;
      $nPosY = $pdf->nPosY+2;
      $nPosX = 12.5;
      $pyy = $nPosY;
      $pdf->SetFont($cEstiloLetra,'',8);
      $pdf->setXY($nPosX,$nPosY);
    } //if($pyy > $nPosFin) {

    /*** Recorro la matriz $mValores para los pagos 4xmil ***/
    $nSubToPcc = 0;
    $nSubToPccIva =0;

    for ($i=0;$i<count($mValores);$i++) {
      $pyy = $pdf->GetY();
      if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
        // $pdf->Rect($nPosX-2, $nPosY-2, 180, $pyy-($nPosY-4));
        $pdf->AddPage();
        $nb++;
        $nPosY = $pdf->nPosY+2;
        $nPosX = 12.5;
        $pyy = $nPosY;
        $pdf->SetFont($cEstiloLetra,'',7);
        $pdf->setXY($nPosX,$nPosY);
      }//if($nPosY < 130){//Validacion para siguiente pagina si se excede espacio de impresion

      $nSubToPcc += $mValores[$i]['comvlrxx'];
      $pdf->SetFont($cEstiloLetra,'',7);
      $pdf->setX($nPosX+3);
      $pdf->Row(array(trim($mValores[$i]['comobsxx']),'','$ '.number_format($mValores[$i]['comvlrxx'],0,',',',')));
    }//for ($i=0;$i<count($mValores);$i++) {


    $pyy = $pdf->GetY();
    if($pyy > $nPosFin) {//Validacion para siguiente pagina si se excede espacio de impresion
      // $pdf->Rect($nPosX-2, $nPosY-2, 180, $pyy-($nPosY-4));
      $pdf->AddPage();
      $nb++;
      $nPosY = $pdf->nPosY+2;
      $nPosX = 12.5;
      $pyy = $nPosY;
      $pdf->SetFont($cEstiloLetra,'',8);
      $pdf->setXY($nPosX,$nPosY);
    } //if($pyy > $nPosFin) {
  }//if(count($mIngTer) > 0 || $nBandPcc == 1){//Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
  /*** Fin Imprimo Pagos a Terceros ***/

  $nSubToIP = 0;
  $nSubToIPIva = 0;

  if($_POST['nSecuencia_IPA'] > 0 || count($mIngPro) > 0) { //Valido si la Secuencia de la Grilla de Ip viene mayor a cero para imprimir bloque de INGRESOS PROPIOS
    if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
      $pdf->AddPage();
      $nb++;
      $nPosY = $pdf->nPosY+2;
      $nPosX = 12.5;
      $pyy = $nPosY;
      $nPosYRec = $pyy;
      $pdf->SetFont($cEstiloLetra,'',7);
      $pdf->setXY($nPosX,$nPosY);
    }//if($nPosY < 130){//Validacion para siguiente pagina si se excede espacio de impresion

    $pdf->setXY($nPosX+5,$pyy);
    $pdf->SetFont($cEstiloLetra,'B',8);
    $pdf->Cell(110,6,utf8_decode("=LIQUIDACIÓN -INGRESOS PROPIOS="),0,0,'L');
    if ($nBandPcc != 1){
      $pdf->Cell(70,6,"VALOR",0,0,'R');
    }
    $pyy += 2;

    /*** Imprimo Ingresos Propios ***/
    $pdf->SetWidths(array(150,0,37));
    $pdf->SetAligns(array("L","L","R"));
    $pdf->SetFont($cEstiloLetra,'',8);

    // hago dos veces el mismo for para separar los ingresos GRAVADOS y luego los NO GRAVADOS
    $nPosicion = 0;

    // OJO: hago dos veces el mismo for para separar los ingresos GRAVADOS y luego los NO GRAVADOS
    for($k=0;$k<(count($mIngPro));$k++) {
      if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
        // $pdf->Rect($nPosX-2, $nPosYRec-2, 180, $pyy-($nPosYRec-4));
        $pdf->AddPage();
        $nb++;
        $nPosY = $pdf->nPosY+2;
        $nPosX = 12.5;
        $pyy = $nPosY;
        $nPosYRec = $pyy;
        $pdf->SetFont($cEstiloLetra,'',7);
        $pdf->setXY($nPosX,$nPosY);
      }

      if( $mIngPro[$k]['comvlr01'] != 0 ) {
        $nPosicion++;
        $nSubToIP += $mIngPro[$k]['comvlrxx'];
        $nSubToIPIva += $mIngPro[$k]['comvlr01'];
        $pdf->SetFont($cEstiloLetra,'',7);

        $cValor = "";
        if($mIngPro[$k]['comfobxx'] == "SI" && $mIngPro[$k]['docfobxx'] > 0) {
          $cValor  = " FOB: ($".number_format($mIngPro[$k]['docfobxx'],2,'.',',');
          $cValor .= ($mIngPro[$k]['doctrmxx'] > 0) ? " TRM: $".number_format($mIngPro[$k]['doctrmxx'],2,'.',',') : "";
          $cValor .= ")]";
        }
        if ($mIngPro[$k]['comcifap'] == "SI"){
          $cValor = " CIF: $(".number_format($mIngPro[$k]['comcifxx'],0,'.',',').')';
        }

        $nCantidad = 1;
        if ($mIngPro[$k]['comdimap'] == "SI"){
          // $cValor = " DIM: (".number_format($mIngPro[$k]['comdimxx'],0,'.',',').')';
          $nCantidad = $mIngPro[$k]['comdimxx'];
        }
        if ($mIngPro[$k]['comdavap'] == "SI"){
          // $cValor = " DAV: (".number_format($mIngPro[$k]['comdavxx'],0,'.',',').')';
          $nCantidad = $mIngPro[$k]['comdavxx'];
        }
        if ($mIngPro[$k]['comvucap'] == "SI"){
          // $cValor = " VUCE: (".number_format($mIngPro[$k]['comvucxx'],0,'.',',').")";
          $nCantidad = $mIngPro[$k]['comvucxx'];
        }
        if ($mIngPro[$k]['comcerap'] == "SI"){
          // $cValor = " CERTIFICADOS DE ORIGEN: (".number_format($mIngPro[$k]['comcerxx'],0,'.',',').")";
          $nCantidad = $mIngPro[$k]['comcerxx'];
        }
        if ($mIngPro[$k]['comhorap'] == "SI"){
          // $cValor = " HORAS: (".number_format($mIngPro[$k]['comhorxx'],0,'.',',').")";
          $nCantidad = $mIngPro[$k]['comhorxx'];
        }
        if ($mIngPro[$k]['compieap'] == "SI"){
          // $cValor = " PIEZAS: (".number_format($mIngPro[$k]['compiexx'],0,'.',',').")";
          $nCantidad = $mIngPro[$k]['compiexx'];
        }
        if ($mIngPro[$k]['comdexap'] == "SI"){
          // $cValor = " DEX: (".number_format($mIngPro[$k]['comdexxx'],0,'.',',').')';
          $nCantidad = $mIngPro[$k]['comdexxx'];
        }
        if ($mIngPro[$k]['comserap'] == "SI"){
          // $cValor = " SERIAL: (".number_format($mIngPro[$k]['comserxx'],0,'.',',').')';
          $nCantidad = $mIngPro[$k]['comserxx'];
        }
        if ($mIngPro[$k]['comaraap'] == "SI"){
          // $cValor = " CANT.: (".number_format($mIngPro[$k]['comaraxx'],0,'.',',').')';
          $nCantidad = $mIngPro[$k]['comaraxx'];
        }
        if ($mIngPro[$k]['comdtaap'] == "SI"){
          // $cValor = " DTA: (".number_format($mIngPro[$k]['comdtaxx'],0,'.',',').')';
          $nCantidad = $mIngPro[$k]['comdtaxx'];
        }
        if ($mIngPro[$k]['comiteap'] == "SI"){
          // $cValor = " ITEMS: (".number_format($mIngPro[$k]['comitexx'],0,'.',',').')';
          $nCantidad = $mIngPro[$k]['comitexx'];
        }
        if ($mIngPro[$k]['comcanap'] == "SI"){
          // $cValor = " CANTIDAD: (".number_format($mIngPro[$k]['comcanxx'],0,'.',',').')';
          $nCantidad = $mIngPro[$k]['comcanxx'];
        }
        if ($mIngPro[$k]['comfobap'] == "SI"){
          $cValor = " FOB: ($".number_format($mIngPro[$k]['comfob2x'],0,'.',',').')';
        }
        if ($mIngPro[$k]['comc20ap'] == "SI" || $mIngPro[$k]['comc40ap'] == "SI" || $mIngPro[$k]['comcsuap'] == "SI"){
          $cValor = "";
          if($mIngPro[$k]['comc20ap'] == "SI"){
            $cValor .= " CONTENEDORES DE 20: (".number_format($mIngPro[$k]['comc20xx'],0,'.',',').')';
          }
          if($mIngPro[$k]['comc40ap'] == "SI"){
            $cValor .= " CONTENEDORES DE 40: (".number_format($mIngPro[$k]['comc40xx'],0,'.',',').')';
          }
          if($mIngPro[$k]['comcsuap'] == "SI"){
            $cValor .= " UNIDADES DE CARGA SUELTA: (".number_format($mIngPro[$k]['comcsuxx'],0,'.',',').')';
          }
        }
        

        $pyy += 4;
        $pdf->SetFont($cEstiloLetra,'',7);
        $pdf->setXY($nPosX+3, $pyy);
        $pdf->Row(array(trim($mIngPro[$k]['comobsxx'].$cValor).( (empty($nCantidad)) ? $nCantidad : ""),'',
                  '$ '.number_format($mIngPro[$k]['comvlrxx'],0,',','.')));

      }
    }

    for($k=0;$k<(count($mIngPro));$k++) {
      if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
        // $pdf->Rect($nPosX-2, $nPosYRec-2, 180, $pyy-($nPosYRec-4));
        $pdf->AddPage();
        $nb++;
        $nPosY = $pdf->nPosY+2;
        $nPosX = 12.5;
        $pyy = $nPosY;
        $nPosYRec = $pyy;
        $pdf->SetFont($cEstiloLetra,'',7);
        $pdf->setXY($nPosX,$nPosY);
      }

      if( $mIngPro[$k]['comvlr01'] == 0 ) {
        $nPosicion++;
        $nSubToIP    += $mIngPro[$k]['comvlrxx'];
        $nSubToIPIva += $mIngPro[$k]['comvlr01'];
        $pdf->SetFont($cEstiloLetra,'',7);

        $cValor = "";
        if($mIngPro[$k]['comfobxx'] == "SI" && $mIngPro[$k]['docfobxx'] > 0) {
          $cValor  = " FOB: ($".number_format($mIngPro[$k]['docfobxx'],2,'.',',');
          $cValor .= ($mIngPro[$k]['doctrmxx'] > 0) ? " TRM: $".number_format($mIngPro[$k]['doctrmxx'],2,'.',',') : "";
          $cValor .= ")]";
        }
        if ($mIngPro[$k]['comcifap'] == "SI"){
          $cValor = " CIF: $(".number_format($mIngPro[$k]['comcifxx'],0,'.',',').')';
        }
        $nCantidad = 1;
        if ($mIngPro[$k]['comdimap'] == "SI"){
          // $cValor = " DIM: (".number_format($mIngPro[$k]['comdimxx'],0,'.',',').')';
          $nCantidad = $mIngPro[$k]['comdimxx'];
        }
        if ($mIngPro[$k]['comdavap'] == "SI"){
          // $cValor = " DAV: (".number_format($mIngPro[$k]['comdavxx'],0,'.',',').')';
          $nCantidad = $mIngPro[$k]['comdavxx'];
        }
        if ($mIngPro[$k]['comvucap'] == "SI"){
          // $cValor = " VUCE: (".number_format($mIngPro[$k]['comvucxx'],0,'.',',').")";
          $nCantidad = $mIngPro[$k]['comvucxx'];
        }
        if ($mIngPro[$k]['comcerap'] == "SI"){
          // $cValor = " CERTIFICADOS DE ORIGEN: (".number_format($mIngPro[$k]['comcerxx'],0,'.',',').")";
          $nCantidad = $mIngPro[$k]['comcerxx'];
        }
        if ($mIngPro[$k]['comhorap'] == "SI"){
          // $cValor = " HORAS: (".number_format($mIngPro[$k]['comhorxx'],0,'.',',').")";
          $nCantidad = $mIngPro[$k]['comhorxx'];
        }
        if ($mIngPro[$k]['compieap'] == "SI"){
          // $cValor = " PIEZAS: (".number_format($mIngPro[$k]['compiexx'],0,'.',',').")";
          $nCantidad = $mIngPro[$k]['compiexx'];
        }
        if ($mIngPro[$k]['comdexap'] == "SI"){
          // $cValor = " DEX: (".number_format($mIngPro[$k]['comdexxx'],0,'.',',').')';
          $nCantidad = $mIngPro[$k]['comdexxx'];
        }
        if ($mIngPro[$k]['comserap'] == "SI"){
          // $cValor = " SERIAL: (".number_format($mIngPro[$k]['comserxx'],0,'.',',').')';
          $nCantidad = $mIngPro[$k]['comserxx'];
        }
        if ($mIngPro[$k]['comaraap'] == "SI"){
          // $cValor = " CANT.: (".number_format($mIngPro[$k]['comaraxx'],0,'.',',').')';
          $nCantidad = $mIngPro[$k]['comaraxx'];
        }
        if ($mIngPro[$k]['comdtaap'] == "SI"){
          // $cValor = " DTA: (".number_format($mIngPro[$k]['comdtaxx'],0,'.',',').')';
          $nCantidad = $mIngPro[$k]['comdtaxx'];
        }
        if ($mIngPro[$k]['comiteap'] == "SI"){
          // $cValor = " ITEMS: (".number_format($mIngPro[$k]['comitexx'],0,'.',',').')';
          $nCantidad = $mIngPro[$k]['comitexx'];
        }
        if ($mIngPro[$k]['comcanap'] == "SI"){
          // $cValor = " CANTIDAD: (".number_format($mIngPro[$k]['comcanxx'],0,'.',',').')';
          $nCantidad = $mIngPro[$k]['comcanxx'];
        }
        if ($mIngPro[$k]['comfobap'] == "SI"){
          $cValor = " FOB: ($".number_format($mIngPro[$k]['comfob2x'],0,'.',',').')';
        }
        if ($mIngPro[$k]['comc20ap'] == "SI" || $mIngPro[$k]['comc40ap'] == "SI" || $mIngPro[$k]['comcsuap'] == "SI"){
          $cValor = "";
          if($mIngPro[$k]['comc20ap'] == "SI"){
            $cValor .= " CONTENEDORES DE 20: (".number_format($mIngPro[$k]['comc20xx'],0,'.',',').')';
          }
          if($mIngPro[$k]['comc40ap'] == "SI"){
            $cValor .= " CONTENEDORES DE 40: (".number_format($mIngPro[$k]['comc40xx'],0,'.',',').')';
          }
          if($mIngPro[$k]['comcsuap'] == "SI"){
            $cValor .= " UNIDADES DE CARGA SUELTA: (".number_format($mIngPro[$k]['comcsuxx'],0,'.',',').')';
          }
        }

        $pyy += 4;
        $pdf->SetFont($cEstiloLetra,'',7);
        $pdf->setXY($nPosX+3, $pyy);
        $pdf->Row(array(trim($mIngPro[$k]['comobsxx'].$cValor).( (empty($nCantidad)) ? $nCantidad : ""),'',
                  '$ '.number_format($mIngPro[$k]['comvlrxx'],0,',','.')));


      }
    }
    /*** Fin Imprimo Ingresos Propios ***/

  }//if($nBandIP == 1){//Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS
  ##Fin Imprimo Detalle de Pagos a Terceros e Ingresos Propios ##

  $pyy = $pdf->GetY();
  if($pyy > 203){//Validacion para siguiente pagina si se excede espacio de impresion
    // $pdf->Rect($nPosX-2, $nPosYRec-2, 180, $pyy-($nPosYRec-4));
    $pdf->AddPage();
    $nb++;
    $nPosY = $pdf->nPosY+2;
    $nPosX = 12.5;
    $pyy = $nPosY;
    $nPosYRec = $pyy;
    $pdf->SetFont($cEstiloLetra,'',7);
    $pdf->setXY($nPosX,$nPosY);
  }//if($nPosY < 130){//Validacion para siguiente pagina si se excede espacio de impresion



  #SUBTOTAL
  $nSubtotal = $nSubToIP + $nSubToPcc + $nSubTotPcc;

  
  // $nTotIva = 0;
  // ##Busco Valor de RET.IVA ##
  // if($mCodDat[$k]['comctocx'] == 'RETIVA'){
  //   $nTotIva += $mCodDat[$k]['comvlrxx'];
  // }
  // ##Fin Busco Valor de RET.IVA ##

  // $nIva=0;
  // ##Busco valor de IVA ##
  // if($mCodDat[$k]['comctocx'] == 'IVAIP'){
  //   $nIva += $mCodDat[$k]['comvlrxx'];
  // }
  // ##Fin Busco Valor de IVA ##

##Bloque que acumula retenciones por valor de porcentaje##
  $mReteFte = array();
	$mRetIca  = array();
	$mRetIva  = array();
  $nTotRet 	= 0;
  for ($i=0;$i<$_POST['nSecuencia_IPA'];$i++) {
    if ($_POST['nPorFte_IPA'.($i+1)] > 0) {
      $nSwitch_Encontre_RetFte = 0;
      for ($j=0;$j<count($mReteFte);$j++) {
        if ($_POST['nPorFte_IPA'.($i+1)] == $mReteFte[$j]['pucretxx']) {
          $nTotRet += ($vCliDat['CLINRPXX'] == "SI") ? round($_POST['nVlrFte_IPA'.($i+1)],2) : round($_POST['nVlrFte_IPA'.($i+1)],0);
          $nSwitch_Encontre_RetFte = 1;
          $mReteFte[$j]['comvlrxx']  += ($vCliDat['CLINRPXX'] == "SI") ? round($_POST['nVlrFte_IPA'.($i+1)],2) : round($_POST['nVlrFte_IPA'.($i+1)],0);
          $mReteFte[$j]['basexxxx']  += ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
        }
      }

      if ($nSwitch_Encontre_RetFte == 0) { // No lo encontro en la matriz para pintar en la factura
        $nTotRet += ($vCliDat['CLINRPXX'] == "SI") ? round($_POST['nVlrFte_IPA' .($i+1)],2) : round($_POST['nVlrFte_IPA'.($i+1)],0);
        $nInd_mReteFte = count($mReteFte);
        $mReteFte[$nInd_mReteFte]['tipretxx'] = "FUENTE";
        $mReteFte[$nInd_mReteFte]['pucretxx'] = $_POST['nPorFte_IPA' .($i+1)];
        $mReteFte[$nInd_mReteFte]['comvlrxx'] = ($vCliDat['CLINRPXX'] == "SI") ? round($_POST['nVlrFte_IPA' .($i+1)],2) : round($_POST['nVlrFte_IPA'.($i+1)],0);
        $mReteFte[$nInd_mReteFte]['basexxxx'] = ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
      }
    }

		if ($_POST['nPorIca_IPA'.($i+1)] > 0) {
      $nSwitch_Encontre_RetIca = 0;
      for ($j=0;$j<count($mRetIca);$j++) {
        if ($_POST['nPorIca_IPA'.($i+1)] == $mRetIca[$j]['pucretxx']) {
          $nSwitch_Encontre_RetIca = 1;
          $mRetIca[$j]['comvlrxx']  += ($vCliDat['CLINRPXX'] == "SI") ? round($_POST['nVlrIca_IPA'.($i+1)],2) : round($_POST['nVlrIca_IPA'.($i+1)],0);
          $mRetIca[$j]['basexxxx']  += ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
        }
      }

      if ($nSwitch_Encontre_RetIca == 0) { // No lo encontro en la matriz para pintar en la factura
        $nInd_mRetIca = count($mRetIca);
        $mRetIca[$nInd_mRetIca]['tipretxx'] = "ICA";
        $mRetIca[$nInd_mRetIca]['pucretxx'] = $_POST['nPorIca_IPA' .($i+1)];
        $mRetIca[$nInd_mRetIca]['comvlrxx'] = ($vCliDat['CLINRPXX'] == "SI") ? round($_POST['nVlrIca_IPA' .($i+1)],2) : round($_POST['nVlrIca_IPA'.($i+1)],0);
        $mRetIca[$nInd_mRetIca]['basexxxx'] = ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
      }
    }

		if ($_POST['nPorIva_IPA'.($i+1)] > 0) {
      $nSwitch_Encontre_RetIva = 0;
      for ($j=0;$j<count($mRetIva);$j++) {
        if ($_POST['nPorIva_IPA'.($i+1)] == $mRetIva[$j]['pucretxx']) {
          $nSwitch_Encontre_RetIva = 1;
          $mRetIva[$j]['comvlrxx']  += ($vCliDat['CLINRPXX'] == "SI") ? round($_POST['nVlrIva_IPA'.($i+1)],2) : round($_POST['nVlrIva_IPA'.($i+1)],0);
          $mRetIva[$j]['basexxxx']  += ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
        }
      }

      if ($nSwitch_Encontre_RetIva == 0) { // No lo encontro en la matriz para pintar en la factura
        $nInd_mRetIva = count($mRetIva);
        $mRetIva[$nInd_mRetIva]['tipretxx'] = "IVA";
        $mRetIva[$nInd_mRetIva]['pucretxx'] = $_POST['nPorIva_IPA' .($i+1)];
        $mRetIva[$nInd_mRetIva]['comvlrxx'] = ($vCliDat['CLINRPXX'] == "SI") ? round($_POST['nVlrIva_IPA' .($i+1)],2) : round($_POST['nVlrIva_IPA'.($i+1)],0);
        $mRetIva[$nInd_mRetIva]['basexxxx'] = ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
      }
    }
  }
  ##Fin Bloque que acumula retenciones por valor de porcentaje##

  /*** Busco Valor a Pagar ***/
  if($_POST['nIPASal'] > 0){
    $cSaldo = "A CARGO";
  } else {
    $cSaldo = "A SU FAVOR";
  }

  $pdf->setXY($nPosX,209);
  $pdf->SetFont($cEstiloLetra,'B',8);
  $pdf->Cell(43,4,"FAVOR ABSTENERSE DE APLICAR RETENCION SOBRE LOS PAGOS A TERCEROS",0,0,'L');

  #TOTAL
  // $nTotPag = $nSubtotal - $_POST['nIPAAnt'] + $nSubToIPIva - $nTotRet;
  $nTotPag = abs($_POST['nIPASal']);

  /*** Fin Busco Valor a Pagar ***/
  $pdf->setXY($nPosX,215);
  $pdf->SetFont($cEstiloLetra,'B',7);
  $pdf->Cell(43,4,"SON:",0,0,'L');
  $pdf->SetFont($cEstiloLetra,'B',7);
  $nTotPag1 = f_Cifra_Php(str_replace("-","",abs($nTotPag)),'PESO');
  if($nTotPag1 <> ""){
    $nImpValor = explode("~",f_Words($nTotPag1,136));
    $pyy = 215;
    for ($n=0;$n<count($nImpValor);$n++) {
      $pdf->setXY(20,$pyy);
      $pdf->Cell(135,4,$nImpValor[$n],0,0,'L');
      $pyy += 4;
    }
  }else{
    $pdf->setXY(20,215);
    $pdf->Cell(135,4,"",0,0,'L');
  }

  $nPosX = $nPosX+140;
  $pyy = 204;

  $pdf->setXY($nPosX,$pyy);
  $pdf->SetFont($cEstiloLetra,'',7);
  $pdf->Cell(25,4,"SUBTOTAL",1,0,'L');
  $pdf->Cell(25,4,"",1,0,'R');
  $pdf->setXY($nPosX+25,$pyy);
  $pdf->Cell(22,4,"$ ".number_format($nSubtotal,0,',',','),0,0,'R');
  ////////////////////////////////////
  $pyy += 4;
  $pdf->setXY($nPosX,$pyy);
  $pdf->SetFont($cEstiloLetra,'',7);
  $pdf->Cell(25,4,"ANTICIPOS",1,0,'L');
  $pdf->Cell(25,4,"",1,0,'R');
  $pdf->setXY($nPosX+25,$pyy);
  $pdf->Cell(22,4,"$ ".number_format(abs($_POST['nIPAAnt']),0,',',','),0,0,'R');

  ////////////////////////////////////
  $pyy += 4;
  $pdf->setXY($nPosX,$pyy);
  $pdf->SetFont($cEstiloLetra,'',7);
  $pdf->Cell(25,4,"IVA 19%",1,0,'L');
  $pdf->Cell(25,4,"",1,0,'R');
  $pdf->setXY($nPosX+25,$pyy);
  $pdf->Cell(22,4,"$ ".number_format($nSubToIPIva,0,',',','),0,0,'R');

  $nRetFte4 = 0;
  $nRetFte11 = 0;
  foreach ($mReteFte as  $mRetencion) {
    ////////////////////////////////////

    if ((int)$mRetencion['pucretxx'] == 11) {
      $nRetFte11 = $mRetencion['comvlrxx'];
    } else if ((int)$mRetencion['pucretxx'] == 4) {
      $nRetFte4 = $mRetencion['comvlrxx'];
    }
  }

	$nRetIca9 = 0;
	$nRetIca6 = 0;
	foreach ($mRetIca as  $reteIca) {
		if ($reteIca['pucretxx'] == '0.966') {
			$nRetIca9 += $reteIca['comvlrxx'];
		} else if ($reteIca['pucretxx'] == '0.690') {
			$nRetIca6 += $reteIca['comvlrxx'];
		}
	}

	$nRetIva = 0;
	foreach ($mRetIva as  $mReteIva) {
		$nRetIva += $mReteIva['comvlrxx'];
	}

  ////////////////////////////////////
  $pyy += 4;
  $pdf->setXY($nPosX,$pyy);
  $pdf->SetFont($cEstiloLetra,'',7);
  $pdf->Cell(25,4,"Retefuente 4%",1,0,'L');
  $pdf->Cell(25,4,"",1,0,'R');
  $pdf->setXY($nPosX+25,$pyy);
  $pdf->Cell(22,4,"$ ".number_format($nRetFte4,0,',',','),0,0,'R');

  ////////////////////////////////////
  $pyy += 4;
  $pdf->setXY($nPosX,$pyy);
  $pdf->SetFont($cEstiloLetra,'',7);
  $pdf->Cell(25,4,"Retefuente 11%",1,0,'L');
  $pdf->Cell(25,4,"",1,0,'R');
  $pdf->setXY($nPosX+25,$pyy);
  $pdf->Cell(22,4,"$ ".number_format($nRetFte11,0,',',','),0,0,'R');

	////////////////////////////////////
  $pyy += 4;
  $pdf->setXY($nPosX,$pyy);
  $pdf->SetFont($cEstiloLetra,'',7);
  $pdf->Cell(25,4,"ReteICA 9,66%",1,0,'L');
  $pdf->Cell(25,4,"",1,0,'R');
  $pdf->setXY($nPosX+25,$pyy);
  $pdf->Cell(22,4,"$ ".number_format($nRetIca9,0,',',','),0,0,'R');

	////////////////////////////////////
  $pyy += 4;
  $pdf->setXY($nPosX,$pyy);
  $pdf->SetFont($cEstiloLetra,'',7);
  $pdf->Cell(25,4,"ReteICA 6,9%",1,0,'L');
  $pdf->Cell(25,4,"",1,0,'R');
  $pdf->setXY($nPosX+25,$pyy);
  $pdf->Cell(22,4,"$ ".number_format($nRetIca6,0,',',','),0,0,'R');

  ////////////////////////////////////
  $pyy += 4;
  $pdf->setXY($nPosX,$pyy);
  $pdf->SetFont($cEstiloLetra,'',7);
  $pdf->Cell(25,4,"ReteIVA",1,0,'L');
  $pdf->Cell(25,4,"",1,0,'R');
  $pdf->setXY($nPosX+25,$pyy);
  $pdf->Cell(22,4,"$ ".number_format($nRetIva,0,',',','),0,0,'R');

  ////////////////////////////////////
  $pyy += 4;
  $pdf->setXY($nPosX,$pyy);
  $pdf->SetFont($cEstiloLetra,'B',7);
  $pdf->Cell(25,4,"TOTAL".$cSaldo,1,0,'C');
  $pdf->Cell(25,4,"",1,0,'R');
  $pdf->setXY($nPosX+25,$pyy);
  $pdf->Cell(22,4,"$ ".number_format($nTotPag,0,',',','),0,0,'R');

  $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
  $pdf->Output($cFile);

  if (file_exists($cFile)){
    chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
  } else {
    f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
  }
  echo "<html><script>document.location='$cFile';</script></html>";

  //$pdf->Output();
?>

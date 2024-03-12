<?php
  /**
   * Imprime Vista Previa Factura de Venta Siaco.
   * --- Descripcion: Permite Imprimir Vista Previa de la Factura de Venta.
   * @author Andres Benavides <andres.benavides@opentecnologia.com.co>
   */
  include("../../../../libs/php/utility.php");
  include("../../../../libs/php/utiliqdo.php");

  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");

  $cRoot = $_SERVER['DOCUMENT_ROOT'];

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

  ##Reconstruyendo POST de DOS, pagos a terceros e ingresos propios
  $qDatDo  = "SELECT * ";
  $qDatDo .= "FROM $cAlfa.$cTabla_DOS ";
  $qDatDo .= "WHERE ";
  $qDatDo .= "cUsrId_DOS = \"{$_COOKIE['kUsrId']}\" AND ";
  $qDatDo .= "cFacId_DOS = \"{$_POST['cFacId']}\" ";
  $qDatDo .= "ORDER BY ABS(cGrid_DOS), ABS(cSeq_DOS) ";
  $xDatDo  = f_MySql("SELECT","",$qDatDo,$xConexion01,"");
  //f_Mensaje(__FILE__,__LINE__,$qDatDo."~".mysql_num_rows($xDatDo));
  $vCampos = array();
  while($xRC = mysql_fetch_field($xDatDo)){
    $vCampos[] = $xRC->name;
  }
  //Armando Matriz de DOs
  while ($xRDD = mysql_fetch_assoc($xDatDo)) {
    for($n=0; $n<count($vCampos); $n++) {
      if ($xRDD['cGrid_DOS'] != "") {
        if ($xRDD['cGrid_DOS'] == $_POST['nOrden_Activo']) {
          $nGrilla = str_replace("_DOS", "_Ord_".$xRDD['cGrid_DOS']."_DOS", $vCampos[$n]);
          $_POST[$nGrilla.($xRDD['cSeq_DOS']+0)] = $xRDD[$vCampos[$n]];
        }
      } else {
        $_POST[$vCampos[$n].($xRDD['cSeq_DOS']+0)] = $xRDD[$vCampos[$n]];
      }
    }
  }

  $qPCCA  = "SELECT * ";
  $qPCCA .= "FROM $cAlfa.$cTabla_PCCA ";
  $qPCCA .= "WHERE ";
  $qPCCA .= "cUsrId_PCCA = \"{$_COOKIE['kUsrId']}\" AND ";
  $qPCCA .= "cFacId_PCCA = \"{$_POST['cFacId']}\" ";
  $qPCCA .= "ORDER BY ABS(cComGrid_PCCA), ABS(cComSeq_PCCA) ";
  $xPCCA  = f_MySql("SELECT","",$qPCCA,$xConexion01,"");
  //f_Mensaje(__FILE__,__LINE__,$qPCCA."~".mysql_num_rows($xPCCA));
  $vCampos = array();
  while($xRC = mysql_fetch_field($xPCCA)){
    $vCampos[] = $xRC->name;
  }
  //Armando Matriz de DOs
  while ($xRP = mysql_fetch_array($xPCCA)) {
    for($n=0; $n<count($vCampos); $n++) {
      if ($xRP['cComGrid_PCCA'] != "") {
        if ($xRP['cComGrid_PCCA'] == $_POST['nOrden_Activo']) {
          $nGrilla = str_replace("_PCCA", "_Ord_".$xRP['cComGrid_PCCA']."_PCCA", $vCampos[$n]);
          $_POST[$nGrilla.($xRP['cComSeq_PCCA']+0)] = $xRP[$vCampos[$n]];
        }
      } else {
        $_POST[$vCampos[$n].($xRP['cComSeq_PCCA']+0)] = $xRP[$vCampos[$n]];
      }
    }
  }

  $qIPA  = "SELECT * ";
  $qIPA .= "FROM $cAlfa.$cTabla_IPA ";
  $qIPA .= "WHERE ";
  $qIPA .= "cUsrId_IPA = \"{$_COOKIE['kUsrId']}\" AND ";
  $qIPA .= "cFacId_IPA = \"{$_POST['cFacId']}\" ";
  $qIPA .= "ORDER BY ABS(cComGrid_IPA), ABS(cComSeq_IPA) ";
  $xIPA  = f_MySql("SELECT","",$qIPA,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qIPA."~".mysql_num_rows($xIPA));
  $vCampos = array();
  while($xRC = mysql_fetch_field($xIPA)){
    $vCampos[] = $xRC->name;
  }
  //Armando Matriz de DOs
  while ($xRI = mysql_fetch_array($xIPA)) {
    for($n=0; $n<count($vCampos); $n++) {
      if ($xRI['cComGrid_IPA'] != "") {
        if ($xRI['cComGrid_IPA'] == $_POST['nOrden_Activo']) {
          $nGrilla = str_replace("_IPA", "_Ord_".$xRI['cComGrid_IPA']."_IPA", $vCampos[$n]);
          $_POST[$nGrilla.($xRI['cComSeq_IPA']+0)] = $xRI[$vCampos[$n]];
        }
      } else {
        $_POST[$vCampos[$n].($xRI['cComSeq_IPA']+0)] = $xRI[$vCampos[$n]];
      }
    }
  }

  if ($_POST['nOrden_Activo'] != "") {
    $qORD  = "SELECT * ";
    $qORD .= "FROM $cAlfa.$cTabla_ORD ";
    $qORD .= "WHERE ";
    $qORD .= "cUsrId_ORD = \"{$_COOKIE['kUsrId']}\" AND ";
    $qORD .= "cFacId_ORD = \"{$_POST['cFacId']}\" ";
    $qORD .= "ORDER BY ABS(cSeq_ORD) ";
    $xORD  = f_MySql("SELECT","",$qORD,$xConexion01,"");
    // f_Mensaje(__FILE__,__LINE__,$qORD."~".mysql_num_rows($xORD));
    $vCampos = array();
    while($xRC = mysql_fetch_field($xORD)){
      $vCampos[] = $xRC->name;
    } 
    //Armando Matriz de DOs
    while ($xRO = mysql_fetch_array($xORD)) {
      for($n=0; $n<count($vCampos); $n++) {
        if ($xRO['cId_ORD'] != "") {
          $_POST[$vCampos[$n].($xRO['cSeq_ORD']+0)] = $xRO[$vCampos[$n]];
        }
      }
    }
  }

  $nGrid    = (isset($_POST['nOrden_Activo'])) ? "_Ord_".$_POST['nOrden_Activo'] : "";
  $cPedOrd = $_POST['cId_ORD'.$_POST['nOrden_Activo']];
  $cObsCab = "";
  if ($_POST['cId_ORD'.$_POST['nOrden_Activo']] != "") {
    $cObsCab .= "PEDIDO: {$_POST['cId_ORD'.$_POST['nOrden_Activo']]}, NRO. CAJAS {$_POST['cNumCaj_ORD'.$_POST['nOrden_Activo']]}, {$_POST['cPor_ORD'.$_POST['nOrden_Activo']]}% / ";
  }
  $cObsGen = ($_POST['cComObs'] != "") ? "{$_POST['cComObs']}. $cObsCab" : $cObsCab;

  ##Buscar el usuario que ingreso##
  $qUserNom  = "SELECT USRNOMXX  ";
  $qUserNom .= "FROM $cAlfa.SIAI0003 ";
  $qUserNom .= "WHERE ";
  $qUserNom .= "USRIDXXX LIKE \"{$_COOKIE['kUsrId']}\" AND ";
  $qUserNom .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
  $xUserNom  = f_MySql("SELECT","",$qUserNom,$xConexion01,"");
  //f_Mensaje(__FILE__,__LINE__,$qResFac." ~ ".mysql_num_rows($xResFac));
  $vUserNom = mysql_fetch_array($xUserNom);

  ##Fin buscar usuario que ingreso##

  ## Traigo el CLINOMXX o Razon Social de la Agencia de Aduana Dietrich Para encabezado de factura ##
  $qAgeDat  = "SELECT ";
  $qAgeDat .= "$cAlfa.SIAI0150.CLIIDXXX, ";
  $qAgeDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX, ";
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
  $qCliDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX, ";
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
  if($vCliDat['CLICONTX'] != ""){
    $vContactos = explode("~",$vCliDat['CLICONTX']);
    //f_Mensaje(__FILE__,__LINE__,count($vContactos));
    for ($nC=0;$nC<count($vContactos);$nC++) {
      if ($vContactos[$nC] != "") {
        $vIdContacto = $vContactos[$nC];

        $qConDat  = "SELECT ";
        $qConDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX ";
        $qConDat .= "FROM $cAlfa.SIAI0150 ";
        $qConDat .= "WHERE ";
        $qConDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"$vIdContacto\" AND ";
        $qConDat .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" ";
        $xConDat  = f_MySql("SELECT","",$qConDat,$xConexion01,"");
        //f_Mensaje(__FILE__,__LINE__,$qConDat."~".mysql_num_rows($xConDat));
        $nFilCon  = mysql_num_rows($xConDat);
        if ($nFilCon > 0) {
          $vConDat = mysql_fetch_array($xConDat);
        }

        $nC = count($vContactos);
      }
    }
  }//if($vCocDat['CLICONTX'] != ""){
  ##Fin Traigo Datos de Contacto del Facturado a ##


  ##Traigo Primer Do para traer primer Do y traer Datos de Tasa de Cambio, Documento de Transporte, Bultos, Peso ##
  $cDocId    = ""; $cSucId    = ""; $cDocSuf    = "";
  $cDocIdAux = ""; $cSucIdAux = ""; $cDocSufAux = "";

  $vDos = array(); $nEncontro = 0; $cObsSchenker = "";
  for ($i=0;$i<$_POST['nSecuencia_Dos'];$i++) {
    $vDos[count($vDos)] = "{$_POST['cSucId'.$nGrid.'_DOS'.($i+1)]}-{$_POST['cDosNro'.$nGrid.'_DOS'.($i+1)]}-{$_POST['cDosSuf'.$nGrid.'_DOS'.($i+1)]}"; //Dos para compara las cartas bancarias y descriminar los tributos

    //Concatenando DO Schenker
    $qDoSchenker  = "SELECT docdoshe ";
    $qDoSchenker .= "FROM $cAlfa.sys00121 ";
    $qDoSchenker .= "WHERE ";
    $qDoSchenker .= "$cAlfa.sys00121.sucidxxx = \"{$_POST['cSucId'.$nGrid.'_DOS'.($i+1)]}\"  AND ";
    $qDoSchenker .= "$cAlfa.sys00121.docidxxx = \"{$_POST['cDosNro'.$nGrid.'_DOS'.($i+1)]}\" AND ";
    $qDoSchenker .= "$cAlfa.sys00121.docsufxx = \"{$_POST['cDosSuf'.$nGrid.'_DOS'.($i+1)]}\" LIMIT 0,1";
    $xDoSchenker  = f_MySql("SELECT","",$qDoSchenker,$xConexion01,"");
    if (mysql_num_rows($xDoSchenker) > 0) {
      $vDoSchenker   = mysql_fetch_array($xDoSchenker);
      if ($vDoSchenker['docdoshe'] != "") {
        $cObsSchenker .= $vDoSchenker['docdoshe'].", ";
      }
    }

    if ($nEncontro == 0) {
      $cDocIdAux   = $_POST['cDosNro'.$nGrid.'_DOS'.($i+1)];
      $cDocSufAux  = $_POST['cDosSuf'.$nGrid.'_DOS'.($i+1)];
      $cSucIdAux   = $_POST['cSucId'.$nGrid.'_DOS'.($i+1)];
      if ($_POST['cDosTip'.$nGrid.'_DOS'.($i+1)] != "REGISTRO") {
        $cDocId   = $_POST['cDosNro'.$nGrid.'_DOS'.($i+1)];
        $cDocSuf  = $_POST['cDosSuf'.$nGrid.'_DOS'.($i+1)];
        $cSucId   = $_POST['cSucId'.$nGrid.'_DOS'.($i+1)];
        $nEncontro = 1;
      }
    }
  }//for ($i=0;$i<count($mDoiId);$i++) {

  ##Fin Traigo Primer Do para traer primer Do y traer Datos de Tasa de Cambio, Documento de Transporte, Bultos, Peso ##
  if ($cDocId == "") {
    $cDocId  = $cDocIdAux;
    $cDocSuf = $cDocSufAux;
    $cSucId  = $cSucIdAux;
  }

  ##Trayendo Datos de Do Dependiendo del Tipo de Operacion ##
  $vDatDo = f_Datos_Operativos_Do($cSucId, $cDocId, $cDocSuf);
  $vDceDat    = $vDatDo['decdatxx'];
  $cTasCam    = $vDatDo['tascamxx']; //Tasa de Cambio
  $cDocTra    = $vDatDo['doctraxx']; //Documento de Transporte
  $cBultos    = $vDatDo['bultosxx']; //Bultos
  $cPesBru    = $vDatDo['pesbruxx']; //Peso Bruto
  $nValAdu    = $vDatDo['valaduxx']; //Valor en aduana
  $nValAduCop = $vDatDo['valaduco']; //Valor en aduana
  $nValAduUsd = $vDatDo['valaduus']; //Valor en aduana
  $cOpera     = $vDatDo['operaxxx']; //texto valor CIF: o FOB: segun el tipo operacion del tramite
  $cPedido    = $vDatDo['pedidoxx']; //Pedido
  $cAduana    = $vDatDo['aduanaxx']; //Descripcion Aduana
  $cNomVen    = $vDatDo['nomvenxx']; //Nombre Vendedor
  $cOrdCom    = $vDatDo['ordcomxx']; //Orden de Compra
  $cDepOri    = $vDatDo['deporide']; //Departamento Origen
  $cPaiOri    = $vDatDo['paioride']; //Pais Origen
  $cPaiOriDes = $vDatDo['paioride']; //Pais de Origen Descripcion
  $cNomDirCue = $vDatDo['dircuexx']; //Departamento Orige
  $cDeposito  = $vDatDo['daadesxx']; //Descripcion deposito de la SIAI0110
  $cLugIngDes = $vDatDo['lindesxx']; //Lugar de Ingreso
  ###Fin Trayendo Datos de Do Dependiendo del Tipo de Operacion ##

  ##Codigo para imprimir los ingresos para terceros ##
  $mIngTer  = array();
  $mPccTC   = array(); //Tasa de cambio, cuando la moneda es en USD
  $mPccTP   = array(); //Tasa Pactada, cuando la moneda es en COP y el pago a tercero tiene tasa de cambio pactada
  $mValores = array();
  for ($i=0;$i<$_POST['nSecuencia_PCCA'];$i++) {
    $mComObs_IP = stripos($_POST['cComObs'.$nGrid.'_PCCA'.($i+1)], "[");
    $vTercero = explode("^",$_POST['cComObs'.$nGrid.'_PCCA' .($i+1)]);

    $cSwitch_Comprobante_Pago_Impuestos = "NO";
    $qComAju  = "SELECT * ";
    $qComAju .= "FROM $cAlfa.fpar0117 ";
    $qComAju .= "WHERE ";
    $qComAju .= "comidxxx = \"{$_POST['cComId3'.$nGrid.'_PCCA' .($i+1)]}\" AND ";
    $qComAju .= "comcodxx = \"{$_POST['cComCod3'.$nGrid.'_PCCA' .($i+1)]}\" AND ";
    $qComAju .= "comtipxx = \"PAGOIMPUESTOS\" AND ";
    $qComAju .= "regestxx = \"ACTIVO\" LIMIT 0,1";
    $xComAju  = f_MySql("SELECT","",$qComAju,$xConexion01,"");
    //f_Mensaje(__FILE__,__LINE__,"{$_POST['cComId3'.$nGrid.'_PCCA' .($i+1)]} -- {$_POST['cComCod3'.$nGrid.'_PCCA' .($i+1)]} --".$qComAju." ~ ".mysql_num_rows($xComAju));

    if (mysql_num_rows($xComAju) == 1) {

      $qCtoCba  = "SELECT * ";
      $qCtoCba .= "FROM $cAlfa.fpar0119 "; // Aqui no aplica la busqueda contra la fpar0121
      $qCtoCba .= "WHERE ";
      $qCtoCba .= "pucidxxx = \"{$_POST['cPucId'.$nGrid.'_PCCA' .($i+1)]}\" AND ";
      $qCtoCba .= "ctocomxx LIKE \"%{$_POST['cComId3'.$nGrid.'_PCCA' .($i+1)]}~{$_POST['cComCod3'.$nGrid.'_PCCA' .($i+1)]}%\" AND ";
      $qCtoCba .= "ctoidxxx = \"{$_POST['cComId'.$nGrid.'_PCCA' .($i+1)]}\" AND ";
      $qCtoCba .= "regestxx = \"ACTIVO\" LIMIT 0,1";
      $xCtoCba  = f_MySql("SELECT","",$qCtoCba,$xConexion01,"");
      //f_Mensaje(__FILE__,__LINE__,$qCtoCba." ~ ".mysql_num_rows($xCtoCba));

      if (mysql_num_rows($xCtoCba) == 1) {
        $vCtoCba = mysql_fetch_array($xCtoCba);
        if ($vCtoCba['ctoptaxg'] == "SI" || $vCtoCba['ctoptaxl'] == "SI") {
          $cSwitch_Comprobante_Pago_Impuestos = "SI";
        } else {
          $cSwitch_Comprobante_Pago_Impuestos = "NO";
        }
      }
    }

    if ($cSwitch_Comprobante_Pago_Impuestos == "SI") { //pago de impuestos.
      /****************/
      //pago de impuestos.
      if ($_POST['cMarca'.$nGrid.'_PCCA' .($i+1)] == "") {
        $_POST['cMarca'.$nGrid.'_PCCA' .($i+1)] = "SI";
        //Verifico en los pagos a terceros si existe la misma L,si existe esta disciminada y no tengo que hacer nada
        //Pero si no debo verificar si en cabecera los tributos son de los que se migraron de GRM y hay que discriminar
        $nDisc = 0; $nCanL29 = 1;
        for ($nP=0;$nP<$_POST['nSecuencia_PCCA'];$nP++) {
          if ($_POST['cComId3'.$nGrid.'_PCCA' .($i+1)]  == "L" &&
              $_POST['cComCod3'.$nGrid.'_PCCA' .($i+1)] == $_POST['cComCod3'.$nGrid.'_PCCA' .($nP+1)] &&
              $_POST['cComCsc3'.$nGrid.'_PCCA'.($i+1)]  == $_POST['cComCsc3'.$nGrid.'_PCCA'.($nP+1)]) {
            $_POST['cMarca'.$nGrid.'_PCCA' .($nP+1)] = "SI";
          }
        }

        if ($nCanL29 == 1) {

          $nSumAra = 0; // Variable para acumular el valor del arancel declaracion x declaracion
          $nSumIva = 0; // Variable para acumular el valor del iva declaracion x declaracion
          $nSumSal = 0; // Variable para acumular el valor de las salvaguardias declaracion x declaracion
          $nSumCom = 0; // Variable para acumular el valor de los derechos compensantorios declaracion x declaracion
          $nSumAnt = 0; // Variable para acumular el valor de los derechos antidumping declaracion x declaracion
          $nSumSan = 0; // Variable para acumular el valor de las sanciones declaracion x declaracion
          $nSumRes = 0; // Variable para acumular el valor de los rescates declaracion x declaracion

          $nNoDetTri = 0; //Variable que indica cuantas cartas de pago de tribuos no tienen los valores discriminados (este caso aplica para SIACO)

          $vTramites = array();
          //Busco en este año la carta bancaria
          $cAno = date('Y');
          $qFcod  = "SELECT commemod ";
          $qFcod .= "FROM $cAlfa.fcoc$cAno ";
          $qFcod .= "WHERE ";
          $qFcod .= "comidxxx = \"{$_POST['cComId3'.$nGrid.'_PCCA' .($i+1)]}\" AND ";
          $qFcod .= "comcodxx = \"{$_POST['cComCod3'.$nGrid.'_PCCA'.($i+1)]}\" AND ";
          $qFcod .= "comcscxx = \"{$_POST['cComCsc3'.$nGrid.'_PCCA'.($i+1)]}\" AND ";
          $qFcod .= "commemod != \"\" LIMIT 0,1 ";
          $xFcod  = f_MySql("SELECT","",$qFcod,$xConexion01,"");
          //f_Mensaje(__FILE__,__LINE__,$qFcod." ~ ".mysql_num_rows($xFcod));
          $nEncCarta = 0;
          if (mysql_num_rows($xFcod) > 0) {
            $nEncCarta = 1;
          } else {
            $cAnoAnt = (($cAno - 1) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : ($cAno - 1);
            $qFcod = str_replace("fcoc$cAno", "fcoc$cAnoAnt", $qFcod);
            $xFcod  = f_MySql("SELECT","",$qFcod,$xConexion01,"");
            //f_Mensaje(__FILE__,__LINE__,$qFcod." ~ ".mysql_num_rows($xFcod));
            if (mysql_num_rows($xFcod) > 0) {
              $nEncCarta = 1;
            }
          }

          if ($nEncCarta == 1) {
            $xRFcod = mysql_fetch_array($xFcod);
            $mTributos01 = explode("|",$xRFcod['commemod']);
            for ($k=0;$k<count($mTributos01);$k++) {
              if ($mTributos01[$k] != "") {
                $mTributos02 = explode("~",$mTributos01[$k]);

                if (in_array("{$mTributos02[0]}-{$mTributos02[1]}-{$mTributos02[2]}",$vDos) == true) {
                  if (count($mTributos02) == 7) { //Desde conexion GRM
                    $nSumAra += $mTributos02[4];
                    $nSumIva += $mTributos02[5];
                    if ($mTributos02[4] == 0 && $mTributos02[5] == 0){
                      $nNoDetTri++;
                    }
                  } else {
                    $nSumAra += $mTributos02[4];
                    $nSumIva += $mTributos02[5];
                    $nSumSal += $mTributos02[6];
                    $nSumCom += $mTributos02[7];
                    $nSumAnt += $mTributos02[8];
                    $nSumSan += $mTributos02[9];
                    $nSumRes += $mTributos02[10];
                    if ($mTributos02[4] == 0 && $mTributos02[5] == 0 && $mTributos02[6] == 0 && $mTributos02[7] == 0 && $mTributos02[8] == 0 && $mTributos02[9] == 0 && $mTributos02[10] == 0) {
                      $nNoDetTri++;
                    }
                  }
                  $vTramites[count($vTramites)] = $mTributos02[1];
                }
              }
            }
          }
          if ($nNoDetTri > 0 || $_POST['cForImp'] == "REGALIAS") {
            $nSumAra = 0;
            $nSumIva = 0;
            $nSumSal = 0;
            $nSumCom = 0;
            $nSumAnt = 0;
            $nSumSan = 0;
            $nSumRes = 0;
          }

          if ($nSumAra > 0 || $nSumIva > 0 || $nSumSal > 0 || $nSumCom > 0 || $nSumAnt > 0 || $nSumSan > 0 || $nSumRes > 0) {
            $nDisc = 1;
            if ($nSumAra > 0) {
              $nSwitch_Find = 0;
              for ($j=0;$j<count($mIngTer);$j++) {
                if ($mIngTer[$j]['cTipo'] == "ARANCEL") {
                  $nSwitch_Find = 1;
                  if ($_POST['cForImp'] != "REGALIAS" && $_POST['cTerId'] != "900736182") { //Para nestle no imprime DO's
                    for ($nT=0; $nT<count($vTramites); $nT++) {
                      if (in_array("{$vTramites[$nT]}",$mIngTer[$j]['document']) == false) {
                        $mIngTer[$j]['ccomcsc3']  .= "/"."{$vTramites[$nT]}";
                        $mIngTer[$j]['document'][] = "{$vTramites[$nT]}";
                      }
                    }
                  }
                  $mIngTer[$j]['nComVlr']   += ($nSumAra+0);
                }
              }
              if ($nSwitch_Find == 0) { // No lo encontro en la matriz para pintar en la factura
                $nInd_mIngTer = count($mIngTer);
                $mIngTer[$nInd_mIngTer]['cComId']   = $_POST['cComId'.$nGrid.'_PCCA' .($i+1)];
                $mIngTer[$nInd_mIngTer]['cNomTer']  = "PAGO TRIBUTOS ADUANEROS GRAVAMEN ARANCELARIO";
                $mIngTer[$nInd_mIngTer]['cTerId']   = $_POST['cTerId'.$nGrid.'_PCCA' .($i+1)];
                if ($_POST['cForImp'] != "REGALIAS" && $_POST['cTerId'] != "900736182") { //Para nestle no imprime DO's
                  for ($nT=0; $nT<count($vTramites); $nT++) {
                    if (in_array("{$vTramites[$nT]}",$mIngTer[$nInd_mIngTer]['document']) == false) {
                      $mIngTer[$nInd_mIngTer]['ccomcsc3']  .= "/"."{$vTramites[$nT]}";
                      $mIngTer[$nInd_mIngTer]['document'][] = "{$vTramites[$nT]}";
                    }
                  }
                }
                $mIngTer[$nInd_mIngTer]['ccomcsc3'] = (substr($mIngTer[$j]['ccomcsc3'],0,1) == "/") ? substr($mIngTer[$j]['ccomcsc3'],1,strlen($mIngTer[$j]['ccomcsc3'])) : $mIngTer[$j]['ccomcsc3'];
                $mIngTer[$nInd_mIngTer]['nComVlr']  = ($nSumAra+0);
                $mIngTer[$nInd_mIngTer]['cTipo']    = "ARANCEL";
              }
            }

            if ($nSumIva > 0) {
              $nSwitch_Find = 0;
              for ($j=0;$j<count($mIngTer);$j++) {
                if ($mIngTer[$j]['cTipo'] == "IVA") {
                  $nSwitch_Find = 1;
                  if ($_POST['cForImp'] != "REGALIAS" && $_POST['cTerId'] != "900736182") { //Para nestle no imprime DO's
                    for ($nT=0; $nT<count($vTramites); $nT++) {
                      if (in_array("{$vTramites[$nT]}",$mIngTer[$j]['document']) == false) {
                        $mIngTer[$j]['ccomcsc3']  .= "/"."{$vTramites[$nT]}";
                        $mIngTer[$j]['document'][] = "{$vTramites[$nT]}";
                      }
                    }
                  }
                  $mIngTer[$j]['nComVlr']   += ($nSumIva+0);
                }
              }
              if ($nSwitch_Find == 0) { // No lo encontro en la matriz para pintar en la factura
                $nInd_mIngTer = count($mIngTer);
                $mIngTer[$nInd_mIngTer]['cComId']   = $_POST['cComId'.$nGrid.'_PCCA' .($i+1)];
                $mIngTer[$nInd_mIngTer]['cNomTer']  = "PAGO TRIBUTOS ADUANEROS IMPUESTO A LAS VENTAS";
                $mIngTer[$nInd_mIngTer]['cTerId']   = $_POST['cTerId'.$nGrid.'_PCCA' .($i+1)];
                if ($_POST['cForImp'] != "REGALIAS" && $_POST['cTerId'] != "900736182") { //Para nestle no imprime DO's
                  for ($nT=0; $nT<count($vTramites); $nT++) {
                    if (in_array("{$vTramites[$nT]}",$mIngTer[$nInd_mIngTer]['document']) == false) {
                      $mIngTer[$nInd_mIngTer]['ccomcsc3']  .= "/"."{$vTramites[$nT]}";
                      $mIngTer[$nInd_mIngTer]['document'][] = "{$vTramites[$nT]}";
                    }
                  }
                }
                $mIngTer[$nInd_mIngTer]['ccomcsc3'] = (substr($mIngTer[$j]['ccomcsc3'],0,1) == "/") ? substr($mIngTer[$j]['ccomcsc3'],1,strlen($mIngTer[$j]['ccomcsc3'])) : $mIngTer[$j]['ccomcsc3'];
                $mIngTer[$nInd_mIngTer]['nComVlr']  = ($nSumIva+0);
                $mIngTer[$nInd_mIngTer]['cTipo']    = "IVA";
              }
            }

            if ($nSumSal > 0) {
              $nSwitch_Find = 0;
              for ($j=0;$j<count($mIngTer);$j++) {
                if ($mIngTer[$j]['cTipo'] == "SALVAGUARDIAS") {
                  $nSwitch_Find = 1;
                  if ($_POST['cForImp'] != "REGALIAS" && $_POST['cTerId'] != "900736182") { //Para nestle no imprime DO's
                    for ($nT=0; $nT<count($vTramites); $nT++) {
                      if (in_array("{$vTramites[$nT]}",$mIngTer[$j]['document']) == false) {
                        $mIngTer[$j]['ccomcsc3']  .= "/"."{$vTramites[$nT]}";
                        $mIngTer[$j]['document'][] = "{$vTramites[$nT]}";
                      }
                    }
                  }
                  $mIngTer[$j]['nComVlr']   += ($nSumSal+0);
                }
              }
              if ($nSwitch_Find == 0) { // No lo encontro en la matriz para pintar en la factura
                $nInd_mIngTer = count($mIngTer);
                $mIngTer[$nInd_mIngTer]['cComId']   = $_POST['cComId'.$nGrid.'_PCCA' .($i+1)];
                $mIngTer[$nInd_mIngTer]['cNomTer']  = "PAGO TRIBUTOS ADUANEROS SALVAGUARDIAS";
                $mIngTer[$nInd_mIngTer]['cTerId']   = $_POST['cTerId'.$nGrid.'_PCCA' .($i+1)];
                if ($_POST['cForImp'] != "REGALIAS" && $_POST['cTerId'] != "900736182") { //Para nestle no imprime DO's
                  for ($nT=0; $nT<count($vTramites); $nT++) {
                    if (in_array("{$vTramites[$nT]}",$mIngTer[$nInd_mIngTer]['document']) == false) {
                      $mIngTer[$nInd_mIngTer]['ccomcsc3']  .= "/"."{$vTramites[$nT]}";
                      $mIngTer[$nInd_mIngTer]['document'][] = "{$vTramites[$nT]}";
                    }
                  }
                }
                $mIngTer[$nInd_mIngTer]['ccomcsc3'] = (substr($mIngTer[$j]['ccomcsc3'],0,1) == "/") ? substr($mIngTer[$j]['ccomcsc3'],1,strlen($mIngTer[$j]['ccomcsc3'])) : $mIngTer[$j]['ccomcsc3'];
                $mIngTer[$nInd_mIngTer]['nComVlr']  = ($nSumSal+0);
                $mIngTer[$nInd_mIngTer]['cTipo']    = "SALVAGUARDIAS";
              }
            }

            if ($nSumCom > 0) {
              $nSwitch_Find = 0;
              for ($j=0;$j<count($mIngTer);$j++) {
                if ($mIngTer[$j]['cTipo'] == "COMPENSATORIOS") {
                  $nSwitch_Find = 1;
                  if ($_POST['cForImp'] != "REGALIAS" && $_POST['cTerId'] != "900736182") { //Para nestle no imprime DO's
                    for ($nT=0; $nT<count($vTramites); $nT++) {
                      if (in_array("{$vTramites[$nT]}",$mIngTer[$j]['document']) == false) {
                        $mIngTer[$j]['ccomcsc3']  .= "/"."{$vTramites[$nT]}";
                        $mIngTer[$j]['document'][] = "{$vTramites[$nT]}";
                      }
                    }
                  }
                  $mIngTer[$j]['nComVlr']   += ($nSumCom+0);
                }
              }
              if ($nSwitch_Find == 0) { // No lo encontro en la matriz para pintar en la factura
                $nInd_mIngTer = count($mIngTer);
                $mIngTer[$nInd_mIngTer]['cComId']   = $_POST['cComId'.$nGrid.'_PCCA' .($i+1)];
                $mIngTer[$nInd_mIngTer]['cNomTer']  = "PAGO TRIBUTOS ADUANEROS DERECHOS COMPENSATORIOS";
                $mIngTer[$nInd_mIngTer]['cTerId']   = $_POST['cTerId'.$nGrid.'_PCCA' .($i+1)];
                if ($_POST['cForImp'] != "REGALIAS" && $_POST['cTerId'] != "900736182") { //Para nestle no imprime DO's
                  for ($nT=0; $nT<count($vTramites); $nT++) {
                    if (in_array("{$vTramites[$nT]}",$mIngTer[$nInd_mIngTer]['document']) == false) {
                      $mIngTer[$nInd_mIngTer]['ccomcsc3']  .= "/"."{$vTramites[$nT]}";
                      $mIngTer[$nInd_mIngTer]['document'][] = "{$vTramites[$nT]}";
                    }
                  }
                }
                $mIngTer[$nInd_mIngTer]['ccomcsc3'] = (substr($mIngTer[$j]['ccomcsc3'],0,1) == "/") ? substr($mIngTer[$j]['ccomcsc3'],1,strlen($mIngTer[$j]['ccomcsc3'])) : $mIngTer[$j]['ccomcsc3'];
                $mIngTer[$nInd_mIngTer]['nComVlr']  = ($nSumCom+0);
                $mIngTer[$nInd_mIngTer]['cTipo']    = "COMPENSATORIOS";
              }
            }

            if ($nSumAnt > 0) {
              $nSwitch_Find = 0;
              for ($j=0;$j<count($mIngTer);$j++) {
                if ($mIngTer[$j]['cTipo'] == "ANTIDUMPING") {
                  $nSwitch_Find = 1;
                  if ($_POST['cForImp'] != "REGALIAS" && $_POST['cTerId'] != "900736182") { //Para nestle no imprime DO's
                    for ($nT=0; $nT<count($vTramites); $nT++) {
                      if (in_array("{$vTramites[$nT]}",$mIngTer[$j]['document']) == false) {
                        $mIngTer[$j]['ccomcsc3']  .= "/"."{$vTramites[$nT]}";
                        $mIngTer[$j]['document'][] = "{$vTramites[$nT]}";
                      }
                    }
                  }
                  $mIngTer[$j]['nComVlr']   += ($nSumAnt+0);
                }
              }
              if ($nSwitch_Find == 0) { // No lo encontro en la matriz para pintar en la factura
                $nInd_mIngTer = count($mIngTer);
                $mIngTer[$nInd_mIngTer]['cComId']   = $_POST['cComId'.$nGrid.'_PCCA' .($i+1)];
                $mIngTer[$nInd_mIngTer]['cNomTer']  = "PAGO TRIBUTOS ADUANEROS DERECHOS ANTIDUMPING";
                $mIngTer[$nInd_mIngTer]['cTerId']   = $_POST['cTerId'.$nGrid.'_PCCA' .($i+1)];
                if ($_POST['cForImp'] != "REGALIAS" && $_POST['cTerId'] != "900736182") { //Para nestle no imprime DO's
                  for ($nT=0; $nT<count($vTramites); $nT++) {
                    if (in_array("{$vTramites[$nT]}",$mIngTer[$nInd_mIngTer]['document']) == false) {
                      $mIngTer[$nInd_mIngTer]['ccomcsc3']  .= "/"."{$vTramites[$nT]}";
                      $mIngTer[$nInd_mIngTer]['document'][] = "{$vTramites[$nT]}";
                    }
                  }
                }
                $mIngTer[$nInd_mIngTer]['ccomcsc3'] = (substr($mIngTer[$j]['ccomcsc3'],0,1) == "/") ? substr($mIngTer[$j]['ccomcsc3'],1,strlen($mIngTer[$j]['ccomcsc3'])) : $mIngTer[$j]['ccomcsc3'];
                $mIngTer[$nInd_mIngTer]['nComVlr']  = ($nSumAnt+0);
                $mIngTer[$nInd_mIngTer]['cTipo']    = "ANTIDUMPING";
              }
            }

            if ($nSumSan > 0) {
              $nSwitch_Find = 0;
              for ($j=0;$j<count($mIngTer);$j++) {
                if ($mIngTer[$j]['cTipo'] == "SANCIONES") {
                  $nSwitch_Find = 1;
                  if ($_POST['cForImp'] != "REGALIAS" && $_POST['cTerId'] != "900736182") { //Para nestle no imprime DO's
                    for ($nT=0; $nT<count($vTramites); $nT++) {
                      if (in_array("{$vTramites[$nT]}",$mIngTer[$j]['document']) == false) {
                        $mIngTer[$j]['ccomcsc3']  .= "/"."{$vTramites[$nT]}";
                        $mIngTer[$j]['document'][] = "{$vTramites[$nT]}";
                      }
                    }
                  }
                  $mIngTer[$j]['nComVlr']   += ($nSumSan+0);
                }
              }
              if ($nSwitch_Find == 0) { // No lo encontro en la matriz para pintar en la factura
                $nInd_mIngTer = count($mIngTer);
                $mIngTer[$nInd_mIngTer]['cComId']   = $_POST['cComId'.$nGrid.'_PCCA' .($i+1)];
                $mIngTer[$nInd_mIngTer]['cNomTer']  = "PAGO TRIBUTOS ADUANEROS SANCIONES";
                $mIngTer[$nInd_mIngTer]['cTerId']   = $_POST['cTerId'.$nGrid.'_PCCA' .($i+1)];
                if ($_POST['cForImp'] != "REGALIAS" && $_POST['cTerId'] != "900736182") { //Para nestle no imprime DO's
                  for ($nT=0; $nT<count($vTramites); $nT++) {
                    if (in_array("{$vTramites[$nT]}",$mIngTer[$nInd_mIngTer]['document']) == false) {
                      $mIngTer[$nInd_mIngTer]['ccomcsc3']  .= "/"."{$vTramites[$nT]}";
                      $mIngTer[$nInd_mIngTer]['document'][] = "{$vTramites[$nT]}";
                    }
                  }
                }
                $mIngTer[$nInd_mIngTer]['ccomcsc3'] = (substr($mIngTer[$j]['ccomcsc3'],0,1) == "/") ? substr($mIngTer[$j]['ccomcsc3'],1,strlen($mIngTer[$j]['ccomcsc3'])) : $mIngTer[$j]['ccomcsc3'];
                $mIngTer[$nInd_mIngTer]['nComVlr']  = ($nSumSan+0);
                $mIngTer[$nInd_mIngTer]['cTipo']    = "SANCIONES";
              }
            }

            if ($nSumRes > 0) {
              $nSwitch_Find = 0;
              for ($j=0;$j<count($mIngTer);$j++) {
                if ($mIngTer[$j]['cTipo'] == "RESCATES") {
                  $nSwitch_Find = 1;
                  if ($_POST['cForImp'] != "REGALIAS" && $_POST['cTerId'] != "900736182") { //Para nestle no imprime DO's
                    for ($nT=0; $nT<count($vTramites); $nT++) {
                      if (in_array("{$vTramites[$nT]}",$mIngTer[$j]['document']) == false) {
                        $mIngTer[$j]['ccomcsc3']  .= "/"."{$vTramites[$nT]}";
                        $mIngTer[$j]['document'][] = "{$vTramites[$nT]}";
                      }
                    }
                  }
                  $mIngTer[$j]['nComVlr']   += ($nSumRes+0);
                }
              }
              if ($nSwitch_Find == 0) { // No lo encontro en la matriz para pintar en la factura
                $nInd_mIngTer = count($mIngTer);
                $mIngTer[$nInd_mIngTer]['cComId']   = $_POST['cComId'.$nGrid.'_PCCA' .($i+1)];
                $mIngTer[$nInd_mIngTer]['cNomTer']  = "PAGO TRIBUTOS ADUANEROS RESCATES";
                $mIngTer[$nInd_mIngTer]['cTerId']   = $_POST['cTerId'.$nGrid.'_PCCA' .($i+1)];
                if ($_POST['cForImp'] != "REGALIAS" && $_POST['cTerId'] != "900736182") { //Para nestle no imprime DO's
                  for ($nT=0; $nT<count($vTramites); $nT++) {
                    if (in_array("{$vTramites[$nT]}",$mIngTer[$nInd_mIngTer]['document']) == false) {
                      $mIngTer[$nInd_mIngTer]['ccomcsc3']  .= "/"."{$vTramites[$nT]}";
                      $mIngTer[$nInd_mIngTer]['document'][] = "{$vTramites[$nT]}";
                    }
                  }
                }
                $mIngTer[$nInd_mIngTer]['ccomcsc3'] = (substr($mIngTer[$j]['ccomcsc3'],0,1) == "/") ? substr($mIngTer[$j]['ccomcsc3'],1,strlen($mIngTer[$j]['ccomcsc3'])) : $mIngTer[$j]['ccomcsc3'];
                $mIngTer[$nInd_mIngTer]['nComVlr']  = ($nSumRes+0);
                $mIngTer[$nInd_mIngTer]['cTipo']    = "RESCATES";
              }
            }
          }

          if ($nDisc == 0) {
            $nSwitch_Find = 0;
            for ($j=0;$j<count($mIngTer);$j++) {
              if ($mIngTer[$j]['cTipo'] == "TRIBUTOS") {
                $nSwitch_Find = 1;
                if ($_POST['cForImp'] != "REGALIAS" && $_POST['cTerId'] != "900736182") { //Para nestle no imprime DO's
                  if (count($vTramites) > 0) {
                    for ($nT=0; $nT<count($vTramites); $nT++) {
                      if (in_array("{$vTramites[$nT]}",$mIngTer[$j]['document']) == false) {
                        $mIngTer[$j]['ccomcsc3']  .= "/"."{$vTramites[$nT]}";
                        $mIngTer[$j]['document'][] = "{$vTramites[$nT]}";
                      }
                    }
                  } else {
                    $vDo = explode("-", $_POST['cComTra'.$nGrid.'_PCCA' .($i+1)]);
                    if (in_array("{$vDo[1]}",$mIngTer[$j]['document']) == false) {
                      $mIngTer[$j]['ccomcsc3']  .= "/"."{$vDo[1]}";
                      $mIngTer[$j]['document'][] = "{$vDo[1]}";
                    }
                  }
                }

                $mIngTer[$j]['nComVlr']   += ($_POST['nComVlr' .$nGrid.'_PCCA'.($i+1)]+0);
                $mIngTer[$j]['nBaseIva']  += ($_POST['nBaseIva'.$nGrid.'_PCCA'.($i+1)]+0);
                $mIngTer[$j]['nVlrIva']   += ($_POST['nVlrIva' .$nGrid.'_PCCA'.($i+1)]+0);
              }
            }

            if ($nSwitch_Find == 0) { // No lo encontro en la matriz para pintar en la factura
              $nInd_mIngTer = count($mIngTer);
              $mIngTer[$nInd_mIngTer]['cComId']     = $_POST['cComId'.$nGrid.'_PCCA' .($i+1)];
              $mIngTer[$nInd_mIngTer]['cNomTer']    = (trim($vTercero[0]) != "PAGO TRIBUTOS ADUANEROS") ? "PAGO TRIBUTOS ADUANEROS ".$vTercero[0] : $vTercero[0];
              $mIngTer[$nInd_mIngTer]['cTerId']     = $_POST['cTerId'.$nGrid.'_PCCA' .($i+1)];
              if ($_POST['cForImp'] != "REGALIAS" && $_POST['cTerId'] != "900736182") { //Para nestle no imprime DO's
                if (count($vTramites) > 0) {
                  for ($nT=0; $nT<count($vTramites); $nT++) {
                    if (in_array("{$vTramites[$nT]}",$mIngTer[$nInd_mIngTer]['document']) == false) {
                      $mIngTer[$nInd_mIngTer]['ccomcsc3']  .= "/"."{$vTramites[$nT]}";
                      $mIngTer[$nInd_mIngTer]['document'][] = "{$vTramites[$nT]}";
                    }
                  }
                } else {
                  $vDo = explode("-", $_POST['cComTra'.$nGrid.'_PCCA' .($i+1)]);
                  $mIngTer[$nInd_mIngTer]['ccomcsc3']   = $vDo[1];
                  $mIngTer[$nInd_mIngTer]['document'][] = "{$vDo[1]}";
                }
              }
              $mIngTer[$nInd_mIngTer]['nComVlr']    = ($_POST['nComVlr'.$nGrid.'_PCCA' .($i+1)]+0);
              $mIngTer[$nInd_mIngTer]['nBaseIva']   = ($_POST['nBaseIva'.$nGrid.'_PCCA' .($i+1)]+0);
              $mIngTer[$nInd_mIngTer]['nVlrIva']    = ($_POST['nVlrIva'.$nGrid.'_PCCA' .($i+1)]+0);
              $mIngTer[$nInd_mIngTer]['cTipo']      = "TRIBUTOS";
            }
          }
        } else {
          $nInd_mIngTer = count($mIngTer);
          $mIngTer[$nInd_mIngTer]['cComId']   = $_POST['cComId'.$nGrid.'_PCCA' .($i+1)];
          $mIngTer[$nInd_mIngTer]['cNomTer']  = (trim($vTercero[0]) != "PAGO TRIBUTOS ADUANEROS") ? "PAGO TRIBUTOS ADUANEROS ".$vTercero[0] : $vTercero[0];
          $mIngTer[$nInd_mIngTer]['cTerId']   = $_POST['cTerId'.$nGrid.'_PCCA' .($i+1)];
          $vDo = explode("-", $_POST['cComTra'.$nGrid.'_PCCA' .($i+1)]);
          $mIngTer[$nInd_mIngTer]['ccomcsc3'] = $vDo[1];
          $mIngTer[$nInd_mIngTer]['nComVlr']  = $_POST['nComVlr'.$nGrid.'_PCCA' .($i+1)];
          $mIngTer[$nInd_mIngTer]['nBaseIva'] = $_POST['nBaseIva'.$nGrid.'_PCCA' .($i+1)];
          $mIngTer[$nInd_mIngTer]['nVlrIva']  = $_POST['nVlrIva'.$nGrid.'_PCCA' .($i+1)];
        }
      }
      /****************/
    }else{

      /**
       * Si es un comprobante L-38 esta marcado para no agrupar, y el concepto es
       * 2805050090 o 2805050117, los conceptos no se agrupan por proveedor, sino por concepto
       */
      $nAgruparConcepto = 0;
      if ($_POST['cComId3'.$nGrid.'_PCCA' .($i+1)] == "L" && $_POST['cComCod3'.$nGrid.'_PCCA'.($i+1)] == "038" &&
          ($_POST['cComId'.$nGrid.'_PCCA'.($i+1)] == "2805050090" || $_POST['cComId'.$nGrid.'_PCCA'.($i+1)] == "2805050117")) {

        $cAno = date('Y');
        $qAjuTri  = "SELECT competxx ";
        $qAjuTri .= "FROM $cAlfa.fcoc$cAno ";
        $qAjuTri .= "WHERE ";
        $qAjuTri .= "comidxxx = \"{$_POST['cComId3'.$nGrid.'_PCCA' .($i+1)]}\" AND ";
        $qAjuTri .= "comcodxx = \"{$_POST['cComCod3'.$nGrid.'_PCCA'.($i+1)]}\" AND ";
        $qAjuTri .= "comcscxx = \"{$_POST['cComCsc3'.$nGrid.'_PCCA'.($i+1)]}\" AND ";
        $qAjuTri .= "competxx = \"SI\" LIMIT 0,1 ";
        $xAjuTri  = f_MySql("SELECT","",$qAjuTri,$xConexion01,"");
        // f_Mensaje(__FILE__,__LINE__,$qAjuTri."~".mysql_num_rows($xAjuTri));
        if (mysql_num_rows($xAjuTri) > 0) {
          $nAgruparConcepto = 1;
          $cDoTri = explode("-", $_POST['cComTra'.$nGrid.'_PCCA'.($i+1)]);
          $_POST['cComCsc3'.$nGrid.'_PCCA'.($i+1)] = $cDoTri[1];
        } else {
          $cAnoAnt = (($cAno - 1) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : ($cAno - 1);
          $qAjuTri = str_replace("fcoc$cAno", "fcoc$cAnoAnt", $qAjuTri);
          $xAjuTri  = f_MySql("SELECT","",$qAjuTri,$xConexion01,"");
          //f_Mensaje(__FILE__,__LINE__,$qAjuTri." ~ ".mysql_num_rows($xAjuTri));
          if (mysql_num_rows($xAjuTri) > 0) {
            $nAgruparConcepto = 1;
            $cDoTri = explode("-", $_POST['cComTra'.$nGrid.'_PCCA'.($i+1)]);
            $_POST['cComCsc3'.$nGrid.'_PCCA'.($i+1)] = $cDoTri[1];
          }
        }
      }

      //Solo los comprobantes tipo P pueden tener tasa pactada
      $_POST['nTasaP'.$nGrid.'_PCCA'.($i+1)] = "SIN_TASAPAC"; //Si tiene tasa pactada, se guarda la tasa, sino se marca sin tasa pactada
      if ($_POST['cComId3' .$nGrid.'_PCCA'.($i+1)] == "P") {
        $cAno = date('Y');
        $qComTP  = "SELECT ";
        $qComTP .= "comidxxx, comcodxx, comcscxx, comcsc2x ";
        $qComTP .= "FROM $cAlfa.fcod$cAno ";
        $qComTP .= "WHERE ";
        $qComTP .= "comidxxx = \"{$_POST['cComId3' .$nGrid.'_PCCA'.($i+1)]}\" AND ";
        $qComTP .= "comcodxx = \"{$_POST['cComCod3'.$nGrid.'_PCCA'.($i+1)]}\" AND ";
        $qComTP .= "comcscxx = \"{$_POST['cComCsc3'.$nGrid.'_PCCA'.($i+1)]}\" AND ";
        $qComTP .= "comseqxx = \"{$_POST['cComSeq3'.$nGrid.'_PCCA'.($i+1)]}\" AND "; 
        $qComTP .= "ctoidxxx = \"{$_POST['cComId'  .$nGrid.'_PCCA'.($i+1)]}\" AND "; 
        $qComTP .= "pucidxxx = \"{$_POST['cPucId'  .$nGrid.'_PCCA'.($i+1)]}\" AND "; 
        $qComTP .= "teridxxx = \"{$_POST['cTerId'  .$nGrid.'_PCCA'.($i+1)]}\" AND "; 
        $qComTP .= "terid2xx = \"{$_POST['cTerId2' .$nGrid.'_PCCA'.($i+1)]}\" AND ";
        $qComTP .= "regestxx = \"ACTIVO\" LIMIT 0,1 "; 
        $xComTP  = mysql_query($qComTP, $xConexion01);
        // echo "<br><br>".$qComTP."~".mysql_num_rows($xComTP);
        $vComTP  = array();
        if (mysql_num_rows($xComTP) > 0) {
          $vComTP = mysql_fetch_array($xComTP);
        } else {
          $cAnoAnt = (($cAno - 1) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : ($cAno - 1);
          $qComTP = str_replace("fcod$cAno", "fcod$cAnoAnt", $qComTP);
          $xComTP  = f_MySql("SELECT","",$qComTP,$xConexion01,"");
          if (mysql_num_rows($xComTP) > 0) {
            $vComTP = mysql_fetch_array($xComTP);
          }
        }
        //Encontro el documento
        if (count($vComTP) > 0) {
          //Buscando datos de cabecera
          $qComObs2  = "SELECT ";
          $qComObs2 .= "comobs2x ";
          $qComObs2 .= "FROM $cAlfa.fcoc$cAno ";
          $qComObs2 .= "WHERE ";
          $qComObs2 .= "comidxxx = \"{$vComTP['comidxxx']}\" AND ";
          $qComObs2 .= "comcodxx = \"{$vComTP['comcodxx']}\" AND ";
          $qComObs2 .= "comcscxx = \"{$vComTP['comcscxx']}\" AND ";
          $qComObs2 .= "comcsc2x = \"{$vComTP['comcsc2x']}\" LIMIT 0,1";
          $xComObs2  = mysql_query($qComObs2, $xConexion01);
          // echo "<br><br>".$qComObs2."~".mysql_num_rows($xComObs2);
          $vComObs2  = array();
          if(mysql_num_rows($xComObs2) > 0){
            $vComObs2 = mysql_fetch_array($xComObs2);
          } else {
            $cAnoAnt = (($cAno - 1) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : ($cAno - 1);
            $qComObs2 = str_replace("fcoc$cAno", "fcoc$cAnoAnt", $qComObs2);
            $xComObs2  = f_MySql("SELECT","",$qComObs2,$xConexion01,"");
            //f_Mensaje(__FILE__,__LINE__,$qComObs2." ~ ".mysql_num_rows($xAjuTri));
            if (mysql_num_rows($xComObs2) > 0) {
              $vComObs2 = mysql_fetch_array($xComObs2);
            }
          }
          if (count($vComObs2) > 0) {
            $vTasaPago = explode("~", $vComObs2['comobs2x']);
            $_POST['nTasaP'.$nGrid.'_PCCA'.($i+1)] = ($vTasaPago[4] == "") ? "SIN_TASAPAC" : $vTasaPago[4]+0;
          }
        }
      }

      if(count($vTercero)==1){
        $mValores['nDesVlr'][] = $_POST['cComObs'.$nGrid.'_PCCA' .($i+1)];
        $mValores['nTotVlr'][] = $_POST['nComVlr'.$nGrid.'_PCCA'.($i+1)];
        $mValores['nTasa'][]   = $_POST['nTasa'.$nGrid.'_PCCA'.($i+1)];
      } 
      if(trim($vTercero[2])!=""){
        $nSwitch_Find = 0;
        for ($j=0;$j<count($mIngTer);$j++) {
          $nEncAgru = 0;
          if ($nAgruparConcepto == 1) {
            //Se agrupa por concepto
            if ($mIngTer[$j]['cComId'] == $_POST['cComId'.$nGrid.'_PCCA'.($i+1)]) {
              $nEncAgru = 1;
            }
          } else {
            //Se agrupa por tercero
            if (trim($mIngTer[$j]['nNitTercero']) == trim($vTercero[2])) {
              $nEncAgru = 1;
            }
          }

          if ($nEncAgru == 1) {
            $nSwitch_Find = 1;
            $mIngTer[$j]['nComVlr']   += ($_POST['nComVlr' .$nGrid.'_PCCA'.($i+1)]+0);
            $mIngTer[$j]['nBaseIva']  += ($_POST['nBaseIva'.$nGrid.'_PCCA'.($i+1)]+0);
            $mIngTer[$j]['nVlrIva']   += ($_POST['nVlrIva' .$nGrid.'_PCCA'.($i+1)]+0);
            $mIngTer[$j]['nTasa']      = "SI";
            //Se agrupa por la tasa de cambio el numero del comprobante.
            $mPccTC[$j]["{$_POST['nTasa'.$nGrid.'_PCCA'.($i+1)]}"][] = "{$_POST['cComCsc3'.$nGrid.'_PCCA'.($i+1)]}";

            //Comprobantes para los que aplica la tasa pactada
            $mPccTP[$j]["{$_POST['nTasaP'.$nGrid.'_PCCA'.($i+1)]}"][] = "{$_POST['cComCsc3'.$nGrid.'_PCCA'.($i+1)]}";
          }
        }

        if ($nSwitch_Find == 0) {
          // No lo encontro en la matriz para pintar en la factura
          // f_mensaje("","",$vTercero[2]);
          if(trim($vTercero[2])!=""){
            if ($nAgruparConcepto == 1) {
              if ($_POST['cComId'.$nGrid.'_PCCA'.($i+1)] == "2805050090") {
                $vTercero[1] = "PAGO TRIBUTOS ADUANEROS GRAVAMEN ARANCELARIO";
                $vTercero[2] = "800197268";
              }

              if ($_POST['cComId'.$nGrid.'_PCCA'.($i+1)] == "2805050117") {
                $vTercero[1] = "PAGO TRIBUTOS ADUANEROS IMPUESTO A LAS VENTAS";
                $vTercero[2] = "800197268";
              }
            }

            $nInd_mIngTer = count($mIngTer);
            $mIngTer[$nInd_mIngTer]['nNitTercero']  = $vTercero[2];
            $mIngTer[$nInd_mIngTer]['cComId']       = $_POST['cComId'.$nGrid.'_PCCA'  .($i+1)];
            $mIngTer[$nInd_mIngTer]['cComObs']      = $vTercero[0];
            $mIngTer[$nInd_mIngTer]['ccomcsc3']     = $_POST['cComCsc3'.$nGrid.'_PCCA'.($i+1)];
            $mIngTer[$nInd_mIngTer]['nComVlr']      = ($_POST['nComVlr'.$nGrid.'_PCCA' .($i+1)]+0);
            $mIngTer[$nInd_mIngTer]['nBaseIva']     = ($_POST['nBaseIva'.$nGrid.'_PCCA'.($i+1)]+0);
            $mIngTer[$nInd_mIngTer]['nVlrIva']      = ($_POST['nVlrIva'.$nGrid.'_PCCA'.($i+1)]+0);
            $mIngTer[$nInd_mIngTer]['nTasa']        = "SI";
            //Se agrupa por la tasa de cambio el numero del comprobante.
            $mPccTC[$nInd_mIngTer]["{$_POST['nTasa'.$nGrid.'_PCCA'.($i+1)]}"][] = "{$_POST['cComCsc3'.$nGrid.'_PCCA'.($i+1)]}";

            //Comprobantes para los que aplica la tasa pactada
            $mPccTP[$nInd_mIngTer]["{$_POST['nTasaP'.$nGrid.'_PCCA'.($i+1)]}"][] = "{$_POST['cComCsc3'.$nGrid.'_PCCA'.($i+1)]}";

            if ($nAgruparConcepto != 1) {
              $qTerDat  = "SELECT IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX ";
              $qTerDat .= "FROM $cAlfa.SIAI0150 ";
              $qTerDat .= "WHERE ";
              $qTerDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"".trim($vTercero[2])."\" AND ";
              $qTerDat .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" ";
              $xTerDat  = f_MySql("SELECT","",$qTerDat,$xConexion01,"");
              // f_Mensaje(__FILE__,__LINE__,$qTerDat."~".mysql_num_rows($xTerDat));
              $vTerDat = mysql_fetch_array($xTerDat);
            }
            $mIngTer[$nInd_mIngTer]['cNomTer'] = $vTercero[1];
          }
        }
      }
    }
  }//for ($i=0;$i<$_POST['nSecuencia_PCCA'];$i++) {

  //Agrupando los valores por tipo
  $nSumAra = 0; $nSumIva = 0; $nSumSal = 0; $nSumCom = 0; $nSumAnt = 0;
  $nSumSan = 0; $nSumRes = 0; $nSumTri = 0;

  for ($i=0;$i<$_POST['nSecuencia_PCCA'];$i++) {
    if ($_POST['cMarca'.$nGrid.'_PCCA' .($i+1)] == "SI") { //Indica que es de tributos

      $vTercero = explode("^",$_POST['cComObs'.$nGrid.'_PCCA' .($i+1)]);

      if (substr_count(trim($vTercero[0]), "GRAVAMEN ARANCELARIO") > 0) {
        $nSumAra +=  ($_POST['nComVlr'.$nGrid.'_PCCA' .($i+1)]+0);
      } elseif (substr_count(trim($vTercero[0]), "IMPUESTO A LAS VENTAS") > 0) {
        $nSumIva +=  ($_POST['nComVlr'.$nGrid.'_PCCA' .($i+1)]+0);
      } elseif (substr_count(trim($vTercero[0]), "SALVAGUARDIAS") > 0) {
        $nSumSal +=  ($_POST['nComVlr'.$nGrid.'_PCCA' .($i+1)]+0);
      } elseif (substr_count(trim($vTercero[0]), "DERECHOS COMPENSATORIOS") > 0) {
        $nSumCom +=  ($_POST['nComVlr'.$nGrid.'_PCCA' .($i+1)]+0);
      } elseif (substr_count(trim($vTercero[0]), "DERECHOS ANTIDUMPING") > 0) {
        $nSumAnt +=  ($_POST['nComVlr'.$nGrid.'_PCCA' .($i+1)]+0);
      } elseif (substr_count(trim($vTercero[0]), "SANCIONES") > 0) {
        $nSumSan +=  ($_POST['nComVlr'.$nGrid.'_PCCA' .($i+1)]+0);
      } elseif (substr_count(trim($vTercero[0]), "RESCATES") > 0) {
        $nSumRes +=  ($_POST['nComVlr'.$nGrid.'_PCCA' .($i+1)]+0);
      }
      $nSumTri +=  ($_POST['nComVlr'.$nGrid.'_PCCA' .($i+1)]+0);
    }
  }

  //Verificando si alguno de esos montos no tuvo ajustes
  for ($nA=0; $nA<count($mIngTer); $nA++) {
    switch ($mIngTer[$nA]['cTipo']) {
      case "TRIBUTOS":
        //No se logro discriminar los impuestos, si hay ajustes a todo el valor del comprobante
        if ($mIngTer[$nA]['nComVlr'] != $nSumTri) {
          $mIngTer[$nA]['nComVlr']  = $nSumTri;
        }
      break;
      case "ARANCEL":
        //No se logro discriminar los impuestos, si hay ajustes a todo el valor del comprobante
        if ($mIngTer[$nA]['nComVlr'] != $nSumAra) {
          $mIngTer[$nA]['nComVlr']  = $nSumAra;
        }
      break;
      case "IVA":
        //No se logro discriminar los impuestos, si hay ajustes a todo el valor del comprobante
        if ($mIngTer[$nA]['nComVlr'] != $nSumIva) {
          $mIngTer[$nA]['nComVlr']  = $nSumIva;
        }
      break;
      case "SALVAGUARDIAS":
        //No se logro discriminar los impuestos, si hay ajustes a todo el valor del comprobante
        if ($mIngTer[$nA]['nComVlr'] != $nSumSal) {
          $mIngTer[$nA]['nComVlr']  = $nSumSal;
        }
      break;
      case "COMPENSATORIOS":
        //No se logro discriminar los impuestos, si hay ajustes a todo el valor del comprobante
        if ($mIngTer[$nA]['nComVlr'] != $nSumCom) {
          $mIngTer[$nA]['nComVlr']  = $nSumCom;
        }
      break;
      case "ANTIDUMPING":
        //No se logro discriminar los impuestos, si hay ajustes a todo el valor del comprobante
        if ($mIngTer[$nA]['nComVlr'] != $nSumAnt) {
          $mIngTer[$nA]['nComVlr']  = $nSumAnt;
        }
      break;
      case "SANCIONES":
        //No se logro discriminar los impuestos, si hay ajustes a todo el valor del comprobante
        if ($mIngTer[$nA]['nComVlr'] != $nSumSan) {
          $mIngTer[$nA]['nComVlr']  = $nSumSan;
        }
      break;
      case "RESCATES":
        //No se logro discriminar los impuestos, si hay ajustes a todo el valor del comprobante
        if ($mIngTer[$nA]['nComVlr'] != $nSumRes) {
          $mIngTer[$nA]['nComVlr']  = $nSumRes;
        }
      break;
    }
  }

  #Cuatro por Mil y formularios#
  for($h=0;$h<count($mValores);$h++ ){
    if ($mValores['nTotVlr'][$h] > 0) {
      $nInd_mIngTer = count($mIngTer);
      $mIngTer[$nInd_mIngTer]['nNitTercero'] = "";
      $mIngTer[$nInd_mIngTer]['cComId']      = "";
      $mIngTer[$nInd_mIngTer]['cComObs']     = "";
      $mIngTer[$nInd_mIngTer]['ccomcsc3']    = "";
      $mIngTer[$nInd_mIngTer]['nComVlr']     = $mValores['nTotVlr'][$h];
      $mIngTer[$nInd_mIngTer]['nBaseIva']    = "";
      $mIngTer[$nInd_mIngTer]['nVlrIva']     = "";
      $mIngTer[$nInd_mIngTer]['cNomTer']     = $mValores['nDesVlr'][$h];
      $mIngTer[$nInd_mIngTer]['nTasa']       = ($mValores['nTasa'][$h] != '' && $_POST['cMonId'] == "USD") ? "T.C. ".number_format($mValores['nTasa'][$h],2,',','.') : "";
    }
  }#Fin Cuatro por Mil y formularios#


  /**
   * Tarifa especial avianca para SIACO
   * se deben agrupar para el cliente avianca las siguientes tarifas en un unico concepto de cobro:
   * 200-200
   * 100-100
   * 301-300
   */
  #Agrupo Ingresos Propios

  $mIP = array();
  for($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {

    $vDatosIp = array();
    $cObs = "|".$_POST['cSerId'.$nGrid.'_IPA'.($i+1)]."~".$_POST['cFcoId'.$nGrid.'_IPA'.($i+1)]."~".$_POST['cComObs'.$nGrid.'_IPA'.($i+1)]."|";
    $vDatosIp = f_Cantidad_Ingreso_Propio($cObs,'',$_POST['cSucId'.$nGrid.'_IPA'.($i+1)],$_POST['cDosNro'.$nGrid.'_IPA'.($i+1)],$_POST['cDosSuf'.$nGrid.'_IPA'.($i+1)]);

    $vComObs_IP = explode("~",trim($vDatosIp[4],"|"));

    //Cantidad por condicion especial
    for ($nP=0; $nP<count($vDatosIp[3]); $nP++) {
      if ($mIP[$_POST['cComId_IPA'.($i+1)]]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] != "") {
        //El campo aplsumxx del array inidica como deben sumarse la cantidades
        //si el valor es SI, se suma normal
        //si el valor es NO, no se suma, se mantiene el valor del IP inicial
        //si el valor es -, deben dividirse la cadena por este caracter y sumar indiviualmente
        switch ($vDatosIp[3][$nP]['aplsumxx']) {
          case "NO":
            //No hace nada se queda con el primer valor
          break;
          case "-":
            $vAuxCanAcu = explode("-",$mIP[$_POST['cComId_IPA'.($i+1)]]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")]);
            $vAuxCanIp  = explode("-",$vDatosIp[3][$nP]['valpdfxx']);
            for ($nAC=0; $nAC<count($vAuxCanAcu); $nAC++) {
              $vAuxCanAcu[$nAC] += $vAuxCanIp[$nAC];
            }
            $cValCanAcu = "";
            for ($nAC=0; $nAC<count($vAuxCanAcu); $nAC++) {
              $cValCanAcu .= $vAuxCanAcu[$nAC]."-";
            }
            $cValCanAcu = substr($cValCanAcu, 0, -1);
            $mIP[$_POST['cComId_IPA'.($i+1)]]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] = $cValCanAcu;
          break;
          default: //por default es SI
            $mIP[$_POST['cComId_IPA'.($i+1)]]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] += $vDatosIp[3][$nP]['valpdfxx'];
          break;
        }
      } else {
        $mIP[$_POST['cComId_IPA'.($i+1)]]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] = $vDatosIp[3][$nP]['valpdfxx'];
        $mIP[$_POST['cComId_IPA'.($i+1)]]['simbolox'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] = $vDatosIp[3][$nP]['simbolox'];
      }      
    }

    $mIP[$_POST['cComId_IPA'.($i+1)]]['ctoidxxx']  = $_POST['cComId'.$nGrid.'_IPA'.($i+1)];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comobsxx']  = $vDatosIp[0];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comvlrxx'] += $_POST['nComVlr'.$nGrid.'_IPA'.($i+1)];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['compivax']  = $_POST['nComPIva'.$nGrid.'_IPA'.($i+1)]; // Porcentaje IVA
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comvlr01'] += $_POST['nComVIva'.$nGrid.'_IPA'.($i+1)]; // Valor Iva
    //Cantidad FE
    $mIP[$_POST['cComId_IPA'.($i+1)]]['unidadfe']  = $vDatosIp[2];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['canfexxx'] += $vDatosIp[1];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['formacob']  = $vDatosIp[6];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['monedaxx']  = $vDatosIp[7];
  }

  $mCodDat = array();
  foreach ($mIP as $cKey => $mValores) {
    $mCodDat[count($mCodDat)] = $mValores;
  }
  #FIN Agrupo Ingresos Propios

  ##Traigo la Forma de Pago##
  $cFormaPag = "";
  if ($_POST['cComFpag'] != "") {
    //Buscando descripcion
    $cFormaPag = ($_POST['cComFpag'] == 1) ? "CONTADO" : "CREDITO";
  }

  ##Fin Codigo para imprimir los ingresos para terceros ##
  ##Recorro Grilla de IP para saber si se habilita la impresion del bloque de Ingresos Propios##
  ##Fin de Recorro Grilla de IP para saber si se habilita la impresion del bloque de Ingresos Propios##
  ## Comienzo a pintar Vista Previa de Factura##
  //////////////////////////////////////////////////////////////////////////

  class PDF extends FPDF {
    function Header() {
      global $cAlfa;   global $cRoot;     global $cPlesk_Skin_Directory;       global $vSysStr; global $gAnoHas;
      global $gCcoId;  global $gSccId;    global $gMesDes; global $gAnoDes;    global $gMesHas; global $cNomDirCue;
      global $cUsrNom; global $cCcoDes;   global $cScoDes; global $nPag;       global $vAgeDat; global $cPaiOri; global $cPaiOriDes;
      global $vResDat; global $cDocId;    global $vCliDat; global $vCiuDat;    global $vDceDat; global $cFormaPag;
      global $vConDat; global $cDeposito; global $vOrigen; global $cObsGen;    global $cComId;  global $cComCod;
      global $cSucId;  global $cPedido;   global $cPedOrd; global $cLugIngDes; global $cObsSchenker; 

      #Texto FIJO#
      $cIca = "";
      switch ($cSucId) {
        case "CTG": $cIca = "8"; break;
        case "CUC": $cIca = "11"; break;
        case "BUN": $cIca = "10"; break;
        case "IPI": $cIca = "6"; break;
        case "SMR": $cIca = "7"; break;
        case "BAQ": $cIca = "6.9"; break;
        case "CLO": $cIca = "11"; break;
        case "BOG": $cIca = "9,66"; break;
      }

      ## Valido si el comprobante es F-005 en ciudad Imprimo RIONEGRO ##
      $cCiuFact = $cLugIngDes;
      $cIdCompr = $cComId."-".$cComCod;
      if ($cIdCompr == "F-005") {
        $cCiuFact = "RIONEGRO";
      }

      $posy	= 8;##PRIMERA POSICION DE Y##
      $posx	= 10;

      ## Marca de agua copia no valida
      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/copianovalida.jpg',20,50,180,180);

      ##Logo
      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logosiaco.jpg',20,28,50);

      ## Agencia - Nit ##
      $this->SetFont('verdanab','',10);
      $this->setXY($posx+120,$posy-2);
      $this->Cell(40,4,"AGENCIA DE ADUANAS SIACO SAS NIVEL 1",0,0,'C');
      $this->Ln(4);
      $this->setX($posx+120);
      $this->Cell(40,4,"NIT: ". number_format($vSysStr['financiero_nit_agencia_aduanas'], 0, '', '.'). "-" . f_Digito_Verificacion($vSysStr['financiero_nit_agencia_aduanas']),0,0,'C');
      $this->Ln(4);
      $this->setX($posx+120);
      $this->Cell(40,4,"Cod.: 0413",0,0,'C');

      #Texto FIJO#
      $this->Ln(5);
      $this->SetFont('verdana','',6);
      $this->setX($posx+120);
      $this->Cell(40,4,"SOMOS IVA REGIMEN COMUN ICA $cIca X 1000 ACT 5229",0,0,'C');
      $this->Ln(3);
      $this->setX($posx+120);
      $this->Cell(40,4,"SOMOS AGENTES RETENEDORES DE IVA",0,0,'C');
      $this->Ln(3.5);
      $this->SetFont('verdana','',6);
      $this->setX($posx+100);
      $this->MultiCell(80,3,utf8_decode("SOMOS AUTORRETENEDORES DEL IMPUESTO SOBRE LA RENTA SEGÚN RESOLUCIÓN No 9016 DEL 9 DE DICIEMBRE DE 2020."),0,'C');
      $this->SetFont('verdanab','',6);
      $this->setX($posx+120);
      $this->Cell(40,3,"NO SOMOS GRANDES CONTRIBUYENTES",0,0,'C');
      #FIN Texto FIJO#

      ##Linea para imprimir la resolucion de la factura##
      $this->SetFont('verdana','',6);
      $cResolucion = date("Y-m-d")."  ".date("H:i:s")."  FACTURACION POR COMPUTADOR RESOLUCION {$vResDat['resclaxx']} DIAN No {$vResDat['residxxx']} DE ";
      $cResolucion .= "{$vResDat['resfdexx']} PREFIJO: {$vResDat['resprexx']} DEL {$vResDat['resdesxx']} AL {$vResDat['reshasxx']}";

      $nPosicionY = 240;
      if ($vResDat['resvigme'] != "") {
        $cResolucion.= " VIGENCIA {$vResDat['resvigme']} ";
        if ($vResDat['resvigme'] == "1") {
          $cResolucion.= "MES";
          $nPosicionY = 250;
        } else {
          $cResolucion.= "MESES";
          $nPosicionY = 252;
        }
      }
      $this->RotatedText(6,$nPosicionY,$cResolucion,90);//14,220
      ##Fin impresion de resolucion##

      ## Numero de la factura
      $posy += 27;
      $this->SetFont('verdanab','',8);
      $this->setXY($posx+100, $posy);
      $this->Cell(23,10, utf8_decode("Factura Electrónica de Venta No. ") .$vResDat['resprexx']."-"."XXXXX",0,0,'L');
      $this->RoundedRect($posx+90, $posy+1, 103, 7, 3, '12');

      #Ciuydd y Fecha Factura ##
			$posy += 10;
      $this->setXY($posx+92, $posy);
      $this->SetFont('verdana','',7);
      $this->Cell(25,4, utf8_decode("Ciudad y Fecha de generación"),0,0,'L');
      $this->Ln(4);
      $this->setX($posx+92);
      $this->SetFont('verdanab','',7);
      $this->Cell(25,4,utf8_decode($cCiuFact),0,0,'L');
      $this->Cell(23,4,$_POST['dRegFCre'],0,0,'R');
			$this->RoundedRect($posx+90, $posy, 50, 8, 3, '1');
      ##Fin Ciudad y Fecha de Factura ##

      ##Numero Do ##
      $this->setXY($posx+145, $posy);
      $this->SetFont('verdana','',7);
      $this->Cell(25,4, utf8_decode("D.O No.:"),0,0,'L');
      $this->Ln(4);
      $this->setX($posx+145);
      $this->SetFont('verdanab','',7);
      $this->Cell(35,4,$cDocId,0,0,'R');
      $this->RoundedRect($posx+143, $posy, 50, 8, 3, '2');
      ##Fin Numero Do ##
      
      ##Fecha Vencimiento ##
      $posy += 10;
      $this->SetFillColor(230, 230, 230);
      $this->RoundedRect($posx+90, $posy, 50, 9, 3, '1', 'F');
      $this->setXY($posx+92, $posy);
      $this->SetFont('verdana','',7);
      $this->Cell(25,4, utf8_decode("Fecha vencimiento:"),0,0,'R');
      $this->Ln(4);
      $this->setX($posx+92);
      $this->SetFont('verdanab','',7);
      $dFecVen = date("Y-m-d",mktime(0,0,0,substr($_POST['dRegFCre'],5,2),substr($_POST['dRegFCre'],8,2)+$_POST['cTerPla'],substr($_POST['dRegFCre'],0,4)));
      $this->Cell(48,4,$dFecVen,0,0,'R');
      ##Fin Fecha Vencimiento ##

      //Numero del Pedido
      $this->setXY($posx+145,$posy);
      $this->SetFont('verdana','',7);
      $this->Cell(25,4, utf8_decode("Pedido:"),0,0,'L');
      $this->Ln(4);
      $this->setX($posx+145);
      $this->SetFont('verdanab','',7);
      // Valida si el facturar a es SODIMAC para pintar el pedido con valor '1'
      if ($vCliDat['CLIIDXXX'] == '800242106') {
        $this->Cell(45,4,"1",0,0,'C');
      } elseif ($vCliDat['CLIIDXXX'] == '900736182') {
        // Valida si el facturar a es APPLE para pintar el valor parametrizado en la variable del sistema
        $this->Cell(30,4, $vSysStr['siacosia_facturacion_referencia_apple'],0,0,'L');
      } else {
        $this->Cell(30,4, substr(($cPedOrd != "") ? $cPedOrd : $cPedido,0,26),0,0,'L');
      }
      $this->RoundedRect($posx+143, $posy, 50, 8, 3, '2');

      ##Datos del Cliente##
      $posy += 11;
      $posx	= 10;
	    //Nombre del cliente
      $this->SetFont('verdanab','',7);
      $this->setXY($posx, $posy);
      $this->Cell(23,4,"Cliente: ",0,0,'L');
      $this->setX($posx+23);
      $this->SetFont('verdana','',7);
      $this->Cell(100,4, substr($vCliDat['CLINOMXX'], 0, 53),0,0,'L');
      //Nit
      $this->setX($posx+112);
      $this->SetFont('verdanab','',7);
      $this->Cell(20,4," Nit: ". $vCliDat['CLIIDXXX']."-".f_Digito_Verificacion($vCliDat['CLIIDXXX']),0,0,'L');

      $this->Ln(4);
      //Ciudad
      $this->setX($posx);
      $this->SetFont('verdanab','',7);
      $this->Cell(23,4,"Ciudad: ",0,0,'L');
      $this->setX($posx+23);
      $this->SetFont('verdana','',7);
      $this->Cell(20,4,$vCiuDat['CIUDESXX'],0,0,'L');

      $this->Ln(4);
      //Direccion
      $this->setX($posx);
      $this->SetFont('verdanab','',7);
      $this->Cell(23,4,utf8_decode("Dirección: "),0,0,'L');
      $this->setX($posx+23);
      $this->SetFont('verdana','',7); 
      $this->Cell(20,4,$vCliDat['CLIDIRXX'],0,0,'L');

      $this->Ln(4);
      //Telefono
      $this->setX($posx);
      $this->SetFont('verdanab','',7);
      $this->Cell(23,4,"Telefono: ",0,0,'L');
      $this->setX($posx+23);
      $this->SetFont('verdana','',7); 
      $this->Cell(20,4,$vCliDat['CLITELXX'],0,0,'L');

			$this->Ln(4);
      //Observaciones  
      $this->setX($posx);
      $this->SetFont('verdanab','',7);
      $this->Cell(23,4,"Observaciones: ",0,0,'L');
      $this->Ln(0.5);
      $this->setX($posx+23);
      $this->SetFont('verdana','',7);
      
      $cObsSchenker = ($cObsSchenker != "") ? "DO SCHENKER: ".substr($cObsSchenker, 0 ,-2).". " : "";
      $cObsGen = str_replace(array(chr(27),chr(9),chr(13),chr(10))," ",$cObsSchenker.$cObsGen);

      $this->MultiCell(117,3, $cObsGen,0,'L');
      $this->Ln(0.5);
      //Forma de pago
      $this->setX($posx);
      $this->SetFont('verdanab','',7);
      $this->Cell(23,4,"Forma de Pago: ",0,0,'L');
      $this->setX($posx+23);
      $this->SetFont('verdana','',7); 
      $this->Cell(20,4,$cFormaPag,0,0,'L');
  
      $this->Ln(4);
      //Medio de pago
      $this->setX($posx);
      $this->SetFont('verdanab','',7);
      $this->Cell(23,4,"Medio de Pago: ",0,0,'L');
      $this->setX($posx+23);
      $this->SetFont('verdana','',7); 
      $this->Cell(20,4, utf8_decode($_POST['cMePagDes']),0,0,'L');
      //Recuadro
      $this->RoundedRect($posx, $posy-1, 140, 35, 3, '1');
      ##Fin Datos del CLiente##

      ##Datos Generales de Do ##
      //Origen
      $this->setXY($posx+143, $posy);
      $this->SetFont('verdana','',6);
      $this->Cell(23,4,"ORIGEN: ",0,0,'L');
      $this->setX($posx+162);
      $this->SetFont('verdanab','',6);
      $this->Cell(23,4, $cPaiOriDes,0,0,'L');

      $this->Ln(5.5);
      //Servicio:
      $this->setX($posx+143);
      $this->SetFont('verdana','',6);
      $this->Cell(23,4,"SERVICIO: ",0,0,'L');
      $this->setX($posx+162);
      $this->SetFont('verdanab','',6);
      $this->Cell(23,4, $vDceDat['doctipxx'],0,0,'L');

      $this->Ln(5.5);
      //Deposito:
      $this->setX($posx+143);
      $this->SetFont('verdana','',6);
      $this->Cell(23,4, utf8_decode("DEPÓSITO: "),0,0,'L');
      //Se busca por el codigo del Pedido Traido de la SIAI0200 para buscar la descripcion del deposito en la SIAI0110
      $this->setX($posx+162);
      $this->SetFont('verdanab','',6);
      $this->Cell(23,4, substr($cDeposito,0,18),0,0,'L');

      $this->Ln(5.5);
      //Contacto:
      $this->setX($posx+143);
      $this->SetFont('verdana','',6);
      $this->Cell(23,4,"CONTACTO: ",0,0,'L');
      $this->setX($posx+162);
      $this->SetFont('verdanab','',6);
      $this->Cell(23,4, substr($vConDat['CLINOMXX'],0,18),0,0,'L');

      $this->Ln(5.5);
      //Director de Cuenta:
      $this->setX($posx+143);
      $this->SetFont('verdana','',6);
      $this->Cell(23,4,"DIR. CUENTA: ",0,0,'L');
      $this->setX($posx+162);
      $this->SetFont('verdanab','',6);
      $this->Cell(23,4, substr($cNomDirCue,0,18),0,0,'L');
      //Recuadro
      $this->RoundedRect($posx+143, $posy-1, 50, 35, 3, '2');
      ##Fin Datos Generales de Do ##

      $posy += 35;
      $this->setXY($posx, $posy);
      $this->SetFont('verdanab','',8);
      $this->Cell(23,4,"REINTREGROS DE CAPITAL",0,0,'L');
      $this->Ln(5);

      ##Cabecera de los items PCC
      $this->setX($posx);
      $this->SetFont('verdanab','',7);
      $this->Cell(20,4,"Cantidad",0,0,'C');
      $this->Cell(117,4,utf8_decode("Descripción"),0,0,'C');
      $this->Cell(28,4,"Valor Unitario",0,0,'C');
      $this->Cell(28,4,"Valor Total",0,0,'C');

      ##Recuadro de los items PCC
      $posy += 5;
      $this->Rect($posx, $posy, 193, 31);
      $this->Line($posx+20, $posy, $posx+20, $posy+31);
      $this->Line($posx+137, $posy, $posx+137, $posy+31);
      $this->Line($posx+165, $posy, $posx+165, $posy+31);

      $posy += 31;
      $this->setXY($posx+120,$posy+4);
      $this->SetFont('verdanab','',8);
      $this->Cell(23,4,"Valor Neto: ",0,0,'L');
      ##Flecha
      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/arrow-right_grey01.jpg',$posx+145,$posy+5,14);
      //Recuadro valor Neto
      $this->RoundedRect($posx+163, $posy+3, 30, 5, 2, '2', 'F');
  
      $this->Ln(3);
      $this->setX($posx);
      $this->SetFont('verdanab','',8);
      $this->Cell(23,4,"INGRESOS PROPIOS",0,0,'L');
        
      $this->Ln(4);
      ##Cabecera de los items IP
      $this->setX($posx);
      $this->SetFont('verdanab','',7);
      $this->Cell(20,4,utf8_decode("Código"),0,0,'C');
      $this->Cell(20,4,"Cantidad",0,0,'C');
      $this->Cell(107,4,utf8_decode("Descripción"),0,0,'C');
      $this->Cell(23,4,"Valor Unitario",0,0,'C');
      $this->Cell(23,4,"Valor Total",0,0,'C');

      ##Recuadro de los items IP
      $posy += 11;
      $this->Rect($posx, $posy, 193, 34);
      $this->Line($posx+20, $posy, $posx+20, $posy+34);
      $this->Line($posx+40, $posy, $posx+40, $posy+34);
      $this->Line($posx+147, $posy, $posx+147, $posy+34);
      $this->Line($posx+170, $posy, $posx+170, $posy+34);
      
      $posy += 34;
      $this->setXY($posx+133, $posy);
      $this->SetFont('verdanab','',7);
      $this->Cell(23,4,"Total Ingresos Propios ",0,0,'L');
      $this->Ln(4);
      $this->setX($posx+126);
      $this->SetFont('verdanab','',8);
      $this->Cell(33,4,"Iva",0,0,'R');

      ##Recuadro de los Totales de IP
      $this->Rect($posx, $posy, 193, 15);

      ##Recuadro Gris de Total factura - Anticipo - Saldo a Favor##
      $this->RoundedRect($posx+0.5, 183, 65, 13, 0, '1', 'F');

      ##Recuadro Gris Valor en letras##
      $this->RoundedRect($posx, 198, 97, 15, 3, '1', 'F');

      ##Recuadro Gris Valor en letras##
      $this->RoundedRect($posx+163, 198, 30, 5, 2, '2', 'F');

    }//Function Header() {

    function Footer() {
      global $cPlesk_Skin_Directory;

      $posx = 5;
      $posy = 183;

      $this->SetFillColor(230, 230, 230);
      $this->setXY($posx+5, $posy);
      $this->SetFont('verdanab','',7);
      $this->Cell(23,4,"TOTAL FACTURA:",0,0,'L');
      $this->Ln(4);
      $this->setX($posx+5);
      $this->Cell(33,4,"TOTAL ANTICIPO RECIBIDO:",0,0,'L');
      $this->Ln(4);
      $this->setX($posx+5);
      $this->Cell(33,4,"SALDO A FAVOR DEL CLIENTE:",0,0,'L');

      $posy += 15;
      $this->setXY($posx+5, $posy);
      $this->SetFont('verdanab','',8);
      $this->Cell(23,5,"SON:",0,0,'L');

      $this->setXY($posx+130, $posy);
      $this->SetFont('verdanab','',10);
      $this->Cell(33,5,"Total Factura:",0,0,'L');

      $this->Ln(6);
      $this->RoundedRect($posx+105, $posy+6, 60, 5, 0, '', 'F');
      $this->setX($posx+110);
      $this->SetFont('verdanab','',10);
      $this->Cell(33,5,"Anticipo Recibido:",0,0,'L');
      //Flecha
      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/arrow-right_grey02.jpg',$posx+150,$posy+7.5,14);
      //Recuadro valor anticipo
      $this->RoundedRect($posx+168, $posy+6, 30, 5, 2, '2');

      $this->Ln(6);
      $this->RoundedRect($posx+105, $posy+12, 60, 5, 0, '', 'F');
      $this->setX($posx+110);
      $this->SetFont('verdanab','',10);
      $this->Cell(38,5,"SALDO",0,0,'R');
      ##Flecha
      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/arrow-right_grey02.jpg',$posx+150,$posy+13.5,14);
      //Recuadro valor saldo
      $this->RoundedRect($posx+168, $posy+12, 30, 5, 2, '2');

      $posy += 20;
      $nAltoCell = 2.5;
      $this->setXY($posx+5, $posy);
      $this->SetFont('verdana','',5);
      $this->Cell(23,$nAltoCell,utf8_decode("1. Esta factura de venta es un Título Valor y se expide de acuerdo al Artículo 774 del Código de Comercio,"),0,0,'L');
      $this->Ln($nAltoCell);
      $this->setX($posx+5);
      $this->Cell(23,$nAltoCell,utf8_decode("una vez vencido este documento generará interés de mora a la tasa máxima autorizada."),0,0,'L');
      $this->Ln($nAltoCell);
      $this->setX($posx+5);
      $this->Cell(23,$nAltoCell,utf8_decode("2. Pasados 5 días después de recibida esta factura se entenderá aceptada y no habrá derecho a reclamo"),0,0,'L');
      $this->Ln($nAltoCell);
      $this->setX($posx+5);
      $this->Cell(23,$nAltoCell,utf8_decode("posterior ni a devolución de la misma."),0,0,'L');
      $this->Ln($nAltoCell);
      $this->setX($posx+5);
      $this->Cell(23,$nAltoCell,utf8_decode("3. Para todos los efectos legales , el domicilio contractual de las partes, será la ciudad de Bogotá D.C."),0,0,'L');
      $this->Ln($nAltoCell);
      $this->setX($posx+5);
      $this->Cell(23,$nAltoCell,utf8_decode("4. Números de cuenta para que realice transferencia bancaria: Cuenta Corriente Banco de Occidente No."),0,0,'L');
      $this->Ln($nAltoCell);
      $this->setX($posx+5);
      $this->Cell(23,$nAltoCell,utf8_decode("231-044322, Cuenta Corriente Bancolombia No. 03114844657, Cuenta Corriente Banco Itaú No. 011220399."),0,0,'L');
      //Recuadro notas finales
      $this->RoundedRect($posx+5, $posy-1, 98, 20, 3, '4');

      ##Elaborado
      $this->setXY($posx+105, $posy);
      $this->SetFont('verdanab','',6);
      $this->Cell(43,4,"ELABORADO",0,0,'C');

      $this->setXY($posx+107, $posy+10);
      $this->Cell(40,4,"FIRMA Y SELLO SIACIO S.A.S.",0,0,'C');
      //Recuadro notas finales
      $this->Line($posx+105, $posy+10, $posx+150, $posy+10);
      $this->Rect($posx+105, $posy-1, 45, 20);

      ##Firma
      $this->setXY($posx+154, $posy);
      $this->SetFont('verdanab','',6);
      $this->Cell(40,4,"FECHA RECIBIDO",0,0,'L');
      
      $this->setXY($posx+154, $posy+8);
      $this->SetFont('verdanab', '', 10);
      $this->SetTextColor(219, 219, 219);
      $this->Cell(40, 4, utf8_decode("ACEPTADA"),0,0,'C');
      $this->SetTextColor(0, 0, 0);

      $this->SetFont('verdanab', '', 6);
      $this->setXY($posx+154, $posy+15);
      $this->Cell(40,4,"NOMBRE. C.C / NIT",0,0,'C');
      //Recuadro notas finales
      $this->Line($posx+152, $posy+15, $posx+198, $posy+15);
      $this->RoundedRect($posx+152, $posy-1, 46, 20, 3, '3');

      ### Firma electronica - CUFE - QR ###
      $posy += 20;
      $this->setXY($posx+5,$posy);
      $this->SetFont('verdanab','',5);
      $this->Cell(30,3, utf8_decode("FECHA Y HORA VALIDACIÓN DIAN: "),0,0,'L');

      $this->setXY($posx+118,$posy);
      $this->SetFont('verdanab','',7);
      $this->Cell(14,5,"CUFE: ",0,0,'L');
      $this->setX($posx+128);
      $this->SetFont('verdana','',6);
      $this->MultiCell(70,2.5, "",0,'L');

      $this->setXY($posx+5,$posy+3);
      $this->SetFont('verdanab','',7);
      $this->Cell(14,5,utf8_decode("Representación Impresa de la Factura Electrónica: "),0,0,'L');
      $this->Ln(4);
      $this->setX($posx+5);
      $this->Cell(14,5,utf8_decode("Firma Electrónica "),0,0,'L');
      $this->Ln(4);
      $this->SetFont('verdana','',5);
      $this->setX($posx+5);
      $this->MultiCell(150,2, "",0,'L');

      //Direccion de Ciudades
      $posy = 262;
      $this->setXY($posx+4, $posy);
      $this->SetFont('verdanab','',6);
      $this->Cell(23,4,"BOGOTA D.C",0,0,'C');
      $this->Ln(4);
      $this->setX($posx-5);
      $this->SetFont('verdana','',6);
      $this->MultiCell(40,2.5,"Ak 97 No. 24C-80\n PBX: 425 2600 / 50",0,'C');

      $this->setXY($posx+34, $posy);
      $this->SetFont('verdanab','',6);
      $this->Cell(23,4,"CARTAGENA",0,0,'C');
      $this->Ln(4);
      $this->setX($posx+25);
      $this->SetFont('verdana','',5);
      $this->MultiCell(40,2.5,utf8_decode("Manga Callejón Porto\n Cra. 18 N0. 25-134\n Tel.: (095) 660 6422 / 5038"),0,'C');

      $this->setXY($posx+68, $posy);
      $this->SetFont('verdanab','',6);
      $this->Cell(23,4,"BUENAVENTURA",0,0,'C');
      $this->Ln(4);
      $this->setX($posx+60);
      $this->SetFont('verdana','',5);
      $this->MultiCell(40,2.5,"Cra. 3A No. 2-13\n Altos Davivienda\n Tel.: (092) 240 0269 / 70",0,'C');

      $this->setXY($posx+103, $posy);
      $this->SetFont('verdanab','',6);
      $this->Cell(23,4,"RIONEGRO",0,0,'C');
      $this->Ln(4);
      $this->setX($posx+95);
      $this->SetFont('verdana','',5);
      $this->MultiCell(40,2.5,"Glorieta del Aeropuerto\n Jose Maria Cordova\n Centro ciudad Karga Fase 1\n Rionegro, Antioquia Of. 302",0,'C');

      $this->setXY($posx+135, $posy);
      $this->SetFont('verdanab','',6);
      $this->Cell(23,4,"IPIALES",0,0,'C');
      $this->Ln(4);
      $this->setX($posx+128);
      $this->SetFont('verdana','',5);
      $this->MultiCell(40,2.5,utf8_decode("Centro Comercial Rumichaca\n Local 23\n Teléfono: 3156001285"),0,'C');

      $this->setXY($posx+172, $posy);
      $this->SetFont('verdanab','',6);
      $this->Cell(23,4,"CALI",0,0,'C');
      $this->Ln(4);
      $this->setX($posx+163);
      $this->SetFont('verdana','',5);
      $this->MultiCell(40,2.5,utf8_decode("Calle 64 Norte No 05B-26\n Oficina 414 Centro Empresa.\n Teléfono: 6642883"),0,'C');
        
      //Copia para VISTA PREVIA
      $this->SetFont('verdanab','',6);
      $this->setXY(60,256);
      $this->Cell(110,3,"VISTA PREVIA",0,0,'R');

      $this->setXY(165,256);
      $this->Cell(40,3,"PAGINA: ".$this->PageNo()." DE {nb}",0,0,'R');
      $this->SetFont('verdana','',7);

    }//function Footer() {

    function RotatedText($x,$y,$txt,$angle){
      //Text rotated around its origin
      $this->Rotate($angle,$x,$y);
      $this->Text($x,$y,$txt);
      $this->Rotate(0);
    }

    function RotatedImage($file,$x,$y,$w,$h,$angle){
      //Image rotated around its upper-left corner
      $this->Rotate($angle,$x,$y);
      $this->Image($file,$x,$y,$w,$h);
      $this->Rotate(0);
    }

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

    function _endpage(){
      if($this->angle!=0) {
        $this->angle=0;
        $this->_out('Q');
      }
      parent::_endpage();
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
      for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h=3*$nb;
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
        $this->MultiCell($w,3,$data[$i],0,$a);
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

    function RoundedRect($x, $y, $w, $h, $r, $corners = '1234', $style = '') {
      $k = $this->k;
      $hp = $this->h;
      if($style=='F')
          $op='f';
      elseif($style=='FD' || $style=='DF')
          $op='B';
      else
          $op='S';
      $MyArc = 4/3 * (sqrt(2) - 1);
      $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));

      $xc = $x+$w-$r;
      $yc = $y+$r;
      $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));
      if (strpos($corners, '2')===false)
          $this->_out(sprintf('%.2F %.2F l', ($x+$w)*$k,($hp-$y)*$k ));
      else
          $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);

      $xc = $x+$w-$r;
      $yc = $y+$h-$r;
      $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
      if (strpos($corners, '3')===false)
          $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-($y+$h))*$k));
      else
          $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);

      $xc = $x+$r;
      $yc = $y+$h-$r;
      $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
      if (strpos($corners, '4')===false)
          $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-($y+$h))*$k));
      else
          $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);

      $xc = $x+$r ;
      $yc = $y+$r;
      $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
      if (strpos($corners, '1')===false)
      {
          $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$y)*$k ));
          $this->_out(sprintf('%.2F %.2F l',($x+$r)*$k,($hp-$y)*$k ));
      }
      else
          $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
      $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3) {
      $h = $this->h;
      $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
          $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }
  }//class PDF extends FPDF {

  // $mIngTer = array_merge($mIngTer, $mIngTer, $mIngTer, $mIngTer);
  // $mCodDat = array_merge($mCodDat, $mCodDat, $mCodDat, $mCodDat, $mCodDat);

  $pdf = new PDF('P','mm','Letter');  //Error al invocar la clase
  global $vUserNom;
  $pdf->AddFont('verdana','','verdana.php');
  $pdf->AddFont('verdanab','','verdanab.php');
  $pdf->SetFont('verdana','',8);
  $pdf->AliasNbPages();
  $pdf->SetMargins(0,0,0);
  $pdf->SetAutoPageBreak(0,0);

  $nBanPCC = 0;     $nBanIP = 0;
  $nPosPCC = 0;     $nPosIP = 0;
  $nSubTotPcc = 0;  $nSubToIP = 0;
  $nVrlTotalSinIva  = 0;
  $nVrlTotalBaseIva = 0;
  $nVrlTotalIva     = 0;
  $nPorcenIva       = 0;

  while ($nBanPCC == 0 || $nBanIP == 0) {
    $pdf->AddPage();

    ##Imprimo Pagos a Terceros ##
    if(count($mIngTer) > 0) { //Si la matriz de Pcc tiene registros

      if ($nBanPCC == 0) {
        $posy = 110;
        $posx = 10;

        ##Imprimo Detalle de Pagos a Terceros e Ingresos Propios ##
        $pdf->SetFont('verdanab','',8);
        $pdf->setXY($posx,$posy);

        $pdf->SetWidths(array(20,117,28,28));
        $pdf->SetAligns(array("C","L","R","R"));

        for($i=$nPosPCC;$i<count($mIngTer);$i++) {

          if (($mIngTer[$i]['nComVlr']+0) != 0) {
            //preparando datos
            $nSubTotPcc += $mIngTer[$i]['nComVlr'];

            //si la moneda es en dolares, para los comprobantes que aplique se debe mostrar la tasa de cambio
            if ($_POST['cMonId'] == "USD") {
              if ($mIngTer[$i]['nTasa'] == "SI") {
                //Recorro el array de la Tasa de Cambio y el comprobante									
                $cCadena = "";
                foreach ($mPccTC[$i] as $cKey => $cValue) {
                  $vAuxDoc = array_unique($cValue);
                  $cComprobante = implode("/", $vAuxDoc);
                  $cCadena .= $cComprobante.", T.C. ".number_format($cKey,2,',','.')."/";
                }					
                $mIngTer[$i]['ccomcsc3'] = substr($cCadena, 0, -1);
              }
            } else {
              //Si la factura es en COP, para los comprobantes que aplique se debe mostrar la tasa pactada
              //Recorro el array de la Tasa de pactada y el comprobante									
              $cCadena = "";
              foreach ($mPccTP[$i] as $cKey => $cValue) {
                $vAuxDoc = array_unique($cValue);
                $cComprobante = implode("/", $vAuxDoc);
                if($cKey != "SIN_TASAPAC"){
                  $cCadena .= $cComprobante.", T.R.M. ".number_format($cKey,2,',','.')."/";
                }else{
                  $cCadena .= $cComprobante."/";
                }
              }
              $mIngTer[$i]['ccomcsc3'] = substr($cCadena, 0, -1);
            }

            $cTerceros  = $mIngTer[$i]['cNomTer'];
            $cTerceros .= ($mIngTer[$i]['nNitTercero'] != "") ? "-NIT:".$mIngTer[$i]['nNitTercero'] : "";

            $mIngTer[$i]['ccomcsc3'] = (substr($mIngTer[$i]['ccomcsc3'],0,1) == "/") ? substr($mIngTer[$i]['ccomcsc3'],1,strlen($mIngTer[$i]['ccomcsc3'])) : $mIngTer[$i]['ccomcsc3'];
						$cTerceros .= ($mIngTer[$i]['ccomcsc3']!="") ? " [DOCS: ".$mIngTer[$i]['ccomcsc3'] : "";
            $cTerceros .= ($mIngTer[$i]['nTasa'] != "" && $mIngTer[$i]['nTasa'] != "SI") ? "  ".$mIngTer[$i]['nTasa'] : "";

            $nVrlSinIva = 0;
            if (($mIngTer[$i]['nVlrIva']+0) > 0) {
              $mIngTer[$i]['nVlrIva'] = ($_POST['cMonId'] == "USD") ? ($mIngTer[$i]['nVlrIva']/$_POST['nTasaCambio']) : $mIngTer[$i]['nVlrIva'];
              $nVrlSinIva = ($mIngTer[$i]['nComVlr']+0) - ($mIngTer[$i]['nVlrIva']+0);
            }
            if (($nVrlSinIva+0) != 0 || ($mIngTer[$i]['nVlrIva']+0) != 0) {
              $cTerceros .= ($mIngTer[$i]['ccomcsc3']!="") ? "" : " [";

              if($_POST['cMonId'] == "USD"){
                $cTerceros .=" VLR. SIN IVA $".number_format($nVrlSinIva,2,',','.');
                $cTerceros .=" IVA $".number_format($mIngTer[$i]['nVlrIva'],2,',','.')."]";
              }else{
                $cTerceros .=" VLR. SIN IVA $".number_format($nVrlSinIva,0,',','.');
                $cTerceros .=" IVA $".number_format($mIngTer[$i]['nVlrIva'],0,',','.')."]";
              }

              $nVrlTotalSinIva  += $nVrlSinIva;
              $nVrlTotalBaseIva += (($mIngTer[$i]['nVlrIva']+0) > 0) ? $nVrlSinIva : 0;
              $nVrlTotalIva     += $mIngTer[$i]['nVlrIva'];
            } else {
              $cTerceros .= ($mIngTer[$i]['ccomcsc3']!="") ? "]" : "";
            }

            $pdf->SetFont('verdana','',6);
            $pdf->setX($posx);
            $pdf->Row(array("1",
                            trim($cTerceros),
                            ($_POST['cMonId'] == "USD") ? number_format($mIngTer[$i]['nComVlr'],2,',','.') : number_format($mIngTer[$i]['nComVlr'],0,',','.'),
                            ($_POST['cMonId'] == "USD") ? number_format($mIngTer[$i]['nComVlr'],2,',','.') : number_format($mIngTer[$i]['nComVlr'],0,',','.')));

            if ($pdf->getY() >= 133) {
              //es el ultimo
              $nPosPCC = $i+1;
              $i=count($mIngTer);
            }
          }
        }//for($i=0;$i<count($mIngTer);$i++){

        if($i == count($mIngTer)) {
          $nPosPCC = $i;
        }
        if ($nPosPCC == count($mIngTer)) {
          $nBanPCC = 1;
        }
      } ## if ($nBanPCC == 0) { ##
    } else {
      $nBanPCC = 1;
    } ## if(count($mIngTer) > 0) { ##
    ##Fin Imprimo Pagos a Terceros ##

    ##Imprimo Ingresos Propios##
    if(count($mCodDat) > 0) { //Valido si la Secuencia de la Grilla de Ip viene mayor a cero para imprimir bloque de INGRESOS PROPIOS
      if ($nBanIP == 0) {
        $posy = 152;
        $posx = 10;

        ##Imprimo Detalle Ingresos Propios ##
        $pdf->SetFont('verdanab','',8);
        $pdf->setXY($posx,$posy);

        $pdf->SetWidths(array(20,20,107,23,23));
        $pdf->SetAligns(array("C","C","L","R","R"));

        for($nIP=$nPosIP;$nIP<count($mCodDat);$nIP++) {
          if($mCodDat[$nIP]['comvlrxx'] > 0){
            $nSubToIP += $mCodDat[$nIP]['comvlrxx'];
            // Obtiene el valor del porcentaje del primer Ingreso Propio
            if ($nPorcenIva == 0) {
              $nPorcenIva = number_format($mCodDat[$nIP]['compivax'], 0);
            }

            //la forma de cobro + cantidades + moneda
            $cValor = $mCodDat[$nIP]['formacob']." ";
            foreach ($mCodDat[$nIP]['itemcanx'] as $cKey => $cValue) {
              //Cantidad de decimales
              $nCanDecCon = (substr_count($cValue,".") > 0) ? 2 : 0;

              //Formateando valor
              $cValue = ($mCodDat[$nIP]['simbolox'][$cKey] == "$") ? number_format(round($cValue,$nCanDecCon),$nCanDecCon,'.',',') : $cValue;

              //Simbolo que acompaña la condicion especial
              $cSimbolo   = ($mCodDat[$nIP]['simbolox'][$cKey] != "") ? $mCodDat[$nIP]['simbolox'][$cKey]." " : "";

              $cValor    .= str_replace("_"," ",$cKey).": ".$cSimbolo.$cValue." ";
            }
            $cValor .= $mCodDat[$nIP]['monedaxx']." ";
            $cValor = " [".trim($cValor)."]";

            $nValorUni = ($mCodDat[$nIP]['unidadfe'] != "A9" && $mCodDat[$nIP]['canfexxx'] > 0) ? $mCodDat[$nIP]['comvlrxx']/$mCodDat[$nIP]['canfexxx'] : $mCodDat[$nIP]['comvlrxx'];

            $pdf->SetFont('verdana','',6);
            $pdf->setX($posx);
            $pdf->Row(array($mCodDat[$nIP]['ctoidxxx'],
                            number_format((($mCodDat[$nIP]['unidadfe'] == "A9") ? 1 : $mCodDat[$nIP]['canfexxx']),0,',',''),
                            trim($mCodDat[$nIP]['comobsxx']).$cValor,
                            ($_POST['cMonId'] == "USD") ? number_format($nValorUni,2,',','.') : number_format($nValorUni,0,',','.'),
                            ($_POST['cMonId'] == "USD") ? number_format($mCodDat[$nIP]['comvlrxx'],2,',','.') : number_format($mCodDat[$nIP]['comvlrxx'],0,',','.')));

            if($pdf->getY() >= 181){
              //es el ultimo
              $nPosIP = $nIP+1;
              $nIP=count($mCodDat);
            }
          }
        } ## for($nIP=$nPosIP;$nIP<($_POST['nSecuencia_IPA']);$nIP++) { ##

        if($nIP == count($mCodDat)) {
          $nPosIP = $nIP;
        }
        if ($nPosIP == count($mCodDat)) {
          $nBanIP = 1;
        }
      } ##if ($nBanIP == 0) {##
    } else {
      $nBanIP = 1;
    }## if($_POST['nSecuencia_IPA'] > 0) {##
    #Fin Imprimo Ingresos Propios##
  } ##while ($nBanPCC == 0 || $nBanIP == 0) { ##

  ##Total pagos a terceros##
  $pdf->SetFont('verdanab','',8);
  $pdf->setXY(172,141);
  $pdf->Cell(30,4,($_POST['cMonId'] == "USD") ? number_format($nSubTotPcc,2,',','.') : number_format($nSubTotPcc,0,',','.'),0,0,'R');
  ##FIN Total pago a terceros##

  #Total ingresos propios
  $pdf->SetFont('verdanab','',6);
  $pdf->setXY(172,182);
  $pdf->Cell(30,4,($_POST['cMonId'] == "USD") ? number_format($nSubToIP,2,',','.') : number_format($nSubToIP,0,',','.'),0,0,'R');
  #FIN Total ingresos propios

  ##Imprimo el iva
  $nTotIva = 0;
  $pdf->setXY(168,186);
  $pdf->SetFont('verdanab','',8);
  $pdf->Cell(5,4,($nPorcenIva > 0 ? $nPorcenIva : "19") . "%",0,0,'L');
  $pdf->SetFont('verdanab','',6);
  $pdf->Cell(29,4,($_POST['cMonId'] == "USD") ? number_format($_POST['nIPAIva'.$nGrid],2,',','.') : number_format($_POST['nIPAIva'.$nGrid],0,',','.'),0,0,'R');
  #Fin Imprimir valor del iva#

  ##Caculo valor total de la factura##
  $nValorTotal = $_POST['nIPAIva'.$nGrid]+$nSubToIP+$nSubTotPcc;
  $pdf->SetFont('verdanab','',8);
  $pdf->setXY(172,199);
  $pdf->Cell(30,4,($_POST['cMonId'] == "USD") ? number_format($nValorTotal,2,',','.') : number_format($nValorTotal,0,',','.'),0,0,'R');
  ##Fin calulo valor total de la factura##

  ##Busco valor de Anticipo ##
  $nTotalAntRecibido = (abs($_POST['nIPAAnt'.$nGrid]) >= $nValorTotal) ? $nValorTotal : abs($_POST['nIPAAnt'.$nGrid]);
  $pdf->SetFont('verdanab','',8);
  $pdf->setXY(172,205);
  $pdf->Cell(30,4,($_POST['cMonId'] == "USD") ? number_format($nTotalAntRecibido,2,',','.') : number_format($nTotalAntRecibido,0,',','.'),0,0,'R');
  ##FIN Busco valor de Anticipo ##

  ##Imprimo saldo de la factura  ##
  $nSaldoAgencia = (abs($_POST['nIPAAnt'.$nGrid]) >= $nValorTotal) ? 0 : ($nValorTotal-abs($_POST['nIPAAnt'.$nGrid]));
  $pdf->setXY(172,211);
  $pdf->Cell(30,4,($_POST['cMonId'] == "USD") ? number_format($nSaldoAgencia,2,',','.') : number_format($nSaldoAgencia,0,',','.'),0,0,'R');
  ##FIn Imprimo saldo de la factura##
  
  /*** Seccion Total Factura - Total Anticipo Rcibido - Saldo a Favor del Cliente ***/
  ##Valor total de la factura##
  $pdf->SetFont('verdanab','',7);
  $pdf->setXY(45,183);
  $pdf->Cell(30,4,($_POST['cMonId'] == "USD") ? number_format($nValorTotal,2,',','.') : number_format($nValorTotal,0,',','.'),0,0,'R');
  ##Fin valor total de la factura##

  ##Imprimo total del Anticipo Recibido##
  $pdf->SetFont('verdanab','',8);
  $pdf->setXY(45,187);
  $pdf->Cell(30,4,($_POST['cMonId'] == "USD") ? number_format(abs($_POST['nIPAAnt'.$nGrid]),2,',','.') : number_format(abs($_POST['nIPAAnt'.$nGrid]),0,',','.'),0,0,'R');
  ##Fin Imprimo total del Anticipo Recibido##

  $nSaldoFavor = (abs($_POST['nIPAAnt'.$nGrid]) >= $nValorTotal) ? abs(abs($_POST['nIPAAnt'.$nGrid]) - $nValorTotal) : 0;
  ##Imprimo saldo a favor del cliente##
  $pdf->SetFont('verdanab','',8);
  $pdf->setXY(45,191);
  $pdf->Cell(30,4,($_POST['cMonId'] == "USD") ? number_format($nSaldoFavor,2,',','.') : number_format($nSaldoFavor,0,',','.'),0,0,'R');
  ##Fin Imprimo saldo a favor del cliente##
  /*** FIN Seccion Total Factura - Total Anticipo Rcibido - Saldo a Favor del Cliente ***/
  
  ##Imprimo el total de la factura en Letras##
  $nToltalPagar = ($nSaldoFavor > 0) ? $nSaldoFavor : $nSaldoAgencia;
  $posy = 203;
  $pdf->SetFont('verdana','',6);
  if($_POST['cMonId'] == "USD"){
    $alinea = explode("~",f_Words(f_Cifra_Php(abs($nToltalPagar),'DOLAR'),100)); //$nTotPag
  }else{
    $alinea = explode("~",f_Words(f_Cifra_Php(abs($nToltalPagar),'PESO'),100)); //$nTotPag
  }

  for ($n=0;$n<count($alinea);$n++) {
    $pdf->setXY(10,$posy);
    $pdf->Cell(110,3,$alinea[$n],0,0,'L');
    $posy+=2.5;
  }
  ##Fin imprimo talta de la facutra en letras##

  ##Imprimo total pagos a terceros e iva
  $pdf->SetFillColor(230, 230, 230);
  $pdf->RoundedRect(10, 138, 120, 5, 0, '1', 'F');
  $pdf->setXY(10,139);
  $pdf->SetFont('verdana','',6);
  if($_POST['cMonId'] == "USD"){
    $pdf->Cell(113,4,'TOTAL TERCEROS: $'.number_format($nVrlTotalSinIva,2,',','.').' BASE IVA TERCEROS: $'.number_format($nVrlTotalBaseIva,2,',','.').' IVA TERCEROS '.$vSysStr['financiero_porcentaje_iva_compras'].'% : $'.number_format($nVrlTotalIva,2,',','.'),0,0,'L');
    // $pdf->setXY(10,232);
    // $pdf->Cell(113,3,'TRM: '.($_POST['nTasaCambio']+0),0,0,'l');
  }else{
    $pdf->Cell(113,4,'TOTAL TERCEROS: $'.number_format($nVrlTotalSinIva,0,',','.').' BASE IVA TERCEROS: $'.number_format($nVrlTotalBaseIva,0,',','.').' IVA TERCEROS '.$vSysStr['financiero_porcentaje_iva_compras'].'% : $'.number_format($nVrlTotalIva,0,',','.'),0,0,'L');
  }
  ##Fin Imprimo total pagos a terceros e iva

  ##Imprimo el nombre de quien elaboro la faactura##
  $pdf->SetFont('verdana','',7);
  $pdf->setXY(110,222);
  $pdf->Cell(43,4,$vUserNom['USRNOMXX'],0,0,'C');
  ##Fin mprimo el nombre de quien elaboro la faactura##

  $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
  $pdf->Output($cFile);

  if (file_exists($cFile)){
    chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
  } else {
    f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
  }
  echo "<html><script>document.location='$cFile';</script></html>";

  function f_Fecha_Letras($xFecha){
    if ($xFecha==''){
      $xFecfor='';
    }else{
      $fano = substr ($xFecha, 0, 4);
      $fdia = substr ($xFecha, 8, 2);
      $fmes_antes = substr ($xFecha, 5, 2);
      if($fmes_antes=='01')
        $fmes="Enero";
      if($fmes_antes=='02')
        $fmes="Febrero";
      if($fmes_antes=='03')
        $fmes="Marzo";
      if($fmes_antes=='04')
        $fmes="Abril";
      if($fmes_antes=='05')
        $fmes="Mayo";
      if($fmes_antes=='06')
        $fmes="Junio";
      if($fmes_antes=='07')
        $fmes="Julio";
      if($fmes_antes=='08')
        $fmes="Agosto";
      if($fmes_antes=='09')
        $fmes="Septiembre";
      if($fmes_antes=='10')
        $fmes="Octubre";
      if($fmes_antes=='11')
        $fmes="Noviembre";
      if($fmes_antes=='12')
        $fmes="Diciembre";
      $xFecFor= $fmes." ".$fdia." de ".$fano;
   }
   return ($xFecFor);
 }
?>

<?php
  /**
   * Imprime Vista Previa Factura de Venta MALCO.
   * --- Descripcion: Permite Imprimir Vista Previa de la Factura de Venta.
   * @author Hair Zabala <hair.zabala@opentecnologia.com.co>
   */

  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");

  include("../../../../libs/php/utility.php");
  include("../../../../libs/php/utiliqdo.php");

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

  //Forma de pago
  $cFormaPago = "";
  if ($_POST['cComFpag'] != "") {
    //Buscando descripcion
    $cFormaPago = ($_POST['cComFpag'] == 1) ? "CONTADO" : "CREDITO";
  }

  //Buscando descripcion Medio de Pago
  $vMedPag['mpadesxx'] = "";
  if ($_POST['cMePagId'] != "") {
    $qMedPag  = "SELECT ";
    $qMedPag .= "mpaidxxx, ";
    $qMedPag .= "mpadesxx, ";
    $qMedPag .= "regestxx ";
    $qMedPag .= "FROM $cAlfa.fpar0155 ";
    $qMedPag .= "WHERE ";
    $qMedPag .= "mpaidxxx = \"{$_POST['cMePagId']}\" LIMIT 0,1";
    $xMedPag  = f_MySql("SELECT","",$qMedPag,$xConexion01,"");
    // f_Mensaje(__FILE__,__LINE__,$qMedPag."~ ".mysql_num_rows($xMedPag));
    $vMedPag = mysql_fetch_array($xMedPag);
  }

  $cPlazo = (isset($_POST['cTerPla']) ? $_POST['cTerPla'] : 0)." ".($_POST['cTerPla'] == 1 ? "DIA": "DIAS");

  $mAnticipos = array();
  $qANT  = "SELECT * ";
  $qANT .= "FROM $cAlfa.$cTabla_ANT ";
  $qANT .= "WHERE ";
  $qANT .= "cUsrId_ANT = \"{$_COOKIE['kUsrId']}\" AND ";
  $qANT .= "cFacId_ANT = \"{$_POST['cFacId']}\" ";
  $qANT .= "ORDER BY cComFec_ANT ASC ";
  $xANT  = f_MySql("SELECT","",$qANT,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qANT."~".mysql_num_rows($xANT));
  # Armando Matriz de Anticipos
  while ($xRANT = mysql_fetch_array($xANT)) {
    // echo "<pre>";
    // print_r($xRANT);
    $nInd_mAnticipos = count($mAnticipos);
    $mAnticipos[$nInd_mAnticipos]['comfecxx'] = $xRANT['cComFec_ANT'];
    $mAnticipos[$nInd_mAnticipos]['puctipej'] = $xRANT['cPucTipEj_ANT'];
    $mAnticipos[$nInd_mAnticipos]['comvlrxx'] = $xRANT['nComVlr_ANT'];
    $mAnticipos[$nInd_mAnticipos]['comvlrnf'] = $xRANT['nComVlrNF_ANT'];

    $mAnticipos[$nInd_mAnticipos]['comidxxx'] = $xRANT['cComId_ANT'];
    $mAnticipos[$nInd_mAnticipos]['comcodxx'] = $xRANT['cComCod_ANT'];
    $mAnticipos[$nInd_mAnticipos]['comcscxx'] = $xRANT['cComCsc_ANT'];
    $mAnticipos[$nInd_mAnticipos]['comseqxx'] = $xRANT['cComSeq_ANT'];

    $mAnticipos[$nInd_mAnticipos]['commovxx'] = $xRANT['cComMov_ANT'];
    $mAnticipos[$nInd_mAnticipos]['comidc2x'] = $xRANT['cComIdCB_ANT'];
    $mAnticipos[$nInd_mAnticipos]['comcodc2'] = $xRANT['cComCodCB_ANT'];
    $mAnticipos[$nInd_mAnticipos]['comcscc2'] = $xRANT['cComCscCB_ANT'];
    $mAnticipos[$nInd_mAnticipos]['comseqc2'] = $xRANT['cComSeqCB_ANT'];
  }
  
  /*** Consulto los anticipos en la matriz y se dejan aquellos que no tengan cruce ***/
  $mAnticiposCruce = array();
  $mAnticiposNuevo = array();

  for ($i = 0; $i < count($mAnticipos); $i++) {
    $borrar = 0;

    if ($mAnticipos[$i]['comidc2x'] != "" && $mAnticipos[$i]['comcodc2'] != "" && $mAnticipos[$i]['comcscc2'] != "" && $mAnticipos[$i]['comseqc2'] != "") {
      $cCruce2 = $mAnticipos[$i]['comidc2x'] . "-" . $mAnticipos[$i]['comcodc2'] . "-" . $mAnticipos[$i]['comcscc2'] . "-" . $mAnticipos[$i]['comseqc2'];

      for ($k = 0; $k < count($mAnticipos); $k++) {
        $cCruce1 = $mAnticipos[$k]['comidxxx'] . "-" . $mAnticipos[$k]['comcodxx'] . "-" . $mAnticipos[$k]['comcscxx'] . "-" . $mAnticipos[$k]['comseqxx'];
        if ($cCruce2 == $cCruce1) {
          $borrar = 1;

          $mAnticipos[$i]['comvlrxx'] = ($mAnticipos[$i]['comvlrxx'] == "C") ? $mAnticipos[$i]['comvlrxx'] : ($mAnticipos[$i]['comvlrxx']*-1);

          $mAnticipos[$k]['comvlrxx'] = $mAnticipos[$i]['comvlrxx'] + $mAnticipos[$k]['comvlrxx'];
          if ($mAnticipos[$k]['comvlrxx'] == 0) {
            $nInd_mAnticiposCruce = count($mAnticiposCruce);
            $mAnticiposCruce[$nInd_mAnticiposCruce] = $k;
          }
        }
      }
    }

    if ($borrar == 1) {
      $nInd_mAnticiposCruce = count($mAnticiposCruce);
      $mAnticiposCruce[$nInd_mAnticiposCruce] = $i;
    }
  }

  for ($i = 0; $i < count($mAnticipos); $i++) {
    if (!in_array($i, $mAnticiposCruce)) {
      $nInd_mAnticiposNuevo = count($mAnticiposNuevo);
      $mAnticiposNuevo[$nInd_mAnticiposNuevo] = $mAnticipos[$i];
    }
  }

  $mAnticipos = $mAnticiposNuevo;

  // f_Mensaje(__FILE__,__LINE__,$_POST['cForImp']." Anticipo: ".$nAnticipo);

  ## Traigo el CLINOMXX o Razon Social de la Agencia de Aduana Dietrich Para encabezado de factura ##
  $qAgeDat  = "SELECT ";
  $qAgeDat .= "$cAlfa.SIAI0150.CLIIDXXX, ";
  $qAgeDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX, ";
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

  /*** Nombre del usuario logueado. ***/
  $qUsrNom  = "SELECT USRNOMXX ";
  $qUsrNom .= "FROM $cAlfa.SIAI0003 ";
  $qUsrNom .= "WHERE ";
  $qUsrNom .= "USRIDXXX = \"$kUser\" LIMIT 0,1 ";
  $xUsrNom  = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qUsrNom."~".mysql_num_rows($xUsrNom));
  $vUsrNom  = mysql_fetch_array($xUsrNom);

  ##Consulto en la SIAI0150 Datos del Facturado A: ##
  $qCliDat  = "SELECT ";
  $qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX, ";
  $qCliDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX, ";
  $qCliDat .= "$cAlfa.SIAI0150.CLIDIRXX, ";
  $qCliDat .= "$cAlfa.SIAI0150.CLIDIR3X, ";
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
  $nFilCli  = mysql_num_rows($xCliDat);
  if ($nFilCli > 0) {
    $vCliDat = mysql_fetch_array($xCliDat);
  }
  //f_Mensaje(__FILE__,__LINE__,$qCliDat." \n - ". $nFilCli);
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

   ##Traigo Departamento del Cliente ##
   $qDatDep  = "SELECT DEPDESXX  ";
   $qDatDep .= "FROM $cAlfa.SIAI0054 ";
   $qDatDep .= "WHERE ";
   $qDatDep .= "PAIIDXXX =\"{$vCliDat['PAIIDXXX']}\" AND ";
   $qDatDep .= "DEPIDXXX =\"{$vCliDat['DEPIDXXX']}\" LIMIT 0,1";
   $xDatDep  = f_MySql("SELECT","",$qDatDep,$xConexion01,"");
   // f_Mensaje(__FILE__,__LINE__,$qDatDep."~".mysql_num_rows($xDatDep));
   if (mysql_num_rows($xDatDep) > 0) {
     $vDatDep = mysql_fetch_array($xDatDep);
   }
   ##Fin Traigo Departamento del Cliente ##

  ##Traigo Ciudad del Facturado A ##
  $qCiuDat  = "SELECT * ";
  $qCiuDat .= "FROM $cAlfa.SIAI0055 ";
  $qCiuDat .= "WHERE ";
  $qCiuDat .= "$cAlfa.SIAI0055.PAIIDXXX = \"{$vCliDat['PAIIDXXX']}\" AND ";
  $qCiuDat .= "$cAlfa.SIAI0055.DEPIDXXX = \"{$vCliDat['DEPIDXXX']}\" AND ";
  $qCiuDat .= "$cAlfa.SIAI0055.CIUIDXXX = \"{$vCliDat['CIUIDXXX']}\" AND ";
  $qCiuDat .= "$cAlfa.SIAI0055.REGESTXX = \"ACTIVO\" ";
  $xCiuDat  = f_MySql("SELECT","",$qCiuDat,$xConexion01,"");
  $nFilCiu  = mysql_num_rows($xCiuDat);
  //f_Mensaje(__FILE__,__LINE__,$qCiuDat."- ".$nFilCiu);
  if ($nFilCiu > 0) {
    $vCiuDat = mysql_fetch_array($xCiuDat);
  }
  ##Fin Traigo Ciudad del Facturado A ##

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

  /*** Ciudad que genera el ingreso ***/
  $qLinDes  = "SELECT LINDESXX ";
  $qLinDes .= "FROM $cAlfa.SIAI0119 ";
  $qLinDes .= "WHERE ";
  $qLinDes .= "LINIDXXX = \"$cSucId\" LIMIT 0,1 ";
  $xLinDes  = f_MySql("SELECT","",$qLinDes,$xConexion01,"");
  //f_Mensaje(__FILE__,__LINE__,$qLinDes."~".mysql_num_rows($xLinDes));
  $vLinDes  = mysql_fetch_array($xLinDes);

  ##Trayendo Datos de Do Dependiendo del Tipo de Operacion ##
  $vDatDo = f_Datos_Operativos_Do($cSucId, $cDocId, $cDocSuf);
  $vDceDat = $vDatDo['decdatxx'];
  $cTasCam = $vDatDo['tascamxx']; //Tasa de Cambio
  $cDocTra = $vDatDo['doctraxx']; //Documento de Transporte
  $cBultos = $vDatDo['bultosxx']; //Bultos
  $cPesBru = $vDatDo['pesbruxx']; //Peso Bruto
  $nValAdu = $vDatDo['valaduxx']; //Valor en aduana
  $cOpera  = $vDatDo['operaxxx']; //texto valor CIF: o FOB: segun el tipo operacion del tramite
  $cPedido = $vDatDo['pedidoxx']; //Pedido
  $cAduana = $vDatDo['aduanaxx']; //Descripcion Aduana
  $cNomVen = $vDatDo['nomvenxx']; //Nombre Vendedor
  $cOrdCom = $vDatDo['ordcomxx']; //Orden de Compra
  $cPaiOri = $vDatDo['paiorixx']; //Pais de Origen
  ###Fin Trayendo Datos de Do Dependiendo del Tipo de Operacion ##
 
  ##Exploto campo Matriz para traer los Do's y consultar los pedidos ##
  $cObsPedido = ""; 
  
  for ($i=0;$i<$_POST['nSecuencia_Dos'];$i++) {

    // echo "<pre>";
    // var_dump({$_POST['cDosNro_DOS'.($i+1)]});

    ##Busco Do en la sys00121 Tabla de Do's Financieros y traigo Tipo de Operacion ##
    $qDceDat  = "SELECT * ";
    $qDceDat .= "FROM $cAlfa.sys00121 ";
    $qDceDat .= "WHERE ";
    $qDceDat .= "$cAlfa.sys00121.sucidxxx = \"{$_POST['cSucId_DOS' .($i+1)]}\" AND ";
    $qDceDat .= "$cAlfa.sys00121.docidxxx = \"{$_POST['cDosNro_DOS'.($i+1)]}\" AND ";
    $qDceDat .= "$cAlfa.sys00121.docsufxx = \"{$_POST['cDosSuf_DOS'.($i+1)]}\" ";
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
        $qDoiDat  = "SELECT DOIPEDXX ";
        $qDoiDat .= "FROM $cAlfa.SIAI0200 ";
        $qDoiDat .= "WHERE $cAlfa.SIAI0200.DOIIDXXX = \"{$_POST['cDosNro_DOS'.($i+1)]}\" AND ";
        $qDoiDat .= "$cAlfa.SIAI0200.DOISFIDX = \"{$_POST['cDosSuf_DOS'.($i+1)]}\" AND ";
        $qDoiDat .= "$cAlfa.SIAI0200.ADMIDXXX = \"{$_POST['cSucId_DOS' .($i+1)]}\" ";
        //f_Mensaje(__FILE__,__LINE__,$qDoiDat);
        $xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
        $nFilDoi  = mysql_num_rows($xDoiDat);
        if ($nFilDoi > 0) {
          $vDoiDat  = mysql_fetch_array($xDoiDat);

          ##Cargo Variable de pedido para impresion de Datos de Do ##
          $cObsPedido .= $vDoiDat['DOIPEDXX']." / ";
        }
      break;
      case "EXPORTACION":
        ## Consulto Datos de Do en Exportaciones tabla siae0199 ##
        $qDexDat  = "SELECT dexpedxx ";
        $qDexDat .= "FROM $cAlfa.siae0199 ";
        $qDexDat .= "WHERE ";
        $qDexDat .= "$cAlfa.siae0199.dexidxxx = \"{$_POST['cDosNro_DOS'.($i+1)]}\" AND ";
        $qDexDat .= "$cAlfa.siae0199.admidxxx = \"{$_POST['cSucId_DOS' .($i+1)]}\" ";
        $xDexDat  = f_MySql("SELECT","",$qDexDat,$xConexion01,"");
        //f_Mensaje(__FILE__,__LINE__,$qDexDat);
        $nFilDex  = mysql_num_rows($xDexDat);
        if ($nFilDex > 0) {
          $vDexDat = mysql_fetch_array($xDexDat);

          ##Cargo Variable de pedido para impresion de Datos de Do ##
          $cObsPedido .= $vDexDat['dexpedxx']." / ";
        }
      break;
      case "TRANSITO":
        ## Traigo Datos de la SIAI0200 ##
        $qDoiDat  = "SELECT DOIPEDXX ";
        $qDoiDat .= "FROM $cAlfa.SIAI0200 ";
        $qDoiDat .= "WHERE ";
        $qDoiDat .= "DOIIDXXX = \"{$_POST['cDosNro_DOS'.($i+1)]}\" AND ";
        $qDoiDat .= "DOISFIDX = \"{$_POST['cDosSuf_DOS'.($i+1)]}\" AND ";
        $qDoiDat .= "ADMIDXXX = \"{$_POST['cSucId_DOS' .($i+1)]}\" ";
        $xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
        //f_Mensaje(__FILE__,__LINE__,$qDoiDat."~".mysql_num_rows($xDoiDat));
        if (mysql_num_rows($xDoiDat) > 0) {
          $vDoiDat = mysql_fetch_array($xDoiDat);
          
          ##Cargo Variable de pedido para impresion de Datos de Do ##
          $cObsPedido .= $vDoiDat['DOIPEDXX']." / ";
        } 
      break;
      case "OTROS":
      break;
    }//switch (){
    ##Fin Switch para traer Datos de Do Dependiendo del Tipo de Operacion ##
  }//for ($i=0;$i<count($mDoiId);$i++) {
  ##Fin Exploto campo Matriz para traer los Do's y consultar los pedidos ##

  #Agrupo Ingresos Propios
  $mIP = array();
  for($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
      
    $vDatosIp = array();
    $cObs = "|".$_POST['cSerId_IPA'.($i+1)]."~".$_POST['cFcoId_IPA'.($i+1)]."~".$_POST['cComObs_IPA'.($i+1)]."|";;
    $vDatosIp = f_Cantidad_Ingreso_Propio($cObs,'',$_POST['cSucId_IPA'.($i+1)],$_POST['cDosNro_IPA'.($i+1)],$_POST['cDosSuf_IPA'.($i+1)]);

    $mIP[$_POST['cComId_IPA'.($i+1)]]['ctoidxxx']  = $_POST['cComId_IPA'.($i+1)];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comobsxx'] = $vDatosIp[0];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comvlrxx'] += $_POST['nComVlr_IPA'.($i+1)];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['compivax']  = $_POST['nComPIva_IPA'.($i+1)]; // Porcentaje IVA
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comvlr01'] += $_POST['nComVIva_IPA'.($i+1)]; // Valor Iva
    //Cantidad FE
    $mIP[$_POST['cComId_IPA'.($i+1)]]['unidadfe']  = $vDatosIp[2];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['canfexxx'] += $vDatosIp[1];

    if ($_POST['cComTFa'] == "MANUAL") {
      //Si la factura manual esta cantidad se tiene en cuenta
      $mIP[$_POST['cComId_IPA'.($i+1)]]['comcanxx'] += $_POST['nComCan_IPA'.($i+1)];
    } else {
      //Si la factura no es manual esta cantidad no se tiene en cuenta
      $mIP[$_POST['cComId_IPA'.($i+1)]]['comcanxx'] = 0;
    }

    //Cantidad por condicion especial
    for ($nP=0; $nP<count($vDatosIp[3]); $nP++) {
      $mIP[$_POST['cComId_IPA'.($i+1)]]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] += $vDatosIp[3][$nP]['valpdfxx'];
    }
  }//for ($k=0;$k<count($mCodDat);$k++) {

  $mIngPro = array();
  foreach ($mIP as $cKey => $mValores) {
    $mIngPro[count($mIngPro)] = $mValores;
  }
  $mValores = array();
  /*** Matriz para pagos de 4xmil GMF ***/
  $mDatGmf = array();
  ##Codigo para imprimir los ingresos para terceros ##
  $mIngTer = array();

  for ($i=0;$i<$_POST['nSecuencia_PCCA'];$i++) {
    $vTercero = explode("^",$_POST['cComObs_PCCA'.($i+1)]);
    $mComObs_IP = stripos($_POST['cComObs_PCCA'.($i+1)], "[");

    if ( $_POST['cTipo_PCCA'.($i+1)] == "IMPUESTO_FINANCIERO") { //si es GMF debe mostrarse en GMF
      $nInd_mDatGmf = count($mDatGmf);
      $mDatGmf[$nInd_mDatGmf]['comobsxx'] = trim($vTercero[0]);
      $mDatGmf[$nInd_mDatGmf]['comvlrxx'] = $_POST['nComVlr_PCCA'.($i+1)];
      // $mDatGmf[$nInd_mDatGmf]['puctipej'] = $xRCD['puctipej'];
      $mDatGmf[$nInd_mDatGmf]['ctoidxxx'] = $_POST['cComId_PCCA'.($i+1)];
      $mDatGmf[$nInd_mDatGmf]['comvlr01'] = $_POST['nVlrIva_PCCA'.($i+1)];
      $mDatGmf[$nInd_mDatGmf]['cTerNom']  = trim($vTercero[1]);

    } else if (substr($_POST['cComId_PCCA'.($i+1)],0,1) == "4") { //si es el 4xmil mostrarse en ingresos propios
      $nInd_mValores = count($mValores);
      $mValores[$nInd_mValores]['comobsxx'] = trim($vTercero[0]);
      $mValores[$nInd_mValores]['comvlrxx'] = $_POST['nComVlr_PCCA'.($i+1)];
      $mValores[$nInd_mValores]['ctoidxxx'] = $_POST['cComId_PCCA'.($i+1)];
      $mValores[$nInd_mValores]['comvlr01'] = $_POST['nVlrIva_PCCA'.($i+1)];
      $mValores[$nInd_mValores]['cTerNom']  = trim($vTercero[1]);

    }else {
      $nSwitch_Find = 0;
      
      if ($nSwitch_Find == 0) { // No lo encontro en la matriz para pintar en la factura
        $nInd_mIngTer = count($mIngTer);
        $mIngTer[$nInd_mIngTer]['cComObs']  = trim($vTercero[0]);
        $mIngTer[$nInd_mIngTer]['cTerNom']  = trim($vTercero[1]);
        $mIngTer[$nInd_mIngTer]['cComId']   = $_POST['cComId_PCCA'  .($i+1)];
        $mIngTer[$nInd_mIngTer]['cTerId']  = trim($vTercero[2]);
        $mIngTer[$nInd_mIngTer]['cComCsc3'] = $_POST['cComCsc3_PCCA'.($i+1)];
        $mIngTer[$nInd_mIngTer]['nComVlr']  = $_POST['nComVlr_PCCA' .($i+1)];
        $mIngTer[$nInd_mIngTer]['nBaseIva'] = $_POST['nBaseIva_PCCA'.($i+1)];
        $mIngTer[$nInd_mIngTer]['nVlrIva']  = $_POST['nVlrIva_PCCA'.($i+1)];
      }
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

    public $headers = [
      ['w' => 20, 'title' => 'COD. REF'],
      ['w' => 66, 'title' => 'DETALLE'],
      ['w' => 15, 'title' => 'CANT'],
      ['w' => 24, 'title' => 'VALOR UNITARIO'],
      ['w' => 11, 'title' => '% IVA'],
      ['w' => 13, 'title' => 'IVA'],
      ['w' => 17, 'title' => 'VALOR USD'],
      ['w' => 39, 'title' => 'VALOR COP'],
    ];

    function Header() {
      global $cAlfa;   global $cPlesk_Skin_Directory;    global $vAgeDat; global $vCocDat;
      global $vResDat; global $cDocTra; global $cTasCam; global $cBultos; global $cPesBru;
      global $cDocId;  global $vCiuDat; global $vDceDat; global $cOpera;  global $nValAdu;
      global $cPaiOri; global $cPedido; global $vCliDat; global $cAduana; global $vPaiDat; 
      global $cNomVen; global $cOrdCom; global $vSysStr; global $vLinDes; global $cEstiloLetra;
      global $gCorreo; global $cCscFac; global $vDatDep; global $vMedPag; global $cFormaPago;
      global $cPlazo;

      if ($vCocDat['regestxx'] == "INACTIVO") {
        $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/facturaanulada.jpg',10,50,190,190);
      }

      if ($_COOKIE['kModo'] == "VERFACTURA"){
        $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/copianovalida.jpg',10,50,190,190);
      }

      if ( $gCorreo == 1 ){
        $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/copianovalida.jpg',10,50,190,190);
      }

      // Impresion Datos Generales Factura
      $nPosX = 5;
      $nPosY = 10;

      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomalco.jpg',$nPosX,$nPosY,32,21);

      $this->SetFont('Arial', 'B', 10);
      $this->setXY($nPosX + 3, $nPosY);
      $this->Cell(200, 4, utf8_decode("AGENCIA DE ADUANAS MARIO LONDOÑO S.A NIVEL 1"), 0, 0, 'C');
      $this->Ln(5);
      $this->setX($nPosX + 3);
      $this->Cell(200, 4, utf8_decode("NIT. {$vSysStr['financiero_nit_agencia_aduanas']}-".f_Digito_Verificacion($vSysStr['financiero_nit_agencia_aduanas'])), 0, 0, 'C');
      $nPosY = $this->GetY() + 5;
      $this->setXY($nPosX + 32, $nPosY);
      $this->SetFont('Arial', 'B', 7);
      $this->Cell(50, 4, utf8_decode("REPRESENTACIÓN GRÁFICA DE LA FACTURA ELECTRÓNICA"), 0, 0, 'L');
      $this->Ln(4);
      $this->setX($nPosX + 32);
      $this->Cell(50, 4, "CUFE:", 0, 0, 'L');

      $this->Ln(4);
      $this->setX($nPosX + 32);
      $this->SetFont('Arial', '', 7);
      $this->MultiCell(65, 3, "", 0, 'L');
      $nPosYfin = $this->GetY();

      $this->setXY($nPosX + 110, $nPosY);
      $this->SetFont('Arial', 'B', 7);
      $this->Cell(50, 4, "SEDE PPAL:", 0, 0, 'L');
      $this->setX($nPosX + 126);
      $this->SetFont('Arial', '', 7);
      $this->MultiCell(47, 4, utf8_decode("MEDELLÍN Cll 8B Nº 65-191 OF.511 Edificio Puerto Seco"), 0, 'L');

      $this->Ln(0.5);
      $this->setX($nPosX + 110);
      $this->SetFont('Arial', 'B', 7);
      $this->Cell(50, 4, "CIUDAD QUE GENERA EL INGRESO:", 0, 0, 'L');
      $this->setX($nPosX + 155);
      $this->SetFont('Arial', '', 7);
      $this->MultiCell(50, 4, utf8_decode($vLinDes['LINDESXX']), 0, 'L');
    
      if($nPosYfin < $this->GetY()){
        $nPosYfin = $this->GetY() + 1;
      }

      $nPosY = $nPosYfin;
      $this->setXY($nPosX, $nPosY);
      $this->SetFillColor(230, 230, 230);
      $this->SetFont('Arial', 'B', 12);
      $this->Cell(205, 7, utf8_decode("FACTURA ELECTRÓNICA DE VENTA No. ".$vResDat['resprexx'].$cCscFac), 0, 0, 'C', TRUE);

      $nPosYfin = $this->GetY() + 7;
      if ($this->PageNo() == 1) {
        $nFontSizeHeaderSumarize = 7;
        $nFontSizeHeader = 8;

        // Columna 1
        $nPosY = $this->GetY() + 10;
        $nPosYIni = $nPosY;

        $this->setXY($nPosX, $nPosY);
        $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
        $this->Cell(20, 4, utf8_decode("SEÑOR(ES):"), 0, 0, 'L');
        $this->SetFont('Arial', '', $nFontSizeHeader);
        $this->MultiCell(67, 3, utf8_decode($vCliDat['CLINOMXX']), 0, 'L');
        $this->Ln(1);

        $this->setX($nPosX);
        $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
        $this->Cell(20, 4, "NIT/CC:", 0, 0, 'L');
        $this->SetFont('Arial', '', $nFontSizeHeader);
        $this->Cell(67, 4, $vCliDat['CLIIDXXX']."-".f_Digito_Verificacion($vCliDat['CLIIDXXX']), 0, 0, 'L');
        $this->Ln(4);

        $this->setX($nPosX);
        $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
        $this->Cell(20, 4, utf8_decode("DIRECCIÓN:"), 0, 0, 'L');
        $this->SetFont('Arial', '', $nFontSizeHeader);
        $this->MultiCell(67, 3, utf8_decode($vCliDat['CLIDIR3X']), 0, 'L');
        $this->Ln(0.5);

        $this->setX($nPosX);
        $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
        $this->Cell(38, 4, utf8_decode("CIUDAD Y DEPARTAMENTO:"), 0, 0, 'L');
        $this->SetFont('Arial', '', $nFontSizeHeader);
        $this->MultiCell(49, 4, utf8_decode($vCiuDat['CIUDESXX'] . " - " . $vDatDep['DEPDESXX']), 0, 'L');
        $this->Ln(0.5);

        $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
        $this->setX($nPosX);
        $this->Cell(26, 4, utf8_decode("FORMA DE PAGO:"), 0, 0, 'L');
        $this->SetFont('Arial', '', $nFontSizeHeader);
        $this->Cell(61, 4, utf8_decode($cFormaPago), 0, 0, 'L');
        $this->Ln(4);
        
        $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
        $this->setX($nPosX);
        $this->Cell(26, 4, utf8_decode("MEDIO DE PAGO:"), 0, 0, 'L');
        $this->SetFont('Arial', '', $nFontSizeHeader);
        $this->Cell(61, 4, utf8_decode($vMedPag['mpadesxx']), 0, 0, 'L');
        $this->Ln(4);

        $nPosYfin = $this->GetY();

        // Columna 2
        $offsetX = 88;
        $this->setXY($nPosX + $offsetX, $nPosY);
        $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
        $this->Cell(20, 4, utf8_decode("TELÉFONO:"), 0, 0, 'L');
        $this->SetFont('Arial', '', $nFontSizeHeader);
        $this->Cell(56, 4, $vCliDat['CLITELXX'], 0, 0, 'L');
        $this->Ln(4);

        $this->setX($nPosX + $offsetX);
        $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
        $this->Cell(45, 4, utf8_decode("FECHA Y HORA DE GENERACIÓN:"), 0, 0, 'L');
        $this->SetFont('Arial', '', $nFontSizeHeader);
        $this->Cell(31, 4, date('Y-m-d H:i:s'), 0, 0, 'L');
        $this->Ln(4);

        $this->setX($nPosX + $offsetX);
        $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
        $this->Cell(49, 4, utf8_decode("FECHA Y HORA DE VALIDACION DIAN:"), 0, 0, 'L');
        $this->SetFont('Arial', '', $nFontSizeHeader);
        $this->Cell(27, 4, "", 0, 0, 'L');
        $this->Ln(4);

        $this->setX($nPosX + $offsetX);
        $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
        $this->Cell(30, 4, "FECHA VENCIMIENTO:", 0, 0, 'L');
        $this->SetFont('Arial', '', $nFontSizeHeader);
        $this->Cell(46, 4, date('Y-m-d', strtotime("+{$_POST['cTerPla']} day")), 0, 0, 'L');
        $this->Ln(4);
        if ($nPosYfin < $this->GetY())
            $nPosYfin = $this->GetY();

        $this->setX($nPosX + $offsetX);
        $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
        $this->Cell(20, 4, "COMERCIAL:", 0, 0, 'L');
        $this->SetFont('Arial', '', $nFontSizeHeader);
        $this->Cell(56, 3, utf8_decode($cNomVen), 0, 0, 'L');
        $this->Ln(4);
        if ($nPosYfin < $this->GetY())
            $nPosYfin = $this->GetY();
        
        // Columna 3
        $offsetX = 165;

        $this->setXY($nPosX + $offsetX, $nPosY);
        $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
        $this->Cell(8, 4, "DO:", 0, 0, 'L');
        $this->SetFont('Arial', '', $nFontSizeHeader);
        $this->Cell(32, 4, $cDocId, 0, 0, 'L');
        $this->Ln(4);

        $this->setX($nPosX + $offsetX);
        $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
        $this->Cell(10, 4, "TRM:", 0, 0, 'L');
        $this->SetFont('Arial', '', $nFontSizeHeader);
        $this->Cell(30, 4, number_format($_POST['nTasaCambio'],2,'.',''), 0, 0, 'L');
        $this->Ln(4);
        
        $this->setX($nPosX + $offsetX);
        $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
        $this->Cell(13, 4, "PEDIDO:", 0, 0, 'L');
        $this->SetFont('Arial', '', $nFontSizeHeader);
        $this->Cell(27, 4, utf8_decode($cPedido), 0, 0, 'L');
        $this->Ln(4.5);

        $this->setX($nPosX + $offsetX);
        $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
        $this->Cell(8, 4, "OC:", 0, 0, 'L');
        $this->SetFont('Arial', '', $nFontSizeHeader);
        $this->Cell(32, 4, utf8_decode($cOrdCom), 0, 0, 'L');
        $this->Ln(4);

        $this->setX($nPosX + $offsetX);
        $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
        $this->Cell(13, 4, "PLAZO:", 0, 0, 'L');
        $this->SetFont('Arial', '', $nFontSizeHeader);
        $this->Cell(27, 4, utf8_decode($cPlazo), 0, 0, 'L');
        $this->Ln(4);
        if ($nPosYfin < $this->GetY()) {
            $nPosYfin = $this->GetY();
        }
        $this->Rect($nPosX, $nPosY - 1, 205, $nPosYfin - ($nPosYIni-2));
      }

      $nPosY = $nPosYfin+3;
      $this->setXY($nPosX, $nPosY);
      $this->SetFillColor(220, 220, 220);
      $this->SetFont('Arial', 'B', 7.5);
      foreach ($this->headers as $head)
          $this->Cell($head['w'], 5, $head['title'], 0, 0, 'C', TRUE);

      $this->Rect($nPosX, $nPosY, 205, 5);
      $this->posYIniLines = $nPosY;
      $this->setXY($nPosX,$nPosY);
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
      global $vUsrNom;
      
      $nPosX = 5;
      //Defino posicion inicial Y para pintar la firma
      $nPosY = 250;

      $this->setXY($nPosX + 137, $nPosY + 4);
      $this->SetFont('Arial', '', 8.5);
      $this->MultiCell(70, 3, utf8_decode($vUsrNom['USRNOMXX']), 0, 'C');

      $this->setXY($nPosX + 137, $nPosY + 13);
      $this->SetFont('Arial', 'B', 8);
      $this->MultiCell(70, 3, utf8_decode("ELABORÓ"), 0, 'C');

      $this->Rect($nPosX, $nPosY, 205, 17);
      $this->Line($nPosX + 136, $nPosY, $nPosX + 136, $nPosY + 17);
      $this->Line($nPosX + 136, $nPosY + 11, $nPosX + 205, $nPosY + 11);

      //Paginacion
      $this->setXY($nPosX, $nPosY + 17);
      $this->SetFont('Arial', 'B', 7);
      $this->Cell(205, 4, utf8_decode('Pág. ') . $this->PageNo() . '/{nb}', 0, 0, 'C');

      $nPosY = $this->GetY()+5;
      $this->setXY($nPosX,$nPosY);
      $this->SetFont($cEstiloLetra,'',8);
      $this->Cell(205,5,utf8_decode("- VISTA PREVIA -"),0,0,'C',true);
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

  $nPosY = $pdf->GetY()+6;
  $nPosX = 5;
  $nPosFin = 240;
  $nPosYVl = 185;
  $nb = 1;
  $pyy = $nPosY;

  // $mIngPro = array_merge($mIngPro,$mIngPro);
  // $mIngPro = array_merge($mIngPro,$mIngPro);
  // $mIngPro = array_merge($mIngPro,$mIngPro);

  /*** Imprimo Detalle de Pagos a Terceros e Ingresos Propios ***/
  /*** Imprimo Pagos a Terceros ***/
  if (count($mIngTer) > 0 || count($mValores) > 0) {//Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1

    $nTotPcc    = 0; 
    $cCodigoPCC = "";

    for($i=0;$i<count($mIngTer);$i++){
      $cCodigoPCC = ($cCodigoPCC == "") ? $mIngTer[$i]['cComId'] : $cCodigoPCC;
      $nTotPcc += $mIngTer[$i]['nComVlr'];
    }//for($i=0;$i<count($mIngTer);$i++){

    for ($i=0;$i<count($mValores);$i++) {
      $cCodigoPCC = ($cCodigoPCC == "") ? $mValores[$i]['ctoidxxx'] : $cCodigoPCC;
      $nTotPcc += $mValores[$i]['comvlrxx'];
    }//for ($i=0;$i<count($mValores);$i++) {

    $pdf->setXY($nPosX,$pyy);
    $pdf->SetWidths(array(20, 66, 15, 24, 11, 13, 17, 39));
    $pdf->SetAligns(array("L", "L", "C", "R", "C", "R", "R", "R"));
    $pdf->SetFont($cEstiloLetra,'B',8);

    $pdf->Row(array(
            utf8_decode($cCodigoPCC),
            utf8_decode("TOTAL PAGOS EFECTUADOS POR SU CUENTA"),
            number_format(1, 0, ',', '.'),
            number_format($nTotPcc, 0, ',', '.'),
            "0%",
            "0",
            number_format($nTotPccMe, 2, ',', '.'),
            number_format($nTotPcc, 0, ',', '.'),
        ));
    $pyy = $pdf->GetY() + 6;
  }//if(count($mIngTer) > 0 || $nBandPcc == 1){//Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
  /*** Fin Imprimo Pagos a Terceros ***/

  // Imprimo Ingresos Propios
  $nSubToIP = 0;    // Subtotal pagos propios
  $nSubToIPIva = 0; // Iva 19%

  $nSubToIPGra   = 0; // Total Ingresos Gravados
  $nSubToIPNoGra = 0; // Total Ingresos No Gravados

  if ($_POST['nSecuencia_IPA'] > 0 || count($mIngPro) > 0 || count($mDatGmf) > 0) {
    $pdf->setXY($nPosX,$pyy);
    $pdf->SetFont($cEstiloLetra,'B',8);
    $pdf->Cell(20,6,"",0,0,'L');
    $pdf->Cell(66,6,utf8_decode("SERVICIOS PRESTADOS"),0,0,'L');
    $pyy += 6;
  }

  if($_POST['nSecuencia_IPA'] > 0 || count($mIngPro) > 0) {//Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS
    $pdf->setXY($nPosX,$pyy);
    $pdf->SetWidths(array(20, 66, 15, 24, 11, 13, 17, 39));
    $pdf->SetAligns(array("L", "L", "C", "R", "C", "R", "R", "R"));
    $pdf->SetFont($cEstiloLetra,'',8);

    for ($k=0;$k<(count($mIngPro));$k++) {
      $pyy = $pdf->GetY();

      if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
        $pdf->Line($nPosX+20,$nPosY-6,$nPosX+20,($nPosFin+10));
        $pdf->Line($nPosX+86,$nPosY-6,$nPosX+86,($nPosFin+10));
        $pdf->Line($nPosX+101,$nPosY-6,$nPosX+101,($nPosFin+10));
        $pdf->Line($nPosX+125,$nPosY-6,$nPosX+125,($nPosFin+10));
        $pdf->Line($nPosX+136,$nPosY-6,$nPosX+136,($nPosFin+10));
        $pdf->Line($nPosX+149,$nPosY-6,$nPosX+149,($nPosFin+10));
        $pdf->Line($nPosX+166,$nPosY-6,$nPosX+166,($nPosFin+10));
        $pdf->Rect($nPosX, $nPosY-6, 205, ($nPosFin+10) - ($nPosY - 6));
        $pdf->AddPage();
        $nb++;
        $nPosY = $pdf->GetY()+6;
        $nPosX = 5;
        $pyy = $nPosY;
        $pdf->SetFont($cEstiloLetra,'',8);
        $pdf->setXY($nPosX,$pyy);
      }

      if($mIngPro[$k]['comvlr01'] != 0) {
        $nSubToIP    += $mIngPro[$k]['comvlrxx'];
        $nSubToIPIva += $mIngPro[$k]['comvlr01'];
        $nSubToIPGra += $mIngPro[$k]['comvlrxx'];

        $cValor = ""; $cValCon = "";
        //Mostrando cantidades por tipo de cantidad
        foreach ($mIngPro[$k]['itemcanx'] as $cKey => $cValue) {
          // Personalizacion de la descripcion por base de datos e informacion adicional
          if($cKey == "FOB" && $cValue > 0) {
            $cValor  = " FOB: ($".$cValue;
            $cValor .= ($mIngPro[$k]['itemcanx']['TRM'] > 0) ? " TRM: $".$mIngPro[$k]['itemcanx']['TRM'] : "";
            $cValor .= ")";
          } elseif ($cKey == "CIF") {
            $cValor = "CIF: ($".$cValue.")";
          } elseif ($cKey == "CONTENEDORES_DE_20") {
            $cValCon .= " CONTENEDORES DE 20: (".$cValue.")";
          } elseif ($cKey == "CONTENEDORES_DE_40") {
            $cValCon .= " CONTENEDORES DE 40: (".$cValue.")";
          }elseif ($cKey == "UNIDADES_DE_CARGA_SUELTA") {
            $cValCon .= " UNIDADES DE CARGA SUELTA: (".$cValue.")";
          }
          $cValor = ($cValCon != "") ? $cValCon : $cValor;
        }

        //Si la factura es manual, se trae la cantidad de la fcodYYYY
        //Si es automatica, se trae la cantidad de la descripcion
        if ($_POST['cComTFa'] == "MANUAL") {
          //Cantidad
          $nCantidad  = (($mIngPro[$k]['comcanxx']+0) > 0) ? ($mIngPro[$k]['comcanxx']+0) : 1;
          //Cantidad de decimales de la cantidad
          $nCanDec = (strpos(($mIngPro[$k]['comcanxx']+0),'.') > 0) ? 2 : 0;
        } else {
          //Canitdad
          $nCantidad = ($mIngPro[$k]['unidadfe'] != "A9" && $mIngPro[$k]['canfexxx'] > 0) ? $mIngPro[$k]['canfexxx'] : 1;
          $nCanDec = 0;
        }

        //Calculando valor unitario
        $nValUni = ($vCliDat['CLINRPXX'] == "SI") ? round(($mIngPro[$k]['comvlrxx']/$nCantidad)*100)/100 : round($mIngPro[$k]['comvlrxx']/$nCantidad);
        $nValIva = ($vCliDat['CLINRPXX'] == "SI") ? number_format(round($mIngPro[$k]['comvlr01']/$_POST['nTasaCambio'],2), 2, ',', '.') : number_format($mIngPro[$k]['comvlr01'], 0, ',', '.');

        //Cantiad de decimales valor unitario
        $nCanDecUni = (strpos(($nValUni+0),'.') > 0) ? 2 : 0;

        $pdf->setX($nPosX);
        $pdf->Row(array(
            utf8_decode($mIngPro[$k]['ctoidxxx']),
            utf8_decode("* ".trim($mIngPro[$k]['comobsxx'].$cValor)),
            number_format($nCantidad, $nCanDec, ',', '.'),
            number_format($nValUni, $nCanDecUni, ',', '.'),
            ($mIngPro[$k]['compivax']+0)."%",
            $nValIva,
            number_format(($vCliDat['CLINRPXX'] == "SI") ? $mIngPro[$k]['comvlrxx'] : 0, 2, ',', '.'),
            number_format(($vCliDat['CLINRPXX'] == "SI") ? round($mIngPro[$k]['comvlrxx']*$_POST['nTasaCambio']) : $mIngPro[$k]['comvlrxx'], 0, ',', '.'),
        ));
      }//if($mIngPro[$k]['comvlr01'] != 0) {
    }

    for ($k=0;$k<count($mIngPro);$k++) {
      $pyy = $pdf->GetY();

      if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
        $pdf->Line($nPosX+20,$nPosY-6,$nPosX+20,($nPosFin+10));
        $pdf->Line($nPosX+86,$nPosY-6,$nPosX+86,($nPosFin+10));
        $pdf->Line($nPosX+101,$nPosY-6,$nPosX+101,($nPosFin+10));
        $pdf->Line($nPosX+125,$nPosY-6,$nPosX+125,($nPosFin+10));
        $pdf->Line($nPosX+136,$nPosY-6,$nPosX+136,($nPosFin+10));
        $pdf->Line($nPosX+149,$nPosY-6,$nPosX+149,($nPosFin+10));
        $pdf->Line($nPosX+166,$nPosY-6,$nPosX+166,($nPosFin+10));
        $pdf->Rect($nPosX, $nPosY-6, 205, ($nPosFin+10) - ($nPosY - 6));
        $pdf->AddPage();
        $nb++;
        $nPosY = $pdf->GetY()+6;
        $nPosX = 5;
        $pyy = $nPosY;
        $pdf->SetFont($cEstiloLetra,'',8);
        $pdf->setXY($nPosX,$pyy);
      }

      if($mIngPro[$k]['comvlr01'] == 0) {
        $nSubToIP      += $mIngPro[$k]['comvlrxx'];
        $nSubToIPIva   += $mIngPro[$k]['comvlr01'];
        $nSubToIPNoGra += $mIngPro[$k]['comvlrxx'];

        $cValor = ""; $cValCon = "";
        //Mostrando cantidades por tipo de cantidad
        foreach ($mIngPro[$k]['itemcanx'] as $cKey => $cValue) {
          // Personalizacion de la descripcion por base de datos e informacion adicional
          if($cKey == "FOB" && $cValue > 0) {
            $cValor  = " FOB: ($".$cValue;
            $cValor .= ($mIngPro[$k]['itemcanx']['TRM'] > 0) ? " TRM: $".$mIngPro[$k]['itemcanx']['TRM'] : "";
            $cValor .= ")";
          } elseif ($cKey == "CIF") {
            $cValor = "CIF: ($".$cValue.")";
          } elseif ($cKey == "CONTENEDORES_DE_20") {
            $cValCon .= " CONTENEDORES DE 20: (".$cValue.")";
          } elseif ($cKey == "CONTENEDORES_DE_40") {
            $cValCon .= " CONTENEDORES DE 40: (".$cValue.")";
          }elseif ($cKey == "UNIDADES_DE_CARGA_SUELTA") {
            $cValCon .= " UNIDADES DE CARGA SUELTA: (".$cValue.")";
          }
          $cValor = ($cValCon != "") ? $cValCon : $cValor;
        }

        //Si la factura es manual, se trae la cantidad de la fcodYYYY
        //Si es automatica, se trae la cantidad de la descripcion
        if ($_POST['cComTFa'] == "MANUAL") {
          //Cantidad
          $nCantidad  = (($mIngPro[$k]['comcanxx']+0) > 0) ? ($mIngPro[$k]['comcanxx']+0) : 1;
          //Cantidad de decimales de la cantidad
          $nCanDec = (strpos(($mIngPro[$k]['comcanxx']+0),'.') > 0) ? 2 : 0;
        } else {
          //Canitdad
          $nCantidad = ($mIngPro[$k]['unidadfe'] != "A9" && $mIngPro[$k]['canfexxx'] > 0) ? $mIngPro[$k]['canfexxx'] : 1;
          $nCanDec = 0;
        }

        //Calculando valor unitario
        $nValUni = ($vCliDat['CLINRPXX'] == "SI") ? round(($mIngPro[$k]['comvlrxx']/$nCantidad)*100)/100 : round($mIngPro[$k]['comvlrxx']/$nCantidad);
        $nValIva = ($vCliDat['CLINRPXX'] == "SI") ? number_format(round($mIngPro[$k]['comvlr01']/$_POST['nTasaCambio'],2), 2, ',', '.') : number_format($mIngPro[$k]['comvlr01'], 0, ',', '.');

        //Cantiad de decimales valor unitario
        $nCanDecUni = (strpos(($nValUni+0),'.') > 0) ? 2 : 0;

        $pdf->setX($nPosX);
        $pdf->Row(array(
            utf8_decode($mIngPro[$k]['ctoidxxx']),
            utf8_decode(trim($mIngPro[$k]['comobsxx'].$cValor)),
            number_format($nCantidad, $nCanDec, ',', '.'),
            number_format($nValUni, $nCanDecUni, ',', '.'),
            ($mIngPro[$k]['compivax']+0)."%",
            $nValIva,
            number_format(($vCliDat['CLINRPXX'] == "SI") ? $mIngPro[$k]['comvlrxx'] : 0, 2, ',', '.'),
            number_format(($vCliDat['CLINRPXX'] == "SI") ? round($mIngPro[$k]['comvlrxx']*$_POST['nTasaCambio']) : $mIngPro[$k]['comvlrxx'], 0, ',', '.'),
        ));
      }//if($mIngPro[$k]['comvlr01'] == 0) {
    }
  }//if($nBandIP == 1){//Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS
  /*** Fin Imprimo Ingresos Propios ***/
  /*** Fin Imprimo Detalle de Pagos a Terceros e Ingresos Propios ***/

  // Impresion GMF
  if ( count($mDatGmf) > 0 ){

    $pyy = ($pdf->GetY() > $pyy) ? $pdf->GetY(): $pyy;

    if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
      $pdf->Line($nPosX+20,$nPosY-6,$nPosX+20,($nPosFin+10));
      $pdf->Line($nPosX+86,$nPosY-6,$nPosX+86,($nPosFin+10));
      $pdf->Line($nPosX+101,$nPosY-6,$nPosX+101,($nPosFin+10));
      $pdf->Line($nPosX+125,$nPosY-6,$nPosX+125,($nPosFin+10));
      $pdf->Line($nPosX+136,$nPosY-6,$nPosX+136,($nPosFin+10));
      $pdf->Line($nPosX+149,$nPosY-6,$nPosX+149,($nPosFin+10));
      $pdf->Line($nPosX+166,$nPosY-6,$nPosX+166,($nPosFin+10));
      $pdf->Rect($nPosX, $nPosY-6, 205, ($nPosFin+10) - ($nPosY - 6));
      $pdf->AddPage();
      $nb++;
      $nPosY = $pdf->GetY()+6;
      $nPosX = 5;
      $pyy = $nPosY;
      $pdf->SetFont($cEstiloLetra,'',8);
      $pdf->setXY($nPosX,$pyy);
    }

    $nSubTotGmf = 0;  $cCodigoGMF = "";
    for ($i=0;$i<count($mDatGmf);$i++) {
      $cCodigoGMF    = ($cCodigoGMF == "") ? $mDatGmf[$i]['ctoidxxx'] : $cCodigoGMF;
      $nSubTotGmf   += $mDatGmf[$i]['comvlrxx'];
    }//for ($i=0;$i<count($mDatGmf);$i++) {

    $pdf->setXY($nPosX,$pyy);
    $pdf->SetWidths(array(20, 66, 15, 24, 11, 13, 17, 39));
    $pdf->SetAligns(array("L", "L", "C", "R", "C", "R", "R", "R"));
    $pdf->SetFont($cEstiloLetra,'',8);

    $pdf->Row(array(
        utf8_decode($cCodigoGMF),
        utf8_decode("RECUPERACIÓN GASTOS BANCARIOS (GMF)"),
        number_format(1, 0, ',', '.'),
        number_format($nSubTotGmf, 0, ',', '.'),
        "0%",
        "0",
        number_format(($vCliDat['CLINRPXX'] == "SI") ? $nSubTotGmf : 0, 2, ',', '.'),
        number_format(($vCliDat['CLINRPXX'] == "SI") ? round($nSubTotGmf*$_POST['nTasaCambio']) : $nSubTotGmf, 0, ',', '.'),
    ));
    $pyy += 6;
  }
  // Fin Impresion GMF

  /*** Fin Impresion GMF ***/
  ##Busco Valor de IVA ##
  $nIva   = $_POST['nIPAIva'];
  ##Busco Valor de RET.IVA ##
  $nTotIva   = $_POST['nIPARIva'];
  ##Busco Valor de RET.ICA ##
  $nTotIca   = $_POST['nIPARIca'];
  ##Busco Valor de AUTO RET.ICA ##
  $nTotAIca  = $_POST['nIPAARIca'];
  ##Busco Valor de RET.FTE ##
  $nTotRfte  = $_POST['nIPARFte'];
  ##Busco Valor de AUTO RET.FTE ##
  $nTotARfte = $_POST['nIPAARFte'];
  ##Busco Valor de RET.CREE ##
  $nTotCree  = $_POST['nIPARCre'];
  ##Busco Valor de AUTO RET.CREE ##
  $nTotACree = $_POST['nIPAARCre'];

  if ($_POST['nIPASal'] > 0) {
    $cSaldo = "CARGO";
  } else {
    $cSaldo = "FAVOR";
  }
  $nTotPag = abs($_POST['nIPASal']);

  //Anticipos
  $nTotAnt = abs($_POST['nIPAAnt']);

  $cCurrency = $vCliDat['CLINRPXX'] != "SI" ? "PESO" : "DOLAR";
  $nTotPag1 = f_Cifra_Php(str_replace("-","",abs($nTotPag)),$cCurrency);
  
  $nSaldoFavor  = 0;
  $nTotAntCruce = abs($nTotAnt);
  if ($cSaldo == "FAVOR" || $nTotPag == 0) {
    //El saldo a favor es el valor de SC y el valor total es cero
    $nSaldoFavor = $nTotPag;
    $nTotPag = 0;

    //Calculando el anticipo utilizado
    $nTotAntCruce = abs($nTotAnt - $nSaldoFavor);
  }

  if($pyy > $nPosYVl){//Validacion para siguiente pagina si se excede espacio de impresion
    $pdf->Line($nPosX+20,$nPosY-6,$nPosX+20,($nPosFin+10));
    $pdf->Line($nPosX+86,$nPosY-6,$nPosX+86,($nPosFin+10));
    $pdf->Line($nPosX+101,$nPosY-6,$nPosX+101,($nPosFin+10));
    $pdf->Line($nPosX+125,$nPosY-6,$nPosX+125,($nPosFin+10));
    $pdf->Line($nPosX+136,$nPosY-6,$nPosX+136,($nPosFin+10));
    $pdf->Line($nPosX+149,$nPosY-6,$nPosX+149,($nPosFin+10));
    $pdf->Line($nPosX+166,$nPosY-6,$nPosX+166,($nPosFin+10));
    $pdf->Rect($nPosX, $nPosY-6, 205, ($nPosFin+10) - ($nPosY - 6));
    $pdf->AddPage();
    $nb++;
    $nPosY = $pdf->GetY()+6;
    $nPosX = 5;
    $pyy = $nPosYVl;
    $pdf->SetFont($cEstiloLetra,'',8);
    $pdf->setXY($nPosX,$pyy);
  } else {
    $pyy = $nPosYVl;
    $pdf->SetFont($cEstiloLetra,'',8);
    $pdf->setXY($nPosX,$pyy);
  }

  $pdf->Line($nPosX+20,$nPosY-6,$nPosX+20,$pyy);
  $pdf->Line($nPosX+86,$nPosY-6,$nPosX+86,$pyy);
  $pdf->Line($nPosX+101,$nPosY-6,$nPosX+101,$pyy);
  $pdf->Line($nPosX+125,$nPosY-6,$nPosX+125,$pyy);
  $pdf->Line($nPosX+136,$nPosY-6,$nPosX+136,$pyy);
  $pdf->Line($nPosX+149,$nPosY-6,$nPosX+149,$pyy);
  $pdf->Line($nPosX+166,$nPosY-6,$nPosX+166,$pyy);
  $pdf->Rect($nPosX, $nPosY-6, 205, $pyy - ($nPosY - 6));

  $pdf->setXY($nPosX, $pyy + 1);
  $pdf->SetFont('Arial', 'B', 8);
  $pdf->MultiCell(135, 3.2, "OBSERVACIONES: \n" . utf8_decode($_POST['cComObs']).". ".substr($cObsPedido, 0, -2), 0, 'L');

  $pdf->setXY($nPosX, $pyy + 23);
  $pdf->SetFont('Arial', '', 8);
  $pdf->MultiCell(135, 3.2, utf8_decode("AGENTES RETENEDORES DE IVA - NO SOMOS GRANDES CONTRIBUYENTES - SOMOS AUTORRETENEDORES EN RENTA RESOLUCIÓN DIAN 005315 DE JUNIO 26 DE 2013."), 0, 'L');
  $pdf->setX($nPosX);
  //Resolucion de facturacion
  $cResolucion = "Somos Autorretenedores de ICA en: Cartagena, Barranquilla, Santa Marta y Riohacha. Facturación autorizada por la DIAN mediante formulario No. ". $vResDat['residxxx'] ." de ". $vResDat['resfdexx'] ." Numeración ". $vResDat['resprexx'] ." ". $vResDat['resdesxx'] ." al ". $vResDat['resprexx'] ." ". $vResDat['reshasxx'] ." impresa por AGENCIA DE ADUANAS MARIO LONDOÑO S.A. NIVEL 1 NIT.890.902.266-2.";
  $pdf->MultiCell(135, 3.2, utf8_decode($cResolucion), 0, 'L');
  $pdf->setX($nPosX);
  $pdf->MultiCell(135, 3.2, utf8_decode("Esta factura causara intereses moratorios a la tasa mas alta autorizada por ley a partir de la fecha acordada para el pago, si este fuera incumplido."), 0, 'L');

  $pdf->setXY($nPosX, $pyy + 54);
  $pdf->SetFont('Arial', 'B', 8);
  $pdf->MultiCell(135, 3.2, utf8_decode("Consignar en Banco de Occidente 400058467 Cta. cte, Bancolombia 60400010197 Cta. cte, a nombre de Agencia de Aduanas Mario Londoño."), 0, 'L');

  $pdf->setXY($nPosX + 137, $pyy + 50);
  $pdf->SetFont('Arial', 'B', 8);
  $pdf->Cell(68, 4, "TOTAL EN LETRAS", 0, 0, 'C');
  $pdf->Ln(4);
  $pdf->setX($nPosX + 137);
  $pdf->SetFont('Arial', '', 7);
  $pdf->MultiCell(68, 3, utf8_decode($nTotPag1), 0, 'C');

  ### Columna de Subtotales ##
  //Para la nota debito el campo de ip gravados se trae del subtotal de ingresos porpios gravados
  $nTotFac = (floatval($nTotPcc + $nSubTotGmf + $nSubToIPGra + $nSubToIPNoGra + $nSubToIPIva)) - ($nTotIca + $nTotIva);
  $pdf->setXY($nPosX + 136, $pyy + 1);
  $pdf->SetFont('Arial', 'B', 8);
  $pdf->Cell(35, 4, "SUBTOTAL SERVICIOS GRAVADOS", 0, 0, 'L');
  $pdf->Cell(34, 4, number_format(floatval($nSubToIPGra), 0), 0, 0, 'R');
  $pdf->Ln(4.5);
  $pdf->setX($nPosX + 136);
  $pdf->Cell(35, 4, "+ IVA", 0, 0, 'L');
  $pdf->Cell(34, 4, number_format($nSubToIPIva, 0), 0, 0, 'R');
  $pdf->Ln(4.5);
  $pdf->setX($nPosX + 136);
  $pdf->Cell(35, 4, "- RETE IVA", 0, 0, 'L');
  $pdf->Cell(34, 4, number_format($nTotIva, 0), 0, 0, 'R');
  $pdf->Ln(4.5);
  $pdf->setX($nPosX + 136);
  $pdf->Cell(35, 4, "- RETE ICA", 0, 0, 'L');
  $pdf->Cell(34, 4, number_format(($nTotAIca == 0) ? $nTotIca : 0, 0), 0, 0, 'R');
  $pdf->Ln(4.5);
  $pdf->setX($nPosX + 136);
  $pdf->Cell(35, 4, "TOTAL PAGOS, SERVICIOS E IVA", 0, 0, 'L');
  $pdf->Cell(34, 4, number_format(floatval($nTotPcc + $nSubTotGmf + $nSubToIPGra + $nSubToIPNoGra + $nSubToIPIva), 0), 0, 0, 'R');
  $pdf->Ln(4.5);
  $pdf->setX($nPosX + 136);
  $pdf->Cell(35, 4, "TOTAL FACTURA", 0, 0, 'L');
  $pdf->Cell(34, 4, number_format($nTotFac, 0), 0, 0, 'R');
  $pdf->Ln(4.5);
  $pdf->setX($nPosX + 136);
  $pdf->Cell(35, 4, "- ANTICIPOS", 0, 0, 'L');
  $pdf->Cell(34, 4, number_format($nTotAnt, 0), 0, 0, 'R');
  $pdf->Ln(4.5);
  $pdf->setX($nPosX + 136);
  $pdf->Cell(35, 4, "TOTAL A PAGAR", 0, 0, 'L');
  $pdf->Cell(34, 4, number_format($nTotPag, 0), 0, 0, 'R');
  $pdf->Ln(4.5);
  $pdf->setX($nPosX + 136);
  $pdf->Cell(35, 4, "TOTAL SALDO A FAVOR", 0, 0, 'L');
  $pdf->Cell(34, 4, number_format($nSaldoFavor, 0), 0, 0, 'R');

  $pdf->Line($nPosX, $pyy + 20, $nPosX + 136, $pyy + 20);
  $pdf->Line($nPosX, $pyy + 50, $nPosX + 205, $pyy + 50);
  $pdf->Line($nPosX + 136, $pyy, $nPosX + 136, $pyy + 65);
  $pdf->Rect($nPosX, $pyy, 205, 65);

  $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
  $pdf->Output($cFile);

  if (file_exists($cFile)){
    chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
  } else {
    f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
  }
  echo "<html><script>document.location='$cFile';</script></html>";
?>

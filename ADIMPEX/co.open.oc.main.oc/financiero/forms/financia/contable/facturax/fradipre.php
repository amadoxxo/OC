<?php
  /**
	 * Imprime Vista Previa Factura de Venta LOGINCAR.
	 * --- Descripcion: Permite Imprimir Vista Previa de la Factura de Venta.
	 * @author Victor Vivenzio <victor.vivenzio@opentecnologia.com.co>
	 */

	// ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");

  include("../../../../libs/php/utility.php");
  include("../../../../libs/php/utiliqdo.php");

  $cRoot = $_SERVER['DOCUMENT_ROOT'];
  $cEstiloLetra  = 'verdana';
  $cEstiloLetrab = 'verdanab';

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

  switch ($_POST['cTerCalInt']) {
    case "NORESIDENTE":
      $cTerCal  = $_POST['cTerCalInt'];
    break;
    default:
      if ($vSysStr['financiero_facturacion_aplica_impuestos_facturar_a'] == "SI") {
        $cTerCal  = $_POST['cTerCalInt'];
      } else {
        $cTerCal  = $_POST['cTerCal'];
      }
    break;
  }

	## Traigo el CLINOMXX o Razon Social de la Agencia de Aduana Dietrich Para encabezado de factura ##
	$qAgeDat  = "SELECT ";
	$qAgeDat .= "$cAlfa.SIAI0150.CLIIDXXX, ";
	$qAgeDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\", $cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X)) AS CLINOMXX, ";
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
  $qResDat .= "regestxx = \"ACTIVO\" ";
  $xResDat  = f_MySql("SELECT","",$qResDat,$xConexion01,"");
  $nFilRes  = mysql_num_rows($xResDat);
  // f_Mensaje(__FILE__,__LINE__,$qResDat);
  if ($nFilRes > 0) {
    while($xREE = mysql_fetch_array($xResDat)){
      $vResDat[count($vResDat)] =$xREE;
    }
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

  ##Buscando nombre del usuario facturador
  $qUsrFac  = "SELECT USRNOMXX ";
  $qUsrFac .= "FROM $cAlfa.SIAI0003 ";
  $qUsrFac .= "WHERE ";
  $qUsrFac .= "USRIDXXX = \"{$_COOKIE['kUsrId']}\" LIMIT 0,1 ";
  $xUsrFac  = f_MySql("SELECT","",$qUsrFac,$xConexion01,"");
  $vUsrFac  = mysql_fetch_array($xUsrFac);
  ##Fin Buscando nombre del usuario facturador

  ##Consulto en la SIAI0150 Datos del Facturado A: ##
  $qCliDat  = "SELECT ";
  $qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX, ";
	$qCliDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\", $cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X)) AS CLINOMXX, ";
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
	// f_Mensaje(__FILE__,__LINE__,$qCliDat);
  $nFilCli  = mysql_num_rows($xCliDat);
  if ($nFilCli > 0) {
    $vCliDat = mysql_fetch_array($xCliDat);
  }
  ##Consulto en la SIAI0150 Datos del Facturado A: ##

  ##Consulto en la SIAI0150 Datos del Importador(IMP):  ##
  ## Se busca  a través del $_POST['cTerId']
  $qCliImp  = "SELECT ";
  $qCliImp .= "$cAlfa.SIAI0150.CLIIDXXX, ";
  $qCliImp .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\", $cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X)) AS CLINOMCL ";
  $qCliImp .= "FROM $cAlfa.SIAI0150 ";
  $qCliImp .= "WHERE ";
  $qCliImp .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$_POST['cTerId']}\" AND ";
  $qCliImp .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
  $xCliImp  = f_MySql("SELECT","",$qCliImp,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qCliImp);
  $nFilCliImp  = mysql_num_rows($xCliImp);
  if ($nFilCliImp > 0) {
    $vCliImp = mysql_fetch_array($xCliImp);
  }
  ##Consulto en la SIAI0150 Datos del Importador(IMP): ##

  // ##Traigo Ciudad del Facturado A ##
  // $qCiuDat  = "SELECT * ";
  // $qCiuDat .= "FROM $cAlfa.SIAI0055 ";
  // $qCiuDat .= "WHERE ";
  // $qCiuDat .= "$cAlfa.SIAI0055.PAIIDXXX = \"{$vCliDat['PAIIDXXX']}\" AND ";
  // $qCiuDat .= "$cAlfa.SIAI0055.DEPIDXXX = \"{$vCliDat['DEPIDXXX']}\" AND ";
  // $qCiuDat .= "$cAlfa.SIAI0055.CIUIDXXX = \"{$vCliDat['CIUIDXXX']}\" AND ";
  // $qCiuDat .= "$cAlfa.SIAI0055.REGESTXX = \"ACTIVO\" ";
  // //f_Mensaje(__FILE__,__LINE__,$qCiuDat);
  // $xCiuDat  = f_MySql("SELECT","",$qCiuDat,$xConexion01,"");
  // $nFilCiu  = mysql_num_rows($xCiuDat);
  // if ($nFilCiu > 0) {
  //   $vCiuDat = mysql_fetch_array($xCiuDat);
  // }
  // ##Fin Traigo Ciudad del Facturado A ##

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
	$cDocId  = "";
	$cDocSuf = "";
	for ($i=0;$i<$_POST['nSecuencia_Dos'];$i++) {
    if ($cDocId != "") {
      $cDocId   = $_POST['cDosNro_DOS'.($i+1)];
      $cDocSuf  = $_POST['cDosSuf_DOS'.($i+1)];
      $cSucId   = $_POST['cSucId_DOS'.($i+1)];
    }
    $cDOs .= $_POST['cDosNro_DOS'.($i+1)] . "-" . $_POST['cDosSuf_DOS'.($i+1)] . ", ";
    // $i = $_POST['nSecuencia_Dos'];
  }//for ($i=0;$i<$_POST['nSecuencia_Dos'];$i++) {
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

  //Consultando el nombre de la sucursal del DO
  $qSucDes  = "SELECT sucdesxx ";
  $qSucDes .= "FROM $cAlfa.fpar0008 ";
  $qSucDes .= "WHERE ";
  $qSucDes .= "$cAlfa.fpar0008.sucidxxx = \"$cSucId\" LIMIT 0,1";
  $xSucDes  = mysql_query($qSucDes,$xConexion01);
  // f_Mensaje(__FILE__,__LINE__,$qSucDes."~".mysql_num_rows($xSucDes));
  $vSucDes  = mysql_fetch_array($xSucDes);
  $cSucDes  = $vSucDes['sucdesxx'];

  ##Traigo Ciudad del Facturado A ##
  $qCiuDat  = "SELECT LINDESXX ";
  $qCiuDat .= "FROM $cAlfa.SIAI0119 ";
  $qCiuDat .= "WHERE ";
  $qCiuDat .= "$cAlfa.SIAI0119.LINIDXXX = \"$cSucId\" AND ";
  $qCiuDat .= "$cAlfa.SIAI0119.REGESTXX = \"ACTIVO\" ";
  //f_Mensaje(__FILE__,__LINE__,$qCiuDat);
  $xCiuDat  = f_MySql("SELECT","",$qCiuDat,$xConexion01,"");
  $nFilCiu  = mysql_num_rows($xCiuDat);
  if ($nFilCiu > 0) {
    $vCiuDat = mysql_fetch_array($xCiuDat);
  }
  ##Fin Traigo Ciudad del Facturado A ##

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

	##Traigo los Documentos que estan marcados como PAGOIMPUESTOS##
	$qDatCom  = "SELECT ";
	$qDatCom .= "ctoidxxx, ";
	$qDatCom .= "pucidxxx ";
	$qDatCom .= "FROM $cAlfa.fpar0119 ";
	$qDatCom .= "WHERE ";
	$qDatCom .= "(ctoptaxg = \"SI\" OR ctoptaxl = \"SI\") AND ";
	$qDatCom .= "regestxx = \"ACTIVO\" ";
	$xDatCom  = f_MySql("SELECT","",$qDatCom,$xConexion01,"");
	$vComImp = array();
	while($xRDC = mysql_fetch_array($xDatCom)){
		$nInd_mComImp = count($vComImp);
		$vComImp[] = $xRDC['ctoidxxx']."~".$xRDC['pucidxxx'];
	}
	##Fin Traigo los Documentos que estan marcados como PAGOIMPUESTOS##

	##Switch para traer Datos de Do Dependiendo del Tipo de Operacion ##
  switch ($vDceDat['doctipxx']){
  	case "IMPORTACION":
    case "TRANSITO":
  		##Traigo Datos de la SIAI0200 DATOS DEL DO ##
        $qDoiDat  = "SELECT * ";
        $qDoiDat .= "FROM $cAlfa.SIAI0200 ";
        $qDoiDat .= "WHERE $cAlfa.SIAI0200.DOIIDXXX = \"$cDocId\" AND ";
        $qDoiDat .= "$cAlfa.SIAI0200.DOISFIDX = \"$cDocSuf\" AND ";
        $qDoiDat .= "$cAlfa.SIAI0200.ADMIDXXX = \"$cSucId\" ";
        // f_Mensaje(__FILE__,__LINE__,$qDoiDat);
        $xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
        $nFilDoi  = mysql_num_rows($xDoiDat);
        if ($nFilDoi > 0) {
          $vDoiDat  = mysql_fetch_array($xDoiDat);
        }
        ##Fin Traigo Datos de la SIAI0200 DATOS DEL DO ##

        //Consulta para traer nombre de pais de destino de la tabla SIAI0119
        $qDesPai  = "SELECT ";
        $qDesPai .= "LINDESXX "; //Nombre de Ciudad
        $qDesPai .= "FROM $cAlfa.SIAI0119 ";
        $qDesPai .= "WHERE ";
        $qDesPai .= "LINIDXXX = \"{$vDoiDat['LINIDXXX']}\"";
        $xDesPai  = f_Mysql("SELECT","",$qDesPai,$xConexion01);
        //f_Mensaje(__FILE__,__LINE__,$qDesPai."~".mysql_num_rows($xDesPai));
        $vDesPai  = mysql_fetch_array($xDesPai);
        //Fin Consulta para traer nombre de pais

        // Consulta para traer descripcion de pais de la tabla SIAI0052
        $qPaises  = "SELECT ";
        $qPaises .= "PAIDESXX "; // Descripcion
        $qPaises .= "FROM $cAlfa.SIAI0052 ";
        $qPaises .= "WHERE ";
        $qPaises .= "PAIIDXXX = \"{$vDoiDat['PAIIDXXX']}\"";
        $xPaises  = f_MySql("SELECT","",$qPaises,$xConexion01,"");
        // f_Mensaje(__FILE__,__LINE__,$qPaises."~".mysql_num_rows($xPaises));
        $vPaises  = mysql_fetch_array($xPaises);
        // Fin de consulta para traer descripcion de pais de la tabla SIAI0052

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
        // f_Mensaje(__FILE__,__LINE__,$qDecDat);
        $xDecDat  = f_MySql("SELECT","",$qDecDat,$xConexion01,"");
        $nFilDec  = mysql_num_rows($xDecDat);
        if ($nFilDec > 0) {
          $vDecDat  = mysql_fetch_array($xDecDat);
        }

        /*
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
        }*/

        /*
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
          $vNomCom = mysql_fetch_array($xDceDat);
        }*/

        //Consulta para traer nombre de mercancia
        $qNomCom  = "SELECT ";
        $qNomCom .= "LPRDESXX "; //Descripcion de Mercancia
        $qNomCom .= "FROM $cAlfa.SIAI0238 ";
        $qNomCom .= "WHERE ";
        $qNomCom .= "LPRIDXXX = \"{$vDoiDat['LPRID3XX']}\"";
        $xNomCom  = f_MySql("SELECT","",$qNomCom,$xConexion01,"");
        // f_Mensaje(__FILE__,__LINE__,$qNomCom."~".mysql_num_rows($xNomCom));
        $vNomCom  = mysql_fetch_array($xNomCom);
        //Fin de consulta para traer nombre de mercancia
        $cTasCam = $vDoiDat['TCATASAX']; // Tasa de cambio traida de la BD cuando no cumple ninguno de los casos.
    		##Cargo Variables para Impresion de Datos de Do ##
        $cDocTra     = $vDoiDat['DGEDTXXX']; //Documento de Transporte
        $cBultos     = $vDoiDat['DGEBULXX']; //Bultos
        $cPesBru     = $vDoiDat['DGEPBRXX']; //Peso Bruto
        $nValAdu     = $vDecDat['LIMCIFXX'];
        $cPaisOrigen = $vDoiDat['PAIIDXXX'];
        $cOpera      = "CIF: $";
        $cPaisOrigen = $vPaises['PAIDESXX']; // Nombre de Ciudad
        $cAduana     = $vDesPai['LINDESXX']; // Nombre de Destino
        $cNumPed     = $vDoiDat['DOIPEDXX']; // Numero de Pedido
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

      ##Trayendo aduana de salida##
      $qAduSal  = "SELECT odiid2xx ";
      $qAduSal .= "FROM $cAlfa.siae0200 ";
      $qAduSal .= "WHERE ";
      $qAduSal .= "$cAlfa.siae0200.dexidxxx = \"$cDocId\" AND ";
      $qAduSal .= "$cAlfa.siae0200.admidxxx = \"$cSucId\" AND ";
      $qAduSal .= "$cAlfa.siae0200.odiid2xx != \"\" LIMIT 0,1 ";
      $xAduSal  = f_MySql("SELECT","",$qAduSal,$xConexion01,"");
      $vDesAdu = array();
      if ($vAduSal['odiid2xx'] != "") {
        $vAduSal  = mysql_fetch_array($xAduSal);
        ##Tayendo descripcion Aduana de salida
        $qDesAdu  = "SELECT ODIDESXX ";
        $qDesAdu .= "FROM $cAlfa.SIAI0103 ";
        $qDesAdu .= "WHERE ";
        $qDesAdu .= "ODIIDXXX = \"{$vAduSal['odiid2xx']}\" ";
        $qDesAdu .= "LIMIT 0,1 ";
        $xDesAdu  = f_MySql("SELECT","",$qDesAdu,$xConexion01,"");
        $vDesAdu  = mysql_fetch_array($xDesAdu);
      }

      ##Trayendo documento de transpote
      $qDocTran  = "SELECT dexdocxx ";
      $qDocTran .= "FROM $cAlfa.siae0200 ";
      $qDocTran .= "WHERE ";
      $qDocTran .= "$cAlfa.siae0200.dexidxxx = \"$cDocId\" AND ";
      $qDocTran .= "$cAlfa.siae0200.admidxxx = \"$cSucId\" AND ";
      $qDocTran .= "$cAlfa.siae0200.dexdocxx != \"\" LIMIT 0,1 ";
      $xDocTran  = f_MySql("SELECT","",$qDocTran,$xConexion01,"");
      $vDocTran  = mysql_fetch_array($xDocTran);

      ##Trayendo la tasa de cambio##
      $qTasa  = "SELECT dextasax ";
      $qTasa .= "FROM $cAlfa.siae0200 ";
      $qTasa .= "WHERE ";
      $qTasa .= "$cAlfa.siae0200.dexidxxx = \"$cDocId\" AND ";
      $qTasa .= "$cAlfa.siae0200.admidxxx = \"$cSucId\" AND ";
      $qTasa .= "($cAlfa.siae0200.dextasax+0) != \"0\" LIMIT 0,1 ";
      $xTasa  = f_MySql("SELECT","",$qTasa,$xConexion01,"");
      $vTasa  = mysql_fetch_array($xTasa);

      ##Trayendo codigo destino##
      $qDestino  = "SELECT dexpaidf ";
      $qDestino .= "FROM $cAlfa.siae0200 ";
      $qDestino .= "WHERE ";
      $qDestino .= "$cAlfa.siae0200.dexidxxx = \"$cDocId\" AND ";
      $qDestino .= "$cAlfa.siae0200.admidxxx = \"$cSucId\" AND ";
      $qDestino .= "$cAlfa.siae0200.dexpaidf != \"\" LIMIT 0,1 ";
      $xDestino  = f_MySql("SELECT","",$qDestino,$xConexion01,"");
      $vPaiSae = array();
      if (mysql_num_rows($xDestino) > 0) {
        $vDestino  = mysql_fetch_array($xDestino);
        ##Consulta para traer la descripcion del destino final de la tabla SIAI0052
        $qPaiSae  = "SELECT ";
        $qPaiSae .= "PAIDESXX ";
        $qPaiSae .= "FROM $cAlfa.SIAI0052 ";
        $qPaiSae .= "WHERE ";
        $qPaiSae .= "PAIIDXXX = \"{$vDestino['dexpaidf']}\"";
        $xPaiSae  = f_MySql("SELECT", "", $qPaiSae,$xConexion01,"");
        // f_Mensaje(__FILE__,__LINE__,$qPaiSae."~".mysql_num_rows($xpaiSae));
        $vPaiSae  = mysql_fetch_array($xPaiSae);
        ##Fin de consulta para traer la descripcion del destino final de la tabla SIAI0052
      }

      ##Traigo Valor fob, peso neto, peso bruto, bultos de la tabla de Items de Exportaciones siae0201 ##
      /*$qIteDat  = "SELECT ";
      $qDatSae .= "SUM($cAlfa.siae0200.dextofob) AS dextofob, ";
      $qIteDat .= "SUM($cAlfa.siae0201.itefobxx) AS itefobxx, ";
      $qIteDat .= "SUM($cAlfa.siae0201.itepbrxx) AS itepbrxx, ";
      $qIteDat .= "SUM($cAlfa.siae0201.itepnexx) AS itepnexx, ";
      $qIteDat .= "SUM($cAlfa.siae0201.itebulxx) AS itebulxx ";
      $qIteDat .= "FROM $cAlfa.siae0201 ";
      $qIteDat .= "WHERE ";
      $qIteDat .= "$cAlfa.siae0201.dexidxxx = \"$cDocId\" AND ";
      $qIteDat .= "$cAlfa.siae0201.admidxxx = \"$cSucId\" ";
      $xIteDat  = f_MySql("SELECT","",$qIteDat,$xConexion01,"");
      $nFilIte  = mysql_num_rows($xIteDat);
      if ($nFilIte > 0) {
        $vIteDat = mysql_fetch_array($xIteDat);
      }*/

      ##Consulta para traer datos de la sae (tasa de cambio, peso, piezas, destino final)
      $qDatSae  = "SELECT ";
      $qDatSae .= "SUM($cAlfa.siae0200.dextofob) AS dextofob, ";
      $qDatSae .= "SUM($cAlfa.siae0200.dexpbrxx) AS dexpbrxx, "; //Sumatoria de Peso Bruto
      $qDatSae .= "SUM($cAlfa.siae0200.dexbulxx) AS dexbulxx  "; //Sumatoria de Bultos
      $qDatSae .= "FROM $cAlfa.siae0200 " ;
      $qDatSae .= "WHERE ";
      $qDatSae .= "$cAlfa.siae0200.dexidxxx = \"$cDocId\" AND ";
      $qDatSae .= "$cAlfa.siae0200.admidxxx = \"$cSucId\"";
      $xDatSae  = f_MySql("SELECT","",$qDatSae,$xConexion01,"");
      //f_Mensaje(__FILE__,__LINE__,$qDatSae."~".mysql_num_rows($xDatSae));
      $vDatSae  = mysql_fetch_array($xDatSae);
      ##Fin de consulta para traer datos de la sae

      ##Consulta para traer la descripcion del pais cuando este sea CO
      $qPaises  = "SELECT ";
      $qPaises .= "PAIDESXX "; //descripcion Pais
      $qPaises .= "FROM $cAlfa.SIAI0052 ";
      $qPaises .= "WHERE ";
      $qPaises .= "PAIIDXXX = \"CO\"";
      $xPaises  = f_MySql("SELECT", "", $qPaises, $xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qPaises."~".mysql_num_rows($xPaises));
      $vPaises  = mysql_fetch_array($xPaises);
      ##Fin de consulta para traer la descripcion del pais cuando este sea CO

      ##Consulta para traer linea de producto
      $qLinPro  = "SELECT LPRDESXX ";
      $qLinPro .= "FROM $cAlfa.SIAI0238 ";
      $qLinPro .= "WHERE ";
      $qLinPro .= "LPRIDXXX = \"{$vDexDat['lprid3xx']}\"";
      $xLinPro  = f_MySql("SELECT","", $qLinPro, $xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qLinPro."~".mysql_num_rows($xLinPro));
      $vNomCom  = mysql_fetch_array($xLinPro);
      ##Fin de consulta para traer linea de producto

      $cTasCam = $vTasa['dextasax']; // Tasa traida de la base de datos.
      ##Cargo Variables para Impresion de Datos de Do ##
      $cDocTra     = $vDocTran['dexdocxx']; //Documento de Transporte
      $cBultos     = $vDatSae['dexbulxx'];  //Bultos
      $cPesBru     = $vDatSae['dexpbrxx'];  //Peso Bruto
      $nValAdu     = $vDatSae['dextofob'];
      $cPaisOrigen = $vPaises['PAIDESXX'];
      $cOpera      = "FOB: US$"; // FOB
      $cAduana     = $vPaiSae['PAIDESXX'];
      $cNumPed     = $vDexDat['dexpedxx'];
      ##Fin Cargo Variables para Impresion de Datos de Do ##
  	break;
  	case "OTROS": default:
      // $cTasCam = $_POST['nTasaCambio']; // tasa de cambio traida de lo que se inserto en el paso 1 de la facturacion
  	break;
  }//switch (){
  ##Fin Switch para traer Datos de Do Dependiendo del Tipo de Operacion ##

  // Consulta las condiciones comerciales del cliente o el grupo de tarifas para validar si se debe agrupar por tipo de operacion
  $cAgruparIp = "NO";
  $qCondiCom  = "SELECT ";
  $qCondiCom .= "gtaidxxx, ";
  $qCondiCom .= "cccagrta ";
  $qCondiCom .= "FROM $cAlfa.fpar0151 ";
  $qCondiCom .= "WHERE ";
  $qCondiCom .= "cliidxxx = \"{$_POST['cTerId']}\" AND ";
  $qCondiCom .= "regestxx = \"ACTIVO\" LIMIT 0,1";
  $xCondiCom  = f_MySql("SELECT", "", $qCondiCom, $xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qCondiCom."~".mysql_num_rows($xCondiCom));
  if (mysql_num_rows($xCondiCom) > 0) {
    $vCondiCom = mysql_fetch_array($xCondiCom);

    if ($vCondiCom['cccagrta'] == "SI") {
      $cAgruparIp = "SI";
    } else {
      $qGruTari  = "SELECT ";
      $qGruTari .= "gtaagrta ";
      $qGruTari .= "FROM $cAlfa.fpar0111 ";
      $qGruTari .= "WHERE ";
      $qGruTari .= "gtaidxxx = \"{$vCondiCom['gtaidxxx']}\" AND ";
      $qGruTari .= "regestxx = \"ACTIVO\" LIMIT 0,1";
      $xGruTari  = f_MySql("SELECT", "", $qGruTari, $xConexion01, "");
      if (mysql_num_rows($xGruTari) > 0) {
        $vGruTari   = mysql_fetch_array($xGruTari);
        $cAgruparIp = ($vGruTari['gtaagrta'] == "SI") ? "SI" : "NO";
      }
    }
  }

  // Consulta la descripcion personalizada de los conceptos de cobro 100-200-300
  $qCtoCobro  = "SELECT ";
  $qCtoCobro .= "seridxxx, ";
  $qCtoCobro .= "serdespx ";
  $qCtoCobro .= "FROM $cAlfa.fpar0129 ";
  $qCtoCobro .= "WHERE ";
  $qCtoCobro .= "seridxxx IN (\"100\",\"200\",\"300\") AND ";
  $qCtoCobro .= "regestxx = \"ACTIVO\"";
  $xCtoCobro  = f_MySql("SELECT", "", $qCtoCobro, $xConexion01, "");
  $vCtoDesc = array();
  if (mysql_num_rows($xCtoCobro) > 0) {
    while ($xRCC = mysql_fetch_array($xCtoCobro)) {
      $vCtoDesc["{$xRCC['seridxxx']}"] = $xRCC['serdespx'];
    }
  }

	#Agrupo Ingresos Propios
  $mIP = array();
  for($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
    //Traigo las cantidades y el detalle de los IP del utiliqdo.php
    $vDatosIp = array();
    $cObs = "|".$_POST['cSerId_IPA'.($i+1)]."~".$_POST['cFcoId_IPA'.($i+1)]."~".$_POST['cComObs_IPA'.($i+1)]."|";
    $vDatosIp = f_Cantidad_Ingreso_Propio($cObs,'',$_POST['cSucId_IPA'.($i+1)],$_POST['cDosNro_IPA'.($i+1)],$_POST['cDosSuf_IPA'.($i+1)]);

    $cSerId  = substr($_POST['cSerId_IPA'.($i+1)], 0, 1);
    $cIndice = $_POST['cComId_IPA'.($i+1)];
    $cDesIp  = $vDatosIp[0];

    // Se agrupan los ingresos propios por tipo de operacion (100-200-300) solo si aplica para el cliente o grupo
    if ($cAgruparIp == "SI") {
      switch ($cSerId) {
        case '1':
          $cIndice = 100;
          $cDesIp  = $vCtoDesc[$cIndice]. " ";
          $mIP[$cIndice]['agrupaip'] = "SI";
        break;
        case '2':
          $cIndice = 200;
          $cDesIp  = $vCtoDesc[$cIndice]. " ";
          $mIP[$cIndice]['agrupaip'] = "SI";
        break;
        case '3':
          $cIndice = 300;
          $cDesIp  = $vCtoDesc[$cIndice]. " ";
          $mIP[$cIndice]['agrupaip'] = "SI";
        break;
        default:
          // No hace nada
        break;
      }
    }

    $mIP[$cIndice]['ctoidxxx']  = $_POST['cComId_IPA'.($i+1)];
    $mIP[$cIndice]['comobsxx']  = $cDesIp;
    $mIP[$cIndice]['comvlrxx'] += $_POST['nComVlr_IPA'.($i+1)];
    $mIP[$cIndice]['compivax']  = $_POST['nComPIva_IPA'.($i+1)]; // Porcentaje IVA
    $mIP[$cIndice]['comvlr01'] += $_POST['nComVIva_IPA'.($i+1)]; // Valor Iva
    //Cantidad FE
    $mIP[$cIndice]['unidadfe'] = $vDatosIp[2];
    $mIP[$cIndice]['canfexxx'] += $vDatosIp[1];

    //Cantidad por condicion especial
    for ($nP=0; $nP<count($vDatosIp[3]); $nP++) {
      $mIP[$cIndice]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] = $vDatosIp[3][$nP]['valpdfxx'];
    }
  }//for($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {

  $mIngPro = array();
  foreach ($mIP as $cKey => $mValores) {
    $mIngPro[count($mIngPro)] = $mValores;
  }

	##Codigo para imprimir los ingresos para terceros ##
	$mIngTer = array();
  for ($i=0;$i<$_POST['nSecuencia_PCCA'];$i++) {
    $vTercero = explode("^",$_POST['cComObs_PCCA'.($i+1)]);
    $mComObs_IP = stripos($_POST['cComObs_PCCA'.($i+1)], "[");
    $nSwitch_Find = 0;
    for ($j=0;$j<count($mIngTer);$j++) {
   		//F_Mensaje(__FILE__,__LINE__,$_POST['cComId_PCCA'.($i+1)]. " - ".$mIngTer[$j]['cComId']);
      if ($_POST['cComId_PCCA'.($i+1)] == $mIngTer[$j]['cComId'] && trim($vTercero[2]) == $mIngTer[$j]['cTerId']) {
        $nSwitch_Find = 1;

        $cComCsc3 = ($_POST['cComDocIn_PCCA'.($i+1)] != "") ?  $_POST['cComDocIn_PCCA'.($i+1)] : $_POST['cComCsc3_PCCA'.($i+1)];
        $mIngTer[$j]['cComCsc3'] .= ((strlen($mIngTer[$nInd_mIngTer]['cComCsc3'])+strlen("/".$_POST['cComCsc3_PCCA'.($i+1)])) <= 40) ? "/".$cComCsc3 : "";
        $mIngTer[$j]['nComVlr']   += $_POST['nComVlr_PCCA'.($i+1)];
        $mIngTer[$j]['nBaseIva']  += $_POST['nBaseIva_PCCA'.($i+1)];
        $mIngTer[$j]['nVlrIva']   += $_POST['nVlrIva_PCCA'.($i+1)];
      }
    }

    if ($nSwitch_Find == 0) { // No lo encontro en la matriz para pintar en la factura
			$nInd_mIngTer = count($mIngTer);

			// if (substr_count($_POST['cComObs_PCCA' .($i+1)]," DIAN") > 0 ||
 				 // (trim(substr($_POST['cComObs_PCCA'.($i+1)],0,$mComObs_IP)) == "RECIBO OFICIAL DE PAGO TRIBUTOS ADUANEROS Y SANCIO")	||
 					// substr_count($_POST['cComObs_PCCA' .($i+1)],"DECLARACION") > 0 ) {
//
 					// $mIngTer[$nInd_mIngTer]['cComObs']  = "TRIBUTOS";
 					// $mIngTer[$nInd_mIngTer]['cTerNom']  = "DIAN";
			// } else {
					// $mIngTer[$nInd_mIngTer]['cComObs']  = trim($vTercero[0]);
					// $mIngTer[$nInd_mIngTer]['cTerNom']  = trim($vTercero[1]);
			// }
			if (in_array("{$_POST['cComId_PCCA'  .($i+1)]}~{$_POST['cPucId_PCCA'  .($i+1)]}", $vComImp) == true) {
				$mIngTer[$nInd_mIngTer]['cComObs'] = "TRIBUTOS ADUANEROS";
				$mIngTer[$nInd_mIngTer]['cTerNom']  = "";
				$mIngTer[$nInd_mIngTer]['cComCsc3'] = "";
			} else {
				$mIngTer[$nInd_mIngTer]['cComObs'] = trim($vTercero[0]);
				$mIngTer[$nInd_mIngTer]['cTerNom']  = trim($vTercero[1]) ." FACT. N";
				$mIngTer[$nInd_mIngTer]['cComCsc3'] = ($_POST['cComDocIn_PCCA'.($i+1)] != "") ?  $_POST['cComDocIn_PCCA'.($i+1)] : $_POST['cComCsc3_PCCA'.($i+1)];
			}


      $mIngTer[$nInd_mIngTer]['cComId']   = $_POST['cComId_PCCA'  .($i+1)];

			$mIngTer[$nInd_mIngTer]['cTerId']  = trim($vTercero[2]);

      $mIngTer[$nInd_mIngTer]['nComVlr']  = $_POST['nComVlr_PCCA' .($i+1)];
      $mIngTer[$nInd_mIngTer]['nBaseIva'] = $_POST['nBaseIva_PCCA'.($i+1)];
      $mIngTer[$nInd_mIngTer]['nVlrIva']  = $_POST['nVlrIva_PCCA'.($i+1)];
      $mIngTer[$nInd_mIngTer]['cTipo']    = $_POST['cTipo_PCCA'.($i+1)];
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
      global $cCscFac; global $vConDat; global $cPaisOrigen; global $cNumPed; global $vNomCom;
      global $vIdContacto; global $cEstiloLetra; global $cEstiloLetrab; global $cAduana;  global $_COOKIE;
      global $vCliDat; global $vCliImp; global $cDocSuf; global $cDOs;

      $nPosX = 10;
      $nPosY = 8;

      /*** Impresion de Logos Agencias de Aduanas Financiero Contable ***/
      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/facturaadimpex.jpg', 0, 0, 220, 281);
      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoadimpex3.jpg', $nPosX, 21, 45, 10);

      // $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoadimpex2.jpg',$nPosX,$nPosY,36,30);

      /*** Nombre de la Agencia de Aduandas. ***/
      $nPosY = $nPosY-1;
      $this->setXY($nPosX+32,$nPosY);
      $this->SetFont('verdanab','',11);
      $this->Cell(120,5,"AGENCIA DE ADUANAS ADUANAMIENTOS ",0,0,'C');
      $nPosY = $nPosY+5;
      $this->setXY($nPosX+32,$nPosY);
      $this->SetFont('verdanab','',11);
      $this->Cell(120,5,"IMPORTACIONES Y EXPORTACIONES S.A.S. NIVEL II",0,0,'C');

      /*** Información Agencia de Aduanas. ***/
      $nPosY = $nPosY+5;
      $this->setXY($nPosX+32,$nPosY);
      $this->SetFont('verdanab','',6);
      $this->Cell(120,3,"NIT. 830.032.263.9 - ACTIVIDAD ECON".chr(211)."MICA 5229 - IVA R".chr(201)."GIMEN COMUN",0,0,'C');

      $nPosY = $nPosY+3;
      $this->setXY($nPosX+32,$nPosY);
      $this->SetFont('verdanab','',6);
      $this->Cell(120,3,"NO SOMOS GRANDES CONTRIBUYENTES NI AUTORETENEDORES",0,0,'C');

      /*** Resolución. ***/

      $nPosY = $nPosY+4;
      $this->setXY($nPosX+32,$nPosY);
      $this->SetFont('verdana','',6);
      $this->Cell(120,3,"RESOLUCI".chr(211)."N FACTURACI".chr(211)."N No. ".$vResDat[0]['residxxx']." del ".$vResDat[0]['resfdexx']." AUTORIZA",0,0,'C');

      $cTextoRes = "";
      for($nI = 0; $nI < count($vResDat) ; $nI++ ){
        $dFechaInicial = date_create($vResDat[$nI]['resfdexx']);
        $dFechaFinal = date_create($vResDat[$nI]['resfhaxx']);
        $nDiferencia = date_diff($dFechaInicial, $dFechaFinal);
        $nMesesVigencia = ( $nDiferencia->y * 12 ) + $nDiferencia->m + (($nDiferencia->d > 0) ? 1 : 0);
        $cTextoRes .= $vResDat[$nI]['resprexx']." desde ".$vResDat[$nI]['resdesxx']." hasta ".$vResDat[$nI]['reshasxx'];
        $cTextoRes .= " CON VIGENCIA DE {$nMesesVigencia} ".( ($nMesesVigencia > 1)? "MESES": "MES" )." - ";
      }
      $cTextoRes = substr($cTextoRes, 0,-2);

      $nPosY = $nPosY+3;
      $this->setXY($nPosX+32,$nPosY);
      $this->SetFont($cEstiloLetra,'',6);
      $this->Cell(120,3,$cTextoRes,0,0,'C');

      /*** Dirección Agencia de Aduana. ***/
      $vAgeDat['CLIDIRXX'] = "Oficina - Bogota D.C: Calle 25D No. 97 - 57 PBX.: 413 2710" ; //BORRAR
      $nPosY = $nPosY+3;
      $this->setXY($nPosX+32,$nPosY);
      $this->SetFont($cEstiloLetra,'',6);
      $this->Cell(120,3,$vAgeDat['CLIDIRXX'],0,0,'C');

      $vAgeDat['CLIDIRXX'] = "Oficina - Cartagena: Transversal 51B No. 21B - 07 Ed. Alameda de Alto Bosque PBX.: 6438866"; //BORRAR
      $nPosY = $nPosY+3;
      $this->setXY($nPosX+32,$nPosY);
      $this->SetFont($cEstiloLetra,'',6);
      $this->Cell(120,3,$vAgeDat['CLIDIRXX'],0,0,'C');

      $vAgeDat['CLIDIRXX'] = utf8_decode("Oficina - Buenaventura: Carrera 3 No. 7 - 5 oficina 405 cel: 300 794 5714"); //BORRAR
      $nPosY = $nPosY+3;
      $this->setXY($nPosX+32,$nPosY);
      $this->SetFont($cEstiloLetra,'',6);
      $this->Cell(120,3,$vAgeDat['CLIDIRXX'],0,0,'C');

      /*** Codigo Factura ***/
      $nPosY = 14;
      $this->setXY($nPosX+153,$nPosY);
      $this->SetFont('verdanab','',12);
      $this->Cell(43,5,"CODIGO 0331",0,0,'C');

      /*** Numero de Factura ***/
      $nPosY += 5;
      $this->Rect($nPosX+153,$nPosY,43,18);
      $this->setXY($nPosX+153,$nPosY);
      $this->SetFont('verdanab','',10);
      $this->Cell(43,5,"FACTURA DE VENTA",0,0,'C');

      $nPosY += 13;
      $this->setXY($nPosX+153,$nPosY);
      $this->SetFont('verdanab','',10);
      $this->Cell(43,3,"NO. ".$vResDat[0]['resprexx']."-XXXXX",0,0,'C');

      /*** Rectangulo Cabecera del documento ***/
      $nPosY = 40;
      // $nPosY = $this->getY()+8;

      $this->Rect($nPosX,$nPosY,196,27);
      $this->setXY($nPosX,$nPosY+1);

      /*** Fecha de Factura ***/
      list($anio,$mes,$dia) = explode("-",$_POST['dRegFCre']);
      $this->setX($nPosX+1);
      $this->SetFont('verdanab','',8);
      $this->Cell(29,5,"FECHA:",0,0,'L');
      $this->SetFont($cEstiloLetra,'',8);
      $this->Cell(60,5,$dia."/".$mes."/".$anio,0,0,'L');

      /*** Ciudad de Factura ***/
      $this->SetFont('verdanab','',8);
      $this->Cell(15,5,"CIUDAD:",0,0,'L');
      $this->SetFont($cEstiloLetra,'',8);
      $this->Cell(24,5,$vCiuDat['LINDESXX'],0,0,'L');

      /*** Vencimiento ***/
      $this->SetFont('verdanab','',8);
      $this->Cell(30,5,"VENCIMIENTO:",0,0,'L');
      $this->SetFont($cEstiloLetra,'',8);
      $this->Cell(36,5,utf8_decode(($_POST['cTerPla'] == 1) ? $_POST['cTerPla']." día" : $_POST['cTerPla']." días"),0,0,'L');

      /*** Cliente de Factura ***/
      $this->Ln(5);
      $this->setX($nPosX+1);
      $this->SetFont('verdanab','',8);
      $this->Cell(29,5,"SE".chr(209)."ORES:",0,0,'L');
      $this->SetFont($cEstiloLetra,'',8);
      $this->Cell(99,5,$vCliDat['CLINOMXX'],0,0,'L');

      /*** NIT Cliente de Factura ***/
      $this->SetFont('verdanab','',8);
      $this->Cell(30,5,"NIT:",0,0,'L');
      $this->SetFont($cEstiloLetra,'',8);
      $this->Cell(36,5,$_POST['cTerIdInt']."-".f_Digito_Verificacion($_POST['cTerIdInt']) ,0,0,'L');

      /*** Cliente de Factura ***/
      $this->Ln(5);
      $this->setX($nPosX+1);
      $this->SetFont('verdanab','',8);
      $this->Cell(29,5,"DIRECCI".chr(211)."N:",0,0,'L');
      $this->SetFont($cEstiloLetra,'',8);
      $this->Cell(99,5,$vCliDat['CLIDIRXX'],0,0,'L');

      /*** Telefono Cliente de Factura ***/
      $this->SetFont('verdanab','',8);
      $this->Cell(30,5,"TEL".chr(201)."FONO:",0,0,'L');
      $this->SetFont($cEstiloLetra,'',8);
      $this->Cell(36,5,$vCliDat['CLITELXX'],0,0,'L');

      /*** Documento de Transporte ***/
      $this->Ln(5);
      $this->setX($nPosX+1);
      $this->SetFont('verdanab','',8);
      $this->Cell(29,5,"GUIA AEREA B/L:",0,0,'L');
      $this->SetFont($cEstiloLetra,'',8);
      $this->Cell(40,5,$cDocTra,0,0,'L');

      /*** Bultos de Transporte ***/
      $this->SetFont('verdanab','',8);
      $this->Cell(7,5,"Bto.",0,0,'L');
      $this->SetFont($cEstiloLetra,'',8);
      $this->Cell(20,5,($cBultos+0),0,0,'L');

      /*** Peso ***/
      $this->SetFont('verdanab','',8);
      $this->Cell(8,5,"KLS.",0,0,'L');
      $this->SetFont($cEstiloLetra,'',8);
      $this->Cell(24,5,($cPesBru+0),0,0,'L');

      /*** DO ***/
      $this->SetFont('verdanab','',8);
      $this->Cell(7,5,"DO.",0,0,'L');
      $this->SetFont($cEstiloLetra,'',8);
      $this->Cell(17.5,5,rtrim($cDOs, ', '),0,0,'L');

      /*** Pedido ***/
      $this->Ln(5);
      $this->setX($nPosX+1);
      $this->SetFont('verdanab','',8);
      $this->Cell(29,5,"PEDIDO.",0,0,'L');
      $this->SetFont($cEstiloLetra,'',8);
      $this->Cell(67,5,$cNumPed,0,0,'L');

      /*** Importador ***/
      $this->SetFont('verdanab','',8);
      $this->Cell(9,5,"IMP:",0,0,'L');
      $this->SetFont($cEstiloLetra,'',8);
      $this->Cell(57,5,substr($vCliImp['CLINOMCL'],0,30),0,0,'L');

      //Datos de detalle,
      $nPosY = $this->getY()+9;
      $this->setXY($nPosX,$nPosY);

      /*** Titulo Ingresos Propios ***/
      $this->SetFillColor(100,100,100);
      $this->SetTextColor(255,255,255);
      $this->Rect($nPosX,$nPosY,196,5,"F");
      $this->SetFont('verdanab','',7);
      $this->Cell(196,5,"INGRESOS PROPIOS",0,0,'C'); // titulo Ingreso
      $this->SetTextColor(0,0,0);

      $nPosY    = $this->getY();

      /*** Rectangulo detalle del Ingresos Propios ***/
      $this->Rect($nPosX,$nPosY,196,65);
      $this->Line($nPosX,$nPosY+5,$nPosX+196,$nPosY+5);
      $this->Line($nPosX+160,$nPosY+5,$nPosX+160,$nPosY+65);

      /*** Relación de gastos por Cuenta del Cliente ***/
      $nPosY    = $this->getY()+67;

      $this->setXY($nPosX,$nPosY);
      $this->SetFillColor(100,100,100);
      $this->SetTextColor(255,255,255);
      $this->Rect($nPosX,$nPosY,196,5,"F");
      $this->SetFont('verdanab','',7);
      $this->Cell(196,5,"RELACI".chr(211)."N DE GASTOS POR CUENTA DEL CLIENTE (No Gravados) NOTA: FAVOR NO PRACTICAR RETENCI".chr(211)."N SOBRE GASTOS DE TERCEROS",0,0,'C'); // titulo Relación de gastos por Cuenta del Cliente
      $this->SetTextColor(0,0,0);

      $this->Rect($nPosX,$nPosY,196,70);
      $this->Line($nPosX,$nPosY+5,$nPosX+196,$nPosY+5);
      $this->Line($nPosX+160,$nPosY+5,$nPosX+160,$nPosY+70);

      /*** Cuadros Totales ***/

      $nPosY    = $this->getY()+65;
      $this->Line($nPosX,$nPosY+5,$nPosX,$nPosY+24);
      $this->Line($nPosX+160,$nPosY+5,$nPosX+160,$nPosY+24);
      $this->Line($nPosX+196,$nPosY+5,$nPosX+196,$nPosY+24);
      $this->Line($nPosX,$nPosY+24,$nPosX+196,$nPosY+24);

      $this->setXY($nPosX+2,$nPosY+6);
      $this->SetFont($cEstiloLetrab,'',8);
      $this->Cell(100,6,"SUBTOTAL",0,0,'L');

      $this->Ln(6);
      $this->setX($nPosX+2);
      $this->SetFont($cEstiloLetrab,'',8);
      $this->Cell(100,6,"MENOS ANTICIPO RECIBIDO R.C.",0,0,'L');

      $this->SetFont($cEstiloLetrab,'',8);
      $this->Cell(30,6,"FECHA",0,0,'L');

      $this->Ln(6);
      $this->setX($nPosX+2);
      $this->SetFont($cEstiloLetrab,'',8);
      $this->Cell(40,6,"SALDO A",0,0,'L');

      $this->SetFont($cEstiloLetrab,'',8);
      $this->Cell(25,6,"CARGO",0,0,'L');

      $this->SetFont($cEstiloLetrab,'',8);
      $this->Cell(25,6,"FAVOR",0,0,'L');

      $nPosY = $this->getY()+1.5;
      $this->Rect($nPosX+55,$nPosY,3,3);
      $this->Rect($nPosX+80,$nPosY,3,3);

     	##Fin Datos Generales de Do ##
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
      global $cRoot;   global $cPlesk_Skin_Directory;   global $cNomCopia;   global $nCopia;    global $nb;    global $nContPage; global $vCocDat; global $mCodDat; global $cSaldo;
      global $cEstiloLetra; global $vResDat;

      /*** Resolución. ***/

      $nPosX = 10;
      $nPosY = 228;
      $this->setXY($nPosX,$nPosY);
      $this->SetFont('verdana','',6);
      $this->Cell(196,3,"Para todo efecto legal ".chr(233)."sta factura es un t".chr(237)."tulo valor seg".chr(250)."n LEY 1231 del 17 de Julio de 2008. Una vez vencida causar".chr(225)." intereses moratorios conforme a la tasa m".chr(225)."xima autorizada.",0,0,'C');

      $nPosY = $nPosY+3;
      $this->setXY($nPosX,$nPosY);
      $this->SetFont('verdana','',6);
      $this->Cell(196,3,"AGENCIA DE ADUANAS ADUANAMIENTOS IMPORTACIONES Y EXPORTACIONES S.A.S. No. 2 se reserva el derecho de dominio hasta su cancelaci".chr(243)."n total.",0,0,'C');

      $nPosY = $nPosY+3;
      $this->setXY($nPosX,$nPosY);
      $this->SetFont('verdana','',6);
      $this->Cell(196,3,"Girar cheque a favor de AGENCIA DE ADUANAS ADIMPEX S.A.S. No. 2",0,0,'C');

      /*** Bloque Firma ***/
      $nPosY = $nPosY+5;
      $this->Rect($nPosX,$nPosY,196,25);
      $this->Line($nPosX+98,$nPosY,$nPosX+98,$nPosY+25);

      /*** Firma y sello Para Bloque Izquiedo ***/
      $this->Line($nPosX+22,$nPosY+20,$nPosX+78,$nPosY+20);
      $this->setXY($nPosX+22,$nPosY+21);
      $this->SetFont('verdanab','',7);
      $this->Cell(58,3,"FIRMA Y SELLO",0,0,'C');

      /*** Firma y sello Para Bloque Derecho ***/
      $this->setXY($nPosX+100,$nPosY+3);
      $this->SetFont('verdanab','',7);
      $this->Cell(20,3,"FECHA:",0,0,'L');

      $this->setXY($nPosX+100,$nPosY+10);
      $this->SetFont('verdanab','',7);
      $this->Cell(20,3,"NOMBRE:",0,0,'L');

      $this->setXY($nPosX+100,$nPosY+17);
      $this->SetFont('verdanab','',7);
      $this->Cell(20,3,"C.C.:",0,0,'L');

      $this->Line($nPosX+114,$nPosY+7,$nPosX+149,$nPosY+7);
      $this->Line($nPosX+114,$nPosY+14,$nPosX+149,$nPosY+14);
      $this->Line($nPosX+114,$nPosY+21,$nPosX+149,$nPosY+21);
      $this->Line($nPosX+150,$nPosY+21,$nPosX+194,$nPosY+21);

      $this->setXY($nPosX+150,$nPosY+22);
      $this->SetFont('verdanab','',7);
      $this->Cell(20,3,"FIRMA Y SELLO AUTORIZADOS",0,0,'L');

      $nPosY = $nPosY+28;
      $this->setXY($nPosX,$nPosY);
      $this->SetFont('verdana','',7);
      $this->Cell(196,3,$cNomCopia,0,0,'C');

      ## Linea Lateral de Open##
      $this->SetFont('verdana','',8);
      $cResolucion = "IMPRESO POR OPENTECNOLOGIA S.A. Nit: 830.135.010-5";
      $this->RotatedText(9,200,$cResolucion,90);//14,220
      $this->Rotate(0);
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

	$cNomCopia = "CLIENTE";
  $oPdf = new PDF('P','mm','Letter');  //Error al invocar la clase
  $oPdf->AddFont($cEstiloLetra,'','verdana.php');
  $oPdf->AddFont($cEstiloLetrab,'','verdanab.php');
	// $oPdf->AddFont($cEstiloLetra,'','arial.php');
  $oPdf->AliasNbPages();
  $oPdf->SetMargins(0,0,0);
  $oPdf->SetAutoPageBreak(0,22);

	# Numeros de Copias
	for($y=1; $y<=1; $y++){
		$oPdf->AddPage();
		##Codigo Para impresion de Copias de Factura ##
		switch($y){
			case 1:
				// $cNomCopia = "CLIENTE";
				break;
			case 2:
				// $cNomCopia = "CONTABILIDAD";
				break;
			case 3:
				// $cNomCopia =  "CARTERA";
				break;
		}
		##Codigo Para impresion de Copias de Factura ##
		$nContPage = 1;
    $nPosy = 73;
    $nPosx = 12;
    $posFin = 126;
    $nb = 1;
    $nPyy = $nPosy;
    $nNextPage = 0;
    $oPdf->setXY($nPosx,$nPyy+3);


    ##Busco Valor de RET.IVA ##
    $nTotIva = 0;
    for ($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
      $nTotIva +=$_POST['nVlrIva_IPA'.($i+1)];
    }
    ##Fin Busco Valor de RET.IVA ##

    ##Busco Valor de RET.ICA ##
    $nTotIca = 0;
    for ($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
      $nTotIca +=$_POST['nVlrIca_IPA'.($i+1)];
    }
    ##Fin Busco Valor de RET.ICA ##

    ##Busco Valor de RET.FTE ##
    $nTotRfte = 0;
    for ($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
      $nTotRfte +=$_POST['nVlrFte_IPA'.($i+1)];
    }

    $nSubTotPcc = 0;
    $nSubTotPccIva = 0;

    $nPosIP1 = 0; $nPosIP2 = 0; $nImprimirIP1 = 0; $nImprimirIP2 = 0;
    $nPosIT1 = 0; $nImprimirIT1 = 0; $nPosIT2 = 0; $nImprimirIT2 = 0;
    $nPos4xM = 0; $nImprimir4xM = 0; $nNextPage = 0;
    $nImpSubIngPro = 0; $nImpSubIngProListo = 0;
    $nImpIVA = 0; $nImpIVAListo = 0; $nImpSubTot = 0; $nImpSubTotListo = 0;
    $nImpIVA    = 0; $nImpIVAListo    = 0; $nImpRetFue = 0; $nImpRetFueListo = 0;
    $nImpRetIca = 0; $nImpRetIcaListo = 0; $nImpRetIva = 0; $nImpRetIvaListo = 0;
    $nImpTot    = 0; $nImpTotListo    = 0; $nImpTotPT  = 0; $nImpTotPTListo  = 0;
    $nPagNue = false;
    do{
      $nPosy = 73;
      $nPosx = 12;
      $posFin = 126;
      $nb = 1;
      $nPyy = $nPosy;
      $nNextPage = 0;
      ##Imprimo Detalle de Pagos a Terceros e Ingresos Propios ##
  	  if($_POST['nSecuencia_IPA'] > 0 || count($mIngPro) > 0) { //Valido si la Secuencia de la Grilla de Ip viene mayor a cero para imprimir bloque de INGRESOS PROPIOS
        if($nPagNue == true){//Validacion para siguiente pagina si se excede espacio de impresion
          $oPdf->AddPage();
          $nb++;
          $nPosy = 73;
          $nPosx = 12;
          $nPyy = $nPosy;
          $oPdf->SetFont($cEstiloLetra,'',8);
          $oPdf->setXY($nPosx,$nPosy);
        }
        ### Imprimo Titulo
        $oPdf->SetFont($cEstiloLetra,'U',8);
        $oPdf->setXY($nPosx,$nPyy);

        ## Imprimo Ingresos Propios##
        $nPyy = $oPdf->GetY();
        $oPdf->setXY($nPosx,$nPyy+3);
        $oPdf->SetWidths(array(126,32,35));
        $oPdf->SetAligns(array("L","R","R"));

        // OJO: hago dos veces el mismo for para separar los ingresos GRAVADOS y luego los NO GRAVADOS
        for($k=$nPosIP1;$k<(count($mIngPro));$k++) {
          $nImprimirIP1 = 0;
          if($nPyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
            $nImprimirIP1 = 1;
            $nPagNue = true;
            $nNextPage = 1;
          }
          if($nImprimirIP1 == 0){
            $nPyy = $oPdf->GetY();
            $nPosIP1 = $k+1;
            if( $mIngPro[$k]['comvlr01'] != 0 ) {
              $nSubToIP += $mIngPro[$k]['comvlrxx'];
              $nSubToIPIva += $mIngPro[$k]['comvlr01'];
              $oPdf->SetFont($cEstiloLetra,'',8);

              $cValor = "";
              //Mostrando cantidades por tipo de cantidad
              foreach ($mIngPro[$k]['itemcanx'] as $cKey => $cValue) {
                if($cKey == "FOB" && $cValue > 0) {
                  $cValor  = "FOB: ($".number_format($cValue,2,',','.');
                  $cValor .= ($mIngPro[$k]['itemcanx']['TRM'] > 0) ? " TRM: $".number_format($mIngPro[$k]['itemcanx']['itemcanx']['TRM'],2,',','.') : "";
                  $cValor .= ")";
                } elseif ($cKey == "CIF") {
                  $cValor = "CIF: ($".number_format($cValue,0,',','.').")";
                } elseif ($cKey == "DIM") {
                  $cValor = "DIM: (".number_format($cValue,0,',','.').")";
                } elseif ($cKey == "DAV") {
                  $cValor = "DAV: (".number_format($cValue,0,',','.').")";
                } elseif ($cKey == "VUCE") {
                  $cValor = "VUCE: (".number_format($cValue,0,',','.').")";
                } elseif ($cKey == "HORAS") {
                  $cValor = "HORAS: (".number_format($cValue,0,',','.').")";
                } elseif ($cKey == "PIEZAS") {
                  $cValor = "PIEZAS: (".number_format($cValue,0,',','.').")";
                } elseif ($cKey == "DEX") {
                  $cValor = "DEX: (".number_format($cValue,0,',','.').")";
                } elseif ($cKey == "SERIAL") {
                  $cValor = "SERIAL: (".number_format($cValue,0,',','.').")";
                } elseif ($cKey == "CANTIDAD") {
                  $cValor = "CANT.: (".number_format($cValue,0,',','.').")";
                }
              }

              $oPdf->setX($nPosx);
              // Si se agrupan los IP no se lleva imprimen las cantidades
              $cValor = ($mIngPro[$k]['agrupaip'] == "SI") ? "" : $cValor;
              $nValor = ((strpos(($mIngPro[$k]['comvlrxx']+0),'.') > 0) ? number_format(($mIngPro[$k]['comvlrxx']+0),2,',','.') : number_format(($mIngPro[$k]['comvlrxx']+0),0,',','.'));
              $oPdf->Row(array($mIngPro[$k]['comobsxx'].$cValor, "", $nValor));
            }
          }
        }

        for($k=$nPosIP2;$k<(count($mIngPro));$k++) {
          $nImprimirIP2 = 0;
          $nPyy = $oPdf->GetY();
          if($nPyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
            $nImprimirIP2 = 1;
            $nPagNue = true;
            $nNextPage = 1;
          }
          if($nImprimirIP2 == 0){
            $nPyy = $oPdf->GetY();
            $nPosIP2 = $k+1;
            if( $mIngPro[$k]['comvlr01'] == 0 ) {
              $nSubToIP += $mIngPro[$k]['comvlrxx'];
              $nSubToIPIva += $mIngPro[$k]['comvlr01'];
              $oPdf->SetFont($cEstiloLetra,'',8);

              $cValor = "";
              //Mostrando cantidades por tipo de cantidad
              foreach ($mIngPro[$k]['itemcanx'] as $cKey => $cValue) {
                if($cKey == "FOB" && $cValue > 0) {
                  $cValor  = "FOB: ($".number_format($cValue,2,',','.');
                  $cValor .= ($mIngPro[$k]['itemcanx']['TRM'] > 0) ? " TRM: $".number_format($mIngPro[$k]['itemcanx']['itemcanx']['TRM'],2,',','.') : "";
                  $cValor .= ")";
                } elseif ($cKey == "CIF") {
                  $cValor = "CIF: ($".number_format($cValue,0,',','.').")";
                } elseif ($cKey == "DIM") {
                  $cValor = "DIM: (".number_format($cValue,0,',','.').")";
                } elseif ($cKey == "DAV") {
                  $cValor = "DAV: (".number_format($cValue,0,',','.').")";
                } elseif ($cKey == "VUCE") {
                  $cValor = "VUCE: (".number_format($cValue,0,',','.').")";
                } elseif ($cKey == "HORAS") {
                  $cValor = "HORAS: (".number_format($cValue,0,',','.').")";
                } elseif ($cKey == "PIEZAS") {
                  $cValor = "PIEZAS: (".number_format($cValue,0,',','.').")";
                } elseif ($cKey == "DEX") {
                  $cValor = "DEX: (".number_format($cValue,0,',','.').")";
                } elseif ($cKey == "SERIAL") {
                  $cValor = "SERIAL: (".number_format($cValue,0,',','.').")";
                } elseif ($cKey == "CANTIDAD") {
                  $cValor = "CANT.: (".number_format($cValue,0,',','.').")";
                }
              }

              $oPdf->setX($nPosx);
              // Si se agrupan los IP no se lleva imprimen las cantidades
              $cValor = ($mIngPro[$k]['agrupaip'] == "SI") ? "" : $cValor;
              $nValor = ((strpos(($mIngPro[$k]['comvlrxx']+0),'.') > 0) ? number_format(($mIngPro[$k]['comvlrxx']+0),2,',','.') : number_format(($mIngPro[$k]['comvlrxx']+0),0,',','.'));
              $oPdf->Row(array($mIngPro[$k]['comobsxx'].$cValor, "", $nValor));
            }
          }
        }

        $nImpSubIngPro = 0;
        $nPyy = $oPdf->GetY();
        if($nPyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
          $nImpSubIngPro = 1;
          $nPagNue = true;
          $nNextPage = 1;
        }

        if($nImpSubIngPro == 0 && $nImpSubIngProListo == 0){
        ## Imprimo subtotal ingresos propios
          $oPdf->SetFont($cEstiloLetrab,'',8);
          $oPdf->setX($nPosx);
          $oPdf->Row(array("SUBTOTAL INGRESOS PROPIOS","", number_format($nSubToIP,0,",",".")));
          $nImpSubIngProListo = 1;
        }

        $nImpIVA = 0;
        $nPyy = $oPdf->GetY();
        if($nPyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
          $nImpIVA = 1;
          $nPagNue = true;
          $nNextPage = 1;
        }

        if($nImpIVA == 0 && $nImpIVAListo == 0){
        ## Imprimo IVA ingresos propios
          $oPdf->SetFont($cEstiloLetra,'',8);
          $oPdf->setX($nPosx);
          $oPdf->Row(array("IVA","", number_format($nSubToIPIva,0,",",".")));
          $nImpIVAListo = 1;
        }

        $nImpSubTot = 0;
        $nPyy = $oPdf->GetY();
        if($nPyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
          $nImpSubTot = 1;
          $nPagNue = true;
          $nNextPage = 1;
        }

        if($nImpSubTot == 0 && $nImpSubTotListo == 0){
          ##Imprimo Subtotal de Ingresos Propios ##
          $oPdf->SetFont($cEstiloLetrab,'',8);
          $oPdf->setX($nPosx);
          $nValor = ((strpos(($nSubToIP+$nSubToIPIva+0),'.') > 0) ? number_format(($nSubToIP+$nSubToIPIva+0),2,',','.') : number_format(($nSubToIP+$nSubToIPIva+0),0,',','.'));
          $oPdf->Row(array("DESCUENTOS TRIBUTARIOS","SUBTOTAL", ($vCocDat['CLINRPXX'] == "SI") ? "": $nValor));
          $nImpSubTotListo = 1;
          ##Imprimo Subtotal de Ingresos Propios ##
        }

        $nImpRetFue = 0;
        $nPyy = $oPdf->GetY();
        if($nPyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
          $nImpRetFue = 1;
          $nPagNue = true;
          $nNextPage = 1;
        }

        if($nImpRetFue == 0 && $nImpRetFueListo == 0){
          ## Retención Fuentes
          $oPdf->SetFont($cEstiloLetra,'',8);
          $oPdf->setX($nPosx);
          $oPdf->Row(array("RETENCION FUENTE","", number_format($nTotRfte,0,",",".")));
          $nImpRetFueListo = 1;
        }

        $nImpRetIca = 0;
        $nPyy = $oPdf->GetY();
        if($nPyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
          $nImpRetIca = 1;
          $nPagNue = true;
          $nNextPage = 1;
        }

        ## Retención ICA
        if($nImpRetIca == 0 && $nImpRetIcaListo == 0){
          $oPdf->SetFont($cEstiloLetra,'',8);
          $oPdf->setX($nPosx);
          $oPdf->Row(array(utf8_decode("RETENCION ICA ".$cSucDes),"", number_format($nTotIca,0,",",".")));
          $nImpRetIcaListo = 1;
        }

        $nImpRetIva = 0;
        $nPyy = $oPdf->GetY();
        if($nPyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
          $nImpRetIva = 1;
          $nPagNue = true;
          $nNextPage = 1;
        }

        if($nImpRetIva == 0 && $nImpRetIvaListo == 0){
          ## Retención IVA
          $oPdf->SetFont($cEstiloLetra,'',8);
          $oPdf->setX($nPosx);
          $oPdf->Row(array("RETENCION IVA","", number_format($nTotIva,0,",",".")));
          $nImpRetIvaListo = 1;
        }

        $nImpTot = 0;
        $nPyy = $oPdf->GetY();
        if($nPyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
          $nImpTot = 1;
          $nPagNue = true;
          $nNextPage = 1;
        }

        if($nImpTot == 0 && $nImpTotListo == 0){
          $oPdf->SetFont($cEstiloLetrab,'',8);
          $oPdf->setX($nPosx);
          $nValor = ( ($nSubToIP+$nSubToIPIva) - $nTotRfte - $nTotIca - $nTotIva);
          $oPdf->Row(array("TOTAL INGRESOS PROPIOS","TOTAL", ($vCocDat['CLINRPXX'] == "SI") ? "": number_format($nValor,0,",",".")));
          $nImpTotListo = 1;
          ##Imprimo Subtotal de Ingresos Propios ##
        }
      }//if($nBandIP == 1){//Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS

      $nPosy = 140;
      $nPosx = 12;
      $posFin = 200;
      $nb = 1;
      $nPyy = $nPosy;

  	  if(count($mIngTer) > 0 ){//Si la matriz de Pcc tiene registros
        //$oPdf->setXY(40,$nPosy);

        ### Imprimo Titulo
        $oPdf->SetFont($cEstiloLetra,'',8);
        $oPdf->setXY($nPosx,$nPyy);

        ## Imprimo Pagos a Terceros ##
        $oPdf->setXY($nPosx,$nPyy+3);
        $oPdf->SetWidths(array(116,42,35));
        $oPdf->SetAligns(array("L","R","R"));
        $nPyy += 3;

  			for($i=$nPosIT1;$i<count($mIngTer);$i++){
  			  $nImprimirIT1 = 0;
  			  if($mIngTer[$i]['cTipo'] != "IMPUESTO_FINANCIERO"){
            $nPyy = $oPdf->GetY();
  			    if($nPyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
              $nImprimirIT1 = 1;
              $nPagNue = true;
              $nNextPage = 1;
            }

            if($nImprimirIT1 == 0){
              $nPyy = $oPdf->GetY();
              $nPosIT1 = $i+1;
      	  		$nSubTotPcc += $mIngTer[$i]['nComVlr'];

      	  		if($mIngTer[$i]['cComCsc3'] != ""){
      	        //$cComObsv = str_replace($mIngTer[$i]['cTerNom'], '', $mIngTer[$i]['cComObs'])." ".$mIngTer[$i]['cTerNom'].' FV '.$mIngTer[$i]['cComCsc3'];
      	        $cComObsv = $mIngTer[$i]['cComObs']." ".$mIngTer[$i]['cTerNom']." ".$mIngTer[$i]['cComCsc3'];
      	  		}else{
      	  		  $cComObsv = $mIngTer[$i]['cComObs'];
      	  		}

      	  		$oPdf->SetFont($cEstiloLetra,'',8);
      				$oPdf->setX($nPosx);
              $nValor = ((strpos(($mIngTer[$i]['nComVlr']+0),'.') > 0) ? number_format(($mIngTer[$i]['nComVlr']+0),2,',','.') : number_format(($mIngTer[$i]['nComVlr']+0),0,',','.'));
      				$oPdf->Row(array( $cComObsv,  "", $nValor));
            }
          }
  	  	}//for($i=0;$i<count($mIngTer);$i++){

  	  	for($i=$nPosIT2;$i<count($mIngTer);$i++){
          if($mIngTer[$i]['cTipo'] == "IMPUESTO_FINANCIERO"){
            $nImprimirIT2 = 0;
            $nPyy = $oPdf->GetY();
            if($nPyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
              $nImprimirIT2 = 1;
              $nPagNue = true;
              $nNextPage = 1;
            }
            if($nImprimirIT2 == 0){
              $nPosIT2 = $i+1;
              $nSubTotPcc += $mIngTer[$i]['nComVlr'];
              if($mIngTer[$i]['cComCsc3'] != ""){
                //$cComObsv = str_replace($mIngTer[$i]['cTerNom'], '', $mIngTer[$i]['cComObs'])." ".$mIngTer[$i]['cTerNom'].' FV '.$mIngTer[$i]['cComCsc3'];
                $cComObsv = $mIngTer[$i]['cComObs']." ".$mIngTer[$i]['cTerNom']." ".$mIngTer[$i]['cComCsc3'];
              }else{
                $cComObsv = $mIngTer[$i]['cComObs'];
              }
              $oPdf->SetFont($cEstiloLetra,'',8);
              $oPdf->setX($nPosx);
              $nValor = ((strpos(($mIngTer[$i]['nComVlr']+0),'.') > 0) ? number_format(($mIngTer[$i]['nComVlr']+0),2,',','.') : number_format(($mIngTer[$i]['nComVlr']+0),0,',','.'));
              $oPdf->Row(array( $cComObsv, "", $nValor));
            }
          }
        }//for($i=0;$i<count($mIngTer);$i++){

        $nImpTotPT = 0;
        $nPyy = $oPdf->GetY();
        if($nPyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
          $nImpTotPT = 1;
          $nPagNue = true;
          $nNextPage = 1;
        }

        if($nImpTotPT == 0 && $nImpTotPTListo == 0){
    	  	##Imprimo Subtotal de Pagos a Terceros ##
    	  	$oPdf->SetFont($cEstiloLetrab,'',8);
    	  	$oPdf->setX($nPosx);
          $nValor = ((strpos(($nSubTotPcc+0),'.') > 0) ? number_format(($nSubTotPcc+0),2,',','.') : number_format(($nSubTotPcc+0),0,',','.'));
          $oPdf->Row(array("TOTAL PAGOS A TERCEROS", "TOTAL", $nValor));

    			$nPyy += 3;
          $nImpTotPTListo = 1;
    	  	##Fin Imprimo Subtotal de Pagos a Terceros ##
        }
  	  }//if(count($mIngTer) > 0 || $nBandPcc == 1){//Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
  	  ##Fin Imprimo Pagos a Terceros ##
    }while($nNextPage == 1);

    $nPyy = $oPdf->GetY();

	  if(count($mIngTer) > 0 || $nBandPcc == 1){
	  	$nPyy += 4;
	  }

	  ##Fin Imprimo Detalle de Pagos a Terceros e Ingresos Propios ##

	  ##Sumo Totales de Pagos a Terceros e Ingresos Propios para impresion de subtotal de factura ##
	  $nSubToFac = $nSubTotPcc + $nSubToIP;
	  ##Fin Sumo Totales de Pagos a Terceros e Ingresos Propios para impresion de subtotal de factura ##

	  ##Sumo Totales de iva de los Pagos a Terceros e Ingresos Propios para impresion de subtotal de factura ##
	  $nSubToFacIva += $nSubToIPIva;

    ## Total Servicio + IVA
    $nTotalFacIva = $nSubToFac+$nSubToFacIva;

    ##Bloque que acumula retenciones por valor de porcentaje##
    $mRetFte = array();
    $mRetIca = array();
    $mRetIva = array();
    $mReteCre = array();
    for ($i=0;$i<$_POST['nSecuencia_IPA'];$i++) {
      if ($_POST['nPorFte_IPA'.($i+1)] > 0) {
        $nSwitch_Encontre_Porcentaje = 0;
        for ($j=0;$j<count($mRetFte);$j++) {
          if ($_POST['nPorFte_IPA'.($i+1)] == $mRetFte[$j]['pucretxx']) {
            $nSwitch_Encontre_Porcentaje = 1;
            $mRetFte[$j]['comvlrxx']  += $_POST['nVlrFte_IPA'.($i+1)];
            $mRetFte[$j]['basexxxx']  += ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
          }
        }
        if ($nSwitch_Encontre_Porcentaje == 0) { // No lo encontro en la matriz para pintar en la factura
          $nInd_mRetFte = count($mRetFte);
          $mRetFte[$nInd_mRetFte]['tipretxx'] = "Retefuente Servicios";
          $mRetFte[$nInd_mRetFte]['pucretxx'] = $_POST['nPorFte_IPA'.($i+1)];
          $mRetFte[$nInd_mRetFte]['comvlrxx'] = $_POST['nVlrFte_IPA'.($i+1)];
          $mRetFte[$nInd_mRetFte]['basexxxx'] = ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
        }
      }

      if ($_POST['nPorIca_IPA'.($i+1)] > 0) {
        $nSwitch_Encontre_Porcentaje = 0;
        for ($j=0;$j<count($mRetIca);$j++) {
          if ($_POST['nPorIca_IPA'.($i+1)] == $mRetIca[$j]['pucretxx']) {
            $nSwitch_Encontre_Porcentaje = 1;
            $mRetIca[$j]['comvlrxx']  += $_POST['nVlrIca_IPA'.($i+1)];
            $mRetIca[$j]['basexxxx']  += ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
          }
        }
        if ($nSwitch_Encontre_Porcentaje == 0) { // No lo encontro en la matriz para pintar en la factura
          $nInd_mRetIca = count($mRetIca);
          $mRetIca[$nInd_mRetIca]['tipretxx'] = "Reteica";
          $mRetIca[$nInd_mRetIca]['pucretxx'] = $_POST['nPorIca_IPA'.($i+1)];
          $mRetIca[$nInd_mRetIca]['comvlrxx'] = $_POST['nVlrIca_IPA'.($i+1)];
          $mRetIca[$nInd_mRetIca]['basexxxx'] = ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
        }
      }

      if ($_POST['nPorIva_IPA'.($i+1)] > 0) {
        $nSwitch_Encontre_Porcentaje = 0;
        for ($j=0;$j<count($mRetIva);$j++) {
          if ($_POST['nPorIva_IPA'.($i+1)] == $mRetIva[$j]['pucretxx']) {
            $nSwitch_Encontre_Porcentaje = 1;
            $mRetIva[$j]['comvlrxx']  += $_POST['nVlrIva_IPA'.($i+1)];
            $mRetIva[$j]['basexxxx']  += ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
          }
        }
        if ($nSwitch_Encontre_Porcentaje == 0) { // No lo encontro en la matriz para pintar en la factura
          $nInd_mRetIva = count($mRetIva);
          $mRetIva[$nInd_mRetIva]['tipretxx'] = "Reteiva";
          $mRetIva[$nInd_mRetIva]['pucretxx'] = $_POST['nPorIva_IPA'.($i+1)];
          $mRetIva[$nInd_mRetIva]['comvlrxx'] = $_POST['nVlrIva_IPA'.($i+1)];
          $mRetIva[$nInd_mRetIva]['basexxxx'] = ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
        }
      }
    }

    $mRenciones = array();
    $mRenciones = array_merge($mRetFte,    $mRetIca);
    $mRenciones = array_merge($mRenciones, $mRetIva);
    $mRenciones = array_merge($mRenciones, $mReteCre);

		## arrray ordenado por porcentaje y por tipo retencion.
		$mRencionesPorc = array();
		$nTotalRetenciones = 0;
		$mTipoRenciones = array();
		//$nTotalTipoRetenciones = 0;

		foreach ($mRenciones as $mRencion) {
			/* agrupo por porcentaje Y POR REFTE*/
			if ($mRencion['tipretxx'] == 'Retefuente Servicios') {
				if (array_key_exists($mRencion['pucretxx'],$mRencionesPorc )) {
					$mRencionesPorc[$mRencion['pucretxx']]['comvlrxx'] += $mRencion['comvlrxx'];
				} else {
					$mRencionesPorc[$mRencion['pucretxx']]['comvlrxx'] = $mRencion['comvlrxx'];
				}
				$nTotalRetenciones += $mRencion['comvlrxx'];
			}
			/* fin de agrupacion por porcentaje Y POR REFTE*/

			/* agrupo por retencion*/
			if ($mRencion['tipretxx'] != '') {
				$cKey = str_replace(" ","-",$mRencion['tipretxx']);
				if (array_key_exists($cKey,$mTipoRenciones )) {
					$mTipoRenciones[$cKey]['comvlrxx'] += $mRencion['comvlrxx'];
				} else {
					$mTipoRenciones[$cKey]['comvlrxx'] = $mRencion['comvlrxx'];
				}
				//$nTotalTipoRetenciones += $mRencion['comvlrxx'];
			}
			/* fin de agrupacion por porcentaje*/
		}

		//ordeno el array
		ksort($mRencionesPorc);
		ksort($mTipoRenciones);
    ##Fin Bloque que acumula retenciones por valor de porcentajey por tipo retencion##

	 	##Fin Busco Valor de RET.FTE ##
	 	if ($_POST['nIPASal'] > 0) {
	 	  $cSaldoCargo = "X";
	 	} else {
	 	  $cSaldoFavor = "X";
	 	}

    $nPosy = 208;
    $oPdf->SetFont($cEstiloLetrab,'',8);
    $oPdf->setXY($nPosx,$nPosy);
    $oPdf->SetWidths(array(128,30,35));
    $oPdf->SetAligns(array("L","R","R"));

    # Total Factura
    $oPdf->setXY($nPosx,$nPosy);
    $nTotal = $nTotalFacIva - $nTotIva - $nTotIca - $nTotRfte;
    $oPdf->Row(array("","", ((strpos(($nTotal+0),'.') > 0) ? number_format(($nTotal+0),0,',','.') : number_format(($nTotal+0),0,',','.'))));

    # Anticipo
    $nPosy += 6.5;
    $oPdf->setXY($nPosx,$nPosy);
    $oPdf->SetWidths(array(60,40,15,40,38));
    $oPdf->SetAligns(array("L","L","L","L","R"));
    $nValor = ((strpos((abs($_POST['nIPAAnt'])+0),'.') > 0) ? number_format((abs($_POST['nIPAAnt'])+0),2,',','.') : number_format((abs($_POST['nIPAAnt'])+0),0,',','.'));
    $oPdf->Row(array("",(($vRecAnt['comcsc3x'] != "")?substr($vRecAnt['comcsc3x'], 3, strlen($vRecAnt['comcsc3x'])):""), "" , $vRecAnt['comfecxx'], $nValor ));

    # Total Factura menos Anticipo
    $nPosy += 6.5;
    $oPdf->setXY($nPosx,$nPosy);
    $oPdf->SetWidths(array(28,28.5,25,111.5));
    $oPdf->SetAligns(array("L","R","R","R"));
    $nValor = ((strpos((abs($_POST['nIPASal'])+0),'.') > 0) ? number_format((abs($_POST['nIPASal'])+0),2,',','.') : number_format((abs($_POST['nIPASal'])+0),0,',','.'));
    $oPdf->Row(array("",$cSaldoCargo,$cSaldoFavor, $nValor));

  }
  $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
	$oPdf->Output($cFile);

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

  //$oPdf->Output();
?>

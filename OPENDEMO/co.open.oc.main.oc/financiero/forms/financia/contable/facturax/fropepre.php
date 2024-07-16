<?php
  /**
	 * Imprime Factura de Venta OPENTECNOLOGIA.
	 * --- Descripcion: Permite Imprimir Factura de Venta OPENTECNOLOGIA por Viste Previa.
	 * @author Juan Jose Trujillo <juan.trujillo@open-eb.co>
	 * @version 001
	 */
  include("../../../../libs/php/utility.php");
  include("../../../../libs/php/utiliqdo.php");

  define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
  require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');

  $pdf = new FPDF('P','mm','Letter');
  $pdf->AddFont('verdana','','verdana.php');
  $pdf->AddFont('verdanab','','verdanab.php');
  $pdf->SetFont('verdana','',8);
  $pdf->SetMargins(0,0,0);
  $pdf->SetAutoPageBreak(0,0);

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

  for ($i=0;$i<$_POST['nSecuencia_Dos'];$i++) {
	  if($i == 0) {
      $cDoiId   = $_POST['cDosNro_DOS'.($i+1)];
      $cComSeq  = $_POST['cDosSuf_DOS'.($i+1)];
      $cSucId   = $_POST['cSucId_DOS' .($i+1)];
    }
  }//for ($i=0;$i<$_POST['nSecuencia_Dos'];$i++) {

  $dFecEmi = $_POST['dRegFCre'];
  $dFecVen = date("Y-m-d",mktime(0,0,0,substr($_POST['dRegFCre'],5,2),substr($_POST['dRegFCre'],8,2)+$_POST['cTerPla'],substr($_POST['dRegFCre'],0,4)));

	//TRAIGO DATOS DE LA sys00121
	$qDceDat  = "SELECT * ";
	$qDceDat .= "FROM $cAlfa.sys00121 ";
	$qDceDat .= "WHERE ";
	$qDceDat .= "docidxxx = \"$cDoiId\" AND ";
	$qDceDat .= "docsufxx = \"$cComSeq\" AND ";
	$qDceDat .= "sucidxxx = \"$cSucId\" ";
  $xDceDat  = f_MySql("SELECT","",$qDceDat,$xConexion01,"");
	if (mysql_num_rows($xDceDat) > 0) {
	  $vDceDat = mysql_fetch_array($xDceDat);
  }
  
  ##Switch para traer Datos de Do Dependiendo del Tipo de Operacion ##
  switch ($vDceDat['doctipxx']){
    case "IMPORTACION":
      ##Traigo Datos de la SIAI0200 DATOS DEL DO ##
      $qDoiDat  = "SELECT * ";
      $qDoiDat .= "FROM $cAlfa.SIAI0200 ";
      $qDoiDat .= "WHERE $cAlfa.SIAI0200.DOIIDXXX = \"$cDoiId\" AND ";
      $qDoiDat .= "$cAlfa.SIAI0200.DOISFIDX = \"$cComSeq\" AND ";
      $qDoiDat .= "$cAlfa.SIAI0200.ADMIDXXX = \"$cSucId\" ";
      $xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qDoiDat."~".mysql_num_rows($xDoiDat));
      if (mysql_num_rows($xDoiDat) > 0) {
        $vDoiDat  = mysql_fetch_array($xDoiDat);
      }
      ##Fin Traigo Datos de la SIAI0200 DATOS DEL DO ##
      
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
      $qDecDat .= "$cAlfa.SIAI0206.DOIIDXXX = \"$cDoiId\"  AND ";
      $qDecDat .= "$cAlfa.SIAI0206.DOISFIDX = \"$cComSeq\" AND ";
      $qDecDat .= "$cAlfa.SIAI0206.ADMIDXXX = \"$cSucId\" ";
      $qDecDat .= "GROUP BY $cAlfa.SIAI0206.DOIIDXXX ";
      $xDecDat  = f_MySql("SELECT","",$qDecDat,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qDecDat."~".mysql_num_rows($xDecDat));
      if (mysql_num_rows($xDecDat) > 0) {
        $vDecDat  = mysql_fetch_array($xDecDat);
      }

      ##Cargo Variables para Impresion de Datos de Do ##
      $cPedido = $vDoiDat['DOIPEDXX'];
      $cAduana = $vAdmIng['ODIDESXX'];
      $cDocTra = $vDoiDat['DGEDTXXX'];
      $nValAdu = $vDecDat['LIMCIFXX'];
      ##Fin Cargo Variables para Impresion de Datos de Do ##
    break;
    case "EXPORTACION":
      ## Consulto Datos de Do en Exportaciones tabla siae0199 ##
      $qDexDat  = "SELECT * ";
      $qDexDat .= "FROM $cAlfa.siae0199 ";
      $qDexDat .= "WHERE ";
      $qDexDat .= "$cAlfa.siae0199.dexidxxx = \"$cDoiId\" AND ";
      $qDexDat .= "$cAlfa.siae0199.admidxxx = \"$cSucId\" ";
      $xDexDat  = f_MySql("SELECT","",$qDexDat,$xConexion01,"");
      //f_Mensaje(__FILE__,__LINE__,$qDexDat);
      if (mysql_num_rows($xDexDat) > 0) {
        $vDexDat = mysql_fetch_array($xDexDat);
      }
      ## Fin Consulto Datos de Do en Exportaciones tabla siae0199 ##
      
      ##Trayendo aduana de salida##
      $qAduSal  = "SELECT odiid2xx ";
      $qAduSal .= "FROM $cAlfa.siae0200 ";
      $qAduSal .= "WHERE ";
      $qAduSal .= "$cAlfa.siae0200.dexidxxx = \"$cDoiId\" AND ";
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

      ##Traigo Valor fob, peso neto, peso bruto, bultos de la tabla de Items de Exportaciones siae0201 ##
      $qIteDat  = "SELECT ";
      $qIteDat .= "SUM($cAlfa.siae0201.itefobxx) AS itefobxx, ";
      $qIteDat .= "SUM($cAlfa.siae0201.itepbrxx) AS itepbrxx, ";
      $qIteDat .= "SUM($cAlfa.siae0201.itepnexx) AS itepnexx, ";
      $qIteDat .= "SUM($cAlfa.siae0201.itebulxx) AS itebulxx ";
      $qIteDat .= "FROM $cAlfa.siae0201 ";
      $qIteDat .= "WHERE ";
      $qIteDat .= "$cAlfa.siae0201.dexidxxx =\"$cDoiId\" AND ";
      $qIteDat .= "$cAlfa.siae0201.admidxxx = \"$cSucId\" ";
      $xIteDat  = f_MySql("SELECT","",$qIteDat,$xConexion01,"");
      if (mysql_num_rows($xIteDat) > 0) {
        $vIteDat = mysql_fetch_array($xIteDat);
      }

      ##Cargo Variables para Impresion de Datos de Do ##
      $cPedido = $vDexDat['dexpedxx'];
      $cAduana = $vDesAdu['ODIDESXX'];
      $cDocTra = $vDexDat['dexdtrxx'];
      $nValAdu = ($vIteDat['itefobxx']*$vDceDat['doctrmxx']);
      ##Fin Cargo Variables para Impresion de Datos de Do ##
    break;
    case "TRANSITO":
      ## Traigo Datos de la SIAI0200 ##
      $qDoiDat  = "SELECT * ";
      $qDoiDat .= "FROM $cAlfa.SIAI0200 ";
      $qDoiDat .= "WHERE ";
      $qDoiDat .= "DOIIDXXX = \"$cDoiId\" AND ";
      $qDoiDat .= "DOISFIDX = \"$cComSeq\" AND ";
      $qDoiDat .= "ADMIDXXX = \"$cSucId\" ";
      $xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
      //f_Mensaje(__FILE__,__LINE__,$qDoiDat."~".mysql_num_rows($xDoiDat));
      if (mysql_num_rows($xDoiDat) > 0) {
        $vDoiDat = mysql_fetch_array($xDoiDat);
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
      }else{
        ## Consulto Datos de Do en Exportaciones tabla siae0199 ##
        $qDoiDat  = "SELECT *, ";
        $qDoiDat .= "dexdtrxx AS DGEDTXXX, ";
        $qDoiDat .= "dexpedxx AS DOIPEDXX, ";
        $qDoiDat .= "vennomxx AS VENNOMXX, ";
        $qDoiDat .= "ordidmax AS ORDIDMAX  ";
        $qDoiDat .= "FROM $cAlfa.siae0199 ";
        $qDoiDat .= "WHERE ";
        $qDoiDat .= "$cAlfa.siae0199.dexidxxx = \"$cDoiId\" AND ";
        $qDoiDat .= "$cAlfa.siae0199.admidxxx = \"$cSucId\" ";
        $xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
        //f_Mensaje(__FILE__,__LINE__,$qDoiDat);
        if (mysql_num_rows($xDoiDat) > 0) {
          $vDoiDat = mysql_fetch_array($xDoiDat);
        }

        ##Trayendo aduana de salida##
        $qAduSal  = "SELECT odiid2xx ";
        $qAduSal .= "FROM $cAlfa.siae0200 ";
        $qAduSal .= "WHERE ";
        $qAduSal .= "$cAlfa.siae0200.dexidxxx = \"$cDoiId\" AND ";
        $qAduSal .= "$cAlfa.siae0200.admidxxx = \"$cSucId\" AND ";
        $qAduSal .= "$cAlfa.siae0200.odiid2xx != \"\" LIMIT 0,1 ";
        $xAduSal  = f_MySql("SELECT","",$qAduSal,$xConexion01,"");
        $vAduSal  = mysql_fetch_array($xAduSal);
        if ($vAduSal['odiid2xx'] != "") {
          ##Tayendo descripcion Aduana de salida
          $qAdmIng  = "SELECT ODIDESXX ";
          $qAdmIng .= "FROM $cAlfa.SIAI0103 ";
          $qAdmIng .= "WHERE ";
          $qAdmIng .= "ODIIDXXX = \"{$vAduSal['odiid2xx']}\" ";
          $qAdmIng .= "LIMIT 0,1 ";
          $xAdmIng  = f_MySql("SELECT","",$qAdmIng,$xConexion01,"");
          $vAdmIng  = mysql_fetch_array($xAdmIng);
        }
      }
      ## Fin Consulta a la tabla de Do's ##

      ## Consulto en la Tabla de Control DTA ##
      $qDtaDat  = "SELECT *, ";
      $qDtaDat .= "FROM $cAlfa.dta00200 ";
      $qDtaDat .= "WHERE ";
      $qDtaDat .= "$cAlfa.dta00200.doiidxxx = \"$cDoiId\" AND ";
      $qDtaDat .= "$cAlfa.dta00200.admidxxx = \"$cSucId\" ";
      $xDtaDat  = f_MySql("SELECT","",$qDtaDat,$xConexion01,"");
      if (mysql_num_rows($xDtaDat) > 0) {
        $vDtaDat = mysql_fetch_array($xDtaDat);
      }
      
      ##Cargo Variables para Impresion de Datos de Do ##
      $cPedido = $vDoiDat['DOIPEDXX'];
      $cAduana = $vAdmIng['ODIDESXX'];
      $cDocTra = $vDoiDat['DGEDTXXX'];
      $nValAdu = $vDtaDat['dtafobxx'];
      ##Fin Cargo Variables para Impresion de Datos de Do ##
    break;
  }//switch (){

  ##Consulto en la SIAI0150 Datos del Facturado A: ##
	$qCliFac  = "SELECT ";
	$qCliFac .= "$cAlfa.SIAI0150.CLIIDXXX,";
  $qCliFac .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,\" SIN NOMBRE\") AS CLINOMXX, ";
	$qCliFac .= "IF($cAlfa.SIAI0150.CLIDIRXX != \"\",$cAlfa.SIAI0150.CLIDIRXX,\"SIN DIRECCION\") AS CLIDIRXX,";
	$qCliFac .= "IF($cAlfa.SIAI0150.CLITELXX != \"\",$cAlfa.SIAI0150.CLITELXX,\"SIN TELEFONO\") AS CLITELXX,";
	$qCliFac .= "IF($cAlfa.SIAI0150.PAIIDXXX != \"\",$cAlfa.SIAI0150.PAIIDXXX,\"\") AS PAIIDXXX,";
	$qCliFac .= "IF($cAlfa.SIAI0150.DEPIDXXX != \"\",$cAlfa.SIAI0150.DEPIDXXX,\"\") AS DEPIDXXX,";
	$qCliFac .= "IF($cAlfa.SIAI0150.CIUIDXXX != \"\",$cAlfa.SIAI0150.CIUIDXXX,\"\") AS CIUIDXXX ";
	$qCliFac .= "FROM $cAlfa.SIAI0150 ";
	$qCliFac .= "WHERE ";
	$qCliFac .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$_POST['cTerIdInt']}\" AND ";
	$qCliFac .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
	$xCliFac  = f_MySql("SELECT","",$qCliFac,$xConexion01,"");
	// f_Mensaje(__FILE__,__LINE__,$qCliDat."~".mysql_num_rows($xCliFac));
	if (mysql_num_rows($xCliFac) > 0) {
		$vCliFac = mysql_fetch_array($xCliFac);
	}
  ##Consulto en la SIAI0150 Datos del Facturado A: ##

  ##Traigo Pais del Cliente del Facturado ##
	$qPaiCfa  = "SELECT PAIDESXX ";
	$qPaiCfa .= "FROM $cAlfa.SIAI0052 ";
	$qPaiCfa .= "WHERE ";
	$qPaiCfa .= "PAIIDXXX = \"{$vCliFac['PAIIDXXX']}\" AND ";
	$qPaiCfa .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
	$xPaiCfa  = f_MySql("SELECT","",$qPaiCfa,$xConexion01,"");
	// f_Mensaje(__FILE__,__LINE__,$qPaiCfa."~".mysql_num_rows($xPaiCfa));
	if (mysql_num_rows($xPaiCfa) > 0) {
		$vPaiCfa = mysql_fetch_array($xPaiCfa);
	}

	##Traigo Departamento del Cliente del Facturado ##
	$qDepCfa  = "SELECT DEPDESXX ";
	$qDepCfa .= "FROM $cAlfa.SIAI0054 ";
	$qDepCfa .= "WHERE ";
	$qDepCfa .= "PAIIDXXX = \"{$vCliFac['PAIIDXXX']}\" AND ";
  $qDepCfa .= "DEPIDXXX = \"{$vCliFac['DEPIDXXX']}\" AND ";
	$qDepCfa .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
	$xDepCfa  = f_MySql("SELECT","",$qDepCfa,$xConexion01,"");
	// f_Mensaje(__FILE__,__LINE__,$qDepCfa."~".mysql_num_rows($xDepCfa));
	if (mysql_num_rows($xDepCfa) > 0) {
		$vDepCfa = mysql_fetch_array($xDepCfa);
  }

  ##Traigo Ciudad del Facturado A ##
  $qCiuCfa  = "SELECT CIUDESXX ";
  $qCiuCfa .= "FROM $cAlfa.SIAI0055 ";
  $qCiuCfa .= "WHERE ";
  $qCiuCfa .= "$cAlfa.SIAI0055.PAIIDXXX = \"{$vCliFac['PAIIDXXX']}\" AND ";
  $qCiuCfa .= "$cAlfa.SIAI0055.DEPIDXXX = \"{$vCliFac['DEPIDXXX']}\" AND ";
  $qCiuCfa .= "$cAlfa.SIAI0055.CIUIDXXX = \"{$vCliFac['CIUIDXXX']}\" AND ";
  $qCiuCfa .= "$cAlfa.SIAI0055.REGESTXX = \"ACTIVO\" ";
  //f_Mensaje(__FILE__,__LINE__,$qCiuCfa);
  $qCiuCfa  = f_MySql("SELECT","",$qCiuCfa,$xConexion01,"");
  if (mysql_num_rows($qCiuCfa) > 0) {
    $vCiuCfa = mysql_fetch_array($qCiuCfa);
  }
  ##Fin Traigo Ciudad del Facturado A ##

  ##Traigo sucursal de facturación##
	$cSucDesx  = "";
  $qFpar117  = "SELECT sucidxxx, ccoidxxx ";
  $qFpar117 .= "FROM $cAlfa.fpar0117 ";
  $qFpar117 .= "WHERE ";
  $qFpar117 .= "comidxxx = \"{$cComId}\" AND ";
  $qFpar117 .= "comcodxx = \"{$cComCod}\" LIMIT 0,1";
  $xFpar117  = f_MySql("SELECT","",$qFpar117,$xConexion01,"");
  //f_Mensaje(__FILE__,__LINE__,$qFpar117." ~ ".mysql_num_rows($xFpar117));
  if (mysql_num_rows($xFpar117) > 0) {
    $vFpar117 = mysql_fetch_array($xFpar117);

    $qFpar008  = "SELECT sucdesxx ";
    $qFpar008 .= "FROM $cAlfa.fpar0008 ";
    $qFpar008 .= "WHERE ";
    $qFpar008 .= "sucidxxx = \"{$vFpar117['sucidxxx']}\" AND ";
    $qFpar008 .= "ccoidxxx = \"{$vFpar117['ccoidxxx']}\" LIMIT 0,1";
    $xFpar008 = f_MySql("SELECT","",$qFpar008,$xConexion01,"");
    $vFpar008 = mysql_fetch_array($xFpar008);
    $cSucDesx = $vFpar008['sucdesxx'];
  }
  ##Traigo sucursal##

	$qUsrDat  = "SELECT ";
	$qUsrDat .= "USRNOMXX  ";
	$qUsrDat .= "FROM $cAlfa.SIAI0003 ";
	$qUsrDat .= "WHERE ";
	$qUsrDat .= "USRIDXXX = \"{$_COOKIE['kUsrId']}\" LIMIT 0,1 ";
	$xUsrDat  = f_MySql("SELECT","",$qUsrDat,$xConexion01,"");
	$nFilUsr  = mysql_num_rows($xUsrDat);
	if ($nFilUsr > 0) {
	  $vUsrDat = mysql_fetch_array($xUsrDat);
	}
	//f_Mensaje(__FILE__,__LINE__,$qUsrDat);

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
    $mIP[$_POST['cComId_IPA'.($i+1)]]['unidadfe'] = $vDatosIp[2];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['canfexxx'] += $vDatosIp[1];

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

  ##Traigo la Forma de Pago##
  $cFormaPag = "";
  if (isset($_POST['cComFpag'])) {
    //Buscando descripcion
    $cFormaPag = ($_POST['cComFpag'] == 1) ? "CONTADO" : "CREDITO";
  }

  define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
  require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf_js.php');
  class PDF_AutoPrint extends PDF_Javascript{

    function Header() {        
      global $cAlfa;   global $cRoot;   global $cPlesk_Skin_Directory; global $vSysStr; global $_COOKIE;
      global $vResDat; global $vCliFac;  global $cComCsc; global $dFecEmi; global $dFecVen; global $cFormaPag; 
      global $vMedPag; global $vCiuCfa; global $vPaiCfa; global $vDepCfa; global $cSucDesx;

			//Inicializo Posicion X,Y
      $posx = 20;
      $posy = 15;
      // Hoja Membrete
      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/hoja_membrete_open.jpg', $posx-12, $posy-15, 197, 280);

      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/copianovalida.jpg',10,50,180,180);
      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/facturaanulada.jpg',10,50,180,180);
      ## Datos Adquiriente ##
      $this->setXY($posx,$posy+30);
      $this->SetFont('verdana','',7.5);
      $this->Cell(15,5, "Fecha de la Factura: " . substr($dFecEmi, 8, 2) . "-" . substr($dFecEmi, 5, 2) . "-" . substr($dFecEmi, 0, 4) ,0,0,'L');
      $this->Ln(4.5);
      $this->setX($posx);
      $this->Cell(15,5, "Fecha de vencimiento: " . substr($dFecVen, 8, 2) . "-" . substr($dFecVen, 5, 2) . "-" . substr($dFecVen, 0, 4),0,0,'L');
      $this->Ln(4.5);
      $this->setX($posx);
      $this->Cell(15,5, "Forma de pago: " . $cFormaPag,0,0,'L');
      $vNitsAdq = ['860030380', '860038063', '830002397', '830025224'];
      if (in_array($vCliFac['CLIIDXXX'], $vNitsAdq)) {
        $this->Ln(4.5);
        $this->setX($posx);
        $this->Cell(15,5,utf8_decode("Ciudad Prestación Servicio: ") . $cSucDesx,0,0,'L');
      }
			$this->Ln(4.5);
      $this->setX($posx);
      $this->Cell(15,5, "Medio de pago: " . $_POST['cMePagDes'],0,0,'L');
      $this->Ln(4.5);
      $this->setX($posx);
      $this->Cell(15,5, "Orden de Compra: " . $_POST['cOrdenCompra'],0,0,'L');
      $this->Ln(4.5);
      
      $this->setX($posx);
      $this->Cell(15,5, "Facturado a",0,0,'L');
      $this->Ln(5);
      //Cliente
      $pyy = $this->getY();
      $alinea2 = explode("~",f_Words($vCliFac['CLINOMXX'],130));
      for ($n=0;$n<count($alinea2);$n++) {
        $this->SetFont('verdana','',7);
        $this->setXY($posx,$pyy);
        $this->Cell(150,3.5,$alinea2[$n], 0,0,'L');
        $pyy+=3;
      }

      $this->Ln(0.5);
      //Nit Cliente
      $this->setX($posx);
      $this->Cell(15,5, "N.I.T: " . $vCliFac['CLIIDXXX'] . "-" . f_Digito_Verificacion($vCliFac['CLIIDXXX']),0,0,'L');				
      $this->Ln(4.5);
      //Telefono Cliente
      $this->setX($posx);
      $this->Cell(15,5, "Tel: " . $vCliFac['CLITELXX'],0,0,'L');
      $this->Ln(4.5);
      //Direccion Cliente
      $this->setX($posx);
      $this->MultiCell(100,4, utf8_decode("Dir: " . $vCliFac['CLIDIRXX']),0,'L');
      //Ciudad - Departamento
      $this->setX($posx);
      $this->Cell(15,5, utf8_decode($vCiuCfa['CIUDESXX'] . " - " . $vDepCfa['DEPDESXX']),0,0,'L');
      $this->Ln(4.5);
      //Pais
      $this->setX($posx);
      $this->Cell(15,5, "Pais: ".utf8_decode($vPaiCfa['PAIDESXX']),0,0,'L');

      ## Datos OFE ##
      $this->setXY($posx+165,$posy+30);
      $this->SetFont('verdanab','',7.5);
      $this->Cell(15,5,utf8_decode("FACTURA ELECTRÓNICA DE VENTA No. ").$vResDat['resprexx'],0,0,'R');
      $this->Ln(4.5);
      $this->SetFont('verdana','',7.5);
      $this->setX($posx+165);
      $this->Cell(15,5,utf8_decode("OPENTECNOLOGIA S.A"),0,0,'R');
      $this->Ln(4.5);
      $this->setX($posx+165);
      $this->Cell(15,5,utf8_decode("N.I.T Nº: ") . number_format($vSysStr['financiero_nit_agencia_aduanas'], 0, '', '.'),0,0,'R');
      $this->Ln(4.5);
      $this->setX($posx+165);
      $this->Cell(15,5,utf8_decode("Regimen Común IVA"),0,0,'R');
      $this->Ln(4.5);
      $this->setX($posx+165);
      $this->Cell(15,5,utf8_decode("Actividad Economica No. 6201 ICA 9,66"),0,0,'R');
      $this->Ln(4.5);
      $this->setX($posx+165);
      $this->Cell(15,5,"Dir.: ".utf8_decode("Carrera 70C No. 49 68"),0,0,'R');
      $this->Ln(4.5);
      $this->setX($posx+165);
      $this->Cell(15,5,"Tel.: "."5800820",0,0,'R');
      $this->Ln(4.5);
      $this->setX($posx+165);
      $this->Cell(15,5,utf8_decode("BOGOTA - BOGOTA D.C"),0,0,'R');
      $this->Ln(4.5);

      //Resolucion DIAN 
      //Calculo numero de Meses entre Desde y Hasta
      $dFechaInicial = date_create($vResDat['resfdexx']);
      $dFechaFinal = date_create($vResDat['resfhaxx']);
      $nDiferencia = date_diff($dFechaInicial, $dFechaFinal);
      $nMesesVigencia = ( $nDiferencia->y * 12 ) + $nDiferencia->m;
      
      $this->setX($posx+165);
      $this->Cell(15,5,utf8_decode("Resolución DIAN: ".$vResDat['residxxx']),0,0,'R');
      $this->Ln(4.5);
      $this->setX($posx+165);
      $this->Cell(15,5,utf8_decode(" Fecha de Expedición ").$vResDat['resfdexx'],0,0,'R');
      $this->Ln(4.5);
      $this->setX($posx+165);
      $this->Cell(15,5,"Vigencia: ". $nMesesVigencia ." Meses",0,0,'R');
      $this->Ln(4.5);
      $this->setX($posx+165);
      $this->Cell(15,5,utf8_decode("Numeracion Autorizada ").$vResDat['resprexx']." desde el No. ".$vResDat['resdesxx']." hasta el No. ".$vResDat['reshasxx'],0,0,'R');
      $this->Ln(4.5);
      $this->setX($posx+165);
      $this->Cell(15,5,utf8_decode("CUENTA CORRIENTE NUMERO 01137626-6 DEL BANCO ITAÚ"),0,0,'R');
      $this->Ln(7);

      /***** Cabecera de detalle de los IP *****/
      $this->SetFillColor(150);
      $this->SetTextColor(255);
      $this->SetFont('verdanab','',7);
      $this->setX($posx+1);
      $this->Cell(15,5,"Cantidad",0,0,'C', true);
      $this->Cell(108,5,utf8_decode("Descripción"),0,0,'C', true);
      $this->Cell(27,5,"Valor Unitario.",0,0,'C',true);
      $this->Cell(27,5,"Valor Total",0,0,'C',true);
      $this->SetTextColor(0);
      $this->Ln(4.5);

    }//Function Header
    
    function Footer(){
      global $cPlesk_Skin_Directory;

      $posx	= 20;
      $posy = 203;
      
      $this->setXY($posx,$posy);
      $this->SetFont('verdanab','',7.5);
      $this->MultiCell(150,4, utf8_decode("ESTA FACTURA DE VENTA SE ASIMILA EN SUS EFECTOS LEGALES A LA LETRA DE CAMBIO, Art. 774, 775, 776 Y SIGUIENTES DE C.C. LA NO CANCELACION A SU VENCIMIENTO, CAUSARA EL MAXIMO INTERES PERMITIDO LEGALMENTE."),0,'L');

      $this->setXY($posx+157,$posy);
      $this->SetFont('verdanab','',7);
      $this->Cell(15,5,"CUFE: ",0,0,'L');
      $this->Ln(5);
      $this->setX($posx+157);
      $this->SetFont('verdana','',6);
      $this->MultiCell(15, 3.5, "",0,'L');

      $this->setXY($posx,$posy+21);
      $this->SetFont('verdana','',7);
      $this->Cell(15,5, utf8_decode("Representación Impresa de la Factura electrónica"),0,0,'L');

    }
  }
    
  $pdf=new PDF_AutoPrint('P','mm','Letter');
  $pdf->AddFont('verdana','','verdana.php');
  $pdf->AddFont('verdanab','','verdanab.php');
  $pdf->SetFont('verdana','',8);
  $pdf->SetMargins(0,0,0);
  $pdf->SetAutoPageBreak(0,0);

  ## Inicializando Posiciones X,Y ##
  $posy	= 125;
  $posx	= 21;
  $posfin = 192;
  $posRect = $posy-19;
  $nComVlr_IPTotal = 0;

  // Siguiente Pagina //
  $pdf->AddPage();
 
  ### Ingresos Propios ###
  $py = $posy-11;
	// $mIngPro = array_merge($mIngPro,$mIngPro,$mIngPro,$mIngPro,$mIngPro,$mIngPro,$mIngPro,$mIngPro,$mIngPro,$mIngPro,$mIngPro,$mIngPro,$mIngPro,$mIngPro, $mIngPro,$mIngPro,$mIngPro,$mIngPro,$mIngPro);
  if ($_POST['nSecuencia_IPA'] > 0 || count($mIngPro) > 0) {
    for ($i=0;$i<(count($mIngPro));$i++) {
    
      if($py > $posfin){		
        $pdf->Rect($posx, $posRect, 177, ($posfin-$posRect+2));
        $pdf->Line($posx + 15, $posRect, $posx + 15, $posfin+2);
        $pdf->Line($posx + 123, $posRect, $posx + 123, $posfin+2);
        $pdf->Line($posx + 150, $posRect, $posx + 150, $posfin+2);

        $pdf->AddPage();
        $py = $posy-11;
      }

      //Cantidad
      $pdf->SetFont('verdana','',7);
      $pdf->setXY($posx-3,$py);
      $pdf->Cell(20,4, number_format($mIngPro[$i]['canfexxx'],0,'.',','), 0,0,'C');

      // ($i+1)
      //Descripcion
      $pdf->setXY($posx+20,$py);
      $pdf->Cell(60,4, substr($mIngPro[$i]['comobsxx'],0,55),0,0,'L');

      $nValorUni = ($mIngPro[$i]['unidadfe'] != "A9" && $mIngPro[$i]['canfexxx'] > 0) ? $mIngPro[$i]['comvlrxx']/$mIngPro[$i]['canfexxx'] : $mIngPro[$i]['comvlrxx'];
      //Valor Unitario
      $pdf->setXY($posx+113,$py);
      $pdf->Cell(30,4, number_format($nValorUni,0,',','.'),0,0,'R');
        
      //Valor Total
      $pdf->setXY($posx+140,$py);
      $pdf->Cell(30,4, number_format($mIngPro[$i]['comvlrxx'],0,',','.'),0,0,'R');
      $nComVlr_IPTotal += $mIngPro[$i]['comvlrxx'];

      $py += 4.5;
    }//for ($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
  }
  $posyFin = $py;

  if($py > $posfin + 5){
    $pdf->Rect($posx, $posRect, 177, ($posfin-$posRect+2));
    $pdf->Line($posx + 15, $posRect, $posx + 15, $posfin+2);
    $pdf->Line($posx + 123, $posRect, $posx + 123, $posfin+2);
    $pdf->Line($posx + 150, $posRect, $posx + 150, $posfin+2);
    $pdf->AddPage();
    $nPagina++; 
    $py = $posy;
  }

  $pdf->Rect($posx, $posRect, 177, ($posyFin-$posRect));
  $pdf->Line($posx + 15, $posRect, $posx + 15, $posyFin);
  $pdf->Line($posx + 123, $posRect, $posx + 123, $posyFin);
  $pdf->Line($posx + 150, $posRect, $posx + 150, $posyFin);

  //Subtotales
  $posy = $pdf->getY() + 4.5;
  $posyIni = $posy;

  $nSubTotal = $nComVlr_IPTotal;
  $nTotPag   = $nSubTotal + $_POST['nIPAIva'];

  $pdf->SetFont('verdanab', '', 8);
  $pdf->setXY($posx + 117, $posyIni + 1);
  $pdf->Cell(30, 5, "SUBTOTAL", 0, 0, 'R');
  $pdf->Cell(30, 5, number_format($nSubTotal, 2,',','.'), 0, 0, 'R');
  $pdf->Ln(6);

  $pdf->setX($posx + 117);
  $pdf->Cell(30, 5, "IVA", 0, 0, 'R');
  $pdf->Cell(30, 5, number_format($_POST['nIPAIva'], 2,',','.'), 0, 0, 'R');
  $pdf->Ln(6);

  $pdf->setX($posx + 117);
  $pdf->Cell(30, 5, "TOTAL", 0, 0, 'R');
  $pdf->Cell(30, 5, number_format($nTotPag, 2,',','.'), 0, 0, 'R');
  $pdf->Ln(6);

  //Recuadro de totales
  $pdf->Line($posx + 150, $posy, $posx + 150, $posy + 18);
  $pdf->Line($posx + 123, $posy + 6, $posx + 177, $posy + 6);
  $pdf->Line($posx + 123, $posy + 12, $posx + 177, $posy + 12);
  $pdf->Rect($posx + 123, $posy, 54, 18);

  ## Observacion
  $pdf->setXY($posx, $posyIni + 1);
  $pdf->SetFont('verdanab', '', 8);
  $pdf->Cell(30, 5, "OBSERVACIONES:", 0, 0, 'L');
  $pdf->Ln(5);
  $pdf->setX($posx);
  $pdf->SetFont('verdana', '', 8);
  $pdf->MultiCell(128, 4, utf8_decode($_POST['cComObs']), 0, 'L');

  ### Inicializo posicion Y
  $posy = 215;
  $nTotPag1 = f_Cifra_Php($nTotPag,'PESO');
  $pdf->setXY($posx-1, $posy);
  $pdf->SetFont('verdanab', '', 8);
  $pdf->MultiCell(130, 4, "SON: " . $nTotPag1 , 0, 'L');

  $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
	$pdf->Output($cFile);

  if (file_exists($cFile)){
    chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
  } else {
    f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
  }

	echo "<html><script>document.location='$cFile';</script></html>";
?>

<?php
  namespace openComex;
  use PHPExcel;
  use PHPExcel_Cell_DataType;
  use PHPExcel_Style_Alignment;
  use PHPExcel_Style_Fill;
  use PHPExcel_Style_Border;
  use PHPExcel_IOFactory;
##Estableciendo que el tiempo de ejecucion no se limite
//set_time_limit (0);
/**
 * Generar.
 * --- Descripcion: Generar Informe Check Register
 * @author Marcio Vilalta <marcio.vilalta@opentecnologia.com.co>
 * @version 001
 * @package opencomex
 */

//ini_set('error_reporting', E_ERROR);
//ini_set("display_errors","1");

include("../../../../../libs/php/utility.php");
include("../../../../../class/PHPExcel/PHPExcel.php");
include("../../../../../class/PHPExcel/PHPExcel/Reader/Excel2007.php");
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

$nSwitch = 0; // Switch para Vericar la Validacion de Datos
$cTexErr = "";
$cMsj    = "\n";

/**
* Validando Licencia
*/
$nLic = f_Licencia();
if ($nLic == 0) {
	$nSwitch = 1;
	$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	$cMsj .= "Error grave de Seguridad otro usuario ingreso con su clave.\n";
}

if ( substr($dDesde,0,4) != substr($dHasta,0,4) ) {
	$nSwitch = 1;
	f_Mensaje(__FILE__,__LINE__, "Las fechas deben ser del mismo año.");
}

$qComCon  = "SELECT ";
$qComCon .= "CONCAT(comidxxx,\"~\",comcodxx) AS comidxxx ";
$qComCon .= "FROM $cAlfa.fpar0117 ";
$qComCon .= "WHERE ";
if ( $tipo == 1 ) {
	$cNomTipo = "SCSGENERALUPL";
	$cNombreFile = "SCOSCSGENERAL";
	$qComCon .= "($cAlfa.fpar0117.comtipxx = \"CPE\" OR $cAlfa.fpar0117.cominafg = \"SI\") AND ";
} else {
	$cNomTipo = "SCSDUTYUPL";
	$cNombreFile = "SCOSCSDUTY";
	$qComCon .= "($cAlfa.fpar0117.comtipxx = \"CPC\" OR $cAlfa.fpar0117.cominafd = \"SI\") AND ";
}
$qComCon .= "$cAlfa.fpar0117.regestxx = \"ACTIVO\" ";
$xComCon  = f_MySql("SELECT","",$qComCon,$xConexion01,"");
$cComCon = "";
while ($xRCC = mysql_fetch_array($xComCon)) {
	$cComCon .= "\"{$xRCC['comidxxx']}\",";
}
$cComCon = substr($cComCon, 0, strlen($cComCon)-1);

// f_Mensaje(__FILE__, __LINE__, $cComCon);

$qCuenta  = "SELECT ";
$qCuenta .= "CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx ";
$qCuenta .= "FROM $cAlfa.fpar0115 ";
$qCuenta .= "WHERE ";
// $qCuenta .= "pucdetxx IN (\"P\",\"C\") OR ";
$qCuenta .= "CONCAT(pucgruxx,pucctaxx) = \"2205\" OR ";
$qCuenta .= "CONCAT(pucgruxx,pucctaxx) = \"2225\" OR ";
$qCuenta .= "CONCAT(pucgruxx,pucctaxx) = \"2335\" OR ";
$qCuenta .= "pucexraf = \"SI\" ";
$XCuenta  = f_MySql("SELECT","",$qCuenta,$xConexion01,"");
// f_Mensaje(__FILE__, __LINE__, $qCuenta);
/* Creo cadena */
$cCuentas = "";
while ($xCUE = mysql_fetch_array($XCuenta)) {
	$cCuentas .=  "\"{$xCUE['pucidxxx']}\",";
}
$cCuentas = substr($cCuentas, 0, strlen($cCuentas)-1);

// f_Mensaje(__FILE__, __LINE__, $cCuentas);

//Trayendo cuentas que se homologan con oracle
$qCuenta  = "SELECT pucupsor, ";
$qCuenta .= "CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx ";
$qCuenta .= "FROM $cAlfa.fpar0115 ";
$qCuenta .= "WHERE ";
$qCuenta .= "pucupsor != \"\"";
$XCuenta  = f_MySql("SELECT","",$qCuenta,$xConexion01,"");
/* Creo cadena */
$vCueUps = "";
while ($xCUE = mysql_fetch_array($XCuenta)) {
	$vCueUps["{$xCUE['pucidxxx']}"] = "{$xCUE['pucupsor']}";
}

$nAno = substr($dDesde,0,4);

//Tabla temporal para cabecera contable
$cTabFcoc = fnCadenaAleatoria();
$qNewTab  = "CREATE TEMPORARY TABLE IF NOT EXISTS $cAlfa.$cTabFcoc LIKE $cAlfa.fcoc$nAno";
// f_Mensaje(__FILE__,__LINE__,$qNewTab);
$xNewTab = mysql_query($qNewTab,$xConexion01);
if (!$xNewTab) {
	$nSwitch = 1;
	f_Mensaje(__FILE__,__LINE__, "Error al Crear Tabla Temporal Cabecera.");
}

$qFcoc  = "SELECT $cAlfa.fcoc$nAno.* ";
$qFcoc .= "FROM $cAlfa.fcoc$nAno ";
$qFcoc .= "WHERE ";
$qFcoc .= "CONCAT($cAlfa.fcoc$nAno.comidxxx,\"~\",$cAlfa.fcoc$nAno.comcodxx) IN ($cComCon) AND ";
$qFcoc .= "$cAlfa.fcoc$nAno.comfecxx BETWEEN \"{$dDesde}\" AND \"{$dHasta}\" AND ";
$qFcoc .= "$cAlfa.fcoc$nAno.regestxx = \"ACTIVO\" ";

$qInsert = "INSERT INTO $cAlfa.$cTabFcoc $qFcoc";
$xInsert = mysql_query($qInsert,$xConexion01);

if (!$xInsert) {
	$nSwitch = 1;
	f_Mensaje(__FILE__,__LINE__, "Error al Insertar Datos Tabla Temporal Cabecera.");
}
//Fin Tabla temporal para cabecera contable

//Tabla temporal para detalle contable
$cTabFcod = fnCadenaAleatoria();
$qNewTab  = "CREATE TEMPORARY TABLE IF NOT EXISTS $cAlfa.$cTabFcod LIKE $cAlfa.fcod$nAno";
$xNewTab = mysql_query($qNewTab,$xConexion01);
if (!$xNewTab) {
	$nSwitch = 1;
	f_Mensaje(__FILE__,__LINE__, "Error al Crear Tabla Temporal Cabecera.");
}

$qFcod  = "SELECT $cAlfa.fcod$nAno.* ";
$qFcod .= "FROM $cAlfa.fcod$nAno ";
$qFcod .= "WHERE ";
$qFcod .= "CONCAT($cAlfa.fcod$nAno.comidxxx,\"~\",$cAlfa.fcod$nAno.comcodxx) IN ($cComCon) AND ";
$qFcod .= "$cAlfa.fcod$nAno.comfecxx BETWEEN \"{$dDesde}\" AND \"{$dHasta}\" AND ";
$qFcod .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";

$qInsert = "INSERT INTO $cAlfa.$cTabFcod $qFcod";
$xInsert = mysql_query($qInsert,$xConexion01);
if (!$xInsert) {
	$nSwitch = 1;
	f_Mensaje(__FILE__,__LINE__, "Error al Insertar Datos Tabla Temporal Cabecera.");
}

if ($nSwitch == 0) {
	$qFcod  = "SELECT ";
	$qFcod .= "$cAlfa.$cTabFcod.*,";
	$qFcod .= "DATE_FORMAT($cAlfa.$cTabFcod.comfecxx, \"%y%m%d\") as comfec1x, ";
	$qFcod .= "$cAlfa.fpar0115.pucterxx, ";
	$qFcod .= "$cAlfa.fpar0115.pucretxx, ";
	$qFcod .= "$cAlfa.fpar0115.pucdetxx ";
	$qFcod .= "FROM $cAlfa.$cTabFcod ";
	$qFcod .= "LEFT JOIN $cAlfa.fpar0115 ON CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = $cAlfa.$cTabFcod.pucidxxx ";
	$qFcod .= "WHERE ";
	// $qFcod .= "$cAlfa.$cTabFcod.comcsc3x = \"FP-385\" AND ";
	$qFcod .= "$cAlfa.$cTabFcod.pucidxxx NOT IN ($cCuentas) ";
	$qFcod .= "ORDER BY $cAlfa.$cTabFcod.comidxxx, $cAlfa.$cTabFcod.comcodxx, $cAlfa.$cTabFcod.comcscxx, $cAlfa.$cTabFcod.comcsc2x, $cAlfa.$cTabFcod.comseqxx";
  // f_Mensaje(__FILE__, __LINE__, $qFcod);
	$xFcod  = mysql_query($qFcod,$xConexion01);

	$mDatos = array();
	$mImpuestos = array();
	$cKey = ""; $cKeyTem = "";
  $nCanReg = 0; $nCanDoc = 0;
	while ($xRFD = mysql_fetch_array($xFcod)) {

		if (substr($xRFD['pucidxxx'], 0, 4) == "2408") {
			//Para el iva hay que detectar a que concepto pertenece para indicar en esa secuencia que aplica IVA, el iva no se incluye en la matriz del comprobante
			//Para las demas retenciones hay que buscar a que concepto pertenece para indicar a que DO aplica e incluirlas en la matriz del comprobante
			$mImpuestos[] = $xRFD;
		} else {
			//si la cuenta detalla por DO y en campo docidxxx esta vacio entonces se envia el consecutivo dos
			$xRFD['docidxxx'] = ($xRFD['docidxxx'] != "") ? $xRFD['docidxxx'] : (($xRFD['pucdetxx'] == "D") ? $xRFD['comcsccx'] : "");

			$cKey = $xRFD['comidxxx'].'-'.$xRFD['comcodxx'].'-'.$xRFD['comcscxx'].'-'.$xRFD['comcsc2x'];
			if ($cKeyTem != $cKey) {
			  $nCanDoc++;

			  //Buscando datos de cabecera del comprobnate
				$qFcoc  = "SELECT ";
				$qFcoc .= "$cAlfa.$cTabFcoc.comvlrxx, ";
				$qFcoc .= "$cAlfa.$cTabFcoc.comobsxx, ";
				$qFcoc .= "$cAlfa.SIAI0150.CLIIDAPX, ";
				$qFcoc .= "$cAlfa.SIAI0150.CLIIDCPX ";
				$qFcoc .= "FROM $cAlfa.$cTabFcoc ";
				$qFcoc .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.$cTabFcoc.terid2xx = $cAlfa.SIAI0150.CLIIDXXX ";
				$qFcoc .= "WHERE ";
				$qFcoc .= "$cAlfa.$cTabFcoc.comidxxx = \"{$xRFD['comidxxx']}\" AND ";
				$qFcoc .= "$cAlfa.$cTabFcoc.comcodxx = \"{$xRFD['comcodxx']}\" AND ";
				$qFcoc .= "$cAlfa.$cTabFcoc.comcscxx = \"{$xRFD['comcscxx']}\" AND ";
				$qFcoc .= "$cAlfa.$cTabFcoc.comcsc2x = \"{$xRFD['comcsc2x']}\" ";
				$xFcoc  = mysql_query($qFcoc,$xConexion01);
				$vFcoc  = array();
				$vFcoc  = mysql_fetch_array($xFcoc);

        //Buscando el valor total del comprobante que es la sumatoria de las cuentas por cobrar y por pagar
        $qFcodTot  = "SELECT ";
        $qFcodTot .= "SUM(IF(commovxx = \"D\",comvlrxx, comvlrxx*-1)) AS sumaxxxx ";
        $qFcodTot .= "FROM $cAlfa.$cTabFcod ";
        $qFcodTot .= "WHERE ";
        $qFcodTot .= "comidxxx = \"{$xRFD['comidxxx']}\" AND ";
        $qFcodTot .= "comcodxx = \"{$xRFD['comcodxx']}\" AND ";
        $qFcodTot .= "comcscxx = \"{$xRFD['comcscxx']}\" AND ";
        $qFcodTot .= "comcsc2x = \"{$xRFD['comcsc2x']}\" AND ";
        $qFcodTot .= "pucidxxx NOT IN ($cCuentas) ";
        $xFcodTot  = mysql_query($qFcodTot,$xConexion01);
        $vFcodTot  = array();
        $vFcodTot  = mysql_fetch_array($xFcodTot);
			}

			$cKeyTem = $cKey;

      $nCanReg++;

			$nInd_mDatos = count($mDatos[$cKey]);
			$mDatos[$cKey][$nInd_mDatos]['PUCIDXXX'] = $xRFD['pucidxxx'];
			$mDatos[$cKey][$nInd_mDatos]['PUCDETXX'] = $xRFD['pucdetxx'];
			$mDatos[$cKey][$nInd_mDatos]['PUCRETXX'] = $xRFD['pucretxx'];
			$mDatos[$cKey][$nInd_mDatos]['PUCTERXX'] = $xRFD['pucterxx'];
			$mDatos[$cKey][$nInd_mDatos]['COMSEQXX'] = $xRFD['comseqxx'];

			$mDatos[$cKey][$nInd_mDatos]['SOURCEXX'] = $cNomTipo;
			$mDatos[$cKey][$nInd_mDatos]['COUNTRYC'] = "CO";
			$mDatos[$cKey][$nInd_mDatos]['SUPPLNUM'] = $vFcoc['CLIIDCPX'];
			$mDatos[$cKey][$nInd_mDatos]['SUPPLSIT'] = $vFcoc['CLIIDAPX'];
			$mDatos[$cKey][$nInd_mDatos]['PAYGROUP'] = "";
			$mDatos[$cKey][$nInd_mDatos]['PAYALONE'] = "";
			$mDatos[$cKey][$nInd_mDatos]['PRIORITY'] = "";
			$mDatos[$cKey][$nInd_mDatos]['PAYTERMS'] = "";

			$mDatos[$cKey][$nInd_mDatos]['INVOICEN'] = $xRFD['comcscxx'];
			$mDatos[$cKey][$nInd_mDatos]['INVOICED'] = $xRFD['comfec1x'];
			$mDatos[$cKey][$nInd_mDatos]['INVOICEA'] = $vFcodTot['sumaxxxx'];
			$mDatos[$cKey][$nInd_mDatos]['VALORREG'] = $xRFD['comvlrxx'];

			$cDescReference  = ($xRFD['comcsc3x'] != "") ? $xRFD['comcsc3x']." " : " ";
			$cDescReference .= ($xRFD['comobsxx'] != "") ? $xRFD['comobsxx']." " : " ";
			$cDescReference .= ($xRFD['docidxxx'] != "") ? "DO ".$xRFD['docidxxx']." ".$xRFD['sucidxxx'] : "";
			$cDescReference  = trim($cDescReference);
			$mDatos[$cKey][$nInd_mDatos]['DESCRE01'] = $cDescReference;

			$mDatos[$cKey][$nInd_mDatos]['DESCEXTD'] = "";
			$mDatos[$cKey][$nInd_mDatos]['DESCSHIP'] = "";
			$mDatos[$cKey][$nInd_mDatos]['DESCSTAM'] = "BOV".$xRFD['comcsc2x'];
			$mDatos[$cKey][$nInd_mDatos]['DESCCHEN'] = "";
			$mDatos[$cKey][$nInd_mDatos]['DESCCHED'] = "";
			$mDatos[$cKey][$nInd_mDatos]['DESCCHEA'] = "";
			$mDatos[$cKey][$nInd_mDatos]['DESCETAD'] = "";
			$mDatos[$cKey][$nInd_mDatos]['DESCRE02'] = "";
			$mDatos[$cKey][$nInd_mDatos]['DESCRUCX'] = "";
			$mDatos[$cKey][$nInd_mDatos]['EXTHED11'] = "";
			$mDatos[$cKey][$nInd_mDatos]['EXTHED12'] = "";
			$mDatos[$cKey][$nInd_mDatos]['EXTHED13'] = "";
			$mDatos[$cKey][$nInd_mDatos]['EXTHED14'] = "";
			$mDatos[$cKey][$nInd_mDatos]['EXTHED15'] = "";
			$mDatos[$cKey][$nInd_mDatos]['EXTHED16'] = "";
			$mDatos[$cKey][$nInd_mDatos]['EXTHED17'] = "";
			$mDatos[$cKey][$nInd_mDatos]['EXTHED18'] = "";
			$mDatos[$cKey][$nInd_mDatos]['EXTHED19'] = "";
			$mDatos[$cKey][$nInd_mDatos]['EXTHED20'] = "";
			$mDatos[$cKey][$nInd_mDatos]['INVOICEC'] = "COP";
			$mDatos[$cKey][$nInd_mDatos]['IMAGEBAT'] = "";
			$mDatos[$cKey][$nInd_mDatos]['INVOICET'] = "";
			$mDatos[$cKey][$nInd_mDatos]['CONTEXTV'] = "";
			$mDatos[$cKey][$nInd_mDatos]['REQUELOC'] = "";
			$mDatos[$cKey][$nInd_mDatos]['PRINTLOC'] = "";
			$mDatos[$cKey][$nInd_mDatos]['KIDCODEE'] = "";
			$mDatos[$cKey][$nInd_mDatos]['DESCSTAI'] = "ITEM";
			$mDatos[$cKey][$nInd_mDatos]['PONUMBER'] = "";
			$mDatos[$cKey][$nInd_mDatos]['POLINENU'] = "";
			$mDatos[$cKey][$nInd_mDatos]['QUANTITY'] = "";
			$mDatos[$cKey][$nInd_mDatos]['POUNITPR'] = "";
			$mDatos[$cKey][$nInd_mDatos]['UNITMEAU'] = "";
			// $mDatos[$cKey][$nInd_mDatos]['LINEAMOU'] = $xRFD['comvlrxx'];
			$mDatos[$cKey][$nInd_mDatos]['LINEAMOU'] = ($xRFD['pucterxx'] == "R") ? $xRFD['comvlrxx']*-1 : $xRFD['comvlrxx'];

			$cTaxCode = "";
			//Si la cuenta es de retención y esta marcada con el 16% se envía SCO3041
			if ($xRFD['pucterxx'] == "R" && ($xRFD['pucretxx']+0) == 16) {
				$cTaxCode = "SCO3041";
			} else if ($xRFD['pucterxx'] == "R" && ($xRFD['pucretxx']+0) == 5) {
				//Si la cuenta es de retención y esta marcada con el 5% se envía SCO3044
				$cTaxCode = "SCO3044";
			} else if ($xRFD['pucterxx'] == "R"){//Para las cuentas de retención el Tax Code es SCO304X
				$cTaxCode = "SCO304X";
			} else {
				//si no se ajusta a ninguna de las anteriores SCO3040
				$cTaxCode = "SCO3040";
			}

			$mDatos[$cKey][$nInd_mDatos]['TAXCODEX'] = $cTaxCode;

			if ($tipo == 1) {
				$mDatos[$cKey][$nInd_mDatos]['LINEDEDO'] = "";
			} else {
				if ($xRFD['docidxxx'] == "") {
					$qDo  = "SELECT docidxxx ";
					$qDo .= "FROM $cAlfa.$cTabFcod ";
					$qDo .= "WHERE comidxxx = \"{$xRFD['comidxxx']}\" ";
					$qDo .= "AND comcodxx = \"{$xRFD['comcodxx']}\" ";
					$qDo .= "AND comcscxx = \"{$xRFD['comcscxx']}\" ";
					$qDo .= "AND docidxxx != \"\" LIMIT 0,1";
					$xDo  = mysql_query($qDo,$xConexion01);
					$vDo = mysql_fetch_array($xDo);
					$mDatos[$cKey][$nInd_mDatos]['LINEDEDO'] = $vDo['docidxxx'];
				} else {
					$mDatos[$cKey][$nInd_mDatos]['LINEDEDO'] = $xRFD['docidxxx'];
				}
			}

			$mDatos[$cKey][$nInd_mDatos]['LINEDEDD'] = $vFcoc['comobsxx'];
			$mDatos[$cKey][$nInd_mDatos]['LINEDEOR'] = "";
			$mDatos[$cKey][$nInd_mDatos]['LINEDEDE'] = $vFcoc['comobsxx'];
			$mDatos[$cKey][$nInd_mDatos]['EXTLIN05'] = "";
			$mDatos[$cKey][$nInd_mDatos]['EXTLIN06'] = "";
			$mDatos[$cKey][$nInd_mDatos]['EXTLIN07'] = "";
			$mDatos[$cKey][$nInd_mDatos]['EXTLIN08'] = "";
			$mDatos[$cKey][$nInd_mDatos]['EXTLIN09'] = "";
			$mDatos[$cKey][$nInd_mDatos]['EXTLIN10'] = "";
			$mDatos[$cKey][$nInd_mDatos]['EXTLIN11'] = "";
			$mDatos[$cKey][$nInd_mDatos]['EXTLIN12'] = "";
			$mDatos[$cKey][$nInd_mDatos]['EXTLIN13'] = "";
			$mDatos[$cKey][$nInd_mDatos]['EXTLIN14'] = "";
			$mDatos[$cKey][$nInd_mDatos]['EXTLIN15'] = "";
			$mDatos[$cKey][$nInd_mDatos]['EXTLIN16'] = "";
			$mDatos[$cKey][$nInd_mDatos]['EXTLIN17'] = "";
			$mDatos[$cKey][$nInd_mDatos]['EXTLIN18'] = "";
			$mDatos[$cKey][$nInd_mDatos]['EXTLIN19'] = "";
			$mDatos[$cKey][$nInd_mDatos]['EXTLIN20'] = "";

			if ( $xRFD['pucterxx'] == 'R' || $xRFD['pucdetxx'] == 'D' ) {
				$nCuenta = '000000';
				$nCcoid = '000';
			} else if ( $xRFD['ccoidxxx'] == 'C01' ) {
				//se debe enviar 503027
				$nCuenta = '503027';
				$nCcoid = $xRFD['ccoidxxx'];
			} else if ( $xRFD['ccoidxxx'] == 'C03' && $xRFD['sccidxxx'] == 'CTG' ) {
				//se encia el codigo 503028
				$nCuenta = '503028';
				$nCcoid = $xRFD['ccoidxxx'];
			} else if (  $xRFD['ccoidxxx'] == 'C03' && $xRFD['sccidxxx'] == 'BUN' ) {
				//se encia el codigo 503029
				$nCuenta = '503029';
				$nCcoid = $xRFD['ccoidxxx'];
			} else {
				//para el resto se envia 000000
				$nCuenta = '000000';
				$nCcoid = $xRFD['ccoidxxx'];
			}

      if ( $tipo == 1) { //APUTGENERAL
        if ($xRFD['ccoidxxx'] == "L55") {
          $nCcoid = ((substr($vCueUps["{$xRFD['pucidxxx']}"],0,6) == "211651") || (substr($vCueUps["{$xRFD['pucidxxx']}"],0,6) == "211652") || (substr($vCueUps["{$xRFD['pucidxxx']}"],0,6) == "211653") || (substr($vCueUps["{$xRFD['pucidxxx']}"],0,6) == "211654")) ? "000" : $nCcoid;
          //se debe conservar la estructura con el 000 cuando existan centros de costos de L55 ( 304.2864.503027.L55.000.786039 )
          $mDatos[$cKey][$nInd_mDatos]['ACCOUNTS'] = "304.2864.$nCuenta.$nCcoid.000.".substr ($vCueUps["{$xRFD['pucidxxx']}"],0,6);
        } else {

          $nCentro = ((substr($vCueUps["{$xRFD['pucidxxx']}"],0,6) == "211651") || (substr($vCueUps["{$xRFD['pucidxxx']}"],0,6) == "211652") || (substr($vCueUps["{$xRFD['pucidxxx']}"],0,6) == "211653") || (substr($vCueUps["{$xRFD['pucidxxx']}"],0,6) == "211654")) ? "000" : "L03";
          //debe quedar 304.2864.503029.L03.C03.742201 Es decir en vez de que el sistema genere 000 coloque un L03 pero antes del centro de Costos (C03).
          $mDatos[$cKey][$nInd_mDatos]['ACCOUNTS'] = "304.2864.$nCuenta.$nCentro.$nCcoid.".substr ($vCueUps["{$xRFD['pucidxxx']}"],0,6);

        }
      } else{ //APUTDUTY
        $nCcoid = ((substr($vCueUps["{$xRFD['pucidxxx']}"],0,6) == "211651") || (substr($vCueUps["{$xRFD['pucidxxx']}"],0,6) == "211652") || (substr($vCueUps["{$xRFD['pucidxxx']}"],0,6) == "211653") || (substr($vCueUps["{$xRFD['pucidxxx']}"],0,6) == "211654")) ? "000" : $nCcoid;
        $mDatos[$cKey][$nInd_mDatos]['ACCOUNTS'] = "304.2864.$nCuenta.$nCcoid.000.".substr($vCueUps["{$xRFD['pucidxxx']}"],0,6);
      }

			$mDatos[$cKey][$nInd_mDatos]['ITEMNUMX'] = "";
			$mDatos[$cKey][$nInd_mDatos]['SHIPTOLO'] = "";
			$mDatos[$cKey][$nInd_mDatos]['CHARGETO'] = "";

			$cCode = (substr($xRFD['pucidxxx'],0,1)  == 5 || substr($xRFD['pucidxxx'],0,1)  == 6 || substr($xRFD['pucidxxx'],0,1)  == 7) ? "SCS" :  "SCS2BALSHT";
			$mDatos[$cKey][$nInd_mDatos]['ATTRICAT'] = $cCode;
			$mDatos[$cKey][$nInd_mDatos]['ATTRIBU1'] = $nCcoid;
			$mDatos[$cKey][$nInd_mDatos]['ATTRIBU2'] = "0000000";
			$mDatos[$cKey][$nInd_mDatos]['PROJECTN'] = "";
			$mDatos[$cKey][$nInd_mDatos]['PROJECTT'] = "";
			$mDatos[$cKey][$nInd_mDatos]['PROJECTE'] = "";
			$mDatos[$cKey][$nInd_mDatos]['ENDOFROW'] = "I";
		}
	}
}

##Validando las cuentas del iva a que registro correspode
$nBand = 0; $mError = array(); //$cMsj  = "";

for ($i=0; $i<count($mImpuestos); $i++) {
	$cKey = $mImpuestos[$i]['comidxxx'].'-'.$mImpuestos[$i]['comcodxx'].'-'.$mImpuestos[$i]['comcscxx'].'-'.$mImpuestos[$i]['comcsc2x'];

	$vEncontro = array(); $nTotCom = 0;
	for ($y=0; $y<count($mDatos[$cKey]); $y++) {
		//Se excluyen las cuentas por cobrar o por pagar o de rentencio
		if ($mDatos[$cKey][$y]['PUCTERXX'] != "R" && $mDatos[$cKey][$y]['APLIVAXX'] == "") {
			$nTotCom += $mDatos[$cKey][$y]['VALORREG'];

      //Buscando cual fue el servicio al que se le calculo el IVA
			$mImpuestos[$i]['ivaxxxxx'] = round($mDatos[$cKey][$y]['VALORREG'] * ($mImpuestos[$i]['pucretxx']/100));
			if (($mImpuestos[$i]['ivaxxxxx']+0) == ($mImpuestos[$i]['comvlrxx']+0)) {
				$vEncontro[] = $mDatos[$cKey][$y]['COMSEQXX'];
        $mDatos[$cKey][$y]['APLIVAXX'] = "CONIVA";
        $mDatos[$cKey][$y]['pucretxx'] = $mImpuestos[$i]['pucretxx'];
			} else {
        $mImpuestos[$i]['ivaxxxxx'] = ceil($mDatos[$cKey][$y]['VALORREG'] * ($mImpuestos[$i]['pucretxx']/100));
        if (($mImpuestos[$i]['ivaxxxxx']+0) == ($mImpuestos[$i]['comvlrxx']+0)) {
          $vEncontro[] = $mDatos[$cKey][$y]['COMSEQXX'];
          $mDatos[$cKey][$y]['APLIVAXX'] = "CONIVA";
          $mDatos[$cKey][$y]['pucretxx'] = $mImpuestos[$i]['pucretxx'];
        } else {
          $mImpuestos[$i]['ivaxxxxx'] = floor($mDatos[$cKey][$y]['VALORREG'] * ($mImpuestos[$i]['pucretxx']/100));
          if (($mImpuestos[$i]['ivaxxxxx']+0) == ($mImpuestos[$i]['comvlrxx']+0)) {
            $vEncontro[] = $mDatos[$cKey][$y]['COMSEQXX'];
            $mDatos[$cKey][$y]['APLIVAXX'] = "CONIVA";
            $mDatos[$cKey][$y]['pucretxx'] = $mImpuestos[$i]['pucretxx'];
          }
        }
			}
		}
	}

	//Verifico si se hizo un solo registro de pago de retencion o iva para todos conceptos del comprobante
	if (count($vEncontro) == 0) {
		$mImpuestos[$i]['ivaxxxxx'] = round($nTotCom * 0.16);
		if (($mImpuestos[$i]['ivaxxxxx']+0) == ($mImpuestos[$i]['comvlrxx']+0)) {
			$vEncontro[] = "TODOS";
		}
	}

	if (count($vEncontro) == 0) {
		$nBand = 1;
    $mError[count($mError)] = "[{$mImpuestos[$i]['comidxxx']}-{$mImpuestos[$i]['comcodxx']}-{$mImpuestos[$i]['comcscxx']}-{$mImpuestos[$i]['comcsc3x']}]: Error al Buscar el Concepto Base del Iva Calculado en la Secuencia [{$mImpuestos[$i]['comseqxx']}].";
		// $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
    // $cMsj .= "[{$mImpuestos[$i]['comidxxx']}-{$mImpuestos[$i]['comcodxx']}-{$mImpuestos[$i]['comcscxx']}-{$mImpuestos[$i]['comcsc3x']}]: Error al Buscar el Concepto Base del Iva Calculado en la Secuencia [{$mImpuestos[$i]['comseqxx']}].\n";
	} else {
		if (count($vEncontro) > 1) {
			//Hay que decidir a cual se le aplico el IVA o la retencion
			//Es muy importante que cuando se haga el comprobante primero este el servicio, despues el iva, despues las retenciones
			//si hay otro servicio, se incluye en el mismo orden
			for($n=count($vEncontro)-1; $n>=0; $n--) {
				if (($vEncontro[$n]+0) < ($mImpuestos[$i]['comseqxx']+0)) {
					$nPosY = $vEncontro[$n];
					$n=-1;
				}
			}
		} else {
			$nPosY = $vEncontro[0];
		}

		for ($y=0; $y<count($mDatos[$cKey]); $y++) {
			//Se excluyen las cuentas por cobrar o por pagar
			if ($mDatos[$cKey][$y]['PUCTERXX'] != "R") {
				if ($nPosY == "TODOS") {
          $mDatos[$cKey][$y]['TAXCODEX'] = "SCO3041";
					//Se Calcula la base
					$mDatos[$cKey][$y]['LINEAMOU'] = $mDatos[$cKey][$y]['VALORREG'];
				} elseif ($nPosY == $mDatos[$cKey][$y]['COMSEQXX']) {
				  //Si a la cuenta aplica algun impuesto de la 2408 se envia el taxcode que corresponde a su impuesto
          if (($mDatos[$cKey][$y]['pucretxx']+0) == 16) {
            $cTaxCode = "SCO3041";
          } else if (($mDatos[$cKey][$y]['pucretxx']+0) == 5) {
            //Si la cuenta es de retención y esta marcada con el 5% se envía SCO3044
            $cTaxCode = "SCO3044";
          } else {
            //si no se ajusta a ninguna de las anteriores SCO3041
            $cTaxCode = "SCO3041";
          }
				  $mDatos[$cKey][$y]['TAXCODEX'] = $cTaxCode;
					//Se Calcula la base
					$mDatos[$cKey][$y]['LINEAMOU'] = $mDatos[$cKey][$y]['VALORREG'];
				}
			}
		}
	}
} ##for ($i=0; $i<count($mImpuestos); $i++) {##

if ($nBand == 1) {
  //Creo el archivo de Errores
  // $nSwitch = 1;
  f_Mensaje(__FILE__,__LINE__,"Se Presentaron Inconsistencias al Generar el Reporte");

  $cFile01 = "ERRORES_".$kUser."_".date('YmdHis').".xls";
  $cFileDownload = "{$OPENINIT['pathdr']}/opencomex/".$vSysStr['system_download_directory']."/".$cFile01;
  $cF01 = fopen($cFileDownload,"a");

  $cData  = '<table border="1" cellpadding="0" cellspacing="0" style="width:800px"><tr>';
  $cData .= '<td><b>Se Presentaron Los Siguientes Errores al Generar el Reporte:</b></td>';
  $cData .= '</tr>';
  fwrite($cF01,$cData);

  for($nR=0;$nR<count($mError);$nR++){
    $cData  = '<tr>';
    $cData .= '<td style="mso-number-format:\'\@\'">'.$mError[$nR].'</td>';
    $cData .= '</tr>';
    fwrite($cF01,$cData);
  }
  $cData  = '</table><br>';
  fwrite($cF01,$cData);
  fclose($cF01);

  ?>
  <script languaje = "javascript">
    parent.fmpro2.location = 'frgendoc.php?cRuta=<?php echo $cFile01 ?>';
  </script>
  <?php
}

##Validando Datos del archivo Cargado
if ($nSwitch == 0) {

	if(count($mDatos) > 0 ) {
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// Set document properties
		$objPHPExcel->getProperties()->setCreator("openComex")
								->setLastModifiedBy("Por openComex")
								->setTitle("Reporte AputFile")
								->setSubject("Plantilla Basica para Generar Reporte AputFile")
								->setDescription("Plantilla Basica para Generar Reporte AputFile")
								->setKeywords("plantilla Reporte AputFile")
								->setCategory("Template $cNomTipo");


		##PONGO EL FONDO EN EL COLOR DESEADO
		$objPHPExcel->getActiveSheet()->getStyle('A2:BY2')->getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setARGB('C0C0C0');
		$objPHPExcel->getActiveSheet()->getStyle('A2:BY2')->getFont()->setBold(true) -> setSize(10) -> getColor()->setRGB('000000');
		$objPHPExcel->getActiveSheet()->getStyle('A3:BY3')->getFont()->setBold(true) -> setSize(10) -> getColor()->setRGB('000000');
		$objPHPExcel->getActiveSheet()->getStyle('A2:BY2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('J')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('K') ->getNumberFormat()->setFormatCode("[black]#.##0;[Red](-#.##0)");
		$objPHPExcel->getActiveSheet()->getStyle('AS') ->getNumberFormat()->setFormatCode("[black]#.##0;[Red](-#.##0)");

		$objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
		$objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(45);
		$objPHPExcel->getActiveSheet()->getStyle('A1:BY1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment:: HORIZONTAL_CENTER);

		$objPHPExcel->getActiveSheet()->getStyle('O')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		##por defecto alineo todo a la izquierda.
		$objPHPExcel->getDefaultStyle() -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('J')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment:: HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->setAutoFilter('A3:BY3');

		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', 'SOURCE');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', 'Required');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B2', 'COUNTRY CODE');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', 'Required');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C2', 'SUPPLIER_NUM');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C3', 'Required');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D2', 'SUPPLIER_SITE_CODE');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D3', 'Required');

		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E2', 'PAY_GROUP OVERRIDE');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F2', 'PAY ALONE FLAG');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G2', 'PRIORITY FLAG');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H2', 'PAYTERMS OVERRIDE');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I2', 'INVOICE_NUM');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I3', 'Required');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J2', 'INVOICE_DATE');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J3', 'Required');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K2', 'INVOICE_AMOUNT');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K3', 'Required');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L2', 'DESC - REFERENCE');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M2', 'DESC - EXT DESCRIPTION');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N2', 'DESC - SHIPMENT #');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O2', 'DESC - STAMP #');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P2', 'DESC - CHECK #');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P3', 'Optional');

		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q2', 'DESC - CHECK DATE');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R2', 'DESC - CHECK AMOUNT');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('S2', 'DESC - ETA DATE');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('S3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('T2', 'DESC - REFERENCE');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('T3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('U2', 'DESC - RUC #');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('U3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('V2', 'Extended Header Description - 11');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('V3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('W2', 'Extended Header Description - 12');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('W3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('X2', 'Extended Header Description - 13');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('X3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Y2', 'Extended Header Description - 14');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Y3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Z2', 'Extended Header Description - 15');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Z3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AA2', 'Extended Header Description - 16');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AA3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AB2', 'Extended Header Description - 17');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AB3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AC2', 'Extended Header Description - 18');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AC3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AD2', 'Extended Header Description - 19');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AD3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AE2', 'Extended Header Description - 20');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AE3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AF2', 'INVOICE_CURRENCY_CODE');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AF3', 'Required');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AG2', 'IMAGE/BATCH NUMBER');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AG3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AH2', 'Invoice Type Code');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AH3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AI2', 'Context Value');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AI3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AJ2', 'Requesting Location');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AJ3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AK2', 'Print Location');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AK3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AL2', 'KID CODE / ESR # / Addl Details');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AL3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AM2', 'LINE_TYPE_LOOKUP_CODE');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AM3', 'Required');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AN2', 'PO Number');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AN3', 'Required on PO');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AO2', 'PO Line Number');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AO3', 'Required on PO');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AP2', 'Quantity Invoiced');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AP3', 'Optional (PO Req\'d)');

		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AQ2', 'PO Unit Price');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AQ3', 'Optional (PO Req\'d)');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AR2', 'Unit of Measure');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AR3', 'Optional (PO Req\'d)');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AS2', 'LINE_AMOUNT');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AS3', 'Required');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AT2', 'TAX_CODE');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AT3', 'Required)');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AU2', 'LINE_DESCRIPTION - DOSSIER #');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AP3', 'Optional');


		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AV2', 'LINE_DESCRIPTION - DESTINATION');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AV3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AW2', 'LINE_DESCRIPTION - ORIGIN');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AW3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AX2', 'LINE_DESCRIPTION - DETAIL REFERENCE');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AX3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AY2', 'Extended Line Description - 5');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AY3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AZ2', 'Extended Line Description - 6');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AZ3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BA2', 'Extended Line Description - 7');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BA3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BB2', 'Extended Line Description - 8');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BB3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BC2', 'Extended Line Description - 9');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BC3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BD2', 'Extended Line Description - 10');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BD3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BE2', 'Extended Line Description - 11');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BE3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BF2', 'Extended Line Description - 12');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BF3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BG2', 'Extended Line Description - 13');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BG3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BH2', 'Extended Line Description - 14');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BH3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BI2', 'Extended Line Description - 15');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BI3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BJ2', 'Extended Line Description - 16');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BJ3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BK2', 'Extended Line Description - 17');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BK3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BL2', 'Extended Line Description - 18');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BL3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BM2', 'Extended Line Description - 19');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BM3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BN2', 'Extended Line Description - 20');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BN3', 'Optional');

		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BO2', 'Accounting Segment');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BO3', 'Required if Item and Ship-to are not provided on Non-PO');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BP2', 'ITEM_NUM');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BP3', 'Required if Accounting Segment is not provided on Non-PO');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BQ2', 'SHIP_TO_LOCATION_CODE');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BQ3', 'Required if Accounting Segment is not provided on Non-PO');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BR2', 'CHARGE_TO_LOCATION_CODE');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BR3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BS2', 'ATTRIBUTE_CATEGORY');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BS3', 'Required');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BT2', 'ATTRIBUTE1');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BT3', 'Required');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BU2', 'ATTRIBUTE2');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BU3', 'Required');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BV2', 'Project Name/#');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BV3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BW2', 'Project Task');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BW3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BX2', 'Project Expenditure Type');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BX3', 'Optional');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BY2', 'END_OF_ROW');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BY3', 'Required - Should be a constant " I "');

		##Border de las celdas
		$objPHPExcel-> getActiveSheet() -> getStyle('A2:BY2') -> getBorders() -> getAllBorders() -> setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel-> getActiveSheet() -> getStyle('A3:BY3') -> getBorders() -> getAllBorders() -> setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

		$worksheet = $objPHPExcel->getActiveSheet();

		##hago auto resize de todas las columnas.
		for ($i=0; $i < 77 ; $i++) {
			$objPHPExcel->getActiveSheet() -> getColumnDimensionByColumn($i) -> setAutoSize(true);
			$worksheet->setCellValueByColumnAndRow($i, 1, $i+1);
		}

		##Seteo de que row empieso a poner los datos.
		$row = 4;
		$nLineAmou = 0;
		foreach ($mDatos as $cKey => $cValue) {
			for ($i=0; $i<count($mDatos[$cKey]); $i++) {
				$worksheet->setCellValueByColumnAndRow(0,$row,$mDatos[$cKey][$i]['SOURCEXX']);
				$worksheet->setCellValueByColumnAndRow(1,$row,$mDatos[$cKey][$i]['COUNTRYC']);
				$worksheet->setCellValueByColumnAndRow(2,$row,$mDatos[$cKey][$i]['SUPPLNUM']);
				$worksheet->setCellValueByColumnAndRow(3,$row,$mDatos[$cKey][$i]['SUPPLSIT']);
				$worksheet->setCellValueByColumnAndRow(4,$row,$mDatos[$cKey][$i]['PAYGROUP']);
				$worksheet->setCellValueByColumnAndRow(5,$row,$mDatos[$cKey][$i]['PAYALONE']);
				$worksheet->setCellValueByColumnAndRow(6,$row,$mDatos[$cKey][$i]['PRIORITY']);
				$worksheet->setCellValueByColumnAndRow(7,$row,$mDatos[$cKey][$i]['PAYTERMS']);
				$worksheet->setCellValueByColumnAndRow(8,$row,$mDatos[$cKey][$i]['INVOICEN']);
				$worksheet->setCellValueByColumnAndRow(9,$row,$mDatos[$cKey][$i]['INVOICED']);
				$worksheet->setCellValueByColumnAndRow(10,$row,$mDatos[$cKey][$i]['INVOICEA']);
				$worksheet->setCellValueByColumnAndRow(11,$row,$mDatos[$cKey][$i]['DESCRE01']);
				$worksheet->setCellValueByColumnAndRow(12,$row,$mDatos[$cKey][$i]['DESCEXTD']);
				$worksheet->setCellValueByColumnAndRow(13,$row,$mDatos[$cKey][$i]['DESCSHIP']);
				$worksheet->setCellValueByColumnAndRow(14,$row,$mDatos[$cKey][$i]['DESCSTAM']);
				$worksheet->setCellValueByColumnAndRow(15,$row,$mDatos[$cKey][$i]['DESCCHEN']);
				$worksheet->setCellValueByColumnAndRow(16,$row,$mDatos[$cKey][$i]['DESCCHED']);
				$worksheet->setCellValueByColumnAndRow(17,$row,$mDatos[$cKey][$i]['DESCCHEA']);
				$worksheet->setCellValueByColumnAndRow(18,$row,$mDatos[$cKey][$i]['DESCETAD']);
				$worksheet->setCellValueByColumnAndRow(19,$row,$mDatos[$cKey][$i]['DESCRE02']);
				$worksheet->setCellValueByColumnAndRow(20,$row,$mDatos[$cKey][$i]['DESCRUCX']);
				$worksheet->setCellValueByColumnAndRow(21,$row,$mDatos[$cKey][$i]['EXTHED11']);
				$worksheet->setCellValueByColumnAndRow(22,$row,$mDatos[$cKey][$i]['EXTHED12']);
				$worksheet->setCellValueByColumnAndRow(23,$row,$mDatos[$cKey][$i]['EXTHED13']);
				$worksheet->setCellValueByColumnAndRow(24,$row,$mDatos[$cKey][$i]['EXTHED14']);
				$worksheet->setCellValueByColumnAndRow(25,$row,$mDatos[$cKey][$i]['EXTHED15']);
				$worksheet->setCellValueByColumnAndRow(26,$row,$mDatos[$cKey][$i]['EXTHED16']);
				$worksheet->setCellValueByColumnAndRow(27,$row,$mDatos[$cKey][$i]['EXTHED17']);
				$worksheet->setCellValueByColumnAndRow(28,$row,$mDatos[$cKey][$i]['EXTHED18']);
				$worksheet->setCellValueByColumnAndRow(29,$row,$mDatos[$cKey][$i]['EXTHED19']);
				$worksheet->setCellValueByColumnAndRow(30,$row,$mDatos[$cKey][$i]['EXTHED20']);
				$worksheet->setCellValueByColumnAndRow(31,$row,$mDatos[$cKey][$i]['INVOICEC']);
				$worksheet->setCellValueByColumnAndRow(32,$row,$mDatos[$cKey][$i]['IMAGEBAT']);
				$worksheet->setCellValueByColumnAndRow(33,$row,$mDatos[$cKey][$i]['INVOICET']);
				$worksheet->setCellValueByColumnAndRow(34,$row,$mDatos[$cKey][$i]['CONTEXTV']);
				$worksheet->setCellValueByColumnAndRow(35,$row,$mDatos[$cKey][$i]['REQUELOC']);
				$worksheet->setCellValueByColumnAndRow(36,$row,$mDatos[$cKey][$i]['PRINTLOC']);
				$worksheet->setCellValueByColumnAndRow(37,$row,$mDatos[$cKey][$i]['KIDCODEE']);
				$worksheet->setCellValueByColumnAndRow(38,$row,$mDatos[$cKey][$i]['DESCSTAI']);
				$worksheet->setCellValueByColumnAndRow(39,$row,$mDatos[$cKey][$i]['PONUMBER']);
				$worksheet->setCellValueByColumnAndRow(40,$row,$mDatos[$cKey][$i]['POLINENU']);
				$worksheet->setCellValueByColumnAndRow(41,$row,$mDatos[$cKey][$i]['QUANTITY']);
				$worksheet->setCellValueByColumnAndRow(42,$row,$mDatos[$cKey][$i]['POUNITPR']);
				$worksheet->setCellValueByColumnAndRow(43,$row,$mDatos[$cKey][$i]['UNITMEAU']);
				$worksheet->setCellValueByColumnAndRow(44,$row,$mDatos[$cKey][$i]['LINEAMOU']);
				$worksheet->setCellValueByColumnAndRow(45,$row,$mDatos[$cKey][$i]['TAXCODEX']);
				$worksheet->setCellValueByColumnAndRow(46,$row,$mDatos[$cKey][$i]['LINEDEDO']);
				$worksheet->setCellValueByColumnAndRow(47,$row,$mDatos[$cKey][$i]['LINEDEDD']);
				$worksheet->setCellValueByColumnAndRow(48,$row,$mDatos[$cKey][$i]['LINEDEOR']);
				$worksheet->setCellValueByColumnAndRow(49,$row,$mDatos[$cKey][$i]['LINEDEDE']);
				$worksheet->setCellValueByColumnAndRow(50,$row,$mDatos[$cKey][$i]['EXTLIN05']);
				$worksheet->setCellValueByColumnAndRow(51,$row,$mDatos[$cKey][$i]['EXTLIN06']);
				$worksheet->setCellValueByColumnAndRow(52,$row,$mDatos[$cKey][$i]['EXTLIN07']);
				$worksheet->setCellValueByColumnAndRow(53,$row,$mDatos[$cKey][$i]['EXTLIN08']);
				$worksheet->setCellValueByColumnAndRow(54,$row,$mDatos[$cKey][$i]['EXTLIN09']);
				$worksheet->setCellValueByColumnAndRow(55,$row,$mDatos[$cKey][$i]['EXTLIN10']);
				$worksheet->setCellValueByColumnAndRow(56,$row,$mDatos[$cKey][$i]['EXTLIN11']);
				$worksheet->setCellValueByColumnAndRow(57,$row,$mDatos[$cKey][$i]['EXTLIN12']);
				$worksheet->setCellValueByColumnAndRow(58,$row,$mDatos[$cKey][$i]['EXTLIN13']);
				$worksheet->setCellValueByColumnAndRow(59,$row,$mDatos[$cKey][$i]['EXTLIN14']);
				$worksheet->setCellValueByColumnAndRow(60,$row,$mDatos[$cKey][$i]['EXTLIN15']);
				$worksheet->setCellValueByColumnAndRow(61,$row,$mDatos[$cKey][$i]['EXTLIN16']);
				$worksheet->setCellValueByColumnAndRow(62,$row,$mDatos[$cKey][$i]['EXTLIN17']);
				$worksheet->setCellValueByColumnAndRow(63,$row,$mDatos[$cKey][$i]['EXTLIN18']);
				$worksheet->setCellValueByColumnAndRow(64,$row,$mDatos[$cKey][$i]['EXTLIN19']);
				$worksheet->setCellValueByColumnAndRow(65,$row,$mDatos[$cKey][$i]['EXTLIN20']);
				$worksheet->setCellValueByColumnAndRow(66,$row,$mDatos[$cKey][$i]['ACCOUNTS']);
				$worksheet->setCellValueByColumnAndRow(67,$row,$mDatos[$cKey][$i]['ITEMNUMX']);
				$worksheet->setCellValueByColumnAndRow(68,$row,$mDatos[$cKey][$i]['SHIPTOLO']);
				$worksheet->setCellValueByColumnAndRow(69,$row,$mDatos[$cKey][$i]['CHARGETO']);
				$worksheet->setCellValueByColumnAndRow(70,$row,$mDatos[$cKey][$i]['ATTRICAT']);
				$worksheet->getCellByColumnAndRow(71, $row)->setValueExplicit($mDatos[$cKey][$i]['ATTRIBU1'], PHPExcel_Cell_DataType::TYPE_STRING);
				$worksheet->getCellByColumnAndRow(72, $row)->setValueExplicit($mDatos[$cKey][$i]['ATTRIBU2'], PHPExcel_Cell_DataType::TYPE_STRING);
				$worksheet->setCellValueByColumnAndRow(73,$row,$mDatos[$cKey][$i]['PROJECTN']);
				$worksheet->setCellValueByColumnAndRow(74,$row,$mDatos[$cKey][$i]['PROJECTT']);
				$worksheet->setCellValueByColumnAndRow(75,$row,$mDatos[$cKey][$i]['PROJECTE']);
				$worksheet->setCellValueByColumnAndRow(76,$row,$mDatos[$cKey][$i]['ENDOFROW']);

				$nLineAmou += $mDatos[$cKey][$i]['LINEAMOU'];
				$row++;
			}
		}
		$worksheet->setCellValueByColumnAndRow(0,$row,'CONTROL');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFill()-> setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FBF807');

		$worksheet->setCellValueByColumnAndRow(2,$row,$nCanReg);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$row)->getFill()-> setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FBF807');

    $worksheet->setCellValueByColumnAndRow(8,$row,$nCanDoc);
    $objPHPExcel->getActiveSheet()->getStyle('I'.$row)->getFill()-> setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FBF807');

		$worksheet->setCellValueByColumnAndRow(44,$row,$nLineAmou);
    $objPHPExcel->getActiveSheet()->getStyle('AS'.$row)->getFill()-> setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FBF807');

		$worksheet->setCellValueByColumnAndRow(76,$row,'I');
    $objPHPExcel->getActiveSheet()->getStyle('BY'.$row)->getFill()-> setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FBF807');

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setTitle('AputFile '.$cNombreFile);

		// Download Excel 2007 file
		$cNomFile = $cNombreFile."_".$kUser."_".date("YmdHis").".xlsx";
		$cFile    = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/".$cNomFile;

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($cFile);

    if (file_exists($cFile)){
      chmod($cFile,intval($vSysStr['system_permisos_archivos'],8)); ?>
      <script languaje = "javascript">
        parent.fmpro3.location = 'frfacdow.php?cRuta=<?php echo $cNomFile ?>';
      </script>
      <?php
    } else {
      f_Mensaje(__FILE__,__LINE__,"Error al Generar el Archivo");
    }
	} else {
		f_Mensaje(__FILE__,__LINE__,"No se Generaron registros");
	}
}

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
?>

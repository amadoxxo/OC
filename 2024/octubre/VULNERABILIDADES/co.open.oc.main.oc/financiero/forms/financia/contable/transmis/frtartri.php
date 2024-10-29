<?php
/**
* GRABA Transmision Archivo TRIDENT
* @author Stephanie Correa <opencomex@opentecnologia.com.co>
* @package opencomex
*/

#Estableciendo que el tiempo de ejecucion no se limite
set_time_limit(0);
ini_set("memory_limit", "512M");

/**
 * Variables de control de errores
 * @var number
 */
$nSwitch = 0;

/**
 * Variable para almacenar los mensajes de error
 * @var string
 */
$cMsj = "";

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
 * Variable para el controlar el reinicio de conexion
 * @var integer
 */
$nCanReg = 0;


/**
  * Cantidad de Registros para reiniciar conexion
 */
define("_NUMREG_",100);

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
	  include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/uticonta.php");
	  include("{$OPENINIT['pathdr']}/opencomex/libs/php/utiprobg.php");

	  /**
	   * Buscando el ID del proceso
	   */
	  //f_Mensaje(__FILE__, __LINE__, "HOLA");
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
	  }else {
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
  include("../../../../libs/php/utility.php");
  include("../../../../config/config.php");
	include("../../../../libs/php/uticonta.php");
	include("../../../../../libs/php/utiprobg.php"); 
}

/**
 *  Cookie fija
 */
$kDf = explode("~", $_COOKIE["kDatosFijos"]);
$kMysqlHost = $kDf[0];
$kMysqlUser = $kDf[1];
$kMysqlPass = $kDf[2];
$kMysqlDb = $kDf[3];
$kUser = $kDf[4];
$kLicencia = $kDf[5];
$swidth = $kDf[6];

$cSystemPath = OC_DOCUMENTROOT;

if ($_SERVER["SERVER_PORT"] != "") {
	/*** Ejecutar proceso en Background ***/
	$cEjProBg = ($cEjProBg != "SI") ? "NO" : $cEjProBg;
} // fin if ($_SERVER["SERVER_PORT"] != "")

if ($_SERVER["SERVER_PORT"] == "") {
  $cComId  = $_POST['cComId'];
  $cComCod = $_POST['cComCod'];
  $cComDes = $_POST['cComDes'];
  $cUsrId  = $_POST['cUsrId'];
	$dDesde  = $_POST['dDesde'];
	$dHasta  = $_POST['dHasta'];
}  // fin del if ($_SERVER["SERVER_PORT"] == "")

$cFecha = explode("-", $dDesde);
$cPerAno = $cFecha[0];

$qLoad = "SELECT SQL_CALC_FOUND_ROWS comidxxx ";
$qLoad .= "FROM $cAlfa.fcod$cPerAno ";
$qLoad .= "WHERE ";
$qLoad .= "$cAlfa.fcod$cPerAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND ";
$qLoad .= "$cAlfa.fcod$cPerAno.comidxxx = \"$cComId\" AND ";
$qLoad .= "$cAlfa.fcod$cPerAno.regestxx = \"ACTIVO\" ";
$qLoad .= "LIMIT 0,1";
$xLoad = f_Mysql("SELECT", "", $qLoad, $xConexion01, "");

mysql_free_result($xLoad);

$xNumRows = mysql_query("SELECT FOUND_ROWS();");
$xRNR = mysql_fetch_array($xNumRows);
$nRegistros = $xRNR['FOUND_ROWS()'];
mysql_free_result($xNumRows);


if (substr($dDesde,0,4) == substr($dHasta,0,4)) {  // verifica que los años de incio y fin sean iguales.
  $cAno = substr($dDesde,0,4);
}else {
	$nSwitch = 1;
	f_Mensaje(__FILE__,__LINE__,"Error al Generar Archivo de Transmision Sistema Contable TRIDENT, Verifique que el Anio de las Fechas Desde y Hasta sea el Mismo");
}

if ($nSwitch == 0) {

	/*** GENERACION DE ARCHIVO xls DE CABECERA ***/
	$fCabXls = "ARCHIVO_TRIDENT_" . $kUser . "_" . date("YmdHis") . ".xls";

	if ($_SERVER["SERVER_PORT"] != "") {
		$fEdiXls = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $fCabXls;
	}else {
		$fEdiXls = "{$OPENINIT['pathdr']}/opencomex/" . $vSysStr['system_download_directory'] . "/" . $fCabXls;
	}

	if (file_exists($fEdiXls)) {
		unlink($fEdiXls);
	}

	$fpXls = fopen($fEdiXls, 'a+');

	/*** Titulos del XLS ***/
	$cData  = "<table border=\"1\">";
	$cData .= "<tr>";
	$cData .= "<td>ANDINOSLTD</td>";
	$cData .= "</tr>";
	$cData .= "<tr>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">CDGOCTA</td>"; //CUENTA CONTABLE
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">DESCMVK</td>"; //OBSERVACION DEL COMPROBANTE
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">VRDBMVK</td>"; //VALOR DEBITO
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">VRCRMVK</td>"; //VALOR CREDITO
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">NITBMVK</td>"; //NIT CLIENTE
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">CDGOINT</td>"; 
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">CDGOOFI</td>"; //
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">CDGOCCTO</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">VLRBASE</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">TIPOFAC</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">NROFAC</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">VCTOFAC</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">FECHMVK</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">FNTEMVK</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">CBTEMVK</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">BODEMVI</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">CDCPMVI</td>";
	$cData .= "<td style=\"width:200px;background-color:#99CCFF;font-weight:bold;\">CANEMVI</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">CANSMVI</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">NPEDMVI</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">PVTAMVI</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">PIVAMVI</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">PDSCMVI</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">NRRGMVK</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">USUARIO</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">HORAMVK</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">ORITMVK</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">BODETRF</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">DATOSFAC</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">PROYECTO</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">SERIAL</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">ITMASSM</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">REFDOCTO</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">VENDDOC</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">ZONADOC</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">LCRDDOC</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">CDGOPAY</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">IMPUESTO</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">CDGOFPAGO</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">COSTO</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">REMISION</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">CONSIGNACION</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">ANEXO</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">CONCEPTO</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">SECUENCIA</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">IDCHEQUE</td>";
	$cData .= "<td style=\"width:140px;background-color:#99CCFF;font-weight:bold;\">LOTE</td>";
	$cData .= "</tr>";
  fwrite($fpXls,$cData);
  
  /**
   * Buscando la informacion de la tabla fpar0115  DETALLES DE LA CTA PUC...
   */
  $qFpar115  = "SELECT ";
  $qFpar115 .= "CONCAT(pucgruxx, pucctaxx, pucsctax, pucauxxx, pucsauxx) AS pucid36x, ";
  $qFpar115 .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,IF($cAlfa.fpar0115.pucauxxx = \"00\" AND $cAlfa.fpar0115.pucsauxx = \"00\",\"\",$cAlfa.fpar0115.pucauxxx),IF($cAlfa.fpar0115.pucsauxx != \"00\",$cAlfa.fpar0115.pucsauxx,\"\")) AS pucidxxx, ";
  $qFpar115 .= "$cAlfa.fpar0115.pucterxx, ";
  $qFpar115 .= "$cAlfa.fpar0115.pucretxx, ";
  $qFpar115 .= "$cAlfa.fpar0115.pucdetxx, ";
  $qFpar115 .= "$cAlfa.fpar0115.pucdoscc, ";
  $qFpar115 .= "$cAlfa.fpar0115.pucdcs1x  ";
  $qFpar115 .= "FROM $cAlfa.fpar0115 ";
  $xFpar115 = f_MySql("SELECT","",$qFpar115,$xConexion01,"");
  // echo $qFpar115."~".mysql_num_rows($xFpar115)."<br><br>";
  $mCuenta = array();
  while($xR115 = mysql_fetch_array($xFpar115)) {
    $mCuenta["{$xR115['pucid36x']}"]['pucid36x'] = $xR115['pucid36x'];
    $mCuenta["{$xR115['pucid36x']}"]['pucidxxx'] = $xR115['pucidxxx'];
    $mCuenta["{$xR115['pucid36x']}"]['pucterxx'] = $xR115['pucterxx'];
    $mCuenta["{$xR115['pucid36x']}"]['pucretxx'] = $xR115['pucretxx'];
    $mCuenta["{$xR115['pucid36x']}"]['pucdetxx'] = $xR115['pucdetxx'];
    $mCuenta["{$xR115['pucid36x']}"]['pucdoscc'] = $xR115['pucdoscc'];
    $mCuenta["{$xR115['pucid36x']}"]['pucdcs1x'] = $xR115['pucdcs1x'];
  }

	/**
	* Buscando la informacion de la tabla fpar0117 COMPROBANTES
	*/
	$qFpar117  = "SELECT ";
	$qFpar117 .= "$cAlfa.fpar0117.comidxxx, ";
	$qFpar117 .= "$cAlfa.fpar0117.comcodxx, ";
	$qFpar117 .= "$cAlfa.fpar0117.comids1x, ";
	$qFpar117 .= "$cAlfa.fpar0117.comcods1, ";
	$qFpar117 .= "$cAlfa.fpar0117.comtipxx, ";
	$qFpar117 .= "$cAlfa.fpar0117.comtcoxx  ";
	$qFpar117 .= "FROM $cAlfa.fpar0117";
	$xFpar117 = f_MySql("SELECT","",$qFpar117,$xConexion01,"");
	// echo $qFpar117."~".mysql_num_rows($xFpar117)."<br><br>";
	$mComprobantes = array();
	while($xR117 = mysql_fetch_array($xFpar117)) {
		$mComprobantes["{$xR117['comidxxx']}~{$xR117['comcodxx']}"]['comids1x'] = $xR117['comids1x'];
		$mComprobantes["{$xR117['comidxxx']}~{$xR117['comcodxx']}"]['comcods1'] = $xR117['comcods1'];
		$mComprobantes["{$xR117['comidxxx']}~{$xR117['comcodxx']}"]['comtipxx'] = $xR117['comtipxx'];
		$mComprobantes["{$xR117['comidxxx']}~{$xR117['comcodxx']}"]['comtcoxx'] = $xR117['comtcoxx'];
	}

	/**
	* Buscando la informacion de la tabla SIAI0003 Usuarios
	*/
	$qSiai0003 = "SELECT ";
	$qSiai0003 .= "$cAlfa.SIAI0003.USRIDXXX, ";
	$qSiai0003 .= "$cAlfa.SIAI0003.USRNOMXX ";
	$qSiai0003 .= "FROM $cAlfa.SIAI0003";
	$xSiai0003 = f_MySql("SELECT", "", $qSiai0003, $xConexion01, "");
	//echo $qSiai0003."~".mysql_num_rows($xSiai0003)."<br><br>";
	$mUsuarios = array();
	while ($xR0003 = mysql_fetch_array($xSiai0003)) {
		$mUsuarios["{$xR0003['USRIDXXX']}"]['USRIDXXX'] = $xR0003['USRIDXXX'];
		$mUsuarios["{$xR0003['USRIDXXX']}"]['USRNOMXX'] = $xR0003['USRNOMXX'];
	}


	$cAno = substr($dDesde,0,4);
	$qCocDat  = "SELECT ";
	$qCocDat .= "$cAlfa.fcod$cAno.comidxxx, ";
	$qCocDat .= "$cAlfa.fcod$cAno.comcodxx, ";
	$qCocDat .= "$cAlfa.fcod$cAno.comcscxx, ";
	$qCocDat .= "$cAlfa.fcod$cAno.comcsc2x, ";
	$qCocDat .= "IF($cAlfa.fcod$cAno.comidxxx = \"F\",$cAlfa.fcod$cAno.comcscxx,$cAlfa.fcod$cAno.comcsc3x) comcsc3x, ";
	$qCocDat .= "$cAlfa.fcod$cAno.comseqxx, ";
	$qCocDat .= "$cAlfa.fcod$cAno.sccidxxx, ";
	$qCocDat .= "$cAlfa.fcod$cAno.teridxxx, ";
	$qCocDat .= "$cAlfa.fcod$cAno.terid2xx, ";
	$qCocDat .= "$cAlfa.fcod$cAno.comfecxx, ";
	$qCocDat .= "$cAlfa.fcod$cAno.comctocx, ";
  $qCocDat .= "$cAlfa.fcod$cAno.ctoidxxx, ";
  $qCocDat .= "$cAlfa.fcod$cAno.pucidxxx, ";
  $qCocDat .= "$cAlfa.fcod$cAno.pucidxxx AS pucid36x, ";
	$qCocDat .= "$cAlfa.fcod$cAno.comvlrxx, ";
	$qCocDat .= "$cAlfa.fcod$cAno.regestxx, ";
	$qCocDat .= "$cAlfa.fcod$cAno.reghcrex, ";
	$qCocDat .= "$cAlfa.fcod$cAno.ccoidxxx, ";
	$qCocDat .= "$cAlfa.fcod$cAno.comvlr01, ";
	$qCocDat .= "$cAlfa.fcod$cAno.comvlr02, ";
	$qCocDat .= "$cAlfa.fcod$cAno.comobsxx, ";
	$qCocDat .= "$cAlfa.fcod$cAno.comfecve, ";
	$qCocDat .= "$cAlfa.fcod$cAno.commovxx, ";
	$qCocDat .= "$cAlfa.fcod$cAno.comidcxx, ";
	$qCocDat .= "$cAlfa.fcod$cAno.comcodcx, ";
	$qCocDat .= "$cAlfa.fcod$cAno.comcsccx, ";
	$qCocDat .= "$cAlfa.fcod$cAno.comseqcx, ";
	$qCocDat .= "$cAlfa.fcod$cAno.comidc2x, ";
	$qCocDat .= "$cAlfa.fcod$cAno.comcodc2, ";
	$qCocDat .= "$cAlfa.fcod$cAno.comcscc2, ";
	$qCocDat .= "$cAlfa.fcod$cAno.comseqc2, ";
	$qCocDat .= "$cAlfa.fcod$cAno.docidxxx, ";
	$qCocDat .= "$cAlfa.fcod$cAno.regusrxx ";
	$qCocDat .= "FROM $cAlfa.fcod$cAno ";    
	$qCocDat .= "WHERE ";
	if($cComId != ""){
		$qCocDat .= "fcod$cAno.comidxxx = \"$cComId\" AND ";
	}
	if($cComCod != ""){
		$qCocDat .= "fcod$cAno.comcodxx = \"$cComCod\" AND ";
	}
	if($cUsrId != ""){
		$qCocDat .= "fcod$cAno.regusrxx = \"$cUsrId\" AND ";
	}
	$qCocDat .= "fcod$cAno.regestxx IN (\"ACTIVO\",\"INACTIVO\") AND ";
	$qCocDat .= "fcod$cAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" ";
	$qCocDat .= "ORDER BY fcod$cAno.comidxxx,fcod$cAno.comcodxx,fcod$cAno.comcscxx,ABS(fcod$cAno.comcsc2x),ABS(fcod$cAno.comseqxx) ";
	$xCocDat  = f_MySql("SELECT","",$qCocDat,$xConexion01,"");
		
	if(mysql_num_rows($xCocDat) > 0){
		if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" ) {
	    $cEjePro = 1;
      $strPost  = "cComId~".$cComId."|";
      $strPost .= "cComCod~".$cComCod."|";
      $strPost .= "cComDes~".$cComDes."|";
      $strPost .= "cUsrId~".$cUsrId."|";
      $strPost .= "cUsrNom~".$mUsuarios[$cUsrId]['USRNOMXX']."|";
      $strPost .= "dDesde~".$dDesde."|";
      $strPost .= "dHasta~".$dHasta;
									
			$vParBg['pbadbxxx'] = $cAlfa;                                         	//Base de Datos
			$vParBg['pbamodxx'] = "FACTURACION";                                  	//Modulo
			$vParBg['pbatinxx'] = "CONTABLETRIDENT";                                 //Tipo Interface
			$vParBg['pbatinde'] = "SISTEMA CONTABLE TRIDENT";                       //Descripcion Tipo de Interfaz
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
			$vParBg['regusrxx'] = $kUser;                                          	//Usuario que Creo Registro
								
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
			}else {
				$nSwitch = 1;
				for ($nR = 1; $nR < count($mReturnProBg); $nR++) {
					$cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
					$cMsj .= $mReturnProBg[$nR] . "\n";
				}
  			f_Mensaje(__FILE__, __LINE__, $cMsj . "Verifique.");
			}
		} // fin del if ($_SERVER["SERVER_PORT"] != "" && $_POST['cEjProBg'] == "SI" && $nSwitch == 0)
    $nCanReg = 0;
		while ($xRCD = mysql_fetch_array($xCocDat)){ 
			$nCanReg++;
			if (($nCanReg % _NUMREG_) == 0) { 
				$xConexion01 = fnReiniciarConexion(); 
      }
      /**
       * Buscando la informacion de la tabla fpar0115
       */
      $xRCD['pucdetxx'] = $mCuenta["{$xRCD['pucid36x']}"]['pucdetxx'];
      $xRCD['pucterxx'] = $mCuenta["{$xRCD['pucid36x']}"]['pucterxx'];

			/**
			 * Buscando la informacion de la tabla fpar0117
			 */
			$xRCD['comcods1'] = $mComprobantes["{$xRCD['comidxxx']}~{$xRCD['comcodxx']}"]['comcods1'];
				
			/*** Inicializando Variables ***/
			if($xRCD['pucdetxx']=='P' || $xRCD['pucdetxx'] == 'C'){         // NIT PROVEEDOR O CLIENTE
				$cDet006 = $xRCD['teridxxx'];
			}else{
				$cDet006 = '';
			}

			if($xRCD['pucdetxx'] == 'D'){									// CENTRO DE COSTO DE DOCUMENTO
				$cDet007 = $xRCD['ccoidxxx'];
				$cDet008 = $xRCD['docidxxx'];
			}else{
				$cDet007 = '';
				$cDet008 = '';
			}

			if ($xRCD['pucterxx'] == 'R') {      							// RETENCION VALOR
				$cDet009 = $xRCD['comvlrxx'];
			} else {
				$cDet009 = 0;
			}
			//CLIENTE O PROVEEDOR TIPO DE DOCUMENTO NO CRUCE
			if (($xRCD['comidxxx'] != $xRCD['comidcxx'] ||
				$xRCD['comcodxx'] != $xRCD['comcodcx'] ||
				$xRCD['comcscxx'] != $xRCD['comcsccx']) && ($xRCD['pucdetxx'] == "C" || $xRCD['pucdetxx'] == "P")) {
				$cDet010 = $xRCD['comidcxx'];
				$cDet011 = $xRCD['comcsccx'];
			}else{
				$cDet010 = '';
				$cDet011 = '';
			}
			//CLIENTE O PROVEEDOR TIPO DE DOCUMENTO QUE CRUCE FECHA DE VENCIMIENTO
			if (($xRCD['pucdetxx'] == "C" || $xRCD['pucdetxx'] == "P") &&
			   !($xRCD['comidxxx'] == $xRCD['comidcxx'] && $xRCD['comcodxx'] == $xRCD['comcodcx'] 
				 & $xRCD['comcscxx'] == $xRCD['comcsccx'])) {
																									
				// Busco los datos del documento cruce
				$nEncontro = 0;
				for ($nA = $cAno; $nA >= $vSysStr['financiero_ano_instalacion_modulo']; $nA--) {
					$qDocCru = "SELECT comfecxx, comfecve ";
					$qDocCru .= "FROM $cAlfa.fcod$nA ";
					$qDocCru .= "WHERE ";
					$qDocCru .= "comidxxx = \"{$xRCD['comidcxx']}\" AND ";
					$qDocCru .= "comcodxx = \"{$xRCD['comcodcx']}\" AND ";
					$qDocCru .= "comcscxx = \"{$xRCD['comcsccx']}\" AND ";
					$qDocCru .= "pucidxxx = \"{$xRCD['pucidxxx']}\" AND ";
					$qDocCru .= "teridxxx = \"{$xRCD['teridxxx']}\" LIMIT 0,1";
					$xDocCru = f_MySql("SELECT", "", $qDocCru, $xConexion01, "");
					//f_Mensaje(__FILE__,__LINE__,$qDocCru." ~ ".mysql_num_rows($xDocCru));
					if (mysql_num_rows($xDocCru) > 0) {
						$nA = $vSysStr['financiero_ano_instalacion_modulo'];
						$nEncontro = 1;
						$vDocCru = mysql_fetch_array($xDocCru);
						$cDet012 = str_pad($vDocCru['comfecxx'], 8, "0", STR_PAD_LEFT); /* Fecha Vencimiento Doc. Cruce */
					}
				}
				if ($nEncontro == 0) {
					$cDet012 = str_pad($xRCD['comfecxx'], 8, "0", STR_PAD_LEFT); /* Fecha Vencimiento Doc. Cruce */
				}
				//Fin Busco los datos del documento cruce
			} else {
				$cDet012 = str_pad($xRCD['comfecve'], 8, "0", STR_PAD_LEFT); /* Fecha Vencimiento Doc. Cruce */
			}
				
			$cDet026 = $xRCD['reghcrex'];								 
			$cDet026 = round(microtime(true) * 1000);		 //HORA EN MILISEGUNDOS
						
			//Lleno los datos en la tabla excel
      $cData  = '<tr>';
			$cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, $xRCD['pucidxxx']) 																.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, $xRCD['comobsxx']) 																.'</td>';
      $cData .= '<td style="mso-number-format:\'0\'">'.number_format(($xRCD['commovxx'] == 'D' ? $xRCD['comvlrxx'] : 0),0,'.','')         .'</td>';
      $cData .= '<td style="mso-number-format:\'0\'">'.number_format(($xRCD['commovxx'] == 'C' ? $xRCD['comvlrxx'] : 0),0,'.','')         .'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, $xRCD['teridxxx']) 																.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, $cDet006) 																				.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, $cDet007) 																				.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, $cDet008) 																				.'</td>';
      $cData .= '<td style="mso-number-format:\'0\'">'.number_format($cDet009,0,'.','') 																				          .'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, $cDet010) 																				.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, $cDet011) 																				.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, $cDet012) 																				.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, str_pad($xRCD['comfecxx'], 8, "0", STR_PAD_LEFT)) .'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, $xRCD['comcods1']) 																.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, $xRCD['comcscxx']) 																.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, '') 																							.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, '') 																							.'</td>';
      $cData .= '<td style="mso-number-format:\'0\'">'.str_replace($cBuscar, $cReempl, 0) 																								.'</td>';
      $cData .= '<td style="mso-number-format:\'0\'">'.str_replace($cBuscar, $cReempl, 0) 																								.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, '') 																							.'</td>';
      $cData .= '<td style="mso-number-format:\'0\'">'.str_replace($cBuscar, $cReempl, 0) 																								.'</td>';
      $cData .= '<td style="mso-number-format:\'0\'">'.str_replace($cBuscar, $cReempl, 0) 																								.'</td>';
      $cData .= '<td style="mso-number-format:\'0\'">'.str_replace($cBuscar, $cReempl, 0) 																								.'</td>';
      $cData .= '<td style="mso-number-format:\'0\'">'.str_replace($cBuscar, $cReempl, 0) 																								.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, $mUsuarios["{$xRCD['regusrxx']}"]['USRNOMXX']) 		.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, $cDet026) 																				.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, $xRCD['comcods1']) 																.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, '') 																							.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, '') 																							.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, '') 																							.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, '') 																							.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, '') 																							.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, '') 																							.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, '') 																							.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, '') 																							.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, '') 																							.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, '') 																							.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, '') 																							.'</td>';
      $cData .= '<td style="mso-number-format:\'0\'">'.str_replace($cBuscar, $cReempl, '') 																							  .'</td>';
      $cData .= '<td style="mso-number-format:\'0\'">'.str_replace($cBuscar, $cReempl, '') 																							  .'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, '') 																							.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, '') 																							.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, '') 																							.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, '') 																							.'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, '') 																							.'</td>';
      $cData .= '<td style="mso-number-format:\'0\'">'.str_replace($cBuscar, $cReempl, '') 																							  .'</td>';
      $cData .= '<td style="mso-number-format:\'\@\'">'.str_replace($cBuscar, $cReempl, '') 																							.'</td>';
			$cData .= '</tr>';
			fwrite($fpXls,$cData);				
    }
    $cData = "</table>";
    fwrite($fpXls,$cData);
    fclose($fpXls);
	}else{
    $nSwitch = 1;
    $cMsj .= "No Se Encontraron Registros.\n";
	}	
}

if($nSwitch == 0){
  if (file_exists($fEdiXls)) {	
    // Obtener la ruta absoluta del archivo
    $cAbsolutePath = realpath($fEdiXls);
    $cAbsolutePath = substr($cAbsolutePath,0,strrpos($cAbsolutePath, '/'));
    
    if (in_array(realpath($cAbsolutePath), $vSystem_Path_Authorized)) {
      chmod($fEdiXls, intval($vSysStr['system_permisos_archivos'], 8));
      $cDownLoadFilename = $cDownLoadFilename !== null ? $cDownLoadFilename : basename($fEdiXls);
      if ($cEjePro == 0){
        if ($_SERVER["SERVER_PORT"] != "") {
          header('Content-Description: File Transfer');
          header('Content-Type: application/octet-stream');
          header('Content-Disposition: attachment; filename=' . $cDownLoadFilename);
          header('Content-Transfer-Encoding: binary');
          header('Expires: 0');
          header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
          header('Pragma: public');
          header('Content-Length: ' . filesize($fEdiXls));
          ob_clean();
          flush();
          readfile($fEdiXls);
          exit;
        } 
      }
      if($_SERVER["SERVER_PORT"] == ""){
        $cNomArc = $fCabXls;
      }		
    }
  }else {
    $nSwitch = 1;
    if ($_SERVER["SERVER_PORT"] != "") {
      f_Mensaje(__FILE__, __LINE__, "No se encontro el archivo $fEdiXls, Favor Comunicar este Error a openTecnologia S.A.");
    }else {
      $cMsj .= "No se encontro el archivo $fEdiXls, Favor Comunicar este Error a openTecnologia S.A.";
    }
  }
}else{
  if ($_SERVER["SERVER_PORT"] != "") {
    f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique");
  }
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


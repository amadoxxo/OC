<?php
/**
 * Graba Nuevo reply Sobre un Ticket Creado Previamente
 * Este programa permite Grabar Reply Sobre un Ticket Creado Previamente
 * @author Yulieth Campos <ycampos@opentecnologia.com.co>
 * @package openComex
 */
/** Libreria para encriptar y desencriptar password * */
include('../../../../class/EnDecryptText.php');
$EnDecryptText = new EnDecryptText();

include("../../../../config/config.php");
include("../../../../libs/php/utility.php");
include("../../../../libs/php/uticemax.php");


switch($cAlfa){
  case "DHLXXXXX":
  case "TEDHLXXXXX":
  case "DEDHLXXXXX";
    include("../../../../ws/dhlxxxxx/utiwsout.php");
    include("../../../../libs/php/uticdhl.php");
  break;
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


/**
 * Cargando los POST con los Valores que llegan por URL
 */
/* $_COOKIE['kModo'] = $gModo;
  $cAlfa = $gAlfa;
  $_POST['cFrom']    = $gFrom;
  $_POST['cCc']      = $gCc;
  $_POST['cTipId']   = $gTipId;
  $_POST['cStsId']   = $gStsId;
  $_POST['cPriId']   = $gPriId;
  $_POST['cTicCon']  = $gTicCon;
  $_POST['dRegFcre'] = $gRegFcre;
  $_POST['hRegHcre'] = $gRegHcre;
  $_POST['dRegFmod'] = $gRegFmod;
  $_POST['hRegHmod'] = $gRegHmod;
  $_POST['cRegEst']  = $gResEst;
  $_POST['cDocId']   = $gDocId;
  $_POST['cDocSuf']  = $gDocSuf;
  $_POST['cSucId']   = $gSucId;
  $_POST['cDocTip']  = $gDocTip;
  $_POST['cUsrId']   = $gUsrId;
  $_POST['cTicId']   = $gTicId;
  $_POST['cTipRes']  = $gTipRes;
  $_POST['cCliId']   = $gCliId;
  $_POST['cCadena']  = (substr($gCadena,(strlen($gCadena)-1),strlen($gCadena)) == "~") ? substr($gCadena,0,(strlen($gCadena)-1)) : $gCadena;

 */
$_COOKIE['kModo'] = $_POST['kModo'];
$cAlfa = $_POST['cAlfa'];
$_POST['cCadena'] = (substr($_POST['cCadena'], (strlen($_POST['cCadena']) - 1), strlen($_POST['cCadena'])) == "~") ? substr($_POST['cCadena'], 0, (strlen($_POST['cCadena']) - 1)) : $_POST['cCadena'];

include("{$OPENINIT['pathdr']}/opencomex/config/config.php");
$xConexion01 = mysql_connect(OC_SERVER, OC_USERROBOT, OC_PASSROBOT);

$nSwitch = 0; // Switch para Vericar la Validacion de Datos
$cMsj = "";

/**
 * Variables para reemplazar caracteres especiales
 * @var array
 */
$cBuscar = array('"',"'",chr(13),chr(10),chr(27),chr(9),chr(11));
$cReempl = array("'","\'"," "," "," "," "," ");

/**
	* Reemplazando Caracteres Especiales en el Campo de Con copia a
  */
$_POST['cCc'] = str_replace($cBuscar,$cReempl,$_POST['cCc']);


/**
 * Primero valido los datos que llegan por metodo POST.
 */
switch ($_COOKIE['kModo']) {
  case "NUEVO":
  case "EDITAR":

    /**
     * Validando Licencia
     */
    $nLic = f_Licencia();
    if ($nLic == 0) {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
      $cMsj .= "Error grave de Seguridad otro usuario ingreso con su clave.\n";
    }

    /**
     * Validando que el tipo o Causal Escogido Exista
     */
    if ($_POST['cTipId'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
      $cMsj .= "El Tipo No Puede ser Vacio.\n";
    } else {
      $qDatTip = "SELECT * ";
      $qDatTip .= "FROM $cAlfa.work0001 ";
      $qDatTip .= "WHERE ";
      $qDatTip .= "tipidxxx = \"{$_POST['cTipId']}\" AND ";
      $qDatTip .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
      $xDatTip = mysql_query($qDatTip, $xConexion01);
      //$xDatTip  = f_MySql("SELECT","",$qDatTip,$xConexion01,"");
      //f_Mensaje(__FILE__,__LINE__,$qDatTip."~".mysql_num_rows($xDatTip));
      if (mysql_num_rows($xDatTip) == 0) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
        $cMsj .= "El Tipo Escogido No Existe en la Base de Datos.\n";
      } else {
        $vDatTip = mysql_fetch_array($xDatTip);
      }
    }

    /**
     * Validando que el Status Escogido Exista
     */
    if ($_POST['cStsId'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
      $cMsj .= "El Status No Puede ser Vacio.\n";
    } else {
      $qDatSts = "SELECT * ";
      $qDatSts .= "FROM $cAlfa.work0002 ";
      $qDatSts .= "WHERE ";
      $qDatSts .= "stsidxxx = \"{$_POST['cStsId']}\" AND ";
      $qDatSts .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
      $xDatSts = mysql_query($qDatSts, $xConexion01);
      //$xDatSts  = f_MySql("SELECT","",$qDatSts,$xConexion01,"");
      //f_Mensaje(__FILE__,__LINE__,$qDatSts."~".mysql_num_rows($xDatSts));
      if (mysql_num_rows($xDatSts) == 0) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
        $cMsj .= "El Status Escogido No Existe en la Base de Datos.\n";
      } else {
        $vDatSts = mysql_fetch_array($xDatSts);
      }
    }

    /**
     * Validando que la Prioridad Escogida Exista
     */
    if ($_POST['cPriId'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
      $cMsj .= "La Prioridad No Puede ser Vacia.\n";
    } else {
      $qDatPri = "SELECT * ";
      $qDatPri .= "FROM $cAlfa.work0003 ";
      $qDatPri .= "WHERE ";
      $qDatPri .= "priidxxx = \"{$_POST['cPriId']}\" AND ";
      $qDatPri .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
      $xDatPri = mysql_query($qDatPri, $xConexion01);
      //$xDatPri  = f_MySql("SELECT","",$qDatPri,$xConexion01,"");
      //f_Mensaje(__FILE__,__LINE__,$qDatPri."~".mysql_num_rows($xDatPri));
      if (mysql_num_rows($xDatPri) == 0) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
        $cMsj .= "La Prioridad Escogida No Existe en la Base de Datos.\n";
      } else {
        $vDatPri = mysql_fetch_array($xDatPri);
      }
    }

    /**
     * Validando que se haya seleccionado como Minimo un Responsable
     */
    if ($_POST['cCadena'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
      $cMsj .= "Debe Seleccionar al Menos un Responsable.\n";
    }

    /**
     * Validando que se haya digitado el Contenido
     */
    if ($_POST['cTicCon'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
      $cMsj .= "Debe Digitar Contenido.\n";
    }

    /**
     * Validando que exista el Ticket y que el Estado sea ABIERTO para permitir hacer un Reply
     * de lo contrario no se permitira guardar el nuevo reply.
     */
    $qEstTic = "SELECT * ";
    $qEstTic .= "FROM $cAlfa.work1001 ";
    $qEstTic .= "WHERE ";
    $qEstTic .= "ticidxxx = \"{$_POST['cTicId']}\" LIMIT 0,1 ";
    $xEstTic = mysql_query($qEstTic, $xConexion01);
    if (mysql_num_rows($xEstTic) == 0) {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
      $cMsj .= "No Existe el Ticket {$_POST['cTicId']} en la Base de Datos.\n";
    } else {
      $vEstTic = mysql_fetch_array($xEstTic);
      if ($vEstTic['stsidxxx'] == "101" && $_POST['cStsId'] == "101") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
        $cMsj .= "No se puede hacer Nuevo Reply porque el Ticket se Encuentra Cerrado.\n";
      } elseif ($vEstTic['stsidxxx'] == "101" && $_POST['cStsId'] != "101") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
        $cMsj .= "No Puede Cambiar el Status Ticket porque se Encuentra Cerrado.\n";
      }

      if ($vEstTic['regestxx'] == "INACTIVO") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
        $cMsj .= "No se puede hacer Nuevo Reply porque el Ticket se Encuentra Inactivo.\n";
      }
    }

    /**
     * Validando extension permitida del archivo
     */
    if($_FILES['cAdjunto']['name'] != ""){
      $vExtPer = ["application/zip","application/x-zip-compressed","multipart/x-zip",
                  "application/pdf","application/vnd.ms-excel","application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                  "application/msword","application/vnd.openxmlformats-officedocument.wordprocessingml.document",
                  "image/png","image/jpg","image/jpeg"];
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mime = finfo_file($finfo, $_FILES['cAdjunto']['tmp_name']);
      if (!in_array($mime, $vExtPer)) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
        $cMsj .= "Archivo No Permitido.\n";
      }
      finfo_close($finfo);
    }

    /**
     * Validando la Fecha de Creacion.
     */
    if (empty($_POST['dRegFcre'])) {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
      $cMsj .= "La Fecha de Creacion No Puede Ser Vacia.\n";
    }

    /**
     * Validando la Hora de Creacion.
     */
    if (empty($_POST['hRegHcre'])) {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
      $cMsj .= "La Hora de Creacion No Puede Ser Vacia.\n";
    }

    /**
     * Validando la Fecha de Modificacion.
     */
    if (empty($_POST['dRegFmod'])) {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
      $cMsj .= "La Fecha de Modificacion No Puede Ser Vacia.\n";
    }
    if ($_POST['dRegFmod'] != f_Fecha()) {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
      $cMsj .= "La Fecha de Modificacion Debe Ser la Actual.\n";
    }

    /**
     * Validando la Hora de Modificacion.
     */
    if (empty($_POST['hRegHmod'])) {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
      $cMsj .= "La Hora de Modificacion No Puede Ser Vacia.\n";
    }

    /**
     * Validando el Estado
     */
    if (empty($_POST['cRegEst'])) {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
      $cMsj .= "El Campo Estado No Puede Estar Vacio.\n";
    }

    if ($_POST['cRegEst'] != "ACTIVO") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
      $cMsj .= "El Estado Debe Estar ACTIVO.\n";
    }
    break;
  default:
    $nSwitch = 1;
    $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
    $cMsj .= "El Modo de Grabado No Es Correcto.\n";
    break;
}
/**
 * Fin de Primero valido los datos que llegan por metodo POST.
 */
if ($nSwitch == 0) {
  switch ($_COOKIE['kModo']) {
    /**
     * Consulto el Csc del Ultimo Ticket para guardar el Nuevo
     */
    case "EDITAR":
      $qTicket = "SELECT * ";
      $qTicket .= "FROM $cAlfa.work1001 ";
      $qTicket .= "WHERE ";
      $qTicket .= "ticidxxx = \"{$_POST['cTicId']}\" AND ";
      $qTicket .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
      $xTicket = mysql_query($qTicket, $xConexion01);
      $vTicket = mysql_fetch_array($xTicket);

      if ($vTicket['stsidxxx'] <> "101" && $_POST['cStsId'] == "101") {
        $vTicket['regfmodx'] = date('Y-m-d');
      } elseif ($vTicket['stsidxxx'] <> "101" && $_POST['cStsId'] <> "101") {
        $vTicket['regfmodx'] = "";
      }

      $qReply = "SELECT MAX(ABS(repidxxx)) AS repidxxx ";
      $qReply .= "FROM $cAlfa.work1002 ";
      $qReply .= "WHERE ";
      $qReply .= "ticidxxx = \"{$_POST['cTicId']}\" ";
      $xReply = mysql_query($qReply, $xConexion01);
      //f_Mensaje(__FILE__,__LINE__,$qReply."~".mysql_num_rows($xReply));
      $vReply = mysql_fetch_array($xReply);
      $vReply['repidxxx'] = $vReply['repidxxx'] + 1;


			$cModoRel = "CERRADO";

      break;
  }

	/****************************************************************************/
	switch ($_POST['cDocTip']) {
		case "IMPORTACION":
			$qTramite = "SELECT ";
			$qTramite .= "$cAlfa.SIAI0200.DOIPEDXX,";
			$qTramite .= "$cAlfa.SIAI0200.DGEDTXXX,";
			$qTramite .= "$cAlfa.SIAI0200.CLIIDXXX,";
			$qTramite .= "$cAlfa.SIAI0200.USRID4XX,";
			$qTramite .= "$cAlfa.SIAI0200.DOIFENRA,";
			$qTramite .= "$cAlfa.SIAI0200.DOIFENFA,";
			$qTramite .= "$cAlfa.SIAI0200.DOIFENTR,";
			$qTramite .= "$cAlfa.SIAI0200.DOIFINXX,";
			$qTramite .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X)) AS CLINOMXX "; //Nombre Cliente
			$qTramite .= "FROM $cAlfa.SIAI0200 ";
			$qTramite .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.SIAI0200.CLIIDXXX = $cAlfa.SIAI0150.CLIIDXXX ";
			$qTramite .= "WHERE ";
			$qTramite .= "$cAlfa.SIAI0200.DOIIDXXX = \"{$_POST['cDocId']}\" AND ";
			$qTramite .= "$cAlfa.SIAI0200.DOISFIDX = \"{$_POST['cDocSuf']}\" AND ";
			$qTramite .= "$cAlfa.SIAI0200.ADMIDXXX = \"{$_POST['cSucId']}\" LIMIT 0,1 ";
		break;
		case "EXPORTACION":
			$qTramite = "SELECT ";
			$qTramite .= "$cAlfa.siae0199.dexpedxx AS DOIPEDXX,";
			$qTramite .= "$cAlfa.siae0199.dexdtrxx AS DGEDTXXX,";
			$qTramite .= "$cAlfa.siae0199.cliidxxx AS CLIIDXXX,";
			$qTramite .= "$cAlfa.siae0199.usrid4xx AS USRID4XX,";
			$qTramite .= "$cAlfa.siae0199.dexfenra AS DOIFENRA,";
			$qTramite .= "$cAlfa.siae0199.dexfentr AS DOIFENFA,";
			$qTramite .= "$cAlfa.siae0199.dexfefac AS DOIFENTR,";
			$qTramite .= "$cAlfa.siae0199.dexffinx AS DOIFINXX,";
			$qTramite .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X)) AS CLINOMXX "; //Nombre Cliente
			$qTramite .= "FROM $cAlfa.siae0199 ";
			$qTramite .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.siae0199.cliidxxx = $cAlfa.SIAI0150.CLIIDXXX ";
			$qTramite .= "WHERE ";
			$qTramite .= "$cAlfa.siae0199.dexidxxx = \"{$_POST['cDocId']}\" AND ";
			$qTramite .= "$cAlfa.siae0199.admidxxx = \"{$_POST['cSucId']}\" LIMIT 0,1 ";
		break;
	}

	if ($_POST['cStsId'] == "101" && $vDatTip['tipaacta'] == "SI") {
		// Configuracion tipo de tickets.
		$vCon = f_Explode_Array($vDatTip['tipcaact'], "|", "~");

		$mQuerySIAI0200 = array();
		$mQueryZdhl0004 = array();
		$mQuerySiae0199 = array();

		for($i = 0; $i < count($vCon); $i++) {
			// Si la estructura tiene 6 posiciones, preparo las variables para guardar fecha y hora. Sino, para guardar un campo con valor especifico.
			if (count($vCon[$i]) == 6) {
				$dFecha				= $vCon[$i][0];
				$hHora				= $vCon[$i][1];
				$cTipoAccion	= $vCon[$i][2];
				$cTipoDoc			= $vCon[$i][3];
				$cTablaAct		= $vCon[$i][4];
        $cModo				= $vCon[$i][5];
        $cCampo				= $vCon[$i][0];
			} else {
				$cCampo				= $vCon[$i][0];
				$cTipoAccion	= $vCon[$i][1];
				$cTipoDoc			= $vCon[$i][2];
				$cTablaAct		= $vCon[$i][3];
				$cModo				= $vCon[$i][4];
			}

			if ($_POST['cDocTip'] != $cTipoDoc || $cModoRel != $cModo) continue;

			// Tipo de accion.
			switch ($cTipoAccion) {
				case 'LIMPIAR':
					$dFec = '0000-00-00';
					$tHor = '00:00:00';
					$cUse = '';
				break;
				case 'ACTUALIZAR':

          /**
           * Si el campo corresponde con la Fecha de Entrega Carpeta a Facturacion se debe hacer Llamado
           * al ws de Actualizacion FILE JBA
           */
          if(($dFecha == "DOIFENCA" && $hHora == "DOIHENCA") || ($dFecha == "docfefxx" && $hHora == "DOIHENCA") ){

            /**
             * Instanciando Objeto para invocar metodo
             */
            $objProcesosFileJBA = new cProcesosFileJBA();

            $nSwitchWs = 0; $cMsjWs = "";
            /**
             * Preparando Vector para enviar datos al metodo
             */
            $vParametros['ADMIDXXX'] = $_POST['cSucId'];  // Sucursal Do
            $vParametros['DOIIDXXX'] = $_POST['cDocId'];  // Numero de Do
            $vParametros['DOISFIDX'] = $_POST['cDocSuf']; // Sufijo Do
            $vParametros['TIPOOPEX'] = $_POST['cDocTip']; // Tipo operación IMPORTACION/EXPORTACION
            $mReturnfnActualizarFileJBA = $objProcesosFileJBA->fnActualizarFileJBA($vParametros);
            if($mReturnfnActualizarFileJBA[0] == "false"){
              $nSwitchWs = 1;
              $cMsjWs .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
              for($nR=1;$nR<count($mReturnfnActualizarFileJBA);$nR++){
                $cMsjWs .= "$mReturnfnActualizarFileJBA[$nR]\n";
              }
              if($nSwitchWs == 1){
                f_Mensaje(__FILE__,__LINE__,$cMsjWs);
              }
            }
          }

					$dFec = date("Y-m-d");
					$tHor = date("H:i:s");
					$cUse = $kUser;
				break;
			}

      if($nSwitch == 0 && $nSwitchWs == 0){
  			// Si la estructura tiene 6 posiciones, guardo fecha y hora.
  			if (count($vCon[$i]) == 6) {
  				switch ($cTablaAct) {
  					case 'SIAI0200':
              array_push($mQuerySIAI0200, "{$dFecha} = \"{$dFec}\", {$hHora} = \"{$tHor}\" ");
              switch($cCampo) {
                case 'DOIFENRA':
                  array_push($mQuerySIAI0200, array('NAME'=>"DOIUSERA", 'VALUE'=>$cUse, 'CHECK'=>'NO'));
                break;
                case 'DOIFENCA':
                  array_push($mQuerySIAI0200, array('NAME'=>"DOIUSADM", 'VALUE'=>$cUse, 'CHECK'=>'NO'));
                break;
              }
  					break;
  					case 'siae0199':
  						array_push($mQuerySiae0199, "{$dFecha} = \"{$dFec}\", {$hHora} = \"{$tHor}\" ");
  					break;
  					case 'zdhl0004':
  						array_push($mQueryZdhl0004, "{$dFecha} = \"{$dFec}\", {$hHora} = \"{$tHor}\" ");
  					break;
  				}
  			} else {
  				// Si tiene 5 posiciones, guardo lo que corresponda en el campo especifico.
  				switch ($cTablaAct) {
  					case 'SIAI0200':
  						switch($cCampo) {
  							case 'DOIREDGC':
  								array_push($mQuerySIAI0200, "{$cCampo} = \"{$cUse}\" ");
                break;
  						}
  					break;
  					case 'siae0199':
  					break;
  					case 'zdhl0004':
  					break;
  				}
  			}
  		}

  		if (!empty($mQuerySIAI0200)) {
  			$qUpdateSiai0200  = "UPDATE $cAlfa.SIAI0200 ";
  			$qUpdateSiai0200 .= "SET ";
  			$qUpdateSiai0200 .= implode(', ', $mQuerySIAI0200);
  			$qUpdateSiai0200 .= "WHERE ";
  			$qUpdateSiai0200 .= "ADMIDXXX = \"{$_POST['cSucId']}\" AND ";
  			$qUpdateSiai0200 .= "DOIIDXXX = \"{$_POST['cDocId']}\" AND ";
  			$qUpdateSiai0200 .= "DOISFIDX = \"{$_POST['cDocSuf']}\" ";

  			if (!mysql_query($qUpdateSiai0200)) {
  				$nSwitch = 1;
  				$cMsj .= "Ocurrio un error al Actualizar Fechas Automaticas \n";
  			}else{
          switch ($cAlfa) {
            case "DHLXXXXX":
            case "TEDHLXXXXX":
            case "DEDHLXXXXX":
              if($cTipoAccion == "ACTUALIZAR" && $dFecha == "DOIDEDEP" && $hHora == "DOIHDCAR") {
                $qConDo  = "SELECT ";
                $qConDo .= "DOIEC789,";
                $qConDo .= "DOIDEDEP,";
                $qConDo .= "DOIHDCAR,";
                $qConDo .= "DOIEEI20,";
                $qConDo .= "DOIREFCW ";
                $qConDo .= "FROM $cAlfa.SIAI0200 ";
                $qConDo .= "WHERE ";
                $qConDo .= "DOIIDXXX = \"{$_POST['cDocId']}\" AND ";
                $qConDo .= "DOISFIDX = \"{$_POST['cDocSuf']}\" AND ";
                $qConDo .= "ADMIDXXX = \"{$_POST['cSucId']}\" LIMIT 0,1 ";
                $xConDo  = mysql_query($qConDo, $xConexion01);
                $vConDo  = mysql_fetch_array($xConDo);
        
                if($vConDo['DOIEC789'] == "0000-00-00 00:00:00" && $vConDo['DOIDEDEP'] != "" && 
                  $vConDo['DOIDEDEP'] != "0000-00-00" && $vConDo['DOIHDCAR'] != "" && 
                  $vConDo['DOIHDCAR'] != "00:00:00" && $vConDo['DOIEEI20'] != "SI" && 
                  $vConDo['DOIREFCW'] != ""){
                    
                  $vParametros = array();
                  $vParametros['ADMIDXXX'] = $_POST['cSucId'];
                  $vParametros['DOIIDXXX'] = $_POST['cDocId'];
                  $vParametros['DOISFIDX'] = $_POST['cDocSuf'];
            
                  $oProcesosCargoWise = new cProcesosCargoWise();
                  $mReturnProcesosCargoWise = $oProcesosCargoWise->fnGenerarInterfaceCDZ789DespachoCarga($vParametros);
            
                  if($mReturnProcesosCargoWise[0] == "false"){
                    $cMsjRetorno .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                    for($i=1;$i<count($mReturnProcesosCargoWise);$i++){
                      $cMsjRetorno .= $mReturnProcesosCargoWise[$i]."\n";
                    }
                    f_Mensaje(__FILE__, __LINE__, $cMsjRetorno."Error al Generar la Interface CDZ789.\nVerifique.");
                  }
                }
              }
            break;
          }
        }
  		}

  		if (!empty($mQuerySiae0199)) {
  			$qUpdateSiae0199  = "UPDATE $cAlfa.siae0199 ";
  			$qUpdateSiae0199 .= "SET ";
  			$qUpdateSiae0199 .= implode(', ', $mQuerySiae0199);
  			$qUpdateSiae0199 .= "WHERE ";
  			$qUpdateSiae0199 .= "admidxxx = \"{$_POST['cSucId']}\" AND ";
  			$qUpdateSiae0199 .= "dexidxxx = \"{$_POST['cDocId']}\"";

  			if (!mysql_query($qUpdateSiae0199)) {
  				$nSwitch = 1;
  				$cMsj .= "Ocurrio un error al Actualizar Fechas Automaticas \n";
  			}
  		}

  		if (!empty($mQueryZdhl0004)) {
  			$qUpdatezdhl0004  = "UPDATE $cAlfa.zdhl0004 ";
  			$qUpdatezdhl0004 .= "SET ";
  			$qUpdatezdhl0004 .= implode(', ', $mQueryZdhl0004);
  			$qUpdatezdhl0004 .= "docusadm = \"{$_POST['cUsrId']}\" ";
  			$qUpdatezdhl0004 .= "WHERE ";
  			$qUpdatezdhl0004 .= "sucidxxx = \"{$_POST['cSucId']}\" AND ";
  			$qUpdatezdhl0004 .= "docidxxx = \"{$_POST['cDocId']}\" AND ";
        $qUpdatezdhl0004 .= "docsufxx = \"{$_POST['cDocSuf']}\" ";

  			if (!mysql_query($qUpdatezdhl0004)) {
  				$nSwitch = 1;
  				$cMsj .= "Ocurrio un error al Actualizar Fechas Automaticas \n";
  			}
  		}
    }
	}

  $xTramite = mysql_query($qTramite, $xConexion01);
  //f_Mensaje(__FILE__,__LINE__,$qTramite."~".mysql_num_rows($xTramite));
  $vTramite = mysql_fetch_array($xTramite);

  /**
   * Cargo Arreglo con los Usuarios Seleccionados como Responsables desde la Interface para Hacer nuevo Reply
   */
  $mUsuRes = array();
  $vUsuRes = explode("~", $_POST['cCadena']);
  for ($i = 0; $i < count($vUsuRes); $i++) {
    if ($vUsuRes[$i] != "") {
      $mUsuRes[count($mUsuRes)] = $vUsuRes[$i];
    }
  }

  /**
   * Consulto los Usuarios Asignados al Tipo para el envio del correo
   * Se debe tener en cuenta la parametizacion de Aplica Responsable por.
   * Si el valor es TIPO, se deben tomar los usuarios del campo tipusrxx de la tabla work0001
   * Si el valor es CLIENTE, se deben tomar los usuarios del campo CLIRESTI, de la tabla SIAI0150
   */
  $qTipo = "SELECT * ";
  $qTipo .= "FROM $cAlfa.work0001 ";
  $qTipo .= "WHERE ";
  $qTipo .= "tipidxxx = \"{$_POST['cTipId']}\" AND ";
  $qTipo .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
  $xTipo = mysql_query($qTipo, $xConexion01);
  //f_Mensaje(__FILE__,__LINE__,$qTipo."~".mysql_num_rows($xTipo));
  $vTipo = mysql_fetch_array($xTipo);
  $cTercero = "SI";

  switch ($_POST['cTipRes']) {
    case "TIPO":
      /**
       * Consulto los Correos de los Usuarios a los que se les debe enviar el Correo
       */
      if (mysql_num_rows($xTipo) > 0) {

        $vCorreos = array();

        switch($cAlfa){
          case "COLVANXX":
          case "TECOLVANXX":
          case "DECOLVANXX":
            $vTipo['tipusrxx'] = $_POST['cCadena'];
          break;

        }
        $mUsuarios = explode("~", $vTipo['tipusrxx']);
        $cResponsables = $vTipo['tipusrxx'];
        $cCorreosUsuarios = "";

        for ($i = 0; $i < count($mUsuarios); $i++) {
          $qCorUsr = "SELECT USRIDXXX,USREMAXX ";
          $qCorUsr .= "FROM $cAlfa.SIAI0003 ";
          $qCorUsr .= "WHERE ";
          $qCorUsr .= "USRIDXXX = \"{$mUsuarios[$i]}\" AND ";
          $qCorUsr .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
          $xCorUsr = mysql_query($qCorUsr, $xConexion01);
          //f_Mensaje(__FILE__,__LINE__,$qCorUsr."~".mysql_num_rows($xCorUsr));
          $vCorUsr = mysql_fetch_array($xCorUsr);

          if (mysql_num_rows($xCorUsr) > 0) {
            if ((in_array($mUsuarios[$i], $mUsuRes) == true) && $_POST['cStsId'] != "101") {
              $nInd_vCorreos = count($vCorreos);
              $cCorreosUsuarios .= $vCorUsr['USREMAXX'].", ";
              $vCorreos[$nInd_vCorreos]['USRIDXXX'] = $vCorUsr['USRIDXXX'];
              $vCorreos[$nInd_vCorreos]['USREMAXX'] = $vCorUsr['USREMAXX'];
            } else {
              $nInd_vCorreos = count($vCorreos);
              $cCorreosUsuarios .= $vCorUsr['USREMAXX'].", ";
              $vCorreos[$nInd_vCorreos]['USRIDXXX'] = "";
              $vCorreos[$nInd_vCorreos]['USREMAXX'] = $vCorUsr['USREMAXX'];
            }
          }

          /**
           * Pregunto si el Usuario que esta haciendo el Reply es Responsable o es un Tercero
           * para guardar la marca en Detalle del Ticket
           */
          if ($_POST['cUsrId'] == $mUsuarios[$i]) {
            $cTercero = "";
          }
        }
      }
    break;
    case "CLIENTE":
      $qUsuarios = "SELECT CLIRESTI ";
      $qUsuarios .= "FROM $cAlfa.SIAI0150 ";
      $qUsuarios .= "WHERE ";
      $qUsuarios .= "CLIIDXXX = \"{$_POST['cCliId']}\" AND ";
      $qUsuarios .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
      $xUsuarios = mysql_query($qUsuarios, $xConexion01);
      //f_Mensaje(__FILE__,__LINE__,$qUsuarios."~".mysql_num_rows($xUsuarios));
      $vUsuarios = mysql_fetch_array($xUsuarios);

      /**
       * Consulto los Correos de los Usuarios a los que se les debe enviar el Correo
       */
      if (mysql_num_rows($xUsuarios) > 0) {

        $vCorreos = array();
        $mUsuarios = f_Explode_Array($vUsuarios['CLIRESTI'], "|", "~");
        $cResponsables = "";
        $cCorreosUsuarios = "";

        for ($i = 0; $i < count($mUsuarios); $i++) {
          if ($mUsuarios[$i][0] == $_POST['cTipId']) {
            $qCorUsr = "SELECT USRIDXXX,USREMAXX ";
            $qCorUsr .= "FROM $cAlfa.SIAI0003 ";
            $qCorUsr .= "WHERE ";
            $qCorUsr .= "USRIDXXX = \"{$mUsuarios[$i][1]}\" AND ";
            $qCorUsr .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
            $xCorUsr = mysql_query($qCorUsr, $xConexion01);
            //f_Mensaje(__FILE__,__LINE__,$qCorUsr."~".mysql_num_rows($xCorUsr));

            $vCorUsr = mysql_fetch_array($xCorUsr);
            if (mysql_num_rows($xCorUsr) > 0) {
              if ((in_array($mUsuarios[$i][1], $mUsuRes) == true) && $_POST['cStsId'] != "101") {
                $nInd_vCorreos = count($vCorreos);
                $cResponsables .= $vCorUsr['USRIDXXX']."~";
                $cCorreosUsuarios .= $vCorUsr['USREMAXX'].", ";
                $vCorreos[$nInd_vCorreos]['USRIDXXX'] = $vCorUsr['USRIDXXX'];
                $vCorreos[$nInd_vCorreos]['USREMAXX'] = $vCorUsr['USREMAXX'];
              } else {
                $nInd_vCorreos = count($vCorreos);
                $cResponsables .= $vCorUsr['USRIDXXX']."~";
                $cCorreosUsuarios .= $vCorUsr['USREMAXX'].", ";
                $vCorreos[$nInd_vCorreos]['USRIDXXX'] = "";
                $vCorreos[$nInd_vCorreos]['USREMAXX'] = $vCorUsr['USREMAXX'];
              }
            }
          }

          /**
           * Pregunto si el Usuario que esta haciendo el Reply es Responsable o es un Tercero
           * para guardar la marca en Detalle del Ticket
           */
          if ($_POST['cUsrId'] == $mUsuarios[$i][1]) {
            $cTercero = "";
          }
        }
        $cResponsables = substr($cResponsables, 0, (strlen($cResponsables) - 1));
      }
      break;
  }

  /**
   * Validando si el Tipo de Ticket tiene Responsables Parametrizados para el envio del correo
   * de lo contrario se debera informar a usuario, y no se permitira guardar el Ticket.
   */
  if (count($vCorreos) == 0) {
    $nSwitch = 1;
    $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
    $cMsj .= "No se Encontraron Responsables Asignados para Realizar el Envio del Correo.\n";
  } elseif (count($vCorreos) > 0) {
    /**
     * Guardo en la Matriz de Correos, los correos a los que se les debe enviar un copia del correo pero
     * para los que se debe deshabilitar el envio de link.
     * Al momento de Enviar el Correo, el sistema validara si en el campo de la Matriz USRIDXXX es vacio, es porque
     * corresponde a un correo para envio cc.
     */
    $vCc = explode(",", $_POST['cCc']);
    for ($ic = 0; $ic < count($vCc); $ic++) {
      if ($vCc[$ic] != "") {
        $nInd_vCorreos = count($vCorreos);
        $vCorreos[$nInd_vCorreos]['USRIDXXX'] = "";
        $vCorreos[$nInd_vCorreos]['USREMAXX'] = $vCc[$ic];
      }
    }
  }

  /**
   * Guardando correo del From para enviar copia del envio
   */
  if ($_POST['cFrom'] != "") {
    $nInd_vCorreos = count($vCorreos);
    $vCorreos[$nInd_vCorreos]['USRIDXXX'] = "";
    $vCorreos[$nInd_vCorreos]['USREMAXX'] = $_POST['cFrom'];
  }

  /**
   * Traigo Historico de Reply's Hechos al Ticket
   */
  $mHistorico = array();
  $nCorreoAbrioTicket = 0;

  $qDetRep = "SELECT work1002.*,";
  $qDetRep .= "SIAI0003.USRNOMXX AS usrnomxx,";
  $qDetRep .= "SIAI0003.USREMAXX ";
  $qDetRep .= "FROM $cAlfa.work1002 ";
  $qDetRep .= "LEFT JOIN $cAlfa.SIAI0003 ON work1002.regusrxx = SIAI0003.USRIDXXX ";
  $qDetRep .= "WHERE ";
  $qDetRep .= "work1002.ticidxxx = \"{$_POST['cTicId']}\" AND ";
  $qDetRep .= "work1002.regestxx = \"ACTIVO\" ";
  $qDetRep .= "ORDER BY ABS(work1002.repidxxx) ASC ";
  $xDetRep = mysql_query($qDetRep, $xConexion01);
  if (mysql_num_rows($xDetRep) > 0) {
    while ($xRDR = mysql_fetch_array($xDetRep)) {
      $mHistorico[count($mHistorico)] = $xRDR;

      if ($nCorreoAbrioTicket == 0) {
        $nCorreoAbrioTicket = 1;
        $cCorreosUsuarios .= $xRDR['USREMAXX'].", ";
        if ((in_array($xRDR['regusrxx'], $mUsuRes) == true) && $_POST['cStsId'] != "101") {
          $nInd_vCorreos = count($vCorreos);
          $vCorreos[$nInd_vCorreos]['USRIDXXX'] = $xRDR['regusrxx'];
          $vCorreos[$nInd_vCorreos]['USREMAXX'] = $xRDR['USREMAXX'];
        } else {
          $nInd_vCorreos = count($vCorreos);
          $vCorreos[$nInd_vCorreos]['USRIDXXX'] = "";
          $vCorreos[$nInd_vCorreos]['USREMAXX'] = $xRDR['USREMAXX'];
        }

        /**
         * Pregunto si el Usuario que esta haciendo el Reply es Responsable o es un Tercero
         * para guardar la marca en Detalle del Ticket
         */
        if ($_POST['cUsrId'] == $xRDR['regusrxx']) {
          $cTercero = "";
        }
      }
    }
  }

  $cCorreosUsuarios = substr($cCorreosUsuarios, 0, (strlen($cCorreosUsuarios) - 2));
}

/**
 * Actualizacion en la Tabla.
 */
if ($nSwitch == 0) {
  switch ($_COOKIE['kModo']) {
    case "NUEVO":
      break;
    case "EDITAR":
      $dFecha = date('Y-m-d');
      $hHora = date('H:i:s');

      $qUpdate = "UPDATE $cAlfa.work1001 SET ";
      //$qUpdate .= "ticresxx = \"{$_POST['cPriId']}\",";
      $qUpdate .= "cliidxxx = \"{$vTramite['CLIIDXXX']}\",";
      $qUpdate .= "priidxxx = \"{$_POST['cPriId']}\",";
      $qUpdate .= "stsidxxx = \"{$_POST['cStsId']}\",";
      $qUpdate .= "ticresxx = \"{$_POST['cCadena']}\",";
      $qUpdate .= "ticoccxx = \"{$_POST['cCc']}\",";
      $qUpdate .= "regusrxx = \"{$_POST['cUsrId']}\" ";
      if ($vTicket['stsidxxx'] <> "101") {
        $qUpdate .= ", regfmodx = \"$dFecha\",";
        $qUpdate .= "reghmodx = \"$hHora\" ";
      }
      $qUpdate .= "WHERE ";
      $qUpdate .= "ticidxxx = \"{$_POST['cTicId']}\" ";
      $xUpdate = mysql_query($qUpdate, $xConexion01);

      if (!$xUpdate) {
        $nSwitch = 1;
        $cMsj .= "Error Actualizando Ticket en Cabecera, Verifique.\n";
      }

      $qInsert = "INSERT INTO $cAlfa.work1002 (ticidxxx,repidxxx,ticconxx,stsidxxx,regusrxx,regfcrex,reghcrex,regfmodx,reghmodx,regestxx) ";
      $qInsert .= "VALUES ";
      $qInsert .= "(\"{$_POST['cTicId']}\", \"{$vReply['repidxxx']}\",\"{$_POST['cTicCon']}\",\"{$_POST['cStsId']}\",\"{$_POST['cUsrId']}\",\"$dFecha\", \"$hHora\", \"$dFecha\", \"$hHora\",\"ACTIVO\")";

      $xInsert = mysql_query($qInsert, $xConexion01);

      if (!$xInsert) {
        $nSwitch = 1;
        $cMsj .= "Error Guardando Reply en Detalle, Verifique.\n";
      }

      if ($nSwitch == 0) {
        /*
         * Armando Parametros para enviar el correo
         */
        $cAsunto = "Solicitud: {$vReply['repidxxx']}/";
        $cAsunto .= $vTipo['tipdesxx']."/";
        $cAsunto .= $vTramite['DGEDTXXX']."/";
        $cAsunto .= $vTramite['CLINOMXX']."/";

        /**
         * Descripcion Evento
         */
        if ($vTramite['DOIFINXX'] <> "0000-00-00") {
          $cEvento = "TRAMITE FINALIZADO";
        } elseif ($vTramite['DOIFENTR'] <> "0000-00-00") {
          $cEvento = "FACTURADO";
        } elseif ($vTramite['DOIFENFA'] <> "0000-00-00") {
          $cEvento = "ENTREGADO A FACTURACION";
        } elseif ($vTramite['DOIFENRA'] <> "0000-00-00") {
          $cEvento = "ENTREGADO A R&A";
        } elseif ($vTramite['USRID4XX'] <> "") {
          $cEvento = "OPERACION ADUANERA";
        } else {
          $cEvento = "APERTURADO";
        }

        $cAsunto .= "$cEvento/";
        $cAsunto .= $_POST['cDocId']."/";
        $cAsunto .= $vTramite['DOIPEDXX'];

        $cMensaje = "<html>";
        $cMensaje .= "<head>";
        $cMensaje .= "<title></title>";
        $cMensaje .= "</head>";
        $cMensaje .= "<body>";
        $cMensaje .= "<table cellspacing='0' cellpadding='0' width='100%' style='border: 1px solid #D0D0D0'>";
        $cMensaje .= "<tr>";
        $cMensaje .= "<td style='border: 1px solid #D0D0D0;background-color:#f1f1f1;padding-left:5px;font-weight:bold' width='20%'>TICKET</td>";
        $cMensaje .= "<td style='border: 1px solid #D0D0D0;padding-left:5px' width='10%'>".$vTicket["ticidxxx"]."</td>";
        $cMensaje .= "<td style='border: 1px solid #D0D0D0;background-color:#f1f1f1;padding-left:5px;font-weight:bold' width='10%'>POST ID</td>";
        $cMensaje .= "<td style='border: 1px solid #D0D0D0;padding-left:5px' width='10%'>".$vReply['repidxxx']."</td>";
        $cMensaje .= "<td style='border: 1px solid #D0D0D0;background-color:#f1f1f1;padding-left:5px;font-weight:bold' width='15%'>PRIORIDAD</td>";
        $cMensaje .= "<td style='border: 1px solid #D0D0D0;padding-left:5px' width='10%'>".$vDatPri["pridesxx"]."</td>";
        $cMensaje .= "<td style='border: 1px solid #D0D0D0;background-color:#f1f1f1;padding-left:5px;font-weight:bold' width='15%'>STATUS</td>";
        $cMensaje .= "<td style='border: 1px solid #D0D0D0;padding-left:5px' width='10%'>".$vDatSts["stsdesxx"]."</td>";
        $cMensaje .= "</tr>";
        $cMensaje .= "</table>";
        $cMensaje .= "<table cellspacing='0' cellpadding='0' width='100%' style='border: 1px solid #D0D0D0'>";
        $cMensaje .= "<tr>";
        $cMensaje .= "<td style='border: 1px solid #D0D0D0;background-color:#f1f1f1;padding-left:5px;font-weight:bold' width='20%'>APERTURA TICKET</td>";
        $cMensaje .= "<td style='border: 1px solid #D0D0D0;padding-left:5px' width='30%'>".$vTicket['regfcrex']."</td>";
        $cMensaje .= "<td style='border: 1px solid #D0D0D0;background-color:#f1f1f1;padding-left:5px;font-weight:bold' width='25%'>CIERRE TICKET</td>";
        $cMensaje .= "<td style='border: 1px solid #D0D0D0;padding-left:5px' width='25%'>".$vTicket['regfmodx']."</td>";
        $cMensaje .= "</tr>";
        $cMensaje .= "</table>";

        $cMensaje .= "<table cellspacing='0' cellpadding='0' width='100%' style='border: 1px solid #D0D0D0'>";
        $cMensaje .= "<tr>";
        $cMensaje .= "<td style='border: 1px solid #D0D0D0;background-color:#f1f1f1;padding-left:5px;font-weight:bold' width='20%'>REPLY CREADO POR:</td>";
        $cMensaje .= "<td style='border: 1px solid #D0D0D0;padding-left:5px' width='80%'>".$_POST['cFrom']."</td>";
        $cMensaje .= "</tr>";
        $cMensaje .= "</table>";
        
        $cMensaje .= "<table cellspacing='0' cellpadding='0' width='100%' style='border: 1px solid #D0D0D0'>";
        $cMensaje .= "<tr>";
        $cMensaje .= "<td colspan = '1' style='border: 1px solid #D0D0D0;background-color:#f1f1f1;padding-left:5px;font-weight:bold' width='20%'>TIPO DE TICKET</td>";
        $cMensaje .= "<td colspan = '5' style='border: 1px solid #D0D0D0;background-color:#FFF;padding-left:5px' width='80%'>".$vTipo["tipdesxx"]."</td>";
        $cMensaje .= "</tr>";
        $cMensaje .= "<tr>";
        $cMensaje .= "<td style='border: 1px solid #D0D0D0;background-color:#f1f1f1;padding-left:5px;font-weight:bold' width='20%'>TICKET ENVIADO A</td>";
        $cMensaje .= "<td style='border: 1px solid #D0D0D0;background-color:#FFF;padding-left:5px' width='80%'>".$cCorreosUsuarios."</td>";
        $cMensaje .= "</tr>";
        if ($_POST['cCc'] != "") {
          $cMensaje .= "<tr>";
          $cMensaje .= "<td style='border: 1px solid #D0D0D0;background-color:#f1f1f1;padding-left:5px;font-weight:bold' width='20%'>TICKET CC A</td>";
          $cMensaje .= "<td style='border: 1px solid #D0D0D0;background-color:#FFF;padding-left:5px' width='80%'>".$_POST['cCc']."</td>";
          $cMensaje .= "</tr>";
        }

        $cMensaje .= "<tr>";
        $cMensaje .= "<td style='border: 1px solid #D0D0D0;background-color:#f1f1f1;padding-left:5px;font-weight:bold' width='20%'>DO</td>";
        $cMensaje .= "<td style='border: 1px solid #D0D0D0;background-color:#FFF;padding-left:5px' width='80%'>".$_POST['cDocId']."</td>";
        $cMensaje .= "</tr>";
        $cMensaje .= "<tr>";
        $cMensaje .= "<td style='border: 1px solid #D0D0D0;background-color:#f1f1f1;padding-left:5px;font-weight:bold' width='20%'>CLIENTE</td>";
        $cMensaje .= "<td style='border: 1px solid #D0D0D0;background-color:#FFF;padding-left:5px' width='80%'>".$vTramite["CLINOMXX"]."</td>";
        $cMensaje .= "</tr>";
        $cMensaje .= "<tr>";
        $cMensaje .= "<td style='border: 1px solid #D0D0D0;background-color:#f1f1f1;padding-left:5px;font-weight:bold' width='20%'>GUIA/BL</td>";
        $cMensaje .= "<td style='border: 1px solid #D0D0D0;background-color:#FFF;padding-left:5px' width='80%'>".trim($vTramite['DGEDTXXX'])."</td>";
        $cMensaje .= "</tr>";
        $cMensaje .= "<tr>";
        $cMensaje .= "<td colspan = '6' style='border: 1px solid #D0D0D0;background-color:#f1f1f1;padding-left:5px;font-weight:bold' width='100%'>CONTENIDO</td>";
        $cMensaje .= "</tr>";
        $cMensaje .= "<tr>";
        $cMensaje .= "<td colspan = '6' style='border: 1px solid #D0D0D0;padding-left:5px' width='100%'>".$_POST['cTicCon']."</td>";
        $cMensaje .= "</tr>";
        $cMensaje .= "</table>";
        $cMensaje .= "<table cellspacing='0' cellpadding='0' width='100%' style='border: 1px solid #D0D0D0'>";
        $cMensaje .= "<tr>";

        /**
         * Pintando Historico de Reply's del Ticket
         */
        $cMenHis = "";
        if (count($mHistorico) > 0) {
          $cMenHis .= "<fieldset><legend>HISTORICO TICKET</legend>";
          $cMenHis .= "<table cellspacing='0' cellpadding='0' width='100%' style='border:0'>";
          $cMenHis .= "<td>Post Id Hecho Por:</td>
            <td style='border: 1px solid #D0D0D0;background-color:#D6DFF7;padding-left:5px;font-weight:bold' width='2%'></td>
            <td>&nbsp;Responsable</td>
            <td style='border: 1px solid #D0D0D0;background-color:#C1E8F3;padding-left:5px;font-weight:bold' width='2%'></td>
            <td>&nbsp;&nbsp;Tercero</td>";
          $cMenHis .= "</table><br>";

          for ($y = 0; $y < count($mHistorico); $y++) {
            if ($mHistorico[$y]['ticadmxx'] <> "") {
              //$cColor = "#D6F0F7";
              $cColor = "#C1E8F3";
            } else {
              $cColor = "#D6DFF7";
            }
            $cMenHis .= "<table cellspacing='0' cellpadding='0' width='100%' style='border: 1px solid #D0D0D0'>
              <tr>
                <td style='border: 1px solid #D0D0D0;background-color:$cColor;padding-left:5px;font-weight:bold' width='20%'>USUARIO</td>
                <td style='border: 1px solid #D0D0D0;background-color:$cColor;padding-left:5px' width='32%'>".$mHistorico[$y]["usrnomxx"]."</td>
                <td style='border: 1px solid #D0D0D0;background-color:$cColor;padding-left:5px;font-weight:bold' width='10%'>POST ID</td>
                <td style='border: 1px solid #D0D0D0;background-color:$cColor;padding-left:5px' width='10%'>".$mHistorico[$y]["repidxxx"]."</td>
                <td style='border: 1px solid #D0D0D0;background-color:$cColor;padding-left:5px;font-weight:bold' width='5%'>FECHA</td>
                <td style='border: 1px solid #D0D0D0;background-color:$cColor;padding-left:5px' width='10%'>".$mHistorico[$y]['regfcrex']."</td>
                <td style='border: 1px solid #D0D0D0;background-color:$cColor;padding-left:5px;font-weight:bold' width='5%'>HORA</td>
                <td style='border: 1px solid #D0D0D0;background-color:$cColor;padding-left:5px' width='8%'>".$mHistorico[$y]["reghcrex"]."</td>
              </tr>
              <tr>
                <td colspan = '8' style='border: 1px solid #D0D0D0;padding-left:5px' width='100%'>".$mHistorico[$y]['ticconxx']."</td>
              </tr>
            </table><br>";
          }
          $cMenHis .= "</fieldset>";
        }

        /**
         * Verificando si hay Adjunto para Incluirlo en el Envio del Correo
         */
        $cAdjunto = "";
        $vArchivos = array();
        if ($_FILES['cAdjunto']['name'] != "") {
          if ($_FILES['cAdjunto']['size'] <= (512 * 1024)) { // Solo se Permite cargar Archivos cuyo tama�o sea menor o igual a 512 KB
            $cFile = $_FILES['cAdjunto']['name'];
            $cFileTemp = $_FILES['cAdjunto']['tmp_name'];
            $cRuta = $OPENINIT['pathdr']."/opencomex/downloads/".$cFile;
            chmod($cRuta, intval($vSysStr['system_permisos_directorios'], 8));
            if (file_exists($cRuta)){
              unlink($cRuta);
            }

            if (!move_uploaded_file($cFileTemp, $cRuta)) {
              $nSwitch = 1;
              $cMsj = "No Se Pudo Guardar el Archivo Adjunto.";
              $mReturn[count($mReturn)] = "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ".$cMsj;
            }

            $nInd_vArchivos = count($vArchivos);
            $vArchivos[$nInd_vArchivos]['rutaxxxx'] = $cRuta;
            $vArchivos[$nInd_vArchivos]['archivox'] = $cFile;
          } else {
            $nSwitch = 1;
            $cMsj = "Archivo Adjunto Supera Limite de Tamano[512K].";
          }
        }

        $cRuta = $OPENINIT['urlweb']."/opencomex/forms/agenciaa/workflow/rlinkxxx/frrepnue.php";

        for ($i = 0; $i < count($vCorreos); $i++) {
          $cMensajeCorreo = "";
          $vCorreos[$i]['USRIDXXX'] = strtoupper(trim($vCorreos[$i]['USRIDXXX']));
          //$cRuta = "http://172.22.11.5/desarrollo/opencomex/forms/agenciaa/workflow/rlinkxxx/frrepnue.php";
          $cVar = "gTicId=".$vTicket['ticidxxx']."&gAlfa=".$cAlfa."&gUsrId=".$vCorreos[$i]['USRIDXXX'];
          $cMenUrl = "<a href='$cRuta?$cVar' target='_blank'>Link</a>";
          $cMensaje1 = "<td colspan = '6' style='border: 1px solid #D0D0D0;padding-left:5px' width='100%'>Consulte el caso visitando el siguiente ".trim($cMenUrl)."</td></tr></table><br>";
          $cMensaje2 = "<br></body></html>";

          if ($vCorreos[$i]['USRIDXXX'] != "") {
            $cMensajeCorreo = $cMensaje.$cMensaje1.$cMenHis.$cMensaje2;
          } else {
            $cMensaje1 = "<td colspan = '6' style='border: 1px solid #D0D0D0;padding-left:5px' width='100%'></td></tr></table><br>";
            $cMensajeCorreo = $cMensaje.$cMensaje1.$cMenHis.$cMensaje2;
          }

          $vDatos = array();
          $vDatos['basedato'] = $cAlfa;
          $vDatos['asuntoxx'] = $cAsunto;
          $vDatos['mensajex'] = $cMensajeCorreo;
          $vDatos['adjuntos'] = $vArchivos;
          $vDatos['destinos'] = [$vCorreos[$i]['USREMAXX']];
          $vDatos['replytox'] = [$_POST['cFrom']];

          $ObjEnvioMail = new cEnvioEmail();
          $vReturnEnviarEmailSMTP = $ObjEnvioMail->fnEviarEmailSMTP($vDatos);
        }
      }
      break;
  }
}


if ($nSwitch == 0) {
  switch ($_COOKIE['kModo']) {
    case "EDITAR":
      $cMsj .= "Datos Guardados Con Exito";
      f_Mensaje(__FILE__, __LINE__, $cMsj);
      ?>
      <script language="javascript">
        top.f_Recargar();
      </script>
      <?php

      break;
  }
} else {
  f_Mensaje(__FILE__, __LINE__, $cMsj);
}
?>

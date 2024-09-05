<?php
set_time_limit(0);
/**
 * Graba Nueva Notificacion
 * Este programa permite Grabar Notificacion
 * @author Yulieth Campos <ycampos@opentecnologia.com.co>
 * @package openComex
 */
include("../../../../libs/php/utility.php");
include("../../../../libs/php/uticwork.php");

ini_set('error_reporting', E_ERROR);
ini_set("display_errors","1");

switch($cAlfa){
  case "DHLXXXXX":
  case "TEDHLXXXXX":
  case "DEDHLXXXXX";
    include("../../../../libs/php/uticdhl.php");
    include("../../../../ws/dhlxxxxx/utiwsout.php");
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
 * Variables para reemplazar caracteres especiales
 * @var array
 */
$cBuscar = array('"',"'",chr(13),chr(10),chr(27),chr(9),chr(11));
$cReempl = array("'","\'"," "," "," "," "," ");

switch ($_POST['cOrigen']) {
  case "FADMINFINANCIERO":
    $_COOKIE['kModo'] = $_POST['kModo'];
    break;
}

$nSwitch = 0; // Switch para Vericar la Validacion de Datos
$cMsj = "";

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
      $xDatTip = f_MySql("SELECT", "", $qDatTip, $xConexion01, "");
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
     * Validando que se haya Parametrizado Aplica Responsable por
     */
    if ($_POST['cTipRes'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
      $cMsj .= "Al Tipo Escogido No se le parametrizado Aplica Responsable por, para hacer el Envio del Correo.\n";
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
      $xDatSts = f_MySql("SELECT", "", $qDatSts, $xConexion01, "");
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
      $xDatPri = f_MySql("SELECT", "", $qDatPri, $xConexion01, "");
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
     * Validaciones solo para Modo EDITAR
     */
    if ($_COOKIE['kModo'] == "EDITAR") {

      /**
       * Validando que se haya seleccionado como Minimo un Responsable
       */
      $_POST['cCadena'] = (substr($_POST['cCadena'], (strlen($_POST['cCadena']) - 1), strlen($_POST['cCadena'])) == "~") ? substr($_POST['cCadena'], 0, (strlen($_POST['cCadena']) - 1)) : $_POST['cCadena'];
      if ($_POST['cCadena'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
        $cMsj .= "Debe Seleccionar al Menos un Responsable.\n";
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
     * Validando el Estado del Ticket.
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
  case "ANULAR";
    /**
     * Validando el Estado del TIPO.
     */
    if ($_POST['cRegEst'] == "ACTIVO") {
      $_POST['cRegEst'] = "INACTIVO";
    } elseif ($_POST['cRegEst'] == "INACTIVO") {
      $_POST['cRegEst'] = "ACTIVO";
    }
  break;
  case "BORRAR":
    /**
     * No hace nada.
     */
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
  //Cargando Vector con los Datos del Ticket
  $vDatosTicket['ticidxxx'] = $_POST['cTicId'];
  $vDatosTicket['sucidxxx'] = $_POST['cSucId'];
  $vDatosTicket['docidxxx'] = $_POST['cDocId'];
  $vDatosTicket['docsufxx'] = $_POST['cDocSuf'];
  $vDatosTicket['cliidxxx'] = $_POST['cCliId'];
  $vDatosTicket['doctipxx'] = $_POST['cDocTip'];
  $vDatosTicket['fromxxxx'] = $_POST['cFrom'];
  //$vDatosTicket['fromxxxx'] = "opensensor@opentecnologia.co";
  $vDatosTicket['ticasuxx'] = $_POST['cTicAsu'];
  $vDatosTicket['tipidxxx'] = $_POST['cTipId'];
  $vDatosTicket['tipresxx'] = $_POST['cTipRes'];
  $vDatosTicket['ccadenax'] = $_POST['cCadena'];
  $vDatosTicket['stsidxxx'] = $_POST['cStsId'];
  $vDatosTicket['priidxxx'] = $_POST['cPriId'];
  $vDatosTicket['ticoccxx'] = str_replace($cBuscar,$cReempl,$_POST['cCc']);
  $vDatosTicket['ticconxx'] = $_POST['cTicCon'];
  $vDatosTicket['regestxx'] = $_POST['cRegEst'];

	// Configuracion tipo de tickets.
	$vCon = f_Explode_Array($vDatTip['tipcaact'], "|", "~");

	switch ($_COOKIE['kModo']) {
		case "NUEVO":
			$cModoRel = "ABIERTO";
			$bActualizar = true;

			#Creando Ticket
			#Creando la instancia para Crear un Nuevo Ticket
			$ObjTicket = new cTicket();
			$mReturn = $ObjTicket->fnCrearTicket($vDatosTicket);
		break;
		case "EDITAR":
			$cModoRel = "CERRADO";
			$bActualizar = $_POST['cStsId'] == "101" ? true : false;

			#Haciendo Reply a un Ticket ya existente
			#Creando la instancia para Hacer un Nuevo Reply a un Ticket ya Existente
			$ObjTicket = new cTicket();
			$mReturn = $ObjTicket->fnHacerReplyTicket($vDatosTicket);
		break;
	}

	if ($mReturn[0] == "true") {
		$vTicket['ticidxxx'] = $mReturn[1];
		if ($vDatTip['tipaacta'] == "SI" && $bActualizar) {
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
            if(($dFecha == "DOIFENCA" && $hHora == "DOIHENCA") || ($dFecha == "docfefxx" && $hHora == "DOIHENCA")){

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
                array_push($mQuerySIAI0200, array('NAME'=>"{$dFecha}", 'VALUE'=>$dFec	, 'CHECK'=>'SI'),
                                            array('NAME'=>"{$hHora}", 'VALUE'=>$tHor	, 'CHECK'=>'SI'));
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
                array_push($mQuerySiae0199, array('NAME'=>"{$dFecha}", 'VALUE'=>$dFec	, 'CHECK'=>'SI'),
                                            array('NAME'=>"{$hHora}", 'VALUE'=>$tHor	, 'CHECK'=>'SI'));
              break;
              case 'zdhl0004':
                array_push($mQueryZdhl0004, array('NAME'=>"{$dFecha}", 'VALUE'=>$dFec	, 'CHECK'=>'SI'),
                                            array('NAME'=>"{$hHora}", 'VALUE'=>$tHor	, 'CHECK'=>'SI'));
              break;
            }
          } else {
            // Si tiene 5 posiciones, guardo lo que corresponda en el campo especifico.
            switch ($cTablaAct) {
              case 'SIAI0200':
                switch($cCampo) {
                  case 'DOIREDGC':
                    array_push($mQuerySIAI0200, array('NAME'=>"{$cCampo}", 'VALUE'=>$cUse	, 'CHECK'=>'SI'));
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
          array_push($mQuerySIAI0200, array('NAME'=>'ADMIDXXX', 'VALUE'=>$_POST['cSucId']		, 'CHECK'=>'WH'),
                                      array('NAME'=>'DOIIDXXX', 'VALUE'=>$_POST['cDocId']		, 'CHECK'=>'WH'),
                                      array('NAME'=>'DOISFIDX', 'VALUE'=>$_POST['cDocSuf']	, 'CHECK'=>'WH'));

          if (!f_MySql("UPDATE","SIAI0200",$mQuerySIAI0200,$xConexion01,$cAlfa)) {
            $nSwitch = 1;
            $cMsj .= "Ocurrio un error al Actualizar Fechas Automaticas \n";
          } else {
            switch ($cAlfa) {
              case "DHLXXXXX":
              case "TEDHLXXXXX":
              case "DEDHLXXXXX":

                if($cTipoAccion == "ACTUALIZAR") {
                  if($dFecha == "DOIDEDEP" && $hHora == "DOIHDCAR") {

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
                    $xConDo = f_MySql("SELECT", "", $qConDo, $xConexion01, "");
                    $vConDo = mysql_fetch_array($xConDo);
                  
                    if($vConDo['DOIEC789'] == "0000-00-00 00:00:00" && $vConDo['DOIDEDEP'] != "" && $vConDo['DOIDEDEP'] != "0000-00-00" && $vConDo['DOIHDCAR'] != "" && $vConDo['DOIHDCAR'] != "00:00:00" && $vConDo['DOIEEI20'] != "SI" && $vConDo['DOIREFCW'] != ""){
            
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
                }
              break;
            }
          }  
        }

        if (!empty($mQuerySiae0199)) {
          array_push($mQuerySiae0199, array('NAME'=>'admidxxx', 'VALUE'=>$_POST['cSucId']		, 'CHECK'=>'WH'),
                                      array('NAME'=>'dexidxxx', 'VALUE'=>$_POST['cDocId']		, 'CHECK'=>'WH'));

          if (!f_MySql("UPDATE","siae0199",$mQuerySiae0199,$xConexion01,$cAlfa)) {
            $nSwitch = 1;
            $cMsj .= "Ocurrio un error al Actualizar Fechas Automaticas \n";
          }
        }

        if (!empty($mQueryZdhl0004)) {
          array_push($mQueryZdhl0004, array('NAME'=>"docusadm", 'VALUE'=>$cUse							, 'CHECK'=>'NO'),
                                      array('NAME'=>'sucidxxx', 'VALUE'=>$_POST['cSucId']		, 'CHECK'=>'WH'),
                                      array('NAME'=>'docidxxx', 'VALUE'=>$_POST['cDocId']		, 'CHECK'=>'WH'),
                                      array('NAME'=>'docsufxx', 'VALUE'=>$_POST['cDocSuf']	, 'CHECK'=>'WH'));

          if (!f_MySql("UPDATE","zdhl0004",$mQueryZdhl0004,$xConexion01,$cAlfa)) {
            $nSwitch = 1;
            $cMsj .= "Ocurrio un error al Actualizar Fechas Automaticas \n";
          }
        }
      }
		}
	} else {
		$nSwitch = 1;
		for ($i = 1; $i < count($mReturn); $i++) {
			$cMsj .= $mReturn[$i]."\n";
		}
	}
}

/**
 * Actualizacion en la Tabla.
 */
if ($nSwitch == 0) {
  switch ($_COOKIE['kModo']) {
    case "ANULAR":
      $qUpdate  = array(array('NAME'=>'regestxx', 'VALUE'=>trim(strtoupper($_POST['cRegEst'])), 'CHECK'=>'SI'),
                        array('NAME'=>'ticidxxx', 'VALUE'=>trim(strtoupper($_POST['cTicId'])), 'CHECK'=>'WH'));

      if (f_MySql("UPDATE", "work1001", $qUpdate, $xConexion01, $cAlfa)) {
      } else {
        $nSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "Error al Cambiar Estado al Ticket");
      }
    break;
    case "BORRAR":
      $qDelete = array(array('NAME'=>'ticidxxx', 'VALUE'=>trim(strtoupper($_POST['cTicId'])), 'CHECK'=>'WH'));

      if (f_MySql("DELETE", "work1001", $qDelete, $xConexion01, $cAlfa)) {
      } else {
        $nSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "Error al Borrar Datos de Cabecera del Ticket");
      }

      if ($nSwitch == 0) {
        $qDelete = array(array('NAME'=>'ticidxxx', 'VALUE'=>trim(strtoupper($_POST['cTicId'])), 'CHECK'=>'WH'));

        if (f_MySql("DELETE", "work1002", $qDelete, $xConexion01, $cAlfa)) {
        } else {
          $nSwitch = 1;
          f_Mensaje(__FILE__, __LINE__, "Error al Borrar Datos Detalle del Ticket");
        }
      }
    break;
  }
} else {
  f_Mensaje(__FILE__, __LINE__, $cMsj."Verifique.");
}

if ($nSwitch == 0) {
  switch ($_COOKIE['kModo']) {
    case "NUEVO":
      f_Mensaje(__FILE__, __LINE__, "Se Creo el Ticket {$vTicket['ticidxxx']} Con Exito.");
    break;
    case "EDITAR":
      f_Mensaje(__FILE__, __LINE__, "Ticket Actualizado Con Exito.");
    break;
    case "ANULAR":
      f_Mensaje(__FILE__, __LINE__, "Se Cambio El Estado del Ticket a {$_POST['cRegEst']} Con Exito.");
    break;
    case "BORRAR":
      f_Mensaje(__FILE__, __LINE__, "Ticket Eliminado Con Exito.");
    break;
  }
  ?>
  <form name = "fregresa" action = "../admticxx/fratiini.php" method = "post" target = "fmwork">
    <input type = "hidden" name = "cSQL"    value = '<?php echo $_POST['cSQL'] ?>'>
    <input type = "hidden" name = "cCampos" value = '<?php echo $_POST['cCampos'] ?>'>
    <input type = "hidden" name = "cResId"  value = '<?php echo $_POST['cResId'] ?>'>
  </form>
  <script>
    switch ("<?php echo $_POST['cOrigen'] ?>") {
      case "IMPORTACION":
        parent.fmwork.document.location = '../../../importar/frdoiini.php';
        parent.fmnav.location = '../../../nivel3.php';
      break;
      case "EXPORTACION":
        parent.fmwork.document.location = '../../../exportar/frdtgini.php';
        parent.fmnav.location = '../../../nivel3.php';
      break;
      case "WORKFLOW":
        parent.fmwork.document.location = '../myticket/frmtiini.php';
        parent.fmnav.location = '../../../nivel3.php';
      break;
      case "ADMINTICKET":
        document.forms['fregresa'].submit();
        parent.fmnav.location = '../../../nivel3.php';
      break;
      case "FADMINFINANCIERO":
        parent.opener.location.reload();
        parent.window.close();
      break;
      case "CABECERA":
        parent.window.close();
      break;
      default:
      break;
    }
  </script>
  <?php
}
?>

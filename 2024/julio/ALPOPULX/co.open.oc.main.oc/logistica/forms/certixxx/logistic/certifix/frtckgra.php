<?php
  /**
   * Graba Certificacion.
   * --- Descripcion: Permite Guardar en la tabla Tickets.
   * @author Elian Amado. <elian.amado@openits.co>
   * @package opencomex
   * @version 001
   */
  include('../../../../../financiero/libs/php/utility.php');
  include('../../../../../logistica/libs/php/utiworkf.php');
  include('../../../../../libs/php/uticemax.php');

  /**
   * Switch para Vericar la Validacion de Datos.
   * 
   * @var int
   */
  $nSwitch = 0;

  /**
   * Almacena los errores generados en el proceso.
   * 
   * @var string
   */
  $cMsj = "\n";
  /**
   * Año actual del sistema.
   * 
   * @var string
   */
  $cPerAno = date('Y');
  
  /** 
   * Instancia de la clase cTickets
   */
  $ticket = new cTickets();

  // Creación de tabla anualizada en caso de que no exista
  $qTabExis = "SHOW TABLES FROM $cAlfa LIKE \"ltic$cPerAno\"";
  $xTabExis = f_MySql("SELECT","",$qTabExis,$xConexion01,"");
  if(mysql_num_rows($xTabExis) == 0){
    $cAnoCrea = $vSysStr['logistica_ano_instalacion_modulo'];

    $qCreate  = "CREATE TABLE IF NOT EXISTS $cAlfa.ltic$cPerAno LIKE $cAlfa.ltic$cAnoCrea ";
    $xCreate = mysql_query($qCreate,$xConexion01);
    if(!$xCreate) {
      $nSwitch   = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "Error al crear Tabla Anualizada [ltic$cPerAno].\n".mysql_error($xConexion01);
    }else {
      $qTabExis = "SHOW TABLES FROM $cAlfa LIKE \"ltid$cPerAno\"";
      $xTabExis  = f_MySql("SELECT","",$qTabExis,$xConexion01,"");
      if( mysql_num_rows($xTabExis) == 0 ){
        $qCreate  = "CREATE TABLE IF NOT EXISTS $cAlfa.ltid$cPerAno LIKE $cAlfa.ltid$cAnoCrea ";
        $xCreate = mysql_query($qCreate,$xConexion01);
        //f_Mensaje(__FILE__,__LINE__,$qTabExis);    
        if(!$xCreate) {
          $nSwitch   = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al crear Tabla Anualizada [ltid$cPerAno].\n";
        }
      }
    }
  }

  $cPerAno = ($_COOKIE['kModo'] == "NUEVOTICKET") ? $cPerAno : $_POST['cAnio'];
  // Inicio de la Validacion
  switch ($_COOKIE['kModo']) {
    case "NUEVOTICKET":
    case "EDITAR":
      // Validando que el Prefijo no sea vacio.
      if ($_POST['cComPre'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Prefijo no puede ser vacio.\n";
      } else {
        // Validando que el prefijo exista.
        if ($_COOKIE['kModo'] == "NUEVOTICKET") {
          $qComprobante  = "SELECT ";
          $qComprobante .= "comidxxx ";
          $qComprobante .= "FROM $cAlfa.lpar0117 ";
          $qComprobante .= "WHERE ";
          $qComprobante .= "$cAlfa.lpar0117.comidxxx = \"{$_POST['cComId']}\" AND ";
          $qComprobante .= "$cAlfa.lpar0117.comprexx = \"{$_POST['cComPre']}\" AND ";
          $qComprobante .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
          $xComprobante  = f_MySql("SELECT","",$qComprobante,$xConexion01,"");
          if(mysql_num_rows($xComprobante) == 0){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Prefijo [{$_POST['cComPre']}] no existe.\n";
          }
        }
      }

      // Validando el Consecutivo
      if ($_COOKIE['kModo'] == "NUEVOTICKET") {
        if ($_POST['cComCsc'] != "") {
          if (!preg_match('/^[0-9]+$/', $_POST['cComCsc'])) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Consecutivo [{$_POST['cComCsc']}] debe ser numerico.\n";
          }
        } elseif ($_POST['cComCsc'] == "" && $_POST['cComTCo'] == "MANUAL") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Consecutivo no puede ser vacio.\n";
        }
      }

      // Validando que el Nit no sea vacio.
      if ($_POST['cCliId'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Nit no puede ser vacio.\n";
      } else {
        // Validando que el Nit exista.
        if ($_COOKIE['kModo'] == "NUEVOTICKET") {
          $qCliDat  = "SELECT * ";
          $qCliDat .= "FROM $cAlfa.lpar0150 ";
          $qCliDat .= "WHERE ";
          $qCliDat .= "$cAlfa.lpar0150.cliidxxx = \"{$_POST['cCliId']}\" AND ";
          $qCliDat .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
          $xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
          if(mysql_num_rows($xCliDat) == 0){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Nit del Cliente [{$_POST['cCliId']}] no existe.\n";
          }
        }
      }

        // Validando Fechas
        // Validando que la Fecha Desde no sea vacia.
        if ($_POST['cComFec'] == "" || $_POST['cComFec'] == "0000-00-00") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Se debe seleccionar una Fecha.\n";
        }

      // Valida que la observacion no sea vacia
      if ($_POST['cAsuTck'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El asunto no puede ser vacio.\n";
      }

      // Valida que el Tipo Ticket no sea vacio
      if ($_POST['cTtiCod'] == "" || $_POST['cTtiDes'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El tipo no puede ser vacio.\n";
      } else {
        // Validando que el Tipo Ticket exista
        if ($_COOKIE['kModo'] == "NUEVOTICKET") {
          $qCliDat  = "SELECT tticodxx, ttidesxx ";
          $qCliDat .= "FROM $cAlfa.lpar0158 ";
          $qCliDat .= "WHERE ";
          $qCliDat .= "$cAlfa.lpar0158.tticodxx = \"{$_POST['cTtiCod']}\" AND ";
          $qCliDat .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
          $xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
          if(mysql_num_rows($xCliDat) == 0){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Tipo Ticket [{$_POST['cTtiCod']}] no existe.\n";
          }
        }
      }

      // Valida que la prioridad no sea vacia
      if ($_POST['cPriori'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La prioridad no puede ser vacia.\n";
      } else {
        // Validando que el Tipo Ticket exista
        if ($_COOKIE['kModo'] == "NUEVOTICKET") {
          $qCliDat  = "SELECT pticodxx, ptidesxx ";
          $qCliDat .= "FROM $cAlfa.lpar0156 ";
          $qCliDat .= "WHERE ";
          $qCliDat .= "$cAlfa.lpar0156.pticodxx = \"{$_POST['cPriori']}\" AND ";
          $qCliDat .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
          $xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
          if(mysql_num_rows($xCliDat) == 0){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Tipo Ticket {$_POST['cPriori']} no existe.\n";
          }
        }
      }
    
      // Valida que el estado no sea vacio
      if ($_POST['cEstado'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El estado no puede ser vacio.\n";
      } else {
        // Validando que el Tipo Ticket exista
        if ($_COOKIE['kModo'] == "NUEVOTICKET") {
          $qCliDat  = "SELECT sticodxx, stidesxx ";
          $qCliDat .= "FROM $cAlfa.lpar0157 ";
          $qCliDat .= "WHERE ";
          $qCliDat .= "$cAlfa.lpar0157.sticodxx = \"{$_POST['cEstado']}\" AND ";
          $qCliDat .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
          $xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
          if(mysql_num_rows($xCliDat) == 0){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Tipo Ticket [{$_POST['cEstado']}] no existe.\n";
          }
        }
      }

      // Valida que el email no sea vacio
      if(trim($_POST['cCliPCECn']) != "") {
        $vCorreos = explode(",", $_POST['cCliPCECn']);
        for ($i=0; $i < count($vCorreos); $i++) { 
          $vCorreos[$i] = trim($vCorreos[$i]);
          if($vCorreos[$i] != ""){
            if (!filter_var($vCorreos[$i], FILTER_VALIDATE_EMAIL)) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= " El Correo [".$vCorreos[$i]."], No es Valido.\n";
            }
          }
        }
      }

      // Valida que el contenido no sea vacio
      if ($_POST['cConten'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El contenido no puede ser vacio.\n";
      }

    break;
  }
  // Fin de la Validacion

  // echo "<br>";
  // echo $nSwitch;
  // echo "<br>";
  // echo $cMsj;
  // echo "<br>";

  if ($nSwitch == 0) {
    switch ($_COOKIE['kModo']) {
      case "NUEVOTICKET":
        // Insertando en la Tabla lccaYYYY (Cabecera)
        $qInsert  = array(array('NAME' => 'ceridxxx','VALUE' => trim($_POST['cCerId'])               ,'CHECK' => 'SI'),  //Id del comprobante
                          array('NAME' => 'comidxxx','VALUE' => trim($_POST['cComId'])               ,'CHECK' => 'SI'),  //Codigo del comprobante
                          array('NAME' => 'comcodxx','VALUE' => trim($_POST['cComCod'])              ,'CHECK' => 'SI'),  //Codigo del comprobante
                          array('NAME' => 'comprexx','VALUE' => trim(strtoupper($_POST['cComPre']))  ,'CHECK' => 'SI'),  //Prefijo
                          array('NAME' => 'comcscxx','VALUE' => trim($_POST['cComCsc'])              ,'CHECK' => 'SI'),  //Consecutivo uno
                          array('NAME' => 'comcsc2x','VALUE' => trim($_POST['cComCsc2'])             ,'CHECK' => 'SI'),  //Consecutivo dos
                          array('NAME' => 'comfecxx','VALUE' => trim($_POST['cComFec'])              ,'CHECK' => 'SI'),  //Fecha del comprobante
                          array('NAME' => 'cliidxxx','VALUE' => trim($_POST['cCliId'])               ,'CHECK' => 'SI'),  //Id Cliente
                          array('NAME' => 'tticodxx','VALUE' => trim($_POST['cTtiCod'])              ,'CHECK' => 'SI'),  //Codigo Tipo de Ticket
                          array('NAME' => 'pticodxx','VALUE' => trim($_POST['cPriori'])              ,'CHECK' => 'SI'),  //Codigo Prioridad Ticket
                          array('NAME' => 'sticodxx','VALUE' => trim($_POST['cEstado'])              ,'CHECK' => 'SI'),  //Codigo Status Ticket
                          array('NAME' => 'ticasuxx','VALUE' => trim($_POST['cAsuTck'])              ,'CHECK' => 'SI'),  //Asunto
                          array('NAME' => 'ticcierx','VALUE' => trim('')                             ,'CHECK' => 'NO'),  //Fecha de cierre
                          array('NAME' => 'regusrxx','VALUE' => trim(strtoupper($_POST['cUsrId']))   ,'CHECK' => 'SI'),  //Usuario que creo el registro
                          array('NAME' => 'regfcrex','VALUE' => date('Y-m-d')                        ,'CHECK' => 'SI'),  //Fecha de creacion
                          array('NAME' => 'reghcrex','VALUE' => date('H:i:s')                        ,'CHECK' => 'SI'),  //Hora de creacion
                          array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                        ,'CHECK' => 'SI'),  //Fecha de modificacion
                          array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                        ,'CHECK' => 'SI'),  //Hora de modificacion
                          array('NAME' => 'regestxx','VALUE' => trim('ACTIVO')                       ,'CHECK' => 'SI'),  //Estado
                          array('NAME' => 'regstamp','VALUE' => date('Y-m-d H:m:s')                  ,'CHECK' => 'SI')); //Fecha de modificacion

        if (!f_MySql("INSERT","ltic$cPerAno",$qInsert,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error guardando datos de la Tickets - Cabecera \n";
        } else {
          // Insertando en la tabla ltidYYYY (Detalle)
          $qDetalleTicket  = "SELECT ";
          $qDetalleTicket .= "MAX(ticidxxx) AS ultimoId ";
          $qDetalleTicket .= "FROM $cAlfa.ltic$cPerAno";
          $xDetalleTicket  = f_MySql("SELECT","",$qDetalleTicket,$xConexion01,"");
          $vDetalleTicket  = mysql_fetch_array($xDetalleTicket);

          $nErrorDetalle = 0;
          $qInsert = array(array('NAME' => 'ticidxxx','VALUE' => $vDetalleTicket['ultimoId']        ,'CHECK' => 'SI'),  //Id Tickets Cabecera
                          array('NAME' => 'repcscxx','VALUE' => trim('1')                           ,'CHECK' => 'SI'),  //Consecutivo Reply
                          array('NAME' => 'tticodxx','VALUE' => trim($_POST['cTtiCod'])             ,'CHECK' => 'SI'),  //Codigo Tipo de Ticket
                          array('NAME' => 'pticodxx','VALUE' => trim($_POST['cPriori'])             ,'CHECK' => 'SI'),  //Codigo Prioridad Ticket
                          array('NAME' => 'sticodxx','VALUE' => trim($_POST['cEstado'])             ,'CHECK' => 'SI'),  //Codigo Status Ticket
                          array('NAME' => 'ticccopx','VALUE' => trim($_POST['cCliPCECn'])           ,'CHECK' => 'NO'),  //Correos en copia
                          array('NAME' => 'repreply','VALUE' => trim($_POST['cConten'])             ,'CHECK' => 'SI'),  //Reply
                          array('NAME' => 'reprepor','VALUE' => trim($_POST['cRePre'])              ,'CHECK' => 'SI'),  //Realizado por (RESPONSABLE/TERCERO)
                          array('NAME' => 'regusrxx','VALUE' => trim(strtoupper($_POST['cUsrId']))  ,'CHECK' => 'SI'),  //Usuario que creo el Reply
                          array('NAME' => 'regusrem','VALUE' => trim($_POST['cUsrEma'])             ,'CHECK' => 'SI'),  //Correo Usuario que Creo el Reply
                          array('NAME' => 'regfcrex','VALUE' => date('Y-m-d')                       ,'CHECK' => 'SI'),  //Fecha de Creacion
                          array('NAME' => 'reghcrex','VALUE' => date('H:i:s')                       ,'CHECK' => 'SI'),  //Hora de Creacion
                          array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                       ,'CHECK' => 'SI'),  //Fecha de Modificacion
                          array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                       ,'CHECK' => 'SI'),  //Hora de Modificacion
                          array('NAME' => 'regestxx','VALUE' => trim('ACTIVO')                      ,'CHECK' => 'SI'),  //Estado
                          array('NAME' => 'regstamp','VALUE' => date('Y-m-d H:m:s')                 ,'CHECK' => 'SI')); //Fecha de modificacion

          if (!f_MySql("INSERT","ltid$cPerAno",$qInsert,$xConexion01,$cAlfa)) {
            $nErrorDetalle = 1;
          }

          if ($nErrorDetalle == 1) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Error guardando datos de la Tickets - Replies en el detalle.\n";
          }
        }
      break;
      case "EDITAR":
        // Actualiza la observacion de cabecera
        $qUpdate = array(array('NAME' => 'tticodxx', 'VALUE' => trim($_POST['cTtiCod']) ,'CHECK'=>'NO'),
                        array('NAME' => 'pticodxx', 'VALUE' => trim($_POST['cPriori'])  ,'CHECK'=>'NO'),
                        array('NAME' => 'sticodxx', 'VALUE' => trim($_POST['cEstado'])  ,'CHECK'=>'NO'),
                        array('NAME' => 'ticidxxx', 'VALUE' => trim($_POST['cTicket'])  ,'CHECK'=>'WH'));

        if (!f_MySql("UPDATE","ltic$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error actualizando la cabecera del Ticket.\n";
        }

        $nErrorDetalle = 0;
        // Actualiza o crea los nuevos subservicios de la grilla de certificacion
        $qTicketDetalle  = "SELECT ";
        $qTicketDetalle .= "$cAlfa.ltid$cPerAno.* ";
        $qTicketDetalle .= "FROM $cAlfa.ltid$cPerAno ";
        $qTicketDetalle .= "WHERE ";
        $qTicketDetalle .= "$cAlfa.ltid$cPerAno.ticidxxx = \"{$_POST['cTicket']}\" ";
        $xTicketDetalle  = f_MySql("SELECT","",$qTicketDetalle,$xConexion01,"");
        if (mysql_num_rows($xTicketDetalle) > 0) {
          $qUpdate = array(array('NAME' => 'tticodxx','VALUE' => trim($_POST['cTtiCod'])   ,'CHECK' => 'SI'),  // ID Ticket
                          array('NAME' => 'repcscxx','VALUE' => trim(mysql_num_rows($xTicketDetalle)+1), 'CHECK' => 'SI'),  // Consecutivo Reply
                          array('NAME' => 'pticodxx','VALUE' => trim($_POST['cPriori'])    ,'CHECK' => 'SI'),  // Codigo Prioridad Ticket
                          array('NAME' => 'sticodxx','VALUE' => trim($_POST['cEstado'])    ,'CHECK' => 'SI'),  // Codigo Status Ticket
                          array('NAME' => 'ticccopx','VALUE' => trim($_POST['cCliPCECn'])  ,'CHECK' => 'SI'),  // Correos en copia
                          array('NAME' => 'repreply','VALUE' => trim($_POST['cConten'])    ,'CHECK' => 'SI'),  // Contenido
                          array('NAME' => 'reprepor','VALUE' => trim($_POST['cRePre'])     ,'CHECK' => 'SI'),  // Realizado por (RESPONSABLE/TERCERO)
                          array('NAME' => 'regusrxx','VALUE' => trim(strtoupper($_COOKIE['kUsrId']))  ,'CHECK' => 'SI'),  // Usuario que creo el Registro
                          array('NAME' => 'regusrem','VALUE' => trim($_POST['cUsrEma'])    ,'CHECK' => 'SI'),  // Correo Usuario que Creo el Registro
                          array('NAME' => 'regfcrex','VALUE' => date('Y-m-d')              ,'CHECK' => 'SI'),  // Fecha de creacion
                          array('NAME' => 'reghcrex','VALUE' => date('H:i:s')              ,'CHECK' => 'SI'),  // Hora de creacion
                          array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')              ,'CHECK' => 'SI'),  // Fecha de modificacion
                          array('NAME' => 'reghmodx','VALUE' => date('H:i:s')              ,'CHECK' => 'SI'),  // Hora de modificacion
                          array('NAME' => 'regestxx','VALUE' => trim('ACTIVO')             ,'CHECK' => 'SI'),  // Hora de modificacion
                          array('NAME' => 'regstamp','VALUE' => date('Y-m-d H:m:s')        ,'CHECK' => 'SI'),  // Hora de modificacion
                          array('NAME' => 'ticidxxx','VALUE' => trim($_POST['cTicket'])    ,'CHECK' => 'WH'));

          if (!f_MySql("INSERT","ltid$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
            $nErrorDetalle = 1;
          }
        } 

      if ($nErrorDetalle == 1) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Error actualizando datos en el detalle del Ticket.\n";
      }

      break;
    }
  }

  // echo "<br>";
  // echo $nSwitch;
  // echo "<br>";
  // echo $cMsj;
  // echo "<br>";
  // die();

  if ($nSwitch == 0) {
    switch ($_COOKIE['kModo']) {
      case "NUEVOTICKET":
        f_Mensaje(__FILE__,__LINE__,"El Ticket se creo con exito.");
        $ticket->fnEnvioEmail($nSwitch, $cMsj, $_POST['cCerId']);
      break;
      case "EDITAR":
        f_Mensaje(__FILE__,__LINE__,"Se actualizo el Ticket con exito.");
        $ticket->fnEnvioEmail($nSwitch, $cMsj, $_POST['cCerId']);
      break;
    }

    if ($_COOKIE['kModo'] == "NUEVOTICKET" || $_COOKIE['kModo'] == "EDITAR") {
      ?>
      <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
      <script languaje = "javascript">
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
        document.forms['frgrm'].submit();
      </script> 
      <?php
    } else {
      ?>
        <script type="text/javascript">
          parent.window.fmwork.location="<?php echo $_COOKIE['kIniAnt'] ?>";
        </script>
      <?php
    }
  } else {
    f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.");
  }
?>

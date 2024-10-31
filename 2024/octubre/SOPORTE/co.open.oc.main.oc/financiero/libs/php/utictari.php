<?php

  /**
   * utirebav.php : Utility para gestionar las tarifas, creacion y/o actualizacion de tarifas, generacion de reportes de tarifas.
   *
   * Este script contiene la colecciones de clases gestionar las tarifas, creacion y/o actualizacion de tarifas, generacion de reportes de tarifas.
   *
   * @package openComex
   */

  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");

  date_default_timezone_set('America/Bogota');

  require_once($OPENINIT['pathdr']."/opencomex/class/spout-3.1.0/src/Spout/Autoloader/autoload.php");

  use Box\Spout\Common\Entity\Style\Border;
  use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;
  use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
  use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
  use Box\Spout\Common\Entity\Style\CellAlignment;
  use Box\Spout\Writer\Common\Entity\Sheet;
  use Box\Spout\Common\Entity\Style\Color;
  use Box\Spout\Common\Entity\Style\Style;

  define("_NUMREG_",100);

  class cTarifasFacturacion {

    /**
     * Metodo para obtener la data del Reporte de Tarifas Consolidad.
     */
    function fnGuardarTarifas($pArrayParametros) {

      global $xConexion01; global $cAlfa; global $vSysStr; global $kUser;

      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var int
       */
      $nSwitch = 0;

      /**
       * Variable para hacer el retorno.
       * 
       * @var array
       */
      $mReturn    = array();
      $mReturn[0] = "";
      
      switch ($pArrayParametros['kModo']) {
        case "NUEVO":
        case "EDITAR":
    
          // Validando tipo de tarifa
          if ($pArrayParametros['cTarTip'] == "") {
            $nSwitch = 1;
            $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
            $cMsj .= "El tipo de Tarifa no Puede ser Vacio.";
            $mReturn[count($mReturn)] = $cMsj;
          }
    
          if ($pArrayParametros['cTarTip'] == "CLIENTE") {
            // Validando Cliente
            if ($pArrayParametros['cCliId'] == "") {
              $nSwitch = 1;
              $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
              $cMsj .= "El Cliente no Puede ser Vacio.";
              $mReturn[count($mReturn)] = $cMsj;
            }
    
            // Valido que Exista el Cliente
            $qSqlCli = "SELECT * FROM $cAlfa.SIAI0150 WHERE CLIIDXXX = \"{$pArrayParametros['cCliId']}\" AND REGESTXX = \"ACTIVO\" LIMIT 0,1";
            $xCrsCli  = f_MySql("SELECT","",$qSqlCli,$xConexion01,"");
            if (mysql_num_rows($xCrsCli) != 1) {
              $nSwitch = 1;
              $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
              $cMsj .= "No Existe el Cliente.";
              $mReturn[count($mReturn)] = $cMsj;
            }
    
            //Valido que el cliente no tenga parametrizado en condiciones comerciales un grupo de tarifa
            $qConCom  = "SELECT gtaidxxx ";
            $qConCom .= "FROM $cAlfa.fpar0151 ";
            $qConCom .= "WHERE ";
            $qConCom .= "cliidxxx = \"{$pArrayParametros['cCliId']}\" AND  ";
            $qConCom .= "regestxx = \"ACTIVO\" LIMIT 0,1";
            $xConCom  = f_MySql("SELECT","",$qConCom,$xConexion01,"");
            if (mysql_num_rows($xConCom) > 0) {
              $xRCC = mysql_fetch_array($xConCom);
              if ($xRCC['gtaidxxx'] != "") {
                $qGruTar = "SELECT gtadesxx FROM $cAlfa.fpar0111 WHERE gtaidxxx = \"{$xRCC['gtaidxxx']}\" AND regestxx = \"ACTIVO\" LIMIT 0,1";
                $xGruTar  = f_MySql("SELECT","",$qGruTar,$xConexion01,"");
                $xRGT = mysql_fetch_array($xGruTar);
    
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Cliente Tiene Parametrizado en sus Condiciones Comerciales el Grupo de Tarifas {$xRGT['gtadesxx']} [{$xRCC['gtaidxxx']}].";
                $mReturn[count($mReturn)] = $cMsj;
              }
            }
    
          } else {
            // Validando grupo de tarifas
            if ($pArrayParametros['cCliId'] == "") {
              $nSwitch = 1;
              $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
              $cMsj .= "El Grupo de Tarifas no Puede ser Vacio.";
              $mReturn[count($mReturn)] = $cMsj;
            }
    
            // Valido que Exista el Grupo de tarifas
            $qGruTar = "SELECT * FROM $cAlfa.fpar0111 WHERE gtaidxxx = \"{$pArrayParametros['cCliId']}\" AND regestxx = \"ACTIVO\" LIMIT 0,1";
            $xGruTar  = f_MySql("SELECT","",$qGruTar,$xConexion01,"");
            if (mysql_num_rows($xGruTar) != 1) {
              $nSwitch = 1;
              $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
              $cMsj .= "No Existe el Grupo de Tarifas.";
              $mReturn[count($mReturn)] = $cMsj;
            }
          }
    
          // Validando Concepto de Cobro
          if ($pArrayParametros['cSerId'] == "") {
            $nSwitch = 1;
            $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
            $cMsj .= "El Concepto de Cobro no puede ser vacio.";
            $mReturn[count($mReturn)] = $cMsj;
          }
    
          // Valido que Exista el Concepto de Cobro
          $qSqlSer = "SELECT * FROM $cAlfa.fpar0129 WHERE seridxxx = \"{$pArrayParametros['cSerId']}\" AND regestxx = \"ACTIVO\" LIMIT 0,1";
          $xCrsSer  = f_MySql("SELECT","",$qSqlSer,$xConexion01,"");
          if (mysql_num_rows($xCrsSer) != 1) {
            $nSwitch = 1;
            $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
            $cMsj .= "No Existe el Concepto de Cobro.";
            $mReturn[count($mReturn)] = $cMsj;
          } else {
            $mMtzSer = mysql_fetch_array($xCrsSer);
          }
    
          if ($pArrayParametros['cSerDesPc'] == "") {
            //Busco la descripcion personalizada de la empresa, si no tiene se guarda entonces la descripcion del concepto de cobro
            $pArrayParametros['cSerDesPc'] = ($mMtzSer['serdespx'] != "")?$mMtzSer['serdespx']:$mMtzSer['serdesxx'];
          }
    
          if ($pArrayParametros['cSerDesPc'] == "") {
            $nSwitch = 1;
            $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
            $cMsj .= "La Descripcion Personalizada del Concepto de Cobro para el Cliente no puede ser vacia.";
            $mReturn[count($mReturn)] = $cMsj;
          }
    
    
          // Validando Forma de Cobro
          if ($pArrayParametros['cFcoId'] == "") {
            $nSwitch = 1;
            $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
            $cMsj .= "La Forma de Cobro no puede ser vacio.";
            $mReturn[count($mReturn)] = $cMsj;
          }
          // Validado que la Forma de Pago Aplique para el Concepto de Cobro
          $mMtzCon = explode("~",$mMtzSer['fcoidxxx']);
          $zSw = 0;
    
          for ($i=0;$i<count($mMtzCon);$i++) {
            if ($pArrayParametros['cFcoId'] == $mMtzCon[$i]) {
              $zSw = 1;
            }
          }
          if ($zSw == 0) {
            $nSwitch = 1;
            $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
            $cMsj .= "La Forma de Cobro no Aplica para el Concepto de Cobro.";
            $mReturn[count($mReturn)] = $cMsj;
          }
    
          // Valido que Exista la Forma de Cobro
          $qSqlFco = "SELECT * FROM $cAlfa.fpar0130 WHERE fcoidxxx = \"{$pArrayParametros['cFcoId']}\" AND regestxx = \"ACTIVO\" LIMIT 0,1";
          $xCrsFco  = f_MySql("SELECT","",$qSqlFco,$xConexion01,"");
    
          if (mysql_num_rows($xCrsFco) != 1) {
            $nSwitch = 1;
            $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
            $cMsj .= "No Existe la Forma de Cobro.";
            $mReturn[count($mReturn)] = $cMsj;
          }
    
          /**
           * Johana Arboleda
           * 2013-07-11 11:22
           * Validacion porcentaje de retencion en la fuente, solo se guarda si es diferente al de la cuenta de retencion
           */
          $cPucRfteId  = ""; //Cuenta de Retencion en la fuente a guardar en la base de datos
          if ($mMtzSer['pucrftex'] != "") {
            if ($pArrayParametros['cPucRfte'] != "") {
              /**
               * Verificando que la cuenta seleccionada exista o que selecciono NOAPLICA
               */
              /* Traigo Descripcion de la Cuenta cPucRfte en la fpar0115 */
              $qSqlCta1 = "SELECT pucdesxx,pucretxx FROM $cAlfa.fpar0115 WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$pArrayParametros['cPucRfte']}\" LIMIT 0,1";
              $xSqlCta1 = f_MySql("SELECT","",$qSqlCta1,$xConexion01,"");
              if (mysql_num_rows($xSqlCta1) == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "La Cuenta de Retencion en la Fuente No Existe.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($mMtzSer['pucrftex'] != $pArrayParametros['cPucRfte']) {
                $cPucRfteId = $pArrayParametros['cPucRfte'];
              }
            }
          }
    
          $cPucARfteId = ""; //Cuenta de Autoretencion en la fuente a guardar en la base de datos
          if ($mMtzSer['pucaftex'] != "") {
            if ($pArrayParametros['cPucARfte'] != "") {
              /**
               * Verificando que la cuenta seleccionada exista o que selecciono NOAPLICA
               */
              /* Traigo Descripcion de la Cuenta cPucRfte en la fpar0115 */
              $qSqlCta1 = "SELECT pucdesxx,pucretxx FROM $cAlfa.fpar0115 WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$pArrayParametros['cPucARfte']}\" LIMIT 0,1";
              $xSqlCta1 = f_MySql("SELECT","",$qSqlCta1,$xConexion01,"");
              if (mysql_num_rows($xSqlCta1) == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "La Cuenta de Autoretencion en la Fuente No Existe.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($mMtzSer['pucaftex'] != $pArrayParametros['cPucARfte']) {
                $cPucARfteId = $pArrayParametros['cPucARfte'];
              }
            }
          }
    
          // Validacion de si Aplica Tarifa Por
          if ($pArrayParametros['cAplicaTar'] == "ESPECIFICO") {
            // Validando Tarifa Por
            if ($pArrayParametros['cFcoTpt'] == "") {
              $nSwitch = 1;
              $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
              $cMsj .= "El Tipo de la Tarifa Por no puede ser vacio.";
              $mReturn[count($mReturn)] = $cMsj;
            }
    
            if ($pArrayParametros['cFcoTpi'] == "") {
              $nSwitch = 1;
              $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
              $cMsj .= "El Id de la Tarifa Por no puede ser vacio.";
              $mReturn[count($mReturn)] = $cMsj;
            }
            if($pArrayParametros['cFcoTpi'] != "" && $pArrayParametros['cFcoTpt'] != ""){
              // Valido que existan las tarifas
              switch($pArrayParametros['cFcoTpt']){
                case "PROYECTO":
                  if (substr_count($vSysStr['alpopular_db_aplica'],$cAlfa) > 0){
                    $qPryDat  = "SELECT $cAlfa.siai1101.* ";
                    $qPryDat .= "FROM $cAlfa.siai1101 ";
                    $qPryDat .= "WHERE ";
                    $qPryDat .= "$cAlfa.siai1101.pryidxxx = \"{$pArrayParametros['cFcoTpi']}\" AND ";
                    $qPryDat .= "$cAlfa.siai1101.regestxx = \"ACTIVO\" LIMIT 0,1";
                  } else {
                    $qPryDat  = "SELECT * ";
                    $qPryDat .= "FROM $cAlfa.fpar0142 ";
                    $qPryDat .= "WHERE ";
                    $qPryDat .= "$cAlfa.fpar0142.pryidxxx = \"{$pArrayParametros['cFcoTpi']}\" AND ";
                    $qPryDat .= "$cAlfa.fpar0142.regestxx = \"ACTIVO\" ";
                    $qPryDat .= "LIMIT 0,1";
                  }
                  $xPryDat  = f_MySql("SELECT","",$qPryDat,$xConexion01,"");
                  if(mysql_num_rows($xPryDat) == 0){
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Id de la Tarifa Por Proyecto no Existe.";
                    $mReturn[count($mReturn)] = $cMsj;
                  } else {
                    if (substr_count($vSysStr['alpopular_db_aplica'],$cAlfa) == 0){
                      $vPryDat = mysql_fetch_array($xPryDat);
                      if($vPryDat['cliidxxx'] != $pArrayParametros['cCliId']){
                        $nSwitch = 1;
                        $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                        $cMsj .= "El Id de la Tarifa Por Proyecto no Pertenece al Cliente seleccionado.";
                        $mReturn[count($mReturn)] = $cMsj;
                      }
                    }
                  }
                break;
                case "PRODUCTO":
                  if (substr_count($vSysStr['alpopular_db_aplica'],$cAlfa) > 0){
                    $qProDat  = "SELECT $cAlfa.zalpo003.*, ";
                    $qProDat  = "SELECT $cAlfa.zalpo003.lprdesxx as prydesxx ";
                    $qProDat .= "FROM $cAlfa.zalpo003 ";
                    $qProDat .= "WHERE ";
                    $qProDat .= "$cAlfa.zalpo003.lpridxxx = \"{$pArrayParametros['cFcoTpi']}\" AND ";
                    $qProDat .= "$cAlfa.zalpo003.regestxx = \"ACTIVO\" LIMIT 0,1";
                  } else {
                    $qProDat  = "SELECT * ";
                    $qProDat .= "FROM $cAlfa.fpar0143 ";
                    $qProDat .= "WHERE ";
                    $qProDat .= "$cAlfa.fpar0143.proidxxx LIKE \"%{$pArrayParametros['cFcoTpi']}%\" AND ";
                    $qProDat .= "$cAlfa.fpar0143.regestxx = \"ACTIVO\" ";
                    $qProDat .= "ORDER BY $cAlfa.fpar0143.proidxxx";
                  }
                  $xProDat  = f_MySql("SELECT","",$qProDat,$xConexion01,"");
                  if(mysql_num_rows($xProDat) == 0){
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Id de la Tarifa Por Producto Generico no Existe.";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                break;
              }
            }
            // Valido que Exista Tarifa Por
            /*$qSqlTpo = "SELECT * FROM $cAlfa.fpar0133 WHERE tpotipxx = \"{$pArrayParametros['cFcoTpt']}\" AND tpoidxxx = \"{$pArrayParametros['cFcoTpi']}\" AND regestxx = \"ACTIVO\" LIMIT 0,1";
            $xCrsTpo  = f_MySql("SELECT","",$qSqlTpo,$xConexion01,"");
            if (mysql_num_rows($xCrsTpo) != 1) {
              $nSwitch = 1;
              $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
              $cMsj .= "No Existe Tarifa Por.";
              $mReturn[count($mReturn)] = $cMsj;
            }*/
    
          } else {
            $pArrayParametros['cFcoTpt'] = "GENERAL";
            $pArrayParametros['cFcoTpi'] = "100";
          }
    
          /***
           * Valido la moneda
           */
          if ($pArrayParametros['cMonId'] == "") {
            $nSwitch = 1;
            $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
            $cMsj .= "La Moneda no Puede ser Vacia.";
            $mReturn[count($mReturn)] = $cMsj;
          } else {
            //Validando que la moneda exista
            $qMoneda  = "SELECT MONIDXXX  ";
            $qMoneda .= "FROM $cAlfa.SIAI0111 ";
            $qMoneda .= "WHERE MONIDXXX = \"{$pArrayParametros['cMonId']}\" AND ";
            $qMoneda .= "REGESTXX = \"ACTIVO\"";
            $xMoneda  = f_MySql("SELECT","",$qMoneda,$xConexion01,"");
            if (mysql_num_rows($xMoneda) == 0) {
              $nSwitch = 1;
              $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
              $cMsj .= "La Moneda no Existe.";
              $mReturn[count($mReturn)] = $cMsj;
            }
          }
    
          // Validando las fechas de vigencia
          if ($vSysStr['system_control_vigencia_tarifas'] == "SI") {
            if ($pArrayParametros['dTarFevDe'] == "" || $pArrayParametros['dTarFevDe'] == "0000-00-00" || $pArrayParametros['dTarFevHa'] == "" || $pArrayParametros['dTarFevDe'] == "0000-00-00") {
              $nSwitch = 1;
              $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
              $cMsj .= "Las Fechas de Vigencia No Pueden Ser Vacias.";
              $mReturn[count($mReturn)] = $cMsj;
            } else {
              if ($pArrayParametros['dTarFevDe'] > $pArrayParametros['dTarFevHa']) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "Las Fecha Desde No Puede Ser Superior a la Fecha Hasta.";
                $mReturn[count($mReturn)] = $cMsj;
              }
            }
          }
    
          // Validando la Sucursal de la Tarifa No este Vacia
          // Busco Sucursales en la fpar0008
          $qSuc008 = "SELECT * FROM $cAlfa.fpar0008 WHERE regestxx = \"ACTIVO\" ORDER BY sucdesxx";
          $xSuc008  = f_MySql("SELECT","",$qSuc008,$xConexion01,"");
    
          $y = 0;
          while ($xRSuc = mysql_fetch_array($xSuc008)) {
            $mSuc008[$y] = $xRSuc;
            $y++;
          }
    
          $nErrSuc = 0;
          for ($y=0;$y<count($mSuc008);$y++) {
            //f_Mensaje(__FILE__,__LINE__,$pArrayParametros["c".$mSuc008[$y]['sucidxxx']]);
            if ($pArrayParametros["c".$mSuc008[$y]['sucidxxx']] != ""){
              $nErrSuc = 1;
    
            }
          }
    
          if ($nErrSuc == 0) {
            $nSwitch = 1;
            $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
            $cMsj .= "La Sucursal No Puede Ser Vacia.";
            $mReturn[count($mReturn)] = $cMsj;
          }
    
          // Validando Tipo de Operacion No este Vacio
          //Para los DO de tipo Registro no se parametriza Modo de transporte
          if ($pArrayParametros['cSerTop'] != "REGISTRO" && $pArrayParametros['cSerTop'] != "OTROS" ) {
            if ($pArrayParametros['cAereo'] == "" && $pArrayParametros['cMaritimo'] == "" && $pArrayParametros['cTerrestre'] == "") {
              $nSwitch = 1;
              $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
              $cMsj .= "El Modo de Transporte No Puede Ser Vacio.";
              $mReturn[count($mReturn)] = $cMsj;
            }
          } else {
            $pArrayParametros['cAereo']     = "";
            $pArrayParametros['cMaritimo']  = "";
            $pArrayParametros['cTerrestre'] = "";
          }
    
          /***** Validacion de Campos de las Diferentes Formas de Pago *****/
          /***** Validacion de Campos de las Diferentes Formas de Pago *****/
          /***** Validacion de Campos de las Diferentes Formas de Pago *****/
    
          switch ($pArrayParametros['cFcoId']) {
    
            case "100": // VALOR FIJO
            case "200":
            case "300":
            case "400":
            case "500":
            case "127": //INTERVALOS X MTRS3
            case "174": //POR METRO CUADRADO POR DIAS EN BODEGA
            case "1122": //VALOR FIJO X HOJA PRINCIPAL
            case "1143": //VALOR FIJO EN USD
            case "1144": //VALOR FIJO POR ÍTEM EN USD
            case "250": //POR METRO CUADRADO POR DIAS EN BODEGA
            case "251": //INTERVALOS X MTRS3
            case "146":
              $nVlrFijo = ($pArrayParametros['cFcoId'] == "1143" || $pArrayParametros['cFcoId'] == "1144") ? $pArrayParametros['nVFi100'] : intval($pArrayParametros['nVFi100']);
              // Validando que el Valor Fijo sea Mayor a Cero
              if ($nVlrFijo <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Fijo Debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              // Fin de Validando que el Valor Fijo sea Mayor a Cero
    
              if ($nSwitch == 0) {
                $cFcoTar = "|{$pArrayParametros['nVFi100']}|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
    
            case "101": // PORCENTAJE VALOR CIF O UNA MINIMA
            case "126": // PORCENTAJE VALOR CIF O UNA MINIMA
              // Validando que el Porcentaje del CIF sea Mayor a Cero
              if ($pArrayParametros['nPor101'] < 0 || $pArrayParametros['nPor101'] > 1 || $pArrayParametros['nPor101'] == "" || $pArrayParametros['nPor101'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje del Valor CIF debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Minimo sea Mayor a Cero
              if ($pArrayParametros['nMin101'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".substr($pArrayParametros['nPor101'],strpos($pArrayParametros['nPor101'],"."),strlen($pArrayParametros['nPor101']))."~".$pArrayParametros['nMin101']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
    
            case "102": // VAOR FIJO x UNIDADES
            case "201":
            case "301":
            case "502":
            case "150":
            case "152":
            case "163":
            case "240":
            case "401":
              // Validando que el Valor Fijo sea Mayor a Cer
              if ($pArrayParametros['nVFU102'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Fijo x Unidades Debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nVFU102']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
    
            case "103": // VALOR x HORAS CON MINIMA
            case "202":
            case "307":
              // Validando que el Valor sea Mayor a Cero
              if ($pArrayParametros['nVlr103'] == "" || $pArrayParametros['nVlr103'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de las Horas no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que las Horas sea Mayor a Cero
              if ($pArrayParametros['nHor103'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "Las Horas deben ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Adicional sea Mayor a Cero
              if ($pArrayParametros['nAdi103'] == "" || $pArrayParametros['nAdi103'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Hora Adicional no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nVlr103']."~".$pArrayParametros['nHor103']."~".$pArrayParametros['nAdi103']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
    
            case "104": // COBRO VARIABLES SEGUN UNIDADES
            case "204":
            case "303":
            case "313": // COBRO VARIABLES SEGUN UNIDADES (CANTIDAD)
            case "504":
              // Validando que el valor inicial sea mayor a 0
              if ($pArrayParametros['nIni104'] == "" || $pArrayParametros['nIni104'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Inicial no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando si las Unidades son Mayores a 0 Entonces Debe Haber Valor Posterior, y el efecto contario tambien aplica
              if ($pArrayParametros['nUni104'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de las Unidades Iniciales debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nAdi104'] == "" || $pArrayParametros['nAdi104'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Tarifa Despues de Inciales no Puede ser Vacio ni menor a Cero, Verifiqe.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nIni104']."~".$pArrayParametros['nUni104']."~".$pArrayParametros['nAdi104']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
    
            case "105": // VALOR CIF DIVIDIDO EN PESOS
              // El valor parcial debe ser mayor a cero.
              if ($pArrayParametros['nVPa105'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Parcial debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // El valor fijo de cobro por primer parcial debe ser mayor a cero.
              if ($pArrayParametros['nVFC105'] == "" || $pArrayParametros['nVFC105'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Fijo de Cobro por el Primer Parcial no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // El % de cobro del valor CIF adicional debe ser mayor a cero.
              if ($pArrayParametros['nVCA105'] < 0 || $pArrayParametros['nVCA105'] > 1 || $pArrayParametros['nVCA105'] == "" || $pArrayParametros['nVCA105'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje del Valor CIF Adicional debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nVPa105']."~".$pArrayParametros['nVFC105']."~".substr($pArrayParametros['nVCA105'],strpos($pArrayParametros['nVCA105'],"."),strlen($pArrayParametros['nVCA105']))."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
    
            case "106": // PORCENTAJE VALOR CIF O MINIMA VARIABLE POR CANTIDAD DE DECLARACIONES DE IMPORTACION
    
              $zNiveles = "";
    
              if ($pArrayParametros['nPor106'] < 0 || $pArrayParametros['nPor106'] > 1 || $pArrayParametros['nPor106'] == "" || $pArrayParametros['nPor106'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje del Valor CIF Adicional debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nNiv106'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarNiC1'] != 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior en el Primer Nivel debe ser Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv106'];$i++) {
    
                if ($pArrayParametros['vTarNiV'.($i+1)] < 0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] no puede ser Menor a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if (($i+1) < $pArrayParametros['nNiv106']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarNiC'.($i+1)] == "" || $pArrayParametros['vTarNiV'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Declaraciones o Valores no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarNiC'.($i+1)] == "" || $pArrayParametros['vTarNiV'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior o Porcentaje no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
    
                  if ($pArrayParametros['vTarNiC'.($i+1)] <= $pArrayParametros['vTarNiC'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Las Delcaraciones en el Nivel [".($i+1)."] debe ser Mayores a las Declaraciones del Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarNiV'.($i+1)] >= $pArrayParametros['vTarNiV'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Valor en el Nivel [".($i+1)."] debe ser Menor al Valor en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vTarNiC'.($i+1)]."^".$pArrayParametros['vTarNiV'.($i+1)];
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".substr($pArrayParametros['nPor106'],strpos($pArrayParametros['nPor106'],"."),strlen($pArrayParametros['nPor106']))."~".$zNiveles."!|";
              } else {
                $nSwitch = 1;  $zNiveles = ""; $cFcoTar = "";
              }
            break;
    
            case "159": // Cobro Escalonado sobre la cantidad de Toneladas por un porcentaje del valor CIF con una minima
            case "107": // COBRO ESCALONADO SOBRE VALOR CIF EN USD
            case "1138": // COBRO ESCALONADO SOBRE VALOR CIF CON MINIMA
              $zNiveles = "";
    
              if ($pArrayParametros['nMin107'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Mayor o igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nNiv107'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiIn1'] != 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior en el Primer Nivel debe ser Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSu'.$pArrayParametros['nNiv107']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv107'];$i++) {
    
                if ($pArrayParametros['vTarPor'.($i+1)] > 100) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] no puede ser Mayor a 100.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if ($pArrayParametros['vTarPor'.($i+1)] < 0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] no puede ser Menor a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if (($i+1) < $pArrayParametros['nNiv107']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarLiSu'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior, Limite Superior o Porcentaje no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarLiIn'.($i+1)] >= $pArrayParametros['vTarLiSu'.($i+1)]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior no puede ser Mayor o Igual al Limite Superior en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior o Porcentaje no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] != $pArrayParametros['vTarLiSu'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ( $vSysStr['financiero_aplica_validacion_niveles_cobro_escalonado'] == 'SI' ) {
                    if ($pArrayParametros['vTarPor'.($i+1)] >= $pArrayParametros['vTarPor'.$i]) {
                      $nSwitch = 1;
                      $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                      $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] debe ser Menor al Porcentaje en el Nivel [".$i."].";
                      $mReturn[count($mReturn)] = $cMsj;
                    }
                  }
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vTarLiIn'.($i+1)]."^".$pArrayParametros['vTarLiSu'.($i+1)]."^".substr($pArrayParametros['vTarPor'.($i+1)],strpos($pArrayParametros['vTarPor'.($i+1)],"."),strlen($pArrayParametros['vTarPor'.($i+1)]));
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nMin107']."~".$zNiveles."!|";
              } else {
                $nSwitch = 1; $zNiveles = ""; $cFcoTar = "";
              }
            break;
    
            case "108": // COBRO ESCALONADO SOBRE VALOR CIF EN COP
              $zNiveles = "";
    
              if ($pArrayParametros['nMin108'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nNiv108'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiIn1'] != 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior en el Primer Nivel debe ser Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSu'.$pArrayParametros['nNiv108']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv108'];$i++) {
    
                if ($pArrayParametros['vTarPor'.($i+1)] > 100) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] no puede ser Mayor a 100.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if ($pArrayParametros['vTarPor'.($i+1)] < 0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] no puede ser Menor a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if (($i+1) < $pArrayParametros['nNiv108']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarLiSu'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior, Limite Superior o Porcentaje no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarLiIn'.($i+1)] >= $pArrayParametros['vTarLiSu'.($i+1)]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior no puede ser Mayor o Igual al Limite Superior en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior o Porcentaje no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] != $pArrayParametros['vTarLiSu'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ( $vSysStr['financiero_aplica_validacion_niveles_cobro_escalonado'] == 'SI' ) {
                    if ($pArrayParametros['vTarPor'.($i+1)] >= $pArrayParametros['vTarPor'.$i]) {
                      $nSwitch = 1;
                      $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                      $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] debe ser Menor al Porcentaje en el Nivel [".$i."].";
                      $mReturn[count($mReturn)] = $cMsj;
                    }
                  }
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vTarLiIn'.($i+1)]."^".$pArrayParametros['vTarLiSu'.($i+1)]."^".substr($pArrayParametros['vTarPor'.($i+1)],strpos($pArrayParametros['vTarPor'.($i+1)],"."),strlen($pArrayParametros['vTarPor'.($i+1)]));
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nMin108']."~".$zNiveles."!|";
              } else {
                $nSwitch = 1; $zNiveles = ""; $cFcoTar = "";
              }
            break;
    
            case "109": // COBRO POR UNIDAD DE CARGA (CONTENEDORES)
            case "207":
            case "305":
            case "134": // COBRO POR UNIDAD DE CARGA (UNIDADES CARGA SUELTA O CONTENEDORES) O MINIMA %CIF
            case "234": // COBRO POR UNIDAD DE CARGA (UNIDADES CARGA SUELTA O CONTENEDORES) O MINIMA %FOB
              if ($pArrayParametros['nPor109'] == "") {
                $pArrayParametros['nPor109'] = 0;
              }
    
              if ($pArrayParametros['nC20109'] == "") {
                $pArrayParametros['nC20109'] = 0;
              }
    
              if ($pArrayParametros['nC40109'] == "") {
                $pArrayParametros['nC40109'] = 0;
              }
    
              if ($pArrayParametros['nCaS109'] == "") {
                $pArrayParametros['nC40109'] = 0;
              }
    
    
              // Validando que el Porcentaje del CIF sea Mayor a Cero y Menor a uno
              if ($pArrayParametros['nPor109'] < 0 || $pArrayParametros['nPor109'] > 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje del Valor CIF debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              //Validando que si el porcentaje CIF es cero debe digitar alguno de los otros datos
              if ($pArrayParametros['nPor109'] == 0 && $pArrayParametros['nC20109'] == 0 && $pArrayParametros['nC40109'] == 0 && $pArrayParametros['nCaS109'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "Debe Digitar el Valor del Cobro de los Contenedores de 20, o el de los Contenedores de 40 o el de la Carga Suelta.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Cobro por Unidad de Carga por Contenedores
              if ($pArrayParametros['nC20109'] == "" || $pArrayParametros['nC20109'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro de los Contenedores de 20 no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nC40109'] == "" || $pArrayParametros['nC40109'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro de los Contenedores de 40 no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nCaS109'] == "" || $pArrayParametros['nCaS109'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro de la Carga Suelta no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".substr($pArrayParametros['nPor109'],strpos($pArrayParametros['nPor109'],"."),strlen($pArrayParametros['nPor109']))."~".$pArrayParametros['nC20109']."~".$pArrayParametros['nC40109']."~".$pArrayParametros['nCaS109']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
    
            case "110": // COBRO ESCALONADO POR CANTIDAD DE CONTENEDORES $ COP IMPORTACION
            case "208":
              $zNiveles = "";
    
              if ($pArrayParametros['nCaS110'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Carga Suelta debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nNiv110'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiIn1'] != 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior en el Primer Nivel debe ser Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSu'.$pArrayParametros['nNiv110']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv110'];$i++) {
    
                if ($pArrayParametros['vTarPor'.($i+1)] < 1) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Valor en el Nivel [".($i+1)."] no puede ser Menor a Uno.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if (($i+1) < $pArrayParametros['nNiv110']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarLiSu'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior, Limite Superior o Valor no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarLiIn'.($i+1)] > $pArrayParametros['vTarLiSu'.($i+1)]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior no puede ser Mayor al Limite Superior en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos del Limite Inferior o Valor no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] != ($pArrayParametros['vTarLiSu'.$i] + 1)) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior mas Uno del Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarPor'.($i+1)] >= $pArrayParametros['vTarPor'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Valor en el Nivel [".($i+1)."] debe ser Menor al Valor en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vTarLiIn'.($i+1)]."^".$pArrayParametros['vTarLiSu'.($i+1)]."^".$pArrayParametros['vTarPor'.($i+1)];
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nCaS110']."~".$zNiveles."!|";
              } else {
                $nSwitch = 1; $zNiveles = ""; $cFcoTar = "";
              }
            break;
    
            case "111": // MINIMA O COBRO VARIABLE SEGUN UNIDADES (PIEZAS)
              // Validando que el Valor sea Mayor a Cero
              if ($pArrayParametros['nVlr111'] == "" || $pArrayParametros['nVlr111'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que las Horas sea Mayor a Cero
              if ($pArrayParametros['nPie111'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "Las Piezas deben ser Mayores a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Adicional sea Mayor a Cero
              if ($pArrayParametros['nAdi111'] == "" || $pArrayParametros['nAdi111'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Adicional no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nVlr111']."~".$pArrayParametros['nPie111']."~".$pArrayParametros['nAdi111']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
    
            case "112": // COBRO POR UNIDAD DE CARGA (UNIDADES CARGA SUELTA O CONTENEDORES)
            case "1117": // COBRO POR UNIDAD DE CARGA + APOYO ARCHIVO
              // Cobro por Unidad de Carga por Contenedores
              if ($pArrayParametros['nC20112'] == "" || $pArrayParametros['nC20112'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro de los Contendores de 20 no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nC40112'] == "" || $pArrayParametros['nC40112'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro de los Contendores de 40 no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nCaS112'] == "" || $pArrayParametros['nCaS112'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro de la Carga Suelta no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nC20112']."~".$pArrayParametros['nC40112']."~".$pArrayParametros['nCaS112']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
    
            case "113": // VALOR POR INCREMENTO DE TARIFA MINIMA
              if ($pArrayParametros['nPor113'] < 0 || $pArrayParametros['nPor113'] > 1 || $pArrayParametros['nPor113'] == "" || $pArrayParametros['nPor113'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje del Valor CIF debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nMin113'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima Normal debe Ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nInc113'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima Incrementada debe Ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".substr( $pArrayParametros['nPor113'],strpos( $pArrayParametros['nPor113'],"."),strlen( $pArrayParametros['nPor113']))."~".$pArrayParametros['nMin113']."~".$pArrayParametros['nInc113']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
    
    
            case "114": // VALOR FIJO O UNA MINIMA
              // Validando que el Valor Fijo sea Mayor a Cero
              if (intval($pArrayParametros['nVFi114']) < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Fijo Debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              // Fin de Validando que el Valor Fijo sea Mayor a Cero
    
              // Validando que el Valor Minimo sea Mayor a Cero
              if ($pArrayParametros['nMin114'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nVFi114']."~".$pArrayParametros['nMin114']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
    
            case "115": // VALOR POR CANTIDAD Y TIPO DE CONTENEDOR
               // Cobro por Cantidad y tipo de contenedor de 20 Pies
              if ($pArrayParametros['nC20115'] == "" || $pArrayParametros['nC20115'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro de los Contenedores de 20 no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Cobro por Cantidad y tipo de contenedor de 40 Pies
              if ($pArrayParametros['nC40115'] == "" || $pArrayParametros['nC40115'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro de los Contenedores de 40 no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Cobro por Cantidad y tipo de contenedor de 20 Pies
              if ($pArrayParametros['nC40HC115'] == "" || $pArrayParametros['nC40HC115'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro de los Contenedores de 40 HC no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nC20115']."~".$pArrayParametros['nC40115']."~".$pArrayParametros['nC40HC115']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
    
            case "147":
            case "257":
            case "258":
            case "188":
            case "116":  // COBRO ESCALONADO POR UNIDAD
            case "1139": // COBRO ESCALONADO POR UNIDAD
            case "1140": // COBRO ESCALONADO POR UNIDAD
            case "1141": // COBRO ESCALONADO POR UNIDAD
            case "1145": // COBRO ESCALONADO VALOR POR UNIDAD
              $zNiveles = "";
              if ($pArrayParametros['nNiv116'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiIn1'] != 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior en el Primer Nivel debe ser Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSu'.$pArrayParametros['nNiv116']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv116'];$i++) {
                if ($pArrayParametros['vTarPor'.($i+1)] < 0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Valor en el Nivel [".($i+1)."] no puede ser Menor a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if (($i+1) < $pArrayParametros['nNiv116']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarLiSu'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior, Limite Superior o Valor x Unidad no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarLiIn'.($i+1)] >= $pArrayParametros['vTarLiSu'.($i+1)]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior no puede ser Mayor o Igual al Limite Superior en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior o Valor x Unidad no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] != $pArrayParametros['vTarLiSu'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  /*if ($pArrayParametros['vTarPor'.($i+1)] >= $pArrayParametros['vTarPor'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] debe ser Menor al Porcentaje en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }*/
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vTarLiIn'.($i+1)]."^".$pArrayParametros['vTarLiSu'.($i+1)]."^".substr($pArrayParametros['vTarPor'.($i+1)],strpos($pArrayParametros['vTarPor'.($i+1)],"."),strlen($pArrayParametros['vTarPor'.($i+1)]));
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|"."~".$zNiveles."!|";
              } else {
                $nSwitch = 1; $zNiveles = ""; $cFcoTar = "";
              }
            break;
    
            case "117": // COBRO ESCALONADO POR UNIDAD CONSOLIDADO
              $zNiveles = "";
              if ($pArrayParametros['nNiv117'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiIn1'] != 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior en el Primer Nivel debe ser Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSu'.$pArrayParametros['nNiv117']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv117'];$i++) {
                if ($pArrayParametros['vTarPor'.($i+1)] < 0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Valor en el Nivel [".($i+1)."] no puede ser Menor a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if (($i+1) < $pArrayParametros['nNiv117']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarLiSu'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior, Limite Superior o Valor x Unidad no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarLiIn'.($i+1)] >= $pArrayParametros['vTarLiSu'.($i+1)]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior no puede ser Mayor o Igual al Limite Superior en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior o Valor x Unidad no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] != $pArrayParametros['vTarLiSu'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  /*if ($pArrayParametros['vTarPor'.($i+1)] >= $pArrayParametros['vTarPor'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] debe ser Menor al Porcentaje en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }*/
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vTarLiIn'.($i+1)]."^".$pArrayParametros['vTarLiSu'.($i+1)]."^".substr($pArrayParametros['vTarPor'.($i+1)],strpos($pArrayParametros['vTarPor'.($i+1)],"."),strlen($pArrayParametros['vTarPor'.($i+1)]));
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|"."~".$zNiveles."!|";
              } else {
                $nSwitch = 1; $zNiveles = ""; $cFcoTar = "";
              }
            break;
    
            case "118": // PORCENTAJE VALOR CIF CON MINIMA Y MAXIMA[Importaciones]
            case "211": // PORCENTAJE VALOR CIF CON MINIMA Y MAXIMA[Exportaciones]
            case "312": // COBRO POR PORCENTAJE FOB CON MINIMA O MAXIMA[DTA]
              // Validando que el Porcentaje del CIF sea Mayor a Cero
              if ($pArrayParametros['nPor118'] < 0 || $pArrayParametros['nPor118'] > 1 || $pArrayParametros['nPor118'] == "" || $pArrayParametros['nPor118'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cValCam = ($pArrayParametros['cFcoId'] == "211") ? "FOB" : "CIF";
                $cMsj .= "El Porcentaje del Valor ".$cValCam." debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Minimo sea Mayor a Cero
              if ($pArrayParametros['nMin118'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Maximo sea Mayor a Cero,
              if ($pArrayParametros['nMax118'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Maxima debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".substr($pArrayParametros['nPor118'],strpos($pArrayParametros['nPor118'],"."),strlen($pArrayParametros['nPor118']))."~".$pArrayParametros['nMin118']."~".$pArrayParametros['nMax118']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
    
            case "187": // VALOR FIJO ESCALONADO CON CIF EN USD
            case "309": // COBRO ESCALONADO CON VALOR FIJO
            case "241": // COBRO ESCALONADO CON VALOR FIJO (ULTIMO NIVEL POR UNIDAD)
            case "246": // COBRO ESCALONADO CON VALOR FIJO
            case "164": // COBRO ESCALONADO CON VALOR FIJO (ULTIMO NIVEL POR UNIDAD)
            case "131":
            case "130":
            case "119": // COBRO ESCALONADO VALOR FIJO
            case "189": // COBRO ESCALONADO VEHICULO VALOR FIJO
            case "259": // COBRO ESCALONADO VEHICULO VALOR FIJO
            case "1124": // COBRO ESCALONADO POR PEDIDO Y CANTIDAD DE ITEMS VALOR FIJO
              $zNiveles = "";
              if ($pArrayParametros['nNiv119'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiIn1'] != 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior en el Primer Nivel debe ser Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSu'.$pArrayParametros['nNiv119']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv119'];$i++) {
                if ($pArrayParametros['vTarPor'.($i+1)] < 0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Valor en el Nivel [".($i+1)."] no puede ser Menor a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if (($i+1) < $pArrayParametros['nNiv119']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarLiSu'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior, Limite Superior o Valor x Unidad no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarLiIn'.($i+1)] >= $pArrayParametros['vTarLiSu'.($i+1)]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior no puede ser Mayor o Igual al Limite Superior en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior o Valor x Unidad no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] != $pArrayParametros['vTarLiSu'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  /*if ($pArrayParametros['vTarPor'.($i+1)] >= $pArrayParametros['vTarPor'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] debe ser Menor al Porcentaje en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }*/
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vTarLiIn'.($i+1)]."^".$pArrayParametros['vTarLiSu'.($i+1)]."^".substr($pArrayParametros['vTarPor'.($i+1)],strpos($pArrayParametros['vTarPor'.($i+1)],"."),strlen($pArrayParametros['vTarPor'.($i+1)]));
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|"."~".$zNiveles."!|";
              } else {
                $nSwitch = 1; $zNiveles = ""; $cFcoTar = "";
              }
            break;
            case "132": // COBRO ESCALONADO POR PROVEEDORES
              $zNiveles = "";
              if ($pArrayParametros['nNiv132'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiIn1'] != 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior en el Primer Nivel debe ser Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSu'.$pArrayParametros['nNiv119']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv132'];$i++) {
                if ($pArrayParametros['vTarPor'.($i+1)] < 0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Valor en el Nivel [".($i+1)."] no puede ser Menor a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if (($i+1) < $pArrayParametros['nNiv132']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarLiSu'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior, Limite Superior o Valor x Unidad no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarLiIn'.($i+1)] >= $pArrayParametros['vTarLiSu'.($i+1)]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior no puede ser Mayor o Igual al Limite Superior en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior o Valor x Unidad no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] != $pArrayParametros['vTarLiSu'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  /*if ($pArrayParametros['vTarPor'.($i+1)] >= $pArrayParametros['vTarPor'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] debe ser Menor al Porcentaje en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }*/
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vTarLiIn'.($i+1)]."^".$pArrayParametros['vTarLiSu'.($i+1)]."^".substr($pArrayParametros['vTarPor'.($i+1)],strpos($pArrayParametros['vTarPor'.($i+1)],"."),strlen($pArrayParametros['vTarPor'.($i+1)]));
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|"."~".$zNiveles."!|";
              } else {
                $nSwitch = 1; $zNiveles = ""; $cFcoTar = "";
              }
            break;
            case "133": // COBRO ESCALONADO VALOR FIJO
              $zNiveles = "";
              if ($pArrayParametros['nNiv133'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiIn1'] != 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior en el Primer Nivel debe ser Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSu'.$pArrayParametros['nNiv133']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv133'];$i++) {
                if ($pArrayParametros['vTarPor'.($i+1)] < 0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Valor en el Nivel [".($i+1)."] no puede ser Menor a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if (($i+1) < $pArrayParametros['nNiv133']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarLiSu'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior, Limite Superior o Valor x Unidad no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarLiIn'.($i+1)] >= $pArrayParametros['vTarLiSu'.($i+1)]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior no puede ser Mayor o Igual al Limite Superior en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior o Valor x Unidad no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] != $pArrayParametros['vTarLiSu'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  /*if ($pArrayParametros['vTarPor'.($i+1)] >= $pArrayParametros['vTarPor'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] debe ser Menor al Porcentaje en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }*/
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vTarLiIn'.($i+1)]."^".$pArrayParametros['vTarLiSu'.($i+1)]."^".substr($pArrayParametros['vTarPor'.($i+1)],strpos($pArrayParametros['vTarPor'.($i+1)],"."),strlen($pArrayParametros['vTarPor'.($i+1)]));
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($nSwitch == 0) {
    
                $cFcoTar = "|"."~".$zNiveles."!|";
              } else {
                $nSwitch = 1; $zNiveles = ""; $cFcoTar = "";
              }
            break;
            case "199": // VALOR FIJO CONSOLIDADO POR DO
            case "270": // VALOR FIJO CONSOLIDADO POR DO
            case "120": // VALOR VARIABLE IMPORTACION
            case "212": // VALOR VARIABLE EXPORTACION
            case "320": // VALOR VARIABLE TRANSITO
            case "213": // VALOR AGENCIAMIENTO VARIABLE
              //Para esta forma de cobro, no hay valor parametrizado, debido a que el valor variable se parametriza en condiciones especiales por Do.
              $cFcoTar = "||";
            break;
    
            case "121": // PORCENTAJE CIF
              // Validando que el Porcentaje del CIF sea Mayor a Cero
              if ($pArrayParametros['nPCif121'] < 0 || $pArrayParametros['nPCif121'] > 1 || $pArrayParametros['nPCif121'] == "" || $pArrayParametros['nPCif121'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje del Valor CIF debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|{$pArrayParametros['nPCif121']}|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
    
            case "122": // Tarifa Vinculada Intercompa�ias
              // Validando que el Porcentaje del CIF y la Minima sea Mayor a Cero
              if ($pArrayParametros['nPCifp122'] < 0 || $pArrayParametros['nPCifp122'] > 1 || $pArrayParametros['nPCifp122'] == "" || $pArrayParametros['nPCifp122'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje del Valor CIF en Tarifa Plena, debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nMinP122'] < 0 ||  $pArrayParametros['nMinP122'] == "" || $pArrayParametros['nMinP122'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima en Tarifa Plena, debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nPCifd122'] < 0 || $pArrayParametros['nPCifd122'] > 1 || $pArrayParametros['nPCifd122'] == "" || $pArrayParametros['nPCifd122'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje del Valor CIF en Tarifa Vinculada Deposito, debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nMinD122'] < 0 ||  $pArrayParametros['nMinD122'] == "" || $pArrayParametros['nMinD122'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima en Tarifa Vinculada Deposito, debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nPCifa122'] < 0 || $pArrayParametros['nPCifa122'] > 1 || $pArrayParametros['nPCifa122'] == "" || $pArrayParametros['nPCifa122'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje del Valor CIF en Tarifa Vinculada Agente Carga, debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nMinA122'] < 0 ||  $pArrayParametros['nMinA122'] == "" || $pArrayParametros['nMinA122'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima en Tarifa Vinculada Agente Carga, debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if($pArrayParametros['nPCifd122'] > $pArrayParametros['nPCifp122']){
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje {$pArrayParametros['nPCifd122']} en Tarifa Vinculada Deposito no puede ser Mayor al Porcentaje {$pArrayParametros['nPCifp122']} de Tarifa Plena.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if($pArrayParametros['nPCifa122'] > $pArrayParametros['nPCifd122']){
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje {$pArrayParametros['nPCifa122']} en Tarifa Vinculada Agente Carga no puede ser Mayor al Porcentaje {$pArrayParametros['nPCifd122']} de Tarifa Vinculada Deposito.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if($pArrayParametros['nMinD122'] > $pArrayParametros['nMinP122']){
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima {$pArrayParametros['nMinD122']} en Tarifa Vinculada Deposito no puede ser Mayor al Valor de la Minima {$pArrayParametros['nMinP122']} de Tarifa Plena.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if($pArrayParametros['nMinA122'] > $pArrayParametros['nMinD122']){
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima {$pArrayParametros['nMinA122']} en Tarifa Vinculada Agente Carga no puede ser Mayor al Valor de la Minima {$pArrayParametros['nMinD122']} en Tarifa Vinculada Deposito.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|{$pArrayParametros['nPCifp122']}~{$pArrayParametros['nMinP122']}|{$pArrayParametros['nPCifd122']}~{$pArrayParametros['nMinD122']}|{$pArrayParametros['nPCifa122']}~{$pArrayParametros['nMinA122']}|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
    
            case "123": // COBRO VARIABLE POR HOJA ADICIONAL
              // Validando que el Porcentaje del CIF sea Mayor a Cero
              if ($pArrayParametros['nTarI123'] < 0 || $pArrayParametros['nTarI123'] == "" || $pArrayParametros['nTarI123'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Tarifa Principal debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Maximo sea Mayor a Cero,
              if ($pArrayParametros['nTarF123'] < 0 || $pArrayParametros['nTarF123'] == "" || $pArrayParametros['nTarF123'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Tarifa Despues de la Inicial debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }elseif($pArrayParametros['nTarF123'] >= $pArrayParametros['nTarI123']){
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Tarifa Despues de la Inicial debe ser Menor al Valor de la Tarifa Principal.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nTarI123']."~".$pArrayParametros['nTarF123']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
    
            case "124": // BASE CON MINIMA POR PORCENTAJE CIF
              // Validando que el Porcentaje del CIF sea Mayor a Cero
              if ($pArrayParametros['nBase124'] < 0 || $pArrayParametros['nBase124'] == "" || $pArrayParametros['nBase124'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Base debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que las Unidades Iniciales sea Mayor a Cero.
              if ($pArrayParametros['nMin124'] < 1 || $pArrayParametros['nMin124'] == "" || $pArrayParametros['nMin124'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima Debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }elseif($pArrayParametros['nMin124'] >= $pArrayParametros['nBase124']){
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Menor al Valor de la Base.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Maximo sea Mayor a Cero,
              if ($pArrayParametros['nPCif124'] < 0 || $pArrayParametros['nPCif124'] > 1 || $pArrayParametros['nPCif124'] == "" || $pArrayParametros['nPCif124'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje del Valor CIF debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nBase124']."~".$pArrayParametros['nMin124']."~".$pArrayParametros['nPCif124']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
    
            case "125": // PORCENTAJE CON UNA MINIMA
              // Validando que la Minima sea Mayor a Cero
              if ($pArrayParametros['nMin125'] < 0 || $pArrayParametros['nMin125'] == "" || $pArrayParametros['nMin125'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Porcentaje sea Mayor a Cero,
              if ($pArrayParametros['nPor125'] < 0 || $pArrayParametros['nPor125'] > 1 || $pArrayParametros['nPor125'] == "" || $pArrayParametros['nPor125'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nMin125']."~".$pArrayParametros['nPor125']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
    
            case "128": // PORCENTAJE CON UNA MINIMA
              // Validando que el campo no sea menor a cero
              if ($pArrayParametros['nVpFparx'] < 0 ) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Parcial no puede ser menos a 0.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el campo no sea menor a cero
              if ($pArrayParametros['nVfcFpar'] < 0 ) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Fijo de cobro no puede ser menos a 0.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el campo  sea mayor a cero
              if ($pArrayParametros['nVsaFpar'] <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor x Serial Adicional debe ser mayor a cero 0.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nVpFparx']."~".$pArrayParametros['nVfcFpar']."~".$pArrayParametros['nVsaFpar']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
    
            break;
    
            case "138": // COBRO ESCALONADO SOBRE VALOR FOB EN COP
            case "275": // COBRO ESCALONADO SOBRE VALOR FOB EN USD
              $zNiveles = "";
    
              if ($pArrayParametros['nNiv138'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiIn1'] != 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior en el Primer Nivel debe ser Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSu'.$pArrayParametros['nNiv138']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv138'];$i++) {
    
                if (($i+1) < $pArrayParametros['nNiv138']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarLiSu'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior, Limite Superior o Valor Fijo no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarLiIn'.($i+1)] >= $pArrayParametros['vTarLiSu'.($i+1)]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior no puede ser Mayor o Igual al Limite Superior en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior o Valor Fijo no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] != $pArrayParametros['vTarLiSu'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ( $vSysStr['financiero_aplica_validacion_niveles_cobro_escalonado'] == 'SI' ) {
                    if ($pArrayParametros['vTarPor'.($i+1)] >= $pArrayParametros['vTarPor'.$i]) {
                      $nSwitch = 1;
                      $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                      $cMsj .= "El Valor Fijo en el Nivel [".($i+1)."] debe ser Menor al Valor Fijo en el Nivel [".$i."].";
                      $mReturn[count($mReturn)] = $cMsj;
                    }
                  }
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vTarLiIn'.($i+1)]."^".$pArrayParametros['vTarLiSu'.($i+1)]."^".substr($pArrayParametros['vTarPor'.($i+1)],strpos($pArrayParametros['vTarPor'.($i+1)],"."),strlen($pArrayParametros['vTarPor'.($i+1)]));
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|~".$zNiveles."!|";
              } else {
                $nSwitch = 1; $zNiveles = ""; $cFcoTar = "";
              }
            break;
    
            case "139": // GRUPO DOCUMENTAL
              // Validando Cantidad por Grupo
              if ($pArrayParametros['nCanDim139'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "La Cantidad DIM por Grupo debe ser Mayor o Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nCanDav139'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "La Cantidad DAV por Grupo debe ser Mayor o Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nCanDim139'] == 0 && $pArrayParametros['nCanDav139'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "La Cantidad DIM o La Cantidad DAV por Grupo debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando Valor Fijo por Grupo
              if ($pArrayParametros['nVlr139'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Fijo por Grupo debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando Cobro a Parir del Grupo
              if ($pArrayParametros['nGru139'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "La Cantidad de Grupos que no se cobran debe ser Igual o Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nCanDim139']."~".$pArrayParametros['nCanDav139']."~".$pArrayParametros['nVlr139']."~".$pArrayParametros['nGru139']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "140": // PORCENTAJE VALOR CIF O UNA MINIMA
              // Validando que el Porcentaje del CIF sea Mayor a Cero
              if ($pArrayParametros['nPor140'] < 0 || $pArrayParametros['nPor140'] > 1 || $pArrayParametros['nPor140'] == "" || $pArrayParametros['nPor140'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje del Valor CIF debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Minimo sea Mayor a Cero
              if ($pArrayParametros['nMin140'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nVlrDc'] == "" || $pArrayParametros['nVlrDc'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Variable de Descargue Directo no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".substr($pArrayParametros['nPor140'],strpos($pArrayParametros['nPor140'],"."),strlen($pArrayParametros['nPor140']))."~".$pArrayParametros['nMin140']."~".$pArrayParametros['nVlrDc']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "137": // PORCENTAJE DEL VALOR FOB O UNA MINIMA
            case "173": // PORCENTAJE DEL VALOR FOB POR PERIODO DE ALMACENAMIENTO CON MINIMA
            case "203": // PORCENTAJE DEL VALOR FOB O UNA MINIMA
            case "249": // PORCENTAJE DEL VALOR FOB O UNA MINIMA
            case "302":
              // Validando que el Porcentaje del CIF sea Mayor a Cero
              if ($pArrayParametros['nPor203'] < 0 || $pArrayParametros['nPor203'] > 1 || $pArrayParametros['nPor203'] == "" || $pArrayParametros['nPor203'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje del Valor FOB debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Minimo sea Mayor a Cero
              if ($pArrayParametros['nMin203'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".substr($pArrayParametros['nPor203'],strpos($pArrayParametros['nPor203'],"."),strlen($pArrayParametros['nPor203']))."~".$pArrayParametros['nMin203']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
    
            case "135": // COBRO ESCALONADO SOBRE VALOR FOB EN USD
            case "205": // COBRO ESCALONADO SOBRE VALOR FOB EN USD
            case "304":
              $zNiveles = "";
    
              if ($pArrayParametros['nMin205'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nNiv205'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiIn1'] != 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior en el Primer Nivel debe ser Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSu'.$pArrayParametros['nNiv205']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv205'];$i++) {
    
                if ($pArrayParametros['vTarPor'.($i+1)] > 100) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] no puede ser Mayor a 100.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if ($pArrayParametros['vTarPor'.($i+1)] < 0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] no puede ser Menor a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if (($i+1) < $pArrayParametros['nNiv205']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarLiSu'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior, Limite Superior o Porcentaje no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarLiIn'.($i+1)] >= $pArrayParametros['vTarLiSu'.($i+1)]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior no puede ser Mayor o Igual al Limite Superior en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior o Porcentaje no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] != $pArrayParametros['vTarLiSu'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ( $vSysStr['financiero_aplica_validacion_niveles_cobro_escalonado'] == 'SI' ) {
                    if ($pArrayParametros['vTarPor'.($i+1)] >= $pArrayParametros['vTarPor'.$i]) {
                      $nSwitch = 1;
                      $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                      $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] debe ser Menor al Porcentaje en el Nivel [".$i."].";
                      $mReturn[count($mReturn)] = $cMsj;
                    }
                  }
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vTarLiIn'.($i+1)]."^".$pArrayParametros['vTarLiSu'.($i+1)]."^".substr($pArrayParametros['vTarPor'.($i+1)],strpos($pArrayParametros['vTarPor'.($i+1)],"."),strlen($pArrayParametros['vTarPor'.($i+1)]));
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nMin205']."~".$zNiveles."!|";
              } else {
                $nSwitch = 1; $zNiveles = ""; $cFcoTar = "";
              }
            break;
    
            case "136": // COBRO ESCALONADO SOBRE VALOR FOB EN COP
            case "206": // COBRO ESCALONADO SOBRE VALOR FOB EN COP
              $zNiveles = "";
    
              if ($pArrayParametros['nMin206'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nNiv206'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiIn1'] != 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior en el Primer Nivel debe ser Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSu'.$pArrayParametros['nNiv206']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv206'];$i++) {
    
                if ($pArrayParametros['vTarPor'.($i+1)] > 100) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] no puede ser Mayor a 100.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if ($pArrayParametros['vTarPor'.($i+1)] < 0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] no puede ser Menor a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if (($i+1) < $pArrayParametros['nNiv206']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarLiSu'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior, Limite Superior o Porcentaje no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarLiIn'.($i+1)] >= $pArrayParametros['vTarLiSu'.($i+1)]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior no puede ser Mayor o Igual al Limite Superior en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior o Porcentaje no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] != $pArrayParametros['vTarLiSu'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ( $vSysStr['financiero_aplica_validacion_niveles_cobro_escalonado'] == 'SI' ) {
                    if ($pArrayParametros['vTarPor'.($i+1)] >= $pArrayParametros['vTarPor'.$i]) {
                      $nSwitch = 1;
                      $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                      $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] debe ser Menor al Porcentaje en el Nivel [".$i."].";
                      $mReturn[count($mReturn)] = $cMsj;
                    }
                  }
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vTarLiIn'.($i+1)]."^".$pArrayParametros['vTarLiSu'.($i+1)]."^".substr($pArrayParametros['vTarPor'.($i+1)],strpos($pArrayParametros['vTarPor'.($i+1)],"."),strlen($pArrayParametros['vTarPor'.($i+1)]));
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nMin206']."~".$zNiveles."!|";
              } else {
                $nSwitch = 1; $zNiveles = ""; $cFcoTar = "";
              }
            break;
    
            case "209": // VALOR FOB DIVIDIDO EN PESOS
              // El valor parcial debe ser mayor a cero.
              if ($pArrayParametros['nPar209'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Parcial debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // El valor fijo de cobro por primer parcial debe ser mayor a cero.
              if ($pArrayParametros['nVFC209'] == "" || $pArrayParametros['nVFC209'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Fijo de Cobro por el Primer Parcial no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // El % de cobro del valor CIF adicional debe ser mayor a cero.
              if ($pArrayParametros['nVCA209'] < 0 || $pArrayParametros['nVCA209'] > 1 || $pArrayParametros['nVCA209'] == "" || $pArrayParametros['nVCA209'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje del Valor CIF Adicional debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nPar209']."~".$pArrayParametros['nVFC209']."~".substr($pArrayParametros['nVCA209'],strpos($pArrayParametros['nVCA209'],"."),strlen($pArrayParametros['nVCA209']))."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
    
            case "1142": // PORCENTAJE VALOR O UNA MINIMA EN USD
            case "1146": // PORCENTAJE VALOR O UNA MINIMA
            case "210":  // PORCENTAJE DEL VALOR FOB POR LA T.R.M.
              // El % de cobro del valor CIF adicional debe ser mayor a cero.
              if ($pArrayParametros['nPVF210'] < 0 || $pArrayParametros['nPVF210'] > 1 || $pArrayParametros['nPVF210'] == "" || $pArrayParametros['nPVF210'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje del Valor FOB debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Minimo sea Mayor a Cero
              if ($pArrayParametros['nMin210'] <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".substr($pArrayParametros['nPVF210'],strpos($pArrayParametros['nPVF210'],"."),strlen($pArrayParametros['nPVF210']))."~".$pArrayParametros['nMin210']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
    
            case "306":
              // Validando que el Porcentaje del CIF sea Mayor a Cero
              if ($pArrayParametros['nPor306'] < 0 || $pArrayParametros['nPor306'] > 1 || $pArrayParametros['nPor306'] == "" || $pArrayParametros['nPor306'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje de los Tributos Suspendidos ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Minimo sea Mayor a Cero
              if ($pArrayParametros['nMin306'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".substr($pArrayParametros['nPor306'],strpos($pArrayParametros['nPor306'],"."),strlen($pArrayParametros['nPor306']))."~".$pArrayParametros['nMin306']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "129": // % del Valor CIF o Valor Parcial
              // Validando que el Porcentaje del CIF sea Mayor a Cero
              if ($pArrayParametros['nPor129'] < 0 || $pArrayParametros['nPor129'] > 1 || $pArrayParametros['nPor129'] == "" || $pArrayParametros['nPor129'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje del Valor CIF debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Parcial sea Mayor a Cero
                  if ($pArrayParametros['nPar129'] < 1) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Valor Parcial debe ser Mayor a Cero.";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($nSwitch == 0) {
                    $cFcoTar = "|".substr($pArrayParametros['nPor129'],strpos($pArrayParametros['nPor129'],"."),strlen($pArrayParametros['nPor129']))."~".$pArrayParametros['nPar129']."|";
                  } else {
                    $nSwitch = 1; $cFcoTar = "";
                  }
            break;
            case "141": // PORCENTAJE VALOR CIF Y TASA DE NEGOCIADA
              // Validando que el Porcentaje del CIF sea Mayor a Cero
              if ($pArrayParametros['nPor141'] < 0 || $pArrayParametros['nPor141'] > 1 || $pArrayParametros['nPor141'] == "" || $pArrayParametros['nPor141'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje del Valor CIF debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Minimo sea Mayor a Cero
              if ($pArrayParametros['nTasNeg141'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la tasa negociada debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".substr($pArrayParametros['nPor141'],strpos($pArrayParametros['nPor141'],"."),strlen($pArrayParametros['nPor141']))."~".$pArrayParametros['nTasNeg141']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "142": //SUBPARTIDA POR UNIDAD CON MINIMA
            case "176": //VALOR POR UNIDADES INICIALES CON MINIMA
            case "253": //VALOR POR UNIDADES INICIALES CON MINIMA
            case "279": //SUBPARTIDA POR UNIDAD CON MINIMA
            
              if ($pArrayParametros['nUniIni142'] <= 0 || $pArrayParametros['nUniIni142'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "Las Unidades Iniciales deben ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nValIni142'] <= 0 || $pArrayParametros['nValIni142'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Inicial debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nUniAdi142'] < 0 || $pArrayParametros['nUniAdi142'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "Las Unidades Adicionales deben ser Mayor o Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nValAdi142'] < 0 || $pArrayParametros['nValAdi142'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Adicional debe ser Mayor o Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nUniIni142']."~".$pArrayParametros['nValIni142']."~".$pArrayParametros['nUniAdi142']."~".$pArrayParametros['nValAdi142']."|";
              } else {
                $nSwitch = 1;
                $cFcoTar = "";
              }
            break;
            case "143": //VALOR UNIDAD O MINIMA
            case "151": //VALOR  FIJO CANTIDAD DE DECLARACIONES DE IMPORTACION CON MINIMA
    
              if ($pArrayParametros['nValUni143'] <= 0 || $pArrayParametros['nValUni143'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor por Unidad debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nMin143'] <= 0 || $pArrayParametros['nMin143'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "La Minima debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nValUni143']."~".$pArrayParametros['nMin143']."|";
              } else {
                $nSwitch = 1;
                $cFcoTar = "";
              }
            break;
            case "144": //VALOR FIJO POR UNIDAD CON MINIMA Y MAXIMA
    
              if ($pArrayParametros['nValFijUni144'] <= 0 || $pArrayParametros['nValFijUni144'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Fijo por Unidad debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nValMin144'] <= 0 || $pArrayParametros['nValMin144'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Minimo debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nValMax144'] <= 0 || $pArrayParametros['nValMax144'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Maximo debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nValMin144'] > $pArrayParametros['nValMax144']) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Maximo debe ser Mayor al Valor Minimo.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nValFijUni144']."~".$pArrayParametros['nValMin144']."~".$pArrayParametros['nValMax144']."|";
              } else {
                $nSwitch = 1;
                $cFcoTar = "";
              }
            break;
            case "145": //COBRO VARIABLE SEGUN CANTIDAD DE CONTENEDORES
    
              if ($pArrayParametros['nTarIniCar145'] <= 0 || $pArrayParametros['nTarIniCar145'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "La Tarifa Inicial Carga Suelta debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nUniIniCar145'] <= 0 || $pArrayParametros['nUniIniCar145'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "Las Unidades Iniciales Carga Suelta debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nTarDesCar145'] < 0 || $pArrayParametros['nTarDesCar145'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "La Tarifa despues de Unidades Iniciales Carga Suelta debe ser Mayor o Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nTarIniC20145'] <= 0 || $pArrayParametros['nTarIniC20145'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "La Tarifa Inicial Contenedores de 20 debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nUniIniC20145'] <= 0 || $pArrayParametros['nUniIniC20145'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "Las Unidades Iniciales Contenedores de 20 debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nTarDesC20145'] < 0 || $pArrayParametros['nTarDesC20145'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "La Tarifa despues de Unidades Iniciales Contenedores de 20 debe ser Mayor o Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nTarIniC40145'] <= 0 || $pArrayParametros['nTarIniC40145'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "La Tarifa Inicial Contenedores de 40 debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nUniIniC40145'] <= 0 || $pArrayParametros['nUniIniC40145'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "Las Unidades Iniciales Contenedores de 40 debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nTarDesC40145'] < 0 || $pArrayParametros['nTarDesC40145'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "La Tarifa despues de Unidades Iniciales Contenedores de 40 debe ser Mayor o Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nTarIniCar145']."^".$pArrayParametros['nUniIniCar145']."^".$pArrayParametros['nTarDesCar145']."~".$pArrayParametros['nTarIniC20145']."^".$pArrayParametros['nUniIniC20145']."^".$pArrayParametros['nTarDesC20145']."~".$pArrayParametros['nTarIniC40145']."^".$pArrayParametros['nUniIniC40145']."^".$pArrayParametros['nTarDesC40145']."|";
              } else {
                $nSwitch = 1;
                $cFcoTar = "";
              }
            break;
            case "235": //COBRO POR UNIDAD DE CARGA CONTENEDORES CON MINIMA (EXPORTACION)
    
              // Validando que el Porcentaje del CIF sea Mayor a Cero y Menor a uno
              if ($pArrayParametros['nCifGra235'] < 0 || $pArrayParametros['nCifGra235'] > 1 || $pArrayParametros['nCifGra235'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje del Valor CIF debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Cobro por Unidad de Carga por Contenedores
              if ($pArrayParametros['nCon20235'] == "" || $pArrayParametros['nCon20235'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro de los Contenedores de 20 no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nCon40235'] == "" || $pArrayParametros['nCon40235'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro de los Contenedores de 40 no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nConCar235'] == "" || $pArrayParametros['nConCar235'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro de la Carga Suelta no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nMin235'] == "" || $pArrayParametros['nMin235'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Minima no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".substr($pArrayParametros['nCifGra235'],strpos($pArrayParametros['nCifGra235'],"."),strlen($pArrayParametros['nCifGra235']))."~".$pArrayParametros['nCon20235']."~".$pArrayParametros['nCon40235']."~".$pArrayParametros['nConCar235']."~".$pArrayParametros['nMin235']."|";
              } else {
                $nSwitch = 1;
                $cFcoTar = "";
              }
            break;
    
            case "154": // COBRO ESCALONADO POR UNIDAD DE PESO
            case "149": // VALOR ESCALONADO POR ITEM CON MINIMA
            case "236": //
            case "148": // COBRO ESCALONADO POR UNIDAD CON MINIMA
              $zNiveles = "";
              if ($pArrayParametros['nNiv148'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiIn1'] != 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior en el Primer Nivel debe ser Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSu'.$pArrayParametros['nNiv148']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv148'];$i++) {
                if ($pArrayParametros['vTarPor'.($i+1)] < 0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Valor en el Nivel [".($i+1)."] no puede ser Menor a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if (($i+1) < $pArrayParametros['nNiv148']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarLiSu'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior, Limite Superior o Valor x Unidad no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarLiIn'.($i+1)] >= $pArrayParametros['vTarLiSu'.($i+1)]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior no puede ser Mayor o Igual al Limite Superior en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior o Valor x Unidad no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] != $pArrayParametros['vTarLiSu'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  /*if ($pArrayParametros['vTarPor'.($i+1)] >= $pArrayParametros['vTarPor'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] debe ser Menor al Porcentaje en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }*/
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vTarLiIn'.($i+1)]."^".$pArrayParametros['vTarLiSu'.($i+1)]."^".substr($pArrayParametros['vTarPor'.($i+1)],strpos($pArrayParametros['vTarPor'.($i+1)],"."),strlen($pArrayParametros['vTarPor'.($i+1)]));
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($pArrayParametros['nMin148'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nMin148']."~".$zNiveles."!|";
              } else {
                $nSwitch = 1;
                $zNiveles = "";
                $cFcoTar = "";
              }
            break;
            case "190": // COBRO ESCALONADO POR UNIDAD CON MAXIMA
            case "260": // COBRO ESCALONADO POR UNIDAD CON MAXIMA
              $zNiveles = "";
              if ($pArrayParametros['nNiv190'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nMax190'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Maxima debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiIn1'] != 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior en el Primer Nivel debe ser Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSu'.$pArrayParametros['nNiv190']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv190'];$i++) {
                if ($pArrayParametros['vTarPor'.($i+1)] < 0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Valor en el Nivel [".($i+1)."] no puede ser Menor a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if (($i+1) < $pArrayParametros['nNiv190']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarLiSu'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior, Limite Superior o Valor x Unidad no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarLiIn'.($i+1)] >= $pArrayParametros['vTarLiSu'.($i+1)]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior no puede ser Mayor o Igual al Limite Superior en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior o Valor x Unidad no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] != $pArrayParametros['vTarLiSu'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  /*if ($pArrayParametros['vTarPor'.($i+1)] >= $pArrayParametros['vTarPor'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] debe ser Menor al Porcentaje en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }*/
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vTarLiIn'.($i+1)]."^".$pArrayParametros['vTarLiSu'.($i+1)]."^".substr($pArrayParametros['vTarPor'.($i+1)],strpos($pArrayParametros['vTarPor'.($i+1)],"."),strlen($pArrayParametros['vTarPor'.($i+1)]));
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nMax190']."~".$zNiveles."!|";
              } else {
                $nSwitch = 1;
                $zNiveles = "";
                $cFcoTar = "";
              }
            break;
    
            case "153": // COBRO POR UNIDAD DE CARGA (CONTENEDORES - FURGON)
    
              // Cobro por Unidad de Carga por Contenedores
              if ($pArrayParametros['nC20153'] == "" || $pArrayParametros['nC20153'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro de los Contenedores de 20 no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nC40153'] == "" || $pArrayParametros['nC40153'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro de los Contenedores de 40 no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nFur153'] == "" || $pArrayParametros['nFur153'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro del Furgon no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nC20153']."~".$pArrayParametros['nC40153']."~".$pArrayParametros['nFur153']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
    
            case "155": // VALOR VARIABLE POR PCC
            case "281": // VALOR VARIABLE POR PCC
    
              // Cobro por Unidad de Carga por Contenedores
              if ($pArrayParametros['nVlrBas155'] == "" || $pArrayParametros['nVlrBas155'] <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Base Debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nPorApl155'] == "" || $pArrayParametros['nPorApl155'] <= 0 || $pArrayParametros['nPorApl155'] > 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El % Aplica Debe ser Entre 0 y 1.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nVlrBas155']."~".$pArrayParametros['nPorApl155']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
    
            case "237": // COBRO POR UNIDAD DE CARGA CON MINIMA - EXPORTACION
            case "156": // COBRO POR UNIDAD DE CARGA CON MINIMA
    
              if ($pArrayParametros['nMin156'] == "" || $pArrayParametros['nMin156'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nC20156'] == "" || $pArrayParametros['nC20156'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro de los Contendores de 20 no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nC40156'] == "" || $pArrayParametros['nC40156'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro de los Contendores de 40 no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nCaS156'] == "" || $pArrayParametros['nCaS156'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro de la Carga Suelta no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nMin156']."~".$pArrayParametros['nC20156']."~".$pArrayParametros['nC40156']."~".$pArrayParametros['nCaS156']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "157": // COBRO SOBRE PORCENTAJE DEL VALOR DE LA FACTURA COMERCIAL O MINIMA
            case "158": // COBRO SOBRE PORCENTAJE DEL VALOR DE LA TOTALIDAD DE LAS FACTURAS COMERCIALES O MINIMA
    
              if ($pArrayParametros['nMin157'] == "" || $pArrayParametros['nMin157'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nPorFac157'] < 0 || $pArrayParametros['nPorFac157'] > 100) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje Valor Factura Debe ser Entre 0 - 100.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nMin157']."~".$pArrayParametros['nPorFac157']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
    
            case "238": //  Cobro Escalonado por cantidad de Kilos - Expo
            case "160": //  Cobro Escalonado por cantidad de Kilos
              $zNiveles = "";
    
              if ($pArrayParametros['nNiv160'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiIn1'] != 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior en el Primer Nivel debe ser Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSu'.$pArrayParametros['nNiv160']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv160'];$i++) {
    
                if ($pArrayParametros['vTarPor'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] < 0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Valor en el Nivel [".($i+1)."] no puede ser Vacio ni Menor a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if (($i+1) < $pArrayParametros['nNiv160']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarLiSu'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior, Limite Superior o Valor no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarLiIn'.($i+1)] >= $pArrayParametros['vTarLiSu'.($i+1)]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior no puede ser Mayor o Igual al Limite Superior en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior o Valor no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] != $pArrayParametros['vTarLiSu'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vTarLiIn'.($i+1)]."^".$pArrayParametros['vTarLiSu'.($i+1)]."^".substr($pArrayParametros['vTarPor'.($i+1)],strpos($pArrayParametros['vTarPor'.($i+1)],"."),strlen($pArrayParametros['vTarPor'.($i+1)]));
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$zNiveles."!|";
              } else {
                $nSwitch = 1; $zNiveles = ""; $cFcoTar = "";
              }
            break;
    
            case "239": 	// PORCENTAJE VALOR FOB O UNA MINIMA O UNA MAXIMA - EXPORTACIONES
            case "245":		// COBRO ESCALONADO SOBRE VALOR FOB EN USD CON MINIMA Y MAXIMA
            case "162": 	// PORCENTAJE VALOR FOB O UNA MINIMA O UNA MAXIMA
            case "161": 	// PORCENTAJE VALOR CIF O UNA MINIMA O UNA MAXIMA
            case "167": 	// COBRO ESCALONADO SOBRE VALOR CIF EN USD CON MINIMA Y MAXIMA
              $zNiveles = "";
    
              if ($pArrayParametros['nMin161'] < 0 || trim($pArrayParametros['nMin161']) == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Mayor o igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nMax161'] < 0 || trim($pArrayParametros['nMax161']) == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Maxima debe ser Mayor o igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nMin161'] >= $pArrayParametros['nMax161']) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Maxima debe ser Mayor al Valor de la Minima.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nNiv161'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiIn1'] != 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior en el Primer Nivel debe ser Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSu'.$pArrayParametros['nNiv161']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv161'];$i++) {
    
                if ($pArrayParametros['vTarPor'.($i+1)] > 100) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] no puede ser Mayor a 100.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if ($pArrayParametros['vTarPor'.($i+1)] < 0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] no puede ser Menor a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if (($i+1) < $pArrayParametros['nNiv161']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarLiSu'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior, Limite Superior o Porcentaje no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarLiIn'.($i+1)] >= $pArrayParametros['vTarLiSu'.($i+1)]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior no puede ser Mayor o Igual al Limite Superior en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior o Porcentaje no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] != $pArrayParametros['vTarLiSu'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ( $vSysStr['financiero_aplica_validacion_niveles_cobro_escalonado'] == 'SI' ) {
                    if ($pArrayParametros['vTarPor'.($i+1)] >= $pArrayParametros['vTarPor'.$i]) {
                      $nSwitch = 1;
                      $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                      $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] debe ser Menor al Porcentaje en el Nivel [".$i."].";
                      $mReturn[count($mReturn)] = $cMsj;
                    }
                  }
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vTarLiIn'.($i+1)]."^".$pArrayParametros['vTarLiSu'.($i+1)]."^".substr($pArrayParametros['vTarPor'.($i+1)],strpos($pArrayParametros['vTarPor'.($i+1)],"."),strlen($pArrayParametros['vTarPor'.($i+1)]));
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nMin161']."~".$pArrayParametros['nMax161']."~".$zNiveles."!|";
              } else {
                $nSwitch = 1; $zNiveles = ""; $cFcoTar = "";
              }
            break;
            case "273": 	// COMISION ESCALONADO VALOR FIJO CON MINIMA Y MAXIMA
            case "1102": 	// COMISION ESCALONADO VALOR FIJO CON MINIMA Y MAXIMA
              $zNiveles = "";
    
              if ($pArrayParametros['nMin161'] < 0 || trim($pArrayParametros['nMin161']) == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Mayor o igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nMax161'] < 0 || trim($pArrayParametros['nMax161']) == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Maxima debe ser Mayor o igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nMin161'] >= $pArrayParametros['nMax161']) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Maxima debe ser Mayor al Valor de la Minima.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nNiv161'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiIn1'] != 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior en el Primer Nivel debe ser Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSu'.$pArrayParametros['nNiv161']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv161'];$i++) {
    
                if ($pArrayParametros['vTarPor'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)]<0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Valor Fijo en el Nivel [".($i+1)."] no puede ser Vacio o Menor a 0.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
    
                if (($i+1) > $pArrayParametros['nNiv161']) { // Ultimo nivel
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior o Valor Fijo no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] != $pArrayParametros['vTarLiSu'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vTarLiIn'.($i+1)]."^".$pArrayParametros['vTarLiSu'.($i+1)]."^".$pArrayParametros['vTarPor'.($i+1)];
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nMin161']."~".$pArrayParametros['nMax161']."~".$zNiveles."!|";
              } else {
                $nSwitch = 1; $zNiveles = ""; $cFcoTar = "";
              }
            break;
    
            case "242": // VALOR FIJO POR PRODUCTO O MINIMA
            case "165": // VALOR FIJO POR PRODUCTO O MINIMA
    
              if ($pArrayParametros['nMin165'] == "" || $pArrayParametros['nMin165'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nSim165'] == "" || $pArrayParametros['nSim165'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro de las SimCards no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nTer165'] == "" || $pArrayParametros['nTer165'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro de los Terminales no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nTab165'] == "" || $pArrayParametros['nTab165'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro de las Tablets no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nMod165'] == "" || $pArrayParametros['nMod165'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro de los Modems no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nMin165']."~".$pArrayParametros['nSim165']."~".$pArrayParametros['nTer165']."~".$pArrayParametros['nTab165']."~".$pArrayParametros['nMod165']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "243": // VALOR x HORAS O MINIMA
            case "166": // VALOR x HORAS O MINIMA
    
              if ($pArrayParametros['nMin166'] == "" || $pArrayParametros['nMin166'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nVlr166'] == "" || $pArrayParametros['nVlr166'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro de las Horas no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nMin166']."~".$pArrayParametros['nVlr166']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "244": // VALOR POR CANTIDAD DE DIM O MINIMA
    
              if ($pArrayParametros['nMin244'] == "" || $pArrayParametros['nMin244'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nVlr244'] == "" || $pArrayParametros['nVlr244'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro de las Declaraciones no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nMin244']."~".$pArrayParametros['nVlr244']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "168": // VALOR FIJO POR UNIDAD CON MINIMA
            case "175": // VALOR FIJO POR UNIDAD CON MINIMA
            case "247": // VALOR FIJO POR UNIDAD CON MINIMA
            case "252": // VALOR FIJO POR UNIDAD CON MINIMA
            case "308": // VALOR FIJO POR UNIDAD CON MINIMA
              // Validando que el Valor Fijo sea Mayor a Cero
              if (intval($pArrayParametros['nValUni168']) < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Unidad Debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              // Fin de Validando que el Valor Fijo sea Mayor a Cero
    
              // Validando que el Valor Minimo sea Mayor a Cero
              if ($pArrayParametros['nMin168'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nValUni168']."~".$pArrayParametros['nMin168']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "169": // VALOR FIJO POR HORAS
              // Validando que el Valor Horas Diurnas sea Valido
              if ($pArrayParametros['nHorDiu169'] == "" || $pArrayParametros['nHorDiu169'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de Horas Diurnas no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              // Validando que el Valor Horas Nocturnas sea Valido
              if ($pArrayParametros['nHorNoc169'] == "" || $pArrayParametros['nHorNoc169'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de Horas Nocturnas no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              // Validando que el Valor Horas Dominicales sea Valido
              if ($pArrayParametros['nHorDom169'] == "" || $pArrayParametros['nHorDom169'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de Horas Dominicales no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              // Validando que el Valor Horas Festivos sea Valido
              if ($pArrayParametros['nHorFes169'] == "" || $pArrayParametros['nHorFes169'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de Horas Festivos no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nHorDiu169']."~".$pArrayParametros['nHorNoc169']."~".$pArrayParametros['nHorDom169']."~".$pArrayParametros['nHorFes169']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "170": // VALOR FIJO POR CANTIDAD DE VEHICULOS CON MINIMA
            case "248": // VALOR FIJO POR CANTIDAD DE VEHICULOS CON MINIMA
              // Validando que el Valor Fijo sea Mayor a Cero
              if (intval($pArrayParametros['nValFij170']) < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Fijo Debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              // Fin de Validando que el Valor Fijo sea Mayor a Cero
    
              // Validando que el Valor Minimo sea Mayor a Cero
              if ($pArrayParametros['nMin170'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nValFij170']."~".$pArrayParametros['nMin170']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "171": // VALOR FIJO x SUFIJO
              // Validando valor del sufijo 001
              if (intval($pArrayParametros['nSuf001171']) < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Sufijo 001 Debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando valor del sufijo 001
              if ($pArrayParametros['nSuf002171'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Sufijo 002 Debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nSuf001171']."~".$pArrayParametros['nSuf002171']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "172": // VALOR FIJO x CIF
              // Validando valor del Valor CIF
              if (intval($pArrayParametros['nValCif172']) < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Hasta del CIF Debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando valor fijo
              if ($pArrayParametros['nValFij172'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Fijo Debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nValCif172']."~".$pArrayParametros['nValFij172']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "177": // VALOR POR HORAS CON MINIMA
              // Validando valor de la minima
              if (intval($pArrayParametros['nMin177']) < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima Debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando valor hora ordinaria
              if ($pArrayParametros['nHorOrd177'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Hora Ordinaria Debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando valor hora festiva
              if ($pArrayParametros['nHorFes177'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Hora Festiva Debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nMin177']."~".$pArrayParametros['nHorOrd177']."~".$pArrayParametros['nHorFes177']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "178": // COBRO POR UNIDAD DE CARGA CON MINIMA
    
              if ($pArrayParametros['nC20178'] == "" || $pArrayParametros['nC20178'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro de los Contendores de 20 no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nC40178'] == "" || $pArrayParametros['nC40178'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro de los Contendores de 40 no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nCaS178'] == "" || $pArrayParametros['nCaS178'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor para el Cobro de la Carga Suelta no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nMinC20178'] == "" || $pArrayParametros['nMinC20178'] <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima para los Contendores de 20 Debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nMinC40178'] == "" || $pArrayParametros['nMinC40178'] <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima para los Contendores de 40 Debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nMinCaS178'] == "" || $pArrayParametros['nMinCaS178'] <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima para la Carga Suelta Debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nC20178']."~".$pArrayParametros['nC40178']."~".$pArrayParametros['nCaS178']."~".$pArrayParametros['nMinC20178']."~".$pArrayParametros['nMinC40178']."~".$pArrayParametros['nMinCaS178']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "179": // Cobro Variable + % Variable
            case "254": // Cobro Variable + % Variable
              // Validando que el Porcentaje del CIF sea Mayor a Cero
              if ($pArrayParametros['nPorVar179'] < 0 || $pArrayParametros['nPorVar179'] > 100 || $pArrayParametros['nPorVar179'] == "" || $pArrayParametros['nPorVar179'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje del Valor Variable debe ser Entre 0 y 100.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|{$pArrayParametros['nPorVar179']}|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "180": // Cobro por unidad con maxima y + Cobro Variable + % Variable uniades adiacionales
    
              /*** Validando que las cantidades iniciales sea mayor a 0. ***/
              if ($pArrayParametros['nCanIni180'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "La Cantidades Iniciales debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              /*** Validando que el valor inicial sea mayor a 0. ***/
              if ($pArrayParametros['nValIni180'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Inicial debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              /*** Validando que el Porcentaje del CIF sea Mayor a Cero ***/
              if ($pArrayParametros['nPorAdi180'] < 0 || $pArrayParametros['nPorAdi180'] > 100 || $pArrayParametros['nPorAdi180'] == "" || $pArrayParametros['nPorAdi180'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje del Valor Adicional debe ser Entre 0 y 100.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|{$pArrayParametros['nCanIni180']}~{$pArrayParametros['nValIni180']}~{$pArrayParametros['nPorAdi180']}|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "181": // % CIF Escalonado por intervalos
    
              /*** Validando que los dias del intervalo sean mayor a cero. ***/
              if (($pArrayParametros['nDiaInt181']+0) <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "Los Dias del Intervalo deben ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              /*** Validando que el Porcentaje del CIF sea Mayor a Cero ***/
              if ($pArrayParametros['nPorCif181'] < 0 || $pArrayParametros['nPorCif181'] > 1 || $pArrayParametros['nPorCif181'] == "" || $pArrayParametros['nPorCif181'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje CIF debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              /*** Validando que el valor de la minima sea mayor a 0. ***/
              if (($pArrayParametros['nMin181']+0) <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|{$pArrayParametros['nDiaInt181']}~{$pArrayParametros['nPorCif181']}~{$pArrayParametros['nMin181']}|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "255": // Valor por posición de acuerdo a referencia
            case "182": // Valor por posición de acuerdo a referencia
    
              /*** Validando que el valor de la tonelada sea mayor a cero. ***/
              if (($pArrayParametros['nValTon182']+0) <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Tonelada debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              /*** Validando que la cantidad maxima sea mayor a cero ***/
              if (($pArrayParametros['nCanMax182']+0) <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "La Cantidad Maxima de Toneladas debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              /*** Validando que el Porcentaje del CIF sea Mayor a Cero ***/
              if ($pArrayParametros['nPorAdi182'] < 0 || $pArrayParametros['nPorAdi182'] > 100 || $pArrayParametros['nPorAdi182'] == "" || $pArrayParametros['nPorAdi182'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje del Valor Adicional debe ser Entre 0 y 100.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|{$pArrayParametros['nValTon182']}~{$pArrayParametros['nCanMax182']}~{$pArrayParametros['nPorAdi182']}|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "183": // Valor por posición de acuerdo a referencia
    
              /*** Validando que el valor de la tonelada sea mayor a cero. ***/
              if (($pArrayParametros['nValEst183']+0) <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Estiba debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              /*** Validando que la Minima de Contenedores de 20 sea mayor a cero ***/
              if (($pArrayParametros['nMinC20183']+0) <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima para Contenedores de 20 debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              /*** Validando que la Minima de Contenedores de 40 sea mayor a cero ***/
              if (($pArrayParametros['nMinC40183']+0) <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima para Contenedores de 20 debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|{$pArrayParametros['nValEst183']}~{$pArrayParametros['nMinC20183']}~{$pArrayParametros['nMinC40183']}|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "184": // Cobro escalonado por cantidad de quincenas
    
              /*** Validando que el valor de la quincena de vehiculos sea mayor a cero. ***/
              if (($pArrayParametros['nQuiVeh184']+0) <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Quincena Vehiculo debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              /*** Validando que el valor de la quincena de camioneta sea mayor a cero. ***/
              if (($pArrayParametros['nQuiCta184']+0) <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Quincena Camioneta debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              /*** Validando que el valor de la quincena de camion sea mayor a cero. ***/
              if (($pArrayParametros['nQuiCam184']+0) <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Quincena Camion debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              /*** Validando que el valor de la quincena de montacarga sea mayor a cero. ***/
              if (($pArrayParametros['nQuiMon184']+0) <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Quincena Montacarga debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|{$pArrayParametros['nQuiVeh184']}~{$pArrayParametros['nQuiCta184']}~{$pArrayParametros['nQuiCam184']}~{$pArrayParametros['nQuiMon184']}|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "185": // Cobro escalonado por cantidad de meses
    
              /*** Validando que el valor del mes de vehiculos sea mayor a cero. ***/
              if (($pArrayParametros['nMesVeh185']+0) <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Mes Vehiculo debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              /*** Validando que el valor del mes de camioneta sea mayor a cero. ***/
              if (($pArrayParametros['nMesCta185']+0) <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Mes Camioneta debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              /*** Validando que el valor del mes de camion sea mayor a cero. ***/
              if (($pArrayParametros['nMesCam185']+0) <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Mes Camion debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              /*** Validando que el valor del mes de montacarga sea mayor a cero. ***/
              if (($pArrayParametros['nMesMon185']+0) <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Mes Montacarga debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|{$pArrayParametros['nMesVeh185']}~{$pArrayParametros['nMesCta185']}~{$pArrayParametros['nMesCam185']}~{$pArrayParametros['nMesMon185']}|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "256": // COBRO ESCALONADO CON MÍNIMA EN EL ÚLTIMO RANGO
            case "186": // COBRO ESCALONADO CON MÍNIMA EN EL ÚLTIMO RANGO
              $zNiveles = "";
    
              /*** Validando que el Porcentaje del CIF sea Mayor a Cero ***/
              if ($pArrayParametros['nPorCif186'] < 0 || $pArrayParametros['nPorCif186'] > 1 || $pArrayParametros['nPorCif186'] == "" || $pArrayParametros['nPorCif186'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje CIF debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nNiv186'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiIn1'] != 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior en el Primer Nivel debe ser Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSu'.$pArrayParametros['nNiv186']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv186'];$i++) {
    
                if (($i+1) < $pArrayParametros['nNiv186']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarLiSu'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior, Limite Superior o Valor Fijo no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarLiIn'.($i+1)] >= $pArrayParametros['vTarLiSu'.($i+1)]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior no puede ser Mayor o Igual al Limite Superior en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior o Minima no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] != $pArrayParametros['vTarLiSu'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vTarLiIn'.($i+1)]."^".$pArrayParametros['vTarLiSu'.($i+1)]."^".substr($pArrayParametros['vTarPor'.($i+1)],strpos($pArrayParametros['vTarPor'.($i+1)],"."),strlen($pArrayParametros['vTarPor'.($i+1)]));
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nPorCif186']."~".$zNiveles."!|";
              } else {
                $nSwitch = 1; $zNiveles = ""; $cFcoTar = "";
              }
            break;
            case "261": // COBRO SECUENCIAL POR CANTIDAD DE VEHICULOS
            case "191": // COBRO SECUENCIAL POR CANTIDAD DE VEHICULOS
              $zNiveles = "";
              if ($pArrayParametros['nNiv191'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de Cantidad debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv191'];$i++) {
                if (($pArrayParametros['vValCan'.($i+1)]+0) <= 0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Valor en el Nivel [".($i+1)."] Debe Ser Mayor a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vSecCan'.($i+1)]."^".$pArrayParametros['vValCan'.($i+1)];
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|"."~".$zNiveles."!|";
              } else {
                $nSwitch = 1; $zNiveles = ""; $cFcoTar = "";
              }
            break;
            case "263": // COBRO ESCALONADO CONTENEDORES DE 20 Y 40 VALOR FIJO POR UNIDAD
            case "193": // COBRO ESCALONADO CONTENEDORES DE 20 Y 40 VALOR FIJO POR UNIDAD
            case "262": // COBRO ESCALONADO CONTENEDORES DE 20 Y 40 VALOR FIJO POR NIVEL
            case "192": // COBRO ESCALONADO CONTENEDORES DE 20 Y 40 VALOR FIJO POR NIVEL
              $zNiveles  = "";
              $zNiveles2 = "";
              // validaciones para los contenedores de 20
              if ($pArrayParametros['nNivC20192'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles Para Cont. 20 debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiInC201'] != 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior Para Cont. 20 en el Primer Nivel debe ser Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSuC20'.$pArrayParametros['nNivC20192']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior Para Cont. 20 en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNivC20192'];$i++) {
                if ($pArrayParametros['vValorC20'.($i+1)] < 0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Valor en el Nivel [".($i+1)."] Para Cont. 20 no puede ser Menor a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if (($i+1) < $pArrayParametros['nNivC20192']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarLiInC20'.($i+1)] == "" || $pArrayParametros['vTarLiSuC20'.($i+1)] == "" || $pArrayParametros['vValorC20'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior, Limite Superior o Valor x Unidad Para Cont. 20 no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarLiInC20'.($i+1)] >= $pArrayParametros['vTarLiSuC20'.($i+1)]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Para Cont. 20 el Limite Inferior no puede ser Mayor o Igual al Limite Superior en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarLiInC20'.($i+1)] == "" || $pArrayParametros['vValorC20'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior o Valor x Unidad Para Cont. 20 no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiInC20'.($i+1)] != $pArrayParametros['vTarLiSuC20'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Para Cont. 20 el Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vTarLiInC20'.($i+1)]."^".$pArrayParametros['vTarLiSuC20'.($i+1)]."^".substr($pArrayParametros['vValorC20'.($i+1)],strpos($pArrayParametros['vValorC20'.($i+1)],"."),strlen($pArrayParametros['vValorC20'.($i+1)]));
                } else {
                  $nSwitch = 1;
                }
              } // for ($i=0;$i<$pArrayParametros['nNivC20192'];$i++)
    
              // validaciones para los contenedores de 40
              if ($pArrayParametros['nNivC40192'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles Para Cont. 40 debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiInC401'] != 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior Para Cont. 40 en el Primer Nivel debe ser Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSuC40'.$pArrayParametros['nNivC40192']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior Para Cont. 40 en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNivC40192'];$i++) {
                if ($pArrayParametros['vValorC40'.($i+1)] < 0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Valor en el Nivel [".($i+1)."] Para Cont. 40 no puede ser Menor a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if (($i+1) < $pArrayParametros['nNivC40192']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarLiInC40'.($i+1)] == "" || $pArrayParametros['vTarLiSuC40'.($i+1)] == "" || $pArrayParametros['vValorC40'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior, Limite Superior o Valor x Unidad Para Cont. 40 no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarLiInC40'.($i+1)] >= $pArrayParametros['vTarLiSuC40'.($i+1)]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Para Cont. 40 el Limite Inferior no puede ser Mayor o Igual al Limite Superior en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarLiInC40'.($i+1)] == "" || $pArrayParametros['vValorC40'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior o Valor x Unidad Para Cont. 40 no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiInC40'.($i+1)] != $pArrayParametros['vTarLiSuC40'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Para Cont. 40 el Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
    
                if ($nSwitch == 0) {
                  $zNiveles2 .= "!".$pArrayParametros['vTarLiInC40'.($i+1)]."^".$pArrayParametros['vTarLiSuC40'.($i+1)]."^".substr($pArrayParametros['vValorC40'.($i+1)],strpos($pArrayParametros['vValorC40'.($i+1)],"."),strlen($pArrayParametros['vValorC40'.($i+1)]));
                } else {
                  $nSwitch = 1;
                }
              } // for ($i=0;$i<$pArrayParametros['nNivC40192'];$i++)
    
              if ($nSwitch == 0) {
                $cFcoTar = "|"."~".$zNiveles."~".$zNiveles2."!|";
              } else {
                $nSwitch = 1; $zNiveles = ""; $zNiveles2 = ""; $cFcoTar = "";
              }
            break;
            case "264": // COBRO CONTENEDORES DE 20 Y DE 40 VALOR FIJO
            case "195": // COBRO CONTENEDORES DE 20 Y DE 40 VALOR POR UNIDAD
            case "265": // COBRO CONTENEDORES DE 20 Y DE 40 VALOR POR UNIDAD
            case "194": // COBRO CONTENEDORES DE 20 Y DE 40 VALOR FIJO
              // Validando que el Valor Fijo del cont 20 sea Mayor a Cero
              if ($pArrayParametros['nValFijC20194'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Fijo del Contenedor 20 Debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              // Fin de Validando que el Valor Fijo sea Mayor a Cero
    
              // Validando que el Valor Fijo del cont 40 sea Mayor a Cero
              if ($pArrayParametros['nValFijC40194'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Fijo del Contenedor 40 Debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nValFijC20194']."~".$pArrayParametros['nValFijC40194']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
    
            case "266": // PORCENTAJE VALOR CIF CON MAXIMA
            case "196": // PORCENTAJE VALOR CIF CON MAXIMA
              // Validando que el Porcentaje del CIF sea Mayor a Cero
              if ($pArrayParametros['nPor196'] < 0 || $pArrayParametros['nPor196'] > 1 || $pArrayParametros['nPor196'] == "" || $pArrayParametros['nPor196'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje del Valor CIF debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Minimo sea Mayor a Cero
              if ($pArrayParametros['nMax196'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Maxima debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".substr($pArrayParametros['nPor196'],strpos($pArrayParametros['nPor196'],"."),strlen($pArrayParametros['nPor196']))."~".$pArrayParametros['nMax196']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "267": // COMISION POR TONELADAS CON MINIMA
              if ($pArrayParametros['nVal267'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de Toneladas Debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              // Fin de Validando que el Valor Fijo sea Mayor a Cero
    
              // Validando que el Valor Fijo del cont 40 sea Mayor a Cero
              if ($pArrayParametros['nMin267'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Minimo Debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nVal267']."~".$pArrayParametros['nMin267']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "268": // COMISION POR PORCENTAJE
              // Validando que el Valor Fijo sea Mayor a Cero y menor a 1
              if ($pArrayParametros['nVal268'] < 0 || $pArrayParametros['nVal268'] > 1 || $pArrayParametros['nVal268'] == "" || $pArrayParametros['nVal268'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del % FOB Debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              // Fin de Validando que el Valor Fijo sea Mayor a Cero
    
              if ($nSwitch == 0) {
                $cFcoTar = "|{$pArrayParametros['nVal268']}|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "197":
              // Validando que el Valor sea Mayor a Cero
              if ($pArrayParametros['nVlr197'] == "" || $pArrayParametros['nVlr197'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de las Horas no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que las Horas sea Mayor a Cero
              if ($pArrayParametros['nHor197'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "Las Horas deben ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Adicional sea Mayor a Cero
              if ($pArrayParametros['nAdi197'] == "" || $pArrayParametros['nAdi197'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Hora Adicional no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que la Maxima sea Mayor a Cero
              if ($pArrayParametros['nMax197'] == "" || $pArrayParametros['nMax197'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Maxima no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nVlr197']."~".$pArrayParametros['nHor197']."~".$pArrayParametros['nAdi197']."~".$pArrayParametros['nMax197']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "269": // COBRO ESCALONADO SOBRE VALOR CIF EN USD CON MINIMA Y MAXIMA
              $zNiveles = "";
    
              if ($pArrayParametros['nMin269'] < 0 || trim($pArrayParametros['nMin269']) == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Mayor o igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nMax269'] < 0 || trim($pArrayParametros['nMax269']) == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Maxima debe ser Mayor o igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nMin269'] >= $pArrayParametros['nMax269']) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Maxima debe ser Mayor al Valor de la Minima.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nNiv269'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiIn1'] != 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior en el Primer Nivel debe ser Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSu'.$pArrayParametros['nNiv269']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv269'];$i++) {
    
                if ($pArrayParametros['vTarPor'.($i+1)] > 100) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] no puede ser Mayor a 100.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if ($pArrayParametros['vTarPor'.($i+1)] < 0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] no puede ser Menor a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if (($i+1) < $pArrayParametros['nNiv269']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarLiSu'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior, Limite Superior o Porcentaje no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarLiIn'.($i+1)] >= $pArrayParametros['vTarLiSu'.($i+1)]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior no puede ser Mayor o Igual al Limite Superior en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior o Porcentaje no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] != $pArrayParametros['vTarLiSu'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                /*  if ( $vSysStr['financiero_aplica_validacion_niveles_cobro_escalonado'] == 'SI' ) {
                    if ($pArrayParametros['vTarPor'.($i+1)] >= $pArrayParametros['vTarPor'.$i]) {
                      $nSwitch = 1;
                      $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                      $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] debe ser Menor al Porcentaje en el Nivel [".$i."].";
                      $mReturn[count($mReturn)] = $cMsj;
                    }
                  }*/
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vTarLiIn'.($i+1)]."^".$pArrayParametros['vTarLiSu'.($i+1)]."^".substr($pArrayParametros['vTarPor'.($i+1)],strpos($pArrayParametros['vTarPor'.($i+1)],"."),strlen($pArrayParametros['vTarPor'.($i+1)]));
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nMin269']."~".$pArrayParametros['nMax269']."~".$zNiveles."!|";
              } else {
                $nSwitch = 1; $zNiveles = ""; $cFcoTar = "";
              }
            break;
            case "198": // PORCENTAJE sobre comision
              // Validando que el Porcentaje del CIF sea Mayor a Cero
              if ($pArrayParametros['nPor198'] < 0 || $pArrayParametros['nPor198'] > 1 || $pArrayParametros['nPor198'] == "" || $pArrayParametros['nPor198'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje del Valor CIF debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor sobre comision sea Mayor a Cero
              if ($pArrayParametros['nSob198'] < 0 || $pArrayParametros['nSob198'] > 100 || $pArrayParametros['nSob198'] == "" || $pArrayParametros['nSob198'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje de la Sobre Comision debe ser Mayor a Cero y Menor o Igual a Cien.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".substr($pArrayParametros['nPor198'],strpos($pArrayParametros['nPor198'],"."),strlen($pArrayParametros['nPor198']))."~".$pArrayParametros['nSob198']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "271": // VALOR FIJO CONSOLIDADO POR CUBICAJE Y DO
            case "1100": // VALOR FIJO CONSOLIDADO POR CUBICAJE Y DO
              // Validando que el Valor de Tramite sea Mayor a Cero
              if ($pArrayParametros['nVal1100'] < 0 ) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de Tramite debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nVal1100']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "272": // VALOR FIJO CONSOLIDADO POR CUBICAJE Y DO
            case "1101": // VALOR FIJO CONSOLIDADO POR CUBICAJE Y DO
              // Validando que el Valor de Tramite sea Mayor a Cero
              if ($pArrayParametros['nTas1101'] < 0 ) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de Tasa Pactada debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              if ($pArrayParametros['nPor1101'] < 0 || $pArrayParametros['nPor1101'] > 1 || $pArrayParametros['nPor1101'] == "" || $pArrayParametros['nPor1101'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              if ($pArrayParametros['nMin1101'] < 0 ) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de Minima debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nTas1101']."~".$pArrayParametros['nPor1101']."~".$pArrayParametros['nMin1101']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "274": // COBRO SOBRE PORCENTAJE DE NEGOCIACION
            case "1103": // COBRO SOBRE PORCENTAJE DE NEGOCIACION
              // Validando que el Valor de Tramite sea Mayor a Cero
              if ($pArrayParametros['nTar1103'] < 0 ) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Tarifa debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              if ($pArrayParametros['nPor1103'] < 0 || $pArrayParametros['nPor1103'] > 100 || $pArrayParametros['nPor1103'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje debe ser Mayor a Cero y Menor o Igual a Cien.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nTar1103']."~".$pArrayParametros['nPor1103']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "1104": // COBRO SOBRE PORCENTAJE DE NEGOCIACION
              // Validando que el Valor de Tramite sea Mayor a Cero
              if ($pArrayParametros['nPor1104'] < 0 || $pArrayParametros['nPor1104'] > 1 || $pArrayParametros['nPor1104'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nPor1104']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "1105": // COBRO % SOBRE VALOR CIF (CONDICIONES ESPECIALES) CON MINIMA Y MAXIMA
              // Validando que el Valor COBRO % SOBRE VALOR CIF sea mayor a cero y menos que 1
              if ($pArrayParametros['nPor1105'] < 0 || $pArrayParametros['nPor1105'] > 1 || $pArrayParametros['nPor1105'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              if ($pArrayParametros['nMin1105'] >= $pArrayParametros['nMax1105']) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Maxima debe ser Mayor al Valor de la Minima.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              if ($pArrayParametros['nMax1105'] < 0 || trim($pArrayParametros['nMax1105']) == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Maxima debe ser Mayor o igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nPor1105']."~".$pArrayParametros['nMin1105']."~".$pArrayParametros['nMax1105']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "1106": // COBRO ESCALONADO SOBRE VALOR CIF EN USD
              $zNiveles = "";
    
              if ($pArrayParametros['nMin1106'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Mayor o igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              // Validando que el % de descuento este entre 0-99
              if ($pArrayParametros['nPorDes1106'] < 0 || $pArrayParametros['nPorDes1106'] > 99 || $pArrayParametros['nPorDes1106'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Descuento Debe estar entre [0-99].";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nNiv1106'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiIn1'] != 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior en el Primer Nivel debe ser Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSu'.$pArrayParametros['nNiv1106']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv1106'];$i++) {
    
                if ($pArrayParametros['vTarPor'.($i+1)] > 100) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] no puede ser Mayor a 100.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if ($pArrayParametros['vTarPor'.($i+1)] < 0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] no puede ser Menor a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if (($i+1) < $pArrayParametros['nNiv1106']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarLiSu'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior, Limite Superior o Porcentaje no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarLiIn'.($i+1)] >= $pArrayParametros['vTarLiSu'.($i+1)]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior no puede ser Mayor o Igual al Limite Superior en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior o Porcentaje no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] != $pArrayParametros['vTarLiSu'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ( $vSysStr['financiero_aplica_validacion_niveles_cobro_escalonado'] == 'SI' ) {
                    if ($pArrayParametros['vTarPor'.($i+1)] >= $pArrayParametros['vTarPor'.$i]) {
                      $nSwitch = 1;
                      $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                      $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] debe ser Menor al Porcentaje en el Nivel [".$i."].";
                      $mReturn[count($mReturn)] = $cMsj;
                    }
                  }
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vTarLiIn'.($i+1)]."^".$pArrayParametros['vTarLiSu'.($i+1)]."^".substr($pArrayParametros['vTarPor'.($i+1)],strpos($pArrayParametros['vTarPor'.($i+1)],"."),strlen($pArrayParametros['vTarPor'.($i+1)]));
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nMin1106']."~".$zNiveles."!"."~".$pArrayParametros['nPorDes1106']."|";
              } else {
                $nSwitch = 1; $zNiveles = ""; $cFcoTar = "";
              }
            break;
            case "1107": // PORCENTAJE O MÍNIMA POR CARGA GRANEL O CONTENEDOR
              // Validando que el Porcentaje del CIF y la Minima sea Mayor a Cero
              if ($pArrayParametros['nPCifc1107'] < 0 || $pArrayParametros['nPCifc1107'] > 1 || $pArrayParametros['nPCifc1107'] == "" || $pArrayParametros['nPCifc1107'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje del Valor CIF en Contenedor debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nMinC1107'] < 0 ||  $pArrayParametros['nMinC1107'] == "" || $pArrayParametros['nMinC1107'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima en Contenedor, debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nPCifg1107'] < 0 || $pArrayParametros['nPCifg1107'] > 1 || $pArrayParametros['nPCifg1107'] == "" || $pArrayParametros['nPCifg1107'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje del Valor CIF en Granel debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nMinG1107'] < 0 ||  $pArrayParametros['nMinG1107'] == "" || $pArrayParametros['nMinG1107'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima en Granel, debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|{$pArrayParametros['nPCifc1107']}~{$pArrayParametros['nMinC1107']}|{$pArrayParametros['nPCifg1107']}~{$pArrayParametros['nMinG1107']}|";
              } else {
                $nSwitch = 1;
              }
            break;
            case "1108": // COBRO ESCALONADO PORCENTAJE O MÍNIMA POR CANTIDAD BL
              $zNiveles = "";
    
              // Validando que el % de descuento este entre 0-1
              if ($pArrayParametros['nPCif1108'] <= 0 || $pArrayParametros['nPCif1108'] > 1 || $pArrayParametros['nPCif1108'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor CIF debe ser mayor a cero y menor que 1.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nNiv1108'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiIn1'] != 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior en el Primer Nivel debe ser Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSu'.$pArrayParametros['nNiv1108']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv1108'];$i++) {
    
                if (($i+1) < $pArrayParametros['nNiv1108']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarLiSu'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior, Limite Superior o valor minima no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarLiIn'.($i+1)] >= $pArrayParametros['vTarLiSu'.($i+1)]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior no puede ser Mayor o Igual al Limite Superior en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior o valor minima no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] != $pArrayParametros['vTarLiSu'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
    
                if ($nSwitch == 0) {
                  //$zNiveles .= "!".$pArrayParametros['vTarLiIn'.($i+1)]."^".$pArrayParametros['vTarLiSu'.($i+1)]."^".substr($pArrayParametros['vTarPor'.($i+1)],strpos($pArrayParametros['vTarPor'.($i+1)],"."),strlen($pArrayParametros['vTarPor'.($i+1)]));
                  $zNiveles .= "!".$pArrayParametros['vTarLiIn'.($i+1)]."^".$pArrayParametros['vTarLiSu'.($i+1)]."^".$pArrayParametros['vTarPor'.($i+1)];
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nPCif1108']."~".$zNiveles."!|";
              } else {
                $nSwitch = 1; $zNiveles = ""; $cFcoTar = "";
              }
            break;
            case "1109": // PORCENTAJE CIF O MÍNIMA POR CARGA GRANEL O CONTENEDOR POR NIVEL O TIPO DE MERCANCÍA
              $zNiveles = "";
              if ($pArrayParametros['nNiv1109'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de Cantidad debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv1109'];$i++) {
                if (($pArrayParametros['vValCif'.($i+1)]+0) <= 0 || ($pArrayParametros['vValCif'.($i+1)]+0) > 1) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Valor en el Nivel [".($i+1)."] Debe Ser Mayor a Cero y menor que 1.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
                if (($pArrayParametros['vValMin'.($i+1)]+0) <= 0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Valor en el Nivel [".($i+1)."] Debe Ser Mayor a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vSecCan'.($i+1)]."^".$pArrayParametros['vValCif'.($i+1)]."^".$pArrayParametros['vValMin'.($i+1)];
                } else {
                  $nSwitch = 1;
                }
              }
    
              for($j=0;$j<$pArrayParametros['nNiv1109'];$j++){
                for($k=$j+1;$k<$pArrayParametros['nNiv1109'];$k++){
                  if(($pArrayParametros['vValCif'.($j+1)]+0) == ($pArrayParametros['vValCif'.($k+1)]+0) && ($pArrayParametros['vValMin'.($j+1)]+0) == ($pArrayParametros['vValMin'.($k+1)]+0)){
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Valores en el Nivel [".($j+1)."] Debe Ser Diferente al Nivel [".($k+1)."] . ";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|"."~".$zNiveles."!|";
              } else {
                $nSwitch = 1; $zNiveles = ""; $cFcoTar = "";
              }
            break;
            case "1110":
              // Validando que el Valor sea Mayor a Cero
              if ($pArrayParametros['nVlr1110'] == "" || $pArrayParametros['nVlr1110'] <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de las Horas Iniciales debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que las Horas sea Mayor a Cero
              if ($pArrayParametros['nHor1110'] <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "Las Horas deben ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Adicional sea Mayor a Cero
              if ($pArrayParametros['nAdi1110'] == "" || $pArrayParametros['nAdi1110'] <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Hora Adicional debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nVlr1110']."~".$pArrayParametros['nHor1110']."~".$pArrayParametros['nAdi1110']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;				
            case "276":
              // Validando que el Porcentaje del CIF sea Mayor a Cero
              if ($pArrayParametros['nPor276'] < 0 || $pArrayParametros['nPor276'] > 1 || $pArrayParametros['nPor276'] == "" || $pArrayParametros['nPor276'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje del Valor FOB debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Minimo sea Mayor a Cero
              if ($pArrayParametros['nMin276'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el % de descuento este entre 0-99
              if ($pArrayParametros['nPorDes276'] < 0 || $pArrayParametros['nPorDes276'] > 99 || $pArrayParametros['nPorDes276'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Descuento Debe estar entre [0-99].";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".substr($pArrayParametros['nPor276'],strpos($pArrayParametros['nPor276'],"."),strlen($pArrayParametros['nPor276']))."~".$pArrayParametros['nMin276']."~".$pArrayParametros['nPorDes276']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "1111":
            case "1118":
            case "277":
            case "278":
            case "310":
            case "311":
              // Validando que el Porcentaje del CIF sea Mayor a Cero
              if ($pArrayParametros['nPor1111'] < 0 || $pArrayParametros['nPor1111'] > 100 || $pArrayParametros['nPor1111'] == "" || $pArrayParametros['nPor1111'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje de Cobro debe ser Mayor a Cero y Menor o Igual a Cien.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nPor1111']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "1112": // CANTIDAD TONELADAS CON UNA MINIMA O MAXIMA
              // Validando que el Valor de la tonelada sea Mayor a Cero
              if ($pArrayParametros['nVal1112'] <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Tonelada debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Minimo sea Mayor a Cero
              if ($pArrayParametros['nMin1112'] <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Maximo sea Mayor a Cero,
              if ($pArrayParametros['nMax1112'] <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Maxima debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nVal1112']."~".$pArrayParametros['nMin1112']."~".$pArrayParametros['nMax1112']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "1113": // PORCENTAJE CIF O MÍNIMA POR CARGA GRANEL O CONTENEDOR POR NIVEL O TIPO DE MERCANCÍA
              $zNiveles = "";
              if ($pArrayParametros['nNiv1113'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de Cantidad debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv1113'];$i++) {
                if (($pArrayParametros['vValCif'.($i+1)]+0) <= 0 || ($pArrayParametros['vValCif'.($i+1)]+0) > 1) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Valor del % CIF en el Nivel [".($i+1)."] Debe Ser Mayor a Cero y menor que 1.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
                if (($pArrayParametros['vValMin'.($i+1)]+0) <= 0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Valor de la Mnima en el Nivel [".($i+1)."] Debe Ser Mayor a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
                if (($pArrayParametros['vValMin'.($i+1)]+0) <= 0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Valor de la Maxima en el Nivel [".($i+1)."] Debe Ser Mayor a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vSecCan'.($i+1)]."^".$pArrayParametros['vValCif'.($i+1)]."^".$pArrayParametros['vValMin'.($i+1)]."^".$pArrayParametros['vValMax'.($i+1)];
                } else {
                  $nSwitch = 1;
                }
              }
    
              for($j=0;$j<$pArrayParametros['nNiv1113'];$j++){
                for($k=$j+1;$k<$pArrayParametros['nNiv1113'];$k++){
                  if(($pArrayParametros['vValCif'.($j+1)]+0) == ($pArrayParametros['vValCif'.($k+1)]+0) && ($pArrayParametros['vValMin'.($j+1)]+0) == ($pArrayParametros['vValMin'.($k+1)]+0) && ($pArrayParametros['vValMax'.($j+1)]+0) == ($pArrayParametros['vValMax'.($k+1)]+0)){
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Valores en el Nivel [".($j+1)."] Debe Ser Diferente al Nivel [".($k+1)."] . ";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|"."~".$zNiveles."!|";
              } else {
                $nSwitch = 1; $zNiveles = ""; $cFcoTar = "";
              }
            break;
            case "1114": // VALOR CIF CON MINIMA + ITEMS
              // Validando que el Valor COBRO % SOBRE VALOR CIF sea mayor a cero y menos que 1
              if ($pArrayParametros['nPor1114'] < 0 || $pArrayParametros['nPor1114'] > 1 || $pArrayParametros['nPor1114'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              if ($pArrayParametros['nMin1114'] <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              if ($pArrayParametros['nIteIni1114'] <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "Las Unidades de los Items Iniciales debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              if ($pArrayParametros['nVlrIte1114'] <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de Item Adicional debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nPor1114']."~".$pArrayParametros['nMin1114']."~".$pArrayParametros['nIteIni1114']."~".$pArrayParametros['nVlrIte1114']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "1115": // CANTIDAD DE HORAS X CONTENEDOR X PERSONA
              // Validando que las Horas sea Mayor a Cero
              if ($pArrayParametros['nHraBlo1115'] <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "Las Horas x Bloque deben ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor sea Mayor a Cero
              if ($pArrayParametros['nVlrBlo1115'] <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Bloque debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nHraBlo1115']."~".$pArrayParametros['nVlrBlo1115']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "1116": // VALOR CIF CON MINIMA + ITEMS
              // Validando que el Valor COBRO % SOBRE VALOR CIF sea mayor a cero y menos que 1
              if ($pArrayParametros['nPor1116'] < 0 || $pArrayParametros['nPor1116'] > 1 || $pArrayParametros['nPor1116'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor de la Minima sea Mayor a Cero
              if ($pArrayParametros['nMin1116'] <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Adicional sea Mayor a Cero
              if ($pArrayParametros['nVlrAdi1116'] <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Fijo Adicional debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nPor1116']."~".$pArrayParametros['nMin1116']."~".$pArrayParametros['nVlrAdi1116']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "1119": // % CIF O MINIMA VARIABLE CONTENEDOR O DESCARGUE DIRECTO
              // Validando que el Valor % CIF sea mayor a cero y menos que 1
              if ($pArrayParametros['nCif1119'] < 0 || $pArrayParametros['nCif1119'] > 1 || $pArrayParametros['nCif1119'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor de la Minima sea Mayor a Cero
              if ($pArrayParametros['nMin1119'] <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor del Contenedor de 20 sea Mayor a Cero
              if ($pArrayParametros['nC201119'] <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Contenedor de 20 debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor del Contenedor de 40 sea Mayor a Cero
              if ($pArrayParametros['nC401119'] <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Contenedor de 40 debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor de Descargue Directo sea Mayor a Cero
              if ($pArrayParametros['nVlrDes1119'] <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de Descargue Directo debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nCif1119']."~".$pArrayParametros['nMin1119']."~".$pArrayParametros['nC201119']."~".$pArrayParametros['nC401119']."~".$pArrayParametros['nVlrDes1119']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "1120": // % CIF O MINIMA POR TIPO CONTENEDOR
              // Contenedores de 20
              // Validando que el Valor % CIF sea mayor a cero y menos que 1
              if ($pArrayParametros['nCifC201120'] < 0 || $pArrayParametros['nCifC201120'] > 1 || $pArrayParametros['nCifC201120'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El % Cif Para Cont. 20 debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor de la Minima sea Mayor a Cero
              if ($pArrayParametros['nMinC201120'] <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima Para Cont. 20 debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Contenedores de 40
              // Validando que el Valor % CIF sea mayor a cero y menos que 1
              if ($pArrayParametros['nCifC401120'] < 0 || $pArrayParametros['nCifC401120'] > 1 || $pArrayParametros['nCifC401120'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El % Cif Para Cont. 40 debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor de la Minima sea Mayor a Cero
              if ($pArrayParametros['nMinC401120'] <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima Para Cont. 40 debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Carga Suelta					
              // Validando que el Valor % CIF sea mayor a cero y menos que 1
              if ($pArrayParametros['nCifCs1120'] < 0 || $pArrayParametros['nCifCs1120'] > 1 || $pArrayParametros['nCifCs1120'] == "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El % Cif Para Carga Suelta debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor de la Minima sea Mayor a Cero
              if ($pArrayParametros['nMinCs1120'] <= 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima Para Carga Suelta debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nCifC201120']."~".$pArrayParametros['nMinC201120']."~".$pArrayParametros['nCifC401120']."~".$pArrayParametros['nMinC401120']."~".$pArrayParametros['nCifCs1120']."~".$pArrayParametros['nMinCs1120']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
    
            case "1121": // COBRO VARIABLE SEGUN CANTIDAD DE SACOS CON MINIMA
            case "280":  // COBRO VARIABLE SEGUN CANTIDAD DE SACOS CON MINIMA
              $zNiveles = "";
              if ($pArrayParametros['nNiv1121'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiIn1'] != 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior en el Primer Nivel debe ser Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSu'.$pArrayParametros['nNiv1121']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv1121'];$i++) {
                if ($pArrayParametros['vTarPor'.($i+1)] < 0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Valor en el Nivel [".($i+1)."] no puede ser Menor a Cero.";
                    $mReturn[count($mReturn)] = $cMsj;
                }
    
                if (($i+1) < $pArrayParametros['nNiv1121']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarLiSu'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior, Limite Superior o Valor x Unidad no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarLiIn'.($i+1)] >= $pArrayParametros['vTarLiSu'.($i+1)]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior no puede ser Mayor o Igual al Limite Superior en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior o Valor x Unidad no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] != $pArrayParametros['vTarLiSu'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vTarLiIn'.($i+1)]."^".$pArrayParametros['vTarLiSu'.($i+1)]."^".substr($pArrayParametros['vTarPor'.($i+1)],strpos($pArrayParametros['vTarPor'.($i+1)],"."),strlen($pArrayParametros['vTarPor'.($i+1)]));
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|"."~".$zNiveles."!|";
              } else {
                $nSwitch = 1; $zNiveles = ""; $cFcoTar = "";
              }
            break;
    
            case "1123": // VALOR POR PEDIDO CON VALOR ADICIONAL
            case "1125": // VALOR POR PEDIDO CON VALOR ADICIONAL POR UNIDAD MAS COMISION ADICIONAL
              // Validando que las Unidades Minimas sea Mayor a Cero
              if ($pArrayParametros['nUniMin1123'] == "" || $pArrayParametros['nUniMin1123'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "Las Unidades Minimas no Pueden ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Unidades Minimas sea Mayor a Cero
              if ($pArrayParametros['nVlrMin1123'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de las Unidades Minimas debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Adicional sea Mayor a Cero
              if ($pArrayParametros['nVlrAdi1123'] == "" || $pArrayParametros['nVlrAdi1123'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Adicional x Unidad Adicional no Puede ser Vacio ni Menor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nUniMin1123']."~".$pArrayParametros['nVlrMin1123']."~".$pArrayParametros['nVlrAdi1123']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "1126": // TRM OPERACION
              // Validando que el Costo Adicional sea Mayor a Cero
              if ($pArrayParametros['nCosAdi1126'] == "" || $pArrayParametros['nCosAdi1126'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Costo Adicional debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Fijo sea Mayor a Cero
              if ($pArrayParametros['nVlrFij1126'] == "" || $pArrayParametros['nVlrFij1126'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Fijo debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
      
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nCosAdi1126']."~".$pArrayParametros['nVlrFij1126']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "1127": // COBRO ESCALONADO POR CANTIDAD DE CONTENEDORES
              $zNiveles = "";
    
              if ($pArrayParametros['nMin1127'] == "" || $pArrayParametros['nMin1127'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nNiv1127'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiIn1'] != 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior en el Primer Nivel debe ser Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSu'.$pArrayParametros['nNiv1127']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv1127'];$i++) {
    
                if ($pArrayParametros['vTarPor'.($i+1)] < 1) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Valor en el Nivel [".($i+1)."] no puede ser Menor a Uno.";
                    $mReturn[count($mReturn)] = $cMsj;
                }
    
                if (($i+1) < $pArrayParametros['nNiv1127']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarLiSu'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior, Limite Superior o Valor no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarLiIn'.($i+1)] > $pArrayParametros['vTarLiSu'.($i+1)]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior no puede ser Mayor al Limite Superior en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos del Limite Inferior o Valor no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] != ($pArrayParametros['vTarLiSu'.$i] + 1)) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior mas Uno del Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarPor'.($i+1)] >= $pArrayParametros['vTarPor'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Valor en el Nivel [".($i+1)."] debe ser Menor al Valor en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vTarLiIn'.($i+1)]."^".$pArrayParametros['vTarLiSu'.($i+1)]."^".$pArrayParametros['vTarPor'.($i+1)];
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nMin1127']."~".$zNiveles."!|";
              } else {
                $nSwitch = 1; $zNiveles = ""; $cFcoTar = "";
              }
            break;
            case "282": // PORCENTAJE (%) A COBRAR SOBRE LOS PAGOS A TERCEROS (CONDICION ESPECIAL)
            case "1128": // PORCENTAJE (%) A COBRAR SOBRE LOS PAGOS A TERCEROS (CONDICION ESPECIAL)
              // Validando que el Valor del Porcentaje sea Mayor a Cero
              if ($pArrayParametros['nPor1128'] == "" || $pArrayParametros['nPor1128'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nPor1128']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "1129": // COBRO POR HOJA PRINCIPAL Y HOJA SECUNDARIA
              // Validando que el Valor de la Hoja Principal sea Mayor a Cero
              if ($pArrayParametros['nHojPpl1129'] == "" || $pArrayParametros['nHojPpl1129'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Hoja Principal debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor de la Hoja Adicional sea Mayor a Cero
              if ($pArrayParametros['nHojAdi1129'] == "" || $pArrayParametros['nHojAdi1129'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Hoja Adicional debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
      
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nHojPpl1129']."~".$pArrayParametros['nHojAdi1129']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "1130": // ESCALONADO POR %CIF + MINIMA
              $zNiveles = "";
    
              if ($pArrayParametros['nMin1130'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Mayor o igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nMinDes1130'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima de Descargue Directo debe ser Mayor o igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nNiv1130'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiIn1'] != 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior en el Primer Nivel debe ser Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSu'.$pArrayParametros['nNiv1130']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv1130'];$i++) {
    
                if ($pArrayParametros['vTarPor'.($i+1)] > 100) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] no puede ser Mayor a 100.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if ($pArrayParametros['vTarPor'.($i+1)] < 0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] no puede ser Menor a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if (($i+1) < $pArrayParametros['nNiv1130']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarLiSu'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior, Limite Superior o Porcentaje no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarLiIn'.($i+1)] >= $pArrayParametros['vTarLiSu'.($i+1)]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior no puede ser Mayor o Igual al Limite Superior en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior o Porcentaje no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] != $pArrayParametros['vTarLiSu'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vTarLiIn'.($i+1)]."^".$pArrayParametros['vTarLiSu'.($i+1)]."^".substr($pArrayParametros['vTarPor'.($i+1)],strpos($pArrayParametros['vTarPor'.($i+1)],"."),strlen($pArrayParametros['vTarPor'.($i+1)]));
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nMin1130']."~".$zNiveles."!"."~".$pArrayParametros['nMinDes1130']."|";
              } else {
                $nSwitch = 1; $zNiveles = ""; $cFcoTar = "";
              }
            break;
            case "1131": // VALOR FIJO DEPOSITO HABILITADO O DESCARGUE DIRECTO
            case "1136": // VALOR FIJO DEPOSITO HABILITADO O DESCARGUE DIRECTO EUROS
              // Validando que el Valor Fijo Deposito Habilitado sea Mayor a Cero
              if ($pArrayParametros['nVlDepHab1131'] == "" || $pArrayParametros['nVlDepHab1131'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Fijo Deposito Habilitado debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Fijo Descargue Directo sea Mayor a Cero
              if ($pArrayParametros['nDesDir1131'] == "" || $pArrayParametros['nDesDir1131'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Fijo Descargue Directo debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
      
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nVlDepHab1131']."~".$pArrayParametros['nDesDir1131']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "1132": // ESCALONADO POR CANTIDAD DE ITEM EN FACTURA
              $zNiveles = "";
    
              if ($pArrayParametros['nNiv1132'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Minima debe ser Mayor o igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nVlrItem1132'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Adicional x Item debe ser Mayor o igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nCanSer1132'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de la Cantidad Minima de Seriales debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['nVlrSer1132'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Adicional de Seriales debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiIn1'] != 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior en el Primer Nivel debe ser Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSu'.$pArrayParametros['nNiv1132']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv1132'];$i++) {
                if ($pArrayParametros['vTarPor'.($i+1)] < 0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Valor Fijo en el Nivel [".($i+1)."] no puede ser Menor a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if (($i+1) < $pArrayParametros['nNiv1132']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarLiSu'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior, Limite Superior o Porcentaje no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarLiIn'.($i+1)] >= $pArrayParametros['vTarLiSu'.($i+1)]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior no puede ser Mayor o Igual al Limite Superior en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior o Porcentaje no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] != $pArrayParametros['vTarLiSu'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vTarLiIn'.($i+1)]."^".$pArrayParametros['vTarLiSu'.($i+1)]."^".substr($pArrayParametros['vTarPor'.($i+1)],strpos($pArrayParametros['vTarPor'.($i+1)],"."),strlen($pArrayParametros['vTarPor'.($i+1)]));
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nVlrItem1132']."~".$zNiveles."!"."~".$pArrayParametros['nCanSer1132']."~".$pArrayParametros['nVlrSer1132']."|";
              } else {
                $nSwitch = 1; $zNiveles = ""; $cFcoTar = "";
              }
            break;
            case "1133": // ELABORACION DECLARACIONES (DIM Y DAV)
              // Validando que el Valor Fijo DIM sea Mayor a Cero
              if ($pArrayParametros['nVlFijDim1133'] == "" || $pArrayParametros['nVlFijDim1133'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Fijo DIM debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Fijo DAV sea Mayor a Cero
              if ($pArrayParametros['nVlFijDav1133'] == "" || $pArrayParametros['nVlFijDav1133'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Fijo DAV debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
      
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nVlFijDim1133']."~".$pArrayParametros['nVlFijDav1133']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "1134": // PORCENTAJE SOBRE TRIBUTOS:(0,1% TRIBUTOS)
              // Validando que el Valor Fijo DIM sea Mayor a Cero
              if ($pArrayParametros['nPorTri1134'] == "" || $pArrayParametros['nPorTri1134'] < 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje Sobre Tributos debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
      
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nPorTri1134']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "1135": // VALOR FIJO + PORCENTAJE CIF
              // Validando que el Porcentaje del CIF sea Mayor a Cero
              if ($pArrayParametros['nPor1135'] < 0 || $pArrayParametros['nPor1135'] > 1 || $pArrayParametros['nPor1135'] == "" || $pArrayParametros['nPor1135'] == 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Porcentaje del Valor CIF debe ser Mayor a Cero y Menor o Igual a Uno.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Fijo sea Mayor a Cero
              if ($pArrayParametros['nVlrFijo1135'] == "" || $pArrayParametros['nVlrFijo1135'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Fijo debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nPor1135']."~".$pArrayParametros['nVlrFijo1135']."|";
              } else {
                $nSwitch = 1; $cFcoTar = "";
              }
            break;
            case "1137": // VALOR ESCALONADO POR CANTIDAD DE FACTURAS + VALOR ADICIONAL X CANTIDAD DE ITEMS
              $zNiveles = "";
              // Validando que el Valor de los Niveles sea Mayor a Cero
              if ($pArrayParametros['nNiv1137'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor de los Niveles debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              // Validando que el Valor Adicional por Item sea Mayor a Cero
              if ($pArrayParametros['nVlrItem1137'] < 1) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor Adicional por Item debe ser Mayor a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiIn1'] != 0) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Inferior en el Primer Nivel debe ser Igual a Cero.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              if ($pArrayParametros['vTarLiSu'.$pArrayParametros['nNiv1137']] != "") {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "El Valor del Limite Superior en el Ultimo Nivel debe ser Vacio.";
                $mReturn[count($mReturn)] = $cMsj;
              }
    
              for ($i=0;$i<$pArrayParametros['nNiv1137'];$i++) {
                if ($pArrayParametros['vTarPor'.($i+1)] < 0) {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Valor en el Nivel [".($i+1)."] no puede ser Menor a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
    
                if (($i+1) < $pArrayParametros['nNiv1137']) { // No he llegado al ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarLiSu'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior, Limite Superior o Valor x Unidad no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  if ($pArrayParametros['vTarLiIn'.($i+1)] >= $pArrayParametros['vTarLiSu'.($i+1)]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior no puede ser Mayor o Igual al Limite Superior en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                } else { // Estoy en el ultimo nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] == "" || $pArrayParametros['vTarPor'.($i+1)] == "") {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "Los Datos de Limite Inferior o Valor x Unidad no Pueden Ser Vacios en el Nivel [".($i+1)."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
    
                if (($i+1) == 1) { // estoy en el primer nivel.
    
                } else { // Estoy despues del primer nivel.
                  if ($pArrayParametros['vTarLiIn'.($i+1)] != $pArrayParametros['vTarLiSu'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Limite Inferior en el Nivel [".($i+1)."] debe ser Igual al Limite Superior en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
    
                  /*if ($pArrayParametros['vTarPor'.($i+1)] >= $pArrayParametros['vTarPor'.$i]) {
                    $nSwitch = 1;
                    $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                    $cMsj .= "El Porcentaje en el Nivel [".($i+1)."] debe ser Menor al Porcentaje en el Nivel [".$i."].";
                    $mReturn[count($mReturn)] = $cMsj;
                  }*/
                }
    
                if ($nSwitch == 0) {
                  $zNiveles .= "!".$pArrayParametros['vTarLiIn'.($i+1)]."^".$pArrayParametros['vTarLiSu'.($i+1)]."^".substr($pArrayParametros['vTarPor'.($i+1)],strpos($pArrayParametros['vTarPor'.($i+1)],"."),strlen($pArrayParametros['vTarPor'.($i+1)]));
                } else {
                  $nSwitch = 1;
                }
              }
    
              if ($nSwitch == 0) {
                $cFcoTar = "|".$pArrayParametros['nVlrItem1137']."~".$zNiveles."!|";
              } else {
                $nSwitch = 1;
                $zNiveles = "";
                $cFcoTar = "";
              }
            break;
            default:
            break;
          }
    
          /***** Fin de Validacion de Campos de las Diferentes Formas de Pago *****/
          /***** Fin de Validacion de Campos de las Diferentes Formas de Pago *****/
          /***** Fin de Validacion de Campos de las Diferentes Formas de Pago *****/
    
          // Armo una Cadena con los CheckBox Prendidos de Sucursales
          $pArrayParametros['cSucId'] = "";
          for ($y=0;$y<count($mSuc008);$y++) {
            if ($pArrayParametros["c".$mSuc008[$y]['sucidxxx']] != "") {
              $pArrayParametros['cSucId'] .= $pArrayParametros["c".$mSuc008[$y]['sucidxxx']]."~";
            }
          }
          $pArrayParametros['cSucId'] = substr($pArrayParametros['cSucId'],0,(strlen($pArrayParametros['cSucId'])-1));
    
          // Armo una Cadena con los CheckBox Prendidos Tipo de Operacion
          $pArrayParametros['cFcoMtr'] = "";
          if ($pArrayParametros['cAereo'] != "") {
            $pArrayParametros['cFcoMtr'] .= $pArrayParametros['cAereo']."~";
          }
          if ($pArrayParametros['cMaritimo'] != "") {
            $pArrayParametros['cFcoMtr'] .= $pArrayParametros['cMaritimo']."~";
          }
          if ($pArrayParametros['cTerrestre'] != "") {
            $pArrayParametros['cFcoMtr'] .= $pArrayParametros['cTerrestre']."~";
          }
          $pArrayParametros['cFcoMtr'] = substr($pArrayParametros['cFcoMtr'],0,(strlen($pArrayParametros['cFcoMtr'])-1));
    
          //f_Mensaje(__FILE__,__LINE__,"lleo~".$pArrayParametros['cAereo'] .' '.$pArrayParametros['cMaritimo'].' '.$pArrayParametros['cTerrestre'] );
          // Cadena de Sucursales
          $mSucId = explode("~",$pArrayParametros['cSucId']);
          $mSucursales = explode("~",$pArrayParametros['cSucursales']);
          for($i=0;$i<count($mSucId);$i++){
            $cCadSuc .= "sucidxxx LIKE \"%{$mSucId[$i]}%\" OR ";
          }
          $cCadSuc = substr($cCadSuc,0,strlen($cCadSuc)-3);
    
          // Cadena de Medio Transporte
          $mFcoMtr = explode("~",$pArrayParametros['cFcoMtr']);
          $mModoTra = explode("~",$pArrayParametros['cModoTra']);
          for($i=0;$i<count($mFcoMtr);$i++){
            $cCadFcoMtr .= "fcomtrxx LIKE \"%{$mFcoMtr[$i]}%\" OR ";
          }
          $cCadFcoMtr = substr($cCadFcoMtr,0,strlen($cCadFcoMtr)-3);
    
          // Validando que no Exista el Mismo Cliente, Servicio, Forma de Cobro,  Tarifa y Sucursal en la Base de Datos en Estado ACTIVO
          $qSql131  = "SELECT * ";
          $qSql131 .= "FROM $cAlfa.fpar0131 ";
          $qSql131 .= "WHERE ";
          $qSql131 .= "cliidxxx = \"{$pArrayParametros['cCliId']}\" AND ";
          $qSql131 .= "seridxxx = \"{$pArrayParametros['cSerId']}\" AND ";
          $qSql131 .=	"fcoidxxx = \"{$pArrayParametros['cFcoId']}\" AND ";
          $qSql131 .= "fcotptxx = \"{$pArrayParametros['cFcoTpt']}\" AND ";
          $qSql131 .=	"fcotpixx = \"{$pArrayParametros['cFcoTpi']}\" AND ";
          $qSql131 .= "($cCadSuc) AND ";
          $qSql131 .=	"fcotopxx = \"{$pArrayParametros['cSerTop']}\" AND ";
          $qSql131 .= "($cCadFcoMtr) AND ";
          $qSql131 .= "tartipxx = \"{$pArrayParametros['cTarTip']}\" AND ";
          if ( $pArrayParametros['kModo'] == "EDITAR") {
            $qSql131 .= "taridxxx != \"{$pArrayParametros['cTarId']}\" AND ";
          }
          $qSql131 .= "regestxx != \"INACTIVO\"";
          $xSql131  = f_MySql("SELECT","",$qSql131,$xConexion01,"");
          // f_Mensaje(__FILE__,__LINE__,$qSql131."~".mysql_num_rows($xSql131));
    
          if (mysql_num_rows($xSql131) > 0) {
            $nSwitch = 1;
    
            //2024-05-29 - OC-33921 - Actualización Masiva de Tarifas
            // Si la variable de control de vigencia de tarifas esta activa, 
            // se debe validar que no exista una tarifa con el mismo rango de fechas seleccionado
            $cMsjAdic = "";
            if ($vSysStr['system_control_vigencia_tarifas'] == "SI") {
              $nSwitchVigencia = 0;
              while ($xRT = mysql_fetch_array($xSql131)) {
                if (($pArrayParametros['dTarFevDe'] >= $xRT['tarfevde'] && $pArrayParametros['dTarFevDe'] <= $xRT['tarfevha']) || 
                    ($pArrayParametros['dTarFevHa'] >= $xRT['tarfevde'] && $pArrayParametros['dTarFevHa'] <= $xRT['tarfevha'])
                ) {
                  $nSwitchVigencia = 1;
                  $cMsjAdic = " o Ya Existe una Tarifa en el Mismo Rango de Fechas de Vigencia";
                }
              }
    
              if ($nSwitchVigencia == 1) {
                $nSwitch = 1;
              } else {
                $nSwitch = 0;
              }
            }
    
            if ($nSwitch == 1) {
              $cTxtEst = "ACTIVO";
              if($vSysStr['system_financiero_autorizacion_tarifas'] == "SI"){
                $cTxtEst = "ACTIVO o PROVISIONAL";
              }
    
              $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
              $cMsj .= "Ya se Encuentra Parametrizada una Tarifa en Estado $cTxtEst para Este Cliente ";
              $cMsj .= "con el Mismo Concepto de Cobro y Forma de Cobro para ";
              $cMsj .= "la Misma Sucursal o ";
              $cMsj .= "Mismo Medio de Transporte o ";
              $cMsj .= "el Mismo Tipo de Tarifa";
              $cMsj .= $cMsjAdic . ".";
              $mReturn[count($mReturn)] = $cMsj;
            }
          }
          
          $dTarfVig = "";
          if ($pArrayParametros['kModo'] == "EDITAR") {
            $qSql131  = "SELECT taridxxx, tarfcvig ";
            $qSql131 .= "FROM $cAlfa.fpar0131 ";
            $qSql131 .= "WHERE ";
            $qSql131 .= "taridxxx = \"{$pArrayParametros['cTarId']}\" ";
            $xSql131  = f_MySql("SELECT","",$qSql131,$xConexion01,"");
            //f_Mensaje(__FILE__,__LINE__,$qSql131."~".mysql_num_rows($xSql131));
    
            if (mysql_num_rows($xSql131) == 0) {
              $nSwitch = 1;
              $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
              $cMsj .= "La Tarifa Seleccionada No Existe.";
              $mReturn[count($mReturn)] = $cMsj;
            } else {
              $vSql131 = mysql_fetch_array($xSql131);
              $dTarfVig = $vSql131['tarfcvig'];
            }

            if ($vSysStr['system_control_vigencia_tarifas'] == "SI") {
              //Si hay cambio de la fecha de vigencia se actualiza el campo de Fecha Ultima Modificacion Vigencia
              if ($pArrayParametros['dTarFevDe'] != $pArrayParametros['dTarFevDeAnt'] || $pArrayParametros['dTarFevHa'] != $pArrayParametros['dTarFevHaAnt']) {
                $dTarfVig = date('Y-m-d');
              }
            }
          }

          // Validando que no Exista el Mismo Cliente, Servicio, Forma de Cobro,  Tarifa y Sucursal en la Base de Datos en Estado ACTIVO
          // Validado El Estado del Registro
          if ($pArrayParametros['cEstado'] == "") {
            $nSwitch = 1;
            $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
            $cMsj .= "El Estado del Registro no puede ser vacio.";
            $mReturn[count($mReturn)] = $cMsj;
          }
    
          //Valido que si la variable system_financiero_autorizacion_tarifas esta en SI el estado es PROVISIONAL
          if($vSysStr['system_financiero_autorizacion_tarifas'] == "SI"){
            $pArrayParametros['cEstado'] = "PROVISIONAL";
          }
    
          //armo string de condiciones especiales
          $cCadEsp = "|";
          $cCamEsp = "";
          foreach ($pArrayParametros['cNueCesp'] as $key => $vEsp) {
            if ($vEsp != '') {
              $cCadEsp .= $key."~".trim(strtoupper($vEsp))."|";
              $cCamEsp .= "\"$key\",";
            }
          }
          $cCadEsp = ($cCadEsp == "|") ? "" : $cCadEsp;
          $cCamEsp = substr($cCamEsp, 0, -1);
    
        break;
        case "BORRAR":
        case "ANULAR":
          // No hago nada.
        break;
        case "AUTORIZARTARIFA":
          $mTarId = explode("~",$pArrayParametros['cTarId']);
    
          for($n=0;$n<count($mTarId);$n++) {
            if ($mTarId[$n] != "") {
              $qTarifas  = "SELECT * ";
              $qTarifas .= "FROM $cAlfa.fpar0131 ";
              $qTarifas .= "WHERE ";
              $qTarifas .= "taridxxx = \"{$mTarId[$n]}\" ";
              $xTarifas  = f_MySql("SELECT","",$qTarifas,$xConexion01,"");
              //f_Mensaje(__FILE__,__LINE__,$qTarifas."~".mysql_num_rows($xTarifas));
              if (mysql_num_rows($xTarifas) > 0) {
                $vTarifas = mysql_fetch_array($xTarifas);
                if($vTarifas['regestxx'] != "PROVISIONAL") {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "El Estado de La Tarifa [{$mTarId[$n]}] Se Encuentra Diferente a PROVISIONAL.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
              } else {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "La Tarifa [{$mTarId[$n]}] No Existe.";
                $mReturn[count($mReturn)] = $cMsj;
              }
            }
          }
        break;
        default:
          $nSwitch = 1;
          $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
          $cMsj .= "El Modo de Grabado no es Valido.";
          $mReturn[count($mReturn)] = $cMsj;
        break;
      }
      // Fin de la Validacion
    
      // 2023-07-19 Ticket: OC-30946 - Proyecto Especial LOG de la funcionalidad Autorizar Tarifas
      if ($cAlfa == "DEALMAVIVA" || $cAlfa == "TEALMAVIVA" || $cAlfa == "ALMAVIVA" || $cAlfa == "TEPRUEBASX") {
        //Para almavia se debe guardar el log de los cambios realizados en el sistema
        //Inicializando la matriz de datos generales, valores anteriores y nuevos
        $mDatCab = array();
        $mDatOld = array();
        $mDatNew = array();
      }
    
      //Fecha de creacion y/o Modificacion, para que el log y la fecha del registro sea la misma
      $dFecha = date('Y-m-d');
      $dHora  = date('H:i:s');
    
      // Ahora Empiezo a Grabar
      // Pregunto si el SWITCH Viene en 0 para Poder Seguir
    
      if ($nSwitch == 0) {
        switch ($pArrayParametros['kModo']) {
          case "NUEVO":
            // 2023-07-19 Ticket: OC-30946 - Proyecto Especial LOG de la funcionalidad Autorizar Tarifas
            if ($cAlfa == "DEALMAVIVA" || $cAlfa == "TEALMAVIVA" || $cAlfa == "ALMAVIVA" || $cAlfa == "TEPRUEBASX") {
              //Cab
              $mDatCab[0]['logaccxx'] = $pArrayParametros['kModo'];
              $mDatCab[0]['tartipxx'] = trim(strtoupper($pArrayParametros['cTarTip']));
              $mDatCab[0]['cliidxxx'] = trim(strtoupper($pArrayParametros['cCliId']));
              $mDatCab[0]['seridxxx'] = trim(strtoupper($pArrayParametros['cSerId']));
              $mDatCab[0]['serdespc'] = trim(strtoupper($pArrayParametros['cSerDesPc']));
              $mDatCab[0]['fcoidxxx'] = trim(strtoupper($pArrayParametros['cFcoId']));
              $mDatCab[0]['fcotptxx'] = trim(strtoupper($pArrayParametros['cFcoTpt']));
              $mDatCab[0]['fcotpixx'] = trim(strtoupper($pArrayParametros['cFcoTpi']));
              $mDatCab[0]['sucidxxx'] = trim(strtoupper($pArrayParametros['cSucId']));
              $mDatCab[0]['fcotopxx'] = trim(strtoupper($pArrayParametros['cSerTop']));
              $mDatCab[0]['fcomtrxx'] = trim(strtoupper($pArrayParametros['cFcoMtr']));
    
              //New
              $mDatNew[0]['cliidxxx'] = trim(strtoupper($pArrayParametros['cCliId']));
              $mDatNew[0]['seridxxx'] = trim(strtoupper($pArrayParametros['cSerId']));
              $mDatNew[0]['fcoidxxx'] = trim(strtoupper($pArrayParametros['cFcoId']));
              $mDatNew[0]['sucidxxx'] = trim(strtoupper($pArrayParametros['cSucId']));
              $mDatNew[0]['fcotptxx'] = trim(strtoupper($pArrayParametros['cFcoTpt']));
              $mDatNew[0]['fcotpixx'] = trim(strtoupper($pArrayParametros['cFcoTpi']));
              $mDatNew[0]['fcotopxx'] = trim(strtoupper($pArrayParametros['cSerTop']));
              $mDatNew[0]['fcomtrxx'] = trim(strtoupper($pArrayParametros['cFcoMtr']));
              $mDatNew[0]['fcotarxx'] = trim(strtoupper($cFcoTar));
              $mDatNew[0]['fcopcexx'] = $cCadEsp;
              $mDatNew[0]['tartipxx'] = trim(strtoupper($pArrayParametros['cTarTip']));
              $mDatNew[0]['monidxxx'] = trim(strtoupper($pArrayParametros['cMonId']));
              if ($vSysStr['system_control_vigencia_tarifas'] == "SI") {
                $mDatNew[0]['tarfevde'] = trim(strtoupper($pArrayParametros['dTarFevDe']));
                $mDatNew[0]['tarfevha'] = trim(strtoupper($pArrayParametros['dTarFevHa']));
                $mDatNew[0]['tarfcvig'] = $dTarfVig;
              }
              $mDatNew[0]['serdespc'] = trim(strtoupper($pArrayParametros['cSerDesPc']));
              $mDatNew[0]['pucrftex'] = $cPucRfteId;
              $mDatNew[0]['pucaftex'] = $cPucARfteId;
              $mDatNew[0]['regusrxx'] = trim(strtoupper($pArrayParametros['kUsrId']));
              $mDatNew[0]['regfcrex'] = $dFecha;
              $mDatNew[0]['reghcrex'] = $dHora;
              $mDatNew[0]['regfmodx'] = $dFecha;
              $mDatNew[0]['reghmodx'] = $dHora;
              $mDatNew[0]['regestxx'] = trim(strtoupper($pArrayParametros['cEstado']));
            }
    
            // Insert en la Tabla.
            $cInsertTab	= array(array('NAME'=>'cliidxxx','VALUE'=>trim(strtoupper($pArrayParametros['cCliId']))     ,'CHECK'=>'SI'),
                                array('NAME'=>'seridxxx','VALUE'=>trim(strtoupper($pArrayParametros['cSerId']))     ,'CHECK'=>'SI'),
                                array('NAME'=>'fcoidxxx','VALUE'=>trim(strtoupper($pArrayParametros['cFcoId']))     ,'CHECK'=>'SI'),
                                array('NAME'=>'sucidxxx','VALUE'=>trim(strtoupper($pArrayParametros['cSucId']))     ,'CHECK'=>'SI'),
                                array('NAME'=>'fcotptxx','VALUE'=>trim(strtoupper($pArrayParametros['cFcoTpt']))    ,'CHECK'=>'SI'),
                                array('NAME'=>'fcotpixx','VALUE'=>trim(strtoupper($pArrayParametros['cFcoTpi']))    ,'CHECK'=>'SI'),
                                array('NAME'=>'fcotopxx','VALUE'=>trim(strtoupper($pArrayParametros['cSerTop']))    ,'CHECK'=>'SI'),
                                array('NAME'=>'fcomtrxx','VALUE'=>trim(strtoupper($pArrayParametros['cFcoMtr']))    ,'CHECK'=>'NO'), //Para los DO de registro es vacio
                                array('NAME'=>'fcotarxx','VALUE'=>trim(strtoupper($cFcoTar))                        ,'CHECK'=>'SI'),
                                array('NAME'=>'fcopcexx','VALUE'=>$cCadEsp  						                            ,'CHECK'=>'NO'),
                                array('NAME'=>'tartipxx','VALUE'=>trim(strtoupper($pArrayParametros['cTarTip']))    ,'CHECK'=>'SI'),
                                array('NAME'=>'monidxxx','VALUE'=>trim(strtoupper($pArrayParametros['cMonId']))     ,'CHECK'=>'SI'),
                                array('NAME'=>'tarfevde','VALUE'=>trim(strtoupper($pArrayParametros['dTarFevDe']))  ,'CHECK'=>'NO'),
                                array('NAME'=>'tarfevha','VALUE'=>trim(strtoupper($pArrayParametros['dTarFevHa']))  ,'CHECK'=>'NO'),
                                array('NAME'=>'tarfcvig','VALUE'=>$dTarfVig                                         ,'CHECK'=>'NO'),
                                array('NAME'=>'serdespc','VALUE'=>trim(strtoupper($pArrayParametros['cSerDesPc']))  ,'CHECK'=>'SI'),
                                array('NAME'=>'pucrftex','VALUE'=>$cPucRfteId													              ,'CHECK'=>'NO'),
                                array('NAME'=>'pucaftex','VALUE'=>$cPucARfteId													            ,'CHECK'=>'NO'),
                                array('NAME'=>'regusrcr','VALUE'=>trim(strtoupper($pArrayParametros['kUsrId']))     ,'CHECK'=>'SI'),
                                array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($pArrayParametros['kUsrId']))     ,'CHECK'=>'SI'),
                                array('NAME'=>'regfcrex','VALUE'=>$dFecha		    									                  ,'CHECK'=>'SI'),
                                array('NAME'=>'reghcrex','VALUE'=>$dHora		                                        ,'CHECK'=>'SI'),
                                array('NAME'=>'regfmodx','VALUE'=>$dFecha						    					                  ,'CHECK'=>'SI'),
                                array('NAME'=>'reghmodx','VALUE'=>$dHora		                                        ,'CHECK'=>'SI'),
                                array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($pArrayParametros['cEstado']))    ,'CHECK'=>'SI'));
    
            if (!f_MySql("INSERT","fpar0131",$cInsertTab,$xConexion01,$cAlfa)) {
              $nSwitch = 1;
              $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
              $cMsj .= "Error Guardando Datos de la Tarifa.";
              $mReturn[count($mReturn)] = $cMsj;
            }
          break;
          case "EDITAR":
            if ( $nSwitch == 0 ) {
              // 2023-07-19 Ticket: OC-30946 - Proyecto Especial LOG de la funcionalidad Autorizar Tarifas
              if ($cAlfa == "DEALMAVIVA" || $cAlfa == "TEALMAVIVA" || $cAlfa == "ALMAVIVA" || $cAlfa == "TEPRUEBASX") {
                //Buscando datos actuales de la tarifa
                $qTarifa  = "SELECT * ";
                $qTarifa .= "FROM $cAlfa.fpar0131 ";
                $qTarifa .= "WHERE ";
                $qTarifa .= "taridxxx = \"{$pArrayParametros['cTarId']}\" ";
                $xTarifa  = f_MySql("SELECT","",$qTarifa,$xConexion01,"");
                //f_Mensaje(__FILE__,__LINE__,$qTarifa."~".mysql_num_rows($xTarifa));
                $vTarifa = array();
                $vTarifa = mysql_fetch_array($xTarifa);
    
                //Cab
                $mDatCab[0]['logaccxx'] = $pArrayParametros['kModo'];
                $mDatCab[0]['tartipxx'] = trim(strtoupper($pArrayParametros['cTarTip']));
                $mDatCab[0]['cliidxxx'] = trim(strtoupper($pArrayParametros['cCliId']));
                $mDatCab[0]['seridxxx'] = trim(strtoupper($pArrayParametros['cSerId']));
                $mDatCab[0]['serdespc'] = trim(strtoupper($pArrayParametros['cSerDesPc']));
                $mDatCab[0]['fcoidxxx'] = trim(strtoupper($pArrayParametros['cFcoId']));
                $mDatCab[0]['fcotptxx'] = trim(strtoupper($pArrayParametros['cFcoTpt']));
                $mDatCab[0]['fcotpixx'] = trim(strtoupper($pArrayParametros['cFcoTpi']));
                $mDatCab[0]['sucidxxx'] = trim(strtoupper($pArrayParametros['cSucId']));
                $mDatCab[0]['fcotopxx'] = trim(strtoupper($pArrayParametros['cSerTop']));
                $mDatCab[0]['fcomtrxx'] = trim(strtoupper($pArrayParametros['cFcoMtr']));
    
                //Old
                $mDatOld[0]['cliidxxx'] = $vTarifa['cliidxxx'];
                $mDatOld[0]['seridxxx'] = $vTarifa['seridxxx'];
                $mDatOld[0]['fcoidxxx'] = $vTarifa['fcoidxxx'];
                $mDatOld[0]['sucidxxx'] = $vTarifa['sucidxxx'];
                $mDatOld[0]['fcotptxx'] = $vTarifa['fcotptxx'];
                $mDatOld[0]['fcotpixx'] = $vTarifa['fcotpixx'];
                $mDatOld[0]['fcotopxx'] = $vTarifa['fcotopxx'];
                $mDatOld[0]['fcomtrxx'] = $vTarifa['fcomtrxx'];
                $mDatOld[0]['fcotarxx'] = $vTarifa['fcotarxx'];
                $mDatOld[0]['fcopcexx'] = $vTarifa['fcopcexx'];
                $mDatOld[0]['tartipxx'] = $vTarifa['tartipxx'];
                $mDatOld[0]['monidxxx'] = $vTarifa['monidxxx'];
                if ($vSysStr['system_control_vigencia_tarifas'] == "SI") {
                  $mDatOld[0]['tarfevde'] = $vTarifa['dTarFevDe'];
                  $mDatOld[0]['tarfevha'] = $vTarifa['dTarFevHa'];
                  $mDatOld[0]['tarfcvig'] = $vTarifa['tarfcvig'];
                }
                $mDatOld[0]['serdespc'] = $vTarifa['serdespc'];
                $mDatOld[0]['pucrftex'] = $vTarifa['pucrftex'];
                $mDatOld[0]['pucaftex'] = $vTarifa['pucaftex'];
                $mDatOld[0]['regusrxx'] = $vTarifa['regusrxx'];
                $mDatOld[0]['regfmodx'] = $vTarifa['regfmodx'];
                $mDatOld[0]['reghmodx'] = $vTarifa['reghmodx'];
                $mDatOld[0]['regestxx'] = $vTarifa['regestxx'];
    
                //New
                $mDatNew[0]['cliidxxx'] = trim(strtoupper($pArrayParametros['cCliId']));
                $mDatNew[0]['seridxxx'] = trim(strtoupper($pArrayParametros['cSerId']));
                $mDatNew[0]['fcoidxxx'] = trim(strtoupper($pArrayParametros['cFcoId']));
                $mDatNew[0]['sucidxxx'] = trim(strtoupper($pArrayParametros['cSucId']));
                $mDatNew[0]['fcotptxx'] = trim(strtoupper($pArrayParametros['cFcoTpt']));
                $mDatNew[0]['fcotpixx'] = trim(strtoupper($pArrayParametros['cFcoTpi']));
                $mDatNew[0]['fcotopxx'] = trim(strtoupper($pArrayParametros['cSerTop']));
                $mDatNew[0]['fcomtrxx'] = trim(strtoupper($pArrayParametros['cFcoMtr']));
                $mDatNew[0]['fcotarxx'] = trim(strtoupper($cFcoTar));
                $mDatNew[0]['fcopcexx'] = $cCadEsp;
                $mDatNew[0]['tartipxx'] = trim(strtoupper($pArrayParametros['cTarTip']));
                $mDatNew[0]['monidxxx'] = trim(strtoupper($pArrayParametros['cMonId']));
                if ($vSysStr['system_control_vigencia_tarifas'] == "SI") {
                  $mDatNew[0]['tarfevde'] = trim(strtoupper($pArrayParametros['dTarFevDe']));
                  $mDatNew[0]['tarfevha'] = trim(strtoupper($pArrayParametros['dTarFevHa']));
                  $mDatNew[0]['tarfcvig'] = $dTarfVig;
                }
                $mDatNew[0]['serdespc'] = trim(strtoupper($pArrayParametros['cSerDesPc']));
                $mDatNew[0]['pucrftex'] = $cPucRfteId;
                $mDatNew[0]['pucaftex'] = $cPucARfteId;
                $mDatNew[0]['regusrxx'] = trim(strtoupper($pArrayParametros['kUsrId']));
                $mDatNew[0]['regfmodx'] = $dFecha;
                $mDatNew[0]['reghmodx'] = $dHora;
                $mDatNew[0]['regestxx'] = trim(strtoupper($pArrayParametros['cEstado']));
              }
    
              // Insert en la Tabla.
              $cInsertTab	= array(array('NAME'=>'cliidxxx','VALUE'=>trim(strtoupper($pArrayParametros['cCliId']))     ,'CHECK'=>'SI'),
                                  array('NAME'=>'seridxxx','VALUE'=>trim(strtoupper($pArrayParametros['cSerId']))     ,'CHECK'=>'SI'),
                                  array('NAME'=>'fcoidxxx','VALUE'=>trim(strtoupper($pArrayParametros['cFcoId']))     ,'CHECK'=>'SI'),
                                  array('NAME'=>'sucidxxx','VALUE'=>trim(strtoupper($pArrayParametros['cSucId']))     ,'CHECK'=>'SI'),
                                  array('NAME'=>'fcotptxx','VALUE'=>trim(strtoupper($pArrayParametros['cFcoTpt']))    ,'CHECK'=>'SI'),
                                  array('NAME'=>'fcotpixx','VALUE'=>trim(strtoupper($pArrayParametros['cFcoTpi']))    ,'CHECK'=>'SI'),
                                  array('NAME'=>'fcotopxx','VALUE'=>trim(strtoupper($pArrayParametros['cSerTop']))    ,'CHECK'=>'SI'),
                                  array('NAME'=>'fcomtrxx','VALUE'=>trim(strtoupper($pArrayParametros['cFcoMtr']))    ,'CHECK'=>'NO'), //Para los DO de registro es vacio
                                  array('NAME'=>'fcotarxx','VALUE'=>trim(strtoupper($cFcoTar))                        ,'CHECK'=>'SI'),
                                  array('NAME'=>'fcopcexx','VALUE'=>$cCadEsp                                          ,'CHECK'=>'NO'),
                                  array('NAME'=>'tartipxx','VALUE'=>trim(strtoupper($pArrayParametros['cTarTip']))    ,'CHECK'=>'SI'),
                                  array('NAME'=>'monidxxx','VALUE'=>trim(strtoupper($pArrayParametros['cMonId']))     ,'CHECK'=>'SI'),
                                  array('NAME'=>'tarfevde','VALUE'=>trim(strtoupper($pArrayParametros['dTarFevDe']))  ,'CHECK'=>'NO'),
                                  array('NAME'=>'tarfevha','VALUE'=>trim(strtoupper($pArrayParametros['dTarFevHa']))  ,'CHECK'=>'NO'),
                                  array('NAME'=>'tarfcvig','VALUE'=>$dTarfVig                                         ,'CHECK'=>'NO'),
                                  array('NAME'=>'serdespc','VALUE'=>trim(strtoupper($pArrayParametros['cSerDesPc']))  ,'CHECK'=>'SI'),
                                  array('NAME'=>'pucrftex','VALUE'=>$cPucRfteId													              ,'CHECK'=>'NO'),
                                  array('NAME'=>'pucaftex','VALUE'=>$cPucARfteId													            ,'CHECK'=>'NO'),
                                  array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($pArrayParametros['kUsrId']))     ,'CHECK'=>'SI'),
                                  array('NAME'=>'regfmodx','VALUE'=>$dFecha						    					                  ,'CHECK'=>'SI'),
                                  array('NAME'=>'reghmodx','VALUE'=>$dHora		                                        ,'CHECK'=>'SI'),
                                  array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($pArrayParametros['cEstado']))    ,'CHECK'=>'SI'),
                                  array('NAME'=>'taridxxx','VALUE'=>$pArrayParametros['cTarId']   										,'CHECK'=>'WH'));
    
              if (!f_MySql("UPDATE","fpar0131",$cInsertTab,$xConexion01,$cAlfa)) {
                $nSwitch = 1;
                $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                $cMsj .= "Error Editando Datos de la Tarifa.";
                $mReturn[count($mReturn)] = $cMsj;
              }
            }
          break;
          case "BORRAR":
            $mTarId = explode("~",$pArrayParametros['cTarId']);
            for($n=0;$n<count($mTarId);$n++) {
              if ($mTarId[$n] != "") {
                // 2023-07-19 Ticket: OC-30946 - Proyecto Especial LOG de la funcionalidad Autorizar Tarifas
                if ($cAlfa == "DEALMAVIVA" || $cAlfa == "TEALMAVIVA" || $cAlfa == "ALMAVIVA" || $cAlfa == "TEPRUEBASX") {
                  //Buscando datos actuales de la tarifa
                  $qTarifa  = "SELECT * ";
                  $qTarifa .= "FROM $cAlfa.fpar0131 ";
                  $qTarifa .= "WHERE ";
                  $qTarifa .= "taridxxx = \"{$mTarId[$n]}\" ";
                  $xTarifa  = f_MySql("SELECT","",$qTarifa,$xConexion01,"");
                  //f_Mensaje(__FILE__,__LINE__,$qTarifa."~".mysql_num_rows($xTarifa));
                  $vTarifa = array();
                  $vTarifa = mysql_fetch_array($xTarifa);
    
                  //Cab
                  $mDatCab[0]['logaccxx'] = $pArrayParametros['kModo'];
                  $mDatCab[0]['tartipxx'] = $vTarifa['tartipxx'];
                  $mDatCab[0]['cliidxxx'] = $vTarifa['cliidxxx'];
                  $mDatCab[0]['seridxxx'] = $vTarifa['seridxxx'];
                  $mDatCab[0]['serdespc'] = $vTarifa['serdespc'];
                  $mDatCab[0]['fcoidxxx'] = $vTarifa['fcoidxxx'];
                  $mDatCab[0]['fcotptxx'] = $vTarifa['fcotptxx'];
                  $mDatCab[0]['fcotpixx'] = $vTarifa['fcotpixx'];
                  $mDatCab[0]['sucidxxx'] = $vTarifa['sucidxxx'];
                  $mDatCab[0]['fcotopxx'] = $vTarifa['fcotopxx'];
                  $mDatCab[0]['fcomtrxx'] = $vTarifa['fcomtrxx'];
    
                  //Old
                  $nInd_mDatOld = count($mDatOld);
                  $mDatOld[$nInd_mDatOld]['cliidxxx'] = $vTarifa['cliidxxx'];
                  $mDatOld[$nInd_mDatOld]['seridxxx'] = $vTarifa['seridxxx'];
                  $mDatOld[$nInd_mDatOld]['fcoidxxx'] = $vTarifa['fcoidxxx'];
                  $mDatOld[$nInd_mDatOld]['sucidxxx'] = $vTarifa['sucidxxx'];
                  $mDatOld[$nInd_mDatOld]['fcotptxx'] = $vTarifa['fcotptxx'];
                  $mDatOld[$nInd_mDatOld]['fcotpixx'] = $vTarifa['fcotpixx'];
                  $mDatOld[$nInd_mDatOld]['fcotopxx'] = $vTarifa['fcotopxx'];
                  $mDatOld[$nInd_mDatOld]['fcomtrxx'] = $vTarifa['fcomtrxx'];
                  $mDatOld[$nInd_mDatOld]['fcotarxx'] = $vTarifa['fcotarxx'];
                  $mDatOld[$nInd_mDatOld]['fcopcexx'] = $vTarifa['fcopcexx'];
                  $mDatOld[$nInd_mDatOld]['tartipxx'] = $vTarifa['tartipxx'];
                  $mDatOld[$nInd_mDatOld]['monidxxx'] = $vTarifa['monidxxx'];
                  if ($vSysStr['system_control_vigencia_tarifas'] == "SI") {
                    $mDatOld[$nInd_mDatOld]['tarfevde'] = $vTarifa['tarfevde'];
                    $mDatOld[$nInd_mDatOld]['tarfevha'] = $vTarifa['tarfevha'];
                    $mDatOld[$nInd_mDatOld]['tarfcvig'] = $vTarifa['tarfcvig'];
                  }
                  $mDatOld[$nInd_mDatOld]['serdespc'] = $vTarifa['serdespc'];
                  $mDatOld[$nInd_mDatOld]['pucrftex'] = $vTarifa['pucrftex'];
                  $mDatOld[$nInd_mDatOld]['pucaftex'] = $vTarifa['pucaftex'];
                  $mDatOld[$nInd_mDatOld]['regusrxx'] = $vTarifa['regusrxx'];
                  $mDatOld[$nInd_mDatOld]['regfcrex'] = $vTarifa['regfcrex'];
                  $mDatOld[$nInd_mDatOld]['reghcrex'] = $vTarifa['reghcrex'];
                  $mDatOld[$nInd_mDatOld]['regfmodx'] = $vTarifa['regfmodx'];
                  $mDatOld[$nInd_mDatOld]['reghmodx'] = $vTarifa['reghmodx'];
                  $mDatOld[$nInd_mDatOld]['regestxx'] = $vTarifa['regestxx'];
                }
    
                $cDeleteTab	 = array(array('NAME'=>'taridxxx','VALUE'=>$mTarId[$n],'CHECK'=>'WH'));
    
                if (f_MySql("DELETE","fpar0131",$cDeleteTab,$xConexion01,$cAlfa)) {
                } else {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "Error al Borrar los Datos de la Tarifa [{$mTarId[$n]}].";
                  $mReturn[count($mReturn)] = $cMsj;
                }
              }
            }
          break;
          case "ANULAR":
    
            $mTarId = explode("~",$pArrayParametros['cTarId']);
    
            for($n=0;$n<count($mTarId);$n++) {
              if ($mTarId[$n] != "") {
                $qSql131  = "SELECT * ";
                $qSql131 .= "FROM $cAlfa.fpar0131 ";
                $qSql131 .= "WHERE ";
                $qSql131 .= "taridxxx = \"{$mTarId[$n]}\" ";
                $xSql131  = f_MySql("SELECT","",$qSql131,$xConexion01,"");
                // f_Mensaje(__FILE__,__LINE__,$qSql131."~".mysql_num_rows($xSql131));
                if (mysql_num_rows($xSql131) > 0) {
                  $xR131 = mysql_fetch_array($xSql131);
    
                  if ($xR131['regestxx'] == "ACTIVO" || $xR131['regestxx'] == "PROVISIONAL") {
                    $cEstado = "INACTIVO";
                  } else {
                    //Si esta activo el modulo de autorizacion de tarifas debe activarse en estado PROVISIONAL
                    if($vSysStr['system_financiero_autorizacion_tarifas'] == "SI"){
                      $cEstado = "PROVISIONAL";
                    } else {
                      $cEstado = "ACTIVO";
                    }
                  }
    
                  if ($cEstado == "ACTIVO" || $cEstado == "PROVISIONAL") {
                    /* Cadena de Sucursales */
                    $mSucId = explode("~",$xR131['sucidxxx']);
                    for($i=0;$i<count($mSucId);$i++){
                      $cCadSuc .= "sucidxxx LIKE \"%{$mSucId[$i]}%\" OR ";
                    }
                    $cCadSuc = substr($cCadSuc,0,strlen($cCadSuc)-3);
                    // Cadena de Tipo de Operacion
                    // Cadena de Medio Transporte
                    $mFcoMtr = explode("~",$xR131['fcomtrxx']);
                    for($i=0;$i<count($mFcoMtr);$i++){
                      $cCadFcoMtr .= "fcomtrxx LIKE \"%{$mFcoMtr[$i]}%\" OR ";
                    }
                    $cCadFcoMtr = substr($cCadFcoMtr,0,strlen($cCadFcoMtr)-3);
                    // Cadena de Medio Transporte
    
                    // Validando que no Exista el Mismo Cliente, Servicio, Forma de Cobro,  Tarifa y Sucursal en la Base de Datos en Estado ACTIVO
                    $qTarifa  = "SELECT * ";
                    $qTarifa .= "FROM $cAlfa.fpar0131 ";
                    $qTarifa .= "WHERE ";
                    $qTarifa .= "cliidxxx = \"{$xR131['cliidxxx']}\" AND ";
                    $qTarifa .= "seridxxx = \"{$xR131['seridxxx']}\" AND ";
                    $qTarifa .=	"fcoidxxx = \"{$xR131['fcoidxxx']}\" AND ";
                    $qTarifa .= "fcotptxx = \"{$xR131['fcotptxx']}\" AND ";
                    $qTarifa .=	"fcotpixx = \"{$xR131['fcotpixx']}\" AND ";
                    $qTarifa .= "($cCadSuc) AND ";
                    $qTarifa .=	"fcotopxx = \"{$xR131['fcotopxx']}\" AND ";
                    $qTarifa .= "($cCadFcoMtr) AND ";
                    $qTarifa .= "tartipxx = \"{$xR131['tartipxx']}\" AND ";
                    $qTarifa .= "regestxx != \"INACTIVO\"";
                    $xTarifa  = f_MySql("SELECT","",$qTarifa,$xConexion01,"");
                    // f_Mensaje(__FILE__,__LINE__,$qTarifa."~".mysql_num_rows($xTarifa));
    
                    if (mysql_num_rows($xTarifa) > 0) {
                      $nSwitch = 1;
    
                      //2024-05-29 - OC-33921 - Actualización Masiva de Tarifas
                      // Si la variable de control de vigencia de tarifas esta activa, se debe validar que no exista una tarifa con el mismo rango de fechas seleccionado
                      $cMsjAdic = "";
                      if ($vSysStr['system_control_vigencia_tarifas'] == "SI") {
                        $nSwitchVigencia = 0;
                        while ($xRT = mysql_fetch_array($xTarifa)) {
                          if (($xR131['tarfevde'] >= $xRT['tarfevde'] && $xR131['tarfevde'] <= $xRT['tarfevha']) ||
                              ($xR131['tarfevha'] >= $xRT['tarfevde'] && $xR131['tarfevha'] <= $xRT['tarfevha'])
                          ) {
                            $nSwitchVigencia = 1;
                            $cMsjAdic = " o Ya Existe una Tarifa en el Mismo Rango de Fechas de Vigencia";
                          }
                        }
    
                        if ($nSwitchVigencia == 1) {
                          $nSwitch = 1;
                        } else {
                          $nSwitch = 0;
                        }
                      }
    
                      if ($nSwitch == 1) {
                        $cTxtEst = "ACTIVO";
                        if($vSysStr['system_financiero_autorizacion_tarifas'] == "SI"){
                          $cTxtEst = "ACTIVO o PROVISIONAL";
                        }

                        $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                        $cMsj .= "Ya se Encuentra Parametrizada una Tarifa en estado $cTxtEst para Este Cliente ";
                        $cMsj .= "con el Mismo Concepto de Cobro y Forma de Cobro para ";
                        $cMsj .= "la Misma Sucursal o ";
                        $cMsj .= "Mismo Medio de Transporte o ";
                        $cMsj .= "el Mismo Tipo de Tarifa";
                        $cMsj .= $cMsjAdic . ".";
                        $mReturn[count($mReturn)] = $cMsj;
                      }
                    }
                    // Validando que no Exista el Mismo Cliente, Servicio, Forma de Cobro,  Tarifa y Sucursal en la Base de Datos en Estado ACTIVO
                  }
    
                  if ($nSwitch == 0) {
                    // 2023-07-19 Ticket: OC-30946 - Proyecto Especial LOG de la funcionalidad Autorizar Tarifas
                    if ($cAlfa == "DEALMAVIVA" || $cAlfa == "TEALMAVIVA" || $cAlfa == "ALMAVIVA" || $cAlfa == "TEPRUEBASX") {
                      //Cab
                      $nInd_mDatCab = count($mDatCab);
                      $mDatCab[$nInd_mDatCab]['logaccxx'] = $pArrayParametros['kModo'];
                      $mDatCab[$nInd_mDatCab]['tartipxx'] = $xR131['tartipxx'];
                      $mDatCab[$nInd_mDatCab]['cliidxxx'] = $xR131['cliidxxx'];
                      $mDatCab[$nInd_mDatCab]['seridxxx'] = $xR131['seridxxx'];
                      $mDatCab[$nInd_mDatCab]['serdespc'] = $xR131['serdespc'];
                      $mDatCab[$nInd_mDatCab]['fcoidxxx'] = $xR131['fcoidxxx'];
                      $mDatCab[$nInd_mDatCab]['fcotptxx'] = $xR131['fcotptxx'];
                      $mDatCab[$nInd_mDatCab]['fcotpixx'] = $xR131['fcotpixx'];
                      $mDatCab[$nInd_mDatCab]['sucidxxx'] = $xR131['sucidxxx'];
                      $mDatCab[$nInd_mDatCab]['fcotopxx'] = $xR131['fcotopxx'];
                      $mDatCab[$nInd_mDatCab]['fcomtrxx'] = $xR131['fcomtrxx'];
    
                      //Old
                      $nInd_mDatOld = count($mDatOld);
                      $mDatOld[$nInd_mDatOld]['regusrxx'] = $xR131['regusrxx'];
                      $mDatOld[$nInd_mDatOld]['regfmodx'] = $xR131['regfmodx'];
                      $mDatOld[$nInd_mDatOld]['reghmodx'] = $xR131['reghmodx'];
                      $mDatOld[$nInd_mDatOld]['regestxx'] = $xR131['regestxx'];
    
                      //New
                      $nInd_mDatNew = count($mDatNew);
                      $mDatNew[$nInd_mDatNew]['regusrxx'] = trim(strtoupper($pArrayParametros['kUsrId']));
                      $mDatNew[$nInd_mDatNew]['regfmodx'] = $dFecha;
                      $mDatNew[$nInd_mDatNew]['reghmodx'] = $dHora;
                      $mDatNew[$nInd_mDatNew]['regestxx'] = $cEstado;
                    }
    
                    $zInsertCab	= array(array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($pArrayParametros['kUsrId'])) ,'CHECK'=>'SI'),
                                        array('NAME'=>'regfmodx','VALUE'=>$dFecha												                ,'CHECK'=>'SI'),
                                        array('NAME'=>'reghmodx','VALUE'=>$dHora		                                    ,'CHECK'=>'SI'),
                                        array('NAME'=>'regestxx','VALUE'=>$cEstado                                      ,'CHECK'=>'SI'),
                                        array('NAME'=>'taridxxx','VALUE'=>$xR131['taridxxx'] 									          ,'CHECK'=>'WH'));
    
                    if (!f_MySql("UPDATE","fpar0131",$zInsertCab,$xConexion01,$cAlfa)) {
                      $nSwitch = 1;
                      $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                      $cMsj .= "Error al Actualizar el Registro [{$mTarId[$n]}].";
                      $mReturn[count($mReturn)] = $cMsj;
                    }
                  }
                } else {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "La Tarifa [{$mTarId[$n]}] No Existe.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
              }
            }
          break;
          case "AUTORIZARTARIFA":
    
            $mTarId = explode("~",$pArrayParametros['cTarId']);
    
            for($n=0;$n<count($mTarId);$n++) {
              if ($mTarId[$n] != "") {
                $qTarifas  = "SELECT * ";
                $qTarifas .= "FROM $cAlfa.fpar0131 ";
                $qTarifas .= "WHERE ";
                $qTarifas .= "taridxxx = \"{$mTarId[$n]}\" ";
                $xTarifas  = f_MySql("SELECT","",$qTarifas,$xConexion01,"");
                //f_Mensaje(__FILE__,__LINE__,$qTarifas."~".mysql_num_rows($xTarifas));
                if (mysql_num_rows($xTarifas) > 0) {
                  $vTarifas = mysql_fetch_array($xTarifas);
    
                  if ($nSwitch == 0) {
                    // 2023-07-19 Ticket: OC-30946 - Proyecto Especial LOG de la funcionalidad Autorizar Tarifas
                    if ($cAlfa == "DEALMAVIVA" || $cAlfa == "TEALMAVIVA" || $cAlfa == "ALMAVIVA" || $cAlfa == "TEPRUEBASX") {
                      //Cab
                      $nInd_mDatCab = count($mDatCab);
                      $mDatCab[$nInd_mDatCab]['logaccxx'] = $pArrayParametros['kModo'];
                      $mDatCab[$nInd_mDatCab]['tartipxx'] = $vTarifas['tartipxx'];
                      $mDatCab[$nInd_mDatCab]['cliidxxx'] = $vTarifas['cliidxxx'];
                      $mDatCab[$nInd_mDatCab]['seridxxx'] = $vTarifas['seridxxx'];
                      $mDatCab[$nInd_mDatCab]['serdespc'] = $vTarifas['serdespc'];
                      $mDatCab[$nInd_mDatCab]['fcoidxxx'] = $vTarifas['fcoidxxx'];
                      $mDatCab[$nInd_mDatCab]['fcotptxx'] = $vTarifas['fcotptxx'];
                      $mDatCab[$nInd_mDatCab]['fcotpixx'] = $vTarifas['fcotpixx'];
                      $mDatCab[$nInd_mDatCab]['sucidxxx'] = $vTarifas['sucidxxx'];
                      $mDatCab[$nInd_mDatCab]['fcotopxx'] = $vTarifas['fcotopxx'];
                      $mDatCab[$nInd_mDatCab]['fcomtrxx'] = $vTarifas['fcomtrxx'];
    
                      //Old
                      $nInd_mDatOld = count($mDatOld);
                      $mDatOld[$nInd_mDatOld]['regusrxx'] = $vTarifas['regusrxx'];
                      $mDatOld[$nInd_mDatOld]['regfmodx'] = $vTarifas['regfmodx'];
                      $mDatOld[$nInd_mDatOld]['reghmodx'] = $vTarifas['reghmodx'];
                      $mDatOld[$nInd_mDatOld]['regestxx'] = $vTarifas['regestxx'];
    
                      //New
                      $nInd_mDatNew = count($mDatNew);
                      $mDatNew[$nInd_mDatNew]['regusrxx'] = trim(strtoupper($pArrayParametros['kUsrId']));
                      $mDatNew[$nInd_mDatNew]['regfmodx'] = $dFecha;
                      $mDatNew[$nInd_mDatNew]['reghmodx'] = $dHora;
                      $mDatNew[$nInd_mDatNew]['regestxx'] = "ACTIVO";
                    }
                    
                    $qUpdate	= array(array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($pArrayParametros['kUsrId'])) ,'CHECK'=>'SI'),
                                      array('NAME'=>'regfmodx','VALUE'=>$dFecha												                ,'CHECK'=>'SI'),
                                      array('NAME'=>'reghmodx','VALUE'=>$dHora		                                    ,'CHECK'=>'SI'),
                                      array('NAME'=>'regestxx','VALUE'=>"ACTIVO"                                      ,'CHECK'=>'SI'),
                                      array('NAME'=>'taridxxx','VALUE'=>$vTarifas['taridxxx'] 							          ,'CHECK'=>'WH'));
    
                    if (!f_MySql("UPDATE","fpar0131",$qUpdate,$xConexion01,$cAlfa)) {
                      $nSwitch = 1;
                      $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                      $cMsj .= "Error al Actualizar el Registro [{$mTarId[$n]}].";
                      $mReturn[count($mReturn)] = $cMsj;
                    }
                  }
                } else {
                  $nSwitch = 1;
                  $cMsj  = ($pArrayParametros['cOrigen'] != "REPORTE") ? "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": " : "";
                  $cMsj .= "La Tarifa [{$mTarId[$n]}] No Existe.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
              }
            }
          break;
        }
      }
    
      if ($nSwitch == 0) {
        // 2023-07-19 Ticket: OC-30946 - Proyecto Especial LOG de la funcionalidad Autorizar Tarifas
        if ($cAlfa == "DEALMAVIVA" || $cAlfa == "TEALMAVIVA" || $cAlfa == "ALMAVIVA" || $cAlfa == "TEPRUEBASX") {
          //Insertando en el LOG de Tarifas
          $nAnio = date('Y');
    
          //Si la tabla del año no existe, se debe crear
          $vReturnLog = $this->fnCrearTablaLogTarifas();
          if ($vReturnLog[0] == "false") {
            $cMsjAdv = "";
            for ($nR=1; $nR<count($vReturnLog); $nR++) {
              $cMsjAdv .= $vReturnLog[$nR]."";
            }
            f_Mensaje(__FILE__,__LINE__,$cMsjAdv);
          } else {
            $nErrLog = 0;
            for ($nT=0; $nT<count($mDatCab);$nT++) {
              //Buscando descripcion servicio
              $qServicio  = "SELECT *, ";
              $qServicio .= "IF(serdespx != \"\",serdespx,serdesxx) AS serdesxx ";
              $qServicio .= "FROM $cAlfa.fpar0129 ";
              $qServicio .= "WHERE ";
              $qServicio .= "seridxxx = \"{$mDatCab[$nT]['seridxxx']}\" LIMIT 0,1";
              $xServicio  = f_MySql("SELECT","",$qServicio,$xConexion01,"");
              if (mysql_num_rows($xServicio) > 0){$vServicio = mysql_fetch_array($xServicio);}else{$vServicio['serdesxx'] = "SERVICIO SIN DESCRIPCION";}
    
              //Buscando descripcion forma de cobro
              $qForCob  = "SELECT * ";
              $qForCob .= "FROM $cAlfa.fpar0130 ";
              $qForCob .= "WHERE ";
              $qForCob .= "fcoidxxx = \"{$mDatCab[$nT]['fcoidxxx']}\" LIMIT 0,1";
              $xForCob  = f_MySql("SELECT","",$qForCob,$xConexion01,"");
              if (mysql_num_rows($xForCob) > 0){$vForCob = mysql_fetch_array($xForCob);}else{$vForCob['fcodesxx'] = "FORMA DE COBRO SIN DESCRIPCION";}
    
              // Nombre del Cliente o Descripcion Grupo
              if ($mDatCab[$nT]['tartipxx'] == "CLIENTE") {
                // Busco el Nombre del Cliente
                $qNomCG  = "SELECT ";
                $qNomCG .= "IF(CLINOMXX != \"\",CLINOMXX,CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) AS clinomxx ";
                $qNomCG .= "FROM $cAlfa.SIAI0150 ";
                $qNomCG .= "WHERE ";
                $qNomCG .= "CLIIDXXX = \"{$mDatCab[$nT]["cliidxxx"]}\" LIMIT 0,1";
                $xNomCG  = f_MySql("SELECT","",$qNomCG,$xConexion01,"");
                if (mysql_num_rows($xNomCG) > 0){$vNomCG = mysql_fetch_array($xNomCG);}else{$vNomCG['clinomxx'] = "CLIENTE SIN NOMBRE";}
                // Fin de Busco el Nombre del Cliente
              } else {
                //Busco Grupo de Tarifas
                $qNomCG  = "SELECT ";
                $qNomCG .= "gtadesxx AS clinomxx ";
                $qNomCG .= "FROM $cAlfa.fpar0111 ";
                $qNomCG .= "WHERE ";
                $qNomCG .= "$cAlfa.fpar0111.gtaidxxx = \"{$mDatCab[$nT]["cliidxxx"]}\" LIMIT 0,1";
                $xNomCG  = f_MySql("SELECT","",$qNomCG,$xConexion01,"");
                //f_Mensaje(__FILE__,__LINE__,$qGruTar."~".mysql_num_rows($xGruTar));
                if (mysql_num_rows($xNomCG) > 0){$vNomCG = mysql_fetch_array($xNomCG);}else{$vNomCG['clinomxx'] = "GRUPO TARIFA SIN NOMBRE";}
                // Fin de Busco el Nombre del Cliente
              }
    
              //Buscando descripcion tarifa por
              // Tener en cuenta que si se va activar la funcionalidad para alpopular, 
              // la logica para buscar la descripcion del producto o proyecto es particular y no se migro
              if($mDatCab[$nT]['fcotptxx'] == "PROYECTO"){
                $qTarPor  = "SELECT prydesxx ";
                $qTarPor .= "FROM $cAlfa.fpar0142 ";
                $qTarPor .= "WHERE ";
                $qTarPor .= "pryidxxx = \"{$mDatCab[$nT]['fcotpixx']}\" AND ";
                $qTarPor .= "cliidxxx = \"{$mDatCab[$nT]['cliidxxx']}\" LIMIT 0,1";
                $xTarPor  = f_MySql("SELECT","",$qTarPor,$xConexion01,"");
                if (mysql_num_rows($xTarPor) > 0){$vTarPor = mysql_fetch_array($xTarPor);}else{$vTarPor['prydesxx'] = "{$mDatCab[$nT]['fcotptxx']} SIN DESCRIPCION";}
              }elseif($mDatCab[$nT]['fcotptxx'] == "PRODUCTO"){
                $qTarPor  = "SELECT prodesxx as prydesxx ";
                $qTarPor .= "FROM $cAlfa.fpar0143 ";
                $qTarPor .= "WHERE ";
                $qTarPor .= "proidxxx = \"{$mDatCab[$nT]['fcotpixx']}\" LIMIT 0,1";
                $xTarPor  = f_MySql("SELECT","",$qTarPor,$xConexion01,"");
                if (mysql_num_rows($xTarPor) > 0){$vTarPor = mysql_fetch_array($xTarPor);}else{$vTarPor['prydesxx'] = "{$mDatCab[$nT]['fcotptxx']} SIN DESCRIPCION";}
              } else {
                $vTarPor['prydesxx'] = "TARIFA GENERAL";
              }
    
              //Buscando el nombre del usuario autenticado
              $qDatUsu  = "SELECT ";
              $qDatUsu .= "IF(USRNOMXX != \"\",USRNOMXX,\"USUARIO SIN NOMBRE\") AS USRNOMXX ";
              $qDatUsu .= "FROM $cAlfa.SIAI0003 ";
              $qDatUsu .= "WHERE ";
              $qDatUsu .= "USRIDXXX = \"{$pArrayParametros['kUsrId']}\" LIMIT 0,1 ";
              $xDatUsu = f_MySql("SELECT","",$qDatUsu,$xConexion01,"");
              $vDatUsu = mysql_fetch_array($xDatUsu);
    
              //Buscando nombre de los campos para guardar en detalle
              //Inicializando la matriz de datos para completar nombre del campo de datalle
              $mDatOldCom = array();
              $mDatNewCom = array();
              if (!empty($mDatOld) || !empty($mDatNew)) {
                //Trayendo descripciones de la condiciones especiales
                if ($cCamEsp != "" && ($mDatOld[$nT]['fcopcexx'] != "" || $mDatNew[$nT]['fcopcexx'] != "")) {
                  
                  $qCampos  = "SELECT dcecampo, dcedesxx ";
                  $qCampos .= "FROM $cAlfa.fpar0145 ";
                  $qCampos .= "WHERE ";
                  $qCampos .= "seridxxx = \"{$mDatCab[$nT]['seridxxx']}\" AND ";
                  $qCampos .= "fcoidxxx = \"{$mDatCab[$nT]['fcoidxxx']}\" AND ";
                  $qCampos .= "dcecampo IN ($cCamEsp) ";
                  $xCampos  = f_MySql("SELECT","",$qCampos,$xConexion01,"");
                  // f_Mensaje(__FILE__,__LINE__,$qCampos."~".mysql_num_rows($xCampos));
                  $vCampos = array();
                  while ($xRC = mysql_fetch_array($xCampos)) {
                    $vCampos["{$xRC['dcecampo']}"] = $xRC['dcedesxx'];
                  }
    
                  if (!empty($mDatOld)) {
                    $mCamEsp = f_Explode_Array($mDatOld[$nT]['fcopcexx'],"|","~");
                    $cCamAux = "";
                    for ($i=0; $i<count($mCamEsp); $i++) {
                      if ($mCamEsp[$i][0] != "") {
                        $cCamAux .= $vCampos["{$mCamEsp[$i][0]}"]."~".$mCamEsp[$i][1]."|";
                      }
                    }
                    $mDatOld[$nT]['fcopcexx'] = substr($cCamAux, 0, -1);
                  }
    
                  if (!empty($mDatNew)) {
                    $mCamEsp = f_Explode_Array($mDatNew[$nT]['fcopcexx'],"|","~");
                    $cCamAux = "";
                    for ($i=0; $i<count($mCamEsp); $i++) {
                      if ($mCamEsp[$i][0] != "") {
                        $cCamAux .= $vCampos["{$mCamEsp[$i][0]}"]."~".$mCamEsp[$i][1]."|";
                      }
                    }
                    $mDatNew[$nT]['fcopcexx'] = substr($cCamAux, 0, -1);
                  }
                }
    
                // Trayendo descripcion de los campos de la tabla fpar0131
                $mCamTar = array();
                $xDescTabla = mysql_query("SHOW FULL COLUMNS FROM $cAlfa.fpar0131 ",$xConexion01);
                while($xRD = mysql_fetch_array($xDescTabla)) {
                  $mCamTar["{$xRD['Field']}"] = $xRD['Comment'];
                }
    
                if (!empty($mDatOld)) {
                  // Completando Informacion de $mDatOld
                  foreach ($mDatOld[$nT] as $cKey => $cValue) {
                    $nInd_mDatOldCom = count($mDatOldCom);
                    $mDatOldCom[$nInd_mDatOldCom]['campoxxx'] = $cKey;
                    $mDatOldCom[$nInd_mDatOldCom]['descamxx'] = $mCamTar[$cKey];
                    $mDatOldCom[$nInd_mDatOldCom]['valorxxx'] = $cValue;
                  }
                }              
    
                // Completando Informacion de $mDatNew
                if (!empty($mDatNew)) {
                  foreach ($mDatNew[$nT] as $cKey => $cValue) {
                    $nInd_mDatNewCom = count($mDatNewCom);
                    $mDatNewCom[$nInd_mDatNewCom]['campoxxx'] = $cKey;
                    $mDatNewCom[$nInd_mDatNewCom]['descamxx'] = $mCamTar[$cKey];
                    $mDatNewCom[$nInd_mDatNewCom]['valorxxx'] = $cValue;
                  }
                }
              }
              
              $cInsertLog	= array(array('NAME'=>'logaccxx','VALUE'=>$pArrayParametros['kModo']                                                      ,'CHECK'=>'SI'), // Accion
                                  array('NAME'=>'tartipxx','VALUE'=>$mDatCab[$nT]['tartipxx']                                                       ,'CHECK'=>'SI'), // Tipo de Tarifa
                                  array('NAME'=>'cliidxxx','VALUE'=>$mDatCab[$nT]['cliidxxx']                                                       ,'CHECK'=>'SI'), // Nit del Cliente o Id Grupo
                                  array('NAME'=>'clinomxx','VALUE'=>$vNomCG['clinomxx']                                                             ,'CHECK'=>'NO'), // Nombre del Cliente o Descripcion Grupo
                                  array('NAME'=>'seridxxx','VALUE'=>$mDatCab[$nT]['seridxxx']                                                       ,'CHECK'=>'SI'), // Id del Concepto de Cobro
                                  array('NAME'=>'serdesxx','VALUE'=>$vServicio['serdesxx']                                                          ,'CHECK'=>'NO'), // Descripcion del Concepto de Cobro
                                  array('NAME'=>'serdespc','VALUE'=>$mDatCab[$nT]['serdespc']                                                       ,'CHECK'=>'NO'), // Descripcion personalizada del Concepto de Cobro
                                  array('NAME'=>'fcoidxxx','VALUE'=>$mDatCab[$nT]['fcoidxxx']                                                       ,'CHECK'=>'SI'), // Id Forma de Cobro
                                  array('NAME'=>'fcodesxx','VALUE'=>$vForCob['fcodesxx']                                                            ,'CHECK'=>'NO'), // Descripcion Forma de Cobro
                                  array('NAME'=>'fcotptxx','VALUE'=>$mDatCab[$nT]['fcotptxx']                                                       ,'CHECK'=>'SI'), // Tarifa Por
                                  array('NAME'=>'fcotpixx','VALUE'=>$mDatCab[$nT]['fcotpixx']                                                       ,'CHECK'=>'SI'), // Id Tarifa Por
                                  array('NAME'=>'fcotpdxx','VALUE'=>$vTarPor['prydesxx']                                                            ,'CHECK'=>'NO'), // Descripcion Tarifa Por
                                  array('NAME'=>'sucidxxx','VALUE'=>$mDatCab[$nT]['sucidxxx']                                                       ,'CHECK'=>'SI'), // Sucursales
                                  array('NAME'=>'fcotopxx','VALUE'=>$mDatCab[$nT]['fcotopxx']                                                       ,'CHECK'=>'SI'), // Tipo de Operacion
                                  array('NAME'=>'fcomtrxx','VALUE'=>$mDatCab[$nT]['fcomtrxx']                                                       ,'CHECK'=>'SI'), // Modo de Transporte
                                  array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($pArrayParametros['kUsrId']))                                   ,'CHECK'=>'SI'), // Id Usuario
                                  array('NAME'=>'regusrno','VALUE'=>$vDatUsu['USRNOMXX']                                                            ,'CHECK'=>'NO'), // Nombre Usuario
                                  array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')                                                                   ,'CHECK'=>'SI'), // Fecha de Creacion
                                  array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')                                                                   ,'CHECK'=>'SI'), // Hora de Creacion
                                  array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')                                                                   ,'CHECK'=>'SI'), // Fecha de Modificacion
                                  array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')                                                                   ,'CHECK'=>'SI'), // Hora de Modificacion
                                  array('NAME'=>'regestxx','VALUE'=>"ACTIVO"                                                                        ,'CHECK'=>'SI')); // Estado
              if (!empty($mDatOld)) {
                $cInsertLog[] = array('NAME'=>'logoldxx','VALUE'=>((!empty($mDatOldCom)) ? str_replace('"','\"',json_encode($mDatOldCom)) : NULL)  ,'CHECK'=>'NO'); // Valores Anteriores
              }
              if (!empty($mDatNew)) {
                $cInsertLog[] = array('NAME'=>'lognewxx','VALUE'=>((!empty($mDatNewCom)) ? str_replace('"','\"',json_encode($mDatNewCom)) : NULL)  ,'CHECK'=>'NO'); // Valores Nuevos
              }
              if (!f_MySql("INSERT","flta$nAnio",$cInsertLog,$xConexion01,$cAlfa)) {
                $nErrLog = 1;
              }
            }
            if($nErrLog == 1) {
              $mReturn[count($mReturn)] = "Error al Insertar el registro en el Log de Tarifas.";
            }
          }
        }
      }

      // 2024-09-30 - OC-33921 - OC-38006 - Actualización Masiva de Tarifas
      // Si la variable de control de vigencia de tarifas esta activa
      // y la base de datos es GRUMALCO se debe actualizar la vigencia de la cotizacion del cliente
      // o de los clientes del grupo de tarifas
      if ($nSwitch == 0) {
        if ($vSysStr['system_control_vigencia_tarifas'] == "SI") {
          if ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") {
            switch ($pArrayParametros['kModo']) {
              case "NUEVO":
              case "EDITAR":
                $vDatosTarifa['cTarTip'] = $pArrayParametros['cTarTip'];
                $vDatosTarifa['cCliId']  = $pArrayParametros['cCliId'];
                $mReturnVigencia = $this->fnActualizarVigenciaCotizacion($vDatosTarifa);
                if ($mReturnVigencia[0] == "false") {
                  for ($nR=1; $nR<count($mReturnVigencia); $nR++) {
                    $mReturn[count($mReturn)] = $mReturnVigencia[$nR];
                  }
                }
              break;
              default:
                //No hace nada
              break;
            }
          }
        }
      }

      if ($nSwitch == 0) {
        $mReturn[0] = "true";
      } else {
        $mReturn[0] = "false";
      }

      return $mReturn;
    } ## function fnGuardarTarifas($pArrayParametros) { ##

    /**
     * Metodo para actualizar la vigencia de la cotizacion del cliente o de los clientes del grupo de tarifas
     */
    function fnActualizarVigenciaCotizacion($pArrayParametros){
      global $xConexion01; global $cAlfa; global $vSysStr; global $kUser;

      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var int
       */
      $nSwitch = 0;

      /**
       * Variable para hacer el retorno.
       * 
       * @var array
       */
      $mReturn    = array();
      $mReturn[0] = "";

      // Trayendo la fecha de vigencia hasta maxima
      $qSql131  = "SELECT MAX(tarfevha) AS tarfevha ";
      $qSql131 .= "FROM $cAlfa.fpar0131 ";
      $qSql131 .= "WHERE ";
      $qSql131 .= "cliidxxx = \"{$pArrayParametros['cCliId']}\" AND ";
      $qSql131 .= "tartipxx = \"{$pArrayParametros['cTarTip']}\" AND ";
      $qSql131 .= "regestxx != \"INACTIVO\"";
      $xSql131  = f_MySql("SELECT","",$qSql131,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qSql131."~".mysql_num_rows($xSql131));

      if (mysql_num_rows($xSql131) > 0) {
        $vqSql131 = mysql_fetch_array($xSql131);

        if ($pArrayParametros['cTarTip'] == "CLIENTE") {
          $qUpdate	= array(array('NAME'=>'cccfhcxx','VALUE'=>$vqSql131['tarfevha']       ,'CHECK'=>'SI'),
                            array('NAME'=>'cliidxxx','VALUE'=>$pArrayParametros['cCliId'] ,'CHECK'=>'WH'));

          if (!f_MySql("UPDATE","fpar0151",$qUpdate,$xConexion01,$cAlfa)) {
            $mReturn[count($mReturn)] = "Error al Actualizar Fecha Vigencia Cotizacion del Cliente.";
          }
        } else {
          $qUpdate	= array(array('NAME'=>'cccfhcxx','VALUE'=>$vqSql131['tarfevha']       ,'CHECK'=>'SI'),
                            array('NAME'=>'gtaidxxx','VALUE'=>$pArrayParametros['cCliId'] ,'CHECK'=>'WH'));

          if (!f_MySql("UPDATE","fpar0151",$qUpdate,$xConexion01,$cAlfa)) {
            $mReturn[count($mReturn)] = "Error al Actualizar Fecha Vigencia Cotizacion del Grupo de Tarifas.";
          }
        }
      }

      if ($nSwitch == 0) {
        $mReturn[0] = "true";
      } else {
        $mReturn[0] = "false";
      }

      return $mReturn;
    }
    
    function fnCrearTablaLogTarifas() {
      global $cAlfa;
  
      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var number
       */
      $nSwitch = 0;
  
      /**
       * Matriz para Retornar Errores
       */
      $vReturn = array();
  
      /**
       * Variable para hacer el retorno.
       * @var array
       */
      $vReturn[0] = "";
      
      /**
       * Variable para hacer el retorno.
       * @var Number
       */
      $nAnio = date('Y');
      
      /**
        * Hacer la conexion a la base de datos
        */
      $xConexion01 = mysql_connect(OC_SERVER,OC_USERROBOT,OC_PASSROBOT);
      
      if (!$xConexion01) {
        $nSwitch = 1;
        $vReturn[count($vReturn)] = "Error al Establecer Conexion.";
      }
      
      if ($nSwitch == 0) {
        /**
         * Creando la tabla de log si no exite
         */
        $nAnioAnterior = date('Y')-1;
        
        $qTabAct = "SHOW TABLES FROM $cAlfa LIKE \"flta$nAnio\" "; 
        $xTabAct = mysql_query($qTabAct,$xConexion01);
        // f_Mensaje(__FILE__,__LINE__,$qTabAct."~".mysql_num_rows($xTabAct));
        if(mysql_num_rows($xTabAct) == 0){
          
          $qTabAnt = "SHOW TABLES FROM $cAlfa LIKE \"flta$nAnioAnterior\" "; 
          $xTabAnt = mysql_query($qTabAnt,$xConexion01);
          
          if(mysql_num_rows($xTabAnt) == 0){
            $nSwitch = 1;
            $vReturn[count($vReturn)] = "Error al Crear Tabla [flta$nAnio], No Existe la Tabla [flta$nAnioAnterior], Comuniquese con openTecnologia.".$qCreate."~".str_replace("'", " ", mysql_error($xConexion01));
          }else{
            $qCreate = "CREATE TABLE IF NOT EXISTS $cAlfa.flta$nAnio LIKE $cAlfa.flta$nAnioAnterior ";
            $xCreate = mysql_query($qCreate,$xConexion01);
            
            if (!$xCreate) {
              $nSwitch = 1;
              $vReturn[count($vReturn)] = "Error al crear Tabla [flta$nAnio] para Log, Comuniquese con openTecnologia.".$qCreate."~".str_replace("'", " ", mysql_error($xConexion01));
            }
          }
        }
      }
  
      if ($nSwitch == 0) {
        $vReturn[0] = "true";
      } else {
        $vReturn[0] = "false";
      }
      return $vReturn;      
    } ## function fnCrearTablaLogTarifas() { ##

    /**
     * Metodo para obtener la data del Reporte de Tarifas Consolidad.
     */
    function fnReporteTarifasConsolidado($pArrayParametros) {

      global $xConexion01; global $cAlfa; global $vSysStr; global $kUser;

      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var int
       */
      $nSwitch = 0;

      /**
       * Variable para hacer el retorno.
       * 
       * @var array
       */
      $mReturn    = array();
      $mReturn[0] = "";

      /**
       * Variable para alamacenar errores.
       * 
       * @var array
       */
      $vError = array();
      $vError['TABLAERR'] = $pArrayParametros['TABLAERR'];

      /**
       * Instanciando Objeto para el Guardado de Errores.
       */
      $objEstructuraTarifasFacturacion = new cEstructurasTarfiasFacturacion();

      // Array con informacion de parametricas
      $vClientes = array();
      $mClientes = array();

      $vGrupos = array();
      $mGrupos = array();

      $vServicios = array();
      $mServicios = array();

      $vForCob = array();
      $mForCob = array();

      $vUsuarios = array();
      $mUsuarios = array();

      $vConEsp = array();
      $mConEsp = array();

      // Array con los campos dinamico creados
      $vCamDin     = array(); // Campos sueltos
      $vCamDinN    = array(); // Campos Tarifas con Niveles
      $vCamDinNC20 = array(); // Campos Tarifas con Niveles C20
      $vCamDinNC40 = array(); // Campos Tarifas con Niveles C40
      $vCamDinVeh  = array(); // Campos Tarifas con Niveles Vehiculos (aplica para GRUMALCO)

      // Datos Insert
      // Insertando Datos Base de las tarifas en la tabla temporal
      $qInsCab  = "INSERT INTO $cAlfa.{$pArrayParametros['TABLAXXX']} (";
      $qInsCab .= "NIT_CLIENTE_O_ID_GRUPO,";
      $qInsCab .= "NOMBRE_CLIENTE_O_DESCRIPCION_GRUPO,";
      $qInsCab .= "APLICA_TARIFA_PARA,";
      $qInsCab .= "ESTADO_CLIENTE_O_GRUPO,";
      $qInsCab .= "ESTADO_TARIFA,";
      if ($vSysStr['system_control_vigencia_tarifas'] == "SI") {
        $qInsCab .= "VIGENCIA_DESDE,";
        $qInsCab .= "VIGENCIA_HASTA,";
        $qInsCab .= "NUEVA_VIGENCIA_DESDE,";
        $qInsCab .= "NUEVA_VIGENCIA_HASTA,";
        $qInsCab .= "FECHA_MODIFICACION_VIGENCIA,";
        $qInsCab .= "ACTUALIZAR_VIGENCIA,";
      }
      $qInsCab .= "ID_CONCEPTO_COBRO,";
      $qInsCab .= "DESCRIPCION_CONCEPTO_COBRO,";
      $qInsCab .= "DESCRIPCION_PERSONALIZADA_CONCEPTO_COBRO,";
      $qInsCab .= "ID_FORMA_COBRO,";
      $qInsCab .= "DESCRIPCION_FORMA_COBRO,";
      $qInsCab .= "MONEDA,";
      $qInsCab .= "CONDICION_ESPECIAL,";
      $qInsCab .= "CONDICION_ESPECIAL_PERSONALIZADA,";
      $qInsCab .= "APLICA_TARIFA_POR,";
      $qInsCab .= "ID_APLICA_TARIFA_POR,";
      $qInsCab .= "DESCRIPCION_APLICA_TARIFA_POR,";
      $qInsCab .= "TIPO_OPERACION,";
      $qInsCab .= "SUCURSALES,";
      $qInsCab .= "MODO_TRANSPORTE,";
      //Entre estos campos el reporte genera los campos dinamicos de las tarifas
      $qInsCab .= "ID_USUARIO_CREACION,";
      $qInsCab .= "NOMBRE_USUARIO_CREACION,";
      $qInsCab .= "ID_USUARIO_MODIFICACION,";
      $qInsCab .= "NOMBRE_USUARIO_MODIFICACION,";
      $qInsCab .= "FECHA_CREACION,";
      $qInsCab .= "FECHA_MODIFICACION,";
      $qInsCab .= "COLUMNAS_RESALTADAS) VALUES ";

      // SQL_CALC
      $nNumReg = 100;

      $qLoad  = "SELECT SQL_CALC_FOUND_ROWS ";
      $qLoad .= "$cAlfa.fpar0131.* ";
      $qLoad .= "FROM $cAlfa.fpar0131 ";
      if ($pArrayParametros['APLITARX'] == "CLIENTE") {
        if ($pArrayParametros['ESTCLIXX'] != "TODOS") {
          $qLoad .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fpar0131.cliidxxx = $cAlfa.SIAI0150.CLIIDXXX ";
        }
      } else {
        if ($pArrayParametros['ESTCLIXX'] != "TODOS") {
          $qLoad .= "LEFT JOIN $cAlfa.fpar0111 ON $cAlfa.fpar0131.cliidxxx = $cAlfa.fpar0111.gtaidxxx ";
        }
      }
      $qLoad .= "WHERE ";
      // Estado Tarifas
      if ($pArrayParametros['ESTTARIX'] == "TODOS") {
        $qLoad .= "$cAlfa.fpar0131.regestxx IN(\"ACTIVO\",\"INACTIVO\") AND ";
      } else if ($pArrayParametros['ESTTARIX'] != "") {
        $qLoad .= "$cAlfa.fpar0131.regestxx = \"{$pArrayParametros['ESTTARIX']}\" AND ";
      }
      // Tipo Operacion
      if ($pArrayParametros['TIPOPEXX'] == "TODOS") {
        $qLoad .= "$cAlfa.fpar0131.fcotopxx IN(\"IMPORTACION\",\"EXPORTACION\",\"TRANSITO\",\"OTROS\") AND ";
      } else if ($pArrayParametros['TIPOPEXX'] != "") {
        $qLoad .= "$cAlfa.fpar0131.fcotopxx = \"{$pArrayParametros['TIPOPEXX']}\" AND ";
      }
      // Tipo Operacion
      if ($pArrayParametros['TIPOPEXX'] == "TODOS") {
        $qLoad .= "$cAlfa.fpar0131.fcotopxx IN(\"IMPORTACION\",\"EXPORTACION\",\"TRANSITO\",\"OTROS\") AND ";
      } else if ($pArrayParametros['TIPOPEXX'] != "") {
        $qLoad .= "$cAlfa.fpar0131.fcotopxx = \"{$pArrayParametros['TIPOPEXX']}\" AND ";
      }
      // Cliente o Grupo
      if ($pArrayParametros['CLIIDXXX'] != "") {
        $qLoad .= "$cAlfa.fpar0131.cliidxxx = \"{$pArrayParametros['CLIIDXXX']}\" AND ";
      }
      //Estado Cliente o Grupo
      if ($pArrayParametros['APLITARX'] == "CLIENTE") {
        if ($pArrayParametros['ESTCLIXX'] != "TODOS") {
          $qLoad .= "$cAlfa.SIAI0150.regestxx = \"{$pArrayParametros['ESTCLIXX']}\" AND ";
        }
      } else{
        if ($pArrayParametros['ESTCLIXX'] != "TODOS") {
          $qLoad .= "$cAlfa.fpar0111.regestxx = \"{$pArrayParametros['ESTCLIXX']}\" AND ";
        }
      }
      if ($pArrayParametros['SERIDXXX'] != "") {
        $qLoad .= "$cAlfa.fpar0131.seridxxx = \"{$pArrayParametros['SERIDXXX']}\" AND ";
      }
      if ($pArrayParametros['FCOIDXXX'] != "") {
        $qLoad .= "$cAlfa.fpar0131.fcoidxxx = \"{$pArrayParametros['FCOIDXXX']}\" AND ";
      }
      if ($pArrayParametros['FECHASTA'] != "" && $pArrayParametros['FECDESDE'] != "") {
        // Rango de Fechas
        switch ($pArrayParametros['TIPOFECX']) {
          case "CREACION":
          case "ACTUALIZACION":
            $cCamFec = ($pArrayParametros['TIPOFECX'] == "CREACION") ? "regfcrex" : "regfmodx";
            $qLoad .= "$cAlfa.fpar0131.$cCamFec BETWEEN \"{$pArrayParametros['FECDESDE']}\" AND \"{$pArrayParametros['FECHASTA']}\" AND ";
          break;
          default:
            // Vigencia
            $qLoad .= "$cAlfa.fpar0131.tarfevde >= \"{$pArrayParametros['FECDESDE']}\" AND ";
            $qLoad .= "$cAlfa.fpar0131.tarfevha <= \"{$pArrayParametros['FECHASTA']}\" AND ";
          break;
        }
      }
      $qLoad  = substr($qLoad, 0, -4);
      $qLoad .= "ORDER BY $cAlfa.fpar0131.cliidxxx,$cAlfa.fpar0131.seridxxx,$cAlfa.fpar0131.fcoidxxx";
      $xLoad  = f_MySql("SELECT","",$qLoad,$xConexion01,"");

      mysql_free_result($xLoad);

      $xNumRows = mysql_query("SELECT FOUND_ROWS();");
      $xRNR = mysql_fetch_array($xNumRows);
      $nRegistros = $xRNR['FOUND_ROWS()'];
      mysql_free_result($xNumRows);
      echo "\nCantidad de Registros: ".$nRegistros;

      // FOR
      for ($i=0;$i<=$nRegistros;$i+=$nNumReg) {

        /*** Reinicio de conexion. ***/
        $xConexion01 = $objEstructuraTarifasFacturacion->fnReiniciarConexionDBRTarifasFacturacion($xConexion01);
        
        // Consulta las tarifas
        $qTarifas  = "SELECT ";
        $qTarifas .= "$cAlfa.fpar0131.* ";
        $qTarifas .= "FROM $cAlfa.fpar0131 ";
        if ($pArrayParametros['APLITARX'] == "CLIENTE") {
          if ($pArrayParametros['ESTCLIXX'] != "TODOS") {
            $qTarifas .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fpar0131.cliidxxx = $cAlfa.SIAI0150.CLIIDXXX ";
          }
        } else{
          if ($pArrayParametros['ESTCLIXX'] != "TODOS") {
            $qTarifas .= "LEFT JOIN $cAlfa.fpar0111 ON $cAlfa.fpar0131.cliidxxx = $cAlfa.fpar0111.gtaidxxx ";
          }
        }
        $qTarifas .= "WHERE ";
        // Estado Tarifas
        if ($pArrayParametros['ESTTARIX'] == "TODOS") {
          $qTarifas .= "$cAlfa.fpar0131.regestxx IN(\"ACTIVO\",\"INACTIVO\") AND ";
        } else if ($pArrayParametros['ESTTARIX'] != "") {
          $qTarifas .= "$cAlfa.fpar0131.regestxx = \"{$pArrayParametros['ESTTARIX']}\" AND ";
        }
        // Tipo Operacion
        if ($pArrayParametros['TIPOPEXX'] == "TODOS") {
          $qTarifas .= "$cAlfa.fpar0131.fcotopxx IN(\"IMPORTACION\",\"EXPORTACION\",\"TRANSITO\",\"OTROS\") AND ";
        } else if ($pArrayParametros['TIPOPEXX'] != "") {
          $qTarifas .= "$cAlfa.fpar0131.fcotopxx = \"{$pArrayParametros['TIPOPEXX']}\" AND ";
        }
        // Cliente o Grupo
        if ($pArrayParametros['CLIIDXXX'] != "") {
          $qTarifas .= "$cAlfa.fpar0131.cliidxxx = \"{$pArrayParametros['CLIIDXXX']}\" AND ";
        }
        //Estado Cliente o Grupo
        if ($pArrayParametros['APLITARX'] == "CLIENTE") {
          if ($pArrayParametros['ESTCLIXX'] != "TODOS") {
            $qTarifas .= "$cAlfa.SIAI0150.regestxx = \"{$pArrayParametros['ESTCLIXX']}\" AND ";
          }
        } else{
          if ($pArrayParametros['ESTCLIXX'] != "TODOS") {
            $qTarifas .= "$cAlfa.fpar0111.regestxx = \"{$pArrayParametros['ESTCLIXX']}\" AND ";
          }
        }
        if ($pArrayParametros['SERIDXXX'] != "") {
          $qTarifas .= "$cAlfa.fpar0131.seridxxx = \"{$pArrayParametros['SERIDXXX']}\" AND ";
        }
        if ($pArrayParametros['FCOIDXXX'] != "") {
          $qTarifas .= "$cAlfa.fpar0131.fcoidxxx = \"{$pArrayParametros['FCOIDXXX']}\" AND ";
        }
        if ($pArrayParametros['FECHASTA'] != "" && $pArrayParametros['FECDESDE'] != "") {
          // Rango de Fechas
          switch ($pArrayParametros['TIPOFECX']) {
            case "CREACION":
            case "ACTUALIZACION":
              $cCamFec = ($pArrayParametros['TIPOFECX'] == "CREACION") ? "regfcrex" : "regfmodx";
              $qTarifas .= "$cAlfa.fpar0131.$cCamFec BETWEEN \"{$pArrayParametros['FECDESDE']}\" AND \"{$pArrayParametros['FECHASTA']}\" AND ";
            break;
            default:
              // Vigencia
              $qTarifas .= "$cAlfa.fpar0131.tarfevde >= \"{$pArrayParametros['FECDESDE']}\" AND ";
              $qTarifas .= "$cAlfa.fpar0131.tarfevha <= \"{$pArrayParametros['FECHASTA']}\" AND ";
            break;
          }
        }
        $qTarifas  = substr($qTarifas, 0, -4);
        $qTarifas .= "ORDER BY $cAlfa.fpar0131.cliidxxx,$cAlfa.fpar0131.seridxxx,$cAlfa.fpar0131.fcoidxxx ";
        $qTarifas .= "LIMIT $i,$nNumReg; ";
        $xTarifas  = f_MySql("SELECT","",$qTarifas,$xConexion01,"");
        // echo $qTarifas . "~" . mysql_num_rows($xTarifas)."<br><br>";
        if (mysql_num_rows($xTarifas) == 0) {
          $nSwitch = 1;
          $vError['TIPOERRX'] = "ERROR";
          $vError['DESERROR'] = "No Se Encontraron Registros.";
          $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
        }
  
        if ($nSwitch == 0) {
          $nCanReg = 0;
          while ($xRT = mysql_fetch_assoc($xTarifas)) {
  
            $nCanReg++;
            if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = $objEstructuraTarifasFacturacion->fnReiniciarConexionDBRTarifasFacturacion($xConexion01); }
  
            // Trayendo nombre de cliente o grupo
            $cNomCliGru = "";
            $cEstCliGru = "";
            if ($xRT['tartipxx'] == "CLIENTE") {
              if (in_array($xRT['cliidxxx'],$vClientes) == false) {
                $qDatCli  = "SELECT ";
                $qDatCli .= "SIAI0150.CLIIDXXX, ";
                $qDatCli .= "IF(SIAI0150.CLINOMXX != \"\",SIAI0150.CLINOMXX,CONCAT(SIAI0150.CLINOM1X,\" \",SIAI0150.CLINOM2X,\" \",SIAI0150.CLIAPE1X,\" \",SIAI0150.CLIAPE2X)) AS CLINOMXX, ";
                $qDatCli .= "SIAI0150.REGESTXX ";
                $qDatCli .= "FROM $cAlfa.SIAI0150 ";
                $qDatCli .= "WHERE ";
                $qDatCli .= "SIAI0150.CLIIDXXX = \"{$xRT['cliidxxx']}\" LIMIT 0,1";
                $xDatCli  = f_MySql("SELECT","",$qDatCli,$xConexion01,"");
                $vDatCli  = mysql_fetch_array($xDatCli);
                $cNomCliGru = $vDatCli['CLINOMXX'];
                $cEstCliGru = $vDatCli['REGESTXX'];
                $vClientes[] = "{$xRT['cliidxxx']}";
                $mClientes["{$xRT['cliidxxx']}"] = $vDatCli;
              } else {
                $cNomCliGru = $mClientes["{$xRT['cliidxxx']}"]['CLINOMXX'];
                $cEstCliGru = $mClientes["{$xRT['cliidxxx']}"]['REGESTXX'];
              }
            } else {
              if (in_array($xRT['cliidxxx'],$vGrupos) == false) {
                $qDatGru  = "SELECT gtaidxxx, ";
                $qDatGru .= "IF(gtadesxx != \"\",gtadesxx,\"GRUPO TARIFA SIN DESCRIPCION\") AS gtadesxx, ";
                $qDatGru .= "regestxx ";
                $qDatGru .= "FROM $cAlfa.fpar0111 ";
                $qDatGru .= "WHERE ";
                $qDatGru .= "gtaidxxx = \"{$xRT['cliidxxx']}\" LIMIT 0,1";
                $xDatGru  = f_MySql("SELECT","",$qDatGru,$xConexion01,"");
                $vDatGru  = mysql_fetch_array($xDatGru);
                $cNomCliGru = $vDatGru['gtadesxx'];
                $cEstCliGru = $vDatGru['regestxx'];
                $vGrupos[] = "{$xRT['cliidxxx']}";
                $mGrupos["{$xRT['cliidxxx']}"] = $vGrupos;
              } else {
                $cNomCliGru = $mGrupos["{$xRT['cliidxxx']}"]['gtadesxx'];
                $cEstCliGru = $mGrupos["{$xRT['cliidxxx']}"]['regestxx'];
              }
            }
  
            // Buscando Informacion Servicios
            if (in_array($xRT['seridxxx'],$vServicios) == false) {
              $qDatSer  = "SELECT seridxxx, ";
              $qDatSer .= "IF(serdesxx != \"\",serdesxx,\"CONCEPTO SIN DESCRIPCION\") AS serdesxx, ";
              $qDatSer .= "IF(serdespx != \"\",serdespx,serdesxx) AS serdespx, ";
              $qDatSer .= "sercones ";
              $qDatSer .= "FROM $cAlfa.fpar0129 ";
              $qDatSer .= "WHERE ";
              $qDatSer .= "seridxxx = \"{$xRT['seridxxx']}\" LIMIT 0,1";
              $xDatSer  = f_MySql("SELECT","",$qDatSer,$xConexion01,"");
              $vDatSer  = mysql_fetch_array($xDatSer);
              $vServicios[] = "{$xRT['seridxxx']}";
              $mServicios["{$xRT['seridxxx']}"] = $vDatSer;
            }
  
            // Buscando Informacion Formas de Cobro
            if (in_array($xRT['fcoidxxx'],$vForCob) == false) {
              $qDatFor  = "SELECT fcoidxxx, ";
              $qDatFor .= "IF(fcodesxx != \"\",fcodesxx,\"FORMA SIN DESCRIPCION\") AS fcodesxx ";
              $qDatFor .= "FROM $cAlfa.fpar0130 ";
              $qDatFor .= "WHERE ";
              $qDatFor .= "fcoidxxx = \"{$xRT['fcoidxxx']}\" LIMIT 0,1";
              $xDatFor  = f_MySql("SELECT","",$qDatFor,$xConexion01,"");
              $vDatFor  = mysql_fetch_array($xDatFor);
              $vForCob[] = "{$xRT['fcoidxxx']}";
              $mForCob["{$xRT['fcoidxxx']}"] = $vDatFor;
            }
  
            // Buscando condiciones personalizadas
            $mData      = explode('|', $mServicios["{$xRT['seridxxx']}"]['sercones']);
            $mCamConEsp = array();
            for ($nC=1; $nC < count($mData); $nC++) {
              if ($mData[$nC] != '') {
                $vCondicion = explode('~', $mData[$nC]);
                if ($vCondicion[0] == $xRT['fcoidxxx']) {
                  for ($nA=1; $nA < count($vCondicion) ; $nA++) {
                    if (in_array("{$xRT['seridxxx']}~{$xRT['fcoidxxx']}~{$vCondicion[$nA]}",$vConEsp) == false) {
                      $vConEsp[] = "{$xRT['seridxxx']}~{$xRT['fcoidxxx']}~{$vCondicion[$nA]}";
  
                      $qCampos  = "SELECT dcedesxx ";
                      $qCampos .= "FROM $cAlfa.fpar0145 ";
                      $qCampos .= "WHERE ";
                      $qCampos .= "seridxxx = \"{$xRT['seridxxx']}\" AND ";
                      $qCampos .= "fcoidxxx = \"{$xRT['fcoidxxx']}\" AND ";
                      $qCampos .= "dcecampo = \"{$vCondicion[$nA]}\" LIMIT 0,1 ";
                      $xCampos  = f_MySql("SELECT","",$qCampos,$xConexion01,"");
                      $vCampos = mysql_fetch_array($xCampos);
                      $mConEsp["{$xRT['seridxxx']}~{$xRT['fcoidxxx']}~{$vCondicion[$nA]}"] = $vCampos;
                    }
                    $mCamConEsp["{$vCondicion[$nA]}"]['dcedesxx'] = $mConEsp["{$xRT['seridxxx']}~{$xRT['fcoidxxx']}~{$vCondicion[$nA]}"]['dcedesxx']; // Descripcion Estandar
                    $mCamConEsp["{$vCondicion[$nA]}"]['dcedespx'] = ""; // Descripcion Personalizada
                  }
                }
              }
            }
  
            if (count($mCamConEsp) > 0) {
              $mConEspPer = explode('|', $xRT['fcopcexx']);
              for ($nC=1; $nC < count($mConEspPer); $nC++) {
                if ($mConEspPer[$nC] != '') {
                  $vCondicion = explode('~', $mConEspPer[$nC]);
                  $mCamConEsp["{$vCondicion[0]}"]['dcedespx'] = $vCondicion[1]; // Descripcion Personalizada
                }
              }
            }
  
            $cCampEst = "";
            $cCampPer = "";
            foreach ($mCamConEsp as $cKey => $cValue) {
              $cCampEst .= str_replace(","," ",$mCamConEsp[$cKey]['dcedesxx']).",";
              $cCampPer .= str_replace(","," ",$mCamConEsp[$cKey]['dcedespx']).",";
            }
            $cCampEst = substr($cCampEst, 0, -1);
            $cCampPer = substr($cCampPer, 0, -1);
  
            // Busco la Descripcion de la Tarifa Por y Dependiendo de la Base de Datos
            if ($xRT['fcotptxx'] == "GENERAL") {
              $vIdPro['prydesxx'] = "";
            } else {
              if (substr_count($vSysStr['alpopular_db_aplica'],$cAlfa) > 0){
                if($xRT['fcotptxx'] == "PROYECTO"){
                  $qIdPro  = "SELECT $cAlfa.siai1101.* ";
                  $qIdPro .= "FROM $cAlfa.siai1101 ";
                  $qIdPro .= "WHERE ";
                  $qIdPro .= "$cAlfa.siai1101.pryidxxx = \"{$xRT['fcotpixx']}\" AND ";
                  $qIdPro .= "$cAlfa.siai1101.regestxx = \"ACTIVO\" LIMIT 0,1";
                  $xIdPro  = f_MySql("SELECT","",$qIdPro,$xConexion01,"");
                }elseif($xRT['fcotptxx'] == "PRODUCTO"){
                  $qIdPro  = "SELECT $cAlfa.zalpo003.*, ";
                  $qIdPro  = "SELECT $cAlfa.zalpo003.lprdesxx as prydesxx ";
                  $qIdPro .= "FROM $cAlfa.zalpo003 ";
                  $qIdPro .= "WHERE ";
                  $qIdPro .= "$cAlfa.zalpo003.lpridxxx = \"{$xRT['fcotpixx']}\" AND ";
                  $qIdPro .= "$cAlfa.zalpo003.regestxx = \"ACTIVO\" LIMIT 0,1";
                  $xIdPro  = f_MySql("SELECT","",$qIdPro,$xConexion01,"");
                }
              }else{
                if($xRT['fcotptxx'] == "PROYECTO"){
                  $qIdPro  = "SELECT $cAlfa.fpar0142.prydesxx ";
                  $qIdPro .= "FROM $cAlfa.fpar0142 ";
                  $qIdPro .= "WHERE ";
                  $qIdPro .= "$cAlfa.fpar0142.pryidxxx = \"{$xRT['fcotpixx']}\" AND ";
                  $qIdPro .= "$cAlfa.fpar0142.cliidxxx = \"{$xRT['cliidxxx']}\" AND ";
                  $qIdPro .= "$cAlfa.fpar0142.regestxx = \"ACTIVO\" LIMIT 0,1";
                  $xIdPro  = f_MySql("SELECT","",$qIdPro,$xConexion01,"");
                }elseif($xRT['fcotptxx'] == "PRODUCTO"){
                  $qIdPro  = "SELECT $cAlfa.fpar0143.prodesxx as prydesxx ";
                  $qIdPro .= "FROM $cAlfa.fpar0143 ";
                  $qIdPro .= "WHERE ";
                  $qIdPro .= "$cAlfa.fpar0143.proidxxx = \"{$xRT['fcotpixx']}\" AND ";
                  $qIdPro .= "$cAlfa.fpar0143.regestxx = \"ACTIVO\" LIMIT 0,1";
                  $xIdPro  = f_MySql("SELECT","",$qIdPro,$xConexion01,"");
                }
              }
              if (mysql_num_rows($xIdPro) > 0){$vIdPro = mysql_fetch_array($xIdPro);}else{$vIdPro['prydesxx'] = "SIN DESCRIPCION";}
            }
  
            // Trayendo Informacion de Usuarios Creacion
            if ($xRT['regusrcr'] != "" && in_array($xRT['regusrcr'],$vUsuarios) == false) {
              $qDatUsr  = "SELECT USRIDXXX, USRNOMXX ";
              $qDatUsr .= "FROM $cAlfa.SIAI0003 ";
              $qDatUsr .= "WHERE ";
              $qDatUsr .= "USRIDXXX = \"{$xRT['regusrcr']}\" LIMIT 0,1";
              $xDatUsr  = f_MySql("SELECT","",$qDatUsr,$xConexion01,"");
              $vDatUsr  = mysql_fetch_array($xDatUsr);
              $vUsuarios[] = "{$xRT['regusrcr']}";
              $mUsuarios["{$xRT['regusrcr']}"] = $vDatUsr;
            }
  
            // Trayendo Informacion de Usuarios Modificacion
            if (in_array($xRT['regusrxx'],$vUsuarios) == false) {
              $qDatUsr  = "SELECT USRIDXXX, USRNOMXX ";
              $qDatUsr .= "FROM $cAlfa.SIAI0003 ";
              $qDatUsr .= "WHERE ";
              $qDatUsr .= "USRIDXXX = \"{$xRT['regusrxx']}\" LIMIT 0,1";
              $xDatUsr  = f_MySql("SELECT","",$qDatUsr,$xConexion01,"");
              $vDatUsr  = mysql_fetch_array($xDatUsr);
              $vUsuarios[] = "{$xRT['regusrxx']}";
              $mUsuarios["{$xRT['regusrxx']}"] = $vDatUsr;
            }
  
            // Logica para determintar los campos dinamicos de las tarifas
            // se debe tener en cuenta que los indices de los campos no pueden contener caracteres especiales, solo el guion bajo es permitido
            $cCamResal = "";
            $cCamResal = $this->fnCamposFormaCobro($xRT['fcoidxxx'], $xRT['fcotarxx'], $vCamDin, $vCamDinNC20, $vCamDinNC40, $vCamDinVeh, $vCamDinN);
            
            $qInsert  = $qInsCab;
            $qInsert .= "(\"{$xRT['cliidxxx']}\",";                             // Id de Cliente o Id Grupo
            $qInsert .= "\"$cNomCliGru\",";                                     // Nombre del Cliente o Descripcion Grupo',";
            $qInsert .= "\"{$xRT['tartipxx']}\",";                              // Aplica tarifa para (CLIENTE/GRUPO)',";
            $qInsert .= "\"$cEstCliGru\",";                                     // Estado Cliente o Grupo',";
            $qInsert .= "\"{$xRT['regestxx']}\",";                              // Estado Tarifa',";
            if ($vSysStr['system_control_vigencia_tarifas'] == "SI") {
              $qInsert .= "\"{$xRT['tarfevde']}\",";                            // Fecha de Vigencia Desde',";
              $qInsert .= "\"{$xRT['tarfevha']}\",";                            // Fecha de Vigencia Hasta',";
              $qInsert .= "\"\",";                                              // Nueva Fecha de Vigencia Desde',";
              $qInsert .= "\"\",";                                              // Nueva Fecha de Vigencia Hasta',";
              $qInsert .= "\"{$xRT['tarfcvig']}\",";                            // Fecha Cambio de Vigencia',";
              $qInsert .= "\"\",";                                              // Modificar',";
            }
            $qInsert .= "\"{$xRT['seridxxx']}\",";                              // Id del Concepto de Cobro',";
            $qInsert .= "\"{$mServicios["{$xRT['seridxxx']}"]['serdespx']}\","; // Descripcion del Concepto de Cobro',";
            $qInsert .= "\"{$xRT['serdespc']}\",";                              // Descripcion Personalizada del Concepto de Cobro',";
            $qInsert .= "\"{$mForCob["{$xRT['fcoidxxx']}"]['fcoidxxx']}\",";    // Id Forma de Cobro',";
            $qInsert .= "\"{$mForCob["{$xRT['fcoidxxx']}"]['fcodesxx']}\",";    // Descripcion Forma de Cobro',";
            $qInsert .= "\"{$xRT['monidxxx']}\",";                              // Moneda',";
            $qInsert .= "\"$cCampEst\",";                                       // Condicion Especial',";
            $qInsert .= "\"$cCampPer\",";                                       // Condicion Especial Personalizada',";
            $qInsert .= "\"{$xRT['fcotptxx']}\",";                              // Aplica Tarifa Por',";
            $qInsert .= "\"{$xRT['fcotpixx']}\",";                              // Id Aplica Tarifa Por',";
            $qInsert .= "\"{$vIdPro['prydesxx']}\",";                           // Descripcion Aplica Tarifa Por',";
            $qInsert .= "\"{$xRT['fcotopxx']}\",";                              // Tipo Operación',";
            $qInsert .= "\"".str_replace("~",",",$xRT['sucidxxx'])."\",";       // Sucursales',";
            $qInsert .= "\"".str_replace("~",",",$xRT['fcomtrxx'])."\",";       // Tipo de Transporte',";
            //Entre estos campos el reporte genera los campos dinamicos de las tarifas
            $qInsert .= "\"{$xRT['regusrcr']}\",";                              // Id Usuario Creacion',";
            $qInsert .= "\"{$mUsuarios["{$xRT['regusrcr']}"]['USRNOMXX']}\",";  // Nombre Usuario Creacion',";
            $qInsert .= "\"{$xRT['regusrxx']}\",";                              // Id Usuario Modificado',";
            $qInsert .= "\"{$mUsuarios["{$xRT['regusrxx']}"]['USRNOMXX']}\",";  // Nombre Usuario Modificado',";
            $qInsert .= "\"{$xRT['regfcrex']}\",";                              // Fecha creacion',";
            $qInsert .= "\"{$xRT['regfmodx']}\",";                              // Fecha Modificado',";
            $qInsert .= "\"".substr($cCamResal,0,-1)."\")";                     // Columnas Resaltadas',";
            // echo $qInsert."<br>";
            $xInsert = mysql_query($qInsert,$xConexion01);
            if (!$xInsert) {
              $nSwitch = 1;
              $vError['TIPOERRX'] = "ERROR";
              $vError['DESERROR'] = "Error al Insertar en la tabla temporal.";
              $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
            }
          }
        }
      }

      if ($nSwitch == 0) {
        // Ordenando campos para crearlos en la tabla temporal
        $vCamNew = array();
        $vCamNew =  $this->fnOrdenarCampos($vCamDin, $vCamDinNC20, $vCamDinNC40, $vCamDinVeh, $vCamDinN);

        for ($n=0; $n<count($vCamNew); $n++) {
          $vCampos['tablaxxx'] = $pArrayParametros['TABLAXXX'];
          $vCampos['camponew'] = $vCamNew[$n];
          $vCampos['camporef'] = ($n == 0) ? "MODO_TRANSPORTE" : $vCamNew[$n-1];
          $vCampos['camtipxx'] = "TEXT";
          $mRetCamDim = $objEstructuraTarifasFacturacion->fnCrearCampo($vCampos);
          if ($mRetCamDim[0] == "false") {
            $nSwitch = 1;
            for($nR=1;$nR<count($mRetCamDim);$nR++) {
              $vError['TIPOERRX'] = "ERROR";
              $vError['DESERROR'] = $mRetCamDim[$nR];
              $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
            }
          }
        }

        if ($nSwitch == 0) {
          $qDatos  = "SELECT LINEAIDX,COLUMNAS_RESALTADAS "; 
          $qDatos .= "FROM $cAlfa.{$pArrayParametros['TABLAXXX']}";
          $xDatos  = f_MySql("SELECT","",$qDatos,$xConexion01,"");
          $nCanReg=0;
          while ($xRT = mysql_fetch_assoc($xDatos)) {
            $nCanReg++;
            if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = $objEstructuraTarifasFacturacion->fnReiniciarConexionDBRTarifasFacturacion($xConexion01); }

            $vCampos = explode("|", $xRT['COLUMNAS_RESALTADAS']);
            $cCamUpd = "";
            for($n=0; $n<count($vCampos); $n++) {
              if ($vCampos[$n] != "") {
                $vCam = explode("~", $vCampos[$n]);
                $cCamUpd .= "{$vCam[0]} = \"{$vCam[1]}\", ";
              }
            }
            $cCamUpd = substr($cCamUpd,0,-2);

            // Las tarifas con valor variable no tienen parametrizacion
            if ($cCamUpd != "") {
              $qUpdate = "UPDATE $cAlfa.{$pArrayParametros['TABLAXXX']} SET $cCamUpd WHERE LINEAIDX = {$xRT['LINEAIDX']}; ";
              $xUpdate = mysql_query($qUpdate,$xConexion01);
              if (!$xUpdate) {
                $nSwitch = 1;
                $vError['TIPOERRX'] = "ERROR";
                $vError['DESERROR'] = "Error al Actualizar los datos de las tarifas.";
                $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
              }
            }
          }
        }

        if ($nSwitch == 0) {
          $vParametros = array();
          $vParametros['TABLAXXX'] = $pArrayParametros['TABLAXXX'];
          $vParametros['TABLAERR'] = $pArrayParametros['TABLAERR'];
          $vParametros['ORIGENXX'] = $pArrayParametros['ORIGENXX'];
          $mRespuesta = $this->fnGenerarReporteTarifasConsolidad($vParametros);

          // Retorna el nombre del archivo o guarda los errores generados
          if ($mRespuesta[0] == "true") {
            $mReturn[1] = $mRespuesta[1];
          } else {
            $nSwitch = 1;
            $vError['LINEAERR'] = __LINE__;
            $vError['TIPOERRX'] = "ERROR";
            $vError['DESERROR'] = "Error al generar los archivos";
            $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
          }
        }
      }
      
      if ($nSwitch == 0) {
        $mReturn[0] = "true";
      } else {
        $mReturn[0] = "false";
      }

      return $mReturn;
    } ## function fnReporteTarifasConsolidado($pArrayParametros) { ##

    /**
     * Metodo que retorna los campos dinamicos de una forma de cobro
     */
    function fnCamposFormaCobro($xFcoId, $xFcoTar, &$vCamDin, &$vCamDinNC20, &$vCamDinNC40, &$vCamDinVeh, &$vCamDinN) {
      
      global $cAlfa;

      /**
       * Recibe como Parametro dos posiciones:
       * $xFcoId  // Id de la forma de cobro
       * $xFcoTar // Valores Tarifa Parametrizada
       * Los array con los campos dinamicos sueltos, con niveles, con niveles de vehiculos, con niveles de C20 y C40 son por referencia
       * Estos array deben definirse en el metodo que llama a esta funcion
       * 
       * Retorna los campos a resaltar para la forma de cobro
       * $cCamResal
      */

      // Inicializando array con los campos retornados
      $vValCam = array();

      $cCadena = explode("|",$xFcoTar);
      switch ($xFcoId) {
        case "100": // Valor Fijo
        case "200": // Valor Fijo
        case "300": // Valor Fijo
        case "400": // Valor Fijo
        case "500": // Valor Fijo
        case "127": // INTERVALOS X MTRS3
        case "174": // POR METRO CUADRADO POR DIAS EN BODEGA
        case "1122": // VALOR FIJO X HOJA PRINCIPAL
        case "1143": // VALOR FIJO EN USD
        case "1144": // VALOR FIJO POR ÍTEM EN USD
        case "250": // POR METRO CUADRADO POR DIAS EN BODEGA
        case "251": // INTERVALOS X MTRS3
        case "146": // Valor Fijo
          if ($xFcoId == '1143' || $xFcoId == '1144') {
            $vValCam["VALOR_FIJO_EN_USD"] = $cCadena[1];
          } else {
            $vValCam["VALOR_FIJO"] = $cCadena[1];
          }
        break;

        case "101": // Porcentaje CIF o Minima
        case "126": // Porcentaje CIF o Minima
          $cCad101 = explode("~",$cCadena[1]);
          $cCamMin = ($xFcoId == '101' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MINIMA_GENERAL_CIF" : "MINIMA";
          $vValCam["PORCENTAJE_CIF"] = $cCad101[0];
          $vValCam[$cCamMin]         = $cCad101[1];
        break;

        case "102": // Valor Fijo x Unidades
        case "201": // Valor Fijo x Unidades
        case "301": // Valor Fijo x Unidades
        case "502": // Valor Fijo x Unidades
        case "150": // Valor Fijo x Unidades
        case "152": // Valor Fijo x Unidades
        case "163": // Valor Fijo x Unidades
        case "240": // Valor Fijo x Unidades
        case "401": // Valor Fijo x Unidades
          $vValCam["VALOR_FIJO_POR_UNIDAD"] = $cCadena[1];
        break;

        case "103": // Porcentaje CIF o Minima
        case "202": // Porcentaje CIF o Minima
        case "307":
          $cCad103 = explode("~",$cCadena[1]);
          $cCamVal = ($xFcoId == '103' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "VALOR_POR_HORA" : "VALOR";
          $vValCam[$cCamVal]          = $cCad103[0];
          $vValCam["HORAS"]           = $cCad103[1];
          $vValCam["VALOR_ADICIONAL"] = $cCad103[2];
        break;

        case "104": // Cobro variables segun Unidades
        case "204": // Cobro variables segun Unidades
        case "303": // Cobro variables segun Unidades
        case "313": // Cobro variables segun Unidades
        case "504": // Cobro variables segun Unidades
          $cCad104 = explode("~",$cCadena[1]);
          $vValCam["TARIFA_INICIAL"]               = $cCad104[0];
          $vValCam["UNIDADES_INICIALES"]           = $cCad104[1];
          $vValCam["TRARIFA_DESPUES_DE_INICIALES"] = $cCad104[2];
        break;

        case "105": // Valor Cif Dividido en Pesos
          $cCad105 = explode("~",$cCadena[1]);
          $vValCam["VALOR_PARCIAL"]                           = $cCad105[0];
          $vValCam["VALOR_FIJO_DE_COBRO_POR_PRIMER_PARCIAL"]  = $cCad105[1];
          $vValCam["PORCENTAJE_DE_COBRO_VALOR_CIF_ADICIONAL"] = $cCad105[2];
        break;

        case "106": // Porcentaje Valor Cif o Minima Variable por Cantidad de Declaraciones de Importacion
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[1]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }

          $vValCam["PORCENTAJE_CIF"] = $cCadena2[0];
          $vValCam["NIVELES"]        = $zNum;
          
          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++) {
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam["NIVELES_".$nNivel."_DECLARACIONES"]    = $zInterno[0];
                $vValCam["NIVELES_".$nNivel."_VALOR_POR_UNIDAD"] = $zInterno[1];
              }
            }
          } else {
            $vValCam["NIVELES_1_DECLARACIONES"]    = "";
            $vValCam["NIVELES_1_VALOR_POR_UNIDAD"] = "";
          }            
        break;

        case "159": //Cobro Escalonado sobre la cantidad de Toneladas por un porcentaje del valor CIF con una minima
        case "107": // Porcentaje Valor Cif o Minima Variable por Cantidad de Declaraciones de Importacion
        case "1138": // Cobro Escalonado Sobre Valor CIF con Minima
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[1]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }
          $cCamMin = ($xFcoId == '107' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MINIMA_GENERAL_CIF" : "MINIMA";
          $cCamNiv = ($xFcoId == '107' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "NIVELES_CANT_VEHICULOS" : "NIVELES";
          $vValCam[$cCamMin] = $cCadena2[0];
          $vValCam[$cCamNiv] = $zNum;

          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam[$cCamNiv."_".$nNivel."_LIMITE_INFERIOR"] = $zInterno[0];
                $vValCam[$cCamNiv."_".$nNivel."_LIMITE_SUPERIOR"] = $zInterno[1];
                $vValCam[$cCamNiv."_".$nNivel."_PORCENTAJE"]      = $zInterno[2];
              }
            }
          } else {
            $vValCam[$cCamNiv."_1_LIMITE_INFERIOR"] = "";
            $vValCam[$cCamNiv."_1_LIMITE_SUPERIOR"] = "";
            $vValCam[$cCamNiv."_1_PORCENTAJE"]      = "";
          }
        break;

        case "108": // COBRO ESCALONADO SOBRE VALOR CIF EN COP
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[1]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }
          $cCamMin = ($xFcoId == '108' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MINIMA_GENERAL_CIF" : "MINIMA";
          $cCamNiv = ($xFcoId == '108' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "NIVELES_CANT_VEHICULOS" : "NIVELES";
          $vValCam[$cCamMin] = $cCadena2[0];
          $vValCam[$cCamNiv] = $zNum;

          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam[$cCamNiv."_".$nNivel."_LIMITE_INFERIOR"] = $zInterno[0];
                $vValCam[$cCamNiv."_".$nNivel."_LIMITE_SUPERIOR"] = $zInterno[1];
                $vValCam[$cCamNiv."_".$nNivel."_PORCENTAJE"]      = $zInterno[2];
              }
            }
          } else {
            $vValCam[$cCamNiv."_1_LIMITE_INFERIOR"] = "";
            $vValCam[$cCamNiv."_1_LIMITE_SUPERIOR"] = "";
            $vValCam[$cCamNiv."_1_PORCENTAJE"]      = "";
          }
        break;

        case "109": // Cobro por Unidad de Carga por Contenedores
        case "207": // Cobro por Unidad de Carga por Contenedores
        case "305": // Cobro por Unidad de Carga por Contenedores
        case "134": //COBRO POR UNIDAD DE CARGA (UNIDADES CARGA SUELTA O CONTENEDORES) O MINIMA %CIF
        case "234": //COBRO POR UNIDAD DE CARGA (UNIDADES CARGA SUELTA O CONTENEDORES) O MINIMA %FOB
          $cCad109 = explode("~",$cCadena[1]);
          if (substr($xFcoId, 0, 2) == "2") {
            $vValCam["PORCENTAJE_FOB_PARA_GRANEL"] = $cCad109[0];
          } else {
            $vValCam["PORCENTAJE_CIF_PARA_GRANEL"] = $cCad109[0];
          }            
          $vValCam["CONTENEDOR_20"]   = $cCad109[1];
          $vValCam["CONTENEDOR_40"]   = $cCad109[2];
          $vValCam["CARGA_SUELTA"]    = $cCad109[3];
        break;

        case "110": // Cobro Escalonado por Cantidad de Contenedores $COP Importancion
        case "208": // Cobro Escalonado por Cantidad de Contenedores $COP Exportacion
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[1]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }

          $vValCam["CARGA_SUELTA"] = $cCadena2[0];
          $vValCam["NIVELES"]      = $zNum;
          
          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam["NIVELES_".$nNivel."_NIVEL_INFERIOR"] = $zInterno[0];
                $vValCam["NIVELES_".$nNivel."_NIVEL_SUPERIOR"] = $zInterno[1];
                $vValCam["NIVELES_".$nNivel."_VALOR_COP"]      = $zInterno[2];
              }
            }
          } else {
            $vValCam["NIVELES_1_NIVEL_INFERIOR"] = "";
            $vValCam["NIVELES_1_NIVEL_SUPERIOR"] = "";
            $vValCam["NIVELES_1_VALOR_COP"]      = "";
          }
        break;

        case "111": // Porcentaje CIF o Minima
          $cCad111 = explode("~",$cCadena[1]);
          $vValCam["CARGA_SUELTA"]              = $cCad111[0];
          $vValCam["PIEZAS"]                    = $cCad111[1];
          $vValCam["VALOR_ADICIONAL_POR_PIEZA"] = $cCad111[2];
        break;

        case "112": // Cobro por Unidad de Carga por Contenedores
        case "1117": // Cobro por Unidad de Carga + Apoyo Archivo
          $cCad112 = explode("~",$cCadena[1]);
          $vValCam["CONTENEDOR_20"]   = $cCad112[0];
          $vValCam["CONTENEDOR_40"]   = $cCad112[1];
          $vValCam["CARGA_SUELTA"]    = $cCad112[2];
        break;

        case "113": // Valor por Incremento de Tarifa Minima.
          $cCad113 = explode("~",$cCadena[1]);
          $vValCam["PORCENTAJE_CIF"]      = $cCad113[0];
          $vValCam["MININA_NORMAL"]       = $cCad113[1];
          $vValCam["MINIMA_INCREMENTADA"] = $cCad113[2];
        break;

        case "114": // Valor Fijo o una Minima
          $cCad114 = explode("~",$cCadena[1]);
          $cCamMin = ($xFcoId == '114' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MINIMA_GENERAL_CIF" : "MINIMA";
          $cCamVal = ($xFcoId == '114' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "VALOR_POR_UNIDAD_TONELADAS" : "VALOR_POR_UNIDAD";
          $vValCam[$cCamVal] = $cCad114[0];
          $vValCam[$cCamMin] = $cCad114[1];
        break;

        case "115": // Valor por Cantidad y Tipo de Contenedor
          $cCad115 = explode("~",$cCadena[1]);
          $vValCam["CONTENEDOR_20"]    = $cCad115[0];
          $vValCam["CONTENEDOR_40"]    = $cCad115[1];
          $vValCam["CONTENEDOR_40_HC"] = $cCad115[2];
        break;

        case "147":
        case "257":
        case "258":
        case "188":
        case "116":  // Cobro Escalonado por Unidad 
        case "1139": // Cobro Escalonado por Unidad 
        case "1140": // Cobro Escalonado por Unidad 
        case "1141": // Cobro Escalonado por Unidad 
        case "1145": // Cobro Escalonado Valor por Unidad 
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[1]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }
          $cCamNiv = (($xFcoId == '116' || $xFcoId == '188' || $xFcoId == '257' || $xFcoId == '258') && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "NIVELES_CANT_VEHICULOS" : "NIVELES";
          $vValCam[$cCamNiv] = $zNum;

          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam[$cCamNiv."_".$nNivel."_LIMITE_INFERIOR"]  = $zInterno[0];
                $vValCam[$cCamNiv."_".$nNivel."_LIMITE_SUPERIOR"]  = $zInterno[1];
                $vValCam[$cCamNiv."_".$nNivel."_VALOR_POR_UNIDAD"] = $zInterno[2];
              }
            }
          } else {
            $vValCam[$cCamNiv."_1_LIMITE_INFERIOR"]  = "";
            $vValCam[$cCamNiv."_1_LIMITE_SUPERIOR"]  = "";
            $vValCam[$cCamNiv."_1_VALOR_POR_UNIDAD"] = "";
          }
        break;

        case "117": // Cobro Escalonado por Unidad Consolidado
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[1]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }
          $vValCam["NIVELES"] = $zNum;

          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam["NIVELES_".$nNivel."_LIMITE_INFERIOR"]  = $zInterno[0];
                $vValCam["NIVELES_".$nNivel."_LIMITE_SUPERIOR"]  = $zInterno[1];
                $vValCam["NIVELES_".$nNivel."_VALOR_POR_UNIDAD"] = $zInterno[2];
              }
            }
          } else {
            $vValCam["NIVELES_1_LIMITE_INFERIOR"]  = "";
            $vValCam["NIVELES_1_LIMITE_SUPERIOR"]  = "";
            $vValCam["NIVELES_1_VALOR_POR_UNIDAD"] = "";
          }
        break;

        case "118": // Porcentaje CIF con Minima y Maxima
        case "211": // Porcentaje CIF con Minima y Maxima
        case "312": // Cobro por Porcentaje FOB con Minima o Maxima
          $cCad118 = explode("~",$cCadena[1]);
          switch ($xFcoId) {
            case '118':
              $cCamMin = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MINIMA_GENERAL_CIF" : "MINIMA";
              $cCamMax = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MAXIMA_GENERAL_CIF" : "MAXIMA";
            break;
            case '211':
              $cCamMin = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MINIMA_FOB" : "MINIMA";
              $cCamMax = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MAXIMA_FOB" : "MAXIMA";
            break;
            default:
              $cCamMin = "MINIMA";
              $cCamMax = "MAXIMA";
            break;
          }
          if ($xFcoId == "211") {
            $vValCam["PORCENTAJE_FOB"] = $cCad118[0];
          } else {
            $vValCam["PORCENTAJE_CIF"] = $cCad118[0];
          }
          $vValCam[$cCamMin] = $cCad118[1];
          $vValCam[$cCamMax] = $cCad118[2];
        break;

        case "187": // VALOR FIJO ESCALONADO CON CIF EN USD
        case "309": // COBRO ESCALONADO CON VALOR FIJO
        case "241": // COBRO ESCALONADO CON VALOR FIJO (ULTIMO NIVEL POR UNIDAD)
        case "246": // COBRO ESCALONADO CON VALOR FIJO
        case "164": // COBRO ESCALONADO CON VALOR FIJO (ULTIMO NIVEL POR UNIDAD)
        case "131":
        case "130": // COBRO ESCALONADO VALOR FIJO UNIDADES DIM
        case "119": // Cobro Escalonado por Unidad Consolidado
        case "189": // COBRO ESCALONADO VEHICULO VALOR FIJO
        case "259": // COBRO ESCALONADO VEHICULO VALOR FIJO
        case "1124": // COBRO ESCALONADO POR PEDIDO Y CANTIDAD DE ITEMS VALOR FIJO
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[1]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }
          $cCamNiv = (($xFcoId == '119' || $xFcoId == '164' || $xFcoId == '187' || $xFcoId == '189' || $xFcoId == '246' || $xFcoId == '259') && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "NIVELES_CANT_VEHICULOS" : "NIVELES";
          $vValCam[$cCamNiv] = $zNum;

          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam[$cCamNiv."_".$nNivel."_LIMITE_INFERIOR"] = $zInterno[0];
                $vValCam[$cCamNiv."_".$nNivel."_LIMITE_SUPERIOR"] = $zInterno[1];
                $vValCam[$cCamNiv."_".$nNivel."_VALOR_FIJO"]      = $zInterno[2];
              }
            }
          } else {
            $vValCam[$cCamNiv."_1_LIMITE_INFERIOR"] = "";
            $vValCam[$cCamNiv."_1_LIMITE_SUPERIOR"] = "";
            $vValCam[$cCamNiv."_1_VALOR_FIJO"]      = "";
          }
        break;

        case "121": // % Cif
          $vValCam["PORCENTAJE_CIF"] = $cCadena[1];
        break;

        case "122": // Tarifa Vinculada Intercompa�ias
          $vTarifaVI = f_Explode_Array($xFcoTar,"|","~");
          //Plena
          $vValCam["PLENA_PORCENTAJE_CIF"]  = $vTarifaVI[0][0];
          $vValCam["PLENA_MINIMA"] = $vTarifaVI[0][1];
          //Vinculada Deposito
          $vValCam["VINCULADA_DEPOSITO_PORCENTAJE_CIF"]  = $vTarifaVI[1][0];
          $vValCam["VINCULADA_DEPOSITO_MINIMA"] = $vTarifaVI[1][1];
          //Vinculada Agente Carga
          $vValCam["VINCULADA_AGENTE_CARGA_PORCENTAJE_CIF"]  = $vTarifaVI[2][0];
          $vValCam["VINCULADA_AGENTE_CARGA_MINIMA"] = $vTarifaVI[2][1];
        break;

        case "123": // Cobro Variable por Hoja Adicional
          $cCad123 = explode("~",$cCadena[1]);
          $vValCam["TARIFA_HOJA_PRINCIPAL"] = $cCad123[0];
          $vValCam["TARIFA_HOJA_ADICIONAL"] = $cCad123[1];
        break;

        case "124": // Base con Minima por % Cif.
          $cCad124 = explode("~",$cCadena[1]);
          $vValCam["BASE"]           = $cCad124[0];
          $vValCam["MINIMA"]         = $cCad124[1];
          $vValCam["PORCENTAJE_CIF"] = $cCad124[2];
        break;

        case "125": //% con una Minima.
          $cCad125 = explode("~",$cCadena[1]);
          $vValCam["MINIMA"]     = $cCad125[0];
          $vValCam["PORCENTAJE"] = $cCad125[1];
        break;

        case "128": //% con una Minima.
          $cCad128 = explode("~",$cCadena[1]);
          $vValCam["VALOR_PARCIAL"]                       = $cCad128[0];
          $vValCam["VALOR_FIJO_COBRO_POR_PRIMER_PARCIAL"] = $cCad128[1];
          $vValCam["VALOR_POR_SERIAL_ADICIONAL"]          = $cCad128[2];
        break;
        
        case "132": // Cobro Escalonado por cantidad de proveedores
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[1]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }
          $vValCam["NIVELES"] = $zNum;

          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam["NIVELES_".$nNivel."_NIVEL_INFERIOR"] = $zInterno[0];
                $vValCam["NIVELES_".$nNivel."_NIVEL_SUPERIOR"] = $zInterno[1];
                $vValCam["NIVELES_".$nNivel."_VALOR_COP"]       = $zInterno[2];
              }
            }
          } else {
            $vValCam["NIVELES_1_NIVEL_INFERIOR"] = "";
            $vValCam["NIVELES_1_NIVEL_SUPERIOR"] = "";
            $vValCam["NIVELES_1_VALOR_COP"]       = "";
          }
        break;

        case "133": // Cobro Escalonado por Unidad Consolidado
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[1]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }
          $cCamNiv = ($xFcoId == '133' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "NIVELES_CANT_VEHICULOS" : "NIVELES";
          $vValCam[$cCamNiv] = $zNum;

          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam[$cCamNiv."_".$nNivel."_LIMITE_INFERIOR"]       = $zInterno[0];
                $vValCam[$cCamNiv."_".$nNivel."_LIMITE_SUPERIOR"]       = $zInterno[1];
                $vValCam[$cCamNiv."_".$nNivel."_VALOR_FIJO_POR_UNIDAD"] = $zInterno[2];
              }
            }
          } else {
            $vValCam[$cCamNiv."_1_LIMITE_INFERIOR"]       = "";
            $vValCam[$cCamNiv."_1_LIMITE_SUPERIOR"]       = "";
            $vValCam[$cCamNiv."_1_VALOR_FIJO_POR_UNIDAD"] = "";
          }
        break;

        case "138":
        case "275":
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[1]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }
          $cCamNiv = ($xFcoId == '275' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "NIVELES_CANT_VEHICULOS" : "NIVELES";
          $vValCam[$cCamNiv] = $zNum;
          
          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam[$cCamNiv."_".$nNivel."_LIMITE_INFERIOR"] = $zInterno[0];
                $vValCam[$cCamNiv."_".$nNivel."_LIMITE_SUPERIOR"] = $zInterno[1];
                $vValCam[$cCamNiv."_".$nNivel."_VALOR_FIJO"]      = $zInterno[2];
              }
            }
          } else {
            $vValCam[$cCamNiv."_1_LIMITE_INFERIOR"] = "";
            $vValCam[$cCamNiv."_1_LIMITE_SUPERIOR"] = "";
            $vValCam[$cCamNiv."_1_VALOR_FIJO"]      = "";
          }
        break;

        case "139": // Grupo Documental
          $cCad139 = explode("~",$cCadena[1]);
          $vValCam["CANTIDAD_DIM_POR_GRUPO"]              = $cCad139[0];
          $vValCam["CANTIDAD_DAV_POR_GRUPO"]              = $cCad139[1];
          $vValCam["VALOR_FIJO_POR_GRUPO"]                = $cCad139[2];
          $vValCam["CANTIDAD_DE_GRUPOS_QUE_NO_SE_COBRAN"] = $cCad139[3];
        break;
        
        case "137": // Porcentaje FOB o Minima
        case "173": // Porcentaje FOB por Periodo de Almacenamiento con Minima
        case "203": // Porcentaje FOB o Minima
        case "249": // Porcentaje FOB o Minima
        case "302": // Porcentaje FOB o Minima
          $cCad203 = explode("~",$cCadena[1]);
          $cCamMin = ($xFcoId == '203' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MINIMA_FOB" : "MINIMA";
          $vValCam["PORCENTAJE_FOB"] = $cCad203[0];
          $vValCam[$cCamMin]         = $cCad203[1];
        break;

        case "135": // Escalonada en USD Importacion
        case "205": // Escalonada en USD Exportacion
        case "304": // Escalonada en USD Transito
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[1]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }
          switch ($xFcoId) {
            case '135':
              $cCamMin = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MINIMA_GENERAL_CIF" : "MINIMA";
            break;
            case '205':
              $cCamMin = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MINIMA_FOB" : "MINIMA";
            break;
            default:
              $cCamMin = "MINIMA";
            break;
          }
          $cCamNiv = (($xFcoId == '135' || $xFcoId == '205') && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "NIVELES_CANT_VEHICULOS" : "NIVELES";
          $vValCam[$cCamMin] = $cCadena2[0];
          $vValCam[$cCamNiv] = $zNum;

          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam[$cCamNiv."_".$nNivel."_LIMITE_INFERIOR"] = $zInterno[0];
                $vValCam[$cCamNiv."_".$nNivel."_LIMITE_SUPERIOR"] = $zInterno[1];
                $vValCam[$cCamNiv."_".$nNivel."_PORCENTAJE"]      = $zInterno[2];
              }
            }
          } else {
            $vValCam[$cCamNiv."_1_LIMITE_INFERIOR"] = "";
            $vValCam[$cCamNiv."_1_LIMITE_SUPERIOR"] = "";
            $vValCam[$cCamNiv."_1_PORCENTAJE"]      = "";
          }
        break;

        case "136": // Escalonada en COP Importacion
        case "206": // Escalonada en COP Exportacion
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[1]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }
          $vValCam["MINIMA"]  = $cCadena2[0];
          $vValCam["NIVELES"] = $zNum;

          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);$nNivel++;
                $vValCam["NIVELES_".$nNivel."_LIMITE_INFERIOR"] = $zInterno[0];
                $vValCam["NIVELES_".$nNivel."_LIMITE_SUPERIOR"] = $zInterno[1];
                $vValCam["NIVELES_".$nNivel."_PORCENTAJE"]      = $zInterno[2];
              }
            }
          } else {
            $vValCam["NIVELES_1_LIMITE_INFERIOR"] = "";
            $vValCam["NIVELES_1_LIMITE_SUPERIOR"] = "";
            $vValCam["NIVELES_1_PORCENTAJE"]      = "";
          }
        break;

        case "209": // Valor CIF Dividido en Pesos.
          $cCad209 = explode("~",$cCadena[1]);
          $vValCam["VALOR_PARCIAL"]                        = $cCad209[0];
          $vValCam["VALOR_FIJO_COBRO_POR_PRIMER_PARCIAL"]  = $cCad209[1];
          $vValCam["PORCENTAJE_COBRO_VALOR_FOB_ADICIONAL"] = $cCad209[2];
        break;

        case "1142": // PORCENTAJE VALOR O UNA MINIMA EN USD
        case "1146": // PORCENTAJE VALOR O UNA MINIMA
        case "210":  // Porcentaje del Valor FOB por la T.R.M.
          $cCad210 = explode("~",$cCadena[1]);
          $vValCam["PORCENTAJE"] = $cCad210[0];
          $vValCam["MINIMA"]     = $cCad210[1];
        break;

        case "306": // Porcentaje FOB o Minima
          $cCad306 = explode("~",$cCadena[1]);
          $vValCam["PORCENTAJE_TRIBUTOS"] = $cCad306[0];
          $vValCam["MINIMA"]              = $cCad306[1];
        break;

        case "129": // Porcentaje CIF o Minima
          $cCad129 = explode("~",$cCadena[1]);
          $vValCam["PORCENTAJE_CIF"]     = $cCad129[0];
          $vValCam["VALOR_FIJO_PARCIAL"] = $cCad129[1];
        break;

        case "140": // Porcentaje CIF o Minima con Variable de descargue Directo
          $cCad140 = explode("~",$cCadena[1]);
          $vValCam["PORCENTAJE_CIF"]             = $cCad140[0];
          $vValCam["MINIMA"]                     = $cCad140[1];
          $vValCam["VARIABLE_DESCARGUE_DIRECTO"] = $cCad140[2];
        break;

        case "141": // Porcentaje CIF o Minima con Variable de descargue Directo
          $cCad141 = explode("~",$cCadena[1]);
          $vValCam["PORCENTAJE_CIF"] = $cCad141[0];
          $vValCam["TASA_NEGOCIADA"] = $cCad141[1];
        break;

        case "142": // SUBPARTIDA POR UNIDAD CON MINIMA
        case "176": // VALOR FIJO POR UNIDAD CON MINIMA
        case "253": // VALOR FIJO POR UNIDAD CON MINIMA
        case "279": // SUBPARTIDA POR UNIDAD CON MINIMA
          $cCad142 = explode("~",$cCadena[1]);
          $vValCam["UNIDADES_INICIALES"]   = $cCad142[0];
          $vValCam["VALOR_INICIAL"]        = $cCad142[1];
          $vValCam["UNIDADES_ADICIONALES"] = $cCad142[2];
          $vValCam["VALOR_ADICIONAL"]      = $cCad142[3];
        break;

        case "143": // VALOR POR UNIDAD O MINIMA
        case "151": // VALOR POR UNIDAD O MINIMA
          $cCad143 = explode("~",$cCadena[1]);
          $cCamMin = ($xFcoId == '151' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MINIMA_GENERAL_CIF" : "MINIMA";
          $cCamVal = ($xFcoId == '151' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "VALOR_POR_UNIDAD_TONELADAS" : "VALOR_POR_UNIDAD";
          $vValCam[$cCamVal] = $cCad143[0];
          $vValCam[$cCamMin] = $cCad143[1];
        break;

        case "144": // VALOR FIJO POR UNIDAD CON MINIMA Y MAXIMA
          $cCad144 = explode("~",$cCadena[1]);
          $vValCam["VALOR_FIJO_POR_UNIDAD"] = $cCad144[1];
          $vValCam["VALOR_MINIMO"]          = $cCad144[1];
          $vValCam["VALOR_MAXIMO"]          = $cCad144[1];
        break;

        case "145": // VALOR FIJO POR UNIDAD CON MINIMA Y MAXIMA
          $cCad145 = f_Explode_Array($cCadena[1],"~","^");
          // Carga Suelta
          $vValCam["CARGA_SUELTA_TARIFA_INICIAL"]               = $cCad145[0][0];
          $vValCam["CARGA_SUELTA_UNIDADES_INICIALES"]           = $cCad145[0][1];
          $vValCam["CARGA_SUELTA_TRARIFA_DESPUES_DE_INICIALES"] = $cCad145[0][2];
          // Contenedor 20
          $vValCam["CONTENEDOR_20_TARIFA_INICIAL"]               = $cCad145[1][0];
          $vValCam["CONTENEDOR_20_UNIDADES_INICIALES"]           = $cCad145[1][1];
          $vValCam["CONTENEDOR_20_TRARIFA_DESPUES_DE_INICIALES"] = $cCad145[1][2];
          // Contenedor 40
          $vValCam["CONTENEDOR_40_TARIFA_INICIAL"]               = $cCad145[2][0];
          $vValCam["CONTENEDOR_40_UNIDADES_INICIALES"]           = $cCad145[2][1];
          $vValCam["CONTENEDOR_40_TRARIFA_DESPUES_DE_INICIALES"] = $cCad145[2][2];
        break;

        case "235": // COBRO POR UNIDAD DE CARGAR CONTENEDORES CON MINIMA
          $cCad235 = explode("~",$cCadena[1]);
          $vValCam["MINIMA"]                     = $cCad235[4];
          $vValCam["PORCENTAJE_CIF_PARA_GRANEL"] = $cCad235[0];
          $vValCam["CONTENEDOR_20"]              = $cCad235[1];
          $vValCam["CONTENEDOR_40"]              = $cCad235[2];
          $vValCam["CARGA_SUELTA"]               = $cCad235[3];
        break;

        case "154": //COBRO ESCALONADO POR UNIDAD DE PESO
        case "149":
        case "236":
        case "148": // Cobro Escalonado por Unidad con Minima
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[1]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }
          switch ($xFcoId) {
            case '148':
            case '149':
              $cCamMin = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MINIMA_GENERAL_CIF" : "MINIMA";
            break;
            case '236':
              $cCamMin = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MINIMA_FOB" : "MINIMA";
            break;
            default:
              $cCamMin = "MINIMA";
            break;
          }
          $cCamNiv = (($xFcoId == '148' || $xFcoId == '149' || $xFcoId == '236') && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "NIVELES_CANT_VEHICULOS" : "NIVELES";
          $vValCam[$cCamMin] = $cCadena2[0];
          $vValCam[$cCamNiv] = $zNum;

          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam[$cCamNiv."_".$nNivel."_LIMITE_INFERIOR"]  = $zInterno[0];
                $vValCam[$cCamNiv."_".$nNivel."_LIMITE_SUPERIOR"]  = $zInterno[1];
                $vValCam[$cCamNiv."_".$nNivel."_VALOR_POR_UNIDAD"] = $zInterno[2];
              }
            }
          } else {
            $vValCam[$cCamNiv."_1_LIMITE_INFERIOR"]  = "";
            $vValCam[$cCamNiv."_1_LIMITE_SUPERIOR"]  = "";
            $vValCam[$cCamNiv."_1_VALOR_POR_UNIDAD"] = "";
          }
        break;

        case "260":
        case "190": // Cobro Escalonado por Unidad con Máxima
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[1]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }
          $vValCam["MAXIMA"]  = $cCadena2[0];
          $vValCam["NIVELES"] = $zNum;

          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam["NIVELES_".$nNivel."_LIMITE_INFERIOR"]  = $zInterno[0];
                $vValCam["NIVELES_".$nNivel."_LIMITE_SUPERIOR"]  = $zInterno[1];
                $vValCam["NIVELES_".$nNivel."_VALOR_POR_UNIDAD"] = $zInterno[2];
              }
            }
          } else {
            $vValCam["NIVELES_1_LIMITE_INFERIOR"]  = "";
            $vValCam["NIVELES_1_LIMITE_SUPERIOR"]  = "";
            $vValCam["NIVELES_1_VALOR_POR_UNIDAD"] = "";
          }
        break;

        case "153": // Cobro por Unidad de Carga por Contenedores - Furgon
          $cCad153 = explode("~",$cCadena[1]);
          $vValCam["CONTENEDOR_20"] = $cCad153[0];
          $vValCam["CONTENEDOR_40"] = $cCad153[1];
          $vValCam["FURGON"]        = $cCad153[2];
        break;

        case "155": // VALOR VARIABLE POR PCC
        case "281": // VALOR VARIABLE POR PCC
          $cCad155 = explode("~",$cCadena[1]);
          $vValCam["VALOR_BASE"]        = $cCad155[0];
          $vValCam["PORCENTAJE_APLICA"] = $cCad155[1];
        break;

        case "237": // Cobro por Unidad de Carga por Contenedores con Minima
        case "156": // Cobro por Unidad de Carga por Contenedores con Minima
          $cCad156 = explode("~",$cCadena[1]);
          $vValCam["MINIMA"]        = $cCad156[0];
          $vValCam["CONTENEDOR_20"] = $cCad156[1];
          $vValCam["CONTENEDOR_40"] = $cCad156[2];
          $vValCam["CARGA_SUELTA"]  = $cCad156[3];
        break;

        case "158": // Cobro sobre porcentaje del valor de la totalidad de las facturas comerciales o minima
        case "157": // Porcentaje del Valor de la Factura Comercial o Minima
          $cCad157 = explode("~",$cCadena[1]);
          $vValCam["MINIMA"]                   = $cCad157[0];
          $vValCam["PORCENTAJE_VALOR_FACTURA"] = $cCad157[1];
        break;

        case "238": // Cobro Escalonado por cantidad de Kilos - Expo
        case "160": // Cobro Escalonado por cantidad de Kilos
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[0]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }
          $vValCam["NIVELES"] = $zNum;
          
          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam["NIVELES_".$nNivel."_LIMITE_INFERIOR"]  = $zInterno[0];
                $vValCam["NIVELES_".$nNivel."_LIMITE_SUPERIOR"]  = $zInterno[1];
                $vValCam["NIVELES_".$nNivel."_VALOR"]            = $zInterno[2];
              }
            }
          } else {
            $vValCam["NIVELES_1_LIMITE_INFERIOR"]  = "";
            $vValCam["NIVELES_1_LIMITE_SUPERIOR"]  = "";
            $vValCam["NIVELES_1_VALOR"]            = "";
          }
        break;

        case "239": 	// Porcentaje Valor FOB o Minima o Maxima - Exportaciones
        case "245": 	// Cobro Escalonado Sobre Valor FOB en USD con Minima y Maxima
        case "273": 	// COMISION ESCALONADO VALOR FIJO CON MINIMA Y MAXIMA
        case "1102": 	// COMISION ESCALONADO VALOR FIJO CON MINIMA Y MAXIMA
        case "162": 	// Porcentaje Valor FOB o Minima o Maxima
        case "161": 	// Porcentaje Valor Cif o Minima o Maxima
        case "167": 	// Cobro Escalonado Sobre Valor CIF en USD con Minima y Maxima
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[2]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }
          switch ($xFcoId) {
            case '161':
            case '167':
              $cCamMin = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MINIMA_GENERAL_CIF" : "MINIMA";
              $cCamMax = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MAXIMA_GENERAL_CIF" : "MAXIMA";
            break;
            case '245':
            case '273':
              $cCamMin = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MINIMA_FOB" : "MINIMA";
              $cCamMax = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MAXIMA_FOB" : "MAXIMA";
            break;
            default:
              $cCamMin = "MINIMA";
              $cCamMax = "MAXIMA";
            break;
          }
          $cCamNiv = (($xFcoId == '161' || $xFcoId == '167' || $xFcoId == '245' || $xFcoId == '273') && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "NIVELES_CANT_VEHICULOS" : "NIVELES";
          $vValCam[$cCamMin] = $cCadena2[0];
          $vValCam[$cCamMax] = $cCadena2[1];
          $vValCam[$cCamNiv] = $zNum;

          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam[$cCamNiv."_".$nNivel."_LIMITE_INFERIOR"] = $zInterno[0];
                $vValCam[$cCamNiv."_".$nNivel."_LIMITE_SUPERIOR"] = $zInterno[1];
                switch ($xFcoId) {
                  case "273":
                  case "1102":
                    $vValCam[$cCamNiv."_".$nNivel."_VALOR_FIJO"] = $zInterno[2];
                  break;
                  default:
                    $vValCam[$cCamNiv."_".$nNivel."_PORCENTAJE"] = $zInterno[2];
                  break;
                }
              }
            }
          } else {
            $vValCam[$cCamNiv."_1_LIMITE_INFERIOR"] = "";
            $vValCam[$cCamNiv."_1_LIMITE_SUPERIOR"] = "";
            switch ($xFcoId) {
              case "273":
              case "1102":
                $vValCam[$cCamNiv."_1_VALOR_FIJO"] = "";
              break;
              default:
                $vValCam[$cCamNiv."_1_PORCENTAJE"] = "";
              break;
            }
          }
        break;

        case "242": // Valor Fijo Por Producto o Minima
        case "165": // Valor Fijo Por Producto o Minima
          $cCad165 = explode("~",$cCadena[1]);
          $vValCam["MINIMA"]     = $cCad165[0];
          $vValCam["SIMCARD"]    = $cCad165[1];
          $vValCam["TERMINALES"] = $cCad165[2];
          $vValCam["TABLET"]     = $cCad165[3];
          $vValCam["MODEM"]      = $cCad165[4];
        break;

        case "243": // Valor x Horas o Minima
        case "166": // Valor x Horas o Minima
          $cCad166 = explode("~",$cCadena[1]);
          $cCamMin = ($xFcoId == '166' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MINIMA_GENERAL_CIF" : "MINIMA";
          $vValCam[$cCamMin]         = $cCad166[0];
          $vValCam["VALOR_POR_HORA"] = $cCad166[1];
        break;

        case "244": // Valor x Cantidad de DIM o Minima
          $cCad244 = explode("~",$cCadena[1]);
          $vValCam["MINIMA"]           = $cCad244[0];
          $vValCam["VALOR_POR_UNIDAD"] = $cCad244[1];
        break;

        case "168": // Valor Fijo Por Unidad Con Minima
        case "175": // Valor Fijo Por Unidad Con Minima
        case "247": // Valor Fijo Por Unidad Con Minima
        case "252": // Valor Fijo Por Unidad Con Minima
        case "308": // Valor Fijo Por Unidad Con Minima
          $cCad168 = explode("~",$cCadena[1]);
          $vValCam["VALOR_POR_UNIDAD"] = $cCad168[0];
          if ($xFcoId == "308") {
            $vValCam["VALOR_MINIMO"] = $cCad168[1];
          } else {
            $vValCam["MINIMA"] = $cCad168[1];
          }
        break;

        case "169": // Valor Fijo Por horas
          $cCad169 = explode("~",$cCadena[1]);
          $vValCam["HORA_DIURNA"]    = $cCad169[0];
          $vValCam["HORA_NOCTURNA"]  = $cCad169[1];
          $vValCam["HORA_DOMINICAL"] = $cCad169[2];
          $vValCam["HORA_FESTIVA"]   = $cCad169[3];
        break;

        case "170": // Valor por cantidad de vehiculos con minima
        case "248": // Valor por cantidad de vehiculos con minima
          $cCad170 = explode("~",$cCadena[1]);
          switch ($xFcoId) {
            case '170':
              $cCamMin = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MINIMA_GENERAL_CIF" : "MINIMA";
            break;
            case '248':
              $cCamMin = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MINIMA_FOB" : "MINIMA";
            break;
            default:
              $cCamMin = "MINIMA";
            break;
          }
          $vValCam["VALOR_FIJO"] = $cCad170[0];
          $vValCam[$cCamMin]     = $cCad170[1];
        break;

        case "171": // Valor fijo x sufijos
          $cCad171 = explode("~",$cCadena[1]);
          $vValCam["SUFIJO_001"] = $cCad171[0];
          $vValCam["SUFIJO_002"] = $cCad171[1];
        break;

        case "172": // Valor Cif, Valor Fijo
          $cCad172 = explode("~",$cCadena[1]);
          $vValCam["HASTA_VALOR_CIF"] = $cCad172[0];
          $vValCam["VALOR_FIJO"]      = $cCad172[1];
        break;

        case "177": // Valor por Horas con Minima
          $cCad177 = explode("~",$cCadena[1]);
          $vValCam["MINIMA"]         = $cCad177[0];
          $vValCam["HORA_ORDINARIA"] = $cCad177[1];
          $vValCam["HORA_FESTIVA"]   = $cCad177[2];
        break;

        case "178": // Cobro por Unidad de Carga con Minima
          $cCad178 = explode("~",$cCadena[1]);
          $vValCam["CONTENEDOR_20"]        = $cCad178[0];
          $vValCam["CONTENEDOR_40"]        = $cCad178[1];
          $vValCam["CARGA_SUELTA"]         = $cCad178[2];
          $vValCam["MINIMA_CONTENEDOR_20"] = $cCad178[3];
          $vValCam["MINIMA_CONTENEDOR_40"] = $cCad178[4];
          $vValCam["MINIMA_CARGA_SUELTA"]  = $cCad178[5];
        break;

        case "179": // Cobro Variable + % Variable
        case "254": // Cobro Variable + % Variable
          $cCad179 = explode("~",$cCadena[1]);
          $vValCam["PORCENTAJE_VARIABLE"] = $cCad179[0];
        break;

        case "180": // Cobro por unidad con maxima y + Cobro Variable + % Variable uniades adiacionales
          $cCad180 = explode("~",$cCadena[1]);
          $vValCam["CANTIDADES_INICIALES"] = $cCad180[0];
          $vValCam["VALOR_INICIAL"]        = $cCad180[1];
          $vValCam["PORCENTAJE_ADICIONAL"] = $cCad180[2];
        break;

        case "181": // % CIF Escalonado por intervalos
          $cCad181 = explode("~",$cCadena[1]);
          $vValCam["DIAS_INTERVALO"] = $cCad181[0];
          $vValCam["PORCENTAJE_CIF"] = $cCad181[1];
          $vValCam["MINIMA"]         = $cCad181[2];
        break;

        case "255": // Valor por posicion de acuerdo a referencia
        case "182": // Valor por posicion de acuerdo a referencia
          $cCad182 = explode("~",$cCadena[1]);
          $vValCam["VALOR_TONELADA"]       = $cCad182[0];
          $vValCam["CANTIDAD_MAXIMA"]      = $cCad182[1];
          $vValCam["PORCENTAJE_ADICIONAL"] = $cCad182[2];
        break;

        case "183": // Valor por posicion de acuerdo a referencia
          $cCad183 = explode("~",$cCadena[1]);
          $vValCam["VALOR_ESTIBA"] = $cCad183[0];
          $vValCam["MINIMA_C20"]   = $cCad183[1];
          $vValCam["MINIMA_C40"]   = $cCad183[2];
        break;

        case "184": // Cobro Escalonado por cantidad de quincenas
          $cCad184 = explode("~",$cCadena[1]);
          $vValCam["PRECIO_QNA_VEHICULO"]   = $cCad184[0];
          $vValCam["PRECIO_QNA_CAMIONETA"]  = $cCad184[1];
          $vValCam["PRECIO_QNA_CAMION"]     = $cCad184[2];
          $vValCam["PRECIO_QNA_MONTACARGA"] = $cCad184[3];
        break;

        case "185": // Cobro Escalonado por cantidad de meses
          $cCad185 = explode("~",$cCadena[1]);
          $vValCam["PRECIO_MES_VEHICULO"]   = $cCad185[0];
          $vValCam["PRECIO_MES_CAMIONETA"]  = $cCad185[1];
          $vValCam["PRECIO_MES_CAMION"]     = $cCad185[2];
          $vValCam["PRECIO_MES_MONTACARGA"] = $cCad185[3];
        break;

        case "256": // cobro escalonado con mínima en el último rango
        case "186": // cobro escalonado con mínima en el último rango
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[1]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }
          $vValCam["PORCENTAJE_CIF"] = $cCadena2[0];
          $vValCam["NIVELES"]        = $zNum;
          
          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam["NIVELES_".$nNivel."_LIMITE_INFERIOR"] = $zInterno[0];
                $vValCam["NIVELES_".$nNivel."_LIMITE_SUPERIOR"] = $zInterno[1];
                $vValCam["NIVELES_".$nNivel."_VALOR_FIJO"]      = $zInterno[2];
              }
            }
          } else {
            $vValCam["NIVELES_1_LIMITE_INFERIOR"] = "";
            $vValCam["NIVELES_1_LIMITE_SUPERIOR"] = "";
            $vValCam["NIVELES_1_VALOR_FIJO"]      = "";
          }
        break;

        case "261":
        case "191": // Cobro Escalonado por Unidad
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[1]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }
          $cCamNiv = ($xFcoId == '261' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "NIVELES_CANT_VEHICULOS" : "NIVELES";
          $vValCam[$cCamNiv] = $zNum;
          
          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam[$cCamNiv."_".$nNivel."_CANTIDAD"] = $zInterno[0];
                $vValCam[$cCamNiv."_".$nNivel."_VALOR"]    = $zInterno[1];
              }
            }
          } else {
            $vValCam[$cCamNiv."_1_CANTIDAD"] = "";
            $vValCam[$cCamNiv."_1_VALOR"]    = "";
          }
        break;

        case "263": // COBRO ESCALONADO CONTENEDORES DE 20 Y 40 VALOR FIJO POR UNIDAD
        case "193": // COBRO ESCALONADO CONTENEDORES DE 20 Y 40 VALOR FIJO POR UNIDAD
        case "262": // COBRO ESCALONADO CONTENEDORES DE 20 Y 40 VALOR FIJO POR NIVEL
        case "192": // COBRO ESCALONADO CONTENEDORES DE 20 Y 40 VALOR FIJO POR NIVEL
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[1]);
          $cCadena4 = explode("!",$cCadena2[2]);
          $zNum=0;
          $zNum2=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }
          for($j=1; $j<count($cCadena4); $j++){
            if($cCadena4[$j]!=""){
              $zNum2=$zNum2+1;
            }
          }

          $vValCam["NIVELES_C20"] = $zNum;

          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam["NIVELES_C20_".$nNivel."_LIMITE_INFERIOR"]  = $zInterno[0];
                $vValCam["NIVELES_C20_".$nNivel."_LIMITE_SUPERIOR"]  = $zInterno[1];
                $vValCam["NIVELES_C20_".$nNivel."_VALOR"]            = $zInterno[2];
              }
            }
          } else {
            $vValCam["NIVELES_C20_1_LIMITE_INFERIOR"]  = "";
            $vValCam["NIVELES_C20_1_LIMITE_SUPERIOR"]  = "";
            $vValCam["NIVELES_C20_1_VALOR"]            = "";
          }

          $vValCam["NIVELES_C40"] = $zNum2;

          if ($xFcoTar != "") {
            $nNivel = 0;
            for($j=1; $j<count($cCadena4); $j++){
              if($cCadena4[$j]!="") {
                $zInterno=explode("^",$cCadena4[$j]);
                $nNivel++;
                $vValCam["NIVELES_C40_".$nNivel."_LIMITE_INFERIOR"]  = $zInterno[0];
                $vValCam["NIVELES_C40_".$nNivel."_LIMITE_SUPERIOR"]  = $zInterno[1];
                $vValCam["NIVELES_C40_".$nNivel."_VALOR"]            = $zInterno[2];
              }
            }
          } else {
            $vValCam["NIVELES_C40_1_LIMITE_INFERIOR"]  = "";
            $vValCam["NIVELES_C40_1_LIMITE_SUPERIOR"]  = "";
            $vValCam["NIVELES_C40_1_VALOR"]            = "";
          }
        break;

        case "264": // COBRO CONTENEDORES DE 20 Y DE 40 VALOR FIJO
        case "195": // COBRO CONTENEDORES DE 20 Y DE 40 VALOR POR UNIDAD
        case "265": // COBRO CONTENEDORES DE 20 Y DE 40 VALOR POR UNIDAD
        case "194": // COBRO CONTENEDORES DE 20 Y DE 40 VALOR FIJO
          $cCad194 =  explode("~",$cCadena[1]);
          $vValCam["CONTENEDOR_20"] = $cCad194[0];
          $vValCam["CONTENEDOR_40"] = $cCad194[1];
        break;

        case "266": // Porcentaje CIF con Maxima
        case "196": // Porcentaje CIF con Maxima
          $cCad196 = explode("~",$cCadena[1]);
          $cCamMax = ($xFcoId == '196' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MAXIMA_GENERAL_CIF" : "MAXIMA";
          $vValCam["PORCENTAJE_CIF"] = $cCad196[0];
          $vValCam[$cCamMax]         = $cCad196[1];
        break;

        case "267": // COMISION POR TONELADAS CON MINIMA
          $cCad267 = explode("~",$cCadena[1]);
          $cCamVal = ($xFcoId == '267' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "VALOR_POR_UNIDAD_TONELADAS" : "VALOR_TONELADA";
          $cCamMin = ($xFcoId == '267' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MINIMA_FOB" : "MINIMA";
          $vValCam[$cCamVal] = $cCad267[0];
          $vValCam[$cCamMin] = $cCad267[1];
        break;

        case "268": //  COMISION POR PORCENTAJE
          $vValCam["PORCENTAJE_FOB"] = $cCadena[1];
        break;

        case "197": // Valor x horas con valor adicional y máxima
          $cCad197 = explode("~",$cCadena[1]);
          $cCamMax = ($xFcoId == '197' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MAXIMA_GENERAL_CIF" : "MAXIMA";
          $cCamVal = ($xFcoId == '197' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "VALOR_POR_HORA" : "VALOR";
          $vValCam[$cCamVal]          = $cCad197[0];
          $vValCam["HORAS"]           = $cCad197[1];
          $vValCam["VALOR_ADICIONAL"] = $cCad197[2];
          $vValCam[$cCamMax]          = $cCad197[3];
        break;

        case "269": // Cobro Escalonado Sobre Valor CIF en USD con Minima y Maxima
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[2]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }
          $vValCam["MINIMA"]  = $cCadena2[0];
          $vValCam["MAXIMA"]  = $cCadena2[1];
          $vValCam["NIVELES"] = $zNum;

          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam["NIVELES_".$nNivel."_LIMITE_INFERIOR"] = $zInterno[0];
                $vValCam["NIVELES_".$nNivel."_LIMITE_SUPERIOR"] = $zInterno[1];
                $vValCam["NIVELES_".$nNivel."_PORCENTAJE"]      = $zInterno[2];
              }
            }
          } else {
            $vValCam["NIVELES_1_LIMITE_INFERIOR"] = "";
            $vValCam["NIVELES_1_LIMITE_SUPERIOR"] = "";
            $vValCam["NIVELES_1_PORCENTAJE"]      = "";
          }
        break;

        case "198": // Porcentaje sobre comision
          $cCad198 = explode("~",$cCadena[1]);
          $vValCam["PORCENTAJE_CIF"]            = $cCad198[0];
          $vValCam["PORCENTAJE_SOBRE_COMISION"] = $cCad198[1];
        break;

        case "271": // VALOR FIJO CONSOLIDADO POR CUBICAJE Y DO
        case "1100": // VALOR FIJO CONSOLIDADO POR CUBICAJE Y DO
          $cCad1100 = explode("~",$cCadena[1]);
          $vValCam["VALOR_TRAMITE"] = $cCad1100[0];
        break;

        case "272": // COBRO POR TRM PACTADA
        case "1101": // COBRO POR TRM PACTADA
          $cCad1101 = explode("~",$cCadena[1]);
          $vValCam["TASA_PACTADA"] = $cCad1101[0];
          $vValCam["PORCENTAJE"]   = $cCad1101[1];
          $vValCam["MINIMA"]       = $cCad1101[2];
        break;

        case "274": // COBRO SOBRE PORCENTAJE DE NEGOCIACION
        case "1103": // COBRO SOBRE PORCENTAJE DE NEGOCIACION
          $cCad1103 = explode("~",$cCadena[1]);
          $vValCam["TARIFA"]     = $cCad1103[0];
          $vValCam["PORCENTAJE"] = $cCad1103[1];
        break;

        case "1104": // COBRO % SOBRE VALOR CIF (CONDICIONES ESPECIALES)
          $cCad1104 = explode("~",$cCadena[1]);
          $vValCam["PORCENTAJE"] = $cCad1104[0];
        break;
        case "1105": // COBRO % SOBRE VALOR CIF (CONDICIONES ESPECIALES)
          $cCad1105 = explode("~",$cCadena[1]);
          $cCamMin = ($xFcoId == '1105' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MINIMA_GENERAL_CIF" : "MINIMA";
          $cCamMax = ($xFcoId == '1105' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MAXIMA_GENERAL_CIF" : "MAXIMA";
          $vValCam[$cCamMin]     = $cCad1105[1];
          $vValCam[$cCamMax]     = $cCad1105[2];
          $vValCam["PORCENTAJE"] = $cCad1105[0];
        break;

        case "1106": // VALOR CIF CON MINIMA-ESCALONADO EN USD CON % DESCUENTO.
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[1]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }
          $cCamMin = ($xFcoId == '1106' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MINIMA_GENERAL_CIF" : "MINIMA";
          $cCamNiv = ($xFcoId == '1106' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "NIVELES_CANT_VEHICULOS" : "NIVELES";
          $vValCam[$cCamMin]               = $cCadena2[0];
          $vValCam["PORCENTAJE_DESCUENTO"] = $cCadena2[2];
          $vValCam[$cCamNiv]               = $zNum;

          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam[$cCamNiv."_".$nNivel."_LIMITE_INFERIOR"] = $zInterno[0];
                $vValCam[$cCamNiv."_".$nNivel."_LIMITE_SUPERIOR"] = $zInterno[1];
                $vValCam[$cCamNiv."_".$nNivel."_PORCENTAJE"]      = $zInterno[2];
              }
            }
          } else {
            $vValCam[$cCamNiv."_1_LIMITE_INFERIOR"] = "";
            $vValCam[$cCamNiv."_1_LIMITE_SUPERIOR"] = "";
            $vValCam[$cCamNiv."_1_PORCENTAJE"]      = "";
          }
        break;

        case "1107": // PORCENTAJE O MÍNIMA POR CARGA GRANEL O CONTENEDOR
          $vTarifaVI = f_Explode_Array($xFcoTar,"|","~");
          // Contenedor
          $vValCam["CONTENEDOR_PORCENTAJE_CIF"] = $vTarifaVI[0][0];
          $vValCam["CONTENEDOR_MINIMA"]         = $vTarifaVI[0][1];
          // Granel
          $vValCam["GRANEL_PORCENTAJE_CIF"] = $vTarifaVI[1][0];
          $vValCam["GRANEL_MINIMA"]         = $vTarifaVI[1][1];
        break;

        case "1108": // COBRO ESCALONADO PORCENTAJE O MÍNIMA POR CANTIDAD BL. 
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[1]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }
          $vValCam["PORCENTAJE_CIF"] = $cCadena2[0];
          $vValCam["NIVELES"]        = $zNum;

          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam["NIVELES_".$nNivel."_LIMITE_INFERIOR"] = $zInterno[0];
                $vValCam["NIVELES_".$nNivel."_LIMITE_SUPERIOR"] = $zInterno[1];
                $vValCam["NIVELES_".$nNivel."_VALOR_MINIMA"]    = $zInterno[2];
              }
            }
          } else {
            $vValCam["NIVELES_1_LIMITE_INFERIOR"] = "";
            $vValCam["NIVELES_1_LIMITE_SUPERIOR"] = "";
            $vValCam["NIVELES_1_VALOR_MINIMA"]    = "";
          }
        break;

        case "1109": // PORCENTAJE CIF O MÍNIMA POR CARGA GRANEL O CONTENEDOR POR NIVEL O TIPO DE MERCANCÍA.
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[1]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }
          $vValCam["NIVELES"] = $zNum;
          
          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam["NIVELES_".$nNivel."_NIVELES"]        = $zInterno[0];
                $vValCam["NIVELES_".$nNivel."_PORCENTAJE_CIF"] = $zInterno[1];
                $vValCam["NIVELES_".$nNivel."_MINIMA"]         = $zInterno[2];
              }
            }
          } else {
            $vValCam["NIVELES_1_NIVELES"]        = "";
            $vValCam["NIVELES_1_PORCENTAJE_CIF"] = "";
            $vValCam["NIVELES_1_MINIMA"]         = "";
          }
        break;

        case "1110":
          $cCad1110 = explode("~",$cCadena[1]);
          $cCamVal = ($xFcoId == '1110' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "VALOR_POR_HORA" : "VALOR";
          $vValCam[$cCamVal]          = $cCad1110[0];
          $vValCam["HORAS"]           = $cCad1110[1];
          $vValCam["VALOR_ADICIONAL"] = $cCad1110[2];
        break;

        case "276": // VALOR FOB CON MINIMA CON % DESCUENTO
          $cCad276 = explode("~",$cCadena[1]);
          $cCamMin = ($xFcoId == '1110' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MINIMA_FOB" : "MINIMA";
          $vValCam["PORCENTAJE_FOB"]       = $cCad276[0];
          $vValCam[$cCamMin]               = $cCad276[1];
          $vValCam["PORCENTAJE_DESCUENTO"] = $cCad276[2];
        break;

        case "1111": // COBRO % SOBRE PCC
        case "1118": // INTERESES SOBRE PCC
        case "277":  // COBRO % SOBRE PCC
        case "278":  // INTERESES SOBRE PCC
        case "310":  // COBRO % SOBRE PCC
        case "311":  // INTERESES SOBRE PCC
          $cCad1111 = explode("~",$cCadena[1]);
          $vValCam["PORCENTAJE"] = $cCad1111[0];
        break;

        case "1112": // Porcentaje CIF con Minima y Maxima
          $cCad1112 = explode("~",$cCadena[1]);
          $vValCam["VALOR_TONELADA"] = $cCad1112[0];
          $vValCam["MINIMA"]         = $cCad1112[1];
          $vValCam["MAXIMA"]         = $cCad1112[2];
        break;

        case "1113": // PORCENTAJE CIF O MÍNIMA POR CARGA GRANEL O CONTENEDOR POR NIVEL O TIPO DE MERCANCÍA.
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[1]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }
          $vValCam["NIVELES"] = $zNum;

          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam["NIVELES_".$nNivel."_NIVELES"]        = $zInterno[0];
                $vValCam["NIVELES_".$nNivel."_PORCENTAJE_CIF"] = $zInterno[1];
                $vValCam["NIVELES_".$nNivel."_MINIMA"]         = $zInterno[2];
                $vValCam["NIVELES_".$nNivel."_MAXIMA"]         = $zInterno[3];
              }
            }
          } else {
            $vValCam["NIVELES_1_NIVELES"]        = "";
            $vValCam["NIVELES_1_PORCENTAJE_CIF"] = "";
            $vValCam["NIVELES_1_MINIMA"]         = "";
            $vValCam["NIVELES_1_MAXIMA"]         = "";
          }
        break;

        case "1114": // VALOR CIF CON MINIMA + ITEMS
          $cCad1114 = explode("~",$cCadena[1]);
          $vValCam["PORCENTAJE_CIF"]       = $cCad1114[0];
          $vValCam["MINIMA"]               = $cCad1114[1];
          $vValCam["ITEMS_INICIALES"]      = $cCad1114[2];
          $vValCam["VALOR_ITEM_ADICIONAL"] = $cCad1114[3];
        break;

        case "1115": // CANTIDAD DE HORAS X CONTENEDOR X PERSONA
          $cCad1115 = explode("~",$cCadena[1]);
          $vValCam["HORAS_POR_BLOQUE"] = $cCad1115[0];
          $vValCam["VALOR_BLOQUE"]     = $cCad1115[1];
        break;

        case "1116": // CANTIDAD DE HORAS X CONTENEDOR X PERSONA
          $cCad1116 = explode("~",$cCadena[1]);
          $vValCam["PORCENTAJE_CIF"]  = $cCad1116[0];
          $vValCam["VALOR_MINIMA"]    = $cCad1116[1];
          $vValCam["VALOR_ADICIONAL"] = $cCad1116[2];
        break;

        case "1119": // % CIF O MINIMA VARIABLE CONTENEDOR O DESCARGUE DIRECTO
          $cCad1119 = explode("~",$cCadena[1]);
          $vValCam["PORCENTAJE_CIF"]    = $cCad1119[0];
          $vValCam["MINIMA"]            = $cCad1119[1];
          $vValCam["CONTENEDOR_20"]     = $cCad1119[2];
          $vValCam["CONTENEDOR_40"]     = $cCad1119[3];
          $vValCam["DESCARGUE_DIRECTO"] = $cCad1119[4];
        break;

        case "1120": // % CIF O MINIMA POR TIPO CONTENEDOR
          $cCad1120 = explode("~",$cCadena[1]);
          // Contenedor 20
          $vValCam["CONTENEDOR_20_PORCENTAJE_CIF"] = $cCad1120[0];
          $vValCam["CONTENEDOR_20_MINIMA"]         = $cCad1120[1];
          // Contenedor 40
          $vValCam["CONTENEDOR_40_PORCENTAJE_CIF"] = $cCad1120[2];
          $vValCam["CONTENEDOR_40_MINIMA"]         = $cCad1120[3];
          // Carga Suelta
          $vValCam["CARGA_SUELTA_PORCENTAJE_CIF"] = $cCad1120[4];
          $vValCam["CARGA_SUELTA_MINIMA"]         = $cCad1120[5];
        break;
        
        case "1121": // COBRO VARIABLE SEGUN CANTIDAD DE SACOS CON MINIMA
        case "280": // COBRO VARIABLE SEGUN CANTIDAD DE SACOS CON MINIMA
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[1]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }
          $cCamNiv = ($xFcoId == '1121' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "NIVELES_CANT_VEHICULOS" : "NIVELES";
          $vValCam[$cCamNiv] = $zNum;

          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam[$cCamNiv."_".$nNivel."_LIMITE_INFERIOR"]  = $zInterno[0];
                $vValCam[$cCamNiv."_".$nNivel."_LIMITE_SUPERIOR"]  = $zInterno[1];
                $vValCam[$cCamNiv."_".$nNivel."_VALOR"]            = $zInterno[2];
              }
            }
          } else {
            $vValCam[$cCamNiv."_1_LIMITE_INFERIOR"]  = "";
            $vValCam[$cCamNiv."_1_LIMITE_SUPERIOR"]  = "";
            $vValCam[$cCamNiv."_1_VALOR"]            = "";
          }
        break;

        case "1123": // VALOR POR PEDIDO CON VALOR ADICIONAL
        case "1125": // VALOR POR PEDIDO CON VALOR ADICIONAL POR UNIDAD MAS COMISION ADICIONAL
          $cCad1123 = explode("~",$cCadena[1]);
          $vValCam["UNIDADES_MINIMAS"]                     = $cCad1123[0];
          $vValCam["VALOR_POR_UNIDADES_MINIMAS"]           = $cCad1123[1];
          $vValCam["VALOR_ADICIONAL_POR_UNIDAD_ADICIONAL"] = $cCad1123[2];
        break;

        case "1126": // TRM OPERACION
          $cCad1126 = explode("~",$cCadena[1]);
          $vValCam["COSTO_ADICIONAL"] = $cCad1126[0];
          $vValCam["VALOR_FIJO"]      = $cCad1126[1];
        break;

        case "1127": // COBRO ESCALONADO POR CANTIDAD DE CONTENDEDORES
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[1]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }
          $vValCam["MINIMA"]  = $cCadena2[0];
          $vValCam["NIVELES"] = $zNum;
          
          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam["NIVELES_".$nNivel."_LIMITE_INFERIOR"]  = $zInterno[0];
                $vValCam["NIVELES_".$nNivel."_LIMITE_SUPERIOR"]  = $zInterno[1];
                $vValCam["NIVELES_".$nNivel."_VALOR_POR_UNIDAD"] = $zInterno[2];
              }
            }
          } else {
            $vValCam["NIVELES_1_LIMITE_INFERIOR"]  = "";
            $vValCam["NIVELES_1_LIMITE_SUPERIOR"]  = "";
            $vValCam["NIVELES_1_VALOR_POR_UNIDAD"] = "";
          }
        break;

        case "282": // PORCENTAJE (%) A COBRAR SOBRE LOS PAGOS A TERCEROS (CONDICION ESPECIAL)
        case "1128": // PORCENTAJE (%) A COBRAR SOBRE LOS PAGOS A TERCEROS (CONDICION ESPECIAL)
          $cCad1128 = explode("~",$cCadena[1]);
          $vValCam["PORCENTAJE"] = $cCad1128[0];
        break;
          
        case "1129": // COBRO POR HOJA PRINCIPAL Y HOJA SECUNDARIA
          $cCad1129 = explode("~",$cCadena[1]);
          $vValCam["VALOR_HOJA_PRINCIPAL"] = $cCad1129[0];
          $vValCam["VALOR_HOJA_ADICIONAL"] = $cCad1129[1];
        break;

        case "1130": // ESCALONADO POR % CIF + MINIMA
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[1]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }

          $vValCam["MINIMA_DEPOSITO_HABILITADO"] = $cCadena2[0];
          $vValCam["MINIMA_DESCARGUE_DIRECTO"]   = $cCadena2[2];
          $vValCam["NIVELES"]                    = $zNum;

          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam["NIVELES_".$nNivel."_LIMITE_INFERIOR"] = $zInterno[0];
                $vValCam["NIVELES_".$nNivel."_LIMITE_SUPERIOR"] = $zInterno[1];
                $vValCam["NIVELES_".$nNivel."_PORCENTAJE"]      = $zInterno[2];
              }
            }
          } else {
            $vValCam["NIVELES_1_LIMITE_INFERIOR"] = "";
            $vValCam["NIVELES_1_LIMITE_SUPERIOR"] = "";
            $vValCam["NIVELES_1_PORCENTAJE"]      = "";
          }
        break;

        case "1131": // VALOR FIJO DEPOSITO HABILITADO O DESCARGUE DIRECTO
        case "1136": // VALOR FIJO DEPOSITO HABILITADO O DESCARGUE DIRECTO EUROS
          $cCad1131 = explode("~",$cCadena[1]);
          $vValCam["VALOR_FIJO_DEPOSITO_HABILITADO"] = $cCad1131[0];
          $vValCam["VALOR_FIJO_DESCARGUE_DIRECTO"]   = $cCad1131[1];
        break;

        case "1132": // ESCALONADO POR CANTIDAD DE ITEM EN FACTURA 
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[1]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }
          $vValCam["NIVELES"]                  = $zNum;
          $vValCam["VALOR_ADICIONAL_POR_ITEM"] = $cCadena2[0];
          $vValCam["CANTIDAD_MINIMA_SERIALES"] = $cCadena2[2];
          $vValCam["VALOR_ADICIONAL_SERIALES"] = $cCadena2[3];

          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam["NIVELES_".$nNivel."_LIMITE_INFERIOR"] = $zInterno[0];
                $vValCam["NIVELES_".$nNivel."_LIMITE_SUPERIOR"] = $zInterno[1];
                $vValCam["NIVELES_".$nNivel."_VALOR_FIJO"]      = $zInterno[2];
              }
            }
          } else {
            $vValCam["NIVELES_1_LIMITE_INFERIOR"] = "";
            $vValCam["NIVELES_1_LIMITE_SUPERIOR"] = "";
            $vValCam["NIVELES_1_VALOR_FIJO"]      = "";
          }
        break;

        case "1133": // VALOR FIJO DEPOSITO HABILITADO O DESCARGUE DIRECTO
          $cCad1133 = explode("~",$cCadena[1]);
          $vValCam["VALOR_FIJO_DIM"] = $cCad1133[0];
          $vValCam["VALOR_FIJO_DAV"] = $cCad1133[1];
        break;

        case "1134": // PORCENTAJE SOBRE TRIBUTOS:(0,1% TRIBUTOS)
          $cCad1134 = explode("~",$cCadena[1]);
          $vValCam["PORCENTAJE_SOBRE_TRIBUTOS"] = $cCad1134[0];
        break;

        case "1135": // VALOR FIJO + PORCENTAJE CIF
          $cCad1135 = explode("~",$cCadena[1]);
          $vValCam["PORCENTAJE_CIF"] = $cCad1135[0];
          $vValCam["VALOR_FIJO"]     = $cCad1135[1];
        break;

        case "1137": // VALOR ESCALONADO POR CANTIDAD DE FACTURAS + VALOR ADICIONAL X CANTIDAD DE ITEMS 
          $cCadena2 = explode("~",$cCadena[1]);
          $cCadena3 = explode("!",$cCadena2[1]);
          $zNum=0;
          for($i=1; $i<count($cCadena3); $i++){
            if($cCadena3[$i]!=""){
              $zNum=$zNum+1;
            }
          }
          $vValCam["VALOR_ADICIONAL_POR_ITEM"] = $cCadena2[0];
          $vValCam["NIVELES"]                  = $zNum;

          if ($xFcoTar != "") {
            $nNivel = 0;
            for($i=1; $i<count($cCadena3); $i++){
              if($cCadena3[$i]!="") {
                $zInterno=explode("^",$cCadena3[$i]);
                $nNivel++;
                $vValCam["NIVELES_".$nNivel."_LIMITE_INFERIOR"]  = $zInterno[0];
                $vValCam["NIVELES_".$nNivel."_LIMITE_SUPERIOR"]  = $zInterno[1];
                $vValCam["NIVELES_".$nNivel."_VALOR_POR_UNIDAD"] = $zInterno[2];
              }
            }
          } else {
            $vValCam["NIVELES_1_LIMITE_INFERIOR"]  = "";
            $vValCam["NIVELES_1_LIMITE_SUPERIOR"]  = "";
            $vValCam["NIVELES_1_VALOR_POR_UNIDAD"] = "";
          }
        break;
      }

      $cCamResal = "";
      foreach($vValCam as $cKey => $cValue) {
        $cCamResal .= "$cKey~$cValue|";

        if ($cKey == "NIVELES_C20" || substr($cKey,0,12) == "NIVELES_C20_") {
          $vInd = explode("_",$cKey);
          if ($cKey == "NIVELES_C20") { 
            $vCamDinNC20[0][0] = "$cKey";
          } elseif (substr($cKey,-16) == "_LIMITE_INFERIOR") {
            $vCamDinNC20[1][$vInd[2]] = "$cKey";
          } elseif (substr($cKey,-16) == "_LIMITE_SUPERIOR") {
            $vCamDinNC20[2][$vInd[2]] = "$cKey";
          } else {
            if (in_array("$cKey", $vCamDinNC20[3][$vInd[2]]) == false) {
              $vCamDinNC20[3][$vInd[2]][] = "$cKey";
            }
          }
        } elseif ($cKey == "NIVELES_C40" || substr($cKey,0,12) == "NIVELES_C40_") {
          $vInd = explode("_",$cKey);
          if ($cKey == "NIVELES_C40") { 
            $vCamDinNC40[0][0] = "$cKey";
          } elseif (substr($cKey,-16) == "_LIMITE_INFERIOR") {
            $vCamDinNC40[1][$vInd[2]] = "$cKey";
          } elseif (substr($cKey,-16) == "_LIMITE_SUPERIOR") {
            $vCamDinNC40[2][$vInd[2]] = "$cKey";
          } else {
            if (in_array("$cKey", $vCamDinNC40[3][$vInd[2]]) == false) {
              $vCamDinNC40[3][$vInd[2]][] = "$cKey";
            }
          }
        } elseif ($cKey == "NIVELES_CANT_VEHICULOS" || substr($cKey,0,23) == "NIVELES_CANT_VEHICULOS_") {
          $vInd = explode("_",$cKey);
          if ($cKey == "NIVELES_CANT_VEHICULOS") { 
            $vCamDinVeh[0][0] = "$cKey";
          } elseif (substr($cKey,-16) == "_LIMITE_INFERIOR") {
            $vCamDinVeh[1][$vInd[3]] = "$cKey";
          } elseif (substr($cKey,-16) == "_LIMITE_SUPERIOR") {
            $vCamDinVeh[2][$vInd[3]] = "$cKey";
          } elseif (substr($cKey,-15) == "_NIVEL_INFERIOR") {
            $vCamDinVeh[3][$vInd[3]] = "$cKey";
          } elseif (substr($cKey,-15) == "_NIVEL_SUPERIOR") {
            $vCamDinVeh[4][$vInd[3]] = "$cKey";
          } else {
            if (in_array("$cKey", $vCamDinVeh[5][$vInd[3]]) == false) {
              $vCamDinVeh[5][$vInd[3]][] = "$cKey";
            }
          }
        } elseif ($cKey == "NIVELES" || substr($cKey,0,8) == "NIVELES_") {
          $vInd = explode("_",$cKey);
          if ($cKey == "NIVELES") { 
            $vCamDinN[0][0] = "$cKey";
          } elseif (substr($cKey,-16) == "_LIMITE_INFERIOR") {
            $vCamDinN[1][$vInd[1]] = "$cKey";
          } elseif (substr($cKey,-16) == "_LIMITE_SUPERIOR") {
            $vCamDinN[2][$vInd[1]] = "$cKey";
          } elseif (substr($cKey,-15) == "_NIVEL_INFERIOR") {
            $vCamDinN[3][$vInd[1]] = "$cKey";
          } elseif (substr($cKey,-15) == "_NIVEL_SUPERIOR") {
            $vCamDinN[4][$vInd[1]] = "$cKey";
          } else {
            if (in_array("$cKey", $vCamDinN[5][$vInd[1]]) == false) {
              $vCamDinN[5][$vInd[1]][] = "$cKey";
            }
          }
        } else {
          if (in_array("$cKey", $vCamDin) == false) {
            $vCamDin[] = "$cKey";
          }
        }
      }

      return $cCamResal;
    }

    /**
     * Metodo que retorna los campos dinamicos ordenados
     */
    function fnOrdenarCampos($vCamDin, $vCamDinNC20, $vCamDinNC40, $vCamDinVeh, $vCamDinN) {
      /**
       * Recibe como Parametro dos posiciones:
       * Los array con los campos dinamicos sueltos, con niveles, con niveles de vehiculos, con niveles de C20 y C40 son por referencia
       * Estos array deben definirse en el metodo que llama a esta funcion
       * 
       * Retorna un array con los campos ordenados
       * $cCamResal
      */
      
      // Inicializando vector de retorno
      $vCamNew = array();

      // Campos sueltos
      for($n=0;$n<count($vCamDin);$n++) {
        $vCamNew[] = $vCamDin[$n];
      }
      // Campos con Niveles
      if (count($vCamDinN) > 0) {
        $vCamNew[] = $vCamDinN[0][0];
        // Comparando niveles de LIMITE_INFERIOR con NIVEL_INFERIOR
        $nMax = (count($vCamDinN[1]) > count($vCamDinN[3])) ? count($vCamDinN[1]) : count($vCamDinN[3]);
        for($n=1;$n<=$nMax;$n++) {
          if (isset($vCamDinN[1][$n])) {
            $vCamNew[] = $vCamDinN[1][$n];
          }
          if (isset($vCamDinN[2][$n])) {
            $vCamNew[] = $vCamDinN[2][$n];
          }
          if (isset($vCamDinN[3][$n])) {
            $vCamNew[] = $vCamDinN[3][$n];
          }
          if (isset($vCamDinN[4][$n])) {
            $vCamNew[] = $vCamDinN[4][$n];
          }
          if (isset($vCamDinN[5][$n])) {
            for($m=0;$m<count($vCamDinN[5][$n]);$m++) {
              if (isset($vCamDinN[5][$n][$m])) {
                $vCamNew[] = $vCamDinN[5][$n][$m];
              }
            }
          }
        }
      }
      // Campos con Nivles de Vehiculos (aplica para GRUMALCO)
      if (count($vCamDinVeh) > 0) {
        $vCamNew[] = $vCamDinVeh[0][0];
        // Comparando niveles de LIMITE_INFERIOR con NIVEL_INFERIOR
        $nMax = (count($vCamDinVeh[1]) > count($vCamDinVeh[3])) ? count($vCamDinVeh[1]) : count($vCamDinVeh[3]);
        for($n=1;$n<=$nMax;$n++) {
          if (isset($vCamDinVeh[1][$n])) {
            $vCamNew[] = $vCamDinVeh[1][$n];
          }
          if (isset($vCamDinVeh[2][$n])) {
            $vCamNew[] = $vCamDinVeh[2][$n];
          }
          if (isset($vCamDinVeh[3][$n])) {
            $vCamNew[] = $vCamDinVeh[3][$n];
          }
          if (isset($vCamDinVeh[4][$n])) {
            $vCamNew[] = $vCamDinVeh[4][$n];
          }
          if (isset($vCamDinVeh[5][$n])) {
            for($m=0;$m<count($vCamDinVeh[5][$n]);$m++) {
              if (isset($vCamDinVeh[5][$n][$m])) {
                $vCamNew[] = $vCamDinVeh[5][$n][$m];
              }
            }
          }
        }
      }
      // Campos con Niveles C20
      if (count($vCamDinNC20) > 0) {
        $vCamNew[] = $vCamDinNC20[0][0];
        for($n=1;$n<=count($vCamDinNC20[1]);$n++) {
          if (isset($vCamDinNC20[1][$n])) {
            $vCamNew[] = $vCamDinNC20[1][$n];
          }
          if (isset($vCamDinNC20[2][$n])) {
            $vCamNew[] = $vCamDinNC20[2][$n];
          }
          if (isset($vCamDinNC20[3][$n])) {
            for($m=0;$m<count($vCamDinNC20[3][$n]);$m++) {
              if (isset($vCamDinNC20[3][$n][$m])) {
                $vCamNew[] = $vCamDinNC20[3][$n][$m];
              }
            }
          }
        }
      }
      // Campos con Niveles C40
      if (count($vCamDinNC40) > 0) {
        $vCamNew[] = $vCamDinNC40[0][0];
        for($n=1;$n<=count($vCamDinNC40[1]);$n++) {
          if (isset($vCamDinNC40[1][$n])) {
            $vCamNew[] = $vCamDinNC40[1][$n];
          }
          if (isset($vCamDinNC40[2][$n])) {
            $vCamNew[] = $vCamDinNC40[2][$n];
          }
          if (isset($vCamDinNC40[3][$n])) {
            for($m=0;$m<count($vCamDinNC40[3][$n]);$m++) {
              if (isset($vCamDinNC40[3][$n][$m])) {
                $vCamNew[] = $vCamDinNC40[3][$n][$m];
              }
            }
          }
        }
      }

      return $vCamNew;
    }
    
    /***
     * Metodo para generar el archivo excel base para la creacion de tarifas
     */
    function fnGenerarFormato($pArrayParametros) {
      global $xConexion01; global $cAlfa; global $vSysStr; global $kUser;

      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var int
       */
      $nSwitch = 0;

      /**
       * Variable para hacer el retorno.
       * 
       * @var array
       */
      $mReturn    = array();
      $mReturn[0] = "";

      /**
       * Variable para alamacenar errores.
       * 
       * @var array
       */
      $vError = array();
      $vError['TABLAERR'] = $pArrayParametros['TABLAERR'];

      /**
       * Instanciando Objeto para el Guardado de Errores.
       */
      $objEstructuraTarifasFacturacion = new cEstructurasTarfiasFacturacion();

      // Array con informacion de parametricas
      $vForCob = array();
      $mForCob = array();

      $vConEsp = array();
      $mConEsp = array();

      // Array con los campos dinamico creados
      $vCamDin     = array(); // Campos sueltos
      $vCamDinN    = array(); // Campos Tarifas con Niveles
      $vCamDinNC20 = array(); // Campos Tarifas con Niveles C20
      $vCamDinNC40 = array(); // Campos Tarifas con Niveles C40
      $vCamDinVeh  = array(); // Campos Tarifas con Niveles Vehiculos (aplica para GRUMALCO)

      $qInsCab  = "INSERT INTO $cAlfa.{$pArrayParametros['TABLAXXX']} (";
      $qInsCab .= "ID_CONCEPTO_COBRO,";
      $qInsCab .= "DESCRIPCION_CONCEPTO_COBRO,";
      $qInsCab .= "DESCRIPCION_PERSONALIZADA_CONCEPTO_COBRO,";
      $qInsCab .= "ID_FORMA_COBRO,";
      $qInsCab .= "DESCRIPCION_FORMA_COBRO,";
      $qInsCab .= "MONEDA,";
      $qInsCab .= "CONDICION_ESPECIAL,";
      $qInsCab .= "APLICA_TARIFA_POR,";
      $qInsCab .= "ID_APLICA_TARIFA_POR,";
      $qInsCab .= "TIPO_OPERACION,";
      //Entre estos campos el reporte genera los campos dinamicos de las tarifas
      $qInsCab .= "COLUMNAS_RESALTADAS) VALUES ";

      // Se consulta la tabla de conceptos de cobro
      $qConCob  = "SELECT ";
      $qConCob .= "seridxxx,";
      $qConCob .= "serdesxx,";
      $qConCob .= "serdespx,";
      $qConCob .= "fcoidxxx,";
      $qConCob .= "sertopxx,";
      $qConCob .= "sercones ";
      $qConCob .= "FROM $cAlfa.fpar0129 ";
      $qConCob .= "WHERE ";
      $qConCob .= "ctoidxxx != \"\" AND ";
      $qConCob .= "regestxx  = \"ACTIVO\"";
      $xConCob  = f_MySql("SELECT","",$qConCob,$xConexion01,"");
      // echo $qConCob . "~" . mysql_num_rows($xConCob)."<br><br>";
      if (mysql_num_rows($xConCob) == 0) {
        $nSwitch = 1;
        $vError['TIPOERRX'] = "ERROR";
        $vError['DESERROR'] = "No Se Encontraron Registros.";
        $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
      }

      // Creando columnas dinamicas
      if ($nSwitch == 0) {
        
        $nCanReg = 0;
        $mServicios = array();
        while ($xRCC = mysql_fetch_assoc($xConCob)) {
          $nCanReg++;
          if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = $objEstructuraTarifasFacturacion->fnReiniciarConexionDBRTarifasFacturacion($xConexion01); }

          // Almacendando servicios en un array
          $mServicios[] = $xRCC;

          // Extrayendo formas de cobro para realizar la busqueda de sus campos dinamicos una sola vez
          $vForId = explode("~",$xRCC['fcoidxxx']);
          for ($n=0; $n<count($vForId); $n++) {
            if ($vForId[$n] != "") {
              // Buscando Informacion Formas de Cobro
              if (in_array($vForId[$n],$vForCob) == false) {
                $qDatFor  = "SELECT fcoidxxx, ";
                $qDatFor .= "IF(fcodesxx != \"\",fcodesxx,\"FORMA SIN DESCRIPCION\") AS fcodesxx ";
                $qDatFor .= "FROM $cAlfa.fpar0130 ";
                $qDatFor .= "WHERE ";
                $qDatFor .= "fcoidxxx = \"{$vForId[$n]}\" LIMIT 0,1";
                $xDatFor  = f_MySql("SELECT","",$qDatFor,$xConexion01,"");
                $vDatFor  = mysql_fetch_array($xDatFor);
                $vForCob[] = "{$vForId[$n]}";
                $mForCob["{$vForId[$n]}"] = $vDatFor;
              }
            }
          }
        }

        for ($n=0; $n<count($vForCob); $n++) {
          // Logica para determintar los campos dinamicos de las tarifas
          // se debe tener en cuenta que los indices de los campos no pueden contener caracteres especiales, solo el guion bajo es permitido
          $cCamResal = "";
          $cCamResal = $this->fnCamposFormaCobro($vForCob[$n], "", $vCamDin, $vCamDinNC20, $vCamDinNC40, $vCamDinVeh, $vCamDinN);

          $mForCob["{$vForCob[$n]}"]['camresal'] = $cCamResal;
        }

        // Ordenando campos para crearlos en la tabla temporal
        $vCamNew = array();
        $vCamNew =  $this->fnOrdenarCampos($vCamDin, $vCamDinNC20, $vCamDinNC40, $vCamDinVeh, $vCamDinN);
        
        for ($n=0; $n<count($vCamNew); $n++) {
          $vCampos['tablaxxx'] = $pArrayParametros['TABLAXXX'];
          $vCampos['camponew'] = $vCamNew[$n];
          $vCampos['camporef'] = ($n == 0) ? "MODO_TRANSPORTE" : $vCamNew[$n-1];
          $vCampos['camtipxx'] = "TEXT";
          $mRetCamDim = $objEstructuraTarifasFacturacion->fnCrearCampo($vCampos);
          if ($mRetCamDim[0] == "false") {
            $nSwitch = 1;
            for($nR=1;$nR<count($mRetCamDim);$nR++) {
              $vError['TIPOERRX'] = "ERROR";
              $vError['DESERROR'] = $mRetCamDim[$nR];
              $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
            }
          }
        }
      }

      if ($nSwitch == 0) {
        $nCanReg = 0;
        for ($nS=0; $nS<count($mServicios); $nS++) {

          $nCanReg++;
          if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = $objEstructuraTarifasFacturacion->fnReiniciarConexionDBRTarifasFacturacion($xConexion01); }

          // Por cada Forma de cobro asociada al concepto de cobro se crea un registro en la tabla temporal
          $vForId = explode("~",$mServicios[$nS]['fcoidxxx']);
          for ($n=0; $n<count($vForId); $n++) {
            if ($vForId[$n] != "") {
              // Buscando condiciones personalizadas
              $mData      = explode('|', $mServicios[$nS]['sercones']);
              $mCamConEsp = array();
              for ($nC=1; $nC < count($mData); $nC++) {
                if ($mData[$nC] != '') {
                  $vCondicion = explode('~', $mData[$nC]);
                  if ($vCondicion[0] == $vForId[$n]) {
                    for ($nA=1; $nA < count($vCondicion) ; $nA++) {
                      if (in_array("{$mServicios[$nS]['seridxxx']}~{$vForId[$n]}~{$vCondicion[$nA]}",$vConEsp) == false) {
                        $vConEsp[] = "{$mServicios[$nS]['seridxxx']}~{$vForId[$n]}~{$vCondicion[$nA]}";

                        $qCampos  = "SELECT dcedesxx ";
                        $qCampos .= "FROM $cAlfa.fpar0145 ";
                        $qCampos .= "WHERE ";
                        $qCampos .= "seridxxx = \"{$mServicios[$nS]['seridxxx']}\" AND ";
                        $qCampos .= "fcoidxxx = \"{$vForId[$n]}\" AND ";
                        $qCampos .= "dcecampo = \"{$vCondicion[$nA]}\" LIMIT 0,1 ";
                        $xCampos  = f_MySql("SELECT","",$qCampos,$xConexion01,"");
                        $vCampos = mysql_fetch_array($xCampos);
                        $mConEsp["{$mServicios[$nS]['seridxxx']}~{$vForId[$n]}~{$vCondicion[$nA]}"] = $vCampos;
                      }
                      $mCamConEsp["{$vCondicion[$nA]}"]['dcedesxx'] = $mConEsp["{$mServicios[$nS]['seridxxx']}~{$vForId[$n]}~{$vCondicion[$nA]}"]['dcedesxx']; // Descripcion Estandar
                      $mCamConEsp["{$vCondicion[$nA]}"]['dcedespx'] = ""; // Descripcion Personalizada
                    }
                  }
                }
              }

              $cCampEst = "";
              foreach ($mCamConEsp as $cKey => $cValue) {
                $cCampEst .= str_replace(","," ",$mCamConEsp[$cKey]['dcedesxx']).",";
              }
              $cCampEst = substr($cCampEst, 0, -1);

              $qInsert  = $qInsCab;
              $qInsert .= "(\"{$mServicios[$nS]['seridxxx']}\",";                             // Id del Concepto de Cobro',";
              $qInsert .= "\"{$mServicios[$nS]['serdesxx']}\",";                              // Descripcion del Concepto de Cobro',";
              $qInsert .= "\"{$mServicios[$nS]['serdespx']}\",";                              // Descripcion Personalizada del Concepto de Cobro',";
              $qInsert .= "\"{$mForCob["{$vForId[$n]}"]['fcoidxxx']}\",";                     // Id Forma de Cobro',";
              $qInsert .= "\"{$mForCob["{$vForId[$n]}"]['fcodesxx']}\",";                     // Descripcion Forma de Cobro',";
              $qInsert .= "\"COP\",";                                                         // Moneda',";
              $qInsert .= "\"$cCampEst\",";                                                   // Condicion Especial',";
              $qInsert .= "\"GENERAL\",";                                                     // Aplica Tarifa Por',";
              $qInsert .= "\"100\",";                                                         // Id Aplica Tarifa Por',";
              $qInsert .= "\"{$mServicios[$nS]['sertopxx']}\",";                              // Tipo Operación',";
              //Entre estos campos el reporte genera los campos dinamicos de las tarifas
              $qInsert .= "\"".substr($mForCob["{$vForId[$n]}"]['camresal'],0,-1)."\")"; // Columnas Resaltadas',";
              // echo $qInsert."<br>";
              $xInsert = mysql_query($qInsert,$xConexion01);
              if (!$xInsert) {
                $nSwitch = 1;
                $vError['TIPOERRX'] = "ERROR";
                $vError['DESERROR'] = "Error al Insertar en la tabla temporal.";
                $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
              }
            }
          }
        }

        if ($nSwitch == 0) {
          $vParametros = array();
          $vParametros['TABLAXXX'] = $pArrayParametros['TABLAXXX'];
          $vParametros['TABLAERR'] = $pArrayParametros['TABLAERR'];
          $vParametros['ORIGENXX'] = $pArrayParametros['ORIGENXX'];
          $mRespuesta = $this->fnGenerarReporteTarifasConsolidad($vParametros);

          // Retorna el nombre del archivo o guarda los errores generados
          if ($mRespuesta[0] == "true") {
            $mReturn[1] = $mRespuesta[1];
          } else {
            $nSwitch = 1;
            $vError['LINEAERR'] = __LINE__;
            $vError['TIPOERRX'] = "ERROR";
            $vError['DESERROR'] = "Error al generar los archivos";
            $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
          }
        }
      }

      if ($nSwitch == 0) {
        $mReturn[0] = "true";
      } else {
        $mReturn[0] = "false";
      }

      return $mReturn;
    }
    
    /**
     * Metodo para generar el archivo Excel del Reporte de Tarifas Consolidad. 
     */
    function fnGenerarReporteTarifasConsolidad($pArrayParametros){
      global $xConexion01; global $cAlfa; global $vSysStr; global $kUser; global $OPENINIT;

      /**
       * Recibe como Parametro un Vector con las siguientes posiciones:
       * $pArrayParametros['TABLAXXX'] // Nombre Tabla Temporal
       * $pArrayParametros['TABLAERR'] // Nombre Tabla Error
      */

      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var int
       */
      $nSwitch = 0;
  
      /**
       * Variable para hacer el retorno.
       * 
       * @var array
       */
      $mReturn[0] = "";

      /**
       * Variable para alamacenar errores.
       * 
       * @var array
       */
      $vError = array();
      $vError['TABLAERR'] = $pArrayParametros['TABLAERR'];

      /**
       * Instanciando Objeto para el Guardado de Errores.
       */
      $objEstructuraTarifasFacturacion = new cEstructurasTarfiasFacturacion();
  
      //validando que el nombre de la tabla Temporal no sea vacia
      if($pArrayParametros['TABLAXXX'] == ""){
        $nSwitch = 1;
        $vError['LINEAERR'] = __LINE__;
        $vError['TIPOERRX'] = "ERROR";
        $vError['DESERROR'] = "La Tabla Temporal del Reporte Tarifas Consolidado No puede ser Vacia.".mysql_error($xConexion01);
        $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
      }

      if($nSwitch == 0){
        // Consultando los registros de la tabla temporal para generar el Excel
        $qTabTem  = "SELECT * ";
        $qTabTem .= "FROM $cAlfa.{$pArrayParametros['TABLAXXX']}";
        $xTabTem  = f_MySql("SELECT", "", $qTabTem, $xConexion01, "");
        // echo $qTabTem."~".mysql_num_rows($xTabTem)."<br>";
        $nCanReg = 0;
        if (mysql_num_rows($xTabTem) > 0) {
          // Consulta las columnas de la tabla temporal para construir las columnas dinamicas
          $qCulumnTabTem  = "SELECT COLUMN_NAME ";
          $qCulumnTabTem .= "FROM INFORMATION_SCHEMA.COLUMNS where ";
          $qCulumnTabTem .= "TABLE_NAME = \"{$pArrayParametros['TABLAXXX']}\"";
          $xCulumnTabTem  = f_MySql("SELECT", "", $qCulumnTabTem, $xConexion01, "");
          // echo $qCulumnTabTem."~".mysql_num_rows($xCulumnTabTem)."<br>";

          if ($pArrayParametros['ORIGENXX'] == "REPORTE") {
            $mExcluirColumnas = ['LINEAIDX','COLUMNAS_RESALTADAS'];
            if ($vSysStr['system_control_vigencia_tarifas'] != "SI") {
              $mExcluirColumnas[] = 'VIGENCIA_DESDE';
              $mExcluirColumnas[] = 'VIGENCIA_HASTA';
              $mExcluirColumnas[] = 'NUEVA_VIGENCIA_DESDE';
              $mExcluirColumnas[] = 'NUEVA_VIGENCIA_HASTA';
              $mExcluirColumnas[] = 'FECHA_MODIFICACION_VIGENCIA';
              $mExcluirColumnas[] = 'ACTUALIZAR_VIGENCIA';
            }
          } else {
            // Descargar formato
            $mExcluirColumnas = ['LINEAIDX',
              'COLUMNAS_RESALTADAS',
              'ESTADO_CLIENTE_O_GRUPO',
              'ESTADO_TARIFA',
              'FECHA_MODIFICACION_VIGENCIA',
              'ID_USUARIO_CREACION',
              'NOMBRE_USUARIO_CREACION',
              'ID_USUARIO_MODIFICACION',
              'NOMBRE_USUARIO_MODIFICACION',
              'FECHA_CREACION',
              'FECHA_MODIFICACION'
            ];
          }

          $mColumnasReporte = [];
          if (mysql_num_rows($xCulumnTabTem) > 0) {
            while($xRCT = mysql_fetch_array($xCulumnTabTem)) {
              // Valida a partir de que columna se deben obtener las columnas que son dinamicas
              if (!in_array($xRCT[0], $mExcluirColumnas)) {
                $nInd_ColumnasReporte = count($mColumnasReporte);
                $mColumnasReporte[$nInd_ColumnasReporte] = $xRCT[0];
              }
            }
          }

          // Columnas en color rojo
          $vRojo = [
            'ESTADO_CLIENTE_O_GRUPO',
            'ESTADO_TARIFA',
            'FECHA_MODIFICACION_VIGENCIA',
            'ID_USUARIO_CREACION',
            'NOMBRE_USUARIO_CREACION',
            'ID_USUARIO_MODIFICACION',
            'NOMBRE_USUARIO_MODIFICACION',
            'FECHA_CREACION',
            'FECHA_MODIFICACION'
          ];

          // Columnas formatedas a la derecha
          $vForLeft = [
            'NIT_CLIENTE_O_ID_GRUPO',
            'NOMBRE_CLIENTE_O_DESCRIPCION_GRUPO',
            'DESCRIPCION_CONCEPTO_COBRO',
            'DESCRIPCION_PERSONALIZADA_CONCEPTO_COBRO',
            'DESCRIPCION_FORMA_COBRO',
            'CONDICION_ESPECIAL',
            'CONDICION_ESPECIAL_PERSONALIZADA',
            'APLICA_TARIFA_POR',
            'ID_APLICA_TARIFA_POR',
            'DESCRIPCION_APLICA_TARIFA_POR',
            'TIPO_OPERACION',
            'SUCURSALES',
            'MODO_TRANSPORTE',
            'ID_USUARIO_CREACION',
            'NOMBRE_USUARIO_CREACION',
            'ID_USUARIO_MODIFICACION',
            'NOMBRE_USUARIO_MODIFICACION'
          ];
          
          // Columnas formateadas al centro
          $vForCenter = [
            'APLICA_TARIFA_PARA',
            'ESTADO_CLIENTE_O_GRUPO',
            'ESTADO_TARIFA',
            'MONEDA',
            'VIGENCIA_DESDE',
            'VIGENCIA_HASTA',
            'NUEVA_VIGENCIA_DESDE',
            'NUEVA_VIGENCIA_HASTA',
            'FECHA_MODIFICACION_VIGENCIA',
            'ACTUALIZAR_VIGENCIA',
            'ID_CONCEPTO_COBRO',
            'ID_FORMA_COBRO',
            'FECHA_CREACION',
            'FECHA_MODIFICACION'
          ];

          // Columnas tipo fecha
          $vForDate = [
            'VIGENCIA_DESDE',
            'VIGENCIA_HASTA',
            'NUEVA_VIGENCIA_DESDE',
            'NUEVA_VIGENCIA_HASTA',
            'FECHA_MODIFICACION_VIGENCIA',
            'FECHA_CREACION',
            'FECHA_MODIFICACION'
          ];

          //Se crea en Downloads
          $cDirectorio = "{$OPENINIT['pathdr']}/opencomex/".$vSysStr['system_download_directory'];
          $cFile = (($pArrayParametros['ORIGENXX'] == "REPORTE") ? "REPORTE_TARIFAS_CONSOLIDADO_" : "FORMATO_TARIFAS_CONSOLIDADO_").$kUser."_".date('YmdHis').".xls";
          $cFileDownload = $cDirectorio."/".$cFile;

          // Borrando archivo si ya existe
          if (file_exists($cFileDownload)){
            unlink($cFileDownload);
          }

          $border = (new BorderBuilder())
              ->setBorderBottom(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
              ->setBorderLeft(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
              ->setBorderRight(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
              ->setBorderTop(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
              ->build();

          $defaultStyle = (new StyleBuilder())
            ->setBorder($border)
            ->setFontSize(10)
            ->build();

          $writer = WriterEntityFactory::createXLSXWriter();

          $writer ->setDefaultRowStyle($defaultStyle)
                  ->openToFile($cFileDownload);
          
          // Titulo Normal
          $cStyTitNor = (new Style())
            ->setShouldWrapText(true)
            ->setFontBold()
            ->setCellAlignment('center')
            ->setBackgroundColor(substr($vSysStr['system_row_title_color_ini'],1,strlen($vSysStr['system_row_title_color_ini'])));
          // Titulo Rojo
          $cStyTitRoj = (new Style())
            ->setShouldWrapText(true)
            ->setFontBold()
            ->setCellAlignment('center')
            ->setBackgroundColor('F7DFDF');
          // Texto Normal centrado
          $cStyNorCen = (new Style())
            ->setShouldWrapText(true)
            ->setCellAlignment('center');
          // Texto Normal Left
          $cStyNorLef = (new Style())
            ->setShouldWrapText(true)
            ->setCellAlignment('left');
          // Texto Normal right
          $cStyNorRig = (new Style())
            ->setShouldWrapText(true)
            ->setCellAlignment('right');
          // Texto Fodo Rojo centrado
          $cStyCenRoj = (new Style())
            ->setShouldWrapText(true)
            ->setCellAlignment('center')
            ->setBackgroundColor('F7DFDF');
          // Texto Fondo Left
          $cStyLefRoj = (new Style())
            ->setShouldWrapText(true)
            ->setCellAlignment('left')
            ->setBackgroundColor('F7DFDF');
          // Texto Fondo right
          $cStyRigRoj = (new Style())
            ->setShouldWrapText(true)
            ->setCellAlignment('right')
            ->setBackgroundColor('F7DFDF');
          // Texto Fodo gris Left
          $cStyLefGri = (new Style())
            ->setShouldWrapText(true)
            ->setCellAlignment('left')
            ->setBackgroundColor('E5E5E5');
          // Formato Fila
          $rowSty = (new StyleBuilder())
            ->setShouldWrapText(true)
            ->build();

          // titulos
          $cells = array();
          for ($n=0; $n < count($mColumnasReporte); $n++) {
            $cStyle = (in_array($mColumnasReporte[$n], $vRojo)) ? $cStyTitRoj : $cStyTitNor;
            $cells[] = WriterEntityFactory::createCell($mColumnasReporte[$n], $cStyle);
          }
          $row = WriterEntityFactory::createRow($cells, $rowSty);
          $writer->addRow($row);

          while($xRTT = mysql_fetch_array($xTabTem)){
            $nCanReg++;

            if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = $objEstructuraTarifasFacturacion->fnReiniciarConexionDBRTarifasFacturacion($xConexion01); }
            //Idenfiticando columnas que se deben resaltar
            $vCampos = explode("|", $xRTT['COLUMNAS_RESALTADAS']);
            $vCamRes = array();
            for($n=0; $n<count($vCampos); $n++) {
              if ($vCampos[$n] != "") {
                $vCam = explode("~", $vCampos[$n]);
                $vCamRes[] = "{$vCam[0]}";
              }
            }

            $cells = array();
            $nForNum = 0;
            for ($n=0; $n < count($mColumnasReporte); $n++) {
              $cStyle = $cStyNorLef; //Por defecto normal derecha
              if (in_array($mColumnasReporte[$n], $vCamRes)) { // Fondo Gris
                $cStyle = $cStyLefGri;
              } elseif (in_array($mColumnasReporte[$n], $vRojo)) { // Fondo Rojo
                if (in_array($mColumnasReporte[$n], $vForLeft)) {
                  $cStyle = $cStyLefRoj;
                } elseif (in_array($mColumnasReporte[$n], $vForCenter)) {
                  $cStyle = $cStyCenRoj;
                } else {
                  $cStyle = $cStyRigRoj;
                }
              } else { // Sin Fondo
                if (in_array($mColumnasReporte[$n], $vForLeft)) {
                  $cStyle = $cStyLefCen;
                } elseif (in_array($mColumnasReporte[$n], $vForCenter)) {
                  $cStyle = $cStyNorCen;
                } else {
                  $cStyle = $cStyNorRig;
                }
              }

              // Si el formato es fecha
              if (in_array($mColumnasReporte[$n], $vForDate)) {
                $xRTT[$mColumnasReporte[$n]] = ($xRTT[$mColumnasReporte[$n]] != "0000-00-00") ? $xRTT[$mColumnasReporte[$n]] : "";
              }
              // Si la columna es posterior a modo transporte se debe dar formato numerico
              if ($mColumnasReporte[$n] == "MODO_TRANSPORTE") {
                $nForNum = 1;
              }
              if ($nForNum == 1 && $mColumnasReporte[$n] != "MODO_TRANSPORTE") {
                $xRTT[$mColumnasReporte[$n]] = (substr($xRTT[$mColumnasReporte[$n]], 0, 1) == ".") ? "0".$xRTT[$mColumnasReporte[$n]] : $xRTT[$mColumnasReporte[$n]];
              }

              $xRTT[$mColumnasReporte[$n]] = mb_convert_encoding($xRTT[$mColumnasReporte[$n]], 'UTF-8');

              $cells[] = WriterEntityFactory::createCell($xRTT[$mColumnasReporte[$n]], $cStyle);
            }
            $row = WriterEntityFactory::createRow($cells, $rowSty);
            $writer->addRow($row);
          }

          $writer->close();
        } else {
          $nSwitch = 1;
          $vError['LINEAERR'] = __LINE__;
          $vError['TIPOERRX'] = "ERROR";
          $vError['DESERROR'] = "No se encontraron resgitros.<br>".mysql_error($xConexion01);
          $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
        }

        if(!file_exists($cFileDownload)) {
          $nSwitch = 1;
          $vError['LINEAERR'] = __LINE__;
          $vError['TIPOERRX'] = "ERROR";
          $vError['DESERROR'] = "Error al Generar Archivo Excel.";
          $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
        }
      }
      
      if($nSwitch == 0){
        // chmod($cFileDownload, intval($vSysStr['system_permisos_archivos'], 8));
        // $cDownLoadFilename = $cDownLoadFilename !== null ? $cDownLoadFilename : basename($cFileDownload);

        // header('Content-Description: File Transfer');
        // header('Content-Type: application/octet-stream');
        // header('Content-Disposition: attachment; filename=' . $cDownLoadFilename);
        // header('Content-Transfer-Encoding: binary');
        // header('Expires: 0');
        // header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        // header('Pragma: public');
        // header('Content-Length: ' . filesize($cFileDownload));
        
        // ob_clean();
        // flush();
        // readfile($cFileDownload);
      
        $mReturn[0] = "true";
        $mReturn[1] = $cFile;
        $mReturn[2] = $cFileDownload;
      }else{
        $mReturn[0] = "false";
      }

      return $mReturn;
    }##function fnGenerarReporteTarifasConsolidad($pArrayParametros){##  

    /**
     * Metodo para Crear o Actualizar Tarifas
     */
    function fnCrearTarifas($pArrayParametros) {

      global $xConexion01; global $cAlfa; global $vSysStr; global $kUser;

      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var int
       */
      $nSwitch = 0;

      /**
       * Variable para hacer el retorno.
       * 
       * @var array
       */
      $mReturn    = array();
      $mReturn[0] = "";

      /**
       * Variable para alamacenar errores.
       * 
       * @var array
       */
      $vError = array();
      $vError['TABLAXXX'] = $pArrayParametros['TABLAXXX'];
      $vError['TABLAERR'] = $pArrayParametros['TABLAERR'];

      /**
       * Vectores para reemplazar caracteres de salto de linea y tabuladores
       */
      $vBuscar = array(chr(13),chr(10),chr(27),chr(9));
      $vReempl = array(" "," "," "," ");

      /**
       * Instanciando Objeto para el Guardado de Errores.
       */
      $objEstructuraTarifasFacturacion = new cEstructurasTarfiasFacturacion();

      // Array con informacion de parametricas
      $vServicios = array();
      $mServicios = array();

      $vConEsp = array();
      $mConEsp = array();

      // Consultado la tabla temporal para obtener cada una de las tarifas que se deben actualizar o crear
      $qTarifas  = "SELECT *";
      $qTarifas .= "FROM $cAlfa.{$pArrayParametros['TABLAXXX']} ";
      $xTarifas = f_MySql("SELECT", "", $qTarifas, $xConexion01, "");
      // echo $qTarifas."~".mysql_num_rows($xTarifas)."<br>";
      $nCanReg = 0;
      while ($xRT = mysql_fetch_assoc($xTarifas)) {
        //Inicializando variable de error
        $nSwErr = 0;

        $nCanReg++;
        if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = $objEstructuraTarifasFacturacion->fnReiniciarConexionDBRTarifasFacturacion($xConexion01); }

        // Eliminando espacios en blanco al inicio y al final de cada campo
        foreach ($xRT as $cKey => $cValue) {
          $xRT[$cKey] = trim($cValue);
        }
  
        // Cadena de Sucursales
        $mSucId  = explode(",",trim($xRT['SUCURSALES'],'"'));
        $cCadSuc = "";
        for($i=0;$i<count($mSucId);$i++){
          $cCadSuc .= "sucidxxx LIKE \"%{$mSucId[$i]}%\" OR ";
        }
        $cCadSuc = substr($cCadSuc,0,strlen($cCadSuc)-3);
  
        // Cadena de Medio Transporte
        $mFcoMtr    = explode(",",trim($xRT['MODO_TRANSPORTE'],'"'));
        $cCadFcoMtr = "";
        for($i=0;$i<count($mFcoMtr);$i++){
          $cCadFcoMtr .= "fcomtrxx LIKE \"%{$mFcoMtr[$i]}%\" OR ";
        }
        $cCadFcoMtr = substr($cCadFcoMtr,0,strlen($cCadFcoMtr)-3);

        //Buscando el Id de la Tarifa ya existe para actualizarlo en la tabla temporal
        $qSql131  = "SELECT taridxxx, pucrftex, pucaftex ";
        $qSql131 .= "FROM $cAlfa.fpar0131 ";
        $qSql131 .= "WHERE ";
        $qSql131 .= "cliidxxx = \"{$xRT['NIT_CLIENTE_O_ID_GRUPO']}\" AND ";
        $qSql131 .= "seridxxx = \"{$xRT['ID_CONCEPTO_COBRO']}\" AND ";
        $qSql131 .=	"fcoidxxx = \"{$xRT['ID_FORMA_COBRO']}\" AND ";
        $qSql131 .= "fcotptxx = \"{$xRT['APLICA_TARIFA_POR']}\" AND ";
        $qSql131 .=	"fcotpixx = \"{$xRT['ID_APLICA_TARIFA_POR']}\" AND ";
        $qSql131 .= "($cCadSuc) AND ";
        $qSql131 .=	"fcotopxx = \"{$xRT['TIPO_OPERACION']}\" AND ";
        $qSql131 .= "($cCadFcoMtr) AND ";
        $qSql131 .= "tartipxx = \"{$xRT['APLICA_TARIFA_PARA']}\" AND ";
        if ($vSysStr['system_control_vigencia_tarifas'] == "SI") {
          $qSql131 .= "tarfevde = \"{$xRT['VIGENCIA_DESDE']}\" AND ";
          $qSql131 .= "tarfevha = \"{$xRT['VIGENCIA_HASTA']}\" AND ";
        }
        $qSql131 .= "regestxx != \"INACTIVO\"";
        $xSql131  = f_MySql("SELECT","",$qSql131,$xConexion01,"");
        // echo "<br>".$qSql131."~".mysql_num_rows($xSql131)."<br><br>";
        // f_Mensaje(__FILE__,__LINE__,$qSql131."~".mysql_num_rows($xSql131));
        if (mysql_num_rows($xSql131) > 1) {
          $nSwErr = 1;
          $vError['TIPOERRX'] = "ERROR";
          $vError['LINEAERR'] = $xRT['LINEAIDX'];
          if ($vSysStr['system_control_vigencia_tarifas'] == "SI") {
            $vError['DESERROR'] = "Existe Mas de Dos Tarifas Activas para el Cliente, Servicio, Forma de Cobro, Tarifa, Sucursal, Medio de Transporte y Vigencia.";
          } else {
            $vError['DESERROR'] = "Existe Mas de Dos Tarifas Activas para el Cliente, Servicio, Forma de Cobro, Tarifa, Sucursal y Medio de Transporte.";
          }
          $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
        }

        if ($xRT['ACTUALIZAR_VIGENCIA'] == "SI" && mysql_num_rows($xSql131) == 0) {
          $nSwErr = 1;
          $vError['TIPOERRX'] = "ERROR";
          $vError['LINEAERR'] = $xRT['LINEAIDX'];
          $vError['DESERROR'] = "La columna ACTUALIZAR_VIGENCIA tiene valor SI, y la tarifa que desea actualizar no existe en estado ACTIVO.";
          $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
        }

        $nTarId    = "";
        $cPucRfte  = "";
        $cPucARfte = "";
        if (mysql_num_rows($xSql131) == 1) {
          $vR131 = mysql_fetch_array($xSql131);
          $nTarId    = $vR131['taridxxx'];
          $cPucRfte  = $vR131['pucrftex'];
          $cPucARfte = $vR131['pucaftex'];
          $qUpdate  = "UPDATE $cAlfa.{$pArrayParametros['TABLAXXX']} ";
          $qUpdate .= "SET ID_TARIFA = \"{$vR131['taridxxx']}\" ";
          $qUpdate .= "WHERE ";
          $qUpdate .= "LINEAIDX = \"{$xRT['LINEAIDX']}\"";
          $xUpdate = mysql_query($qUpdate,$xConexion01);
          if (!$xUpdate) {
            $nSwErr = 1;
            $vError['TIPOERRX'] = "ERROR";
            $vError['LINEAERR'] = $xRT['LINEAIDX'];
            $vError['DESERROR'] = "Error al Actualizar en la tabla temporal el Id de la Tarifa.";
            $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
          }
        }

        if ($nSwErr == 0) {
          // Creando Vector con datos para enviar a guardar la tarifa
          unset($mDatos);
          $mDatos = array();
          $mDatos['cOrigen']    = "REPORTE";
          $mDatos['kModo']      =	($nTarId == "") ? "NUEVO" : "EDITAR";  // Modo de grabado (NUEVO,ANTERIOR,EDITAR,BORRAR,LEGALIZAR)
          $mDatos['kUsrId']     =	$kUser;	// Usuario que esta grabando
          $mDatos['cTarId']     = $nTarId;
          $mDatos['cTarTip']    = $xRT['APLICA_TARIFA_PARA'];
          $mDatos['cCliId']     = $xRT['NIT_CLIENTE_O_ID_GRUPO'];
          $mDatos['cSerId']     = $xRT['ID_CONCEPTO_COBRO'];
          $mDatos['cSerDesPc']  = $xRT['DESCRIPCION_PERSONALIZADA_CONCEPTO_COBRO'];
          $mDatos['cFcoId']     = $xRT['ID_FORMA_COBRO'];
          $mDatos['cPucRfte']   = $cPucRfte;
          $mDatos['cPucARfte']  = $cPucARfte;
          $mDatos['cAplicaTar'] = ($xRT['APLICA_TARIFA_POR'] != "GENERAL") ? "ESPECIFICO" : "";
          $mDatos['cFcoTpt']    = $xRT['APLICA_TARIFA_POR'];
          $mDatos['cFcoTpi']    = $xRT['ID_APLICA_TARIFA_POR'];
          $mDatos['cMonId']     = $xRT['MONEDA'];
          $mDatos['dTarFevDe']  = "";
          $mDatos['dTarFevHa']  = "";
          if ($vSysStr['system_control_vigencia_tarifas'] == "SI") {
            if ($xRT['ACTUALIZAR_VIGENCIA'] == "SI") {
              // Fecha Nueva
              $mDatos['dTarFevDe']    = $xRT['NUEVA_VIGENCIA_DESDE'];
              $mDatos['dTarFevHa']    = $xRT['NUEVA_VIGENCIA_HASTA'];
              // Fecha Anterior
              $mDatos['dTarFevDeAnt'] = $xRT['VIGENCIA_DESDE'];
              $mDatos['dTarFevHaAnt'] = $xRT['VIGENCIA_HASTA'];
            } else {
              $mDatos['dTarFevDe']    = $xRT['VIGENCIA_DESDE'];
              $mDatos['dTarFevHa']    = $xRT['VIGENCIA_HASTA'];
              if ($nTarId == "") { // Nuevo
                $mDatos['dTarFevDeAnt'] = "";
                $mDatos['dTarFevHaAnt'] = "";
              } else { // Editar
                $mDatos['dTarFevDeAnt'] = $xRT['VIGENCIA_DESDE'];
                $mDatos['dTarFevHaAnt'] = $xRT['VIGENCIA_HASTA'];
              }
            }
          }
          $mDatos['cEstado'] = "ACTIVO";

          // Busco Sucursales en la fpar0008
          $qSuc008 = "SELECT sucidxxx FROM $cAlfa.fpar0008 WHERE regestxx = \"ACTIVO\" ORDER BY sucdesxx";
          $xSuc008  = f_MySql("SELECT","",$qSuc008,$xConexion01,"");
          $mDatos['cSucursales'] = "";
          while ($xRSuc = mysql_fetch_array($xSuc008)) {
            $mDatos['cSucursales'] .= "{$xRSuc['sucidxxx']}~";
          }
          $mDatos['cSucursales'] = substr($mDatos['cSucursales'], 0, -1);

          // Armando la Cadena de Sucursales
          $mSucId  = explode(",",trim($xRT['SUCURSALES'],'"'));
          for($i=0;$i<count($mSucId);$i++){
            if ($mSucId[$i] != "") {
              $mDatos['c'.$mSucId[$i]] = $mSucId[$i];
            }
          }

          // Validando Sucursales
          for($i=0;$i<count($mSucId);$i++){
            $qSuc008  = "SELECT sucidxxx ";
            $qSuc008 .= "FROM $cAlfa.fpar0008 ";
            $qSuc008 .= "WHERE ";
            $qSuc008 .= "sucidxxx = \"{$mSucId[$i]}\" AND ";
            $qSuc008 .= "regestxx = \"ACTIVO\"";
            $xSuc008  = f_MySql("SELECT","",$qSuc008,$xConexion01,"");
            if (mysql_num_rows($xSuc008) == 0) {
              $nSwErr = 1;
              $vError['TIPOERRX'] = "ERROR";
              $vError['LINEAERR'] = $xRT['LINEAIDX'];
              $vError['DESERROR'] = "La Sucursal [{$mSucId[$i]}] No Existe o esta Inactiva.";
              $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
            }
          }

          // Validando tipo de operacion
          $mDatos['cSerTop'] = $xRT['TIPO_OPERACION'];

          if ($mDatos['cSerTop'] == "IMPORTACION") {
            if (substr($mDatos['cSerId'], 0, 1) != "1") {
              $nSwErr = 1;
              $vError['TIPOERRX'] = "ERROR";
              $vError['LINEAERR'] = $xRT['LINEAIDX'];
              $vError['DESERROR'] = "El Id de Concepto de Cobro no corresponde al Tipo de Operacion [".$mDatos['cSerTop']."].";
              $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
            }
          } elseif ($mDatos['cSerTop'] == "EXPORTACION") {
            if (substr($mDatos['cSerId'], 0, 1) != "2") {
              $nSwErr = 1;
              $vError['TIPOERRX'] = "ERROR";
              $vError['LINEAERR'] = $xRT['LINEAIDX'];
              $vError['DESERROR'] = "El Id de Concepto de Cobro no corresponde al Tipo de Operacion [".$mDatos['cSerTop']."].";
              $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
            }
          } elseif ($mDatos['cSerTop'] == "TRANSITO") {
            if (substr($mDatos['cSerId'], 0, 1) != "3") {
              $nSwErr = 1;
              $vError['TIPOERRX'] = "ERROR";
              $vError['LINEAERR'] = $xRT['LINEAIDX'];
              $vError['DESERROR'] = "El Id de Concepto de Cobro no corresponde al Tipo de Operacion [".$mDatos['cSerTop']."].";
              $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
            }
          } elseif ($mDatos['cSerTop'] == "OTROS") {
            if (substr($mDatos['cSerId'], 0, 1) != "4") {
              $nSwErr = 1;
              $vError['TIPOERRX'] = "ERROR";
              $vError['LINEAERR'] = $xRT['LINEAIDX'];
              $vError['DESERROR'] = "El Id de Concepto de Cobro no corresponde al Tipo de Operacion [".$mDatos['cSerTop']."].";
              $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
            }
          } elseif ($mDatos['cSerTop'] == "REGSITRO") {
            if (substr($mDatos['cSerId'], 0, 1) != "5") {
              $nSwErr = 1;
              $vError['TIPOERRX'] = "ERROR";
              $vError['LINEAERR'] = $xRT['LINEAIDX'];
              $vError['DESERROR'] = "El Id de Concepto de Cobro no corresponde al Tipo de Operacion [".$mDatos['cSerTop']."].";
              $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
            }
          } else {
            $nSwErr = 1;
            $vError['TIPOERRX'] = "ERROR";
            $vError['LINEAERR'] = $xRT['LINEAIDX'];
            $vError['DESERROR'] = "Tipo de Operacion No Valido.";
            $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
          }

          $vModTra = explode(",",trim($xRT['MODO_TRANSPORTE'],'"'));
          $mDatos['cAereo']     = "";
          $mDatos['cMaritimo']  = "";
          $mDatos['cTerrestre'] = "";
          $nErrTra = 0;
          $cErrTra = "";
          for($i=0;$i<count($vModTra);$i++){
            if ($vModTra[$i] == "AEREO") {
              $mDatos['cAereo'] = $vModTra[$i];
            } elseif ($vModTra[$i] == "MARITIMO") {
              $mDatos['cMaritimo'] = $vModTra[$i];
            } elseif ($vModTra[$i] == "TERRESTRE") {
              $mDatos['cTerrestre'] = $vModTra[$i];
            } elseif ($vModTra[$i] != "") {
              $nErrTra = 1;
              $cErrTra .= "{$vModTra[$i]}, ";
            }
          }

          if ($nErrTra == 1) {
            $nSwErr = 1;
            $vError['TIPOERRX'] = "ERROR";
            $vError['LINEAERR'] = $xRT['LINEAIDX'];
            $vError['DESERROR'] = "Modo(s) de Transporte [".substr($cErrTra, 0, -2)."] No Valido(s).";
            $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
          }

          // Buscando Informacion Servicios
          if (in_array($xRT['ID_CONCEPTO_COBRO'],$vServicios) == false) {
            $qDatSer  = "SELECT seridxxx, ";
            $qDatSer .= "IF(serdesxx != \"\",serdesxx,\"CONCEPTO SIN DESCRIPCION\") AS serdesxx, ";
            $qDatSer .= "IF(serdespx != \"\",serdespx,serdesxx) AS serdespx, ";
            $qDatSer .= "sercones ";
            $qDatSer .= "FROM $cAlfa.fpar0129 ";
            $qDatSer .= "WHERE ";
            $qDatSer .= "seridxxx = \"{$xRT['ID_CONCEPTO_COBRO']}\" LIMIT 0,1";
            $xDatSer  = f_MySql("SELECT","",$qDatSer,$xConexion01,"");
            $vDatSer  = mysql_fetch_array($xDatSer);
            $vServicios[] = "{$xRT['ID_CONCEPTO_COBRO']}";
            $mServicios["{$xRT['ID_CONCEPTO_COBRO']}"] = $vDatSer;
          }

          // Buscando condiciones personalizadas
          $mData      = explode('|', $mServicios["{$xRT['ID_CONCEPTO_COBRO']}"]['sercones']);
          $mCamConEsp = array();
          for ($nC=1; $nC < count($mData); $nC++) {
            if ($mData[$nC] != '') {
              $vCondicion = explode('~', $mData[$nC]);
              if ($vCondicion[0] == $xRT['ID_FORMA_COBRO']) {
                for ($nA=1; $nA < count($vCondicion) ; $nA++) {
                  if (in_array("{$xRT['ID_CONCEPTO_COBRO']}~{$xRT['ID_FORMA_COBRO']}~{$vCondicion[$nA]}",$vConEsp) == false) {
                    $vConEsp[] = "{$xRT['ID_CONCEPTO_COBRO']}~{$xRT['ID_FORMA_COBRO']}~{$vCondicion[$nA]}";

                    $qCampos  = "SELECT dcedesxx ";
                    $qCampos .= "FROM $cAlfa.fpar0145 ";
                    $qCampos .= "WHERE ";
                    $qCampos .= "seridxxx = \"{$xRT['ID_CONCEPTO_COBRO']}\" AND ";
                    $qCampos .= "fcoidxxx = \"{$xRT['ID_FORMA_COBRO']}\" AND ";
                    $qCampos .= "dcecampo = \"{$vCondicion[$nA]}\" LIMIT 0,1 ";
                    $xCampos  = f_MySql("SELECT","",$qCampos,$xConexion01,"");
                    $vCampos = mysql_fetch_array($xCampos);
                    $mConEsp["{$xRT['ID_CONCEPTO_COBRO']}~{$xRT['ID_FORMA_COBRO']}~{$vCondicion[$nA]}"] = $vCampos;
                  }
                  $nInd_mCamConEsp = count($mCamConEsp);
                  $mCamConEsp[$nInd_mCamConEsp]['dcecampo'] = $vCondicion[$nA];
                  $mCamConEsp[$nInd_mCamConEsp]['dcedesxx'] = $mConEsp["{$xRT['ID_CONCEPTO_COBRO']}~{$xRT['ID_FORMA_COBRO']}~{$vCondicion[$nA]}"]['dcedesxx'];
                }
              }
            }
          }

          // Si el servicio y forma de cobro tienen condiciones especiales personalizadas
          if (count($mCamConEsp) > 0) {
            $mConEspExc = explode(",",trim($xRT['CONDICION_ESPECIAL'],'"'));
            $nError = 0;
            for ($nC=0; $nC < count($mConEspExc); $nC++) {
              if ($mConEspExc[$nC] != $mCamConEsp[$nC]['dcedesxx']) {
                $nError = 1;
              }
            }

            if ($nError == 1) {
              $nSwErr = 1;
              $vError['TIPOERRX'] = "ERROR";
              $vError['LINEAERR'] = $xRT['LINEAIDX'];
              $vError['DESERROR'] = "Las Condiciones Especiales Personalizadas no Coinciden con las Parametricas.";
              $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
            }

            // El campo CONDICION_ESPECIAL_PERSONALIZADA debe ser menor o igual al campo CONDICION_ESPECIAL en numero de posiciones
            $mConEspPer = explode(",",trim($xRT['CONDICION_ESPECIAL_PERSONALIZADA'],'"'));
            for ($nC=0; $nC < count($mConEspPer); $nC++) {
              $mDatos['cNueCesp']["{$mCamConEsp[$nC]['dcecampo']}"] = $mConEspPer[$nC];
            }
          }
        }

        // Validando Campos numericos
        $nValidar = 0;
        foreach ($xRT as $cKey => $cValue) {
          if ($cKey == "MODO_TRANSPORTE") {
            $nValidar = 1;
          }
          if ($nValidar == 1 && $cKey != "MODO_TRANSPORTE") {
            $nCanDec = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? 2 : 10;
            if(!preg_match_all("/^[0-9]+([.][0-9]{0,$nCanDec})?$/", $cValue) && $cValue != "") {
              $nSwErr = 1;
              $vError['TIPOERRX'] = "ERROR";
              $vError['LINEAERR'] = $xRT['LINEAIDX'];
              $vError['DESERROR'] = "La columna [$cKey] debe ser numerica, sin separador de miles, separador de decimales punto (.) y puede contener maximo $nCanDec decimales.";
              $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
            }
          }
        }

        if ($nSwErr == 0) {
          /***** Validacion de Campos de las Diferentes Formas de Pago *****/
          /***** Validacion de Campos de las Diferentes Formas de Pago *****/
          /***** Validacion de Campos de las Diferentes Formas de Pago *****/
    
          switch ($mDatos['cFcoId']) {
    
            case "100": // VALOR FIJO
            case "200":
            case "300":
            case "400":
            case "500":
            case "127": //INTERVALOS X MTRS3
            case "174": //POR METRO CUADRADO POR DIAS EN BODEGA
            case "1122": //VALOR FIJO X HOJA PRINCIPAL
            case "1143": //VALOR FIJO EN USD
            case "1144": //VALOR FIJO POR ÍTEM EN USD
            case "250": //POR METRO CUADRADO POR DIAS EN BODEGA
            case "251": //INTERVALOS X MTRS3
            case "146":
              if ($mDatos['cFcoId'] == "1143" || $mDatos['cFcoId'] == "1144") {
                $mDatos['nVFi100'] = $xRT['VALOR_FIJO_EN_USD'];
              } else {
                $mDatos['nVFi100'] = $xRT['VALOR_FIJO'];
              }
            break;
    
            case "101": // PORCENTAJE VALOR CIF O UNA MINIMA
            case "126": // PORCENTAJE VALOR CIF O UNA MINIMA
              $cCamMin = ($mDatos['cFcoId'] == '101' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MINIMA_GENERAL_CIF" : "MINIMA";
              $mDatos['nPor101'] = $xRT['PORCENTAJE_CIF'];
              $mDatos['nMin101'] = $xRT[$cCamMin];
            break;
    
            case "102": // VAOR FIJO x UNIDADES
            case "201":
            case "301":
            case "502":
            case "150":
            case "152":
            case "163":
            case "240":
            case "401":
              $mDatos['nVFU102'] = $xRT['VALOR_FIJO_POR_UNIDAD'];
            break;
    
            case "103": // VALOR x HORAS CON MINIMA
            case "202":
            case "307":
              $cCamVal = ($mDatos['cFcoId'] == '103' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "VALOR_POR_HORA" : "VALOR";
              $mDatos['nVlr103'] = $xRT[$cCamVal];
              $mDatos['nHor103'] = $xRT['HORAS'];
              $mDatos['nAdi103'] = $xRT['VALOR_ADICIONAL'];
            break;
    
            case "104": // COBRO VARIABLES SEGUN UNIDADES
            case "204":
            case "303":
            case "313": // COBRO VARIABLES SEGUN UNIDADES (CANTIDAD)
            case "504":
              $mDatos['nIni104'] = $xRT['TARIFA_INICIAL'];
              $mDatos['nUni104'] = $xRT['UNIDADES_INICIALES'];
              $mDatos['nAdi104'] = $xRT['TRARIFA_DESPUES_DE_INICIALES'];
            break;
    
            case "105": // VALOR CIF DIVIDIDO EN PESOS
              $mDatos['nVPa105'] = $xRT['VALOR_PARCIAL'];
              $mDatos['nVFC105'] = $xRT['VALOR_FIJO_DE_COBRO_POR_PRIMER_PARCIAL'];
              $mDatos['nVCA105'] = $xRT['PORCENTAJE_DE_COBRO_VALOR_CIF_ADICIONAL'];
            break;
    
            case "106": // PORCENTAJE VALOR CIF O MINIMA VARIABLE POR CANTIDAD DE DECLARACIONES DE IMPORTACION
              $mDatos['nPor106'] = $xRT['PORCENTAJE_CIF'];
              $mDatos['nNiv106'] = $xRT['NIVELES'];
              
              for($i=1; $i<=$mDatos['nNiv106']; $i++) {
                $mDatos['vTarNiC'.$i] = $xRT['NIVELES_'.$i.'_DECLARACIONES'];
                $mDatos['vTarNiV'.$i] = $xRT['NIVELES_'.$i.'_VALOR_POR_UNIDAD'];
              }
            break;
    
            case "159": // Cobro Escalonado sobre la cantidad de Toneladas por un porcentaje del valor CIF con una minima
            case "107": // COBRO ESCALONADO SOBRE VALOR CIF EN USD
            case "1138": // COBRO ESCALONADO SOBRE VALOR CIF CON MINIMA

              $cCamMin = ($mDatos['cFcoId'] == '107' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MINIMA_GENERAL_CIF" : "MINIMA";
              $cCamNiv = ($mDatos['cFcoId'] == '107' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "NIVELES_CANT_VEHICULOS" : "NIVELES";
              $mDatos['nMin107'] = $xRT[$cCamMin];
              $mDatos['nNiv107'] = $xRT[$cCamNiv];

              for($i=1; $i<=$mDatos['nNiv107']; $i++) {
                $mDatos['vTarLiIn'.$i] = $xRT[$cCamNiv.'_'.$i.'_LIMITE_INFERIOR'];
                $mDatos['vTarLiSu'.$i] = $xRT[$cCamNiv.'_'.$i.'_LIMITE_SUPERIOR'];
                $mDatos['vTarPor' .$i] = $xRT[$cCamNiv.'_'.$i.'_PORCENTAJE'];
              }
            break;
    
            case "108": // COBRO ESCALONADO SOBRE VALOR CIF EN COP
              $cCamMin = ($mDatos['cFcoId'] == '108' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MINIMA_GENERAL_CIF" : "MINIMA";
              $cCamNiv = ($mDatos['cFcoId'] == '108' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "NIVELES_CANT_VEHICULOS" : "NIVELES";
    
              $mDatos['nMin108'] = $xRT[$cCamMin];
              $mDatos['nNiv108'] = $xRT[$cCamNiv];

              for($i=1; $i<=$mDatos['nNiv108']; $i++) {
                $mDatos['vTarLiIn'.$i] = $xRT[$cCamNiv.'_'.$i.'_LIMITE_INFERIOR'];
                $mDatos['vTarLiSu'.$i] = $xRT[$cCamNiv.'_'.$i.'_LIMITE_SUPERIOR'];
                $mDatos['vTarPor' .$i] = $xRT[$cCamNiv.'_'.$i.'_PORCENTAJE'];
              }
            break;
    
            case "109": // COBRO POR UNIDAD DE CARGA (CONTENEDORES)
            case "207":
            case "305":
            case "134": // COBRO POR UNIDAD DE CARGA (UNIDADES CARGA SUELTA O CONTENEDORES) O MINIMA %CIF
            case "234": // COBRO POR UNIDAD DE CARGA (UNIDADES CARGA SUELTA O CONTENEDORES) O MINIMA %FOB
              if (substr($mDatos['cFcoId'], 0, 2) == "2") {
                $mDatos['nPor109'] = $xRT['PORCENTAJE_FOB_PARA_GRANEL'];
              } else {
                $mDatos['nPor109'] = $xRT['PORCENTAJE_CIF_PARA_GRANEL'];
              }
              $mDatos['nC20109'] = $xRT['CONTENEDOR_20'];
              $mDatos['nC40109'] = $xRT['CONTENEDOR_40'];
              $mDatos['nCaS109'] = $xRT['CARGA_SUELTA'];
            break;
    
            case "110": // COBRO ESCALONADO POR CANTIDAD DE CONTENEDORES $ COP IMPORTACION
            case "208":
              $mDatos['nCaS110'] = $xRT['CARGA_SUELTA'];
              $mDatos['nNiv110'] = $xRT['NIVELES'];
    
              for($i=1; $i<=$mDatos['nNiv110']; $i++) {
                $mDatos['vTarLiIn'.$i] = $xRT['NIVELES_'.$i.'_NIVEL_INFERIOR'];
                $mDatos['vTarLiSu'.$i] = $xRT['NIVELES_'.$i.'_NIVEL_SUPERIOR'];
                $mDatos['vTarPor' .$i] = $xRT['NIVELES_'.$i.'_VALOR_COP'];
              }
            break;
    
            case "111": // MINIMA O COBRO VARIABLE SEGUN UNIDADES (PIEZAS)
              $mDatos['nVlr111'] = $xRT['CARGA_SUELTA'];
              $mDatos['nPie111'] = $xRT['PIEZAS'];
              $mDatos['nAdi111'] = $xRT['VALOR_ADICIONAL_POR_PIEZA'];
            break;
    
            case "112": // COBRO POR UNIDAD DE CARGA (UNIDADES CARGA SUELTA O CONTENEDORES)
            case "1117": // COBRO POR UNIDAD DE CARGA + APOYO ARCHIVO
              // Cobro por Unidad de Carga por Contenedores
              $mDatos['nC20112'] = $xRT['CONTENEDOR_20'];
              $mDatos['nC40112'] = $xRT['CONTENEDOR_40'];
              $mDatos['nCaS112'] = $xRT['CARGA_SUELTA'];
            break;
    
            case "113": // VALOR POR INCREMENTO DE TARIFA MINIMA
              $mDatos['nPor113'] = $xRT['PORCENTAJE_CIF'];
              $mDatos['nMin113'] = $xRT['MININA_NORMAL'];
              $mDatos['nInc113'] = $xRT['MINIMA_INCREMENTADA'];
            break;
    
            case "114": // VALOR FIJO O UNA MINIMA
              $cCamMin = ($mDatos['cFcoId'] == '114' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MINIMA_GENERAL_CIF" : "MINIMA";
              $cCamVal = ($mDatos['cFcoId'] == '114' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "VALOR_POR_UNIDAD_TONELADAS" : "VALOR_POR_UNIDAD";
              $mDatos['nVFi114'] = $xRT[$cCamVal];
              $mDatos['nMin114'] = $xRT[$cCamMin];
            break;
    
            case "115": // VALOR POR CANTIDAD Y TIPO DE CONTENEDOR
              $mDatos['nC20115']   = $xRT['CONTENEDOR_20'];
              $mDatos['nC40115']   = $xRT['CONTENEDOR_40'];
              $mDatos['nC40HC115'] = $xRT['CONTENEDOR_40_HC'];
            break;
    
            case "147":
            case "257":
            case "258":
            case "188":
            case "116":  // COBRO ESCALONADO POR UNIDAD
            case "1139": // COBRO ESCALONADO POR UNIDAD
            case "1140": // COBRO ESCALONADO POR UNIDAD
            case "1141": // COBRO ESCALONADO POR UNIDAD
            case "1145": // COBRO ESCALONADO VALOR POR UNIDAD
              $cCamNiv = (($mDatos['cFcoId'] == '116' || $mDatos['cFcoId'] == '188' || $mDatos['cFcoId'] == '257' || $mDatos['cFcoId'] == '258') && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "NIVELES_CANT_VEHICULOS" : "NIVELES";
              $mDatos['nNiv116'] = $xRT[$cCamNiv];

              for($i=1; $i<=$mDatos['nNiv116']; $i++) {
                $mDatos['vTarLiIn'.$i] = $xRT[$cCamNiv.'_'.$i.'_LIMITE_INFERIOR'];
                $mDatos['vTarLiSu'.$i] = $xRT[$cCamNiv.'_'.$i.'_LIMITE_SUPERIOR'];
                $mDatos['vTarPor' .$i] = $xRT[$cCamNiv.'_'.$i.'_VALOR_POR_UNIDAD'];
              }
            break;
    
            case "117": // COBRO ESCALONADO POR UNIDAD CONSOLIDADO
              $mDatos['nNiv117'] = $xRT['NIVELES'];
              
              for($i=1; $i<=$mDatos['nNiv117']; $i++) {
                $mDatos['vTarLiIn'.$i] = $xRT['NIVELES_'.$i.'_LIMITE_INFERIOR'];
                $mDatos['vTarLiSu'.$i] = $xRT['NIVELES_'.$i.'_LIMITE_SUPERIOR'];
                $mDatos['vTarPor' .$i] = $xRT['NIVELES_'.$i.'_VALOR_POR_UNIDAD'];
              }
            break;
    
            case "118": // PORCENTAJE VALOR CIF CON MINIMA Y MAXIMA[Importaciones]
            case "211": // PORCENTAJE VALOR CIF CON MINIMA Y MAXIMA[Exportaciones]
            case "312": // COBRO POR PORCENTAJE FOB CON MINIMA O MAXIMA[DTA]
              switch ($mDatos['cFcoId']) {
                case '118':
                  $cCamMin = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MINIMA_GENERAL_CIF" : "MINIMA";
                  $cCamMax = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MAXIMA_GENERAL_CIF" : "MAXIMA";
                break;
                case '211':
                  $cCamMin = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MINIMA_FOB" : "MINIMA";
                  $cCamMax = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MAXIMA_FOB" : "MAXIMA";
                break;
                default:
                  $cCamMin = "MINIMA";
                  $cCamMax = "MAXIMA";
                break;
              }
              if ($xFcoId == "211") {
                $mDatos['nPor118'] = $xRT['PORCENTAJE_FOB'];
              } else {
                $mDatos['nPor118'] = $xRT['PORCENTAJE_CIF'];
              }
              $mDatos['nMin118'] = $xRT[$cCamMin];
              $mDatos['nMax118'] = $xRT[$cCamMax];
            break;
    
            case "187": // VALOR FIJO ESCALONADO CON CIF EN USD
            case "309": // COBRO ESCALONADO CON VALOR FIJO
            case "241": // COBRO ESCALONADO CON VALOR FIJO (ULTIMO NIVEL POR UNIDAD)
            case "246": // COBRO ESCALONADO CON VALOR FIJO
            case "164": // COBRO ESCALONADO CON VALOR FIJO (ULTIMO NIVEL POR UNIDAD)
            case "131":
            case "130":
            case "119": // COBRO ESCALONADO VALOR FIJO
            case "189": // COBRO ESCALONADO VEHICULO VALOR FIJO
            case "259": // COBRO ESCALONADO VEHICULO VALOR FIJO
            case "1124": // COBRO ESCALONADO POR PEDIDO Y CANTIDAD DE ITEMS VALOR FIJO
              $cCamNiv = (($mDatos['cFcoId'] == '119' || $mDatos['cFcoId'] == '164' || $mDatos['cFcoId'] == '187' || $mDatos['cFcoId'] == '189' || $mDatos['cFcoId'] == '246' || $mDatos['cFcoId'] == '259') && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "NIVELES_CANT_VEHICULOS" : "NIVELES";
              $mDatos['nNiv119'] = $xRT[$cCamNiv];

              for($i=1; $i<=$mDatos['nNiv119']; $i++) {
                $mDatos['vTarLiIn'.$i] = $xRT[$cCamNiv.'_'.$i.'_LIMITE_INFERIOR'];
                $mDatos['vTarLiSu'.$i] = $xRT[$cCamNiv.'_'.$i.'_LIMITE_SUPERIOR'];
                $mDatos['vTarPor' .$i] = $xRT[$cCamNiv.'_'.$i.'_VALOR_FIJO'];
              }
            break;
            case "132": // COBRO ESCALONADO POR PROVEEDORES
              $mDatos['nNiv132'] = $xRT['NIVELES'];

              for($i=1; $i<=$mDatos['nNiv132']; $i++) {
                $mDatos['vTarLiIn'.$i] = $xRT['NIVELES_'.$i.'_LIMITE_INFERIOR'];
                $mDatos['vTarLiSu'.$i] = $xRT['NIVELES_'.$i.'_LIMITE_SUPERIOR'];
                $mDatos['vTarPor' .$i] = $xRT['NIVELES_'.$i.'_VALOR_COP'];
              }
            break;
            case "133": // COBRO ESCALONADO VALOR FIJO
              $cCamNiv = ($mDatos['cFcoId'] == '133' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "NIVELES_CANT_VEHICULOS" : "NIVELES";
              $mDatos['nNiv133'] = $xRT[$cCamNiv];

              for($i=1; $i<=$mDatos['nNiv133']; $i++) {
                $mDatos['vTarLiIn'.$i] = $xRT[$cCamNiv.'_'.$i.'_LIMITE_INFERIOR'];
                $mDatos['vTarLiSu'.$i] = $xRT[$cCamNiv.'_'.$i.'_LIMITE_SUPERIOR'];
                $mDatos['vTarPor' .$i] = $xRT[$cCamNiv.'_'.$i.'_VALOR_FIJO_POR_UNIDAD'];
              }
            break;
            case "199": // VALOR FIJO CONSOLIDADO POR DO
            case "270": // VALOR FIJO CONSOLIDADO POR DO
            case "120": // VALOR VARIABLE IMPORTACION
            case "212": // VALOR VARIABLE EXPORTACION
            case "320": // VALOR VARIABLE TRANSITO
            case "213": // VALOR AGENCIAMIENTO VARIABLE
              //Para esta forma de cobro, no hay valor parametrizado, debido a que el valor variable se parametriza en condiciones especiales por Do.
              //No se envia informacion de tarifas
            break;
    
            case "121": // PORCENTAJE CIF
              $mDatos['nPCif121'] = $xRT['PORCENTAJE_CIF'];
            break;
    
            case "122": // Tarifa Vinculada Intercompañias
              //Plena
              $mDatos['nPCifp122'] = $xRT['PLENA_PORCENTAJE_CIF'];
              $mDatos['nMinP122']  = $xRT['PLENA_MINIMA'];
              //Vinculada Deposito
              $mDatos['nPCifd122'] = $xRT['VINCULADA_DEPOSITO_PORCENTAJE_CIF'];
              $mDatos['nMinD122']  = $xRT['VINCULADA_DEPOSITO_MINIMA'];
              //Vinculada Agente Carga
              $mDatos['nPCifa122'] = $xRT['VINCULADA_AGENTE_CARGA_PORCENTAJE_CIF'];
              $mDatos['nMinA122']  = $xRT['VINCULADA_AGENTE_CARGA_MINIMA'];
            break;
    
            case "123": // COBRO VARIABLE POR HOJA ADICIONAL
              $mDatos['nTarI123'] = $xRT['TARIFA_HOJA_PRINCIPAL'];
              $mDatos['nTarF123'] = $xRT['TARIFA_HOJA_ADICIONAL'];
            break;
    
            case "124": // BASE CON MINIMA POR PORCENTAJE CIF
              $mDatos['nBase124'] = $xRT['BASE'];
              $mDatos['nMin124']  = $xRT['MINIMA'];
              $mDatos['nPCif124'] = $xRT['PORCENTAJE_CIF'];
            break;
    
            case "125": // PORCENTAJE CON UNA MINIMA
              $mDatos['nMin125'] = $xRT['MINIMA'];
              $mDatos['nPor125'] = $xRT['PORCENTAJE'];
            break;
    
            case "128": // PORCENTAJE CON UNA MINIMA
              $mDatos['nVpFparx'] = $xRT['VALOR_PARCIAL'];
              $mDatos['nVfcFpar'] = $xRT['VALOR_FIJO_COBRO_POR_PRIMER_PARCIAL'];
              $mDatos['nVsaFpar'] = $xRT['VALOR_POR_SERIAL_ADICIONAL'];
            break;
    
            case "138": // COBRO ESCALONADO SOBRE VALOR FOB EN COP
            case "275": // COBRO ESCALONADO SOBRE VALOR FOB EN USD
              $cCamNiv = ($mDatos['cFcoId'] == '275' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "NIVELES_CANT_VEHICULOS" : "NIVELES";
              $mDatos['nNiv138'] = $xRT[$cCamNiv];

              for($i=1; $i<=$mDatos['nNiv138']; $i++) {
                $mDatos['vTarLiIn'.$i] = $xRT[$cCamNiv.'_'.$i.'_LIMITE_INFERIOR'];
                $mDatos['vTarLiSu'.$i] = $xRT[$cCamNiv.'_'.$i.'_LIMITE_SUPERIOR'];
                $mDatos['vTarPor' .$i] = $xRT[$cCamNiv.'_'.$i.'_VALOR_FIJO'];
              }
            break;
    
            case "139": // GRUPO DOCUMENTAL
              $mDatos['nCanDim139'] = $xRT['CANTIDAD_DIM_POR_GRUPO'];
              $mDatos['nCanDav139'] = $xRT['CANTIDAD_DAV_POR_GRUPO'];
              $mDatos['nVlr139']    = $xRT['VALOR_FIJO_POR_GRUPO'];
              $mDatos['nGru139']    = $xRT['CANTIDAD_DE_GRUPOS_QUE_NO_SE_COBRAN'];
            break;
            case "140": // PORCENTAJE VALOR CIF O UNA MINIMA
              $mDatos['nPor140'] = $xRT['PORCENTAJE_CIF'];
              $mDatos['nMin140'] = $xRT['MINIMA'];
              $mDatos['nVlrDc']  = $xRT['VARIABLE_DESCARGUE_DIRECTO'];
            break;
            case "137": // PORCENTAJE DEL VALOR FOB O UNA MINIMA
            case "173": // PORCENTAJE DEL VALOR FOB POR PERIODO DE ALMACENAMIENTO CON MINIMA
            case "203": // PORCENTAJE DEL VALOR FOB O UNA MINIMA
            case "249": // PORCENTAJE DEL VALOR FOB O UNA MINIMA
            case "302":
              $cCamMin = ($mDatos['cFcoId'] == '203' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MINIMA_FOB" : "MINIMA";
              $mDatos['nPor203'] = $xRT['PORCENTAJE_FOB'];
              $mDatos['nMin203'] = $xRT[$cCamMin];
            break;
    
            case "135": // COBRO ESCALONADO SOBRE VALOR FOB EN USD
            case "205": // COBRO ESCALONADO SOBRE VALOR FOB EN USD
            case "304":
              switch ($mDatos['cFcoId']) {
                case '135':
                  $cCamMin = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MINIMA_GENERAL_CIF" : "MINIMA";
                break;
                case '205':
                  $cCamMin = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MINIMA_FOB" : "MINIMA";
                break;
                default:
                  $cCamMin = "MINIMA";
                break;
              }
              $cCamNiv = (($mDatos['cFcoId'] == '135' || $mDatos['cFcoId'] == '205') && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "NIVELES_CANT_VEHICULOS" : "NIVELES";
              $mDatos['nMin205'] = $xRT[$cCamMin];
              $mDatos['nNiv205'] = $xRT[$cCamNiv];

              for($i=1; $i<=$mDatos['nNiv205']; $i++) {
                $mDatos['vTarLiIn'.$i] = $xRT[$cCamNiv.'_'.$i.'_LIMITE_INFERIOR'];
                $mDatos['vTarLiSu'.$i] = $xRT[$cCamNiv.'_'.$i.'_LIMITE_SUPERIOR'];
                $mDatos['vTarPor' .$i] = $xRT[$cCamNiv.'_'.$i.'_PORCENTAJE'];
              }
            break;
    
            case "136": // COBRO ESCALONADO SOBRE VALOR FOB EN COP
            case "206": // COBRO ESCALONADO SOBRE VALOR FOB EN COP
              $mDatos['nMin206'] = $xRT['MINIMA'];
              $mDatos['nNiv206'] = $xRT['NIVELES'];

              for($i=1; $i<=$mDatos['nNiv206']; $i++) {
                $mDatos['vTarLiIn'.$i] = $xRT['NIVELES_'.$i.'_LIMITE_INFERIOR'];
                $mDatos['vTarLiSu'.$i] = $xRT['NIVELES_'.$i.'_LIMITE_SUPERIOR'];
                $mDatos['vTarPor' .$i] = $xRT['NIVELES_'.$i.'_PORCENTAJE'];
              }
            break;
    
            case "209": // VALOR FOB DIVIDIDO EN PESOS
              $mDatos['nPar209'] = $xRT['VALOR_PARCIAL'];
              $mDatos['nVFC209'] = $xRT['VALOR_FIJO_COBRO_POR_PRIMER_PARCIAL'];
              $mDatos['nVCA209'] = $xRT['PORCENTAJE_COBRO_VALOR_FOB_ADICIONAL'];
            break;
    
            case "1142": // PORCENTAJE VALOR O UNA MINIMA EN USD
            case "1146": // PORCENTAJE VALOR O UNA MINIMA
            case "210":  // PORCENTAJE DEL VALOR FOB POR LA T.R.M.
              $mDatos['nPVF210'] = $xRT['PORCENTAJE'];
              $mDatos['nMin210'] = $xRT['MINIMA'];
            break;
    
            case "306":
              $mDatos['nPor306'] = $xRT['PORCENTAJE_TRIBUTOS'];
              $mDatos['nMin306'] = $xRT['MINIMA'];
            break;
            case "129": // % del Valor CIF o Valor Parcial
              $mDatos['nPor129'] = $xRT['PORCENTAJE_CIF'];
              $mDatos['nPar129'] = $xRT['VALOR_FIJO_PARCIAL'];
            break;
            case "141": // PORCENTAJE VALOR CIF Y TASA DE NEGOCIADA
              $mDatos['nPor141']    = $xRT['PORCENTAJE_CIF'];
              $mDatos['nTasNeg141'] = $xRT['TASA_NEGOCIADA'];
            break;
            case "142": //SUBPARTIDA POR UNIDAD CON MINIMA
            case "176": //VALOR POR UNIDADES INICIALES CON MINIMA
            case "253": //VALOR POR UNIDADES INICIALES CON MINIMA
            case "279": //SUBPARTIDA POR UNIDAD CON MINIMA
              $mDatos['nUniIni142'] = $xRT['UNIDADES_INICIALES'];
              $mDatos['nValIni142'] = $xRT['VALOR_INICIAL'];
              $mDatos['nUniAdi142'] = $xRT['UNIDADES_ADICIONALES'];
              $mDatos['nValAdi142'] = $xRT['VALOR_ADICIONAL'];
            break;
            case "143": //VALOR UNIDAD O MINIMA
            case "151": //VALOR  FIJO CANTIDAD DE DECLARACIONES DE IMPORTACION CON MINIMA
              $cCamMin = ($mDatos['cFcoId'] == '151' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MINIMA_GENERAL_CIF" : "MINIMA";
              $cCamVal = ($mDatos['cFcoId'] == '151' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "VALOR_POR_UNIDAD_TONELADAS" : "VALOR_POR_UNIDAD";
              $mDatos['nValUni143'] = $xRT[$cCamVal];
              $mDatos['nMin143']    = $xRT[$cCamMin];
            break;
            case "144": //VALOR FIJO POR UNIDAD CON MINIMA Y MAXIMA
              $mDatos['nValFijUni144'] = $xRT['VALOR_FIJO_POR_UNIDAD'];
              $mDatos['nValMin144']    = $xRT['VALOR_MINIMO'];
              $mDatos['nValMax144']    = $xRT['VALOR_MAXIMO'];
            break;
            case "145": //COBRO VARIABLE SEGUN CANTIDAD DE CONTENEDORES
              // Carga Suelta
              $mDatos['nTarIniCar145'] = $xRT['CARGA_SUELTA_TARIFA_INICIAL'];
              $mDatos['nUniIniCar145'] = $xRT['CARGA_SUELTA_UNIDADES_INICIALES'];
              $mDatos['nTarDesCar145'] = $xRT['CARGA_SUELTA_TRARIFA_DESPUES_DE_INICIALES'];
              // Contenedor 20
              $mDatos['nTarIniC20145'] = $xRT['CONTENEDOR_20_TARIFA_INICIAL'];
              $mDatos['nUniIniC20145'] = $xRT['CONTENEDOR_20_UNIDADES_INICIALES'];
              $mDatos['nTarDesC20145'] = $xRT['CONTENEDOR_20_TRARIFA_DESPUES_DE_INICIALES'];
              // Contenedor 40
              $mDatos['nTarIniC40145'] = $xRT['CONTENEDOR_40_TARIFA_INICIAL'];
              $mDatos['nUniIniC40145'] = $xRT['CONTENEDOR_40_UNIDADES_INICIALES'];
              $mDatos['nTarDesC40145'] = $xRT['CONTENEDOR_40_TRARIFA_DESPUES_DE_INICIALES'];
            break;
            case "235": //COBRO POR UNIDAD DE CARGA CONTENEDORES CON MINIMA (EXPORTACION)
              $mDatos['nMin235']    = $xRT['MINIMA'];
              $mDatos['nCifGra235'] = $xRT['PORCENTAJE_CIF_PARA_GRANEL'];
              $mDatos['nCon20235']  = $xRT['CONTENEDOR_20'];
              $mDatos['nCon40235']  = $xRT['CONTENEDOR_40'];
              $mDatos['nConCar235'] = $xRT['CARGA_SUELTA'];
            break;
    
            case "154": // COBRO ESCALONADO POR UNIDAD DE PESO
            case "149": // VALOR ESCALONADO POR ITEM CON MINIMA
            case "236": //
            case "148": // COBRO ESCALONADO POR UNIDAD CON MINIMA
              switch ($mDatos['cFcoId']) {
                case '148':
                case '149':
                  $cCamMin = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MINIMA_GENERAL_CIF" : "MINIMA";
                break;
                case '236':
                  $cCamMin = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MINIMA_FOB" : "MINIMA";
                break;
                default:
                  $cCamMin = "MINIMA";
                break;
              }
              $cCamNiv = (($mDatos['cFcoId'] == '148' || $mDatos['cFcoId'] == '149' || $mDatos['cFcoId'] == '236') && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "NIVELES_CANT_VEHICULOS" : "NIVELES";
              $mDatos['nMin148'] = $xRT[$cCamMin];
              $mDatos['nNiv148'] = $xRT[$cCamNiv];

              for($i=1; $i<=$mDatos['nNiv148']; $i++) {
                $mDatos['vTarLiIn'.$i] = $xRT[$cCamNiv.'_'.$i.'_LIMITE_INFERIOR'];
                $mDatos['vTarLiSu'.$i] = $xRT[$cCamNiv.'_'.$i.'_LIMITE_SUPERIOR'];
                $mDatos['vTarPor' .$i] = $xRT[$cCamNiv.'_'.$i.'_VALOR_POR_UNIDAD'];
              }
            break;
            case "190": // COBRO ESCALONADO POR UNIDAD CON MAXIMA
            case "260": // COBRO ESCALONADO POR UNIDAD CON MAXIMA
              $mDatos['nNiv190'] = $xRT['NIVELES'];
              $mDatos['nMax190'] = $xRT['MAXIMA'];

              for($i=1; $i<=$mDatos['nNiv190']; $i++) {
                $mDatos['vTarLiIn'.$i] = $xRT['NIVELES_'.$i.'_LIMITE_INFERIOR'];
                $mDatos['vTarLiSu'.$i] = $xRT['NIVELES_'.$i.'_LIMITE_SUPERIOR'];
                $mDatos['vTarPor' .$i] = $xRT['NIVELES_'.$i.'_VALOR_POR_UNIDAD'];
              }
            break;
    
            case "153": // COBRO POR UNIDAD DE CARGA (CONTENEDORES - FURGON)
              $mDatos['nC20153'] = $xRT['CONTENEDOR_20'];
              $mDatos['nC40153'] = $xRT['CONTENEDOR_40'];
              $mDatos['nFur153'] = $xRT['FURGON'];
            break;
    
            case "155": // VALOR VARIABLE POR PCC
            case "281": // VALOR VARIABLE POR PCC
              $mDatos['nVlrBas155'] = $xRT['VALOR_BASE'];
              $mDatos['nPorApl155'] = $xRT['PORCENTAJE_APLICA'];
            break;
    
            case "237": // COBRO POR UNIDAD DE CARGA CON MINIMA - EXPORTACION
            case "156": // COBRO POR UNIDAD DE CARGA CON MINIMA
              $mDatos['nMin156'] = $xRT['MINIMA'];
              $mDatos['nC20156'] = $xRT['CONTENEDOR_20'];
              $mDatos['nC40156'] = $xRT['CONTENEDOR_40'];
              $mDatos['nCaS156'] = $xRT['CARGA_SUELTA'];
            break;
            case "157": // COBRO SOBRE PORCENTAJE DEL VALOR DE LA FACTURA COMERCIAL O MINIMA
            case "158": // COBRO SOBRE PORCENTAJE DEL VALOR DE LA TOTALIDAD DE LAS FACTURAS COMERCIALES O MINIMA
              $mDatos['nMin157']    = $xRT['MINIMA'];
              $mDatos['nPorFac157'] = $xRT['PORCENTAJE_VALOR_FACTURA'];
            break;
    
            case "238": //  Cobro Escalonado por cantidad de Kilos - Expo
            case "160": //  Cobro Escalonado por cantidad de Kilos
              $mDatos['nNiv160'] = $xRT['NIVELES'];

              for($i=1; $i<=$mDatos['nNiv160']; $i++) {
                $mDatos['vTarLiIn'.$i] = $xRT['NIVELES_'.$i.'_LIMITE_INFERIOR'];
                $mDatos['vTarLiSu'.$i] = $xRT['NIVELES_'.$i.'_LIMITE_SUPERIOR'];
                $mDatos['vTarPor' .$i] = $xRT['NIVELES_'.$i.'_VALOR'];
              }
            break;
    
            case "239": 	// PORCENTAJE VALOR FOB O UNA MINIMA O UNA MAXIMA - EXPORTACIONES
            case "245":		// COBRO ESCALONADO SOBRE VALOR FOB EN USD CON MINIMA Y MAXIMA
            case "273": 	// COMISION ESCALONADO VALOR FIJO CON MINIMA Y MAXIMA
            case "1102": 	// COMISION ESCALONADO VALOR FIJO CON MINIMA Y MAXIMA
            case "162": 	// PORCENTAJE VALOR FOB O UNA MINIMA O UNA MAXIMA
            case "161": 	// PORCENTAJE VALOR CIF O UNA MINIMA O UNA MAXIMA
            case "167": 	// COBRO ESCALONADO SOBRE VALOR CIF EN USD CON MINIMA Y MAXIMA
              switch ($mDatos['cFcoId']) {
                case '161':
                case '167':
                  $cCamMin = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MINIMA_GENERAL_CIF" : "MINIMA";
                  $cCamMax = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MAXIMA_GENERAL_CIF" : "MAXIMA";
                break;
                case '245':
                case '273':
                  $cCamMin = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MINIMA_FOB" : "MINIMA";
                  $cCamMax = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MAXIMA_FOB" : "MAXIMA";
                break;
                default:
                  $cCamMin = "MINIMA";
                  $cCamMax = "MAXIMA";
                break;
              }
              $cCamNiv = (($mDatos['cFcoId'] == '161' || $mDatos['cFcoId'] == '167' || $mDatos['cFcoId'] == '245' || $mDatos['cFcoId'] == '273') && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "NIVELES_CANT_VEHICULOS" : "NIVELES";
              $mDatos['nMin161'] = $xRT[$cCamMin];
              $mDatos['nMax161'] = $xRT[$cCamMax];
              $mDatos['nNiv161'] = $xRT[$cCamNiv];

              for($i=1; $i<=$mDatos['nNiv161']; $i++) {
                $mDatos['vTarLiIn'.$i] = $xRT[$cCamNiv.'_'.$i.'_LIMITE_INFERIOR'];
                $mDatos['vTarLiSu'.$i] = $xRT[$cCamNiv.'_'.$i.'_LIMITE_SUPERIOR'];
                switch ($mDatos['cFcoId']) {
                  case "273":
                  case "1102":
                    $mDatos['vTarPor' .$i] = $xRT[$cCamNiv.'_'.$i.'_VALOR_FIJO'];
                  break;
                  default:
                    $mDatos['vTarPor' .$i] = $xRT[$cCamNiv.'_'.$i.'_PORCENTAJE'];
                  break;
                }
              }
            break;
    
            case "242": // VALOR FIJO POR PRODUCTO O MINIMA
            case "165": // VALOR FIJO POR PRODUCTO O MINIMA
              $mDatos['nMin165'] = $xRT['MINIMA'];
              $mDatos['nSim165'] = $xRT['SIMCARD'];
              $mDatos['nTer165'] = $xRT['TERMINALES'];
              $mDatos['nTab165'] = $xRT['TABLET'];
              $mDatos['nMod165'] = $xRT['MODEM'];
            break;
            case "243": // VALOR x HORAS O MINIMA
            case "166": // VALOR x HORAS O MINIMA
              $cCamMin = ($mDatos['cFcoId'] == '166' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MINIMA_GENERAL_CIF" : "MINIMA";
              $mDatos['nMin166'] = $xRT[$cCamMin];
              $mDatos['nVlr166'] = $xRT['VALOR_POR_HORA'];
            break;
            case "244": // VALOR POR CANTIDAD DE DIM O MINIMA
              $mDatos['nMin244'] = $xRT['MINIMA'];
              $mDatos['nVlr244'] = $xRT['VALOR_POR_UNIDAD'];
            break;
            case "168": // VALOR FIJO POR UNIDAD CON MINIMA
            case "175": // VALOR FIJO POR UNIDAD CON MINIMA
            case "247": // VALOR FIJO POR UNIDAD CON MINIMA
            case "252": // VALOR FIJO POR UNIDAD CON MINIMA
            case "308": // VALOR FIJO POR UNIDAD CON MINIMA
              $mDatos['nValUni168'] = $xRT['VALOR_POR_UNIDAD'];
              if ($mDatos['cFcoId'] == "308") {
                $mDatos['nMin168'] = $xRT['VALOR_MINIMO'];
              } else {
                $mDatos['nMin168'] = $xRT['MINIMA'];
              }
            break;
            case "169": // VALOR FIJO POR HORAS
              $mDatos['nHorDiu169'] = $xRT['HORA_DIURNA'];
              $mDatos['nHorNoc169'] = $xRT['HORA_NOCTURNA'];
              $mDatos['nHorDom169'] = $xRT['HORA_DOMINICAL'];
              $mDatos['nHorFes169'] = $xRT['HORA_FESTIVA'];
            break;
            case "170": // VALOR FIJO POR CANTIDAD DE VEHICULOS CON MINIMA
            case "248": // VALOR FIJO POR CANTIDAD DE VEHICULOS CON MINIMA
              switch ($mDatos['cFcoId']) {
                case '170':
                  $cCamMin = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MINIMA_GENERAL_CIF" : "MINIMA";
                break;
                case '248':
                  $cCamMin = ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") ? "MINIMA_FOB" : "MINIMA";
                break;
                default:
                  $cCamMin = "MINIMA";
                break;
              }
              $mDatos['nValFij170'] = $xRT['VALOR_FIJO'];
              $mDatos['nMin170']    = $xRT[$cCamMin];
            break;
            case "171": // VALOR FIJO x SUFIJO
              $mDatos['nSuf001171'] = $xRT['SUFIJO_001'];
              $mDatos['nSuf002171'] = $xRT['SUFIJO_002'];
            break;
            case "172": // VALOR FIJO x CIF
              $mDatos['nValCif172'] = $xRT['HASTA_VALOR_CIF'];
              $mDatos['nValFij172'] = $xRT['VALOR_FIJO'];
            break;
            case "177": // VALOR POR HORAS CON MINIMA
              $mDatos['nMin177']    = $xRT['MINIMA'];
              $mDatos['nHorOrd177'] = $xRT['HORA_ORDINARIA'];
              $mDatos['nHorFes177'] = $xRT['HORA_FESTIVA'];
            break;
            case "178": // COBRO POR UNIDAD DE CARGA CON MINIMA
              $mDatos['nC20178']    = $xRT['CONTENEDOR_20'];
              $mDatos['nC40178']    = $xRT['CONTENEDOR_40'];
              $mDatos['nCaS178']    = $xRT['CARGA_SUELTA'];
              $mDatos['nMinC20178'] = $xRT['MINIMA_CONTENEDOR_20'];
              $mDatos['nMinC40178'] = $xRT['MINIMA_CONTENEDOR_40'];
              $mDatos['nMinCaS178'] = $xRT['MINIMA_CARGA_SUELTA'];
            break;
            case "179": // Cobro Variable + % Variable
            case "254": // Cobro Variable + % Variable
              $mDatos['nPorVar179'] = $xRT['PORCENTAJE_VARIABLE'];
            break;
            case "180": // Cobro por unidad con maxima y + Cobro Variable + % Variable uniades adiacionales
              $mDatos['nCanIni180'] = $xRT['CANTIDADES_INICIALES'];
              $mDatos['nValIni180'] = $xRT['VALOR_INICIAL'];
              $mDatos['nPorAdi180'] = $xRT['PORCENTAJE_ADICIONAL'];
            break;
            case "181": // % CIF Escalonado por intervalos
              $mDatos['nDiaInt181'] = $xRT['DIAS_INTERVALO'];
              $mDatos['nPorCif181'] = $xRT['PORCENTAJE_CIF'];
              $mDatos['nMin181']    = $xRT['MINIMA'];
            break;
            case "255": // Valor por posición de acuerdo a referencia
            case "182": // Valor por posición de acuerdo a referencia
              $mDatos['nValTon182'] = $xRT['VALOR_TONELADA'];
              $mDatos['nCanMax182'] = $xRT['CANTIDAD_MAXIMA'];
              $mDatos['nPorAdi182'] = $xRT['PORCENTAJE_ADICIONAL'];
            break;
            case "183": // Valor por posición de acuerdo a referencia
              $mDatos['nValEst183'] = $xRT['VALOR_ESTIBA'];
              $mDatos['nMinC20183'] = $xRT['MINIMA_C20'];
              $mDatos['nMinC40183'] = $xRT['MINIMA_C40'];
            break;
            case "184": // Cobro escalonado por cantidad de quincenas
              $mDatos['nQuiVeh184'] = $xRT['PRECIO_QNA_VEHICULO'];
              $mDatos['nQuiCta184'] = $xRT['PRECIO_QNA_CAMIONETA'];
              $mDatos['nQuiCam184'] = $xRT['PRECIO_QNA_CAMION'];
              $mDatos['nQuiMon184'] = $xRT['PRECIO_QNA_MONTACARGA'];
            break;
            case "185": // Cobro escalonado por cantidad de meses
              $mDatos['nMesVeh185'] = $xRT['PRECIO_MES_VEHICULO'];
              $mDatos['nMesCta185'] = $xRT['PRECIO_MES_CAMIONETA'];
              $mDatos['nMesCam185'] = $xRT['PRECIO_MES_CAMION'];
              $mDatos['nMesMon185'] = $xRT['PRECIO_MES_MONTACARGA'];
            break;
            case "256": // COBRO ESCALONADO CON MÍNIMA EN EL ÚLTIMO RANGO
            case "186": // COBRO ESCALONADO CON MÍNIMA EN EL ÚLTIMO RANGO
              $mDatos['nPorCif186'] = $xRT['PORCENTAJE_CIF'];
              $mDatos['nNiv186'] = $xRT['NIVELES'];

              for($i=1; $i<=$mDatos['nNiv186']; $i++) {
                $mDatos['vTarLiIn'.$i] = $xRT['NIVELES_'.$i.'_LIMITE_INFERIOR'];
                $mDatos['vTarLiSu'.$i] = $xRT['NIVELES_'.$i.'_LIMITE_SUPERIOR'];
                $mDatos['vTarPor' .$i] = $xRT['NIVELES_'.$i.'_VALOR_FIJO'];
              }
            break;
            case "261": // COBRO SECUENCIAL POR CANTIDAD DE VEHICULOS
            case "191": // COBRO SECUENCIAL POR CANTIDAD DE VEHICULOS
              $cCamNiv = ($mDatos['cFcoId'] == '261' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "NIVELES_CANT_VEHICULOS" : "NIVELES";
              $mDatos['nNiv191'] = $xRT[$cCamNiv];

              for($i=1; $i<=$mDatos['nNiv191']; $i++) {
                $mDatos['vSecCan'.$i] = $xRT[$cCamNiv.'_'.$i.'_CANTIDAD'];
                $mDatos['vValCan'.$i] = $xRT[$cCamNiv.'_'.$i.'_VALOR'];
              }
            break;
            case "263": // COBRO ESCALONADO CONTENEDORES DE 20 Y 40 VALOR FIJO POR UNIDAD
            case "193": // COBRO ESCALONADO CONTENEDORES DE 20 Y 40 VALOR FIJO POR UNIDAD
            case "262": // COBRO ESCALONADO CONTENEDORES DE 20 Y 40 VALOR FIJO POR NIVEL
            case "192": // COBRO ESCALONADO CONTENEDORES DE 20 Y 40 VALOR FIJO POR NIVEL
              $mDatos['nNivC20192'] = $xRT['NIVELES_C20'];

              for($i=1; $i<=$mDatos['nNivC20192']; $i++) {
                $mDatos['vTarLiInC20'.$i] = $xRT['NIVELES_C20_'.$i.'_LIMITE_INFERIOR'];
                $mDatos['vTarLiSuC20'.$i] = $xRT['NIVELES_C20_'.$i.'_LIMITE_SUPERIOR'];
                $mDatos['vValorC20'  .$i] = $xRT['NIVELES_C20_'.$i.'_VALOR'];
              }

              $mDatos['nNivC40192'] = $xRT['NIVELES_C40'];

              for($i=1; $i<=$mDatos['nNivC40192']; $i++) {
                $mDatos['vTarLiInC40'.$i] = $xRT['NIVELES_C40_'.$i.'_LIMITE_INFERIOR'];
                $mDatos['vTarLiSuC40'.$i] = $xRT['NIVELES_C40_'.$i.'_LIMITE_SUPERIOR'];
                $mDatos['vValorC40'  .$i] = $xRT['NIVELES_C40_'.$i.'_VALOR'];
              }
            break;
            case "264": // COBRO CONTENEDORES DE 20 Y DE 40 VALOR FIJO
            case "195": // COBRO CONTENEDORES DE 20 Y DE 40 VALOR POR UNIDAD
            case "265": // COBRO CONTENEDORES DE 20 Y DE 40 VALOR POR UNIDAD
            case "194": // COBRO CONTENEDORES DE 20 Y DE 40 VALOR FIJO
              $mDatos['nValFijC20194'] = $xRT['CONTENEDOR_20'];
              $mDatos['nValFijC40194'] = $xRT['CONTENEDOR_40'];
            break;
    
            case "266": // PORCENTAJE VALOR CIF CON MAXIMA
            case "196": // PORCENTAJE VALOR CIF CON MAXIMA
              $cCamMax = ($mDatos['cFcoId'] == '196' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MAXIMA_GENERAL_CIF" : "MAXIMA";
              $mDatos['nPor196'] = $xRT['PORCENTAJE_CIF'];
              $mDatos['nMax196'] = $xRT[$cCamMax];
            break;
            case "267": // COMISION POR TONELADAS CON MINIMA
              $cCamVal = ($mDatos['cFcoId'] == '267' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "VALOR_POR_UNIDAD_TONELADAS" : "VALOR_TONELADA";
              $cCamMin = ($mDatos['cFcoId'] == '267' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MINIMA_FOB" : "MINIMA";
              $mDatos['nVal267'] = $xRT[$cCamVal];
              $mDatos['nMin267'] = $xRT[$cCamMin];
            break;
            case "268": // COMISION POR PORCENTAJE
              $mDatos['nVal268'] = $xRT['PORCENTAJE_FOB'];
            break;
            case "197":
              $cCamMax = ($mDatos['cFcoId'] == '197' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MAXIMA_GENERAL_CIF" : "MAXIMA";
              $cCamVal = ($mDatos['cFcoId'] == '197' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "VALOR_POR_HORA" : "VALOR";
              $mDatos['nVlr197'] = $xRT[$cCamVal];
              $mDatos['nHor197'] = $xRT['HORAS'];
              $mDatos['nAdi197'] = $xRT['VALOR_ADICIONAL'];
              $mDatos['nMax197'] = $xRT[$cCamMax];
            break;
            case "269": // COBRO ESCALONADO SOBRE VALOR CIF EN USD CON MINIMA Y MAXIMA
              $mDatos['nMin269'] = $xRT['MINIMA'];
              $mDatos['nMax269'] = $xRT['MAXIMA'];
              $mDatos['nNiv269'] = $xRT['NIVELES'];

              for($i=1; $i<=$mDatos['nNiv269']; $i++) {
                $mDatos['vTarLiIn'.$i] = $xRT['NIVELES_'.$i.'_LIMITE_INFERIOR'];
                $mDatos['vTarLiSu'.$i] = $xRT['NIVELES_'.$i.'_LIMITE_SUPERIOR'];
                $mDatos['vTarPor' .$i] = $xRT['NIVELES_'.$i.'_PORCENTAJE'];
              }
            break;
            case "198": // PORCENTAJE sobre comision
              $mDatos['nPor198'] = $xRT['PORCENTAJE_CIF'];
              $mDatos['nSob198'] = $xRT['PORCENTAJE_SOBRE_COMISION'];
            break;
            case "271": // VALOR FIJO CONSOLIDADO POR CUBICAJE Y DO
            case "1100": // VALOR FIJO CONSOLIDADO POR CUBICAJE Y DO
              $mDatos['nVal1100'] = $xRT['VALOR_TRAMITE'];
            break;
            case "272": // VALOR FIJO CONSOLIDADO POR CUBICAJE Y DO
            case "1101": // VALOR FIJO CONSOLIDADO POR CUBICAJE Y DO
              $mDatos['nTas1101'] = $xRT['TASA_PACTADA'];
              $mDatos['nPor1101'] = $xRT['PORCENTAJE'];
              $mDatos['nMin1101'] = $xRT['MINIMA'];
            break;
            case "274": // COBRO SOBRE PORCENTAJE DE NEGOCIACION
            case "1103": // COBRO SOBRE PORCENTAJE DE NEGOCIACION
              $mDatos['nTar1103'] = $xRT['TARIFA'];
              $mDatos['nPor1103'] = $xRT['PORCENTAJE'];
            break;
            case "1104": // COBRO SOBRE PORCENTAJE DE NEGOCIACION
              $mDatos['nPor1104'] = $xRT['PORCENTAJE'];
            break;
            case "1105": // COBRO % SOBRE VALOR CIF (CONDICIONES ESPECIALES) CON MINIMA Y MAXIMA
              $cCamMin = ($mDatos['cFcoId'] == '1105' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MINIMA_GENERAL_CIF" : "MINIMA";
              $cCamMax = ($mDatos['cFcoId'] == '1105' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MAXIMA_GENERAL_CIF" : "MAXIMA";
              $mDatos['nMin1105'] = $xRT[$cCamMin];
              $mDatos['nMax1105'] = $xRT[$cCamMax];
              $mDatos['nPor1105'] = $xRT['PORCENTAJE'];
            break;
            case "1106": // COBRO ESCALONADO SOBRE VALOR CIF EN USD
              $cCamMin = ($mDatos['cFcoId'] == '1106' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MINIMA_GENERAL_CIF" : "MINIMA";
              $cCamNiv = ($mDatos['cFcoId'] == '1106' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "NIVELES_CANT_VEHICULOS" : "NIVELES";
              $mDatos['nMin1106']    = $xRT[$cCamMin];
              $mDatos['nPorDes1106'] = $xRT['PORCENTAJE_DESCUENTO'];
              $mDatos['nNiv1106']    = $xRT[$cCamNiv];

              for($i=1; $i<=$mDatos['nNiv1106']; $i++) {
                $mDatos['vTarLiIn'.$i] = $xRT[$cCamNiv.'_'.$i.'_LIMITE_INFERIOR'];
                $mDatos['vTarLiSu'.$i] = $xRT[$cCamNiv.'_'.$i.'_LIMITE_SUPERIOR'];
                $mDatos['vTarPor' .$i] = $xRT[$cCamNiv.'_'.$i.'_PORCENTAJE'];
              }
            break;
            case "1107": // PORCENTAJE O MÍNIMA POR CARGA GRANEL O CONTENEDOR
              // Contenedor
              $mDatos['nPCifc1107'] = $xRT['CONTENEDOR_PORCENTAJE_CIF'];
              $mDatos['nMinC1107']  = $xRT['CONTENEDOR_MINIMA'];
              // Granel
              $mDatos['nPCifg1107'] = $xRT['GRANEL_PORCENTAJE_CIF'];
              $mDatos['nMinG1107']  = $xRT['GRANEL_MINIMA'];
            break;
            case "1108": // COBRO ESCALONADO PORCENTAJE O MÍNIMA POR CANTIDAD BL
              $mDatos['nPCif1108'] = $xRT['PORCENTAJE_CIF'];
              $mDatos['nNiv1108']  = $xRT['NIVELES'];

              for($i=1; $i<=$mDatos['nNiv1108']; $i++) {
                $mDatos['vTarLiIn'.$i] = $xRT['NIVELES_'.$i.'_LIMITE_INFERIOR'];
                $mDatos['vTarLiSu'.$i] = $xRT['NIVELES_'.$i.'_LIMITE_SUPERIOR'];
                $mDatos['vTarPor' .$i] = $xRT['NIVELES_'.$i.'_VALOR_MINIMA'];
              }
            break;
            case "1109": // PORCENTAJE CIF O MÍNIMA POR CARGA GRANEL O CONTENEDOR POR NIVEL O TIPO DE MERCANCÍA
              $mDatos['nNiv1109']  = $xRT['NIVELES'];

              for($i=1; $i<=$mDatos['nNiv1109']; $i++) {
                $mDatos['vSecCan'.$i] = $xRT['NIVELES_'.$i.'_NIVELES'];
                $mDatos['vValCif'.$i] = $xRT['NIVELES_'.$i.'_PORCENTAJE_CIF'];
                $mDatos['vValMin'.$i] = $xRT['NIVELES_'.$i.'_MINIMA'];
              }
            break;
            case "1110":
              $cCamVal = ($mDatos['cFcoId'] == '1110' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "VALOR_POR_HORA" : "VALOR";
              $mDatos['nVlr1110'] = $xRT[$cCamVal];
              $mDatos['nHor1110']  = $xRT['HORAS'];
              $mDatos['nAdi1110']  = $xRT['VALOR_ADICIONAL'];
            break;				
            case "276":
              $cCamMin = ($mDatos['cFcoId'] == '1110' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "MINIMA_FOB" : "MINIMA";
              $mDatos['nPor276']    = $xRT['PORCENTAJE_FOB'];
              $mDatos['nMin276']    = $xRT[$cCamMin];
              $mDatos['nPorDes276'] = $xRT['PORCENTAJE_DESCUENTO'];
            break;
            case "1111":
            case "1118":
            case "277":
            case "278":
            case "310":
            case "311":
              $mDatos['nPor1111'] = $xRT['PORCENTAJE'];
            break;
            case "1112": // CANTIDAD TONELADAS CON UNA MINIMA O MAXIMA
              $mDatos['nVal1112'] = $xRT['VALOR_TONELADA'];
              $mDatos['nMin1112'] = $xRT['MINIMA'];
              $mDatos['nMax1112'] = $xRT['MAXIMA'];
            break;
            case "1113": // PORCENTAJE CIF O MÍNIMA POR CARGA GRANEL O CONTENEDOR POR NIVEL O TIPO DE MERCANCÍA
              $mDatos['nNiv1113'] = $xRT['NIVELES'];

              for($i=1; $i<=$mDatos['nNiv1113']; $i++) {
                $mDatos['vSecCan'.$i] = $xRT['NIVELES_'.$i.'_NIVELES'];
                $mDatos['vValCif'.$i] = $xRT['NIVELES_'.$i.'_PORCENTAJE_CIF'];
                $mDatos['vValMin'.$i] = $xRT['NIVELES_'.$i.'_MINIMA'];
                $mDatos['vValMax'.$i] = $xRT['NIVELES_'.$i.'_MAXIMA'];
              }
            break;
            case "1114": // VALOR CIF CON MINIMA + ITEMS
              $mDatos['nPor1114']    = $xRT['PORCENTAJE_CIF'];
              $mDatos['nMin1114']    = $xRT['MINIMA'];
              $mDatos['nIteIni1114'] = $xRT['ITEMS_INICIALES'];
              $mDatos['nVlrIte1114'] = $xRT['VALOR_ITEM_ADICIONAL'];
            break;
            case "1115": // CANTIDAD DE HORAS X CONTENEDOR X PERSONA
              $mDatos['nHraBlo1115'] = $xRT['HORAS_POR_BLOQUE'];
              $mDatos['nVlrBlo1115'] = $xRT['VALOR_BLOQUE'];
            break;
            case "1116": // VALOR CIF CON MINIMA + ITEMS
              $mDatos['nPor1116']    = $xRT['PORCENTAJE_CIF'];
              $mDatos['nMin1116']    = $xRT['VALOR_MINIMA'];
              $mDatos['nVlrAdi1116'] = $xRT['VALOR_ADICIONAL'];
            break;
            case "1119": // % CIF O MINIMA VARIABLE CONTENEDOR O DESCARGUE DIRECTO
              $mDatos['nCif1119']    = $xRT['PORCENTAJE_CIF'];
              $mDatos['nMin1119']    = $xRT['MINIMA'];
              $mDatos['nC201119']    = $xRT['CONTENEDOR_20'];
              $mDatos['nC401119']    = $xRT['CONTENEDOR_40'];
              $mDatos['nVlrDes1119'] = $xRT['DESCARGUE_DIRECTO'];
            break;
            case "1120": // % CIF O MINIMA POR TIPO CONTENEDOR
              // Contenedor 20
              $mDatos['nCifC201120'] = $xRT['CONTENEDOR_20_PORCENTAJE_CIF'];
              $mDatos['nMinC201120'] = $xRT['CONTENEDOR_20_MINIMA'];
              // Contenedor 40
              $mDatos['nCifC401120'] = $xRT['CONTENEDOR_40_PORCENTAJE_CIF'];
              $mDatos['nMinC401120'] = $xRT['CONTENEDOR_40_MINIMA'];
              // Carga Suelta
              $mDatos['nCifCs1120']  = $xRT['CARGA_SUELTA_PORCENTAJE_CIF'];
              $mDatos['nMinCs1120']  = $xRT['CARGA_SUELTA_MINIMA'];
            break;
    
            case "1121": // COBRO VARIABLE SEGUN CANTIDAD DE SACOS CON MINIMA
            case "280":  // COBRO VARIABLE SEGUN CANTIDAD DE SACOS CON MINIMA
              $cCamNiv = ($mDatos['cFcoId'] == '1121' && ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO")) ? "NIVELES_CANT_VEHICULOS" : "NIVELES";
              $mDatos['nNiv1121'] = $xRT[$cCamNiv];

              for($i=1; $i<=$mDatos['nNiv1121']; $i++) {
                $mDatos['vTarLiIn'.$i] = $xRT[$cCamNiv.'_'.$i.'_LIMITE_INFERIOR'];
                $mDatos['vTarLiSu'.$i] = $xRT[$cCamNiv.'_'.$i.'_LIMITE_SUPERIOR'];
                $mDatos['vTarPor' .$i] = $xRT[$cCamNiv.'_'.$i.'_VALOR'];
              }
            break;
    
            case "1123": // VALOR POR PEDIDO CON VALOR ADICIONAL
            case "1125": // VALOR POR PEDIDO CON VALOR ADICIONAL POR UNIDAD MAS COMISION ADICIONAL
              $mDatos['nUniMin1123'] = $xRT['UNIDADES_MINIMAS'];
              $mDatos['nVlrMin1123'] = $xRT['VALOR_POR_UNIDADES_MINIMAS'];
              $mDatos['nVlrAdi1123'] = $xRT['VALOR_ADICIONAL_POR_UNIDAD_ADICIONAL'];
            break;
            case "1126": // TRM OPERACION
              $mDatos['nCosAdi1126'] = $xRT['COSTO_ADICIONAL'];
              $mDatos['nVlrFij1126'] = $xRT['VALOR_FIJO'];
            break;
            case "1127": // COBRO ESCALONADO POR CANTIDAD DE CONTENEDORES
              $mDatos['nMin1127'] = $xRT['MINIMA'];
              $mDatos['nNiv1127'] = $xRT['NIVELES'];

              for ($i=1; $i<=$mDatos['nNiv1127']; $i++) {
                $mDatos['vTarLiIn'.$i] = $xRT['NIVELES_'.$i.'_LIMITE_INFERIOR'];
                $mDatos['vTarLiSu'.$i] = $xRT['NIVELES_'.$i.'_LIMITE_SUPERIOR'];
                $mDatos['vTarPor' .$i] = $xRT['NIVELES_'.$i.'_VALOR_POR_UNIDAD'];
              }
            break;
            case "282": // PORCENTAJE (%) A COBRAR SOBRE LOS PAGOS A TERCEROS (CONDICION ESPECIAL)
            case "1128": // PORCENTAJE (%) A COBRAR SOBRE LOS PAGOS A TERCEROS (CONDICION ESPECIAL)
              $mDatos['nPor1128'] = $xRT['PORCENTAJE'];
            break;
            case "1129": // COBRO POR HOJA PRINCIPAL Y HOJA SECUNDARIA
              $mDatos['nHojPpl1129'] = $xRT['VALOR_HOJA_PRINCIPAL'];
              $mDatos['nHojAdi1129'] = $xRT['VALOR_HOJA_ADICIONAL'];
            break;
            case "1130": // ESCALONADO POR %CIF + MINIMA
              $mDatos['nMin1130']    = $xRT['MINIMA_DEPOSITO_HABILITADO'];
              $mDatos['nMinDes1130'] = $xRT['MINIMA_DESCARGUE_DIRECTO'];
              $mDatos['nNiv1130']    = $xRT['NIVELES'];

              for ($i=1; $i<=$mDatos['nNiv1130']; $i++) {
                $mDatos['vTarLiIn'.$i] = $xRT['NIVELES_'.$i.'_LIMITE_INFERIOR'];
                $mDatos['vTarLiSu'.$i] = $xRT['NIVELES_'.$i.'_LIMITE_SUPERIOR'];
                $mDatos['vTarPor' .$i] = $xRT['NIVELES_'.$i.'_PORCENTAJE'];
              }
            break;
            case "1131": // VALOR FIJO DEPOSITO HABILITADO O DESCARGUE DIRECTO
            case "1136": // VALOR FIJO DEPOSITO HABILITADO O DESCARGUE DIRECTO EUROS
              $mDatos['nVlDepHab1131'] = $xRT['VALOR_FIJO_DEPOSITO_HABILITADO'];
              $mDatos['nDesDir1131']   = $xRT['VALOR_FIJO_DESCARGUE_DIRECTO'];
            break;
            case "1132": // ESCALONADO POR CANTIDAD DE ITEM EN FACTURA
              $mDatos['nNiv1132']     = $xRT['NIVELES'];
              $mDatos['nVlrItem1132'] = $xRT['VALOR_ADICIONAL_POR_ITEM'];
              $mDatos['nCanSer1132']  = $xRT['CANTIDAD_MINIMA_SERIALES'];
              $mDatos['nVlrSer1132']  = $xRT['VALOR_ADICIONAL_SERIALES'];
    
              for ($i=1; $i<=$mDatos['nNiv1132']; $i++) {
                $mDatos['vTarLiIn'.$i] = $xRT['NIVELES_'.$i.'_LIMITE_INFERIOR'];
                $mDatos['vTarLiSu'.$i] = $xRT['NIVELES_'.$i.'_LIMITE_SUPERIOR'];
                $mDatos['vTarPor' .$i] = $xRT['NIVELES_'.$i.'_VALOR_FIJO'];
              }
            break;
            case "1133": // ELABORACION DECLARACIONES (DIM Y DAV)
              $mDatos['nVlFijDim1133'] = $xRT['VALOR_FIJO_DIM'];
              $mDatos['nVlFijDav1133'] = $xRT['VALOR_FIJO_DAV'];
            break;
            case "1134": // PORCENTAJE SOBRE TRIBUTOS:(0,1% TRIBUTOS)
              $mDatos['nPorTri1134'] = $xRT['PORCENTAJE_SOBRE_TRIBUTOS'];
            break;
            case "1135": // VALOR FIJO + PORCENTAJE CIF
              $mDatos['nPor1135']     = $xRT['PORCENTAJE_CIF'];
              $mDatos['nVlrFijo1135'] = $xRT['VALOR_FIJO'];
            break;
            case "1137": // VALOR ESCALONADO POR CANTIDAD DE FACTURAS + VALOR ADICIONAL X CANTIDAD DE ITEMS
              $mDatos['nVlrItem1137'] = $xRT['VALOR_ADICIONAL_POR_ITEM'];
              $mDatos['nNiv1137']     = $xRT['NIVELES'];

              for ($i=1; $i<=$mDatos['nNiv1137']; $i++) {
                $mDatos['vTarLiIn'.$i] = $xRT['NIVELES_'.$i.'_LIMITE_INFERIOR'];
                $mDatos['vTarLiSu'.$i] = $xRT['NIVELES_'.$i.'_LIMITE_SUPERIOR'];
                $mDatos['vTarPor' .$i] = $xRT['NIVELES_'.$i.'_VALOR_POR_UNIDAD'];
              }
            break;
            default:
            break;
          }
          /***** Fin de Validacion de Campos de las Diferentes Formas de Pago *****/
          /***** Fin de Validacion de Campos de las Diferentes Formas de Pago *****/
          /***** Fin de Validacion de Campos de las Diferentes Formas de Pago *****/

          // Eliminando caracteres de salto de linea y tabuladores
          foreach ($mDatos as $cKey => $cValue) {
            $mDatos[$cKey] = str_replace($vBuscar,$vReempl,$mDatos[$cKey]);
          }

          // Guardando la tarifa
          # Creación Ajuste Contable
          $mRetorna = $this->fnGuardarTarifas($mDatos); //Se envian todos los datos que llegan por POST

          if($mRetorna[0] == "false") {
            $nSwErr = 1;
            $vError['TIPOERRX'] = "ERROR";
            $vError['LINEAERR'] = $xRT['LINEAIDX'];
            for ($i=1; $i<count($mRetorna); $i++) {
              $vError['DESERROR'] = $mRetorna[$i];
              $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
            }
          } else {
            $vError['TIPOERRX'] = "EXITOSO";
            $vError['LINEAERR'] = $xRT['LINEAIDX'];
            $vError['DESERROR'] = "Tarifa Guardada con Exito.";
            $objEstructuraTarifasFacturacion->fnGuardarErrorTarfiasFacturacion($vError);
          }
        }

        if ($nSwErr == 1) {
          $nSwitch = 1;
        }
      }

      if ($nSwitch == 0) {
        $mReturn[0] = "true";
      } else {
        $mReturn[0] = "false";
      }

      return $mReturn;
    }## function fnCrearTarifas($pArrayParametros) { ##
  } ## class cTarifasFacturacion { ##

  class cEstructurasTarfiasFacturacion {
    /**
     * Metodo que se encarga de Crear las Estructuras de las Tablas
     */
    function fnCrearEstructurasTarifasFacturacion($pArrayParametros){
      global $cAlfa; global $vSysStr;

      /**
       *Recibe como Parametro un vector con las siguientes posiciones:
       *$pArrayParametros['TIPOTABL] //TIPO DE ESTRUCTURA
       */

      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var int
       */
      $nSwitch = 0;

      /**
       * Matriz para Retornar Valores
       */
      $mReturn = array();

      /**
       * Reservando Primera Posición para retorna true o false
       */
      $mReturn[0] = "";

      /**
       * Llamando Metodo que hace conexion
       */
      $mReturnConexionTM = $this->fnConectarDBReporteTarifasConsolidado();
      if($mReturnConexionTM[0] == "true"){
        $xConexionTM = $mReturnConexionTM[1];
      }else{
        $nSwitch = 1;
        for($nR=1;$nR<count($mReturnConexionTM);$nR++){
          $mReturn[count($mReturn)] = $mReturnConexionTM[$nR];
        }
      }

      /**
       * Random para Nombre de la Tabla
       */
      $cTabTemp  = mt_rand(1000000000, 9999999999);

      switch($pArrayParametros['TIPOTABL']){
        case "REPORTE":
          $cTabla = "memretar".$cTabTemp;

          $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
        	$qNewTab .= "LINEAIDX                                 int(11)       NOT NULL AUTO_INCREMENT,"; //Id Autoincremantal
          $qNewTab .= "NIT_CLIENTE_O_ID_GRUPO                   varchar(20)   NOT NULL COMMENT 'Id de Cliente o Id Grupo',";
          $qNewTab .= "NOMBRE_CLIENTE_O_DESCRIPCION_GRUPO       varchar(255)  NOT NULL COMMENT 'Nombre del Cliente o Descripcion Grupo',";
          $qNewTab .= "APLICA_TARIFA_PARA                       varchar(10)   NOT NULL COMMENT 'Aplica Tarifa Para (CLIENTE/GRUPO)',";
          $qNewTab .= "ESTADO_CLIENTE_O_GRUPO                   varchar(20)   NOT NULL COMMENT 'Estado Cliente o Grupo',";
          $qNewTab .= "ESTADO_TARIFA                            varchar(10)   NOT NULL COMMENT 'Estado Tarifa',";
          if ($vSysStr['system_control_vigencia_tarifas'] == "SI") {
            $qNewTab .= "VIGENCIA_DESDE                         date          NOT NULL COMMENT 'Fecha de Vigencia Desde',";
            $qNewTab .= "VIGENCIA_HASTA                         date          NOT NULL COMMENT 'Fecha de Vigencia Hasta',";
            $qNewTab .= "NUEVA_VIGENCIA_DESDE                   date          NOT NULL COMMENT 'Nueva Fecha de Vigencia Desde',";
            $qNewTab .= "NUEVA_VIGENCIA_HASTA                   date          NOT NULL COMMENT 'Nueva Fecha de Vigencia Hasta',";
            $qNewTab .= "FECHA_MODIFICACION_VIGENCIA            date          NOT NULL COMMENT 'Fecha Modificacion Vigencia',";
            $qNewTab .= "ACTUALIZAR_VIGENCIA                    varchar(2)    NOT NULL COMMENT 'Indica que se debe actualizar la vigencia (SI/NO)',";
          }
          $qNewTab .= "ID_CONCEPTO_COBRO                        varchar(10)   NOT NULL COMMENT 'Id del Concepto de Cobro',";
          $qNewTab .= "DESCRIPCION_CONCEPTO_COBRO               varchar(255)  NOT NULL COMMENT 'Descripcion del Concepto de Cobro',";
          $qNewTab .= "DESCRIPCION_PERSONALIZADA_CONCEPTO_COBRO varchar(255)  NOT NULL COMMENT 'Descripcion Personalizada del Concepto de Cobro',";
          $qNewTab .= "ID_FORMA_COBRO                           varchar(10)   NOT NULL COMMENT 'Id Forma de Cobro',";
          $qNewTab .= "DESCRIPCION_FORMA_COBRO                  varchar(255)  NOT NULL COMMENT 'Descripcion Forma de Cobro',";
          $qNewTab .= "MONEDA                                   varchar(10)   NOT NULL COMMENT 'Id la Moneda',";
          $qNewTab .= "CONDICION_ESPECIAL                       text          NOT NULL COMMENT 'Condicion Especial',";
          $qNewTab .= "CONDICION_ESPECIAL_PERSONALIZADA         text          NOT NULL COMMENT 'Condicion Especial Personalizada',";
          $qNewTab .= "APLICA_TARIFA_POR                        varchar(20)   NOT NULL COMMENT 'Aplica Tarifa Por',";
          $qNewTab .= "ID_APLICA_TARIFA_POR                     varchar(20)   NOT NULL COMMENT 'Id Aplica Tarifa Por',";
          $qNewTab .= "DESCRIPCION_APLICA_TARIFA_POR            varchar(255)  NOT NULL COMMENT 'Descripcion Aplica Tarifa Por',";
          $qNewTab .= "TIPO_OPERACION                           varchar(12)   NOT NULL COMMENT 'Tipo Operación',";
          $qNewTab .= "SUCURSALES                               varchar(255)  NOT NULL COMMENT 'Sucursales',";
          $qNewTab .= "MODO_TRANSPORTE                          varchar(255)  NOT NULL COMMENT 'Tipo de Transporte',";
          //Entre estos campos el reporte genera los campos dinamicos de las tarifas
          $qNewTab .= "ID_USUARIO_CREACION                      varchar(12)    NOT NULL COMMENT 'Id Usuario Creacion',";
          $qNewTab .= "NOMBRE_USUARIO_CREACION                  varchar(255)   NOT NULL COMMENT 'Nombre Usuario Creacion',";
          $qNewTab .= "ID_USUARIO_MODIFICACION                  varchar(12)    NOT NULL COMMENT 'Id Usuario Modificado',";
          $qNewTab .= "NOMBRE_USUARIO_MODIFICACION              varchar(255)   NOT NULL COMMENT 'Nombre Usuario Modificado',";
          $qNewTab .= "FECHA_CREACION                           date           NOT NULL COMMENT 'Fecha creacion',";
          $qNewTab .= "FECHA_MODIFICACION                       date           NOT NULL COMMENT 'Fecha Modificado',";
          $qNewTab .= "COLUMNAS_RESALTADAS                      text           NOT NULL COMMENT 'Columnas Resaltadas',";
          $qNewTab .= "PRIMARY KEY (lineaidx)) ENGINE=InnoDB ";
          $xNewTab = mysql_query($qNewTab,$xConexionTM);

          if(!$xNewTab) {
            $nSwitch = 1;
            $mReturn[count($mReturn)] = "(".__LINE__.") Error al Crear Tabla Temporal .".mysql_error($xConexionTM);
          }

        break;
        case "TARIFAS":
          $cTabla = "memcatar".$cTabTemp;

          $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
        	$qNewTab .= "LINEAIDX                                 int(11)       NOT NULL AUTO_INCREMENT,"; //Id Autoincremantal
          $qNewTab .= "ID_TARIFA                                varchar(11)   NOT NULL COMMENT 'Id Autoincremantal',";
          $qNewTab .= "NIT_CLIENTE_O_ID_GRUPO                   varchar(20)   NOT NULL COMMENT 'Id de Cliente o Id Grupo',";
          $qNewTab .= "NOMBRE_CLIENTE_O_DESCRIPCION_GRUPO       varchar(255)  NOT NULL COMMENT 'Nombre del Cliente o Descripcion Grupo',";
          $qNewTab .= "APLICA_TARIFA_PARA                       varchar(10)   NOT NULL COMMENT 'Aplica Tarifa Para (CLIENTE/GRUPO)',";
          if ($vSysStr['system_control_vigencia_tarifas'] == "SI") {
            $qNewTab .= "VIGENCIA_DESDE                         date          NOT NULL COMMENT 'Fecha de Vigencia Desde',";
            $qNewTab .= "VIGENCIA_HASTA                         date          NOT NULL COMMENT 'Fecha de Vigencia Hasta',";
            $qNewTab .= "NUEVA_VIGENCIA_DESDE                   date          NOT NULL COMMENT 'Nueva Fecha de Vigencia Desde',";
            $qNewTab .= "NUEVA_VIGENCIA_HASTA                   date          NOT NULL COMMENT 'Nueva Fecha de Vigencia Hasta',";
            $qNewTab .= "ACTUALIZAR_VIGENCIA                    varchar(2)    NOT NULL COMMENT 'Indica que se debe actualizar la vigencia (SI/NO)',";
          }
          $qNewTab .= "ID_CONCEPTO_COBRO                        varchar(10)   NOT NULL COMMENT 'Id del Concepto de Cobro',";
          $qNewTab .= "DESCRIPCION_CONCEPTO_COBRO               varchar(255)  NOT NULL COMMENT 'Descripcion del Concepto de Cobro',";
          $qNewTab .= "DESCRIPCION_PERSONALIZADA_CONCEPTO_COBRO varchar(255)  NOT NULL COMMENT 'Descripcion Personalizada del Concepto de Cobro',";
          $qNewTab .= "ID_FORMA_COBRO                           varchar(10)   NOT NULL COMMENT 'Id Forma de Cobro',";
          $qNewTab .= "DESCRIPCION_FORMA_COBRO                  varchar(255)  NOT NULL COMMENT 'Descripcion Forma de Cobro',";
          $qNewTab .= "MONEDA                                   varchar(10)   NOT NULL COMMENT 'Id la Moneda',";
          $qNewTab .= "CONDICION_ESPECIAL                       text          NOT NULL COMMENT 'Condicion Especial',";
          $qNewTab .= "CONDICION_ESPECIAL_PERSONALIZADA         text          NOT NULL COMMENT 'Condicion Especial Personalizada',";
          $qNewTab .= "APLICA_TARIFA_POR                        varchar(20)   NOT NULL COMMENT 'Aplica Tarifa Por',";
          $qNewTab .= "ID_APLICA_TARIFA_POR                     varchar(20)   NOT NULL COMMENT 'Id Aplica Tarifa Por',";
          $qNewTab .= "DESCRIPCION_APLICA_TARIFA_POR            varchar(255)  NOT NULL COMMENT 'Descripcion Aplica Tarifa Por',";
          $qNewTab .= "TIPO_OPERACION                           varchar(12)   NOT NULL COMMENT 'Tipo Operación',";
          $qNewTab .= "SUCURSALES                               varchar(255)  NOT NULL COMMENT 'Sucursales',";
          $qNewTab .= "MODO_TRANSPORTE                          varchar(255)  NOT NULL COMMENT 'Tipo de Transporte',";
          $qNewTab .= "PRIMARY KEY (lineaidx)) ENGINE=InnoDB ";
          $xNewTab = mysql_query($qNewTab,$xConexionTM);

          if(!$xNewTab) {
            $nSwitch = 1;
            $mReturn[count($mReturn)] = "(".__LINE__.") Error al Crear Tabla Temporal .".mysql_error($xConexionTM);
          }

        break;
        case "ERRORES":
          $cTabla = "memerror".$cTabTemp;

          $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
          $qNewTab .= "LINEAIDX INT(11) NOT NULL AUTO_INCREMENT,";//LINEA
          $qNewTab .= "LINEAERR VARCHAR(10) NOT NULL,";           //LINEA DEL ARCHIVO
          $qNewTab .= "TIPOERRX VARCHAR(20) NOT NULL,";           //TIPO DE ERROR
          $qNewTab .= "DESERROR TEXT NOT NULL,";                  //DESCRIPCION DEL ERROR
          $qNewTab .= "PRIMARY KEY (LINEAIDX), ";
          $qNewTab .= "KEY (TIPOERRX)) ENGINE=MyISAM ";
          $xNewTab  = mysql_query($qNewTab,$xConexionTM);
          //f_Mensaje(__FILE__,__LINE__,$qNewTab);

          if(!$xNewTab) {
            $nSwitch = 1;
            $mReturn[count($mReturn)] = "Error al Crear Tabla Temporal de Errores.".mysql_error($xConexionTM);
          }
        break;
        default:
          $nSwitch = 1;
          $mReturn[count($mReturn)] = "No se Recibio Tipo de Estructura a Crear";
        break;
      }

      if($nSwitch == 0){
        $mReturn[0] = "true"; $mReturn[1] = $cTabla;
      }else{
        $mReturn[0] = "false";
      }
      return $mReturn;
    } ## function fnCrearEstructurasTarifasFacturacion(){ ##

    function fnCrearCampo($pArrayParametros){
      global $cAlfa;

      /**
       *Recibe como Parametro un vector con las siguientes posiciones:
       *$pArrayParametros['TIPOTABL] //TIPO DE ESTRUCTURA
       */

      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var int
       */
      $nSwitch = 0;

      /**
       * Matriz para Retornar Valores
       */
      $mReturn = array();

      /**
       * Reservando Primera Posición para retorna true o false
       */
      $mReturn[0] = "";

      /**
       * Llamando Metodo que hace conexion
       */
      $mReturnConexionTM = $this->fnConectarDBReporteTarifasConsolidado();
      if($mReturnConexionTM[0] == "true"){
        $xConexionTM = $mReturnConexionTM[1];
      }else{
        $nSwitch = 1;
        for($nR=1;$nR<count($mReturnConexionTM);$nR++){
          $mReturn[count($mReturn)] = $mReturnConexionTM[$nR];
        }
      }

      try {
        $qAltTab  = "ALTER TABLE $cAlfa.{$pArrayParametros['tablaxxx']} ADD {$pArrayParametros['camponew']} {$pArrayParametros['camtipxx']} NOT NULL AFTER {$pArrayParametros['camporef']}";
        $xAltTab  = mysql_query($qAltTab,$xConexionTM);
        //f_Mensaje(__FILE__,__LINE__,$qAltTab);
        if(!$xAltTab) {
          $nSwitch = 1;
          $mReturn[count($mReturn)] = "Error al Crear el campo [{$pArrayParametros['camponew']}] en la tabla temporal.".mysql_error($xConexionTM);
        }
      } catch (\Exception $e) {
        $nSwitch = 1;
        $mReturn[count($mReturn)] = "Error al Crear el campo [{$pArrayParametros['camponew']}] en la tabla temporal.";
      }
      
      if ($nSwitch == 0) {
        $mReturn[0] = "true";
      } else {
        $mReturn[0] = "false";
      }
      return $mReturn;
    } ## function fnCrearEstructurasTarifasFacturacion(){ ##
    
    /**
     * Metodo que realiza la conexion.
     */
    function fnConectarDBReporteTarifasConsolidado(){
      global $cAlfa;

      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var int
       */
      $nSwitch = 0;

      /**
       * Matriz para Retornar Valores.
       * 
       * @var array
       */
      $mReturn = array();

      /**
       * Reservo Primera Posicion para retorna true o false
       */
      $mReturn[0] = "";

      $xConexion99 = mysql_connect(OC_SERVER,OC_USERROBOT,OC_PASSROBOT) or die("El Sistema no Logro Conexion con ".OC_SERVER);
      if($xConexion99){
        $nSwitch = 0;
      }else{
        $nSwitch = 1;
        $mReturn[count($mReturn)] = "El Sistema no Logro Conexion con ".OC_SERVER;
      }

      if($nSwitch == 0){
        $mReturn[0] = "true"; $mReturn[1] = $xConexion99;
      }else{
        $mReturn[0] = "false";
      }
      return $mReturn;
    }##function fnConectarDBReporteTarifasConsolidado(){##

    /**
     * Metodo que realiza el reinicio de la conexion.
     */
    function fnReiniciarConexionDBRTarifasFacturacion($pConexion){
      global $cHost;  global $cUserHost;  global $cPassHost;

      mysql_close($pConexion);
      $xConexion01 = mysql_connect($cHost,$cUserHost,$cPassHost,TRUE);

      return $xConexion01;
    }##function fnReiniciarConexionDBRTarifasFacturacion(){##

    /**
     * Metodo que se encarga de Guardar los Errores Generados por los Metodos de Interfaces.
     */
    function fnGuardarErrorTarfiasFacturacion($pArrayParametros){
      global $xConexion01; global $cAlfa; global $vSysStr; global $kUser;

      /**
       * Recibe como parametro un vector con los siguientes campos
       * $pArrayParametros['TABLAERR']  //TABLA ERROR
       * $pArrayParametros['LINEAERR']  //LINEA ERROR
       * $pArrayParametros['TIPOERRX']  //TIPO DE ERROR
       * $pArrayParametros['DESERROR']  //DESCRIPCION DEL ERROR
       * $pArrayParametros['MOSTRARX']  //INDICA SI SE DEBE PINTAR O NO EL ERROR.  EN SI O VACIO SE PINTA.
       */

      /**
       * Variables para reemplazar caracteres especiales.
       * 
       * @var array
       */
      $cBuscar = array('"',"'",chr(13),chr(10),chr(27),chr(9));
      $cReempl = array('\"',"\'"," "," "," "," ");

      if($pArrayParametros['TABLAERR'] != ""){

        $qInsert =  array(array('NAME'=>'LINEAERR','VALUE'=>$pArrayParametros['LINEAERR']                                 ,'CHECK'=>'NO'),
                          array('NAME'=>'TIPOERRX','VALUE'=>$pArrayParametros['TIPOERRX']                                 ,'CHECK'=>'NO'),
                          array('NAME'=>'DESERROR','VALUE'=>str_replace($cBuscar,$cReempl,$pArrayParametros['DESERROR'])  ,'CHECK'=>'NO'));

        f_MySql("INSERT",$pArrayParametros['TABLAERR'],$qInsert,$xConexion01,$cAlfa);
      }
    }##function fnGuardarErrorTarfiasFacturacion($pArrayParametros){##

    /*+ 
     * Metodo para capturar la informacion del motor de DB asosciada al query.
     */
    function fnMysqlQueryInfo($xConexion,$xQueryTime) {

      global $cSystemPath; global $cAlfa; global $_SERVER; global $kDf;

      $xMysqlInfo = mysql_info($xConexion);

      ereg("Changed: ([0-9]*)",$xMysqlInfo,$vChanged);
      ereg("Deleted: ([0-9]*)",$xMysqlInfo,$vDeleted);
      ereg("Duplicates: ([0-9]*)",$xMysqlInfo,$vDuplicates);
      ereg("Records: ([0-9]*)",$xMysqlInfo,$vRecords);
      ereg("Rows matched: ([0-9]*)",$xMysqlInfo,$vRows_matched);
      ereg("Skipped: ([0-9]*)",$xMysqlInfo,$vSkipped);
      ereg("Warnings: ([0-9]*)",$xMysqlInfo,$vWarnings);

      $cQueryInfo  = "|";
      $cQueryInfo .= "Changed~{$vChanged[1]}|";
      $cQueryInfo .= "Deleted~{$vDeleted[1]}|";
      $cQueryInfo .= "Duplicates~{$vDuplicates[1]}|";
      $cQueryInfo .= "Records~{$vRecords[1]}|";
      $cQueryInfo .= "Rows matched~{$vRows_matched[1]}|";
      $cQueryInfo .= "Skipped~{$vSkipped[1]}|";
      $cQueryInfo .= "Warnings~{$vWarnings[1]}|";
      $cQueryInfo .= "Affected Rows~".mysql_affected_rows($xConexion)."|";
      $cQueryInfo .= "Query Time~".number_format($xQueryTime,2)."|";
      $cQueryInfo .= "Error Number~".mysql_errno($xConexion)."|";
      $cQueryInfo .= "Error Description~".mysql_error($xConexion)."|";

      $cIP = "";
      $cHost = "";
      if ($_SERVER['HTTP_CLIENT_IP'] != "") {
        $cIP   = $_SERVER['HTTP_CLIENT_IP'];
        $cHost = $_SERVER['HTTP_VIA'];
      }elseif ($_SERVER['HTTP_X_FORWARDED_FOR'] != "") {
        $cIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
        $cHost = $_SERVER['HTTP_VIA'];
      }else{
        $cIP = $_SERVER['REMOTE_ADDR'];
        $cHost = $_SERVER['HTTP_VIA'];
      }

      if ($cHost == "") {
        $cHost = $cIP;
      }

      $copenComex  = "|";
      $copenComex .= "{$kDf[4]}~";
      $copenComex .= "{$_SERVER['PHP_SELF']}~";
      $copenComex .= "$cIP~";
      $copenComex .= "$cHost~";
      $copenComex .= "{$kDf[3]}~";
      $copenComex .= date("Y-m-d")."~";
      $copenComex .= date("H:i:s");
      $copenComex .= "|";
      $xopenComex = mysql_query("SET @opencomex = \"$copenComex\"",$xConexion);
      $xQueryInfo = mysql_query("SET @mysqlinfo = \"$cQueryInfo\"",$xConexion);
    } ## function f_Mysql_Query_Info($xConexion,$xQueryTime) {
    ## Metodo para capturar la informacion del motor de DB asosciada al query
  }
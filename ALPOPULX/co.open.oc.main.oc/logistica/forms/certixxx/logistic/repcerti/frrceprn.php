<?php
  /**
   * Generar Archivo de Excel - Reporte Certificaciones.
   * --- Descripcion: Permite Generar Impreso de Excel con la Información del Movimiento de las Certificaciones.
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
   * @package opencomex
   * @version 001
   */

  include("../../../../../financiero/libs/php/utility.php");

  define(_NUMREG_,100);

  /**
   * Variables de control de errores.
   * 
   * @var int
   */
  $nSwitch = 0;

  /**
   * Variable para almacenar los mensajes de error.
   * 
   * @var string
   */
  $cMsj = "\n";

  /**
   * Variable para almacenar el anio actual.
   * 
   * @var string
   */
  $cAnio = date('Y');

  // Validaciones
  // Valida que el cliente exista
  if ($gCliId != "") {
    $qCliente  = "SELECT ";
    $qCliente .= "cliidxxx ";
    $qCliente .= "FROM $cAlfa.lpar0150 ";
    $qCliente .= "WHERE ";
    $qCliente .= "cliidxxx = \"$gCliId\" AND ";
    $qCliente .= "cliclixx = \"SI\" AND ";
    $qCliente .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
    $xCliente  = f_MySql("SELECT","",$qCliente,$xConexion01,"");
    if (mysql_num_rows($xCliente) == 0) {
      $nSwitch = 1;
      $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
      $cMsj .= "El Cliente Seleccionado no Existe.\n";
    }
  }

  // Valida si existe la MIF seleccionada
  if ($gComCod != "" && $gComCsc != "") {
    $qMifCab  = "SELECT ";
    $qMifCab .= "$cAlfa.lmca$gPerAno.* ";
    $qMifCab .= "FROM $cAlfa.lmca$gPerAno ";
    $qMifCab .= "WHERE ";
    $qMifCab .= "$cAlfa.lmca$gPerAno.comidxxx = \"M\" AND ";
    $qMifCab .= "$cAlfa.lmca$gPerAno.comcodxx = \"$gComCod\" AND ";
    $qMifCab .= "$cAlfa.lmca$gPerAno.comcscxx = \"$gComCsc\" AND ";
    $qMifCab .= "$cAlfa.lmca$gPerAno.comcsc2x = \"$gComCsc2\" AND ";
    $qMifCab .= "$cAlfa.lmca$gPerAno.cliidxxx = \"$gCliId\" AND ";
    $qMifCab .= "$cAlfa.lmca$gPerAno.regestxx IN (\"ACTIVO\",\"CERTIFICADO_PARCIAL\") LIMIT 0,1 ";
    $xMifCab  = f_MySql("SELECT","",$qMifCab,$xConexion01,"");
    if (mysql_num_rows($xMifCab) == 0) {
      $nSwitch = 1;
      $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
      $cMsj .= "La MIF Seleccionada no Existe.\n";
    }
  }

  // Valida que el deposito exista
  if ($gDepNum != "") {
    $qDeposito  = "SELECT ";
    $qDeposito .= "depnumxx ";
    $qDeposito .= "FROM $cAlfa.lpar0155 ";
    $qDeposito .= "WHERE ";
    $qDeposito .= "depnumxx = \"$gDepNum\" AND ";
    $qDeposito .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
    $xDeposito  = f_MySql("SELECT","",$qDeposito,$xConexion01,"");
    if (mysql_num_rows($xDeposito) == 0) {
      $nSwitch = 1;
      $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
      $cMsj .= "El Deposito Seleccionado no Existe.\n";
    }
  }

  // Valida que la organizacion de venta exista
  if ($gOrvSap != "") {
    $qOrgVenta  = "SELECT ";
    $qOrgVenta .= "orvsapxx, ";
    $qOrgVenta .= "orvdesxx, ";
    $qOrgVenta .= "regestxx ";
    $qOrgVenta .= "FROM $cAlfa.lpar0001 ";                        
    $qOrgVenta .= "WHERE ";
    $qOrgVenta .= "orvsapxx = \"$gOrvSap\" AND ";
    $qOrgVenta .= "regestxx = \"ACTIVO\" ";
    $xOrgVenta  = f_MySql("SELECT","",$qOrgVenta,$xConexion01,"");
    if (mysql_num_rows($xOrgVenta) == 0) {
      $nSwitch = 1;
      $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
      $cMsj .= "La Organizacion de Venta Seleccionada no Existe.\n";
    }
  }

  // Valida que la oficina de venta exista
  if ($gOfvSap != "") {
    $qOfiVenta  = "SELECT ";
    $qOfiVenta .= "ofvsapxx, ";
    $qOfiVenta .= "ofvdesxx, ";
    $qOfiVenta .= "regestxx ";
    $qOfiVenta .= "FROM $cAlfa.lpar0002 ";                        
    $qOfiVenta .= "WHERE ";
    $qOfiVenta .= "orvsapxx = \"$gOrvSap\" AND ";
    $qOfiVenta .= "ofvsapxx = \"$gOfvSap\" AND ";
    $qOfiVenta .= "regestxx = \"ACTIVO\" ";
    $qOfiVenta .= "ORDER BY ofvsapxx ";
    $xOfiVenta  = f_MySql("SELECT","",$qOfiVenta,$xConexion01,"");  
    if (mysql_num_rows($xOfiVenta) == 0) {
      $nSwitch = 1;
      $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
      $cMsj .= "La Oficina de Venta Seleccionada no Existe.\n";
    }    
  }

  if ($gDesde == "" || $gHasta == "" || $gDesde == "0000-00-00" || $gHasta == "0000-00-00") {
    $nSwitch = 1;
    $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
    $cMsj .= "Debe Seleccionar un Rango de Fechas.\n";
  }
  // Fin Validaciones

  $mData = array();
  if ($nSwitch == 0) {

    // Valida si el año de instalacion del modulo es menor al año actual, para consultar sobre los dos ultimos años
    $nAnioDesde = substr($gDesde, 0, 4);
    $cAnioAnt   = ($nAnioDesde < $vSysStr['logistica_ano_instalacion_modulo']) ? $vSysStr['logistica_ano_instalacion_modulo'] : $nAnioDesde;

    $mClientes = array();
    $mDeposito = array();
    $nCantReg  = 0;
    for ($cAnio=$cAnioAnt;$cAnio<=date('Y');$cAnio++) {
      // Consulta la cabecera de la certificacion
      $qCertiCab  = "SELECT ";
      $qCertiCab .= "$cAlfa.lcca$cAnio.* ";
      $qCertiCab .= "FROM $cAlfa.lcca$cAnio ";
      $qCertiCab .= "WHERE ";
      if ($gCliId != "") {
        $qCertiCab .= "$cAlfa.lcca$cAnio.cliidxxx = \"$gCliId\" AND ";
      }
      if ($gDepNum != "") {
        $qCertiCab .= "$cAlfa.lcca$cAnio.depnumxx = \"$gDepNum\" AND ";
      }
      if ($gMifId != "") {
        $qCertiCab .= "$cAlfa.lcca$cAnio.mifidxxx = \"$gMifId\" AND ";
        $qCertiCab .= "$cAlfa.lcca$cAnio.mifidano = \"$gMifAnio\" AND ";
      }
      $qCertiCab .= "$cAlfa.lcca$cAnio.regfcrex BETWEEN \"$gDesde\" AND \"$gHasta\" ";
      if ($gEstCert != "") {
        $qCertiCab .= "AND $cAlfa.lcca$cAnio.regestxx = \"$gEstCert\" ";
      }
      $qCertiCab .= "ORDER BY $cAlfa.lcca$cAnio.ceridxxx ASC ";
      $xCertiCab  = f_MySql("SELECT","",$qCertiCab,$xConexion01,"");
      // echo $qCertiCab . " - " . mysql_num_rows($xCertiCab) . "<br>";

      if (mysql_num_rows($xCertiCab) > 0) {
        while ($xRCC = mysql_fetch_array($xCertiCab)) {
          $cNumMif = "";
          $nItem   = 0;

          // Consulta la información del cliente
          if(in_array($xRCC['cliidxxx'], $mClientes)){
            $xRCC['clinomxx'] = $mClientes[$xRCC['cliidxxx']]['clinomxx'];
            $xRCC['clisapxx'] = $mClientes[$xRCC['cliidxxx']]['clisapxx'];
          } else {
            $qCliente  = "SELECT ";
            $qCliente .= "cliidxxx, ";
            $qCliente .= "IF($cAlfa.lpar0150.clinomxx != \"\",$cAlfa.lpar0150.clinomxx,(TRIM(CONCAT($cAlfa.lpar0150.clinomxx,\" \",$cAlfa.lpar0150.clinom1x,\" \",$cAlfa.lpar0150.clinom2x,\" \",$cAlfa.lpar0150.cliape1x,\" \",$cAlfa.lpar0150.cliape2x)))) AS clinomxx, ";
            $qCliente .= "clisapxx ";
            $qCliente .= "FROM $cAlfa.lpar0150 ";
            $qCliente .= "WHERE ";
            $qCliente .= "cliidxxx = \"{$xRCC['cliidxxx']}\" LIMIT 0,1 ";
            $xCliente  = f_MySql("SELECT", "", $qCliente, $xConexion01, "");
            if (mysql_num_rows($xCliente)) {
              $vCliente  = mysql_fetch_array($xCliente);
              $mClientes[$xRCC['cliidxxx']][] = $vCliente;
              $xRCC['clinomxx'] = $vCliente['clinomxx'];
              $xRCC['clisapxx'] = $vCliente['clisapxx'];
            }
          }

          // Consulta el numero de la MIF
          if ($xRCC['certipxx'] == "AUTOMATICA") {
            $cMifAnio = $xRCC['mifidano'];
            $qMifCab  = "SELECT ";
            $qMifCab .= "lmca$cMifAnio.comidxxx, ";
            $qMifCab .= "lmca$cMifAnio.comcodxx, ";
            $qMifCab .= "lmca$cMifAnio.comprexx, ";
            $qMifCab .= "lmca$cMifAnio.comcscxx, ";
            $qMifCab .= "lmca$cMifAnio.comcsc2x ";
            $qMifCab .= "FROM $cAlfa.lmca$cMifAnio ";
            $qMifCab .= "WHERE ";
            $qMifCab .= "lmca$cMifAnio.mifidxxx = \"{$xRCC['mifidxxx']}\" LIMIT 0,1";
            $xMifCab  = f_MySql("SELECT", "", $qMifCab, $xConexion01, "");
            if (mysql_num_rows($xMifCab) > 0) {
              $vMifCab = mysql_fetch_array($xMifCab);
              $cNumMif = $vMifCab['comidxxx'] ."-". $vMifCab['comprexx'] ."-". $vMifCab['comcscxx'];
            }
          }

          // Consulta informacion del deposito
          if(in_array($xRCC['depnumxx'], $mDeposito)){
            $xRCC['orvdesxx'] = $mDeposito[$xRCC['depnumxx']]['orvdesxx'];
            $xRCC['ofvdesxx'] = $mDeposito[$xRCC['depnumxx']]['orvdesxx'];
          } else {
            $qDeposito  = "SELECT ";
            $qDeposito .= "depnumxx, ";
            $qDeposito .= "lpar0001.orvsapxx, ";
            $qDeposito .= "lpar0001.orvdesxx, ";
            $qDeposito .= "lpar0002.ofvsapxx, ";
            $qDeposito .= "lpar0002.ofvdesxx ";
            $qDeposito .= "FROM $cAlfa.lpar0155 ";
            $qDeposito .= "LEFT JOIN $cAlfa.lpar0001 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0001.orvsapxx ";
            $qDeposito .= "LEFT JOIN $cAlfa.lpar0002 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0002.orvsapxx AND $cAlfa.lpar0155.ofvsapxx = $cAlfa.lpar0002.ofvsapxx ";
            $qDeposito .= "WHERE ";
            $qDeposito .= "depnumxx = \"{$xRCC['depnumxx']}\" LIMIT 0,1 ";
            $xDeposito  = f_MySql("SELECT", "", $qDeposito, $xConexion01, "");
            if (mysql_num_rows($xDeposito)) {
              $vDeposito  = mysql_fetch_array($xDeposito);
              $mDeposito[$xRCC['depnumxx']][] = $vDeposito;
              $xRCC['orvdesxx'] = $vDeposito['orvdesxx'];
              $xRCC['ofvdesxx'] = $vDeposito['ofvdesxx'];
            }
          }

          // Consulta el detalle de la certificacion
          $qCertiDet  = "SELECT ";
          $qCertiDet .= "$cAlfa.lcde$cAnio.*, ";
          $qCertiDet .= "IF($cAlfa.lcde$cAnio.sersapxx != \"\",$cAlfa.lpar0011.serdesxx,\"\") AS serdesxx, ";
          $qCertiDet .= "IF($cAlfa.lcde$cAnio.obfidxxx != \"\",$cAlfa.lpar0004.obfdesxx,\"\") AS obfdesxx, ";
          $qCertiDet .= "IF($cAlfa.lcde$cAnio.ufaidxxx != \"\",$cAlfa.lpar0006.ufadesxx,\"\") AS ufadesxx, ";
          $qCertiDet .= "IF($cAlfa.lcde$cAnio.cebidxxx != \"\",$cAlfa.lpar0010.cebcodxx,\"\") AS cebcodxx, ";
          $qCertiDet .= "IF($cAlfa.lcde$cAnio.cebidxxx != \"\",$cAlfa.lpar0010.cebdesxx,\"\") AS cebdesxx ";
          $qCertiDet .= "FROM $cAlfa.lcde$cAnio ";
          $qCertiDet .= "LEFT JOIN $cAlfa.lpar0004 ON $cAlfa.lcde$cAnio.obfidxxx = $cAlfa.lpar0004.obfidxxx ";
          $qCertiDet .= "LEFT JOIN $cAlfa.lpar0006 ON $cAlfa.lcde$cAnio.ufaidxxx = $cAlfa.lpar0006.ufaidxxx ";
          $qCertiDet .= "LEFT JOIN $cAlfa.lpar0010 ON $cAlfa.lcde$cAnio.cebidxxx = $cAlfa.lpar0010.cebidxxx ";
          $qCertiDet .= "LEFT JOIN $cAlfa.lpar0011 ON $cAlfa.lcde$cAnio.sersapxx = $cAlfa.lpar0011.sersapxx ";
          $qCertiDet .= "WHERE ";
          $qCertiDet .= "$cAlfa.lcde$cAnio.ceridxxx = \"{$xRCC['ceridxxx']}\" ";
          $qCertiDet .= "ORDER BY $cAlfa.lcde$cAnio.ceridxxx ASC ";
          $xCertiDet  = f_MySql("SELECT","",$qCertiDet,$xConexion01,"");
          // echo $qCertiDet ." - ". mysql_num_rows($xCertiDet) . "<br>";
          // die();

          if (mysql_num_rows($xCertiDet) > 0) {
            while ($xRCD = mysql_fetch_array($xCertiDet)) {
              $nCantReg++;
              if (($nCantReg % _NUMREG_) == 0) { $xConexion01 = fnReiniciarConexion(); }

              // Informacion de cabecera de la certificacion
              $nInd_mData = count($mData);
              $mData[$nInd_mData]['itemxxxx'] = ($nItem += 1);      // Numero Item
              $mData[$nInd_mData]['numcerti'] = $xRCC['comidxxx'] ."-". $xRCC['comprexx'] ."-". $xRCC['comcscxx']; // Numero Certificacion
              $mData[$nInd_mData]['cliidxxx'] = $xRCC['cliidxxx'];  // Nit del Cliente
              $mData[$nInd_mData]['clisapxx'] = $xRCC['clisapxx'];  // Cod. SAP del Cliente
              $mData[$nInd_mData]['clinomxx'] = $xRCC['clinomxx'];  // Nombre del Cliente
              $mData[$nInd_mData]['nummifxx'] = $cNumMif;           // Numero de la MIF
              $mData[$nInd_mData]['cerfdexx'] = $xRCC['cerfdexx'];  // Fecha Vigencia Desde
              $mData[$nInd_mData]['cerfhaxx'] = $xRCC['cerfhaxx'];  // Fecha Vigencia Hasta
              $mData[$nInd_mData]['depnumxx'] = $xRCC['depnumxx'];  // Numero del Deposito
              $mData[$nInd_mData]['orvdesxx'] = $xRCC['orvdesxx'];  // Descripcion Organizacion de Venta
              $mData[$nInd_mData]['ofvdesxx'] = $xRCC['ofvdesxx'];  // Descripcion Oficina de Venta
              // Informacion de detalle de la certificacion
              $mData[$nInd_mData]['sersapxx'] = $xRCD['sersapxx'];  // Cod. SAP del Servicio
              $mData[$nInd_mData]['serdesxx'] = $xRCD['serdesxx'];  // Descripcion del Servicio
              $mData[$nInd_mData]['subdesxx'] = $xRCD['subdesxx'];  // Descripcion del Subservicio
              $mData[$nInd_mData]['obfdesxx'] = $xRCD['obfdesxx'];  // Descripcion del Objeto Facturable
              $mData[$nInd_mData]['ufadesxx'] = $xRCD['ufadesxx'];  // Descripcion de la Unidad Facturable
              $mData[$nInd_mData]['cebcodxx'] = $xRCD['cebcodxx'];  // Codigo CEBE
              $mData[$nInd_mData]['cebdesxx'] = $xRCD['cebdesxx'];  // Descripcion CEBE
              $mData[$nInd_mData]['basexxxx'] = $xRCD['basexxxx'];  // Base
              $mData[$nInd_mData]['cerdconx'] = $xRCD['cerdconx'];  // Condicion
              $mData[$nInd_mData]['cerdestx'] = $xRCD['cerdestx'];  // Estatus
              // Informacion de cabecera de la certificacion
              $mData[$nInd_mData]['cerobsxx'] = $xRCC['cerobsxx'];  // Observaciones Generales
              $mData[$nInd_mData]['cerusufa'] = $xRCC['cerusufa'];  // Usuario Certificacion para Facturacion
              $mData[$nInd_mData]['cerobsfa'] = $xRCC['cerobsfa'];  // Observacion Certificacion para Facturacion
              $mData[$nInd_mData]['cerusufi'] = $xRCC['cerusufi'];  // Usuario Certificacion para Financiero
              $mData[$nInd_mData]['cerobsfi'] = $xRCC['cerobsfi'];  // Observacion Certificacion para Financiero
              $mData[$nInd_mData]['cerusuap'] = ($xRCC['regestxx'] == "CERTIFICADO" && $xRCC['cerusuar'] != "") ? $xRCC['cerusuar'] : "";  // Usuario Aprobado Financiero
              $mData[$nInd_mData]['cerobsap'] = ($xRCC['regestxx'] == "CERTIFICADO" && $xRCC['cerusuar'] != "") ? $xRCC['cerobsar'] : "";  // Observacion Aprobado Financiero
              $mData[$nInd_mData]['cerusure'] = ($xRCC['regestxx'] == "ENPROCESO" && $xRCC['cerusuar'] != "")   ? $xRCC['cerusuar'] : "";  // Usuario Rechazado Financiero
              $mData[$nInd_mData]['cerobsre'] = ($xRCC['regestxx'] == "ENPROCESO" && $xRCC['cerusuar'] != "")   ? $xRCC['cerobsar'] : "";  // Observacion Rechazado Financiero
              $mData[$nInd_mData]['cerusuan'] = $xRCC['cerusuan'];  // Usuario Anulacion
              $mData[$nInd_mData]['cerobsan'] = $xRCC['cerobsan'];  // Observacion Anulacion
              $mData[$nInd_mData]['regestxx'] = str_replace("_", " ", $xRCC['regestxx']); // Estado
              $mData[$nInd_mData]['regfmodx'] = $xRCC['regfmodx'];  // Fecha Modificado
            }
          } else {
            $nInd_mData = count($mData);
            $mData[$nInd_mData]['itemxxxx'] = ($nItem += 1);      // Numero Item
            $mData[$nInd_mData]['numcerti'] = $xRCC['comidxxx'] ."-". $xRCC['comprexx'] ."-". $xRCC['comcscxx']; // Numero Certificacion
            $mData[$nInd_mData]['cliidxxx'] = $xRCC['cliidxxx'];  // Nit del Cliente
            $mData[$nInd_mData]['clisapxx'] = $xRCC['clisapxx'];  // Cod. SAP del Cliente
            $mData[$nInd_mData]['clinomxx'] = $xRCC['clinomxx'];  // Nombre del Cliente
            $mData[$nInd_mData]['nummifxx'] = $cNumMif;           // Numero de la MIF
            $mData[$nInd_mData]['cerfdexx'] = $xRCC['cerfdexx'];  // Fecha Vigencia Desde
            $mData[$nInd_mData]['cerfhaxx'] = $xRCC['cerfhaxx'];  // Fecha Vigencia Hasta
            $mData[$nInd_mData]['depnumxx'] = $xRCC['depnumxx'];  // Numero del Deposito
            $mData[$nInd_mData]['cerobsxx'] = $xRCC['cerobsxx'];  // Observaciones Generales
            $mData[$nInd_mData]['cerusufa'] = $xRCC['cerusufa'];  // Usuario Certificacion para Facturacion
            $mData[$nInd_mData]['cerobsfa'] = $xRCC['cerobsfa'];  // Observacion Certificacion para Facturacion
            $mData[$nInd_mData]['cerusufi'] = $xRCC['cerusufi'];  // Usuario Certificacion para Financiero
            $mData[$nInd_mData]['cerobsfi'] = $xRCC['cerobsfi'];  // Observacion Certificacion para Financiero
            $mData[$nInd_mData]['cerusuap'] = ($xRCC['regestxx'] == "CERTIFICADO" && $xRCC['cerusuar'] != "") ? $xRCC['cerusuar'] : "";  // Usuario Aprobado Financiero
            $mData[$nInd_mData]['cerobsap'] = ($xRCC['regestxx'] == "CERTIFICADO" && $xRCC['cerusuar'] != "") ? $xRCC['cerobsar'] : "";  // Observacion Aprobado Financiero
            $mData[$nInd_mData]['cerusure'] = ($xRCC['regestxx'] == "ENPROCESO" && $xRCC['cerusuar'] != "")   ? $xRCC['cerusuar'] : "";  // Usuario Rechazado Financiero
            $mData[$nInd_mData]['cerobsre'] = ($xRCC['regestxx'] == "ENPROCESO" && $xRCC['cerusuar'] != "")   ? $xRCC['cerobsar'] : "";  // Observacion Rechazado Financiero
            $mData[$nInd_mData]['cerusuan'] = $xRCC['cerusuan'];  // Usuario Anulacion
            $mData[$nInd_mData]['cerobsan'] = $xRCC['cerobsan'];  // Observacion Anulacion
            $mData[$nInd_mData]['regestxx'] = str_replace("_", " ", $xRCC['regestxx']); // Estado
            $mData[$nInd_mData]['regfmodx'] = $xRCC['regfmodx'];  // Fecha Modificado
          }
        }
      }
    }
  }  else {
    f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.");
  }

  // echo "<pre>";
  // print_r($mData);
  // die();

  // Inica a pintar el Excel
  if (count($mData) > 0) {
    $data     = '';
    $cNomFile = "MOVIMIENTO_CERTIFICACIONES_".date("YmdHis").".xls";

    if ($_SERVER["SERVER_PORT"] != "") {
      $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;
    } else {
      $cFile = "{$OPENINIT['pathdr']}/opencomex/" . $vSysStr['system_download_directory'] . "/" . $cNomFile;
    }

    if (file_exists($cFile)) {
      unlink($cFile);
    }

    $fOp = fopen($cFile, 'a');

    $data .= '<table width="1024px" cellpadding="1" cellspacing="1" border="1" style="font-family:arial;font-size:12px;border-collapse: collapse;">';
      $data .= '<tr>';
        $data .= '<td colspan="34" style="font-size:14px;text-align:center;"><B>ALPOPULAR SA</B></td>';
      $data .= '</tr>';
      $data .= '<tr>';
        $data .= '<td colspan="34" style="font-size:14px;text-align:center;"><B>REPORTE CERTIFICACI&Oacute;N BASES DE FACTURACI&Oacute;N</B></td>';
      $data .= '</tr>';
      $data .= '<tr>';
        $data .= '<td colspan="34" style="font-size:14px;text-align:left;"><B>Fecha Generaci&oacute;n: '.date('Y-m-d H:i:s').'</B></td>';
      $data .= '</tr>';

      $data .= '<tr>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>ITEM</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>No. CERTIFICACI&Oacute;N</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>NIT</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>COD. SAP CLIENTE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>CLIENTE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>No. MIF</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>FECHA DESDE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>FECHA HASTA</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>DEPOSITO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>ORGANIZACI&Oacute;N VENTAS</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>OFICINA DE VENTAS</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>COD SAP SERVICIO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>DESCRIPCI&Oacute;N SERVICIO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>SUB SERVICIO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>OBJETO FACTURABLE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>UNIDAD FAC</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>CEBE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B> DESCRIPCI&Oacute;N CEBE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>BASE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>CONDICI&Oacute;N</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>ESTATUS </B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>OBSERVACIONES GENERALES</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>CERTIFICACI&Oacute;N PARA FACTURACI&Oacute;N</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>OBSERVACI&Oacute;N CERT PARA FACTURACI&Oacute;N</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>CERTIFICACI&Oacute;N PARA FINANCIERO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>OBSERVACI&Oacute;N CERT PARA FINANCIERO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>APROBADO FINANCIERO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>OBSERVACI&Oacute;N APROBADO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>RECHAZO FINANCIERO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>OBSERVACI&Oacute;N RECHAZO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>ANULACI&Oacute;N</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>OBSERVACI&Oacute;N ANULACI&Oacute;N</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>ESTADO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>FECHA CAMBIO ESTADO</B></td>';
      $data .= '</tr>';

      for ($i=0; $i < count($mData); $i++) { 
        $data .= '<tr>';
          $data .= '<td width="200px" style="font-size:14px;text-align:right;">'.$mData[$i]['itemxxxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:center;">'.$mData[$i]['numcerti'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:center;">'.$mData[$i]['cliidxxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:center;">'.$mData[$i]['clisapxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['clinomxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['nummifxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:right;">'.date("Y-m-d", strtotime($mData[$i]['cerfdexx'])).'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:right;">'.date("Y-m-d", strtotime($mData[$i]['cerfhaxx'])).'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:center;">'.$mData[$i]['depnumxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.utf8_decode($mData[$i]['orvdesxx']).'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.utf8_decode($mData[$i]['ofvdesxx']).'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.utf8_decode($mData[$i]['sersapxx']).'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.utf8_decode($mData[$i]['serdesxx']).'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:right;">'.utf8_decode($mData[$i]['subdesxx']).'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:right;">'.utf8_decode($mData[$i]['obfdesxx']).'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:right;">'.utf8_decode($mData[$i]['ufadesxx']).'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:right;">'.$mData[$i]['cebcodxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:right;">'.utf8_decode($mData[$i]['cebdesxx']).'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['basexxxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['cerdconx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['cerdestx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['cerobsxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['cerusufa'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['cerobsfa'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['cerusufi'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['cerobsfi'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['cerusuap'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['cerobsap'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['cerusure'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['cerobsre'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['cerusuan'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['cerobsan'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['regestxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:right;">'.date("Y-m-d", strtotime($mData[$i]['regfmodx'])).'</td>';
        $data .= '</tr>';
      }

    $data .= '</table>';


    fwrite($fOp, $data);
    fclose($fOp);

    if (file_exists($cFile)){
      ?>
      <script languaje = "javascript">
        parent.fmpro.location = 'frgendoc.php?cRuta=<?php echo $cNomFile ?>';
      </script>
      <?php
    } else {
      f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
    }
  } else {
    f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");
  }

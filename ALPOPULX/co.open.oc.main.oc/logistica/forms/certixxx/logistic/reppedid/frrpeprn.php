<?php
  /**
   * Generar Archivo de Excel - Reporte Pedido.
   * --- Descripcion: Permite Generar Impreso de Excel con la Información del Movimiento de los Pedidos.
	 * @author Elian Amado. <elian.amado@openits.co>
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

    $mClientes  = array();
    $mOrgVentas = array();
    $mOfiVentas = array();
    $mCentLog   = array();
    $mSector    = array();
    $mObjFact   = array();
    $nCantReg   = 0;
    for ($cAnio=$cAnioAnt;$cAnio<=date('Y');$cAnio++) {
      // Consulta el movimiento del pedido
      $qPedido  = "SELECT ";
      $qPedido .= "$cAlfa.lpde$cAnio.*, ";
      $qPedido .= "$cAlfa.lpde$cAnio.pedvlrxx AS pedvlrde, ";
      $qPedido .= "$cAlfa.lpde$cAnio.regestxx AS regestde, ";
      $qPedido .= "$cAlfa.lpde$cAnio.regfmodx AS regfmodd, ";
      $qPedido .= "$cAlfa.lpca$cAnio.* ";
      $qPedido .= "FROM $cAlfa.lpde$cAnio ";
      $qPedido .= "LEFT JOIN $cAlfa.lpca$cAnio ON $cAlfa.lpde$cAnio.pedidxxx = $cAlfa.lpca$cAnio.pedidxxx ";
      $qPedido .= "WHERE ";
      if ($gCliId != "") {
        $qPedido .= "$cAlfa.lpca$cAnio.cliidxxx = \"$gCliId\" AND ";
      }
      if ($gDepNum != "") {
        $qPedido .= "$cAlfa.lpca$cAnio.pedmemde LIKE \"%$gDepNum%\" AND ";
      }
      if ($gPedId != "") {
        $qPedido .= "$cAlfa.lpca$cAnio.pedidxxx = \"$gPedId\" AND ";
      }
      $qPedido .= "$cAlfa.lpca$cAnio.regfcrex BETWEEN \"$gDesde\" AND \"$gHasta\" ";
      if ($gEstCert != "") {
        $qPedido .= "AND $cAlfa.lpca$cAnio.regestxx = \"$gEstCert\" ";
      }
      $qPedido .= "ORDER BY $cAlfa.lpde$cAnio.peddidxx ASC ";
      $xPedido  = f_MySql("SELECT","",$qPedido,$xConexion01,"");
      // echo $qPedido . " - " . mysql_num_rows($xPedido) . "<br>";

      $cComId    = "";
      $cComCod   = "";
      $cComCsc   = "";
      $cComCsc2  = "";
      $nItem     = 0;
      $cNumMif   = "";
      $cEstMif   = "";
      $cNumCert  = "";
      $cEstCert  = "";
      if (mysql_num_rows($xPedido) > 0) {
        while ($xRPE = mysql_fetch_array($xPedido)) {
          
          $nInd_mData = count($mData);

          $nCantReg++;
          if (($nCantReg % _NUMREG_) == 0) { $xConexion01 = fnReiniciarConexion(); }

            // Detecta el cambio de comprobante
            if ($cComId != $xRCC['comidxxx'] || $cComCod != $xRCC['comcodxx'] || $cComCsc != $xRCC['comcscxx'] || $cComCsc2 != $xRCC['comcsc2x']) {
              $cComId   = $xRCC['comidxxx'];
              $cComCod  = $xRCC['comcodxx'];
              $cComCsc  = $xRCC['comcscxx'];
              $cComCsc2 = $xRCC['comcsc2x'];
              $nItem    = 0;
              $cEstMif  = "";
              $cNumCert = "";
              $cEstCert = "";
  
              
            }

          // Consulta la información del cliente
          if(in_array($xRPE['cliidxxx'], $mClientes)){
            $xRPE['clinomxx'] = $mClientes[$xRPE['cliidxxx']]['clinomxx'];
            $xRPE['clisapxx'] = $mClientes[$xRPE['cliidxxx']]['clisapxx'];
          } else {
            $qCliente  = "SELECT ";
            $qCliente .= "cliidxxx, ";
            $qCliente .= "IF($cAlfa.lpar0150.clinomxx != \"\",$cAlfa.lpar0150.clinomxx,(TRIM(CONCAT($cAlfa.lpar0150.clinomxx,\" \",$cAlfa.lpar0150.clinom1x,\" \",$cAlfa.lpar0150.clinom2x,\" \",$cAlfa.lpar0150.cliape1x,\" \",$cAlfa.lpar0150.cliape2x)))) AS clinomxx, ";
            $qCliente .= "clisapxx ";
            $qCliente .= "FROM $cAlfa.lpar0150 ";
            $qCliente .= "WHERE ";
            $qCliente .= "cliidxxx = \"{$xRPE['cliidxxx']}\" LIMIT 0,1 ";
            $xCliente  = f_MySql("SELECT", "", $qCliente, $xConexion01, "");
            if (mysql_num_rows($xCliente)) {
              $vCliente  = mysql_fetch_array($xCliente);
              $mClientes[$xRPE['cliidxxx']][] = $vCliente;
              $xRPE['clinomxx'] = $vCliente['clinomxx'];
              $xRPE['clisapxx'] = $vCliente['clisapxx'];
            }
          }

          // Si el deposito es automatico la informacion del Paso 2 del deposito se obtine del deposito
          if ($xRPE["pedtipxx"] == "AUTOMATICA" && $xRPE["depnumxx"] != "") {
            // Consulta informacion del deposito
            $qDeposito  = "SELECT ";
            $qDeposito .= "lpar0155.depnumxx, ";
            $qDeposito .= "lpar0155.ccoidocx, ";
            $qDeposito .= "lpar0007.tdeidxxx, ";
            $qDeposito .= "lpar0007.tdedesxx, ";
            $qDeposito .= "lpar0001.orvsapxx, ";
            $qDeposito .= "lpar0001.orvdesxx, ";
            $qDeposito .= "lpar0002.ofvsapxx, ";
            $qDeposito .= "lpar0002.ofvdesxx, ";
            $qDeposito .= "lpar0003.closapxx, ";
            $qDeposito .= "lpar0003.clodesxx, ";
            $qDeposito .= "lpar0009.secsapxx, ";
            $qDeposito .= "lpar0009.secdesxx, ";
            $qDeposito .= "lpar0155.regestxx ";
            $qDeposito .= "FROM $cAlfa.lpar0155 ";
            $qDeposito .= "LEFT JOIN $cAlfa.lpar0007 ON $cAlfa.lpar0155.tdeidxxx = $cAlfa.lpar0007.tdeidxxx ";
            $qDeposito .= "LEFT JOIN $cAlfa.lpar0001 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0001.orvsapxx ";
            $qDeposito .= "LEFT JOIN $cAlfa.lpar0002 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0002.orvsapxx AND $cAlfa.lpar0155.ofvsapxx = $cAlfa.lpar0002.ofvsapxx ";
            $qDeposito .= "LEFT JOIN $cAlfa.lpar0003 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0003.orvsapxx AND $cAlfa.lpar0155.ofvsapxx = $cAlfa.lpar0003.ofvsapxx AND $cAlfa.lpar0155.closapxx = $cAlfa.lpar0003.closapxx ";
            $qDeposito .= "LEFT JOIN $cAlfa.lpar0009 ON $cAlfa.lpar0155.secsapxx = $cAlfa.lpar0009.secsapxx ";
            $qDeposito .= "WHERE ";
            $qDeposito .= "lpar0155.depnumxx = \"{$xRPE['depnumxx']}\" ";
            $xDeposito  = f_MySql("SELECT", "", $qDeposito, $xConexion01, "");
            $vDeposito = array();
            if (mysql_num_rows($xDeposito) > 0) {
              $vDeposito = mysql_fetch_array($xDeposito);
            }

            // Informacion de Deposito de Pedidos
            $mData[$nInd_mData]['orgvntsx'] = $vDeposito['orvsapxx'];  // ORGANIZACIÓN VENTAS
            $mData[$nInd_mData]['desorgvt'] = $vDeposito['orvdesxx'];  // DESCRIPCIÓN ORG. VENTAS
            $mData[$nInd_mData]['oficvnts'] = $vDeposito['ofvsapxx'];  // OFICINA VTAS
            $mData[$nInd_mData]['desofcvt'] = $vDeposito['ofvdesxx'];  // DESCRIPCIÓN OFI. VENTAS
            $mData[$nInd_mData]['centlogx'] = $vDeposito['closapxx'];  // CENTRO LOGÍSTICO
            $mData[$nInd_mData]['desctlog'] = $vDeposito['clodesxx'];  // DESCRIPCIÓN CENTRO LOGÍSTICO
            $mData[$nInd_mData]['sectorxx'] = $vDeposito['secsapxx'];  // SECTOR
            $mData[$nInd_mData]['dessectx'] = $vDeposito['secdesxx'];  // DESCRIPCIÓN SECTOR
          } else if ($xRPE["pedtipxx"] == "MANUAL") {

            // Consulta la Organización Ventas
            if(in_array($xRPE['orvsapxx'], $mOrgVentas)){
              $cDesOrgVen = $mOrgVentas[$xRPE['orvsapxx']]['orvdesxx'];
            } else {
              $qOrgVentas  = "SELECT ";
              $qOrgVentas .= "orvsapxx, ";
              $qOrgVentas .= "orvdesxx ";
              $qOrgVentas .= "FROM $cAlfa.lpar0001 ";
              $qOrgVentas .= "WHERE ";
              $qOrgVentas .= "orvsapxx = \"{$xRPE['orvsap2x']}\" LIMIT 0,1 ";
              $xOrgVentas  = f_MySql("SELECT", "", $qOrgVentas, $xConexion01, "");
              if (mysql_num_rows($xOrgVentas)) {
                $vOrgVentas  = mysql_fetch_array($xOrgVentas);
                $mOrgVentas[$xRPE['orvsapxx']][] = $vOrgVentas;
                $cDesOrgVen = $vOrgVentas['orvdesxx'];
              }
            }

            // Consulta la Oficina de Ventas
            if(in_array($xRPE['ofvsapxx'], $mOfiVentas)){
              $cDesOfiVen = $mOfiVentas[$xRPE['ofvsapxx']]['ofvdesxx'];
            } else {
              $qOfiVentas  = "SELECT ";
              $qOfiVentas .= "ofvsapxx, ";
              $qOfiVentas .= "ofvdesxx ";
              $qOfiVentas .= "FROM $cAlfa.lpar0002 ";
              $qOfiVentas .= "WHERE ";
              $qOfiVentas .= "ofvsapxx = \"{$xRPE['ofvsap2x']}\" LIMIT 0,1 ";
              $xOfiVentas  = f_MySql("SELECT", "", $qOfiVentas, $xConexion01, "");
              if (mysql_num_rows($xOfiVentas)) {
                $vOfiVentas  = mysql_fetch_array($xOfiVentas);
                $mOfiVentas[$xRPE['ofvsapxx']][] = $vOfiVentas;
                $cDesOfiVen = $vOfiVentas['ofvdesxx'];
              }
            }

            // Consulta la Centro Logistico
            if(in_array($xRPE['closapxx'], $mCentLog)){
              $cDesCosLog = $mCentLog[$xRPE['closapxx']]['clodesxx'];
            } else {
              $qCentLog  = "SELECT ";
              $qCentLog .= "closapxx, ";
              $qCentLog .= "clodesxx ";
              $qCentLog .= "FROM $cAlfa.lpar0003 ";
              $qCentLog .= "WHERE ";
              $qCentLog .= "closapxx = \"{$xRPE['closapxx']}\" LIMIT 0,1 ";
              $xCentLog  = f_MySql("SELECT", "", $qCentLog, $xConexion01, "");
              if (mysql_num_rows($xCentLog)) {
                $vCentLog  = mysql_fetch_array($xCentLog);
                $mCentLog[$xRPE['closapxx']][] = $vCentLog;
                $cDesCosLog = $vCentLog['clodesxx'];
              }
            }

            // Consulta del Sector
            if(in_array($xRPE['secsapxx'], $mSector)){
              $cDesSect = $mSector[$xRPE['secsapxx']]['secdesxx'];
            } else {
              $qSector  = "SELECT ";
              $qSector .= "secsapxx, ";
              $qSector .= "secdesxx ";
              $qSector .= "FROM $cAlfa.lpar0009 ";
              $qSector .= "WHERE ";
              $qSector .= "secsapxx = \"{$xRPE['secsapxx']}\" LIMIT 0,1 ";
              $xSector  = f_MySql("SELECT", "", $qSector, $xConexion01, "");
              if (mysql_num_rows($xSector)) {
                $vSector  = mysql_fetch_array($xSector);
                $mSector[$xRPE['secsapxx']][] = $vSector;
                $cDesSect = $vSector['secdesxx'];
              }
            }

            $mData[$nInd_mData]['orgvntsx'] = $xRPE['orvsap2x'];  // ORGANIZACIÓN VENTAS
            $mData[$nInd_mData]['desorgvt'] = $cDesOrgVen;        // DESCRIPCIÓN ORG. VENTAS
            $mData[$nInd_mData]['oficvnts'] = $xRPE['ofvsap2x'];  // OFICINA VTAS
            $mData[$nInd_mData]['desofcvt'] = $cDesOfiVen;        // DESCRIPCIÓN OFI. VENTAS
            $mData[$nInd_mData]['centlogx'] = $xRPE['closapxx'];  // CENTRO LOGÍSTICO
            $mData[$nInd_mData]['desctlog'] = $cDesCosLog;        // DESCRIPCIÓN CENTRO LOGÍSTICO
            $mData[$nInd_mData]['sectorxx'] = $xRPE['secsapxx'];  // SECTOR
            $mData[$nInd_mData]['dessectx'] = $cDesSect;          // DESCRIPCIÓN SECTOR

            // Consulta la MIF
            $nAnio = $xRPE['mifidano'];
            $qMifCab  = "SELECT ";
            $qMifCab .= "$cAlfa.lmca$nAnio.mifidxxx, ";
            $qMifCab .= "$cAlfa.lmca$nAnio.comidxxx, ";
            $qMifCab .= "$cAlfa.lmca$nAnio.comcodxx, ";
            $qMifCab .= "$cAlfa.lmca$nAnio.comprexx, ";
            $qMifCab .= "$cAlfa.lmca$nAnio.comcscxx, ";
            $qMifCab .= "$cAlfa.lmca$nAnio.comcsc2x, ";
            $qMifCab .= "$cAlfa.lmca$nAnio.regestxx ";
            $qMifCab .= "FROM $cAlfa.lmca$nAnio ";
            $qMifCab .= "WHERE ";
            $qMifCab .= "$cAlfa.lmca$nAnio.mifidxxx = \"{$xRPE['mifidxxx']}\"";
            $xMifCab  = f_MySql("SELECT", "", $qMifCab, $xConexion01, "");
            $vMifCab = array();
            if (mysql_num_rows($xMifCab) > 0) {
              $vMifCab = mysql_fetch_array($xMifCab);
              $cNumMif = $vMifCab['comidxxx'] ."-". $vMifCab['comprexx'] ."-". $vMifCab['comcscxx'];
              $cEstMif = $vMifCab['regestxx'];
            }

            // Consulta la Certificacion
            $nAnio = $xRPE['ceranoxx'];
            $qCertiCab  = "SELECT ";
            $qCertiCab .= "$cAlfa.lcca$nAnio.ceridxxx, ";
            $qCertiCab .= "$cAlfa.lcca$nAnio.comidxxx, ";
            $qCertiCab .= "$cAlfa.lcca$nAnio.comcodxx, ";
            $qCertiCab .= "$cAlfa.lcca$nAnio.comprexx, ";
            $qCertiCab .= "$cAlfa.lcca$nAnio.comcscxx, ";
            $qCertiCab .= "$cAlfa.lcca$nAnio.comcsc2x, ";
            $qCertiCab .= "$cAlfa.lcca$nAnio.regestxx  ";
            $qCertiCab .= "FROM $cAlfa.lcca$nAnio ";
            $qCertiCab .= "WHERE ";
            $qCertiCab .= "$cAlfa.lcca$nAnio.ceridxxx = \"{$xRPE['ceridxxx']}\"";
            $xCertiCab = f_MySql("SELECT","",$qCertiCab,$xConexion01,"");
            $vCertiCab = array();
            if (mysql_num_rows($xCertiCab) > 0) {
              $vCertiCab = mysql_fetch_array($xCertiCab);
              $cNumCert  = $vCertiCab['comidxxx'] ."-". $vCertiCab['comprexx'] ."-". $vCertiCab['comcscxx'];
              $cEstCert  = $vCertiCab['regestxx'];
            }

            $mData[$nInd_mData]['nummifxx'] = $cNumMif;  // N° MIF
            $mData[$nInd_mData]['estmifxx'] = $cEstMif;  // ESTADO MIF
            $mData[$nInd_mData]['numcert2'] = $cNumCert; // N° CERTIFICACIÓN
            $mData[$nInd_mData]['estcerti'] = $cEstCert; // ESTADO CERTIFICACIÓN
          }

          $mData[$nInd_mData]['condcomx'] = ($xRPE['ccoidocx'] != "" ) ? $xRPE['ccoidocx'] : $xRPE['ccoidoc2'];  // N° COND. COMERCIAL
          $mData[$nInd_mData]['deposito'] = ($xRPE['depnumxx'] != "" ) ? $xRPE['depnumxx'] : $xRPE['depnum2x'];  // DEPÓSITO

          // Consulta del Servicio
          if(in_array($xRPE['sersapxx'], $mServicio)){
            $cDesServ = $mServicio[$xRPE['sersapxx']]['serdesxx'];
          } else {
            $qServicio  = "SELECT ";
            $qServicio .= "sersapxx, ";
            $qServicio .= "serdesxx ";
            $qServicio .= "FROM $cAlfa.lpar0011 ";
            $qServicio .= "WHERE ";
            $qServicio .= "sersapxx = \"{$xRPE['sersapxx']}\" LIMIT 0,1 ";
            $xServicio  = f_MySql("SELECT", "", $qServicio, $xConexion01, "");
            if (mysql_num_rows($xServicio)) {
              $vServicio  = mysql_fetch_array($xServicio);
              $mServicio[$xRPE['sersapxx']][] = $vServicio;
              $cDesServ = $vServicio['serdesxx'];
            }
          }

          // Consulta del Subservicio
          if(in_array($xRPE['subidxxx'], $mSubServ)){
            $cDesSubSer = $mSubServ[$xRPE['subidxxx']]['subdesxx'];
          } else {
            $qSubServ  = "SELECT ";
            $qSubServ .= "subidxxx, ";
            $qSubServ .= "subdesxx ";
            $qSubServ .= "FROM $cAlfa.lpar0012 ";
            $qSubServ .= "WHERE ";
            $qSubServ .= "sersapxx = \"{$xRPE['sersapxx']}\" AND ";
            $qSubServ .= "subidxxx = \"{$xRPE['subidxxx']}\" LIMIT 0,1 ";
            $xSubServ  = f_MySql("SELECT", "", $qSubServ, $xConexion01, "");
            if (mysql_num_rows($xSubServ)) {
              $vSubServ  = mysql_fetch_array($xSubServ);
              $mSubServ[$xRPE['subidxxx']][] = $vSubServ;
              $cDesSubSer = $vSubServ['subdesxx'];
            }
          }

          // Consulta del Codigo Cebe
          if(in_array($xRPE['cebidxxx'], $mCodCebe)){
            $cCodCebe = $mCodCebe[$xRPE['cebidxxx']]['cebcodxx'];
            $cDesCebe = $mCodCebe[$xRPE['cebidxxx']]['cebdesxx'];
          } else {
            $qCodCebe  = "SELECT ";
            $qCodCebe .= "cebidxxx, ";
            $qCodCebe .= "cebcodxx, ";
            $qCodCebe .= "cebdesxx ";
            $qCodCebe .= "FROM $cAlfa.lpar0010 ";
            $qCodCebe .= "WHERE ";
            $qCodCebe .= "cebidxxx = \"{$xRPE['cebidxxx']}\" LIMIT 0,1 ";
            $xCodCebe  = f_MySql("SELECT", "", $qCodCebe, $xConexion01, "");
            if (mysql_num_rows($xCodCebe)) {
              $vCodCebe  = mysql_fetch_array($xCodCebe);
              $mCodCebe[$xRPE['cebidxxx']][] = $vCodCebe;
              $cCodCebe = $vCodCebe['cebcodxx'];
              $cDesCebe = $vCodCebe['cebdesxx'];
            }
          }

          // Consulta Unidad Facturable
          if(in_array($xRPE['ufaidxxx'], $mUniFact)){
            $cDesUniFact = $mUniFact[$xRPE['ufaidxxx']]['ufadesxx'];
          } else {
            $qUniFact  = "SELECT ";
            $qUniFact .= "ufaidxxx, ";
            $qUniFact .= "ufadesxx ";
            $qUniFact .= "FROM $cAlfa.lpar0006 ";
            $qUniFact .= "WHERE ";
            $qUniFact .= "ufaidxxx = \"{$xRPE['ufaidxxx']}\" LIMIT 0,1 ";
            $xUniFact  = f_MySql("SELECT", "", $qUniFact, $xConexion01, "");
            if (mysql_num_rows($xUniFact)) {
              $vUniFact  = mysql_fetch_array($xUniFact);
              $mUniFact[$xRPE['ufaidxxx']][] = $vUniFact;
              $cDesUniFact = $vUniFact['ufadesxx'];
            }
          }
  
          // Consulta Objeto Facturable
          if(in_array($xRPE['obfidxxx'], $mObjFact)){
            $cDesObjFac = $mObjFact[$xRPE['obfidxxx']]['obfdesxx'];
          } else {
            $qObjFact  = "SELECT ";
            $qObjFact .= "obfidxxx, ";
            $qObjFact .= "obfdesxx ";
            $qObjFact .= "FROM $cAlfa.lpar0004 ";
            $qObjFact .= "WHERE ";
            $qObjFact .= "obfidxxx = \"{$xRPE['obfidxxx']}\" LIMIT 0,1 ";
            $xObjFact  = f_MySql("SELECT", "", $qObjFact, $xConexion01, "");
            if (mysql_num_rows($xObjFact)) {
              $vObjFact  = mysql_fetch_array($xObjFact);
              $mObjFact[$xRPE['obfidxxx']][] = $vObjFact;
              $cDesObjFac = $vObjFact['obfdesxx'];
            }
          }
         
          // Informacion de Cabecera de Pedidos
          $mData[$nInd_mData]['numpedid'] = $xRPE['comprexx']."-".$xRPE['comcscxx']; // N° PEDIDO
          $mData[$nInd_mData]['docusapx'] = "";                 // DOCUMENTO SAP
          $mData[$nInd_mData]['comfecxx'] = date("Y-m-d", strtotime($xRPE['comfecxx']));  // FECHA PEDIDO
          $mData[$nInd_mData]['cliidxxx'] = $xRPE['cliidxxx'];  // NIT
          $mData[$nInd_mData]['clisapxx'] = $xRPE['clisapxx'];  // COD. SAP CLIENTE
          $mData[$nInd_mData]['clinomxx'] = $xRPE['clinomxx'];  // CLIENTE
          $mData[$nInd_mData]['fecdesde'] = ($xRPE['pedtipxx'] == "AUTOMATICA") ? $xRPE['cerfdexx'] : $xRPE['pedfdexx'];  // FECHA DESDE
          $mData[$nInd_mData]['fechasta'] = ($xRPE['pedtipxx'] == "AUTOMATICA") ? $xRPE['cerfhaxx'] : $xRPE['pedfhaxx'];  // FECHA HASTA

          // Informacion de detalle del Paso 3 del Pedido
          $mData[$nInd_mData]['itemxxxx'] = ($nItem++);         // ITEM
          $mData[$nInd_mData]['cseidxxx'] = $xRPE['cseidxxx'];  // COND. DE SERVICIO
          $mData[$nInd_mData]['sersapxx'] = $xRPE['sersapxx'];  // COD. SAP
          $mData[$nInd_mData]['serdesxx'] = $cDesServ;          // SERVICIO
          $mData[$nInd_mData]['subidxxx'] = $cDesSubSer;        // SUB SERVICIO
          $mData[$nInd_mData]['cebcodxx'] = $cCodCebe;          // COD. CEBE
          $mData[$nInd_mData]['cebdesxx'] = $cDesCebe;          // DESCRIPCIÓN CEBE
          $mData[$nInd_mData]['ufaidxxx'] = $xRPE['ufaidxxx'];  // ID UNID FACTURABLE
          $mData[$nInd_mData]['unifactx'] = $cDesUniFact;       // UNIDAD FACTURABLE
          $mData[$nInd_mData]['obfidxxx'] = $xRPE['obfidxxx'];  // ID OBJ FACTURABLE
          $mData[$nInd_mData]['obfdesxx'] = $cDesObjFac;        // OBJETO FACTURABLE
          $mData[$nInd_mData]['pedbasex'] = $xRPE['pedbasex'];  // BASE
          $mData[$nInd_mData]['pedtarix'] = $xRPE['pedtarix'];  // TARIFA
          $mData[$nInd_mData]['pedcalcu'] = $xRPE['pedcalcu'];  // CÁLCULO
          $mData[$nInd_mData]['pedminix'] = $xRPE['pedminix'];  // MÍNIMA
          $mData[$nInd_mData]['pedvlrxx'] = $xRPE['pedvlrde'];  // VALOR. PEDIDO
          $mData[$nInd_mData]['regestxx'] = $xRPE['regestxx'];  // ESTADO
          $mData[$nInd_mData]['regfcrex'] = $xRPE['regfcrex'];  // FECHA ESTADO
          $mData[$nInd_mData]['pedtipxx'] = $xRPE['pedtipxx'];  // TIPO DE PEDIDO
          $mData[$nInd_mData]['regusrxx'] = $xRPE['regusrxx'];  // USUARIO PEDIDO
        }
      }
    }  
  } else {
    f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.");
  }

  // echo "<pre>";
  // print_r($mData);
  // die();

  // Inicia a pintar el Excel
  if (count($mData) > 0) {
    $data     = '';
    $cNomFile = "MOVIMIENTO_PEDIDOS_".date("YmdHis").".xls";

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
        $data .= '<td colspan="34" style="font-size:14px;text-align:center;"><B>REPORTE PEDIDO</B></td>';
      $data .= '</tr>';
      $data .= '<tr>';
        $data .= '<td colspan="34" style="font-size:14px;text-align:left;"><B>Fecha Generaci&oacute;n: '.date('Y-m-d H:i:s').'</B></td>';
      $data .= '</tr>';

      $data .= '<tr>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>No. PEDIDO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>DOCUMENTO SAP</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>FECHA PEDIDO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>NIT</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>COD. SAP CLIENTE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>CLIENTE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>ORGANIZACI&Oacute;N VENTAS</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>DESCRIPCI&Oacute;N ORG. VENTAS</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>OFICINA DE VENTAS</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>DESCRIPCI&Oacute;N OFI. VENTAS</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>CENTRO LOG&Iacute;STICO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>DESCRIPCI&Oacute;N CENTRO LOG&Iacute;STICO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>SECTOR</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>DESCRIPCI&Oacute;N SECTOR</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>No. COND. COMERCIAL</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>DEP&Oacute;SITO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>No. MIF</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>ESTADO MIF</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>No CERTIFICACI&Oacute;N</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>ESTADO CERTIFICACI&Oacute;N</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>FECHA DESDE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>FECHA HASTA</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>ITEM</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>COND. DE SERVICIO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>COD. SAP</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>SERVICIO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>SUB SERVICIO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>COD. CEBE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>DESCRIPCI&Oacute;N CEBE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>ID UNID FACTURABLE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>UNIDAD FACTURABLE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>ID OBJ FACTURABLE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>OBJETO FACTURABLE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>BASE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>TARIFA</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>C&Aacute;LCULO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>M&Iacute;NIMA</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>VALOR. PEDIDO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>ESTADO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>FECHA ESTADO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>TIPO DE PEDIDO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>USUARIO PEDIDO</B></td>';
      $data .= '</tr>';

      for ($i=0; $i < count($mData); $i++) { 
        $data .= '<tr>';
          $data .= '<td width="200px" style="font-size:14px;text-align:right;">'.$mData[$i]['numpedid'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:center;">'.$mData[$i]['docusapx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:center;">'.$mData[$i]['comfecxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:center;">'.$mData[$i]['cliidxxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['clisapxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['clinomxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['orgvntsx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.utf8_decode($mData[$i]['desorgvt']).'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['oficvnts'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.utf8_decode($mData[$i]['desofcvt']).'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['centlogx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.utf8_decode($mData[$i]['desctlog']).'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['sectorxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.utf8_decode($mData[$i]['dessectx']).'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['condcomx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['deposito'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['nummifxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['estmifxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['numcert2'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['estcerti'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:right;">'.(($mData[$i]['fecdesde'] != "" && $mData[$i]['fecdesde'] != "0000-00-00") ? date("Y-m-d", strtotime($mData[$i]['fecdesde'])) : '').'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:right;">'.(($mData[$i]['fecdesde'] != "" && $mData[$i]['fecdesde'] != "0000-00-00") ? date("Y-m-d", strtotime($mData[$i]['fechasta'])) : '').'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:center;">'.$mData[$i]['itemxxxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['cseidxxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['sersapxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['serdesxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:right;">'.$mData[$i]['subidxxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:right;">'.$mData[$i]['cebcodxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:right;">'.utf8_decode($mData[$i]['cebdesxx']).'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:right;">'.$mData[$i]['ufaidxxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:right;">'.$mData[$i]['unifactx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['obfidxxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['obfdesxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['pedbasex'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['pedtarix'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['pedcalcu'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['pedminix'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['pedvlrxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['regestxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['regfcrex'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['pedtipxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['regusrxx'].'</td>';
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

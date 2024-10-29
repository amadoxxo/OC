<?php
  namespace openComex;
  /**
   * Grillas de Detalle de Certificacion.
   * --- Descripcion: Permite cargar las grillas en el formulario de Pedido.
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
   * @package opencomex
   * @version 001
   */

  include("../../../../../financiero/libs/php/utility.php");
  include("../../../../libs/php/utitarix.php");

  //Recorriendo todos los casos
  switch ($gTipo) {
    case "1": //DETALLE CERTIFICACION

      $vCerIds = explode("~", $gCerIds);
      $vAnio   = explode("~", $gAnioIds);

      $mDataDetalle = array();
      for ($i=0; $i < count($vAnio); $i++) { 
        $cAnio = $vAnio[$i];

        // Consulta la información de detalle de la certificación
        $qCertifiDet  = "SELECT ";
        $qCertifiDet .= "$cAlfa.lcde$cAnio.*, ";
        $qCertifiDet .= "$cAlfa.lcca$cAnio.cliidxxx, ";
        $qCertifiDet .= "$cAlfa.lcca$cAnio.depnumxx, ";
        $qCertifiDet .= "$cAlfa.lpar0011.sersapxx, ";
        $qCertifiDet .= "$cAlfa.lpar0011.serdesxx, ";
        $qCertifiDet .= "$cAlfa.lpar0004.obfidxxx, ";
        $qCertifiDet .= "$cAlfa.lpar0004.obfdesxx, ";
        $qCertifiDet .= "$cAlfa.lpar0006.ufaidxxx, ";
        $qCertifiDet .= "$cAlfa.lpar0006.ufadesxx, ";
        $qCertifiDet .= "$cAlfa.lpar0010.cebidxxx, ";
        $qCertifiDet .= "$cAlfa.lpar0010.cebcodxx, ";
        $qCertifiDet .= "$cAlfa.lpar0010.cebdesxx ";
        $qCertifiDet .= "FROM $cAlfa.lcde$cAnio ";
        $qCertifiDet .= "LEFT JOIN $cAlfa.lcca$cAnio ON $cAlfa.lcde$cAnio.ceridxxx = $cAlfa.lcca$cAnio.ceridxxx ";
        $qCertifiDet .= "LEFT JOIN $cAlfa.lpar0011 ON $cAlfa.lcde$cAnio.sersapxx = $cAlfa.lpar0011.sersapxx ";
        $qCertifiDet .= "LEFT JOIN $cAlfa.lpar0004 ON $cAlfa.lcde$cAnio.obfidxxx = $cAlfa.lpar0004.obfidxxx ";
        $qCertifiDet .= "LEFT JOIN $cAlfa.lpar0006 ON $cAlfa.lcde$cAnio.ufaidxxx = $cAlfa.lpar0006.ufaidxxx ";
        $qCertifiDet .= "LEFT JOIN $cAlfa.lpar0010 ON $cAlfa.lcde$cAnio.cebidxxx = $cAlfa.lpar0010.cebidxxx ";
        $qCertifiDet .= "WHERE ";
        $qCertifiDet .= "$cAlfa.lcde$cAnio.ceridxxx = \"{$vCerIds[$i]}\"";
        $xCertifiDet  = f_MySql("SELECT","",$qCertifiDet,$xConexion01,"");
        // echo $qCertifiDet . " - " . mysql_num_rows($xCertifiDet);
        if (mysql_num_rows($xCertifiDet) > 0) {
          $cExisteServicio = "NUEVO"; // Permite identificar si es un servicio nuevo o existente en la base de datos
          while ($xRCD = mysql_fetch_array($xCertifiDet)) {
            $nIncluir = 0;

            // Por la opción de nuevo se deben pintar todos los servicios de la certificación que no hayan sido cargados en otro pedido
            if ($_COOKIE['kModo'] == "NUEVO" && $xRCD['pedcscxx'] == "") {
              $nIncluir = 1;
            } else if($_COOKIE['kModo'] == "EDITAR") {

              // Valida si existe la certificación en la base de datos o si en una certificación nueva seleccionada en la grilla
              $qDetPedido  = "SELECT ";
              $qDetPedido .= "$cAlfa.lpde$gPedAnio.peddidxx, ";
              $qDetPedido .= "$cAlfa.lpca$gPedAnio.comidxxx, ";
              $qDetPedido .= "$cAlfa.lpca$gPedAnio.comprexx, ";
              $qDetPedido .= "$cAlfa.lpca$gPedAnio.comcscxx ";
              $qDetPedido .= "FROM $cAlfa.lpde$gPedAnio ";
              $qDetPedido .= "LEFT JOIN $cAlfa.lpca$cAnio ON $cAlfa.lpde$cAnio.pedidxxx = $cAlfa.lpca$cAnio.pedidxxx ";
              $qDetPedido .= "WHERE ";
              $qDetPedido .= "lpde$gPedAnio.pedidxxx = \"$gPedId\" AND ";
              $qDetPedido .= "lpde$gPedAnio.ceridxxx = \"{$xRCD['ceridxxx']}\" AND ";
              $qDetPedido .= "lpde$gPedAnio.ceranoxx = \"{$cAnio}\" ";
              $xDetPedido  = f_MySql("SELECT","",$qDetPedido,$xConexion01,"");

              // Construye el consecutivo del Pedido
              $cConsecutivo = $xRCD['comidxxx'] . "-" . $xRCD['comprexx'] . "-" . $xRCD['comcscxx'];
              if (mysql_num_rows($xDetPedido) > 0) {
                $cExisteServicio = "EXISTE";

                while ($xRDP = mysql_fetch_array($xDetPedido)) {
                  // Valida si el servicio y subservicio de la certificacion exite en el detalle del pedido para pintarlo en la grilla
                  if ($xRDP['sersapxx'] == $xRCD['sersapxx'] && $xRDP['subidxxx'] == $xRCD['sersapxx'] && $xRCD['pedcscxx'] == $cConsecutivo) {
                    $nIncluir = 1;
                  }   
                }
              } else {
                if ($xRCD['pedcscxx'] == "") {
                  $nIncluir = 1;
                }
              }

              // Valida si existe el subservicio guardado en la tabla para cargarlo en la grilla
              $qDetPedido  = "SELECT ";
              $qDetPedido .= "$cAlfa.lpde$gPedAnio.peddidxx ";
              $qDetPedido .= "FROM $cAlfa.lpde$gPedAnio ";
              $qDetPedido .= "WHERE ";
              $qDetPedido .= "lpde$gPedAnio.pedidxxx = \"$gPedId\" AND ";
              $qDetPedido .= "lpde$gPedAnio.ceridxxx = \"{$xRCD['ceridxxx']}\" AND ";
              $qDetPedido .= "lpde$gPedAnio.ceranoxx = \"{$cAnio}\" AND ";
              $qDetPedido .= "lpde$gPedAnio.sersapxx = \"{$xRCD['sersapxx']}\" AND ";
              $qDetPedido .= "lpde$gPedAnio.subidxxx = \"{$xRCD['subidxxx']}\" LIMIT 0,1 ";
              $xDetPedido  = f_MySql("SELECT","",$qDetPedido,$xConexion01,"");
              if (mysql_num_rows($xDetPedido) > 0) {
                $nIncluir = 1;
              }
            }

            if ($nIncluir == 1) {
              $nInd_mDataDetalle = count($mDataDetalle);
              $mDataDetalle[$nInd_mDataDetalle] = $xRCD;
              $mDataDetalle[$nInd_mDataDetalle]['ceranoxx'] = $cAnio;
              $mDataDetalle[$nInd_mDataDetalle]['existexx'] = $cExisteServicio;
            }
          }
        }
      }

      // Consulta los servicios manuales que pueden existir en el Pedido automatico
      if($_COOKIE['kModo'] == "EDITAR") {
        // Consulta el detalle del Pedido
        $qDetPedido  = "SELECT ";
        $qDetPedido .= "$cAlfa.lpar0011.sersapxx, ";
        $qDetPedido .= "$cAlfa.lpar0011.serdesxx, ";
        $qDetPedido .= "$cAlfa.lpar0012.subidxxx, ";
        $qDetPedido .= "$cAlfa.lpar0012.subdesxx, ";
        $qDetPedido .= "$cAlfa.lpar0004.obfidxxx, ";
        $qDetPedido .= "$cAlfa.lpar0004.obfdesxx, ";
        $qDetPedido .= "$cAlfa.lpar0006.ufaidxxx, ";
        $qDetPedido .= "$cAlfa.lpar0006.ufadesxx, ";
        $qDetPedido .= "$cAlfa.lpar0010.cebidxxx, ";
        $qDetPedido .= "$cAlfa.lpar0010.cebcodxx, ";
        $qDetPedido .= "$cAlfa.lpar0010.cebdesxx, ";
        $qDetPedido .= "$cAlfa.lpde$gPedAnio.ceridxxx, ";
        $qDetPedido .= "$cAlfa.lpde$gPedAnio.ceranoxx, ";
        $qDetPedido .= "$cAlfa.lpde$gPedAnio.sersapxx, ";
        $qDetPedido .= "$cAlfa.lpde$gPedAnio.subidxxx ";
        $qDetPedido .= "FROM $cAlfa.lpde$gPedAnio ";
        $qDetPedido .= "LEFT JOIN $cAlfa.lpar0011 ON $cAlfa.lpde$gPedAnio.sersapxx = $cAlfa.lpar0011.sersapxx ";
        $qDetPedido .= "LEFT JOIN $cAlfa.lpar0012 ON $cAlfa.lpde$gPedAnio.sersapxx = $cAlfa.lpar0012.sersapxx AND $cAlfa.lpde$gPedAnio.subidxxx = $cAlfa.lpar0012.subidxxx ";
        $qDetPedido .= "LEFT JOIN $cAlfa.lpar0004 ON $cAlfa.lpde$gPedAnio.obfidxxx = $cAlfa.lpar0004.obfidxxx ";
        $qDetPedido .= "LEFT JOIN $cAlfa.lpar0006 ON $cAlfa.lpde$gPedAnio.ufaidxxx = $cAlfa.lpar0006.ufaidxxx ";
        $qDetPedido .= "LEFT JOIN $cAlfa.lpar0010 ON $cAlfa.lpde$gPedAnio.cebidxxx = $cAlfa.lpar0010.cebidxxx ";
        $qDetPedido .= "WHERE ";
        $qDetPedido .= "lpde$gPedAnio.pedidxxx = \"$gPedId\" AND ";
        $qDetPedido .= "lpde$gPedAnio.ceridxxx = \"0\" AND ";
        $qDetPedido .= "lpde$gPedAnio.ceranoxx = \"\"";
        $xDetPedido  = f_MySql("SELECT","",$qDetPedido,$xConexion01,"");

        if (mysql_num_rows($xDetPedido) > 0) {
          while ($xRDP = mysql_fetch_array($xDetPedido)) {
            $nInd_mDataDetalle = count($mDataDetalle);
            $mDataDetalle[$nInd_mDataDetalle] = $xRDP;
            $mDataDetalle[$nInd_mDataDetalle]['existexx'] = "EXISTE";
          }
        }

      }

      fnCargarDetalleCertificacion($mDataDetalle);
    break;
    default:
      //No Hace Nada
    break;
  }

  /**
   * Valida si existe un subservicio en el detalle de certificacion.
   * 
   * Esta funcion se utiliza cuando el kModo es EDITAR o VER para marcar los subservicios ACTIVOS
   */
  function fnCargarDetalleCertificacion($mDataDetalle) {
    global $xConexion01; global $cAlfa; global $gPedAnio; global $gPedId; global $_COOKIE;

    ?>
    <script languaje = "javascript">
      parent.fmwork.document.getElementById('Grid_Detalle_Certificacion').innerHTML = "";
    </script>
    <?php

    $nCountGrid = 0;
    $cEditar    = "NO";
    // Total Pedido 
    $nTotPedido  = 0;
    $nIndexCerti = 0;
    $cCerId      = "";
    $cCerAnio    = "";

    if (count($mDataDetalle) > 0) {
      for ($i=0; $i < count($mDataDetalle); $i++) { 

        // Valida si existe una Autorizacion de (Excluir) con el Servicio y Subservicio para Excluirlo del Pedido
        $qAutorizaExcl  = "SELECT ";
        $qAutorizaExcl .= "lpar0160.aesidxxx ";
        $qAutorizaExcl .= "FROM $cAlfa.lpar0160 ";
        $qAutorizaExcl .= "WHERE ";
        $qAutorizaExcl .= "lpar0160.cliidxxx = \"{$mDataDetalle[$i]['cliidxxx']}\" AND ";
        $qAutorizaExcl .= "lpar0160.ceridxxx = \"{$mDataDetalle[$i]['ceridxxx']}\" AND ";
        $qAutorizaExcl .= "lpar0160.ceranoxx = \"{$mDataDetalle[$i]['ceranoxx']}\" AND ";
        $qAutorizaExcl .= "lpar0160.sersapxx = \"{$mDataDetalle[$i]['sersapxx']}\" AND ";
        $qAutorizaExcl .= "lpar0160.subidxxx = \"{$mDataDetalle[$i]['subidxxx']}\" AND ";
        $qAutorizaExcl .= "lpar0160.regestxx = \"ACTIVO\" LIMIT 0,1";
        $xAutorizaExcl  = f_MySql("SELECT","",$qAutorizaExcl,$xConexion01,"");
        // echo $qAutorizaExcl . " - " . mysql_num_rows($xAutorizaExcl);
        // echo "<br>";

        if (mysql_num_rows($xAutorizaExcl) == 0) {
          $nCountGrid += 1;

          if ($cCerId != $mDataDetalle[$i]['ceridxxx'] || $cCerAnio != $mDataDetalle[$i]['ceranoxx']) {
            $nIndexCerti++;
            $cCerId   = $mDataDetalle[$i]['ceridxxx'];
            $cCerAnio = $mDataDetalle[$i]['ceranoxx'];
          }

          // Valida si existe una Autorizacion de (Modificar) con el Servicio y Subservicio para permitir Editar los valores del Pedido
          if ($_COOKIE['kModo'] == "EDITAR") {
            $qAutorizaMod  = "SELECT ";
            $qAutorizaMod .= "lpar0161.amcidxxx ";
            $qAutorizaMod .= "FROM $cAlfa.lpar0161 ";
            $qAutorizaMod .= "WHERE ";
            $qAutorizaMod .= "lpar0161.cliidxxx = \"{$mDataDetalle[$i]['cliidxxx']}\" AND ";
            $qAutorizaMod .= "lpar0161.pedidxxx = \"{$gPedId}\" AND ";
            $qAutorizaMod .= "lpar0161.pedanoxx = \"{$gPedAnio}\" AND ";
            $qAutorizaMod .= "lpar0161.sersapxx = \"{$mDataDetalle[$i]['sersapxx']}\" AND ";
            $qAutorizaMod .= "lpar0161.subidxxx = \"{$mDataDetalle[$i]['subidxxx']}\" AND ";
            $qAutorizaMod .= "lpar0161.regestxx = \"ACTIVO\" LIMIT 0,1";
            $xAutorizaMod  = f_MySql("SELECT","",$qAutorizaMod,$xConexion01,"");
            // echo $qAutorizaMod . " - " . mysql_num_rows($xAutorizaMod);
            // echo "<br>";
            if (mysql_num_rows($xAutorizaMod) > 0) {
              $cEditar = "SI";
            } else {
              $cEditar = "NO";
            }
          }

          // Consulta la información del Depósito
          $qDeposito  = "SELECT ";
          $qDeposito .= "lpar0155.depnumxx, ";
          $qDeposito .= "lpar0155.ccoidocx, ";
          $qDeposito .= "lpar0155.regestxx ";
          $qDeposito .= "FROM $cAlfa.lpar0155 ";
          $qDeposito .= "WHERE ";
          $qDeposito .= "lpar0155.depnumxx = \"{$mDataDetalle[$i]['depnumxx']}\" AND ";
          $qDeposito .= "lpar0155.regestxx = \"ACTIVO\" ";
          $xDeposito  = f_MySql("SELECT","",$qDeposito,$xConexion01,"");
          $vDeposito = array();
          if (mysql_num_rows($xDeposito) > 0) {
            $vDeposito = mysql_fetch_array($xDeposito);
          }

          // Consulta la condicion de servicio
          $qCondiServ  = "SELECT ";
          $qCondiServ .= "lpar0152.cseidxxx ";
          $qCondiServ .= "FROM $cAlfa.lpar0152 ";
          $qCondiServ .= "WHERE ";
          $qCondiServ .= "lpar0152.cliidxxx = \"{$mDataDetalle[$i]['cliidxxx']}\" AND ";
          $qCondiServ .= "lpar0152.ccoidocx = \"{$vDeposito['ccoidocx']}\" AND ";
          $qCondiServ .= "lpar0152.sersapxx = \"{$mDataDetalle[$i]['sersapxx']}\" AND ";
          $qCondiServ .= "lpar0152.regestxx = \"ACTIVO\"";
          $xCondiServ  = f_MySql("SELECT","",$qCondiServ,$xConexion01,"");
          // echo "<br>";
          // echo $qCondiServ . " ~ " . mysql_num_rows($xCondiServ);
          $vCondiServ = array();
          if (mysql_num_rows($xCondiServ) > 0) {
            while ($xRCS = mysql_fetch_array($xCondiServ)) {
              // Consulto las condiciones de servicio con relacion al subservicios
              $qSubservicios  = "SELECT cseidxxx ";
              $qSubservicios .= "FROM $cAlfa.lpar0153 ";
              $qSubservicios .= "WHERE ";
              $qSubservicios .= "lpar0153.cseidxxx = \"{$xRCS['cseidxxx']}\" AND ";
              $qSubservicios .= "lpar0153.sersapxx = \"{$mDataDetalle[$i]['sersapxx']}\" AND ";
              $qSubservicios .= "lpar0153.subidxxx = \"{$mDataDetalle[$i]['subidxxx']}\" AND ";
              $qSubservicios .= "lpar0153.regestxx = \"ACTIVO\" limit 0,1 ";
              $xSubservicios  = f_MySql("SELECT","",$qSubservicios,$xConexion01,"");
              // echo $qSubservicios . " ~ " . mysql_num_rows($xSubservicios);
              // echo "<br>";
              if (mysql_num_rows($xSubservicios) > 0) {
                $vCondiServ = mysql_fetch_array($xSubservicios);
                break;
              }
            }
          }

          // Obtiene el valor de la tarifa
          $vParametros = array();
          $vParametros['cseidxxx'] = $vCondiServ['cseidxxx'];
          $vParametros['cantbase'] = $mDataDetalle[$i]['basexxxx'];
          $mTarifa = fnCalcularValorTarifa($vParametros);

          $nCalculo = ($mDataDetalle[$i]['basexxxx'] * $mTarifa['tarifaxx']);
          // Si la tarifa es manual este campo queda vacio
          $nCalculo = ($mTarifa['fcoidxxx'] == "008" || $mTarifa['fcoidxxx'] == "009" || $mTarifa['fcoidxxx'] == "010" || $mTarifa['fcoidxxx'] == "011" || $mDataDetalle[$i]['cerdorix'] == "TRANSACCIONAL") ? "" : $nCalculo;

          // Si el subservicio existe en el detalle del pedido se consulta los valores guardados en la tabla
          if ($mDataDetalle[$i]['existexx'] == "EXISTE") {
            $qDetPedido  = "SELECT ";
            $qDetPedido .= "$cAlfa.lpde$gPedAnio.pedbasex, ";
            $qDetPedido .= "$cAlfa.lpde$gPedAnio.pedtarix, ";
            $qDetPedido .= "$cAlfa.lpde$gPedAnio.pedcalcu, ";
            $qDetPedido .= "$cAlfa.lpde$gPedAnio.pedminix, ";
            $qDetPedido .= "$cAlfa.lpde$gPedAnio.pedvlrxx ";
            $qDetPedido .= "FROM $cAlfa.lpde$gPedAnio ";
            $qDetPedido .= "WHERE ";
            $qDetPedido .= "lpde$gPedAnio.pedidxxx = \"$gPedId\" AND ";
            $qDetPedido .= "lpde$gPedAnio.ceridxxx = \"{$mDataDetalle[$i]['ceridxxx']}\" AND ";
            $qDetPedido .= "lpde$gPedAnio.ceranoxx = \"{$mDataDetalle[$i]['ceranoxx']}\" AND ";
            $qDetPedido .= "lpde$gPedAnio.sersapxx = \"{$mDataDetalle[$i]['sersapxx']}\" AND ";
            $qDetPedido .= "lpde$gPedAnio.subidxxx = \"{$mDataDetalle[$i]['subidxxx']}\" LIMIT 0,1 ";
            $xDetPedido  = f_MySql("SELECT","",$qDetPedido,$xConexion01,"");
            // echo $qDetPedido . " - " . mysql_num_rows($xDetPedido);
            // echo "<br>";
            if (mysql_num_rows($xDetPedido) > 0) {
              $vDetPedido = mysql_fetch_array($xDetPedido);
              $mDataDetalle[$i]['basexxxx'] = $vDetPedido['pedbasex'];
              $mTarifa['tarifaxx']          = $vDetPedido['pedtarix'];
              $nCalculo                     = $vDetPedido['pedcalcu'];
              $mTarifa['minimaxx']          = $vDetPedido['pedminix'];
              $mTarifa['comvalor']          = $vDetPedido['pedvlrxx'];
            }
          }

          // Total Pedido 
          $nTotPedido = $mTarifa['comvalor'] + $nTotPedido;
          ?>
          <script languaje = "javascript">
            parent.fmwork.fnAddNewRowDetalleCerticacion('Grid_Detalle_Certificacion', '');

            // Asigna los valores a la grilla de Detalle de la Certificacion
            parent.fmwork.document.forms['frgrm']['cCSeId_Det'+'<?php echo $nCountGrid ?>'].value     = '<?php echo $vCondiServ['cseidxxx'] ?>';
            parent.fmwork.document.forms['frgrm']['nCerIndex'+'<?php echo $nCountGrid ?>'].value      = '<?php echo $nIndexCerti ?>';
            parent.fmwork.document.forms['frgrm']['cCerDetId'+'<?php echo $nCountGrid ?>'].value      = '<?php echo $mDataDetalle[$i]['cerdidxx'] ?>';
            parent.fmwork.document.forms['frgrm']['cCerdEst_Det'+'<?php echo $nCountGrid ?>'].value   = '<?php echo $mDataDetalle[$i]['cerdestx'] ?>';
            parent.fmwork.document.forms['frgrm']['cSerSap_Det'+'<?php echo $nCountGrid ?>'].value    = '<?php echo $mDataDetalle[$i]['sersapxx'] ?>';
            parent.fmwork.document.forms['frgrm']['cSerDes_Det'+'<?php echo $nCountGrid ?>'].value    = '<?php echo $mDataDetalle[$i]['serdesxx'] ?>';
            parent.fmwork.document.forms['frgrm']['cSubId_Det'+'<?php echo $nCountGrid ?>'].value     = '<?php echo $mDataDetalle[$i]['subidxxx'] ?>';
            parent.fmwork.document.forms['frgrm']['cSubDes_Det'+'<?php echo $nCountGrid ?>'].value    = '<?php echo $mDataDetalle[$i]['subdesxx'] ?>';
            parent.fmwork.document.forms['frgrm']['cObfId_Det'+'<?php echo $nCountGrid ?>'].value     = '<?php echo $mDataDetalle[$i]['obfidxxx'] ?>';
            parent.fmwork.document.forms['frgrm']['cUfaId_Det'+'<?php echo $nCountGrid ?>'].value     = '<?php echo $mDataDetalle[$i]['ufaidxxx'] ?>';
            parent.fmwork.document.forms['frgrm']['cCebId_Det'+'<?php echo $nCountGrid ?>'].value     = '<?php echo $mDataDetalle[$i]['cebidxxx'] ?>';
            parent.fmwork.document.forms['frgrm']['cCebCod_Det'+'<?php echo $nCountGrid ?>'].value    = '<?php echo $mDataDetalle[$i]['cebcodxx'] ?>';
            parent.fmwork.document.forms['frgrm']['cCebDes_Det'+'<?php echo $nCountGrid ?>'].value    = '<?php echo $mDataDetalle[$i]['cebdesxx'] ?>';
            parent.fmwork.document.forms['frgrm']['cBase_Det'+'<?php echo $nCountGrid ?>'].value      = '<?php echo number_format($mDataDetalle[$i]['basexxxx'], 2, '.', ',') ?>';
            parent.fmwork.document.forms['frgrm']['cTarifa_Det'+'<?php echo $nCountGrid ?>'].value    = '<?php echo number_format($mTarifa['tarifaxx'], 2, '.', ',') ?>';
            parent.fmwork.document.forms['frgrm']['cCalculo_Det'+'<?php echo $nCountGrid ?>'].value   = '<?php echo number_format($nCalculo, 2, '.', ',') ?>';
            parent.fmwork.document.forms['frgrm']['cMinima_Det'+'<?php echo $nCountGrid ?>'].value    = '<?php echo (($mTarifa['minimaxx'] != "") ? number_format($mTarifa['minimaxx'], 2, '.', ',') : ""); ?>';
            parent.fmwork.document.forms['frgrm']['cVlrPedido_Det'+'<?php echo $nCountGrid ?>'].value = '<?php echo number_format($mTarifa['comvalor'], 2, '.', ',') ?> ';

            // Deshabilita los campos que ya fueron cargados
            parent.fmwork.document.forms['frgrm']['cCSeId_Det'+'<?php echo $nCountGrid ?>'].readOnly     = true;
            parent.fmwork.document.forms['frgrm']['cCerdEst_Det'+'<?php echo $nCountGrid ?>'].readOnly   = true;
            parent.fmwork.document.forms['frgrm']['cSerSap_Det'+'<?php echo $nCountGrid ?>'].readOnly    = true;
            parent.fmwork.document.forms['frgrm']['cSubId_Det'+'<?php echo $nCountGrid ?>'].readOnly     = true;
            parent.fmwork.document.forms['frgrm']['cSubDes_Det'+'<?php echo $nCountGrid ?>'].readOnly    = true;
            parent.fmwork.document.forms['frgrm']['cObfId_Det'+'<?php echo $nCountGrid ?>'].readOnly     = true;
            parent.fmwork.document.forms['frgrm']['cUfaId_Det'+'<?php echo $nCountGrid ?>'].readOnly     = true;
            parent.fmwork.document.forms['frgrm']['cCebId_Det'+'<?php echo $nCountGrid ?>'].readOnly     = true;
            parent.fmwork.document.forms['frgrm']['cCebCod_Det'+'<?php echo $nCountGrid ?>'].readOnly    = true;
            parent.fmwork.document.forms['frgrm']['cCebDes_Det'+'<?php echo $nCountGrid ?>'].readOnly    = true;
            parent.fmwork.document.forms['frgrm']['cBase_Det'+'<?php echo $nCountGrid ?>'].readOnly      = true;
            parent.fmwork.document.forms['frgrm']['cCalculo_Det'+'<?php echo $nCountGrid ?>'].readOnly   = true;
            parent.fmwork.document.forms['frgrm']['cVlrPedido_Det'+'<?php echo $nCountGrid ?>'].readOnly = true;
            if ('<?php echo $mTarifa['minimaxx'] ?>' == "") {
              parent.fmwork.document.forms['frgrm']['cMinima_Det'+'<?php echo $nCountGrid ?>'].readOnly = true;
            }
            if ('<?php echo $mTarifa['tarifaxx'] ?>' != "") {
              parent.fmwork.document.forms['frgrm']['cTarifa_Det'+'<?php echo $nCountGrid ?>'].readOnly = true;
            }

            if ('<?php echo $_COOKIE['kModo'] ?>' == 'EDITAR' && '<?php echo $cEditar ?>' == "NO") {
              parent.fmwork.document.forms['frgrm']['cBase_Det'+'<?php echo $nCountGrid ?>'].readOnly      = true;
              parent.fmwork.document.forms['frgrm']['cTarifa_Det'+'<?php echo $nCountGrid ?>'].readOnly    = true;
              parent.fmwork.document.forms['frgrm']['cCalculo_Det'+'<?php echo $nCountGrid ?>'].readOnly   = true;
              parent.fmwork.document.forms['frgrm']['cMinima_Det'+'<?php echo $nCountGrid ?>'].readOnly    = true;
              parent.fmwork.document.forms['frgrm']['cVlrPedido_Det'+'<?php echo $nCountGrid ?>'].readOnly = true;
            } 
            
            if (('<?php echo $_COOKIE['kModo'] ?>' == 'EDITAR' && '<?php echo $cEditar ?>' == "SI") || '<?php echo $mTarifa['fcoidxxx'] ?>' == "011") {
              parent.fmwork.document.forms['frgrm']['cBase_Det'+'<?php echo $nCountGrid ?>'].readOnly      = false;
              parent.fmwork.document.forms['frgrm']['cTarifa_Det'+'<?php echo $nCountGrid ?>'].readOnly    = false;
              parent.fmwork.document.forms['frgrm']['cCalculo_Det'+'<?php echo $nCountGrid ?>'].readOnly   = false;
              parent.fmwork.document.forms['frgrm']['cMinima_Det'+'<?php echo $nCountGrid ?>'].readOnly    = false;
              parent.fmwork.document.forms['frgrm']['cVlrPedido_Det'+'<?php echo $nCountGrid ?>'].readOnly = false;
            }
          </script>
          <?php
        }
      } ?>
      <script languaje = "javascript">
        parent.fmwork.document.forms['frgrm']['nTotPedido'].value = '<?php echo number_format($nTotPedido, 2, '.', ',') ?>';
      </script>
      <?php
    } else { ?>
      <script languaje = "javascript">
        parent.fmwork.fnAddNewRowDetalleCerticacion('Grid_Detalle_Certificacion', 'MANUAL');
      </script>
      <?php
    }
  }

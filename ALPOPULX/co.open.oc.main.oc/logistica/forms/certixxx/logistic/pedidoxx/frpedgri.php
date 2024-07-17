<?php
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
          while ($xRCD = mysql_fetch_array($xCertifiDet)) {
            $nInd_mDataDetalle = count($mDataDetalle);
            $mDataDetalle[$nInd_mDataDetalle] = $xRCD;
            $mDataDetalle[$nInd_mDataDetalle]['ceranoxx'] = $cAnio;
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

    // echo $_COOKIE['kModo'];
    // echo "<pre>";
    // print_r($mDataDetalle);
    // echo "<br>";
    // echo $gPedId;
    // echo "<br>";
    // echo $gPedAnio;
    
    $nCountGrid = 0;
    $cEditar    = "NO";
    // Total Pedido 
    $nTotPedido = 0;
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
        $qAutorizaExcl .= "lpar0160.subidxxx = \"{$mDataDetalle[$i]['subidxxx']}\" LIMIT 0,1";
        $xAutorizaExcl  = f_MySql("SELECT","",$qAutorizaExcl,$xConexion01,"");
        // echo $qAutorizaExcl . " - " . mysql_num_rows($xAutorizaExcl);
        // echo "<br>";

        if (mysql_num_rows($xAutorizaExcl) == 0) {
          $nCountGrid += 1;

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
            $qAutorizaMod .= "lpar0161.subidxxx = \"{$mDataDetalle[$i]['subidxxx']}\" LIMIT 0,1";
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

          $nCalculo = ($mTarifa['calculox'] * $mDataDetalle[$i]['basexxxx']);
          // Si la tarifa es manual este campo queda vacio
          $nCalculo = ($mTarifa['fcoidxxx'] == "011" || $mDataDetalle[$i]['cerdorix'] == "TRANSACCIONAL") ? "" : $nCalculo;
          // Total Pedido 
          $nTotPedido = $mTarifa['comvalor'] + $nTotPedido;
          ?>
          <script languaje = "javascript">
            parent.fmwork.fnAddNewRowDetalleCerticacion('Grid_Detalle_Certificacion', '');

            // Asigna los valores a la grilla de Detalle de la Certificacion
            parent.fmwork.document.forms['frgrm']['cCSeId_Det'+'<?php echo $nCountGrid ?>'].value     = '<?php echo $vCondiServ['cseidxxx'] ?>';
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
            parent.fmwork.document.forms['frgrm']['cBase_Det'+'<?php echo $nCountGrid ?>'].value      = '<?php echo number_format($mDataDetalle[$i]['basexxxx'], 2, '.', '') ?>';
            parent.fmwork.document.forms['frgrm']['cTarifa_Det'+'<?php echo $nCountGrid ?>'].value    = '<?php echo number_format($mTarifa['tarifaxx'], 2, '.', '') ?>';
            parent.fmwork.document.forms['frgrm']['cCalculo_Det'+'<?php echo $nCountGrid ?>'].value   = '<?php echo number_format($nCalculo, 2, '.', '') ?>';
            parent.fmwork.document.forms['frgrm']['cMinima_Det'+'<?php echo $nCountGrid ?>'].value    = '<?php echo (($mTarifa['minimaxx'] != "") ? number_format($mTarifa['minimaxx'], 2, '.', '') : ""); ?>';
            parent.fmwork.document.forms['frgrm']['cVlrPedido_Det'+'<?php echo $nCountGrid ?>'].value = '<?php echo number_format($mTarifa['comvalor'], 2, '.', '') ?> ';

            // Deshabilita los campos que ya fueron cargados
            parent.fmwork.document.forms['frgrm']['cCSeId_Det'+'<?php echo $nCountGrid ?>'].readOnly   = true;
            parent.fmwork.document.forms['frgrm']['cCerdEst_Det'+'<?php echo $nCountGrid ?>'].readOnly = true;
            parent.fmwork.document.forms['frgrm']['cSerSap_Det'+'<?php echo $nCountGrid ?>'].readOnly  = true;
            parent.fmwork.document.forms['frgrm']['cSubId_Det'+'<?php echo $nCountGrid ?>'].readOnly   = true;
            parent.fmwork.document.forms['frgrm']['cSubDes_Det'+'<?php echo $nCountGrid ?>'].readOnly  = true;
            parent.fmwork.document.forms['frgrm']['cObfId_Det'+'<?php echo $nCountGrid ?>'].readOnly   = true;
            parent.fmwork.document.forms['frgrm']['cUfaId_Det'+'<?php echo $nCountGrid ?>'].readOnly   = true;
            parent.fmwork.document.forms['frgrm']['cCebId_Det'+'<?php echo $nCountGrid ?>'].readOnly   = true;
            parent.fmwork.document.forms['frgrm']['cCebCod_Det'+'<?php echo $nCountGrid ?>'].readOnly  = true;
            parent.fmwork.document.forms['frgrm']['cCebDes_Det'+'<?php echo $nCountGrid ?>'].readOnly  = true;
            parent.fmwork.document.forms['frgrm']['cBase_Det'+'<?php echo $nCountGrid ?>'].readOnly    = true;
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
            } else if ('<?php echo $_COOKIE['kModo'] ?>' == 'EDITAR' && '<?php echo $cEditar ?>' == "SI") {
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
        parent.fmwork.document.forms['frgrm']['nTotPedido'].value = '<?php echo number_format($nTotPedido, 2, '.', '') ?>';
      </script>
      <?php
    } else { ?>
      <script languaje = "javascript">
        parent.fmwork.fnAddNewRowDetalleCerticacion('Grid_Detalle_Certificacion', '');
      </script>
      <?php
    }
  }

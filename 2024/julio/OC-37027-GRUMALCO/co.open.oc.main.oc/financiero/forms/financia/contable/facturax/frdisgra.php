<?php
  /**
   * Graba Disconformidad de los comprobantes.
   * Este programa permite Grabar la Disconformidad de los comprobantes.
   * @author Camilo Dulce. <camilo.dulce@open-eb.co>
   * @package openComex
   */
  include("../../../../libs/php/utility.php");

  /**
   * Variable para controlar errores de validacion.
   * @var number
   */
  $nSwitch = 0;

  /**
   * Varible para almacenar errores de validacion
   */
  $cMsj = "";

  /**
   * Validando Licencia
   */
  $nLic = f_Licencia();
  if ($nLic == 0){
    $nSwitch = 1;
    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
    $cMsj .= "Error grave de Seguridad otro usuario ingreso con su clave\n";
  }
  
  /*** Validaciones ***/
  if($_POST['cDisId'] == ""){
    $nSwitch = 1;
    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
    $cMsj .= "La Disconformidad No Puede Ser Vacia.\n";
  }else{
    $qDisconformidad  = "SELECT ";
    $qDisconformidad .= "regestxx ";
    $qDisconformidad .= "FROM $cAlfa.fpar0160 ";
    $qDisconformidad .= "WHERE ";
    $qDisconformidad .= "disidxxx = \"{$_POST['cDisId']}\" LIMIT 0,1";
    $xDisconformidad  = f_MySql("SELECT", "", $qDisconformidad, $xConexion01, "");
    if(mysql_num_rows($xDisconformidad) == 0){
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "La Disconformidad[{$_POST['cDisId']}] No Existe En La Base de Datos.\n";
    }else{
      $vDisconformidad = mysql_fetch_array($xDisconformidad);
      if($vDisconformidad['regestxx'] != "ACTIVO"){
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La Disconformidad[{$_POST['cDisId']}] Se Encuentra INACTIVA.\n";
      }
    }
  }

  $mComprobante = f_Explode_Array($_POST['cComIds'],"|","~");
  $cComprobantes = "";
  $mDatos = array();
  for ($nP=0; $nP<count($mComprobante); $nP++) {
    if ($mComprobante[$nP][0] != "") {
      /**
       * Validando que el estado exista
       */
      $nAno = substr($mComprobante[$nP][4],0,4);
      $qCabFac  = "SELECT comidxxx, comcodxx, comcscxx, comcsc2x, terid2xx ";
      $qCabFac .= "FROM $cAlfa.fcoc$nAno ";
      $qCabFac .= "WHERE ";
      $qCabFac .= "comidxxx = \"{$mComprobante[$nP][0]}\"  AND ";
      $qCabFac .= "comcodxx = \"{$mComprobante[$nP][1]}\" AND ";
      $qCabFac .= "comcscxx = \"{$mComprobante[$nP][2]}\" AND ";
      $qCabFac .= "comcsc2x = \"{$mComprobante[$nP][3]}\" LIMIT 0,1 ";
      $xCabFac  = f_MySql("SELECT","",$qCabFac,$xConexion01,"");
      // f_Mensaje(__FILE__, __LINE__,$qCabFac."~".mysql_num_rows($xCabFac));
      if(mysql_num_rows($xCabFac) == 0){
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Comprobante[{$mComprobante[$nP][0]}-{$mComprobante[$nP][1]}-{$mComprobante[$nP][2]}] No Existe En La Base de Datos.\n";
      }else{
        $vCabFac = mysql_fetch_array($xCabFac);
        $nInd_mDatos = count($mDatos);
        $mDatos[$nInd_mDatos] = $vCabFac;
        $mDatos[$nInd_mDatos]['anofacxx'] = $nAno;
      }
    }
  }
  /*** Fin de validaciones. ***/

  /** 
   * Valida que el Facturar a no tenga asignado una disconformidad.
   */
  for ($i=0;$i<count($mDatos);$i++) {
    $qTercero  = "SELECT CLIDISID ";
    $qTercero .= "FROM $cAlfa.SIAI0150 ";
    $qTercero .= "WHERE ";
    $qTercero .= "CLIIDXXX = \"{$mDatos[$i]['terid2xx']}\" LIMIT 0,1 ";
    $xTercero  = f_MySql("SELECT","",$qTercero,$xConexion01,"");
    // echo $qTercero." - ".mysql_num_rows($xTercero);
    if(mysql_num_rows($xTercero) > 0){
      $vTercero = mysql_fetch_array($xTercero);

      if($vTercero['CLIDISID'] != ""){
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Cliente[{$mDatos[$i]['terid2xx']}], Ya tiene asignada la disconformidad [{$vTercero['CLIDISID']}].\n";
      }
    }
  }

  /**
   * Actualizacion en la Tabla.
   */
  if ($nSwitch == 0) {
    $cComAct = ""; //Comprobantes actualizados
    for ($j=0;$j<count($mDatos);$j++) {
      //Actualizando fecha en la cabecera de facturacion
      $nAno = $mDatos[$j]['anofacxx'];
      $qUpdate = array( array('NAME'=>'disidxxx','VALUE'=>trim($_POST['cDisId'])  ,'CHECK'=>'NO'),
                        array('NAME'=>'comidxxx','VALUE'=>$mDatos[$j]['comidxxx']	,'CHECK'=>'WH'),
                        array('NAME'=>'comcodxx','VALUE'=>$mDatos[$j]['comcodxx']	,'CHECK'=>'WH'),
                        array('NAME'=>'comcscxx','VALUE'=>$mDatos[$j]['comcscxx']	,'CHECK'=>'WH'),
                        array('NAME'=>'comcsc2x','VALUE'=>$mDatos[$j]['comcsc2x']	,'CHECK'=>'WH'));

      if (f_MySql("UPDATE","fcoc$nAno",$qUpdate,$xConexion01,$cAlfa)) {
        $cComAct .= "{$mDatos[$j]['comidxxx']}-{$mDatos[$j]['comcodxx']}-{$mDatos[$j]['comcscxx']}-{$mDatos[$j]['comcsc2x']}, ";
      } else {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Sistema no Pudo Actualizar el Comprobante.\n";
      }
    }
  }
  
  if ($nSwitch == 0){
    $cComAct = substr($cComAct, 0, strlen($cComAct)-2);
    f_Mensaje(__FILE__,__LINE__,"Se Actualizaron con Exito las Siguientes Facturas:\n$cComAct.");
    ?>
    <script languaje = "javascript">
      parent.window.opener.document.forms['frgrm'].submit();
      parent.window.close();
    </script>
    <?php
  }else{
    f_Mensaje(__FILE__,__LINE__,"Se Presentaron Errores en el Proceso:\n".$cMsj."Verifique");
  }
  
  
?>
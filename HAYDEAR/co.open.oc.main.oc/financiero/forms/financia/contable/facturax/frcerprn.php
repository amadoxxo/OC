<?php
  /**
	 * Imprime Certificado Mensual de Retenciones x Pagos a Terceros.
	 * --- Descripcion: Permite Imprimir Certificado Mensual de Retenciones x Pagos a Terceros.
	 * @author Yulieth Campos <ycampos@opentecnologia.com.co>
	 * @version 002
	 */
  //  ini_set('error_reporting', E_ERROR);
  //  ini_set("display_errors","1");

  include("../../../../libs/php/utility.php");
  include("../../../../../financiero/libs/php/utimovdo.php");
  include("../../../../../financiero/libs/php/uticerma.php");

  /**
   * Variable para saber si hay o no errores de validacion.
   *
   * @var number
   */
  $nSwitch = 0;

  /**
   * Variable para concatenar los errores de validacion
   *
   * @var string
   */
  $cMsj = "";

  $mPrn = explode("|",$prints);
  for ($nn=0;$nn<count($mPrn);$nn++) {
    if (strlen($mPrn[$nn]) > 0) {
      $vComp = explode("~",$mPrn[$nn]);
  		$cComId   = $vComp[0];
  		$cComCod  = $vComp[1];
  		$cComCsc  = $vComp[3];
  		$cComCsc2 = $vComp[3];
  		$dRegFCre = $vComp[4];
    }
  }

  /*** array para el envío de datos al método ***/
  $vDatos = array();
  $vDatos['cTipo']    = "1";    // Tipo de impresión, por pdf o excel
  $vDatos['cGenerar'] = "FACTURADO"; // opción para impresión: facturado y/o no facturado
  $vDatos['cIntPag']  = ($gTipo == "CERTIFICADOINT") ? "SI" : "NO";  // Intermediación de Pagos
  $vDatos['cTerId']   = "";   // Tercero
  $vDatos['dFecDes']  = $dRegFCre;  // Fecha desde
  $vDatos['dFecHas']  = $dRegFCre;  // Fecha Hasta
  $vDatos['cComId']   = $cComId; // Id del comprobante
  $vDatos['cComCod']  = $cComCod; // Código del comprobante
  $vDatos['cComCsc']  = $cComCsc; // Consecutivo Uno del Comprobante
  $vDatos['cComCsc2'] = $cComCsc2; // Consecutivo Dos del Comprobante

  /*** Se instancia la clase cMovimientoDo del utility utimovdo.php ***/
  $ObjMovimiento = new cMovimientoDo();

  // se envían todos los datos necesarios al método fnPagosaTerceros
  $mReturn = $ObjMovimiento->fnPagosaTerceros($vDatos);
  $mDatos  = $mReturn[1];
  $vResDat = $mReturn[2];
  $vResId  = $mReturn[3];
  $vCocDat = $mReturn[4];

  $vParametros = array();
  $vParametros['DATOSXXX'] = $mReturn[1];
  $vParametros['RESDATXX'] = $mReturn[2];
  $vParametros['RESIDXXX'] = $mReturn[3];
  $vParametros['COCDATXX'] = $mReturn[4];
  $vParametros['TIPOXXXX'] = $gTipo;

  $objCerMan = new cCertificadoMandato();
  $mCerMan   = $objCerMan->fnGenerarCertificadoMandato($vParametros);

  if($mCerMan[0] == "true"){
    echo "<html><script>document.location='{$mCerMan[1]}';</script></html>";
  }else if($mCerMan[0] == "false"){
    $nSwitch = 1;
    for($nCM = 1; $nCM < count($mCerMan); $nCM++){
      $cMsj .= $mCerMan[$nCM]."\n";
    }

    f_Mensaje(__FILE__,__LINE__,$cMsj."\nVerifique.");
    ?>
    <form name = "frnav" action = "frfacini.php" method = "post" target = "fmwork"></form>
    <script languaje = "javascript">
      parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
      document.forms['frnav'].submit();
    </script>
    <?php

  }


?>

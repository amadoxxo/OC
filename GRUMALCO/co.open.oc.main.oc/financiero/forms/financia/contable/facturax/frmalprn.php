<?php
  /**
   * Imprime Factura de Venta Malco.
   * --- Descripcion: Permite Imprimir Factura de Venta.
   * @author Hair Zabala C. <hair.zabala@opentecnologia.com.co>
   */
  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");

  include("../../../../libs/php/utility.php");
  include("../../../../libs/php/utiliqdo.php");

  //Generacion del codigo QR
	require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/phpqrcode/qrlib.php');

  if( $gCorreo == 1 ){
    // Envio de correo desde aplicacion
    include("../../../../libs/php/utimovdo.php");
    include("../../../../libs/php/uticerma.php");
    include("../../../../libs/php/utireplg.php");
    include("../../../../../class/phpmailer/PHPMailerAutoload.php");
  }

   define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
   require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');

  $nSwitch=0;
  $vMemo=explode("|",$prints);

  // Validacion de Comprobante Repetido
  $mPrints = f_Explode_Array($prints,"|","~");
  $cAno    = substr($mPrints[0][4],0,4);
  $cEstiloLetra = 'arial';
  $cEstiloLetrab = 'arialb';

  //Busco los comprobantes donde El tipo de Comprobante sea RCM
  $qFpar117  = "SELECT comidxxx, comcodxx ";
  $qFpar117 .= "FROM $cAlfa.fpar0117 ";
  $qFpar117 .= "WHERE ";
  $qFpar117 .= "comtipxx  = \"RCM\" ";
  $xFpar117  = f_MySql("SELECT","",$qFpar117,$xConexion01,"");
  //f_Mensaje(__FILE__,__LINE__,$qFpar117." ~ ".mysql_num_rows($xFpar117));
  $mRCM = array();
  while ($xRF117 = mysql_fetch_array($xFpar117)) {
    $mRCM[count($mRCM)] = "{$xRF117['comidxxx']}~{$xRF117['comcodxx']}";
  }

  // Busco la resolucion en la tabla GRM00138.
  $qResFac  = "SELECT rescomxx ";
  $qResFac .= "FROM $cAlfa.fpar0138 ";
  $qResFac .= "WHERE ";
  $qResFac .= "rescomxx LIKE \"%{$mPrints[0][0]}~{$mPrints[0][1]}%\" AND ";
  $qResFac .= "regestxx = \"ACTIVO\" LIMIT 0,1";
  $xResFac  = f_MySql("SELECT","",$qResFac,$xConexion01,"");
  //f_Mensaje(__FILE__,__LINE__,$qResFac." ~ ".mysql_num_rows($xResFac));
  $mResFac = mysql_fetch_array($xResFac);
  // Fin de Busco la resolucion en la tabla GRM00138.

  // Armo la variable con los codigos de los comprobantes de facturacion para la actualizacion del consecutivo despues de grabar la factura.
  $mCodCom = f_Explode_Array($mResFac['rescomxx'],"|","~");
  $cCodigos_Comprobantes = "";
  for ($i=0;$i<count($mCodCom);$i++) {
    $cCodigos_Comprobantes .= "\"";
    $cCodigos_Comprobantes .= "{$mCodCom[$i][1]}";
    $cCodigos_Comprobantes .= "\"";
    if ($i < (count($mCodCom) -1)) { $cCodigos_Comprobantes .= ","; }
  }
  // Fin de Armo la variable con los codigos de los comprobantes de facturacion para la actualizacion del consecutivo despues de grabar la factura.

  $qValCsc  = "SELECT comidxxx,comcodxx,comcscxx,comcsc2x ";
  $qValCsc .= "FROM $cAlfa.fcoc$cAno ";
  $qValCsc .= "WHERE ";
  $qValCsc .= "comidxxx = \"{$mPrints[0][0]}\"  AND ";
  $qValCsc .= "comcodxx IN ($cCodigos_Comprobantes) AND ";
  $qValCsc .= "comcscxx = \"{$mPrints[0][2]}\"";
  $xValCsc  = f_MySql("SELECT","",$qValCsc,$xConexion01,"");
  //f_Mensaje(__FILE__,__LINE__,$qValCsc." ~ ".mysql_num_rows($xValCsc));
  if (mysql_num_rows($xValCsc) > 1) {
    $swich = 1;
    f_Mensaje(__FILE__,__LINE__,"El Documento [{$mPrints[0][0]}-{$mPrints[0][1]}-{$mPrints[0][2]}] No se puede Imprimir porque su Consecutivo se Encuentra Repetido en el Modulo de Contabilidad, Verifique");
  }
  // Fin de Validacion de Comprobante Repetido

  $permisos=0;
  $zCadPer="|";
  $resolucion=0;
  $zCadRes="|";
  ///////////////////////
  $fomularios=0;
  $zCadFor="";
  ///////////////////////
  $diferencia=0;
  $paso=0;
  ##Codigo para verificar si la factura ya fue impresa al menos una vez, sino se debe hacer una autorizacion ##
  for($u=0; $u<count($vMemo); $u++) {
    if ($vMemo[$u]!=""){
      $zMatriz=explode("~",$vMemo[$u]);
      ## Select a la 1001 para traer el campo que se marca cuando se ha impreso factura##
      $qCocDat  = "SELECT * ";
      $qCocDat .= "FROM $cAlfa.fcoc$cAno ";
      $qCocDat .= "WHERE $cAlfa.fcoc$cAno.comidxxx = \"{$zMatriz[0]}\" AND ";
      $qCocDat .= "$cAlfa.fcoc$cAno.comcodxx = \"{$zMatriz[1]}\" AND ";
      $qCocDat .= "$cAlfa.fcoc$cAno.comcscxx = \"{$zMatriz[2]}\" AND ";
      $qCocDat .= "$cAlfa.fcoc$cAno.comcsc2x = \"{$zMatriz[3]}\" LIMIT 0,1";
      //f_Mensaje(__FILE__,__LINE__,$qCocDat);
      $xCocDat  = f_MySql("SELECT","",$qCocDat,$xConexion01,"");
      if (mysql_num_rows($xCocDat) > 0) {
        $vCocDat  = mysql_fetch_array($xCocDat);
        if($vCocDat['comprnxx']=="IMPRESO" && $vCocDat['regestxx'] != "INACTIVO" && $_COOKIE['kModo'] != "VERFACTURA" && $gCorreo != 1){
          $zFac=$zMatriz[0].$zMatriz[1]."-".$zMatriz[2]."|";
          $zCadPer .=$zFac;
          $permisos=1;
        }
      }
      ##Fin Select a la 1001 para traer el campo que se marca cuando se ha impreso factura##
    }//if ($vMemo[$u]!=""){
  }//for($u=0; $u<count($vMemo); $u++) {

  ##Codigo que valida si hay errores para permitir o NO la Impresion de la Factura ##
  ##El Codigo se comenta a solicitud del cliente, no debe hacerse control de re-impresion de la factura##
  if($permisos==1){
    $nSwitch=1;
    f_Mensaje(__FILE__,__LINE__,"Las Siguientes Facturas No tienen Permiso de Impresion [$zCadPer], Verifique.");?>
    <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
      <script languaje = "javascript">
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
        document.forms['frgrm'].submit();
      </script>
    <?php
  }
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if($fomularios==1){
    $nSwitch=1;
    f_Mensaje(__FILE__,__LINE__,"Las Siguientes Facturas Presentan Inconsistencias con Formularios: \n $zCadFor --- Verifique --- ");?>
    <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
      <script languaje = "javascript">
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
        document.forms['frgrm'].submit();
      </script>
    <?php
  }
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if($resolucion==1){
    $nSwitch=1;
    f_Mensaje(__FILE__,__LINE__,"Las Siguientes Facturas no Tienen Resolucion Activa [$zCadRes], Verifique."); ?>
      <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
      <script languaje = "javascript">
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
        document.forms['frgrm'].submit();
      </script>
    <?php
  }

  if($nSwitch == 0) {

    $mPrn = explode("|",$prints);
    for ($nn=0;$nn<count($mPrn);$nn++) {
      if (strlen($mPrn[$nn]) > 0) {
        $vComp = explode("~",$mPrn[$nn]);
        $cComId   = $vComp[0];
        $cComCod  = $vComp[1];
        $cComCsc  = $vComp[3];
        $cComCsc2 = $vComp[3];
        $cRegFCre = $vComp[4];
        $cNewYear = substr($cRegFCre,0,4);
        //$cAno     = substr($cRegFCre,0,4);
      }
    }

    // Para el envio de correo se debe adjuntar el certificado de mandato
    if( $gCorreo == 1 ){
      // array para el envío de datos al método
      $vDatos = array();
      $vDatos['cTipo']    = "1";         // Tipo de impresión, por pdf o excel
      $vDatos['cGenerar'] = "FACTURADO"; // opción para impresión: facturado y/o no facturado
      $vDatos['cIntPag']  = "NO";        // Intermediación de Pagos
      $vDatos['cTerId']   = "";          // Tercero
      $vDatos['dFecDes']  = $cRegFCre;   // Fecha desde
      $vDatos['dFecHas']  = $cRegFCre;   // Fecha Hasta
      $vDatos['cComId']   = $cComId;     // Id del comprobante
      $vDatos['cComCod']  = $cComCod;    // Código del comprobante
      $vDatos['cComCsc']  = $cComCsc;    // Consecutivo Uno del Comprobante
      $vDatos['cComCsc2'] = $cComCsc2;   // Consecutivo Dos del Comprobante

      // Se instancia la clase cMovimientoDo del utility utimovdo.php
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
      $vParametros['TIPOXXXX'] = "CERTIFICADO";

      $objCerMan = new cCertificadoMandato();
      $mCerMan   = $objCerMan->fnGenerarCertificadoMandato($vParametros);

      /**
       * Variable para el control de errores.
       * @var string
       */
      $cMsj = "";

      if($mCerMan[0] == "true"){
        $cRutCerMan = $mCerMan[1];
      }else if($mCerMan[0] == "false"){
        $nSwitch = 1;
        for($nCM = 1; $nCM < count($mCerMan); $nCM++){
          $cMsj .= $mCerMan[$nCM]."\n";
        }
      }
    }

    if($vCocDat['regestxx'] != "INACTIVO" && $_COOKIE['kModo'] != "VERFACTURA" && $gCorreo != 1) {
      ##Codigo para Actualizar Campo de Impresion en la 1001 ##
      $mUpdPrn = array(array('NAME'=>'comprnxx','VALUE'=>'IMPRESO'  ,'CHECK'=>'SI'),
                       array('NAME'=>'comidxxx','VALUE'=>$cComId    ,'CHECK'=>'WH'),
                       array('NAME'=>'comcodxx','VALUE'=>$cComCod   ,'CHECK'=>'WH'),
                       array('NAME'=>'comcscxx','VALUE'=>$cComCsc   ,'CHECK'=>'WH'),
                       array('NAME'=>'comcsc2x','VALUE'=>$cComCsc2  ,'CHECK'=>'WH'));

      if (!f_MySql("UPDATE","fcoc$cAno",$mUpdPrn,$xConexion01,$cAlfa)) {
        $nSwitch = 1;
      }
     ##Fin Codigo para Actualizar Campo de Impresion en la 1001 ##
    }

    ////// CABECERA 1001 /////
    $qCocDat  = "SELECT ";
    $qCocDat .= "$cAlfa.fcoc$cNewYear.*, ";
    $qCocDat .= "IF($cAlfa.fpar0008.sucidxxx != \"\",$cAlfa.fpar0008.sucidxxx,\"SUCURSAL SIN ID\") AS sucidxxx, ";
    $qCocDat .= "IF($cAlfa.fpar0008.sucdesxx != \"\",$cAlfa.fpar0008.sucdesxx,\"SUCURSAL SIN DESCRIPCION\") AS sucdesxx, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,\"CLIENTE SIN NOMBRE\") AS CLINOMXX, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.CLIDIRXX != \"\",$cAlfa.SIAI0150.CLIDIRXX,\"SIN DIRECCION\") AS CLIDIRXX, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.CLIDIR3X != \"\",$cAlfa.SIAI0150.CLIDIR3X,\"SIN DIRECCION\") AS CLIDIR3X, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.CLITELXX != \"\",$cAlfa.SIAI0150.CLITELXX,\"SIN TELEFONO\") AS CLITELXX, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.CLICONTX != \"\",$cAlfa.SIAI0150.CLICONTX,\"SIN RESPONSABLE\") AS CLICONTX, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.PAIIDXXX != \"\",$cAlfa.SIAI0150.PAIIDXXX,\"\") AS PAIIDXXX, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.DEPIDXXX != \"\",$cAlfa.SIAI0150.DEPIDXXX,\"\") AS DEPIDXXX, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.CIUIDXXX != \"\",$cAlfa.SIAI0150.CIUIDXXX,\"\") AS CIUIDXXX, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.CLINRPXX != \"\",$cAlfa.SIAI0150.CLINRPXX,\"\") AS CLINRPXX, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.CLIPLAXX != \"\",$cAlfa.SIAI0150.CLIPLAXX,\"\") AS CLIPLAXX ";
    $qCocDat .= "FROM $cAlfa.fcoc$cNewYear ";
    $qCocDat .= "LEFT JOIN $cAlfa.fpar0008 ON $cAlfa.fcoc$cNewYear.ccoidxxx = $cAlfa.fpar0008.ccoidxxx ";
    $qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcoc$cNewYear.terid2xx = $cAlfa.SIAI0150.CLIIDXXX ";
    $qCocDat .= "WHERE ";
    $qCocDat .= "$cAlfa.fcoc$cNewYear.comidxxx = \"$cComId\"  AND ";
    $qCocDat .= "$cAlfa.fcoc$cNewYear.comcodxx = \"$cComCod\" AND ";
    $qCocDat .= "$cAlfa.fcoc$cNewYear.comcscxx = \"$cComCsc\" AND ";
    $qCocDat .= "$cAlfa.fcoc$cNewYear.comcsc2x = \"$cComCsc2\" LIMIT 0,1";

    //f_Mensaje(__FILE__,__LINE__,$qCocDat);
    $xCocDat  = f_MySql("SELECT","",$qCocDat,$xConexion01,"");
    if (mysql_num_rows($xCocDat) > 0) {
      $vCocDat  = mysql_fetch_array($xCocDat);
      //observacion 2 de la factura
      $vComObs2 = explode("~", $vCocDat['comobs2x']);
    }
    //////////////////////////////////////////////////////////////////////////////////////

    ////// DETALLE 1002 /////
    $qCodDat  = "SELECT DISTINCT ";
    $qCodDat .= "$cAlfa.fcod$cNewYear.*, ";
    $qCodDat .= "$cAlfa.sys00121.docmtrxx AS docmtrxx ";
    $qCodDat .= "FROM $cAlfa.fcod$cNewYear ";
    $qCodDat .= "LEFT JOIN $cAlfa.sys00121 ON $cAlfa.fcod$cNewYear.comcsccx = $cAlfa.sys00121.docidxxx AND $cAlfa.fcod$cNewYear.comseqcx = $cAlfa.sys00121.docsufxx  ";
    $qCodDat .= "WHERE $cAlfa.fcod$cNewYear.comidxxx = \"$cComId\" AND ";
    $qCodDat .= "$cAlfa.fcod$cNewYear.comcodxx = \"$cComCod\" AND ";
    $qCodDat .= "$cAlfa.fcod$cNewYear.comcscxx = \"$cComCsc\" AND ";
    $qCodDat .= "$cAlfa.fcod$cNewYear.comcsc2x = \"$cComCsc2\" ORDER BY ABS($cAlfa.fcod$cNewYear.comseqxx) ASC ";
    $xCodDat  = f_MySql("SELECT","",$qCodDat,$xConexion01,"");
    // f_Mensaje(__FILE__,__LINE__,$qCodDat);
    // Matriz para pagos de 4xmil GMF
    $mDatGmf = array();

    if (mysql_num_rows($xCodDat) > 0) {
      // Cargo la Matriz con los ROWS del Cursor
      $iA=0;
      while ($xRCD = mysql_fetch_array($xCodDat)) {

        if($xRCD['comctocx'] == "PCC" && $xRCD['comidc2x'] != "X"){
          //donde el campo pucidxxx like '4%' y el campo cmoctocx = 'PCC'
          $nInd_mDatGmf = count($mDatGmf);
          $mDatGmf[$nInd_mDatGmf]['ctoidxxx'] = $xRCD['ctoidxxx'];
          $mDatGmf[$nInd_mDatGmf]['comobsxx'] = $xRCD['comobsxx'];
          $mDatGmf[$nInd_mDatGmf]['comvlrxx'] = $xRCD['comvlrxx'];
          $mDatGmf[$nInd_mDatGmf]['comvlrme'] = $xRCD['comvlrme'];
          $mDatGmf[$nInd_mDatGmf]['puctipej'] = $xRCD['puctipej'];
          $mDatGmf[$nInd_mDatGmf]['ctoidxxx'] = $xRCD['ctoidxxx'];
          $mDatGmf[$nInd_mDatGmf]['comvlr01'] = $xRCD['comvlr01'];
        }else if ($xRCD['comctocx'] == "PCC") {
          //donde el campo pucidxxx like '4%' y el campo cmoctocx = 'PCC'
          $nInd_mValores = count($mValores);
          $mValores[$nInd_mValores]['ctoidxxx'] = $xRCD['ctoidxxx'];
          $mValores[$nInd_mValores]['comobsxx'] = $xRCD['comobsxx'];
          $mValores[$nInd_mValores]['comvlrxx'] = $xRCD['comvlrxx'];
          $mValores[$nInd_mValores]['comvlrme'] = $xRCD['comvlrme'];
          $mValores[$nInd_mValores]['puctipej'] = $xRCD['puctipej'];
          $mValores[$nInd_mValores]['ctoidxxx'] = $xRCD['ctoidxxx'];
          $mValores[$nInd_mValores]['comvlr01'] = $xRCD['comvlr01'];

        } else {
          $nSwitch_Encontre_Concepto = 0;
          //Agrupando por Concepto
          if ($xRCD['comctocx'] == "IP") {
            //Trayendo descripcion concepto, cantidad y unidad
            $vDatosIp = array();
            $vDatosIp = f_Cantidad_Ingreso_Propio($xRCD['comobsxx'],'',$xRCD['sucidxxx'],$xRCD['docidxxx'],$xRCD['docsufxx']);
            
            //Los IP se agrupan por Sevicio
            for($j=0;$j<count($mCodDat);$j++){
              if($mCodDat[$j]['ctoidxxx'] == $xRCD['ctoidxxx'] && $mCodDat[$j]['seridxxx'] == $xRCD['seridxxx']){
                $nSwitch_Encontre_Concepto = 1;

                $mCodDat[$j]['comvlrxx'] += $xRCD['comvlrxx'];
                $mCodDat[$j]['comvlrme'] += $xRCD['comvlrme'];
                
                $mCodDat[$j]['compivax']  = $xRCD['compivax']; // Porcentaje IVA
                $mCodDat[$j]['comvlr01'] += $xRCD['comvlr01']; // Valor Iva

                //Cantidad FE
                $mCodDat[$j]['canfexxx'] += $vDatosIp[1];

                if ($vComObs2[0] == "MANUAL") {
                  //Cantidad cuando la factura fue manual (por ahora solo aplica para malco)
                  $mCodDat[$j]['comcanxx'] += $xRCD['comcanxx'];
                }
                //Cantidad por condicion especial
                for ($nP=0; $nP<count($vDatosIp[3]); $nP++) {
                  $mCodDat[$j]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] += $vDatosIp[3][$nP]['valpdfxx'];
                }
              }
            }
          }
          if ($nSwitch_Encontre_Concepto == 0) {
            $nInd_mConData = count($mCodDat);
            $mCodDat[$nInd_mConData] = $xRCD;
            if ($xRCD['comctocx'] == "IP") {
              $mCodDat[$nInd_mConData]['comobsxx'] = $vDatosIp[0];
              $mCodDat[$nInd_mConData]['canfexxx'] = $vDatosIp[1];
              $mCodDat[$nInd_mConData]['unidadfe'] = $vDatosIp[2];

              if ($vComObs2[0] != "MANUAL") {
                //Si la factura no es manual esta cantidad no se tiene en cuenta
                $mCodDat[$nInd_mConData]['comcanxx'] = 0;
              }

              for ($nP=0; $nP<count($vDatosIp[3]); $nP++) {
                $mCodDat[$nInd_mConData]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] = $vDatosIp[3][$nP]['valpdfxx'];
              }
            }
          }
        }
      }
      // Fin de Cargo la Matriz con los ROWS del Cursor
    }

    // Consulto los anticipos en el campo memo de cabecera commempa y se llena la matriz de anticipos
    $mAnticipos = array();
    $mComMemPa = explode("|", $vCocDat['commempa']);
    for( $nCMP = 0; $nCMP < count($mComMemPa); $nCMP++ ) {
      $mAntAux = explode("~", $mComMemPa[$nCMP]);
      if($mAntAux[0] == "ANTICIPO"){
        $nInd_mAnticipos = count($mAnticipos);
        $mAnticipos[$nInd_mAnticipos]['comfecxx'] = $mAntAux[8];
        $mAnticipos[$nInd_mAnticipos]['puctipej'] = $mAntAux[13];
        $mAnticipos[$nInd_mAnticipos]['comvlrxx'] = $mAntAux[14];
        $mAnticipos[$nInd_mAnticipos]['comvlrnf'] = $mAntAux[15];

        $mAnticipos[$nInd_mAnticipos]['comidxxx'] = $mAntAux[1];
        $mAnticipos[$nInd_mAnticipos]['comcodxx'] = $mAntAux[2];
        $mAnticipos[$nInd_mAnticipos]['comcscxx'] = $mAntAux[3];
        $mAnticipos[$nInd_mAnticipos]['comseqxx'] = $mAntAux[5];
        
        $mAnticipos[$nInd_mAnticipos]['comidc2x'] = $mAntAux[22];
        $mAnticipos[$nInd_mAnticipos]['comcodc2'] = $mAntAux[23];
        $mAnticipos[$nInd_mAnticipos]['comcscc2'] = $mAntAux[24];
        $mAnticipos[$nInd_mAnticipos]['comseqc2'] = $mAntAux[25];
      }
    }

    // Consulto los anticipos en la matriz y se dejan aquellos que no tengan cruce
    $mAnticiposCruce = array();
    $mAnticiposNuevo = array();

    for ($i=0; $i < count($mAnticipos); $i++) { 
      $borrar = 0;

      if($mAnticipos[$i]['comidc2x'] != "" && $mAnticipos[$i]['comcodc2'] != "" && $mAnticipos[$i]['comcscc2'] != "" && $mAnticipos[$i]['comseqc2'] != ""){
        $cCruce2 = $mAnticipos[$i]['comidc2x']."-".$mAnticipos[$i]['comcodc2']."-".$mAnticipos[$i]['comcscc2']."-".$mAnticipos[$i]['comseqc2'];

        for ($k=0; $k < count($mAnticipos); $k++) {
          $cCruce1 = $mAnticipos[$k]['comidxxx']."-".$mAnticipos[$k]['comcodxx']."-".$mAnticipos[$k]['comcscxx']."-".$mAnticipos[$k]['comseqxx'];
          if($cCruce2 == $cCruce1){
            $borrar = 1;

            $mAnticipos[$k]['comvlrxx'] = $mAnticipos[$k]['comvlrxx'] + $mAnticipos[$i]['comvlrxx'];
            if($mAnticipos[$k]['comvlrxx'] == 0){
              $nInd_mAnticiposCruce = count($mAnticiposCruce);
              $mAnticiposCruce[$nInd_mAnticiposCruce] = $k;
            }
          }
        }
      }

      if($borrar == 1){
        $nInd_mAnticiposCruce = count($mAnticiposCruce);
        $mAnticiposCruce[$nInd_mAnticiposCruce] = $i;
      }
    }

    for ($i=0; $i < count($mAnticipos); $i++) {
      if(!in_array($i, $mAnticiposCruce)){
        $nInd_mAnticiposNuevo = count($mAnticiposNuevo);
        $mAnticiposNuevo[$nInd_mAnticiposNuevo] = $mAnticipos[$i];
      }
    }

    $mAnticipos = $mAnticiposNuevo;

    // Nombre del usuario logueado.
    $qUsrNom  = "SELECT USRNOMXX ";
    $qUsrNom .= "FROM $cAlfa.SIAI0003 ";
    $qUsrNom .= "WHERE ";
    $qUsrNom .= "USRIDXXX = \"{$vCocDat['regusrxx']}\" LIMIT 0,1 ";
    $xUsrNom  = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
    // f_Mensaje(__FILE__,__LINE__,$qUsrNom."~".mysql_num_rows($xUsrNom));
    $vUsrNom  = mysql_fetch_array($xUsrNom);


    ## Traigo el CLINOMXX o Razon Social de la Agencia de Aduana Dietrich Para encabezado de factura ##
    $qAgeDat  = "SELECT ";
    $qAgeDat .= "$cAlfa.SIAI0150.CLIIDXXX, ";
    $qAgeDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX, ";
    $qAgeDat .= "$cAlfa.SIAI0150.CLIDIRXX, ";
    $qAgeDat .= "$cAlfa.SIAI0150.CLITELXX, ";
    $qAgeDat .= "$cAlfa.SIAI0150.CLIFAXXX ";
    $qAgeDat .= "FROM $cAlfa.SIAI0150 ";
    $qAgeDat .= "WHERE ";
    $qAgeDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$vSysStr['financiero_nit_agencia_aduanas']}\" AND ";
    $qAgeDat .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
    $xAgeDat  = f_MySql("SELECT","",$qAgeDat,$xConexion01,"");
    $vAgeDat  = mysql_fetch_array($xAgeDat);
    ## Fin Traigo el CLINOMXX o Razon Social de la Agencia de Aduana Dietrich Para encabezado de factura ##

    ##Traigo Datos de la Resolucion ##
    $qResDat  = "SELECT * ";
    $qResDat .= "FROM $cAlfa.fpar0138 ";
    $qResDat .= "WHERE ";
    $qResDat .= "rescomxx LIKE \"%{$cComId}~{$cComCod}%\" AND ";
    $qResDat .= "regestxx = \"ACTIVO\" LIMIT 0,1";
    $xResDat  = f_MySql("SELECT","",$qResDat,$xConexion01,"");
    if (mysql_num_rows($xResDat) > 0) {
      $vResDat = mysql_fetch_array($xResDat);
    }
    ##Fin Traigo Datos de la Resolucion ##

    ##Traigo Pais del Cliente ##
    $qPaiDat  = "SELECT PAIDESXX ";
    $qPaiDat .= "FROM $cAlfa.SIAI0052 ";
    $qPaiDat .= "WHERE ";
    $qPaiDat .= "$cAlfa.SIAI0052.PAIIDXXX = \"{$vCocDat['PAIIDXXX']}\" AND ";
    $qPaiDat .= "$cAlfa.SIAI0052.REGESTXX = \"ACTIVO\" ";
    //f_Mensaje(__FILE__,__LINE__,$qPaiDat);
    $xPaiDat  = f_MySql("SELECT","",$qPaiDat,$xConexion01,"");
    if (mysql_num_rows($xPaiDat) > 0) {
      $vPaiDat = mysql_fetch_array($xPaiDat);
    }
    ##Fin Traigo Pais del Cliente ##

    ##Traigo Departamento del Cliente ##
    $qDatDep  = "SELECT DEPDESXX  ";
    $qDatDep .= "FROM $cAlfa.SIAI0054 ";
    $qDatDep .= "WHERE ";
    $qDatDep .= "PAIIDXXX =\"{$vCocDat['PAIIDXXX']}\" AND ";
    $qDatDep .= "DEPIDXXX =\"{$vCocDat['DEPIDXXX']}\" LIMIT 0,1";
    $xDatDep  = f_MySql("SELECT","",$qDatDep,$xConexion01,"");
    // f_Mensaje(__FILE__,__LINE__,$qDatDep."~".mysql_num_rows($xDatDep));
    if (mysql_num_rows($xDatDep) > 0) {
      $vDatDep = mysql_fetch_array($xDatDep);
    }
    ##Fin Traigo Departamento del Cliente ##

    ##Traigo Ciudad del Cliente ##
    $qCiuDat  = "SELECT * ";
    $qCiuDat .= "FROM $cAlfa.SIAI0055 ";
    $qCiuDat .= "WHERE ";
    $qCiuDat .= "$cAlfa.SIAI0055.PAIIDXXX = \"{$vCocDat['PAIIDXXX']}\" AND ";
    $qCiuDat .= "$cAlfa.SIAI0055.DEPIDXXX = \"{$vCocDat['DEPIDXXX']}\" AND ";
    $qCiuDat .= "$cAlfa.SIAI0055.CIUIDXXX = \"{$vCocDat['CIUIDXXX']}\" AND ";
    $qCiuDat .= "$cAlfa.SIAI0055.REGESTXX = \"ACTIVO\" ";
    $xCiuDat  = f_MySql("SELECT","",$qCiuDat,$xConexion01,"");
    // f_Mensaje(__FILE__,__LINE__,$qCiuDat."~".mysql_num_rows($xCiuDat));
    if (mysql_num_rows($xCiuDat) > 0) {
      $vCiuDat = mysql_fetch_array($xCiuDat);
    }
    ##Fin Traigo Ciudad del Cliente ##

    //Forma de pago
		$cFormaPago = "";
		if ($vComObs2[14] != "") {
			//Buscando descripcion
			$cFormaPago = ($vComObs[14] == 1) ? "CONTADO" : "CREDITO";
    }
    //Buscando descripcion Medio de Pago
    $vMedPag['mpadesxx'] = "";
    if ($vComObs2[15] != "") {
      $qMedPag  = "SELECT ";
      $qMedPag .= "mpaidxxx, ";
      $qMedPag .= "mpadesxx, ";
      $qMedPag .= "regestxx ";
      $qMedPag .= "FROM $cAlfa.fpar0155 ";
      $qMedPag .= "WHERE ";
      $qMedPag .= "mpaidxxx = \"{$vComObs2[15]}\" LIMIT 0,1";
      $xMedPag  = f_MySql("SELECT","",$qMedPag,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qMedPag."~ ".mysql_num_rows($xMedPag));
      $vMedPag = mysql_fetch_array($xMedPag);
    }

    $cPlazo = (isset($vComObs2[10]) ? $vComObs2[10] : 0)." ".($vComObs2[10] == 1 ? "DIA": "DIAS");

    ##Exploto campo Matriz para traer primer Do y traer Datos de Tasa de Cambio, Documento de Transporte, Bultos, Peso ##
    $cDocId  = ""; $cDocSuc = ""; $cDocSuf = "";
    $dFecMay = date("Y"); //Fecha
    $mDoiId = explode("|",$vCocDat['comfpxxx']);
    for ($i=0;$i<count($mDoiId);$i++) {
      if($mDoiId[$i] != "") {
        $vDoiId  = explode("~",$mDoiId[$i]);
        if($cDocId == "") {
          $cDocId  = $vDoiId[2];
          $cDocSuf = $vDoiId[3];
          $cSucId  = $vDoiId[15];
        }
        $dFecMay = ($dFecMay > substr($vDoiId[6],0,4)) ? substr($vDoiId[6],0,4) : $dFecMay;
      }//if($mDoiId[$i] != ""){
    }//for ($i=0;$i<count($mDoiId);$i++) {
    $nAnoIniDo = (($dFecMay-1) <  $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : ($dFecMay-1);
    ##Fin Exploto campo Matriz para traer primer Do y traer Datos de Tasa de Cambio, Documento de Transporte, Bultos, Peso ##

    // Ciudad que genera el ingreso
    $qLinDes  = "SELECT LINDESXX ";
    $qLinDes .= "FROM $cAlfa.SIAI0119 ";
    $qLinDes .= "WHERE ";
    $qLinDes .= "LINIDXXX = \"$cSucId\" LIMIT 0,1 ";
    $xLinDes  = f_MySql("SELECT","",$qLinDes,$xConexion01,"");
    // f_Mensaje(__FILE__,__LINE__,$qLinDes."~".mysql_num_rows($xLinDes));
    $vLinDes  = mysql_fetch_array($xLinDes);

    $vReturnCorreos = array();

    ##Trayendo Datos de Do Dependiendo del Tipo de Operacion ##
    $vDatDo = f_Datos_Operativos_Do($cSucId, $cDocId, $cDocSuf);
		$vDceDat = $vDatDo['decdatxx'];
		$cTasCam = $vDatDo['tascamxx']; //Tasa de Cambio
		$cDocTra = $vDatDo['doctraxx']; //Documento de Transporte
		$cBultos = $vDatDo['bultosxx']; //Bultos
		$cPesBru = $vDatDo['pesbruxx']; //Peso Bruto
		$nValAdu = $vDatDo['valaduxx']; //Valor en aduana
		$cOpera  = $vDatDo['operaxxx']; //texto valor CIF: o FOB: segun el tipo operacion del tramite
		$cPedido = $vDatDo['pedidoxx']; //Pedido
		$cAduana = $vDatDo['aduanaxx']; //Descripcion Aduana
		$cNomVen = $vDatDo['nomvenxx']; //Nombre Vendedor
		$cOrdCom = $vDatDo['ordcomxx']; //Orden de Compra
		$cPaiOri = $vDatDo['paiorixx']; //Pais de Origen
    ###Fin Trayendo Datos de Do Dependiendo del Tipo de Operacion ##

    ##Exploto campo Matriz para traer los Do's y consultar los pedidos ##
    $cObsPedido = ""; 
    $mDoiId = explode("|",$vCocDat['comfpxxx']);
    for ($i=0;$i<count($mDoiId);$i++) {
      if($mDoiId[$i] != "") {
        $vDoiId  = explode("~",$mDoiId[$i]);
        
        ##Busco Do en la sys00121 Tabla de Do's Financieros y traigo Tipo de Operacion ##
        $qDceDat  = "SELECT * ";
        $qDceDat .= "FROM $cAlfa.sys00121 ";
        $qDceDat .= "WHERE ";
        $qDceDat .= "$cAlfa.sys00121.sucidxxx = \"{$vDoiId[15]}\" AND ";
        $qDceDat .= "$cAlfa.sys00121.docidxxx = \"{$vDoiId[2]}\" AND ";
        $qDceDat .= "$cAlfa.sys00121.docsufxx = \"{$vDoiId[3]}\" ";
        $xDceDat  = f_MySql("SELECT","",$qDceDat,$xConexion01,"");
        if (mysql_num_rows($xDceDat) > 0) {
          $vDceDat = mysql_fetch_array($xDceDat);
        }
        ##Fin Busco Do en la sys00121 Tabla de Do's Financieros y traigo Tipo de Operacion ##

        ##Switch para traer Datos de Do Dependiendo del Tipo de Operacion ##
        switch ($vDceDat['doctipxx']){
          case "IMPORTACION":
            ##Traigo Datos de la SIAI0200 DATOS DEL DO ##
            $qDoiDat  = "SELECT DOIPEDXX ";
            $qDoiDat .= "FROM $cAlfa.SIAI0200 ";
            $qDoiDat .= "WHERE $cAlfa.SIAI0200.DOIIDXXX = \"{$vDoiId[2]}\" AND ";
            $qDoiDat .= "$cAlfa.SIAI0200.DOISFIDX = \"{$vDoiId[3]}\" AND ";
            $qDoiDat .= "$cAlfa.SIAI0200.ADMIDXXX = \"{$vDoiId[15]}\" ";
            //f_Mensaje(__FILE__,__LINE__,$qDoiDat);
            $xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
            if (mysql_num_rows($xDoiDat) > 0) {
              $vDoiDat  = mysql_fetch_array($xDoiDat);

              ##Cargo Variable de pedido para impresion de Datos de Do ##
              $cObsPedido .= $vDoiDat['DOIPEDXX']." / "; 
            }
          break;
          case "EXPORTACION":
            ## Consulto Datos de Do en Exportaciones tabla siae0199 ##
            $qDexDat  = "SELECT dexpedxx ";
            $qDexDat .= "FROM $cAlfa.siae0199 ";
            $qDexDat .= "WHERE ";
            $qDexDat .= "$cAlfa.siae0199.dexidxxx = \"{$vDoiId[2]}\" AND ";
            $qDexDat .= "$cAlfa.siae0199.admidxxx = \"{$vDoiId[15]}\" ";
            $xDexDat  = f_MySql("SELECT","",$qDexDat,$xConexion01,"");
            //f_Mensaje(__FILE__,__LINE__,$qDexDat);
            if (mysql_num_rows($xDexDat) > 0) {
              $vDexDat = mysql_fetch_array($xDexDat);

              ##Cargo Variable de pedido para impresion de Datos de Do ##
              $cObsPedido .= $vDexDat['dexpedxx']." / ";
            }           
          break;
          case "TRANSITO":
            ## Traigo Datos de la SIAI0200 ##
            $qDoiDat  = "SELECT DOIPEDXX ";
            $qDoiDat .= "FROM $cAlfa.SIAI0200 ";
            $qDoiDat .= "WHERE ";
            $qDoiDat .= "DOIIDXXX = \"{$vDoiId[2]}\" AND ";
            $qDoiDat .= "DOISFIDX = \"{$vDoiId[3]}\" AND ";
            $qDoiDat .= "ADMIDXXX = \"{$vDoiId[15]}\" ";
            $xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
            //f_Mensaje(__FILE__,__LINE__,$qDoiDat."~".mysql_num_rows($xDoiDat));
            if (mysql_num_rows($xDoiDat) > 0) {
              $vDoiDat = mysql_fetch_array($xDoiDat);

              ##Cargo Variable de pedido para impresion de Datos de Do ##
              $cObsPedido .= $vDoiDat['DOIPEDXX']." / "; 
            }
          break;
          case "OTROS":
          break;
        }//switch (){
        ##Fin Switch para traer Datos de Do Dependiendo del Tipo de Operacion ##
      }//if($mDoiId[$i] != ""){
    }//for ($i=0;$i<count($mDoiId);$i++) {
    ##Fin Exploto campo Matriz para traer los Do's y consultar los pedidos ##

    // echo "<pre>";
    // var_dump($cObsPedido);
    // die();

    ##Recorrido para saber si hay registros PCC e IP y poder controlar la impresion de bloques de Pagos a Terceros e Ingresos Propios ##
    $nBandPcc = 0;  $nBandIP = 0; // Banderas que se ponen en 1 si encontro registros para impresion bloques PCC e IP.
    for ($k=0;$k<count($mCodDat);$k++) {
      if($mCodDat[$k]['comctocx'] == 'PCC' && substr($mCodDat[$k]['pucidxxx'], 0,1) == "4"){
        $nBandIP = 1;
      } elseif($mCodDat[$k]['comctocx'] == 'PCC'){
        $nBandPcc = 1;
      }//if($mCodDat[$k]['comctocx'] == 'PCC'){
      if($mCodDat[$k]['comctocx'] == 'IP'){
        $nBandIP = 1;
      }//if($mCodDat[$k]['comctocx'] == 'IP'){
    }//for ($k=0;$k<count($mCodDat);$k++) {
    ##Fin Recorrido para saber si hay registros PCC e IP y poder controlar la impresion de bloques de Pagos a Terceros e Ingresos Propios ##

    // Codigo para imprimir los ingresos para terceros
    $mIT = f_Explode_Array($vCocDat['commemod'],"|","~");
    $mIngTer = array();
    for ($i=0;$i<count($mIT);$i++) {
      if ($mIT[$i][1] != "") {
        $vTercero = explode("^",$mIT[$i][2]);
        $mComObs_PCC = stripos($mIT[$i][2],"[");

        // $mIT[$i][7] = ($vCocDat['CLINRPXX'] == "SI") ? $mIT[$i][20] : $mIT[$i][7];
        $nInd_mIngTer = count($mIngTer);
        $mIngTer[$nInd_mIngTer] = $mIT[$i]; // Ingreso el registro como nuevo.
        $mIngTer[$nInd_mIngTer][2]  = $vTercero[0];
        $mIngTer[$nInd_mIngTer][99]  = $vTercero[1];
        $mIngTer[$nInd_mIngTer][98] =  $vTercero[2];
        $mIngTer[$nInd_mIngTer][100] = ((strlen($mIngTer[$nInd_mIngTer][100]) + strlen($mIT[$i][5]) + 1) < 50) ? $mIngTer[$nInd_mIngTer][100]."/".$mIT[$i][5] : $mIngTer[$nInd_mIngTer][100];
        $mIngTer[$nInd_mIngTer][100] = (substr($mIngTer[$nInd_mIngTer][100],0,1) == "/") ? substr($mIngTer[$nInd_mIngTer][100],1,strlen($mIngTer[$nInd_mIngTer][100])) : $mIngTer[$nInd_mIngTer][100];
      }
    }

    $cCscFac = ($vCocDat['regestxx'] == "PROVISIONAL") ?  "XXXXX" : $vCocDat['comcscxx'];
    // Fin de Codigo para imprimir los ingresos para terceros

    // Para el caso de envio de correo desde el tracking o desde el graba,
    // solo se completa el proceso de generacion de la factura si hay correos a quien enviar
    $nGenerar = 0;
    if ( $gCorreo == 1 ) {
      if (count(fnContactos($vCocDat['terid2xx'])) == 0) {
        $nGenerar = 1;
        f_Mensaje(__FILE__,__LINE__,"Los Contactos Asignados al Tramite, No Tienen Correos Parametrizados.");
      }
    }

    if( $nGenerar == 0 ){
      ##Fin Switch para incluir fuente y clase pdf segun base de datos ##
      class PDF2 extends FPDF {

        public $headers = [
          ['w' => 20, 'title' => 'COD. REF'],
          ['w' => 66, 'title' => 'DETALLE'],
          ['w' => 15, 'title' => 'CANT'],
          ['w' => 24, 'title' => 'VALOR UNITARIO'],
          ['w' => 11, 'title' => '% IVA'],
          ['w' => 13, 'title' => 'IVA'],
          ['w' => 17, 'title' => 'VALOR USD'],
          ['w' => 39, 'title' => 'VALOR COP'],
        ];

        function Header() {
          global $cAlfa;   global $cPlesk_Skin_Directory;    global $vAgeDat; global $vCocDat;
          global $vResDat; global $cDocTra; global $cTasCam; global $cBultos; global $cPesBru;
          global $cDocId;  global $vCiuDat; global $vDceDat; global $cOpera;  global $nValAdu;
          global $cCscFac; global $cPaiOri; global $cPedido; global $vSysStr; global $cEstiloLetra; 
          global $cAduana; global $_COOKIE; global $vPaiDat; global $cNomVen; global $cOrdCom; 
          global $vLinDes; global $gCorreo; global $vDatDep; global $vMedPag; global $cFormaPago;
          global $cPlazo;

          if ($vCocDat['regestxx'] == "INACTIVO") {
            $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/facturaanulada.jpg',10,50,190,190);
          }

          if ($_COOKIE['kModo'] == "VERFACTURA"){
            $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/copianovalida.jpg',10,50,190,190);
          }

          if ( $gCorreo == 1 ){
            $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/copianovalida.jpg',10,50,190,190);
          }

          // Impresion Datos Generales Factura
          $nPosX = 5;
          $nPosY = 10;

          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomalco.jpg',$nPosX,$nPosY,32,21);

          $this->SetFont('Arial', 'B', 10);
          $this->setXY($nPosX + 3, $nPosY);
          $this->Cell(200, 4, utf8_decode("AGENCIA DE ADUANAS MARIO LONDOÑO S.A NIVEL 1"), 0, 0, 'C');
          $this->Ln(5);
          $this->setX($nPosX + 3);
          $this->Cell(200, 4, utf8_decode("NIT. {$vSysStr['financiero_nit_agencia_aduanas']}-".f_Digito_Verificacion($vSysStr['financiero_nit_agencia_aduanas'])), 0, 0, 'C');
          $nPosY = $this->GetY() + 5;
          $this->setXY($nPosX + 32, $nPosY);
          $this->SetFont('Arial', 'B', 7);
          $this->Cell(50, 4, utf8_decode("REPRESENTACIÓN GRÁFICA DE LA FACTURA ELECTRÓNICA"), 0, 0, 'L');
          $this->Ln(4);
          $this->setX($nPosX + 32);
          $this->Cell(50, 4, "CUFE:", 0, 0, 'L');

          $this->Ln(4);
          $this->setX($nPosX + 32);
          $this->SetFont('Arial', '', 7);
          $this->MultiCell(65, 3, $vCocDat['compcecu'], 0, 'L');
          $nPosYfin = $this->GetY();

          $this->setXY($nPosX + 110, $nPosY);
          $this->SetFont('Arial', 'B', 7);
          $this->Cell(50, 4, "SEDE PPAL:", 0, 0, 'L');
          $this->setX($nPosX + 126);
          $this->SetFont('Arial', '', 7);
          $this->MultiCell(47, 4, utf8_decode("MEDELLÍN Cll 8B Nº 65-191 OF.511 Edificio Puerto Seco"), 0, 'L');

          $this->Ln(0.5);
          $this->setX($nPosX + 110);
          $this->SetFont('Arial', 'B', 7);
          $this->Cell(50, 4, "CIUDAD QUE GENERA EL INGRESO:", 0, 0, 'L');
          $this->setX($nPosX + 155);
          $this->SetFont('Arial', '', 7);
          $this->MultiCell(50, 4, utf8_decode($vLinDes['LINDESXX']), 0, 'L');
          
          //Codigo QR
          if ($vCocDat['compceqr'] != "") {
            $cFileQR = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/QR_".$_COOKIE['kUsrId']."_".date("YmdHis").".png";
            QRcode::png($vCocDat['compceqr'], $cFileQR, "H", 10, 1);
            $this->Image($cFileQR,$nPosX+173, $nPosY-15,25,25);
          }

          if($nPosYfin < $this->GetY()){
            $nPosYfin = $this->GetY() + 1;
          }

          $nPosY = $nPosYfin;
          $this->setXY($nPosX, $nPosY);
          $this->SetFillColor(230, 230, 230);
          $this->SetFont('Arial', 'B', 12);
          $this->Cell(205, 7, utf8_decode("FACTURA ELECTRÓNICA DE VENTA No. ".$vResDat['resprexx'].$cCscFac), 0, 0, 'C', TRUE);

          $nPosYfin = $this->GetY() + 7;
          if ($this->PageNo() == 1) {
            $nFontSizeHeaderSumarize = 7;
            $nFontSizeHeader = 8;

            // Columna 1
            $nPosY = $this->GetY() + 10;
            $nPosYIni = $nPosY;

            $this->setXY($nPosX, $nPosY);
            $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
            $this->Cell(20, 4, utf8_decode("SEÑOR(ES):"), 0, 0, 'L');
            $this->SetFont('Arial', '', $nFontSizeHeader);
            $this->MultiCell(67, 3, utf8_decode($vCocDat['CLINOMXX']), 0, 'L');
            $this->Ln(1);

            $this->setX($nPosX);
            $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
            $this->Cell(20, 4, "NIT/CC:", 0, 0, 'L');
            $this->SetFont('Arial', '', $nFontSizeHeader);
            $this->Cell(67, 4, $vCocDat['terid2xx']."-".f_Digito_Verificacion($vCocDat['terid2xx']), 0, 0, 'L');
            $this->Ln(4);

            $this->setX($nPosX);
            $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
            $this->Cell(20, 4, utf8_decode("DIRECCIÓN:"), 0, 0, 'L');
            $this->SetFont('Arial', '', $nFontSizeHeader);
            $this->MultiCell(67, 3, utf8_decode($vCocDat['CLIDIR3X']), 0, 'L');
            $this->Ln(0.5);

            $this->setX($nPosX);
            $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
            $this->Cell(38, 4, utf8_decode("CIUDAD Y DEPARTAMENTO:"), 0, 0, 'L');
            $this->SetFont('Arial', '', $nFontSizeHeader);
            $this->MultiCell(49, 4, utf8_decode($vCiuDat['CIUDESXX'] . " - " . $vDatDep['DEPDESXX']), 0, 'L');
            $this->Ln(0.5);

            $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
            $this->setX($nPosX);
            $this->Cell(26, 4, utf8_decode("FORMA DE PAGO:"), 0, 0, 'L');
            $this->SetFont('Arial', '', $nFontSizeHeader);
            $this->Cell(61, 4, utf8_decode($cFormaPago), 0, 0, 'L');
            $this->Ln(4);
            
            $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
            $this->setX($nPosX);
            $this->Cell(26, 4, utf8_decode("MEDIO DE PAGO:"), 0, 0, 'L');
            $this->SetFont('Arial', '', $nFontSizeHeader);
            $this->Cell(61, 4, utf8_decode($vMedPag['mpadesxx']), 0, 0, 'L');
            $this->Ln(4);

            $nPosYfin = $this->GetY();

            // Columna 2
            $offsetX = 88;
            $this->setXY($nPosX + $offsetX, $nPosY);
            $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
            $this->Cell(20, 4, utf8_decode("TELÉFONO:"), 0, 0, 'L');
            $this->SetFont('Arial', '', $nFontSizeHeader);
            $this->Cell(56, 4, $vCocDat['CLITELXX'], 0, 0, 'L');
            $this->Ln(4);

            $this->setX($nPosX + $offsetX);
            $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
            $this->Cell(45, 4, utf8_decode("FECHA Y HORA DE GENERACIÓN:"), 0, 0, 'L');
            $this->SetFont('Arial', '', $nFontSizeHeader);
            $this->Cell(31, 4, $vCocDat['comfecxx']." ".$vCocDat['reghcrex'], 0, 0, 'L');
            $this->Ln(4);

            $this->setX($nPosX + $offsetX);
            $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
            $this->Cell(49, 4, utf8_decode("FECHA Y HORA DE VALIDACION DIAN:"), 0, 0, 'L');
            $this->SetFont('Arial', '', $nFontSizeHeader);
            $this->Cell(27, 4, substr($vCocDat['compcevd'],0,16), 0, 0, 'L');
            $this->Ln(4);

            $this->setX($nPosX + $offsetX);
            $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
            $this->Cell(30, 4, "FECHA VENCIMIENTO:", 0, 0, 'L');
            $this->SetFont('Arial', '', $nFontSizeHeader);
            $this->Cell(46, 4, $vCocDat['comfecve'], 0, 0, 'L');
            $this->Ln(4);
            if ($nPosYfin < $this->GetY())
                $nPosYfin = $this->GetY();

            $this->setX($nPosX + $offsetX);
            $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
            $this->Cell(20, 4, "COMERCIAL:", 0, 0, 'L');
            $this->SetFont('Arial', '', $nFontSizeHeader);
            $this->Cell(56, 3, utf8_decode($cNomVen), 0, 0, 'L');
            $this->Ln(4);
            if ($nPosYfin < $this->GetY())
                $nPosYfin = $this->GetY();
            
            // Columna 3
            $offsetX = 165;

            $this->setXY($nPosX + $offsetX, $nPosY);
            $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
            $this->Cell(8, 4, "DO:", 0, 0, 'L');
            $this->SetFont('Arial', '', $nFontSizeHeader);
            $this->Cell(32, 4, $cDocId, 0, 0, 'L');
            $this->Ln(4);

            $this->setX($nPosX + $offsetX);
            $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
            $this->Cell(10, 4, "TRM:", 0, 0, 'L');
            $this->SetFont('Arial', '', $nFontSizeHeader);
            $this->Cell(30, 4, number_format($vCocDat['tcatasax'],2,'.',''), 0, 0, 'L');
            $this->Ln(4);
            
            $this->setX($nPosX + $offsetX);
            $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
            $this->Cell(13, 4, "PEDIDO:", 0, 0, 'L');
            $this->SetFont('Arial', '', $nFontSizeHeader);
            $this->Cell(27, 4, utf8_decode($cPedido), 0, 0, 'L');
            $this->Ln(4.5);

            $this->setX($nPosX + $offsetX);
            $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
            $this->Cell(8, 4, "OC:", 0, 0, 'L');
            $this->SetFont('Arial', '', $nFontSizeHeader);
            $this->Cell(32, 4, utf8_decode($cOrdCom), 0, 0, 'L');
            $this->Ln(4);

            $this->setX($nPosX + $offsetX);
            $this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
            $this->Cell(13, 4, "PLAZO:", 0, 0, 'L');
            $this->SetFont('Arial', '', $nFontSizeHeader);
            $this->Cell(27, 4, utf8_decode($cPlazo), 0, 0, 'L');
            $this->Ln(4);
            if ($nPosYfin < $this->GetY()) {
                $nPosYfin = $this->GetY();
            }
            $this->Rect($nPosX, $nPosY - 1, 205, $nPosYfin - ($nPosYIni-2));
          }

          $nPosY = $nPosYfin+3;
          $this->setXY($nPosX, $nPosY);
          $this->SetFillColor(220, 220, 220);
          $this->SetFont('Arial', 'B', 7.5);
          foreach ($this->headers as $head)
              $this->Cell($head['w'], 5, $head['title'], 0, 0, 'C', TRUE);

          $this->Rect($nPosX, $nPosY, 205, 5);
          $this->posYIniLines = $nPosY;
          $this->setXY($nPosX,$nPosY);

        } //Function Header

        // // rota la celda
        function RotatedText($x,$y,$txt,$angle){
          //Text rotated around its origin
          $this->Rotate($angle,$x,$y);
          $this->Text($x,$y,$txt);
          $this->Rotate(0);
        }

        // rota la celda
        var $angle=0;
        function Rotate($angle,$x=-1,$y=-1){
          if($x==-1)
            $x=$this->x;
          if($y==-1)
            $y=$this->y;
          if($this->angle!=0)
            $this->_out('Q');
          $this->angle=$angle;
          if($angle!=0) {
            $angle*=M_PI/180;
            $c=cos($angle);
            $s=sin($angle);
            $cx=$x*$this->k;
            $cy=($this->h-$y)*$this->k;
            $this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
          }
        }

        function Footer() {
          global $cNomCopia; global $vCocDat; global $vUsrNom; global $vSysStr;

          //$nPosY = 217;
          $nPosX = 5;
          //Defino posicion inicial Y para pintar la firma
          $nPosY = 250;

          if ($vCocDat['compceqr'] != "" && $vCocDat['compcesv'] != "") {
              $this->setXY($nPosX, $nPosY + 1);
              $this->SetFont('Arial', '', 8);
              $this->Cell(130, 3, utf8_decode("Firma Electrónica:"), 0, 0, 'L');
              $this->Ln(4);
              $this->setX($nPosX);
              $this->SetFont('Arial', '', 6.5);
              $this->MultiCell(135, 3, $vCocDat['compcesv'], 0, 'J');
          }

          $this->setXY($nPosX + 137, $nPosY + 4);
          $this->SetFont('Arial', '', 8.5);
          $this->MultiCell(70, 3, utf8_decode($vUsrNom['USRNOMXX']), 0, 'C');

          $this->setXY($nPosX + 137, $nPosY + 13);
          $this->SetFont('Arial', 'B', 8);
          $this->MultiCell(70, 3, utf8_decode("ELABORÓ"), 0, 'C');

          $this->Rect($nPosX, $nPosY, 205, 17);
          $this->Line($nPosX + 136, $nPosY, $nPosX + 136, $nPosY + 17);
          $this->Line($nPosX + 136, $nPosY + 11, $nPosX + 205, $nPosY + 11);

          //Paginacion
          $this->setXY($nPosX, $nPosY + 17);
          $this->SetFont('Arial', 'B', 7);
          $this->Cell(205, 4, utf8_decode('Pág. ') . $this->PageNo() . '/{nb}', 0, 0, 'C');

          $nPosY = $this->GetY()+5;
          $this->setXY($nPosX,$nPosY);
          $this->SetFont($cEstiloLetra,'',8);
          $this->Cell(205,5,utf8_decode($cNomCopia),0,0,'C',true);
        }

        function Setwidths($w) {
          //Set the array of column widths
          $this->widths=$w;
        }

        function SetAligns($a){
          //Set the array of column alignments
          $this->aligns=$a;
        }

        function Row($data){
          //Calculate the height of the row
          $nb=0;
          for($i=0;$i<count($data);$i++)
              $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
          $h=4*$nb;
          //Issue a page break first if needed
          $this->CheckPageBreak($h);
          //Draw the cells of the row
          for($i=0;$i<count($data);$i++) {
            $w=$this->widths[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            //Draw the border
            //$this->Rect($x,$y,$w,$h);
            //Print the text
            $this->MultiCell($w,4,$data[$i],0,$a);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
          }
          //Go to the next line
          $this->Ln($h);
        }

        function CheckPageBreak($h){
          //If the height h would cause an overflow, add a new page immediately
          if($this->GetY()+$h>$this->PageBreakTrigger)
          $this->AddPage($this->CurOrientation);
        }

        function NbLines($w,$txt){
          //Computes the number of lines a MultiCell of width w will take
          $cw=&$this->CurrentFont['cw'];
          if($w==0)
              $w=$this->w-$this->rMargin-$this->x;
          $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
          $s=str_replace("\r",'',$txt);
          $nb=strlen($s);
          if($nb>0 and $s[$nb-1]=="\n")
              $nb--;
          $sep=-1;
          $i=0;
          $j=0;
          $l=0;
          $nl=1;
          while($i<$nb){
            $c=$s[$i];
            if($c=="\n"){
              $i++;
              $sep=-1;
              $j=$i;
              $l=0;
              $nl++;
              continue;
            }
            if($c==' ')
                  $sep=$i;
              $l+=$cw[$c];
              if($l>$wmax){
                if($sep==-1){
                  if($i==$j)
                      $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
              }
              else
                  $i++;
            }
            return $nl;
        }

      }//class PDF extends FPDF {


      $pdf = new PDF2('P','mm','Letter');  //Error al invocar la clase
      $pdf->AliasNbPages();
      $pdf->SetMargins(0,0,0);
      $pdf->SetAutoPageBreak(0,22);
      $pdf->SetFillColor(229,229,229);

      $nNumCop = 3;
      if( $gCorreo == 1 ){
        $nNumCop = 1;
      }

      for($y=1; $y<=$nNumCop; $y++){
        $pdf->AddPage();
        $cNomCopia = "";

        ##Codigo Para impresion de Copias de Factura ##
        switch($y){
          case 1:
            $cNomCopia = "- ORIGINAL -";
          break;
          case 2:
            $cNomCopia = "- COPIA -";
          break;
          case 3:
            $cNomCopia = "- COPIA 2 -";
          break;
        }
        ##Codigo Para impresion de Copias de Factura ##

        // Inicializando variables por copia
        $nTotPag1 = "";  $cSaldo    = ""; $cNeg      = "";
        $nTotPag  = 0;   $nTotAnt   = 0;  $nIva      = 0;
        $nTotRfte = 0;   $nTotIca   = 0;  $nTotIva   = 0;
        $nTotPcc  = 0;   $nTotPccMe = 0;
        

        $nPosY = $pdf->GetY()+6;
        $nPosX = 5;
        $nPosFin = 240;
        $nPosYVl = 185;
        $nb = 1;
        $pyy = $nPosY;
        // Imprimo Detalle de Pagos a Terceros e Ingresos Propios
        // Imprimo Pagos a Terceros
        if (count($mIngTer) > 0 || $nBandPcc == 1 || count($mValores) > 0) { //Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
          $nTotPcc    = 0; 
          $nTotPcccMe = 0; 
          $cCodigoPCC = "";
          
          for($i=0;$i<count($mIngTer);$i++){
            $cCodigoPCC = ($cCodigoPCC == "") ? $mIngTer[$i][1] : $cCodigoPCC;
            $nTotPcc   += $mIngTer[$i][7];
            $nTotPccMe += ($vCocDat['CLINRPXX'] == "SI") ? $mIngTer[$i][20] : 0;
          }//for($i=0;$i<count($mIngTer);$i++){

          // Recorro la matriz de la 1002 para imprimir Registros de PCC
          for ($i=0;$i<count($mValores);$i++) {
            $cCodigoPCC = ($cCodigoPCC == "") ? $mValores[$i]['ctoidxxx'] : $cCodigoPCC;
            $nTotPcc   += $mValores[$i]['comvlrxx'];
            $nTotPccMe += ($vCocDat['CLINRPXX'] == "SI") ? $mValores[$i]['comvlrme'] : 0;
          }//for ($i=0;$i<count($mValores);$i++) {

          $pdf->setXY($nPosX,$pyy);
          $pdf->SetWidths(array(20, 66, 15, 24, 11, 13, 17, 39));
          $pdf->SetAligns(array("L", "L", "C", "R", "C", "R", "R", "R"));
          $pdf->SetFont($cEstiloLetra,'B',8);

          $pdf->Row(array(
                  utf8_decode($cCodigoPCC),
                  utf8_decode("TOTAL PAGOS EFECTUADOS POR SU CUENTA"),
                  number_format(1, 0, ',', '.'),
                  number_format($nTotPcc, 0, ',', '.'),
                  "0%",
                  "0",
                  number_format($nTotPccMe, 2, ',', '.'),
                  number_format($nTotPcc, 0, ',', '.'),
              ));

          //El valor total debe ser en la moneda del documento
          $nTotPcc = ($vCocDat['CLINRPXX'] == "SI") ? $nTotPccMe : $nTotPcc;

          $pyy = $pdf->GetY() + 6;
        }//if(count($mIngTer) > 0 || $nBandPcc == 1){//Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
        // Fin Imprimo Pagos a Terceros

        // Imprimo Ingresos Propios
        $nSubToIP = 0;    // Subtotal pagos propios
        $nSubToIPIva = 0; // Iva 19%

        $nSubToIPGra   = 0; // Total Ingresos Gravados
        $nSubToIPNoGra = 0; // Total Ingresos No Gravados

        if (count($mCodDat) > 0 || $nBandIP == 1 || count($mDatGmf) > 0) {
          $pdf->setXY($nPosX,$pyy);
          $pdf->SetFont($cEstiloLetra,'B',8);
          $pdf->Cell(20,6,"",0,0,'L');
          $pdf->Cell(66,6,utf8_decode("SERVICIOS PRESTADOS"),0,0,'L');
          $pyy += 6;
        }

        if(count($mCodDat) > 0 || $nBandIP == 1){//Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS
          $pdf->setXY($nPosX,$pyy);
          $pdf->SetWidths(array(20, 66, 15, 24, 11, 13, 17, 39));
          $pdf->SetAligns(array("L", "L", "C", "R", "C", "R", "R", "R"));
          $pdf->SetFont($cEstiloLetra,'',8);

          // hago dos veces el mismo for para separar los ingresos GRAVADOS y luego los NO GRAVADOS
          for($k=0;$k<(count($mCodDat));$k++) {
            $pyy = $pdf->GetY();

            if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
              $pdf->Line($nPosX+20,$nPosY-6,$nPosX+20,($nPosFin+10));
              $pdf->Line($nPosX+86,$nPosY-6,$nPosX+86,($nPosFin+10));
              $pdf->Line($nPosX+101,$nPosY-6,$nPosX+101,($nPosFin+10));
              $pdf->Line($nPosX+125,$nPosY-6,$nPosX+125,($nPosFin+10));
              $pdf->Line($nPosX+136,$nPosY-6,$nPosX+136,($nPosFin+10));
              $pdf->Line($nPosX+149,$nPosY-6,$nPosX+149,($nPosFin+10));
              $pdf->Line($nPosX+166,$nPosY-6,$nPosX+166,($nPosFin+10));
              $pdf->Rect($nPosX, $nPosY-6, 205, ($nPosFin+10) - ($nPosY - 6));
              $pdf->AddPage();
              $nb++;
              $nPosY = $pdf->GetY()+6;
              $nPosX = 5;
              $pyy = $nPosY;
              $pdf->SetFont($cEstiloLetra,'',8);
              $pdf->setXY($nPosX,$pyy);
            }

            if($mCodDat[$k]['comctocx'] == "IP" && $mCodDat[$k]['comvlr01'] != 0 ) {
              $nSubToIP    += ($vCocDat['CLINRPXX'] == "SI") ? $mCodDat[$k]['comvlrme'] : $mCodDat[$k]['comvlrxx'];
              $nSubToIPIva += ($vCocDat['CLINRPXX'] == "SI") ? round($mCodDat[$k]['comvlr01']/$vCocDat['tcatasax'],2) : $mCodDat[$k]['comvlr01'];
              $nSubToIPGra += ($vCocDat['CLINRPXX'] == "SI") ? $mCodDat[$k]['comvlrme'] : $mCodDat[$k]['comvlrxx'];

              $cValor = ""; $cValCon = "";
              //Mostrando cantidades por tipo de cantidad
              foreach ($mCodDat[$k]['itemcanx'] as $cKey => $cValue) {
                // Personalizacion de la descripcion por base de datos e informacion adicional
                if($cKey == "FOB" && $cValue > 0) {
                  $cValor  = " FOB: ($".$cValue;
                  $cValor .= ($mCodDat[$k]['itemcanx']['TRM'] > 0) ? " TRM: $".$mCodDat[$k]['itemcanx']['TRM'] : "";
                  $cValor .= ")";
                } elseif ($cKey == "CIF") {
                  $cValor = "CIF: ($".$cValue.")";
                } elseif ($cKey == "CONTENEDORES_DE_20") {
                  $cValCon .= " CONTENEDORES DE 20: (".$cValue.")";
                } elseif ($cKey == "CONTENEDORES_DE_40") {
                  $cValCon .= " CONTENEDORES DE 40: (".$cValue.")";
                }elseif ($cKey == "UNIDADES_DE_CARGA_SUELTA") {
                  $cValCon .= " UNIDADES DE CARGA SUELTA: (".$cValue.")";
                }
                $cValor = ($cValCon != "") ? $cValCon : $cValor;
              }

              //Si la factura es manual, se trae la cantidad de la fcodYYYY
              //Si es automatica, se trae la cantidad de la descripcion
              if ($vComObs2[0] == "MANUAL") {
                //Cantidad
                $nCantidad  = (($mCodDat[$k]['comcanxx']+0) > 0) ? ($mCodDat[$k]['comcanxx']+0) : 1;
                //Cantidad de decimales de la cantidad
                $nCanDec = (strpos(($mCodDat[$k]['comcanxx']+0),'.') > 0) ? 2 : 0;
              } else {
                //Canitdad
                $nCantidad = ($mCodDat[$k]['unidadfe'] != "A9" && $mCodDat[$k]['canfexxx'] > 0) ? $mCodDat[$k]['canfexxx'] : 1;
                $nCanDec = 0;
              }

              //Calculando valor unitario
              $nValUni = ($vCocDat['CLINRPXX'] == "SI") ? round(($mCodDat[$k]['comvlrxx']/$nCantidad)*100)/100 : round($mCodDat[$k]['comvlrxx']/$nCantidad);
              $nValIva = ($vCocDat['CLINRPXX'] == "SI") ? number_format(round($mCodDat[$k]['comvlr01']/$vCocDat['tcatasax'],2), 2, ',', '.') : number_format($mCodDat[$k]['comvlr01'], 0, ',', '.');

              //Cantiad de decimales valor unitario
              $nCanDecUni = (strpos(($nValUni+0),'.') > 0) ? 2 : 0;

              $pdf->setX($nPosX);
              $pdf->Row(array(
                  utf8_decode($mCodDat[$k]['ctoidxxx']),
                  utf8_decode("* ".trim($mCodDat[$k]['comobsxx'].$cValor)),
                  number_format($nCantidad, $nCanDec, ',', '.'),
                  number_format($nValUni, $nCanDecUni, ',', '.'),
                  ($mCodDat[$k]['compivax']+0)."%",
                  $nValIva,
                  number_format(($vCocDat['CLINRPXX'] == "SI") ? $mCodDat[$k]['comvlrme'] : 0, 2, ',', '.'),
                  number_format($mCodDat[$k]['comvlrxx'], 0, ',', '.'),
              ));
            }//if($mCodDat[$k]['comctocx'] == 'IP'){
          }## for($k=$nPosIP;$k<(count($mCodDat));$k++) { ##

          for($k=0;$k<(count($mCodDat));$k++) {
            $pyy = $pdf->GetY();
            if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
              $pdf->Line($nPosX+20,$nPosY-6,$nPosX+20,($nPosFin+10));
              $pdf->Line($nPosX+86,$nPosY-6,$nPosX+86,($nPosFin+10));
              $pdf->Line($nPosX+101,$nPosY-6,$nPosX+101,($nPosFin+10));
              $pdf->Line($nPosX+125,$nPosY-6,$nPosX+125,($nPosFin+10));
              $pdf->Line($nPosX+136,$nPosY-6,$nPosX+136,($nPosFin+10));
              $pdf->Line($nPosX+149,$nPosY-6,$nPosX+149,($nPosFin+10));
              $pdf->Line($nPosX+166,$nPosY-6,$nPosX+166,($nPosFin+10));
              $pdf->Rect($nPosX, $nPosY-6, 205, ($nPosFin+10) - ($nPosY - 6));
              $pdf->AddPage();
              $nb++;
              $nPosY = $pdf->GetY()+6;
              $nPosX = 5;
              $pyy = $nPosY;
              $pdf->SetFont($cEstiloLetra,'',8);
              $pdf->setXY($nPosX,$nPosY);
            }

            if($mCodDat[$k]['comctocx'] == "IP" && $mCodDat[$k]['comvlr01'] == 0 ) {
              $nSubToIP      += ($vCocDat['CLINRPXX'] == "SI") ? $mCodDat[$k]['comvlrme'] : $mCodDat[$k]['comvlrxx'];
              $nSubToIPIva   += ($vCocDat['CLINRPXX'] == "SI") ? round($mCodDat[$k]['comvlr01']/$vCocDat['tcatasax'],2) : $mCodDat[$k]['comvlr01'];
              $nSubToIPNoGra += ($vCocDat['CLINRPXX'] == "SI") ? $mCodDat[$k]['comvlrme'] : $mCodDat[$k]['comvlrxx'];

              $cValor = ""; $cValCon = "";
              //Mostrando cantidades por tipo de cantidad
              foreach ($mCodDat[$k]['itemcanx'] as $cKey => $cValue) {
                // Personalizacion de la descripcion por base de datos e informacion adicional
                if($cKey == "FOB" && $cValue > 0) {
                  $cValor  = " FOB: ($".$cValue;
                  $cValor .= ($mCodDat[$k]['itemcanx']['TRM'] > 0) ? " TRM: $".$mCodDat[$k]['itemcanx']['TRM'] : "";
                  $cValor .= ")";
                } elseif ($cKey == "CIF") {
                  $cValor = "CIF: ($".$cValue.")";
                } elseif ($cKey == "CONTENEDORES_DE_20") {
                  $cValCon .= " CONTENEDORES DE 20: (".$cValue.")";
                } elseif ($cKey == "CONTENEDORES_DE_40") {
                  $cValCon .= " CONTENEDORES DE 40: (".$cValue.")";
                }elseif ($cKey == "UNIDADES_DE_CARGA_SUELTA") {
                  $cValCon .= " UNIDADES DE CARGA SUELTA: (".$cValue.")";
                }
                $cValor = ($cValCon != "") ? $cValCon : $cValor;
              }

              //Si la factura es manual, se trae la cantidad de la fcodYYYY
              //Si es automatica, se trae la cantidad de la descripcion
              if ($vComObs2[0] == "MANUAL") {
                //Cantidad
                $nCantidad  = (($mCodDat[$k]['comcanxx']+0) > 0) ? ($mCodDat[$k]['comcanxx']+0) : 1;
                //Cantidad de decimales de la cantidad
                $nCanDec = (strpos(($mCodDat[$k]['comcanxx']+0),'.') > 0) ? 2 : 0;
              } else {
                //Canitdad
                $nCantidad = ($mCodDat[$k]['unidadfe'] != "A9" && $mCodDat[$k]['canfexxx'] > 0) ? $mCodDat[$k]['canfexxx'] : 1;
                $nCanDec = 0;
              }

              //Calculando valor unitario
              $nValUni = ($vCocDat['CLINRPXX'] == "SI") ? round(($mCodDat[$k]['comvlrxx']/$nCantidad)*100)/100 : round($mCodDat[$k]['comvlrxx']/$nCantidad);
              $nValIva = ($vCocDat['CLINRPXX'] == "SI") ? number_format(round($mCodDat[$k]['comvlr01']/$vCocDat['tcatasax'],2), 2, ',', '.') : number_format($mCodDat[$k]['comvlr01'], 0, ',', '.');

              //Cantiad de decimales valor unitario
              $nCanDecUni = (strpos(($nValUni+0),'.') > 0) ? 2 : 0;

              $pdf->setX($nPosX);
              $pdf->Row(array(
                  utf8_decode($mCodDat[$k]['ctoidxxx']),
                  utf8_decode(trim($mCodDat[$k]['comobsxx'].$cValor)),
                  number_format($nCantidad, $nCanDec , ',', '.'),
                  number_format($nValUni, $nCanDecUni, ',', '.'),
                  ($mCodDat[$k]['compivax']+0)."%",
                  $nValIva,
                  number_format(($vCocDat['CLINRPXX'] == "SI") ? $mCodDat[$k]['comvlrme'] : 0, 2, ',', '.'),
                  number_format($mCodDat[$k]['comvlrxx'], 0, ',', '.'),
              ));
            }//if($mCodDat[$k]['comctocx'] == 'IP'){
          }## for($k=$nPosIP;$k<(count($mCodDat));$k++) { ##

        }//if(count($mCodDat) > 0 || $nBandIP == 1){//Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS
        // Fin Imprimo Ingresos Propios
        // Fin Imprimo Detalle de Pagos a Terceros e Ingresos Propios

        // Impresion GMF
        if ( count($mDatGmf) > 0 ){

          $pyy = ($pdf->GetY() > $pyy) ? $pdf->GetY(): $pyy;

          if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
            $pdf->Line($nPosX+20,$nPosY-6,$nPosX+20,($nPosFin+10));
            $pdf->Line($nPosX+86,$nPosY-6,$nPosX+86,($nPosFin+10));
            $pdf->Line($nPosX+101,$nPosY-6,$nPosX+101,($nPosFin+10));
            $pdf->Line($nPosX+125,$nPosY-6,$nPosX+125,($nPosFin+10));
            $pdf->Line($nPosX+136,$nPosY-6,$nPosX+136,($nPosFin+10));
            $pdf->Line($nPosX+149,$nPosY-6,$nPosX+149,($nPosFin+10));
            $pdf->Line($nPosX+166,$nPosY-6,$nPosX+166,($nPosFin+10));
            $pdf->Rect($nPosX, $nPosY-6, 205, ($nPosFin+10) - ($nPosY - 6));
            $pdf->AddPage();
            $nb++;
            $nPosY = $pdf->GetY()+6;
            $nPosX = 5;
            $pyy = $nPosY;
            $pdf->SetFont($cEstiloLetra,'',8);
            $pdf->setXY($nPosX,$pyy);
          }

          $nSubTotGmf = 0;  $nSubTotGmfMe = 0; $cCodigoGMF = "";
          for ($i=0;$i<count($mDatGmf);$i++) {
            $cCodigoGMF    = ($cCodigoGMF == "") ? $mDatGmf[$i]['ctoidxxx'] : $cCodigoGMF;
            $nSubTotGmf   += $mDatGmf[$i]['comvlrxx'];
            $nSubTotGmfMe += $mDatGmf[$i]['comvlrme'];
          }//for ($i=0;$i<count($mDatGmf);$i++) {

          $pdf->setXY($nPosX,$pyy);
          $pdf->SetWidths(array(20, 66, 15, 24, 11, 13, 17, 39));
          $pdf->SetAligns(array("L", "L", "C", "R", "C", "R", "R", "R"));
          $pdf->SetFont($cEstiloLetra,'',8);

          $pdf->Row(array(
              utf8_decode($cCodigoGMF),
              utf8_decode("RECUPERACIÓN GASTOS BANCARIOS (GMF)"),
              number_format(1, 0, ',', '.'),
              number_format($nSubTotGmf, 0, ',', '.'),
              "0%",
              "0",
              number_format(($vCocDat['CLINRPXX'] == "SI") ? $nSubTotGmfMe : 0, 2, ',', '.'),
              number_format($nSubTotGmf, 0, ',', '.'),
          ));

          //El valor total debe ser en la moneda del documento
          $nSubTotGmf = ($vCocDat['CLINRPXX'] == "SI") ? $nSubTotGmfMe : $nSubTotGmf;
          $pyy += 6;
        }
        // Fin Impresion GMF

        // Calculo e impresión de Iva y Retenciones.
        $nIva      = 0;
        $nTotRfte  = 0;
        $nTotARfte = 0;
        $nTotCree  = 0;
        $nTotACree = 0;
        $nTotIva   = 0;
        $nTotIca   = 0;
        $nTotAIca  = 0;

        for ($k=0;$k<count($mCodDat);$k++) {
          ##Busco valor de IVA ##
          if($mCodDat[$k]['comctocx'] == 'IVAIP'){
            $nIva += $mCodDat[$k]['comvlrxx'];
          }
          ##Fin Busco Valor de IVA ##

          ##Busco Valor de RET.FTE ##
          if($mCodDat[$k]['comctocx'] == 'RETFTE'){
            $nTotRfte += $mCodDat[$k]['comvlrxx'];
          }
          ##Fin Busco Valor de RET.FTE ##

          ##Busco Valor de AUTO RET.FTE ##
          if($mCodDat[$k]['comctocx'] == 'ARETFTE'){
            $nTotARfte += $mCodDat[$k]['comvlrxx'];
          }
          ##Fin Busco Valor de AUTO RET.FTE ##

          ##Busco Valor de RET.CREE ##
          if($mCodDat[$k]['comctocx'] == 'RETCRE'){
            $nTotCree += $mCodDat[$k]['comvlrxx'];
          }
          ##Fin Busco Valor de RET.CREE ##

          ##Busco Valor de AUTO RET.CREE ##
          if($mCodDat[$k]['comctocx'] == 'ARETCRE'){
            $nTotACree += $mCodDat[$k]['comvlrxx'];
          }
          ##Fin Busco Valor de AUTO RET.CREE ##

          ##Busco Valor de RET.IVA ##
          if($mCodDat[$k]['comctocx'] == 'RETIVA'){
            $nTotIva += $mCodDat[$k]['comvlrxx'];
          }
          ##Fin Busco Valor de RET.IVA ##

          ##Busco Valor de RET.ICA ##
          if($mCodDat[$k]['comctocx'] == 'RETICA'){
            $nTotIca += $mCodDat[$k]['comvlrxx'];
          }
          ##Fin Busco Valor de RET.ICA ##

          ##Busco Valor de AUTO RET.ICA ##
          if($mCodDat[$k]['comctocx'] == 'ARETICA'){
            $nTotAIca += $mCodDat[$k]['comvlrxx'];
          }
          ##Fin Busco Valor de AUTO RET.ICA ##
        }

        // Busco Valor a Pagar
        $nTotPag = 0;
        for ($k=0;$k<count($mCodDat);$k++) {
          if($mCodDat[$k]['comctocx'] == "SS" || $mCodDat[$k]['comctocx'] == "SC"){
            if($mCodDat[$k]['comctocx'] == "SC"){
              $cSaldo = "FAVOR";
            } else {
              $cSaldo = "CARGO";
            }
            $nTotPag += $mCodDat[$k]['comvlrxx'];
          }
        }
        // Fin Busco Valor a Pagar
        
        /*
         * En caso de que el valor a pagar de la Factura sea cero, en detalle no se guarda registro SS o SC,
         * Razon por la cual no se muestra el valor del anticipo que fue aplicado.
         * Para imprimir este valor se debe tomar el campo comfpxx de cabecera, posicion 13 donde se guarda el valor del anticipo
         */
        $nTotAnt = 0;
        if ($vCocDat['CLINRPXX'] == "SI") {
          for ($k=0;$k<count($mCodDat);$k++) {
            if($mCodDat[$k]['comctocx'] == 'CD' && strpos($mCodDat[$k]['comobsxx'],'ANTICIPOS') > 0){
              $nTotAnt += $mCodDat[$k]['comvlrme'];
            }
          }
        } else {
          $mComFp = f_Explode_Array($vCocDat['comfpxxx'],"|","~");
          for ($k=0;$k<count($mComFp);$k++) {
            if($mComFp[$k][13] != "" && $mComFp[$k][13] != 0){
              $nTotAnt += $mComFp[$k][13];
            }
          }
        }
        $nTotAnt = abs($nTotAnt);
        //Fin de Recorrido al campo comfpxxx para imprimir valor de anticipo.

        $cCurrency = $vCocDat['CLINRPXX'] != "SI" ? "PESO" : "DOLAR";
        $nTotPag1 = f_Cifra_Php(str_replace("-","",abs($nTotPag)),$cCurrency);
        
        $nSaldoFavor = 0;
        $nTotAntCruce = abs($nTotAnt);
        if ($cSaldo == "FAVOR" || $nTotPag == 0) {
          //El saldo a favor es el valor de SC y el valor total es cero
          $nSaldoFavor = $nTotPag;
          $nTotPag = 0;

          //Calculando el anticipo utilizado
          $nTotAntCruce = abs($nTotAnt - $nSaldoFavor);
        }

        if($pyy > $nPosYVl){//Validacion para siguiente pagina si se excede espacio de impresion
          $pdf->Line($nPosX+20,$nPosY-6,$nPosX+20,($nPosFin+10));
          $pdf->Line($nPosX+86,$nPosY-6,$nPosX+86,($nPosFin+10));
          $pdf->Line($nPosX+101,$nPosY-6,$nPosX+101,($nPosFin+10));
          $pdf->Line($nPosX+125,$nPosY-6,$nPosX+125,($nPosFin+10));
          $pdf->Line($nPosX+136,$nPosY-6,$nPosX+136,($nPosFin+10));
          $pdf->Line($nPosX+149,$nPosY-6,$nPosX+149,($nPosFin+10));
          $pdf->Line($nPosX+166,$nPosY-6,$nPosX+166,($nPosFin+10));
          $pdf->Rect($nPosX, $nPosY-6, 205, ($nPosFin+10) - ($nPosY - 6));
          $pdf->AddPage();
          $nb++;
          $nPosY = $pdf->GetY()+6;
          $nPosX = 5;
          $pyy = $nPosYVl;
          $pdf->SetFont($cEstiloLetra,'',8);
          $pdf->setXY($nPosX,$pyy);
        } else {
          $pyy = $nPosYVl;
          $pdf->SetFont($cEstiloLetra,'',8);
          $pdf->setXY($nPosX,$pyy);
        }

        $pdf->Line($nPosX+20,$nPosY-6,$nPosX+20,$pyy);
        $pdf->Line($nPosX+86,$nPosY-6,$nPosX+86,$pyy);
        $pdf->Line($nPosX+101,$nPosY-6,$nPosX+101,$pyy);
        $pdf->Line($nPosX+125,$nPosY-6,$nPosX+125,$pyy);
        $pdf->Line($nPosX+136,$nPosY-6,$nPosX+136,$pyy);
        $pdf->Line($nPosX+149,$nPosY-6,$nPosX+149,$pyy);
        $pdf->Line($nPosX+166,$nPosY-6,$nPosX+166,$pyy);
        $pdf->Rect($nPosX, $nPosY-6, 205, $pyy - ($nPosY - 6));

        $pdf->setXY($nPosX, $pyy + 1);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->MultiCell(135, 3.2, "OBSERVACIONES: \n" . utf8_decode($vCocDat['comobsxx']).". ".substr($cObsPedido, 0, -2), 0, 'L');

        $pdf->setXY($nPosX, $pyy + 23);
        $pdf->SetFont('Arial', '', 8);
        $pdf->MultiCell(135, 3.2, utf8_decode("AGENTES RETENEDORES DE IVA - NO SOMOS GRANDES CONTRIBUYENTES - SOMOS AUTORRETENEDORES EN RENTA RESOLUCIÓN DIAN 005315 DE JUNIO 26 DE 2013."), 0, 'L');
        $pdf->setX($nPosX);
        //Resolucion de facturacion
        $cResolucion = "Somos Autorretenedores de ICA en: Cartagena, Barranquilla, Santa Marta y Riohacha. Facturación autorizada por la DIAN mediante formulario No. ". $vResDat['residxxx'] ." de ". $vResDat['resfdexx'] ." Numeración ". $vResDat['resprexx'] ." ". $vResDat['resdesxx'] ." al ".  $vResDat['resprexx'] ." ". $vResDat['reshasxx'] ." impresa por AGENCIA DE ADUANAS MARIO LONDOÑO S.A. NIVEL 1 NIT.890.902.266-2.";
        $pdf->MultiCell(135, 3.2, utf8_decode($cResolucion), 0, 'L');
        $pdf->setX($nPosX);
        $pdf->MultiCell(135, 3.2, utf8_decode("Esta factura causara intereses moratorios a la tasa mas alta autorizada por ley a partir de la fecha acordada para el pago, si este fuera incumplido."), 0, 'L');

        $pdf->setXY($nPosX, $pyy + 54);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->MultiCell(135, 3.2, utf8_decode("Consignar en Banco de Occidente 400058467 Cta. cte, Bancolombia 60400010197 Cta. cte, a nombre de Agencia de Aduanas Mario Londoño."), 0, 'L');

        $pdf->setXY($nPosX + 137, $pyy + 50);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(68, 4, "TOTAL EN LETRAS", 0, 0, 'C');
        $pdf->Ln(4);
        $pdf->setX($nPosX + 137);
        $pdf->SetFont('Arial', '', 7);
        $pdf->MultiCell(68, 3, utf8_decode($nTotPag1), 0, 'C');

        ### Columna de Subtotales ##
        //Para la nota debito el campo de ip gravados se trae del subtotal de ingresos porpios gravados
        $nTotFac = (floatval($nTotPcc + $nSubTotGmf + $nSubToIPGra + $nSubToIPNoGra + $nSubToIPIva)) - ($nTotIca + $nTotIva);
        $pdf->setXY($nPosX + 136, $pyy + 1);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(35, 4, "SUBTOTAL SERVICIOS GRAVADOS", 0, 0, 'L');
        $pdf->Cell(34, 4, number_format(floatval($nSubToIPGra), 0), 0, 0, 'R');
        $pdf->Ln(4.5);
        $pdf->setX($nPosX + 136);
        $pdf->Cell(35, 4, "+ IVA", 0, 0, 'L');
        $pdf->Cell(34, 4, number_format($nSubToIPIva, 0), 0, 0, 'R');
        $pdf->Ln(4.5);
        $pdf->setX($nPosX + 136);
        $pdf->Cell(35, 4, "- RETE IVA", 0, 0, 'L');
        $pdf->Cell(34, 4, number_format($nTotIva, 0), 0, 0, 'R');
        $pdf->Ln(4.5);
        $pdf->setX($nPosX + 136);
        $pdf->Cell(35, 4, "- RETE ICA", 0, 0, 'L');
        $pdf->Cell(34, 4, number_format(($nTotAIca == 0) ? $nTotIca : 0, 0), 0, 0, 'R');
        $pdf->Ln(4.5);
        $pdf->setX($nPosX + 136);
        $pdf->Cell(35, 4, "TOTAL PAGOS, SERVICIOS E IVA", 0, 0, 'L');
        $pdf->Cell(34, 4, number_format(floatval($nTotPcc + $nSubTotGmf + $nSubToIPGra + $nSubToIPNoGra + $nSubToIPIva), 0), 0, 0, 'R');
        $pdf->Ln(4.5);
        $pdf->setX($nPosX + 136);
        $pdf->Cell(35, 4, "TOTAL FACTURA", 0, 0, 'L');
        $pdf->Cell(34, 4, number_format($nTotFac, 0), 0, 0, 'R');
        $pdf->Ln(4.5);
        $pdf->setX($nPosX + 136);
        $pdf->Cell(35, 4, "- ANTICIPOS", 0, 0, 'L');
        $pdf->Cell(34, 4, number_format($nTotAnt, 0), 0, 0, 'R');
        $pdf->Ln(4.5);
        $pdf->setX($nPosX + 136);
        $pdf->Cell(35, 4, "TOTAL A PAGAR", 0, 0, 'L');
        $pdf->Cell(34, 4, number_format($nTotPag, 0), 0, 0, 'R');
        $pdf->Ln(4.5);
        $pdf->setX($nPosX + 136);
        $pdf->Cell(35, 4, "TOTAL SALDO A FAVOR", 0, 0, 'L');
        $pdf->Cell(34, 4, number_format($nSaldoFavor, 0), 0, 0, 'R');

        $pdf->Line($nPosX, $pyy + 20, $nPosX + 136, $pyy + 20);
        $pdf->Line($nPosX, $pyy + 50, $nPosX + 205, $pyy + 50);
        $pdf->Line($nPosX + 136, $pyy, $nPosX + 136, $pyy + 65);
        $pdf->Rect($nPosX, $pyy, 205, 65);

        // Fin Impresion pagos, servicios e IVA.
        //////////////////////////////////////////////////////////////////////////////////
      }//for($y=1; $y<=2; $y++){

      if( $gCorreo == 1){
        $cNomFile    = "Factura_$cComId-$cComCod-$cComCsc.pdf";
        $cNomFileCer = "Certificado_Mandato_$cComId-$cComCod-$cComCsc.pdf";
        $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/$cNomFile";
      }else{
        $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
      }

      //$pdf->Output(); // DESCOMENTAR ESTA LINEA PARA DESARROLLO
      $pdf->Output($cFile);

      if (file_exists($cFile)){
        chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
        if( $gCorreo == 1 ){

          $nInd_vFile = count($vFile);
          $vFile[$nInd_vFile]['ruta']    = $cFile;
          $vFile[$nInd_vFile]['archivo'] = $cNomFile;

          $nInd_vFile = count($vFile);
          $vFile[$nInd_vFile]['ruta']    = $cRutCerMan;
          $vFile[$nInd_vFile]['archivo'] = $cNomFileCer;

          $vReturn = array();
          $vReturn = fnEnviarFactura($vFile,$cComId,$cComCod,$cComCsc,$cComCsc2,$vCocDat['terid2xx'],$vCocDat['CLINOMXX'],$cDocId,(($cPedOrd != "") ? $cPedOrd : $cPedido));
          $vReturnCorreos[count($vReturnCorreos)] = $vReturn;
          $vFile = array();
        }

      } else {
        f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
      }

      if( $gCorreo == 1 ){
        for($nC=0; $nC<count($vReturnCorreos);$nC++) {
          for($nM=0; $nM<count($vReturnCorreos[$nC]); $nM++) {
            if ($vReturnCorreos[$nC][$nM] != "") {
              $cMsj .= $vReturnCorreos[$nC][$nM]."\n";
            }
          }
        }
        f_Mensaje(__FILE__,__LINE__,$cMsj);
      }else{
        echo "<html><script>document.location='$cFile';</script></html>"; // COMENTAR ESTA LINEA PARA DESARROLLO
      }
    }
  }

  /** Funcion para el envio de la factura por correo electronico, recibe como parametros
   * @param $xFile    -> Ruta del archivo de la factura
   * @param $xComCsc  -> Consecutivo de la factura
   * @param $xTerId2  -> Facturar a, a quien se le facturo
   */
  function fnEnviarFactura($xFile,$xComId,$xComCod,$xComCsc,$xComCsc2,$xTerId2,$xTerNom2,$xTramite,$xPedido) {
    global $cAlfa; global $xConexion01; global $vSysStr;

    $nSwitch = 0; $vMsj = array();

    $cMailerUsr   = $vSysStr['system_financiero_correo_factura_user'];
    $cMailerPass  = $vSysStr['system_financiero_correo_factura_pas'];
    $cMailerSmtp  = $vSysStr['system_financiero_correo_factura_smtp'];
    $cMailerPto   = $vSysStr['system_financiero_correo_factura_puerto'];

    //Enviar de Factura por correo
    $vReturnCorreos = fnContactos($xTerId2);

    $vCorreos = array();
    for ($nC=0; $nC<count($vReturnCorreos); $nC++) {
      if ($vReturnCorreos[$nC] != "") {
        $vAxuCor = explode(",", $vReturnCorreos[$nC]);
        for ($nA=0; $nA<count($vAxuCor); $nA++) {
          $vCorreos[] = trim($vAxuCor[$nA]);
        }
      }
    }

    if (count($vCorreos) > 0) {

      $cDominio = "opentecnologia.com.co";

      $cSubject = "Factura Malco No. $xComId-$xComCod-$xComCsc DO: $xTramite".(($xPedido != "") ? " PEDIDO: $xPedido" : "");

      $cMessage  = "Se&ntilde;ores,<br>";
      $cMessage .= "<b>$xTerNom2</b><br><br>";
      $cMessage .= "Estamos enviando copia virtual de su factura original No. $xComId-$xComCod-$xComCsc en formato PDF. ";
      $cMessage .= "Este documento es de caracter informativo, su factura original llegar&aacute; adjunta con sus soportes pr&oacute;ximamente.<br><br>";
      $cMessage .= "Si tiene alguna observaci&oacute;n frente a esta factura, por favor comunicarse directamente con su asesor.";
      $cMessage .= "<br><br>";
      $cMessage .= "<b><font color='red'>Correo autogenerado, por favor no lo responda.</font></b>";
      $cMessage .= "<br><br>";

      $cFrom = "Facturacion Malco <no-reply@$cDominio>";
      $cHeaders = "From: $cFrom";

      // boundary
      $semi_rand = md5(time());
      $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

      // headers for attachment
      $cHeaders .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";

      // multipart boundary
      $cMessage = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"iso-8859-1\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $cMessage . "\n\n";
      $cMessage .= "--{$mime_boundary}\n";

      for ($nA=0; $nA<count($xFile); $nA++) {
        if(filesize($xFile[$nA]['ruta']) <= (1024*1024)){
          $file = fopen($xFile[$nA]['ruta'],"rb");
          $data = fread($file,filesize($xFile[$nA]['ruta']));
          fclose($file);
          $data = chunk_split(base64_encode($data));
          $name = $xFile[$nA]['archivo'];
          $cMessage .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$name\"\n";
          $cMessage .= "Content-Disposition: attachment;\n" . " filename=\"$name\"\n";
          $cMessage .= "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
          $cMessage .= "--{$mime_boundary}\n";

        } else {
          $nSwitch = 1;
          $vMsj[count($vMsj)] = "Para la Factura [$xComId-$xComCod-$xComCsc] Archivo Adjunto [$xFile[$nA]['archivo']] Supera Limite de Tamano [1024]. Favor Comunicar este Error a openTecnologia S.A.\n";
        }
      }

      // send
      //Enviando correos a los contactos y director(es) de Cuenta del o los Do
      $cCorreos = "";
      for ($nC=0; $nC<count($vCorreos); $nC++) {
        $xMail = mail($vCorreos[$nC], $cSubject, $cMessage, $cHeaders);
        if(!$xMail){
          $nSwitch = 1;
          $vMsj[count($vMsj)] = "Para la Factura [$xComId-$xComCod-$xComCsc] Error al Enviar Correo al destinatario [{$vCorreos[$nC]}]. Favor Comunicar este Error a openTecnologia S.A.\n";
        }
        $cCorreos .= "{$vCorreos[$nC]}, ";
      }
      $cCorreos = substr($cCorreos, 0, strlen($cCorreos)-2);

      if($nSwitch == 0) {
        $nSwitch = 2;
        $vMsj[count($vMsj)] = "Se Envio la Factura [$xComId-$xComCod-$xComCsc] y Certificado de Mandato con Exito a los Siguientes Correos:\n$cCorreos.\n";
      }
    }

    if ($nSwitch == 1 || $nSwitch == 2) {
      return $vMsj;
    }
  }

  //Funcion que retorna los contactos a quien debe enviarseles el correo
  function fnContactos($xTerId2) {
    global $cAlfa; global $xConexion01; global $vSysStr;

    //Buscado los destinarios del Correo
    //Si el Cliente del DO tiene parametrizados contactos
    //debe realizarse la busqueda de los contactos y enviar el correo
    $qContactos  = "SELECT ";
    $qContactos .= "$cAlfa.sys00122.conidxxx, ";
    $qContactos .= "$cAlfa.SIAI0150.CLIEMAXX AS cliemaxx  ";
    $qContactos .= "FROM $cAlfa.sys00122 ";
    $qContactos .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.sys00122.conidxxx = $cAlfa.SIAI0150.CLIIDXXX ";
    $qContactos .= "WHERE ";
    $qContactos .= "$cAlfa.sys00122.cliidxxx = \"$xTerId2\" AND  ";
    $qContactos .= "$cAlfa.sys00122.iacefxxx = \"SI\" AND  ";
    $qContactos .= "$cAlfa.sys00122.regestxx = \"ACTIVO\" AND  ";
    $qContactos .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" AND  ";
    $qContactos .= "$cAlfa.SIAI0150.CLIEMAXX != \"\"";
    $xContactos  = f_MySql("SELECT","",$qContactos,$xConexion01,"");
    // f_Mensaje(__FILE__,__LINE__,$qContactos."~".mysql_num_rows($xContactos));
    $vCorreos = array();
    while($xRC  = mysql_fetch_array($xContactos)) {
      if (in_array($xRC['cliemaxx'],$vCorreos) == false) {
        $vCorreos[count($vCorreos)] = $xRC['cliemaxx'];
      }
    }

    $qEmaAdi  = "SELECT $cAlfa.SIAI0150.CLIPCECN ";
    $qEmaAdi .= "FROM $cAlfa.SIAI0150 ";
    $qEmaAdi .= "WHERE ";
    $qEmaAdi .= "$cAlfa.SIAI0150.CLIIDXXX = \"$xTerId2\" AND  ";
    $qEmaAdi .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" LIMIT 0,1";
    $xEmaAdi  = f_MySql("SELECT","",$qEmaAdi,$xConexion01,"");
    $vEmaAdi  = mysql_fetch_array($xEmaAdi);

    $vCorreosAdicionales = explode(',',$vEmaAdi['CLIPCECN']);
    for ($i=0; $i <count($vCorreosAdicionales); $i++) {
      if ($vCorreosAdicionales[$i] != ''){
        if (in_array($vCorreosAdicionales[$i],$vCorreos) == false) {
          $vCorreos[count($vCorreos)] = trim($vCorreosAdicionales[$i]);
        }
      }
    }

    return $vCorreos;
  }
?>

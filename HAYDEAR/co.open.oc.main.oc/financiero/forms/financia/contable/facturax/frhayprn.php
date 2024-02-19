<?php
  /**
   * Nuevo formato factura de venta HAYDEAR.
   * --- Descripcion: Permite Imprimir Factura de Venta de HAYDEAR por Vista Definitiva.
   * @author Elian Amado Ramirez. <elian.amado@openits.co>
   * @package openComex
   * @version 001
   */
  
  include("../../../../libs/php/utility.php");
  include("../../../../libs/php/utiliqdo.php");
  
  //Generacion del codigo QR
  require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/phpqrcode/qrlib.php');
  
  define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
  require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');
  
  // Variables de control de errores
  $nSwitch = 0;

  // Validacion de Comprobante Repetido
  $mPrints = f_Explode_Array($prints,"|","~");
  $cAno    =  substr($mPrints[0][4],0,4);
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
    if ($i < (count($mCodCom) -1)) { 
      $cCodigos_Comprobantes .= ","; 
    }
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
    $nSwitch = 1;
    f_Mensaje(__FILE__,__LINE__,"El Documento [{$mPrints[0][0]}-{$mPrints[0][1]}-{$mPrints[0][2]}] No se puede Imprimir porque su Consecutivo se Encuentra Repetido en el Modulo de Contabilidad, Verifique");
  }
  // Fin de Validacion de Comprobante Repetido

  $vMemo      = explode("|",$prints);
  $permisos   = 0;
  $zCadPer    = "|";
  $resolucion = 0;
  $zCadRes    = "|";
  $fomularios = 0;
  $zCadFor    = "";

  for($u=0; $u<count($vMemo); $u++) {
    if ($vMemo[$u]!=""){
      $zMatriz=explode("~",$vMemo[$u]);
      ////// CABECERA 1001 /////
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
        if($vCocDat['comprnxx']=="IMPRESO" && $vCocDat['regestxx'] != "INACTIVO" && $_COOKIE['kModo'] != "VERFACTURA"){
          $zFac=$zMatriz[0].$zMatriz[1]."-".$zMatriz[2]."|";
          $zCadPer .=$zFac;
          $permisos=1;
        }
      }
    }
  }

  if($permisos == 1){
    $nSwitch=1;
    f_Mensaje(__FILE__,__LINE__,"Las Siguientes Facturas No tienen Permiso de Impresion [$zCadPer], Verifique.");?>
    <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
    <script languaje = "javascript">
      parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
      document.forms['frgrm'].submit();
    </script>
    <?php
  }

  if($fomularios == 1){
    $nSwitch=1;
    f_Mensaje(__FILE__,__LINE__,"Las Siguientes Facturas Presentan Inconsistencias con Formularios: \n $zCadFor --- Verifique --- ");?>
    <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
    <script languaje = "javascript">
      parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
      document.forms['frgrm'].submit();
    </script>
    <?php
  }

  if($resolucion == 1){
    $nSwitch=1;
    f_Mensaje(__FILE__,__LINE__,"Las Siguientes Facturas no Tienen Resolucion Activa [$zCadRes], Verifique."); ?>
    <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
    <script languaje = "javascript">
      parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
      document.forms['frgrm'].submit();
    </script>
    <?php
  }

  $mPrn = explode("|",$prints);
  for ($nn=0;$nn<count($mPrn);$nn++) {
    if (strlen($mPrn[$nn]) > 0) {
      $vComp = explode("~",$mPrn[$nn]);
      $cComId   = $vComp[0];
      $cComCod  = $vComp[1];
      $cComCsc  = $vComp[3];
      $cComCsc2 = $vComp[3];
      $cRegFCre = $vComp[4];
      $cAno     =  substr($cRegFCre,0,4);
    }
  }
  
  // Codigo para actualizar campo de impresion
  if($vCocDat['regestxx'] != "INACTIVO" && $_COOKIE['kModo'] != "VERFACTURA") {
    $mUpdPrn = array(array('NAME'=>'comprnxx','VALUE'=>'IMPRESO'  ,'CHECK'=>'SI'),
                      array('NAME'=>'comidxxx','VALUE'=>$cComId    ,'CHECK'=>'WH'),
                      array('NAME'=>'comcodxx','VALUE'=>$cComCod   ,'CHECK'=>'WH'),
                      array('NAME'=>'comcscxx','VALUE'=>$cComCsc   ,'CHECK'=>'WH'),
                      array('NAME'=>'comcsc2x','VALUE'=>$cComCsc2  ,'CHECK'=>'WH'));
    if (f_MySql("UPDATE","fcoc$cAno",$mUpdPrn,$xConexion01,$cAlfa)){
    }else{
      $nSwitch = 1;
    }
  }
  // Fin Codigo para actualizar campo de impresion

  // MOVIMIENTO CABECERA
  $qCocDat  = "SELECT ";
  $qCocDat .= "$cAlfa.fcoc$cAno.*, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX, ";
  $qCocDat .= "$cAlfa.SIAI0150.CLINRPXX, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.CLIDIRXX != \"\",$cAlfa.SIAI0150.CLIDIRXX,\"SIN DIRECCION\") AS CLIDIRXX, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.CLITELXX != \"\",$cAlfa.SIAI0150.CLITELXX,\"SIN TELEFONO\") AS CLITELXX, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.CLIEMAXX != \"\",$cAlfa.SIAI0150.CLIEMAXX,\"SIN CORREO\") AS CLIEMAXX, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.PAIIDXXX != \"\",$cAlfa.SIAI0150.PAIIDXXX,\"\") AS PAIIDXXX, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.DEPIDXXX != \"\",$cAlfa.SIAI0150.DEPIDXXX,\"\") AS DEPIDXXX, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.CIUIDXXX != \"\",$cAlfa.SIAI0150.CIUIDXXX,\"\") AS CIUIDXXX ";
  $qCocDat .= "FROM $cAlfa.fcoc$cAno ";
  $qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcoc$cAno.terid2xx = $cAlfa.SIAI0150.CLIIDXXX ";
  $qCocDat .= "WHERE $cAlfa.fcoc$cAno.comidxxx = \"$cComId\" AND ";
  $qCocDat .= "$cAlfa.fcoc$cAno.comcodxx = \"$cComCod\" AND ";
  $qCocDat .= "$cAlfa.fcoc$cAno.comcscxx = \"$cComCsc\" AND ";
  $qCocDat .= "$cAlfa.fcoc$cAno.comcsc2x = \"$cComCsc2\" LIMIT 0,1";
  $xCocDat  = f_MySql("SELECT","",$qCocDat,$xConexion01,"");
  $vComObs2 = array();
  if (mysql_num_rows($xCocDat) > 0) {
    $vCocDat  = mysql_fetch_array($xCocDat);
    $vComObs2 = explode("~",$vCocDat['comobs2x']);
  }

  // Traigo la Descripcion de la Ciudad del Facturado A
  $qCiuFac  = "SELECT CIUDESXX ";
  $qCiuFac .= "FROM $cAlfa.SIAI0055 ";
  $qCiuFac .= "WHERE ";
  $qCiuFac .= "$cAlfa.SIAI0055.PAIIDXXX = \"{$vCocDat['PAIIDXXX']}\" AND ";
  $qCiuFac .= "$cAlfa.SIAI0055.DEPIDXXX = \"{$vCocDat['DEPIDXXX']}\" AND ";
  $qCiuFac .= "$cAlfa.SIAI0055.CIUIDXXX = \"{$vCocDat['CIUIDXXX']}\" AND ";
  $qCiuFac .= "$cAlfa.SIAI0055.REGESTXX = \"ACTIVO\" LIMIT 0,1";
  $xCiuFac  = f_MySql("SELECT","",$qCiuFac,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qCiuFac."~".mysql_num_rows($xCiuFac));
  if (mysql_num_rows($xCiuFac) > 0) {
    $vCiuFac = mysql_fetch_array($xCiuFac);
  }
  // Fin la Descripcion de la Traigo Ciudad del Facturado A

  // Traigo el CLINOMXX o Razon Social de la Agencia
  $qAgenDat  = "SELECT ";
  $qAgenDat .= "$cAlfa.SIAI0150.CLIIDXXX, ";
  $qAgenDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX, ";
  $qAgenDat .= "$cAlfa.SIAI0150.CLIDIRXX, ";
  $qAgenDat .= "$cAlfa.SIAI0150.CLITELXX, ";
  $qAgenDat .= "$cAlfa.SIAI0150.CLIEMAXX, ";
  $qAgenDat .= "IF($cAlfa.SIAI0150.PAIIDXXX != \"\",$cAlfa.SIAI0150.PAIIDXXX,\"\") AS PAIIDXXX, ";
  $qAgenDat .= "IF($cAlfa.SIAI0150.DEPIDXXX != \"\",$cAlfa.SIAI0150.DEPIDXXX,\"\") AS DEPIDXXX, ";
  $qAgenDat .= "IF($cAlfa.SIAI0150.CIUIDXXX != \"\",$cAlfa.SIAI0150.CIUIDXXX,\"\") AS CIUIDXXX ";
  $qAgenDat .= "FROM $cAlfa.SIAI0150 ";
  $qAgenDat .= "WHERE ";
  $qAgenDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$vSysStr['financiero_nit_agencia_aduanas']}\" AND ";
  $qAgenDat .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
  $xAgenDat  = f_MySql("SELECT","",$qAgenDat,$xConexion01,"");
  $vAgenDat  = array();
  if (mysql_num_rows($xAgenDat) > 0) {
    $vAgenDat = mysql_fetch_array($xAgenDat);
  }
  // Fin Traigo el CLINOMXX o Razon Social de la Agencia

  // Traigo la Descripcion de la Ciudad de la Agencia
  $qCiuAgen  = "SELECT CIUDESXX ";
  $qCiuAgen .= "FROM $cAlfa.SIAI0055 ";
  $qCiuAgen .= "WHERE ";
  $qCiuAgen .= "$cAlfa.SIAI0055.PAIIDXXX = \"{$vAgenDat['PAIIDXXX']}\" AND ";
  $qCiuAgen .= "$cAlfa.SIAI0055.DEPIDXXX = \"{$vAgenDat['DEPIDXXX']}\" AND ";
  $qCiuAgen .= "$cAlfa.SIAI0055.CIUIDXXX = \"{$vAgenDat['CIUIDXXX']}\" AND ";
  $qCiuAgen .= "$cAlfa.SIAI0055.REGESTXX = \"ACTIVO\" LIMIT 0,1";
  $xCiuAgen  = f_MySql("SELECT","",$qCiuAgen,$xConexion01,"");
  $vCiuAgen  = array();
  if (mysql_num_rows($xCiuAgen) > 0) {
    $vCiuAgen = mysql_fetch_array($xCiuAgen);
  }
  // Fin Traigo la Descripcion de la Ciudad de la Agencia

  // Traigo la Descripcion del Pais de la Agencia
  $qPaisAgen  = "SELECT ";
  $qPaisAgen .= "PAIDESXX ";
  $qPaisAgen .= "FROM $cAlfa.SIAI0052 ";
  $qPaisAgen .= "WHERE ";
  $qPaisAgen .= "PAIIDXXX = \"{$vAgenDat['PAIIDXXX']}\"";
  $xPaisAgen  = f_MySql("SELECT","",$qPaisAgen,$xConexion01,"");
  $vPaisAgen  = array();
  if (mysql_num_rows($xPaisAgen) > 0) {
    $vPaisAgen = mysql_fetch_array($xPaisAgen);
  }
  // Fin Traigo la Descripcion del Pais de la Agencia

  // MOVIMIENTO DETALLE 
  $qCodDat  = "SELECT DISTINCT ";
  $qCodDat .= "$cAlfa.fcod$cAno.* ";
  $qCodDat .= "FROM $cAlfa.fcod$cAno ";
  $qCodDat .= "WHERE $cAlfa.fcod$cAno.comidxxx = \"$cComId\" AND ";
  $qCodDat .= "$cAlfa.fcod$cAno.comcodxx = \"$cComCod\" AND ";
  $qCodDat .= "$cAlfa.fcod$cAno.comcscxx = \"$cComCsc\" AND ";
  $qCodDat .= "$cAlfa.fcod$cAno.comcsc2x = \"$cComCsc2\" ORDER BY ABS($cAlfa.fcod$cAno.comseqxx) ASC ";
  $xCodDat  = f_MySql("SELECT","",$qCodDat,$xConexion01,"");

  // Matriz para pagos de 4xmil GMF e Ingresos Propios
  $mDatGmf = array();
  $mDatIP  = array();
  if(mysql_num_rows($xCodDat) > 0) {
    // Cargo la Matriz con los ROWS del Cursor
    $mCodDat = array();
    $iA = 0;
    while ($xRCD = mysql_fetch_array($xCodDat)) {
      $mCodDat[$iA] = $xRCD;
      $iA++;

      if($xRCD['comctocx'] == "PCC" && $xRCD['comidc2x'] != "X"){
        $nInd_mDatGmf = count($mDatGmf);
        $mDatGmf[$nInd_mDatGmf]['ctoidxxx'] = $xRCD['ctoidxxx'];
        $mDatGmf[$nInd_mDatGmf]['comobsxx'] = $xRCD['comobsxx'];
        $mDatGmf[$nInd_mDatGmf]['comvlrxx'] = $xRCD['comvlrxx'];
        $mDatGmf[$nInd_mDatGmf]['puctipej'] = $xRCD['puctipej'];
        $mDatGmf[$nInd_mDatGmf]['comvlr01'] = $xRCD['comvlr01'];
      }elseif ($xRCD['comctocx'] == "PCC"){
        $nInd_mValores = count($mValores);
        $mValores[$nInd_mValores]['ctoidxxx'] = $xRCD['ctoidxxx'];
        $mValores[$nInd_mValores]['comobsxx'] = $xRCD['comobsxx'];
        $mValores[$nInd_mValores]['comvlrxx'] = $xRCD['comvlrxx'];
        $mValores[$nInd_mValores]['puctipej'] = $xRCD['puctipej'];
        $mValores[$nInd_mValores]['comvlr01'] = $xRCD['comvlr01'];
      }elseif ($xRCD['comctocx'] == "IP"){
        // Traigo las cantidades y el detalle de los IP del utiliqdo.php
        $vDatosIp = array();
        $vDatosIp = f_Cantidad_Ingreso_Propio($xRCD['comobsxx'],'',$xRCD['sucidxxx'],$xRCD['docidxxx'],$xRCD['docsufxx']);

        $nInd_mDatIP = count($mDatIP);
        $mDatIP[$nInd_mDatIP] = $xRCD;
        $mDatIP[$nInd_mDatIP]['comobsxx'] = $vDatosIp[0];
        $mDatIP[$nInd_mDatIP]['comvlrxx'] = $xRCD['comvlrxx'];
        $mDatIP[$nInd_mDatIP]['compivax'] = $xRCD['compivax']; // Porcentaje IVA
        $mDatIP[$nInd_mDatIP]['comvlr01'] = $xRCD['comvlr01']; // Valor Iva
        $mDatIP[$nInd_mDatIP]['unidadfe'] = $vDatosIp[2];
        //Cantidad FE
        $mDatIP[$nInd_mDatIP]['canfexxx'] += $vDatosIp[1];
        //Cantidad por condicion especial
        for ($nP=0; $nP<count($vDatosIp[3]); $nP++) {
          $mDatIP[$nInd_mDatIP]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] += $vDatosIp[3][$nP]['valpdfxx'];
        }
      }
    }
  }

  $cDocId  = "";
  $cDocSuf = "";
  $mDoiId  = explode("|",$vCocDat['comfpxxx']);
  for ($i=0;$i<count($mDoiId);$i++) {
    if($mDoiId[$i] != ""){
      $vDoiId  = explode("~",$mDoiId[$i]);
      $cDocId  = $vDoiId[2];
      $cDocSuf = $vDoiId[3];
      $cSucId  = $vDoiId[15];
      $i = count($mDoiId);
    }
  }

  // Trayendo Datos de Do Dependiendo del Tipo de Operacion
  $vDatDo = f_Datos_Operativos_Do($cSucId, $cDocId, $cDocSuf);
  $vDceDat = $vDatDo['decdatxx'];
  $cTasCam = $vDatDo['tascamxx']; //Tasa de Cambio
  $cDocTra = $vDatDo['doctraxx']; //Documento de Transporte
  $cBultos = $vDatDo['bultosxx']; //Bultos
  $cPesBru = $vDatDo['pesbruxx']; //Peso Bruto
  $cPesNet = $vDatDo['pesnetxx']; //Peso Neto
  $nValAdu = $vDatDo['valaduxx']; //Valor en aduana
  $cOpera  = $vDatDo['operaxxx']; //texto valor CIF: o FOB: segun el tipo operacion del tramite
  $cPedido = $vDatDo['pedidoxx']; //Pedido
  $cAduana = $vDatDo['aduanaxx']; //Descripcion Aduana
  $cNomVen = $vDatDo['nomvenxx']; //Nombre Vendedor
  $cCodPos = $vDatDo['clicposx']; //Codigo Postal Vendedor
  $cOrdCom = $vDatDo['ordcomxx']; //Orden de Compra
  $cPaiOri = $vDatDo['paiorixx']; //Pais de Origen
  $dReaArr = $vDatDo['fecrearr']; //Fecha real de arribo
  $cCiuOri = $vDatDo['ciuorixx']; //Ciudad Origen
  $cCiuDes = $vDatDo['ciudesxx']; //Ciudad Destino
  $cFecTra = $vDatDo['dgefdtxx']; //Fecha Doc. Transporte
  $cDoitra = $vDatDo['tradesxx']; //Transportadora
  $cCodEmb = $vDatDo['temidxxx']; //Codigo Embalaje
  $cNomImp = $vDatDo['nomimpor']; //Nombre de Importador
  // Fin Trayendo Datos de Do Dependiendo del Tipo de Operacion

  // Recorrido para saber si hay registros PCC e IP y poder controlar la impresion de bloques de Pagos a Terceros e Ingresos Propios
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
  // Fin Recorrido para saber si hay registros PCC e IP y poder controlar la impresion de bloques de Pagos a Terceros e Ingresos Propios

  // Traigo los datos de resolucion
  $qResDat  = "SELECT * ";
  $qResDat .= "FROM $cAlfa.fpar0138 ";
  $qResDat .= "WHERE ";
  $qResDat .= "rescomxx LIKE \"%{$cComId}~{$cComCod}%\" AND ";
  $qResDat .= "regestxx = \"ACTIVO\" LIMIT 0,1";
  $xResDat  = f_MySql("SELECT","",$qResDat,$xConexion01,"");
  $nFilRes  = mysql_num_rows($xResDat);
  if ($nFilRes > 0) {
    $vResDat = mysql_fetch_array($xResDat);
  }
  // Fin Traigo los datos de resolucion

  // Traigo el codigo de la Unidad de medida por Concepto
  $qCtoCon  = "SELECT ";
  $qCtoCon .= "ctoidxxx, ";
  $qCtoCon .= "ctoclapr, ";
  $qCtoCon .= "cceidxxx, ";
  $qCtoCon .= "umeidxxx, ";
  $qCtoCon .= "ctochald ";
  $qCtoCon .= "FROM $cAlfa.fpar0121 ";
  $xCtoCon  = mysql_query($qCtoCon, $xConexion01);
  while ($xRC = mysql_fetch_assoc($xCtoCon)) {
    $vCtoCon["{$xRC['ctoidxxx']}"] = $xRC;
  }
  // Fin Traigo el codigo de la Unidad de medida por Concepto

  // Codigo para imprimir los ingresos para terceros
  $mIT     = f_Explode_Array($vCocDat['commemod'],"|","~");
  $mIngTer = array();
  for ($i=0;$i<count($mIT);$i++) {
    //Traer descripcion concepto
    $vDesc = explode("^",$mIT[$i][2]);
    $nInd_mIngTer = count($mIngTer);
    $mIngTer[$nInd_mIngTer]['ctodesxx'] = trim($vDesc[0]) . " " . $vDesc[1];//Descripcion
    $mIngTer[$nInd_mIngTer]['ctoidxxx'] = $mIT[$i][1];//Codigo concepto
    $mIngTer[$nInd_mIngTer]['baseivax'] = $mIT[$i][15];//Base Iva
    $mIngTer[$nInd_mIngTer]['ivaxxxxx'] = $mIT[$i][16]+0;//Iva
    $mIngTer[$nInd_mIngTer]['totalxxx'] = $mIT[$i][7];//Total
    $mIngTer[$nInd_mIngTer]['facturax'] = $mIT[$i][5];//Factura
    $mIngTer[$nInd_mIngTer]['umeidxxx'] = ($vCtoCon["{$mIT[$i][1]}"]['umeidxxx'] != '') ? $vCtoCon["{$mIT[$i][1]}"]['umeidxxx'] : "A9"; //Unidad de medida

  }

  //Incluyendo GMF y Formularios
  for ($i=0;$i<count($mValores);$i++) {
    $nInd_mIngTer = count($mIngTer);
    $mIngTer[$nInd_mIngTer]['ctoidxxx'] = $mValores[$i]['ctoidxxx'];//Codigo concepto
    $mIngTer[$nInd_mIngTer]['ctodesxx'] = $mValores[$i]['comobsxx'];//Descripcion
    $mIngTer[$nInd_mIngTer]['baseivax'] = 0;//base
    $mIngTer[$nInd_mIngTer]['ivaxxxxx'] = 0;//Iva
    $mIngTer[$nInd_mIngTer]['totalxxx'] = $mValores[$i]['comvlrxx'];//Total
    $mIngTer[$nInd_mIngTer]['facturax'] = "";//Factura
    $mIngTer[$nInd_mIngTer]['umeidxxx'] = ($vCtoCon["{$mValores[$i]['ctoidxxx']}"]['umeidxxx'] != '') ? $vCtoCon["{$mValores[$i]['ctoidxxx']}"]['umeidxxx'] : "A9"; //Unidad de medida
  }

  for ($i=0;$i<count($mDatGmf);$i++) {
    $nInd_mIngTer = count($mIngTer);
    $mIngTer[$nInd_mIngTer]['ctoidxxx'] = $mDatGmf[$i]['ctoidxxx'];//Codigo concepto
    $mIngTer[$nInd_mIngTer]['ctodesxx'] = $mDatGmf[$i]['comobsxx'];//Descripcion
    $mIngTer[$nInd_mIngTer]['baseivax'] = 0;//base
    $mIngTer[$nInd_mIngTer]['ivaxxxxx'] = 0;//Iva
    $mIngTer[$nInd_mIngTer]['totalxxx'] = $mDatGmf[$i]['comvlrxx'];//Total
    $mIngTer[$nInd_mIngTer]['facturax'] = "";//Factura
    $mIngTer[$nInd_mIngTer]['umeidxxx'] = ($vCtoCon["{$mDatGmf[$i]['ctoidxxx']}"]['umeidxxx'] != '') ? $vCtoCon["{$mDatGmf[$i]['ctoidxxx']}"]['umeidxxx'] : "A9"; //Unidad de medida
  }

  // Traigo la Forma de Pago
  $vCodFormPago = explode("~", $vCocDat['comobs2x']);
  $cFormaPag = "";
  if ($vCodFormPago[14] != "") {
    //Buscando descripcion
    $cFormaPag = ($vCodFormPago[14] == 1) ? "CONTADO" : "CREDITO";
  }
  // FIN Traigo la Forma de Pago

  // Traigo el Medio de Pago
  $cMedioPago = "";
  if ($vCodFormPago[15] != "") {
    $qMedPag  = "SELECT mpadesxx ";
    $qMedPag .= "FROM $cAlfa.fpar0155 ";
    $qMedPag .= "WHERE mpaidxxx = \"{$vCodFormPago[15]}\" AND ";
    $qMedPag .= "regestxx = \"ACTIVO\" LIMIT 0,1";
    $xMedPag  = f_MySql("SELECT","",$qMedPag,$xConexion01,"");
    if (mysql_num_rows($xMedPag) > 0) {
      $vMedPag = mysql_fetch_array($xMedPag);
      $cMedioPago = $vMedPag['mpadesxx'];
    }
  }
  // FIN Traigo el Medio de Pago

  if($nSwitch == 0){
    class PDF extends FPDF {

      // Función para dibujar un rectángulo redondeado
      function RoundedRect($x, $y, $w, $h, $r, $style = '')
      {
          $k = $this->k;
          $hp = $this->h;
          if ($style == 'F') {
              $op = 'f';
          } elseif ($style == 'FD' || $style == 'DF') {
              $op = 'B';
          } else {
              $op = 'S';
          }
          $MyArc = 4 / 3 * (sqrt(2) - 1);
          $this->_out(sprintf('%.2F %.2F m', ($x + $r) * $k, ($hp - $y) * $k));
          $xc = $x + $w - $r;
          $yc = $y + $r;
          $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - $y) * $k));
          if ($r > 0) {
              $this->_Arc($xc + $r * $MyArc, $yc - $r, $xc + $r, $yc - $r * $MyArc, $xc + $r, $yc);
          }
          $xc = $x + $w - $r;
          $yc = $y + $h - $r;
          $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $yc) * $k));
          if ($r > 0) {
              $this->_Arc($xc + $r, $yc + $r * $MyArc, $xc + $r * $MyArc, $yc + $r, $xc, $yc + $r);
          }
          $xc = $x + $r;
          $yc = $y + $h - $r;
          $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - ($y + $h)) * $k));
          if ($r > 0) {
              $this->_Arc($xc - $r * $MyArc, $yc + $r, $xc - $r, $yc + $r * $MyArc, $xc - $r, $yc);
          }
          $xc = $x + $r;
          $yc = $y + $r;
          $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - $yc) * $k));
          if ($r > 0) {
              $this->_Arc($xc - $r, $yc - $r * $MyArc, $xc - $r * $MyArc, $yc - $r, $xc, $yc - $r);
          }
          $this->_out($op);
      }

      function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
      {
          $h = $this->h;
          $this->_out(sprintf(
              '%.2F %.2F %.2F %.2F %.2F %.2F c ',
              $x1 * $this->k,
              ($h - $y1) * $this->k,
              $x2 * $this->k,
              ($h - $y2) * $this->k,
              $x3 * $this->k,
              ($h - $y3) * $this->k
          ));
      }
      function Header() {
        global $cAlfa;    global $cRoot;   global $vSysStr; global $_COOKIE;   global $cPlesk_Skin_Directory;
        global $vAgenDat; global $vResDat; global $vCocDat; global $cFormaPag; global $cMedioPago;
        global $cPedido;  global $cDocId;  global $cDocTra; global $cBultos;   global $cPesBru;
        global $cTasCam;  global $vComObs2;

        if ($vCocDat['regestxx'] == "INACTIVO") {
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/facturaanulada.jpg',10,50,180,180);
        }
  
        if ($_COOKIE['kModo'] == "VERFACTURA"){
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/copianovalida.jpg',10,50,180,180);
        }

        $posx = 8;
        $posy = 10;

        // logo
        $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logohaydear.jpeg', $posx + 8, $posy - 8, 60, 25);

        $this->SetFont('Arial', '', 6);
        $this->TextWithDirection(210,60,utf8_decode("NOMBRE DEL FABRICANTE DEL SOFTWARE (PROVEEDOR TECNOLÓGICO): OPENTECNOLOGIA SA NIT: 830135010 NOMBRE DEL SOFTWARE: OPEN-V2"),'D');  
        $cResolucion  = "AUTORIZACIÓN FACTURACIÓN ELECTRÓNICA DIAN No. ".$vResDat['residxxx']." DE ".$this->fechaCastellano($vResDat['resfdexx'])." AL ".$this->fechaCastellano($vResDat['resfhaxx'])." AUTORIZA DESDE ".$vResDat['resprexx']." ".$vResDat['resdesxx']." HASTA ".$vResDat['resprexx']." ".$vResDat['reshasxx'];
        $cResponsable = "RESPONSABLE DE IVA, RETENEDOR EN OPERACIONES A NO RESPONSABLE DE IVA, TARIFA ICA 9.66 X 1000";
        $this->TextWithDirection(3.5,240,utf8_decode($cResolucion),'U');
        $this->TextWithDirection(5.5,200,utf8_decode($cResponsable),'U');

        // Informacion Agencia
        $this->setXY($posx+65, $posy+8);
        $this->SetTextColor(50, 143, 206);
        $this->SetFont('Arial', 'B', 11);
        $this->MultiCell(70, 4, utf8_decode($vAgenDat['CLINOMXX']), 0, 'C');
        $this->SetTextColor(0);
        $this->Ln(1);
        $this->setX($posx+5);
        $this->SetFont('Arial', '', 7);
        $cNitAduana  = number_format($vSysStr['financiero_nit_agencia_aduanas'], 0, '', '.')."-";
        $cNitAduana .= f_Digito_Verificacion($vSysStr['financiero_nit_agencia_aduanas']);
        $this->Cell(70, 4, utf8_decode("NIT. " . $cNitAduana), 0, 0, 'C');
        $this->Ln(4);
        $this->setX($posx+5);
        $this->Cell(70, 4, utf8_decode("COD 0309"), 0, 0, 'C');
        $this->SetTextColor(0);

        $this->setXY($posx-3,$posy+22);
        $this->SetFont('Arial', 'I', 8);
        $this->Ln(2);

        // Informacion factura
        $this->SetFont('Arial', 'B', 9);
        $this->setXY($posx + 130, $posy);
        $this->MultiCell(70, 4, utf8_decode("FACTURA ELECTRÓNICA DE VENTA No."), 0, 'R');
        $this->setX($posx + 100);
        $this->SetTextColor(238, 47, 47);
        $this->MultiCell(70, 4, $vResDat['resprexx']." ".$vCocDat['comcscxx'], 0, 'R');
        $this->SetTextColor(0);

        // Fechas del documento e informacion de pago
        $posy = $this->GetY();
        $this->setXY($posx, $posy+18);
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(40, 3.5, utf8_decode("Fecha y Hora de Generación:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(60, 3.5, $vCocDat['comfecxx'] ." ". $vCocDat['reghcrex'], 0, 0, 'L');
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(25, 3.5, "FORMA DE PAGO:", 0, 0, 'L');
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(78, 3.5, utf8_decode($cFormaPag), 0, 0, 'L');
        $this->Ln(4);
        $this->setX($posx);
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(30, 3.5, "FECHA VENCIMIENTO:", 0, 0, 'L');
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(70, 3.5, $vCocDat['comfecve'], 0, 0, 'L');
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(22, 3.5, "Medio de Pago:", 0, 0, 'L');
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(78, 3.5, $cMedioPago, 0, 0, 'L');
        $this->Ln(4);
        $posy = $this->GetY();

        // Informacion facturar a
        $this->setXY($posx, $posy);
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(23, 4, utf8_decode("FACTURADO A:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7.5);
        $this->MultiCell(83, 4, utf8_decode($vCocDat['CLINOMXX']), 0, 'J');
        $this->setX($posx);
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(15, 4, utf8_decode("NIT:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7.5);
        $this->MultiCell(83, 4, number_format($vCocDat['CLIIDXXX'], 0, '', '.'). "-" .f_Digito_Verificacion($vCocDat['CLIIDXXX']), 0, 'L');
        $this->setX($posx);
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(20, 4, utf8_decode("DIRECCION:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7.5);
        $this->MultiCell(83, 4, utf8_decode($vCocDat['CLIDIRXX']), 0, 'L');
        $this->setX($posx);
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(15, 4, utf8_decode("Factura:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7.5);
        $this->MultiCell(83, 4, utf8_decode($vComObs2[18]), 0, 'L');
        $this->setX($posx);
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(15, 4, utf8_decode("Pedidos:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7.5);
        $this->MultiCell(83, 4, utf8_decode($cPedido), 0, 'L');
        $this->setX($posx);
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(15, 4, utf8_decode("Protocolo:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7.5);
        $this->MultiCell(83, 4, utf8_decode($vComObs2[19]), 0, 'L');
        $this->setX($posx);
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(23, 4, utf8_decode("Código de Cobro:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7.5);
        $this->MultiCell(75, 4, utf8_decode($vComObs2[20]), 0, 'L');
        $this->setX($posx);
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(15, 4, utf8_decode("Jobs:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7.5);
        $this->MultiCell(83, 4, utf8_decode($vComObs2[21]), 0, 'L');
        $posyy = $this->GetY();

        $this->setXY($posx + 101, $posy);
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(20, 4, utf8_decode("NUMERO DO:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(43, 4, utf8_decode($cDocId), 0, 0, 'L');
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(8, 4, utf8_decode("Guia:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7.5);
        $this->MultiCell(42, 4, utf8_decode($cDocTra), 0, 'L');
        $this->setX($posx + 101);
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(12, 4, utf8_decode("Registro:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7.5);
        $this->MultiCell(85, 4, utf8_decode($vComObs2[22]), 0, 'L');
        $this->setX($posx + 101);
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(17, 4, utf8_decode("Declaración:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7.5);
        $this->MultiCell(80, 4, utf8_decode($vComObs2[23]), 0, 'L');
        $this->setX($posx + 101);
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(10, 4, utf8_decode("Bultos:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(40, 4, number_format($cBultos,2,',','.'), 0, 0, 'L');
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(8, 4, utf8_decode("Kilos:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7.5);
        $this->MultiCell(39, 4, number_format($cPesBru,2,',','.'), 0, 'L');
        $this->setX($posx + 101);
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(15, 4, utf8_decode("Contenido:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7.5);
        $this->MultiCell(80, 4, utf8_decode($vComObs2[24]), 0, 'L');
        $this->setX($posx + 101);
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(23, 4, utf8_decode("Tasa de Cambio:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7.5);
        $this->MultiCell(75, 4, "$ ".$vComObs2[25], 0, 'L');

        $posyy = $this->GetY() > $posyy ? $this->GetY() : $posyy;
        $this->RoundedRect($posx, $posy-10, 200, 45, 5, 'D');
        $posy = $posyy;

        $this->setXY($posx, $posy+5);
        $this->SetFont('Arial', '', 7);
        $this->Cell(10, 5, utf8_decode("ITEM"), 'TBL', 0, 'C');
        $this->Cell(20, 5, utf8_decode("CÓDIGO"), 'TB', 0, 'C');
        $this->Cell(85, 5, utf8_decode("DESCRIPCIÓN"), 'TB', 0, 'L');
        $this->Cell(20, 5, utf8_decode("UND"), 'TB', 0, 'C');
        $this->Cell(15, 5, utf8_decode("CANT"), 'TB', 0, 'C');
        $this->Cell(25, 5, utf8_decode("VR. UNITARIO"), 'TB', 0, 'C');
        $this->Cell(25, 5, utf8_decode("VR. TOTAL"), 'TBR', 0, 'C');
        $this->Ln(5);
      }//Function Header

      function Footer(){
        global $vSysStr; global $vAgenDat; global $vCiuAgen; global $vPaisAgen;

        $posx	= 8;
        $posy = 258;

        $this->Line($posx, $posy, $posx + 200, $posy);
        $this->setXY($posx, $posy);
        $this->SetFont('Arial', '', 8);
        $this->MultiCell(200, 4, utf8_decode($vAgenDat['CLIDIRXX']), 0, 'C');
        $this->setX($posx);
        $this->MultiCell(200, 4, utf8_decode("Teléfonos: ".$vAgenDat['CLITELXX']." ".$vCiuAgen['CIUDESXX'] ." - ". $vPaisAgen['PAIDESXX']), 0, 'C');
        $this->setX($posx);
        $this->MultiCell(200, 4, utf8_decode("E-mail: ".$vAgenDat['CLIEMAXX']), 0, 'C');

        // Paginacion
        $this->setXY($posx,$posy+15);
        $this->SetFont('Arial','B',8);
        $this->Cell(200,4,utf8_decode('Página: ').$this->PageNo().' de {nb}',0,0,'C');
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
          // $this->Rect($x,$y,$w,$h);
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

      function TextWithDirection($x, $y, $txt, $direction='U') {
        if ($direction=='R')
            $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',1,0,0,1,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
        elseif ($direction=='L')
            $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',-1,0,0,-1,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
        elseif ($direction=='U')
            $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',0,1,-1,0,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
        elseif ($direction=='D')
            $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',0,-1,1,0,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
        else
            $s=sprintf('BT %.2F %.2F Td (%s) Tj ET',$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
        if ($this->ColorFlag)
            $s='q '.$this->TextColor.' '.$s.' Q';
        $this->_out($s);
      }

      function fechaCastellano($dFecha) {
        $nombreCompletoMeses = [
          1 => 'ENERO',
          2 => 'FEBRERO',
          3 => 'MARZO',
          4 => 'ABRIL',
          5 => 'MAYO',
          6 => 'JUNIO',
          7 => 'JULIO',
          8 => 'AGOSTO',
          9 => 'SEPTIEMBRE',
          10 => 'OCTUBRE',
          11 => 'NOVIEMBRE',
          12 => 'DICIEMBRE'
        ];
    
        $dia = date("d", strtotime($dFecha));
        $anio = date("Y", strtotime($dFecha));
      
        $mes = $nombreCompletoMeses[date("n", strtotime($dFecha))];
        return $dia . " DE ". $mes . " DE " . $anio;
      }
    }

    $pdf = new PDF('P','mm','Letter');
    $pdf->AddFont('verdana','','verdana.php');
    $pdf->AddFont('verdanab','','verdanab.php');
    $pdf->SetFont('verdana','',8);
    $pdf->AliasNbPages();
    $pdf->SetMargins(0,0,0);
    $pdf->SetAutoPageBreak(0,0);

    $pdf->AddPage();
    $posy	   = $pdf->GetY()+3; 
    $posx	   = 8;
    $posfin  = 240;
    $nCount  = 0; 
    $pyy     = $posy;

    $nTotalPCC     = 0;
    $nTotalIPGra   = 0;
    $nTotalIPNoGra = 0;

    //$mDatIP = array_merge($mDatIP, $mDatIP, $mDatIP, $mDatIP, $mDatIP, $mDatIP);
    //$mDatIP = array_merge($mDatIP, $mDatIP, $mDatIP, $mDatIP, $mDatIP, $mDatIP);
    //$mDatIP = array_merge($mDatIP, $mDatIP, $mDatIP, $mDatIP, $mDatIP, $mDatIP);

    // Imprimo Pagos a Terceros
    if (count($mIngTer) > 0 || $nBandPcc == 1) {//Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
      $pdf->SetFont('arial','B',8);
      $pdf->setXY($posx+10,$posy);
      $pdf->Cell(140,5,"DETALLE PAGOS A TERCEROS",0,0,'L');
      $pyy = $posy+6;

      //Se imprimen los Ingresos por Terceros
      $pdf->SetWidths(array(10, 20, 85, 20, 15, 25, 25));
      $pdf->SetAligns(array("C", "C", "L", "C", "C", "R", "R"));
      $pdf->setXY($posx,$pyy);

      for($i=0;$i<count($mIngTer);$i++){
        $nCount++;
        $pyy = $pdf->GetY();
        if($pyy > $posfin){
          $pdf->Rect($posx, $posy-3, 200, $pdf->getY()-($posy-3));
          $pdf->AddPage();
          $pyy = $posy+5;
          $pdf->setXY($posx,$pyy);
        }

        //Consulto la descripcion de la Unidad de medida
        $cUniMedi  = '';
        $qUniMedi  = "SELECT umedesxx ";
        $qUniMedi .= "FROM $cAlfa.fpar0157 ";
        $qUniMedi .= "WHERE ";
        $qUniMedi .= "umeidxxx = \"{$mIngTer[$i]['umeidxxx']}\" LIMIT 0,1";
        $xUniMedi  = mysql_query($qUniMedi, $xConexion01);
        if (mysql_num_rows($xUniMedi) > 0) {
          $vUniMedi = mysql_fetch_array($xUniMedi);
          $cUniMedi = $vUniMedi['umedesxx'];
        }

        $nTotalPCC += $mIngTer[$i]['totalxxx'];
        $nVlrBase = ($mIngTer[$i]['baseivax'] > 0) ? $mIngTer[$i]['baseivax'] : $mIngTer[$i]['totalxxx'];
        $cDescrip = ($mIngTer[$i]['facturax'] != "") ? " Base: " . number_format($nVlrBase,2,'.',',') . " Iva: " . number_format($mIngTer[$i]['ivaxxxxx'],2,'.',',') . " FC. " . $mIngTer[$i]['facturax'] : "";

        $pdf->SetFont('arial','',7);
        $pdf->setX($posx);
        $pdf->Row(array(
          $nCount,
          $mIngTer[$i]['ctoidxxx'],
          $mIngTer[$i]['ctodesxx'] . " " . $cDescrip,
          utf8_decode($cUniMedi),
          "1",
          number_format($mIngTer[$i]['totalxxx'],2,'.',','),
          number_format($mIngTer[$i]['totalxxx'],2,'.',',')
        ));
      }//for($i=0;$i<count($mIngTer);$i++){

      if($pyy > $posfin){
        $pdf->Rect($posx, $posy-3, 200, $pdf->getY()-($posy-3));
        $pdf->AddPage();
        $pyy = $posy+5;
        $pdf->setXY($posx,$pyy);
      }
      $pyy += 10;
    }//if(count($mIngTer) > 0 || $nBandPcc == 1){//Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
    // Fin Imprimo Pagos a Terceros

    // Imprimo Ingresos Propios
    if($nBandIP == 1){//Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS
      $pdf->setXY($posx+10,$pyy);
      $pdf->SetFont('arial','B',8);
      $pdf->Cell(67,6,utf8_decode("DETALLE INGRESOS PROPIOS"),0,0,'L');
      $pyy += 6;

      $pdf->SetWidths(array(10, 20, 85, 20, 15, 25, 25));
      $pdf->SetAligns(array("C", "C", "L", "C", "C", "R", "R"));
      $pdf->setXY($posx,$pyy);

      // hago dos veces el mismo for para separar los ingresos GRAVADOS y luego los NO GRAVADOS
      for($k=0;$k<(count($mDatIP));$k++) {
        $pyy = $pdf->GetY();
        if($pyy > $posfin){
          $pdf->Rect($posx, $posy-3, 200, $pdf->getY()-($posy-3));
          $pdf->AddPage();
          $pyy = $posy+5;
          $pdf->setXY($posx,$pyy);
        }

        if($mDatIP[$k]['comctocx'] == "IP" && $mDatIP[$k]['comvlr01'] != 0) {
          $nCount++;
          // Consulto la descripcion de la Unidad de medida
          $cUniMedi  = '';
          $qUniMedi  = "SELECT umedesxx ";
          $qUniMedi .= "FROM $cAlfa.fpar0157 ";
          $qUniMedi .= "WHERE ";
          $qUniMedi .= "umeidxxx = \"{$mDatIP[$k]['unidadfe']}\" LIMIT 0,1";
          $xUniMedi  = mysql_query($qUniMedi, $xConexion01);
          if (mysql_num_rows($xUniMedi) > 0) {
            $vUniMedi = mysql_fetch_array($xUniMedi);
            $cUniMedi = $vUniMedi['umedesxx'];
          }

          $nTotalIPGra += $mDatIP[$k]['comvlrxx'];
          $nVlrUnit = ($mDatIP[$k]['unidadfe'] != "A9" && $mDatIP[$k]['canfexxx'] > 0) ? ($mDatIP[$k]['comvlrxx']/$mDatIP[$k]['canfexxx']) : $mDatIP[$k]['comvlrxx'];

          $pdf->SetFont('arial','',7);
          $pdf->setX($posx);
          $pdf->Row(array(
            $nCount,
            $mDatIP[$k]['ctoidxxx'],
            $mDatIP[$k]['comobsxx'],
            utf8_decode($cUniMedi),
            $mDatIP[$k]['canfexxx'],
            number_format($nVlrUnit,2,'.',','),
            number_format($mDatIP[$k]['comvlrxx'],2,'.',',')
          ));
        }//if($mDatIP[$k]['comctocx'] == 'IP'){
      }// for($k=$nPosIP;$k<(count($mDatIP));$k++) {

      for($k=0;$k<(count($mDatIP));$k++) {
        $pyy = $pdf->GetY();
        if($pyy > $posfin){
          $pdf->Rect($posx, $posy-3, 200, $pdf->getY()-($posy-3));
          $pdf->AddPage();
          $pyy = $posy+5;
          $pdf->setXY($posx,$pyy);
        }

        if($mDatIP[$k]['comctocx'] == "IP" && $mDatIP[$k]['comvlr01'] == 0) {
          $nCount++;
          //Consulto la descripcion de la Unidad de medida
          $cUniMedi  = '';
          $qUniMedi  = "SELECT umedesxx ";
          $qUniMedi .= "FROM $cAlfa.fpar0157 ";
          $qUniMedi .= "WHERE ";
          $qUniMedi .= "umeidxxx = \"{$mDatIP[$k]['unidadfe']}\" LIMIT 0,1";
          $xUniMedi  = mysql_query($qUniMedi, $xConexion01);
          if (mysql_num_rows($xUniMedi) > 0) {
            $vUniMedi = mysql_fetch_array($xUniMedi);
            $cUniMedi = $vUniMedi['umedesxx'];
          }

          $nTotalIPNoGra += $mDatIP[$k]['comvlrxx'];
          $nVlrUnit = ($mDatIP[$k]['unidadfe'] != "A9" && $mDatIP[$k]['canfexxx'] > 0) ? ($mDatIP[$k]['comvlrxx']/$mDatIP[$k]['canfexxx']) : $mDatIP[$k]['comvlrxx'];

          $pdf->SetFont('arial','',7);
          $pdf->setX($posx);
          $pdf->Row(array(
            $nCount,
            $mDatIP[$k]['ctoidxxx'],
            $mDatIP[$k]['comobsxx'],
            utf8_decode($cUniMedi),
            $mDatIP[$k]['canfexxx'],
            number_format($nVlrUnit,2,'.',','),
            number_format($mDatIP[$k]['comvlrxx'],2,'.',',')
          ));
        }//if($mDatIP[$k]['comctocx'] == 'IP'){
      }// for($k=$nPosIP;$k<(count($mDatIP));$k++) {

      if($pyy > $posfin){
        $pdf->Rect($posx, $posy-3, 200, $pdf->getY()-($posy-3));
        $pdf->AddPage();
        $pyy = $posy+5;
        $pdf->setXY($posx,$pyy);
      }
      $pyy += 5;
    }//if($nBandIP == 1){//Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS
    // Fin Imprimo Ingresos Propios

    // Se calculan los valores de los totales
    $nIva     = 0;
    $nTotRfte = 0;
    $nPorRfte = 0;
    $nTotRiva = 0;
    $nPorRiva = 0;
    $nTotRica = 0;
    $nPorRica = 0;

    for ($k=0;$k<count($mCodDat);$k++) {
      // Busco valor de IVA
      if($mCodDat[$k]['comctocx'] == 'IVAIP'){
        $nIva += $mCodDat[$k]['comvlrxx'];
      }
      // Fin Busco Valor de IVA

      // Busco Valor de RET.FUENTE
      if($mCodDat[$k]['comctocx'] == 'RETFTE'){
        $qPucDat  = "SELECT $cAlfa.fpar0115.pucretxx ";
        $qPucDat .= "FROM $cAlfa.fpar0115 ";
        $qPucDat .= "WHERE ";
        $qPucDat .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = \"{$mCodDat[$k]['pucidxxx']}\" LIMIT 0,1 ";
        $xPucDat  = f_MySql("SELECT","",$qPucDat,$xConexion01,"");
        $nFilPuc  = mysql_num_rows($xPucDat);
        if(mysql_num_rows($xPucDat) > 0){
          $vPucDat  = mysql_fetch_array($xPucDat);
          $nPorRfte = $vPucDat['pucretxx'];
        }
        $nTotRfte += $mCodDat[$k]['comvlrxx'];
      }//if($mCodDat[$k]['comctocx'] == 'RETFTE'){        
      // Fin Busco Valor de RET.FUENTE

      // Busco Valor de RET.IVA
      if($mCodDat[$k]['comctocx'] == 'RETIVA'){
        $qPucDat  = "SELECT $cAlfa.fpar0115.pucretxx ";
        $qPucDat .= "FROM $cAlfa.fpar0115 ";
        $qPucDat .= "WHERE ";
        $qPucDat .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = \"{$mCodDat[$k]['pucidxxx']}\" LIMIT 0,1 ";
        $xPucDat  = f_MySql("SELECT","",$qPucDat,$xConexion01,"");
        $nFilPuc  = mysql_num_rows($xPucDat);
        if(mysql_num_rows($xPucDat) > 0){
          $vPucDat  = mysql_fetch_array($xPucDat);
          $nPorRiva = $vPucDat['pucretxx'];
        }
        $nTotRiva += $mCodDat[$k]['comvlrxx'];
      }
      // Fin Busco Valor de RET.IVA

      // Busco Valor de RET.ICA
      if($mCodDat[$k]['comctocx'] == 'RETICA'){
        $qPucDat  = "SELECT $cAlfa.fpar0115.pucretxx ";
        $qPucDat .= "FROM $cAlfa.fpar0115 ";
        $qPucDat .= "WHERE ";
        $qPucDat .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = \"{$mCodDat[$k]['pucidxxx']}\" LIMIT 0,1 ";
        $xPucDat  = f_MySql("SELECT","",$qPucDat,$xConexion01,"");
        $nFilPuc  = mysql_num_rows($xPucDat);
        if(mysql_num_rows($xPucDat) > 0){
          $vPucDat  = mysql_fetch_array($xPucDat);
          $nPorRica = $vPucDat['pucretxx'];
        }
        $nTotRica += $mCodDat[$k]['comvlrxx'];
      }
      // Fin Busco Valor de RET.ICA
    }

    $cNegativo = "";
    $nTotAnt   = 0;
    $nTotPag   = 0;
    $nSubTotal = $nTotalPCC + $nIva + $nTotalIPGra + $nTotalIPNoGra;

    for ($k=0;$k<count($mCodDat);$k++) {
      if($mCodDat[$k]['comctocx'] == 'CD' && strpos($mCodDat[$k]['comobsxx'],'ANTICIPOS') > 0){
        $nTotAnt += $mCodDat[$k]['comvlrxx'];
      }

      if($mCodDat[$k]['comctocx'] == 'SS' || $mCodDat[$k]['comctocx'] == 'SC'){
        if($mCodDat[$k]['comctocx'] == 'SC'){
          $cNegativo = "-";
        }
        $nTotPag = $nSubTotal - ($nTotRfte + $nTotRica + $nTotRiva);
      }
    }

    $nSaldoFavor = 0;
    if($cNegativo == "-") {
      $nSaldoFavor = abs($nTotPag - $nTotAnt);
    } else {
      $nTotPag = abs($nTotPag - $nTotAnt);
    }
    
    // Imprimo los valores de los Totales
    if($pyy > 195){
      $pdf->Rect($posx, $posy-3, 200, $pdf->getY()-($posy-3));
      $pdf->AddPage();
      $pyy = $posy;
    }

    // Recuadro de los Item
    $pdf->Rect($posx, $posy-3, 200, 195-($posy-3));

    $posx	= 8;
    $posy = 200;
    $pdf->setXY($posx, $posy + 1);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->MultiCell(20,4,"Total Item: ".$nCount,0,'L');
    $pdf->setX($posx);
    $pdf->SetFont('Arial', '', 7);
    $pdf->MultiCell(140, 4, 'OBSERVACIONES: ' . utf8_decode($vCocDat['comobsxx']), 0, 'L');
  
    $pdf->setXY($posx + 140, $posy+1);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(35, 4, "Total Ingresos para Terceros", 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(25, 4, number_format($nTotalPCC, 2, '.', ','), 0, 0, 'R');
    $pdf->ln(4);
    $pdf->setX($posx + 140);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(35, 4, "Total Ingresos Propios", 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(25, 4, number_format(($nTotalIPGra + $nTotalIPNoGra), 2, '.', ','), 0, 0, 'R');
    $pdf->ln(4);
    $pdf->setX($posx + 140);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(35, 4, "IVA", 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(25, 4, number_format($nIva, 2, '.', ','), 0, 0, 'R');
    $pdf->ln(4);
    $pdf->setX($posx + 140);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(35, 4, "Total Factura", 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(25, 4, number_format($nSubTotal, 2, '.', ','), 0, 0, 'R');
    $pdf->ln(4);
    $pdf->setX($posx + 140);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(35, 4, "Retefuente" , 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(25, 4, number_format($nTotRfte, 2, '.', ','), 0, 0, 'R');
    $pdf->ln(4);
    $pdf->setX($posx + 140);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(35, 4, "Reteiva", 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(25, 4, number_format($nTotRiva, 2, '.', ','), 0, 0, 'R');
    $pdf->ln(4);
    $pdf->setX($posx + 140);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(35, 4, "Reteica", 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(25, 4, number_format($nTotRica, 2, '.', ','), 0, 0, 'R');
    $pdf->ln(4);
    $pdf->setX($posx + 140);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(35, 4, "Anticipo", 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(25, 4, number_format($nTotAnt, 2, '.', ','), 0, 0, 'R');
    $pdf->ln(4);
    $pdf->setX($posx + 140);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(35, 4, "Saldo a su Favor", 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(25, 4, number_format($nSaldoFavor > 0 ? $nSaldoFavor : 0, 2, '.', ','), 0, 0, 'R');
    $pdf->ln(4);
    $pdf->setX($posx + 140);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(35, 5, "Total a Pagar", "T", 0, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(25, 5, number_format($nSaldoFavor > 0 ? 0 : $nTotPag, 2, '.', ','), "T", 0, 'R');

    $pdf->Rect($posx, $posy, 200, 42);
    $pdf->Line($posx + 140, $posy, $posx + 140, $posy + 42);
    $pdf->Line($posx + 176, $posy, $posx + 176, $posy + 42);
    
    // Valor en letras
    $posy += 42;
    $nTotPag   = $nSaldoFavor > 0 ? $nSaldoFavor : $nTotPag;
    $cVlrLetra = f_Cifra_Php(str_replace("-","",abs($nTotPag)),"PESO");
    $pdf->setXY($posx, $posy+1);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(20,5,"Valor en Pesos:",0,0,'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->MultiCell(133, 5, utf8_decode($cVlrLetra), 0, 'L');
    $pdf->Rect($posx, $posy+1, 200, 5);

    $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
    $pdf->Output($cFile);

    if (file_exists($cFile)){
      chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
    } else {
      f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
    }

    echo "<html><script>document.location='$cFile';</script></html>";
  }
?>

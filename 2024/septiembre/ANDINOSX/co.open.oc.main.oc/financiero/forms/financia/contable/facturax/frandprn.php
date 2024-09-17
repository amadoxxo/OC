<?php
  /**
   * Imprime Factura de Venta Andinos.
   * --- Descripcion: Permite Imprimir Factura de Venta.
   * @author Shamaru Primera <shamaru001@gmail.com>
   */
  include("../../../../libs/php/utility.php");
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
      $nFilCoc  = mysql_num_rows($xCocDat);
      if ($nFilCoc > 0) {
        $vCocDat  = mysql_fetch_array($xCocDat);
        if($vCocDat['comprnxx']=="IMPRESO" && $vCocDat['regestxx'] != "INACTIVO" && $_COOKIE['kModo'] != "VERFACTURA"){
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
  /*if($permisos==1){
    $nSwitch=1;
    f_Mensaje(__FILE__,__LINE__,"Las Siguientes Facturas No tienen Permiso de Impresion [$zCadPer], Verifique.");?>
    <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
      <script languaje = "javascript">
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
        document.forms['frgrm'].submit();
      </script>
    <?php
  }*/
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

    if($vCocDat['regestxx'] != "INACTIVO" && $_COOKIE['kModo'] != "VERFACTURA") {
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
    $qCocDat .= "IF($cAlfa.fpar0008.sucidxxx <> \"\",$cAlfa.fpar0008.sucidxxx,\"SUCURSAL SIN ID\") AS sucidxxx, ";
    $qCocDat .= "IF($cAlfa.fpar0008.sucdesxx <> \"\",$cAlfa.fpar0008.sucdesxx,\"SUCURSAL SIN DESCRIPCION\") AS sucdesxx, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,\"CLIENTE SIN NOMBRE\") AS CLINOMXX, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.CLIDIRXX <> \"\",$cAlfa.SIAI0150.CLIDIRXX,\"SIN DIRECCION\") AS CLIDIRXX, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.CLITELXX <> \"\",$cAlfa.SIAI0150.CLITELXX,\"SIN TELEFONO\") AS CLITELXX, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.CLICONTX <> \"\",$cAlfa.SIAI0150.CLICONTX,\"SIN RESPONSABLE\") AS CLICONTX, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.PAIIDXXX <> \"\",$cAlfa.SIAI0150.PAIIDXXX,\"\") AS PAIIDXXX, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.DEPIDXXX <> \"\",$cAlfa.SIAI0150.DEPIDXXX,\"\") AS DEPIDXXX, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.CIUIDXXX <> \"\",$cAlfa.SIAI0150.CIUIDXXX,\"\") AS CIUIDXXX, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.CLINRPXX <> \"\",$cAlfa.SIAI0150.CLINRPXX,\"\") AS CLINRPXX, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.CLIPLAXX <> \"\",$cAlfa.SIAI0150.CLIPLAXX,\"\") AS CLIPLAXX ";
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
    $nFilCoc  = mysql_num_rows($xCocDat);
    if ($nFilCoc > 0) {
      $vCocDat  = mysql_fetch_array($xCocDat);
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
    $nFilCod  = mysql_num_rows($xCodDat);
    if ($nFilCod > 0) {
      // Cargo la Matriz con los ROWS del Cursor
      $iA=0;
      while ($xRCD = mysql_fetch_array($xCodDat)) {

        if ($xRCD['comctocx'] == "PCC") {
          //donde el campo pucidxxx like '4%' y el campo cmoctocx = 'PCC'
          $nInd_mValores = count($mValores);
          $mValores[$nInd_mValores]['comobsxx'] = $xRCD['comobsxx'];
          $mValores[$nInd_mValores]['comvlrxx'] = $xRCD['comvlrxx'];
          $mValores[$nInd_mValores]['puctipej'] = $xRCD['puctipej'];
          $mValores[$nInd_mValores]['ctoidxxx'] = $xRCD['ctoidxxx'];
          $mValores[$nInd_mValores]['comvlr01'] = $xRCD['comvlr01'];

        } else {

          $nSwitch_Encontre_Concepto = 0;

          #Armando Observacion
          $cObs    = "";
          $cCif    = "";
          $nCif    = 0;
          $cAplCif = "NO";

          $cDim    = "";
          $nDim    = 0;
          $cAplDim = "NO";

          $cHor    = "";
          $nHor    = 0;
          $cAplHor = "NO";

          $cPie    = "";
          $nPie    = 0;
          $cAplPie = "NO";

          $cDav    = "";
          $nDav    = 0;
          $cAplDav = "NO";

          $cVuce    = "";
          $nVuce    = 0;
          $cAplVuce = "NO";

          $cCertificados    = "";
          $nCertificados    = 0;
          $cAplCertificados = "NO";

          $cDex    = "";
          $nDex    = 0;
          $cAplDex = "NO";

          $cSerial    = "";
          $nSerial    = 0;
          $cAplSerial = "NO";

          $cArancelaria    = "";
          $nArancelaria    = 0;
          $cAplArancelaria = "NO";

          $cDta      = "";
          $nDta      = 0;
          $cAplDta   = "NO";

          $cItems    = "";
          $nItems    = 0;
          $cAplItems = "NO";

          $cCan      = "";
          $nCan      = 0;
          $cAplCan   = "NO";

          $cFob      = "";
          $nFob      = 0;
          $cAplFob   = "NO";

          $cCon20    = "";
          $nCon20    = 0;
          $cAplCon20 = "NO";

          $cCon40    = "";
          $nCon40    = 0;
          $cAplCon40 = "NO";

          $cCarSue   = "";
          $nCarSue   = 0;
          $cAplCarSue= "NO";

          $cFobAgen = "NO";    // Valor Fob de Agenciamiento para Expo
          $xRDD     = array(); //Inicializando el cursor de Valor Fob

          $mComObs_IP = f_Explode_Array($xRCD['comobsxx'],"|","~");
          if(count($mComObs_IP) > 0){
            for($nC=0;$nC<count($mComObs_IP);$nC++){
              switch ($mComObs_IP[$nC][0]) {
                case "109":
                  if($mComObs_IP[$nC][2] != ""){
                    $nComObs_IP = stripos($mComObs_IP[$nC][2], "[");
                    if($nComObs_IP > 0){
                      $mAuxArancelaria = explode("CLASIFICACIONES ARANCELARIAS:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
                      $cArancelaria = "";
                      if(count($mAuxArancelaria) > 1) {
                        $cArancelaria = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxArancelaria[1]);
                        $nArancelaria = $cArancelaria;
                        $cAplArancelaria = "SI";
                      }
                      $cObs = substr($mComObs_IP[$nC][2],0,$nComObs_IP)/*.$mAuxPie[0]*/;
                    }else{
                      $cObs = $mComObs_IP[$nC][2];
                    }
                  }//if($mComObs_IP[$i][2] != ""){
                break;
                case "111":
                  if($mComObs_IP[$nC][2] != ""){
                    $nComObs_IP = stripos($mComObs_IP[$nC][2], "[");
                    if($nComObs_IP > 0){
                      $mAuxPie = explode("PIEZAS:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
                      $cPie = "";
                      if(count($mAuxPie) > 1) {
                        $cPie    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxPie[1]);
                        $nPie    = $cPie;
                        $cAplPie = "SI";
                      }
                      $cObs = substr($mComObs_IP[$nC][2],0,$nComObs_IP)/*.$mAuxPie[0]*/;
                    }else{
                      $cObs = $mComObs_IP[$nC][2];
                    }
                  }//if($mComObs_IP[$i][2] != ""){
                break;
                case "101":
                case "103":
                case "119":
                case "201":
                case "309":
                  if($mComObs_IP[$nC][2] != ""){
                    $nComObs_IP = stripos($mComObs_IP[$nC][2], "[");
                    if($nComObs_IP > 0){
                      $mAuxHor    = explode("HORAS:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
                      $mAuxSerial = explode("CANT SERIALES:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
                      $mAuxItems  = explode("ITEMS:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
                      $mAuxCan    = explode("CANTIDAD:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));

                      $cHor = "";
                      $cSerial = "";
                      $cItems = "";
                      if(count($mAuxHor) > 1) {
                        $cHor    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxHor[1]);
                        $nHor    = $cHor;
                        $cAplHor = "SI";
                      }

                      if(count($mAuxSerial) > 1) {
                        $cSerial    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxSerial[1]);
                        $nSerial    = $cSerial;
                        $cAplSerial = "SI";
                      }

                      if(count($mAuxItems) > 1) {
                        $cItems    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxItems[1]);
                        $nItems    = $cItems;
                        $cAplItems = "SI";
                      }

                      if(count($mAuxCan) > 1) {
                        $cCan    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxCan[1]);
                        $nCan    = $cCan;
                        $cAplCan = "SI";
                      }
                      $cObs = substr($mComObs_IP[$nC][2],0,$nComObs_IP)/*.$mAuxHor[0]*/;
                    }else{
                      $cObs = $mComObs_IP[$nC][2];
                    }
                  }//if($mComObs_IP[$i][2] != ""){
                break;
                case "102":
                case "110":
                  if($mComObs_IP[$nC][2] != ""){
                    $nComObs_IP = stripos($mComObs_IP[$nC][2], "[");
                    if($nComObs_IP > 0){
                      $mAuxDim = explode("DIM:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
                      $cDim = "";
                      if(count($mAuxDim) > 1) {
                        $cDim    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxDim[1]);
                        $nDim    = $cDim;
                        $cAplDim = "SI";
                      }
                      $cObs = substr($mComObs_IP[$nC][2],0,$nComObs_IP)/*.$mAuxDim[0]*/;
                    }else{
                      $cObs = $mComObs_IP[$nC][2];
                    }
                  }//if($mComObs_IP[$i][2] != ""){
                break;
                case "103":
                case "148":
                case "156":
                  if($mComObs_IP[$nC][2] != ""){
                    $nComObs_IP = stripos($mComObs_IP[$nC][2], "[");
                    if($nComObs_IP > 0){
                      $mAuxDav = explode("DAV:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
                      $mAuxDavMag = explode("DAV MAGNETICAS:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
                      $cDav = "";
                      if(count($mAuxDav) > 1 || count($mAuxDavMag) > 1) {
                        $cDav    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", (count($mAuxDav) > 1) ? $mAuxDav[1]  : $mAuxDavMag[1] );
                        $nDav    = $cDav;
                        $cAplDav = "SI";
                      }
                      $cObs = substr($mComObs_IP[$nC][2],0,$nComObs_IP)/*.$mAuxDav[0]*/;
                    }else{
                      $cObs = $mComObs_IP[$nC][2];
                    }
                  }//if($mComObs_IP[$i][2] != ""){
                break;
                case "104":
                case "504":
                  if($mComObs_IP[$nC][2] != ""){
                    $nComObs_IP = stripos($mComObs_IP[$nC][2], "[");
                    if($nComObs_IP > 0){
                      $mAuxVuce = explode("VUCE:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
                      $cVuce = "";
                      if(count($mAuxVuce) > 1) {
                        $cVuce    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxVuce[1]);
                        $nVuce    = $cVuce;
                        $cAplVuce = "SI";
                      }
                      $cObs = substr($mComObs_IP[$nC][2],0,$nComObs_IP)/*.$mAuxVuce[0]*/;
                    }else{
                      $cObs = $mComObs_IP[$nC][2];
                    }
                  }//if($mComObs_IP[$i][2] != ""){
                break;
                case "200":
                  if($mComObs_IP[$nC][2] != ""){
                    $nComObs_IP = stripos($mComObs_IP[$nC][2], "[");
                    if($nComObs_IP > 0){
                      $mAuxFob    = explode("FOB:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
                      $mAuxFob[0] = str_replace(array(",","$","]","MONEDA:COP","MONEDA:USD"), "", $mAuxFob[0]);
                      $cObs = substr($mComObs_IP[$nC][2],0,$nComObs_IP)/*.$mAuxFob[0]*/;
                    }else{
                      $cObs = $mComObs_IP[$nC][2];
                    }
                  }//if($mComObs_IP[$i][2] != ""){
                break;
                case "203":
                  if($mComObs_IP[$nC][2] != ""){
                    $nComObs_IP = stripos($mComObs_IP[$nC][2], "[");
                    if($nComObs_IP > 0){
                      $mAuxCertificados = explode("ORIGEN:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
                      $cCertificados = "";
                      if(count($mAuxCertificados) > 1) {
                        $cCertificados    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD","MONEDA",":","COP","USD"), "", $mAuxCertificados[1]);
                        $nCertificados    = $cCertificados;
                        $cAplCertificados = "SI";
                      }
                      $cObs = substr($mComObs_IP[$nC][2],0,$nComObs_IP)/*.$mAuxVuce[0]*/;
                    }else{
                      //f_Mensaje(__FILE__,__LINE__,"Entro al else del segundo if case 203");
                      $cObs = $mComObs_IP[$nC][2];
                    }
                  }//if($mComObs_IP[$i][2] != ""){
                break;
                case '201':
                case '204':
                case "202":
                  if($mComObs_IP[$nC][2] != ""){
                    $nComObs_IP = stripos($mComObs_IP[$nC][2], "[");
                    if($nComObs_IP > 0){
                      $mAuxDex = explode("DEX:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
                      $cDex = "";
                      if(count($mAuxDex) > 1) {
                        $cDex    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxDex[1]);
                        $nDex    = $cDex;
                        $cAplDex = "SI";
                      }
                      $cObs = substr($mComObs_IP[$nC][2],0,$nComObs_IP)/*.$mAuxDav[0]*/;
                    }else{
                      $cObs = $mComObs_IP[$nC][2];
                    }
                  }//if($mComObs_IP[$i][2] != ""){
                break;
                case "301":
                case "308":
                  if($mComObs_IP[$nC][2] != ""){
                    $nComObs_IP = stripos($mComObs_IP[$nC][2], "[");
                    if($nComObs_IP > 0){
                      $mAuxDta = explode("DTA:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
                      $cDta = "";
                      if(count($mAuxDta) > 1) {
                        $cDta = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxDta[1]);
                        $nDta = $cDta;
                        $cAplDta = "SI";
                      }
                      $cObs = substr($mComObs_IP[$nC][2],0,$nComObs_IP)/*.$mAuxPie[0]*/;
                    }else{
                      $cObs = $mComObs_IP[$nC][2];
                    }
                  }//if($mComObs_IP[$i][2] != ""){
                break;
                case "305":
                  if($mComObs_IP[$nC][2] != ""){
                    $nComObs_IP = stripos($mComObs_IP[$nC][2], "[");
                    if($nComObs_IP > 0){
                      $mAuxCan = explode("Cantidad:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
                      $cCan = "";
                      if(count($mAuxCan) > 1) {
                        $cCan = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxCan[1]);
                        $nCan = $cCan;
                        $cAplCan = "SI";
                      }
                      $cObs = substr($mComObs_IP[$nC][2],0,$nComObs_IP)/*.$mAuxPie[0]*/;
                    }else{
                      $cObs = $mComObs_IP[$nC][2];
                    }
                  }//if($mComObs_IP[$i][2] != ""){
                break;
                case "300":
                case "307":
                  if($mComObs_IP[$nC][2] != ""){
                    $nComObs_IP = stripos($mComObs_IP[$nC][2], "[");
                    if($nComObs_IP > 0){
                      //Valor FOB - Buscando Posicion TRM en la observacion
                      $nPosTrm   = stripos($mComObs_IP[$nC][2], "TRM");
                      $mAuxFob   = explode("FOB:",substr($mComObs_IP[$nC][2],$nComObs_IP,($nPosTrm-$nComObs_IP)));
                      //Contenedores de 20 - Buscando Posicion Contenedores de 40
                      $nPosCon40 = stripos($mComObs_IP[$nC][2], "CONTENEDORES DE 40:");
                      $nPosCon40 = ($nPosCon40 === false) ? strlen($mComObs_IP[$nC][2]) : $nPosCon40 ;
                      $mAuxCon20 = explode("CONTENEDORES DE 20:",substr($mComObs_IP[$nC][2],$nComObs_IP,($nPosCon40-$nComObs_IP)));
                      //Contenedores de 40
                      $mAuxCon40 = explode("CONTENEDORES DE 40:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
                      //Carga Suelta
                      $mAuxCarSue = explode("UNIDADES DE CARGA SUELTA:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
                      $cFob    = "";
                      $cCon20  = "";
                      $cCon40  = "";
                      $cCarSue = "";
                      if(count($mAuxFob) > 1) {
                        $cFob = str_replace(array(".","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxFob[1]);
                        $nFob = $cFob;
                        $cAplFob = "SI";
                      }
                      if(count($mAuxCon20) > 1) {
                        $cCon20 = str_replace(array("(",")",".","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxCon20[1]);
                        $nCon20 = $cCon20;
                        $cAplCon20 = "SI";
                      }
                      if(count($mAuxCon40) > 1) {
                        $cCon40 = str_replace(array("(",")",".","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxCon40[1]);
                        $nCon40 = $cCon40;
                        $cAplCon40 = "SI";
                      }
                      if(count($mAuxCarSue) > 1) {
                        $cCarSue = str_replace(array("(",")",".","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxCarSue[1]);
                        $nCarSue = $cCarSue;
                        $cAplCarSue = "SI";
                      }
                      $cObs = substr($mComObs_IP[$nC][2],0,$nComObs_IP)/*.$mAuxPie[0]*/;
                    }else{
                      $cObs = $mComObs_IP[$nC][2];
                    }
                  }//if($mComObs_IP[$i][2] != ""){
                break;
                default:
                  if($mComObs_IP[$nC][2] != ""){
                    $nComObs_IP = stripos($mComObs_IP[$nC][2], "[");
                    if($nComObs_IP > 0){
                      $mAuxCif = explode("CIF:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
                      $cCif = "";
                      if(count($mAuxCif) > 1) {
                        $cCif    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxCif[1]);
                        $nCif    = $cCif;
                        $cAplCif = "SI";
                      }
                      $cObs = substr($mComObs_IP[$nC][2],0,$nComObs_IP)/*.$mAuxCif[0]*/;
                    }else{
                      $cObs = $mComObs_IP[$nC][2];
                    }
                  } else {
                    $cObs = $mComObs_IP[$nC][1];
                  }//if($mComObs_IP[$nC][2] != ""){
                break;

              }

              switch ($mComObs_IP[$nC][0]) {
                case "200":
                case "203":
                  $qDocDat  = "SELECT docfobxx,doctrmxx ";
                  $qDocDat .= "FROM $cAlfa.sys00121 ";
                  $qDocDat .= "WHERE ";
                  $qDocDat .= "docidxxx = \"{$xRCD['docidxxx']}\" AND ";
                  $qDocDat .= "sucidxxx = \"{$xRCD['sucidxxx']}\" AND ";
                  $qDocDat .= "docsufxx = \"{$xRCD['docsufxx']}\" LIMIT 0,1 ";
                  $xDocDat  = f_MySql("SELECT","",$qDocDat,$xConexion01,"");
                  $xRDD = mysql_fetch_array($xDocDat);
                  $cFobAgen = "SI";
                break;
              }
            }//for($nC=0;$nC<count($mComObs_IP);$nC++){
          }//if(count($mComObs_IP) > 0){

          //Agrupando por Concepto
          if ($xRCD['comctocx'] == "IP") {
            for($j=0;$j<count($mCodDat);$j++){
              if($mCodDat[$j]['ctoidxxx'] == $xRCD['ctoidxxx'] && $mCodDat[$j]['seridxxx'] == $xRCD['seridxxx']){
                $nSwitch_Encontre_Concepto = 1;

                $mCodDat[$j]['comctocx'] =  $xRCD['comctocx'];
                $mCodDat[$j]['comvlrxx'] += $xRCD['comvlrxx'];
                $mCodDat[$j]['comvlr01'] += $xRCD['comvlr01'];
                $mCodDat[$j]['ctoidxxx']  = $xRCD['ctoidxxx'];
                $mCodDat[$j]['comobsxx']  = str_replace(array("MONEDA:COP","MONEDA : COP","MONEDA : USD"), "", $cObs);
                $mCodDat[$j]['comcifxx'] += $nCif;
                $mCodDat[$j]['comcifap']  = ($mCodDat[$j]['comcifap'] == "SI")?$mCodDat[$j]['comcifap']:$cAplCif;
                $mCodDat[$j]['comdimxx'] += $nDim;
                $mCodDat[$j]['comdimap']  = ($mCodDat[$j]['comdimap'] == "SI")?$mCodDat[$j]['comdimap']:$cAplDim;
                $mCodDat[$j]['comdavxx'] += $nDav;
                $mCodDat[$j]['comdavap']  = ($mCodDat[$j]['comdavap'] == "SI")?$mCodDat[$j]['comdavap']:$cAplDav;
                $mCodDat[$j]['comvucxx'] += $nVuce;
                $mCodDat[$j]['comvucap']  = ($mCodDat[$nC]['comvucap']  == "SI")?$mCodDat[$nC]['comvucap'] :$cAplVuce;
                $mCodDat[$j]['comcerxx'] += $nCertificados;
                $mCodDat[$j]['comcerap']  = ($mCodDat[$nC]['comcerap']  == "SI")?$mCodDat[$nC]['comcerap'] :$cAplCertificados;
                $mCodDat[$j]['comfobxx']  = $cFobAgen;
                $mCodDat[$j]['docfobxx'] += $xRDD['docfobxx'];
                $mCodDat[$j]['comhorxx'] += $nHor;
                $mCodDat[$j]['comhorap']  = ($mCodDat[$j]['comhorap'] == "SI")?$mCodDat[$j]['comhorap']:$cAplHor;
                $mCodDat[$j]['compiexx'] += $nPie;
                $mCodDat[$j]['compieap']  = ($mCodDat[$j]['compieap'] == "SI")?$mCodDat[$j]['compieap']:$cAplPie;
                $mCodDat[$j]['comdexxx'] += $nDex;
                $mCodDat[$j]['comdexap']  = ($mCodDat[$j]['comdexap'] == "SI")?$mCodDat[$j]['comdexap']:$cAplDex;
                $mCodDat[$j]['comserxx'] += $nSerial;
                $mCodDat[$j]['comserap']  = ($mCodDat[$j]['comserap'] == "SI")?$mCodDat[$j]['comserap']:$cAplSerial;
                $mCodDat[$j]['comaraxx'] += $nArancelaria;
                $mCodDat[$j]['comaraap']  = ($mCodDat[$j]['comaraap'] == "SI")?$mCodDat[$j]['comaraap']:$cAplArancelaria;
                $mCodDat[$j]['comdtaxx'] += $nDta;
                $mCodDat[$j]['comdtaap']  = ($mCodDat[$j]['comdtaap'] == "SI")?$mCodDat[$j]['comdtaap']:$cAplDta;
                $mCodDat[$j]['comitexx'] += $nItems;
                $mCodDat[$j]['comiteap']  = ($mCodDat[$j]['comiteap'] == "SI")?$mCodDat[$j]['comiteap']:$cAplItems;
                $mCodDat[$j]['comcanxx'] += $nCan;
                $mCodDat[$j]['comcanap']  = ($mCodDat[$j]['comcanap'] == "SI")?$mCodDat[$j]['comcanap']:$cAplCan;
                $mCodDat[$j]['comfob2x'] += $nFob;
                $mCodDat[$j]['comfobap']  = ($mCodDat[$j]['comfobap'] == "SI")?$mCodDat[$j]['comfobap']:$cAplFob;
                $mCodDat[$j]['comc20xx'] += $nCon20;
                $mCodDat[$j]['comc20ap']  = ($mCodDat[$j]['comc20ap'] == "SI")?$mCodDat[$j]['comc20ap']:$cAplCon20;
                $mCodDat[$j]['comc40xx'] += $nCon40;
                $mCodDat[$j]['comc40ap']  = ($mCodDat[$j]['comc40ap'] == "SI")?$mCodDat[$j]['comc40ap']:$cAplCon40;
                $mCodDat[$j]['comcsuxx'] += $nCarSue;
                $mCodDat[$j]['comcsuap']  = ($mCodDat[$j]['comcsuap'] == "SI")?$mCodDat[$j]['comcsuap']:$cAplCarSue;
              }
            }
          }

          if ($nSwitch_Encontre_Concepto == 0) {
            $nInd_mConData = count($mCodDat);
            $mCodDat[$nInd_mConData] = $xRCD;
            $mCodDat[$nInd_mConData]['comcifxx'] = $nCif;
            $mCodDat[$nInd_mConData]['comobsxx'] = str_replace(array("MONEDA:COP","MONEDA : COP","MONEDA : USD"), "", $cObs);
            $mCodDat[$nInd_mConData]['comcifap'] = $cAplCif;
            $mCodDat[$nInd_mConData]['comdimxx'] = $nDim;
            $mCodDat[$nInd_mConData]['comdimap'] = $cAplDim;
            $mCodDat[$nInd_mConData]['comdavxx'] = $nDav;
            $mCodDat[$nInd_mConData]['comdavap'] = $cAplDav;
            $mCodDat[$nInd_mConData]['comvucxx'] = $nVuce;
            $mCodDat[$nInd_mConData]['comvucap'] = $cAplVuce;
            $mCodDat[$nInd_mConData]['comcerxx'] = $nCertificados;
            $mCodDat[$nInd_mConData]['comcerap'] = $cAplCertificados;
            $mCodDat[$nInd_mConData]['comfobxx'] = $cFobAgen;
            $mCodDat[$nInd_mConData]['docfobxx'] = $xRDD['docfobxx'];
            $mCodDat[$nInd_mConData]['doctrmxx'] = $xRDD['doctrmxx'];
            $mCodDat[$nInd_mConData]['comhorxx'] = $nHor;
            $mCodDat[$nInd_mConData]['comhorap'] = $cAplHor;
            $mCodDat[$nInd_mConData]['compiexx'] = $nPie;
            $mCodDat[$nInd_mConData]['compieap'] = $cAplPie;
            $mCodDat[$nInd_mConData]['comdexxx'] = $nDex;
            $mCodDat[$nInd_mConData]['comdexap'] = $cAplDex;
            $mCodDat[$nInd_mConData]['comserxx'] = $nSerial;
            $mCodDat[$nInd_mConData]['comserap'] = $cAplSerial;
            $mCodDat[$nInd_mConData]['comaraxx'] = $nArancelaria;
            $mCodDat[$nInd_mConData]['comaraap'] = $cAplArancelaria;
            $mCodDat[$nInd_mConData]['comdtaxx'] = $nDta;
            $mCodDat[$nInd_mConData]['comdtaap'] = $cAplDta;
            $mCodDat[$nInd_mConData]['comitexx'] = $nItems;
            $mCodDat[$nInd_mConData]['comiteap'] = $cAplItems;
            $mCodDat[$nInd_mConData]['comcanxx'] = $nCan;
            $mCodDat[$nInd_mConData]['comcanap'] = $cAplCan;
            $mCodDat[$nInd_mConData]['comfob2x'] = $nFob;
            $mCodDat[$nInd_mConData]['comfobap'] = $cAplFob;
            $mCodDat[$nInd_mConData]['comc20xx'] = $nCon20;
            $mCodDat[$nInd_mConData]['comc20ap'] = $cAplCon20;
            $mCodDat[$nInd_mConData]['comc40xx'] = $nCon40;
            $mCodDat[$nInd_mConData]['comc40ap'] = $cAplCon40;
            $mCodDat[$nInd_mConData]['comcsuxx'] = $nCarSue;
            $mCodDat[$nInd_mConData]['comcsuap'] = $cAplCarSue;
          }
        }
      }
      // Fin de Cargo la Matriz con los ROWS del Cursor
    }

    /*** Consulto en que bloque se deben imprimir los anticipos PCC o IP ***/
    $vComObs2 = explode("~", $vCocDat['comobs2x']);
    if( $vComObs2[9] == "PCC" ){
      $nAnticipo = 1; // anticipos en pagos a terceros
    }else if($vComObs2[9] == "AMBOS" || $vComObs2[9] == ""){
      $nAnticipo = 2; // anticipos en ingresos propios
    }

    /*** Consulto los anticipos en el campo memo de cabecera commempa y se llena la matriz de anticipos ***/
    $nTotAnt = 0;
    $mComFp = f_Explode_Array($vCocDat['comfpxxx'],"|","~");
    for ($k=0;$k<count($mComFp);$k++) {
      if($mComFp[$k][13] != "" && $mComFp[$k][13] != 0){
        $nTotAnt += $mComFp[$k][13];
      }
    }

    ## Traigo el CLINOMXX o Razon Social de la Agencia de Aduana Dietrich Para encabezado de factura ##
    $qAgeDat  = "SELECT ";
    $qAgeDat .= "$cAlfa.SIAI0150.CLIIDXXX, ";
    $qAgeDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX, ";
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
    $nFilRes  = mysql_num_rows($xResDat);
    if ($nFilRes > 0) {
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
    $nFilCiu  = mysql_num_rows($xPaiDat);
    if ($nFilCiu > 0) {
      $vPaiDat = mysql_fetch_array($xPaiDat);
    }
    ##Fin Traigo Pais del Cliente ##

    ##Traigo Ciudad del Cliente ##
    $qCiuDat  = "SELECT * ";
    $qCiuDat .= "FROM $cAlfa.SIAI0055 ";
    $qCiuDat .= "WHERE ";
    $qCiuDat .= "$cAlfa.SIAI0055.PAIIDXXX = \"{$vCocDat['PAIIDXXX']}\" AND ";
    $qCiuDat .= "$cAlfa.SIAI0055.DEPIDXXX = \"{$vCocDat['DEPIDXXX']}\" AND ";
    $qCiuDat .= "$cAlfa.SIAI0055.CIUIDXXX = \"{$vCocDat['CIUIDXXX']}\" AND ";
    $qCiuDat .= "$cAlfa.SIAI0055.REGESTXX = \"ACTIVO\" ";
    //f_Mensaje(__FILE__,__LINE__,$qCiuDat);
    $xCiuDat  = f_MySql("SELECT","",$qCiuDat,$xConexion01,"");
    $nFilCiu  = mysql_num_rows($xCiuDat);
    if ($nFilCiu > 0) {
      $vCiuDat = mysql_fetch_array($xCiuDat);
    }
    ##Fin Traigo Ciudad del Cliente ##

    ##Traigo Datos de Contacto del Facturado a ##
    if($vCocDat['CLICONTX'] <> ""){
      $vContactos = explode("~",$vCocDat['CLICONTX']);
      //f_Mensaje(__FILE__,__LINE__,count($vContactos));
      if(count($vContactos) > 1){
        $vIdContacto = $vContactos[1];
      }else{
        $vIdContacto = $vCocDat['CLICONTX'];
      }

    }//if($vCocDat['CLICONTX'] <> ""){

    $qConDat  = "SELECT ";
    $qConDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)))) AS NOMBRE ";
    $qConDat .= "FROM $cAlfa.SIAI0150 ";
    $qConDat .= "WHERE ";
    $qConDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"$vIdContacto\" AND ";
    $qConDat .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" ";
    //f_Mensaje(__FILE__,__LINE__,$qConDat);
    $xConDat  = f_MySql("SELECT","",$qConDat,$xConexion01,"");
    $nFilCon  = mysql_num_rows($xConDat);
    if ($nFilCon > 0) {
      $vConDat = mysql_fetch_array($xConDat);
    }
    ##Fin Traigo Datos de Contacto del Facturado a ##

    ##Traigo Dias de Plazo ##
    $qCccDat  = "SELECT * ";
    $qCccDat .= "FROM $cAlfa.fpar0151 ";
    $qCccDat .= "WHERE ";
    $qCccDat .= "$cAlfa.fpar0151.cliidxxx = \"{$vCocDat['terid2xx']}\" AND ";
    $qCccDat .= "$cAlfa.fpar0151.regestxx = \"ACTIVO\" ";
    $xCccDat  = f_MySql("SELECT","",$qCccDat,$xConexion01,"");
    $nFilCcc  = mysql_num_rows($xCccDat);
    if ($nFilCcc > 0) {
      $vCccDat = mysql_fetch_array($xCccDat);
    }
    ##Fin Traigo Dias de Plazo ##

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

    ##Busco Do en la sys00121 Tabla de Do's Financieros y traigo Tipo de Operacion ##
    $qDceDat  = "SELECT * ";
    $qDceDat .= "FROM $cAlfa.sys00121 ";
    $qDceDat .= "WHERE ";
    $qDceDat .= "$cAlfa.sys00121.sucidxxx = \"$cSucId\" AND ";
    $qDceDat .= "$cAlfa.sys00121.docidxxx = \"$cDocId\" AND ";
    $qDceDat .= "$cAlfa.sys00121.docsufxx = \"$cDocSuf\" ";
    $xDceDat  = f_MySql("SELECT","",$qDceDat,$xConexion01,"");
    $nFilDce  = mysql_num_rows($xDceDat);
    if ($nFilDce > 0) {
      $vDceDat = mysql_fetch_array($xDceDat);
    }
    ##Fin Busco Do en la sys00121 Tabla de Do's Financieros y traigo Tipo de Operacion ##

    ##Switch para traer Datos de Do Dependiendo del Tipo de Operacion ##
    switch ($vDceDat['doctipxx']){
      case "IMPORTACION":
        ##Traigo Datos de la SIAI0200 DATOS DEL DO ##
        $qDoiDat  = "SELECT * ";
        $qDoiDat .= "FROM $cAlfa.SIAI0200 ";
        $qDoiDat .= "WHERE $cAlfa.SIAI0200.DOIIDXXX = \"$cDocId\" AND ";
        $qDoiDat .= "$cAlfa.SIAI0200.DOISFIDX = \"$cDocSuf\" AND ";
        $qDoiDat .= "$cAlfa.SIAI0200.ADMIDXXX = \"$cSucId\" ";
        //f_Mensaje(__FILE__,__LINE__,$qDoiDat);
        $xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
        $nFilDoi  = mysql_num_rows($xDoiDat);
        if ($nFilDoi > 0) {
          $vDoiDat  = mysql_fetch_array($xDoiDat);
        }
        ##Fin Traigo Datos de la SIAI0200 DATOS DEL DO ##

        $qDecDat  = "SELECT ";
        $qDecDat .= "SUBID2XX, ";
        $qDecDat .= "ADMIDXXX, ";
        $qDecDat .= "SUM($cAlfa.SIAI0206.LIMBULXX) AS LIMBULXX, ";
        $qDecDat .= "SUM($cAlfa.SIAI0206.LIMNETXX) AS LIMNETXX, ";
        $qDecDat .= "SUM($cAlfa.SIAI0206.LIMCIFXX) AS LIMCIFXX, ";
        $qDecDat .= "SUM($cAlfa.SIAI0206.LIMPBRXX) AS LIMPBRXX, ";
        $qDecDat .= "SUM($cAlfa.SIAI0206.LIMPNEXX) AS LIMPNEXX, ";
        $qDecDat .= "SUM($cAlfa.SIAI0206.LIMVLRXX) AS LIMVLRXX, ";//Fob
        $qDecDat .= "SUM($cAlfa.SIAI0206.LIMGRAXX) AS LIMGRA2X, ";
        $qDecDat .= "SUM($cAlfa.SIAI0206.LIMSUBT2) AS LIMSUBT2, ";
        $qDecDat .= "SUM($cAlfa.SIAI0206.LIMFLEXX) AS LIMFLEXX, ";
        $qDecDat .= "SUM($cAlfa.SIAI0206.LIMSEGXX) AS LIMSEGXX ";
        $qDecDat .= "FROM $cAlfa.SIAI0206 ";
        $qDecDat .= "WHERE ";
        $qDecDat .= "$cAlfa.SIAI0206.DOIIDXXX = \"$cDocId\"  AND ";
        $qDecDat .= "$cAlfa.SIAI0206.DOISFIDX = \"$cDocSuf\" AND ";
        $qDecDat .= "$cAlfa.SIAI0206.ADMIDXXX = \"$cSucId\" ";
        $qDecDat .= "GROUP BY $cAlfa.SIAI0206.DOIIDXXX ";

        //f_Mensaje(__FILE__,__LINE__,$qDecDat);
        $xDecDat  = f_MySql("SELECT","",$qDecDat,$xConexion01,"");
        $nFilDec  = mysql_num_rows($xDecDat);
        if ($nFilDec > 0) {
          $vDecDat  = mysql_fetch_array($xDecDat);
        }

        ##Administracion de ingreso##
        $vAdmIng = array();
        if ($vDoiDat['ODIIDXXX'] != "") {
          $qAdmIng  = "SELECT ODIDESXX ";
          $qAdmIng .= "FROM $cAlfa.SIAI0103 ";
          $qAdmIng .= "WHERE ";
          $qAdmIng .= "ODIIDXXX = \"{$vDoiDat['ODIIDXXX']}\" ";
          $qAdmIng .= "LIMIT 0,1 ";
          $xAdmIng  = f_MySql("SELECT","",$qAdmIng,$xConexion01,"");
          $vAdmIng  = mysql_fetch_array($xAdmIng);
        }

        //Busco nombre comercial
        $qDceDat  = "SELECT $cAlfa.SIAI0205.ITENOCXX ";
        $qDceDat .= "FROM $cAlfa.SIAI0205 ";
        $qDceDat .= "WHERE ";
        $qDceDat .= "$cAlfa.SIAI0205.ADMIDXXX = \"$cSucId\" AND ";
        $qDceDat .= "$cAlfa.SIAI0205.DOIIDXXX = \"$cDocId\" AND ";
        $qDceDat .= "$cAlfa.SIAI0205.DOISFIDX = \"$cDocSuf\" LIMIT 0,1";
        $xDceDat  = f_MySql("SELECT","",$qDceDat,$xConexion01,"");
        $nFilDce  = mysql_num_rows($xDceDat);
        if ($nFilDce > 0) {
          $vNomComercial = mysql_fetch_array($xDceDat);
        }

        //Busco transportadora comercial
        $qTraDat  = "SELECT $cAlfa.SIAI0206.TRADESXX ";
        $qTraDat .= "FROM $cAlfa.SIAI0206 ";
        $qTraDat .= "WHERE ";
        $qTraDat .= "$cAlfa.SIAI0206.ADMIDXXX = \"$cSucId\" AND ";
        $qTraDat .= "$cAlfa.SIAI0206.DOIIDXXX = \"$cDocId\" AND ";
        $qTraDat .= "$cAlfa.SIAI0206.DOISFIDX = \"$cDocSuf\" LIMIT 0,1";
        $xTraDat  = f_MySql("SELECT","",$qTraDat,$xConexion01,"");
        $nFilDce  = mysql_num_rows($xTraDat);
        if ($nFilDce > 0) {
          $vTransComercial = mysql_fetch_array($xTraDat);
        }


        ##Cargo Variables para Impresion de Datos de Do ##
        $cTasCam = $vDoiDat['TCATASAX']; //Tasa de Cambio
        $cDocTra = $vDoiDat['DGEDTXXX']; //Documento de Transporte
        $cBultos = $vDoiDat['DGEBULXX']; //Bultos
        $cPesBru = $vDoiDat['DGEPBRXX']; //Peso Bruto
        $nValAdu  = $vDecDat['LIMCIFXX'];
        $cOpera  = "CIF:";
        $cManifi = $vDoiDat['DGEMCXXX'];
        $cPaisOrigen = $vDoiDat['PAIIDXXX'];
        $cPedido = $vDoiDat['DOIPEDXX'];
        $cAduana = $vAdmIng['ODIDESXX'];
        $cLinPro = $vDoiDat['LPRID3XX'];
        $cTraCom = $vTransComercial['TRADESXX'];
        $cFecLev = $vDoiDat['DOIMYLEV'];
        $cNomVen = $vDoiDat['VENNOMXX'];
        ##Fin Cargo Variables para Impresion de Datos de Do ##
      break;
      case "EXPORTACION":
        ## Consulto Datos de Do en Exportaciones tabla siae0199 ##
        $qDexDat  = "SELECT * ";
        $qDexDat .= "FROM $cAlfa.siae0199 ";
        $qDexDat .= "WHERE ";
        $qDexDat .= "$cAlfa.siae0199.dexidxxx = \"$cDocId\" AND ";
        $qDexDat .= "$cAlfa.siae0199.admidxxx = \"$cSucId\" ";
        $xDexDat  = f_MySql("SELECT","",$qDexDat,$xConexion01,"");
        //f_Mensaje(__FILE__,__LINE__,$qDexDat);
        $nFilDex  = mysql_num_rows($xDexDat);
        if ($nFilDex > 0) {
          $vDexDat = mysql_fetch_array($xDexDat);
        }
        ## Fin Consulto Datos de Do en Exportaciones tabla siae0199 ##

        ##Trayendo aduana de salida##
        $qAduSal  = "SELECT odiid2xx ";
        $qAduSal .= "FROM $cAlfa.siae0200 ";
        $qAduSal .= "WHERE ";
        $qAduSal .= "$cAlfa.siae0200.dexidxxx = \"$cDocId\" AND ";
        $qAduSal .= "$cAlfa.siae0200.admidxxx = \"$cSucId\" AND ";
        $qAduSal .= "$cAlfa.siae0200.odiid2xx != \"\" LIMIT 0,1 ";
        $xAduSal  = f_MySql("SELECT","",$qAduSal,$xConexion01,"");
        $vAduSal  = mysql_fetch_array($xAduSal);
        if ($vAduSal['odiid2xx'] != "") {
          ##Tayendo descripcion Aduana de salida
          $qDesAdu  = "SELECT ODIDESXX ";
          $qDesAdu .= "FROM $cAlfa.SIAI0103 ";
          $qDesAdu .= "WHERE ";
          $qDesAdu .= "ODIIDXXX = \"{$vAduSal['odiid2xx']}\" ";
          $qDesAdu .= "LIMIT 0,1 ";
          $xDesAdu  = f_MySql("SELECT","",$qDesAdu,$xConexion01,"");
          $vDesAdu  = mysql_fetch_array($xDesAdu);
        }

        ##Traigo Valor fob, peso neto, peso bruto, bultos de la tabla de Items de Exportaciones siae0201 ##
        $qIteDat  = "SELECT ";
        $qIteDat .= "SUM($cAlfa.siae0201.itefobxx) AS itefobxx, ";
        $qIteDat .= "SUM($cAlfa.siae0201.itepbrxx) AS itepbrxx, ";
        $qIteDat .= "SUM($cAlfa.siae0201.itepnexx) AS itepnexx, ";
        $qIteDat .= "SUM($cAlfa.siae0201.itebulxx) AS itebulxx ";
        $qIteDat .= "FROM $cAlfa.siae0201 ";
        $qIteDat .= "WHERE ";
        $qIteDat .= "$cAlfa.siae0201.dexidxxx =\"$cDocId\" AND ";
        $qIteDat .= "$cAlfa.siae0201.admidxxx = \"$cSucId\" ";
        $xIteDat  = f_MySql("SELECT","",$qIteDat,$xConexion01,"");
        $nFilIte  = mysql_num_rows($xIteDat);
        if ($nFilIte > 0) {
          $vIteDat = mysql_fetch_array($xIteDat);
        }

        ##Cargo Variables para Impresion de Datos de Do ##
        $cTasCam = $vDoiDat['TCATASAX']; //Tasa de Cambio
        $cDocTra = $vDexDat['dexdtrxx']; //Documento de Transporte
        $cBultos = $vIteDat['itebulxx']; //Bultos
        $cPesBru = $vIteDat['itepbrxx']; //Peso Bruto
        $nValAdu  = ($vIteDat['itefobxx']*$vDceDat['doctrmxx']);
        $cOpera  = "FOB"; // FOB
        $cManifi = $vDexDat['dexmanxx']; //Manifiesto
        $cPedido = $vDexDat['dexpedxx'];
        $cAduana = $vDesAdu['ODIDESXX'];
        $cLinPro = $vDoiDat['lprid3xx'];
        $cTraCom = "";
        $cFecLev = "";
        $cNomVen = $vDexDat['vennomxx'];
        ##Fin Cargo Variables para Impresion de Datos de Do ##
      break;
      case "TRANSITO":
        ## Traigo Datos de la SIAI0200 ##
        $qDoiDat  = "SELECT * ";
        $qDoiDat .= "FROM $cAlfa.SIAI0200 ";
        $qDoiDat .= "WHERE ";
        $qDoiDat .= "DOIIDXXX = \"$cDocId\" AND ";
        $qDoiDat .= "DOISFIDX = \"$cDocSuf\" AND ";
        $qDoiDat .= "ADMIDXXX = \"$cSucId\" ";
        $xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
        $nFilDoi  = mysql_num_rows($xDoiDat);
        if ($nFilDoi > 0) {
          $vDoiDat = mysql_fetch_array($xDoiDat);
        }
        ## Fin Consulta a la tabla de Do's ##
        //f_Mensaje(__FILE__,__LINE__,$qDoiDat);

        ## Consulto en la Tabla de Control DTA ##
        $qDtaDat  = "SELECT * ";
        $qDtaDat .= "FROM $cAlfa.dta00200 ";
        $qDtaDat .= "WHERE ";
        $qDtaDat .= "$cAlfa.dta00200.doiidxxx = \"$cDocId\" AND ";
        $qDtaDat .= "$cAlfa.dta00200.admidxxx = \"$cSucId\" ";
        $xDtaDat  = f_MySql("SELECT","",$qDtaDat,$xConexion01,"");
        $nFilDta  = mysql_num_rows($xDtaDat);
        if ($nFilDta > 0) {
          $vDtaDat = mysql_fetch_array($xDtaDat);
        }
        ## Fin consulto en la tabla de Control DTA ##
        ## Consulto en la tabla de Items DTA ##
        $qIteDat  = "SELECT  ";
        $qIteDat .= "SUM($cAlfa.dta00201.itepbrxx) AS itepbrxx, ";
        $qIteDat .= "SUM($cAlfa.dta00201.itebulxx) AS itebulxx ";
        $qIteDat .= "FROM $cAlfa.dta00201 ";
        $qIteDat .= "WHERE ";
        $qIteDat .= "$cAlfa.dta00201.doiidxxx = \"$cDocId\" AND ";
        $qIteDat .= "$cAlfa.dta00201.admidxxx = \"$cSucId\" ";
        $xIteDat  = f_MySql("SELECT","",$qIteDat,$xConexion01,"");
        $nFilIte  = mysql_num_rows($xIteDat);
        if ($nFilIte > 0) {
          $vIteDat = mysql_fetch_array($xIteDat);
        }

        ##Administracion de ingreso##
        $vAdmIng = array();
        if ($vDoiDat['ODIIDXXX'] != "") {
          $qAdmIng  = "SELECT ODIDESXX ";
          $qAdmIng .= "FROM $cAlfa.SIAI0103 ";
          $qAdmIng .= "WHERE ";
          $qAdmIng .= "ODIIDXXX = \"{$vDoiDat['ODIIDXXX']}\" ";
          $qAdmIng .= "LIMIT 0,1 ";
          $xAdmIng  = f_MySql("SELECT","",$qAdmIng,$xConexion01,"");
          $vAdmIng  = mysql_fetch_array($xAdmIng);
        }

        //Busco nombre comercial
        $qDceDat  = "SELECT $cAlfa.SIAI0205.ITENOCXX ";
        $qDceDat .= "FROM $cAlfa.SIAI0205 ";
        $qDceDat .= "WHERE ";
        $qDceDat .= "$cAlfa.SIAI0205.ADMIDXXX = \"$cSucId\" AND ";
        $qDceDat .= "$cAlfa.SIAI0205.DOIIDXXX = \"$cDocId\" AND ";
        $qDceDat .= "$cAlfa.SIAI0205.DOISFIDX = \"$cDocSuf\" LIMIT 0,1";
        $xDceDat  = f_MySql("SELECT","",$qDceDat,$xConexion01,"");
        $nFilDce  = mysql_num_rows($xDceDat);
        if ($nFilDce > 0) {
          $vNomComercial = mysql_fetch_array($xDceDat);
        }

        //Busco transportadora comercial
        $qTraDat  = "SELECT $cAlfa.SIAI0206.TRADESXX ";
        $qTraDat .= "FROM $cAlfa.SIAI0206 ";
        $qTraDat .= "WHERE ";
        $qTraDat .= "$cAlfa.SIAI0206.ADMIDXXX = \"$cSucId\" AND ";
        $qTraDat .= "$cAlfa.SIAI0206.DOIIDXXX = \"$cDocId\" AND ";
        $qTraDat .= "$cAlfa.SIAI0206.DOISFIDX = \"$cDocSuf\" LIMIT 0,1";
        $xTraDat  = f_MySql("SELECT","",$qTraDat,$xConexion01,"");
        $nFilDce  = mysql_num_rows($xTraDat);
        if ($nFilDce > 0) {
          $vTransComercial = mysql_fetch_array($xTraDat);
        }        

        ##Cargo Variables para Impresion de Datos de Do ##
        $cTasCam = $vDoiDat['TCATASAX']; //Tasa de Cambio
        $cDocTra = $vDoiDat['DGEDTXXX']; //Documento de Transporte
        $cBultos = $vIteDat['itebulxx']; //Bultos
        $cPesBru = $vIteDat['itepbrxx']; //Peso Bruto
        $nValAdu = $vDtaDat['dtafobxx'];
        $cOpera  = "CIF"; // CIF
        $cManifi = $vDoiDat['DGEMCXXX'];
        $cPedido = $vDoiDat['DOIPEDXX'];
        $cAduana = $vAdmIng['ODIDESXX'];
        $cLinPro = $vDoiDat['LPRID3XX'];
        $cTraCom = $vTransComercial['TRADESXX'];
        $cFecLev = $vDoiDat['DOIMYLEV'];
        $cNomVen = $vDoiDat['VENNOMXX'];
        ##Fin Cargo Variables para Impresion de Datos de Do ##
      break;
      case "OTROS":
      break;
    }//switch (){
    ##Fin Switch para traer Datos de Do Dependiendo del Tipo de Operacion ##

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
        /*if (substr_count($mIT[$i][2]," DIAN") > 0 || substr_count($mIT[$i][2],"DECLARACION") > 0 ||
          (trim(substr($mIT[$i][2],0,$mComObs_PCC)) == "RECIBO OFICIAL DE PAGO TRIBUTOS ADUANEROS Y SANCIO")){ // Encontre la palabra DIAN de pago de impuestos.
          $nInd_mIngTer = count($mIngTer);
          $mIngTer[$nInd_mIngTer] = $mIT[$i]; // Ingreso el registro como nuevo.
          $mIngTer[$nInd_mIngTer][2]  = "DERECHOS DE ADUANA Y COMPLEMENTARIOS";
          $mIngTer[$nInd_mIngTer][99]  = "DIAN";
          $mIngTer[$nInd_mIngTer][100] = ((strlen($mIngTer[$i][100]) + strlen($mIT[$i][5]) + 1) < 50) ? $mIngTer[$i][100]."/".$mIT[$i][5] : $mIngTer[$i][100];
          $mIngTer[$nInd_mIngTer][100] = (substr($mIngTer[$i][100],0,1) == "/") ? substr($mIngTer[$i][100],1,strlen($mIngTer[$i][100])) : $mIngTer[$i][100];
        } else {*/
          $vRCM = array();
          if (in_array("{$mIT[$i][3]}~{$mIT[$i][4]}", $mRCM) == true) {
            $vTramite = explode("-",$mIT[$i][14]);
            $cSucId  = $vTramite[0];
            $cDocId  = "";
            for($nD=1; $nD<count($vTramite)-1; $nD++) {
              $cDocId .= "{$vTramite[$nD]}-";
            }
            $cDocId  = substr($cDocId, 0,-1);
            $cDocSuf = $vTramite[count($vTramite)-1];

            //Traigo el consecutivo del comprobante tipo P
            for($iAno=date('Y');$iAno>=$nAnoIniDo;$iAno--) {
              $qComMar  = "SELECT comcsc2x ";
              $qComMar .= "FROM $cAlfa.fcod$cAno ";
              $qComMar .= "WHERE ";
              $qComMar .= "comidxxx = \"{$mIT[$i][3]}\"  AND ";
              $qComMar .= "comcodxx = \"{$mIT[$i][4]}\"  AND ";
              $qComMar .= "comcscxx = \"{$mIT[$i][5]}\"  AND ";
              $qComMar .= "comseqxx = \"{$mIT[$i][6]}\"  AND ";
              $qComMar .= "ctoidxxx = \"{$mIT[$i][1]}\"  AND ";
              $qComMar .= "pucidxxx = \"{$mIT[$i][9]}\"  AND ";
              $qComMar .= "teridxxx = \"{$mIT[$i][11]}\" AND ";
              $qComMar .= "terid2xx = \"{$mIT[$i][12]}\" AND ";
              $qComMar .= "comidcxx = \"P\"        AND ";
              $qComMar .= "comcodcx = \"001\"      AND ";
              $qComMar .= "comcsccx = \"$cDocId\"  AND ";
              $qComMar .= "comseqcx = \"$cDocSuf\" AND ";
              $qComMar .= "regestxx = \"ACTIVO\" ";
              $xComMar  = f_MySql("SELECT","",$qComMar,$xConexion01,"");
              //f_Mensaje(__FILE__,__LINE__,$qComMar,"~",mysql_num_rows($xComMar));
              if(mysql_num_rows($xComMar) > 0) {
                $iAno = $nAnoIniDo-1;
                $vRCM = mysql_fetch_array($xComMar);
              }
            }
          }

          $nSwitch_Encontre_Concepto = 0;
          // for ($j=0;$j<count($mIngTer);$j++) {
          //   if ($mIngTer[$j][1] == $mIT[$i][1] /*&& $vTercero[2] == $mIngTer[$j][98]*/) {
          //     $nSwitch_Encontre_Concepto = 1;
          //     $mIngTer[$j][7] += $mIT[$i][7]; // Acumulo el valor de ingreso para tercero.
          //     $mIngTer[$j][15] += $mIT[$i][15]; // Acumulo base de iva.
          //     $mIngTer[$j][16] += $mIT[$i][16]; // Acumulo valor del iva.
          //     $mIngTer[$j][20] += $mIT[$i][20]; // Acumulo el valor de ingreso para tercero en Dolares.
          //     $mIngTer[$j][100] = ((strlen($mIngTer[$j][100]) + strlen($mIT[$i][5]) + 1) < 50) ? $mIngTer[$j][100]."/".$mIT[$i][5] : $mIngTer[$j][100];
          //     $mIngTer[$j][100] = (substr($mIngTer[$j][100],0,1) == "/") ? substr($mIngTer[$j][100],1,strlen($mIngTer[$j][100])) : $mIngTer[$j][100];
          //     $j = count($mIngTer); // Me salgo del FOR cuando encuentro el concepto.
          //   }
          // }

          if ($nSwitch_Encontre_Concepto == 0) { // No encontre el ingreso para tercero en la matrix $mIngTer
            $nInd_mIngTer = count($mIngTer);

            $mIngTer[$nInd_mIngTer] = $mIT[$i]; // Ingreso el registro como nuevo.
            // if (substr_count($mIT[$i][2]," DIAN") > 0 || substr_count($mIT[$i][2],"DECLARACION") > 0 ||
            // (trim(substr($mIT[$i][2],0,$mComObs_PCC)) == "RECIBO OFICIAL DE PAGO TRIBUTOS ADUANEROS Y SANCIO")){
            //  $mIngTer[$nInd_mIngTer][2]  = "DERECHOS DE ADUANA Y COMPLEMENTARIOS";
            //  $mIngTer[$nInd_mIngTer][99]  = "DIAN";
            // } else {
              $mIngTer[$nInd_mIngTer][2]  = $vTercero[0];
              $mIngTer[$nInd_mIngTer][99]  = $vTercero[1];
            // }

            $mIngTer[$nInd_mIngTer][98] =  $vTercero[2];
            //Si es Tipo RCM
            if (in_array("{$mIT[$i][3]}~{$mIT[$i][4]}", $mRCM) == true && $vRCM['comcsc2x'] != "") {
              $mIngTer[$nInd_mIngTer][100] = ((strlen($mIngTer[$nInd_mIngTer][100]) + strlen($vRCM['comcsc2x']) + 1) < 50) ? $mIngTer[$nInd_mIngTer][100]."/".$vRCM['comcsc2x'] : $mIngTer[$nInd_mIngTer][100];
              $mIngTer[$nInd_mIngTer][100] = (substr($mIngTer[$nInd_mIngTer][100],0,1) == "/") ? substr($mIngTer[$nInd_mIngTer][100],1,strlen($mIngTer[$nInd_mIngTer][100])) : $mIngTer[$nInd_mIngTer][100];
            }else{
              $mIngTer[$nInd_mIngTer][100] = ((strlen($mIngTer[$nInd_mIngTer][100]) + strlen($mIT[$i][5]) + 1) < 50) ? $mIngTer[$nInd_mIngTer][100]."/".$mIT[$i][5] : $mIngTer[$nInd_mIngTer][100];
              $mIngTer[$nInd_mIngTer][100] = (substr($mIngTer[$nInd_mIngTer][100],0,1) == "/") ? substr($mIngTer[$nInd_mIngTer][100],1,strlen($mIngTer[$nInd_mIngTer][100])) : $mIngTer[$nInd_mIngTer][100];
            }
          }
        /*}*/
      }
    }

    $cCscFac = ($vCocDat['regestxx'] == "PROVISIONAL") ?  "XXXXX" : $vCocDat['comcscxx'];
    // Fin de Codigo para imprimir los ingresos para terceros

    ## Codigo Para Imprimir Original y numero de Copias ##
    $cRoot = $_SERVER['DOCUMENT_ROOT'];

    define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
    require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');

    ##Fin Switch para incluir fuente y clase pdf segun base de datos ##
    class PDF extends FPDF {
      function Header() {
        global $cAlfa;   global $cRoot;   global $cPlesk_Skin_Directory;
        global $gCcoId;  global $gSccId;  global $gMesDes; global $gAnoDes; global $gMesHas; global $gAnoHas;
        global $cUsrNom; global $cCcoDes; global $cScoDes; global $nPag;    global $vAgeDat; global $vCocDat;
        global $vResDat; global $cDocTra; global $cTasCam; global $cDocTra; global $cBultos; global $cPesBru;
        global $cDocId;  global $vCiuDat; global $vDceDat; global $cOpera;  global $nValAdu;  global $vCccDat;
        global $cCscFac; global $vConDat; global $cPaisOrigen; global $cPedido; global $vNomComercial; global $cManifi;
        global $vIdContacto; global $cEstiloLetra; global $cAduana;  global $_COOKIE; global $vPaiDat; global $vSysStr; 
        global $cTraCom; global $cLinPro; global $cFecLev; global $cNomVen;

        if ($vCocDat['regestxx'] == "INACTIVO") {
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/facturaanulada.jpg',10,50,180,180);
        }

        if ($_COOKIE['kModo'] == "VERFACTURA"){
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/copianovalida.jpg',10,50,180,180);
        }

        /*** Impresion Datos Generales Factura ***/
        $nPosY = 18;
        $nPosX = 12.5;

        $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoAndinos2.jpeg',$nPosX,$nPosY-13,25,30);

        $this->SetFont($cEstiloLetra,'',11);
        $this->setXY($nPosX+60,$nPosY-9);
        $this->Cell(77,4,utf8_decode("AGENCIA DE ADUANAS ANDINOS S.A.S NIVEL 1"),0,0,'C');
        $this->SetFont($cEstiloLetra,'',8);
        $this->setXY($nPosX+60,$nPosY-5);
        $this->Cell(77,4,utf8_decode("NIT: {$vSysStr['financiero_nit_agencia_aduanas']}"),0,0,'C');
        $this->setXY($nPosX+60,$nPosY-1);
        $this->Cell(77,4,utf8_decode("IVA RÉGIMEN COMÚN DIAN No 0128"),0,0,'C');

        $this->SetFont($cEstiloLetra,'B',8);
        $this->setXY($nPosX+125,$nPosY);
        $this->Cell(77,4,utf8_decode("OFICINA PRINCIPAL"),0,0,'C');
        $this->setXY($nPosX+120,$nPosY+6);
        $this->Cell(77,4,utf8_decode("Carrera 46 No. 62 - 20 APT 202 - EDIFICIO TOWER 62"),0,0,'C');
        $this->setXY($nPosX+125,$nPosY+9);
        $this->Cell(77,4,utf8_decode("Celular: 3165339963"),0,0,'C');
        $this->setXY($nPosX+125,$nPosY+12);
        $this->Cell(77,4,utf8_decode("Barranquilla - Colombia"),0,0,'C');


        #RESOLUCION
        $this->SetFont($cEstiloLetra,'',6);
        $this->setXY($nPosX,$nPosY+14);
        $this->Cell(77,4,utf8_decode("RESOLUCIÓN DE FACTURA N° {$vResDat['residxxx']}"),0,0,'L');
        $this->setXY($nPosX,$nPosY+17);
        $this->Cell(77,4,utf8_decode("FECHA DEL {$vResDat['resfdexx']} AL {$vResDat['resfhaxx']}"),0,0,'L');
        $this->setXY($nPosX,$nPosY+20);
        $this->Cell(77,4,utf8_decode("DEL {$vResDat['resprexx']}-{$vResDat['resdesxx']} Al {$vResDat['resprexx']}-{$vResDat['reshasxx']}"),0,0,'L');
        $this->setXY($nPosX,$nPosY+23);
        $this->Cell(77,4,utf8_decode("NO SOMOS GRANDES CONTRIBUYENTES CODIGO DE INDUSTRIA Y COMERCIO No 7490. Tarifa ICA 9.66*1000 Para Bogotá"),0,0,'L');
        $this->setXY($nPosX,$nPosY+26);
        $this->Cell(77,4,"Tarifa de Industria y Comercio Cali-Buenaventura-Barranquilla 10*1000 Tarifa Industria y Comercio Cartagena 8*1000 - Tarifa Ipiales 7*1000",0,0,'L');


        #SEÑORES 
        $this->Rect($nPosX, $nPosY+30, 120, 20); 
        $this->SetFont($cEstiloLetra,'B',8);
        $this->setXY($nPosX,$nPosY+31);
        $this->Cell(16,4,utf8_decode("Señor(es): "),0,0,'L');
        $this->SetFont($cEstiloLetra,'',8);
        $this->Cell(110,4,utf8_decode("{$vCocDat['CLINOMXX']} "),0,0,'L');
        ///////////////////////////////////////////////////////////////
        #NIT
        $this->setXY($nPosX+16,$nPosY+35);
        $this->Cell(110,4,$vCocDat['terid2xx'],0,0,'L');
        $this->setXY($nPosX+16,$nPosY+39);
        #DIRECCION
        $this->Cell(110,4,$vCocDat['CLIDIRXX'],0,0,'L');
        $this->setXY($nPosX+16,$nPosY+43);
        #CIUDAD
        $this->Cell(110,4,$vCiuDat['CIUDESXX'],0,0,'L');
        #TELEFONO
        $this->setXY($nPosX+16,$nPosY+47);
        $this->Cell(110,3,$vCocDat['CLITELXX'],0,0,'L');

        ///////////////////////////////////////////////

        $this->Rect($nPosX+125, $nPosY+30, 65, 8);
        $this->SetFont($cEstiloLetra,'B',8);
        $this->setXY($nPosX+125,$nPosY+30.5);
        $this->Cell(65,4,utf8_decode("FACTURA DE VENTA"),0,0,'C');
        $this->SetFont($cEstiloLetra,'B',8);
        $this->setXY($nPosX+125,$nPosY+34);
        $this->Cell(35,4,"No. {$vCocDat['resprexx']}",0,0,'R');
        $this->SetFont($cEstiloLetra,'',8);
        $this->Cell(30,4,utf8_decode("{$vCocDat['comcscxx']}"),0,0,'L');

        $this->Rect($nPosX+125, $nPosY+38, 65, 5);
        $this->SetFont($cEstiloLetra,'B',7);
        $this->setXY($nPosX+130,$nPosY+39);
        $this->Cell(35,4,utf8_decode("FECHA FAC."),0,0,'L');
        $this->Cell(35,4,utf8_decode("FECHA VENC."),0,0,'L');

        $this->Rect($nPosX+125, $nPosY+43, 65, 7);
        $this->SetFont($cEstiloLetra,'B',6.5);
        $this->setXY($nPosX+130,$nPosY+43.5);
        $this->Cell(35,3,"DD/MM/AAAA",0,0,'L');
        $this->setXY($nPosX+165,$nPosY+43.5);
        $this->Cell(35,3,"DD/MM/AAAA",0,0,'L');
        #FECHA FACTURA 
        $this->SetFont($cEstiloLetra,'',8);
        $this->setXY($nPosX+130,$nPosY+46.5);
        $this->Cell(35,3,str_replace("-", "/", $vCocDat['comfecxx']),0,0,'L');
        #FECHA DE VENCIMIENTO
        $this->setXY($nPosX+165,$nPosY+46.5);
        $this->Cell(35,3,str_replace("-", "/", $vCocDat['comfecve']),0,0,'L');

        ////////////////////////////////////////////////////////
        $this->SetFont($cEstiloLetra,'',8);
        $this->setXY($nPosX+8,$nPosY+51);
        $this->Cell(30,3,"MANIFIESTO:",0,0,'L');
        $this->Cell(80,3,$cManifi,0,0,'L');

        ////////////////////////////////////////////////////////////
        $this->setXY($nPosX+8,$nPosY+55);
        $this->Cell(30,3,"BULTOS:",0,0,'L');
        $this->Cell(80,3,number_format($cBultos, 0,'.','.'),0,0,'L');
        ////////////////////////////////////////////////////////////
        $this->setXY($nPosX+8,$nPosY+59);
        $this->Cell(30,3,"CONTENIDO:",0,0,'L');
        $this->Cell(80,3,$cLinPro,0,0,'L');
        ////////////////////////////////////////////////////////////
        $this->SetFont($cEstiloLetra,'',8);
        $this->setXY($nPosX+8,$nPosY+63);
        $this->Cell(30,3,"PEDIDO:",0,0,'L');
        $this->Cell(80,3,$cPedido,0,0,'L');
        ////////////////////////////////////////////////////////////
        $this->setXY($nPosX+8,$nPosY+67);
        $this->Cell(30,3,"OBSERVACIONES:",0,0,'L');
        $this->Cell(75,3,substr($vCocDat['comobsxx'], 0, 49),0,0,'L');


        ////////////////////////////////////////////////////////////
        $this->SetFont($cEstiloLetra,'',8);
        $this->setXY($nPosX+110,$nPosY+51);
        $this->Cell(33,3,"D.O: ",0,0,'L');
        $this->Cell(27,3,$cDocId,0,0,'L');
        ////////////////////////////////////////////////////////////
        $this->SetFont($cEstiloLetra,'',8);
        $this->setXY($nPosX+110,$nPosY+55);
        $this->Cell(33,3,"NAVIERA: ",0,0,'L');
        $this->MultiCell(27,3,$cTraCom,0,'L');
        ////////////////////////////////////////////////////////////
        $posY_naviera = $this->GetY()+2;
        $this->SetFont($cEstiloLetra,'',8);
        $this->setXY($nPosX+110,$posY_naviera);
        $this->Cell(33,3,"FECHA DE DESPACHO: ",0,0,'L');
        $this->Cell(27,3,str_replace("-", "", $cFecLev) ,0,0,'L');
        ////////////////////////////////////////////////////////////
        $this->SetFont($cEstiloLetra,'',8);
        $this->setXY($nPosX+110,$posY_naviera+4);
        $this->Cell(10,3,"KILOS: ",0,0,'L');
        $this->Cell(23,3,number_format($cPesBru, 3,'.','.'). " KG" ,0,0,'L');

        ///////////////////////////////////////////////////////////
        $this->SetFont($cEstiloLetra,'',8);
        $this->Cell(8,3,"TRM: ",0,0,'L');
        $this->SetFont($cEstiloLetra,'',8);
        $this->Cell(19,3,number_format($cTasCam, 0,'.','.'),0,0,'L');
        ////////////////////////////////////////////////////////////
        $this->SetFont($cEstiloLetra,'',8);
        $this->setXY($nPosX+110,$posY_naviera+8);
        $this->Cell(18,3,"",0,0,'L');
        $this->SetFont($cEstiloLetra,'',7);
        $this->MultiCell(54,3,"",0,'L');
        // $this->Cell(18,3,"COMERCIAL: ",0,0,'L');
        // $this->SetFont($cEstiloLetra,'',7);
        // $this->MultiCell(54,3,$cNomVen,0,'L');

        $this->nPosY = $this->GetY();
        $this->Rect($nPosX, $nPosY+50, 190, ($this->nPosY)-($nPosY+50));


        //RECTANGULO DEL CONTENIDO
        $this->nPosYFoo = (140 - ($this->nPosY - 88)) + $this->nPosY;
        $this->Rect($nPosX, $this->nPosY, 165, 140 - ($this->nPosY - 88));
        $this->Rect($nPosX+165, $this->nPosY, 25, 140 - ($this->nPosY - 88));

        //Datos de Impresion
        $this->SetFont($cEstiloLetra,'',7);
        $cResoliucion = "IMPRESO POR AGENCIA DE ADUANAS ANDINOS S.A.S. NIVEL 1 NIT.: 860.050.097-1 - OPENCOMEX SOFTWARE";
        $this->RotatedText(204,77,$cResoliucion,270);
        $this->Rotate(0);

      }//Function Header

      // rota la celda
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
        global $cRoot;   global $cPlesk_Skin_Directory;   global $cNomCopia; global $nCopia;
        global $nContPage; global $vCocDat; global $mCodDat; global $cSaldo; global $vResDat;
        global $cEstiloLetra; global $gCorreo; global $nb;

        $nPosY = $this->nPosYFoo;
        $nPosX = 12.5;

        $this->Rect($nPosX, $nPosY, 140,28);
        $this->Rect($nPosX+140, $nPosY, 50,28);

        $this->setXY($nPosX,$nPosY+2);
        $this->SetFont($cEstiloLetra,'',8);
        $this->Cell(115,3,utf8_decode("RECIBIDO:"),0,0,'C');

        $this->setXY($nPosX,$nPosY+8);
        $this->SetFont($cEstiloLetra,'',8);
        $this->Cell(22,3,utf8_decode("NOMBRE:"),0,0,'L');
        $this->Line(30,$nPosY+11,90,$nPosY+11);

        $this->setXY($nPosX,$nPosY+13);
        $this->SetFont($cEstiloLetra,'',8);
        $this->Cell(22,3,utf8_decode("CC:"),0,0,'L');
        $this->Line(30,$nPosY+16,90,$nPosY+16);

        $this->setXY($nPosX,$nPosY+18);
        $this->SetFont($cEstiloLetra,'',8);
        $this->Cell(22,3,utf8_decode("FECHA:"),0,0,'L');
        $this->Line(30,$nPosY+21,90,$nPosY+21);

        $this->setXY($nPosX,$nPosY+23);
        $this->SetFont($cEstiloLetra,'',6);
        $this->Cell(80,3,utf8_decode("RECIBI A SATISFACCION ACEPTO LA FACTURA Y ME OBLIGO A PAGARLA"),0,0,'L');

        $this->setXY($nPosX+83,$nPosY+22);
        $this->Cell(25,3,utf8_decode("FIRMA Y SELLO"),0,0,'L');

        $this->Line(120,$nPosY,120,$nPosY+28);
        $this->setXY($nPosX+108.5,$nPosY+2);
        $this->SetFont($cEstiloLetra,'B',8);
        $this->MultiCell(30,3,utf8_decode("La presente factura debe ser cancelada a su vencimiento; de lo contrario se causarán los intererses de mora a las tasas máximas permitidas por la ley"),0,'J');  

        $this->Line(157,$nPosY+20,198,$nPosY+20);
        $this->setXY($nPosX+145,$nPosY+23);
        $this->SetFont($cEstiloLetra,'',8);
        $this->Cell(45,3,utf8_decode("FIRMA AUTORIZADA"),0,0,'C');

        $this->setXY($nPosX,$nPosY+28);
        $this->SetFont($cEstiloLetra,'',5.5);
        $this->Cell(190,3,utf8_decode("La presente factura de Venta es un Título Valor, según ley 1231 de julio 17 de 2008, excusando el protesto, el aviso de rechazo y la presentación de pago (*Art. 774 del decreto 410 de 1971: Código de Comercio)"),0,0,'C');

        if ($vCocDat['comcodxx'] == "011" || $vCocDat['comcodxx'] == "012") {
          $this->setXY($nPosX,$nPosY+32);
          $this->SetFont($cEstiloLetra,'',8);
          $this->MultiCell(188,4,utf8_decode("Favor efectuar consignación para recaudos de Cartagena y Barranquilla a la cuenta Bancolombia-Ahorros No.23700005593 y enviar soporte al correo diana.villamil@andinossas.com"),0,'C');
        }

        $nPosY = 270;
        $this->SetFont('Arial','B',7);
        $this->setXY($nPosX,$nPosY);
        $this->Cell(190,4,'Pag '.$this->PageNo().'/{nb}',0,0,'C');

        // $this->RotatedText(12,$nPosicionY,"PRUEBA",90);//14,220
        // ##Fin impresion de resolucion##
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

    $pdf = new PDF('P','mm','Letter');  //Error al invocar la clase
    $pdf->AddFont($cEstiloLetra,'','arial.php');
    $pdf->AliasNbPages();
    $pdf->SetMargins(0,0,0);
    $pdf->SetAutoPageBreak(0,22);
    $pdf->SetFillColor(229,229,229);

    // $mIngTer = array_merge($mIngTer, $mIngTer);
    // $mIngTer = array_merge($mIngTer, $mIngTer);
    // $mIngTer = array_merge($mIngTer, $mIngTer);
    // $mIngTer = array_merge($mIngTer, $mIngTer);
    // $mCodDat = array_merge($mCodDat, $mCodDat,$mCodDat, $mCodDat,$mCodDat, $mCodDat, $mCodDat, $mCodDat);
    // $mCodDat = array_merge($mCodDat, $mCodDat,$mCodDat, $mCodDat,$mCodDat, $mCodDat, $mCodDat, $mCodDat);

    for($y=1; $y<=1; $y++){
      $pdf->AddPage();
      $cNomCopia = "";
      $nCopia    = "";

      #Codigo Para impresion de Copias de Factura ##
      // switch($y){
      //   case 1:
      //     $cNomCopia = "ORIGINAL";
      //   break;
      //   case 2:
      //     $cNomCopia = "COPIA";
      //   break;
      // }
      #Codigo Para impresion de Copias de Factura ##

      /*** Inicializando variables por copia ***/
      $nTotPag1  = "";  
      $nTotPag   = 0;   $nSubToFacIva = 0;  $nSubToFac = 0;
      $nTotRfte  = 0;   $nTotIca      = 0;  $nTotIva = 0;       $nIva = 0;
      $nSubToIP  = 0;   $nSubToIPIva  = 0;  $nTotPcc = 0;
      $nSubToPcc = 0;   $nSubToPccIva = 0;  $nSubToPcc = 0;     $nSubTotPcc = 0;

      $nPosY = $pdf->nPosY+2;
      $nPosX = 12.5;
      $nPosFin = 200;
      $nb = 1;
      $pyy = $nPosY;


      /*** Imprimo Detalle de Pagos a Terceros e Ingresos Propios ***/
      /*** Imprimo Pagos a Terceros ***/
      if (count($mIngTer) > 0 || $nBandPcc == 1) {//Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
        $nBandPcc = 1;
        $nSubTotPcc = 0;

        $pdf->SetFont($cEstiloLetra,'B',8);

        $pdf->setXY($nPosX+5,$pyy);
        $pdf->Cell(110,6," =GASTOS POR CUENTA DEL CLIENTE - TERCEROS =",0,0,'L');
        $pdf->Cell(70,6,"VALOR",0,0,'R');

        $pyy += 6;
        $pdf->setXY($nPosX+5,$pyy);
        $pdf->SetWidths(array(150,0,37));
        $pdf->SetAligns(array("L","L","R"));

        for($i=0;$i<count($mIngTer);$i++){
          $pyy = $pdf->GetY();
          if($pyy > $nPosFin) {//Validacion para siguiente pagina si se excede espacio de impresion
            // $pdf->Rect($nPosX-2, $nPosY-2, 180, $pyy-($nPosY-4));
            $pdf->AddPage();
            $nb++;
            $nPosY = $pdf->nPosY+2;
            $nPosX = 12.5;
            $pyy = $nPosY;
            $pdf->SetFont($cEstiloLetra,'',7);
            $pdf->setXY($nPosX,$nPosY);
          }//if($pyy > $nPosFin) {

          $nSubTotPcc += $mIngTer[$i][7];
          //$cComObs  = explode("^",$mIngTer[$i][2]);
          if( $mIngTer[$i][100] <> "" ){
            $cConDes = $mIngTer[$i][2]." Fact No ".$mIngTer[$i][100];
          }else if ( $cComCsc != "" ){
            $cConDes = $mIngTer[$i][2].' Fact No '.$cComCsc;
          }else{
            $cConDes = $mIngTer[$i][2];
          }

          $pdf->SetFont($cEstiloLetra,'',7);
          $pdf->setX($nPosX+3);
          $pdf->Row(array(trim(utf8_decode($cConDes)),'','$ '.number_format($mIngTer[$i][7],0,',',',')));
        }//for($i=0;$i<count($mIngTer);$i++){

        $pyy = $pdf->GetY();
        if($pyy > $nPosFin) {//Validacion para siguiente pagina si se excede espacio de impresion
          // $pdf->Rect($nPosX-2, $nPosY-2, 180, $pyy-($nPosY-4));
          $pdf->AddPage();
          $nb++;
          $nPosY = $pdf->nPosY+2;
          $nPosX = 12.5;
          $pyy = $nPosY;
          $pdf->SetFont($cEstiloLetra,'',8);
          $pdf->setXY($nPosX,$nPosY);
        } //if($pyy > $nPosFin) {

        /*** Recorro la matriz de la 1002 para imprimir Registros de PCC ***/
        $nSubToPcc = 0;
        $nSubToPccIva =0;

        for ($i=0;$i<count($mValores);$i++) {
          $pyy = $pdf->GetY();
          if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
            // $pdf->Rect($nPosX-2, $nPosY-2, 180, $pyy-($nPosY-4));
            $pdf->AddPage();
            $nb++;
            $nPosY = $pdf->nPosY+2;
            $nPosX = 12.5;
            $pyy = $nPosY;
            $pdf->SetFont($cEstiloLetra,'',7);
            $pdf->setXY($nPosX,$nPosY);
          }//if($nPosY < 130){//Validacion para siguiente pagina si se excede espacio de impresion

          $nSubToPcc += $mValores[$i]['comvlrxx'];
          $pdf->SetFont($cEstiloLetra,'',7);
          $pdf->setX($nPosX+3);
          $pdf->Row(array(trim($mValores[$i]['comobsxx']),'','$ '.number_format($mValores[$i]['comvlrxx'],0,',',',')));
        }//for ($i=0;$i<count($mValores);$i++) {

        ## Fin Recorro la matriz de la 1002 para imprimir Registros de PCC ##
        $pyy = $pdf->GetY();
        if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
          // $pdf->Rect($nPosX-2, $nPosY-2, 180, $pyy-($nPosY-4));
          $pdf->AddPage();
          $nb++;
          $nPosY = $pdf->nPosY+2;
            $nPosX = 12.5;
          $pyy = $nPosY;
          $pdf->SetFont($cEstiloLetra,'',7);
          $pdf->setXY($nPosX,$nPosY);
        }//if($nPosY < 130){//Validacion para siguiente pagina si se excede espacio de impresion
        /*** Fin Imprimo Subtotal de Pagos a Terceros ***/
      }//if(count($mIngTer) > 0 || $nBandPcc == 1){//Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
      /*** Fin Imprimo Pagos a Terceros ***/


      $nSubToIP = 0;
      $nSubToIPIva = 0;

      if(count($mPccIng) > 0 || $nBandIP == 1){//Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS

        if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
          $pdf->AddPage();
          $nb++;
          $nPosY = $pdf->nPosY+2;
          $nPosX = 12.5;
          $pyy = $nPosY;
          $nPosYRec = $pyy;
          $pdf->SetFont($cEstiloLetra,'',7);
          $pdf->setXY($nPosX,$nPosY);
        }//if($nPosY < 130){//Validacion para siguiente pagina si se excede espacio de impresion

        $pdf->setXY($nPosX+5,$pyy);
        $pdf->SetFont($cEstiloLetra,'B',8);
        $pdf->Cell(110,6,utf8_decode("=LIQUIDACIÓN -INGRESOS PROPIOS="),0,0,'L');
        if ($nBandPcc != 1){
          $pdf->Cell(70,6,"VALOR",0,0,'R');
        }
        $pyy += 2;

        /*** Imprimo Ingresos Propios ***/
        $pdf->SetWidths(array(150,0,37));
        $pdf->SetAligns(array("L","L","R"));
        $pdf->SetFont($cEstiloLetra,'',8);

        // hago dos veces el mismo for para separar los ingresos GRAVADOS y luego los NO GRAVADOS
        $nPosicion = 0;
        for($k=0;$k<(count($mCodDat));$k++) {
          if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
            // $pdf->Rect($nPosX-2, $nPosYRec-2, 180, $pyy-($nPosYRec-4));
            $pdf->AddPage();
            $nb++;
            $nPosY = $pdf->nPosY+2;
            $nPosX = 12.5;
            $pyy = $nPosY;
            $nPosYRec = $pyy;
            $pdf->SetFont($cEstiloLetra,'',7);
            $pdf->setXY($nPosX,$nPosY);
          }

          if($mCodDat[$k]['comctocx'] == "IP" && $mCodDat[$k]['comvlr01'] != 0 ) {
            $nPosicion++;
            $nSubToIP += $mCodDat[$k]['comvlrxx'];
            $nSubToIPIva += $mCodDat[$k]['comvlr01'];
            $pdf->SetFont($cEstiloLetra,'',7);

            $cValor = "";
            if($mCodDat[$k]['comfobxx'] == "SI" && $mCodDat[$k]['docfobxx'] > 0) {
              $cValor  = " FOB: ($".number_format($mCodDat[$k]['docfobxx'],2,'.',',');
              $cValor .= ($mCodDat[$k]['doctrmxx'] > 0) ? " TRM: $".number_format($mCodDat[$k]['doctrmxx'],2,'.',',') : "";
              $cValor .= ")";
            }
            if ($mCodDat[$k]['comcifap'] == "SI"){
              $cValor = "CIF: ($".number_format($mCodDat[$k]['comcifxx'],0,'.',',').")";
            }
            $nCantidad = 1;
            if ($mCodDat[$k]['comdimap'] == "SI"){
              $nCantidad = $mCodDat[$k]['comdimxx'];
              // $cValor = "DIM: (".number_format($mCodDat[$k]['comdimxx'],0,'.',',').")";
            }
            if ($mCodDat[$k]['comdavap'] == "SI"){
              $nCantidad = $mCodDat[$k]['comdavxx'];
              // $cValor = "DAV: (".number_format($mCodDat[$k]['comdavxx'],0,'.',',').")";
            }
            if ($mCodDat[$k]['comvucap'] == "SI"){
              $nCantidad = $mCodDat[$k]['comvucxx'];
              // $cValor = " VUCE: (".number_format($mCodDat[$k]['comvucxx'],0,'.',',').")";
            }
            if ($mCodDat[$k]['comcerap'] == "SI"){
              $nCantidad = $mCodDat[$k]['comcerxx'];
              // $cValor = " CERTIFICADOS DE ORIGEN: (".number_format($mCodDat[$k]['comcerxx'],0,'.',',').")";
            }
            if ($mCodDat[$k]['comhorap'] == "SI"){
              $nCantidad = $mCodDat[$k]['comhorxx'];
              // $cValor = "HORAS: (".number_format($mCodDat[$k]['comhorxx'],0,'.',',').")";
            }
            if ($mCodDat[$k]['compieap'] == "SI"){
              $nCantidad = $mCodDat[$k]['compiexx'];
              // $cValor = "PIEZAS: (".number_format($mCodDat[$k]['compiexx'],0,'.',',').")";
            }
            if ($mCodDat[$k]['comdexap'] == "SI"){
              $nCantidad = $mCodDat[$k]['comdexxx'];
              // $cValor = " DEX: (".number_format($mCodDat[$k]['comdexxx'],0,'.',',').')';
            }
            if ($mCodDat[$k]['comserap'] == "SI"){
              $nCantidad = $mCodDat[$k]['comserxx'];
              // $cValor = " SERIAL: (".number_format($mCodDat[$k]['comserxx'],0,'.',',').')';
            }
            if ($mCodDat[$k]['comaraap'] == "SI"){
              $nCantidad = $mCodDat[$k]['comaraxx'];
              // $cValor = " CANT.: (".number_format($mCodDat[$k]['comaraxx'],0,'.',',').')';
            }
            if ($mCodDat[$k]['comdtaap'] == "SI"){
              $nCantidad = $mCodDat[$k]['comdtaxx'];
              // $cValor = " DTA: (".number_format($mCodDat[$k]['comdtaxx'],0,'.',',').')';
            }
            if ($mCodDat[$k]['comiteap'] == "SI"){
              $nCantidad = $mCodDat[$k]['comitexx'];
              // $cValor = " ITEMS: (".number_format($mCodDat[$k]['comitexx'],0,'.',',').')';
            }
            if ($mCodDat[$k]['comcanap'] == "SI"){
              $nCantidad = $mCodDat[$k]['comcanxx'];
              // $cValor = " CANTIDAD: (".number_format($mCodDat[$k]['comcanxx'],0,'.',',').')';
            }
            if ($mCodDat[$k]['comfobap'] == "SI"){
              $cValor = " FOB: ($".number_format($mCodDat[$k]['comfob2x'],0,'.',',').')';
            }
            if ($mCodDat[$k]['comc20ap'] == "SI" || $mCodDat[$k]['comc40ap'] == "SI" || $mCodDat[$k]['comcsuap'] == "SI"){
              $cValor = "";
              if($mCodDat[$k]['comc20ap'] == "SI"){
                $cValor .= " CONTENEDORES DE 20: (".number_format($mCodDat[$k]['comc20xx'],0,'.',',').')';
              }
              if($mCodDat[$k]['comc40ap'] == "SI"){
                $cValor .= " CONTENEDORES DE 40: (".number_format($mCodDat[$k]['comc40xx'],0,'.',',').')';
              }
              if ($mCodDat[$k]['comcsuap'] == "SI"){
                $cValor .= " UNIDADES DE CARGA SUELTA: (".number_format($mCodDat[$k]['comcsuxx'],0,'.',',').')';
              }
            }

            $pyy += 4;
            $pdf->SetFont($cEstiloLetra,'',7);
            $pdf->setXY($nPosX+3, $pyy);
            $pdf->Row(array(trim($mCodDat[$k]['comobsxx'].$cValor).( (empty($nCantidad)) ? $nCantidad : ""), '',
                      '$ '.number_format($mCodDat[$k]['comvlrxx'],0,',',',')));

          }//if($mCodDat[$k]['comctocx'] == 'IP'){
        }## for($k=$nPosIP;$k<(count($mCodDat));$k++) { ##

        for($k=0;$k<(count($mCodDat));$k++) {

          if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
            // $pdf->Rect($nPosX-2, $nPosYRec-2, 180, $pyy-($nPosYRec-4));
            $pdf->AddPage();
            $nb++;
            $nPosY = $pdf->nPosY+2;
            $nPosX = 12.5;
            $pyy = $nPosY;
            $nPosYRec = $pyy;
            $pdf->SetFont($cEstiloLetra,'',7);
            $pdf->setXY($nPosX,$nPosY);
          }

          if($mCodDat[$k]['comctocx'] == "IP" && $mCodDat[$k]['comvlr01'] == 0 ) {
            $nPosicion++;
            $nSubToIP += $mCodDat[$k]['comvlrxx'];
            $nSubToIPIva += $mCodDat[$k]['comvlr01'];
            $pdf->SetFont($cEstiloLetra,'',7);

            $cValor = "";
            if($mCodDat[$k]['comfobxx'] == "SI" && $mCodDat[$k]['docfobxx'] > 0) {
              $cValor  = " FOB: ($".number_format($mCodDat[$k]['docfobxx'],2,'.',',');
              $cValor .= ($mCodDat[$k]['doctrmxx'] > 0) ? " TRM: $".number_format($mCodDat[$k]['doctrmxx'],2,'.',',') : "";
              $cValor .= ")";

            }
            if ($mCodDat[$k]['comcifap'] == "SI"){
              $cValor = "CIF: ($".number_format($mCodDat[$k]['comcifxx'],0,'.',',').")";
            }
            $nCantidad = 1;
            if ($mCodDat[$k]['comdimap'] == "SI"){
              $nCantidad = $mCodDat[$k]['comdimxx'];
              // $cValor = "DIM: (".number_format($mCodDat[$k]['comdimxx'],0,'.',',').")";
            }
            if ($mCodDat[$k]['comdavap'] == "SI"){
              $nCantidad = $mCodDat[$k]['comdavxx'];
              // $cValor = "DAV: (".number_format($mCodDat[$k]['comdavxx'],0,'.',',').")";
            }
            if ($mCodDat[$k]['comvucap'] == "SI"){
              $nCantidad = $mCodDat[$k]['comvucxx'];
              // $cValor = " VUCE: (".number_format($mCodDat[$k]['comvucxx'],0,'.',',').")";
            }
            if ($mCodDat[$k]['comcerap'] == "SI"){
              $nCantidad = $mCodDat[$k]['comcerxx'];
              // $cValor = " CERTIFICADOS DE ORIGEN: (".number_format($mCodDat[$k]['comcerxx'],0,'.',',').")";
            }
            if ($mCodDat[$k]['comhorap'] == "SI"){
              $nCantidad = $mCodDat[$k]['comhorxx'];
              // $cValor = "HORAS: (".number_format($mCodDat[$k]['comhorxx'],0,'.',',').")";
            }
            if ($mCodDat[$k]['compieap'] == "SI"){
              $nCantidad = $mCodDat[$k]['compiexx'];
              // $cValor = "PIEZAS: (".number_format($mCodDat[$k]['compiexx'],0,'.',',').")";
            }
            if ($mCodDat[$k]['comdexap'] == "SI"){
              $nCantidad = $mCodDat[$k]['comdexxx'];
              // $cValor = " DEX: (".number_format($mCodDat[$k]['comdexxx'],0,'.',',').')';
            }
            if ($mCodDat[$k]['comserap'] == "SI"){
              $nCantidad = $mCodDat[$k]['comserxx'];
              // $cValor = " SERIAL: (".number_format($mCodDat[$k]['comserxx'],0,'.',',').')';
            }
            if ($mCodDat[$k]['comaraap'] == "SI"){
              $nCantidad = $mCodDat[$k]['comaraxx'];
              // $cValor = " CANT.: (".number_format($mCodDat[$k]['comaraxx'],0,'.',',').')';
            }
            if ($mCodDat[$k]['comdtaap'] == "SI"){
              $nCantidad = $mCodDat[$k]['comdtaxx'];
              // $cValor = " DTA: (".number_format($mCodDat[$k]['comdtaxx'],0,'.',',').')';
            }
            if ($mCodDat[$k]['comiteap'] == "SI"){
              $nCantidad = $mCodDat[$k]['comitexx'];
              // $cValor = " ITEMS: (".number_format($mCodDat[$k]['comitexx'],0,'.',',').')';
            }
            if ($mCodDat[$k]['comcanap'] == "SI"){
              $nCantidad = $mCodDat[$k]['comcanxx'];
              // $cValor = " CANTIDAD: (".number_format($mCodDat[$k]['comcanxx'],0,'.',',').')';
            }
            if ($mCodDat[$k]['comfobap'] == "SI"){
              $cValor = " FOB: ($".number_format($mCodDat[$k]['comfob2x'],0,'.',',').')';
            }
            if ($mCodDat[$k]['comc20ap'] == "SI" || $mCodDat[$k]['comc40ap'] == "SI" || $mCodDat[$k]['comcsuap'] == "SI"){
              $cValor = "";
              if($mCodDat[$k]['comc20ap'] == "SI"){
                $cValor .= " CONTENEDORES DE 20: (".number_format($mCodDat[$k]['comc20xx'],0,'.',',').')';
              }
              if($mCodDat[$k]['comc40ap'] == "SI"){
                $cValor .= " CONTENEDORES DE 40: (".number_format($mCodDat[$k]['comc40xx'],0,'.',',').')';
              }
              if ($mCodDat[$k]['comcsuap'] == "SI"){
                $cValor .= " UNIDADES DE CARGA SUELTA: (".number_format($mCodDat[$k]['comcsuxx'],0,'.',',').')';
              }
            }

            $pyy += 4;
            $pdf->SetFont($cEstiloLetra,'',7);
            $pdf->setXY($nPosX+3, $pyy);
            $pdf->Row(array(trim($mCodDat[$k]['comobsxx'].$cValor).( (empty($nCantidad)) ? $nCantidad : ""), '',
                      '$ '.number_format($mCodDat[$k]['comvlrxx'],0,',',',')));

          }//if($mCodDat[$k]['comctocx'] == 'IP'){
        }## for($k=$nPosIP;$k<(count($mCodDat));$k++) { ##
        /*** Fin Imprimo Ingresos Propios ***/
      }//if(count($mPccIng) > 0 || $nBandIP == 1){//Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS

      /*** Fin Imprimo Detalle de Pagos a Terceros e Ingresos Propios ***/

      $pyy = $pdf->GetY();
      if($pyy > 203){//Validacion para siguiente pagina si se excede espacio de impresion
        // $pdf->Rect($nPosX-2, $nPosYRec-2, 180, $pyy-($nPosYRec-4));
        $pdf->AddPage();
        $nb++;
        $nPosY = $pdf->nPosY+2;
        $nPosX = 12.5;
        $pyy = $nPosY;
        $nPosYRec = $pyy;
        $pdf->SetFont($cEstiloLetra,'',7);
        $pdf->setXY($nPosX,$nPosY);
      }//if($nPosY < 130){//Validacion para siguiente pagina si se excede espacio de impresion

      /*** Busco Valor a Pagar ***/
      for ($k=0;$k<count($mCodDat);$k++) {
        if($mCodDat[$k]['comctocx'] == "SS" || $mCodDat[$k]['comctocx'] == "SC"){
          if($mCodDat[$k]['comctocx'] == "SC"){
            $cSaldo = "A SU FAVOR";
          } else {
            $cSaldo = "A CARGO";
          }
          $nTotPag += $mCodDat[$k]['comvlrxx'];
        }
      }

  
      #SUBTOTAL
      $nSubtotal = $nSubToIP + $nSubToPcc + $nSubTotPcc;

      
      // $nTotIva = 0;
      // ##Busco Valor de RET.IVA ##
      // if($mCodDat[$k]['comctocx'] == 'RETIVA'){
      //   $nTotIva += $mCodDat[$k]['comvlrxx'];
      // }
      // ##Fin Busco Valor de RET.IVA ##

      // $nIva=0;
      // ##Busco valor de IVA ##
      // if($mCodDat[$k]['comctocx'] == 'IVAIP'){
      //   $nIva += $mCodDat[$k]['comvlrxx'];
      // }
      // ##Fin Busco Valor de IVA ##

      $mReteFte = array();
			$mRetIca = array();
			$mRetIva = array();
      $nTotRet = 0;
      for ($k=0;$k<count($mCodDat);$k++) {
      #agrupar por retencion:
        if($mCodDat[$k]['comctocx'] == 'RETFTE'){
          $qPucDat = "SELECT $cAlfa.fpar0115.pucretxx ";
          $qPucDat .= "FROM $cAlfa.fpar0115 ";
          $qPucDat .= "WHERE ";
          $qPucDat .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = \"{$mCodDat[$k]['pucidxxx']}\" LIMIT 0,1 ";
          $xPucDat = mysql_query($qPucDat,$xConexion01);
          if(mysql_num_rows($xPucDat) > 0){
          //echo $qPucDat."~".mysql_num_rows($xPucDat);
            while($xRPD = mysql_fetch_array($xPucDat)){
              $nSwitch_Encontre_Porcentaje = 0;
              for ($j=0;$j<count($mReteFte);$j++) {
                if($mReteFte[$j]['pucretxx'] == $xRPD['pucretxx']){
                  $nSwitch_Encontre_Porcentaje = 1;
                  $mReteFte[$j]['comvlrxx'] += $mCodDat[$k]['comvlrxx'];
                  $mReteFte[$j]['basexxxx'] += $mCodDat[$k]['comvlr01'];
                  $nTotRet += $mCodDat[$k]['comvlrxx'];
                }
              }
              if ($nSwitch_Encontre_Porcentaje == 0) {
                $nInd_mReteFte = count($mReteFte);
                $mReteFte[$nInd_mReteFte]['descripcion'] = "RETENCION FUENTE:";
                $mReteFte[$nInd_mReteFte]['pucretxx'] = $xRPD['pucretxx'];
                $mReteFte[$nInd_mReteFte]['basexxxx'] = $mCodDat[$k]['comvlr01'];
                $mReteFte[$nInd_mReteFte]['comvlrxx'] = $mCodDat[$k]['comvlrxx'];
                $nTotRet += $mCodDat[$k]['comvlrxx'];
              }
            }//while($xRPD = mysql_fetch_array($xPucDat)){
          }//if($nFilPuc > 0){
        }//if($mCodDat[$k]['comctocx'] == 'RETFTE'){

				if($mCodDat[$k]['comctocx'] == 'RETICA'){
					$qPucDat  = "SELECT $cAlfa.fpar0115.pucretxx ";
					$qPucDat .= "FROM $cAlfa.fpar0115 ";
					$qPucDat .= "WHERE ";
					$qPucDat .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = \"{$mCodDat[$k]['pucidxxx']}\" LIMIT 0,1 ";
					$xPucDat  = f_MySql("SELECT","",$qPucDat,$xConexion01,"");
					$nFilPuc  = mysql_num_rows($xPucDat);
					if($nFilPuc > 0){
						//f_Mensaje(__FILE__,__LINE__,$qPucDat);
						while($xRPD = mysql_fetch_array($xPucDat)){
							$nSwitch_Encontre_Porcentaje = 0;
							for ($j=0;$j<count($mRetIca);$j++) {
								if($mRetIca[$j]['pucretxx'] == $xRPD['pucretxx']){
									$nSwitch_Encontre_Porcentaje = 1;
									$mRetIca[$j]['comvlrxx'] += $mCodDat[$k]['comvlrxx'];
									$mRetIca[$j]['basexxxx'] += $mCodDat[$k]['comvlr01'];
								}
							}
							if ($nSwitch_Encontre_Porcentaje == 0) {
								$nInd_mRetIca = count($mRetIca);
								$mRetIca[$nInd_mRetIca]['tipretxx'] = "ICA";
								$mRetIca[$nInd_mRetIca]['pucretxx'] = $xRPD['pucretxx'];
								$mRetIca[$nInd_mRetIca]['basexxxx'] = $mCodDat[$k]['comvlr01'];
								$mRetIca[$nInd_mRetIca]['comvlrxx'] = $mCodDat[$k]['comvlrxx'];
							}
						}//while($xRPD = mysql_fetch_array($xPucDat)){
					}//if($nFilPuc > 0){
				}

				if($mCodDat[$k]['comctocx'] == 'RETIVA'){
					$qPucDat  = "SELECT $cAlfa.fpar0115.pucretxx ";
					$qPucDat .= "FROM $cAlfa.fpar0115 ";
					$qPucDat .= "WHERE ";
					$qPucDat .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = \"{$mCodDat[$k]['pucidxxx']}\" LIMIT 0,1 ";
					$xPucDat  = f_MySql("SELECT","",$qPucDat,$xConexion01,"");
					$nFilPuc  = mysql_num_rows($xPucDat);
					if($nFilPuc > 0){
						//f_Mensaje(__FILE__,__LINE__,$qPucDat);
						while($xRPD = mysql_fetch_array($xPucDat)){
							$nSwitch_Encontre_Porcentaje = 0;
							for ($j=0;$j<count($mRetIva);$j++) {
								if($mRetIva[$j]['pucretxx'] == $xRPD['pucretxx']){
									$nSwitch_Encontre_Porcentaje = 1;
									$mRetIva[$j]['comvlrxx'] += $mCodDat[$k]['comvlrxx'];
									$mRetIva[$j]['basexxxx'] += $mCodDat[$k]['comvlr01'];
								}
							}
							if ($nSwitch_Encontre_Porcentaje == 0) {
								$nInd_mRetIva = count($mRetIva);
								$mRetIva[$nInd_mRetIva]['tipretxx'] = "IVA";
								$mRetIva[$nInd_mRetIva]['pucretxx'] = $xRPD['pucretxx'];
								$mRetIva[$nInd_mRetIva]['basexxxx'] = $mCodDat[$k]['comvlr01'];
								$mRetIva[$nInd_mRetIva]['comvlrxx'] = $mCodDat[$k]['comvlrxx'];
							}
						}//while($xRPD = mysql_fetch_array($xPucDat)){
					}//if($nFilPuc > 0){
				}
      }

      $pdf->setXY($nPosX,209);
      $pdf->SetFont($cEstiloLetra,'B',8);
      $pdf->Cell(43,4,"FAVOR ABSTENERSE DE APLICAR RETENCION SOBRE LOS PAGOS A TERCEROS",0,0,'L');

      #TOTAL
      // $nTotPag = $nSubtotal - $nTotAnt + $nSubToIPIva - $nTotRet;

      /*** Fin Busco Valor a Pagar ***/
      $pdf->setXY($nPosX,215);
      $pdf->SetFont($cEstiloLetra,'B',7);
      $pdf->Cell(43,4,"SON:",0,0,'L');
      $pdf->SetFont($cEstiloLetra,'B',7);
      $nTotPag1 = f_Cifra_Php(str_replace("-","",abs($nTotPag)),'PESO');
      if($nTotPag1 <> ""){
        $nImpValor = explode("~",f_Words($nTotPag1,136));
        $pyy = 215;
        for ($n=0;$n<count($nImpValor);$n++) {
          $pdf->setXY(20,$pyy);
          $pdf->Cell(135,4,$nImpValor[$n],0,0,'L');
          $pyy += 4;
        }
      }else{
        $pdf->setXY(20,215);
        $pdf->Cell(135,4,"",0,0,'L');
      }

      $nPosX = $nPosX+140;
      $pyy = 204;

      $pdf->setXY($nPosX,$pyy);
      $pdf->SetFont($cEstiloLetra,'',7);
      $pdf->Cell(25,4,"SUBTOTAL",1,0,'L');
      $pdf->Cell(25,4,"",1,0,'R');
      $pdf->setXY($nPosX+25,$pyy);
      $pdf->Cell(22,4,"$ ".number_format($nSubtotal,0,',',','),0,0,'R');
      ////////////////////////////////////
      $pyy += 4;
      $pdf->setXY($nPosX,$pyy);
      $pdf->SetFont($cEstiloLetra,'',7);
      $pdf->Cell(25,4,"ANTICIPOS",1,0,'L');
      $pdf->Cell(25,4,"",1,0,'R');
      $pdf->setXY($nPosX+25,$pyy);
      $pdf->Cell(22,4,"$ ".number_format(abs($nTotAnt),0,',',','),0,0,'R');

      ////////////////////////////////////
      $pyy += 4;
      $pdf->setXY($nPosX,$pyy);
      $pdf->SetFont($cEstiloLetra,'',7);
      $pdf->Cell(25,4,"IVA 19%",1,0,'L');
      $pdf->Cell(25,4,"",1,0,'R');
      $pdf->setXY($nPosX+25,$pyy);
      $pdf->Cell(22,4,"$ ".number_format($nSubToIPIva,0,',',','),0,0,'R');

      $nRetFte4 = 0;
      $nRetFte11 = 0;
      foreach ($mReteFte as  $mRetencion) {
        if ((int)$mRetencion['pucretxx'] == 11) {
          $nRetFte11 = $mRetencion['comvlrxx'];
        } else if ((int)$mRetencion['pucretxx'] == 4) {
          $nRetFte4 = $mRetencion['comvlrxx'];
        }
      }

			$nRetIca9 = 0;
      $nRetIca6 = 0;
			foreach ($mRetIca as  $reteIca) {
        if ($reteIca['pucretxx'] == '0.966') {
          $nRetIca9 += $reteIca['comvlrxx'];
        } else if ($reteIca['pucretxx'] == '0.690') {
          $nRetIca6 += $reteIca['comvlrxx'];
        }
      }

			$nRetIva = 0;
			foreach ($mRetIva as  $mReteIva) {
				$nRetIva += $mReteIva['comvlrxx'];
      }

      ////////////////////////////////////
      $pyy += 4;
      $pdf->setXY($nPosX,$pyy);
      $pdf->SetFont($cEstiloLetra,'',7);
      $pdf->Cell(25,4,"Retefuente 4%",1,0,'L');
      $pdf->Cell(25,4,"",1,0,'R');
      $pdf->setXY($nPosX+25,$pyy);
      $pdf->Cell(22,4,"$ ".number_format($nRetFte4,0,',',','),0,0,'R');

      ////////////////////////////////////
      $pyy += 4;
      $pdf->setXY($nPosX,$pyy);
      $pdf->SetFont($cEstiloLetra,'',7);
      $pdf->Cell(25,4,"Retefuente 11%",1,0,'L');
      $pdf->Cell(25,4,"",1,0,'R');
      $pdf->setXY($nPosX+25,$pyy);
      $pdf->Cell(22,4,"$ ".number_format($nRetFte11,0,',',','),0,0,'R');

			////////////////////////////////////
      $pyy += 4;
      $pdf->setXY($nPosX,$pyy);
      $pdf->SetFont($cEstiloLetra,'',7);
      $pdf->Cell(25,4,"ReteICA 9,66%",1,0,'L');
      $pdf->Cell(25,4,"",1,0,'R');
      $pdf->setXY($nPosX+25,$pyy);
      $pdf->Cell(22,4,"$ ".number_format($nRetIca9,0,',',','),0,0,'R');

			////////////////////////////////////
      $pyy += 4;
      $pdf->setXY($nPosX,$pyy);
      $pdf->SetFont($cEstiloLetra,'',7);
      $pdf->Cell(25,4,"ReteICA 6,9%",1,0,'L');
      $pdf->Cell(25,4,"",1,0,'R');
      $pdf->setXY($nPosX+25,$pyy);
      $pdf->Cell(22,4,"$ ".number_format($nRetIca6,0,',',','),0,0,'R');

      ////////////////////////////////////
      $pyy += 4;
      $pdf->setXY($nPosX,$pyy);
      $pdf->SetFont($cEstiloLetra,'',7);
      $pdf->Cell(25,4,"ReteIVA",1,0,'L');
      $pdf->Cell(25,4,"",1,0,'R');
      $pdf->setXY($nPosX+25,$pyy);
      $pdf->Cell(22,4,"$ ".number_format($nRetIva,0,',',','),0,0,'R');

      ////////////////////////////////////
      $pyy += 4;
      $pdf->setXY($nPosX,$pyy);
      $pdf->SetFont($cEstiloLetra,'B',7);
      $pdf->Cell(25,4,"TOTAL ".$cSaldo,1,0,'C');
      $pdf->Cell(25,4,"",1,0,'R');
      $pdf->setXY($nPosX+25,$pyy);
      $pdf->Cell(22,4,"$ ".number_format($nTotPag,0,',',','),0,0,'R');
    }//for($y=1; $y<=2; $y++){

    $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
    $pdf->Output($cFile);
    // $pdf->Output(); // DESARROLLO

    if (file_exists($cFile)){
      chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
    } else {
      f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
    }
    echo "<html><script>document.location='$cFile';</script></html>";
  }

?>

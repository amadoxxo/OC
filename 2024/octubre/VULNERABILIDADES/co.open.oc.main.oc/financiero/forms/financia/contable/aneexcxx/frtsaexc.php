<?php
  namespace openComex;
  /**
   * Imprime Anexo de Factura -Pagos a Terceros Servicios y Anticipos .
   * --- Descripcion: Permite Imprimir Anexo de Factura Pagos a Terceros Servicios y Anticipos en Excel.
   * @author Yulieth Campos <ycampos@opentecnologia.com.co>
   * @version 002
   */
  include("../../../../libs/php/utility.php");

  global $xConexion01; global $cAlfa;
  //$cNewYear = substr($gDesde,0,4);
  //$cNewYear = date('Y');
  
  $vBuscar = array(chr(13),chr(10),chr(27),chr(9),chr(59));
  $vReempl = array(" "," "," "," "," ");

  $nFecIni = substr($gDesde,0,4);
  $nFecFin = substr($gHasta,0,4);
  $mCocDat = array();
  for($cNewYear=$nFecIni;$cNewYear<=$nFecFin;$cNewYear++){
    /***** CABECERA 1001 *****/
    $qCocDat  = "SELECT ";
    $qCocDat .= "$cAlfa.fcoc$cNewYear.*, ";
    $qCocDat .= "IF($cAlfa.fpar0008.sucidxxx <> \"\",$cAlfa.fpar0008.sucidxxx,\"SUCURSAL SIN ID\") AS sucidxxx, ";
    $qCocDat .= "IF($cAlfa.fpar0008.sucdesxx <> \"\",$cAlfa.fpar0008.sucdesxx,\"SUCURSAL SIN DESCRIPCION\") AS sucdesxx, ";
    $qCocDat .= "(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))) AS CLINOMXX, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.CLIDIRXX <> \"\",$cAlfa.SIAI0150.CLIDIRXX,\"SIN DIRECCION\") AS CLIDIRXX, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.CLITELXX <> \"\",$cAlfa.SIAI0150.CLITELXX,\"SIN TELEFONO\") AS CLITELXX, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.CLIFAXXX <> \"\",$cAlfa.SIAI0150.CLIFAXXX,\"SIN TELEFONO\") AS CLIFAXXX, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.CLICONTX <> \"\",$cAlfa.SIAI0150.CLICONTX,\"SIN RESPONSABLE\") AS CLICONTX, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.PAIIDXXX <> \"\",$cAlfa.SIAI0150.PAIIDXXX,\"\") AS PAIIDXXX, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.DEPIDXXX <> \"\",$cAlfa.SIAI0150.DEPIDXXX,\"\") AS DEPIDXXX, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.CIUIDXXX <> \"\",$cAlfa.SIAI0150.CIUIDXXX,\"\") AS CIUIDXXX ";
    $qCocDat .= "FROM $cAlfa.fcoc$cNewYear ";
    $qCocDat .= "LEFT JOIN $cAlfa.fpar0008 ON $cAlfa.fcoc$cNewYear.ccoidxxx = $cAlfa.fpar0008.ccoidxxx ";
    $qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcoc$cNewYear.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
    $qCocDat .= "WHERE $cAlfa.fcoc$cNewYear.comidxxx = \"F\" AND ";
    // $qCocDat .= "$cAlfa.fcoc$cNewYear.comcodxx = \"302\" AND ";
    // $qCocDat .= "$cAlfa.fcoc$cNewYear.comcscxx = \"15502\" AND ";
    if ($gTerId != "") {
      $qCocDat .= "$cAlfa.fcoc$cNewYear.teridxxx LIKE \"$gTerId\" AND ";
    }
    if ($gSucId != "") {
      $qCocDat .= "$cAlfa.fcoc$cNewYear.ccoidxxx LIKE \"$gSucId\" AND ";
    }
    if ($gComCsc != "") {
      $qCocDat .= "$cAlfa.fcoc$cNewYear.comcscxx LIKE \"$gComCsc\" AND ";
    }
    $qCocDat .= "$cAlfa.fcoc$cNewYear.regfcrex BETWEEN  \"$gDesde\" AND \"$gHasta\" AND ";
    $qCocDat .= "$cAlfa.fcoc$cNewYear.regestxx =\"ACTIVO\" ORDER BY $cAlfa.fcoc$cNewYear.teridxxx, $cAlfa.fcoc$cNewYear.comcscxx ";
    //f_Mensaje(__FILE__,__LINE__,$qCocDat);
    $xCocDat  = f_MySql("SELECT","",$qCocDat,$xConexion01,"");
    $nFilCoc  = mysql_num_rows($xCocDat);
    if ($nFilCoc > 0) {
      $n4xmil = 0;
      while($xRCD = mysql_fetch_array($xCocDat)){
        /**
         * Si el valor del 4xmil del campo comifxxx es diferente a la sumatoria del 4xmil de todos los DO del campo comfpxxx
         * Debe calcularse nuevamente el 4xmil de los pagos a terceros por DO
         */
        $nCanDo = 0; $n4xmil = 0;
        $mComFp = f_Explode_Array($xRCD['comfpxxx'],"|","~");
        for($i=0;$i<count($mComFp);$i++){
          if ($mComFp[$i][0] != "") {
            $nCanDo++;
            if($mComFp[$i][18] !="" && $mComFp[$i][18] != 0){
              $n4xmil += ($mComFp[$i][18]+0);
            }
          }
        }

        if (round($n4xmil) != ($xRCD['comifxxx']+0) && ($xRCD['comifxxx']+0) > 0) { 
          $mComFp = f_Explode_Array($xRCD['comfpxxx'],"|","~");
          $cComFp = "";
          for($i=0;$i<count($mComFp);$i++){
            if ($nCanDo == 1) {
              $mComFp[$i][18] = $xRCD['comifxxx']+0;
            } else {
              $mComFp[$i][18] = 0;
              //Calcular el 4xmil de los pagos a terceros por DO
              $mPCC = f_Explode_Array($xRCD['commemod'],"|","~");
              $n4xmil = 0;
              for($nPCC=0;$nPCC<count($mPCC);$nPCC++) {
                if($mPCC[$nPCC][14] == $mComFp[$i][15]."-".$mComFp[$i][2]."-".$mComFp[$i][3]) {
                  $n4xmil += $mPCC[$nPCC][7] * $vSysStr['financiero_porcentaje_impuesto_financiero'];
                }
              }
              $mComFp[$i][18] = round($n4xmil);
            }
            $cComFp .= implode("~", $mComFp[$i])."|";
          }
          $xRCD['comfpxxx'] = "|".$cComFp;
        }

        /*****CONSULTA A LA 1002 *****/
        $nInd_mCocDat = count($mCocDat);
        $mCocDat[$nInd_mCocDat]= $xRCD;
        $qCodDat  = "SELECT DISTINCT ";
        $qCodDat .= "$cAlfa.fcod$cNewYear.*, ";
        $qCodDat .= "IF($cAlfa.fpar0008.sucdesxx <> \"\",$cAlfa.fpar0008.sucdesxx,\"SUCURSAL SIN DESCRIPCION\") AS sucdesxx, ";
        $qCodDat .= "(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))) AS CLINOMXX ";
        $qCodDat .= "FROM $cAlfa.fcod$cNewYear ";
        $qCodDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcod$cNewYear.terid2xx = $cAlfa.SIAI0150.CLIIDXXX ";
        $qCodDat .= "LEFT JOIN $cAlfa.fpar0008 ON $cAlfa.fcod$cNewYear.ccoidxxx = $cAlfa.fpar0008.ccoidxxx ";
        $qCodDat .= "WHERE "; 
        $qCodDat .= "$cAlfa.fcod$cNewYear.comidxxx = \"{$xRCD['comidxxx']}\" AND ";
        $qCodDat .= "$cAlfa.fcod$cNewYear.comcodxx = \"{$xRCD['comcodxx']}\" AND ";
        $qCodDat .= "$cAlfa.fcod$cNewYear.comcscxx = \"{$xRCD['comcscxx']}\" AND ";
        $qCodDat .= "$cAlfa.fcod$cNewYear.comcsc2x = \"{$xRCD['comcsc2x']}\" ORDER BY ABS($cAlfa.fcod$cNewYear.comseqxx) ASC ";
        $xCodDat  = f_MySql("SELECT","",$qCodDat,$xConexion01,"");
        $nFilCod  = mysql_num_rows($xCodDat);
        if ($nFilCod > 0) {
          while ($xRCDo = mysql_fetch_array($xCodDat)) {
            $nInd_mCodDat = count($mCodDat);
            $mCodDat[$nInd_mCodDat]= $xRCDo;
            $mCodDat[$nInd_mCodDat]['comfpxxx'] = $xRCD['comfpxxx'];          
          }
        }
      }
    }
  }

  /**
   *  
   */
  $mDoiId    = array();
  for($a=0;$a<count($mCocDat);$a++){
    $mDo = f_Explode_Array($mCocDat[$a]['commemod'],"|","~");
    $cFactura = $mCocDat[$a]['comidxxx']."-".$mCocDat[$a]['comcodxx']."-".$mCocDat[$a]['comcscxx']."-".$mCocDat[$a]['comcsc2x'];
    for($i=0;$i<count($mDo);$i++) {
      if($mDo[$i][14] != "") {
        $mDo[$i][101] = $cFactura;
        $nSwitch_Encontre_Do = 0;
        for($j=0;$j<count($mDoiId);$j++) {
          if($mDoiId[$j][101] == $mDo[$i][101]) {
            if($mDoiId[$j][14] == $mDo[$i][14]) {
              if($mDoiId[$j][1] == $mDo[$i][1]){
              $nSwitch_Encontre_Do = 1;
                $mDoiId[$j][7] += $mDo[$i][7]; // Acumulo el valor de ingreso para tercero.
                $mDoiId[$j][100] = ((strlen($mDoiId[$j][100]) + strlen($mDo[$i][5]) + 1) < 16) ? $mDoiId[$j][100]."/".$mDo[$i][5] : $mDoiId[$j][100];
                $mDoiId[$j][100] = (substr($mDoiId[$j][100],0,1) == "/") ? substr($mDoiId[$j][100],1,strlen($mDoiId[$j][100])) : $mDoiId[$j][100];
              }
            }
          }
        }
        if($nSwitch_Encontre_Do == 0) { // No encontre el ingreso para tercero en la matrix $mDoiId
          $nInd_mDoiId = count($mDoiId);
          $mDoiId[$nInd_mDoiId] = $mDo[$i]; // Ingreso el registro como nuevo.
          $mDoiId[$nInd_mDoiId][100] = ((strlen($mDoiId[$nInd_mDoiId][100]) + strlen($mDo[$i][5]) + 1) < 16) ? $mDoiId[$nInd_mDoiId][100]."/".$mDo[$i][5] : $mDoiId[$nInd_mDoiId][100];
          $mDoiId[$nInd_mDoiId][100] = (substr($mDoiId[$nInd_mDoiId][100],0,1) == "/") ? substr($mDoiId[$nInd_mDoiId][100],1,strlen($mDoiId[$nInd_mDoiId][100])) : $mDoiId[$nInd_mDoiId][100];
          $mDoiId[$nInd_mDoiId][101] = $cFactura;
        }
      }
    }
  }

  $mConcepto = array();
  for($a=0;$a<count($mCocDat);$a++){
    $mDo = f_Explode_Array($mCocDat[$a]['commemod'],"|","~");
    for ($i=0;$i<count($mDo);$i++) {
      if ($mDo[$i][14] != "") {
        $nSwitch_Encontre_Concepto = 0;
        for ($j=0;$j<count($mConcepto);$j++) {
          if($mConcepto[$j]['cCtoId'] == $mDo[$i][1]){
            $nSwitch_Encontre_Concepto = 1;
          }
        }
        if ($nSwitch_Encontre_Concepto == 0) { // No encontre el ingreso para tercero en la matrix $mDoiId
          $nInd_mConcepto = count($mConcepto);
          $mConcepto[$nInd_mConcepto]['cCtoId']  = $mDo[$i][1];
          $mConcepto[$nInd_mConcepto]['cCtoDes'] = $mDo[$i][2];
        }
      }
    }
  }

  /***** Cargo Matriz con Do's*****/
  $mMatrizDo = array();
  for($a=0;$a<count($mCocDat);$a++){
    $mDo = f_Explode_Array($mCocDat[$a]['commemod'],"|","~");
    $cFac = $mCocDat[$a]['comidxxx']."-".$mCocDat[$a]['comcodxx']."-".$mCocDat[$a]['comcscxx']."-".$mCocDat[$a]['comcsc2x'];
    for($k=0;$k<count($mDo);$k++) {
      if($mDo[$k][14] != "") {
        $nFacDo = $mCocDat[$a]['comidxxx']."-".$mCocDat[$a]['comcodxx']."-".$mCocDat[$a]['comcscxx']."-".$mCocDat[$a]['comcsc2x'];
        $nSwitch_Encontre_Doi = 0;
        for($l=0;$l<count($mMatrizDo);$l++) {
          if($mMatrizDo[$l][0] == $mDo[$k][14]) {
            if($mMatrizDo[$l][4] == $nFacDo){
              $nSwitch_Encontre_Doi = 1;
            }
          }
        }
        if($nSwitch_Encontre_Doi == 0) { // No encontre el ingreso para tercero en la matrix $mDoiId
          $nInd_mMatrizDo = count($mMatrizDo);
          $mMatrizDo[$nInd_mMatrizDo][0]  = $mDo[$k][14];
          $mMatrizDo[$nInd_mMatrizDo][1]  = $mCocDat[$a]['CLINOMXX'];
          $mMatrizDo[$nInd_mMatrizDo][2]  = $mCocDat[$a]['terid2xx'];
          $mMatrizDo[$nInd_mMatrizDo][3]  = $mCocDat[$a]['sucdesxx'];
          $mMatrizDo[$nInd_mMatrizDo][4]  = $cFac;
          $mMatrizDo[$nInd_mMatrizDo][5]  = $mCocDat[$a]['comfpxxx'];
        }
      }
    }
  }
  /*****Fin de Carga Matriz de Do's *****/

  /***** Carga Matriz con Conceptos Ingresos Propios *****/
  $mMatrizIP = array();
  for($i=0;$i<count($mCodDat);$i++){
    if($mCodDat[$i]['comctocx'] == "IP"){
      $nSwitch_Encontre_IP = 0;
      for($k=0;$k<count($mMatrizIP);$k++){
        if($mMatrizIP[$k]['ctoidxxx'] == $mCodDat[$i]['ctoidxxx']) {
          $nSwitch_Encontre_IP = 1;
        }
      }

      if($nSwitch_Encontre_IP == 0) { // No encontre el Ingreso para Tercero en la Matrix $mDoiId
        $nInd_mMatrizIP = count($mMatrizIP);
        $mMatrizIP[$nInd_mMatrizIP]['ctoidxxx']  = $mCodDat[$i]['ctoidxxx'];
        $mComObs_IP = f_Explode_Array($mCodDat[$i]['comobsxx'],"|","~");
        $nIP = 0;
        if(count($mComObs_IP) > 0){
          for($p=0;$p<count($mComObs_IP);$p++){
            if($mComObs_IP[$p][2] != ""){
              $nComObs_IP = stripos($mComObs_IP[$p][2], "[");
              if($nComObs_IP > 0){
                $mMatrizIP[$nInd_mMatrizIP]['ctodesxx']  = substr($mComObs_IP[$p][2],0,$nComObs_IP);
              }else{
                $mMatrizIP[$nInd_mMatrizIP]['ctodesxx']  = $mComObs_IP[$p][2];
              }
              $nIP++;
            }
          }
        }
        if($nIP == 0){
          $nComObs_IP = stripos($mCodDat[$i]['comobsxx'], "[");
          if($nComObs_IP > 0){
            $mMatrizIP[$nInd_mMatrizIP]['ctodesxx']  = substr($mComObs_IP[$p][2],0,$nComObs_IP);
          }else{
            $mMatrizIP[$nInd_mMatrizIP]['ctodesxx']  = $mComObs_IP[$p][2];
          }
        }
      }
    }
  }
  /***** Fin Matriz Conceptos Ingresos Propios *****/

  /***** Cargo Matriz Concepto PCC de la 1002 *****/
  $mMatrizPCC = array();
  for($i=0;$i<count($mCodDat);$i++){
    if($mCodDat[$i]['comctocx'] == "PCC" && $mCodDat[$i]['comtraxx'] != ""){
      $nSwitch_Encontre_PCC = 0;
      for($k=0;$k<count($mMatrizPCC);$k++){
        if($mMatrizPCC[$k] == $mCodDat[$i]['ctoidxxx']) {
          $nSwitch_Encontre_PCC = 1;
        }
      }
      if($nSwitch_Encontre_PCC == 0) { // No encontre el ingreso para tercero en la matrix $mDoiId
        $nInd_mMatrizPCC = count($mMatrizPCC);
        $mMatrizPCC[$nInd_mMatrizPCC]  = $mCodDat[$i]['ctoidxxx'];
      }
    }
  }
  /***** Fin Matriz Concepto PCC de la 1002 *****/

  /***** Busco si en la 1002 hay Do's diferentes a los del campo commemod de la 1001 *****/
  for($i=0;$i<count($mCodDat);$i++){
    if($mCodDat[$i]['comctocx'] == "PCC" && $mCodDat[$i]['comtraxx'] != ""){
      $cDoPCC = $mCodDat[$i]['comidxxx']."-".$mCodDat[$i]['comcodxx']."-".$mCodDat[$i]['comcscxx']."-".$mCodDat[$i]['comcsc2x'];
      $nSwitch_Encontre_Doi = 0;
      for($k=0;$k<count($mMatrizDo);$k++){
        if($mMatrizDo[$k][0] == $mCodDat[$i]['comtraxx']) {
          if($mMatrizDo[$k][4] == $cDoPCC){
            $nSwitch_Encontre_Doi = 1;
          }
        }
      }
      if($nSwitch_Encontre_Doi == 0) { // No encontre el Do en la Matriz $mMatrizDo
        $nInd_mMatrizDo = count($mMatrizDo);
        $mMatrizDo[$nInd_mMatrizDo][0]  = $mCodDat[$i]['comtraxx'];
        $mMatrizDo[$nInd_mMatrizDo][1]  = $mCodDat[$i]['CLINOMXX'];
        $mMatrizDo[$nInd_mMatrizDo][2]  = $mCodDat[$i]['terid2xx'];
        $mMatrizDo[$nInd_mMatrizDo][3]  = $mCodDat[$i]['sucdesxx'];
        $mMatrizDo[$nInd_mMatrizDo][4]  = $mCodDat[$i]['comidxxx']."-".$mCodDat[$i]['comcodxx']."-".$mCodDat[$i]['comcscxx']."-".$mCodDat[$i]['comcsc2x'];
        $mMatrizDo[$nInd_mMatrizDo][5]  = $mCodDat[$i]['comfpxxx'];
      }
    }
  }
  /***** Fin busqueda de Do's *****/

  /*****Cargo conceptos de ingresos propios de 1002  *****/
  for($i=0;$i<count($mCodDat);$i++){
    if($mCodDat[$i]['comctocx'] == "IP" && $mCodDat[$i]['comtraxx'] != ""){
      $nSwitch_Encontre_Doi = 0;
      $cDoIP = $mCodDat[$i]['comidxxx']."-".$mCodDat[$i]['comcodxx']."-".$mCodDat[$i]['comcscxx']."-".$mCodDat[$i]['comcsc2x'];
      for($k=0;$k<count($mMatrizDo);$k++){
        if($mMatrizDo[$k][0] == $mCodDat[$i]['comtraxx']) {
          if($mMatrizDo[$k][4] == $cDoIP){
            $nSwitch_Encontre_Doi = 1;
          }
        }
      }
      if($nSwitch_Encontre_Doi == 0) { // No encontre el ingreso para tercero en la matriz $mMatrizDo
        $nInd_mMatrizDo = count($mMatrizDo);
        $mMatrizDo[$nInd_mMatrizDo][0]  = $mCodDat[$i]['comtraxx'];
        $mMatrizDo[$nInd_mMatrizDo][1]  = $mCodDat[$i]['CLINOMXX'];
        $mMatrizDo[$nInd_mMatrizDo][2]  = $mCodDat[$i]['terid2xx'];
        $mMatrizDo[$nInd_mMatrizDo][3]  = $mCodDat[$i]['sucdesxx'];
        $mMatrizDo[$nInd_mMatrizDo][4]  = $mCodDat[$i]['comidxxx']."-".$mCodDat[$i]['comcodxx']."-".$mCodDat[$i]['comcscxx']."-".$mCodDat[$i]['comcsc2x'];
        $mMatrizDo[$nInd_mMatrizDo][5]  = $mCodDat[$i]['comfpxxx'];
      }
    }
  }

  $header = '';
  $header .= 'ANEXO DE FACTURA - PAGOS A TERCEROS Y SERVICIOS'."\n";
  $header .= "\n";
  $data = '';
  $title = "Anexo Factura.xls";


  $atitles = array("REFERENCIA","FACTURA ALPOPULAR","SUCURSAL","PEDIDO","DOCUMENTO DE TRANSPORTE","PIEZAS EMBALAJE","FACTURA COMERCIAL","FECHA");
  for($i=0;$i<count($mConcepto);$i++){
    $mCtoId = explode("^",$mConcepto[$i]['cCtoDes']);
    if($mCtoId[0] != ""){
      $atitles[count($atitles)] = $mCtoId[0];
    }
  }

  for($m=0;$m<count($mCocDat);$m++){
    $nCont  = 0;
    //$nContI = 0;
    $mComFp = f_Explode_Array($mCocDat[$m]['comfpxxx'],"|","~");
    for($i=0;$i<count($mComFp);$i++){
  
      $cDoiId  = $mComFp[$i][2];
      $cDocSuf = $mComFp[$i][3];
      $cSucId  = $mComFp[$i][15];
  
      $qDceDat  = "SELECT doctipxx ";
      $qDceDat .= "FROM $cAlfa.sys00121 ";
      $qDceDat .= "WHERE ";
      $qDceDat .= "sucidxxx = \"$cSucId\" AND ";
      $qDceDat .= "docidxxx = \"$cDoiId\" AND ";
      $qDceDat .= "docsufxx = \"$cDocSuf\" LIMIT 0,1 ";
      $xDceDat  = f_MySql("SELECT","",$qDceDat,$xConexion01,"");
      $vDceDat = mysql_fetch_array($xDceDat);
      // f_Mensaje(__FILE__,__LINE__,$qDceDat."~".mysql_num_rows($xDceDat));
  
      if($vDceDat['doctipxx'] != "TRANSPORTE"){
        if($mComFp[$i][18] !="" && $mComFp[$i][18] != 0){
          $nCont = 1;
        }
  
        if($nCont == 1){
          $i = count($mComFp);
          $m = count($mCocDat);
        }
      }
    }
  }

  if($nCont == 1){
    $qCtoDat  = "SELECT * ";
    $qCtoDat .= "FROM $cAlfa.fpar0119 ";
    $qCtoDat .= "WHERE ";
    $qCtoDat .= "$cAlfa.fpar0119.ctoclaxf = \"IMPUESTOFINANCIERO\" AND ";
    $qCtoDat .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
    $xCtoDat  = f_MySql("SELECT","",$qCtoDat,$xConexion01,"");
    $nFilCto  = mysql_num_rows($xCtoDat);
    if ($nFilCto > 0) {
      $vCtoDat = mysql_fetch_array($xCtoDat);
    }
    $atitles[count($atitles)] = $vCtoDat['ctodesxf'];
  }

  for($i=0;$i<count($mMatrizPCC);$i++){
    $atitles[count($atitles)] = $mMatrizPCC[$i]['cCtoDes'];
  }

  for($i=0;$i<count($mMatrizIP);$i++){
    $atitles[count($atitles)] = $mMatrizIP[$i]['ctodesxx'];
  }

  for($m=0;$m<count($mCocDat);$m++){
    $nContI  = 0;
    //$nContI = 0;
    $mComFp = f_Explode_Array($mCocDat[$m]['comfpxxx'],"|","~");
    for($i=0;$i<count($mComFp);$i++){
      if($mComFp[$i][20] !="" && $mComFp[$i][20] != 0){
        $nContI = 1;
      }
      if($nContI == 1){
        $i = count($mComFp);
        $m = count($mCocDat);
      }
    }
  }

  if($nContI == 1){
    $qCtoDatI  = "SELECT * ";
    $qCtoDatI .= "FROM $cAlfa.fpar0119 ";
    $qCtoDatI .= "WHERE ";
    $qCtoDatI .= "$cAlfa.fpar0119.ctoclaxf = \"IVAIP\" AND ";
    $qCtoDatI .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
    $xCtoDatI  = f_MySql("SELECT","",$qCtoDatI,$xConexion01,"");
    //f_Mensaje(__FILE__,__LINE__,$qCtoDatI);
    $nFilCtoI  = mysql_num_rows($xCtoDatI);
    if ($nFilCtoI > 0) {
      $vCtoDatI = mysql_fetch_array($xCtoDatI);
    }
    $atitles[count($atitles)] = $vCtoDatI['ctodesxf'];
  }

  $atitles[count($atitles)] = "TOTAL POR DO";

  for($i=0;$i<count($atitles);$i++) {
    $header .= $atitles[$i]."\t";
  }

  // echo "<pre>";
  // print_r($atitles);
  // echo "</pre>";

  /***** Armo Matriz para pasar datos a Excel *****/
  $mMatrizExc = array();
  for($k=0;$k<count($mMatrizDo);$k++){

    /*****Traigo Datos del Do *****/
    $mDoiDat = explode("-",$mMatrizDo[$k][0]);
    /*****Traigo el tipo de Operacion de los Do's que se van a pintar en en excel *****/
    $qDceDat  = "SELECT * ";
    $qDceDat .= "FROM $cAlfa.sys00121 ";
    $qDceDat .= "WHERE ";
    $qDceDat .= "$cAlfa.sys00121.sucidxxx = \"{$mDoiDat[0]}\" AND ";
    $qDceDat .= "$cAlfa.sys00121.docidxxx = \"{$mDoiDat[1]}\" AND ";
    $qDceDat .= "$cAlfa.sys00121.docsufxx = \"{$mDoiDat[2]}\" ";
    $xDceDat  = f_MySql("SELECT","",$qDceDat,$xConexion01,"");
    $nFilDce  = mysql_num_rows($xDceDat);
    if ($nFilDce > 0) {
      $vDceDat  = mysql_fetch_array($xDceDat);
    }
    switch($vDceDat['doctipxx']){//Dependiendo del tipo de operacion del Tramite se deben hacer select's a tablas diferentes para los datos de Doc. Transporte, Pedido, Factura Comercial
      case "IMPORTACION":
        $qDoiDat  = "SELECT * ";
        $qDoiDat .= "FROM $cAlfa.SIAI0200 ";
        $qDoiDat .= "WHERE $cAlfa.SIAI0200.DOIIDXXX = \"{$mDoiDat[1]}\" AND ";
        $qDoiDat .= "$cAlfa.SIAI0200.DOISFIDX = \"{$mDoiDat[2]}\" AND ";
        $qDoiDat .= "$cAlfa.SIAI0200.ADMIDXXX = \"{$mDoiDat[0]}\" ";
        $xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
        $nFilDoi  = mysql_num_rows($xDoiDat);
        if ($nFilDoi > 0) {
          $vDoiDat  = mysql_fetch_array($xDoiDat);
        }
        /***** Fin Consulta SIAI0200 CONTROL DO *****/

        /*****Traigo todas las Facturas de este Do  *****/
        $qFacDat  = "SELECT * ";
        $qFacDat .= "FROM $cAlfa.SIAI0204 ";
        $qFacDat .= "WHERE ";
        $qFacDat .= "$cAlfa.SIAI0204.DOIIDXXX = \"{$vDoiDat['DOIIDXXX']}\" AND ";
        $qFacDat .= "$cAlfa.SIAI0204.DOISFIDX = \"{$vDoiDat['DOISFIDX']}\" AND ";
        $qFacDat .= "$cAlfa.SIAI0204.ADMIDXXX = \"{$vDoiDat['ADMIDXXX']}\" ";
        $qFacDat .= "ORDER BY $cAlfa.SIAI0204.FACIDXXX ";
        $xFacDat  = f_MySql("SELECT","",$qFacDat,$xConexion01,"");
        $x = 0;
        while($xRFD = mysql_fetch_array($xFacDat)){
          if($x == 0){
            $cFactura = $xRFD['FACIDXXX'];
            $cFecFac  = $xRFD['FACFECXX'];
            $x++;
          }else{
            $cFacCom .= "/".$xRFD['FACIDXXX'];
            $cFecFac  .= "/". $xRFD['FACFECXX'];
          }
        }
        /*****Fin Traigo todas las Facturas de este Do *****/
        /*****Cargo variables  *****/
        $cPedido = $vDoiDat['DOIPEDXX'];
        $cDocTra = $vDoiDat['DGEDTXXX'];
        $cPieEmb = $vDoiDat['TEMIDXXX'];
        /*****Fin Cargo Variables *****/
      break;
      case "EXPORTACION":
        /*****Consulto Datos de Do en Exportaciones tabla siae0199 *****/
        $qDexDat  = "SELECT * ";
        $qDexDat .= "FROM $cAlfa.siae0199 ";
        $qDexDat .= "WHERE ";
        $qDexDat .= "$cAlfa.siae0199.dexidxxx = \"$mDoiDat[1]\" AND ";
        $qDexDat .= "$cAlfa.siae0199.admidxxx = \"$mDoiDat[0]\" ";
        $xDexDat  = f_MySql("SELECT","",$qDexDat,$xConexion01,"");
        $nFilDex  = mysql_num_rows($xDexDat);
        if ($nFilDex > 0) {
          $vDexDat = mysql_fetch_array($xDexDat);
        }
        /*****Traigo Valor fob, peso neto, peso bruto, bultos de la tabla de Items de Exportaciones siae0201 *****/
        $qIteDat  = "SELECT ";
        $qIteDat .= "SUM($cAlfa.siae0201.itefobxx) AS itefobxx, ";
        $qIteDat .= "SUM($cAlfa.siae0201.itepbrxx) AS itepbrxx, ";
        $qIteDat .= "SUM($cAlfa.siae0201.itepnexx) AS itepnexx, ";
        $qIteDat .= "SUM($cAlfa.siae0201.itebulxx) AS itebulxx ";
        $qIteDat .= "FROM $cAlfa.siae0201 ";
        $qIteDat .= "WHERE ";
        $qIteDat .= "$cAlfa.siae0201.dexidxxx =\"{$mDoiDat[1]}\" AND ";
        $qIteDat .= "$cAlfa.siae0201.admidxxx = \"{$mDoiDat[0]}\" ";
        $xIteDat  = f_MySql("SELECT","",$qIteDat,$xConexion01,"");
        $nFilIte  = mysql_num_rows($xIteDat);
        if ($nFilIte > 0) {
          $vIteDat = mysql_fetch_array($xIteDat);
        }
        /*****Fin Traigo Valor fob, peso neto, peso bruto, bultos de la tabla de Items de Exportaciones siae0201 *****/
        /*****Cargo Variables para imprimir Pedido, Factura, Documento de Transporte *****/
        $cDocTra   = $vDexDat['dexdtrxx'];
        $cPedido   = $vDexDat['dexpedxx'];
        $cFactura  = $vDexDat['dexfaccl'];
        $cPieEmb  += $vIteDat['itebulxx'];
        $cFecFac   = "";
        /*****Fin Cargo Variables para imprimir Pedido, Factura, Documento de Transporte  *****/
      break;
      case "TRANSITO":
        /*****Traigo Datos de la SIAI0200 *****/
        $qDoiDat  = "SELECT * ";
        $qDoiDat .= "FROM $cAlfa.SIAI0200 ";
        $qDoiDat .= "WHERE ";
        $qDoiDat .= "DOIIDXXX = \"$mDoiDat[1]\" AND ";
        $qDoiDat .= "DOISFIDX = \"$mDoiDat[2]\" AND ";
        $qDoiDat .= "ADMIDXXX = \"$mDoiDat[0]\" ";
        $xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
        $nFilDoi  = mysql_num_rows($xDoiDat);
        if ($nFilDoi > 0) {
          $vDoiDat = mysql_fetch_array($xDoiDat);
        }
        /*****Fin Consulta a la tabla de Do's *****/
        //f_Mensaje(__FILE__,__LINE__,$qDoiDat);

        /*****Consulto en la Tabla de Control DTA *****/
        $qDtaDat  = "SELECT * ";
        $qDtaDat .= "FROM $cAlfa.dta00200 ";
        $qDtaDat .= "WHERE ";
        $qDtaDat .= "$cAlfa.dta00200.doiidxxx = \"$mDoiDat[1]\" AND ";
        $qDtaDat .= "$cAlfa.dta00200.admidxxx = \"$mDoiDat[0]\" ";
        $xDtaDat  = f_MySql("SELECT","",$qDtaDat,$xConexion01,"");
        $nFilDta  = mysql_num_rows($xDtaDat);
        if ($nFilDta > 0) {
          $vDtaDat = mysql_fetch_array($xDtaDat);
        }
        /*****Fin consulto en la tabla de Control DTA *****/
        /*****Cargo variables para imprimir Pedido, Factura, Documento de Transporte *****/
        $cDocTra  = $vDoiDat['DGEDTXXX'];
        $cPedido  = $vDoiDat['DOIPEDXX'];
        $cFactura = "";
        $cFecFac  = "";
        /*****Fin Cargo variables para imprimir Pedido, Factura, Documento de Transporte *****/
      break;
    }//Fin Switch para datos de doc. Transporte, pedido, factura comercial,
    /*****Fin Traigo el tipo de Operacion de los Do's que se van a pintar en en excel *****/

    /*****Comienzo armar matriz para imprimir excel *****/
    $mMDo = explode("-",$mMatrizDo[$k][0]);
    $mFac = explode("-",$mMatrizDo[$k][4]);

    $nInd_mMatrizExc = count($mMatrizExc);
    $mMatrizExc[$nInd_mMatrizExc][0] =  "'".$mMDo[1];
    $mMatrizExc[$nInd_mMatrizExc][1] =  $mFac[0]."-".$mFac[1]."-".$mFac[2];
    $mMatrizExc[$nInd_mMatrizExc][2] =  $mMatrizDo[$k][3];
    $mMatrizExc[$nInd_mMatrizExc][3] =  "'".$cPedido;
    $mMatrizExc[$nInd_mMatrizExc][4] =  $cDocTra;
    $mMatrizExc[$nInd_mMatrizExc][5] =  $cPieEmb;
    $mMatrizExc[$nInd_mMatrizExc][6] =  $cFactura;
    $mMatrizExc[$nInd_mMatrizExc][7] =  $cFecFac;
    $nTotPCC = 0;

    $xy = 8;
    for($l=0;$l<count($mDoiId);$l++){
      if($mMatrizDo[$k][0] == $mDoiId[$l][14]){
        if($mMatrizDo[$k][4] == $mDoiId[$l][101]){
          for($j=0;$j<count($mConcepto);$j++){
            if($mConcepto[$j]['cCtoId'] == $mDoiId[$l][1]){
              $mMatrizExc[$nInd_mMatrizExc][$j+$xy] = number_format($mDoiId[$l][7],0,',','');
              $nTotPCC += $mDoiId[$l][7];
            }
          }
        }
      }
    }

    $xy += count($mConcepto);
    $mComFp  = f_Explode_Array($mMatrizDo[$k][5],"|","~");
    $ToComFp = 0;
    for($y=0;$y<count($mComFp);$y++){
    
      $cDoiId  = $mComFp[$y][2];
      $cDocSuf = $mComFp[$y][3];
      $cSucId  = $mComFp[$y][15];
  
      $qDceDat  = "SELECT doctipxx ";
      $qDceDat .= "FROM $cAlfa.sys00121 ";
      $qDceDat .= "WHERE ";
      $qDceDat .= "sucidxxx = \"$cSucId\" AND ";
      $qDceDat .= "docidxxx = \"$cDoiId\" AND ";
      $qDceDat .= "docsufxx = \"$cDocSuf\" LIMIT 0,1 ";
      $xDceDat  = f_MySql("SELECT","",$qDceDat,$xConexion01,"");
      $vDceDat = mysql_fetch_array($xDceDat);
      // f_Mensaje(__FILE__,__LINE__,$qDceDat."~".mysql_num_rows($xDceDat));
  
      if($vDceDat['doctipxx'] != "TRANSPORTE"){
        if($mMatrizDo[$k][0] == $mComFp[$y][15]."-".$mComFp[$y][2]."-".$mComFp[$y][3]){
          if($mComFp[$y][18] != "" && $mComFp[$y][18] != 0){
            $ToComFp += $mComFp[$y][18];
            $mMatrizExc[$nInd_mMatrizExc][$xy] = number_format($mComFp[$y][18],0,',','');
          }
        }
      }
    }
    
    $nTotPCC2 = 0;
    if($nCont == 1){
      $xy += 1;
    }
    
    for($m=0;$m<count($mCodDat);$m++){
      if($mCodDat[$m]['comctocx'] == "PCC" && $mCodDat[$m]['comtraxx'] != ""){
        $cFacPCC = $mCodDat[$m]['comidxxx']."-".$mCodDat[$m]['comcodxx']."-".$mCodDat[$m]['comcscxx']."-".$mCodDat[$m]['comcsc2x'];
        if($mCodDat[$m]['comtraxx'] == $mMatrizDo[$k][0]){
          if($cFacPCC == $mMatrizDo[$k][4]){
            for($n=0;$n<count($mMatrizPCC);$n++){
              $nComObs_IP = stripos($mCodDat[$m]['comobsxx'], "[");
              if($nComObs_IP > 0){
                $mCodDat[$m]['comobsxx'] = substr($mCodDat[$m]['comobsxx'],0,$nComObs_IP);
              }else{
                $mCodDat[$m]['comobsxx'] = $mCodDat[$m]['comobsxx'];
              }
              if (($mMatrizPCC[$n]['cCtoId'] == $mCodDat[$m]['ctoidxxx']) && $mMatrizPCC[$n]['cCtoDes'] == $mCodDat[$m]['comobsxx']) {
                $mMatrizExc[$nInd_mMatrizExc][$n+$xy] = number_format($mCodDat[$m]['comvlrxx'],0,',','');
                //$line .= '"'. number_format($mCodDat[$m]['comvlrxx'],0,',','').'"'."\t";
                $nTotPCC2 += $mCodDat[$m]['comvlrxx'];
              }
            }
          }
        }
      }
    }

    $nTotIP = 0;
    $xy += count($mMatrizPCC);
    //echo $xy;
    for($m=0;$m<count($mCodDat);$m++){
      if($mCodDat[$m]['comctocx'] == "IP" && $mCodDat[$m]['comtraxx'] != ""){
        $cFacIP = $mCodDat[$m]['comidxxx']."-".$mCodDat[$m]['comcodxx']."-".$mCodDat[$m]['comcscxx']."-".$mCodDat[$m]['comcsc2x'];
        if($mCodDat[$m]['comtraxx'] == $mMatrizDo[$k][0]){
          if($cFacIP == $mMatrizDo[$k][4]){
            for($n=0;$n<count($mMatrizIP);$n++){
              if($mMatrizIP[$n]['ctoidxxx'] == $mCodDat[$m]['ctoidxxx']){
                $mMatrizExc[$nInd_mMatrizExc][$n+$xy] = number_format($mCodDat[$m]['comvlrxx'],0,',','');
                //$line .= '"'. number_format($mCodDat[$m]['comvlrxx'],0,',','').'"'."\t";
                $nTotIP += $mCodDat[$m]['comvlrxx'];
              }
            }
          }
        }
      }
    }

    $xy += count($mMatrizIP);
    $mComFpI = f_Explode_Array($mMatrizDo[$k][5],"|","~");
    $ToComFpI = 0;
    for($yi=0;$yi<count($mComFpI);$yi++){
      if($mComFpI[$yi][15]."-".$mComFpI[$yi][2]."-".$mComFpI[$yi][3] == $mMatrizDo[$k][0]){
        if($mComFpI[$yi][20] != "" && $mComFpI[$yi][20] != 0){
          $ToComFpI += $mComFpI[$yi][20];
          // $mMatrizExc[$nInd_mMatrizExc][$yi+$xy] = number_format($mComFpI[$yi][20],0,',','');
          $mMatrizExc[$nInd_mMatrizExc][$xy] = number_format($mComFpI[$yi][20],0,',','');
        }
      }
    }

    $xy += 1; 

    $nTotDo  = $nTotPCC + $ToComFp + $nTotPCC2 + $nTotIP + $ToComFpI;
    $mMatrizExc[$nInd_mMatrizExc][$xy] = number_format($nTotDo,0,',','');

  }

  /***** Fin Matriz Excel*****/

  // echo "<pre>";
  // print_r($mMatrizExc);
  // echo "</pre>";

  for($i=0;$i<count($mMatrizExc);$i++){
    for($n=0;$n<=$xy;$n++) {
      $data .= str_replace($vBuscar,$vReempl,$mMatrizExc[$i][$n])."\t";
    }
    $data .= "\n";
  }

  $data = str_replace("\r","",$data);
  if ($data == "") {
    $data = "\n(0) REGISTROS!\n";
  }

  header("Pragma: public");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Cache-Control: private",false); // required for certain browsers
  header("Content-type: application/octet-stream");
  header("Content-Disposition: attachment; filename=\"".basename($title)."\";");

  print "$header\n$data";

?>
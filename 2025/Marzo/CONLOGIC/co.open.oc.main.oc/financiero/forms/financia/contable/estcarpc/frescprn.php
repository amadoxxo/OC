<?php
  /**
   * Imprime Analisis de Cuentas.
   * --- Descripcion: Permite Imprimir Reporte Estado de Cartera.
   * @author Johana Arboleda <johana.arboleda@opentecnologia.com.co>
   */

  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");

	ini_set("memory_limit","4096M");
	set_time_limit(0);

	date_default_timezone_set("America/Bogota");

  include("../../../../libs/php/utility.php");
  include("../../../../libs/php/utiescar.php");

  $nSwitch = 0;   // Variable para la Validacion de los Datos
  $cMsj = "\n";   // Variable para Guardar los Errores de las Validaciones

  $mArcCre = array();

  $cMes = "";

  switch (substr($dHasta,5,2)){
    case "01": $cMes="ENERO";      break;
    case "02": $cMes="FEBRERO";    break;
    case "03": $cMes="MARZO";      break;
    case "04": $cMes="ABRIL";      break;
    case "05": $cMes="MAYO";       break;
    case "06": $cMes="JUNIO";      break;
    case "07": $cMes="JULIO";      break;
    case "08": $cMes="AGOSTO";     break;
    case "09": $cMes="SEPTIEMBRE"; break;
    case "10": $cMes="OCTUBRE";    break;
    case "11": $cMes="NOVIEMBRE";  break;
    case "12": $cMes="DICIEMBRE";  break;
  }
  $cFecha = substr($dHasta,8,2)." de $cMes de ".substr($dHasta,0,4);
  /////INICO DE VALIDACIONES /////
  ///Inicio Validaciones para condiciones del Reporte ///

  $cTerId = trim($cTerId);

  // Inicio Nit //
  if ($cTerId == "") {
    $nSwitch = 1;
    $cMsj .= "El Nit de Cliente No Puede Ser Vacio.\n";
  }
  // Fin Nit //

  // Inicio Fecha de Corte //
  if ($dHasta == "") {
    $nSwitch = 1;
    $cMsj .= "La Fecha de Corte no puede ser vacio.\n";
  } else {
    if (substr($dHasta,0,4) < $vSysStr['financiero_ano_instalacion_modulo']) {
      $nSwitch = 1;
      $cMsj .= "El Ano de la Fecha de Corte no puede ser menor al Ano en que se instalo el Modulo Financiero Contable.\n";
    }
  }

  // Fin Fecha de Corte //
  //Fin de Validaciones para condiciones del Reporte
  /////FIN DE VALIDACIONES /////
  if ($nSwitch == 0) {
    $AnoFin=substr($dHasta,0,4);
    $AnoIni= $vSysStr['financiero_ano_instalacion_modulo'];

    $qDatExt  = "SELECT comidxxx,comcodxx,comtipxx ";
    $qDatExt .= "FROM $cAlfa.fpar0117 ";
    $qDatExt .= "WHERE ";
    $qDatExt .= "(comidxxx = \"P\" OR comidxxx = \"L\" OR comidxxx = \"C\") AND ";
    $qDatExt .= "regestxx = \"ACTIVO\" ";
    $xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
    $mComP = array();
    while ($xRDE = mysql_fetch_array($xDatExt)){
      $mComP[$xRDE['comidxxx']][$xRDE['comcodxx']] = $xRDE['comtipxx'];
    }

    # Traigo el Nombre del Cliente
    $qNomCli  = "SELECT ";
    $qNomCli .= "if($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,IF($cAlfa.SIAI0150.CLIAPE1X  != \"\",CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X),\"TERCERO SIN NOMBRE\")) AS clinomxx, ";
    $qNomCli .= "$cAlfa.SIAI0150.CLITELXX ";
    $qNomCli .= "FROM $cAlfa.SIAI0150 ";
    $qNomCli .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"$cTerId\" LIMIT 0,1";
    $xNomCli = f_MySql("SELECT","",$qNomCli,$xConexion01,"");
    $vNomCli = mysql_fetch_array($xNomCli);
    //f_Mensaje(__FILE__,__LINE__,$qNomCli."~".mysql_num_rows($xNomCli));
    # Fin Traigo el Nombre del Cliente

    $cCliIdSearch .= "\"$cTerId\"";

    #Buscando solo los saldos de cartera
    $qSaldos  = "SELECT pucidxxx ";
    $qSaldos .= "FROM $cAlfa.fpar0119 ";
    $qSaldos .= "LEFT JOIN $cAlfa.fpar0115 ON CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = $cAlfa.fpar0119.pucidxxx ";
    $qSaldos .= "WHERE ";
    $qSaldos .= "$cAlfa.fpar0119.ctoclaxf IN (\"SCLIENTE\",\"SCLIENTEUSD\",\"SAGENCIA\",\"SAGENCIAIP\",\"SAGENCIAPCC\",\"SAGENCIAUSD\",\"SAGENCIAUSDIP\",\"SAGENCIAUSDPCC\") AND ";
    $qSaldos .= "$cAlfa.fpar0115.pucdetxx IN (\"C\",\"P\") AND ";
    $qSaldos .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
    $xSaldos  = f_MySql("SELECT","",$qSaldos,$xConexion01,"");
    $cPucSal  = "";
    while ($xRDS = mysql_fetch_array($xSaldos)){
      $cPucSal .= "\"{$xRDS['pucidxxx']}\",";
    }
    mysql_free_result($xSaldos);
    $cPucSal = substr($cPucSal, 0, -1);
    #Fin Buscando solo los saldos de cartera

    $cCuentas  = ""; $vCuentas = array();
    if ($cPucSal != "") {
      #Buscando cuentas por cobrar o por pagar
      $qCuentas  = "SELECT *, CONCAT(pucgruxx, pucctaxx, pucsctax, pucauxxx, pucsauxx) AS pucidxxx ";
      $qCuentas .= "FROM $cAlfa.fpar0115 ";
      $qCuentas .= "WHERE ";
      $qCuentas .= "CONCAT(pucgruxx, pucctaxx, pucsctax, pucauxxx, pucsauxx) IN ($cPucSal) AND ";
      $qCuentas .= "regestxx = \"ACTIVO\" ";
      $xCuentas  = f_MySql("SELECT","",$qCuentas,$xConexion01,"");
      while ($xRDS = mysql_fetch_array($xCuentas)){
        $cCuentas .= "\"{$xRDS['pucidxxx']}\",";
        $vCuentas["{$xRDS['pucidxxx']}"] = $xRDS;
      }
      mysql_free_result($xCuentas);
      $cCuentas = substr($cCuentas, 0, -1);
      #Fin Buscando cuentas por cobrar o por pagar
    }

    ##Creacion de la tabla detalle del dia
    $mTabMov = array(); //Nombre de las tablas temporales para el movimiento
    $mFacCli = array(); //Datos de Cabecera de las facturas del cleinte
    $mTabMovCsc3 = array(); //Nombre de las tablas temporales para el movimiento UPS
    if ($cCuentas != "") {
      ##Fin Acciones sobre la DB en el paso Dos
      $mDatMov = array();
      $mDatMov = array();
      for ($nAno=$AnoIni;$nAno<=$AnoFin;$nAno++) {
        $qDatMov  = "SELECT ";
        $qDatMov .= "$cAlfa.fcod$nAno.comidcxx, ";
        $qDatMov .= "$cAlfa.fcod$nAno.comcodcx, ";
        $qDatMov .= "$cAlfa.fcod$nAno.comcsccx, ";
        $qDatMov .= "$cAlfa.fcod$nAno.teridxxx, ";
        $qDatMov .= "$cAlfa.fcod$nAno.pucidxxx, ";
        $qDatMov .= "GROUP_CONCAT(CONCAT($cAlfa.fcod$nAno.comidxxx,\"-\",$cAlfa.fcod$nAno.comcodxx,\"-\",$cAlfa.fcod$nAno.comcscxx,\"~\",$cAlfa.fcod$nAno.comfecxx,\"~\",$cAlfa.fcod$nAno.comfecve,\"~\",$cAlfa.fcoc$nAno.comfefac) ORDER BY $cAlfa.fcod$nAno.comfecxx) AS fechasxx, ";
        $qDatMov .= "$cAlfa.fcod$nAno.commovxx, ";
        $qDatMov .= "SUM(if ($cAlfa.fcod$nAno.commovxx = \"D\", $cAlfa.fcod$nAno.comvlrxx, $cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx, ";
        $qDatMov .= "$cAlfa.fcod$nAno.regestxx,";
        $qDatMov .= "$cAlfa.fcoc$nAno.comidxxx,";
        $qDatMov .= "$cAlfa.fcoc$nAno.comcodxx,";
        $qDatMov .= "$cAlfa.fcoc$nAno.comcscxx,";
        $qDatMov .= "GROUP_CONCAT($cAlfa.fcoc$nAno.comfpxxx) AS comfpxxx, ";
        $qDatMov .= "$cAlfa.fcoc$nAno.comobs2x ";
        $qDatMov .= "FROM $cAlfa.fcod$nAno ";
        $qDatMov .= "LEFT JOIN  $cAlfa.fcoc$nAno ON ";
        $qDatMov .= "$cAlfa.fcod$nAno.comidxxx = $cAlfa.fcoc$nAno.comidxxx AND ";
        $qDatMov .= "$cAlfa.fcod$nAno.comcodxx = $cAlfa.fcoc$nAno.comcodxx AND ";
        $qDatMov .= "$cAlfa.fcod$nAno.comcscxx = $cAlfa.fcoc$nAno.comcscxx AND ";
        $qDatMov .= "$cAlfa.fcod$nAno.comcsc2x = $cAlfa.fcoc$nAno.comcsc2x ";
        $qDatMov .= "WHERE  ";
        // if ($_COOKIE['kUsrId'] == "ADMIN") {
        //   $qDatMov .= "$cAlfa.fcod$nAno.comidcxx = \"F\" AND ";
        //   $qDatMov .= "$cAlfa.fcod$nAno.comcodcx = \"001\" AND ";
        //   $qDatMov .= "$cAlfa.fcod$nAno.comcsccx = \"43634\" AND ";
        // }
        if ($cCliIdSearch != "") {
          $qDatMov .= "$cAlfa.fcod$nAno.teridxxx IN ($cCliIdSearch) AND ";
        }
        $qDatMov .= "$cAlfa.fcod$nAno.comfecxx <= \"$dHasta\" AND ";
        $qDatMov .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" AND ";
        $qDatMov .= "$cAlfa.fcod$nAno.pucidxxx IN ($cCuentas)  ";
        $qDatMov .= "GROUP BY $cAlfa.fcod$nAno.comidcxx,$cAlfa.fcod$nAno.comcodcx,$cAlfa.fcod$nAno.comcsccx,$cAlfa.fcod$nAno.teridxxx,$cAlfa.fcod$nAno.pucidxxx ";
        $qDatMov .= "ORDER BY $cAlfa.fcod$nAno.comidcxx,$cAlfa.fcod$nAno.comcodcx,$cAlfa.fcod$nAno.comcsccx,$cAlfa.fcod$nAno.teridxxx,$cAlfa.fcod$nAno.pucidxxx ";
        $xDatMov = mysql_query($qDatMov,$xConexion01);
        // if ($_COOKIE['kUsrId'] == "ADMIN") {
        //     echo $qDatMov."~".mysql_num_rows($xDatMov)."<br><br>";
        // }
        while ($xCre = mysql_fetch_array($xDatMov)) {

          $xCre['pucdetxx'] = $vCuentas["{$xCre['pucidxxx']}"]['pucdetxx'];
          $xCre['pucdesxx'] = $vCuentas["{$xCre['pucidxxx']}"]['pucdesxx'];

          //Buscando la fecha del comprobante
          $mAuxFec = explode(",", $xCre['fechasxx']);
          $dFecCre = ""; $dFecVen = "";

          $nEncFec = 0;
          for ($nF=0; $nF<count($mAuxFec); $nF++) {
            if ($mAuxFec[$nF] != "") {
              $mAuxCom = array();
              $mAuxCom = explode("~", $mAuxFec[$nF]);
              $dFecCre = $mAuxCom[1];
              $dFecVen = $mAuxCom[2];
              $vAuxId = explode("-",$mAuxCom[0]);

              if ($vAuxId[0] == "S" || $mAuxCom[0] == $xCre['comidcxx']."-".$xCre['comcodcx']."-".$xCre['comcsccx']) {
                $nEncFec = 1;
                //Encontro fecha comprobante
                $xCre['comfecxx'] = $mAuxCom[1];
                $xCre['comfecve'] = $mAuxCom[2];
                $xCre['comfefac'] = $mAuxCom[3];
                $nF = count($mAuxFec);
              }
            }
          }

          $nDias = 0;
          $cKey = $xCre['comidcxx']."-".$xCre['comcodcx']."-".$xCre['comcsccx']."-".$xCre['teridxxx']."-".$xCre['pucidxxx'];
          if($mDatMov[$cKey]['comidcxx'] == '') {

            $mDatMov[$cKey]['comidcxx']  = $xCre['comidcxx'];
            $mDatMov[$cKey]['comcodcx']  = $xCre['comcodcx'];
            $mDatMov[$cKey]['comcsccx']  = $xCre['comcsccx'];
            $mDatMov[$cKey]['teridxxx']  = $xCre['teridxxx'];
            $mDatMov[$cKey]['pucidxxx']  = $xCre['pucidxxx'];
            $mDatMov[$cKey]['pucdetxx']  = $xCre['pucdetxx'];
            $mDatMov[$cKey]['pucdesxx']  = $xCre['pucdesxx'];
            $mDatMov[$cKey]['commovxx']  = $xCre['commovxx'];
            $mDatMov[$cKey]['comfpxxx']  = trim($xCre['comfpxxx'],",");
            $mDatMov[$cKey]['comobs2x']  = $xCre['comobs2x'];
            $mDatMov[$cKey]['regestxx']  = $xCre['regestxx'];
          }

          if ($nEncFec == 1) {
            $mDatMov[$cKey]['comfecin']  = $xCre['comfecxx'];
            $mDatMov[$cKey]['comfefac']  = ($xCre['comfefac'] != "") ? $xCre['comfefac'] : "0000-00-00";
            $mDatMov[$cKey]['comfecxx']  = ($xCre['comfecxx'] != "") ? $xCre['comfecxx'] : $dFecCre;
            $mDatMov[$cKey]['comfecve']  = ($xCre['comfecve'] != "") ? $xCre['comfecve'] : $dFecVen;
          }
          $mDatMov[$cKey]['fechasxx'] .= $xCre['fechasxx'].",";
          $mDatMov[$cKey]['saldoxxx'] += $xCre['comvlrxx'];
        }
      }

      // echo "<pre>";
      // print_r($mDatMov);
      // echo "</pre>";

      //// Empiezo a Recorrer la Matriz de Creditos Vs Debitos para Dejar las Cuentas que a la fecha de Corte tienen Saldo ////
      $mCarteraVencida   = array(); //Cartera que tiene uno o mas dias de vencimiento
      $mCarteraSinVencer = array(); //Carter que no se ha vencido
      $mSaldosaFavor     = array(); //Saldos a Favor del Cliente, valores negativos
      foreach ($mDatMov as $i => $cValue) {
        if ($mDatMov[$i]['saldoxxx'] != 0) {

          //Fechas de vencimeinto de SIACO, se calcula con la fecha de entrega de factura al cliente
          if (($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") && $mDatMov[$i]['comidcxx'] == "F") {
            //Buscando Pedido
            if ($mDatMov[$i]['comobs2x'] != "") {
              $vAuxPed = explode("~",$mDatMov[$i]['comobs2x']);
              $mDatMov[$i]['pedidoxx'] = $vAuxPed[8];
            }
            //Calculando cuantos dias son para el vencimiento
            $dComFec  = str_replace("-","",$mDatMov[$i]['comfecxx']);
            $dConFeVe = str_replace("-","",$mDatMov[$i]['comfecve']);
            $nDias    = round((mktime(0,0,0,substr($dConFeVe,4,2),substr($dConFeVe,6,2), substr($dConFeVe,0,4))  - mktime(0,0,0,substr($dComFec,4,2), substr($dComFec,6,2),  substr($dComFec,0,4))) / (60 * 60 * 24));

            if ($mDatMov[$i]['comfefac'] == "0000-00-00") {
              $mDatMov[$i]['comfecnx'] = $mDatMov[$i]['comfecxx'];
              $mDatMov[$i]['comfecvn'] = $mDatMov[$i]['comfecve'];
            } else {
              $mDatMov[$i]['comfecnx'] = $mDatMov[$i]['comfefac'];
              $dConFeVe = str_replace("-","",$mDatMov[$i]['comfefac']);
              $mDatMov[$i]['comfecvn'] = date("Y-m-d",mktime(0,0,0,substr($dConFeVe,4,2),substr($dConFeVe,6,2)+$nDias, substr($dConFeVe,0,4)));
            }

            $dComFecVe = $mDatMov[$i]['comfecvn'];
            $dComFec   = $mDatMov[$i]['comfecnx'];

          } else {
            $mDatMov[$i]['comfecvn'] = $mDatMov[$i]['comfecxx'];
            $mDatMov[$i]['comfecvn'] = $mDatMov[$i]['comfecve'];
            $dComFecVe = $mDatMov[$i]['comfecvn'];
            $dComFec   = $mDatMov[$i]['comfecxx'];
          }

          $valorVen = 0;
          $valorCar = 0;

          if ($dComFecVe != "0000-00-00" && $dComFec != "0000-00-00") {
            $dFecCor = date('Ymd');
            $dFecCar = str_replace("-","",$dComFec);
            $dFecVen = str_replace("-","",$dComFecVe);
            $dateVen = mktime(0,0,0,substr($dFecVen,4,2), substr($dFecVen,6,2), substr($dFecVen,0,4));

            $dateCar = mktime(0,0,0,substr($dFecCar,4,2), substr($dFecCar,6,2), substr($dFecCar,0,4));
            $valorCar= round(($dateVen  - $dateCar) / (60 * 60 * 24));

            $dateCor = mktime(0,0,0,substr($dFecCor,4,2), substr($dFecCor,6,2), substr($dFecCor,0,4));
            $valorVen= round(($dateCor  - $dateVen) / (60 * 60 * 24));
          }

          //echo $mDatMov[$i]['comcsccx']."~".$mDatMov[$i]['comfecve']."~".$mDatMov[$i]['comfecxx']."<br>";

          $mDatMov[$i]['commovxx'] = ($mDatMov[$i]['saldoxxx'] > 0) ? "D" : "C";
          $cDocument = $mDatMov[$i]['comidcxx']."-".$mDatMov[$i]['comcodcx']."-".$mDatMov[$i]['comcsccx'];

          if ( $vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI') {
            // si aplica el tercer consecutivo busco el comcsc3x
            for ($nAno=$AnoIni;$nAno<=$AnoFin;$nAno++) {
              $qDatMov  = "SELECT ";
              $qDatMov .= "$cAlfa.fcoc$nAno.comcsc3x, ";
              $qDatMov .= "$cAlfa.fcoc$nAno.comcsc2x ";
              $qDatMov .= "FROM $cAlfa.fcoc$nAno ";
              $qDatMov .= "WHERE ";
              $qDatMov .= "($cAlfa.fcoc$nAno.comidxxx = \"{$mDatMov[$i]['comidcxx']}\" OR $cAlfa.fcoc$nAno.comidxxx =\"S\" ) AND ";
              $qDatMov .= "$cAlfa.fcoc$nAno.comidcxx = \"{$mDatMov[$i]['comidcxx']}\" AND ";
              $qDatMov .= "$cAlfa.fcoc$nAno.comcodcx = \"{$mDatMov[$i]['comcodcx']}\" AND ";
              $qDatMov .= "$cAlfa.fcoc$nAno.comcsccx = \"{$mDatMov[$i]['comcsccx']}\" AND ";
              $qDatMov .= "$cAlfa.fcoc$nAno.teridxxx = \"{$mDatMov[$i]['teridxxx']}\" AND ";
              $qDatMov .= "$cAlfa.fcoc$nAno.pucidxxx = \"{$mDatMov[$i]['pucidxxx']}\" LIMIT 0,1";
              $xDatMov  = mysql_query($qDatMov,$xConexion01);
              // echo $qDatMov."~".mysql_num_rows($xDatMov)."<br><br>";

              if (mysql_num_rows($xDatMov) > 0 ) {
                $vCre = mysql_fetch_array($xDatMov);
                $vCre['comcsc3x'] = ($vCre['comcsc3x'] != '') ? $vCre['comcsc3x'] : $vCre['comcsc2x'];
                $cDocument = $mDatMov[$i]['comidcxx']."-".$mDatMov[$i]['comcodcx']."-".$mDatMov[$i]['comcsccx']."-".$vCre['comcsc3x'];
                $nAno = $AnoFin + 1;
              }
            }
          }

          //Consulto el numero de DO
          if ($mDatMov[$i]['comidcxx'] == "F") {
            $mDocid = f_Explode_Array($mDatMov[$i]['comfpxxx'],"|","~");
            $mDatMov[$i]['docidxxx'] = $mDocid[0][2];
          }

          //Comprabante NC y ND
          if($mDatMov[$i]['comidcxx'] == "C" || $mDatMov[$i]['comidcxx'] == "D"){
            for ($nAno=$AnoIni;$nAno<=$AnoFin;$nAno++) {
              // Buscan documento afectado de la NC o ND
              $qCocDat  = "SELECT ";
              $qCocDat .= "$cAlfa.fcoc$nAno.comobs2x ";
              $qCocDat .= "FROM $cAlfa.fcoc$nAno ";
              $qCocDat .= "WHERE ";
              $qCocDat .= "$cAlfa.fcoc$nAno.comidxxx = \"{$mDatMov[$i]['comidcxx']}\" AND ";
              $qCocDat .= "$cAlfa.fcoc$nAno.comcodxx = \"{$mDatMov[$i]['comcodcx']}\" AND ";
              $qCocDat .= "$cAlfa.fcoc$nAno.comcscxx = \"{$mDatMov[$i]['comcsccx']}\" ";
              $xCocDat  = mysql_query($qCocDat,$xConexion01);
              //f_Mensaje(__FILE__, __LINE__,$qCocDat.'~'.mysql_num_rows($xCocDat));
              $vCocDat  = mysql_fetch_array($xCocDat);	
              //Documento afectado
              $vDocAfe = explode("~", $vCocDat['comobs2x']);
              if ($vDocAfe[0] != "") {
                /// Si es ND busca si afecta una Nota Credito
                if($mDatMov[$i]['comidcxx'] == "D" && $vDocAfe[1] == "C") {
                  // Buscan documento afectado de la ND
                  $qNdAfec  = "SELECT ";
                  $qNdAfec .= "comobs2x ";
                  $qNdAfec .= "FROM $cAlfa.fcoc$vDocAfe[0] ";
                  $qNdAfec .= "WHERE ";
                  $qNdAfec .= "comidxxx = \"{$vDocAfe[1]}\" AND ";
                  $qNdAfec .= "comcodxx = \"{$vDocAfe[2]}\" AND ";
                  $qNdAfec .= "comcscxx = \"{$vDocAfe[3]}\" AND ";
                  $qNdAfec .= "comcsc2x = \"{$vDocAfe[4]}\" LIMIT 0,1";
                  $xNdAfec  = mysql_query($qNdAfec,$xConexion01);
                  // f_Mensaje(__FILE__, __LINE__,$qNdAfec.'~'.mysql_num_rows($xNdAfec));
                  $vNdAfec  = mysql_fetch_array($xNdAfec);	
                  //Nota de Credito afectado
                  $vDocAfe = explode("~", $vNdAfec['comobs2x']);
                }

                //Busco Factura Afectada
                $qFacAfe  = "SELECT ";
                $qFacAfe .= "comfpxxx ";
                $qFacAfe .= "FROM $cAlfa.fcoc$vDocAfe[0] ";
                $qFacAfe .= "WHERE ";
                $qFacAfe .= "comidxxx = \"{$vDocAfe[1]}\" AND ";
                $qFacAfe .= "comcodxx = \"{$vDocAfe[2]}\" AND ";
                $qFacAfe .= "comcscxx = \"{$vDocAfe[3]}\" AND ";
                $qFacAfe .= "comcsc2x = \"{$vDocAfe[4]}\" LIMIT 0,1";
                $xFacAfe  = mysql_query($qFacAfe,$xConexion01);
                $vFacAfe  = mysql_fetch_array($xFacAfe);	
                // f_Mensaje(__FILE__, __LINE__,$qFacAfe.'~'.mysql_num_rows($xFacAfe));
                //$mDoiId = explode("|",$vFacAfe['comfpxxx']);
                $mDoiId = f_Explode_Array($vFacAfe['comfpxxx'],"|","~");
                //f_Mensaje(__FILE__, __LINE__,$vFacAfe['comfpxxx']);

                $mDatMov[$i]['docidxxx'] = $mDoiId[0][2];
              }		
            }			
          }

          //IN-2495 Para la agencia de aduana internacional, en vez de la cuenta contable, debe mostrarse  el numero del DO
          if (($cAlfa == "DEAAINTERX" || $cAlfa == "TEAAINTERX" || $cAlfa == "AAINTERX") && $mDatMov[$i]['comidcxx'] == "F") {
            $mDatMov[$i]['pucidxxx'] = $mDatMov[$i]['docidxxx'];
          }

          if ($mDatMov[$i]['saldoxxx'] < 0 || $mDatMov[$i]['pucdetxx'] == "P") { //es un saldo a favor del cliente
            $nInd_mSaldosaFavor = count($mSaldosaFavor);
            $mSaldosaFavor[$nInd_mSaldosaFavor]['comidxxx']=$mDatMov[$i]['comidcxx'];
            $mSaldosaFavor[$nInd_mSaldosaFavor]['comcodxx']=$mDatMov[$i]['comcodcx'];
            $mSaldosaFavor[$nInd_mSaldosaFavor]['comcscxx']=$mDatMov[$i]['comcsccx'];
            $mSaldosaFavor[$nInd_mSaldosaFavor]['document']=$cDocument;
            $mSaldosaFavor[$nInd_mSaldosaFavor]['comfecin']=$mDatMov[$i]['comfecin'];
            $mSaldosaFavor[$nInd_mSaldosaFavor]['comfecxx']=$mDatMov[$i]['comfecxx'];
            $mSaldosaFavor[$nInd_mSaldosaFavor]['comfecve']=$mDatMov[$i]['comfecve'];
            $mSaldosaFavor[$nInd_mSaldosaFavor]['comfecnx']=$mDatMov[$i]['comfecnx'];
            $mSaldosaFavor[$nInd_mSaldosaFavor]['comfecvn']=$mDatMov[$i]['comfecvn'];
            $mSaldosaFavor[$nInd_mSaldosaFavor]['diascart']=$valorCar;
            $mSaldosaFavor[$nInd_mSaldosaFavor]['diasvenc']=0;
            $mSaldosaFavor[$nInd_mSaldosaFavor]['teridxxx']=$mDatMov[$i]['teridxxx'];
            $mSaldosaFavor[$nInd_mSaldosaFavor]['pucidxxx']=$mDatMov[$i]['pucidxxx'];
            $mSaldosaFavor[$nInd_mSaldosaFavor]['pucdesxx']=$mDatMov[$i]['pucdesxx'];
            $mSaldosaFavor[$nInd_mSaldosaFavor]['commovxx']=$mDatMov[$i]['commovxx'];
            $mSaldosaFavor[$nInd_mSaldosaFavor]['saldoxxx']=abs($mDatMov[$i]['saldoxxx']);
            $mSaldosaFavor[$nInd_mSaldosaFavor]['regestxx']=$mDatMov[$i]['regestxx'];
            $mSaldosaFavor[$nInd_mSaldosaFavor]['pedidoxx']=$mDatMov[$i]['pedidoxx'];
            $mSaldosaFavor[$nInd_mSaldosaFavor]['docidxxx']=$mDatMov[$i]['docidxxx'];
          } else if ($valorVen > 0) { //Cartera vencida
            $nInd_mCarteraVencida = count($mCarteraVencida);
            $mCarteraVencida[$nInd_mCarteraVencida]['comidxxx']=$mDatMov[$i]['comidcxx'];
            $mCarteraVencida[$nInd_mCarteraVencida]['comcodxx']=$mDatMov[$i]['comcodcx'];
            $mCarteraVencida[$nInd_mCarteraVencida]['comcscxx']=$mDatMov[$i]['comcsccx'];
            $mCarteraVencida[$nInd_mCarteraVencida]['document']=$cDocument;
            $mCarteraVencida[$nInd_mCarteraVencida]['comfecin']=$mDatMov[$i]['comfecin'];
            $mCarteraVencida[$nInd_mCarteraVencida]['comfecxx']=$mDatMov[$i]['comfecxx'];
            $mCarteraVencida[$nInd_mCarteraVencida]['comfecve']=$mDatMov[$i]['comfecve'];
            $mCarteraVencida[$nInd_mCarteraVencida]['comfecnx']=$mDatMov[$i]['comfecnx'];
            $mCarteraVencida[$nInd_mCarteraVencida]['comfecvn']=$mDatMov[$i]['comfecvn'];
            $mCarteraVencida[$nInd_mCarteraVencida]['diascart']=$valorCar;
            $mCarteraVencida[$nInd_mCarteraVencida]['diasvenc']=$valorVen;
            $mCarteraVencida[$nInd_mCarteraVencida]['teridxxx']=$mDatMov[$i]['teridxxx'];
            $mCarteraVencida[$nInd_mCarteraVencida]['pucidxxx']=$mDatMov[$i]['pucidxxx'];
            $mCarteraVencida[$nInd_mCarteraVencida]['pucdesxx']=$mDatMov[$i]['pucdesxx'];
            $mCarteraVencida[$nInd_mCarteraVencida]['commovxx']=$mDatMov[$i]['commovxx'];
            $mCarteraVencida[$nInd_mCarteraVencida]['saldoxxx']=abs($mDatMov[$i]['saldoxxx']);
            $mCarteraVencida[$nInd_mCarteraVencida]['regestxx']=$mDatMov[$i]['regestxx'];
            $mCarteraVencida[$nInd_mCarteraVencida]['pedidoxx']=$mDatMov[$i]['pedidoxx'];
            $mCarteraVencida[$nInd_mCarteraVencida]['docidxxx']=$mDatMov[$i]['docidxxx'];
          } else { //Cartera no vencida
            $nInd_mCarteraSinVencer = count($mCarteraSinVencer);
            $mCarteraSinVencer[$nInd_mCarteraSinVencer]['comidxxx']=$mDatMov[$i]['comidcxx'];
            $mCarteraSinVencer[$nInd_mCarteraSinVencer]['comcodxx']=$mDatMov[$i]['comcodcx'];
            $mCarteraSinVencer[$nInd_mCarteraSinVencer]['comcscxx']=$mDatMov[$i]['comcsccx'];
            $mCarteraSinVencer[$nInd_mCarteraSinVencer]['document']=$cDocument;
            $mCarteraSinVencer[$nInd_mCarteraSinVencer]['comfecin']=$mDatMov[$i]['comfecin'];
            $mCarteraSinVencer[$nInd_mCarteraSinVencer]['comfecxx']=$mDatMov[$i]['comfecxx'];
            $mCarteraSinVencer[$nInd_mCarteraSinVencer]['comfecve']=$mDatMov[$i]['comfecve'];
            $mCarteraSinVencer[$nInd_mCarteraSinVencer]['comfecnx']=$mDatMov[$i]['comfecnx'];
            $mCarteraSinVencer[$nInd_mCarteraSinVencer]['comfecvn']=$mDatMov[$i]['comfecvn'];
            $mCarteraSinVencer[$nInd_mCarteraSinVencer]['diascart']=$valorCar;
            $mCarteraSinVencer[$nInd_mCarteraSinVencer]['diasvenc']=$valorVen;
            $mCarteraSinVencer[$nInd_mCarteraSinVencer]['teridxxx']=$mDatMov[$i]['teridxxx'];
            $mCarteraSinVencer[$nInd_mCarteraSinVencer]['pucidxxx']=$mDatMov[$i]['pucidxxx'];
            $mCarteraSinVencer[$nInd_mCarteraSinVencer]['pucdesxx']=$mDatMov[$i]['pucdesxx'];
            $mCarteraSinVencer[$nInd_mCarteraSinVencer]['commovxx']=$mDatMov[$i]['commovxx'];
            $mCarteraSinVencer[$nInd_mCarteraSinVencer]['saldoxxx']=abs($mDatMov[$i]['saldoxxx']);
            $mCarteraSinVencer[$nInd_mCarteraSinVencer]['regestxx']=$mDatMov[$i]['regestxx'];
            $mCarteraSinVencer[$nInd_mCarteraSinVencer]['pedidoxx']=$mDatMov[$i]['pedidoxx'];
            $mCarteraSinVencer[$nInd_mCarteraSinVencer]['docidxxx']=$mDatMov[$i]['docidxxx'];
          }
        }
      }
      //// Fin Recorrer la Matriz de Creditos-Debitos para Dejar las Cuentas que a la fecha de Corte tienen Saldo ////
      /////FIN DE CALCULOS PARA ARMAR EL ARCHIVO /////
    }

    ///Recibos Provisionales a la fecha de corte
    $mRecProv = array();
    for ($nAno=$AnoIni;$nAno<=$AnoFin;$nAno++) {
      $qProvCab  = "SELECT ";
      $qProvCab .= "$cAlfa.fcoc$nAno.comidxxx, ";
      $qProvCab .= "$cAlfa.fcoc$nAno.comcodxx, ";
      $qProvCab .= "$cAlfa.fcoc$nAno.comcscxx, ";
      $qProvCab .= "$cAlfa.fcoc$nAno.comcsc2x, ";
      $qProvCab .= "$cAlfa.fcoc$nAno.comcsc3x, ";
      $qProvCab .= "$cAlfa.fcoc$nAno.teridxxx, ";
      $qProvCab .= "$cAlfa.fcoc$nAno.comfecxx, ";
      $qProvCab .= "$cAlfa.fcoc$nAno.comfecve, ";
      $qProvCab .= "$cAlfa.fcoc$nAno.comvlr01  ";
      $qProvCab .= "FROM $cAlfa.fcoc$nAno ";
      $qProvCab .= "WHERE ";
      $qProvCab .= "$cAlfa.fcoc$nAno.comidxxx != \"F\" AND ";
      if ($cCliIdSearch != "") {
        $qProvCab .= "$cAlfa.fcoc$nAno.teridxxx IN ($cCliIdSearch) AND ";
      }
      $qProvCab .= "$cAlfa.fcoc$nAno.regestxx = \"PROVISIONAL\" ";
      $xProvCab = f_MySql("SELECT","",$qProvCab,$xConexion01,"");

      while ($xRDM = mysql_fetch_array($xProvCab)) {
        if ($xRDM['comvlr01'] != 0) {
          if ( $vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI' && $xRDM['comcsc3x'] == '' ) {
            $xRDM['comcsc3x'] = $xRDM['comcsc2x'];
          }
          $nInd_mRecProv = count($mRecProv);
          $mRecProv[$nInd_mRecProv]['comidxxx']=$xRDM['comidxxx'];
          $mRecProv[$nInd_mRecProv]['comcodxx']=$xRDM['comcodxx'];
          $mRecProv[$nInd_mRecProv]['comcscxx']=$xRDM['comcscxx'];
          $mRecProv[$nInd_mRecProv]['comfecin']=$xRDM['comfecxx'];
          $mRecProv[$nInd_mRecProv]['comfecxx']=$xRDM['comfecxx'];
          $mRecProv[$nInd_mRecProv]['document']= ( $vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI' ) ? $xRDM['comidxxx']."-".$xRDM['comcodxx']."-".$xRDM['comcscxx']."-".$xRDM['comcsc3x'] : $xRDM['comidxxx']."-".$xRDM['comcodxx']."-".$xRDM['comcscxx'];
          $mRecProv[$nInd_mRecProv]['comfecve']=$xRDM['comfecve'];
          $mRecProv[$nInd_mRecProv]['diascart']="";
          $mRecProv[$nInd_mRecProv]['diasvenc']="";
          $mRecProv[$nInd_mRecProv]['teridxxx']=$xRDM['teridxxx'];
          $mRecProv[$nInd_mRecProv]['commovxx']=($xRDM['comvlr01'] > 0) ? "D" : "C";
          $mRecProv[$nInd_mRecProv]['saldoxxx']=abs($xRDM['comvlr01']);
        }
      }
    }
    ///Recibos Provisionales a la fecha de corte

    $mSaldosaFavor     = f_ordenar_array_bidimensional($mSaldosaFavor,'diasvenc',SORT_DESC,'comfecxx',SORT_ASC,'document',SORT_ASC);
    $mCarteraVencida   = f_ordenar_array_bidimensional($mCarteraVencida,'diasvenc',SORT_DESC,'comfecxx',SORT_ASC,'document',SORT_ASC);
    $mCarteraSinVencer = f_ordenar_array_bidimensional($mCarteraSinVencer,'diasvenc',SORT_DESC,'comfecxx',SORT_ASC,'document',SORT_ASC);
    $mRecProv          = f_ordenar_array_bidimensional($mRecProv,'comfecxx',SORT_ASC,'document',SORT_ASC);

    switch ($cTipo) {
      case 1:
        // PINTA POR PANTALLA//  ?>
        <script language="javascript">
          function f_Ver(xComId,xComCod,xComCsc,xComFec,xRegEst,xTipCom) {

            var xComId = xComId;
            var xComCod = xComCod;
            var xComCsc = xComCsc;
            var xComFec = xComFec;
            var xRegEst = xRegEst;
            var xTipCom = xTipCom;

            var ruta  = "frvercom.php?xComId="+xComId+"&xComCod="+xComCod+"&xComCsc="+xComCsc+"&xComFec="+xComFec+"&xRegEst="+xRegEst+"&xTipCom="+xTipCom;

            //document.location = ruta; // Invoco el menu.
            //
            var zX    = screen.width;
            var zY    = screen.height;
            var zNx     = (zX-550)/2;
            var zNy     = (zY-350)/2;
            var zWinPro = 'width=550,scrollbars=1,height=350,left='+zNx+',top='+zNy;
            //var cNomVen = 'zWindowcom';
            var cNomVen = 'zWindow'+Math.ceil(Math.random()*1000);
            zWindow = window.open(ruta,cNomVen,zWinPro);
            zWindow.focus();
          }
        </script>
        <html>
          <title>Reporte Estado de Cartera</title>
          <head>
            <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
            <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
            <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
            <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
            <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
            <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
            <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
          </head>
          <body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0">
            <?php
            $nCol     = ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") ? 9 : 8;
            $nCol     = ($cColPed == "SI") ? ($nCol + 1) : $nCol;
            $nCol     = ($vSysStr['financiero_ver_do_reporte_estado_cartera'] == "SI") ? ($nCol + 1) : $nCol;
            $nColProv = ($cColPed == "SI") ? 3 : 2;
            ?>
            <center>
              <table border = "1" cellpadding = "0" cellspacing = "0" width = "98%">
                <tr>
                  <?php
                  switch ($cAlfa) {
                    case 'ADUANAMO':
                    case 'DEADUANAMO':
                    case 'TEADUANAMO': ?>
                      <td class="name" style="font-size:14px;width:120px">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/logo_aduanamo.png">
                      </td>
                    <?php break;
                    case 'SIACOSIA':
                    case 'TESIACOSIP':
                    case 'DESIACOSIP': ?>
                     <td class="name" style="font-size:14px;width:70px">
                       <img src = "<?php echo $cPlesk_Skin_Directory ?>/logo_repcar.jpg" style="height: 50px;width:70">
                     </td>
                    <?php break;
                    case 'LOGINCAR':
                    case 'TELOGINCAR':
                    case 'DELOGINCAR': ?>
                     <td class="name" style="font-size:14px;width:70px">
                       <img src = "<?php echo $cPlesk_Skin_Directory ?>/Logo_Login_Cargo_Ltda_2.jpg" style="height: 41px;width:156">
                     </td>
                    <?php break;
										case "ROLDANLO"://ROLDAN
			              case "TEROLDANLO"://ROLDAN
			              case "DEROLDANLO"://ROLDAN ?>
                      <td class="name" style="font-size:14px;width:100px">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/logoroldan.png" style="height: 41px;width:156">
                      </td>
                    <?php break;
                    case "CASTANOX":
                    case "DECASTANOX":
                    case "TECASTANOX": ?>
                      <td class="name" style="font-size:14px;width:100px">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/logomartcam.jpg.png" style="height: 41px;width:156">
                      </td>
                    <?php break;
                    case "ALMACAFE": //ALMACAFE
    	  						case "TEALMACAFE": //ALMACAFE
    	  						case "DEALMACAFE": //ALMACAFE ?>
                      <td class="name" style="font-size:14px;width:100px">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/logoalmacafe.jpg.png" style="height: 41px;width:156">
                      </td>
                    <?php break;
                    case "GRUMALCO"://GRUMALCO
			              case "TEGRUMALCO"://GRUMALCO
			              case "DEGRUMALCO"://GRUMALCO?>
                      <td class="name" style="font-size:14px;width:100px">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/logomalco.jpg" style="height: 55px;width:120">
                      </td>
                    <?php break;
                    case "ALADUANA"://ALADUANA
			              case "TEALADUANA"://ALADUANA
			              case "DEALADUANA":  ?>
                      <td class="name" style="font-size:14px;width:100px">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/logoaladuana.jpg" style="height: 60px;width:120">
                      </td>
                    <?php break;
                    case "ANDINOSX"://ANDINOSX
                    case "TEANDINOSX"://ANDINOSX
                    case "DEANDINOSX"://ANDINOSX ?>
                      <td class="name" style="font-size:14px;width:100px">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/logoAndinos2.jpeg" style="height: 50px;width:40">
                      </td>
                    <?php break;
                    case "GRUPOALC"://GRUPOALC
                    case "TEGRUPOALC"://GRUPOALC
                    case "DEGRUPOALC"://GRUPOALC ?>
                      <td class="name" style="font-size:14px;width:100px">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/logoalc.jpg" style="height: 60px;width:120">
                      </td>
                    <?php break;
                    case "AAINTERX"://AAINTERX
			              case "TEAAINTERX"://AAINTERX
			              case "DEAAINTERX":  ?>
                      <td class="name" style="font-size:14px;width:100px">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/logointernacional.jpg" style="height: 60px;width:120">
                      </td>
                    <?php break;
                    case "AALOPEZX":
                    case "TEAALOPEZX":
                    case "DEAALOPEZX": ?>
                      <td class="name" style="font-size:14px;width:100px">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/logoaalopez.png" style="width:120">
                      </td>
                    <?php break;
                    case "ADUAMARX"://ADUAMARX
                    case "TEADUAMARX"://ADUAMARX
                    case "DEADUAMARX"://ADUAMARX ?>
                      <td class="name" style="font-size:14px;width:100px">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/logoaduamar.jpg" style="width:70">
                      </td>
                    <?php break;
                    case "SOLUCION"://SOLUCION
                    case "TESOLUCION"://SOLUCION
                    case "DESOLUCION"://SOLUCION ?>
                      <td class="name" style="font-size:14px;width:100px">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/logosoluciones.jpg" style="width:120">
                      </td>
										<?php break;
										case "FENIXSAS"://FENIXSAS
										case "TEFENIXSAS"://FENIXSAS
										case "DEFENIXSAS"://FENIXSAS ?>
											<td class="name" style="font-size:14px;width:100px">
												<img src = "<?php echo $cPlesk_Skin_Directory ?>/logofenix.jpg" style="width:130">
											</td>
										<?php break;
										case "COLVANXX"://COLVANXX
										case "TECOLVANXX"://COLVANXX
										case "DECOLVANXX"://COLVANXX ?>
											<td class="name" style="font-size:14px;width:100px">
												<img src = "<?php echo $cPlesk_Skin_Directory ?>/logocolvan.jpg" style="width:130">
											</td>
										<?php break;
										case "INTERLAC"://INTERLAC
										case "TEINTERLAC"://INTERLAC
										case "DEINTERLAC"://INTERLAC ?>
											<td class="name" style="font-size:14px;width:100px">
												<img src = "<?php echo $cPlesk_Skin_Directory ?>/logointerlace.jpg" style="width:130">
											</td>
										<?php break;
										case "DHLEXPRE": //DHLEXPRE
										case "TEDHLEXPRE": //DHLEXPRE
										case "DEDHLEXPRE": //DHLEXPRE?>
											<td class="name" style="font-size:14px;width:100px">
												<img src = "<?php echo $cPlesk_Skin_Directory ?>/logo_dhl_express.jpg" style="height: 41px;width:156">
											</td>
										<?php break;
                    case "KARGORUX": //KARGORUX
                    case "TEKARGORUX": //KARGORUX
                    case "DEKARGORUX": //KARGORUX?>
                      <td class="name" style="font-size:14px;width:100px">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/logokargoru.jpg" style="height: 41px;width:156">
                      </td>
                    <?php break;
                    case "ALOGISAS": //LOGISTICA
                    case "TEALOGISAS": //LOGISTICA
                    case "DEALOGISAS": //LOGISTICA?>
                      <td class="name" style="font-size:14px;width:100px">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/logologisticasas.jpg" style="height: 41px;width:156">
                      </td>
                    <?php break;
                    case "PROSERCO": //PROSERCO
                    case "TEPROSERCO": //PROSERCO
                    case "DEPROSERCO": //PROSERCO?>
                      <td class="name" style="font-size:14px;width:100px">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/logoproserco.png" style="height: 41px;width:100">
                      </td>
                    <?php break;
                    case "FEDEXEXP": //FEDEX
                    case "TEFEDEXEXP": //FEDEX
                    case "DEFEDEXEXP": //FEDEX?>
                      <td class="name" style="font-size:14px;width:100px">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/logofedexexp.jpg" style="height: 60px;width:100">
                      </td>
                    <?php break;
                    case "EXPORCOM": //EXPORCOMEX
                    case "TEEXPORCOM": //EXPORCOMEX
                    case "DEEXPORCOM": //EXPORCOMEX?>
                      <td class="name" style="font-size:14px;width:100px">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/logoexporcomex.jpg" style="height: 50px;width:100">
                      </td>
                    <?php break;
                    case "HAYDEARX": //EXPORCOMEX
                    case "TEHAYDEARX": //EXPORCOMEX
                    case "DEHAYDEARX": //EXPORCOMEX ?>
                      <td class="name" style="font-size:14px;width:100px">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/logohaydear.jpeg" style="height: 70px;width:200">
                      </td>
                    <?php break;
                    case "CONNECTA":   //CONNECTA
                    case "DECONNECTA": //CONNECTA
                    case "TECONNECTA": //CONNECTA ?>
                      <td class="name" style="font-size:14px;width:100px">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/logoconnecta.jpg" style="height: 80px;width:120">
                      </td>
                    <?php break;
                    case "CONLOGIC":   //CONLOGIC
                    case "DECONLOGIC": //CONLOGIC
                    case "TECONLOGIC": //CONLOGIC ?>
                      <td class="name" style="font-size:14px;width:100px">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/logoconlogic.jpg" style="height: 80px;width:120">
                      </td>
                    <?php break;
                    case "OPENEBCO":   //OPENEBCO
                    case "DEOPENEBCO": //OPENEBCO
                    case "TEOPENEBCO": //OPENEBCO ?>
                      <td class="name" style="font-size:14px;width:100px">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/opentecnologia.JPG" style="height: 80px;width:200">
                      </td>
                    <?php break;
                  }?>
                  <td class="name" style="font-size:14px">
                    <center><br><span style="font-size:18px"> <?php echo "REPORTE DE ESTADO DE CARTERA AL ". $cFecha ?></span></center><br>
                  </td>
                </tr>
              </table>
              <table border = "1" cellpadding = "0" cellspacing = "0" width = "98%">
                <tr>
                  <td align="left" colspan = "8" bgcolor = "#96ADEB" style="font-size:14px">
                    <b>CONSULTA POR CLIENTE</b>
                  </td>
                </tr>
                <tr>
                  <td align="center" bgcolor = "#D6DFF7"><b>NIT</b></td>
                  <td align="center"><b><?php echo $cTerId."-".f_Digito_Verificacion($cTerId)?></b></td>
                  <td align="center" bgcolor = "#D6DFF7"><b>CLIENTE</b></td>
                  <td align="center"><b><?php echo $vNomCli['clinomxx']?></b></td>
                  <td align="center" bgcolor = "#D6DFF7"><b>TEL&Eacute;FONO</b></td>
                  <td align="center"><b><?php echo $vNomCli['CLITELXX']?></b></td>
                  <td align="center" bgcolor = "#D6DFF7" style="width:200px"><b>FECHA Y HORA DE CONSULTA</b></td>
                  <td align="center"><b><?php echo date("Y-m-d H:i:s");?></b></td>
                </tr>
              </table>
              <br>
              <table border = "1" cellpadding = "0" cellspacing = "0" width = "98%">
                <tr>
                  <td align="left" colspan = "<?php echo $nCol ?>" bgcolor = "#96ADEB" style="font-size:14px"><b>CARTERA VENCIDA</b></td>
                </tr>
                <tr>
                  <td align="center" bgcolor = "#D6DFF7"><b>Comprobante</b></td>
                  <?php if ($vSysStr['financiero_ver_do_reporte_estado_cartera'] == "SI") { ?>
                    <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>DO</b></td>
                  <?php } ?>
                  <?php if ($cColPed == "SI")  { ?>
                    <td align="center" bgcolor = "#D6DFF7"><b>Pedido</b></td>
                  <?php } ?>
                  <?php switch($cAlfa) {
                    case "DEAAINTERX":
                    case "TEAAINTERX":
                    case "AAINTERX": ?>
                      <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>DO</b></td>
                    <?php break;
                    default: ?>
                      <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Cuenta</b></td>
                    <?php break;
                  } ?>
                  <?php if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") { ?>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Fecha</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Entrega Cliente</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Vencimiento</b></td>
                  <?php } else { ?>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Fecha</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Vencimiento</b></td>
                  <?php } ?>
                  <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Cartera</b></td>
                  <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Vencidos</b></td>
                  <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Movimiento</b></td>
                  <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Saldo</b></td>
                </tr>
                <?php for($i=0;$i<count($mCarteraVencida);$i++){

                  if($mCarteraVencida[$i]['comidxxx'] == 'P' || $mCarteraVencida[$i]['comidxxx'] == 'L' || $mCarteraVencida[$i]['comidxxx'] == 'C'){
                    $cTipCom = $mComP[$mCarteraVencida[$i]['comidxxx']][$mCarteraVencida[$i]['comcodxx']];

                    if (in_array("{$mCarteraVencida[$i]['comidxxx']}~{$mCarteraVencida[$i]['comcodxx']}", $mRCM) == true) {
                      $cTipCom = "RCM";
                    }
                  }else{
                    $cTipCom = "";
                  } ?>
                  <tr>
                    <td align="left"><?php echo ($mCarteraVencida[$i]['document'] != "")?"<a href=\"javascript:f_Ver('{$mCarteraVencida[$i]['comidxxx']}','{$mCarteraVencida[$i]['comcodxx']}','{$mCarteraVencida[$i]['comcscxx']}','{$mCarteraVencida[$i]['comfecin']}','{$mCarteraVencida[$i]['regestxx']}','$cTipCom');\">{$mCarteraVencida[$i]['document']}</a>": "&nbsp;"; ?></td>
                    <?php if ($vSysStr['financiero_ver_do_reporte_estado_cartera'] == "SI") { ?>
                      <td align="center"><?php echo ($mCarteraVencida[$i]['docidxxx'] != "") ? $mCarteraVencida[$i]['docidxxx'] : "&nbsp;" ?></td>
                    <?php } ?>
                    <?php if ($cColPed == "SI")  { ?>
                      <td align="center"><?php echo ($mCarteraVencida[$i]['pedidoxx'] != "") ? $mCarteraVencida[$i]['pedidoxx'] : "&nbsp;" ?></td>
                    <?php } ?>
                    <td align="center"><?php echo ($mCarteraVencida[$i]['pucidxxx'] != "") ? $mCarteraVencida[$i]['pucidxxx'] : "&nbsp;" ?></td>
                    <td align="center"><?php echo ($mCarteraVencida[$i]['comfecxx'] != "") ? $mCarteraVencida[$i]['comfecxx'] : "&nbsp;" ?></td>
                    <?php if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") { ?>
                      <td align="center"><?php echo ($mCarteraVencida[$i]['comfecnx'] != "") ? $mCarteraVencida[$i]['comfecnx'] : "&nbsp;" ?></td>
                    <?php } ?>
                    <td align="center"><?php echo ($mCarteraVencida[$i]['comfecvn'] != "") ? $mCarteraVencida[$i]['comfecvn'] : "&nbsp;" ?></td>
                    <td align="center"><?php echo ($mCarteraVencida[$i]['diascart'] != "") ? $mCarteraVencida[$i]['diascart'] : "&nbsp;" ?></td>
                    <td align="center"><font color="red"><?php echo ($mCarteraVencida[$i]['diasvenc'] != "") ? $mCarteraVencida[$i]['diasvenc'] : "&nbsp;" ?></font></td>
                    <td align="center"><?php echo ($mCarteraVencida[$i]['commovxx'] != "") ? $mCarteraVencida[$i]['commovxx'] : "&nbsp;" ?></td>
                    <td align="right"><?php echo number_format($mCarteraVencida[$i]['saldoxxx'],2,",",".")?></td>
                  </tr>
                  <?php $nTotCarVencida += ($mCarteraVencida[$i]['commovxx'] == "D") ? $mCarteraVencida[$i]['saldoxxx'] : ($mCarteraVencida[$i]['saldoxxx'] * -1);
                } ?>
                <tr>
                  <td align="right" colspan = "<?php echo $nCol-1 ?>" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL CARTERA VENCIDA: </b></td>
                  <td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b><?php echo number_format($nTotCarVencida,2,",",".")?></b></td>
                </tr>
                <tr><td colspan = "<?php echo $nCol ?>">&nbsp;</td></tr>
                <tr>
                  <td align="left" colspan = "<?php echo $nCol ?>" bgcolor = "#96ADEB" style="font-size:14px"><b>CARTERA SIN VENCER</b></td>
                </tr>
                <tr>
                  <td align="center" bgcolor = "#D6DFF7"><b>Comprobante</b></td>
                  <?php if ($vSysStr['financiero_ver_do_reporte_estado_cartera'] == "SI") { ?>
                    <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>DO</b></td>
                  <?php } ?>
                  <?php if ($cColPed == "SI")  { ?>
                    <td align="center" bgcolor = "#D6DFF7"><b>Pedido</b></td>
                  <?php } ?>
                  <?php switch($cAlfa) {
                    case "DEAAINTERX":
                    case "TEAAINTERX":
                    case "AAINTERX": ?>
                      <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>DO</b></td>
                    <?php break;
                    default: ?>
                      <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Cuenta</b></td>
                    <?php break;
                  } ?>
                  <?php if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") { ?>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Fecha</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Entrega Cliente</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Vencimiento</b></td>
                  <?php } else { ?>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Fecha</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Vencimiento</b></td>
                  <?php } ?>
                  <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Cartera</b></td>
                  <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Vencidos</b></td>
                  <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Movimiento</b></td>
                  <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Saldo</b></td>
                </tr>
                <?php for($i=0;$i<count($mCarteraSinVencer);$i++){
                  if($mCarteraSinVencer[$i]['comidxxx'] == 'P' || $mCarteraSinVencer[$i]['comidxxx'] == 'L' || $mCarteraSinVencer[$i]['comidxxx'] == 'C'){
                    $cTipCom = $mComP[$mCarteraSinVencer[$i]['comidxxx']][$mCarteraSinVencer[$i]['comcodxx']];

                    if (in_array("{$mCarteraSinVencer[$i]['comidxxx']}~{$mCarteraSinVencer[$i]['comcodxx']}", $mRCM) == true) {
                      $cTipCom = "RCM";
                    }
                  }else{
                    $cTipCom = "";
                  } ?>
                  <tr>
                    <td align="left"><?php echo ($mCarteraSinVencer[$i]['document'] != "")?"<a href=\"javascript:f_Ver('{$mCarteraSinVencer[$i]['comidxxx']}','{$mCarteraSinVencer[$i]['comcodxx']}','{$mCarteraSinVencer[$i]['comcscxx']}','{$mCarteraSinVencer[$i]['comfecin']}','{$mCarteraSinVencer[$i]['regestxx']}','$cTipCom')\">{$mCarteraSinVencer[$i]['document']}</a>": "&nbsp;"; ?></td>
                    <?php if ($vSysStr['financiero_ver_do_reporte_estado_cartera'] == "SI") { ?>
                      <td align="center"><?php echo ($mCarteraSinVencer[$i]['docidxxx'] != "") ? $mCarteraSinVencer[$i]['docidxxx'] : "&nbsp;" ?></td>
                    <?php } ?>
                    <?php if ($cColPed == "SI")  { ?>
                      <td align="center"><?php echo ($mCarteraSinVencer[$i]['pedidoxx'] != "") ? $mCarteraSinVencer[$i]['pedidoxx'] : "&nbsp;" ?></td>
                    <?php } ?>
                    <td align="center"><?php echo ($mCarteraSinVencer[$i]['pucidxxx'] != "") ? $mCarteraSinVencer[$i]['pucidxxx'] : "&nbsp;" ?></td>
                    <td align="center"><?php echo ($mCarteraSinVencer[$i]['comfecxx'] != "") ? $mCarteraSinVencer[$i]['comfecxx'] : "&nbsp;"; ?></td>
                    <?php if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") { ?>
                      <td align="center"><?php echo ($mCarteraSinVencer[$i]['comfecnx'] != "") ? $mCarteraSinVencer[$i]['comfecnx'] : "&nbsp;" ?></td>
                    <?php } ?>
                    <td align="center"><?php echo ($mCarteraSinVencer[$i]['comfecvn'] != "") ? $mCarteraSinVencer[$i]['comfecvn'] : "&nbsp;"; ?></td>
                    <td align="center"><?php echo ($mCarteraSinVencer[$i]['diascart'] != "") ? $mCarteraSinVencer[$i]['diascart'] : "&nbsp;"; ?></td>
                    <td align="center"><?php echo ($mCarteraSinVencer[$i]['diasvenc'] != "") ? $mCarteraSinVencer[$i]['diasvenc'] : "&nbsp;"; ?></td>
                    <td align="center"><?php echo ($mCarteraSinVencer[$i]['commovxx'] != "") ? $mCarteraSinVencer[$i]['commovxx'] : "&nbsp;"; ?></td>
                    <td align="right"><?php echo number_format($mCarteraSinVencer[$i]['saldoxxx'],2,",",".")?></td>
                  </tr>
                  <?php $nTotCartera += ($mCarteraSinVencer[$i]['commovxx'] == "D") ? $mCarteraSinVencer[$i]['saldoxxx'] : ($mCarteraSinVencer[$i]['saldoxxx'] * -1);
                } ?>
                <tr>
                  <td align="right" colspan = "<?php echo $nCol-1 ?>" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL CARTERA SIN VENCER: </b></td>
                  <td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b><?php echo number_format($nTotCartera,2,",",".")?></b></td>
                </tr>
                <tr><td colspan = "<?php echo $nCol ?>">&nbsp;</td></tr>
                <tr>
                  <td align="left" colspan = "<?php echo $nCol ?>" bgcolor = "#96ADEB" style="font-size:14px"><b>SALDOS A FAVOR</b></td>
                </tr>
                <tr>
                  <td align="center" bgcolor = "#D6DFF7"><b>Comprobante</b></td>
                  <?php if ($vSysStr['financiero_ver_do_reporte_estado_cartera'] == "SI") { ?>
                    <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>DO</b></td>
                  <?php } ?>
                  <?php if ($cColPed == "SI")  { ?>
                    <td align="center" bgcolor = "#D6DFF7"><b>Pedido</b></td>
                  <?php } ?>
                  <?php switch($cAlfa) {
                    case "DEAAINTERX":
                    case "TEAAINTERX":
                    case "AAINTERX": ?>
                      <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>DO</b></td>
                    <?php break;
                    default: ?>
                      <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Cuenta</b></td>
                    <?php break;
                  } ?>
                  <?php if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") { ?>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Fecha</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Entrega Cliente</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Vencimiento</b></td>
                  <?php } else { ?>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Fecha</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Vencimiento</b></td>
                  <?php } ?>
                  <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Cartera</b></td>
                  <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Vencidos</b></td>
                  <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Movimiento</b></td>
                  <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Saldo</b></td>
                </tr>
                <?php for($i=0;$i<count($mSaldosaFavor);$i++){
                  if($mSaldosaFavor[$i]['comidxxx'] == 'P' || $mSaldosaFavor[$i]['comidxxx'] == 'L' || $mSaldosaFavor[$i]['comidxxx'] == 'C'){
                    $cTipCom = $mComP[$mSaldosaFavor[$i]['comidxxx']][$mSaldosaFavor[$i]['comcodxx']];

                    if (in_array("{$mSaldosaFavor[$i]['comidxxx']}~{$mSaldosaFavor[$i]['comcodxx']}", $mRCM) == true) {
                      $cTipCom = "RCM";
                    }
                  }else{
                    $cTipCom = "";
                  } ?>
                  <tr>
                    <td align="left"><?php echo ($mSaldosaFavor[$i]['document'] != "")?"<a href=\"javascript:f_Ver('{$mSaldosaFavor[$i]['comidxxx']}','{$mSaldosaFavor[$i]['comcodxx']}','{$mSaldosaFavor[$i]['comcscxx']}','{$mSaldosaFavor[$i]['comfecin']}','{$mSaldosaFavor[$i]['regestxx']}','$cTipCom')\">{$mSaldosaFavor[$i]['document']}</a>": "&nbsp;"; ?></td>
                    <?php if ($vSysStr['financiero_ver_do_reporte_estado_cartera'] == "SI") { ?>
                      <td align="center"><?php echo ($mSaldosaFavor[$i]['docidxxx'] != "") ? $mSaldosaFavor[$i]['docidxxx'] : "&nbsp;" ?></td>
                    <?php } ?>
                    <?php if ($cColPed == "SI")  { ?>
                      <td align="center"><?php echo ($mSaldosaFavor[$i]['pedidoxx'] != "") ? $mSaldosaFavor[$i]['pedidoxx'] : "&nbsp;" ?></td>
                    <?php } ?>
                    <td align="center"><?php echo $mSaldosaFavor[$i]['pucidxxx']?></td>
                    <td align="center"><?php echo ($mSaldosaFavor[$i]['comfecxx'] != "") ? $mSaldosaFavor[$i]['comfecxx'] : "&nbsp;" ?></td>
                    <?php if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") { ?>
                      <td align="center"><?php echo ($mSaldosaFavor[$i]['comfecnx'] != "") ? $mSaldosaFavor[$i]['comfecnx'] : "&nbsp;" ?></td>
                    <?php } ?>
                    <td align="center"><?php echo ($mSaldosaFavor[$i]['comfecvn'] != "") ? $mSaldosaFavor[$i]['comfecvn'] : "&nbsp;" ?></td>
                    <td align="center"><?php echo ($mSaldosaFavor[$i]['diascart'] != "") ? $mSaldosaFavor[$i]['diascart'] : "&nbsp;" ?></td>
                    <td align="center"><?php echo ($mSaldosaFavor[$i]['diasvenc'] != "") ? $mSaldosaFavor[$i]['diasvenc'] : "&nbsp;" ?></td>
                    <td align="center"><?php echo ($mSaldosaFavor[$i]['commovxx'] != "") ? $mSaldosaFavor[$i]['commovxx'] : "&nbsp;" ?></td>
                    <td align="right"><?php echo number_format($mSaldosaFavor[$i]['saldoxxx'],2,",",".")?></td>
                  </tr>
                  <?php $nTotSaldos += ($mSaldosaFavor[$i]['commovxx'] == "D") ? $mSaldosaFavor[$i]['saldoxxx'] : ($mSaldosaFavor[$i]['saldoxxx'] * -1);
                } ?>
                <tr>
                  <td align="right" colspan = "<?php echo $nCol-1 ?>" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL SALDOS A FAVOR: </b></td>
                  <td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b><?php echo number_format($nTotSaldos,2,",",".")?></b></td>
                </tr>
                <tr><td colspan = "<?php echo $nCol ?>">&nbsp;</td></tr>
                <tr>
                  <td align="left" colspan = "<?php echo $nCol ?>" bgcolor = "#96ADEB" style="font-size:14px"><b>RECIBOS PROVISIONALES</b></td>
                </tr>
                <tr>
                  <td align="center" bgcolor = "#D6DFF7"<?php echo ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") ? " colspan=\"$nColProv\"" : ""; ?>><b>Comprobante</b></td>
                  <?php if ($vSysStr['financiero_ver_do_reporte_estado_cartera'] == "SI") { ?>
                    <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>DO</b></td>
                  <?php } ?>
                  <?php switch($cAlfa) {
                    case "DEAAINTERX":
                    case "TEAAINTERX":
                    case "AAINTERX": ?>
                      <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>DO</b></td>
                    <?php break;
                    default: ?>
                      <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Cuenta</b></td>
                    <?php break;
                  } ?>
                  <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Fecha</b></td>
                  <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Vencimiento</b></td>
                  <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Cartera</b></td>
                  <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Vencidos</b></td>
                  <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Movimiento</b></td>
                  <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Saldo</b></td>
                </tr>
                <?php for($i=0;$i<count($mRecProv);$i++){
                  if($mRecProv[$i]['comidxxx'] == 'P' || $mRecProv[$i]['comidxxx'] == 'L' || $mRecProv[$i]['comidxxx'] == 'C'){
                    $cTipCom = $mComP[$mRecProv[$i]['comidxxx']][$mRecProv[$i]['comcodxx']];

                    if (in_array("{$mRecProv[$i]['comidxxx']}~{$mRecProv[$i]['comcodxx']}", $mRCM) == true) {
                      $cTipCom = "RCM";
                    }
                  }else{
                    $cTipCom = "";
                  }?>
                  <tr>
                    <td align="left"<?php echo ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") ? " colspan=\"$nColProv\"" : ""; ?>><?php echo ($mRecProv[$i]['document'] != "")?"<a href=\"javascript:f_Ver('{$mRecProv[$i]['comidxxx']}','{$mRecProv[$i]['comcodxx']}','{$mRecProv[$i]['comcscxx']}','{$mRecProv[$i]['comfecin']}','PROVISIONAL','$cTipCom')\">{$mRecProv[$i]['document']}</a>": "&nbsp;"; ?></td>
                    <?php if ($vSysStr['financiero_ver_do_reporte_estado_cartera'] == "SI") { ?>
                      <td align="center"><?php echo "&nbsp;" ?></td>
                    <?php } ?>
                    <td align="center"><?php echo ($mRecProv[$i]['pucidxxx'] != "") ? $mRecProv[$i]['pucidxxx'] : "&nbsp;" ?></td>
                    <td align="center"><?php echo ($mRecProv[$i]['comfecxx'] != "") ? $mRecProv[$i]['comfecxx'] : "&nbsp;" ?></td>
                    <td align="center"><?php echo ($mRecProv[$i]['comfecvn'] != "") ? $mRecProv[$i]['comfecvn'] : "&nbsp;" ?></td>
                    <td align="center"><?php echo ($mRecProv[$i]['diascart'] != "") ? $mRecProv[$i]['diascart'] : "&nbsp;" ?></td>
                    <td align="center"><?php echo ($mRecProv[$i]['diasvenc'] != "") ? $mRecProv[$i]['diasvenc'] : "&nbsp;" ?></td>
                    <td align="center"><?php echo ($mRecProv[$i]['commovxx'] != "") ? $mRecProv[$i]['commovxx'] : "&nbsp;" ?></td>
                    <td align="right"><?php echo number_format($mRecProv[$i]['saldoxxx'],2,",",".")?></td>
                  </tr>
                  <?php $nTotProvicionales += ($mRecProv[$i]['commovxx'] == "D") ? $mRecProv[$i]['saldoxxx'] : ($mRecProv[$i]['saldoxxx'] * -1);
                } ?>
                <tr>
                  <td align="right" colspan = "<?php echo $nCol-1 ?>" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL SALDOS PROVISIONALES: </b></td>
                  <td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b><?php echo number_format($nTotProvicionales,2,",",".")?></b></td>
                </tr>
                <tr><td colspan = "<?php echo $nCol ?>">&nbsp;</td></tr>
                <tr>
                  <td align="right" colspan = "<?php echo $nCol-1 ?>" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL SALDOS A FAVOR: </b></td>
                  <td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b><?php echo number_format($nTotSaldos,2,",",".")?></b></td>
                </tr>
                <tr>
                  <td align="right" colspan = "<?php echo $nCol-1 ?>" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL CARTERA: </b></td>
                  <td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b><?php echo number_format(($nTotCarVencida+$nTotCartera),2,",",".")?></b></td>
                </tr>
                <?php $mNomTotales = array();
                (abs($nTotCarVencida+$nTotCartera)>0) ? $mNomTotales[count($mNomTotales)] ="TOTAL CARTERA" :"";
                (abs($nTotSaldos)>0) ? $mNomTotales[count($mNomTotales)] ="SALDOS A FAVOR" :"";
                (abs($nTotProvicionales)>0) ? $mNomTotales[count($mNomTotales)]="RECIBOS PROVISIONALES" :"";

                $mTitulo="";

                for($j=0;$j <= (count($mNomTotales)-1);$j++){
                  $mTitulo .= $mNomTotales[$j];
                  ($j==(count($mNomTotales)-1)) ? "" : $mTitulo .=" - ";
                } ?>
                <tr>
                  <td align="right" colspan = "<?php echo $nCol-1 ?>" bgcolor = "#A6DEEE" style="font-size:12px"><b><?php echo $mTitulo.":" ?></b></td>
                  <td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b><?php echo number_format((($nTotCarVencida+$nTotCartera) - ($nTotProvicionales) + ($nTotSaldos)),2,",",".")?></b></td>
                </tr>
              </table>
            </center>
          </body>
        </html>
      <?php break;
      case 2:
        // PINTA POR EXCEL//

        /**
         * Variable para armar la cadena de texto que se envia al excel
         * @var Text
         */
        $header .= 'Reporte Estado de Cartera'."\n";
        $header .= "\n";
        $cData = '';
        $title = "ESTADO_DE_CUENTA_".$kUser."_".date('YmdHis').".xls";

        $nCol     = ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") ? 9 : 8;
        $nCol     = ($cColPed == "SI") ? ($nCol + 1) : $nCol;
        $nCol     = ($vSysStr['financiero_ver_do_reporte_estado_cartera'] == "SI") ? ($nCol + 1) : $nCol;
        $nColProv = ($cColPed == "SI") ? 3 : 2;

        $cData .= '<table border = "1" cellpadding = "0" cellspacing = "0" width = "1200px">';
          $cData .= '<tr>';
            $cData .= '<td class="name" colspan = "'.$nCol.'" style="font-size:14px">';
              $cData .= '<center><span style="font-size:18px">REPORTE DE ESTADO DE CARTERA AL '.$cFecha.'</span></center>';
            $cData .= '</td>';
          $cData .= '</tr>';
          $cData .= '<tr>';
            $cData .= '<td align="left" colspan = "'.$nCol.'" bgcolor = "#96ADEB" style="font-size:14px"><b>CONSULTA POR CLIENTE</b></td>';
          $cData .= '</tr>';
          $cData .= '<tr>';
            $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:50px"><b>NIT</b></td>';
            $cData .= '<td align="center" style="width:100px"><b>'.$cTerId."-".f_Digito_Verificacion($cTerId).'</b></td>';
            $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Cliente</b></td>';
            $cData .= '<td align="center"><b>'.($vNomCli['clinomxx']).'</b></td>';
            $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Tel&eacute;fono</b></td>';
            $cData .= '<td align="center"'.(($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") ? "colspan=\"2\"" : "").'><b>'.($vNomCli['CLITELXX']).'</b></td>';
            $cData .= '<td align="center" bgcolor = "#D6DFF7"><b>Fecha y Hora de Consulta</b></td>';
            $cData .= '<td align="center" style="width:120px"><b>'.date("Y-m-d H:i:s").'</b></td>';
          $cData .= '</tr>';
          $cData .= '<tr><td colspan = "'.$nCol.'"></td></tr>';
          $cData .= '<tr>';
            $cData .= '<td align="left" colspan = "'.$nCol.'" bgcolor = "#96ADEB" style="font-size:14px"><b>CARTERA VENCIDA</b></td>';
          $cData .= '</tr>';
          $cData .= '<tr>';
            $cData .= '<td align="center" bgcolor = "#D6DFF7"><b>Comprobante</b></td>';
            if ($vSysStr['financiero_ver_do_reporte_estado_cartera'] == "SI") {
              $cData .= '<td align="center" bgcolor = "#D6DFF7"><b>DO</b></td>';
            }
            if ($cColPed == "SI")  {
              $cData .= '<td align="center" bgcolor = "#D6DFF7"><b>Pedido</b></td>';
            }
            switch($cAlfa) {
              case "DEAAINTERX":
              case "TEAAINTERX":
              case "AAINTERX":
                $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>DO</b></td>';
              break;
              default:
                $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Cuenta</b></td>';
              break;
            }            
            if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") {
            $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Fecha</b></td>';
            $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Entrega Cliente</b></td>';
            $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Vencimiento</b></td>';
            } else {
            $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Fecha</b></td>';
            $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Vencimiento</b></td>';
            }
            $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Cartera</b></td>';
            $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Vencidos</b></td>';
            $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Movimiento</b></td>';
            $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Saldo</b></td>';
          $cData .= '</tr>';
          for($i=0;$i<count($mCarteraVencida);$i++) {
            if($mCarteraVencida[$i]['comidxxx'] == 'P' || $mCarteraVencida[$i]['comidxxx'] == 'L' || $mCarteraVencida[$i]['comidxxx'] == 'C'){
              $cTipCom = $mComP[$mCarteraVencida[$i]['comidxxx']][$mCarteraVencida[$i]['comcodxx']];

              if (in_array("{$mCarteraVencida[$i]['comidxxx']}~{$mCarteraVencida[$i]['comcodxx']}", $mRCM) == true) {
                $cTipCom = "RCM";
              }
            }else{
              $cTipCom = "";
            }
            $cData .= '<tr>';
              $cData .= '<td align="left"   style="mso-number-format:\'\@\'">'.(($mCarteraVencida[$i]['document'] != "") ? $mCarteraVencida[$i]['document'] : "").'</td>';
              if ($vSysStr['financiero_ver_do_reporte_estado_cartera'] == "SI") {
                $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mCarteraVencida[$i]['docidxxx'] != "") ? $mCarteraVencida[$i]['docidxxx'] : "").'</td>';
              }
              if ($cColPed == "SI")  {
                $cData .= '<td align="center">'.(($mCarteraVencida[$i]['pedidoxx'] != "") ? $mCarteraVencida[$i]['pedidoxx'] : "").'</td>';
              }
              $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mCarteraVencida[$i]['pucidxxx'] != "") ? $mCarteraVencida[$i]['pucidxxx'] : "").'</td>';
              $cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mCarteraVencida[$i]['comfecxx'] != "") ? $mCarteraVencida[$i]['comfecxx'] : "").'</td>';
              if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") {
              $cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mCarteraVencida[$i]['comfecnx'] != "") ? $mCarteraVencida[$i]['comfecnx'] : "").'</td>';
              }
              $cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mCarteraVencida[$i]['comfecvn'] != "") ? $mCarteraVencida[$i]['comfecvn'] : "").'</td>';
              $cData .= '<td align="center">'.(($mCarteraVencida[$i]['diascart'] != "") ? $mCarteraVencida[$i]['diascart'] : "").'</td>';
              $cData .= '<td align="center"><font color="red">'.(($mCarteraVencida[$i]['diasvenc'] != "") ? $mCarteraVencida[$i]['diasvenc'] : "").'</font></td>';
              $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mCarteraVencida[$i]['commovxx'] != "") ? $mCarteraVencida[$i]['commovxx'] : "").'</td>';
              $cData .= '<td align="right">'.(number_format($mCarteraVencida[$i]['saldoxxx'],2,",",".")).'</td>';
            $cData .= '</tr>';
            $nTotCarVencida += ($mCarteraVencida[$i]['commovxx'] == "D") ? $mCarteraVencida[$i]['saldoxxx'] : ($mCarteraVencida[$i]['saldoxxx'] * -1);
          }
          $cData .= '<tr>';
            $cData .= '<td align="right" colspan = "'.($nCol-1).'" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL CARTERA VENCIDA: </b></td>';
            $cData .= '<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b>'.(number_format($nTotCarVencida,2,",",".")).'</b></td>';
          $cData .= '</tr>';
          $cData .= '<tr><td colspan = "'.$nCol.'"></td></tr>';
          $cData .= '<tr>';
            $cData .= '<td align="left" colspan = "'.$nCol.'" bgcolor = "#96ADEB" style="font-size:14px"><b>CARTERA SIN VENCER</b></td>';
          $cData .= '</tr>';
          $cData .= '<tr>';
            $cData .= '<td align="center" bgcolor = "#D6DFF7"><b>Comprobante</b></td>';
            if ($vSysStr['financiero_ver_do_reporte_estado_cartera'] == "SI") {
              $cData .= '<td align="center" bgcolor = "#D6DFF7"><b>DO</b></td>';
            }
            if ($cColPed == "SI")  {
              $cData .= '<td align="center" bgcolor = "#D6DFF7"><b>Pedido</b></td>';
            }
            switch($cAlfa) {
              case "DEAAINTERX":
              case "TEAAINTERX":
              case "AAINTERX":
                $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>DO</b></td>';
              break;
              default:
                $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Cuenta</b></td>';
              break;
            }   
            if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") {
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Fecha</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Entrega Cliente</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Vencimiento</b></td>';
            } else {
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Fecha</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Vencimiento</b></td>';
            }
            $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Cartera</b></td>';
            $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Vencidos</b></td>';
            $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Movimiento</b></td>';
            $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Saldo</b></td>';
          $cData .= '</tr>';
          for($i=0;$i<count($mCarteraSinVencer);$i++){
            if($mCarteraSinVencer[$i]['comidxxx'] == 'P' || $mCarteraSinVencer[$i]['comidxxx'] == 'L' || $mCarteraSinVencer[$i]['comidxxx'] == 'C'){
              $cTipCom = $mComP[$mCarteraSinVencer[$i]['comidxxx']][$mCarteraSinVencer[$i]['comcodxx']];

              if (in_array("{$mCarteraSinVencer[$i]['comidxxx']}~{$mCarteraSinVencer[$i]['comcodxx']}", $mRCM) == true) {
                $cTipCom = "RCM";
              }
            }else{
              $cTipCom = "";
            }
            $cData .= '<tr>';
            $cData .= '<td align="left"   style="mso-number-format:\'\@\'">'.(($mCarteraSinVencer[$i]['document'] != "")? $mCarteraSinVencer[$i]['document'] : "").'</td>';
            if ($vSysStr['financiero_ver_do_reporte_estado_cartera'] == "SI") {
              $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mCarteraSinVencer[$i]['docidxxx'] != "") ? $mCarteraSinVencer[$i]['docidxxx'] : "").'</td>';
            }
            if ($cColPed == "SI")  {
              $cData .= '<td align="center">'.(($mCarteraSinVencer[$i]['pedidoxx'] != "") ? $mCarteraSinVencer[$i]['pedidoxx'] : "").'</td>';
            }
            $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mCarteraSinVencer[$i]['pucidxxx'] != "") ? $mCarteraSinVencer[$i]['pucidxxx'] : "").'</td>';
            $cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mCarteraSinVencer[$i]['comfecxx'] != "") ? $mCarteraSinVencer[$i]['comfecxx'] : "").'</td>';
            if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") {
              $cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mCarteraSinVencer[$i]['comfecnx'] != "") ? $mCarteraSinVencer[$i]['comfecnx'] : "").'</td>';
            }
            $cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mCarteraSinVencer[$i]['comfecvn'] != "") ? $mCarteraSinVencer[$i]['comfecvn'] : "").'</td>';
            $cData .= '<td align="center">'.(($mCarteraSinVencer[$i]['diascart'] != "") ? $mCarteraSinVencer[$i]['diascart'] : "").'</td>';
            $cData .= '<td align="center">'.(($mCarteraSinVencer[$i]['diasvenc'] != "") ? $mCarteraSinVencer[$i]['diasvenc'] : "").'</td>';
            $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mCarteraSinVencer[$i]['commovxx'] != "") ? $mCarteraSinVencer[$i]['commovxx'] : "").'</td>';
            $cData .= '<td align="right">'.(number_format($mCarteraSinVencer[$i]['saldoxxx'],2,",",".")).'</td>';
            $cData .= '</tr>';
            $nTotCartera += ($mCarteraSinVencer[$i]['commovxx'] == "D") ? $mCarteraSinVencer[$i]['saldoxxx'] : ($mCarteraSinVencer[$i]['saldoxxx'] * -1);
          }
          $cData .= '<tr>';
            $cData .= '<td align="right" colspan = "'.($nCol-1).'" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL CARTERA SIN VENCER: </b></td>';
            $cData .= '<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b>'.(number_format($nTotCartera,2,",",".")).'</b></td>';
          $cData .= '</tr>';
          $cData .= '<tr><td colspan = "'.$nCol.'"></td></tr>';
          $cData .= '<tr>';
            $cData .= '<td align="left" colspan = "'.$nCol.'" bgcolor = "#96ADEB" style="font-size:14px"><b>SALDOS A FAVOR</b></td>';
          $cData .= '</tr>';
          $cData .= '<tr>';
            $cData .= '<td align="center" bgcolor = "#D6DFF7"><b>Comprobante</b></td>';
            if ($vSysStr['financiero_ver_do_reporte_estado_cartera'] == "SI") {
              $cData .= '<td align="center" bgcolor = "#D6DFF7"><b>DO</b></td>';
            }
            if ($cColPed == "SI")  {
              $cData .= '<td align="center" bgcolor = "#D6DFF7"><b>Pedido</b></td>';
            }
            switch($cAlfa) {
              case "DEAAINTERX":
              case "TEAAINTERX":
              case "AAINTERX":
                $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>DO</b></td>';
              break;
              default:
                $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Cuenta</b></td>';
              break;
            }  
            if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") {
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Fecha</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Entrega Cliente</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Vencimiento</b></td>';
            } else {
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Fecha</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Vencimiento</b></td>';
            }
            $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Cartera</b></td>';
            $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Vencidos</b></td>';
            $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Movimiento</b></td>';
            $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Saldo</b></td>';
          $cData .= '</tr>';
          for($i=0;$i<count($mSaldosaFavor);$i++) {
            if($mSaldosaFavor[$i]['comidxxx'] == 'P' || $mSaldosaFavor[$i]['comidxxx'] == 'L' || $mSaldosaFavor[$i]['comidxxx'] == 'C'){
              $cTipCom = $mComP[$mSaldosaFavor[$i]['comidxxx']][$mSaldosaFavor[$i]['comcodxx']];

              if (in_array("{$mSaldosaFavor[$i]['comidxxx']}~{$mSaldosaFavor[$i]['comcodxx']}", $mRCM) == true) {
                $cTipCom = "RCM";
              }
            }else{
              $cTipCom = "";
            }
            $cData .= '<tr>';
              $cData .= '<td align="left"   style="mso-number-format:\'\@\'">'.(($mSaldosaFavor[$i]['document'] != "")? $mSaldosaFavor[$i]['document'] : "").'</td>';
              if ($vSysStr['financiero_ver_do_reporte_estado_cartera'] == "SI") {
                $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mSaldosaFavor[$i]['docidxxx'] != "") ? $mSaldosaFavor[$i]['docidxxx'] : "").'</td>';
              }
              if ($cColPed == "SI")  {
                $cData .= '<td align="center">'.(($mSaldosaFavor[$i]['pedidoxx'] != "") ? $mSaldosaFavor[$i]['pedidoxx'] : "").'</td>';
              }
              $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.($mSaldosaFavor[$i]['pucidxxx']).'</td>';
              $cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mSaldosaFavor[$i]['comfecxx'] != "") ? $mSaldosaFavor[$i]['comfecxx'] : "").'</td>';
              if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") {
                $cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mSaldosaFavor[$i]['comfecnx'] != "") ? $mSaldosaFavor[$i]['comfecnx'] : "").'</td>';
              }
              $cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mSaldosaFavor[$i]['comfecvn'] != "") ? $mSaldosaFavor[$i]['comfecvn'] : "").'</td>';
              $cData .= '<td align="center">'.(($mSaldosaFavor[$i]['diascart'] != "") ? $mSaldosaFavor[$i]['diascart'] : "").'</td>';
              $cData .= '<td align="center">'.(($mSaldosaFavor[$i]['diasvenc'] != "") ? $mSaldosaFavor[$i]['diasvenc'] : "").'</td>';
              $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mSaldosaFavor[$i]['commovxx'] != "") ? $mSaldosaFavor[$i]['commovxx'] : "").'</td>';
              $cData .= '<td align="right">'.(number_format($mSaldosaFavor[$i]['saldoxxx'],2,",",".")).'</td>';
            $cData .= '</tr>';
            $nTotSaldos += ($mSaldosaFavor[$i]['commovxx'] == "D") ? $mSaldosaFavor[$i]['saldoxxx'] : ($mSaldosaFavor[$i]['saldoxxx'] * -1);
          }
          $cData .= '<tr>';
            $cData .= '<td align="right" colspan = "'.($nCol-1).'" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL SALDOS A FAVOR: </b></td>';
            $cData .= '<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b>'.(number_format($nTotSaldos,2,",",".")).'</b></td>';
          $cData .= '</tr>';
          $cData .= '<tr><td colspan = "'.$nCol.'"></td></tr>';
          $cData .= '<tr>';
            $cData .= '<td align="left" colspan = "'.$nCol.'" bgcolor = "#96ADEB" style="font-size:14px"><b>RECIBOS PROVISIONALES</b></td>';
          $cData .= '</tr>';
          $cData .= '<tr>';
            $cData .= '<td align="center" bgcolor = "#D6DFF7"'.(($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") ? "colspan=\"$nColProv\"" : "").'<b>Comprobante</b></td>';
            if ($vSysStr['financiero_ver_do_reporte_estado_cartera'] == "SI") {
              $cData .= '<td align="center" bgcolor = "#D6DFF7"><b>DO</b></td>';
            }
            switch($cAlfa) {
              case "DEAAINTERX":
              case "TEAAINTERX":
              case "AAINTERX":
                $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>DO</b></td>';
              break;
              default:
                $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Cuenta</b></td>';
              break;
            }
            $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Fecha</b></td>';
            $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Vencimiento</b></td>';
            $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Cartera</b></td>';
            $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Vencidos</b></td>';
            $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Movimiento</b></td>';
            $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Saldo</b></td>';
          $cData .= '</tr>';
          for($i=0;$i<count($mRecProv);$i++) {
            if($mRecProv[$i]['comidxxx'] == 'P' || $mRecProv[$i]['comidxxx'] == 'L' || $mRecProv[$i]['comidxxx'] == 'C'){
              $cTipCom = $mComP[$mRecProv[$i]['comidxxx']][$mRecProv[$i]['comcodxx']];

              if (in_array("{$mRecProv[$i]['comidxxx']}~{$mRecProv[$i]['comcodxx']}", $mRCM) == true) {
                $cTipCom = "RCM";
              }
            }else{
              $cTipCom = "";
            }
          $cData .= '<tr>';
            $cData .= '<td align="left" style="mso-number-format:\'\@\'"'.(($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") ? "colspan=\"$nColProv\"" : "").'>'.(($mRecProv[$i]['document'] != "")?$mRecProv[$i]['document'] : "").'</td>';
            if ($vSysStr['financiero_ver_do_reporte_estado_cartera'] == "SI") {
              $cData .= '<td align="center" style="mso-number-format:\'\@\'">'."".'</td>';
            }
            $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mRecProv[$i]['pucidxxx'] != "") ? $mRecProv[$i]['pucidxxx'] : "").'</td>';
            $cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mRecProv[$i]['comfecxx'] != "") ? $mRecProv[$i]['comfecxx'] : "").'</td>';
            $cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mRecProv[$i]['comfecvn'] != "") ? $mRecProv[$i]['comfecvn'] : "").'</td>';
            $cData .= '<td align="center">'.(($mRecProv[$i]['diascart'] != "") ? $mRecProv[$i]['diascart'] : "").'</td>';
            $cData .= '<td align="center">'.(($mRecProv[$i]['diasvenc'] != "") ? $mRecProv[$i]['diasvenc'] : "").'</td>';
            $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mRecProv[$i]['commovxx'] != "") ? $mRecProv[$i]['commovxx'] : "").'</td>';
            $cData .= '<td align="right">'.(number_format($mRecProv[$i]['saldoxxx'],2,",",".")).'</td>';
          $cData .= '</tr>';
          $nTotProvicionales += ($mRecProv[$i]['commovxx'] == "D") ? $mRecProv[$i]['saldoxxx'] : ($mRecProv[$i]['saldoxxx'] * -1);
          }
          $cData .= '<tr>';
            $cData .= '<td align="right" colspan = "'.($nCol-1).'" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL SALDOS PROVISIONALES: </b></td>';
            $cData .= '<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b>'.(number_format($nTotProvicionales,2,",",".")).'</b></td>';
          $cData .= '</tr>';
          $cData .= '<tr><td colspan = "'.$nCol.'"></td></tr>';
          $cData .= '<tr>';
            $cData .= '<td align="right" colspan = "'.($nCol-1).'" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL SALDOS A FAVOR: </b></td>';
            $cData .= '<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b>'.(number_format($nTotSaldos,2,",",".")).'</b></td>';
          $cData .= '</tr>';
          $cData .= '<tr>';
            $cData .= '<td align="right" colspan = "'.($nCol-1).'" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL CARTERA: </b></td>';
            $cData .= '<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b>'.(number_format(($nTotCarVencida+$nTotCartera),2,",",".")).'</b></td>';
          $cData .= '</tr>';
          $mNomTotales = array();
          (abs($nTotCarVencida+$nTotCartera)>0) ? $mNomTotales[count($mNomTotales)] ="TOTAL CARTERA" :"";
          (abs($nTotSaldos)>0) ? $mNomTotales[count($mNomTotales)] ="SALDOS A FAVOR" :"";
          (abs($nTotProvicionales)>0) ? $mNomTotales[count($mNomTotales)]="RECIBOS PROVISIONALES" :"";

          $mTitulo="";

          for($j=0;$j <= (count($mNomTotales)-1);$j++){
            $mTitulo .= $mNomTotales[$j];
            ($j==(count($mNomTotales)-1)) ? "" : $mTitulo .=" - ";
           }
          $cData .= '<tr>';
            $cData .= '<td align="right" colspan = "'.($nCol-1).'" bgcolor = "#A6DEEE" style="font-size:12px"><b>'.($mTitulo.":" ).'</b></td>';
            $cData .= '<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b>'.(number_format((($nTotCarVencida+$nTotCartera) - ($nTotProvicionales) + ($nTotSaldos)),2,",",".")).'</b></td>';
          $cData .= '</tr>';
        $cData .= '</table>';

        if ($cData == "") {
          f_Mensaje(__FILE__,__LINE__,"Error al Generar el Archivo Excel."); ?>
          <script languaje = "javascript">
            window.close();
          </script>
        <?php } else {
          header("Pragma: public");
          header("Expires: 0");
          header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
          header("Cache-Control: private",false); // required for certain browsers
          header("Content-type: application/octet-stream");
          header("Content-Disposition: attachment; filename=\"".basename($title)."\";");
          print $cData;
        }
      break;
      default:

        define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
        require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');

        class PDF extends FPDF {
          function Header() {
          }

          function Footer(){
            $this->SetY(-10);
            $this->SetFont('Arial','',6);
            $this->Cell(0,5,'PAGINA '.$this->PageNo().' DE {nb}',0,0,'C');
          }
        }

        $pdf = new PDF('L','mm','Letter');
        $pdf->AliasNbPages();
        $pdf->SetMargins(5,5,5);
        $pdf->SetAutoPageBreak(true,10);
        $pdf->AddPage();
        $pdf->AddFont('otfon1','','otfon1.php');

        $pdf->SetFont('Arial','B',12);

        if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") {
          $pdf->setXY(10,15);
          $pdf->Cell(268,10,"REPORTE DE ESTADO DE CARTERA AL ". $cFecha,0,0,'C');
          $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_repcar.jpg',10,10,32,22);
          $pdf->setXY(10,25);
          $pdf->Rect(5,10,268,22);
          $nPosy = 35;
        } else {
          ##Impresión de Logo de ADIMPEX en la parte superior derecha##
          switch($cAlfa){
            case "TEADIMPEXX": // ADIMPEX
            case "DEADIMPEXX": // ADIMPEX
            case "ADIMPEXX": // ADIMPEX
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoadimpex5.jpg',255,00,25,20);
            break;
            default:
              // No hace nada
            break;
          }
          ##Fin Impresión de Logo de ADIMPEX en la parte superior derecha##

          $pdf->setXY(10,10);
          $pdf->Cell(268,10,"REPORTE DE ESTADO DE CARTERA AL ". $cFecha,0,0,'C');
          $pdf->Rect(5,10,268,15);
          $nPosy = 30;
        }

        switch ($cAlfa) {
          case 'ADUANAMO':
          case 'DEADUANAMO':
          case 'TEADUANAMO':
            $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_aduanamo.jpg',10,11,32,13);
          break;
					case "LOGINCAR":
					case "DELOGINCAR":
					case "TELOGINCAR":
            $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/Logo_Login_Cargo_Ltda_2.jpg',10,11,39,13);
          break;
          case "TRLXXXXX":
          case "DETRLXXXXX":
          case "TETRLXXXXX":
            $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logobma.jpg',10,11,17,13);
          break;
					case "TEADIMPEXX": // ADIMPEX
					case "DEADIMPEXX": // ADIMPEX
					case "ADIMPEXX": // ADIMPEX
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoadimpex4.jpg',10,13,36,8);
					break;
					case "ROLDANLO"://ROLDAN
          case "TEROLDANLO"://ROLDAN
          case "DEROLDANLO"://ROLDAN
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoroldan.png',10,11,40,13);
					break;
          case "CASTANOX":
          case "DECASTANOX":
          case "TECASTANOX":
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomartcam.jpg',10,11,29,13);
					break;
          case "ALMACAFE": //ALMACAFE
          case "TEALMACAFE": //ALMACAFE
          case "DEALMACAFE": //ALMACAFE
            $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoalmacafe.jpg',10,12,26,11);
          break;
          case "GRUMALCO"://GRUMALCO
          case "TEGRUMALCO"://GRUMALCO
          case "DEGRUMALCO"://GRUMALCO
            $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomalco.jpg',10,11,26,13);
					break;
					case "ALADUANA"://ALADUANA
					case "TEALADUANA"://ALADUANA
					case "DEALADUANA"://ALADUANA
            $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaladuana.jpg',16,11,29,13);
          break;
          case "ANDINOSX"://ANDINOSX
          case "TEANDINOSX"://ANDINOSX
          case "DEANDINOSX"://ANDINOSX
            $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoAndinos2.jpeg', 6, 11, 17, 13);
          break;
          case "GRUPOALC"://GRUPOALC
          case "TEGRUPOALC"://GRUPOALC
          case "DEGRUPOALC"://GRUPOALC
            $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoalc.jpg',6,11,29,13);
          break;
          case "AAINTERX"://AAINTERX
					case "TEAAINTERX"://AAINTERX
					case "DEAAINTERX"://AAINTERX
            $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logointernacional.jpg',16,11,29,13);
          break;
          case "AALOPEZX":
          case "TEAALOPEZX":
          case "DEAALOPEZX":
            $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoaalopez.png', 6, 11, 27);
          break;
          case "ADUAMARX"://ADUAMARX
          case "TEADUAMARX"://ADUAMARX
          case "DEADUAMARX"://ADUAMARX
            $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaduamar.jpg',10,11,13);
          break;
          case "SOLUCION"://SOLUCION
          case "TESOLUCION"://SOLUCION
          case "DESOLUCION"://SOLUCION
            $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logosoluciones.jpg',10,11,30);
					break;
					case "FENIXSAS"://FENIXSAS
					case "TEFENIXSAS"://FENIXSAS
					case "DEFENIXSAS"://FENIXSAS
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logofenix.jpg',10,11,34);
					break;
					case "COLVANXX"://COLVANXX
					case "TECOLVANXX"://COLVANXX
					case "DECOLVANXX"://COLVANXX
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logocolvan.jpg',10,9,34);
					break;
					case "INTERLAC"://INTERLAC
					case "TEINTERLAC"://INTERLAC
					case "DEINTERLAC"://INTERLAC
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logointerlace.jpg',10,8,34);
					break;
					case "DHLEXPRE": //DHLEXPRE
					case "TEDHLEXPRE": //DHLEXPRE
					case "DEDHLEXPRE": //DHLEXPRE
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_dhl_express.jpg',10,11,26,13);
					break;
          case "KARGORUX": //KARGORUX
          case "TEKARGORUX": //KARGORUX
          case "DEKARGORUX": //KARGORUX
            $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logokargoru.jpg',10,11,26,13);
          break;
          case "ALOGISAS": //LOGISTICA
          case "TEALOGISAS": //LOGISTICA
          case "DEALOGISAS": //LOGISTICA
            $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logologisticasas.jpg',8,11,32);
          break;
          case "PROSERCO": //PROSERCO
          case "TEPROSERCO": //PROSERCO
          case "DEPROSERCO": //PROSERCO
            $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoproserco.png',8,11,22);
          break;
          case "FEDEXEXP":
          case "DEFEDEXEXP":
          case "TEFEDEXEXP":
            $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logofedexexp.jpg',8,11,23);
          break;
          case "EXPORCOM":
          case "DEEXPORCOM":
          case "TEEXPORCOM":
            $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoexporcomex.jpg',8,12,23);
          break;
          case "HAYDEARX":
          case "DEHAYDEARX":
          case "TEHAYDEARX":
            $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logohaydear.jpeg',8,12,30,11);
          break;
          case "CONNECTA":
          case "DECONNECTA":
          case "TECONNECTA":
            $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoconnecta.jpg',8,11,25,13);
          break;
          case "CONLOGIC":
          case "DECONLOGIC":
          case "TECONLOGIC":
            $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoconlogic.jpg',8,11,18,13);
          break;
          case "OPENEBCO":
          case "DEOPENEBCO":
          case "TEOPENEBCO":
            $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/opentecnologia.JPG',6,11,35,13);
          break;
        }

        $nPosx = 5;
        $pdf->SetFont('Arial','B',10);
        $pdf->setXY($nPosx,$nPosy);
        $pdf->SetFillColor(150,173,235);
        $pdf->Cell(268,4,"CONSULTA POR CLIENTE",1,0,'L',1);
        $nPosy += 4;
        $pdf->setXY($nPosx,$nPosy);
        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(8,4,"NIT",1,0,'L',1);
        $pdf->SetFont('Arial','',7);
        $pdf->Cell(25,4,$cTerId."-".f_Digito_Verificacion($cTerId),1,0,'L',1);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(15,4,"CLIENTE",1,0,'L',1);
        $pdf->SetFont('Arial','',7);
        $pdf->Cell(105,4,$vNomCli['clinomxx'],1,0,'L',1);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(15,4,"TELEFONO",1,0,'C',1);
        $pdf->SetFont('Arial','',7);
        $pdf->Cell(30,4,$vNomCli['CLITELXX'],1,0,'L',1);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(40,4,"FECHA Y HORA DE CONSULTA",1,0,'L',1);
        $pdf->SetFont('Arial','',7);
        $pdf->Cell(30,4,date("Y-m-d H:i:s"),1,0,'L',1);
        $nPosy += 8;

        $pdf->SetFont('Arial','B',10);
        $pdf->setXY($nPosx,$nPosy);
        $pdf->SetFillColor(150,173,235);
        $pdf->Cell(268,4,"CARTERA VENCIDA",1,0,'L',1);
        $nPosy += 4;
        $pdf->setXY($nPosx,$nPosy);
        $pdf->SetFont('Arial','B',7);
        $pdf->SetFillColor(150,173,235);
        if ($cColPed == "SI")  {
          $pdf->Cell(49,4,"Comprobante",1,0,'C',1);
          $pdf->Cell(25,4,"Pedido",1,0,'C',1);
        } else {
          $pdf->Cell(74,4,"Comprobante",1,0,'C',1);
        }
        switch($cAlfa) {
          case "DEAAINTERX":
          case "TEAAINTERX":
          case "AAINTERX":
            $pdf->Cell(34,4,"DO",1,0,'C',1);
          break;
          default:
            $pdf->Cell(34,4,"Cuenta",1,0,'C',1);
          break;
        }
        if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") {
          $pdf->Cell(22,4,"Fecha",1,0,'C',1);
          $pdf->Cell(22,4,"Entrega Cliente",1,0,'C',1);
          $pdf->Cell(22,4,"Vencimiento",1,0,'C',1);
        } else {
          $pdf->Cell(33,4,"Fecha",1,0,'C',1);
          $pdf->Cell(33,4,"Vencimiento",1,0,'C',1);
        }
        $pdf->Cell(20,4,"Dias Cartera",1,0,'C',1);
        $pdf->Cell(20,4,"Dias Vencidos",1,0,'C',1);
        $pdf->Cell(20,4,"Movimiento",1,0,'C',1);
        $pdf->Cell(34,4,"Saldo",1,0,'C',1);

        for($i=0;$i<count($mCarteraVencida);$i++) {
          $nPosy += 4;

          if($nPosy > 196){
            $pdf->AddPage();
            $nPosx = 5;
            $nPosy = 25;
          }

          $pdf->setXY($nPosx,$nPosy);
          if ($cColPed == "SI")  {
            $pdf->Cell(49,4,$mCarteraVencida[$i]['document'],1,0,'L');
            $pdf->Cell(25,4,$mCarteraVencida[$i]['pedidoxx'],1,0,'L');
          } else {
            $pdf->Cell(74,4,$mCarteraVencida[$i]['document'],1,0,'L');
          }
          $pdf->Cell(34,4,$mCarteraVencida[$i]['pucidxxx'],1,0,'C');
          if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") {
            $pdf->Cell(22,4,$mCarteraVencida[$i]['comfecxx'],1,0,'C');
            $pdf->Cell(22,4,$mCarteraVencida[$i]['comfecnx'],1,0,'C');
            $pdf->Cell(22,4,$mCarteraVencida[$i]['comfecvn'],1,0,'C');
          } else {
            $pdf->Cell(33,4,$mCarteraVencida[$i]['comfecxx'],1,0,'C');
            $pdf->Cell(33,4,$mCarteraVencida[$i]['comfecvn'],1,0,'C');
          }
          $pdf->Cell(20,4,$mCarteraVencida[$i]['diascart'],1,0,'C');
          $pdf->SetTextColor(255,0,0);
          $pdf->Cell(20,4,$mCarteraVencida[$i]['diasvenc'],1,0,'C');
          $pdf->SetTextColor(0,0,0);
          $pdf->Cell(20,4,$mCarteraVencida[$i]['commovxx'],1,0,'C');
          $pdf->Cell(34,4,number_format($mCarteraVencida[$i]['saldoxxx'],2,",","."),1,0,'R');

          $nTotCarVencida += ($mCarteraVencida[$i]['commovxx'] == "D") ? $mCarteraVencida[$i]['saldoxxx'] : ($mCarteraVencida[$i]['saldoxxx'] * -1);
        }

        $nPosy += 4;

        if($nPosy > 196){
          $pdf->AddPage();
          $nPosx = 5;
          $nPosy = 25;
        }
        $pdf->SetFont('Arial','B',7);
        $pdf->setXY($nPosx,$nPosy);
        $pdf->SetFillColor(150,173,235);
        $pdf->Cell(234,4,"TOTAL CARTERA VENCIDA:",1,0,'R',1);
        $pdf->Cell(34,4,number_format($nTotCarVencida,2,",","."),1,0,'R',1);

        $nPosy += 8;

        if($nPosy > 196){
          $pdf->AddPage();
          $nPosx = 5;
          $nPosy = 25;
        }

        $pdf->SetFont('Arial','B',10);
        $pdf->setXY($nPosx,$nPosy);
        $pdf->SetFillColor(150,173,235);
        $pdf->Cell(268,4,"CARTERA SIN VENCER",1,0,'L',1);
        $nPosy += 4;
        $pdf->setXY($nPosx,$nPosy);
        $pdf->SetFont('Arial','B',7);
        $pdf->SetFillColor(150,173,235);
        if ($cColPed == "SI")  {
          $pdf->Cell(49,4,"Comprobante",1,0,'C',1);
          $pdf->Cell(25,4,"Pedido",1,0,'C',1);
        } else {
          $pdf->Cell(74,4,"Comprobante",1,0,'C',1);
        }
        switch($cAlfa) {
          case "DEAAINTERX":
          case "TEAAINTERX":
          case "AAINTERX":
            $pdf->Cell(34,4,"DO",1,0,'C',1);
          break;
          default:
            $pdf->Cell(34,4,"Cuenta",1,0,'C',1);
          break;
        }
        if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") {
          $pdf->Cell(22,4,"Fecha",1,0,'C',1);
          $pdf->Cell(22,4,"Entrega Cliente",1,0,'C',1);
          $pdf->Cell(22,4,"Vencimiento",1,0,'C',1);
        } else {
          $pdf->Cell(33,4,"Fecha",1,0,'C',1);
          $pdf->Cell(33,4,"Vencimiento",1,0,'C',1);
        }
        $pdf->Cell(20,4,"Dias Cartera",1,0,'C',1);
        $pdf->Cell(20,4,"Dias Vencidos",1,0,'C',1);
        $pdf->Cell(20,4,"Movimiento",1,0,'C',1);
        $pdf->Cell(34,4,"Saldo",1,0,'C',1);

        for($i=0;$i<count($mCarteraSinVencer);$i++){
          $nPosy += 4;

          if($nPosy > 196){
            $pdf->AddPage();
            $nPosx = 5;
            $nPosy = 25;
          }

          $pdf->setXY($nPosx,$nPosy);
          if ($cColPed == "SI")  {
            $pdf->Cell(49,4,$mCarteraSinVencer[$i]['document'],1,0,'L');
            $pdf->Cell(25,4,$mCarteraSinVencer[$i]['pedidoxx'],1,0,'L');
          } else {
            $pdf->Cell(74,4,$mCarteraSinVencer[$i]['document'],1,0,'L');
          }
          $pdf->Cell(34,4,$mCarteraSinVencer[$i]['pucidxxx'],1,0,'C');
          if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") {
            $pdf->Cell(22,4,$mCarteraSinVencer[$i]['comfecxx'],1,0,'C');
            $pdf->Cell(22,4,$mCarteraSinVencer[$i]['comfecnx'],1,0,'C');
            $pdf->Cell(22,4,$mCarteraSinVencer[$i]['comfecvn'],1,0,'C');
          } else {
            $pdf->Cell(33,4,$mCarteraSinVencer[$i]['comfecxx'],1,0,'C');
            $pdf->Cell(33,4,$mCarteraSinVencer[$i]['comfecvn'],1,0,'C');
          }
          $pdf->Cell(20,4,$mCarteraSinVencer[$i]['diascart'],1,0,'C');
          $pdf->Cell(20,4,$mCarteraSinVencer[$i]['diasvenc'],1,0,'C');
          $pdf->Cell(20,4,$mCarteraSinVencer[$i]['commovxx'],1,0,'C');
          $pdf->Cell(34,4,number_format($mCarteraSinVencer[$i]['saldoxxx'],2,",","."),1,0,'R');

          $nTotCartera += ($mCarteraSinVencer[$i]['commovxx'] == "D") ? $mCarteraSinVencer[$i]['saldoxxx'] : ($mCarteraSinVencer[$i]['saldoxxx'] * -1);
        }

        $nPosy += 4;

        if($nPosy > 196){
          $pdf->AddPage();
          $nPosx = 5;
          $nPosy = 25;
        }
        $pdf->SetFont('Arial','B',7);
        $pdf->setXY($nPosx,$nPosy);
        $pdf->SetFillColor(150,173,235);
        $pdf->Cell(234,4,"TOTAL CARTERA SIN VENCER:",1,0,'R',1);
        $pdf->Cell(34,4,number_format($nTotCartera,2,",","."),1,0,'R',1);

        $nPosy += 8;

        if($nPosy > 196){
          $pdf->AddPage();
          $nPosx = 5;
          $nPosy = 25;
        }

        $pdf->SetFont('Arial','B',10);
        $pdf->setXY($nPosx,$nPosy);
        $pdf->SetFillColor(150,173,235);
        $pdf->Cell(268,4,"SALDOS A FAVOR",1,0,'L',1);
        $nPosy += 4;
        $pdf->setXY($nPosx,$nPosy);
        $pdf->SetFont('Arial','B',7);
        $pdf->SetFillColor(150,173,235);
        if ($cColPed == "SI")  {
          $pdf->Cell(49,4,"Comprobante",1,0,'C',1);
          $pdf->Cell(25,4,"Pedido",1,0,'C',1);
        } else {
          $pdf->Cell(74,4,"Comprobante",1,0,'C',1);
        }
        switch($cAlfa) {
          case "DEAAINTERX":
          case "TEAAINTERX":
          case "AAINTERX":
            $pdf->Cell(34,4,"DO",1,0,'C',1);
          break;
          default:
            $pdf->Cell(34,4,"Cuenta",1,0,'C',1);
          break;
        }
        if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") {
          $pdf->Cell(22,4,"Fecha",1,0,'C',1);
          $pdf->Cell(22,4,"Entrega Cliente",1,0,'C',1);
          $pdf->Cell(22,4,"Vencimiento",1,0,'C',1);
        } else {
          $pdf->Cell(33,4,"Fecha",1,0,'C',1);
          $pdf->Cell(33,4,"Vencimiento",1,0,'C',1);
        }
        $pdf->Cell(20,4,"Dias Cartera",1,0,'C',1);
        $pdf->Cell(20,4,"Dias Vencidos",1,0,'C',1);
        $pdf->Cell(20,4,"Movimiento",1,0,'C',1);
        $pdf->Cell(34,4,"Saldo",1,0,'C',1);

        for($i=0;$i<count($mSaldosaFavor);$i++){
          $nPosy += 4;

          if($nPosy > 196){
            $pdf->AddPage();
            $nPosx = 5;
            $nPosy = 25;
          }

          $pdf->setXY($nPosx,$nPosy);
          if ($cColPed == "SI")  {
            $pdf->Cell(49,4,$mSaldosaFavor[$i]['document'],1,0,'L');
            $pdf->Cell(25,4,$mSaldosaFavor[$i]['pedidoxx'],1,0,'L');
          } else {
            $pdf->Cell(74,4,$mSaldosaFavor[$i]['document'],1,0,'L');
          }
          $pdf->Cell(34,4,$mSaldosaFavor[$i]['pucidxxx'],1,0,'C');
          if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") {
            $pdf->Cell(22,4,$mSaldosaFavor[$i]['comfecxx'],1,0,'C');
            $pdf->Cell(22,4,$mSaldosaFavor[$i]['comfecnx'],1,0,'C');
            $pdf->Cell(22,4,$mSaldosaFavor[$i]['comfecvn'],1,0,'C');
          } else {
            $pdf->Cell(33,4,$mSaldosaFavor[$i]['comfecxx'],1,0,'C');
            $pdf->Cell(33,4,$mSaldosaFavor[$i]['comfecvn'],1,0,'C');
          }
          $pdf->Cell(20,4,$mSaldosaFavor[$i]['diascart'],1,0,'C');
          $pdf->Cell(20,4,$mSaldosaFavor[$i]['diasvenc'],1,0,'C');
          $pdf->Cell(20,4,$mSaldosaFavor[$i]['commovxx'],1,0,'C');
          $pdf->Cell(34,4,number_format($mSaldosaFavor[$i]['saldoxxx'],2,",","."),1,0,'R');

          $nTotSaldos += ($mSaldosaFavor[$i]['commovxx'] == "D") ? $mSaldosaFavor[$i]['saldoxxx'] : ($mSaldosaFavor[$i]['saldoxxx'] * -1);
        }

        $nPosy += 4;

        if($nPosy > 196){
          $pdf->AddPage();
          $nPosx = 5;
          $nPosy = 25;
        }
        $pdf->SetFont('Arial','B',7);
        $pdf->setXY($nPosx,$nPosy);
        $pdf->SetFillColor(150,173,235);
        $pdf->Cell(234,4,"TOTAL SALDOS A FAVOR:",1,0,'R',1);
        $pdf->Cell(34,4,number_format($nTotSaldos,2,",","."),1,0,'R',1);

        $nPosy += 8;

        if($nPosy > 196){
          $pdf->AddPage();
          $nPosx = 5;
          $nPosy = 25;
        }

        $pdf->SetFont('Arial','B',10);
        $pdf->setXY($nPosx,$nPosy);
        $pdf->SetFillColor(150,173,235);
        $pdf->Cell(268,4,"RECIBOS PROVISIONALES",1,0,'L',1);
        $nPosy += 4;
        $pdf->setXY($nPosx,$nPosy);
        $pdf->SetFont('Arial','B',7);
        $pdf->SetFillColor(150,173,235);
        $pdf->Cell(74,4,"Comprobante",1,0,'C',1);
        switch($cAlfa) {
          case "DEAAINTERX":
          case "TEAAINTERX":
          case "AAINTERX":
            $pdf->Cell(34,4,"DO",1,0,'C',1);
          break;
          default:
            $pdf->Cell(34,4,"Cuenta",1,0,'C',1);
          break;
        }
        $pdf->Cell(33,4,"Fecha",1,0,'C',1);
        $pdf->Cell(33,4,"Vencimiento",1,0,'C',1);
        $pdf->Cell(20,4,"Dias Cartera",1,0,'C',1);
        $pdf->Cell(20,4,"Dias Vencidos",1,0,'C',1);
        $pdf->Cell(20,4,"Movimiento",1,0,'C',1);
        $pdf->Cell(34,4,"Saldo",1,0,'C',1);

        for($i=0;$i<count($mRecProv);$i++){
          $nPosy += 4;

          if($nPosy > 196){
            $pdf->AddPage();
            $nPosx = 5;
            $nPosy = 25;
          }

          $pdf->setXY($nPosx,$nPosy);
          $pdf->Cell(74,4,$mRecProv[$i]['document'],1,0,'L');
          $pdf->Cell(34,4,$mRecProv[$i]['pucidxxx'],1,0,'C');
          $pdf->Cell(33,4,$mRecProv[$i]['comfecxx'],1,0,'C');
          $pdf->Cell(33,4,$mRecProv[$i]['comfecvn'],1,0,'C');
          $pdf->Cell(20,4,$mRecProv[$i]['diascart'],1,0,'C');
          $pdf->Cell(20,4,$mRecProv[$i]['diasvenc'],1,0,'C');
          $pdf->Cell(20,4,$mRecProv[$i]['commovxx'],1,0,'C');
          $pdf->Cell(34,4,number_format($mRecProv[$i]['saldoxxx'],2,",","."),1,0,'R');

          $nTotProvicionales += ($mRecProv[$i]['commovxx'] == "D") ? $mRecProv[$i]['saldoxxx'] : ($mRecProv[$i]['saldoxxx'] * -1);
        }

        $nPosy += 4;

        if($nPosy > 196){
          $pdf->AddPage();
          $nPosx = 5;
          $nPosy = 25;
        }
        $pdf->SetFont('Arial','B',7);
        $pdf->setXY($nPosx,$nPosy);
        $pdf->SetFillColor(150,173,235);
        $pdf->Cell(234,4,"TOTAL SALDOS PROVISIONALES: ",1,0,'R',1);
        $pdf->Cell(34,4,number_format($nTotProvicionales,2,",","."),1,0,'R',1);
        $nPosy += 4;
        $pdf->SetFont('Arial','B',9);
        $pdf->setXY($nPosx,$nPosy);
        $pdf->SetFillColor(166,222,238);
        $pdf->Cell(234,4,"TOTAL SALDOS A FAVOR: ",1,0,'R',1);
        $pdf->Cell(34,4,number_format($nTotSaldos,2,",","."),1,0,'R',1);
        $nPosy += 4;
        $pdf->setXY($nPosx,$nPosy);
        $pdf->Cell(234,4,"TOTAL CARTERA: ",1,0,'R',1);
        $pdf->Cell(34,4,number_format(($nTotCarVencida+$nTotCartera),2,",","."),1,0,'R',1);
        $nPosy += 4;

        $mNomTotales = array();
        (abs($nTotCarVencida+$nTotCartera)>0) ? $mNomTotales[count($mNomTotales)]="TOTAL CARTERA" :"";
        (abs($nTotSaldos)>0) ? $mNomTotales[count($mNomTotales)]="SALDOS A FAVOR" :"";
        (abs($nTotProvicionales)>0) ? $mNomTotales[count($mNomTotales)]="RECIBOS PROVISIONALES" :"";

        $mTitulo="";

        for($j=0;$j <= (count($mNomTotales)-1);$j++){
        $mTitulo .= $mNomTotales[$j];
        ($j==(count($mNomTotales)-1)) ? "" : $mTitulo .=" - ";
        }

        $pdf->setXY($nPosx,$nPosy);
        $pdf->Cell(234,4,$mTitulo,1,0,'R',1);
        $pdf->Cell(34,4,number_format((($nTotCarVencida+$nTotCartera) - ($nTotProvicionales) + ($nTotSaldos)),2,",","."),1,0,'R',1);

        $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";

        $pdf->Output($cFile);

        if (file_exists($cFile)){
          chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
        } else {
          f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
        }

        echo "<html><script>document.location='$cFile';</script></html>";
      break;
    }
  }

  if ($nSwitch == 0) {
  } else {
    f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.\n");

     switch ($cTipo) {
      case 2:
        /** Excel
         * No hace nada porque se ejecuta en el fmpro
        **/
      break;
      default: ?>
        <script languaje = "javascript">
          window.close();
        </script>
      <?php break;
     }
  }

  function fnCadenaAleatoria($pLength = 8) {
    $cCaracteres = "abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ";
    $nCaracteres = strlen($cCaracteres);
    $cResult = "";
    for ($x=0;$x< $pLength;$x++) {
      $nIndex = mt_rand(0,$nCaracteres - 1);
      $cResult .= $cCaracteres[$nIndex];
    }
    return $cResult;
  }
  ?>

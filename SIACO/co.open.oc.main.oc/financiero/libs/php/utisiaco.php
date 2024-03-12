  <?php
  /**
   * utisiaco.php : Utility de Clases para el Formato Factura de SIACO.
   *
   * Este script contiene la colecciones de clases para generar el formato de factura de venta
   * @author Juan Jose Trujillo <juan.trujillo@openits.co>
   * @package openComex
   */

  // ini_set('error_reporting', E_ALL);
  // ini_set("display_errors","1");

  class cFormatoFacturaVentaSiaco {
    /**
     * Metodo que genera el pdf del formato factura de venta del cliente SIACO.
     */
    function fnGenerarFormatoFacturaVentaSiaco($pArrayParametros) {

      global $xConexion01; global $cAlfa;      global $vSysStr; global $kUser;     global $cPlesk_Skin_Directory; 
      global $nPag;        global $vAgeDat; 	 global $vCocDat; global $vResDat;   global $cDocId;
      global $vCiuDat;     global $vDceDat;  	 global $vCccDat; global $vConDat; 	 global $vCcoDat;
      global $cDeposito;   global $mIngTer; 	 global $cPaiOri; global $cFormaPag; global $vMedPag;
      global $cNomDirCue;  global $cSucId; 	   global $cPedido; global $cPedOrd;   global $cPath;
      global $cLugIngDes;  global $cPaiOriDes;
      global $cUsrId;      global $cRoot;      global $kModo;   global $cRootQR;
      global $cProceso;

      /**
       *
       * $pArrayParametros['PRINTSXX'] Informacion del comprobante
       * $pArrayParametros['USERIDXX'] ID del Usuario
       * $pArrayParametros['CORREOXX'] Variable para identificar si se envia el correo
       * $pArrayParametros['ORIGENXX'] Identifica el origen si es por la aplicacion o por la URL del navegador
       * $pArrayParametros['KMODOXXX'] Modo
       * $pArrayParametros['USRIDXXX'] Usuario
       * $pArrayParametros['RUTAARCX'] Ruta archivos
       * $pArrayParametros['PROCESOX'] Desde donde es guardado el comprobante: desde la tarea automatica (TAREA_AUTOMATICA) o desde el formulario (Vacio)
       */

      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var number
       */
      $nSwitch = 0;

      /**
       * Variable para hacer el retorno.
       * 
       * @var array
       */
      $mReturn    = array();
      $mReturn[0] = ""; // Exito o error
      $mReturn[1] = ""; // Ruta Archivo

      // Variables para rutas y nombres de archivos
      $cUsrId   = $pArrayParametros['USRIDXXX'];
      $kModo    = $pArrayParametros['KMODOXXX'];
      $cRoot    = str_replace(array("/produccion","/desarrollo","/pruebas"),array("","",""), $pArrayParametros['RUTAARCX']);
      $cRootQR  = $pArrayParametros['RUTAARCX'];
      $cProceso = $pArrayParametros['PROCESOX'];

      // Validacion de Comprobante Repetido
      $mPrints = f_Explode_Array($pArrayParametros['PRINTSXX'],"|","~");
      $vMemo   = explode("|",$pArrayParametros['PRINTSXX']);

      ##Buscar el usuario que ingreso##
      $qUserNom  = "SELECT USRNOMXX  ";
      $qUserNom .= "FROM $cAlfa.SIAI0003 ";
      $qUserNom .= "WHERE ";
      $qUserNom .= "USRIDXXX LIKE \"{$pArrayParametros['USERIDXX']}\" AND ";
      $qUserNom .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
      $xUserNom  = f_MySql("SELECT","",$qUserNom,$xConexion01,"");
      $vUserNom = mysql_fetch_array($xUserNom);
      //f_Mensaje(__FILE__,__LINE__,$qUserNom." ~ ".mysql_num_rows($xUserNom));
      ##Fin buscar usuario que ingreso##

      for ($i=0; $i<count($mPrints); $i++) {
        if ($mPrints[$i][0]!=""){
          $cAno = substr($mPrints[$i][4],0,4);

          // Busco la resolucion en la tabla GRM00138.
          $qResFac  = "SELECT rescomxx,resclaxx,residxxx,resfdexx,resdesxx,reshasxx, resvigme ";
          $qResFac .= "FROM $cAlfa.fpar0138 ";
          $qResFac .= "WHERE ";
          $qResFac .= "rescomxx LIKE \"%{$mPrints[$i][0]}~{$mPrints[$i][1]}%\" AND ";
          $qResFac .= "regestxx = \"ACTIVO\" LIMIT 0,1";
          $xResFac  = f_MySql("SELECT","",$qResFac,$xConexion01,"");
          $mResFac = mysql_fetch_array($xResFac);
          //f_Mensaje(__FILE__,__LINE__,$qResFac." ~ ".mysql_num_rows($xResFac));
          // Fin de Busco la resolucion en la tabla GRM00138.

          // Armo la variable con los codigos de los comprobantes de facturacion para la actualizacion del consecutivo despues de grabar la factura.
          $mCodCom = f_Explode_Array($mResFac['rescomxx'],"|","~");
          $cCodigos_Comprobantes = "";
          for ($j=0;$j<count($mCodCom);$j++) {
            $cCodigos_Comprobantes .= "\"";
            $cCodigos_Comprobantes .= "{$mCodCom[$j][1]}";
            $cCodigos_Comprobantes .= "\"";
            if ($j < (count($mCodCom) -1)) { $cCodigos_Comprobantes .= ","; }
          }
          // Fin de Armo la variable con los codigos de los comprobantes de facturacion para la actualizacion del consecutivo despues de grabar la factura.

          $qValCsc  = "SELECT comidxxx,comcodxx,comcscxx,comcsc2x ";
          $qValCsc .= "FROM $cAlfa.fcoc$cAno ";
          $qValCsc .= "WHERE ";
          $qValCsc .= "comidxxx = \"{$mPrints[$i][0]}\"  AND ";
          $qValCsc .= "comcodxx IN ($cCodigos_Comprobantes) AND ";
          $qValCsc .= "comcscxx = \"{$mPrints[$i][2]}\"";
          $xValCsc  = f_MySql("SELECT","",$qValCsc,$xConexion01,"");
          //f_Mensaje(__FILE__,__LINE__,$qValCsc." ~ ".mysql_num_rows($xValCsc));
          if (mysql_num_rows($xValCsc) > 1) {
            $nSwitch = 1;
            $mReturn[count($mReturn)] = "El Documento [{$mPrints[$i][0]}-{$mPrints[$i][1]}-{$mPrints[$i][2]}] No se puede Imprimir porque su Consecutivo se Encuentra Repetido en el Modulo de Contabilidad.";
          }
        }
      }
      // Fin de Validacion de Comprobante Repetido

      $resolucion = 0;
      $zCadRes    = "|";
      $fomularios = 0;
      $zCadFor    = "";
      if($fomularios == 1){
        $nSwitch = 1;
        $mReturn[count($mReturn)] = "Las Siguientes Facturas Presentan Inconsistencias con Formularios: \n $zCadFor.";
      }

      if($resolucion == 1){
        $nSwitch = 1;
        $mReturn[count($mReturn)] = "Las Siguientes Facturas no Tienen Resolucion Activa [$zCadRes].";
      }

      if($nSwitch == 0){
        $vRetunCorreos = array();
        $mPrn = explode("|",$pArrayParametros['PRINTSXX']);        

        for ($nn=0;$nn<count($mPrn);$nn++) {
          if (strlen($mPrn[$nn]) > 0) {
            // Array con archivos para enviar al correo
            $vFile = array();
            // Ruta del PDF generado
            $cFile = "";

            // Documento
            $vComp = explode("~",$mPrn[$nn]);
            $cAno = substr($vComp[4],0,4);
            $cComId   = $vComp[0];
            $cComCod  = $vComp[1];
            $cComCsc  = $vComp[3];
            $cComCsc2 = $vComp[3];
            $cRegFCre = $vComp[4];
            $cNewYear = substr($cRegFCre,0,4);

            ///// CABECERA 1001 /////
            $qCocDat  = "SELECT ";
            $qCocDat .= "$cAlfa.fcoc$cNewYear.*, ";
            $qCocDat .= "IF($cAlfa.fpar0008.sucidxxx != \"\",$cAlfa.fpar0008.sucidxxx,\"SUCURSAL SIN ID\") AS sucidxxx, ";
            $qCocDat .= "IF($cAlfa.fpar0008.sucdesxx != \"\",$cAlfa.fpar0008.sucdesxx,\"SUCURSAL SIN DESCRIPCION\") AS sucdesxx, ";
            $qCocDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX, ";
            $qCocDat .= "IF($cAlfa.SIAI0150.CLIDIRXX != \"\",$cAlfa.SIAI0150.CLIDIRXX,\"SIN DIRECCION\") AS CLIDIRXX, ";
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
            $qCocDat .= "WHERE $cAlfa.fcoc$cNewYear.comidxxx = \"$cComId\" AND ";
            $qCocDat .= "$cAlfa.fcoc$cNewYear.comcodxx = \"$cComCod\" AND ";
            $qCocDat .= "$cAlfa.fcoc$cNewYear.comcscxx = \"$cComCsc\" AND ";
            $qCocDat .= "$cAlfa.fcoc$cNewYear.comcsc2x = \"$cComCsc2\" LIMIT 0,1";
            //f_Mensaje(__FILE__,__LINE__,$qCocDat);
            $xCocDat  = f_MySql("SELECT","",$qCocDat,$xConexion01,"");
            if (mysql_num_rows($xCocDat) > 0) {
              $vCocDat  = mysql_fetch_array($xCocDat);
              //Traydendo el pedido de la orden
              $vComObs2 = explode("~",$vCocDat['comobs2x']);
              $cPedOrd = $vComObs2[8];
              $cForImp = $vComObs2[9];
            }

            ##Traigo Dias de Plazo ##
            $qCccDat  = "SELECT * ";
            $qCccDat .= "FROM $cAlfa.fpar0151 ";
            $qCccDat .= "WHERE ";
            $qCccDat .= "$cAlfa.fpar0151.cliidxxx = \"{$vCocDat['terid2xx']}\" AND ";
            $qCccDat .= "$cAlfa.fpar0151.regestxx = \"ACTIVO\" ";
            $xCccDat  = f_MySql("SELECT","",$qCccDat,$xConexion01,"");
            if (mysql_num_rows($xCccDat) > 0) {
              $vCccDat = mysql_fetch_array($xCccDat);
            }
            ##Fin Traigo Dias de Plazo ##

            //Para siaco se reescribe el campo $vCccDat['cccimpus'] por lo que se guarda en la posicion [16]
            $vCccDat['cccimpus'] = ($vComObs2[16] == "USD") ? "SI" : "NO";

            ##Traigo la Forma de Pago##
            $cFormaPag = "";
            if ($vComObs2[14] != "") {
              //Buscando descripcion
              $cFormaPag = ($vComObs2[14] == 1) ? "CONTADO" : "CREDITO";
            }
            ##FIN Traigo la Forma de Pago##

            $cMedioPago = "";
            ##Traigo el Medio de Pago##
            if ($vComObs2[15] != "") {
              $qMedPag  = "SELECT mpadesxx ";
              $qMedPag .= "FROM $cAlfa.fpar0155 ";
              $qMedPag .= "WHERE mpaidxxx = \"{$vComObs2[15]}\" AND ";
              $qMedPag .= "regestxx = \"ACTIVO\" LIMIT 0,1";
              $xMedPag  = f_MySql("SELECT","",$qMedPag,$xConexion01,"");
              if (mysql_num_rows($xMedPag) > 0) {
                $vMedPag = mysql_fetch_array($xMedPag);
              }
            }
            ##FIN Traigo el Medio de Pago##

            $cComVlr = ($vCccDat['cccimpus'] == "SI") ? "comvlrme" : "comvlrxx";

            ////// DETALLE 1002 /////
            $qCodDat  = "SELECT DISTINCT ";
            $qCodDat .= "$cAlfa.fcod$cNewYear.* ";
            $qCodDat .= "FROM $cAlfa.fcod$cNewYear ";
            $qCodDat .= "WHERE ";
            $qCodDat .= "$cAlfa.fcod$cNewYear.comidxxx = \"$cComId\" AND ";
            $qCodDat .= "$cAlfa.fcod$cNewYear.comcodxx = \"$cComCod\" AND ";
            $qCodDat .= "$cAlfa.fcod$cNewYear.comcscxx = \"$cComCsc\" AND ";
            $qCodDat .= "$cAlfa.fcod$cNewYear.comcsc2x = \"$cComCsc2\" ";
            $qCodDat .= "ORDER BY ABS($cAlfa.fcod$cNewYear.comseqxx) ASC ";
            $xCodDat  = f_MySql("SELECT","",$qCodDat,$xConexion01,"");
            $mValores = Array(); $mCodDat = array();
            //f_Mensaje(__FILE__,__LINE__,$qCodDat);

            while ($xRCD = mysql_fetch_array($xCodDat)) {
              $xRCD['tcatasax'] = $vCocDat['tcatasax'];

              if ($xRCD['comctocx'] == "PCC") {
                $mValores['nDesVlr'][] = $xRCD['comobsxx'];
                $mValores['nTotVlr'][] = $xRCD[$cComVlr];
                $mValores['cTipEje'][] = $xRCD['puctipej'];
                $mValores['nTasaCa'][] = $xRCD['tcatasax'];
              } else {
                if ($xRCD['comctocx'] == "IP") {
                  //Trayendo descripcion concepto, cantidad y unidad
                  $vDatosIp = array();
                  $vDatosIp = f_Cantidad_Ingreso_Propio($xRCD['comobsxx'], '', $xRCD['sucidxxx'], $xRCD['docidxxx'], $xRCD['docsufxx']);

                  $nSwitch_Encontre_Concepto = 0;
                  //Los IP se agrupan por Sevicio
                  for($j=0;$j<count($mCodDat);$j++){
                    if($mCodDat[$j]['ctoidxxx'] == $xRCD['ctoidxxx']){ //&& $mCodDat[$j]['seridxxx'] == $xRCD['ctoidxxx']
                      $nSwitch_Encontre_Concepto = 1;

                      $mCodDat[$j]['comvlrxx'] += $xRCD[$cComVlr];
                      $mCodDat[$j]['comvlrme'] += $xRCD['comvlrme'];

                      $mCodDat[$j]['compivax']  = $xRCD['compivax']; // Porcentaje IVA
                      $mCodDat[$j]['comvlr01'] += $xRCD['comvlr01']; // Valor Iva

                      //Cantidad de veces que se encuentra el servicio
                      $mCodDat[$j]['agrupaip'] += 1;
                      //Cantidad FE
                      $mCodDat[$j]['canfexxx'] += $vDatosIp[1];
                      //Cantidad por condicion especial
                      for ($nP=0; $nP<count($vDatosIp[3]); $nP++) {
                        //El campo aplsumxx del array inidica como deben sumarse la cantidades
                        //si el valor es SI, se suma normal
                        //si el valor es NO, no se suma, se mantiene el valor del IP inicial
                        //si el valor es -, deben dividirse la cadena por este caracter y sumar indiviualmente
                        switch ($vDatosIp[3][$nP]['aplsumxx']) {
                          case "NO":
                            //No hace nada se queda con el primer valor
                          break;
                          case "-":
                            $vAuxCanAcu = explode("-",$mCodDat[$j]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")]);
                            $vAuxCanIp  = explode("-",$vDatosIp[3][$nP]['valpdfxx']);
                            for ($nAC=0; $nAC<count($vAuxCanAcu); $nAC++) {
                              $vAuxCanAcu[$nAC] += $vAuxCanIp[$nAC];
                            }
                            $cValCanAcu = "";
                            for ($nAC=0; $nAC<count($vAuxCanAcu); $nAC++) {
                              $cValCanAcu .= $vAuxCanAcu[$nAC]."-";
                            }
                            $cValCanAcu = substr($cValCanAcu, 0, -1);
                            $mCodDat[$j]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] = $cValCanAcu;
                          break;
                          default: //por default es SI
                            $mCodDat[$j]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] += $vDatosIp[3][$nP]['valpdfxx'];
                          break;
                        }
                      }
                    }
                  }

                  if ($nSwitch_Encontre_Concepto == 0) {

                    $xRCD['comvlrxx'] = $xRCD[$cComVlr];
                    $nInd_mConData = count($mCodDat);
                    $mCodDat[$nInd_mConData] = $xRCD;
                    $mCodDat[$nInd_mConData]['comobsxx'] = $vDatosIp[0];
                    $mCodDat[$nInd_mConData]['canfexxx'] = $vDatosIp[1];
                    $mCodDat[$nInd_mConData]['unidadfe'] = $vDatosIp[2];
                    $mCodDat[$nInd_mConData]['formacob'] = $vDatosIp[6];
                    $mCodDat[$nInd_mConData]['monedaxx'] = $vDatosIp[7];

                    for ($nP=0; $nP<count($vDatosIp[3]); $nP++) {
                      $mCodDat[$nInd_mConData]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] = $vDatosIp[3][$nP]['valpdfxx'];
                      $mCodDat[$nInd_mConData]['simbolox'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] = $vDatosIp[3][$nP]['simbolox'];
                    }
                  }
                } else {
                  $nInd_mConData = count($mCodDat);
                  $mCodDat[$nInd_mConData] = $xRCD;
                }
              }
            }
            // Fin de Cargo la Matriz con los ROWS del Cursor

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

            ##Traigo Ciudad por la que se Facturo ##
            $qCcoDat  = "SELECT * ";
            $qCcoDat .= "FROM $cAlfa.fpar0008 ";
            $qCcoDat .= "WHERE ";
            $qCcoDat .= "$cAlfa.fpar0008.ccoidxxx = \"{$vCocDat['ccoidxxx']}\" AND ";
            $qCcoDat .= "$cAlfa.fpar0008.regestxx = \"ACTIVO\" LIMIT 0,1 ";
            $xCcoDat  = f_MySql("SELECT","",$qCcoDat,$xConexion01,"");
            if (mysql_num_rows($xCcoDat) > 0) {
              $vCcoDat = mysql_fetch_array($xCcoDat);
            }
            ##Fin Traigo Ciudad por la que se Facturo ##

            ##Traigo Ciudad del Cliente ##
            $qCiuDat  = "SELECT * ";
            $qCiuDat .= "FROM $cAlfa.SIAI0055 ";
            $qCiuDat .= "WHERE ";
            $qCiuDat .= "$cAlfa.SIAI0055.PAIIDXXX = \"{$vCocDat['PAIIDXXX']}\" AND ";
            $qCiuDat .= "$cAlfa.SIAI0055.DEPIDXXX = \"{$vCocDat['DEPIDXXX']}\" AND ";
            $qCiuDat .= "$cAlfa.SIAI0055.CIUIDXXX = \"{$vCocDat['CIUIDXXX']}\" AND ";
            $qCiuDat .= "$cAlfa.SIAI0055.REGESTXX = \"ACTIVO\" ";
            $xCiuDat  = f_MySql("SELECT","",$qCiuDat,$xConexion01,"");
            //f_Mensaje(__FILE__,__LINE__,$qCiuDat);
            if (mysql_num_rows($xCiuDat) > 0) {
              $vCiuDat = mysql_fetch_array($xCiuDat);
            }
            ##Fin Traigo Ciudad del Cliente ##

            ##Traigo Datos de Contacto del Facturado a ##
            if($vCocDat['CLICONTX'] != ""){
              $vContactos = explode("~",$vCocDat['CLICONTX']);
              for ($nC=0;$nC<count($vContactos);$nC++) {
                if ($vContactos[$nC] != "") {
                  $vIdContacto = $vContactos[$nC];

                  $qConDat  = "SELECT ";
                  $qConDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX ";
                  $qConDat .= "FROM $cAlfa.SIAI0150 ";
                  $qConDat .= "WHERE ";
                  $qConDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"$vIdContacto\" AND ";
                  $qConDat .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" ";
                  $xConDat  = f_MySql("SELECT","",$qConDat,$xConexion01,"");
                  //f_Mensaje(__FILE__,__LINE__,$qConDat."~".mysql_num_rows($xConDat));
                  if (mysql_num_rows($xConDat) > 0) {
                    $vConDat = mysql_fetch_array($xConDat);
                  }
                  $nC = count($vContactos);
                }
              }
            }//if($vCocDat['CLICONTX'] != ""){
            ##Fin Traigo Datos de Contacto del Facturado a ##

            ##Exploto campo Matriz para traer primer Do y traer Datos de Tasa de Cambio, Documento de Transporte, Bultos, Peso ##
            $cDocId    = ""; $cSucId    = ""; $cDocSuf    = "";
            $cDocIdAux = ""; $cSucIdAux = ""; $cDocSufAux = "";
            $mDoiId  = explode("|",$vCocDat['comfpxxx']);
            $vDirCue = array(); $cNomDirCue = "";
            $vDos    = array(); $nEncontro  = 0;

            for ($i=0;$i<count($mDoiId);$i++) {
              if($mDoiId[$i] != ""){

                $vDoiId  = explode("~",$mDoiId[$i]);
                $vDos[count($vDos)] = "{$vDoiId[15]}-{$vDoiId[2]}-{$vDoiId[3]}"; //Dos para compara las cartas bancarias y descriminar los tributos

                if ($nEncontro == 0) {
                  $cDocIdAux  = $vDoiId[2];
                  $cDocSufAux = $vDoiId[3];
                  $cSucIdAux  = $vDoiId[15];
                  if ($vDoiId[4] != "REGISTRO") {
                    $cDocId  = $vDoiId[2];
                    $cDocSuf = $vDoiId[3];
                    $cSucId = $vDoiId[15];
                    $nEncontro = 1;
                  }
                }
              }//if($mDoiId[$i] != ""){
            }//for ($i=0;$i<count($mDoiId);$i++) {
            ##Fin Exploto campo Matriz para traer primer Do y traer Datos de Tasa de Cambio, Documento de Transporte, Bultos, Peso ##

            if ($cDocId == "") {
              $cDocId  = $cDocIdAux;
              $cDocSuf = $cDocSufAux;
              $cSucId  = $cSucIdAux;
            }

            ##Trayendo Datos de Do Dependiendo del Tipo de Operacion ##
            $vDatDo = f_Datos_Operativos_Do($cSucId, $cDocId, $cDocSuf);
            $vDceDat    = $vDatDo['decdatxx'];
            $cTasCam    = $vDatDo['tascamxx']; //Tasa de Cambio
            $cDocTra    = $vDatDo['doctraxx']; //Documento de Transporte
            $cBultos    = $vDatDo['bultosxx']; //Bultos
            $cPesBru    = $vDatDo['pesbruxx']; //Peso Bruto
            $nValAdu    = $vDatDo['valaduxx']; //Valor en aduana
            $nValAduCop = $vDatDo['valaduco']; //Valor en aduana
            $nValAduUsd = $vDatDo['valaduus']; //Valor en aduana
            $cOpera     = $vDatDo['operaxxx']; //texto valor CIF: o FOB: segun el tipo operacion del tramite
            $cPedido    = $vDatDo['pedidoxx']; //Pedido
            $cAduana    = $vDatDo['aduanaxx']; //Descripcion Aduana
            $cNomVen    = $vDatDo['nomvenxx']; //Nombre Vendedor
            $cOrdCom    = $vDatDo['ordcomxx']; //Orden de Compra
            $cPaiOri    = $vDatDo['paiorixx']; //Pais de Origen
            $cPaiOriDes = $vDatDo['paioride']; //Pais de Origen Descripcion
            $cDepOri    = $vDatDo['deporide']; //Departamento Origen
            $cIdDirCue  = $vDatDo['diridxxx']; //Identificacion Director de cuenta
            $cNomDirCue = $vDatDo['dircuexx']; //Nombre Director de cuenta
            $cDeposito  = $vDatDo['daadesxx']; //Descripcion deposito de la SIAI0110
            $cLugIngDes = $vDatDo['lindesxx']; //Lugar de Ingreso
            ###Fin Trayendo Datos de Do Dependiendo del Tipo de Operacion ##

            if (in_array($cIdDirCue,$vDirCue) == false) {
              $vDirCue[count($vDirCue)] = $cIdDirCue;
            }

            //Para el caso de envio de correo desde el tracking o desde el graba,
            //solo se completa el proceso de generacion de la factura si hay correos a quien enviar
            $nGenerar = 0;
            if ($pArrayParametros['CORREOXX'] != 0) {
              if (count($this->fnContactos($vCocDat['terid2xx'],$vDirCue)) == 0) {
                $nGenerar = 1;
                if ($pArrayParametros['CORREOXX'] == 1) {
                  $nSwitch = 1;
                  $mReturn[count($mReturn)] = "Los Contactos o Directores de Cuenta Asignados al Tramite, No Tienen Correos Parametrizados.";
                }
              }
            }

            if ($nGenerar == 0) {
              //Trayendo el valor de los tributos
              $mIT     = f_Explode_Array($vCocDat['commemod'],"|","~");
              $mIngTer = array();
              $mPccTC  = array(); //Tasa de cambio, cuando la moneda es en USD
              $mPccTP  = array(); //Tasa Pactada, cuando la moneda es en COP y el pago a tercero tiene tasa de cambio pactada
              $vComTri = array();

              $nConTri   = 0;
              $vNoDetTri = array(); //Variable que indica cuantas cartas de pago de tribuos no tienen los valores discriminados (este caso aplica para SIACO)

              for ($i=0;$i<count($mIT);$i++) {
                if ($mIT[$i][1] != "") {
                  $vTercero = explode("^",$mIT[$i][2]);

                  $cSwitch_Comprobante_Pago_Impuestos = "NO";
                  $qComAju  = "SELECT * ";
                  $qComAju .= "FROM $cAlfa.fpar0117 ";
                  $qComAju .= "WHERE ";
                  $qComAju .= "comidxxx = \"{$mIT[$i][3]}\" AND ";
                  $qComAju .= "comcodxx = \"{$mIT[$i][4]}\" AND ";
                  $qComAju .= "comtipxx = \"PAGOIMPUESTOS\" AND ";
                  $qComAju .= "regestxx = \"ACTIVO\" LIMIT 0,1";
                  $xComAju  = f_MySql("SELECT","",$qComAju,$xConexion01,"");
                  //f_Mensaje(__FILE__,__LINE__,$qComAju." ~ ".mysql_num_rows($xComAju));

                  if (mysql_num_rows($xComAju) == 1) {
                    $qCtoCba  = "SELECT * ";
                    $qCtoCba .= "FROM $cAlfa.fpar0119 "; // Aqui no aplica la busqueda contra la fpar0121
                    $qCtoCba .= "WHERE ";
                    $qCtoCba .= "pucidxxx = \"{$mIT[$i][9]}\" AND ";
                    $qCtoCba .= "ctocomxx LIKE \"%{$mIT[$i][3]}~{$mIT[$i][4]}%\" AND ";
                    $qCtoCba .= "ctoidxxx = \"{$mIT[$i][1]}\" AND ";
                    $qCtoCba .= "regestxx = \"ACTIVO\" LIMIT 0,1";
                    $xCtoCba  = f_MySql("SELECT","",$qCtoCba,$xConexion01,"");
                    //f_Mensaje(__FILE__,__LINE__,$qCtoCba." ~ ".mysql_num_rows($xCtoCba));

                    if (mysql_num_rows($xCtoCba) == 1) {
                      $vCtoCba = mysql_fetch_array($xCtoCba);
                      if ($vCtoCba['ctoptaxg'] == "SI" || $vCtoCba['ctoptaxl'] == "SI") {
                        $cSwitch_Comprobante_Pago_Impuestos = "SI";
                      } else {
                        $cSwitch_Comprobante_Pago_Impuestos = "NO";
                      }
                    }
                  }

                  if ($cSwitch_Comprobante_Pago_Impuestos == "SI") { //pago de impuestos.
                    $mIT[$i][200] = "SI";

                    if (in_array("{$mIT[$i][3]}-{$mIT[$i][4]}-{$mIT[$i][5]}", $vComTri) == false) {
                      $vComTri[] = "{$mIT[$i][3]}-{$mIT[$i][4]}-{$mIT[$i][5]}";

                      $nSumAra = 0; // Variable para acumular el valor del arancel declaracion x declaracion
                      $nSumIva = 0; // Variable para acumular el valor del iva declaracion x declaracion
                      $nSumSal = 0; // Variable para acumular el valor de las salvaguardias declaracion x declaracion
                      $nSumCom = 0; // Variable para acumular el valor de los derechos compensantorios declaracion x declaracion
                      $nSumAnt = 0; // Variable para acumular el valor de los derechos antidumping declaracion x declaracion
                      $nSumSan = 0; // Variable para acumular el valor de las sanciones declaracion x declaracion
                      $nSumRes = 0; // Variable para acumular el valor de los rescates declaracion x declaracion

                      $vTramites = array();

                      //Busco en este anio la carta bancaria
                      $cAno = date('Y');
                      $qFcod  = "SELECT commemod ";
                      $qFcod .= "FROM $cAlfa.fcoc$cAno ";
                      $qFcod .= "WHERE ";
                      $qFcod .= "comidxxx = \"{$mIT[$i][3]}\" AND ";
                      $qFcod .= "comcodxx = \"{$mIT[$i][4]}\" AND ";
                      $qFcod .= "comcscxx = \"{$mIT[$i][5]}\" AND ";
                      $qFcod .= "commemod != \"\" LIMIT 0,1 ";
                      $xFcod  = f_MySql("SELECT","",$qFcod,$xConexion01,"");
                      //f_Mensaje(__FILE__,__LINE__,$qFcod." ~ ".mysql_num_rows($xFcod));
                      $nEncCarta = 0;
                      if (mysql_num_rows($xFcod) > 0) {
                        $nEncCarta = 1;
                      } else {
                        $cAnoAnt = (($cAno - 1) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : ($cAno - 1);
                        $qFcod = str_replace("fcoc$cAno", "fcoc$cAnoAnt", $qFcod);
                        $xFcod  = f_MySql("SELECT","",$qFcod,$xConexion01,"");
                        //f_Mensaje(__FILE__,__LINE__,$qFcod." ~ ".mysql_num_rows($xFcod));
                        if (mysql_num_rows($xFcod) > 0) {
                          $nEncCarta = 1;
                        }
                      }

                      if ($nEncCarta == 1) {
                        $xRFcod = mysql_fetch_array($xFcod);
                        $mTributos01 = explode("|",$xRFcod['commemod']);
                        for ($k=0;$k<count($mTributos01);$k++) {
                          if ($mTributos01[$k] != "") {
                            $mTributos02 = explode("~",$mTributos01[$k]);
                            if (in_array("{$mTributos02[0]}-{$mTributos02[1]}-{$mTributos02[2]}",$vDos) == true) {
                              if (count($mTributos02) == 7) { //Desde conexion GRM
                                $nSumAra += $mTributos02[4];
                                $nSumIva += $mTributos02[5];
                                if ($mTributos02[4] == 0 && $mTributos02[5] == 0){
                                  $vNoDetTri["{$mIT[$i][3]}~{$mIT[$i][4]}~{$mIT[$i][5]}"]++;
                                }
                              } else {
                                $nSumAra += $mTributos02[4];
                                $nSumIva += $mTributos02[5];
                                $nSumSal += $mTributos02[6];
                                $nSumCom += $mTributos02[7];
                                $nSumAnt += $mTributos02[8];
                                $nSumSan += $mTributos02[9];
                                $nSumRes += $mTributos02[10];
                                if ($mTributos02[4] == 0 && $mTributos02[5] == 0 && $mTributos02[6] == 0 && $mTributos02[7] == 0 && $mTributos02[8] == 0 && $mTributos02[9] == 0 && $mTributos02[10] == 0) {
                                  $vNoDetTri["{$mIT[$i][3]}~{$mIT[$i][4]}~{$mIT[$i][5]}"]++;
                                }
                              }
                              if (in_array("{$mTributos02[1]}", $vTramites) == false) {
                                $vTramites[count($vTramites)] = "{$mTributos02[1]}";
                              }
                            }## if (in_array("{$mTributos02[0]}-{$mTributos02[1]}-{$mTributos02[2]}",$vDos) == true) {
                          } ## if ($mTributos01[$k] != "") {
                        } ## for ($k=0;$k<count($mTributos01);$k++) {
                      } else {
                        $vNoDetTri["{$mIT[$i][3]}~{$mIT[$i][4]}~{$mIT[$i][5]}"]++;
                      }## if (mysql_num_rows($xFcod) > 0) {

                      if (($vNoDetTri["{$mIT[$i][3]}~{$mIT[$i][4]}~{$mIT[$i][5]}"]+0) > 0 || $cForImp == "REGALIAS") {
                        $nSumAra = 0;
                        $nSumIva = 0;
                        $nSumSal = 0;
                        $nSumCom = 0;
                        $nSumAnt = 0;
                        $nSumSan = 0;
                        $nSumRes = 0;
                      }
                      if ($nSumAra > 0 || $nSumIva > 0 || $nSumSal > 0 || $nSumCom > 0 || $nSumAnt > 0 || $nSumSan > 0 || $nSumRes > 0) {
                        if ($nSumAra > 0) {
                          $nSwitch_Find = 0;
                          for ($j=0;$j<count($mIngTer);$j++) {
                            if ($mIngTer[$j][102] == "ARANCEL") {
                              $nSwitch_Find = 1;
                              if ($cForImp != "REGALIAS" && $vCocDat['teridxxx'] != "900736182") { //Para nestle no imprime DO's
                                for ($nT=0; $nT<count($vTramites); $nT++) {
                                  if (in_array("{$vTramites[$nT]}",$mIngTer[$j][101]) == false) {
                                    $mIngTer[$j][100]  .= "/"."{$vTramites[$nT]}";
                                    $mIngTer[$j][101][] = "{$vTramites[$nT]}";
                                  }
                                }
                              }
                              // $mIngTer[$j][7] += ($nSumAra+0);
                              $mIngTer[$j][7] += ($vCccDat['cccimpus'] == "SI") ? (round(($nSumAra/$vCocDat['tcatasax']),3)) : ($nSumAra+0);
                            }
                          }
                          if ($nSwitch_Find == 0) { // No lo encontro en la matriz para pintar en la factura
                            $nInd_mIngTer = count($mIngTer);
                            $mIngTer[$nInd_mIngTer]      = $mIT[$i]; // Ingreso el registro como nuevo.
                            $mIngTer[$nInd_mIngTer][98]  = $vTercero[2];
                            $mIngTer[$nInd_mIngTer][99]  = "PAGO TRIBUTOS ADUANEROS GRAVAMEN ARANCELARIO";
                            if ($cForImp != "REGALIAS" && $vCocDat['teridxxx'] != "900736182") { //Para nestle no imprime DO's
                              for ($nT=0; $nT<count($vTramites); $nT++) {
                                if (in_array("{$vTramites[$nT]}",$mIngTer[$nInd_mIngTer][101]) == false) {
                                  $mIngTer[$nInd_mIngTer][100]  .= "/"."{$vTramites[$nT]}";
                                  $mIngTer[$nInd_mIngTer][101][] = "{$vTramites[$nT]}";
                                }
                              }
                            }
                            $mIngTer[$nInd_mIngTer][100] = (substr($mIngTer[$j][100],0,1) == "/") ? substr($mIngTer[$j][100],1,strlen($mIngTer[$j][100])) : $mIngTer[$j][100];
                            // $mIngTer[$nInd_mIngTer][7]   = ($nSumAra+0); // Acumulo el valor de ingreso para tercero.
                            $mIngTer[$nInd_mIngTer][7]   = ($vCccDat['cccimpus'] == "SI") ? (round(($nSumAra/$vCocDat['tcatasax']),3)) : ($nSumAra+0);; // Acumulo el valor de ingreso para tercero.
                            $mIngTer[$nInd_mIngTer][102] = "ARANCEL";
                          }
                        }

                        if ($nSumIva > 0) {
                          $nSwitch_Find = 0;
                          for ($j=0;$j<count($mIngTer);$j++) {
                            if ($mIngTer[$j][102] == "IVA") {
                              $nSwitch_Find = 1;
                              if ($cForImp != "REGALIAS" && $vCocDat['teridxxx'] != "900736182") { //Para nestle no imprime DO's
                                for ($nT=0; $nT<count($vTramites); $nT++) {
                                  if (in_array("{$vTramites[$nT]}",$mIngTer[$j][101]) == false) {
                                    $mIngTer[$j][100]  .= "/"."{$vTramites[$nT]}";
                                    $mIngTer[$j][101][] = "{$vTramites[$nT]}";
                                  }
                                }
                              }
                              // $mIngTer[$j][7] += ($nSumIva+0);
                              $mIngTer[$j][7] += ($vCccDat['cccimpus'] == "SI") ? (round(($nSumIva/$vCocDat['tcatasax']),3)) : ($nSumIva+0);
                            }
                          }
                          if ($nSwitch_Find == 0) { // No lo encontro en la matriz para pintar en la factura
                            $nInd_mIngTer = count($mIngTer);
                            $mIngTer[$nInd_mIngTer]      = $mIT[$i]; // Ingreso el registro como nuevo.
                            $mIngTer[$nInd_mIngTer][98]  = $vTercero[2];
                            $mIngTer[$nInd_mIngTer][99]  = "PAGO TRIBUTOS ADUANEROS IMPUESTO A LAS VENTAS";
                            if ($cForImp != "REGALIAS" && $vCocDat['teridxxx'] != "900736182") { //Para nestle no imprime DO's
                              for ($nT=0; $nT<count($vTramites); $nT++) {
                                if (in_array("{$vTramites[$nT]}",$mIngTer[$nInd_mIngTer][101]) == false) {
                                  $mIngTer[$nInd_mIngTer][100]  .= "/"."{$vTramites[$nT]}";
                                  $mIngTer[$nInd_mIngTer][101][] = "{$vTramites[$nT]}";
                                }
                              }
                            }
                            $mIngTer[$nInd_mIngTer][100] = (substr($mIngTer[$j][100],0,1) == "/") ? substr($mIngTer[$j][100],1,strlen($mIngTer[$j][100])) : $mIngTer[$j][100];
                            // $mIngTer[$nInd_mIngTer][7]   = ($nSumIva+0); // Acumulo el valor de ingreso para tercero.
                            $mIngTer[$nInd_mIngTer][7]   = ($vCccDat['cccimpus'] == "SI") ? (round(($nSumIva/$vCocDat['tcatasax']),3)) : ($nSumIva+0);; // Acumulo el valor de ingreso para tercero.
                            $mIngTer[$nInd_mIngTer][102] = "IVA";
                          }
                        }

                        if ($nSumSal > 0) {
                          $nSwitch_Find = 0;
                          for ($j=0;$j<count($mIngTer);$j++) {
                            if ($mIngTer[$j][102] == "SALVAGUARDIAS") {
                              $nSwitch_Find = 1;
                              if ($cForImp != "REGALIAS" && $vCocDat['teridxxx'] != "900736182") { //Para nestle no imprime DO's
                                for ($nT=0; $nT<count($vTramites); $nT++) {
                                  if (in_array("{$vTramites[$nT]}",$mIngTer[$j][101]) == false) {
                                    $mIngTer[$j][100]  .= "/"."{$vTramites[$nT]}";
                                    $mIngTer[$j][101][] = "{$vTramites[$nT]}";
                                  }
                                }
                              }
                              // $mIngTer[$j][7] += ($nSumSal+0);
                              $mIngTer[$j][7] += ($vCccDat['cccimpus'] == "SI") ? (round(($nSumSal/$vCocDat['tcatasax']),3)) : ($nSumSal+0);
                            }
                          }
                          if ($nSwitch_Find == 0) { // No lo encontro en la matriz para pintar en la factura
                            $nInd_mIngTer = count($mIngTer);
                            $mIngTer[$nInd_mIngTer]      = $mIT[$i]; // Ingreso el registro como nuevo.
                            $mIngTer[$nInd_mIngTer][98]  = $vTercero[2];
                            $mIngTer[$nInd_mIngTer][99]  = "PAGO TRIBUTOS ADUANEROS SALVAGUARDIAS";
                            if ($cForImp != "REGALIAS" && $vCocDat['teridxxx'] != "900736182") { //Para nestle no imprime DO's
                              for ($nT=0; $nT<count($vTramites); $nT++) {
                                if (in_array("{$vTramites[$nT]}",$mIngTer[$nInd_mIngTer][101]) == false) {
                                  $mIngTer[$nInd_mIngTer][100]  .= "/"."{$vTramites[$nT]}";
                                  $mIngTer[$nInd_mIngTer][101][] = "{$vTramites[$nT]}";
                                }
                              }
                            }
                            $mIngTer[$nInd_mIngTer][100] = (substr($mIngTer[$j][100],0,1) == "/") ? substr($mIngTer[$j][100],1,strlen($mIngTer[$j][100])) : $mIngTer[$j][100];
                            // $mIngTer[$nInd_mIngTer][7]   = ($nSumSal+0); // Acumulo el valor de ingreso para tercero.
                            $mIngTer[$nInd_mIngTer][7]   = ($vCccDat['cccimpus'] == "SI") ? (round(($nSumSal/$vCocDat['tcatasax']),3)) : ($nSumSal+0); // Acumulo el valor de ingreso para tercero.
                            $mIngTer[$nInd_mIngTer][102] = "SALVAGUARDIAS";
                          }
                        }

                        if ($nSumCom > 0) {
                          $nSwitch_Find = 0;
                          for ($j=0;$j<count($mIngTer);$j++) {
                            if ($mIngTer[$j][102] == "COMPENSATORIOS") {
                              $nSwitch_Find = 1;
                              if ($cForImp != "REGALIAS" && $vCocDat['teridxxx'] != "900736182") { //Para nestle no imprime DO's
                                for ($nT=0; $nT<count($vTramites); $nT++) {
                                  if (in_array("{$vTramites[$nT]}",$mIngTer[$j][101]) == false) {
                                    $mIngTer[$j][100]  .= "/"."{$vTramites[$nT]}";
                                    $mIngTer[$j][101][] = "{$vTramites[$nT]}";
                                  }
                                }
                              }
                              // $mIngTer[$j][7] += ($nSumCom+0);
                              $mIngTer[$j][7] += ($vCccDat['cccimpus'] == "SI") ? (round(($nSumCom/$vCocDat['tcatasax']),3)) : ($nSumCom+0);
                            }
                          }
                          if ($nSwitch_Find == 0) { // No lo encontro en la matriz para pintar en la factura
                            $nInd_mIngTer = count($mIngTer);
                            $mIngTer[$nInd_mIngTer]      = $mIT[$i]; // Ingreso el registro como nuevo.
                            $mIngTer[$nInd_mIngTer][98]  = $vTercero[2];
                            $mIngTer[$nInd_mIngTer][99]  = "PAGO TRIBUTOS ADUANEROS DERECHOS COMPENSATORIOS";
                            if ($cForImp != "REGALIAS" && $vCocDat['teridxxx'] != "900736182") { //Para nestle no imprime DO's
                              for ($nT=0; $nT<count($vTramites); $nT++) {
                                if (in_array("{$vTramites[$nT]}",$mIngTer[$nInd_mIngTer][101]) == false) {
                                  $mIngTer[$nInd_mIngTer][100]  .= "/"."{$vTramites[$nT]}";
                                  $mIngTer[$nInd_mIngTer][101][] = "{$vTramites[$nT]}";
                                }
                              }
                            }
                            $mIngTer[$nInd_mIngTer][100] = (substr($mIngTer[$j][100],0,1) == "/") ? substr($mIngTer[$j][100],1,strlen($mIngTer[$j][100])) : $mIngTer[$j][100];
                            // $mIngTer[$nInd_mIngTer][7]   = ($nSumCom+0); // Acumulo el valor de ingreso para tercero.
                            $mIngTer[$nInd_mIngTer][7]   = ($vCccDat['cccimpus'] == "SI") ? (round(($nSumCom/$vCocDat['tcatasax']),3)) : ($nSumCom+0); // Acumulo el valor de ingreso para tercero.
                            $mIngTer[$nInd_mIngTer][102] = "COMPENSATORIOS";
                          }
                        }

                        if ($nSumAnt > 0) {
                          $nSwitch_Find = 0;
                          for ($j=0;$j<count($mIngTer);$j++) {
                            if ($mIngTer[$j][102] == "ANTIDUMPING") {
                              $nSwitch_Find = 1;
                              if ($cForImp != "REGALIAS" && $vCocDat['teridxxx'] != "900736182") { //Para nestle no imprime DO's
                                for ($nT=0; $nT<count($vTramites); $nT++) {
                                  if (in_array("{$vTramites[$nT]}",$mIngTer[$j][101]) == false) {
                                    $mIngTer[$j][100]  .= "/"."{$vTramites[$nT]}";
                                    $mIngTer[$j][101][] = "{$vTramites[$nT]}";
                                  }
                                }
                              }
                              // $mIngTer[$j][7] += ($nSumAnt+0);
                              $mIngTer[$j][7] += ($vCccDat['cccimpus'] == "SI") ? (round(($nSumAnt/$vCocDat['tcatasax']),3)) : ($nSumAnt+0);
                            }
                          }
                          if ($nSwitch_Find == 0) { // No lo encontro en la matriz para pintar en la factura
                            $nInd_mIngTer = count($mIngTer);
                            $mIngTer[$nInd_mIngTer]      = $mIT[$i]; // Ingreso el registro como nuevo.
                            $mIngTer[$nInd_mIngTer][98]  = $vTercero[2];
                            $mIngTer[$nInd_mIngTer][99]  = "PAGO TRIBUTOS ADUANEROS DERECHOS ANTIDUMPING";
                            if ($cForImp != "REGALIAS" && $vCocDat['teridxxx'] != "900736182") { //Para nestle no imprime DO's
                              for ($nT=0; $nT<count($vTramites); $nT++) {
                                if (in_array("{$vTramites[$nT]}",$mIngTer[$nInd_mIngTer][101]) == false) {
                                  $mIngTer[$nInd_mIngTer][100]  .= "/"."{$vTramites[$nT]}";
                                  $mIngTer[$nInd_mIngTer][101][] = "{$vTramites[$nT]}";
                                }
                              }
                            }
                            $mIngTer[$nInd_mIngTer][100] = (substr($mIngTer[$j][100],0,1) == "/") ? substr($mIngTer[$j][100],1,strlen($mIngTer[$j][100])) : $mIngTer[$j][100];
                            // $mIngTer[$nInd_mIngTer][7]   = ($nSumAnt+0); // Acumulo el valor de ingreso para tercero.
                            $mIngTer[$nInd_mIngTer][7]   = ($vCccDat['cccimpus'] == "SI") ? (round(($nSumAnt/$vCocDat['tcatasax']),3)) : ($nSumAnt+0); // Acumulo el valor de ingreso para tercero.
                            $mIngTer[$nInd_mIngTer][102] = "ANTIDUMPING";
                          }
                        }

                        if ($nSumSan > 0) {
                          $nSwitch_Find = 0;
                          for ($j=0;$j<count($mIngTer);$j++) {
                            if ($mIngTer[$j][102] == "SANCIONES") {
                              $nSwitch_Find = 1;
                              if ($cForImp != "REGALIAS" && $vCocDat['teridxxx'] != "900736182") { //Para nestle no imprime DO's
                                for ($nT=0; $nT<count($vTramites); $nT++) {
                                  if (in_array("{$vTramites[$nT]}",$mIngTer[$j][101]) == false) {
                                    $mIngTer[$j][100]  .= "/"."{$vTramites[$nT]}";
                                    $mIngTer[$j][101][] = "{$vTramites[$nT]}";
                                  }
                                }
                              }
                              // $mIngTer[$j][7] += ($nSumSan+0);
                              $mIngTer[$j][7] += ($vCccDat['cccimpus'] == "SI") ? (round(($nSumSan/$vCocDat['tcatasax']),3)) : ($nSumSan+0);
                            }
                          }
                          if ($nSwitch_Find == 0) { // No lo encontro en la matriz para pintar en la factura
                            $nInd_mIngTer = count($mIngTer);
                            $mIngTer[$nInd_mIngTer]      = $mIT[$i]; // Ingreso el registro como nuevo.
                            $mIngTer[$nInd_mIngTer][98]  = $vTercero[2];
                            $mIngTer[$nInd_mIngTer][99]  = "PAGO TRIBUTOS ADUANEROS SANCIONES";
                            if ($cForImp != "REGALIAS" && $vCocDat['teridxxx'] != "900736182") { //Para nestle no imprime DO's
                              for ($nT=0; $nT<count($vTramites); $nT++) {
                                if (in_array("{$vTramites[$nT]}",$mIngTer[$nInd_mIngTer][101]) == false) {
                                  $mIngTer[$nInd_mIngTer][100]  .= "/"."{$vTramites[$nT]}";
                                  $mIngTer[$nInd_mIngTer][101][] = "{$vTramites[$nT]}";
                                }
                              }
                            }
                            $mIngTer[$nInd_mIngTer][100] = (substr($mIngTer[$j][100],0,1) == "/") ? substr($mIngTer[$j][100],1,strlen($mIngTer[$j][100])) : $mIngTer[$j][100];
                            // $mIngTer[$nInd_mIngTer][7]   = ($nSumSan+0); // Acumulo el valor de ingreso para tercero.
                            $mIngTer[$nInd_mIngTer][7]   = ($vCccDat['cccimpus'] == "SI") ? (round(($nSumSan/$vCocDat['tcatasax']),3)) : ($nSumSan+0); // Acumulo el valor de ingreso para tercero.
                            $mIngTer[$nInd_mIngTer][102] = "SANCIONES";
                          }
                        }

                        if ($nSumRes > 0) {
                          $nSwitch_Find = 0;
                          for ($j=0;$j<count($mIngTer);$j++) {
                            if ($mIngTer[$j][102] == "RESCATES") {
                              $nSwitch_Find = 1;
                              if ($cForImp != "REGALIAS" && $vCocDat['teridxxx'] != "900736182") { //Para nestle no imprime DO's
                                for ($nT=0; $nT<count($vTramites); $nT++) {
                                  if (in_array("{$vTramites[$nT]}",$mIngTer[$j][101]) == false) {
                                    $mIngTer[$j][100]  .= "/"."{$vTramites[$nT]}";
                                    $mIngTer[$j][101][] = "{$vTramites[$nT]}";
                                  }
                                }
                              }
                              // $mIngTer[$j][7] += ($nSumRes+0);
                              $mIngTer[$j][7] += ($vCccDat['cccimpus'] == "SI") ? (round(($nSumRes/$vCocDat['tcatasax']),3)) : ($nSumRes+0);
                            }
                          }
                          if ($nSwitch_Find == 0) { // No lo encontro en la matriz para pintar en la factura
                            $nInd_mIngTer = count($mIngTer);
                            $mIngTer[$nInd_mIngTer]      = $mIT[$i]; // Ingreso el registro como nuevo.
                            $mIngTer[$nInd_mIngTer][98]  = $vTercero[2];
                            $mIngTer[$nInd_mIngTer][99]  = "PAGO TRIBUTOS ADUANEROS RESCATES";
                            if ($cForImp != "REGALIAS" && $vCocDat['teridxxx'] != "900736182") { //Para nestle no imprime DO's
                              for ($nT=0; $nT<count($vTramites); $nT++) {
                                if (in_array("{$vTramites[$nT]}",$mIngTer[$nInd_mIngTer][101]) == false) {
                                  $mIngTer[$nInd_mIngTer][100]  .= "/"."{$vTramites[$nT]}";
                                  $mIngTer[$nInd_mIngTer][101][] = "{$vTramites[$nT]}";
                                }
                              }
                            }
                            $mIngTer[$nInd_mIngTer][100] = (substr($mIngTer[$j][100],0,1) == "/") ? substr($mIngTer[$j][100],1,strlen($mIngTer[$j][100])) : $mIngTer[$j][100];
                            // $mIngTer[$nInd_mIngTer][7]   = ($nSumRes+0); // Acumulo el valor de ingreso para tercero.
                            $mIngTer[$nInd_mIngTer][7]   = ($vCccDat['cccimpus'] == "SI") ? (round(($nSumRes/$vCocDat['tcatasax']),3)) : ($nSumRes+0); // Acumulo el valor de ingreso para tercero.
                            $mIngTer[$nInd_mIngTer][102] = "RESCATES";
                          }
                        }
                      }
                    } ## if (in_array("{$mIT[$i][3]}-{$mIT[$i][4]}-{$mIT[$i][5]}", $vComTri) == false) {

                    if ($vNoDetTri["{$mIT[$i][3]}~{$mIT[$i][4]}~{$mIT[$i][5]}"] > 0 || $cForImp == "REGALIAS") {
                      //Indico que ese registro de la matriz de pagos terceros es de tributos
                      $nConTri++;
                      $mIT[$i][201] = "TRIBUTOS";
                      $mIT[$i][203] = $vTramites; //Tramites
                    }
                  } ## if ($cSwitch_Comprobante_Pago_Impuestos == "SI") {
                } ## if ($mIT[$i][1] != "") {
              } ## for ($i=0;$i<count($mIT);$i++) {

              if ($nConTri > 0) { //Alguna de las cartas bancarias no discrimino
                for ($i=0;$i<count($mIT);$i++) {
                  if ($mIT[$i][1] != "") {
                    $vTercero = explode("^",$mIT[$i][2]);
                    $vTramites = array(); $vTramites = $mIT[$i][203];

                    if ($mIT[$i][200] == "SI" && $mIT[$i][201] == "TRIBUTOS") {
                      $nSwitch_Find = 0;
                      for ($j=0;$j<count($mIngTer);$j++) {
                        if ($mIngTer[$j][102] == "TRIBUTOS") {
                          $nSwitch_Find = 1;
                          if ($cForImp != "REGALIAS" && $vCocDat['teridxxx'] != "900736182") { //Para nestle no imprime DO's
                            $vDo = explode("-", $mIT[$i][14]);
                            if (in_array("{$vDo[1]}",$mIngTer[$j][101]) == false) {
                              $mIngTer[$j][100]  .= "/"."{$vDo[1]}";
                              $mIngTer[$j][101][] = "{$vDo[1]}";
                            }
                          }
                          // $mIngTer[$j][7]  += $mIT[$i][7]; // Acumulo el valor de ingreso para tercero.
                          $mIngTer[$j][7]  += ($vCccDat['cccimpus'] == "SI") ? $mIT[$i][20] : $mIT[$i][7]; // Acumulo el valor de ingreso para tercero.
                        }
                      }
                      
                      if ($nSwitch_Find == 0) { // No lo encontro en la matriz para pintar en la factura
                        
                        $mIT[$i][7]  = ($vCccDat['cccimpus'] == "SI") ? $mIT[$i][20] : $mIT[$i][7]; // Acumulo el valor de ingreso para tercero.

                        $nInd_mIngTer = count($mIngTer);
                        $mIngTer[$nInd_mIngTer]      = $mIT[$i]; // Ingreso el registro como nuevo.
                        $mIngTer[$nInd_mIngTer][98]  = $vTercero[2];
                        $mIngTer[$nInd_mIngTer][99]  = (trim($vTercero[0]) != "PAGO TRIBUTOS ADUANEROS") ? "PAGO TRIBUTOS ADUANEROS ".$vTercero[0] : $vTercero[0];
                        //En la carta de tributos el documento es el DO
                        if ($cForImp != "REGALIAS" && $vCocDat['teridxxx'] != "900736182") { //Para nestle no imprime DO's
                          $vDo = explode("-", $mIT[$i][14]);
                          $mIngTer[$nInd_mIngTer][100]   = $mIngTer[$j][100]."/".$vDo[1];
                          $mIngTer[$nInd_mIngTer][101][] = "{$vDo[1]}";
                        }
                        $mIngTer[$nInd_mIngTer][100]   = (substr($mIngTer[$nInd_mIngTer][100],0,1) == "/") ? substr($mIngTer[$nInd_mIngTer][100],1,strlen($mIngTer[$nInd_mIngTer][100])) : $mIngTer[$nInd_mIngTer][100];
                        $mIngTer[$nInd_mIngTer][102] = "TRIBUTOS";
                      }
                    }
                  }
                }
              }

              //Agrupando los valores por tipo
              $nSumAra = 0;	$nSumIva = 0;	$nSumSal = 0; $nSumCom = 0; $nSumAnt = 0;
              $nSumSan = 0; $nSumRes = 0;	$nSumTri = 0;

              for ($i=0;$i<count($mIT);$i++) {
                if ($mIT[$i][200] == "SI") { //Indica que es de tributos

                  $vTercero = explode("^",$mIT[$i][2]);

                  if (substr_count(trim($vTercero[0]), "GRAVAMEN ARANCELARIO") > 0) {
                    // $nSumAra +=  $mIT[$i][7];
                    $nSumAra +=  ($vCccDat['cccimpus'] == "SI") ? $mIT[$i][20] : $mIT[$i][7];
                  } elseif (substr_count(trim($vTercero[0]), "IMPUESTO A LAS VENTAS") > 0) {
                    // $nSumIva +=  $mIT[$i][7];
                    $nSumIva +=  ($vCccDat['cccimpus'] == "SI") ? $mIT[$i][20] : $mIT[$i][7];
                  } elseif (substr_count(trim($vTercero[0]), "SALVAGUARDIAS") > 0) {
                    // $nSumSal +=  $mIT[$i][7];
                    $nSumSal +=  ($vCccDat['cccimpus'] == "SI") ? $mIT[$i][20] : $mIT[$i][7];
                  } elseif (substr_count(trim($vTercero[0]), "DERECHOS COMPENSATORIOS") > 0) {
                    // $nSumCom +=  $mIT[$i][7];
                    $nSumCom +=  ($vCccDat['cccimpus'] == "SI") ? $mIT[$i][20] : $mIT[$i][7];
                  } elseif (substr_count(trim($vTercero[0]), "DERECHOS ANTIDUMPING") > 0) {
                    // $nSumAnt +=  $mIT[$i][7];
                    $nSumAnt +=  ($vCccDat['cccimpus'] == "SI") ? $mIT[$i][20] : $mIT[$i][7];
                  } elseif (substr_count(trim($vTercero[0]), "SANCIONES") > 0) {
                    // $nSumSan +=  $mIT[$i][7];
                    $nSumSan +=  ($vCccDat['cccimpus'] == "SI") ? $mIT[$i][20] : $mIT[$i][7];
                  } elseif (substr_count(trim($vTercero[0]), "RESCATES") > 0) {
                    // $nSumRes +=  $mIT[$i][7];
                    $nSumRes +=  ($vCccDat['cccimpus'] == "SI") ? $mIT[$i][20] : $mIT[$i][7];
                  }
                  // $nSumTri +=  $mIT[$i][7];
                  $nSumTri +=  ($vCccDat['cccimpus'] == "SI") ? $mIT[$i][20] : $mIT[$i][7];
                }
              }

              //Verificando si alguno de esos montos no tuvo ajustes
              for ($nA=0; $nA<count($mIngTer); $nA++) {
                switch ($mIngTer[$nA][102]) {
                  case "TRIBUTOS":
                    //No se logro discriminar los impuestos, si hay ajustes a todo el valor del comprobante
                    if ($mIngTer[$nA][7] != $nSumTri) {
                      $mIngTer[$nA][7]  = $nSumTri;
                    }
                  break;
                  case "ARANCEL":
                    //No se logro discriminar los impuestos, si hay ajustes a todo el valor del comprobante
                    if ($mIngTer[$nA][7] != $nSumAra) {
                      $mIngTer[$nA][7]  = $nSumAra;
                    }
                  break;
                  case "IVA":
                    //No se logro discriminar los impuestos, si hay ajustes a todo el valor del comprobante
                    if ($mIngTer[$nA][7] != $nSumIva) {
                      $mIngTer[$nA][7]  = $nSumIva;
                    }
                  break;
                  case "SALVAGUARDIAS":
                    //No se logro discriminar los impuestos, si hay ajustes a todo el valor del comprobante
                    if ($mIngTer[$nA][7] != $nSumSal) {
                      $mIngTer[$nA][7]  = $nSumSal;
                    }
                  break;
                  case "COMPENSATORIOS":
                    //No se logro discriminar los impuestos, si hay ajustes a todo el valor del comprobante
                    if ($mIngTer[$nA][7] != $nSumCom) {
                      $mIngTer[$nA][7]  = $nSumCom;
                    }
                  break;
                  case "ANTIDUMPING":
                    //No se logro discriminar los impuestos, si hay ajustes a todo el valor del comprobante
                    if ($mIngTer[$nA][7] != $nSumAnt) {
                      $mIngTer[$nA][7]  = $nSumAnt;
                    }
                  break;
                  case "SANCIONES":
                    //No se logro discriminar los impuestos, si hay ajustes a todo el valor del comprobante
                    if ($mIngTer[$nA][7] != $nSumSan) {
                      $mIngTer[$nA][7]  = $nSumSan;
                    }
                  break;
                  case "RESCATES":
                    //No se logro discriminar los impuestos, si hay ajustes a todo el valor del comprobante
                    if ($mIngTer[$nA][7] != $nSumRes) {
                      $mIngTer[$nA][7]  = $nSumRes;
                    }
                  break;
                }
              }

              // Codigo para imprimir los ingresos para terceros
              for ($i=0;$i<count($mIT);$i++) {
                if ($mIT[$i][1] != "") {
                  $vTercero = explode("^",$mIT[$i][2]);

                  if ($mIT[$i][200] != "SI") { //Diferente a pago de impuestos.

                    /**
                    * Si es un comprobante L-38 esta marcado para no agrupar, y el concepto es
                    * 2805050090 o 2805050117, los conceptos no se agrupan por proveedor, sino por concepto
                    */
                    $nAgruparConcepto = 0;
                    if ($mIT[$i][3] == "L" && $mIT[$i][4] == "038" &&
                        ($mIT[$i][1] == "2805050090" || $mIT[$i][1] == "2805050117")) {

                      $cAno = date('Y');
                      $qAjuTri  = "SELECT competxx ";
                      $qAjuTri .= "FROM $cAlfa.fcoc$cAno ";
                      $qAjuTri .= "WHERE ";
                      $qAjuTri .= "comidxxx = \"{$mIT[$i][3]}\" AND ";
                      $qAjuTri .= "comcodxx = \"{$mIT[$i][4]}\" AND ";
                      $qAjuTri .= "comcscxx = \"{$mIT[$i][5]}\" AND ";
                      $qAjuTri .= "competxx = \"SI\" LIMIT 0,1 ";
                      $xAjuTri  = f_MySql("SELECT","",$qAjuTri,$xConexion01,"");
                      //f_Mensaje(__FILE__,__LINE__,$qAjuTri."~".mysql_num_rows($xAjuTri));
                      if (mysql_num_rows($xAjuTri) > 0) {
                        $nAgruparConcepto = 1;
                        $cDoTri = explode("-", $mIT[$i][14]);
                        $mIT[$i][5] = $cDoTri[1];
                      } else {
                        $cAnoAnt = (($cAno - 1) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : ($cAno - 1);
                        $qAjuTri = str_replace("fcoc$cAno", "fcoc$cAnoAnt", $qAjuTri);
                        $xAjuTri  = f_MySql("SELECT","",$qAjuTri,$xConexion01,"");
                        //f_Mensaje(__FILE__,__LINE__,$qAjuTri." ~ ".mysql_num_rows($xAjuTri));
                        if (mysql_num_rows($xAjuTri) > 0) {
                          $nAgruparConcepto = 1;
                          $cDoTri = explode("-", $mIT[$i][14]);
                          $mIT[$i][5] = $cDoTri[1];
                        }
                      }
                    }
                    
                    //Solo los comprobantes tipo P pueden tener tasa pactada
                    $mIT[$i][103] = "SIN_TASAPAC"; //Si tiene tasa pactada, se guarda la tasa, sino se marca sin tasa pactada
                    if ($mIT[$i][3] == "P") {
                      $cAno = date('Y');
                      $qComTP  = "SELECT ";
                      $qComTP .= "comidxxx, comcodxx, comcscxx, comcsc2x ";
                      $qComTP .= "FROM $cAlfa.fcod$cAno ";
                      $qComTP .= "WHERE ";
                      $qComTP .= "comidxxx = \"{$mIT[$i][3]}\"  AND ";
                      $qComTP .= "comcodxx = \"{$mIT[$i][4]}\"  AND ";
                      $qComTP .= "comcscxx = \"{$mIT[$i][5]}\"  AND ";
                      $qComTP .= "comseqxx = \"{$mIT[$i][6]}\"  AND "; 
                      $qComTP .= "ctoidxxx = \"{$mIT[$i][1]}\"  AND "; 
                      $qComTP .= "pucidxxx = \"{$mIT[$i][9]}\"  AND "; 
                      $qComTP .= "teridxxx = \"{$mIT[$i][11]}\" AND "; 
                      $qComTP .= "terid2xx = \"{$mIT[$i][12]}\" AND ";
                      $qComTP .= "regestxx = \"ACTIVO\" LIMIT 0,1"; 
                      $xComTP  = mysql_query($qComTP, $xConexion01);
                      // echo "<br><br>".$qComTP."~".mysql_num_rows($xComTP);
                      $vComTP  = array();
                      if (mysql_num_rows($xComTP) > 0) {
                        $vComTP = mysql_fetch_array($xComTP);
                      } else {
                        $cAnoAnt = (($cAno - 1) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : ($cAno - 1);
                        $qComTP = str_replace("fcod$cAno", "fcod$cAnoAnt", $qComTP);
                        $xComTP  = f_MySql("SELECT","",$qComTP,$xConexion01,"");
                        if (mysql_num_rows($xComTP) > 0) {
                          $vComTP = mysql_fetch_array($xComTP);
                        }
                      }
                      //Encontro el documento
                      if (count($vComTP) > 0) {
                        //Buscando datos de cabecera
                        $qComObs2  = "SELECT ";
                        $qComObs2 .= "comobs2x ";
                        $qComObs2 .= "FROM $cAlfa.fcoc$cAno ";
                        $qComObs2 .= "WHERE ";
                        $qComObs2 .= "comidxxx = \"{$vComTP['comidxxx']}\" AND ";
                        $qComObs2 .= "comcodxx = \"{$vComTP['comcodxx']}\" AND ";
                        $qComObs2 .= "comcscxx = \"{$vComTP['comcscxx']}\" AND ";
                        $qComObs2 .= "comcsc2x = \"{$vComTP['comcsc2x']}\" LIMIT 0,1";
                        $xComObs2  = mysql_query($qComObs2, $xConexion01);
                        // echo "<br><br>".$qComObs2."~".mysql_num_rows($xComObs2);
                        $vComObs2  = array();
                        if(mysql_num_rows($xComObs2) > 0){
                          $vComObs2 = mysql_fetch_array($xComObs2);
                        } else {
                          $cAnoAnt = (($cAno - 1) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : ($cAno - 1);
                          $qComObs2 = str_replace("fcoc$cAno", "fcoc$cAnoAnt", $qComObs2);
                          $xComObs2  = f_MySql("SELECT","",$qComObs2,$xConexion01,"");
                          //f_Mensaje(__FILE__,__LINE__,$qComObs2." ~ ".mysql_num_rows($xAjuTri));
                          if (mysql_num_rows($xComObs2) > 0) {
                            $vComObs2 = mysql_fetch_array($xComObs2);
                          }
                        }
                        if (count($vComObs2) > 0) {
                          $vTasaPago = explode("~", $vComObs2['comobs2x']);
                          $mIT[$i][103] = ($vTasaPago[4] == "") ? "SIN_TASAPAC" : $vTasaPago[4]+0;
                        }
                      }
                    }

                    $nSwitch_Encontre_Concepto = 0;
                    for ($j=0;$j<count($mIngTer);$j++) {
                      if ($mIngTer[$j][200] != "SI") { //Diferente a pago de impuestos.
                        $nEncAgru = 0;
                        if ($nAgruparConcepto == 1) {
                          //Se agrupa por concepto
                          if ($mIngTer[$j][1] == $mIT[$i][1]) {
                            $nEncAgru = 1;
                          }
                        } else {
                          //Se agrupa por tercero
                          if (trim($mIngTer[$j][98]) == trim($vTercero[2])) {
                            $nEncAgru = 1;
                          }
                        }

                        if ($nEncAgru == 1) {
                          $nSwitch_Encontre_Concepto = 1;
                          // $mIngTer[$j][7]  += ($mIT[$i][7]+0); // Acumulo el valor de ingreso para tercero.
                          $mIngTer[$j][7]  += ($vCccDat['cccimpus'] == "SI") ? ($mIT[$i][20]+0) : ($mIT[$i][7]+0); // Acumulo el valor de ingreso para tercero.
                          $mIngTer[$j][15] += ($mIT[$i][15]+0); // Acumulo base de iva.
                          $mIngTer[$j][16] += ($mIT[$i][16]+0); // Acumulo valor del iva.
                          $mIngTer[$j][20] += ($mIT[$i][20]+0); // Acumulo el valor de ingreso para tercero en Dolares.
                          $mIngTer[$j][19]  = "SI";             // Tasa de cambio.
                          $mIngTer[$j][100] = "";
                          //Se agrupa por la tasa de cambio y se almacena el numero del comprobante.
                          $mPccTC[$j]["{$mIT[$i][19]}"][] = "{$mIT[$i][5]}";
                          
                          //Se agrupa por la tasa de cambio y se almacena el numero del comprobante.
                          $mPccTP[$j]["{$mIT[$i][103]}"][] = "{$mIT[$i][5]}";

                          $j = count($mIngTer); // Me salgo del FOR cuando encuentro el concepto.
                        }
                      }
                    }
                    if ($nSwitch_Encontre_Concepto == 0) { // No encontre el ingreso para tercero en la matrix $mIngTer

                      if ($nAgruparConcepto == 1) {
                        if ($mIT[$i][1] == "2805050090") {
                          $vTercero[1] = "PAGO TRIBUTOS ADUANEROS GRAVAMEN ARANCELARIO";
                          $vTercero[2] = "800197268";
                        }

                        if ($mIT[$i][1] == "2805050117") {
                          $vTercero[1] = "PAGO TRIBUTOS ADUANEROS IMPUESTO A LAS VENTAS";
                          $vTercero[2] = "800197268";
                        }
                      }

                      $nInd_mIngTer = count($mIngTer);
                      $mIngTer[$nInd_mIngTer] = $mIT[$i]; // Ingreso el registro como nuevo.
                      // $mIngTer[$nInd_mIngTer][7]   = ($mIT[$i][7]+0); // Acumulo el valor de ingreso para tercero.
                      $mIngTer[$nInd_mIngTer][7]   = ($vCccDat['cccimpus'] == "SI") ? ($mIT[$i][20]+0) : ($mIT[$i][7]+0); // Acumulo el valor de ingreso para tercero.
                      $mIngTer[$nInd_mIngTer][15]  = ($mIT[$i][15]+0); // Acumulo base de iva.
                      $mIngTer[$nInd_mIngTer][19]  = "SI"; 		         // Tasa de cambio.
                      $mIngTer[$nInd_mIngTer][16]  = ($mIT[$i][16]+0); // Acumulo valor del iva.
                      $mIngTer[$nInd_mIngTer][20]  = ($mIT[$i][20]+0); // Acumulo el valor de ingreso para tercero en Dolares.
                      $mIngTer[$nInd_mIngTer][98]  = $vTercero[2];
                      $mIngTer[$nInd_mIngTer][99]  = trim($vTercero[1]);
                      $mIngTer[$nInd_mIngTer][100] = "";
                      //Se agrupa por la tasa de cambio y se almacena el numero del comprobante.
                      $mPccTC[$nInd_mIngTer]["{$mIT[$i][19]}"][] = "{$mIT[$i][5]}";
                      
                      //Se agrupa por la tasa de cambio y se almacena el numero del comprobante.
                      $mPccTP[$nInd_mIngTer]["{$mIT[$i][103]}"][] = "{$mIT[$i][5]}";

                      //Cantidad por PCC;
                      $mIngTer[$nInd_mIngTer]['comcanxx'] = 1;
                    }
                  }
                }
              }
              // Fin de Codigo para imprimir los ingresos para terceros

              #Cuatro por Mil y formularios#
              for($h=0;$h<count($mValores);$h++ ){
                if (($mValores['nTotVlr'][$h]+0) > 0) {
                  $nInd_mIngTer = count($mIngTer);
                  $mIngTer[$nInd_mIngTer][7]   = $mValores['nTotVlr'][$h];
                  $mIngTer[$nInd_mIngTer][15]  = "";
                  $mIngTer[$nInd_mIngTer][16]  = "";
                  $mIngTer[$nInd_mIngTer][19]  = ($mValores['nTasaCa'][$h] != '' && $vCccDat['cccimpus'] == "SI") ? "T.C. ".number_format($mValores['nTasaCa'][$h],2,',','.') : "";
                  $mIngTer[$nInd_mIngTer][24]  = $mValores['cTipEje'][$h];
                  $mIngTer[$nInd_mIngTer][98]  = "";
                  $mIngTer[$nInd_mIngTer][99]  = $mValores['nDesVlr'][$h];
                  $mIngTer[$nInd_mIngTer][100] = "";
                  //Cantidad por PCC;
                  $mIngTer[$nInd_mIngTer]['comcanxx'] = 1;
                }
              }#Fin Cuatro por Mil y formularios#

              ### Pintando la Informacion ###
              ### Pintando la Informacion ###
              // $mIngTer = array_merge($mIngTer,$mIngTer,$mIngTer,$mIngTer,$mIngTer);
              // $mCodDat = array_merge($mCodDat, $mCodDat, $mCodDat, $mCodDat, $mCodDat);
              
              //Creando el PDF
              $pdf = new PDFSIACO('P','mm','Letter');  //Error al invocar la clase
              $pdf->AddFont('verdana','','verdana.php');
              $pdf->AddFont('verdanab','','verdanab.php');
              $pdf->SetFont('verdana','',8);
              $pdf->AliasNbPages();
              $pdf->SetMargins(0,0,0);
              $pdf->SetAutoPageBreak(0,0);

              //Tener en cuenta que para el caso del envio del correo solo se genera la original
              $nCopias =  1;
              //Caulculando la copia de cartera y contabilidad
              $nConta = 0;
              $nCarte = 0;
              $y = 1;
              $nImprimir = 0;

              do {
                $nBanPCC = 0;  		$nBanIP = 0;
                $nPosPCC = 0;  		$nPosIP = 0;
                $nSubTotPcc = 0;	$nSubToIP = 0;
                $nVrlTotalSinIva 	= 0;
                $nVrlTotalBaseIva	= 0;
                $nVrlTotalIva			= 0;
                $nContador = 1;
                $pdf->StartPageGroup();

                while ($nBanPCC == 0 || $nBanIP == 0) {
                  $pdf->AddPage();
                  ##Codigo Para impresion de Copias de Factura ##
                  switch($y) {
                    case $nConta:
                      $cNomCopia = ($pArrayParametros['CORREOXX'] == 0) ? "CARTERA" : "";
                    break;
                    case $nCarte:
                      $cNomCopia = ($pArrayParametros['CORREOXX'] == 0) ? "CONTABILIDAD" : "";
                    break;
                    default:
                      $cNomCopia = ($pArrayParametros['CORREOXX'] == 0) ? "CLIENTE" : "";
                    break;
                  }
                  ##Codigo Para impresion de Copias de Factura ##

                  ##Imprimo Pagos a Terceros ##
                  if(count($mIngTer) > 0) { //Si la matriz de Pcc tiene registros
                    if ($nBanPCC == 0) {
                      $posy = 110;
                      $posx = 10;

                      ##Imprimo Detalle de Pagos a Terceros e Ingresos Propios ##
                      $pdf->SetFont('verdanab','',8);
                      $pdf->setXY($posx,$posy);

                      $pdf->SetWidths(array(20,117,28,28));
                      $pdf->SetAligns(array("C","L","R","R"));

                      for($i=$nPosPCC;$i<count($mIngTer);$i++) {
                        if(($mIngTer[$i][7]+0) != 0) {
                          //preparando datos
                          $nSubTotPcc += $mIngTer[$i][7];
                          $pdf->SetFont('verdana','',6);
                          
                          //si la moneda es en dolares, para los comprobantes que aplique se debe mostrar la tasa de cambio
                          if ($vCccDat['cccimpus'] == "SI") {
                            if ($mIngTer[$i][19] == "SI") {
                              //Recorro el array de la Tasa de Cambio y el comprobante
                              $cCadena = "";
                              foreach ($mPccTC[$i] as $cKey => $cValue) {
                                $vAuxDoc = array_unique($cValue);
                                $cComprobante = implode("/", $vAuxDoc);
                                $cCadena .= $cComprobante.", T.C. ".number_format($cKey,2,',','.')."/";
                              }
                              $mIngTer[$i][100] = substr($cCadena, 0, -1);
                            }
                          } else {
                            //Si la factura es en COP, para los comprobantes que aplique se debe mostrar la tasa pactada
                            //Recorro el array de la Tasa de pactada y el comprobante									
                            $cCadena = "";
                            foreach ($mPccTP[$i] as $cKey => $cValue) {
                              $vAuxDoc = array_unique($cValue);
                              $cComprobante = implode("/", $vAuxDoc);
                              if($cKey != "SIN_TASAPAC"){
                                $cCadena .= $cComprobante.", T.R.M. ".number_format($cKey,2,',','.')."/";
                              }else{
                                $cCadena .= $cComprobante."/";
                              }
                            }						
                            $mIngTer[$i][100] = substr($cCadena, 0, -1);
                          }

                          $cTerceros  = $mIngTer[$i][99];
                          $cTerceros .= ($mIngTer[$i][98] != "") ? "-NIT:".$mIngTer[$i][98] : "";
                          $cTerceros .= (($mIngTer[$i][100]!="" )) ? " [DOCS: ".$mIngTer[$i][100] : "";

                          $nVrlSinIva = 0;
                          if (($mIngTer[$i][16]+0) > 0) { //si el valor del iva es mayor a cero se calcula el valor sin iva
                            $mIngTer[$i][16] = ($vCccDat['cccimpus'] == "SI") ? ($mIngTer[$i][16]/$vCocDat['tcatasax']) : $mIngTer[$i][16];
                            $nVrlSinIva = ($mIngTer[$i][7]+0) - ($mIngTer[$i][16]+0);
                          }
                          if (($nVrlSinIva+0) !=0 || ($mIngTer[$i][16]+0) != 0) {
                            $cTerceros .= (($mIngTer[$i][100]!="" )) ? "" : " [";
                            
                            if($vCccDat['cccimpus'] == "SI"){
                              $cTerceros .=" VLR. SIN IVA $".number_format($nVrlSinIva,2,',','.');
                              $cTerceros .=" IVA $".number_format($mIngTer[$i][16],2,',','.')."]";
                            }else{
                              $cTerceros .=" VLR. SIN IVA $".number_format($nVrlSinIva,0,',','.');
                              $cTerceros .=" IVA $".number_format($mIngTer[$i][16],0,',','.')."]";
                            }
                            //Totales de pagos a Terceros Sin Iva
                            $nVrlTotalSinIva 	+= $nVrlSinIva;
                            $nVrlTotalBaseIva	+= (($mIngTer[$i][16]+0) > 0) ? $nVrlSinIva : 0;
                            $nVrlTotalIva 		+= $mIngTer[$i][16];
                          } else {
                            $cTerceros .= (($mIngTer[$i][100]!="" )) ? "]" : "";
                          }

                          $nValorUnitario = $mIngTer[$i][7]/$mIngTer[$i]['comcanxx'];

                          $pdf->SetFont('verdana','',6);
                          $pdf->setX($posx);
                          $pdf->Row(array($mIngTer[$i]['comcanxx'],
                                          trim($cTerceros),
                                          ($vCccDat['cccimpus'] == "SI") ? number_format($nValorUnitario,2,',','.') : number_format($nValorUnitario,0,',','.'),
                                          ($vCccDat['cccimpus'] == "SI") ? number_format($mIngTer[$i][7],2,',','.') : number_format($mIngTer[$i][7],0,',','.')));

                          if ($pdf->getY() >= 133) {
                            //es el ultimo
                            $nPosPCC = $i+1;
                            $i=count($mIngTer);
                          }
                        }
                      }//for($i=0;$i<count($mIngTer);$i++){

                      if($i == count($mIngTer)) {
                        $nPosPCC = $i;
                      }
                      if ($nPosPCC == count($mIngTer)) {
                        $nBanPCC = 1;
                      }
                    } ## if ($nBanPCC == 0) { ##
                  } else {
                    $nBanPCC = 1;
                  } ## if(count($mIngTer) > 0) { ##
                  ##Fin Imprimo Pagos a Terceros ##

                  ##Imprimo Ingresos Propios##
                  if(count($mCodDat) > 0) { //Valido si la Secuencia de la Grilla de Ip viene mayor a cero para imprimir bloque de INGRESOS PROPIOS
                    if ($nBanIP == 0) {
                      $posy = 152;
                      $posx = 10;

                      ##Imprimo Detalle Ingresos Propios ##
                      $pdf->SetFont('verdanab','',8);
                      $pdf->setXY($posx,$posy);

                      $pdf->SetWidths(array(20,20,107,23,23));
                      $pdf->SetAligns(array("C","C","L","R","R"));

                      $nContarIp = 0;
                      for($k=0;$k<(count($mCodDat));$k++) {
                        if($mCodDat[$k]['comctocx'] == 'IP'){
                          $nContarIp++;
                        }
                      }

                      for($k=$nPosIP;$k<(count($mCodDat));$k++) {

                        if($mCodDat[$k]['comctocx'] == 'IP'){
                          $nSubToIP += $mCodDat[$k]['comvlrxx'];
                          $nValorUnitario = ($mCodDat[$k]['unidadfe'] != "A9" && $mCodDat[$k]['canfexxx'] > 0) ? ($mCodDat[$k]['comvlrxx']/$mCodDat[$k]['canfexxx']) : $mCodDat[$k]['comvlrxx'];

                          //la forma de cobro + cantidades + moneda
                          $cValor = $mCodDat[$k]['formacob']." ";
                          foreach ($mCodDat[$k]['itemcanx'] as $cKey => $cValue) {
                            //Cantidad de decimales
                            $nCanDecCon = (substr_count($cValue,".") > 0) ? 2 : 0;

                            //Formateando valor
                            $cValue = ($mCodDat[$k]['simbolox'][$cKey] == "$") ? number_format(round($cValue,$nCanDecCon),$nCanDecCon,'.',',') : $cValue;

                            //Simbolo que acompaña la condicion especial
                            $cSimbolo   = ($mCodDat[$k]['simbolox'][$cKey] != "") ? $mCodDat[$k]['simbolox'][$cKey]." " : "";

                            $cValor    .= str_replace("_"," ",$cKey).": ".$cSimbolo.$cValue." ";
                          }
                          $cValor .= $mCodDat[$k]['monedaxx']." ";
                          $cValor = " [".trim($cValor)."]";

                          $pdf->SetFont('verdana','',6);
                          $pdf->setX($posx);
                          $pdf->Row(array($mCodDat[$k]['ctoidxxx'],
                                          number_format((($mCodDat[$k]['unidadfe'] == "A9") ? 1 : $mCodDat[$k]['canfexxx']),0,',',''),
                                          trim($mCodDat[$k]['comobsxx'].(($cValor != "") ? $cValor : "")),
                                          ($vCccDat['cccimpus'] == "SI") ? number_format($nValorUnitario,2,',','.') : number_format($nValorUnitario,0,',','.'),
                                          ($vCccDat['cccimpus'] == "SI") ? number_format($mCodDat[$k]['comvlrxx'],2,',','.') : number_format($mCodDat[$k]['comvlrxx'],0,',','.')));		
                        
                          if($pdf->getY() >= 181){
                            //es el ultimo
                            if($nContador != $nContarIp){
                              $nPosIP = $k+1;
                              $k=count($mCodDat);
                            }
                          }
                          $nContador++;
                        }//if($mCodDat[$k]['comctocx'] == 'IP'){
                      } ## for($k=$nPosIP;$k<(count($mCodDat));$k++) { ##

                      if($k == count($mCodDat)) {
                        $nPosIP = $k;
                      }
                      if ($nPosIP == count($mCodDat)) {
                        $nBanIP = 1;
                      }
                    } ##if ($nBanIP == 0) {##
                  } else {
                    $nBanIP = 1;
                  }## if(count($mCodDat) > 0) {##
                  #Fin Imprimo Ingresos Propios##
                } ##while ($nBanPCC == 0 || $nBanPCC == 0) { ##

                ##Total pagos a terceros##
                $pdf->SetFont('verdanab','',8);
                $pdf->setXY(172,141);
                $pdf->Cell(30,4,($vCccDat['cccimpus'] == "SI") ? number_format($nSubTotPcc,2,',','.') : number_format($nSubTotPcc,0,',','.'),0,0,'R');
                ##Fin Total pago a terceros##

                #Total ingresos propios
                $pdf->SetFont('verdanab','',6);
                $pdf->setXY(172,182);
                $pdf->Cell(30,4,($vCccDat['cccimpus'] == "SI") ? number_format($nSubToIP,2,',','.') : number_format($nSubToIP,0,',','.'),0,0,'R');
                #Fin Total ingresos propios
                
                ##Busco valor de IVA ##
                $nIva = 0;
					      $nPorcenIva = 0;
                for ($k=0;$k<count($mCodDat);$k++) {
                  if($mCodDat[$k]['comctocx'] == 'IVAIP'){
                    $nIva += $mCodDat[$k][$cComVlr];
                    // Obtiene el porcentaje del Ingreso Propio
                    if ($nPorcenIva == 0) {
                      $nPorcenIva = number_format($mCodDat[$k]['compivax'], 0);
                    }
                  }
                }
                $pdf->setXY(168,186);
                $pdf->SetFont('verdanab','',8);
	  			      $pdf->Cell(5,4,($nPorcenIva > 0 ? $nPorcenIva : "19") . "%",0,0,'L');
                $pdf->SetFont('verdanab','',6);
                $pdf->Cell(29,4,($vCccDat['cccimpus'] == "SI") ? number_format($nIva,2,',','.') : number_format($nIva,0,',','.'),0,0,'R');
                #Fin Imprimir valor del iva#

                ##Valor Total de la Factura
                $nValorTotal = $nIva+$nSubToIP+$nSubTotPcc;

                ##Busco el valor del Anticipo
                $nTotAnticipo = 0;
                $mComMemPa = explode("|", $vCocDat['commempa']);
                if(count($mComMemPa) > 0) {
                  for( $nCMP = 0; $nCMP < count($mComMemPa); $nCMP++ ) {
                    $mAntAux = explode("~", $mComMemPa[$nCMP]);
                    if($mAntAux[0] == "ANTICIPO") {
                      $nTotAnticipo += $mAntAux[14];
                    }
                  }
                }
                $nTotAnticipo = ($vCccDat['cccimpus'] == "SI") ? (round(($nTotAnticipo/$vCocDat['tcatasax']),3)) : ($nTotAnticipo);
                ##Fin Busco el valor del Anticipo

                ##Calculo valor total de la factura##
                $pdf->SetFont('verdanab','',8);
                $pdf->setXY(172,199);
                $pdf->Cell(30,4,($vCccDat['cccimpus'] == "SI") ? number_format($nValorTotal,2,',','.') : number_format($nValorTotal,0,',','.'),0,0,'R');
                ##Fin calculo valor total de la factura##

                ##Calculo valor Anticipo Recibido##
                $nAnticipoRecibido = ($nTotAnticipo >= $nValorTotal) ? $nValorTotal : $nTotAnticipo;
                $pdf->SetFont('verdanab','',8);
                $pdf->setXY(172,205);
                $pdf->Cell(30,4,($vCccDat['cccimpus'] == "SI") ? number_format($nAnticipoRecibido,2,',','.') : number_format($nAnticipoRecibido,0,',','.'),0,0,'R');
                ##Fin Calculo valor Anticipo Recibido##

                ##Busco Valor a Pagar ##
                $nTotPag = 0;
                if($vCccDat['cccimpus'] == "SI"){
                  $nTotPag = abs($nValorTotal-$nTotAnticipo);
                }else{
                  for ($k=0;$k<count($mCodDat);$k++) {
                    if($mCodDat[$k]['comctocx'] == 'SS' || $mCodDat[$k]['comctocx'] == 'SC'){
                      $nTotPag += $mCodDat[$k]['comvlrxx'];
                    }
                  }
                }
                ##Fin Busco Valor a Pagar ##

                ##Imprimo saldo de la factura ##
                $nSaldoFactura = ($nTotAnticipo >= $nValorTotal) ? 0 : $nTotPag;
                $pdf->setXY(172,211);
                $pdf->Cell(30,4,($vCccDat['cccimpus'] == "SI") ? number_format($nSaldoFactura,2,',','.') : number_format($nSaldoFactura,0,',','.'),0,0,'R');
                ##FIn Imprimo saldo de la factura##

                /*** Seccion Total Factura - Total Anticipo Rcibido - Saldo a Favor del Cliente ***/
                ##Valor total de la factura##
                $pdf->SetFont('verdanab','',7);
                $pdf->setXY(45,183);
                $pdf->Cell(30,4,($vCccDat['cccimpus'] == "SI") ? number_format($nValorTotal,2,',','.') : number_format($nValorTotal,0,',','.'),0,0,'R');
                ##Fin valor total de la factura##

                ##Imprimo total del Anticipo Real Recibido##
                $pdf->SetFont('verdanab','',8);
                $pdf->setXY(45,187);
                $pdf->Cell(30,4,($vCccDat['cccimpus'] == "SI") ? number_format($nTotAnticipo,2,',','.') : number_format($nTotAnticipo,0,',','.'),0,0,'R');
                ##Fin Imprimo total del Anticipo Real Recibido##

                ##Imprimo saldo a favor del cliente##
                $nSaldoFavor = ($nTotAnticipo >= $nValorTotal) ? abs($nTotAnticipo - $nValorTotal) : 0;
                $pdf->SetFont('verdanab','',8);
                $pdf->setXY(45,191);
                $pdf->Cell(30,4,($vCccDat['cccimpus'] == "SI") ? number_format($nSaldoFavor,2,',','.') : number_format($nSaldoFavor,0,',','.'),0,0,'R');
                ##Fin Imprimo saldo a favor del cliente##
                /*** FIN Seccion Total Factura - Total Anticipo Rcibido - Saldo a Favor del Cliente ***/

                ##Imprimo el total de la factura en Letras##
                $nToltalPagar = ($nSaldoFavor > 0) ? $nSaldoFavor : $nSaldoFactura;
                $posy = 203;
                $pdf->SetFont('verdana','',6);
                if($vCccDat['cccimpus'] == "SI"){
                  $alinea = explode("~",f_Words(f_Cifra_Php(abs($nToltalPagar),'DOLAR'),100)); //$nToltalPagar
                }else{
                  $alinea = explode("~",f_Words(f_Cifra_Php(abs($nToltalPagar),'PESO'),100)); //$nToltalPagar
                }

                for ($n=0;$n<count($alinea);$n++) {
                  $pdf->setXY(10,$posy);
                  $pdf->Cell(110,3,$alinea[$n],0,0,'L');
                  $posy+=2.5;
                }
                ##Fin imprimo talta de la facutra en letras##

                ##Imprimo total pagos a terceros e iva
                $pdf->SetFillColor(230, 230, 230);
                $pdf->RoundedRect(10, 138, 120, 5, 0, '1', 'F');
                $pdf->setXY(10,139);
                $pdf->SetFont('verdana','',6);
                if($vCccDat['cccimpus'] == "SI"){
                  $pdf->Cell(113,4,'TOTAL TERCEROS: $'.number_format($nVrlTotalSinIva,2,',','.').' BASE IVA TERCEROS: $'.number_format($nVrlTotalBaseIva,2,',','.').' IVA TERCEROS '.$vSysStr['financiero_porcentaje_iva_compras'].'% : $'.number_format($nVrlTotalIva,2,',','.'),0,0,'L');
                  // $pdf->setXY(10,213);
                  // $pdf->Cell(113,4,'TRM: '.($vCocDat['tcatasax']+0),0,0,'l');
                }else{
                  $pdf->Cell(113,4,'TOTAL TERCEROS: $'.number_format($nVrlTotalSinIva,0,',','.').' BASE IVA TERCEROS: $'.number_format($nVrlTotalBaseIva,0,',','.').' IVA TERCEROS '.$vSysStr['financiero_porcentaje_iva_compras'].'% : $'.number_format($nVrlTotalIva,0,',','.'),0,0,'L');
                }
                ##Fin Imprimo total pagos a terceros e iva

                ##Imprimo el nombre de quien elaboro la faactura##
                $pdf->SetFont('verdana','',7);
                $pdf->setXY(110,222);
                $pdf->Cell(43,4,$vUserNom['USRNOMXX'],0,0,'C');
                ##Fin mprimo el nombre de quien elaboro la faactura#
                $y++;

                if ($vCccDat['cccimpus'] != "SI") {
                  if(($vCocDat['comvlrxx']+0) != ($nValorTotal+0)){
                    $nImprimir = 1;
                  }
                }
              } while ($y<=$nCopias);

              $cNomFile = "Factura_Siaco_$cComId-$cComCod-$cComCsc.pdf";
              if ($pArrayParametros['PROCESOX'] == "TAREA_AUTOMATICA") {
                $cFile    = "{$pArrayParametros['RUTAARCX']}/opencomex/".$vSysStr['system_download_directory']."/$cNomFile";
              } else {
                $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/$cNomFile";
              }
              
              $pdf->Output($cFile);
              unset($pdf);

              if (file_exists($cFile)) {
                chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
                $nInd_vFile = count($vFile);
                $vFile[$nInd_vFile]['ruta']    = $cFile;
                $vFile[$nInd_vFile]['archivo'] = $cNomFile;

                if ($pArrayParametros['ORIGENXX'] == "") {
                  //Codigo Para enviar los reportes del Cliente LG
                  if ($vSysStr['siacosia_importador_lg'] == $vCocDat['teridxxx'] || $vSysStr['siacosia_importador_lg'] == $vCocDat['terid2xx']) {

                    $vFiltros = array();
                    $vFiltros[0]['comidxxx'] = $vCocDat['comidxxx'];
                    $vFiltros[0]['comcodxx'] = $vCocDat['comcodxx'];
                    $vFiltros[0]['comcscxx'] = $vCocDat['comcscxx'];
                    $vFiltros[0]['comcsc2x'] = $vCocDat['comcsc2x'];
                    $vFiltros[0]['regfcrex'] = $vCocDat['regfcrex'];
                    $vFiltros[0]['procesox'] = $pArrayParametros['PROCESOX'];
                    $vFiltros[0]['rutaarcx'] = $pArrayParametros['RUTAARCX'];


                    $oReportesLg = new cFacturacionLG();
                    $vReturnLG   = $oReportesLg->fnReportesFacturacionLG($vFiltros);
                    if ($vReturnLG[0] == "true") {
                      for ($nI = 1; $nI < count($vReturnLG); $nI++) {
                        if($vReturnLG[$nI]['ruta'] !== "") {
                          $nInd_vFile = count($vFile);
                          $vFile[$nInd_vFile]['ruta']    = $vReturnLG[$nI]['ruta'];
                          $vFile[$nInd_vFile]['archivo'] = $vReturnLG[$nI]['archivo'];
                        }
                      }
                    }
                  }
                }

                if ($pArrayParametros['CORREOXX'] == 1 || $pArrayParametros['CORREOXX'] == 2) {
                  if ($nImprimir == 0) {
                    if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP") {
                      $vRetrun = array();
                      $vRetrun = $this->fnEnviarFactura($vFile,$cComId,$cComCod,$cComCsc,$vCocDat['terid2xx'],$vCocDat['CLINOMXX'],$vDirCue,$cDocId,(($cPedOrd != "") ? $cPedOrd : $cPedido));
                      $vRetunCorreos[count($vRetunCorreos)] = $vRetrun;
                    }
                  }
                }
              } else {
                $nSwitch = 1;
                $mReturn[count($mReturn)] = "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.";
              }
            }
          } ## if (strlen($mPrn[$nn]) > 0) { ##
        } ## for ($nn=0;$nn<count($mPrn);$nn++) { ##

        if ($nSwitch == 0) {
          if ($pArrayParametros['CORREOXX'] == 0) {
            $mReturn[1] = $cFile;
          }

          //Impriendo respuesta del envio de correo
          if ($pArrayParametros['CORREOXX'] == 1 || $pArrayParametros['CORREOXX'] == 2) {
            if ($nImprimir == 0) {
              if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP") {
                $cMsj = "";
                for($nC=0; $nC<count($vRetunCorreos);$nC++) {
                  for($nM=0; $nM<count($vRetunCorreos[$nC]); $nM++) {
                    if ($vRetunCorreos[$nC][$nM] != "") {
                      $cMsj .= $vRetunCorreos[$nC][$nM]."\n";
                    }
                  }
                }
                if ($cMsj != ""){
                  $mReturn[count($mReturn)] = "\n".$cMsj;
                }
              }
            } else {
              $nSwitch = 1;
              $mReturn[count($mReturn)] = "Error al generar el PDF de la Factura para envio de correo. Por favor enviar la Factura por la opcion manual en el tracking.";
            }
          }
        }
      }

      if ($nSwitch == 0) {
        $mReturn[0] = "true";
      } else {
        $mReturn[0] = "false";
      }
      return $mReturn;
    }

    //Funcion que retorna los contactos a quien debe enviarseles el correo
    function fnContactos($xTerId2,$xvDirCue) {
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
      //f_Mensaje(__FILE__,__LINE__,$qContactos."~".mysql_num_rows($xContactos));
      $vCorreos = array();
      while($xRC  = mysql_fetch_array($xContactos)) {
        if (in_array($xRC['cliemaxx'],$vCorreos) == false) {
          $vCorreos[count($vCorreos)] = $xRC['cliemaxx'];
        }
      }

      //Trayendo los correos de los directores de cuenta
      for ($nD=0; $nD<count($xvDirCue);$nD++) {
        $qDirCue  = "SELECT USREMAXX ";
        $qDirCue .= "FROM $cAlfa.SIAI0003 ";
        $qDirCue .= "WHERE ";
        $qDirCue .= "$cAlfa.SIAI0003.USRIDXXX =  \"{$xvDirCue[$nD]}\" AND ";
        $qDirCue .= "$cAlfa.SIAI0003.USREMAXX != \"\" AND ";
        $qDirCue .= "$cAlfa.SIAI0003.REGESTXX =  \"ACTIVO\" LIMIT 0,1";
        $xDirCue  = f_MySql("SELECT","",$qDirCue,$xConexion01,"");
        //f_Mensaje(__FILE__,__LINE__,$qDirCue." ~ ".mysql_num_rows($xDirCue));
        if (mysql_num_rows($xDirCue) > 0) {
          $xRDC = mysql_fetch_array($xDirCue);
          if (in_array($xRDC['USREMAXX'],$vCorreos) == false) {
            $vCorreos[count($vCorreos)] = $xRDC['USREMAXX'];
          }
        }
      }

      return $vCorreos;
    }

    //Funcion para el envio de la factura por correo electronico, recibe como parametros
    //@param $xFile    -> Ruta del archivo de la factura
    //@param $xComCsc  -> Consecutivo de la factura
    //@param $xTerId2  -> Facturar a, a quien se le facturo
    //@param $xvDirCue -> Vector con los directores de cuenta de los DO facturados
    function fnEnviarFactura($xFile,$xComId,$xComCod,$xComCsc,$xTerId2,$xTerNom2,$xvDirCue,$xTramite,$xPedido) {
      global $cAlfa; global $xConexion01; global $vSysStr;

      $nSwitch = 0; $vMsj = array();

      //Enviar de Factura por correo
      $vReturnCorreos = $this->fnContactos($xTerId2,$xvDirCue);

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
        $cDominio = $vSysStr['siacosia_phpmailer_usuario'];
        $cFrom = "Facturacion SIACO <$cDominio>";

        $cSubject = "Factura Siaco No. $xComId-$xComCod-$xComCsc DO: $xTramite".(($xPedido != "") ? " PEDIDO: $xPedido" : "");

        $cMessage  = "Se&ntilde;ores,<br>";
        $cMessage .= "<b>$xTerNom2</b><br><br>";
        $cMessage .= "Estamos Enviando Copia Virtual de su Factura original No.$xComId-$xComCod-$xComCsc en Formato PDF. ";
        $cMessage .= "Este documento es de caracter Informativo, su factura Original llegar&aacute; Adjunta con sus Soportes Proximamente.<br><br>";
        $cMessage .= "Si Tiene alguna Observaci&oacute;n Frente a Esta Factura, Por favor comunicarse directamente con su director de cuenta o con el departamento Comercial. No Contestar este correo.";
        $cMessage .= "<br>";

        if ($vSysStr['siacosia_phpmailer_activar'] == "SI") {
          // Se envia el correo con la libreria phpmailer
          $cMailerUsr   = $vSysStr['siacosia_phpmailer_usuario'];
          $cMailerPass  = $vSysStr['siacosia_phpmailer_password'];
          $cMailerSmtp  = $vSysStr['siacosia_phpmailer_smtp'];
          $cMailerPto   = $vSysStr['siacosia_phpmailer_puerto'];

          $mail = new PHPMailer();
          $mail->Mailer = "smtp";
          $mail->Port = $cMailerPto;
          $mail->SMTPSecure = "tls";
          $mail->Host = $cMailerSmtp;
          $mail->SMTPAuth = true;
          $mail->Username = $cMailerUsr;
          $mail->Password = $cMailerPass;
          $mail->From = $cDominio;
          $mail->FromName = $cFrom;

          $mail->Timeout = 30;
          $mail->Subject = $cSubject;
          $mail->ContentType = "text/html";
          $mail->Body = $cMessage;

          for ($nA=0; $nA<count($xFile); $nA++) {
            if(filesize($xFile[$nA]['ruta']) <= (1024*1024)){
              $mail->AddAttachment($xFile[$nA]['ruta'],$xFile[$nA]['archivo']);
            } else {
              $nSwitch = 1;
              $vMsj[count($vMsj)] = "Para la Factura [$xComId-$xComCod-$xComCsc] Archivo Adjunto Supera Limite de Tamano [1024]. Favor Comunicar este Error a openTecnologia S.A.\n";
            }
          }

          // send
          if ($nSwitch == 0) {
            $cCorreos = "";
            for ($nC=0; $nC<count($vCorreos); $nC++) {
              if ($vCorreos[$nC] != "") {
                $mail->AddAddress($vCorreos[$nC]);
                //Enviando correos a los contactos y director(es) de Cuenta del o los Do
                if ($mail->Send()) {
                  //Enviado con Exito
                } else {
                  $nSwitch = 1;
                  $vMsj[count($vMsj)] = "Para la Factura [$xComId-$xComCod-$xComCsc] Error al Enviar Correo al destinatario [{$vCorreos[$nC]}]. Favor Comunicar este Error a openTecnologia S.A.\n".$mail->ErrorInfo;
                  //echo $mail->ErrorInfo;
                }

                $mail->ClearAddresses();
                $cCorreos .= "{$vCorreos[$nC]}, ";
              }
            }
            $cCorreos = substr($cCorreos, 0, strlen($cCorreos)-2);
          }
        } else {
          // Se envia el correo por la funcion mail de php
          //Modelo actual
          # segim OS Windows o Mac o Linux
          switch (strtoupper(substr(PHP_OS,0,3))) {
            case "MAC":
              $cFin = "\r" ;
            break;
            case "WIN":
              $cFin = "\r\n" ;
            break;
            default:
              $cFin = "\n" ;
            break;
          }
        
          $cHeaders = "From: $cFrom";
          
          // boundary 
          $semi_rand = md5(time()); 
          $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
            
          // headers for attachment 
          $cHeaders .= "\nMIME-Version: 1.0$cFin" . "Content-Type: multipart/mixed;$cFin" . " boundary=\"{$mime_boundary}\"$cFin"; 
            
          // multipart boundary 
          $cMessage = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"iso-8859-1\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $cMessage . "\n\n"; 
          $cMessage .= "--{$mime_boundary}\n";
          
          // preparo el adjunto
          for ($nA=0; $nA<count($xFile); $nA++) {
            if(filesize($xFile[$nA]['ruta']) <= (1024*1024)){
              $file = fopen($xFile[$nA]['ruta'],"rb");
              $data = fread($file,filesize($xFile[$nA]['ruta']));
              fclose($file);
              $data = chunk_split(base64_encode($data));
              $name = $xFile[$nA]['archivo'];
              $cMessage .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$name\"\n" . 
              "Content-Disposition: attachment;\n" . " filename=\"$name\"\n" . 
              "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
              $cMessage .= "--{$mime_boundary}\n";
            } else {
              $nSwitch = 1;
              $vMsj[count($vMsj)] = "Para la Factura [$xComId-$xComCod-$xComCsc] Archivo Adjunto Supera Limite de Tamano [1024]. Favor Comunicar este Error a openTecnologia S.A.\n";
            }
          }

          // send
          if ($nSwitch == 0) {
            $cCorreos = "";
            for ($nC=0; $nC<count($vCorreos); $nC++) {
              if ($vCorreos[$nC] != "") {
                //Enviando correos a los contactos y director(es) de Cuenta del o los Do
                $xMail = mail($vCorreos[$nC], $cSubject, $cMessage, $cHeaders);
                if(!$xMail){
                  $nSwitch = 1;
                  $vMsj[count($vMsj)] = "Para la Factura [$xComId-$xComCod-$xComCsc] Error al Enviar Correo al destinatario [{$vCorreos[$nC]}]. Favor Comunicar este Error a openTecnologia S.A.\n";
                }
                $cCorreos .= "{$vCorreos[$nC]}, ";
              }
            }
            $cCorreos = substr($cCorreos, 0, strlen($cCorreos)-2);
          }
        }

        if($nSwitch == 0) {
          $nSwitch = 2;
          $vMsj[count($vMsj)] = "Se Envio la Factura [$xComId-$xComCod-$xComCsc] con Exito a los Siguientes Correos:\n$cCorreos.\n";
        }
      }

      if ($nSwitch == 1 || $nSwitch == 2) {
        return $vMsj;
      }
    }
  }

  class PDFSIACO extends FPDF {
    function Header() {
      global $cPlesk_Skin_Directory;  global $vSysStr;
      global $nPag;       global $vAgeDat; 	  global $vCocDat;  global $vResDat;   global $cDocId;
      global $vCiuDat;    global $vDceDat; 		global $vCccDat; 	global $vConDat; 	 global $vCcoDat;
      global $cDeposito;  global $mIngTer; 	  global $cPaiOri; 	global $cFormaPag; global $vMedPag;
      global $cNomDirCue; global $cSucId; 	  global $cPedido;  global $cPedOrd;
      global $cLugIngDes; global $cPaiOriDes;
      global $cUsrId;     global $cRoot;      global $kModo;

      $cIca = "";
      switch ($cSucId) {
        case "CTG": $cIca = "8"; break;
        case "CUC": $cIca = "11"; break;
        case "BUN": $cIca = "10"; break;
        case "IPI": $cIca = "6"; break;
        case "SMR": $cIca = "7"; break;
        case "BAQ": $cIca = "6.9"; break;
        case "CLO": $cIca = "11"; break;
        case "BOG": $cIca = "9,66"; break;
      }

      ## Valido si el comprobante es F-005 en ciudad Imprimo RIONEGRO ##
      $cCiuFact = $cLugIngDes;
      $cIdCompr = $vCocDat['comidxxx']."-".$vCocDat['comcodxx'];
      if ($cIdCompr == "F-005") {
        $cCiuFact = "RIONEGRO";
      }

      $posy	= 8;##PRIMERA POSICION DE Y##
      $posx	= 10;

      if ($vCocDat['regestxx'] == "INACTIVO") {
        $this->Image($cRoot.'/'.$cPlesk_Skin_Directory.'/facturaanulada.jpg',10,50,180,180);
      }

      ## Marca de agua copia no valida
      if ($kModo == "VERFACTURA" || $vCocDat['regestxx'] == "PROVISIONAL") {
        $this->Image($cRoot.'/'.$cPlesk_Skin_Directory.'/copianovalida.jpg', 20, 50, 180, 180);
      }

      ##Logo
      $this->Image($cRoot.'/'.$cPlesk_Skin_Directory.'/logosiaco.jpg',20,28,50);
      
      ## Agencia - Nit ##
      $this->SetFont('verdanab','',10);
      $this->setXY($posx+120,$posy-2);
      $this->Cell(40,4,"AGENCIA DE ADUANAS SIACO SAS NIVEL 1",0,0,'C');
      $this->Ln(4);
      $this->setX($posx+120);
      $this->Cell(40,4,"NIT: ". number_format($vSysStr['financiero_nit_agencia_aduanas'], 0, '', '.'). "-" . f_Digito_Verificacion($vSysStr['financiero_nit_agencia_aduanas']),0,0,'C');
      $this->Ln(4);
      $this->setX($posx+120);
      $this->Cell(40,4,"Cod.: 0413",0,0,'C');

      #Texto FIJO#
      $this->Ln(5);
      $this->SetFont('verdana','',6);
      $this->setX($posx+120);
      $this->Cell(40,4,"SOMOS IVA REGIMEN COMUN ICA $cIca X 1000 ACT 5229",0,0,'C');
      $this->Ln(3);
      $this->setX($posx+120);
      $this->Cell(40,4,"SOMOS AGENTES RETENEDORES DE IVA",0,0,'C');
      $this->Ln(3.5);
      $this->SetFont('verdana','',6);
      $this->setX($posx+100);
      $this->MultiCell(80,3,utf8_decode("SOMOS AUTORRETENEDORES DEL IMPUESTO SOBRE LA RENTA SEGÚN RESOLUCIÓN No 9016 DEL 9 DE DICIEMBRE DE 2020."),0,'C');
      $this->SetFont('verdanab','',6);
      $this->setX($posx+120);
      $this->Cell(40,3,"NO SOMOS GRANDES CONTRIBUYENTES",0,0,'C');
      #FIN Texto FIJO#

      ##Linea para imprimir la resolucion de la factura##
      $this->SetFont('verdana','',6);
      $cResolucion  = date("Y-m-d")."  ".date("H:i:s")."  FACTURACION POR COMPUTADOR RESOLUCION {$vResDat['resclaxx']} DIAN No {$vResDat['residxxx']} DE ";
      $cResolucion .= "{$vResDat['resfdexx']} PREFIJO: {$vResDat['resprexx']} DEL {$vResDat['resdesxx']} AL {$vResDat['reshasxx']}";

      $nPosicionY = 240;
      if ($vResDat['resvigme'] != "") {
        $cResolucion.= " VIGENCIA {$vResDat['resvigme']} ";
        if ($vResDat['resvigme'] == "1") {
          $cResolucion.= "MES";
          $nPosicionY = 250;
        } else {
          $cResolucion.= "MESES";
          $nPosicionY = 252;
        }
      }
      $this->RotatedText(6,$nPosicionY,$cResolucion,90);//14,220
      ##Fin impresion de resolucion##

      ## Numero de la factura
      $posy += 27;
      $this->SetFont('verdanab','',8);
      $this->setXY($posx+100, $posy);
      $this->Cell(23,10, utf8_decode("Factura Electrónica de Venta No. ") .$vResDat['resprexx']."-".$vCocDat['comcscxx'],0,0,'L');
      $this->RoundedRect($posx+90, $posy+1, 103, 7, 3, '12');

      ## Fecha de la factura
      $posy += 10;
      $this->setXY($posx+92, $posy);
      $this->SetFont('verdana','',7);
      $this->Cell(25,4, utf8_decode("Ciudad y Fecha de generación"),0,0,'L');
      $this->Ln(4);
      $this->setX($posx+92);
      $this->SetFont('verdanab','',7);
      $this->Cell(25,4,utf8_decode($cCiuFact),0,0,'L');
      $this->Cell(23,4,$vCocDat['comfecxx'],0,0,'R');
      $this->RoundedRect($posx+90, $posy, 50, 8, 3, '1');

      ## DO de la Factura
      $this->setXY($posx+145, $posy);
      $this->SetFont('verdana','',7);
      $this->Cell(25,4, utf8_decode("D.O No.:"),0,0,'L');
      $this->Ln(4);
      $this->setX($posx+145);
      $this->SetFont('verdanab','',7);
      $this->Cell(35,4,$cDocId,0,0,'R');
      $this->RoundedRect($posx+143, $posy, 50, 8, 3, '2');

      ## Fecha de vencimiento de la factura
      $posy += 10;
      $this->SetFillColor(230, 230, 230);
      $this->RoundedRect($posx+90, $posy, 50, 8, 3, '1', 'F');
      $this->setXY($posx+92, $posy);
      $this->SetFont('verdana','',7);
      $this->Cell(25,4, utf8_decode("Fecha vencimiento:"),0,0,'R');
      $this->Ln(4);
      $this->setX($posx+92);
      $this->SetFont('verdanab','',7);
      $this->Cell(48,4,$vCocDat['comfecve'],0,0,'R');
      
      //Pedido del DO traido de la SIAI0200
      $this->setXY($posx+145,$posy);
      $this->SetFont('verdana','',7);
      $this->Cell(25,4, utf8_decode("Pedido:"),0,0,'L');
      $this->Ln(4);
      $this->setX($posx+145);
      $this->SetFont('verdanab','',7);
      // Valida si el facturar a es SODIMAC para pintar el pedido con valor '1'
      if ($vCocDat['terid2xx'] == '800242106') {
        $this->Cell(45,4,"1",0,0,'C');
      } elseif ($vCliDat['CLIIDXXX'] == '900736182') {
        // Valida si el facturar a es APPLE para pintar el valor parametrizado en la variable del sistema
        $this->Cell(30,4, $vSysStr['siacosia_facturacion_referencia_apple'],0,0,'L');
      } else {
        $this->Cell(30,4, substr(($cPedOrd != "") ? $cPedOrd : $cPedido,0,26),0,0,'L');
      }
      $this->RoundedRect($posx+143, $posy, 50, 8, 3, '2');

      ##Datos del Cliente##
      $posy += 11;
      $posx	= 10;
      //Nombre del cliente
      $this->SetFont('verdanab','',7);
      $this->setXY($posx, $posy);
      $this->Cell(23,4,"Cliente: ",0,0,'L');
      $this->setX($posx+23);
      $this->SetFont('verdana','',7);
      $this->Cell(100,4, substr($vCocDat['CLINOMXX'], 0, 53),0,0,'L');
      //Nit
      $this->setX($posx+112);
      $this->SetFont('verdanab','',7);
      $this->Cell(20,4," Nit: ".$vCocDat['terid2xx']."-".f_Digito_Verificacion($vCocDat['terid2xx']),0,0,'L');

      $this->Ln(4);
      //Ciudad
      $this->setX($posx);
      $this->SetFont('verdanab','',7);
      $this->Cell(23,4,"Ciudad: ",0,0,'L');
      $this->setX($posx+23);
      $this->SetFont('verdana','',7);
      $this->Cell(20,4,$vCiuDat['CIUDESXX'],0,0,'L');

      $this->Ln(4);
      //Direccion
      $this->setX($posx);
      $this->SetFont('verdanab','',7);
      $this->Cell(23,4,utf8_decode("Dirección: "),0,0,'L');
      $this->setX($posx+23);
      $this->SetFont('verdana','',7); 
      $this->Cell(20,4,$vCocDat['CLIDIRXX'],0,0,'L');
      
      $this->Ln(4);
      //Telefono
      $this->setX($posx);
      $this->SetFont('verdanab','',7);
      $this->Cell(23,4,"Telefono: ",0,0,'L');
      $this->setX($posx+23);
      $this->SetFont('verdana','',7); 
      $this->Cell(20,4,$vCocDat['CLITELXX'],0,0,'L');

      $this->Ln(4);
      //Observaciones
      $this->setX($posx);
      $this->SetFont('verdanab','',7);
      $this->Cell(23,4,"Observaciones: ",0,0,'L');
      $this->Ln(0.5);
      $this->setX($posx+23);
      $this->SetFont('verdana','',7);
      $this->MultiCell(117,3, $vCocDat['comobsxx'],0,'L');
      
      $this->Ln(0.5);
      //Forma de pago
      $this->setX($posx);
      $this->SetFont('verdanab','',7);
      $this->Cell(23,4,"Forma de Pago: ",0,0,'L');
      $this->setX($posx+23);
      $this->SetFont('verdana','',7); 
      $this->Cell(20,4,$cFormaPag,0,0,'L');

      $this->Ln(4);
      //Medio de pago
      $this->setX($posx);
      $this->SetFont('verdanab','',7);
      $this->Cell(23,4,"Medio de Pago: ",0,0,'L');
      $this->setX($posx+23);
      $this->SetFont('verdana','',7); 
      $this->Cell(20,4,utf8_decode($vMedPag['mpadesxx']),0,0,'L');
      //Recuadro
      $this->RoundedRect($posx, $posy-1, 140, 35, 3, '1');

      ## Datos adicionales ## 
      //Origen
      $this->setXY($posx+143, $posy);
      $this->SetFont('verdana','',6);
      $this->Cell(23,4,"ORIGEN: ",0,0,'L');
      $this->setX($posx+162);
      $this->SetFont('verdanab','',6);
      $this->Cell(23,4, $cPaiOriDes,0,0,'L');

      $this->Ln(5.5);
      //Servicio:
      $this->setX($posx+143);
      $this->SetFont('verdana','',6);
      $this->Cell(23,4,"SERVICIO: ",0,0,'L');
      $this->setX($posx+162);
      $this->SetFont('verdanab','',6);
      $this->Cell(23,4, $vDceDat['doctipxx'],0,0,'L');

      $this->Ln(5.5);
      //Deposito:
      $this->setX($posx+143);
      $this->SetFont('verdana','',6);
      $this->Cell(23,4, utf8_decode("DEPÓSITO: "),0,0,'L');
      //Se busca por el codigo del Pedido Traido de la SIAI0200 para buscar la descripcion del deposito en la SIAI0110
      $this->setX($posx+162);
      $this->SetFont('verdanab','',6);
      $this->Cell(23,4, substr($cDeposito,0,18),0,0,'L');

      $this->Ln(5.5);
      //Contacto:
      $this->setX($posx+143);
      $this->SetFont('verdana','',6);
      $this->Cell(23,4,"CONTACTO: ",0,0,'L');
      $this->setX($posx+162);
      $this->SetFont('verdanab','',6);
      $this->Cell(23,4, substr($vConDat['CLINOMXX'],0,18),0,0,'L');

      $this->Ln(5.5);
      //Director de Cuenta:
      $this->setX($posx+143);
      $this->SetFont('verdana','',6);
      $this->Cell(23,4,"DIR. CUENTA: ",0,0,'L');
      $this->setX($posx+162);
      $this->SetFont('verdanab','',6);
      $this->Cell(23,4, substr($cNomDirCue,0,18),0,0,'L');
      //Recuadro
      $this->RoundedRect($posx+143, $posy-1, 50, 35, 3, '2');

      $posy += 35;
      $this->setXY($posx, $posy);
      $this->SetFont('verdanab','',8);
      $this->Cell(23,4,"REINTREGROS DE CAPITAL",0,0,'L');
      $this->Ln(5);
      
      ##Cabecera de los items PCC
      $this->setX($posx);
      $this->SetFont('verdanab','',7);
      $this->Cell(20,4,"Cantidad",0,0,'C');
      $this->Cell(117,4,utf8_decode("Descripción"),0,0,'C');
      $this->Cell(28,4,"Valor Unitario",0,0,'C');
      $this->Cell(28,4,"Valor Total",0,0,'C');
      
      ##Recuadro de los items PCC
      $posy += 5;
      $this->Rect($posx, $posy, 193, 31);
      $this->Line($posx+20, $posy, $posx+20, $posy+31);
      $this->Line($posx+137, $posy, $posx+137, $posy+31);
      $this->Line($posx+165, $posy, $posx+165, $posy+31);

      $posy += 31;
      $this->setXY($posx+120,$posy+4);
      $this->SetFont('verdanab','',8);
      $this->Cell(23,4,"Valor Neto: ",0,0,'L');
      ##Flecha
      $this->Image($cRoot.'/'.$cPlesk_Skin_Directory.'/arrow-right_grey01.jpg',$posx+145,$posy+5,14);
      //Recuadro valor Neto
      $this->RoundedRect($posx+163, $posy+3, 30, 5, 2, '2', 'F');
      
      $this->Ln(3);
      $this->setX($posx);
      $this->SetFont('verdanab','',8);
      $this->Cell(23,4,"INGRESOS PROPIOS",0,0,'L');
      
      $this->Ln(4);
      ##Cabecera de los items IP
      $this->setX($posx);
      $this->SetFont('verdanab','',7);
      $this->Cell(20,4,utf8_decode("Código"),0,0,'C');
      $this->Cell(20,4,"Cantidad",0,0,'C');
      $this->Cell(107,4,utf8_decode("Descripción"),0,0,'C');
      $this->Cell(23,4,"Valor Unitario",0,0,'C');
      $this->Cell(23,4,"Valor Total",0,0,'C');

      ##Recuadro de los items IP
      $posy += 11;
      $this->Rect($posx, $posy, 193, 34);
      $this->Line($posx+20, $posy, $posx+20, $posy+34);
      $this->Line($posx+40, $posy, $posx+40, $posy+34);
      $this->Line($posx+147, $posy, $posx+147, $posy+34);
      $this->Line($posx+170, $posy, $posx+170, $posy+34);
      
      $posy += 34;
      $this->setXY($posx+133, $posy);
      $this->SetFont('verdanab','',7);
      $this->Cell(23,4,"Total Ingresos Propios ",0,0,'L');
      $this->Ln(4);
      $this->setX($posx+126);
      $this->SetFont('verdanab','',8);
      $this->Cell(33,4,"Iva",0,0,'R');

      ##Recuadro de los Totales de IP
      $this->Rect($posx, $posy, 193, 15);

      ##Recuadro Gris de Total factura - Anticipo - Saldo a Favor##
      $this->RoundedRect($posx+0.5, 183, 65, 13, 0, '1', 'F');

      ##Recuadro Gris Valor en letras##
      $this->RoundedRect($posx, 198, 97, 15, 3, '1', 'F');

      ##Recuadro Gris Valor en letras##
      $this->RoundedRect($posx+163, 198, 30, 5, 2, '2', 'F');

    }//Function Header

    function Footer() {
      global $cNomCopia; global $vCocDat; global $cPlesk_Skin_Directory; global $vSysStr; global $pArrayParametros;
      global $cUsrId;    global $cRoot;   global $cRootQR; global $cProceso;

      $posx = 5;
      $posy = 183;

      $this->SetFillColor(230, 230, 230);
      $this->setXY($posx+5, $posy);
      $this->SetFont('verdanab','',7);
      $this->Cell(23,4,"TOTAL FACTURA:",0,0,'L');
      $this->Ln(4);
      $this->setX($posx+5);
      $this->Cell(33,4,"TOTAL ANTICIPO RECIBIDO:",0,0,'L');
      $this->Ln(4);
      $this->setX($posx+5);
      $this->Cell(33,4,"SALDO A FAVOR DEL CLIENTE:",0,0,'L');

      $posy += 15;
      $this->setXY($posx+5, $posy);
      $this->SetFont('verdanab','',8);
      $this->Cell(23,5,"SON:",0,0,'L');

      $this->setXY($posx+130, $posy);
      $this->SetFont('verdanab','',10);
      $this->Cell(33,5,"Total Factura:",0,0,'L');

      $this->Ln(6);
      $this->RoundedRect($posx+105, $posy+6, 60, 5, 0, '', 'F');
      $this->setX($posx+110);
      $this->SetFont('verdanab','',10);
      $this->Cell(33,5,"Anticipo Recibido:",0,0,'L');
      //Flecha
      $this->Image($cRoot.'/'.$cPlesk_Skin_Directory.'/arrow-right_grey02.jpg',$posx+150,$posy+7.5,14);
      //Recuadro valor anticipo
      $this->RoundedRect($posx+168, $posy+6, 30, 5, 2, '2');

      $this->Ln(6);
      $this->RoundedRect($posx+105, $posy+12, 60, 5, 0, '', 'F');
      $this->setX($posx+110);
      $this->SetFont('verdanab','',10);
      $this->Cell(38,5,"SALDO",0,0,'R');
      ##Flecha
      $this->Image($cRoot.'/'.$cPlesk_Skin_Directory.'/arrow-right_grey02.jpg',$posx+150,$posy+13.5,14);
      //Recuadro valor saldo
      $this->RoundedRect($posx+168, $posy+12, 30, 5, 2, '2');

      $posy += 20;
      $nAltoCell = 2.5;
      $this->setXY($posx+5, $posy);
      $this->SetFont('verdana','',5);
      $this->Cell(23,$nAltoCell,utf8_decode("1. Esta factura de venta es un Título Valor y se expide de acuerdo al Artículo 774 del Código de Comercio,"),0,0,'L');
      $this->Ln($nAltoCell);
      $this->setX($posx+5);
      $this->Cell(23,$nAltoCell,utf8_decode("una vez vencido este documento generará interés de mora a la tasa máxima autorizada."),0,0,'L');
      $this->Ln($nAltoCell);
      $this->setX($posx+5);
      $this->Cell(23,$nAltoCell,utf8_decode("2. Pasados 5 días después de recibida esta factura se entenderá aceptada y no habrá derecho a reclamo"),0,0,'L');
      $this->Ln($nAltoCell);
      $this->setX($posx+5);
      $this->Cell(23,$nAltoCell,utf8_decode("posterior ni a devolución de la misma."),0,0,'L');
      $this->Ln($nAltoCell);
      $this->setX($posx+5);
      $this->Cell(23,$nAltoCell,utf8_decode("3. Para todos los efectos legales , el domicilio contractual de las partes, será la ciudad de Bogotá D.C."),0,0,'L');
      $this->Ln($nAltoCell);
      $this->setX($posx+5);
      $this->Cell(23,$nAltoCell,utf8_decode("4. Números de cuenta para que realice transferencia bancaria: Cuenta Corriente Banco de Occidente No."),0,0,'L');
      $this->Ln($nAltoCell);
      $this->setX($posx+5);
      $this->Cell(23,$nAltoCell,utf8_decode("231-044322, Cuenta Corriente Bancolombia No. 03114844657, Cuenta Corriente Banco Itaú No. 011220399."),0,0,'L');
      //Recuadro notas finales
      $this->RoundedRect($posx+5, $posy-1, 98, 20, 3, '4');

      ##Elaborado
      $this->setXY($posx+105, $posy);
      $this->SetFont('verdanab','',6);
      $this->Cell(43,4,"ELABORADO",0,0,'C');

      $this->setXY($posx+107, $posy+10);
      $this->Cell(40,4,"FIRMA Y SELLO SIACIO S.A.S.",0,0,'C');
      //Recuadro notas finales
      $this->Line($posx+105, $posy+10, $posx+150, $posy+10);
      $this->Rect($posx+105, $posy-1, 45, 20);

      ##Firma
      $this->setXY($posx+154, $posy);
      $this->SetFont('verdanab','',6);
      $this->Cell(40,4,"FECHA RECIBIDO",0,0,'L');

      $this->setXY($posx+154, $posy+8);
      $this->SetFont('verdanab', '', 10);
      $this->SetTextColor(219, 219, 219);
      $this->Cell(40, 4, utf8_decode("ACEPTADA"),0,0,'C');
      $this->SetTextColor(0, 0, 0);

      $this->SetFont('verdanab', '', 6);
      $this->setXY($posx+154, $posy+15);
      $this->Cell(40,4,"NOMBRE. C.C / NIT",0,0,'C');
      //Recuadro notas finales
      $this->Line($posx+152, $posy+15, $posx+198, $posy+15);
      $this->RoundedRect($posx+152, $posy-1, 46, 20, 3, '3');

      ### Firma electronica - CUFE - QR ###
      $posy += 20;
      $this->setXY($posx+5,$posy);
      $this->SetFont('verdanab','',5);
      $this->Cell(30,3, utf8_decode("FECHA Y HORA VALIDACIÓN DIAN: ").substr($vCocDat['compcevd'],0,16),0,0,'L');

      $this->setXY($posx+118,$posy);
      $this->SetFont('verdanab','',7);
      $this->Cell(14,5,"CUFE: ",0,0,'L');
      $this->setX($posx+128);
      $this->SetFont('verdana','',6);
      $this->MultiCell(70,2.5,$vCocDat['compcecu'],0,'L');

      if ($vCocDat['compceqr'] != "") {
        if ($cProceso == "TAREA_AUTOMATICA") {
          $cFileQR = "$cRootQR/opencomex/".$vSysStr['system_download_directory']."/QR_".$cUsrId."_".date("YmdHis").".png";
        } else {
          $cFileQR = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/QR_".$_COOKIE['kUsrId']."_".date("YmdHis").".png";
        }        
        QRcode::png($vCocDat['compceqr'], $cFileQR, "H", 10, 1);
        $this->Image($cFileQR,$posx+160,$posy+5,20,20);
      }

      $this->setXY($posx+5,$posy+3);
      $this->SetFont('verdanab','',7);
      $this->Cell(14,5,utf8_decode("Representación Impresa de la Factura Electrónica: "),0,0,'L');
      $this->Ln(4);
      $this->setX($posx+5);
      $this->Cell(14,5,utf8_decode("Firma Electrónica "),0,0,'L');
      $this->Ln(4);
      $this->SetFont('verdana','',5);
      $this->setX($posx+5);
      $this->MultiCell(150,2, $vCocDat['compcesv'],0,'L');

      //Direccion de Ciudades
      $posy = 262;
      $this->setXY($posx+4, $posy);
      $this->SetFont('verdanab','',6);
      $this->Cell(23,4,"BOGOTA D.C",0,0,'C');
      $this->Ln(4);
      $this->setX($posx-5);
      $this->SetFont('verdana','',6);
      $this->MultiCell(40,2.5,"Ak 97 No. 24C-80\n PBX: 425 2600 / 50",0,'C');

      $this->setXY($posx+34, $posy);
      $this->SetFont('verdanab','',6);
      $this->Cell(23,4,"CARTAGENA",0,0,'C');
      $this->Ln(4);
      $this->setX($posx+25);
      $this->SetFont('verdana','',5);
      $this->MultiCell(40,2.5,utf8_decode("Manga Callejón Porto\n Cra. 18 N0. 25-134\n Tel.: (095) 660 6422 / 5038"),0,'C');

      $this->setXY($posx+68, $posy);
      $this->SetFont('verdanab','',6);
      $this->Cell(23,4,"BUENAVENTURA",0,0,'C');
      $this->Ln(4);
      $this->setX($posx+60);
      $this->SetFont('verdana','',5);
      $this->MultiCell(40,2.5,"Cra. 3A No. 2-13\n Altos Davivienda\n Tel.: (092) 240 0269 / 70",0,'C');

      $this->setXY($posx+103, $posy);
      $this->SetFont('verdanab','',6);
      $this->Cell(23,4,"RIONEGRO",0,0,'C');
      $this->Ln(4);
      $this->setX($posx+95);
      $this->SetFont('verdana','',5);
      $this->MultiCell(40,2.5,"Glorieta del Aeropuerto\n Jose Maria Cordova\n Centro ciudad Karga Fase 1\n Rionegro, Antioquia Of. 302",0,'C');

      $this->setXY($posx+135, $posy);
      $this->SetFont('verdanab','',6);
      $this->Cell(23,4,"IPIALES",0,0,'C');
      $this->Ln(4);
      $this->setX($posx+128);
      $this->SetFont('verdana','',5);
      $this->MultiCell(40,2.5,utf8_decode("Centro Comercial Rumichaca\n Local 23\n Teléfono: 3156001285"),0,'C');

      $this->setXY($posx+172, $posy);
      $this->SetFont('verdanab','',6);
      $this->Cell(23,4,"CALI",0,0,'C');
      $this->Ln(4);
      $this->setX($posx+163);
      $this->SetFont('verdana','',5);
      $this->MultiCell(40,2.5,utf8_decode("Calle 64 Norte No 05B-26\n Oficina 414 Centro Empresa.\n Teléfono: 6642883"),0,'C');

    }//function Footer() {

    //Agrupar Pagina
    var $NewPageGroup;   // variable indicating whether a new group was requested
    var $PageGroups;     // variable containing the number of pages of the groups
    var $CurrPageGroup;  // variable containing the alias of the current page group

    // create a new page group; call this before calling AddPage()
    function StartPageGroup() {
      $this->NewPageGroup=true;
    }

    // current page in the group
    function GroupPageNo() {
      return $this->PageGroups[$this->CurrPageGroup];
    }

    // alias of the current page group -- will be replaced by the total number of pages in this group
    function PageGroupAlias(){
      return $this->CurrPageGroup;
    }

    function _beginpage($orientation) {
      parent::_beginpage($orientation);
      if($this->NewPageGroup) {
        // start a new group
        $n = sizeof($this->PageGroups)+1;
        $alias = "{nb$n}";
        $this->PageGroups[$alias] = 1;
        $this->CurrPageGroup = $alias;
        $this->NewPageGroup=false;
      } elseif($this->CurrPageGroup)
      $this->PageGroups[$this->CurrPageGroup]++;
    }

    function _putpages() {
      $nb = $this->page;
      if (!empty($this->PageGroups)) {
        // do page number replacement
        foreach ($this->PageGroups as $k => $v) {
          for ($n = 1; $n <= $nb; $n++) {
            $this->pages[$n]=str_replace($k, $v, $this->pages[$n]);
          }
        }
      }
      parent::_putpages();
    }
    //Fin Agrupar Pagina

    function RotatedText($x,$y,$txt,$angle){
      //Text rotated around its origin
      $this->Rotate($angle,$x,$y);
      $this->Text($x,$y,$txt);
      $this->Rotate(0);
    }

    function RotatedImage($file,$x,$y,$w,$h,$angle){
      //Image rotated around its upper-left corner
      $this->Rotate($angle,$x,$y);
      $this->Image($file,$x,$y,$w,$h);
      $this->Rotate(0);
    }

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

    function _endpage(){
      if($this->angle!=0) {
        $this->angle=0;
        $this->_out('Q');
      }
      parent::_endpage();
    }

    function SetWidths($w) {
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
      $h=3*$nb;
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
        $this->MultiCell($w,3,$data[$i],0,$a);
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
    
    function RoundedRect($x, $y, $w, $h, $r, $corners = '1234', $style = '') {
      $k = $this->k;
      $hp = $this->h;
      if($style=='F')
          $op='f';
      elseif($style=='FD' || $style=='DF')
          $op='B';
      else
          $op='S';
      $MyArc = 4/3 * (sqrt(2) - 1);
      $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));

      $xc = $x+$w-$r;
      $yc = $y+$r;
      $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));
      if (strpos($corners, '2')===false)
          $this->_out(sprintf('%.2F %.2F l', ($x+$w)*$k,($hp-$y)*$k ));
      else
          $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);

      $xc = $x+$w-$r;
      $yc = $y+$h-$r;
      $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
      if (strpos($corners, '3')===false)
          $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-($y+$h))*$k));
      else
          $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);

      $xc = $x+$r;
      $yc = $y+$h-$r;
      $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
      if (strpos($corners, '4')===false)
          $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-($y+$h))*$k));
      else
          $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);

      $xc = $x+$r ;
      $yc = $y+$r;
      $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
      if (strpos($corners, '1')===false)
      {
          $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$y)*$k ));
          $this->_out(sprintf('%.2F %.2F l',($x+$r)*$k,($hp-$y)*$k ));
      }
      else
          $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
      $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3) {
      $h = $this->h;
      $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
          $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }
  }//class PDFSIACO extends FPDF {

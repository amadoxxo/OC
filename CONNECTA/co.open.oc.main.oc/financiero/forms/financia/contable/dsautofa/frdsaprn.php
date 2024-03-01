<?php
  /**
   * Documento Soporte Autofactura.
   * --- Descripcion: Permite Imprimir el Formato Estandar de Documento Soporte Autofactura.
   * @author Juan Jose Trujillo <juan.trujillo@openits.co>
   * @package openComex
   * @version 001
   */
  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");

  include("../../../../libs/php/utility.php");

  //Generacion del codigo QR
  require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/phpqrcode/qrlib.php');

  //Generacion del PDF
  define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
  require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');

  // Variables de control de errores
  $nSwitch = 0;

  // Validacion de Comprobante Repetido
  $mPrints = f_Explode_Array($prints,"|","~");
  $cAno    =  substr($mPrints[0][4],0,4);

  // Busco la Resolucion para Documentos Soporte
  $qResDocs  = "SELECT rescomxx ";
  $qResDocs .= "FROM $cAlfa.fpar0161 ";
  $qResDocs .= "WHERE ";
  $qResDocs .= "rescomxx LIKE \"%{$mPrints[0][0]}~{$mPrints[0][1]}%\" AND ";
  $qResDocs .= "regestxx = \"ACTIVO\" LIMIT 0,1";
  $xResDocs  = f_MySql("SELECT","",$qResDocs,$xConexion01,"");
  $vResDocs  = array();
  if (mysql_num_rows($xResDocs) > 0) {
    $vResDocs = mysql_fetch_array($xResDocs);
  }

  // Armo la variable con los codigos de los comprobantes de facturacion para la actualizacion del consecutivo despues de grabar el documento soporte.
  $mCodCom = f_Explode_Array($vResDocs['rescomxx'],"|","~");
  $cCodigos_Comprobantes = "";
  for ($i=0;$i<count($mCodCom);$i++) {
    $cCodigos_Comprobantes .= "\"";
    $cCodigos_Comprobantes .= "{$mCodCom[$i][1]}";
    $cCodigos_Comprobantes .= "\"";
    if ($i < (count($mCodCom) -1)) { 
      $cCodigos_Comprobantes .= ","; 
    }
  }
  // Fin de Armo la variable con los codigos de los comprobantes de facturacion para la actualizacion del consecutivo despues de grabar el documento soporte.

  $qValCsc  = "SELECT ";
  $qValCsc .= "comidxxx, ";
  $qValCsc .= "comcodxx, ";
  $qValCsc .= "comcscxx, ";
  $qValCsc .= "comcsc2x ";
  $qValCsc .= "FROM $cAlfa.fdsc$cAno ";
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

  // Movimiento de Cabecera
  $qComCab  = "SELECT ";
  $qComCab .= "$cAlfa.fdsc$cAno.*, ";
  $qComCab .= "IF(SIAI0150.CLINOMXX != \"\",SIAI0150.CLINOMXX,CONCAT(SIAI0150.CLINOM1X,\" \",SIAI0150.CLINOM2X,\" \",SIAI0150.CLIAPE1X,\" \",SIAI0150.CLIAPE2X)) AS CLINOMXX,";
  $qComCab .= "$cAlfa.SIAI0150.TDIIDXXX, ";
  $qComCab .= "$cAlfa.SIAI0150.CLINRPXX, ";
  $qComCab .= "IF($cAlfa.SIAI0150.CLIDIRXX != \"\",$cAlfa.SIAI0150.CLIDIRXX,\"SIN DIRECCION\") AS CLIDIRXX, ";
  $qComCab .= "IF($cAlfa.SIAI0150.CLITELXX != \"\",$cAlfa.SIAI0150.CLITELXX,\"SIN TELEFONO\") AS CLITELXX, ";
  $qComCab .= "IF($cAlfa.SIAI0150.CLIEMAXX != \"\",$cAlfa.SIAI0150.CLIEMAXX,\"SIN CORREO\") AS CLIEMAXX, ";
  $qComCab .= "IF($cAlfa.SIAI0150.PAIIDXXX != \"\",$cAlfa.SIAI0150.PAIIDXXX,\"\") AS PAIIDXXX, ";
  $qComCab .= "IF($cAlfa.SIAI0150.DEPIDXXX != \"\",$cAlfa.SIAI0150.DEPIDXXX,\"\") AS DEPIDXXX, ";
  $qComCab .= "IF($cAlfa.SIAI0150.CLICPOSX != \"\",$cAlfa.SIAI0150.CLICPOSX,\"\") AS CLICPOSX, ";
  $qComCab .= "IF($cAlfa.SIAI0150.CIUIDXXX != \"\",$cAlfa.SIAI0150.CIUIDXXX,\"\") AS CIUIDXXX ";
  $qComCab .= "FROM $cAlfa.fdsc$cAno ";
  $qComCab .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fdsc$cAno.terid2xx = $cAlfa.SIAI0150.CLIIDXXX ";
  $qComCab .= "WHERE $cAlfa.fdsc$cAno.comidxxx = \"$cComId\" AND ";
  $qComCab .= "$cAlfa.fdsc$cAno.comcodxx = \"$cComCod\" AND ";
  $qComCab .= "$cAlfa.fdsc$cAno.comcscxx = \"$cComCsc\" AND ";
  $qComCab .= "$cAlfa.fdsc$cAno.comcsc2x = \"$cComCsc2\" LIMIT 0,1";
  $xComCab  = f_MySql("SELECT","",$qComCab,$xConexion01,"");
  $vComObs2 = array();
  if (mysql_num_rows($xComCab) > 0) {
    $vComCab  = mysql_fetch_array($xComCab);
    $vComObs2 = explode("~",$vComCab['comobs2x']);
  }

  // Traigo los datos de resolucion
  $qResDat  = "SELECT * ";
  $qResDat .= "FROM $cAlfa.fpar0161 ";
  $qResDat .= "WHERE ";
  $qResDat .= "rescomxx LIKE \"%{$cComId}~{$cComCod}%\" AND ";
  $qResDat .= "regestxx = \"ACTIVO\" LIMIT 0,1";
  $xResDat  = f_MySql("SELECT","",$qResDat,$xConexion01,"");
  if (mysql_num_rows($xResDat) > 0) {
    $vResDat = mysql_fetch_array($xResDat);
  }
  // Fin Traigo los datos de resolucion

  // Traigo la Descripcion del Pais del Vendedor
  $qPaises  = "SELECT ";
  $qPaises .= "PAIDESXX ";
  $qPaises .= "FROM $cAlfa.SIAI0052 ";
  $qPaises .= "WHERE ";
  $qPaises .= "PAIIDXXX = \"{$vComCab['PAIIDXXX']}\" AND ";
  $qPaises .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
  $xPaises  = f_MySql("SELECT","",$qPaises,$xConexion01,"");
  $vPaises  = array();
  if (mysql_num_rows($xPaises) > 0) {
    $vPaises = mysql_fetch_array($xPaises);
  }
  // Fin Traigo la Descripcion del Pais del Vendedor

  // Traigo la Descripcion de la Ciudad del Vendedor
  $qCiuProv  = "SELECT CIUDESXX ";
  $qCiuProv .= "FROM $cAlfa.SIAI0055 ";
  $qCiuProv .= "WHERE ";
  $qCiuProv .= "$cAlfa.SIAI0055.PAIIDXXX = \"{$vComCab['PAIIDXXX']}\" AND ";
  $qCiuProv .= "$cAlfa.SIAI0055.DEPIDXXX = \"{$vComCab['DEPIDXXX']}\" AND ";
  $qCiuProv .= "$cAlfa.SIAI0055.CIUIDXXX = \"{$vComCab['CIUIDXXX']}\" AND ";
  $qCiuProv .= "$cAlfa.SIAI0055.REGESTXX = \"ACTIVO\" LIMIT 0,1";
  $xCiuProv  = f_MySql("SELECT","",$qCiuProv,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qCiuProv."~".mysql_num_rows($xCiuProv));
  $vCiuProv = array();
  if (mysql_num_rows($xCiuProv) > 0) {
    $vCiuProv = mysql_fetch_array($xCiuProv);
  }
  // Fin la Descripcion de la Traigo Ciudad del Vendedor

  // Traigo el CLINOMXX o Razon Social del Facturar
  $qFacturar  = "SELECT ";
  $qFacturar .= "$cAlfa.SIAI0150.CLIIDXXX, ";
  $qFacturar .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX, ";
  $qFacturar .= "$cAlfa.SIAI0150.CLIDIRXX, ";
  $qFacturar .= "$cAlfa.SIAI0150.CLITELXX, ";
  $qFacturar .= "$cAlfa.SIAI0150.CLIFAXXX, ";
  $qFacturar .= "IF($cAlfa.SIAI0150.PAIIDXXX != \"\",$cAlfa.SIAI0150.PAIIDXXX,\"\") AS PAIIDXXX, ";
  $qFacturar .= "IF($cAlfa.SIAI0150.DEPIDXXX != \"\",$cAlfa.SIAI0150.DEPIDXXX,\"\") AS DEPIDXXX, ";
  $qFacturar .= "IF($cAlfa.SIAI0150.CLICPOSX != \"\",$cAlfa.SIAI0150.CLICPOSX,\"\") AS CLICPOSX, ";
  $qFacturar .= "IF($cAlfa.SIAI0150.CIUIDXXX != \"\",$cAlfa.SIAI0150.CIUIDXXX,\"\") AS CIUIDXXX ";
  $qFacturar .= "FROM $cAlfa.SIAI0150 ";
  $qFacturar .= "WHERE ";
  $qFacturar .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$vComCab['teridxxx']}\" AND ";
  $qFacturar .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
  $xFacturar  = f_MySql("SELECT","",$qFacturar,$xConexion01,"");
  $vFacturar  = array();
  if (mysql_num_rows($xFacturar) > 0) {
    $vFacturar = mysql_fetch_array($xFacturar);
  }
  // Fin Traigo el CLINOMXX o Razon Social del Facturar

  // Traigo la Descripcion de la Ciudad del Facturar
  $qCiuFact  = "SELECT CIUDESXX ";
  $qCiuFact .= "FROM $cAlfa.SIAI0055 ";
  $qCiuFact .= "WHERE ";
  $qCiuFact .= "$cAlfa.SIAI0055.PAIIDXXX = \"{$vFacturar['PAIIDXXX']}\" AND ";
  $qCiuFact .= "$cAlfa.SIAI0055.DEPIDXXX = \"{$vFacturar['DEPIDXXX']}\" AND ";
  $qCiuFact .= "$cAlfa.SIAI0055.CIUIDXXX = \"{$vFacturar['CIUIDXXX']}\" AND ";
  $qCiuFact .= "$cAlfa.SIAI0055.REGESTXX = \"ACTIVO\" LIMIT 0,1";
  $xCiuFact  = f_MySql("SELECT","",$qCiuFact,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qCiuFact."~".mysql_num_rows($xCiuFact));
  $vCiuFact = array();
  if (mysql_num_rows($xCiuFact) > 0) {
    $vCiuFact = mysql_fetch_array($xCiuFact);
  }
  // Fin la Descripcion de la Traigo Ciudad del Facturar

  // Busco descripcion Medio de Pago
  if ($vComObs2[3] != "") {
    $qMedPag  = "SELECT ";
    $qMedPag .= "mpaidxxx, ";
    $qMedPag .= "mpadesxx, ";
    $qMedPag .= "regestxx ";
    $qMedPag .= "FROM $cAlfa.fpar0155 ";
    $qMedPag .= "WHERE ";
    $qMedPag .= "mpaidxxx = \"{$vComObs2[3]}\" LIMIT 0,1";
    $xMedPag  = f_MySql("SELECT","",$qMedPag,$xConexion01,"");
    // f_Mensaje(__FILE__,__LINE__,$qMedPag."~ ".mysql_num_rows($xMedPag));
    $vMedPag = mysql_fetch_array($xMedPag);
  }
  $cFormaPago = ($vComObs2[2] == "1") ? 'CONTADO' : 'CREDITO';

  // Busco descripcion del tipo de documento
  $qTipoDoc  = "SELECT ";
  $qTipoDoc .= "tdiidxxx, ";
  $qTipoDoc .= "tdidesxx, ";
  $qTipoDoc .= "regestxx ";
  $qTipoDoc .= "FROM $cAlfa.fpar0109 ";
  $qTipoDoc .= "WHERE ";
  $qTipoDoc .= "tdiidxxx = \"{$vComCab['TDIIDXXX']}\" LIMIT 0,1";
  $xTipoDoc  = f_MySql("SELECT","",$qTipoDoc,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qTipoDoc."~ ".mysql_num_rows($xTipoDoc));
  $vTipoDoc = mysql_fetch_array($xTipoDoc);

  // Movimiento de Detalle
  $qComDet  = "SELECT DISTINCT ";
  $qComDet .= "$cAlfa.fdsd$cAno.* ";
  $qComDet .= "FROM $cAlfa.fdsd$cAno ";
  $qComDet .= "WHERE $cAlfa.fdsd$cAno.comidxxx = \"$cComId\" AND ";
  $qComDet .= "$cAlfa.fdsd$cAno.comcodxx = \"$cComCod\" AND ";
  $qComDet .= "$cAlfa.fdsd$cAno.comcscxx = \"$cComCsc\" AND ";
  $qComDet .= "$cAlfa.fdsd$cAno.comcsc2x = \"$cComCsc2\" ";
  $qComDet .= "ORDER BY ABS($cAlfa.fdsd$cAno.comseqxx) ASC ";
  $xComDet  = f_MySql("SELECT","",$qComDet,$xConexion01,"");
  $mDataItem = array();
  if(mysql_num_rows($xComDet) > 0) {
    //Cargo la Matriz con los ROWS del Cursor
    $mComDet = array();
    $iA = 0;
    while ($xRCD = mysql_fetch_array($xComDet)) {
      $mComDet[$iA] = $xRCD;
      $iA++;

      if($xRCD['comctocx'] == "ITEM"){
        //La descripcion del concepto se trae segun el origen
        if ($vComObs2[5] == "") {
          // Se guardo desde el modulo de documento soporte
          $qCtoId  = "SELECT ";
          $qCtoId .= "$cAlfa.fpar0165.pucidxxx, ";
          $qCtoId .= "$cAlfa.fpar0165.ctoidxxx, ";
          $qCtoId .= "$cAlfa.fpar0165.ctodesxx, ";
          $qCtoId .= "$cAlfa.fpar0115.puccccxx, ";
          $qCtoId .= "$cAlfa.fpar0115.pucdoscc, ";
          $qCtoId .= "$cAlfa.fpar0115.pucdetxx, ";
          $qCtoId .= "$cAlfa.fpar0115.pucretxx  ";
          $qCtoId .= "FROM $cAlfa.fpar0165 ";
          $qCtoId .= "LEFT JOIN $cAlfa.fpar0115 ON CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = $cAlfa.fpar0165.pucidxxx ";
          $qCtoId .= "WHERE ";
          $qCtoId .= "$cAlfa.fpar0165.pucidxxx = \"{$xRCD['pucidxxx']}\" AND ";
          $qCtoId .= "$cAlfa.fpar0165.ctoidxxx = \"{$xRCD['ctoidxxx']}\" LIMIT 0,1 ";
          $xCtoId  = f_MySql("SELECT","",$qCtoId,$xConexion01,"");
          $vCtoId = mysql_fetch_array($xCtoId);
          // f_Mensaje(__FILE__,__LINE__,$qCtoId."~ ".mysql_num_rows($xCtoId));
        } else {
          // Si se especifico que id de comprobante se tomo la descripcion se muestra ese, 
          // sino se busca la primera descripcion que aparezca
          if ($xRCD['comctoc2'] != "") {
            $qDesCom  = "IF($cAlfa.fpar0119.ctodesx".strtolower($xRCD['comctoc2'])." != \"\",$cAlfa.fpar0119.ctodesx".strtolower($xRCD['comctoc2']).",$cAlfa.fpar0119.ctodesxx) ";
          } else {
            // Se guardo desde el movimiento contable
            //Descripcion concepto contable
            $qDesCom  = "IF($cAlfa.fpar0119.ctodesxp != \"\",$cAlfa.fpar0119.ctodesxp, ";
              $qDesCom .= "IF($cAlfa.fpar0119.ctodesxg != \"\",$cAlfa.fpar0119.ctodesxg, ";
                $qDesCom .= "IF($cAlfa.fpar0119.ctodesxr != \"\",$cAlfa.fpar0119.ctodesxr, ";
                  $qDesCom .= "IF($cAlfa.fpar0119.ctodesxl != \"\",$cAlfa.fpar0119.ctodesxl, ";
                    $qDesCom .= "IF($cAlfa.fpar0119.ctodesxf != \"\",$cAlfa.fpar0119.ctodesxf, ";
                      $qDesCom .= "IF($cAlfa.fpar0119.ctodesxm != \"\",$cAlfa.fpar0119.ctodesxm, ";
                        $qDesCom .= "IF($cAlfa.fpar0119.ctodesxx != \"\",$cAlfa.fpar0119.ctodesxx,\"CONCEPTO SIN DESCRIPCION\") ";
                      $qDesCom .= ")";
                    $qDesCom .= ")";
                  $qDesCom .= ")";
                $qDesCom .= ")";
              $qDesCom .= ")";
            $qDesCom .= ")";
          }

          $qCtoId  = "SELECT ";
          $qCtoId .= "$cAlfa.fpar0119.pucidxxx, ";
          $qCtoId .= "$cAlfa.fpar0119.ctoidxxx, ";
          $qCtoId .= $qDesCom." AS ctodesxx, ";
          $qCtoId .= "$cAlfa.fpar0115.puccccxx, ";
          $qCtoId .= "$cAlfa.fpar0115.pucdoscc, ";
          $qCtoId .= "$cAlfa.fpar0115.pucdetxx, ";
          $qCtoId .= "$cAlfa.fpar0115.pucretxx  ";
          $qCtoId .= "FROM $cAlfa.fpar0119 ";
          $qCtoId .= "LEFT JOIN $cAlfa.fpar0115 ON CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = $cAlfa.fpar0119.pucidxxx ";
          $qCtoId .= "WHERE ";
          $qCtoId .= "$cAlfa.fpar0119.pucidxxx = \"{$xRCD['pucidxxx']}\" AND ";
          $qCtoId .= "$cAlfa.fpar0119.ctoidxxx = \"{$xRCD['ctoidxxx']}\" LIMIT 0,1 ";
          $xCtoId  = f_MySql("SELECT","",$qCtoId,$xConexion01,"");
          // f_Mensaje(__FILE__,__LINE__,$qCtoId."~ ".mysql_num_rows($xCtoId));
          if (mysql_num_rows($xCtoId) > 0) {
            $vCtoId = mysql_fetch_array($xCtoId);
          } else {
            // Se busca en conceptos de causacion automatica
            $qCtoId  = "SELECT ";
            $qCtoId .= "$cAlfa.fpar0121.pucidxxx, ";
            $qCtoId .= "$cAlfa.fpar0121.ctoidxxx, ";
            $qCtoId .= "$cAlfa.fpar0121.ctodesxx, ";
            $qCtoId .= "$cAlfa.fpar0115.puccccxx, ";
            $qCtoId .= "$cAlfa.fpar0115.pucdoscc, ";
            $qCtoId .= "$cAlfa.fpar0115.pucdetxx, ";
            $qCtoId .= "$cAlfa.fpar0115.pucretxx  ";
            $qCtoId .= "FROM $cAlfa.fpar0121 ";
            $qCtoId .= "LEFT JOIN $cAlfa.fpar0115 ON CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = $cAlfa.fpar0121.pucidxxx ";
            $qCtoId .= "WHERE ";
            $qCtoId .= "$cAlfa.fpar0121.pucidxxx = \"{$xRCD['pucidxxx']}\" AND ";
            $qCtoId .= "$cAlfa.fpar0121.ctoidxxx = \"{$xRCD['ctoidxxx']}\" LIMIT 0,1 ";
            $xCtoId  = f_MySql("SELECT","",$qCtoId,$xConexion01,"");
            $vCtoId = mysql_fetch_array($xCtoId);
          }
        }

        $nInd_mDataItem = count($mDataItem);
        $mDataItem[$nInd_mDataItem]['ctoidxxx'] = $xRCD['ctoidxxx'];
        $mDataItem[$nInd_mDataItem]['ctodesxx'] = $vCtoId['ctodesxx'];
        $mDataItem[$nInd_mDataItem]['comcanxx'] = $xRCD['comcanxx'];
        $mDataItem[$nInd_mDataItem]['comvlrun'] = $xRCD['comvlrun'];
        $mDataItem[$nInd_mDataItem]['comvlrxx'] = $xRCD['comvlrxx'];
      }
    }
  }

  // echo "<pre>";
  // print_r($mDataItem);
  // die();
 
  if($nSwitch == 0){
    class PDF extends FPDF {
      function Header() {
        global $vComCab;   global $cFormaPago; global $vMedPag; global $vTipoDoc; global $vCiuProv; global $vPaises;
        global $vFacturar; global $vCiuFact;   global $vResDat; global $cAlfa; global $cPlesk_Skin_Directory;

        $posx = 10;
        $posy = 5;

				switch($cAlfa){
					case "HAYDEARX":   //HAYDEARX
					case "DEHAYDEARX": //HAYDEARX
					case "TEHAYDEARX": //HAYDEARX
						$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logohaydear.jpeg',$posx,$posy+3,50,18);
					break;
					case "CONNECTA":   //CONNECTA
					case "DECONNECTA": //CONNECTA
					case "TECONNECTA": //CONNECTA
						$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoconnecta.jpg',$posx,$posy+3,35,20);
					break;
				}

        // Datos del documento soporte
        // Bloque de la izquierda
        $this->SetFont("verdana", '', 7);
        $this->setXY($posx, $posy + 30);
        $this->Cell(66, 4, utf8_decode("Fecha y hora: " . $vComCab['comfecxx'] . " " . $vComCab['reghcrex']), 0, 0, 'L');
        $this->setXY($posx, $posy + 33);
        $this->Cell(66, 4, utf8_decode("Fecha de vencimiento: " . $vComCab['comfecve']), 0, 0, 'L');
        $this->setXY($posx, $posy + 39);
        $this->Cell(66, 4, utf8_decode("Forma de pago: " . $cFormaPago), 0, 'L');
        $this->setXY($posx, $posy + 42);
        $this->Cell(66, 4, utf8_decode("Medio de pago: " . $vMedPag['mpadesxx']), 0, 'L');

        // Datos del Vendedor
        $this->setXY($posx, $posy + 48);
        $this->SetFont("verdanab", '', 7);
        $this->Cell(66, 4, utf8_decode("Vendedor"), 0, 0, 'L');
        $this->SetFont("verdana", '', 7);
        $this->setXY($posx, $posy + 52);
        $this->MultiCell(120, 4, utf8_decode($vComCab['CLINOMXX']), 0, 'L');
        $this->setXY($posx, $posy + 56);
        $this->MultiCell(66, 4, $vTipoDoc['tdidesxx'] . ": " . $vComCab['terid2xx'], 0, 'L');
        $this->setXY($posx, $posy + 60);
        $this->Cell(66, 4, utf8_decode("Tel: " . $vComCab['CLITELXX']), 0, 0, 'L');
        $this->setXY($posx, $posy + 64);
        $this->Cell(66, 4, utf8_decode("Dir: " . $vComCab['CLIDIRXX']), 0, 0, 'L');
        $this->setXY($posx, $posy + 68);
        $this->Cell(108, 4, utf8_decode($vCiuProv['CIUDESXX']), 0, 0, 'L');
        $this->setXY($posx, $posy + 72);
        $this->Cell(108, 4, utf8_decode($vPaises['PAIDESXX']), 0, 0, 'L');

        // Datos del documento soporte
        // Bloque de la deracha
        $this->SetFont("verdanab", '', 7);
        $this->setXY($posx + 110, $posy + 30);
        $this->MultiCell(85, 4, utf8_decode("DOCUMENTO SOPORTE EN ADQUISICIONES EFECTUADAS A NO OBLIGADOS A FACTURAR No. " . $vResDat['resprexx']." ".$vComCab['comcscxx']), 0, 'R');
        $this->Ln(1);
        $this->SetFont("verdana", '', 7);
        $this->setX($posx + 66);
        $this->Cell(129, 4, utf8_decode($vFacturar['CLINOMXX']), 0, 0, 'R');
        $this->Ln(4);
        $this->setX($posx + 66);
        $this->Cell(129, 4, utf8_decode("N.I.T Nº: " . $vComCab['teridxxx']), 0, 0, 'R');
        $this->Ln(4);
        $this->setX($posx + 66);
        $this->Cell(129, 4, utf8_decode("Responsable del impuesto sobre las ventas IVA"), 0, 0, 'R');
        $this->Ln(4);
        $this->setX($posx + 66);
        $this->Cell(129, 4, utf8_decode("Dir.: " . $vFacturar['CLIDIRXX']), 0, 0, 'R');
        $this->Ln(4);
        $this->setX($posx + 66);
        $this->Cell(129, 4, utf8_decode("Tel.: " . $vFacturar['CLITELXX']), 0, 0, 'R');
        $this->Ln(4);
        $this->setX($posx + 66);
        $this->Cell(129, 4, utf8_decode($vCiuFact['CIUDESXX']), 0, 0, 'R');
        $this->Ln(4);
        $posyfin = $this->GetY();

        // Se obtiene la vigencia en meses de la resolucion
        $dFechaInicial  = date_create($vResDat['resfdexx']);
        $dFechaFinal    = date_create($vResDat['resfhaxx']);
        $nDiferencia    = date_diff($dFechaInicial, $dFechaFinal);
        $nMesesVigencia = ( $nDiferencia->y * 12 ) + $nDiferencia->m;

        $this->setX($posx + 66);
        $this->Cell(129, 4, utf8_decode("Resolución DIAN {$vResDat['residxxx']}"), 0, 0, 'R');
        $this->Ln(4);
        $this->setX($posx + 66);
        $this->Cell(129, 4, utf8_decode("Fecha de Expedición ". substr($vResDat['resfdexx'], 0, 4)."-".substr($vResDat['resfdexx'], 5, 2)."-".substr($vResDat['resfdexx'], 8, 2)), 0, 0, 'R');
        $this->Ln(4);
        $this->setX($posx + 66);
        $this->Cell(129, 4, utf8_decode("Vigencia de Resolución ". $nMesesVigencia ." meses"), 0, 0, 'R');
        $this->Ln(4);
        $this->setX($posx + 66);
        $this->Cell(129, 4, utf8_decode("Numeración Autorizada desde el No. {$vResDat['resprexx']}{$vResDat['resdesxx']} hasta el No. {$vResDat['resprexx']}{$vResDat['reshasxx']}"), 0, 0, 'R');
        $this->Ln(5);

        $posy = ($posyfin > $this->GetY()) ? $posyfin+5 : $this->GetY()+5;
        $this->SetFillColor(240, 240, 240);
        $this->SetTextColor(0, 0, 0);
        $this->setXY($posx, $posy);
        $this->SetFont('verdanab', '', 7);
        $this->Cell(10, 6, utf8_decode("Ítem"), "B", 0, 'C', true);
        $this->Cell(15, 6, utf8_decode("Código"), "B", 0, 'C', true);
        $this->Cell(98, 6, utf8_decode("Descripción"), "B", 0, 'C', true);
        $this->Cell(15, 6, utf8_decode("Cantidad"), "B", 0, 'C', true);
        $this->Cell(28, 6, utf8_decode("Valor Unitario"), "B", 0, 'C', true);
        $this->Cell(30, 6, utf8_decode("Valor Total"), "B", 0, 'C', true);
      }

      function Footer(){
        global $vSysStr; global $_COOKIE; global $vComCab; global $_SERVER; global $cPlesk_Skin_Directory; global $nTotRiva;
        global $nTotRfte;

        // Datos QR y Firma
        $posx = 10;
        $posy = 230;

        $this->setXY($posx,$posy+2);
        $this->SetFont('verdana','',7);
        $this->Cell(100,4,utf8_decode("Fecha y hora de validación dian: "),0,0,'L');
        $this->Ln(4);
        $this->setX($posx);
        $this->Cell(100,4,utf8_decode("Representación gráfica de documento soporte electrónico: "),0,0,'L');
        $this->Ln(4);
        $this->setX($posx);
        $this->Cell(50,4,utf8_decode("Firma Electrónica:"),0,0,'L');
        $this->Ln(4);
        $this->setX($posx);
        $this->SetFont('verdana','',6);
        $this->MultiCell(130,3, $vComCab['comptesv'],0,'L');
        // Recuadro de retenciones
        if ($nTotRiva > 0 || $nTotRfte > 0) {
          $this->SetFont('Arial','B',7);
          $this->setXY($posx + 153, 208);
          $this->Cell(25, 6, "VALORES INFORMATIVOS",0,0,'C');
          $this->setXY($posx + 135, 214.8);
          $this->SetFillColor(240, 240, 240);
          $this->Cell(60, 4.5, "RETENCIONES",1,1,'L', true);
          $this->SetFont('Arial','',7);
          $this->Rect($posx + 135, 219.3, 60, 10);
        }

        $this->setXY($posx+150,$posy+2);
        $this->SetFont('verdanab','',7);
        $this->Cell(100,4,utf8_decode("CUFE"),0,0,'L');
        $this->Ln(4);
        $this->setX($posx+150);
        $this->SetFont('verdana','',6);
        $this->MultiCell(45,3, $vComCab['comptecu'],0,'L');
        $this->Ln(4);

        if ($vComCab['compteqr'] != "") {
          $cFileQR = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/QR_".$_COOKIE['kUsrId']."_".date("YmdHis").".png";
          QRcode::png($vComCab['compteqr'], $cFileQR, "H", 10, 1);
          $this->Image($cFileQR,$posx+165, $posy+20,25,25);
        }

        //Paginación
        $this->setXY($posx, $posy + 41);
        $this->SetFont('verdana', '', 7);
        $this->Cell(190, 4, utf8_decode('Pág. ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
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
    }

    $pdf = new PDF('P','mm','Letter');
    $pdf->AddFont('verdana','','verdana.php');
    $pdf->AddFont('verdanab','','verdanab.php');
    $pdf->SetFont('verdana','',8);
    $pdf->AliasNbPages();
    $pdf->SetMargins(0,0,0);
    $pdf->SetAutoPageBreak(0,0);

    $pdf->AddPage();
    $posy      = $pdf->GetY()+8; 
    $posx      = 10;
    $posfin    = 215;
    $nCount    = 0; 
    $pyy       = $posy;
    $nSubtotal = 0;

    // $mDataItem = array_merge($mDataItem, $mDataItem, $mDataItem);
    // $mDataItem = array_merge($mDataItem, $mDataItem, $mDataItem);
    // $mDataItem = array_merge($mDataItem, $mDataItem, $mDataItem);

    // Imprimo los Item
    if (count($mDataItem) > 0) {
      $pdf->SetWidths(array(10,18,95,15,27,30));
      $pdf->SetAligns(array("C","L","L","R","R","R"));
      $pdf->setXY($posx, $pyy);

      for($i=0;$i<count($mDataItem);$i++){
        $nCount++;
        $pyy = $pdf->GetY();
        if($pyy > $posfin){
          $pdf->AddPage();
          $pyy = $posy;
          $pdf->setXY($posx, $pyy);
        }

        $nSubtotal += $mDataItem[$i]['comvlrxx'];

        $pdf->SetFont('verdana','',7);
        $pdf->setX($posx);
        $pdf->Row(array(
          $nCount,
          $mDataItem[$i]['ctoidxxx'],
          $mDataItem[$i]['ctodesxx'],
          $mDataItem[$i]['comcanxx'],
          number_format($mDataItem[$i]['comvlrun'],2,'.',','),
          number_format($mDataItem[$i]['comvlrxx'],2,'.',',')
        ));
      }//for($i=0;$i<count($mDataItem);$i++){
      
      // Total de items
      $pdf->setXY($posx, $pyy+5);
      $pdf->Cell(35, 4, utf8_decode("Total Ítem: ") . $nCount, 0, 0, 'L');

      if($pyy > $posfin){
        $pdf->AddPage();
        $pyy = $posy;
        $pdf->setXY($posx, $pyy);
      }
    }// if (count($mDataItem) > 0) {
    // Fin Imprimo Item

    // Se obtienen los valores de los totales
    $nIva          = 0;
    $nTotRiva      = 0;
    $nTotRfte      = 0;
    $nTotCargo     = 0;
    $nTotDescuento = 0;

    for ($k=0;$k<count($mComDet);$k++) {
      // Se obtiene el valor del IVA
      if($mComDet[$k]['comctocx'] == 'IVA'){
        $nIva += $mComDet[$k]['comvlrxx'];
      }

      // Se obtiene el valor del RETEIVA
      if($mComDet[$k]['comctocx'] == 'RETEIVA'){
        $nTotRiva += $mComDet[$k]['comvlrxx'];
      }

      // Se obtiene el valor del RETEFTE
      if($mComDet[$k]['comctocx'] == 'RETEFTE'){
        $nTotRfte += $mComDet[$k]['comvlrxx'];
      }

      // Se obtiene el valor del CARGO
      if($mComDet[$k]['comctocx'] == 'CARGO'){
        $nTotCargo += $mComDet[$k]['comvlrxx'];
      }

      // Se obtiene el valor del DESCUENTO
      if($mComDet[$k]['comctocx'] == 'DESCUENTO'){
        $nTotDescuento += $mComDet[$k]['comvlrxx'];
      }
    }

    $nTotal = $nSubtotal + $nIva;
    $nTotalPagar = ($nTotal + $nTotCargo) - ($nTotDescuento);

    // Imprimo los valores de los Totales
    if($pyy > 185){
      $pdf->AddPage();
      $pyy = $posy;
    }

    $posx = 10;
    $posy = 180;

    if ($nTotRfte == 0 && $nTotRiva == 0) {
      $posy = 205;
    } 
    $pdf->Line($posx, $posy-2, $posx + 195, $posy-2);

    $pdf->setXY($posx + 135, $posy);
    $pdf->SetFont('verdana', '', 7);
    $pdf->Cell(35, 4, "SUBTOTAL", 0, 0, 'L');
    $pdf->SetFont('verdana', '', 7);
    $pdf->Cell(25, 4, number_format($nSubtotal, 2, '.', ','), 0, 0, 'R');
    $pdf->ln(4);
    $pdf->setX($posx + 135);
    $pdf->SetFont('verdana', '', 7);
		if ($nIva > 0) {
    	$pdf->Cell(35, 4, "IVA", 0, 0, 'L');
    	$pdf->SetFont('verdana', '', 7);
			$pdf->Cell(25, 4, number_format($nIva, 2, '.', ','), 0, 0, 'R');
      $pdf->ln(4);
		}
    $pdf->setX($posx + 135);
    $pdf->SetFont('verdana', '', 7);
    $pdf->Cell(35, 4, "TOTAL", 0, 0, 'L');
    $pdf->SetFont('verdana', '', 7);
    $pdf->Cell(25, 4, number_format($nTotal, 2, '.', ','), 0, 0, 'R');
    $pdf->ln(4);

    if ($nTotDescuento > 0) {
      $pdf->setX($posx + 135);
      $pdf->SetFont('verdana', '', 7);
      $pdf->Cell(35, 4, "DESCUENTOS", 0, 0, 'L');
      $pdf->SetFont('verdana', '', 7);
      $pdf->Cell(25, 4, number_format($nTotDescuento, 2, '.', ','), 0, 0, 'R');
      $pdf->ln(4);
    }
   
    if ($nTotCargo > 0) {
      $pdf->setX($posx + 135);
      $pdf->SetFont('verdana', '', 7);
      $pdf->Cell(35, 4, "CARGOS", 0, 0, 'L');
      $pdf->SetFont('verdana', '', 7);
      $pdf->Cell(25, 4, number_format($nTotCargo, 2, '.', ','), 0, 0, 'R');
      $pdf->ln(4);
    }

    $pdf->setX($posx + 135);
    $pdf->SetFont('verdana', '', 7);
    $pdf->Cell(35, 4, "TOTAL A PAGAR", 0, 0, 'L');
    $pdf->SetFont('verdana', '', 7);
    $pdf->Cell(25, 4, number_format(abs($nTotalPagar), 2, '.', ','), 0, 0, 'R');

    // Retenciones
    if ($nTotRfte > 0){
      $pdf->setXY($posx + 135, 220);
      $pdf->SetFont('verdana', '', 7);
      $pdf->Cell(35, 4, "RETEFUENTE", 0, 0, 'L');
      $pdf->Cell(25, 4, number_format($nTotRfte, 2, '.', ','), 0, 0, 'R');
    }
 
    if ($nTotRiva > 0) {
      $pdf->setXY($posx + 135, 224);
      $pdf->SetFont('verdana', '', 7);
      $pdf->Cell(35, 4, "RETEIVA", 0, 0, 'L');
      $pdf->Cell(25, 4, number_format($nTotRiva, 2, '.', ','), 0, 0, 'R');
    }

    //Valor en letras
    $nTotLetra = f_Cifra_Php(str_replace("-","",abs($nTotalPagar)),"PESO");
    $pdf->setXY($posx, $posy);
    $pdf->SetFont('verdana', '', 7);
    $pdf->MultiCell(130, 4, "SON: " . utf8_decode($nTotLetra), 0, 'L');
    $pdf->ln(1);

    $pdf->setX($posx);
    $pdf->SetFont('verdana', '', 7);
    $pdf->Cell(35, 4, utf8_decode("Observación"), 0, 0, 'L');
    $pdf->ln(4);
    $pdf->setX($posx);
    $pdf->MultiCell(130, 3.5, $vComCab['comobsxx'], 0, 'L');

    $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
    $pdf->Output($cFile);

    if (file_exists($cFile)){
      chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
    } else {
      f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
    }

    echo "<html><script>document.location='$cFile';</script></html>";
  }

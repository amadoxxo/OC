<?php
  namespace openComex;
/**
 * Carga Pagos a Terceros y Anticipos sin Facturar
 * Este programa permite Cargar los Pagos a Terceros y Anticipos sin Facturar
 * @author Johana Arboleda Ramos <johana.arboleda@open-eb.co>
 * @package openComex
 */

// ini_set('error_reporting', E_ERROR);
// ini_set("display_errors","1");

include("../../../../libs/php/utility.php");
include("../../../../libs/php/utiliqdo.php");
include("../../../../libs/php/utimovdo.php");

$nSwitch = 0; // Switch para Vericar la Validacion de Datos
$cMsj = "";

/**
* Validando Licencia
*/
$nLic = f_Licencia();
if ($nLic == 0){
  $nSwitch = 1;
  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  $cMsj .= "Error grave de Seguridad otro usuario ingreso con su clave.\n";
}

if($_POST['cSucIdOri'] != "" && $_POST['cDocNroOri'] != "" && $_POST['cDocSufOri'] != "") {

  //Trayendo los comprobantes a los cuales el usuario puede hacer traslado
  $qUsrDat  = "SELECT ";
  $qUsrDat .= "$cAlfa.SIAI0003.USRDOCTR ";
  $qUsrDat .= "FROM $cAlfa.SIAI0003 ";
  $qUsrDat .= "WHERE ";
  $qUsrDat .= "$cAlfa.SIAI0003.USRIDXXX = \"{$_COOKIE['kUsrId']}\" LIMIT 0,1 ";
  $xUsrDat  = f_MySql("SELECT","",$qUsrDat,$xConexion01,""); 
  $vUsrDat = mysql_fetch_array($xUsrDat); 
  // f_Mensaje(__FILE__,__LINE__,$qUsrDat);

  if($vUsrDat['USRDOCTR'] == "") {
    $nSwitch = 1;
    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
    $cMsj .= "El Usuario [{$_COOKIE['kUsrId']}] No Tiene Comprobantes Autorizados para Traslado de DO a DO.\n";
  }

  if ($nSwitch == 0) {
    //Validando que el DO enviado exista
    $qTramites  = "SELECT sucidxxx, docidxxx, docsufxx, doctipxx, ccoidxxx, regfcrex, regestxx ";
    $qTramites .= "FROM $cAlfa.sys00121 ";
    $qTramites .= "WHERE ";
    $qTramites .= "sucidxxx = \"{$_POST['cSucIdOri']}\" AND ";
    $qTramites .= "docidxxx = \"{$_POST['cDocNroOri']}\" AND ";
    $qTramites .= "docsufxx = \"{$_POST['cDocSufOri']}\" AND ";
    $qTramites .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
    $xTramites  = f_MySql("SELECT","",$qTramites,$xConexion01,"");
    // f_Mensaje(__FILE__,__LINE__,$qTramites." ~ ".mysql_num_rows($xTramites));
    if (mysql_num_rows($xTramites) == 0) {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "El Do [{$_POST['cSucIdOri']}-{$_POST['cDocNroOri']}-{$_POST['cDocSufOri']}] No Existe o Se Encuentra en Estado FACTURADO.\n";
    }else {
      $vTramites = mysql_fetch_array($xTramites);
      
      //Trae el movimiento del DO
      //Buscano conceptos de causaciones automaticas
      $qCAyP121  = "SELECT DISTINCT ";
      $qCAyP121 .= "pucidxxx, ";
      $qCAyP121 .= "ctoidxxx ";
      $qCAyP121 .= "FROM $cAlfa.fpar0121 ";
      $qCAyP121 .= "WHERE ";
      $qCAyP121 .= "regestxx = \"ACTIVO\"";
      $xCAyP121 = f_MySql("SELECT","",$qCAyP121,$xConexion01,"");
      //f_Mensaje(__FILE__,__LINE__,$qCAyP121."~".mysql_num_rows($xCAyP121));
      $cCAyP121 = "";
      while($xRCP121 = mysql_fetch_array($xCAyP121)) {
        $cCAyP121 .= "\"{$xRCP121['pucidxxx']}~{$xRCP121['ctoidxxx']}\",";
      }

      //Buscando conceptos
      $qCtoAntyPCC  = "SELECT DISTINCT ";
      $qCtoAntyPCC .= "ctoantxx, ";
      $qCtoAntyPCC .= "ctopccxx, ";
      $qCtoAntyPCC .= "pucidxxx, ";
      $qCtoAntyPCC .= "ctoidxxx ";
      $qCtoAntyPCC .= "FROM $cAlfa.fpar0119 ";
      $qCtoAntyPCC .= "WHERE ";
      $qCtoAntyPCC .= "(ctoantxx = \"SI\" OR ctopccxx = \"SI\") AND ";
      $qCtoAntyPCC .= "regestxx = \"ACTIVO\"";
      $xCtoAntyPCC = f_MySql("SELECT","",$qCtoAntyPCC,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qCtoAntyPCC."~".mysql_num_rows($xCtoAntyPCC));

      $cCtoAntyPCC = "";
      while($xRCAP = mysql_fetch_array($xCtoAntyPCC)) {
        $cCtoAntyPCC .= "\"{$xRCAP['pucidxxx']}~{$xRCAP['ctoidxxx']}\",";
      }
      $cCtoAntyPCC = $cCAyP121.substr($cCtoAntyPCC,0,strlen($cCtoAntyPCC)-1);

      //Trayendo descripcion de los centro de costo
      $qCenCos  = "SELECT ccoidxxx, ccodesxx ";
      $qCenCos .= "FROM $cAlfa.fpar0116 ";
      $xCenCos  = f_MySql("SELECT","",$qCenCos,$xConexion01,"");
      $vCenCos = array();
      while ($xRCC = mysql_fetch_array($xCenCos)) {
        $vCenCos["{$xRCC['ccoidxxx']}"] = "{$xRCC['ccodesxx']}";
      }

      //Trayendo tipo de comprobante
      $qTipCom  = "SELECT comidxxx, comcodxx, comtipxx ";
      $qTipCom .= "FROM $cAlfa.fpar0117 ";
      $xTipCom  = f_MySql("SELECT","",$qTipCom,$xConexion01,"");
      $vTipCom = array();
      while ($xRTC = mysql_fetch_array($xTipCom)) {
        $vTipCom["{$xRTC['comidxxx']}~{$xRTC['comcodxx']}"] = "{$xRTC['comtipxx']}";
      }

      //Array de clientes-proveedores
      $vClientes = array();
      $mClientes = array();

      //Se busca movimiento contable desde el año anterior a la creacion del DO
      $nAnoIni = ((substr($vTramites['regfcrex'],0,4)-1) <  $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : (substr($vTramites['regfcrex'],0,4)-1);

      $mMovCon = array();
      for ($iAno=$nAnoIni;$iAno<=date('Y');$iAno++) { // Recorro desde el año de creacion del DO hasta el año de la fecha de la factura
        /// SELECT CON LEFT JOIN DE LAS TABLAS fcod, fpar0117, fpar0119 y SIAI0150. ///
        $qSqlMdo  = "SELECT ";
        $qSqlMdo .= "comidxxx, ";
        $qSqlMdo .= "comcodxx, ";
        $qSqlMdo .= "comcscxx, ";
        $qSqlMdo .= "comcsc2x, ";
        $qSqlMdo .= "comcsc3x, ";
        $qSqlMdo .= "comseqxx, ";
        $qSqlMdo .= "comfecxx, ";
        $qSqlMdo .= "pucidxxx, ";
        $qSqlMdo .= "ccoidxxx, ";
        $qSqlMdo .= "ctoidxxx, ";
        $qSqlMdo .= "commovxx, ";
        $qSqlMdo .= "comperxx, ";
        $qSqlMdo .= "teridxxx, ";
        $qSqlMdo .= "terid2xx, ";
        $qSqlMdo .= "comvlrxx, ";
        $qSqlMdo .= "comvlr01, ";
        $qSqlMdo .= "comvlr02, ";
        $qSqlMdo .= "comidcxx, ";
        $qSqlMdo .= "comcodcx, ";
        $qSqlMdo .= "comcsccx, ";
        $qSqlMdo .= "comseqcx, ";
        $qSqlMdo .= "comidc2x, ";
        $qSqlMdo .= "comcodc2, ";
        $qSqlMdo .= "comcscc2, ";
        $qSqlMdo .= "comseqc2, ";
        $qSqlMdo .= "comctoc2, ";
        $qSqlMdo .= "sucidxxx, ";
        $qSqlMdo .= "docidxxx, ";
        $qSqlMdo .= "docsufxx  ";
        $qSqlMdo .= "FROM $cAlfa.fcod$iAno ";
        $qSqlMdo .= "WHERE ";
        $qSqlMdo .= "CONCAT(pucidxxx,\"~\",ctoidxxx) IN ($cCtoAntyPCC) AND "; // combinacion cuenta-concepto para anticipos y pcc
        $qSqlMdo .= "comfacxx = \"\" AND "; //cuenta puc para los cuadre de DO de anticipos y pcc
        $qSqlMdo .= "comcsccx = \"{$_POST['cDocNroOri']}\" AND ";
        $qSqlMdo .= "comseqcx = \"{$_POST['cDocSufOri']}\" AND ";
        $qSqlMdo .= "regestxx IN (\"ACTIVO\",\"PROVISIONAL\") ";
        $qSqlMdo .= "ORDER BY comidxxx,comcodxx,comcscxx,comseqxx";
        $xCrsMdo = f_MySql("SELECT","",$qSqlMdo,$xConexion01,"");
        // echo $qSqlMdo."~".mysql_num_rows($xCrsMdo)."<br><br>";

        while ($xRMdo = mysql_fetch_assoc($xCrsMdo)) {

          $nIncluir = 0; //Varible que indica si el registro es del DO o NO y si se debe tener en cuenta
          if ($xRMdo['sucidxxx'] != "" && $xRMdo['docidxxx'] != "" && $xRMdo['docsufxx'] != "") {
            //si el pago tiene los campos de sucursal, do y sufijo digitados se hace la comparacion con estos campos
            if ($xRMdo['sucidxxx'] == $_POST['cSucIdOri'] && $xRMdo['docidxxx'] == $_POST['cDocNroOri'] && $xRMdo['docsufxx'] == $_POST['cDocSufOri']) {
              $nIncluir = 1;
            }
          } else {
            //Comparando por el centro de costo
            if ($xRMdo['ccoidxxx'] == $vTramites['ccoidxxx']) {
              $nIncluir = 1;
            }
          }

          if ($nIncluir == 1) {
            $xRMdo['comvlr01'] = ($xRMdo['commovxx'] == "D") ? $xRMdo['comvlr01'] : ($xRMdo['comvlr01']*-1);
            $xRMdo['comvlr02'] = ($xRMdo['commovxx'] == "D") ? $xRMdo['comvlr02'] : ($xRMdo['comvlr02']*-1);
            $xRMdo['comvlrxx'] = ($xRMdo['commovxx'] == "D") ? $xRMdo['comvlrxx'] : ($xRMdo['comvlrxx']*-1);
            $mMovCon[count($mMovCon)] = $xRMdo;
          }
        }
      }

      //Cruzando Ajustes
      $mMovConCruce = array();
      $mMovConNuevo = array();
      for ($i=0; $i < count($mMovCon); $i++) { 
        $nBorrar = 0;
        if($mMovCon[$i]['comidc2x'] != "" && $mMovCon[$i]['comcodc2'] != "" && $mMovCon[$i]['comcscc2'] != "" && $mMovCon[$i]['comseqc2'] != ""){
          //El documento cruce es:
          // Id comprobante
          // Codigo Comprobante
          // Consecutivo comprobante
          // Secuencia Comprobante
          // Concepto Comprobante
          // Cliente
          // Proveedor
          // Sucursal
          // Do
          // Sufijo
          $cCruce2  = $mMovCon[$i]['comidc2x']."~";
          $cCruce2 .= $mMovCon[$i]['comcodc2']."~";
          $cCruce2 .= $mMovCon[$i]['comcscc2']."~";
          $cCruce2 .= $mMovCon[$i]['comseqc2']."~";
          $cCruce2 .= $mMovCon[$i]['ctoidxxx']."~";
          $cCruce2 .= $mMovCon[$i]['teridxxx']."~";
          $cCruce2 .= $mMovCon[$i]['terid2xx']."~";
          $cCruce2 .= $mMovCon[$i]['sucidxxx']."~";
          $cCruce2 .= $mMovCon[$i]['docidxxx']."~";
          $cCruce2 .= $mMovCon[$i]['docsufxx'];
          for ($k=0; $k < count($mMovCon); $k++) {
            $cCruce1  = $mMovCon[$k]['comidxxx']."~";
            $cCruce1 .= $mMovCon[$k]['comcodxx']."~";
            $cCruce1 .= $mMovCon[$k]['comcscxx']."~";
            $cCruce1 .= $mMovCon[$k]['comseqxx']."~";
            $cCruce1 .= $mMovCon[$k]['ctoidxxx']."~";
            $cCruce1 .= $mMovCon[$k]['teridxxx']."~";
            $cCruce1 .= $mMovCon[$k]['terid2xx']."~";
            $cCruce1 .= $mMovCon[$k]['sucidxxx']."~";
            $cCruce1 .= $mMovCon[$k]['docidxxx']."~";
            $cCruce1 .= $mMovCon[$k]['docsufxx'];
            if($cCruce2 == $cCruce1){
              $nBorrar = 1;
              $mMovCon[$k]['comvlr01'] += $mMovCon[$i]['comvlr01'];
              $mMovCon[$k]['comvlr02'] += $mMovCon[$i]['comvlr02'];
              $mMovCon[$k]['comvlrxx'] += $mMovCon[$i]['comvlrxx'];
              if($mMovCon[$k]['comvlrxx'] == 0){
                $nInd_mMovConCruce = count($mMovConCruce);
                $mMovConCruce[$nInd_mMovConCruce] = $k;
              }
            }
          }
        }

        if($nBorrar == 1){
          $nInd_mMovConCruce = count($mMovConCruce);
          $mMovConCruce[$nInd_mMovConCruce] = $i;
        }
      }

      //Documentos autorizados
      $vComUsr   = f_Explode_Array($vUsrDat['USRDOCTR'],"|","~");
      $vPermisos = array();
      for ($nC=0; $nC<count($vComUsr);$nC++) {
        if ($vComUsr[$nC][0] != "") {
          $vPermisos[count($vPermisos)] = $vComUsr[$nC][0]."~".$vComUsr[$nC][1];
        }
      }

      for ($i=0; $i < count($mMovCon); $i++) {
        //verificando que el regisro no haya sido anulado con un ajsute
        //y que el usuario tenga permisos sobre el comprobante
        if(!in_array($i, $mMovConCruce) && in_array($mMovCon[$i]['comidxxx']."~".$vTipCom["{$mMovCon[$i]['comidxxx']}~{$mMovCon[$i]['comcodxx']}"], $vPermisos)){
          //Movimiento
          $mMovCon[$i]['commovxx'] = ($mMovCon[$i]['comvlrxx'] > 0) ? "D" : "C";

          $nInd_mMovConNuevo = count($mMovConNuevo);
          $mMovConNuevo[$nInd_mMovConNuevo] = $mMovCon[$i];
        }
      }
      $mMovCon = $mMovConNuevo;

      //Cantidad total de pagos a terceros y anticipos no facturados
      $nContador = 0;

      // Comprobantes de pago impuestos
      $qComPagImp  = "SELECT comidxxx, comcodxx ";
      $qComPagImp .= "FROM $cAlfa.fpar0117 ";
      $qComPagImp .= "WHERE ";
      $qComPagImp .= "comtipxx = \"PAGOIMPUESTOS\" AND ";
      $qComPagImp .= "regestxx = \"ACTIVO\"";
      $xComPagImp  = f_MySql("SELECT","",$qComPagImp,$xConexion01,"");
      //f_Mensaje(__FILE__,__LINE__,$qComPagImp." ~ ".mysql_num_rows($xComPagImp));
      $vComPagImp  = array();
      while ($xRCPI = mysql_fetch_assoc($xComPagImp)) {
        $vComPagImp[] = $xRCPI['comidxxx']."~".$xRCPI['comcodxx'];
      }

      for ($nA=0; $nA<count($mMovCon); $nA++) {
        if ($mMovCon[$nA]['comfacxx'] == "") { 
          $nContador++;

          #Busco nombre del cliente
          if (in_array("{$mMovCon[$nA]['teridxxx']}",$vClientes) == false) {
            $vClientes = "{$mMovCon[$nA]['teridxxx']}";

            $qCliNom  = "SELECT ";
            $qCliNom .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
            $qCliNom .= "FROM $cAlfa.SIAI0150 ";
            $qCliNom .= "WHERE ";
            $qCliNom .= "CLIIDXXX = \"{$mMovCon[$nA]['teridxxx']}\" LIMIT 0,1";
            $xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
            // echo $qCliNom."~".mysql_num_rows($xCliNom)."<br>";
            if (mysql_num_rows($xCliNom) > 0) {
              $xRCN = mysql_fetch_array($xCliNom);
              $xRDC['clinomxx'] = $xRCN['clinomxx'];
            } else {
              $xRDC['clinomxx'] = "SIN NOMBRE";
            }
            $mClientes["{$mMovCon[$nA]['teridxxx']}"] = $xRDC['clinomxx'];
          }

          #Busco nombre proveedor
          if (in_array("{$mMovCon[$nA]['terid2xx']}",$vClientes) == false) {
            $vClientes = "{$mMovCon[$nA]['terid2xx']}";

            $qCliNom  = "SELECT ";
            $qCliNom .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
            $qCliNom .= "FROM $cAlfa.SIAI0150 ";
            $qCliNom .= "WHERE ";
            $qCliNom .= "CLIIDXXX = \"{$mMovCon[$nA]['terid2xx']}\" LIMIT 0,1";
            $xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
            // echo $qCliNom."~".mysql_num_rows($xCliNom)."<br>";
            if (mysql_num_rows($xCliNom) > 0) {
              $xRCN = mysql_fetch_array($xCliNom);
              $xRDC['clinomxx'] = $xRCN['clinomxx'];
            } else {
              $xRDC['clinomxx'] = "SIN NOMBRE";
            }
            $mClientes["{$mMovCon[$nA]['terid2xx']}"] = $xRDC['clinomxx'];
          }

          // Busco la descripcion del concepto
          $qCtoCon  = "SELECT $cAlfa.fpar0119.*,$cAlfa.fpar0115.* ";
          $qCtoCon .= "FROM $cAlfa.fpar0119,$cAlfa.fpar0115 ";
          $qCtoCon .= "WHERE ";
          $qCtoCon .= "$cAlfa.fpar0119.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
          $qCtoCon .= "$cAlfa.fpar0119.ctoidxxx = \"{$mMovCon[$nA]['ctoidxxx']}\" AND ";
          $qCtoCon .= "$cAlfa.fpar0119.pucidxxx = \"{$mMovCon[$nA]['pucidxxx']}\" LIMIT 0,1";
          $xCtoCon  = f_MySql("SELECT","",$qCtoCon,$xConexion01,"");
          //f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
          $nConAut = 0;
          if (mysql_num_rows($xCtoCon) > 0) {
            $vCtoCon = mysql_fetch_array($xCtoCon);
          } else {
            //Busco en la parametrica de Conceptos Contables Causaciones Automaticas
            $qCtoCon  = "SELECT $cAlfa.fpar0121.*,$cAlfa.fpar0115.* ";
            $qCtoCon .= "FROM $cAlfa.fpar0121,$cAlfa.fpar0115 ";
            $qCtoCon .= "WHERE ";
            $qCtoCon .= "$cAlfa.fpar0121.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
            $qCtoCon .= "$cAlfa.fpar0121.ctoidxxx = \"{$mMovCon[$nA]['ctoidxxx']}\" AND ";
            $qCtoCon .= "$cAlfa.fpar0121.pucidxxx = \"{$mMovCon[$nA]['pucidxxx']}\" LIMIT 0,1";
            $xCtoCon  = f_MySql("SELECT","",$qCtoCon,$xConexion01,"");
            //f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
            if (mysql_num_rows($xCtoCon) > 0) {
              $nConAut = 1;
              $vCtoCon = mysql_fetch_array($xCtoCon);
              $vCtoCon['ctoantxx'] = "NO";
            }
          }

          $vCtoCon['ctodesxx'] = ($vCtoCon['ctodesx'.strtolower($mMovCon[$nA]['comidxxx'])] != "") ? $vCtoCon['ctodesx'.strtolower($mMovCon[$nA]['comidxxx'])] : $vCtoCon['ctodesxx'];
          $vCtoCon['ctodesxx'] = ($vCtoCon['ctodesxx'] != "") ? $vCtoCon['ctodesxx'] : "CONCEPTO SIN DESCRIPCION";
        
          //Busco la sucursal ICA
          $mMovCon[$nA]['sucicaxx'] = "";
          if ($vCtoCon['ctoantxx'] != "SI" && in_array("{$mMovCon[$nA]['comidxxx']}~{$mMovCon[$nA]['comcodxx']}", $vComPagImp) == false) {
            $nAnioCab = substr($mMovCon[$nA]['comfecxx'], 0, 4);
            $qFcoc  = "SELECT comobs2x, comtrasx ";
            $qFcoc .= "FROM $cAlfa.fcoc$nAnioCab ";
            $qFcoc .= "WHERE ";
            $qFcoc .= "comidxxx = \"{$mMovCon[$nA]['comidxxx']}\" AND ";
            $qFcoc .= "comcodxx = \"{$mMovCon[$nA]['comcodxx']}\" AND ";
            $qFcoc .= "comcscxx = \"{$mMovCon[$nA]['comcscxx']}\" AND ";
            $qFcoc .= "comcsc2x = \"{$mMovCon[$nA]['comcsc2x']}\" LIMIT 0,1 ";
            $xFcoc = f_MySql("SELECT","",$qFcoc,$xConexion01,"");
            // echo $qFcoc."~".mysql_num_rows($xFcoc)."<br><br>";
            $vFcoc = mysql_fetch_array($xFcoc);

            //Trayendo datos de sucursal Auto Rete ICA y tipo de prorrateo
            //Si es un ajuste se pregunta por comobs3x, si es una P, se pregunta por comobs2x
            if ($mMovCon[$nA]['comidxxx'] == 'P' && $vFcoc['comobs2x'] != '') {
              $mAux = array();
              $mAux = explode("~",$vFcoc['comobs2x']);
              $mMovCon[$nA]['sucicaxx'] = $mAux[1];
            } elseif ($vFcoc['comtrasx'] == "SI") {
              $qFcod  = "SELECT sucicaxx ";
              $qFcod .= "FROM $cAlfa.fcod$nAnioCab ";
              $qFcod .= "WHERE ";
              $qFcod .= "comidxxx = \"{$mMovCon[$nA]['comidxxx']}\" AND ";
              $qFcod .= "comcodxx = \"{$mMovCon[$nA]['comcodxx']}\" AND ";
              $qFcod .= "comcscxx = \"{$mMovCon[$nA]['comcscxx']}\" AND ";
              $qFcod .= "comcsc2x = \"{$mMovCon[$nA]['comcsc2x']}\" AND ";
              $qFcod .= "comseqxx = \"{$mMovCon[$nA]['comseqxx']}\" LIMIT 0,1 ";
              $xFcod = f_MySql("SELECT","",$qFcod,$xConexion01,"");
              // echo $qFcod."~".mysql_num_rows($xFcod)."<br><br>";
              $vFcod = mysql_fetch_array($xFcod);
              if ($vFcod['sucicaxx'] != "") {
                $mMovCon[$nA]['sucicaxx'] = $vFcod['sucicaxx'];
              }
            }     
          }

          //Cuenta de anticipo o de comprobante de pago de impuestos o el concepto es de tributos
          $cAplSuc = ($vCtoCon['ctoantxx'] == "SI" || in_array("{$mMovCon[$nA]['comidxxx']}~{$mMovCon[$nA]['comcodxx']}", $vComPagImp) == true || $vCtoCon['ctoptaxl'] == "SI" || $vCtoCon['ctoptaxg'] == "SI" || $nConAut == 0) ? "SI" : "NO";

          //Id del registro
          $cId  = $mMovCon[$nA]['comidxxx']."~";                //[0]  Id Combropante
          $cId .= $mMovCon[$nA]['comcodxx']."~";                //[1]  Codigo Combprobante
          $cId .= $mMovCon[$nA]['comcscxx']."~";                //[2]  Consecutivo Uno
          $cId .= $mMovCon[$nA]['comcsc2x']."~";                //[3]  Consecutivo Dos
          $cId .= $mMovCon[$nA]['comseqxx']."~";                //[4]  Secuencia Comprobante
          $cId .= $mMovCon[$nA]['comfecxx']."~";                //[5]  Fecha Comprobante
          $cId .= abs($mMovCon[$nA]['comvlr01']+0)."~";         //[6]  Base
          $cId .= abs($mMovCon[$nA]['comvlr02']+0)."~";         //[7]  Iva
          $cId .= abs($mMovCon[$nA]['comvlrxx']+0)."~";         //[8]  Valor
          $cId .= $mMovCon[$nA]['commovxx']."~";                //[9]  Movimiento Concepto
          $cId .= $mMovCon[$nA]['ctoidxxx']."~";                //[10] Codigo Concepto
          $cId .= str_replace("~"," ",$vCtoCon['ctodesxx'])."~";//[11] Descripcion concepto
          $cId .= $cAplSuc;                                     //[12] Cuenta de anticipo o de comprobante de pago de impuestos
          $cCheck = true;
          ?>
          <script languaje = "javascript">
            parent.fmwork.f_Add_New_Row("<?php echo $cId ?>","<?php echo $cCheck ?>");

            parent.fmwork.document.getElementById('cTerId_'    +parent.fmwork.document.forms['frnav']['nSecuencia'].value).innerHTML = "<?php echo $mMovCon[$nA]['teridxxx'] ?>";
            parent.fmwork.document.getElementById('cTerNom_'   +parent.fmwork.document.forms['frnav']['nSecuencia'].value).innerHTML = "<?php echo $mClientes["{$mMovCon[$nA]['teridxxx']}"] ?>";
            parent.fmwork.document.getElementById('cCcoDes_'   +parent.fmwork.document.forms['frnav']['nSecuencia'].value).innerHTML = "<?php echo $vCenCos["{$mMovCon[$nA]['ccoidxxx']}"] ?>";
            parent.fmwork.document.getElementById('cComId_'    +parent.fmwork.document.forms['frnav']['nSecuencia'].value).innerHTML = "<?php echo $mMovCon[$nA]['comidxxx'] ?>";
            parent.fmwork.document.getElementById('cNumCom_'   +parent.fmwork.document.forms['frnav']['nSecuencia'].value).innerHTML = "<?php echo $mMovCon[$nA]['comcsc2x'] ?>";
            parent.fmwork.document.getElementById('cNumFac_'   +parent.fmwork.document.forms['frnav']['nSecuencia'].value).innerHTML = "<?php echo $mMovCon[$nA]['comcscxx'] ?>";
            parent.fmwork.document.getElementById('cComFec_'   +parent.fmwork.document.forms['frnav']['nSecuencia'].value).innerHTML = "<?php echo $mMovCon[$nA]['comfecxx'] ?>";
            parent.fmwork.document.getElementById('cCtoDes_'   +parent.fmwork.document.forms['frnav']['nSecuencia'].value).innerHTML = "<?php echo $vCtoCon['ctodesxx'] ?>";
            parent.fmwork.document.getElementById('cComVlr01_' +parent.fmwork.document.forms['frnav']['nSecuencia'].value).innerHTML = "<?php echo (abs($mMovCon[$nA]['comvlr01']) > 0) ? abs($mMovCon[$nA]['comvlr01']) : abs($mMovCon[$nA]['comvlrxx']) ?>";
            parent.fmwork.document.getElementById('cComVlr_'   +parent.fmwork.document.forms['frnav']['nSecuencia'].value).innerHTML = "<?php echo abs($mMovCon[$nA]['comvlrxx']) ?>";
            parent.fmwork.document.getElementById('cTerId2_'   +parent.fmwork.document.forms['frnav']['nSecuencia'].value).innerHTML = "<?php echo $mMovCon[$nA]['terid2xx'] ?>";
            parent.fmwork.document.getElementById('cTerNom2_'  +parent.fmwork.document.forms['frnav']['nSecuencia'].value).innerHTML = "<?php echo $mClientes["{$mMovCon[$nA]['terid2xx']}"] ?>";
            parent.fmwork.document.forms['frnav']['cSucIca_'   +parent.fmwork.document.forms['frnav']['nSecuencia'].value].value     = "<?php echo $mMovCon[$nA]['sucicaxx'] ?>";
            parent.fmwork.document.getElementById('cComMov_'   +parent.fmwork.document.forms['frnav']['nSecuencia'].value).innerHTML = "<?php echo $mMovCon[$nA]['commovxx'] ?>";

            //Si es una cuenta de anticipo, se inactiva el combo de sucursal ICA
            if ("<?php echo $cAplSuc ?>" == "SI" || "<?php echo $mMovCon[$nA]['sucicaxx'] ?>" != "") {
              parent.fmwork.document.forms['frnav']['cSucIca_'  +parent.fmwork.document.forms['frnav']['nSecuencia'].value].disabled = true;
            }
          </script>
        <?php }
      }

      //Cargando PCC
      //Validando que todos los pcc esten facturados
      for ($nA=0; $nA<count($mDatos['pccxxxxx']); $nA++) {
        if ($mDatos['pccxxxxx'][$nA]['comfacxx'] == "") {
          $nContador++;

        }
      }

      if ($nContador == 0) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "No Se Encontraron Pagos a Terceros, ni Anticipos sin Factura para el Tramite [{$_POST['cSucIdOri']}-{$_POST['cDocNroOri']}-{$_POST['cDocSufOri']}].\n";
      }

    }
  }
} else {
  $nSwitch = 1;
  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  $cMsj .= "Parametros Incompletos.\n";
}

if ($nSwitch == 1) {
  f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique");
}
?>

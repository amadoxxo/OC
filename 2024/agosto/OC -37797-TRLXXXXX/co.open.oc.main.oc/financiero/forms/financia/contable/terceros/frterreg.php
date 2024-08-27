<?php
  //set_time_limit(0);
  include("../../../../libs/php/utility.php");

  $cNomFile = "CargueTerceros_".$_COOKIE['kUsrId']."_".date("YmdHis").".xls";
  $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/".$cNomFile;
  if (file_exists($cFile)){
    unlink($cFile);
  }

  $fOp = fopen($cFile,'a');

  $cBuscar = ($_POST['cBuscar'] == "") ? "OR" : $_POST['cBuscar'];

  $cCad01  = "<table border=\"1\">";
    $cCad01  .= "<tr>";
      $cCad01 .= "<td style=\"font-weight: bold\">NIT</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">DIGITO VERIFICACION</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">CODIGO TIPO DE DOCUMENTO</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">TIPO DE PERSONA (PUBLICA - JURIDICA - NATURAL)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">RAZON SOCIAL</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">NOMBRE COMERCIAL</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">PRIMER APELLIDO</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">SEGUNDO APELLIDO</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">PRIMER NOMBRE</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">OTROS NOMBRES</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">CODIGO PAIS DOMICILIO FISCAL</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">CODIGO DEPARTAMENTO DOMICILIO FISCAL</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">CODIGO CIUDAD DOMICILIO FISCAL</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">TELEFONO DOMICILIO FISCAL</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">DIRECCION DOMICILIO FISCAL</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">CORREO ELECTRONICO</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">CODIGO PAIS CORRESPONDENCIA</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">CODIGO DEPARTAMENTO CORRESPONDENCIA</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">CODIGO CIUDAD CORRESPONDENCIA</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">DIRECCION CORRESPONDENCIA</td>";
      $cCad01 .= "<td style=\"font-weight: bold;background-color:red\">CLIENTE (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">PROVEEDOR-CLIENTE (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">PROVEEDOR-EMPRESA (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">PROVEEDOR-SOCIO (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">ENTIDAD FINANCIERA (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">PROVEEDOR-OTROS (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">EMPLEADO (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">VENDEDOR (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">VENDEDOR ASIGNADO</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">RESPONSABLE IVA REGIMEN COMUN (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">RESPONSABLE IVA REGIMEN SIMPLIFICADO (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">GRAN CONTRIBUYENTE (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">REGIMEN SIMPLE TRIBUTARIO (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">NO RESIDENTE EN EL PAIS  (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">NO RESIDENTE EN EL PAIS - APLICA IVA (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">NO RESIDENTE EN EL PAIS - APLICA GMF (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">NO RESIDENTE EN EL PAIS - NO SUJETO RETEFTE POR RENTA (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">AUTORETENEDOR EN RENTA (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">AUTORETENEDOR DE IVA (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">AUTORETENEDOR DE ICA (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">CODIGO SUCURSALES AUTORETENEDOR DE ICA (SEPARADAS POR COMA)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">AUTORETENEDOR DE CREE (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">NO SUJETO RETEFTE POR RENTA (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">NO SUJETO RETEFTE POR IVA (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">NO SUJETO RETENCION CREE (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">NO SUJETO A RETENCION ICA (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">AGENTE RETENEDOR EN RENTA (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">AGENTE RETENEDOR EN IVA (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">AGENTE RETENEDOR CREE (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">AGENTE RETENEDOR ICA (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">CODIGO SUCURSALES AGENTE RETENEDOR ICA (SEPARADAS POR COMA)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">PROVEEDOR COMERCIALIZADORA INTERNACIONAL (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">NO SUJETO A EXPEDIR FACTURA DE VENTA O DOCUMENTO EQUIVALENTE (SI - NO)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">CODIGO POSTAL DIRECCION FISCAL</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">CODIGO POSTAL DIRECCION CORRESPONDENCIA</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">RESPONSABILIDAD FISCAL (SEPARADAS POR COMA)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">RESPONSABILIDAD TRIBUTO (SEPARADAS POR COMA)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">CORREOS NOTIFICACION (SEPARADOS POR COMA Y SIN ESPACIOS)</td>";
      $cCad01 .= "<td style=\"font-weight: bold\">MATRICULA MERCANTIL</td>";

      if ($cAlfa == "DHLEXPRE" || $cAlfa == "DEDHLEXPRE" || $cAlfa == "TEDHLEXPRE") {
        $cCad01 .= "<td style=\"font-weight: bold\">CUENTA IMP CASH</td>";
        $cCad01 .= "<td style=\"font-weight: bold\">ESTADO CUENTA IMP CASH</td>";
        $cCad01 .= "<td style=\"font-weight: bold\">CUENTA IMP CREDITO</td>";
        $cCad01 .= "<td style=\"font-weight: bold\">ESTADO CUENTA IMP CREDITO</td>";
        $cCad01 .= "<td style=\"font-weight: bold\">ID BANCO</td>";
        $cCad01 .= "<td style=\"font-weight: bold\">NOMBRE BANCO</td>";
        $cCad01 .= "<td style=\"font-weight: bold\">TIPO CUENTA</td>";
        $cCad01 .= "<td style=\"font-weight: bold\">NUMERO DE CUENTA</td>";
        $cCad01 .= "<td style=\"font-weight: bold\">ESTADO CUENTA</td>";
      }

      $cCad01 .= "<td style=\"font-weight: bold;background-color:red;text-align: center\">FECHA DE CREACION</td>";
      $cCad01 .= "<td style=\"font-weight: bold;background-color:red\">ESTADO</td>";
    $cCad01  .= "</tr>";

  fwrite($fOp,$cCad01);

  /**
   * Armando condiciones del query
   * la unica condicion obligatoria es la clasificacion y el estado
   */
  $qSqlTip = "";

  if ($_POST['vChCli'] == "SI") {
    $qSqlTip .= "CLICLIXX = \"SI\" $cBuscar ";
  }

  if ($_POST['vChProC'] == "SI") {
    $qSqlTip .= "CLIPROCX = \"SI\" $cBuscar ";
  }

  if ($_POST['vChSoc'] == "SI") {
    $qSqlTip .= "CLISOCXX = \"SI\" $cBuscar ";
  }

  if ($_POST['vChProE'] == "SI") {
    $qSqlTip .= "CLIPROEX = \"SI\" $cBuscar ";
  }

  if ($_POST['vChEmp'] == "SI") {
    $qSqlTip .= "CLIEMPXX = \"SI\" $cBuscar ";
  }

  if ($_POST['vChEfi'] == "SI") {
    $qSqlTip .= "CLIEFIXX = \"SI\" $cBuscar ";
  }

  if ($_POST['vChOtr'] == "SI") {
    $qSqlTip .= "CLIOTRXX = \"SI\" $cBuscar ";
  }

  if ($_POST['vChCliVenCo'] == "SI") {
    $qSqlTip .= "CLIVENCO = \"SI\" $cBuscar ";
  }

  $qSqlAux = "";

  if ($_POST['cTpeId'] != ""){
    $qSqlAux .= "CLITPERX = \"{$_POST['cTpeId']}\" $cBuscar ";
  }

  if ($_POST['cTdiId'] != ""){
    $qSqlAux .= "TDIIDXXX = \"{$_POST['cTdiId']}\" $cBuscar ";
  }

  if ($_POST['cTerId'] != ""){
    if ($_POST['oExcTerId'] == "SI"){
      $qSqlAux .="CLIIDXXX = \"{$_POST['cTerId']}\" $cBuscar ";
    } else{
      $qSqlAux .="CLIIDXXX LIKE \"%{$_POST['cTerId']}%\" $cBuscar ";
    }
  }

  if ($_POST['cTerNom'] != ""){
    if ($_POST['oExcTerNom'] == "SI"){
      $qSqlAux .="CLINOMXX = \"{$_POST['cTerNom']}\" $cBuscar ";
    } else{
      $qSqlAux .="CLINOMXX LIKE \"%{$_POST['cTerNom']}%\" $cBuscar ";
    }
  }

  if ($_POST['cTerNomC'] != ""){
    if ($_POST['oExcTerNomC'] == "SI"){
      $qSqlAux .="CLINOMCX = \"{$_POST['cTerNomC']}\" $cBuscar ";
    } else{
      $qSqlAux .="CLINOMCX LIKE \"%{$_POST['cTerNomC']}%\" $cBuscar ";
    }
  }

  if ($_POST['cPaiId'] != ""){
    $qSqlAux .= "PAIIDXXX = \"{$_POST['cPaiId']}\" $cBuscar ";
  }

  if ($_POST['cDepId'] != ""){
    $qSqlAux .= "DEPIDXXX = \"{$_POST['cDepId']}\" $cBuscar ";
  }

  if ($_POST['cCiuId'] != ""){
    $qSqlAux .= "CIUIDXXX = \"{$_POST['cCiuId']}\" $cBuscar ";
  }

  if ($_POST['cGruId'] != ""){
    $qSqlAux .= "GRUIDXXX = \"{$_POST['cGruId']}\" $cBuscar ";
  }

  if ($_POST['cPaiId1'] != ""){
    $qSqlAux .= "PAIID3XX = \"{$_POST['cPaiId1']}\" $cBuscar ";
  }

  if ($_POST['cDepId1'] != ""){
    $qSqlAux .= "DEPID3XX = \"{$_POST['cDepId1']}\" $cBuscar ";
  }

  if ($_POST['cCiuId1'] != ""){
    $qSqlAux .= "CIUID3XX = \"{$_POST['cCiuId1']}\" $cBuscar ";
  }

  if ($_POST['cTerFPa'] != ""){
    $qSqlAux .= "CLIFORPX = \"{$_POST['cTerFPa']}\" $cBuscar ";
  }

  if ($_POST['cTerMedP'] != ""){
    $qSqlAux .= "CLIMEDPX = \"{$_POST['cTerMedP']}\" $cBuscar ";
  }

  if ($_POST['oCliReIva'] == "SI"){
    $qSqlAux .="CLIREIVA = \"{$_POST['oCliReIva']}\" $cBuscar ";
  }

  if ($_POST['oCliReg'] == "SI"){
    $qSqlAux .="CLIRECOM = \"{$_POST['oCliReg']}\" $cBuscar ";
  }

  if ($_POST['oCliReSim'] == "SI"){
    $qSqlAux .="CLIRESIM = \"{$_POST['oCliReSim']}\" $cBuscar ";
  }

  if ($_POST['oCliGc'] == "SI"){
    $qSqlAux .="CLIGCXXX = \"{$_POST['oCliGc']}\" $cBuscar ";
  }

  if ($_POST['oCliNrp'] == "SI"){
    $qSqlAux .="CLINRPXX = \"{$_POST['oCliNrp']}\" $cBuscar ";
  }

  if ($_POST['oCliNrpai'] == "SI"){
    $qSqlAux .="CLINRPAI = \"{$_POST['oCliNrpai']}\" $cBuscar ";
  }

  if ($_POST['oCliNrpif'] == "SI"){
    $qSqlAux .="CLINRPIF = \"{$_POST['oCliNrpif']}\" $cBuscar ";
  }

  if ($_POST['oCliNrpNsr'] == "SI"){
    $qSqlAux .="CLINRNSR = \"{$_POST['oCliNrpNsr']}\" $cBuscar ";
  }

  if ($_POST['oCliAr'] == "SI"){
    $qSqlAux .="CLIARXXX = \"{$_POST['oCliAr']}\" $cBuscar ";
  }

  if ($_POST['oCliArAre'] == "SI"){
    $qSqlAux .="CLIARARE = \"{$_POST['oCliArAre']}\" $cBuscar ";
  }

  if ($_POST['oCliArAiv'] == "SI"){
    $qSqlAux .="CLIARAIV = \"{$_POST['oCliArAiv']}\" $cBuscar ";
  }

  if ($_POST['oCliArAic'] == "SI"){
    $qSqlAux .="CLIARAIC = \"{$_POST['oCliArAic']}\" $cBuscar ";
  }

  if ($_POST['oCliArAcr'] == "SI"){
    $qSqlAux .="CLIARACR = \"{$_POST['oCliArAcr']}\" $cBuscar ";
  }

  if ($_POST['cCliArAis'] != ""){
    $qSqlAux .= "CLIARAIS like \"%{$_POST['cCliArAis']}%\" $cBuscar ";
  }

  if ($_POST['oCliNsrr'] == "SI"){
    $qSqlAux .="CLINSRRX = \"{$_POST['oCliNsrr']}\" $cBuscar ";
  }

  if ($_POST['oCliNsriv'] == "SI"){
    $qSqlAux .="CLINSRIV = \"{$_POST['oCliNsriv']}\" $cBuscar ";
  }

  if ($_POST['oCliNsrcr'] == "SI"){
    $qSqlAux .="CLINSRCR = \"{$_POST['oCliNsrcr']}\" $cBuscar ";
  }

  if ($_POST['oCliArr'] == "SI"){
    $qSqlAux .="CLIARRXX = \"{$_POST['oCliArr']}\" $cBuscar ";
  }

  if ($_POST['oCliAriva'] == "SI"){
    $qSqlAux .="CLIARIVA = \"{$_POST['oCliAriva']}\" $cBuscar ";
  }

  if ($_POST['oCliArcr'] == "SI"){
    $qSqlAux .="CLIARCRX = \"{$_POST['oCliArcr']}\" $cBuscar ";
  }

  if ($_POST['oCliArrI'] == "SI"){
    $qSqlAux .="CLIARRIX = \"{$_POST['oCliArrI']}\" $cBuscar ";
  }

  if ($_POST['cCliArrIs'] != ""){
    $qSqlAux .="CLIARRIS = \"%{$_POST['cCliArrIs']}%\" $cBuscar ";
  }

  if ($_POST['oCliNsrri'] == "SI"){
    $qSqlAux .="CLINSRRI = \"{$_POST['oCliNsrri']}\" $cBuscar ";
  }

  if ($_POST['oCliPci'] == "SI"){
    $qSqlAux .="CLIPCIXX = \"{$_POST['oCliPci']}\" $cBuscar ";
  }

  if ($_POST['oCliNsOfe'] == "SI"){
    $qSqlAux .="CLINSOFE = \"{$_POST['oCliNsOfe']}\" $cBuscar ";
  }

  $qSqlAux = substr($qSqlAux, 0, (($cBuscar == "OR") ? -4 : -5));
  $qSqlTip = substr($qSqlTip, 0, (($cBuscar == "OR") ? -4 : -5));

  $qTerceros  = "SELECT * ";
  $qTerceros .= "FROM $cAlfa.SIAI0150 ";
  $qTerceros .= "WHERE  ";
  if ($qSqlTip != "") {
    $qTerceros .= "(".$qSqlTip.") AND ";
  }

  if ($qSqlAux != "") {
    $qTerceros .= "(".$qSqlAux.") AND ";
  }
  switch($_POST['cEstado']) {
    case "ACTIVO":
      $qTerceros .= "REGESTXX = \"ACTIVO\" ";
    break;
    case "INACTIVO":
      $qTerceros .= "REGESTXX = \"INACTIVO\" ";
    break;
    default:
      $qTerceros .= "REGESTXX IN (\"ACTIVO\",\"INACTIVO\") ";
    break;
  }
  $xTerceros = f_MySql("SELECT","",$qTerceros,$xConexion01,"");
  // f_mensaje(__FILE__,__LINE__,$qTerceros."~".mysql_num_rows($xTerceros));

  if (mysql_num_rows($xTerceros) > 0) {
    while ($xRT = mysql_fetch_array($xTerceros)) {
      switch ($xRT['CLITPERX']) {
        case "PUBLICA":
        case "JURIDICA":
          $xRT['CLINOMXX'] = ($xRT['CLINOMXX'] != "") ? $xRT['CLINOMXX'] : trim($xRT['CLIAPE1X']." ".$xRT['CLIAPE2X']." ".$xRT['CLINOM1X']." ".$xRT['CLINOM2X']);
          $xRT['CLIAPE1X'] = "";
          $xRT['CLIAPE2X'] = "";
          $xRT['CLINOM1X'] = "";
          $xRT['CLINOM2X'] = "";
        break;
        default:
          $xRT['CLIAPE1X'] = ($xRT['CLIAPE1X'] != "") ? $xRT['CLIAPE1X'] : $xRT['CLINOMXX'];
          $xRT['CLINOMXX'] = "";
          $xRT['CLINOMCX'] = "";
        break;
      }

      $xRT['CLIVENXX'] = trim(str_replace("~", ",", $xRT['CLIVENXX']),",");

      if ($cAlfa == "DHLEXPRE" || $cAlfa == "DEDHLEXPRE" || $cAlfa == "TEDHLEXPRE") {
        $cDesBanc  = '';
        $qDatBanc  = "SELECT bandesxx ";
        $qDatBanc .= "FROM $cAlfa.fpar0124 ";
        $qDatBanc .= "WHERE banidxxx =\"{$xRT['CLIBANID']}\" LIMIT 0,1";
        $xDatBanc  = f_MySql("SELECT","",$qDatBanc,$xConexion01,"");
        if (mysql_num_rows($xDatBanc) > 0) {
          $vDatBanc = mysql_fetch_array($xDatBanc);
          $cDesBanc = $vDatBanc['bandesxx'];
        }

        // Logica para obtener las cuentas IMP Cash y Credito para pintarlas separadas por coma
        $cCtaCash = ""; 
        $cEstCash = "";
        if ($xRT['CLIIMPCS'] != "") {
          $vCtaCash = f_Explode_Array($xRT['CLIIMPCS'], "|", "~");
          for ($i=0; $i < count($vCtaCash); $i++) {
            $cCtaCash .= $vCtaCash[$i][0].",";
            $cEstCash .= $vCtaCash[$i][1].",";

          }
        }

        $cCtaCred = "";
        $cEstCred = "";
        if ($xRT['CLIIMPCR'] != "") {
          $vCtaCred = f_Explode_Array($xRT['CLIIMPCR'], "|", "~");

          for ($i=0; $i < count($vCtaCred); $i++) {
            $cCtaCred .= $vCtaCred[$i][0].",";
            $cEstCred .= $vCtaCred[$i][1].",";
          }
        }
      }

      $cCad01  = "<tr>";
        $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['CLIIDXXX']}</td>"; //NIT
        $cCad01 .= "<td style=\"mso-number-format:\@\">". gendv($xRT['CLIIDXXX']) ."</td>"; //DIGITO VERIFICACION
        $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['TDIIDXXX']}</td>"; //CODIGO TIPO DE DOCUMENTO
        $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['CLITPERX']}</td>"; //TIPO DE PERSONA (PUBLICA - JURIDICA - NATURAL)
        $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['CLINOMXX']}</td>"; //RAZON SOCIAL
        $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['CLINOMCX']}</td>"; //NOMBRE COMERCIAL
        $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['CLIAPE1X']}</td>"; //PRIMER APELLIDO
        $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['CLIAPE2X']}</td>"; //SEGUNDO APELLIDO
        $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['CLINOM1X']}</td>"; //PRIMER NOMBRE
        $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['CLINOM2X']}</td>"; //OTROS NOMBRES
        $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['PAIIDXXX']}</td>"; //CODIGO PAIS DOMICILIO FISCAL
        $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['DEPIDXXX']}</td>"; //CODIGO DEPARTAMENTO DOMICILIO FISCAL
        $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['CIUIDXXX']}</td>"; //CODIGO CIUDAD DOMICILIO FISCAL
        $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['CLITELXX']}</td>"; //TELEFONO DOMICILIO FISCAL
        $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['CLIDIRXX']}</td>"; //DIRECCION DOMICILIO FISCAL
        $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['CLIEMAXX']}</td>"; //CORREO ELECTRONICO
        $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['PAIID3XX']}</td>"; //CODIGO PAIS CORRESPONDENCIA
        $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['DEPID3XX']}</td>"; //CODIGO DEPARTAMENTO CORRESPONDENCIA
        $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['CIUID3XX']}</td>"; //CODIGO CIUDAD CORRESPONDENCIA
        $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['CLIDIR3X']}</td>"; //DIRECCION CORRESPONDENCIA
        $cCad01 .= "<td style=\"background-color:red;mso-number-format:\@\">".(($xRT['CLICLIXX'] == "SI") ? $xRT['CLICLIXX'] : "")."</td>"; //CLIENTE (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLIPROCX'] == "SI") ? $xRT['CLIPROCX'] : "")."</td>"; //PROVEEDOR-CLIENTE (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLIPROEX'] == "SI") ? $xRT['CLIPROEX'] : "")."</td>"; //PROVEEDOR-EMPRESA (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLISOCXX'] == "SI") ? $xRT['CLISOCXX'] : "")."</td>"; //PROVEEDOR-SOCIO (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLIEFIXX'] == "SI") ? $xRT['CLIEFIXX'] : "")."</td>"; //ENTIDAD FINANCIERA (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLIOTRXX'] == "SI") ? $xRT['CLIOTRXX'] : "")."</td>"; //PROVEEDOR-OTROS (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLIEMPXX'] == "SI") ? $xRT['CLIEMPXX'] : "")."</td>"; //EMPLEADO (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLIVENCO'] == "SI") ? $xRT['CLIVENCO'] : "")."</td>"; //VENDEDOR (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".$xRT['CLIVENXX']."</td>"; //VENDEDOR ASIGNADO
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLIRECOM'] == "SI") ? $xRT['CLIRECOM'] : "")."</td>"; //RESPONSABLE IVA REGIMEN COMUN (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLIRESIM'] == "SI") ? $xRT['CLIRESIM'] : "")."</td>"; //RESPONSABLE IVA REGIMEN SIMPLIFICADO (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLIGCXXX'] == "SI") ? $xRT['CLIGCXXX'] : "")."</td>"; //GRAN CONTRIBUYENTE (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLIREGST'] == "SI") ? $xRT['CLIREGST'] : "")."</td>"; //REGIMEN SIMPLE TRIBUTARIO (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLINRPXX'] == "SI") ? $xRT['CLINRPXX'] : "")."</td>"; //NO RESIDENTE EN EL PAIS  (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLINRPAI'] == "SI") ? $xRT['CLINRPAI'] : "")."</td>"; //NO RESIDENTE EN EL PAIS - APLICA IVA (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLINRPIF'] == "SI") ? $xRT['CLINRPIF'] : "")."</td>"; //NO RESIDENTE EN EL PAIS - APLICA GMF (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLINRNSR'] == "SI") ? $xRT['CLINRNSR'] : "")."</td>"; //NO RESIDENTE EN EL PAIS - NO SUJETO RETEFTE POR RENTA (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLIARARE'] == "SI") ? $xRT['CLIARARE'] : "")."</td>"; //AUTORETENEDOR EN RENTA (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLIARAIV'] == "SI") ? $xRT['CLIARAIV'] : "")."</td>"; //AUTORETENEDOR DE IVA (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLIARAIC'] == "SI") ? $xRT['CLIARAIC'] : "")."</td>"; //AUTORETENEDOR DE ICA (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".trim(str_replace("~", ",", $xRT['CLIARAIS']),",")."</td>"; //CODIGO SUCURSALES AUTORETENEDOR DE ICA (SEPARADAS POR COMA)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLIARACR'] == "SI") ? $xRT['CLIARACR'] : "")."</td>"; //AUTORETENEDOR DE CREE (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLINSRRX'] == "SI") ? $xRT['CLINSRRX'] : "")."</td>"; //NO SUJETO RETEFTE POR RENTA (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLINSRIV'] == "SI") ? $xRT['CLINSRIV'] : "")."</td>"; //NO SUJETO RETEFTE POR IVA (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLINSRCR'] == "SI") ? $xRT['CLINSRCR'] : "")."</td>"; //NO SUJETO RETENCION CREE (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLINSRRI'] == "SI") ? $xRT['CLINSRRI'] : "")."</td>"; //NO SUJETO A RETENCION ICA (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLIARRXX'] == "SI") ? $xRT['CLIARRXX'] : "")."</td>"; //AGENTE RETENEDOR EN RENTA (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLIARIVA'] == "SI") ? $xRT['CLIARIVA'] : "")."</td>"; //AGENTE RETENEDOR EN IVA (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLIARCRX'] == "SI") ? $xRT['CLIARCRX'] : "")."</td>"; //AGENTE RETENEDOR CREE (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLIARRIX'] == "SI") ? $xRT['CLIARRIX'] : "")."</td>"; //AGENTE RETENEDOR ICA (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".trim(str_replace("~", ",", $xRT['CLIARRIS']),",")."</td>"; //CODIGO SUCURSALES AGENTE RETENEDOR ICA (SEPARADAS POR COMA)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLIPCIXX'] == "SI") ? $xRT['CLIPCIXX'] : "")."</td>"; //PROVEEDOR COMERCIALIZADORA INTERNACIONAL(SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">".(($xRT['CLINSOFE'] == "SI") ? $xRT['CLINSOFE'] : "")."</td>"; //NO SUJETO A EXPEDIR FACTURA DE VENTA O DOCUMENTO EQUIVALENTE (SI - NO)
        $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['CLICPOSX']}</td>"; //CODIGO POSTAL DIRECCION FISCAL
        $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['CLICPOS3']}</td>"; //CODIGO POSTAL DIRECCION CORRESPONDENCIA
        $cCad01 .= "<td style=\"mso-number-format:\@\">".trim(str_replace("~", ",", $xRT['CLIRESFI']),",")."</td>"; //RESPONSABILIDAD FISCAL
        $cCad01 .= "<td style=\"mso-number-format:\@\">".trim(str_replace("~", ",", $xRT['CLITRIBU']),",")."</td>"; //RESPONSABILIDAD TRIBUTO
        $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['CLIPCECN']}</td>"; //CORREOS NOTIFICACION (SEPARADOS POR COMA Y SIN ESPACIOS)
        $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['CLIMMERX']}</td>"; //MATRICULA MERCANTIL

        if ($cAlfa == "DHLEXPRE" || $cAlfa == "DEDHLEXPRE" || $cAlfa == "TEDHLEXPRE") {
          $cCad01 .= "<td style=\"mso-number-format:\@\">".trim($cCtaCash,',')."</td>"; //CUENTA IMP CASH
          $cCad01 .= "<td style=\"mso-number-format:\@\">".trim($cEstCash,',')."</td>"; //ESTADO CUENTA
          $cCad01 .= "<td style=\"mso-number-format:\@\">".trim($cCtaCred,',')."</td>"; //CUENTA IMP CREDITO
          $cCad01 .= "<td style=\"mso-number-format:\@\">".trim($cEstCred,',')."</td>"; //ESTADO CUENTA
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['CLIBANID']}</td>"; //ID BANCO
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$cDesBanc}</td>"; //NOMBRE BANCO
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['CLITIPCU']}</td>"; //TIPO CUENTA
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['CLINUMCU']}</td>"; //NUMERO DE CUENTA
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['CLIESTCU']}</td>"; //ESTADO CUENTA
        }

        $cCad01 .= "<td style=\"background-color:red;mso-number-format:yyyy-mm-dd;text-align: center\">{$xRT['REGFECXX']}</td>"; //Fecha de Creacion				
        $cCad01 .= "<td style=\"background-color:red\">{$xRT['REGESTXX']}</td>"; //ESTADO
      $cCad01 .= "</tr>";
      fwrite($fOp,$cCad01);
    }
  }
  $cCad01 = "</table>";
  fwrite($fOp,$cCad01);
  fclose($fOp);
  if (file_exists($cFile)){
    chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));

    $cDownLoadFilename = $cDownLoadFilename !== null ? $cDownLoadFilename : basename($cFile);
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . $cDownLoadFilename);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($cFile));

    ob_clean();
    flush();
    readfile($cFile);
    exit;
  } else {
    f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
  }
?>

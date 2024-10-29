<?php
  namespace openComex;
  /**
   * Generar Impreso de Excel - Certificación.
   * --- Descripcion: Permite Generar Impreso de Excel con la Información de la Certificación.
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
   * @package opencomex
   * @version 001
   */

  include("../../../../../financiero/libs/php/utility.php");

  /**
   * Variables de control de errores.
   * 
   * @var int
   */
  $nSwitch = 0;

  /**
   * Variable para almacenar los mensajes de error.
   * 
   * @var string
   */
  $cMsj = "\n";

  // Consulta la información de cabecera de la certificación
  $vCertificacion  = array();
  $qCertificacion  = "SELECT $cAlfa.lcca$cAnio.*, ";
  $qCertificacion .= "IF($cAlfa.lpar0150.clinomxx != \"\",$cAlfa.lpar0150.clinomxx,(TRIM(CONCAT($cAlfa.lpar0150.clinomxx,\" \",$cAlfa.lpar0150.clinom1x,\" \",$cAlfa.lpar0150.clinom2x,\" \",$cAlfa.lpar0150.cliape1x,\" \",$cAlfa.lpar0150.cliape2x)))) AS clinomxx, ";
  $qCertificacion .= "$cAlfa.lpar0150.clisapxx, ";
  $qCertificacion .= "$cAlfa.lpar0008.cdisapxx, ";
  $qCertificacion .= "$cAlfa.lpar0008.cdidesxx ";
  $qCertificacion .= "FROM $cAlfa.lcca$cAnio ";
  $qCertificacion .= "LEFT JOIN $cAlfa.lpar0150 ON $cAlfa.lcca$cAnio.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
  $qCertificacion .= "LEFT JOIN $cAlfa.lpar0008 ON $cAlfa.lcca$cAnio.cdisapxx = $cAlfa.lpar0008.cdisapxx ";
  $qCertificacion .= "WHERE ";
  $qCertificacion .= "$cAlfa.lcca$cAnio.ceridxxx = \"$cCerId\" LIMIT 0,1";
  $xCertificacion  = f_MySql("SELECT","",$qCertificacion,$xConexion01,"");
  if (mysql_num_rows($xCertificacion) > 0) {
    $vCertificacion = mysql_fetch_array($xCertificacion);

    // Consulta la información de la MIF
    $cAnioMif = $vCertificacion['mifidano'];
    $vMatriz  = array();
    $qMatriz  = "SELECT ";
    $qMatriz .= "$cAlfa.lmca$cAnioMif.* ";
    $qMatriz .= "FROM $cAlfa.lmca$cAnioMif ";
    $qMatriz .= "WHERE ";
    $qMatriz .= "$cAlfa.lmca$cAnioMif.mifidxxx = {$vCertificacion['mifidxxx']} LIMIT 0,1";
    $xMatriz  = f_MySql("SELECT","",$qMatriz,$xConexion01,"");
    // echo $qMatriz . " - " .  mysql_num_rows($xMatriz);
    if (mysql_num_rows($xMatriz) > 0) {
      $vMatriz = mysql_fetch_array($xMatriz);
    }

    // Consulta la información del Depósito
    $vDeposito  = array();
    $qDeposito  = "SELECT ";
    $qDeposito .= "lpar0155.depnumxx, ";
    $qDeposito .= "lpar0155.ccoidocx, ";
    $qDeposito .= "lpar0007.tdeidxxx, ";
    $qDeposito .= "lpar0007.tdedesxx, ";
    $qDeposito .= "lpar0001.orvsapxx, ";
    $qDeposito .= "lpar0001.orvdesxx, ";
    $qDeposito .= "lpar0002.ofvsapxx, ";
    $qDeposito .= "lpar0002.ofvdesxx, ";
    $qDeposito .= "lpar0003.closapxx, ";
    $qDeposito .= "lpar0003.clodesxx, ";
    $qDeposito .= "lpar0009.secsapxx, ";
    $qDeposito .= "lpar0009.secdesxx, ";
    $qDeposito .= "lpar0155.regestxx ";
    $qDeposito .= "FROM $cAlfa.lpar0155 ";
    $qDeposito .= "LEFT JOIN $cAlfa.lpar0007 ON $cAlfa.lpar0155.tdeidxxx = $cAlfa.lpar0007.tdeidxxx ";
    $qDeposito .= "LEFT JOIN $cAlfa.lpar0001 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0001.orvsapxx ";
    $qDeposito .= "LEFT JOIN $cAlfa.lpar0002 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0002.orvsapxx AND $cAlfa.lpar0155.ofvsapxx = $cAlfa.lpar0002.ofvsapxx ";
    $qDeposito .= "LEFT JOIN $cAlfa.lpar0003 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0003.orvsapxx AND $cAlfa.lpar0155.ofvsapxx = $cAlfa.lpar0003.ofvsapxx AND $cAlfa.lpar0155.closapxx = $cAlfa.lpar0003.closapxx ";
    $qDeposito .= "LEFT JOIN $cAlfa.lpar0009 ON $cAlfa.lpar0155.secsapxx = $cAlfa.lpar0009.secsapxx ";
    $qDeposito .= "WHERE ";
    $qDeposito .= "$cAlfa.lpar0155.depnumxx = \"{$vCertificacion['depnumxx']}\"";
    $xDeposito  = f_MySql("SELECT","",$qDeposito,$xConexion01,"");
    if (mysql_num_rows($xDeposito) > 0) {
      $vDeposito = mysql_fetch_array($xDeposito);
    }

    // Consulta la información de detalle de la certificación
    $mDataDetalle = array();
    $qCertifiDet  = "SELECT ";
    $qCertifiDet .= "$cAlfa.lcde$cAnio.*, ";
    $qCertifiDet .= "$cAlfa.lpar0011.sersapxx, ";
    $qCertifiDet .= "$cAlfa.lpar0011.serdesxx, ";
    $qCertifiDet .= "$cAlfa.lpar0004.obfidxxx, ";
    $qCertifiDet .= "$cAlfa.lpar0004.obfdesxx, ";
    $qCertifiDet .= "$cAlfa.lpar0006.ufaidxxx, ";
    $qCertifiDet .= "$cAlfa.lpar0006.ufadesxx, ";
    $qCertifiDet .= "$cAlfa.lpar0010.cebidxxx, ";
    $qCertifiDet .= "$cAlfa.lpar0010.cebcodxx, ";
    $qCertifiDet .= "$cAlfa.lpar0010.cebdesxx ";
    $qCertifiDet .= "FROM $cAlfa.lcde$cAnio ";
    $qCertifiDet .= "LEFT JOIN $cAlfa.lpar0011 ON $cAlfa.lcde$cAnio.sersapxx = $cAlfa.lpar0011.sersapxx ";
    $qCertifiDet .= "LEFT JOIN $cAlfa.lpar0004 ON $cAlfa.lcde$cAnio.obfidxxx = $cAlfa.lpar0004.obfidxxx ";
    $qCertifiDet .= "LEFT JOIN $cAlfa.lpar0006 ON $cAlfa.lcde$cAnio.ufaidxxx = $cAlfa.lpar0006.ufaidxxx ";
    $qCertifiDet .= "LEFT JOIN $cAlfa.lpar0010 ON $cAlfa.lcde$cAnio.cebidxxx = $cAlfa.lpar0010.cebidxxx ";


    $qCertifiDet .= "WHERE ";
    $qCertifiDet .= "$cAlfa.lcde$cAnio.ceridxxx = \"{$vCertificacion['ceridxxx']}\"";
    $xCertifiDet  = f_MySql("SELECT","",$qCertifiDet,$xConexion01,"");
    // echo $qCertifiDet;
    if (mysql_num_rows($xCertifiDet) > 0) {
      while ($xRCD = mysql_fetch_array($xCertifiDet)) {
        $nInd_mDataDetalle = count($mDataDetalle);
        $mDataDetalle[$nInd_mDataDetalle] = $xRCD;
      }
    }
  } else {
    $nSwitch = 1;
    $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
    $cMsj .= "La Certificacion Seleccionada No Existe.\n";
  }

  if ($nSwitch == 0) {
    // Inica a pintar el Excel //
    $data     = '';
    $cNomFile = "IMPRESO_CERTIFICACION_".$vCertificacion['comprexx'].$vCertificacion['comcscxx']."_".date("YmdHis").".xls";

    if ($_SERVER["SERVER_PORT"] != "") {
      $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;
    } else {
      $cFile = "{$OPENINIT['pathdr']}/opencomex/" . $vSysStr['system_download_directory'] . "/" . $cNomFile;
    }

    if (file_exists($cFile)) {
      unlink($cFile);
    }

    $fOp = fopen($cFile, 'a');

    $data .= '<table width="1024px" cellpadding="1" cellspacing="1" border="1" style="font-family:arial;font-size:12px;border-collapse: collapse;">';
      $data .= '<tr>';
        $data .= '<td colspan="12" style="font-size:14px;text-align:center;" bgcolor="#BFBFBF"><B>CERTIFICACI&Oacute;N DE BASES DE FACTURACI&Oacute;N</B></td>';
      $data .= '</tr>';
      $data .= '<tr><td colspan="12"></td></tr>';
    
      $data .= '<tr>';
        $data .= '<td colspan="2" width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>NIT</B></td>';
        $data .= '<td width="200px" style="font-size:14px;"><center>'.$vCertificacion['cliidxxx'].'</center></td>';
        $data .= '<td width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>CLIENTE</B></td>';
        $data .= '<td colspan="2" width="200px" style="font-size:14px;"><center>'.$vCertificacion['clinomxx'].'</center></td>';
        $data .= '<td width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>CODIGO SAP</B></td>';
        $data .= '<td colspan="2" width="200px" style="font-size:14px;"><center>'.$vCertificacion['clisapxx'].'</center></td>';
        $data .= '<td width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>FECHA DESDE</B></td>';
        $data .= '<td colspan="2" width="200px" style="font-size:14px;"><center>'.$vCertificacion['cerfdexx'].'</center></td>';
      $data .= '</tr>';
      $data .= '<tr>';
        $data .= '<td colspan="2" width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>No. MIF</B></td>';
        $data .= '<td width="200px" style="font-size:14px;"><center>'.$vMatriz['comprexx'].$vMatriz['comcscxx'].'</center></td>';
        $data .= '<td width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>TIPO DEPOSITO</B></td>';
        $data .= '<td colspan="2" width="200px" style="font-size:14px;"><center>'.$vDeposito['tdedesxx'].'</center></td>';
        $data .= '<td width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>DEPOSITO</B></td>';
        $data .= '<td colspan="2" width="200px" style="font-size:14px;"><center>'.$vDeposito['depnumxx'].'</center></td>';
        $data .= '<td width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>FECHA HASTA</B></td>';
        $data .= '<td colspan="2" width="200px" style="font-size:14px;"><center>'.$vCertificacion['cerfhaxx'].'</center></td>';
      $data .= '</tr>';
      $data .= '<tr>';
        $data .= '<td colspan="2" width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>ORGANIZACI&Oacute;N VENTAS</B></td>';
        $data .= '<td width="200px" style="font-size:14px;"><center>'.$vDeposito['orvdesxx'].'</center></td>';
        $data .= '<td width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>OFICINA DE VENTAS</B></td>';
        $data .= '<td colspan="2" width="200px" style="font-size:14px;"><center>'.$vDeposito['ofvdesxx'].'</center></td>';
        $data .= '<td width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>CENTRO</B></td>';
        $data .= '<td colspan="2" width="200px" style="font-size:14px;"><center>'.$vDeposito['clodesxx'].'</center></td>';
        $data .= '<td width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>SECTOR</B></td>';
        $data .= '<td colspan="2" width="200px" style="font-size:14px;"><center>'.$vDeposito['secdesxx'].'</center></td>';
      $data .= '</tr>';
      $data .= '<tr>';
        $data .= '<td colspan="2" width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>CANAL DISTRIBUCI&Oacute;N</B></td>';
        $data .= '<td width="200px" style="font-size:14px;"><center>'.$vCertificacion['cdidesxx'].'</center></td>';
        $data .= '<td width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>TIPO DE MERCANC&Iacute;A</B></td>';
        $data .= '<td colspan="5" width="200px" style="font-size:14px;">'.$vCertificacion['certipme'].'</td>';
        $data .= '<td width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>ESTADO</B></td>';
        $data .= '<td colspan="2" width="200px" style="font-size:14px;"><center>'.$vCertificacion['regestxx'].'</center></td>';
      $data .= '</tr>';
      $data .= '<tr><td colspan="12"></td></tr>';

      $data .= '<tr>';
        $data .= '<td colspan="4" width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>DESCRIPCI&Oacute;N SERVICIO</B></td>';
        $data .= '<td colspan="4" width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>DESCRIPCI&Oacute;N SUB- SERVICIO</B></td>';
        $data .= '<td colspan="4" width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>UNIDAD FACTURABLE</B></td>';
      $data .= '</tr>';
      for ($i=0; $i < count($mDataDetalle); $i++) { 
        if ($mDataDetalle[$i]['sersapxx'] != "") {
          $data .= '<tr>';
            $data .= '<td colspan="4" width="200px" style="font-size:14px;text-align:center">'.$mDataDetalle[$i]['serdesxx'].'</td>';
            $data .= '<td colspan="4" width="200px" style="font-size:14px;text-align:center">'.$mDataDetalle[$i]['subdesxx'].'</td>';
            $data .= '<td colspan="4" width="200px" style="font-size:14px;text-align:center">'.$mDataDetalle[$i]['ufadesxx'].'</td>';
          $data .= '</tr>';
        }
      }
      $data .= '<tr><td colspan="12"></td></tr>';

      $data .= '<tr>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center" bgcolor="#BFBFBF"><B>ITEM</B></td>';
        $data .= '<td colspan="2" width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>DESCRIPCI&Oacute;N MATERIAL</B></td>';
        $data .= '<td width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>COD. SAP SERVICIO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>OBJETO FACTURABLE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>UNIDAD FACTURABLE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>CEBE</B></td>';
        $data .= '<td colspan="2" width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>DESCRIPCI&Oacute;N CEBE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>BASE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>CONDICI&Oacute;N</B></td>';
        $data .= '<td width="200px" style="font-size:14px;" bgcolor="#BFBFBF"><B>ESTATUS</B></td>';
      $data .= '</tr>';

      // Pinta la informacion del detalle de la certificacion
      for ($i=0; $i < count($mDataDetalle); $i++) { 
        $data .= '<tr>';
          $data .= '<td width="200px" style="font-size:14px;text-align:center" bgcolor="#BFBFBF">'.($i+1).'</td>';
          $data .= '<td colspan="2" width="200px" style="font-size:14px;text-align:left">'.$mDataDetalle[$i]['subdesxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:center">'.$mDataDetalle[$i]['sersapxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left">'.$mDataDetalle[$i]['obfdesxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left">'.$mDataDetalle[$i]['ufadesxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:right">'.$mDataDetalle[$i]['cebcodxx'].'</td>';
          $data .= '<td colspan="2" width="200px" style="font-size:14px;text-align:center">'.$mDataDetalle[$i]['cebdesxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:right">'.$mDataDetalle[$i]['basexxxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left">'.$mDataDetalle[$i]['cerdconx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left">'.$mDataDetalle[$i]['cerdestx'].'</td>';
        $data .= '</tr>';
      }
      $data .= '<tr><td colspan="12"></td></tr>';

      $data .= '<tr>';
        $data .= '<td colspan="12" width="200px" style="font-size:14px;text-align:center" bgcolor="#BFBFBF"><B>OBSERVACIONES GENERALES</B></td>';
      $data .= '</tr>';
      $data .= '<tr>';
        $data .= '<td colspan="12" width="200px" style="font-size:14px;text-align:center"><B>'.$vCertificacion['cerobsxx'].'</B></td>';
      $data .= '</tr>';
      $data .= '<tr><td colspan="12"></td></tr>';

      // Observacion de las acciones del tracking
      $data .= '<tr>';
        $data .= '<td colspan="2" width="200px" style="font-size:14px;text-align:center" bgcolor="#BFBFBF"><B>CERT PARA FACTURACI&Oacute;N</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:left">'.$vCertificacion['cerusufa'].'</td>';
        $data .= '<td colspan="9" width="200px" style="font-size:14px;text-align:left">'.$vCertificacion['cerobsfa'].'</td>';
      $data .= '</tr>';
      $data .= '<tr>';
        $data .= '<td colspan="2" width="200px" style="font-size:14px;text-align:center" bgcolor="#BFBFBF"><B>CERTIFICADO PARA FINANCIERO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:left">'.$vCertificacion['cerusufi'].'</td>';
        $data .= '<td colspan="9" width="200px" style="font-size:14px;text-align:left">'.$vCertificacion['cerobsfi'].'</td>';
      $data .= '</tr>';
      if (($vCertificacion['regestxx'] == "CERTIFICADO" || $vCertificacion['regestxx'] == "ENPROCESO") && $vCertificacion['cerusuar'] != "") {
        $cTitulo = $vCertificacion['regestxx'] == "CERTIFICADO" ? "APROBADO FINANCIERO" : "RECHAZO FINANCIERO";
        $data .= '<tr>';
          $data .= '<td colspan="2" width="200px" style="font-size:14px;text-align:center" bgcolor="#BFBFBF"><B>'.$cTitulo.'</B></td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left">'.$vCertificacion['cerusuar'].'</td>';
          $data .= '<td colspan="9" width="200px" style="font-size:14px;text-align:left">'.$vCertificacion['cerobsar'].'</td>';
        $data .= '</tr>';
      }
      if ($vCertificacion['regestxx'] == "ANULADO" ) {
        $data .= '<tr>';
          $data .= '<td colspan="2" width="200px" style="font-size:14px;text-align:center" bgcolor="#BFBFBF"><B>ANULADO</B></td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left">'.$vCertificacion['cerusuan'].'</td>';
          $data .= '<td colspan="9" width="200px" style="font-size:14px;text-align:left">'.$vCertificacion['cerobsan'].'</td>';
        $data .= '</tr>';
      }
    $data .= '</table>';

    fwrite($fOp, $data);
    fclose($fOp);

    if (file_exists($cFile)){
      ?>
      <script languaje = "javascript">
        parent.fmpro.location = 'frgendoc.php?cRuta=<?php echo $cNomFile ?>';
      </script>
      <?php
    } else {
      f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
    }
  } else {
    f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.");
  }

?>

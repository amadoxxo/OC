<?php
namespace openComex;

/**
 * Generar Impreso de Excel - Mis Tickets.
 * --- Descripcion: Permite Generar Impreso de Excel con la Información de Mis Tickets.
 * @author Cristian Perdomo <cristian.perdomo@openits.co>
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

if ($vLimInf == "" && $vLimSup == "") {
  $vLimInf = "00";
  $vLimSup = $vSysStr['system_rows_page_ini'];
} elseif ($vLimInf == "") {
  $vLimInf = "00";
}

if (substr_count($vLimInf, "-") > 0) {
  $vLimInf = "00";
}

if ($vPaginas == "") {
  $vPaginas = "1";
}

/**INICIO SQL**/
if ($_POST['cPeriodos'] == "") {
  $_POST['cPeriodos'] == "20";
  $_POST['dDesde'] = substr(date('Y-m-d'), 0, 8) . "01";
  $_POST['dHasta'] = date('Y-m-d');
}

//La consulta solo se hace si el usuario es responsable de algun tipo de ticket
if ($_POST['vSearch'] != "") {
  //Buscando por el nombre del responsable para traer los ID's
  $qResUsr  = "SELECT USRIDXXX ";
  $qResUsr .= "FROM $cAlfa.SIAI0003 ";
  $qResUsr .= "WHERE ";
  $qResUsr .= "USRNOMXX LIKE \"%{$_POST['vSearch']}%\" ";
  $xResUsr = f_MySql("SELECT","",$qResUsr,$xConexion01,"");
  $cResUsr = "";
  while ($xRRU = mysql_fetch_array($xResUsr)) {
    $cResUsr .= "\"{$xRRU['USRIDXXX']}\",";
  }
  $cResUsr = substr($cResUsr,0,-1);

  //Buscando los nit de los clientes por razon social
  $qNieCli  = "SELECT cliidxxx ";
  $qNieCli .= "FROM $cAlfa.lpar0150 ";
  $qNieCli .= "WHERE ";
  $qNieCli .= "IF($cAlfa.lpar0150.clinomxx != \"\",$cAlfa.lpar0150.clinomxx,REPLACE(CONCAT($cAlfa.lpar0150.clinom1x,\" \",$cAlfa.lpar0150.clinom2x,\" \",$cAlfa.lpar0150.cliape1x,\" \",$cAlfa.lpar0150.cliape2x), \"  \", \" \")) LIKE \"%{$_POST['vSearch']}%\" ";
  $xNieCli = f_MySql("SELECT","",$qNieCli,$xConexion01,"");
  // echo $qNieCli."~".mysql_num_rows($xNieCli)."<br><br>";
  $cNieCli = "";
  while ($xRNC = mysql_fetch_array($xNieCli)) {
    $cNieCli .= "\"{$xRNC['cliidxxx']}\",";
  }
  $cNieCli = substr($cNieCli,0,-1);
}

$nAnioDesde = substr($_POST['dDesde'], 0, 4);
$nAnioDesde = ($nAnioDesde < $vSysStr['logistica_ano_instalacion_modulo']) ? $vSysStr['logistica_ano_instalacion_modulo'] : $nAnioDesde;

$mMiTicket = array();
for ($iAno = $nAnioDesde; $iAno <= substr($_POST['dHasta'],0,4); $iAno++) { // Recorro desde el anio de inicio hasta el anio de fin de la consulta

  $qResTic  = "SELECT GROUP_CONCAT(SIAI0003_2.USRNOMXX SEPARATOR ', ') AS ttiusrxx ";
  $qResTic .= "FROM $cAlfa.lpar0159 ";
  $qResTic .= "LEFT JOIN $cAlfa.SIAI0003 AS SIAI0003_2 ON $cAlfa.lpar0159.ttiusrxx = SIAI0003_2.USRIDXXX ";
  $qResTic .= "WHERE ";
  $qResTic .= "$cAlfa.lpar0159.tticodxx = $cAlfa.ltic$iAno.tticodxx ";

  if ($iAno == $nAnioDesde) {
    $qMiTicket  = "(SELECT DISTINCT ";
    $qMiTicket .= "SQL_CALC_FOUND_ROWS ";
  }else {
    $qMiTicket  .= "(SELECT DISTINCT ";
  }
  $qMiTicket .= "$cAlfa.ltic$iAno.ticidxxx, ";  // Id Ticket
  $qMiTicket .= "$cAlfa.ltic$iAno.ceridxxx, ";  // Id certificacion
  $qMiTicket .= "CONCAT($cAlfa.ltic$iAno.comidxxx,\"-\",$cAlfa.ltic$iAno.comprexx,$cAlfa.ltic$iAno.comcscxx) AS comcscxx,";  // Consecutivo
  $qMiTicket .= "$cAlfa.ltic$iAno.comfecxx, ";  // Fecha Comprobante
  $qMiTicket .= "$cAlfa.ltic$iAno.cliidxxx,";   // Id cliente
  $qMiTicket .= "$cAlfa.ltic$iAno.tticodxx, ";  // Codigo Tipo Ticket
  $qMiTicket .= "$cAlfa.ltic$iAno.pticodxx, ";  // Codigo Prioridad Ticket
  $qMiTicket .= "$cAlfa.ltic$iAno.sticodxx, ";  // Codigo Status Ticket
  $qMiTicket .= "$cAlfa.ltic$iAno.ticasuxx, ";  // Asunto
  $qMiTicket .= "$cAlfa.ltic$iAno.ticcierx, ";  // Fecha de cierre
  $qMiTicket .= "$cAlfa.ltic$iAno.regusrxx, ";  // Usuario que creo el registro
  $qMiTicket .= "$cAlfa.ltic$iAno.regfcrex, ";  // Fecha de creación
  $qMiTicket .= "$cAlfa.ltic$iAno.reghcrex, ";  // Hora de creación
  $qMiTicket .= "$cAlfa.ltic$iAno.regfmodx, ";  // Fecha de modificación
  $qMiTicket .= "$cAlfa.ltic$iAno.reghmodx, ";  // Hora de modificación
  $qMiTicket .= "$cAlfa.ltic$iAno.regstamp, ";  // Hora de modificación
  $qMiTicket .= "$cAlfa.ltic$iAno.regestxx, ";  // Estado
  $qMiTicket .= "$cAlfa.lpar0158.ttidesxx, ";   // Descripcion Ticket
  $qMiTicket .= "$cAlfa.lpar0156.pticolxx, ";   // Color
  $qMiTicket .= "$cAlfa.lpar0156.ptidesxx, ";   // Proiridad descripcion
  $qMiTicket .= "$cAlfa.lpar0157.stidesxx, ";   // Status
  $qMiTicket .= "$cAlfa.lpar0157.stitipxx  ";   // Tipo Status
  if (substr_count($_POST['cOrderByOrder'],"usrnomxx") > 0) {
    $qMiTicket .= ", $cAlfa.SIAI0003.USRNOMXX AS usrnomxx ";   // Creado por
  }
  if (substr_count($_POST['cOrderByOrder'],"clinomxx") > 0) {
    $qMiTicket .= ", IF($cAlfa.lpar0150.clinomxx != \"\",$cAlfa.lpar0150.clinomxx,REPLACE(CONCAT($cAlfa.lpar0150.clinom1x,\" \",$cAlfa.lpar0150.clinom2x,\" \",$cAlfa.lpar0150.cliape1x,\" \",$cAlfa.lpar0150.cliape2x), \"  \", \" \")) AS clinomxx "; // Nombre Cliente
  }
  if (substr_count($_POST['cOrderByOrder'],"ttiusrxx") > 0) {
    $qMiTicket .= ", ($qResTic) AS ttiusrxx ";   // Responsables
  }
  $qMiTicket .= "FROM $cAlfa.ltic$iAno ";
  if (substr_count($_POST['cOrderByOrder'],"clinomxx") > 0) {
    $qMiTicket .= "LEFT JOIN $cAlfa.lpar0150 ON $cAlfa.ltic$iAno.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
  }
  $qMiTicket .= "LEFT JOIN $cAlfa.lpar0158 ON $cAlfa.ltic$iAno.tticodxx = $cAlfa.lpar0158.tticodxx ";
  $qMiTicket .= "LEFT JOIN $cAlfa.lpar0156 ON $cAlfa.ltic$iAno.pticodxx = $cAlfa.lpar0156.pticodxx ";
  $qMiTicket .= "LEFT JOIN $cAlfa.lpar0157 ON $cAlfa.ltic$iAno.sticodxx = $cAlfa.lpar0157.sticodxx ";
  $qMiTicket .= "LEFT JOIN $cAlfa.lpar0159 ON $cAlfa.ltic$iAno.tticodxx = $cAlfa.lpar0159.tticodxx ";
  if (substr_count($_POST['cOrderByOrder'],"usrnomxx") > 0) {
    $qMiTicket .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.ltic$iAno.regusrxx = $cAlfa.SIAI0003.USRIDXXX ";
  }
  $qMiTicket .= "WHERE ";
  // Campos de la Consulta inducida
  // Buscando por Ticket id
  if ($_POST['cTicket'] != "") {
    $qMiTicket .= "$cAlfa.ltic$iAno.ticidxxx = \"{$_POST['cTicket']}\" AND ";
  }
  // Buscando por Asunto
  if ($_POST['cTiAsun'] != "") {
    $qMiTicket .= "$cAlfa.ltic$iAno.ticasuxx LIKE \"%{$_POST['cTiAsun']}%\" AND ";
  }
  // Buscando por certificado
  if ($_POST['cCerId'] != "") {
    $qMiTicket .= "$cAlfa.ltic$iAno.ceridxxx = \"{$_POST['cCerId']}\" AND ";
  }
  // Buscando por Cliente
  if ($_POST['cCliId'] != "") {
    $qMiTicket .= "$cAlfa.ltic$iAno.cliidxxx = \"{$_POST['cCliId']}\" AND ";
  }
  // Creado por
  if ($_POST['cUsrId'] != "") {
    $qMiTicket .= "$cAlfa.ltic$iAno.regusrxx = \"{$_POST['cUsrId']}\" AND ";
  }
  // Responsable
  if ($_POST['cResId'] != "") {
    $qMiTicket .= "$cAlfa.lpar0159.ttiusrxx = \"{$_POST['cResId']}\" AND ";
  }
  // Tipo ticket
  if ($_POST['cTipId'] != "") {
    $qMiTicket .= "$cAlfa.ltic$iAno.tticodxx = \"{$_POST['cTipId']}\" AND ";
  }
  // Prioridad ticket
  if ($_POST['cPriori'] != "") {
    $qMiTicket .= "$cAlfa.ltic$iAno.pticodxx = \"{$_POST['cPriori']}\" AND ";
  }
  // Status
  if ($_POST['cStatus'] != "") {
    $qMiTicket .= "$cAlfa.ltic$iAno.sticodxx = \"{$_POST['cStatus']}\" AND ";
  }
  // Busqueda por campo vSearch
  if ($_POST['vSearch'] != "") {
    $qMiTicket .= "($cAlfa.ltic$iAno.ticidxxx LIKE \"%{$_POST['vSearch']}%\" OR ";
    if ($cNieCli != "") {
      $qMiTicket .= "$cAlfa.ltic$iAno.cliidxxx IN ($cNieCli) OR ";
    }
    $qMiTicket .= "CONCAT($cAlfa.ltic$iAno.comidxxx,\"-\",$cAlfa.ltic$iAno.comprexx,$cAlfa.ltic$iAno.comcscxx) LIKE \"%{$_POST['vSearch']}%\" OR ";
    $qMiTicket .= "$cAlfa.lpar0158.ttidesxx  LIKE \"%{$_POST['vSearch']}%\" OR ";
    $qMiTicket .= "$cAlfa.ltic$iAno.ticasuxx LIKE \"%{$_POST['vSearch']}%\" OR ";
    if ($cResUsr != ""){
      $qMiTicket .= "$cAlfa.ltic$iAno.regusrxx IN ($cResUsr) OR ";
      $qMiTicket .= "$cAlfa.lpar0159.ttiusrxx  IN ($cResUsr) OR ";
    }            
    $qMiTicket .= "$cAlfa.lpar0156.ptidesxx  LIKE \"%{$_POST['vSearch']}%\" OR ";
    $qMiTicket .= "$cAlfa.lpar0157.stidesxx  LIKE \"%{$_POST['vSearch']}%\") AND ";
  }
  // Consulta solo los tickets que el usuario es responsable
  $qMiTicket .= "$cAlfa.ltic$iAno.regfcrex BETWEEN \"{$_POST['dDesde']}\" AND \"{$_POST['dHasta']}\") ";
  /***** FIN SQL *****/
  if ($iAno >= $nAnioDesde && $iAno < substr($_POST['dHasta'],0,4)) {
    $qMiTicket .= " UNION ";
  }
} ## for ($iAno=$nAnioDesde;$iAno<=substr($_POST['dHasta'],0,4);$iAno++) { ##
// CODIGO NUEVO PARA ORDER BY
$cOrderBy = "";
$vOrderByOrder = explode("~", $_POST['cOrderByOrder']);
for ($z = 0; $z < count($vOrderByOrder); $z++) {
  if ($vOrderByOrder[$z] != "") {
    if ($_POST[$vOrderByOrder[$z]] != "") {
      $cOrderBy .= $_POST[$vOrderByOrder[$z]];
    }
  }
}
if (strlen($cOrderBy) > 0) {
  $cOrderBy = substr($cOrderBy, 0, strlen($cOrderBy) - 1);
  $cOrderBy = "ORDER BY " . $cOrderBy;
} else {
  $cOrderBy = "ORDER BY regstamp DESC ";
}
// FIN CODIGO NUEVO PARA ORDER BY
$qMiTicket   .= "$cOrderBy LIMIT $vLimInf,$vLimSup ";
$cIdCountRow  = mt_rand(1000000000, 9999999999);
$xMiTicket    = mysql_query($qMiTicket, $xConexion01, true, $cIdCountRow);
// f_Mensaje(__FILE__,__LINE__,$qMiTicket."~".mysql_num_rows($xMiTicket));
// echo $qMiTicket."~".mysql_num_rows($xMiTicket)."<br><br>";

$xNumRows = mysql_query("SELECT @foundRows".$cIdCountRow." AS CANTIDAD",$xConexion01);
$xRNR     = mysql_fetch_array($xNumRows);
$nRNR     = $xRNR['CANTIDAD'];

$mMatrizUsr = array();
$vExisteUsr = array();
while ($xRMI = mysql_fetch_array($xMiTicket)) {
  if (substr_count($_POST['cOrderByOrder'],"usrnomxx") == 0) {
    // Busco la informacion del usuario autenticado
    $qUsrNom  = "SELECT USRIDXXX, USRNOMXX, REGESTXX ";
    $qUsrNom .= "FROM $cAlfa.SIAI0003 ";
    $qUsrNom .= "WHERE ";
    $qUsrNom .= "USRIDXXX = \"{$xRMI['regusrxx']}\"";
    $xUsrNom = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
    if (mysql_num_rows($xUsrNom) > 0) {
      $vUsrNom = mysql_fetch_array($xUsrNom);
      $xRMI['usrnomxx'] = $vUsrNom['USRNOMXX'];
    }
  }
  if (substr_count($_POST['cOrderByOrder'],"ttiusrxx") == 0) {
    $qResTic  = "SELECT GROUP_CONCAT(SIAI0003.USRNOMXX SEPARATOR ', ') AS ttiusrxx ";
    $qResTic .= "FROM $cAlfa.lpar0159 ";
    $qResTic .= "LEFT JOIN $cAlfa.SIAI0003 AS SIAI0003 ON $cAlfa.lpar0159.ttiusrxx = SIAI0003.USRIDXXX ";
    $qResTic .= "WHERE ";
    $qResTic .= "$cAlfa.lpar0159.tticodxx = \"{$xRMI['tticodxx']}\"";
    $xResTic = f_MySql("SELECT","",$qResTic,$xConexion01,"");
    $vResTic = mysql_fetch_array($xResTic);
    $xRMI['ttiusrxx'] = $vResTic['ttiusrxx'];
  }

  if (substr_count($_POST['cOrderByOrder'],"clinomxx") == 0) {
    $qNieCli  = "SELECT IF($cAlfa.lpar0150.clinomxx != \"\",$cAlfa.lpar0150.clinomxx,REPLACE(CONCAT($cAlfa.lpar0150.clinom1x,\" \",$cAlfa.lpar0150.clinom2x,\" \",$cAlfa.lpar0150.cliape1x,\" \",$cAlfa.lpar0150.cliape2x), \"  \", \" \")) AS clinomxx ";
    $qNieCli .= "FROM $cAlfa.lpar0150 ";
    $qNieCli .= "WHERE ";
    $qNieCli .= "cliidxxx = \"{$xRMI['cliidxxx']}\"";
    $xNieCli = f_MySql("SELECT","",$qNieCli,$xConexion01,"");
    $vNieCli = mysql_fetch_array($xNieCli);
    $xRMI['clinomxx'] = $vNieCli['clinomxx'];
  }

  $mMiTicket[count($mMiTicket)] = $xRMI;
}

if ($nSwitch == 0) {
  // Inica a pintar el Excel //
  
  $cNomFile = "IMPRESO_ADMONTICKETS_" . $_COOKIE['kUsrId'] . "_" . date("YmdHis") . ".xls";

  if ($_SERVER["SERVER_PORT"] != "") {
    $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;
  } else {
    $cFile = "{$OPENINIT['pathdr']}/opencomex/" . $vSysStr['system_download_directory'] . "/" . $cNomFile;
  }

  if (file_exists($cFile)) {
    unlink($cFile);
  }

  $fOp = fopen($cFile, 'a');

  $data  = '';
  $data .= '<table cellpadding="1" cellspacing="1" border="1" style="font-family: Arial, sans-serif; font-size: 12px; border-collapse: collapse;">';
  $data .= '<tr>';
  $data .= '<td colspan="11" style="font-size: 14px; font-weight: bold;">REPORTE MIS TICKETS</td>';
  $data .= '</tr>';
  $data .= '<tr>';
    // Define los anchos de las columnas
    $columns = [
      'TICKET'        => '50px',
      'ASUNTO'        => '200px',
      'CERTIFICACIÓN' => '150px',
      'CLIENTE'       => '200px',
      'TIPO'          => '100px',
      'CREADO POR'    => '80px',
      'RESPONSABLE'   => '200px',
      'CREADO'        => '100px',
      'PRIORIDAD'     => '100px',
      'STATUS'        => '100px',
      'CIERRE'        => '100px'
    ];
    foreach ($columns as $header => $width) {
      $data .= "<td style='font-weight: bold; text-align: center; background-color: #0b6730; color: white; width: $width;'>$header</td>";
    }
  $data .= '</tr>';
  fwrite($fOp, $data);

  for ($i = 0; $i < count($mMiTicket); $i++) {
    $data  = '<tr>';
    $data .= '<td style="mso-number-format:\@\"; border: 0.5px solid black;">' . htmlspecialchars($mMiTicket[$i]['ticidxxx'], ENT_QUOTES, 'UTF-8') . '</td>';
    $data .= '<td style="mso-number-format:\@\"; border: 0.5px solid black;">' . htmlspecialchars($mMiTicket[$i]['ticasuxx'], ENT_QUOTES, 'UTF-8') . '</td>';
    $data .= '<td style="mso-number-format:\@\"; border: 0.5px solid black;">' . htmlspecialchars($mMiTicket[$i]['comcscxx'], ENT_QUOTES, 'UTF-8') . '</td>';
    $data .= '<td style="mso-number-format:\@\"; border: 0.5px solid black;">' . htmlspecialchars($mMiTicket[$i]['clinomxx'], ENT_QUOTES, 'UTF-8') . '</td>';
    $data .= '<td style="mso-number-format:\@\"; border: 0.5px solid black;">' . htmlspecialchars($mMiTicket[$i]['ttidesxx'], ENT_QUOTES, 'UTF-8') . '</td>';
    $data .= '<td style="mso-number-format:\@\"; border: 0.5px solid black;">' . htmlspecialchars($mMiTicket[$i]['usrnomxx'], ENT_QUOTES, 'UTF-8') . '</td>';
    $data .= '<td style="mso-number-format:\@\"; border: 0.5px solid black;">' . htmlspecialchars($mMiTicket[$i]['ttiusrxx'], ENT_QUOTES, 'UTF-8') . '</td>';
    $data .= '<td style="mso-number-format:\@\"; border: 0.5px solid black;">' . htmlspecialchars($mMiTicket[$i]['regfcrex'], ENT_QUOTES, 'UTF-8') . '</td>';
    $data .= '<td class="table-cell" style="border: 0.5px solid black; color:' . $mMiTicket[$i]['pticolxx'] . '">' . htmlspecialchars($mMiTicket[$i]['ptidesxx'], ENT_QUOTES, 'UTF-8') . '</td>';
    $data .= '<td style="mso-number-format:\@\"; border: 0.5px solid black;">' . htmlspecialchars($mMiTicket[$i]['stidesxx'], ENT_QUOTES, 'UTF-8') . '</td>';
    $data .= '<td style="mso-number-format:\@\"; border: 0.5px solid black;">' . htmlspecialchars((($mMiTicket[$i]['ticcierx'] != "0000-00-00 00:00:00") ? $mMiTicket[$i]['ticcierx'] : ""), ENT_QUOTES, 'UTF-8') . '</td>';
    $data .= '</tr>';
    fwrite($fOp, $data);
  }
  $data = '</table>';
  fwrite($fOp, $data);
  fclose($fOp);

  if (file_exists($cFile)) { ?>
    <script languaje="javascript">
      parent.fmpro.location = 'fratidoc.php?cRuta=<?php echo $cNomFile ?>';
    </script>
  <?php } else {
    f_Mensaje(__FILE__, __LINE__, "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
  }
} else {
  f_Mensaje(__FILE__, __LINE__, $cMsj . "Verifique.");
}

?>
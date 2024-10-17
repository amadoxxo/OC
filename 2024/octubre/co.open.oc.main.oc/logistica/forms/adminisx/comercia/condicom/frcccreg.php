<?php
  namespace openComex;
  //set_time_limit(0);
	include("../../../../../financiero/libs/php/utility.php");

  /**
   * Variable para controlar los errores.
   */
  $nSwitch = 0;

  /**
   * Variable para almacenar los errores.
   */
  $cMsj = "";
  
  // Validaciones
  if ($gDesde == "" || $gDesde == "0000-00-00" || $gHasta == "" || $gHasta == "0000-00-00") {
    $nSwitch = 1;
    $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
    $cMsj .= "La Fecha Desde y Fecha Hasta son Obligatorias.\n";
  } else {
    // Valida que el rango de fechas sea maximo de 1 anio 
    $dFecLimi = date("d-m-Y",strtotime($gHasta."- 12 month"));
    $dFecLimi = strtotime($dFecLimi);
    $dDesde   = strtotime($gDesde);
    if($dDesde < $dFecLimi) {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "El Rango de Fechas no Puede ser Mayor a 1 Anio.\n";
    }
  }

  if ($nSwitch == 0) {
    // Consulta las condiciones comerciales
    $qCondiCom  = "SELECT lpar0151.*, ";
    $qCondiCom .= "lpar0150.clisapxx, ";
    $qCondiCom .= "IF($cAlfa.lpar0150.clinomxx != \"\",$cAlfa.lpar0150.clinomxx,CONCAT($cAlfa.lpar0150.clinom1x,\" \",$cAlfa.lpar0150.clinom2x,\" \",$cAlfa.lpar0150.cliape1x,\" \",$cAlfa.lpar0150.cliape2x)) AS clinomxx ";
    $qCondiCom .= "FROM $cAlfa.lpar0151 ";
    $qCondiCom .= "LEFT JOIN $cAlfa.lpar0150 ON lpar0151.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
    $qCondiCom .= "WHERE ";
    if ($gCliId != "") {
      $qCondiCom .= "lpar0151.cliidxxx = \"$gCliId\" AND ";
    }
    $qCondiCom .= "lpar0151.regfcrex BETWEEN \"$gDesde\" AND \"$gHasta\" AND ";
    switch($gEstado) {
      case "ACTIVO":
        $qCondiCom .= "lpar0151.regestxx = \"ACTIVO\" ";
      break;
      case "INACTIVO":
        $qCondiCom .= "lpar0151.regestxx = \"INACTIVO\" ";
      break;
      default:
        $qCondiCom .= "lpar0151.regestxx IN (\"ACTIVO\",\"INACTIVO\") ";
      break;
    }
    $xCondiCom = f_MySql("SELECT","",$qCondiCom,$xConexion01,"");
    // f_mensaje(__FILE__,__LINE__,$qCondiCom."~".mysql_num_rows($xCondiCom));

    switch ($gTipo) {
      case 1: ?>
        <html>
          <head>
            <title>Reporte Condiciones comerciales</title>
            <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
            <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
            <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
            <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
            <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
          </head>
          <body>
            <form name = 'frgrm' method="POST">
              <center>
                <table border="1" cellspacing="0" cellpadding="0" align=center style="margin:5px" width="99%">
                  <tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">
                    <td class="name" colspan = "12" align="center" style="font-size:12pt"><b>ALPOPULAR SA</b></td>
                  </tr>
                  <tr>
                    <td class="name" colspan = "12" align="center" style="font-size:12pt"><b>REPORTE CONDICIONES COMERCIALES</b></td>
                  </tr>
                  <tr>
                    <td class="name" colspan = "12" align="left">Fecha Generacion <?php echo date('Y-m-d H:i:s') ?></td>
                  </tr>
                  <tr height="20">
                    <td class="name" style="background-color:#2ce404;padding-left:5px;padding-top:5px;text-align:center">No. OFERTA COMERCIAL</td>
                    <td class="name" style="background-color:#2ce404;padding-left:5px;padding-top:5px;text-align:center">NIT</td>
                    <td class="name" style="background-color:#2ce404;padding-left:5px;padding-top:5px;text-align:center">CLIENTE</td>
                    <td class="name" style="background-color:#2ce404;padding-left:5px;padding-top:5px;text-align:center">COD SAP</td>
                    <td class="name" style="background-color:#2ce404;padding-left:5px;padding-top:5px;text-align:center">TIPO</td>
                    <td class="name" style="background-color:#2ce404;padding-left:5px;padding-top:5px;text-align:center">DIA CIERRE DE FACTURACION</td>
                    <td class="name" style="background-color:#2ce404;padding-left:5px;padding-top:5px;text-align:center">FECHA VIGENCIA DESDE</td>
                    <td class="name" style="background-color:#2ce404;padding-left:5px;padding-top:5px;text-align:center">FECHA VIGENCIA HASTA</td>
                    <td class="name" style="background-color:#2ce404;padding-left:5px;padding-top:5px;text-align:center">TIPO DE INCREMENTO</td>
                    <td class="name" style="background-color:#2ce404;padding-left:5px;padding-top:5px;text-align:center">ESPECIFIQUE</td>
                    <td class="name" style="background-color:#2ce404;padding-left:5px;padding-top:5px;text-align:center">OBSERVACIONES</td>
                    <td class="name" style="background-color:#2ce404;padding-left:5px;padding-top:5px;text-align:center">ESTADO</td>
                  </tr>
                  <?php

                  if (mysql_num_rows($xCondiCom) > 0) {
                    while ($xRCC = mysql_fetch_array($xCondiCom)) { ?>
                      <tr height="20">
                        <td class="letra8" style="padding-left:5px;padding-top:5px;text-align:center"><?php echo $xRCC['ccoidocx'] ?></td>
                        <td class="letra8" style="padding-left:5px;padding-top:5px;text-align:center"><?php echo $xRCC['cliidxxx'] ?></td>
                        <td class="letra8" style="padding-left:5px;padding-top:5px;text-align:left"><?php echo $xRCC['clinomxx'] ?></td>
                        <td class="letra8" style="padding-left:5px;padding-top:5px;text-align:center"><?php echo $xRCC['clisapxx'] ?></td>
                        <td class="letra8" style="padding-left:5px;padding-top:5px;text-align:left"><?php echo $xRCC['ccotipxx'] ?></td>
                        <td class="letra8" style="padding-left:5px;padding-top:5px;text-align:left"><?php echo $xRCC['ccociexx'] ?></td>
                        <td class="letra8" style="padding-left:5px;padding-top:5px;text-align:center"><?php echo $xRCC['ccofvdxx'] ?></td>
                        <td class="letra8" style="padding-left:5px;padding-top:5px;text-align:center"><?php echo $xRCC['ccofvhxx'] ?></td>
                        <td class="letra8" style="padding-left:5px;padding-top:5px;text-align:center"><?php echo $xRCC['ccoincxx'] ?></td>
                        <td class="letra8" style="padding-left:5px;padding-top:5px;text-align:center"><?php echo $xRCC['ccoincox'] ?></td>
                        <td class="letra8" style="padding-left:5px;padding-top:5px;text-align:left"><?php echo $xRCC['ccoobsxx'] ?></td>
                        <td class="letra8" style="padding-left:5px;padding-top:5px;text-align:center"><?php echo $xRCC['regestxx'] ?></td>
                      </tr>
                    <?php }
                  } ?>
                </table>
              </center>
            </form>
          </body>
        </html>
        <?php
      break;
      case 2:
        $cNomFile = "ReporteCondicionesComerciales_".$_COOKIE['kUsrId']."_".date("YmdHis").".xls";
        $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/".$cNomFile;
        if (file_exists($cFile)){
          unlink($cFile);
        }

        $fOp = fopen($cFile,'a');

        $cCad01 .= "<table border=\"1\" cellspacing=\"0\" cellpadding=\"0\" align=center style=\"margin:5px\" width=\"99%\">";
          $cCad01 .= "<tr bgcolor = \"white\" height=\"20\" style=\"padding-left:5px;padding-top:5px\">";
            $cCad01 .= "<td colspan = \"12\" align=\"center\" style=\"font-size:12pt\"><b>ALPOPULAR SA</b></td>";
          $cCad01 .= "</tr>";
          $cCad01 .= "<tr>";
            $cCad01 .= "<td colspan = \"12\" align=\"center\" style=\"font-size:12pt\"><b>REPORTE CONDICIONES COMERCIALES</b></td>";
          $cCad01 .= "</tr>";
          $cCad01 .= "<tr>";
            $cCad01 .= "<td colspan = \"12\" align=\"left\">Fecha Generacion ".date('Y-m-d H:i:s')."</td>";
          $cCad01 .= "</tr>";
          $cCad01 .= "<tr height=\"20\">";
            $cCad01 .= "<td style=\"background-color:#2ce404;padding-left:5px;padding-top:5px;text-align:center\"><b>No. OFERTA COMERCIAL</b></td>";
            $cCad01 .= "<td style=\"background-color:#2ce404;padding-left:5px;padding-top:5px;text-align:center\"><b>NIT</b></td>";
            $cCad01 .= "<td style=\"background-color:#2ce404;padding-left:5px;padding-top:5px;text-align:center\"><b>CLIENTE</b></td>";
            $cCad01 .= "<td style=\"background-color:#2ce404;padding-left:5px;padding-top:5px;text-align:center\"><b>COD SAP</b></td>";
            $cCad01 .= "<td style=\"background-color:#2ce404;padding-left:5px;padding-top:5px;text-align:center\"><b>TIPO</b></td>";
            $cCad01 .= "<td style=\"background-color:#2ce404;padding-left:5px;padding-top:5px;text-align:center\"><b>DIA CIERRE DE FACTURACION</b></td>";
            $cCad01 .= "<td style=\"background-color:#2ce404;padding-left:5px;padding-top:5px;text-align:center\"><b>FECHA VIGENCIA DESDE</b></td>";
            $cCad01 .= "<td style=\"background-color:#2ce404;padding-left:5px;padding-top:5px;text-align:center\"><b>FECHA VIGENCIA HASTA</b></td>";
            $cCad01 .= "<td style=\"background-color:#2ce404;padding-left:5px;padding-top:5px;text-align:center\"><b>TIPO DE INCREMENTO</b></td>";
            $cCad01 .= "<td style=\"background-color:#2ce404;padding-left:5px;padding-top:5px;text-align:center\"><b>ESPECIFIQUE</b></td>";
            $cCad01 .= "<td style=\"background-color:#2ce404;padding-left:5px;padding-top:5px;text-align:center\"><b>OBSERVACIONES</b></td>";
            $cCad01 .= "<td style=\"background-color:#2ce404;padding-left:5px;padding-top:5px;text-align:center\"><b>ESTADO</b></td>";
          $cCad01 .= "</tr>";
          fwrite($fOp,$cCad01);
          if (mysql_num_rows($xCondiCom) > 0) {
            while ($xRCC = mysql_fetch_array($xCondiCom)) {

              $cCad01  = "<tr>";
                $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRCC['ccoidocx']}</td>"; //No. OFERTA COMERCIAL
                $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRCC['cliidxxx']}</td>"; //NIT
                $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRCC['clinomxx']}</td>"; //CLIENTE
                $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRCC['clisapxx']}</td>"; //COD SAP
                $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRCC['ccotipxx']}</td>"; //TIPO
                $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRCC['ccociexx']}</td>"; //DIA CIERRE DE FACTURACION
                $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRCC['ccofvdxx']}</td>"; //FECHA VIGENCIA DESDE
                $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRCC['ccofvhxx']}</td>"; //FECHA VIGENCIA HASTA
                $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRCC['ccoincxx']}</td>"; //TIPO DE INCREMENTO
                $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRCC['ccoincox']}</td>"; //ESPECIFIQUE
                $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRCC['ccoobsxx']}</td>"; //OBSERVACIONES
                $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRCC['regestxx']}</td>"; //ESTADO
              $cCad01 .= "</tr>";
              fwrite($fOp,$cCad01);
            }
          }
        $cCad01 = "</table>";
        fwrite($fOp,$cCad01);
        fclose($fOp);
        if (file_exists($cFile)){
          // Obtener la ruta absoluta del archivo
          $cAbsolutePath = realpath($cFile);
          $cAbsolutePath = substr($cAbsolutePath,0,strrpos($cAbsolutePath, '/'));

          if (in_array(realpath($cAbsolutePath), $vSystem_Path_Authorized)) {
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
          }
        } else {
          f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
        }
      break;
    }
  } else {
    f_Mensaje(__FILE__,__LINE__,$cMsj . "Verifique."); ?>
    <script languaje="javascript">
    window.close();
    </script>
    <?php
  }	
?>

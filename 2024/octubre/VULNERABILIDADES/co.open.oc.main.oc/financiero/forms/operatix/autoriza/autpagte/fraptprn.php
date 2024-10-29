<?php
  namespace openComex;
  /**
   * Generar Impreso de Excel - Observaciones
   * --- Descripcion: Permite Generar Impreso de Excel con la Informacion de las Observaciones
   * @author Juan José Hernandez <juan.hernandez@openits.co>
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

  // Consulta las Observaciones
  $qObservacion  = "SELECT  ";
  $qObservacion .= "$cAlfa.fdob0000.fdobidxx, ";
  $qObservacion .= "$cAlfa.fdob0000.sucidxxx, ";
  $qObservacion .= "$cAlfa.fdob0000.docidxxx, ";
  $qObservacion .= "$cAlfa.fdob0000.docsufxx, ";
  $qObservacion .= "$cAlfa.fdob0000.obsobsxx, ";
  $qObservacion .= "$cAlfa.fdob0000.regusrxx, ";
  $qObservacion .= "$cAlfa.fdob0000.regfcrex, ";
  $qObservacion .= "$cAlfa.fdob0000.reghcrex, ";
  $qObservacion .= "$cAlfa.fdob0000.regfmodx, ";
  $qObservacion .= "$cAlfa.fdob0000.reghmodx, ";
  $qObservacion .= "$cAlfa.fdob0000.regestxx, ";
  $qObservacion .= "IF($cAlfa.SIAI0003.USRNOMXX != \"\",$cAlfa.SIAI0003.USRNOMXX,\"USUARIO SIN NOMBRE\") AS usrnomxx ";
  $qObservacion .= "FROM $cAlfa.fdob0000 ";
  $qObservacion .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.fdob0000.regusrxx = $cAlfa.SIAI0003.USRIDXXX ";
  $xObservacion  = f_MySql("SELECT","",$qObservacion,$xConexion01,"");

  if (mysql_num_rows($xObservacion) == 0) {
    $nSwitch = 1;
    $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
    $cMsj .= "No se Encontraron Registros.\n";
  }

  if ($nSwitch == 0) {
    // Inica a pintar el Excel //
    $data     = '';
    $cNomFile = "AUTORIZACION_PAGOS_TERCEROS_".date("YmdHis").".xls";

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
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>SUCURSAL</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>DO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>SUFIJO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>OBSERVACI&Oacute;N</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>USUARIO CREACI&Oacute;N</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>CREADO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>HORA</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>MODIFICADO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>HORA</B></td>';
      $data .= '</tr>';
      
      while ($xRSS = mysql_fetch_array($xObservacion)) {
        $data .= '<tr>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$xRSS['sucidxxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$xRSS['docidxxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$xRSS['docsufxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$xRSS['obsobsxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$xRSS['usrnomxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:center;">'.date("Y-m-d", strtotime($xRSS['regfcrex'])).'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:center;">'.date("H:i", strtotime($xRSS['reghcrex'])).'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:center;">'.date("Y-m-d", strtotime($xRSS['regfmodx'])).'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:center;">'.date("H:i", strtotime($xRSS['reghmodx'])).'</td>';
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

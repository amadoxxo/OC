<?php
  namespace openComex;
  /**
   * Generar Impreso de Excel - Subservicios
   * --- Descripcion: Permite Generar Impreso de Excel con la Informacion de la Subservicios
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

  // Consulta los Subservicios
  $qSubservicios  = "SELECT  ";
  $qSubservicios .= "$cAlfa.lpar0012.*, ";
  $qSubservicios .= "$cAlfa.lpar0011.sersapxx, ";
  $qSubservicios .= "$cAlfa.lpar0011.serdesxx ";
  $qSubservicios .= "FROM $cAlfa.lpar0012 ";
  $qSubservicios .= "LEFT JOIN $cAlfa.lpar0011 ON lpar0012.sersapxx = $cAlfa.lpar0011.sersapxx ";
  $xSubservicios  = f_MySql("SELECT","",$qSubservicios,$xConexion01,"");
  echo $qSubservicios;

  if (mysql_num_rows($xSubservicios) == 0) {
    $nSwitch = 1;
    $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
    $cMsj .= "No se Encontraron Registros.\n";
  }

  if ($nSwitch == 0) {
    // Inica a pintar el Excel //
    $data     = '';
    $cNomFile = "IMPRESO_SUBSERVICIOS_".date("YmdHis").".xls";

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
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>ID SUBSERVICIO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>DESCRIPCI&Oacute;N SUBSERVICIO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>COD. SAP SERVICIO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>DESCRIPCI&Oacute;N SERVICIO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>FECHA CREACI&Oacute;N</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>FECHA MODIFICACI&Oacute;N</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>ESTADO</B></td>';
      $data .= '</tr>';
      
      while ($xRSS = mysql_fetch_array($xSubservicios)) {
        $data .= '<tr>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$xRSS['subidxxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$xRSS['subdesxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$xRSS['sersapxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$xRSS['serdesxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:center;">'.date("Y-m-d", strtotime($xRSS['regfcrex'])).'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:center;">'.date("Y-m-d", strtotime($xRSS['regfmodx'])).'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:center;">'.$xRSS['regestxx'].'</td>';
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

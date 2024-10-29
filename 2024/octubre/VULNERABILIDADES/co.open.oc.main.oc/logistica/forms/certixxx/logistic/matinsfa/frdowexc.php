<?php
  namespace openComex;
  /**
   * Generar Rpeorte - Matriz de Insumos Facturables.
   * --- Descripcion: Permite Generar el Reporte en Excel con la Informacion de la Matriz de Insumos Facturables.
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
   * @package opencomex
   * @version 001
   */

  include("../../../../../financiero/libs/php/utility.php");
  include("../../../../libs/php/utimifxx.php");

  /**
   * Se instancia la clase de Matriz de Insumos Facturables
   */
  $ObjcMatrizInsumosFacturables = new cMatrizInsumosFacturables();

  // Se consultan los subservicios
  $pArrayDatos = array();
  $pArrayDatos['cMifId']   = $_POST['cMifId'];
  $pArrayDatos['cAnio']    = $_POST['cAnio'];
  $pArrayDatos['cEstSubs'] = "ACTIVO";
  $pArrayDatos['cCcoIdOc'] = $_POST['cCcoIdOc'];
  $pArrayDatos['cDepNum']  = $_POST['cDepNum'];
  $mReturnSubservicios     = $ObjcMatrizInsumosFacturables->fnCargarDataSubserviciosMIF($pArrayDatos);

  $cNomFile = "CargueMasivoMovimiento_".$_COOKIE['kUsrId']."_".date("YmdHis").".xls";
  $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/".$cNomFile;
  if (file_exists($cFile)){
    unlink($cFile);
  }
  
  $fOp = fopen($cFile,'a');

  $cDataCab = "FECHA\t";
  $cDataDet = "";
  if ($mReturnSubservicios[0] == "true") {
    $mData = $mReturnSubservicios[1]['subservi'];

    // Se recorren los subservicios
    for ($i=0; $i < count($mData); $i++) {
      $cDataCab .= $mData[$i]['subidxxx'] . " - " . $mData[$i]['subdesxx']."\t";
    }

    $cDataCab .= "\n";

    $fechaInicio = strtotime(date($_POST['dDesde']));
    $fechaFin    = strtotime(date($_POST['dHasta']));

    $cDataDet = "";
    for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){
      $cFecha = date("Y-m-d", $i);
      $cDataDet .= $cFecha."\t";

      // Consulta la cantidad por fecha
      for ($j=0; $j < count($mData); $j++) {
        $vMifSubservi  = array();
        $qMifSubservi  = "SELECT ";
        $qMifSubservi .= "lmsu$cAnio.mifdidxx, ";
        $qMifSubservi .= "IF(lmsu$cAnio.mifdcanx > 0, lmsu$cAnio.mifdcanx, \"\") AS mifdcanx ";
        $qMifSubservi .= "FROM $cAlfa.lmsu$cAnio ";
        $qMifSubservi .= "WHERE ";
        $qMifSubservi .= "$cAlfa.lmsu$cAnio.mifidxxx = \"{$_POST['cMifId']}\" AND ";
        $qMifSubservi .= "$cAlfa.lmsu$cAnio.subidxxx = \"{$mData[$j]['subidxxx']}\" AND ";
        $qMifSubservi .= "$cAlfa.lmsu$cAnio.mifdfecx = \"$cFecha\" AND ";
        $qMifSubservi .= "$cAlfa.lmsu$cAnio.regestxx IN (\"ACTIVO\",\"CERTIFICADO\") LIMIT 0,1";
        $xMifSubservi  = f_MySql("SELECT","",$qMifSubservi,$xConexion01,"");
        // echo $qMifSubservi . " ~ " . mysql_num_rows($xMifSubservi);
        // echo "<br>";
        if (mysql_num_rows($xMifSubservi) > 0) {
          $vMifSubservi = mysql_fetch_array($xMifSubservi);
        }
        $cDataDet .= $vMifSubservi['mifdcanx']."\t";
      }

      $cDataDet .= "\n";
    }
  }
  fwrite($fOp,$cDataCab);
  fwrite($fOp,$cDataDet);

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
?>
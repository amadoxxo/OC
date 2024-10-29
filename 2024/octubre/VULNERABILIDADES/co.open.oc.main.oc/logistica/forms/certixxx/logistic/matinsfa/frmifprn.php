<?php
  namespace openComex;
  /**
   * Generar Impreso de Excel - Matriz de Insumos Facturables.
   * --- Descripcion: Permite Generar Impreso de Excel con la Informacion de la Matriz de Insumos Facturables.
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

  // Consulta a la MIF
  $vMatriz  = array();
  $qMatriz  = "SELECT lmca$cAnio.*, ";
  $qMatriz .= "IF(lpar0150.clinomxx != \"\",lpar0150.clinomxx,(TRIM(CONCAT(lpar0150.clinomxx,\" \",lpar0150.clinom1x,\" \",lpar0150.clinom2x,\" \",lpar0150.cliape1x,\" \",lpar0150.cliape2x)))) AS clinomxx, ";
  $qMatriz .= "lpar0150.clisapxx ";
  $qMatriz .= "FROM $cAlfa.lmca$cAnio ";
  $qMatriz .= "LEFT JOIN $cAlfa.lpar0150 ON $cAlfa.lmca$cAnio.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
  $qMatriz .= "WHERE ";
  $qMatriz .= "mifidxxx = \"$cMifId\" LIMIT 0,1";
  $xMatriz  = f_MySql("SELECT","",$qMatriz,$xConexion01,"");
  if (mysql_num_rows($xMatriz) > 0) {
    $vMatriz = mysql_fetch_array($xMatriz);

    // Consulta el de deposito
    $vDeposito  = array();
    $qDeposito  = "SELECT ";
    $qDeposito .= "lpar0155.depnumxx, ";
    $qDeposito .= "lpar0155.ccoidocx, ";
    $qDeposito .= "lpar0005.pfaidxxx, ";
    $qDeposito .= "lpar0005.pfadesxx ";
    $qDeposito .= "FROM $cAlfa.lpar0155 ";
    $qDeposito .= "LEFT JOIN $cAlfa.lpar0005 ON $cAlfa.lpar0155.pfaidxxx = $cAlfa.lpar0005.pfaidxxx ";
    $qDeposito .= "WHERE ";
    $qDeposito .= "depnumxx = \"{$vMatriz['depnumxx']}\" LIMIT 0,1 ";
    $xDeposito  = f_MySql("SELECT","",$qDeposito,$xConexion01,"");
    if (mysql_num_rows($xDeposito) > 0) {
      $vDeposito = mysql_fetch_array($xDeposito);
    }

    $pArrayDatos = array();
    $pArrayDatos['cMifId']   = $cMifId;
    $pArrayDatos['cAnio']    = $cAnio;
    $pArrayDatos['cEstSubs'] = "ACTIVO";
    $pArrayDatos['cCcoIdOc'] = $vDeposito['ccoidocx'];
    $pArrayDatos['cDepNum']  = $vDeposito['depnumxx'];
    
    $mSubservicios = array();
    $mReturnSubservicios = $ObjcMatrizInsumosFacturables->fnCargarDataSubserviciosMIF($pArrayDatos);
    if ($mReturnSubservicios[0] == "true") {
      $mSubservicios = $mReturnSubservicios[1]['subservi'];

      for ($i=0; $i < count($mSubservicios); $i++) { 
        // Consulta los servicios asociados a los subservicios
        $qServicio  = "SELECT  ";
        $qServicio .= "sersapxx, ";
        $qServicio .= "serdesxx ";
        $qServicio .= "FROM $cAlfa.lpar0011 ";
        $qServicio .= "WHERE ";
        $qServicio .= "sersapxx = \"{$mSubservicios[$i]['sersapxx']}\" LIMIT 0,1 ";
        $xServicio  = f_MySql("SELECT","",$qServicio,$xConexion01,"");
        if (mysql_num_rows($xServicio) > 0) {
          $vServicio = mysql_fetch_array($xServicio);
          $mSubservicios[$i]['serdesxx'] = $vServicio['serdesxx'];
        }
      }
    }
  } else {
    $nSwitch = 1;
    $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
    $cMsj .= "No Existe la MIF Seleccionada.\n";
  }

  if ($nSwitch == 0) {
    // Inica a pintar el Excel //
    $data     = '';
    $cNomFile = "IMPRESO_".$vMatriz['comprexx'].$vMatriz['comcscxx']."_MIF_".date("YmdHis").".xls";

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
        $data .= '<td width="200px" style="font-size:14px;color:white;" bgcolor="#8E8E8E"><B>ID</B></td>';
        $data .= '<td width="200px" style="font-size:14px;"><center>'.$vMatriz['comprexx'].$vMatriz['comcscxx'].'</center></td>';
        $data .= '<td width="200px"></td>';
        $data .= '<td width="200px" style="font-size:14px;color:white;" bgcolor="#8E8E8E"><B>PERIODICIDAD</B></td>';
        $data .= '<td width="200px" style="font-size:14px;"><center>'.$vDeposito['pfadesxx'].'</center></td>';
      $data .= '</tr>';
      $data .= '<tr>';
        $data .= '<td width="200px" style="font-size:14px;color:white;" bgcolor="#8E8E8E"><B>NIT</B></td>';
        $data .= '<td width="200%" style="font-size:14px;"><center>'.$vMatriz['cliidxxx'].'</center></td>';
        $data .= '<td width="200px"></td>';
        $data .= '<td width="200px" style="font-size:14px;color:white;" bgcolor="#8E8E8E"><B>FECHA DESDE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;"><center>'.$vMatriz['miffdexx'].'</center></td>';
      $data .= '</tr>';
      $data .= '<tr>';
        $data .= '<td width="200px" style="font-size:14px;color:white;" bgcolor="#8E8E8E"><B>CODIGO SAP</B></td>';
        $data .= '<td width="200px" style="font-size:14px;"><center>'.$vMatriz['clisapxx'].'</center></td>';
        $data .= '<td width="200px"></td>';
        $data .= '<td width="200px" style="font-size:14px;color:white;" bgcolor="#8E8E8E"><B>FECHA HASTA</B></td>';
        $data .= '<td width="200px" style="font-size:14px;"><center>'.$vMatriz['miffhaxx'].'</center></td>';
      $data .= '</tr>';
      $data .= '<tr>';
        $data .= '<td width="200px" style="font-size:14px;color:white;" bgcolor="#8E8E8E"><B>CLIENTE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;"><center>'.$vMatriz['clinomxx'].'</center></td>';
        $data .= '<td width="200px"></td>';
        $data .= '<td width="200px" style="font-size:14px;color:white;" bgcolor="#8E8E8E"><B>ESTADO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;"><center>'.$vMatriz['regestxx'].'</center></td>';
      $data .= '</tr>';
      $data .= '<tr>';
        $data .= '<td width="200px" style="font-size:14px;color:white;" bgcolor="#8E8E8E"><B>DEPOSITO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;"><center>'.$vMatriz['depnumxx'].'</center></td>';
      $data .= '</tr>';
      $data .= '<tr><td></td></tr>';
    $data .= '</table>';

    $data .= '<table width="1024px" cellpadding="1" cellspacing="1" border="1" style="font-family:arial;font-size:12px;border-collapse: collapse;">';
    $nColspan = (round(count($mSubservicios)/2) == 1) ? 2 : round(count($mSubservicios)/2);
    for ($i=0; $i < count($mSubservicios); $i++) { 
      $data .= '<tr>';
        $data .= '<td width="200px" style="font-size:14px;"><B>ITEM'.($i+1).'</B></td>';
        $data .= '<td colspan="'.$nColspan.'" width="200px" style="font-size:14px;"><B><center>'.$mSubservicios[$i]['subdesxx'].'</center></B></td>';
        $data .= '<td width="200px" style="font-size:14px;"><B><center>'.$mSubservicios[$i]['ufadesxx'].'</center></B></td>';
        $data .= '<td width="200px" style="font-size:14px;"><B>'.$mSubservicios[$i]['mifdfecx'].'</B></td>';
      $data .= '</tr>';
    }
      $data .= '<tr><td></td></tr>';
    $data .= '</table>';
      
    $data .= '<table width="1024px" cellpadding="1" cellspacing="1" border="1" style="font-family:arial;font-size:12px;border-collapse: collapse;">';
      $data .= '<tr>';
        $data .= '<th rowspan="3" width="200px" style="font-size:14px;" bgcolor="#8E8E8E"><B><center>FECHA</center></B></th>';
        for ($i=0; $i < count($mSubservicios); $i++) { 
          $data .= '<td width="200px" style="font-size:14px;" bgcolor="#8E8E8E"><B><center>'.$mSubservicios[$i]['serdesxx'].'</center></B></td>';
        }
      $data .= '</tr>';
      $data .= '<tr>';
      for ($i=0; $i < count($mSubservicios); $i++) { 
        $data .= '<td width="200px" style="font-size:14px;" bgcolor="#8E8E8E"><B><center>'.$mSubservicios[$i]['subdesxx'].'</center></B></td>';
      }
      $data .= '</tr>';
      $data .= '<tr>';
      for ($i=0; $i < count($mSubservicios); $i++) { 
        $data .= '<td width="200px" style="font-size:14px;" bgcolor="#8E8E8E"><B><center>'.$mSubservicios[$i]['ufadesxx'].'</center></B></td>';
      }
      $data .= '</tr>';

      $mDataCantidad = array();
      $fechaInicio = strtotime(date($vMatriz['miffdexx']));
      $fechaFin    = strtotime(date($vMatriz['miffhaxx']));

      //Se incrementan los dias en 1
      for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){
        $cFecha = date("Y-m-d", $i);
        $data .= '</tr>';
          $data .= '<td width="200px" style="font-size:14px;">'.$cFecha.'</td>';
          for ($j=0; $j < count($mSubservicios); $j++) { 

            // Consulta la cantidad del subservicio
            $vMifSubservi  = array();
            $qMifSubservi  = "SELECT ";
            $qMifSubservi .= "lmsu$cAnio.mifdidxx, ";
            $qMifSubservi .= "lmsu$cAnio.mifidxxx, ";
            $qMifSubservi .= "IF(lmsu$cAnio.mifdcanx > 0, lmsu$cAnio.mifdcanx, \"\") AS mifdcanx ";
            $qMifSubservi .= "FROM $cAlfa.lmsu$cAnio ";
            $qMifSubservi .= "WHERE ";
            $qMifSubservi .= "$cAlfa.lmsu$cAnio.mifidxxx = \"{$vMatriz['mifidxxx']}\" AND ";
            $qMifSubservi .= "$cAlfa.lmsu$cAnio.subidxxx = \"{$mSubservicios[$j]['subidxxx']}\" AND ";
            $qMifSubservi .= "$cAlfa.lmsu$cAnio.mifdfecx = \"$cFecha\" AND ";
            $qMifSubservi .= "$cAlfa.lmsu$cAnio.regestxx IN (\"ACTIVO\",\"CERTIFICADO\") LIMIT 0,1";
            $xMifSubservi  = f_MySql("SELECT","",$qMifSubservi,$xConexion01,"");
            if (mysql_num_rows($xMifSubservi) > 0) {
              $vMifSubservi = mysql_fetch_array($xMifSubservi);
            }
            $data .= '<td width="200px" style="font-size:14px;">'.((!empty($vMifSubservi['mifdcanx'])) ? ($vMifSubservi['mifdcanx']+0) : "").'</td>';

            // Agrupa las cantidades por columnas y objeto facturable para realizar el calculo del valor total
            $mDataCantidad["{$mSubservicios[$j]['obfidxxx']}~{$j}"][] = $vMifSubservi['mifdcanx'];
          }
        $data .= '</tr>';
      }

      // Valores totales por columa de subservicios
      $data .= '<tr>';
        $data .= '<td width="200px" style="font-size:14px;" bgcolor="#8E8E8E"></td>';
        foreach ($mDataCantidad as $key => $value) {
          $vObjeto = explode("~", $key);
          $nValor  = $ObjcMatrizInsumosFacturables->fnCalcularCantidadTotal($vObjeto[0], $value);
          $data .= '<td width="200px" style="font-size:14px;" bgcolor="#8E8E8E"><B>'.$nValor.'</B></td>';

        }
      $data .= '</tr>';
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

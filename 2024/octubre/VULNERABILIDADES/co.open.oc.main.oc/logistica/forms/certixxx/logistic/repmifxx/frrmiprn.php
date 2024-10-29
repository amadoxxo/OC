<?php
  namespace openComex;
  /**
   * Generar Archivo de Excel - Reporte MIF.
   * --- Descripcion: Permite Generar Impreso de Excel con la Información del Movimiento de la MIF.
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

  // Validaciones
  // Valida que el cliente exista
  if ($gCliId != "") {
    $qCliente  = "SELECT ";
    $qCliente .= "cliidxxx ";
    $qCliente .= "FROM $cAlfa.lpar0150 ";
    $qCliente .= "WHERE ";
    $qCliente .= "cliidxxx = \"$gCliId\" AND ";
    $qCliente .= "cliclixx = \"SI\" AND ";
    $qCliente .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
    $xCliente  = f_MySql("SELECT","",$qCliente,$xConexion01,"");
    if (mysql_num_rows($xCliente) == 0) {
      $nSwitch = 1;
      $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
      $cMsj .= "El Cliente Seleccionado no Existe.\n";
    }
  }
  
  // Valida que el deposito exista
  if ($gDepNum != "") {
    $qDeposito  = "SELECT ";
    $qDeposito .= "depnumxx ";
    $qDeposito .= "FROM $cAlfa.lpar0155 ";
    $qDeposito .= "WHERE ";
    $qDeposito .= "depnumxx = \"$gDepNum\" AND ";
    $qDeposito .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
    $xDeposito  = f_MySql("SELECT","",$qDeposito,$xConexion01,"");
    if (mysql_num_rows($xDeposito) == 0) {
      $nSwitch = 1;
      $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
      $cMsj .= "El Deposito Seleccionado no Existe.\n";
    }
  }

  if ($gDesde == "" || $gHasta == "" || $gDesde == "0000-00-00" || $gHasta == "0000-00-00") {
    $nSwitch = 1;
    $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
    $cMsj .= "Debe Seleccionar un Rango de Fechas.\n";
  }
  // Fin Validaciones

  $mData = array();
  if ($nSwitch == 0) {

    // Valida si el año de instalacion del modulo es menor al año actual, para consultar sobre los dos ultimos años
    $nAnioDesde = substr($gDesde, 0, 4);
    $cAnioAnt   = ($nAnioDesde < $vSysStr['logistica_ano_instalacion_modulo']) ? $vSysStr['logistica_ano_instalacion_modulo'] : $nAnioDesde;

    for ($cAnio=$cAnioAnt;$cAnio<=date('Y');$cAnio++) {
      // Consulta el movimiento de cabecera de la MIF
      $qMifCab  = "SELECT ";
      $qMifCab .= "$cAlfa.lmca$cAnio.*, ";
      $qMifCab .= "IF($cAlfa.lpar0150.clinomxx != \"\",$cAlfa.lpar0150.clinomxx,(TRIM(CONCAT($cAlfa.lpar0150.clinomxx,\" \",$cAlfa.lpar0150.clinom1x,\" \",$cAlfa.lpar0150.clinom2x,\" \",$cAlfa.lpar0150.cliape1x,\" \",$cAlfa.lpar0150.cliape2x)))) AS clinomxx, ";
      $qMifCab .= "$cAlfa.lpar0150.clisapxx, ";
      $qMifCab .= "$cAlfa.lpar0155.depnumxx, ";
      $qMifCab .= "$cAlfa.lpar0155.ccoidocx ";
      $qMifCab .= "FROM $cAlfa.lmca$cAnio ";
      $qMifCab .= "LEFT JOIN $cAlfa.lpar0150 ON $cAlfa.lmca$cAnio.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
      $qMifCab .= "LEFT JOIN $cAlfa.lpar0155 ON $cAlfa.lmca$cAnio.depnumxx = $cAlfa.lpar0155.depnumxx ";
      $qMifCab .= "WHERE ";
      if ($gCliId != "") {
        $qMifCab .= "$cAlfa.lmca$cAnio.cliidxxx = \"$gCliId\" AND ";
      }
      if ($gDepNum != "") {
        $qMifCab .= "$cAlfa.lmca$cAnio.depnumxx = \"$gDepNum\" AND ";
      }
      $qMifCab .= "$cAlfa.lmca$cAnio.regfcrex BETWEEN \"$gDesde\" AND \"$gHasta\" ";
      if ($gEstMif != "") {
        $qMifCab .= "AND $cAlfa.lmca$cAnio.regestxx = \"$gEstMif\" ";
      }
      $xMifCab  = f_MySql("SELECT","",$qMifCab,$xConexion01,"");
      // echo $qMifCab . "<br><br>" . mysql_num_rows($xMifCab);
      if (mysql_num_rows($xMifCab) > 0) {
        while ($xRMC = mysql_fetch_array($xMifCab)) {
          $nItem = 0;

          $pArrayDatos = array();
          $pArrayDatos['cMifId']   = $xRMC['mifidxxx'];
          $pArrayDatos['cAnio']    = $cAnio;
          $pArrayDatos['cEstSubs'] = "ACTIVO";
          $pArrayDatos['cCcoIdOc'] = $xRMC['ccoidocx'];
          $pArrayDatos['cDepNum']  = $xRMC['depnumxx'];

          // Se obtiene la informacion de los subservicios asociados a la MIF
          $mReturnSubservicios = $ObjcMatrizInsumosFacturables->fnCargarDataSubserviciosMIF($pArrayDatos);
          if ($mReturnSubservicios[0] == "true") {
            $mSubservicios = $mReturnSubservicios[1]['subservi'];

            for ($i=0; $i < count($mSubservicios); $i++) {
              // Obtiene la cantidad total de cada subservicio dependiendo del objeto facturable
              $nTotal = fnTotalSubservicio($xRMC['mifidxxx'], $mSubservicios[$i]['sersapxx'], $mSubservicios[$i]['subidxxx'], $mSubservicios[$i]['obfidxxx']);

              $nInd_mData = count($mData);
              $mData[$nInd_mData]['itemxxxx'] = ($nItem += 1);                  // Numero Item
              $mData[$nInd_mData]['nummifxx'] = $xRMC['comidxxx'] ."-". $xRMC['comprexx'] ."-". $xRMC['comcscxx']; // Numero MIF
              $mData[$nInd_mData]['cliidxxx'] = $xRMC['cliidxxx'];              // Nit del Cliente
              $mData[$nInd_mData]['clisapxx'] = $xRMC['clisapxx'];              // Cod. SAP del Cliente
              $mData[$nInd_mData]['clinomxx'] = $xRMC['clinomxx'];              // Nombre del Cliente
              $mData[$nInd_mData]['depnumxx'] = $xRMC['depnumxx'];              // Numero del Deposito
              $mData[$nInd_mData]['miffdexx'] = $xRMC['miffdexx'];              // Fecha Vigencia Desde
              $mData[$nInd_mData]['miffhaxx'] = $xRMC['miffhaxx'];              // Fecha Vigencia Hasta
              $mData[$nInd_mData]['sersapxx'] = $mSubservicios[$i]['sersapxx']; // Cod. SAP del Servicio
              $mData[$nInd_mData]['serdesxx'] = $mSubservicios[$i]['serdesxx']; // Descripcion del Servicio
              $mData[$nInd_mData]['subdesxx'] = $mSubservicios[$i]['subdesxx']; // Descripcion del Subservicio
              $mData[$nInd_mData]['ufaidxxx'] = $mSubservicios[$i]['ufaidxxx']; // Codigo Unidad Facturable
              $mData[$nInd_mData]['ufadesxx'] = $mSubservicios[$i]['ufadesxx']; // Descripcion Unidad Facturable
              $mData[$nInd_mData]['fechulti'] = $mSubservicios[$i]['mifdfecx']; // Fecha Ultima Captura
              $mData[$nInd_mData]['totcalcu'] = $nTotal;                        // Total Calculo
              $mData[$nInd_mData]['regestxx'] = str_replace("_", " ", $xRMC['regestxx']); // Estado de la MIF
              $mData[$nInd_mData]['regfmodx'] = $xRMC['regfmodx'];              // Fecha Modificado de la MIF
              $mData[$nInd_mData]['cercscxx'] = $mSubservicios[$i]['cercscxx']; // N. Certificacion 
            }
          }
        }
      }
    }
  } else {
    f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.");
  }

  // echo "<pre>";
  // print_r($mData);
  // die();

  // Inica a pintar el Excel
  if (count($mData) > 0) {
    $data     = '';
    $cNomFile = "MOVIMIENTO_MIF_".date("YmdHis").".xls";

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
        $data .= '<td colspan="18" style="font-size:14px;text-align:center;"><B>ALPOPULAR SA</B></td>';
      $data .= '</tr>';
      $data .= '<tr>';
        $data .= '<td colspan="18" style="font-size:14px;text-align:center;"><B>REPORTE MATRIZ INSUMO FACTURABLE</B></td>';
      $data .= '</tr>';
      $data .= '<tr>';
        $data .= '<td colspan="18" style="font-size:14px;text-align:left;"><B>Fecha Generaci&oacute;n: '.date('Y-m-d H:i:s').'</B></td>';
      $data .= '</tr>';

      $data .= '<tr>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>ITEM</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>No. MIF</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>NIT</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>COD. SAP CLIENTE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>CLIENTE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>DEPOSITO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>FECHA DESDE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>FECHA HASTA</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>COD. SAP</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>SERVICIO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>SUB SERVICIO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>UNIDAD FACTURABLE</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>DESCRIPCI&Oacute;N UF</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>FECHA &Uacute;LTIMA CAPTURA</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>TOTAL C&Aacute;LCULO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>ESTADO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>FECHA ESTADO</B></td>';
        $data .= '<td width="200px" style="font-size:14px;text-align:center;" bgcolor="#95CF4C"><B>No. CERTIFICACI&Oacute;N</B></td>';
      $data .= '</tr>';

      for ($i=0; $i < count($mData); $i++) { 
        $data .= '<tr>';
          $data .= '<td width="200px" style="font-size:14px;text-align:right;">'.$mData[$i]['itemxxxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:center;">'.$mData[$i]['nummifxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:center;">'.$mData[$i]['cliidxxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:center;">'.$mData[$i]['clisapxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.utf8_decode($mData[$i]['clinomxx']).'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['depnumxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:right;">'.date("Y-m-d", strtotime($mData[$i]['miffdexx'])).'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:right;">'.date("Y-m-d", strtotime($mData[$i]['miffhaxx'])).'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:center;">'.$mData[$i]['sersapxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.utf8_decode($mData[$i]['serdesxx']).'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.utf8_decode($mData[$i]['subdesxx']).'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['ufaidxxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['ufadesxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:right;">'.$mData[$i]['fechulti'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:right;">'.$mData[$i]['totcalcu'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['regestxx'].'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:right;">'.date("Y-m-d", strtotime($mData[$i]['regfmodx'])).'</td>';
          $data .= '<td width="200px" style="font-size:14px;text-align:left;">'.$mData[$i]['cercscxx'].'</td>';
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
    f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");
  }


  /**
   * Obtiene el valor base de cada subservicio dependiendo.
   */
  function fnTotalSubservicio($xMifId, $xSerSap, $xSubId, $xObjeto) {
    global $cAnio; global $cAlfa; global $xConexion01;

    /**
     * Se instancia la clase de Matriz de Insumos Facturables
     */
    $ObjcMatrizInsumosFacturables = new cMatrizInsumosFacturables();

    $qMifSubservi  = "SELECT ";
    $qMifSubservi .= "lmsu$cAnio.mifidxxx, ";
    $qMifSubservi .= "IF(lmsu$cAnio.mifdcanx > 0, lmsu$cAnio.mifdcanx, \"\") AS mifdcanx ";
    $qMifSubservi .= "FROM $cAlfa.lmsu$cAnio ";
    $qMifSubservi .= "WHERE ";
    $qMifSubservi .= "lmsu$cAnio.mifidxxx = \"$xMifId\" AND ";
    $qMifSubservi .= "lmsu$cAnio.sersapxx = \"$xSerSap\" AND ";
    $qMifSubservi .= "lmsu$cAnio.subidxxx = \"$xSubId\" ";
    $xMifSubservi  = f_MySql("SELECT","",$qMifSubservi,$xConexion01,"");

    $mCantidades = array();
    if(mysql_num_rows($xMifSubservi) > 0) {
      while($xRMS = mysql_fetch_array($xMifSubservi)) {
        $nInd_mCantidades = count($mCantidades);
        $mCantidades[$nInd_mCantidades] = $xRMS['mifdcanx'];
      }
    }

    return $ObjcMatrizInsumosFacturables->fnCalcularCantidadTotal($xObjeto, $mCantidades);
  }
?>

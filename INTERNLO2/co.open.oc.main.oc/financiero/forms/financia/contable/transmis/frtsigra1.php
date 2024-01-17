<?php

  // ini_set('error_reporting', E_ERROR);
	// ini_set("display_errors", "1");

	set_time_limit(0);
	ini_set("memory_limit", "1024M");
	date_default_timezone_set('America/Bogota');

  /**
	 * Cantidad de Registros para reiniciar conexion.
	 */
	define("_NUMREG_",100);

  require_once($OPENINIT['pathdr'].'/opencomex/class/spout-2.7.3/src/Spout/Autoloader/autoload.php');
  include("../../../../libs/php/utility.php");

	use Box\Spout\Writer\WriterFactory;
	use Box\Spout\Common\Type;
	use Box\Spout\Writer\Style\Color;
	use Box\Spout\Writer\Style\Border;
	use Box\Spout\Writer\Style\StyleBuilder;
	use Box\Spout\Writer\Style\BorderBuilder;


  /**
    * Buscando la informacion de la tabla fpar0115 (Cuentas Contables).
    
    *
    * @var array
    */
  $mCuenta  = array();
  $qPucIds  = "SELECT $cAlfa.fpar0115.pucdetxx, $cAlfa.fpar0115.pucterxx , CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx ";
  $qPucIds .= "FROM $cAlfa.fpar0115 ";
  $xPucIds  = mysql_query($qPucIds,$xConexion01);
  // echo "<br>".$qPucIds."~".mysql_num_rows($xPucIds);
  while($xRPU = mysql_fetch_array($xPucIds)) {
    $mCuenta["{$xRPU['pucidxxx']}"]['pucdetxx'] = $xRPU['pucdetxx'];
    $mCuenta["{$xRPU['pucidxxx']}"]['pucterxx'] = $xRPU['pucterxx'];
  }


  /**
    * Buscando las causaciones proveedor empresa.
    *
    * @var array
    */
  $mCpe  = array();
  $qCpe  = "SELECT ";
  $qCpe .= "CONCAT(comidxxx,\"-\",comcodxx) AS comidxxx ";
  $qCpe .= "FROM $cAlfa.fpar0117 ";
  $qCpe .= "WHERE ";
  $qCpe .= "comidxxx = \"P\" AND ";
  $qCpe .= "comtipxx = \"CPE\" ";
  $xCpe = f_MySql("SELECT","",$qCpe,$xConexion01,"");
  while ($xRDB = mysql_fetch_array($xCpe)) {
    $mCpe[count($mCpe)] = $xRDB['comidxxx'];
  }

  $gAnoIni = date('Y');
  $gAnofin = date('Y');


  $mDataHoja2 = array();
  $mDataHoja3 = array();
  for ($nAno=$gAnoIni; $nAno<=$gAnofin; $nAno++) {
    // Consulta principal
    $qDatMov  = "SELECT ";
    $qDatMov .= "$cAlfa.fcod$nAno.comidxxx, ";
    $qDatMov .= "$cAlfa.fcod$nAno.comcodxx, ";
    $qDatMov .= "$cAlfa.fcod$nAno.comcscxx, ";
    $qDatMov .= "$cAlfa.fcod$nAno.comcsc2x, ";
    $qDatMov .= "$cAlfa.fcod$nAno.comseqxx, ";
    $qDatMov .= "$cAlfa.fcod$nAno.teridxxx, ";
    $qDatMov .= "$cAlfa.fcod$nAno.terid2xx, ";
    $qDatMov .= "$cAlfa.fcod$nAno.comfecxx, ";
    $qDatMov .= "$cAlfa.fcod$nAno.pucidxxx, ";
    $qDatMov .= "$cAlfa.fcod$nAno.comvlrxx, ";
    $qDatMov .= "$cAlfa.fcod$nAno.regestxx, ";
    $qDatMov .= "$cAlfa.fcod$nAno.comvlr01, ";
    $qDatMov .= "$cAlfa.fcod$nAno.comfecve, ";
    $qDatMov .= "$cAlfa.fcod$nAno.commovxx, ";
    $qDatMov .= "$cAlfa.fcod$nAno.comidcxx, ";
    $qDatMov .= "$cAlfa.fcod$nAno.comcodcx, ";
    $qDatMov .= "$cAlfa.fcod$nAno.comcsccx, ";
    $qDatMov .= "$cAlfa.fcod$nAno.ccoidxxx, ";
    $qDatMov .= "$cAlfa.fcod$nAno.sccidxxx, ";
    $qDatMov .= "$cAlfa.fcod$nAno.comctocx, ";
    $qDatMov .= "$cAlfa.fcod$nAno.comidc2x, ";
    $qDatMov .= "$cAlfa.fcod$nAno.comobsxx, ";
    $qDatMov .= "$cAlfa.fcoc$nAno.comcscxx AS comcsc1c, ";
    $qDatMov .= "$cAlfa.fcoc$nAno.comcsc2x AS comcsc2c, ";
    $qDatMov .= "$cAlfa.fcoc$nAno.teridxxx AS teridcxx, ";
    $qDatMov .= "$cAlfa.fcoc$nAno.terid2xx AS terid2cx, ";
    $qDatMov .= "$cAlfa.fcoc$nAno.comfecxx AS comfeccx, ";
    $qDatMov .= "$cAlfa.fcoc$nAno.comvlrxx AS comvlrca, ";
    $qDatMov .= "$cAlfa.fcoc$nAno.ccoidxxx AS ccoidcab, ";
    $qDatMov .= "$cAlfa.fcoc$nAno.sccidxxx AS sccidcab, ";
    $qDatMov .= "$cAlfa.fcoc$nAno.comobsxx AS comobsca, ";
    $qDatMov .= "$cAlfa.fcoc$nAno.comtcbxx, ";
    $qDatMov .= "$cAlfa.fcoc$nAno.comncbxx ";
    $qDatMov .= "FROM $cAlfa.fcod$nAno ";
    $qDatMov .= "LEFT JOIN $cAlfa.fcoc$nAno ON $cAlfa.fcod$nAno.comidxxx = $cAlfa.fcoc$nAno.comidxxx AND $cAlfa.fcod$nAno.comcodxx = $cAlfa.fcoc$nAno.comcodxx AND $cAlfa.fcod$nAno.comcscxx = $cAlfa.fcoc$nAno.comcscxx AND $cAlfa.fcod$nAno.comcsc2x = $cAlfa.fcoc$nAno.comcsc2x ";
    $qDatMov .= "WHERE ";
    if($gComId != ""){
      $qDatMov .= "fcod$nAno.comidxxx = \"$gComId\" AND ";
    }
    if($gComCod != ""){
      $qDatMov .= "fcod$nAno.comcodxx = \"$gComCod\" AND ";
    }
    if($gUsrId != ""){
      $qDatMov .= "fcod$nAno.regusrxx = \"$gUsrId\" AND ";
    }
    $qDatMov .= "fcod$nAno.regestxx = \"ACTIVO\" AND ";
    $qDatMov .= "fcod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" ";
    $qDatMov .= "ORDER BY fcod$nAno.comidxxx,fcod$nAno.comcodxx,fcod$nAno.comcscxx,ABS(fcod$nAno.comcsc2x),ABS(fcod$nAno.comseqxx) ";
    $xDatMov  = f_MySql("SELECT","",$qDatMov,$xConexion01,"");
    // echo $qDatMov."~".mysql_num_rows($xDatMov);


    $cComId   = "";
    $cComCod  = "";
    $cComCsc  = "";
    $cComCsc2 = "";
    while ($xRDM = mysql_fetch_array($xDatMov)) {
      
      // Detecta el cambio de comprobante para asignar la información de cabecera
      if ($cComId != $xRDM['comidxxx'] || $cComCod != $xRDM['comcodxx'] || $cComCsc != $xRDM['comcscxx'] || $cComCsc2 != $xRDM['comcsc2x']) {
        $nInd_mDataHoja2 = count($mDataHoja2);
        $cComId   = $xRDM['comidxxx'];
        $cComCod  = $xRDM['comcodxx'];
        $cComCsc  = $xRDM['comcscxx'];
        $cComCsc2 = $xRDM['comcsc2x'];


        // Array de la hoja 2 - Cabecera del comprobante
        $mDataHoja2[$nInd_mDataHoja2]['fciaxxxx'] = '001';
        $mDataHoja2[$nInd_mDataHoja2]['ccoidcab'] = substr($xRDM['ccoidcab'], 1);
        $mDataHoja2[$nInd_mDataHoja2]['tipodocu'] = $xRDM['comidxxx'] . substr($xRDM['comcodxx'], 1);
        $mDataHoja2[$nInd_mDataHoja2]['comcscxx'] = $xRDM['comidxxx'] == "F" ? $xRDM['comcsc1c'] : substr($xRDM['comcsc2c'], 2);
        $mDataHoja2[$nInd_mDataHoja2]['comfecxx'] = str_replace("-", "", $xRDM['comfeccx']);
        $mDataHoja2[$nInd_mDataHoja2]['teridxxx'] = $xRDM['comidxxx'] == "R" ? $xRDM['teridcxx'] : $xRDM['terid2cx'];


        // Se obtiene el primer DO del comprobante
        $cDocId  = ""; $cDocSuf = ""; $cSucId = "";
        $mDoiId = explode("|",$vCocDat['comfpxxx']);
        for ($i=0;$i<count($mDoiId);$i++) {
          if($mDoiId[$i] != "") {
            $vDoiId  = explode("~",$mDoiId[$i]);
            if($cDocId == "") {
              $cDocId  = $vDoiId[2];
              $cDocSuf = $vDoiId[3];
              $cSucId  = $vDoiId[15];
            }
          }//if($mDoiId[$i] != ""){
        }//for ($i=0;$i<count($mDoiId);$i++) {
            
        $cObservacion = "";
        if ($xRDM['comidxxx'] == 'F') {
          $cObservacion = $xRDM['comidxxx'] . substr($xRDM['comcodxx'], 1). " " . $xRDM['comcsc1c'] . " " . $cSucId . $cDocId . $cDocSuf;
        } else {
          if ($xRDM['comobsca'] != "") {
            $cObservacion = $xRDM['comobsca'];
          } else {
            $cObservacion = $xRDM['comidxxx'] . substr($xRDM['comcodxx'], 1). " " . $xRDM['comcsc1c'] . " " . $cSucId . $cDocId . $cDocSuf;
          }
        }


        $mDataHoja2[$nInd_mDataHoja2]['notasxxx'] = $cObservacion;
      }


      // Array de la hoja 3 = Detalle del movimiento contable
      if ($mCuenta[$xRDM['pucidxxx']]['pucdetxx'] != "P" && $mCuenta[$xRDM['pucidxxx']]['pucdetxx'] != "C" && $xRDM['comctocx'] != "SC") {
        $nInd_mDataHoja3 = count($mDataHoja3);
        $mDataHoja3[$nInd_mDataHoja3]['fciaxxxx'] = '001';
        $mDataHoja3[$nInd_mDataHoja3]['ccoidcab'] = substr($xRDM['ccoidcab'], 1);
        $mDataHoja3[$nInd_mDataHoja3]['tipodocu'] = $xRDM['comidxxx'] . substr($xRDM['comcodxx'], 1);
        $mDataHoja3[$nInd_mDataHoja3]['comcscxx'] = $xRDM['comidxxx'] == "F" ? $xRDM['comcsc1c'] : substr($xRDM['comcsc2c'], 2);


        if (substr($xRDM['pucidxxx'], 4) == "0000") {
          $mDataHoja3[$nInd_mDataHoja3]['cuentaxx'] = rtrim($xRDM['pucidxxx'], '0000');
        } elseif (substr($xRDM['pucidxxx'], 4) == "00") {
          $mDataHoja3[$nInd_mDataHoja3]['cuentaxx'] = rtrim($xRDM['pucidxxx'], '00');
        } else {
          $mDataHoja3[$nInd_mDataHoja3]['cuentaxx'] = $xRDM['pucidxxx'];
        }


        // Pendiente tercero
        switch ($xRDM['comidxxx']) {
          case 'R':
              if (substr($xRDM['pucidxxx'], 0, 2) == "28") {
                $mDataHoja3[$nInd_mDataHoja3]['teridxxx'] = $xRDM['teridcxx'];
              } elseif(substr($xRDM['pucidxxx'], 0, 4) == "1110") {
                $mDataHoja3[$nInd_mDataHoja3]['teridxxx'] = '';
              } else {
                $mDataHoja3[$nInd_mDataHoja3]['teridxxx'] = $xRDM['terid2cx'];
              }
          break;
          case 'L':
          case 'G':
            if (substr($xRDM['pucidxxx'], 0, 2) == "28") {
              $mDataHoja3[$nInd_mDataHoja3]['teridxxx'] = $xRDM['teridcxx'];
            } elseif(substr($xRDM['pucidxxx'], 0, 4) == "1110") {
              $mDataHoja3[$nInd_mDataHoja3]['teridxxx'] = '';
            } else {
              $mDataHoja3[$nInd_mDataHoja3]['teridxxx'] = $xRDM['teridxxx'];
            }
          break;
          case 'P':
            if (in_array($xRDM['comidxxx'] . "-" . $xRDM['comcodxx'], $mCpe)) {
              $mDataHoja3[$nInd_mDataHoja3]['teridxxx'] = $xRDM['teridcxx'];
            } else {
              if (substr($xRDM['pucidxxx'], 0, 2) == "28") {
                $mDataHoja3[$nInd_mDataHoja3]['teridxxx'] = $xRDM['teridcxx'];
              } else {
                $mDataHoja3[$nInd_mDataHoja3]['teridxxx'] = $xRDM['terid2cx'];
              }
            }
          break;
          case 'F':
            if ($xRDM['comctocx'] == "IP" || $xRDM['comctocx'] == "PCC" ) {
              $mDataHoja3[$nInd_mDataHoja3]['teridxxx'] = $xRDM['teridcxx'];
            } else {
              $mDataHoja3[$nInd_mDataHoja3]['teridxxx'] = $xRDM['terid2cx'];
            }
          break;
          case 'C':
          case 'D':
            $mDataHoja3[$nInd_mDataHoja3]['teridxxx'] = $xRDM['terid2cx'];
          break;
          default:
            // No hace nada
            break;
        }


        $mDataHoja3[$nInd_mDataHoja3]['ccoidxxx'] = substr($xRDM['ccoidxxx'], 1);
        $mDataHoja3[$nInd_mDataHoja3]['idunxxxx'] = '001';


        $mDataHoja3[$nInd_mDataHoja3]['sccidxxx'] = '';
        if (in_array(substr($xRDM['pucidxxx'], 0, 2), array("51","52","53","61"))) {
          $mDataHoja3[$nInd_mDataHoja3]['sccidxxx'] = $xRDM['sccidxxx'];
        }


        $mDataHoja3[$nInd_mDataHoja3]['codfexxx'] = '';
        $mDataHoja3[$nInd_mDataHoja3]['comtcbxx'] = '';
        $mDataHoja3[$nInd_mDataHoja3]['comncbxx'] = '';
        if (substr($xRDM['pucidxxx'], 0, 4) == "1110" || substr($xRDM['pucidxxx'], 0, 4) == "1245") {
          if ($xRDM['commovxx'] == "C") {
            $mDataHoja3[$nInd_mDataHoja3]['codfexxx'] = "1201";
          } else {
            $mDataHoja3[$nInd_mDataHoja3]['codfexxx'] = "1101";
          }


          if ($xRDM['comidxxx'] == 'R' || $xRDM['comidxxx'] == 'L' ||  $xRDM['comidxxx'] == 'G') {
            $mDataHoja3[$nInd_mDataHoja3]['comtcbxx'] = $xRDM['comtcbxx'];
            $mDataHoja3[$nInd_mDataHoja3]['comncbxx'] = $xRDM['comncbxx'];
          }
        }


        if ($xRDM['commovxx'] == "C") {
          $mDataHoja3[$nInd_mDataHoja3]['vlrcrxxx'] = $xRDM['comvlrxx'];
          $mDataHoja3[$nInd_mDataHoja3]['vlrdbxxx'] = '+000000000000000.0000';
        } else {
          $mDataHoja3[$nInd_mDataHoja3]['vlrdbxxx'] = $xRDM['comvlrxx'];
          $mDataHoja3[$nInd_mDataHoja3]['vlrcrxxx'] = '+000000000000000.0000';
        }


        if ($mCuenta[$xRDM['pucidxxx']]['pucterxx'] == "R") {
          $mDataHoja3[$nInd_mDataHoja3]['basegrav'] = ($xRCD['comvlr01'] > 0) ? $xRCD['comvlr01'] : '+000000000000000.0000';
        } else {
          $mDataHoja3[$nInd_mDataHoja3]['basegrav'] = '+000000000000000.0000';
        }


        $mDataHoja3[$nInd_mDataHoja3]['notasxxx'] = $cObservacion;
      }

			// Array de la hoja 4 - Cuentas por cobrar y saldo a favor de los comprobantes
      if ($mCuenta[$xRDM['pucidxxx']]['pucdetxx'] == "C" || $xRDM['comctocx'] == "SC") {
        $nInd_mDataHoja4 = count($mDataHoja4);
        $mDataHoja4[$nInd_mDataHoja4]['fciaxxxx'] = '001';
        $mDataHoja4[$nInd_mDataHoja4]['ccoidcab'] = substr($xRDM['ccoidcab'], 1);
        $mDataHoja4[$nInd_mDataHoja4]['tipodocu'] = $xRDM['comidxxx'] . substr($xRDM['comcodxx'], 1);
        $mDataHoja4[$nInd_mDataHoja4]['comcscxx'] = $xRDM['comidxxx'] == "F" ? $xRDM['comcsc1c'] : substr($xRDM['comcsc2c'], 2);

        if (substr($xRDM['pucidxxx'], 4) == "0000") {
          $mDataHoja4[$nInd_mDataHoja4]['cuentaxx'] = rtrim($xRDM['pucidxxx'], '0000');
        } elseif (substr($xRDM['pucidxxx'], 4) == "00") {
          $mDataHoja4[$nInd_mDataHoja4]['cuentaxx'] = rtrim($xRDM['pucidxxx'], '00');
        } else {
          $mDataHoja4[$nInd_mDataHoja4]['cuentaxx'] = $xRDM['pucidxxx'];
        }

        // Nit del tercero
        $mDataHoja4[$nInd_mDataHoja4]['ccoidxxx'] = substr($xRDM['ccoidxxx'], 1);
        $mDataHoja4[$nInd_mDataHoja4]['idunxxxx'] = '001';

        if ($xRDM['commovxx'] == "C") {
          $mDataHoja4[$nInd_mDataHoja4]['vlrcrxxx'] = $xRDM['comvlrxx'];
          $mDataHoja4[$nInd_mDataHoja4]['vlrdbxxx'] = '+000000000000000.0000';
        } else {
          $mDataHoja4[$nInd_mDataHoja4]['vlrdbxxx'] = $xRDM['comvlrxx'];
          $mDataHoja4[$nInd_mDataHoja4]['vlrcrxxx'] = '+000000000000000.0000';
        }

        $mDataHoja4[$nInd_mDataHoja4]['sucidxxx'] = '001';
        $mDataHoja4[$nInd_mDataHoja4]['tipdoccr'] = $xRDM['comidxxx'] . substr($xRDM['comcodxx'], 1);
        $mDataHoja4[$nInd_mDataHoja4]['comcscc2'] = ($xRDM['comidxxx'] == "F") ? $xRDM['comcsc1c'] : substr($xRDM['comcscc2'], 2);
        $mDataHoja4[$nInd_mDataHoja4]['comfecve'] = ($xRDM['comidxxx'] == "R" || $xRDM['comidxxx'] == "N") ? str_replace("-", "", $xRDM['comfecxx']) : str_replace("-", "", $xRDM['comfecve']);

        // Pendiente vendedor
        $mDataHoja4[$nInd_mDataHoja4]['notasxxx'] = $cObservacion;
      }
    }
  }


  $writer = WriterFactory::create(Type::XLSX); // for XLSX files
  
  $cRuta = "background5.xls";
  $excelFilePath = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/".$cRuta;

  $writer->openToFile($excelFilePath); // write data to a file or to a PHP stream
  $border = (new BorderBuilder())
      ->setBorderBottom(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
      ->setBorderLeft(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
      ->setBorderRight(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
      ->setBorderTop(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
      ->build();

  $style = (new StyleBuilder())
          ->setFontBold()
          ->setFontSize(11)
          ->setFontColor(Color::BLACK)
          ->setShouldWrapText(false)
          ->setBorder($border)
          ->build();

  // Hoja 1
  $writer->getCurrentSheet()->setName('Inicial');

  $mColumnasInicial  = [
    'F_CIA'
  ];

  $writer->addRowWithStyle($mColumnasInicial, $style);

  // Crear una nueva hoja
  $writer->addNewSheetAndMakeItCurrent();
  // Hoja 2
  $writer->getCurrentSheet()->setName('Documentocontable');

  $mColumnasDocumentocontable = [
    'F_CIA',
    'F350_ID_CO',
    'F350_ID_TIPO_DOCTO',
    'F350_CONSEC_DOCTO',
    'F350_FECHA',
    'F350_ID_TERCERO',
    'F350_NOTAS'
  ];

  $writer->addRowWithStyle($mColumnasDocumentocontable, $style);

  for ($i=0; $i<count($mDataHoja2); $i++) {
    $writer->addRowWithStyle($mDataHoja2[$i], $style);
  }

  // Crear una nueva hoja
  $writer->addNewSheetAndMakeItCurrent();
  // Hoja 3
  $writer->getCurrentSheet()->setName('Movimientocontable');

  $mColumnasMovimientocontable = [
    'F_CIA',
    'F350_ID_CO',
    'F350_ID_TIPO_DOCTO',
    'F350_CONSEC_DOCTO',
    'F351_ID_AUXILIAR',
    'F351_ID_TERCERO',
    'F351_ID_CO_MOV',
    'F351_ID_UN',
    'F351_ID_CCOSTO',
    'F351_ID_FE',
    'F351_VALOR_DB',
    'F351_VALOR_CR',
    'F351_BASE_GRAVABLE',
    'F351_DOCTO_BANCO',
    'F351_NRO_DOCTO_BANCO',
    'F351_NOTAS',
  ];

  $writer->addRowWithStyle($mColumnasMovimientocontable, $style);
  for ($i=0; $i <count($mDataHoja3); $i++) { 
    $writer->addRowWithStyle($mDataHoja3[$i], $style);
  }


  // Crear una nueva hoja
  $writer->addNewSheetAndMakeItCurrent();
  // Hoja 4
  $writer->getCurrentSheet()->setName('MovimientoCxC');

  $mColumnasMovimientoCxC = [
      'F_CIA',
      'F350_ID_CO',
      'F350_ID_TIPO_DOCTO',
      'F350_CONSEC_DOCTO',
      'F351_ID_AUXILIAR',
      'F351_ID_TERCERO',
      'F351_ID_CO_MOV',
      'F351_ID_UN',
      'F351_ID_CCOSTO',
      'F351_VALOR_DB',
      'F351_VALOR_CR',
      'F353_ID_SUCURSAL',
      'F353_ID_TIPO_DOCTO_CRUCE',
      'F353_CONSEC_DOCTO_CRUCE',
      'F353_FECHA_VCTO',
      'F353_FECHA_DSCTO_PP',
      'F354_TERCERO_VEND',
      'F354_NOTAS'
  ];

  $writer->addRowWithStyle($mColumnasMovimientoCxC, $style);

  // Crear una nueva hoja
  $writer->addNewSheetAndMakeItCurrent();
  // Hoja 5
  $writer->getCurrentSheet()->setName('MovimientoCxP');

  $mColumnasMovimientoCxP = [
      'F_CIA',
      'F350_ID_CO',
      'F350_ID_TIPO_DOCTO',
      'F350_CONSEC_DOCTO',
      'F351_ID_AUXILIAR',
      'F351_ID_TERCERO',
      'F351_ID_CO_MOV',
      'F351_ID_UN',
      'F351_VALOR_DB',
      'F351_VALOR_CR',
      'F353_ID_SUCURSAL',
      'F353_PREFIJO_CRUCE',
      'F353_CONSEC_DOCTO_CRUCE',
      'F353_FECHA_VCTO',
      'F353_FECHA_DSCTO_PP',
      'F353_FECHA_DOCTO_CRUCE',
      'F354_NOTAS'
  ];

  $writer->addRowWithStyle($mColumnasMovimientoCxP, $style);

  // Crear una nueva hoja
  $writer->addNewSheetAndMakeItCurrent();
  // Hoja 6
  $writer->getCurrentSheet()->setName('Final');

  $mColumnasFinal = [
      'F_CIA'
  ];

  $writer->addRowWithStyle($mColumnasFinal, $style);

  $writer->close();

  if (file_exists($excelFilePath)) {
    $cDownLoadFilename = $cDownLoadFilename !== null ? $cDownLoadFilename : basename($excelFilePath);

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . $cDownLoadFilename);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($excelFilePath));

    ob_clean();
    flush();
    readfile($excelFilePath);
    exit;
  }
?>
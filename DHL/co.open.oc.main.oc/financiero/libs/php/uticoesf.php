<?php

/**
 * uticietl.php : Utility de Clases Para el procesamiento de las condiciones especiales de de Facturacion
 *                Para importaciones, exportaciones, Transito y Otros
 *
 * Este script contiene la colecciones de clases para el procesamiento de las condiciones especiales de de Facturacion
 * @author Johana Arboleda Ramos <johana.arboleda@openits.co>
 * @package openComex
 */

date_default_timezone_set('America/Bogota');

// ini_set('error_reporting', E_ALL);
// ini_set('error_reporting', E_ERROR);
// ini_set("display_errors","1");
class cConEspFac {
  /**
   * Metodo para Traer las condiciones especiales del DO
   */
  function fnCondiconesEspecialesDO($pArrayParametros) {

    global $xConexion01; global $cAlfa; global $vSysStr;

    /**
     * Recibe como Parametro una Matriz con las siguientes posiciones:
     * 
     * $pArrayParametros['sucidxxx'] //Sucursal
     * $pArrayParametros['docidxxx'] //Do
     * $pArrayParametros['docsufxx'] //Sufijo
     * $pArrayParametros['moduloxx'] //Modulo Origen IMPORTACION, EXPORTACION, TRANSITO u OTROS
     * 
     * Retorna
     * $mReturn[1] Datos
     */

    /**
     * Variable para hacer el retorno.
     * El listado de errores se retorna a partir de la posicion 2
     * @var array
     */
    $mReturn    = array();
    $mReturn[0] = "";
    $mReturn[1] = "";

    /**
     * Variable para saber si hay o no errores de validacion.
     * @var number
     */
    $nSwitch = 0;

    //Trayendo datos del DO
    $qDocDat  = "SELECT * ";
    $qDocDat .= "FROM $cAlfa.sys00121 ";
    $qDocDat .= "WHERE ";
    $qDocDat .= "sucidxxx = \"{$pArrayParametros['sucidxxx']}\" AND ";
    $qDocDat .= "docidxxx = \"{$pArrayParametros['docidxxx']}\" AND ";
    $qDocDat .= "docsufxx = \"{$pArrayParametros['docsufxx']}\" LIMIT 0,1 ";
    $xDocDat  = f_MySql("SELECT","",$qDocDat,$xConexion01,"");
    // f_Mensaje(__FILE__,__LINE__,$qDocDat."~".mysql_num_rows($xDocDat));
    if (mysql_num_rows($xDocDat) == 0) {
      $nSwitch = 1;
      $mReturn[count($mReturn)] = "El DO [{$pArrayParametros['sucidxxx']}-{$pArrayParametros['docidxxx']}-{$pArrayParametros['docsufxx']}] No Existe.";
    } else {
      $xRDD = mysql_fetch_assoc($xDocDat);

      //Condiciones especiales por tipo de operacion y/o base de datos
      switch ($xRDD['doctipxx']) {
        case "IMPORTACION":
          if($cAlfa == "DHLEXPRE" || $cAlfa == "DEDHLEXPRE" || $cAlfa == "TEDHLEXPRE") {
            //Consulta SIAI0200 para obtenr el valor del campo DO Parcial
            $qDatDoxx  = "SELECT ";
            $qDatDoxx .= "$cAlfa.SIAI0200.DOIPARXX ";
            $qDatDoxx .= "FROM $cAlfa.SIAI0200 ";
            $qDatDoxx .= "WHERE ";
            $qDatDoxx .= "$cAlfa.SIAI0200.DOIIDXXX = \"{$xRDD['docidxxx']}\" AND ";
            $qDatDoxx .= "$cAlfa.SIAI0200.DOISFIDX = \"{$xRDD['docsufxx']}\" AND ";
            $qDatDoxx .= "$cAlfa.SIAI0200.ADMIDXXX = \"{$xRDD['sucidxxx']}\" LIMIT 0,1";
            $xDatDoxx  = f_MySql("SELECT","",$qDatDoxx,$xConexion01,"");
            $xRDD['docdopar'] = "NO";
            if (mysql_num_rows($xDatDoxx) > 0) {
              $vDatDoxx = mysql_fetch_assoc($xDatDoxx);
              if ($vDatDoxx['DOIPARXX'] != "" && $vDatDoxx['DOIPARXX'] == "SI") {
                $xRDD['docdopar'] = "SI";
              }
            }
          }

          //Se traen los productos que son de tipo PRECINTOS
          $qProPre = "SELECT ptoidxxx FROM $cAlfa.fpar0132 WHERE ptotipxx = \"PRE\" AND regestxx = \"ACTIVO\"";

          //Busco si hay PRECINTOS asignados para el DO en estado "PRVGASTO", "CONDO", "ANULADO"
          $qPreAsi  = "SELECT sucidxxx, doccomex, docsufxx ";
          $qPreAsi .= "FROM $cAlfa.ffoi0000 ";
          $qPreAsi .= "WHERE ";
          $qPreAsi .= "sucidxxx = \"{$xRDD['sucidxxx']}\" AND ";
          $qPreAsi .= "doccomex = \"{$xRDD['docidxxx']}\" AND ";
          $qPreAsi .= "docsufxx = \"{$xRDD['docsufxx']}\" AND ";
          $qPreAsi .= "(ptoidxxx IN ($qProPre)) AND ";
          $qPreAsi .= "regestxx IN (\"PRVGASTO\",\"CONDO\",\"ANULADO\") LIMIT 0,1 ";
          $xPreAsi  = f_MySql("SELECT","",$qPreAsi,$xConexion01,"");
          //f_Mensaje(__FILE__,__LINE__,$qPreAsi."~".mysql_num_rows($xPreAsi));
          $xRDD['prescinx'] = "NO";
          if (mysql_num_rows($xPreAsi) > 0) {
            $xRDD['prescinx'] = "SI";
          }
        break;
        case "EXPORTACION":
          ##Validacion para traer valor FOB ##
          if($xRDD['docfobxx'] == "" || $xRDD['docfobxx'] == 0 && $xRDD['docfobxx'] != "NO"){
            $qVlrFob  = "SELECT ";
            $qVlrFob .= "SUM(dextofob) AS dextofob ";
            $qVlrFob .= "FROM $cAlfa.siae0200 ";
            $qVlrFob .= "WHERE ";
            $qVlrFob .= "dexidxxx = \"{$pArrayParametros['docidxxx']}\" AND ";
            $qVlrFob .= "admidxxx = \"{$pArrayParametros['sucidxxx']}\" ";
            $qVlrFob .= "GROUP BY dexidxxx, admidxxx ";
            $xVlrFob  = f_MySql("SELECT","",$qVlrFob,$xConexion01,"");
            //f_Mensaje(__FILE__,__LINE__,$qVlrFob." - ".mysql_num_rows($xVlrFob));
            $vVlrFob  = mysql_fetch_array($xVlrFob);

            $xRDD['docfobxx'] = number_format($vVlrFob['dextofob'],2,".","");
            ##Fin Validacion para traer valor FOB##
          }//if($xRDD['docfobxx'] == "" || $xRDD['docfobxx'] == 0){

          if($cAlfa == "ALPOPULX" || $cAlfa == "TEALPOPULP" || $cAlfa == "DEALPOPULP"){
            if($xRDD['doctrmxx'] == "" || $xRDD['doctrmxx'] == 0){
              $xRDD['doctrmxx'] = f_Buscar_Tasa_Cambio(date('Y-m-d'),"USD");
            }
          }//if($cAlfa == "ALPOPULX" || $cAlfa == "DESARROL" || $cAlfa == "FACTURAX"){
        break;
        case "TRANSITO":
          //Si la consulta del DO se realiza desde el modulo de importacion
          //Corresponde a la funcionalidad que permite asignar tarifas de importacion a un modulo DO de transito
          if ($pArrayParametros['moduloxx'] == "IMPORTACION") {
            if ($xRDD['doctipxx'] == "TRANSITO" && $xRDD['doctarim'] == "SI") {
              $xRDD['doctipxx'] = "IMPORTACION";
            }
          }
        break;
        case "OTROS":
          $xRDD['doctepxx'] = "GENERAL";
          $xRDD['doctepid'] = "100";
        break;
        default:
          $nSwitch = 1;
          $mReturn[count($mReturn)] = "El Tipo de Operacion [{$pArrayParametros['doctipxx']}] No es Valido.";
        break;
      }
    }

    // Trayendo informacion adicional del DO
    if ($nSwitch == 0) {
      //Nombre del cliente
      $qCliNom  = "SELECT ";
      $qCliNom .= "IF(CLINOMXX != \"\",CLINOMXX,CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) AS CLINOMXX ";
      $qCliNom .= "FROM $cAlfa.SIAI0150 ";
      $qCliNom .= "WHERE ";
      $qCliNom .= "CLIIDXXX = \"{$xRDD['cliidxxx']}\" AND ";
      $qCliNom .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
      $xCliNom  = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
      $xRCN = mysql_fetch_assoc($xCliNom);
      $xRDD['clinomxx'] = $xRCN['CLINOMXX'];

      // Si aplica las tarifas del facturar a
      $xRDD['docfacoe'] = ($xRDD['docfacoe'] != "") ? $xRDD['docfacoe'] : $xRDD['cliidxxx'];

      //Datos del facturar a
      $qFacA  = "SELECT ";
      $qFacA .= "CLIIDXXX, ";
      $qFacA .= "IF(CLINOMXX != \"\",CLINOMXX,CONCAT(CLINOM1X,' ',CLINOM2X,' ',CLIAPE1X,' ',CLIAPE2X)) AS CLINOMXX ";
      $qFacA .= "FROM $cAlfa.SIAI0150 ";
      $qFacA .= "WHERE ";
      $qFacA .= "CLIIDXXX = \"{$xRDD['docfacoe']}\" AND ";
      $qFacA .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
      $xFacA  = f_MySql("SELECT","",$qFacA,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qFacA."~".mysql_num_rows($xFacA));
      $xRFA = mysql_fetch_assoc($xFacA);
      $xRDD['teridint'] = $xRFA['CLIIDXXX'];
      $xRDD['ternomin'] = $xRFA['CLINOMXX'];
    }

    if ($nSwitch == 0) {
      $mReturn[0] = "true";
      $mReturn[1] = $xRDD;
    } else {
      $mReturn[0] = "false";
    }

    return $mReturn;
  }

  /**
   * Metodo para Traer las Descripciones de las condiciones especiales por tipo de operacion
   */
  function fnDescripcionCondicones($pArrayParametros) {

    global $xConexion01; global $cAlfa; global $vSysStr;

    /**
     * Recibe como Parametro una Matriz con las siguientes posiciones:
     * 
     * $pArrayParametros['cliidxxx'] //Cliente
     * $pArrayParametros['teridint'] //Facturar a
     * $pArrayParametros['gtaidxxx'] //Grupo Tarifa
     * $pArrayParametros['doctepxx'] //Tarifa por GENERAL, PROYECTO o PRODUCTO
     * $pArrayParametros['doctepid'] //Id Proyecto o Producto
     * $pArrayParametros['sucidxxx'] //Sucursal
     * $pArrayParametros['doctipxx'] //Tipo de operación IMPORTACION, EXPORTACION, TRANSITO u OTROS
     * $pArrayParametros['docmtrxx'] //Modo de Transporte
     * $pArrayParametros['tartipxx'] //Tipo de Tarifa
     * 
     * Retorna
     * $mReturn[1] Datos, que es una matriz con los siguientes datos:
     * $mDatos[nombreCampoSys00121]['camcones'] => Nombre radio button
     * $mDatos[nombreCampoSys00121]['camcone2'] => Nombre en el formulario del campo de texto principal (opcional)
     * $mDatos[nombreCampoSys00121]['camcone3'] => Nombre en el formulario del campo de texto adicional (opcional)
     * $mDatos[nombreCampoSys00121]['camcamp3'] => Nombre en la sys00121 del campo de texto adicional (opcional, obligatorio si se asigna camcone3)
     * $mDatos[nombreCampoSys00121]['descones'] => Descripcion por defecto de la condicion especial
     * $mDatos[nombreCampoSys00121]['moscanho'] => Variable para indicar si mostrar o no el bloque de cantidad de horas de reconocimiento
     * $mDatos[nombreCampoSys00121]['accionjs'] => Condicon en el metodo f_Prende_Check para realizar acciones sobre los radio en el formulario
     */

    /**
     * Variable para hacer el retorno.
     * El listado de errores se retorna a partir de la posicion 2
     * @var array
     */
    $mReturn    = array();
    $mReturn[0] = ""; // true o false
    $mReturn[1] = ""; // Condicones especiales

    /**
     * Variable para saber si hay o no errores de validacion.
     * @var number
     */
    $nSwitch = 0;

    /**
     * Variable con los datos retornados
     * @var array
     */
    $mDatos = array();

    /**
     * Campos por defecto por tipo de operacion
     * @var array
     */
    $mCampos = array();

    switch ($pArrayParametros['doctipxx']) {
      case "IMPORTACION":
        // Matriz con los nombres de los radio y su descripcion
        // El indice principal corresponde al nombre del campo en la tabla sys00121
        // El indicie camcones corresponde al nombre del Radio Button
        // El indicie camcone2 corresponde al nombre del campo donde se digita el valor
        $mDatos['docddxxx']['camcones'] = 'oDocDd';
        $mDatos['docddxxx']['camcone2'] = 'cDocDd';
        $mDatos['docddxxx']['descones'] = 'APLICA DESCARGUE DIRECTO';

        $mDatos['docvbxxx']['camcones'] = 'oDocVb';
        $mDatos['docvbxxx']['camcone2'] = 'cDocVb';
        $mDatos['docvbxxx']['descones'] = 'APLICAN VISTOS BUENOS ANTE ENTIDADES COMPETENTES';

        $mDatos['docarxxx']['camcones'] = 'oDocCar';
        $mDatos['docarxxx']['camcone2'] = 'cDocCar';
        $mDatos['docarxxx']['descones'] = 'APLICA RECONOCIMIENTO';
        
        $mDatos['docaflex']['camcones'] = 'oDocFle';
        $mDatos['docaflex']['camcone2'] = 'cDocFle';
        $mDatos['docaflex']['descones'] = 'APLICA FLETE';

        $mDatos['doccaxxx']['camcones'] = 'oDocCa';
        $mDatos['doccaxxx']['camcone2'] = 'cDocCa';
        $mDatos['doccaxxx']['descones'] = 'APLICAN CLASIFICACIONES ARANCELARIAS';

        $mDatos['docculrx']['camcones'] = 'oDocCulr';
        $mDatos['docculrx']['camcone2'] = '';
        $mDatos['docculrx']['descones'] = 'APLICA COMISION UNICA PARA LEGALIZACIONES Y REEMBARQUES';

        $mDatos['doceuxxx']['camcones'] = 'oDocEu';
        $mDatos['doceuxxx']['camcone2'] = '';
        $mDatos['doceuxxx']['descones'] = 'APLICA ENTREGA URGENTE';

        $mDatos['dochrexx']['camcones'] = 'oDocHre';
        $mDatos['dochrexx']['camcone2'] = 'cDocHre';
        $mDatos['dochrexx']['camcone3'] = 'cDocHrea';
        $mDatos['dochrexx']['camcamp3'] = 'dochreax';
        $mDatos['dochrexx']['descones'] = 'APLICAN HORAS DE RECONOCIMIENTO';
        $mDatos['dochrexx']['moscanho'] = 'SI'; // Variable para indicar si mostrar o no el bloque de cantidad de horas de reconocimiento
        
        $mDatos['doccagxx']['camcones'] = 'oDocCag';
        $mDatos['doccagxx']['camcone2'] = '';
        $mDatos['doccagxx']['descones'] = 'APLICA CARGA AL GRANEL';
        $mDatos['doccagxx']['accionjs'] = 'CAG';

        $mDatos['docc20xx']['camcones'] = 'oDocC20';
        $mDatos['docc20xx']['camcone2'] = 'cDocC20';
        $mDatos['docc20xx']['descones'] = 'APLICAN CANTIDAD DE CONTENEDORES DE 20 PIES';
        $mDatos['docc20xx']['accionjs'] = 'C20';

        $mDatos['docc40xx']['camcones'] = 'oDocC40';
        $mDatos['docc40xx']['camcone2'] = 'cDocC40';
        $mDatos['docc40xx']['descones'] = 'APLICAN CANTIDAD DE CONTENEDORES DE 40 PIES';
        $mDatos['docc40xx']['accionjs'] = 'C40';

        $mDatos['docc40hc']['camcones'] = 'oDocC40Hc';
        $mDatos['docc40hc']['camcone2'] = 'cDocC40Hc';
        $mDatos['docc40hc']['descones'] = 'APLICAN CANTIDAD DE CONTENEDORES DE 40 PIES HC';
        $mDatos['docc40hc']['accionjs'] = 'C40HC';

        $mDatos['doccsxxx']['camcones'] = 'oDocCs';
        $mDatos['doccsxxx']['camcone2'] = '';
        $mDatos['doccsxxx']['descones'] = 'APLICA CARGA SUELTA';
        $mDatos['doccsxxx']['accionjs'] = 'CS';

        $mDatos['doccsuxx']['camcones'] = 'oDocCsu';
        $mDatos['doccsuxx']['camcone2'] = 'cDocCsu';
        $mDatos['doccsuxx']['descones'] = 'APLICAN UNIDADES DE CARGA SUELTA';
        $mDatos['doccsuxx']['accionjs'] = 'CSU';

        $mDatos['docfurxx']['camcones'] = 'oDocFur';
        $mDatos['docfurxx']['camcone2'] = 'cDocFur';
        $mDatos['docfurxx']['descones'] = 'APLICA CANTIDAD DE FURGONES';
        $mDatos['docfurxx']['accionjs'] = 'FUR';

        $mDatos['docpiexx']['camcones'] = 'oDocPie';
        $mDatos['docpiexx']['camcone2'] = 'cDocPie';
        $mDatos['docpiexx']['descones'] = 'APLICAN PIEZAS';

        $mDatos['doccfvxx']['camcones'] = 'oDocCfv';
        $mDatos['doccfvxx']['camcone2'] = 'cDocCfv';
        $mDatos['doccfvxx']['descones'] = 'APLICAN FORMULARIOS VIRTUALES - DECLARACION DE IMPORTACION';

        $mDatos['docuapxx']['camcones'] = 'oDocUap';
        $mDatos['docuapxx']['camcone2'] = '';
        $mDatos['docuapxx']['descones'] = 'APLICA ADMINISTRACION DE UAP';

        $mDatos['doctmpxx']['camcones'] = 'oDocTmp';
        $mDatos['doctmpxx']['camcone2'] = '';
        $mDatos['doctmpxx']['descones'] = 'APLICA CONTROL DE TEMPORALES';

        $mDatos['docssxxx']['camcones'] = 'oDocSs';
        $mDatos['docssxxx']['camcone2'] = 'cDocSs';
        $mDatos['docssxxx']['descones'] = 'APLICAN SELLOS DE SEGURIDAD';

        $mDatos['docasexx']['camcones'] = 'oDocAse';
        $mDatos['docasexx']['camcone2'] = 'cDocAse';
        $mDatos['docasexx']['descones'] = 'APLICA SOLICITUD DE ESTAMPILLAS';

        $mDatos['docastxx']['camcones'] = 'oDocAst';
        $mDatos['docastxx']['camcone2'] = 'cDocAst';
        $mDatos['docastxx']['descones'] = 'APLICA SOLICITUD TORNAGUIAS';

        $mDatos['docadfxx']['camcones'] = 'oDocAdf';
        $mDatos['docadfxx']['camcone2'] = 'cDocAdf';
        $mDatos['docadfxx']['descones'] = 'APLICA DILIGENCIAMIENTO FONDOCUENTA';

        $mDatos['docamscx']['camcones'] = 'oDocAmsc';
        $mDatos['docamscx']['camcone2'] = '';
        $mDatos['docamscx']['descones'] = 'APLICA MUESTRA SIN VALOR COMERCIAL';

        $mDatos['doccsixx']['camcones'] = 'oDocCsi';
        $mDatos['doccsixx']['camcone2'] = '';
        $mDatos['doccsixx']['descones'] = 'APLICA COTIZACION SEGURO';

        $mDatos['docfeuxx']['camcones'] = 'oDocFeu';
        $mDatos['docfeuxx']['camcone2'] = 'cDocFeu';
        $mDatos['docfeuxx']['descones'] = 'APLICA FORMULARIO ENTREGA URGENTE';

        $mDatos['doctvdxx']['camcones'] = 'oDocTvd';
        $mDatos['doctvdxx']['camcone2'] = '';
        $mDatos['doctvdxx']['descones'] = 'APLICA ALMACENAJE DEPOSITO DEL GRUPO';
        $mDatos['doctvdxx']['accionjs'] = 'TVI';

        $mDatos['doctvaxx']['camcones'] = 'oDocTva';
        $mDatos['doctvaxx']['camcone2'] = '';
        $mDatos['doctvaxx']['descones'] = 'APLICA AGENTE DE CARGA DEL GRUPO';
        $mDatos['doctvaxx']['accionjs'] = 'TVI';

        $mDatos['docvbinv']['camcones'] = 'oDocVbinv';
        $mDatos['docvbinv']['camcone2'] = 'cDocVbinv';
        $mDatos['docvbinv']['descones'] = 'APLICA VISTO BUENO INVIMA';

        $mDatos['docvbsic']['camcones'] = 'oDocVbsic';
        $mDatos['docvbsic']['camcone2'] = 'cDocVbsic';
        $mDatos['docvbsic']['descones'] = 'APLICA VISTO BUENO SIC';

        $mDatos['docvbica']['camcones'] = 'oDocVbica';
        $mDatos['docvbica']['camcone2'] = 'cDocVbica';
        $mDatos['docvbica']['descones'] = 'APLICA VISTO BUENO ICA';

        $mDatos['docvbmxx']['camcones'] = 'oDocVbm';
        $mDatos['docvbmxx']['camcone2'] = 'cDocVbm';
        $mDatos['docvbmxx']['descones'] = 'APLICA VISTO BUENO MINAMBIENTE';

        $mDatos['doctramx']['camcones'] = 'oDocTram';
        $mDatos['doctramx']['camcone2'] = 'cDocTram';
        $mDatos['doctramx']['descones'] = 'APLICA TRAMITE MINCOMERCIO';

        $mDatos['doctltix']['camcones'] = 'oDocTlti';
        $mDatos['doctltix']['camcone2'] = 'cDocTlti';
        $mDatos['doctltix']['descones'] = 'APLICA TRAMITE LICENCIA DE TRANSPORTE INGEOMINAS';

        $mDatos['doctliix']['camcones'] = 'oDocTlii';
        $mDatos['doctliix']['camcone2'] = 'cDocTlii';
        $mDatos['doctliix']['descones'] = 'APLICA TRAMITE LICENCIA DE IMPORTACION INGEOMINAS';

        $mDatos['docmavxx']['camcones'] = 'oDocMav';
        $mDatos['docmavxx']['camcone2'] = 'cDocMav';
        $mDatos['docmavxx']['descones'] = 'APLICA MEDICION Y ACOMPA&NtildeAMIENTO VEHICULO DURANTE EL TRANSPORTE DE MATERIAL RADIACTIVO';

        $mDatos['docesdxx']['camcones'] = 'oDocEsd';
        $mDatos['docesdxx']['camcone2'] = 'cDocEsd';
        $mDatos['docesdxx']['descones'] = 'APLICA ELABORACION SHIPPER DECLARATION(DGD)';

        $mDatos['docacalx']['camcones'] = 'oDocAcal';
        $mDatos['docacalx']['camcone2'] = 'cDocAcal';
        $mDatos['docacalx']['descones'] = 'APLICA ACARREO LOCAL';

        $mDatos['docsexxx']['camcones'] = 'oDocSe';
        $mDatos['docsexxx']['camcone2'] = 'cDocSe';
        $mDatos['docsexxx']['descones'] = 'APLICA SERVICIO ESCOLTA';

        $mDatos['doccpdxx']['camcones'] = 'oDocCpd';
        $mDatos['doccpdxx']['camcone2'] = 'cDocCpd';
        $mDatos['doccpdxx']['descones'] = 'APLICA CANCELACION POLIZA DIAN';

        $mDatos['docecpxx']['camcones'] = 'oDocEcp';
        $mDatos['docecpxx']['camcone2'] = 'cDocEcp';
        $mDatos['docecpxx']['descones'] = 'APLICA EMISION CERTIFICADO POLIZA';

        $mDatos['docstnxx']['camcones'] = 'oDocStn';
        $mDatos['docstnxx']['camcone2'] = 'cDocStn';
        $mDatos['docstnxx']['descones'] = 'APLICA SERVICIO NACIONAL DE TRANSPORTE';
        
        $mDatos['docaregx']['camcones'] = 'oDocAre';
        $mDatos['docaregx']['camcone2'] = 'cDocAre';
        $mDatos['docaregx']['descones'] = 'APLICA REGISTRO';

        $mDatos['docvedim']['camcones'] = 'oDocDim';
        $mDatos['docvedim']['camcone2'] = 'cDocDim';
        $mDatos['docvedim']['descones'] = 'APLICA VALOR ELABORACION DE DECLARACION DE IMPORTACION  ';

        $mDatos['docvedav']['camcones'] = 'oDocDav';
        $mDatos['docvedav']['camcone2'] = 'cDocDav';
        $mDatos['docvedav']['descones'] = 'APLICA VALOR ELABORACION DE DECLARACION DE VALOR';

        $mDatos['dochadav']['camcones'] = 'oDocHaDav';
        $mDatos['dochadav']['camcone2'] = 'cDocHaDav';
        $mDatos['dochadav']['descones'] = 'APLICA HOJAS ADICIONALES - ELABORACION DECLARACION DE VALOR';

        $mDatos['docetfmm']['camcones'] = 'oDocEtfMm';
        $mDatos['docetfmm']['camcone2'] = 'cDocEtfMm';
        $mDatos['docetfmm']['descones'] = 'APLICA ELABORACION Y TRAMITE FORMULARIO MOVIMIENTO MERCANCIA EN ZONAS FRANCA';

        $mDatos['docppxxx']['camcones'] = 'oDocPp';
        $mDatos['docppxxx']['camcone2'] = '';
        $mDatos['docppxxx']['descones'] = 'APLICA PRESENCIA DE PERSONAL CARGUE/DESCARGUE DE MERCANCIAS';

        $mDatos['docreexx']['camcones'] = 'oDocRee';
        $mDatos['docreexx']['camcone2'] = 'cDocRee';
        $mDatos['docreexx']['descones'] = 'APLICA REEXPEDICION';

        $mDatos['docverme']['camcones'] = 'oDocVerMe';
        $mDatos['docverme']['camcone2'] = 'cDocVerMe';
        $mDatos['docverme']['descones'] = 'APLICA VERIFICACION DE MERCANCIAS';

        $mDatos['docacaxx']['camcones'] = 'oDocAcar';
        $mDatos['docacaxx']['camcone2'] = 'cDocAcar';
        $mDatos['docacaxx']['descones'] = 'APLICA ACARREO';

        $mDatos['docafdmx']['camcones'] = 'oDocAfdm';
        $mDatos['docafdmx']['camcone2'] = 'cDocAfdm';
        $mDatos['docafdmx']['descones'] = 'APLICA FORMULARIO DAV MAGNETICAS';

        $mDatos['docaecor']['camcones'] = 'oDocAecor';
        $mDatos['docaecor']['camcone2'] = 'cDocAecor';
        $mDatos['docaecor']['descones'] = 'APLICA ELABORACI&Oacute;N CERTIFICADO DE ORIGEN';

        $mDatos['docaifxx']['camcones'] = 'oDocAif';
        $mDatos['docaifxx']['camcone2'] = 'cDocAif';
        $mDatos['docaifxx']['descones'] = 'APLICA INTRODUCCION A LA FERIA';

        $mDatos['docpalxx']['camcones'] = 'oDocPal';
        $mDatos['docpalxx']['camcone2'] = 'cDocPal';
        $mDatos['docpalxx']['descones'] = 'APLICA PERIODOS DE ALMACENAMIENTO';

        $mDatos['docadavm']['camcones'] = 'oDocAdavm';
        $mDatos['docadavm']['camcone2'] = 'cDocAdavm';
        $mDatos['docadavm']['descones'] = 'APLICA FORMULARIO DAV MAGNETICAS - IP';

        $mDatos['docmts3x']['camcones'] = 'oDocMts3';
        $mDatos['docmts3x']['camcone2'] = 'cDocMts3';
        $mDatos['docmts3x']['camcone3'] = 'cDocInt';
        $mDatos['docmts3x']['camcamp3'] = 'docintxx';
        $mDatos['docmts3x']['descones'] = 'APLICAN INTERVALOS Y METROS CUBICOS';
        
        $mDatos['docsrexx']['camcones'] = 'oDocSre';
        $mDatos['docsrexx']['camcone2'] = 'cDocSre';
        $mDatos['docsrexx']['descones'] = 'APLICAN SERIALES DE RECONOCIMIENTO';

        $mDatos['docaprox']['camcones'] = 'oDocApr';
        $mDatos['docaprox']['camcone2'] = 'cDocApr';
        $mDatos['docaprox']['descones'] = 'APLICA CANTIDAD DE PROVEEDORES';

        $mDatos['docacbfx']['camcones'] = 'oDocCbf';
        $mDatos['docacbfx']['camcone2'] = 'cDocCbf';
        $mDatos['docacbfx']['descones'] = 'APLICA COMBUSTIBLE (BAF/FS)';

        $mDatos['docacgbx']['camcones'] = 'oDocCgb';
        $mDatos['docacgbx']['camcone2'] = 'cDocCgb';
        $mDatos['docacgbx']['descones'] = 'APLICA CORTE DE GUIA O BL';

        $mDatos['docagrix']['camcones'] = 'oDocGri';
        $mDatos['docagrix']['camcone2'] = 'cDocGri';
        $mDatos['docagrix']['descones'] = 'APLICA GRI (GENERAL RATE INCREASE)';
        
        $mDatos['docahxxx']['camcones'] = 'oDocHxx';
        $mDatos['docahxxx']['camcone2'] = 'cDocHxx';
        $mDatos['docahxxx']['descones'] = 'APLICA HandLING';

        $mDatos['docaiohx']['camcones'] = 'oDocIoh';
        $mDatos['docaiohx']['camcone2'] = 'cDocIoh';
        $mDatos['docaiohx']['descones'] = 'APLICA IN/OUT (HandLING)';

        $mDatos['docaidxx']['camcones'] = 'oDocAid';
        $mDatos['docaidxx']['camcone2'] = 'cDocAid';
        $mDatos['docaidxx']['descones'] = 'APLICA INLand/DRAYAGE';

        $mDatos['docaprof']['camcones'] = 'oDocPro';
        $mDatos['docaprof']['camcone2'] = 'cDocPro';
        $mDatos['docaprof']['descones'] = 'APLICA PROFIT';

        $mDatos['docapssx']['camcones'] = 'oDocApss';
        $mDatos['docapssx']['camcone2'] = 'cDocApss';
        $mDatos['docapssx']['descones'] = 'APLICA PSS (PEAK SEASON)';

        $mDatos['docarcpx']['camcones'] = 'oDocRcp';
        $mDatos['docarcpx']['camcone2'] = 'cDocRcp';
        $mDatos['docarcpx']['descones'] = 'APLICA RECARGO CARGA PELIGROSA';

        $mDatos['docarpux']['camcones'] = 'oDocRpu';
        $mDatos['docarpux']['camcone2'] = 'cDocRpu';
        $mDatos['docarpux']['descones'] = 'APLICA RECOGIDA (PICK UP)';

        $mDatos['docaorox']['camcones'] = 'oDocRox';
        $mDatos['docaorox']['camcone2'] = 'cDocRox';
        $mDatos['docaorox']['descones'] = 'APLICA OTROS RECARGOS EN ORIGEN';

        $mDatos['docascxx']['camcones'] = 'oDocScx';
        $mDatos['docascxx']['camcone2'] = 'cDocScx';
        $mDatos['docascxx']['descones'] = 'APLICA SECURITY CHARGE (SC)';

        $mDatos['docasedc']['camcones'] = 'oDocSed';
        $mDatos['docasedc']['camcone2'] = 'cDocSed';
        $mDatos['docasedc']['descones'] = 'APLICA SED (DOCUMENTOS DE EXPORTACION)/CUSTOMS';

        $mDatos['docasaix']['camcones'] = 'oDocSai';
        $mDatos['docasaix']['camcone2'] = 'cDocSai';
        $mDatos['docasaix']['descones'] = 'APLICA SERVICIOS AEROPORTUARIOS INTERNACIONALES';

        $mDatos['docathcx']['camcones'] = 'oDocThc';
        $mDatos['docathcx']['camcone2'] = 'cDocThc';
        $mDatos['docathcx']['descones'] = 'APLICA THC (TERMINAL HandLING)';

        $mDatos['docawxxx']['camcones'] = 'oDocWxx';
        $mDatos['docawxxx']['camcone2'] = 'cDocWxx';
        $mDatos['docawxxx']['descones'] = 'APLICA WAREHOUSING';

        $mDatos['docarblx']['camcones'] = 'oDocRbl';
        $mDatos['docarblx']['camcone2'] = 'cDocRbl';
        $mDatos['docarblx']['descones'] = 'APLICA RADICACION BL';

        $mDatos['doctdtxx']['camcones'] = 'oDocTdt';
        $mDatos['doctdtxx']['camcone2'] = '';
        $mDatos['doctdtxx']['descones'] = 'APLICA TRAMITE DELEGADO A UN TERCERO';

        $mDatos['doccsubx']['camcones'] = 'oDocCSub';
        $mDatos['doccsubx']['camcone2'] = 'cDocCSub';
        $mDatos['doccsubx']['descones'] = 'APLICA CANTIDAD SUBPARTIDAS';

        $mDatos['doczunme']['camcones'] = 'oDocZunMe';
        $mDatos['doczunme']['camcone2'] = 'cDocZunMe';
        $mDatos['doczunme']['descones'] = 'APLICA ZUNCHADA DE MERCANCIAS';

        $mDatos['docaaros']['camcones'] = 'oDocAaRos';
        $mDatos['docaaros']['camcone2'] = 'cDocAaRos';
        $mDatos['docaaros']['descones'] = 'APLICA ASESORIAS DE ATENCION REA, OTRAS SOLICITUDES DE LA DIAN U OTRAS ENTIDADES';
        
        $mDatos['docppdxx']['camcones'] = 'oDocPpd';
        $mDatos['docppdxx']['camcone2'] = 'cDocPpd';
        $mDatos['docppdxx']['descones'] = 'APLICA PRORROGAS DE PERMANENCIA EN DEPOSITO';
        
        $mDatos['docdecan']['camcones'] = 'oDocDecAn';
        $mDatos['docdecan']['camcone2'] = 'cDocDecAn';
        $mDatos['docdecan']['descones'] = 'APLICA DECLARACIONES ANTICIPADAS';
        
        $mDatos['docrraox']['camcones'] = 'oDocRrao';
        $mDatos['docrraox']['camcone2'] = 'cDocRrao';
        $mDatos['docrraox']['descones'] = 'APLICA RESPUESTA A REQUERIMIENTOS ADUANEROS Y ORDINARIOS';
        
        $mDatos['docresub']['camcones'] = 'oDocReSub';
        $mDatos['docresub']['camcone2'] = 'cDocReSub';
        $mDatos['docresub']['descones'] = 'APLICA REVISION POR SUBPARTIDA';
        
        $mDatos['docreimp']['camcones'] = 'oDocReImp';
        $mDatos['docreimp']['camcone2'] = 'cDocReImp';
        $mDatos['docreimp']['descones'] = 'APLICA REVISION DE REGISTRO DE IMPORTACION';
        
        $mDatos['docserme']['camcones'] = 'oDocSerMe';
        $mDatos['docserme']['camcone2'] = 'cDocSerMe';
        $mDatos['docserme']['descones'] = 'APLICA SERVICIO DE MERCIOLOGIA';
        
        $mDatos['docolian']['camcones'] = 'oDocOLiAn';
        $mDatos['docolian']['camcone2'] = 'cDocOLiAn';
        $mDatos['docolian']['descones'] = 'APLICA OBTENCION LICENCIA ANUAL';
        
        $mDatos['docmlian']['camcones'] = 'oDocMLiAn';
        $mDatos['docmlian']['camcone2'] = 'cDocMLiAn';
        $mDatos['docmlian']['descones'] = 'APLICA MODIFICACION LICENCIA ANUAL';
        
        $mDatos['docilian']['camcones'] = 'oDocILiAn';
        $mDatos['docilian']['camcone2'] = 'cDocILiAn';
        $mDatos['docilian']['descones'] = 'APLICA INFORMACION LICENCIA ANUAL ANTE MINISTERIOS';
        
        $mDatos['docasind']['camcones'] = 'oDocAsInd';
        $mDatos['docasind']['camcone2'] = 'cDocAsInd';
        $mDatos['docasind']['descones'] = 'APLICA ASESORIA INDUMIL';
        
        $mDatos['docclaar']['camcones'] = 'oDocClaAr';
        $mDatos['docclaar']['camcone2'] = 'cDocClaAr';
        $mDatos['docclaar']['descones'] = 'APLICA CLASIFICACIONES ARANCELARIAS OFICIALES UNIDAD FUNCIONAL';
        
        $mDatos['docregsa']['camcones'] = 'oDocRegSa';
        $mDatos['docregsa']['camcone2'] = 'cDocRegSa';
        $mDatos['docregsa']['descones'] = 'APLICA REGISTROS SANITARIOS';
        
        $mDatos['doccervl']['camcones'] = 'oDocCerVl';
        $mDatos['doccervl']['camcone2'] = 'cDocCerVl';
        $mDatos['doccervl']['descones'] = 'APLICA CERTIFICADOS DE VENTA LIBRE';
        
        $mDatos['docoepdi']['camcones'] = 'oDocOepDi';
        $mDatos['docoepdi']['camcone2'] = 'cDocOepDi';
        $mDatos['docoepdi']['descones'] = 'APLICA OBTENCION EMISION POR PRUEBA DINAMICA';
        
        $mDatos['doccerhv']['camcones'] = 'oDocCerHv';
        $mDatos['doccerhv']['camcone2'] = 'cDocCerHv';
        $mDatos['doccerhv']['descones'] = 'APLICA CERTIFICADOS DE HOMOLOGACION DE VEHICULOS';
        
        $mDatos['docaexiv']['camcones'] = 'oDocAExIv';
        $mDatos['docaexiv']['camcone2'] = 'cDocAExIv';
        $mDatos['docaexiv']['descones'] = 'APLICA ASESORIA PARA EXCLUSION IMPUESTO A LAS VENTAS';
        
        $mDatos['docaseva']['camcones'] = 'oDocAseVa';
        $mDatos['docaseva']['camcone2'] = 'cDocAseVa';
        $mDatos['docaseva']['descones'] = 'APLICA ASESORIA VARIAS';
        
        $mDatos['docperxx']['camcones'] = 'oDocPer';
        $mDatos['docperxx']['camcone2'] = 'cDocPer';
        $mDatos['docperxx']['descones'] = 'APLICA PERMISOS';
        
        $mDatos['doccouap']['camcones'] = 'oDocCoUap';
        $mDatos['doccouap']['camcone2'] = 'cDocCoUap';
        $mDatos['doccouap']['descones'] = 'APLICA CONTROL UAP';
        
        $mDatos['docrenpo']['camcones'] = 'oDocRenPo';
        $mDatos['docrenpo']['camcone2'] = 'cDocRenPo';
        $mDatos['docrenpo']['descones'] = 'APLICA RENOVACION POLIZA UAP O ACTA ADMINISTRATIVA';
        
        $mDatos['doccuapm']['camcones'] = 'oDocCUapM';
        $mDatos['doccuapm']['camcone2'] = 'cDocCUapM';
        $mDatos['doccuapm']['descones'] = 'APLICA CONTROL UAP MENSUAL';
        
        $mDatos['docdioea']['camcones'] = 'oDocDiOea';
        $mDatos['docdioea']['camcone2'] = 'cDocDiOea';
        $mDatos['docdioea']['descones'] = 'APLICA DIAGNOSTICO OEA';
        
        $mDatos['docacoea']['camcones'] = 'oDocAcOea';
        $mDatos['docacoea']['camcone2'] = 'cDocAcOea';
        $mDatos['docacoea']['descones'] = 'APLICA ACOMPA&NtildeAMIENTO OEA';
        
        $mDatos['docacroe']['camcones'] = 'oDocAcrOe';
        $mDatos['docacroe']['camcone2'] = 'cDocAcrOe';
        $mDatos['docacroe']['descones'] = 'APLICA ACREDITACION OEA';
        
        $mDatos['docasoea']['camcones'] = 'oDocAsOea';
        $mDatos['docasoea']['camcone2'] = 'cDocAsOea';
        $mDatos['docasoea']['descones'] = 'APLICA ASESORIA EN OEA';
        
        $mDatos['docasanl']['camcones'] = 'oDocAsAnl';
        $mDatos['docasanl']['camcone2'] = 'cDocAsAnl';
        $mDatos['docasanl']['descones'] = 'APLICA ASESORIA EN ANLA';
        
        $mDatos['docaseco']['camcones'] = 'oDocAseCo';
        $mDatos['docaseco']['camcone2'] = 'cDocAseCo';
        $mDatos['docaseco']['descones'] = 'APLICA ASESORIA DE CLASIFICACION OFICIAL';
        
        $mDatos['docsocmp']['camcones'] = 'oDocSoCmp';
        $mDatos['docsocmp']['camcone2'] = 'cDocSoCmp';
        $mDatos['docsocmp']['descones'] = 'APLICA SOLICITUD CERTIFICACION MAQUINARIA PESADA';
        
        $mDatos['docsolpn']['camcones'] = 'oDocSolPn';
        $mDatos['docsolpn']['camcone2'] = 'cDocSolPn';
        $mDatos['docsolpn']['descones'] = 'APLICA SOLICITUD PRODUCCION NACIONAL';
        
        $mDatos['docelaem']['camcones'] = 'oDocElaEm';
        $mDatos['docelaem']['camcone2'] = 'cDocElaEm';
        $mDatos['docelaem']['descones'] = 'APLICA ELABORACION DE EMBALAJE';
        
        $mDatos['docrevdo']['camcones'] = 'oDocRevDo';
        $mDatos['docrevdo']['camcone2'] = 'cDocRevDo';
        $mDatos['docrevdo']['descones'] = 'APLICA REVISION DOCUMENTAL REALIZADA POR OTRAS AGENCIAS DE ADUANA';
        
        $mDatos['docampim']['camcones'] = 'oDocAmpIm';
        $mDatos['docampim']['camcone2'] = 'cDocAmpIm';
        $mDatos['docampim']['descones'] = 'APLICA AMPLIACION DE IMPORTACION TEMPORAL CORTO PLAZO';
        
        $mDatos['docfiicp']['camcones'] = 'oDocFiIcp';
        $mDatos['docfiicp']['camcone2'] = 'cDocFiIcp';
        $mDatos['docfiicp']['descones'] = 'APLICA FINALIZACION IMPORTACION CORTO PLAZO';
        
        $mDatos['docfiilp']['camcones'] = 'oDocFiIlp';
        $mDatos['docfiilp']['camcone2'] = 'cDocFiIlp';
        $mDatos['docfiilp']['descones'] = 'APLICA FINALIZACION IMPORTACION LARGO PLAZO';
        
        $mDatos['doccomve']['camcones'] = 'oDocComVe';
        $mDatos['doccomve']['camcone2'] = 'cDocComVe';
        $mDatos['doccomve']['descones'] = 'APLICA COMISION POR VEHICULO';
        
        $mDatos['doccanve']['camcones'] = 'oDocCanVe';
        $mDatos['doccanve']['camcone2'] = 'cDocCanVe';
        $mDatos['doccanve']['descones'] = 'APLICA CANTIDAD DE VEHICULOS';
        
        $mDatos['doccanca']['camcones'] = 'oDocCanCa';
        $mDatos['doccanca']['camcone2'] = 'cDocCanCa';
        $mDatos['doccanca']['descones'] = 'APLICA CANTIDAD DE CAJAS';
        
        $mDatos['docmercu']['camcones'] = 'oDocMerCu';
        $mDatos['docmercu']['camcone2'] = 'cDocMerCu';
        $mDatos['docmercu']['descones'] = 'APLICA CANTIDAD DE UNIDADES CARGUE DE MERCANCIA CUADRILLA';
        
        $mDatos['docdesme']['camcones'] = 'oDocDesMe';
        $mDatos['docdesme']['camcone2'] = 'cDocDesMe';
        $mDatos['docdesme']['descones'] = 'APLICA CANTIDAD DE UNIDADES DESCARGUE DE MERCANCIA CUADRILLA';
        
        $mDatos['docmanco']['camcones'] = 'oDocManCo';
        $mDatos['docmanco']['camcone2'] = 'cDocManCo';
        $mDatos['docmanco']['descones'] = 'APLICA MANEJO DE CONTENEDORES';
        
        $mDatos['doccanto']['camcones'] = 'oDocCanTo';
        $mDatos['doccanto']['camcone2'] = 'cDocCanTo';
        $mDatos['doccanto']['descones'] = 'APLICA CANTIDAD DE TONELADAS';
        
        $mDatos['doccant2']['camcones'] = 'oDocCanT2';
        $mDatos['doccant2']['camcone2'] = 'cDocCanT2';
        $mDatos['doccant2']['descones'] = 'APLICA CANTIDAD DE TONELADAS EQUIPOS DE MOVILIZACION';
        
        $mDatos['doccant3']['camcones'] = 'oDocCanT3';
        $mDatos['doccant3']['camcone2'] = 'cDocCanT3';
        $mDatos['doccant3']['descones'] = 'APLICA CANTIDAD DE TONELADAS ALMACENAMIENTO DEPOSITO HABILITADO';
        
        $mDatos['doccant4']['camcones'] = 'oDocCanT4';
        $mDatos['doccant4']['camcone2'] = 'cDocCanT4';
        $mDatos['doccant4']['descones'] = 'APLICA CANTIDAD DE TONELADAS CLASIFICACION ARANCELARIA';
        
        $mDatos['doccant5']['camcones'] = 'oDocCanT5';
        $mDatos['doccant5']['camcone2'] = 'cDocCanT5';
        $mDatos['doccant5']['descones'] = 'APLICA CANTIDAD DE TONELADAS INTERMEDIACION ADUANERA';
        
        $mDatos['doccanki']['camcones'] = 'oDocCanKi';
        $mDatos['doccanki']['camcone2'] = 'cDocCanKi';
        $mDatos['doccanki']['descones'] = 'APLICA CANTIDAD DE KILOS';
        
        $mDatos['docsimca']['camcones'] = 'oDocSimCa';
        $mDatos['docsimca']['camcone2'] = 'cDocSimCa';
        $mDatos['docsimca']['descones'] = 'APLICA CANTIDAD DE SIMCARDS';
        
        $mDatos['docterxx']['camcones'] = 'oDocTer';
        $mDatos['docterxx']['camcone2'] = 'cDocTer';
        $mDatos['docterxx']['descones'] = 'APLICA CANTIDAD DE TERMINALES';
        
        $mDatos['doctabxx']['camcones'] = 'oDocTab';
        $mDatos['doctabxx']['camcone2'] = 'cDocTab';
        $mDatos['doctabxx']['descones'] = 'APLICA CANTIDAD DE TABLETS';
        
        $mDatos['docmodxx']['camcones'] = 'oDocMod';
        $mDatos['docmodxx']['camcone2'] = 'cDocMod';
        $mDatos['docmodxx']['descones'] = 'APLICA CANTIDAD DE MODEMS';
        
        $mDatos['doccanga']['camcones'] = 'oDocCanGa';
        $mDatos['doccanga']['camcone2'] = 'cDocCanGa';
        $mDatos['doccanga']['descones'] = 'APLICA CANTIDAD DE GASTOS';
        
        $mDatos['doccanse']['camcones'] = 'oDocCanSe';
        $mDatos['doccanse']['camcone2'] = 'cDocCanSe';
        $mDatos['doccanse']['descones'] = 'APLICA CANTIDAD DE SERIALES';
        
        $mDatos['dochordi']['camcones'] = 'oDocHorDi';
        $mDatos['dochordi']['camcone2'] = 'cDocHorDi';
        $mDatos['dochordi']['descones'] = 'APLICA CANTIDAD DE HORAS DIURNAS';
        
        $mDatos['dochorno']['camcones'] = 'oDocHorNo';
        $mDatos['dochorno']['camcone2'] = 'cDocHorNo';
        $mDatos['dochorno']['descones'] = 'APLICA CANTIDAD DE HORAS NOCTURNAS';
        
        $mDatos['dochordo']['camcones'] = 'oDocHorDo';
        $mDatos['dochordo']['camcone2'] = 'cDocHorDo';
        $mDatos['dochordo']['descones'] = 'APLICA CANTIDAD DE HORAS DOMINICALES';
        
        $mDatos['dochorfe']['camcones'] = 'oDocHorFe';
        $mDatos['dochorfe']['camcone2'] = 'cDocHorFe';
        $mDatos['dochorfe']['descones'] = 'APLICA CANTIDAD DE HORAS FESTIVAS';
        
        $mDatos['doccanpe']['camcones'] = 'oDocCanPe';
        $mDatos['doccanpe']['camcone2'] = 'cDocCanPe';
        $mDatos['doccanpe']['descones'] = 'APLICA CANTIDAD DE PERIODOS';
        
        $mDatos['doccanme']['camcones'] = 'oDocCanMe';
        $mDatos['doccanme']['camcone2'] = 'cDocCanMe';
        $mDatos['doccanme']['descones'] = 'APLICA CANTIDAD DE METROS';
        
        $mDatos['doccandi']['camcones'] = 'oDocCandi';
        $mDatos['doccandi']['camcone2'] = 'cDocCandi';
        $mDatos['doccandi']['descones'] = 'APLICA CANTIDAD DE DIAS';
        
        $mDatos['docalmxx']['camcones'] = 'oDocAlm';
        $mDatos['docalmxx']['camcone2'] = 'cDocAlm';
        $mDatos['docalmxx']['descones'] = 'APLICA CANTIDAD DE ALMACENAMIENTO CARGA';
        
        $mDatos['docverxx']['camcones'] = 'oDocVer';
        $mDatos['docverxx']['camcone2'] = 'cDocVer';
        $mDatos['docverxx']['descones'] = 'APLICA CANTIDAD DE VERSIONES';
        
        $mDatos['docver1x']['camcones'] = 'oDocVer1';
        $mDatos['docver1x']['camcone2'] = 'cDocVer1';
        $mDatos['docver1x']['descones'] = 'APLICA CANTIDAD DE VERSIONES PICKING';
        
        $mDatos['docver2x']['camcones'] = 'oDocVer2';
        $mDatos['docver2x']['camcone2'] = 'cDocVer2';
        $mDatos['docver2x']['descones'] = 'APLICA CANTIDAD DE VERSIONES RECIBO DE MERCANCIAS';
        
        $mDatos['docmerte']['camcones'] = 'oDocMerTe';
        $mDatos['docmerte']['camcone2'] = 'cDocMerTe';
        $mDatos['docmerte']['descones'] = 'APLICA CANTIDAD DE MERCANCIAS TERREMOTO';
        
        $mDatos['docsegam']['camcones'] = 'oDocSegAm';
        $mDatos['docsegam']['camcone2'] = 'cDocSegAm';
        $mDatos['docsegam']['descones'] = 'APLICA CANTIDAD DE SEGUROS AMIT';
        
        $mDatos['doccanes']['camcones'] = 'oDocCanEs';
        $mDatos['doccanes']['camcone2'] = 'cDocCanEs';
        $mDatos['doccanes']['descones'] = 'APLICA CANTIDAD DE ESTIBAS';
        
        $mDatos['docjorad']['camcones'] = 'oDocJorAd';
        $mDatos['docjorad']['camcone2'] = 'cDocJorAd';
        $mDatos['docjorad']['descones'] = 'APLICA CANTIDAD DE JORNADAS ADICIONALES';
        
        $mDatos['dochoror']['camcones'] = 'oDocHorOr';
        $mDatos['dochoror']['camcone2'] = 'cDocHorOr';
        $mDatos['dochoror']['descones'] = 'APLICA CANTIDAD DE HORAS ORDINARIAS';
        
        $mDatos['dochorfx']['camcones'] = 'oDocHorF';
        $mDatos['dochorfx']['camcone2'] = 'cDocHorF';
        $mDatos['dochorfx']['descones'] = 'APLICA CANTIDAD DE HORAS FESTIVAS';
        
        $mDatos['docdecun']['camcones'] = 'oDocDecUn';
        $mDatos['docdecun']['camcone2'] = 'cDocDecUn';
        $mDatos['docdecun']['descones'] = 'APLICA CANTIDAD DECLARACIONES DE UNIDADES';
        
        $mDatos['docdecu1']['camcones'] = 'oDocDecU1';
        $mDatos['docdecu1']['camcone2'] = 'cDocDecU1';
        $mDatos['docdecu1']['descones'] = 'APLICA CANTIDAD DECLARACIONES DE UNIDADES CARGUE CUADRILLA';
        
        $mDatos['docdecu2']['camcones'] = 'oDocDecU2';
        $mDatos['docdecu2']['camcone2'] = 'cDocDecU2';
        $mDatos['docdecu2']['descones'] = 'APLICA CANTIDAD DECLARACIONES DE UNIDADES DESCARGUE CUADRILLA';
        
        $mDatos['docsegme']['camcones'] = 'oDocSegMe';
        $mDatos['docsegme']['camcone2'] = 'cDocSegMe';
        $mDatos['docsegme']['descones'] = 'APLICA CANTIDAD SEGUROS MERCANCIA TERREMOTO';
        
        $mDatos['docsegad']['camcones'] = 'oDocSegAd';
        $mDatos['docsegad']['camcone2'] = 'cDocSegAd';
        $mDatos['docsegad']['descones'] = 'APLICA SEGURO DE ADMINISTRACION DE RIESGO';
        
        $mDatos['doccanmd']['camcones'] = 'oDocCanMD';
        $mDatos['doccanmd']['camcone2'] = 'cDocCanMD';
        $mDatos['doccanmd']['descones'] = 'APLICA CANTIDAD MANEJO DOCUMENTAL';
        
        $mDatos['docfotxx']['camcones'] = 'oDocFot';
        $mDatos['docfotxx']['camcone2'] = 'cDocFot';
        $mDatos['docfotxx']['descones'] = 'APLICA CANTIDAD DE FOTOCOPIAS';
        
        $mDatos['doccand2']['camcones'] = 'oDocCand2';
        $mDatos['doccand2']['camcone2'] = 'cDocCand2';
        $mDatos['doccand2']['descones'] = 'APLICA CANTIDAD DE DIAS';
        
        $mDatos['docest20']['camcones'] = 'oDocEst20';
        $mDatos['docest20']['camcone2'] = 'cDocEst20';
        $mDatos['docest20']['descones'] = 'APLICA CANTIDAD ESTIBAS CONTENEDORES DE 20';
        
        $mDatos['docest40']['camcones'] = 'oDocEst40';
        $mDatos['docest40']['camcone2'] = 'cDocEst40';
        $mDatos['docest40']['descones'] = 'APLICA CANTIDAD ESTIBAS CONTENEDORES DE 40';
        
        $mDatos['docdiveh']['camcones'] = 'oDocDiVeh';
        $mDatos['docdiveh']['camcone2'] = 'cDocDiVeh';
        $mDatos['docdiveh']['descones'] = 'APLICA CANTIDAD DE DIAS VEHICULOS';
        
        $mDatos['docdicta']['camcones'] = 'oDocDiCta';
        $mDatos['docdicta']['camcone2'] = 'cDocDiCta';
        $mDatos['docdicta']['descones'] = 'APLICA CANTIDAD DE DIAS CAMIONETA';
        
        $mDatos['docdicam']['camcones'] = 'oDocDiCam';
        $mDatos['docdicam']['camcone2'] = 'cDocDiCam';
        $mDatos['docdicam']['descones'] = 'APLICA CANTIDAD DE DIAS CAMION';
        
        $mDatos['docdimon']['camcones'] = 'oDocDiMon';
        $mDatos['docdimon']['camcone2'] = 'cDocDiMon';
        $mDatos['docdimon']['descones'] = 'APLICA CANTIDAD DE DIAS MONTACARGA';
        
        $mDatos['doccon20']['camcones'] = 'oDocCon20';
        $mDatos['doccon20']['camcone2'] = 'cDocCon20';
        $mDatos['doccon20']['descones'] = 'CANTIDAD DE CONTENEDORES DE 20 PIES';
        
        $mDatos['doccon40']['camcones'] = 'oDocCon40';
        $mDatos['doccon40']['camcone2'] = 'cDocCon40';
        $mDatos['doccon40']['descones'] = 'CANTIDAD DE CONTENEDORES DE 40 PIES';
        
        $mDatos['doccanun']['camcones'] = 'oDocCanUn';
        $mDatos['doccanun']['camcone2'] = 'cDocCanUn';
        $mDatos['doccanun']['descones'] = 'APLICA CANTIDAD DE UNIDADES';

        // Array con campos de cantidad que aplican para importaciones agrupados por nombres
        $vCamCan = ["3", "5", "6", "7", "8", "9"];
        for ($nC=0; $nC < count($vCamCan); $nC++) {
          $mDatos['doccanu'.$vCamCan[$nC]]['camcones'] = 'oDocCanU'.$vCamCan[$nC];
          $mDatos['doccanu'.$vCamCan[$nC]]['camcone2'] = 'cDocCanU'.$vCamCan[$nC];
          $mDatos['doccanu'.$vCamCan[$nC]]['descones'] = 'CANTIDAD DE UNIDADES';
        }

        $vCamCan = ["10", "11", "12", "13", "14", "15", "16", "17", "18", "19", 
                    "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", 
                    "30", "31", "32", "33", "34", "35", "36", "37", "38", "39", 
                    "40", "41", "42", "43", "44", "45", "46", "47", "48", "49", 
                    "50", "51", "52", "53",
                    "63","64"];
        for ($nC=0; $nC < count($vCamCan); $nC++) {
          $mDatos['doccau'.$vCamCan[$nC]]['camcones'] = 'oDocCau'.$vCamCan[$nC];
          $mDatos['doccau'.$vCamCan[$nC]]['camcone2'] = 'cDocCau'.$vCamCan[$nC];
          $mDatos['doccau'.$vCamCan[$nC]]['descones'] = 'CANTIDAD DE UNIDADES';
        }
        
        $mDatos['docvlfij']['camcones'] = 'oDocVlFij';
        $mDatos['docvlfij']['camcone2'] = 'cDocVlFij';
        $mDatos['docvlfij']['descones'] = 'CANTIDAD DE UNIDADES';
        
        $mDatos['doccubxx']['camcones'] = 'oDocCub';
        $mDatos['doccubxx']['camcone2'] = 'cDocCub';
        $mDatos['doccubxx']['descones'] = 'CANTIDAD CUBICAJE TOTAL';
        $mDatos['doccubxx']['accionjs'] = 'PROCC';
        
        $mDatos['docvlcub']['camcones'] = 'oDocVlCub';
        $mDatos['docvlcub']['camcone2'] = 'cDocVlCub';
        $mDatos['docvlcub']['descones'] = 'VALOR CUBICAJE DEL DO';
        $mDatos['docvlcub']['accionjs'] = 'PROVC';
        
        $mDatos['docproxx']['camcones'] = 'oDocPror';
        $mDatos['docproxx']['camcone2'] = '';
        $mDatos['docproxx']['descones'] = 'APLICA PRORRATEO';
        $mDatos['docproxx']['accionjs'] = 'PRO';
        
        $mDatos['docaddxx']['camcones'] = 'oDocAdd';
        $mDatos['docaddxx']['camcone2'] = '';
        $mDatos['docaddxx']['descones'] = 'APLICA DESCARGUE DIRECTO';
        
        $mDatos['docvlcif']['camcones'] = 'oDocVlCif';
        $mDatos['docvlcif']['camcone2'] = 'cDocVlCif';
        $mDatos['docvlcif']['descones'] = 'VALOR CIF';
        
        $mDatos['doccag2x']['camcones'] = 'oDocCag2';
        $mDatos['doccag2x']['camcone2'] = '';
        $mDatos['doccag2x']['descones'] = 'APLICA CARGA GRANEL';
        $mDatos['doccag2x']['accionjs'] = 'CAR2';
        
        $mDatos['docconxx']['camcones'] = 'oDocCon';
        $mDatos['docconxx']['camcone2'] = '';
        $mDatos['docconxx']['descones'] = 'APLICA CONTENEDORES';
        $mDatos['docconxx']['accionjs'] = 'CAR2';
        
        $mDatos['docblxxx']['camcones'] = 'oDocBl';
        $mDatos['docblxxx']['camcone2'] = 'cDocBl';
        $mDatos['docblxxx']['descones'] = 'APLICA CANTIDAD BL PARA AGENCIAMIENTO';
        
        $mDatos['docnivag']['camcones'] = 'oDocNivAg';
        $mDatos['docnivag']['camcone2'] = 'cDocNivAg';
        $mDatos['docnivag']['descones'] = 'APLICA NIVEL AGENCIAMIENTO';
        
        $mDatos['dochhdre']['camcones'] = 'oDocHhdre';
        $mDatos['dochhdre']['camcone2'] = 'cDocHhdre';
        $mDatos['dochhdre']['descones'] = 'VALOR HORAS x HOMBRE x DIA';
        
        $mDatos['doccanho']['camcones'] = 'oDocCanHo';
        $mDatos['doccanho']['camcone2'] = 'cDocCanHo';
        $mDatos['doccanho']['descones'] = 'APLICA CANTIDAD DE HORAS';
        
        $mDatos['doccaper']['camcones'] = 'oDocCaPer';
        $mDatos['doccaper']['camcone2'] = 'cDocCaPer';
        $mDatos['doccaper']['descones'] = 'APLICA CANTIDAD DE PERSONAS';
        
        $mDatos['docvalaa']['camcones'] = 'oDocValAA';
        $mDatos['docvalaa']['camcone2'] = 'cDocValAA';
        $mDatos['docvalaa']['descones'] = 'APLICA VALOR APOYO ARCHIVO';
        
        $mDatos['docac20x']['camcones'] = 'oDocAC20';
        $mDatos['docac20x']['camcone2'] = '';
        $mDatos['docac20x']['descones'] = 'APLICA	CONTENEDORES DE 20';
        $mDatos['docac20x']['accionjs'] = 'C20';
        
        $mDatos['docac40x']['camcones'] = 'oDocAC40';
        $mDatos['docac40x']['camcone2'] = '';
        $mDatos['docac40x']['descones'] = 'APLICA	CONTENEDORES DE 40';
        $mDatos['docac40x']['accionjs'] = 'C40';
        
        $mDatos['doccansa']['camcones'] = 'oDocCanSa';
        $mDatos['doccansa']['camcone2'] = 'cDocCanSa';
        $mDatos['doccansa']['descones'] = 'APLICA CANTIDAD DE SACOS';
        
        $mDatos['docufame']['camcones'] = 'oDocUFaMe';
        $mDatos['docufame']['camcone2'] = '';
        $mDatos['docufame']['descones'] = 'APLICA ULTIMA FACTURA DEL MES';
        
        $mDatos['doccacon']['camcones'] = 'oDocCaCon';
        $mDatos['doccacon']['camcone2'] = 'cDocCaCon';
        $mDatos['doccacon']['descones'] = 'APLICA CANTIDAD DE CONTENEDORES';
        
        $mDatos['docvlter']['camcones'] = 'oDocVlTer';
        $mDatos['docvlter']['camcone2'] = 'cDocVlTer';
        $mDatos['docvlter']['descones'] = 'APLICA VALOR DE LOS PAGOS A TERCEROS';

        $mDatos['docdopar']['camcones'] = 'oDocDoPar';
        $mDatos['docdopar']['camcone2'] = '';
        $mDatos['docdopar']['descones'] = 'APLICA DO PARCIAL';

        $mDatos['doccovar']['camcones'] = 'oDocCoVar';
        $mDatos['doccovar']['camcone2'] = 'cDocCoVar';
        $mDatos['doccovar']['descones'] = 'APLICA COBRO VARIABLE';

        $mDatos['docrecli']['camcones'] = 'oDocRecLi';
        $mDatos['docrecli']['camcone2'] = '';
        $mDatos['docrecli']['descones'] = 'APLICA RECONOCIMIETNO POR LINEA';

        $mDatos['docrecin']['camcones'] = 'oDocRecIn';
        $mDatos['docrecin']['camcone2'] = '';
        $mDatos['docrecin']['descones'] = 'APLICA RECONOCIMIENTO PREINSCRIPCION';

        $mDatos['doccrefa']['camcones'] = 'oDocRecFa';
        $mDatos['doccrefa']['camcone2'] = '';
        $mDatos['doccrefa']['descones'] = 'APLICA REFACTURACION';

        //Campos por defecto para importaciones, esta condicion especial debe mostrarse para todos los DO
        //Si se incluye una nueva condicion en este vector debe incluirse tambien en la facrtuacion en el archivo frfacvdo.php
        if($vSysStr['financiero_fecha_instalacion_modulo_comisiones_vendedores'] != ""){
          $mCampos = array('doctdtxx');
          // Descripcion para el servicio cuando no aplica en ninguna tarifa
          $mDatos['doctdtxx']['serdesxx'] = "MODULO COMISIONES VENDEDORES";
        }

        if ($pArrayParametros['cliidxxx'] != "") {
          // Busco las condiciones comerciales del cliente para ver si aplica
          // APLICAN FORMULARIOS VIRTUALES - DECLARACION DE IMPORTACION (doccfvxx)
          // APLICA FORMULARIO DAV MAGNETICAS (docafdmx)
          //Cliente o factura a
          $cCliFor  = ($pArrayParametros['teridint'] != "") ? $pArrayParametros['teridint'] : $pArrayParametros['cliidxxx'];

          $qConCom  = "SELECT ccccfvxx, cccfdmxx ";
          $qConCom .= "FROM $cAlfa.fpar0151 ";
          $qConCom .= "WHERE ";
          $qConCom .= "cliidxxx = \"$cCliFor\" AND ";
          $qConCom .= "regestxx = \"ACTIVO\" LIMIT 0,1";
          $xConCom  = f_MySql("SELECT","",$qConCom,$xConexion01,"");
          // f_Mensaje(__FILE__,__LINE__,$qConCom." ~ ".mysql_num_rows($xConCom));
          if (mysql_num_rows($xConCom) > 0) {
            $xRCC = mysql_fetch_array($xConCom);
            if ($xRCC['ccccfvxx'] == 'SI') {
              $mCampos[] = 'doccfvxx';
              // Descripcion para el servicio cuando no aplica en ninguna tarifa
              $mDatos['doccfvxx']['serdesxx'] = "CONDICION COMERCIAL CLIENTE";
            }
            if ($xRCC['cccfdmxx'] == 'SI') {
              $mCampos[] = 'docafdmx';
              // Descripcion para el servicio cuando no aplica en ninguna tarifa
              $mDatos['docafdmx']['serdesxx'] = "CONDICION COMERCIAL CLIENTE";
            }
          }
        } else {
          // se incluyen los campos de formulario por defecto
          $mCampos[] = 'doccfvxx';
          $mCampos[] = 'docafdmx';
          // Descripcion para el servicio cuando no aplica en ninguna tarifa
          $mDatos['doccfvxx']['serdesxx'] = "CONDICION COMERCIAL CLIENTE";
          $mDatos['docafdmx']['serdesxx'] = "CONDICION COMERCIAL CLIENTE";
        }        
      break;
      case "EXPORTACION":
        // Matriz con los nombres de los radio y su descripcion
        // El indice principal corresponde al nombre del campo en la tabla sys00121
        // El indicie camcones corresponde al nombre del Radio Button
        // El indicie camcone2 corresponde al nombre del campo donde se digita el valor
        $mDatos['dochiaxx']['camcones'] = 'oDocHia';
        $mDatos['dochiaxx']['camcone2'] = 'cDocHia';
        $mDatos['dochiaxx']['descones'] = 'APLICAN HORAS DE INSPECCION ANTINARCOTICOS';

        $mDatos['docfobxx']['camcones'] = 'oDocFob';
        $mDatos['docfobxx']['camcone2'] = 'cDocFob';
        $mDatos['docfobxx']['descones'] = 'APLICA VALOR DE LA OPERACION';

        $mDatos['doctrmxx']['camcones'] = 'oDocTrm';
        $mDatos['doctrmxx']['camcone2'] = 'cDocTrm';
        $mDatos['doctrmxx']['descones'] = 'APLICA TRM DE LA NEGOCIACION';

        $mDatos['docdecxx']['camcones'] = 'oDocDec';
        $mDatos['docdecxx']['camcone2'] = 'cDocDec';
        $mDatos['docdecxx']['descones'] = 'APLICAN CANTIDAD DE DECLARACIONES';

        $mDatos['doccagxx']['camcones'] = 'oDocCag';
        $mDatos['doccagxx']['camcone2'] = '';
        $mDatos['doccagxx']['descones'] = 'APLICA CARGA AL GRANEL';
        $mDatos['doccagxx']['accionjs'] = 'CAG';

        $mDatos['docc20xx']['camcones'] = 'oDocC20';
        $mDatos['docc20xx']['camcone2'] = 'cDocC20';
        $mDatos['docc20xx']['descones'] = 'APLICAN CANTIDAD DE CONTENEDORES DE 20 PIES';
        $mDatos['docc20xx']['accionjs'] = 'C20';

        $mDatos['docc40xx']['camcones'] = 'oDocC40';
        $mDatos['docc40xx']['camcone2'] = 'cDocC40';
        $mDatos['docc40xx']['descones'] = 'APLICAN CANTIDAD DE CONTENEDORES DE 40 PIES';
        $mDatos['docc40xx']['accionjs'] = 'C40';

        $mDatos['doccsxxx']['camcones'] = 'oDocCs';
        $mDatos['doccsxxx']['camcone2'] = '';
        $mDatos['doccsxxx']['descones'] = 'APLICA CARGA SUELTA';
        $mDatos['doccsxxx']['accionjs'] = 'CS';

        $mDatos['doccsuxx']['camcones'] = 'oDocCsu';
        $mDatos['doccsuxx']['camcone2'] = 'cDocCsu';
        $mDatos['doccsuxx']['descones'] = 'APLICAN UNIDADES DE CARGA SUELTA';
        $mDatos['doccsuxx']['accionjs'] = 'CSU';

        $mDatos['doceocox']['camcones'] = 'oDocEoco';
        $mDatos['doceocox']['camcone2'] = 'cDocEoco';
        $mDatos['doceocox']['descones'] = 'APLICA ELABORACION Y OBTENCION DE CRITERIOS DE ORIGEN';

        $mDatos['docssxxx']['camcones'] = 'oDocSs';
        $mDatos['docssxxx']['camcone2'] = 'cDocSs';
        $mDatos['docssxxx']['descones'] = 'APLICAN SELLOS DE SEGURIDAD';

        $mDatos['docvbinv']['camcones'] = 'oDocVbinv';
        $mDatos['docvbinv']['camcone2'] = 'cDocVbinv';
        $mDatos['docvbinv']['descones'] = 'APLICA VISTO BUENO INVIMA';

        $mDatos['doccaxxx']['camcones'] = 'oDocCa';
        $mDatos['doccaxxx']['camcone2'] = 'cDocCa';
        $mDatos['doccaxxx']['descones'] = 'APLICAN CLASIFICACIONES ARANCELARIAS';

        $mDatos['docvbica']['camcones'] = 'oDocVbica';
        $mDatos['docvbica']['camcone2'] = 'cDocVbica';
        $mDatos['docvbica']['descones'] = 'APLICA VISTO BUENO ICA';

        $mDatos['doctltix']['camcones'] = 'oDocTlti';
        $mDatos['doctltix']['camcone2'] = 'cDocTlti';
        $mDatos['doctltix']['descones'] = 'APLICA TRAMITE LICENCIA DE TRANSPORTE INGEOMINAS';

        $mDatos['doctleix']['camcones'] = 'oDocTlei';
        $mDatos['doctleix']['camcone2'] = 'cDocTlei';
        $mDatos['doctleix']['descones'] = 'APLICA TRAMITE LICENCIA DE EXPORTACION INGEOMINAS';

        $mDatos['docmavxx']['camcones'] = 'oDocMav';
        $mDatos['docmavxx']['camcone2'] = '';
        $mDatos['docmavxx']['descones'] = 'APLICA MEDICION Y ACOMPA&Ntilde;AMIENTO VEHICULO DURANTE EL TRANSPORTE DE MATERIAL RADIACTIVO';

        $mDatos['docstnxx']['camcones'] = 'oDocStn';
        $mDatos['docstnxx']['camcone2'] = 'cDocStn';
        $mDatos['docstnxx']['descones'] = 'APLICA SERVICIO NACIONAL DE TRANSPORTE';

        $mDatos['docesdxx']['camcones'] = 'oDocEsd';
        $mDatos['docesdxx']['camcone2'] = 'cDocEsd';
        $mDatos['docesdxx']['descones'] = 'APLICA ELABORACION SHIPPER DECLARATION(DGD)';

        $mDatos['doczunme']['camcones'] = 'oDocZunMe';
        $mDatos['doczunme']['camcone2'] = 'cDocZunMe';
        $mDatos['doczunme']['descones'] = 'APLICA ZUNCHADA DE MERCANCIAS';

        $mDatos['docppxxx']['camcones'] = 'oDocPp';
        $mDatos['docppxxx']['camcone2'] = '';
        $mDatos['docppxxx']['descones'] = 'APLICA PRESENCIA DE PERSONAL CARGUE/DESCARGUE DE MERCANCIAS';

        $mDatos['docreexx']['camcones'] = 'oDocRee';
        $mDatos['docreexx']['camcone2'] = '';
        $mDatos['docreexx']['descones'] = 'APLICA REEXPEDICION';

        $mDatos['docverme']['camcones'] = 'oDocVerMe';
        $mDatos['docverme']['camcone2'] = 'cDocVerMe';
        $mDatos['docverme']['descones'] = 'APLICA VERIFICACION DE MERCANCIAS';

        $mDatos['doctrans']['camcones'] = 'oDocTrans';
        $mDatos['doctrans']['camcone2'] = '';
        $mDatos['doctrans']['descones'] = 'APLICA TRANSFERENCIA';

        $mDatos['docafvex']['camcones'] = 'oDocAfve';
        $mDatos['docafvex']['camcone2'] = 'cDocAfve';
        $mDatos['docafvex']['descones'] = 'APLICA FORMULARIOS VIRTUALES';

        $mDatos['docafvha']['camcones'] = 'oDocAfvha';
        $mDatos['docafvha']['camcone2'] = 'cDocAfvha';
        $mDatos['docafvha']['descones'] = 'APLICA HOJA ADICIONAL PARA FORMULARIOS VIRTUALES';

        $mDatos['docaepex']['camcones'] = 'oDocAEpe';
        $mDatos['docaepex']['camcone2'] = 'cDocAEpe';
        $mDatos['docaepex']['descones'] = 'APLICA ELABORACION PLANTILLA EXPORTACIONES';

        $mDatos['docarxxx']['camcones'] = 'oDocCar';
        $mDatos['docarxxx']['camcone2'] = 'cDocCar';
        $mDatos['docarxxx']['descones'] = 'APLICA RECONOCIMIENTO';

        $mDatos['docaflex']['camcones'] = 'oDocFle';
        $mDatos['docaflex']['camcone2'] = 'cDocFle';
        $mDatos['docaflex']['descones'] = 'APLICA FLETE';

        $mDatos['docaecor']['camcones'] = 'oDocAecor';
        $mDatos['docaecor']['camcone2'] = 'cDocAecor';
        $mDatos['docaecor']['descones'] = 'APLICA ELABORACI&Oacute;N CERTIFICADO DE ORIGEN';

        $mDatos['docaebul']['camcones'] = 'oDocAebul';
        $mDatos['docaebul']['camcone2'] = 'cDocAebul';
        $mDatos['docaebul']['descones'] = 'APLICA ETIQUETADO DE BULTOS';

        $mDatos['docotrga']['camcones'] = 'oDocOtrGa';
        $mDatos['docotrga']['camcone2'] = 'cDocOtrGa';
        $mDatos['docotrga']['descones'] = 'APLICA OTROS GASTOS';

        $mDatos['docacxxx']['camcones'] = 'oDocAcxx';
        $mDatos['docacxxx']['camcone2'] = 'cDocAcxx';
        $mDatos['docacxxx']['descones'] = 'APLICA COURRIER';

        $mDatos['docadfax']['camcones'] = 'oDocAdfax';
        $mDatos['docadfax']['camcone2'] = 'cDocAdfax';
        $mDatos['docadfax']['descones'] = 'APLICA DIFERENCIA EN FLETE AEREO';

        $mDatos['docadaxx']['camcones'] = 'oDocAdaxx';
        $mDatos['docadaxx']['camcone2'] = 'cDocAdaxx';
        $mDatos['docadaxx']['descones'] = 'APLICA DUE AGENT';

        $mDatos['docadcxx']['camcones'] = 'oDocAdcxx';
        $mDatos['docadcxx']['camcone2'] = 'cDocAdcxx';
        $mDatos['docadcxx']['descones'] = 'APLICA DUE CARRIER';

        $mDatos['docafaix']['camcones'] = 'oDocAfai';
        $mDatos['docafaix']['camcone2'] = 'cDocAfai';
        $mDatos['docafaix']['descones'] = 'APLICA FLETE AEREO INTERNACIONAL';

        $mDatos['docafmix']['camcones'] = 'oDocAfmi';
        $mDatos['docafmix']['camcone2'] = 'cDocAfmi';
        $mDatos['docafmix']['descones'] = 'APLICA FLETE MARITIMO INTERNACIONAL';

        $mDatos['docageex']['camcones'] = 'oDocAgeex';
        $mDatos['docageex']['camcone2'] = 'cDocAgeex';
        $mDatos['docageex']['descones'] = 'APLICA GASTOS EN EL EXTERIOR';

        $mDatos['docapmxx']['camcones'] = 'oDocApmx';
        $mDatos['docapmxx']['camcone2'] = 'cDocApmx';
        $mDatos['docapmxx']['descones'] = 'APLICA POLIZA DE MERCANCIA';

        $mDatos['docacfax']['camcones'] = 'oDocAcfax';
        $mDatos['docacfax']['camcone2'] = 'cDocAcfax';
        $mDatos['docacfax']['descones'] = 'APLICA COMISION FLETE AEREO';

        $mDatos['docageva']['camcones'] = 'oDocAgeVa';
        $mDatos['docageva']['camcone2'] = 'cDocAgeVa';
        $mDatos['docageva']['descones'] = 'APLICA AGENCIAMIENTO VARIABLE';

        $mDatos['doccanki']['camcones'] = 'oDocCanKi';
        $mDatos['doccanki']['camcone2'] = 'cDocCanKi';
        $mDatos['doccanki']['descones'] = 'APLICA CANTIDAD DE KILOS';

        $mDatos['doccanto']['camcones'] = 'oDocCanTo';
        $mDatos['doccanto']['camcone2'] = 'cDocCanTo';
        $mDatos['doccanto']['descones'] = 'APLICA CANTIDAD DE TONELADAS';

        $mDatos['doccant1']['camcones'] = 'oDocCanT1';
        $mDatos['doccant1']['camcone2'] = 'cDocCanT1';
        $mDatos['doccant1']['descones'] = 'APLICA CANTIDAD DE TONELADAS RECIBO DE MERCANCIAS';

        $mDatos['doccant4']['camcones'] = 'oDocCanT4';
        $mDatos['doccant4']['camcone2'] = 'cDocCanT4';
        $mDatos['doccant4']['descones'] = 'APLICA CANTIDAD DE TONELADAS CLASIFICACION ARANCELARIA';

        $mDatos['docsimca']['camcones'] = 'oDocSimCa';
        $mDatos['docsimca']['camcone2'] = 'cDocSimCa';
        $mDatos['docsimca']['descones'] = 'APLICA CANTIDAD DE SIMCARDS';

        $mDatos['docterxx']['camcones'] = 'oDocTer';
        $mDatos['docterxx']['camcone2'] = 'cDocTer';
        $mDatos['docterxx']['descones'] = 'APLICA CANTIDAD DE TERMINALES';

        $mDatos['doctabxx']['camcones'] = 'oDocTab';
        $mDatos['doctabxx']['camcone2'] = 'cDocTab';
        $mDatos['doctabxx']['descones'] = 'APLICA CANTIDAD DE TABLETS';

        $mDatos['docmodxx']['camcones'] = 'oDocMod';
        $mDatos['docmodxx']['camcone2'] = 'cDocMod';
        $mDatos['docmodxx']['descones'] = 'APLICA CANTIDAD DE MODEMS';

        $mDatos['doccanga']['camcones'] = 'oDocCanGa';
        $mDatos['doccanga']['camcone2'] = 'cDocCanGa';
        $mDatos['doccanga']['descones'] = 'APLICA CANTIDAD DE GASTOS';

        $mDatos['doccanve']['camcones'] = 'oDocCanVe';
        $mDatos['doccanve']['camcone2'] = 'cDocCanVe';
        $mDatos['doccanve']['descones'] = 'APLICA CANTIDAD DE VEHICULOS';

        $mDatos['doccanp1']['camcones'] = 'oDocCanP1';
        $mDatos['doccanp1']['camcone2'] = 'cDocCanP1';
        $mDatos['doccanp1']['descones'] = 'APLICA CANTIDAD DE PERIODOS DEPOSITO ADUANERO';

        $mDatos['doccanpe']['camcones'] = 'oDocCanPe';
        $mDatos['doccanpe']['camcone2'] = 'cDocCanPe';
        $mDatos['doccanpe']['descones'] = 'APLICA CANTIDAD DE PERIODOS ALMACENAMIENTO CARGA';

        $mDatos['docmerte']['camcones'] = 'oDocMerTe';
        $mDatos['docmerte']['camcone2'] = 'cDocMerTe';
        $mDatos['docmerte']['descones'] = 'APLICA CANTIDAD DE MERCANCIAS TERREMOTO';

        $mDatos['docalmxx']['camcones'] = 'oDocAlm';
        $mDatos['docalmxx']['camcone2'] = 'cDocAlm';
        $mDatos['docalmxx']['descones'] = 'APLICA ALMACENAMIENTO CARGA';

        $mDatos['doccanme']['camcones'] = 'oDocCanMe';
        $mDatos['doccanme']['camcone2'] = 'cDocCanMe';
        $mDatos['doccanme']['descones'] = 'APLICA CANTIDAD DE METROS';

        $mDatos['doccandi']['camcones'] = 'oDocCanDi';
        $mDatos['doccandi']['camcone2'] = 'cDocCanDi';
        $mDatos['doccandi']['descones'] = 'APLICA CANTIDAD DE DIAS';
        $mDatos['doccandi']['accionjs'] = '';

        $mDatos['docver2x']['camcones'] = 'oDocVer2';
        $mDatos['docver2x']['camcone2'] = 'cDocVer2';
        $mDatos['docver2x']['descones'] = 'APLICA CANTIDAD DE RECIBO DE MERCANCIAS';

        $mDatos['docsegam']['camcones'] = 'oDocSegAm';
        $mDatos['docsegam']['camcone2'] = 'cDocSegAm';
        $mDatos['docsegam']['descones'] = 'APLICA CANTIDAD SEGUROS AMIT';
        $mDatos['docsegam']['accionjs'] = '';

        $mDatos['docsegme']['camcones'] = 'oDocSegMe';
        $mDatos['docsegme']['camcone2'] = 'cDocSegMe';
        $mDatos['docsegme']['descones'] = 'APLICA CANTIDAD SEGUROS MERCANCIA TERREMOTO';

        $mDatos['doccon20']['camcones'] = 'oDocCon20';
        $mDatos['doccon20']['camcone2'] = 'cDocCon20';
        $mDatos['doccon20']['descones'] = 'CANTIDAD DE CONTENEDORES DE 20 PIES';

        $mDatos['doccon40']['camcones'] = 'oDocCon40';
        $mDatos['doccon40']['camcone2'] = 'cDocCon40';
        $mDatos['doccon40']['descones'] = 'CANTIDAD DE CONTENEDORES DE 40 PIES';

        $mDatos['doccant5']['camcones'] = 'oDocCant5';
        $mDatos['doccant5']['camcone2'] = 'cDocCant5';
        $mDatos['doccant5']['descones'] = 'CANTIDAD DE TONELADAS';

        $mDatos['docvlfij']['camcones'] = 'oDocVlFij';
        $mDatos['docvlfij']['camcone2'] = 'cDocVlFij';
        $mDatos['docvlfij']['descones'] = 'CANTIDAD DE UNIDADES';

        $mDatos['doccubxx']['camcones'] = 'oDocCuB';
        $mDatos['doccubxx']['camcone2'] = 'cDocCuB';
        $mDatos['doccubxx']['descones'] = 'CANTIDAD CUBICAJE TOTAL';
        $mDatos['doccubxx']['accionjs'] = 'PROCC';

        $mDatos['docvlcub']['camcones'] = 'oDocVlCub';
        $mDatos['docvlcub']['camcone2'] = 'cDocVlCub';
        $mDatos['docvlcub']['descones'] = 'VALOR CUBICAJE DEL DO';
        $mDatos['docvlcub']['accionjs'] = 'PROVC';

        $mDatos['docproxx']['camcones'] = 'oDocPro';
        $mDatos['docproxx']['camcone2'] = 'oDocPro';
        $mDatos['docproxx']['descones'] = 'APLICA PRORRATEO';
        $mDatos['docproxx']['accionjs'] = 'PRO';

        $mDatos['doccanun']['camcones'] = 'oDocCanUn';
        $mDatos['doccanun']['camcone2'] = 'cDocCanUn';
        $mDatos['doccanun']['descones'] = 'APLICA CANTIDAD DE UNIDADES';

        // Array con campos de cantidad que aplican para importaciones agrupados por nombres
        $vCamCan = ["2","3","4","5", "6", "7", "8", "9"];
        for ($nC=0; $nC < count($vCamCan); $nC++) {
          $mDatos['doccanu'.$vCamCan[$nC]]['camcones'] = 'oDocCanU'.$vCamCan[$nC];
          $mDatos['doccanu'.$vCamCan[$nC]]['camcone2'] = 'cDocCanU'.$vCamCan[$nC];
          $mDatos['doccanu'.$vCamCan[$nC]]['descones'] = 'CANTIDAD DE UNIDADES';
        }

        $vCamCan = ["10","11","12","13","14","15","16","17","18","19",
                    "20","21","22","23","24","25","26","27","28","29",
                    "30","31","32","33","34","35","36","37","38","39",
                    "40","41","42","43","44","49",
                    "50"];
        for ($nC=0; $nC < count($vCamCan); $nC++) {
          $mDatos['doccau'.$vCamCan[$nC]]['camcones'] = 'oDocCau'.$vCamCan[$nC];
          $mDatos['doccau'.$vCamCan[$nC]]['camcone2'] = 'cDocCau'.$vCamCan[$nC];
          $mDatos['doccau'.$vCamCan[$nC]]['descones'] = 'CANTIDAD DE UNIDADES';
        }

        $mDatos['doccansa']['camcones'] = 'oDocCanSa';
        $mDatos['doccansa']['camcone2'] = 'cDocCanSa';
        $mDatos['doccansa']['descones'] = 'CANTIDAD DE SACOS';

        $mDatos['docvlter']['camcones'] = 'oDocVlTer';
        $mDatos['docvlter']['camcone2'] = 'cDocVlTer';
        $mDatos['docvlter']['descones'] = 'APLICA VALOR DE LOS PAGOS A TERCEROS';

        $mDatos['doccovar']['camcones'] = 'oDocCoVar';
        $mDatos['doccovar']['camcone2'] = 'cDocCoVar';
        $mDatos['doccovar']['descones'] = 'APLICA COBRO VARIABLE';

        $mDatos['doccrefa']['camcones'] = 'oDocRecFa';
        $mDatos['doccrefa']['camcone2'] = '';
        $mDatos['doccrefa']['descones'] = 'APLICA REFACTURACION';

        if ($pArrayParametros['cliidxxx'] != "") {
          //Busco las condiciones comerciales del cliente para ver si aplica
          //APLICA FORMULARIOS VIRTUALES (docafvex)
          //APLICA HOJA ADICIONAL PARA FORMULARIOS VIRTUALES (docafvha)

          //Cliente o factura a
          $cCliFor  = ($pArrayParametros['teridint'] != "") ? $pArrayParametros['teridint'] : $pArrayParametros['cliidxxx'];

          $qConCom  = "SELECT cccfvexx, cccfvhax ";
          $qConCom .= "FROM $cAlfa.fpar0151 ";
          $qConCom .= "WHERE ";
          $qConCom .= "cliidxxx = \"$cCliFor\" AND ";
          $qConCom .= "regestxx = \"ACTIVO\" LIMIT 0,1";
          $xConCom  = f_MySql("SELECT","",$qConCom,$xConexion01,"");
          //f_Mensaje(__FILE__,__LINE__,$qConCom." ~ ".mysql_num_rows($xConCom));
          if (mysql_num_rows($xConCom) > 0) {
            $xRCC = mysql_fetch_array($xConCom);
            if ($xRCC['cccfvexx'] == 'SI') {
              $mCampos[] = 'docafvex';
              // Descripcion para el servicio cuando no aplica en ninguna tarifa
              $mDatos['docafvex']['serdesxx'] = "CONDICION COMERCIAL CLIENTE";
            }
            if ($xRCC['cccfvhax'] == 'SI') {
              $mCampos[] = 'docafvha';
              // Descripcion para el servicio cuando no aplica en ninguna tarifa
              $mDatos['docafvha']['serdesxx'] = "CONDICION COMERCIAL CLIENTE";
            }
          }
        } else {
          // se incluyen los campos de formulario por defecto
          $mCampos[] = 'docafvex';
          $mCampos[] = 'docafvha';
          // Descripcion para el servicio cuando no aplica en ninguna tarifa
          $mDatos['docafvex']['serdesxx'] = "CONDICION COMERCIAL CLIENTE";
          $mDatos['docafvha']['serdesxx'] = "CONDICION COMERCIAL CLIENTE";
        }
      break;
      case "TRANSITO":
        // Matriz con los nombres de los radio y su descripcion
        // El indice principal corresponde al nombre del campo en la tabla sys00121
        // El indicie camcones corresponde al nombre del Radio Button
        // El indicie camcone2 corresponde al nombre del campo donde se digita el valor
        $mDatos['dochrdxx']['camcones'] = 'oDocHrd';
        $mDatos['dochrdxx']['camcone2'] = 'cDocHrd';
        $mDatos['dochrdxx']['descones'] = 'APLICAN HORAS DE RECONOCIMIENTO';

        $mDatos['docfobxx']['camcones'] = 'oDocFob';
        $mDatos['docfobxx']['camcone2'] = 'cDocFob';
        $mDatos['docfobxx']['descones'] = 'APLICA VALOR FOB DE LA NEGOCIACION';

        $mDatos['doctrmxx']['camcones'] = 'oDocTrm';
        $mDatos['doctrmxx']['camcone2'] = 'cDocTrm';
        $mDatos['doctrmxx']['descones'] = 'APLICA TRM DE LA NEGOCIACION';

        $mDatos['docssxxx']['camcones'] = 'oDocSs';
        $mDatos['docssxxx']['camcone2'] = 'cDocSs';
        $mDatos['docssxxx']['descones'] = 'APLICAN SELLOS DE SEGURIDAD';

        $mDatos['docdecxx']['camcones'] = 'oDocDec';
        $mDatos['docdecxx']['camcone2'] = 'cDocDec';
        $mDatos['docdecxx']['descones'] = 'APLICAN CANTIDAD DE DECLARACIONES';

        $mDatos['docverme']['camcones'] = 'oDocVer';
        $mDatos['docverme']['camcone2'] = 'cDocVer';
        $mDatos['docverme']['descones'] = 'APLICAN VERIFICACION DE MERCANCIA';

        $mDatos['doccagxx']['camcones'] = 'oDocCag';
        $mDatos['doccagxx']['camcone2'] = '';
        $mDatos['doccagxx']['descones'] = 'APLICA CARGA AL GRANEL';
        $mDatos['doccagxx']['accionjs'] = 'CAG';

        $mDatos['docc20xx']['camcones'] = 'oDocC20';
        $mDatos['docc20xx']['camcone2'] = 'cDocC20';
        $mDatos['docc20xx']['descones'] = 'APLICAN CANTIDAD DE CONTENEDORES DE 20 PIES';
        $mDatos['docc20xx']['accionjs'] = 'C20';

        $mDatos['docc40xx']['camcones'] = 'oDocC40';
        $mDatos['docc40xx']['camcone2'] = 'cDocC40';
        $mDatos['docc40xx']['descones'] = 'APLICAN CANTIDAD DE CONTENEDORES DE 40 PIES';
        $mDatos['docc40xx']['accionjs'] = 'C40';

        $mDatos['doccsxxx']['camcones'] = 'oDocCs';
        $mDatos['doccsxxx']['camcone2'] = '';
        $mDatos['doccsxxx']['descones'] = 'APLICA CARGA SUELTA';
        $mDatos['doccsxxx']['accionjs'] = 'CS';

        $mDatos['doccsuxx']['camcones'] = 'oDocCsu';
        $mDatos['doccsuxx']['camcone2'] = 'cDocCsu';
        $mDatos['doccsuxx']['descones'] = 'APLICAN UNIDADES DE CARGA SUELTA';
        $mDatos['doccsuxx']['accionjs'] = 'CSU';

        $mDatos['docafdmx']['camcones'] = 'oDocAfdm';
        $mDatos['docafdmx']['camcone2'] = 'cDocAfdm';
        $mDatos['docafdmx']['descones'] = 'APLICA FORMULARIO DAV MAGNETICAS';

        $mDatos['docsptax']['camcones'] = 'oDocSpta';
        $mDatos['docsptax']['camcone2'] = '';
        $mDatos['docsptax']['descones'] = 'APLICA SEGURO PARA TRANSITO ADUANERO';

        $mDatos['docapxxx']['camcones'] = 'oDocAp';
        $mDatos['docapxxx']['camcone2'] = '';
        $mDatos['docapxxx']['descones'] = 'APLICA ACOMPA&Ntilde;AMIENTO POSTERIOR';

        $mDatos['doctarim']['camcones'] = 'oDocTarIm';
        $mDatos['doctarim']['camcone2'] = '';
        $mDatos['doctarim']['descones'] = 'APLICAR TARIFAS DE IMPORTACION';

        $mDatos['docvciat']['camcones'] = 'APLICA VALOR COMISION POR INTERMEDIACION ADUANERA TRANSITO';
        $mDatos['docvciat']['camcone2'] = 'oDocAvci';
        $mDatos['docvciat']['descones'] = 'cDocAvci';

        $mDatos['doccanin']['camcones'] = 'oDocCanIn';
        $mDatos['doccanin']['camcone2'] = 'cDocCanIn';
        $mDatos['doccanin']['descones'] = 'CANTIDAD DE INSPECCIONES';

        $mDatos['docvedta']['camcones'] = 'oDocVeDta';
        $mDatos['docvedta']['camcone2'] = 'cDocVeDta';
        $mDatos['docvedta']['descones'] = 'VALOR ELABORACION DOCUMENTO DE TRANSITO ADUANERO';

        $mDatos['docvafot']['camcones'] = 'oDocVaFot';
        $mDatos['docvafot']['camcone2'] = 'cDocVaFot';
        $mDatos['docvafot']['descones'] = 'VALOR FOTOCOPIAS';

        $mDatos['docvgvar']['camcones'] = 'oDocVgVar';
        $mDatos['docvgvar']['camcone2'] = 'cDocVgVar';
        $mDatos['docvgvar']['descones'] = 'VALOR GASTOS VARIOS';

        $mDatos['docvstad']['camcones'] = 'oDocVsTad';
        $mDatos['docvstad']['camcone2'] = 'cDocVsTad';
        $mDatos['docvstad']['descones'] = 'VALOR SEGURO PARA TRANSITO ADUANERO';

        $mDatos['docvapos']['camcones'] = 'oDocVaPos';
        $mDatos['docvapos']['camcone2'] = 'cDocVaPos';
        $mDatos['docvapos']['descones'] = 'VALOR ACOMPAÑAMIENTO POSTERIOR';

        $mDatos['docvrocg']['camcones'] = 'oDocVrOcg';
        $mDatos['docvrocg']['camcone2'] = 'cDocVrOcg';
        $mDatos['docvrocg']['descones'] = 'VALOR REINTEGRO DE OTROS COSTOS Y GASTOS';

        $mDatos['docvcdta']['camcones'] = 'oDocVcDta';
        $mDatos['docvcdta']['camcone2'] = 'cDocVcDta';
        $mDatos['docvcdta']['descones'] = 'VALOR COORDINACION ADUANERA DTA';

        $mDatos['docvassp']['camcones'] = 'oDocVaSsp';
        $mDatos['docvassp']['camcone2'] = 'cDocVaSsp';
        $mDatos['docvassp']['descones'] = 'VALOR SELLOS DE SEGURIDAD PRECINTOS';

        $mDatos['docvavem']['camcones'] = 'oDocVaVem';
        $mDatos['docvavem']['camcone2'] = 'cDocVaVem';
        $mDatos['docvavem']['descones'] = 'VALOR VERIFICACION DE MERCANCIAS';

        $mDatos['docc40hc']['camcones'] = 'oDocCdDta';
        $mDatos['docc40hc']['camcone2'] = 'cDocCdDta';
        $mDatos['docc40hc']['descones'] = 'CANTIDAD DECLARACIONES DTA';

        $mDatos['doccanun']['camcones'] = 'oDocCanUn';
        $mDatos['doccanun']['camcone2'] = 'cDocCanUn';
        $mDatos['doccanun']['descones'] = 'CANTIDAD DE UNIDADES DTA';

        $vCamCan = ["47", "48", "49"];
        for ($nC=0; $nC < count($vCamCan); $nC++) {
        $mDatos['doccau'.$vCamCan[$nC]]['camcones'] = 'oDocCau'.$vCamCan[$nC];
        $mDatos['doccau'.$vCamCan[$nC]]['camcone2'] = 'cDocCau'.$vCamCan[$nC];
        $mDatos['doccau'.$vCamCan[$nC]]['descones'] = 'CANTIDAD DE UNIDADES';
        }

        $mDatos['doccau50']['camcones'] = 'oDocCau50';
        $mDatos['doccau50']['camcone2'] = 'cDocCau50';
        $mDatos['doccau50']['descones'] = 'CANTIDAD DE UNIDADES DTA';

        $mDatos['doccrefa']['camcones'] = 'oDocRecFa';
        $mDatos['doccrefa']['camcone2'] = '';
        $mDatos['doccrefa']['descones'] = 'APLICA REFACTURACION';

        //Campos por defecto para importaciones, esta condicion especial debe mostrarse para todos los DO
        //Si se incluye una nueva condicion en este vector debe incluirse tambien en la facrtuacion en el archivo frfacvdo.php
        if ($vSysStr['financiero_aplica_tarifa_importacion_a_transito'] == "SI") {
          $mCampos = array('docfobxx','doctrmxx','docdecxx','doctarim');
        } else {
          $mCampos = array('docfobxx','doctrmxx','docdecxx');
        }

        if ($pArrayParametros['cliidxxx'] != "") {
          //Busco las condiciones comerciales del cliente para ver si aplica
          //APLICA FORMULARIOS VIRTUALES (docafvex)
          //APLICA HOJA ADICIONAL PARA FORMULARIOS VIRTUALES (docafvha)

          //Cliente o factura a
          $cCliFor  = ($pArrayParametros['teridint'] != "") ? $pArrayParametros['teridint'] : $pArrayParametros['cliidxxx'];

          $qConCom  = "SELECT cccfdmxx ";
          $qConCom .= "FROM $cAlfa.fpar0151 ";
          $qConCom .= "WHERE ";
          $qConCom .= "cliidxxx = \"$cCliFor\" AND ";
          $qConCom .= "regestxx = \"ACTIVO\" LIMIT 0,1";
          $xConCom  = f_MySql("SELECT","",$qConCom,$xConexion01,"");
          //f_Mensaje(__FILE__,__LINE__,$qConCom." ~ ".mysql_num_rows($xConCom));
          if (mysql_num_rows($xConCom) > 0) {
            $xRCC = mysql_fetch_array($xConCom);
            if ($xRCC['cccfdmxx'] == 'SI') {
              $mCampos[] = 'docafdmx';
              // Descripcion para el servicio cuando no aplica en ninguna tarifa
              $mDatos['docafdmx']['serdesxx'] = "CONDICION COMERCIAL CLIENTE";
            }
          }
        }else {
          // se incluyen los campos de formulario por defecto
          $mCampos[] = 'docafdmx';
          // Descripcion para el servicio cuando no aplica en ninguna tarifa
          $mDatos['docafdmx']['serdesxx'] = "CONDICION COMERCIAL CLIENTE";
        }        
      break;
      case "OTROS":
        // Matriz con los nombres de los radio y su descripcion
        // El indice principal corresponde al nombre del campo en la tabla sys00121
        // El indicie camcones corresponde al nombre del Radio Button
        // El indicie camcone2 corresponde al nombre del campo donde se digita el valor
        $mDatos['doccanun']['camcones'] = 'oDocCanUn';
        $mDatos['doccanun']['camcone2'] = 'cDocCanUn';
        $mDatos['doccanun']['descones'] = 'CANTIDAD DE UNIDADES';

        // Array con campos de cantidad que aplican para Otros agrupados por nombres
        $vCamCan = ["54", "55", "56", "57", "58", "59", "60", "61", "62"];
        for ($nC=0; $nC < count($vCamCan); $nC++) {
          $mDatos['doccau'.$vCamCan[$nC]]['camcones'] = 'oDocCau'.$vCamCan[$nC];
          $mDatos['doccau'.$vCamCan[$nC]]['camcone2'] = 'cDocCau'.$vCamCan[$nC];
          $mDatos['doccau'.$vCamCan[$nC]]['descones'] = 'CANTIDAD DE UNIDADES';
        }

        //Campos por defecto para importaciones, esta condicion especial debe mostrarse para todos los DO
        //Si se incluye una nueva condicion en este vector debe incluirse tambien en la facrtuacion en el archivo frfacvdo.php
        $mCampos  = array();
      break;
      default:
        $nSwitch = 1;
        $mReturn[count($mReturn)] = "El Tipo de Operacion [{$pArrayParametros['doctipxx']}] No es Valido.";
      break;
    }

    if ($nSwitch == 0) {
      // Busco las tarifas del cliente para traer descripciones personalizadas de las condiciones especiales
      // Solo se consulta si se envia el parametro de cliente
      if ($pArrayParametros['cliidxxx'] != "") {
        /**
         * Condiciones Personalizadas por defecto
         */
        $qCampos  = "SELECT seridxxx,fcoidxxx,dcecampo,dcedesxx ";
        $qCampos .= "FROM $cAlfa.fpar0145 ";
        $qCampos .= "WHERE ";
        $qCampos .= "regestxx = \"ACTIVO\" ";
        $xCampos  = f_MySql("SELECT","",$qCampos,$xConexion01,"");
        // f_Mensaje(__FILE__,__LINE__,$qCampos."~".mysql_num_rows($xCampos));
        $vCampos = array();
        while($xRC = mysql_fetch_assoc($xCampos)){
          $vCampos["{$xRC['seridxxx']}~{$xRC['fcoidxxx']}~{$xRC['dcecampo']}"] = $xRC['dcedesxx'];
        }

        // Si se envia grupo de tarifas se buscan las tarifas del grupo
        // si se envia facturar a se buscan las tarifas del facturar a
        // sino se buscan las tarifas del cliente
        $cTarCli  = ($pArrayParametros['gtaidxxx'] != "") ? $pArrayParametros['gtaidxxx'] : (($pArrayParametros['teridint'] != "") ? $pArrayParametros['teridint'] : $pArrayParametros['cliidxxx']);

        $qTarifas  = "SELECT seridxxx, fcoidxxx, fcopcexx, serdespc ";
        $qTarifas .= "FROM $cAlfa.fpar0131 ";
        $qTarifas .= "WHERE ";
        $qTarifas .= "cliidxxx = \"$cTarCli\" AND ";
        $qTarifas .= "fcotptxx = \"{$pArrayParametros['doctepxx']}\"      AND ";
        $qTarifas .= "fcotpixx = \"{$pArrayParametros['doctepid']}\"      AND ";
        if ($pArrayParametros['sucidxxx'] != "") {
          $qTarifas .= "sucidxxx LIKE \"%{$pArrayParametros['sucidxxx']}%\" AND ";
        }        
        $qTarifas .= "fcotopxx LIKE \"%{$pArrayParametros['doctipxx']}%\" AND ";
        if ($pArrayParametros['docmtrxx'] != "") {
          $qTarifas .= "fcomtrxx LIKE \"%{$pArrayParametros['docmtrxx']}%\" AND ";
        }
        $qTarifas .= "tartipxx = \"{$pArrayParametros['tartipxx']}\"      AND ";
        $qTarifas .= "regestxx = \"ACTIVO\"";
        $xTarifas  = f_MySql("SELECT","",$qTarifas,$xConexion01,"");
        // f_Mensaje(__FILE__,__LINE__,$qTarifas." ~ ".mysql_num_rows($xTarifas));
        
        // Las tarifas se deben guardar en un array ya que primero deben buscarse las descripciones
        // personalizadas por concepto de cobro, y luego se deben sobre escribir con las descripciones
        // personalizadas por tarifa del cliente
        $mTarifas = array();
        while ($xRT = mysql_fetch_assoc($xTarifas)) {
          $mTarifas[] = $xRT;
        }

        //Primero se buscan las descripciones personalizadas por concepto de cobro
        $mCondicionEspPers = array();
        for ($nT=0; $nT < count($mTarifas); $nT++) {
          $qConEsp  = "SELECT seridxxx, serdesxx, serdespx, sercones ";
          $qConEsp .= "FROM $cAlfa.fpar0129 ";
          $qConEsp .= "WHERE ";
          $qConEsp .= "seridxxx = \"{$mTarifas[$nT]['seridxxx']}\" AND ";
          $qConEsp .= "regestxx = \"ACTIVO\"";
          $xConEsp  = f_MySql("SELECT","",$qConEsp,$xConexion01,"");
          // f_Mensaje(__FILE__,__LINE__,$qConEsp." ~ ".mysql_num_rows($xConEsp));
          while($xRCE = mysql_fetch_assoc($xConEsp)){
            $mAux = explode("|",$xRCE['sercones']);
            for($i = 0; $i < count($mAux); $i++ ) {
              if ($mAux[$i] != "") {
                $mAux02 = explode("~",$mAux[$i]);
                for($j = 1; $j < count($mAux02); $j++) {
                  if ($mAux02[0] == $mTarifas[$nT]['fcoidxxx']) {
                    if ($mTarifas[$nT]['serdespc'] != "") {
                      $mDatos[$mAux02[$j]]['seridxxx'][] = "[".$xRCE['seridxxx']."] ".$mTarifas[$nT]['serdespc'];
                    } elseif ($xRCE['serdespx'] != "") {
                      $mDatos[$mAux02[$j]]['seridxxx'][] = "[".$xRCE['seridxxx']."] ".$xRCE['serdespx'];
                    } else {
                      $mDatos[$mAux02[$j]]['seridxxx'][] = "[".$xRCE['seridxxx']."] ".$xRCE['serdesxx'];
                    }                    
                    switch ($pArrayParametros['doctipxx']) {
                      case "IMPORTACION":
                        if($mAux02[$j] == "dochrexx" && $mTarifas[$nT]['seridxxx'] == "101" && $mTarifas[$nT]['fcoidxxx'] == "100"){
                          $mDatos['dochrexx']['moscanho'] = "NO";
                        }
                      break;
                      default:
                        //No hace nada
                      break;
                    }
                    $mCampos[] = $mAux02[$j];
                    if($vCampos["{$mTarifas[$nT]['seridxxx']}~{$mTarifas[$nT]['fcoidxxx']}~{$mAux02[$j]}"] != ""){
                      $mCondicionEspPers[$mAux02[$j]] = $vCampos["{$mTarifas[$nT]['seridxxx']}~{$mTarifas[$nT]['fcoidxxx']}~{$mAux02[$j]}"];
                      $mDatos[$mAux02[$j]]['descrixx'][] = $vCampos["{$mTarifas[$nT]['seridxxx']}~{$mTarifas[$nT]['fcoidxxx']}~{$mAux02[$j]}"];
                    }
                  }
                }
              }
            }
          }
        }

        // Descripcion personalizada por tarifa del cliente
        for ($nT=0; $nT < count($mTarifas); $nT++) {
          $mAux = explode("|",$mTarifas[$nT]['fcopcexx']);
          for($i = 0; $i < count($mAux); $i++ ) {
            if ($mAux[$i] != "") {
              $mAux02 = explode("~",$mAux[$i]);
              for($j = 1; $j < count($mAux02); $j++) {
                $mCondicionEspPers[$mAux02[0]] = $mAux02[1];
                $mDatos[$mAux02[0]]['descrixx'][] = $mAux02[1];
              }
            }
          }
        }

        // si existe el campo de la condicion especial modificada en el array  $mDatos modifico la descripcion del campo
        foreach ($mCondicionEspPers as $key => $value) {
          if (array_key_exists(strtolower($key),$mDatos)) {
            $mDatos[strtolower($key)]['descones'] = $value;
          }
        }

        //Se marcan los campos que deben ser visibles en las tarifas
        for($nC=0; $nC<count($mCampos); $nC++) {
          $mDatos[$mCampos[$nC]]['mostrarx'] = "SI";
          // Si no se encontro descripcion personalizada se asigna la descripcion por defecto
          if (count($mDatos[$mCampos[$nC]]['descrixx']) == 0) {
            $mDatos[$mCampos[$nC]]['descrixx'][] = $mDatos[$mCampos[$nC]]['descones'];
          }
          // Si no se encontro servicio se envia un texto fijo
          if (count($mDatos[$mCampos[$nC]]['seridxxx']) == 0) {
            $mDatos[$mCampos[$nC]]['seridxxx'][] = ($mDatos[$mCampos[$nC]]['serdesxx'] != "") ? $mDatos[$mCampos[$nC]]['serdesxx'] : "NO APLICA";
          }
        }
      }
    }

    if ($nSwitch == 0) {
      $mReturn[0] = "true";
      $mReturn[1] = $mDatos;
    } else {
      $mReturn[0] = "false";
    }
    return $mReturn;
  }

  /**
   * Metodo para Validar las condiciones especiales
   */
  function fnValidarCondiciones($pArrayParametros) {

    global $xConexion01; global $cAlfa; global $vSysStr;

    /**
     * Recibe como Parametro una Matriz con las siguientes posiciones:
     * 
     * $pArrayParametros //Array con todos los valores del post deben llegar de la siguiente manera
     * $pArrayParametros[Nombre radio button]
     * $pArrayParametros[Nombre en el formulario del campo de texto principal (opcional)]
     * $pArrayParametros[Nombre en el formulario del campo de texto adicional (opcional)]
     * $pArrayParametros[conespxx]  //Array con las condiciones especiales que aplican para el DO
     * $pArrayParametros[datosdox]  //Array con los datos del DO
     * $pArrayParametros[cTerIdInt] //Facturar a
     * $pArrayParametros[cCcAplFa]  //Aplica facturas del facturar a
     */

    /**
     * Variable para hacer el retorno.
     * El listado de errores se retorna a partir de la posicion 2
     * @var array
     */
    $mReturn    = array();
    $mReturn[0] = "";
    $mReturn[1] = "";

    /**
     * Variable para saber si hay o no errores de validacion.
     * @var number
     */
    $nSwitch = 0;

    //Campos condiciones especiales
    $mTabId = $pArrayParametros['conespxx'];
    //Datos DO
    $xRDD   = $pArrayParametros['datosdox'];

    //Validar datos contra el DO
    $nValidarDo = "NO";
    if ($xRDD['cliidxxx'] != "" && $xRDD['docidxxx'] != "" && $xRDD['docsufxx'] != "" && $xRDD['sucidxxx'] != "") {
      $nValidarDo = "SI";
    }

    // Si la variable de sistema es NO, checkeo que todos los campos estén diligenciados.
    // Si no se envia el DO no se valida la condicion que todos las condiciones especiales esten digitadas
    if ($vSysStr['financiero_permitir_condiciones_especiales_parciales'] == 'NO' && $nValidarDo == "SI") {
      #Verifico que haya seleccionado SI o NO en todas las condiciones especiales
      foreach ($mTabId as $cKey => $cValue) {
        if ($mTabId[$cKey]['mostrarx'] == "SI") {
          // f_Mensaje(__FILE__,__LINE__,$mTabId[$cKey]['camcones']."~".$pArrayParametros[$mTabId[$cKey]['camcones']]);
          if($pArrayParametros[$mTabId[$cKey]['camcones']] == "") {
            $nSwitch = 1;
            $cMsj  = "La Condicion Especial [{$mTabId[$cKey]['descones']}] No Puede Ser Vacia.";
            $mReturn[count($mReturn)] = $cMsj;
          }
        }
      }
      #Fin Verfifico que haya seleccionado SI o NO en todas las condiciones especiales
    }

    switch ($xRDD['doctipxx']) {
      case "IMPORTACION":
        // Validaciones
        // Matriz con los nombres de los radio y su descripcion
        // Este metodo retorna una matriz con las siguientes posiciones
        // $mDatos[nombreCampoSys00121]['camcones'] => Nombre radio button
        // $mDatos[nombreCampoSys00121]['camcone2'] => Nombre en el formulario del campo de texto principal (opcional)
        // $mDatos[nombreCampoSys00121]['camcone3'] => Nombre en el formulario del campo de texto adicional (opcional)
        // $mDatos[nombreCampoSys00121]['camcamp3'] => Nombre en la sys00121 del campo de texto adicional (opcional, obligatorio si se asigna camcone3)
        // $mDatos[nombreCampoSys00121]['descones'] => Descripcion por defecto de la condicion especial
        // $mDatos[nombreCampoSys00121]['moscanho'] => Variable para indicar si mostrar o no el bloque de cantidad de horas de reconocimiento
        // $mDatos[nombreCampoSys00121]['accionjs'] => Condicon en el metodo f_Prende_Check para realizar accione
        foreach ($mTabId as $cKey => $cValue) {
          switch ($cKey) {
            case 'docarxxx':
            case 'docaflex':
              // Valores Vacios
              if ($pArrayParametros[$mTabId[$cKey]['camcones']] == "SI") {
                if ($pArrayParametros[$mTabId[$cKey]['camcone2']] == "" ) {
                  $nSwitch = 1;
                  $cMsj  = "La Condicion Especial [".$mTabId[$cKey]['descones']."] No Puede Ser Vacia.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
              } elseif ($pArrayParametros[$mTabId[$cKey]['camcones']] == "NO") {
                $pArrayParametros[$mTabId[$cKey]['camcone2']] = "NO";
              }
            break;
            case 'doccfvxx':
              // Formularios virtuales
              if ($pArrayParametros[$mTabId[$cKey]['camcones']] == "SI") {
                if (!preg_match("/^(\d)?(\d)*\.?\d*$/", $pArrayParametros[$mTabId[$cKey]['camcone2']])) {
                  $nSwitch = 1;
                  $cMsj  = "La Condicion Especial [".$mTabId[$cKey]['descones']."] No Puede Ser Menor o Igual a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                } else {
                  if ($nValidarDo == "SI") {
                    // Validando que la cantidad de formularios virtuales no sea mayor a la cantidad de declaraciones en openComex.
                    $qDecImp  = "SELECT COUNT(SUBID2XX) AS DECIMPO ";
                    $qDecImp .= "FROM $cAlfa.SIAI0206 ";
                    $qDecImp .= "WHERE ";
                    $qDecImp .= "CLIIDXXX = \"{$xRDD['cliidxxx']}\" AND ";
                    $qDecImp .= "DOIIDXXX = \"{$xRDD['docidxxx']}\" AND ";
                    $qDecImp .= "DOISFIDX = \"{$xRDD['docsufxx']}\" AND ";
                    $qDecImp .= "ADMIDXXX = \"{$xRDD['sucidxxx']}\" AND ";
                    $qDecImp .= "REGESTXX = \"ACTIVO\" ";
                    $qDecImp .= "GROUP BY DOIIDXXX,DOISFIDX,ADMIDXXX ";
                    $qDecImp .= "ORDER BY DOIIDXXX,DOISFIDX,ADMIDXXX LIMIT 0,1";
                    $xDecImp  = f_MySql("SELECT","",$qDecImp,$xConexion01,"");
                    //f_Mensaje(__FILE__,__LINE__,$qDecImp." - ".mysql_num_rows($xDecImp));
                    $vDecImp  = mysql_fetch_assoc($xDecImp);
                    if ($pArrayParametros['cDocCfv'] > $vDecImp['DECIMPO']) {
                      $nSwitch = 1;
                      $cMsj  = "La Condicion Especial [".$mTabId[$cKey]['descones']."] [{$pArrayParametros['cDocCfv']}] no puede ser Mayor a la Cantidad de Declaraciones de Importacion [{$vDecImp['DECIMPO']}].";
                      $mReturn[count($mReturn)] = $cMsj;
                    }
                  }
                }
              } elseif ($pArrayParametros[$mTabId[$cKey]['camcones']] == "NO") {
                $pArrayParametros[$mTabId[$cKey]['camcone2']] = "NO";
              }
            break;
            case 'docafdmx':
              // Formularios DAV magneticas
              if ($pArrayParametros[$mTabId[$cKey]['camcones']] == "SI") {
                if (!preg_match("/^(\d)?(\d)*\.?\d*$/", $pArrayParametros[$mTabId[$cKey]['camcone2']])) {
                  $nSwitch = 1;
                  $cMsj  = "La Condicion Especial [".$mTabId[$cKey]['descones']."] No Puede Ser Menor o Igual a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                } else {
                  if ($nValidarDo == "SI") {
                    $qDAVMag  = "SELECT COUNT(DOIIDXXX) AS davmag ";
                    $qDAVMag .= "FROM $cAlfa.SIAI0207 ";
                    $qDAVMag .= "WHERE ";
                    $qDAVMag .= "CLIIDXXX = \"{$xRDD['cliidxxx']}\" AND ";
                    $qDAVMag .= "DOIIDXXX = \"{$xRDD['docidxxx']}\" AND ";
                    $qDAVMag .= "DOISFIDX = \"{$xRDD['docsufxx']}\" AND ";
                    $qDAVMag .= "ADMIDXXX = \"{$xRDD['sucidxxx']}\" AND ";
                    $qDAVMag .= "DOIGDAVM = \"1\" AND "; // Medio magnetico igual a 1
                    $qDAVMag .= "DVANOFOR != \"\" AND "; // Consecutivo de la decalracion diferente a 1
                    $qDAVMag .= "REGESTXX = \"ACTIVO\" ";
                    $qDAVMag .= "GROUP BY DOIIDXXX,DOISFIDX,ADMIDXXX ";
                    $qDAVMag .= "ORDER BY DOIIDXXX,DOISFIDX,ADMIDXXX LIMIT 0,1";
                    $xDAVMag  = f_MySql("SELECT","",$qDAVMag,$xConexion01,"");
                    //f_Mensaje(__FILE__,__LINE__,$qDAVMag." - ".mysql_num_rows($xDAVMag));
                    $vDAVMag  = mysql_fetch_assoc($xDAVMag);

                    if ($pArrayParametros[$mTabId[$cKey]['camcone2']] > $vDAVMag['davmag']) {
                      $nSwitch = 1;
                      $cMsj  = "La Condicion Especial [".$mTabId[$cKey]['descones']."] No Puede Ser Mayor a las DAV Magneticas Generadas por el Sistema [{$vDAVMag['davmag']}].";
                      $mReturn[count($mReturn)] = $cMsj;
                    }
                  }
                }
              } elseif ($pArrayParametros[$mTabId[$cKey]['camcones']] == "NO") {
                $pArrayParametros[$mTabId[$cKey]['camcone2']] = "NO";
              }
            break;
            case 'dochhdre':
              // VALOR x HORAS x HOMBRE x DIA CON VALOR ADICIONAL (1110)
              if ($pArrayParametros[$mTabId[$cKey]['camcones']] == "SI") {
                if ($pArrayParametros[$mTabId[$cKey]['camcone2']] == "" ) {
                  $nSwitch = 1;
                  $cMsj  = "La Condicion Especial [".$mTabId[$cKey]['descones']."] No Puede Ser Vacia.";
                  $mReturn[count($mReturn)] = $cMsj;
                } else {
                  $mValores = f_Explode_Array($pArrayParametros[$mTabId[$cKey]['camcone2']],",","-");

                  for ($j=0;$j<count($mValores);$j++) {
                    if (!preg_match("/^(\d)?(\d)*\.?\d*$/", $mValores[$j][0])) {
                      if ($mValores[$j][0] < 1) {
                        $nSwitch = 1;
                        $cMsj  = "Para la Condicion Especial [".$mTabId[$cKey]['descones']."]: La Cantidad de Hombres Para el Dia [".($j+1)."] Debe Ser Mayor a Cero.";
                        $mReturn[count($mReturn)] = $cMsj;
                      }
                    } else {
                      $nSwitch = 1;
                      $cMsj  = "Para la Condicion Especial [".$mTabId[$cKey]['descones']."]: La Cantidad de Hombres Para el Dia [".($j+1)."] Debe Ser Entero.";
                      $mReturn[count($mReturn)] = $cMsj;
                    }

                    if (!preg_match("/^(\d)?(\d)*\.?\d*$/", $mValores[$j][1])) {
                      if ($mValores[$j][1] < 1 ) {
                        $nSwitch = 1;
                        $cMsj  = "Para la Condicion Especial [".$mTabId[$cKey]['descones']."]: La Cantidad de Horas Para el Dia [".($j+1)."] Debe Ser Mayor a Cero.";
                        $mReturn[count($mReturn)] = $cMsj;
                      }
                    } else {
                      $nSwitch = 1;
                      $cMsj  = "Para la Condicion Especial [".$mTabId[$cKey]['descones']."]: La Cantidad de Horas Para el Dia [".($j+1)."] Debe Ser Entero.";
                      $mReturn[count($mReturn)] = $cMsj;
                    }
                  }
                }
              } elseif ($pArrayParametros[$mTabId[$cKey]['camcones']] == "NO") {
                $pArrayParametros[$mTabId[$cKey]['camcone2']] = "NO";
              }
            break;
            default:
              // Valores Numericos o solo radio
              if ($pArrayParametros[$mTabId[$cKey]['camcones']] == "SI") {
                // Validando campo principal si aplica
                if ($mTabId[$cKey]['camcone2'] != "") {
                  // Variable para indicar si mostrar o no el bloque de cantidad de horas de reconocimiento y si se debe valiar
                  if ($mTabId[$cKey]['moscanho'] != "NO") {
                    if ($pArrayParametros[$mTabId[$cKey]['camcone2']] == "" ) {
                      $nSwitch = 1;
                      $cMsj  = "La Condicion Especial [".$mTabId[$cKey]['descones']."] No Puede Ser Vacia.";
                      $mReturn[count($mReturn)] = $cMsj;
                    }
                  }
                  if (!preg_match("/^(\d)?(\d)*\.?\d*$/", $pArrayParametros[$mTabId[$cKey]['camcone2']])) {
                    $nSwitch = 1;
                    $cMsj  = "La Condicion Especial [".$mTabId[$cKey]['descones']."] Debe ser Numerica y el Separador de Decimales debe ser un Punto (.).";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
                // Validando campo adicional si aplica
                if ($mTabId[$cKey]['camcone3'] != "") {
                  // Solo se valida si es diferente de vacio
                  if ($pArrayParametros[$mTabId[$cKey]['camcone3']] != "" ) {
                    if (!preg_match("/^(\d)?(\d)*\.?\d*$/", $pArrayParametros[$mTabId[$cKey]['camcone3']])) {
                      $nSwitch = 1;
                      $cMsj  = "La Condicion Especial [".$mTabId[$cKey]['descones']."] (Valor Adicional) Debe ser Numerica y el Separador de Decimales debe ser un Punto (.).";
                      $mReturn[count($mReturn)] = $cMsj;
                    }
                  }                  
                }
              } elseif ($pArrayParametros[$mTabId[$cKey]['camcones']] == "NO") {
                if ($mTabId[$cKey]['camcone2'] != "") {
                  $pArrayParametros[$mTabId[$cKey]['camcone2']] = "NO";
                }
                if ($mTabId[$cKey]['camcone3'] != "") {
                  $pArrayParametros[$mTabId[$cKey]['camcone3']] = "NO";
                }
              }
            break;
          }
        }

        // Validaciones Combinando Campos
        // Validacion de Contenedores y Carga Suelta
        if (
          ($pArrayParametros['oDocC20'] == "SI" || $pArrayParametros['oDocC40'] == "SI") && 
          ($pArrayParametros['oDocCs'] == "SI" || $pArrayParametros['oDocCsu'] == "SI" || $pArrayParametros['oDocCag'] == "SI")
        ) {
          $nSwitch = 1;
          $cMsj  = "No puede Aplicar Carga al Granel o Contenedores o Carga Suelta al Mismo Tiempo.";
          $mReturn[count($mReturn)] = $cMsj;
        }        
      break;
      case "EXPORTACION":
        // Validaciones
        // Matriz con los nombres de los radio y su descripcion
        // Este metodo retorna una matriz con las siguientes posiciones
        // $mDatos[nombreCampoSys00121]['camcones'] => Nombre radio button
        // $mDatos[nombreCampoSys00121]['camcone2'] => Nombre en el formulario del campo de texto principal (opcional)
        // $mDatos[nombreCampoSys00121]['camcone3'] => Nombre en el formulario del campo de texto adicional (opcional)
        // $mDatos[nombreCampoSys00121]['camcamp3'] => Nombre en la sys00121 del campo de texto adicional (opcional, obligatorio si se asigna camcone3)
        // $mDatos[nombreCampoSys00121]['descones'] => Descripcion por defecto de la condicion especial
        // $mDatos[nombreCampoSys00121]['moscanho'] => Variable para indicar si mostrar o no el bloque de cantidad de horas de reconocimiento
        // $mDatos[nombreCampoSys00121]['accionjs'] => Condicon en el metodo f_Prende_Check para realizar accione
        foreach ($mTabId as $cKey => $cValue) {
          switch ($cKey) {
            case 'docarxxx':
            case 'docaflex':
              // Valores Vacios
              if ($pArrayParametros[$mTabId[$cKey]['camcones']] == "SI") {
                if ($pArrayParametros[$mTabId[$cKey]['camcone2']] == "" ) {
                  $nSwitch = 1;
                  $cMsj  = "La Condicion Especial [".$mTabId[$cKey]['descones']."] No Puede Ser Vacia.";
                  $mReturn[count($mReturn)] = $cMsj;
                }
              } elseif ($pArrayParametros[$mTabId[$cKey]['camcones']] == "NO") {
                $pArrayParametros[$mTabId[$cKey]['camcone2']] = "NO";
              }
            break;
            default:
              // Valores Numericos o solo radio
              if ($pArrayParametros[$mTabId[$cKey]['camcones']] == "SI") {
                // Validando campo principal si aplica
                if ($mTabId[$cKey]['camcone2'] != "") {
                  // Variable para indicar si mostrar o no el bloque de cantidad de horas de reconocimiento y si se debe valiar
                  if ($mTabId[$cKey]['moscanho'] != "NO") {
                    if ($pArrayParametros[$mTabId[$cKey]['camcone2']] == "" ) {
                      $nSwitch = 1;
                      $cMsj  = "La Condicion Especial [".$mTabId[$cKey]['descones']."] No Puede Ser Vacia.";
                      $mReturn[count($mReturn)] = $cMsj;
                    }
                  }
                  if (!preg_match("/^(\d)?(\d)*\.?\d*$/", $pArrayParametros[$mTabId[$cKey]['camcone2']])) {
                    $nSwitch = 1;
                    $cMsj  = "La Condicion Especial [".$mTabId[$cKey]['descones']."] Debe ser Numerica y el Separador de Decimales debe ser un Punto (.).";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
                // Validando campo adicional si aplica
                if ($mTabId[$cKey]['camcone3'] != "") {
                  // Solo se valida si es diferente de vacio
                  if ($pArrayParametros[$mTabId[$cKey]['camcone3']] != "" ) {
                    if (!preg_match("/^(\d)?(\d)*\.?\d*$/", $pArrayParametros[$mTabId[$cKey]['camcone3']])) {
                      $nSwitch = 1;
                      $cMsj  = "La Condicion Especial [".$mTabId[$cKey]['descones']."] (Valor Adicional) Debe ser Numerica y el Separador de Decimales debe ser un Punto (.).";
                      $mReturn[count($mReturn)] = $cMsj;
                    }
                  }                  
                }
              } elseif ($pArrayParametros[$mTabId[$cKey]['camcones']] == "NO") {
                if ($mTabId[$cKey]['camcone2'] != "") {
                  $pArrayParametros[$mTabId[$cKey]['camcone2']] = "NO";
                }
                if ($mTabId[$cKey]['camcone3'] != "") {
                  $pArrayParametros[$mTabId[$cKey]['camcone3']] = "NO";
                }
              }
            break;
          }
        }

        // Validacion de Contenedores y Carga Suelta
        if (
          ($pArrayParametros['oDocC20'] == "SI" || $pArrayParametros['oDocC40'] == "SI") && 
          ($pArrayParametros['oDocCs'] == "SI" || $pArrayParametros['oDocCsu'] == "SI" || $pArrayParametros['oDocCag'] == "SI")
        ) {
          $nSwitch = 1;
          $cMsj = "No puede Aplicar Carga al Granel o Contenedores o Carga Suelta al Mismo Tiempo.";
          $mReturn[count($mReturn)] = $cMsj;
        }
      break;
      case "TRANSITO":
        // Validaciones
        // Matriz con los nombres de los radio y su descripcion
        // Este metodo retorna una matriz con las siguientes posiciones
        // $mDatos[nombreCampoSys00121]['camcones'] => Nombre radio button
        // $mDatos[nombreCampoSys00121]['camcone2'] => Nombre en el formulario del campo de texto principal (opcional)
        // $mDatos[nombreCampoSys00121]['camcone3'] => Nombre en el formulario del campo de texto adicional (opcional)
        // $mDatos[nombreCampoSys00121]['camcamp3'] => Nombre en la sys00121 del campo de texto adicional (opcional, obligatorio si se asigna camcone3)
        // $mDatos[nombreCampoSys00121]['descones'] => Descripcion por defecto de la condicion especial
        // $mDatos[nombreCampoSys00121]['moscanho'] => Variable para indicar si mostrar o no el bloque de cantidad de horas de reconocimiento
        // $mDatos[nombreCampoSys00121]['accionjs'] => Condicon en el metodo f_Prende_Check para realizar accione
        foreach ($mTabId as $cKey => $cValue) {
          switch ($cKey) {
            case 'docafdmx':
              // Formularios DAV magneticas
              if ($pArrayParametros[$mTabId[$cKey]['camcones']] == "SI") {
                if (!preg_match("/^(\d)?(\d)*\.?\d*$/", $pArrayParametros[$mTabId[$cKey]['camcone2']])) {
                  $nSwitch = 1;
                  $cMsj  = "La Condicion Especial [".$mTabId[$cKey]['descones']."] No Puede Ser Menor o Igual a Cero.";
                  $mReturn[count($mReturn)] = $cMsj;
                } else {
                  if ($nValidarDo == "SI") {
                    $qDAVMag  = "SELECT COUNT(DOIIDXXX) AS davmag ";
                    $qDAVMag .= "FROM $cAlfa.SIAI0207 ";
                    $qDAVMag .= "WHERE ";
                    $qDAVMag .= "CLIIDXXX = \"{$xRDD['cliidxxx']}\" AND ";
                    $qDAVMag .= "DOIIDXXX = \"{$xRDD['docidxxx']}\" AND ";
                    $qDAVMag .= "DOISFIDX = \"{$xRDD['docsufxx']}\" AND ";
                    $qDAVMag .= "ADMIDXXX = \"{$xRDD['sucidxxx']}\" AND ";
                    $qDAVMag .= "DOIGDAVM = \"1\" AND "; // Medio magnetico igual a 1
                    $qDAVMag .= "DVANOFOR != \"\" AND "; // Consecutivo de la decalracion diferente a 1
                    $qDAVMag .= "REGESTXX = \"ACTIVO\" ";
                    $qDAVMag .= "GROUP BY DOIIDXXX,DOISFIDX,ADMIDXXX ";
                    $qDAVMag .= "ORDER BY DOIIDXXX,DOISFIDX,ADMIDXXX LIMIT 0,1";
                    $xDAVMag  = f_MySql("SELECT","",$qDAVMag,$xConexion01,"");
                    //f_Mensaje(__FILE__,__LINE__,$qDAVMag." - ".mysql_num_rows($xDAVMag));
                    $vDAVMag  = mysql_fetch_assoc($xDAVMag);

                    if ($pArrayParametros[$mTabId[$cKey]['camcone2']] > $vDAVMag['davmag']) {
                      $nSwitch = 1;
                      $cMsj  = "La Condicion Especial [".$mTabId[$cKey]['descones']."] No Puede Ser Mayor a las DAV Magneticas Generadas por el Sistema [{$vDAVMag['davmag']}].";
                      $mReturn[count($mReturn)] = $cMsj;
                    }
                  }
                }
              } elseif ($pArrayParametros[$mTabId[$cKey]['camcones']] == "NO") {
                $pArrayParametros[$mTabId[$cKey]['camcone2']] = "NO";
              }
            break;
            default:
              // Valores Numericos o solo radio
              if ($pArrayParametros[$mTabId[$cKey]['camcones']] == "SI") {
                // Validando campo principal si aplica
                if ($mTabId[$cKey]['camcone2'] != "") {
                  // Variable para indicar si mostrar o no el bloque de cantidad de horas de reconocimiento y si se debe valiar
                  if ($mTabId[$cKey]['moscanho'] != "NO") {
                    if ($pArrayParametros[$mTabId[$cKey]['camcone2']] == "" ) {
                      $nSwitch = 1;
                      $cMsj  = "La Condicion Especial [".$mTabId[$cKey]['descones']."] No Puede Ser Vacia.";
                      $mReturn[count($mReturn)] = $cMsj;
                    }
                  }
                  if (!preg_match("/^(\d)?(\d)*\.?\d*$/", $pArrayParametros[$mTabId[$cKey]['camcone2']])) {
                    $nSwitch = 1;
                    $cMsj  = "La Condicion Especial [".$mTabId[$cKey]['descones']."] Debe ser Numerica y el Separador de Decimales debe ser un Punto (.).";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
                // Validando campo adicional si aplica
                if ($mTabId[$cKey]['camcone3'] != "") {
                  // Solo se valida si es diferente de vacio
                  if ($pArrayParametros[$mTabId[$cKey]['camcone3']] != "" ) {
                    if (!preg_match("/^(\d)?(\d)*\.?\d*$/", $pArrayParametros[$mTabId[$cKey]['camcone3']])) {
                      $nSwitch = 1;
                      $cMsj  = "La Condicion Especial [".$mTabId[$cKey]['descones']."] (Valor Adicional) Debe ser Numerica y el Separador de Decimales debe ser un Punto (.).";
                      $mReturn[count($mReturn)] = $cMsj;
                    }
                  }                  
                }
              } elseif ($pArrayParametros[$mTabId[$cKey]['camcones']] == "NO") {
                if ($mTabId[$cKey]['camcone2'] != "") {
                  $pArrayParametros[$mTabId[$cKey]['camcone2']] = "NO";
                }
                if ($mTabId[$cKey]['camcone3'] != "") {
                  $pArrayParametros[$mTabId[$cKey]['camcone3']] = "NO";
                }
              }
            break;
          }
        }

        // Validaciones Combinando Campos
        // Validacion de Contenedores y Carga Suelta
        if (
          ($pArrayParametros['oDocC20'] == "SI" || $pArrayParametros['oDocC40'] == "SI") && 
          ($pArrayParametros['oDocCs'] == "SI" || $pArrayParametros['oDocCsu'] == "SI" || $pArrayParametros['oDocCag'] == "SI")
        ) {
          $nSwitch = 1;
          $cMsj  = "No puede Aplicar Carga al Granel o Contenedores o Carga Suelta al Mismo Tiempo.";
          $mReturn[count($mReturn)] = $cMsj;
        }
      break;
      case "OTROS":
        // Validaciones
        // Matriz con los nombres de los radio y su descripcion
        // Este metodo retorna una matriz con las siguientes posiciones
        // $mDatos[nombreCampoSys00121]['camcones'] => Nombre radio button
        // $mDatos[nombreCampoSys00121]['camcone2'] => Nombre en el formulario del campo de texto principal (opcional)
        // $mDatos[nombreCampoSys00121]['camcone3'] => Nombre en el formulario del campo de texto adicional (opcional)
        // $mDatos[nombreCampoSys00121]['camcamp3'] => Nombre en la sys00121 del campo de texto adicional (opcional, obligatorio si se asigna camcone3)
        // $mDatos[nombreCampoSys00121]['descones'] => Descripcion por defecto de la condicion especial
        // $mDatos[nombreCampoSys00121]['moscanho'] => Variable para indicar si mostrar o no el bloque de cantidad de horas de reconocimiento
        // $mDatos[nombreCampoSys00121]['accionjs'] => Condicon en el metodo f_Prende_Check para realizar accione
        foreach ($mTabId as $cKey => $cValue) {
          switch ($cKey) {
            case '':
              //No hace nada
              //Se deja case vacio para indicar que si a futuro uno de los campos necesita
              //validaciones especiales pueden incluirse en este case
            break;
            default:
              // Valores Numericos o solo radio
              if ($pArrayParametros[$mTabId[$cKey]['camcones']] == "SI") {
                // Validando campo principal si aplica
                if ($mTabId[$cKey]['camcone2'] != "") {
                  // Variable para indicar si mostrar o no el bloque de cantidad de horas de reconocimiento y si se debe valiar
                  if ($mTabId[$cKey]['moscanho'] != "NO") {
                    if ($pArrayParametros[$mTabId[$cKey]['camcone2']] == "" ) {
                      $nSwitch = 1;
                      $cMsj  = "La Condicion Especial [".$mTabId[$cKey]['descones']."] No Puede Ser Vacia.";
                      $mReturn[count($mReturn)] = $cMsj;
                    }
                  }
                  if (!preg_match("/^(\d)?(\d)*\.?\d*$/", $pArrayParametros[$mTabId[$cKey]['camcone2']])) {
                    $nSwitch = 1;
                    $cMsj  = "La Condicion Especial [".$mTabId[$cKey]['descones']."] Debe ser Numerica y el Separador de Decimales debe ser un Punto (.).";
                    $mReturn[count($mReturn)] = $cMsj;
                  }
                }
                // Validando campo adicional si aplica
                if ($mTabId[$cKey]['camcone3'] != "") {
                  // Solo se valida si es diferente de vacio
                  if ($pArrayParametros[$mTabId[$cKey]['camcone3']] != "" ) {
                    if (!preg_match("/^(\d)?(\d)*\.?\d*$/", $pArrayParametros[$mTabId[$cKey]['camcone3']])) {
                      $nSwitch = 1;
                      $cMsj  = "La Condicion Especial [".$mTabId[$cKey]['descones']."] (Valor Adicional) Debe ser Numerica y el Separador de Decimales debe ser un Punto (.).";
                      $mReturn[count($mReturn)] = $cMsj;
                    }
                  }                  
                }
              } elseif ($pArrayParametros[$mTabId[$cKey]['camcones']] == "NO") {
                if ($mTabId[$cKey]['camcone2'] != "") {
                  $pArrayParametros[$mTabId[$cKey]['camcone2']] = "NO";
                }
                if ($mTabId[$cKey]['camcone3'] != "") {
                  $pArrayParametros[$mTabId[$cKey]['camcone3']] = "NO";
                }
              }
            break;
          }
        }
      break;
      default:
        $nSwitch = 1;
        $mReturn[count($mReturn)] = "El Tipo de Operacion [{$xRDD['doctipxx']}] No es Valido.";
      break;
    }

    if ($pArrayParametros['cCcAplFa'] == "SI") {
      if ($pArrayParametros['cTerIdInt'] == "") {
        $nSwitch = 1;
        $cMsj  = "El Cliente tiene parametrizada en su Condicion Comercial la opcion \"Aplicar tarifas del Facturar a\", por favor seleccione el Facturar a.";
        $mReturn[count($mReturn)] = $cMsj;
      } else {
        //Validando que el facturar a sea valido
        $qFacA  = "SELECT ";
        $qFacA .= "CLIIDXXX ";
        $qFacA .= "FROM $cAlfa.SIAI0150 ";
        $qFacA .= "WHERE ";
        $qFacA .= "CLIIDXXX = \"{$pArrayParametros['cTerIdInt']}\" AND ";
        $qFacA .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
        $xFacA  = f_MySql("SELECT","",$qFacA,$xConexion01,"");
        if (mysql_num_rows($xFacA) == 0) {
          $nSwitch = 1;
          $cMsj  = "El Facturar a No Exite o Se Encuentra Inactivo.";
          $mReturn[count($mReturn)] = $cMsj;
        }
      }
    } else {
      $pArrayParametros['cTerIdInt'] = "";
    }

    if ($nValidarDo == "SI") {
      if ($cAlfa == "ALMACAFE" || $cAlfa == "DEALMACAFE" || $cAlfa == "TEALMACAFE") {
        // Correos para enviar las Notificación
        $qUsuNotF  = "SELECT ";
        $qUsuNotF .= "usremaxx ";
        $qUsuNotF .= "FROM $cAlfa.fpar0159 ";
        $qUsuNotF .= "WHERE ";
        $qUsuNotF .= "cliidxxx = \"{$xRDD['cliidxxx']}\" AND ";
        $qUsuNotF .= "sucidxxx = \"{$xRDD['sucidxxx']}\" AND ";
        $qUsuNotF .= "regestxx = \"ACTIVO\" ";
        $xUsuNotF  = f_MySql("SELECT","",$qUsuNotF,$xConexion01,"");
        //f_Mensaje(__FILE__,__LINE__,$qUsuNotF."~".mysql_num_rows($xUsuNotF));
        if (mysql_num_rows($xUsuNotF) == 0) {
          $nSwitch = 1;
          $cMsj  = "No Se Encontraron Correos Parametrizados para Notificar las Condicones Especiales.";
          $mReturn[count($mReturn)] = $cMsj;
        }
      }
    }

    //Eliminando las condiciones especiales y los datos del DO
    unset($pArrayParametros['conespxx']);
    unset($pArrayParametros['datosdox']);

    if ($nSwitch == 0) {
      $mReturn[0] = "true";
      $mReturn[1] = $pArrayParametros;
    } else {
      $mReturn[0] = "false";
    }
    return $mReturn;
  }

  /**
   * Metodo para Guardar las condiciones especiales
   */
  function fnGuardarCondicones($pArrayParametros) {

    global $xConexion01; global $cAlfa; global $vSysStr; global $kUser;

    /**
     * Recibe como Parametro una Matriz con las siguientes posiciones:
     * 
     * $pArrayParametros //Array con todos los valores del post deben llegar de la siguiente manera
     * $pArrayParametros[Nombre radio button]
     * $pArrayParametros[Nombre en el formulario del campo de texto principal (opcional)]
     * $pArrayParametros[Nombre en el formulario del campo de texto adicional (opcional)]
     * $pArrayParametros[conespxx]  //Array con las condiciones especiales que aplican para el DO
     * $pArrayParametros[datosdox]  //Array con los datos del DO
     * $pArrayParametros[cTerIdInt] //Facturar a
     */

    /**
     * Variable para hacer el retorno.
     * El listado de errores se retorna a partir de la posicion 2
     * @var array
     */
    $mReturn    = array();
    $mReturn[0] = ""; //true o false
    $mReturn[1] = array(); //advertencias

    /**
     * Variable para saber si hay o no errores de validacion.
     * @var number
     */
    $nSwitch = 0;

    //Campos condiciones especiales
    $mTabId = $pArrayParametros['conespxx'];
    //Datos DO
    $xRDD   = $pArrayParametros['datosdox'];

    // Si la variable de sistema financiero_permitir_condiciones_especiales_parciales es SI, 
    // agrego solo los campos diligenciados con SI o NO al array para el SQL.
    // Si ES diferente de SI se incluyen todos los campos
    $qUpdate = array();
    foreach($mTabId as $cKey => $cValue) {
      $nIncluir = 0;
      if ($vSysStr['financiero_permitir_condiciones_especiales_parciales'] == "SI") {
        // Si no selecciono SI o NO, no se guarda esa condicion especial
        if ($pArrayParametros[$mTabId[$cKey]['camcones']] == "") {
          $nIncluir = 1;
        }
      }

      if($nIncluir == 0) {
        if ($mTabId[$cKey]['camcone2'] != "") {
          array_push($qUpdate, array('NAME'=>$cKey,'VALUE'=>$pArrayParametros[$mTabId[$cKey]['camcone2']]	,'CHECK'=>'NO'));
        } else  {
          array_push($qUpdate, array('NAME'=>$cKey,'VALUE'=>$pArrayParametros[$mTabId[$cKey]['camcones']]	,'CHECK'=>'NO'));
        }
        //Si hay campo adicional se guarda
        if ($mTabId[$cKey]['camcone3'] != "") {
          array_push($qUpdate, array('NAME'=>$mTabId[$cKey]['camcamp3'],'VALUE'=>$pArrayParametros[$mTabId[$cKey]['camcone3']]	,'CHECK'=>'NO'));
        }
      }
    }
    array_push($qUpdate,array('NAME'=>'docusrce','VALUE'=>$kUser                        ,'CHECK'=>'NO'),
                        array('NAME'=>'docfecce','VALUE'=>date('Y-m-d H:i:s')           ,'CHECK'=>'NO'),
                        array('NAME'=>'docfacoe','VALUE'=>$pArrayParametros['cTerIdInt'],'CHECK'=>'NO'),
                        array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')                 ,'CHECK'=>'SI'),
                        array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')                 ,'CHECK'=>'SI'),
                        array('NAME'=>'sucidxxx','VALUE'=>$xRDD['sucidxxx']             ,'CHECK'=>'WH'),
                        array('NAME'=>'docidxxx','VALUE'=>$xRDD['docidxxx']             ,'CHECK'=>'WH'),                                
                        array('NAME'=>'docsufxx','VALUE'=>$xRDD['docsufxx']             ,'CHECK'=>'WH'));
    if (!f_MySql("UPDATE","sys00121",$qUpdate,$xConexion01,$cAlfa)) {
      $nSwitch = 1;
      $cMsj  = "Error al Actualizar el Registro en la Tabla sys00121, Verifique.";
      $mReturn[count($mReturn)] = $cMsj;
    }
  
    if($nSwitch == 0){
      switch ($xRDD['doctipxx']) {
        case "IMPORTACION":
          // Logica Especial para ALMACAFE          
          if ($cAlfa == "ALMACAFE" || $cAlfa == "DEALMACAFE" || $cAlfa == "TEALMACAFE") {
            $qFecDo  = "SELECT ";
            $qFecDo .= "DOIFENCA,";
            $qFecDo .= "DOIHENCA ";
            $qFecDo .= "FROM $cAlfa.SIAI0200 ";
            $qFecDo .= "WHERE ";
            $qFecDo .= "DOIIDXXX = \"{$xRDD['docidxxx']}\" AND ";
            $qFecDo .= "DOISFIDX = \"{$xRDD['docsufxx']}\" AND ";
            $qFecDo .= "ADMIDXXX = \"{$xRDD['sucidxxx']}\" AND ";
            $qFecDo .= "DOIFENCA != \"0000-00-00\" AND ";
            $qFecDo .= "DOIHENCA != \"00:00:00\" LIMIT 0,1 ";
            $xFecDo  = f_MySql("SELECT","",$qFecDo,$xConexion01,"");
            //f_Mensaje(__FILE__,__LINE__,$xRDD['cDosTip_DOS']."~".$qFecDo."~".mysql_num_rows($xFecDo));
            if (mysql_num_rows($xFecDo) == 0) {
              $mUpd200 = array( array('NAME'=>'DOIFENCA','VALUE'=>date("Y-m-d")   	,'CHECK'=>'SI'),
                                array('NAME'=>'DOIHENCA','VALUE'=>date("H:i:s")     ,'CHECK'=>'SI'),
                                array('NAME'=>'DOIIDXXX','VALUE'=>$xRDD['docidxxx'] ,'CHECK'=>'WH'),
                                array('NAME'=>'DOISFIDX','VALUE'=>$xRDD['docsufxx'] ,'CHECK'=>'WH'),
                                array('NAME'=>'ADMIDXXX','VALUE'=>$xRDD['sucidxxx'] ,'CHECK'=>'WH'));
              if (!f_MySql("UPDATE","SIAI0200",$mUpd200,$xConexion01,$cAlfa)) {
                $cMsj = "El Sistema no Pudo Actualizar la Fecha de la Factura en el Modulo de Control Fechas.";
                $mReturn[1][] = $cMsj;
              }
            }
          }
        break;
        case "EXPORTACION":
          // Logica Especial para ALMACAFE 
          if ($cAlfa == "ALMACAFE" || $cAlfa == "DEALMACAFE" || $cAlfa == "TEALMACAFE") {
            $mUpd199 = array( array('NAME'=>'dexfenfa','VALUE'=>date("Y-m-d")        ,'CHECK'=>'SI'),
                              array('NAME'=>'dexidxxx','VALUE'=>$xRDD['docidxxx'] 		,'CHECK'=>'WH'),
                              array('NAME'=>'admidxxx','VALUE'=>$xRDD['sucidxxx'] 		,'CHECK'=>'WH'));
            if (!f_MySql("UPDATE","siae0199",$mUpd199,$xConexion01,$cAlfa)) {
              $cMsj = "El Sistema no Pudo Actualizar la Fecha de la Factura en el Modulo de Control Fechas.\n";
              $mReturn[1][] = $cMsj;
            }
					}
        break;
        case "TRANSITO":
          if ($pArrayParametros['oDocTarIm'] == "SI") {
            $mReturn[1][] = "Recuerde Parametrizar las Condiciones Especiales del DO en el modulo Operativo Impo, para las Tarifas de Importacion.\n";
          }

          $qFecDo  = "SELECT ";
          $qFecDo .= "DOIFENCA,";
          $qFecDo .= "DOIHENCA ";
          $qFecDo .= "FROM $cAlfa.SIAI0200 ";
          $qFecDo .= "WHERE ";
          $qFecDo .= "DOIIDXXX = \"{$xRDD['docidxxx']}\" AND ";
          $qFecDo .= "DOISFIDX = \"{$xRDD['docsufxx']}\" AND ";
          $qFecDo .= "ADMIDXXX = \"{$xRDD['sucidxxx']}\" AND ";
          $qFecDo .= "DOIFENCA != \"0000-00-00\" AND ";
          $qFecDo .= "DOIHENCA != \"00:00:00\" LIMIT 0,1 ";
          $xFecDo  = f_MySql("SELECT","",$qFecDo,$xConexion01,"");
          //f_Mensaje(__FILE__,__LINE__,$xRDD['cDosTip_DOS']."~".$qFecDo."~".mysql_num_rows($xFecDo));
          if (mysql_num_rows($xFecDo) == 0) {
            $mUpd200 = array( array('NAME'=>'DOIFENCA','VALUE'=>date("Y-m-d")   	,'CHECK'=>'SI'),
                              array('NAME'=>'DOIHENCA','VALUE'=>date("H:i:s")     ,'CHECK'=>'SI'),
                              array('NAME'=>'DOIIDXXX','VALUE'=>$xRDD['docidxxx'] ,'CHECK'=>'WH'),
                              array('NAME'=>'DOISFIDX','VALUE'=>$xRDD['docsufxx'] ,'CHECK'=>'WH'),
                              array('NAME'=>'ADMIDXXX','VALUE'=>$xRDD['sucidxxx'] ,'CHECK'=>'WH'));
            if (!f_MySql("UPDATE","SIAI0200",$mUpd200,$xConexion01,$cAlfa)) {
              f_Mensaje(__FILE__,__LINE__,"El Sistema no Pudo Actualizar la Fecha de la Factura en el Modulo de Control Fechas.");
            }
          }
        break;
        case "OTROS":
          //No hace nada
        break;
        default:
          //No hace nada
        break;
      }

      // Logica Especial para ALMACAFE          
      if ($cAlfa == "ALMACAFE" || $cAlfa == "DEALMACAFE" || $cAlfa == "TEALMACAFE") {
        $qCliImp  = "SELECT ";
        $qCliImp .= "IF(CLINOMXX != \"\",CLINOMXX,CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) AS CLINOMXX ";
        $qCliImp .= "FROM $cAlfa.SIAI0150 ";
        $qCliImp .= "WHERE ";
        $qCliImp .= "CLIIDXXX = \"{$xRDD['cliidxxx']}\" LIMIT 0,1 ";
        $xCliImp  = f_MySql("SELECT","",$qCliImp,$xConexion01,"");
        $vCliImp  = mysql_fetch_assoc($xCliImp);

        $vDatos['sucidxxx'] = $xRDD['sucidxxx'];
        $vDatos['docidxxx'] = $xRDD['docidxxx'];
        $vDatos['docsufxx'] = $xRDD['docsufxx'];
        $vDatos['doctipxx'] = $xRDD['doctipxx'];
        $vDatos['cliidxxx'] = $xRDD['cliidxxx'];
        $vDatos['clinomxx'] = $vCliImp['CLINOMXX'];
        $vDatos['origenxx'] = $xRDD['doctipxx'];
        $oCondicionesEspeciales = new cCondicionesEspeciales();
        $mReturnExcel = $oCondicionesEspeciales->fnGenerarExcelCondiciones($vDatos);
        if($mReturnExcel[0] == "false"){
          $nSwitch = 1;
          for($nR=2; $nR<count($mReturnExcel); $nR++) {
            $mReturn[1][] = $mReturnExcel[$nR];
          }
        } else {
          // Vector con el nombre del archivo retornado en la posicion [1] de la matriz $mReturnExcel
          $vArchivos[] = $mReturnExcel[1];
          $cAsunto  = "{$xRDD['sucidxxx']}-{$xRDD['docidxxx']}-{$xRDD['docsufxx']} ";
          $cAsunto .= "{$vCliImp['CLINOMXX']} (Nit.{$xRDD['cliidxxx']}-".f_Digito_Verificacion($xRDD['cliidxxx']).")";
          $cMsjCorreo .= "<p>Notificaci&oacute;n Entrega Carpeta Facturaci&oacute;n.</p>";

          $cCorreos = "";
          // Correos para enviar las Notificación
          $qUsuNotF  = "SELECT ";
          $qUsuNotF .= "usremaxx ";
          $qUsuNotF .= "FROM $cAlfa.fpar0159 ";
          $qUsuNotF .= "WHERE ";
          $qUsuNotF .= "cliidxxx = \"{$xRDD['cliidxxx']}\" AND ";
          $qUsuNotF .= "sucidxxx = \"{$xRDD['sucidxxx']}\" AND ";
          $qUsuNotF .= "regestxx = \"ACTIVO\" ";
          $xUsuNotF  = f_MySql("SELECT","",$qUsuNotF,$xConexion01,"");
          //f_Mensaje(__FILE__,__LINE__,$qUsuNotF."~".mysql_num_rows($xUsuNotF));
          while($xRUN = mysql_fetch_assoc($xUsuNotF)){
            if($xRUN['usremaxx'] != ""){
              $vUsrEma = explode(",",$xRUN['usremaxx']);
              for($nE=0; $nE<count($vUsrEma); $nE++) {
                // Correo del usuario que se usa para enviar la notificación
                if($vUsrEma[$nE] != ""){
                  $cCorreos .= $vUsrEma[$nE].", ";
                  $oCondicionesEspeciales->fnEnviarEmail(strtolower($vUsrEma[$nE]), $cAsunto, $cMsjCorreo, $vArchivos);
                }
              }
            }
          }
          if ($cCorreos != "") {
            $cMsj  = "Se Notifico Actualizacion de Condiciones Especiales a: ".substr($cCorreos, 0, -2);
            $mReturn[1][] = $cMsj;
          }
        }
      }
    }

    if ($nSwitch == 0) {
      $mReturn[0] = "true";
    } else {
      $mReturn[0] = "false";
    }
    return $mReturn;
  }

  /**
   * Metodo para generar el excel de condiciones especiales cargue masivo
   */
  function fnGenerarExcelInterfacesCondicionesEspeciales($pArrayParametros) {
    global $vSysStr; global $kUser; global $cSystemPath;

    /**
      * Recibe como parametro un vector que contiene las siguientes posiciones
      * $pArrayParametros['tramites'] // Tramites
      * $pArrayParametros['columnas'] // Columnas
      */

    /**
      * Variable para saber si hay o no errores de validacion.
      *
      * @var number
      */
    $nSwitch = 0;

    /**
      * Matriz para Retornar Valores
      */
    $mReturn = array();

    /**
      * Reservando Primera Posición para retorna true o false
      */
    $mReturn[0] = "";

    $cFile01 = "PLANTILLA_SUBIR_CONDICIONES_ESPECIALES_IMPORTACION_".$kUser."_".date('YmdHis').".xls";
    $cFileDownload = "$cSystemPath/opencomex/".$vSysStr['system_download_directory']."/".$cFile01;
    $cF01 = fopen($cFileDownload,"a");

    /**
     * Cantidad de Registros en el excel de resultados
     * @var Number
     */
    $nRegistros = 0;    
    if(count($pArrayParametros['tramites']) > 0){
      $nRegistros++;
      $cColor = ';background-color:#F2F2F2';
      $cData  = '<table border="1" cellspacing="0" cellpadding="0">';
      $cData .= '<tr>';
      $cData .= '<td align="center" style="width:100px'.$cColor.';">SUCURSAL</td>';
      $cData .= '<td align="center" style="width:150px'.$cColor.';">DO</td>';
      $cData .= '<td align="center" style="width:100px'.$cColor.';">SUFIJO</td>';
      $cData .= '<td align="center" style="width:150px'.$cColor.';">TIPO OPERACION</td>';
      $cData .= '<td align="center" style="width:100px'.$cColor.';">IMPORTADOR</td>';
      $cData .= '<td align="center" style="width:150px'.$cColor.';">RAZON SOCIAL</td>';
      $cData .= '<td align="center" style="width:150px'.$cColor.';">FACTURAR A</td>';
      $cData .= '<td align="center" style="width:150px'.$cColor.';">RAZON SOCIAL FACTURAR A</td>';
      foreach ($pArrayParametros['columnas'] as $cKey => $vDesc) {
        $cData .= '<td align="center" style="width:350px'.$cColor.';">'.'['.$cKey.'] '. trim(implode(", ", $vDesc)) .'</td>';
      }
      $cData .= '</tr>';
      fwrite($cF01,$cData);

      $cColorTD = ';background-color:#D6DFF7';
      for($nD=0;$nD<count($pArrayParametros['tramites']);$nD++) {
        $cData  = '<tr>';
        $cData .= '<td align="left" style="mso-number-format:\'\@\'">'.$pArrayParametros['tramites'][$nD]['sucidxxx'].'</td>';
        $cData .= '<td align="left" style="mso-number-format:\'\@\'">'.$pArrayParametros['tramites'][$nD]['docidxxx'].'</td>';
        $cData .= '<td align="left" style="mso-number-format:\'\@\'">'.$pArrayParametros['tramites'][$nD]['docsufxx'].'</td>';
        $cData .= '<td align="left" style="mso-number-format:\'\@\'">'.$pArrayParametros['tramites'][$nD]['doctipxx'].'</td>';
        $cData .= '<td align="left" style="mso-number-format:\'\@\'">'.$pArrayParametros['tramites'][$nD]['cliidxxx'].'</td>';
        $cData .= '<td align="left" style="mso-number-format:\'\@\'">'.$pArrayParametros['tramites'][$nD]['clinomxx'].'</td>';
        $cData .= '<td align="left" style="mso-number-format:\'\@\'">'.$pArrayParametros['tramites'][$nD]['teridint'].'</td>';
        $cData .= '<td align="left" style="mso-number-format:\'\@\'">'.$pArrayParametros['tramites'][$nD]['ternomin'].'</td>';
        foreach ($pArrayParametros['columnas'] as $cKey => $vDesc) {
          //Limpiando campos numericos con valor a cero
          $pArrayParametros['tramites'][$nD][$cKey] = ($pArrayParametros['tramites'][$nD][$cKey] == "0.00") ? "" : $pArrayParametros['tramites'][$nD][$cKey];
          //Tanto el valor como el color se muestran solo si la condicion especial aplica para el DO
          if ((in_array($cKey, $pArrayParametros['tramites'][$nD]['conespxx']) == true)) {
            $cData .= '<td align="left" style="mso-number-format:\'\@\''.$cColorTD.';">'.$pArrayParametros['tramites'][$nD][$cKey].'</td>';
          } else{
            $cData .= '<td align="left" style="mso-number-format:\'\@\';"></td>';
          }
        }
        $cData .= '</tr>';
        fwrite($cF01,$cData);
      }
      $cData = '</table><br>';
      fwrite($cF01,$cData);
    }
    fclose($cF01);

    if ($nRegistros == 0) {
      /**
       * No se encontro ningun registro para crear el excel
       */
      $cFile01 = "";
    }

    if($nSwitch == 0){
      $mReturn[0] = "true";
      $mReturn[1] = $cFile01;
    }else{
      $mReturn[0] = "false";
    }

    return $mReturn;
  }
} ## class cConEspFac { ##

class cEstructurasConEspFac {

  /**
   * Metodo que se encarga de Crear las Estructuras de las Tablas Temporales
   */
  function fnCrearEstructurasConEspFac($pArrayParametros, $cAlfa) {
    /**
     * Recibe como Parametro un vector con las siguientes posiciones:
     * $pArrayParametros['TIPOESTU] //TIPO DE ESTRUCTURA
     * $pArrayParametros['CAMCOESP] //CAMPOS CONDICIONES ESPECIALES
     * $pArrayParametros['TIPOCAMP] //TIPOS DE CAMPOS DE CONDICIONES ESPECIALES
     */

    /**
     * Variable para saber si hay o no errores de validacion.
     * @var number
     */
    $nSwitch = 0;

    /**
     * Matriz para Retornar Valores
     */
    $mReturn = array();

    /**
     * Reservando Primera Posición para retorna true o false
     * Reservando Segunda Posición para el nombre de la tabla
     */
    $mReturn[0] = "";
    $mReturn[1] = "";

    /**
     * Llamando Metodo que hace conexion
     */
    $mReturnConexionTM = $this->fnConectarDBConEspFac();

    if($mReturnConexionTM[0] == "true") {
      $xConexionTM = $mReturnConexionTM[1];
    }else{
      $nSwitch = 1;
      for($nR=1;$nR<count($mReturnConexionTM);$nR++) {
        $mReturn[count($mReturn)] = $mReturnConexionTM[$nR];
      }
    }

    /**
     * Random para Nombre de la Tabla
     */
    $cTabCar  = mt_rand(1000000000, 9999999999);

    switch($pArrayParametros['TIPOESTU']) {
      case "SUBIRCONDICIONESESPECIALES":
        $cTabla = "memscesp".$cTabCar;

        $cTabCar  = mt_rand();
        $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
        $qNewTab .= "lineaidx INT(11)       NOT NULL AUTO_INCREMENT,";  //autoincremental
        $qNewTab .= "sucidxxx VARCHAR(3)    NOT NULL,";
        $qNewTab .= "docidxxx VARCHAR(20)   NOT NULL,";
        $qNewTab .= "docsufxx VARCHAR(3)    NOT NULL,";
        $qNewTab .= "doctipxx VARCHAR(12)   NOT NULL,"; 
        $qNewTab .= "cliidxxx VARCHAR(12)   NOT NULL,"; 
        $qNewTab .= "clinomxx VARCHAR(255)  NOT NULL,";
        $qNewTab .= "teridint VARCHAR(12)   NOT NULL,"; 
        $qNewTab .= "ternomin VARCHAR(255)  NOT NULL,";
        // Se crean campos de condiciones especiales
        $mTiposCampos = $pArrayParametros['TIPOCAMP'];
        for ($i=0; $i<count($pArrayParametros['CAMCOESP']); $i++) {
          $qNewTab .= "{$pArrayParametros['CAMCOESP'][$i]} {$mTiposCampos[$pArrayParametros['CAMCOESP'][$i]]} NOT NULL,";
        } 
        $qNewTab .= " PRIMARY KEY (lineaidx)) ENGINE=MyISAM "; //MyISAM
        $xNewTab = mysql_query($qNewTab,$xConexionTM);

        $vFieldsExcluidos = array();

        if(!$xNewTab) {
          $nSwitch = 1;
          $mReturn[count($mReturn)] = "Error al Crear Tabla Temporal. ".mysql_error($xConexionTM);
        }
      break;
      case "ERRORES":
      $cTabla = "memerror".$cTabCar;

      $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
      $qNewTab .= "lineaidx INT(11)     NOT NULL AUTO_INCREMENT, "; //LINEA
      $qNewTab .= "lineaerr VARCHAR(10) NOT NULL, ";                //LINEA DEL ARCHIVO
      $qNewTab .= "tipoerrx VARCHAR(20) NOT NULL, ";                //TIPO DE ERROR
      $qNewTab .= "deserror TEXT        NOT NULL, ";                //DESCRIPCION DEL ERROR
      $qNewTab .= "PRIMARY KEY (lineaidx)) ENGINE=MyISAM ";
      $xNewTab  = mysql_query($qNewTab,$xConexionTM);

      if(!$xNewTab) {
        $nSwitch = 1;
        $mReturn[count($mReturn)] = "Error al Crear Tabla Temporal de Errores, por Favor Informar a OpenTecnologia S.A. ".$qNewTab."~".mysql_error($xConexionTM);
      }
      break;
      default:
        $nSwitch = 1;
        $mReturn[count($mReturn)] = "No se Recibio Tipo de Estructura a Crear, por Favor Informar a OpenTecnologia S.A.";
      break;
    }

    if($nSwitch == 0) {
      $mReturn[0] = "true";
      $mReturn[1] = $cTabla;
    }else{
      $mReturn[0] = "false";
    }
    return $mReturn;
  }

  /**
   * Metodo que realiza la conexion
   */
  function fnConectarDBConEspFac() {
    /**
     * Variable para saber si hay o no errores de validacion.
     *
     * @var number
     */
    $nSwitch = 0;
    /**
     * Matriz para Retornar Valores
     */
    $mReturn = array();

    /**
     * Reservo Primera Posicion para retorna true o false
     */
    $mReturn[0] = "";

    $xConexion99 = mysql_connect(OC_SERVER,OC_USERROBOT,OC_PASSROBOT) or die("El Sistema no Logro Conexion con ".OC_SERVER);

    if($xConexion99) {
      $nSwitch = 0;
    }else{
      $nSwitch = 1;
      $mReturn[count($mReturn)] = "El Sistema no Logro Conexion con ".OC_SERVER;
    }

    if($nSwitch == 0) {
      $mReturn[0] = "true";
      $mReturn[1] = $xConexion99;
    } else{
      $mReturn[0] = "false";
    }
    return $mReturn;
  }##function fnConectarDB() {##

  /**
   * Metodo que realiza el reinicio de la conexion
   */
  function fnReiniciarConexionDBConEspFac($pConexion) {
    global $cHost;  global $cUserHost;  global $cPassHost;

    // echo "<br>Reconectando...";
    mysql_close($pConexion);
    if($cHost != "" && $cUserHost != "" && $cPassHost != "") {
      $xConexion01 = mysql_connect($cHost,$cUserHost,$cPassHost,TRUE);
    }else{
      $xConexion01 = mysql_connect(OC_SERVER,OC_USERROBOT,OC_PASSROBOT,TRUE);
    }
    return $xConexion01;
  }##function fnReiniciarConexionDBIBI() {##

  /**
   * Metodo que se encarga de Guardar los Errores Generados por los Metodos de Interfaces
   */
  function fnGuardarErrorConEspFac($pArrayParametros) {
    /**
     * Recibe como parametro un vector con los siguientes campos
     * $pArrayParametros['tablaerr']  //TABLA ERROR
     * $pArrayParametros['lineaerr']  //LINEA ERROR
     * $pArrayParametros['tipoerrx']  //TIPO DE ERROR
     * $pArrayParametros['deserror']  //DESCRIPCION DEL ERROR
     * $pArrayParametros['database']  //BASE DE DATOS
     * $pArrayParametros['conexion']  //CONEXION DB
     */

    /**
     * Variables para reemplazar caracteres especiales
     * @var array
     */
    $cBuscar = array('"',"'",chr(13),chr(10),chr(27),chr(9));
    $cReempl = array('\"',"\'"," "," "," "," ");

    if($pArrayParametros['tablaerr'] != "") {
      $qInsert  = "INSERT INTO {$pArrayParametros['database']}.{$pArrayParametros['tablaerr']} (lineaerr, tipoerrx, deserror) VALUES ";
      $qInsert .= "(\"{$pArrayParametros['lineaerr']}\",";
      $qInsert .= "\"{$pArrayParametros['tipoerrx'] }\",";
      $qInsert .= "\"".str_replace($cBuscar,$cReempl,$pArrayParametros['deserror'])."\")";
      mysql_query($qInsert,$pArrayParametros['conexion']);
      // echo "<br>".$qInsert;
    }
  }##function fnGuardarErrorIBI($pArrayParametros) {##

  ## Metodo para capturar la informacion del motor de DB asosciada al query
  function fnMysqlQueryInfo($xConexion,$xQueryTime) {

    global $cSystemPath; global $cAlfa; global $_SERVER; global $kDf;

    $xMysqlInfo = mysql_info($xConexion);

    ereg("Changed: ([0-9]*)",$xMysqlInfo,$vChanged);
    ereg("Deleted: ([0-9]*)",$xMysqlInfo,$vDeleted);
    ereg("Duplicates: ([0-9]*)",$xMysqlInfo,$vDuplicates);
    ereg("Records: ([0-9]*)",$xMysqlInfo,$vRecords);
    ereg("Rows matched: ([0-9]*)",$xMysqlInfo,$vRows_matched);
    ereg("Skipped: ([0-9]*)",$xMysqlInfo,$vSkipped);
    ereg("Warnings: ([0-9]*)",$xMysqlInfo,$vWarnings);

    $cQueryInfo  = "|";
    $cQueryInfo .= "Changed~{$vChanged[1]}|";
    $cQueryInfo .= "Deleted~{$vDeleted[1]}|";
    $cQueryInfo .= "Duplicates~{$vDuplicates[1]}|";
    $cQueryInfo .= "Records~{$vRecords[1]}|";
    $cQueryInfo .= "Rows matched~{$vRows_matched[1]}|";
    $cQueryInfo .= "Skipped~{$vSkipped[1]}|";
    $cQueryInfo .= "Warnings~{$vWarnings[1]}|";
    $cQueryInfo .= "Affected Rows~".mysql_affected_rows($xConexion)."|";
    $cQueryInfo .= "Query Time~".number_format($xQueryTime,2)."|";
    $cQueryInfo .= "Error Number~".mysql_errno($xConexion)."|";
    $cQueryInfo .= "Error Description~".mysql_error($xConexion)."|";

    $cIP = "";
    $cHost = "";
    if ($_SERVER['HTTP_CLIENT_IP'] != "") {
      $cIP   = $_SERVER['HTTP_CLIENT_IP'];
      $cHost = $_SERVER['HTTP_VIA'];
    }elseif ($_SERVER['HTTP_X_FORWARDED_FOR'] != "") {
      $cIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
      $cHost = $_SERVER['HTTP_VIA'];
    }else{
      $cIP = $_SERVER['REMOTE_ADDR'];
      $cHost = $_SERVER['HTTP_VIA'];
    }

    if ($cHost == "") {
      $cHost = $cIP;
    }

    $copenComex  = "|";
    $copenComex .= "{$kDf[4]}~";
    $copenComex .= "{$_SERVER['PHP_SELF']}~";
    $copenComex .= "$cIP~";
    $copenComex .= "$cHost~";
    $copenComex .= "{$kDf[3]}~";
    $copenComex .= date("Y-m-d")."~";
    $copenComex .= date("H:i:s");
    $copenComex .= "|";
    $xopenComex = mysql_query("SET @opencomex = \"$copenComex\"",$xConexion);
    $xQueryInfo = mysql_query("SET @mysqlinfo = \"$cQueryInfo\"",$xConexion);
  } ## function f_Mysql_Query_Info($xConexion,$xQueryTime) {
    ## Metodo para capturar la informacion del motor de DB asosciada al query
}
?>
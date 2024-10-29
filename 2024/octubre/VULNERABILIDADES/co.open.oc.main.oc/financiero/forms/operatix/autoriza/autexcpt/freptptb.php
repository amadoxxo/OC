<?php
  namespace openComex;
/**
 * Script para cargar pagos a terceros de un DO parametrica Autorizacion Excluir Conceptos de Pagos a Terceros
 * @author Johana Arboleda Ramos <jarboleda@opentecnologia.com.co>
 * @version 001
 */
  include("../../../../libs/php/utility.php");
  include("../../../../libs/php/utiliqdo.php");

  $mExcluidos = array();

  $mTramSel = f_Explode_Array($_POST['cTramites'],"|","~");

  $nCanTra = 0; $nCanPcc = 0; $vComboMarcadosTramites = array();

  for ($i=0; $i<count($mTramSel); $i++) {
    if ($mTramSel[$i][0] != "") {
      $nCanTra++;

      $qTramite  = "SELECT * ";
      $qTramite .= "FROM $cAlfa.sys00121 ";
      $qTramite .= "WHERE ";
      $qTramite .= "sucidxxx  = \"{$mTramSel[$i][0]}\" AND ";
      $qTramite .= "docidxxx  = \"{$mTramSel[$i][1]}\" AND ";
      $qTramite .= "docsufxx  = \"{$mTramSel[$i][2]}\" AND ";
      $qTramite .= "regestxx  = \"ACTIVO\" LIMIT 0,1 ";
      $xTramite  = f_MySql("SELECT","",$qTramite,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qTramite." ~ ".mysql_num_rows($xTramite));
      if (mysql_num_rows($xTramite) == 0) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Do [{$mTramSel[$i][0]}-{$mTramSel[$i][1]}-{$mTramSel[$i][2]}] No Existe, o se encuentra en estado FACTURADO o INACTIVO.\n";
      } else {
        $vTramite = mysql_fetch_array($xTramite);

        if ($vTramite['doctexpt'] != "") {
          //Pagos Excluidos con anterioridad
          $mAux = f_Explode_Array($vTramite['doctexpt'],"|","~");

          for ($i=0; $i<count($mAux); $i++) {
            if ($mAux[$i][0] != "") {
              //Se quita del Id el valor del combo de enviar a, aplica cuando al momento de guardar la exclusion
              //la variable system_habilitar_liquidacion_do_facturacion estaba en SI
              $cId  = "{$mAux[$i][0]}~";
              $cId .= "{$mAux[$i][1]}~";
              $cId .= "{$mAux[$i][2]}~";
              $cId .= "{$mAux[$i][3]}~";
              $cId .= "{$mAux[$i][4]}~";
              $cId .= "{$mAux[$i][5]}~";
              $cId .= "{$mAux[$i][6]}~";
              $cId .= "{$mAux[$i][7]}~";
              $cId .= "{$mAux[$i][8]}~";
              $cId .= "{$mAux[$i][9]}";
              $mExcluidos[count($mExcluidos)] = $cId;

              //Valor actual del combo enviar a
              $vComboMarcadosTramites["$cId"] = $mAux[$i][10];
            }
          }
        }

        // Busco el detalle de la cuenta de creacion del DO.
        $qCuenta  = "SELECT * ";
        $qCuenta .= "FROM ";
        $qCuenta .= "$cAlfa.fpar0115 ";
        $qCuenta .= "WHERE ";
        $qCuenta .= "CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$vTramite['pucidxxx']}\" AND ";
        $qCuenta .= "regestxx = \"ACTIVO\" LIMIT 0,1";
        $xCuenta  = f_MySql("SELECT","",$qCuenta,$xConexion01,"");
        //f_Mensaje(__FILE__,__LINE__,$qCuenta." ~ ".mysql_num_rows($xCuenta));
        $vCuenta  = mysql_fetch_array($xCuenta);

        //Busco la Calidad del Tercero
        $cTerCal = "";
        $qDatExt  = "SELECT * ";
        $qDatExt .= "FROM $cAlfa.SIAI0150 ";
        $qDatExt .= "WHERE ";
        $qDatExt .= "CLIIDXXX = \"{$vTramite['cliidxxx']}\" AND ";
        $qDatExt .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
        $xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
        //f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));

        if (mysql_num_rows($xDatExt) > 0) {
          $vDatExt = mysql_fetch_array($xDatExt);
          if ($vDatExt['CLIRECOM']=="SI" && $vDatExt['CLIGCXXX']<>"SI") {
            $cTerCal = "COMUN";
          } else {
            $cTerCal = "CONTRIBUYENTE";
          }
          if ($vDatExt['CLIRESIM']=="SI") {
            $cTerCal = "SIMPLIFICADO";
          }
          if ($vDatExt['CLIGCXXX']=="SI") {
            $cTerCal = "CONTRIBUYENTE";
          }
          if ($vDatExt['CLINRPXX']=="SI") {
            $cTerCal = "NORESIDENTE";
          }
        }
        ##Fin Traigo Datos Adicionales del Do ##

        ##Trayendo conceptos de pago a terceros que aplican para ese DO##
        $nInd_mTramites = count($mTramites);
        $mTramites[$i]['sucidxxx'] = $vTramite['sucidxxx'];
        $mTramites[$i]['docidxxx'] = $vTramite['docidxxx'];
        $mTramites[$i]['docsufxx'] = $vTramite['docsufxx'];
        $mTramites[$i]['tipocobx'] = "TODO"; //Se asume que se van a facturar PCC y IP
        $mTramites[$i]['facturax'] = "";
        $mTramites[$i]['pucidxxx'] = $vTramite['pucidxxx'];
        $mTramites[$i]['pucdetxx'] = $vCuenta['pucdetxx'];
        $mTramites[$i]['puctretx'] = $vCuenta['puctretx'];
        $mTramites[$i]['imporest'] = "SI";     // Importa el estado (SI/NO)
        $mTramites[$i]['regestxx'] = "ACTIVO"; // Cual estado "ACTIVO"
        $mTramites[$i]['comidxxx'] = "";
        $mTramites[$i]['comcodxx'] = "";
        $mTramites[$i]['comidxxf'] = "";  // Id del Documento de Facturacion
        $mTramites[$i]['comcodxf'] = "";  // Codigo del Documento de Facturacion
        $mTramites[$i]['comcscxf'] = "";  // Consecutivo del Documento de Facturacion
        $mTramites[$i]['clicteri'] = $cTerCal; // Update Facturacion en Dolares - Calidad del Tercero Intermediario "Facturar a"
        $mTramites[$i]['tcatasax'] = f_Buscar_Tasa_Cambio(date('Y-m-d'),"USD"); // Update Facturacion en Dolares - Tasa de Cambio Seleccionada en la Factura
      }
    }
  } ## for ($i=0; $i<$_POST['nSecuencia']; $i++) { ##

  if ($nCanTra == 0) {
    $nSwitch = 1;
    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
    $cMsj .= "Debe Seleccionar un Tramite en el Paso 1.\n";
  }

  if ($nSwitch == 0) { ?>

    <script language="javascript">
      parent.fmwork.document.getElementById("Grid_Pcc").innerHTML   = "";
      parent.fmwork.document.forms['frgrm']['nSecuencia_Pcc'].value = 0;
    </script>
    <?php

    //Valido que si hay conceptos de ejeucion local o ambas, no se permite conceptos de solo ejecucion NIIF
    //Recorro la grilla para determinar el tipo de ejecucion del comprobante
    //Si Los concepto son todos de ejecucion LOCAL se suman solos los valores de ejecucion LOCAL
    //Si Los concepto son todos de ejecucion NIFF se suman solos los valores de ejecucion NIFF
    //Si Los concepto son de ejecucion LOCAL y/o ejecucion NIFF y/o ejecucion AMBAS
    //se suman solos los valores de ejecucion LOCAL, porque en este caso los valores NIIF son solo informativos
    $nCanLocal   = 0; //Cantidad de Conceptos que son de ejecucion Local
    $nCanNiff    = 0; //Cantidad de Conceptos que son de ejecucion Niif
    $nCanAmbas   = 0; //Cantidad de Conceptos que son de Ambas

    $nCanLocalAnt   = 0; //Cantidad de Conceptos que son de ejecucion Local Anticipos
    $nCanNiffAnt    = 0; //Cantidad de Conceptos que son de ejecucion Niif Anticipos
    $nCanAmbasAnt   = 0; //Cantidad de Conceptos que son de Ambas Anticipos

    $mCauxDO = array(); // Matriz para causaciones por DO.
    $mFormsxTipo = array(); // Matriz para agrupar los formularios por tipo.

    //Matriz para verificar si ya habia sido marcado
    $vCheckMarcados = array();
    $mAuxMarcados   = array();
    $mAuxMarcados   = f_Explode_Array($_POST['cComMemo'],"|","~");

    //Se quita del Id el valor del combo de enviar a, aplica cuando al momento de guardar la exclusion
    //la variable system_habilitar_liquidacion_do_facturacion estaba en SI
    for ($nA=0; $nA<count($mAuxMarcados);$nA++) {
      if($vAuxMarcados[$nA][0] != "") {
        $cId  = "{$vAuxMarcados[$nA][0]}~";
        $cId .= "{$vAuxMarcados[$nA][1]}~";
        $cId .= "{$vAuxMarcados[$nA][2]}~";
        $cId .= "{$vAuxMarcados[$nA][3]}~";
        $cId .= "{$vAuxMarcados[$nA][4]}~";
        $cId .= "{$vAuxMarcados[$nA][5]}~";
        $cId .= "{$vAuxMarcados[$nA][6]}~";
        $cId .= "{$vAuxMarcados[$nA][7]}~";
        $cId .= "{$vAuxMarcados[$nA][8]}~";
        $cId .= "{$vAuxMarcados[$nA][9]}";
        $vCheckMarcados[count($vCheckMarcados)] = $cId;

        //Valor actual del combo enviar a
        $vComboMarcados["$cId"] = $vAuxMarcados[$nA][10];
      }
    }

    $mPCCA = f_Liquida_PCCA_Tramites($mTramites,date('Y-m-d'),"NO");

    // Facturacion Automatica sin importar el tipo de operacion de cada tramite facturado
    for ($j=0;$j<count($mPCCA);$j++) {
      // Primero Traigo el Gravamen Arancelario de los DO's Seleccionados.
      if ($mPCCA[$j]['mostrarx'] == 1 && $mPCCA[$j]['tipopcca'] == "TRIBUTOS") {

        // Busco el nombre del tercero de la carta bancaria
        if ($mPCCA[$j]['terid2xx'] != "") {
          $qProId  = "SELECT *,CLIIDXXX AS TERIDXXX,CONCAT(CLINOMXX,\" \",CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X) AS TERNOMXX ";
          $qProId .= "FROM $cAlfa.SIAI0150 ";
          $qProId .= "WHERE ";
          $qProId .= "CLIIDXXX = \"{$mPCCA[$j]['terid2xx']}\" AND ";
          $qProId .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
          $xProId  = f_MySql("SELECT","",$qProId,$xConexion01,"");
          //f_Mensaje(__FILE__,__LINE__,$qProId." ~ ".mysql_num_rows($xProId));
          if (mysql_num_rows($xProId) > 0) {
            $vProId = mysql_fetch_array($xProId);
          } else {
            $vProId = array(); $vProId['TERNOMXX'] = "TERCERO SIN NOMBRE UNO";
          }
        } else {
          $vProId = array(); $vProId['TERNOMXX'] = "TERCERO SIN NOMBRE DOS";
        }

        // Cargo la matriz con los pagos por cuenta del cliente agrupados por TERCERO-COMPROBANTE.
        $mPCCA[$j]['ctodesxx'] .= " ^ ".trim(trim($vProId['TERNOMXX']))." ^ ".trim($vProId['TERIDXXX']);
        // Fin de Busco el nombre del tercero de la carta bancaria

        $cId  = "{$mPCCA[$j]['ctoidxxx']}~";
        $cId .= "{$mPCCA[$j]['pucidxxx']}~";
        $cId .= "{$mPCCA[$j]['tipopcca']}~";
        $cId .= "{$mPCCA[$j]['comidxxx']}~";
        $cId .= "{$mPCCA[$j]['comcodxx']}~";
        $cId .= "{$mPCCA[$j]['comcscxx']}~";
        $cId .= "{$mPCCA[$j]['comseqxx']}~";
        $cId .= "{$mPCCA[$j]['sucidxxx']}~";
        $cId .= "{$mPCCA[$j]['docidxxx']}~";
        $cId .= "{$mPCCA[$j]['docsufxx']}";

        $cValue  = "{$mPCCA[$j]['ctoidxxx']}~";
        $cValue .= "{$mPCCA[$j]['pucidxxx']}~";
        $cValue .= "{$mPCCA[$j]['tipopcca']}~";
        $cValue .= "{$mPCCA[$j]['comidxxx']}~";
        $cValue .= "{$mPCCA[$j]['comcodxx']}~";
        $cValue .= "{$mPCCA[$j]['comcscxx']}~";
        $cValue .= "{$mPCCA[$j]['sucidxxx']}~";
				$cValue .= "{$mPCCA[$j]['docidxxx']}~";
        $cValue .= "{$mPCCA[$j]['docsufxx']}";

        if (count($vCheckMarcados) > 0) {
          $cExcluido  = (in_array($cId, $vCheckMarcados) == true) ? true : false;
          $cComboSele =  ($vComboMarcados["$cId"] != "") ? $vComboMarcados["$cId"] : "NOAPLICA";
        } else {
          $cExcluido = (in_array($cId, $mExcluidos) == false)    ? false : true;
          $cComboSele =  ($vComboMarcadosTramites["$cId"] != "") ? $vComboMarcadosTramites["$cId"] : "NOAPLICA";
        }
        $nCanPcc++;
        ?>
        <script languaje = "javascript">
          parent.fmwork.f_Add_New_Row_Pcc("<?php echo $cId ?>","<?php echo $cValue ?>","<?php echo count($mPCCA) ?>","<?php echo $cExcluido ?>");

          parent.fmwork.document.forms['frgrm']['cCtoId'    + parent.fmwork.document.forms['frgrm']['nSecuencia_Pcc'].value].value = "<?php echo $mPCCA[$j]['ctoidfac']; ?>";
          parent.fmwork.document.forms['frgrm']['cTramite'  + parent.fmwork.document.forms['frgrm']['nSecuencia_Pcc'].value].value = "<?php echo "{$mPCCA[$j]['sucidxxx']}-{$mPCCA[$j]['docidxxx']}-{$mPCCA[$j]['docsufxx']}"; ?>";
          parent.fmwork.document.forms['frgrm']['cServicio' + parent.fmwork.document.forms['frgrm']['nSecuencia_Pcc'].value].value = "<?php echo trim($mPCCA[$j]['ctodesxx']); ?>";
          parent.fmwork.document.forms['frgrm']['cDocFuente'+ parent.fmwork.document.forms['frgrm']['nSecuencia_Pcc'].value].value = "<?php echo trim($mPCCA[$j]['comidxxx'])."-".trim($mPCCA[$j]['comcodxx'])."-".trim($mPCCA[$j]['comcscxx'])."-".trim($mPCCA[$j]['comseqxx']) ?>";
          parent.fmwork.document.forms['frgrm']['cDocInf'   + parent.fmwork.document.forms['frgrm']['nSecuencia_Pcc'].value].value = "<?php echo trim($mPCCA[$j]['comdocin']) ?>";
          parent.fmwork.document.forms['frgrm']['cComVlr'   + parent.fmwork.document.forms['frgrm']['nSecuencia_Pcc'].value].value = "<?php echo (($mPCCA[$j]['puctipej'] == "L" || $mPCCA[$j]['puctipej'] == "") ? $mPCCA[$j]['comlocal'] : 0) ?>";
          if ("<?php echo $vSysStr['system_habilitar_liquidacion_do_facturacion'] ?>" == "SI") {
            parent.fmwork.document.forms['frgrm']['cEnviarA'  + parent.fmwork.document.forms['frgrm']['nSecuencia_Pcc'].value].value = "<?php echo $cComboSele ?>";
          }
          parent.fmwork.document.forms['frgrm']['cComMov'   + parent.fmwork.document.forms['frgrm']['nSecuencia_Pcc'].value].value = "<?php echo $mPCCA[$j]['commovxx'] ?>";
        </script>
      <?php
      }
      // Fin de Primero Traigo el Gravamen Arancelario de los DO's Seleccionados.
    }

    // Cargo los pagos por cuenta del cliente x TERCERO.

    for ($j=0;$j<count($mPCCA);$j++) {
      // Acumulo causaciones x DO.

      if ($mPCCA[$j]['mostrarx'] == 1 && ($mPCCA[$j]['tipopcca'] == "PCCA" || $mPCCA[$j]['tipopcca'] == "CAJA_MENOR")) {

        if ($mPCCA[$j]['terid2xx'] != "") {
          $qProId  = "SELECT *,CLIIDXXX AS TERIDXXX,CONCAT(CLINOMXX,\" \",CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X) AS TERNOMXX ";
          $qProId .= "FROM $cAlfa.SIAI0150 ";
          $qProId .= "WHERE ";
          $qProId .= "CLIIDXXX = \"{$mPCCA[$j]['terid2xx']}\" AND ";
          $qProId .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
          $xProId  = f_MySql("SELECT","",$qProId,$xConexion01,"");
          //f_Mensaje(__FILE__,__LINE__,$qProId." ~ ".mysql_num_rows($xProId));
          if (mysql_num_rows($xProId) > 0) {
            $vProId = mysql_fetch_array($xProId);
          } else {
            $vProId = array(); $vProId['TERNOMXX'] = "TERCERO SIN NOMBRE UNO";
          }
        } else {
          $vProId = array(); $vProId['TERNOMXX'] = "TERCERO SIN NOMBRE DOS";
        }

        // Cargo la matriz con los pagos por cuenta del cliente agrupados por TERCERO-COMPROBANTE.
        $mPCCA[$j]['ctodesxx'] .= " ^ ".trim(trim($vProId['TERNOMXX']))." ^ ".trim($vProId['TERIDXXX']);

        $cId  = "{$mPCCA[$j]['ctoidxxx']}~";
        $cId .= "{$mPCCA[$j]['pucidxxx']}~";
        $cId .= "{$mPCCA[$j]['tipopcca']}~";
        $cId .= "{$mPCCA[$j]['comidxxx']}~";
        $cId .= "{$mPCCA[$j]['comcodxx']}~";
        $cId .= "{$mPCCA[$j]['comcscxx']}~";
        $cId .= "{$mPCCA[$j]['comseqxx']}~";
        $cId .= "{$mPCCA[$j]['sucidxxx']}~";
        $cId .= "{$mPCCA[$j]['docidxxx']}~";
        $cId .= "{$mPCCA[$j]['docsufxx']}";

        if (count($vCheckMarcados) > 0) {
          $cExcluido  = (in_array($cId, $vCheckMarcados) == true) ? true : false;
          $cComboSele =  ($vComboMarcados["$cId"] != "") ? $vComboMarcados["$cId"] : "NOAPLICA";
        } else {
          $cExcluido = (in_array($cId, $mExcluidos) == false)    ? false : true;
          $cComboSele =  ($vComboMarcadosTramites["$cId"] != "") ? $vComboMarcadosTramites["$cId"] : "NOAPLICA";
        }
        $nCanPcc++;

        ?>
        <script languaje = "javascript">
          parent.fmwork.f_Add_New_Row_Pcc("<?php echo $cId ?>","<?php echo $cId ?>","<?php echo count($mPCCA) ?>","<?php echo $cExcluido ?>","<?php $mAuxExc[10] ?>");

          parent.fmwork.document.forms['frgrm']['cCtoId'    + parent.fmwork.document.forms['frgrm']['nSecuencia_Pcc'].value].value = "<?php echo $mPCCA[$j]['ctoidfac']; ?>";
          parent.fmwork.document.forms['frgrm']['cTramite'  + parent.fmwork.document.forms['frgrm']['nSecuencia_Pcc'].value].value = "<?php echo "{$mPCCA[$j]['sucidxxx']}-{$mPCCA[$j]['docidxxx']}-{$mPCCA[$j]['docsufxx']}"; ?>";
          parent.fmwork.document.forms['frgrm']['cServicio' + parent.fmwork.document.forms['frgrm']['nSecuencia_Pcc'].value].value = "<?php echo trim($mPCCA[$j]['ctodesxx']); ?>";
          parent.fmwork.document.forms['frgrm']['cDocFuente'+ parent.fmwork.document.forms['frgrm']['nSecuencia_Pcc'].value].value = "<?php echo trim($mPCCA[$j]['comidxxx'])."-".trim($mPCCA[$j]['comcodxx'])."-".trim($mPCCA[$j]['comcscxx'])."-".trim($mPCCA[$j]['comseqxx']) ?>";
          parent.fmwork.document.forms['frgrm']['cDocInf'   + parent.fmwork.document.forms['frgrm']['nSecuencia_Pcc'].value].value = "<?php echo trim($mPCCA[$j]['comdocin']) ?>";
          parent.fmwork.document.forms['frgrm']['cComVlr'   + parent.fmwork.document.forms['frgrm']['nSecuencia_Pcc'].value].value = "<?php echo (($mPCCA[$j]['puctipej'] == "L" || $mPCCA[$j]['puctipej'] == "") ? $mPCCA[$j]['comlocal'] : 0) ?>";
          if ("<?php echo $vSysStr['system_habilitar_liquidacion_do_facturacion'] ?>" == "SI") {
            parent.fmwork.document.forms['frgrm']['cEnviarA'  + parent.fmwork.document.forms['frgrm']['nSecuencia_Pcc'].value].value = "<?php echo $cComboSele ?>";
          }
          parent.fmwork.document.forms['frgrm']['cComMov'   + parent.fmwork.document.forms['frgrm']['nSecuencia_Pcc'].value].value = "<?php echo $mPCCA[$j]['commovxx'] ?>";
        </script>
      <?php
      }
    }
    // Fin de cargo los pagos por cuenta del cliente x TERCERO.
    // Fin de Facturacion Automatica sin importar el tipo de operacion de cada tramite facturado
  }

  if ($nSwitch == 0) { ?>
    <script language="javascript">
      parent.fmwork.document.forms['frgrm']['nRecords'].value  = "<?php echo $nCanPcc ?>";
    </script>
  <?php
  } else {
    f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique"); ?>
    <script language="javascript">
      parent.fmwork.document.forms['frgrm']['cStep'].value     = "1";
      parent.fmwork.document.forms['frgrm']['cStep_Ant'].value = "2";

      parent.fmwork.document.forms['frgrm'].target = "fmwork";
      parent.fmwork.document.forms['frgrm'].action = "freptmas.php";
      parent.fmwork.document.forms['frgrm'].submit();
    </script>
  <?php }

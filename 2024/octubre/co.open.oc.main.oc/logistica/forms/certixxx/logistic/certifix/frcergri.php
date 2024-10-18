<?php
  namespace openComex;
  /**
   * Grillas de Servicios en Certificacion.
   * --- Descripcion: Permite cargar las grillas en el formulario de Certificacion.
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
   * @package opencomex
   * @version 001
   */

  include("../../../../../financiero/libs/php/utility.php");
  include("../../../../libs/php/utimifxx.php");

  $vTipos = array();
  //esta enviando un solo caso
  if (isset($gTipo)) {
    $vTipos[0] = $gTipo;
  }

  //Esta enviando varios casos
  if (isset($gParametro)) {
    $mAux = f_Explode_Array($gParametro,"|","^");
    for($i=0; $i<count($mAux);$i++) {
      if ($mAux[$i][0] != "") {
        switch ($mAux[$i][0]) {
          case "1";
            $vTipos[] = $mAux[$i][0];
            $gMifId   = $mAux[$i][1];
            $gAnio    = $mAux[$i][2];
          break;
          default:
            //No hace nada
          break;
        }
      }
    }
  }

  //Recorriendo todos los casos
  for ($nT=0; $nT<count($vTipos);$nT++) {
    switch ($vTipos[$nT]) {
      case "1": //SUBSERVICIOS
        $mData = array();
        $cTexto = "";

        $cConsecutivo = "";
        if ($_COOKIE['kModo'] != "NUEVO") {
          $qCertifiCab  = "SELECT ";
          $qCertifiCab .= "$cAlfa.lcca$gAnio.comidxxx, ";
          $qCertifiCab .= "$cAlfa.lcca$gAnio.comcodxx, ";
          $qCertifiCab .= "$cAlfa.lcca$gAnio.comprexx, ";
          $qCertifiCab .= "$cAlfa.lcca$gAnio.comcscxx, ";
          $qCertifiCab .= "$cAlfa.lcca$gAnio.comcsc2x ";
          $qCertifiCab .= "FROM $cAlfa.lcca$gAnio ";
          $qCertifiCab .= "WHERE ";
          $qCertifiCab .= "$cAlfa.lcca$gAnio.ceridxxx = \"$gCerId\" LIMIT 0,1 ";
          $xCertifiCab  = f_MySql("SELECT","",$qCertifiCab,$xConexion01,"");
          if (mysql_num_rows($xCertifiCab) > 0) {
            $vCertifiCab = mysql_fetch_array($xCertifiCab);
            $cConsecutivo = $vCertifiCab['comidxxx']."-".$vCertifiCab['comprexx']."-".$vCertifiCab['comcscxx'];
          }
        }

        // Se consultan los subservicios de la MIF
        $qMifSubservi  = "SELECT ";
        $qMifSubservi .= "lmsu$gAnio.mifidxxx, ";
        $qMifSubservi .= "lmsu$gAnio.sersapxx, ";
        $qMifSubservi .= "lmsu$gAnio.subidxxx, ";
        $qMifSubservi .= "lpar0011.serdesxx, ";
        $qMifSubservi .= "lpar0012.subdesxx ";
        $qMifSubservi .= "FROM $cAlfa.lmsu$gAnio ";
        $qMifSubservi .= "LEFT JOIN $cAlfa.lpar0011 ON $cAlfa.lmsu$gAnio.sersapxx = $cAlfa.lpar0011.sersapxx ";
        $qMifSubservi .= "LEFT JOIN $cAlfa.lpar0012 ON $cAlfa.lmsu$gAnio.sersapxx = $cAlfa.lpar0012.sersapxx AND $cAlfa.lmsu$gAnio.subidxxx = $cAlfa.lpar0012.subidxxx ";
        $qMifSubservi .= "WHERE ";
        $qMifSubservi .= "lmsu$gAnio.mifidxxx = \"$gMifId\" ";
        if ($_COOKIE['kModo'] == "NUEVO") {
          $qMifSubservi .= "AND lmsu$gAnio.regestxx = \"ACTIVO\" ";
        } else {
          // Al momento de ver o editar se deben mostrar solo los subservicios activos y/o los asociados a la certificacion
          $qMifSubservi .= "AND (lmsu$gAnio.regestxx = \"ACTIVO\" OR (lmsu$gAnio.regestxx = \"CERTIFICADO\" AND lmsu$gAnio.cercscxx = \"$cConsecutivo\")) ";
        } 
        $qMifSubservi .= "GROUP BY lmsu$gAnio.subidxxx";
        $xMifSubservi  = f_MySql("SELECT","",$qMifSubservi,$xConexion01,"");
        // echo $qMifSubservi . " ~ " . mysql_num_rows($xMifSubservi);

        if (mysql_num_rows($xMifSubservi) > 0) {
          $cTexto = "<table border = \"0\" cellpadding = \"0\" cellspacing = \"0\" width=\"940\">";
          $nCol = f_Format_Cols(47); echo $nCol;
            $cTexto .= "<tr>";
              $cTexto .= "<td colspan=\"47\" class= \"clase08\" align=\"right\">";
              $cTexto .= "</td>";
            $cTexto .= "</tr>";
              $cTexto .= "<tr>";
              $cTexto .= "<td class = \"clase08\" style = \"width:020;\" align=\"center\">Sec.</td>";
              $cTexto .= "<td class = \"clase08\" style = \"width:340;\" align=\"center\">Servicio</td>"; 
              $cTexto .= "<td class = \"clase08\" style = \"width:340;\" align=\"center\">Subservicio</td>";
              $cTexto .= "<td class = \"clase08\" style = \"width:200;\" align=\"center\">Unidad Facturable</td>";
              $cTexto .= "<td class = \"clase08\" style = \"width:040;\" align=\"center\">&nbsp;</td>";
            $cTexto .= "</tr>";
          $cTexto .= "</table>";

          $cTexto .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"940\">";

          $nSecuencia = 0;
          while ($xRMS = mysql_fetch_array($xMifSubservi)) {
            $nSecuencia++;

            $mData[$nSecuencia]['sersapxx'] = $xRMS['sersapxx'];
            $mData[$nSecuencia]['subidxxx'] = $xRMS['subidxxx'];

            // Consulta la condicion de servicio para obtener la unidad facturable y el objeto facturable
            $qCondiServ  = "SELECT ";
            $qCondiServ .= "lpar0152.cseidxxx, ";
            $qCondiServ .= "lpar0152.sersapxx, ";
            $qCondiServ .= "lpar0152.ufaidxxx, ";
            $qCondiServ .= "lpar0006.ufadesxx, ";
            $qCondiServ .= "lpar0152.obfidxxx, ";
            $qCondiServ .= "lpar0004.obfdesxx ";
            $qCondiServ .= "FROM $cAlfa.lpar0152 ";
            $qCondiServ .= "LEFT JOIN $cAlfa.lpar0006 ON $cAlfa.lpar0152.ufaidxxx = $cAlfa.lpar0006.ufaidxxx ";
            $qCondiServ .= "LEFT JOIN $cAlfa.lpar0004 ON $cAlfa.lpar0152.obfidxxx = $cAlfa.lpar0004.obfidxxx ";
            $qCondiServ .= "WHERE ";
            $qCondiServ .= "lpar0152.cliidxxx = \"$gCliId\" AND ";
            $qCondiServ .= "lpar0152.ccoidocx = \"$gCcoIdOc\" AND ";
            $qCondiServ .= "lpar0152.sersapxx = \"{$xRMS['sersapxx']}\" AND ";
            $qCondiServ .= "lpar0152.regestxx = \"ACTIVO\"";
            $xCondiServ  = f_MySql("SELECT","",$qCondiServ,$xConexion01,"");
            // echo "<br>";
            // echo $qCondiServ . " ~ " . mysql_num_rows($xCondiServ);

            $cObfId  = "";
            $cOfdDes = "";
            $cUfaId  = "";
            $cUfaDes = "";
            if (mysql_num_rows($xCondiServ) > 0) {
              while ($xRCS = mysql_fetch_array($xCondiServ)) {
                // Consulto las condiciones de servicio relacionada al subservicio que esta guardado en la MIF
                $qCondSerSubser  = "SELECT ";
                $qCondSerSubser .= "lpar0153.sersapxx, ";
                $qCondSerSubser .= "lpar0153.subidxxx, ";
                $qCondSerSubser .= "lpar0012.subdesxx ";
                $qCondSerSubser .= "FROM $cAlfa.lpar0153 ";
                $qCondSerSubser .= "LEFT JOIN $cAlfa.lpar0012 ON $cAlfa.lpar0153.sersapxx = $cAlfa.lpar0012.sersapxx AND $cAlfa.lpar0153.subidxxx = $cAlfa.lpar0012.subidxxx ";
                $qCondSerSubser .= "WHERE ";
                $qCondSerSubser .= "lpar0153.cseidxxx = \"{$xRCS['cseidxxx']}\" AND ";
                $qCondSerSubser .= "lpar0153.sersapxx = \"{$xRCS['sersapxx']}\" AND ";
                $qCondSerSubser .= "lpar0153.subidxxx = \"{$xRMS['subidxxx']}\" AND ";
                $qCondSerSubser .= "lpar0153.regestxx = \"ACTIVO\"";
                $xCondSerSubser  = f_MySql("SELECT","",$qCondSerSubser,$xConexion01,"");
                if (mysql_num_rows($xCondSerSubser) > 0) {
                  $cObfId  = $xRCS['obfidxxx'];
                  $cOfdDes = $xRCS['obfdesxx'];
                  $cUfaId  = $xRCS['ufaidxxx'];
                  $cUfaDes = $xRCS['ufadesxx'];
                }
              }
            }

            $mRespuesta = fnBaseSubservicio($xRMS['mifidxxx'], $xRMS['sersapxx'], $xRMS['subidxxx'], $cObfId);

            // Si las cantidades del subservicio son mayores a Cero de pinta en la grilla de Servicios a Certificar
            if ($mRespuesta['nCantidad'] > 0) {
              $cTexto .= "<tr>";
                $cTexto .= "<td>";
                  $cTexto .= "<input type = \"hidden\" name = \"nTotSer\" value = \"".mysql_num_rows($xMifSubservi)."\">";
                  $cTexto .= "<input type = \"text\" class = \"letra\" style = \"width:020;border:1;text-align:center;padding:2.5px\" name = \"cSecuencia".$nSecuencia."\" id = \"cSecuencia".$nSecuencia."\" value = \"".$nSecuencia."\" readonly>";
                $cTexto .= "</td>";
                $cTexto .= "<td>";
                  $cTexto .= "<input type = \"hidden\" name = \"cSerSap".$nSecuencia."\" value = \"".$xRMS['sersapxx']."\" >";
                  $cTexto .= "<input type = \"text\" class = \"letra\" style = \"width:340;border:1;text-align:left;padding:2.5px\" name = \"cSerDes".$nSecuencia."\" value = \"".$xRMS['serdesxx']."\" readonly>";
                $cTexto .= "</td>";

                $cTexto .= "<td>";
                  $cTexto .= "<input type = \"hidden\" name = \"cSubId".$nSecuencia."\" value = \"".$xRMS['subidxxx']."\" >";
                  $cTexto .= "<input type = \"text\" class = \"letra\" style = \"width:340;border:1;text-align:left;padding:2.5px\" name = \"cSubDes".$nSecuencia."\" value = \"".$xRMS['subdesxx']."\" readonly>";
                $cTexto .= "</td>";

                $cTexto .= "<td>";
                  $cTexto .= "<input type = \"hidden\" name = \"cUfaId".$nSecuencia."\" value = \"".$cUfaId."\" >";
                  $cTexto .= "<input type = \"text\" class = \"letra\" style = \"width:200;border:1;text-align:left;padding:2.5px\" name = \"cUfaDes".$nSecuencia."\" value = \"".$cUfaDes."\" readonly>";
                
                  $cTexto .= "<input type = \"hidden\" name = \"cObfId".$nSecuencia."\" value = \"".$cObfId."\">";
                  $cTexto .= "<input type = \"hidden\" name = \"cObfDes".$nSecuencia."\" value = \"".$cOfdDes."\">";
                  $cTexto .= "<input type = \"hidden\" name = \"cBaseServ".$nSecuencia."\" value = \"".$mRespuesta['nBase']."\">";
                $cTexto .= "</td>";
                
                $cTexto .= "<td>";
                  $cTexto .= "<input type=\"checkbox\" name=\"cCheckSer".$nSecuencia."\" id=\"cCheckSer".$nSecuencia."\" style = \"width:040;border:1;text-align:center;padding:2.5px\" onchange= \"javascript:fnAgregarServicio(this.checked,".$nSecuencia.")\">";
                $cTexto .= "</td>";
              $cTexto .= "</tr>";
            }
          }
          $cTexto .= "</table>";
        }
        ?>  
        <script languaje = "javascript">
          parent.fmwork.document.getElementById('overDivSubServicios').innerHTML = '<?php echo $cTexto ?>';
          parent.fmwork.document.forms['frgrm']['nSecuencia_Servicio'].value     = '<?php echo $nSecuencia ?>';
        </script>
        <?php 
        if (count($mData) > 0 && ($_COOKIE['kModo'] == "EDITAR" || $_COOKIE['kModo'] == "VER") && $gValidaExisteSubservicio == "SI") {
          fnValidaExisteSubservicio($mData);
        }
      break;
      default:
        //No Hace Nada
      break;
    }
  }

  /**
   * Permite calcular la base del total de los subservicios.
   */
  function fnBaseSubservicio($xMifId, $xSerSap, $xSubId, $xObjeto) {
    global $gAnio; global $cAlfa; global $xConexion01;

    /**
     * Se instancia la clase de Matriz de Insumos Facturables
     */
    $ObjcMatrizInsumosFacturables = new cMatrizInsumosFacturables();

    $qMifSubservi  = "SELECT ";
    $qMifSubservi .= "lmsu$gAnio.mifidxxx, ";
    $qMifSubservi .= "IF(lmsu$gAnio.mifdcanx > 0, lmsu$gAnio.mifdcanx, \"\") AS mifdcanx ";
    $qMifSubservi .= "FROM $cAlfa.lmsu$gAnio ";
    $qMifSubservi .= "WHERE ";
    $qMifSubservi .= "lmsu$gAnio.mifidxxx = \"$xMifId\" AND ";
    $qMifSubservi .= "lmsu$gAnio.sersapxx = \"$xSerSap\" AND ";
    $qMifSubservi .= "lmsu$gAnio.subidxxx = \"$xSubId\" ";
    $xMifSubservi  = f_MySql("SELECT","",$qMifSubservi,$xConexion01,"");
    // echo $qMifSubservi;

    $mCantidades  = array();
    $nTotCantidad = 0;
    if(mysql_num_rows($xMifSubservi) > 0) {
      while($xRMS = mysql_fetch_array($xMifSubservi)) {
        $nInd_mCantidades = count($mCantidades);
        $mCantidades[$nInd_mCantidades] = $xRMS['mifdcanx'];
        $nTotCantidad += $xRMS['mifdcanx'];
      }
    }

    return [
      "nCantidad" => $nTotCantidad,
      "nBase"     => $ObjcMatrizInsumosFacturables->fnCalcularCantidadTotal($xObjeto, $mCantidades)
    ];
  }

  /**
   * Valida si existe un subservicio en el detalle de certificacion.
   * 
   * Esta funcion se utiliza cuando el kModo es EDITAR o VER para marcar los subservicios ACTIVOS
   */
  function fnValidaExisteSubservicio($mData) {
    global $xConexion01; global $cAlfa; global $gAnio; global $gCerId;

    $nCountExiste = 0;
    for ($i=1; $i <= count($mData); $i++) {
      $qCertifiDet  = "SELECT ";
      $qCertifiDet .= "$cAlfa.lcde$gAnio.*, ";
      $qCertifiDet .= "$cAlfa.lpar0004.obfidxxx, ";
      $qCertifiDet .= "$cAlfa.lpar0004.obfdesxx, ";
      $qCertifiDet .= "$cAlfa.lpar0006.ufaidxxx, ";
      $qCertifiDet .= "$cAlfa.lpar0006.ufadesxx, ";
      $qCertifiDet .= "$cAlfa.lpar0010.cebidxxx, ";
      $qCertifiDet .= "$cAlfa.lpar0010.cebcodxx, ";
      $qCertifiDet .= "$cAlfa.lpar0010.cebdesxx ";
      $qCertifiDet .= "FROM $cAlfa.lcde$gAnio ";
      $qCertifiDet .= "LEFT JOIN $cAlfa.lpar0006 ON $cAlfa.lcde$gAnio.ufaidxxx = $cAlfa.lpar0006.ufaidxxx ";
      $qCertifiDet .= "LEFT JOIN $cAlfa.lpar0004 ON $cAlfa.lcde$gAnio.obfidxxx = $cAlfa.lpar0004.obfidxxx ";
      $qCertifiDet .= "LEFT JOIN $cAlfa.lpar0010 ON $cAlfa.lcde$gAnio.cebidxxx = $cAlfa.lpar0010.cebidxxx ";
      $qCertifiDet .= "WHERE ";
      $qCertifiDet .= "$cAlfa.lcde$gAnio.ceridxxx = \"$gCerId\" AND ";
      $qCertifiDet .= "$cAlfa.lcde$gAnio.sersapxx = \"{$mData[$i]['sersapxx']}\" AND ";
      $qCertifiDet .= "$cAlfa.lcde$gAnio.subidxxx = \"{$mData[$i]['subidxxx']}\" AND ";
      $qCertifiDet .= "$cAlfa.lcde$gAnio.regestxx != \"INACTIVO\" LIMIT 0,1";
      $xCertifiDet  = f_MySql("SELECT","",$qCertifiDet,$xConexion01,"");
      // echo $qCertifiDet;

      // f_Mensaje(__FILE__,__LINE__,$qCertifiDet."~".mysql_num_rows($xCertifiDet));
      if (mysql_num_rows($xCertifiDet) > 0) {
        $nCountExiste++;
        $vCertifiDet = mysql_fetch_array($xCertifiDet);
        ?>
        <script languaje = "javascript">
          parent.fmwork.document.forms['frgrm']['cCheckSer'+'<?php echo $i ?>'].checked = true;
          parent.fmwork.fnAddNewRowCertificacion('Grid_Certificacion', '<?php echo $i; ?>', 'MIF');

          // Asigna los valores a la grilla de Certificacion
          parent.fmwork.document.forms['frgrm']['cDesMaterial'+'<?php echo $nCountExiste ?>'].value  = '<?php echo $vCertifiDet['subdesxx'] ?>';
          parent.fmwork.document.forms['frgrm']['cCerdId'+'<?php echo $nCountExiste ?>'].value       = '<?php echo $vCertifiDet['cerdidxx'] ?>';
          parent.fmwork.document.forms['frgrm']['cCodSapSer'+'<?php echo $nCountExiste ?>'].value    = '<?php echo $vCertifiDet['sersapxx'] ?>';
          parent.fmwork.document.forms['frgrm']['cSubId_Certi'+'<?php echo $nCountExiste ?>'].value  = '<?php echo $vCertifiDet['subidxxx'] ?>';
          parent.fmwork.document.forms['frgrm']['cObfId_Certi'+'<?php echo $nCountExiste ?>'].value  = '<?php echo $vCertifiDet['obfidxxx'] ?>';
          parent.fmwork.document.forms['frgrm']['cObfDes_Certi'+'<?php echo $nCountExiste ?>'].value = '<?php echo $vCertifiDet['obfdesxx'] ?>';
          parent.fmwork.document.forms['frgrm']['cUfaId_Certi'+'<?php echo $nCountExiste ?>'].value  = '<?php echo $vCertifiDet['ufaidxxx'] ?>';
          parent.fmwork.document.forms['frgrm']['cUfaDes_Certi'+'<?php echo $nCountExiste ?>'].value = '<?php echo $vCertifiDet['ufadesxx'] ?>';
          parent.fmwork.document.forms['frgrm']['cCebId'+'<?php echo $nCountExiste ?>'].value        = '<?php echo $vCertifiDet['cebidxxx'] ?>';
          parent.fmwork.document.forms['frgrm']['cCebCod'+'<?php echo $nCountExiste ?>'].value       = '<?php echo $vCertifiDet['cebcodxx'] ?>';
          parent.fmwork.document.forms['frgrm']['cCebDes'+'<?php echo $nCountExiste ?>'].value       = '<?php echo $vCertifiDet['cebdesxx'] ?>';
          parent.fmwork.document.forms['frgrm']['cBase'+'<?php echo $nCountExiste ?>'].value         = '<?php echo $vCertifiDet['basexxxx'] ?>';
          parent.fmwork.document.forms['frgrm']['cCondicion'+'<?php echo $nCountExiste ?>'].value    = '<?php echo $vCertifiDet['cerdconx'] ?>';
          parent.fmwork.document.forms['frgrm']['cStatus'+'<?php echo $nCountExiste ?>'].value       = '<?php echo $vCertifiDet['cerdestx'] ?>';
          parent.fmwork.document.forms['frgrm']['cTipoCerti'+'<?php echo $nCountExiste ?>'].value    = '<?php echo $vCertifiDet['cerdorix'] ?>';

          <?php if ($_COOKIE['kModo'] == "VER" || ($_COOKIE['kModo'] == "EDITAR" && $vCertifiDet['cerdestx'] == "AUTOMATICO")) { ?>
            parent.fmwork.fnActivarDesactivarCamposCertificacion('<?php echo $nCountExiste ?>', true);
          <?php } ?>
        </script>
        <?php
      }
      
      if ($_COOKIE['kModo'] == "VER") {
        ?>
        <script languaje = "javascript">
          parent.fmwork.document.forms['frgrm']['cCheckSer'+'<?php echo $i ?>'].disabled = true;
        </script>
        <?php
      }
    }
  }

<?php
  namespace openComex;
  /**
   * Grillas para el formulario de Condiciones de Servicio.
   * --- Descripcion: Permite cargar las grillas en el formulario.
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
      case "1": //SUBSERVICIO
        $cTexto = "";

        if ($_COOKIE['kModo'] == "NUEVO") {
          fnValidarDepsitoCliente();
        }

        $pArrayDatos = array();
        $pArrayDatos['cMifId']   = $gMifId;
        $pArrayDatos['cAnio']    = $gAnio;
        $pArrayDatos['cEstSubs'] = ($_COOKIE['kModo'] == "NUEVO" || $_COOKIE['kModo'] == "EDITAR" || $_COOKIE['kModo'] == "VER") ? "" : "ACTIVO";
        $pArrayDatos['cCcoIdOc'] = $gCcoIdOc;
        $pArrayDatos['cDepNum']  = $gDepNum;
        
        $mData = array();
        $mReturnSubservicios = $ObjcMatrizInsumosFacturables->fnCargarDataSubserviciosMIF($pArrayDatos);
        if ($mReturnSubservicios[0] == "true") {
          $mData = $mReturnSubservicios[1]['subservi'];
          $nSecuencia = 0;

          $cTexto = "<table border = \"0\" cellpadding = \"0\" cellspacing = \"0\" width=\"580\">";
          $nCol = f_Format_Cols(29); echo $nCol;
            $cTexto .= "<tr>";
              $cTexto .= "<td colspan=\"29\" class= \"clase08\" align=\"right\">";
              $cTexto .= "</td>";
            $cTexto .= "</tr>";
              $cTexto .= "<tr>";
              $cTexto .= "<td class = \"clase08\" style = \"width:040;\" align=\"left\">No.</td>";
              $cTexto .= "<td class = \"clase08\" style = \"width:210;\" align=\"left\">Subservicio</td>"; 
              $cTexto .= "<td class = \"clase08\" style = \"width:180;\" align=\"left\">Unidad Facturable</td>";
              $cTexto .= "<td class = \"clase08\" style = \"width:120;\" align=\"left\">Fecha</td>";
              if ($_COOKIE['kModo'] == "NUEVO") {
                $cTexto .= "<td class = \"clase08\" style = \"width:030;\" align=\"center\">";
                  $cTexto .= "<input type=\"checkbox\" name=\"cCheckTodos\" id=\"cCheckTodos\" style = \"margin-left:10.5px;border:1;text-align:center\" onchange= \"javascript:fnCheckTodos(this, ".count($mData).")\">";
                $cTexto .= "</td>";
              } else {
                $cTexto .= "<td class = \"clase08\" style = \"width:030;\" align=\"center\">&nbsp;</td>";
              }
            $cTexto .= "</tr>";
          $cTexto .= "</table>";

          $cSubservicios = "";
          $cTexto .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"580\">";
          for ($i=0; $i < count($mData); $i++) {
            $cSubservicios .= $mData[$i]['sersapxx']."~".$mData[$i]['subidxxx']."|";
            $nSecuencia++;
            $cTexto .= "<tr>";
              $cTexto .= "<td>";
                $cTexto .= "<input type = \"hidden\" name = \"nTotSubser\" value = \"".count($mData)."\">";
                $cTexto .= "<input type = \"text\" class = \"letra\" style = \"width:040;border:1;text-align:center;padding:2.5px\" name = \"cSecuencia".$nSecuencia."\" id = \"cSecuencia".$i."\" value = \"".str_pad($nSecuencia,3,"0",STR_PAD_LEFT)."\" readonly>";
              $cTexto .= "</td>";
              $cTexto .= "<td>";
                $cTexto .= "<input type = \"hidden\" name = \"cSerId".$i."\" value = \"".$mData[$i]['sersapxx']."\" >";
                $cTexto .= "<input type = \"hidden\" name = \"cSubId".$i."\" value = \"".$mData[$i]['subidxxx']."\" >";
                $cTexto .= "<input type = \"text\" class = \"letra\" style = \"width:210;border:1;text-align:left;padding:2.5px\" name = \"cSubDes".$i."\" value = \"".$mData[$i]['subdesxx']."\" readonly>";
              $cTexto .= "</td>";
              $cTexto .= "<td>";
                $cTexto .= "<input type = \"hidden\" name = \"cUfaId".$i."\" value = \"".$mData[$i]['ufaidxxx']."\" >";
                $cTexto .= "<input type = \"text\" class = \"letra\" style = \"width:180;border:1;text-align:left;padding:2.5px\" name = \"cUfaDes".$i."\" value = \"".$mData[$i]['ufadesxx']."\" readonly>";
              $cTexto .= "</td>";
              $cTexto .= "<td>";
                $cTexto .= "<input type = \"text\" class = \"letra\" style = \"width:120;border:1;text-align:center;padding:2.5px\" name = \"cMifdFec".$i."\" value = \"".$mData[$i]['mifdfecx']."\" readonly>";
              $cTexto .= "</td>";
              $cTexto .= "<td>";
                $cTexto .= "<input type=\"checkbox\" name=\"cCheckSub".$i."\" id=\"cCheckSub".$i."\" style = \"width:030;border:1;text-align:center;padding:2.5px\" onchange= \"javascript:fnCambiarCheck(this, ".count($mData).", ".$i.")\">";
              $cTexto .= "</td>";
            $cTexto .= "</tr>";
          }
          $cTexto .= "</table>";
        }
        ?>  
        <script languaje = "javascript">
          parent.fmwork.document.getElementById('overDivSubServicios').innerHTML = '<?php echo $cTexto ?>';

          var indice = parent.fmwork.document.forms['frgrm']['nIndexSubser'].value;
          if (indice != "") {
            parent.fmwork.document.forms['frgrm']['cCheckSub'+indice].checked = true;
          }

          if ('<?php echo $_COOKIE['kModo'] ?>' == "NUEVO") {
            parent.fmwork.document.forms['frgrm']['cSubserNoMarcados'].value = '<?php echo $cSubservicios ?>';
          }
        </script>
        <?php 
        if (count($mData) > 0 && ($_COOKIE['kModo'] == "EDITAR" || $_COOKIE['kModo'] == "VER")) {
          fnValidaExisteSubservicio($mData);
        }
      break;
      default:
        //No Hace Nada
      break;
    }
  }

  /**
   * Permite validar si ya existe un registro para el deposito y cliente seleccionado.
   * 
   * Esta validación solo aplica para el formulario de NUEVO
   * 
   */
  function fnValidarDepsitoCliente() {
    global $vSysStr; global $xConexion01; global $cAlfa; global $gCliId; global $gDepNum;

    /**
     * Variable para saber existe un documento.
     *
     * @var int
     */
    $nExiste = 0;

    $nAnioAnterior = (date('Y') - 1);
    $nAnioInicial  = $nAnioAnterior < $vSysStr['logistica_ano_instalacion_modulo'] ? date('Y') : $nAnioAnterior;
    for ($nAnio = $nAnioInicial; $nAnio <= date('Y'); $nAnio++) {
      // Valida que no exista una M.I.F para el Deposito y Cliente
      $qMifDat  = "SELECT ";
      $qMifDat .= "cliidxxx, depnumxx ";
      $qMifDat .= "FROM $cAlfa.lmca$nAnio ";
      $qMifDat .= "WHERE ";
      $qMifDat .= "$cAlfa.lmca$nAnio.cliidxxx = \"$gCliId\" AND ";
      $qMifDat .= "$cAlfa.lmca$nAnio.depnumxx = \"$gDepNum\" AND ";
      $qMifDat .= "$cAlfa.lmca$nAnio.regestxx IN (\"ENPROCESO\",\"ACTIVO\") LIMIT 0,1";
      $xMifDat  = f_MySql("SELECT","",$qMifDat,$xConexion01,"");
      if(mysql_num_rows($xMifDat) > 0){
        $nExiste = 1;
        break;
      }
    }

    if ($nExiste == 1) { ?>
      <script languaje = "javascript">
        if (!confirm("El sistema ya registra una M.I.F para el cliente [" + <?php echo $gCliId ?> + "] y deposito [" + <?php echo $gDepNum ?> + "]. Esta seguro que desea continuar?.")) {
          parent.fmwork.fnRetorna();
        }
      </script>
      <?php
    }
  }

  /**
   * Valida si existe un subservicio en estado ACTIVO y retorna la fecha mayor de todos los registros.
   * 
   * Esta funcion se utiliza cuando el kModo es EDITAR o VER para marcar los subservicios ACTIVOS
   */
  function fnValidaExisteSubservicio($mData) {
    global $xConexion01; global $cAlfa; global $gAnio; global $gMifId;

    for ($i=0; $i < count($mData); $i++) {
      $qMifSubservi  = "SELECT ";
      $qMifSubservi .= "mifdidxx, ";
      $qMifSubservi .= "mifdfecx, ";
      $qMifSubservi .= "IF(mifdcanx > 0, mifdcanx, \"\") AS mifdcanx ";
      $qMifSubservi .= "FROM $cAlfa.lmsu$gAnio ";
      $qMifSubservi .= "WHERE ";
      $qMifSubservi .= "mifidxxx = \"$gMifId\" AND ";
      $qMifSubservi .= "subidxxx = \"{$mData[$i]['subidxxx']}\" AND ";
      $qMifSubservi .= "regestxx IN (\"ACTIVO\",\"CERTIFICADO\") ";
      $qMifSubservi .= "ORDER BY lmsu$gAnio.mifdfecx ASC";
      $xMifSubservi  = f_MySql("SELECT","",$qMifSubservi,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qMifSubservi."~".mysql_num_rows($xMifSubservi));
      $dFechaInicial = "";
      if (mysql_num_rows($xMifSubservi) > 0) {
        while ($xRMS = mysql_fetch_array($xMifSubservi)) {
          // Se obtiene la fecha mayor solo si han digitado alguna cantidad
          if (strtotime($xRMS['mifdfecx']) > strtotime($dFechaInicial) && !empty($xRMS['mifdcanx'])) {
            $dFechaInicial = $xRMS['mifdfecx'];
          }
        }
        ?>

        <script languaje = "javascript">
          parent.fmwork.document.forms['frgrm']['cMifdFec'+'<?php echo $i ?>'].value = '<?php echo $dFechaInicial ?>';
          parent.fmwork.document.forms['frgrm']['cCheckSub'+'<?php echo $i ?>'].checked = true;
          parent.fmwork.fnCambiarCheck('', '<?php echo count($mData); ?>', '<?php echo $i; ?>');
        </script>
        <?php
      } 
      
      if ($_COOKIE['kModo'] == "VER") {
        ?>
        <script languaje = "javascript">
          parent.fmwork.document.forms['frgrm']['cCheckSub'+'<?php echo $i ?>'].disabled = true;
        </script>
        <?php
      }
    }
  }
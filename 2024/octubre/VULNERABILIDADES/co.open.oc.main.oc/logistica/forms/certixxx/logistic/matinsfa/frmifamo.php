<?php
  namespace openComex;
  /**
   * Adicionar Movimiento - Matriz de Insumos Facturables.
   * --- Descripcion: Permite Adicionar Movimiento a una Matriz de Insumos Facturables.
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
   * @package opencomex
   * @version 001
   */

  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");

  include("../../../../../financiero/libs/php/utility.php");
  include("../../../../libs/php/utimifxx.php");

  /**
   * Se instancia la clase de Matriz de Insumos Facturables
   */
  $ObjcMatrizInsumosFacturables = new cMatrizInsumosFacturables();

?>
<html>
  <head>
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
    <style type="text/css">
      .bntGuardar {
        width:91px;
        height:21px;
        border:0px;
        cursor:pointer;
        text-align:center;
        background:url(<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif);
      }
    </style>
    <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
    <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
    <script languaje = "javascript">
      function fnRetorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
      }

      function fnMostrarOcultarObjetos(xStep)	{
        // Oculto campos de valor FOB y cantidad de formularios que solo aplican para EXPORTACIONES y DTA's.
        switch (xStep) {
          case "1":
            document.getElementById("idPaso1").style.display="block";
            document.getElementById("idPaso2").style.display="none";
          break;
          case "2":
            document.getElementById("idPaso1").style.display="none";
            document.getElementById("idPaso2").style.display="block";
          break;
        }
      }

      function fnAsignarValores() {
        document.forms['frestado']['cStep'].value        = document.forms['frgrm']['cStep'].value;
        document.forms['frestado']['cStep_Ant'].value    = document.forms['frgrm']['cStep_Ant'].value;
        document.forms['frestado']['cMifId'].value       = document.forms['frgrm']['cMifId'].value;
        document.forms['frestado']['cAnio'].value        = document.forms['frgrm']['cAnio'].value;
        document.forms['frestado']['cMifOri'].value      = document.forms['frgrm']['cMifOri'].value;
        document.forms['frestado']['dDesde'].value       = document.forms['frgrm']['dDesde'].value;
        document.forms['frestado']['dHasta'].value       = document.forms['frgrm']['dHasta'].value;
        document.forms['frestado']['cSubservicio'].value = document.forms['frgrm']['cSubservicio'].value;
        document.forms['frestado']['cSubSerDes'].value   = document.forms['frgrm']['cSubSerDes'].value;
        document.forms['frestado']['nIndexSubser'].value = document.forms['frgrm']['nIndexSubser'].value;
      }

      function fnCargarGrillaSubServicio() {
        var cParametro = "1^"+document.forms['frgrm']['cMifId'].value + "^" +
                             +document.forms['frgrm']['cAnio'].value;

        var cRuta = "frmifgri.php?gCcoIdOc="+document.forms['frgrm']['cCcoIdOc'].value +
                                 "&gDepNum="+document.forms['frgrm']['cDepNum'].value +
                                 "&gParametro="+cParametro;
        parent.fmpro.location = cRuta;
      }

      function fnCambiarCheck(value, xCantidad, xIndice) {
        for (i=0;i<xCantidad;i++) {
          document.forms['frgrm']['cCheckSub'+i].checked = false;
        }

        document.forms['frgrm'][value.name].checked   = true;
        document.forms['frgrm']['cSubservicio'].value = document.forms['frgrm']['cSubId'+xIndice].value;
        document.forms['frgrm']['cSubSerDes'].value   = document.forms['frgrm']['cSubDes'+xIndice].value;
        document.forms['frgrm']['nIndexSubser'].value = xIndice;
      }

    </script>
  </head>
  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
    <center>
      <table border="0" cellpadding="0" cellspacing="0" width="620">
        <tr>
          <td>
            <!-- Estos campos son los que se envian al graba y vista previa -->
            <form name = "frestado" action = "frmifgra.php" method = "post" target="fmpro">
              <input type = "hidden" name = "cStep"        value = "">
              <input type = "hidden" name = "cStep_Ant"    value = "">
              <input type = "hidden" name = "cMifId"       value = "">
              <input type = "hidden" name = "cAnio"        value = "">
              <input type = 'hidden' name = 'cMifOri'      value = "">
              <input type = 'hidden' name = 'cRegEst'      value = "">
              <input type = 'hidden' name = 'dDesde'       value = "">
              <input type = 'hidden' name = 'dHasta'       value = "">
              <input type = 'hidden' name = 'cSubservicio' value = "">
              <input type = 'hidden' name = 'cSubSerDes'   value = "">
              <input type = 'hidden' name = 'nIndexSubser' value = "">
            </form>

            <form name = 'frgrm' action = 'frmifgra.php' method = 'post' target='fmpro'>
              <input type = "hidden" name = "cStep"        value = "<?php echo $_POST['cStep'] ?>">
              <input type = "hidden" name = "cStep_Ant"    value = "<?php echo $_POST['cStep_Ant'] ?>"> <!-- Paso anterior de la factura de donde vengo navegando -->
              <input type = 'hidden' name = 'cMifId'       value = "<?php echo $cMifId ?>">
              <input type = 'hidden' name = 'cAnio'        value = "<?php echo $cAnio ?>">
              <input type = 'hidden' name = 'cMifOri'      value = "<?php echo $_POST['cMifOri'] ?>">
              <input type = 'hidden' name = 'cRegEst'      value = "<?php echo $_POST['cRegEst'] ?>">
              <input type = 'hidden' name = 'cSubservicio' value = "<?php echo $_POST['cSubservicio'] ?>">
              <input type = 'hidden' name = 'cSubSerDes'   value = "<?php echo $_POST['cSubSerDes'] ?>">
              <input type = 'hidden' name = 'nIndexSubser' value = "<?php echo $_POST['nIndexSubser'] ?>">

              <fieldset>
                <legend>Adicionar Movimiento - Paso <?php if($_POST['cStep'] == ""){ echo $_POST['cStep'] = "1";}else{ echo $_POST['cStep'];} ?></legend>
                <center>
                  <table border="0" cellpadding="0" cellspacing="0" width="620" id="idPaso1">
                    <?php $nCol = f_Format_Cols(31); echo $nCol; ?>

                    <!-- Seccion 1 -->
                    <tr>
                      <td Class = "clase08" colspan="3">Prefijo<br>
                        <input type="hidden" name="cComId">
                        <input type="hidden" name="cComCod">
                        <input type = 'text' Class = 'letra' style = 'width:60' name = 'cComPre' readonly>
                      </td>
                      <td class="clase08" colspan="8">No. M.I.F<br>
                        <input type = 'text' Class = 'letra' style = 'width:160' name = "cComCsc" readonly>
                      </td>
                      <td Class = "clase08" colspan="5">Nit<br>
                        <input type = 'text' Class = 'letra' style = 'width:100' name = 'cCliId' maxlength="20" readonly>
                      </td>
                      <td class="clase08" colspan="1">Dv<br>
                        <input type = "text" Class = "letra" style = "width:020;text-align:center" name = "cCliDV" readonly>
                      </td>
                      <td class="clase08" colspan="10">Cliente<br>
                        <input type = 'text' Class = 'letra' style = 'width:200' name = "cCliNom" readonly>
                      </td>
                      <td class="clase08" colspan="4">C&oacute;digo SAP<br>
                        <input type = 'text' Class = 'letra' style = 'width:80' name = "cCliSap" readonly>
                      </td>
                    </tr>

                    <!-- Seccion 2 -->
                    <tr>
                      <td class="clase08" colspan="6">Dep&oacute;sito<br>
                        <input type = 'text' Class = 'letra' style = 'width:120' name = 'cDepNum' maxlength="20" readonly>
                        <input type="hidden" name="cCcoIdOc">
                      </td>
                      <td class="clase08" colspan="10">Tipo Deposito<br>
                        <input type = 'text' Class = 'letra' style = 'width:200' name = "cTipoDep" readonly>
                      </td>
                      <td class="clase08" colspan="5">Periodicidad<br>
                        <input type = 'text' Class = 'letra' style = 'width:100' name = "cPerFacDes" readonly>
                      </td>
                      <td class="clase08" colspan = "5">Fecha Desde<br>
                        <input type="text" style = "width:100;height:15;text-align:center" name = "dDesde" readonly>
                      </td>
                      <td class="clase08" colspan = "5">Fecha Hasta<br>
                        <input type="text" style = "width:100;height:15;text-align:center" name = "dHasta" readonly>
                      </td>
                    </tr>

                    <!-- Seccion 3 -->
                    <tr>
                      <td Class = "clase08" colspan="31">
                        <fieldset>
                          <input type = 'hidden' name = 'cCseSubServ'>
                          <legend>Subservicios</legend>
                          <div id = 'overDivSubServicios'></div>
                        </fieldset>
                      </td>
                    </tr>
                  </table>
                  <table border="0" cellpadding="0" cellspacing="0" width="620" id="idPaso2">
                    <?php $nCol = f_Format_Cols(31); echo $nCol; ?>

                    <!-- Seccion 1 -->
                    <tr>
                      <td Class = "clase08" colspan="31">
                        <fieldset>
                          <legend id="lblDes">Descripci&oacute;n del Subservicio seleccionado</legend>
                          <div id = "overDivCantidades"></div>
                        </fieldset>
                      </td>
                    </tr>
                  </table>
                </center>
              </fieldset>
            </form>
          </td>
        </tr>
      </table>
    </center>
    <center>
      <table border="0" cellpadding="0" cellspacing="0" width="620">
        <tr height="21">
        <?php switch ($_POST['cStep']) {
            case "1": ?>
              <td width="438" height="21"></td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" 
                onClick = "javascript:document.forms['frgrm']['cStep'].value = '2';
                                      document.forms['frgrm']['cStep_Ant'].value = '1';
                                      fnAsignarValores();
                                      if (document.forms['frgrm']['cSubservicio'].value != '') {
                                        document.forms['frestado'].target='fmwork';
                                        document.forms['frestado'].action='frmifamo.php';
                                        document.forms['frestado'].submit()
                                      } else {
                                        alert('Debe Seleccionar un Subservicio');
                                      }"
                >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Siguiente</td>

              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
            <?php break;
            case "2": ?>
              <td width="438" height="21"></td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/anterior.gif" style="cursor:pointer"
                onClick = "javascript:document.forms['frgrm']['cStep'].value = '1';
                                      document.forms['frgrm']['cStep_Ant'].value = '2';
                                      fnAsignarValores();
                                      document.forms['frestado'].target='fmwork';
                                      document.forms['frestado'].action='frmifamo.php';
                                      document.forms['frestado'].submit()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Anterior</td>
              <td width="91" height="21">
                  <input type="button" name="Btn_Guardar" id="Btn_Guardar" value="Grabar" Class="name bntGuardar"
                            onclick = "javascript:document.forms['frgrm'].submit();"></td>
            <?php break;
          } ?>
        </tr>
      </table>
    </center>
    <br>
    <?php
      // Se obtiene la informacion de la M.I.F
      $pArrayDatos = array();
      $pArrayDatos['cMifId'] = $cMifId;
      $pArrayDatos['cAnio']  = $cAnio;

      $mReturnMif = $ObjcMatrizInsumosFacturables->fnCargarDataMIF($pArrayDatos);
      $mData      = $mReturnMif[1];
      // echo "<pre>";
      // print_r($mData);
      if ($_POST['cStep'] == "2") {
        fnCargaCantidadSubservicio($_POST['cSubservicio'], $_POST['cSubSerDes'], $_POST['dDesde'], $_POST['dHasta']);
      }
    ?>
    <script language = "javascript">
      fnMostrarOcultarObjetos("<?php echo $_POST['cStep'] ?>");

      document.forms['frgrm']['cComId'].value     = "<?php echo $mData['comidxxx'] ?>";
      document.forms['frgrm']['cComCod'].value    = "<?php echo $mData['comcodxx'] ?>";
      document.forms['frgrm']['cComPre'].value    = "<?php echo $mData['comprexx'] ?>";
      document.forms['frgrm']['cComCsc'].value    = "<?php echo $mData['comcscxx'] ?>";
      document.forms['frgrm']['cCliId'].value     = "<?php echo $mData['cliidxxx'] ?>";
      document.forms['frgrm']['cCliDV'].value     = "<?php echo gendv($mData['cliidxxx']) ?>";
      document.forms['frgrm']['cCliNom'].value    = "<?php echo $mData['climonxx'] ?>";
      document.forms['frgrm']['cCliSap'].value    = "<?php echo $mData['clisapxx'] ?>";
      document.forms['frgrm']['cDepNum'].value    = "<?php echo $mData['depnumxx'] ?>";
      document.forms['frgrm']['cCcoIdOc'].value   = "<?php echo $mData['ccoidocx'] ?>";
      document.forms['frgrm']['cTipoDep'].value   = "<?php echo $mData['tdedesxx'] ?>";
      document.forms['frgrm']['cPerFacDes'].value = "<?php echo $mData['pfadesxx'] ?>";
      document.forms['frgrm']['dDesde'].value     = "<?php echo $mData['miffdexx'] ?>";
      document.forms['frgrm']['dHasta'].value     = "<?php echo $mData['miffhaxx'] ?>";
      document.forms['frgrm']['cMifOri'].value    = "<?php echo $mData['miforixx'] ?>";
      document.forms['frgrm']['cRegEst'].value    = "<?php echo $mData['regestxx'] ?>";

      if ("<?php echo $_POST['cStep'] ?>" == "1") {
        fnCargarGrillaSubServicio();
      }
    </script>
    <?php
      function fnCargaCantidadSubservicio($cSubId, $cSubDes, $dFecDesde, $dFecHasta) {
        global $cAlfa; global $xConexion01; global $cMifId; global $cAnio; global $vSysStr;

        $fechaInicio = strtotime(date($dFecDesde));
        $fechaFin    = strtotime(date($dFecHasta));

        $cTexto = "";
        $cTexto .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"300\" style=\"margin:auto\">";
          $nCol = f_Format_Cols(15); echo $nCol;
          $cTexto .= "<tr bgcolor = \"".$vSysStr['system_row_title_color_ini']."\">";
            $cTexto .= "<td colspan=\"7\"><p style = \"width:140px;text-align:center;font-weight: bold;\">D&iacute;a</p>";
            $cTexto .= "</td>";
            $cTexto .= "<td colspan=\"8\"><p style = \"width:160px;text-align:center;font-weight: bold;\">Cantidad</p>";
            $cTexto .= "</td>";
          $cTexto .= "</tr>";
        $cTexto .= "</table>";

        $cTexto .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"300\" style=\"margin:auto\">";
          $nCol = f_Format_Cols(15); echo $nCol;

          $nCount = 0;
          //Se incrementan los dias en 1
          for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){
            $cColor = "{$vSysStr['system_row_impar_color_ini']}";
            if($nCount % 2 == 0) {
              $cColor = "{$vSysStr['system_row_par_color_ini']}";
            }

            // Consulta la cantidad del subservicio
            $cFecha = date("Y-m-d", $i);
            $vMifSubservi  = array();
            $qMifSubservi  = "SELECT ";
            $qMifSubservi .= "lmsu$cAnio.*, ";
            $qMifSubservi .= "IF(lmsu$cAnio.mifdcanx > 0, lmsu$cAnio.mifdcanx, \"\") AS mifdcanx ";
            $qMifSubservi .= "FROM $cAlfa.lmsu$cAnio ";
            $qMifSubservi .= "WHERE ";
            $qMifSubservi .= "$cAlfa.lmsu$cAnio.mifidxxx = \"$cMifId\" AND ";
            $qMifSubservi .= "$cAlfa.lmsu$cAnio.subidxxx = \"$cSubId\" AND ";
            $qMifSubservi .= "$cAlfa.lmsu$cAnio.mifdfecx = \"$cFecha\" AND ";
            $qMifSubservi .= "$cAlfa.lmsu$cAnio.regestxx = \"ACTIVO\" LIMIT 0,1";
            $xMifSubservi  = f_MySql("SELECT","",$qMifSubservi,$xConexion01,"");
            // echo $qMifSubservi . " ~ " . mysql_num_rows($xMifSubservi);
            // echo "<br>";
            if (mysql_num_rows($xMifSubservi) > 0) {
              $vMifSubservi = mysql_fetch_array($xMifSubservi);
            }

            $cTexto .= "<tr bgcolor = \"".$cColor."\">";
              $cTexto .= "<td colspan=\"7\"><p style = \"width:140px;padding-left:5px;height:10px;text-align:center;\">".$cFecha."</p>";
              $cTexto .= "</td>";
              $cTexto .= "<td colspan=\"8\">";
                $cTexto .= "<input type = \"hidden\" name = \"cMifdId".$nCount."\" id = \"cMifdId".$nCount."\" value = \"".$vMifSubservi['mifdidxx']."\">";
                $cTexto .= "<input type = \"hidden\" name = \"cMifdFec".$nCount."\" id = \"cMifdFec".$nCount."\" value = \"".$cFecha."\">";
                $cTexto .= "<input type = \"hidden\" name = \"cMifdMod".$nCount."\" id = \"cMifdMod".$nCount."\" value = \"".$vMifSubservi['mifdmodx']."\">";
                $cTexto .= "<input type = \"hidden\" name = \"nCantOcul".$nCount."\" id = \"nCantOcul".$nCount."\" value = \"".((!empty($vMifSubservi['mifdcanx'])) ? ($vMifSubservi['mifdcanx']+0) : "")."\">";
                $cReadonly = ($vMifSubservi['mifdcanx'] != "" && $vMifSubservi['mifdmodx'] != "SI") ? "readonly" : "";
                $cTexto .= "<input type = \"text\" class = \"letra\" min = \"0\" style = \"width:150px;height:21px;border:1;text-align:right;\" name = \"nCant".$nCount."\" id = \"".$nCount."\" value = \"".((!empty($vMifSubservi['mifdcanx'])) ? ($vMifSubservi['mifdcanx']+0) : "")."\" ".$cReadonly.">";
              $cTexto .= "</td>";
            $cTexto .= "</tr>";
            $nCount++;
          }
          $cTexto .= "<input type = \"hidden\" name = \"nCantSub\" id = \"nCantSub\" value = \"".$nCount."\">";
        $cTexto .= "</table>";
        ?>  
        <script languaje = "javascript">
          document.getElementById("lblDes").innerHTML = '<?php echo $cSubDes; ?>';
          document.getElementById('overDivCantidades').innerHTML = '<?php echo $cTexto ?>';
        </script>
      <?php 
      }
    ?>
  </body>
</html>
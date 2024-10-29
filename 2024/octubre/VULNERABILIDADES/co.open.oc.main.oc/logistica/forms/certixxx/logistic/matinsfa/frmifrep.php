<?php
  namespace openComex;
  /**
   * Cargar Reporte - Matriz de Insumos Facturables.
   * --- Descripcion: Permite Cargar Reporte de una Matriz de Insumos Facturables.
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
    <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
    <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
    <script languaje = "javascript">
      function fnRetorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
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
        document.forms['frgrm']['nIndexSubser'].value = xIndice;
      }

      function fnSubir() {
        document.forms['frgrm'].action = "frmifcag.php";
        document.forms['frgrm'].submit();
      }
    </script>
  </head>
  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
    <center>
      <table border="0" cellpadding="0" cellspacing="0" width="620">
        <tr>
          <td>
            <form name = "frgrm" enctype='multipart/form-data' action = "frmifcag.php" method = "post" target="fmpro">
              <input type = 'hidden' name = 'cMifId'       value = "<?php echo $cMifId ?>">
              <input type = 'hidden' name = 'cAnio'        value = "<?php echo $cAnio ?>">
              <input type = 'hidden' name = 'cMifOri'      value = "<?php echo $_POST['cMifOri'] ?>">
              <input type = 'hidden' name = 'cRegEst'      value = "<?php echo $_POST['cRegEst'] ?>">
              <input type = 'hidden' name = 'cSubservicio' value = "<?php echo $_POST['cSubservicio'] ?>">
              <input type = 'hidden' name = 'nIndexSubser' value = "<?php echo $_POST['nIndexSubser'] ?>">

              <fieldset>
                <legend>Cargue Reporte</legend>
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

                    <!-- Seccion 4 -->
                    <tr>
                      <td Class = "clase08" colspan="31">
                        <fieldset>
                          <legend>Cargue</legend>
                          <table border = "0" cellpadding = "0" cellspacing = "0" width="580">
                            <?php $nCol = f_Format_Cols(29); echo $nCol; ?>
                            <tr>
                              <td Class="name" colspan="29">Archivo<br>
                                <input type = "file" Class = "letra" style = "width:380px;height:22px" name = "cArcPla">
                              </td>
                            </tr>
                            <tr>
                              <td Class="letra" colspan="25"><br>
                                <b>Recomendaciones:</b><br>
                                Debe exportar el archivo Excel a un archivo TXT delimitado por tabulaciones.<br><br>
                              </td>
                            </tr>
                          </table>
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
      <table border = "0" cellpadding = "0" cellspacing = "0" width="620">
        <tr height="21">
          <td width="438" height="21">&nbsp;</td>
          <td width="91" height="21" Class="name" >
            <input type="button" name="Btn_Subir" id="Btn_Subir" value="Subir" Class = "name"  style = "cursor:pointer;width:91;height:21;background:url(<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_ok_bg.gif) no-repeat;border:0px"
              onclick = "javascript:fnSubir()">
          </td>
          <td width="91" height="21" Class="name" >
            <input type="button" value="Salir" Class = "name"  style = "cursor:pointer;width:91;height:21;background:url(<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_cancel_bg.gif) no-repeat;border:0px"
              onClick = "javascript:fnRetorna()">
          </td>
        </tr>
      </table>
    </center>
    <br>
    <?php
      // Se obtiene la informacion de la M.I.F
      $pArrayDatos = array();
      $pArrayDatos['cMifId'] = $cMifId;
      $pArrayDatos['cAnio']  = $cAnio;

      $mData = array();
      $mReturnMif = $ObjcMatrizInsumosFacturables->fnCargarDataMIF($pArrayDatos);
      if ($mReturnMif[0] == "true") {
        $mData = $mReturnMif[1];
      }
    ?>
    <script language = "javascript">
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

      fnCargarGrillaSubServicio();
    </script>
  </body>
</html>
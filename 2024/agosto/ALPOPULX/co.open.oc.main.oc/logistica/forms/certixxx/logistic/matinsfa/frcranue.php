<?php
  /**
   * Ventana Cargar Anexos.
   * --- Descripcion: Permite Cargar Anexos de un registro M.I.F
   * @author Elian Amado. elian.amado@openits.co
   * @package opencomex
   * @version 001
   */
  include("../../../../../financiero/libs/php/utility.php");

  // Obtengo los datos de Gestor Documental - Tipos Documentales
  $mMatrizTipDoc = array();
  $qTipDoc  = "SELECT tdoidxxx, tdodesxx ";
  $qTipDoc .= "FROM $cAlfa.lpar0162 ";
  $qTipDoc .= "WHERE ";
  $qTipDoc .= "tdogruxx = \"MIF\" AND ";
  $qTipDoc .= "regestxx = \"ACTIVO\"";
  $xTipDoc = f_MySql("SELECT","",$qTipDoc,$xConexion01,"");
  if (mysql_num_rows($xTipDoc) > 0) {
    while ($vTipDoc = mysql_fetch_array($xTipDoc)) {
      $nInd_mMatrizTipDoc = count($mMatrizTipDoc);
      $mMatrizTipDoc[$nInd_mMatrizTipDoc]['tdoidxxx'] = $vTipDoc['tdoidxxx'];
      $mMatrizTipDoc[$nInd_mMatrizTipDoc]['tdodesxx'] = $vTipDoc['tdodesxx'];
    }
  }
?>
<html>
  <head>
    <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
    <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
    <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
    <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
    <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
    <script languaje="javascript" src="<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
    <script languaje="javascript" src="<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
    <script languaje="javascript">
      function fnRetorna() {
        if ("<?php echo $cOrigen ?>" == "CERTIFICACION") {
          document.location="../certifix/frcerini.php";
          parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
        } else if("<?php echo $cOrigen ?>" == "PEDIDO") {
          document.location="../pedidoxx/frpedini.php";
          parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
        } else {
          document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
          parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
        }
      }

      function fnAddNewRow() {
        var cGrid      = document.getElementById("Grid");
        var nLastRow   = cGrid.rows.length;
        var nSecuencia = nLastRow+1;

        // Verificar si nSecuencia es 10 o mas
        if (nSecuencia > 10) {
            alert("No se pueden agregar mas de 10 filas.");
            return; // Salir de la función si nSecuencia es 10 o más
        }

        var cTableRow  = cGrid.insertRow(nLastRow);

        var fCrgArch = "fCrgArch" + nSecuencia;
        var sTipDocu = "sTipDocu" + nSecuencia;

        var TD_xAll = cTableRow.insertCell(0);
        TD_xAll.className   = "name";
        TD_xAll.style.width = "380px";
        TD_xAll.innerHTML   = "Archivo:<br>" +
                              "<input type='file' class='letra' style='width: 380px; height: 22px;' name="+fCrgArch+" id="+fCrgArch+" accept='.jpg, .jpeg, .pdf, .doc, .docx, .xls, .xlsx'>";

        var TD_xAll = cTableRow.insertCell(1);
        TD_xAll.className   = "name";
        TD_xAll.style.width = "400px";
        TD_xAll.innerHTML   = "Tipo Documental " +
                              "<select name="+sTipDocu+" id="+sTipDocu+" style='width: 400px;'>" +
                                "<option value='' selected>[SELECCIONE]</option>" +
                                <?php for($i=0;$i<count($mMatrizTipDoc);$i++) { ?> 
                                  "<option value='<?php echo $mMatrizTipDoc[$i]['tdoidxxx'] ?>'><?php echo $mMatrizTipDoc[$i]['tdodesxx'] ?></option>" +
                                <?php } ?> 
                              "</select>" ;

        var TD_xAll = cTableRow.insertCell(2);
        TD_xAll.style       = "padding-top: 13px";
        TD_xAll.style.width = "20px";
        TD_xAll.innerHTML   = "<input type='button' style='width: 020px; text-align: center;' id="+nSecuencia+" value='X' "+
                                      "onclick='javascript:fnDeleteRow(this.value,\""+nSecuencia+"\",\"Grid\", this);'>";
        document.forms['frgrm']['nSecuencia'].value = nSecuencia;
      }

      function fnDeleteRow(xNumRow, xSecuencia, xTabla, btn) {
        var cGrid = document.getElementById(xTabla);
        var nLastRow = cGrid.rows.length;
        if (nLastRow > 1 && xNumRow === "X") {
          if (confirm("Realmente Desea Eliminar La Secuencia [" + xSecuencia + "]?")) {
            // Elimina la fila actual
            var row = btn.parentNode.parentNode;
            cGrid.deleteRow(row.rowIndex);
            
            // Reasigna los IDs y nombres de los campos restantes
            for (var i = 0; i < cGrid.rows.length; i++) {
              var nNuevaSecuencia = i + 1;
              var fCrgArch = "fCrgArch" + nNuevaSecuencia;
              var sTipDocu = "sTipDocu" + nNuevaSecuencia;
              
              var inputFile = cGrid.rows[i].cells[0].getElementsByTagName('input')[0];
              var selectTipDocu = cGrid.rows[i].cells[1].getElementsByTagName('select')[0];
              
              inputFile.id = fCrgArch;
              inputFile.name = fCrgArch;
              
              selectTipDocu.id = sTipDocu;
              selectTipDocu.name = sTipDocu;
            }

            // Actualiza el valor de la secuencia en el formulario
            document.forms['frgrm']['nSecuencia'].value = nLastRow - 1;
          }
        } else {
            alert("No se Pueden Eliminar Todas las Secuencias, Verifique.");
        }
      }
    </script>
  </head>
  <body topmargin="0" leftmargin="0" marginwidth="0" marginheight="0" style="margin-right:0">
    <center>
      <table border="0" cellpading="0" cellspacing="0" width="800">
        <tr>
          <td>
            <fieldset>
              <legend><?php echo $_COOKIE['kMenDes'] ?></legend>
              <form name="frgrm" action="frcragra.php" method="post" target="fmpro" enctype="multipart/form-data">
                <input type="hidden" name="nSecuencia" value="">
                <input type="hidden" name="nMifId"    value="<?php echo $nMifId ?>">
                <input type="hidden" name="dFechaMif" value="<?php echo $dFechaMif ?>">
                <input type="hidden" name="cOrigen"   value="<?php echo $cOrigen ?>">
                <center>
                  <table border="0" cellpadding="0" cellspacing="0" width="800">
                    <?php $nCol = f_Format_Cols(40); echo $nCol; ?>
                    <tr>
                      <td class="clase08" colspan="40">
                        <fieldset>
                          <legend>Anexos</legend>
                          <table border="0" cellpadding="0" cellspacing="0" width="800">
                            <td class="clase08" colspan="40" align="right">
                              <img src="<?php echo $cPlesk_Skin_Directory ?>/btn_create-dir_bg.gif" onclick="javascript:fnAddNewRow()" style="cursor: pointer;" title="Adicionar">
                            </td>
                          </table>
                          <table border="0" cellpadding="0" cellspacing="0" width="800" id="Grid"></table>
                        </fieldset>
                      </td>
                    </tr>
                  </table>
                </center>
                <script languaje="javascript">
                  fnAddNewRow();
                </script>
              </form>
            </fieldset>
          </td>
        </tr>
      </table>
    </center>
    <center>
      <table border="0" cellpading="0" cellspacing="0" width="800">
        <tr>
          <td width="900" height="21"></td>
          <td width="100" height="21" class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_ok_bg.gif" style="cursor: pointer;" onclick="javascript:document.forms['frgrm'].submit()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Upload</td>
          <td width="106.6" height="21" class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_cancel_bg.gif" style="cursor: pointer;" onclick="javascript:fnRetorna()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
        </tr>
      </table>
    </center>
  </body>
</html>
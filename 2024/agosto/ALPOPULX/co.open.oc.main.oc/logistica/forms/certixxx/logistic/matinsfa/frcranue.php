<?php
  /**
   * Ventana Cargar Anexos.
   * --- Descripcion: Permite Cargar Anexos de un registro M.I.F
   * @author Elian Amado. elian.amado@openits.co
   * @package opencomex
   * @version 001
   */
  include("../../../../../financiero/libs/php/utility.php");
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
        document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
      }

      function fnAddNewRow() {
        var cGrid      = document.getElementById("Grid");
        var nLastRow   = cGrid.rows.length;
        var nSecuencia = nLastRow+1;
        var cTableRow  = cGrid.insertRow(nLastRow);

        var fCrgArch = "fCrgArch" + nSecuencia;
        var sTipDocu = "sTipDocu" + nSecuencia;

        var TD_xAll = cTableRow.insertCell(0);
        TD_xAll.className   = "name";
        TD_xAll.style.width = "380px";
        TD_xAll.innerHTML   = "Archivo:<br>" +
                              "<input type='file' class='letra' style='width: 380px; height: 22px;' name="+fCrgArch+" id="+fCrgArch+">";

        var TD_xAll = cTableRow.insertCell(1);
        TD_xAll.className   = "name";
        TD_xAll.style.width = "400px";
        TD_xAll.innerHTML   = "Tipo Documental " +
                              "<select name="+sTipDocu+" id="+sTipDocu+" style='width: 400px;'>" +
                                "<option value=''>[SELECCIONE]</option>" +
                              "</select>" ;

        var TD_xAll = cTableRow.insertCell(2);
        TD_xAll.style       = "padding-top: 13px";
        TD_xAll.style.width = "20px";
        TD_xAll.innerHTML   = "<input type='button' style='width: 020px; text-align: center;' id="+nSecuencia+" value='X' "+
                                      "onclick='javascript:fnDeleteRow(this.value,\""+nSecuencia+"\",\"Grid\", this);'>";
        document.forms['frgrm']['nSecuencia'].value = nSecuencia;
      }

      function fnDeleteRow(xNumRow,xSecuencia,xTabla) {
        var cGrid = document.getElementById(xTabla);
        var nLastRow = cGrid.rows.length;
        if (nLastRow > 1 && xNumRow == "X") {
          if (confirm("Realmente Desea Eliminar La Secuencia ["+xSecuencia+"]?")){
            if(xSecuencia < nLastRow) {
              var j=0;
              for(var i=xSecuencia;i<nLastRow;i++){
                j = parseFloat(i)+1;
                document.forms['frgrm']['fCrgArch'+ i].value = document.forms['frgrm']['fCrgArch'+ j].value; 
                document.forms['frgrm']['sTipDocu'+ i].value = document.forms['frgrm']['sTipDocu'+ j].value; 
              }
            }
            cGrid.deleteRow(nLastRow - 1);
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
              <form name="frgrm" action="" method="post" target="fmpro">
                <input type="hidden" name="nSecuencia" value="">
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
          <td width="100" height="21" class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_ok_bg.gif" style="cursor: pointer;" onclick="javascript:">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Upload</td>
          <td width="106.6" height="21" class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_cancel_bg.gif" style="cursor: pointer;" onclick="javascript:fnRetorna()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
        </tr>
      </table>
    </center>
  </body>
</html>
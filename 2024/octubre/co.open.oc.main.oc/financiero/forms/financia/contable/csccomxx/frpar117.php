<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");
  $qTblCom = "SELECT TABLE_COMMENT FROM $cOmega.TABLES WHERE TABLE_NAME = \"fpar0117\" LIMIT 0,1";
  $xTblCom = f_MySql("SELECT","",$qTblCom,$xConexion01,"");
  $vTblCom = mysql_fetch_array($xTblCom);
?>
<html>
  <head>
    <title><?php echo $vTblCom['TABLE_COMMENT'] ?></title>
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
    <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
    <script languaje = 'javascript'>
      function fnCargaGrilla() {
        switch (document.forms['frnav']['nRecords'].value) {
          case "1":
            if (document.forms['frnav']['oChkTra'].checked == true) {
              var vMatriz = document.forms['frnav']['oChkTra'].id.split("~");
              window.opener.document.forms['frgrm']['cComId<?php echo $nSecuencia ?>'].value  = vMatriz[0];
              window.opener.document.forms['frgrm']['cComCod<?php echo $nSecuencia ?>'].value = vMatriz[1];
              window.opener.document.forms['frgrm']['cComDes<?php echo $nSecuencia ?>'].value = vMatriz[2];
            }
          break;
          default:
              var nSecuencia = '<?php echo $nSecuencia ?>';
              for (i=0;i<document.forms['frnav']['nRecords'].value;i++) {
                if (document.forms['frnav']['oChkTra'][i].checked == true) {
                  if(nSecuencia != '<?php echo $nSecuencia ?>') {
                    window.opener.fnAddComprobante();
                  }
                  var vMatriz = document.forms['frnav']['oChkTra'][i].id.split("~");
                  window.opener.document.forms['frgrm']['cComId'+nSecuencia].value  = vMatriz[0];
                  window.opener.document.forms['frgrm']['cComCod'+nSecuencia].value = vMatriz[1];
                  window.opener.document.forms['frgrm']['cComDes'+nSecuencia].value = vMatriz[2];
                  nSecuencia++;
                }
              }
          break;
        }
        window.close();
      }

      function fnMarca() {//Marca y Desmarca los Comprobantes Seleccionados en la tabla
      if (document.forms['frnav']['nCheckAll'].checked == true){
        if (document.forms['frnav']['nRecords'].value == 1){
          document.forms['frnav']['oChkTra'].checked=true;
        } else {
            if (document.forms['frnav']['nRecords'].value > 1){
              for (i=0;i<document.forms['frnav']['oChkTra'].length;i++){
                document.forms['frnav']['oChkTra'][i].checked = true;
              }
            }
        }
       } else {
          if (document.forms['frnav']['nRecords'].value == 1){
            document.forms['frnav']['oChkTra'].checked=false;
          } else {
              if (document.forms['frnav']['nRecords'].value > 1){
                for (i=0;i<document.forms['frnav']['oChkTra'].length;i++){
                  document.forms['frnav']['oChkTra'][i].checked = false;
                }
              }
           }
        }
      }
    </script>
  </head>
  <body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
    <center>
      <table border = "0" cellpadding = "0" cellspacing = "0" width = "450">
        <tr>
          <td>
            <fieldset>
              <legend><?php echo $vTblCom['TABLE_COMMENT'] ?></legend>
              <form name = "frnav" action = "" method = "post" target = "fmpro">
                <input type = "hidden" name = "nRecords"   value = "0">
                <?php

                $vComprobantes = explode("|",$cComprobantes);
                $cComUsados = "";
                for ($n=0; $n<count($vComprobantes); $n++) {
                  if ($vComprobantes[$n] != "") {
                    $cComUsados .= "\"{$vComprobantes[$n]}\",";
                  }
                }
                $cComUsados = substr($cComUsados, 0, -1);

                $qDatExt  = "SELECT * ";
                $qDatExt .= "FROM $cAlfa.fpar0117 ";
                $qDatExt .= "WHERE ";
                $qDatExt .= "comidxxx LIKE \"%$cComId%\" AND ";
                $qDatExt .= "comcodxx LIKE \"%$cComCod%\" AND ";
                if ($cComUsados != "") {
                  $qDatExt .= "CONCAT(comidxxx,\"~\",comcodxx) NOT IN ($cComUsados) AND ";
                }
                $qDatExt .= "regestxx = \"ACTIVO\" ORDER BY comcodxx";
                $xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
                // f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));
                if (mysql_num_rows($xDatExt) > 0) { ?>
                  <center>
                    <table cellspacing = "0" cellpadding = "1" border = "1" width = "450">
                      <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
                        <td widht = "020" Class = "name" style = "text-align:center">
                          <input type="checkbox" name="nCheckAll" onClick = "javascript:fnMarca();">
                        </td>
                        <td widht = "030" Class = "name"><center>Id</center></td>
                        <td widht = "030" Class = "name"><center>Cod</center></td>
                        <td widht = "390" Class = "name"><center>Descripcion</center></td>
                      </tr>
                      <?php
                      while ($xRDE = mysql_fetch_array($xDatExt)) { ?>
                        <tr>
                          <td width = "020" class= "name" style = "text-align:center">
                            <input type="checkbox" name="oChkTra" value = "<?php echo mysql_num_rows($xDatExt) ?>"
                                    id = "<?php echo $xRDE['comidxxx']."~".$xRDE['comcodxx']."~".$xRDE['comdesxx'] ?>">
                          </td>
                          <td width = "050" class= "letra7" style = "text-align:center"><?php echo $xRDE['comidxxx'] ?></td>
                          <td width = "050" class= "letra7" style = "text-align:center"><?php echo $xRDE['comcodxx'] ?></td>
                          <td width = "330" class= "letra7"><?php echo $xRDE['comdesxx'] ?></td>
                        </tr>
                      <?php } ?>
                    </table>
                  </center>
                  <script languaje = "javascript">
                    document.forms['frnav']['nRecords'].value = "<?php echo mysql_num_rows($xDatExt) ?>";
                  </script>
                <?php } else { f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros, Verifique."); ?>
                  <script languaje="javascript">
                    window.opener.document.forms['frgrm']['cComId<?php echo $nSecuencia ?>'].value  = '';
                    window.opener.document.forms['frgrm']['cComCod<?php echo $nSecuencia ?>'].value = '';
                    window.opener.document.forms['frgrm']['cComDes<?php echo $nSecuencia ?>'].value = '';
                    window.close();
                  </script>
                <?php } ?>
              </form>
            </fieldset>
            <center>
              <table border="0" cellpadding="0" cellspacing="0" width="450">
                <tr height="21">
                  <td width="268" height="21"></td>
                  <td width="91" height="21" Class="name">
                    <input type="button" Class="name" name="Btn_Aceptar" value = "Guardar" style="border:0px;width:91;height:21px;background:url('<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif');text-align:center"
                      onclick="javascript:fnCargaGrilla();">
                  </td>
                   <td width="91" height="21" Class="name">
                      <input type="button" Class="name" name="Btn_Salir"   value = "Salir"   style="border:0px;width:91;height:21px;background:url('<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif');text-align:center"
                        onclick="javascript:parent.window.close()" readonly>
                  </td>
                </tr>
              </table>
            </center>
          </td>
        </tr>
      </table>
    </center>
  </body>
</html>

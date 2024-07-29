<?php
/**
 * Asignar Disconformidad Factura.
 * --- Descripcion: Permite Asignar Disconformidad de comprobantes
 * @author Camilo Dulce. <camilo.dulce@open-eb.co>
 * @package openComex
 */

include("../../../../libs/php/utility.php"); 

if (!empty($gPrints)) { ?>
  <html>
    <head>
      <title>Asignar Disconformidad</title>
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
      <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
      <script type="text/javascript">
        function fnGuardar(){
          if(document.frnav['cDisId'].value != ""){
            document.frnav.submit();
          }else{
            alert("Disconformidad No Puede Ser Vacia");
          }
        }

        function fnLinks(xLink,xSwitch,xIteration) {
          var nX = screen.width;
					var nY = screen.height;

          switch (xLink) {
            case "cDisId":
              if (xSwitch == "VALID" && document.frnav[xLink].value == ''){
                document.frnav['cDisDes'].value = '';
              }
              if (xSwitch == "VALID" && document.frnav[xLink].value.length > 0){
                var cRuta  = "frdis160.php?gWhat=VALID&gFunction="+xLink+"&gDisId="+document.frnav[xLink].value.toUpperCase();
                parent.fmpro.location = cRuta;
              }else{
                if (xSwitch == "WINDOW"){
                  var nNx     = (nX-600)/2;
                  var nNy     = (nY-550)/2;
                  var zWinPro = 'width=600,scrollbars=1,height=550,left='+nNx+',top='+nNy;
                  var cRuta   = "frdis160.php?gWhat=WINDOW&gFunction="+xLink+"&gDisId="+document.frnav[xLink].value.toUpperCase();
                  if (xIteration == -1){
                    cRuta = "frdis160.php?gWhat=WINDOW&gFunction="+xLink+"&gDisId="+"&gTraOdi=";
                  }
                  zWindow = window.open(cRuta,"zWindow",zWinPro);
                  zWindow.focus();
                }else{
                  if (xSwitch == "EXACT"){
                    var cRuta  = "frdis160.php?gWhat=EXACT&gFunction="+xLink+"&gDisId="+document.frnav[xLink].value.toUpperCase();
                    parent.fmpro.location = cRuta;
                  }
                }
              }            
            break;
          }
        }
      </script>
    </head>
    <body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
      <center>
        <table border = "0" cellpadding= "0" cellspacing= "0" width = "400">
          <tr>
            <td>
              <fieldset>
                <legend id="idComprobantes">Asignar Disconformidad</legend>
                <form name = 'frnav' action = 'frdisgra.php' method = 'post' target="fmpro">
                  <input type="hidden" name="cComIds" value=<?php echo $gPrints ?> >
                  <input type="hidden" name="cModo"   value=<?php echo $gModo ?> >
                  <center>
                    <table border = "0" cellpadding = "0" cellspacing = "0" width = "400">
                      <?php echo columnas(20, 20); ?>
                      <tr>
                        <td Class = "name" colspan = "3"><a href = "javascript:fnLinks('cDisId','WINDOW',-1)" title="Buscar Disconformidad">Id</a><br>
                          <input type = "text" Class = "letra" style = "width:60;text-align:left" name = "cDisId"
                            onBlur = "javascript:this.value=this.value.toUpperCase();																								
                                                fnLinks('cDisId','VALID');
                                                this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                            onFocus="javascript:document.forms['frnav']['cDisId'].value  = '';
                                                document.forms['frnav']['cDisDes'].value = '';
                                                this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                        </td>
                        <td Class = "name" colspan = "17">Descripci&oacute;n<br>
                          <input type = "text" Class = "letra" name = "cDisDes" style = "width:340" readonly>
                        </td>
                      </tr>
                    </table>
                  </center>
                </form>
              </fieldset>
              <center>
              <table border="0" cellpadding="0" cellspacing="0" width="400">
                <tr height="21">
                  <td width="218" height="21"></td>
                  <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:fnGuardar();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
                  <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:parent.window.close()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
                </tr>
              </table>
              </center>
            </td>
          </tr>
        </table>
      </center>
    </body>
  </html>
  <?php

  fnCargaData($gPrints);

} else {
  f_Mensaje(__FILE__,__LINE__,"No se Recibieron Parametros Completos");
  ?><script>
    window.close();
  </script><?php
} 

function fnCargaData($xPrints) {

  $mComprobante = f_Explode_Array($xPrints,"|","~");
  $cComprobantes = "";
  for ($nP=0; $nP<count($mComprobante); $nP++) {
    if ($mComprobante[$nP][0] != "") {
      $cSaltoLinea = (($nP % 6) == 0 ? "<br>" : "");
      $cComprobantes .= $mComprobante[$nP][0]."-".$mComprobante[$nP][1]."-".$mComprobante[$nP][2].",".$cSaltoLinea;
    }
  }
  $cComprobantes = substr($cComprobantes, 0, -2);

  ?>
  <script language = "javascript">
    // document.getElementById("idComprobantes").innerHTML = 'Asignar Disconformidad <?php echo $cComprobantes ?>';
  </script>
  <?php
}

?>
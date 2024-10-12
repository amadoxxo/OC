<?php
  namespace openComex;
/**
 * Ini reporte de ingresos propios pagos terceros facturados
 * @package opencomex
 * @todo Agregar DatePickers
 */

# Librerias
include("../../../../libs/php/utility.php");

$dHoy = date('Y-m-d');

$qSysProbg = "SELECT * ";
$qSysProbg .= "FROM $cBeta.sysprobg ";
$qSysProbg .= "WHERE ";
$qSysProbg .= "DATE(regdcrex) =\"$dHoy\" AND ";
$qSysProbg .= "regusrxx = \"$kUser\" AND ";
$qSysProbg .= "pbadbxxx = \"$cAlfa\" AND ";
$qSysProbg .= "pbamodxx = \"FACTURACION\" AND ";
$qSysProbg .= "pbatinxx = \"REPORTE_IP_PCC_FACTURADOS\" ";
$qSysProbg .= "ORDER BY regdcrex DESC";
$xSysProbg = f_MySql("SELECT", "", $qSysProbg, $xConexion01, "");
// f_Mensaje(__FILE__, __LINE__,$qSysProbg."~".mysql_num_rows($xSysProbg));

$mArcProBg = array();

while ($xRB = mysql_fetch_array($xSysProbg)) {
  $vArchivos = explode("~", trim($xRB['pbaexcxx'], "~"));
  for ($nA = 0; $nA < count($vArchivos); $nA++) {
    $nInd_mArcProBg = count($mArcProBg);
    $cRuta = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $vArchivos[$nA];
    if ($vArchivos[$nA] != "" && file_exists($cRuta)) {
      $mArcProBg[$nInd_mArcProBg]['pbaexcxx'] = $vArchivos[$nA];
    } else {
      $mArcProBg[$nInd_mArcProBg]['pbaexcxx'] = "";
    }

    $mArcProBg[$nInd_mArcProBg]['pbaidxxx'] = $xRB['pbaidxxx'];
    $mArcProBg[$nInd_mArcProBg]['regunomx'] = $xRB['regunomx'];

    if ($xRB['regestxx'] != "INACTIVO") {
      $nTieEst = round(((strtotime(date('Y-m-d H:i:s')) - strtotime($xRB['regdinix'])) / ($xRB['pbatxixx'] * $xRB['pbacrexx'])), 2) . "&#37";
    } else {
      $nTieEst = "";
    }

    $mArcProBg[$nInd_mArcProBg]['pbarespr'] = $xRB['pbarespr'];
    $mArcProBg[$nInd_mArcProBg]['pbaerrxx'] = $xRB['pbaerrxx'];
    $mArcProBg[$nInd_mArcProBg]['regestxx'] = ($xRB['regdinix'] != "0000-00-00 00:00:00" && $xRB['regdfinx'] == "0000-00-00 00:00:00") ? "EN PROCESO" : $xRB['regestxx'];

    $mPost = f_Explode_Array($xRB['pbapostx'], "|", "~");
    for ($nP = 0; $nP < count($mPost); $nP++) {
      if ($mPost[$nP][0] != "") {
        $mArcProBg[$nInd_mArcProBg][$mPost[$nP][0]] = $mPost[$nP][1];
      }
    }
  }
}

?>
<html>
  <head>
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
    <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
    <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
    <!-- Funciones JavaScript -->
    <script language="JavaScript">
      function fnRetorna() {
        parent.fmwork.location='<?php echo $cPlesk_Forms_Directory ?>/frproces.php';
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
      }

      function fnRecargar() {
        parent.fmwork.location="<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>";
      }

      <?php
      /**
       * JavaScript:f_Links
       * @param string  xLink     Proceso.
       * @param string  xSwitch   Parametro de control.
       * @var   int     nX        Ancho de ventana.
       * @var   int     nY        Alto de ventana.
       * @var   string  cTerTip
       * @var   string  cTerId
       * @var   string  cTerNom
       * @var   string  cPathUrl
       * @var   int     nNx       Posición de la ventana en el eje X
       * @var   int     nNy       Posición de la ventana en el eje Y
       * @var   string  cWinOpt   Opciones de configuración de la ventana 
       */
      ?>
      function f_Links(xLink,xSwitch) {
        var nX = screen.width;
        var nY = screen.height;
        var cTerTip = 'CLICLIXX';
        switch(xLink){
          case "cTerId":
          case "cTerNom":
            var cTerId = document.forms['frgrm']['cTerId'].value.toUpperCase();
            var cTerNom = document.forms['frgrm']['cTerNom'].value.toUpperCase();
            if (xSwitch == "VALID") {
              var cPathUrl = "frcer150.php?gModo="+xSwitch+"&gFunction="+xLink+"&gTerTip="+cTerTip+"&gTerId="+cTerId+"&gTerNom="+cTerNom;
              parent.fmpro.location = cPathUrl;
            }else{
              var nNx = (nX-600)/2;
              var nNy = (nY-250)/2;
              var cWinOpt = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
              var cPathUrl = "frcer150.php?gModo="+xSwitch+"&gFunction="+xLink+"&gTerTip="+cTerTip+"&gTerId="+cTerId+"&gTerNom="+cTerNom;
              cWindow = window.open(cPathUrl,xLink,cWinOpt);
              cWindow.focus();
            }
          break;
        }
      }
      <?php
      /**
       * JavaScript:fnGenerar
       * @var int     nSwitch   Control de errores
       * @var string  cMsj      Mensaje de alerta
       * @var date    dDesde    Fecha desde
       * @var date    dHasta    Fecha hasta
       * @var string  cRuta     Ruta para imprimir archivo
       */
      ?>
      function fnGenerar(){
        var nSwitch = 0;
        var cMsj = "";
        var dDesde = new Date(document.forms['frgrm']['dDesde'].value);
        var dHasta = new Date(document.forms['frgrm']['dHasta'].value);
        dDesde.setDate (dDesde.getDate() + 1);
        dHasta.setDate (dHasta.getDate() + 1);
        if (document.forms['frgrm']['cTerId'].value == ""){
          cMsj += 'Debe Seleccionar un Tercero.\n';
          nSwitch = 1;
        }
        if (dDesde.getFullYear() != dHasta.getFullYear() ){
          cMsj += 'El periodo no corresponde al mismo ano.\n';
          nSwitch = 1;
        }
        if(nSwitch == 0){
          var cRuta = 'fripfprn.php?'+
                      'gTerId='     + document.forms['frgrm']['cTerId'].value +
                      '&gAnioD='    + document.forms['frgrm']['dDesde'].value +
                      '&gAnioH='    + document.forms['frgrm']['dHasta'].value +
                      '&cEjProBg='  + document.forms['frgrm']['cEjProBg'].value;
          // parent.fmpro.location=cRuta;
          parent.fmpro.location=cRuta;
        }else{
          alert(cMsj+"Por favor verifique.");
        }  
      }

      function chDate(fld){
        var val = fld.value;
        if (val.length > 0){
          var ok = 1;
          if (val.length < 10){
              alert('Formato de Fecha debe ser aaaa-mm-dd');
              fld.value = '';
              fld.focus();
              ok = 0;
          }
          if(val.substr(4,1) == '-' && val.substr(7,1) == '-' && ok == 1){
            var anio = val.substr(0,4);
            var mes  = val.substr(5,2);
            var dia  = val.substr(8,2);
            if (mes.substr(0,1) == '0'){
              mes = mes.substr(1,1);
            }
            if (dia.substr(0,1) == '0'){
              dia = dia.substr(1,1);
            }

            if(mes > 12){
              alert('El mes debe ser menor a 13');
              fld.value = '';
              fld.focus();
            }
            if (dia > 31){
              alert('El dia debe ser menor a 32');
              fld.value = '';
              fld.focus();
            }
            var aniobi = 28;
            if(anio % 4 ==  0){
              aniobi = 29;
            }
            if (mes == 4 || mes == 6 || mes == 9 || mes == 11){
                if (dia < 1 || dia > 30){
                  alert('El dia debe ser menor a 31, dia queda en 30');
                  fld.value = val.substr(0,8)+'30';
                }
            }
            if (mes == 1 || mes == 3 || mes == 5 || mes == 7 || mes == 8 || mes == 10 || mes == 12){
                if (dia < 1 || dia > 32){
                  alert('El dia debe ser menor a 32');
                  fld.value = '';
                  fld.focus();
                }
            }
            if(mes == 2 && aniobi == 28 && dia > 28 ){
              alert('El dia debe ser menor a 29');
              fld.value = '';
              fld.focus();
            }
            if(mes == 2 && aniobi == 29 && dia > 29){
              alert('El dia debe ser menor a 30');
              fld.value = '';
              fld.focus();
            }
            }else{
                  if(val.length > 0){
                    alert('Fecha erronea, verifique');
                  }
                    fld.value = '';
                    fld.focus();
                }
          }
      }

      function fnDescargar(xArchivo){
        parent.fmwork.location = "frgendoc.php?cRuta="+xArchivo;
      }

    </script>
  </head>
  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
    <center>
      <form name='frgrm' action='fripfprn.php' method="POST" target="fmwork">
        <center>
          <fieldset style="width: 520;">
            <legend>Reporte de Ingresos Propios Pagos Terceros Facturados</legend>
            <!-- Titulo de cabecera -->
            <table border="2" cellspacing="0" cellpadding="0" width="">
              <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="heigth:25">
                <td class="name" width="30%"><center><h5><br>REPORTE DE INGRESOS PROPIOS PAGOS TERCEROS FACTURADOS</h5></center></td>
              </tr>
            </table>
            <!-- Filtros de información -->
            <!-- Cliente -->
            <table border="0" cellspacing="0" cellpadding="0" width="">
              <tr>
                <td class="name" width="120">
                  <a href = "javascript:document.forms['frgrm']['cTerId'].value  = '';
                                        document.forms['frgrm']['cTerNom'].value = '';
                                        document.forms['frgrm']['cTerDV'].value  = '';
                                        f_Links('cTerId','VALID')" id="id_href_cTerId"><br>Nit</a><br>
                  <input type = "text" Class = "letra" style = "width:120;text-align:center" name = "cTerId"
                    onfocus="javascript:document.forms['frgrm']['cTerId'].value  = '';
                                        document.forms['frgrm']['cTerNom'].value = '';
                                        document.forms['frgrm']['cTerDV'].value  = '';
                                        this.style.background='#00FFFF'"
                    onBlur = "javascript:this.value=this.value.toUpperCase();
                                        f_Links('cTerId','VALID');
                                        this.style.background='#FFFFFF'">
                </td>
                <td class="name" width="20"><br>Dv<br>
                  <input type = "text" Class = "letra" style = "width:20;text-align:center" name = "cTerDV" readonly>
                </td>
                <td class="name" width="380"><br>Cliente<br>
                  <input type = "text" Class = "letra" style = "width:380" name = "cTerNom"
                    onfocus="javascript:document.forms['frgrm']['cTerId'].value  = '';
                                        document.forms['frgrm']['cTerNom'].value = '';
                                        document.forms['frgrm']['cTerDV'].value  = '';
                                        this.style.background='#00FFFF'"
                    onBlur = "javascript:this.value=this.value.toUpperCase();
                                        f_Links('cTerNom','VALID');
                                        this.style.background='#FFFFFF'">
                </td>
                <td class="name" width="30"></td>
              </tr>
            </table>
            <!-- Periodo -->
            <table border="0" cellspacing="0" cellpadding="0" width="">
              <tr>
                <td class="name" width="120"><br>Rango De Fechas(Fecha Doc.):</td>
                <td class="name" width="60"><br><center><a href='javascript:show_calendar("frgrm.dDesde")' id="id_href_dDesde">De</a></center></td>
                <td class="name" width="140"><br>
                  <input type="text" name="dDesde" style = "width:140; text-align:center;"
                        onblur="javascript:chDate(this);">
                </td>
                <td class="name" width="60"><br><center><a href='javascript:show_calendar("frgrm.dHasta")' id="id_href_dHasta">A</a></center></td>
                <td class="name" width="140"><br>
                  <input type="text" name="dHasta" style = "width:140; text-align:center"
                        onblur="javascript:chDate(this);">
                </td>
              </tr>
              <tr id="EjProBg">
                <td Class = "name" colspan = "25"><br>
                  <label><input type="checkbox" id="cEjProBg" name="cEjProBg" value ="SI" onclick="javascript:if(this.checked == true) { this.value = 'SI'} else { this.value = 'NO'}" checked>Ejecutar Proceso en Background</label>
                </td>
              </tr>
            </table>
          </fieldset>
          <table border="0" cellpadding="0" cellspacing="0" width="520">
            <tr height="21">
              <td width="338" height="21"></td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = 'javascript:fnGenerar()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Generar</td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
            </tr>
          </table>
        </center>
      </form>
    </center>
    <?php
    if(count($mArcProBg) > 0){ ?>
      <center>
        <table border="0" cellpadding="0" cellspacing="0" width="520">
          <tr>
            <td Class = "name" colspan = "21"><br>
              <fieldset>
                <legend>Reportes Generados. Fecha [<?php echo date('Y-m-d'); ?>]</legend>
                <label>
                  <table border="0" cellspacing="1" cellpadding="0" width="520">
                    <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="height:20px">
                      <td align="center"><strong>Usuario</strong></td>
                      <td align="center"><strong>Cliente</strong></td>
                      <td align="center"><strong>Rango Fechas</strong></td>
                      <td align="center"><strong>Resultado</strong></td>
                      <td align="center"><strong>Estado</strong></td>
                      <td align="center"><img src = "<?php echo $cPlesk_Skin_Directory ?>/b_cambest.gif" onClick = "javascript:fnRecargar()" style = "cursor:pointer" title="Recargar"></td>
                    </tr>
                    <?php for ($i = 0; $i < count($mArcProBg); $i++) {
                      $cColor = "{$vSysStr['system_row_impar_color_ini']}";
                      if($i % 2 == 0) {
                        $cColor = "{$vSysStr['system_row_par_color_ini']}";
                      }
                    ?>
                    <tr bgcolor = "<?php echo $cColor ?>">
                      <td style="padding:2px"><?php echo $mArcProBg[$i]['regunomx']; ?></td>
                      
                      <td> 
                        <?php 
                        if($mArcProBg[$i]['gTerId'] != ""){?>
                            <?php echo $mArcProBg[$i]['gTerId']; ?>
                          <?php
                        }
                        ?>
                      </td>
                      <td>
                        <?php 
                        if($mArcProBg[$i]['gDesde'] != ""){?>
                            <?php echo $mArcProBg[$i]['gDesde'] . " " . $mArcProBg[$i]['gHasta']; ?>
                          <?php
                        }
                        ?>
                      </td>
                      <td style="padding:2px"><?php echo $mArcProBg[$i]['pbarespr']; ?></td>
                      <td style="padding:2px"><?php echo $mArcProBg[$i]['regestxx']; ?></td>
                      <td>
                        <?php if ($mArcProBg[$i]['pbaexcxx'] != "") { ?>
                          <a href = "javascript:fnDescargar('<?php echo $mArcProBg[$i]['pbaexcxx']; ?>')">
                            Descargar
                          </a>
                        <?php } ?>
                        <?php if ($mArcProBg[$i]['pbaerrxx'] != "") { ?>
                          <a href = "javascript:alert('<?php echo str_replace(array("<br>","'",'"'),array("\n"," "," "),$mArcProBg[$i]['pbaerrxx']) ?>')">
                            Ver
                          </a>
                        <?php } ?>
                      </td>
                    </tr>
                    <?php } ?>
                  </table>
                </label>
              </fieldset>
            </td>
        </tr>
      </table>
      </center>
  <?php } ?> 
  </body>
</html>
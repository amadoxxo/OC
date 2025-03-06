<?php
  /**
  * Actualizacion de tafifas desde un txt delimintado por tabulaciones.
  * --- Descripcion: Actualizacion de tafifas desde un txt delimintado por tabulaciones.
  * @author Juan Jose Trujillo <juan.trujillo@openits.co>
  * @version 001
  */
  include("../../../../libs/php/utility.php");
  
  $dHoy = date('Y-m-d');
  $qSysProbg = "SELECT * ";
  $qSysProbg .= "FROM $cBeta.sysprobg ";
  $qSysProbg .= "WHERE ";
  $qSysProbg .= "DATE(regdcrex) =\"$dHoy\" AND ";
  $qSysProbg .= "regusrxx = \"$kUser\" AND ";
  $qSysProbg .= "pbadbxxx = \"$cAlfa\" AND ";
  $qSysProbg .= "pbamodxx = \"FACTURACION\" AND ";
  $qSysProbg .= "pbatinxx IN (\"FORTARIFASCON\",\"CARGARTARIFAS\") ";
  $qSysProbg .= "ORDER BY regdcrex DESC";
  $xSysProbg = f_MySql("SELECT", "", $qSysProbg, $xConexion01, "");
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
  
      $cInt = "";
      if ($xRB['pbatinxx'] == "FORTARIFASCON") {
        $cInt = " [FORMATO]";
      }
      if ($xRB['pbatinxx'] == "CARGARTARIFAS") {
        $cInt = " [CARGA DE TARIFAS]";
      }

      $mArcProBg[$nInd_mArcProBg]['pbaidxxx'] = $xRB['pbaidxxx'];
      $mArcProBg[$nInd_mArcProBg]['regunomx'] = $xRB['regunomx'];
      $mArcProBg[$nInd_mArcProBg]['pbarespr'] = $xRB['pbarespr'].$cInt;
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
  <head>
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
    <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>

    <script languaje = 'javascript'>
      function fnRetorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
      }

      function fnRecargar() {
        parent.fmwork.location="<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>";
      }

      function fnDescargar(xArchivo){
        parent.fmwork.location = "frgendoc.php?cRuta="+xArchivo;
      }

      function fnDownLoad() {
        document.forms['frgrm'].action = "frdowexc.php";
        document.forms['frgrm'].submit();
      }
      
      function fnGuardar() {
        document.forms['frgrm'].action = "frctrcrg.php";
        document.forms['frgrm'].submit();
      }
    </script>
  </head>
  <body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
    <!-- PRIMERO PINTO EL FORMULARIO -->
    <center>
      <table border ="0" cellpadding="0" cellspacing="0">
        <tr>
          <td>
            <form name = "frgrm" enctype='multipart/form-data' action = "frctrcrg.php" method = "post" target="fmpro">
              <input type="hidden" name="cTipo" value="0">
              <table border ="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td>
                    <fieldset>
                      <legend>Actualizaci&oacute;n de Tarifas </legend>
                      <table border = "0" cellpadding = "0" cellspacing = "0" width="380">
                        <?php $nCol = f_Format_Cols(19); echo $nCol; ?>
                        <tr>
                          <td Class="name" colspan="19">Archivo<br>
                            <input type = "file" Class = "letra" style = "width:380px;height:22px" name = "cArcPla">
                          </td>
                        </tr>
                        <tr>
                          <td Class="name" colspan="19"><br>
                            <a href = "javascript:fnDownLoad()">Descargar Formato</a>
                          </td>
                        </tr>
                        <tr>
                          <td Class="letra" colspan="19"><br>
                            <b>Recomendaciones:</b><br>
                            Debe exportar el archivo Excel a un archivo TXT delimitado por tabulaciones.<br>
                            Si el archivo excel fue generado desde el Reporte de Tarifas Consolidado, debe eliminar las columnas de color Rojo.<br><br>
                          </td>
                        </tr>
                        <tr id="EjProBg">
                          <td Class = "name" colspan = "25"><br>
                            <label><input type="checkbox" name="cEjProBg" value ="SI" onclick="javascript:this.value = 'SI';this.checked = true" checked>Ejecutar Proceso en Background</label>
                          </td>
                        </tr>
                      </table>
                    </fieldset>
                    <table border = "0" cellpadding = "0" cellspacing = "0" width="400">
                      <tr height="21">
                        <td width="218" height="21">&nbsp;</td>
                        <td width="91" height="21" Class="name" >
                          <input type="button" name="Btn_Subir" id="Btn_Subir" value="Subir" Class = "name"  style = "width:91;height:21;background:url(<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif) no-repeat;border:0px"
                            onclick = "javascript:fnGuardar()">
                        </td>
                        <td width="91" height="21" Class="name" >
                          <input type="button" value="Salir" Class = "name"  style = "width:91;height:21;background:url(<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif) no-repeat;border:0px"
                            onClick = "javascript:fnRetorna()">
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>																		
            </form>
          </td>
        </tr>
      </table>
    </center>
    <?php if(count($mArcProBg) > 0){ ?>
      <center>
        <table border="0" cellpadding="0" cellspacing="0" width="380">
          <tr>
            <td Class = "name" colspan = "19"><br>
              <fieldset>
                <legend>Reportes Generados. Fecha [<?php echo date('Y-m-d'); ?>]</legend>
                <label>
                  <table border="0" cellspacing="1" cellpadding="0" width="500">
                    <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="height:20px">
                      <td align="center"><strong>Usuario</strong></td>
                      <td align="center"><strong>Resultado</strong></td>
                      <td align="center"><strong>Estado</strong></td>
                      <td align="right"><img src = "<?php echo $cPlesk_Skin_Directory ?>/b_cambest.gif" onClick = "javascript:fnRecargar()" style = "cursor:pointer" title="Recargar">&nbsp;</td>
                    </tr>
                    <?php for ($i = 0; $i < count($mArcProBg); $i++) {
                      $cColor = "{$vSysStr['system_row_impar_color_ini']}";
                      if($i % 2 == 0) {
                        $cColor = "{$vSysStr['system_row_par_color_ini']}";
                      }
                      ?>
                    <tr bgcolor = "<?php echo $cColor ?>">
                      <td style="padding:2px"><?php echo $mArcProBg[$i]['regunomx']; ?></td>
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
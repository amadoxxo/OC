<?php
/**
   * Proceso Autorización Modificar Campos Pedido.
   * --- Descripcion: Permite Editar y una Autorización Modificar Campos Pedido para Excluir Subservicios del Pedido.
   * @author Elian Amado <elian.amado@openits.co>
   * @version 001
   */
  include("../../../../../financiero/libs/php/utility.php");
?>
<html>
  <head>
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
    <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
    <script language="javascript">
      function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
      }
    
      function f_Marca() {
        if (document.forms['frgrm']['nCheckAll'].checked == true){
          if (document.forms['frgrm']['nRecords'].value == 1){
            document.forms['frgrm']['cCheck'].checked=true;
          } else {
              if (document.forms['frgrm']['nRecords'].value > 1){
                for (i=0;i<document.forms['frgrm']['cCheck'].length;i++){
                  document.forms['frgrm']['cCheck'][i].checked = true;
                }
              }
          }
        } else {
            if (document.forms['frgrm']['nRecords'].value == 1){
              document.forms['frgrm']['cCheck'].checked=false;
            } else {
                if (document.forms['frgrm']['nRecords'].value > 1){
                  for (i=0;i<document.forms['frgrm']['cCheck'].length;i++){
                    document.forms['frgrm']['cCheck'][i].checked = false;
                  }
                }
            }
          }
      }

      function f_Carga_Data() {
        document.forms['frgrm']['cComMemo'].value="|";
        switch (document.forms['frgrm']['nRecords'].value) {
          case "1":
            if (document.forms['frgrm']['cCheck'].checked == true) {
              document.forms['frgrm']['cComMemo'].value += document.forms['frgrm']['cCheck'].id+"|";
            }
          break;
          default:
            if (document.forms['frgrm']['cCheck'] !== undefined) {
              for (i=0;i<document.forms['frgrm']['cCheck'].length;i++) {
                if (document.forms['frgrm']['cCheck'][i].checked == true) {
                  document.forms['frgrm']['cComMemo'].value += document.forms['frgrm']['cCheck'][i].id+"|";
                }
              }
            }
          break;
        }
        if (document.forms['frgrm']['cComMemo'].value == "|"){
          document.forms['frgrm']['cComMemo'].value = "";
        }
      }
    </script>
  </head>
  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
    <center>
      <table border ="0" cellpadding="0" cellspacing="0" width="560">
        <tr>
          <td>
            <fieldset>
              <legend><?php echo $_COOKIE['kModo']." ".$_COOKIE['kProDes'] ?></legend>
              <?php
                $pedidoId    = $_GET['pedidxxx'];
                $pedidNomb   = $_GET['pedcscxx'];
                $pedAnio     = $_GET['pedanoxx'];
                $pedidoOb    = $_GET['amcobsxx'];
                $nit         = $_GET['cliidxxx'];
                $nomCliente  = $_GET['clinomxx'];
              ?>
              <form name = 'frgrm' action = 'framcgra.php' method = 'post' target='fmpro'>
                <input type = "hidden" name = "cStep"     value = "<?php echo $_POST['cStep'] ?>">
                <input type = "hidden" name = "nRecords"  value = "<?php echo $_POST['nRecords'] ?>">
                <input type = "hidden" name = "cPedidoId" value = "<?php echo $pedidoId  ?>" readonly>
                <input type = "hidden" name = "cPedNom"   value = "<?php echo $pedidNomb ?>" readonly>
                <input type = "hidden" name = "cPedAnio"  value = "<?php echo $pedAnio  ?>" readonly>
                <input type = "hidden" name = "cPedObser" value = "<?php echo $pedidoOb ?>" readonly>
                <input type = "hidden" name = "nTimesSave" value = "0">
                <textarea name = "cComMemo"  id = "cComMemo"><?php  echo $_POST['cComMemo'] ?></textarea>
                <script languaje = "javascript">
                  document.getElementById("cComMemo").style.display ="none";
                </script>
                <center>
                <fieldset id="Grid_Paso1">
                  <legend>Datos Pedido</legend>
                  <table border = '0' cellpadding = '0' cellspacing = '0' width='560'>
                    <?php $nCol = f_Format_Cols(28); echo $nCol;?>
                    <tr>
                      <td class = "name" colspan = "2">Nit<br>
                        <input type = "text" class = "letra" name = "cNit" style = "width:100" value="<?php echo $nit ?>" readonly>
                      </td>
                      <td class = "name" colspan = "5">Cliente<br>
                        <input type = "text" class = "letra" style = "width:300;text-align:left" name = "cNomCli" value="<?php echo $nomCliente ?>"  readonly>
                      </td>
                      <td class = "name" colspan = "2">Pedido<br>
                        <input type = "text" class = "letra" name = "cPedido" style = "width:160" value="<?php echo $pedidNomb ?>" readonly>
                      </td>
                    </tr>
                  </table>
                </fieldset>
                <?php 
                switch ($_COOKIE['kModo']) {
                  case "EDITAR":
                  case "VER":
                    $_POST['cSucId']  = $gSucId;
                    $_POST['cDocId']  = $gDocId;
                    $_POST['cDocSuf'] = $gDocSuf;
                  break;
                }
                $cAnio   = $_GET['pedanoxx'];
                $vPedIds = $_GET['pedidxxx'];
                # CONSULTA PEDIDOS
                $qPedidoDet  = "SELECT ";
                $qPedidoDet .= "$cAlfa.lpca$cAnio.pedidxxx, ";
                $qPedidoDet .= "$cAlfa.lpca$cAnio.comidxxx, ";
                $qPedidoDet .= "$cAlfa.lpca$cAnio.comcodxx, ";
                $qPedidoDet .= "$cAlfa.lpca$cAnio.comprexx, ";
                $qPedidoDet .= "$cAlfa.lpca$cAnio.comcscxx, ";
                $qPedidoDet .= "$cAlfa.lpca$cAnio.comcsc2x, ";
                $qPedidoDet .= "$cAlfa.lpca$cAnio.cliidxxx, ";
                $qPedidoDet .= "$cAlfa.lpca$cAnio.comfecxx, ";
                $qPedidoDet .= "$cAlfa.lpar0150.clinomxx, ";
                $qPedidoDet .= "$cAlfa.lpde$cAnio.*, ";
                $qPedidoDet .= "$cAlfa.lpar0011.sersapxx, ";
                $qPedidoDet .= "$cAlfa.lpar0011.serdesxx, ";
                $qPedidoDet .= "$cAlfa.lpar0012.subidxxx, ";
                $qPedidoDet .= "$cAlfa.lpar0012.subdesxx ";
                $qPedidoDet .= "FROM $cAlfa.lpde$cAnio ";
                $qPedidoDet .= "LEFT JOIN $cAlfa.lpca$cAnio ON $cAlfa.lpde$cAnio.pedidxxx = $cAlfa.lpca$cAnio.pedidxxx ";
                $qPedidoDet .= "LEFT JOIN $cAlfa.lpar0011 ON $cAlfa.lpde$cAnio.sersapxx = $cAlfa.lpar0011.sersapxx ";
                $qPedidoDet .= "LEFT JOIN $cAlfa.lpar0012 ON $cAlfa.lpde$cAnio.sersapxx = $cAlfa.lpar0012.sersapxx AND $cAlfa.lpde$cAnio.subidxxx = $cAlfa.lpar0012.subidxxx ";
                $qPedidoDet .= "LEFT JOIN $cAlfa.lpar0150 ON $cAlfa.lpca$cAnio.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
                $qPedidoDet .= "WHERE ";
                $qPedidoDet .= "$cAlfa.lpde$cAnio.pedidxxx = $vPedIds";
                // f_Mensaje(__FILE__, __LINE__, $qPedidoDet."~".mysql_num_rows($xPedidoDet));
                $xPedidoDet  = f_MySql("SELECT","",$qPedidoDet,$xConexion01,"");
                ?>
                <script type="text/javascript">
                  document.forms['frgrm']['cPedido'].readOnly = true;
                  document.forms['frgrm']['cPedido'].onblur = "";
                  document.forms['frgrm']['cPedido'].onfocus = "";
                </script>
                <?php 
                if(mysql_num_rows($xPedidoDet) > 0){
                  ?>
                  <fieldset id="Tarifas">
                    <legend>Servicios a Modificar</legend>
                      <center>
                        <table border = "0" cellpadding = "0" cellspacing = "0" width = "560">
                          <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
                            <td class = "clase08" width = "060" style="padding-left:5px;padding-right:5px" align = "center">Cod SAP</td>
                            <td class = "clase08" width = "320" style="padding-left:5px;padding-right:5px" align = "center">Servicio</td>
                            <td class = "clase08" width = "160" style="padding-left:5px;padding-right:5px" align = "center">Subservicio</td>
                            <td class = "clase08" width = "020" style="padding-left:5px;padding-right:5px" align = "center"><input type="checkbox" name="nCheckAll" onClick = "javascript:f_Marca()"></td>
                          </tr>
                          <script languaje="javascript">
                            document.forms['frgrm']['nRecords'].value = "<?php echo mysql_num_rows($xPedidoDet) ?>";
                          </script>
                          <?php 
                            $y=0;
                            while ($xRT = mysql_fetch_array($xPedidoDet)) {
                              ?>
                              <tr>
                                <td bgcolor = "<?php echo $vSysStr['system_row_impar_color_ini'] ?>" class = "letra7" style="padding-left:5px;padding-right:2px;border:1px solid #E6E6E6" align="center"><?php echo $xRT['sersapxx'] ?></td>
                                <td bgcolor = "<?php echo $vSysStr['system_row_impar_color_ini'] ?>" class = "letra7" style="padding-left:5px;padding-right:2px;border:1px solid #E6E6E6"><?php echo $xRT['serdesxx'] ?></td>
                                <td bgcolor = "<?php echo $vSysStr['system_row_impar_color_ini'] ?>" class = "letra7" style="padding-left:5px;padding-right:2px;border:1px solid #E6E6E6" align="center"><?php echo $xRT['subdesxx'] ?></td>
                                <td bgcolor = "<?php echo $vSysStr['system_row_impar_color_ini'] ?>" class = "letra7" style="padding-left:5px;padding-right:2px;border:1px solid #E6E6E6" align="center">
                                  <input type="checkbox" name="cCheck"  
                                        value = "<?php echo mysql_num_rows($xPedidoDet) ?>"
                                        id="<?php echo $xRT['subidxxx'].'~'.$xRT['sersapxx']?>">
                                </td>
                              </tr>
                            <?php $y++;
                            }
                          ?>
                      </table>
                    </center>
                  </fieldset>
                <?php } else {
                  f_Mensaje(__FILE__,__LINE__,"No hay Subservicios Parametrizados para el Pedido {$_POST['cPedNom']}");
                }
                ?>
                </center>
              </form>
            </fieldset>
          </td>
        </tr>
      </table>
    </center>
    <center>
    <?php
      switch ($_COOKIE['kModo']) {
        case "EDITAR": ?>
          <table border="0" cellpadding="0" cellspacing="0" id='bnt_Paso2' width="560">
            <tr>
              <td width="378" height="21"></td>
              <td width="91" height="21" class="name">
                <input type="button" class="name" name="Btn_Guardar" value="Guardar" style = "background:url(<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif);width:91;height:21;border:0px;"
                  onclick = "javascript:f_Carga_Data();
                                        document.forms['frgrm'].target='fmpro';
                                        document.forms['frgrm'].action='framcgra.php';
                                        document.forms['frgrm']['nTimesSave'].value++;
                                        document.forms['frgrm'].submit();"></td>
              <td width="91" height="21" class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand"
                onClick ="javascript:f_Retorna();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
              </td>
            </tr>
          </table>
        <?php break;
        default: ?>
          <table border="0" cellpadding="0" cellspacing="0" id='bnt_Paso2' width="560">
            <tr>
              <td width="469" height="21"></td>
              <td width="91" height="21" class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
            </tr>
          </table>
      <?php break;
      }
      ?>
    </center>
    <?php
    switch ($_COOKIE['kModo']) {
      case "EDITAR":
        f_CargaData(); 
        ?>
        <script languaje = "javascript">
          document.forms['frgrm']['cPedido'].readOnly = true;
          document.forms['frgrm']['cPedido'].onblur = "";
          document.forms['frgrm']['cPedido'].onfocus = "";
        </script>
      <?php break;
      case "VER":
        f_CargaData(); 
        ?>
        <script languaje = "javascript">
          document.forms['frgrm']['cPedido'].readOnly = true;
          document.forms['frgrm']['cPedido'].onblur = "";
          document.forms['frgrm']['cPedido'].onfocus = "";
          for (x=0;x<document.forms['frgrm'].elements.length;x++) {
            document.forms['frgrm'].elements[x].readOnly = true;
            document.forms['frgrm'].elements[x].onfocus  = "";
            document.forms['frgrm'].elements[x].onblur   = "";
            document.forms['frgrm'].elements[x].disabled = true;
          }
        </script>
      <?php break;
    } ?>

    <?php
    function f_CargaData() {
      global $xConexion01;
      global $cAlfa;
      $cAnio   = $_GET['pedanoxx'];
      $vPedIds = $_GET['pedidxxx'];
  
      // Consulta la información de detalle de pedido
      $qPedidoDet  = "SELECT ";
      $qPedidoDet .= "$cAlfa.lpca$cAnio.pedidxxx, ";
      $qPedidoDet .= "$cAlfa.lpca$cAnio.comidxxx, ";
      $qPedidoDet .= "$cAlfa.lpca$cAnio.comcodxx, ";
      $qPedidoDet .= "$cAlfa.lpca$cAnio.comprexx, ";
      $qPedidoDet .= "$cAlfa.lpca$cAnio.comcscxx, ";
      $qPedidoDet .= "$cAlfa.lpca$cAnio.comcsc2x, ";
      $qPedidoDet .= "$cAlfa.lpca$cAnio.cliidxxx, ";
      $qPedidoDet .= "$cAlfa.lpca$cAnio.comfecxx, ";
      $qPedidoDet .= "$cAlfa.lpar0150.clinomxx, ";
      $qPedidoDet .= "$cAlfa.lpde$cAnio.*, ";
      $qPedidoDet .= "$cAlfa.lpar0011.sersapxx, ";
      $qPedidoDet .= "$cAlfa.lpar0011.serdesxx, ";
      $qPedidoDet .= "$cAlfa.lpar0012.subidxxx, ";
      $qPedidoDet .= "$cAlfa.lpar0012.subdesxx ";
      $qPedidoDet .= "FROM $cAlfa.lpde$cAnio ";
      $qPedidoDet .= "LEFT JOIN $cAlfa.lpca$cAnio ON $cAlfa.lpde$cAnio.pedidxxx = $cAlfa.lpca$cAnio.pedidxxx ";
      $qPedidoDet .= "LEFT JOIN $cAlfa.lpar0011 ON $cAlfa.lpde$cAnio.sersapxx = $cAlfa.lpar0011.sersapxx ";
      $qPedidoDet .= "LEFT JOIN $cAlfa.lpar0012 ON $cAlfa.lpde$cAnio.sersapxx = $cAlfa.lpar0012.sersapxx AND $cAlfa.lpde$cAnio.subidxxx = $cAlfa.lpar0012.subidxxx ";
      $qPedidoDet .= "LEFT JOIN $cAlfa.lpar0150 ON $cAlfa.lpca$cAnio.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
      $qPedidoDet .= "WHERE ";
      $qPedidoDet .= "$cAlfa.lpde$cAnio.pedidxxx = $vPedIds";
      $xPedidoDet  = f_MySql("SELECT","",$qPedidoDet,$xConexion01,"");
  
      // Consulta para obtener los registros guardados en la tabla lpar0160
      $qLpar0161  = "SELECT ";
      $qLpar0161 .= "$cAlfa.lpar0161.sersapxx, ";
      $qLpar0161 .= "$cAlfa.lpar0161.subidxxx, ";
      $qLpar0161 .= "$cAlfa.lpar0161.pedidxxx ";
      $qLpar0161 .= "FROM $cAlfa.lpar0161 ";
      $qLpar0161 .= "WHERE $cAlfa.lpar0161.pedidxxx = $vPedIds";
      $xLpar0161  = f_MySql("SELECT","",$qLpar0161,$xConexion01,"");
  
      // Crear un array para almacenar los resultados de lpar0161
      $lpar0161Data = [];
      while ($row = mysql_fetch_array($xLpar0161)) {
          $lpar0161Data[] = $row;
      }

      // Almacena los IDs de los checkboxes a marcar en un array
      $checkboxIdsToCheck = [];
      while ($xRT = mysql_fetch_array($xPedidoDet)) {
        foreach ($lpar0161Data as $lparRow) {
          if ($xRT['subidxxx'] == $lparRow['subidxxx']) {
            $checkboxIdsToCheck[] = $xRT['subidxxx'].'~'.$xRT['sersapxx'];
          }
        }
      }

      ?>
      <script>
        window.onload = function() {
          <?php
          foreach ($checkboxIdsToCheck as $checkboxId) {
          ?>
            var checkbox = document.getElementById('<?php echo $checkboxId; ?>');
            if (checkbox) {
              checkbox.checked = true;
            }
          <?php
          }
          ?>
        };
      </script>
      <?php
    }
    ?>
  </body>
</html>

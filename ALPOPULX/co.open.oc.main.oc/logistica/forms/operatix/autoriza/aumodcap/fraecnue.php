<?php
/**
   * Proceso Autorizacion Excluir Conceptos de Cobro
   * --- Descripcion: Permite Crear un Nueva autorizacion para Excluir conceptos de Cobro para Facturacion.
   * @author Yulieth Campos <ycampos@opentecnologia.com.co>
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
    
      function f_Marca() {//Marca y Desmarca los registros seleccionados en la tabla de Conceptos de Cobro
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

      function f_Carga_Data() { //Arma cadena para guardar en campo matriz de la sys00121
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
      
      function f_Links(xLink,xSwitch) {
        var nX    = screen.width;
        var nY    = screen.height;
        switch (xLink) {
          case "cTerIdInt":
          case "cTerNomInt":
            if (xSwitch == "VALID") {
              var cRuta = "fraecint.php?gModo="+xSwitch+"&gFunction="+xLink+
                                        "&gTerId="+document.forms['frgrm']['cCliId'].value+
                                        "&gTerIdInt="+document.forms['frgrm'][xLink].value;
              // alert(cRuta);
              parent.fmpro.location = cRuta;
            } else {
              var nNx      = (nX-600)/2;
              var nNy      = (nY-250)/2;
              var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
              var cRuta = "fraecint.php?gModo="+xSwitch+"&gFunction="+xLink+
                                        "&gTerId="+document.forms['frgrm']['cCliId'].value+
                                        "&gTerIdInt="+document.forms['frgrm'][xLink].value;
              cWindow = window.open(cRuta,xLink,cWinOpt);
              cWindow.focus();
            }
          break;
        }
      }
      
      function f_CargarTarifasFacturaA(){
        document.forms['frgrm'].target = 'fmwork';
        document.forms['frgrm'].action = 'fraecnue.php';
        document.forms['frgrm'].submit();
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
              <form name = 'frgrm' action = 'fraecgra.php' method = 'post' target='fmpro'>
                <input type = "hidden" name = "cStep"      value = "<?php echo $_POST['cStep'] ?>">
                <input type = "hidden" name = "cTarExc"    value = "">
                <input type = "hidden" name = "nRecords"   value = "<?php echo $_POST['nRecords'] ?>">
                <input type = "hidden" name = "gCliId">
                <input type = "hidden" name = "gSucId">
                <input type = "hidden" name = "gDocSuf">
                <input type = "hidden" name = "cCcAplFa">
                <input type = "hidden" name = "nTimesSave" value = "0">
                <textarea name = "cComMemo"  id = "cComMemo"><?php  echo $_POST['cComMemo'] ?></textarea>
                <script languaje = "javascript">
                  document.getElementById("cComMemo").style.display ="none";
                </script>
                <center>
                <fieldset id="Grid_Paso1">
                  <legend>Datos Pedido</legend>
                  <table border = '0' cellpadding = '0' cellspacing = '0' width='560'>
                    <?php $nCol = f_Format_Cols(28);
                    echo $nCol;?>
                    <tr>
                      <td class = "name" colspan = "2">Nit<br>
                        <input type = "text" class = "letra" name = "" style = "width:40" readonly>
                      </td>
                      <td class = "name" colspan = "5">Cliente<br>
                        <input type = "text" class = "letra" style = "width:100;text-align:left" name = ""  readonly>
                      </td>
                      <td class = "name" colspan = "2">Pedido<br>
                        <input type = "text" class = "letra" name = "" style = "width:40" readonly>
                      </td>
                    </tr>
                  </table>
                  <table border="0" cellspacing="0" cellpadding="0" width="560" id="tblFacA">
                    <?php $cCols = f_Format_Cols(28); echo $cCols; ?>
                    <tr>
                      <td Class = "name" colspan = "5">Facturar a:<br>
                        <input type = "text" Class = "letra" style = "width:100" name = "cTerIdInt" 
                          onFocus="javascript:document.forms['frgrm']['cTerIdInt'].value   = '';
                                              document.forms['frgrm']['cTerDVInt'].value   = '';
                                              document.forms['frgrm']['cTerNomInt'].value  = '';"
                          onBlur = "javascript:f_Links('cTerIdInt','VALID');">
                      </td>
                      <td Class = "name" colspan = "1"><br>
                        <input type = "text" Class = "letra" style = "width:20;text-align:center" name = "cTerDVInt" readonly>
                      </td>
                      <td Class = "name" colspan = "22"><br>
                        <input type = "text" Class = "letra" style = "width:440" name = "cTerNomInt" readonly>
                      </td>
                      <tr>
                        <td colspan="28">Nota: El Cliente tiene parametrizada en su Condici&oacute;n Comercial la opci&oacute;n "Aplicar tarifas del Facturar a".</td>
                      </tr>
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
                  default: //No hace nada 
                  break;
                }
          
                ##Traigo Datos Adicionales del Do ##
                $qTramite  = "SELECT * ";
                $qTramite .= "FROM $cAlfa.sys00121 ";
                $qTramite .= "WHERE ";
                $qTramite .= "$cAlfa.sys00121.sucidxxx = \"{$_POST['cSucId']}\" AND ";
                $qTramite .= "$cAlfa.sys00121.docidxxx = \"{$_POST['cDocId']}\" AND ";
                $qTramite .= "$cAlfa.sys00121.docsufxx = \"{$_POST['cDocSuf']}\" ";
                $xTramite  = f_MySql("SELECT","",$qTramite,$xConexion01,"");
                if (mysql_num_rows($xTramite) == 1) {
                  $vTramite = mysql_fetch_array($xTramite);
                }
                
                $vTramite['tarclixx'] = $vTramite['cliidxxx'];
                $vTramite['tartipxx'] = "CLIENTE";
                    
                // Verifica si tiene asociacion por grupo
                $qConCom  = "SELECT gtaidxxx, cccaplfa ";
                $qConCom .= "FROM $cAlfa.fpar0151 ";
                $qConCom .= "WHERE ";
                $qConCom .= "cliidxxx = \"{$vTramite['cliidxxx']}\" AND  ";
                $qConCom .= "regestxx = \"ACTIVO\" LIMIT 0,1";
                $xConCom  = f_MySql("SELECT","",$qConCom,$xConexion01,"");
                      
                if (mysql_num_rows($xConCom) > 0) {
                  $xRCC = mysql_fetch_array($xConCom);
                  $cCcAplFa = $xRCC['cccaplfa'];
                  if ($xRCC['gtaidxxx'] <> "") {
                    $vTramite['tarclixx'] = $xRCC['gtaidxxx'];
                    $vTramite['tartipxx'] = "GRUPO";
                  }
                }
                
                $_POST['cTerIdInt'] = ($_POST['cTerIdInt'] != "") ? $_POST['cTerIdInt'] : (($vTramite['docfaexc'] != "") ? $vTramite['docfaexc'] : $vTramite['cliidxxx']) ;
                
                if ($cCcAplFa == "SI") {
                  
                  $vTramite['tarclixx'] = $_POST['cTerIdInt'];
                  $vTramite['tartipxx'] = "CLIENTE";
                
                  $qConCom  = "SELECT gtaidxxx ";
                  $qConCom .= "FROM $cAlfa.fpar0151 ";
                  $qConCom .= "WHERE ";
                  $qConCom .= "cliidxxx = \"{$vTramite['tarclixx']}\" AND  ";
                  $qConCom .= "regestxx = \"ACTIVO\" LIMIT 0,1";
                  $xConCom  = f_MySql("SELECT","",$qConCom,$xConexion01,"");
                        
                  if (mysql_num_rows($xConCom) > 0) {
                    $xRCC = mysql_fetch_array($xConCom);
                    if ($xRCC['gtaidxxx'] <> "") {
                      $vTramite['tarclixx'] = $xRCC['gtaidxxx'];
                      $vTramite['tartipxx'] = "GRUPO";
                    }
                  }
                }
                      
                ##Fin Traigo Datos Adicionales del Do ##
                ##Traigo Tarifas parametrizadas al cliente para excluir Conceptos de Cobro al momento de facturar##
                $qTarifas  = "SELECT ";
                $qTarifas .= "$cAlfa.fpar0131.seridxxx, ";
                $qTarifas .= "IF($cAlfa.fpar0129.serdespx != \"\", $cAlfa.fpar0129.serdespx,$cAlfa.fpar0129.serdesxx) AS serdesxx, ";
                $qTarifas .= "$cAlfa.fpar0131.fcotptxx, ";
                $qTarifas .= "$cAlfa.fpar0131.fcotpixx  ";
                $qTarifas .= "FROM $cAlfa.fpar0131 ";
                $qTarifas .= "LEFT JOIN $cAlfa.fpar0129 ON $cAlfa.fpar0131.seridxxx = $cAlfa.fpar0129.seridxxx ";
                $qTarifas .= "WHERE ";
                $qTarifas .= "$cAlfa.fpar0131.cliidxxx = \"{$vTramite['tarclixx']}\" AND ";
                $qTarifas .= "$cAlfa.fpar0131.fcotptxx = \"{$vTramite['doctepxx']}\" AND ";
                $qTarifas .= "$cAlfa.fpar0131.fcotpixx = \"{$vTramite['doctepid']}\" AND ";
                $qTarifas .= "$cAlfa.fpar0131.sucidxxx LIKE \"%{$vTramite['sucidxxx']}%\" AND ";
                $qTarifas .= "$cAlfa.fpar0131.fcotopxx LIKE \"%{$vTramite['doctipxx']}%\" AND ";
                $qTarifas .= "$cAlfa.fpar0131.fcomtrxx LIKE \"%{$vTramite['docmtrxx']}%\" AND ";
                $qTarifas .= "$cAlfa.fpar0131.tartipxx = \"{$vTramite['tartipxx']}\"      AND ";
                $qTarifas .= "$cAlfa.fpar0131.regestxx = \"ACTIVO\" ";
                $xTarifas  = f_MySql("SELECT","",$qTarifas,$xConexion01,"");
                // f_Mensaje(__FILE__, __LINE__, $qTarifas."~".mysql_num_rows($xTarifas));
                ?>
                <script type="text/javascript">
                  document.forms['frgrm']['cDocId'].readOnly = true;
                  document.forms['frgrm']['cDocId'].onblur = "";
                  document.forms['frgrm']['cDocId'].onfocus = "";
                </script>
                <?php 
                if(mysql_num_rows($xTarifas) > 0){
                  ?>
                  <fieldset id="Tarifas">
                    <legend>Servicios a Modificar</legend>
                      <center>
                        <table border = "0" cellpadding = "0" cellspacing = "0" width = "560">
                          <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
                            <td class = "clase08" width = "040" style="padding-left:5px;padding-right:5px" align = "center">Cod SAP</td>
                            <td class = "clase08" width = "400" style="padding-left:5px;padding-right:5px" align = "center">Servicio</td>
                            <td class = "clase08" width = "100" style="padding-left:5px;padding-right:5px" align = "center">Subservicio</td>
                            <td class = "clase08" width = "020" style="padding-left:5px;padding-right:5px" align = "center"><input type="checkbox" name="nCheckAll" onClick = "javascript:f_Marca()"></td>
                          </tr>
                          <script languaje="javascript">
                            document.forms['frgrm']['nRecords'].value = "<?php echo mysql_num_rows($xTarifas) ?>";
                          </script>
                          <?php 
                            $y=0;
                            while ($xRT = mysql_fetch_array($xTarifas)) {
                              ?>
                              <tr>
                                <td bgcolor = "<?php echo $vSysStr['system_row_impar_color_ini'] ?>" class = "letra7" style="padding-left:5px;padding-right:2px;border:1px solid #E6E6E6" align="center"><?php echo $xRT['seridxxx'] ?></td>
                                <td bgcolor = "<?php echo $vSysStr['system_row_impar_color_ini'] ?>" class = "letra7" style="padding-left:5px;padding-right:2px;border:1px solid #E6E6E6"><?php echo $xRT['serdesxx'] ?></td>
                                <td bgcolor = "<?php echo $vSysStr['system_row_impar_color_ini'] ?>" class = "letra7" style="padding-left:5px;padding-right:2px;border:1px solid #E6E6E6" align="center"><?php echo $xRT['fcotptxx'] ?></td>
                                <td bgcolor = "<?php echo $vSysStr['system_row_impar_color_ini'] ?>" class = "letra7" style="padding-left:5px;padding-right:2px;border:1px solid #E6E6E6" align="center">
                                  <input type="checkbox" name="cCheck"  
                                        value = "<?php echo mysql_num_rows($xTarifas) ?>"
                                        id="<?php echo $xRT['fcotptxx']."~".$xRT['fcotpixx']."~".$xRT['seridxxx'] ?>">
                                </td>  
                              </tr>
                            <?php $y++;
                            }//while ($xRT = mysql_fetch_array($xTarifas)) { 
                          ?>
                      </table>
                    </center>
                  </fieldset>
                <?php }else{//if(mysql_num_rows($xTarifas) > 0){
                f_Mensaje(__FILE__,__LINE__,"No hay Tarifas Parametrizadas para el Do {$_POST['cSucId']} - {$_POST['cDocId']} - {$_POST['cDocSuf']}");
                }
                ##Fin Traigo Tarifas parametrizadas al cliente para excluir Conceptos de Cobro al momento de facturar##
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
                                        document.forms['frgrm'].action='fraecgra.php';
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
    <!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
    <?php
    switch ($_COOKIE['kModo']) {
      case "EDITAR":
        f_CargaData($gSucId,$gDocId,$gDocSuf,$_POST['cTerIdInt']); 
        ?>
        <script languaje = "javascript">
          document.forms['frgrm']['cDocId'].readOnly = true;
          document.forms['frgrm']['cDocId'].onblur = "";
          document.forms['frgrm']['cDocId'].onfocus = "";
        </script>
      <?php break;
      case "VER":
        f_CargaData($gSucId,$gDocId,$gDocSuf,$_POST['cTerIdInt']); 
        ?>
        <script languaje = "javascript">
          document.forms['frgrm']['cDocId'].readOnly = true;
          document.forms['frgrm']['cDocId'].onblur = "";
          document.forms['frgrm']['cDocId'].onfocus = "";
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
    function f_CargaData($gSucId,$gDocId,$gDocSuf,$xTerIdInt) {

      global $xConexion01; global $cAlfa;
      ## Traigo Datos Proyecto por Cliente ##
      $qTarifas  = "SELECT $cAlfa.sys00121.*, ";
      $qTarifas .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)) AS clinomxx ";
      $qTarifas .= "FROM $cAlfa.sys00121 ";
      $qTarifas .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.sys00121.cliidxxx = $cAlfa.SIAI0150.CLIIDXXX ";
      $qTarifas .= "WHERE ";
      $qTarifas .= "$cAlfa.sys00121.docidxxx = \"$gDocId\" AND ";
      $qTarifas .= "$cAlfa.sys00121.docsufxx = \"$gDocSuf\" AND ";
      $qTarifas .= "$cAlfa.sys00121.sucidxxx = \"$gSucId\" LIMIT 0,1 ";
      
      $xTarifas  = f_MySql("SELECT","",$qTarifas,$xConexion01,"");
      $vTarifas = mysql_fetch_array($xTarifas);
      
      $qFacA  = "SELECT ";
      $qFacA .= "$cAlfa.SIAI0150.*, ";
      $qFacA .= "IF($cAfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
      $qFacA .= "FROM $cAlfa.SIAI0150 ";
      $qFacA .= "WHERE ";
      $qFacA .= "$cAlfa.SIAI0150.CLIIDXXX = \"$xTerIdInt\" AND ";
      $qFacA .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" LIMIT 0,1";
      $xFacA  = f_MySql("SELECT","",$qFacA,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qFacA."~".mysql_num_rows($xFacA));
      $xRFA = mysql_fetch_array($xFacA);
      
      ##Cargo Matriz con Valores de Conceptos Excluidos para Facturacion ##
      $vTarExc = f_Explode_Array($vTarifas['doctexxx'],"|","~");
    
      ##Fin Cargo Matriz con Valores de Conceptos Excluidos para Facturacion ##
      ?>
      <script language = "javascript">
        document.forms['frgrm']['cSucId'].value    = "<?php echo $vTarifas['sucidxxx'] ?>";
        document.forms['frgrm']['cDocId'].value    = "<?php echo $vTarifas['docidxxx'] ?>";
        document.forms['frgrm']['cDocSuf'].value   = "<?php echo $vTarifas['docsufxx'] ?>";
        document.forms['frgrm']['cDocTip'].value   = "<?php echo $vTarifas['doctipxx'] ?>";
        document.forms['frgrm']['cCliId'].value    = "<?php echo $vTarifas['cliidxxx'] ?>";
        document.forms['frgrm']['cCliNom'].value   = "<?php echo $vTarifas['clinomxx'] ?>";
        document.forms['frgrm']['cTarExc'].value   = "<?php echo $vTarifas['doctexxx'] ?>";
        document.forms['frgrm']['cComMemo'].value  = "<?php echo $vTarifas['doctexxx'] ?>";
        
        document.forms['frgrm']['gDocId'].value  = "<?php echo $vTarifas['docidxxx'] ?>";
        document.forms['frgrm']['gSucId'].value  = "<?php echo $vTarifas['sucidxxx'] ?>";
        document.forms['frgrm']['gDocSuf'].value = "<?php echo $vTarifas['docsufxx'] ?>";
        
        document.getElementById('tblFacA').style.display= "none";
        document.forms['frgrm']['cTerIdInt'].value   = "<?php echo $xRFA['CLIIDXXX'] ?>";
        document.forms['frgrm']['cTerDVInt'].value   = "<?php echo f_Digito_Verificacion($xRFA['CLIIDXXX']) ?>";
        document.forms['frgrm']['cTerNomInt'].value  = "<?php echo $xRFA['CLINOMXX'] ?>";
        
      </script>
      <?php
      $vTarifas['tarclixx'] = $vTarifas['cliidxxx'];
      $vTarifas['tartipxx'] = "CLIENTE";
                
      // Verifica si tiene asociacion por grupo
      $qConCom  = "SELECT gtaidxxx, cccaplfa ";
      $qConCom .= "FROM $cAlfa.fpar0151 ";
      $qConCom .= "WHERE ";
      $qConCom .= "cliidxxx = \"{$vTarifas['cliidxxx']}\" AND  ";
      $qConCom .= "regestxx = \"ACTIVO\" LIMIT 0,1";
      $xConCom  = f_MySql("SELECT","",$qConCom,$xConexion01,"");
      if (mysql_num_rows($xConCom) > 0) {
        $xRCC = mysql_fetch_array($xConCom);
        $cCcAplFa = $xRCC['cccaplfa'];
        if ($xRCC['gtaidxxx'] <> "") {
          $vTarifas['tarclixx'] = $xRCC['gtaidxxx'];
          $vTarifas['tartipxx'] = "GRUPO";
        }
      }
      
      if ($cCcAplFa == "SI") { ?>
        <script language = "javascript">
          document.getElementById('tblFacA').style.display= "block";
          document.forms['frgrm']['cCcAplFa'].value       = "<?php echo $cCcAplFa ?>";
        </script>
        <?php
        /**
         * Busco las condiciones comerciales del cliente
         */
        $vTarifas['tarclixx'] = $xTerIdInt;
        $vTarifas['tartipxx'] = "CLIENTE";
        
        $qConCom  = "SELECT gtaidxxx, cccaplfa ";
        $qConCom .= "FROM $cAlfa.fpar0151 ";
        $qConCom .= "WHERE ";
        $qConCom .= "cliidxxx = \"$xTerIdInt\" AND  ";
        $qConCom .= "regestxx = \"ACTIVO\" LIMIT 0,1";
        $xConCom  = f_MySql("SELECT","",$qConCom,$xConexion01,"");
        // f_Mensaje(__FILE__,__LINE__,"Factuar a ->".$qConCom." ~ ".mysql_num_rows($xConCom));
        if (mysql_num_rows($xConCom) > 0) {
          $xRCC = mysql_fetch_array($xConCom);
          if ($xRCC['gtaidxxx'] <> "") {
            $vTarifas['tarclixx'] = $xRCC['gtaidxxx'];
            $vTarifas['tartipxx'] = "GRUPO";  
          }
        }
      } else { ?>
        <script language = "javascript">
          document.forms['frgrm']['cTerIdInt'].value   = "";
          document.forms['frgrm']['cTerDVInt'].value   = "";
          document.forms['frgrm']['cTerNomInt'].value  = "";
          document.forms['frgrm']['cCcAplFa'].value    = "";
        </script>
      <?php }
      

      $qTarifas  = "SELECT ";
      $qTarifas .= "$cAlfa.fpar0131.seridxxx, ";
      $qTarifas .= "$cAlfa.fpar0129.serdesxx, ";
      $qTarifas .= "$cAlfa.fpar0131.fcotptxx, ";
      $qTarifas .= "$cAlfa.fpar0131.fcotpixx  ";
      $qTarifas .= "FROM $cAlfa.fpar0131 ";
      $qTarifas .= "LEFT JOIN $cAlfa.fpar0129 ON $cAlfa.fpar0131.seridxxx = $cAlfa.fpar0129.seridxxx ";
      $qTarifas .= "WHERE ";
      $qTarifas .= "$cAlfa.fpar0131.cliidxxx = \"{$vTarifas['tarclixx']}\" AND ";
      $qTarifas .= "$cAlfa.fpar0131.fcotptxx = \"{$vTarifas['doctepxx']}\" AND ";
      $qTarifas .= "$cAlfa.fpar0131.fcotpixx = \"{$vTarifas['doctepid']}\" AND ";
      $qTarifas .= "$cAlfa.fpar0131.sucidxxx LIKE \"%{$vTarifas['sucidxxx']}%\" AND ";
      $qTarifas .= "$cAlfa.fpar0131.fcotopxx LIKE \"%{$vTarifas['doctipxx']}%\" AND ";
      $qTarifas .= "$cAlfa.fpar0131.fcomtrxx LIKE \"%{$vTarifas['docmtrxx']}%\" AND ";
      $qTarifas .= "$cAlfa.fpar0131.tartipxx = \"{$vTarifas['tartipxx']}\"      AND ";
      $qTarifas .= "$cAlfa.fpar0131.regestxx = \"ACTIVO\" ";
      $xTarifas  = f_MySql("SELECT","",$qTarifas,$xConexion01,"");
      while($xRT = mysql_fetch_array($xTarifas)){
        for($i=0;$i<count($vTarExc);$i++){//Exploto campo donde se guardan conceptos excluidos para facturacion.
          if($vTarExc[$i][0] <> ""){
            if($vTarExc[$i][0] == $xRT['fcotptxx'] && $vTarExc[$i][2] == $xRT['seridxxx']){
              if(mysql_num_rows($xTarifas) == 1){ ?>
              <script>
                document.forms['frgrm']['cCheck'].checked=true;
              </script>
              <?php } else { ?>
              <script>
                if (document.getElementById('<?php echo $xRT['fcotptxx']."~".$xRT['fcotpixx']."~".$xRT['seridxxx'] ?>')) {
                  document.getElementById('<?php echo $xRT['fcotptxx']."~".$xRT['fcotpixx']."~".$xRT['seridxxx'] ?>').checked = true;
                }
              </script>
              <?php }
              }//if($vTarExc[$i][0] == $xRT['fcotptxx'] && $vTarExc[$i][2] == $xRT['seridxxx']){
            }//if($vTarExc[$i][0] <> ""){
        }//for($i=0;$i<count($vTarExc);$i++){//Exploto campo donde se guardan conceptos excluidos para facturacion.
      }//while($xRT = mysql_fetch_array($xTarifas)){
    }//fin Funcion f_Carga_Data
    ?>
  </body>
</html>

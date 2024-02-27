<?php
	include("../../../../libs/php/utility.php");
  include("../../../../libs/php/uticoesf.php");
	if ($gModo != "") { $_COOKIE['kModo'] = $gModo; } // La variable $gModo viene difernete de vacio cuando este PHP es llamado desde el paso de la factura.
?>
<html>
	<head>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
		<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
		<script language="javascript">
      function f_Prende_Check(xCondi){
        //alert(xCondi);
        switch (xCondi) {
          case "CAG":
            if(document.forms['frgrm']['DocCagSi'].checked==true) {
              document.forms['frgrm']['DocC20No'].checked=true;
              document.forms['frgrm']['DocC20Si'].checked=false;
              document.forms['frgrm']['oDocC20'].value="NO";
              document.forms['frgrm']['cDocC20'].value="";
              document.getElementById('cDocC20').style.display= "none";
              document.forms['frgrm']['DocC40No'].checked=true;
              document.forms['frgrm']['DocC40Si'].checked=false;
              document.forms['frgrm']['oDocC40'].value="NO";
              document.forms['frgrm']['cDocC40'].value="";
              document.getElementById('cDocC40').style.display= "none";
              document.forms['frgrm']['DocCsNo'].checked=true;
              document.forms['frgrm']['DocCsSi'].checked=false;
              document.forms['frgrm']['oDocCs'].value="NO";
              document.forms['frgrm']['DocCsuNo'].checked=true;
              document.forms['frgrm']['DocCsuSi'].checked=false;
              document.forms['frgrm']['oDocCsu'].value="NO";
              document.forms['frgrm']['cDocCsu'].value="";
              document.getElementById('cDocCsu').style.display= "none";
            }
          break;
          case "C20":
          case "C40":
            if(document.forms['frgrm']['DocC20Si'].checked==true ||
                document.forms['frgrm']['DocC40Si'].checked==true) {
              document.forms['frgrm']['DocCagNo'].checked=true;
              document.forms['frgrm']['DocCagSi'].checked=false;
              document.forms['frgrm']['oDocCag'].value="NO";
              document.forms['frgrm']['DocCsNo'].checked=true;
              document.forms['frgrm']['DocCsSi'].checked=false;
              document.forms['frgrm']['oDocCs'].value="NO";
              document.forms['frgrm']['DocCsuNo'].checked=true;
              document.forms['frgrm']['DocCsuSi'].checked=false;
              document.forms['frgrm']['oDocCsu'].value="NO";
              document.forms['frgrm']['cDocCsu'].value="";
              document.getElementById('cDocCsu').style.display= "none";
            }
          break;
          case "CS":
            if(document.forms['frgrm']['DocCsSi'].checked==true ) {
              document.forms['frgrm']['DocC20No'].checked=true;
              document.forms['frgrm']['DocC20Si'].checked=false;
              document.forms['frgrm']['oDocC20'].value="NO";
              document.forms['frgrm']['cDocC20'].value="";
              document.getElementById('cDocC20').style.display= "none";
              document.forms['frgrm']['DocC40No'].checked=true;
              document.forms['frgrm']['DocC40Si'].checked=false;
              document.forms['frgrm']['oDocC40'].value="NO";
              document.forms['frgrm']['cDocC40'].value="";
              document.getElementById('cDocC40').style.display= "none";
              document.forms['frgrm']['DocCsuNo'].checked=false;
              document.forms['frgrm']['DocCsuSi'].checked=false;
              document.forms['frgrm']['oDocCsu'].value="";
              document.forms['frgrm']['cDocCsu'].value="";
              document.getElementById('cDocCsu').style.display= "none";
            }
          break;
          case "CSU":
            if(document.forms['frgrm']['DocCsuSi'].checked==true ) {
              document.forms['frgrm']['DocCagNo'].checked=true;
              document.forms['frgrm']['DocCagSi'].checked=false;
              document.forms['frgrm']['oDocCag'].value="NO";
              document.forms['frgrm']['DocC20No'].checked=true;
              document.forms['frgrm']['DocC20Si'].checked=false;
              document.forms['frgrm']['oDocC20'].value="NO";
              document.forms['frgrm']['cDocC20'].value="";
              document.getElementById('cDocC20').style.display= "none";
              document.forms['frgrm']['DocC40No'].checked=true;
              document.forms['frgrm']['DocC40Si'].checked=false;
              document.forms['frgrm']['oDocC40'].value="NO";
              document.forms['frgrm']['cDocC40'].value="";
              document.getElementById('cDocC40').style.display= "none";
            }
          break;
        }
      }

      function f_Verificar(xIdCond){
        if(document.getElementById('c'+xIdCond)){
          if(document.forms['frgrm'][xIdCond+'Si'].checked==true){
            document.getElementById('c'+xIdCond).style.display= "block";
          }
          if(document.forms['frgrm'][xIdCond+'Si'].checked==false){
            document.getElementById('c'+xIdCond).style.display= "none";
            document.forms['frgrm']['c'+xIdCond].value="";
          }
        }
      }

      function f_Inicia(){
        for (x=0;x<document.forms['frgrm'].elements.length;x++) {
          if (document.forms['frgrm'].elements[x].type == 'radio') {
            var cNomCam = document.forms['frgrm'].elements[x].name.substring(1,document.forms['frgrm'].elements[x].name.length);
            if(document.forms['frgrm'].elements[x].id == (cNomCam+"Si")) {
              document.forms['frgrm'][cNomCam+'Si'].checked=false;
              document.forms['frgrm'][cNomCam+'No'].checked=false;
              if (document.getElementById('c'+cNomCam)) {
                document.getElementById('c'+cNomCam).style.display= "none";
                document.forms['frgrm']['c'+cNomCam].value="";
              }
            }
          }
        }
      }

      function f_Imprimir() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        var nX      = screen.width;
        var nY      = screen.height;
        var nNx     = (nX-900)/2;
        var nNy     = (nY-500)/2;
        var cWinPro = "width=900,scrollbars=1,height=500,left="+nNx+",top="+nNy;
        if(document.forms['frgrm']['cSucId'].value.length > 0 &&
            document.forms['frgrm']['cDocTip'].value.length > 0 &&
            document.forms['frgrm']['cDocId'].value.length > 0){
          var cRuta = "frmdoprn.php?gSucId="+document.forms['frgrm']['cSucId'].value+
                                  "&gDocTip="+document.forms['frgrm']['cDocTip'].value+
                                  "&gDocId="+document.forms['frgrm']['cDocId'].value;
          zWindow = window.open(cRuta,'zWinTrp',cWinPro);
        } else {
          alert("El Numero del DO esta Vacio, Verifique");
        }
      }

      function f_Links(xLink,xSwitch) {
        var nX    = screen.width;
        var nY    = screen.height;
        switch (xLink) {
          case "cDocId":
            if (xSwitch == "VALID") {
              var cRuta  = "frcde121.php?gWhat=VALID&gFunction=cDocId&gDocNro="+document.forms['frgrm']['cDocId'].value.toUpperCase();
              parent.fmpro.location = cRuta;
            } else {

              var nNx     = (nX-400)/2;
              var nNy     = (nY-250)/2;
              var cWinPro = 'width=400,scrollbars=1,height=250,left='+nNx+',top='+nNy;
              var cRuta  = "frcde121.php?gWhat=WINDOW&gFunction=cDocId&gDocNro="+document.forms['frgrm']['cDocId'].value.toUpperCase();
              zWindow = window.open(cRuta,"zWindow",cWinPro);
              zWindow.focus();
            }
          break;
          case "cTerIdInt":
          case "cTerNomInt":
            if (xSwitch == "VALID") {
              var cRuta = "frcdtint.php?gModo="+xSwitch+"&gFunction="+xLink+
                                        "&gTerId="+document.forms['frgrm']['cCliId'].value+
                                        "&gTerIdInt="+document.forms['frgrm'][xLink].value;
              //alert(cRuta);
              parent.fmpro.location = cRuta;
            } else {
              var nNx      = (nX-600)/2;
              var nNy      = (nY-250)/2;
              var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
              var cRuta = "frcdtint.php?gModo="+xSwitch+"&gFunction="+xLink+
                                         "&gTerId="+document.forms['frgrm']['cCliId'].value+
                                         "&gTerIdInt="+document.forms['frgrm'][xLink].value;
              cWindow = window.open(cRuta,xLink,cWinOpt);
              cWindow.focus();
            }
          break;
        }
      }

      function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
      }

      function f_Guardar(){
        document.forms['frgrm'].target = 'fmpro';
        document.forms['frgrm'].action = 'frcdtgra.php';
        document.forms['frgrm'].submit();
      }

      function f_CargarTarifasFacturaA(){
        document.forms['frgrm'].target = 'fmwork';
        document.forms['frgrm'].action = 'frcdtnue.php';
        document.forms['frgrm'].submit();
      }
    </script>
  </head>
	<body>
    <form name = "frgrm" action = "frcdtgra.php" method = "post" target="fmpro">
      <input type="hidden" name="cComMemo">
      <input type="hidden" name="cPreAsi">
      <input type="hidden" name="cTarCli">
      <input type="hidden" name="cTipCli">
      <input type="hidden" name="gDoiId">
      <input type="hidden" name="gSucId">
      <input type="hidden" name="gDocSuf">
      <input type="hidden" name="cCcAplFa">
      <input type="hidden" name = "gTipSav" value = "UPDATE">
      <center>
        <table width="700" cellspacing="0" cellpadding="0" border="0"><tr><td>
          <fieldset>
            <legend><?php echo $_COOKIE['kModo'] ?> Condiciones Especiales</legend>
            <table border="0" cellspacing="0" cellpadding="0" width="700">
              <?php $cCols = f_Format_Cols(35); echo $cCols; ?>
              <tr>
                <td Class = "name" colspan = "4">Buscar Do:</td>
                <td Class = "name" colspan = "5">
                  <input type = "text" Class = "letra" name="cDocId" style="width:100" readonly >
                </td>
                <td Class = "name" colspan = "2">
                  <input type = "text" Class = "letra" name="cSucId" style="width:40"  readonly>
                </td>
                <td Class = "name" colspan = "2">
                  <input type = "text" Class = "letra" name="cDocSuf" style="width:40"  readonly>
                </td>
                <td Class = "name" colspan = "4">
                  <input type = "text" Class = "letra" name="cDocTip" style="width:080"  readonly>
                </td>
                <td Class = "name" colspan = "4">
                  <input type = "text" Class = "letra" name="cCliId" style="width:080"  readonly>
                </td>
                <td Class = "name" colspan = "14">
                  <input type = "text" Class = "letra" name="cCliNom" style="width:280"  readonly>
                </td>
              </tr>
            </table>
            <br>
            <table border="0" cellspacing="0" cellpadding="0" width="700" id="tblFacA">
              <?php $cCols = f_Format_Cols(35); echo $cCols; ?>
              <tr>
                <td Class = "name" colspan = "4">Facturar a:</td>
                <td Class = "name" colspan = "5">
                  <input type = "text" Class = "letra" style = "width:100" name = "cTerIdInt" value="<?php echo $_POST['cTerIdInt'] ?>"
                    onFocus="javascript:document.forms['frgrm']['cTerIdInt'].value   = '';
                                        document.forms['frgrm']['cTerDVInt'].value   = '';
                                        document.forms['frgrm']['cTerNomInt'].value  = '';"
                    onBlur = "javascript:f_Links('cTerIdInt','VALID');">
                </td>
                <td Class = "name" colspan = "1">
                  <input type = "text" Class = "letra" style = "width:20;text-align:center" name = "cTerDVInt" value="<?php echo $_POST['cTerDVInt'] ?>" readonly>
                </td>
                <td Class = "name" colspan = "25">
                  <input type = "text" Class = "letra" style = "width:500" name = "cTerNomInt" value="<?php echo $_POST['cTerNomInt'] ?>">
                </td>
                <tr>
                  <td colspan="4">&nbsp;</td>
                  <td colspan="31">Nota: El Cliente tiene parametrizada en su Condici&oacute;n Comercial la opci&oacute;n "Aplicar tarifas del Facturar a".</td>
                </tr>
              </tr>
            </table>
            <br>
            <?php 
            // Variables manejo de errores
            $nSwitch = 0;
            $cMsj    = "";

            // Inicializando clase para el procesamiento de condiciones especiales del DO
            $ObjConEsp = new cConEspFac();

            // Matriz con los nombres de los radio y su descripcion
            // Este metodo retorna una matriz con las siguientes posiciones
            // $mDatos[nombreCampoSys00121]['camcones'] => Nombre radio button
            // $mDatos[nombreCampoSys00121]['camcone2'] => Nombre en el formulario del campo de texto principal (opcional)
            // $mDatos[nombreCampoSys00121]['camcone3'] => Nombre en el formulario del campo de texto adicional (opcional)
            // $mDatos[nombreCampoSys00121]['camcamp3'] => Nombre en la sys00121 del campo de texto adicional (opcional, obligatorio si se asigna camcone3)
            // $mDatos[nombreCampoSys00121]['descones'] => Descripcion por defecto de la condicion especial
            // $mDatos[nombreCampoSys00121]['accionjs'] => Condicon en el metodo f_Prende_Check para realizar acciones sobre los radio en el formulario
            // Para ingresar un nuevo campo, este debe incluirse en el uticoesf, en el metodo fnDescripcionCondicones
            $vDatos['doctipxx'] = "TRANSITO"; //Tipo de operación IMPORTACION, EXPORTACION, TRANSITO u OTROS
            $mReturnConEsp = $ObjConEsp->fnDescripcionCondicones($vDatos);
            if ($mReturnConEsp[0] == "true") {
              $mDatos = $mReturnConEsp[1];
            } else {
              $nSwitch = 1;
              for($nR=2;$nR<count($mReturnConEsp);$nR++){
                $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= $mReturnConEsp[$nR]."\n";
              }
            }
            if ($nSwitch == 1) {
              f_Mensaje(__FILE__,__LINE__,"Error al Consultar las Condiciones Especiales:\n".$cMsj);
            }
            // Fin Matriz con los nombres de los radio y su descripcion
            ?>
            <table border="0" cellspacing="0" cellpadding="0" width="700" align="center" bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>">
              <?php foreach ($mDatos as $cKey => $cValue) { 
                $cId = substr($mDatos[$cKey]['camcones'], 1, strlen($mDatos[$cKey]['camcones'])); ?>
                <tr id ="<?php echo $cKey ?>">
                  <td Class = "clase08" align="left" style="border:#FFFFFF 1px solid;height:25px;width:450px">
                    <?php echo $mDatos[$cKey]['descones'] ?>
                  </td>
                  <td Class = "clase08" align="center" style="border:#FFFFFF 1px solid;height:25px;width:120px">
                    <?php if ($mDatos[$cKey]['accionjs'] != "") { ?>
                      <label><input type="radio" name = "<?php echo $mDatos[$cKey]['camcones'] ?>" id="<?php echo $cId ?>No" value="NO" onclick="javascript:f_Verificar('<?php echo $cId ?>');f_Prende_Check('<?php echo $mDatos[$cKey]['accionjs'] ?>');">NO</label>
                      <label><input type="radio" name = "<?php echo $mDatos[$cKey]['camcones'] ?>" id="<?php echo $cId ?>Si" value="SI" onclick="javascript:f_Verificar('<?php echo $cId ?>');f_Prende_Check('<?php echo $mDatos[$cKey]['accionjs'] ?>');">SI</label>
                    <?php } else {
                      if ($cAlfa == 'DHLEXPRE' || $cAlfa == 'TEDHLEXPRE' || $cAlfa == 'DEDHLEXPRE') {
                    ?>
                      <label><input disabled type="radio" name = "<?php echo $mDatos[$cKey]['camcones'] ?>" id="<?php echo $cId ?>No" value="NO" onclick="javascript:f_Verificar('<?php echo $cId ?>');">NO</label>
                      <label><input disabled type="radio" name = "<?php echo $mDatos[$cKey]['camcones'] ?>" id="<?php echo $cId ?>Si" value="SI" onclick="javascript:f_Verificar('<?php echo $cId ?>');">SI</label>
                    <?php } else { ?>
                      <label><input type="radio" name = "<?php echo $mDatos[$cKey]['camcones'] ?>" id="<?php echo $cId ?>No" value="NO" onclick="javascript:f_Verificar('<?php echo $cId ?>');">NO</label>
                      <label><input type="radio" name = "<?php echo $mDatos[$cKey]['camcones'] ?>" id="<?php echo $cId ?>Si" value="SI" onclick="javascript:f_Verificar('<?php echo $cId ?>');">SI</label>
                    <?php } ?>
                    <? } ?>
                  </td>
                  <td Class = "clase08" align="left" style="border:#FFFFFF 1px solid;height:25px;width:130px">
                    <table border="0" cellspacing="0" cellpadding="0" align="center" style="width:130px;">
                      <tr>
                        <td Class = "clase08">
                        <?php if ($mDatos[$cKey]['camcone2'] != "") { ?>
                          <input type="text" name="<?php echo $mDatos[$cKey]['camcone2'] ?>" id="<?php echo $mDatos[$cKey]['camcone2'] ?>" style="width:60px;text-align:right" maxlength="15">
                        <?php } ?>
                        </td>
                        <td Class = "clase08">
                          <?php if ($mDatos[$cKey]['camcone3'] != "") { ?>
                            <input type="text" name="<?php echo $mDatos[$cKey]['camcone3'] ?>" id="<?php echo $mDatos[$cKey]['camcone3'] ?>" style="width:60px;text-align:right" maxlength="15">
                          <?php } ?>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              <? } ?>
            </table>
          </fieldset>
          <center>
          <?php switch ($_COOKIE['kModo']) {
            case "VER": ?>
              <table border="0" cellpadding="0" cellspacing="0" width="700">
                <tr height="21">
                  <td width="518" height="21"></td>
                  <td width="91" height="21" Class="name" ></td>
                  <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
                </tr>
              </table>
            <?php break;
            case "VER_DESDE_FACTURA": ?>
                <table border="0" cellpadding="0" cellspacing="0" width="700"></table>
            <?php break;
            default: ?>
              <table border="0" cellpadding="0" cellspacing="0" width="700">
                <tr height="21">
                  <td width="518" height="21"></td>
                  <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:hand" onClick = "javascript:f_Guardar()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
                  <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand" onClick = "javascript:f_Retorna()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
                </tr>
              </table>
            <?php break;
          } ?>
        </td></tr></table>
      </center>
    </form>
  </body>
</html>
<script languaje = "javascript">
  f_Inicia();
</script>
<?php
switch ($_COOKIE['kModo']) {
  case "VER":
  case "VER_DESDE_FACTURA":
    f_CargaData($gDoiId,$gSucId,$gDocSuf,$_POST['cTerIdInt']); ?>
    <script languaje = "javascript">
      document.forms['frgrm']['cSucId'].readOnly  = true;
      document.forms['frgrm']['cDocTip'].readOnly = true;
      document.forms['frgrm']['cDocId'].readOnly  = true;
      document.forms['frgrm']['cCliNom'].readOnly = true;
      document.forms['frgrm']['cDocId'].onfocus   = "";
      document.forms['frgrm']['cDocId'].onblur    = "";

      for (x=0;x<document.forms['frgrm'].elements.length;x++) {
        document.forms['frgrm'].elements[x].readOnly = true;
        document.forms['frgrm'].elements[x].onfocus  = "";
        document.forms['frgrm'].elements[x].onblur   = "";
        document.forms['frgrm'].elements[x].disabled = true;
        document.forms['frgrm'].elements[x].style.fontWeight = "bold";
      }
    </script>
  <?php break;
  case "EDITAR":
    f_CargaData($gDoiId,$gSucId,$gDocSuf,$_POST['cTerIdInt']); ?>
  <?php break;
}

function f_CargaData($xDoiId,$xSucId,$xDocSuf,$xTerIdInt) {
  global $xConexion01; global $cAlfa;

  // Inicializando clase condiciones especiales
  $ObjConEsp = new cConEspFac();

  //Datos del DO
  $vDatos['sucidxxx'] = $xSucId;       //Sucursal
  $vDatos['docidxxx'] = $xDoiId;       //Do
  $vDatos['docsufxx'] = $xDocSuf;      //Sufijo
  $vDatos['moduloxx'] = "TRANSITO";    //Modulo
  $mReturnConEspDo = $ObjConEsp->fnCondiconesEspecialesDO($vDatos);
  if ($mReturnConEspDo[0] == "true") {
    $xRDD = $mReturnConEspDo[1];
  }
  ?>
  <script language = "javascript">
    document.forms['frgrm']['cSucId'].value  = "<?php echo $xRDD['sucidxxx'] ?>";
    document.forms['frgrm']['cDocSuf'].value = "<?php echo $xRDD['docsufxx'] ?>";
    document.forms['frgrm']['cDocTip'].value = "<?php echo $xRDD['doctipxx'] ?>";
    document.forms['frgrm']['cDocId'].value  = "<?php echo $xRDD['docidxxx'] ?>";
    document.forms['frgrm']['cCliId'].value  = "<?php echo $xRDD['cliidxxx'] ?>";
    document.forms['frgrm']['cCliNom'].value = "<?php echo $xRDD['clinomxx'] ?>";
    document.forms['frgrm']['cPreAsi'].value = "<?php echo $xRDD['prescinx'] ?>";
    document.forms['frgrm']['gDoiId'].value  = "<?php echo $xRDD['docidxxx'] ?>";
    document.forms['frgrm']['gSucId'].value  = "<?php echo $xRDD['sucidxxx'] ?>";
    document.forms['frgrm']['gDocSuf'].value = "<?php echo $xRDD['docsufxx'] ?>";

    document.getElementById('tblFacA').style.display= "none";

    <?php if ($xRDD['docfacoe'] != "" && $xTerIdInt == "") { ?>
      document.forms['frgrm']['cTerIdInt'].value   = "<?php echo $xRDD['teridint'] ?>";
      document.forms['frgrm']['cTerDVInt'].value   = "<?php echo f_Digito_Verificacion($xRDD['teridint']) ?>";
      document.forms['frgrm']['cTerNomInt'].value  = "<?php echo $xRDD['ternomin'] ?>";
    <?php } ?>
  </script>
  <?php
  //Mostrando solo los campos de condiciones especiales que aplican para ese DO segun su tarifa
  // Busco las condiciones comerciales del cliente
  $cTarCli  = $xRDD['cliidxxx'];
  $cTarTip  = "CLIENTE";
  $cCcAplFa = "";

  $qConCom  = "SELECT gtaidxxx, cccaplfa ";
  $qConCom .= "FROM $cAlfa.fpar0151 ";
  $qConCom .= "WHERE ";
  $qConCom .= "cliidxxx = \"{$xRDD['cliidxxx']}\" AND  ";
  $qConCom .= "regestxx = \"ACTIVO\" LIMIT 0,1";
  $xConCom  = f_MySql("SELECT","",$qConCom,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,"Cliente ->".$qConCom." ~ ".mysql_num_rows($xConCom));
  if (mysql_num_rows($xConCom) > 0) {
    $vRCC = mysql_fetch_array($xConCom);
    $cCcAplFa = $vRCC['cccaplfa'];
    if ($vRCC['gtaidxxx'] != "") {
      $cTarCli = $vRCC['gtaidxxx'];
      $cTarTip = "GRUPO";
    }
  }

  if ($cCcAplFa == "SI") { ?>
    <script language = "javascript">
      document.getElementById('tblFacA').style.display= "block";
    </script>
    <?php
    // Mostrando tarifas del factuar a
    // Si llega el post por el parametro deben buscarse las tarifas del facturar a seleccionado
    // El factuara a solo se muestra si el cliente tiene parametrizado que las tarifas al facturar a
    // Busco las condiciones comerciales del cliente o el facturar a
    $xTerIdInt = ($xTerIdInt == "") ? $xRDD['docfacoe'] : $xTerIdInt;

    $cTarCli  = $xTerIdInt;
    $cTarTip  = "CLIENTE";

    $qConCom  = "SELECT gtaidxxx, cccaplfa ";
    $qConCom .= "FROM $cAlfa.fpar0151 ";
    $qConCom .= "WHERE ";
    $qConCom .= "cliidxxx = \"$xTerIdInt\" AND  ";
    $qConCom .= "regestxx = \"ACTIVO\" LIMIT 0,1";
    $xConCom  = f_MySql("SELECT","",$qConCom,$xConexion01,"");
    // f_Mensaje(__FILE__,__LINE__,"Factuar a ->".$qConCom." ~ ".mysql_num_rows($xConCom));
    if (mysql_num_rows($xConCom) > 0) {
      $vRCC = mysql_fetch_array($xConCom);
      if ($vRCC['gtaidxxx'] != "") {
        $cTarCli = $vRCC['gtaidxxx'];
        $cTarTip = "GRUPO";
      }
    }
  } else { ?>
    <script language = "javascript">
      document.forms['frgrm']['cTerIdInt'].value   = "";
      document.forms['frgrm']['cTerDVInt'].value   = "";
      document.forms['frgrm']['cTerNomInt'].value  = "";
    </script>
  <?php }

  #Matriz con los nombres de los radio y su descripcion
  $vDatos['cliidxxx'] = $xRDD['cliidxxx'];                      //Cliente
  $vDatos['teridint'] = $xTerIdInt;                             //Facturar a
  $vDatos['gtaidxxx'] = ($cTarTip  == "GRUPO") ? $cTarCli : ""; //Grupo Tarifa
  $vDatos['doctepxx'] = $xRDD['doctepxx'];                      //Tarifa por GENERAL, PROYECTO o PRODUCTO
  $vDatos['doctepid'] = $xRDD['doctepid'];                      //Id Proyecto o Producto
  $vDatos['sucidxxx'] = $xRDD['sucidxxx'];                      //Sucursal
  $vDatos['doctipxx'] = $xRDD['doctipxx'];                      //Tipo de operación IMPORTACION, EXPORTACION, TRANSITO u OTROS
  $vDatos['docmtrxx'] = $xRDD['docmtrxx'];                      //Modo de Transporte
  $vDatos['tartipxx'] = $cTarTip;                               //Tipo de Tarifa
  $mReturnConEsp = $ObjConEsp->fnDescripcionCondicones($vDatos);

  if ($mReturnConEsp[0] == "true") {
    $mTabId = $mReturnConEsp[1];
  }

  // Cargando valores 
  foreach ($mTabId as $cKey => $cValue) {
    $cId = substr($mTabId[$cKey]['camcones'], 1, strlen($mTabId[$cKey]['camcones']));
    //Limpiando campos numericos con valor a cero
    $xRDD[$cKey] = ($xRDD[$cKey] == "0.00") ? "" : $xRDD[$cKey];
    if ($mTabId[$cKey]['camcone3'] != "") {
      $xRDD[$mTabId[$cKey]['camcamp3']] = ($xRDD[$mTabId[$cKey]['camcamp3']] == "0.00") ? "" : $xRDD[$mTabId[$cKey]['camcamp3']];
    }
    ?> 
    <script languaje = "javascript">
      <?php if(!empty($xRDD[$cKey]) && $xRDD[$cKey] != "NO") { ?>
        document.forms['frgrm']['<?php echo $cId ?>Si'].checked  = true;
        if ("<?php echo $mTabId[$cKey]['camcone2'] ?>" != "") {
          document.forms['frgrm']['<?php echo $mTabId[$cKey]['camcone2'] ?>'].style.display= "block";
          document.forms['frgrm']['<?php echo $mTabId[$cKey]['camcone2'] ?>'].value="<?php echo $xRDD[$cKey]; ?>";
        }
        if ("<?php echo $mTabId[$cKey]['camcone3'] ?>" != "") {
          document.forms['frgrm']['<?php echo $mTabId[$cKey]['camcone3'] ?>'].style.display= "block";
          document.forms['frgrm']['<?php echo $mTabId[$cKey]['camcone3'] ?>'].value="<?php echo $xRDD[$mTabId[$cKey]['camcamp3']]; ?>";
        }
        <?php
      } elseif($xRDD[$cKey] == "NO") { ?>
        document.forms['frgrm']['<?php echo $cId ?>No'].checked  = true;
        if ("<?php echo $mTabId[$cKey]['camcone2'] ?>" != "") {
          document.forms['frgrm']['<?php echo $mTabId[$cKey]['camcone2'] ?>'].style.display= "none";
          document.forms['frgrm']['<?php echo $mTabId[$cKey]['camcone2'] ?>'].value="";
        }
        if ("<?php echo $mTabId[$cKey]['camcone3'] ?>" != "") {
          document.forms['frgrm']['<?php echo $mTabId[$cKey]['camcone3'] ?>'].style.display= "none";
          document.forms['frgrm']['<?php echo $mTabId[$cKey]['camcone3'] ?>'].value="";
        }
        <?php
      } ?>
    </script>
  <?php }

  #Mostrando solo las filas de las condiciones especiales que aplican para ese do
  foreach ($mTabId as $cKey => $cValue) {
    if($mTabId[$cKey]['mostrarx'] == "SI") {
      ?>
      <script languaje = "javascript">
        if(document.getElementById('<?php echo strtolower($cKey) ?>')){
          document.getElementById('<?php echo strtolower($cKey) ?>').getElementsByTagName('td')[0].innerText = '<?php echo $mTabId[$cKey]['descones'] ?>';
        }
      </script>
      <?php
      #Muestro la fila ?>
      <script languaje = "javascript">
        if(document.getElementById('<?php echo $cKey ?>')){
          document.getElementById('<?php echo $cKey ?>').style.display="block";
        }
      </script>
      <?php 
      if ($cKey == "dochrexx" && $mTabId[$cKey]['moscanho'] == "NO") { 
        $cId = substr($mTabId[$cKey]['camcones'], 1, strlen($mTabId[$cKey]['camcones'])); ?>
        <script languaje = "javascript">
          document.forms['frgrm']['cAplDocHre'].value = "NO";
          if(document.forms['frgrm']['<?php echo $cId?>Si'].checked){
            document.getElementById('<?php echo $mTabId[$cKey]['camcone2']?>').style.display= "none";
            document.forms['frgrm']['<?php echo $mTabId[$cKey]['camcone2']?>'].value=1;
            document.getElementById('<?php echo $mTabId[$cKey]['camcone3']?>').style.display= "none";
            document.forms['frgrm']['<?php echo $mTabId[$cKey]['camcone3']?>'].value=1;
          }
        </script>
      <?php }
    } else {
    #oculto la fila ?>
      <script languaje = "javascript">
        if(document.getElementById('<?php echo $cKey ?>')){
          document.getElementById('<?php echo $cKey ?>').style.display="none";
        }
      </script>
    <?php }
  } ?>

  <script languaje = "javascript">
    document.forms['frgrm']['cTarCli'].value   = "<?php echo $cTarCli; ?>";
    document.forms['frgrm']['cTipCli'].value   = "<?php echo $cTarTip; ?>";
    document.forms['frgrm']['cCcAplFa'].value  = "<?php echo $cCcAplFa; ?>";
  </script>
<?php } ?>

<?php
  namespace openComex;
  /**
   * Formulario para consulta inducida en el traking de Pedido.
   * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
   * @package opencomex
   * @version 001
   */
  include("../../../../../financiero/libs/php/utility.php");

  //  Cookie fija
  $kDf = explode("~",$_COOKIE["kDatosFijos"]);
  $kMysqlHost = $kDf[0];
  $kMysqlUser = $kDf[1];
  $kMysqlPass = $kDf[2];
  $kMysqlDb   = $kDf[3];
  $kUser      = $kDf[4];
  $kLicencia  = $kDf[5];
  $swidth     = $kDf[6];

  if ($cCliId != "") {
    $qCliente  = "SELECT * ";
    $qCliente .= "FROM $cAlfa.lpar0150 ";
    $qCliente .= "WHERE ";
    $qCliente .= "CLIIDXXX = \"$cCliId\" LIMIT 0,1";    
    $xCliente  = f_MySql("SELECT","",$qCliente,$xConexion01,"");
    $vCliente  = mysql_fetch_array($xCliente);
    $cCliDV   = f_Digito_Verificacion($vCliente['CLIIDXXX']);
    $cCliNom  = ($vCliente['CLINOMXX'] != "") ? $vCliente['CLINOMXX'] : trim($vCliente['CLIAPE1X']." ".$vCliente['CLIAPE2X']." ".$vCliente['CLINOM1X']." ".$vCliente['CLINOM2X']);
  }
?>
<html>
  <head>
    <title>Consulta Inducida</title>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
    <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
    <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
    <script language="javascript">
      function fnEnviarDatos(){
        var vDatos = new Array();
        vDatos['cPeriodos']     = document.forms['frgrm']['cPeriodos'].value;
        vDatos['dDesde']        = document.forms['frgrm']['dDesde'].value;
        vDatos['dHasta']        = document.forms['frgrm']['dHasta'].value;
        vDatos['cOfvSap']       = document.forms['frgrm']['cOfvSap'].value;
        vDatos['cUsrId']        = document.forms['frgrm']['cUsrId'].value;
        vDatos['cEstado']       = document.forms['frgrm']['cEstado'].value;
        vDatos['cConsecutivo']  = document.forms['frgrm']['cConsecutivo'].value;
        vDatos['cCerId']        = document.forms['frgrm']['cCerId'].value;
        vDatos['cCliId']        = document.forms['frgrm']['cCliId'].value;
        vDatos['cCliDV']        = document.forms['frgrm']['cCliDV'].value;
        vDatos['cCliNom']       = document.forms['frgrm']['cCliNom'].value;
        vDatos['cDepNum']       = document.forms['frgrm']['cDepNum'].value;
        parent.window.opener.fnEnviarConsultaInducida(vDatos);
        parent.window.close();
      }

      function fnLinks(xLink,xSwitch) {
        var nX = screen.width;
        var nY = screen.height;
        switch (xLink) {
          // Cliente
          case "cCliId":
            if (xSwitch == "VALID") {
              var zRuta  = "frped150.php?gWhat=VALID&gFunction=cCliId&gCliId="+document.forms['frgrm']['cCliId'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (nX-600)/2;
              var zNy     = (nY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frped150.php?gWhat=WINDOW&gFunction=cCliId&gCliId="+document.forms['frgrm']['cCliId'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cCliNom":
            if (xSwitch == "VALID") {
              var zRuta  = "frped150.php?gWhat=VALID"+
                                        "&gFunction=cCliNom"+
                                        "&gCliNom="+document.forms['frgrm']['cCliNom'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (nX-600)/2;
              var zNy     = (nY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frped150.php?gWhat=WINDOW"+
                                        "&gFunction=cCliNom"+
                                        "&gCliNom="+document.forms['frgrm']['cCliNom'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cCerComCsc":
            if(document.forms['frgrm']['cCerComCsc'].value != ""){
              if(document.forms['frgrm']['cCerComCsc'].value.length < 1){
                alert('Debe Digitar al Menos un Digito de la Certificaci&oacute;n');
              }else{
                if (xSwitch == "VALID") {
                  var zRuta = "frlccaxx.php?gModo="+xSwitch+"&gFunction="+xLink+
                                          "&cPerAno="+document.forms['frgrm']['cPerAno'].value +
                                          "&gComCsc="+document.forms['frgrm']['cCerComCsc'].value.toUpperCase();
                  parent.fmpro.location = zRuta;
                } else if (xSwitch == "WINDOW") {
                  var nNx      = (nX-400)/2;
                  var nNy      = (nY-250)/2;
                  var zWinPro  = "width=400,scrollbars=1,height=250,left="+nNx+",top="+nNy;
                  var zRuta = "frlccaxx.php?gModo="+xSwitch+"&gFunction="+xLink+
                                          "&cPerAno="+document.forms['frgrm']['cPerAno'].value +
                                          "&gComCsc="+document.forms['frgrm']['cCerComCsc'].value.toUpperCase();
                  zWindow = window.open(zRuta,xLink,zWinPro);
                  zWindow.focus();
                } else if (xSwitch == "EXACT") {
                  var zRuta = "frlccaxx.php?gModo=EXACT&gFunction=" + xLink +
                                          "&cPerAno="+document.forms['frgrm']['cPerAno'].value +
                                          "&gComCod="+document.frgrm['cCerComCod'].value.toUpperCase() +
                                          "&gComCsc="+document.frgrm['cCerComCsc'].value.toUpperCase() +
                                          "&gComCsc2="+document.frgrm['cCerComCsc2'].value.toUpperCase();
                  parent.fmpro.location = zRuta;
                }
              }
            }
          break;
          case "cDepNum":
            if (xSwitch == "VALID") {
              var zRuta  = "frped155.php?gWhat=VALID" +
                            "&gFunction=cDepNum" +
                            "&gDepNum="+document.forms['frgrm']['cDepNum'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (nX-600)/2;
              var zNy     = (nY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frped155.php?gWhat=WINDOW"+
                            "&gFunction=cDepNum"+
                            "&gDepNum="+document.forms['frgrm']['cDepNum'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
        }
      }
    </script>
  </head>
  <body>
    <form name = "frgrm"  action = "frpedini.php" method = "post" target="fmwork">
      <input type = "hidden" name = "cConsultaInducida"  value = "SI">
      <center>
        <table width="480" cellspacing="0" cellpadding="0">
          <tr>
            <td>
              <fieldset>
                <legend><b>Consulta Inducida</b></legend>
                <table border="0" cellspacing="0" cellpadding="0" width="460">
                  <tr><?php $cCols = f_Format_Cols(23); echo $cCols; ?></tr>
                  <tr>
                    <td class = "name" colspan="05"><br>Rango Fechas</td>
                    <td class = "name" colspan="10"><br>
                      <select Class = "letra" name="cPeriodos"  style = "width:200" onChange = "javascript:
                      parent.fmpro.location='<?php echo $cSystem_Libs_Php_Directory ?>/utilfepe.php?gTipo='+this.value+'&gForm='+'frgrm'+'&gFecIni='+'dDesde'+'&gFecFin='+'dHasta';
                                          if (document.forms['frgrm']['cPeriodos'].value == '99') {
                                            document.forms['frgrm']['dDesde'].readOnly = false;
                                            document.forms['frgrm']['dHasta'].readOnly = false;
                                            document.forms['frgrm']['dDesde'].value = '';
                                            document.forms['frgrm']['dHasta'].value = '';
                                          } else {
                                            document.forms['frgrm']['dDesde'].readOnly = true;
                                            document.forms['frgrm']['dHasta'].readOnly = true;
                                          }">
                        <option value = "10">Hoy</option>
                        <option value = "15">Esta Semana</option>
                        <option value = "20" selected>Este Mes</option>
                        <option value = "25">Este A&ntilde;o</option>
                        <option value = "30">Ayer</option>
                        <option value = "35">Semana Pasada</option>
                        <option value = "40">Semana Pasada Hasta Hoy</option>
                        <option value = "45">Mes Pasado</option>
                        <option value = "50">Mes Pasado Hasta Hoy</option>
                        <option value = "55">Ultimos Tres Meses</option>
                        <option value = "60">Ultimos Seis Meses</option>
                        <option value = "65">Ultimo A&ntilde;o</option>
                        <option value = "99">Periodo Especifico</option>
                      </select>
                      <script language = "javascript">
                        document.forms['frgrm']['cPeriodos'].value = "<?php echo $cPeriodos ?>";
                      </script>
                    </td>
                    <td class="name" colspan="4" align="right"><br>
                        <input type = "text" Class = "letra" name = "dDesde" style = "width:90%" value = "<?php
                        if($_GET['dDesde']=="" && $_GET['cPeriodos'] == ""){
                          echo substr(date('Y-m-d'),0,8)."01";
                        } else{
                          echo $_GET['dDesde'];
                        } ?>"
                          onblur="javascript:document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));" readonly>
                      </td>
                      <td class="name" colspan="4" align="right"><br>
                        <input type = "text" Class = "letra" name = "dHasta"  style = "width:90%" value = "<?php
                            if($_GET['dHasta']=="" && $_GET['cPeriodos'] == ""){
                              echo date('Y-m-d');
                            } else{
                              echo $_GET['dHasta'];
                            }  ?>"
                            onblur = "javascript:document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1)); " readonly>
                      </td>
                  </tr>
                  <tr>
                    <td class = "name" colspan="05"><br>Oficina de Ventas</td>
                    <td class = "name" colspan="18"><br>
                      <select Class = "letra" name="cOfvSap" style = "width:360" value = "<?php echo $cOfvSap ?>">
                        <option value = "ALL" selected>OFICINA DE VENTAS</option>
                        <?php
                          $qOfiVenta  = "SELECT ";
                          $qOfiVenta .= "orvsapxx, ";
                          $qOfiVenta .= "ofvsapxx, ";
                          $qOfiVenta .= "ofvdesxx ";
                          $qOfiVenta .= "FROM $cAlfa.lpar0002 ";
                          $qOfiVenta .= "WHERE ";
                          $qOfiVenta .= "regestxx = \"ACTIVO\" ORDER BY ofvdesxx";
                          $xOfiVenta = f_MySql("SELECT","",$qOfiVenta,$xConexion01,"");
                          if (mysql_num_rows($xOfiVenta) > 0) {
                            while ($xROV = mysql_fetch_array($xOfiVenta)) {
                              if ($xROV['ofvsapxx'] == $cOfvSap) { ?>
                                <option value = "<?php echo $xROV['ofvsapxx']?>" selected><?php echo $xROV['ofvdesxx'] ?></option>
                              <?php } else { ?>
                                <option value = "<?php echo $xROV['ofvsapxx']?>"><?php echo $xROV['ofvdesxx'] ?></option>
                              <?php }
                            }
                          }
                        ?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td class = "name" colspan="05"><br>Usuario</td>
                    <td class = "name" colspan="18"><br>
                      <select Class = "letrase" name = "cUsrId" value = "<?php echo $cUsrId ?>" style = "width:360" >
                        <option value = "ALL" selected>USUARIOS</option>
                        <?php
                          if ($_COOKIE["kUsrId"] == 'ADMIN' || $cUsrInt == "SI") {
                            $qUsrNom  = "SELECT ";
                            $qUsrNom .= "USRIDXXX, ";
                            $qUsrNom .= "USRNOMXX, ";
                            $qUsrNom .= "USRPROXX, ";
                            $qUsrNom .= "REGESTXX ";
                            $qUsrNom .= "FROM $cAlfa.SIAI0003 ";
                            $qUsrNom .= "WHERE ";
                            $qUsrNom .= "USRIDXXX != \"ADMIN\" AND ";
                            $qUsrNom .= "USRINTXX != \"SI\" AND ";
                            $qUsrNom .= "USRPROXX LIKE \"%103%\" ";
                          } else {
                            $qUsrNom  = "SELECT  ";
                            $qUsrNom .= "USRIDXXX, ";
                            $qUsrNom .= "USRNOMXX, ";
                            $qUsrNom .= "USRPROXX, ";
                            $qUsrNom .= "REGESTXX ";
                            $qUsrNom .= "FROM $cAlfa.SIAI0003 ";
                            $qUsrNom .= "WHERE ";
                            $qUsrNom .= "USRIDXXX = \"{$_COOKIE["kUsrId"]}\" AND ";
                            $qUsrNom .= "USRPROXX LIKE \"%103%\" ";
                            ?>
                            <script language="javascript">
                              document.forms['frgrm']['cUsrId'].remove(0);
                            </script>
                            <?php
                          }
                          $xUsrNom = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
                          while ($xRUN = mysql_fetch_array($xUsrNom)) {
                            $mPerEsp = array();
                            $mPerEsp = explode("|",$xRUN['USRPROXX']);
                            for($j=0; $j<count($mPerEsp); $j++) {
                              $mAuxPer = array();
                              $mAuxPer = explode("~",$mPerEsp[$j]);
                              if($mAuxPer[1] == "103") {
                                $mMatrizUsr[$i]['usridxxx'] = $xRUN['USRIDXXX'];
                                $mMatrizUsr[$i]['usrnomxx'] = $xRUN['USRNOMXX'];
                                $mMatrizUsr[$i]['regestxx'] = $xRUN['REGESTXX'];
                                $j = count($mPerEsp);
                                $i++;
                              }
                            }
                          }
                          $mMatrizUsr = f_Sort_Array_By_Field($mMatrizUsr,"usrnomxx","ASC_AZ");

                          for ($i=0;$i<count($mMatrizUsr);$i++) {
                            if($mMatrizUsr[$i]['regestxx'] == "INACTIVO"){
                              $cColor = "#FF0000";
                            }else{
                              $cColor = "#000000";
                            }
                            if ($mMatrizUsr[$i]['usridxxx'] == $cUsrId && $cUsrId != "ADMIN" && $cUsrInt != "SI") { ?>
                              <option value = "<?php echo $mMatrizUsr[$i]['usridxxx']?>" style="color:<?php echo $cColor ?>" selected><?php echo $mMatrizUsr[$i]['usrnomxx'] ?></option>
                            <?php } else { ?>
                              <option value = "<?php echo $mMatrizUsr[$i]['usridxxx']?>" style="color:<?php echo $cColor ?>"><?php echo $mMatrizUsr[$i]['usrnomxx'] ?></option>
                            <?php }
                          }
                        ?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td class = "name" colspan="05"><br>Estado</td>
                    <td class = "name" colspan="18"><br>
                      <select Class = "letra" name = "cEstado" style = "width:360" value = "<?php echo $cEstado ?>">
                        <option value = "" >ESTADO</option>
                        <option value = "PENDIENTE">PENDIENTE</option>
                        <option value = "ACTIVO">ACTIVO</option>
                        <option value = "FACTURADO">FACTURADO</option>
                        <option value = "ANULADO">ANULADA</option>
                        <option value = "ANULADO">ANULADO</option>
                        <option value = "RECHAZADO">RECHAZADO</option>
                        <option value = "NOTA_CREDITO">NOTA CREDITO</option>
                      </select>
                      <script language = "javascript">
                        document.forms['frgrm']['cEstado'].value = "<?php echo $cEstado ?>";
                      </script>
                    </td>
                  </tr>
                  <tr>
                    <td class = "name" colspan="23"><br><hr></hr></td>
                  </tr>
                  <tr>
                    <td class = "name" colspan="05"><br>Consecutivo</td>
                    <td class = "name" colspan="18"><br>
                    <input type="text" class="letra" name = "cConsecutivo" value = "<?php echo $cConsecutivo ?>" style= "width:360" onblur="javascript:this.value=this.value.toUpperCase()">
                    </td>
                  </tr>
                  <tr>
                    <td class = "name" colspan="05"><br>Certificaci&oacute;n:
                    </td>
                    <input type="hidden" name="cCerId" value = "<?php echo $cCerId ?>">
                    <td colspan = "04" class = "name"><br>A&ntilde;o<br>
                      <select name = "cPerAno" style = "width:80;height:16">
                        <?php for($i=$vSysStr['logistica_ano_instalacion_modulo'];$i<=date('Y');$i++){ ?>
                          <option value="<?php echo $i ?>"><?php echo $i ?></option>
                        <?php  } ?>
                      </select>
                      <script language="javascript">
                        document.forms['frgrm']['cPerAno'].value = "<?php echo date('Y') ?>";
                      </script> 
                    </td> 
                    <td Class = "name" colspan = "2"><br>Id<br>
                      <input type = "text" Class = "letra" style = "width:40" name = "cCerComId" value = "<?php echo $cCerComId ?>" readonly>
                    </td>
                    <td Class = "name" colspan = "2"><br>Cod
                      <input type = "text" Class = "letra" style = "width:40" name = "cCerComCod" value = "<?php echo $cCerComCod ?>" readonly>
                    </td>
                    <td Class = "name" colspan = "5"><br>Certificaci&oacute;n
                      <input type = "text" Class = "letra" style = "width:100" name = "cCerComCsc" value = "<?php echo $cCerComCsc ?>"
                            onBlur = "javascript:f_FixFloat(this);
                                  if(document.forms['frgrm']['cCerComCsc'].value.length > 0){
                                    fnLinks('cCerComCsc','VALID');
                                    this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'
                                  }else{
                                    alert('Debe Digitar al Menos Un Digito de la Certificaci&oacute;n');
                                  }"
                            onFocus = "javascript:document.forms['frgrm']['cCerComId'].value = '';
                                                document.forms['frgrm']['cCerComCod'].value  = '';
                                                document.forms['frgrm']['cCerComCsc'].value  = '';
                                                document.forms['frgrm']['cCerComCsc2'].value = '';
                                                this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                    </td> 
                    <td Class = "name" colspan = "5"><br>Consecutivo 2
                      <input type = "text" Class = "letra" style = "width:100" name = "cCerComCsc2" value = "<?php echo $cCerComCsc2 ?>" readonly>
                    </td>
                  </tr>
                  <tr>
                    <td class = "name" colspan="05"><br><a href = "javascript:document.forms['frgrm']['cCliId'].value = '';
                                            document.forms['frgrm']['cCliDV'].value  = '';
                                            document.forms['frgrm']['cCliNom'].value = '';
                                            fnLinks('cCliId','VALID')">Cliente:</a>
                    </td>
                    <td class="name" colspan = "05"><br>
                      <input type="text" name="cCliId" style = "width:100" value = "<?php echo $cCliId ?>"
                            onfocus="javascript:document.forms['frgrm']['cCliId'].value  = '';
                                                document.forms['frgrm']['cCliDV'].value  = '';
                                                document.forms['frgrm']['cCliNom'].value = '';
                                                this.style.background='#00FFFF'"
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                fnLinks('cCliId','VALID');
                                                this.style.background='#FFFFFF'">
                    </td>
                    <td Class = "name" colspan = "02"><br>
                      <input type = "text" style = "width:40;text-align:center" name = "cCliDV" value = "<?php echo $cCliDV ?>" readonly>
                    </td>
                    <td class="name" colspan = "11"><br>
                      <input type="text" name="cCliNom" style = "width:220" value = "<?php echo $cCliNom ?>"
                            onfocus="javascript:document.forms['frgrm']['cCliId'].value  = '';
                                                document.forms['frgrm']['cCliDV'].value  = '';
                                                document.forms['frgrm']['cCliNom'].value = '';
                                                this.style.background='#00FFFF'"
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                fnLinks('cCliNom','VALID');
                                                this.style.background='#FFFFFF'">
                    </td>
                    <input type = "hidden" name = "cCliSap">
                  </tr>
                  <tr>
                    <td class = "name" colspan="05"><br>
                      <a href = "javascript:document.forms['frgrm']['cDepNum'].value = '';
                                            fnLinks('cDepNum','VALID')">Dep&oacute;sito:</a>
                    </td>
                    <td class="name" colspan = "18"><br>
                      <input type="text" name="cDepNum" style = "width:360" value = "<?php echo $cDepNum ?>"
                            onfocus="javascript:document.forms['frgrm']['cDepNum'].value = '';
                                                this.style.background='#00FFFF'"
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                fnLinks('cDepNum','VALID');
                                                this.style.background='#FFFFFF'">
                    </td>
                  </tr>
                  </tr>
                </table>
              </fieldset>
            </td>
          </tr>
        </table>
        <table border="0" cellpadding="0" cellspacing="0" width="480">
          <tr>
            <td width="298" height="21"></td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:fnEnviarDatos();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Generar</td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = "javascript:parent.window.close()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
          </tr>
        </table>
      </center>
    </form>
  </body>
</html>

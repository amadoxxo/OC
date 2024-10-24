<?php
namespace openComex;

/**
 * Formulario para consulta inducida en el traking de Mis Tikets.
 * @author Cristian Perdomo <cristian.perdomo@openits.co>
 * @package opencomex
 * @version 001
 */
include("../../../../../financiero/libs/php/utility.php");

//  Cookie fija
$kDf = explode("~", $_COOKIE["kDatosFijos"]);
$kMysqlHost = $kDf[0];
$kMysqlUser = $kDf[1];
$kMysqlPass = $kDf[2];
$kMysqlDb   = $kDf[3];
$kUser      = $kDf[4];
$kLicencia  = $kDf[5];
$swidth     = $kDf[6];
?>
<html>
  <head>
    <title>Consulta Inducida</title>
    <LINK rel='stylesheet' href='<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
    <LINK rel='stylesheet' href='<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
    <LINK rel='stylesheet' href='<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
    <LINK rel='stylesheet' href='<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
    <script languaje='javascript' src='<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
    <script languaje='javascript' src='<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
    <script language="javascript">
      function fnEnviarDatos() {
        var vDatos = new Array();
        vDatos['cPeriodos']       = document.forms['frgrm']['cPeriodos'].value;
        vDatos['dDesde']          = document.forms['frgrm']['dDesde'].value;
        vDatos['dHasta']          = document.forms['frgrm']['dHasta'].value;
        vDatos['cTicket']         = document.forms['frgrm']['cTicket'].value;
        vDatos['cTiAsun']         = document.forms['frgrm']['cTiAsun'].value;
        vDatos['cCerId']          = document.forms['frgrm']['cCerId'].value;
        vDatos['cPerAno']         = document.forms['frgrm']['cPerAno'].value;
        vDatos['cCliId']          = document.forms['frgrm']['cCliId'].value;
        vDatos['cUsrId']          = document.forms['frgrm']['cUsrId'].value;
        vDatos['cResId']          = document.forms['frgrm']['cResId'].value;
        vDatos['cTipId']          = document.forms['frgrm']['cTipId'].value;
        vDatos['cPriori']         = document.forms['frgrm']['cPriori'].value;
        vDatos['cStatus']         = document.forms['frgrm']['cStatus'].value;
        parent.window.opener.fnEnviarConsultaInducida(vDatos);
        parent.window.close();
      }

      function fnLimpiar() {
        var vDatos = new Array();
        document.forms['frgrm']['cPeriodos'].value   = "20";
        document.forms['frgrm']['dDesde'].value      = '<?php echo substr(date('Y-m-d'),0,8)."01";  ?>';
        document.forms['frgrm']['dHasta'].value      = '<?php echo date('Y-m-d');  ?>';
        document.forms['frgrm']['cTicket'].value     = "";
        document.forms['frgrm']['cTiAsun'].value     = "";
        document.forms['frgrm']['cCerId'].value      = "";
        document.forms['frgrm']['cPerAno'].value     = '<?php echo date('Y');  ?>';
        document.forms['frgrm']['cCerComId'].value   = "";
        document.forms['frgrm']['cCerComCod'].value  = "";
        document.forms['frgrm']['cCerComCsc'].value  = "";
        document.forms['frgrm']['cCerComCsc2'].value = "";
        document.forms['frgrm']['cCliId'].value      = "";
        document.forms['frgrm']['cCliDV'].value      = "";
        document.forms['frgrm']['cCliNom'].value     = "";
        document.forms['frgrm']['cUsrId'].value      = "";
        document.forms['frgrm']['cUsrNom'].value     = "";
        document.forms['frgrm']['cResId'].value      = "";
        document.forms['frgrm']['cResNom'].value     = "";
        document.forms['frgrm']['cTipId'].value      = "";
        document.forms['frgrm']['cTipDes'].value     = "";
        document.forms['frgrm']['cPriori'].value     = "";
        document.forms['frgrm']['cStatus'].value     = "";
      }

      function fnLinks(xLink, xSwitch) {
        var nX = screen.width;
        var nY = screen.height;
        switch (xLink) {
          // Cliente
          case "cCliId":
          case "cCliNom":
            if (xSwitch == "VALID") {
              var cRuta = "frmti150.php?gWhat=VALID&gFunction="+xLink+"&gCliId=" + document.forms['frgrm'][xLink].value.toUpperCase();
              parent.fmpro.location = cRuta;
            } else {
              var zNx = (nX - 600) / 2;
              var zNy = (nY - 250) / 2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left=' + zNx + ',top=' + zNy;
              var cRuta = "frmti150.php?gWhat=WINDOW&gFunction="+xLink+"&gCliId=" + document.forms['frgrm'][xLink].value.toUpperCase();
              zWindow = window.open(cRuta, "zWindow", zWinPro);
              zWindow.focus();
            }
          break;
          //usuarios y responsables
          case "cUsrId":
          case "cUsrNom":
          case "cResId":
          case "cResNom":
            if (xSwitch == "VALID") {
              var cRuta = "frmti003.php?gWhat=VALID&gFunction="+xLink+"&gUsrId=" + document.forms['frgrm'][xLink].value.toUpperCase();
              parent.fmpro.location = cRuta;
            } else {
              var zNx = (nX - 600) / 2;
              var zNy = (nY - 250) / 2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left=' + zNx + ',top=' + zNy;
              var cRuta = "frmti003.php?gWhat=WINDOW&gFunction="+xLink+"&gUsrId=" + document.forms['frgrm'][xLink].value.toUpperCase();
              zWindow = window.open(cRuta, "zWindow", zWinPro);
              zWindow.focus();
            }
          break;
          //Tipo Ticket
          case "cTipId":
          case "cTipDes":
            if (xSwitch == "VALID") {
              var cRuta = "frmti158.php?gWhat=VALID&gFunction="+xLink+"&gTipId=" + document.forms['frgrm'][xLink].value.toUpperCase();
              parent.fmpro.location = cRuta;
            } else {
              var zNx = (nX - 600) / 2;
              var zNy = (nY - 250) / 2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left=' + zNx + ',top=' + zNy;
              var cRuta = "frmti158.php?gWhat=WINDOW&gFunction="+xLink+"&gTipId=" + document.forms['frgrm'][xLink].value.toUpperCase();
              zWindow = window.open(cRuta, "zWindow", zWinPro);
              zWindow.focus();
            }
          break;
          case "cCerComCsc":
            if (document.forms['frgrm']['cCerComCsc'].value != "") {
              if (document.forms['frgrm']['cCerComCsc'].value.length < 1) {
                alert('Debe Digitar al Menos un Digito de la Certificaci&oacute;n');
              } else {
                if (xSwitch == "VALID") {
                  var cRuta = "frmtiaxx.php?gModo=" + xSwitch + "&gFunction=" + xLink +
                    "&cPerAno=" + document.forms['frgrm']['cPerAno'].value +
                    "&gComCsc=" + document.forms['frgrm']['cCerComCsc'].value.toUpperCase();
                  parent.fmpro.location = cRuta;
                } else if (xSwitch == "WINDOW") {
                  var nNx = (nX - 500) / 2;
                  var nNy = (nY - 250) / 2;
                  var zWinPro = "width=500,scrollbars=1,height=250,left=" + nNx + ",top=" + nNy;
                  var cRuta = "frmtiaxx.php?gModo=" + xSwitch + "&gFunction=" + xLink +
                    "&cPerAno=" + document.forms['frgrm']['cPerAno'].value +
                    "&gComCsc=" + document.forms['frgrm']['cCerComCsc'].value.toUpperCase();
                  zWindow = window.open(cRuta, xLink, zWinPro);
                  zWindow.focus();
                }
              }
            }
          break;
        }
      }
    </script>
  </head>
  <body>
    <form name="frgrm" action="frmtiini.php" method="post" target="fmwork">
      <input type="hidden" name="cConsultaInducida" value="SI">
      <center>
        <table width="480" cellspacing="0" cellpadding="0">
          <tr>
            <td>
              <fieldset>
                <legend><b>Consulta Inducida</b></legend>
                <table border="0" cellspacing="0" cellpadding="0" width="460">
                  <tr><?php $cCols = f_Format_Cols(23);
                      echo $cCols; ?>
                  </tr>
                  <tr>
                    <td class="name" colspan="05"><br>Rango Fechas</td>
                    <td class="name" colspan="10"><br>
                      <select Class="letra" name="cPeriodos" style="width:200" onChange="javascript:
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
                        <option value="10">Hoy</option>
                        <option value="15">Esta Semana</option>
                        <option value="20" selected>Este Mes</option>
                        <option value="25">Este A&ntilde;o</option>
                        <option value="30">Ayer</option>
                        <option value="35">Semana Pasada</option>
                        <option value="40">Semana Pasada Hasta Hoy</option>
                        <option value="45">Mes Pasado</option>
                        <option value="50">Mes Pasado Hasta Hoy</option>
                        <option value="55">Ultimos Tres Meses</option>
                        <option value="60">Ultimos Seis Meses</option>
                        <option value="65">Ultimo A&ntilde;o</option>
                        <option value="99">Periodo Especifico</option>
                      </select>
                      <script language="javascript">
                        document.forms['frgrm']['cPeriodos'].value = "<?php echo $cPeriodos ?>";
                      </script>
                    </td>
                    <td class="name" colspan="4" align="right"><br>
                      <input type="text" Class="letra" name="dDesde" style="width:90%" value="<?php
                                                                                              if ($_GET['dDesde'] == "" && $_GET['cPeriodos'] == "") {
                                                                                                echo substr(date('Y-m-d'), 0, 8) . "01";
                                                                                              } else {
                                                                                                echo $_GET['dDesde'];
                                                                                              } ?>" onblur="javascript:document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));" readonly>
                    </td>
                    <td class="name" colspan="4" align="right"><br>
                      <input type="text" Class="letra" name="dHasta" style="width:90%" value="<?php
                                                                                              if ($_GET['dHasta'] == "" && $_GET['cPeriodos'] == "") {
                                                                                                echo date('Y-m-d');
                                                                                              } else {
                                                                                                echo $_GET['dHasta'];
                                                                                              }  ?>" onblur="javascript:document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1)); " readonly>
                    </td>
                  </tr>
                  <tr>
                    <td class="name" colspan="05"><br>Ticket</td>
                    <td class="name" colspan="18"><br>
                      <input type="text" class="letra" name="cTicket" style="width:360" onblur="javascript:this.value=this.value.toUpperCase()" value="<?php echo $cTicket ?>">
                    </td>
                  </tr>
                  <tr>
                    <td class="name" colspan="05"><br>Asunto</td>
                    <td class="name" colspan="18"><br>
                      <input type="text" class="letra" name="cTiAsun" style="width:360" onblur="javascript:this.value=this.value.toUpperCase()" value="<?php echo $cTiAsun ?>">
                    </td>
                  </tr>
                  <tr>
                    <td class="name" colspan="05"><br>Certificaci&oacute;n:
                    </td>
                    <input type="hidden" name="cCerId" value="<?php echo $cCerId ?>">
                    <td colspan="04" class="name"><br>A&ntilde;o<br>
                      <select Class="letra" name="cPerAno" style="width:80">
                        <?php for ($i = $vSysStr['logistica_ano_instalacion_modulo']; $i <= date('Y'); $i++) { ?>
                          <option value="<?php echo $i ?>"><?php echo $i ?></option>
                        <?php  } ?>
                      </select>
                      <script language="javascript">
                        document.forms['frgrm']['cPerAno'].value = "<?php echo date('Y') ?>";
                      </script>
                    </td>
                    <td Class="name" colspan="2"><br>Id<br>
                      <input type="text" Class="letra" style="width:40" name="cCerComId" value="<?php echo $cCerComId ?>" readonly>
                    </td>
                    <td Class="name" colspan="2"><br>Cod
                      <input type="text" Class="letra" style="width:40" name="cCerComCod" value="<?php echo $cCerComCod ?>" readonly>
                    </td>
                    <td Class="name" colspan="5"><br>Certificaci&oacute;n
                      <input type="text" Class="letra" style="width:100" name="cCerComCsc" value="<?php echo $cCerComCsc ?>" onBlur="javascript:f_FixFloat(this);
                                    if(document.forms['frgrm']['cCerComCsc'].value.length > 0){
                                      fnLinks('cCerComCsc','VALID');
                                      this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'
                                    }else{
                                      alert('Debe Digitar al Menos Un Digito de la Certificaci&oacute;n');
                                    }" onFocus="javascript:document.forms['frgrm']['cCerComId'].value = '';
                                                  document.forms['frgrm']['cCerComCod'].value  = '';
                                                  document.forms['frgrm']['cCerComCsc'].value  = '';
                                                  document.forms['frgrm']['cCerComCsc2'].value = '';
                                                  this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                    </td>
                    <td Class="name" colspan="5"><br>Consecutivo 2
                      <input type="text" Class="letra" style="width:100" name="cCerComCsc2" value="<?php echo $cCerComCsc2 ?>" readonly>
                    </td>
                  </tr>
                  <tr>
                    <td class="name" colspan="05"><br><a href="javascript:document.forms['frgrm']['cCliId'].value = '';
                                              document.forms['frgrm']['cCliDV'].value  = '';
                                              document.forms['frgrm']['cCliNom'].value = '';
                                              fnLinks('cCliId','WINDOW')">Cliente:</a>
                    </td>
                    <td class="name" colspan="05"><br>
                      <input type="text" name="cCliId" style="width:100" value="<?php echo $cCliId ?>" onfocus="javascript:document.forms['frgrm']['cCliId'].value  = '';
                                                  document.forms['frgrm']['cCliDV'].value  = '';
                                                  document.forms['frgrm']['cCliNom'].value = '';
                                                  this.style.background='#00FFFF'" onBlur="javascript:this.value=this.value.toUpperCase();
                                                  fnLinks('cCliId','VALID');
                                                  this.style.background='#FFFFFF'">
                    </td>
                    <td Class="name" colspan="02"><br>
                      <input type="text" style="width:40;text-align:center" name="cCliDV" readonly>
                    </td>
                    <td class="name" colspan="11"><br>
                      <input type="text" name="cCliNom" style="width:220" 
                        onfocus="javascript:document.forms['frgrm']['cCliId'].value  = '';
                                                  document.forms['frgrm']['cCliDV'].value  = '';
                                                  document.forms['frgrm']['cCliNom'].value = '';
                                                  this.style.background='#00FFFF'" onBlur="javascript:this.value=this.value.toUpperCase();
                                                  fnLinks('cCliNom','VALID');
                                                  this.style.background='#FFFFFF'">
                    </td>
                    
                  </tr>
                  <tr>
                    <td class="name" colspan="05"><br><a href="javascript:document.forms['frgrm']['cUsrId'].value = '';
                                              document.forms['frgrm']['cUsrNom'].value = '';
                                              fnLinks('cUsrId','WINDOW')">Creado por:</a>
                    </td>
                    <td class="name" colspan="05"><br>
                      <input type="text" name="cUsrId" style="width:100" value="<?php echo $cUsrId ?>" 
                        onfocus="javascript:document.forms['frgrm']['cUsrId'].value  = '';
                                  document.forms['frgrm']['cUsrNom'].value = '';
                                  this.style.background='#00FFFF'" onBlur="javascript:this.value=this.value.toUpperCase();
                                  fnLinks('cUsrId','VALID');
                                  this.style.background='#FFFFFF'">
                    </td>
                    <td Class="name" colspan="02"><br>
                      <input type="text" style="width:40;text-align:center" readonly>
                    </td>
                    <td class="name" colspan="11"><br>
                      <input type="text" name="cUsrNom" style="width:220" 
                        onfocus="javascript:document.forms['frgrm']['cUsrId'].value  = '';
                                  document.forms['frgrm']['cUsrNom'].value = '';
                                  this.style.background='#00FFFF'" onBlur="javascript:this.value=this.value.toUpperCase();
                                  fnLinks('cUsrNom','VALID');
                                  this.style.background='#FFFFFF'">
                    </td>
                  </tr>
                  <tr>
                    <td class="name" colspan="05"><br><a href="javascript:document.forms['frgrm']['cResId'].value = '';
                                              document.forms['frgrm']['cResNom'].value = '';
                                              fnLinks('cResId','WINDOW')">Responsable:</a>
                    </td>
                    <td class="name" colspan="05"><br>
                      <input type="text" name="cResId" style="width:100" value="<?php echo $cResId ?>" 
                        onfocus="javascript:document.forms['frgrm']['cResId'].value  = '';
                          document.forms['frgrm']['cResNom'].value = '';
                          this.style.background='#00FFFF'" onBlur="javascript:this.value=this.value.toUpperCase();
                          fnLinks('cResId','VALID');
                          this.style.background='#FFFFFF'">
                    </td>
                    <td Class="name" colspan="02"><br>
                      <input type="text" style="width:40;text-align:center" readonly>
                    </td>
                    <td class="name" colspan="11"><br>
                      <input type="text" name="cResNom" style="width:220"
                        onfocus="javascript:document.forms['frgrm']['cResId'].value  = '';
                                  document.forms['frgrm']['cResNom'].value = '';
                                  this.style.background='#00FFFF'" onBlur="javascript:this.value=this.value.toUpperCase();
                                  fnLinks('cResNom','VALID');
                                  this.style.background='#FFFFFF'">
                    </td>
                  </tr>
                  <tr>
                    <td class="name" colspan="05"><br><a href="javascript:document.forms['frgrm']['cTipId'].value = '';
                                              document.forms['frgrm']['cTipDes'].value = '';
                                              fnLinks('cTipId','WINDOW')">Tipo Ticket:</a>
                    </td>
                    <td class="name" colspan="05"><br>
                      <input type="text" name="cTipId" style="width:100" value="<?php echo $cTipId ?>" 
                        onfocus="javascript:document.forms['frgrm']['cTipId'].value  = '';
                          document.forms['frgrm']['cTipDes'].value = '';
                          this.style.background='#00FFFF'" onBlur="javascript:this.value=this.value.toUpperCase();
                          fnLinks('cTipId','VALID');
                          this.style.background='#FFFFFF'">
                    </td>
                    <td Class="name" colspan="02"><br>
                      <input type="text" style="width:40;text-align:center" readonly>
                    </td>
                    <td class="name" colspan="11"><br>
                      <input type="text" name="cTipDes" style="width:220"
                        onfocus="javascript:document.forms['frgrm']['cTipId'].value  = '';
                                  document.forms['frgrm']['cTipDes'].value = '';
                                  this.style.background='#00FFFF'" onBlur="javascript:this.value=this.value.toUpperCase();
                                  fnLinks('cTipDes','VALID');
                                  this.style.background='#FFFFFF'">
                    </td>
                  </tr>
                  <tr>
                    <td class="name" colspan="05"><br>Prioridad:</td>
                    <td class="name" colspan="18"><br>
                      <select Class="letra" name="cPriori" style="width:360">
                        <option value="" selected></option>
                        <?php
                        $qPrioridad  = "SELECT ";
                        $qPrioridad .= "pticodxx, ";
                        $qPrioridad .= "ptidesxx ";
                        $qPrioridad .= "FROM $cAlfa.lpar0156 ";
                        $qPrioridad .= "WHERE ";
                        $qPrioridad .= "regestxx = \"ACTIVO\" ORDER BY pticodxx";
                        $xPrioridad = f_MySql("SELECT", "", $qPrioridad, $xConexion01, "");
                        if (mysql_num_rows($xPrioridad) > 0) {
                          while ($xROV = mysql_fetch_array($xPrioridad)) {
                        ?>
                            <option value="<?php echo $xROV['pticodxx'] ?>"><?php echo $xROV['ptidesxx'] ?></option>
                        <?php
                          }
                        }
                        ?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td class="name" colspan="05"><br>Status:</td>
                    <td class="name" colspan="18"><br>
                      <select Class="letra" name="cStatus" style="width:360">
                        <option value="" selected></option>
                        <?php
                        $qStatus  = "SELECT ";
                        $qStatus .= "sticodxx, ";
                        $qStatus .= "stidesxx ";
                        $qStatus .= "FROM $cAlfa.lpar0157 ";
                        $qStatus .= "WHERE ";
                        $qStatus .= "regestxx = \"ACTIVO\" ORDER BY sticodxx";
                        $xStatus = f_MySql("SELECT", "", $qStatus, $xConexion01, "");
                        if (mysql_num_rows($xStatus) > 0) {
                          while ($xROV = mysql_fetch_array($xStatus)) {
                        ?>
                            <option value="<?php echo $xROV['sticodxx'] ?>"><?php echo $xROV['stidesxx'] ?></option>
                        <?php
                          }
                        }
                        ?>
                      </select>
                    </td>
                  </tr>
                </table>
              </fieldset>
            </td>
          </tr>
        </table>
        <table border="0" cellpadding="0" cellspacing="0" width="480">
          <tr height="21">
            <td width="207" height="21"></td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_New ?>/btn_cancel_bg.gif" style="cursor:hand"
                onClick = "javascript:fnLimpiar();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Limpiar
            </td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_New ?>/btn_ok_bg.gif" style="cursor:hand"
                onClick = "javascript:fnEnviarDatos();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Generar
            </td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_New ?>/btn_cancel_bg.gif" style="cursor:hand"
                onClick = "javascript:parent.window.close();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
            </td>
          </tr>
        </table>
      </center>
      <?php
        // Consultando los datos para pregargarlos
        // Cliente
        if ($cCliId != "") {
          $qDatCli  = "SELECT ";
          $qDatCli .= "cliidxxx, ";
          $qDatCli .= "clisapxx, ";
          $qDatCli .= "IF(clinomxx != \"\",clinomxx,(TRIM(CONCAT(clinomxx,\" \",clinom1x,\" \",clinom2x,\" \",cliape1x,\" \",cliape2x)))) AS clinomxx ";
          $qDatCli .= "FROM $cAlfa.lpar0150 ";
          $qDatCli .= "WHERE ";
          $qDatCli .= "cliidxxx = \"$cCliId\"";
          $xDatCli  = f_MySql("SELECT", "", $qDatCli, $xConexion01, "");
          $vDatCli  = mysql_fetch_array($xDatCli);
          // f_Mensaje(__FILE__, __LINE__,$qDatCli."~".mysql_num_rows($xDatCli));
          ?>
          <script language="javascript">
            document.forms['frgrm']['cCliId'].value  = "<?php echo $vDatCli['cliidxxx'] ?>";
            document.forms['frgrm']['cCliDV'].value  = "<?php echo gendv($vDatCli['cliidxxx'])?>";
            document.forms['frgrm']['cCliNom'].value = "<?php echo $vDatCli['clinomxx'] ?>";
          </script>
        <?php }
        // Certificacion
        if ($cCerId != "" && $cPerAno != "") {
          $qCertiCab  = "SELECT ";
          $qCertiCab .= "ceridxxx,";
          $qCertiCab .= "comidxxx,";
          $qCertiCab .= "comcodxx,";
          $qCertiCab .= "comprexx,";
          $qCertiCab .= "comcscxx,";
          $qCertiCab .= "comcsc2x,";
          $qCertiCab .= "comfecxx ";
          $qCertiCab .= "FROM $cAlfa.lcca$cPerAno ";
          $qCertiCab .= "WHERE ";
          $qCertiCab .= "$cAlfa.lcca$cPerAno.ceridxxx = \"$cCerId\"";
          $xCertiCab = f_MySql("SELECT", "", $qCertiCab, $xConexion01, "");
          $vCertiCab = mysql_fetch_array($xCertiCab);
          // f_Mensaje(__FILE__, __LINE__,$qCertiCab."~".mysql_num_rows($xCertiCab));
          ?>
          <script language = "javascript">
            document.forms['frgrm']['cCerId'].value      = '<?php echo $vCertiCab['ceridxxx'] ?>';
            document.forms['frgrm']['cCerComId'].value   = '<?php echo $vCertiCab['comidxxx'] ?>';
            document.forms['frgrm']['cCerComCod'].value  = '<?php echo $vCertiCab['comcodxx'] ?>';
            document.forms['frgrm']['cCerComCsc'].value  = '<?php echo $vCertiCab['comcscxx'] ?>';
            document.forms['frgrm']['cCerComCsc2'].value = '<?php echo $vCertiCab['comcsc2x'] ?>';
          </script>
          <?php
        }
        // Usuario
        if ($cUsrId != "") {
          $qDatUsr  = "SELECT ";
          $qDatUsr .= "USRIDXXX, ";
          $qDatUsr .= "USRNOMXX, ";
          $qDatUsr .= "USRNOMXX,";
          $qDatUsr .= "REGESTXX ";
          $qDatUsr .= "FROM $cAlfa.SIAI0003 ";
          $qDatUsr .= "WHERE ";
          $qDatUsr .= "USRIDXXX = \"$cUsrId\"";
          $xDatUsr  = f_MySql("SELECT", "", $qDatUsr, $xConexion01, "");
          $vDatUsr  = mysql_fetch_array($xDatUsr);
          ?>
          <script language = "javascript">
            document.forms['frgrm']['cUsrId'].value  = "<?php echo $vDatUsr['USRIDXXX'] ?>";
            document.forms['frgrm']['cUsrNom'].value = "<?php echo $vDatUsr['USRNOMXX'] ?>";
          </script>
          <?php
        }
        // Responsable
        if ($cResId != "") {
          $qDatUsr  = "SELECT ";
          $qDatUsr .= "USRIDXXX, ";
          $qDatUsr .= "USRNOMXX, ";
          $qDatUsr .= "USRNOMXX,";
          $qDatUsr .= "REGESTXX ";
          $qDatUsr .= "FROM $cAlfa.SIAI0003 ";
          $qDatUsr .= "WHERE ";
          $qDatUsr .= "USRIDXXX = \"$cResId\"";
          $xDatUsr  = f_MySql("SELECT", "", $qDatUsr, $xConexion01, "");
          $vDatUsr  = mysql_fetch_array($xDatUsr);
          ?>
          <script language = "javascript">
            document.forms['frgrm']['cResId'].value  = "<?php echo $vDatUsr['USRIDXXX'] ?>";
            document.forms['frgrm']['cResNom'].value = "<?php echo $vDatUsr['USRNOMXX'] ?>";
          </script>
          <?php
        }
        // Tipo Ticket
        if ($cTipId != "") {
          $qTipTic  = "SELECT ";
          $qTipTic .= "tticodxx, ";
          $qTipTic .= "ttidesxx ";
          $qTipTic .= "FROM $cAlfa.lpar0158 ";
          $qTipTic .= "WHERE ";
          $qTipTic .= "tticodxx = \"$cTipId\"";
          $xTipTic  = f_MySql("SELECT","",$qTipTic,$xConexion01,"");
          $vTipTic  = mysql_fetch_array($xTipTic);
          // f_Mensaje(__FILE__, __LINE__,$qTipTic."~".mysql_num_rows($xTipTic));
          ?>
          <script language = "javascript">
            document.forms['frgrm']['cTipId'].value  = "<?php echo $vTipTic['tticodxx'] ?>";
            document.forms['frgrm']['cTipDes'].value = "<?php echo $vTipTic['ttidesxx'] ?>";
          </script>
          <?php
        }
        // Prioridad
        if ($cPriori != "") {
          ?>
          <script language = "javascript">
            document.forms['frgrm']['cPriori'].value  = "<?php echo $cPriori ?>";
          </script>
          <?php
        }
        // Status
        if ($cStatus != "") {
          ?>
          <script language = "javascript">
            document.forms['frgrm']['cStatus'].value  = "<?php echo $cStatus ?>";
          </script>
          <?php
        }
      ?>
    </form>
  </body>
</html>
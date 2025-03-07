<?php
  namespace openComex;
  /**
   * Formulario para consulta inducida en el traking de Autorizacion Pagos a Terceros.
   * @author Juan José Hernandez <juan.hernandez@openits.co>
   * @package openComex
   * @version 001
   */

  include("../../../../libs/php/utility.php");

  //  Cookie fija
  $kDf = explode("~",$_COOKIE["kDatosFijos"]);
  $kMysqlHost = $kDf[0];
  $kMysqlUser = $kDf[1];
  $kMysqlPass = $kDf[2];
  $kMysqlDb   = $kDf[3];
  $kUser      = $kDf[4];
  $kLicencia  = $kDf[5];
  $swidth     = $kDf[6];

?>
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
        vDatos['cPeriodos'] = document.forms['frgrm']['cPeriodos'].value;
        vDatos['dDesde']    = document.forms['frgrm']['dDesde'].value;
        vDatos['dHasta']    = document.forms['frgrm']['dHasta'].value;
        vDatos['cUsrId']    = document.forms['frgrm']['cUsrId'].value;
        vDatos['cCcoId']    = document.forms['frgrm']['cCcoId'].value;
        vDatos['cDo']       = document.forms['frgrm']['cDo'].value;
        vDatos['cConsultaInducida'] = document.forms['frgrm']['cConsultaInducida'].value;
        parent.window.opener.fnEnviarConsultaInducida(vDatos);
        parent.window.close();
      }
    </script>
  </head>
  <body>
    <?php 
      // Reiniciamos los campos del formulario
      $cPeriodos = "20";
      $dDesde    = "";
      $dHasta    = "";
      $cCcoId    = "";
      $cDo       = "";
      $cUsrId    = "";
    ?>
    <form name = "frgrm"  action = "fraptini.php" method = "post" target="fmwork">
      <input type = "hidden" name = "cConsultaInducida"    value = "SI">
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
                        if($_GET['dDesde'] == "" && $_GET['cPeriodos'] == ""){
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
                    <td class = "name" colspan="05"><br>Sucursal</td>
                    <td class = "name" colspan="18"><br>
                      <select Class = "letra" name="cCcoId" style = "width:360" value = "<?php echo $cCcoId ?>">
                        <option value = "ALL" selected>SUCURSALES</option>
                        <?php
                        $qSucDat  = "SELECT sucidxxx,sucdesxx FROM $cAlfa.fpar0008 WHERE ";
                        $qSucDat .= "regestxx = \"ACTIVO\" ORDER BY sucdesxx";
                        $xSucDat = f_MySql("SELECT","",$qSucDat,$xConexion01,"");
                        if (mysql_num_rows($xSucDat) > 0) {
                          while ($xRSD = mysql_fetch_array($xSucDat)) {
                            if ($xRSD['sucidxxx'] == $cCcoId) { ?>
                              <option value = "<?php echo $xRSD['sucidxxx']?>" selected><?php echo $xRSD['sucdesxx'] ?></option>
                            <?php } else { ?>
                              <option value = "<?php echo $xRSD['sucidxxx']?>"><?php echo $xRSD['sucdesxx'] ?></option>
                            <?php }
                          }
                        }
                        ?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td class = "name" colspan="05"><br>Do</td>
                    <td class = "name" colspan="18"><br>
                    <input type="text" class="letra" name = "cDo" value = "<?php echo $cDo ?>" style= "width:360" onblur="javascript:this.value=this.value.toUpperCase()">
                    </td>
                  </tr>
                  <tr>
                    <td class = "name" colspan="05"><br>Usuario</td>
                    <td class = "name" colspan="18"><br>
                    <select Class = "letrase" name = "cUsrId" value = "<?php echo $cUsrId ?>" style = "width:360" >
                        <option value = "ALL" selected>USUARIOS</option>
                        <?php
                          if (($_COOKIE["kUsrId"] == 'ADMIN' || $cUsrInt == "SI") || ($cAlfa != 'DEOPENWORK' && $cAlfa != 'OPENWORK' && $cAlfa != 'TEOPENWORK')) {
                            $qUsrNom  = "SELECT USRIDXXX,USRNOMXX,USRPROXX,REGESTXX FROM $cAlfa.SIAI0003 WHERE USRIDXXX != \"ADMIN\" AND USRINTXX != \"SI\" AND USRPROXX LIKE \"%103%\" ";
                          } else {
                            $qUsrNom  = "SELECT USRIDXXX,USRNOMXX,USRPROXX,REGESTXX FROM $cAlfa.SIAI0003 WHERE USRIDXXX = \"{$_COOKIE["kUsrId"]}\" AND USRPROXX LIKE \"%103%\" ";?>
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
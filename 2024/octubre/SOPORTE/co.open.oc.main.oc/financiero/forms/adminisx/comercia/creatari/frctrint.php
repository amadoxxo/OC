<?php
  include('../../../../libs/php/utility.php');
?>
<html>
  <head>
    <title>Clientes</title>
    <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
    <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
    <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
    <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
    <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
    <script language="javascript" src="<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
    <script language="javascript">

      function fnGuardar() {
        var cadena = document.forms['frcotoplo']['cCadena'].value;
        if (cadena.length > 1) {
          var cRuta = "frctrsav.php?cCadena="+cadena+"&cTarCli="+window.opener.document.forms['frgrm']['cTarCli'].value+"&tipsave=5";
          fnmakeRequest(cRuta);
        } else {
          alert('Debe Seleccionar un Cliente. Verifique.')
        }
      }

      function fnmakeRequest(xRuta){
        http_request = false;
        if (window.XMLHttpRequest) { // Mozilla, Safari,...
          http_request = new XMLHttpRequest();
          if (http_request.overrideMimeType) {
            http_request.overrideMimeType('text/xml');
          }
        }else if (window.ActiveXObject) { // IE
          try {
            http_request = new ActiveXObject("Msxml2.XMLHTTP");
          } catch (e) {
            try {
              http_request = new ActiveXObject("Microsoft.XMLHTTP");
            }  catch (e) {}
          }
        }        
        if (!http_request) {
          alert('Falla :( No es posible crear una instancia XMLHTTP');
          return false;
        }
        
        http_request.onreadystatechange = fnAlertContents;
        http_request.open('GET', xRuta, true);
        http_request.send(null);
      }

      function fnAlertContents() {
        if(http_request.readyState==1){
        }else if(http_request.readyState == 4) {
          if (http_request.status == 200) {
            if(http_request.responseText!=""){
              var cRetorno = http_request.responseText.replace(/^\s+|\s+$/g,"");
              var mRetorno = cRetorno.split("|");
              if (mRetorno[0] == "true") {
                window.opener.document.forms['frgrm']['cTarCli'].value = mRetorno[1];
                window.opener.fnCargarGrilla();
                window.close();
              } else {
                alert(mRetorno[1]);
              }
            }else{
              //No Hace Nada
            }
          } else {
            alert('Hubo problemas con la peticion.');
          }
        }
      }

      function fnCo(fld){
        var cade = document.forms['frcotoplo']['cCadena'].value
        var name = 'OFF';
        if (fld.checked == true)	{
          name = 'ON';
        }
        var otra = fld.name+',';
        if (name == 'ON')	{
          if (cade.indexOf(otra) < 0) {
            cade = cade + otra;
            document.forms['frcotoplo']['cCadena'].value = cade;
          }
        }
        if (name == 'OFF')	{
          cade = cade.replace(otra,'');
          document.forms['frcotoplo']['cCadena'].value = cade;
        }
      }
    </script>
  </head>
  <body topmargin="0" leftmargin="0" style="margin-right: 0;">
    <?php
      $mCadena = explode(",",$gTarCli);
      $mClientes = array();
      for ($i=0;$i<count($mCadena);$i++) { 
        if ($mCadena[$i] != "") {
          $mClientes[count($mClientes)] = $mCadena[$i];
        }
      }
    ?>
    <form name="frcotoplo" action="" method="post" target="fmpro">
      <input type="hidden" name="cCadena" value="<?php echo $cCadena ?>" style="width: 500px;" readonly>
    </form>
    <center>
      <table border="0" cellpadding="0" cellspacing="0" width="450">
        <tr>
          <td>
            <fieldset>
              <legend>Clientes</legend>
              <form name="frgrm" action="" method="post" target="fmpro">
                <?php
                  $qCliDat  = "SELECT ";
                  $qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX,";
                  $qCliDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX, ";
                  $qCliDat .= "$cAlfa.SIAI0150.REGESTXX ";
                  $qCliDat .= "FROM $cAlfa.SIAI0150 ";
                  $qCliDat .= "WHERE ";
                  $qCliDat .= "REGESTXX = \"ACTIVO\" ORDER BY CLINOMXX";
                  $xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
                  //f_Mensaje(__FILE__,__LINE__,$qCliDat."~".mysql_num_rows($xCliDat));

                  if (mysql_num_rows($xCliDat) > 0) {
                    ?>
                    <center>
                      <table cellspacing="0" cellpadding="1" border="1" width="450">
                        <tr bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>">
                          <td width="020" class="name"><center></center></td>
                          <td width="100" class="name"><center>Nit</center></td>
                          <td width="280" class="name"><center>Nombre</center></td>
                          <td width="050" class="name"><center>Estado</center></td>
                        </tr>
                        <?php
                          $y=0;
                          $cont=0;
                          while ($zRCom = mysql_fetch_array($xCliDat)) {
                            $serv = $zRCom['CLIIDXXX'];
                            $vb   = $serv;
                            $cvb  = 0;
                            if (in_array($vb,$mClientes) == true) {
                              $cvb = 1;
                            }
                            if ($cvb == 0)	{
                              $y++;
                              $cont++;
                              $zColor = "{$vSysStr['system_row_impar_color_ini']}";
                              if($y % 2 == 0) {
                                $zColor = "{$vSysStr['system_row_par_color_ini']}";
                              }
                              ?>
                              <tr bgcolor="<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')">
                                <td style="width: 020px;" class="letra7"><center><input type="checkbox" style="width: 20px;" name="<?php echo $serv?>" onclick="javascript:fnCo(this)"></center></td>
                                <td style="width: 100px;" class="letra7"><?php echo $zRCom['CLIIDXXX'] ?></td>
                                <td style="width: 280px;" class="letra7"><?php echo substr($zRCom['CLINOMXX'],0,45) ?></td>
                                <td style="width: 050px;" class="letra7"><?php echo $zRCom['REGESTXX'] ?></td>
                              </tr>
                              <?php
                            }
                          }
                        ?>
                      </table>
                    </center>
                    <?php
                    if ($cont == 0) {
                      ?>
                      <script language="javascript">
                        alert('Ya tiene asignados todos los Clientes Existentes.');
                        window.close();
                      </script>
                      <?php
                    }
                    ?>
                    <center>
                      <table border="0" cellpadding="0" cellspacing="0" width="450">
                        <tr height="21">
                          <td width="268" height="21"></td>
                          <td width="91" height="21" class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor: hand;" onclick="javascript:fnGuardar()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
                          <td width="91" height="21" class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor: pointer;" onclick="javascript:window.close()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
                        </tr>
                      </table>
                    </center>
                    <?php
                  } else {
                    f_Mensaje(__FILE__,__LINE__,"No Se Encontraron Registros"); ?>
                    <script language="javascript">
                      window.close();
                    </script>
                  <?php } 
                  ?>
              </form>
            </fieldset>
          </td>
        </tr>
      </table>
    </center>
  </body>
</html>
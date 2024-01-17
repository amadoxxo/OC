<?php
/**
 * Formulario de Texto Solicitud Anticipo DHL.
 * Descripcion: Permite Crear solo una vez un registro de Texto Solicitud Anticipo DHL,
 * si ya existe un registro se cargara la información para actuaizar los datos.
 * 
 * @author Diego Cortes<diego.cortes@openits.co>
 * @package openComex
 */

  include ("../../../libs/php/utility.php");

  /**
   *  Cookie fija
   */
  $kDf = explode("~",$_COOKIE["kDatosFijos"]);
  $kMysqlHost = $kDf[0];
  $kMysqlUser = $kDf[1];
  $kMysqlPass = $kDf[2];
  $kMysqlDb   = $kDf[3];
  $kUser      = $kDf[4];
  $kLicencia  = $kDf[5];
  $swidth     = $kDf[6];
  $kProDes    = $_COOKIE["kProDes"];
  ?>

  <html>
    <head>
      <link rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/estilo.css'>
      <link rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/general.css'>
      <link rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/layout.css'>
      <link rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/custom.css'>
      <script language="javascript">
        function fnRetorna() {
          parent.parent.fmwork.location='<?php echo $cPlesk_Forms_Directory_New ?>/frproces.php';
          parent.parent.fmnav.location='<?php echo $cPlesk_Forms_Directory_New ?>/nivel2.php';
        }

        function fnGuardar(){
					document.forms['frgrm'].submit();
				}
      </script>
    </head>
    <body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
    <center>
      <table border ="0" cellpadding="0" cellspacing="0" width="700">
        <tr>
          <td>
            <fieldset>
              <legend>Texto Solicitud Anticipo DHL</legend>
              <form name = "frgrm" action = "frtsagra.php" method = "post" target = "fmpro">
                <center>
                  <table border = "0" cellpadding = "0" cellspacing = "0" width="700">
                  <?php echo f_Columnas(35,20); ?>
                    <tr>
                      <td Class = "name" colspan = "35">T&iacute;tulo<br>
                        <input type = "text" Class = "letra" name = "cTsaTitu" style = "width:700" maxlength="150"
                            onFocus="javascript:this.style.background='#00FFFF';"
                            onblur = "javascript:this.style.background='#FFFFFF'">
                      </td>
                    </tr>
                    <tr>
                      <td Class = "name" colspan = "35"><br>Contenido <b style="color:red;">(Recuerde para separar los p&aacute;rrafos (enter) se deber&aacute; incluir //)</b><br>
                        <textarea name="cTsaCont" rows="3" style = "width:700; height:70; "
                                  onFocus="javascript:this.style.background='#00FFFF';"
                                  onblur = "javascript:this.style.background='#FFFFFF'"></textarea>
                      </td>
                    </tr>
                    <tr>
                      <td Class = "name" colspan = "7"><br>Creado<br>
                        <input type = "text" Class = "letra"  style = "width:140;text-align:center"  name = "dRegFCre"
                          value = "<?php echo date('Y-m-d') ?>" readonly>
                      </td>
                      <td Class = "name" colspan = "7"><br>Hora<br>
                        <input type = "text" Class = "letra" style = "width:140;text-align:center" name = "hRegHCre"
                          value = "<?php echo date('H:i:s') ?>" readonly>
                      </td>
                      <td Class = "name" colspan = "7"><br>Modificado<br>
                        <input type = "text" Class = "letra"  style = "width:140;text-align:center"  name = "dRegFMod"
                          value = "<?php echo date('Y-m-d') ?>" readonly>
                      </td>
                      <td Class = "name" colspan = "7"><br>Hora<br>
                        <input type = "text" Class = "letra"  style = "width:140;text-align:center"  name = "hRegHMod"
                          value = "<?php echo date('H:i:s') ?>" readonly>
                      </td>
                      <td Class = "name" colspan = "7"><br>Estado<br>
                        <input type = "text" Class = "letra" style = "width:140;text-align:center" name = "cRegEst"
                          value = "ACTIVO" readonly>
                      </td>
                    </tr>
                  </table>
                </center>
              </form>
            </fieldset>
          </td>
        </tr>
      </table>
    </center>
    <center>
      <table border="0" cellpadding="0" cellspacing="0" width="700">
        <tr height="21"> 
          <td width="518" height="21"></td>
          <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_New ?>/btn_ok_bg.gif" style="cursor:pointer"
            onClick = "javascript:fnGuardar();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar
          </td>
          <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_New ?>/btn_cancel_bg.gif" style="cursor:pointer"
            onClick = "javascript:fnRetorna()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
          </td>
        </tr>
      </table>
    </center>

    <?php 
    fnVerficarCargueData();
    function fnVerficarCargueData() {
      global $cAlfa;  global $xConexion01;

      /**
       * Esta tabla solo contendra un registro, se consulta para saber si existe el registro 
       * y traer la información del registro para editarlo, de lo contrario se crea un registro.
       */
      $qTexNotC  = "SELECT * ";
      $qTexNotC .= "FROM $cAlfa.zdex0007 LIMIT 0,1";
      $xTexNotC  = f_MySql("SELECT","",$qTexNotC,$xConexion01,"");

      if (mysql_num_rows($xTexNotC) == 1) {
        $vTexNotC = mysql_fetch_array($xTexNotC); ?>

        <script language = "javascript">
          document.forms['frgrm']['cTsaTitu'].value = "<?php echo str_replace(array('"',"'"),array('\"',"\'"),$vTexNotC['tsatitxx']) ?>";
          document.forms['frgrm']['cTsaCont'].value = "<?php echo str_replace(array('"',"'"),array('\"',"\'"),$vTexNotC['tsacontx']) ?>";
          document.forms['frgrm']['dRegFCre'].value = "<?php echo $vTexNotC['regfcrex'] ?>";
          document.forms['frgrm']['hRegHCre'].value = "<?php echo $vTexNotC['reghcrex'] ?>";
          document.forms['frgrm']['dRegFMod'].value = "<?php echo $vTexNotC['regfmodx'] ?>";
          document.forms['frgrm']['hRegHMod'].value = "<?php echo $vTexNotC['reghmodx'] ?>";
          document.forms['frgrm']['cRegEst'].value  = "<?php echo $vTexNotC['regestxx'] ?>";
        </script>
        <?php 
      }
    }
    ?>

    </body>
  </html>
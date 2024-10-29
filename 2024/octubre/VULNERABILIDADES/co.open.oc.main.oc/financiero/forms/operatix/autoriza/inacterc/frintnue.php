<?php
  namespace openComex;
  /**
   * Nuevo Inactivacion Terceros con Movimiento Contable.
   * --- Descripcion: Permite Crear Nuevo Inactivacion Terceros con Movimiento Contable.
   * @author Juan Jose Trujillo <juan.trujillo@open-eb.co>
   * @package openComex
   */
  include("../../../../libs/php/utility.php");
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
  ?>
  <html>
    <head>
      <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
      <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
      <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
      <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
      <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
      <script language="javascript">
        function fnRetorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
          document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
  				parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
        }

        function fnLinks(xLink,xSwitch,xSecuencia,xGrid,xType) {
          var nX    = screen.width;
          var nY    = screen.height;
          switch (xLink) {
            case "cTerId":
            case "cTerNom":
              if (xLink == "cTerId" || xLink == "cTerNom") {
                var cTerId  = document.forms['frgrm']['cTerId'].value.toUpperCase();
                var cTerNom = document.forms['frgrm']['cTerNom'].value.toUpperCase();
              }
              if (xSwitch == "VALID") {
                var cPathUrl = "frint150.php?gModo="+xSwitch+"&gFunction="+xLink+
                                          "&gTerId="+cTerId+
                                          "&gTerNom="+cTerNom;
                //alert(cPathUrl);
                parent.fmpro.location = cPathUrl;
              } else {
                var nNx      = (nX-600)/2;
                var nNy      = (nY-250)/2;
                var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
                var cPathUrl = "frint150.php?gModo="+xSwitch+"&gFunction="+xLink+
                                          "&gTerId="+cTerId+
                                          "&gTerNom="+cTerNom;
                cWindow = window.open(cPathUrl,xLink,cWinOpt);
                cWindow.focus();
              }
            break;
          }
	      }
      </script>
    </head>
    <body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
      <center>
        <table border ="0" cellpadding="0" cellspacing="0" width="400">
          <tr>
            <td>
              <fieldset>
                <legend><?php echo $_COOKIE['kProDes'] ?></legend>
                <form name = "frgrm" action = "frintgra.php" method = "post" target = "fmpro">

                  <input type="hidden" name="cCliIndice" value="<?php echo $cCliIndice ?>">
                  <center>
                    <table border = "0" cellpadding = "0" cellspacing = "0" width="400">
                      <?php echo f_Columnas(20,20); ?>
                      <tr>
                        <td Class = "name" colspan = "5">
                          <a href = "javascript:document.forms['frgrm']['cTerId'].value  = '';
                                                document.forms['frgrm']['cTerNom'].value = '';
                                                document.forms['frgrm']['cTerDV'].value  = '';
                                                fnLinks('cTerId','VALID')" id="id_href_cTerId"><br>Nit</a><br>
                          <input type = "text" Class = "letra" style = "width:100;text-align:center" name = "cTerId"
                            onfocus="javascript:document.forms['frgrm']['cTerId'].value  = '';
                                                document.forms['frgrm']['cTerNom'].value = '';
                                                document.forms['frgrm']['cTerDV'].value  = '';
                                                this.style.background='#00FFFF'"
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                fnLinks('cTerId','VALID');
                                                this.style.background='#FFFFFF'">
                        </td>
                        <td Class = "name" colspan = "1"><br>Dv<br>
                          <input type = "text" Class = "letra" style = "width:20;text-align:center" name = "cTerDV" readonly>
                        </td>
                        <td Class = "name" colspan = "19"><br>Cliente<br>
                          <input type = "text" Class = "letra" style = "width:280" name = "cTerNom"
                            onfocus="javascript:document.forms['frgrm']['cTerId'].value  = '';
                                                document.forms['frgrm']['cTerNom'].value = '';
                                                document.forms['frgrm']['cTerDV'].value  = '';
                                                this.style.background='#00FFFF'"
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                fnLinks('cTerNom','VALID');
                                                this.style.background='#FFFFFF'">
                        </td>
          	       	  </tr>
                      <tr>
                        <td Class = "name" colspan = "20">Observaci&oacute;n<br>
                          <textarea Class = "letra" name = "cCliObsIn" style = "width:400"></textarea>
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
        <table border="0" cellpadding="0" cellspacing="0" width="400">
          <tr height="21">
            <?php 
            switch ($_COOKIE['kModo']) {
              case "VER": ?>
                <td width="309" height="21"></td>
                <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_New ?>/btn_cancel_bg.gif" style="cursor:hand"
                  onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
                </td>
                <?php 
              break;
              default: ?>
                <td width="218" height="21"></td>
                <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_New ?>/btn_ok_bg.gif" style="cursor:hand"
                  onClick = "javascript:document.forms['frgrm'].submit()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar
                </td>
                <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_New ?>/btn_cancel_bg.gif" style="cursor:hand"
                  onClick = "javascript:fnRetorna()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
                </td>
              <?php 
              break;
            } ?>
          </tr>
        </table>
      </center>
      <!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
      <?php 
      switch ($_COOKIE['kModo']) {
        case "NUEVO":
        // No hace nada
        break;
        case "EDITAR":
          // No hace nada
        break;
        case "VER":
          f_CargaData($cCliIndice);
          ?>
          <script languaje = "javascript">
            document.forms['frgrm']['cTerId'].disabled    = true;
            document.forms['frgrm']['cTerDV'].disabled    = true;
            document.forms['frgrm']['cTerNom'].disabled   = true;
            document.forms['frgrm']['cCliObsIn'].disabled = true;
            document.getElementById('id_href_cTerId').disabled = true;
            document.getElementById('id_href_cTerId').href="javascript:alert('No Permitido');";
          </script>
          <?php 
        break;
      } ?>
      <?php 
      function f_CargaData($xCliIndice) {
        global $xConexion01; global $cAlfa;

        $vDetalle = explode("~", $xCliIndice);

        $qCliObsx  = "SELECT ";
        $qCliObsx  = "SELECT CLIIDXXX, ";
        $qCliObsx .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX, ";
        $qCliObsx .= "$cAlfa.SIAI0150.CLIOBSIN ";
        $qCliObsx .= "FROM $cAlfa.SIAI0150 ";
        $qCliObsx .= "WHERE ";
        $qCliObsx .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$vDetalle[0]}\" LIMIT 0,1";
        $xCliObsx  = f_MySql("SELECT","",$qCliObsx,$xConexion01,"");
        // f_Mensaje(__FILE__,__LINE__,$qCliObsx."~".mysql_num_rows($xCliObsx));
        if(mysql_num_rows($xCliObsx) > 0){

          $vCliObsx = mysql_fetch_array($xCliObsx);
          $vObsInac = f_Explode_Array($vCliObsx['CLIOBSIN'], "|", "___");

          for($j=0;$j<count($vObsInac);$j++){
            $vData   = explode("__", $vObsInac[$j][1]);
            $cIndice = $vCliObsx['CLIIDXXX']."~".$vData[1]."~".$vData[2]."~".$vData[3];

            if ($cIndice == $xCliIndice) {
              ?>
              <script language = "javascript">
                document.forms['frgrm']['cTerId'].value    = "<?php echo $vCliObsx['CLIIDXXX'] ?>"; 
                document.forms['frgrm']['cTerDV'].value    = "<?php echo f_Digito_Verificacion($vCliObsx['CLIIDXXX']) ?>";
                document.forms['frgrm']['cTerNom'].value   = "<?php echo $vCliObsx['CLINOMXX'] ?>";
                document.forms['frgrm']['cCliObsIn'].value = "<?php echo $vData[4] ?>";
              </script>
              <?php 
            }
          }
        }
      }
      ?>
    </body>
  </html>
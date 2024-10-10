<?php 
  namespace openComex;
  /**
   * Valid/Window de Prefacturas Disponibles.
   * --- Descripcion: Permite Visualizar las Prefacturas con las que Hizo Match el Consecutivo Ingresado.
   * @author Cristian Camilo Segura V <cristian.segura@open-eb.co>
   * @package Opencomex
   */

   include("../../../../libs/php/utility.php");

  if (!empty($gModo) && !empty($gFunction) && !empty($gSecuencia) && !empty($gPreAnio) && !empty($gProvien) ) {
  if($gProvien == 'frfplnue') { 
    $colTable = "SUBSTRING_INDEX(SUBSTRING_INDEX(comfacpr, \"-\", -2) , \"-\" ,1 )";  
    $colTabl2 = 'comfacpr';
  }else{
    $colTable = 'comcscxx'; 
    $colTabl2 = 'comcscxx';
  }

  /**
   * Permite definir el valor dinamico de la secuencia, teniendo en cuenta si es el consecutivo 
   * de la factura legalizada o sin legalizar.
   */
  function fnValorSecuncia($xRT, $colTabl2, $posicion, $cName){
    if ($colTabl2 == 'comfacpr') {
      $comfacpr = explode( '-', $xRT['comfacpr'] );
      return $comfacpr[$posicion];
    }else{
      return $xRT[$cName];
    }
  }
  ?>
  <html>
    <head>
      <title>Prefacturas Disponibles</title>
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
      <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
      <script languaje = 'javascript'>
        function f_Carga_Grilla() {
          const d = parent.window.opener.document;
          if (parent.window.opener.document.forms['frgrm']['nSecuencia'].value == "<?php echo $gSecuencia ?>") { // Estoy en la ultima fila.
            var nSwPrv = 0; // Switch de Primera Vez
            for (i=0;i<document.forms['frgrm']['oChkTra'].length;i++){
              if (document.forms['frgrm']['oChkTra'][i].checked == true) {
                if (nSwPrv == 0) { // Si es la primera vez que entre cambio el estado del switch
                  nSwPrv = 1;
                } else { // Si no es la primera vez que entro adiciono una row
                  parent.window.opener.fnAddNewRowPre();
                }
                
                var mComCru = document.forms['frgrm']['oChkTra'][i].id.split("~");
                d.forms['frgrm']['cComId' + d.forms['frgrm']['nSecuencia'].value].value = mComCru[0]; 
                d.forms['frgrm']['cComCod' + d.forms['frgrm']['nSecuencia'].value].value = mComCru[1]; 
                d.forms['frgrm']['cComCsc'+ d.forms['frgrm']['nSecuencia'].value].value = mComCru[2];
                d.forms['frgrm']['cComCsc2'+ d.forms['frgrm']['nSecuencia'].value].value = mComCru[3];
                d.forms['frgrm']['cComFech'+ d.forms['frgrm']['nSecuencia'].value].value = mComCru[4];
                d.forms['frgrm']['cTerId' + d.forms['frgrm']['nSecuencia'].value].value = mComCru[5]; 
                d.forms['frgrm']['cTerIdDv' + d.forms['frgrm']['nSecuencia'].value].value = mComCru[6]; 
                d.forms['frgrm']['cCliNom'+ d.forms['frgrm']['nSecuencia'].value].value = mComCru[7];
                
                d.getElementById('cDocSeq'+d.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                d.getElementById('cDocSeq'+d.forms['frgrm']['nSecuencia'].value).style.color      = "#000000";
                d.getElementById('cComId' +d.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                d.getElementById('cComId' +d.forms['frgrm']['nSecuencia'].value).style.color      = "#000000";
                d.getElementById('cComCod' +d.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                d.getElementById('cComCod' +d.forms['frgrm']['nSecuencia'].value).style.color      = "#000000";
                d.getElementById('cComCsc'+d.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                d.getElementById('cComCsc'+d.forms['frgrm']['nSecuencia'].value).style.color      = "#000000";
                d.getElementById('cComFech'+d.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                d.getElementById('cComFech'+d.forms['frgrm']['nSecuencia'].value).style.color      = "#000000";
                d.getElementById('cTerId' +d.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                d.getElementById('cTerId' +d.forms['frgrm']['nSecuencia'].value).style.color      = "#000000";
                d.getElementById('cTerIdDv' +d.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                d.getElementById('cTerIdDv' +d.forms['frgrm']['nSecuencia'].value).style.color      = "#000000";
                d.getElementById('cCliNom'+d.forms['frgrm']['nSecuencia'].value).style.background = "#FFFFFF";
                d.getElementById('cCliNom'+d.forms['frgrm']['nSecuencia'].value).style.color      = "#000000";
              }
            }
          } else {
            alert("Solo se Puede Ingresar Multiples Registros si esta Ubicado en la Ultima Posicion de los Items, Verifique.");
          }
          parent.window.close();
        }
        
        function f_Marca(xRows) {
        if (document.forms['frgrm']['oChkTraAll'].checked == true){
          if (xRows == 1){
            document.forms['frgrm']['oChkTra'].checked=true;
          } else {
            if (xRows > 1){
              for (i=0;i<document.forms['frgrm']['oChkTra'].length;i++){
                document.forms['frgrm']['oChkTra'][i].checked = true;
              }
            }
          }
        } else {
          if (xRows == 1){
            document.forms['frgrm']['oChkTra'].checked=false;
          } else {
            if (xRows > 1){
              for (i=0;i<document.forms['frgrm']['oChkTra'].length;i++){
                document.forms['frgrm']['oChkTra'][i].checked = false;
              }
            }
          }
        }
      }
      </script>
    </head>
    <body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">

    <center>
      <table border ="0" cellpadding="0" cellspacing="0" width="760">
        <tr>
          <td>
            <fieldset>
              <legend>Prefacturas Disponibles</legend>
              <form name = "frgrm" action = "" method = "post" target = "fmpro">
                <input type = "hidden" name = "mChkTra" value = "">
                <?php
                  switch ($gModo) { 
                    case "VALID":
                      $qTramites  = "SELECT * ";
                      $qTramites .= "FROM $cAlfa.fcoc$gPreAnio ";
                      $qTramites .= "WHERE ";
                      $qTramites .= "comidxxx = \"F\" AND ";
                      $qTramites .= "$colTable LIKE \"%$gComCsc%\" AND ";
                      if ($gProvien == 'frfplnue') {
                        $qTramites .= "(regestxx = \"ACTIVO\" AND (comfprfe != \"\" OR comfprfe != \"0000-00-00\")) ";
                      }else{
                        $qTramites .= "regestxx = \"PROVISIONAL\" ";
                      }
                      $qTramites .= "ORDER BY $colTabl2 ASC ";
                      // echo "<script>alert('".$qTramites."')</script>";
                      $xTramites  = f_MySql("SELECT","",$qTramites,$xConexion01,"");
                      
                      if (mysql_num_rows($xTramites) == 1) {
                        $vTramites = mysql_fetch_array($xTramites);
                        
                        //Busco la el nombre del cliente
                        $qDatCli  = "SELECT ";
                        $qDatCli .= "$cAlfa.SIAI0150.*, ";
                        $qDatCli .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
                        $qDatCli .= "FROM $cAlfa.SIAI0150 ";
                        $qDatCli .= "WHERE ";
                        $qDatCli .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$vTramites['teridxxx']}\" LIMIT 0,1";
                        $xDatCli  = f_MySql("SELECT","",$qDatCli,$xConexion01,"");
                        if(mysql_num_rows($xDatCli) > 0) {
                          $xRDC = mysql_fetch_array($xDatCli);
                          $vTramites['clinomxx'] = $xRDC['CLINOMXX'];
                        } else {
                          $vTramites['clinomxx'] = "CLIENTE SIN NOMBRE";
                        }
                        ?>
                      
                        <script language="javascript">
                          const pfd = parent.fmwork.document;
                          pfd.forms['frgrm']['cComId' + "<?php echo $gSecuencia ?>"].value = "<?php echo fnValorSecuncia($vTramites, $colTabl2, 0, 'comidxxx');?>";
                          pfd.forms['frgrm']['cComCod' + "<?php echo $gSecuencia ?>"].value = "<?php echo fnValorSecuncia($vTramites, $colTabl2, 1, 'comcodxx');?>"; 
                          pfd.forms['frgrm']['cComCsc'+ "<?php echo $gSecuencia ?>"].value = "<?php echo fnValorSecuncia($vTramites, $colTabl2, 2, 'comcscxx');?>"; 
                          //hidden
                          pfd.forms['frgrm']['cComCsc2'+ "<?php echo $gSecuencia ?>"].value = "<?php echo fnValorSecuncia($vTramites, $colTabl2, 3, 'comcsc2x');?>"; 
                          pfd.forms['frgrm']['cComFech'+ "<?php echo $gSecuencia ?>"].value = "<?php 
                          if($gProvien == 'frfplnue') {
                            echo $vTramites['comfprfe']; 
                          }else{ 
                            echo $vTramites['comfecxx'];
                          } ?>";
                          pfd.forms['frgrm']['cTerId' + "<?php echo $gSecuencia ?>"].value = "<?php echo $vTramites['teridxxx']; ?>";
                          pfd.forms['frgrm']['cTerIdDv' + "<?php echo $gSecuencia ?>"].value = "<?php echo f_Digito_Verificacion($vTramites['teridxxx']); ?>";
                          pfd.forms['frgrm']['cCliNom'+ "<?php echo $gSecuencia ?>"].value = "<?php echo $vTramites['clinomxx']; ?>";

                          pfd.getElementById('cDocSeq' + "<?php echo $gSecuencia ?>").style.background = "FFFFFF";
                          pfd.getElementById('cDocSeq' + "<?php echo $gSecuencia ?>").style.color = "#000000";
                          pfd.getElementById('cComId' + "<?php echo $gSecuencia ?>").style.background = "FFFFFF";
                          pfd.getElementById('cComId' + "<?php echo $gSecuencia ?>").style.color = "#000000";
                          pfd.getElementById('cComCod' + "<?php echo $gSecuencia ?>").style.background = "#FFFFFF";
                          pfd.getElementById('cComCod' + "<?php echo $gSecuencia ?>").style.color = "#000000";
                          pfd.getElementById('cComCsc'+ "<?php echo $gSecuencia ?>").style.background = "#FFFFFF";
                          pfd.getElementById('cComCsc'+ "<?php echo $gSecuencia ?>").style.color = "#000000";
                          pfd.getElementById('cComFech'+ "<?php echo $gSecuencia ?>").style.background = "#FFFFFF";
                          pfd.getElementById('cComFech'+ "<?php echo $gSecuencia ?>").style.color = "#000000";
                          pfd.getElementById('cTerId' + "<?php echo $gSecuencia ?>").style.background = "#FFFFFF";
                          pfd.getElementById('cTerId' + "<?php echo $gSecuencia ?>").style.color = "#000000";
                          pfd.getElementById('cTerIdDv' + "<?php echo $gSecuencia ?>").style.background = "#FFFFFF";
                          pfd.getElementById('cTerIdDv' + "<?php echo $gSecuencia ?>").style.color = "#000000";
                        </script>
                        <?php 
                      } else { ?>
                        <script language="javascript">
                          parent.fmwork.fnLinks("<?php echo $gFunction ?>","WINDOW","<?php echo $gSecuencia ?>");
                        </script>
                      <?php }
                    break;
                    case "WINDOW":
                      $qTramites  = "SELECT * ";
                      $qTramites .= "FROM $cAlfa.fcoc$gPreAnio ";
                      $qTramites .= "WHERE ";
                      $qTramites .= "comidxxx = \"F\" AND ";
                      $qTramites .= "$colTable  LIKE \"%$gComCsc%\" AND ";
                      if ($gProvien == 'frfplnue') { 
                        $qTramites .= "(regestxx = \"ACTIVO\" AND (comfprfe != \"\" OR comfprfe != \"0000-00-00\")) ";
                      }else{
                        $qTramites .= "regestxx = \"PROVISIONAL\" ";
                      }
                      $qTramites .= "ORDER BY $colTabl2 ASC ";
                      $xTramites  = f_MySql("SELECT","",$qTramites,$xConexion01,"");
                      // f_Mensaje(__FILE__,__LINE__,$qTramites." ~ ".mysql_num_rows($xTramites));
                      
                      if (mysql_num_rows($xTramites) > 0) { ?>
                        <center>
                        <table cellspacing = "0" cellpadding = "1" border = "1" width = "760">
                          <tr>
                            <td width = "040" Class = "name"><center>ID</center></td>
                            <td width = "120" Class = "name"><center>COD</center></td>
                            <td width = "030" Class = "name"><center>PREFACTURA</center></td>
                            <td width = "100" Class = "name"><center>FECHA</center></td>
                            <td width = "100" Class = "name"><center>NIT</center></td>
                            <td Class = "name"><center>DV</center></td>
                            <td width = "060" Class = "name"><center>IMPORTADOR</center></td>
                            <td width = "020" Class = "name"><center><input type="checkbox" name="oChkTraAll" onClick = "javascript:f_Marca('<?php echo mysql_num_rows($xTramites) ?>')"></center></td>
                          </tr>
                          <?php while ($xRT = mysql_fetch_array($xTramites)) {
                            //Busco la el nombre del cliente
                            $qDatCli  = "SELECT ";
                            $qDatCli .= "$cAlfa.SIAI0150.*, ";
                            $qDatCli .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
                            $qDatCli .= "FROM $cAlfa.SIAI0150 ";
                            $qDatCli .= "WHERE ";
                            $qDatCli .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$xRT['teridxxx']}\" LIMIT 0,1";
                            $xDatCli  = f_MySql("SELECT","",$qDatCli,$xConexion01,"");
                            if(mysql_num_rows($xDatCli) > 0) {
                              $xRDC = mysql_fetch_array($xDatCli);
                              $xRT['clinomxx'] = $xRDC['CLINOMXX'];
                            } else {
                              $xRT['clinomxx'] = "CLIENTE SIN NOMBRE";
                            }
                          ?>
                            <tr bgcolor="#FFFFFF">
                              <td class= "name" align="center"><?php echo fnValorSecuncia($xRT, $colTabl2, 0, 'comidxxx');?></td>
                              <td class= "name" align="center"><?php echo fnValorSecuncia($xRT, $colTabl2, 1, 'comcodxx');?></td>
                              <td class= "name" align="center"><?php echo fnValorSecuncia($xRT, $colTabl2, 2, 'comcscxx');?></td>
                              <td class= "name" align="center"><?php
                              if ($colTabl2 == 'comfacpr') {
                                echo $xRT['comfprfe'];
                              }else{
                                echo $xRT['comfecxx'];
                              }?></td>
                              <td Class = "name" align="center"><?php echo $xRT['teridxxx'] ?></td>
                              <td Class = "name" align="center"><?php echo f_Digito_Verificacion($xRT['teridxxx']); ?></td>
                              <td class= "name" align="center"><?php echo $xRT['clinomxx'] ?></td>

                              <td class= "name" style = "text-align:right">
                                <input type="checkbox" name="oChkTra" value = "<?php echo $i ?>"
                                  id = "<?php ($colTabl2 == 'comfacpr') ? $fecha = $xRT['comfprfe'] : $fecha = $xRT['comfecxx']; 
                                              echo $xRT['comidxxx']."~".
                                                   $xRT['comcodxx']."~".
                                                   fnValorSecuncia($xRT, $colTabl2, 2, 'comcscxx')."~".
                                                   fnValorSecuncia($xRT, $colTabl2, 3, 'comcsc2x')."~".
                                                   $fecha."~".
                                                   $xRT['teridxxx']."~".
                                                   f_Digito_Verificacion($xRT['teridxxx'])."~".
                                                   $xRT['clinomxx']; ?>">
                              </td>
                            </tr>
                          <?php } ?>
                          <tr>
                            <td colspan="8">
                              <center>
                                <input type="button" name="Btn_Aceptar" value = "Aceptar" style="width:100;text-align:center"
                                      onclick="javascript:f_Carga_Grilla();" readonly>
                                <input type="button" name="Btn_Salir"   value = "Salir"   style="width:100;text-align:center"
                                      onclick="javascript:parent.window.close()" readonly>
                              </center>
                            </td>
                          </tr>
                        </table>
                        </center>  
                      <?php } else {
                        f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros, Verifique."); ?>
                        <script language="javascript">
                          const dwod = parent.window.opener.document;
                          dwod.forms['frgrm']['cComId' + '<?php echo $gSecuencia ?>'].value = "";
                          dwod.forms['frgrm']['cComCod' + '<?php echo $gSecuencia ?>'].value = ""; 
                          dwod.forms['frgrm']['cComCsc'+ '<?php echo $gSecuencia ?>'].value = "";
                          dwod.forms['frgrm']['cComCsc2'+ '<?php echo $gSecuencia ?>'].value = "";
                          dwod.forms['frgrm']['cComFech'+ '<?php echo $gSecuencia ?>'].value = "";
                          dwod.forms['frgrm']['cTerId' + '<?php echo $gSecuencia ?>'].value = "";
                          dwod.forms['frgrm']['cTerIdDv' + '<?php echo $gSecuencia ?>'].value = ""; 
                          dwod.forms['frgrm']['cCliNom'+ '<?php echo $gSecuencia ?>'].value = "";

                          dwod.getElementById('cDocSeq' + '<?php echo $gSecuencia ?>').style.background = "#FFFFFF";
                          dwod.getElementById('cDocSeq' + '<?php echo $gSecuencia ?>').style.color = "#000000";
                          dwod.getElementById('cComId' + '<?php echo $gSecuencia ?>').style.background = "#FFFFFF";
                          dwod.getElementById('cComId' + '<?php echo $gSecuencia ?>').style.color = "#000000";
                          dwod.getElementById('cComCod' + '<?php echo $gSecuencia ?>').style.background = "#FFFFFF";
                          dwod.getElementById('cComCod' + '<?php echo $gSecuencia ?>').style.color = "#000000";
                          dwod.getElementById('cComCsc'+ '<?php echo $gSecuencia ?>').style.background = "#FFFFFF";
                          dwod.getElementById('cComCsc'+ '<?php echo $gSecuencia ?>').style.color = "#000000";
                          dwod.getElementById('cComFech'+ '<?php echo $gSecuencia ?>').style.background = "#FFFFFF";
                          dwod.getElementById('cComFech'+ '<?php echo $gSecuencia ?>').style.color = "#000000";
                          dwod.getElementById('cTerId' + '<?php echo $gSecuencia ?>').style.background = "#FFFFFF";
                          dwod.getElementById('cTerId' + '<?php echo $gSecuencia ?>').style.color = "#000000";
                          dwod.getElementById('cTerIdDv' + '<?php echo $gSecuencia ?>').style.background = "#FFFFFF";
                          dwod.getElementById('cTerIdDv' + '<?php echo $gSecuencia ?>').style.color = "#000000";
                          parent.window.close()
                        </script>
                        <?php
                      }
                    break;
                  }
                  ?>
              </form>
            </fieldset>
          </td>
        </tr>
      </table>
    </center>
  </body>
</html>
<?php } else {
  f_Mensaje(__FILE__,__LINE__,"No se Recibieron Parametros Completos, Verifique.");
} ?>
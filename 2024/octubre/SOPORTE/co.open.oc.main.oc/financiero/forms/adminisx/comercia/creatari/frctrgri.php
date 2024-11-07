<?php
  include('../../../../libs/php/utility.php');

  $cTexto  = "<br>";
  $cTexto  .= "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" width=\"500\">";
    $cTexto  .= "<tr bgcolor=\"".$vSysStr['system_row_title_color_ini']."\">";
      $cTexto .= "<td Class = \"clase08\" width = \"20\"><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_create-dir_bg.gif\" onClick =\"javascript:f_Links(\'cCliente\',\'VALID\')\" style = \"cursor:hand\" alt=\"Adicionar Cliente\">" : "")."</center></td>";
      $cTexto .= "<td Class = \"clase08\" width = \"120\">Nit</td>";
      $cTexto .= "<td Class = \"clase08\" width = \"20\">DV</td>";
      $cTexto .= "<td Class = \"clase08\" width = \"540\">Cliente</td>";
    $cTexto  .= "</tr>";
    if ($gTarCli != "") {
      $mMatrizInt = explode(',',$gTarCli);
      $cadena = "";
      $y = 0;
      for ($i=0;$i<count($mMatrizInt);$i++) {
        if ($mMatrizInt[$i] != "") {
          $qCliDat  = "SELECT ";
          $qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX,";
          $qCliDat .= "$cAlfa.SIAI0150.CLISAPXX,";
          $qCliDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX, ";
          $qCliDat .= "$cAlfa.SIAI0150.REGESTXX ";
          $qCliDat .= "FROM $cAlfa.SIAI0150 ";
          $qCliDat .= "WHERE ";
          $qCliDat .= "CLIIDXXX = \"{$mMatrizInt[$i]}\" AND ";
          $qCliDat .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
          $xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
          if (mysql_num_rows($xCliDat) > 0) {
            while ($xRCD = mysql_fetch_array($xCliDat)) {
              $y++;
              if ($xRCD['CLINOMXX'] == "") {
                $xRCD['NOMBREXX'] = $xRCD['NOMBRE'];
              } else {
                $xRCD['NOMBREXX'] = $xRCD['CLINOMXX'];
              }
              $cId  = $xRCD['CLIIDXXX'];
              $cadena .= '|'.$cId.'|';
              $zColor = "{$vSysStr['system_row_impar_color_ini']}";
              if($y % 2 == 0) {
                $zColor = "{$vSysStr['system_row_par_color_ini']}";
              }
              $cTexto .= "<tr bgcolor = \"$zColor\" onmouseover=\"javascript:uRowColor(this,\'".$vSysStr['system_row_select_color_ini']."\')\" onmouseout=\"javascript:uRowColor(this,\'$zColor\')\">";
                $cTexto .= "<td Class = \"clase08\"><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_remove-selected_bg.gif\" onClick =\"javascript:uDelCom(\'$cId\')\" style = \"cursor:hand\" alt=\"Borrar Cliente: ".$mMatrizInt[$i]." - ".substr($xRCD['NOMBRE'],0,60)."\">" : "")."</center></td>";
                $cTexto .= "<td Class = \"clase08\">".substr($xRCD['CLIIDXXX'],0,10)."</td>";
                $cTexto .= "<td Class = \"clase08\">".f_Digito_Verificacion($xRCD['CLIIDXXX'])."</td>";
                $cTexto .= "<td Class = \"clase08\">".substr($xRCD['NOMBREXX'],0,60)."</td>";
              $cTexto .= "</tr>";
            }
          }
        }
      }
    }
  $cTexto .= "</table>"; ?>
<script language="javascript">
  parent.fmwork.document.getElementById('overDivCli').innerHTML = '<?php echo $cTexto ?>';
</script>
<?php 
  namespace openComex;

ini_set("memory_limit","512M");
set_time_limit(0);

//ini_set('error_reporting', E_ERROR);
//ini_set("display_errors","1");
  
include("../../../../libs/php/utility.php"); 
  
switch ($_POST['cModo']) {
  case "PEGARDO":
  
    $nSecuencia = ($_POST['nSecuencia'] == "") ? "001" : $_POST['nSecuencia'];
    
    if ($_POST['cMemo'] == "") {
      f_Mensaje(__FILE__,__LINE__,"Debe Ingresar los Numero de Do, Verifique.");
    } else {
      
      $nCanDo = 0;
        
      $cBuscar = array(","," ",chr(13),chr(10),chr(27),chr(9));
      $cReempl = array("~","~","~","~","~","~");
      
      $_POST['cMemo'] = str_replace($cBuscar,$cReempl,$_POST['cMemo']);
      $vTramAxu = explode("~", $_POST['cMemo']);
      
      $vTramPeg = array();
      for ($nT=0; $nT<count($vTramAxu); $nT++) {
        if ($vTramAxu[$nT] != "") {
          if (in_array($vTramAxu[$nT], $vTramPeg) == false) {
            $vTramPeg[count($vTramPeg)] = $vTramAxu[$nT];
          }
        }
      }
      
      $cTramites = ""; $mTramites = array();
      
      for ($nT=0; $nT<count($vTramPeg); $nT++) {
        if ($vTramPeg[$nT] != "") {
          ##Fin de Buscando los Do##
          $qTramites  = "SELECT * ";
          $qTramites .= "FROM $cAlfa.sys00121 ";
          $qTramites .= "WHERE ";
          $qTramites .= "docidxxx  = \"{$vTramPeg[$nT]}\" AND ";
          $qTramites .= "docfmaxx != \"SI\" AND ";
          $qTramites .= "regestxx  = \"ACTIVO\" ";
          $xTramites  = f_MySql("SELECT","",$qTramites,$xConexion01,"");
          // f_Mensaje(__FILE__, __LINE__, $qTramites."~".mysql_num_rows($xTramites));
          while ($xRT = mysql_fetch_array($xTramites)) {
            //Busco la el nombre del cliente
            $qDatCli  = "SELECT ";
            $qDatCli .= "$cAlfa.SIAI0150.*, ";
            $qDatCli .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
            $qDatCli .= "FROM $cAlfa.SIAI0150 ";
            $qDatCli .= "WHERE ";
            $qDatCli .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$xRT['cliidxxx']}\" LIMIT 0,1";
            $xDatCli  = f_MySql("SELECT","",$qDatCli,$xConexion01,"");
            if(mysql_num_rows($xDatCli) > 0) {
              $vDatCli = mysql_fetch_array($xDatCli);
              $xRT['clinomxx'] = $vDatCli['CLINOMXX'];
              $xRT['cliidxxx'] = $vDatCli['CLIIDXXX'];
            } else {
              $xRT['clinomxx'] = "CLIENTE SIN NOMBRE";
              $xRT['cliidxxx'] = $vDatCli['CLIIDXXX'];
            }
            
            $vTramites[count($vTramites)] = "{$xRT['sucidxxx']}-{$xRT['docidxxx']}-{$xRT['docsufxx']}";
            $cColor = (($xRT['doctipxx'] == "REGISTRO") ? "red" : "");
            $nCanDo++; 
            ?>
            <script languaje = "javascript">
              if ("<?php echo $cColor ?>" == "red") {
                var cBgColor = "#FF0000";
                var cColor   = "#FFFFFF";
              } else {
                var cBgColor = "#FFFFFF";
                var cColor   = "#000000";
              }
              
              if ("<?php echo str_pad($nSecuencia,3,"0",STR_PAD_LEFT) ?>" != "<?php echo str_pad($_POST['nSecuencia'],3,"0",STR_PAD_LEFT) ?>") {
                parent.window.opener.f_Add_New_Row_Do();  
              }
                
              var nSecuencia = parent.window.opener.document.forms['frgrm']['nSecuencia'].value;
              parent.window.opener.document.forms['frgrm']['cSucId' +nSecuencia].value = '<?php echo $xRT['sucidxxx'] ?>';
              parent.window.opener.document.forms['frgrm']['cDocId' +nSecuencia].value = '<?php echo $xRT['docidxxx']  ?>';
              parent.window.opener.document.forms['frgrm']['cDocSuf'+nSecuencia].value = '<?php echo $xRT['docsufxx']  ?>';
              parent.window.opener.document.forms['frgrm']['cDocTip'+nSecuencia].value = '<?php echo $xRT['doctipxx']  ?>';
              parent.window.opener.document.forms['frgrm']['cCliId' +nSecuencia].value = '<?php echo $xRT['cliidxxx']  ?>';
              parent.window.opener.document.forms['frgrm']['cCliDv' +nSecuencia].value = '<?php echo f_Digito_Verificacion($xRT['cliidxxx'])  ?>';
              parent.window.opener.document.forms['frgrm']['cCliNom'+nSecuencia].value = '<?php echo $xRT['clinomxx']  ?>';
              
              parent.window.opener.document.forms['frgrm']['cDocSeq'+nSecuencia].style.backgroundColor = cBgColor;
              parent.window.opener.document.forms['frgrm']['cDocSeq'+nSecuencia].style.color = cColor;
              parent.window.opener.document.forms['frgrm']['cSucId' +nSecuencia].style.backgroundColor = cBgColor;
              parent.window.opener.document.forms['frgrm']['cSucId' +nSecuencia].style.color = cColor;
              parent.window.opener.document.forms['frgrm']['cDocId' +nSecuencia].style.backgroundColor = cBgColor;
              parent.window.opener.document.forms['frgrm']['cDocId' +nSecuencia].style.color = cColor;
              parent.window.opener.document.forms['frgrm']['cDocSuf'+nSecuencia].style.backgroundColor = cBgColor;
              parent.window.opener.document.forms['frgrm']['cDocSuf'+nSecuencia].style.color = cColor;
              parent.window.opener.document.forms['frgrm']['cDocTip'+nSecuencia].style.backgroundColor = cBgColor;
              parent.window.opener.document.forms['frgrm']['cDocTip'+nSecuencia].style.color = cColor;
              parent.window.opener.document.forms['frgrm']['cCliId' +nSecuencia].style.backgroundColor = cBgColor;
              parent.window.opener.document.forms['frgrm']['cCliId' +nSecuencia].style.color = cColor;
              parent.window.opener.document.forms['frgrm']['cCliDv' +nSecuencia].style.backgroundColor = cBgColor;
              parent.window.opener.document.forms['frgrm']['cCliDv' +nSecuencia].style.color = cColor;
              parent.window.opener.document.forms['frgrm']['cCliNom'+nSecuencia].style.backgroundColor = cBgColor;
              parent.window.opener.document.forms['frgrm']['cCliNom'+nSecuencia].style.color = cColor;
            </script>
            <?php $nSecuencia++;
            
          } ## while ($xRT = mysql_fetch_array($xTramites)) { ##
          ##Fin de Buscando los Do##
        } 
      }
      if ($nCanDo == 0) {
        f_Mensaje(__FILE__,__LINE__,"No se encontraron registros.");
      }
    } ?>
    <script languaje = "javascript">
      //cerrar la ventana
      parent.window.close();  
    </script>
  <?php break;
  default:
    //No hace nada  
  break;
}
?>
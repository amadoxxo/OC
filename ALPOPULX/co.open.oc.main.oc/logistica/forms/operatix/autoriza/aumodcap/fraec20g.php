<?php // Hola Mundo ...
ini_set("memory_limit","512M");
set_time_limit(0);
 
//ini_set('error_reporting', E_ERROR);
//ini_set("display_errors","1");
  
include("../../../../../financiero/libs/php/utility.php"); 
  
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
          $qTramites .= "(doctexxx = \"\" OR ";
          $qTramites .= "doctexxx IS NULL) AND ";
          $qTramites .= "regestxx  != \"INACTIVO\" ";
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
            // f_Mensaje(__FILE__, __LINE__, $qDatCli."~".mysql_num_rows($xDatCli));
            if(mysql_num_rows($xDatCli) > 0) {
              $vDatCli = mysql_fetch_array($xDatCli);
              $xRT['clinomxx'] = $vDatCli['CLINOMXX'];
              $xRT['cliidxxx'] = $vDatCli['CLIIDXXX'];
            } else {
              $xRT['clinomxx'] = "CLIENTE SIN NOMBRE";
              $xRT['cliidxxx'] = $xRT['cliidxxx'];
            }
            
            $vTramites[count($vTramites)] = "{$xRT['sucidxxx']}-{$xRT['docidxxx']}-{$xRT['docsufxx']}";
            $cColor = (($xRT['doctipxx'] == "REGISTRO") ? "red" : "");
            $nCanDo++; 
            
            //Buscando condiciones comerciales del cliente
            $qConCom  = "SELECT cccaplfa ";
            $qConCom .= "FROM $cAlfa.fpar0151 ";
            $qConCom .= "WHERE ";
            $qConCom .= "cliidxxx = \"{$xRT['cliidxxx']}\" AND  ";
            $qConCom .= "regestxx = \"ACTIVO\" LIMIT 0,1";
            $xConCom  = f_MySql("SELECT","",$qConCom,$xConexion01,"");
            // f_Mensaje(__FILE__,__LINE__,"Cliente ->".$qConCom." ~ ".mysql_num_rows($xConCom));
            $cCcAplFa = "";
            if (mysql_num_rows($xConCom) > 0) {
              $xRCC = mysql_fetch_array($xConCom);
              $cCcAplFa = $xRCC['cccaplfa'];
            }
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
              
              if ("<?php echo $cCcAplFa ?>" == "SI") {
                parent.window.opener.document.forms['frgrm']['cTerIdInt' +nSecuencia].value = '<?php echo $xRT['cliidxxx']  ?>';
                parent.window.opener.document.forms['frgrm']['cTerDVInt' +nSecuencia].value = '<?php echo f_Digito_Verificacion($xRT['cliidxxx']) ?>';
                parent.window.opener.document.forms['frgrm']['cTerNomInt'+nSecuencia].value = '<?php echo $xRT['clinomxx']  ?>';
                parent.window.opener.document.forms['frgrm']['cCcAplFa'  +nSecuencia].value = '<?php echo $cCcAplFa  ?>';
                parent.window.opener.document.forms['frgrm']['cTerIdInt' +nSecuencia].readOnly = false;
              }
              
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
              
              parent.window.opener.document.forms['frgrm']['cTerIdInt'+nSecuencia].style.backgroundColor  = cBgColor;
              parent.window.opener.document.forms['frgrm']['cTerIdInt'+nSecuencia].style.color = cColor;
              parent.window.opener.document.forms['frgrm']['cTerDVInt'+nSecuencia].style.backgroundColor  = cBgColor;
              parent.window.opener.document.forms['frgrm']['cTerDVInt'+nSecuencia].style.color = cColor;
              parent.window.opener.document.forms['frgrm']['cTerNomInt'+nSecuencia].style.backgroundColor = cBgColor;
              parent.window.opener.document.forms['frgrm']['cTerNomInt'+nSecuencia].style.color = cColor;
              
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
  case "VALIDARDO":
    // for ($i=0; $i<$_POST['nSecuencia']; $i++) {
    //   if ($_POST['cSucId' .($i+1)] != "" && $_POST['cDocId' .($i+1)] != "" && $_POST['cDocSuf' .($i+1)] != "") {
    //     $qTramites  = "SELECT sucidxxx, docidxxx, docsufxx, doctipxx, regestxx ";
    //     $qTramites .= "FROM $cAlfa.sys00121 ";
    //     $qTramites .= "WHERE ";
    //     $qTramites .= "sucidxxx  = \"{$_POST['cSucId' .($i+1)]}\" AND ";
    //     $qTramites .= "docidxxx  = \"{$_POST['cDocId' .($i+1)]}\" AND ";
    //     $qTramites .= "docsufxx  = \"{$_POST['cDocSuf'.($i+1)]}\" AND ";
    //     $qTramites .= "regestxx != \"INACTIVO\" LIMIT 0,1 ";
    //     $xTramites  = f_MySql("SELECT","",$qTramites,$xConexion01,"");
    //     // f_Mensaje(__FILE__,__LINE__,$qTramites." ~ ".mysql_num_rows($xTramites));
    //     if (mysql_num_rows($xTramites) == 0) {
    //       $nSwitch = 1;
    //       $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
    //       $cMsj .= "El Do [{$_POST['cSucId' .($i+1)]}-{$_POST['cDocId' .($i+1)]}-{$_POST['cDocSuf' .($i+1)]}] No Existe, o se encuentra en estado INACTIVO.\n";
    //     } 
    //   } else {
    //     $nSwitch = 1;
    //     $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
    //     $cMsj .= "Debe Selecionar un Do en la Secuencia {$_POST['cDocSeq' .($i+1)]}.\n";
    //   }
      
    //   if ($_POST['cCcAplFa' .($i+1)] == "SI") {
    //     //Validando que el facturar a exista
    //     $qFacA  = "SELECT ";
    //     $qFacA .= "$cAlfa.SIAI0150.CLIIDXXX ";
    //     $qFacA .= "FROM $cAlfa.SIAI0150 ";
    //     $qFacA .= "WHERE ";
    //     $qFacA .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$_POST['cTerIdInt' .($i+1)]}\" AND ";
    //     $qFacA .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" LIMIT 0,1";
    //     $xFacA  = f_MySql("SELECT","",$qFacA,$xConexion01,"");
    //     if (mysql_num_rows($xFacA) == 0) {
    //       $nSwitch = 1;
    //       $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
    //       $cMsj .= "El Facturar a en la Secuencia {$_POST['cDocSeq' .($i+1)]} No Exite o Se Encuentra Inactivo.\n";
    //     }
    //   }
    // } ## for ($i=0; $i<$_POST['nSecuencia']; $i++) { ##
    
    if ($nSwitch == 0) {   ?>
      <script language="javascript">
        parent.fmwork.document.forms['frgrm']['cStep'].value     = "2";
        parent.fmwork.document.forms['frgrm']['cStep_Ant'].value = "1";
        
        parent.fmwork.document.forms['frgrm'].target = "fmwork";
        parent.fmwork.document.forms['frgrm'].action = "framcnue.php";
        parent.fmwork.document.forms['frgrm'].submit();
      </script>
    <?php } else {
      f_Mensaje(__FILE__,__LINE__,$cMsj); ?>
      <script language="javascript">
        parent.fmwork.document.forms['frgrm']['cStep'].value     = "1";
        parent.fmwork.document.forms['frgrm']['cStep_Ant'].value = "";
        parent.fmwork.document.forms['frgrm'].action = "<?php echo $cArcPaso?>";
      </script>
    <?php }
    if ($nSwitch == 1) {
      f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique");
    }
  break;
  default:
    //No hace nada  
  break;
}
?>
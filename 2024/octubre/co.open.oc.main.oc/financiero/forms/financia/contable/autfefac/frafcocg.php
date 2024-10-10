<?php 
  namespace openComex;
  /**
   * Pegar Prefacturas Validacion
   * --- Descripcion: Permite Validar si los Codigos Ingresados por Cada Linea Existen para Mostrarlos en el Formulario.
   * @author Cristian Camilo Segura V <cristian.segura@open-eb.co>
   * @package Opencomex
   */

ini_set("memory_limit","512M");
set_time_limit(0);
 
//ini_set('error_reporting', E_ERROR);
//ini_set("display_errors","1");
  
include("../../../../libs/php/utility.php"); 

/**
 * Permite definir el valor dinamico de la secuencia, teniendo en cuenta si es el consecutivo 
 * de la factura legalizada o sin legalizar.
 */
function fnValorSecuncia($xRT, $colTable, $posicion, $cName){
  if ($colTable == 'comfacpr') {
    $comfacpr = explode('-', $xRT['comfacpr'] );
    echo $comfacpr[$posicion];
  }else{
    echo $xRT[$cName];
  }
}

switch ($_POST['cModo']) {
  case "PEGARPREFACTURADA":
  
    $nSecuencia = ($_POST['nSecuencia'] == "") ? "001" : $_POST['nSecuencia']; 
    $_POST['cProvien'] == 'frfplnue' ?  $colTable = 'comfacpr' :  $colTable = 'comcscxx';    
    if ($_POST['cMemo'] == "") {
      f_Mensaje(__FILE__,__LINE__,"Debe Ingresar los codigos de la PREFACTURA, Verifique.");
    } else {
      
      $nCanPre = 0;
        
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
          if ($colTable == 'comfacpr') {
            $tipocons = "\"{$vTramPeg[$nT]}\" = SUBSTRING_INDEX(SUBSTRING_INDEX($colTable, \"-\", -2) , \"-\" ,1) ";
          }else{
            $tipocons = "$colTable = \"{$vTramPeg[$nT]}\" ";
          }
          $qTramites  = "SELECT * ";
          $qTramites .= "FROM $cAlfa.fcoc".$_POST['cPreAnio']."  ";
          $qTramites .= "WHERE ";
          $qTramites .= $tipocons;
          $qTramites .= "AND comidxxx = \"F\" AND ";
          if ($colTable == 'comfacpr') {
            $qTramites .= "(regestxx = \"ACTIVO\" AND (comfprfe != \"\" OR comfprfe != \"0000-00-00\")) ";
          }else{
            $qTramites .= "regestxx = \"PROVISIONAL\" ";
          }
          $xTramites  = f_MySql("SELECT","",$qTramites,$xConexion01,"");

          while ($xRT = mysql_fetch_array($xTramites)) {
            //Busco la el nombre del cliente
            $qDatCli  = "SELECT ";
            $qDatCli .= "$cAlfa.SIAI0150.*, ";
            $qDatCli .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
            $qDatCli .= "FROM $cAlfa.SIAI0150 ";
            $qDatCli .= "WHERE ";
            $qDatCli .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$xRT['teridxxx']}\" LIMIT 0,1";
            $xDatCli  = f_MySql("SELECT","",$qDatCli,$xConexion01,"");
            if(mysql_num_rows($xDatCli) > 0) {
              $vDatCli = mysql_fetch_array($xDatCli);
              $xRT['clinomxx'] = $vDatCli['CLINOMXX'];
              $xRT['cliidxxx'] = $vDatCli['CLIIDXXX'];
            } else {
              $xRT['clinomxx'] = "CLIENTE SIN NOMBRE";
              $xRT['cliidxxx'] = $vDatCli['CLIIDXXX'];
            }  
           // $vTramites[count($vTramites)] = "{$xRT['sucidxxx']}-{$xRT['docidxxx']}-{$xRT['docsufxx']}";
            $cColor = "";
            $nCanPre++; 
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
                parent.window.opener.fnAddNewRowPre();  
              }                
              var nSecuencia = parent.window.opener.document.forms['frgrm']['nSecuencia'].value;
              parent.window.opener.document.forms['frgrm']['cComId' +nSecuencia].value = '<?php fnValorSecuncia($xRT, $colTable, 0, 'comidxxx');?>';
              parent.window.opener.document.forms['frgrm']['cComCod' +nSecuencia].value = '<?php fnValorSecuncia($xRT, $colTable, 1, 'comcodxx');?>';
              parent.window.opener.document.forms['frgrm']['cComCsc'+nSecuencia].value = '<?php fnValorSecuncia($xRT, $colTable, 2, 'comcscxx');?>';
              //hidden
              parent.window.opener.document.forms['frgrm']['cComCsc2'+nSecuencia].value = '<?php fnValorSecuncia($xRT, $colTable, 3, 'comcsc2x');?>';
              parent.window.opener.document.forms['frgrm']['cComFech'+nSecuencia].value = '<?php 
              if ($colTable == 'comfacpr') {
                echo $xRT['comfprfe'];
              }else{
                echo $xRT['comfecxx'];
              }?>';
              parent.window.opener.document.forms['frgrm']['cTerId' +nSecuencia].value = '<?php echo $xRT['teridxxx']  ?>';
              parent.window.opener.document.forms['frgrm']['cTerIdDv' +nSecuencia].value = '<?php echo f_Digito_Verificacion($xRT['cliidxxx'])  ?>';
              parent.window.opener.document.forms['frgrm']['cCliNom'+nSecuencia].value = '<?php echo $xRT['clinomxx']  ?>';
              
              parent.window.opener.document.forms['frgrm']['cDocSeq'+nSecuencia].style.backgroundColor = cBgColor;
              parent.window.opener.document.forms['frgrm']['cDocSeq'+nSecuencia].style.color = cColor;
              parent.window.opener.document.forms['frgrm']['cComId' +nSecuencia].style.backgroundColor = cBgColor;
              parent.window.opener.document.forms['frgrm']['cComId' +nSecuencia].style.color = cColor;
              parent.window.opener.document.forms['frgrm']['cComCod' +nSecuencia].style.backgroundColor = cBgColor;
              parent.window.opener.document.forms['frgrm']['cComCod' +nSecuencia].style.color = cColor;
              parent.window.opener.document.forms['frgrm']['cComCsc'+nSecuencia].style.backgroundColor = cBgColor;
              parent.window.opener.document.forms['frgrm']['cComCsc'+nSecuencia].style.color = cColor;
              parent.window.opener.document.forms['frgrm']['cComFech'+nSecuencia].style.backgroundColor = cBgColor;
              parent.window.opener.document.forms['frgrm']['cComFech'+nSecuencia].style.color = cColor;
              parent.window.opener.document.forms['frgrm']['cTerId' +nSecuencia].style.backgroundColor = cBgColor;
              parent.window.opener.document.forms['frgrm']['cTerId' +nSecuencia].style.color = cColor;
              parent.window.opener.document.forms['frgrm']['cTerIdDv' +nSecuencia].style.backgroundColor = cBgColor;
              parent.window.opener.document.forms['frgrm']['cTerIdDv' +nSecuencia].style.color = cColor;
              parent.window.opener.document.forms['frgrm']['cCliNom'+nSecuencia].style.backgroundColor = cBgColor;
              parent.window.opener.document.forms['frgrm']['cCliNom'+nSecuencia].style.color = cColor;
            </script>
            <?php $nSecuencia++;
            
          } ## while ($xRT = mysql_fetch_array($xTramites)) { ##
          ##Fin de Buscando los Do##
        } 
      }
      if ($nCanPre == 0) {
        f_Mensaje(__FILE__,__LINE__,"No se encontraron registros.");
      }
    } ?>
    <script languaje = "javascript">
     parent.window.close();  
    </script>
  <?php break;
  default:
  break;
}
  
  ?>
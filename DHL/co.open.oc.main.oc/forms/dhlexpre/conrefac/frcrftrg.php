<?php
  /**
	 * Permite Eliminar y Cargar los Conceptos de Cobro en los Conceptos Reporte DHL.
	 * @author Elian Amado Ramirez <elian.amado@openits.co>
	 */
	include("../../../libs/php/utility.php");

  switch ($tipsave) {
    case "4": //Eliminar Responsabilidad Fiscal
      $fl = 1;
      if(strlen($cIntId)== 0){
        $fl = 0;
        f_Mensaje(__FILE__,__LINE__,"Datos Incompletos.".$cIntId);
      }
      if ($fl == 1) {
        $cCadena = "";
        if ($cColCtoId != "") {
          $mMatrizInt = explode("~",$cColCtoId);
          for ($i=0;$i<count($mMatrizInt);$i++) {
            if ($mMatrizInt[$i] != "") {
              if ($mMatrizInt[$i] != $cIntId) {
                $cCadena .= $mMatrizInt[$i]."~";
              }
            }
          }
          if (strlen($cCadena) > 2) {
            $cCadena = substr($cCadena,0,strlen($cCadena)-1);
          }
        }

        if ($cCadena == "~")  {
          $cCadena = "";
        } ?>
          <script languaje = 'javascript'>
            parent.fmwork.document.forms['frnav']['cColCtoId'].value = "<?php echo $cCadena ?>";
            parent.fmwork.fnCargarGrillas();
          </script>
      <?php }
    break;
    case "5":
      if ($cColCtoId != "") {
        $vCadenaAnt = $cColCtoId;
      }

      if (strlen($cCadena) > 0) {
        $mMatrizVen = explode("~",$cCadena);
        $zMatrizAnt = explode("~",$vCadenaAnt);
        
        $vCadenaAnt = ($vCadenaAnt != "") ? $vCadenaAnt."~" : "";
        for ($i=0;$i<count($mMatrizVen);$i++) {
          if (in_array($mMatrizVen[$i],$zMatrizAnt) == false) {
            if(trim($mMatrizVen[$i]) != ""){
              $vCadenaAnt .= $mMatrizVen[$i]."~";
            }
          }
        }
        $vCadenaAnt = substr($vCadenaAnt,0,strlen($vCadenaAnt)-1);
      }
      
      $cMsj = "";
      if (strlen($cCadena) > 0){
        $cMsj = "true|".trim($vCadenaAnt);
      } else {
        $cMsj = "false|No selecciono ninguna Responsabilidad Fiscal para adicionar.";
      }
      echo $cMsj;
    break;
    default:
      //No hace Nada
    break;
  }
?>
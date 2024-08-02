<?php
  /**
   * Eliminar y Cargar Tipo de Ticket.
   * --- Descripcion: Permite eliminar cargar los Responsables Asignados en el formulario de Tipos de Ticket.
   * @author cristian.perdomo@openits.co
   * @package opencomex
   * @version 001
   */

  include("../../../../../financiero/libs/php/utility.php");

  switch ($tipsave) {
    case "1": //Cargar Responsable
      if ($cReAsig != "") {
        $vCadenaAnt = $cReAsig;
      }

      if (strlen($cCadena) > 0) {
        $mMatrizOrg = explode("~",$cCadena);
        $zMatrizAnt = explode("~",$vCadenaAnt);
        
        $vCadenaAnt = ($vCadenaAnt != "") ? $vCadenaAnt."~" : "";
        for ($i=0;$i<count($mMatrizOrg);$i++) {
          if (in_array($mMatrizOrg[$i],$zMatrizAnt) == false) {
            if(trim($mMatrizOrg[$i]) != ""){
              $vCadenaAnt .= $mMatrizOrg[$i]."~";
            }
          }
        }
        $vCadenaAnt = substr($vCadenaAnt,0,strlen($vCadenaAnt)-1);
      }

      $cMsj = "";
      if (strlen($cCadena) > 0){
        $cMsj = "true|".trim($vCadenaAnt);
      } else {
        $cMsj = "false|No selecciono ningun responsable para adicionar.";
      }
      echo $cMsj;
    break;
    case "2": //Eliminar Organizacion de venta
      $fl = 1;
      if(strlen($cIntId)== 0){
        $fl = 0;
        f_Mensaje(__FILE__,__LINE__,"Datos Incompletos.".$cIntId);
      }
      if ($fl == 1) {
        $cCadena = "";
        if ($cReAsig != "") {
          $mMatrizInt = explode("~",$cReAsig);
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
            parent.fmwork.document.forms['frgrm']['cReAsig'].value = "<?php echo $cCadena ?>";
            parent.fmwork.fnCargarGrillas();
          </script>
      <?php }
    break;
    default:
      //No hace Nada
    break;
  }
?>
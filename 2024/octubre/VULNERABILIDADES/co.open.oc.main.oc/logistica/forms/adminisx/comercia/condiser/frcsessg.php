<?php
  namespace openComex;
  /**
   * Eliminar y Cargar Subservicios.
   * --- Descripcion: Permite eliminar cargar los Subservicios en el formulario de Condiciones Comerciales.
   * @author juan.trujillo@openits.co
   * @package opencomex
   * @version 001
   */

  include("../../../../../financiero/libs/php/utility.php");

  switch ($tipsave) {
    case "1": //Cargar Subservicios
      if ($cCseSubServ != "") {
        $vCadenaAnt = $cCseSubServ;
      }

      if (strlen($cCadena) > 0) {
        $mMatrizSub = explode("~",$cCadena);
        $zMatrizAnt = explode("~",$vCadenaAnt);
        
        $vCadenaAnt = ($vCadenaAnt != "") ? $vCadenaAnt."~" : "";
        for ($i=0;$i<count($mMatrizSub);$i++) {
          if (in_array($mMatrizSub[$i],$zMatrizAnt) == false) {
            if(trim($mMatrizSub[$i]) != ""){
              $vCadenaAnt .= $mMatrizSub[$i]."~";
            }
          }
        }
        $vCadenaAnt = substr($vCadenaAnt,0,strlen($vCadenaAnt)-1);
      }

      $cMsj = "";
      if (strlen($cCadena) > 0){
        $cMsj = "true|".trim($vCadenaAnt);
      } else {
        $cMsj = "false|No selecciono ningun Subservicio para adicionar.";
      }
      echo $cMsj;
    break;
    case "2": //Eliminar Subservicios
      $fl = 1;
      if(strlen($cIntId)== 0){
        $fl = 0;
        f_Mensaje(__FILE__,__LINE__,"Datos Incompletos.".$cIntId);
      }
      if ($fl == 1) {
        $cCadena = "";
        if ($cCseSubServ != "") {
          $mMatrizInt = explode("~",$cCseSubServ);
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
            parent.fmwork.document.forms['frgrm']['cCseSubServ'].value = "<?php echo $cCadena ?>";
            parent.fmwork.fnCargarSubservicios();
          </script>
      <?php }
    break;
    default:
      //No hace Nada
    break;
  }
?>
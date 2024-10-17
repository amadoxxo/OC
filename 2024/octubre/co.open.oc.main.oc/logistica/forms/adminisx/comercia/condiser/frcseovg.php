<?php
  namespace openComex;
  /**
   * Eliminar y Cargar Organizacion de Venta.
   * --- Descripcion: Permite eliminar cargar los Organizacion de Venta en el formulario de Condiciones Comerciales.
   * @author juan.trujillo@openits.co
   * @package opencomex
   * @version 001
   */

  include("../../../../../financiero/libs/php/utility.php");

  switch ($tipsave) {
    case "1": //Cargar Organizacion de Venta
      if ($cCseOrgVenta != "") {
        $vCadenaAnt = $cCseOrgVenta;
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
        $cMsj = "false|No selecciono ninguna organizacion de venta para adicionar.";
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
        if ($cCseOrgVenta != "") {
          $mMatrizInt = explode("~",$cCseOrgVenta);
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
            parent.fmwork.document.forms['frgrm']['cCseOrgVenta'].value = "<?php echo $cCadena ?>";
            parent.fmwork.document.forms['frgrm']['cCseOfiVenta_' + '<?php echo $cIntId ?>'].value = "";
            parent.fmwork.fnCargarOrganizacionVentas();

            setTimeout(function(){
              parent.fmwork.fnCargarOficinaVentas();
            }, 1000);
          </script>
      <?php }
    break;
    case "3": //Cargar Oficina de Venta
      if ($cCseOfiVenta != "") {
        $vCadenaAnt = $cCseOfiVenta;
      }

      if (strlen($cCadena) > 0) {
        $mMatrizOfi = explode("~",$cCadena);
        $zMatrizAnt = explode("~",$vCadenaAnt);
        
        $vCadenaAnt = ($vCadenaAnt != "") ? $vCadenaAnt."~" : "";
        for ($i=0;$i<count($mMatrizOfi);$i++) {
          if (in_array($mMatrizOfi[$i],$zMatrizAnt) == false) {
            if(trim($mMatrizOfi[$i]) != ""){
              $vCadenaAnt .= $mMatrizOfi[$i]."~";
            }
          }
        }
        $vCadenaAnt = substr($vCadenaAnt,0,strlen($vCadenaAnt)-1);
      }

      $cMsj = "";
      if (strlen($cCadena) > 0){
        $cMsj = "true|".trim($vCadenaAnt);
      } else {
        $cMsj = "false|No selecciono ninguna oficina de venta para adicionar.";
      }

      echo $cMsj;
    break;
    case "4": //Eliminar Oficina de venta
      $fl = 1;
      if(strlen($cIntId)== 0){
        $fl = 0;
        f_Mensaje(__FILE__,__LINE__,"Datos Incompletos.".$cIntId);
      }
      if ($fl == 1) {
        $cCadena = "";
        if ($cCseOfiVenta != "") {
          $mMatrizInt = explode("~",$cCseOfiVenta);
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
            parent.fmwork.document.forms['frgrm']['cCseOfiVenta_' + '<?php echo $cCseOrgVenta ?>'].value = "<?php echo $cCadena ?>";
            parent.fmwork.fnCargarOficinaVentas();
          </script>
      <?php }
    break;
    default:
      //No hace Nada
    break;
  }
?>
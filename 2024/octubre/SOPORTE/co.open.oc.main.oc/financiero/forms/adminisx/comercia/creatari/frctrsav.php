<?php
  include('../../../../libs/php/utility.php');

  switch ($tipsave) {
    case '4': //Eliminar Cliente
      $fl = 1;
      if(strlen($cIntId)== 0){
        $fl = 0;
        f_Mensaje(__FILE__,__LINE__,"Datos Incompletos.".$cIntId);
      }
      if ($fl == 1) {
        $cCadena = "";
        
        if ($cTarCli != "") {
          $mMatrizInt = explode(",",$cTarCli);
          for ($i=0;$i<count($mMatrizInt);$i++) {
            if ($mMatrizInt[$i] != "") {
              if ($mMatrizInt[$i] != $cIntId) {
                $cCadena .= $mMatrizInt[$i].",";
              }
            }
          }

          if (strlen($cCadena) > 2) {
            $cCadena = substr($cCadena,0,strlen($cCadena)-1);
          }
        }

        if ($cCadena == ",") {
          $cCadena = "";
        } ?>
          <script language="javascript">
            parent.fmwork.document.forms['frgrm']['cTarCli'].value = "<?php echo $cCadena ?>";
            parent.fmwork.fnCargarGrilla();
          </script>
      <?php }
    break;
    case '5': //Guardar Cliente
      if ($cTarCli != "") {
        $vCadenaAnt = $cTarCli;
      }

      if (strlen($cCadena) > 0) {
        $mMatrizVen = explode(",",$cCadena);
        $zMatrizAnt = explode(",",$vCadenaAnt);
        
        $vCadenaAnt = ($vCadenaAnt != "") ? $vCadenaAnt."," : "";
        for ($i=0;$i<count($mMatrizVen);$i++) {
          if (in_array($mMatrizVen[$i],$zMatrizAnt) == false) {
            $vCadenaAnt .= $mMatrizVen[$i].",";
          }
        }
        $vCadenaAnt = substr($vCadenaAnt,0,strlen($vCadenaAnt)-1);
      }
      
      $cMsj = "";
      if (strlen($cCadena) > 0){
        $cMsj = "true|".trim($vCadenaAnt);
      } else {
        $cMsj = "false|No selecciono ningun Cliente para adicionar.";
      }
      echo $cMsj;
    break;
  }
?>
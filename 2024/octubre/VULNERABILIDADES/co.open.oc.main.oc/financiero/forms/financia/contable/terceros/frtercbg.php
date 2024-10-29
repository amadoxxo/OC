<?php
  namespace openComex;
  /**
	 * Eliminar y cargar cuentas bancarias en la ficha de terceros
	 * @author Hair Zabala C <hair.zabala@open-eb.co>
	 */
	include("../../../../libs/php/utility.php");
  
  switch ($tipsave) {
    case "4": //Eliminar Cuenta Bancaria

      /**
       * Variable para controlar los errores en el proceso.
       * @var number
       */
      $nSwitch = 0;

      $fl = 1;
      if(strlen($cIntId)== 0){
        $fl = 0;
        f_Mensaje(__FILE__,__LINE__,"Datos Incompletos.".$cIntId);
      }
      if ($fl == 1) {
        $cCadena = "";
        if ($cCliCueBa != "") {
          $mMatrizInt = explode("~",$cCliCueBa);
          $vIntId = explode("~",$cIntId);
          for ($i=0;$i<count($mMatrizInt);$i++) {
            if ($mMatrizInt[$i] != "") {
              if (!in_array($mMatrizInt[$i],$vIntId)) {
              // if ($mMatrizInt[$i] != $cIntId) {
                $cCadena .= $mMatrizInt[$i]."~";
              }
            }
          }
          if (strlen($cCadena) > 2) {
            $cCadena = substr($cCadena,0,strlen($cCadena)-1);
          }

          /*** Eliminando las cuentas bancarias de la parametrica ***/
          $cCueBan = "";
          $cCbaErr = "";
          for($nII = 0; $nII < count($vIntId); $nII++){
            if($vIntId[$nII] != ""){

              $aCampos = array(array('NAME'=>'banctaxx','VALUE'=>trim(strtoupper($vIntId[$nII])) ,'CHECK'=>'WH'),
              array('NAME'=>'cliidxxx','VALUE'=>trim(strtoupper($cTerId))        ,'CHECK'=>'WH'));
              if (f_MySql("DELETE","fpar0150",$aCampos,$xConexion01,$cAlfa)) {
                /***** Grabo Bien *****/
                $cCueBan .= $vIntId[$nII].", ";
              }else{
                $nSwitch = 1;
                $cCbaErr .= $vIntId[$nII].", ";
              }

            }
          }

          $cCueBan = substr($cCueBan,0,-2).".";
          $cCbaErr = substr($cCbaErr,0,-2).".";
          
          if($nSwitch == 0){
            f_Mensaje(__FILE__,__LINE__,"Se Eliminaron con Exito las Siguientes Cuentas Bancarias: ".$cCueBan);
          }else{
            f_Mensaje(__FILE__,__LINE__,"Se Presentaron Errores en el Proceso de Eliminacion de las Siguientes Cuentas Bancarias: ".$cCbaErr);
          }
        }

        if ($cCadena == "~"){
          $cCadena = "";
        }
        ?>
        <script languaje = 'javascript'>
          parent.fmwork.document.forms['frgrm']['cCliCueBa'].value = "<?php echo $cCadena ?>";
          parent.fmwork.fnCargarCuentasBancarias();
        </script>
      <?php 
      }
    break;
    case "5": // Agregar Cuenta Bancaria
      if ($cCliCueBa != "") {
        $vCadenaAnt = $cCliCueBa;
      }

      if (strlen($cCadena) > 0) {
        $mMatrizVen = explode("~",$cCadena);
        $zMatrizAnt = explode("~",$vCadenaAnt);
        
        $vCadenaAnt = ($vCadenaAnt != "") ? $vCadenaAnt."~" : "";
        for ($i=0;$i<count($mMatrizVen);$i++) {
          if (in_array($mMatrizVen[$i],$zMatrizAnt) == false) {
            $vCadenaAnt .= $mMatrizVen[$i]."~";
          }
        }
        $vCadenaAnt = substr($vCadenaAnt,0,strlen($vCadenaAnt)-1);
      }
      
      $cMsj = "";
      if (strlen($cCadena) > 0){
        $cMsj = "true|".trim($vCadenaAnt);
      } else {
        $cMsj = "false|No selecciono ninguna Cuenta Bancaria para adicionar.";
      }
      echo $cMsj;
    break;
    default:
      //No hace Nada
    break;
  }
?>
<?php
  include("../../../../libs/php/utility.php");
  
  switch ($tipsave) {
  	case "4": //ELIMINAR INTERMEDIARIO DE UN CLIENTE
      $fl = 1;
      if(strlen($cIntId)== 0){
        $fl = 0;
        f_Mensaje(__FILE__,__LINE__,"VERIFIQUE QUE NO ESTEN VACIO");
      }
      if ($fl == 1) {
        $cCadena = "";
        if ($cFacA != "") {
          $mMatrizInt = explode("~",$cFacA);
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
           parent.fmwork.document.forms['frgrm']['cFacA'].value = "<?php echo $cCadena ?>";
           parent.fmwork.f_CargarFacturara();
        </script>
      <?php }
  	break;
  	case "5": //NUEVO INTERMEDIARIO
      if ($cFacA != "") {
          $vCadenaAnt = $cFacA;
      }

      if (strlen($cCadena) > 0)  {
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
        $cMsj = "false|No selecciono ningun Vendedor para adicionar.";
      }
      echo $cMsj;
      
    break; 
		case "6": //ELIMINAR CONCEPTO CONTABLE --- EXCLUSION DE PAGOS A TERCERO EN FACTURACION
      $fl = 1;
      if(strlen($cIntId)== 0){
        $fl = 0;
        f_Mensaje(__FILE__,__LINE__,"VERIFIQUE QUE NO ESTEN VACIO");
      }
      if ($fl == 1) {
        $cCadena = "";
        if ($cExcPt != "") {
          $mMatrizInt = explode("~",$cExcPt);
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
           parent.fmwork.document.forms['frgrm']['cExcPt'].value = "<?php echo $cCadena ?>";
           parent.fmwork.fnCargarExclusionPagosTerceros();
        </script>
      <?php }
  	break;
		case "7": //NUEVO CONCEPTO CONTABLE --- EXCLUSION DE PAGOS A TERCERO EN FACTURACION
      if ($cExcPt != "") {
        $vCadenaAnt = $cExcPt;
      }

      if (strlen($cCadena) > 0)  {
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
        $cMsj = "false|No selecciono ningun Concepto Contable para adicionar.";
      }
      echo $cMsj;
      
    break;
    case "8": //NUEVO DESCUENTOS
      if ($cDescuen != "") {
        $vCadenaAnt = $cDescuen;
      }

      if (strlen($cCadena) > 0)  {
        $mMatrizDes = explode("|",$cCadena);
        $zMatrizAnt = explode("|",$vCadenaAnt);
        
        $vCadenaAnt = ($vCadenaAnt != "") ? $vCadenaAnt."|" : "";
        for ($i=0;$i<count($mMatrizDes);$i++) {
          if ($mMatrizDes[$i] != "") {
            if (in_array($mMatrizDes[$i],$zMatrizAnt) == false) {
              $vCadenaAnt .= $mMatrizDes[$i]."|";
            }
          }
        }
        $vCadenaAnt = rtrim($vCadenaAnt, "|");
      }
      
      $cMsj = "";
      if (strlen($cCadena) > 0){
        $cMsj = "true||".trim($vCadenaAnt);
      } else {
        $cMsj = "false||No selecciono ningun Descuento para adicionar.";
      }
      echo $cMsj;

    break;
    case "9": //ELIMINAR DESCUENTOS
      $fl = 1;
      if(strlen($cIntId)== 0){
        $fl = 0;
        f_Mensaje(__FILE__,__LINE__,"VERIFIQUE QUE NO ESTEN VACIO");
      }
      if ($fl == 1) {
        $cCadena = "";
        if ($cDescuen != "") {
          $mMatrizInt = explode("|",$cDescuen);
          for ($i=0;$i<count($mMatrizInt);$i++) {
            if ($mMatrizInt[$i] != "") {
              if ($mMatrizInt[$i] != $cIntId) {
                $cCadena .= $mMatrizInt[$i]."|";
              }
            }
          }
          if (strlen($cCadena) > 2) {
            $cCadena = rtrim($cCadena, "|");
          }
        }

        if ($cCadena == "|")  {
          $cCadena = "";
        } ?>
        <script languaje = 'javascript'>
          parent.fmwork.document.forms['frgrm']['cDescuen'].value = "<?php echo $cCadena ?>";
          parent.fmwork.fnCargarDescuentos();
        </script>
      <?php }
  	break;
  	default:
  		/** No hace nada **/
  	break;
  }
?>
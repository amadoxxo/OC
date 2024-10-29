<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");
  
	  switch ($tipsave) {
	  	case "4": //ELIMINAR REQUISITO LEGAL
	    $fl = 1;
      //f_Mensaje(__FILE__,__LINE__,"$cIntId");
      if(strlen($cIntId)== 0){
        $fl = 0;
        f_Mensaje(__FILE__,__LINE__,"Datos Incompletos.");
      }
      if ($fl == 1) {
        $cCadena = "";
        
        if ($cCliDrl != "") {          
          $mMatrizInt1 = explode("~",$cCliDrl);
          for ($i=0;$i<count($mMatrizInt1);$i++) {
	          if($mMatrizInt1[$i] != ""){
	            $mMatrizInt[$i] = $mMatrizInt1[$i];
	          }
        	}
          for ($i=0;$i<count($mMatrizInt1);$i++) {
            if ($mMatrizInt1[$i] != "") {
            	$mMatrizAux = array();
            	$mMatrizAux = explode(",",$mMatrizInt1[$i]);            	
              if ($mMatrizAux[0] != $cIntId)  {
                $cCadenaf .= $mMatrizInt1[$i]."~";
              }
            }
          }

          $cCadena = substr($cCadenaf,0,1);
          if($cCadena == ","){
           $cCadenaf = substr($cCadenaf,1,strlen($cCadenaf));
          }

          $cCadenaE = substr($cCadenaf,strlen($cCadenaf)-1,strlen($cCadenaf));
          if($cCadenaE == "~"){
           $cCadenaf = substr($cCadenaf,0,strlen($cCadenaf)-1);
          }
          //f_Mensaje(__FILE__,__LINE__,$cCadenaf);
          #Fin Buscando los requisitos legales actuales#
        }
        /**
         * Guardando nuevos requisitos legales, despues de eliminar el seleccionado
         */
        ?>
        <script languaje = 'javascript'>
          parent.fmwork.document.forms['frgrm']['cCliDrl'].value = "<?php echo $cCadenaf ?>";
          parent.fmwork.f_CargarRequisitos();
        </script>
        <?php }
	  	break;
	  	case "5": //GUARDAR DOCUMENTOS REQUISITOS LEGALES
	  	  //Este Proceso se realiza con AJAX
	  	  
		    #Armo cadena para grabar
	      $mCade1 = explode("~",$cCadena1);
	      $mCade2 = explode("~",$cCadena2);
	      for($i=0; $i<count($mCade1); $i++){
	         if ($mCade1[$i] <> "" && $mCade2[$i] <> "") {
	           $cCadenaf .= $mCade1[$i].",".$mCade2[$i]."~";
	         }
	      }
	  
	     #Buscando los requisitos legales actuales#
	     $vCadenaAnt = $cCliDrl;

	     $mMatrizAnt = explode("~",$vCadenaAnt);
	     $mMatrizInt = explode("~",$cCadenaf);
	  
	    /**
	     * Buscando en la matriz anterior si hay fechas para actualizar de la nueva matriz
	     */
	    $cCadena = "";
	    for ($i=0; $i<count($mMatrizAnt); $i++) {
	      $mAuxAnt = array();
	      $mAuxAnt = explode(",",$mMatrizAnt[$i]);
	          
	      if ($mAuxAnt[0] != "") {
	        $cDocLeg = "";
	        for ($j=0; $j<count($mMatrizInt); $j++) {
	          $mAuxInt = array();
	          $mAuxInt = explode(",",$mMatrizInt[$j]);
	          if ($mAuxInt[0] != "") {
	            if ($mAuxAnt[0] == $mAuxInt[0]) {
	              //Actualizo el dato
	              $cDocLeg = $mMatrizInt[$j];
	              $j = count($mMatrizInt);
	            }
	          }
	        } 
	        if ($cDocLeg == "") {
	          $cDocLeg = $mMatrizAnt[$i];
	        }
	        $cCadena .= $cDocLeg."~";
	      }
	    }
	    
	    /**
	     * Agregando a la cadena las fechas que no estan en la matriz anterior
	     */
	    for ($j=0; $j<count($mMatrizInt); $j++) {
	      $mAuxInt = array();
	      $mAuxInt = explode(",",$mMatrizInt[$j]);
	      
	      if ($mAuxInt[0] != "") {
	        $nEncontro = 0;
	          for ($i=0; $i<count($mMatrizAnt); $i++) {
	            $mAuxAnt = array();
	            $mAuxAnt = explode(",",$mMatrizAnt[$i]);
	            if ($mAuxAnt[0] != "") {
	              if ($mAuxInt[0] == $mAuxAnt[0]) {
	                //Ya se actualizo el dato, no se incluye
	                $nEncontro = 1;
	                $i = count($mMatrizAnt);
	              }
	            }
	          }
	          if ($nEncontro == 0) {
	            $cCadena .= trim($mMatrizInt[$j])."~";
	          }
	        } 
	      }
	      $cCadena = substr($cCadena,0,strlen($cCadena)-1);
	      
	      $cMsj = "";
	      if (strlen($cCadena) > 0){
	       $cMsj = "true|".trim($cCadena);
	      } else {
	        $cMsj = "false|No selecciono ningun Documento Requisito Legal para adicionar.";
	      }
	      echo $cMsj;
      break;	  	
	  	default:
	  		//No Hace Nada
	  	break;
	  }
?>
	
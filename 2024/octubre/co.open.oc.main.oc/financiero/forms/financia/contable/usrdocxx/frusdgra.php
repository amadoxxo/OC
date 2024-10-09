<?php
  namespace openComex;
/**
 * Graba Concepto Contable.
 * Este programa permite Guardar en la tabla Catalogo Inventario.
 * @author
 * @package emisioncero
 */
	include("../../../../libs/php/utility.php");

	$nSwitch = "0"; // Switch para Vericar la Validacion de Datos
	$cCadErr = "";

	switch ($_COOKIE['kModo']) {
  	case "EDITAR":
  		
  		if($_POST['cUsrId']==""){
  			$zSwitch = 1;
			  $cCadErr .= "Debe Escoger un Usuario, \n";
  		}
  		
  		/**
  		 * Preparando Arreglo de Documentos Contables
  		 */
  		$cUsrDoc = "|";
  		for($i=1;$i<=$_POST['gIteration'];$i++){
  			if($_POST['oCheck'.$i] <> ""){
  				$cUsrDoc .= $_POST['oCheck'.$i]."|";
  			}
  		}
  		
  		if($cUsrDoc == "|"){
  			$cUsrDoc = "";
      }
      
      /**
  		 * Preparando Arreglo de Documentos Contables autorizados para traslado de do a do
  		 */
      $cUsrDocTR = "|";
      if ($cAlfa == "GRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "DEGRUMALCO") {
        for($i=1;$i<=$_POST['nSecuencia'];$i++){
          if($_POST['oCheckTr'.$i] <> ""){
            $cUsrDocTR .= $_POST['oCheckTr'.$i]."|";
          }
        }
      }
  		
  		if($cUsrDocTR == "|"){
  			$cUsrDocTR = "";
      }
  	break;
	}

	switch ($_COOKIE['kModo']) {
  	case "EDITAR":
	    if ($zSwitch == 0 ) {
	      //f_Mensaje(__FILE__,__LINE__,"entre");
        $zInsertCsc = array(array('NAME'=>'USRDOCXX','VALUE'=>$cUsrDoc	                          ,'CHECK'=>'NO'),
                            array('NAME'=>'USRDOCTR','VALUE'=>$cUsrDocTR                          ,'CHECK'=>'NO'),
		                        array('NAME'=>'USRIDXXX','VALUE'=>$_POST['cUsrId']                    ,'CHECK'=>'WH'));
    		if (f_MySql("UPDATE","SIAI0003",$zInsertCsc,$xConexion01,$cAlfa)){
    		}else{
    		  $cCadErr .= "Error al Actualizar los Datos [SIAI0003], Verifique ";
			    $zSwitch = 1;
    		}
	    }
  	break;
	}

	if($zSwitch==0){
	  //f_Mensaje(__FILE__,__LINE__,"entre");
	  switch ($_COOKIE['kModo']) {
		 case "EDITAR":
		   //f_Mensaje(__FILE__,__LINE__,"entre");
			 f_Mensaje(__FILE__,__LINE__,"El Concepto ha Sido Modificado Con Exito, Verifique");?>
			 <html><body>
			 <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt']; ?>" method = "post" target = "fmwork"></form>
			 <script languaje = "javascript">
				 parent.fmnav.location='<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php';
			 	 document.frgrm.submit();
			 </script>
			 </body>
			 </html>
		 <?php break;
	  }
  } else {
  	f_Mensaje(__FILE__,__LINE__,"$cCadErr Verifique");
  }
?>
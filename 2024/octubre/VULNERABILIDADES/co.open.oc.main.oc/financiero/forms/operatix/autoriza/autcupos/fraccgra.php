<?php
  namespace openComex;
	include("../../../../libs/php/utility.php");

  $nSwitch = 0;
  $cCadErr = "";

  switch ($_COOKIE['kModo']) {
	  case "NUEVO":
	    /***** Validando Sucursal no puede ser Vacio *****/
  		if($_POST['cSucId']==""){
  		  $nSwitch = 1;
  		  $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			$cCadErr .= "La Sucursal del Do no puede ser Vacio,\n";
  		}
	    /***** Validando Do no puede ser Vacio *****/
  		if($_POST['cDocId']==""){
  		  $nSwitch = 1;
  		  $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			$cCadErr .= "El Do no puede ser Vacio,\n";
  		}
  		/***** Validando Subfijo no puede ser Vacio *****/
  		if($_POST['cDocSuf']==""){
  		  $nSwitch = 1;
  		  $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			$cCadErr .= "El Sufijo no puede ser Vacio,\n";
  		}
  		switch ($_POST['cCliCupTi']){
  	    case "SINCUPO":
  	    case "LIMITADO/ILIMITADO":
  	      if (is_numeric($_POST['cCupAut']) == false || $_POST['cCupAut'] <=0) {
      		  $nSwitch = 1;
      		  $cCadErr .= " El Cupo Autorizado debe ser Mayor a Cero,\n";
      		}
  	    break;
  	    case "LIMITADO":
  	    case "ILIMITADO/LIMITADO":
  	     if (is_numeric($_POST['cCupAut']) == false || $_POST['cCupAut'] <= $_POST['cCliCupOp']) {
      		  $nSwitch = 1;
      		  $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      		  $cCadErr .= " El Cupo Autorizado debe ser Mayor al Cupo por Operacion,\n";
      		}
  	    break;
  	    case "ILIMITADO":
  	     $nSwitch = 1;
  	     $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      	 $cCadErr .= " No es Necesario Autorizar la Operacion, El Cliente Tiene Cupo Ilimitado,\n";
  	    break;
  		}
  	  
  	  if($_POST['cAutFac']==""){
  		  $nSwitch = 1;
  		  $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			$cCadErr .= "La Opcion de Autoriza Facturar no puede ser Vacia,\n";
  		}
  	break;
	}	/***** Fin de la Validacion *****/


	/***** Ahora Empiezo a Grabar *****/
	/***** Pregunto si el SWITCH Viene en 0 para Poder Seguir *****/

	if ($nSwitch == 0) {
		switch ($_COOKIE['kModo']) {
			/*****************************   UPDATE    ***********************************************/
			case "NUEVO":
				if ($nSwitch == 0) {
					$zIns1001 = array(array('NAME'=>'doccupxx','VALUE'=>trim($_POST['cCupAut'])               ,'CHECK'=>'SI'),
    					 							array('NAME'=>'doccupaf','VALUE'=>$_POST['cAutFac']										  ,'CHECK'=>'SI'),
                            array('NAME'=>'doccupfe','VALUE'=>date('Y-m-d')    										  ,'CHECK'=>'SI'),
    					 							array('NAME'=>'doccupho','VALUE'=>date('H:i:s')    		                  ,'CHECK'=>'SI'),
    					 							array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')    										  ,'CHECK'=>'SI'),
    					 							array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')    		                  ,'CHECK'=>'SI'),
    										    array('NAME'=>'docidxxx','VALUE'=>trim(strtoupper($_POST['cDocId']))    ,'CHECK'=>'WH'),
    										    array('NAME'=>'docsufxx','VALUE'=>trim(strtoupper($_POST['cDocSuf']))   ,'CHECK'=>'WH'),
    										    array('NAME'=>'sucidxxx','VALUE'=>trim(strtoupper($_POST['cSucId']))    ,'CHECK'=>'WH'));

    			if (f_MySql("UPDATE","sys00121",$zIns1001,$xConexion01,$cAlfa)) {
    			} else {
    				$nSwitch = 1;
    				f_Mensaje(__FILE__,__LINE__,"Error al Actualizar el Registro la Tabla sys00121.");
    			}
				}
      break;
      
      /****************************************   EDITAR  *******************************************/
      case "EDITAR":
				if ($nSwitch == 0) {
					$zIns1001 = array(array('NAME'=>'doccupxx','VALUE'=>trim($_POST['cCupAut'])               ,'CHECK'=>'SI'),
    					 							array('NAME'=>'doccupaf','VALUE'=>$_POST['cAutFac']										  ,'CHECK'=>'SI'),
                            array('NAME'=>'doccupfe','VALUE'=>date('Y-m-d')    										  ,'CHECK'=>'SI'),
    					 							array('NAME'=>'doccupho','VALUE'=>date('H:i:s')    		                  ,'CHECK'=>'SI'),
    					 							array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')    										  ,'CHECK'=>'SI'),
    					 							array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')    		                  ,'CHECK'=>'SI'),
    										    array('NAME'=>'docidxxx','VALUE'=>trim(strtoupper($_POST['cDocId']))    ,'CHECK'=>'WH'),
    										    array('NAME'=>'docsufxx','VALUE'=>trim(strtoupper($_POST['cDocSuf']))   ,'CHECK'=>'WH'),
    										    array('NAME'=>'sucidxxx','VALUE'=>trim(strtoupper($_POST['cSucId']))    ,'CHECK'=>'WH'));

    			if (f_MySql("UPDATE","sys00121",$zIns1001,$xConexion01,$cAlfa)) {
    			} else {
    				$nSwitch = 1;
    				f_Mensaje(__FILE__,__LINE__,"Error al Actualizar el Registro la Tabla sys00121.");
    			}
				}
      break;
     }
  } else {
  	f_Mensaje(__FILE__,__LINE__,"$cCadErr Verifique");
  }



  if ($nSwitch == 0) {
 	  if($_COOKIE['kModo']=="NUEVO"){
 		  f_Mensaje(__FILE__,__LINE__,"Se Autorizo Cupo Con Exito");
 	  }
 	  if($_COOKIE['kModo']=="EDITAR"){
 	    f_Mensaje(__FILE__,__LINE__,"Se Edito Autoriacion Para Cupo Con Exito");
 	  }
 		?>
		<form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
			<script languaje = "javascript">
  			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
				document.forms['frgrm'].submit()
			</script>
  	<?php
 	}

?>
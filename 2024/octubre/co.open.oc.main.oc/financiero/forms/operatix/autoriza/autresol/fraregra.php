<?php
  namespace openComex;

	include("../../../../libs/php/utility.php");

	$nSwitch = 0; // Switch para Vericar la Validacion de Datos
	$cCadErr = "";
	
	
	switch ($_COOKIE['kModo']) {
	
	  case "NUEVO":
	  	/***** Validando Codigo *****/
  		if ($_POST['cSucId'] == "") {
  		  $nSwitch = 1;
  		  $cCadErr .= " La Sucursal del Do no puede ser Vacio, \n";
  		}

  		/***** Validando Codigo *****/
  		if ($_POST['cDocId'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= " El Do no puede ser Vacio,\n";
  		}

  		/***** Validando Codigo *****/
  		if ($_POST['cDocSuf'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= " El Sufijo no puede ser Vacio, \n";
  		}
			/***** Validando Codigo *****/
  		if ($_POST['cDocFsr'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= "El Tipo de Resolucion no puede ser Vacio, \n";
  		  
  		}
		
	}
	
	

	/***** Ahora Empiezo a Grabar *****/
  		
		if ($nSwitch == 0) {
			switch ($_COOKIE['kModo']) {	
			case "NUEVO":
					if ($nSwitch == 0) {
			  	if($_POST['cDocFsr']=="PRINCIPAL"){
			  		
			  	  $cRes = "SECUNDARIA";
			  	}else{
			  	  $cRes = "PRINCIPAL";
			  	}
					$zIns1001 = array(array('NAME'=>'docfrsxx','VALUE'=>$cRes                                 ,'CHECK'=>'SI'),
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
      case "EDITAR":
				if ($nSwitch == 0) {
			  	if($_POST['cDocFsr']=="SECUNDARIA"){
			  	  $cRes = "PRINCIPAL";			  	
			  	}else{
			  	  $cRes = "SECUNDARIA";
			  	}
					$zIns1001 = array(array('NAME'=>'docfrsxx','VALUE'=>$cRes                                 ,'CHECK'=>'SI'),
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
  }else {
  	f_Mensaje(__FILE__,__LINE__,"$cCadErr Verifique");
  }
  
    if ($nSwitch == 0) {
 	  if($_COOKIE['kModo']=="NUEVO"){
 		  f_Mensaje(__FILE__,__LINE__,"Se Autorizo Cambio de Resolucion con Exito");
 	  }
 	  if($_COOKIE['kModo']=="EDITAR"){
 	    f_Mensaje(__FILE__,__LINE__,"Se Cambio Tipo de Resolucion con Exito");
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
  		

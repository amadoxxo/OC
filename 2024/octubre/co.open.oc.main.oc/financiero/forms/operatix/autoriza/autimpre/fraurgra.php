<?php
  namespace openComex;

	include("../../../../libs/php/utility.php");

	$nSwitch = "0"; // Switch para Vericar la Validacion de Datos
	$cCadErr = "";
	
	switch ($_COOKIE['kModo']) {
		
	  case "NUEVO":
	  	/***** Validando Codigo *****/
  		if ($_POST['cComId'] == "") {
  		  $nSwitch = 1;
  		  $cCadErr .= " El Id del Comprobante no puede ser vacio, \n";
  		}

  		/***** Validando Codigo *****/
  		if ($_POST['cComCod'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= " El Codigo del Comprobante no puede ser Vacio,\n";
  		}

  		/***** Validando Codigo *****/
  		if ($_POST['cComCsc'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= " El Consecutivo del Comprobante no puede ser vacio, \n";
  		}
	/***** Validando Codigo *****/
  		if ($_POST['cCcoDes'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= " La descripcion del Centro de Costo no puede ser vacio, \n";
  		}
	/***** Validando Codigo *****/
  		if ($_POST['cCliId'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= " El NIT de Cliente no puede ser vacio, \n";
  		}
  			/***** Validando Codigo *****/
  		if ($_POST['cCliDV'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= " El Digito de Verificacion no puede ser vacio, \n";
  		}
		/***** Validando Codigo *****/
  		if ($_POST['cCliNom'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= " El Nombre del Cliente no puede ser vacio, \n";
  		}
	}

	/***** Ahora Empiezo a Grabar *****/
  		
		if ($nSwitch == 0) {
		switch ($_COOKIE['kModo']) {
			
			/*****************************   UPDATE    ***********************************************/
			case "NUEVO":
				if ($nSwitch == 0) {
				
					$cPerAno = $_POST['cPerAno'];//Capturo Valor del Año Para Update en Tabla de Cabecera fcoc$cPerAno
					$cComPrn = "";
					$zIns1001 = array(array('NAME'=>'comprnxx','VALUE'=>$cComPrn              			,'CHECK'=>'NO'),
    					 							array('NAME'=>'comidxxx','VALUE'=>trim($_POST['cComId'])			,'CHECK'=>'WH'),
    					 							array('NAME'=>'comcodxx','VALUE'=>trim($_POST['cComCod'])     ,'CHECK'=>'WH'),
    					 							array('NAME'=>'comcscxx','VALUE'=>trim($_POST['cComCsc'])     ,'CHECK'=>'WH'));
    			if (f_MySql("UPDATE","fcoc$cPerAno",$zIns1001,$xConexion01,$cAlfa)) {
    			} else {
    				$nSwitch = 1;
    				f_Mensaje(__FILE__,__LINE__,"Error al Actualizar el Registro la Tabla fcoc$cPerAno.");
    			}
				}
      break;
      case "ANULAR":
				if ($nSwitch == 0) {
					$cPerAno = substr($_POST['cComFec'],0,4);
					$cComPrn = "IMPRESO";
					$zIns1001 = array(array('NAME'=>'comprnxx','VALUE'=>$cComPrn              			,'CHECK'=>'NO'),
    					 							array('NAME'=>'comidxxx','VALUE'=>trim($_POST['cComId'])			,'CHECK'=>'WH'),
    					 							array('NAME'=>'comcodxx','VALUE'=>trim($_POST['cComCod'])     ,'CHECK'=>'WH'),
    					 							array('NAME'=>'comcscxx','VALUE'=>trim($_POST['cComCsc'])     ,'CHECK'=>'WH'));
    			if (f_MySql("UPDATE","fcoc$cPerAno",$zIns1001,$xConexion01,$cAlfa)) {
    			} else {
    				$nSwitch = 1;
    				f_Mensaje(__FILE__,__LINE__,"Error al Actualizar el Registro la Tabla fcoc$cPerAno.");
    			}
				}
      break;
     }
  } else {
  	f_Mensaje(__FILE__,__LINE__,"$cCadErr Verifique");
  }
  
    if ($nSwitch == 0) {
 	  if($_COOKIE['kModo']=="NUEVO"){
 		  f_Mensaje(__FILE__,__LINE__,"Se Autorizo Reeimpresion con Exito");
 	  }
 	  if($_COOKIE['kModo']=="ANULAR"){
 	    f_Mensaje(__FILE__,__LINE__,"Se Anulo Autorizacion Para Reeimpresion con Exito");
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
  		
  		
  		
  		
  		
  		
  		

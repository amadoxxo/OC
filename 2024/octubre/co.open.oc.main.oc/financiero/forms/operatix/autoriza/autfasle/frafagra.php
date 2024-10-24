<?php
  namespace openComex;

	include("../../../../libs/php/utility.php");

$nSwitch = "0"; // Switch para Vericar la Validacion de Datos
$cCadErr = "";
$cMsj = "";
switch ($_COOKIE['kModo']) {
	case "NUEVO":
			
		$mDos = array();
		$nDoAnt ='';
		foreach ($_POST as $key => $dato) {
			$i = preg_match_all('!\d+!', $key, $matches);
			$i = $matches[0][0];
			
			if ( $i != "" ) {
				if ( $i != $nDoAnt) {
					$arrayName = array(
						'cSucId'=> $_POST['cSucId'.($i)],
						'cDocId'=> $_POST['cDocId'.($i)],
						'cDocSuf'=> $_POST['cDocSuf'.($i)],
						'cDocTip'=> $_POST['cDocTip'.($i)],
						'cCliId'=> $_POST['cCliId'.($i)],
						'cCliDv'=> $_POST['cCliDv'.($i)],
						'cCliNom'=> $_POST['cCliNom'.($i)]
					);
					
					$mDos[] = $arrayName;
				}
				$nDoAnt = $i;
			}
		}
			
		foreach ($mDos as $Do) {
	  	/***** Validando Codigo *****/
  		if ($Do['cSucId'] == "") {
  		  $nSwitch = 1;
  		  $cCadErr .= " La sucursal no puede ser vacio, \n";
  		}

  		/***** Validando Codigo *****/
  		if ($Do['cDocId'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= " El DO no puede ser Vacio,\n";
  		}

  		/***** Validando Codigo *****/
  		if ($Do['cDocTip'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= " El Tipo de Operacion no puede ser vacio, \n";
  		}
	/***** Validando Codigo *****/
  		if ($Do['cCliId'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= " El NIT de Cliente no puede ser vacio, \n";
  		}
	/***** Validando Codigo *****/
  		if ($Do['cCliNom'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= " El Nombre del Cliente no puede ser vacio Do, \n";
  		}
		}
		break;
	case "ANULAR":
		$mAuxExc = array();
		$mDos = array();
		$mAuxExc = explode("|", $_POST['cComMemo']);
		$cComprobantes = "";
		
		for ($nPe=0; $nPe<count($mAuxExc); $nPe++) {
			if ($mAuxExc[$nPe] != "") {
				$vAuxExc = array();
				$vAuxExc = explode("~", $mAuxExc[$nPe]);						
				
				$mDos[$vAuxExc[0].'-'.$vAuxExc[1].'-'.$vAuxExc[2]]['cSucId'] = $vAuxExc[0];
				$mDos[$vAuxExc[0].'-'.$vAuxExc[1].'-'.$vAuxExc[2]]['cDocId'] = $vAuxExc[1];
				$mDos[$vAuxExc[0].'-'.$vAuxExc[1].'-'.$vAuxExc[2]]['cDocSuf'] = $vAuxExc[2];
			}
		}
	break;
	}
	/***** Ahora Empiezo a Grabar *****/
  		
		if ($nSwitch == 0) {
		switch ($_COOKIE['kModo']) {			
			//f_Mensaje(__FILE__,__LINE__,$_COOKIE['kModo'])
			/*****************************   UPDATE    ***********************************************/
			case "NUEVO":
				if ($nSwitch == 0) {
					foreach ($mDos as $Do) {
			  		$zIns1001 = array(array('NAME'=>'docafasl','VALUE'=>"SI"                         ,'CHECK'=>'SI'),
					 							array('NAME'=>'docidxxx','VALUE'=>trim(strtoupper($Do['cDocId']))    ,'CHECK'=>'WH'),
										    array('NAME'=>'docsufxx','VALUE'=>trim(strtoupper($Do['cDocSuf']))   ,'CHECK'=>'WH'),
										    array('NAME'=>'sucidxxx','VALUE'=>trim(strtoupper($Do['cSucId']))    ,'CHECK'=>'WH'));
	
	    			if (f_MySql("UPDATE","sys00121",$zIns1001,$xConexion01,$cAlfa)) {
	    				$cMsj .= "Se Autorizo Facturacion Anticipos sin Legalizar con Exito para el Do [{$Do['cSucId']}-{$Do['cDocId']}-{$Do['cDocSuf']}],\n";
	    			} else {
	    				$cMsj .= "Error al Actualizar el Registro la Tabla sys00121 para el Do [{$Do['cSucId']}-{$Do['cDocId']}-{$Do['cDocSuf']}],\n";
	    				//f_Mensaje(__FILE__,__LINE__,"Error al Actualizar el Registro la Tabla sys00121.");
	    			}
					}
				}
      break;
      /*****************************   UPDATE    ***********************************************/
			case "ANULAR":
				//f_Mensaje(__FILE__,__LINE__,$_COOKIE['kModo']);
			if ($nSwitch == 0) {
				foreach ($mDos as $Do) {
					$zIns1001 = array(array('NAME'=>'docafasl','VALUE'=>"NO"                         ,'CHECK'=>'SI'),
    		 							array('NAME'=>'docidxxx','VALUE'=>trim(strtoupper($Do['cDocId']))    ,'CHECK'=>'WH'),
    							    array('NAME'=>'docsufxx','VALUE'=>trim(strtoupper($Do['cDocSuf']))   ,'CHECK'=>'WH'),
    							    array('NAME'=>'sucidxxx','VALUE'=>trim(strtoupper($Do['cSucId']))    ,'CHECK'=>'WH'));

    			if (f_MySql("UPDATE","sys00121",$zIns1001,$xConexion01,$cAlfa)) {
    				$cMsj .= "Se Anulo Autorizacion de Facturacion Anticipos sin Legalizar Con Exito para el Do [{$Do['cSucId']}-{$Do['cDocId']}-{$Do['cDocSuf']}],\n";
    			} else {
    				$cMsj .= "Error al Actualizar el Registro la Tabla sys00121 para el Do [{$Do['cSucId']}-{$Do['cDocId']}-{$Do['cDocSuf']}],\n";
    				//f_Mensaje(__FILE__,__LINE__,"Error al Actualizar el Registro la Tabla sys00121.");
    			}
				}
			}
      break;
     }
  } else {
  	f_Mensaje(__FILE__,__LINE__,"$cCadErr Verifique");
  }
  
    if ($nSwitch == 0) {
    
 	  if($_COOKIE['kModo']=="NUEVO"){
 		  f_Mensaje(__FILE__,__LINE__,$cMsj);
 	  }
 	  if($_COOKIE['kModo']=="ANULAR"){
 	    f_Mensaje(__FILE__,__LINE__,$cMsj);
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
  		
  		
  		
  		
  		
  		
  		

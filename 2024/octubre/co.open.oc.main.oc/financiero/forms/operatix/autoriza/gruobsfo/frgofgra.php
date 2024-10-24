<?php
  namespace openComex;
/**
 * Graba Tipo de Documento.
 * Este programa permite Guardar en la tabla Tipo de Documento.
 * @author
 * @package emisioncero
 */
	include("../../../../libs/php/utility.php");

	$nSwitch = 0; // Switch para Vericar la Validacion de Datos
	$cMsj = "";

	switch ($_COOKIE['kModo']) {
	  case "NUEVO":
	  case "EDITAR":
	  	
	  	/***** Validando Codigo *****/
  		if ($_POST['cGofId'] == "" && $_COOKIE['kModo'] == "EDITAR") {
  		  $nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "Id de la Observacion no puede ser vacio.\n";
  		}
  		
			if ($_POST['cGofTip'] == "" && $_COOKIE['kModo'] == "EDITAR") {
  		  $nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "El Tipo de Observacion no puede ser vacio.\n";
  		}

	  	/***** Validando Descripcion*****/
  		if ($_POST['cGofDes'] == "") {
  		  $nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "La Descripcion de la Observacion no puede ser vacio.\n";
  		}
  	break;
	  case "ANULAR":
	  if (!f_InList($_POST['cGofEst'],"ACTIVO","INACTIVO")) {
	  		$nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "El Estado del Registro Debe ser ACTIVO o INACTIVO.\n";
	  	}
	  break;
	}	/***** Fin de la Validacion *****/

	/***** Ahora Empiezo a Grabar *****/
	/***** Pregunto si el SWITCH Viene en 0 para Poder Seguir *****/
	if ($nSwitch == 0) {
		switch ($_COOKIE['kModo']) {
			case "NUEVO":
				#Buscar consecutivo
				$qNumSec  = "SELECT gofidxxx FROM $cAlfa.fpar0123 ORDER BY ABS(gofidxxx) DESC LIMIT 0,1 ";
  	    $xNumSec  = f_MySql("SELECT","",$qNumSec,$xConexion01,"");
  	    //f_Mensaje(__FILE__,__LINE__,$qNumSec."~".mysql_num_rows($xNumSec));
  	    if(mysql_num_rows($xNumSec) > 0) {
  	    	$xRNS = mysql_fetch_array($xNumSec);
  	    	$cCsc = $xRNS['gofidxxx'] + 1;
  	    } else {
  	    	$cCsc = 100;
  	    }
        
  	    /**
  	     * Insert en la Tabla.
  	     */
        $cInsertTab	 = array(array('NAME'=>'goftipxx','VALUE'=>'FORMULARIOS'										    ,'CHECK'=>'SI'),
        										 array('NAME'=>'gofidxxx','VALUE'=>$cCsc														    ,'CHECK'=>'SI'),
				                     array('NAME'=>'gofdesxx','VALUE'=>trim(strtoupper($_POST['cGofDes']))  ,'CHECK'=>'SI'),
										    		 array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId'])) ,'CHECK'=>'SI'),
					 									 array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
					 									 array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
					 									 array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
					 									 array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
														 array('NAME'=>'regestxx','VALUE'=>'ACTIVO' 														,'CHECK'=>'SI'));

				if (f_MySql("INSERT","fpar0123",$cInsertTab,$xConexion01,$cAlfa)) {
				} else {
					$nSwitch = 1;
					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMsj .= "Error al Guardar el Registro [fpar0123].\n";
				}
			break;
			/*****************************   UPDATE    ***********************************************/
			case "EDITAR":
				$cInsertTab	 = array(array('NAME'=>'gofdesxx','VALUE'=>trim(strtoupper($_POST['cGofDes']))  ,'CHECK'=>'SI'),
									    		   array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
				 									   array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
				 									   array('NAME'=>'goftipxx','VALUE'=>trim(strtoupper($_POST['cGofTip']))  ,'CHECK'=>'WH'),
                             array('NAME'=>'gofidxxx','VALUE'=>trim(strtoupper($_POST['cGofId']))   ,'CHECK'=>'WH'));

					if (f_MySql("UPDATE","fpar0123",$cInsertTab,$xConexion01,$cAlfa)) {
						/***** Grabo Bien *****/
					} else {
						$nSwitch = 1;
						$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						$cMsj .= "Error al Actualizar el Registro [fpar0123].\n";
					}
      break;
      /*****************************   UPDATE    ***********************************************/
      case "ANULAR":
         if($_POST['cGofEst']=="ACTIVO"){
           $cEstado="INACTIVO";
         }
         if($_POST['cGofEst']=="INACTIVO"){
           $cEstado="ACTIVO";
         }

					$zInsertCab	 = array(array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
					 									   array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')											  ,'CHECK'=>'SI'),
														   array('NAME'=>'regestxx','VALUE'=>$cEstado                             ,'CHECK'=>'SI'),
														   array('NAME'=>'goftipxx','VALUE'=>trim(strtoupper($_POST['cGofTip']))  ,'CHECK'=>'WH'),
                               array('NAME'=>'gofidxxx','VALUE'=>trim(strtoupper($_POST['cGofId']))   ,'CHECK'=>'WH'));

				 if (f_MySql("UPDATE","fpar0123",$zInsertCab,$xConexion01,$cAlfa)) {
					 /***** Grabo Bien *****/
				 } else {
					 $nSwitch = 1;
					 $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					 $cMsj .= "Error al Actualizar el Registro [fpar0123].\n";
				 }
      break;
    }
  } 

  if($nSwitch == 1){
  	f_Mensaje(__FILE__,__LINE__,"$cMsj Verifique");
  } else {
 	  if($_COOKIE['kModo']!="ANULAR"){
 		  f_Mensaje(__FILE__,__LINE__,"El Registro se cargo con Exito");
 	  }
 	  if($_COOKIE['kModo']=="ANULAR"){
 	    f_Mensaje(__FILE__,__LINE__,"El Registro Cambio de Estado Con Exito");
 	  }
 		?>
		<form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
			<script languaje = "javascript">
  			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
				document.forms['frgrm'].submit()
			</script>
  	<?php }
?>
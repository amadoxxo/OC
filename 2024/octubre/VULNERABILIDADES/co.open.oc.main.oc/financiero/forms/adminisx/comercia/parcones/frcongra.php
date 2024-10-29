<?php
  namespace openComex;
/**
 * Graba Banco.
 * Este programa permite Guardar en la tabla Bancos.
 * @author
 * @package emisioncero
 */
	include("../../../../libs/php/utility.php");

	f_Mensaje(__FILE__,__LINE__,"Para [INSERTAR] [ACTUALIZAR] [ELIMINAR] Una Condicion Especial, por favor Comunicarse con openTecnologia Ltda.");

/*
	$nSwitch = "0"; // Switch para Vericar la Validacion de Datos
	$cCadErr = "";

	switch ($_COOKIE['kModo']) {
	  case "NUEVO":
	  case "EDITAR":
	    /***** Validando Descripcion Linea Inventario*****/
/*
  		if ($_POST['cConDes'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= "Descripcion Forma de Cobro no puede ser vacio \n ";
  		}

  		/* Validado El Estado del Registro */
/*
			if ($_POST['cEstado'] == "") {
        $nSwitch = "1";
				$cCadErr .= "El Estado del Registro no puede ser vacio \n ";
			}

  	break;
	}	/***** Fin de la Validacion *****/

	/***** Ahora Empiezo a Grabar *****/
	/***** Pregunto si el SWITCH Viene en 0 para Poder Seguir *****/
/*
	if ($nSwitch == "0") {
		switch ($_COOKIE['kModo']) {
			case "NUEVO":
  	    /**
  	    * Insert en la Tabla.
  	    */
/*
  	    $qNumSec  = "SELECT convert (max(`conidxxx`), DECIMAL) conidxxx ";
        $qNumSec .= "FROM $cAlfa.fpar0134 LIMIT 0,1 ";
  	    $xNumSec  = f_MySql("SELECT","",$qNumSec,$xConexion01,"");

        $vNumSec  = mysql_fetch_array($xNumSec);
        $cNumSec  = $vNumSec['conidxxx']+1;

        $cInsertTab	 = array(array('NAME'=>'conidxxx','VALUE'=>str_pad($cNumSec,3,0,STR_PAD_LEFT)   ,'CHECK'=>'SI'),
				                     array('NAME'=>'condesxx','VALUE'=>trim(strtoupper($_POST['cConDes']))  ,'CHECK'=>'SI'),
				                     array('NAME'=>'contipxx','VALUE'=>trim(strtoupper($_POST['cConTip']))  ,'CHECK'=>'SI'),
										    		 array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId'])) ,'CHECK'=>'SI'),
					 									 array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')    										,'CHECK'=>'SI'),
					 									 array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')                        ,'CHECK'=>'SI'),
					 									 array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')				    						,'CHECK'=>'SI'),
					 									 array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')                        ,'CHECK'=>'SI'),
														 array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cEstado']))  ,'CHECK'=>'SI'));

				if (f_MySql("INSERT","fpar0134",$cInsertTab,$xConexion01,$cAlfa)) {
				} else {
					$nSwitch = "1";
					f_Mensaje(__FILE__,__LINE__,"Error Guardando Datos de la Linea Inventario, Verifique");
				}
			break;
			/*****************************   UPDATE    ***********************************************/
/*
			case "EDITAR":
				if ($nSwitch == "0") {
					$cInsertTab	 = array(array('NAME'=>'condesxx','VALUE'=>trim(strtoupper($_POST['cConDes']))  ,'CHECK'=>'SI'),
					                     array('NAME'=>'condesxx','VALUE'=>trim(strtoupper($_POST['cConTip']))  ,'CHECK'=>'SI'),
										    		   array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId'])) ,'CHECK'=>'SI'),
					 									   array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')    										,'CHECK'=>'SI'),
					 									   array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')    		                ,'CHECK'=>'SI'),
														   array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cEstado']))  ,'CHECK'=>'SI'),
                               array('NAME'=>'conidxxx','VALUE'=>trim(strtoupper($_POST['cConId']))   ,'CHECK'=>'WH'));

						if (f_MySql("UPDATE","fpar0134",$cInsertTab,$xConexion01,$cAlfa)) {
							/***** Grabo Bien *****/
/*
							$nSwitch = "0";
						} else {
							$nSwitch = "1";
							f_Mensaje(__FILE__,__LINE__,"Error al Actualizar el Registro");
						}
				}
      break;
      /*****************************   UPDATE    ***********************************************/
/*
      case "ANULAR":
         if($_POST['cEstado']=="ACTIVO"){
           $cEstado="INACTIVO";
         }
         if($_POST['cEstado']=="INACTIVO"){
           $cEstado="ACTIVO";
         }

					$zInsertCab	 = array(array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId'])) ,'CHECK'=>'SI'),
					 									   array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
					 									   array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
														   array('NAME'=>'regestxx','VALUE'=>$cEstado                             ,'CHECK'=>'SI'),
                               array('NAME'=>'conidxxx','VALUE'=>trim(strtoupper($_POST['cConId']))   ,'CHECK'=>'WH'));

				 if (f_MySql("UPDATE","fpar0134",$zInsertCab,$xConexion01,$cAlfa)) {
					 /***** Grabo Bien *****/
/*
					 $nSwitch = "0";
				 } else {
					 $nSwitch = "1";
					 f_Mensaje(__FILE__,__LINE__,"Error al Actualizar el Registro");
				 }
      break;
    }
  } else {
  	f_Mensaje(__FILE__,__LINE__,"$cCadErr Verifique");
  }

 	if ($nSwitch == "0") {
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
*/
?>
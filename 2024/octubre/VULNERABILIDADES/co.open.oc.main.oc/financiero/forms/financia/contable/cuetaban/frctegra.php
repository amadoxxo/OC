<?php
  namespace openComex;
/**
 * Graba Cuentas Corrientes.
 * Este programa permite Guardar en la tabla Cuentas Corrientes.
 * @author
 * @package emisioncero
 */
	include("../../../../libs/php/utility.php");

	$nSwitch = "0"; // Switch para Vericar la Validacion de Datos
	$cCadErr = "";

	switch ($_COOKIE['kModo']) {
	  case "NUEVO":
	  case "EDITAR":
	  	/***** Validando Codigo *****/
  		if ($_POST['cBanId'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= " Codigo del Banco no puede ser vacio, \n";
  		}

  		/***** Validando Codigo *****/
  		if ($_POST['cBanCta'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= " Codigo de la Cuenta Corriente no puede ser vacio, \n";
  		}
  		
  		 /****** Validando Codigo *****/
			if ($_POST['cTipCta'] == "") {
		  		  $nSwitch = "1";
		  		  $cCadErr .= "El Tipo de Cuenta no puede ser vacio, \n";
  		}
  		
  		/***** Validando Codigo *****/
  		if ($_POST['cBanSuc'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= " Codigo de la Sucursal no puede ser vacio, \n";
  		}

  		/***** Validando Codigo *****/
  		if ($_POST['cBanDir'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= " La Direccion de la Sucursal del Banco no puede ser vacia, \n";
  		}

  		/***** Validando Codigo *****/
  		if ($_POST['cPucId'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= " Codigo de la Cuenta PUC no puede ser vacio, \n";
  		}

  		/***** Validando Codigo no exista *****/
	  	if ($_COOKIE['kModo'] == "NUEVO") {
		  	$cCodigo = trim(strtoupper($_POST['cBanId']));
		  	$cCuenta = trim(strtoupper($_POST['cBanCta']));
		  	$qCteCod  = "SELECT banidxxx,banctaxx ";
	      $qCteCod .= "FROM $cAlfa.fpar0128 WHERE banidxxx = \"$cCodigo\" AND banctaxx = \"$cCuenta\" LIMIT 0,1";
	  	  $xCteCod  = f_MySql("SELECT","",$qCteCod,$xConexion01,"");

	      //$vCteCod  = mysql_fetch_array($xCteCod);
	      $nCteCod  = mysql_num_rows($xCteCod);
	      if ($nCteCod > 0) {
	  		  $nSwitch = "1";
	  		  $cCadErr .= " Cuenta $cCuenta con el Banco $cCodigo ya existe, \n";
	  		}
	  	}
  	break;
	}	/***** Fin de la Validacion *****/

	/***** Ahora Empiezo a Grabar *****/
	/***** Pregunto si el SWITCH Viene en 0 para Poder Seguir *****/
	if ($nSwitch == "0") {
		switch ($_COOKIE['kModo']) {
			case "NUEVO":
  	    /**
  	     * Insert en la Tabla.
  	     */
        $cInsertTab	 = array(array('NAME'=>'banidxxx','VALUE'=>$cCodigo													     ,'CHECK'=>'SI'),
				                     array('NAME'=>'banctaxx','VALUE'=>$cCuenta														   ,'CHECK'=>'SI'),
				                     array('NAME'=>'banticta','VALUE'=>trim(strtoupper($_POST['cTipCta']))   ,'CHECK'=>'SI'),
				                     array('NAME'=>'bansucxx','VALUE'=>trim(strtoupper($_POST['cBanSuc']))   ,'CHECK'=>'SI'),
				                     array('NAME'=>'bandirxx','VALUE'=>trim(strtoupper($_POST['cBanDir']))   ,'CHECK'=>'SI'),
				                     array('NAME'=>'bancodcx','VALUE'=>trim(strtoupper($_POST['cBanCodc']))  ,'CHECK'=>'NO'),
				                     array('NAME'=>'pucidxxx','VALUE'=>trim(strtoupper($_POST['cPucId']))	   ,'CHECK'=>'SI'),
										    		 array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId']))  ,'CHECK'=>'SI'),
					 									 array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')												 ,'CHECK'=>'SI'),
					 									 array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')		                     ,'CHECK'=>'SI'),
					 									 array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												 ,'CHECK'=>'SI'),
					 									 array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                     ,'CHECK'=>'SI'),
														 array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cEstado']))   ,'CHECK'=>'SI'));

				if (f_MySql("INSERT","fpar0128",$cInsertTab,$xConexion01,$cAlfa)) {
				} else {
					$nSwitch = "1";
					f_Mensaje(__FILE__,__LINE__,"Error Guardando Datos de la Cuenta Corriente, Verifique");
				}
			break;
			/*****************************   UPDATE    ***********************************************/
			case "EDITAR":

				/***** Validaciones Particulares *****/
				/* Validado El Estado del Registro */
				if (!f_InList($_POST['cEstado'],"ACTIVO","INACTIVO")) {
					$nSwitch = "1";
					f_Mensaje(__FILE__,__LINE__,"El Estado del Registro No es Correcto, Verifique");
				}
				/***** Fin de Validaciones Particulares *****/
				if ($nSwitch == "0") {
					$cInsertTab	 = array(array('NAME'=>'bansucxx','VALUE'=>trim(strtoupper($_POST['cBanSuc']))   ,'CHECK'=>'SI'),
					                     array('NAME'=>'bandirxx','VALUE'=>trim(strtoupper($_POST['cBanDir']))   ,'CHECK'=>'SI'),
					                     array('NAME'=>'banticta','VALUE'=>trim(strtoupper($_POST['cTipCta']))   ,'CHECK'=>'SI'),
					                     array('NAME'=>'bancodcx','VALUE'=>trim(strtoupper($_POST['cBanCodc']))  ,'CHECK'=>'NO'),
				                     	 array('NAME'=>'pucidxxx','VALUE'=>trim(strtoupper($_POST['cPucId']))	   ,'CHECK'=>'SI'),
				                     	 array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId']))  ,'CHECK'=>'SI'),
					 									   array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												 ,'CHECK'=>'SI'),
					 									   array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                     ,'CHECK'=>'SI'),
														   array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cEstado']))   ,'CHECK'=>'SI'),
														   array('NAME'=>'banidxxx','VALUE'=>trim(strtoupper($_POST['cBanId']))    ,'CHECK'=>'WH'),
                               array('NAME'=>'banctaxx','VALUE'=>trim(strtoupper($_POST['cBanCta']))   ,'CHECK'=>'WH'));

						if (f_MySql("UPDATE","fpar0128",$cInsertTab,$xConexion01,$cAlfa)) {
							/***** Grabo Bien *****/
							$nSwitch = "0";
						} else {
							$nSwitch = "1";
							f_Mensaje(__FILE__,__LINE__,"Error al Actualizar el Registro");
						}
				}
      break;
      /*****************************   UPDATE    ***********************************************/
      case "ANULAR":
         if($_POST['cCliEst']=="ACTIVO"){
           $cEstado="INACTIVO";
         }
         if($_POST['cCliEst']=="INACTIVO"){
           $cEstado="ACTIVO";
         }

					$zInsertCab	 = array(array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId'])) ,'CHECK'=>'SI'),
					 									   array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
					 									   array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')												,'CHECK'=>'SI'),
														   array('NAME'=>'regestxx','VALUE'=>$cEstado                             ,'CHECK'=>'SI'),
                               array('NAME'=>'banidxxx','VALUE'=>trim(strtoupper($_POST['cBanId']))   ,'CHECK'=>'WH'),
                               array('NAME'=>'banctaxx','VALUE'=>trim(strtoupper($_POST['cBanCta']))  ,'CHECK'=>'WH'));

				 if (f_MySql("UPDATE","fpar0128",$zInsertCab,$xConexion01,$cAlfa)) {
					 /***** Grabo Bien *****/
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
?>
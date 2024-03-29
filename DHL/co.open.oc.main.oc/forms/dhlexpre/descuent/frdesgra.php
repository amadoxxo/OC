<?php
	/**
	 * Graba Descuentos 
	 * Este programa permite Grabar Descuentos en el Sistema.
	 * @author Juan Jose Trujillo <juan.trujillo@openits.co>
	 * @package openComex
	 */

	include("../../../libs/php/utility.php");

	/**
	 *  Cookie fija
	 */
	$kDf = explode("~",$_COOKIE["kDatosFijos"]);
	$kMysqlHost = $kDf[0];
	$kMysqlUser = $kDf[1];
	$kMysqlPass = $kDf[2];
	$kMysqlDb   = $kDf[3];
	$kUser      = $kDf[4];
	$kLicencia  = $kDf[5];
	$swidth     = $kDf[6];

	/**
	 * Variable para controlar si hay errores de validacion
	 * @var integer
	 */
	$nSwitch = 0;

	/**
	 * Variable para concatenar los mensajes de exito en el proceso.
	 * @var string
	 */
	$cMsjExi = "";

	/**
	 * Variable para concatenar los mensajes de validacion
	 * @var string
	 */
	$cMsj = "";

	/**
	 * Validando Licencia
	 */
	$nLic = f_Licencia();
	if ($nLic == 0){
		$nSwitch = 1;
		$cMsj .= "Error grave de Seguridad otro usuario ingreso con su clave\n";
	}

	/**
	 * Inicio de validaciones
	 */
	switch ($_COOKIE['kModo']) {
		case "NUEVO":
		case "EDITAR":

			// Validando el ID del Servicio
			if(empty($_POST['cSerId'])) {
				$nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "El Servicio No Puede Ser Vacio.\n";
			}

			// Validando el ID de la Forma de Cobro
			if (empty($_POST['cFcoId'])) {
				$nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "La Forma de Cobro No Puede Ser Vacia.\n";
			}

			// Validando el Codigo del Descuento
			if (empty($_POST['cDesCod'])) {
				$nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "El Codigo del Descuento No Puede Ser Vacio.\n";
			}

			// Validando el Porcentaje del Descuento
			if (empty($_POST['cDesPorc'])) {
				$nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "La Porcentaje del Descuento No Puede Ser Vacio.\n";
			}

			// Validando la Fecha de Creacion.
			if (empty($_POST['dRegFCre'])) {
				$nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "La Fecha de Creacion No Puede Ser Vacia.\n";
			}

			// Validando la Fecha de Modificacion.
			if (empty($_POST['dRegFMod'])) {
				$nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "La Fecha de Modificacion No Puede Ser Vacia.\n";
			}
			if ($_POST['dRegFMod'] != f_Fecha()) {
				$nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "La Fecha de Modificacion Debe Ser la Actual.\n";
			}

			// Validando la Hora de Creacion.
			if (empty($_POST['tRegHCre'])) {
				$nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "La Hora de Creacion No Puede Ser Vacia.\n";
			}

			// Validando la Hora de Modificacion.
			if (empty($_POST['tRegHMod'])) {
				$nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "La Hora de Modificacion No Puede Ser Vacia.\n";
			}

		 	// Validando el Estado del Descuento 
			if (empty($_POST['cRegEst'])) {
				$nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "El Campo Estado No Puede Estar Vacio.\n";
			}
			if ($_POST['cRegEst'] != "ACTIVO") {
				$nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "El Estado Debe Estar ACTIVO.\n";
			}

			if($nSwitch == 0) {
				// Validando que los codigos no existan.
				$qDescuento  = "SELECT desidxxx ";
				$qDescuento .= "FROM $cAlfa.fpar0164 ";
				$qDescuento .= "WHERE ";
				$qDescuento .= "seridxxx = \"{$_POST['cSerId']}\" AND ";
				$qDescuento .= "fcoidxxx = \"{$_POST['cFcoId']}\" AND ";
				$qDescuento .= "descodxx = \"{$_POST['cDesCod']}\" AND ";
				$qDescuento .= "desidxxx != \"{$_POST['cDesId']}\" LIMIT 0,1";
				$xDescuento  = f_MySql("SELECT","",$qDescuento,$xConexion01,"");
				if (mysql_num_rows($xDescuento) > 0) {
					$nSwitch = 1;
					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMsj .= "Ya Existe un Descuento con Servicio [". $_POST['cSerId'] ."], Forma de Cobro [". $_POST['cFcoId'] ."] y Codigo [". $_POST['cDesCod'] ."].\n";
				}
			}
		break;
		case "BORRAR":
			/*** No hace nada. ***/
		break;
		default:
			$nSwitch = 1;
			$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			$cMsj .= "El Modo de Grabado No Es Correcto.\n";
		break;
	}
	/*** Fin de Validaciones ***/

	/**
	 * Actualizacion en la Tabla.
	 */

	if ($nSwitch == 0) {
		switch ($_COOKIE['kModo']) {
			case "NUEVO":
				$qInsert = array(array('NAME'=>'seridxxx','VALUE'=>trim(strtoupper($_POST['cSerId'])) ,'CHECK'=>'SI'),
												array('NAME'=>'fcoidxxx','VALUE'=>trim(strtoupper($_POST['cFcoId']))  ,'CHECK'=>'SI'),
												array('NAME'=>'descodxx','VALUE'=>trim(strtoupper($_POST['cDesCod'])) ,'CHECK'=>'SI'),
												array('NAME'=>'desporce','VALUE'=>trim(strtoupper($_POST['cDesPorc'])),'CHECK'=>'SI'),
												array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($kUser))            ,'CHECK'=>'SI'),
												array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')		    							  ,'CHECK'=>'SI'),
												array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')	                      ,'CHECK'=>'SI'),
												array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')						    			  ,'CHECK'=>'SI'),
												array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')                       ,'CHECK'=>'SI'),
												array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cRegEst'])) ,'CHECK'=>'SI'));

				if (f_MySql("INSERT","fpar0164",$qInsert,$xConexion01,$cAlfa)) {
					/***** Grabo Bien *****/
					$cMsjExi = "Se Inserto el Registro con Exito.\n";
				}else{
					$nSwitch = 1;
					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMsj .= "Error Al Guardar el Descuento.\n";
				}
			break;
			case "EDITAR":
				$qUpdate = array(array('NAME'=>'seridxxx','VALUE'=>trim(strtoupper($_POST['cSerId'])) ,'CHECK'=>'SI'),
												array('NAME'=>'fcoidxxx','VALUE'=>trim(strtoupper($_POST['cFcoId']))  ,'CHECK'=>'SI'),
												array('NAME'=>'descodxx','VALUE'=>trim(strtoupper($_POST['cDesCod'])) ,'CHECK'=>'SI'),
												array('NAME'=>'desporce','VALUE'=>trim(strtoupper($_POST['cDesPorc'])),'CHECK'=>'SI'),
												array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($kUser))            ,'CHECK'=>'SI'),
												array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')    		                ,'CHECK'=>'SI'),
												array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')						    			  ,'CHECK'=>'SI'),
												array('NAME'=>'desidxxx','VALUE'=>trim(strtoupper($_POST['cDesId']))  ,'CHECK'=>'WH'));

				if (f_MySql("UPDATE","fpar0164",$qUpdate,$xConexion01,$cAlfa)) {
					/***** Grabo Bien *****/
					$cMsjExi = "Se Edito el Registro con Exito.\n";
				}else{
					$nSwitch = 1;
					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMsj .= "Error al Actualizar el Registro.\n";
				}
			break;
			case "BORRAR":
				$qDelete = array(array('NAME'=>'desidxxx','VALUE'=>trim(strtoupper($_POST['cDesId']))   ,'CHECK'=>'WH'));
				if (f_MySql("DELETE","fpar0164",$qDelete,$xConexion01,$cAlfa)) {
					/***** Grabo Bien *****/
					$cMsjExi = "Se Elimino el Registro con Exito.\n";
				}else{
					$nSwitch = 1;
					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMsj .= "Error al Eliminar el Registro.\n";
				}
			break;
		}
	}

	if ($nSwitch == 0){
		f_Mensaje(__FILE__,__LINE__,$cMsjExi);
		?>
		<form name = "frnav" action = "frdesini.php" method = "post" target = "fmwork"></form>
		<script languaje = "javascript">
			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_New ?>/nivel3.php";
			document.forms['frnav'].submit();
		</script>
		<?php
	}else{
		f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique");
	}
?>

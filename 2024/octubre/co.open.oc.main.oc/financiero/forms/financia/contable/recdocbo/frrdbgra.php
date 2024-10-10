<?php
  namespace openComex;
/**
  * Graba Recuperacion de Documentos Contable Borrados.
  * @author Johana Arboleda Ramos <johana.arboleda@opentecnologia.com.co>
  * @package openComex
  */
	
	include("../../../../libs/php/utility.php");
	include("../../../../libs/php/utirecdo.php");
	include("../../../../config/config.php");
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

  $nSwitch = 0; // Switch para Vericar la Validacion de Datos
  $cMsj = "";

	switch ($_COOKIE['kModo']) {
	  case "RECUPERAR":
	  	
	  	/**
	  	  * Validando Licencia
	  	  */
	  	$nLic = f_Licencia();
	  	if ($nLic == 0){
	  		$nSwitch = 1;
	  		$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	  		$cMsj .= "Error grave de Seguridad otro usuario ingreso con su clave.\n";
	  	}
	  	
	  	/**
	  	 * Validando que el campo logidxxx no llegue vacio.
	  	 */
	  	if ($_POST['cLogId'] == ""){
	  		$nSwitch = 1;
	  		$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	  		$cMsj .= "El Id del Log de Comprobantes Borrados No Puede Ser Vacio.\n";
	  	}
	  	
	  	
		break;
		default:
			$nSwitch = 1;
		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			$cMsj .= "El Modo de Grabado No Es Correcto.\n";
		break;
	}

	if ($nSwitch == 0) {
	
	switch ($_COOKIE['kModo']) {
			case "RECUPERAR":
				$ObjRecupera = new cRecuperaDocumento();
				$mRetorna = $ObjRecupera->fnRecuperaDocumento($_POST['cLogId']);
		  break;
		  default:
		    //No hace nada
		  break;
		}
	}
	
	if($mRetorna[0] == "false") {
		$cMsj = "Se Presentaron los Siguientes Errores al Recuperar el Documento:\n\n";
		for ($i=2; $i<count($mRetorna); $i++) {
			$cMsj .= $mRetorna[$i]."\n";
		}
		f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique");
	}
  
  if($mRetorna[0] == "true") {
  	$cTexto = ($mRetorna[2] == "INACTIVO") ? $mRetorna[1].", por favor Active el Comprobante desde su Modulo Correspondiente" : $mRetorna[1];
		f_Mensaje(__FILE__,__LINE__, $cTexto);
	?>
		<form name = "frgrm" action = "frrdbini.php" method = "post" target = "fmwork"></form>
		<script languaje = "javascript">document.forms['frgrm'].submit();</script>
<?php } ?>

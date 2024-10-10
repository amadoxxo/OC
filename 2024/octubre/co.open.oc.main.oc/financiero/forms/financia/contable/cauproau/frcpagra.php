<?php
  namespace openComex;
  /** Modificacion codigo 2007-05-24
	 *  1) Switch que estaba como caracter se dejo numerico  [ Antes $nSwitch == "0" ahora $nSwitch == 0]
	 *  2) Se verifico que el switch, No cambiara.
	 *  3) Se agrego case BORRAR cuando Actualizo en la tabla de consecutivos
	 *  4) Se Quito linea de Codigo comentariada despues de insertar en la 1001.
	 *	[Alexander Gordillo / 20101009]: Se incluye la funcinalidad del control de saldos por documento cruce
	 *  [Alexander Gordillo / 20101009]: Se incluye la funcinalidad del control de cupos financieros
	 */

  ini_set('error_reporting', E_ERROR);
  ini_set("display_errors","1");

  date_default_timezone_set('America/Bogota');

	include("../../../../libs/php/utility.php");
	include("../../../../libs/php/uticonta.php");
  include("../../../../libs/php/utitrans.php");
  include("../../../../libs/php/utiajuxx.php");
  include("../../../../libs/php/uticupro.php");
  include("../../../../libs/php/uticpaxx.php");

	//f_Mensaje(__FILE__,__LINE__,$_POST['nTimesSave']);

  $nSwitch = 0; 
  $cMensaje = "";
  $cMsjAdv  = "";
  
  //Vector con datos $_POST del formulario
  $vDatos = array();

  //Preparando datos para enviar guadado del comprobante al uticpaxx cuando vienen por post
  if (count($_POST) > 0) {
    $vDatos = $_POST;
  }

  //Preparando datos para enviar guadado del comprobante al uticpaxx cuando vienen por get
  if (count($_GET) > 0) {
    $vDatos = $_GET;
  }
  //asignando modo de grabado
  $vDatos['kModo'] = $_COOKIE['kModo'];

  #Creando Causacion pagos a terceros automatica
  #Creando la instancia para la creacion de Causacion pagos a terceros automatica
  $ObjCau = new cCausacionPagosTercerosAutomatica();
  $mRetorna = $ObjCau->fnGuardarCausacionPagosTercerosAutomatica($vDatos);

  //Retorna:
  //[0]  => true o false
  //[1]  => cComId
	//[2]  => cComCod
	//[3]  => cComCsc
	//[4]  => cComCsc2
  //[5]  => dComFec
  //[6]  => array con Advertencias
  //[+7] => Listado de errores
  
  $nSwitch = ($mRetorna[0] == "true") ? 0 : 1;
  for ($i=7; $i<count($mRetorna); $i++) {
    $mAuxText = explode("~",$mRetorna[$i]);
    $cMensaje .= ($mAuxText[0] != "") ? "Linea ".str_pad($mAuxText[0],4,"0",STR_PAD_LEFT).": " : "";
    $cMensaje .= $mAuxText[1]."\n";
  }

  //Advertencias
  if (count($mRetorna[6]) > 0) {
    for ($i=0; $i<count($mRetorna[6]); $i++) {
      $mAuxText = explode("~",$mRetorna[6][$i]);
      $cMsjAdv .= ($mAuxText[0] != "") ? "Linea ".str_pad($mAuxText[0],4,"0",STR_PAD_LEFT).": " : "";
      $cMsjAdv .= $mAuxText[1]."\n";
    }
  }

	if ($nSwitch == 0) {
		switch ($_COOKIE['kModo']) {
			case "NUEVO":
			case "ANTERIOR":
			case "EDITAR":
      case "BORRAR":
      case "VERIFICAR":
      case "CAMBIAESTADO";
				if ($nSwitch == 0) {
          f_Mensaje(__FILE__,__LINE__,"\n".$cMensaje);
          if ($cMsjAdv != "") {
            f_Mensaje(__FILE__,__LINE__,"\n".$cMsjAdv);
          }
          ?>
          <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
          <script languaje = "javascript">document.forms['frgrm'].submit();</script>
				<?php }
			break;
      case "AJUSTEAUTO":
        if ($nSwitch == 0) {
          f_Mensaje(__FILE__,__LINE__,"\n".$cMensaje);
          if ($cMsjAdv != "") {
            f_Mensaje(__FILE__,__LINE__,"\n".$cMsjAdv);
          } ?>
          <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
					<script languaje = "javascript">
						parent.window.opener.document.forms['frgrm'].submit();
						document.forms['frgrm'].submit();
						parent.window.close();
					</script>
        <?php }
      break;
			default:
			break;
		}
	}

	if ($nSwitch != 0) { 
    f_Mensaje(__FILE__,__LINE__,"\nHa Ocurrido un Error en el Proceso, Verifique.\n".$cMensaje);
    if ($cMsjAdv != "") {
      f_Mensaje(__FILE__,__LINE__,"\n".$cMsjAdv);
    }?>
		<script languaje = "javascript">
    	if ("<?php echo $_COOKIE['kModo'] ?>" != "CAMBIAESTADO" && "<?php echo $_COOKIE['kModo'] ?>" != "AJUSTEAUTO") {
	  	  parent.fmwork.document.forms['frgrm']['nTimesSave'].value = 0;
	  	  parent.fmwork.document.forms['frgrm']['Btn_Guardar'].disabled = false;
	  	}
		</script>
	<?php }
?>

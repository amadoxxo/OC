<?php
  namespace openComex;
  include("../../../../../financiero/libs/php/utility.php");
  include("../../../../libs/php/uticclix.php");

  $kDf = explode("~",$_COOKIE["kDatosFijos"]);
  $kMysqlHost = $kDf[0];
  $kMysqlUser = $kDf[1];
  $kMysqlPass = $kDf[2];
  $kMysqlDb   = $kDf[3];
  $kUser      = $kDf[4];
  $kLicencia  = $kDf[5];
  $swidth     = $kDf[6];

  /**
   * Variable para saber si hay o no errores de validacion.
   *
   * @var number
   */
  $nSwitch = 0;

  /**
   * Variable para concatenar errores de validacion u otros.
   *
   * @var string
   */
  $cMsj = "\n";

  /**
   * Datos enviados, debe ser un parametro por referencia, ya que se modificaran valores en las validaciones
   * @var array
   */
  $pArrayDatos          = array();
  $pArrayDatos          = $_POST;
  $pArrayDatos['cModo'] =	$_COOKIE['kModo']; // Modo de grabado
  
  # Creando la instancia para procesar clientes
  $ObjcClientes = new cClientes();
  # Validar Cliente
  $mRetVal      = $ObjcClientes->fnValidarCliente($pArrayDatos); //Se envian todos los datos que llegan por POST
  if ($mRetVal[0] == "false") {
    $nSwitch = 1;
    for ($nR=1; $nR<count($mRetVal); $nR++) {
      $mAuxText = explode("~",$mRetVal[$nR]);
      $cMsj .= ($mAuxText[0] != "") ? "Linea ".str_pad($mAuxText[0],4,"0",STR_PAD_LEFT).": " : "";
      $cMsj .= $mAuxText[1]."\n";
    }
  }

  if ($nSwitch == 0) {
    $mRetVal = $ObjcClientes->fnGuardarCliente($pArrayDatos); //Se envian todos los datos que llegan por POST
    if ($mRetVal[0] == "false") {
      $nSwitch = 1;
      for ($nR=1; $nR<count($mRetVal); $nR++) {
        $mAuxText = explode("~",$mRetVal[$nR]);
        $cMsj .= ($mAuxText[0] != "") ? "Linea ".str_pad($mAuxText[0],4,"0",STR_PAD_LEFT).": " : "";
        $cMsj .= $mAuxText[1]."\n";
      }
    }
  }

  if ($nSwitch == 1) {
    f_Mensaje(__FILE__,__LINE__,"$cMsj Verifique.");
  }

  if ($nSwitch == 0) {
    if($_COOKIE['kModo']!="CAMBIAESTADO"){
      f_Mensaje(__FILE__,__LINE__,"El Registro se Guardo con Exito.");
    }
    if($_COOKIE['kModo']=="CAMBIAESTADO"){
      f_Mensaje(__FILE__,__LINE__,"El Registro Cambio de Estado Con Exito.");
    }
    ?>
    <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
    <script languaje = "javascript">
      parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
      document.forms['frgrm'].submit()
    </script>
    <?php
  }
?>
<?php
  /**
   * Guarda o Actualiza registro de Texto Factura de Venta DHL.
   * Descripción: Si no hay un registro de Texto Factura de Venta DHL lo crea,
   * si ya existe lo actualiza.
   * 
   * @author Elian Amado <elian.amado@openits.co>
   * @package openComex
   */

  include("../../../libs/php/utility.php");

  // Cookie fija
  $kDf = explode("~",$_COOKIE["kDatosFijos"]);
  $kMysqlHost = $kDf[0];
  $kMysqlUser = $kDf[1];
  $kMysqlPass = $kDf[2];
  $kMysqlDb   = $kDf[3];
  $kUser      = $kDf[4];
  $kLicencia  = $kDf[5];
  $swidth     = $kDf[6];

  // Variable para saber si hay o no errores de validacion.
  $nSwitch = 0;

  // Variables para reemplazar caracteres especiales
  $cBuscar = array('"',"'","<",">","«","»","%",chr(13),chr(10),chr(27),chr(9));
  $cReempl = array('\"',"\'","&#60;","&#62;","&#171;","&#187;","&#37;"," "," "," "," ");

  // Variable para concatenar los errores de validacion
  $cMsj = "";

  // Validando Licencia
  $nLic = f_Licencia();
  if ($nLic == 0){
    $nSwitch = 1;
    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
    $cMsj .= "Error grave de Seguridad otro usuario ingreso con su clave.\n";
  }

  // Validando los datos que llegan por POST.

  //Validando el Titulo.
  if(trim($_POST['cTfvTitu']) == ""){
    $nSwitch = 1;
    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
    $cMsj .= "El Titulo No Puede ser Vacío.\n";
  }

  // Validando Contenido
  if(trim($_POST['cTfvCont']) == ""){
    $nSwitch = 1;
    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
    $cMsj .= "El campo Contenido A No Puede ser Vacío.\n";
  }

  // Fin de Validaciones
  if($nSwitch == 0){
    $cAccion = "";

    /**
     * Esta tabla solo contendra un registro, se consulta para saber si existe el registro 
     * y editarlo, de lo contrario se crea un registro.
     */
    $qTexNotS  = "SELECT * ";
    $qTexNotS .= "FROM $cAlfa.zdex0011 LIMIT 0,1";
    $xTexNotS  = f_MySql("SELECT","",$qTexNotS,$xConexion01,"");

    if (mysql_num_rows($xTexNotS) == 1) {
      $vTexNotS = mysql_fetch_array($xTexNotS);

      // Actualizando Texto Factura de Venta DHL.
      $qUpdate =  array(array('NAME'=>'tfvtitxx','VALUE'=>str_replace($cBuscar,$cReempl,trim($_POST['cTfvTitu'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'tfvcontx','VALUE'=>str_replace($cBuscar,$cReempl,trim($_POST['cTfvCont'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')                                           ,'CHECK'=>'SI'),
                        array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')                                           ,'CHECK'=>'SI'),
                        array('NAME'=>'tfvidxxx','VALUE'=>$vTexNotS['tfvidxxx']                                   ,'CHECK'=>'WH'));

      if(!f_MySql("UPDATE","zdex0011",$qUpdate,$xConexion01,$cAlfa)){
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Error al Actualizar el Registro de Texto Factura de Venta DHL, Favor Informar a openTecnologia S.A.\n";
      }else{
        $cAccion = "Actualizados";
      }
    }else{
      /**
       * Insertando registro de Texto Factura de Venta DHL.
       */
      $qInsert =  array(array('NAME'=>'tfvtitxx','VALUE'=>str_replace($cBuscar,$cReempl,trim($_POST['cTfvTitu'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'tfvcontx','VALUE'=>str_replace($cBuscar,$cReempl,trim($_POST['cTfvCont'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'regusrxx','VALUE'=>$kUser                                                  ,'CHECK'=>'SI'),
                        array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')                                           ,'CHECK'=>'SI'),
                        array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')                                           ,'CHECK'=>'SI'),
                        array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')                                           ,'CHECK'=>'SI'),
                        array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')                                           ,'CHECK'=>'SI'),
                        array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cRegEst']))                     ,'CHECK'=>'SI'));

      if (!f_MySql("INSERT","zdex0011",$qInsert,$xConexion01,$cAlfa)) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Error al Guardar el Registro de Texto Factura de Venta DHL, Favor Informar a openTecnologia S.A.\n";
      }else{
        $cAccion = "Guardados";
      }
    }
  }

  if ($nSwitch == 0) {
    $cMsj = "Datos $cAccion con Exito.\n";

    f_Mensaje(__FILE__,__LINE__,utf8_encode($cMsj)); ?>
    <form name = "frgrm" action = "frtfvnue.php" method = "post" target = "fmwork"></form>
      <script languaje = "javascript">
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_New ?>/nivel3.php";
        document.forms['frgrm'].submit();
      </script>
  <?php
  } else {
    f_Mensaje(__FILE__,__LINE__,utf8_decode($cMsj."Verifique.\n"));
  }
?>
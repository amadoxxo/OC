<?php
  namespace openComex;
  /**
   * Graba Inactivacion Terceros con Movimiento Contable.
   * Este programa permite Grabar Inactivacion Terceros con Movimiento Contable.
   * @author Juan Jose Trujillo <juan.trujillo@open-eb.co>
   * @package openComex
   */
  include("../../../../libs/php/utility.php");
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
  /**
   * Validando Licencia
   */
  $nLic = f_Licencia();
  if ($nLic == 0){
    $nSwitch = 1;
    $cMsj .= "Error grave de Seguridad otro usuario ingreso con su clave\n";
  }
  
  /**
   * Primero valido los datos que llegan por metodo POST.
   */
  switch ($_COOKIE['kModo']) {
    case "NUEVO":
    case "EDITAR":

      /**
       * Validando el Nit del Cliente
       */
      if(empty($_POST['cTerId'])){
        $nSwitch = 1;
        $cMsj .= "El Nit del Cliente, No Puede ser Vacio.\n";
      }

      /**
       * Validando que el Cliente Exista en el Sistema y este ACTIVO
       */
      if(!empty($_POST['cTerId'])){
        $vCliObsx  = "";
        $qCliObsx  = "SELECT ";
        $qCliObsx .= "CLIOBSIN ";
        $qCliObsx .= "FROM $cAlfa.SIAI0150 ";
        $qCliObsx .= "WHERE ";
        $qCliObsx .= "CLIIDXXX = \"{$_POST['cTerId']}\" AND ";
        $qCliObsx .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
        $xCliObsx  = f_MySql("SELECT","",$qCliObsx,$xConexion01,"");
        if (mysql_num_rows($xCliObsx) > 0) {
          $vCliObsx = mysql_fetch_array($xCliObsx);
        } else {
          $nSwitch = 1;
          $cMsj .= "El Cliente No Existe en el Sistema o esta INACTIVO, No Puede ser Vacio.\n";
        }
      }

      /**
       * Validando la Observacion de Inactivacion de Tercero
       */
      if (empty($_POST['cCliObsIn'])) {
        $nSwitch = 1;
        $cMsj .= "La Observacion de Inactivacion, No Puede ser Vacia.\n";
      }

    break;
    case "CAMBIAESTADO":
    case "BORRAR":
      /**
       * No hace nada.
       */
    break;
    default:
      $nSwitch = 1;
      $cMsj .= "El Modo de Grabado No Es Correcto.\n";
    break;
  }
  /**
   * Fin de Primero valido los datos que llegan por metodo POST.
   */
  /**
   * Actualizacion en la Tabla.
   */

  if ($nSwitch == 0) {
    switch ($_COOKIE['kModo']) {
      case "NUEVO":
        $cObs  = $vCliObsx['CLIOBSIN'];
        $cObs .= "|___";
        $cObs .= trim(strtoupper("Inactivo"))."__";
        $cObs .= trim(strtoupper($kUser))."__";
        $cObs .= date('Y-m-d')."__";
        $cObs .= date('H:i')."__";
        $cObs .= trim(strtoupper($_POST['cCliObsIn']));
        $cObs .= "___|";

        $qUpdate = array(array('NAME'=>'REGESTXX','VALUE'=>"INACTIVO"             ,'CHECK'=>'SI'),
                         array('NAME'=>'CLIOBSIN','VALUE'=>$cObs                  ,'CHECK'=>'SI'),
                         array('NAME'=>'REGMODXX','VALUE'=>date('Y-m-d')					,'CHECK'=>'SI'),
                         array('NAME'=>'REGHMODX','VALUE'=>date('H:i')    		    ,'CHECK'=>'SI'),
                         array('NAME'=>'CLIIDXXX','VALUE'=>trim($_POST['cTerId']) ,'CHECK'=>'WH'));
         
        if (f_MySql("UPDATE","SIAI0150",$qUpdate,$xConexion01,$cAlfa)) {
          /***** Grabo Bien *****/
          $nSwitch = 0;
          f_Mensaje(__FILE__,__LINE__,"Se actualizo el Registro con Exito");
        }else{
          $nSwitch = 1;
          f_Mensaje(__FILE__,__LINE__,"Error al Actualizar el Registro");
        }
      break;
      case "EDITAR":
        //no hace nada
      break;
      case "CAMBIAESTADO":
        //no hace nada
      break;
    }
  }else{
    f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique");
  }
  
  if ($nSwitch == 0){
    ?>
    <form name = "frnav" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
    <script languaje = "javascript">
      parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
      document.forms['frnav'].submit();
    </script>
    <?php
  }
?>
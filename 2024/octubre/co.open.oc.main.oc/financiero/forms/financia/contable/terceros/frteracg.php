<?php
  namespace openComex;
/**
 * Actualiza Correos Notificacion Rechazo Revisor Fiscal
 * Este programa permite Actualizar Correos Notificacion Rechazo Revisor Fiscal
 * @author Camilo Dulce <camilo.dulce@open-eb.co>
 * @package openComex
 * 
 * Variables:
 */

# Librerias
include ("../../../../libs/php/utility.php");

# Cookie fija
$kDf = explode("~",$_COOKIE["kDatosFijos"]);
$kMysqlHost = $kDf[0];
$kMysqlUser = $kDf[1];
$kMysqlPass = $kDf[2];
$kMysqlDb   = $kDf[3];
$kUser      = $kDf[4];
$kLicencia  = $kDf[5];
$swidth     = $kDf[6];

//Validar que los clientes existan
$nSwitch = 0;

$mClientes = explode("|", $_POST['cTerIds']);

$mDatos =  array();
for ($i=0; $i < count($mClientes); $i++) {
  if($mClientes[$i] != ""){
    $qDatCli  = "SELECT ";
    $qDatCli .= "CLIIDXXX ";
    $qDatCli .= "FROM $cAlfa.SIAI0150 ";
    $qDatCli .= "WHERE "; 
    $qDatCli .= "CLIIDXXX = \"{$mClientes[$i]}\" LIMIT 0,1"; 
    $xDatCli  = f_MySql("SELECT","",$qDatCli,$xConexion01,"");
    if(mysql_num_rows($xDatCli) == 0) {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "El Cliente[{$mClientes[$i]}] No Existe";
    }

    $nInd_mDatos = count($mDatos);
    $mDatos[$nInd_mDatos]['CLIIDXXX'] = $mClientes[$i] ;  
    $mDatos[$nInd_mDatos]['CLICNRRF'] = $_POST['cCliCnrRf'];
  }
}

if(trim($_POST['cCliCnrRf']) != ""){
  $vCorreos = explode(",", $_POST['cCliCnrRf']);
  for ($i=0; $i < count($vCorreos); $i++) { 
    $vCorreos[$i] = trim($vCorreos[$i]);
    if($vCorreos[$i] != ""){
      if (!filter_var($vCorreos[$i], FILTER_VALIDATE_EMAIL)) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= " El Correo Notificacion Rechazo Revisor Fiscal[".$vCorreos[$i]."], No es Valido.\n";
      }
    }
  }
}

if($nSwitch == 0) {
  for($i=0; $i<count($mDatos); $i++) {
    $qUpdate    = array(array('NAME'=>'CLICNRRF','VALUE'=>trim($mDatos[$i]['CLICNRRF'])       ,'CHECK'=>'NO'),
                        array('NAME'=>'CLIIDXXX','VALUE'=>trim($mDatos[$i]['CLIIDXXX'])       ,'CHECK'=>'WH'));       
    if (!f_MySql("UPDATE","SIAI0150",$qUpdate,$xConexion01,$cAlfa)) {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "Error al Actualizar el Registro.\n";
    } 
  }
}

if($nSwitch == 0){
  f_Mensaje(__FILE__,__LINE__,"Se Actualizaron los Datos con Exito");
  ?>
    <script type="text/javascript">
      parent.window.close();
    </script>
  <?php
}else{
  f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique");
}

?>
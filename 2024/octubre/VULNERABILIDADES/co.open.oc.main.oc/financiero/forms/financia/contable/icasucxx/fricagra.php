<?php
  namespace openComex;
/**
 * Graba Ica x Sucursal.
 * Este programa permite Guardar en la tabla de Ciudades la Tarifa Ica x Sucursal.
 * @author
 * @package emisioncero
 */
	include("../../../../libs/php/utility.php");

	$nSwitch = "0"; // Switch para Vericar la Validacion de Datos
	$cCadErr = "";

  switch ($_COOKIE['kModo']) {
    case "NUEVO":
    case "EDITAR":
    /***Valido el pais ***/
    if ($_POST['cPaiId'] == "") {
      $nSwitch = "1";
      $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cCadErr .= "El Pais no puede ser vacio \n";
    }
    
    /***Valido el departamento ***/
    if ($_POST['cDepId'] == "") {
      $nSwitch = "1";
      $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cCadErr .= "El Departamento no puede ser vacio \n";
    } 		
    
    /***Valido la ciudad ***/
    if ($_POST['cCiuId'] == "") {
      $nSwitch = "1";
      $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cCadErr .= "La Cuidad no puede ser vacia \n";
    }
    
    /***Valido la sucursal ***/
    if ($_POST['cSucId'] == "") {
      $nSwitch = "1";
      $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cCadErr .= "La Sucursal no puede ser vacia \n";
    }
    
    /***Valido el centro de costo ***/
    if ($_POST['cCcoId'] == "") {
      $nSwitch = "1";
      $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cCadErr .= "El Centro de Costo no puede ser vacio \n";
    }
    
    /***Valido la cuenta PUC retencion ICA ***/
    if ($_POST['cPucId'] == "") {
      $nSwitch = "1";
      $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cCadErr .= "La Cuenta PUC de Rencion ICA no puede ser vacia \n";
    }
    
    /***Valido la tarifa de retencion ICA ***/
    if ($_POST['cCiuIca'] == "") {
      $nSwitch = "1";
      $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cCadErr .= "La Tarifa ICA de Rencion ICA no puede ser vacia \n";
    }else{
      if ($_POST['cCiuIca'] <= 0 || $_POST['cCiuIca'] > 100) {
        $nSwitch = "1";
        $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cCadErr .= "La Tarifa ICA de Rencion ICA debe ser mayor a cero (0) y menor o igual a cien (100) \n";
      }
    }
    
    if ($_POST['cPucId2'] <> "" || $_POST['cCiuIca2'] <> "") {
      /***Valido la cuenta PUC autoretencion ICA ***/
      if ($_POST['cPucId2'] == "") {
        $nSwitch = "1";
        $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cCadErr .= "La Cuenta PUC de Autorencion ICA no puede ser vacia \n";
      }
      
      /***Valido la tarifa de autoretencion ICA ***/
      if ($_POST['cCiuIca2'] == "") {
        $nSwitch = "1";
        $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cCadErr .= "La Tarifa ICA de Autorencion ICA no puede ser vacia \n";
      }else{
        if ($_POST['cCiuIca2'] <= 0 || $_POST['cCiuIca2'] > 100) {
          $nSwitch = "1";
          $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cCadErr .= "La Tarifa ICA de Rencion ICA debe ser mayor a cero (0) y menor o igual a cien (100) \n";
        }
      }
    }
    
    /**
     * Validando que la sucursal exista
     */
    $qDatTer  = "SELECT * ";
    $qDatTer .= "FROM $cAlfa.SIAI0055 ";
    $qDatTer .= "WHERE ";
    $qDatTer .= "PAIIDXXX = \"".trim(strtoupper($_POST['cPaiId']))."\" AND ";
    $qDatTer .= "DEPIDXXX = \"".trim(strtoupper($_POST['cDepId']))."\" AND ";
    $qDatTer .= "CIUIDXXX = \"".trim(strtoupper($_POST['cCiuId']))."\" AND ";
    $qDatTer .= "REGESTXX = \"ACTIVO\" ";
    $xDatTer = f_MySql("SELECT","",$qDatTer,$xConexion01,"");
    // f_Mensaje(__FILE__,__LINE__,$qDatTer."~".mysql_num_rows($xDatTer));
    if (mysql_num_rows($xDatTer) == 0) {
      $nSwitch = "1";
      $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cCadErr .= "El Pais-Departamento-Ciudad [".trim(strtoupper($_POST['cPaiId']))."-".trim(strtoupper($_POST['cDepId']))."-".trim(strtoupper($_POST['cCiuId']))."] No Existe o esta Inactivo.\n";
    }
    
    /** Validando que la sucursal y el centro de consto sean unicos en la tabla*/
    $cSucId = trim(strtoupper($_POST['cSucId']));
    $cCcoId = trim(strtoupper($_POST['cCcoId']));
    
    $qDatCiu  = "SELECT * ";
	  $qDatCiu .= "FROM $cAlfa.SIAI0055 ";
	  $qDatCiu .= "WHERE ";
	  $qDatCiu .= "SUCIDXXX = \"$cSucId\" AND ";
	  $qDatCiu .= "CCOIDXXX = \"$cCcoId\" LIMIT 0,1";
	  $xDatCiu = f_MySql("SELECT","",$qDatCiu,$xConexion01,"");
    if(mysql_num_rows($xDatCiu) > 0){
	    switch ($_COOKIE['kModo']) {
        case "NUEVO":
          $nSwitch = "1";
          $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cCadErr .= "La Sucursal y el Centro de Costo ya se encuentran parametrizados para otra Ciudad.\n";
        break;
        case "EDITAR":
          $xRDC = mysql_fetch_array($xDatCiu);
          if($xRDC['PAIIDXXX'] <> trim(strtoupper($_POST['cPaiId'])) || $xRDC['DEPIDXXX'] <> trim(strtoupper($_POST['cDepId'])) || $xRDC['CIUIDXXX'] <> trim(strtoupper($_POST['cCiuId']))){
            $nSwitch = "1";
            $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cCadErr .= "La Sucursal y el Centro de Costo ya se encuentran parametrizados para la Ciudad de {$xRDC['CIUDESXX']}.\n";
          }
        break;
	    }
    }
    
    break;
  }
	//}	/***** Fin de la Validacion *****/

	/***** Ahora Empiezo a Grabar *****/
	/***** Pregunto si el SWITCH Viene en 0 para Poder Seguir *****/
	if ($nSwitch == "0") {
		switch ($_COOKIE['kModo']) {
		  /*****************************   UPDATE    ***********************************************/
			case "NUEVO":
			case "EDITAR":
        if ($nSwitch == "0") {
          $cInsertTab	= array(array('NAME'=>'SUCIDXXX','VALUE'=>trim(strtoupper($_POST['cSucId']))           ,'CHECK'=>'SI'),
                              array('NAME'=>'CCOIDXXX','VALUE'=>trim(strtoupper($_POST['cCcoId']))           ,'CHECK'=>'SI'),
                              array('NAME'=>'CIUICAXX','VALUE'=>trim(strtoupper($_POST['cCiuIca']))          ,'CHECK'=>'SI'),
                              array('NAME'=>'PUCIDXXX','VALUE'=>trim(strtoupper($_POST['cPucId']))           ,'CHECK'=>'SI'),
                              array('NAME'=>'CIUICA2X','VALUE'=>trim(strtoupper($_POST['cCiuIca2']))         ,'CHECK'=>'NO'),
                              array('NAME'=>'PUCID2XX','VALUE'=>trim(strtoupper($_POST['cPucId2']))          ,'CHECK'=>'NO'),
                              array('NAME'=>'REGMODXX','VALUE'=>date('Y-m-d')			                           ,'CHECK'=>'SI'),
                              array('NAME'=>'REGHORXX','VALUE'=>date('H:i')		                               ,'CHECK'=>'SI'),
                              array('NAME'=>'PAIIDXXX','VALUE'=>trim(strtoupper($_POST['cPaiId']))           ,'CHECK'=>'WH'),
                              array('NAME'=>'DEPIDXXX','VALUE'=>trim(strtoupper($_POST['cDepId']))           ,'CHECK'=>'WH'),
                              array('NAME'=>'CIUIDXXX','VALUE'=>trim(strtoupper($_POST['cCiuId']))           ,'CHECK'=>'WH'));
          
          if (f_MySql("UPDATE","SIAI0055",$cInsertTab,$xConexion01,$cAlfa)) {
            /***** Grabo Bien *****/
            $nSwitch = "0";
          } else {
            $nSwitch = "1";
            f_Mensaje(__FILE__,__LINE__,"Error al Actualizar el Registro");
          }
        }
      break;
    }
  } else {
  	f_Mensaje(__FILE__,__LINE__,"$cCadErr");
  }

 	if ($nSwitch == "0") {
 	  if($_COOKIE['kModo']!="ANULAR"){
 		  f_Mensaje(__FILE__,__LINE__,"El Registro se cargo con Exito");
 	  }
 	  /*if($_COOKIE['kModo']=="ANULAR"){
 	    f_Mensaje(__FILE__,__LINE__,"El Registro Cambio de Estado Con Exito");
 	  }*/
 		?>
		<form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
			<script languaje = "javascript">
  			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
				document.forms['frgrm'].submit()
			</script>
  	<?php }
?>
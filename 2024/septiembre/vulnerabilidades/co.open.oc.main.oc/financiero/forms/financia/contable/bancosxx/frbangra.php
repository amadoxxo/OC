<?php
  namespace openComex;
/**
 * Graba Banco.
 * Este programa permite Guardar en la tabla Bancos.
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
  		  $cCadErr .= " Codigo del Banco no puede ser vacio, ";
  		}

	  	/***** Validando Descripcion Tipo de Documento *****/
  		if ($_POST['cBanDes'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= " Descripcion del Banco no puede ser vacio, ";
  		}

  		/***** Validando Codigo no exista *****/
	  	if ($_COOKIE['kModo'] == "NUEVO") {
		  	$cCodigo = trim(strtoupper($_POST['cBanId']));
		  	$qBanCod  = "SELECT banidxxx ";
	      $qBanCod .= "FROM $cAlfa.fpar0124 WHERE banidxxx = \"$cCodigo\" LIMIT 0,1";
	  	  $xBanCod  = f_MySql("SELECT","",$qBanCod,$xConexion01,"");
	      $nBanCod  = mysql_num_rows($xBanCod);
	      if ($nBanCod > 0) {
	  		  $nSwitch = "1";
	  		  $cCadErr .= " Codigo del Banco ya existe, ";
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
        $cInsertTab	 = array(array('NAME'=>'banidxxx','VALUE'=>$cCodigo													    ,'CHECK'=>'SI'),
				                     array('NAME'=>'bandesxx','VALUE'=>trim(strtoupper($_POST['cBanDes']))  ,'CHECK'=>'SI'),
                             array('NAME'=>'bancuexx','VALUE'=>trim(strtoupper($_POST['cBanCue']))  ,'CHECK'=>'NO'),
										    		 array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId'])) ,'CHECK'=>'SI'),
					 									 array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
					 									 array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
					 									 array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
					 									 array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
														 array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cEstado']))  ,'CHECK'=>'SI'));

				if (f_MySql("INSERT","fpar0124",$cInsertTab,$xConexion01,$cAlfa)) {
				} else {
					$nSwitch = "1";
					f_Mensaje(__FILE__,__LINE__,"Error Guardando Datos del Banco, Verifique");
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
					$cInsertTab	 = array(array('NAME'=>'bandesxx','VALUE'=>trim(strtoupper($_POST['cBanDes']))  ,'CHECK'=>'SI'),
                               array('NAME'=>'bancuexx','VALUE'=>trim(strtoupper($_POST['cBanCue']))  ,'CHECK'=>'NO'),
										    		   array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId'])) ,'CHECK'=>'SI'),
					 									   array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
					 									   array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
														   array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cEstado']))  ,'CHECK'=>'SI'),
                               array('NAME'=>'banidxxx','VALUE'=>trim(strtoupper($_POST['cBanId']))   ,'CHECK'=>'WH'));

						if (f_MySql("UPDATE","fpar0124",$cInsertTab,$xConexion01,$cAlfa)) {
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
					 									   array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		,'CHECK'=>'SI'),
														   array('NAME'=>'regestxx','VALUE'=>$cEstado                             ,'CHECK'=>'SI'),
                               array('NAME'=>'banidxxx','VALUE'=>trim(strtoupper($_POST['cBanId']))   ,'CHECK'=>'WH'));

				 if (f_MySql("UPDATE","fpar0124",$zInsertCab,$xConexion01,$cAlfa)) {
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
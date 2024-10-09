<?php
  namespace openComex;
/**
 * Graba Tipo de Documento.
 * Este programa permite Guardar en la tabla Tipo de Documento.
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
  		if ($_POST['cTdiId'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= " Codigo del Tipo de Documento no puede ser vacio, ";
  		}

	  	/***** Validando Descripcion Tipo de Documento *****/
  		if ($_POST['cTdiDes'] == "") {
  		  $nSwitch = "1";
  		  $cCadErr .= " Descripcion del Tipo de Documento no puede ser vacio, ";
  		}

  		/***** Validando Codigo no exista *****/
	  	if ($_COOKIE['kModo'] == "NUEVO") {
		  	$cCodigo = trim(strtoupper($_POST['cTdiId']));
		  	$qTdiCod  = "SELECT tdiidxxx ";
	      $qTdiCod .= "FROM $cAlfa.fpar0109 WHERE tdiidxxx = \"$cCodigo\" LIMIT 0,1";
	  	  $xTdiCod  = f_MySql("SELECT","",$qTdiCod,$xConexion01,"");
	      $nTdiCod  = mysql_num_rows($xTdiCod);
	      if ($nTdiCod > 0) {
	  		  $nSwitch = "1";
	  		  $cCadErr .= " Codigo del Tipo de Documento ya existe, ";
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
        $cInsertTab	 = array(array('NAME'=>'tdiidxxx','VALUE'=>$cCodigo													    ,'CHECK'=>'SI'),
				                     array('NAME'=>'tdidesxx','VALUE'=>trim(strtoupper($_POST['cTdiDes']))  ,'CHECK'=>'SI'),
										    		 array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId'])) ,'CHECK'=>'SI'),
					 									 array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
					 									 array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
					 									 array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
					 									 array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
														 array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cEstado']))  ,'CHECK'=>'SI'));

				if (f_MySql("INSERT","fpar0109",$cInsertTab,$xConexion01,$cAlfa)) {
				} else {
					$nSwitch = "1";
					f_Mensaje(__FILE__,__LINE__,"Error Guardando Datos del Tipo de Documento, Verifique");
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
					$cInsertTab	 = array(array('NAME'=>'tdidesxx','VALUE'=>trim(strtoupper($_POST['cTdiDes']))  ,'CHECK'=>'SI'),
										    		   array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId'])) ,'CHECK'=>'SI'),
					 									   array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
					 									   array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
														   array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cEstado']))  ,'CHECK'=>'SI'),
                               array('NAME'=>'tdiidxxx','VALUE'=>trim(strtoupper($_POST['cTdiId']))   ,'CHECK'=>'WH'));

						if (f_MySql("UPDATE","fpar0109",$cInsertTab,$xConexion01,$cAlfa)) {
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
                               array('NAME'=>'tdiidxxx','VALUE'=>trim(strtoupper($_POST['cTdiId']))   ,'CHECK'=>'WH'));

				 if (f_MySql("UPDATE","fpar0109",$zInsertCab,$xConexion01,$cAlfa)) {
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
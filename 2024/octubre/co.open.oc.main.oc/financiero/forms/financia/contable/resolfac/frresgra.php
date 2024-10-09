<?php
  namespace openComex;
/**
 * Graba Cuentas Corrientes.
 * Este programa permite Guardar en la tabla Cuentas Corrientes.
 * @author
 * @package emisioncero
 */
	include("../../../../libs/php/utility.php");

	$nSwitch = 0; // Switch para Vericar la Validacion de Datos
	$cCadErr = "";

	/**
	 * Vectores para reemplazar caracteres de salto de linea y tabuladores
	 */
	$vBuscar = array(chr(13),chr(10),chr(27),chr(9));
	$vReempl = array(" "," "," "," ");

	//f_Mensaje(__FILE__,__LINE__,"Entre");
	switch ($_COOKIE['kModo']) {
	  case "NUEVO":
	  case "EDITAR":

      /**
       * Primero valido los datos que llegan por metodo POST.
       */
      switch ($_COOKIE['kModo']) {
        case "NUEVO":

         /**
          * Validando el Tipo de Facturacion, no pueden existir dos resoluciones "autorizaciones" activas del mismo tipo para un centro de costo.
          */
          $zSqlFac  = "SELECT * FROM $cAlfa.fpar0138 ";
          $zSqlFac .= "WHERE ";
          $zSqlFac .= "residxxx = \"{$_POST['cResId']}\"  AND ";
          $zSqlFac .= "resprexx = \"{$_POST['cResPre']}\" AND ";
          $zSqlFac .= "resclaxx = \"{$_POST['cComCla']}\" AND ";
          $zSqlFac .= "regestxx = \"ACTIVO\" ";
          $zCrsFac = f_MySql("SELECT","",$zSqlFac,$xConexion01,"");

          $zRFac = mysql_fetch_array($zCrsFac);
          if(mysql_num_rows($zCrsFac) > 0){
            $nSwitch = 1;
            $cCadErr .= "La Resolucion # [{$zRFac['residxxx']}] ya esta Activa, \n";
          }
        break;
        case "EDITAR":
          //Busando que exista la resolucion
          $zSqlFac  = "SELECT * FROM $cAlfa.fpar0138 ";
          $zSqlFac .= "WHERE ";
          $zSqlFac .= "residxxx = \"{$_POST['cResId']}\"     AND ";
          $zSqlFac .= "resprexx = \"{$_POST['cResPreAnt']}\" AND ";
          $zSqlFac .= "resclaxx = \"{$_POST['cComCla']}\"";
          $zCrsFac = f_MySql("SELECT","",$zSqlFac,$xConexion01,"");
          $zRFac = mysql_fetch_array($zCrsFac);
          if(mysql_num_rows($zCrsFac) == 0){
            $nSwitch = 1;
            $cCadErr .= "La Resolucion # [{$zRFac['residxxx']}] No Existe, \n";
          }
        break;
      }

	  	//f_Mensaje(__FILE__,__LINE__,$_POST['cComMemo']);

	    // Validando el Numero de la Resolucion.
      if ($_POST['cResId'] == "") {
	     	$nSwitch = 1;
	     	$cCadErr .= " Debe Digitar el Numero de la Resolucion, \n";
	   	}
	   	if (!is_numeric($_POST['cResId'])||($_POST['cResId']) < '0') {
	      $nSwitch = 1;
	      $cCadErr .= " Resolucion Debe Ser un Campo Numerico Positivo, \n";
	    }

	    // Validando el Tipo de Facturacion.
      if ($_POST['rTipo'] == "") {
	     	$nSwitch = 1;
	     	$cCadErr .= " Debe Selecionar Tipo de Facturacion, \n";
	   	}

  	  // Validando la Fecha Inicial de la Resolucion.
      if ($_POST['dResFde'] == "") {
	     	$nSwitch = 1;
	     	$cCadErr .= " Debe Digitar Rige Desde, \n";
	   	}

		  // Validando la Fecha Final de la Resolucion.
		  if ($_POST['dResFha'] == "") {
		  	$nSwitch = 1;
		   	$cCadErr .= " Debe Digitar Rige Hasta, \n";
		  }
	   	if (($_POST['dResFde']) > ($_POST['dResFha'])){
	   		$nSwitch = 1;
	    	$cCadErr .= " La Fecha de Inicial de Resolucion No Puede Ser Posterior a la Fecha Final, \n";
	   	}

      // Validando la Factura Inicial.
	    if ($_POST['cResDes'] == "") {
	      $nSwitch = 1;
	      $cCadErr .= " Debe Digitar la Factura Inicial, \n";
	   	}

	   	if (!is_numeric($_POST['cResDes'])||($_POST['cResDes']) < 0){
	   	  $nSwitch = 1;
	      $cCadErr .= " Factura Inicial Debe Ser un Campo Numerico Positivo, \n";
	   	}

	   	// Validando la Factura Final.
	    if ($_POST['cResHas'] == "") {
	      $nSwitch = 1;
	      $cCadErr .= " La Factura Final no Puede Estar Vacia, \n";
	    }

	    if ($_POST['cResDes'] >= $_POST['cResHas']){
	   	  $nSwitch = 1;
	      $cCadErr .= " Factura Final Menor que Factura Inicial, \n";
	    }

	    if (!is_numeric($_POST['cResHas'])||($_POST['cResHas']) < 0 || !is_numeric($_POST['cResHas'])){
	      $nSwitch = 1;
	      $cCadErr .= " Factura Final Debe Ser un Campo Numerico Positivo, \n";
	   	}

      // Validando Dias de Aviso.
	    if ($_POST['cDiasAv'] == "") {
	      $nSwitch = 1;
	      $cCadErr .= " Sin Dias de Aviso, \n";
	    }
	    if (!is_numeric($_POST['cDiasAv'])||($_POST['cDiasAv']) < '0') {
	      $nSwitch = 1;
	      $cCadErr .= " Dias de Aviso Debe Ser un Campo Numerico Positivo, \n";
	    }

      // Validando Consecutivos de Aviso.
	    if ($_POST['cCscAvi'] == "") {
	      $nSwitch = 1;
	      $cCadErr .= " Sin Consecutivos de Aviso, \n";
	    }
	    if (!is_numeric($_POST['cCscAvi'])||($_POST['cCscAvi']) < '0' ) {
	      $nSwitch = 1;
	      $cCadErr .= " Consecutivos de Aviso Debe Ser un Campo Numerico Positivo, \n";
	    }

			// Validando que la variable de "Vigencia en Meses" esté definida, solo aplica para SIACO
			if (!isset($_POST['cResVigMe'])) {
	      $_POST['cResVigMe'] = "";
	    }

		  // Validando el Estado de la Resolucion.
	    if ($_POST['cEstado'] == "") {
	      $nSwitch = 1;
	      $cCadErr .= " Estado No Puede Estar Vacio, \n";
	    }
	    if ($_POST['cEstado'] != "ACTIVO") {
	      $nSwitch = 1;
	      $cCadErr .= " El Estado Debe Estar ACTIVO, \n";
	    }

	    // Validando el Usuario.
	    if ($_COOKIE['kUsrId'] == "") {
	      $nSwitch = 1;
	      $cCadErr .= " Problemas con el Usuario, \n";
	    }

	    //Validando que aplique al menos un comprobante
			if($_POST['cComMemo'] == ""){
				$cCadErr .= "Debe Aplicar Por lo Menos a Un Comprobante, Verifique \n";
			  $nSwitch = 1;
			}else{
				if(strlen($_POST['cComMemo']) <= 1){
					$cCadErr .= "Debe Aplicar Por lo Menos a Un Comprobante, Verifique \n";
				  $nSwitch = 1;
				}
			}

			//Validando que los comprobantes escogidos para esta resolucion no se encuentren seleccionados en otra que este activa.
	    //$cComId = f_Explode_Array($_POST['cComMemo'],"|","~");
	    $cComId = explode("|",$_POST['cComMemo']);
	    for($i=0;$i<count($cComId);$i++){
		    if($cComId[$i] != ""){
		    	//f_Mensaje(__FILE__,__LINE__,$cComId[$i]);
		    	$qResDat  = "SELECT * ";
		    	$qResDat .= "FROM $cAlfa.fpar0138 ";
		    	$qResDat .= "WHERE ";
		    	$qResDat .= "$cAlfa.fpar0138.rescomxx LIKE \"%$cComId[$i]%\" AND ";
		    	$qResDat .= "$cAlfa.fpar0138.regestxx = \"ACTIVO\" ";
		    	//f_Mensaje(__FILE__,__LINE__,$qResDat);
			    $xResDat  = f_MySql("SELECT","",$qResDat,$xConexion01,"");
					$nFilRes  = mysql_num_rows($xResDat);
					if ($nFilRes > 0) {
						$vResDat  = mysql_fetch_array($xResDat);
						if(($vResDat['residxxx'] != $_POST['cResId']) && ($vResDat['resprexx'] != $_POST['cResPre'])){
							$nSwitch = 1;
							$cCadErr .= "El Comprobante [$cComId[$i]] ya se Encuentra Asignado en la Resolucion [{$vResDat['residxxx']}],  \n";
						}
					}
		    }
	    }

  	break;
  	case "ANULAR":
  	  // Cuando Se Requiere Activar Una Resolusion Verifico
  	  // que No haya otra con algun Comprobante en Comun
      if($_POST['cRegEst'] == "INACTIVO") {

        $qResCom  = "SELECT rescomxx FROM $cAlfa.fpar0138 ";
        $qResCom .= "WHERE ";
        $qResCom .= "residxxx = \"{$_POST['cResId']}\"  AND ";
        $qResCom .= "resprexx = \"{$_POST['cResPre']}\" AND ";
        $qResCom .= "resclaxx = \"{$_POST['cComCla']}\" AND ";
        $qResCom .= "regestxx = \"INACTIVO\" ";
        $xResCom = f_MySql("SELECT","",$qResCom,$xConexion01,"");
        $xRRE = mysql_fetch_array($xResCom);

        $cCadSql = "";
        $mResCom = explode("|",$xRRE['rescomxx']);
        for ($i=0; $i<count($mResCom); $i++) {
          if ($mResCom[$i] != "") {
            $cCadSql .= "rescomxx LIKE \"%{$mResCom[$i]}%\" OR ";
          }
        }
        $cCadSql = substr($cCadSql,0,strlen($cCadSql)-3);

        $cSqlRes  = "SELECT * ";
  		  $cSqlRes .= "FROM $cAlfa.fpar0138 ";
  		  $cSqlRes .= "WHERE ";
  		  $cSqlRes .= "resclaxx = \"{$_POST['cComCla']}\" AND ";
  		  $cSqlRes .= "(".$cCadSql.") AND ";
  		  $cSqlRes .= "regestxx = \"ACTIVO\" ";
  		  $zCrsRes = f_MySql("SELECT","",$cSqlRes,$xConexion01,"");

  		  //f_Mensaje(__FILE__,__LINE__,"$cSqlRes - ".mysql_num_rows($zCrsRes));

  		  if (mysql_num_rows($zCrsRes) > 0) {
          $cCadErr .= "La Resolucion No Se Puede ACTIVAR, Porque Los Comprobantes de Este Se Encuentran Activos en Otra Resolusion \n";
			    $nSwitch = 1;
  		  }
      }
    break;
	}
	/***** Fin de la Validacion *****/


  //f_Mensaje(__FILE__,__LINE__,"$cCadErr");

	/***** Ahora Empiezo a Grabar *****/
	/***** Pregunto si el SWITCH Viene en 0 para Poder Seguir *****/

	if ($nSwitch == 0) {
		switch ($_COOKIE['kModo']) {
			case "NUEVO":
  	    /**
  	     * Insert en la Tabla.
  	     */
				$cInsertTab  = array(array('NAME'=>'restipxx','VALUE'=>str_replace($vBuscar,$vReempl,trim(strtoupper($_POST['rTipo' ])))		,'CHECK'=>'SI'),
      	    								 array('NAME'=>'residxxx','VALUE'=>str_replace($vBuscar,$vReempl,trim(strtoupper($_POST['cResId' ])))		,'CHECK'=>'SI'),
      	    								 array('NAME'=>'resprexx','VALUE'=>str_replace($vBuscar,$vReempl,trim(strtoupper($_POST['cResPre' ])))	,'CHECK'=>'NO'),
      	    								 array('NAME'=>'resclaxx','VALUE'=>str_replace($vBuscar,$vReempl,trim(strtoupper($_POST['cComCla'])))		,'CHECK'=>'SI'),
      	    								 array('NAME'=>'rescomxx','VALUE'=>str_replace($vBuscar,$vReempl,$_POST['cComMemo'])                 		,'CHECK'=>'SI'),
    										  	 array('NAME'=>'resfdexx','VALUE'=>str_replace($vBuscar,$vReempl,trim(strtoupper($_POST['dResFde'])))		,'CHECK'=>'SI'),
    										  	 array('NAME'=>'resfhaxx','VALUE'=>str_replace($vBuscar,$vReempl,trim(strtoupper($_POST['dResFha'])))		,'CHECK'=>'SI'),
    										  	 array('NAME'=>'resdesxx','VALUE'=>str_replace($vBuscar,$vReempl,trim(strtoupper($_POST['cResDes'])))		,'CHECK'=>'SI'),
    										  	 array('NAME'=>'reshasxx','VALUE'=>str_replace($vBuscar,$vReempl,trim(strtoupper($_POST['cResHas'])))		,'CHECK'=>'SI'),
    										  	 array('NAME'=>'resdiasa','VALUE'=>str_replace($vBuscar,$vReempl,trim(strtoupper($_POST['cDiasAv'])))		,'CHECK'=>'SI'),
      	                     array('NAME'=>'rescscax','VALUE'=>str_replace($vBuscar,$vReempl,trim(strtoupper($_POST['cCscAvi'])))		,'CHECK'=>'SI'),
														 array('NAME'=>'resvigme','VALUE'=>str_replace($vBuscar,$vReempl,trim(strtoupper($_POST['cResVigMe'])))	,'CHECK'=>'NO'),
    										  	 array('NAME'=>'regusrxx','VALUE'=>str_replace($vBuscar,$vReempl,trim(strtoupper($_COOKIE['kUsrId']))) 	,'CHECK'=>'SI'),
    										  	 array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')																												,'CHECK'=>'SI'),
					 									 array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')		                    																,'CHECK'=>'SI'),
					 									 array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')																												,'CHECK'=>'SI'),
					 									 array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    																,'CHECK'=>'SI'),
														 array('NAME'=>'regestxx','VALUE'=>str_replace($vBuscar,$vReempl,trim(strtoupper($_POST['cEstado'])))  	,'CHECK'=>'SI'));


				if (f_MySql("INSERT","fpar0138",$cInsertTab,$xConexion01,$cAlfa)) {
				} else {
					$nSwitch = 1;
					f_Mensaje(__FILE__,__LINE__,"Error Guardando Datos de la Cuenta Corriente, Verifique");
				}

			break;
			/*****************************   UPDATE    ***********************************************/

			case "EDITAR":

				/***** Validaciones Particulares *****/
				/* Validado El Estado del Registro */

				if (!f_InList($_POST['cEstado'],"ACTIVO","INACTIVO")) {
					$nSwitch = 1;
					f_Mensaje(__FILE__,__LINE__,"El Estado del Registro No es Correcto, Verifique");
				}
				/***** Fin de Validaciones Particulares *****/

				if ($nSwitch == 0) {
					//f_Mensaje(__FILE__,__LINE__,$_POST['cComMemo']);
          $cInsertTab  = array(array('NAME'=>'resprexx','VALUE'=>str_replace($vBuscar,$vReempl,trim(strtoupper($_POST['cResPre'])))   ,'CHECK'=>'NO'),
                               array('NAME'=>'rescomxx','VALUE'=>str_replace($vBuscar,$vReempl,$_POST['cComMemo'])                    ,'CHECK'=>'SI'),
                               array('NAME'=>'resfdexx','VALUE'=>str_replace($vBuscar,$vReempl,trim(strtoupper($_POST['dResFde'])))   ,'CHECK'=>'SI'),
      	    								   array('NAME'=>'resfhaxx','VALUE'=>str_replace($vBuscar,$vReempl,trim(strtoupper($_POST['dResFha'])))   ,'CHECK'=>'SI'),
      	                       array('NAME'=>'resdesxx','VALUE'=>str_replace($vBuscar,$vReempl,trim(strtoupper($_POST['cResDes'])))   ,'CHECK'=>'SI'),
      	                       array('NAME'=>'reshasxx','VALUE'=>str_replace($vBuscar,$vReempl,trim(strtoupper($_POST['cResHas'])))   ,'CHECK'=>'SI'),
      	                       array('NAME'=>'resdiasa','VALUE'=>str_replace($vBuscar,$vReempl,trim(strtoupper($_POST['cDiasAv'])))   ,'CHECK'=>'SI'),
      	                       array('NAME'=>'rescscax','VALUE'=>str_replace($vBuscar,$vReempl,trim(strtoupper($_POST['cCscAvi'])))   ,'CHECK'=>'SI'),
															 array('NAME'=>'resvigme','VALUE'=>str_replace($vBuscar,$vReempl,trim(strtoupper($_POST['cResVigMe']))) ,'CHECK'=>'NO'),
      	                       array('NAME'=>'regusrxx','VALUE'=>str_replace($vBuscar,$vReempl,trim(strtoupper($_COOKIE['kUsrId'])))  ,'CHECK'=>'SI'),
					 									   array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												 																,'CHECK'=>'SI'),
					 									   array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                     																,'CHECK'=>'SI'),
														   array('NAME'=>'regestxx','VALUE'=>str_replace($vBuscar,$vReempl,trim(strtoupper($_POST['cEstado'])))   ,'CHECK'=>'SI'),
														   array('NAME'=>'residxxx','VALUE'=>trim(strtoupper($_POST['cResId']))    																,'CHECK'=>'WH'),
                               array('NAME'=>'resprexx','VALUE'=>trim(strtoupper($_POST['cResPreAnt']))																,'CHECK'=>'WH'),
                               array('NAME'=>'resclaxx','VALUE'=>trim(strtoupper($_POST['cComCla']))   																,'CHECK'=>'WH'));

						if (f_MySql("UPDATE","fpar0138",$cInsertTab,$xConexion01,$cAlfa)) {
							/***** Grabo Bien *****/

							$nSwitch = 0;
						} else {
							$nSwitch = 1;
							f_Mensaje(__FILE__,__LINE__,"Error al Actualizar el Registro");
						}
				}
      break;
      /*****************************   UPDATE    ***********************************************/
      case "ANULAR":
        //f_Mensaje(__FILE__,__LINE__,"{$_POST['cRegEst']}");
        if($_POST['cRegEst']=="ACTIVO"){
          $cEstado="INACTIVO";
        }
        if($_POST['cRegEst']=="INACTIVO"){
          $cEstado="ACTIVO";
        }
        $zInsertCab = array(array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId'])) ,'CHECK'=>'SI'),
    				 								array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												 ,'CHECK'=>'SI'),
    				 								array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')												 ,'CHECK'=>'SI'),
    												array('NAME'=>'regestxx','VALUE'=>$cEstado                             ,'CHECK'=>'SI'),
      	                    array('NAME'=>'residxxx','VALUE'=>trim(strtoupper($_POST['cResId']))   ,'CHECK'=>'WH'),
      	                    array('NAME'=>'resprexx','VALUE'=>trim(strtoupper($_POST['cResPre']))  ,'CHECK'=>'WH'),
      	                    array('NAME'=>'resclaxx','VALUE'=>trim(strtoupper($_POST['cComCla']))  ,'CHECK'=>'WH'));

				if (f_MySql("UPDATE","fpar0138",$zInsertCab,$xConexion01,$cAlfa)) {
				  /***** Grabo Bien *****/
				  $nSwitch = 0;
				} else {
				  $nSwitch = 1;
				  f_Mensaje(__FILE__,__LINE__,"Error al Actualizar el Registro");
				}
      break;
    }
  } else {
  	f_Mensaje(__FILE__,__LINE__,"$cCadErr Verifique");
  }

 	if ($nSwitch == 0) {
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

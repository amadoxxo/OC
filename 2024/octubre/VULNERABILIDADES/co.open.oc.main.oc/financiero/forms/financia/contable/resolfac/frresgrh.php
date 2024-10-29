<?php
  namespace openComex;
/**
 * Graba Cuentas Corrientes.
 * Este programa permite Guardar en la tabla Cuentas Corrientes.
 * @author
 * @package emisioncero
 */
	include("../../../../libs/php/utility.php");

	$nSwitch = "0"; // Switch para Vericar la Validacion de Datos
	$cCadErr = "";

	// Primero valido los datos que llegan por metodo POST.
	if ($_COOKIE['kModo']=="NUEVOB"){
    $zSqlPer  = "SELECT * ";
  	$zSqlPer .= "FROM $cAlfa.fpar0138 ";
  	$zSqlPer .= "WHERE ";
  	/*$zSqlPer .= "comidxxx = \"{$_POST['cComId']}\" AND ";
  	$zSqlPer .= "comcodxx = \"{$_POST['cComCod' ]}\" AND ";*/
  	$zSqlPer .= "residxxx = \"{$_POST['cResId']}\" AND ";
  	$zSqlPer .= "restipxx = \"{$_POST['rTipo'  ]}\" LIMIT 0,1";
  	$zCrsPer = f_MySql("SELECT","",$zSqlPer,$xConexion01,"");

  	$zRPer    = mysql_fetch_array($zCrsPer);
		if (mysql_num_rows($zCrsPer) > 0) {
		 	$nSwitch = 1;
		  $cCadErr .= " Esta Resolucion Ya Existe, \n";
		}

		// Validando el Tipo de Facturacion, no pueden existir dos resoluciones activas del mismo tipo para un centro de costo.
    $zSqlFac  = "SELECT * ";
    $zSqlFac  = "FROM $cAlfa.fpar0138 ";
    $zSqlFac .= "WHERE ";
    /*$zSqlFac .= "comidxxx = \"{$_POST['cComId']}\" AND ";
    $zSqlFac .= "comcodxx = \"{$_POST['cComCod']}\" AND ";*/
    $zSqlFac .= "residxxx = \"{$_POST['cResId']}\" AND ";
    $zSqlFac .= "resclaxx = \"{$_POST['cComCla']}\" AND ";
    $zSqlFac .= "regestxx = \"ACTIVO\" ";
    $zCrsFac = f_MySql("SELECT","",$zSqlFac,$xConexion01,"");

    $zRFac = mysql_fetch_array($zCrsFac);
    if(mysql_num_rows($zCrsFac) > 0){
    	$nSwitch = 1;
    	//$cMensaje  = "La Resolucion # [{$zRFac['residxxx']}]  ya esta Activa para El Comprobante {$zRFac['comidxxx']} {$zRFac['comcodxx']}, Verifique";
    	$cMensaje  = "La Resolucion # [{$zRFac['residxxx']}]  ya esta Activa, Verifique";
    	f_Mensaje(__FILE__,__LINE__,$cMensaje);
    }
	}
	if ($_COOKIE['kModo']=="EDITAR"){

    // Validando el Tipo de Facturacion, no pueden existir dos resoluciones activas del mismo tipo para un centro de costo.
    $zSqlFac  = "SELECT * ";
    $zSqlFac  = "FROM $cAlfa.fpar0138 ";
    $zSqlFac .= "WHERE ";
    /*$zSqlFac .= "comidxxx = \"{$_POST['cComId']}\" AND ";
    $zSqlFac .= "comcodxx = \"{$_POST['cComCod']}\" AND ";*/
    $zSqlFac .= "residxxx = \"{$_POST['cResId']}\" AND ";
    $zSqlFac .= "resclaxx = \"{$_POST['cComCla']}\" AND ";
    $zSqlFac .= "REGESTXX = \"ACTIVO\" ";
    $zCrsFac = f_MySql("SELECT","",$zSqlFac,$xConexion01,"");

    $zRFac = mysql_fetch_array($zCrsFac);
    if(mysql_num_rows($zCrsFac) > 0){
    	$nSwitch = 1;
    	//$cMensaje  = "La Resolucion # [{$zRFac['residxxx']}]  ya esta Activa para El Comprobante {$zRFac['comidxxx']} {$zRFac['comcodxx']}, Verifique";
    	$cMensaje  = "La Resolucion # [{$zRFac['residxxx']}]  ya esta Activa, Verifique";
    	f_Mensaje(__FILE__,__LINE__,$cMensaje);
    }
	}


	switch ($_COOKIE['kModo']) {
	  case "NUEVOB":
  	case "EDITAR":

  	  // Validando el Numero de la Resolucion H.
      if ($_POST['cResIdH'] == "") {
	     	$nSwitch = 1;
	     	$cCadErr .= " Debe Seleccionar una Resolucion con Autorizacion, \n";
	   	}
	   	if (!is_numeric($_POST['cResIdH'])||($_POST['cResIdH']) < '0') {
	      $nSwitch = 1;
	      $cCadErr .= " Resolucion con Autorizacion Debe Ser un Campo Numerico Positivo, \n";
	    }


      // Validando el Numero de la Resolucion.
      if ($_POST['cResId'] == "") {
	     	$nSwitch = 1;
	     	$cCadErr .= " Debe Digitar el Numero de la Resolucion, \n";
	   	}
	   	if (!is_numeric($_POST['cResId'])||($_POST['cResId']) < '0') {
	      $nSwitch = 1;
	      $cCadErr .= " Resolucion Debe Ser un Campo Numerico Positivo, \n";
	    }

      // Validando el Centro de Costo.
      /*
      if ($_POST['cComId'] == "") {
	     	$nSwitch = 1;
	     	$cCadErr .= " Debe Selecionar Comprobante, \n";
	   	}
	   	if ($_POST['cComCod'] == "") {
	     	$nSwitch = 1;
	     	$cCadErr .= " Debe Selecionar Tipo de Comprobante, \n";
	   	}
	   	*/

	   	// Validando la Fecha Inicial de la Resolucion.
      if ($_POST['dResFde'] == "") {
	     	$nSwitch = 1;
	     	$cCadErr .= " Debe Digitar Rige Desde, \n";
	   	}
	   	if (($_POST['dResFde']) < ($_POST['dFecFinH'])){
	   		$nSwitch = 1;
	    	$cCadErr .= " La Resolucion {$_POST['cResIdH']} Vence El {$_POST['dFecFinH']} (Fecha Inicial), \n";
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
	   	if ($_POST['cFacIniH'] > $_POST['cResDes']){
	   	  $nSwitch = 1;
	      $cCadErr .= " La Factura Inicial debe estar entre {$_POST['cFacIniH']} y {$_POST['cFacFinH']}, \n";
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


	   /* $_POST['cComMemoApl']=substr($_POST['cComMemoApl'],0,strlen($_POST['cComMemoApl'])-1);
			if($_POST['cComMemoApl']==""){
			  $cCadErr .= "Debe Aplicar Por lo Menos a Un Comprobante, Verifique \n";
			  $zSwitch = 1;
			}*/

			if($_POST['cComMemo']==""){
				$cCadErr .= "Debe Aplicar Por lo Menos a Un Comprobante, Verifique \n";
			  $nSwitch = 1;
			}else{
				if(strlen($_POST['cComMemo']) <= 1){
					$cCadErr .= "Debe Aplicar Por lo Menos a Un Comprobante, Verifique \n";
				  $nSwitch = 1;
				}
			}




			/*if($_POST['cComMemo']==""){
			  $cCadErr .= "Debe Aplicar Por lo Menos a Un Comprobante, Verifique \n";
			  $zSwitch = 1;
			}*/
  	break;
  	case "ANULAR":
  	  // Cuando Se Requiere Activar Una Resolusion Verifico
  	  // que No haya otra con algun Comprobante en Comun
      if($_POST['cRegEst']=="INACTIVO"){

        $cCadSql = "";
        $mResCom = explode("|",$_POST['cResCom']);
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

  //f_Mensaje(__FILE__,__LINE__,"$cCadErr Verifique");

	/***** Ahora Empiezo a Grabar *****/
	/***** Pregunto si el SWITCH Viene en 0 para Poder Seguir *****/

	if ($nSwitch == "0") {
		switch ($_COOKIE['kModo']) {
			case "NUEVOB":
  	    /**
  	     * Insert en la Tabla.
  	     */
        /*
        $cInsertTab	 = array(array('NAME'=>'sucidxxx','VALUE'=>$cCodigo													    ,'CHECK'=>'SI'),
				                     array('NAME'=>'ccoidxxx','VALUE'=>$cCentro														  ,'CHECK'=>'SI'),
				                     array('NAME'=>'sucdesxx','VALUE'=>trim(strtoupper($_POST['cSucDes']))	,'CHECK'=>'SI'),
										    		 array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId'])) ,'CHECK'=>'SI'),
					 									 array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
					 									 array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
					 									 array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
					 									 array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
														 array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cEstado']))  ,'CHECK'=>'SI'));
        */

        $cInsertTab = array(/*array('NAME'=>'comidxxx','VALUE'=>trim(strtoupper($_POST['cComId' ])) ,'CHECK'=>'SI'),
  	    							      array('NAME'=>'comcodxx','VALUE'=>trim(strtoupper($_POST['cComCod']))   ,'CHECK'=>'SI'),*/
    	    								  array('NAME'=>'residxxx','VALUE'=>trim(strtoupper($_POST['cResId' ]))   ,'CHECK'=>'SI'),
    	    								  array('NAME'=>'resprexx','VALUE'=>trim(strtoupper($_POST['cResPre' ]))  ,'CHECK'=>'NO'),
    	    								  array('NAME'=>'restipxx','VALUE'=>trim(strtoupper($_POST['cResTipH']))  ,'CHECK'=>'SI'),
    	    								  array('NAME'=>'resclaxx','VALUE'=>trim(strtoupper($_POST['cComCla']))   ,'CHECK'=>'SI'),
    	    								  array('NAME'=>'rescomxx','VALUE'=>$_POST['cComMemo']                    ,'CHECK'=>'SI'),
  										  	  array('NAME'=>'resfdexx','VALUE'=>trim(strtoupper($_POST['dResFde']))   ,'CHECK'=>'SI'),
  										  	  array('NAME'=>'resfhaxx','VALUE'=>trim(strtoupper($_POST['dResFha']))   ,'CHECK'=>'SI'),
  										  	  array('NAME'=>'resdesxx','VALUE'=>trim(strtoupper($_POST['cResDes']))   ,'CHECK'=>'SI'),
  										  	  array('NAME'=>'reshasxx','VALUE'=>trim(strtoupper($_POST['cResHas']))   ,'CHECK'=>'SI'),
  										  	  array('NAME'=>'resdiasa','VALUE'=>trim(strtoupper($_POST['cDiasAv']))   ,'CHECK'=>'SI'),
    	                      array('NAME'=>'rescscax','VALUE'=>trim(strtoupper($_POST['cCscAvi']))   ,'CHECK'=>'SI'),
														array('NAME'=>'resvigme','VALUE'=>trim(strtoupper($_POST['cResVigMe'])) ,'CHECK'=>'NO'),
    	                      /*array('NAME'=>'comidhxx','VALUE'=>trim(strtoupper($_POST['cComId']))  ,'CHECK'=>'SI'),
    	                      array('NAME'=>'comcodhx','VALUE'=>trim(strtoupper($_POST['cComCod']))   ,'CHECK'=>'SI'),*/
    	                      array('NAME'=>'residhxx','VALUE'=>trim(strtoupper($_POST['cResIdH']))   ,'CHECK'=>'SI'),
    	                      array('NAME'=>'resclahx','VALUE'=>trim(strtoupper($_POST['cResClaH']))  ,'CHECK'=>'SI'),
  										  	  array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId']))  ,'CHECK'=>'SI'),
    										  	array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')												  ,'CHECK'=>'SI'),
					 									array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')		                      ,'CHECK'=>'SI'),
					 									array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												  ,'CHECK'=>'SI'),
					 									array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                      ,'CHECK'=>'SI'),
														array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cEstado']))   ,'CHECK'=>'SI'));

				if (f_MySql("INSERT","fpar0138",$cInsertTab,$xConexion01,$cAlfa)) {
				} else {
					$nSwitch = "1";
					f_Mensaje(__FILE__,__LINE__,"Error Guardando Datos, Verifique");
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
				  $cInsertTab  = array(array('NAME'=>'rescomxx','VALUE'=>$_POST['cComMemo']                   ,'CHECK'=>'SI'),
				                       array('NAME'=>'resfdexx','VALUE'=>trim(strtoupper($_POST['dResFde']))  ,'CHECK'=>'SI'),
  	    								       array('NAME'=>'resfhaxx','VALUE'=>trim(strtoupper($_POST['dResFha']))  ,'CHECK'=>'SI'),
      	                       array('NAME'=>'resdesxx','VALUE'=>trim(strtoupper($_POST['cResDes']))  ,'CHECK'=>'SI'),
      	                       array('NAME'=>'reshasxx','VALUE'=>trim(strtoupper($_POST['cResHas']))  ,'CHECK'=>'SI'),
      	                       array('NAME'=>'resdiasa','VALUE'=>trim(strtoupper($_POST['cDiasAv']))  ,'CHECK'=>'SI'),
      	                       array('NAME'=>'rescscax','VALUE'=>trim(strtoupper($_POST['cCscAvi']))  ,'CHECK'=>'SI'),
															 array('NAME'=>'resvigme','VALUE'=>trim(strtoupper($_POST['cResVigMe'])),'CHECK'=>'NO'),
      	                       array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId'])) ,'CHECK'=>'SI'),
					 									   array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
					 									   array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
														   array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cEstado']))  ,'CHECK'=>'SI'),
      	                       /*array('NAME'=>'comidxxx','VALUE'=>trim(strtoupper($_POST['cComId']))   ,'CHECK'=>'WH'),
      	                       array('NAME'=>'comcodxx','VALUE'=>trim(strtoupper($_POST['cComCod']))  ,'CHECK'=>'WH'),*/
      	                       array('NAME'=>'residxxx','VALUE'=>trim(strtoupper($_POST['cResId']))   ,'CHECK'=>'WH'),
      	                       array('NAME'=>'resprexx','VALUE'=>trim(strtoupper($_POST['cResPre']))  ,'CHECK'=>'WH'),
      	                       array('NAME'=>'resclaxx','VALUE'=>trim(strtoupper($_POST['cComCla']))  ,'CHECK'=>'WH'));

					if (f_MySql("UPDATE","fpar0138",$cInsertTab,$xConexion01,$cAlfa)) {
						// Grabo Bien
						$nSwitch = "0";
					} else {
						$nSwitch = "1";
						f_Mensaje(__FILE__,__LINE__,"Error al Actualizar el Registro");
					}
				}
      break;
      /*****************************   UPDATE    ***********************************************/
/*
      case "ANULAR":
        if($_POST['cCliEst']=="ACTIVO"){
          $cEstado="INACTIVO";
        }
        if($_POST['cCliEst']=="INACTIVO"){
          $cEstado="ACTIVO";
        }

        $zInsertCab  = array(array('NAME'=>'REGESTXX','VALUE'=>trim(strtoupper($_POST['cEstado'])),  'CHECK'=>'SI'),
      	    								 array('NAME'=>'comidxxx','VALUE'=>trim(strtoupper($_POST['cComId' ])),  'CHECK'=>'WH'),
      	    								 array('NAME'=>'comcodxx','VALUE'=>trim(strtoupper($_POST['cComCod'])),  'CHECK'=>'WH'),
      	                     array('NAME'=>'residxxx','VALUE'=>trim(strtoupper($_POST['cResId' ])),  'CHECK'=>'WH'),
      	                     array('NAME'=>'resclaxx','VALUE'=>trim(strtoupper($_POST['cResClaH' ])),'CHECK'=>'WH'));

        if (f_MySql("UPDATE","fpar0138",$zInsertCab,$xConexion01,$cAlfa)) {
				  // Grabo Bien
				  $nSwitch = "0";
				} else {
				  $nSwitch = "1";
					f_Mensaje(__FILE__,__LINE__,"Error al Actualizar el Registro");
				}
      break;
*/
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

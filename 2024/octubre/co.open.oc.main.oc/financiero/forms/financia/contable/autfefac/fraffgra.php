<?php
  namespace openComex;

	include("../../../../../libs/php/utility.php");
	include("../../../../../libs/php/utiindi.php");

	$nSwitch = 0; // Switch para Vericar la Validacion de Datos
	$nError  = 0; // Switch para Vericar la Validacion de Datos
	$cMsj    = "\n";
	
	switch ($_COOKIE['kModo']) {
		
	  case "NUEVO":
	  	
			/***** Validando ID *****/
  		if ($_POST['cPerAno'] == "") {
  		  $nSwitch = 1;
  		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "El Ano no puede ser vacio.\n";
  		}
  		
	  	/***** Validando ID *****/
  		if ($_POST['cComId'] == "") {
  		  $nSwitch = 1;
  		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "El Id del Comprobante no puede ser vacio.\n";
  		}

  		/***** Validando Codigo *****/
  		if ($_POST['cComCod'] == "") {
  		  $nSwitch = 1;
  		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "El Codigo del Comprobante no puede ser Vacio,\n";
  		}

  		/***** Validando consecutivo *****/
  		if ($_POST['cComCsc'] == "") {
  		  $nSwitch = 1;
  		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "El Consecutivo del Comprobante no puede ser vacio.\n";
  		}
  		
			/***** Validando consecutivo2 *****/
  		if ($_POST['cComCsc2'] == "") {
  		  $nSwitch = 1;
  		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "El Consecutivo Dos del Comprobante no puede ser vacio.\n";
  		}
  		
			/***** Validando fecha *****/
  		if ($_POST['dComFec'] == "") {
  		  $nSwitch = 1;
  		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "La Fecha del Comprobante no puede ser vacia.\n";
  		}
  		
			/***** Validando Nueva *****/
  		if ($_POST['dFecNue'] == "") {
  		  $nSwitch = 1;
  		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "La Nueva Fecha del Comprobante no puede ser vacia.\n";
  		}
  		
			/***** Validando Codigo *****/
  		if ($_POST['cCliId'] == "") {
  		  $nSwitch = 1;
  		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "El NIT de Cliente no puede ser vacio.\n";
  		}
  			/***** Validando Codigo *****/
  		if ($_POST['cCliDV'] == "") {
  		  $nSwitch = 1;
  		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "El Digito de Verificacion no puede ser vacio.\n";
  		}
			/***** Validando Codigo *****/
  		if ($_POST['cCliNom'] == "") {
  		  $nSwitch = 1;
  		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "El Nombre del Cliente no puede ser vacio.\n";
  		}
  		
  		#Validando que exista la tabla del a�o seleccionado
  		$cPerAno = $_POST['cPerAno'];
  		$qVerTab = "SELECT COUNT(comidxxx) FROM $cAlfa.fcoc$cPerAno LIMIT 0,1";
	    $xVerTab  = f_MySql("SELECT","",$qVerTab,$xConexion01,"");
	    //f_Mensaje(__FILE__,__LINE__,$qVerTab."~".mysql_num_rows($xVerTab));
	    if (!$xVerTab) {
	      $nSwitch = 1;
  		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "No Existe la Tabla de Movimiento Contable para el Ano {$_POST['cPerAno']}.\n";
	    }	
  		
  		if ($nSwitch == 0) {
  			#Busco el comprobante y realizo las validaciones correspondientes al proceso  
  		
  			#Que la factura exista en la tabla (a�o) correspondiente.
  			$qFacDat  = "SELECT ";
				$qFacDat .= "$cAlfa.fcoc$cPerAno.comidxxx, ";
				$qFacDat .= "$cAlfa.fcoc$cPerAno.comcodxx, ";
				$qFacDat .= "$cAlfa.fcoc$cPerAno.comcscxx, ";
				$qFacDat .= "$cAlfa.fcoc$cPerAno.comcsc2x, ";
				$qFacDat .= "$cAlfa.fcoc$cPerAno.teridxxx, ";
				$qFacDat .= "$cAlfa.fcoc$cPerAno.comfecxx, ";
				$qFacDat .= "$cAlfa.fcoc$cPerAno.comfecve, ";
				$qFacDat .= "$cAlfa.fcoc$cPerAno.comobs2x, ";
				$qFacDat .= "$cAlfa.fcoc$cPerAno.comfpxxx, ";
				$qFacDat .= "$cAlfa.fcoc$cPerAno.commemod, ";
				$qFacDat .= "$cAlfa.fcoc$cPerAno.regestxx  ";
				$qFacDat .= "FROM $cAlfa.fcoc$cPerAno ";
				$qFacDat .= "WHERE ";
				$qFacDat .= "$cAlfa.fcoc$cPerAno.comidxxx = \"{$_POST['cComId']}\" AND ";											
				$qFacDat .= "$cAlfa.fcoc$cPerAno.comcodxx = \"{$_POST['cComCod']}\" AND ";											
				$qFacDat .= "$cAlfa.fcoc$cPerAno.comcscxx = \"{$_POST['cComCsc']}\" AND ";											
				$qFacDat .= "$cAlfa.fcoc$cPerAno.comcsc2x = \"{$_POST['cComCsc2']}\" AND ";											
				$qFacDat .= "$cAlfa.fcoc$cPerAno.regestxx = \"ACTIVO\" ";
				$qFacDat .= "LIMIT 0,1 ";
				$xFacDat = f_MySql("SELECT","",$qFacDat,$xConexion01,"");		
				if (mysql_num_rows($xFacDat) > 0){	
					$xRFD = mysql_fetch_array($xFacDat);
											
		  		#Que la tabla (a�o) de la nueva fecha exista en el sistema.
					$cPerAnoNew = substr($_POST['dFecNue'],0,4);
					$qVerTab = "SELECT comidxxx FROM $cAlfa.fcoc$cPerAnoNew LIMIT 0,1";
			    $xVerTab  = f_MySql("SELECT","",$qVerTab,$xConexion01,"");
			    //f_Mensaje(__FILE__,__LINE__,$qVerTab."~".mysql_num_rows($xVerTab));
			    if (!$xVerTab) {
			      $nSwitch = 1;
		  		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						$cMsj .= "No Existe la Tabla de Movimiento Contable para la Nueva Fecha [$cPerAnoNew].\n";
			    }
					
					#Que la factura pertenezca al cliente en cuestion.
					if ($_POST['cCliId'] <> $xRFD['teridxxx']) {
			    	$nSwitch = 1;
		  		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						$cMsj .= "El Cliente Seleccionado No es el Mimso de la Factura.\n";
			    }
			    
			    #Valido que el cliente exista y tenga parametrizado el plazo
			    $qDatExt  = "SELECT * ";
					$qDatExt .= "FROM $cAlfa.SIAI0150 ";
					$qDatExt .= "WHERE ";
					$qDatExt .= "CLIIDXXX = \"{$xRFD['teridxxx']}\" AND ";
					$qDatExt .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
  				$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
			  	//f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));
					if (!$xDatExt || mysql_num_rows($xDatExt) == 0) {
			      $nSwitch = 1;
		  		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						$cMsj .= "El Cliente No Existe o Esta Inactivo.\n";
			    } else {
			    	$xRDE = mysql_fetch_array($xDatExt);
			    	
			    	#Busco las condiciones especiales del cliente
			    	$qConCom = "SELECT * FROM $cAlfa.fpar0151 WHERE cliidxxx = \"{$xRDE['CLIIDXXX']}\" AND regestxx = \"ACTIVO\" LIMIT 0,1";
  					$xConCom = f_MySql("SELECT","",$qConCom,$xConexion01,"");
  					//f_Mensaje(__FILE__,__LINE__,$qConCom." ~ ".mysql_num_rows($xConCom));
  					if (!$xConCom || mysql_num_rows($xConCom) == 0) {
				      $nSwitch = 1;
			  		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMsj .= "El Cliente No Tiene Condiciones Comerciales Parametrizadas.\n";
				    } else {
				    	$xRCC = mysql_fetch_array($xConCom);
				    	if ($xRCC['cccplaxx'] == "" || $xRCC['cccplaxx'] < 0) {
				    		$nSwitch = 1;
				  		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
								$cMsj .= "El Cliente No Tiene Parametrizado el Plazo de Credito para Facturacion.\n";
				    	}
				    }
			    }
			    
					#Que haya obligatoria observacion.
		  		if($_POST['cObsObs'] == "") {
		  			$nSwitch = 1;
		  		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						$cMsj .= "La Observacion no puede ser vacia.\n";
		  		}
				} else {
					$nSwitch = 1;
	  		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMsj .= "El Comprobante No Existe o Esta Inactivo en el Ano {$_POST['cPerAno']}.\n";
				}
  		}
  		
	}

	/***** Ahora Empiezo a Grabar *****/
  		
	if ($nSwitch == 0) {
		switch ($_COOKIE['kModo']) {
			
			/*****************************   UPDATE    ***********************************************/
			case "NUEVO":
			
        //Calculando la nueva fecha de vencimiento
        //calculo timestam de las dos fechas
        $vFecha    = explode("-",$xRFD['comfecxx']);
        $dComFec   = mktime(0,0,0,$vFecha[1],$vFecha[2],$vFecha[0]);

        $vFechaVe  = explode("-",$xRFD['comfecve']);
        $dComFecVe = mktime(0,0,0,$vFechaVe[1],$vFechaVe[2],$vFechaVe[0]);

        //resto a una fecha la otra
        $nDiferencia = $dComFecVe - $dComFec;

        //convierto segundos en dias
        $nDias = round($nDiferencia / (60 * 60 * 24));

				$cPerMesNew = substr($_POST['dFecNue'],5,2);
				$cPerDiaNew = substr($_POST['dFecNue'],8,2);
					
				//Nueva fecha de vencimiento
				$_POST['dComVen'] = date("Y-m-d", mktime(0, 0, 0, $cPerMesNew, $cPerDiaNew+$nDias, $cPerAnoNew));
					
				#Nuevo Periodo
				$cPerNew = $cPerAnoNew.str_pad($cPerMesNew,2,"0",STR_PAD_LEFT);
					
				if($cPerAno == $cPerAnoNew) {
					#Se cambia la fecha en los registro
					$mUpdFcoc = array(array('NAME'=>'comfecxx','VALUE'=>$_POST['dFecNue']           ,'CHECK'=>'SI'),
														array('NAME'=>'comfecve','VALUE'=>$_POST['dComVen']           ,'CHECK'=>'SI'),
														array('NAME'=>'comperxx','VALUE'=>$cPerNew              			,'CHECK'=>'SI'),
														array('NAME'=>'regfmodx','VALUE'=>$_POST['dFecNue']           ,'CHECK'=>'SI'),
														array('NAME'=>'reghmodx','VALUE'=>date("H:i:s")          			,'CHECK'=>'SI'),
    					 							array('NAME'=>'comidxxx','VALUE'=>trim($_POST['cComId'])			,'CHECK'=>'WH'),
    					 							array('NAME'=>'comcodxx','VALUE'=>trim($_POST['cComCod'])     ,'CHECK'=>'WH'),
    					 							array('NAME'=>'comcscxx','VALUE'=>trim($_POST['cComCsc'])     ,'CHECK'=>'WH'),
    					 							array('NAME'=>'comcsc2x','VALUE'=>trim($_POST['cComCsc2'])    ,'CHECK'=>'WH'));
    			if (f_MySql("UPDATE","fcoc$cPerAnoNew",$mUpdFcoc,$xConexion01,$cAlfa)) {
    				#Actualizo Detalle
	    			#Se cambia la fecha en los registro
						$mUpdFcod = array(array('NAME'=>'comfecxx','VALUE'=>$_POST['dFecNue']           ,'CHECK'=>'SI'),
															array('NAME'=>'comfecve','VALUE'=>$_POST['dComVen']           ,'CHECK'=>'SI'),
															array('NAME'=>'comperxx','VALUE'=>$cPerNew              			,'CHECK'=>'SI'),
															array('NAME'=>'regfmodx','VALUE'=>$_POST['dFecNue']           ,'CHECK'=>'SI'),
															array('NAME'=>'reghmodx','VALUE'=>date("H:i:s")          			,'CHECK'=>'SI'),
	    					 							array('NAME'=>'comidxxx','VALUE'=>trim($_POST['cComId'])			,'CHECK'=>'WH'),
	    					 							array('NAME'=>'comcodxx','VALUE'=>trim($_POST['cComCod'])     ,'CHECK'=>'WH'),
	    					 							array('NAME'=>'comcscxx','VALUE'=>trim($_POST['cComCsc'])     ,'CHECK'=>'WH'),
	    					 							array('NAME'=>'comcsc2x','VALUE'=>trim($_POST['cComCsc2'])    ,'CHECK'=>'WH'));
	    			if (f_MySql("UPDATE","fcod$cPerAnoNew",$mUpdFcod,$xConexion01,$cAlfa)) {
	    			
	    			} else {
	    				$nSwitch = 1;
	    				$nError  = 1;
	    				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMsj .= "Error al Actualizar el Registro la Tabla fcod$cPerAno.\n";
	    			}	
    			} else {
    				$nSwitch = 1;
    				$nError  = 1;
    				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						$cMsj .= "Error al Actualizar el Registro la Tabla fcoc$cPerAno.\n";
    			}				
				} else {
					#Se crea el registo en la tabla $cPerAnoNew y se borrar el de la tabla $cPerAno
					#Traigo datos de cabecera
					$qFcoc  = "SELECT * ";
					$qFcoc .= "FROM $cAlfa.fcoc$cPerAno ";
					$qFcoc .= "WHERE ";
					$qFcoc .= "$cAlfa.fcoc$cPerAno.comidxxx = \"{$_POST['cComId']}\" AND ";											
					$qFcoc .= "$cAlfa.fcoc$cPerAno.comcodxx = \"{$_POST['cComCod']}\" AND ";											
					$qFcoc .= "$cAlfa.fcoc$cPerAno.comcscxx = \"{$_POST['cComCsc']}\" AND ";											
					$qFcoc .= "$cAlfa.fcoc$cPerAno.comcsc2x = \"{$_POST['cComCsc2']}\" AND ";											
					$qFcoc .= "$cAlfa.fcoc$cPerAno.regestxx = \"ACTIVO\" ";
					$qFcoc .= "LIMIT 0,1 ";
					$xFcoc = f_MySql("SELECT","",$qFcoc,$xConexion01,"");		
					if (mysql_num_rows($xFcoc) == 0){
						$nSwitch = 1;
    				$nError  = 1;
    				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						$cMsj .= "Error al Buscar el Registro la Tabla fcoc$cPerAno.\n";						
					}
					
					#Traigo datos de detalle
					$qFcod  = "SELECT * ";
					$qFcod .= "FROM $cAlfa.fcod$cPerAno ";
					$qFcod .= "WHERE ";
					$qFcod .= "$cAlfa.fcod$cPerAno.comidxxx = \"{$_POST['cComId']}\" AND ";											
					$qFcod .= "$cAlfa.fcod$cPerAno.comcodxx = \"{$_POST['cComCod']}\" AND ";											
					$qFcod .= "$cAlfa.fcod$cPerAno.comcscxx = \"{$_POST['cComCsc']}\" AND ";											
					$qFcod .= "$cAlfa.fcod$cPerAno.comcsc2x = \"{$_POST['cComCsc2']}\" AND ";											
					$qFcod .= "$cAlfa.fcod$cPerAno.regestxx = \"ACTIVO\" ";
					$xFcod = f_MySql("SELECT","",$qFcod,$xConexion01,"");		
					if (mysql_num_rows($xFcod) == 0){
						$nSwitch = 1;
    				$nError  = 1;
    				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						$cMsj .= "Error al Buscar el Registro la Tabla fcod$cPerAno.\n";						
					}
					
					if ($nSwitch == 0) {
						#Insertando datos de cabecera
						$xRFC = mysql_fetch_assoc($xFcoc);
						$mInsFcoc = array();
						foreach ($xRFC as $cKey => $cValue) {
							switch ($cKey){
								case "comfecxx":
									$mInsFcoc[] = array('NAME'=>$cKey,'VALUE'=>$_POST['dFecNue'] ,'CHECK'=>'NO');
								break;
								case "comfecve":
									$mInsFcoc[] = array('NAME'=>$cKey,'VALUE'=>$_POST['dComVen'] ,'CHECK'=>'NO');
								break;
								case "comperxx":
									$mInsFcoc[] = array('NAME'=>$cKey,'VALUE'=>$cPerNew ,'CHECK'=>'NO');
								break;
								case "regfmodx":
									$mInsFcoc[] = array('NAME'=>$cKey,'VALUE'=>$_POST['dFecNue'] ,'CHECK'=>'NO');
								break;
								case "reghmodx":
									$mInsFcoc[] = array('NAME'=>$cKey,'VALUE'=>date("H:i:s") ,'CHECK'=>'NO');
								break;
								default:
									$mInsFcoc[] = array('NAME'=>$cKey,'VALUE'=>$cValue ,'CHECK'=>'NO');
								break;
							}
						}
						
						if (f_MySql("INSERT","fcoc$cPerAnoNew",$mInsFcoc,$xConexion01,$cAlfa)) {
		    			
		    		} else {
		    			$nSwitch = 1;
		    			$nError  = 1;
		    			$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMsj .= "Error al Insertar el Registro la Tabla fcoc$cPerAnoNew.\n";
		    		}
						
						#Insertando datos de detalle
						while ($xRFD = mysql_fetch_assoc($xFcod)) {
							$mInsFcod = array();
							foreach ($xRFD as $cKey => $cValue) {
								switch ($cKey){
									case "comfecxx":
										$mInsFcod[] = array('NAME'=>$cKey,'VALUE'=>$_POST['dFecNue'] ,'CHECK'=>'NO');
									break;
									case "comfecve":
										$mInsFcod[] = array('NAME'=>$cKey,'VALUE'=>$_POST['dComVen'] ,'CHECK'=>'NO');
									break;
									case "comperxx":
										$mInsFcod[] = array('NAME'=>$cKey,'VALUE'=>$cPerNew ,'CHECK'=>'NO');
									break;
									case "regfmodx":
										$mInsFcod[] = array('NAME'=>$cKey,'VALUE'=>$_POST['dFecNue'] ,'CHECK'=>'NO');
									break;
									case "reghmodx":
										$mInsFcod[] = array('NAME'=>$cKey,'VALUE'=>date("H:i:s") ,'CHECK'=>'NO');
									break;
									default:
										$mInsFcod[] = array('NAME'=>$cKey,'VALUE'=>$cValue ,'CHECK'=>'NO');
									break;
								}
							}
							
							if (f_MySql("INSERT","fcod$cPerAnoNew",$mInsFcod,$xConexion01,$cAlfa)) {
			    			
			    		} else {
			    			$nSwitch = 1;
			    			$nError  = 1;
			    			$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
								$cMsj .= "Error al Insertar el Registro la Tabla fcod$cPerAnoNew.\n";
			    		}
						}
						
					}
					if ($nSwitch == 0) {
						#Borrando datos de cabera registro anterior
						$mDelFcoc = array(array('NAME'=>'comidxxx','VALUE'=>trim($_POST['cComId'])			,'CHECK'=>'WH'),
	    					 							array('NAME'=>'comcodxx','VALUE'=>trim($_POST['cComCod'])     ,'CHECK'=>'WH'),
	    					 							array('NAME'=>'comcscxx','VALUE'=>trim($_POST['cComCsc'])     ,'CHECK'=>'WH'),
	    					 							array('NAME'=>'comcsc2x','VALUE'=>trim($_POST['cComCsc2'])    ,'CHECK'=>'WH'));
	    			if (f_MySql("DELETE","fcoc$cPerAno",$mDelFcoc,$xConexion01,$cAlfa)) {
	    				#Borrando datos de datalle registro anterior
							$mDelFcod = array(array('NAME'=>'comidxxx','VALUE'=>trim($_POST['cComId'])			,'CHECK'=>'WH'),
		    					 							array('NAME'=>'comcodxx','VALUE'=>trim($_POST['cComCod'])     ,'CHECK'=>'WH'),
		    					 							array('NAME'=>'comcscxx','VALUE'=>trim($_POST['cComCsc'])     ,'CHECK'=>'WH'),
		    					 							array('NAME'=>'comcsc2x','VALUE'=>trim($_POST['cComCsc2'])    ,'CHECK'=>'WH'));
		    			if (f_MySql("DELETE","fcod$cPerAno",$mDelFcod,$xConexion01,$cAlfa)) {
		    			
		    			} else {
		    				$nSwitch = 1;
		    				$nError  = 1;
		    				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
								$cMsj .= "Error al Borrar el Registro la Tabla fcod$cPerAno.\n";
		    			}	
	    			} else {
	    				$nSwitch = 1;
	    				$nError  = 1;
	    				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMsj .= "Error al Borrar el Registro la Tabla fcoc$cPerAno.\n";
	    			}
					}
				}
				
				#Si se actualiza cabecera y detalle de la factura correctamente
				#Se cambia la fecha en la tabla fcxc0000 y fcxp0000
				#en la maestra de seriales ffoi0000
				#en las de movimiento de DO de IMPO y EXPO
				if ($nSwitch == 0) {
					#Cambiando en la table de fcxc0000
					$mUpdFcxc = array(array('NAME'=>'comfecve','VALUE'=>$_POST['dComVen']          ,'CHECK'=>'SI'),
													  array('NAME'=>'regfmodx','VALUE'=>$_POST['dFecNue']          ,'CHECK'=>'SI'),
													  array('NAME'=>'reghmodx','VALUE'=>date("H:i:s")          		 ,'CHECK'=>'SI'),
    				 							  array('NAME'=>'comidxxx','VALUE'=>trim($_POST['cComId'])		 ,'CHECK'=>'WH'),
    				 							  array('NAME'=>'comcodxx','VALUE'=>trim($_POST['cComCod'])    ,'CHECK'=>'WH'),
    				 							  array('NAME'=>'comcscxx','VALUE'=>trim($_POST['cComCsc'])    ,'CHECK'=>'WH'));
	    		if (f_MySql("UPDATE","fcxc0000",$mUpdFcxc,$xConexion01,$cAlfa)) {
	    			
	    		} else {
	    			$nSwitch = 1;
	    			$nError  = 1;
	    			$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						$cMsj .= "Error al Actualizar el Registro la Tabla fcxc0000.\n";
	    		}

					#Cambiando en la table de fcxp0000
					$mUpdFcxp = array(array('NAME'=>'comfecve','VALUE'=>$_POST['dComVen']           ,'CHECK'=>'SI'),
													  array('NAME'=>'regfmodx','VALUE'=>$_POST['dFecNue']           ,'CHECK'=>'SI'),
													  array('NAME'=>'reghmodx','VALUE'=>date("H:i:s")          		  ,'CHECK'=>'SI'),
    				 							  array('NAME'=>'comidxxx','VALUE'=>trim($_POST['cComId'])			,'CHECK'=>'WH'),
    				 							  array('NAME'=>'comcodxx','VALUE'=>trim($_POST['cComCod'])     ,'CHECK'=>'WH'),
    				 							  array('NAME'=>'comcscxx','VALUE'=>trim($_POST['cComCsc'])     ,'CHECK'=>'WH'));
	    		if (f_MySql("UPDATE","fcxp0000",$mUpdFcxp,$xConexion01,$cAlfa)) {
	    			
	    		} else {
	    			$nSwitch = 1;
	    			$nError  = 1;
	    			$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						$cMsj .= "Error al Actualizar el Registro la Tabla fcxp0000.\n";
	    		}
	    		
					#Cambiando en la table de ffoi0000
					$mUpdFfoi = array(array('NAME'=>'comfecsx','VALUE'=>$_POST['dFecNue']           ,'CHECK'=>'SI'),
													  array('NAME'=>'comidsxx','VALUE'=>trim($_POST['cComId'])			,'CHECK'=>'WH'),
    				 							  array('NAME'=>'comcodsx','VALUE'=>trim($_POST['cComCod'])     ,'CHECK'=>'WH'),
    				 							  array('NAME'=>'comcscsx','VALUE'=>trim($_POST['cComCsc'])     ,'CHECK'=>'WH'),
	    					 						array('NAME'=>'comcscs2','VALUE'=>trim($_POST['cComCsc2'])    ,'CHECK'=>'WH'));
	    		if (f_MySql("UPDATE","ffoi0000",$mUpdFfoi,$xConexion01,$cAlfa)) {
	    			
	    		} else {
	    			$nSwitch = 1;
	    			$nError  = 1;
	    			$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						$cMsj .= "Error al Actualizar el Registro la Tabla ffoi0000.\n";
	    		}
	    		
	    		#Actualizando DO	    		
	    		#Busco primero los DO afectados con ingresos propios comfpxxx
	    		$mAuxDo = array();
	    		$mAuxDo = explode("|",$xRFD['comfpxxx']);
	    		for ($i=0; $i<count($mAuxDo); $i++) {
	    			if ($mAuxDo[$i] <> "") {
	    				$mAuxDat = array();
	    				$mAuxDat = explode("~",$mAuxDo[$i]);
	    				if ($mAuxDat[4] <> "" && $mAuxDat[15] <> "" && $mAuxDat[2] <> "" && $mAuxDat[3] <> "") {
	    					#Busco el DO y verifico que ya tenga fecha
	    					switch ($mAuxDat[4]) {
	    						case "IMPORTACION":
	    						case "TRANSITO":
	    							$qDo  = "SELECT DOIFENTR,DOIHENTR,DOITIPXX,DOIAPEXX ";
	    							$qDo .= "FROM $cAlfa.SIAI0200 ";
	    							$qDo .= "WHERE ";
	    							$qDo .= "DOIIDXXX  = \"{$mAuxDat[2]}\"  AND ";
	    							$qDo .= "DOISFIDX  = \"{$mAuxDat[3]}\"  AND ";
	    							$qDo .= "ADMIDXXX  = \"{$mAuxDat[15]}\" AND ";
	    							$qDo .= "DOIFENTR <> \"0000-00-00\" LIMIT 0,1 ";
			    					$xDo  = f_MySql("SELECT","",$qDo,$xConexion01,"");
			    					if (mysql_num_rows($xDo) > 0) {
			    					  $xRDo = mysql_fetch_array($xDo);
			    					  if ($xRDo['DOITIPXX'] == "IMPORTACION" && $vSysStr['indicadores_activa_modulo'] == "SI" &&  $xRDo['DOIAPEXX'] >= $vSysStr['indicadores_fecha_instalacion_modulo']) {
                          #Validando si puede guardar la fecha de generacion de la factura
                          if ($_POST['dFecNue'] <> "" && $_POST['dFecNue'] <> "0000-00-00") {
	                          //Traigo los Campos que aplican a esta Etapa "Documentos Completos"
											      $qEtapasTramites  = "SELECT ";
											      $qEtapasTramites .= "$cBeta.sys00027.etacampx ";
											      $qEtapasTramites .= "FROM $cBeta.sys00027 ";
											      $qEtapasTramites .= "WHERE ";
											      $qEtapasTramites .= "$cBeta.sys00027.etaidxxx = \"160000\" AND ";
											      $qEtapasTramites .= "$cBeta.sys00027.regestxx = \"ACTIVO\" ";
											      $xEtapasTramites  = f_MySql("SELECT","",$qEtapasTramites,$xConexion01,"");
											      $vEtapasTramites  = mysql_fetch_array($xEtapasTramites);
											                
											      //Armo Vector con Parametros para invocar metodo fnActualizaSaldosIndicadoresxDOxEtapa clase cIndicadoresGestionImportaciones
											      $vDatos = array();
											      $vDatos['TIPOACTX'] = "AUTORIZACION";// Tipo de Actualizacion (ETAPA o AUTORIZACION)
											      $vDatos['ADMIDXXX'] = $mAuxDat[15]; //Sucursal del Do
											      $vDatos['DOIIDXXX'] = $mAuxDat[2];// Numero del DO
											      $vDatos['DOISFIDX'] = $mAuxDat[3];// Sucursal del DO
											      $vDatos['DATOSIND'] = $vEtapasTramites['etacampx'];// Campos del Indicador, son los campos de fecha y hora en la 200
											      $vDatos['FECHALSX'] = $_POST['dFecNue'];// Fecha del Limite Superior
											      $vDatos['HORALSXX'] = $xRDo['DOIHENTR'];// Hora del Limite Superior
											      $vDatos['FECHALSA'] = $xRDo['DOIFENTR'];// Fecha del Limite Superior Anterior
											      $vDatos['HORALSAX'] = $xRDo['DOIHENTR'];// Hora del Limite Superior Anterior
											      
											      $oSaldosIndicadoresxDoxEtapa = new cIndicadoresGestionImportaciones();
											      $mReturn = $oSaldosIndicadoresxDoxEtapa->fnActualizaSaldosIndicadoresxDOxEtapa($vDatos);
											      if($mReturn[0] == "false"){
											        $cError = "";
											        for($i=1;$i<count($mReturn);$i++) {
											            $cError .= $mReturn[$i]."\n";
											        }
											        f_Mensaje(__FILE__,__LINE__,$cError);
											      }
                          }     
                        }
                        
				    					#Cambiando fecha fatura DO
											$mUpdDo = array(array('NAME'=>'DOIFENTR','VALUE'=>$_POST['dFecNue'] ,'CHECK'=>'SI'),
																			array('NAME'=>'DOIIDXXX','VALUE'=>$mAuxDat[2]			  ,'CHECK'=>'WH'),
						    				 							array('NAME'=>'DOISFIDX','VALUE'=>$mAuxDat[3]       ,'CHECK'=>'WH'),
						    				 							array('NAME'=>'ADMIDXXX','VALUE'=>$mAuxDat[15]      ,'CHECK'=>'WH'));
							    		if (f_MySql("UPDATE","SIAI0200",$mUpdDo,$xConexion01,$cAlfa)) {
							    			
							    		} else {
							    			$nSwitch = 1;
							    			$nError  = 1;
							    			$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
												$cMsj .= "Error al Actualizar el Do {$mAuxDat[15]}-{$mAuxDat[2]}-{$mAuxDat[3]}.\n";
							    		}			    					
			    					}
	    						break;
	    						case "EXPORTACION":
	    							$qDo  = "SELECT dexfefac ";
	    							$qDo .= "FROM $cAlfa.siae0199 ";
	    							$qDo .= "WHERE ";
	    							$qDo .= "dexidxxx  = \"{$mAuxDat[2]}\"  AND ";
	    							$qDo .= "admidxxx  = \"{$mAuxDat[15]}\" AND ";
	    							$qDo .= "dexfefac <> \"0000-00-00\" LIMIT 0,1 ";
			    					$xDo  = f_MySql("SELECT","",$qDo,$xConexion01,"");
			    					if (mysql_num_rows($xDo) > 0) {
			    						#Cambiando fecha fatura DO
											$mUpdDo = array(array('NAME'=>'dexfefac','VALUE'=>$_POST['dFecNue'] ,'CHECK'=>'SI'),
																			array('NAME'=>'dexidxxx','VALUE'=>$mAuxDat[2]			  ,'CHECK'=>'WH'),
						    				 							array('NAME'=>'admidxxx','VALUE'=>$mAuxDat[15]      ,'CHECK'=>'WH'));
							    		if (f_MySql("UPDATE","siae0199",$mUpdDo,$xConexion01,$cAlfa)) {
							    			
							    		} else {
							    			$nSwitch = 1;
							    			$nError  = 1;
							    			$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
												$cMsj .= "Error al Actualizar el Do {$mAuxDat[15]}-{$mAuxDat[2]}-{$mAuxDat[3]}.\n";
							    		}	
			    					}
	    						break; 
	    					}
	    				}
	    			}
	    		}
	    		
	    		#Busco primero los DO afectados con Pagos a Terceros commemod
	    		$mAuxDo = array();
	    		$mAuxDo = explode("|",$xRFD['commemod']);
	    		for ($i=0; $i<count($mAuxDo); $i++) {
	    			if ($mAuxDo[$i] <> "") {
	    				$mAuxDat = array();
	    				$mAuxDat = explode("~",$mAuxDo[$i]);
	    				if ($mAuxDat[14] <> "") {
		    				#Busco primero en tabla de importaciones
		    				$qDo  = "SELECT DOIFENTR,DOIHENTR,DOITIPXX,DOIAPEXX ";
		    				$qDo .= "FROM $cAlfa.SIAI0200 ";
		    				$qDo .= "WHERE ";
		    				$qDo .= "CONCAT(ADMIDXXX,\"-\",DOIIDXXX,\"-\",DOISFIDX) = \"{$mAuxDat[14]}\" LIMIT 0,1 ";
				    		$xDo  = f_MySql("SELECT","",$qDo,$xConexion01,"");
				    		if (mysql_num_rows($xDo) > 0) {
				    			$xRDo = mysql_fetch_array($xDo);
				    			if($xRDo['DOIFENTR'] <> "0000-00-00") {
				    			  $mAuxPcc = explode("-",$mAuxDat[14]);
				    			  
				    			  if ($xRDo['DOITIPXX'] == "IMPORTACION" && $vSysStr['indicadores_activa_modulo'] == "SI" &&  $xRDo['DOIAPEXX'] >= $vSysStr['indicadores_fecha_instalacion_modulo']) {
                      #Validando si puede guardar la fecha de generacion de la factura
                      if ($_POST['dFecNue'] <> "" && $_POST['dFecNue'] <> "0000-00-00") {
                        //Traigo los Campos que aplican a esta Etapa "Documentos Completos"
                        $qEtapasTramites  = "SELECT ";
                        $qEtapasTramites .= "$cBeta.sys00027.etacampx ";
                        $qEtapasTramites .= "FROM $cBeta.sys00027 ";
                        $qEtapasTramites .= "WHERE ";
                        $qEtapasTramites .= "$cBeta.sys00027.etaidxxx = \"160000\" AND ";
                        $qEtapasTramites .= "$cBeta.sys00027.regestxx = \"ACTIVO\" ";
                        $xEtapasTramites  = f_MySql("SELECT","",$qEtapasTramites,$xConexion01,"");
                        $vEtapasTramites  = mysql_fetch_array($xEtapasTramites);
                                      
                        //Armo Vector con Parametros para invocar metodo fnActualizaSaldosIndicadoresxDOxEtapa clase cIndicadoresGestionImportaciones
                        $vDatos = array();
                        $vDatos['TIPOACTX'] = "AUTORIZACION";// Tipo de Actualizacion (ETAPA o AUTORIZACION)
                        $vDatos['ADMIDXXX'] = $mAuxPcc[0]; //Sucursal del Do
                        $vDatos['DOIIDXXX'] = $mAuxPcc[1];// Numero del DO
                        $vDatos['DOISFIDX'] = $mAuxPcc[2];// Sucursal del DO
                        $vDatos['DATOSIND'] = $vEtapasTramites['etacampx'];// Campos del Indicador, son los campos de fecha y hora en la 200
                        $vDatos['FECHALSX'] = $_POST['dFecNue'];// Fecha del Limite Superior
                        $vDatos['HORALSXX'] = $xRDo['DOIHENTR'];// Hora del Limite Superior
                        $vDatos['FECHALSA'] = $xRDo['DOIFENTR'];// Fecha del Limite Superior Anterior
                        $vDatos['HORALSAX'] = $xRDo['DOIHENTR'];// Hora del Limite Superior Anterior
                            
                        $oSaldosIndicadoresxDoxEtapa = new cIndicadoresGestionImportaciones();
                        $mReturn = $oSaldosIndicadoresxDoxEtapa->fnActualizaSaldosIndicadoresxDOxEtapa($vDatos);
                        if($mReturn[0] == "false"){
                          $cError = "";
                          for($i=1;$i<count($mReturn);$i++) {
                            $cError .= $mReturn[$i]."\n";
                          }
                          f_Mensaje(__FILE__,__LINE__,$cError);
                        }
                      }     
                    }
				    				
						    		#Cambiando fecha fatura DO
										$mUpdDo = array(array('NAME'=>'DOIFENTR','VALUE'=>$_POST['dFecNue'] ,'CHECK'=>'SI'),
																		array('NAME'=>'DOIIDXXX','VALUE'=>$mAuxPcc[1]			  ,'CHECK'=>'WH'),
								    	 							array('NAME'=>'DOISFIDX','VALUE'=>$mAuxPcc[2]       ,'CHECK'=>'WH'),
								    	 							array('NAME'=>'ADMIDXXX','VALUE'=>$mAuxPcc[0]       ,'CHECK'=>'WH'));
									  if (f_MySql("UPDATE","SIAI0200",$mUpdDo,$xConexion01,$cAlfa)) {
									  } else {
									  	$nSwitch = 1;
									    $nError  = 1;
									    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
											$cMsj .= "Error al Actualizar el Do {$mAuxDat[14]}.\n";
									  }	
				    			}		    					
				    		} else {
				    			$mAuxPcc = explode("-",$mAuxDat[14]);
				    			#Busco el do en la tabla de Exportaciones
				    			$qDo  = "SELECT dexfefac ";
	    						$qDo .= "FROM $cAlfa.siae0199 ";
	    						$qDo .= "WHERE ";
	    						$qDo .= "dexidxxx  = \"{$mAuxPcc[1]}\"  AND ";
	    						$qDo .= "admidxxx  = \"{$mAuxPcc[0]}\" AND ";
	    						$qDo .= "dexfefac <> \"0000-00-00\" LIMIT 0,1 ";
			    				$xDo  = f_MySql("SELECT","",$qDo,$xConexion01,"");
			    				if (mysql_num_rows($xDo) > 0) {
			    					#Cambiando fecha fatura DO
										$mUpdDo = array(array('NAME'=>'dexfefac','VALUE'=>$_POST['dFecNue'] ,'CHECK'=>'SI'),
																		array('NAME'=>'dexidxxx','VALUE'=>$mAuxPcc[1]			  ,'CHECK'=>'WH'),
						    			 							array('NAME'=>'admidxxx','VALUE'=>$mAuxPcc[0]      ,'CHECK'=>'WH'));
							    	if (f_MySql("UPDATE","siae0199",$mUpdDo,$xConexion01,$cAlfa)) {
							    		
							    	} else {
							    		$nSwitch = 1;
							    		$nError  = 1;
							    		$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
											$cMsj .= "Error al Actualizar el Do {$mAuxDat[14]}.\n";
							    	}	
			    				}
				    		}				    		
	    				}
	    			}
	    		}
	    		
	    		#Guardando observacion
	    		#Buscando el consecutivo
				  $qNumSec  = "SELECT obscscxx FROM $cAlfa.fcob0000 ORDER BY ABS(obscscxx) DESC LIMIT 0,1 ";
	  	    $xNumSec  = f_MySql("SELECT","",$qNumSec,$xConexion01,"");
	  	    //f_Mensaje(__FILE__,__LINE__,$qNumSec."~".mysql_num_rows($xNumSec));
	  	    if(mysql_num_rows($xNumSec) > 0) {
	  	    	$xRNS = mysql_fetch_array($xNumSec);
	  	    	$cNumSec = $xRNS['obscscxx'] + 1;
	  	    } else {
	  	    	$cNumSec = 1;
	  	    }
	  	    $cNumSec = str_pad($cNumSec,5,"0",STR_PAD_LEFT);
	  	    
	    		$mInsObs = array(array('NAME'=>'comidxxx','VALUE'=>$_POST['cComId']       ,'CHECK'=>'SI'),
										    	 array('NAME'=>'comcodxx','VALUE'=>$_POST['cComCod']      ,'CHECK'=>'SI'),
										    	 array('NAME'=>'comcscxx','VALUE'=>$_POST['cComCsc']      ,'CHECK'=>'SI'),
										    	 array('NAME'=>'comcsc2x','VALUE'=>$_POST['cComCsc2']     ,'CHECK'=>'SI'),
										    	 array('NAME'=>'comfecxx','VALUE'=>$_POST['dFecNue']      ,'CHECK'=>'SI'),
										    	 array('NAME'=>'comfecan','VALUE'=>$_POST['dComFec']      ,'CHECK'=>'SI'),
										    	 array('NAME'=>'obscscxx','VALUE'=>$cNumSec 				      ,'CHECK'=>'SI'),
										    	 array('NAME'=>'obsidxxx','VALUE'=>'FACTURA' 					    ,'CHECK'=>'SI'),
										    	 array('NAME'=>'gofidxxx','VALUE'=>'100' 							    ,'CHECK'=>'SI'),
										    	 array('NAME'=>'obsobsxx','VALUE'=>trim($_POST['cObsObs']),'CHECK'=>'SI','CS'=>'NONE'),
										    	 array('NAME'=>'regusrxx','VALUE'=>$_COOKIE['kUsrId']     ,'CHECK'=>'SI'),
													 array('NAME'=>'regfcrex','VALUE'=>date("Y-m-d")		      ,'CHECK'=>'SI'),
													 array('NAME'=>'reghcrex','VALUE'=>date("H:i:s")		      ,'CHECK'=>'SI'),
													 array('NAME'=>'regfmodx','VALUE'=>date("Y-m-d")          ,'CHECK'=>'SI'),
													 array('NAME'=>'reghmodx','VALUE'=>date("H:i:s")		      ,'CHECK'=>'SI'),
													 array('NAME'=>'regestxx','VALUE'=>"ACTIVO"  			        ,'CHECK'=>'SI'));
		    	if (f_MySql("INSERT","fcob0000",$mInsObs,$xConexion01,$cAlfa)) {
		    		
		    	} else {
		    		$nSwitch = 1;
		    		$nError  = 1;
		    		$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						$cMsj .= "Error al Insertar la Observacion [fcob0000].\n";
		    	}	
				}
      break;
     }
  } 
  
  if ($cError == 1){
  	$cMsj .= "Por Favor Comuniquese con Opentecnologia S.A.S.\n";
  }   
  if ($nSwitch == 1) {
  	f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique");
  }  
  if ($nSwitch == 0) {
 	  if($_COOKIE['kModo']=="NUEVO"){
 		  f_Mensaje(__FILE__,__LINE__,"Se Realizo el Cambio de Fecha con Exito");
 	  }
 		?>
		<form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
			<script languaje = "javascript">
  			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
				document.forms['frgrm'].submit()
			</script>
  	<?php
 	}	
	
  
?> 		
  		
  		
  		
  		
  		
  		
  		

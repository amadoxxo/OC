<?php
	//Estableciendo que el tiempo de ejecucion no se limite 
  ini_set("memory_limit","1024M");
  set_time_limit(0);
  
  ini_set('error_reporting', E_ERROR);
  ini_set("display_errors","1");
	
	/**
	* Graba Creacion y/o Actualizacion de saldos desde un txt delimintado por tabulaciones.
	* --- Descripcion: Permite Subir y Creacion y/o Actualizacion de saldos desde un txt delimintado por tabulaciones.
	* @author Fabián Sierra Pineda <fabian.sierra@opentecnologia.com.co>
	* @version 001
	*/
	include("../../../../libs/php/utility.php");
  include("../../../../../config/config.php");
  include("../../../../libs/php/uticonta.php");
	
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
	
  define("_NUMREG_",100);
  
	$cSystemPath= OC_DOCUMENTROOT;
	 
	$nSwitch  = 0;   // Switch para Vericar la Validacion de Datos
	$nError   = 0;   // Errores para las actualziaciones
	$cMsj     = "\n";

	#Cadenas para reemplazar caracteres espciales
	$vBuscar = array(chr(13),chr(10),chr(27),chr(9));
	$vReempl = array(" "," "," "," ");
	
  /*Validando que el usuario haya dado un solo click en el boton guardar.*/
  if ($_POST['nTimesSave'] != 1) {
    $nSwitch = 1;
    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
    $cMsj .= "El Sistema Detecto mas de un Click en el Boton Guardar, Verifique.\n";
  }
  
  $mReturnCon  = fnConectarDB();
  if($mReturnCon[0] == "false") {
    $nSwitch = 1;
    for($nR=1;$nR<count($mReturnCon);$nR++){
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= $mReturnCon[$nR]."\n";
    }
  } else {
    $xEnlaces = $mReturnCon[1];
  }
  
	switch ($_COOKIE['kModo']) {
		case "SUBIR":
      /**
       * Validando extension permitida del archivo
       */
      if($_FILES['cArcPla']['name'] != ""){
        $vExtPer = ["text/plain"];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['cArcPla']['tmp_name']);
        if (!in_array($mime, $vExtPer)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
          $cMsj .= "Archivo No Permitido.\n";
        }
        finfo_close($finfo);
      }
			## Validando que haya seleccionado un archivo
			if ($_FILES['cArcPla']['name'] == "") {
				$nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "Debe Seleccionar un Archivo.\n";
			} else {
				#Copiando el archivo a la carpeta de downloads
				$cNomFile = "/carbcoaut_".$kUser."_".date("YmdHis").".txt";
				switch (PHP_OS) {
					case "Linux" :
						$cFile = "$cSystemPath/opencomex/".$vSysStr['system_download_directory'].$cNomFile;
						break;
					case "WINNT":
						$cFile = "$cSystemPath/opencomex/".$vSysStr['system_download_directory'].$cNomFile;
						break;
				}
				
				if(!copy($_FILES['cArcPla']['tmp_name'],$cFile)){
					$nSwitch = 1;
					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMsj .= "Error al Copiar Archivo.\n";
				}
			}

			#Creando tabla temporal
			if ($nSwitch == 0) {
			  switch($_POST['cTipSaldo']){
          case "CxC":
				    //tabla temporal con los datos cargados
				    // $cTabCar  = "saldos_cxc";
				    $cTabCar  = "memsaldo".mt_rand(1000000000, 9999999999);
				    $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabCar (";
            $qNewTab .= "lineaidx INT(11) NOT NULL AUTO_INCREMENT,";
				    $qNewTab .= "comidxxx VARCHAR(200),"; 																									
				    $qNewTab .= "comcodxx VARCHAR(200),"; 																				
				    $qNewTab .= "comcscxx VARCHAR(200),"; 																	
				    $qNewTab .= "comseqxx VARCHAR(200),"; 																	
				    $qNewTab .= "teridxxx VARCHAR(200),"; 																			
				    $qNewTab .= "pucidxxx VARCHAR(200),"; 													
				    $qNewTab .= "commovxx VARCHAR(200),"; 															
				    $qNewTab .= "comfecxx VARCHAR(200),"; 														
				    $qNewTab .= "comfecve VARCHAR(200),"; 																		
				    $qNewTab .= "ccoidxxx VARCHAR(200),"; 																				
				    $qNewTab .= "sccidxxx VARCHAR(200),"; 														
				    $qNewTab .= "puctipej VARCHAR(200),"; 														
				    $qNewTab .= "comsaldo VARCHAR(200), "; 	
            $qNewTab .= "PRIMARY KEY (lineaidx)) ENGINE=MyISAM "; 										
				    //f_Mensaje(__FILE__, __LINE__, $qNewTab);
				    $xNewTab = mysql_query($qNewTab,$xEnlaces);
				    if(!$xNewTab) {
					    $nSwitch = 1;
					    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					    $cMsj .= "Error al Crear la Tabla Temporal.\n";
				    }
          break;
          case "CxP":
            //tabla temporal con los datos cargados
            // $cTabCar  = "saldos_cxp";
            $cTabCar  = "memsaldo".mt_rand(1000000000, 9999999999);
            $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabCar (";
            $qNewTab .= "lineaidx INT(11) NOT NULL AUTO_INCREMENT,";
            $qNewTab .= "comidxxx VARCHAR(200),";                                                   
            $qNewTab .= "comcodxx VARCHAR(200),";                                         
            $qNewTab .= "comcscxx VARCHAR(200),";                                   
            $qNewTab .= "comseqxx VARCHAR(200),";                                   
            $qNewTab .= "teridxxx VARCHAR(200),";                                       
            $qNewTab .= "pucidxxx VARCHAR(200),";                           
            $qNewTab .= "commovxx VARCHAR(200),";                               
            $qNewTab .= "comfecxx VARCHAR(200),";                             
            $qNewTab .= "comfecve VARCHAR(200),";                                     
            $qNewTab .= "ccoidxxx VARCHAR(200),";                                         
            $qNewTab .= "sccidxxx VARCHAR(200),";                             
            $qNewTab .= "puctipej VARCHAR(200),";                             
            $qNewTab .= "comsaldo VARCHAR(200), "; 
            $qNewTab .= "PRIMARY KEY (lineaidx)) ENGINE=MyISAM ";                       
            //f_Mensaje(__FILE__, __LINE__, $qNewTab);
            $xNewTab = mysql_query($qNewTab,$xEnlaces);
            if(!$xNewTab) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "Error al Crear la Tabla Temporal.\n";
            }
          break;
          default:
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Debe Seleccionar un Tipo de Saldo.\n";
          break;
        }
			}
			#Fin Creando tabla temporal
			
			#Cargando Archivo a tabla temporal
			if ($nSwitch == 0) {
				$xDescTabla = mysql_query("DESCRIBE $cAlfa.$cTabCar",$xConexion01);
				
				/**
				 * Campos a excluir en el LOAD DATA INFILE
				 */
				$vFieldsExcluidos = array("comseqxx","puctipej");
		
		  	while ($xRD = mysql_fetch_array($xDescTabla)) {
		  		if (!in_array($xRD['Field'],$vFieldsExcluidos)) {
		  			$vFields[count($vFields)] = $xRD['Field'];
					}
		  	}
				array_shift($vFields); $cFields = implode(",",$vFields);
				
				$qLoad  = "LOAD DATA LOCAL INFILE '$cFile' INTO TABLE $cAlfa.$cTabCar ";
				$qLoad .= "FIELDS TERMINATED BY '\\t' LINES TERMINATED BY '\\n' ";
		    $qLoad .= "IGNORE 1 LINES ";
		    $qLoad .= "($cFields) ";
		    $xLoad = mysql_query($qLoad,$xConexion01);
				// echo $qLoad;
		    if(!$xLoad) {
		      //die(mysql_error());
		      $nSwitch = 1;
		      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
		      $cMsj .= "Error al Cargar los Datos ".mysql_errno($xConexion01)." - ".mysql_error($xLoad);
		    }
			}
			#Fin Cargando Archivo a tabla temporal
			
			//Calculando cantidad de registros en la tabla
			$qDatos  = "SELECT SQL_CALC_FOUND_ROWS * ";
			$qDatos .= "FROM $cAlfa.$cTabCar LIMIT 0,1";
			$xDatos = mysql_query($qDatos,$xConexion01);
			//f_Mensaje(__FILE__,__LINE__,$qDatos."~".mysql_num_rows($xDatos));
			mysql_free_result($xDatos);
			
			$xNumRows = mysql_query("SELECT FOUND_ROWS();",$xConexion01);
			$xRNR = mysql_fetch_array($xNumRows);
			$nCanReg = $xRNR['FOUND_ROWS()'];
			mysql_free_result($xNumRows);
			//f_Mensaje(__FILE__,__LINE__,"tabla temporal -> ".$nCanReg);
			
			if ($nCanReg == 0) {
				$nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "No Se Encontraron Registros.\n";
			}	
      
      if ($nSwitch == 0) {
			  $qDato = "SELECT GROUP_CONCAT(lineaidx+1) AS lineasxx, comidxxx, comcodxx, comcscxx, pucidxxx, teridxxx, COUNT(*) AS cantidad  ";
        $qDato.= "FROM $cAlfa.$cTabCar ";
        $qDato.= "GROUB BY comidxxx, comcodxx, comcscxx, pucidxxx, teridxxx ";
        $qDato.= "HAVING (cantidad > 1 ) ";
        $xDato = mysql_query($qDato,$xConexion01);
        
        while($xPR = mysql_fetch_array($xProRep)){
          $nSwitch = 1;
          $cMsj .= "Lineas {$xRD['lineasxx']}: ";
          $cMsj .= "El Saldo [{$xRD['comidxxx']}~{$xRD['comcodxx']}~{$xRD['comcscxx']}~{$xRD['teridxxx']}~{$xRD['pucidxxx']}] Se Encuentra Repetido.\n";
        }
      }
      
			if ($nSwitch == 0) {
				$qDatos = "SELECT * FROM $cAlfa.$cTabCar";
				$xDatos = mysql_query($qDatos,$xConexion01);
				//f_Mensaje(__FILE__,__LINE__,$qDatos."~".mysql_num_rows($xDatos));
				$nCanReg = 0;
				while ($xRD = mysql_fetch_assoc($xDatos)) {
				    
				  $nCanReg++;
          if (($nCanReg % _NUMREG_) == 0) {$xConexion01 = fnReiniciarConexion(); }
				  
				  //Eliminando caracteres de tabulacion, intelieado de los campos
	        foreach ($xRD as $ckey => $cValue) {
	          $xRD[$ckey] = trim(strtoupper(str_replace($vBuscar,$vReempl,$xRD[$ckey])));
	        }
          
          //Asignando el valor de la secuencia cruece.
          $xRD['comseqxx'] = "001";
					
					//Validaciones del graba de Saldos
					/***** Validando Codigo *****/
					$nExiste = 0;
          
          /*Validar que el codigo del comprobante no este vacio */
          if($xRD['comidxxx'] == "" || $xRD['comcodxx'] == ""){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Comprobante No Puede Ser Vacio.\n";
          } else {
            /* Validar que el codigo del comprobante exista en la fpar0117 */
            $qCom  = "SELECT comidxxx, comcodxx ";
            $qCom .= "FROM $cAlfa.fpar0117 ";
            $qCom .= "WHERE ";
            $qCom .= "comidxxx = \"{$xRD['comidxxx']}\" AND ";
            $qCom .= "comcodxx = \"{$xRD['comcodxx']}\" AND ";
            $qCom .= "regestxx = \"ACTIVO\" LIMIT 0,1";
            $xCom  = f_MySql("SELECT","",$qCom,$xConexion01,"");
            // f_Mensaje(__FILE__, __LINE__, $qCom."~".mysql_num_rows($xCom));
            if (mysql_num_rows($xCom) == 0) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "El Codigo del Comprobante [{$xRD['comidxxx']}-{$xRD['comcodxx']}] No Existe o esta Inactivo.\n";
            }
          }
          
          /*Validar que el consecutivo del comprobante no este vacio */
          if($xRD['comcscxx'] == ""){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Consecutivo del Comprobante No Puede Ser Vacio.\n";
          }else{
            /*Validar si existe saldo segun tipo*/
            
            $cTabla = ($_POST['cTipSaldo'] == "CxC") ? "fcxc0000" : "fcxp0000";
               
            $qSaldo  = "SELECT comidxxx,comcodxx,comcscxx,comseqxx,teridxxx,pucidxxx ";
            $qSaldo .= "FROM $cAlfa.$cTabla ";
            $qSaldo .= "WHERE ";
            $qSaldo .= "comidxxx = \"{$xRD['comidxxx']}\" AND ";
            $qSaldo .= "comcodxx = \"{$xRD['comcodxx']}\" AND ";
            $qSaldo .= "comcscxx = \"{$xRD['comcscxx']}\" AND ";
            $qSaldo .= "comseqxx = \"{$xRD['comseqxx']}\" AND ";
            $qSaldo .= "teridxxx = \"{$xRD['teridxxx']}\" AND ";
            $qSaldo .= "pucidxxx = \"{$xRD['pucidxxx']}\" LIMIT 0,1 ";
            $xSaldo = mysql_query($qSaldo,$xConexion01);

            if ($_POST['cSalTip'] == 'CRUZARSALDO'){ // CRUZAR SALDO
              // f_Mensaje(__FILE__, __LINE__, $qSaldo."~".mysql_num_rows($xSaldo));
              if (mysql_num_rows($xSaldo) == 0) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La {$_POST['cTipSaldo']}, Documento: {$xRD['comidxxx']}-{$xRD['comcodxx']}-{$xRD['comcscxx']}-{$xRD['comseqxx']}, Tercero: {$xRD['teridxxx']} y Cuenta: {$xRD['pucidxxx']}, No existe\n";
              }
            }else{
              if (mysql_num_rows($xSaldo) > 0) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La {$_POST['cTipSaldo']}, Documento: {$xRD['comidxxx']}-{$xRD['comcodxx']}-{$xRD['comcscxx']}-{$xRD['comseqxx']}, Tercero: {$xRD['teridxxx']} y Cuenta: {$xRD['pucidxxx']}, Ya Existe.\n";
              }
            }

                
            for($nAnio=$vSysStr['financiero_ano_instalacion_modulo']; $nAnio<=date('Y'); $nAnio++){
              $qFcod  = "SELECT comidxxx, comcodxx, comcscxx, comcsc2x, comvlrxx ";
              $qFcod .= "FROM $cAlfa.fcod$nAnio ";
              $qFcod .= "WHERE "; 
              $qFcod .= "comidcxx = \"{$xRD['comidxxx']}\" AND ";
              $qFcod .= "comcodcx = \"{$xRD['comcodxx']}\" AND ";
              $qFcod .= "comcsccx = \"{$xRD['comcscxx']}\" AND ";
              $qFcod .= "comseqcx = \"{$xRD['comseqxx']}\" AND ";
              $qFcod .= "teridxxx = \"{$xRD['teridxxx']}\" AND ";
              $qFcod .= "pucidxxx = \"{$xRD['pucidxxx']}\" LIMIT 0,1";
              $xFCod = mysql_query($qFcod,$xConexion01);
              // f_Mensaje(__FILE__, __LINE__, $qFcod."~".mysql_num_rows($xFCod));

              if ($_POST['cSalTip'] == 'CRUZARSALDO'){// CRUZAR SALDO
                //NOTHING FOR THE MOMENT
              }else{
                if (mysql_num_rows($xFCod) > 0) {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "La {$_POST['cTipSaldo']}, Documento: {$xRD['comidxxx']}-{$xRD['comcodxx']}-{$xRD['comcscxx']}-{$xRD['comseqxx']}, Tercero: {$xRD['teridxxx']} y Cuenta: {$xRD['pucidxxx']}, Ya Existe Como Documento Cruce en el Movimiento Contable.\n";
                }
              }
            }
          }

          /*Validar que el cliente no este vacio */
          if($xRD['teridxxx'] == ""){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Cliente/Proveedor No Puede Ser Vacio.\n";
          }else{
            /* Validar que el cliente exista en la SIAI0150 */
            $qCliNom  = "SELECT * ";
            $qCliNom .= "FROM $cAlfa.SIAI0150 ";
            $qCliNom .= "WHERE ";
            $qCliNom .= "CLIIDXXX = \"{$xRD['teridxxx']}\" AND ";
            $qCliNom .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
            $xCliNom  = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
            // echo $qCliNom."~".mysql_num_rows($xCliNom)."<br>";
            // f_Mensaje(__FILE__, __LINE__, $qCliNom."~".mysql_num_rows($xCliNom));
            if (mysql_num_rows($xCliNom) == 0) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "El Cliente/Proveedor [{$xRD['teridxxx']}] No Existe o esta Inactivo.\n";
            }
          }

          /*Validar que la cuenta PUC no este vacia */
          if($xRD['pucidxxx'] == ""){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El La Cuenta PUC No Puede Ser Vacia.\n";
          }else{
            $qPucId  = "SELECT * ";
            $qPucId .= "FROM $cAlfa.fpar0115 ";
            $qPucId .= "WHERE ";
            $qPucId .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = \"{$xRD['pucidxxx']}\" AND ";
            $qPucId .= "regestxx = \"ACTIVO\" LIMIT 0,1";
            $xPucId  = f_MySql("SELECT","",$qPucId,$xConexion01,"");
            if (mysql_num_rows($xPucId) == 0) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "La Cuenta PUC [{$xRD['pucidxxx']}] No Existe o esta Inactiva.\n";
            } else {
              $vPucId = mysql_fetch_array($xPucId);
              $xRD['puctipej'] = $vPucId['puctipej'];
              
              if ($_POST['cTipSaldo'] == "CxC") {
                if($vPucId['pucdetxx'] != "C") {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "La Cuenta [{$xRD['pucidxxx']}] No Detalla por Cobrar.\n";
                }  
              } elseif ($_POST['cTipSaldo'] == "CxP") {
                if($vPucId['pucdetxx'] != "P") {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "La Cuenta [{$xRD['pucidxxx']}] No Detalla por Pagar.\n";
                }
              }
            }
          }

          /*Validar que el movimiento no este vacia */
          $xRD['commovxx'] = strtoupper($xRD['commovxx']);
          
          if($xRD['commovxx'] == ""){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Movimiento No Puede Ser Vacio.\n";
          }elseif (!f_InList($xRD['commovxx'],"C","D")){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Movimiento [{$xRD['commovxx']}] Debe ser C o D.\n";
          }
          
          $vComFec = explode("-",$xRD['comfecxx']);
          /*Validar que la fecha del comprobante no este vacia */
          if($xRD['comfecxx'] == ""){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "La Fecha de Comprobante No Puede Ser Vacio.\n";
          }elseif (strlen($vComFec[0]) != 4 || strlen($vComFec[1]) != 2 || strlen($vComFec[2]) != 2) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Formato de Fecha Debe Ser AAAA-MM-DD.\n";
          }
          
          $vComFecVe = explode("-",$xRD['comfecve']);
          /*Validar que la fecha de vencimiento del comprobante no este vacia */
          if($xRD['comfecve'] == ""){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "La Fecha de Vencimiento del Comprobante No Puede Ser Vacio.\n";
          }elseif (strlen($vComFec[0]) != 4 || strlen($vComFec[1]) != 2 || strlen($vComFec[2]) != 2) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Formato de Fecha Debe Ser AAAA-MM-DD.\n";
            
          }
          
          if ($xRD['comfecve'] < $xRD['comfecxx']) {
             $nSwitch = 1;
             $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
             $cMsj .= "La Fecha de Vencimiento [{$xRD['comfecve']}] no Puede ser Menor a la Fecha del Comprobante [{$xRD['comfecxx']}].\n";
           }
          
          /*Validar que el centro de costo no este vacia */
          if($xRD['ccoidxxx'] == ""){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Centro de Costo No Puede Ser Vacio.\n";
          }else{
            /*Validar que el centro de costo exista */
            $qValCco  = "SELECT * ";
            $qValCco .= "FROM $cAlfa.fpar0116 ";
            $qValCco .= "WHERE ";
            $qValCco .= "ccoidxxx = \"{$xRD['ccoidxxx']}\" AND ";
            $qValCco .= "regestxx = \"ACTIVO\" LIMIT 0,1";
            $xValCco  = f_MySql("SELECT","",$qValCco,$xConexion01,"");
            if (mysql_num_rows($xValCco) == 0) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "El Centro de Costo [{$xRD['ccoidxxx']}] No Existe o esta Inactivo.\n";
            }
          }

          /*Validar que el subcentro de costo no este vacia */
          if($vSysStr['financiero_obligar_subcentro_de_costo'] == "SI") {
            if($xRD['sccidxxx'] == ""){
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "El Subcentro de Costo No Puede Ser Vacio.\n";
            }
          }
          
          if($xRD['sccidxxx'] != ""){
            /*Validar que el subcentro de costo exista */
            $qValScc  = "SELECT * ";
            $qValScc .= "FROM $cAlfa.fpar0120 ";
            $qValScc .= "WHERE ";
            $qValScc .= "ccoidxxx = \"{$xRD['ccoidxxx']}\" AND ";
            $qValScc .= "sccidxxx = \"{$xRD['sccidxxx']}\" AND ";
            $qValScc .= "regestxx = \"ACTIVO\" LIMIT 0,1";
            $xValScc  = f_MySql("SELECT","",$qValScc,$xConexion01,"");
            if (mysql_num_rows($xValScc) == 0) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "El Subcentro de Costo [{$xRD['sccidxxx']}] No Existe, esta Inactivo o no Pertenece al Centro de Costo [{$xRD['ccoidxxx']}].\n";
            }
          }
          
          /*Validar que el valor no este vacia */
          $xRD['comsaldo'] = abs($xRD['comsaldo']);
          if($xRD['comsaldo'] <= 0){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Valor Debe Ser Mayor a Cero.\n";
          }
          
          /**
           * Actualizo datos de la tabla temporal
           */
          $qUpdate  = "UPDATE $cAlfa.$cTabCar SET ";
          $qUpdate .= "comseqxx = \"{$xRD['comseqxx']}\", ";
          $qUpdate .= "commovxx = \"{$xRD['commovxx']}\", ";
          $qUpdate .= "puctipej = \"{$xRD['puctipej']}\", ";
          $qUpdate .= "comsaldo = \"{$xRD['comsaldo']}\"  ";
          $qUpdate .= "WHERE ";
          $qUpdate .= "lineaidx = \"{$xRD['lineaidx']}\" ";
          $xUpdate = mysql_query($qUpdate,$xConexion01);
          if (!$xUpdate) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Error Al Actualizar Datos Adicionales.\n".str_replace("'", " ", mysql_error());
          }
          
				} ## while ($xRD = mysql_fetch_assoc($xDatos)) { ##
			}
		break;
		default:
			$nSwitch = 1;
			$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			$cMsj .= "Modo de Grabado Viene Vacio.\n";
		break;
	}

  if ($nSwitch == 0) {
    switch ($_COOKIE['kModo']) {
      case "SUBIR":
        if ($_POST['cSalTip'] == 'CRUZARSALDO'){ // CRUZAR SALDO
          $cTabla = ($_POST['cTipSaldo'] == "CxC") ? "fcxc0000": "fcxp0000";
          
          $qDatos = "SELECT * FROM $cAlfa.$cTabCar";
          $xDatos = mysql_query($qDatos,$xConexion01);
          
          $nTotal = 0; $nError = 0; $nCanReg = 0;
          while ($xRD = mysql_fetch_array($xDatos)) {
            $nCanReg++;
            if (($nCanReg % _NUMREG_) == 0) {$xConexion01 = fnReiniciarConexion(); }
            
            //Eliminando caracteres de tabulacion, intelieado de los campos
            foreach ($xRD as $ckey => $cValue) {
              $xRD[$ckey] = trim(strtoupper(str_replace($vBuscar,$vReempl,$xRD[$ckey])));
            }
            
            $nBand = 0;
            
            //Buscando si existe el comprobante del saldo
            $nAno = substr($xRD['comfecxx'],0,4);
            $nMes = substr($xRD['comfecxx'],5,2);
            
            $qConsec = "SELECT (MAX(SUBSTRING(comcscxx,-4))+0) AS comcscxx ";
            $qConsec .= "FROM $cAlfa.fcoc$nAno ";
            $qConsec .= "WHERE ";
            $qConsec .= "comidxxx = \"S\" AND ";
            $qConsec .= "comperxx = \"".$nAno.$nMes."\" ";
            $xConsec  = f_MySql("SELECT","",$qConsec,$xConexion01,"");
            // f_Mensaje(__FILE__, __LINE__, $qConsec."~".mysql_num_rows($xConsec));
            $vConsec  = mysql_fetch_array($xConsec);
            $cCsc     = $nAno.$nMes.str_pad(($vConsec['comcscxx']+1),4,"0",STR_PAD_LEFT);
                
            $nSaldo   = ($xRD['puctipej'] == "" || $xRD['puctipej'] == "L") ? abs($xRD['comsaldo']) : 0;
            $nSaldoNF = ($xRD['puctipej'] == "" || $xRD['puctipej'] == "N") ? abs($xRD['comsaldo']) : 0;
                
            //Creando saldo en cabecera
            $qInsert  = "INSERT INTO $cAlfa.fcoc$nAno (";
            $qInsert .= "comidxxx,";
            $qInsert .= "comcodxx,";
            $qInsert .= "comcscxx,";
            $qInsert .= "comcsc2x,";
            $qInsert .= "comfecxx,";
            $qInsert .= "comfecve,";
            $qInsert .= "comperxx,";
            $qInsert .= "ccoidxxx,";
            $qInsert .= "sccidxxx,";
            $qInsert .= "tertipxx,";
            $qInsert .= "teridxxx,";
            $qInsert .= "tertip2x,";
            $qInsert .= "terid2xx,";
            $qInsert .= "comvlrxx,";
            $qInsert .= "comvlrnf,";
            $qInsert .= "monidxxx,";
            $qInsert .= "comobsxx,";
            $qInsert .= "regusrxx,";
            $qInsert .= "regfcrex,";
            $qInsert .= "reghcrex,";
            $qInsert .= "regfmodx,";
            $qInsert .= "reghmodx,";
            $qInsert .= "regestxx) VALUES (";
            $qInsert .= "\"S\",";
            $qInsert .= "\"999\",";
            $qInsert .= "\"$cCsc\",";
            $qInsert .= "\"$cCsc\",";
            $qInsert .= "\"{$xRD['comfecxx']}\",";
            $qInsert .= "\"{$xRD['comfecve']}\",";
            $qInsert .= "\"$nAno$nMes\",";
            $qInsert .= "\"{$xRD['ccoidxxx']}\",";
            $qInsert .= "\"{$xRD['sccidxxx']}\",";
            $qInsert .= "\"CLICLIXX\",";
            $qInsert .= "\"{$xRD['teridxxx']}\",";
            $qInsert .= "\"CLICLIXX\",";
            $qInsert .= "\"{$xRD['teridxxx']}\",";
            $qInsert .= "\"$nSaldo\",";
            $qInsert .= "\"$nSaldoNF\",";
            $qInsert .= "\"USD\",";
            $qInsert .= "\"CRUCE DE SALDOS\",";
            $qInsert .= "\"$kUser\",";
            $qInsert .= "\"".date("Y-m-d")."\","; 
            $qInsert .= "\"".date("H:i:s")."\","; 
            $qInsert .= "\"".date("Y-m-d")."\","; 
            $qInsert .= "\"".date("H:i:s")."\","; 
            $qInsert .= "\"ACTIVO\")";
            $xInsert = mysql_query($qInsert,$xConexion01);
            if (!$xInsert) {
              $nBand  = 1;
              $nError = 1;
              $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "Error al Insertar Datos en Cabecera del Movimiento Contable [fcoc].\n";
            } else {  
              //Inserto Detalle
              $qInsert  = "INSERT INTO $cAlfa.fcod$nAno (";
              $qInsert .= "comidxxx,";
              $qInsert .= "comcodxx,";
              $qInsert .= "comcscxx,";
              $qInsert .= "comcsc2x,";
              $qInsert .= "comseqxx,";
              $qInsert .= "comfecxx,";
              $qInsert .= "ccoidxxx,";
              $qInsert .= "sccidxxx,";
              $qInsert .= "pucidxxx,";
              $qInsert .= "ctoidxxx,";
              $qInsert .= "commovxx,";
              $qInsert .= "comperxx,";
              $qInsert .= "tertipxx,";
              $qInsert .= "teridxxx,";
              $qInsert .= "tertip2x,";
              $qInsert .= "terid2xx,";
              $qInsert .= "comobsxx,";
              $qInsert .= "puctipej,";
              $qInsert .= "comvlrxx,";
              $qInsert .= "comvlrnf,";
              $qInsert .= "comidcxx,";
              $qInsert .= "comcodcx,";
              $qInsert .= "comcsccx,";
              $qInsert .= "comseqcx,";
              $qInsert .= "comfecve,";
              $qInsert .= "regusrxx,";
              $qInsert .= "regfcrex,";
              $qInsert .= "reghcrex,";
              $qInsert .= "regfmodx,";
              $qInsert .= "reghmodx,";
              $qInsert .= "regestxx) VALUE (";
              $qInsert .= "\"S\",";
              $qInsert .= "\"999\",";
              $qInsert .= "\"$cCsc\",";
              $qInsert .= "\"$cCsc\",";
              $qInsert .= "\"001\",";
              $qInsert .= "\"{$xRD['comfecxx']}\",";
              $qInsert .= "\"{$xRD['ccoidxxx']}\",";
              $qInsert .= "\"{$xRD['sccidxxx']}\",";
              $qInsert .= "\"{$xRD['pucidxxx']}\",";
              $qInsert .= "\"{$xRD['pucidxxx']}\",";
              $qInsert .= "\"{$xRD['commovxx']}\",";
              $qInsert .= "\"$nAno$nMes\",";
              $qInsert .= "\"CLICLIXX\",";
              $qInsert .= "\"{$xRD['teridxxx']}\",";
              $qInsert .= "\"CLICLIXX\",";
              $qInsert .= "\"{$xRD['teridxxx']}\",";
              $qInsert .= "\"CRUCE DE SALDOS\",";
              $qInsert .= "\"{$xRD['puctipej']}\",";                  
              $qInsert .= "\"$nSaldo\",";
              $qInsert .= "\"$nSaldoNF\",";                  
              $qInsert .= "\"{$xRD['comidxxx']}\",";
              $qInsert .= "\"{$xRD['comcodxx']}\",";
              $qInsert .= "\"{$xRD['comcscxx']}\",";
              $qInsert .= "\"001\",";
              $qInsert .= "\"{$xRD['comfecve']}\",";
              $qInsert .= "\"$kUser\",";
              $qInsert .= "\"".date("Y-m-d")."\","; 
              $qInsert .= "\"".date("H:i:s")."\","; 
              $qInsert .= "\"".date("Y-m-d")."\","; 
              $qInsert .= "\"".date("H:i:s")."\","; 
              $qInsert .= "\"ACTIVO\")";
              $xInsert = mysql_query($qInsert,$xConexion01);
              if (!$xInsert) {
                $nBand  = 1; 
                $nError = 1; 
                $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Error al Insertar Datos en Detalle del Movimiento Contable [fcod].\n";
              }                 
            }

            /*** Busco el detalle de la cuenta. ***/ 
            $qSqlPuc  = "SELECT *,CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AS pucidxxx ";
            $qSqlPuc .= "FROM $cAlfa.fpar0115 ";
            $qSqlPuc .= "WHERE ";
            $qSqlPuc .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = \"{$xRD['pucidxxx']}\" AND ";
            $qSqlPuc .= "regestxx = \"ACTIVO\" LIMIT 0,1";
            $xSqlPuc  = f_MySql("SELECT","",$qSqlPuc,$xConexion01,"");
            // f_Mensaje(__FILE__,__LINE__,$qSqlPuc." ~ ".mysql_num_rows($xSqlPuc));
            $vSqlPuc  = mysql_fetch_array($xSqlPuc);

            /*** Actualizar/Crear los Saldos. ***/
            if($nBand == 0) {
              $mSaldos = array();
              $mSaldos['cComIdC']  = $xRD['comidxxx'];
              $mSaldos['cComCodC'] = $xRD['comcodxx'];
              $mSaldos['cComCscC'] = $xRD['comcscxx'];
              $mSaldos['cComSeqC'] = $xRD['comseqxx'];
              $mSaldos['cPucDet']  = $vSqlPuc['pucdetxx'];
              $mSaldos['cPucTipEj']= $xRD['puctipej'];
              $mSaldos['cComMov']  = $xRD['commovxx'];
              $mSaldos['cPucId']   = $xRD['pucidxxx'];
              $mSaldos['cTerId']   = $xRD['teridxxx'];
              $mSaldos['cTerIdB']  = $xRD['teridxxx'];
              $mSaldos['nComVlr']  = $nSaldo;
              $mSaldos['nComVlrNF']= $nSaldoNF;
              $mSaldos['dComFec']  = $xRD['comfecxx'];
              $mSaldos['dComVen']  = $xRD['comfecve'];
              $mSaldos['cCcoId']   = $xRD['ccoidxxx'];
              $mSaldos['cSccId']   = $xRD['sccidxxx'];

              if (!f_Crea_Saldos_Cuentas($mSaldos)) {
                $nBan   = 1;
                $nError = 1;
                $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Error al Actualizar los Modulos de CONTABILIDAD, CxC, CxP y DoS.\n";
              }
              /*** Fin Actualizar los Saldos. ***/
              
              if($nBand == 1){
                //Con errores Borro comprobante en cabecera o detalle
                $qDelete  = "DELETE FROM $cAlfa.fcoc$nAno ";
                $qDelete .= "WHERE ";
                $qDelete .= "comidxxx = \"S\"     AND ";
                $qDelete .= "comcodxx = \"999\"   AND ";
                $qDelete .= "comcscxx = \"$cCsc\" AND ";
                $qDelete .= "comcsc2x = \"$cCsc\" ";
                $xDelete = mysql_query($qDelete,$xConexion01);
                
                $qDelete = str_replace("$cAlfa.fcoc$nAno","$cAlfa.fcod$nAno",$qDelete);
                $xDelete = mysql_query($qDelete,$xConexion01);
                
                /*** Se recalcula el saldo reversando los cambios. ***/
                if (!f_Crea_Saldos_Cuentas($mSaldos)) {
                  $nBan   = 1;
                  $nError = 1;
                  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "Error al Actualizar los Modulos de CONTABILIDAD, CxC, CxP y DoS.\n";
                }
              }
              
              if ($nBand == 0) {
                $nTotal++;
              } 
            } 
          }##while ($xRD = mysql_fetch_array($xDatos)) {##

        }else{ /*** Proceso Normal de subir saldos sin la actualizacion de actualizar los saldos.***/
          $cTabla = ($_POST['cTipSaldo'] == "CxC") ? "fcxc0000": "fcxp0000";
          
          $qDatos = "SELECT * FROM $cAlfa.$cTabCar";
          $xDatos = mysql_query($qDatos,$xConexion01);
          
          $nTotal = 0; $nError = 0; $nCanReg = 0;
          while ($xRD = mysql_fetch_array($xDatos)) {
            
            $nCanReg++;
            if (($nCanReg % _NUMREG_) == 0) {$xConexion01 = fnReiniciarConexion(); }
            
            //Eliminando caracteres de tabulacion, intelieado de los campos
            foreach ($xRD as $ckey => $cValue) {
              $xRD[$ckey] = trim(strtoupper(str_replace($vBuscar,$vReempl,$xRD[$ckey])));
            }
            
            $nBand = 0;
            
            //Buscando si existe el comprobante del saldo
            $nAno = substr($xRD['comfecxx'],0,4);
            $nMes = substr($xRD['comfecxx'],5,2);
            
            $qConsec = "SELECT (MAX(SUBSTRING(comcscxx,-4))+0) AS comcscxx ";
            $qConsec .= "FROM $cAlfa.fcoc$nAno ";
            $qConsec .= "WHERE ";
            $qConsec .= "comidxxx = \"S\" AND ";
            $qConsec .= "comperxx = \"".$nAno.$nMes."\" ";
            $xConsec  = f_MySql("SELECT","",$qConsec,$xConexion01,"");
            // f_Mensaje(__FILE__, __LINE__, $qConsec."~".mysql_num_rows($xConsec));
            $vConsec  = mysql_fetch_array($xConsec);
            $cCsc     = $nAno.$nMes.str_pad(($vConsec['comcscxx']+1),4,"0",STR_PAD_LEFT);
                
            $qFcod  = "SELECT comidxxx, comcodxx, comcscxx, comcsc2x, comvlrxx ";
            $qFcod .= "FROM $cAlfa.fcod$nAno ";
            $qFcod .= "WHERE ";
            $qFcod .= "comidcxx = \"{$xRD['comidxxx']}\" AND ";
            $qFcod .= "comcodcx = \"{$xRD['comcodxx']}\" AND ";
            $qFcod .= "comcsccx = \"{$xRD['comcscxx']}\" AND ";
            $qFcod .= "comseqcx = \"{$xRD['comseqxx']}\" AND ";
            $qFcod .= "teridxxx = \"{$xRD['teridxxx']}\" AND ";
            $qFcod .= "pucidxxx = \"{$xRD['pucidxxx']}\" LIMIT 0,1";
            $xFcod = mysql_query($qFcod,$xConexion01);
            // f_Mensaje(__FILE__, __LINE__, $qSaldo."~".mysql_num_rows($xSaldo));
                
            if (mysql_num_rows($xFcod) == 0) {
              $nSaldo   = ($xRD['puctipej'] == "" || $xRD['puctipej'] == "L") ? abs($xRD['comsaldo']) : 0;
              $nSaldoNF = ($xRD['puctipej'] == "" || $xRD['puctipej'] == "N") ? abs($xRD['comsaldo']) : 0;
                  
              //Creando saldo en cabecera
              $qInsert  = "INSERT INTO $cAlfa.fcoc$nAno (";
              $qInsert .= "comidxxx,";
              $qInsert .= "comcodxx,";
              $qInsert .= "comcscxx,";
              $qInsert .= "comcsc2x,";
              $qInsert .= "comfecxx,";
              $qInsert .= "comfecve,";
              $qInsert .= "comperxx,";
              $qInsert .= "ccoidxxx,";
              $qInsert .= "sccidxxx,";
              $qInsert .= "tertipxx,";
              $qInsert .= "teridxxx,";
              $qInsert .= "tertip2x,";
              $qInsert .= "terid2xx,";
              $qInsert .= "comvlrxx,";
              $qInsert .= "comvlrnf,";
              $qInsert .= "monidxxx,";
              $qInsert .= "comobsxx,";
              $qInsert .= "regusrxx,";
              $qInsert .= "regfcrex,";
              $qInsert .= "reghcrex,";
              $qInsert .= "regfmodx,";
              $qInsert .= "reghmodx,";
              $qInsert .= "regestxx) VALUES (";
              $qInsert .= "\"S\",";
              $qInsert .= "\"999\",";
              $qInsert .= "\"$cCsc\",";
              $qInsert .= "\"$cCsc\",";
              $qInsert .= "\"{$xRD['comfecxx']}\",";
              $qInsert .= "\"{$xRD['comfecve']}\",";
              $qInsert .= "\"$nAno$nMes\",";
              $qInsert .= "\"{$xRD['ccoidxxx']}\",";
              $qInsert .= "\"{$xRD['sccidxxx']}\",";
              $qInsert .= "\"CLICLIXX\",";
              $qInsert .= "\"{$xRD['teridxxx']}\",";
              $qInsert .= "\"CLICLIXX\",";
              $qInsert .= "\"{$xRD['teridxxx']}\",";
              $qInsert .= "\"$nSaldo\",";
              $qInsert .= "\"$nSaldoNF\",";
              $qInsert .= "\"USD\",";
              $qInsert .= "\"CARGUE SALDOS INICIALES\",";
              $qInsert .= "\"$kUser\",";
              $qInsert .= "\"".date("Y-m-d")."\","; 
              $qInsert .= "\"".date("H:i:s")."\","; 
              $qInsert .= "\"".date("Y-m-d")."\","; 
              $qInsert .= "\"".date("H:i:s")."\","; 
              $qInsert .= "\"ACTIVO\")";
              $xInsert = mysql_query($qInsert,$xConexion01);
              if (!$xInsert) {
                $nBand  = 1;
                $nError = 1;
                $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Error al Insertar Datos en Cabecera del Movimiento Contable [fcoc].\n";
              } else {  
                //Inserto Detalle
                $qInsert  = "INSERT INTO $cAlfa.fcod$nAno (";
                $qInsert .= "comidxxx,";
                $qInsert .= "comcodxx,";
                $qInsert .= "comcscxx,";
                $qInsert .= "comcsc2x,";
                $qInsert .= "comseqxx,";
                $qInsert .= "comfecxx,";
                $qInsert .= "ccoidxxx,";
                $qInsert .= "sccidxxx,";
                $qInsert .= "pucidxxx,";
                $qInsert .= "ctoidxxx,";
                $qInsert .= "commovxx,";
                $qInsert .= "comperxx,";
                $qInsert .= "tertipxx,";
                $qInsert .= "teridxxx,";
                $qInsert .= "tertip2x,";
                $qInsert .= "terid2xx,";
                $qInsert .= "comobsxx,";
                $qInsert .= "puctipej,";
                $qInsert .= "comvlrxx,";
                $qInsert .= "comvlrnf,";
                $qInsert .= "comidcxx,";
                $qInsert .= "comcodcx,";
                $qInsert .= "comcsccx,";
                $qInsert .= "comseqcx,";
                $qInsert .= "comfecve,";
                $qInsert .= "regusrxx,";
                $qInsert .= "regfcrex,";
                $qInsert .= "reghcrex,";
                $qInsert .= "regfmodx,";
                $qInsert .= "reghmodx,";
                $qInsert .= "regestxx) VALUE (";
                $qInsert .= "\"S\",";
                $qInsert .= "\"999\",";
                $qInsert .= "\"$cCsc\",";
                $qInsert .= "\"$cCsc\",";
                $qInsert .= "\"001\",";
                $qInsert .= "\"{$xRD['comfecxx']}\",";
                $qInsert .= "\"{$xRD['ccoidxxx']}\",";
                $qInsert .= "\"{$xRD['sccidxxx']}\",";
                $qInsert .= "\"{$xRD['pucidxxx']}\",";
                $qInsert .= "\"{$xRD['pucidxxx']}\",";
                $qInsert .= "\"{$xRD['commovxx']}\",";
                $qInsert .= "\"$nAno$nMes\",";
                $qInsert .= "\"CLICLIXX\",";
                $qInsert .= "\"{$xRD['teridxxx']}\",";
                $qInsert .= "\"CLICLIXX\",";
                $qInsert .= "\"{$xRD['teridxxx']}\",";
                $qInsert .= "\"CARGUE SALDOS INICIALES\",";
                $qInsert .= "\"{$xRD['puctipej']}\",";                  
                $qInsert .= "\"$nSaldo\",";
                $qInsert .= "\"$nSaldoNF\",";                  
                $qInsert .= "\"{$xRD['comidxxx']}\",";
                $qInsert .= "\"{$xRD['comcodxx']}\",";
                $qInsert .= "\"{$xRD['comcscxx']}\",";
                $qInsert .= "\"001\",";
                $qInsert .= "\"{$xRD['comfecve']}\",";
                $qInsert .= "\"$kUser\",";
                $qInsert .= "\"".date("Y-m-d")."\","; 
                $qInsert .= "\"".date("H:i:s")."\","; 
                $qInsert .= "\"".date("Y-m-d")."\","; 
                $qInsert .= "\"".date("H:i:s")."\","; 
                $qInsert .= "\"ACTIVO\")";
                $xInsert = mysql_query($qInsert,$xConexion01);
                if (!$xInsert) {
                  $nBand  = 1; 
                  $nError = 1; 
                  $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "Error al Insertar Datos en Detalle del Movimiento Contable [fcod].\n";
                }                 
              }             
            } else {
              $nBand  = 1;
              $nError = 1;
              $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "La {$_POST['cTipSaldo']}, Documento: {$xRD['comidxxx']}-{$xRD['comcodxx']}-{$xRD['comcscxx']}-{$xRD['comseqxx']}, Tercero: {$xRD['teridxxx']} y Cuenta: {$xRD['pucidxxx']}, Ya Existe Como Documento Cruce en el Movimiento Contable.\n";
            }
            
            if($nBand == 0) {
              //Verifico que no exista la CxC o CxP
              $qSaldo  = "SELECT comidxxx,comcodxx,comcscxx,comseqxx,teridxxx,pucidxxx ";
              $qSaldo .= "FROM $cAlfa.$cTabla ";
              $qSaldo .= "WHERE ";
              $qSaldo .= "comidxxx = \"{$xRD['comidxxx']}\" AND ";
              $qSaldo .= "comcodxx = \"{$xRD['comcodxx']}\" AND ";
              $qSaldo .= "comcscxx = \"{$xRD['comcscxx']}\" AND ";
              $qSaldo .= "comseqxx = \"{$xRD['comseqxx']}\" AND ";
              $qSaldo .= "teridxxx = \"{$xRD['teridxxx']}\" AND ";
              $qSaldo .= "pucidxxx = \"{$xRD['pucidxxx']}\" LIMIT 0,1 ";
              $xSaldo = mysql_query($qSaldo,$xConexion01);
              if (mysql_num_rows($xSaldo) > 0) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La {$_POST['cTipSaldo']}, Documento: {$xRD['comidxxx']}-{$xRD['comcodxx']}-{$xRD['comcscxx']}-{$xRD['comseqxx']}, Tercero: {$xRD['teridxxx']} y Cuenta: {$xRD['pucidxxx']}, Ya Existe.\n";
              } else {
                //Insertando el saldo
                
                $xRD['comsaldo'] = ($xRD['commovxx'] == "C") ? abs($xRD['comsaldo'])*-1 : abs($xRD['comsaldo']);
                $nSaldo   = ($xRD['puctipej'] == "" || $xRD['puctipej'] == "L") ? $xRD['comsaldo'] : 0;
                $nSaldoNF = ($xRD['puctipej'] == "" || $xRD['puctipej'] == "N") ? $xRD['comsaldo'] : 0;
                
                $qInsert  = "INSERT INTO $cAlfa.$cTabla (comidxxx, comcodxx, comcscxx, comseqxx, teridxxx, terid2xx, pucidxxx, commovxx, comfecve, ccoidxxx, sccidxxx, puctipej, comsaldo, comsalnf, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx) VALUES ";
                $qInsert .= "(\"{$xRD['comidxxx']}\","; //comidxxx varchar(1) NOT NULL COMMENT 'Id del Comprobante',
                $qInsert .= "\"{$xRD['comcodxx']}\",";  //comcodxx varchar(4) NOT NULL DEFAULT '' COMMENT 'Codigo del Comprobante',
                $qInsert .= "\"{$xRD['comcscxx']}\",";  //comcscxx varchar(20) NOT NULL DEFAULT '' COMMENT 'Consecutivo Uno del Comprobante',
                $qInsert .= "\"{$xRD['comseqxx']}\",";  //comseqxx varchar(3) NOT NULL COMMENT 'Secuencia del Comprobante',
                $qInsert .= "\"{$xRD['teridxxx']}\",";  //teridxxx varchar(20) NOT NULL COMMENT 'Tercero del Comprobante',
                $qInsert .= "\"{$xRD['teridxxx']}\",";  //terid2xx varchar(20) NOT NULL COMMENT 'Id del Tercero',
                $qInsert .= "\"{$xRD['pucidxxx']}\",";  //pucidxxx varchar(10) NOT NULL COMMENT 'Cuenta Contable PUC',
                $qInsert .= "\"{$xRD['commovxx']}\",";  //commovxx varchar(1) NOT NULL COMMENT 'Movimiento del Comprobante',
                $qInsert .= "\"{$xRD['comfecve']}\",";  //comfecve date NOT NULL COMMENT 'Fecha de Vencimiento del Comprobante',
                $qInsert .= "\"{$xRD['ccoidxxx']}\",";  //ccoidxxx varchar(10) NOT NULL COMMENT 'Centro de Costo del Comprobante',
                $qInsert .= "\"{$xRD['sccidxxx']}\",";  //sccidxxx varchar(20) NOT NULL COMMENT 'Id del Sub Centro de Costo',
                $qInsert .= "\"{$xRD['puctipej']}\",";  //puctipej varchar(1) NOT NULL COMMENT 'Tipo de Ejecucion(L-Local,N-Niif,vacio-Ambas)',
                $qInsert .= "\"".$nSaldo."\",";         //comsaldo decimal(15,2) NOT NULL COMMENT 'Saldo del Comprobante',
                $qInsert .= "\"".$nSaldoNF."\",";       //comsalnf decimal(15,2) NOT NULL COMMENT 'Saldo del Comprobante en Ejecucion NIIF',
                $qInsert .= "\"$kUser\",";              //regusrxx varchar(12) NOT NULL COMMENT 'Usuario que Creo el Registro',
                $qInsert .= "\"{$xRD['comfecxx']}\",";  //regfcrex date NOT NULL COMMENT 'Fecha de Creacion del Registro',
                $qInsert .= "\"".date("H:i:s")."\",";   //reghcrex time NOT NULL COMMENT 'Hora de Creacion del Registro',
                $qInsert .= "\"".date("Y-m-d")."\",";   //regfmodx date NOT NULL COMMENT 'Fecha de Modificacion del Registro',
                $qInsert .= "\"".date("H:i:s")."\",";   //reghmodx time NOT NULL COMMENT 'Hora de Modificacion del Registro',
                $qInsert .= "\"ACTIVO\")";              //regestxx varchar(12) NOT NULL COMMENT 'Estado del Registro',
                //echo "{$xRD['comidxxx']}~{$xRD['comcodxx']}~{$xRD['comcscxx']}~{$xRD['comseqxx']}~{$xRD['teridxxx']}~{$xRD['pucidxxx']}~{$xRD['commovxx']}~{$xRD['comfecve']}~{$xRD['ccoidxxx']}~{$xRD['sccidxxx']}~{$xRD['puctipej']}~{$xRD['comsaldo']}\n";
                $xInsert = mysql_query($qInsert,$xConexion01);
                if (!$xInsert) {
                  $nBand  = 1;
                  $nError = 1;
                  $cMsj .= "Linea ".str_pad($xRD['lineaidx']+1,4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "Error al Insertar Datos en Saldos [$cTabla].\n";
                }
              }
            }

            if($nBand == 1){
              //Con errores Borro comprobante en cabecera o detalle
              $qDelete  = "DELETE FROM $cAlfa.fcoc$nAno ";
              $qDelete .= "WHERE ";
              $qDelete .= "comidxxx = \"S\"     AND ";
              $qDelete .= "comcodxx = \"999\"   AND ";
              $qDelete .= "comcscxx = \"$cCsc\" AND ";
              $qDelete .= "comcsc2x = \"$cCsc\" ";
              $xDelete = mysql_query($qDelete,$xConexion01);
              
              $qDelete = str_replace("$cAlfa.fcoc$nAno","$cAlfa.fcod$nAno",$qDelete);
              $xDelete = mysql_query($qDelete,$xConexion01);
              
              $qDelete  = "DELETE FROM $cAlfa.$cTabla ";
              $qDelete .= "WHERE ";
              $qDelete .= "comidxxx = \"{$xRD['comidxxx']}\" AND ";
              $qDelete .= "comcodxx = \"{$xRD['comcodxx']}\" AND ";
              $qDelete .= "comcscxx = \"{$xRD['comcscxx']}\" AND ";
              $qDelete .= "comseqxx = \"{$xRD['comseqxx']}\" AND ";
              $qDelete .= "teridxxx = \"{$xRD['teridxxx']}\" AND ";
              $qDelete .= "pucidxxx = \"{$xRD['pucidxxx']}\"";
              $xDelete = mysql_query($qDelete,$xConexion01);
            }
            
            if ($nBand == 0) {
              $nTotal++;
            } 
          }##while ($xRD = mysql_fetch_array($xDatos)) {##
        }

      break;
    } 
  }
  
  if ($nSwitch != 0) { 
    f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique"); ?>
    <script languaje = "javascript">
      parent.fmwork.document.forms['frgrm']['nTimesSave'].value = 0;
      parent.fmwork.document.forms['frgrm']['Btn_Guardar'].disabled = false;
    </script>
  <?php } else {
    if ($_POST['cSalTip'] == 'CRUZARSALDO'){
      $cMsj = "Se Cruzaron $nTotal Saldos Inciales.".(($nError == 1) ? "\n\nSe presentaron los siguientes errores en la ejecucion del proceso: ".$cMsj : "");
    }else{
      $cMsj = "Se Creron $nTotal Saldos Inciales.".(($nError == 1) ? "\n\nSe presentaron los siguientes errores en la ejecucion del proceso: ".$cMsj : "");
    }
     f_Mensaje(__FILE__,__LINE__,$cMsj);
    ?>
    <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
    <script languaje = "javascript">
      parent.fmwork.document.forms['frgrm']['Btn_Guardar'].disabled = true;
      parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
      document.forms['frgrm'].submit()
    </script>
  <?php } 
  
  
  /**
   * Metodo que realiza la conexion y crear las tablas
   */
  function fnConectarDB(){
    global $cAlfa;

    /**
     * Variable para saber si hay o no errores de validacion.
     *
     * @var number
     */
    $nSwitch = 0;

    /**
     * Matriz para Retornar Valores
     */
    $mReturn = array();
    
    /**
     * Reservo Primera Posicion para retorna true o false
     */
    $mReturn[0] = "";
    
    $xConexion99 = mysql_connect(OC_SERVER,OC_USERROBOT,OC_PASSROBOT) or die("El Sistema no Logro Conexion con ".OC_SERVER);
    if($xConexion99){
      $nSwitch = 0;
    }else{
      $nSwitch = 1;
      $mReturn[count($mReturn)] = "El Sistema no Logro Conexion con ".OC_SERVER;
    }
    
    if($nSwitch == 0){
      $mReturn[0] = "true"; $mReturn[1] = $xConexion99; 
    }else{
      $mReturn[0] = "false";    
    }
    return $mReturn;
  }##function fnConectarDB(){##
  
	?>
<?php
  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");
  
	//Estableciendo que el tiempo de ejecucion no se limite 
	set_time_limit (0);
	
	/**
	* Graba Comprobante Cartas Bancarias.
	* --- Descripcion: Permite Subir y Guardar Automaticamente Cartas Bancarias.
	* @author Johana Arboleda Ramos <jarboleda@opentecnologia.com.co>
	* @version 001
	*/
	include("../../../../libs/php/utility.php");
	include("../../../../../config/config.php");
	include("../../../../libs/php/uticonta.php");
	include("../../../../libs/php/uticarbx.php");
	
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
	
	$cSystemPath= OC_DOCUMENTROOT;
	 
	$nSwitch  = 0;   // Switch para Vericar la Validacion de Datos
	$cMensaje     = "\n";
	
	#Numero de registros por recorrido
	$nNumReg = 2000;
	
	#Cadenas para reemplazar caracteres espciales
	$cBuscar = array(chr(13),chr(10),chr(27),chr(9));
	$cReempl = array(" "," "," "," ");
	
	// Validando que el usuario haya dado un solo click en el boton guardar.
	if ($_POST['nTimesSave'] != 1) {
		$nSwitch = 1;
		$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
		$cMensaje .= "El Sistema Detecto mas de un Click en el Boton Guardar.\n";
	}
	
	switch ($_COOKIE['kModo']) {
		case "SUBIR":
			## Validando que haya seleccionado un archivo
			if ($_FILES['cArcPla']['name'] == "") {
				$nSwitch = 1;
				$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMensaje .= "Debe Seleccionar un Archivo.\n";
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
					$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMensaje .= "Error al Copiar Archivo.\n";
				}
			}
			
			#Creando tabla temporal
			if ($nSwitch == 0) {
				//tabla temporal con los datos cargados
				$cTabCar  = fnCadenaAleatoria();
				$qNewTab  = "CREATE TEMPORARY TABLE IF NOT EXISTS $cAlfa.$cTabCar (";
				$qNewTab .= "ccoidxxx varchar(10) NOT NULL COMMENT 'Centro de Costo del Comprobante',";
				$qNewTab .= "sucidxxx varchar(3) NOT NULL COMMENT 'Id de la Sucursal Operativa',";
				$qNewTab .= "docidxxx varchar(20) NOT NULL COMMENT 'Id del DO',";
				$qNewTab .= "docsufxx varchar(3) NOT NULL COMMENT 'Sufijo del DO',";
				$qNewTab .= "teridxxx varchar(12) NOT NULL COMMENT 'Id del Tercero - Cliente',";
				$qNewTab .= "terid2xx varchar(12) NOT NULL COMMENT 'Id del Tercero - Girado a',";
				$qNewTab .= "comobsxx varchar(255) NOT NULL COMMENT 'Observacion del Comprobante',";
				$qNewTab .= "comdocin varchar(10) NOT NULL COMMENT 'Numero Documento Informativo',";
				$qNewTab .= "ctoidxxx varchar(10) DEFAULT NULL COMMENT 'Concepto Contable del Comprobante',";
				$qNewTab .= "comvlr01 decimal(15,2) NOT NULL COMMENT 'Base de Retencion del Comprobante',";
				$qNewTab .= "comvlr02 decimal(15,2) NOT NULL COMMENT 'Iva',";
				$qNewTab .= "comvlr03 decimal(15,2) NOT NULL COMMENT 'ReteFte',";
				$qNewTab .= "comvlrxx decimal(15,2) NOT NULL COMMENT 'Valor Local Comprobante',";
				$qNewTab .= "comvlrnf decimal(15,2) NOT NULL COMMENT 'Valor NIIF Comprobante',";
				$qNewTab .= "banidxxx varchar(12) NOT NULL COMMENT 'Id del Banco',";
				$qNewTab .= "banctaxx varchar(20) NOT NULL COMMENT 'Numero de Cuenta',";
				$qNewTab .= "comtcbxx varchar(2) NOT NULL COMMENT 'Tipo de Comprobante Bancario [Sistema Uno]',";
				$qNewTab .= "comncbxx varchar(6) NOT NULL COMMENT 'Numero de Comprobante Bancario [Sistema Uno]',";
				$qNewTab .= "comcodxx varchar(4) NOT NULL COMMENT 'Codigo del Comprobante')";
				
				$xNewTab = mysql_query($qNewTab,$xConexion01);
				if(!$xNewTab) {
					$nSwitch = 1;
					$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMensaje .= "Error al Crear la Tabla Temporal.\n";
				}
			}
			#Fin Creando tabla temporal
			
			#Cargando Archivo a tabla temporal
			if ($nSwitch == 0) {
				$qLoad  = "LOAD DATA LOCAL INFILE '$cFile' INTO TABLE $cAlfa.$cTabCar ";
				$qLoad .= "FIELDS TERMINATED BY '\\t' LINES TERMINATED BY '\\n' ";
				$qLoad .= "IGNORE 1 LINES";
				$xLoad = mysql_query($qLoad,$xConexion01);
				if(!$xLoad) {
					$nSwitch = 1;
					$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMensaje .= "Error al Cargar los Datos.\n";
					// echo $qLoad."~".mysql_error($xConexion01);
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
				$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMensaje .= "No Se Encontraron Registros.\n";
			}	
			
			
			$nLinea  = 2; //Linea, se empieza en dos porque en el excel exite la fila de titulos
			$cErrGen = "";  //Texto Para Errores Generales
			$vComAna = array(); //Comprobantes analizados si el usuario tiene parametrizado dicho comprobante
			
			$qDatos = "SELECT * FROM $cAlfa.$cTabCar";
			$xDatos = mysql_query($qDatos,$xConexion01);
			//f_Mensaje(__FILE__,__LINE__,$qDatos."~".mysql_num_rows($xDatos));
			$mDatos = array();
			
			while ($xRD = mysql_fetch_array($xDatos)) {
						
				// Validado que exista el centro de costos.
				if ($xRD['ccoidxxx'] == "") {
					$nSwitch = 1;
					$cErrGen .= '<tr>';
					$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
					$cErrGen .= '<td style="padding-left:5px">El Centro de Costo No Puede Ser Vacio.</td>';
					$cErrGen .= '</tr>';
				} else {
					$qValCco  = "SELECT * ";
					$qValCco .= "FROM $cAlfa.fpar0116 ";
					$qValCco .= "WHERE ";
					$qValCco .= "ccoidxxx = \"{$xRD['ccoidxxx']}\" AND ";
					$qValCco .= "regestxx = \"ACTIVO\" LIMIT 0,1";
					$xValCco  = f_MySql("SELECT","",$qValCco,$xConexion01,"");
					//f_Mensaje(__FILE__,__LINE__,$qValCco." ~ ".mysql_num_rows($xValCco));
					if (mysql_num_rows($xValCco) != 1) {
						$nSwitch = 1;
						$cErrGen .= '<tr>';
						$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
						$cErrGen .= '<td style="padding-left:5px">El Centro de Costo ['.$xRD['ccoidxxx'].'] no esta Parametrizado.</td>';
						$cErrGen .= '</tr>';
					}
				}
				
				// Trayendo Datos del Concepto para el DO informativo
				$xRD['ctoidxxx'] = strtoupper(trim($xRD['ctoidxxx']));
				$xRD['comcodxx'] = strtoupper(trim($xRD['comcodxx']));
					
				$qValCon  = "SELECT fpar0119.*,fpar0115.* ";
				$qValCon .= "FROM $cAlfa.fpar0119,$cAlfa.fpar0115 ";
				$qValCon .= "WHERE ";
				$qValCon .= "$cAlfa.fpar0119.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
				$qValCon .= "$cAlfa.fpar0119.ctoidxxx LIKE \"%{$xRD['ctoidxxx']}%\" AND ";
				$qValCon .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
				$qValCon .= "ORDER BY $cAlfa.fpar0119.pucidxxx,$cAlfa.fpar0119.ctoidxxx ";
				$xValCon  = f_MySql("SELECT","",$qValCon,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qValCon." ~ ".mysql_num_rows($xValCon));
					
				$mConCom = array(); $nIndCon = 0;
				while ($xRCon = mysql_fetch_array($xValCon)) {
					$mConceptos = f_explode_array($xRCon['ctocomxx'],"|","~");
					for ($j=0;$j<count($mConceptos);$j++) {
						if (($mConceptos[$j][0] == "L" && $mConceptos[$j][1] == "") ||
						($mConceptos[$j][0] == "L" && $mConceptos[$j][1] == $xRD['comcodxx'])) {
							$mConCom[$nIndCon] = $xRCon;
							$mConCom[$nIndCon]['ctomovxx'] = $mConceptos[$j][2];
							$nIndCon++;
						}
					}
				}
					
				if (count($mConCom) != 1) {
					$nSwitch = 1;
					$cErrGen .= '<tr>';
					$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
					$cErrGen .= '<td style="padding-left:5px">El Concepto ['.$xRD['ctoidxxx'].'] no esta Parametrizado para el Comprobante [L-'.$xRD['comcodxx'].'].</td>';
					$cErrGen .= '</tr>';
				} else {
					//Descipcion Concepto
					$xRD['ctodesxl'] = ($mConCom[0]['ctodesxl'] != "") ? strtoupper(trim($mConCom[0]['ctodesxl'])) : "SIN DESCRIPCION";
					$xRD['ctonitxx'] = $mConCom[0]['ctonitxx'];
					$xRD['pucidxxx'] = $mConCom[0]['pucidxxx'];
					$xRD['pucdetxx'] = $mConCom[0]['pucdetxx'];
					$xRD['pucterxx'] = $mConCom[0]['pucterxx'];
					$xRD['pucretxx'] = $mConCom[0]['pucretxx'];
					$xRD['pucnatxx'] = $mConCom[0]['pucnatxx'];
					$xRD['pucinvxx'] = $mConCom[0]['pucinvxx'];
					$xRD['puccccxx'] = $mConCom[0]['puccccxx'];
					$xRD['pucdoscc'] = $mConCom[0]['pucdoscc'];
					$xRD['puctipej'] = $mConCom[0]['puctipej'];
					$xRD['ctovlr01'] = $mConCom[0]['ctovlr01'];
					$xRD['ctovlr02'] = $mConCom[0]['ctovlr02'];
					$xRD['ctomovxx'] = $mConCom[0]['ctomovxx'];
					$xRD['ctodtocl'] = $mConCom[0]['ctodtocl'];
					$xRD['ctodocxl'] = $mConCom[0]['ctodocxl'];
					$xRD['ctoctocl'] = $mConCom[0]['ctoctocl'];
					$xRD['ctodsacl'] = $mConCom[0]['ctodsacl'];
					$xRD['ctoantxl'] = $mConCom[0]['ctoantxl'];
					$xRD['ctoaplxl'] = $mConCom[0]['ctoaplxl'];
					$xRD['ctoptaxl'] = $mConCom[0]['ctoptaxl'];
					$xRD['ctopvxxl'] = $mConCom[0]['ctopvxxl'];
					$xRD['ctodocil'] = $mConCom[0]['ctodocil'];
					
					if ($xRD['sucidxxx'] != "" && $xRD['docidxxx'] != "" && $xRD['docsufxx'] != "") {
						//Validando que el do exista
						$qDo  = "SELECT * ";
						$qDo .= "FROM $cAlfa.sys00121 ";
						$qDo .= "WHERE ";
						$qDo .= "sucidxxx = \"{$xRD['sucidxxx']}\" AND ";
						$qDo .= "docidxxx = \"{$xRD['docidxxx']}\" AND ";
						$qDo .= "docsufxx = \"{$xRD['docsufxx']}\" AND ";
						$qDo .= "regestxx IN (\"ACTIVO\",\"FACTURADO\") LIMIT 0,1 ";
						$xDo  = f_MySql("SELECT","",$qDo,$xConexion01,"");
						//f_Mensaje(__FILE__,__LINE__,$qDo." ~ ".mysql_num_rows($xDo));
						$vDo =  array();
						if (mysql_num_rows($xDo) != 1) {
							$nSwitch = 1;
							$cErrGen .= '<tr>';
							$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
							$cErrGen .= '<td style="padding-left:5px">El DO ['.$xRD['sucidxxx'].'-'.$xRD['docidxxx'].'-'.$xRD['docsufxx'].'] no Existe.</td>';
							$cErrGen .= '</tr>';
						} else {
							$vDo  = mysql_fetch_array($xDo);
							if ($vDo['regestxx'] == "FACTURADO") {
								$nSwitch = 1;
								$cErrGen .= '<tr>';
								$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
								$cErrGen .= '<td style="padding-left:5px">El DO ['.$xRD['sucidxxx'].'-'.$xRD['docidxxx'].'-'.$xRD['docsufxx'].'] ya fue Facturado.</td>';
								$cErrGen .= '</tr>';
							}
					
							$xRD['comiddox'] = $vDo['comidxxx'];
							$xRD['comcoddo'] = $vDo['comcodxx'];
							$xRD['cliiddox'] = $vDo['cliidxxx'];
							$xRD['clinomdo'] = $vDo['clinomxx'];
					
							if ($vDo['cliidxxx'] != $xRD['teridxxx']) {
								$nSwitch = 1;
								$cErrGen .= '<tr>';
								$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
								$cErrGen .= '<td style="padding-left:5px">El Tercero Cliente ['.$xRD['teridxxx'].'] No es el Importador del DO ['.$xRD['sucidxxx'].'-'.$xRD['docidxxx'].'-'.$xRD['docsufxx'].'].</td>';
								$cErrGen .= '</tr>';
							}
							
							// Validando que exista el tercero cliente, este solo se valida si se digita DO
							$qCliNom  = "SELECT ";
							$qCliNom .= "$cAlfa.SIAI0150.*, ";
							$qCliNom .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
							$qCliNom .= "FROM $cAlfa.SIAI0150 ";
							$qCliNom .= "WHERE ";
							$qCliNom .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$xRD['teridxxx']}\" LIMIT 0,1";
							$xCliNom  = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
							//f_Mensaje(__FILE__,__LINE__,$qCliNom."~".mysql_num_rows($xCliNom));
							if (mysql_num_rows($xCliNom) != 1) {
								$nSwitch = 1;
								$cErrGen .= '<tr>';
								$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
								$cErrGen .= '<td style="padding-left:5px">No Existe el Tercero Cliente ['.$xRD['teridxxx'].'].</td>';
								$cErrGen .= '</tr>';
							} else {
								$vCliNom  = mysql_fetch_array($xCliNom);
								$xRD['ternomxx'] = ($vCliNom['CLINOMXX'] == "") ? "CLIENTE SIN NOMBRE" : $vCliNom['CLINOMXX'];
							}
							
						}
					} else {
						if ($xRD['sucidxxx'] != "" || $xRD['docidxxx'] != "" || $xRD['docsufxx'] != "") {
							$nSwitch = 1;
							$cErrGen .= '<tr>';
							$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
							$cErrGen .= '<td style="padding-left:5px">Los Datos del DO estan Incompletos.</td>';
							$cErrGen .= '</tr>';
						}
					}
						
					// Busco el nombre del tercero proveedor.
					$qProNom  = "SELECT ";
					$qProNom .= "$cAlfa.SIAI0150.*, ";
					$qProNom .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
					$qProNom .= "FROM $cAlfa.SIAI0150 ";
					$qProNom .= "WHERE ";
					$qProNom .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$xRD['terid2xx']}\" LIMIT 0,1";
					$xProNom  = f_MySql("SELECT","",$qProNom,$xConexion01,"");
					if (mysql_num_rows($xProNom) != 1) {
						$nSwitch = 1;
						$cErrGen .= '<tr>';
						$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
						$cErrGen .= '<td style="padding-left:5px">No Existe el Tercero Beneficiario ['.$xRD['terid2xx'].'].</td>';
						$cErrGen .= '</tr>';
					} else {
						$vProNom  = mysql_fetch_array($xProNom);
						$xRD['ternom2x'] = ($vProNom['CLINOMXX'] == "") ? "PROVEEDOR SIN NOMBRE" : $vProNom['CLINOMXX'];

						if ($vProNom['CLIPROCX'] != "SI") {
							$nSwitch = 1;
							$cErrGen .= '<tr>';
							$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
							$cErrGen .= '<td style="padding-left:5px">El Tercero Beneficiario ['.$xRD['terid2xx'].'] No es Proveedor.</td>';
							$cErrGen .= '</tr>';
						}							
					}
						
					// Valido la observacion del comprobante.
					// if (strlen($xRD['comobsxx']) > 50) {
						// $nSwitch = 1;
						// $cErrGen .= '<tr>';
						// $cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
						// $cErrGen .= '<td style="padding-left:5px">La longitud de la Observacion no Puede ser Mayor a 50 Caracteres.</td>';
						// $cErrGen .= '</tr>';
					// }
					
					//Validando que la cuenta PUC del concepto detalla por DO o No Detalle
					if (!f_InList($xRD['pucdetxx'] ,"D","N")) {						
						$nSwitch = 1;
						$cErrGen .= '<tr>';
						$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
						$cErrGen .= '<td style="padding-left:5px">La cuenta ['.$xRD['pucidxxx'].'] parametrizada para el Concepto ['.$xRD['ctoidxxx'].'] debe Detalla por DO o No Detallar.</td>';
						$cErrGen .= '</tr>';
					}
					
					if ($xRD['pucdetxx'] == "D" && ($xRD['sucidxxx'] == "" || $xRD['docidxxx'] == "" || $xRD['docsufxx'] == "")) {
						$nSwitch = 1;
						$cErrGen .= '<tr>';
						$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
						$cErrGen .= '<td style="padding-left:5px">La cuenta Detalla por DO, debe Digitar los Datos de DO.</td>';
						$cErrGen .= '</tr>';
					}
					
					if ($xRD['pucdoscc'] == "S" && ($xRD['sucidxxx'] == "" || $xRD['docidxxx'] == "" || $xRD['docsufxx'] == "")) {
						$nSwitch = 1;
						$cErrGen .= '<tr>';
						$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
						$cErrGen .= '<td style="padding-left:5px">La cuenta Obliga Subcentro de Costo un DO, debe Digitar los Datos de DO.</td>';
						$cErrGen .= '</tr>';
					}
					
					// Validando el tipo de ejecucion de la cuenta
					if (!($xRD['puctipej'] == "L" || $xRD['puctipej'] == "N" || $xRD['puctipej'] == "")) {
						$nSwitch = 1;
						$cErrGen .= '<tr>';
						$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
						$cErrGen .= '<td style="padding-left:5px">Para la cuenta el Tipo de Ejecucion no es Valido, debe Ser LOCAL, NIIF o AMBAS.</td>';
						$cErrGen .= '</tr>';
					}
						
					//Validando el documento informativo segun la parametrizacion del concepto
					if ($xRD['ctodocil'] == "SI") {
						$xRD['comdocin'] = strtoupper(trim($xRD['comdocin']));
						if ($xRD['comdocin'] == ""){
							$nSwitch = 1;
							$cErrGen .= '<tr>';
							$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
							$cErrGen .= '<td style="padding-left:5px">El Numero de Factura No Puede Ser Vacio.</td>';
							$cErrGen .= '</tr>';
						}
					}
						
					//Si es proveedor se valida si es un Concepto de Devolucion de Saldo a Favor en las L
					if ($vProNom['CLIPROCX'] == "SI") {
						if ($xRD['ctodsacl'] == "SI" && "CLIPROCX" != "CLICLIXX") {
							$nSwitch = 1;
							$cErrGen .= '<tr>';
							$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
							$cErrGen .= '<td style="padding-left:5px">El Concepto Esta Parametrizado como [APLICA CLIENTE] pero el Tipo de Tercero es Diferente de Cliente.</td>';
							$cErrGen .= '</tr>';
						}
					}
						
					//Validando que si el concepto es de pago de tributos
					if ($xRD['ctoptaxl'] == "SI") {
						$nSwitch = 1;
						$cErrGen .= '<tr>';
						$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
						$cErrGen .= '<td style="padding-left:5px">El Concepto ['.$xRD['ctoidxxx'].'] - '.$xRD['ctodesxl'].' Esta Parametrizado como Concepto Para Pago de Tributos Aduaneros.</td>';
						$cErrGen .= '</tr>';
					}
						
					//Se validan las retenciones e Ivas si el tipo de ejecuci�n es Local o Ambas
					if ($xRD['puctipej'] == "L" || $xRD['puctipej'] == "") {
						if ($xRD['ctovlr01'] == "SI" || $xRD['ctovlr02'] == "SI") {
							if ($xRD['pucretxx'] > 0) { // Es una retencion
								//Debe tener Digitada la Base
								if (($xRD['comvlr01']+0) == 0) {
									$nSwitch = 1;
									$cErrGen .= '<tr>';
									$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
									$cErrGen .= '<td style="padding-left:5px">Debe Digitar la Base, para el concepto ['.$xRD['ctoidxxx'].'] - '.$xRD['ctodesxl'].' aplica una Retencion del ['.$xRD['pucretxx'].'%].</td>';
									$cErrGen .= '</tr>';
								}
					  		
								//No debe tener Digitado Iva
								if (($xRD['comvlr02']+0) > 0) {
									$nSwitch = 1;
									$cErrGen .= '<tr>';
									$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
									$cErrGen .= '<td style="padding-left:5px">No Debe Digitar IVA, el concepto ['.$xRD['ctoidxxx'].'] - '.$xRD['ctodesxl'].' es un Concepto de Retencion.</td>';
									$cErrGen .= '</tr>';
								}
				
								//Debe tener digitada la Retencion
								if (($xRD['comvlr03']+0) == 0) {
									$nSwitch = 1;
									$cErrGen .= '<tr>';
									$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
									$cErrGen .= '<td style="padding-left:5px">Debe Digitar la Retencion para el concepto ['.$xRD['ctoidxxx'].'] - '.$xRD['ctodesxl'].' aplica una Retencion del ['.$xRD['pucretxx'].'%].</td>';
									$cErrGen .= '</tr>';
								}
				
								//La Retencion debe ser igual al calculo de la base por el porcentaje de retencion de la cuenta
								$nRetencion = ($xRD['pucretxx']+0)/100;
								$nValorRete = round($xRD['comvlr01'] * $nRetencion);
				
								if (($xRD['comvlr03']+0) != $nValorRete) {
									$nSwitch = 1;
									$cErrGen .= '<tr>';
									$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
									$cErrGen .= '<td style="padding-left:5px">El Valor de la Retencion ['.$xRD['comvlr03'].'] no es Igual al ['.$xRD['pucretxx'].'%] de la Base ['.$xRD['comvlr01'].'].</td>';
									$cErrGen .= '</tr>';
								} 
				
								//El valor total debe ser igual a la retencion
								if (($xRD['comvlrxx']+0) != ($xRD['comvlr03']+0)) {
									$nSwitch = 1;
									$cErrGen .= '<tr>';
									$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
									$cErrGen .= '<td style="padding-left:5px">El Valor del Comprobante ['.$xRD['comvlrxx'].'] no es Igual al Valor Retencion ['.$xRD['comvlr03'].'].</td>';
									$cErrGen .= '</tr>';
								}
				
							} else { // Es un IVA.
								//Debe tener Digitada la Base
								if (($xRD['comvlr01']+0) == 0) {
									$nSwitch = 1;
									$cErrGen .= '<tr>';
									$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
									$cErrGen .= '<td style="padding-left:5px">Debe Digitar la Base, para el concepto ['.$xRD['ctoidxxx'].'] - '.$xRD['ctodesxl'].' aplica IVA.</td>';
									$cErrGen .= '</tr>';
								}
				
								//No debe tener digitada Retencion
								if (($xRD['comvlr03']+0) > 0) {
									$nSwitch = 1;
									$cErrGen .= '<tr>';
									$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
									$cErrGen .= '<td style="padding-left:5px">No Debe Digitar Retencion, el concepto ['.$xRD['ctoidxxx'].'] - '.$xRD['ctodesxl'].' No es un concepto de Retencion.</td>';
									$cErrGen .= '</tr>';
								}
				
								//Debe tener digitado el IVA
								if (($xRD['comvlr02']+0) < 0) {
									$nSwitch = 1;
									$cErrGen .= '<tr>';
									$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
									$cErrGen .= '<td style="padding-left:5px">Para el concepto ['.$xRD['ctoidxxx'].'] - '.$xRD['ctodesxl'].' debe Digitar el IVA.</td>';
									$cErrGen .= '</tr>';
								}
				
								//El valor total debe ser igual a la base mas el iva
								if (($xRD['comvlrxx']+0) != ($xRD['comvlr01'] + $xRD['comvlr02'])) {
									$nSwitch = 1;
									$cErrGen .= '<tr>';
									$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
									$cErrGen .= '<td style="padding-left:5px">El Valor del Comprobante debe Ser Igual a la Sumatoria de la Base mas el Iva.</td>';
									$cErrGen .= '</tr>';
								}
							}
						} else {
							//No debe digitar Base
							if (($xRD['comvlr01']+0) > 0) {
								$nSwitch = 1;
								$cErrGen .= '<tr>';
								$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
								$cErrGen .= '<td style="padding-left:5px">Para el concepto ['.$xRD['ctoidxxx'].'] - '.$xRD['ctodesxl'].' la Base debe ser Vacia o Igual a Cero.</td>';
								$cErrGen .= '</tr>';
							}
					  	
							//No debe tener digitado IVA
							if (($xRD['comvlr02']+0) > 0) {
								$nSwitch = 1;
								$cErrGen .= '<tr>';
								$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
								$cErrGen .= '<td style="padding-left:5px">Para el concepto ['.$xRD['ctoidxxx'].'] - '.$xRD['ctodesxl'].' el IVA debe ser Vacio o Igual a Cero.</td>';
								$cErrGen .= '</tr>';
							}
					  	
							//No debe tener digitado Retencion
							if (($xRD['comvlr03']+0) > 0) {
								$nSwitch = 1;
								$cErrGen .= '<tr>';
								$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
								$cErrGen .= '<td style="padding-left:5px">Para el concepto ['.$xRD['ctoidxxx'].'] - '.$xRD['ctodesxl'].' la Retencion debe ser Vacia o Igual a Cero.</td>';
								$cErrGen .= '</tr>';
							}
						}
					} elseif ($xRD['puctipej'] == "N") {
						//No debe digitar Base
						if (($xRD['comvlr01']+0) > 0) {
							$nSwitch = 1;
							$cErrGen .= '<tr>';
							$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
							$cErrGen .= '<td style="padding-left:5px">Para el Tipo de Ejecucion NIIF la Base debe ser Vacia o Igual a Cero.</td>';
							$cErrGen .= '</tr>';
						}
						
						//No debe tener digitado IVA
						if (($xRD['comvlr02']+0) > 0) {
							$nSwitch = 1;
							$cErrGen .= '<tr>';
							$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
							$cErrGen .= '<td style="padding-left:5px">Para el Tipo de Ejecucion NIIF el IVA debe ser Vacio o Igual a Cero.</td>';
							$cErrGen .= '</tr>';
						}
						
						//No debe tener digitado Retencion
						if (($xRD['comvlr03']+0) > 0) {
							$nSwitch = 1;
							$cErrGen .= '<tr>';
							$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
							$cErrGen .= '<td style="padding-left:5px">Para el Tipo de Ejecucion NIIF la Retencion debe ser Vacia o Igual a Cero.</td>';
							$cErrGen .= '</tr>';
						}
					}
			
					if ($xRD['puctipej'] == "L" || $xRD['puctipej'] == "") {
						//Validando el valor del comprobante
						if (($xRD['comvlrxx']+0) <= 0) {
							$nSwitch = 1;
							$cErrGen .= '<tr>';
							$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
							$cErrGen .= '<td style="padding-left:5px">El Valor Local del Comprobante debe ser Mayor a Cero.</td>';
							$cErrGen .= '</tr>';
						}
						if ($xRD['puctipej'] == "L") {
							//Validando que no haya digitado valor NIIF
							if (($xRD['comvlrnf']+0) > 0) {
								$nSwitch = 1;
								$cErrGen .= '<tr>';
								$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
								$cErrGen .= '<td style="padding-left:5px">El Valor NIIF del Comprobante debe ser Cero.</td>';
								$cErrGen .= '</tr>';
							}
						} else {
							//Validando que haya digitado valor NIIF
							if (($xRD['comvlrnf']+0) <= 0) {
								$nSwitch = 1;
								$cErrGen .= '<tr>';
								$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
								$cErrGen .= '<td style="padding-left:5px">El Valor NIIF del Comprobante debe ser Mayor a Cero.</td>';
								$cErrGen .= '</tr>';
							}
						}
					} elseif ($xRD['puctipej'] == "N") {
						//Validando que haya digitado valor NIIF
						if (($xRD['comvlrnf']+0) <= 0) {
							$nSwitch = 1;
							$cErrGen .= '<tr>';
							$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
							$cErrGen .= '<td style="padding-left:5px">El Valor NIIF del Comprobante debe ser Mayor a Cero.</td>';
							$cErrGen .= '</tr>';
						}
						//Validando que no haya digitado valor Local
						if (($xRD['comvlrxx']+0) > 0) {
							$nSwitch = 1;
							$cErrGen .= '<tr>';
							$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
							$cErrGen .= '<td style="padding-left:5px">El Valor Local del Comprobante debe ser Cero.</td>';
							$cErrGen .= '</tr>';
						}
					}	
				}
			
				//Validando el Banco
				$qBanId  = "SELECT banidxxx ";
				$qBanId .= "FROM $cAlfa.fpar0124 ";
				$qBanId .= "WHERE ";
				$qBanId .= "banidxxx = \"{$xRD['banidxxx']}\" AND ";
				$qBanId .= "regestxx = \"ACTIVO\" LIMIT 0,1";
				$xBanId  = f_MySql("SELECT","",$qBanId,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qBanId." ~ ".mysql_num_rows($xBanId));
				if (mysql_num_rows($xBanId) != 1) {
					$nSwitch = 1;
					$cErrGen .= '<tr>';
					$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
					$cErrGen .= '<td style="padding-left:5px">El Banco ['.$xRD['banidxxx'].'] no esta Parametrizado.</td>';
					$cErrGen .= '</tr>';
				}
			
				//Validando la Cta Corriente del Banco
				$qCtaBan  = "SELECT banidxxx ";
				$qCtaBan .= "FROM $cAlfa.fpar0128 ";
				$qCtaBan .= "WHERE ";
				$qCtaBan .= "banidxxx = \"{$xRD['banidxxx']}\" AND ";
				$qCtaBan .= "banctaxx = \"{$xRD['banctaxx']}\" AND ";
				$qCtaBan .= "regestxx = \"ACTIVO\" LIMIT 0,1";
				$xCtaBan  = f_MySql("SELECT","",$qCtaBan,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qCtaBan." ~ ".mysql_num_rows($xCtaBan));
				if (mysql_num_rows($xCtaBan) != 1) {
					$nSwitch = 1;
					$cErrGen .= '<tr>';
					$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
					$cErrGen .= '<td style="padding-left:5px">La Cuenta ['.$xRD['banctaxx'].'] no esta Parametrizada para el Banco ['.$xRD['banidxxx'].'].</td>';
					$cErrGen .= '</tr>';
				}
			
				//Si la variable General del Sistema financiero_aplica_documento_conciliacion_cartabco viene en SI, se hacen obligatorios los campos de Comprobante Bancario
				if($vSysStr['financiero_aplica_documento_conciliacion_cartabco'] == "SI"){
					if (!f_InList($xRD['comtcbxx'],"CG","CH","ND","NC")) {
						$nSwitch = 1;
						$cErrGen .= '<tr>';
						$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
						$cErrGen .= '<td style="padding-left:5px">El Comprobante Bancario debe ser [CG o CH o ND o NC].</td>';
						$cErrGen .= '</tr>';
					}
			
					if($xRD['comncbxx'] == ""){
						$nSwitch = 1;
						$cErrGen .= '<tr>';
						$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
						$cErrGen .= '<td style="padding-left:5px">El Numero de Comprobante Bancario no puede ser Vacio.</td>';
						$cErrGen .= '</tr>';
					}
				} else {
					//Valido que si digito comprobante bancario este sea un valor valido
					if ($xRD['comtcbxx'] != "") {
						if (!f_InList($xRD['comtcbxx'],"CG","CH","ND","NC")) {
							$nSwitch = 1;
							$cErrGen .= '<tr>';
							$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
							$cErrGen .= '<td style="padding-left:5px">El Comprobante Bancario debe ser [CG o CH o ND o NC].</td>';
							$cErrGen .= '</tr>';
						}
					}
				}
			
				// Validado que exista el comprobante.
				if ($xRD['comcodxx'] == "") {
					$nSwitch = 1;
					$cErrGen .= '<tr>';
					$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
					$cErrGen .= '<td style="padding-left:5px">El Codigo del Comprobante No Puede Ser Vacio.</td>';
					$cErrGen .= '</tr>';
				} else {
					// Validado que exista el comprobante.
					$qValCom  = "SELECT * ";
					$qValCom .= "FROM $cAlfa.fpar0117 ";
					$qValCom .= "WHERE ";
					$qValCom .= "comidxxx = \"L\" AND ";
					$qValCom .= "comcodxx = \"{$xRD['comcodxx']}\" AND ";
					$qValCom .= "regestxx = \"ACTIVO\" LIMIT 0,1";
					$xValCom  = f_MySql("SELECT","",$qValCom,$xConexion01,"");
					//f_Mensaje(__FILE__,__LINE__,$qValCom." ~ ".mysql_num_rows($xValCom));
					
					if (mysql_num_rows($xValCom) != 1) {
						$nSwitch = 1;
						$cErrGen .= '<tr>';
						$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
						$cErrGen .= '<td style="padding-left:5px">El Comprobante [L-'.$xRD['comcodxx'].'] no esta Parametrizado.</td>';
						$cErrGen .= '</tr>';
					} else {
						$vValCom = mysql_fetch_array($xValCom);
			
						if ($vValCom['comtcoxx'] == "MANUAL") {
							$nSwitch = 1;
							$cErrGen .= '<tr>';
							$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
							$cErrGen .= '<td style="padding-left:5px">El Tipo de Consecutivo del Comprobante [L-'.$xRD['comcodxx'].'] es MANUAL.</td>';
							$cErrGen .= '</tr>';
						}
						
						if (in_array("L-{$xRD['comcodxx']}",$vComAna) == false) {
							$vComAna[count($vComAna)] = "L-{$xRD['comcodxx']}";
							/**
							 * Valido que el usuario tenga parametrizado este documento contable
							 */
							$qUsrDoc  = "SELECT USRDOCXX ";
							$qUsrDoc .= "FROM $cAlfa.SIAI0003 ";
							$qUsrDoc .= "WHERE USRIDXXX = \"{$_COOKIE['kUsrId']}\" AND ";
							$qUsrDoc .= "USRDOCXX LIKE \"%|L~{$xRD['comcodxx']}|%\" AND ";
							$qUsrDoc .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
							$xUsrDoc  = f_MySql("SELECT","",$qUsrDoc,$xConexion01,"");
							//f_Mensaje(__FILE__,__LINE__,$qUsrDoc."~".mysql_num_rows($xUsrDoc));
							if(mysql_num_rows($xUsrDoc)==0){
								$nSwitch = 1;
								$cErrGen .= '<tr>';
								$cErrGen .= '<td style="text-align:center">'.str_pad($nLinea,3,"0",STR_PAD_LEFT).'</td>';
								$cErrGen .= '<td style="padding-left:5px">El Usuario no tiene Parametrizado el Comprobante [L-'.$xRD['comcodxx'].'].</td>';
								$cErrGen .= '</tr>';
							}
						}
					}
				}
					
				$mDatos[count($mDatos)] = $xRD;
				$nLinea++;
			}		
		break;
		case "NUEVO":
			
			if ($_POST['cCcoId'] == "") {
				$nSwitch = 1;
				$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMensaje .= "Debe Seleccionar el Centro de Costo.\n";
			} else {
				// Validado que exista el centro de costos.
				$qValCco  = "SELECT * ";
				$qValCco .= "FROM $cAlfa.fpar0116 ";
				$qValCco .= "WHERE ";
				$qValCco .= "ccoidxxx = \"{$_POST['cCcoId']}\" AND ";
				$qValCco .= "regestxx = \"ACTIVO\" LIMIT 0,1";
				$xValCco  = f_MySql("SELECT","",$qValCco,$xConexion01,"");
				if (mysql_num_rows($xValCco) != 1) {
					$nSwitch = 1;
					$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMensaje .= "El Centro de Costo [{$_POST['cCcoId']}] no esta Parametrizado.\n";
				}
			}
			
			//Si es Alcomex, se hace obligatorio digitar Subcentro de Costo en Cabecera.
			if($cAlfa == "INTERLOG" || $cAlfa == "TEINTERLOG" || $cAlfa == "DEINTERLOG"){
				if ($_POST['cSccId'] == "") {
					$nSwitch = 1;
					$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMensaje .= "El Sub Centro de Costo no puede ser Vacio.\n";
				}
			}
			
			// Si el subcentro de costo viene diferente de vacio se valida que exista.
			if ($_POST['cSccId'] != "") {
				$qValScc  = "SELECT * ";
				$qValScc .= "FROM $cAlfa.fpar0120 ";
				$qValScc .= "WHERE ";
				$qValScc .= "ccoidxxx = \"{$_POST['cCcoId']}\" AND ";
				$qValScc .= "sccidxxx = \"{$_POST['cSccId']}\" AND ";
				$qValScc .= "regestxx = \"ACTIVO\" LIMIT 0,1";
				$xValScc  = f_MySql("SELECT","",$qValScc,$xConexion01,"");
				if (mysql_num_rows($xValScc) != 1) {
					$nSwitch = 1;
					$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMensaje .= "El Sub Centro de Costo [{$_POST['cSccId']}] no esta Parametrizado.\n";
				}
			}
			
			// Validando que la Fecha del Comprobante no este Vacia.
			if ($_POST['dComFec'] == "" or $_POST['dComFec'] == "0000-00-00") {
				$nSwitch = 1;
				$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMensaje .= "La Fecha del Comprobante no puede ser vacia el formato es [AAAA-MM-DD].\n";
			}
			$cPerAno  = substr($_POST['dComFec'],0,4);
			$cPerMes  = substr($_POST['dComFec'],5,2);
			
			// Validando la hora del comprobante.
			if ($_POST['tRegHCre'] == "") {
				$nSwitch = 1;
				$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMensaje .= "La Hora del Comprobante [{$_POST['tRegHCre']}] no Puede ser Vacia.\n";
			}
			
			// Validando el tipo de documento a imprimir, CARTA o CHEQUE
			if (!f_InList($_POST['cComAsu'],"CARTA","CHEQUE")) {
				$nSwitch = 1;
				$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMensaje .= "El Asunto del Documento ".(($_POST['cComAsu'] != "") ? "[{$_POST['cComAsu']}] " : "")."debe ser [CARTA o CHEQUE].\n";
			}
			
			// Validando el tipo de contrapartida
			if (!f_InList($_POST['cConPro'],"SI","NO")) {
				$nSwitch = 1;
				$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMensaje .= "Debe Seleccionar si la Contrapartida Automatica es por Proveedor [SI o NO].\n";
			}
			
			// Valido que haya tasa de cambio
			if ($_POST['nTasaCambio'] == "" || $_POST['nTasaCambio'] <= 0) {
				$nSwitch = 1;
				$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMensaje .= "El Sistema no Detecto tasa de Cambio para el Comprobante.\n";
			}
			// Fin de Valido que haya tasa de cambio
			
			if ($_POST['cTerIdB'] == "") {
				$nSwitch = 1;
				$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMensaje .= "Debe Seleccionar el Tercero Beneficiario.\n";
			} else {
				// Validando que exista el tercero beneficiario
				$qTerPro  = "SELECT * ";
				$qTerPro .= "FROM $cAlfa.SIAI0150 ";
				$qTerPro .= "WHERE ";
				$qTerPro .= "CLIIDXXX = \"{$_POST['cTerIdB']}\" AND ";
				$qTerPro .= "{$_POST['cTerTipB']} = \"SI\" AND ";
				$qTerPro .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
				$xTerPro  = f_MySql("SELECT","",$qTerPro,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qTerPro." ~ ".mysql_num_rows($xTerPro));
				if (mysql_num_rows($xTerPro) != 1) {
					$nSwitch = 1;
					$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMensaje .= "No Existe el Tercero Beneficiario [{$_POST['cTerIdB']} - {$_POST['cTerNomB']}].\n";
				}
			}
			
			if ($_POST['nSecuencia'] == 0) {
				$nSwitch = 1;
				$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMensaje .= "Debe Existir al Menos un Registro en la grilla.\n";
			}
			
			if ($nSwitch == 0) {
				/**
				 * Armando matriz con los datos del comprobante y validando datos
				 * Se debe armar una matriz con datos de cabecera y una con datos de detalle
				 * El sistema debe agrupar por comprobante y banco
				 */
				
				/**
				 * Matriz de Cabecera, se toman los datos del primier regisro del comprobante
				 * Los unicos datos que no se toman del primer comprobante son el centro de costo y subcentro de costo
				 * que se seleccionan en la interfaz
				 * 
				 * $mDatos['cModo']   			//Accion (NUEVO,ANTERIOR,EDITAR,BORRAR)
				 * $mDatos['cConPro']   		//Creacion de la contrapartida automatica por proveedor (con un unico registro (NO) o por Proveedor (SI))
				 * $mDatos['nSecuencia']   //Cantidad de Registros de la Grilla
				 * $mDatos['nTimesSave']   //Cantidad de Click de bnt Guardar
				 * $mDatos['cComId']       //Comprobante
				 * $mDatos['cComCod']      //Codigo
				 * $mDatos['cComTco']      //Tipo de Consecutivo para el comprobante (MANUAL/AUTOMATICO)
				 * $mDatos['cComCco]; 			//Control Consecutivo (MENSUAL/ANUAL/INDEFINIDO)
				 * $mDatos['dComFec']      //Fecha
				 * $mDatos['tRegHCre']	 		//Hora Creacion del Comprobante
				 * $mDatos['cCcoId']       //Centro de Costo seleccionado en la cabecera
				 * $mDatos['cSccId']       //Subcentro de Costo seleccionado en la cabecera
				 * $mDatos['cComAsu']      //Asunto
				 * $mDatos['cTerTipB']     //Siempre es CLIPROCX
				 * $mDatos['cTerIdB']      //Girado a
				 * $mDatos['cComObs']      //Observacion
				 * $mDatos['cBanId']       //Banco	     
				 * $mDatos['cBanCta']      //Cuenta Corriente	Banco		     
				 * $mDatos['cBanPuc']      //Cuenta PUC Banco				     
				 * $mDatos['cComTCB']      //Tipo de Comprobante Bancario [Sistema Uno]
				 * $mDatos['cComNCB']      //Numero de Comprobante Bancario [Sistema Uno]
				 * $mDatos['cComPet']	     //Pago Electronico Tributos Aduaneros siempre va en NO
				 * $mDatos['cComMemo']     //Declaraciones
				 * $mDatos['nTasaCambio']  //Tasa de Cambio
				 * $mDatos['nComVlr01']    //Sumatoria de las bases
				 * $mDatos['nComVlr02']    //Sumatoria del Iva
				 * $mDatos['cRegEst']    	//Estado del Comprobante
				 */
				
				/**
				 * La matriz de detalle debe armarse con los valores que se muestran en la grilla del comprobante
				 * $mDatos['cComSeq'  .$i] // Secuencia
				 * $mDatos['cComSeqE' .$i] // Equivalencia en la grilla del proceso de cartas bancarias automaticas
				 * $mDatos['cCtoId'   .$i] // Id del Concepto
				 * $mDatos['cCtoDes'  .$i] // Descripcion del Concepto
				 * $mDatos['cComObs'  .$i] // Observacion del Comprobante
				 * $mDatos['cComIdC'  .$i] // Id Comprobante Cruce
				 * $mDatos['cComCodC' .$i] // Codigo Comprobante Cruce
				 * $mDatos['cComCscC' .$i] // Consecutivo Comprobante Cruce
				 * $mDatos['cComSeqC' .$i] // Secuencia Comprobante Cruce
				 * $mDatos['cCcoId'   .$i] // Centro de Costos
				 * $mDatos['cSccId'   .$i] // Sub Centro de Costos
				 * $mDatos['cDocInf'  .$i] // Documento Informativo Aduanera Grancolombiana
				 * $mDatos['cComCtoC' .$i] // Concepto Comprobante Cruce
				 * $mDatos['cDosIdC'  .$i] // Hidden DO Cruce - Id del Comprobante
				 * $mDatos['cDosCodC' .$i] // Hidden DO Cruce - Codigo del Comprobante
				 * $mDatos['cDosCscC' .$i] // Hidden DO Cruce - Consecutivo del Comprobante
				 * $mDatos['cDosSeqC' .$i] // Hidden DO Cruce - Secuencia del Comprobante
				 * $mDatos['cDosCto'  .$i] // Hidden DO Cruce - Concepto Contable
				 * $mDatos['nComBRet' .$i] // Base de Retencion
				 * $mDatos['nComBIva' .$i] // Base de Iva
				 * $mDatos['nComIva'  .$i] // Valor del Iva
				 * $mDatos['nComVlr'  .$i] // Valor del Comprobante
				 * $mDatos['cComMov'  .$i] // Movimiento Debito o Credito
				 * $mDatos['cComNit'  .$i] // Hidden (Nit que va para SIIGO)
				 * $mDatos['cTerTip'  .$i] // Hidden (Tipo de Tercero) - Siempre es CLICLIXX 
				 * $mDatos['cTerId'   .$i] // Hidden (Id del Tercero)
				 * $mDatos['cTerNom'  .$i] // Hidden (Nombre del Tercero)
				 * $mDatos['cTerTipB' .$i] // Hidden (Tipo de Tercero Dos) - Siempre es CLIPROCX
				 * $mDatos['cTerIdB'  .$i] // Hidden (Id del Tercero Dos)
				 * $mDatos['cTerTipC' .$i] // Hidden (Tipo de Tercero DO Informativo)
				 * $mDatos['cTerIdC'  .$i] // Hidden (Id del Tercero DO Informativo)			  			  
				 * $mDatos['cPucId'   .$i] // Hidden (La Cuenta Contable)
				 * $mDatos['cPucDet'  .$i] // Hidden (Detalle de la Cuenta)
				 * $mDatos['cPucTer'  .$i] // Hidden (Cuenta de Terceros?)
				 * $mDatos['nPucRet'  .$i] // Hidden (Porcentaje de Retencion de la Cuenta)
				 * $mDatos['cPucNat'  .$i] // Hidden (Naturaleza de la Cuenta)
				 * $mDatos['cPucInv'  .$i] // Hidden (Cuenta de Inventarios?)
				 * $mDatos['cPucCco'  .$i] // Hidden (Aplica Centro de Costo para esta Cuenta?)
				 * $mDatos['cPucDoSc' .$i] // Hidden (Aplica DO para Subcentro de Costo?)			  
				 * $mDatos['cComVlr1' .$i] // Hidden (Valor Uno)
				 * $mDatos['cComVlr2' .$i] // Hidden (Valor Dos)
				 * $mDatos['cComFac'  .$i] // Hidden (Comfac)
				 * $mDatos['cSucId'   .$i] // Hidden (Sucursal)
				 * $mDatos['cDocId'   .$i] // Hidden (Do)
				 * $mDatos['cDocSuf'  .$i] // Hidden (Sufijo)
				 * $mDatos['cDecxDO'  .$i] // Hidden Declaraciones por DO, en este caso va vacio porque no se van a pagar tributos
				 * $mDatos['cCtoDtoC' .$i] // Hidden Aplica Documento Cruce
				 * $mDatos['cCtoDOC'  .$i] // Hidden Aplica DO Informativo
				 * $mDatos['cCtoCtoC' .$i] // Hidden Aplica Concepto Informativo
				 * $mDatos['cCtoTF'   .$i] // Hidden Es un Concepto de Transferencia de Fondos, vacio porque aplica para las G, pero esta en el estandar de la grilla
				 * $mDatos['cCtoDSAC' .$i] // Hidden Es un Concepto de Devolucion de Saldos a Favor del Cliente
				 * $mDatos['cCtoAnt'  .$i] // Hidden Es un Concepto de Anticipos
				 * $mDatos['cCtoApl'  .$i] // Hidden Para que Tipo de Terceros Aplica
				 * $mDatos['cCtoPTA'  .$i] // Hidden Es un Concepto para Pago de Tributos Aduaneros
				 * $mDatos['cCtoVUCE' .$i] // Hidden Es un Concepto para Pago VUCE
				 * $mDatos['cDocInfV' .$i] // Hidden Esta variable se utiliza para activar la casilla de Documento Informativo Aduanera Grancolombiana
				 */
				
				/**
				 * Agurupando por Comprobante y Banco
				 * Se realizan las validaciones correspondientes
				 */
				
				$mDatos  = array(); //Su Llave va hacer comprobante banco
				$vPerAna = array(); //Comprobantes a los que ya se les analizo si tienen o no el periodo abierto
				
				for ($i=0; $i<$_POST['nSecuencia']; $i++) {
					
					// Validado que exista el comprobante.
					$qValCom  = "SELECT * ";
					$qValCom .= "FROM $cAlfa.fpar0117 ";
					$qValCom .= "WHERE ";
					$qValCom .= "comidxxx = \"L\" AND ";
					$qValCom .= "comcodxx = \"{$_POST['cComCod'.($i+1)]}\" AND ";
					$qValCom .= "regestxx = \"ACTIVO\" LIMIT 0,1";
					$xValCom  = f_MySql("SELECT","",$qValCom,$xConexion01,"");
					if (mysql_num_rows($xValCom) != 1) {
						$nSwitch = 1;
						$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						$cMensaje .= "En la Secuencia {$_POST['cComSeq'.($i+1)]} el Comprobante [L-{$_POST['cComCod'.($i+1)]}] no esta Parametrizado.\n";
					} else {
						$vValCom = mysql_fetch_array($xValCom);
						$_POST['cComDes'.($i+1)] = $vValCom['comdesxx'];
						$_POST['cComTco'.($i+1)] = $vValCom['comtcoxx'];
						$_POST['cComCco'.($i+1)] = $vValCom['comccoxx'];

						if ($_POST['cComTco'.($i+1)] == "MANUAL") {
							$nSwitch = 1;
							$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMensaje .= "En la Secuencia {$_POST['cComSeq'.($i+1)]} el Tipo de Consecutivo del comprobante es MANUAL.\n";
						}
						
						if (in_array("L-{$_POST['cComCod'.($i+1)]}",$vPerAna) == false) {
							$vPerAna[count($vPerAna)] = "L-{$_POST['cComCod'.($i+1)]}";
							
							
							/**
							 * Valido que el usuario tenga parametrizado este documento contable
							 */
							$qUsrDoc  = "SELECT USRDOCXX ";
							$qUsrDoc .= "FROM $cAlfa.SIAI0003 ";
							$qUsrDoc .= "WHERE USRIDXXX = \"{$_COOKIE['kUsrId']}\" AND ";
							$qUsrDoc .= "USRDOCXX LIKE \"%|L~{$_POST['cComCod'.($i+1)]}|%\" AND ";
							$qUsrDoc .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
							$xUsrDoc  = f_MySql("SELECT","",$qUsrDoc,$xConexion01,"");
							//f_Mensaje(__FILE__,__LINE__,$qUsrDoc."~".mysql_num_rows($xUsrDoc));
							if(mysql_num_rows($xUsrDoc)==0){
								$nSwitch = 1;
								$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
								$cMensaje .= "El Usuario no tiene Parametrizado el Comprobante [L-{$_POST['cComCod'.($i+1)]}].\n";
							}
							
							// Valido que haya periodo contable abierto.
							$qValPer  = "SELECT * ";
							$qValPer .= "FROM $cAlfa.fpar0122 ";
							$qValPer .= "WHERE ";
							$qValPer .= "comidxxx = \"L\"  AND ";
							$qValPer .= "comcodxx = \"{$_POST['cComCod'.($i+1)]}\" AND ";
							$qValPer .= "peranoxx = \"$cPerAno\"                  AND ";
							$qValPer .= "permesxx = \"$cPerMes\"                  AND ";
							$qValPer .= "regestxx = \"ABIERTO\" ORDER BY comidxxx,comcodxx,peranoxx,permesxx DESC LIMIT 0,1";
							$xValPer  = f_MySql("SELECT","",$qValPer,$xConexion01,"");
							//f_mensaje(__FILE__,__LINE__,$qValPer);
							if (mysql_num_rows($xValPer) != 1) {
								$nSwitch = 1;
								$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
								$cMensaje .= "Para el Comprobante [L-{$_POST['cComCod'.($i+1)]}] No Existe Periodo Abierto para la Fecha [{$_POST['dComFec']}].\n";
							}
						}
					}
					
					// Validado que exista el centro de costos.
					if ($_POST['cCcoId'.($i+1)] == "") {
						$nSwitch = 1;
						$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] el Centro de Costo No Puede Ser Vacio.\n";
					} else {
					  $qValCco  = "SELECT * ";
					  $qValCco .= "FROM $cAlfa.fpar0116 ";
					  $qValCco .= "WHERE ";
					  $qValCco .= "ccoidxxx = \"{$_POST['cCcoId'.($i+1)]}\" AND ";
					  $qValCco .= "regestxx = \"ACTIVO\" LIMIT 0,1";
	          $xValCco  = f_MySql("SELECT","",$qValCco,$xConexion01,"");
	          //f_Mensaje(__FILE__,__LINE__,$qValCco." ~ ".mysql_num_rows($xValCco));
	          if (mysql_num_rows($xValCco) != 1) {
	            $nSwitch = 1;
					    $cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
		  			  $cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] el Centro de Costo [{$_POST['cCcoId']}] no esta Parametrizado.\n";
	          }
					}
					
					// Validando que el concepto exista y que pertenezca al comprobante que estoy utilizando.
          $qValCon  = "SELECT fpar0119.*,fpar0115.* ";
					$qValCon .= "FROM $cAlfa.fpar0119,$cAlfa.fpar0115 ";
					$qValCon .= "WHERE ";
					$qValCon .= "$cAlfa.fpar0119.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
					$qValCon .= "$cAlfa.fpar0119.ctoidxxx LIKE \"%{$_POST['cCtoId'.($i+1)]}%\" AND ";
					$qValCon .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
					$qValCon .= "ORDER BY $cAlfa.fpar0119.pucidxxx,$cAlfa.fpar0119.ctoidxxx ";
					$xValCon  = f_MySql("SELECT","",$qValCon,$xConexion01,"");
					//f_Mensaje(__FILE__,__LINE__,$qValCon." ~ ".mysql_num_rows($xValCon));
	  			
	  			$mConCom = array(); $nIndCon = 0;
	  			while ($xRCon = mysql_fetch_array($xValCon)) {
	  				$mConceptos = f_explode_array($xRCon['ctocomxx'],"|","~");
	  				for ($j=0;$j<count($mConceptos);$j++) {
	  					if (($mConceptos[$j][0] == "L" && $mConceptos[$j][1] == "") ||
	  					($mConceptos[$j][0] == "L" && $mConceptos[$j][1] == $_POST['cComCod'.($i+1)])) {
	  						$mConCom[$nIndCon] = $xRCon;
	  						$mConCom[$nIndCon]['ctomovxx'] = $mConceptos[$j][2];
	  						$nIndCon++;
	  					}
	  				}
	  			}
	  			
	  			if (count($mConCom) != 1) {
	  				$nSwitch = 1;
	  				$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	  				$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] el Concepto [{$_POST['cCtoId'.($i+1)]}] no esta Parametrizado para el Comprobante [L-{$_POST['cComCod'.($i+1)]}] .\n";
	  			} else {	  				
	  				//Completando datos que se cargan cuando se selecciona el concepto contable	  				
	  				$_POST['cCtoDes'  .($i+1)] = $mConCom[0]['ctodesxl'];
  					$_POST['cComNit'  .($i+1)] = $mConCom[0]['ctonitxx'];
  					$_POST['cPucId'   .($i+1)] = $mConCom[0]['pucidxxx'];
  					$_POST['cPucDet'  .($i+1)] = $mConCom[0]['pucdetxx'];
  					$_POST['cPucTer'  .($i+1)] = $mConCom[0]['pucterxx'];
  					$_POST['nPucRet'  .($i+1)] = $mConCom[0]['pucretxx'];
  					$_POST['cPucNat'  .($i+1)] = $mConCom[0]['pucnatxx'];
  					$_POST['cPucInv'  .($i+1)] = $mConCom[0]['pucinvxx'];
  					$_POST['cPucCco'  .($i+1)] = $mConCom[0]['puccccxx'];
  					$_POST['cPucDoSc' .($i+1)] = $mConCom[0]['pucdoscc'];		                        
  					$_POST['cPucTipEj'.($i+1)] = $mConCom[0]['puctipej'];		                        
  					$_POST['cComVlr1' .($i+1)] = $mConCom[0]['ctovlr01'];
  					$_POST['cComVlr2' .($i+1)] = $mConCom[0]['ctovlr02'];
  					$_POST['cComMov'  .($i+1)] = $mConCom[0]['ctomovxx'];
  					$_POST['cCtoDtoC' .($i+1)] = $mConCom[0]['ctodtocl'];
  					$_POST['cCtoDOC'  .($i+1)] = $mConCom[0]['ctodocxl'];
  					$_POST['cCtoCtoC' .($i+1)] = $mConCom[0]['ctoctocl'];
  					$_POST['cCtoDSAC' .($i+1)] = $mConCom[0]['ctodsacl'];
  					$_POST['cCtoAnt'  .($i+1)] = $mConCom[0]['ctoantxl'];
  					$_POST['cCtoApl'  .($i+1)] = $mConCom[0]['ctoaplxl'];
  					$_POST['cCtoPTA'  .($i+1)] = $mConCom[0]['ctoptaxl'];
  					$_POST['cCtoVUCE' .($i+1)] = $mConCom[0]['ctopvxxl'];
  					$_POST['cDocInfV' .($i+1)] = $mConCom[0]['ctodocil'];
  					
  					if ($_POST['cSucId' .($i+1)] != "" && $_POST['cDocId' .($i+1)] != "" && $_POST['cDocSuf'.($i+1)] != "") {
  						//Validando que el do exista
  						$qDo  = "SELECT * ";
  						$qDo .= "FROM $cAlfa.sys00121 ";
  						$qDo .= "WHERE ";
  						$qDo .= "sucidxxx = \"{$_POST['cSucId' .($i+1)]}\" AND ";
  						$qDo .= "docidxxx = \"{$_POST['cDocId' .($i+1)]}\" AND ";
  						$qDo .= "docsufxx = \"{$_POST['cDocSuf'.($i+1)]}\" AND ";
  						$qDo .= "regestxx IN (\"ACTIVO\",\"FACTURADO\") LIMIT 0,1 ";
  						$xDo  = f_MySql("SELECT","",$qDo,$xConexion01,"");
  						$vDo  = mysql_fetch_array($xDo);
  						//f_Mensaje(__FILE__,__LINE__,$qDo." ~ ".mysql_num_rows($xDo));
  						if (mysql_num_rows($xDo) != 1) {
  							$nSwitch = 1;
  							$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  							$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] el DO [{$_POST['cSucId'.($i+1)]}-{$_POST['cDocId'.($i+1)]}-{$_POST['cDocSuf'.($i+1)]}] no Existe.\n";
  						} else {
  							if ($vDo['regestxx'] == "FACTURADO") {
  								$nSwitch = 1;
  								$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  								$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] el DO [{$_POST['cSucId'.($i+1)]}-{$_POST['cDocId'.($i+1)]}-{$_POST['cDocSuf'.($i+1)]}] ya fue Facturado.\n";
  							}
  										
  							if ($vDo['cliidxxx'] != $_POST['cTerId'.($i+1)]) {
  								$nSwitch = 1;
  								$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  								$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] el Tercero Cliente [{$_POST['cTerId'.($i+1)]}] No es el Importador del DO [{$_POST['cSucId'.($i+1)]}-{$_POST['cDocId'.($i+1)]}-{$_POST['cDocSuf'.($i+1)]}].\n";
  							}
  							
  							// Validando que exista el tercero cliente, este solo se valida si se digita DO
  							$qValTer  = "SELECT * ";
  							$qValTer .= "FROM $cAlfa.SIAI0150 ";
  							$qValTer .= "WHERE ";
  							$qValTer .= "CLIIDXXX = \"{$_POST['cTerId'.($i+1)]}\" AND ";
  							$qValTer .= "CLICLIXX = \"SI\" AND ";
  							$qValTer .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
  							$xValTer  = f_MySql("SELECT","",$qValTer,$xConexion01,"");
  							//f_Mensaje(__FILE__,__LINE__,$qValTer." ~ ".mysql_num_rows($xValTer));
  							if (mysql_num_rows($xValTer) != 1) {
  								$nSwitch = 1;
  								$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  								$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] No Existe el Tercero Cliente [{$_POST['cTerId'.($i+1)]} - {$_POST['cTerNom'.($i+1)]}].\n";
  							} else {
  								$_POST['cTerTip'.($i+1)] = "CLICLIXX";
  							}
							}
  					} else {
  						if ($_POST['cSucId' .($i+1)] != "" || $_POST['cDocId' .($i+1)] != "" || $_POST['cDocSuf'.($i+1)] != "") {
  							$nSwitch = 1;
  							$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  							$cMensaje .= "Para la Secuencia [{$_POST['cComSeq'.($i+1)]}] los Datos del DO estan Incompletos.\n";
  						}
  					}
  					
  					// Validando que exista el tercero beneficiario
  					$qTerPro  = "SELECT * ";
  					$qTerPro .= "FROM $cAlfa.SIAI0150 ";
  					$qTerPro .= "WHERE ";
  					$qTerPro .= "CLIIDXXX = \"{$_POST['cTerIdB'.($i+1)]}\" AND ";
  					$qTerPro .= "CLIPROCX = \"SI\" AND ";
  					$qTerPro .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
  					$xTerPro  = f_MySql("SELECT","",$qTerPro,$xConexion01,"");
	  				//f_Mensaje(__FILE__,__LINE__,$qTerPro." ~ ".mysql_num_rows($xTerPro));
  					if (mysql_num_rows($xTerPro) != 1) {
  						$nSwitch = 1;
  						$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  						$cMensaje .= "No Existe el Tercero Beneficiario [{$_POST['cTerIdB'.($i+1)]} - {$_POST['cTerNomB'.($i+1)]}].\n";
  					} else {
  						$_POST['cTerTipB'.($i+1)] = "CLIPROCX";
	  				}
  					
  					// Valido la observacion del comprobante.
  					if (strlen($_POST['cComObs'.($i+1)]) > $vSysStr['financiero_longitud_observaciones_grilla']) {
  						$nSwitch = 1;
  						$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  						$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] la longitud de la Observacion no Puede ser Mayor a ". $vSysStr['financiero_longitud_observaciones_grilla'] . " Caracteres.\n";
  					}
	  				
  					//Validando que la cuenta PUC del concepto detalla por DO o No Detalle
  					if (!f_InList($_POST['cPucDet' .($i+1)] ,"D","N")) {
  						$nSwitch = 1;
  						$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  						$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] la cuenta [{$_POST['cPucId'  .($i+1)]}] parametrizada para el Concepto [{$_POST['cCtoId'.($i+1)]}] debe Detalla por DO o No Detallar.\n";
  					}
  						
  					if ($_POST['cPucDet' .($i+1)] == "D" && ($_POST['cSucId' .($i+1)] == "" || $_POST['cDocId' .($i+1)] == "" || $_POST['cDocSuf'.($i+1)] == "")) {
  						$nSwitch = 1;
  						$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  						$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] la cuenta [{$_POST['cPucId'  .($i+1)]}] parametrizada para el Concepto [{$_POST['cCtoId'.($i+1)]}] Detalla por DO, debe Digitar los Datos de DO.\n";
  					}
  						
  					if ($_POST['cPucDoSc'.($i+1)] == "S" && ($_POST['cSucId' .($i+1)] == "" || $_POST['cDocId' .($i+1)] == "" || $_POST['cDocSuf'.($i+1)] == "")) {
  						$nSwitch = 1;
  						$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  						$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] la cuenta [{$_POST['cPucId'  .($i+1)]}] parametrizada para el Concepto [{$_POST['cCtoId'.($i+1)]}] Obliga Subcentro de Costo un DO, debe Digitar los Datos de DO.\n";
  					}
  					
  					// Validando el tipo de ejecucion de la cuenta
  					if (!($_POST['cPucTipEj'.($i+1)] == "L" || $_POST['cPucTipEj'.($i+1)] == "N" || $_POST['cPucTipEj'.($i+1)] == "")) {
  						$nSwitch = 1;
  						$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  						$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] en la cuenta [{$_POST['cPucId'  .($i+1)]}] parametrizada para el Concepto [{$_POST['cCtoId'.($i+1)]}] el Tipo de Ejecucion no es Valido, debe Ser LOCAL, NIIF o AMBAS.\n";
  					}
  					
  					//Completando datos que se cargan cuando se selecciona el documento cruece, en este caso un DO
  					if ($_POST['cPucDet' .($i+1)] == "D") {
	  					$_POST['cComIdC' .($i+1)] = $vDo['comidxxx'];
	  					$_POST['cComCodC'.($i+1)] = $vDo['comcodxx'];
	  					$_POST['cComCscC'.($i+1)] = $vDo['docidxxx'];
	  					$_POST['cComSeqC'.($i+1)] = $vDo['docsufxx'];
	  					$_POST['cCcoId'  .($i+1)] = $vDo['ccoidxxx'];
	  					$_POST['cSccId'  .($i+1)] = $vDo['docidxxx'];
	  					$_POST['cDosCscC'.($i+1)] = $vDo['docidxxx'];
	  					$_POST['cDosSeqC'.($i+1)] = $vDo['docsufxx'];
	  					$_POST['cSucId'  .($i+1)] = $vDo['sucidxxx'];
	  					$_POST['cDocId'  .($i+1)] = $vDo['docidxxx'];
	  					$_POST['cDocSuf' .($i+1)] = $vDo['docsufxx'];
	  					$_POST['cComCtoC'.($i+1)] = "";
  					
	  					if ($_POST['cComNit' .($i+1)] == "CLIENTE" && $_POST['cCtoDSAC'.($i+1)] != "SI") { // Nit para buscar el documento cruce
	  						$_POST['cTerTip' .($i+1)] = "CLICLIXX";
	  						$_POST['cTerId'  .($i+1)] = $_POST['cTerId'  .($i+1)];		      					
	  						$_POST['cTerNom' .($i+1)] = $_POST['cTerNom' .($i+1)];
	  						$_POST['cTerTipB'.($i+1)] = $_POST['cTerTipB'.($i+1)];
	  					  $_POST['cTerIdB' .($i+1)] = $_POST['cTerIdB' .($i+1)];
	  					  $_POST['cTerIdC' .($i+1)] = $_POST['cTerId'  .($i+1)];                    
	  					} else {
	  						$_POST['cTerTip' .($i+1)] = $_POST['cTerTip' .($i+1)];	
	  						$_POST['cTerId'  .($i+1)] = $_POST['cTerId'  .($i+1)];		      					
	  						$_POST['cTerNom' .($i+1)] = $_POST['cTerNom' .($i+1)];
	  						$_POST['cTerTipB'.($i+1)] = $_POST['cTerTip' .($i+1)];
	  					  $_POST['cTerIdB' .($i+1)] = $_POST['cTerId'  .($i+1)];
	  						$_POST['cTerIdC' .($i+1)] = $_POST['cTerId'  .($i+1)];                    
	  					}
	  														
	  					if ($_POST['cCtoDOC' .($i+1)] == "SI") { // Aplica DO Informativo
	  						$_POST['cDosIdC' .($i+1)] = $_POST['cDoiTip'.($i+1)];
	  						$_POST['cDosCodC'.($i+1)] = $_POST['cDoiCod'.($i+1)];
	  						$_POST['cDosCscC'.($i+1)] = $_POST['cDoiId' .($i+1)];
	  						$_POST['cDosSeqC'.($i+1)] = $_POST['cDoiSuf'.($i+1)];
	  						$_POST['cTerIdC' .($i+1)] = $_POST['cDoiCli'.($i+1)];
	  					}
	  					
	  					if ($_POST['cCtoCtoC'.($i+1)] == "SI") { // Aplica Concepto Informativo
	  						$_POST['cDosCto' .($i+1)] = $_POST['cDosCto'.($i+1)];
	  					} else {
	  						$_POST['cDosCto' .($i+1)] = "";
	  					}
	  						
	  					if ($_POST['cDocInfV'.($i+1)] == "SI") {
	  						if ($_POST['cDocInf'.($i+1)] == ""){
	  							$nSwitch = 1;
	  							$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	  							$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] la parametrizacion del concepto [{$_POST['cCtoId'.($i+1)]}] no permite que el campo Documento Informativo sea vacio.\n";
	  						}
	  					} else {
	  						if ($_POST['cDocInf'.($i+1)] != ""){
	  							$nSwitch = 1;
	  							$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	  							$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] la parametrizacion del concepto [{$_POST['cCtoId'.($i+1)]}] no permite que el campo Documento Informativo no permite que el campo Documento Informativo sea diferente de vacio.\n";
	  						}
	  					}
	  						
	  					$_POST['cComFac' .($i+1)] = "";
	  					
  					} else { //La Cuenta No Detalla
  						
  						$_POST['cTerTip' .($i+1)] = $_POST['cTerTipB'.($i+1)];
  						$_POST['cTerId'  .($i+1)] = $_POST['cTerIdB' .($i+1)];
  						$_POST['cTerTipB'.($i+1)] = $_POST['cTerTipB'.($i+1)];
  						$_POST['cTerIdB' .($i+1)] = $_POST['cTerIdB' .($i+1)];
  						
  						if ($_POST['cPucDoSc'.($i+1)] == "S") {
  							$_POST['cCcoId'  .($i+1)] = $vDo['ccoidxxx'];
  							$_POST['cSccId'  .($i+1)] = $vDo['docidxxx'];
  							$_POST['cSucId'  .($i+1)] = $vDo['sucidxxx'];
  							$_POST['cDocId'  .($i+1)] = $vDo['docidxxx'];
  							$_POST['cDocSuf' .($i+1)] = $vDo['docsufxx'];
  						} else {
	  						//El Subcentro de costo es igual al seleccionado en cabecera
	  						$_POST['cSccId'  .($i+1)] = $_POST['cSccId'];
  						}
  					}
  						
  					//Validando que si el concepto es de pago de tributos
  					if ($_POST['cCtoPTA' .($i+1)] == "SI") {
  						$nSwitch = 1;
  						$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  						$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] El Concepto [{$_POST['cCtoId'.($i+1)]}] - {$_POST['cCtoDes'.($i+1)]} Esta Parametrizado como Concepto Para Pago de Tributos Aduaneros.";
  						$nSwitch = 1;
  					}
  					
  					if ($_POST['cCtoDSAC'.($i+1)] == "SI" && $_POST['cTerTipB'.($i+1)] != "CLICLIXX") {
  						$nSwitch = 1;
  						$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  						$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] el Concepto Esta Parametrizado como [APLICA CLIENTE] pero el Tipo de Tercero es Diferente de Cliente.\n";
  					}
  					
  					//Se validan las retenciones e Ivas si el tipo de ejecuci�n es Local o Ambas
  					if ($_POST['cPucTipEj'.($i+1)] == "L" || $_POST['cPucTipEj'.($i+1)] == "") {
	  					if ($_POST['cComVlr1'.($i+1)] == "SI" || $_POST['cComVlr2'.($i+1)]  == "SI") {
	  						if ($_POST['nPucRet'.($i+1)] > 0) { // Es una retencion
	  							//Debe tener Digitada la Base
	  							if (($_POST['nComBase'.($i+1)]+0) == 0) {
	  								$nSwitch = 1;
	  								$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	  								$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] Debe Digitar la Base, para el concepto [{$_POST['cCtoId'.($i+1)]}] - {$_POST['cCtoDes'.($i+1)]} ";
	  								$cMensaje .= "aplica una Retencion del [{$_POST['nPucRet'.($i+1)]}%].\n";
	  							}
	  							
	  							//No debe tener Digitado Iva
	  							if (($_POST['nComIva'.($i+1)]+0) > 0) {
	  								$nSwitch = 1;
	  								$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	  								$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] No Debe Digitar IVA, el concepto [{$_POST['cCtoId'.($i+1)]}] - {$_POST['cCtoDes'.($i+1)]} ";
	  								$cMensaje .= "es un Concepto de Retencion.\n";
	  							}
	  					
	  							//Debe tener digitada la Retencion
	  							if (($_POST['nComRte'.($i+1)]+0) == 0) {
	  								$nSwitch = 1;
	  								$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	  								$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] Debe Digitar la Retencion, para el concepto [{$_POST['cCtoId'.($i+1)]}] - {$_POST['cCtoDes'.($i+1)]} ";
	  								$cMensaje .= "aplica una Retencion del [{$_POST['nPucRet'.($i+1)]}%].\n";
	  							}
	  							
	  							//La Retencion debe ser igual al calculo de la base por el porcentaje de retencion de la cuenta
	  							$nRetencion = ($_POST['nPucRet'.($i+1)]+0)/100;
	  							$nValorRete = round($_POST['nComBase'.($i+1)] * $nRetencion);
	  							
	  							if (($_POST['nComRte'.($i+1)]+0) != $nValorRete) {
	  								$nSwitch = 1;
	  								$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
										$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] el Valor de la Retencion [{$_POST['nComRte'.($i+1)]}] no es Igual ";
										$cMensaje .= "al valor [$nValorRete] del [".($_POST['nPucRet'.($i+1)]+0)."%] de la Base [{$_POST['nComBase'.($i+1)]}].\n";
	  							}
	  						
	  							
	  							//El valor total debe ser igual a la retencion
	  							if (($_POST['nComVlr'.($i+1)]+0) != ($_POST['nComRte'.($i+1)]+0)) {
	  								$nSwitch = 1;
	  								$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	  								$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] el Valor del Comprobante [{$_POST['nComVlr'.($i+1)]}] no es Igual ";
	  								$cMensaje .= "al Valor Retencion [{$_POST['nComRte'.($i+1)]}].\n";
	  							}
	  							
	  							$_POST['nComBRet' .($i+1)] = ($_POST['nComBase'.($i+1)]+0) ;// Base de Retencion
	  							$_POST['nComBIva' .($i+1)] = ""; // Base de Iva
	  							$_POST['nComIva'  .($i+1)] = ""; // Valor del Iva
	  							$_POST['nComVlr'  .($i+1)] = ($_POST['nComVlr'.($i+1)]+0); // Valor del Comprobante
	  					
	  						} else { // Es un IVA.
	  							//Debe tener Digitada la Base
	  							if (($_POST['nComBase'.($i+1)]+0) == 0) {
	  								$nSwitch = 1;
	  								$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	  								$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] Debe Digitar la Base, para el concepto [{$_POST['cCtoId'.($i+1)]}] - {$_POST['cCtoDes'.($i+1)]} ";
	  								$cMensaje .= "aplica IVA.\n";
	  							}
	  							
	  							//No debe tener digitada Retencion
	  							if (($_POST['nComRte'.($i+1)]+0) > 0) {
	  								$nSwitch = 1;
	  								$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	  								$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] No Debe Digitar Retencion, el concepto [{$_POST['cCtoId'.($i+1)]}] - {$_POST['cCtoDes'.($i+1)]} No es un Concepto de Retencion.\n";
	  							}
	  							
	  							//Debe tener digitado el IVA
	  							if (($_POST['nComIva'.($i+1)]+0) < 0) {
	  								$nSwitch = 1;
	  								$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	  								$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] Para el concepto [{$_POST['cCtoId'.($i+1)]}] - {$_POST['cCtoDes'.($i+1)]} debe Digitar el IVA.\n";
	  							}
	  							
	  							//El valor total debe ser igual a la base mas el iva
	  							if (($_POST['nComVlr'.($i+1)]+0) != ($_POST['nComBase'.($i+1)] + $_POST['nComIva'.($i+1)])) {
	  								$nSwitch = 1;
	  								$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	  								$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] el Valor del Comprobante debe Ser Igual a la Sumatoria de la Base mas el Iva.\n";
	  							}  
	
	  							$_POST['nComBRet' .($i+1)] = "";// Base de Retencion
	  							$_POST['nComBIva' .($i+1)] = ($_POST['nComBase'.($i+1)]+0); // Base de Iva
	  							$_POST['nComIva'  .($i+1)] = ($_POST['nComIva' .($i+1)]+0); // Valor del Iva
	  							$_POST['nComVlr'  .($i+1)] = ($_POST['nComVlr' .($i+1)]+0); // Valor del Comprobante
	  						}
	  					} else {
	  						//No debe digitar Base
	  						if (($_POST['nComBase'.($i+1)]+0) > 0) {
	  							$nSwitch = 1;
	  							$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	  							$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] para el concepto [{$_POST['cCtoId'.($i+1)]}] - {$_POST['cCtoDes'.($i+1)]} la Base debe ser Vacia o Igual a Cero.";
	  						}
	  						
	  						//No debe tener digitado IVA
	  						if (($_POST['nComIva'.($i+1)]+0) > 0) {
	  							$nSwitch = 1;
	  							$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	  							$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] para el concepto [{$_POST['cCtoId'.($i+1)]}] - {$_POST['cCtoDes'.($i+1)]} el IVA debe ser Vacio o Igual a Cero.";
	  						}
	  						
	  						//No debe tener digitado Retencion
	  						if (($_POST['nComRte'.($i+1)]+0) > 0) {
	  							$nSwitch = 1;
	  							$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	  							$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] para el concepto [{$_POST['cCtoId'.($i+1)]}] - {$_POST['cCtoDes'.($i+1)]} la Retencion debe ser Vacia o Igual a Cero.";
	  						}	
	  					}
  					} elseif ($_POST['cPucTipEj'.($i+1)] == "N") {
  						//No debe digitar Base
  						if (($_POST['nComBase'.($i+1)]+0) > 0) {
  							$nSwitch = 1;
  							$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  							$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] para el Tipo de Ejecucion NIIF la Base debe ser Vacia o Igual a Cero.";
  						}
  							
  						//No debe tener digitado IVA
  						if (($_POST['nComIva'.($i+1)]+0) > 0) {
  							$nSwitch = 1;
  							$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  							$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] para el Tipo de Ejecucion NIIF  el IVA debe ser Vacio o Igual a Cero.";
  						}
  							
  						//No debe tener digitado Retencion
  						if (($_POST['nComRte'.($i+1)]+0) > 0) {
  							$nSwitch = 1;
  							$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  							$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] para el Tipo de Ejecucion NIIF  la Retencion debe ser Vacia o Igual a Cero.";
  						}
  					}
  					
  					if ($_POST['cPucTipEj'.($i+1)] == "L" || $_POST['cPucTipEj'.($i+1)] == "") {
  						//Validando el valor del comprobante
  						if (($_POST['nComVlr'.($i+1)]+0) <= 0) {
  							$nSwitch = 1;
  							$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  							$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] el Valor Local del Comprobante debe ser Mayor a Cero.\n";
  						}
  						
  						if ($_POST['cPucTipEj'.($i+1)] == "L") {
  							//Validando que no haya valor NIIF
  							if (($_POST['nComVlrNF'.($i+1)]+0) > 0) {
  								$nSwitch = 1;
  								$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  								$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] el Valor NIIF del Comprobante debe ser Cero.\n";
  							}	
  						} else {
  							//Validando que haya valor NIIF
  							if (($_POST['nComVlrNF'.($i+1)]+0) <= 0) {
  								$nSwitch = 1;
  								$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  								$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] el Valor NIIF del Comprobante debe ser Mayor a Cero.\n";
  							}
  						}  						
  					} elseif ($_POST['cPucTipEj'.($i+1)] == "N") {
  						//Validando que haya valor NIIF
  						if (($_POST['nComVlrNF'.($i+1)]+0) <= 0) {
  							$nSwitch = 1;
  							$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  							$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] el Valor NIIF del Comprobante debe ser Mayor a Cero.\n";
  						}
  						//Validando que no haya valor Local
  						if (($_POST['nComVlr'.($i+1)]+0) > 0) {
  							$nSwitch = 1;
  							$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  							$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] el Valor Local del Comprobante debe ser Cero.\n";
  						}  						
  					} 
	  			}
	  			
	  			//Validando el Banco
	  			$qBanId  = "SELECT banidxxx ";
	  			$qBanId .= "FROM $cAlfa.fpar0124 ";
	  			$qBanId .= "WHERE ";
	  			$qBanId .= "banidxxx = \"{$_POST['cBanId'.($i+1)]}\" AND ";
	  			$qBanId .= "regestxx = \"ACTIVO\" LIMIT 0,1";
	  			$xBanId  = f_MySql("SELECT","",$qBanId,$xConexion01,"");
	  			//f_Mensaje(__FILE__,__LINE__,$qBanId." ~ ".mysql_num_rows($xBanId));
	  			if (mysql_num_rows($xBanId) != 1) {
	  				$nSwitch = 1;
	  				$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	  				$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] el ID del Banco [{$_POST['cBanId'.($i+1)]}] no esta Parametrizado.\n";
	  			}
	  				
	  			//Validando la Cta Corriente del Banco
	  			$qCtaBan  = "SELECT pucidxxx ";
	  			$qCtaBan .= "FROM $cAlfa.fpar0128 ";
	  			$qCtaBan .= "WHERE ";
	  			$qCtaBan .= "banidxxx = \"{$_POST['cBanId' .($i+1)]}\" AND ";
	  			$qCtaBan .= "banctaxx = \"{$_POST['cBanCta'.($i+1)]}\" AND ";
	  			$qCtaBan .= "regestxx = \"ACTIVO\" LIMIT 0,1";
	  			$xCtaBan  = f_MySql("SELECT","",$qCtaBan,$xConexion01,"");
	  			//f_Mensaje(__FILE__,__LINE__,$qCtaBan." ~ ".mysql_num_rows($xCtaBan));
	  			if (mysql_num_rows($xCtaBan) != 1) {
	  				$nSwitch = 1;
	  				$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	  				$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] la Cuenta [{$_POST['cBanCta'.($i+1)]}]  no esta Parametrizada para el Banco [{$_POST['cBanId'.($i+1)]}] .\n";
	  			} else {
	  				$vCtaBan  = mysql_fetch_array($xCtaBan);
	  				$_POST['cBanPuc'.($i+1)] = $vCtaBan['pucidxxx'];
	  			}
	  			
	  			//Si la variable General del Sistema financiero_aplica_documento_conciliacion_cartabco viene en SI, se hacen obligatorios los campos de Comprobante Bancario
	  			if($vSysStr['financiero_aplica_documento_conciliacion_cartabco'] == "SI"){
	  				if (!f_InList($_POST['cComTCB'.($i+1)],"CG","CH","ND","NC")) {
	  					$nSwitch = 1;
	  					$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	  					$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] el Comprobante Bancario debe ser [CG o CH o ND o NC].\n";
	  				}
	  			
	  				if($_POST['cComNCB'.($i+1)] == ""){
	  					$nSwitch = 1;
	  					$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	  					$cMensaje .= "En la Secuencia [{$_POST['cComSeq'.($i+1)]}] el Numero de Comprobante Bancario no puede ser Vacio.\n";
	  				}
	  			}
	  			
	  			//Todos los datos son validos
	  			if ($nSwitch == 0) {
	  				
	  				$nInd_mDatos = ($mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['nSecuencia'] == "") ? 1 : ($mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['nSecuencia'] + 1);
	  				
	  				//Secuencia
	  				$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['nSecuencia']   = $nInd_mDatos;
	  				
	  				//Datos de Cabecera
						if ($nInd_mDatos == 1) {
							$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cModo']   		 = "NUEVO"; //Accion (NUEVO,ANTERIOR,EDITAR,BORRAR)
							$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cConPro']      = $_POST['cConPro']; //Creacion de la contrapartida con un unico registro (NO) o por Proveedor (SI)
							$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['nTimesSave']   = 1;
							$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cComId']       = "L"; //Comprobante
							$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cComCod']      = $_POST['cComCod'  .($i+1)]; //Codigo
							$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cComTco']      = $_POST['cComTco'  .($i+1)]; //Tipo de Consecutivo para el comprobante (MANUAL/AUTOMATICO)
							$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cComCco']      = $_POST['cComCco'  .($i+1)]; //Control Consecutivo (MENSUAL/ANUAL/INDEFINIDO)
							$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['dComFec']      = $_POST['dComFec']; //Fecha
							$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['tRegHCre']	   = $_POST['tRegHCre']; //Hora Creacion del Comprobante
							$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cCcoId']       = $_POST['cCcoId']; //Centro de Costo seleccionado en la cabecera
							$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cSccId']       = $_POST['cSccId']; //Subcentro de Costo seleccionado en la cabecera
							$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cComAsu']      = $_POST['cComAsu']; //Asunto
							$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cTerTipB']     = $_POST['cTerTipB']; //Siempre es CLIPROCX
							$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cTerIdB']      = $_POST['cTerIdB']; //Girado a
							$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cComObs']      = $_POST['cComObs'  .($i+1)]; //Observacion
							$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cBanId']       = $_POST['cBanId'   .($i+1)]; //Banco	     
							$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cBanCta']      = $_POST['cBanCta'  .($i+1)]; //Cuenta Corriente	Banco		     
							$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cBanPuc']      = $_POST['cBanPuc'  .($i+1)]; //Cuenta PUC Banco				     
							$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cComTCB']      = $_POST['cComTCB'  .($i+1)]; //Tipo de Comprobante Bancario [Sistema Uno]
							$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cComNCB']      = $_POST['cComNCB'  .($i+1)]; //Numero de Comprobante Bancario [Sistema Uno]
							$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cComPet']	     = "NO"; //Pago Electronico Tributos Aduaneros siempre va en NO
							$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cComMemo']     = ""; //Declaraciones, para este caso va en blanco
							$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['nTasaCambio']  = $_POST['nTasaCambio']; //Tasa de Cambio
							$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cRegEst']      = "ACTIVO"; //Tasa de Cambio
						}
						
						//Sumatoria de las Bases y el IVA
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['nComVlr01']      += $_POST['nComBIva'.($i+1)]; //Sumatoria de las bases
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['nComVlr02']      += $_POST['nComIva' .($i+1)]; //Sumatoria del Iva
						
						//Detalle
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cComSeq'  . $nInd_mDatos] = str_pad($nInd_mDatos,3,"0",STR_PAD_LEFT); // Secuencia
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cComSeqE' . $nInd_mDatos] = str_pad(($i+1),3,"0",STR_PAD_LEFT); 			// Equivalencia en la grilla del proceso de cartas bancarias automaticas
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cCtoId'   . $nInd_mDatos] = $_POST['cCtoId'   .($i+1)]; // Id del Concepto
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cCtoDes'  . $nInd_mDatos] = $_POST['cCtoDes'  .($i+1)]; // Descripcion del Concepto
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cComObs'  . $nInd_mDatos] = $_POST['cComObs'  .($i+1)]; // Observacion del Comprobante
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cComIdC'  . $nInd_mDatos] = $_POST['cComIdC'  .($i+1)]; // Id Comprobante Cruce
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cComCodC' . $nInd_mDatos] = $_POST['cComCodC' .($i+1)]; // Codigo Comprobante Cruce
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cComCscC' . $nInd_mDatos] = $_POST['cComCscC' .($i+1)]; // Consecutivo Comprobante Cruce
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cComSeqC' . $nInd_mDatos] = $_POST['cComSeqC' .($i+1)]; // Secuencia Comprobante Cruce
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cCcoId'   . $nInd_mDatos] = $_POST['cCcoId'   .($i+1)]; // Centro de Costos
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cSccId'   . $nInd_mDatos] = $_POST['cSccId'   .($i+1)]; // Sub Centro de Costos
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cDocInf'  . $nInd_mDatos] = $_POST['cDocInf'  .($i+1)]; // Documento Informativo Aduanera Grancolombiana
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cComCtoC' . $nInd_mDatos] = $_POST['cComCtoC' .($i+1)]; // Concepto Comprobante Cruce
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cDosIdC'  . $nInd_mDatos] = $_POST['cDosIdC'  .($i+1)]; // Hidden DO Cruce - Id del Comprobante
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cDosCodC' . $nInd_mDatos] = $_POST['cDosCodC' .($i+1)]; // Hidden DO Cruce - Codigo del Comprobante
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cDosCscC' . $nInd_mDatos] = $_POST['cDosCscC' .($i+1)]; // Hidden DO Cruce - Consecutivo del Comprobante
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cDosSeqC' . $nInd_mDatos] = $_POST['cDosSeqC' .($i+1)]; // Hidden DO Cruce - Secuencia del Comprobante
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cDosCto'  . $nInd_mDatos] = $_POST['cDosCto'  .($i+1)]; // Hidden DO Cruce - Concepto Contable
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['nComBRet' . $nInd_mDatos] = $_POST['nComBRet' .($i+1)]; // Base de Retencion
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['nComBIva' . $nInd_mDatos] = $_POST['nComBIva' .($i+1)]; // Base de Iva
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['nComIva'  . $nInd_mDatos] = $_POST['nComIva'  .($i+1)]; // Valor del Iva
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['nComVlr'  . $nInd_mDatos] = $_POST['nComVlr'  .($i+1)]; // Valor Local del Comprobante
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['nComVlrNF'. $nInd_mDatos] = $_POST['nComVlrNF'.($i+1)]; // Valor NIIF del Comprobante
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cComMov'  . $nInd_mDatos] = $_POST['cComMov'  .($i+1)]; // Movimiento Debito o Credito
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cComNit'  . $nInd_mDatos] = $_POST['cComNit'  .($i+1)]; // Hidden (Nit que va para SIIGO)
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cTerTip'  . $nInd_mDatos] = $_POST['cTerTip'  .($i+1)]; // Hidden (Tipo de Tercero) - Siempre es CLICLIXX 
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cTerId'   . $nInd_mDatos] = $_POST['cTerId'   .($i+1)]; // Hidden (Id del Tercero)
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cTerNom'  . $nInd_mDatos] = $_POST['cTerNom'  .($i+1)]; // Hidden (Nombre del Tercero)
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cTerTipB' . $nInd_mDatos] = $_POST['cTerTipB' .($i+1)]; // Hidden (Tipo de Tercero Dos) - Siempre es CLIPROCX
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cTerIdB'  . $nInd_mDatos] = $_POST['cTerIdB'  .($i+1)]; // Hidden (Id del Tercero Dos)
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cTerTipC' . $nInd_mDatos] = $_POST['cTerTipC' .($i+1)]; // Hidden (Tipo de Tercero DO Informativo)
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cTerIdC'  . $nInd_mDatos] = $_POST['cTerIdC'  .($i+1)]; // Hidden (Id del Tercero DO Informativo)			  			  
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cPucId'   . $nInd_mDatos] = $_POST['cPucId'   .($i+1)]; // Hidden (La Cuenta Contable)
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cPucDet'  . $nInd_mDatos] = $_POST['cPucDet'  .($i+1)]; // Hidden (Detalle de la Cuenta)
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cPucTer'  . $nInd_mDatos] = $_POST['cPucTer'  .($i+1)]; // Hidden (Cuenta de Terceros?)
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['nPucRet'  . $nInd_mDatos] = $_POST['nPucRet'  .($i+1)]; // Hidden (Porcentaje de Retencion de la Cuenta)
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cPucNat'  . $nInd_mDatos] = $_POST['cPucNat'  .($i+1)]; // Hidden (Naturaleza de la Cuenta)
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cPucInv'  . $nInd_mDatos] = $_POST['cPucInv'  .($i+1)]; // Hidden (Cuenta de Inventarios?)
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cPucCco'  . $nInd_mDatos] = $_POST['cPucCco'  .($i+1)]; // Hidden (Aplica Centro de Costo para esta Cuenta?)
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cPucDoSc' . $nInd_mDatos] = $_POST['cPucDoSc' .($i+1)]; // Hidden (Aplica DO para Subcentro de Costo?)			  
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cPucTipEj'. $nInd_mDatos] = $_POST['cPucTipEj'.($i+1)]; // Hidden (Tipo de Ejecucion(L-Local,N-Niif,vacio-Ambas))			  
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cComVlr1' . $nInd_mDatos] = $_POST['cComVlr1' .($i+1)]; // Hidden (Valor Uno)
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cComVlr2' . $nInd_mDatos] = $_POST['cComVlr2' .($i+1)]; // Hidden (Valor Dos)
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cComFac'  . $nInd_mDatos] = $_POST['cComFac'  .($i+1)]; // Hidden (Comfac)
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cSucId'   . $nInd_mDatos] = $_POST['cSucId'   .($i+1)]; // Hidden (Sucursal)
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cDocId'   . $nInd_mDatos] = $_POST['cDocId'   .($i+1)]; // Hidden (Do)
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cDocSuf'  . $nInd_mDatos] = $_POST['cDocSuf'  .($i+1)]; // Hidden (Sufijo)
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cDecxDO'  . $nInd_mDatos] = ""; // Hidden Declaraciones por DO, en este caso va vacio porque no se van a pagar tributos
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cCtoDtoC' . $nInd_mDatos] = $_POST['cCtoDtoC' .($i+1)]; // Hidden Aplica Documento Cruce
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cCtoDOC'  . $nInd_mDatos] = $_POST['cCtoDOC'  .($i+1)]; // Hidden Aplica DO Informativo
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cCtoCtoC' . $nInd_mDatos] = $_POST['cCtoCtoC' .($i+1)]; // Hidden Aplica Concepto Informativo
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cCtoTF'   . $nInd_mDatos] = $_POST['cCtoTF'   .($i+1)]; // Hidden Es un Concepto de Transferencia de Fondos, vacio porque aplica para las G, pero esta en el estandar de la grilla
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cCtoDSAC' . $nInd_mDatos] = $_POST['cCtoDSAC' .($i+1)]; // Hidden Es un Concepto de Devolucion de Saldos a Favor del Cliente
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cCtoAnt'  . $nInd_mDatos] = $_POST['cCtoAnt'  .($i+1)]; // Hidden Es un Concepto de Anticipos
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cCtoApl'  . $nInd_mDatos] = $_POST['cCtoApl'  .($i+1)]; // Hidden Para que Tipo de Terceros Aplica
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cCtoPTA'  . $nInd_mDatos] = $_POST['cCtoPTA'  .($i+1)]; // Hidden Es un Concepto para Pago de Tributos Aduaneros
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cCtoVUCE' . $nInd_mDatos] = $_POST['cCtoVUCE' .($i+1)]; // Hidden Es un Concepto para Pago VUCE
						$mDatos["{$_POST['cComCod'.($i+1)]}"]["{$_POST['cBanId'.($i+1)]}"]["{$_POST['cBanCta'.($i+1)]}"]['cDocInfV' . $nInd_mDatos] = $_POST['cDocInfV' .($i+1)]; // Hidden Esta variable se utiliza para activar la casilla de Documento Informativo Aduanera Grancolombiana
	  				
	  			}
				}
			}			
		break;
		default:
		break;
	}
	
	if ($nSwitch == 0) {
		switch ($_COOKIE['kModo']) {
			case "SUBIR": ?>
					<script languaje = "javascript">
						parent.fmwork.f_Delete_Row_All();
					</script>
				<?php		
				$nId = "";		
				for ($nD=0; $nD<count($mDatos); $nD++) {  ?>
					<script languaje = "javascript">
						parent.fmwork.f_Add_New_Row_Comprobante();
						var nSecuencia = parent.fmwork.document.forms['frgrm']['nSecuencia'].value;
						parent.fmwork.document.forms['frgrm']['cCcoId'   +nSecuencia].value = "<?php echo strtoupper(trim($mDatos[$nD]['ccoidxxx'])) ?>";
						parent.fmwork.document.forms['frgrm']['cSucId'   +nSecuencia].value = "<?php echo strtoupper(trim($mDatos[$nD]['sucidxxx'])) ?>";
						parent.fmwork.document.forms['frgrm']['cDocId'   +nSecuencia].value = "<?php echo strtoupper(trim($mDatos[$nD]['docidxxx'])) ?>";
						parent.fmwork.document.forms['frgrm']['cDocSuf'  +nSecuencia].value = "<?php echo strtoupper(trim($mDatos[$nD]['docsufxx'])) ?>";
						parent.fmwork.document.forms['frgrm']['cTerId'   +nSecuencia].value = "<?php echo strtoupper(trim($mDatos[$nD]['teridxxx'])) ?>";
						parent.fmwork.document.forms['frgrm']['cTerNom'  +nSecuencia].value = "<?php echo strtoupper(trim($mDatos[$nD]['ternomxx'])) ?>";
						parent.fmwork.document.forms['frgrm']['cTerIdB'  +nSecuencia].value = "<?php echo strtoupper(trim($mDatos[$nD]['terid2xx'])) ?>";
						parent.fmwork.document.forms['frgrm']['cTerNomB' +nSecuencia].value = "<?php echo strtoupper(trim($mDatos[$nD]['ternom2x'])) ?>";
						parent.fmwork.document.forms['frgrm']['cComObs'  +nSecuencia].value = "<?php echo strtoupper(trim($mDatos[$nD]['comobsxx'])) ?>";
						parent.fmwork.document.forms['frgrm']['cDocInf'  +nSecuencia].value = "<?php echo strtoupper(trim($mDatos[$nD]['comdocin'])) ?>";
						parent.fmwork.document.forms['frgrm']['cCtoId'   +nSecuencia].value = "<?php echo strtoupper(trim($mDatos[$nD]['ctoidxxx'])) ?>";
						parent.fmwork.document.forms['frgrm']['cCtoDes'  +nSecuencia].value = "<?php echo $mDatos[$nD]['ctodesxl'] ?>";							
						parent.fmwork.document.forms['frgrm']['nComBase' +nSecuencia].value = "<?php echo ($mDatos[$nD]['comvlr01'] > 0) ? $mDatos[$nD]['comvlr01']+0 : "" ?>";
						parent.fmwork.document.forms['frgrm']['nComIva'  +nSecuencia].value = "<?php echo ($mDatos[$nD]['comvlr02'] > 0) ? $mDatos[$nD]['comvlr02']+0 : "" ?>";
						parent.fmwork.document.forms['frgrm']['nComRte'  +nSecuencia].value = "<?php echo ($mDatos[$nD]['comvlr03'] > 0) ? $mDatos[$nD]['comvlr03']+0 : "" ?>";
						parent.fmwork.document.forms['frgrm']['nComVlr'  +nSecuencia].value = "<?php echo ($mDatos[$nD]['comvlrxx'] > 0) ? $mDatos[$nD]['comvlrxx']+0 : "" ?>";
						parent.fmwork.document.forms['frgrm']['nComVlrNF'+nSecuencia].value = "<?php echo ($mDatos[$nD]['comvlrnf'] > 0) ? $mDatos[$nD]['comvlrnf']+0 : "" ?>";
						parent.fmwork.document.forms['frgrm']['cBanId'   +nSecuencia].value = "<?php echo strtoupper(trim($mDatos[$nD]['banidxxx'])) ?>";
						parent.fmwork.document.forms['frgrm']['cBanCta'  +nSecuencia].value = "<?php echo strtoupper(trim($mDatos[$nD]['banctaxx'])) ?>";
						parent.fmwork.document.forms['frgrm']['cComTCB'  +nSecuencia].value = "<?php echo strtoupper(trim($mDatos[$nD]['comtcbxx'])) ?>";
						parent.fmwork.document.forms['frgrm']['cComNCB'  +nSecuencia].value = "<?php echo strtoupper(trim($mDatos[$nD]['comncbxx'])) ?>";
						parent.fmwork.document.forms['frgrm']['cComCod'  +nSecuencia].value = "<?php echo strtoupper(trim($mDatos[$nD]['comcodxx'])) ?>";

						//El concepto Aplica DO Informativo
						if ("<?php echo $mDatos[$nD]['ctodocxl'] ?>" == "SI") {	
							parent.fmwork.document.forms['frgrm']['cDoiTip'  +nSecuencia].value = "<?php echo $mDatos[$nD]['comiddox'] ?>";
							parent.fmwork.document.forms['frgrm']['cDoiCod'  +nSecuencia].value = "<?php echo $mDatos[$nD]['comcoddo'] ?>";
							parent.fmwork.document.forms['frgrm']['cDoiSuc'  +nSecuencia].value = "<?php echo $mDatos[$nD]['sucidxxx'] ?>";
							parent.fmwork.document.forms['frgrm']['cDoiId'   +nSecuencia].value = "<?php echo $mDatos[$nD]['docidxxx'] ?>";
							parent.fmwork.document.forms['frgrm']['cDoiId'   +nSecuencia].id    = "<?php echo $mDatos[$nD]['docidxxx'] ?>";
							parent.fmwork.document.forms['frgrm']['cDoiSuf'  +nSecuencia].value = "<?php echo $mDatos[$nD]['docsufxx'] ?>";
							parent.fmwork.document.forms['frgrm']['cDoiCli'  +nSecuencia].value = "<?php echo $mDatos[$nD]['cliiddox'] ?>";
							parent.fmwork.document.forms['frgrm']['cDoiNom'  +nSecuencia].value = "<?php echo $mDatos[$nD]['clinomdo'] ?>";								
							parent.fmwork.document.forms['frgrm']['cDoiId'   +nSecuencia].disabled = false;	
						} else {
							parent.fmwork.document.forms['frgrm']['cDoiTip'  +nSecuencia].value = "";
							parent.fmwork.document.forms['frgrm']['cDoiCod'  +nSecuencia].value = "";
							parent.fmwork.document.forms['frgrm']['cDoiSuc'  +nSecuencia].value = "";
							parent.fmwork.document.forms['frgrm']['cDoiId'   +nSecuencia].value = "";
							parent.fmwork.document.forms['frgrm']['cDoiId'   +nSecuencia].id    = "";
							parent.fmwork.document.forms['frgrm']['cDoiSuf'  +nSecuencia].value = "";
							parent.fmwork.document.forms['frgrm']['cDoiCli'  +nSecuencia].value = "";
							parent.fmwork.document.forms['frgrm']['cDoiNom'  +nSecuencia].value = "";								
							parent.fmwork.document.forms['frgrm']['cDoiId'   +nSecuencia].onblur   = "";								
							parent.fmwork.document.forms['frgrm']['cDoiId'   +nSecuencia].disabled = true;
						}

						//Aplica Concepto Infomativo
						if ("<?php echo $mDatos[$nD]['ctoctocl'] ?>" == "SI") {
							parent.fmwork.document.forms['frgrm']['cDosCto'  +nSecuencia].value = "<?php echo strtoupper(trim($mDatos[$nD]['ctoidxxx'])) ?>";
							parent.fmwork.document.forms['frgrm']['cDosCto'  +nSecuencia].id    = "<?php echo strtoupper(trim($mDatos[$nD]['ctoidxxx'])) ?>";
							parent.fmwork.document.forms['frgrm']['cDosCto'  +nSecuencia].disabled = false;
						} else {
							parent.fmwork.document.forms['frgrm']['cDosCto'  +nSecuencia].value = "";
							parent.fmwork.document.forms['frgrm']['cDosCto'  +nSecuencia].id    = "";
							parent.fmwork.document.forms['frgrm']['cDosCto'  +nSecuencia].onblur   = "";								
							parent.fmwork.document.forms['frgrm']['cDosCto'  +nSecuencia].disabled = true;
						}		

						//aplica documento informativo
						if ("<?php echo $mDatos[$nD]['ctodocil'] ?>" == "SI") {
							parent.fmwork.document.forms['frgrm']['cDocInf'  +nSecuencia].value = "<?php echo strtoupper(trim($mDatos[$nD]['comdocin'])) ?>";
							parent.fmwork.document.forms['frgrm']['cDocInf'  +nSecuencia].disabled = false;
						} else {
							parent.fmwork.document.forms['frgrm']['cDocInf'  +nSecuencia].value = "";
							parent.fmwork.document.forms['frgrm']['cDocInf'  +nSecuencia].disabled = true;
						}

						//Segun el tipo de ejecucion Local, ambas o NIIF se habilitan los valores, las bases y retenciones
						if ("<?php echo $mDatos[$nD]['puctipej'] ?>" == "L" || "<?php echo $mDatos[$nD]['puctipej'] ?>" == "") {
							if ("<?php echo $mDatos[$nD]['puctipej'] ?>" == "L") {
								//Tipo de ejecucion Local inhabilita campo valor NIIF
								parent.fmwork.document.forms['frgrm']['nComVlrNF'  +nSecuencia].disabled = true;
								parent.fmwork.document.forms['frgrm']['nComVlrNF'  +nSecuencia].value    = "";
							} else {
								//Tipo de ejecucion AMBAS habilita campo valor NIIF
								parent.fmwork.document.forms['frgrm']['nComVlrNF'  +nSecuencia].disabled = false;
							}
							//Habilitacion de los valores y bases
							if ("<?php echo $mDatos[$nD]['ctovlr01'] ?>" == "SI" || "<?php echo $mDatos[$nD]['ctovlr02'] ?>" == "SI") {
		  					if ("<?php echo $mDatos[$nD]['pucretxx'] ?>" > 0) { // Es una retencion
		  						parent.fmwork.document.forms['frgrm']['nComIva'  +nSecuencia].disabled = true; 
		  						parent.fmwork.document.forms['frgrm']['nComIva'  +nSecuencia].value    = "";
		  						parent.fmwork.document.forms['frgrm']['nComRte'  +nSecuencia].disabled = false;
		  					} else { // Es un IVA.
		  						parent.fmwork.document.forms['frgrm']['nComRte'  +nSecuencia].disabled = true; 
		  						parent.fmwork.document.forms['frgrm']['nComRte'  +nSecuencia].value    = "";
		  						parent.fmwork.document.forms['frgrm']['nComIva'  +nSecuencia].disabled = false;
		  					}
		  				} else {
		  					parent.fmwork.document.forms['frgrm']['nComIva'  +nSecuencia].disabled = true; 
		  					parent.fmwork.document.forms['frgrm']['nComIva'  +nSecuencia].value    = "";
		  					parent.fmwork.document.forms['frgrm']['nComRte'  +nSecuencia].disabled = true; 
		  					parent.fmwork.document.forms['frgrm']['nComRte'  +nSecuencia].value    = "";
		  					parent.fmwork.document.forms['frgrm']['nComBase' +nSecuencia].disabled = true; 
		  					parent.fmwork.document.forms['frgrm']['nComBase' +nSecuencia].value    = "";
		  				}
						} else if("<?php echo $mDatos[$nD]['puctipej'] ?>" == "N") {
							//Tipo de ejecucion NIIF inhabilita campo valor Local, bases y retenciones
							parent.fmwork.document.forms['frgrm']['nComVlr'  +nSecuencia].disabled = true;
							parent.fmwork.document.forms['frgrm']['nComVlr'  +nSecuencia].value    = "";
							parent.fmwork.document.forms['frgrm']['nComIva'  +nSecuencia].disabled = true; 
		  				parent.fmwork.document.forms['frgrm']['nComIva'  +nSecuencia].value    = "";
		  				parent.fmwork.document.forms['frgrm']['nComRte'  +nSecuencia].disabled = true; 
		  				parent.fmwork.document.forms['frgrm']['nComRte'  +nSecuencia].value    = "";
		  				parent.fmwork.document.forms['frgrm']['nComBase' +nSecuencia].disabled = true; 
		  				parent.fmwork.document.forms['frgrm']['nComBase' +nSecuencia].value    = "";
						}
					</script>
				<?php }				
			break;
			case "NUEVO":
				$cErrGen  = "";  //Texto Para Errores Generales
				$cErrOk   = "";  //Texto Para Errores Generales
				
				//Enviando los comprobantes a guardar
				foreach ($mDatos as $cKey => $mDatos01) { //Primera agrupacion por Codigo de Comprobante
					foreach ($mDatos01 as $cKey01 => $mDatos02) { //Segunda agrupacion por Banco
						$mAgruparxProveedor = array();
						foreach ($mDatos02 as $cKey02 => $mDatos03) { //Tercera agrupacion por Cuenta Cte Banco
							#Creando Carta Bancaria
							#Creando la instancia para la creacion de cartas bancarias
							$ObjCartaBancaria= new cCartasBancarias();
							$mRetorna = $ObjCartaBancaria->fnGuardarCartaBancaria($mDatos03);
								
							if($mRetorna[0] == "false") {
								$cErrGen  .= ($cErrGen != "") ? "\n" : "";
								$cErrGen  .= "Comprobante [{$mDatos03['cComId']}-{$mDatos03['cComCod']}], Banco [{$mDatos03['cBanId']}] y Cta Corriente [{$mDatos03['cBanCta']}]:\n\n";
								
								for ($i=1; $i<count($mRetorna); $i++) {
									$mAuxText = explode("~",$mRetorna[$i]);
									$cErrGen .= "Linea ".str_pad($mAuxText[0],4,"0",STR_PAD_LEFT).": ";
									$cErrGen .= $mAuxText[1]."\n";
								}
							} else {
								for ($i=1; $i<count($mRetorna); $i++) {
									$mAuxText = explode("~",$mRetorna[$i]);
									$cErrOk .= "Linea ".str_pad($mAuxText[0],4,"0",STR_PAD_LEFT).": ";
									$cErrOk .= $mAuxText[1]."\n";
								}
							}														
						}
					}
				}
				
				if ($cErrOk != "") {
					$cMensaje .= "Se Crearon con Exito las Siguientes Cartas Bancarias Automaticas:\n\n".$cErrOk;
				}
				
				if ($cErrGen != "") {
					$cMensaje .= "Se Presentaron Los Siguientes Errores en la Creacion de la Carta Bancaria Automatica:\n\n".$cErrGen;
				}
				
				if ($cErrGen !="" && $cErrOk == "") {
					//Se presentaron solo errores y no se guardo ningun comprobante
					$nSwitch = 1;
				}
			break;
			default:
				//No hace Nada
			break;
		}
	}
	
	if ($nSwitch == 1) {
		switch ($_COOKIE['kModo']) {
			case "SUBIR":
				$cMensaje = (($cMensaje != "") ? $cMensaje."\n": "")."Se Presentaron Errores al Subir el Archivo, el Proceso No Puede Continuar.";
	
				$cTexto = "";
				if ($cErrGen != "") {
					$cTexto  = '<br><span style="font-weight:bold">Se Presentaron los Siguiente Errores:</span><br><br>';
					$cTexto .= '<table border="1" cellpadding="0" cellspacing="0" width="400px">';
						$cTexto .= '<tr bgcolor = "#D6DFF7">';
							$cTexto .= '<td width = "50px" class="name"><center>Linea</center></td>';
							$cTexto .= '<td class="name"><center>Error</center></td>';
						$cTexto .= '</tr>';
						$cTexto .= $cErrGen;
					$cTexto .= '</table>';
				} ?>
				<script languaje = "javascript">
					parent.fmwork.document.getElementById('tblErr').innerHTML = '<?php echo str_replace($cBuscar,$cReempl,$cTexto); ?>';
					parent.fmwork.document.forms['frgrm']['nTimesSave'].value = 0;
					parent.fmwork.document.forms['frgrm']['Btn_Subir'].disabled = false;
					parent.fmwork.document.getElementById('Btn_Subir').style.display = "none";
					parent.fmwork.f_Delete_Row_All();
				</script>
			<?php break;
			break;
			default:
				//No hace nada
			break;
		}
		f_Mensaje(__FILE__,__LINE__,$cMensaje."Verifique"); ?>
		<script languaje = "javascript">
			parent.fmwork.document.forms['frgrm']['nTimesSave'].value = 0;
		 	parent.fmwork.document.forms['frgrm']['Btn_Guardar'].disabled = false;			
		 	parent.fmwork.document.forms['frgrm']['Btn_Subir'].disabled = false;			
		</script>
	<?php } 
	
	if ($nSwitch == 0) {
		switch ($_COOKIE['kModo']) {
			case "SUBIR": ?>
				<script languaje = "javascript">
					parent.fmwork.document.getElementById('tblArchivo').style.display = "none";
					parent.fmwork.document.getElementById('tblDatos').style.display   = "block";		
					parent.fmwork.document.forms['frgrm']['nTimesSave'].value = 0;
				 	parent.fmwork.document.forms['frgrm']['Btn_Guardar'].disabled = false;
				 	parent.fmwork.document.forms['frgrm']['cPaso'].value = "NUEVO";			
				</script>
			<?php break;
			case "NUEVO": 
				f_Mensaje(__FILE__,__LINE__,$cMensaje); ?>
				<form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
				<script languaje = "javascript">
					document.forms['frgrm'].submit();
				</script>
			<?php break;
			default:
				//No Hace Nada
			break;
		}
	} ?>
		
	<?php  
	function fnCadenaAleatoria($pLength = 8) {
		$cCaracteres = "abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ";
	  $nCaracteres = strlen($cCaracteres);
	  $cResult = "";
	  for ($x=0;$x< $pLength;$x++) {
	  	$nIndex = mt_rand(0,$nCaracteres - 1);
	    $cResult .= $cCaracteres[$nIndex];
	  }
	  return $cResult;
	} ?>
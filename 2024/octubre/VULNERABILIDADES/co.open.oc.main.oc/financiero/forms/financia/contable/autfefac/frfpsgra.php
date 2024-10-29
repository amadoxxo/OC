<?php
  namespace openComex;
	/**
	 * Graba Cambio de Fecha Prefacturas Sin Legalizar.
	 * --- Descripcion: Permite Guardar el Cambio de Fecha de las Facturas Sin Legalizar.
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@open-eb.co>
	 * @package Opencomex
	 */

	include("../../../../../libs/php/utility.php");
	include("../../../../../libs/php/utiindi.php");

	$nSwitch = 0; // Switch para Vericar la Validacion de Datos
	$nError  = 0; // Switch para Vericar la Validacion de Datos
	$cMsj    = "\n";

	switch ($_COOKIE['kModo']) {
	  case "CAMBIOFECHAPFSL":
			/***** Validando que llegue la fecha nueva *****/
      if ($_POST['dFecNue'] == "" || $_POST['dFecNue'] == "0000-00-00") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Debe Seleccionar una fecha nueva.\n";
      }

			/***** Validando que la observacion no sea vacia *****/
      if ($_POST['cObserv'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Debe Ingresar una Observacion.\n";
      }

      #Que la tabla (anio) de la nueva fecha exista en el sistema.
      $cPerAnoNew = substr($_POST['dFecNue'],0,4);
      $qVerTab  = "SELECT comidxxx FROM $cAlfa.fcoc$cPerAnoNew LIMIT 0,1";
      $xVerTab  = f_MySql("SELECT","",$qVerTab,$xConexion01,"");
      //f_Mensaje(__FILE__,__LINE__,$qVerTab."~".mysql_num_rows($xVerTab));
      if (!$xVerTab) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "No Existe la Tabla de Movimiento Contable para la Nueva Fecha [$cPerAnoNew].\n";
      }

      #Validando que exista la tabla del anio seleccionado
      $cPerAnio = $_POST['cPreAnio'];
      $qVerTab  = "SELECT COUNT(comidxxx) FROM $cAlfa.fcoc$cPerAnio LIMIT 0,1";
      $xVerTab  = f_MySql("SELECT","",$qVerTab,$xConexion01,"");
      //f_Mensaje(__FILE__,__LINE__,$qVerTab."~".mysql_num_rows($xVerTab));
      if (!$xVerTab) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "No Existe la Tabla de Movimiento Contable para el Anio [$cPerAnio].\n";
      }

  		if ($nSwitch == 0) {
        $mPreFact = array();
        for ($i=0; $i<$_POST['nSecuencia']; $i++) {
          if ($_POST['cComId' .($i+1)] != "" && $_POST['cComCod' .($i+1)] != "" && $_POST['cComCsc' .($i+1)] != "" && $_POST['cComCsc2' .($i+1)] != "") {

            #Busco el comprobante y realizo las validaciones correspondientes al proceso  
            #Que la factura exista en la tabla (anio) correspondiente.
            $qFacDat  = "SELECT ";
            $qFacDat .= "$cAlfa.fcoc$cPerAnio.comidxxx, ";
            $qFacDat .= "$cAlfa.fcoc$cPerAnio.comcodxx, ";
            $qFacDat .= "$cAlfa.fcoc$cPerAnio.comcscxx, ";
            $qFacDat .= "$cAlfa.fcoc$cPerAnio.comcsc2x, ";
            $qFacDat .= "$cAlfa.fcoc$cPerAnio.teridxxx, ";
						$qFacDat .= "$cAlfa.fcoc$cPerAnio.comfecxx, ";
						$qFacDat .= "$cAlfa.fcoc$cPerAnio.comfecve, ";
            $qFacDat .= "$cAlfa.fcoc$cPerAnio.comobs2x, ";
            $qFacDat .= "$cAlfa.fcoc$cPerAnio.comfpxxx, ";
            $qFacDat .= "$cAlfa.fcoc$cPerAnio.commemod, ";
            $qFacDat .= "$cAlfa.fcoc$cPerAnio.regestxx  ";
            $qFacDat .= "FROM $cAlfa.fcoc$cPerAnio ";
            $qFacDat .= "WHERE ";
            $qFacDat .= "$cAlfa.fcoc$cPerAnio.comidxxx = \"{$_POST['cComId' .($i+1)]}\" AND ";											
            $qFacDat .= "$cAlfa.fcoc$cPerAnio.comcodxx = \"{$_POST['cComCod' .($i+1)]}\" AND ";											
            $qFacDat .= "$cAlfa.fcoc$cPerAnio.comcscxx = \"{$_POST['cComCsc' .($i+1)]}\" AND ";											
            $qFacDat .= "$cAlfa.fcoc$cPerAnio.comcsc2x = \"{$_POST['cComCsc2' .($i+1)]}\" AND ";											
            $qFacDat .= "$cAlfa.fcoc$cPerAnio.regestxx = \"PROVISIONAL\" ";
            $qFacDat .= "LIMIT 0,1 ";
            $xFacDat = f_MySql("SELECT","",$qFacDat,$xConexion01,"");
            // f_Mensaje(__FILE__,__LINE__,$qFacDat." ~ ".mysql_num_rows($xFacDat));
						// echo $qFacDat;

            if (mysql_num_rows($xFacDat) > 0){
              $xRFD = mysql_fetch_array($xFacDat);

              #Que la factura pertenezca al cliente en cuestion.
              if ($_POST['cTerId' .($i+1)] != $xRFD['teridxxx']) {
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

							if($cPerAnio != $cPerAnoNew) {
								#Busco el comprobante y realizo la validacion de que no exista en el anio nuevo
								$qFcocxxx  = "SELECT ";
								$qFcocxxx .= "$cAlfa.fcoc$cPerAnoNew.comidxxx, ";
								$qFcocxxx .= "$cAlfa.fcoc$cPerAnoNew.comcodxx, ";
								$qFcocxxx .= "$cAlfa.fcoc$cPerAnoNew.comcscxx, ";
								$qFcocxxx .= "$cAlfa.fcoc$cPerAnoNew.comcsc2x ";
								$qFcocxxx .= "FROM $cAlfa.fcoc$cPerAnoNew ";
								$qFcocxxx .= "WHERE ";
								$qFcocxxx .= "$cAlfa.fcoc$cPerAnoNew.comidxxx = \"{$_POST['cComId' .($i+1)]}\" AND ";											
								$qFcocxxx .= "$cAlfa.fcoc$cPerAnoNew.comcodxx = \"{$_POST['cComCod' .($i+1)]}\" AND ";											
								$qFcocxxx .= "$cAlfa.fcoc$cPerAnoNew.comcscxx = \"{$_POST['cComCsc' .($i+1)]}\" AND ";											
								$qFcocxxx .= "$cAlfa.fcoc$cPerAnoNew.comcsc2x = \"{$_POST['cComCsc2' .($i+1)]}\" AND ";											
								$qFcocxxx .= "$cAlfa.fcoc$cPerAnoNew.regestxx = \"PROVISIONAL\" LIMIT 0,1";
								$xFcocxxx = f_MySql("SELECT","",$qFcocxxx,$xConexion01,"");

								if (mysql_num_rows($xFcocxxx) > 0){
									$nSwitch = 1;
									$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
									$cMsj .= "No es Posible Cambiar la Fecha de la Prefactura [{$_POST['cComId' .($i+1)]}-{$_POST['cComCod' .($i+1)]}-{$_POST['cComCsc' .($i+1)]}-{$_POST['cComCsc2' .($i+1)]}], ";
									$cMsj .= "Porque ya Existe para el Anio [$cPerAnoNew].\n";
								}
							}

							$nInd_mPreFact = count($mPreFact);
							$mPreFact[$nInd_mPreFact]['comidxxx'] = $xRFD['comidxxx'];
							$mPreFact[$nInd_mPreFact]['comcodxx'] = $xRFD['comcodxx'];
							$mPreFact[$nInd_mPreFact]['comcscxx'] = $xRFD['comcscxx'];
							$mPreFact[$nInd_mPreFact]['comcsc2x'] = $xRFD['comcsc2x'];
							$mPreFact[$nInd_mPreFact]['teridxxx'] = $xRFD['teridxxx'];
							$mPreFact[$nInd_mPreFact]['comfecxx'] = $xRFD['comfecxx'];
							$mPreFact[$nInd_mPreFact]['comfecve'] = $xRFD['comfecve'];
							$mPreFact[$nInd_mPreFact]['comobs2x'] = $xRFD['comobs2x'];
							$mPreFact[$nInd_mPreFact]['comfpxxx'] = $xRFD['comfpxxx'];
							$mPreFact[$nInd_mPreFact]['commemod'] = $xRFD['commemod'];

            } else {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "La Prefactura [{$_POST['cComId' .($i+1)]}-{$_POST['cComCod' .($i+1)]}-{$_POST['cComCsc' .($i+1)]}-{$_POST['cComCsc2' .($i+1)]}] ";
              $cMsj .= "No Existe o Esta Inactiva en el Anio [{$_POST['cPreAnio']}].\n";
            }
          } else {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Debe Selecionar una Prefactura en la Secuencia {$_POST['cDocSeq' .($i+1)]}.\n";
          }
        }
      }
    break;
    default:
      //no hace nada
    break;
	}

	/***** Ahora Empiezo a Grabar *****/
	if ($nSwitch == 0) {
		switch ($_COOKIE['kModo']) {
			/*****************************   UPDATE    ***********************************************/
			case "CAMBIOFECHAPFSL":
        foreach ($mPreFact as $vPreFact) {
					//Calculando la nueva fecha de vencimiento
					//calculo timestam de las dos fechas
					$vFecha    = explode("-",$vPreFact['comfecxx']);
					$dComFec   = mktime(0,0,0,$vFecha[1],$vFecha[2],$vFecha[0]);

					$vFechaVe  = explode("-",$vPreFact['comfecve']);
					$dComFecVe = mktime(0,0,0,$vFechaVe[1],$vFechaVe[2],$vFechaVe[0]);

					//resto a una fecha la otra
					$nDiferencia = $dComFecVe - $dComFec;

					//convierto segundos en dias
					$nDias = round($nDiferencia / (60 * 60 * 24));

					$cPerMesNew = substr($_POST['dFecNue'],5,2);
					$cPerDiaNew = substr($_POST['dFecNue'],8,2);
						
					#Buscando el plazo del cliente para calcular la nueva fecha de vencimiento
					$_POST['dComVen'] = date("Y-m-d", mktime(0, 0, 0, $cPerMesNew, $cPerDiaNew+$nDias, $cPerAnoNew));
						
					#Nuevo Periodo
					$cPerNew = $cPerAnoNew.str_pad($cPerMesNew,2,"0",STR_PAD_LEFT);

					if($cPerAnio == $cPerAnoNew) {
						#Se cambia la fecha en los registro
						$mUpdFcoc = array(array('NAME'=>'comfecxx','VALUE'=>$_POST['dFecNue']           	,'CHECK'=>'SI'),
															array('NAME'=>'comfecve','VALUE'=>$_POST['dComVen']           	,'CHECK'=>'SI'),
															array('NAME'=>'comperxx','VALUE'=>$cPerNew              				,'CHECK'=>'SI'),
															array('NAME'=>'regfmodx','VALUE'=>$_POST['dFecNue']           	,'CHECK'=>'SI'),
															array('NAME'=>'reghmodx','VALUE'=>date("H:i:s")          				,'CHECK'=>'SI'),
															array('NAME'=>'comidxxx','VALUE'=>trim($vPreFact['comidxxx'])		,'CHECK'=>'WH'),
															array('NAME'=>'comcodxx','VALUE'=>trim($vPreFact['comcodxx'])		,'CHECK'=>'WH'),
															array('NAME'=>'comcscxx','VALUE'=>trim($vPreFact['comcscxx'])		,'CHECK'=>'WH'),
															array('NAME'=>'comcsc2x','VALUE'=>trim($vPreFact['comcsc2x'])		,'CHECK'=>'WH'));
						if (f_MySql("UPDATE","fcoc$cPerAnoNew",$mUpdFcoc,$xConexion01,$cAlfa)) {
							#Actualizo Detalle
							#Se cambia la fecha en los registro
							$mUpdFcod = array(array('NAME'=>'comfecxx','VALUE'=>$_POST['dFecNue']           ,'CHECK'=>'SI'),
																array('NAME'=>'comfecve','VALUE'=>$_POST['dComVen']           ,'CHECK'=>'SI'),
																array('NAME'=>'comperxx','VALUE'=>$cPerNew              			,'CHECK'=>'SI'),
																array('NAME'=>'regfmodx','VALUE'=>$_POST['dFecNue']           ,'CHECK'=>'SI'),
																array('NAME'=>'reghmodx','VALUE'=>date("H:i:s")          			,'CHECK'=>'SI'),
																array('NAME'=>'comidxxx','VALUE'=>trim($vPreFact['comidxxx'])	,'CHECK'=>'WH'),
																array('NAME'=>'comcodxx','VALUE'=>trim($vPreFact['comcodxx']) ,'CHECK'=>'WH'),
																array('NAME'=>'comcscxx','VALUE'=>trim($vPreFact['comcscxx']) ,'CHECK'=>'WH'),
																array('NAME'=>'comcsc2x','VALUE'=>trim($vPreFact['comcsc2x']) ,'CHECK'=>'WH'));
							if (f_MySql("UPDATE","fcod$cPerAnoNew",$mUpdFcod,$xConexion01,$cAlfa)) {
							
							} else {
								$nSwitch = 1;
								$nError  = 1;
								$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
								$cMsj .= "Error al Actualizar el Registro la Tabla fcod$cPerAnio.\n";
							}	
						} else {
							$nSwitch = 1;
							$nError  = 1;
							$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMsj .= "Error al Actualizar el Registro la Tabla fcoc$cPerAnio.\n";
						}				
					} else {
						#Se crea el registo en la tabla $cPerAnoNew y se borrar el de la tabla $cPerAnio
						#Traigo datos de cabecera
						$qFcoc  = "SELECT * ";
						$qFcoc .= "FROM $cAlfa.fcoc$cPerAnio ";
						$qFcoc .= "WHERE ";
						$qFcoc .= "$cAlfa.fcoc$cPerAnio.comidxxx = \"{$vPreFact['comidxxx']}\" AND ";											
						$qFcoc .= "$cAlfa.fcoc$cPerAnio.comcodxx = \"{$vPreFact['comcodxx']}\" AND ";											
						$qFcoc .= "$cAlfa.fcoc$cPerAnio.comcscxx = \"{$vPreFact['comcscxx']}\" AND ";											
						$qFcoc .= "$cAlfa.fcoc$cPerAnio.comcsc2x = \"{$vPreFact['comcsc2x']}\" AND ";											
						$qFcoc .= "$cAlfa.fcoc$cPerAnio.regestxx = \"PROVISIONAL\" ";
						$qFcoc .= "LIMIT 0,1 ";
						$xFcoc = f_MySql("SELECT","",$qFcoc,$xConexion01,"");
						if (mysql_num_rows($xFcoc) == 0){
							$nSwitch = 1;
							$nError  = 1;
							$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMsj .= "Error al Buscar el Registro la Tabla fcoc$cPerAnio.\n";						
						}

						#Traigo datos de detalle
						$qFcod  = "SELECT * ";
						$qFcod .= "FROM $cAlfa.fcod$cPerAnio ";
						$qFcod .= "WHERE ";
						$qFcod .= "$cAlfa.fcod$cPerAnio.comidxxx = \"{$vPreFact['comidxxx']}\" AND ";											
						$qFcod .= "$cAlfa.fcod$cPerAnio.comcodxx = \"{$vPreFact['comcodxx']}\" AND ";											
						$qFcod .= "$cAlfa.fcod$cPerAnio.comcscxx = \"{$vPreFact['comcscxx']}\" AND ";											
						$qFcod .= "$cAlfa.fcod$cPerAnio.comcsc2x = \"{$vPreFact['comcsc2x']}\" AND ";											
						$qFcod .= "$cAlfa.fcod$cPerAnio.regestxx = \"PROVISIONAL\" ";
						$xFcod = f_MySql("SELECT","",$qFcod,$xConexion01,"");		
						if (mysql_num_rows($xFcod) == 0){
							$nSwitch = 1;
							$nError  = 1;
							$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMsj .= "Error al Buscar el Registro la Tabla fcod$cPerAnio.\n";						
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
							$mDelFcoc = array(array('NAME'=>'comidxxx','VALUE'=>trim($vPreFact['comidxxx'])			,'CHECK'=>'WH'),
																array('NAME'=>'comcodxx','VALUE'=>trim($vPreFact['comcodxx'])			,'CHECK'=>'WH'),
																array('NAME'=>'comcscxx','VALUE'=>trim($vPreFact['comcscxx'])			,'CHECK'=>'WH'),
																array('NAME'=>'comcsc2x','VALUE'=>trim($vPreFact['comcsc2x'])			,'CHECK'=>'WH'));
							if (f_MySql("DELETE","fcoc$cPerAnio",$mDelFcoc,$xConexion01,$cAlfa)) {
								#Borrando datos de datalle registro anterior
								$mDelFcod = array(array('NAME'=>'comidxxx','VALUE'=>trim($vPreFact['comidxxx'])   ,'CHECK'=>'WH'),
																	array('NAME'=>'comcodxx','VALUE'=>trim($vPreFact['comcodxx'])   ,'CHECK'=>'WH'),
																	array('NAME'=>'comcscxx','VALUE'=>trim($vPreFact['comcscxx'])   ,'CHECK'=>'WH'),
																	array('NAME'=>'comcsc2x','VALUE'=>trim($vPreFact['comcsc2x'])   ,'CHECK'=>'WH'));
								if (f_MySql("DELETE","fcod$cPerAnio",$mDelFcod,$xConexion01,$cAlfa)) {
									//no hace nada
								} else {
									$nSwitch = 1;
									$nError  = 1;
									$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
									$cMsj .= "Error al Borrar el Registro la Tabla fcod$cPerAnio.\n";
								}
							} else {
								$nSwitch = 1;
								$nError  = 1;
								$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
								$cMsj .= "Error al Borrar el Registro la Tabla fcoc$cPerAnio.\n";
							}
						}
					}

					if ($nSwitch == 0) {
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
						
						$mInsObs = array(array('NAME'=>'comidxxx','VALUE'=>$vPreFact['comidxxx']	,'CHECK'=>'SI'),
														array('NAME'=>'comcodxx','VALUE'=>$vPreFact['comcodxx']		,'CHECK'=>'SI'),
														array('NAME'=>'comcscxx','VALUE'=>$vPreFact['comcscxx']		,'CHECK'=>'SI'),
														array('NAME'=>'comcsc2x','VALUE'=>$vPreFact['comcsc2x']		,'CHECK'=>'SI'),
														array('NAME'=>'comfecxx','VALUE'=>$_POST['dFecNue']				,'CHECK'=>'SI'),
														array('NAME'=>'comfecan','VALUE'=>$vPreFact['comfecxx']	  ,'CHECK'=>'SI'),
														array('NAME'=>'obscscxx','VALUE'=>$cNumSec								,'CHECK'=>'SI'),
														array('NAME'=>'obsidxxx','VALUE'=>'FACTURA'								,'CHECK'=>'SI'),
														array('NAME'=>'gofidxxx','VALUE'=>'100'										,'CHECK'=>'SI'),
														array('NAME'=>'obsobsxx','VALUE'=>trim($_POST['cObserv'])	,'CHECK'=>'SI','CS'=>'NONE'),
														array('NAME'=>'regusrxx','VALUE'=>$_COOKIE['kUsrId']			,'CHECK'=>'SI'),
														array('NAME'=>'regfcrex','VALUE'=>date("Y-m-d")						,'CHECK'=>'SI'),
														array('NAME'=>'reghcrex','VALUE'=>date("H:i:s")						,'CHECK'=>'SI'),
														array('NAME'=>'regfmodx','VALUE'=>date("Y-m-d")						,'CHECK'=>'SI'),
														array('NAME'=>'reghmodx','VALUE'=>date("H:i:s")						,'CHECK'=>'SI'),
														array('NAME'=>'regestxx','VALUE'=>"ACTIVO"								,'CHECK'=>'SI'));
						if (f_MySql("INSERT","fcob0000",$mInsObs,$xConexion01,$cAlfa)) {
							
						} else {
							$nSwitch = 1;
							$nError  = 1;
							$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMsj .= "Error al Insertar la Observacion [fcob0000].\n";
						}	
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
  } else {
 	  if($_COOKIE['kModo']=="CAMBIOFECHAPFSL"){
 		  f_Mensaje(__FILE__,__LINE__,"Se Realizo el Cambio de Fecha de la(s) Prefactura(s) Sin Legalizar con Exito.!");
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

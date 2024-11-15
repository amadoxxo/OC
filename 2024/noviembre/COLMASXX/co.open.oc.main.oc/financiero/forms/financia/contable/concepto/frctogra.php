<?php
/**
 * Graba Concepto Contable.
 * @author
 * @package opencomex
 */
	include("../../../../libs/php/utility.php");
	$nSwitch = "0"; // Switch para Vericar la Validacion de Datos
	$cMsj = "\n";

	switch ($_COOKIE['kModo']) {
  	case "NUEVO":
  		$cApar=substr($_POST['cPucId'],0,6);
  		$qSqlCsc  = "SELECT ctocscxx ";
			$qSqlCsc .= "FROM $cAlfa.fpar0119 ";
			$qSqlCsc .= "WHERE SUBSTRING(pucidxxx,1,6) = \"{$cApar}\" AND  ";
			$qSqlCsc .= "regestxx = \"ACTIVO\" ORDER BY ctoidxxx DESC ";
			$xSqlCsc  = f_MySql("SELECT","",$qSqlCsc,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qSqlCsc."~".mysql_num_rows($xSqlCsc));
			$cCsc119 = 0;
			if(mysql_num_rows($xSqlCsc) > 0){
			  $xRCsc = mysql_fetch_array($xSqlCsc);
				$cCsc119=intval($xRCsc['ctocscxx']);
			}

	    $qSqlCsc  = "SELECT ctocscxx ";
      $qSqlCsc .= "FROM $cAlfa.fpar0121 ";
      $qSqlCsc .= "WHERE SUBSTRING(pucidxxx,1,6) = \"{$cApar}\" AND  ";
      $qSqlCsc .= "regestxx = \"ACTIVO\" ORDER BY ctoidxxx DESC ";
      $xSqlCsc  = f_MySql("SELECT","",$qSqlCsc,$xConexion01,"");
      //f_Mensaje(__FILE__,__LINE__,$qSqlCsc."~".mysql_num_rows($xSqlCsc));
      $cCsc121= 0;
      if(mysql_num_rows($xSqlCsc) > 0){
        $xRCsc = mysql_fetch_array($xSqlCsc);
        $cCsc121=intval($xRCsc['ctocscxx']);
      }

      //f_Mensaje(__FILE__,__LINE__,$cCsc119." ~ ".$cCsc121);
      if($cCsc119 > $cCsc121){
        $_POST['cCtoId'] = str_pad((intval($cCsc119)+1),4,"0",STR_PAD_LEFT);;
      } else {
        $_POST['cCtoId'] = str_pad((intval($cCsc121)+1),4,"0",STR_PAD_LEFT);;
      }

  	  $zBandEgr=0;
  	  $zBandP  =0;
  	  $zBandCPC=0; //Bandera que indica si hay P seleccionadas con Pagos por cuenta del cliente
  	  $zBandN  =0;
  	  $zBandC  =0;
  	  $zBandD  =0;
  	  $zBandCarBan=0;
  	  $zBandRec=0;
  	  $zBandCauPE=0;
  	  $zBandCajMe=0;
  	  $zBandFactu=0;
  	  $zMtrMov=array();

  	  $zSiEgr=explode("|",$_POST['cComMemo']);

  	  for($i=0; $i<count($zSiEgr); $i++) {
  	    if($zSiEgr[$i]!=""){
  	      $zSiEgrAh=explode("~",$zSiEgr[$i]);
  	      if($zSiEgrAh[0]=="G"){
  	        $zBandEgr=1;
  	      }
  	      if($zSiEgrAh[0]=="P"){
  	        $zBandP = 1;

  	        #Actualizacion Sucursal de Retencion Ica si la P es de Pagos por cuenta del cliente
  	        #Johana Arboleda Ramos 2012-08-16 10:20
  	        #Busco si la P es PCP
  	        $qComPCP  = "SELECT comidxxx, comcodxx, comtipxx ";
  	        $qComPCP .= "FROM $cAlfa.fpar0117 ";
  	        $qComPCP .= "WHERE ";
  	        $qComPCP .= "comidxxx = \"{$zSiEgrAh[0]}\" AND ";
  	        $qComPCP .= "comcodxx = \"{$zSiEgrAh[1]}\" AND ";
  	        $qComPCP .= "comtipxx = \"CPC\" AND ";
  	        $qComPCP .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
  	        $xComPCP  = f_MySql("SELECT","",$qComPCP,$xConexion01,"");
  	        if (mysql_num_rows($xComPCP) > 0) {
  	        	$zBandCPC = 1;
  	        }
  	      }
  	    	if($zSiEgrAh[0]=="N"){
  	        $zBandN = 1;
  	      }
  	    	if($zSiEgrAh[0]=="C"){
  	        $zBandC = 1;
  	      }
  	    	if($zSiEgrAh[0]=="D"){
  	        $zBandD = 1;
  	      }
  	      if($zSiEgrAh[0]=="R"){
  	      	$zBandRec=1;
  	      }
  	      if($zSiEgrAh[0]=="L"){
  	      	$zBandCarBan=1;
  	      }
  	      if($zSiEgrAh[0]=="M"){
  	      	$zBandCajMe=1;
  	      }
  	      if($zSiEgrAh[0]=="F"){
  	      	$zBandFactu=1;
  	      }
  	    }
  	  }

  	  $qSqlCta  = "SELECT  * ";
			$qSqlCta .= "FROM $cAlfa.fpar0115 ";
			$qSqlCta .= "WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$_POST['cPucId']}\" AND ";
			$qSqlCta .= "regestxx = \"ACTIVO\" ";
			$xSqlCta  = f_MySql("SELECT","",$qSqlCta,$xConexion01,"");
			$zRCta = mysql_fetch_array($xSqlCta);

			$qSqlCon  = "SELECT  * ";
			$qSqlCon .= "FROM $cAlfa.fpar0119 ";
			$qSqlCon .= "WHERE pucidxxx = \"{$_POST['cPucId']}\" AND ctoidxxx = \"{$_POST['cCtoId']}\" ";
			$qSqlCon .= "regestxx = \"ACTIVO\" ";
			$xSqlCon  = f_MySql("SELECT","",$qSqlCon,$xConexion01,"");
			$zRCon = mysql_fetch_array($xSqlCon);

			if($zRCon!=""){
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			  $cMsj .= "El No de Concepto ya Existe para esa Cuenta [fpar0119], \n";
			}

	    $qSqlCon  = "SELECT  * ";
      $qSqlCon .= "FROM $cAlfa.fpar0121 ";
      $qSqlCon .= "WHERE pucidxxx = \"{$_POST['cPucId']}\" AND ctoidxxx = \"{$_POST['cCtoId']}\" ";
      $qSqlCon .= "regestxx = \"ACTIVO\" ";
      $xSqlCon  = f_MySql("SELECT","",$qSqlCon,$xConexion01,"");
      $zRCon = mysql_fetch_array($xSqlCon);

      if($zRCon!=""){
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El No de Concepto ya Existe para esa Cuenta [fpar0121], \n";
      }

			if($_POST['cPucId']==""){
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			  $cMsj .= "Debe Escoger el No de Cuenta, \n";
			}

			if($_POST['cCtoId']==""){
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			  $cMsj .= "Debe Digitar el No de Concepto, \n";
			}

			if($_POST['cComMemo']=="|" || empty($_POST['cComMemo'])){
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			  $cMsj .= "Debe Escoger al menos un Comprobante, \n";
			}

			if($_POST['cCtoNit']==""){
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			  $cMsj .= "Debe Parametrizar el Tipo de Nit para Busqueda de Documento Cruce. \n ";
			}

			if($_POST['cCtoAnt']==""){
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			  $cMsj .= "Debe Parametrizar el Concepto para Anticipo, \n ";
			}

			if($_POST['cCtoPcc']==""){
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			  $cMsj .= "Debe Parametrizar el Concepto para Pagos a Terceros, \n ";
			}

			if($zBandP==1){
  			if($_POST['cCtoDesp']==""){
  			  $nSwitch = 1;
  			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe Digitar La Descripcion del Concepto, \n";
  			}

  			if($vSysStr['financiero_aplica_nit1_nit2_integracion_sistema_contable'] == "SI"){
  				if($_POST['cCtoNit1p'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo P \n";
  				}

  				if($_POST['cCtoNit2p'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo P \n";
  				}
  			}
			} else{
			  $_POST['cCtoDesp'] = "";
				$_POST['cCtoNit1p'] = "";
				$_POST['cCtoNit2p'] = "";
				$_POST['cCtoSucri'] = "";
			}

			#Validando la sucursal de Retencion ICA, esto aplica solo para las cuentas que empiezan por lo parametrizado en la variable del sistema y las P
			$vCtaRtIca = explode(",",$vSysStr['financiero_cuentas_reteica']);
			if (in_array(substr($_POST['cPucId'],0,4), $vCtaRtIca) && $zBandP == 1) {
	  		if($_POST['cCtoSucri']==""){
	  			$nSwitch = 1;
	  			$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	  			$cMsj .= "Debe Seleccionar la Sucursal Retencion ICA, \n";
	  		}
  		} else {
  			$_POST['cCtoSucri']="";
  		}

			if($zBandN==1){
  			if($_POST['cCtoDesn']==""){
  			  $nSwitch = 1;
  			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe Digitar La Descripcion del Concepto, \n";
  			}

				if($vSysStr['financiero_aplica_nit1_nit2_integracion_sistema_contable'] == "SI"){
  				if($_POST['cCtoNit1n'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo N \n";
  				}

  				if($_POST['cCtoNit2n'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo N \n";
  				}
  			}
			} else {
			  $_POST['cCtoDesn'] = "";
        $_POST['cCtoNit1n'] = "";
        $_POST['cCtoNit2n'] = "";
			}

			if($zBandC==1){
  			if($_POST['cCtoDesc']==""){
  			  $nSwitch = 1;
  			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe Digitar La Descripcion del Concepto, \n";
  			}

			  if($vSysStr['financiero_aplica_nit1_nit2_integracion_sistema_contable'] == "SI"){
          if($_POST['cCtoNit1c'] == ""){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo C \n";
          }

          if($_POST['cCtoNit2c'] == ""){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo C \n";
          }
        }
			} else {
			  $_POST['cCtoDesc'] = "";
				$_POST['cCtoNit1c'] = "";
				$_POST['cCtoNit2c'] = "";
			}

			if($zBandD==1){
  			if($_POST['cCtoDesd']==""){
  			  $nSwitch = 1;
  			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe Digitar La Descripcion del Concepto, \n";
  			}

				if($vSysStr['financiero_aplica_nit1_nit2_integracion_sistema_contable'] == "SI"){
  				if($_POST['cCtoNit1d'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo D \n";
  				}

  				if($_POST['cCtoNit2d'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo D \n";
  				}
  			}
			} else {
			  $_POST['cCtoDesd'] = "";
        $_POST['cCtoNit1d'] = "";
        $_POST['cCtoNit2d'] = "";
			}

			if($zBandEgr==1){
			  if($_POST['cCtoDesg']==""){
  			  $nSwitch = 1;
  			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe Digitar La Descripcion del Concepto, \n";
  			}

  			if($_POST['cADoCruEgr']==""){
  			  $nSwitch = 1;
  			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe  Parametrizar en los Datos Adicionales xra Egresos si aplica Do Cruce, Verifique \n";
  			}

  			if($_POST['cAConCruEgr']==""){
  			  $nSwitch = 1;
  			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe  Parametrizar en los Datos Adicionales para Egresos si aplica Concepto Cruce, Verifique \n";
  			}

				if($_POST['cCtoVlr01']==""){
  			  $_POST['cCtoVlr01'] = "NO";
  			}
				if($_POST['cCtoVlr02']==""){
  			  $_POST['cCtoVlr02'] = "NO";
  			}

				if($_POST['cCtoPvxxg']==""){
  			  $_POST['cCtoPvxxg'] = "NO";
  			}

  			$cCtoAntxg  = "NO";
      	$cCtoTfxxg  = "NO";
      	$cCtoDsacg  = "NO";
      	$cCtoNormal = "NO";

      	switch ($_POST['rTipoConcepto']){
      	  case "1":
      	    $cCtoAntxg = "SI";
      	  break;
      	  case "2":
      	    $cCtoTfxxg = "SI";
      	  break;
      	  case "3":
      	    $cCtoDsacg = "SI";
      	  break;
      	  case "4":
      	  	$cCtoNormal = "SI";
      	  break;
      	}

      	/**
      	* En Tipo de Comprobante G, si el tipo de concepto es NORMAL y se escogio PAGO TRIBUTOS ADUANEROS, el DO Informativo,Concepto Informativo, Pago VUCE,
      	* y calculo automatico de IVA deben ir en NO. En este caso solo Calculo Automatico BASE va en SI.
      	*/
      	if($cCtoNormal == "SI" && $_POST['cCtoPta'] == "SI"){
      		if($_POST['cADoCruEgr'] <> "NO"){//Do Informativo
      		  $nSwitch = 1;
      		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      	  	$cMsj .= "Para Tipo de Concepto NORMAL y Pago de Tributos Aduaneros, No aplica Do Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
      		}
      		if($_POST['cAConCruEgr'] <> "NO"){//Cto Informativo
      	  	$nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      		  $cMsj .= "Para Tipo de Concepto NORMAL y Pago de Tributos Aduaneros, No aplica Cto Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
      		}
      		if($_POST['cCtoPvxxg'] <> "NO"){//Pago Vuce.
      	  	$nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      		  $cMsj .= "Para Tipo de Concepto NORMAL y Pago de Tributos Aduaneros, No Aplica PAGO VUCE. Debe escoger una opcion diferente a SI, Verifique \n";
      		}
      	}
      	/**
      	* Fin validacion si el tipo de concepto es NORMAL y se escogio PAGO TRIBUTOS ADUANEROS, el DO Informativo,Concepto Informativo, Pago VUCE,
      	* y calculo automatico de IVA deben ir en NO. En este caso solo Calculo Automatico BASE va en SI.
      	*/

      	/**
      	* En Tipo de Comprobante G, si el tipo de concepto es NORMAL y se escogio PAGO VUCE, el DO Informativo, Concepto Informativo, Pago Tributos,
      	* y Calculo automatico de IVA deben ir en NO.  En este caso solo Calculo automatico Base va en SI.
      	*/
      	elseif($cCtoNormal == "SI" && $_POST['cCtoPvxxg'] == "SI"){
	      	if($_POST['cADoCruEgr'] <> "NO"){//Do Informativo
	      	  	$nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	    $cMsj .= "Para Tipo de Concepto NORMAL y Pago Vuce, No aplica Do Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
	  			}
	      		if($_POST['cAConCruEgr'] <> "NO"){//Cto Informativo
	      	  	$nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      		  $cMsj .= "Para Tipo de Concepto NORMAL y Pago Vuce, No aplica Cto Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
	      		}
	      		if($_POST['cCtoPta'] <> "NO"){//Pago Tributos.
	      	  	$nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      		  $cMsj .= "Para Tipo de Concepto NORMAL y Pago de Tributos Aduaneros, No Aplica PAGO VUCE. Debe escoger una opcion diferente a SI, Verifique \n";
	      		}
      		}

      	/**
      	 * Fin de validacion si el tipo de concepto es NORMAL y se escogio PAGO VUCE, el DO Informativo, Concepto Informativo, Pago Tributos,
      	 * y Calculo automatico de IVA deben ir en NO.  En este caso solo Calculo automatico Base va en SI.
      	 */

      	/**
      	 * En Tipo de Comprobante G, si el tipo de concepto es NORMAL y se escogio DO informativo, Cto Informativo debe ir en SI, los demas campos [Pago Tributos,
      	 * Calculo Automatico Base, Calculo Automatico IVA, Pago Vuce] deben ir en NO.
      	 */
      	elseif($cCtoNormal == "SI" && ($_POST['cADoCruEgr'] == "SI" || $_POST['cAConCruEgr'] == "SI")){
      		$cDesCon = "";//Variable que carga si es Do Informativo o Cto Informativo.
      		if($_POST['cADoCruEgr'] == "SI"){
      			$cDesCon = "Do Informativo";
      			if($_POST['cAConCruEgr'] <> "SI"){
      				$nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      			  $cMsj .= "Para Tipo de Concepto NORMAL y $cDesCon, Aplica Cto Informativo. Debe escoger una opcion diferente a NO, Verifique \n";
      			}
      		}else{
      			$cDesCon = "Cto Informativo";
      			if($_POST['cADoCruEgr'] <> "SI"){
      			  $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      				$cMsj .= "Para Tipo de Concepto NORMAL y $cDesCon, Aplica Do Informativo. Debe escoger una opcion diferente a NO, Verifique \n";
      			}
      		}
      		if($_POST['cCtoPta'] <> "NO"){//Pago Tributos.
      		    $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  	$cMsj .= "Para Tipo de Concepto NORMAL y $cDesCon, No Aplica Pago Tributos. Debe escoger una opcion diferente a SI, Verifique \n";
	      	}
      		if($_POST['cCtoPvxxg'] <> "NO"){//Calculo Automatico IVA.
      		    $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  	$cMsj .= "Para Tipo de Concepto NORMAL y $cDesCon, No Aplica Pago Vuce. Debe escoger una opcion diferente a SI, Verifique \n";
	      	}
      	}
      	/**
      	 * Fin Validacion si el tipo de concepto es NORMAL y se escogio DO informativo, Cto Informativo debe ir en SI, los demas campos [Pago Tributos,
      	 * Calculo Automatico Base, Calculo Automatico IVA, Pago Vuce] deben ir en NO.
      	 */


      	/**
      	 * En Tipo de Comprobante G, si se escogio Calculo Automatico de IVA, obligatoriamente debe ir en SI Calculo automatico Base.
      	 * y los demas campos [Pago Tributos,Calculo Automatico Base, Calculo Automatico IVA, Pago Vuce] deben ir en NO.
      	*/
      	elseif($cCtoNormal == "SI"){
      		if($_POST['cADoCruEgr'] <> "NO"){//Do Informativo
      		  $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  $cMsj .= "Para Tipo de Concepto NORMAL y Calculo Automatico de IVA, No aplica Do Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
	      	}
	      	if($_POST['cAConCruEgr'] <> "NO"){//Cto Informativo
	      	  $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      		$cMsj .= "Para Tipo de Concepto NORMAL y Calculo Automatico de IVA, No aplica Cto Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
	      	}
	      	if($_POST['cCtoPta'] <> "NO"){//Pago Tributos.
	      	  $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      		$cMsj .= "Para Tipo de Concepto NORMAL y Calculo Automatico de IVA, No Aplica Pago Tributos. Debe escoger una opcion diferente a SI, Verifique \n";
	      	}
      		if($_POST['cCtoPvxxg'] <> "NO"){//Pago Vuce.
      		  $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      		$cMsj .= "Para Tipo de Concepto NORMAL y Calculo Automatico de IVA, No Aplica Pago Vuce. Debe escoger una opcion diferente a SI, Verifique \n";
	      	}
      	}
      	/**
      	 * Fin Validacion si se escogio Calculo Automatico de IVA, obligatoriamente debe ir en SI Calculo automatico Base.
      	 * y los demas campos [Pago Tributos,Calculo Automatico Base, Calculo Automatico IVA, Pago Vuce] deben ir en NO.
      	 */

      	/**
      	 * En Tipo de Comprobante G, si se parametrizar Calculo Automatico de IVA en NO, El Calculo automatico Base puede ir en SI/NO.
      	 * y los demas campos [Pago Tributos,Calculo Automatico Base, Calculo Automatico IVA, Pago Vuce] deben ir en NO.
      	*/
      	elseif($cCtoNormal == "SI" ){
      		if($_POST['cADoCruEgr'] <> "NO"){//Do Informativo
      		  $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  $cMsj .= "Para Tipo de Concepto NORMAL y Calculo Automatico de IVA, No aplica Do Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
	      	}
	      	if($_POST['cAConCruEgr'] <> "NO"){//Cto Informativo
	      	  $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      		$cMsj .= "Para Tipo de Concepto NORMAL y Calculo Automatico de IVA, No aplica Cto Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
	      	}
	      	if($_POST['cCtoPta'] <> "NO"){//Pago Tributos.
	      	  $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      		$cMsj .= "Para Tipo de Concepto NORMAL y Calculo Automatico de IVA, No Aplica Pago Tributos. Debe escoger una opcion diferente a SI, Verifique \n";
	      	}
      		if($_POST['cCtoPvxxg'] <> "NO"){//Pago Vuce.
      		  $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      		$cMsj .= "Para Tipo de Concepto NORMAL y Calculo Automatico de IVA, No Aplica Pago Vuce. Debe escoger una opcion diferente a SI, Verifique \n";
	      	}
      	}

      	/**
      	 * Fin Validacion si se parametrizar Calculo Automatico de IVA en NO, El Calculo automatico Base puede ir en SI/NO.
      	 * y los demas campos [Pago Tributos,Calculo Automatico Base, Calculo Automatico IVA, Pago Vuce] deben ir en NO.
      	 */
      	/**
      	 * Si tipo de Concepto es Tranferencia Fondos o Aplica cliente no aplica ninguna opcion
      	 */
      	if($cCtoTfxxg == "SI" || $cCtoDsacg == "SI"){
	      	if($_POST['cADoCruEgr'] <> "NO"){//Do Informativo
	      	  $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  $cMsj .= "Para Tipo de Concepto NORMAL y Transferencia Fondos, No aplica Do Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
	      	}
	      	if($_POST['cAConCruEgr'] <> "NO"){//Cto Informativo
	      	  $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  $cMsj .= "Para Tipo de Concepto NORMAL y Transferencia Fondos, No aplica Cto Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
	      	}
      		if($_POST['cCtoPta'] <> "NO"){//Pago Tributos.
      		  $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      		$cMsj .= "Para Tipo de Concepto NORMAL y Transferencia Fondos, No Aplica Pago Tributos. Debe escoger una opcion diferente a SI, Verifique \n";
	      	}
	      	if($_POST['cCtoPvxxg'] <> "NO"){//Pago Vuce.
	      	  $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  $cMsj .= "Para Tipo de Concepto NORMAL y Transferencia Fondos, No Aplica PAGO VUCE. Debe escoger una opcion diferente a SI, Verifique \n";
	      	}
      	}
      	/**
      	 * Fin validacion si tipo de Concepto es Tranferencia Fondos o Aplica Cliente no aplica ninguna opcion
      	 */

				if($vSysStr['financiero_aplica_nit1_nit2_integracion_sistema_contable'] == "SI"){
  				if($_POST['cCtoNit1g'] == ""){
  					$nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo G \n";
  				}

  				if($_POST['cCtoNit2g'] == ""){
  					$nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo G \n";
  				}
  			}

  			if($_POST['cDocInfG'] == ""){//Documento Informativo - Campo requerido para Aduanera Grancolombiana.
  			  $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Documento Informativo no puede ser vacio, \n ";
        }
			} else {
			  $_POST['cCtoDesg'] = "";
				$_POST['cADoCruEgr'] = "";
				$_POST['cAConCruEgr'] = "";
				$_POST['cDocInfG'] = "";
				$cCtoTfxxg = "";
				$cCtoDsacg = "";
				$cCtoAntxg = "";
				$_POST['cComMemoApl'] = "";
				$_POST['cCtoPta'] = "";
				$_POST['cCtoPvxxg'] = "";
				$_POST['cCtoNit1g'] = "";
				$_POST['cCtoNit2g'] = "";
			}

			if($zBandRec==1){
			  if($_POST['cCtoDesr']==""){
			    $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe Digitar La Descripcion del Concepto, \n ";
  			}
  			if($_POST['cCtoAntxr']==""){
  			  $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe Parametrizar en los Datos Adicionales xra Recibos de Caja si aplica Anticipo, \n ";
  			}

				if($vSysStr['financiero_aplica_nit1_nit2_integracion_sistema_contable'] == "SI"){
  				if($_POST['cCtoNit1r'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo R \n";
  				}

  				if($_POST['cCtoNit2r'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo R \n";
  				}
  			}
			} else {
			  $_POST['cCtoDesr'] = "";
        $_POST['cCtoAntxr'] = "";
        $_POST['cCtoNit1r'] = "";
        $_POST['cCtoNit2r'] = "";
			}

			if($zBandCarBan==1){
			  if($_POST['cCtoDesL']==""){
  			  $nSwitch = 1;
  			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe Digitar La Descripcion del Concepto, \n";
  			}
  			if($_POST['cADoCruL']==""){
  			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe Parametrizar en los Datos Adicionales para Cartas Bancarias si aplica Do Informativo, Verifique \n";
  			  $nSwitch = 1;
  			}
  			if($_POST['cAConCruL']==""){
  			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe Parametrizar en los Datos Adicionales para Cartas Bancarias si aplica Concepto Informativo, Verifique \n";
  			  $nSwitch = 1;
  			}
				if($_POST['cCtoVlr01']==""){
  			  $_POST['cCtoVlr01'] = "NO";
  			}
				if($_POST['cCtoVlr02']==""){
  			  $_POST['cCtoVlr02'] = "NO";
  			}

				if($_POST['cCtoPvxxL']==""){
  			  $_POST['cCtoPvxxL'] = "NO";
  			}

      	$cCtoDsacL   = "NO";
      	$cCtoNormalL = "NO";

      	switch ($_POST['rTipoConceptoL']){
      	  case "1":
      	  	$cCtoNormalL = "SI";
      	  break;
      	  case "2":
      	    $cCtoDsacL = "SI";
      	  break;
      	}

      	/**
      	 * Inicio Validacion
      	 * En Tipo de Comprobante L, si el tipo de concepto es NORMAL y se escogio PAGO TRIBUTOS ADUANEROS,
      	 * el DO Informativo, Concepto Informativo, Pago VUCE y calculo automatico de IVA deben ir en NO.
      	 * En este caso solo Calculo Automatico BASE va en SI.
      	*/
      	if($cCtoNormalL == "SI" && $_POST['cCtoPtaL'] == "SI"){
      		if($_POST['cADoCruL'] <> "NO"){//Do Informativo
      		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      	  	$cMsj .= "Para Tipo de Concepto NORMAL y Pago de Tributos Aduaneros, No aplica Do Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
  			  	$nSwitch = 1;
      		}
      		if($_POST['cAConCruL'] <> "NO"){//Cto Informativo
      		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      	  	$cMsj .= "Para Tipo de Concepto NORMAL y Pago de Tributos Aduaneros, No aplica Cto Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
  			  	$nSwitch = 1;
      		}
      		if($_POST['cCtoPvxxL'] <> "NO"){//Pago Vuce.
      		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      	  	$cMsj .= "Para Tipo de Concepto NORMAL y Pago de Tributos Aduaneros, No Aplica PAGO VUCE. Debe escoger una opcion diferente a SI, Verifique \n";
  			  	$nSwitch = 1;
      		}
      	}
      	/**
      	 * Fin Validacion
      	 * En Tipo de Comprobante L, si el tipo de concepto es NORMAL y se escogio PAGO TRIBUTOS ADUANEROS,
      	 * el DO Informativo, Concepto Informativo, Pago VUCE y calculo automatico de IVA deben ir en NO.
      	 * En este caso solo Calculo Automatico BASE va en SI.
      	*/

      	/**
      	 * Inicio Validacion
      	 * En Tipo de Comprobante L, si el tipo de concepto es NORMAL y se escogio PAGO VUCE,
      	 * el DO Informativo, Concepto Informativo, Pago Tributos y Calculo automatico de IVA deben ir en NO.
      	 * En este caso solo Calculo Automatico Base va en SI.
      	*/
      	if($cCtoNormalL == "SI" && $_POST['cCtoPvxxL'] == "SI"){
	      	if($_POST['cADoCruL'] <> "NO"){//Do Informativo
	      	  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      	  	$cMsj .= "Para Tipo de Concepto NORMAL y Pago Vuce, No aplica Do Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
  			  	$nSwitch = 1;
      		}
      		if($_POST['cAConCruL'] <> "NO"){//Cto Informativo
      		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      	  	$cMsj .= "Para Tipo de Concepto NORMAL y Pago Vuce, No aplica Cto Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
  			  	$nSwitch = 1;
      		}
      		if($_POST['cCtoPtaL'] <> "NO"){//Pago Tributos.
      		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      	  	$cMsj .= "Para Tipo de Concepto NORMAL y Pago de Tributos Aduaneros, No Aplica PAGO VUCE. Debe escoger una opcion diferente a SI, Verifique \n";
  			  	$nSwitch = 1;
      		}
      	}
      	/**
      	 * Fin Validacion
      	 * En Tipo de Comprobante L, si el tipo de concepto es NORMAL y se escogio PAGO VUCE,
      	 * el DO Informativo, Concepto Informativo, Pago Tributos y Calculo automatico de IVA deben ir en NO.
      	 * En este caso solo Calculo Automatico Base va en SI.
      	*/

      	/**
      	 * Inicio Validacion
      	 * En Tipo de Comprobante L, si el tipo de concepto es NORMAL y se escogio DO informativo el Cto Informativo debe ir en SI,
      	 * los demas campos [Pago Tributos, Pago Vuce, Calculo Automatico Base, Calculo Automatico IVA] deben ir en NO.
      	*/
      	if($cCtoNormalL == "SI" && ($_POST['cADoCruL'] == "SI" || $_POST['cAConCruL'] == "SI")){
      		$cDesConL = "";//Variable que carga si es Do Informativo o Cto Informativo.
      		if($_POST['cADoCruL'] == "SI"){
      			$cDesConL = "DO INFORMATIVO";
      			if($_POST['cAConCruL'] <> "SI"){
      			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      				$cMsj .= "Para Tipo de Concepto NORMAL y $cDesConL, Aplica Cto Informativo. Debe escoger una opcion diferente a NO, Verifique \n";
	  			  	$nSwitch = 1;
      			}
      		}else{
      			$cDesConL = "CTO INFORMATIVO";
      			if($_POST['cADoCruL'] <> "SI"){
      			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      				$cMsj .= "Para Tipo de Concepto NORMAL y $cDesConL, Aplica Do Informativo. Debe escoger una opcion diferente a NO, Verifique \n";
	  			  	$nSwitch = 1;
      			}
      		}
      		if($_POST['cCtoPtaL'] <> "NO"){//Pago Tributos.
      		    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  	$cMsj .= "Para Tipo de Concepto NORMAL y $cDesConL, No Aplica Pago Tributos. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  	$nSwitch = 1;
	      	}
      		if($_POST['cCtoPvxxL'] <> "NO"){//Pago VUCE
      		    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  	$cMsj .= "Para Tipo de Concepto NORMAL y $cDesConL, No Aplica Pago Vuce. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  	$nSwitch = 1;
	      	}
      		if($_POST['cCtoVlr01'] <> "NO"){//Calculo Automatico de Base
      		    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  	$cMsj .= "Para Tipo de Concepto NORMAL y $cDesConL, No Aplica Calculo Automatico Base. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  	$nSwitch = 1;
	      	}
      		if($_POST['cCtoVlr02'] <> "NO"){//Calculo Automatico de IVA
      		    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  	$cMsj .= "Para Tipo de Concepto NORMAL y $cDesConL, No Aplica Calculo Automatico de IVA. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  	$nSwitch = 1;
	      	}
      	}
      	/**
      	 * Fin Validacion
      	 * En Tipo de Comprobante L, si el tipo de concepto es NORMAL y se escogio DO informativo el Cto Informativo debe ir en SI,
      	 * los demas campos [Pago Tributos, Pago Vuce, Calculo Automatico Base, Calculo Automatico IVA] deben ir en NO.
      	*/

       	/**
       	 * Inicio Validacion
      	 * Si tipo de Concepto es Aplica cliente no aplica ninguna opcion
      	*/
      	if($cCtoDsacL == "SI"){
	      	if($_POST['cADoCruL'] <> "NO"){//Do Informativo
	      	  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  $cMsj .= "Para Tipo de Concepto APLICA CLIENTE, No aplica Do Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  $nSwitch = 1;
	      	}
	      	if($_POST['cAConCruL'] <> "NO"){//Cto Informativo
	      	  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  $cMsj .= "Para Tipo de Concepto APLICA CLIENTE, No aplica Cto Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  $nSwitch = 1;
	      	}
      		if($_POST['cCtoPtaL'] <> "NO"){//Pago Tributos.
      		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      		$cMsj .= "Para Tipo de Concepto APLICA CLIENTE, No Aplica Pago Tributos. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  $nSwitch = 1;
	      	}
	      	if($_POST['cCtoPvxxL'] <> "NO"){//Pago Vuce.
	      	  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  $cMsj .= "Para Tipo de Concepto APLICA CLIENTE, No Aplica PAGO VUCE. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  $nSwitch = 1;
	      	}
      	}
       	/**
       	 * Fin Validacion
      	 * Si tipo de Concepto es Aplica cliente no aplica ninguna opcion
      	*/

				if($vSysStr['financiero_aplica_nit1_nit2_integracion_sistema_contable'] == "SI"){
  				if($_POST['cCtoNit1L'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo L \n";
  				}

  				if($_POST['cCtoNit2L'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo L \n";
  				}
  			}

      	if($_POST['cDocInfL'] == ""){//Documento Informativo - Campo requerido para Aduanera Grancolombiana (2012-02-15).
      	  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      	  $cMsj .= "El Documento Informativo no puede ser vacio, \n ";
  			  $nSwitch = 1;
      	}
			} else {
			  $_POST['cCtoDesL']="";
				$_POST['cADoCruL']="";
				$_POST['cAConCruL']="";
				$_POST['cDocInfL']="";
				$cCtoDsacL="";
				$_POST['cCtoPtaL']="";
				$_POST['cCtoPvxxL']="";
				$_POST['cCtoNit1L']="";
				$_POST['cCtoNit2L']="";
				$_POST['cComMemoApl']="";
			}

			if($zBandCajMe==1){
			  if($_POST['cCtoDesm']==""){
			    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe Digitar La Descripcion del Concepto, \n ";
  			  $nSwitch = 1;
  			}
  			if($_POST['cCtoTpaxm']==""){
  			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe Parametrizar en los Datos Adicionales xra Caja Menor si aplica Tipo de Pago, \n ";
  			  $nSwitch = 1;
  			}

				if($vSysStr['financiero_aplica_nit1_nit2_integracion_sistema_contable'] == "SI"){
  				if($_POST['cCtoNit1m'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo M \n";
  				}

  				if($_POST['cCtoNit2m'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo M \n";
  				}
  			}
			} else {
			  $_POST['cCtoDesm'] = "";
				$_POST['cCtoTpaxm'] = "";
				$_POST['cCtoNit1m'] = "";
				$_POST['cCtoNit2m'] = "";
			}

			if($zBandFactu==1){
				$mComprobantes = f_Explode_Array($_POST['cComMemo'],"|","~");
				for ($i=0;$i<count($mComprobantes);$i++) {
				 	if ($mComprobantes[$i][0] == "F") {
	          // Validar que el Concepto (fpar0119) no se repita con la misma clase de concepto
						$qSqlCto  = "SELECT * ";
						$qSqlCto .= "FROM $cAlfa.fpar0119 ";
						$qSqlCto .= "WHERE ";
						$qSqlCto .= "ctocomxx LIKE \"%{$mComprobantes[$i][0]}~{$mComprobantes[$i][1]}%\" AND ";
						$qSqlCto .= "ctoclaxf = \"{$_POST['cCtoClaxf']}\" AND ";
						$qSqlCto .= "regestxx = \"ACTIVO\"";
						$xSqlCto  = f_MySql("SELECT","",$qSqlCto,$xConexion01,"");
						//f_Mensaje(__FILE__,__LINE__,$qSqlCto." ~ ".mysql_num_rows($xSqlCto));
						if (mysql_num_rows($xSqlCto) > 0) {
							$cCtoCad = "";
							while ($zRCto = mysql_fetch_array($xSqlCto)) {
								$cCtoCad .= "[".$zRCto['ctoidxxx']."-".$zRCto['regestxx']."], ";
							}
							$cCtoCad = substr($cCtoCad,0,strlen($cCtoCad)-2);
							$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMsj .= "Clase de Concepto [".$_POST['cCtoClaxf']."] ya se Encuentra Parametrizado para el Comprobante ";
							$cMsj .= "[{$mComprobantes[$i][0]}~{$mComprobantes[$i][1]}]\n";
							$nSwitch = 1;
	          }

	          /**
	           * Para Las siguientes clases de concepto no pueden existir su equivalente en (IP+PCC)
	           */
	          $vClases = array("SAGENCIA","SAGENCIAUSD","SAGENCIAIP","SAGENCIAUSDIP","SAGENCIAPCC","SAGENCIAUSDPCC");

		        if (in_array($_POST['cCtoClaxf'], $vClases) == true) {

		        	$mClaCon["SAGENCIA"]       = "\"SAGENCIAIP\",\"SAGENCIAPCC\"";    //Equivalencia con IP y PCC
		        	$mClaCon["SAGENCIAIP"]     = "\"SAGENCIA\"";    //Equivalencia con IP+PCC
		        	$mClaCon["SAGENCIAPCC"]    = "\"SAGENCIA\"";    //Equivalencia con IP+PCC

		        	$mClaCon["SAGENCIAUSD"]    = "\"SAGENCIAUSDIP\",\"SAGENCIAUSDPCC\"";    //Equivalencia con IP y PCC
		        	$mClaCon["SAGENCIAUSDIP"]  = "\"SAGENCIAUSD\""; //Equivalencia con IP+PCC
		        	$mClaCon["SAGENCIAUSDPCC"] = "\"SAGENCIAUSD\""; //Equivalencia con IP+PCC

		          // Validar que el Concepto (fpar0119) no se repita con la misma clase de concepto para (IP+PCC)
		          $qSqlCto  = "SELECT * ";
		          $qSqlCto .= "FROM $cAlfa.fpar0119 ";
		          $qSqlCto .= "WHERE ";
		          $qSqlCto .= "ctocomxx LIKE \"%{$mComprobantes[$i][0]}~{$mComprobantes[$i][1]}%\" AND ";
		          $qSqlCto .= "ctoclaxf IN (".$mClaCon["{$_POST['cCtoClaxf']}"].") AND ";
		          $qSqlCto .= "regestxx = \"ACTIVO\"";
		          $xSqlCto  = f_MySql("SELECT","",$qSqlCto,$xConexion01,"");
		          //f_Mensaje(__FILE__,__LINE__,$qSqlCto." ~ ".mysql_num_rows($xSqlCto));
		          if (mysql_num_rows($xSqlCto) > 0) {
		          	$cCtoCad = "";
		          	while ($zRCto = mysql_fetch_array($xSqlCto)) {
		          		$cCtoCad .= "[".$zRCto['ctoidxxx']."-".$zRCto['regestxx']."], ";
		          	}
		          	$cCtoCad = substr($cCtoCad,0,strlen($cCtoCad)-2);
		          	$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
		          	$cMsj .= "La Clase de Concepto [".$_POST['cCtoClaxf']."] ya se Encuentra Parametrizado para el Comprobante ";
		          	$cMsj .= "[{$mComprobantes[$i][0]}~{$mComprobantes[$i][1]}] con la Clase de Concepto [".str_replace("\"", "", $mClaCon["{$_POST['cCtoClaxf']}"])."].\n";
		          	$nSwitch = 1;
		          }
	          } ## if (in_array($_POST['cCtoClaxf'], $vClases) == true) { ##
					}//if ($mComprobantes[$i][0] == "F") {
	      }//for ($i=0;$i<count($mComprobantes);$i++) {
				if($_POST['cCtoDesf']==""){
				  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe Digitar La Descripcion del Concepto, \n ";
  			  $nSwitch = 1;
  			}
  			if($_POST['cCtoClaxf']==""){
  			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe Parametrizar en los Datos Adicionales xra Facturacion la Clase del Concepto, \n ";
  			  $nSwitch = 1;
  			}

				if($vSysStr['financiero_aplica_nit1_nit2_integracion_sistema_contable'] == "SI"){
  				if($_POST['cCtoNit1f'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo F \n";
  				}

  				if($_POST['cCtoNit2f'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo F \n";
  				}
  			}
			} else {
			  $_POST['cCtoDesf'] = "";
				$_POST['cCtoClaxf'] = "";
				$_POST['cCtoNit1f'] = "";
				$_POST['cCtoNit2f'] = "";
			}

			/**
       * Codigo de Integracion con E2K
       */
			if ($cAlfa == "UPSXXXXX" || $cAlfa == "TEUPSXXXXX" || $cAlfa == "TEUPSXXXXP" || $cAlfa == "DEUPSXXXXX") {

			  /**
			   * Si la cuenta detalla por DO y la base de datos es de UPS se obliga a digitar el codigo E2K
			   */
	      if ($zRCta['pucdetxx'] == 'D' && $_POST['cCtoE2k'] == "") {
	        $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Codigo de Integracion para E2K no Puede Ser Vacio.\n";
	      }

       /**
        * Valido que sean solo alfanumericos
        */

        if ($_POST['cCtoE2k'] != "" && !preg_match("/^[[:alnum:]]+$/", $_POST['cCtoE2k'])) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Codigo de Integracion para E2K debe ser Alfanumerico.\n";
        }
      }

      if ($_POST['cNuAsBel'] != ""){ // Valida si es n�mero la asignacion para la Integracion de Belcorp 2013-06-12
      	if (!is_numeric($_POST['cNuAsBel'])) {
      		$nSwitch  = 1;
      		$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      		$cMsj .= "El N&uacute;mero de Asignaci&oacute;n debe ser N&uacute;merico. \n";
      	}else{
      		if ($_POST['cNuAsBel'] == 0) {
      			$nSwitch  = 1;
      			$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      			$cMsj .= "El N&uacute;mero de Asignaci&oacute;n debe ser Mayor a Cero. \n";
      		}
      	}
      }

			/*** Si la variable Categoriacion Conceptos Factuacion esta encendida se habilita el menu de Categoria Conceptos***/
			if($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI'){
				/*** Valido que la categoria concepto exista en el sistema.***/
				$qCatCon  = "SELECT * ";
				$qCatCon .= "FROM $cAlfa.fpar0144 ";
				$qCatCon .= "WHERE ";
				$qCatCon .= "cacidxxx = \"{$_POST['cCacId']}\" LIMIT 0,1 ";
				$xCatCon  = f_MySql("SELECT","",$qCatCon,$xConexion01,"");
				// f_Mensaje(__FILE__,__LINE__,$qCatCon."~".mysql_num_rows($xCatCon));
				if(mysql_num_rows($xCatCon) == 0 && $_POST['cCacId'] != ""){
					$nSwitch  = 1;
					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMsj .= "La Categoria Concepto es invalida. \n";
				}
			}

			/*** Valido que si se seleciono la opcion 001 en el combo clasificacion producto, se debe diligenciar el campo de Codigo Colombia Compra Eficiente ***/
			if($_POST['cClasProd'] == "001" && $_POST['cCceId'] == ""){
				$nSwitch  = 1;
				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "El Campo Colombia Compra Eficiente no puede ser vacio. \n";
			}

			$cLineaNeg = "";
			$vBDIntegracionColmasSap = explode("~",$vSysStr['system_integracion_colmas_sap']);
			if (in_array($cAlfa, $vBDIntegracionColmasSap) == true) {
				// Validando las lineas de ingreso
				for ($i=0;$i<$_POST['nSecuencia_Grid_LineaNegocio'];$i++) {
					if ($_POST['cCodLineaNeg' . ($i+1)] != "") {
						$cLineaNeg .= $_POST['cCodLineaNeg' . ($i+1)]."~".$_POST['cCtaIngreso' . ($i+1)]."~".$_POST['cCtaCosto' . ($i+1)]."|";

						// Convierte la cuenta a un texto con formato numerico valido
						$cCtaIngreso = str_replace(',', '.', $_POST['cCtaIngreso' . ($i+1)]);
						$cCtaCosto   = str_replace(',', '.', $_POST['cCtaCosto' . ($i+1)]);

						// Validando la linea de negocio
						$qLineaNeg  = "SELECT ";
						$qLineaNeg .= "regestxx ";
						$qLineaNeg .= "FROM $cAlfa.zcol0003 ";
						$qLineaNeg .= "WHERE ";
						$qLineaNeg .= "lnecodxx = \"{$_POST['cCodLineaNeg' . ($i+1)]}\" LIMIT 0,1";
						$xLineaNeg  = f_MySql("SELECT","",$qLineaNeg,$xConexion01,"");
						if (mysql_num_rows($xLineaNeg) > 0) {
							$vLineaNeg = mysql_fetch_array($xLineaNeg);
							if ($vLineaNeg['regestxx'] == "INACTIVO") {
								$nSwitch  = 1;
								$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
								$cMsj .= " La Linea de Negocio[".$_POST['cCodLineaNeg' . ($i+1)]."], se Encuentra INACTIVA, Secuencia [".($i+1)."]. \n";
							}
						} else {
							$nSwitch  = 1;
							$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMsj .= " La Linea de Negocio[".$_POST['cCodLineaNeg' . ($i+1)]."], No Existe, Secuencia [".($i+1)."]. \n";
						}

						if ($_POST['cCtaIngreso' . ($i+1)] == "" && $_POST['cCtaCosto' . ($i+1)] == "") {
							$nSwitch  = 1;
							$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMsj .= " Debe Ingresar la Cuenta de Ingreso o la Cuenta de Costo, Secuencia [".($i+1)."]. \n";
						}

						if ((!is_numeric($cCtaIngreso) && $cCtaIngreso != "") || ($cCtaIngreso != "" && trim($cCtaIngreso) == "")) {
							$nSwitch  = 1;
							$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMsj .= " Debe Ingresar un Tipo de Dato Numerico en la Cuenta de Ingreso, Secuencia [".($i+1)."]. \n";
						}

						if ((!is_numeric($cCtaCosto) && $cCtaCosto != "") || ($cCtaCosto != "" && trim($cCtaCosto) == "")) {
							$nSwitch  = 1;
							$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMsj .= " Debe Ingresar un Tipo de Dato Numerico en la Cuenta de Costo, Secuencia [".($i+1)."]. \n";
						}
					}
				}
			}
		break;
  	case "EDITAR":

  	  $zBandEgr=0;
  	  $zBandP  =0;
  	  $zBandCPC=0; //Bandera que indica si hay P seleccionadas con Pagos por cuenta del cliente
  	  $zBandN  =0;
  	  $zBandC  =0;
  	  $zBandD  =0;
  	  $zBandCarBan=0;
  	  $zBandRec=0;
  	  $zBandCauPE=0;
  	  $zBandCajMe=0;
  	  $zBandFactu=0;

  	  $zSiEgr=explode("|",$_POST['cComMemo']);
  	  for($i=0; $i<count($zSiEgr); $i++){
  	    if($zSiEgr[$i]!=""){
  	      $zSiEgrAh=explode("~",$zSiEgr[$i]);
  	      if($zSiEgrAh[0]=="G"){
  	        $zBandEgr = 1;
  	      }
  	      if($zSiEgrAh[0]=="P"){
  	        $zBandP = 1;

  	      	#Actualizacion Sucursal de Retencion Ica si la P es de Pagos por cuenta del cliente
  	        #Johana Arboleda Ramos 2012-08-16 10:20
  	        #Busco si la P es PCP
  	        $qComPCP  = "SELECT comidxxx, comcodxx, comtipxx ";
  	        $qComPCP .= "FROM $cAlfa.fpar0117 ";
  	        $qComPCP .= "WHERE ";
  	        $qComPCP .= "comidxxx = \"{$zSiEgrAh[0]}\" AND ";
  	        $qComPCP .= "comcodxx = \"{$zSiEgrAh[1]}\" AND ";
  	        $qComPCP .= "comtipxx = \"CPC\" AND ";
  	        $qComPCP .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
  	        $xComPCP  = f_MySql("SELECT","",$qComPCP,$xConexion01,"");
  	        if (mysql_num_rows($xComPCP) > 0) {
  	        	$zBandCPC = 1;
  	        }
  	      }
  	    	if($zSiEgrAh[0]=="N"){
  	        $zBandN = 1;
  	      }
  	    	if($zSiEgrAh[0]=="C"){
  	        $zBandC = 1;
  	      }
  	    	if($zSiEgrAh[0]=="D"){
  	        $zBandD = 1;
  	      }
  	      if($zSiEgrAh[0]=="R"){
  	      	$zBandRec=1;
  	      }
  	      if($zSiEgrAh[0]=="M"){
  	      	$zBandCajMe=1;
  	      }
  	      if($zSiEgrAh[0]=="L"){
  	        $zBandCarBan=1;
  	      }
  	      if($zSiEgrAh[0]=="F"){
  	      	$zBandFactu=1;
  	      }
  	    }
  	  }

  	  $qSqlCta  = "SELECT  * ";
			$qSqlCta .= "FROM $cAlfa.fpar0115 ";
			$qSqlCta .= "WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$_POST['cPucId']}\" AND ";
			$qSqlCta .= "regestxx = \"ACTIVO\" ";
			$xSqlCta  = f_MySql("SELECT","",$qSqlCta,$xConexion01,"");
			$zRCta = mysql_fetch_array($xSqlCta);

			if($_POST['cPucId']==""){
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			  $cMsj .= "Debe Escoger el No de Cuenta, \n";
			}
			if($_POST['cCtoId']==""){
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			  $cMsj .= "Debe Digitar el No de Concepto, \n";
			}

			if($_POST['cComMemo']=="|" || empty($_POST['cComMemo'])){
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			  $cMsj .= "Debe Escoger al menos un Comprobante, \n";
			}

			if($_POST['cCtoNit']==""){
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			  $cMsj .= "Debe Parametrizar el Tipo de Nit para Busqueda de Documeto Cruce. \n";
			}

			if($_POST['cCtoAnt']==""){
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			  $cMsj .= "Debe Parametrizar el Concepto para Anticipo, \n";
			}

			if($_POST['cCtoPcc']==""){
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			  $cMsj .= "Debe Parametrizar el Concepto para Pagos a Terceros, \n";
			}

			if($zBandP==1){
  			if($_POST['cCtoDesp']==""){
  			  $nSwitch = 1;
  			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe Digitar La Descripcion del Concepto, \n";
  			}

				if($vSysStr['financiero_aplica_nit1_nit2_integracion_sistema_contable'] == "SI"){
  				if($_POST['cCtoNit1p'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo P \n";
  				}

  				if($_POST['cCtoNit2p'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo P \n";
  				}
  			}
			} else {
			 $_POST['cCtoDesp'] = "";
			 $_POST['cCtoNit1p'] = "";
			 $_POST['cCtoNit2p'] = "";
			 $_POST['cCtoSucri'] = "";
			}

			#Validando la sucursal de Retencion ICA, esto aplica solo para las cuentas que empiezan por lo parametrizado en la variable del sistema y las P
			$vCtaRtIca = explode(",",$vSysStr['financiero_cuentas_reteica']);
			if (in_array(substr($_POST['cPucId'],0,4), $vCtaRtIca) && $zBandP == 1) {

  		  if ($_POST['cCtoSucri'] <> $_POST['cCtoSucriAnt'] && $_POST['cCtoSucriAnt'] <> ''){
	  		  //Validando que no sea un concepto hijo
	  		  $qCtoHijo  = "SELECT ctoidxxx, ctodesxx ";
	  		  $qCtoHijo .= "FROM $cAlfa.fpar0121 ";
	  		  $qCtoHijo .= "WHERE ";
	  		  $qCtoHijo .= "ctoctori LIKE \"%{$_POST['cCtoId']}|%\" OR ";
	  		  $qCtoHijo .= "ctoctorf LIKE \"{$_POST['cCtoId']}\" OR ";
	  		  $qCtoHijo .= "ctoctorv LIKE \"{$_POST['cCtoId']}\" OR ";
	  		  $qCtoHijo .= "ctoctorc LIKE \"{$_POST['cCtoId']}\" OR ";
	  		  $qCtoHijo .= "ctoctocp LIKE \"{$_POST['cCtoId']}\" LIMIT 0,1";
	  		  $xCtoHijo  = f_MySql("SELECT","",$qCtoHijo,$xConexion01,"");
	  		  //f_Mensaje(__FILE__,__LINE__,$qCtoHijo." ~ ".mysql_num_rows($xCtoHijo));
	  		  if (mysql_num_rows($xCtoHijo) > 0) {
	          $xRCH = mysql_fetch_array($xCtoHijo);
	          $nSwitch = 1;
	          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	          $cMsj .= "No Puede Cambiar la Sucursal Retencion ICA [{$_POST['cCtoSucriAnt']}] ";
	          $cMsj .= "porque este Concepto esta asociado al Concepto de Causacion Automatica [".$xRCH['ctoidxxx']." - ".$xRCH['ctodesxx']."],\n";
	        }
  		  }

	  		if($_POST['cCtoSucri']==""){
	  			$nSwitch = 1;
	  			$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	  			$cMsj .= "Debe Seleccionar la Sucursal Retencion ICA, \n";
	  		}
  		} else {
  			$_POST['cCtoSucri']="";
  		}

			if($zBandN==1){
  			if($_POST['cCtoDesn']==""){
  			  $nSwitch = 1;
  			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe Digitar La Descripcion del Concepto, \n";
  			}

				if($vSysStr['financiero_aplica_nit1_nit2_integracion_sistema_contable'] == "SI"){
  				if($_POST['cCtoNit1n'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo N \n";
  				}

  				if($_POST['cCtoNit2n'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo N \n";
  				}
  			}
			} else {
        $_POST['cCtoDesn'] = "";
        $_POST['cCtoNit1n'] = "";
        $_POST['cCtoNit2n'] = "";
      }

			if($zBandC==1){
  			if($_POST['cCtoDesc']==""){
  			  $nSwitch = 1;
  			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe Digitar La Descripcion del Concepto, \n";
  			}

				if($vSysStr['financiero_aplica_nit1_nit2_integracion_sistema_contable'] == "SI"){
  				if($_POST['cCtoNit1c'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo C \n";
  				}

  				if($_POST['cCtoNit2c'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo C \n";
  				}
  			}
			} else {
        $_POST['cCtoDesc'] = "";
        $_POST['cCtoNit1c'] = "";
        $_POST['cCtoNit2c'] = "";
      }
			if($zBandD==1){
  			if($_POST['cCtoDesd']==""){
  			  $nSwitch = 1;
  			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe Digitar La Descripcion del Concepto, \n";
  			}

				if($vSysStr['financiero_aplica_nit1_nit2_integracion_sistema_contable'] == "SI"){
  				if($_POST['cCtoNit1d'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo D \n";
  				}

  				if($_POST['cCtoNit2d'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo D \n";
  				}
  			}
			} else {
        $_POST['cCtoDesd'] = "";
        $_POST['cCtoNit1d'] = "";
        $_POST['cCtoNit2d'] = "";
      }

			if($zBandEgr==1){
			  if($_POST['cCtoDesg']==""){
  			  $nSwitch = 1;
  			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe Digitar La Descripcion del Concepto, \n";
  			}

  			if($_POST['cADoCruEgr']==""){
  			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe Parametrizar en los Datos Adicionales xra Egresos si aplica Do Cruce, Verifique \n";
  			  $nSwitch = 1;
  			}

				if($_POST['cCtoVlr01']==""){
  			  $_POST['cCtoVlr01']="NO";
  			}

				if($_POST['cCtoVlr02']==""){
  			  $_POST['cCtoVlr02']="NO";
  			}

				if($_POST['cCtoPvxxg']==""){
  			  $_POST['cCtoPvxxg']="NO";
  			}

  			$cCtoAntxg  = "NO";
      	$cCtoTfxxg  = "NO";
      	$cCtoDsacg  = "NO";
      	$cCtoNormal = "NO";

      	switch ($_POST['rTipoConcepto']){
      	  case "1":
      	    $cCtoAntxg = "SI";
      	  break;
      	  case "2":
      	    $cCtoTfxxg = "SI";
      	  break;
      	  case "3":
      	    $cCtoDsacg = "SI";
      	  break;
      	  case "4":
      	  	$cCtoNormal = "SI";
      	  break;
      	}

      	/**
      	 * En Tipo de Comprobante G, si el tipo de concepto es NORMAL y se escogio PAGO TRIBUTOS ADUANEROS, el DO Informativo,Concepto Informativo, Pago VUCE,
      	 * y calculo automatico de IVA deben ir en NO. En este caso solo Calculo Automatico BASE va en SI.
      	 */
      	if($cCtoNormal == "SI" && $_POST['cCtoPta'] == "SI"){
      		if($_POST['cADoCruEgr'] <> "NO"){//Do Informativo
      		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      	  	$cMsj .= "Para Tipo de Concepto NORMAL y Pago de Tributos Aduaneros, No aplica Do Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
  			  	$nSwitch = 1;
      		}
      		if($_POST['cAConCruEgr'] <> "NO"){//Cto Informativo
      		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      	  	$cMsj .= "Para Tipo de Concepto NORMAL y Pago de Tributos Aduaneros, No aplica Cto Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
  			  	$nSwitch = 1;
      		}
      		if($_POST['cCtoPvxxg'] <> "NO"){//Pago Vuce.
      		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      	  	$cMsj .= "Para Tipo de Concepto NORMAL y Pago de Tributos Aduaneros, No Aplica PAGO VUCE. Debe escoger una opcion diferente a SI, Verifique \n";
  			  	$nSwitch = 1;
      		}
      	}
      	/**
      	 * Fin validacion si el tipo de concepto es NORMAL y se escogio PAGO TRIBUTOS ADUANEROS, el DO Informativo,Concepto Informativo, Pago VUCE,
      	 * y calculo automatico de IVA deben ir en NO. En este caso solo Calculo Automatico BASE va en SI.
      	 */

      	/**
      	 * En Tipo de Comprobante G, si el tipo de concepto es NORMAL y se escogio PAGO VUCE, el DO Informativo, Concepto Informativo, Pago Tributos,
      	 * y Calculo automatico de IVA deben ir en NO.  En este caso solo Calculo automatico Base va en SI.
      	 */
      	elseif($cCtoNormal == "SI" && $_POST['cCtoPvxxg'] == "SI"){
	      	if($_POST['cADoCruEgr'] <> "NO"){//Do Informativo
	      	    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  	$cMsj .= "Para Tipo de Concepto NORMAL y Pago Vuce, No aplica Do Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  	$nSwitch = 1;
	      		}
	      		if($_POST['cAConCruEgr'] <> "NO"){//Cto Informativo
	      		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  	$cMsj .= "Para Tipo de Concepto NORMAL y Pago Vuce, No aplica Cto Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  	$nSwitch = 1;
	      		}
	      		if($_POST['cCtoPta'] <> "NO"){//Pago Tributos.
	      		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  	$cMsj .= "Para Tipo de Concepto NORMAL y Pago de Tributos Aduaneros, No Aplica PAGO VUCE. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  	$nSwitch = 1;
	      		}
      	}

      	/**
      	 * Fin de validacion si el tipo de concepto es NORMAL y se escogio PAGO VUCE, el DO Informativo, Concepto Informativo, Pago Tributos,
      	 * y Calculo automatico de IVA deben ir en NO.  En este caso solo Calculo automatico Base va en SI.
      	 */

      	/**
      	 * En Tipo de Comprobante G, si el tipo de concepto es NORMAL y se escogio DO informativo, Cto Informativo debe ir en SI, los demas campos [Pago Tributos,
      	 * Calculo Automatico Base, Calculo Automatico IVA, Pago Vuce] deben ir en NO.
      	 */
      	elseif($cCtoNormal == "SI" && ($_POST['cADoCruEgr'] == "SI" || $_POST['cAConCruEgr'] == "SI")){
      		$cDesCon = "";//Variable que carga si es Do Informativo o Cto Informativo.
      		if($_POST['cADoCruEgr'] == "SI"){
      			if($_POST['cAConCruEgr'] <> "SI"){
      			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      				$cMsj .= "Para Tipo de Concepto NORMAL y $cDesCon, Aplica Cto Informativo. Debe escoger una opcion diferente a NO, Verifique \n";
	  			  	$nSwitch = 1;
      			}
      			$cDesCon = "Do Informativo";
      		}else{
      			if($_POST['cADoCruEgr'] <> "SI"){
      			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      				$cMsj .= "Para Tipo de Concepto NORMAL y $cDesCon, Aplica Do Informativo. Debe escoger una opcion diferente a NO, Verifique \n";
	  			  	$nSwitch = 1;
      			}
      			$cDesCon = "Cto Informativo";
      		}
      		if($_POST['cCtoPta'] <> "NO"){//Pago Tributos.
      		    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  	$cMsj .= "Para Tipo de Concepto NORMAL y $cDesCon, No Aplica Pago Tributos. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  	$nSwitch = 1;
	      	}
      		if($_POST['cCtoPvxxg'] <> "NO"){//Calculo Automatico IVA.
      		    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  	$cMsj .= "Para Tipo de Concepto NORMAL y $cDesCon, No Aplica Pago Vuce. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  	$nSwitch = 1;
	      	}
      	}
      	/**
      	 * Fin Validacion si el tipo de concepto es NORMAL y se escogio DO informativo, Cto Informativo debe ir en SI, los demas campos [Pago Tributos,
      	 * Calculo Automatico Base, Calculo Automatico IVA, Pago Vuce] deben ir en NO.
      	 */

      	/**
      	 * En Tipo de Comprobante G, si se escogio Calculo Automatico de IVA, obligatoriamente debe ir en SI Calculo automatico Base.
      	 * y los demas campos [Pago Tributos,Calculo Automatico Base, Calculo Automatico IVA, Pago Vuce] deben ir en NO.
      	*/
      	//elseif($cCtoNormal == "SI" && $_POST['cCtoVlr2g'] == "SI"){
      	elseif($cCtoNormal == "SI"){
      		if($_POST['cADoCruEgr'] <> "NO"){//Do Informativo
      		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  $cMsj .= "Para Tipo de Concepto NORMAL y Calculo Automatico de IVA, No aplica Do Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  $nSwitch = 1;
	      	}
	      	if($_POST['cAConCruEgr'] <> "NO"){//Cto Informativo
	      	  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      		$cMsj .= "Para Tipo de Concepto NORMAL y Calculo Automatico de IVA, No aplica Cto Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  $nSwitch = 1;
	      	}
	      	if($_POST['cCtoPta'] <> "NO"){//Pago Tributos.
	      	  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      		$cMsj .= "Para Tipo de Concepto NORMAL y Calculo Automatico de IVA, No Aplica Pago Tributos. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  $nSwitch = 1;
	      	}
      		if($_POST['cCtoPvxxg'] <> "NO"){//Pago Vuce.
      		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      		$cMsj .= "Para Tipo de Concepto NORMAL y Calculo Automatico de IVA, No Aplica Pago Vuce. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  $nSwitch = 1;
	      	}
      	}
      	/**
      	 * Fin Validacion si se escogio Calculo Automatico de IVA, obligatoriamente debe ir en SI Calculo automatico Base.
      	 * y los demas campos [Pago Tributos,Calculo Automatico Base, Calculo Automatico IVA, Pago Vuce] deben ir en NO.
      	 */

      	/**
      	 * En Tipo de Comprobante G, si se parametrizar Calculo Automatico de IVA en NO, El Calculo automatico Base puede ir en SI/NO.
      	 * y los demas campos [Pago Tributos,Calculo Automatico Base, Calculo Automatico IVA, Pago Vuce] deben ir en NO.
      	*/
      	elseif($cCtoNormal == "SI"){
      	//elseif($cCtoNormal == "SI" && $_POST['cCtoVlr2g'] == "NO"){
      		if($_POST['cADoCruEgr'] <> "NO"){//Do Informativo
      		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  $cMsj .= "Para Tipo de Concepto NORMAL y Calculo Automatico de IVA, No aplica Do Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  $nSwitch = 1;
	      	}
	      	if($_POST['cAConCruEgr'] <> "NO"){//Cto Informativo
	      	  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      		$cMsj .= "Para Tipo de Concepto NORMAL y Calculo Automatico de IVA, No aplica Cto Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  $nSwitch = 1;
	      	}
	      	if($_POST['cCtoPta'] <> "NO"){//Pago Tributos.
	      	  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      		$cMsj .= "Para Tipo de Concepto NORMAL y Calculo Automatico de IVA, No Aplica Pago Tributos. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  $nSwitch = 1;
	      	}
      		if($_POST['cCtoPvxxg'] <> "NO"){//Pago Vuce.
      		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      		$cMsj .= "Para Tipo de Concepto NORMAL y Calculo Automatico de IVA, No Aplica Pago Vuce. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  $nSwitch = 1;
	      	}
      	}
      	/**
      	 * Fin Validacion si se parametrizar Calculo Automatico de IVA en NO, El Calculo automatico Base puede ir en SI/NO.
      	 * y los demas campos [Pago Tributos,Calculo Automatico Base, Calculo Automatico IVA, Pago Vuce] deben ir en NO.
      	 */

      	/**
      	 * Si tipo de Concepto es Tranferencia Fondos o Aplica cliente no aplica ninguna opcion
      	 */
      	if($cCtoTfxxg == "SI" || $cCtoDsacg == "SI"){
	      	if($_POST['cADoCruEgr'] <> "NO"){//Do Informativo
	      	  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  $cMsj .= "Para Tipo de Concepto Transferencia Fondos o Aplica cliente, No aplica Do Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  $nSwitch = 1;
	      	}
	      	if($_POST['cAConCruEgr'] <> "NO"){//Cto Informativo
	      	  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  $cMsj .= "Para Tipo de Concepto Transferencia Fondos o Aplica cliente, No aplica Cto Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  $nSwitch = 1;
	      	}
      		if($_POST['cCtoPta'] <> "NO"){//Pago Tributos.
      		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      		$cMsj .= "Para Tipo de Concepto Transferencia Fondos o Aplica cliente, No Aplica Pago Tributos. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  $nSwitch = 1;
	      	}
	      	if($_POST['cCtoPvxxg'] <> "NO"){//Pago Vuce.
	      	  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  $cMsj .= "Para Tipo de Concepto Transferencia Fondos ó Aplica cliente, No Aplica PAGO VUCE. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  $nSwitch = 1;
	      	}
      	}
      	/**
      	 * Fin validacion si tipo de Concepto es Tranferencia Fondos o Aplica Cliente no aplica ninguna opcion
      	 */

				if($vSysStr['financiero_aplica_nit1_nit2_integracion_sistema_contable'] == "SI"){
  				if($_POST['cCtoNit1g'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo G \n";
  				}

  				if($_POST['cCtoNit2g'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo G \n";
  				}
  			}

			  if($_POST['cDocInfG'] == ""){//Documento Informativo - Campo requerido para Aduanera Grancolombiana.
			    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Documento Informativo no puede ser vacio, \n ";
          $nSwitch = 1;
        }
			} else {
			  $_POST['cCtoDesg'] = "";
        $_POST['cADoCruEgr'] = "";
        $_POST['cAConCruEgr'] = "";
        $_POST['cDocInfG'] = "";
        $cCtoTfxxg = "";
        $cCtoDsacg = "";
        $cCtoAntxg = "";
        $_POST['cComMemoApl'] = "";
        $_POST['cCtoPta'] = "";
        $_POST['cCtoPvxxg'] = "";
        $_POST['cCtoNit1g'] = "";
        $_POST['cCtoNit2g'] = "";
			}

			if($zBandRec==1){
			  if($_POST['cCtoDesr']==""){
			    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe Digitar La Descripcion del Concepto, \n ";
  			  $nSwitch = 1;
  			}
  			if($_POST['cCtoAntxr']==""){
  			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe Parametrizar en los Datos Adicionales xra Recibos de Caja si aplica Anticipo, \n ";
  			  $nSwitch = 1;
  			}

				if($vSysStr['financiero_aplica_nit1_nit2_integracion_sistema_contable'] == "SI"){
  				if($_POST['cCtoNit1r'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo R \n";
  				}

  				if($_POST['cCtoNit2r'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo R \n";
  				}
  			}
			} else {
			  $_POST['cCtoDesr'] = "";
				$_POST['cCtoAntxr'] = "";
				$_POST['cCtoNit1r'] = "";
				$_POST['cCtoNit2r'] = "";
			}

			if($zBandCarBan==1){
			  if($_POST['cCtoDesL']==""){
  			  $nSwitch = 1;
  			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe Digitar La Descripcion del Concepto, \n";
  			}

  			if($_POST['cADoCruL']==""){
  			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe Parametrizar en los Datos Adicionales xra Cartas Bancarias si aplica Do Cruce, Verifique \n";
  			  $nSwitch = 1;
  			}

				if($_POST['cCtoVlr01']==""){
  			  $_POST['cCtoVlr01']="NO";
  			}
				if($_POST['cCtoVlr02']==""){
  			  $_POST['cCtoVlr02']="NO";
  			}
				if($_POST['cCtoPvxxL']==""){
  			  $_POST['cCtoPvxxL']="NO";
  			}

      	$cCtoDsacL  = "NO";
      	$cCtoNormalL = "NO";

      	switch ($_POST['rTipoConceptoL']){
      	  case "1":
      	  	$cCtoNormalL = "SI";
      	  break;
      	  case "2":
      	    $cCtoDsacL = "SI";
      	  break;
      	}

      	/**
      	 * Inicio Validacion
      	 * En Tipo de Comprobante L, si el tipo de concepto es NORMAL y se escogio PAGO TRIBUTOS ADUANEROS,
      	 * el DO Informativo, Concepto Informativo, Pago VUCE y calculo automatico de IVA deben ir en NO.
      	 * En este caso solo Calculo Automatico BASE va en SI.
      	*/
      	if($cCtoNormalL == "SI" && $_POST['cCtoPtaL'] == "SI"){
      		if($_POST['cADoCruL'] <> "NO"){//Do Informativo
      		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      	  	$cMsj .= "Para Tipo de Concepto NORMAL y Pago de Tributos Aduaneros, No aplica Do Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
  			  	$nSwitch = 1;
      		}
      		if($_POST['cAConCruL'] <> "NO"){//Cto Informativo
      		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      	  	$cMsj .= "Para Tipo de Concepto NORMAL y Pago de Tributos Aduaneros, No aplica Cto Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
  			  	$nSwitch = 1;
      		}
      		if($_POST['cCtoPvxxL'] <> "NO"){//Pago Vuce.
      		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      	  	$cMsj .= "Para Tipo de Concepto NORMAL y Pago de Tributos Aduaneros, No Aplica PAGO VUCE. Debe escoger una opcion diferente a SI, Verifique \n";
  			  	$nSwitch = 1;
      		}
      	}
      	/**
      	 * Fin Validacion
      	 * En Tipo de Comprobante L, si el tipo de concepto es NORMAL y se escogio PAGO TRIBUTOS ADUANEROS,
      	 * el DO Informativo, Concepto Informativo, Pago VUCE y calculo automatico de IVA deben ir en NO.
      	 * En este caso solo Calculo Automatico BASE va en SI.
      	*/

      	/**
      	 * Inicio Validacion
      	 * En Tipo de Comprobante L, si el tipo de concepto es NORMAL y se escogio PAGO VUCE,
      	 * el DO Informativo, Concepto Informativo, Pago Tributos y Calculo automatico de IVA deben ir en NO.
      	 * En este caso solo Calculo Automatico Base va en SI.
      	*/
      	if($cCtoNormalL == "SI" && $_POST['cCtoPvxxL'] == "SI"){
	      	if($_POST['cADoCruL'] <> "NO"){//Do Informativo
	      	  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  $cMsj .= "Para Tipo de Concepto NORMAL y Pago Vuce, No aplica Do Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  $nSwitch = 1;
	      	}
	      	if($_POST['cAConCruL'] <> "NO"){//Cto Informativo
	      	  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  $cMsj .= "Para Tipo de Concepto NORMAL y Pago Vuce, No aplica Cto Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  $nSwitch = 1;
	      	}
	      	if($_POST['cCtoPtaL'] <> "NO"){//Pago Tributos.
	      	  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  $cMsj .= "Para Tipo de Concepto NORMAL y Pago Vuce, No Aplica pago de Tributos Aduaneros. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  $nSwitch = 1;
	      	}
      	}
      	/**
      	 * Fin Validacion
      	 * En Tipo de Comprobante L, si el tipo de concepto es NORMAL y se escogio PAGO VUCE,
      	 * el DO Informativo, Concepto Informativo, Pago Tributos y Calculo automatico de IVA deben ir en NO.
      	 * En este caso solo Calculo Automatico Base va en SI.
      	*/

      	/**
      	 * Inicio Validacion
      	 * En Tipo de Comprobante L, si el tipo de concepto es NORMAL y se escogio DO informativo el Cto Informativo debe ir en SI,
      	 * los demas campos [Pago Tributos, Pago Vuce, Calculo Automatico Base, Calculo Automatico IVA] deben ir en NO.
      	*/
      	if($cCtoNormalL == "SI" && ($_POST['cADoCruL'] == "SI" || $_POST['cAConCruL'] == "SI")){
      		$cDesConL = "";//Variable que carga si es Do Informativo o Cto Informativo.
      		if($_POST['cADoCruL'] == "SI"){
      			$cDesConL = "DO INFORMATIVO";
      			if($_POST['cAConCruL'] <> "SI"){
      			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      				$cMsj .= "Para Tipo de Concepto NORMAL y $cDesConL, Aplica Cto Informativo. Debe escoger una opcion diferente a NO, Verifique \n";
	  			  	$nSwitch = 1;
      			}
      		}else{
      			$cDesConL = "CTO INFORMATIVO";
      			if($_POST['cADoCruL'] <> "SI"){
      			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      				$cMsj .= "Para Tipo de Concepto NORMAL y $cDesConL, Aplica Do Informativo. Debe escoger una opcion diferente a NO, Verifique \n";
	  			  	$nSwitch = 1;
      			}
      		}
      		if($_POST['cCtoPtaL'] <> "NO"){//Pago Tributos.
      		    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  	$cMsj .= "Para Tipo de Concepto NORMAL y $cDesConL, No Aplica Pago Tributos. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  	$nSwitch = 1;
	      	}
      		if($_POST['cCtoPvxxL'] <> "NO"){//Pago VUCE
      		    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  	$cMsj .= "Para Tipo de Concepto NORMAL y $cDesConL, No Aplica Pago Vuce. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  	$nSwitch = 1;
	      	}
      	}
      	/**
      	 * Fin Validacion
      	 * En Tipo de Comprobante L, si el tipo de concepto es NORMAL y se escogio DO informativo el Cto Informativo debe ir en SI,
      	 * los demas campos [Pago Tributos, Pago Vuce, Calculo Automatico Base, Calculo Automatico IVA] deben ir en NO.
      	*/

       	/**
       	 * Inicio Validacion
      	 * Si tipo de Concepto es Aplica cliente no aplica ninguna opcion
      	*/
      	if($cCtoDsacL == "SI"){
	      	if($_POST['cADoCruL'] <> "NO"){//Do Informativo
	      	  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  $cMsj .= "Para Tipo de Concepto APLICA CLIENTE, No aplica Do Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  $nSwitch = 1;
	      	}
	      	if($_POST['cAConCruL'] <> "NO"){//Cto Informativo
	      	  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  $cMsj .= "Para Tipo de Concepto APLICA CLIENTE, No aplica Cto Informativo. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  $nSwitch = 1;
	      	}
      		if($_POST['cCtoPtaL'] <> "NO"){//Pago Tributos.
      		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      		$cMsj .= "Para Tipo de Concepto APLICA CLIENTE, No Aplica Pago Tributos. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  $nSwitch = 1;
	      	}
	      	if($_POST['cCtoPvxxL'] <> "NO"){//Pago Vuce.
	      	  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      	  $cMsj .= "Para Tipo de Concepto APLICA CLIENTE, No Aplica PAGO VUCE. Debe escoger una opcion diferente a SI, Verifique \n";
	  			  $nSwitch = 1;
	      	}
      	}
       	/**
       	 * Fin Validacion
      	 * Si tipo de Concepto es Aplica cliente no aplica ninguna opcion
      	*/

				if($vSysStr['financiero_aplica_nit1_nit2_integracion_sistema_contable'] == "SI"){
  				if($_POST['cCtoNit1L'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo L \n";
  				}

  				if($_POST['cCtoNit2L'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo L \n";
  				}
  			}

      	if($_POST['cDocInfL'] == ""){//Documento Informativo - Campo requerido para Aduanera Grancolombiana (2012-02-15).
      	  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      	  $cMsj .= "El Documento Informativo no puede ser vacio, \n ";
  			  $nSwitch = 1;
      	}
			} else {
			  $_POST['cCtoDesL']="";
        $_POST['cADoCruL']="";
        $_POST['cAConCruL']="";
        $_POST['cDocInfL']="";
        $cCtoDsacL="";
        $_POST['cCtoPtaL']="";
        $_POST['cCtoPvxxL']="";
        $_POST['cCtoNit1L']="";
        $_POST['cCtoNit2L']="";
        $_POST['cComMemoApl']="";
			}

			if($zBandCajMe==1){
			  if($_POST['cCtoDesm']==""){
			    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe Digitar La Descripcion del Concepto, \n ";
  			  $nSwitch = 1;
  			}
  			if($_POST['cCtoTpaxm']==""){
  			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  $cMsj .= "Debe Parametrizar en los Datos Adicionales xra Caja Menor si aplica Tipo de Pago, \n ";
  			  $nSwitch = 1;
  			}

				if($vSysStr['financiero_aplica_nit1_nit2_integracion_sistema_contable'] == "SI"){
  				if($_POST['cCtoNit1m'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo M \n";
  				}

  				if($_POST['cCtoNit2m'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo M \n";
  				}
  			}
			} else {
			  $_POST['cCtoDesm'] = "";
        $_POST['cCtoTpaxm'] = "";
        $_POST['cCtoNit1m'] = "";
        $_POST['cCtoNit2m'] = "";
			}

			if($zBandFactu==1){
				$mComprobantes = f_Explode_Array($_POST['cComMemo'],"|","~");
				for ($i=0;$i<count($mComprobantes);$i++) {
					if ($mComprobantes[$i][0] == "F") {
		      	// Validar que el Concepto (fpar0119) no se repita con la misma clase de concepto
						$qSqlCto  = "SELECT * ";
						$qSqlCto .= "FROM $cAlfa.fpar0119 ";
						$qSqlCto .= "WHERE ";
						$qSqlCto .= "ctocomxx LIKE \"%{$mComprobantes[$i][0]}~{$mComprobantes[$i][1]}%\" AND ";
						$qSqlCto .= "ctoidxxx <> \"{$_POST['cCtoId']}\" AND ";
						$qSqlCto .= "ctoclaxf = \"{$_POST['cCtoClaxf']}\" AND ";
						$qSqlCto .= "regestxx = \"ACTIVO\"";
						$xSqlCto  = f_MySql("SELECT","",$qSqlCto,$xConexion01,"");
						//f_Mensaje(__FILE__,__LINE__,$qSqlCto." ~ ".mysql_num_rows($xSqlCto));
						if (mysql_num_rows($xSqlCto) > 0) {
							$cCtoCad = "";
							while ($zRCto = mysql_fetch_array($xSqlCto)) {
								$cCtoCad .= "[".$zRCto['ctoidxxx']."-".$zRCto['regestxx']."], ";
							}
							$cCtoCad = substr($cCtoCad,0,strlen($cCtoCad)-2);
							$cMsj .= "Clase de Concepto [".$_POST['cCtoClaxf']."] ya se Encuentra Parametrizado para el Comprobante ";
							$cMsj .= "[{$mComprobantes[$i][0]}~{$mComprobantes[$i][1]}]\n";
							$nSwitch = 1;
		        }


		        /**
		         * Para Las siguientes clases de concepto no pueden existir su equivalente en (IP+PCC)
		         */
		        $vClases = array("SAGENCIA","SAGENCIAUSD","SAGENCIAIP","SAGENCIAUSDIP","SAGENCIAPCC","SAGENCIAUSDPCC");

		        if (in_array($_POST['cCtoClaxf'], $vClases) == true) {

		        	$mClaCon["SAGENCIA"]       = "\"SAGENCIAIP\",\"SAGENCIAPCC\"";    //Equivalencia con IP y PCC
		        	$mClaCon["SAGENCIAIP"]     = "\"SAGENCIA\"";    //Equivalencia con IP+PCC
		        	$mClaCon["SAGENCIAPCC"]    = "\"SAGENCIA\"";    //Equivalencia con IP+PCC

		        	$mClaCon["SAGENCIAUSD"]    = "\"SAGENCIAUSDIP\",\"SAGENCIAUSDPCC\"";    //Equivalencia con IP y PCC
		        	$mClaCon["SAGENCIAUSDIP"]  = "\"SAGENCIAUSD\""; //Equivalencia con IP+PCC
		        	$mClaCon["SAGENCIAUSDPCC"] = "\"SAGENCIAUSD\""; //Equivalencia con IP+PCC

		        	// Validar que el Concepto (fpar0119) no se repita con la misma clase de concepto para (IP+PCC)
		        	$qSqlCto  = "SELECT * ";
		        	$qSqlCto .= "FROM $cAlfa.fpar0119 ";
		        	$qSqlCto .= "WHERE ";
		        	$qSqlCto .= "ctocomxx LIKE \"%{$mComprobantes[$i][0]}~{$mComprobantes[$i][1]}%\" AND ";
		        	$qSqlCto .= "ctoidxxx <> \"{$_POST['cCtoId']}\" AND ";
		        	$qSqlCto .= "ctoclaxf IN (".$mClaCon["{$_POST['cCtoClaxf']}"].") AND ";
		        	$qSqlCto .= "regestxx = \"ACTIVO\"";
		        	$xSqlCto  = f_MySql("SELECT","",$qSqlCto,$xConexion01,"");
		        	//f_Mensaje(__FILE__,__LINE__,$qSqlCto." ~ ".mysql_num_rows($xSqlCto));
		        	if (mysql_num_rows($xSqlCto) > 0) {
		        		$cCtoCad = "";
		        		while ($zRCto = mysql_fetch_array($xSqlCto)) {
		        			$cCtoCad .= "[".$zRCto['ctoidxxx']."-".$zRCto['regestxx']."], ";
		        		}
		        		$cCtoCad = substr($cCtoCad,0,strlen($cCtoCad)-2);
		        		$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
		        		$cMsj .= "La Clase de Concepto [".$_POST['cCtoClaxf']."] ya se Encuentra Parametrizado para el Comprobante ";
		        		$cMsj .= "[{$mComprobantes[$i][0]}~{$mComprobantes[$i][1]}] con la Clase de Concepto [".str_replace("\"", "", $mClaCon["{$_POST['cCtoClaxf']}"])."].\n";
		        		$nSwitch = 1;
		        	}
		        } ## if (in_array($_POST['cCtoClaxf'], $vClases) == true) { ##

					}//if ($mComprobantes[$i][0] == "F") {
		    }//for ($i=0;$i<count($mComprobantes);$i++) {
				if($_POST['cCtoDesf']==""){
  			  $cMsj .= "Debe Digitar La Descripcion del Concepto, \n ";
  			  $nSwitch = 1;
  			}
  			if($_POST['cCtoClaxf']==""){
  			  $cMsj .= "Debe Parametrizar en los Datos Adicionales xra Facturacion la Clase del Concepto, \n ";
  			  $nSwitch = 1;
  			}

				if($vSysStr['financiero_aplica_nit1_nit2_integracion_sistema_contable'] == "SI"){
  				if($_POST['cCtoNit1f'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo F \n";
  				}

  				if($_POST['cCtoNit2f'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo F \n";
  				}
  			}

			} else {
			  $_POST['cCtoDesf'] = "";
        $_POST['cCtoClaxf'] = "";
        $_POST['cCtoNit1f'] = "";
        $_POST['cCtoNit2f'] = "";
			}

			/**
       * Codigo de Integracion con E2K
       */
      if ($cAlfa == "UPSXXXXX" || $cAlfa == "TEUPSXXXXX" || $cAlfa == "TEUPSXXXXP" || $cAlfa == "DEUPSXXXXX") {

        /**
         * Si la cuenta detalla por DO y la base de datos es de UPS se obliga a digitar el codigo E2K
         */
        if ($zRCta['pucdetxx'] == 'D' && $_POST['cCtoE2k'] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Codigo de Integracion para E2K no Puede Ser Vacio.\n";
        }

       /**
        * Valido que sean solo alfanumericos
        */

        if ($_POST['cCtoE2k'] != "" && !preg_match("/^[[:alnum:]]+$/", $_POST['cCtoE2k'])) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Codigo de Integracion para E2K debe ser Alfanumerico.\n";
        }
      }

      if ($_POST['cNuAsBel'] != ""){ // Valida si es n�mero la asignacion para la Integracion de Belcorp 2013-06-12
      	if (!is_numeric($_POST['cNuAsBel'])) {
      		$nSwitch  = 1;
      		$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      		$cMsj .= "El N&uacute;mero de Asignaci&oacute;n debe ser N&uacute;merico. \n";
      	}else{
      		if ($_POST['cNuAsBel'] == 0) {
      			$nSwitch  = 1;
      			$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      			$cMsj .= "El N&uacute;mero de Asignaci&oacute;n debe ser Mayor a Cero. \n";
      		}
      	}
      }

			/*** Si la variable Categoriacion Conceptos Factuacion esta encendida se habilita el menu de Categoria Conceptos***/
			if($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI'){
				/*** Valido que la categoria concepto exista en el sistema.***/
				$qCatCon  = "SELECT * ";
				$qCatCon .= "FROM $cAlfa.fpar0144 ";
				$qCatCon .= "WHERE ";
				$qCatCon .= "cacidxxx = \"{$_POST['cCacId']}\" LIMIT 0,1 ";
				$xCatCon  = f_MySql("SELECT","",$qCatCon,$xConexion01,"");
				// f_Mensaje(__FILE__,__LINE__,$qCatCon."~".mysql_num_rows($xCatCon));
				if(mysql_num_rows($xCatCon) == 0 && $_POST['cCacId'] != ""){
					$nSwitch  = 1;
					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMsj .= "La Categoria Concepto es invalida. \n";
				}
			}

			/*** Valido que el Codigo de Compra Eficiente Exista en la Base de Datos y no se Encuentre Inactivo ***/
			if($_POST['cClasProd'] == "001" && $_POST['cCceId'] == ""){
				$nSwitch  = 1;
				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "El Campo Colombia Compra Eficiente no puede ser vacio. \n";
			}

			if($_POST['cCceId'] != ""){
				$qComEfi  = "SELECT regestxx ";
				$qComEfi .= "FROM $cAlfa.fpar0156 ";
				$qComEfi .= "WHERE ";
				$qComEfi .= "cceidxxx = \"{$_POST['cCceId']}\" LIMIT 0,1 ";
				$xComEfi  = f_MySql("SELECT","",$qComEfi,$xConexion01,"");

				if(mysql_num_rows($xComEfi) == 0){
					$nSwitch  = 1;
					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMsj .= " El codigo Colombia Compra Eficiente[".$_POST['cCceId']."], No Existe en la Base de Datos. \n";
				}else{
					$vComEfi = mysql_fetch_array($xComEfi);
					if($vComEfi['regestxx'] == "INACTIVO"){
						$nSwitch  = 1;
						$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						$cMsj .= " El codigo Colombia Compra Eficiente[".$_POST['cCceId']."], Se Encuentra INACTIVO. \n";
					}
				}
			}

			/*** Valido que la Unidad de Medida Exista en la Base de Datos y no se Encuentre Inactiva ***/
			if($_POST['cUmeId'] != ""){
				$qUniMed  = "SELECT regestxx ";
				$qUniMed .= "FROM $cAlfa.fpar0157 ";
				$qUniMed .= "WHERE ";
				$qUniMed .= "umeidxxx = \"{$_POST['cUmeId']}\" LIMIT 0,1 ";
				$xUniMed  = f_MySql("SELECT","",$qUniMed,$xConexion01,"");

				if(mysql_num_rows($xUniMed) == 0){
					$nSwitch  = 1;
					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMsj .= " La Unidad de Medida[".$_POST['cUmeId']."], No Existe en la Base de Datos. \n";
				}else{
					$vUniMed = mysql_fetch_array($xUniMed);
					if($vUniMed['regestxx'] == "INACTIVO"){
						$nSwitch  = 1;
						$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						$cMsj .= " La Unidad de Medida[".$_POST['cUmeId']."], se Encuentra INACTIVA. \n";
					}
				}
			}

			$cLineaNeg = "";
			$vBDIntegracionColmasSap = explode("~",$vSysStr['system_integracion_colmas_sap']);
			if (in_array($cAlfa, $vBDIntegracionColmasSap) == true) {
				// Validando las lineas de ingreso
				for ($i=0;$i<$_POST['nSecuencia_Grid_LineaNegocio'];$i++) {
					if ($_POST['cCodLineaNeg' . ($i+1)] != "") {
						$cLineaNeg .= $_POST['cCodLineaNeg' . ($i+1)]."~".$_POST['cCtaIngreso' . ($i+1)]."~".$_POST['cCtaCosto' . ($i+1)]."|";

						// Convierte la cuenta a un texto con formato numerico valido
						$cCtaIngreso = str_replace(',', '.', $_POST['cCtaIngreso' . ($i+1)]);
						$cCtaCosto   = str_replace(',', '.', $_POST['cCtaCosto' . ($i+1)]);

						// Validando la linea de negocio
						$qLineaNeg  = "SELECT ";
						$qLineaNeg .= "regestxx ";
						$qLineaNeg .= "FROM $cAlfa.zcol0003 ";
						$qLineaNeg .= "WHERE ";
						$qLineaNeg .= "lnecodxx = \"{$_POST['cCodLineaNeg' . ($i+1)]}\" LIMIT 0,1";
						$xLineaNeg  = f_MySql("SELECT","",$qLineaNeg,$xConexion01,"");
						if (mysql_num_rows($xLineaNeg) > 0) {
							$vLineaNeg = mysql_fetch_array($xLineaNeg);
							if ($vLineaNeg['regestxx'] == "INACTIVO") {
								$nSwitch  = 1;
								$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
								$cMsj .= " La Linea de Negocio[".$_POST['cCodLineaNeg' . ($i+1)]."], se Encuentra INACTIVA, Secuencia [".($i+1)."]. \n";
							}
						} else {
							$nSwitch  = 1;
							$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMsj .= " La Linea de Negocio[".$_POST['cCodLineaNeg' . ($i+1)]."], No Existe, Secuencia [".($i+1)."]. \n";
						}

						if ($_POST['cCtaIngreso' . ($i+1)] == "" && $_POST['cCtaCosto' . ($i+1)] == "") {
							$nSwitch  = 1;
							$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMsj .= " Debe Ingresar la Cuenta de Ingreso o la Cuenta de Costo, Secuencia [".($i+1)."]. \n";
						}

						if ((!is_numeric($cCtaIngreso) && $cCtaIngreso != "") || ($cCtaIngreso != "" && trim($cCtaIngreso) == "")) {
							$nSwitch  = 1;
							$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMsj .= " Debe Ingresar un Tipo de Dato Numerico en la Cuenta de Ingreso, Secuencia [".($i+1)."]. \n";
						}

						if ((!is_numeric($cCtaCosto) && $cCtaCosto != "") || ($cCtaCosto != "" && trim($cCtaCosto) == "")) {
							$nSwitch  = 1;
							$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMsj .= " Debe Ingresar un Tipo de Dato Numerico en la Cuenta de Costo, Secuencia [".($i+1)."]. \n";
						}
					}
				}
			}
		break;
	}

  //f_Mensaje(__FILE__,__LINE__,"{$_POST['cCtoId']}");

	switch ($_COOKIE['kModo']) {
  	case "NUEVO":
	    if ($nSwitch == 0 ) {
		    $zInsertCsc = array(array('NAME'=>'pucidxxx','VALUE'=>$_POST['cPucId']                              ,'CHECK'=>'SI'),
		                        array('NAME'=>'ctocscxx','VALUE'=>$_POST['cCtoId']                              ,'CHECK'=>'SI'),
		                        array('NAME'=>'ctoidxxx','VALUE'=>substr($_POST['cPucId'],0,6).$_POST['cCtoId'] ,'CHECK'=>'SI'),
		                        array('NAME'=>'ctocomxx','VALUE'=>$_POST['cComMemo']	                          ,'CHECK'=>'SI'),
		                        array('NAME'=>'ctovlr01','VALUE'=>$_POST['cCtoVlr01']	                          ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctovlr02','VALUE'=>$_POST['cCtoVlr02']	                          ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonitxx','VALUE'=>$_POST['cCtoNit']                             ,'CHECK'=>'SI'),
		                        array('NAME'=>'ctoantxx','VALUE'=>$_POST['cCtoAnt']                             ,'CHECK'=>'SI'),
		                        array('NAME'=>'ctopccxx','VALUE'=>$_POST['cCtoPcc']                             ,'CHECK'=>'SI'),
		                        /*** Comprobantes Tipo P ***/
		                        array('NAME'=>'ctodesxp','VALUE'=>$_POST['cCtoDesp']                            ,'CHECK'=>'NO'),
  								    	    array('NAME'=>'ctonit1p','VALUE'=>$_POST['cCtoNit1p']	                          ,'CHECK'=>'NO'),
  								    	    array('NAME'=>'ctonit2p','VALUE'=>$_POST['cCtoNit2p']	                          ,'CHECK'=>'NO'),
  								    	    array('NAME'=>'ctosucri','VALUE'=>$_POST['cCtoSucri']                           ,'CHECK'=>'NO'), //Sucural Retencion ICA
  								    	    /*** Egresos ****/
		                        array('NAME'=>'ctodesxg','VALUE'=>$_POST['cCtoDesg']                            ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctodocxg','VALUE'=>$_POST['cADoCruEgr']                          ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctoctocg','VALUE'=>$_POST['cAConCruEgr']                         ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctodocig','VALUE'=>$_POST['cDocInfG']                            ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctotfxxg','VALUE'=>$cCtoTfxxg                                    ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctodsacg','VALUE'=>$cCtoDsacg                                    ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctoantxg','VALUE'=>$cCtoAntxg                                    ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctoaplxg','VALUE'=>$_POST['cComMemoApl']                         ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctoptaxg','VALUE'=>$_POST['cCtoPta']                             ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctopvxxg','VALUE'=>$_POST['cCtoPvxxg']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit1g','VALUE'=>$_POST['cCtoNit1g']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit2g','VALUE'=>$_POST['cCtoNit2g']                           ,'CHECK'=>'NO'),
		                        /*** Recibos de caja ***/
		                        array('NAME'=>'ctodesxr','VALUE'=>$_POST['cCtoDesr']                            ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctoantxr','VALUE'=>$_POST['cCtoAntxr']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit1r','VALUE'=>$_POST['cCtoNit1r']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit2r','VALUE'=>$_POST['cCtoNit2r']                           ,'CHECK'=>'NO'),
		                        //Cartas bancarias Nuevo
		                        array('NAME'=>'ctodesxl','VALUE'=>$_POST['cCtoDesL']                            ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctodocxl','VALUE'=>$_POST['cADoCruL']                         		,'CHECK'=>'NO'),
		                        array('NAME'=>'ctoctocl','VALUE'=>$_POST['cAConCruL']                        		,'CHECK'=>'NO'),
		                        array('NAME'=>'ctodocil','VALUE'=>$_POST['cDocInfL']                        		,'CHECK'=>'NO'),
														array('NAME'=>'ctoapcon','VALUE'=>$_POST['cApConBan']                        		,'CHECK'=>'NO'),
		                        array('NAME'=>'ctodsacl','VALUE'=>$cCtoDsacL                                    ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctoaplxl','VALUE'=>$_POST['cComMemoApl']                         ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctoptaxl','VALUE'=>$_POST['cCtoPtaL']                            ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctopvxxl','VALUE'=>$_POST['cCtoPvxxL']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit1l','VALUE'=>$_POST['cCtoNit1L']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit2l','VALUE'=>$_POST['cCtoNit2L']                           ,'CHECK'=>'NO'),
		                        /**** Recibos de Caja Menor **/
		                        array('NAME'=>'ctodesxm','VALUE'=>$_POST['cCtoDesm']                            ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctotpaxm','VALUE'=>$_POST['cCtoTpaxm']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit1m','VALUE'=>$_POST['cCtoNit1m']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit2m','VALUE'=>$_POST['cCtoNit2m']                           ,'CHECK'=>'NO'),
		                        /*** Facturas ***/
		                        array('NAME'=>'ctodesxf','VALUE'=>$_POST['cCtoDesf']                            ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctoclaxf','VALUE'=>$_POST['cCtoClaxf']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit1f','VALUE'=>$_POST['cCtoNit1f']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit2f','VALUE'=>$_POST['cCtoNit2f']                           ,'CHECK'=>'NO'),
		                        /***  Comprobantes Tipo N ***/
		                        array('NAME'=>'ctodesxn','VALUE'=>$_POST['cCtoDesn']                            ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit1n','VALUE'=>$_POST['cCtoNit1n']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit2n','VALUE'=>$_POST['cCtoNit2n']                           ,'CHECK'=>'NO'),
		                        /*** Comprobantes Tipo C ***/
		                        array('NAME'=>'ctodesxc','VALUE'=>$_POST['cCtoDesc']                            ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit1c','VALUE'=>$_POST['cCtoNit1c']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit2c','VALUE'=>$_POST['cCtoNit2c']                           ,'CHECK'=>'NO'),
		                        /*** Comprobantes Tipo D ***/
		                        array('NAME'=>'ctodesxd','VALUE'=>$_POST['cCtoDesd']                            ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit1d','VALUE'=>$_POST['cCtoNit1d']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit2d','VALUE'=>$_POST['cCtoNit2d']                           ,'CHECK'=>'NO'),
		                        /*** Codigo de Integracion con E2K ***/
		                        array('NAME'=>'ctoe2kxx','VALUE'=>$_POST['cCtoE2k']                             ,'CHECK'=>'NO'),
														/***  Codigo Homologacion Aladuanas ***/
														array('NAME'=>'ctochald','VALUE'=>$_POST['cCtoChAld']                           ,'CHECK'=>'NO'),
		    										/***  Codigo Integracion con Belcorp***/
		    										array('NAME'=>'pucadbel','VALUE'=>trim($_POST['cPucBel'])                       ,'CHECK'=>'NO'), //C�digo Integraci�n Belcorp
		    										array('NAME'=>'pucadnas','VALUE'=>trim($_POST['cNuAsBel'])                      ,'CHECK'=>'NO'), //N�mero Asignaci�n Belcorp
  								    	    /*** Categoria concepto ***/
  								    	    array('NAME'=>'cacidxxx','VALUE'=>trim($_POST['cCacId'])                        ,'CHECK'=>'NO'),
                            /*** Integracion SAP ***/
														array('NAME'=>'ctosapid','VALUE'=>trim($_POST['cCtoSapId'])                     ,'CHECK'=>'NO'),
                            array('NAME'=>'ctosapcx','VALUE'=>trim($_POST['cCtoSapC'])                      ,'CHECK'=>'NO'),
                            array('NAME'=>'ctosapix','VALUE'=>trim($_POST['cCtoSapI'])                      ,'CHECK'=>'NO'),
                            array('NAME'=>'ctosapic','VALUE'=>trim($_POST['cCtoSapIc'])                     ,'CHECK'=>'NO'),
                            array('NAME'=>'ctosapiv','VALUE'=>trim($_POST['cCtoSapIv'])                     ,'CHECK'=>'NO'),
                            array('NAME'=>'ctosapca','VALUE'=>trim($_POST['cCtoSapCA'])                     ,'CHECK'=>'NO'),
                            array('NAME'=>'ctosapli','VALUE'=>trim($_POST['cCtoSapLI'])                     ,'CHECK'=>'NO'),
														array('NAME'=>'ctosaple','VALUE'=>trim($_POST['cCtoSapLE'])                     ,'CHECK'=>'NO'),
														/*** Clasificacion Producto ***/
														array('NAME'=>'ctoclapr','VALUE'=>trim($_POST['cCtoClapr'])                     ,'CHECK'=>'NO'),
		    										/*** Codigo Colombia Compra Eficiente ***/														
														array('NAME'=>'cceidxxx','VALUE'=>trim($_POST['cCceId'])                        ,'CHECK'=>'NO'),
														/*** Unidad de Medida ***/														
  								    	    array('NAME'=>'umeidxxx','VALUE'=>trim($_POST['cUmeId'])                        ,'CHECK'=>'NO'),
  								    	    array('NAME'=>'ctolineg','VALUE'=>trim($cLineaNeg)															,'CHECK'=>'NO'),
  								    	    array('NAME'=>'ctopuc85','VALUE'=>trim($_POST['cCtoPuc85'])                     ,'CHECK'=>'NO'),
														array('NAME'=>'ctocwccx','VALUE'=>trim($_POST['cCtocWccX'])  	      						,'CHECK'=>'NO'),
		    										array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId']))          ,'CHECK'=>'SI'),
  								    	    array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')												          ,'CHECK'=>'SI'),
					 									array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')		                              ,'CHECK'=>'SI'),
					 									array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												          ,'CHECK'=>'SI'),
					 									array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                              ,'CHECK'=>'SI'),
														array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cEstado']))           ,'CHECK'=>'SI'));
    		if (f_MySql("INSERT","fpar0119",$zInsertCsc,$xConexion01,$cAlfa)){
    		}else{
    			$cMsj .= "Error al Guardar los Datos [fpar0119], Verifique ";
			    $nSwitch = 1;
    		}
	    }
  	break;
  	case "EDITAR":
	    if ($nSwitch == 0 ) {
	      //f_Mensaje(__FILE__,__LINE__,"entre");
		    $zInsertCsc = array(array('NAME'=>'ctocomxx','VALUE'=>$_POST['cComMemo']	                          ,'CHECK'=>'SI'),
		    										array('NAME'=>'ctovlr01','VALUE'=>$_POST['cCtoVlr01']	                          ,'CHECK'=>'NO'),
		    										array('NAME'=>'ctovlr02','VALUE'=>$_POST['cCtoVlr02']	                          ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonitxx','VALUE'=>$_POST['cCtoNit']                             ,'CHECK'=>'SI'),
		                        array('NAME'=>'ctoantxx','VALUE'=>$_POST['cCtoAnt']                             ,'CHECK'=>'SI'),
		                        array('NAME'=>'ctopccxx','VALUE'=>$_POST['cCtoPcc']                             ,'CHECK'=>'SI'),
		                        array('NAME'=>'ctodesxp','VALUE'=>$_POST['cCtoDesp']                            ,'CHECK'=>'NO'),
  								    	    array('NAME'=>'ctonit1p','VALUE'=>$_POST['cCtoNit1p']	                          ,'CHECK'=>'NO'),
  								    	    array('NAME'=>'ctonit2p','VALUE'=>$_POST['cCtoNit2p']	                          ,'CHECK'=>'NO'),
  								    	    array('NAME'=>'ctosucri','VALUE'=>$_POST['cCtoSucri']                           ,'CHECK'=>'NO'), //Sucural Retencion ICA
		                        array('NAME'=>'ctodesxg','VALUE'=>$_POST['cCtoDesg']                            ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctodocxg','VALUE'=>$_POST['cADoCruEgr']                          ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctoctocg','VALUE'=>$_POST['cAConCruEgr']                         ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctodocig','VALUE'=>$_POST['cDocInfG']                            ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctotfxxg','VALUE'=>$cCtoTfxxg                                    ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctodsacg','VALUE'=>$cCtoDsacg                                    ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctoantxg','VALUE'=>$cCtoAntxg                                    ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctoaplxg','VALUE'=>$_POST['cComMemoApl']                         ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctoptaxg','VALUE'=>$_POST['cCtoPta']                             ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctopvxxg','VALUE'=>$_POST['cCtoPvxxg']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit1g','VALUE'=>$_POST['cCtoNit1g']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit2g','VALUE'=>$_POST['cCtoNit2g']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctodesxr','VALUE'=>$_POST['cCtoDesr']                            ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctoantxr','VALUE'=>$_POST['cCtoAntxr']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit1r','VALUE'=>$_POST['cCtoNit1r']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit2r','VALUE'=>$_POST['cCtoNit2r']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctodesxl','VALUE'=>$_POST['cCtoDesL']                            ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctodocxl','VALUE'=>$_POST['cADoCruL']                         		,'CHECK'=>'NO'),
		                        array('NAME'=>'ctoctocl','VALUE'=>$_POST['cAConCruL']                        		,'CHECK'=>'NO'),
		                        array('NAME'=>'ctodocil','VALUE'=>$_POST['cDocInfL']                        		,'CHECK'=>'NO'),
														array('NAME'=>'ctoapcon','VALUE'=>$_POST['cApConBan']                        		,'CHECK'=>'NO'),
		                        array('NAME'=>'ctodsacl','VALUE'=>$cCtoDsacL                                    ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctoaplxl','VALUE'=>$_POST['cComMemoApl']                         ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctoptaxl','VALUE'=>$_POST['cCtoPtaL']                            ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctopvxxl','VALUE'=>$_POST['cCtoPvxxL']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit1l','VALUE'=>$_POST['cCtoNit1L']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit2l','VALUE'=>$_POST['cCtoNit2L']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctodesxr','VALUE'=>$_POST['cCtoDesr']                            ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctoantxr','VALUE'=>$_POST['cCtoAntxr']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit1r','VALUE'=>$_POST['cCtoNit1r']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit2r','VALUE'=>$_POST['cCtoNit2r']                           ,'CHECK'=>'NO'),
  								    	    array('NAME'=>'ctodesxm','VALUE'=>$_POST['cCtoDesm']                            ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctotpaxm','VALUE'=>$_POST['cCtoTpaxm']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit1m','VALUE'=>$_POST['cCtoNit1m']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit2m','VALUE'=>$_POST['cCtoNit2m']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctodesxf','VALUE'=>$_POST['cCtoDesf']                            ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctoclaxf','VALUE'=>$_POST['cCtoClaxf']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit1f','VALUE'=>$_POST['cCtoNit1f']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit2f','VALUE'=>$_POST['cCtoNit2f']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctodesxn','VALUE'=>$_POST['cCtoDesn']                            ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit1n','VALUE'=>$_POST['cCtoNit1n']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit2n','VALUE'=>$_POST['cCtoNit2n']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctodesxc','VALUE'=>$_POST['cCtoDesc']                            ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit1c','VALUE'=>$_POST['cCtoNit1c']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit2c','VALUE'=>$_POST['cCtoNit2c']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctodesxd','VALUE'=>$_POST['cCtoDesd']                            ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit1d','VALUE'=>$_POST['cCtoNit1d']                           ,'CHECK'=>'NO'),
		                        array('NAME'=>'ctonit2d','VALUE'=>$_POST['cCtoNit2d']                           ,'CHECK'=>'NO'),
		                        /*** Codigo de Integracion con E2K ***/
                            array('NAME'=>'ctoe2kxx','VALUE'=>$_POST['cCtoE2k']                             ,'CHECK'=>'NO'),
														/***  Codigo Homologacion Aladuanas ***/
														array('NAME'=>'ctochald','VALUE'=>$_POST['cCtoChAld']                           ,'CHECK'=>'NO'),
                            /***  Codigo Integracion con Belcorp***/
                            array('NAME'=>'pucadbel','VALUE'=>trim($_POST['cPucBel'])                       ,'CHECK'=>'NO'), //C�digo Integraci�n Belcorp
                            array('NAME'=>'pucadnas','VALUE'=>trim($_POST['cNuAsBel'])                      ,'CHECK'=>'NO'), //N�mero Asignaci�n Belcorp

                            /*** Categoria concepto ***/
  								    	    array('NAME'=>'cacidxxx','VALUE'=>trim($_POST['cCacId'])                        ,'CHECK'=>'NO'),
                            /*** Integracion SAP ***/
														array('NAME'=>'ctosapid','VALUE'=>trim($_POST['cCtoSapId'])                     ,'CHECK'=>'NO'),
                            array('NAME'=>'ctosapcx','VALUE'=>trim($_POST['cCtoSapC'])                      ,'CHECK'=>'NO'),
                            array('NAME'=>'ctosapix','VALUE'=>trim($_POST['cCtoSapI'])                      ,'CHECK'=>'NO'),
                            array('NAME'=>'ctosapic','VALUE'=>trim($_POST['cCtoSapIc'])                     ,'CHECK'=>'NO'),
                            array('NAME'=>'ctosapiv','VALUE'=>trim($_POST['cCtoSapIv'])                     ,'CHECK'=>'NO'),
                            array('NAME'=>'ctosapca','VALUE'=>trim($_POST['cCtoSapCA'])                     ,'CHECK'=>'NO'),
                            array('NAME'=>'ctosapli','VALUE'=>trim($_POST['cCtoSapLI'])                     ,'CHECK'=>'NO'),
                            array('NAME'=>'ctosaple','VALUE'=>trim($_POST['cCtoSapLE'])                     ,'CHECK'=>'NO'),
                            /*** Siempre que se edita un registro se limpia el campo ctosapxx = 0000-00-00 00:00:00 ***/
														array('NAME'=>'ctosapxx','VALUE'=>"0000-00-00 00:00:00"                         ,'CHECK'=>'NO'),
														/*** Clasificacion Producto ***/
														array('NAME'=>'ctoclapr','VALUE'=>trim($_POST['cCtoClapr'])                     ,'CHECK'=>'NO'),
		    										/*** Codigo Colombia Compra Eficiente ***/														
														array('NAME'=>'cceidxxx','VALUE'=>trim($_POST['cCceId'])                        ,'CHECK'=>'NO'),
														/*** Unidad de Medida ***/														
  								    	    array('NAME'=>'umeidxxx','VALUE'=>trim($_POST['cUmeId'])                        ,'CHECK'=>'NO'),
  								    	    array('NAME'=>'ctolineg','VALUE'=>trim($cLineaNeg)															,'CHECK'=>'NO'),
  								    	    array('NAME'=>'ctopuc85','VALUE'=>trim($_POST['cCtoPuc85'])                     ,'CHECK'=>'NO'),
														array('NAME'=>'ctocwccx','VALUE'=>trim($_POST['cCtocWccX'])											,'CHECK'=>'NO'),
		                        array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId']))          ,'CHECK'=>'SI'),
  								    	    array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												          ,'CHECK'=>'SI'),
					 									array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                              ,'CHECK'=>'SI'),
														array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cEstado']))           ,'CHECK'=>'SI'),
														array('NAME'=>'pucidxxx','VALUE'=>$_POST['cPucId']                              ,'CHECK'=>'WH'),
		                        array('NAME'=>'ctoidxxx','VALUE'=>$_POST['cCtoId']                              ,'CHECK'=>'WH'));

    		if (f_MySql("UPDATE","fpar0119",$zInsertCsc,$xConexion01,$cAlfa)){
    		  //f_Mensaje(__FILE__,__LINE__,"entre");
    		}else{
    		  $cMsj .= "Error al Actualizar los Datos [fpar0119], Verifique ";
			    $nSwitch = 1;
    		}
	    }
  	break;
	}

	if($nSwitch==0){
	  //f_Mensaje(__FILE__,__LINE__,"entre");
	  switch ($_COOKIE['kModo']) {
		 case "NUEVO":
			 f_Mensaje(__FILE__,__LINE__,"El Concepto ha Sido Creado Con Exito, Verifique");?>
			 <html><body>
			 <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt']; ?>" method = "post" target = "fmwork"></form>
			 <script languaje = "javascript">
  			 parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
				 document.frgrm.submit();
			 </script>
			 </body>
			 </html>
		 <?php break;
		 case "EDITAR":
		   //f_Mensaje(__FILE__,__LINE__,"entre");
			 f_Mensaje(__FILE__,__LINE__,"El Concepto ha Sido Modificado Con Exito, Verifique");?>
			 <html><body>
			 <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt']; ?>" method = "post" target = "fmwork"></form>
			 <script languaje = "javascript">
				 parent.fmnav.location='<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php';
			 	 document.frgrm.submit();
			 </script>
			 </body>
			 </html>
		 <?php break;
	  }
  } else {
  	f_Mensaje(__FILE__,__LINE__,"$cMsj Verifique");
  }
?>

<?php
  namespace openComex;

  /**
	 * Grabar la Liberacion de Formularios Facturados.
	 * --- Descripcion: Me permite Liberar los formularios con estado FACTURADO y que estan disponibles para ser liberados.
	 * @author Johana Arboleda <dp5@opentecnologia.com.co>
	 * @version 001
	 */
 	include("../../../../libs/php/utility.php");

  $nSwitch = 0;
  $cMsj = "\n";
  
	switch ($_COOKIE['kModo']) {
		case "LIBERAR";
		  // realizo validaciones de Chackeo, si algo viene o no checkeado.
			if ($_POST['nRecords'] ==0) {
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "No Existen Formularios Asignados para ningun DO,\n";
			}

			if ($_POST['cChekeados'] ==0) {
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Usted no ha Escogido Ningun Formulario para Liberar,\n";
			}
			
			if ($_POST['cGofId']=="") {
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "Debe Seleccionar el Grupo de Observacion para Formularios al Gasto.\n";
			}
						
	    if ($_POST['cObserv']=="") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Usted no Digito una Observación,\n";
      }
		break;
    default:
      $nSwitch = 1;
      f_Mensaje(__FILE__,__LINE__,"El Modo de Grabado Viene Vacio, Verifique");
    break;	
	}
	
	if ($nSwitch == 0) {
		switch ($_COOKIE['kModo']) {
	    case "LIBERAR";
	    	
	    	#Buscando el consecutivo
			  $qNumSec  = "SELECT obscscxx FROM $cAlfa.ffob0000 ORDER BY ABS(obscscxx) DESC LIMIT 0,1 ";
  	    $xNumSec  = f_MySql("SELECT","",$qNumSec,$xConexion01,"");
  	    //f_Mensaje(__FILE__,__LINE__,$qNumSec."~".mysql_num_rows($xNumSec));
  	    if(mysql_num_rows($xNumSec) > 0) {
  	    	$xRNS = mysql_fetch_array($xNumSec);
  	    	$cNumSec = $xRNS['obscscxx'] + 1;
  	    } else {
  	    	$cNumSec = 1;
  	    }
  	    $cNumSec = str_pad($cNumSec,5,"0",STR_PAD_LEFT);
			  $nNumSeq = 0;    
			  
	      $mMatriz01 = explode("|",$_POST['cComMemo']);
	      for ($i=0;$i<count($mMatriz01);$i++) {
          if ($mMatriz01[$i] !="") {
            
            $mMatriz02 = explode("~", $mMatriz01[$i]);
            $cPtoId = trim(strtoupper($mMatriz02[1]));
            $cSerId = trim(strtoupper($mMatriz02[0]));
            $cDo    = trim(strtoupper($mMatriz02[2]));
            $cSucId = trim(strtoupper($mMatriz02[3]));
            $cSuf   = trim(strtoupper($mMatriz02[4]));
            
            $qFoiDat  = "SELECT comidsxx, comcodsx, comfecsx, comcscsx, diridxxx ";
			      $qFoiDat .= "FROM $cAlfa.ffoi0000 ";
			      $qFoiDat .= "WHERE " ;
			      $qFoiDat .= "ptoidxxx = \"$cPtoId\" AND ";
  	        $qFoiDat .= "seridxxx = \"$cSerId\" LIMIT 0,1 ";
  	        $xFoiDat  = f_MySql("SELECT","",$qFoiDat,$xConexion01,"");
  	        if (mysql_num_rows($xFoiDat) > 0) {
  	         $xRFD = mysql_fetch_array($xFoiDat);
  	         
  	         #Datos para buscar la factura en cabecera
  	         $nAnoFac = substr($xRFD['comfecsx'],0,4);
  	         $cComId  = $xRFD['comidsxx'];
  	         $cComCod = $xRFD['comcodsx'];
  	         $cComCsc = $xRFD['comcscsx'];
  	         $cDirId  = $xRFD['diridxxx'];
  	        }
  	        
  	        // a los formularios escogidos les cambio el estado a CONDO
            $zInsertCab = array(array('NAME'=>'regestxx','VALUE'=>'CONDO'      ,'CHECK'=>'SI'),
                                array('NAME'=>'comidsxx','VALUE'=>''           ,'CHECK'=>'NO'),
                                array('NAME'=>'comcodsx','VALUE'=>''           ,'CHECK'=>'NO'),
                                array('NAME'=>'comcscsx','VALUE'=>''           ,'CHECK'=>'NO'),
                                array('NAME'=>'comcscs2','VALUE'=>''           ,'CHECK'=>'NO'),
                                array('NAME'=>'ptoidxxx','VALUE'=>$cPtoId      ,'CHECK'=>'WH'),
                                array('NAME'=>'seridxxx','VALUE'=>$cSerId      ,'CHECK'=>'WH'));

            if (f_MySql("UPDATE","ffoi0000",$zInsertCab,$xConexion01,$cAlfa)) {
            	#Busco en cabecera la factura para actualizar el campo commemof
	  	        $qCabFac  = "SELECT commemof ";
	  	        $qCabFac .= "FROM $cAlfa.fcoc$nAnoFac ";
	  	        $qCabFac .= "WHERE ";
	  	        $qCabFac .= "comidxxx = \"$cComId\" AND ";
	  	        $qCabFac .= "comcodxx = \"$cComCod\" AND ";
	  	        $qCabFac .= "comcscxx = \"$cComCsc\" LIMIT 0,1";
	  	        $xCabFac  = f_MySql("SELECT","",$qCabFac,$xConexion01,"");
	  	        //f_Mensaje(__FILE__,__LINE__,"$qCabFac ~ ".mysql_num_rows($xCabFac));
  	        	if (mysql_num_rows($xCabFac) > 0) {
  	        		$xRCF = mysql_fetch_array($xCabFac);
  	        	  $cFormu = "|";
  	        	  $mFormu = explode("|",$xRCF['commemof']);
  	        	  for ($j=0; $j < count($mFormu); $j++) {  	        	  
  	        	  	if($mFormu[$j] <> "") {
  	        	  		$mAux = explode("~",$mFormu[$j]);
  	        	  		if(!($mAux[3] == $cPtoId && $mAux[4] == $cSerId)) {
  	        	  			$cFormu .= "{$mFormu[$j]}|";
  	        	  		}
  	        	  	}
  	        	  }
  	        	  $cFormu = ($cFormu == "|")?"":$cFormu;
  	        	  
  	        	  
  	        	  #Actualizo la factura en cabecera
  	        	  $zInsertCab = array(array('NAME'=>'commemof','VALUE'=>$cFormu      ,'CHECK'=>'NO'),
		                                array('NAME'=>'comidxxx','VALUE'=>$cComId      ,'CHECK'=>'WH'),
		                                array('NAME'=>'comcodxx','VALUE'=>$cComCod     ,'CHECK'=>'WH'),
		                                array('NAME'=>'comcscxx','VALUE'=>$cComCsc     ,'CHECK'=>'WH'));
		
		            if (f_MySql("UPDATE","fcoc$nAnoFac",$zInsertCab,$xConexion01,$cAlfa)) {
		            } else {
		              $nSwitch = 1;
		              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
		              $cMsj .= "Error al Actualizar el Registro [fcoc$nAnoFac]\n";
		            }
  	        	}
  	        	
  	        	#Actualizo tabla de observaciones
  	        	#Guardo la observacion correspondiente al formulario
					    $nNumSeq++;
					  	$zInsertCab = array(array('NAME'=>'ptoidxxx','VALUE'=>$cPtoId     												,'CHECK'=>'SI'),
																  array('NAME'=>'seridxxx','VALUE'=>$cSerId     												,'CHECK'=>'SI'),
																  array('NAME'=>'obstipxx','VALUE'=>'LIBERARFAC'                        ,'CHECK'=>'SI'),
																  array('NAME'=>'obscscxx','VALUE'=>$cNumSec                       			,'CHECK'=>'SI'),
																  array('NAME'=>'obsseqxx','VALUE'=>str_pad($nNumSeq,5,"0",STR_PAD_LEFT),'CHECK'=>'SI'),
																  array('NAME'=>'gofidxxx','VALUE'=>trim(strtoupper($_POST['cGofId']))  ,'CHECK'=>'SI'),
																  array('NAME'=>'obsobsxx','VALUE'=>trim($_POST['cObserv']) 						,'CHECK'=>'SI','CS'=>'NONE'),
																  array('NAME'=>'diridxxx','VALUE'=>$cDirId													    ,'CHECK'=>'SI'),
																  array('NAME'=>'docsucxx','VALUE'=>$cSucId   													,'CHECK'=>'SI'),
																  array('NAME'=>'docnroxx','VALUE'=>$cDo			   												,'CHECK'=>'SI'),
																  array('NAME'=>'docsufxx','VALUE'=>$cSuf													      ,'CHECK'=>'SI'),
																  array('NAME'=>'regusrxx','VALUE'=>$_COOKIE['kUsrId']						      ,'CHECK'=>'SI'),
			      											array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')  								 			,'CHECK'=>'SI'),
			      											array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')  								 			,'CHECK'=>'SI'),
			      											array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d') 								 			,'CHECK'=>'SI'),
			      											array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')  								 			,'CHECK'=>'SI'),
			      											array('NAME'=>'regestxx','VALUE'=>'ACTIVO'  										 			,'CHECK'=>'SI'));
  
						  if (f_MySql("INSERT","ffob0000",$zInsertCab,$xConexion01,$cAlfa)) {
							} else {
							  $nSwitch = 1;
								$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
								$cMsj .= "Error al Actualizar Observacion.\n";
							}	  	        	
            } else {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "Error al Actualizar el Registro [ffoi0000]\n";
            }
          }
        }
	    break;
	  }
	}
  
	if($nSwitch==0){
    f_Mensaje(__FILE__,__LINE__,"Usted Ha Enviado a Liberado con Exito los Formularios Escogidos");?>
    <form name = "frgrm" action = "frlffini.php" method = "post" target = "fmwork"></form>
    <script language = "javascript">
      parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
      document.forms['frgrm'].submit();
    </script>
  <?php } else {
    f_Mensaje(__FILE__,__LINE__,"$cMsj Verifique");
  }
?>
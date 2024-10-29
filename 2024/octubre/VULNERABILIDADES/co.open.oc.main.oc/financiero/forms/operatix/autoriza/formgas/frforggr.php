<?php
  namespace openComex;

  /**
	 * Grabar la Legalizacion de Formularios.
	 * --- Descripcion: Me permite enviar al Gasto Provisional los formularios en estado CONDO. Colocando su estado en PRVGASTO en la tabla 1012
	 * @author Pedro Leon Burbano Suarez <pedrob@repremundo.com.co>
	 * @version 001
	 */
 	include("../../../../libs/php/utility.php");

 	$cPerAno = date('Y');
  $cPerMes = date('m');

  //f_Mensaje(__FILE__,__LINE__,$_COOKIE['kModo']);
  $nSwitch = 0;
	$cMsj = "\n";
	
	switch ($_COOKIE['kModo']) {
		case "LEGALIZAR";
		  // realizo validaciones de Chackeo, si algo viene o no checkeado.
			if ($_POST['nRecords']==0) {
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "No Existen Formularios Asignados para ningun Director de Cuenta.\n";
			}

			if ($_POST['cChekeados']=="") {
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "Debe Seleccionar al Menos un Formulario para Enviar al Gasto Provisional.\n";
			}
			
			if ($_POST['cGofId']=="") {
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "Debe Seleccionar el Grupo de Observacion para Formularios al Gasto.\n";
			}
			
			if ($_POST['cObserv']=="") {
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "Debe Ingresar la Observacion.\n";
			}

			$cDifer=0;
			$mMatriz01 = explode("|",$_POST['cComMemo']);
			for ($i=0;$i<count($mMatriz01);$i++) {
			  if ($mMatriz01[$i] !="") {
				  $mMatriz02 = explode("~", $mMatriz01[$i]);
				  $cDir=$mMatriz02[2];
				  $i=count($mMatriz01);
				}
			}

			$mMatriz01 = explode("|",$_POST['cComMemo']);
			for ($i=0;$i<count($mMatriz01);$i++) {
			  if ($mMatriz01[$i] !="") {
			    $mMatriz02 = explode("~", $mMatriz01[$i]);
			    if($cDir!=$mMatriz02[2])
			       $cDifer=1;
				}
			}

			if($cDifer==1) {
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "Existen Formularios de Diferentes Directores de Cuenta.\n";
			}			
		break;
		default:
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			$cMsj .= "El Modo de Grabado Viene Vacio.\n";
		break;
	}
	
	if ($nSwitch == 0) {
	
		switch ($_COOKIE['kModo']) {
			case "LEGALIZAR";
				
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
				    // a los formularios escogidos les cambio el estado a PRVGASTO
				    $zInsertCab = array(array('NAME'=>'comfepgx','VALUE'=>date('Y-m-d')                        ,'CHECK'=>'SI'),
				                        array('NAME'=>'regestxx','VALUE'=>'PRVGASTO'                           ,'CHECK'=>'SI'),
      			                    array('NAME'=>'ptoidxxx','VALUE'=>trim(strtoupper($mMatriz02[1]))      ,'CHECK'=>'WH'),
      												  array('NAME'=>'seridxxx','VALUE'=>trim(strtoupper($mMatriz02[0]))      ,'CHECK'=>'WH'));

					  if (f_MySql("UPDATE","ffoi0000",$zInsertCab,$xConexion01,$cAlfa)) {
					  	#Guardo la observacion correspondiente al formulario
					    $nNumSeq++;
					  	$zInsertCab = array(array('NAME'=>'ptoidxxx','VALUE'=>trim(strtoupper($mMatriz02[1]))     ,'CHECK'=>'SI'),
																  array('NAME'=>'seridxxx','VALUE'=>trim(strtoupper($mMatriz02[0]))     ,'CHECK'=>'SI'),
																  array('NAME'=>'obstipxx','VALUE'=>'AUTPRVGASTO'                       ,'CHECK'=>'SI'),
																  array('NAME'=>'obscscxx','VALUE'=>$cNumSec                       			,'CHECK'=>'SI'),
																  array('NAME'=>'obsseqxx','VALUE'=>str_pad($nNumSeq,5,"0",STR_PAD_LEFT),'CHECK'=>'SI'),
																  array('NAME'=>'gofidxxx','VALUE'=>trim(strtoupper($_POST['cGofId']))  ,'CHECK'=>'SI'),
																  array('NAME'=>'obsobsxx','VALUE'=>trim($_POST['cObserv']) 						,'CHECK'=>'SI','CS'=>'NONE'),
																  array('NAME'=>'diridxxx','VALUE'=>trim(strtoupper($mMatriz02[2]))     ,'CHECK'=>'SI'),
																  array('NAME'=>'docsucxx','VALUE'=>trim(strtoupper($mMatriz02[4]))     ,'CHECK'=>'SI'),
																  array('NAME'=>'docnroxx','VALUE'=>trim(strtoupper($mMatriz02[3]))     ,'CHECK'=>'SI'),
																  array('NAME'=>'docsufxx','VALUE'=>trim(strtoupper($mMatriz02[5]))     ,'CHECK'=>'SI'),
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
							$cMsj .= "Error al Actualizar el Registro de Cabecera.\n";
					  }
				  }
			  }
			break;
		}
	} 
	
	if($nSwitch == 1){
  	f_Mensaje(__FILE__,__LINE__,"$cMsj Verifique");
  } else {
 	  f_Mensaje(__FILE__,__LINE__,"Usted Ha Enviado a Provisional al Gasto con Exito los Formularios Escogidos");?>
		<form name = "frgrm" action = "frgasini.php" method = "post" target = "fmwork"></form>
		<script language = "javascript">
		 	parent.fmwork.f_Imp_Soporte('<?php echo $cNumSec; ?>');
		  parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
			document.forms['frgrm'].submit();
	  </script>
  	<?php } ?>
	
?>
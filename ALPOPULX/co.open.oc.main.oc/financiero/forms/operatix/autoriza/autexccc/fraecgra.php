<?php
/**
 * Graba Autorizacion Excluir Conceptos de Cobro para Facturacion.
 * Este programa guarda los conceptos que van a ser excluidos de cobro en el momento de realizar la factura.
 * @author Yulieth Campos <ycampos@opentecnologia.com.co>
 * @version 001
 */
	include("../../../../libs/php/utility.php");


	$nSwitch = 0; // Switch para Vericar la Validacion de Datos
	$cMsj    = "";
	$cMsjAdv = "";
	
	switch ($_COOKIE['kModo']) {
		case "MASIVA":
				/*****Validando que aplique al menos un concepto de cobro*****/
			if($_POST['cComMemo']=="" || strlen($_POST['cComMemo']) == 1 ){
				$nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Debe Escoger Por lo Menos Un Concepto de Cobro, \n";
			}
			
      if ($nSwitch == 0) {
  			/**
  			 * Validando que el DO exista y armando la observacion
  			 */
  			
  			$mAuxExc = array();
  			$vDos = array();
  			$mAuxExc = explode("|", $_POST['cComMemo']);
  			$cComprobantes = "";
  			
  			for ($nPe=0; $nPe<count($mAuxExc); $nPe++) {
  				if ($mAuxExc[$nPe] != "") {
  					$vAuxExc = array();
  					$vAuxExc = explode("~", $mAuxExc[$nPe]);						
  					
  					$vDos[$vAuxExc[3].'-'.$vAuxExc[4].'-'.$vAuxExc[5]]['sucidxxx'] = $vAuxExc[3];
  					$vDos[$vAuxExc[3].'-'.$vAuxExc[4].'-'.$vAuxExc[5]]['docidxxx'] = $vAuxExc[4];
  					$vDos[$vAuxExc[3].'-'.$vAuxExc[4].'-'.$vAuxExc[5]]['docsufxx'] = $vAuxExc[5];
  					$vDos[$vAuxExc[3].'-'.$vAuxExc[4].'-'.$vAuxExc[5]]['ccaplfax'] = $vAuxExc[6];
  					$vDos[$vAuxExc[3].'-'.$vAuxExc[4].'-'.$vAuxExc[5]]['teridint'] = ($vAuxExc[6] == "SI") ? $vAuxExc[7] : "";
  					
  					$vDos[$vAuxExc[3].'-'.$vAuxExc[4].'-'.$vAuxExc[5]]['comMemox'] .= "{$vAuxExc[0]}~{$vAuxExc[1]}~{$vAuxExc[2]}|";
            
            $qTramites  = "SELECT sucidxxx, docidxxx, docsufxx, doctipxx, regestxx ";
            $qTramites .= "FROM $cAlfa.sys00121 ";
            $qTramites .= "WHERE ";
            $qTramites .= "sucidxxx  = \"{$vAuxExc[3]}\" AND ";
            $qTramites .= "docidxxx  = \"{$vAuxExc[4]}\" AND ";
            $qTramites .= "docsufxx  = \"{$vAuxExc[5]}\" AND ";
            $qTramites .= "regestxx != \"INACTIVO\" LIMIT 0,1 ";
            $xTramites  = f_MySql("SELECT","",$qTramites,$xConexion01,"");
            // f_Mensaje(__FILE__,__LINE__,$qTramites." ~ ".mysql_num_rows($xTramites));
            if (mysql_num_rows($xTramites) == 0) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "El Do [{$vAuxExc[3]}-{$vAuxExc[4]}-{$vAuxExc[5]}] No Existe, o se encuentra en estado INACTIVO.\n";
            }
            
            if ($vAuxExc[6] == "SI") {
              //Validando que el facturar a exista
              $qFacA  = "SELECT ";
              $qFacA .= "$cAlfa.SIAI0150.CLIIDXXX ";
              $qFacA .= "FROM $cAlfa.SIAI0150 ";
              $qFacA .= "WHERE ";
              $qFacA .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$vAuxExc[7]}\" AND ";
              $qFacA .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" LIMIT 0,1";
              $xFacA  = f_MySql("SELECT","",$qFacA,$xConexion01,"");
              if (mysql_num_rows($xFacA) == 0) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Para el El Do [{$vAuxExc[3]}-{$vAuxExc[4]}-{$vAuxExc[5]}], el Facturar a No Exite o Se Encuentra Inactivo.\n";
              }
            }
  				}
  			}
			}
		break;
	  case "NUEVO":
	  case "EDITAR":
	  	/***** Validando Sucursal del Do*****/
  		if ($_POST['cSucId'] == "") {
  		  $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La Sucursal del Do, no puede ser vacio \n ";
  		}
  		/***** Fin Validando Sucursal del Do*****/
  		
  		/***** Validando Numero Do*****/
  		if ($_POST['cDocId'] == "") {
  		  $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Numero del Do, no puede ser vacio \n ";
  		}
  		//f_Mensaje(__FILE__,__LINE__,$_POST['cDocId']);
  		/***** Fin Validando numero Do*****/
  		
  		/***** Validando Numero Do*****/
  		if ($_POST['cDocSuf'] == "") {
  		  $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Sufijo del Do, no puede ser vacio \n ";
  		}
  		/***** Fin Validando numero Do*****/

  		/***** Validando Nit del cliente *****/
  		if ($_POST['cCliId'] == "") {
  		  $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Nit del Cliente no puede ser vacio \n ";
  		}
  		/*****Fin Validando Nit del cliente *****/

  		/***** Validando Nombre del cliente *****/
  		if ($_POST['cCliNom'] == "") {
  		  $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Nombre del Cliente no puede ser vacio \n ";
  		}
  		/*****Fin Validando Nombre del cliente *****/
  		
	 		/*****Validando que aplique al menos un concepto de cobro*****/
			if($_POST['cComMemo']==""){
				$nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Debe Escoger Por lo Menos Un Concepto de Cobro, Verifique \n";
			}
			/*****Validando que aplique al menos un concepto de cobro*****/
			
			$qTramites  = "SELECT sucidxxx, docidxxx, docsufxx, doctipxx, regestxx ";
      $qTramites .= "FROM $cAlfa.sys00121 ";
      $qTramites .= "WHERE ";
      $qTramites .= "sucidxxx  = \"{$_POST['cSucId']}\" AND ";
      $qTramites .= "docidxxx  = \"{$_POST['cDocId']}\" AND ";
      $qTramites .= "docsufxx  = \"{$_POST['cDocSuf']}\" AND ";
      $qTramites .= "regestxx != \"INACTIVO\" LIMIT 0,1 ";
      $xTramites  = f_MySql("SELECT","",$qTramites,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qTramites." ~ ".mysql_num_rows($xTramites));
      if (mysql_num_rows($xTramites) == 0) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Do [{$_POST['cSucId']}-{$_POST['cDocId']}-{$_POST['cDocSuf']}] No Existe, o se encuentra en estado INACTIVO.\n";
      }
      
      if ( $_POST['cCcAplFa'] == "SI" ) {
        if ( $_POST['cTerIdInt'] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= " El Cliente tiene parametrizada en su Condicion Comercial la opcion \"Aplicar tarifas del Facturar a\", por favor seleccione el Facturar a.\n";
        } else {
          //Validando que el facturar a sea valido
          $qFacA  = "SELECT ";
          $qFacA .= "$cAlfa.SIAI0150.CLIIDXXX ";
          $qFacA .= "FROM $cAlfa.SIAI0150 ";
          $qFacA .= "WHERE ";
          $qFacA .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$_POST['cTerIdInt']}\" AND ";
          $qFacA .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" LIMIT 0,1";
          $xFacA  = f_MySql("SELECT","",$qFacA,$xConexion01,"");
          if (mysql_num_rows($xFacA) == 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= " El Facturar a No Exite o Se Encuentra Inactivo.\n";
          }
        }
      } else {
        $_POST['cTerIdInt'] = "";
      }
  	break;
	}	/***** Fin de la Validacion *****/

	/***** Ahora Empiezo a Grabar *****/
	/***** Pregunto si el SWITCH Viene en 0 para Poder Seguir *****/

	if ($nSwitch == 0) {
		switch ($_COOKIE['kModo']) {
			case "MASIVA":
				/*****************************   UPDATE    ***********************************************/
				foreach ($vDos as $vDo) {
				  
          $vDo['comMemox'] = (trim($vDo['comMemox']) != "") ? "|".trim($vDo['comMemox']) : "";
          
					$qUpdate	 = array(array('NAME'=>'docfaexc','VALUE'=>trim($vDo['teridint'])            ,'CHECK'=>'NO'),
					                   array('NAME'=>'doctexxx','VALUE'=>trim($vDo['comMemox'])            ,'CHECK'=>'NO'),
                             array('NAME'=>'sucidxxx','VALUE'=>trim(strtoupper($vDo['sucidxxx'])),'CHECK'=>'WH'),
                             array('NAME'=>'docidxxx','VALUE'=>trim(strtoupper($vDo['docidxxx'])),'CHECK'=>'WH'),
                             array('NAME'=>'docsufxx','VALUE'=>trim(strtoupper($vDo['docsufxx'])),'CHECK'=>'WH'));

					if (f_MySql("UPDATE","sys00121",$qUpdate,$xConexion01,$cAlfa)) {
						/***** Grabo Bien *****/
						$cMsj .= "El Registro se Actualizo con Exito para el Do [{$vDo['sucidxxx']}-{$vDo['docidxxx']}-{$vDo['docsufxx']}], \n";
					} else {
            $cMsjAdv .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						$cMsjAdv .= "Error al Actualizar el Registro para el Do [{$vDo['sucidxxx']}-{$vDo['docidxxx']}-{$vDo['docsufxx']}], \n";
					}
				}
			break;
			case "NUEVO":
			case "EDITAR":
				/*****************************   UPDATE    ***********************************************/
				$qUpdate	 = array(array('NAME'=>'docfaexc','VALUE'=>trim($_POST['cTerIdInt'])            ,'CHECK'=>'NO'),
				                   array('NAME'=>'doctexxx','VALUE'=>trim($_POST['cComMemo'])             ,'CHECK'=>'SI'),
                           array('NAME'=>'sucidxxx','VALUE'=>trim(strtoupper($_POST['cSucId']))   ,'CHECK'=>'WH'),
                           array('NAME'=>'docidxxx','VALUE'=>trim(strtoupper($_POST['cDocId']))   ,'CHECK'=>'WH'),
                           array('NAME'=>'docsufxx','VALUE'=>trim(strtoupper($_POST['cDocSuf']))  ,'CHECK'=>'WH'));

					if (f_MySql("UPDATE","sys00121",$qUpdate,$xConexion01,$cAlfa)) {
						/***** Grabo Bien *****/
						$cMsj .= "El Registro se Actualizo con Exito para el Do [{$_POST['cSucId']}-{$_POST['cDocId']}-{$_POST['cDocSuf']}]";
					} else {
						$nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Error al Actualizar el Registro";
					}
      break;
      /*****************************   UPDATE    ***********************************************/
      case "ANULAR":
         if($_POST['cEstado']=="ACTIVO"){
           $cEstado="INACTIVO";
         }
					$qUpdate	 = array(array('NAME'=>'docfaexc','VALUE'=>""                  									,'CHECK'=>'NO'),
					                   array('NAME'=>'doctexxx','VALUE'=>""                                    ,'CHECK'=>'NO'),
                             array('NAME'=>'docidxxx','VALUE'=>trim(strtoupper($_POST['cDocId']))   ,'CHECK'=>'WH'),
                             array('NAME'=>'docsufxx','VALUE'=>trim(strtoupper($_POST['cDocSuf']))  ,'CHECK'=>'WH'),
                             array('NAME'=>'sucidxxx','VALUE'=>trim(strtoupper($_POST['cSucId']))   ,'CHECK'=>'WH'));

				 if (f_MySql("UPDATE","sys00121",$qUpdate,$xConexion01,$cAlfa)) {
					 /***** Grabo Bien *****/
				 } else {
           $nSwitch = 1;
           $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
           $cMsj .= "Error al Actualizar el Registro";
				 }
      break;
    }
  } 

 	if ($nSwitch == 0) {
 	  if($_COOKIE['kModo']!="ANULAR"){
 	  	f_Mensaje(__FILE__,__LINE__,$cMsj);
 	  }
 	  if($_COOKIE['kModo']=="ANULAR"){
 	    f_Mensaje(__FILE__,__LINE__,"El Registro Actualizado Con Exito");
 	  }
		
 		?>
		<form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
		<script languaje = "javascript">
			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
			document.forms['frgrm'].submit()
		</script>
  <?php }
  
  if ($nSwitch == 1) {
    f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique");
  } ?>
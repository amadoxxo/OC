<?php
  namespace openComex;
/**
 * Graba Autorizacion Excluir Conceptos de Pagos Terceros.
 * Este programa guarda los conceptos que van a ser excluidos de cobro en el momento de realizar la factura.
 * @author Johana Arboleda Ramos <jarboleda@opentecnologia.com.co>
 * @version 001
 */
	include("../../../../libs/php/utility.php");

	$nSwitch = 0; // Switch para Vericar la Validacion de Datos
	$cMsj    = "";
  $cMsjAdv = "";

	switch ($_COOKIE['kModo']) {
	  case "NUEVO":
	  case "EDITAR":
	  	/***** Validando Sucursal del Do*****/
  		if ($_POST['cSucId'] == "") {
  		  $nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "La Sucursal del Do no puede ser vacia.\n";
  		}
  		/***** Fin Validando Sucursal del Do*****/

  		/***** Validando Numero Do*****/
  		if ($_POST['cDocId'] == "") {
  		  $nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "El Numero del Do no puede ser vacio.\n";
  		}
  		//f_Mensaje(__FILE__,__LINE__,$_POST['cDocId']);
  		/***** Fin Validando numero Do*****/

  		/***** Validando Numero Do*****/
  		if ($_POST['cDocSuf'] == "") {
  		  $nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "El Sufijo del Do no puede ser vacio.\n";
  		}
  		/***** Fin Validando numero Do*****/

  		/***** Validando Nit del cliente *****/
  		if ($_POST['cCliId'] == "") {
  		  $nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "El Nit del Cliente no puede ser vacio.\n";
  		}
  		/*****Fin Validando Nit del cliente *****/

  		/***** Validando Nombre del cliente *****/
  		if ($_POST['cCliNom'] == "") {
  		  $nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "El Nombre del Cliente no puede ser vacio \n";
  		}
  		/*****Fin Validando Nombre del cliente *****/

			// valido si viene solo | lo pongo en vacio.
      if (strlen($_POST['cComMemo']) == 1) {
        $_POST['cComMemo'] = '';
      }

			$mAuxExc = array();
      $mAuxExc = f_Explode_Array($_POST['cComMemo'],"|","~");

      $nSel = 0;
      for ($i=0; $i<count($mAuxExc); $i++) {
        if ($mAuxExc[$i][0] != "") {
          $nSel++;

          //Si la variable system_habilitar_liquidacion_do_facturacion es SI
          //Se debe validar que seleccione enviar a
          if ($vSysStr['system_habilitar_liquidacion_do_facturacion'] == "SI") {

            if (!f_InList($mAuxExc[$i][10],"NOAPLICA","COSTOS","GASTOS","INGRESOS","FINANCIACION")) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "Para el comprobante {$mAuxExc[$i][2]}-{$mAuxExc[$i][3]}-{$mAuxExc[$i][4]}-{$mAuxExc[$i][5]}, la Opcion Enviar a No Puede Ser Vacia.\n";
            }
          }
        }
      }

      if ($nSel == 0) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Debe Escoger Por lo Menos Un Concepto de Pagos a Terceros.\n";
      }

			$cComprobantes = "";
			for ($nPe=0; $nPe<count($mAuxExc); $nPe++) {
				if ($mAuxExc[$nPe] != "") {
					$vAuxExc = array();
					$vAuxExc = explode("~", $mAuxExc[$nPe]);
					$cComprobantes .= "{$vAuxExc[2]}-{$vAuxExc[3]}-{$vAuxExc[4]}-{$vAuxExc[5]}, ";
				}
			}

			$cComprobantes = substr($cComprobantes, 0, strlen($cComprobantes)-2);

			#Observacion
			$cObs  = "|___";
			$cObs .= trim(strtoupper($_COOKIE['kUsrId']))."__";
			$cObs .= date('Y-m-d')."__";
			$cObs .= date('H:i:s')."__";
			$cObs .= $cComprobantes;

			$qObsDo  = "SELECT docobept ";
			$qObsDo .= "FROM $cAlfa.sys00121 ";
			$qObsDo .= "WHERE " ;
			$qObsDo .= "sucidxxx = \"{$_POST['cSucId']}\"  AND ";
			$qObsDo .= "docidxxx = \"{$_POST['cDocId']}\"  AND ";
			$qObsDo .= "docsufxx = \"{$_POST['cDocSuf']}\" AND ";
			$qObsDo .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
			$xObsDo  = f_MySql("SELECT","",$qObsDo,$xConexion01,"");
			if (mysql_num_rows($xObsDo) == 0) {
				$nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Do [{$_POST['cSucId']}-{$_POST['cDocId']}-{$_POST['cDocSuf']}] No Existe, o se encuentra en estado FACTURADO o INACTIVO.\n";
			} else {
				$xROD = mysql_fetch_array($xObsDo);
				$cObs .= ($xROD['docobept'] == "") ? "___|" : $xROD['docobept'];
			}

  	break;
		case "MASIVA":

      $mTramites = array();
			$mAuxExc   = array();
      $mAuxExc   = f_Explode_Array($_POST['cComMemo'],"|","~");

      $nSel = 0;
      for ($i=0; $i<count($mAuxExc); $i++) {
        if ($mAuxExc[$i][0] != "") {
          $nSel++;

          $qTramites  = "SELECT sucidxxx, docidxxx, docsufxx, doctipxx, regestxx ";
          $qTramites .= "FROM $cAlfa.sys00121 ";
          $qTramites .= "WHERE ";
          $qTramites .= "sucidxxx  = \"{$mAuxExc[$i][7]}\" AND ";
          $qTramites .= "docidxxx  = \"{$mAuxExc[$i][8]}\" AND ";
          $qTramites .= "docsufxx  = \"{$mAuxExc[$i][9]}\" AND ";
          $qTramites .= "regestxx  = \"ACTIVO\" LIMIT 0,1 ";
          $xTramites  = f_MySql("SELECT","",$qTramites,$xConexion01,"");
           // f_Mensaje(__FILE__,__LINE__,$qTramites." ~ ".mysql_num_rows($xTramites));
          if (mysql_num_rows($xTramites) == 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Do [{$mAuxExc[$i][3]}-{$mAuxExc[$i][4]}-{$mAuxExc[$i][5]}] No Existe, se encuentra en estado FACTURADO o INACTIVO.\n";
          }

          $mTramites[$mAuxExc[$i][7]."-".$mAuxExc[$i][8]."-".$mAuxExc[$i][9]]['sucidxxx']  = $mAuxExc[$i][7];
          $mTramites[$mAuxExc[$i][7]."-".$mAuxExc[$i][8]."-".$mAuxExc[$i][9]]['docidxxx']  = $mAuxExc[$i][8];
          $mTramites[$mAuxExc[$i][7]."-".$mAuxExc[$i][8]."-".$mAuxExc[$i][9]]['docsufxx']  = $mAuxExc[$i][9];
          $mTramites[$mAuxExc[$i][7]."-".$mAuxExc[$i][8]."-".$mAuxExc[$i][9]]['comproba'] .= $mAuxExc[$i][2]."-".$mAuxExc[$i][3]."-".$mAuxExc[$i][4]."-".$mAuxExc[$i][5].", ";

          $cId  = $mAuxExc[$i][0]."~";
          $cId .= $mAuxExc[$i][1]."~";
          $cId .= $mAuxExc[$i][2]."~";
          $cId .= $mAuxExc[$i][3]."~";
          $cId .= $mAuxExc[$i][4]."~";
          $cId .= $mAuxExc[$i][5]."~";
          $cId .= $mAuxExc[$i][6]."~";
          $cId .= $mAuxExc[$i][7]."~";
          $cId .= $mAuxExc[$i][8]."~";
          $cId .= $mAuxExc[$i][9];
          $cId .= ($vSysStr['system_habilitar_liquidacion_do_facturacion'] == "SI") ? "~".$mAuxExc[$i][10] : "";
          $cId .= "|";

          $mTramites[$mAuxExc[$i][7]."-".$mAuxExc[$i][8]."-".$mAuxExc[$i][9]]['pagosexc'] .= $cId;

          //Si la variable system_habilitar_liquidacion_do_facturacion es SI
          //Se debe validar que seleccione enviar a
          if ($vSysStr['system_habilitar_liquidacion_do_facturacion'] == "SI") {
            if (!f_InList($mAuxExc[$i][10],"NOAPLICA","COSTOS","GASTOS","INGRESOS","FINANCIACION")) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "Para el comprobante {$mAuxExc[$i][2]}-{$mAuxExc[$i][3]}-{$mAuxExc[$i][4]}-{$mAuxExc[$i][5]}, la Opcion Enviar a No Puede Ser Vacia.\n";
            }
          }
        }
      }

      if ($nSel == 0) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Debe Escoger Por lo Menos Un Concepto de Pagos a Terceros.\n";
      }

			if ($nSwitch == 0) {
				foreach ($mTramites as $vDo) {

					$cComprobantes = substr($vDo['comproba'], 0, strlen($cComprobantes)-2);

					#Observacion
					$cObs  = "|___";
					$cObs .= trim(strtoupper($_COOKIE['kUsrId']))."__";
					$cObs .= date('Y-m-d')."__";
					$cObs .= date('H:i:s')."__";
					$cObs .= $cComprobantes;

					$qObsDo  = "SELECT docobept ";
					$qObsDo .= "FROM $cAlfa.sys00121 ";
					$qObsDo .= "WHERE " ;
					$qObsDo .= "sucidxxx = \"{$vDo['sucidxxx']}\" AND ";
					$qObsDo .= "docidxxx = \"{$vDo['docidxxx']}\" AND ";
					$qObsDo .= "docsufxx = \"{$vDo['docsufxx']}\" LIMIT 0,1 ";
					$xObsDo  = f_MySql("SELECT","",$qObsDo,$xConexion01,"");
					//f_Mensaje(__FILE__,__LINE__,$cComprobantes);
					if (mysql_num_rows($xObsDo) == 0) {
						$nSwitch = 1;
						$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						$cMsj .= "El DO [{$vDo['sucidxxx']}-{$vDo['docidxxx']}-{$vDo['docsufxx']}] No Existe.\n";
					} else {
						$xROD = mysql_fetch_array($xObsDo);
						$cObs .= ($xROD['docobept'] == "") ? "___|" : $xROD['docobept'];
						$mTramites[$vDo['sucidxxx'].'-'.$vDo['docidxxx'].'-'.$vDo['docsufxx']]['docobept'] = $cObs;
					}
				}
			}
		break;
	}	/***** Fin de la Validacion *****/

	/***** Ahora Empiezo a Grabar *****/
	/***** Pregunto si el SWITCH Viene en 0 para Poder Seguir *****/

	if ($nSwitch == 0) {
		switch ($_COOKIE['kModo']) {
			case "NUEVO":
			case "EDITAR":
				/*****************************   UPDATE    ***********************************************/
				$qUpdate	 = array(array('NAME'=>'doctexpt','VALUE'=>trim($_POST['cComMemo'])             ,'CHECK'=>'NO'),
													 array('NAME'=>'docobept','VALUE'=>$cObs									             	,'CHECK'=>'SI'),
                           array('NAME'=>'sucidxxx','VALUE'=>trim(strtoupper($_POST['cSucId']))   ,'CHECK'=>'WH'),
                           array('NAME'=>'docidxxx','VALUE'=>trim(strtoupper($_POST['cDocId']))   ,'CHECK'=>'WH'),
                           array('NAME'=>'docsufxx','VALUE'=>trim(strtoupper($_POST['cDocSuf']))  ,'CHECK'=>'WH'));

					if (f_MySql("UPDATE","sys00121",$qUpdate,$xConexion01,$cAlfa)) {
						/***** Grabo Bien *****/

					print_r($qUpdate);
            $cMsj .= "El Registro se Actualizo con Exito para el Do [{$_POST['cSucId']}-{$_POST['cDocId']}-{$_POST['cDocSuf']}]";
					} else {
						$nSwitch = 1;
						$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						$cMsj .= "Error al Actualizar el Registro";
					}
      break;
      case "MASIVA":
      /*****************************   UPDATE    ***********************************************/
      foreach ($mTramites as $vPccExc) {
        $qUpdate  = array(array('NAME'=>'doctexpt','VALUE'=>trim($vPccExc['pagosexc'])               ,'CHECK'=>'NO'),
                          array('NAME'=>'docobept','VALUE'=>$vPccExc['docobept']                     ,'CHECK'=>'SI'),
                          array('NAME'=>'sucidxxx','VALUE'=>trim(strtoupper($vPccExc['sucidxxx']))   ,'CHECK'=>'WH'),
                          array('NAME'=>'docidxxx','VALUE'=>trim(strtoupper($vPccExc['docidxxx']))   ,'CHECK'=>'WH'),
                          array('NAME'=>'docsufxx','VALUE'=>trim(strtoupper($vPccExc['docsufxx']))   ,'CHECK'=>'WH'));

        if (f_MySql("UPDATE","sys00121",$qUpdate,$xConexion01,$cAlfa)) {
          /***** Grabo Bien *****/
          $cMsj .= "El Registro se Actualizo con Exito para el Do [{$vPccExc['sucidxxx']}-{$vPccExc['docidxxx']}-{$vPccExc['docsufxx']}], \n";
        } else {
          $cMsjAdv .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsjAdv .= "Error al Actualizar el Registro para el Do [{$vPccExc['sucidxxx']}-{$vPccExc['docidxxx']}-{$vPccExc['docsufxx']}], \n";
        }
      }
      break;
      /*****************************   UPDATE    ***********************************************/
      case "ANULAR":

					$qUpdate	 = array(array('NAME'=>'doctexpt','VALUE'=>""                  									,'CHECK'=>'NO'),
                             array('NAME'=>'docidxxx','VALUE'=>trim(strtoupper($_POST['cDocId']))   ,'CHECK'=>'WH'),
                             array('NAME'=>'docsufxx','VALUE'=>trim(strtoupper($_POST['cDocSuf']))  ,'CHECK'=>'WH'),
                             array('NAME'=>'sucidxxx','VALUE'=>trim(strtoupper($_POST['cSucId']))   ,'CHECK'=>'WH'));

				 if (f_MySql("UPDATE","sys00121",$qUpdate,$xConexion01,$cAlfa)) {
					 /***** Grabo Bien *****/

					 $nSwitch = 0;
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
  }

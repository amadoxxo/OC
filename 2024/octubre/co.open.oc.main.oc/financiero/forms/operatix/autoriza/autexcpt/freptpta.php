<?php
  namespace openComex;
/**
 * Script para cargar pagos a terceros de un DO parametrica Autorizacion Excluir Conceptos de Pagos a Terceros
 * @author Johana Arboleda Ramos <jarboleda@opentecnologia.com.co>
 * @version 001
 */
include("../../../../libs/php/utility.php");
include("../../../../libs/php/utiliqdo.php");

$cTexto = "";

if($gSucId != "" && $gDocId != "" && $gDocSuf != "") {

	##Traigo Datos Adicionales del Do ##
	$qTramite  = "SELECT * ";
	$qTramite .= "FROM $cAlfa.sys00121 ";
	$qTramite .= "WHERE ";
	$qTramite .= "$cAlfa.sys00121.sucidxxx = \"$gSucId\" AND ";
	$qTramite .= "$cAlfa.sys00121.docidxxx = \"$gDocId\" AND ";
	$qTramite .= "$cAlfa.sys00121.docsufxx = \"$gDocSuf\" ";
	$xTramite  = f_MySql("SELECT","",$qTramite,$xConexion01,"");
	// f_Mensaje(__FILE__,__LINE__,$qTramite." ~ ".mysql_num_rows($xTramite));
	if (mysql_num_rows($xTramite) == 1) {
		$vTramite = mysql_fetch_array($xTramite);
	}

	$mExcluidos = array(); $vComboMarcadosTramites = array();

	if ($vTramite['doctexpt'] != "") {
    //Pagos Excluidos con anterioridad
    $mAux = f_Explode_Array($vTramite['doctexpt'],"|","~");

    for ($i=0; $i<count($mAux); $i++) {
      if ($mAux[$i][0] != "") {
        //Se quita del Id el valor del combo de enviar a, aplica cuando al momento de guardar la exclusion
        //la variable system_habilitar_liquidacion_do_facturacion estaba en SI
        $cId  = "{$mAux[$i][0]}~";
        $cId .= "{$mAux[$i][1]}~";
        $cId .= "{$mAux[$i][2]}~";
        $cId .= "{$mAux[$i][3]}~";
        $cId .= "{$mAux[$i][4]}~";
        $cId .= "{$mAux[$i][5]}~";
        $cId .= "{$mAux[$i][6]}~";
        $cId .= "{$mAux[$i][7]}~";
        $cId .= "{$mAux[$i][8]}~";
        $cId .= "{$mAux[$i][9]}";
        $mExcluidos[count($mExcluidos)] = $cId;

        //Valor actual del combo enviar a
        $vComboMarcadosTramites["$cId"] = $mAux[$i][10];
      }
    }
  }

	// Busco el detalle de la cuenta de creacion del DO.
	$qCuenta  = "SELECT * ";
	$qCuenta .= "FROM ";
	$qCuenta .= "$cAlfa.fpar0115 ";
	$qCuenta .= "WHERE ";
	$qCuenta .= "CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$vTramite['pucidxxx']}\" AND ";
	$qCuenta .= "regestxx = \"ACTIVO\" LIMIT 0,1";
	$xCuenta  = f_MySql("SELECT","",$qCuenta,$xConexion01,"");
	// f_Mensaje(__FILE__,__LINE__,$qCuenta." ~ ".mysql_num_rows($xCuenta));
	$vCuenta  = mysql_fetch_array($xCuenta);

	//Busco la Calidad del Tercero
	$cTerCal = "";
	$qDatExt  = "SELECT * ";
	$qDatExt .= "FROM $cAlfa.SIAI0150 ";
	$qDatExt .= "WHERE ";
	$qDatExt .= "CLIIDXXX = \"{$vTramite['cliidxxx']}\" AND ";
	$qDatExt .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
	$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
	//f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));

	if (mysql_num_rows($xDatExt) > 0) {
		$vDatExt = mysql_fetch_array($xDatExt);
		if ($vDatExt['CLIRECOM']=="SI" && $vDatExt['CLIGCXXX']<>"SI") {
			$cTerCal = "COMUN";
		} else {
			$cTerCal = "CONTRIBUYENTE";
		}
		if ($vDatExt['CLIRESIM']=="SI") {
			$cTerCal = "SIMPLIFICADO";
		}
		if ($vDatExt['CLIGCXXX']=="SI") {
			$cTerCal = "CONTRIBUYENTE";
		}
		if ($vDatExt['CLINRPXX']=="SI") {
			$cTerCal = "NORESIDENTE";
		}
	}
	##Fin Traigo Datos Adicionales del Do ##

	##Trayendo conceptos de pago a terceros que aplican para ese DO##
	$mTramites = array(); $i=0;
	$mTramites[$i]['sucidxxx'] = $gSucId;
	$mTramites[$i]['docidxxx'] = $gDocId;
	$mTramites[$i]['docsufxx'] = $gDocSuf;
	$mTramites[$i]['tipocobx'] = "TODO"; //Se asume que se van a facturar PCC y IP
	$mTramites[$i]['facturax'] = "";
	$mTramites[$i]['pucidxxx'] = $vTramite['pucidxxx'];
	$mTramites[$i]['pucdetxx'] = $vCuenta['pucdetxx'];
	$mTramites[$i]['puctretx'] = $vCuenta['puctretx'];
	$mTramites[$i]['imporest'] = "SI";		 // Importa el estado (SI/NO)
	$mTramites[$i]['regestxx'] = "ACTIVO"; // Cual estado "ACTIVO"
	$mTramites[$i]['comidxxx'] = "";
	$mTramites[$i]['comcodxx'] = "";
	$mTramites[$i]['comidxxf'] = "";  // Id del Documento de Facturacion
	$mTramites[$i]['comcodxf'] = "";  // Codigo del Documento de Facturacion
	$mTramites[$i]['comcscxf'] = "";  // Consecutivo del Documento de Facturacion
	$mTramites[$i]['clicteri'] = $cTerCal; // Update Facturacion en Dolares - Calidad del Tercero Intermediario "Facturar a"
	$mTramites[$i]['tcatasax'] = f_Buscar_Tasa_Cambio(date('Y-m-d'),"USD"); // Update Facturacion en Dolares - Tasa de Cambio Seleccionada en la Factura

	$mPCCA = f_Liquida_PCCA_Tramites($mTramites,date('Y-m-d'),"NO");

	// Cargo los pagos por cuenta del cliente x TERCERO.
	$mPagTer = array();
	for ($j=0;$j<count($mPCCA);$j++) {
		// Primero Traigo el Gravamen Arancelario de los DO's Seleccionados.
		if ($mPCCA[$j]['mostrarx'] == 1 && $mPCCA[$j]['tipopcca'] == "TRIBUTOS") {

			// Busco el nombre del tercero de la carta bancaria
			if ($mPCCA[$j]['terid2xx'] != "") {
				$qProId  = "SELECT *,CLIIDXXX AS TERIDXXX,CONCAT(CLINOMXX,\" \",CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X) AS TERNOMXX ";
				$qProId .= "FROM $cAlfa.SIAI0150 ";
				$qProId .= "WHERE ";
				$qProId .= "CLIIDXXX = \"{$mPCCA[$j]['terid2xx']}\" AND ";
				$qProId .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
				$xProId  = f_MySql("SELECT","",$qProId,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qProId." ~ ".mysql_num_rows($xProId));
				if (mysql_num_rows($xProId) > 0) {
					$vProId = mysql_fetch_array($xProId);
				} else {
					$vProId = array(); $vProId['TERNOMXX'] = "TERCERO SIN NOMBRE UNO";
				}
			} else {
				$vProId = array(); $vProId['TERNOMXX'] = "TERCERO SIN NOMBRE DOS";
			}

			// Cargo la matriz con los pagos por cuenta del cliente agrupados por TERCERO-COMPROBANTE.
			$mPCCA[$j]['ctodesxx'] .= " ^ ".trim(trim($vProId['TERNOMXX']))." ^ ".trim($vProId['TERIDXXX']);

			$nInd_mPagTer = count($mPagTer);
			$mPagTer[$nInd_mPagTer]['ctoidxxx'] = $mPCCA[$j]['ctoidfac'];
			$mPagTer[$nInd_mPagTer]['ctodesxx'] = trim($mPCCA[$j]['ctodesxx']);
			$mPagTer[$nInd_mPagTer]['sucidxxx'] = trim($mPCCA[$j]['sucidxxx']);
			$mPagTer[$nInd_mPagTer]['docidxxx'] = trim($mPCCA[$j]['comcsccx']);
			$mPagTer[$nInd_mPagTer]['docsufxx'] = trim($mPCCA[$j]['comseqcx']);
			$mPagTer[$nInd_mPagTer]['comid3xx'] = trim($mPCCA[$j]['comidxxx']);
			$mPagTer[$nInd_mPagTer]['comcod3x'] = trim($mPCCA[$j]['comcodxx']);
			$mPagTer[$nInd_mPagTer]['comcsc3x'] = trim($mPCCA[$j]['comcscxx']);
			$mPagTer[$nInd_mPagTer]['comseq3x'] = trim($mPCCA[$j]['comseqxx']);
			$mPagTer[$nInd_mPagTer]['comdocin'] = trim($mPCCA[$j]['comdocin']);
			$mPagTer[$nInd_mPagTer]['comvalor'] = (($mPCCA[$j]['puctipej'] == "L" || $mPCCA[$j]['puctipej'] == "") ? $mPCCA[$j]['comlocal'] : 0);
			$mPagTer[$nInd_mPagTer]['commovxx'] = trim($mPCCA[$j]['commovxx']);
			$mPagTer[$nInd_mPagTer]['titlexxx'] = "";
			$mPagTer[$nInd_mPagTer]['pucidxxx'] = $mPCCA[$j]['pucidxxx'];
			$mPagTer[$nInd_mPagTer]['tipopcca'] = $mPCCA[$j]['tipopcca'];
		}
	}

	// Cargo los pagos por cuenta del cliente x TERCERO.
	for ($j=0;$j<count($mPCCA);$j++) {
		if ($mPCCA[$j]['mostrarx'] == 1 && ($mPCCA[$j]['tipopcca'] == "PCCA" || $mPCCA[$j]['tipopcca'] == "CAJA_MENOR")) {

			if ($mPCCA[$j]['terid2xx'] != "") {
				$qProId  = "SELECT *,CLIIDXXX AS TERIDXXX,CONCAT(CLINOMXX,\" \",CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X) AS TERNOMXX ";
				$qProId .= "FROM $cAlfa.SIAI0150 ";
				$qProId .= "WHERE ";
				$qProId .= "CLIIDXXX = \"{$mPCCA[$j]['terid2xx']}\" AND ";
				$qProId .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
				$xProId  = f_MySql("SELECT","",$qProId,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qProId." ~ ".mysql_num_rows($xProId));
				if (mysql_num_rows($xProId) > 0) {
					$vProId = mysql_fetch_array($xProId);
				} else {
					$vProId = array(); $vProId['TERNOMXX'] = "TERCERO SIN NOMBRE UNO";
				}
			} else {
				$vProId = array(); $vProId['TERNOMXX'] = "TERCERO SIN NOMBRE DOS";
			}
			// Cargo la matriz con los pagos por cuenta del cliente agrupados por TERCERO-COMPROBANTE.
			$mPCCA[$j]['ctodesxx'] .= " ^ ".trim(trim($vProId['TERNOMXX']))." ^ ".trim($vProId['TERIDXXX']);
			$nInd_mPagTer = count($mPagTer);
			$mPagTer[$nInd_mPagTer]['ctoidxxx'] = $mPCCA[$j]['ctoidfac'];
			$mPagTer[$nInd_mPagTer]['ctodesxx'] = trim($mPCCA[$j]['ctodesxx']);
			$mPagTer[$nInd_mPagTer]['sucidxxx'] = trim($mPCCA[$j]['sucidxxx']);
			$mPagTer[$nInd_mPagTer]['docidxxx'] = trim($mPCCA[$j]['comcsccx']);
			$mPagTer[$nInd_mPagTer]['docsufxx'] = trim($mPCCA[$j]['comseqcx']);
			$mPagTer[$nInd_mPagTer]['comid3xx'] = trim($mPCCA[$j]['comidxxx']);
			$mPagTer[$nInd_mPagTer]['comcod3x'] = trim($mPCCA[$j]['comcodxx']);
			$mPagTer[$nInd_mPagTer]['comcsc3x'] = trim($mPCCA[$j]['comcscxx']);
			$mPagTer[$nInd_mPagTer]['comseq3x'] = trim($mPCCA[$j]['comseqxx']);
			$mPagTer[$nInd_mPagTer]['comdocin'] = trim($mPCCA[$j]['comdocin']);
			$mPagTer[$nInd_mPagTer]['comvalor'] = (($mPCCA[$j]['puctipej'] == "L" || $mPCCA[$j]['puctipej'] == "") ? $mPCCA[$j]['comlocal'] : 0);
			$mPagTer[$nInd_mPagTer]['commovxx'] = trim($mPCCA[$j]['commovxx']);
			$mPagTer[$nInd_mPagTer]['titlexxx'] = "|PorIva: ".$mPCCA[$j]['comivapx']."|";
			$mPagTer[$nInd_mPagTer]['pucidxxx'] = $mPCCA[$j]['pucidxxx'];
			$mPagTer[$nInd_mPagTer]['tipopcca'] = $mPCCA[$j]['tipopcca'];
		}
	}

	if (count($mPagTer) > 0) {
		$cTexto .= "<table border = \"0\" cellpadding = \"0\" cellspacing = \"0\" width = \"800\">";
			$cTexto .= "<tr bgcolor = \"{$vSysStr['system_row_title_color_ini']}\">";
				$cTexto .= "<td class = \"clase08\" width = \"80\"  style=\"padding-left:5px;padding-right:5px\" align = \"left\">Cto</td>";
				$cTexto .= "<td class = \"clase08\" width = \"360\" style=\"padding-left:5px;padding-right:5px\" align = \"left\">Servicio</td>";
				$cTexto .= "<td class = \"clase08\" width = \"140\" style=\"padding-left:5px;padding-right:5px\" align = \"left\">Documento Fuente</td>";
				$cTexto .= "<td class = \"clase08\" width = \"060\" style=\"padding-left:5px;padding-right:5px\" align = \"left\">Doc.Info</td>";
				$cTexto .= "<td class = \"clase08\" width = \"100\" style=\"padding-left:5px;padding-right:5px\" align = \"left\">Valor</td>";
				if ($vSysStr['system_habilitar_liquidacion_do_facturacion'] =="SI") {
				  $cTexto .= "<td class = \"clase08\" width = \"80\" style=\"padding-left:5px;padding-right:5px\" align = \"left\">Enviar A</td>";
				}
				$cTexto .= "<td class = \"clase08\" width = \"020\" style=\"padding-left:5px;padding-right:5px\" align = \"center\">M</td>";
				$cTexto .= "<td class = \"clase08\" width = \"020\" align=\"center\"><input type=\"checkbox\" name=\"nCheckAll\" onClick = \"javascript:f_Marca();f_Carga_Data();\"></td>";
			$cTexto .= "</tr>";

			$y=0;
			$nContador = 0;
				// f_Mensaje(__FILE__,__LINE__,implode('-',$mExcluidos)."excluido");

			for($i=0; $i<count($mPagTer); $i++) {
				$nContador ++;

				$cId  = "{$mPagTer[$i]['ctoidxxx']}~";
				$cId .= "{$mPagTer[$i]['pucidxxx']}~";
				$cId .= "{$mPagTer[$i]['tipopcca']}~";
				$cId .= "{$mPagTer[$i]['comid3xx']}~";
				$cId .= "{$mPagTer[$i]['comcod3x']}~";
				$cId .= "{$mPagTer[$i]['comcsc3x']}~";
				$cId .= "{$mPagTer[$i]['comseq3x']}~";
				$cId .= "{$mPagTer[$i]['sucidxxx']}~";
				$cId .= "{$mPagTer[$i]['docidxxx']}~";
				$cId .= "{$mPagTer[$i]['docsufxx']}";

				$cTexto .= "<tr>";
					$cTexto .= "<td bgcolor = \"{$vSysStr['system_row_impar_color_ini']}\" class = \"letra7\" style=\"padding-left:5px;padding-right:2px;border:1px solid #E6E6E6\" align=\"center\">{$mPagTer[$i]['ctoidxxx']}</td>";
					$cTexto .= "<td bgcolor = \"{$vSysStr['system_row_impar_color_ini']}\" class = \"letra7\" style=\"padding-left:5px;padding-right:2px;border:1px solid #E6E6E6\">{$mPagTer[$i]['ctodesxx']}</td>";
					$cTexto .= "<td bgcolor = \"{$vSysStr['system_row_impar_color_ini']}\" class = \"letra7\" style=\"padding-left:5px;padding-right:2px;border:1px solid #E6E6E6\" align=\"left\">{$mPagTer[$i]['comid3xx']}-{$mPagTer[$i]['comcod3x']}-{$mPagTer[$i]['comcsc3x']}-{$mPagTer[$i]['comseq3x']}</td>";
					$cTexto .= "<td bgcolor = \"{$vSysStr['system_row_impar_color_ini']}\" class = \"letra7\" style=\"padding-left:5px;padding-right:2px;border:1px solid #E6E6E6\" align=\"left\">".(($mPagTer[$i]['comdocin']!="")?$mPagTer[$i]['comdocin']:"&nbsp;")."</td>";
					$cTexto .= "<td bgcolor = \"{$vSysStr['system_row_impar_color_ini']}\" class = \"letra7\" style=\"padding-left:2px;padding-right:5px;border:1px solid #E6E6E6\" align=\"right\">{$mPagTer[$i]['comvalor']}</td>";
					if ($vSysStr['system_habilitar_liquidacion_do_facturacion'] =="SI") {
					  $cTexto .= "<td bgcolor = \"{$vSysStr['system_row_impar_color_ini']}\" class = \"letra7\" style=\"padding-left:2px;padding-right:5px;border:1px solid #E6E6E6\" align=\"right\">";
              $cTexto .= "<select name = \"cEnviarA".($i+1)."\" class =letra7 style = width:100;border:0;padding:2px >";
                // $cTexto .= "<option value=>[SELECCIONE]</option>";
                $cTexto .= "<option value=\"NOAPLICA\"".(($vComboMarcadosTramites["$cId"] == "NOAPLICA" || $vComboMarcadosTramites["$cId"] == "") ? " selected" : "").">COBRAR EN OTRA FACTURA</option>";
                $cTexto .= "<option value=\"COSTOS\"".(($vComboMarcadosTramites["$cId"] == "COSTOS") ? " selected" : "").">COSTO DO</option>";
                $cTexto .= "<option value=\"GASTOS\"".(($vComboMarcadosTramites["$cId"] == "GASTOS") ? " selected" : "").">TARIFA INTEGRAL</option>";
                //$cTexto .= "<option value=\"INGRESOS\"".(($vComboMarcadosTramites["$cId"] == "INGRESOS") ? " selected" : "").">INGRESOS</option>";
                $cTexto .= "<option value=\"FINANCIACION\"".(($vComboMarcadosTramites["$cId"] == "FINANCIACION") ? " selected" : "").">FINANCIACION X LIQUIDAR</option>";
              $cTexto .= "</select>";
            $cTexto .= "</td>";
					}
					$cTexto .= "<td bgcolor = \"{$vSysStr['system_row_impar_color_ini']}\" class = \"letra7\" style=\"padding-left:5px;padding-right:2px;border:1px solid #E6E6E6\" align=\"center\" title=\"{$mPagTer[$i]['titlexxx']}\">".(($mPagTer[$i]['commovxx']!="")?$mPagTer[$i]['commovxx']:"&nbsp;")."</td>";
					$cTexto .= "<td class = \"letra7\" align=\"center\"><input type=\"checkbox\" name=\"cCheck\" value = \"$cId\"";
					$cTexto .= "id=\"$cId\"";
					$cTexto .= "onclick=\"javascript:document.forms[\'frgrm\'][\'nRecords\'].value=\'".count($mPagTer)."\';f_Marcar_Iguales(this.id,this.checked);\"".((in_array($cId, $mExcluidos) == false) ? "" : "checked").">";
					$cTexto .= "</td>";
				$cTexto .= "</tr>";
				$y++;
			}//for($i=0; $i<count($mPagTer); $i++) {
		$cTexto .= '</table>'; ?>
		<script languaje="javascript">
			parent.fmwork.document.forms['frgrm']['nRecords'].value      = "<?php echo count($mPagTer) ?>";
			parent.fmwork.document.getElementById('tblPagTer').innerHTML = '<?php echo $cTexto ?>';
		</script>

	<?php } else{//if (count($mPagTer) > 0) {
		f_Mensaje(__FILE__,__LINE__,"No hay Pagos a Terceros Asignados al Do [$gSucId-$gDocId-$gDocSuf], Verifique.");
	}
} else {
	f_Mensaje(__FILE__,__LINE__,"Datos del DO Incompletos, verifique.");
} ?>

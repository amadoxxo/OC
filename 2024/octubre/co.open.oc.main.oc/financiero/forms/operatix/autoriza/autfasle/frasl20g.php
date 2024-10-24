<?php // Hola Mundo ...
  namespace openComex;
	ini_set("memory_limit","512M");
	set_time_limit(0);
 
	 //ini_set('error_reporting', E_ERROR);
	 //ini_set("display_errors","1");
	
include("../../../../libs/php/utility.php"); 
	
switch ($_POST['cModo']) {
	case "PEGARDO";
	
		$nSecuencia = ($_POST['nSecuencia'] == "") ? "001" : $_POST['nSecuencia'];
		
		if ($_POST['cMemo'] == "") {
			f_Mensaje(__FILE__,__LINE__,"Debe Ingresar los Numero de Do, Verifique.");
		} else {
			
			$nCanDo = 0;
				
			$cBuscar = array(","," ",chr(13),chr(10),chr(27),chr(9));
  		$cReempl = array("~","~","~","~","~","~");
			
			$_POST['cMemo'] = str_replace($cBuscar,$cReempl,$_POST['cMemo']);
			$vTramAxu = explode("~", $_POST['cMemo']);
			
			$vTramPeg = array();
			for ($nT=0; $nT<count($vTramAxu); $nT++) {
				if ($vTramAxu[$nT] != "") {
					if (in_array($vTramAxu[$nT], $vTramPeg) == false) {
						$vTramPeg[count($vTramPeg)] = $vTramAxu[$nT];
					}
				}
			}
			
			$cTramites = ""; $mTramites = array();
			
			for ($nT=0; $nT<count($vTramPeg); $nT++) {
				if ($vTramPeg[$nT] != "") {
					##Fin de Buscando los Do##
					$qTramites  = "SELECT * ";
          $qTramites .= "FROM $cAlfa.sys00121 ";
          $qTramites .= "WHERE ";
          $qTramites .= "docidxxx  = \"{$vTramPeg[$nT]}\" AND ";
          $qTramites .= "docafasl != \"SI\"              AND ";
          $qTramites .= "regestxx = \"ACTIVO\" ";
          $xTramites  = f_MySql("SELECT","",$qTramites,$xConexion01,"");
					// f_Mensaje(__FILE__, __LINE__, $qTramites."~".mysql_num_rows($xTramites));
					while ($xRT = mysql_fetch_array($xTramites)) {
						$i = count($mTramites);
						$mTramites[$i] = $xRT;
						
							$nIncluir = 1; //Variable que indica que el tramite fue encontrado en el modulo de aduana con las condiciones necesaria para que se muestre
						if ($xRT['doctipxx'] == "REGISTRO") {
							
							$mAuxDo = explode("~", $xRT['docdosre']);
							for ($nA=0; $nA<count($mAuxDo);$nA++) {
								if ($mAuxDo[$nA] != "") {
									//Verifico que si es un DO de Registro no este asociado a un DO de Importacion
									$qDoReg  = "SELECT *";
									$qDoReg .= "FROM $cAlfa.sys00121 ";
									$qDoReg .= "WHERE ";
									$qDoReg .= "doctipxx != \"REGISTRO\" AND ";
									$qDoReg .= "docidxxx  = \"{$mAuxDo[$nA]}\" AND ";
									$qDoReg .= "regestxx =  \"ACTIVO\" ";
									$xDoReg  = f_MySql("SELECT","",$qDoReg,$xConexion01,"");
									//f_Mensaje(__FILE__,__LINE__,$qDoReg." ~ ".mysql_num_rows($xDoReg));
									$nEncontro = 0; $cDoAso = "";
									while ($xRDR = mysql_fetch_array($xDoReg)) {
										$i = count($xDoReg);
										$mTramites[$i] = $xRDR;
									}
								}
							}
						}
						
						//Buscando los DO de registro asociados  al DO
						if ($xRT['docdosre'] != "" && $xRT['doctipxx'] != "REGISTRO") {
							$mAuxDo = explode("~", $xRT['docdosre']);
							for ($nA=0; $nA<count($mAuxDo);$nA++) {
								if ($mAuxDo[$nA] != "") {
									if (in_array("{$mAuxDo[$nA]}", $xRT) == false) {
										$xRT[count($xRT)] = "{$mAuxDo[$nA]}";
										//Busco el DO de Registro
										//Busco el tramite en la sys00121 de modulo de facturacion
										$qRegistro  = "SELECT * ";
										$qRegistro .= "FROM $cAlfa.sys00121 ";
										$qRegistro .= "WHERE ";
										$qRegistro .= "doctipxx = \"REGISTRO\"       AND ";
										$qRegistro .= "docidxxx = \"{$mAuxDo[$nA]}\" AND ";
										$qRegistro .= "regestxx = \"ACTIVO\" ";
										$xRegistro  = f_MySql("SELECT","",$qRegistro,$xConexion01,"");
										//f_Mensaje(__FILE__,__LINE__,$qRegistro." ~ aaaa ".mysql_num_rows($xRegistro));
										
										while ($xRR = mysql_fetch_array($xRegistro)) {
											$i = count($mTramites);
											$mTramites[$i] = $xRR;
										} ## while ($xRR = mysql_fetch_array($xRegistro)) { ##
									} ## if (in_array("{$mAux01[0]}~{$mAux01[1]}", $xRT) == false) { ##
								} ## if ($mAuxDo[$nA] != "") { ##
							} ## for ($nA=0; $nA<count($mAuxDo);$nA++) { ##
						} ## if ($xRT['docdosre'] != "") { ##
					} ## while ($xRT = mysql_fetch_array($xTramites)) { ##
					##Fin de Buscando los Do##
				}	
			}

			$vTramites = array();
			for ($i=0; $i<count($mTramites); $i++) {
			  //Buscando que el DO no se haya insertado en la grilla
			  if (in_array("{$mTramites[$i]['sucidxxx']}-{$mTramites[$i]['docidxxx']}-{$mTramites[$i]['docsufxx']}", $vTramites) == false) {
			    //Busco la el nombre del cliente
          $qDatCli  = "SELECT ";
          $qDatCli .= "$cAlfa.SIAI0150.*, ";
          $qDatCli .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
          $qDatCli .= "FROM $cAlfa.SIAI0150 ";
          $qDatCli .= "WHERE ";
          $qDatCli .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$mTramites[$i]['cliidxxx']}\" LIMIT 0,1";
          $xDatCli  = f_MySql("SELECT","",$qDatCli,$xConexion01,"");
          if(mysql_num_rows($xDatCli) > 0) {
            $vDatCli = mysql_fetch_array($xDatCli);
            $mTramites[$i]['clinomxx'] = $vDatCli['CLINOMXX'];
            $mTramites[$i]['cliidxx']  = $vDatCli['CLIIDXXX'];
          } else {
            $mTramites[$i]['clinomxx'] = "CLIENTE SIN NOMBRE";
            $mTramites[$i]['cliidxx'] = $vDatCli['CLIIDXXX'];
          }
          
          $vTramites[count($vTramites)] = "{$mTramites[$i]['sucidxxx']}-{$mTramites[$i]['docidxxx']}-{$mTramites[$i]['docsufxx']}";
          $cColor = (($mTramites[$i]['doctipxx'] == "REGISTRO") ? "red" : "");
          $nCanDo++; 
          ?>
          <script languaje = "javascript">
            if ("<?php echo $cColor ?>" == "red") {
              var cBgColor = "#FF0000";
              var cColor   = "#FFFFFF";
            } else {
              var cBgColor = "#FFFFFF";
              var cColor   = "#000000";
            }
            
            if ("<?php echo str_pad($nSecuencia,3,"0",STR_PAD_LEFT) ?>" != "<?php echo str_pad($_POST['nSecuencia'],3,"0",STR_PAD_LEFT) ?>") {
              parent.window.opener.f_Add_New_Row_Do();  
            }
              
            var nSecuencia = parent.window.opener.document.forms['frgrm']['nSecuencia'].value;
            parent.window.opener.document.forms['frgrm']['cSucId' +nSecuencia].value = '<?php echo $mTramites[$i]['sucidxxx'] ?>';
            parent.window.opener.document.forms['frgrm']['cDocId' +nSecuencia].value = '<?php echo $mTramites[$i]['docidxxx']  ?>';
            parent.window.opener.document.forms['frgrm']['cDocSuf'+nSecuencia].value = '<?php echo $mTramites[$i]['docsufxx']  ?>';
            parent.window.opener.document.forms['frgrm']['cDocTip'+nSecuencia].value = '<?php echo $mTramites[$i]['doctipxx']  ?>';
            parent.window.opener.document.forms['frgrm']['cCliId' +nSecuencia].value = '<?php echo $mTramites[$i]['cliidxxx']  ?>';
            parent.window.opener.document.forms['frgrm']['cCliDv' +nSecuencia].value = '<?php echo f_Digito_Verificacion($mTramites[$i]['cliidxxx'])  ?>';
            parent.window.opener.document.forms['frgrm']['cCliNom'+nSecuencia].value = '<?php echo $mTramites[$i]['clinomxx']  ?>';
            
            parent.window.opener.document.forms['frgrm']['cDocSeq'+nSecuencia].style.backgroundColor = cBgColor;
            parent.window.opener.document.forms['frgrm']['cDocSeq'+nSecuencia].style.color = cColor;
            parent.window.opener.document.forms['frgrm']['cSucId' +nSecuencia].style.backgroundColor = cBgColor;
            parent.window.opener.document.forms['frgrm']['cSucId' +nSecuencia].style.color = cColor;
            parent.window.opener.document.forms['frgrm']['cDocId' +nSecuencia].style.backgroundColor = cBgColor;
            parent.window.opener.document.forms['frgrm']['cDocId' +nSecuencia].style.color = cColor;
            parent.window.opener.document.forms['frgrm']['cDocSuf'+nSecuencia].style.backgroundColor = cBgColor;
            parent.window.opener.document.forms['frgrm']['cDocSuf'+nSecuencia].style.color = cColor;
            parent.window.opener.document.forms['frgrm']['cDocTip'+nSecuencia].style.backgroundColor = cBgColor;
            parent.window.opener.document.forms['frgrm']['cDocTip'+nSecuencia].style.color = cColor;
            parent.window.opener.document.forms['frgrm']['cCliId' +nSecuencia].style.backgroundColor = cBgColor;
            parent.window.opener.document.forms['frgrm']['cCliId' +nSecuencia].style.color = cColor;
            parent.window.opener.document.forms['frgrm']['cCliDv' +nSecuencia].style.backgroundColor = cBgColor;
            parent.window.opener.document.forms['frgrm']['cCliDv' +nSecuencia].style.color = cColor;
            parent.window.opener.document.forms['frgrm']['cCliNom'+nSecuencia].style.backgroundColor = cBgColor;
            parent.window.opener.document.forms['frgrm']['cCliNom'+nSecuencia].style.color = cColor;
          </script>
          <?php $nSecuencia++;
        }
			}
			if ($nCanDo == 0) {
				f_Mensaje(__FILE__,__LINE__,"No se encontraron registros.");
			}
		} ?>
		<script languaje = "javascript">
			//cerrar la ventana
			parent.window.close();	
		</script>
	<?php break;
	default:
		//No hace nada	
	break;
}
	
	?>
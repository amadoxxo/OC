<?php
	/**
	* Comprobante(Cartas Bancarias).
	* --- Descripcion: Permite Subir y Guardar Automaticamente Cartas Bancarias.
	* @author Johana Arboleda Ramos <jarboleda@opentecnologia.com.co>
	* @version 001
	*/
	include("../../../../libs/php/utility.php");
	
	/**
	 * Buscando los comprobantes contable de las cartas bancarias
	 */
	
	$qComCod  = "SELECT comcodxx ";
	$qComCod .= "FROM $cAlfa.fpar0117 ";
	$qComCod .= "WHERE comidxxx = \"L\"  AND ";
	$qComCod .= "comtipxx != \"AJUSTES\" AND ";
	$qComCod .= "regestxx = \"ACTIVO\" ORDER BY comcodxx";
	$xComCod  = f_MySql("SELECT","",$qComCod,$xConexion01,"");
	//f_Mensaje(__FILE__,__LINE__,$qComCod." ~ ".mysql_num_rows($xComCod));
	$cComCod = "";
	if (mysql_num_rows($xComCod) > 0) {
		while ($xRCC = mysql_fetch_array($xComCod)) {
			$cComCod .= "{$xRCC['comcodxx']}~";
		}
		$cComCod = substr($cComCod, 0, strlen($cComCod)-1);
	}
?>
	<head>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
		<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
		<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>

		<script languaje = 'javascript'>
		  function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
  			document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
  			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
  	  }

		  function f_Links(xLink,xSwitch,xSecuencia,xType) {
				var nX    = screen.width;
				var nY    = screen.height;
				switch (xLink) {
					case "cCcoId":
						var cCcoId = "";
					  switch (xType) {
					 	  case "GRID": cCcoId = document.forms['frgrm']['cCcoId'+xSecuencia].value.toUpperCase(); break;
					 	  default: 		 cCcoId = document.forms['frgrm']['cCcoId'].value.toUpperCase(); 					  break;
					  }
						if (xSwitch == "VALID") {
							var cPathUrl = "frcba116.php?gModo="+xSwitch+"&gFunction="+xLink+
																			 	 "&gCcoId="+cCcoId+
							                           "&gType="+xType+
							                           "&gSecuencia="+xSecuencia;
							//alert(cPathUrl);
							parent.fmpro.location = cPathUrl;
						} else {
							var nNx      = (nX-300)/2;
							var nNy      = (nY-250)/2;
							var cWinOpt  = "width=300,scrollbars=1,height=250,left="+nNx+",top="+nNy;
							var cPathUrl = "frcba116.php?gModo="+xSwitch+"&gFunction="+xLink+
																					"&gCcoId="+cCcoId+
							                            "&gType="+xType+
							                            "&gSecuencia="+xSecuencia;
							//alert(cPathUrl);
							cWindow = window.open(cPathUrl,xLink,cWinOpt);
				  		cWindow.focus();
						}
					break;
					case "cSccId":
						var cCcoId = ""; var cSccId = ""; var cPucDet = "";
						switch (xType) {
					 	  case "GRID":
					 	  	cCcoId = document.forms['frgrm']['cCcoId'+xSecuencia].value.toUpperCase();
					 	  	cSccId = document.forms['frgrm']['cSccId'+xSecuencia].value.toUpperCase();
					 	  	cPucDet = document.forms['frgrm']['cPucDet'+xSecuencia].value.toUpperCase();
					 	  break;
					 	  default:
					 	  	cCcoId = document.forms['frgrm']['cCcoId'].value.toUpperCase();
					 	  	cSccId = document.forms['frgrm']['cSccId'].value.toUpperCase();
					 	  	var xType = "";
					 	  break;
					  }
						if ((xType == "GRID" && cPucDet != "D") || (xType == "")) {
							if (xSwitch == "VALID") {
								var cPathUrl = "frcba120.php?gModo="+xSwitch+"&gFunction="+xLink+
																					 "&gCcoId="+cCcoId+
								                           "&gSccId="+cSccId+
								                           "&gType="+xType+
								                           "&gSecuencia="+xSecuencia;
								//alert(cPathUrl);
								parent.fmpro.location = cPathUrl;
							} else {
								var nNx      = (nX-300)/2;
								var nNy      = (nY-250)/2;
								var cWinOpt  = "width=300,scrollbars=1,height=250,left="+nNx+",top="+nNy;
								var cPathUrl = "frcba120.php?gModo="+xSwitch+"&gFunction="+xLink+
								                            "&gCcoId="+cCcoId+
								                            "&gSccId="+cSccId+
								                            "&gType="+xType+
								                            "&gSecuencia="+xSecuencia;
								cWindow = window.open(cPathUrl,xLink,cWinOpt);
					  		cWindow.focus();
							}
						} else if (xType == "GRID" && cPucDet == "D") {
							document.forms['frgrm']['cSccId'+xSecuencia].value = document.forms['frgrm']['cComCscC'+xSecuencia].value.toUpperCase();
						}
					break;
					case "cSccId_DocId":
						var cCcoId = ""; var cSccId = ""; var cPucDet = "";
						switch (xType) {
					 	  case "GRID":
						 		cCcoId  = document.forms['frgrm']['cCcoId' +xSecuencia].value.toUpperCase();
					 	  	cSccId  = document.forms['frgrm']['cSccId' +xSecuencia].value.toUpperCase();
					 	  	cPucDet = document.forms['frgrm']['cPucDet'+xSecuencia].value.toUpperCase();
					 	  break;
					 	  default:
						 		cCcoId   = document.forms['frgrm']['cCcoId'].value.toUpperCase();
					 	  	cSccId   = document.forms['frgrm']['cSccId'].value.toUpperCase();
					 	  	var xType = "";
					 	  	document.forms['frgrm']['cSccId'].value = "";
					 	  break;
					  }
						
						if ((xType == "GRID" && cPucDet != "D") || (xType == "")) {
							if (xSwitch == "VALID") {
								var cPathUrl = "frscc121.php?gModo="+xSwitch+"&gFunction="+xLink+
																					 "&gCcoId="+cCcoId+
																					 "&gSccId="+cSccId+
								                           "&gType="+xType+
								                           "&gSecuencia="+xSecuencia;
								//alert(cPathUrl);
								parent.fmpro.location = cPathUrl;
							} else {
								var nNx      = (nX-300)/2;
								var nNy      = (nY-250)/2;
								var cWinOpt  = "width=300,scrollbars=1,height=250,left="+nNx+",top="+nNy;
								var cPathUrl = "frscc121.php?gModo="+xSwitch+"&gFunction="+xLink+
																						"&gCcoId="+cCcoId+
								                            "&gSccId="+cSccId+
								                            "&gType="+xType+
								                            "&gSecuencia="+xSecuencia;
								cWindow = window.open(cPathUrl,xLink,cWinOpt);
					  		cWindow.focus();
							}
						} else {
							if (xType == "GRID" && cPucDet == "D") {
								document.forms['frgrm']['cSccId'+xSecuencia].value = document.forms['frgrm']['cComCscC'+xSecuencia].value.toUpperCase();
							}
						}
					break;
					case "cDoiId":
						if(document.forms['frgrm']['cDoiId'+xSecuencia].id == "" || document.forms['frgrm']['cDoiId'+xSecuencia].id != document.forms['frgrm']['cDoiId'+xSecuencia].value) {
							if (xSwitch == "VALID") {
								var cPathUrl = "frcbados.php?gModo="+xSwitch+"&gFunction="+xLink+
																					 "&gDoiId="+document.forms['frgrm']['cDoiId'+xSecuencia].value.toUpperCase()+
																					 "&gType="+xType+
															             "&gSecuencia="+xSecuencia;
								//alert(cPathUrl);
								parent.fmpro.location = cPathUrl;
							} else {
								var nNx      = (nX-600)/2;
								var nNy      = (nY-300)/2;
								var cWinOpt  = "width=600,scrollbars=1,height=300,left="+nNx+",top="+nNy;
								var cPathUrl = "frcbados.php?gModo="+xSwitch+"&gFunction="+xLink+
																						"&gDoiId="+document.forms['frgrm']['cDoiId'+xSecuencia].value.toUpperCase()+
																						"&gType="+xType+
																            "&gSecuencia="+xSecuencia;
								//alert(cPathUrl);
								cWindow = window.open(cPathUrl,xLink,cWinOpt);
					  		cWindow.focus();
							}
						}
					break;
					case "cDosCto":
						if(document.forms['frgrm']['cDosCto'+xSecuencia].id == "" || document.forms['frgrm']['cDosCto'+xSecuencia].id != document.forms['frgrm']['cDosCto'+xSecuencia].value) {
							if (xSwitch == "VALID") {
								var cPathUrl = "frcbacto.php?gModo="+xSwitch+"&gFunction="+xLink+
																					 "&gDoiId="+document.forms['frgrm']['cDosCto'+xSecuencia].value.toUpperCase()+
																					 "&gType="+xType+
															             "&gSecuencia="+xSecuencia;
								//alert(cPathUrl);
								parent.fmpro.location = cPathUrl;
							} else {
								var nNx      = (nX-600)/2;
								var nNy      = (nY-300)/2;
								var cWinOpt  = "width=600,scrollbars=1,height=300,left="+nNx+",top="+nNy;
								var cPathUrl = "frcbacto.php?gModo="+xSwitch+"&gFunction="+xLink+
																						"&gDoiId="+document.forms['frgrm']['cDosCto'+xSecuencia].value.toUpperCase()+
																						"&gType="+xType+
																            "&gSecuencia="+xSecuencia;
								//alert(cPathUrl);
								cWindow = window.open(cPathUrl,xLink,cWinOpt);
					  		cWindow.focus();
							}
						}
					break;
					case "cTerIdB":
					case "cTerNomB":
						var cTerTip = document.forms['frgrm']['cTerTipB'].value.toUpperCase();
						var cTerId  = document.forms['frgrm']['cTerIdB'].value.toUpperCase();
						var cTerNom = document.forms['frgrm']['cTerNomB'].value.toUpperCase();

						if (cTerTip != "") {
							if (xSwitch == "VALID") {
								var cPathUrl = "frcba150.php?gModo="+xSwitch+"&gFunction="+xLink+
																					"&gTerTip="+cTerTip+
																					"&gTerId="+cTerId+
																					"&gTerNom="+cTerNom;
								//alert(cPathUrl);
								parent.fmpro.location = cPathUrl;
							} else {
								var nNx      = (nX-600)/2;
								var nNy      = (nY-250)/2;
								var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
								var cPathUrl = "frcba150.php?gModo="+xSwitch+"&gFunction="+xLink+
																					 "&gTerTip="+cTerTip+
																					 "&gTerId="+cTerId+
																					 "&gTerNom="+cTerNom;
								cWindow = window.open(cPathUrl,xLink,cWinOpt);
					  		cWindow.focus();
							}
						} else {
							alert("El Tipo de Tercero esta Vacio, Verifique.");
						}
					break;
				}
		  }

		  function f_Guardar() {

			  document.getElementById('tblErr').innerHTML = '';
			  
			  if (document.forms['frgrm']['cPaso'].value == "SUBIR") { //Cargar el archivo
				  document.cookie="kModo=SUBIR;path="+"/";
			  } else { //Guardar carta bancaria
					document.cookie="kModo=NUEVO;path="+"/";
			  }
			  document.forms['frgrm']['Btn_Subir'].disabled=true;
			  document.forms['frgrm']['Btn_Guardar'].disabled=true;
			  document.forms['frgrm']['nTimesSave'].value++;
			  document.forms['frgrm'].submit();
			}

		  function f_Add_New_Row_Comprobante() {

				var cGrid      = document.getElementById("Grid_Comprobante");
				var nLastRow   = cGrid.rows.length;
				var nSecuencia = nLastRow+1;
				var cTableRow  = cGrid.insertRow(nLastRow);

				var cComSeq  = 'cComSeq'  + nSecuencia; // Secuencia
				var cCcoId   = 'cCcoId'   + nSecuencia; // Centro de Costos
				var cSucId   = 'cSucId'   + nSecuencia; // Sucursal
				var cDocId   = 'cDocId'   + nSecuencia; // Do
			  var cDocSuf  = 'cDocSuf'  + nSecuencia; // Sufijo
			  var cTerId   = 'cTerId'   + nSecuencia; // Hidden (Id del Cliente)
				var cTerNom  = 'cTerNom'  + nSecuencia; // Nombre del Cliente
				var cTerIdB  = 'cTerIdB'  + nSecuencia; // Hidden (Id del Girado a)
				var cTerNomB = 'cTerNomB' + nSecuencia; // Nombre del Girado a				
				var cComObs  = 'cComObs'  + nSecuencia; // Hidden (Observacion del Comprobante)
				//Documento Infomativo
				var cDocInf  = 'cDocInf'  + nSecuencia; // Documento Informativo Aduanera Grancolombiana
				//Do Informativo
				var cDoiTip  = 'cDoiTip'  + nSecuencia;
				var cDoiCod  = 'cDoiCod'  + nSecuencia;
				var cDoiSuc  = 'cDoiSuc'  + nSecuencia;
				var cDoiId   = 'cDoiId'  + nSecuencia;
				var cDoiSuf  = 'cDoiSuf'  + nSecuencia;
				var cDoiCli  = 'cDoiCli'  + nSecuencia;
				var cDoiNom  = 'cDoiNom'  + nSecuencia;				 
				//Cto Informativo
				var cDosCto  = 'cDosCto'  + nSecuencia; // Hidden DO Cruce - Concepto Contable
				var cCtoId   = 'cCtoId'   + nSecuencia; // Id del Concepto
				var cCtoDes  = 'cCtoDes'  + nSecuencia; // Descripcion del Concepto
				var nComBase = 'nComBase' + nSecuencia; // Base
				var nComIva  = 'nComIva'  + nSecuencia; // Iva
				var nComRte  = 'nComRte'  + nSecuencia; // Retefte
				var nComVlr  = 'nComVlr'  + nSecuencia; // Valor
				var nComVlrNF= 'nComVlrNF'+ nSecuencia; // Valor NIIF
				var cBanId   = 'cBanId'   + nSecuencia; // Id Banco
				var cBanCta  = 'cBanCta'  + nSecuencia; // Cta Corriente Bta
				var cComTCB  = 'cComTCB'  + nSecuencia; // Cte Bancario
				var cComNCB  = 'cComNCB'  + nSecuencia; // Numero Cte Bancario
				var cComCod  = 'cComCod'  + nSecuencia; // Codigo Comprobante
		
		    var TD_xAll = cTableRow.insertCell(0);
		    TD_xAll.innerHTML  = "<input type = 'text'   Class = 'letra' style = 'width:030;text-align:center' name = "+cComSeq+"  value = "+f_Str_Pad(nSecuencia,3,"0","STR_PAD_LEFT")+"  readonly>";

				var TD_xAll = cTableRow.insertCell(1);
				TD_xAll.innerHTML  = "<input type = 'text'   Class = 'letra' style = 'width:040;text-align:center' name = "+cCcoId+"  readonly>";			                    

				var TD_xAll = cTableRow.insertCell(2);
				TD_xAll.innerHTML  = "<input type = 'text'   Class = 'letra' style = 'width:030;text-align:center' name = "+cSucId+"  readonly>";

				var TD_xAll = cTableRow.insertCell(3);
				TD_xAll.innerHTML  = "<input type = 'text'   Class = 'letra' style = 'width:070' name = "+cDocId+"  readonly>";

				var TD_xAll = cTableRow.insertCell(4);
				TD_xAll.innerHTML   = "<input type = 'text'   Class = 'letra' style = 'width:030;text-align:center' name = "+cDocSuf+" readonly>";

				var TD_xAll = cTableRow.insertCell(5);				
				TD_xAll.innerHTML  = "<input type = 'hidden' Class = 'letra' style = 'width:0'   name = "+cTerId+" readonly>";
				TD_xAll.innerHTML += "<input type = 'text'   Class = 'letra' style = 'width:087' name = "+cTerNom+" readonly>";

				var TD_xAll = cTableRow.insertCell(6);
				TD_xAll.innerHTML  = "<input type = 'hidden' Class = 'letra' style = 'width:0'   name = "+cTerIdB+" readonly>";
				TD_xAll.innerHTML += "<input type = 'text'   Class = 'letra' style = 'width:088' name = "+cTerNomB+" readonly>";
				TD_xAll.innerHTML += "<input type = 'hidden' Class = 'letra' style = 'width:0'   name = "+cComObs+" readonly>";

				var TD_xAll = cTableRow.insertCell(7);
				TD_xAll.innerHTML  = "<input type = 'text'   Class = 'letra' style = 'width:060' name = "+cDocInf+" onBlur = 'javascript:this.value=this.value.toUpperCase()'>";

				var TD_xAll = cTableRow.insertCell(8);
				TD_xAll.innerHTML   = "<input type = 'hidden' Class = 'letra' style = 'width:0'   name = "+cDoiTip+" readonly>";
				TD_xAll.innerHTML  += "<input type = 'hidden' Class = 'letra' style = 'width:0'   name = "+cDoiCod+" readonly>";
				TD_xAll.innerHTML  += "<input type = 'hidden' Class = 'letra' style = 'width:0'   name = "+cDoiSuc+" readonly>";
				TD_xAll.innerHTML  += "<input type = 'text'   Class = 'letra' style = 'width:070' name = "+cDoiId+" onBlur = 'javascript:this.value=this.value.toUpperCase();f_Links(\"cDoiId\",\"VALID\",\""+nSecuencia+"\",\"GRID\");'>";
				TD_xAll.innerHTML  += "<input type = 'hidden' Class = 'letra' style = 'width:0'   name = "+cDoiSuf+" readonly>";
				TD_xAll.innerHTML  += "<input type = 'hidden' Class = 'letra' style = 'width:0'   name = "+cDoiCli+" readonly>";
				TD_xAll.innerHTML  += "<input type = 'hidden' Class = 'letra' style = 'width:0'   name = "+cDoiNom+" readonly>";

				var TD_xAll = cTableRow.insertCell(9);
				TD_xAll.innerHTML  = "<input type = 'text'   Class = 'letra' style = 'width:070' name = "+cDosCto+" onBlur = 'javascript:this.value=this.value.toUpperCase();f_Links(\"cDosCto\",\"VALID\",\""+nSecuencia+"\",\"GRID\");'>";
				
				var TD_xAll = cTableRow.insertCell(10);
				TD_xAll.innerHTML  = "<input type = 'text'   Class = 'letra' style = 'width:070' name = "+cCtoId+"  readonly>";

				var TD_xAll = cTableRow.insertCell(11);
				TD_xAll.innerHTML  = "<input type = 'text'   Class = 'letra' style = 'width:070' name = "+cCtoDes+" readonly>";				

				var TD_xAll = cTableRow.insertCell(12);
				TD_xAll.innerHTML  = "<input type = 'text'   Class = 'letra' style = 'width:055;text-align:right'  name = "+nComBase+" onBlur = 'javascript:f_FixFloat(this)'>";

				var TD_xAll = cTableRow.insertCell(13);
				TD_xAll.innerHTML  = "<input type = 'text'   Class = 'letra' style = 'width:055;text-align:right'  name = "+nComIva+" onBlur = 'javascript:f_FixFloat(this)'>";

				var TD_xAll = cTableRow.insertCell(14);
				TD_xAll.innerHTML  = "<input type = 'text'   Class = 'letra' style = 'width:055;text-align:right'  name = "+nComRte+" onBlur = 'javascript:f_FixFloat(this)'>";

				var TD_xAll = cTableRow.insertCell(15);
				TD_xAll.innerHTML  = "<input type = 'text'   Class = 'letra' style = 'width:055;text-align:right'  name = "+nComVlr+" onBlur = 'javascript:f_FixFloat(this)'>";

				var TD_xAll = cTableRow.insertCell(16);
				TD_xAll.innerHTML  = "<input type = 'text'   Class = 'letra' style = 'width:055;text-align:right'  name = "+nComVlrNF+" onBlur = 'javascript:f_FixFloat(this)'>";

				var TD_xAll = cTableRow.insertCell(17);
				TD_xAll.innerHTML  = "<input type = 'text'   Class = 'letra' style = 'width:040;text-align:center' name = "+cBanId+" readonly>";
				
				var TD_xAll = cTableRow.insertCell(18);
				TD_xAll.innerHTML  = "<input type = 'text'   Class = 'letra' style = 'width:080;text-align:center' name = "+cBanCta+" readonly>";

				var TD_xAll = cTableRow.insertCell(19);
				TD_xAll.innerHTML  = "<input type = 'text'   Class = 'letra' style = 'width:050;text-align:center' name = "+cComTCB+" readonly>";

				var TD_xAll = cTableRow.insertCell(20);
				TD_xAll.innerHTML  = "<input type = 'text'   Class = 'letra' style = 'width:070' name = "+cComNCB+" readonly>";

				var TD_xAll = cTableRow.insertCell(21);
				TD_xAll.innerHTML  = "<select Class = 'text' name = "+cComCod+" style = 'width:050'></select>";
				
				var mComCod = document.forms['frgrm']['cComCod'].value.split("~");
				document.forms['frgrm'][cComCod].options[0] = new Option("","");
				for (var i=0; i<mComCod.length; i++) {
					document.forms['frgrm'][cComCod].options[i+1] = new Option(mComCod[i],mComCod[i]);
				}
					
				document.forms['frgrm']['nSecuencia'].value = nSecuencia;
			}

		  function f_Delete_Row_All() {
			  var cGrid = document.getElementById("Grid_Comprobante");
			  var nLastRow = cGrid.rows.length;
			  
				for (i=1;i<=nLastRow;i++) {
					var nLastRow01 = cGrid.rows.length;
					cGrid.deleteRow(nLastRow01 - 1);
				}
				document.forms['frgrm']['nSecuencia'].value = 0;				
	    }
		</script>
	</head>
	<body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
		<!-- PRIMERO PINTO EL FORMULARIO -->
		<center>
			<table border ="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<form name = "frgrm" enctype='multipart/form-data' action = "frcbaaug.php" method = "post" target="fmpro">
							<input type = "hidden" name = "nSecuencia"  value = "0">
							<input type = "hidden" name = "nTimesSave"  value = "0">
							<input type = "hidden" name = "cModo"       value = "<?php echo $_COOKIE['kModo']; ?>">
							<input type = "hidden" name = "cPaso"       value = "SUBIR">
							<input type = "hidden" name = "cComCod"     value = "<?php echo $cComCod; ?>">
							<table border ="0" cellpadding="0" cellspacing="0" id ="tblArchivo">
								<tr>
									<td>
										<fieldset>
						   				<legend>Cague Autom&aacute;tico <?php echo $_COOKIE['kProDes'] ?></legend>
											<table border = "0" cellpadding = "0" cellspacing = "0" width="380">
												<tr>
													<td Class="name">
														<input type = "file" Class = "letra" style = "width:380px;height:22px" name = "cArcPla">
													</td>
												</tr>
											</table>
										</fieldset>
										<table border = "0" cellpadding = "0" cellspacing = "0" width="400">
											<tr height="21">
												<td width="218" height="21">&nbsp;</td>
												<td width="91" height="21" Class="name" >
													<input type="button" name="Btn_Subir" id="Btn_Subir" value="Subir" Class = "name"  style = "width:91;height:21;background:url(<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif) no-repeat;border:0px"
														onclick = "javascript:f_Guardar()">
												</td>
										  	<td width="91" height="21" Class="name" >
										  		<input type="button" value="Salir" Class = "name"  style = "width:91;height:21;background:url(<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif) no-repeat;border:0px"
														onClick = "javascript:f_Retorna()">
										  	</td>
										  </tr>
										</table>
										<table border="0" cellpadding="0" cellspacing="0" width="400px">										 			
											<tr>
												<td id="tblErr"></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>							
							<table border ="0" cellpadding="0" cellspacing="0" id ="tblDatos">
								<tr>
									<td>
										<fieldset>
						   				<legend>Cague Autom&aacute;tico <?php echo $_COOKIE['kProDes'] ?></legend>
						   				<table border = "0" cellpadding = "0" cellspacing = "0" style = "width:1280">
									<?php $cCols = f_Format_Cols(64); echo $cCols; ?>
									<tr>
										<td Class = "name" colspan = "9">Contrapartida por Proveedor<br>
											<select type = "text" name = "cConPro" style = "width:180">
											  <option value="SI" selected>SI</option>
  											<option value="NO">NO</option>
											</select>
										</td>
										<td Class = "name" colspan = "5">
											<a href = "javascript:document.forms['frgrm']['cCcoId'].value='';
																						document.forms['frgrm']['cSccId'].value='';
																						document.forms['frgrm']['cSccId_SucId'].value='';
																						document.forms['frgrm']['cSccId_DocId'].value='';
																						document.forms['frgrm']['cSccId_DocSuf'].value='';
																						f_Links('cCcoId','VALID');" id="id_href_cCcoId">Centro Costo</a><br>
											<input type = "text" Class = "letra" style = "width:100;text-align:center" name = "cCcoId" maxlength = "10"
												onfocus="javascript:this.value='';
																						document.forms['frgrm']['cSccId'].value='';
																						document.forms['frgrm']['cSccId_SucId'].value='';
																						document.forms['frgrm']['cSccId_DocId'].value='';
																						document.forms['frgrm']['cSccId_DocSuf'].value='';
																						this.style.background='#00FFFF'"
												onblur = "javascript:f_Links('cCcoId','VALID');
																						 this.style.background='#FFFFFF';">
										<td Class = "name" colspan = "5">
											<a href = "javascript:document.forms['frgrm']['cSccId'].value='';
																						document.forms['frgrm']['cSccId_SucId'].value='';
																						document.forms['frgrm']['cSccId_DocId'].value='';
																						document.forms['frgrm']['cSccId_DocSuf'].value='';
																						f_Links('cSccId','VALID');" id="id_href_cSccId">Sub Centro</a><br>
											<input type = "text" Class = "letra" style = "width:100;text-align:center" name = "cSccId" maxlength = "20"
												onfocus="javascript:this.value='';
																						document.forms['frgrm']['cSccId_SucId'].value='';
																						document.forms['frgrm']['cSccId_DocId'].value='';
																						document.forms['frgrm']['cSccId_DocSuf'].value='';
																						this.style.background='#00FFFF'"
												onblur = "javascript:f_Links('cSccId','VALID');
																						 this.style.background='#FFFFFF';">
											<input type = "hidden" Class = "letra" style = "width:80" name = "cSccId_SucId"  readonly>
											<input type = "hidden" Class = "letra" style = "width:80" name = "cSccId_DocId"  readonly>
											<input type = "hidden" Class = "letra" style = "width:80" name = "cSccId_DocSuf" readonly>
										</td>
										<td Class = "name" colspan = "4">
											  <a href='javascript:show_calendar("frgrm.dComFec")' id="id_href_dComFec">Fecha</a><br>
												<input type = "text" Class = "letra" style = "width:80;text-align:center"
												  name = "dComFec" value = "<?php echo date('Y-m-d') ?>" onBlur = "javascript:f_Date(this)">
										</td>
										<td Class = "name" colspan = "3">Hora<br>
											<input type = "text" Class = "letra" style = "width:60;text-align:center"
										    name = "tRegHCre" value = "<?php echo date('H:i:s') ?>" readonly>
										</td>		
										<td Class = "name" colspan = "6">Asunto<br>
											<select type = "text" name = "cComAsu" style = "width:120">
											  <option value = ''>[SELECCIONE]</option>
  											<option value="CHEQUE">CHEQUE</option>
  											<option value="CARTA">CARTA</option>
											</select>
										</td>								
										<td Class = "name" colspan = "5">Tasa Cambio<br>
											<input type = "text" Class = "letra" style = "width:100;text-align:right" name = "nTasaCambio" value="<?php echo f_Buscar_Tasa_Cambio(date('Y-m-d'),"USD"); ?>"
												onFocus="javascript:this.style.background='#00FFFF';"  
						    	      onBlur = "javascript:this.style.background='#FFFFFF';">
  									</td>
										<td colspan = "03"></td>
										<!-- Tercero Cabecera -->
										<td Class = "name" colspan = "5">Tipo Tercero<br>
											<select Class = "letrase" name = "cTerTipB" style = "width:100"
											  onchange="javascript:document.forms['frgrm']['cTerIdB'].value  = '';
													                   document.forms['frgrm']['cTerDVB'].value  = '';
											  										 document.forms['frgrm']['cTerNomB'].value = '';">
											  <option value = 'CLICLIXX'>CLIENTE</option>
												<option value = 'CLIPROCX' selected>PROVEEDORC</option>
												<option value = 'CLIPROEX'>PROVEEDORE</option>
												<option value = 'CLIEFIXX'>E. FINANCIERA</option>
												<option value = 'CLISOCXX'>SOCIO</option>
												<option value = 'CLIEMPXX'>EMPLEADO</option>
												<option value = 'CLIOTRXX'>OTROS</option>
											</select>
										</td>
										<td Class = "name" colspan = "5">
											<a href = "javascript:document.forms['frgrm']['cTerIdB'].value  = '';
																		  		  document.forms['frgrm']['cTerNomB'].value = '';
																						document.forms['frgrm']['cTerDVB'].value  = '';
																						f_Links('cTerIdB','VALID')" id="id_href_cTerIdB">Nit</a><br>
											<input type = "text" Class = "letra" style = "width:100;text-align:center" name = "cTerIdB"
												onfocus="javascript:document.forms['frgrm']['cTerIdB'].value  = '';
            						  									document.forms['frgrm']['cTerNomB'].value = '';
																				    document.forms['frgrm']['cTerDVB'].value  = '';
													                  this.style.background='#00FFFF'"
									    	onBlur = "javascript:this.value=this.value.toUpperCase();
																		         f_Links('cTerIdB','VALID');
																		         this.style.background='#FFFFFF'">
										</td>
										<td Class = "name" colspan = "1">Dv<br>
											<input type = "text" Class = "letra" style = "width:20;text-align:center" name = "cTerDVB" readonly>
										</td>
										<td Class = "name" colspan = "10">Beneficiario<br>
											<input type = "text" Class = "letra" style = "width:200" name = "cTerNomB"
									    	onfocus="javascript:document.forms['frgrm']['cTerIdB'].value  = '';
            						  									document.forms['frgrm']['cTerNomB'].value = '';
																				    document.forms['frgrm']['cTerDVB'].value  = '';
													                  this.style.background='#00FFFF'"
									    	onBlur = "javascript:this.value=this.value.toUpperCase();
													                   f_Links('cTerNomB','VALID');
													                   this.style.background='#FFFFFF'">
										</td>
										<td colspan = "03"></td>
									</tr>																										
									<tr><td colspan = "64"><hr></td></tr>
 	          	    </table>
											<table border = "0" cellpadding = "0" cellspacing = "0" style = "width:1280">
												<tr>
													<td width = "030"  class = "name" bgcolor = "<?php echo $vSysStr['system_row_par_color_ini'] ?>"><center>Sq</center></td>
													<td width = "040"  class = "name" bgcolor = "<?php echo $vSysStr['system_row_par_color_ini'] ?>"><center>CC</center></td>
													<td width = "030"  class = "name" bgcolor = "<?php echo $vSysStr['system_row_par_color_ini'] ?>"><center>Suc.</center></td>
													<td width = "070"  class = "name" bgcolor = "<?php echo $vSysStr['system_row_par_color_ini'] ?>"><center>Do</center></td>
													<td width = "030"  class = "name" bgcolor = "<?php echo $vSysStr['system_row_par_color_ini'] ?>"><center>Suf</center></td>
													<td width = "087"  class = "name" bgcolor = "<?php echo $vSysStr['system_row_par_color_ini'] ?>"><center>Cliente</center></td>
													<td width = "088"  class = "name" bgcolor = "<?php echo $vSysStr['system_row_par_color_ini'] ?>"><center>Girado a</center></td>
													<td width = "060"  class = "name" bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>"><center>Doc.Info</center></td>
													<td width = "070"  class = "name" bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>"><center>DO Info</center></td>
													<td width = "070"  class = "name" bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>"><center>Cto.Info</center></td>
													<td width = "070"  class = "name" bgcolor = "<?php echo $vSysStr['system_row_par_color_ini'] ?>"><center>Cto.</center></td>
													<td width = "070"  class = "name" bgcolor = "<?php echo $vSysStr['system_row_par_color_ini'] ?>"><center>Concepto</center></td>												
										 			<td width = "055"  class = "name" bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>"><center>Base</center></td>
													<td width = "055"  class = "name" bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>"><center>Iva</center></td>
													<td width = "055"  class = "name" bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>"><center>Retencion</center></td>
													<td width = "055"  class = "name" bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>"><center>Valor Local</center></td>
													<td width = "055"  class = "name" bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>"><center>Valor NIIF</center></td>
													<td width = "040"  class = "name" bgcolor = "<?php echo $vSysStr['system_row_par_color_ini'] ?>"><center>Banco</center></td>
													<td width = "080"  class = "name" bgcolor = "<?php echo $vSysStr['system_row_par_color_ini'] ?>"><center>Cta.Corriente</center></td>
													<td width = "050"  class = "name" bgcolor = "<?php echo $vSysStr['system_row_par_color_ini'] ?>"><center>Cte.Ban</center></td>
													<td width = "070"  class = "name" bgcolor = "<?php echo $vSysStr['system_row_par_color_ini'] ?>"><center>No.Cte.Ban</center></td>												
													<td width = "050"  class = "name" bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>"><center>Cod.</center></td>												
												</tr>
											</table>
											<table border = "0" cellpadding = "0" cellspacing = "0" style = "width:1220"  id = "Grid_Comprobante">
											</table>
										</fieldset>
										<table border = "0" cellpadding = "0" cellspacing = "0" width="1280">
											<tr height="21">
												<td width="1098" height="21">&nbsp;</td>
												<td width="91" height="21" Class="name" >
													<input type="button" name="Btn_Guardar" value="Guardar" Class = "name"  style = "width:91;height:21;background:url(<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif) no-repeat;border:0px"
														onclick = "javascript:f_Guardar()">
												</td>
										  	<td width="91" height="21" Class="name" >
										  		<input type="button" name="Btn_Salir" value="Salir" Class = "name"  style = "width:91;height:21;background:url(<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif) no-repeat;border:0px"
														onClick = "javascript:f_Retorna()">
										  	</td>
										  </tr>
										</table>
									</td>
								</tr>
							</table>												
						</form>
					</td>
				</tr>
			</table>
		</center>
		<?php switch ($_COOKIE['kModo']) {
			case "AUTOMATICA": ?>
				<script languaje = "javascript">
					document.getElementById('tblArchivo').style.display = "block";
					document.getElementById('tblDatos').style.display   = "none";
				</script>
			<?php break;
		} ?>
	</body>
</html>
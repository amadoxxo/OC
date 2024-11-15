<?php
	/**
	 * Proceso Concepto Contable.
	 * --- Descripcion: Permite Crear un Nuevo Concepto Contables.
	 * @author
	 * @package opentecnologia
	 * @version 001
	 */
	include("../../../../libs/php/utility.php");
?>
<html>
	<head>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
		<script language="javascript">
  		function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
  				document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
  				parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
  	  }

  	  function f_ValAp(){
			  var bandG=0;
			  var bandP=0;
			  var bandN=0;
			  var bandC=0;
			  var bandD=0;
			  var bandL29=0;
			  var bandRec=0;
			  var bandCauPE=0;
			  var bandCajMen=0;
			  var bandFac=0;
	  	  switch (document.frgrm.gIteration.value) {
  			  case "1":
  				  if (document.frgrm.oCheck.checked == true) {
  				    vMatr=document.frgrm.oCheck.value.split("~");
              if(vMatr[0]=="G"){
                document.getElementById('ad_egr').style.display="block";
              } else{
                document.getElementById('ad_egr').style.display="none";
              }
              if(vMatr[0]=="P"){
                document.getElementById('ad_TipP').style.display="block";
              } else{
                document.getElementById('ad_TipP').style.display="none";
              }
              if(vMatr[0]=="N"){
                 document.getElementById('ad_TipN').style.display="block";
              } else{
                document.getElementById('ad_TipN').style.display="none";
              }
              if(vMatr[0]=="C"){
                 document.getElementById('ad_TipC').style.display="block";
              } else{
                document.getElementById('ad_TipC').style.display="none";
              }
              if(vMatr[0]=="D"){
                  document.getElementById('ad_TipD').style.display="block";
              } else{
                  document.getElementById('ad_TipD').style.display="none";
              }
              if(vMatr[0]=="R"){
                document.getElementById('ad_RecCaja').style.display="block";
              } else{
                document.getElementById('ad_RecCaja').style.display="none";
              }
              if(vMatr[0]=="M"){
                document.getElementById('ad_CajaMenor').style.display="block";
              } else{
                document.getElementById('ad_CajaMenor').style.display="none";
              }
              if(vMatr[0]=="F"){
                document.getElementById('ad_Facturacion').style.display="block";
              } else{
                document.getElementById('ad_Facturacion').style.display="none";
              }
              if(vMatr[0]=="L"){
                document.getElementById('ad_cartasban').style.display="block";
              } else{
                document.getElementById('ad_cartasban').style.display="none";
              }
  					}
  				break;
  				default:
  					for (i=0;i<document.frgrm.oCheck.length;i++) {
  						if (document.frgrm.oCheck[i].checked == true) {
  						  vMatr=document.frgrm.oCheck[i].value.split("~");
  						  if(vMatr[0]=="G"){
                  bandG=1;
                }
                if(vMatr[0]=="P"){
                  bandP=1;
                }
                if(vMatr[0]=="N"){
                  bandN=1;
                }
                if(vMatr[0]=="C"){
                  bandC=1;
                }
                if(vMatr[0]=="D"){
                  bandD=1;
                }
                if(vMatr[0]=="R"){
                  bandRec=1;
                }
                if(vMatr[0]=="M"){
                  bandCajMen=1;
                }
                if(vMatr[0]=="L"){
                  bandL29=1;
                }
                if(vMatr[0]=="F"){
                  bandFac=1;
                }
  						}
  					}

  					if(bandG==1){
  					  document.getElementById('ad_egr').style.display="block";
  					}
  					if(bandG==0){
  					  document.getElementById('ad_egr').style.display="none";
  					}

  					if(bandP==1){
  					  document.getElementById('ad_TipP').style.display="block";
  					}
  					if(bandP==0){
  					  document.getElementById('ad_TipP').style.display="none";
  					}

  					if(bandN==1){
  					  document.getElementById('ad_TipN').style.display="block";
  					}
  					if(bandN==0){
  					  document.getElementById('ad_TipN').style.display="none";
  					}

  					if(bandC==1){
  					  document.getElementById('ad_TipC').style.display="block";
  					}
  					if(bandC==0){
  					  document.getElementById('ad_TipC').style.display="none";
  					}

  					if(bandD==1){
  					  document.getElementById('ad_TipD').style.display="block";
  					}
  					if(bandD==0){
  					  document.getElementById('ad_TipD').style.display="none";
  					}

  					if(bandRec==1){
  					  document.getElementById('ad_RecCaja').style.display="block";
  					}
  					if(bandRec==0){
  					  document.getElementById('ad_RecCaja').style.display="none";
  					}

  					if(bandCajMen==1){
  					  document.getElementById('ad_CajaMenor').style.display="block";
  					}
  					if(bandCajMen==0){
  					  document.getElementById('ad_CajaMenor').style.display="none";
  					}

  					if(bandFac==1){
  					  document.getElementById('ad_Facturacion').style.display="block";
  					}
  					if(bandFac==0){
  					  document.getElementById('ad_Facturacion').style.display="none";
  					}

  					if(bandL29==1){
  					  document.getElementById('ad_cartasban').style.display="block";
  					}
  					if(bandL29==0){
  					  document.getElementById('ad_cartasban').style.display="none";
  					}
   				break;
  			}
			}

			function f_Valida_Check() {
			  switch (document.frgrm.gIteration.value) {
  			  case "1":
    				if(document.frgrm.vRecords.value > 0){
	  			    if(document.frgrm.oCheck.checked == true && (document.frgrm.oCheckD.checked != true && document.frgrm.oCheckC.checked != true)){
	  					  alert('No Puede Existir un Comprobante sin parametrizacion de Debitos y Creditos, Verifique');
	  					}else{
	  					  if (document.frgrm.oCheck.checked != true){
	  					    alert('Debe Seleccionar al menos un Comprobante, Verifique');
	  					  }else{
	  					    document.forms['frgrm'].submit();
	  					  }
	            }
    				}
          break;
  				default:
  				  var band=0;
  					if(document.frgrm.vRecords.value > 0){
	  				  for (i=0;i<document.frgrm.oCheck.length;i++){
	  						if(document.frgrm.oCheck[i].checked == true && (document.frgrm.oCheckD[i].checked != true && document.frgrm.oCheckC[i].checked != true)){
	    					  band=1;
	   					  }
	  				  }
	  				  if(band==0){
	  					  document.frgrm.cComMemoApl.value="";
	  						document.forms['frgrm'].submit();
	  					}else{
	  					  alert('No Puede Existir un Comprobante sin parametrizacion de Debitos y Creditos, Verifique');
	  					}
  					}
  			  break;
  			}
			}

			function f_Activar_Check() {
        switch (document.frgrm.gIteration.value) {
  			  case "1":
  			    if(document.frgrm.oCheck[i].checked == true && (document.frgrm.oCheckD[i].checked == true || document.frgrm.oCheckC[i].checked == true)){
    				  document.frgrm.oCheckD[i].disabled = false;
    				  document.frgrm.oCheckC[i].disabled = false;
    				}else{
    				  if(document.frgrm.oCheck[i].checked == false ){
                document.frgrm.oCheckD[i].checked  = false;
      				  document.frgrm.oCheckC[i].checked  = false;
      				  document.frgrm.oCheckD[i].disabled = true;
      				  document.frgrm.oCheckC[i].disabled = true;
    				  }else{
    				    if(document.frgrm.oCheck[i].checked == true){
        			    document.frgrm.oCheckD[i].disabled = false;
        				  document.frgrm.oCheckC[i].disabled = false;
        				}
    				  }
  				  }
			    break;
  				default:
  				  for (i=0;i<document.frgrm.oCheck.length;i++) {
    				  if(document.frgrm.oCheck[i].checked == true && (document.frgrm.oCheckD[i].checked == true || document.frgrm.oCheckC[i].checked == true)){
    				    document.frgrm.oCheckD[i].disabled = false;
    				    document.frgrm.oCheckC[i].disabled = false;
    				  }else{
    				    if(document.frgrm.oCheck[i].checked == false ){
      				    document.frgrm.oCheckD[i].checked  = false;
      				    document.frgrm.oCheckC[i].checked  = false;
      				    document.frgrm.oCheckD[i].disabled = true;
      				    document.frgrm.oCheckC[i].disabled = true;
    				    }else{
    				      if(document.frgrm.oCheck[i].checked == true){
        				    document.frgrm.oCheckD[i].disabled = false;
        				    document.frgrm.oCheckC[i].disabled = false;
        				  }
    				    }
  				    }
  				  }
			   break;
  			}
			}

			function f_Carga_Data() {
			  var band=0;
	  	  document.frgrm.cComMemo.value="|";
	  	  switch (document.frgrm.gIteration.value) {
  			  case "1":
  				  if (document.frgrm.oCheck.checked == true) {
  					  document.frgrm.cComMemo.value += document.frgrm.oCheck.value;
  					  if(document.frgrm.oCheckD.checked!=true && document.frgrm.oCheckC.checked!=true){
  						  band=1;
  					  }else{
  					    if(document.frgrm.oCheckD.checked==true && document.frgrm.oCheckC.checked!= true){
  					      document.frgrm.cComMemo.value +="~"+"D";
  					    }
  					    if(document.frgrm.oCheckC.checked==true && document.frgrm.oCheckD.checked!=true){
  					      document.frgrm.cComMemo.value +="~"+"C";
  					    }
  					    if(document.frgrm.oCheckC.checked==true && document.frgrm.oCheckD.checked==true){
    					    document.frgrm.cComMemo.value +="~";
    					  }
  					    document.frgrm.cComMemo.value+="|";
  					  }
  					}
  				break;
  				default:
  					var zSw_Prv = 0;
  					for (i=0;i<document.frgrm.oCheck.length;i++) {
  						if (document.frgrm.oCheck[i].checked == true && band==0) {
  							document.frgrm.cComMemo.value += document.frgrm.oCheck[i].value;

  							if(document.frgrm.oCheckD[i].checked!=true && document.frgrm.oCheckC[i].checked!=true){
    						  band=1;
    					  }else{
    					    if(document.frgrm.oCheckD[i].checked==true && document.frgrm.oCheckC[i].checked!=true){
    					      document.frgrm.cComMemo.value +="~"+"D";
    					    }
    					    if(document.frgrm.oCheckC[i].checked==true && document.frgrm.oCheckD[i].checked!=true){
    					      document.frgrm.cComMemo.value +="~"+"C";
    					    }
    					    if(document.frgrm.oCheckC[i].checked==true && document.frgrm.oCheckD[i].checked==true){
    					      document.frgrm.cComMemo.value +="~";
    					    }
                  document.frgrm.cComMemo.value+="|";
                }
  						}
  					}
  				break;
  			}
	  	}

      function f_Links(xLink,xSwitch,xIteration) {
				var zX    = screen.width;
				var zY    = screen.height;
				switch (xLink){
					case "cCacId":
						if (xSwitch == "VALID") {
							var zRuta  = "frcac144.php?gWhat=VALID&gFunction=cCacId&gCacId="+document.frgrm.cCacId.value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else{
							if(xSwitch == "WINDOW"){
			  				var zNx     = (zX-560)/2;
								var zNy     = (zY-300)/2;
								var zWinPro = 'width=560,scrollbars=1,height=300,left='+zNx+',top='+zNy;
								var zRuta   = "frcac144.php?gWhat=WINDOW&gFunction=cCacId&gCacId="+document.frgrm.cCacId.value.toUpperCase()+"";
								zWindow = window.open(zRuta,"zWindow",zWinPro);
						  	zWindow.focus();
							}else{
	              if (xSwitch == "EXACT"){
	                var zRuta  = "frcac144.php?gWhat=EXACT&gFunction=cCacId&gCacId="+document.frgrm.cCacId.value.toUpperCase()+"";
	                parent.fmpro.location = zRuta;
	              }
	            }
            }
				  break;
				  case "cCacDes":
						if (xSwitch == "VALID") {
							var zRuta  = "frcac144.php?gWhat=VALID&gFunction=cCacDes&gCacDes="+document.frgrm.cCacDes.value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else{
							if(xSwitch == "WINDOW"){
			  				var zNx     = (zX-560)/2;
								var zNy     = (zY-300)/2;
								var zWinPro = 'width=560,scrollbars=1,height=300,left='+zNx+',top='+zNy;
								var zRuta   = "frcac144.php?gWhat=WINDOW&gFunction=cCacDes&gCacDes="+document.frgrm.cCacDes.value.toUpperCase()+"";
								zWindow = window.open(zRuta,"zWindow",zWinPro);
						  	zWindow.focus();
							}else{
	              if (xSwitch == "EXACT"){
	                var zRuta  = "frcac144.php?gWhat=EXACT&gFunction=cCacDes&gCacDes="+document.frgrm.cCacDes.value.toUpperCase()+"";
	                parent.fmpro.location = zRuta;
	              }
	            }
            }
				  break;
  				case "cPucId":
						if (xSwitch == "VALID") {
							var zRuta  = "frcto115.php?gWhat=VALID&gFunction=cPucId&cPucId="+document.frgrm.cPucId.value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
		  				var zNx     = (zX-600)/2;
							var zNy     = (zY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frcto115.php?gWhat=WINDOW&gFunction=cPucId&cPucId="+document.frgrm.cPucId.value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
					  	zWindow.focus();
						}
				  break;
				  case "vComId":
						if (xSwitch == "VALID") {
							var zRuta  = "frpdc117.php?gWhat=VALID&gFunction=vComId&gComId="+document.forms['frgrm']['vComId'+xIteration].value.toUpperCase()+"&gIteration="+xIteration;
							parent.fmpro.location = zRuta;
						} else {
		  				var zNx     = (zX-350)/2;

							var zNy     = (zY-250)/2;
							var zWinPro = 'width=350,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frpdc117.php?gWhat=WINDOW&gFunction=vComId&gComId="+document.forms['frgrm']['vComId'+xIteration].value.toUpperCase()+"&gIteration="+xIteration;
							zWindow = window.open(zRuta,"zWindow",zWinPro);
					  	zWindow.focus();
						}
				  break;
				  case "cCtoSucri":
						if (xSwitch == "VALID") {
							var zRuta  = "frpar008.php?gWhat=VALID&gFunction=cCtoSucri&gCtoSucri="+document.forms['frgrm']['cCtoSucri'].value.toUpperCase();
							parent.fmpro.location = zRuta;
						} else {
		  				var zNx     = (zX-350)/2;

							var zNy     = (zY-250)/2;
							var zWinPro = 'width=350,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frpar008.php?gWhat=WINDOW&gFunction=cCtoSucri&gCtoSucri="+document.forms['frgrm']['cCtoSucri'].value.toUpperCase();
							zWindow = window.open(zRuta,"zWindow",zWinPro);
					  	zWindow.focus();
						}
				  break;
					case "cCceId":
						if (xSwitch == "VALID") {
							var zRuta  = "frcto156.php?gWhat=VALID&gFunction="+xLink+"&gCceId="+document.forms['frgrm']['cCceId'].value;
							parent.fmpro.location = zRuta;
						} else {
							if (xSwitch == "WINDOW") {
								var zNx     = (zX-600)/2;
								var zNy     = (zY-250)/2;

								var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
								var zRuta   = "frcto156.php?gWhat=WINDOW&gFunction="+xLink+"&gCceId="+document.forms['frgrm']['cCceId'].value;
								zWindow = window.open(zRuta,"zWindow",zWinPro);
								zWindow.focus();
							} 
							else {
								if(xSwitch == "EXACT"){
									var zRuta  = "frcto156.php?gWhat=EXACT&gFunction="+xLink+"&gCceId="+document.forms['frgrm']['cCceId'].value;
									parent.fmpro.location = zRuta;
								}
							}
						}
					break;
					case "cUmeId":
						if (xSwitch == "VALID") {
							var zRuta  = "frcto157.php?gWhat=VALID&gFunction="+xLink+"&gUmeId="+document.forms['frgrm']['cUmeId'].value;
							parent.fmpro.location = zRuta;
						} else {
							if (xSwitch == "WINDOW") {
								var zNx     = (zX-600)/2;
								var zNy     = (zY-250)/2;

								var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
								var zRuta   = "frcto157.php?gWhat=WINDOW&gFunction="+xLink+"&gUmeId="+document.forms['frgrm']['cUmeId'].value;
								zWindow = window.open(zRuta,"zWindow",zWinPro);
								zWindow.focus();
							} else {
								if(xSwitch == "EXACT"){
									var zRuta  = "frcto157.php?gWhat=EXACT&gFunction="+xLink+"&gUmeId="+document.forms['frgrm']['cUmeId'].value;
									parent.fmpro.location = zRuta;
								}
							}
						}
					break;
					case "cCodLineaNeg":
						if (xSwitch == "VALID") {
							var zRuta  = "frserz03.php?gWhat=VALID&gFunction="+xLink+"&gCodLineaNeg="+document.forms['frgrm']['cCodLineaNeg'+xIteration].value.toUpperCase()+"&gSecuencia="+xIteration;
							parent.fmpro.location = zRuta;
						} else {
							var zNx = (zX-800)/2;
							var zNy = (zY-500)/2;

							var zWinPro = 'width=800,scrollbars=1,height=500,left='+zNx+',top='+zNy;
							var zRuta   = "frserz03.php?gWhat=WINDOW&gFunction="+xLink+
																				"&gCodLineaNeg="+document.forms['frgrm']['cCodLineaNeg'+xIteration].value.toUpperCase()+
																				"&gSecuencia="+xIteration;
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
				}
			}

      function f_Activar_Sucursal_Ica(xAccion) {
        var cRuta  = "fractica.php?gAccion="+xAccion;
        parent.fmpro.location = cRuta;
			}

			function f_LastButton() {
				var tbl = document.getElementById('TblMtr');
				var lastRow = tbl.rows.length;
				for (i=1;i<=lastRow;i++) {
					var aRow = document.getElementById('vBtn2' + i);
					if (i < lastRow) {
						aRow.value = '';
					} else {
						aRow.value = 'X';
					}
				}
			}

  	  function f_Enter(e, xGrid, xIteration){
				var code;

				if (!e) var e = window.event;
				if (e.keyCode) code = e.keyCode;
				else if (e.which) code = e.which;{
					if(code==13) {
						if (xGrid == 'Grid_LineaNegocio') {
							fnAddNewRowLineaNegocio('Grid_LineaNegocio')
						} else if(xGrid == 'cCodLineaNeg') {
							f_Links('cCodLineaNeg', 'VALID', xIteration);
						} else {
							f_AddRowNew();
						}
					}
				}
      }

			function f_DelRow(xNumRow) {
				var tbl = document.getElementById('TblMtr');
				var lastRow = tbl.rows.length;
				if (lastRow > 1 && xNumRow == 'X'){
					if (confirm('Realmente Desea Eliminar la Secuencia')){
				  	tbl.deleteRow(lastRow - 1);
				    document.frgrm.gIteration.value = lastRow - 1;
				    f_LastButton();
				  }
				} else {
					alert('Operacion no Permitida');
				}
	    }

		  function f_AddRowNew() {
  			var tbl       = document.getElementById('TblMtr');
  			var lastRow   = tbl.rows.length;
  			var iteration = lastRow+1;
  			var TR        = tbl.insertRow(lastRow);
  			var lRow      = iteration-1;

  			var zSeq      = iteration;
  		  var vSecId    = 'vSecId'   + iteration;
  			var vComId    = 'vComId'    + iteration;
  			var vComCod   = 'vComCod'   + iteration;
  			var vComMov   = 'vComMov'   + iteration;
  			var vBtn2     = 'vBtn2'     + iteration;

  			var TD_xSecId = TR.insertCell(0);
  			TD_xSecId.innerHTML  = "<input type = 'text'   Class = 'letra' style = 'width:020;color:#FF0000' name = "+vSecId+"  value = "+zSeq+"  readonly>";

  			var TD_xComId = TR.insertCell(1);
  			TD_xComId.innerHTML    = "<input type = 'text'   Class = 'letra' style = 'width:150;text-align:left' name = "+vComId+" onBlur = 'javascript:this.value=this.value.toUpperCase();f_Links(\"vComId\",\"VALID\",\""+iteration+"\")'>";

  			var TD_xComCod = TR.insertCell(2);
  			TD_xComCod.innerHTML    = "<input type = 'text'   Class = 'letra' style = 'width:150;text-align:left' name = "+vComCod+" onBlur = 'javascript:this.value=this.value.toUpperCase();' >";

  			var TD_xComMov = TR.insertCell(3);
  			TD_xComMov.innerHTML    = "<input type = 'text'   Class = 'letra' style = 'width:160;text-align:left' name = "+vComMov+" onBlur = 'javascript:this.value=this.value.toUpperCase();' onKeyUp = 'javascript:f_Enter(event,this.name);' >";

  			var TD_vBtn2 = TR.insertCell(4);
  			TD_vBtn2.innerHTML = "<input type = 'button' Class = 'letra' style = 'width:020' id = "+vBtn2+" value = '..' onclick = 'javascript:f_DelRow(this.value)'>";

  			document.frgrm.gIteration.value = iteration;
  			f_LastButton();
		  }

		  function f_EnabledCombos() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				document.forms['frgrm']['cPucId'].disabled =false;
		  }

		  function f_DisabledCombos() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				document.forms['frgrm']['cPucId'].disabled =true;
		  }

			function f_ValidacEstado(){
       	var zEstado = document.forms['frgrm']['cEstado'].value.toUpperCase();
       	if(zEstado == 'A' || zEstado == 'AC' || zEstado == 'ACT' || zEstado == 'ACTI' || zEstado == 'ACTIV' || zEstado == 'ACTIVO'){
       		zEstado = 'ACTIVO';
       	} else {
       		if(zEstado == 'I' || zEstado == 'IN' || zEstado == 'INA' || zEstado == 'INAC' || zEstado == 'INACT' || zEstado == 'INACTI' || zEstado == 'INACTIV' || zEstado == 'INACTIVO') {
       			zEstado = 'INACTIVO';
       		} else {
       			zEstado = '';
       		}
       	}
       	document.forms['frgrm']['cEstado'].value = zEstado;
    	}

			function fnHabilitarCodCompraEficiente() {
				if (document.forms['frgrm']['cCtoClapr'].value == 001) {
					document.forms['frgrm']['cCceId'].disabled = false;
					document.forms['frgrm']['cCceDes'].disabled = false;
					document.getElementById('idComEfi').href	 = "javascript:document.frgrm.cCceId.value  = ''; document.frgrm.cCceDes.value  = ''; f_Links('cCceId','VALID')";
				} else {
					document.forms['frgrm']['cCceId'].value    = "";
					document.forms['frgrm']['cCceId'].disabled = true;
					document.forms['frgrm']['cCceDes'].disabled = true;
					document.getElementById('idComEfi').href   = "javascript:alert('Opcion No Permitida')";
				}
			}

			/**
			 * Permite agregar una nueva grilla en la sección de linea de negocio.
			 */
			function fnAddNewRowLineaNegocio(xTabla) {
				var cGrid        = document.getElementById(xTabla);
				var nLastRow     = cGrid.rows.length;
				var nSecuencia   = nLastRow+1;
				var cTableRow    = cGrid.insertRow(nLastRow);
				var cCodLineaNeg = 'cCodLineaNeg' + nSecuencia; // Codigo Linea de Negocio
				var cDesLineaNeg = 'cDesLineaNeg' + nSecuencia; // Descripcion Linea de Negocio
				var cCtaIngreso  = 'cCtaIngreso'  + nSecuencia; // Cuenta de Ingreso
				var cCtaCosto    = 'cCtaCosto'    + nSecuencia; // Cuenta de Costo
				var oBtnDelLinea = 'oBtnDelLinea' + nSecuencia; // Boton de Borrar Row

				TD_xAll = cTableRow.insertCell(0);
				TD_xAll.style.width  = "160px";
				TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:160;text-align:left' name = '"+cCodLineaNeg+"' id = '"+cCodLineaNeg+"'' onkeyup='javascript:f_Enter(event, \"cCodLineaNeg\", \""+nSecuencia+"\")'>";
																		
				TD_xAll = cTableRow.insertCell(1);
				TD_xAll.style.width  = "160px";
				TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:160;text-align:left' name = '"+cDesLineaNeg+"' id = '"+cDesLineaNeg+"'' readonly>";
							
				TD_xAll = cTableRow.insertCell(2);
				TD_xAll.style.width  = "160px";
				TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:160;text-align:left' name = '"+cCtaIngreso+"' id = '"+cCtaIngreso+"''>";

				TD_xAll = cTableRow.insertCell(3);
				TD_xAll.style.width  = "180px";
				TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:180;text-align:left' name = '"+cCtaCosto+"' id = '"+cCtaCosto+"' onKeyUp='javascript:f_Enter(event,\"Grid_LineaNegocio\");'>";

				TD_xAll = cTableRow.insertCell(4);
				TD_xAll.style.width  = "20px";
				TD_xAll.innerHTML    = "<input type = 'button' style = 'width:020;text-align:center' name = "+oBtnDelLinea+" id = "+oBtnDelLinea+" value = 'X' "+
																"onClick = 'javascript:fnDeleteRowLineaNegocio(this.value,\""+nSecuencia+"\",\""+xTabla+"\");'>";
																
				document.forms['frgrm']['nSecuencia_' + xTabla].value = nSecuencia;
			}

			/**
			 * Permite eliminar una grilla de la sección de linea de negocio.
			 */
			function fnDeleteRowLineaNegocio(xNumRow,xSecuencia,xTabla) {
        var cGrid = document.getElementById(xTabla);
        var nLastRow = cGrid.rows.length;
        if (nLastRow > 1 && xNumRow == "X") {
          if (confirm("Realmente Desea Eliminar La Linea de Negocio ["+document.forms['frgrm']['cCodLineaNeg' + xSecuencia].value+"]?")){ 
            if(xSecuencia < nLastRow){
              var j=0;
              for(var i=xSecuencia;i<nLastRow;i++){
                j = parseFloat(i)+1;
                document.forms['frgrm']['cCodLineaNeg' + i].value = document.forms['frgrm']['cCodLineaNeg' + j].value;
                document.forms['frgrm']['cDesLineaNeg' + i].value = document.forms['frgrm']['cDesLineaNeg' + j].value;
                document.forms['frgrm']['cCtaIngreso'  + i].value = document.forms['frgrm']['cCtaIngreso' + j].value;
                document.forms['frgrm']['cCtaCosto'    + i].value = document.forms['frgrm']['cCtaCosto' + j].value;
              }
            }
            cGrid.deleteRow(nLastRow - 1);
            document.forms['frgrm']['nSecuencia_' + xTabla].value = nLastRow - 1;
          }
        } else {
          alert("No se Pueden Eliminar Todas las Secuencias, Verifique.");
        }
      }

			/**
			 * Elimina todas la grillas de la sección de linea de negocio.
			 */
			function fnBorrarLineaNegocio(xTabla){
        document.getElementById(xTabla).innerHTML = "";
        fnAddNewRowLineaNegocio(xTabla);
      }
	  </script>
	</head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="740">
				<tr>
					<td>
						<fieldset>
						  <legend><?php echo ucfirst(strtolower($_COOKIE['kModo']))." ".$_COOKIE['kProDes'] ?></legend>
							<center>
					  	<form name = 'frgrm' action = 'frctogra.php' method = 'post' target='fmpro'>
						 	  <input type="hidden" name="gIteration" value="0" >
						 	  <input type="hidden" name="cComMemo" value="" >
						 	  <input type="hidden" name="cComMemoApl" value="" >
								<input type = "hidden" name = "nSecuencia_Grid_LineaNegocio">
							 	<center>
							 	  <fieldset style="width:720px">
                    <legend>Datos Generales</legend>
      							<table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
  							 			<?php $nCol = f_Format_Cols(35);
  							 			echo $nCol;?>
									    <tr>
  								      <td Class = "name" colspan = "5">
  												<a href = "javascript:document.frgrm.cPucId.value  = '';
  																			  		  document.frgrm.cPucDes.value = '';
  																							document.frgrm.cPucRet.value  = '';
  																							f_Activar_Sucursal_Ica('NO');
  																							f_Links('cPucId','VALID')" id="IdCta">Cuenta</a><br>
  												<input type = "text" Class = "letra" style = "width:100" name = "cPucId"
  										    	onBlur = "javascript:this.value=this.value.toUpperCase();
  																			         f_Links('cPucId','VALID');
  																			         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
  										    	onFocus="javascript:document.frgrm.cPucId.value  = '';
  	            						  									document.frgrm.cPucDes.value = '';
  																					    document.frgrm.cPucRet.value = '';
  																					    f_Activar_Sucursal_Ica('NO');
  														                  this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
  										  </td>
  										  <td Class = "name" colspan = "3">Ret (%)<br>
  												<input type = "text" Class = "letra" style = "width:60" name = "cPucRet" readonly>
  										  </td>
  										  <td Class = "name" colspan = "27">Descripcion<br>
  												 <input type = "text" Class = "letra" style = "width:540" name = "cPucDes" readonly>
  										  </td>
  									  </tr>
  									  <tr>
												<td Class = "name" colspan = "7">
  												Concepto<br>
  												<input type = "text" Class = "letra" style = "width:140" name = "cCtoId" onBlur = "javascript:this.value=this.value.toUpperCase();" readonly>
  										  </td>
  										  <td Class = "name" colspan = "28">
  												Nit Busqueda Documento Cruce<br>
  												<select name="cCtoNit" style="width:560">
  												  <option value="">[SELECCIONE]</option>
  												  <option value="TERCERO">TERCERO</option>
  												  <option value="CLIENTE">CLIENTE</option>
  												</select>
  										  </td>
											</tr>
  										<tr>
    									  <td Class = "name" colspan = "9">
    											Calculo Automatico Base<br>
    											<select name="cCtoVlr01" style="width:180">
    											  <option value="">[SELECCIONE]</option>
    											  <option value="SI">SI</option>
    											  <option value="NO">NO</option>
    											</select>
    									  </td>
    									  <td Class = "name" colspan = "8">
    											Calculo Automatico Iva<br>
    											<select name="cCtoVlr02" style="width:160">
    											  <option value="">[SELECCIONE]</option>
    											  <option value="SI">SI</option>
    											  <option value="NO">NO</option>
    											</select>
    									  </td>
  									   <td Class = "name" colspan = "8">
  												Concepto para Anticipo<br>
  												<select name="cCtoAnt" style="width:160">
  												  <option value="">[SELECCIONE]</option>
  												  <option value="SI">SI</option>
  												  <option value="NO" selected>NO</option>
  												</select>
  										  </td>
  										  <td Class = "name" colspan = "10">
  												Concepto para Pagos a Terceros<br>
  												<select name="cCtoPcc" style="width:200">
  												  <option value="">[SELECCIONE]</option>
  												  <option value="SI">SI</option>
  												  <option value="NO" selected>NO</option>
  												</select>
  											</td>
											</tr>
									  </table>
									</fieldset>
									<?php
									/*** Si la variable Categoriacion Conceptos Factuacion esta encendida se habilita el menu de Categoria Conceptos***/
									if($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI'){
										?>
										<fieldset style="width:720px">
	                    <legend>Categoria Concepto</legend>
	      							<table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
	  							 			<?php $nCol = f_Format_Cols(35);
	  							 			echo $nCol;?>
										    <tr>
	  								      <td Class = "name" colspan = "5">
	  												<a href = "javascript:document.frgrm.cCacId.value  = '';
	  																			  		  document.frgrm.cCacDes.value = '';
	  																							f_Links('cCacId','VALID')" id="IdCac">C&oacute;digo</a><br>
	  												<input type = "text" Class = "letra" style = "width:100" name = "cCacId"
	  										    	onBlur = "javascript:this.value=this.value.toUpperCase();
	  																			         f_Links('cCacId','VALID');
	  																			         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
	  										    	onFocus="javascript:document.frgrm.cCacId.value  ='';
	  	            						  									document.frgrm.cCacDes.value = '';
	  														                  this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
	  										  </td>
	  										  <td Class = "name" colspan = "30">Descripci&oacute;n<br>
	  												 <input type = "text" Class = "letra" style = "width:600" name = "cCacDes"
	  												 onBlur = "javascript:this.value=this.value.toUpperCase();
	  																			         f_Links('cCacDes','VALID');
	  																			         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'">
	  										  </td>
	  									  </tr>
										  </table>
										</fieldset>
										<?php
									}
									?>
									<fieldset style="width:720px">
					  	      <legend>Comprobantes</legend>
									  <table border = '1' cellpadding = '0' cellspacing = '0' width='700'>
  							 			<?php $nCol = f_Format_Cols(35);
  							 			echo $nCol;?>
  									  <tr>
  								      <td Class = "name" colspan = "5" align="center">
  												Comprobante
  										  </td>
  										  <td Class = "name" colspan = "4" align="center">
  												Codigo
  										  </td>
  										  <td Class = "name" colspan = "16" align="center">
  												Descripci&oacute;n
  										  </td>
  										  <td Class = "name" colspan = "4" align="center">
  												Seleccione
  										  </td>
  										  <td Class = "name" colspan = "3" align="center">
  												Debito
  										  </td>
  										  <td Class = "name" colspan = "3" align="center">
  												Credito
  										  </td>
  									  </tr>
  									  <?php
                      $qSqlCom  = "SELECT * ";
                			$qSqlCom .= "FROM $cAlfa.fpar0117 ";
                      $qSqlCom .= "WHERE ";
                      $qSqlCom .= "comtipxx NOT IN (\"AUTOFACTURA\",\"AUTOFACTURA_NC\") AND ";
                      $qSqlCom .= "regestxx = \"ACTIVO\" ";
                      $qSqlCom .= "ORDER BY comidxxx,comcodxx ";
                			$xSqlCom  = f_MySql("SELECT","",$qSqlCom,$xConexion01,"");

                			while ($zRCom = mysql_fetch_array($xSqlCom)) {?>
    									  <script>
    									   document.forms['frgrm']['gIteration'].value ++;
    									  </script>
    									  <tr>
    								      <td Class = "name" colspan = "5" align="center">
    												<?php echo $zRCom['comidxxx'] ?>
    										  </td>
    										  <td Class = "name" colspan = "4" align="center">
    												<?php echo str_pad($zRCom['comcodxx'],3,"0",STR_PAD_LEFT) ?>
    										  </td>
    										   <td Class = "name" colspan = "16" align="left" style="padding-left:5px">
    												<?php echo substr($zRCom['comdesxx'],0,42) ?>
    										  </td>
    										  <td Class = "name" colspan = "4" align="center">
    												<input type="checkbox" name="oCheck" value="<?php echo $zRCom['comidxxx'].'~'.$zRCom['comcodxx'] ?>" onclick="javascript:f_Carga_Data();f_Activar_Check();f_ValAp();" >
    										  </td>
    										  <td Class = "name" colspan = "3" align="center">
    												<input type="checkbox" name="oCheckD" value="<?php echo $zRCom['comidxxx'].'~'.$zRCom['comcodxx'] ?>" onclick="javascript:f_Carga_Data();f_Activar_Check();" disabled>
    										  </td>
    										  <td Class = "name" colspan = "3" align="center">
    												<input type="checkbox" name="oCheckC" value="<?php echo $zRCom['comidxxx'].'~'.$zRCom['comcodxx'] ?>" onclick="javascript:f_Carga_Data();f_Activar_Check();" disabled>
    										  </td>
    									  </tr>
    									  <?php
                			}?>
									  </table>
									  <!-- Controla el numero de Check -->
									   <input type="hidden" name="vRecords" value = "<?php echo mysql_num_rows($xSqlCom) ?>">
									</fieldset>
									<fieldset id="ad_TipP" style="border:4px ridge; padding:3;width:720px">
					  	    <legend>Datos Adicionales Para Comprobantes Tipo P</legend>
                    <table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
   							 			<?php $nCol = f_Format_Cols(35);
   							 			echo $nCol;?>
   							 			<tr>
     							 			<td Class = "name" colspan = "25">
     							 			<div id="tblCtoSucri"></div>
	    									</td>
  										</tr>
  									</table>
  									<br>
  									<fieldset style="width:700px">
                    <legend>Datos Adicionales Orden de Nit's para Integraci&oacute;n con Saphiens </legend>
                    	<table border = '0' cellpadding = '0' cellspacing = '0' width='680'>
      							 		<?php $nCol = f_Format_Cols(34);
      							 		echo $nCol;?>
    									  <tr>
									     		<td Class = "name" colspan = "17">N C&oacute;digo<br>
  													<select name="cCtoNit1p" style="width:340">
  												  	<option value="">[SELECCIONE]</option>
  												  	<option value="CLIENTE">CLIENTE</option>
  												  	<option value="TERCERO">TERCERO</option>
  													</select>
  										 		</td>
  										 		<td Class = "name" colspan = "17">NP C&oacute;digo<br>
  													<select name="cCtoNit2p" style="width:340">
  												  	<option value="">[SELECCIONE]</option>
  												  	<option value="CLIENTE">CLIENTE</option>
  												  	<option value="TERCERO">TERCERO</option>
  													</select>
  										  	</td>
									   		</tr>
    									</table>
    									<br>
  									</fieldset>
					  	    </fieldset>
					  	    <fieldset id="ad_TipN" style="border:4px ridge; padding:3;width:720px">
					  	    <legend>Datos Adicionales Para Comprobantes Tipo N</legend>
                    <table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
   							 			<?php $nCol = f_Format_Cols(35);
   							 			echo $nCol;?>
   							 			<tr>
     							 			<td Class = "name" colspan = "35">Descripcion<br>
                          <input type = "text" Class = "letra" style = "width:700" name = "cCtoDesn"
    										   	onBlur = "javascript:this.value=this.value.toUpperCase();
    													                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'";
    										   	onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'"; >
    										</td>
  										</tr>
  									</table>
  									<fieldset style="width:700px">
                    <legend>Datos Adicionales Orden de Nit's para Integraci&oacute;n con Saphiens </legend>
                    	<table border = '0' cellpadding = '0' cellspacing = '0' width='680'>
      							 		<?php $nCol = f_Format_Cols(34);
      							 		echo $nCol;?>
    									  <tr>
									     		<td Class = "name" colspan = "17">N C&oacute;digo<br>
  													<select name="cCtoNit1n" style="width:340">
  												  	<option value="">[SELECCIONE]</option>
  												  	<option value="CLIENTE">CLIENTE</option>
  												  	<option value="TERCERO">TERCERO</option>
  													</select>
  										 		</td>
  										 		<td Class = "name" colspan = "17">NP C&oacute;digo<br>
  													<select name="cCtoNit2n" style="width:340">
  												  	<option value="">[SELECCIONE]</option>
  												  	<option value="CLIENTE">CLIENTE</option>
  												  	<option value="TERCERO">TERCERO</option>
  													</select>
  										  	</td>
									   		</tr>
    									</table>
    									<br>
  									</fieldset>
					  	    </fieldset>
					  	    <fieldset id="ad_TipC" style="border:4px ridge; padding:3;width:720px">
					  	    <legend>Datos Adicionales Para Comprobantes Tipo C</legend>
                    <table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
   							 			<?php $nCol = f_Format_Cols(35);
   							 			echo $nCol;?>
   							 			<tr>
     							 			<td Class = "name" colspan = "35">Descripcion<br>
                          <input type = "text" Class = "letra" style = "width:700" name = "cCtoDesc"
    										   	onBlur = "javascript:this.value=this.value.toUpperCase();
    													                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'";
    										   	onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'"; >
    										</td>
  										</tr>
  									</table>
  									<fieldset style="width:700px">
                    <legend>Datos Adicionales Orden de Nit's para Integraci&oacute;n con Saphiens </legend>
                    	<table border = '0' cellpadding = '0' cellspacing = '0' width='680'>
      							 		<?php $nCol = f_Format_Cols(34);
      							 		echo $nCol;?>
    									  <tr>
									     		<td Class = "name" colspan = "17">N C&oacute;digo<br>
  													<select name="cCtoNit1c" style="width:340">
  												  	<option value="">[SELECCIONE]</option>
  												  	<option value="CLIENTE">CLIENTE</option>
  												  	<option value="TERCERO">TERCERO</option>
  													</select>
  										 		</td>
  										 		<td Class = "name" colspan = "17">NP C&oacute;digo<br>
  													<select name="cCtoNit2c" style="width:340">
  												  	<option value="">[SELECCIONE]</option>
  												  	<option value="CLIENTE">CLIENTE</option>
  												  	<option value="TERCERO">TERCERO</option>
  													</select>
  										  	</td>
									   		</tr>
    									</table>
    									<br>
  									</fieldset>
					  	    </fieldset>
					  	    <fieldset id="ad_TipD" style="border:4px ridge; padding:3;width:720px">
					  	    <legend>Datos Adicionales Para Comprobantes Tipo D</legend>
                    <table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
   							 			<?php $nCol = f_Format_Cols(35);
   							 			echo $nCol;?>
   							 			<tr>
     							 			<td Class = "name" colspan = "35">Descripcion<br>
                          <input type = "text" Class = "letra" style = "width:700" name = "cCtoDesd"
    										   	onBlur = "javascript:this.value=this.value.toUpperCase();
    													                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'";
    										   	onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'"; >
    										</td>
  										</tr>
  									</table>
  									<fieldset style="width:700px">
                    <legend>Datos Adicionales Orden de Nit's para Integraci&oacute;n con Saphiens</legend>
                    	<table border = '0' cellpadding = '0' cellspacing = '0' width='680'>
      							 		<?php $nCol = f_Format_Cols(34);
      							 		echo $nCol;?>
    									  <tr>
									     		<td Class = "name" colspan = "17">N C&oacute;digo<br>
  													<select name="cCtoNit1d" style="width:340">
  												  	<option value="">[SELECCIONE]</option>
  												  	<option value="CLIENTE">CLIENTE</option>
  												  	<option value="TERCERO">TERCERO</option>
  													</select>
  										 		</td>
  										 		<td Class = "name" colspan = "17">NP C&oacute;digo<br>
  													<select name="cCtoNit2d" style="width:340">
  												  	<option value="">[SELECCIONE]</option>
  												  	<option value="CLIENTE">CLIENTE</option>
  												  	<option value="TERCERO">TERCERO</option>
  													</select>
  										  	</td>
									   		</tr>
    									</table>
    									<br>
  									</fieldset>
					  	    </fieldset>
                  <fieldset id="ad_egr" style="border:4px ridge; padding:3;width:720px">
					  	    	<legend>Datos Adicionales Para Comprobantes Tipo G</legend>
					  	      <table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
   							 			<?php $nCol = f_Format_Cols(35);
   							 			echo $nCol;?>
   							 			<tr>
     							 			<td Class = "name" colspan = "35">Descripcion<br>
   												<input type = "text" Class = "letra" style = "width:700" name = "cCtoDesg"
    										   	onBlur = "javascript:this.value=this.value.toUpperCase();
    													                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'";
    										   	onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'"; >
    										</td>
  										</tr>
  									</table>
  									<fieldset style="width:700px">
                      <legend>Tipo de Concepto </legend>
											<table border = '0' cellpadding = '0' cellspacing = '0' width='680'>
												<?php $nCol = f_Format_Cols(34);
												echo $nCol;?>
												<tr height="30px">
													<td Class = "name" colspan = "11">
														<input type="radio" name="rTipoConcepto" value="4" checked>Normal</td>
													<td Class = "name" colspan = "12">
														<input type="radio" name="rTipoConcepto" value="2">Transferencia Fondos
													</td>
													<td Class = "name" colspan = "11">
														<input type="radio" name="rTipoConcepto" value="3">Aplica Cliente
													</td>
												</tr>
											</table>
  										<table border = '0' cellpadding = '0' cellspacing = '0' width='680'>
   						 					<?php $nCol = f_Format_Cols(34);
   						 					echo $nCol;?>
  								  		<tr>
  								    		<td Class = "name" colspan = "7">
    												Do Informativo<br>
	    											<select name="cADoCruEgr" style="width:140">
	    											  <option value="">[SELECCIONE]</option>
	    											  <option value="SI">SI</option>
	    											  <option value="NO">NO</option>
	    											</select>
	    									  </td>
	    									  <td Class = "name" colspan = "7">
	    											Cto. Informativo<br>
	    											<select name="cAConCruEgr" style="width:140">
	    											  <option value="">[SELECCIONE]</option>
	    											  <option value="SI">SI</option>
	    											  <option value="NO">NO</option>
	    											</select>
	    									  </td>
	    									  <td Class = "name" colspan = "7">
	    											Pago Tributos<br>
	    											<select name="cCtoPta" style="width:140">
	    											  <option value="">[SELECCIONE]</option>
	    											  <option value="SI">SI</option>
	    											  <option value="NO">NO</option>
	    											</select>
	    									  </td>
  									  	  <td Class = "name" colspan = "6">
	    											Pago Vuce<br>
	    											<select name="cCtoPvxxg" style="width:120">
	    											  <option value="">[SELECCIONE]</option>
	    											  <option value="SI">SI</option>
	    											  <option value="NO">NO</option>
	    											</select>
	    									  </td>
	    									  <td Class = "name" colspan = "7">
                            Documento Informativo<br>
                            <select name="cDocInfG" style="width:140">
                              <option value="">[SELECCIONE]</option>
                              <option value="SI">SI</option>
                              <option value="NO">NO</option>
                            </select>
                          </td>
	  									  </tr>
											</table>
  									</fieldset>
	  								<fieldset style="width:700px">
	                    <legend>Datos Adicionales Orden de Nit's para Integraci&oacute;n con Saphiens</legend>
                    	<table border = '0' cellpadding = '0' cellspacing = '0' width='680'>
      							 		<?php $nCol = f_Format_Cols(34);
      							 		echo $nCol;?>
    									  <tr>
									     		<td Class = "name" colspan = "17">N C&oacute;digo<br>
  													<select name="cCtoNit1g" style="width:340">
  												  	<option value="">[SELECCIONE]</option>
  												  	<option value="CLIENTE">CLIENTE</option>
  												  	<option value="TERCERO">TERCERO</option>
  													</select>
  										 		</td>
  										 		<td Class = "name" colspan = "17">NP C&oacute;digo<br>
  													<select name="cCtoNit2g" style="width:340">
  												  	<option value="">[SELECCIONE]</option>
  												  	<option value="CLIENTE">CLIENTE</option>
  												  	<option value="TERCERO">TERCERO</option>
  													</select>
  										  	</td>
									   		</tr>
    									</table>
    									<br>
	  								</fieldset>
					  	    </fieldset>

					  	    <fieldset id="ad_cartasban" style="border:4px ridge; padding:3;width:720px">
                    <legend>Datos Adicionales Cartas Bancarias</legend>
                    <fieldset style="width:700px">
                    	<legend>Tipo de Concepto </legend>
	                    <table border = '0' cellpadding = '0' cellspacing = '0' width='680'>
  	 							 			<?php $nCol = f_Format_Cols(34);
   								 			echo $nCol;?>
   								 			<tr>
     								 			<td Class = "name" colspan = "34">Descripcion<br>
   													<input type = "text" Class = "letra" style = "width:680" name = "cCtoDesL"
    											   	onBlur = "javascript:this.value=this.value.toUpperCase();
    														                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'";
    											   	onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'"; >
    											</td>
  											 </tr>
  									  </table>
                      <table border = '0' cellpadding = '0' cellspacing = '0' width='680'>
      						 			<?php $nCol = f_Format_Cols(34);
      						 			echo $nCol;?>
    								    <tr height="30px">
    								      <td Class = "name" colspan = "17">
    								      	<input type="radio" name="rTipoConceptoL" value="1" checked>Normal</td>
      											<td Class = "name" colspan = "17">
      												<input type="radio" name="rTipoConceptoL" value="2">Aplica Cliente
      											</td>
    								    </tr>
    								  </table>
  										<table border = '0' cellpadding = '0' cellspacing = '0' width='680'>
   							 				<?php $nCol = f_Format_Cols(34);
   							 				echo $nCol;?>
	  									  <tr>
	  									    <td Class = "name" colspan = "7">
	    											Do Informativo<br>
	    											<select name="cADoCruL" style="width:140">
	    											  <option value="">[SELECCIONE]</option>
	    											  <option value="SI">SI</option>
	    											  <option value="NO">NO</option>
	    											</select>
	    									  </td>
	    									  <td Class = "name" colspan = "7">
	    											Concepto Informativo<br>
	    											<select name="cAConCruL" style="width:140">
	    											  <option value="">[SELECCIONE]</option>
	    											  <option value="SI">SI</option>
	    											  <option value="NO">NO</option>
	    											</select>
	    									  </td>
	    									  <td Class = "name" colspan = "7">
	    											Pago Tributos<br>
	    											<select name="cCtoPtaL" style="width:140">
	    											  <option value="">[SELECCIONE]</option>
	    											  <option value="SI">SI</option>
	    											  <option value="NO">NO</option>
	    											</select>
	    									  </td>
	  									    <td Class = "name" colspan = "6">
	    											Pago Vuce<br>
	    											<select name="cCtoPvxxL" style="width:120">
	    											  <option value="">[SELECCIONE]</option>
	    											  <option value="SI">SI</option>
	    											  <option value="NO">NO</option>
	    											</select>
	    									  </td>
	    									  <td Class = "name" colspan = "7">
	    											Documento Informativo<br>
	    											<select name="cDocInfL" style="width:140">
	    											  <option value="">[SELECCIONE]</option>
	    											  <option value="SI">SI</option>
	    											  <option value="NO">NO</option>
	    											</select>
	    									  </td>
	  									  </tr>
												<?php if (f_InList($cAlfa,"DHLEXPRE","DEDHLEXPRE","TEDHLEXPRE")) { ?>
													<tr>
														<td Class = "name" colspan = "17"><br>Aplica como concepto de Carta Bancaria en los Ajustes:</td>
														<td Class = "name" colspan = "4"><br><input type="radio" name="cApConBan" value="SI">SI</td>
														<td Class = "name" colspan = "4"><br><input type="radio" name="cApConBan" value="NO" checked>NO</td>
													</tr>
												<?php }else {
													?>
													<input type="hidden" name="cApConBan" value="NO" >
													<?php
												} ?>
	  									</table>
  									</fieldset>
									  <fieldset style="width:700px">
                    <legend>Datos Adicionales Orden de Nit's para Integraci&oacute;n con Saphiens </legend>
                    	<table border = '0' cellpadding = '0' cellspacing = '0' width='680'>
      							 		<?php $nCol = f_Format_Cols(34);
      							 		echo $nCol;?>
    									  <tr>
									     		<td Class = "name" colspan = "17">N C&oacute;digo<br>
  													<select name="cCtoNit1L" style="width:340">
  												  	<option value="">[SELECCIONE]</option>
  												  	<option value="CLIENTE">CLIENTE</option>
  												  	<option value="TERCERO">TERCERO</option>
  													</select>
  										 		</td>
  										 		<td Class = "name" colspan = "17">NP C&oacute;digo<br>
  													<select name="cCtoNit2L" style="width:340">
  												  	<option value="">[SELECCIONE]</option>
  												  	<option value="CLIENTE">CLIENTE</option>
  												  	<option value="TERCERO">TERCERO</option>
  													</select>
  										  	</td>
									   		</tr>
    									</table>
    									<br>
  									</fieldset>
					  	    </fieldset>
					  	    <fieldset id="ad_RecCaja" style="border:4px ridge; padding:3;width:720px">
					  	      <legend>Datos Adicionales Recibos de Caja</legend>
					  	      <table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
  							 			<?php $nCol = f_Format_Cols(35);
  							 			echo $nCol;?>
  							 			<tr>
     							 			<td Class = "name" colspan = "27">Descripcion<br>
   												<input type = "text" Class = "letra" style = "width:540" name = "cCtoDesr"
    										   	onBlur = "javascript:this.value=this.value.toUpperCase();
    													                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'";
    										   	onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'"; >
    										</td>
  									    <td Class = "name" colspan = "8">
  												Aplica Anticipo<br>
  												<select name="cCtoAntxr" style="width:160">
  												  <option value="">[SELECCIONE]</option>
  												  <option value="SI">SI</option>
  												  <option value="NO">NO</option>
  												</select>
  										  </td>
										  </tr>
									  </table>
									  <fieldset style="width:700px">
                    <legend>Datos Adicionales Orden de Nit's para Integraci&oacute;n con Saphiens </legend>
                    	<table border = '0' cellpadding = '0' cellspacing = '0' width='680'>
      							 		<?php $nCol = f_Format_Cols(34);
      							 		echo $nCol;?>
    									  <tr>
									     		<td Class = "name" colspan = "17">N C&oacute;digo<br>
  													<select name="cCtoNit1r" style="width:340">
  												  	<option value="">[SELECCIONE]</option>
  												  	<option value="CLIENTE">CLIENTE</option>
  												  	<option value="TERCERO">TERCERO</option>
  													</select>
  										 		</td>
  										 		<td Class = "name" colspan = "17">NP C&oacute;digo<br>
  													<select name="cCtoNit2r" style="width:340">
  												  	<option value="">[SELECCIONE]</option>
  												  	<option value="CLIENTE">CLIENTE</option>
  												  	<option value="TERCERO">TERCERO</option>
  													</select>
  										  	</td>
									   		</tr>
    									</table>
    									<br>
  									</fieldset>
					  	    </fieldset>
  					  	  <fieldset id="ad_CajaMenor" style="border:4px ridge; padding:3;width:720px">
					  	      <legend>Datos Adicionales Recibos Caja Menor</legend>
					  	      <table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
  							 			<?php $nCol = f_Format_Cols(35);
  							 			echo $nCol;?>
  							 			<tr>
     							 			<td Class = "name" colspan = "27">Descripcion<br>
   												<input type = "text" Class = "letra" style = "width:540" name = "cCtoDesm"
    										   	onBlur = "javascript:this.value=this.value.toUpperCase();
    													                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'";
    										   	onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'"; >
    										</td>
  										  <td Class = "name" colspan = "8">Tipo de Pago<br>
  												<select name="cCtoTpaxm" style="width:160">
  												  <option value="">[SELECCIONE]</option>
  												  <option value="PROPIOS">PROPIOS</option>
  												  <option value="TERCEROS">TERCEROS</option>
  												</select>
  										  </td>
									    </tr>
									  </table>
									  <fieldset style="width:700px">
                    <legend>Datos Adicionales Orden de Nit's para Integraci&oacute;n con Saphiens </legend>
                    	<table border = '0' cellpadding = '0' cellspacing = '0' width='680'>
      							 		<?php $nCol = f_Format_Cols(34);
      							 		echo $nCol;?>
    									  <tr>
									     		<td Class = "name" colspan = "17">N C&oacute;digo<br>
  													<select name="cCtoNit1m" style="width:340">
  												  	<option value="">[SELECCIONE]</option>
  												  	<option value="CLIENTE">CLIENTE</option>
  												  	<option value="TERCERO">TERCERO</option>
  													</select>
  										 		</td>
  										 		<td Class = "name" colspan = "17">NP C&oacute;digo<br>
  													<select name="cCtoNit2m" style="width:340">
  												  	<option value="">[SELECCIONE]</option>
  												  	<option value="CLIENTE">CLIENTE</option>
  												  	<option value="TERCERO">TERCERO</option>
  													</select>
  										  	</td>
									   		</tr>
    									</table>
    									<br>
  									</fieldset>
  					  	  </fieldset>
  							 	<fieldset id="ad_Facturacion" style="border:4px ridge; padding:3;width:720px">
					  	      <legend>Datos Adicionales Facturacion</legend>
					  	      <table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
  							 			<?php $nCol = f_Format_Cols(25);
  							 			echo $nCol;?>
  							 			<tr>
     							 			<td Class = "name" colspan = "20">Descripcion<br>
   												<input type = "text" Class = "letra" style = "width:400" name = "cCtoDesf"
    										   	onBlur = "javascript:this.value=this.value.toUpperCase();
    													                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'";
    										   	onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'"; >
    										</td>
  										  <td Class = "name" colspan = "15">Clase de Concepto<br>
  												<select name="cCtoClaxf" style="width:300">
  												  <option value="">[SELECCIONE]</option>
  												  <option value="FORMULARIOS">FORMULARIOS</option>
  												  <option value="IMPUESTOFINANCIERO">IMPUESTO FINANCIERO</option>
  												  <!-- Impuestos sobre el impuesto financiero -->
  												  <option value="RETCREIF">IMPUESTO RETECREE SOBRE IF</option>
                            <option value="ARETCREIF">IMPUESTO AUTORETECREE SOBRE IF</option>
                            <option value="RETFTEIF">IMPUESTO RETEFUENTE SOBRE IF</option>
                            <option value="ARETFTEIF">IMPUESTO AUTORETEFUENTE SOBRE IF</option>
                            <option value="RETICAIF">IMPUESTO RETEICA SOBRE IF</option>
                            <option value="ARETICAIF">IMPUESTO AUTORETEICA SOBRE IF</option>
  												  <!-- Fin Impuestos sobre el impuesto financiero -->
  												  <option value="IVAIP">IVA INGRESOS PROPIOS</option>
  												  <option value="SCLIENTE">SALDO A FAVOR DEL CLIENTE COP</option>
  												  <option value="SCLIENTEUSD">SALDO A FAVOR DEL CLIENTE USD</option>
  												  <option value="SAGENCIA">SALDO A FAVOR DE LA AGENCIA COP (IP+PCC)</option>
  												  <option value="SAGENCIAIP">SALDO A FAVOR DE LA AGENCIA COP (IP)</option>
  												  <option value="SAGENCIAPCC">SALDO A FAVOR DE LA AGENCIA COP (PCC)</option>
  												  <option value="SAGENCIAUSD">SALDO A FAVOR DE LA AGENCIA USD (IP+PCC)</option>
  												  <option value="SAGENCIAUSDIP">SALDO A FAVOR DE LA AGENCIA USD (IP)</option>
  												  <option value="SAGENCIAUSDPCC">SALDO A FAVOR DE LA AGENCIA USD (PCC)</option>

  												  <option value="ADCAMBIOI">AJUSTE DIFERENCIA EN CAMBIO INGRESO</option>
  												  <option value="ADCAMBIOG">AJUSTE DIFERENCIA EN CAMBIO GASTO</option>
  												</select>
  										  </td>
									    </tr>
									  </table>
									  <fieldset style="width:700px">
                    <legend>Datos Adicionales Orden de Nit's para Integraci&oacute;n con Saphiens </legend>
                    	<table border = '0' cellpadding = '0' cellspacing = '0' width='680'>
      							 		<?php $nCol = f_Format_Cols(35);
      							 		echo $nCol;?>
    									  <tr>
									     		<td Class = "name" colspan = "17">N C&oacute;digo<br>
  													<select name="cCtoNit1f" style="width:340">
  												  	<option value="">[SELECCIONE]</option>
  												  	<option value="CLIENTE">CLIENTE</option>
  												  	<option value="TERCERO">TERCERO</option>
  													</select>
  										 		</td>
  										 		<td Class = "name" colspan = "17">NP C&oacute;digo<br>
  													<select name="cCtoNit2f" style="width:340">
  												  	<option value="">[SELECCIONE]</option>
  												  	<option value="CLIENTE">CLIENTE</option>
  												  	<option value="TERCERO">TERCERO</option>
  													</select>
  										  	</td>
									   		</tr>
    									</table>
    									<br>
  									</fieldset>
  					  	  </fieldset>
  					  	  <!-- Codigo de integracion con E2K UPS -->
  					  	  <?php if ($cAlfa == "UPSXXXXX" || $cAlfa == "TEUPSXXXXX" || $cAlfa == "TEUPSXXXXP" || $cAlfa == "DEUPSXXXXX") { ?>
  					  	    <fieldset>
                      <legend>Integraci&oacute;n con E2K</legend>
                      <table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
                        <?php $nCol = f_Format_Cols(35);
                        echo $nCol;?>
                        <tr>
                          <td Class = "name" colspan = "35">COD E2K<br>
                            <input type = "text" Class = "letra"  style = "width:180"  name = "cCtoE2k" value = "" maxlength="3"
                                   onblur = "javascript:this.value=this.value.toUpperCase();">
                          </td>
                        </tr>
                      </table>
                    </fieldset>
                  <?php } else { ?>
                    <input type = "hidden" name = "cCtoE2k" value = "">
                  <?php } ?>
  					  	  <!-- Fin Codigo de integracion con E2K UPS -->

									<!-- Codigo Homologacion Aladuanas -->
  					  	  <?php if ($cAlfa == "TEALADUANA" || $cAlfa == "DEALADUANA" || $cAlfa == "ALADUANA") { ?>
  					  	    <fieldset>
                      <legend>C&oacute;digo Homologaci&oacute;n Reporte Facturaci&oacute;n</legend>
                      <table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
                        <?php $nCol = f_Format_Cols(35);
                        echo $nCol;?>
                        <tr>
                          <td Class = "name" colspan = "35">C&oacute;digo<br>
                            <input type = "text" Class = "letra"  style = "width:100"  name = "cCtoChAld" value = "" maxlength="10"
                                   onblur = "javascript:this.value=this.value.toUpperCase();">
                          </td>
                        </tr>
                      </table>
                    </fieldset>
                  <?php } else { ?>
                    <input type = "hidden" name = "cCtoChAld" value = "">
                  <?php } ?>
  					  	  <!-- Fin Codigo Homologacion Aladuanas -->

  					  	  <!-- Inicio Codigo de Integracion con Belcorp -->
  					  	  <?php
  					  	  if ($cAlfa == "ADUANERA" || $cAlfa == "DEADUANERA" || $cAlfa == "TEADUANERA" || $cAlfa == "DEDESARROL" ||  $cAlfa == "DEADUANERP"){
									?>
										<fieldset>
											<legend>Datos para Integraci&oacute;n con Belcorp </legend>
											<table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
											<?php $nCol = f_Format_Cols(35);
  							 			echo $nCol;?>
									    <tr>
  								      <td Class = "name" colspan = "18">
  												Tipo de Documento para Registros de Importados en SAP
  												<input type = "text" Class = "letra" style = "width:360" name = "cPucBel" maxlength="2"
  										    	onblur = "javascript:this.value=this.value.toUpperCase();
				    																	      this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
				    								onFocus= "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
  										  </td>
  										  <td Class = "name" colspan = "17">N&uacute;mero de Asignaci&oacute;n<br>
  												<input type = "text" Class = "letra" style = "width:340" name = "cNuAsBel" maxlength="2" value=""
  												 	onblur = "javascript:this.value=this.value.toUpperCase();
				    																	      this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
				    								onFocus= "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
  										  </td>
  									  </tr>
										</table>
									</fieldset>
									<?php }?>
									<!-- Fin Codigo de Integracion con Belcorp -->
									<!-- Codigo Global ID SAP -->
									<?php if ($cAlfa == "TEDHLEXPRE" || $cAlfa == "DEDHLEXPRE" || $cAlfa == "DHLEXPRE") { ?>
	                    <fieldset>
	                      <legend>Integraci&oacute;n con SAP</legend>
	                      <table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
	                        <?php $nCol = f_Format_Cols(35);
	                        echo $nCol;?>
	                        <tr>
	                          <td Class = "name" colspan = "35">Global ID SAP<br>
	                            <input type = "text" Class = "letra"  style = "width:180"  name = "cCtoSapId" value = "" maxlength="3"
	                                   onblur = "javascript:this.value=this.value.toUpperCase();">
	                          </td>
	                        </tr>
	                      </table>
	                    </fieldset>
	                  <?php } else { ?>
	                    <input type = "hidden" name = "cCtoSapId" value = "">
	                  <?php } ?>
	                <!-- Codigo Global ID SAP -->
  					  	  <!-- Inicio Codigo de Integracion SAP -->
									<?php
									$vBDIntegracionColmasSap = explode("~",$vSysStr['system_integracion_colmas_sap']);
  					  	  if (in_array($cAlfa, $vBDIntegracionColmasSap) == true) {
									?>
										<fieldset>
											<legend>Integraci&oacute;n SAP </legend>
											<table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
    										<?php $nCol = f_Format_Cols(35);
    							 			echo $nCol;?>
                        <tr>
                          <td Class = "name" colspan = "07">Cuenta Costo<br>
                            <input type = "text" Class = "letra"  style = "width:140"  name = "cCtoSapC" maxlength="8"
                              onblur = "javascript:this.value=this.value.toUpperCase();
                                                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                              onFocus= "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                          </td>
                          <td Class = "name" colspan = "07">Cuenta Ingreso<br>
                            <input type = "text" Class = "letra"  style = "width:140"  name = "cCtoSapI" maxlength="8"
                              onblur = "javascript:this.value=this.value.toUpperCase();
                                                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                              onFocus= "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                          </td>
                          <td Class = "name" colspan = "11">C&oacute;digo Impuesto de Compra<br>
                            <input type = "text" Class = "letra"  style = "width:220"  name = "cCtoSapIc" maxlength="8"
                              onblur = "javascript:this.value=this.value.toUpperCase();
                                                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                              onFocus= "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                          </td>
                          <td Class = "name" colspan = "10">C&oacute;digo Impuesto de Venta<br>
                            <input type = "text" Class = "letra"  style = "width:200"  name = "cCtoSapIv" maxlength="8"
                              onblur = "javascript:this.value=this.value.toUpperCase();
                                                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                              onFocus= "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                          </td>
                        </tr>
                        <tr>
                          <td Class = "name" colspan = "05">C&oacute;digo &Aacute;rea<br>
                            <input type = "text" Class = "letra"  style = "width:100"  name = "cCtoSapCA" maxlength="8"
                              onblur = "javascript:this.value=this.value.toUpperCase();
                                                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                              onFocus= "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                          </td>
                          <td Class = "name" colspan = "10">C&oacute;digo L&iacute;nea Importaciones<br>
                            <input type = "text" Class = "letra"  style = "width:200"  name = "cCtoSapLI" maxlength="8"
                              onblur = "javascript:this.value=this.value.toUpperCase();
                                                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                              onFocus= "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                          </td>
													<td Class = "name" colspan = "10">C&oacute;digo L&iacute;nea Exportaciones<br>
                            <input type = "text" Class = "letra"  style = "width:200"  name = "cCtoSapLE" maxlength="8"
                              onblur = "javascript:this.value=this.value.toUpperCase();
                                                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                              onFocus= "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                          </td>
													<td Class = "name" colspan = "10">&nbsp;</td>
                        </tr>
												<tr>
													<td colspan="35">
														<fieldset>
															<legend>L&iacute;nea de Negocio</legend>
															<table border = '0' cellpadding = '0' cellspacing = '0' width='680'>
																<?php $nCol = f_Format_Cols(34); echo $nCol;?>
																<tr>
																	<td colspan="34" class= "clase08" align="right">
																		<?php if ($_COOKIE['kModo'] != "VER") { ?>
																			<img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_create-dir_bg.gif" onClick = "javascript:fnAddNewRowLineaNegocio('Grid_LineaNegocio')" style = "cursor:pointer" title="Adicionar">
																			<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_drop.png" onClick = "javascript:fnBorrarLineaNegocio('Grid_LineaNegocio')" style = "cursor:pointer" title="Eliminar Todos">
																		<?php } ?>
																	</td>                       
																</tr>
																<tr>
																	<td class = "clase08" colspan="08" align="left">L&iacute;nea de Negocio</td>
																	<td class = "clase08" colspan="08" align="left">Descripci&oacute;n Linea</td>
																	<td class = "clase08" colspan="08" align="left">Cuenta de Ingreso</td>
																	<td class = "clase08" colspan="08" align="left">Cuenta de Costo</td>
																	<td class = "clase08" colspan="01" align="right">&nbsp;</td>                       
																</tr>
															</table>
															<table border = "0" cellpadding = "0" cellspacing = "0" width = "680" id = "Grid_LineaNegocio"></table>
														</fieldset>
													</td>
												</tr>
    									</table>
    								</fieldset>
									<?php
                  }?>
									<!-- Fin Codigo de Integracion con Belcorp -->
									
									<!-- Inicio Integracion DSV -->
									<?php if($cAlfa == "DSVSASXX" || $cAlfa == "TEDSVSASXX" || $cAlfa == "DEDSVSASXX") { ?>
										<fieldset>
											<legend>Integraci&oacute;n DSV</legend>
											<table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
											<?php $nCol = f_Format_Cols(35);
												echo $nCol;?>
												<tr>
													<td Class = "name" colspan = "100">Charge Codes Cargowise<br>
														<input type = "text" Class = "letra" style = "width:200" maxlength="20" name = "cCtocWccX"
																	onblur = "javascript:this.value=this.value.toUpperCase(); this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																	onFocus= "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
													</td>
												</tr>
											</table>
										</fieldset>
									<?php } else { ?>
                    <input type = "hidden" name = "cCtocWccX" value = "">
                  <?php } ?>
									<!-- Fin Integracion DVS -->

									<!-- Inicio Datos Adicionales openETL -->
									<fieldset>
										<?php if($cAlfa == "ALPOPULX" || $cAlfa == "TEALPOPULP" || $cAlfa == "TEALPOPULX" || $cAlfa == "DEALPOPULX") { ?>
											<legend>Infomaci&oacute;n Adicional</legend>
										<?php } else { ?>
											<legend>Datos Adicionales openETL</legend>
										<?php } ?>
										<table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
											<?php $nCol = f_Format_Cols(35);
  							 			echo $nCol;?>
									    <tr id="tblCodigoProducto">
  								      <td Class = "name" colspan = "14">
  												Clasificaci&oacute;n Producto<br>
  												<select name="cCtoClapr" style="width:280" onChange = "javascript:fnHabilitarCodCompraEficiente();">
  												  <option value="">[SELECCIONE]</option>
  												  <option value="001">001 - UNSPSC (COLOMBIA COMPRA EFICIENTE)</option>
  												  <option value="999">999 - ESTANDAR DE ADOPCION DEL CONTRIBUYENTE</option>
  												</select>
  										  </td>
												<td Class = "name" colspan = "05">
													<a href = "javascript:document.frgrm.cCceId.value  = '';
																								document.frgrm.cCceDes.value = '';
  																							f_Links('cCceId','VALID')" id = "idComEfi">C&oacute;digo UNSPSC</a><br>
  												<input type = "text" Class = "letra" style = "width:100" name = "cCceId" 
  										    	onBlur = "javascript:this.value=this.value.toUpperCase();
  																			         f_Links('cCceId','VALID');
  																			         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
  										    	onFocus="javascript:document.frgrm.cCceId.value  = '';
																								document.frgrm.cCceDes.value = '';
																								this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
												</td>
												<td Class = "name" colspan = "16">Descripci&oacute;n Colombia Compra Eficiente<br>
  												<input type = "text" Class = "letra" style = "width:320" name = "cCceDes" readonly>
												</td>
  									  </tr>
											<tr>
												<td Class = "name" colspan = "08">
													<a href = "javascript:document.frgrm.cUmeId.value  = '';
																								document.frgrm.cUmeDes.value  = '';
  																							f_Links('cUmeId','VALID')" id = "cCodUmed">Unidad Medida</a><br>
  												<input type = "text" Class = "letra" style = "width:160" name = "cUmeId"
  										    	onBlur = "javascript:this.value=this.value.toUpperCase();
  																			         f_Links('cUmeId','VALID');
  																			         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
  										    	onFocus="javascript:document.frgrm.cUmeId.value  = '';
																								document.frgrm.cUmeDes.value  = '';
																								this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
												</td>
												<td Class = "name" colspan = "27">Descripci&oacute;n<br>
  												<input type = "text" Class = "letra" style = "width:540" name = "cUmeDes" readonly>
												</td>
											</tr>
										</table>
									</fieldset>
									<?php if($cAlfa == "ALPOPULX" || $cAlfa == "TEALPOPULP" || $cAlfa == "TEALPOPULX" || $cAlfa == "DEALPOPULX") { ?>
										<script languaje = "javascript">
											document.getElementById('tblCodigoProducto').style.display = "none";
										</script>
									<?php } ?>
									<!-- Fin Datos Adicionales openETL -->

									<!-- Inicio Datos Adicionales Transmision 8.5 -->
									<?php if($cAlfa == "COLVANXX" || $cAlfa == "TECOLVANXX" || $cAlfa == "DECOLVANXX") { ?>
									<fieldset>
										<legend><b>Infomaci&oacute;n Adicional Transmisi&oacute;n 8.5</b></legend>
										<table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
											<?php $nCol = f_Format_Cols(35);
  							 			echo $nCol;?>
											<tr>
												<td Class = "name" colspan = "100">Cuenta Iva Transmisi&oacute;n 8.5<br>
  												<input type = "text" Class = "letra" style = "width:200" name = "cCtoPuc85">
												</td>
											</tr>
										</table>
									</fieldset>
									<?php } else { ?>
                    <input type = "hidden" name = "cCtoPuc85" value = "">
									<?php } ?>
									<!-- Fin Datos Adicionales Transmision 8.5 -->

  					  	  <fieldset style="width:720px">
					  	      <legend>Datos del Registro</legend>
  					  	    <table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
    							 		<?php $nCol = f_Format_Cols(35);
    							 		echo $nCol;?>
    					  	    <tr>
    								 		<td Class = "clase08" colspan = "7">Fecha Creado<br>
    								  		<input type = "text" Class = "letra"  style = "width:140;text-align:center"  name = "dFecCre"  value = "<?php echo date('Y-m-d') ?>" readonly>
    								   	</td>
    								   	<td Class = "clase08" colspan = "7">Hora Creado<br>
    									 		<input type = 'text' Class = 'letra' style = "width:140;text-align:center" name = "cHorCre"  value = "<?php echo date('H:i:s') ?>" readonly>
    										</td>
    										<td Class = "clase08" colspan = "7">Fecha Modificado<br>
    								  		<input type = "text" Class = "letra"  style = "width:140;text-align:center"  name = "dFecMod"  value = "<?php echo date('Y-m-d') ?>" readonly>
    								   	</td>
    								   	<td Class = "clase08" colspan = "7">Hora Modificado<br>
    									 		<input type = 'text' Class = 'letra' style = "width:140;text-align:center" name = "cHorMod"  value = "<?php echo date('H:i:s') ?>" readonly>
    										</td>
    								 		<td Class = "clase08" colspan = "7">Estado<br>
    									 		<input type = "text" Class = "letra" style = "width:140;text-align:center" name = "cEstado"  value = "ACTIVO"
    									      onblur = "javascript:this.value=this.value.toUpperCase();f_ValidacEstado();
    								  							             this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
    												onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
    										</td>
    									</tr>
                    </table>
                  </fieldset>
								</center>
			 	      </form>
			 	      </center>
						</fieldset>
					</td>
				</tr>
		 	</table>
		</center>
		<center>
			<table border="0" cellpadding="0" cellspacing="0" width="720">
				<tr height="21">
					<?php switch ($_COOKIE['kModo']) {
						case "VER": ?>
							<td width="629" height="21"></td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
						<?php break;
						default: ?>
							<td width="538" height="21"></td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:f_EnabledCombos();f_Valida_Check();f_DisabledCombos()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
						<?php break;
			  	} ?>
				</tr>
			</table>
		</center>
		<br>
		<!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
		<?php
		switch ($_COOKIE['kModo']) {
			case "NUEVO":
				?>
				<script languaje = "javascript">

				  document.getElementById('ad_egr').style.display="none";
				  document.getElementById('ad_TipP').style.display="none";
				  document.getElementById('ad_RecCaja').style.display="none";
				  document.getElementById('ad_CajaMenor').style.display="none";
				  document.getElementById('ad_Facturacion').style.display="none";
				  document.getElementById('ad_cartasban').style.display="none";
				  document.getElementById('ad_TipN').style.display="none";
				  document.getElementById('ad_TipC').style.display="none";
				  document.getElementById('ad_TipD').style.display="none";
				  f_Activar_Sucursal_Ica('NO');
				  document.forms['frgrm']['cEstado'].readOnly  = true;

					//Inhabilitar Campo codigo Colombia Compra Eficiente
					document.forms['frgrm']['cCceId'].disabled  = true;
					document.forms['frgrm']['cCceDes'].disabled = true;
					document.getElementById('idComEfi').href	  = "javascript:alert('Opcion No Permitida')";
					<?php 
					$vBDIntegracionColmasSap = explode("~",$vSysStr['system_integracion_colmas_sap']);
					if (in_array($cAlfa, $vBDIntegracionColmasSap) == true) {?>
						fnAddNewRowLineaNegocio('Grid_LineaNegocio');
					<?php } ?>
				</script>
				<?php
			break;
			case "EDITAR":
				?>
				<script languaje = "javascript">
				  document.getElementById('ad_egr').style.display="none";
				  document.getElementById('ad_TipP').style.display="none";
				  document.getElementById('ad_TipN').style.display="none";
				  document.getElementById('ad_TipC').style.display="none";
				  document.getElementById('ad_TipD').style.display="none";
				  document.getElementById('ad_RecCaja').style.display="none";
				  document.getElementById('ad_CajaMenor').style.display="none";
				  document.getElementById('ad_Facturacion').style.display="none";
				  document.getElementById('ad_cartasban').style.display="none";
				  document.forms['frgrm']['cEstado'].readOnly  = true;
				</script>
        <?php
        f_CargaData($cCtoId,$cPucId);
      break;
			case "VER":
				?>
				<script languaje = "javascript">
				  for (x=0;x<document.forms['frgrm'].elements.length;x++) {
            document.forms['frgrm'].elements[x].readOnly = true;
            document.forms['frgrm'].elements[x].onfocus  = "";
            document.forms['frgrm'].elements[x].onblur   = "";
            document.forms['frgrm'].elements[x].disabled = true;
          }
				  document.getElementById('ad_egr').style.display="none";
				  document.getElementById('ad_TipP').style.display="none";
				  document.getElementById('ad_TipN').style.display="none";
				  document.getElementById('ad_TipC').style.display="none";
				  document.getElementById('ad_TipD').style.display="none";
				  document.getElementById('ad_RecCaja').style.display="none";
				  document.getElementById('ad_CajaMenor').style.display="none";
				  document.getElementById('ad_Facturacion').style.display="none";
				  document.getElementById('ad_cartasban').style.display="none";

				  //Link Categoria Conceptos
				  if("<?php echo $vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] ?>" == 'SI'){
				  	document.getElementById('IdCac').disabled=true;
						document.getElementById('IdCac').href="#";
					}
				</script>
			  <?php
			  f_CargaData($cCtoId,$cPucId);
			break;
		} ?>

		<?php
		function f_CargaData($xCtoId,$xPucId) {
		  global $xConexion01;
		  global $cAlfa;
			global $vSysStr;

			$qSqlCab  = "SELECT * ";
			$qSqlCab .= "FROM $cAlfa.fpar0119 ";
			$qSqlCab .= "WHERE ";
			$qSqlCab .= "pucidxxx = \"$xPucId\" AND ctoidxxx = \"$xCtoId\" AND ";
			$qSqlCab .= "regestxx = \"ACTIVO\" LIMIT 0,1";
			$xSqlCab  = f_MySql("SELECT","",$qSqlCab,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qSqlCab."~".mysql_num_rows($xSqlCab));

			while ($zRCab = mysql_fetch_array($xSqlCab)) {
				$qSqlCta  = "SELECT * ";
				$qSqlCta .= "FROM $cAlfa.fpar0115 ";
				$qSqlCta .= "WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"$xPucId\" AND ";
				$qSqlCta .= "regestxx = \"ACTIVO\"";
				$xSqlCta  = f_MySql("SELECT","",$qSqlCta,$xConexion01,"");
			  $zRCta = mysql_fetch_array($xSqlCta);

			  $zComPro=explode("|",$zRCab['ctocomxx']);
			  /* comprobantes contables */
			  $e=0;
        $qSqlCom  = "SELECT * ";
        $qSqlCom .= "FROM $cAlfa.fpar0117 ";
        $qSqlCom .= "WHERE ";
        $qSqlCom .= "comtipxx NOT IN (\"AUTOFACTURA\",\"AUTOFACTURA_NC\") AND ";
        $qSqlCom .= "regestxx = \"ACTIVO\" ";
        $qSqlCom .= "ORDER BY comidxxx,comcodxx ";
        $xSqlCom  = f_MySql("SELECT","",$qSqlCom,$xConexion01,"");
				
				//Buscando descripcio del codigo de colombia compra eficiente
				$vComEfi = array();
				if ($zRCab['cceidxxx'] != "") {
					$qComEfi  = "SELECT ";
					$qComEfi .= "cceidxxx, ";
					$qComEfi .= "ccedesxx ";
					$qComEfi .= "FROM $cAlfa.fpar0156 ";
					$qComEfi .= "WHERE ";
					$qComEfi .= "cceidxxx = \"{$zRCab['cceidxxx']}\"";
					$xComEfi  = f_MySql("SELECT","",$qComEfi,$xConexion01,"");
					// f_Mensaje(__FILE__,__LINE__,$qComEfi."~".mysql_num_rows($xComEfi));
					$vComEfi = mysql_fetch_array($xComEfi);
				}
				
				//Buscando descripcion unidad de medida
				$vUniMed = array();
				if ($zRCab['umeidxxx'] != "") {
					$qUniMed  = "SELECT ";
					$qUniMed .= "umeidxxx, ";
					$qUniMed .= "umedesxx ";
					$qUniMed .= "FROM $cAlfa.fpar0157 ";
					$qUniMed .= "WHERE ";
					$qUniMed .= "umeidxxx = \"{$zRCab['umeidxxx']}\"";
					$xUniMed  = f_MySql("SELECT","",$qUniMed,$xConexion01,"");
					// f_Mensaje(__FILE__,__LINE__,$qUniMed."~".mysql_num_rows($xUniMed));
					$vUniMed = mysql_fetch_array($xUniMed);
				}

				/*** Si la variable Categoriacion Conceptos Factuacion esta encendida se habilita el menu de Categoria Conceptos***/
				if($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI'){
					/*** Categoria Conceptos***/
					$qCatCon  = "SELECT * ";
					$qCatCon .= "FROM $cAlfa.fpar0144 ";
					$qCatCon .= "WHERE ";
					$qCatCon .= "cacidxxx = \"{$zRCab['cacidxxx']}\" LIMIT 0,1 ";
					$xCatCon  = f_MySql("SELECT","",$qCatCon,$xConexion01,"");
					// f_Mensaje(__FILE__,__LINE__,$qCatCon."~".mysql_num_rows($xCatCon));
					$vCatCon = mysql_fetch_array($xCatCon);
				}

  			while ($zRCom = mysql_fetch_array($xSqlCom)) {
  			  for($i=0; $i<count($zComPro); $i++){
  			    if($zComPro[$i]!=""){
  			      $zComCad=explode("~",$zComPro[$i]);
  			      if($zRCom['comidxxx']==$zComCad[0] and $zRCom['comcodxx']==$zComCad[1]){?>
  			        <script>
  			          document.forms['frgrm']['oCheck']['<?php echo $e ?>'].checked=true;
  			          <?php if($zComCad[2]!=""){
  			            if($zComCad[2]=="C"){?>
  			            document.forms['frgrm']['oCheckC']['<?php echo $e ?>'].checked=true;
  			          <?php
  			            }
  			            if($zComCad[2]=="D"){?>
  			            document.forms['frgrm']['oCheckD']['<?php echo $e ?>'].checked=true;
  			          <?php
  			            }
  			          }else{?>
  			             document.forms['frgrm']['oCheckD']['<?php echo $e ?>'].checked=true;
  			             document.forms['frgrm']['oCheckC']['<?php echo $e ?>'].checked=true;
                  <?php }?>
  			        </script>
  			      <?php }
  			    }
  			  }
  			  $e++;
  			}
  			/* comprobantes contables */
  			for($i=0; $i<count($zComPro); $i++){
			    if($zComPro[$i]!=""){
			      $zComCad=explode("~",$zComPro[$i]);

			      if($zComCad[0]=="G"){?>
			      	<script>
			      		document.getElementById('ad_egr').style.display="block";
			      	</script>
			      <?php }
			      if($zComCad[0]=="P"){?>
			      	<script>
			      		document.getElementById('ad_TipP').style.display="block";
			      	</script>
			      <?php }
			    	if($zComCad[0]=="N"){?>
			      	<script>
			      		document.getElementById('ad_TipN').style.display="block";
			      	</script>
			      <?php }
			    	if($zComCad[0]=="C"){?>
			      	<script>
			      		document.getElementById('ad_TipC').style.display="block";
			      	</script>
			      <?php }
			    	if($zComCad[0]=="D"){?>
			      	<script>
			      		document.getElementById('ad_TipD').style.display="block";
			      	</script>
			      <?php }
			      if($zComCad[0]=="R"){?>
			      	<script>
			      		document.getElementById('ad_RecCaja').style.display="block";
			      	</script>
			      <?php }
			      if($zComCad[0]=="M"){?>
			      	<script>
			      		document.getElementById('ad_CajaMenor').style.display="block";
			      	</script>
			      <?php }
			      if($zComCad[0]=="F"){?>
			      	<script>
			      		document.getElementById('ad_Facturacion').style.display="block";
			      	</script>
			      <?php }
			      if($zComCad[0]=="L"){?>
			      	<script>
			      		document.getElementById('ad_cartasban').style.display="block";
			      	</script>
			      <?php }
			    }
  			}

  			/* Para que Tipo de Terceros Aplica [G's] */
  			$zCtoApl=explode("~",$zRCab['ctoaplxg']);
  			/* Para que Tipo de Terceros Aplica [G's] */

  			/* Sucursal ICA para las P y las cuentas que empiezan por lo parametrizado en la variable del sistema */
				$cTexto = "";
				$cAccion = "NO";
				$cPucId = $zRCta['pucgruxx'].$zRCta['pucctaxx'].$zRCta['pucsctax'].$zRCta['pucauxxx'].$zRCta['pucsauxx'];

				$vCtaRtIca = explode(",",$vSysStr['financiero_cuentas_reteica']);
				if (in_array(substr($cPucId,0,4), $vCtaRtIca)) {
				  $cAccion = "SI";
        }
			  switch ($cAccion){
			    case "SI":
			      $cTexto .= "<table border = \"0\" cellpadding = \"0\" cellspacing = \"0\" width=\"700\">";
			        $cTexto .= "<tr>";
			          $cTexto .= "<td Class = \"name\">Descripcion<br>";
			            $cTexto .= "<input type = \"text\" Class = \"letra\" style = \"width:500\" name = \"cCtoDesp\" ";
			              $cTexto .= "onBlur = \"javascript:this.value=this.value.toUpperCase()";
			              $cTexto .= "this.style.background=\'".$vSysStr['system_imput_onblur_color']."\'\" ";
			              $cTexto .= "onFocus=\"javascript:this.style.background=\'".$vSysStr['system_imput_onfocus_color']."\'\">";
			          $cTexto .= "</td>";
			          $cTexto .= "<td Class = \"name\">";
			            $cTexto .= "<a href = \"javascript:document.frgrm.cCtoSucri.value = \'\';f_Links(\'cCtoSucri\',\'VALID\')\" id=\"IdCtoSucri\">Sucursal Retenci&oacute;n ICA</a><br>";
			            $cTexto .= "<input type=\"text\" Class = \"letra\" name =\"cCtoSucri\" style = \"width:200\" ";
			            $cTexto .= "onBlur = \"javascript:this.value=this.value.toUpperCase(); ";
			              $cTexto .= "f_Links(\'cCtoSucri\',\'VALID\'); ";
			              $cTexto .= "this.style.background=\'".$vSysStr['system_imput_onblur_color']."\'\" ";
			              $cTexto .= "onFocus=\"javascript:this.style.background=\'".$vSysStr['system_imput_onfocus_color']."\'\">";
			             $cTexto .= "<input type=\"hidden\" name =\"cCtoSucriAnt\">";
			          $cTexto .= "</td>";
			        $cTexto .= "</tr>";
			      $cTexto .= "</table>";
			    break;
			    default:
			      $cTexto .= "<table border = \"0\" cellpadding = \"0\" cellspacing = \"0\" width=\"700\">";
			        $cTexto .= "<tr>";
			          $cTexto .= "<td Class = \"name\">Descripcion<br>";
			            $cTexto .= "<input type = \"text\" Class = \"letra\" style = \"width:700\" name = \"cCtoDesp\" ";
			            $cTexto .= "onBlur = \"javascript:this.value=this.value.toUpperCase()";
			            $cTexto .= "this.style.background=\'".$vSysStr['system_imput_onblur_color']."\'\" ";
			            $cTexto .= "onFocus=\"javascript:this.style.background=\'".$vSysStr['system_imput_onfocus_color']."\'\">";
			          $cTexto .= "</td>";
			        $cTexto .= "</tr>";
			      $cTexto .= "</table>";
			    break;
			  }
  			/* Fin Sucursal ICA para las P y las cuentas que empiezan por lo parametrizado en la variable del sistema */
  			?>
				<script language = "javascript">
				  document.forms['frgrm']['cComMemo'].value  = "<?php echo $zRCab['ctocomxx'] ?>";
				 	document.forms['frgrm']['cPucId'].value    = "<?php echo $cPucId ?>";
				 	document.forms['frgrm']['cPucRet'].value   = "<?php echo $zRCta['pucretxx'] ?>";
				 	document.forms['frgrm']['cPucDes'].value   = "<?php echo $zRCta['pucdesxx'] ?>";
				 	document.forms['frgrm']['cCtoId'].value    = "<?php echo $zRCab['ctoidxxx'] ?>";
				 	document.forms['frgrm']['cCtoNit'].value   = "<?php echo $zRCab['ctonitxx'] ?>";
				 	document.forms['frgrm']['cCtoVlr01'].value = "<?php echo $zRCab['ctovlr01'] ?>";
				 	document.forms['frgrm']['cCtoVlr02'].value = "<?php echo $zRCab['ctovlr02'] ?>";
				 	document.forms['frgrm']['cCtoAnt'].value   = "<?php echo $zRCab['ctoantxx'] ?>";
				 	document.forms['frgrm']['cCtoPcc'].value   = "<?php echo $zRCab['ctopccxx'] ?>";

				 	document.forms['frgrm']['cPucId'].readOnly  = true;
				 	document.forms['frgrm']['cPucRet'].readOnly = true;
				 	document.forms['frgrm']['cPucDes'].readOnly = true;
				 	document.forms['frgrm']['cCtoId'].readOnly  = true;

				 	document.forms['frgrm']['cPucId'].onfocus   = "";
				 	document.forms['frgrm']['cPucRet'].onfocus  = "";
				 	document.forms['frgrm']['cPucDes'].onfocus  = "";
				 	document.forms['frgrm']['cCtoId'].onfocus   = "";

				 	document.forms['frgrm']['cPucId'].onblur    = "";
				 	document.forms['frgrm']['cPucRet'].onblur   = "";
				 	document.forms['frgrm']['cPucDes'].onblur   = "";
				 	document.forms['frgrm']['cCtoId'].onblur    = "";

				 	document.getElementById('IdCta').disabled=true;
				 	document.getElementById('IdCta').href="#";

				 	document.getElementById('tblCtoSucri').innerHTML = '<?php echo $cTexto ?>';
				 	if('<?php echo $cAccion?>' == 'SI') {
				 		  document.forms['frgrm']['cCtoSucri'].value    = "<?php echo $zRCab['ctosucri'] ?>";
				 		  document.forms['frgrm']['cCtoSucriAnt'].value = "<?php echo $zRCab['ctosucri'] ?>";
				 	}

				 	document.forms['frgrm']['cCtoDesp'].value    = "<?php echo $zRCab['ctodesxp'] ?>";
				 	document.forms['frgrm']['cCtoNit1p'].value   = "<?php echo $zRCab['ctonit1p'] ?>";
			 		document.forms['frgrm']['cCtoNit2p'].value   = "<?php echo $zRCab['ctonit2p'] ?>";
				 	document.forms['frgrm']['cCtoDesn'].value    = "<?php echo $zRCab['ctodesxn'] ?>";
				 	document.forms['frgrm']['cCtoNit1n'].value   = "<?php echo $zRCab['ctonit1n'] ?>";
			 		document.forms['frgrm']['cCtoNit2n'].value   = "<?php echo $zRCab['ctonit2n'] ?>";
				 	document.forms['frgrm']['cCtoDesc'].value    = "<?php echo $zRCab['ctodesxc'] ?>";
				 	document.forms['frgrm']['cCtoDesd'].value    = "<?php echo $zRCab['ctodesxd'] ?>";
				 	document.forms['frgrm']['cCtoNit1d'].value   = "<?php echo $zRCab['ctonit1d'] ?>";
			 		document.forms['frgrm']['cCtoNit2d'].value   = "<?php echo $zRCab['ctonit2d'] ?>";

				 	document.forms['frgrm']['cCtoDesg'].value     = "<?php echo $zRCab['ctodesxg'] ?>";
				 	document.forms['frgrm']['cADoCruEgr'].value   = "<?php echo $zRCab['ctodocxg'] ?>";
				 	document.forms['frgrm']['cAConCruEgr'].value  = "<?php echo $zRCab['ctoctocg'] ?>";
				 	document.forms['frgrm']['cDocInfG'].value     = "<?php echo $zRCab['ctodocig'] ?>";
				 	document.forms['frgrm']['cCtoPta'].value      = "<?php echo $zRCab['ctoptaxg'] ?>";
				 	document.forms['frgrm']['cCtoPta'].value      = "<?php echo $zRCab['ctoptaxg'] ?>";
				 	document.forms['frgrm']['cCtoPvxxg'].value    = "<?php echo $zRCab['ctopvxxg'] ?>";
				 	document.forms['frgrm']['cCtoNit1g'].value    = "<?php echo $zRCab['ctonit1g'] ?>";
			 		document.forms['frgrm']['cCtoNit2g'].value    = "<?php echo $zRCab['ctonit2g'] ?>";

			 		//Cartas Bancarias
				  document.forms['frgrm']['cCtoDesL'].value  = "<?php echo $zRCab['ctodesxl'] ?>";
				 	document.forms['frgrm']['cADoCruL'].value  = "<?php echo $zRCab['ctodocxl'] ?>";
				 	document.forms['frgrm']['cADoCruL'].value  = "<?php echo $zRCab['ctodocxl'] ?>";
				 	document.forms['frgrm']['cAConCruL'].value = "<?php echo $zRCab['ctoctocl'] ?>";
				 	document.forms['frgrm']['cDocInfL'].value  = "<?php echo $zRCab['ctodocil'] ?>";
				 	document.forms['frgrm']['cApConBan'].value = "<?php echo $zRCab['ctoapcon'] ?>";
				 	document.forms['frgrm']['cCtoPtaL'].value  = "<?php echo $zRCab['ctoptaxl'] ?>";
				 	document.forms['frgrm']['cCtoPvxxL'].value = "<?php echo $zRCab['ctopvxxl'] ?>";
				 	document.forms['frgrm']['cCtoNit1L'].value = "<?php echo $zRCab['ctonit1l'] ?>";
			 		document.forms['frgrm']['cCtoNit2L'].value = "<?php echo $zRCab['ctonit2l'] ?>";

			 		<?php
					//Tipo de Concepto Egresos
			    if ($zRCab['ctoantxg'] == "SI") { ?>
            for (i=0;i<document.frgrm.rTipoConcepto.length;i++) {
      				if (document.frgrm.rTipoConcepto[i].value == "1") {
      			 	  document.frgrm.rTipoConcepto[i].checked=true;
        			}
    				}
			    <?php } elseif ($zRCab['ctotfxxg'] == "SI") { ?>
			      for (i=0;i<document.frgrm.rTipoConcepto.length;i++) {
      				if (document.frgrm.rTipoConcepto[i].value == "2") {
      			 	  document.frgrm.rTipoConcepto[i].checked=true;
        			}
    				}
			    <?php } elseif ($zRCab['ctodsacg'] == "SI") { ?>
			      for (i=0;i<document.frgrm.rTipoConcepto.length;i++) {
      				if (document.frgrm.rTipoConcepto[i].value == "3") {
      			 	  document.frgrm.rTipoConcepto[i].checked=true;
        			}
    				}
    			<?php } else { ?>
			      for (i=0;i<document.frgrm.rTipoConcepto.length;i++) {
      				if (document.frgrm.rTipoConcepto[i].value == "4") {
      			 	  document.frgrm.rTipoConcepto[i].checked=true;
        			}
    				}
			    <?php }

			    //Tipos de Concepto Cartas Bancarias
			    if ($zRCab['ctodsacl'] == "SI") { ?>
            for (i=0;i<document.frgrm.rTipoConceptoL.length;i++) {
      				if (document.frgrm.rTipoConceptoL[i].value == "2") {
      			 	  document.frgrm.rTipoConceptoL[i].checked=true;
        			}
            } <?php
	      	} else { ?>
			      for (i=0;i<document.frgrm.rTipoConceptoL.length;i++) {
      				if (document.frgrm.rTipoConceptoL[i].value == "1") {
      			 	  document.frgrm.rTipoConceptoL[i].checked=true;
        			}
    				} <?php
					} ?>

			    document.forms['frgrm']['cCtoDesr'].value         = "<?php echo $zRCab['ctodesxr'] ?>";
			    document.forms['frgrm']['cCtoAntxr'].value        = "<?php echo $zRCab['ctoantxr'] ?>";
			    document.forms['frgrm']['cCtoNit1r'].value        = "<?php echo $zRCab['ctonit1r'] ?>";
		 			document.forms['frgrm']['cCtoNit2r'].value        = "<?php echo $zRCab['ctonit2r'] ?>";

			    document.forms['frgrm']['cCtoDesm'].value         = "<?php echo $zRCab['ctodesxm'] ?>";
			    document.forms['frgrm']['cCtoTpaxm'].value        = "<?php echo $zRCab['ctotpaxm'] ?>";
			    document.forms['frgrm']['cCtoNit1m'].value        = "<?php echo $zRCab['ctonit1m'] ?>";
		 			document.forms['frgrm']['cCtoNit2m'].value        = "<?php echo $zRCab['ctonit2m'] ?>";

		 			document.forms['frgrm']['cCtoNit1c'].value        = "<?php echo $zRCab['ctonit1c'] ?>";
		 			document.forms['frgrm']['cCtoNit2c'].value        = "<?php echo $zRCab['ctonit2c'] ?>";

			    document.forms['frgrm']['cCtoDesf'].value         = "<?php echo $zRCab['ctodesxf'] ?>";
			    document.forms['frgrm']['cCtoClaxf'].value        = "<?php echo $zRCab['ctoclaxf'] ?>";
			    document.forms['frgrm']['cCtoNit1f'].value        = "<?php echo $zRCab['ctonit1f'] ?>";
		 			document.forms['frgrm']['cCtoNit2f'].value        = "<?php echo $zRCab['ctonit2f'] ?>";

				 	document.forms['frgrm']['cCtoE2k'].value          = "<?php echo $zRCab['ctoe2kxx'] ?>";
					document.forms['frgrm']['cCtoSapId'].value   		  = "<?php echo $zRCab['ctosapid'] ?>";
				 	document.forms['frgrm']['cCtoChAld'].value        = "<?php echo $zRCab['ctochald'] ?>";

          // Campo Integración DSV - Charge Codes Cargowise
          <?php
				 	if ($cAlfa == "DSVSASXX" || $cAlfa == "TEDSVSASXX" || $cAlfa == "DEDSVSASXX"){?>
					 	document.forms['frgrm']['cCtocWccX'].value      = "<?php echo $zRCab['ctocwccx'] ?>"; //Charge Codes Cargowis
		      <?php } ?>
          
          <?php
				 	if ($cAlfa == "ADUANERA" || $cAlfa == "DEADUANERA" || $cAlfa == "TEADUANERA" || $cAlfa == "DEDESARROL" ||  $cAlfa == "DEADUANERP"){?>
					 	document.forms['frgrm']['cPucBel'].value          = "<?php echo $zRCab['pucadbel'] ?>"; //Codigo Integracion Belcorp
				  	document.forms['frgrm']['cNuAsBel'].value         = "<?php echo $zRCab['pucadnas'] ?>"; //Numero Asignacion Belcorp
						if(document.forms['frgrm']['cNuAsBel'].value == "0"){
				 			document.forms['frgrm']['cNuAsBel'].value = "";
						}
					<?php } ?>

          <?php
					$vBDIntegracionColmasSap = explode("~",$vSysStr['system_integracion_colmas_sap']);
					if (in_array($cAlfa, $vBDIntegracionColmasSap) == true) { ?>
					 	document.forms['frgrm']['cCtoSapC'].value  = "<?php echo $zRCab['ctosapcx'] ?>"; //Cuenta Costo
				  	document.forms['frgrm']['cCtoSapI'].value  = "<?php echo $zRCab['ctosapix'] ?>"; //Cuenta Ingreso
				  	document.forms['frgrm']['cCtoSapIc'].value = "<?php echo $zRCab['ctosapic'] ?>"; //Cuenta del Impuesto de Compra
				  	document.forms['frgrm']['cCtoSapIv'].value = "<?php echo $zRCab['ctosapiv'] ?>"; //Cuenta del Impuesto de Venta
						document.forms['frgrm']['cCtoSapCA'].value = "<?php echo $zRCab['ctosapca'] ?>"; //Codigo del area
				  	document.forms['frgrm']['cCtoSapLI'].value = "<?php echo $zRCab['ctosapli'] ?>"; //Codigo de la linea
				  	document.forms['frgrm']['cCtoSapLE'].value = "<?php echo $zRCab['ctosaple'] ?>"; //Codigo de la linea

					<?php
						$mLineasNegocio = f_explode_array($zRCab['ctolineg'],"|","~");
						$nCanCueCs = 0;
						for ($i=0;$i<count($mLineasNegocio);$i++) {
              if ($mLineasNegocio[$i][0] != "") { 
								$nCanCueCs++;
								$qDesLinNeg  = "SELECT lnedesxx ";
								$qDesLinNeg .= "FROM $cAlfa.zcol0003 ";
								$qDesLinNeg .= "WHERE lnecodxx = \"{$mLineasNegocio[$i][0]}\" AND ";
								$qDesLinNeg .= "regestxx = \"ACTIVO\";";
								$xDesLinNeg = f_MySql("SELECT","",$qDesLinNeg,$xConexion01,"");
								if (mysql_num_rows($xDesLinNeg) > 0) {
									$vDesLinNeg = mysql_fetch_array($xDesLinNeg);
									$vDesLinNeg = $vDesLinNeg['lnedesxx'];
								} 
							?>
								fnAddNewRowLineaNegocio('Grid_LineaNegocio');
								document.forms['frgrm']['cCodLineaNeg' + document.forms['frgrm']['nSecuencia_Grid_LineaNegocio'].value].value = "<?php echo $mLineasNegocio[$i][0] ?>"
								document.forms['frgrm']['cDesLineaNeg' + document.forms['frgrm']['nSecuencia_Grid_LineaNegocio'].value].value = "<?php echo $vDesLinNeg ?>";
								document.forms['frgrm']['cCtaIngreso'  + document.forms['frgrm']['nSecuencia_Grid_LineaNegocio'].value].value = "<?php echo $mLineasNegocio[$i][1] ?>";
								document.forms['frgrm']['cCtaCosto'    + document.forms['frgrm']['nSecuencia_Grid_LineaNegocio'].value].value = "<?php echo $mLineasNegocio[$i][2] ?>";
								if ("<?php echo $_COOKIE['kModo'] ?>" == "VER") {
									document.forms['frgrm']['oBtnDelLinea' + document.forms['frgrm']['nSecuencia_Grid_LineaNegocio'].value].disabled = true;
								}
								<?php
							}
						}
						if ($nCanCueCs == 0) { ?>
							fnAddNewRowLineaNegocio('Grid_LineaNegocio');
						<?php }
						} ?>

					//// Campos Nuevos ////
					document.forms['frgrm']['cCtoClapr'].value		= "<?php echo $zRCab['ctoclapr'] ?>";
					<?php
					if ($zRCab['ctoclapr'] == "001"){ ?>
						document.forms['frgrm']['cCceId'].value  	 	= "<?php echo $zRCab['cceidxxx'] ?>";
						document.forms['frgrm']['cCceDes'].value  	= "<?php echo str_replace('"','\"',$vComEfi['ccedesxx']) ?>";
						document.forms['frgrm']['cCceId'].disabled 	= false;
						document.forms['frgrm']['cCceDes'].disabled = false;
						document.getElementById('idComEfi').href		=	"javascript:document.frgrm.cCceId.value  = ''; document.frgrm.cCceDes.value  = ''; f_Links('cCceId','VALID')";
					<?php } else { ?>
						document.forms['frgrm']['cCceId'].value    	= "";
						document.forms['frgrm']['cCceId'].disabled 	= true;
						document.forms['frgrm']['cCceDes'].disabled	= true;
						document.getElementById('idComEfi').href		= "javascript:alert('Opcion No Permitida')";
					<?php } 
					if ($_COOKIE['kModo'] == "VER"){ ?>
						//Link Codigo Colombia Compra Eficiente
						document.getElementById('idComEfi').href		= "javascript:alert('Opcion No Permitida')";
						//Link Unidad de Medida
						document.getElementById('cCodUmed').href		= "javascript:alert('Opcion No Permitida')";
					<?php } ?>
					document.forms['frgrm']['cUmeId'].value     	= "<?php echo $zRCab['umeidxxx'] ?>";
					document.forms['frgrm']['cUmeDes'].value     	= "<?php echo str_replace('"','\"',$vUniMed['umedesxx']) ?>";
					document.forms['frgrm']['cCtoPuc85'].value    = "<?php echo $zRCab['ctopuc85'] ?>";
					
					document.forms['frgrm']['dFecCre'].value      = "<?php echo $zRCab['regfcrex'] ?>";
					document.forms['frgrm']['dFecCre'].value      = "<?php echo $zRCab['regfcrex'] ?>";
					document.forms['frgrm']['cHorCre'].value      = "<?php echo $zRCab['reghcrex'] ?>";
					document.forms['frgrm']['dFecMod'].value      = "<?php echo $zRCab['regfmodx'] ?>";
					document.forms['frgrm']['cHorMod'].value      = "<?php echo $zRCab['reghmodx'] ?>";
					document.forms['frgrm']['cEstado'].value      = "<?php echo $zRCab['regestxx'] ?>";

					f_Activar_Check();

					//Categoria Conceptos
					/*** Si la variable Categoriacion Conceptos Factuacion esta encendida se habilita el menu de Categoria Conceptos***/
					if('<?php echo $vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI' ?>'){

							document.forms['frgrm']['cCacId'].value           = "<?php echo $vCatCon['cacidxxx'] ?>";
							document.forms['frgrm']['cCacDes'].value          = "<?php echo $vCatCon['cacdesxx'] ?>";
					}

					document.forms['frgrm']['dFecCre'].value          = "<?php echo $zRCab['regfcrex'] ?>";
					document.forms['frgrm']['cHorCre'].value          = "<?php echo $zRCab['reghcrex'] ?>";
					document.forms['frgrm']['dFecMod'].value          = "<?php echo $zRCab['regfmodx'] ?>";
					document.forms['frgrm']['cHorMod'].value          = "<?php echo $zRCab['reghmodx'] ?>";
					document.forms['frgrm']['cEstado'].value          = "<?php echo $zRCab['regestxx'] ?>";
				</script>
		<?php }
		} ?>
	</body>
</html>

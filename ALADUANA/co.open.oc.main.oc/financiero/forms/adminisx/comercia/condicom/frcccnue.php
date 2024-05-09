<?php
/**
	 * Proceso Sucursales.
	 * --- Descripcion: Permite Crear una Sucursal.
	 * @author
	 * @package emisioncero
	 * @version 001
	 */
	include("../../../../libs/php/utility.php");
	/**
	 *  Cookie fija
	 */
	$kDf = explode("~",$_COOKIE["kDatosFijos"]);
	$kMysqlHost = $kDf[0];
	$kMysqlUser = $kDf[1];
	$kMysqlPass = $kDf[2];
	$kMysqlDb   = $kDf[3];
	$kUser      = $kDf[4];
	$kLicencia  = $kDf[5];
	$swidth     = $kDf[6];
	$kModo = $_COOKIE['kModo'];
?>
<html>
	<head>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
		<script language="javascript">

			//Elimina Grupo de Gestion
			function uDelGru(valor)	{
				if (confirm('ELIMINAR EL GRUPO '+valor+' DE ESTE CLIENTE ?'))	{
	     		var ruta = "frielgge.php?cCliId=<?php echo $cCliId ?>&tipsave=4&cIntId="+valor+"&gGruGesId="+document.forms['frgrm']['cGruGesId'].value;
	        parent.fmpro.location = ruta;
	    	}
			}
		  function uDelCom(valor)	{
				if (confirm('ELIMINAR EL INTERMEDIARIO '+valor+' DE ESTE CLIENTE ?'))	{
		     	var ruta = "frcccsav.php?cCliId=<?php echo $cCliId ?>&tipsave=4&cIntId="+valor+"&cFacA="+document.forms['frgrm']['cFacA'].value;
	        parent.fmpro.location = ruta;
		    }
			}

			function uDelConCon(valor)	{
				if (confirm('ELIMINAR EL CONCEPTO CONTABLE '+valor+' DE ESTE CLIENTE ?'))	{
		     	var ruta = "frcccsav.php?cCliId=<?php echo $cCliId ?>&tipsave=6&cIntId="+valor+"&cExcPt="+document.forms['frgrm']['cExcPt'].value;
	        parent.fmpro.location = ruta;
		    }
			}

			function uDelDescuento(valor)	{
				if (confirm('ELIMINAR EL DESCUENTO '+valor+' DE ESTE CLIENTE ?'))	{
		     	var ruta = "frcccsav.php?cCliId=<?php echo $cCliId ?>&tipsave=9&cIntId="+valor+"&cDescuen="+document.forms['frgrm']['cDescuen'].value;
	        parent.fmpro.location = ruta;
		    }
			}

  		function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
  				document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
  				parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
  	  }

  	  function f_Valida_cCccIfa(xValor) {
  			if (xValor == "SI") {
  				document.forms['frgrm']['cCccIfv'].readOnly   = false;
  		   } else {
  		  	document.forms['frgrm']['cCccIfv'].readOnly   = true;
  				document.forms['frgrm']['cCccIfv'].value      ='';
					document.forms['frgrm']['cCccRfIf'].value     = "NO";
					document.forms['frgrm']['cCccArfIf'].value    = "NO";
					document.forms['frgrm']['cCccRiIf'].value     = "NO";
				  document.forms['frgrm']['cCccAriIf'].value    = "NO";
  		   }
  		 }

		  function f_EnabledCombos() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
		  }

		  function f_DisabledCombos() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
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

    	function f_Links(xLink,xSwitch,xSecuencia='') {
				var zX    = screen.width;
				var zY    = screen.height;
				switch (xLink){
				  case "cCliId":                           // CASO PARA EL CLIENTE //
  					if (xSwitch == "VALID") {
  						var zRuta  = "frccc150.php?gWhat=VALID&gFunction=cCliId&gCliId="+document.forms['frgrm']['cCliId'].value.toUpperCase();
  						parent.fmpro.location = zRuta;
  					} else {
  		  			var zNx     = (zX-600)/2;
  						var zNy     = (zY-250)/2;
  						var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
  						var zRuta   = "frccc150.php?gWhat=WINDOW&gFunction=cCliId&gCliId="+document.forms['frgrm']['cCliId'].value.toUpperCase();
  						zWindow = window.open(zRuta,"zWindow",zWinPro);
  				  	zWindow.focus();
  					}
  			  break;
  				case "cCliNom":
  					if (xSwitch == "VALID") {
  						var zRuta  = "frccc15n.php?gWhat=VALID&gFunction=cCliNom&gCliNom="+document.forms['frgrm']['cCliNom'].value.toUpperCase();
  						parent.fmpro.location = zRuta;
  					} else {
  		  			var zNx     = (zX-600)/2;
  						var zNy     = (zY-250)/2;
  						var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
  						var zRuta   = "frccc15n.php?gWhat=WINDOW&gFunction=cCliNom&gCliNom="+document.forms['frgrm']['cCliNom'].value.toUpperCase();
  						zWindow = window.open(zRuta,"zWindow",zWinPro);
  				  	zWindow.focus();
  					}
  			  break;
  				case "cGtaId":
  					if (xSwitch == "VALID") {
  						var zRuta  = "frccc111.php?gWhat=VALID&gFunction=cGtaId&gGtaId="+document.forms['frgrm']['cGtaId'].value.toUpperCase();
  						parent.fmpro.location = zRuta;
  					} else {
  		  			var zNx     = (zX-600)/2;
  						var zNy     = (zY-250)/2;
  						var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
  						var zRuta   = "frccc111.php?gWhat=WINDOW&gFunction=cGtaId&gGtaId="+document.forms['frgrm']['cGtaId'].value.toUpperCase();
  						zWindow = window.open(zRuta,"zWindow",zWinPro);
  				  	zWindow.focus();
  					}
  			  break;
  			  case "cCccIntId":
  			  case "cCccIntNom":
  					if (xSwitch == "WINDOW") {
  					  var zNx     = (zX-600)/2;
  						var zNy     = (zY-250)/2;
  						var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
  						var zRuta   = "frccc351.php?gWhat=WINDOW&gFunction=cCccIntId"+
  																										"&gCccIntNom="+document.forms['frgrm']['cCccIntId'].value.toUpperCase()+
  																										"&gCliId="+document.forms['frgrm']['cCliId'].value.toUpperCase();
  						zWindow = window.open(zRuta,"zWindow",zWinPro);
  				  	zWindow.focus();
  					}
  			  break;
  			  case "cIntermediario":
						var zCliId  =  document.forms['frgrm']['cCliId'].value.toUpperCase();
						var zNx     = (zX-520)/2;
						var zNy     = (zY-500)/2;
						var zWinPro = 'width=520,scrollbars=1,height=500,left='+zNx+',top='+zNy;
						var zRuta   = 'frcccint.php?cCliId='+zCliId+'&gFacA='+document.forms['frgrm']['cFacA'].value;
						zWindow2    = window.open(zRuta,'zWindow2',zWinPro);
				    zWindow2.focus();
					break;
					case "cExclusionPagTer":
			    	/*** Si la variable Categoriacion Conceptos Factuacion esta encendida se habilita el menu de Categoria Conceptos***/
						if("<?php echo $vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] ?>" == "SI"){
							var zNx      = (zX-750)/2;
						  var zNy      = (zY-400)/2;
						  var zWinPro  = "width=750,scrollbars=1,height=400,left="+zNx+",top="+zNy;
					  }else{
							var zNx      = (zX-520)/2;
						  var zNy      = (zY-500)/2;
						  var zWinPro  = "width=600,scrollbars=1,height=250,left="+zNx+",top="+zNy;
					  }
					  var zCliId  =  document.forms['frgrm']['cCliId'].value.toUpperCase();
            var zRuta   = 'frccofrm.php?cCliId='+zCliId+'&gExcPt='+document.forms['frgrm']['cExcPt'].value;
            zWindow2 = window.open(zRuta,"zWindow2",zWinPro);
            zWindow2.focus();
					break;
  			  case "cGruGesId":
						var zCliId  =  document.forms['frgrm']['cCliId'].value.toUpperCase();
						var zNx     = (zX-520)/2;
						var zNy     = (zY-500)/2;
						var zWinPro = 'width=520,scrollbars=1,height=500,left='+zNx+',top='+zNy;
						var zRuta   = 'frauggen.php?cCliId='+zCliId+'&gGruGesId='+document.forms['frgrm']['cGruGesId'].value;
						zWindow2    = window.open(zRuta,'zWindow2',zWinPro);
				    zWindow2.focus();
					break;
					case "cMePagId":
						if (xSwitch == "VALID") {
							var cPathUrl  = "frccc155.php?gWhat="+xSwitch+"&gFunction="+xLink+"&gMePagId="+document.forms['frgrm']['cMePagId'].value;
							parent.fmpro.location = cPathUrl;
						} else {
							if (xSwitch == "WINDOW") {
								var zNx     = (zX-600)/2;
								var zNy     = (zY-250)/2;
								var cWinOpt = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
								var cPathUrl   = "frccc155.php?gWhat="+xSwitch+"&gFunction="+xLink+"&gMePagId="+document.forms['frgrm']['cMePagId'].value;
								zWindow = window.open(cPathUrl,"zWindow",cWinOpt);
								zWindow.focus();
							} else {
								if (xSwitch == "EXACT") {
									var cPathUrl  = "frccc155.php?gWhat="+xSwitch+"&gFunction="+xLink+"&gMePagId="+document.forms['frgrm']['cMePagId'].value;
									parent.fmpro.location = cPathUrl;
                }
							}
						}
					break;
					case "cDescuentos":
						var zCliId  =  document.forms['frgrm']['cCliId'].value.toUpperCase();
						var zNx     = (zX-520)/2;
						var zNy     = (zY-500)/2;
						var zWinPro = 'width=520,scrollbars=1,height=500,left='+zNx+',top='+zNy;
						var zRuta   = 'frccc164.php?cCliId='+zCliId+'&gDecuen='+document.forms['frgrm']['cDescuen'].value;
						zWindow2    = window.open(zRuta,'zWindow2',zWinPro);
				    zWindow2.focus();
					break;
					case "cCliId2":	
					case "cCliNom2":
            if (xSwitch == "VALID") {
              var cRuta = "frcccfaa.php?gWhat="+xSwitch+"&gFunction="+xLink+
                                        "&gCliId="+document.forms['frgrm']['cCliId2'].value +
                                        "&gFacA="+document.forms['frgrm']['cFacA'].value;
              parent.fmpro.location = cRuta;
            } else {
              var nNx      = (zX-600)/2;
              var nNy      = (zY-250)/2;
              var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
              var cRuta = "frcccfaa.php?gWhat="+xSwitch+"&gFunction="+xLink+
																				"&gCliId="+document.forms['frgrm']['cCliId2'].value +
                                        "&gFacA="+document.forms['frgrm']['cFacA'].value;
              cWindow = window.open(cRuta,xLink,cWinOpt);
              cWindow.focus();
            }
					break;
          case "cSucId":
            if (xSwitch == "VALID") {
              var cPathUrl = "frccc008.php?gWhat="+xSwitch
                              +"&gFunction="+xLink
                              +"&gSecuencia="+xSecuencia
                              +"&gSucId="+document.forms['frgrm']['cSucId'+xSecuencia].value;
              parent.fmpro.location = cPathUrl;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var cWinOpt = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var cPathUrl = "frccc008.php?gWhat="+xSwitch
                              +"&gFunction="+xLink
                              +"&gSecuencia="+xSecuencia
                              +"&gSucId="+document.forms['frgrm']['cSucId'+xSecuencia].value;
              zWindow = window.open(cPathUrl,"zWindow",cWinOpt);
              zWindow.focus();
            }
          break;
          case "cUsrId":
            if (xSwitch == "VALID") {
              var cPathUrl = "frccc003.php?gWhat="+xSwitch
                              +"&gFunction="+xLink
                              +"&gSecuencia="+xSecuencia
                              +"&gUsrId="+document.forms['frgrm']['cUsrId'+xSecuencia].value;
              parent.fmpro.location = cPathUrl;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var cWinOpt = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var cPathUrl = "frccc003.php?gWhat="+xSwitch
                              +"&gFunction="+xLink
                              +"&gSecuencia="+xSecuencia
                              +"&gUsrId="+document.forms['frgrm']['cUsrId'+xSecuencia].value;
              zWindow = window.open(cPathUrl,"zWindow",cWinOpt);
              zWindow.focus();
            }
					break;
          case "cComDes":
            var nError  = 0;
            var cError  = "";
            if (document.forms['frgrm']['cUsrId'+xSecuencia].value == "") {
              nError = 1;
              cError += "Debe Seleccionar el Usuario Facturador.\n";
            }

            if (nError == 0) {
              if (xSwitch == "VALID") {
                var cPathUrl = "frccccfa.php?gWhat="+xSwitch
                                +"&gFunction="+xLink
                                +"&gSecuencia="+xSecuencia
                                +"&gUsrId="+document.forms['frgrm']['cUsrId'+xSecuencia].value
                                +"&gComDesNom="+document.forms['frgrm']['cComDesNom'+xSecuencia].value;
                parent.fmpro.location = cPathUrl;
              } else {
                var zNx     = (zX-600)/2;
                var zNy     = (zY-250)/2;
                var cWinOpt = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
                var cPathUrl = "frccccfa.php?gWhat="+xSwitch
                                +"&gFunction="+xLink
                                +"&gSecuencia="+xSecuencia
                                +"&gUsrId="+document.forms['frgrm']['cUsrId'+xSecuencia].value
                                +"&gComDesNom="+document.forms['frgrm']['cComDesNom'+xSecuencia].value;
                zWindow = window.open(cPathUrl,"zWindow",cWinOpt);
                zWindow.focus();
              }
            } else {
              alert(cError+"Verifique.");
            }
					break;
        }
			}

    	function f_CargarFacturara() {
  		  var cRuta = "frcccgri.php?gTipo=1&gFacA="+document.forms['frgrm']['cFacA'].value;
        parent.fmpro2.location = cRuta;
      }

      function fnCargarExclusionPagosTerceros() {
  		  var cRuta = "frcccgri.php?gTipo=2&gExcPt="+document.forms['frgrm']['cExcPt'].value;
        parent.fmpro3.location = cRuta;
      }

			function fnCargarDescuentos() {
  		  var cRuta = "frcccgri.php?gTipo=3&gDescuen="+document.forms['frgrm']['cDescuen'].value;
        parent.fmpro5.location = cRuta;
      }

      function f_CargarGruposGestion(){
        var cRuta = "frcargge.php?gTipo=1&gGruGesId="+document.forms['frgrm']['cGruGesId'].value;
	      parent.fmpro.location = cRuta;
     	}

      function fnCargarValoresUnitariosTerceros(){
        var cRuta = "frcccgri.php?gTipo=4&gValUniTer"+document.forms['frgrm']['cValUniTer'].value;
        parent.fmpro4.location = cRuta;
      }

			function fnMostrarCampos() {
				if(document.forms['frgrm']['cAplica'].value == "SI"){
					document.getElementById('idFacAut').style.display  		 = 'block';
				}else{
					document.getElementById('idFacAut').style.display  		 = 'none';
				}
			}

			function fnMarcaHoras() {
				if (document.forms['frgrm']['vCheckAll'].checked == true){
					for (i=0;i<document.forms['frgrm']['cHoras[]'].length;i++){
						document.forms['frgrm']['cHoras[]'][i].checked = true;
					}
				} else {
					for (i=0;i<document.forms['frgrm']['cHoras[]'].length;i++){
						document.forms['frgrm']['cHoras[]'][i].checked = false;
					}
				}
			}

			function fnMarcaDias() {
				if (document.forms['frgrm']['vCheckAll1'].checked == true){
					for (i=0;i<document.forms['frgrm']['cDias[]'].length;i++){
						document.forms['frgrm']['cDias[]'][i].checked = true;
					}
				} else {
					for (i=0;i<document.forms['frgrm']['cDias[]'].length;i++){
						document.forms['frgrm']['cDias[]'][i].checked = false;
					}
				}
			}

			function fnMarcaMes() {
				if (document.forms['frgrm']['vCheckAll2'].checked == true){
					for (i=0;i<document.forms['frgrm']['cMes[]'].length;i++){
						document.forms['frgrm']['cMes[]'][i].checked = true;
					}
				} else {
					for (i=0;i<document.forms['frgrm']['cMes[]'].length;i++){
						document.forms['frgrm']['cMes[]'][i].checked = false;
					}
				}
			}

			function fnMarcaDiaSemana() {
				if (document.forms['frgrm']['vCheckAll3'].checked == true){
					for (i=0;i<document.forms['frgrm']['cDiaSemA[]'].length;i++){
						document.forms['frgrm']['cDiaSemA[]'][i].checked = true;
					}
				} else {
					for (i=0;i<document.forms['frgrm']['cDiaSemA[]'].length;i++){
						document.forms['frgrm']['cDiaSemA[]'][i].checked = false;
					}
				}
			}

      function fnAddNewRowCiuFac(xTabla) {
      
        var cGrid      = document.getElementById(xTabla);
        var nLastRow   = cGrid.rows.length;
        var nSecuencia = nLastRow+1;
        var cTableRow  = cGrid.insertRow(nLastRow);
        var cSucId     = 'cSucId'     + nSecuencia; // Sucursal
        var cSucIdDiv  = 'cSucIdDiv'  + nSecuencia; // Division Sucursal
        var cUsrId     = 'cUsrId'     + nSecuencia; // Usuario Facturador
        var cUsrNom    = 'cUsrNom'    + nSecuencia; // Usuario Facturador
        var cUsrIdDiv  = 'cUsrIdDiv'  + nSecuencia; // DivisionUsuario Facturador
        var cComDes    = 'cComDes'    + nSecuencia; // Ciudad Facturacion
        var cComDesNom = 'cComDesNom' + nSecuencia; // Ciudad Facturacion
        var oBtnDel    = 'oBtnDel'    + nSecuencia; // Boton de Borrar Row
        
        TD_xAll = cTableRow.insertCell(0);
        TD_xAll.style.width  = "80px";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:060;text-align:center' name = '"+cSucId+"' id = '"+cSucId+"'  "+ 
                              "onBlur = 'javascript:this.value=this.value.toUpperCase(); "+
                              "document.forms[\"frgrm\"][\""+cUsrNom+"\"].value = \"\";"+
                              "document.forms[\"frgrm\"][\""+cUsrId+"\"].value = \"\";"+
                              "document.forms[\"frgrm\"][\""+cComDesNom+"\"].value = \"\";"+
                              "document.forms[\"frgrm\"][\""+cComDes+"\"].value = \"\";"+
                              "f_Links(\"cSucId\",\"VALID\",\""+nSecuencia+"\")'>"+
                              "<input type = 'text' class = 'letra' style = 'width:020;text-align:center' name = '"+cSucIdDiv+"' id = '"+cSucIdDiv+"'>";

        TD_xAll = cTableRow.insertCell(1);
        TD_xAll.style.width  = "200px";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:180;text-align:left' name = '"+cUsrNom+"' id = '"+cUsrNom+"' "+ 
                              "onBlur = 'javascript:this.value=this.value.toUpperCase(); "+
                              "document.forms[\"frgrm\"][\""+cComDesNom+"\"].value = \"\";"+
                              "document.forms[\"frgrm\"][\""+cComDes+"\"].value = \"\";"+
                              "f_Links(\"cUsrId\",\"VALID\",\""+nSecuencia+"\")'>" +
                              "<input type = 'text' class = 'letra' style = 'width:020;text-align:center' name = '"+cUsrIdDiv+"' id = '"+cUsrIdDiv+"'>"+
                              "<input type = 'hidden' name = '"+cUsrId+"' id = '"+cUsrId+"' readonly>";

        TD_xAll = cTableRow.insertCell(2);
        TD_xAll.style.width  = "240px";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:240;text-align:left' name = '"+cComDesNom+"' id = '"+cComDesNom+"'  "+ 
                              "onBlur = 'javascript:this.value=this.value.toUpperCase(); "+
                              "f_Links(\"cComDes\",\"VALID\",\""+nSecuencia+"\")'>" +
                              "<input type = 'hidden' name = '"+cComDes+"' id = '"+cComDes+"' readonly>";

        TD_xAll = cTableRow.insertCell(3);
        TD_xAll.style.width  = "20px";
        TD_xAll.innerHTML    = "<input type = 'button' style = 'width:020;text-align:center' name = "+oBtnDel+" id = "+oBtnDel+" value = 'X' "+
                                "onClick = 'javascript:fnDeleteRowCiuFac(this.value,\""+nSecuencia+"\",\""+xTabla+"\");'>";
        
        document.forms['frgrm']['nSecuencia_' + xTabla].value = nSecuencia;
      }

      function fnDeleteRowCiuFac(xNumRow,xSecuencia,xTabla) {
        var cGrid = document.getElementById(xTabla);
        var nLastRow = cGrid.rows.length;
        if (nLastRow > 1 && xNumRow == "X") {
          if (confirm("Realmente Desea Eliminar el Registro?")){ 
            if(xSecuencia < nLastRow){
              var j=0;
              for(var i=xSecuencia;i<nLastRow;i++){
                j = parseFloat(i)+1;
                document.forms['frgrm']['cSucId'    + i].value = document.forms['frgrm']['cSucId'    + j].value; 
                document.forms['frgrm']['cUsrId'    + i].value = document.forms['frgrm']['cUsrId'    + j].value; 
                document.forms['frgrm']['cUsrNom'   + i].value = document.forms['frgrm']['cUsrNom'   + j].value; 
                document.forms['frgrm']['cComDes'   + i].value = document.forms['frgrm']['cComDes'   + j].value; 
                document.forms['frgrm']['cComDesNom'+ i].value = document.forms['frgrm']['cComDesNom'+ j].value; 
              }
            }
            cGrid.deleteRow(nLastRow - 1);
            document.forms['frgrm']['nSecuencia_' + xTabla].value = nLastRow - 1;
          }
        } else {
          alert("No se Pueden Eliminar Todas las Secuencias, Verifique.");
        }
      }
      
      function fnBorrarCiuFac(xTabla){
        document.getElementById(xTabla).innerHTML = "";
        fnAddNewRowCiuFac(xTabla);
      }

      function fnAddNewRowImp(xTabla) {
      
        var cGrid           = document.getElementById(xTabla);
        var nLastRow        = cGrid.rows.length;
        var nSecuencia      = nLastRow+1;
        var cTableRow       = cGrid.insertRow(nLastRow);
        var cConcepto       = 'cConcepto'        + xTabla + nSecuencia; // Concepto
        var cDescripcion    = 'cDescripcion'     + xTabla + nSecuencia; // Descripcion
        var cUnidadMedida   = 'cUnidadMedida'    + xTabla + nSecuencia; // UnidadMedida
        var cValorUnitario  = 'cValorUnitario'   + xTabla + nSecuencia; // ValorUnitario
        var oBtnDel         = 'oBtnDel' + xTabla + nSecuencia; // Boton de Borrar Row
        
        TD_xAll = cTableRow.insertCell(0);
        TD_xAll.style.width  = "151px";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:151;text-align:left' name = '"+cConcepto+"' id = '"+cConcepto+"' onKeyUp='javascript:f_Enter(event,this.name,\""+xTabla+"\");'>";

        TD_xAll = cTableRow.insertCell(1);
        TD_xAll.style.width  = "190px";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:190;text-align:left' name = '"+cDescripcion+"' id = '"+cDescripcion+"' onKeyUp='javascript:f_Enter(event,this.name,\""+xTabla+"\");'>";

        TD_xAll = cTableRow.insertCell(2);
        TD_xAll.style.width  = "113px";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:113;text-align:left' name = '"+cUnidadMedida+"' id = '"+cUnidadMedida+"' onKeyUp='javascript:f_Enter(event,this.name,\""+xTabla+"\");'>";

        TD_xAll = cTableRow.insertCell(3);
        TD_xAll.style.width  = "105px";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:105;text-align:left' name = '"+cValorUnitario+"' id = '"+cValorUnitario+"' onKeyUp='javascript:f_Enter(event,this.name,\""+xTabla+"\");'>";

        TD_xAll = cTableRow.insertCell(4);
        TD_xAll.style.width  = "20px";
        TD_xAll.innerHTML    = "<input type = 'button' style = 'width:020;text-align:center' name = "+oBtnDel+" id = "+oBtnDel+" value = 'X' "+
                                "onClick = 'javascript:fnDeleteRowImp(this.value,\""+nSecuencia+"\",\""+xTabla+"\");'>";

        document.forms['frgrm']['nSecuencia_' + xTabla].value = nSecuencia;
    }
    </script>
	</head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="600">
				<tr>
					<td>
				  	<fieldset>
					  	<legend><?php echo $_COOKIE['kModo']." ".$_COOKIE['kProDes'] ?></legend>
						 	<form name = 'frgrm' action = 'frcccgra.php' method = 'post' target='fmpro'>
                <input type = "hidden" name = "nSecuencia_Grid_CiuFac">
							 	<center>
      							<table border = '0' cellpadding = '0' cellspacing = '0' width='600'>
  							 			<?php $nCol = f_Format_Cols(30);
  							 			echo $nCol;?>
  							 			<tr>
  										  <td Class = "clase08" colspan = "5">

  												<a href = "javascript:document.forms['frgrm']['cCliId'].value  = '';
  																		  		  document.forms['frgrm']['cCliNom'].value = '';
  																						document.forms['frgrm']['cCliDV'].value  = '';
  																						f_Links('cCliId','VALID')" id = "IdCli">Nit</a><br>
  												<input type = "text" Class = "letra" style = "width:100" name = "cCliId"
  									    	onBlur = "javascript:this.value=this.value.toUpperCase();
  																		         f_Links('cCliId','VALID');
  																		         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
  									    	onFocus="javascript:document.forms['frgrm']['cCliId'].value  ='';
              						  									document.forms['frgrm']['cCliNom'].value = '';
  																				    document.forms['frgrm']['cCliDV'].value  = '';
  													                  this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">

  											</td>
  											<td Class = "clase08" colspan = "1">Dv<br>
  												<input type = "text" Class = "letra" style = "width:20" name = "cCliDV" readonly>
  											</td>
  											<td Class = "clase08" colspan = "24">Cliente<br>
  												<input type = "text" Class = "letra" style = "width:480" name = "cCliNom"
  										    	onBlur = "javascript:this.value=this.value.toUpperCase();
  														                   f_Links('cCliNom','VALID');
  														                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
  										    	onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
  											</td>
  										</tr>
  										<tr>
  											<td Class = "clase08" colspan = "10">Plazo PCC o TODO (Dias calendario)<br>
  												<input type = "text" Class = "letra" style = "width:200" name = "cCccPla" maxlength="3"
  										    	onBlur = "javascript:f_FixInt(this);
  																			         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
  										    	onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
  											</td>
  											<td Class = "clase08" colspan = "10">Plazo IP (Dias calendario)<br>
                          <input type = "text" Class = "letra" style = "width:200" name = "cCccPlaIp" maxlength="3"
                            onBlur = "javascript:f_FixInt(this);
                                                 this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                            onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                        </td>
  											<td Class = "clase08" colspan = "10">Tipo Anticipo
  												<select class="letrase" name = "cCccAnt"  style = "width:200">
  													<option value = "" selected>-- SELECCIONE --</option>
  													<option value = "CON" >CON ANTICIPO</option>
  													<option value = "SIN">SIN ANTICIPO</option>
  													<option value = "CONDICIONADO" >ANTICIPO CONDICIONADO</option>
  													<option value = "GLOBAL" >ANTICIPO GLOBAL</option>
  												</select>
  											</td>
                      </tr>
                      <tr>
                        <td Class = "clase08" colspan = "5">
                          <a href = "javascript:document.forms['frgrm']['cGtaId'].value  = '';
                                              document.forms['frgrm']['cGtaDes'].value = '';
                                              f_Links('cGtaId','VALID')" id = "IdGta">Grupo de Tarifas</a><br>
                        <input type = "text" Class = "letra" style = "width:100" name = "cGtaId"
                          onBlur = "javascript:this.value=this.value.toUpperCase();
                                               f_Links('cGtaId','VALID');
                                               this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                          onFocus="javascript:document.forms['frgrm']['cGtaId'].value  ='';
                                              document.forms['frgrm']['cGtaDes'].value = '';
                                              this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">

                        </td>
                        <?php if (f_InList($cAlfa,"SIACOSIA","TESIACOSIP","DESIACOSIP","DEDESARROL","TEPRUEBASX")) { ?>
                          <td Class = "clase08" colspan = "10">Descripci&oacute;n<br>
                            <input type = "text" Class = "letra" style = "width:200" name = "cGtaDes" readonly>
                          </td>
                          <td Class = "clase08" colspan = "8" align"right" >Aplicar tarifas Facturar a<br>
                            <select class="letrase" name = "cAplFacA" style = "width:160">
                              <option value = "NO" selected>NO</option>
                              <option value = "SI">SI</option>
                            </select>
                          </td>
                          <td Class = "clase08" colspan = "7" align"right" >Aplicar Plazo Facturar a<br>
                            <select class="letrase" name = "cPlaFacA" style = "width:140">
                            <option value = "NO">NO</option>
                            <option value = "SI" selected>SI</option>
                            </select>
                          </td>
                        <?php } else { ?>
                          <td Class = "clase08" colspan = "15">Descripci&oacute;n<br>
                            <input type = "text" Class = "letra" style = "width:300" name = "cGtaDes" readonly>
                          </td>
                          <td Class = "clase08" colspan = "10" align"right" >Aplicar tarifas del Facturar a<br>
                            <select class="letrase" name = "cAplFacA" style = "width:200">
                              <option value = "NO" selected>NO</option>
                              <option value = "SI">SI</option>
                            </select>
                            <input type = "hidden" name = "cPlaFacA" readonly>
                          </td>
                        <?php } ?>
                      </tr>
                      <tr>
  											<td Class = "clase08" colspan = "6">Aplica Interes F<br>
                        	<select class="letrase" name = "cCccIfa" style = "width:120" onchange="javascript:f_Valida_cCccIfa(this.value)">
												    <option value = "NO" selected>NO</option>
  			                		<option value = "SI">SI</option>
  			                	</select>
                        </td>
                        <td Class = "clase08" colspan = "7">Apartir de que Monto
  										 		<input type = "text" Class = "letra" style = "width:140;text-align: right" name = "cCccIfv" value = "" readonly
                            onBlur = "javascript:f_FixInt(this);
  																	             this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                            onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                        </td>
                        <td Class = "clase08" colspan = "8">Numero Cotizacion x Cliente<br>
  										 		<input type = "text" Class = "letra" style = "width:160" name = "cCccCot" maxlength="20"
  													onblur = "javascript:this.value=this.value.toUpperCase();
  																	             this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
  									    	  onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                        </td>
                         <?php if (f_InList($cAlfa,"SIACOSIA","TESIACOSIP","DESIACOSIP","DEDESARROL","TEPRUEBASX")) {
                         	$nColAux=5; ?>
                        <td Class = "clase08" colspan = "4">No. Facturas<br><!-- Nuevo campo agregado -->
  										 		<select name = "cCccCop" size="1" style = "width:80">
  													<?php for($j=0; $j<3; $j++){;?>
  													<option value=<?php echo $j+1; ?>><?php echo $j+1; ?></option>
    									  			<?php } ?>
  											  </select>
                        </td>
                       <?php } else{
                       	$nColAux=9;?>
                       	<input type="hidden" name="cCccCop">
                       	<?php } ?>

                        <td Class = "clase08" colspan = "<?php echo $nColAux ?>">Cierre Facturacion<br>
  												<select name = "cCccDfa" size="1" style = "width:<?php echo $nColAux * 20 ?>">
  												<option value="">---</option>
    										 	  <?php for($i=0; $i<31; $i++){$e=$i+1;?>
    										 	  <option value=<?php echo $e; ?>><?php echo $e; ?></option>
    										 	  <?php }?>
  											  </select>
  											</td>
                      </tr>
											<tr>
												<td Class = "clase08" colspan = "8">ReteFuente Sobre IF<br>
													<select class="letrase" size="1" name = "cCccRfIf"  style = "width:160">
														<option value = "NO" selected>NO</option>
														<option value = "SI" >SI</option>
													</select>
												</td>
												<td Class = "clase08" colspan = "8">AutoreteFuente Sobre IF<br>
													<select class="letrase" size="1" name = "cCccArfIf"  style = "width:160">
														<option value = "NO" selected>NO</option>
														<option value = "SI" >SI</option>
													</select>
												</td>
												<td Class = "clase08" colspan = "7">ReteIca Sobre IF<br>
													<select class="letrase" size="1" name = "cCccRiIf"  style = "width:140">
														<option value = "NO" selected>NO</option>
														<option value = "SI" >SI</option>
													</select>
												</td>
												<td Class = "clase08" colspan = "7">AutoreteIca Sobre IF<br>
													<select class="letrase" size="1" name = "cCccAriIf"  style = "width:140">
														<option value = "NO" selected>NO</option>
														<option value = "SI" >SI</option>
													</select>
												</td>
											</tr>
                      <tr>
                      	<td Class = "clase08" colspan = "15">Cobro Forms Virtuales<br>
  												<select class="letrase" size="1" name = "cCccCfv"  style = "width:300">
  													<option value = "NO" selected>NO</option>
  													<option value = "SI" >SI</option>
  												</select>
  											</td>
  											<td Class = "clase08" colspan = "15">Valor Formulario Virtual
  										 		<input type = "text" Class = "letra" style = "width:300" name = "cCccCfvv" maxlength = "8"
  													onBlur = "javascript:f_FixInt(this);
  																	             this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
  									    	  onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                        </td>
                      </tr>
                      <tr>
                      	<td Class = "clase08" colspan = "15">Cobro Forms DAV Magneticas<br>
  												<select class="letrase" size="1" name = "cCccFdm"  style = "width:300">
  													<option value = "NO" selected>NO</option>
  													<option value = "SI" >SI</option>
  												</select>
  											</td>
  											<td Class = "clase08" colspan = "15">Valor Formulario DAV Magneticas
  										 		<input type = "text" Class = "letra" style = "width:300" name = "cCccFdmv" maxlength = "8"
  													onBlur = "javascript:f_FixInt(this);
  																	             this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
  									    	  onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                        </td>
                      </tr>
                      <tr>
                      	<td Class = "clase08" colspan = "15">Cobro Forms Virtuales Exportacion<br>
  												<select class="letrase" size="1" name = "cCccFve"  style = "width:300">
  													<option value = "NO" selected>NO</option>
  													<option value = "SI" >SI</option>
  												</select>
  											</td>
  											<td Class = "clase08" colspan = "15">Valor Forms Virtuales Exportacion
  										 		<input type = "text" Class = "letra" style = "width:300" name = "cCccFvev" maxlength = "8"
  													onBlur = "javascript:f_FixInt(this);
  																	             this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
  									    	  onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                        </td>
                      </tr>
                      <tr>
                      	<td Class = "clase08" colspan = "15">Cobro Hoja Adicional Forms Virtuales Exportacion<br>
  												<select class="letrase" size="1" name = "cCccFvha"  style = "width:300">
  													<option value = "NO" selected>NO</option>
  													<option value = "SI" >SI</option>
  												</select>
  											</td>
  											<td Class = "clase08" colspan = "15">Valor Hoja Adicional Formulario Virtual Exportacion
  										 		<input type = "text" Class = "letra" style = "width:300" name = "cCccFvhav" maxlength = "8"
  													onBlur = "javascript:f_FixInt(this);
  																	             this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
  									    	  onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                        </td>
                      </tr>
  										<tr>
  											<td Class = "clase08" colspan = "15">Numero Mensual De Facturas Cobro Manejo Documental
  												<input type = "text" style = "width:300;text-align:left" name = "cCccNmfmd" value = "" maxlength="3"
  												onblur="javascript:f_FixInt(this);">
  											</td>
  											<td Class = "clase08" colspan = "8">
  												<a href='javascript:show_calendar("frgrm.dCccFdc")' id="IdVdt">Vigencia Desde</a>
  												<input type = "text" Class = "letra" style = "width:160;text-align:center" name = "dCccFdc" value = "<?php echo date('Y-m-d') ?>" onBlur = "javascript:f_Date(this)">
  											</td>
  											<td Class = "clase08" colspan = "7">
  												<a href='javascript:show_calendar("frgrm.dCccFhc")' id="IdVth">Vigencia Hasta</a>
  												<input type = "text" Class = "letra" style = "width:140;text-align:center" name = "dCccFhc" value = "<?php echo date('Y-m-d') ?>" onBlur = "javascript:f_Date(this)">
  											</td>
  										</tr>
                      <tr>
  								  		<td Class = "clase08" colspan = "30">
  								  			<fieldset>
  					  							<legend>A quien va dirigida la Factura</legend>
  					  							<textarea name = 'cFacA' id = 'cFacA'></textarea>
  					  							<div id = 'overDivFacA'></div>
  					  							<script language="javascript">
  					  								document.getElementById("cFacA").style.display = "none";
  					  							</script>
  												</fieldset>
  								    	</td>
  								  	</tr>

  								  	<?php if (f_InList($cAlfa,"SIACOSIA","TESIACOSIP","DESIACOSIP","DEDESARROL","TEPRUEBASX")) { ?>
  								  	<tr>
    								  	<td Class = "clase08" colspan = "30">
    								  		<fieldset>
    					  						<legend>Grupos de Gesti&oacute;n</legend>
    					  						<textarea name = 'cGruGesId' id = 'cGruGesId'></textarea>
    					  							<div id = 'overDivGruGesId'></div>
    					  							<script language="javascript">
    					  								document.getElementById("cGruGesId").style.display = "none";
    					  							</script>
    												</fieldset>
    								    	</td>
  								  		</tr>
  								  		<?php  } else { ?>
  								  			<input type="hidden" name="cGruGesId" value="">
  								  		<?php } ?>

  								  		<tr>
	  								  		<td Class = "clase08" colspan = "30">
	  								  			<fieldset>
	  					  							<legend> Exclusi&oacute;n de Pagos a Terceros en Facturaci&oacute;n</legend>
	  					  							<textarea name = 'cExcPt' id = 'cExcPt'></textarea>
	  					  							<div id = 'overDivExcPt'></div>
	  					  							<script language="javascript">
	  					  								document.getElementById("cExcPt").style.display = "none";
	  					  							</script>
	  												</fieldset>
	  								    	</td>
	  								  	</tr>

                        <?php if ($cAlfa=="ALADUANA" || $cAlfa=="DEALADUANA" || $cAlfa=='TEALADUANA') { ?>
                        <tr>
                          <td Class = "clase08" colspan = "36">
                            <fieldset>
                              <legend>Valores Unitarios Conceptos Pagos a Terceros</legend>
                              <table border = '0' cellpadding = '0' cellspacing = '0'>
                                <tr>
                                  <td colspan = "35" class="clase08">
                                    <textarea name = 'cValUniTer' id = 'cValUniTer'></textarea>
                                    <div id = 'overDivValUniTer'></div>
                                    <script language="javascript">document.getElementById("cValUniTer").style.display = "none";</script><br>
                                  </td>
                                </tr>
                                <tr>
                                  <td class = "clase08" width = "151" align="left">Concepto</td>
                                  <td class = "clase08" width = "190" align="left">Descripcion</td>
                                  <td class = "clase08" width = "113" align="left">Unidad de Medida</td>
                                  <td class = "clase08" width = "105" align="left">Valor Unitario</td>
                                  <td class = "clase08" width = "020" align="right">&nbsp;</td>
                                </tr>
                              </table>
                              <table border = "0" cellpadding = "0" cellspacing = "0" id="Grid_ValUniTer"></table>
                            </fieldset>
                          </td>
                        </tr>
                        <?php } ?>
                        
	  								  	<?php
	 											/*** Si la variable Categoriacion Conceptos Factuacion esta encendida se habilita el menu de Categoria Conceptos***/
	 											/*** Para Almaviva se parametriza la agrupacion de los conceptos al momento de imprimir por concepto o por tercero***/
												if($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == "SI" ||
												   ($cAlfa == "ALMAVIVA" || $cAlfa == "DEALMAVIVA" || $cAlfa == "TEALMAVIVA")) {

                          $vCccImpRo = array();
                          if($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == "SI") {
                            $vCccImpRo['DISCRIMINADO'] = "DISCRIMINADO";
                            $vCccImpRo['CONSOLIDADO']  = "CONSOLIDADO";
                            $cTitulo = "Formato de Impresi&oacute;n";
                          } elseif($cAlfa == "ALMAVIVA" || $cAlfa == "DEALMAVIVA" || $cAlfa == "TEALMAVIVA") {
                            $vCccImpRo['CONCEPTO'] = "CONCEPTO";
                            $vCccImpRo['CONCEPTO-TERCERO'] = "CONCEPTO-TERCERO";
                            $cTitulo = "Agrupaci&oacute;n de Pagos a Terceros";
                          }
                          ?>
		  								  	<tr>
		  								  		<td Class = "clase08" colspan = "30">
		  								  			<fieldset>
		  					  							<legend>Formato de Impresi&oacute;n</legend>
		  					  							<table border = '0' cellpadding = '0' cellspacing = '0' width='580'>
	                    			 		<?php $nCol = f_Format_Cols(29);
	                    			 		echo $nCol;?>
	                    					<tr>
	                    					  <td Class = "name" colspan = "12"><?php echo $cTitulo ?><br>
	                    							<select class="letra" size="1" name = "cCccImpRo"  style = "width:240">
	                    							  <?php foreach ($vCccImpRo as $cKey => $cValue) { ?>
																				<option value = "<?php echo $cKey ?>"><?php echo $cValue ?></option>
																			<?php } ?>
	                    							</select>
	                    						</td>
	                    				  </tr>
	                            </table>
		  												</fieldset>
		  								    	</td>
		  								  	</tr>
	  								  	<?php } ?>

												<?php if ($cAlfa=="SIACOSIA" || $cAlfa=="DESIACOSIP" || $cAlfa=="TESIACOSIP") { ?>
		  								  	<tr>
		  								  		<td Class = "clase08" colspan = "30">
		  								  			<fieldset>
		  					  							<legend>Do Schenker</legend>
		  					  							<table border = '0' cellpadding = '0' cellspacing = '0' width='580'>
		  					  							  <?php $nCol = f_Format_Cols(29);
                                  echo $nCol;?>
	                    						<tr>
		                    					  <td Class = "name" colspan = "9">Do Schenker Obligatorio para:</td>
		                    				  	<td Class = "letra" colspan = "6">
		                    					 		<input type = "checkbox" name = "oChkSheImp" value = "IMPORTACION">IMPORTACIONES
		                    					 	</td>
		                    						<td Class = "letra"  colspan = "6">
		                    					 		<input type = "checkbox" name = "oChkSheExp" value = "EXPORTACION">EXPORTACIONES
		                    					 	</td>
		                    						<td Class = "letra" colspan = "4" >
		                    					 		<input type = "checkbox" name = "oChkSheDta" value = "DTA">DTA
		                    						</td>
		                    						<td Class = "letra" colspan = "4">
		                    					 		<input type = "checkbox" name = "oChkSheOtr" value = "OTROS">OTROS
		                    						</td>
	                    				  	</tr>
	                            	</table>
		  												</fieldset>
		  								    	</td>
		  								  	</tr>
	  								  	<?php } ?>

												<?php if ($cAlfa=="ALMACAFE" || $cAlfa=="DEALMACAFE" || $cAlfa=="TEALMACAFE" || $cAlfa=="DEDESARROL") { ?>
		  								  	<tr>
		  								  		<td Class = "clase08" colspan = "30">
		  								  			<fieldset>
		  					  							<legend>Integraci&oacute;n con Sistema SAP</legend>
		  					  							<table border = '0' cellpadding = '0' cellspacing = '0' width='580'>
		  					  							  <?php $nCol = f_Format_Cols(29);
                                  echo $nCol;?>
	                    						<tr>
																		<td Class = "name" colspan = "14">Distribuci&oacute;n Anticipo<br>
		                    							<select class="letra" size="1" name = "cCccCdAnt"  style = "width:280">
		                    								<option value = "" selected>-- SELECCIONE --</option>
		                    								<option value = "PCC" >PAGOS A TERCEROS</option>
		                    								<option value = "AMBOS">PAGOS A TERCEROS E INGRESOS PROPIOS</option>
		                    							</select>
		                    						</td>
	                    				  	</tr>
	                            	</table>
		  												</fieldset>
		  								    	</td>
		  								  	</tr>
	  								  	<?php } ?>

                        <?php if ($cAlfa=="DHLEXPRE" || $cAlfa=="DEDHLEXPRE" || $cAlfa=="TEDHLEXPRE") { ?>
                          <tr>
                            <td Class = "clase08" colspan = "30">
                              <fieldset>
                                <legend>Incluir Descuentos</legend>
                                <textarea name = 'cDescuen' id = 'cDescuen'></textarea>
                                <div id = 'overDescuentos'></div>
                                <script language="javascript">
                                  document.getElementById("cDescuen").style.display = "none";
                                </script>
                              </fieldset>
                            </td>
                          </tr>
                        <?php } ?>

                        <tr>
                          <td Class = "clase08" colspan="30">
                            <center>
                            <fieldset>
                              <legend>Integraci&oacute;n con Sistema Seven</legend>
                              <table border = '0' cellpadding = '0' cellspacing = '0' width='580'>
                                <?php $nCol = f_Format_Cols(29);
                                echo $nCol;?>
                                <tr>
                                  <td Class = "name" colspan = "7">Sucursal Recaudadora<br>
                                    <input type = "text" Class = "letra"  style = "width:140"  name = "cCccSur" value = "" maxlength="<?php echo (($cAlfa=="TEALPOPULP" || $cAlfa=="ALPOPULX") ? 5 : 10) ?>"
                                      onblur = "javascript:this.value=this.value.toUpperCase();">
                                  </td>
                                  <td Class = 'name' colspan = "7">C&oacute;digo Detalle<br>
                                    <input type = 'text' Class = 'letra' style = "width:140" name = "cCccDet" value = "" maxlength="2"
                                    onblur = "javascript:this.value=this.value.toUpperCase();">
                                  </td>
                                  <td Class = 'name' colspan = "7">C&oacute;digo Contacto<br>
                                    <input type = 'text' Class = 'letra' style = "width:140" name = "cCccCon" value = "" maxlength="2"
                                    onblur = "javascript:this.value=this.value.toUpperCase();">
                                  </td>
                                  <td Class = 'name' colspan = "8">Direcci&oacute;n Env&iacute;o Factura<br>
                                    <input type = 'text' Class = 'letra' style = "width:160" name = "cCccDir" value = "" maxlength="255"
                                    onblur = "javascript:this.value=this.value.toUpperCase();">
                                  </td>
                                </tr>
                                <tr>
                                  <td Class = "name" colspan = "14">Esquema de Impresi&oacute;n<br>
                                    <select class="letra" size="1" name = "cCccImp"  style = "width:280">
                                      <option value = "" selected>-- SELECCIONE --</option>
                                      <option value = "NORMAL" >NORMAL</option>
                                      <option value = "DPT">DISCRIMINACION PAGOS A TERCEROS</option>
                                    </select>
                                  </td>
                                </tr>
                              </table>
                            </fieldset>
                            </center>
                          </td>
                        </tr>

                        <?php if ($vSysStr['system_activa_facturacion_automatica'] == "SI") { ?>
                        <tr>
                          <td Class = "clase08" colspan="30">
                            <center>
                            <fieldset>
                              <legend>Facturaci&oacute;n Autom&aacute;tica</legend>
                              <table border = '0' cellpadding = '0' cellspacing = '0' width='580'>
                                <?php $nCol = f_Format_Cols(29);
                                echo $nCol;?>
                                <tr>
                                  <td Class = "clase08" colspan = "08">Aplica Facturaci&oacute;n Autom&aacute;tica<br>
                                  </td>
                                  <td Class = "clase08" colspan = "21">
                                    <label><input type="radio" name="cAplica" value="SI" onClick="javascript:fnMostrarCampos()"> SI</label>
                                    <label><input type="radio" name="cAplica" value="NO" onClick="javascript:fnMostrarCampos()"> NO</label>
                                    <br>
                                  </td>
                                </tr>
                              </table>
                              <br>
                              <table border = '0' cellpadding = '0' cellspacing = '0' width='580' id="idFacAut" style="display:none">
                                <?php $nCol = f_Format_Cols(29);
                                echo $nCol;?>
                                <tr>
                                  <td Class = "name" colspan = "29">Generar factura de PCC e IP independientes<br>
                                    <select class="letrase" size="1" name = "cGenFacP"  style = "width:580">
                                      <option value = "" selected>-- SELECCIONE --</option>
                                      <option value = "NO" >NO</option>
                                      <option value = "SI" >SI</option>
                                    </select>
                                  </td>
                                </tr>
                                <tr>
                                  <?php if ($cAlfa=="SIACOSIA" || $cAlfa=="DESIACOSIP" || $cAlfa=="TESIACOSIP") {
                                    $cCol1   = "10";
                                    $cWidth1 = "200";
                                    $cCol2   = "09";
                                    $cWidth2 = "180";
                                    $cCol3   = "10";
                                    $cWidth3 = "200";
                                  } else { 
                                    $cCol1   = "14";
                                    $cWidth1 = "280";
                                    $cCol3   = "15";
                                    $cWidth3 = "300";
                                    ?>
                                    <input type = "hidden" name = "cForImp" value = "">
                                  <?php } ?>
                                  <td Class = "name" colspan = "<?php echo $cCol1 ?>">Tipo de Factura<br>
                                    <select Class = "letrase" name = "cTipFacT" style = "width:<?php echo $cWidth1 ?>">
                                      <option value = "" selected>-- SELECCIONE --</option>
                                      <option value = "DEFINITIVO">DEFINITIVO</option>
                                      <option value = "PREFACTURA">PREFACTURA</option>
                                    </select>
                                  </td>
                                  <?php if ($cAlfa=="SIACOSIA" || $cAlfa=="DESIACOSIP" || $cAlfa=="TESIACOSIP") { ?>
                                    <td Class = "name" colspan = "<?php echo $cCol2 ?>">Formato Impresi&oacute;n<br>
                                      <select Class = "letrase" style = "width:<?php echo $cWidth2 ?>" name = "cForImp">
                                        <option value = "" selected>-- SELECCIONE --</option>
                                        <option value = "NORMAL">NORMAL</option>
                                        <option value = "REGALIAS">REGALIAS</option>
                                      </select>
                                    </td>
                                  <?php } ?>
                                  <td Class = "name" colspan = "<?php echo $cCol3 ?>">Facturar por<br>
                                    <select class="letrase" size="1" name = "cFacPor"  style = "width:<?php echo $cWidth3 ?>">
                                      <option value = "" selected>-- SELECCIONE --</option>
                                      <option value = "TRAMITE" >TR&Aacute;MITE</option>
                                      <option value = "LOTE" >LOTE</option>
                                      <option value = "TRAMITES_APROBADOS" >TRAMITES APROBADOS</option>
                                    </select>
                                  </td>
                                </tr>
                                <tr>
                                  <?php 
                                  if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP" || 
                                      $cAlfa == "KARGORUX" || $cAlfa == "TEKARGORUX" || $cAlfa == "DEKARGORUX") {
                                    $cCol1   = "15";
                                    $cWidth1 = "300";
                                  } else {
                                    $cCol1   = "20";
                                    $cWidth1 = "400";
                                    ?>
                                    <input type = "hidden" name = "cMonId" value = "">
                                  <? } ?>
                                  <td Class = "name" colspan = "8">
                                    <a href = "javascript:document.forms['frgrm']['cCliId2'].value  = '';
                                                        document.forms['frgrm']['cCliNom2'].value = '';
                                                        document.forms['frgrm']['cCliDv2'].value  = '';
                                                        f_Links('cCliId2','VALID')" id = "IdCli2">Nit</a><br>
                                    <input type = "text" Class = "letra" style = "width:160" name = "cCliId2"
                                    onBlur = "javascript:this.value=this.value.toUpperCase();
                                                          if (document.forms['frgrm']['cCliId2'].value  != '') {
                                                            f_Links('cCliId2','VALID');
                                                          }
                                                          this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                    onFocus="javascript:document.forms['frgrm']['cCliId2'].value  ='';
                                                        document.forms['frgrm']['cCliNom2'].value = '';
                                                        document.forms['frgrm']['cCliDv2'].value  = '';
                                                        this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                                  </td>
                                  <td Class = "name" colspan = "1">Dv<br>
                                    <input type = "text" Class = "letra" style = "width:20" name = "cCliDv2" readonly>
                                  </td>
                                  <td Class = "name" colspan = "<?php echo $cCol1 ?>">Facturar a<br>
                                    <input type = "text" Class = "letra" style = "width:<?php echo $cWidth1 ?>" name = "cCliNom2"
                                      onBlur = "javascript:this.value=this.value.toUpperCase();
                                                            if (document.forms['frgrm']['cCliNom2'].value  != '') {
                                                              f_Links('cCliNom2','VALID');
                                                            }
                                                            this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                      onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                                  </td>
                                  <?php
                                  if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP" || 
                                      $cAlfa == "KARGORUX" || $cAlfa == "TEKARGORUX" || $cAlfa == "DEKARGORUX") {
                                  ?>
                                    <td Class = "name" colspan = "5">Moneda<br>
                                      <select Class = "letrase" style = "width:100" name = "cMonId">
                                        <option value = "COP">COP</option>
                                        <option value = "USD">USD</option>
                                      </select>
                                    </td>
                                  <?php } ?>
                                </tr>
                                <?php if($vSysStr['system_activar_openetl'] == "SI") { 
                                  $qTipFac  = "SELECT ";
                                  $qTipFac .= "comtdoxx ";
                                  $qTipFac .= "FROM $cAlfa.fpar0117 ";
                                  $qTipFac .= "WHERE ";
                                  $qTipFac .= "comidxxx = \"F\" AND ";
                                  $qTipFac .= "regestxx = \"ACTIVO\"";
                                  $xTipFac  = f_MySql("SELECT","",$qTipFac,$xConexion01,"");
                                  // f_Mensaje(__FILE__,__LINE__,$qTipFac." ~ ".mysql_num_rows($xTipFac));
                                  $vTipFac = array(); $mTipFac = array();
                                  while ($xRTF = mysql_fetch_array($xTipFac)) {
                                    $mComTdo = explode("|",$xRTF['comtdoxx']);
                                    for($i = 0; $i < count($mComTdo); $i++){
                                      if(trim($mComTdo[$i]) != ""){
                                        $vTipoDoc = explode("~",$mComTdo[$i]); 
                                        if (in_array("{$vTipoDoc[0]}",$vTipFac) == false) {
                                          $vTipFac[] = "{$vTipoDoc[0]}";
                                          $nInd_mTipFac = count($mTipFac);
                                          $mTipFac[$nInd_mTipFac]['comtdoid'] = "{$vTipoDoc[0]}";
                                          $mTipFac[$nInd_mTipFac]['comtdode'] = $vTipoDoc[1];
                                        }
                                      }
                                    }
                                  }
                                  ?>
                                  <tr>
                                    <td Class = "name" colspan = "11">Tipo de Factura Eletr&oacute;nica<br>
                                      <select Class = "letrase" style = "width:220" name = "cComTdoc">
                                        <option value = "">[SELECCIONE]</option>
                                        <?php
                                        $nTipo = 0; $cTipo = ""; 
                                        for($i=0; $i<count($mTipFac); $i++) {
                                          $cTipo = ($cTipo == "") ? $mTipFac[$i]['comtdoid'] : $cTipo;
                                          $nTipo++;
                                          ?>
                                          <option value = "<?php echo $mTipFac[$i]['comtdoid'] ?>"><?php echo strtoupper($mTipFac[$i]['comtdode']) ?></option>
                                          <?php
                                        }
                                        ?>
                                      </select>
                                      <script language="javascript">
                                        parent.fmwork.document.forms['frgrm']['cComTdoc'].value="<?php echo (($nTipo == 1) ? $cTipo : "") ?>";
                                      </script>
                                    </td>
                                    <td Class = "name" colspan = "6">Forma de Pago<br>
                                      <select Class = "letrase" style = "width:120" name = "cComFpag">
                                        <option value = "">-- SELECCIONE --</option>	
                                        <option value = "1">(1) CONTADO</option>
                                        <option value = "2">(2) CREDITO</option>
                                      </select> 
                                    </td>
                                    <td Class = "name" colspan = "12">
                                      <input type = "hidden" name = "cMePagId"  value = "">
                                      <a href = "javascript:document.forms['frgrm']['cMePagId'].value  = '';
                                                            document.forms['frgrm']['cMePagDes'].value = '';
                                                            f_Links('cMePagId','VALID')" id="idMedPag">Medio de Pago</a><br>
                                      <input type = 'text' Class = 'letra' style = 'width:240' name = "cMePagDes" value="<?php echo $_POST['cMePagDes'] ?>" readonly
                                        onFocus="javascript:document.forms['frgrm']['cMePagId'].value  = '';
                                                            document.forms['frgrm']['cMePagDes'].value = '';
                                                            this.style.background='#00FFFF'"
                                                            
                                        onBlur = "javascript:this.value=this.value.toUpperCase();
                                                              f_Links('cMePagId','VALID');
                                                              this.style.background='#FFFFFF'">
                                    </td>
                                  </tr>
                                <?php } ?>
                                <tr>
                                  <td Class = "name" colspan = "29">Observaciones Generales de la Factura [Maximo 200 Caracteres]<br>
                                  <textarea Class = "letrata" name="cComObs" style="width:580;height:35;overflow:auto"
                                    onBlur = "javascript:this.value=this.value.toUpperCase();">
                                  </textarea>
                                  </td>
                                </tr>
                                <tr>
                                  <td Class = "name" colspan = "29">Correos de notificaci&oacute;n<br>
                                    <textarea type = "text" Class = "letra" name = "cCorNotI" style = "width:580;height:40px"
                                      onFocus="javascript:this.style.background='#00FFFF';"
                                      onblur ="javascript:this.style.background='#FFFFFF'"></textarea>
                                  </td>
                                </tr>
                                <tr>
                                  <td Class = "clase08" colspan = "29">
                                    <fieldset>
                                      <legend>Ciudad Facturaci&oacute;n por Sucural</legend>
                                      <table border = '0' cellpadding = '0' cellspacing = '0' width='540'>
                                        <?php $nCol = f_Format_Cols(27); echo $nCol;?>
                                        <tr>
                                          <td colspan="27" class= "clase08" align="right">
                                            <?php if ($_COOKIE['kModo'] != "VER") { ?>
                                              <img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_create-dir_bg.gif" onClick = "javascript:fnAddNewRowCiuFac('Grid_CiuFac')" style = "cursor:pointer" title="Adicionar">
                                              <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_drop.png" onClick = "javascript:fnBorrarCiuFac('Grid_CiuFac')" style = "cursor:pointer" title="Eliminar Todos">
                                            <?php } ?>
                                          </td>                       
                                        </tr>
                                        <tr>
                                          <td class = "clase08" colspan="04" align="left">Sucursal</td>                                                          
                                          <td class = "clase08" colspan="10" align="left">Usuario Facturador</td>
                                          <td class = "clase08" colspan="12" align="left">Ciudad de Facturaci&oacute;n</td>
                                          <td class = "clase08" colspan="01" align="right">&nbsp;</td>                       
                                        </tr>
                                      </table>
                                      <table border = "0" cellpadding = "0" cellspacing = "0" width = "540" id = "Grid_CiuFac"></table>
                                    </fieldset>
                                  </td>
                                <tr>
                                  <td Class = "clase08" colspan = "29">
                                  <fieldset>
                                    <legend>Frecuencia de ejecuci&oacute;n</legend>
                                    <fieldset>
                                      <legend>En que Hora <input type="checkbox" name="vCheckAll" onClick = 'javascript:fnMarcaHoras()'></legend>
                                      <table border = '0' cellpadding = '0' cellspacing = '0' width='520'>
                                        <?php $nCol = f_Format_Cols(26);
                                        echo $nCol;?>
                                        <tr>
                                          <td Class = "clase08" colspan = "3"><input type = "checkbox" name = "cHoras[]" value = "00" onclick="javascript:if(this.checked == true) { this.value = '0'} else { this.value = ''}">0</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cHoras[]" value = "01" onclick="javascript:if(this.checked == true) { this.value = '1'} else { this.value = ''}">1</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cHoras[]" value = "02" onclick="javascript:if(this.checked == true) { this.value = '2'} else { this.value = ''}">2</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cHoras[]" value = "03" onclick="javascript:if(this.checked == true) { this.value = '3'} else { this.value = ''}">3</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cHoras[]" value = "04" onclick="javascript:if(this.checked == true) { this.value = '4'} else { this.value = ''}">4</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cHoras[]" value = "05" onclick="javascript:if(this.checked == true) { this.value = '5'} else { this.value = ''}">5</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cHoras[]" value = "06" onclick="javascript:if(this.checked == true) { this.value = '6'} else { this.value = ''}">6</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cHoras[]" value = "07" onclick="javascript:if(this.checked == true) { this.value = '7'} else { this.value = ''}">7</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cHoras[]" value = "08" onclick="javascript:if(this.checked == true) { this.value = '8'} else { this.value = ''}">8</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cHoras[]" value = "09" onclick="javascript:if(this.checked == true) { this.value = '9'} else { this.value = ''}">9</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cHoras[]" value = "10" onclick="javascript:if(this.checked == true) { this.value = '10'} else { this.value = ''}">10</td>
                                          <td Class = "clase08" colspan = "3"><input type = "checkbox" name = "cHoras[]" value = "11" onclick="javascript:if(this.checked == true) { this.value = '11'} else { this.value = ''}">11</td>
                                        <tr>
                                        <tr>
                                          <td Class = "clase08" colspan = "3"><input type = "checkbox" name = "cHoras[]" value = "12" onclick="javascript:if(this.checked == true) { this.value = '12'} else { this.value = ''}">12</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cHoras[]" value = "13" onclick="javascript:if(this.checked == true) { this.value = '13'} else { this.value = ''}">13</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cHoras[]" value = "14" onclick="javascript:if(this.checked == true) { this.value = '14'} else { this.value = ''}">14</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cHoras[]" value = "15" onclick="javascript:if(this.checked == true) { this.value = '15'} else { this.value = ''}">15</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cHoras[]" value = "16" onclick="javascript:if(this.checked == true) { this.value = '16'} else { this.value = ''}">16</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cHoras[]" value = "17" onclick="javascript:if(this.checked == true) { this.value = '17'} else { this.value = ''}">17</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cHoras[]" value = "18" onclick="javascript:if(this.checked == true) { this.value = '18'} else { this.value = ''}">18</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cHoras[]" value = "19" onclick="javascript:if(this.checked == true) { this.value = '19'} else { this.value = ''}">19</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cHoras[]" value = "20" onclick="javascript:if(this.checked == true) { this.value = '20'} else { this.value = ''}">20</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cHoras[]" value = "21" onclick="javascript:if(this.checked == true) { this.value = '21'} else { this.value = ''}">21</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cHoras[]" value = "22" onclick="javascript:if(this.checked == true) { this.value = '22'} else { this.value = ''}">22</td>
                                          <td Class = "clase08" colspan = "3"><input type = "checkbox" name = "cHoras[]" value = "23" onclick="javascript:if(this.checked == true) { this.value = '23'} else { this.value = ''}">23</td>
                                          </td>
                                        </tr>
                                      </table>
                                    </fieldset>
                                    <fieldset>
                                      <legend>En que Dia <input type="checkbox" name="vCheckAll1" onClick = 'javascript:fnMarcaDias()'></legend>
                                      <table border = '0' cellpadding = '0' cellspacing = '0' width='520'>
                                        <?php $nCol = f_Format_Cols(26);
                                        echo $nCol;?>
                                        <tr>
                                          <td Class = "clase08" colspan = "3"><input type = "checkbox" name = "cDias[]" value = "01" onclick="javascript:if(this.checked == true) { this.value = '01'} else { this.value = ''}">1</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cDias[]" value = "02" onclick="javascript:if(this.checked == true) { this.value = '02'} else { this.value = ''}">2</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cDias[]" value = "03" onclick="javascript:if(this.checked == true) { this.value = '03'} else { this.value = ''}">3</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cDias[]" value = "04" onclick="javascript:if(this.checked == true) { this.value = '04'} else { this.value = ''}">4</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cDias[]" value = "05" onclick="javascript:if(this.checked == true) { this.value = '05'} else { this.value = ''}">5</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cDias[]" value = "06" onclick="javascript:if(this.checked == true) { this.value = '06'} else { this.value = ''}">6</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cDias[]" value = "07" onclick="javascript:if(this.checked == true) { this.value = '07'} else { this.value = ''}">7</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cDias[]" value = "08" onclick="javascript:if(this.checked == true) { this.value = '08'} else { this.value = ''}">8</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cDias[]" value = "09" onclick="javascript:if(this.checked == true) { this.value = '09'} else { this.value = ''}">9</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cDias[]" value = "10" onclick="javascript:if(this.checked == true) { this.value = '10'} else { this.value = ''}">10</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cDias[]" value = "11" onclick="javascript:if(this.checked == true) { this.value = '11'} else { this.value = ''}">11</td>
                                          <td Class = "clase08" colspan = "3"><input type = "checkbox" name = "cDias[]" value = "12" onclick="javascript:if(this.checked == true) { this.value = '12'} else { this.value = ''}">12</td>
                                        </tr>
                                        <tr>
                                          <td Class = "clase08" colspan = "3"><input type = "checkbox" name = "cDias[]" value = "13" onclick="javascript:if(this.checked == true) { this.value = '13'} else { this.value = ''}">13</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cDias[]" value = "14" onclick="javascript:if(this.checked == true) { this.value = '14'} else { this.value = ''}">14</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cDias[]" value = "15" onclick="javascript:if(this.checked == true) { this.value = '15'} else { this.value = ''}">15</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cDias[]" value = "16" onclick="javascript:if(this.checked == true) { this.value = '16'} else { this.value = ''}">16</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cDias[]" value = "17" onclick="javascript:if(this.checked == true) { this.value = '17'} else { this.value = ''}">17</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cDias[]" value = "18" onclick="javascript:if(this.checked == true) { this.value = '18'} else { this.value = ''}">18</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cDias[]" value = "19" onclick="javascript:if(this.checked == true) { this.value = '19'} else { this.value = ''}">19</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cDias[]" value = "20" onclick="javascript:if(this.checked == true) { this.value = '20'} else { this.value = ''}">20</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cDias[]" value = "21" onclick="javascript:if(this.checked == true) { this.value = '21'} else { this.value = ''}">21</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cDias[]" value = "22" onclick="javascript:if(this.checked == true) { this.value = '22'} else { this.value = ''}">22</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cDias[]" value = "23" onclick="javascript:if(this.checked == true) { this.value = '23'} else { this.value = ''}">23</td>
                                          <td Class = "clase08" colspan = "3"><input type = "checkbox" name = "cDias[]" value = "24" onclick="javascript:if(this.checked == true) { this.value = '24'} else { this.value = ''}">24</td>
                                        </tr>
                                        <tr>
                                          <td Class = "clase08" colspan = "3"><input type = "checkbox" name = "cDias[]" value = "25" onclick="javascript:if(this.checked == true) { this.value = '25'} else { this.value = ''}">25</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cDias[]" value = "26" onclick="javascript:if(this.checked == true) { this.value = '26'} else { this.value = ''}">26</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cDias[]" value = "27" onclick="javascript:if(this.checked == true) { this.value = '27'} else { this.value = ''}">27</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cDias[]" value = "28" onclick="javascript:if(this.checked == true) { this.value = '28'} else { this.value = ''}">28</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cDias[]" value = "29" onclick="javascript:if(this.checked == true) { this.value = '29'} else { this.value = ''}">29</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cDias[]" value = "30" onclick="javascript:if(this.checked == true) { this.value = '30'} else { this.value = ''}">30</td>
                                          <td Class = "clase08" colspan = "2"><input type = "checkbox" name = "cDias[]" value = "31" onclick="javascript:if(this.checked == true) { this.value = '31'} else { this.value = ''}">31</td>
                                          <td Class = "clase08" colspan = "11"></td>
                                        </tr>
                                      </table>
                                    </fieldset>
                                    <fieldset>
                                      <legend>En que mes <input type="checkbox" name="vCheckAll2" onClick = 'javascript:fnMarcaMes()'></legend>
                                      <table border = '0' cellpadding = '0' cellspacing = '0' width='520'>
                                        <?php $nCol = f_Format_Cols(26);
                                        echo $nCol;?>
                                        <tr>
                                          <td Class = "clase08" colspan = "7"><input type = "checkbox" name = "cMes[]" value = "01" onclick="javascript:if(this.checked == true) { this.value = '01'} else { this.value = ''}">Enero</td>
                                          <td Class = "clase08" colspan = "6"><input type = "checkbox" name = "cMes[]" value = "02" onclick="javascript:if(this.checked == true) { this.value = '02'} else { this.value = ''}">Febrero</td>
                                          <td Class = "clase08" colspan = "6"><input type = "checkbox" name = "cMes[]" value = "03" onclick="javascript:if(this.checked == true) { this.value = '03'} else { this.value = ''}">Marzo</td>
                                          <td Class = "clase08" colspan = "7"><input type = "checkbox" name = "cMes[]" value = "04" onclick="javascript:if(this.checked == true) { this.value = '04'} else { this.value = ''}">Abril</td>
                                        </tr>
                                        <tr>
                                          <td Class = "clase08" colspan = "7"><input type = "checkbox" name = "cMes[]" value = "05" onclick="javascript:if(this.checked == true) { this.value = '05'} else { this.value = ''}">Mayo</td>
                                          <td Class = "clase08" colspan = "6"><input type = "checkbox" name = "cMes[]" value = "06" onclick="javascript:if(this.checked == true) { this.value = '06'} else { this.value = ''}">Junio</td>
                                          <td Class = "clase08" colspan = "6"><input type = "checkbox" name = "cMes[]" value = "07" onclick="javascript:if(this.checked == true) { this.value = '07'} else { this.value = ''}">Julio</td>
                                          <td Class = "clase08" colspan = "7"><input type = "checkbox" name = "cMes[]" value = "08" onclick="javascript:if(this.checked == true) { this.value = '08'} else { this.value = ''}">Agosto</td>
                                        </tr>
                                        <tr>
                                          <td Class = "clase08" colspan = "7"><input type = "checkbox" name = "cMes[]" value = "09" onclick="javascript:if(this.checked == true) { this.value = '09'} else { this.value = ''}">Septiembre</td>
                                          <td Class = "clase08" colspan = "6"><input type = "checkbox" name = "cMes[]" value = "10" onclick="javascript:if(this.checked == true) { this.value = '10'} else { this.value = ''}">Octubre</td>
                                          <td Class = "clase08" colspan = "6"><input type = "checkbox" name = "cMes[]" value = "11" onclick="javascript:if(this.checked == true) { this.value = '11'} else { this.value = ''}">Noviembre</td>
                                          <td Class = "clase08" colspan = "7"><input type = "checkbox" name = "cMes[]" value = "12" onclick="javascript:if(this.checked == true) { this.value = '12'} else { this.value = ''}">Diciembre</td>
                                        </tr>
                                      </table>
                                    </fieldset>
                                    <fieldset>
                                      <legend>En que dia de la semana <input type="checkbox" name="vCheckAll3" onClick = 'javascript:fnMarcaDiaSemana()'></legend>
                                      <table border = '0' cellpadding = '0' cellspacing = '0' width='520'>
                                        <?php $nCol = f_Format_Cols(26);
                                        echo $nCol;?>
                                        <tr>
                                          <td Class = "clase08" colspan = "3"><input type = "checkbox" name = "cDiaSemA[]" value = "1" onclick="javascript:if(this.checked == true) { this.value = '1'} else { this.value = ''}">Lunes</td>
                                          <td Class = "clase08" colspan = "3"><input type = "checkbox" name = "cDiaSemA[]" value = "2" onclick="javascript:if(this.checked == true) { this.value = '2'} else { this.value = ''}">Martes</td>
                                          <td Class = "clase08" colspan = "4"><input type = "checkbox" name = "cDiaSemA[]" value = "3" onclick="javascript:if(this.checked == true) { this.value = '3'} else { this.value = ''}">Miercoles</td>
                                          <td Class = "clase08" colspan = "4"><input type = "checkbox" name = "cDiaSemA[]" value = "4" onclick="javascript:if(this.checked == true) { this.value = '4'} else { this.value = ''}">Jueves</td>
                                          <td Class = "clase08" colspan = "4"><input type = "checkbox" name = "cDiaSemA[]" value = "5" onclick="javascript:if(this.checked == true) { this.value = '5'} else { this.value = ''}">Viernes</td>
                                          <td Class = "clase08" colspan = "4"><input type = "checkbox" name = "cDiaSemA[]" value = "6" onclick="javascript:if(this.checked == true) { this.value = '6'} else { this.value = ''}">Sabado</td>
                                          <td Class = "clase08" colspan = "4"><input type = "checkbox" name = "cDiaSemA[]" value = "7" onclick="javascript:if(this.checked == true) { this.value = '7'} else { this.value = ''}">Domingo</td>
                                        </tr>
                                      </table>
                                    </fieldset>
                                  </fieldset>
                                  </td>
                              </table>
                            </fieldset>
                            </center>
                          </td>
                        </tr>
											<?php } ?>
											<?php if ($cAlfa=="SIACOSIA" || $cAlfa=="DESIACOSIP" || $cAlfa=="TESIACOSIP") { ?>
												<tr>
													<td Class = "clase08" colspan = "30">
														<fieldset>
															<legend>M&oacute;neda de Impresi&oacute;n</legend>
															<table border = '0' cellpadding = '0' cellspacing = '0' width='580'>
																<?php $nCol = f_Format_Cols(29);
																echo $nCol;?>
																<tr>
																	<td Class = "name" colspan = "7">Impresi&oacute;n en D&oacute;lares:</td>
																	<td Class = "letra" colspan = "6">
																		<input type = "checkbox" name = "oChkImpUS" value = "NO" onclick="javascript:if(this.checked == true) { this.value = 'SI'} else { this.value = 'NO'}">
																	</td>
																</tr>
															</table>
														</fieldset>
													</td>
												</tr>

												<tr>
													<td Class = "clase08" colspan = "30">
														<fieldset>
															<legend>Tarifa por Defecto DO de Tr&aacute;nsito</legend>
															<table border = '0' cellpadding = '0' cellspacing = '0' width='580'>
																<?php $nCol = f_Format_Cols(29);
																echo $nCol;?>
																<tr>
																	<td Class = "name" colspan = "23">Asignar Valor por Defecto en NO a Condiciones Especiales de los DO de Tr&aacute;nsito:</td>
																	<td Class = "letra" colspan = "6">
																		<input type = "checkbox" name = "oChkTrans" value = "NO" onclick="javascript:if(this.checked == true) { this.value = 'SI'} else { this.value = 'NO'}">
																	</td>
																</tr>
															</table>
														</fieldset>
													</td>
												</tr>
											<?php } ?>

                      <?php if ($cAlfa=="ADIMPEXX" || $cAlfa=="DEADIMPEXX" || $cAlfa=="TEADIMPEXX") { ?>
                        <tr>
                          <td Class = "clase08" colspan = "30">
                            <fieldset>
                              <legend>Otros</legend>
                              <table border="0" cellpadding="0" cellspacing="0" width="560">
                                <?php $nCol = f_Format_Cols(28);
                                echo $nCol;?>
                                <tr>
                                  <td Class = "name" colspan="14">Agrupar Tarifas en Representaci&oacute;n Gr&aacute;fica:</td>
                                  <td Class = "name" colspan="1">
                                    <input type="checkbox" name="oCccAgrTa" align="left" onclick="javascript:if(this.checked == true) { this.value = 'SI'} else { this.value = 'NO'}">
                                  </td>
                                </tr>
                              </table>
                            </fieldset>
                          </td>
                        </tr>
                      <?php } ?>
											
  										<tr>
  								   		<td Class = "clase08" colspan = "7">Fecha Cre<br>
  									   		<input type = "text" Class = "letra"  style = "width:140;text-align:center"  name = "dFecCre"  value = "<?php echo date('Y-m-d') ?>" readonly>
  								    	</td>
  								    	<td Class = "clase08" colspan = "5">Hora Cre<br>
  										 		<input type = 'text' Class = 'letra' style = "width:100;text-align:center" name = "dHorCre"  value = "<?php echo date('H:i:s') ?>" readonly>
  											</td>
  											<td Class = "clase08" colspan = "7">Fecha Mod<br>
  									   		<input type = "text" Class = "letra"  style = "width:140;text-align:center"  name = "dFecMod"  value = "<?php echo date('Y-m-d') ?>" readonly>
  								    	</td>
  								    	<td Class = "clase08" colspan = "5">Hora Mod<br>
  										 		<input type = 'text' Class = 'letra' style = "width:100;text-align:center" name = "dHorMod"  value = "<?php echo date('H:i:s') ?>" readonly>
  											</td>
  								   		<td Class = "clase08" colspan = "6">Estado<br>
  										 		<input type = "text" Class = "letra" style = "width:120;text-align:center" name = "cEstado"  value = "ACTIVO"
  										         onblur = "javascript:this.value=this.value.toUpperCase();f_ValidacEstado();
  																		             this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
  														 onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
  											</td>
  										</tr>
									</table>
								</center>
			 	      </form>
						</fieldset>
					</td>
				</tr>
		 	</table>
		</center>
		<center>
			<table border="0" cellpadding="0" cellspacing="0" width="600">
				<tr height="21">
					<?php switch ($_COOKIE['kModo']) {
						case "VER": ?>
							<td width="509" height="21"></td>
							<td width="91" height="21" Class="clase08" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
						<?php break;
						default: ?>
							<td width="418" height="21"></td>
							<td width="91" height="21" Class="clase08" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:f_EnabledCombos();document.forms['frgrm'].submit();f_DisabledCombos()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
							<td width="91" height="21" Class="clase08" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
						<?php break;
			  	} ?>
				</tr>
			</table>
		</center>
		<!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
		<?php
		switch ($_COOKIE['kModo']) {
			case "NUEVO":
				?>
				<script languaje = "javascript">
					document.forms['frgrm']['cEstado'].readOnly  = true;
					f_CargarFacturara();
					fnCargarExclusionPagosTerceros();
					//cargo grilla de Grupos de Gestion
					switch("<?php echo $cAlfa ?>"){
            case "SIACOSIA":
            case "TESIACOSIP":
            case "DESIACOSIP":
            case "DEDESARROL":
            case "TEPRUEBASX":
              f_CargarGruposGestion();
            break;
						case "DHLEXPRE":
            case "TEDHLEXPRE":
            case "DEDHLEXPRE":
              fnCargarDescuentos();
            break;
            case "ALADUANA":
            case "TEALADUANA":
            case "DEALADUANA":
              fnCargarValoresUnitariosTerceros();
              fnAddNewRowImp('Grid_ValUniTer');
            break;
            default:
              //No hace nada
            break;
          }

					switch("<?php echo $cAlfa ?>"){
            case "ROLDANLO":
            case "TEROLDANLO":
            case "DEROLDANLO":
              document.forms['frgrm']['cCccIfa'].value = 'SI';
              document.forms['frgrm']['cCccIfv'].value = '1';
            break;
            default:
              document.forms['frgrm']['cCccIfa'].value = 'NO';
              document.forms['frgrm']['cCccIfv'].value = '';
            break;
          }
          f_Valida_cCccIfa(document.forms['frgrm']['cCccIfa'].value);
          fnAddNewRowCiuFac('Grid_CiuFac');
				</script>
				<?php
			break;
			case "EDITAR":
				f_CargaData($cCliId);
				?>
				<script languaje = "javascript">
					document.forms['frgrm']['cCliId'].readOnly	= true;
					document.forms['frgrm']['cCliId'].onfocus   = "";
				 	document.forms['frgrm']['cCliId'].onblur    = "";

					document.getElementById('IdCli').disabled=true;
				 	document.getElementById('IdCli').href="#";

				 	f_CargarFacturara();
				 	fnCargarExclusionPagosTerceros();
					//cargo grilla de Grupos de Gestion
					//cargo grilla de Grupos de Gestion
          switch("<?php echo $cAlfa ?>"){
            case "SIACOSIA":
            case "TESIACOSIP":
            case "DESIACOSIP":
            case "DEDESARROL":
            case "TEPRUEBASX":
              f_CargarGruposGestion();
            break;
						case "DHLEXPRE":
            case "TEDHLEXPRE":
            case "DEDHLEXPRE":
              fnCargarDescuentos();
            break;
            default:
              //No hace nada
            break;
          }
				</script>
			<?php break;
			case "VER":
				f_CargaData($cCliId);  ?>
				<script languaje = "javascript">
					//document.forms['frgrm']['cCcoId'].readOnly	 = true;
					for (x=0;x<document.forms['frgrm'].elements.length;x++) {
            document.forms['frgrm'].elements[x].readOnly = true;
            document.forms['frgrm'].elements[x].onfocus  = "";
            document.forms['frgrm'].elements[x].onblur   = "";
            document.forms['frgrm'].elements[x].disabled = true;
            document.forms['frgrm'].elements[x].style.fontWeight = "bold";
          }

          document.getElementById('IdCli').disabled=true;
				 	document.getElementById('IdCli').href="#";
				 	document.getElementById('IdVdt').disabled=true;
				 	document.getElementById('IdVdt').href="#";
				 	document.getElementById('IdVth').disabled=true;
				 	document.getElementById('IdVth').href="#";

				 	f_CargarFacturara();
				 	fnCargarExclusionPagosTerceros();
				  //cargo grilla de Grupos de Gestion
					//cargo grilla de Grupos de Gestion
          switch("<?php echo $cAlfa ?>"){
            case "SIACOSIA":
            case "TESIACOSIP":
            case "DESIACOSIP":
            case "DEDESARROL":
            case "TEPRUEBASX":
              f_CargarGruposGestion();
            break;
						case "DHLEXPRE":
            case "TEDHLEXPRE":
            case "DEDHLEXPRE":
              fnCargarDescuentos();
            break;
            break;
            default:
              //No hace nada
            break;
          }
				</script>
			<?php break;
		} ?>

		<?php function f_CargaData($xCliId) {
		  global $cAlfa; global $xConexion01; global $vSysStr;

		  /* TRAIGO DATOS DE CABECERA*/
      $qDatCcc  = "SELECT $cAlfa.fpar0151.*, ";
      $qDatCcc .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X)) AS clinomxx, ";
      $qDatCcc .= "IF($cAlfa.A.CLINOMXX !=\"\",$cAlfa.A.CLINOMXX,CONCAT($cAlfa.A.CLIAPE1X,\" \",$cAlfa.A.CLIAPE2X,\" \",$cAlfa.A.CLINOM1X,\" \",$cAlfa.A.CLINOM2X)) AS clinom2x, ";
			$qDatCcc .= "IF($cAlfa.fpar0151.gtaidxxx != \"\",IF($cAlfa.fpar0111.gtadesxx != \"\",$cAlfa.fpar0111.gtadesxx,\"GRUPO TARIFAS SIN DESCRIPCION\"),\"\") AS gtadesxx ";
			$qDatCcc .= "FROM $cAlfa.fpar0151 ";
			$qDatCcc .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fpar0151.cliidxxx = $cAlfa.SIAI0150.CLIIDXXX ";
			$qDatCcc .= "LEFT JOIN $cAlfa.SIAI0150 AS A ON $cAlfa.fpar0151.cccintxx = $cAlfa.SIAI0150.CLIIDXXX ";
			$qDatCcc .= "LEFT JOIN $cAlfa.fpar0111 ON $cAlfa.fpar0151.gtaidxxx = $cAlfa.fpar0111.gtaidxxx ";
	    $qDatCcc .= "WHERE $cAlfa.fpar0151.cliidxxx = \"$xCliId\" LIMIT 0,1";
			$xDatCcc = f_MySql("SELECT","",$qDatCcc,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qDatCcc."~".mysql_num_rows($xDatCcc));


		 	/* EMPIEZO A RECORRER EL CURSOR DE CABECERA */
			while ($xRDC = mysql_fetch_array($xDatCcc)) {

				$qMedPag  = "SELECT ";
				$qMedPag .= "mpaidxxx, ";
				$qMedPag .= "mpadesxx ";
				$qMedPag .= "FROM $cAlfa.fpar0155 ";
				$qMedPag .= "WHERE ";
				$qMedPag .= "mpaidxxx = \"{$xRDC['cccmpagx']}\" LIMIT 0,1";
				$xMedPag  = f_MySql("SELECT","",$qMedPag,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qMedPag."~ ".mysql_num_rows($xMedPag));
				$vMedPag = mysql_fetch_array($xMedPag);

				$qDatExt  = "SELECT ";
				$qDatExt .= "CLIIDXXX, ";
				$qDatExt .= "IF(CLINOMXX != \"\",CLINOMXX,CONCAT(CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X)) AS CLINOMXX ";
				$qDatExt .= "FROM $cAlfa.SIAI0150 ";
				$qDatExt .= "WHERE ";
				$qDatExt .= "CLIIDXXX = \"{$xRDC['cccfacax']}\" LIMIT 0,1 ";
				$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
				$vDatExt  = mysql_fetch_array($xDatExt);

        // Ciudad Facturacion
        $mCiuFac = f_Explode_Array($xRDC['ccccfacx'],"|","^");
        for ($i=0; $i<count($mCiuFac); $i++) {
          if ($mCiuFac[$i][0] != "") {
            // Buscando el nombre del usuario
            $qNomUsr  = "SELECT USRNOMXX "; 
            $qNomUsr .= "FROM $cAlfa.SIAI0003 ";
            $qNomUsr .= "WHERE USRIDXXX = \"{$mCiuFac[$i][1]}\" LIMIT 0,1";
            $xNomUsr  = f_MySql("SELECT","",$qNomUsr,$xConexion01,"");
            // f_Mensaje(__FILE__,__LINE__,$qNomUsr."~".mysql_num_rows($xNomUsr));
            $vNomUsr  = mysql_fetch_array($xNomUsr);

            $mCiuFac[$i][4] = $vNomUsr['USRNOMXX'];            
          }
        }
				?>
				<script language = "javascript">
				  document.forms['frgrm']['cCliId'].value		    = "<?php echo $xRDC['cliidxxx'] ?>";
				  document.forms['frgrm']['cCliDV'].value		    = "<?php echo f_Digito_Verificacion($xRDC['cliidxxx']) ?>";
				  document.forms['frgrm']['cCliNom'].value	    = "<?php echo $xRDC['clinomxx'] ?>";
				  document.forms['frgrm']['cCccPla'].value	    = "<?php echo $xRDC['cccplaxx'] ?>";
				  document.forms['frgrm']['cCccPlaIp'].value    = "<?php echo $xRDC['cccplaip'] ?>";
				  document.forms['frgrm']['cCccAnt'].value	    = "<?php echo $xRDC['cccantxx'] ?>";

				  document.forms['frgrm']['cCccIfa'].value      = "<?php echo $xRDC['cccifaxx'] ?>";
				  document.forms['frgrm']['cCccIfv'].value      = "<?php echo $xRDC['cccifvxx'] ?>";

				  document.forms['frgrm']['cCccRfIf'].value     = "<?php echo ($xRDC['cccrfifx'] != "") ? $xRDC['cccrfifx'] : "NO" ?>";
				  document.forms['frgrm']['cCccArfIf'].value    = "<?php echo ($xRDC['cccarfif'] != "") ? $xRDC['cccarfif'] : "NO" ?>";
				  document.forms['frgrm']['cCccRiIf'].value     = "<?php echo ($xRDC['cccriifx'] != "") ? $xRDC['cccriifx'] : "NO" ?>";
				  document.forms['frgrm']['cCccAriIf'].value    = "<?php echo ($xRDC['cccariif'] != "") ? $xRDC['cccariif'] : "NO" ?>";

					document.forms['frgrm']['cCccCfvv'].value     = "<?php echo $xRDC['ccccfvvx'] ?>";
					document.forms['frgrm']['cCccNmfmd'].value	  = "<?php echo $xRDC['cccnmfmd'] ?>";
				  document.forms['frgrm']['dCccFdc'].value	    = "<?php echo $xRDC['cccfdcxx'] ?>";
				  document.forms['frgrm']['dCccFhc'].value	    = "<?php echo $xRDC['cccfhcxx'] ?>";
				  document.forms['frgrm']['cCccCfv'].value	    = "<?php echo $xRDC['ccccfvxx'] ?>";
				  document.forms['frgrm']['cCccFdmv'].value	    = "<?php echo $xRDC['cccfdmvx'] ?>";
				  document.forms['frgrm']['cCccFvev'].value	    = "<?php echo $xRDC['cccfvevx'] ?>";
				  document.forms['frgrm']['cCccFvhav'].value	  = "<?php echo $xRDC['cccfvhav'] ?>";

				  document.forms['frgrm']['cCccCot'].value	    = "<?php echo $xRDC['ccccotxx'] ?>";
				  document.forms['frgrm']['cCccDfa'].value	    = "<?php echo $xRDC['cccdfaxx'] ?>";

				  document.forms['frgrm']['cCccSur'].value	    = "<?php echo $xRDC['cccsurxx'] ?>";
				  document.forms['frgrm']['cCccDet'].value	    = "<?php echo $xRDC['cccdetxx'] ?>";
				  document.forms['frgrm']['cCccCon'].value	    = "<?php echo $xRDC['cccconxx'] ?>";
				  document.forms['frgrm']['cCccDir'].value	    = "<?php echo $xRDC['cccdirxx'] ?>";

				  document.forms['frgrm']['cGtaId'].value       = "<?php echo $xRDC['gtaidxxx'] ?>";
		      document.forms['frgrm']['cGtaDes'].value      = "<?php echo $xRDC['gtadesxx'] ?>";

		      document.forms['frgrm']['cFacA'].value        = "<?php echo $xRDC['cccintxx'] ?>";
		      document.forms['frgrm']['cExcPt'].value       = "<?php echo $xRDC['cccexcpt'] ?>";
		      document.forms['frgrm']['cGruGesId'].value    = "<?php echo $xRDC['cccggesx'] ?>";
		      document.forms['frgrm']['dFecCre'].value      = "<?php echo $xRDC['regfcrex'] ?>";
				 	document.forms['frgrm']['dHorCre'].value      = "<?php echo $xRDC['reghcrex'] ?>";
				 	document.forms['frgrm']['dFecMod'].value      = "<?php echo $xRDC['regfmodx'] ?>";
				 	document.forms['frgrm']['dHorMod'].value      = "<?php echo $xRDC['reghmodx'] ?>";
				 	document.forms['frgrm']['cEstado'].value      = "<?php echo $xRDC['regestxx'] ?>";
					document.forms['frgrm']['cCccCop'].value      = "<?php echo $xRDC['ccccopfa'] ?>";
					document.forms['frgrm']['cAplFacA'].value     = "<?php echo ($xRDC['cccaplfa'] == "SI") ? $xRDC['cccaplfa'] : "NO" ?>";
          //El valor por defecto de este campo es SI, vacio aplica SI, para las demas agencias diferentes de SIACO
          document.forms['frgrm']['cPlaFacA'].value     = "<?php echo ($xRDC['cccplafa'] == "NO") ? $xRDC['cccplafa'] : "SI" ?>";

					if ("<?php echo $cAlfa ?>" == "DHLEXPRE" || "<?php echo $cAlfa ?>" == "DEDHLEXPRE" || "<?php echo $cAlfa ?>" == "TEDHLEXPRE") {
						document.forms['frgrm']['cDescuen'].value   = "<?php echo rtrim($xRDC['cccdescu'], "|") ?>";
					}

          if ("<?php echo $vSysStr['system_activa_facturacion_automatica'] ?>" == "SI") {
            document.forms['frgrm']['cAplica'].value		  = "<?php echo ($xRDC['cccafaxx'] == "SI") ? $xRDC['cccafaxx'] : "NO"; ?>";
            if("<?php echo $xRDC['cccafaxx']?>" == "SI"){
              fnMostrarCampos();
              document.forms['frgrm']['cGenFacP'].value		= "<?php echo $xRDC['cccgfacx']?>";
              document.forms['frgrm']['cTipFacT'].value		= "<?php echo $xRDC['ccctfacx']?>";
              <?php
              //Cargando resoluciones de facturacion
              if (count($mCiuFac) > 0) {
                for ($i=0; $i<count($mCiuFac); $i++) {
                  if ($mCiuFac[$i][0] != "") { ?>
                    fnAddNewRowCiuFac('Grid_CiuFac');
                    document.forms['frgrm']['cSucId'+document.forms['frgrm']['nSecuencia_Grid_CiuFac'].value].value		  = "<?php echo $mCiuFac[$i][0] ?>";
                    document.forms['frgrm']['cUsrId'+document.forms['frgrm']['nSecuencia_Grid_CiuFac'].value].value		  = "<?php echo $mCiuFac[$i][1] ?>";
                    document.forms['frgrm']['cUsrNom'+document.forms['frgrm']['nSecuencia_Grid_CiuFac'].value].value		= "<?php echo $mCiuFac[$i][4] ?>";
                    document.forms['frgrm']['cComDes'+document.forms['frgrm']['nSecuencia_Grid_CiuFac'].value].value		= "<?php echo $mCiuFac[$i][2] ?>";
                    document.forms['frgrm']['cComDesNom'+document.forms['frgrm']['nSecuencia_Grid_CiuFac'].value].value = "<?php echo $mCiuFac[$i][3] ?>";
                  <?php }
                }
              } else { ?>
                fnAddNewRowCiuFac('Grid_CiuFac');
              <?php } ?>

              document.forms['frgrm']['cForImp'].value    = "<?php echo $xRDC['cccfimpx']?>";
              document.forms['frgrm']['cFacPor'].value    = "<?php echo $xRDC['cccfacpo']?>";
              document.forms['frgrm']['cCliId2'].value    = "<?php echo $xRDC['cccfacax']?>";
              document.forms['frgrm']['cCliDv2'].value    = "<?php echo f_Digito_Verificacion($vDatExt['CLIIDXXX'])?>";
              document.forms['frgrm']['cCliNom2'].value   = "<?php echo $vDatExt['CLINOMXX']?>";
              document.forms['frgrm']['cMonId'].value     = "<?php echo $xRDC['cccmonfa']?>";
              document.forms['frgrm']['cComObs'].value    = "<?php echo $xRDC['cccobsfa']?>";
              document.forms['frgrm']['cCorNotI'].value   = "<?php echo $xRDC['ccccnotx']?>";
              if ("<?php echo $vSysStr['system_activar_openetl'] ?>" == "SI") {
                document.forms['frgrm']['cComTdoc'].value		= "<?php echo $xRDC['cccuftfe']?>";
                document.forms['frgrm']['cComFpag'].value		= "<?php echo $xRDC['cccfpagx']?>";
                document.forms['frgrm']['cMePagId'].value   = "<?php echo $vMedPag['mpaidxxx']?>";
                document.forms['frgrm']['cMePagDes'].value  = "<?php echo $vMedPag['mpadesxx']?>";
              }

              // Funcionalidad para traer los checkbox que se marquen.
              var cHoras    = document.forms['frgrm']['cHoras[]'];
              var cDias     = document.forms['frgrm']['cDias[]'];
              var cMes      = document.forms['frgrm']['cMes[]'];
              var cDiaSemA  = document.forms['frgrm']['cDiaSemA[]'];

              var mHoras = "<?php echo $xRDC['cccfehor']?>";
              mHoras = mHoras.split(",");
              for(var h=0; h<document.forms['frgrm']['cHoras[]'].length; h++){
                if(mHoras.find(e => e == cHoras[h].value)){
                  cHoras[h].checked = true;
                }
              }

              var mDias = "<?php echo $xRDC['cccfedmx']?>";
              mDias = mDias.split(",");
              for(var d=0; d<document.forms['frgrm']['cDias[]'].length; d++){
                if(mDias.find(e => e == cDias[d].value)){
                  cDias[d].checked = true;
                }
              }

              var mMeses = "<?php echo $xRDC['cccfemes']?>";
              mMeses = mMeses.split(",");
              for(var i=0; i<document.forms['frgrm']['cMes[]'].length; i++){
                if(mMeses.find(e => e == cMes[i].value)){
                  cMes[i].checked = true;
                }
              }

              var mDiaSemA = "<?php echo $xRDC['cccfedsx']?>";
              mDiaSemA = mDiaSemA.split(",");
              for(var i=0; i<document.forms['frgrm']['cDiaSemA[]'].length; i++){
                if(mDiaSemA.find(e => e == cDiaSemA[i].value)){
                  cDiaSemA[i].checked = true;
                }
              }
            }
          }
				</script>

        <?php if($cAlfa=="SIACOSIA" || $cAlfa=="DESIACOSIP" || $cAlfa=="TESIACOSIP")  {

          $vSchenker = explode("~", $xRDC['cccschek']);

          if (in_array("IMPORTACION", $vSchenker) == true) { ?>
            <script languaje = "javascript">
              document.forms['frgrm']['oChkSheImp'].checked   = true;
            </script>
          <?php }

          if (in_array("EXPORTACION", $vSchenker) == true) { ?>
            <script languaje = "javascript">
              document.forms['frgrm']['oChkSheExp'].checked   = true;
            </script>
          <?php }

          if (in_array("DTA", $vSchenker) == true) { ?>
            <script languaje = "javascript">
              document.forms['frgrm']['oChkSheDta'].checked   = true;
            </script>
          <?php }

          if (in_array("OTROS", $vSchenker) == true) { ?>
            <script languaje = "javascript">
             document.forms['frgrm']['oChkSheOtr'].checked   = true;
            </script>
					<?php }
					
					if ($xRDC['cccimpus'] == 'SI') { ?>
						<script languaje = "javascript">
							document.forms['frgrm']['oChkImpUS'].checked   = true;
						</script>
						<?php
					}
					
					if ($xRDC['ccctrans'] == 'SI') { ?>
						<script languaje = "javascript">
							document.forms['frgrm']['oChkTrans'].checked   = true;
						</script>
						<?php
					}
				}

        if (($cAlfa == "ADIMPEXX" || $cAlfa == "DEADIMPEXX" || $cAlfa == "TEADIMPEXX") && $xRDC['cccagrta'] == 'SI') { ?>
          <script languaje = "javascript">
            document.forms['frgrm']['oCccAgrTa'].checked   = true;
          </script>
          <?php
        }

        if ($xRDC['cccifaxx'] == 'NO') { ?>
          <script languaje = "javascript">
            document.forms['frgrm']['cCccIfv'].readOnly   = true;
          </script>
          <?php
        }

			  if ($xRDC['cccifaxx'] == 'NO') { ?>
				  <script languaje = "javascript">
            document.forms['frgrm']['cCccIfv'].readOnly   = true;
				  </script>
				  <?php
			  }
			  if ($xRDC['cccifaxx'] == 'SI') { ?>
				  <script languaje = "javascript">
            document.forms['frgrm']['cCccIfv'].readOnly   = false;
				  </script>
				  <?php
			  }

				if ($xRDC['cccfdmxx'] == 'SI') { ?>
				  <script languaje = "javascript">
            document.forms['frgrm']['cCccFdm'].value = 'SI';
				  </script>
				  <?php
			  }

				if ($xRDC['cccfvexx'] == 'SI') { ?>
				  <script languaje = "javascript">
            document.forms['frgrm']['cCccFve'].value = 'SI';
				  </script>
				  <?php
			  }

				if ($xRDC['cccfvhax'] == 'SI') { ?>
				  <script languaje = "javascript">
            document.forms['frgrm']['cCccFvha'].value = 'SI';
				  </script>
				  <?php
			  }

			  if ($xRDC['cccimpxx'] != '') {
  			   if($xRDC['cccimpxx'] == 'NORMAL'){
  			  ?>
  				  <script languaje = "javascript">
              document.forms['frgrm']['cCccImp'].value = 'NORMAL';
  				  </script>
  				  <?php
  			  }
  			   if($xRDC['cccimpxx'] == 'DPT'){
  			  ?>
  				  <script languaje = "javascript">
              document.forms['frgrm']['cCccImp'].value = 'DPT';
  				  </script>
  				  <?php
  			  }

			  }?>
				<?php
				/*** Si la variable Categoriacion Conceptos Factuacion esta encendida se habilita el menu de Categoria Conceptos***/
				if ($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI' ||
				    ($cAlfa == "ALMAVIVA" || $cAlfa == "DEALMAVIVA" || $cAlfa == "TEALMAVIVA")) {
				  //Valor por defecto
          if($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == "SI") {
            $cCccImpRo = "DISCRIMINADO";
          } elseif($cAlfa == "ALMAVIVA" || $cAlfa == "DEALMAVIVA" || $cAlfa == "TEALMAVIVA") {
            $cCccImpRo = "CONCEPTO";
          }
          ?>
					<script>
						document.forms['frgrm']['cCccImpRo'].value = "<?php echo ($xRDC['cccimpro'] != "") ? $xRDC['cccimpro'] : $cCccImpRo ?>";
					</script>
				<?php }

				/*** Cargar Data Integracion SAP - Almacafe ***/
				if ( $cAlfa == "ALMACAFE" || $cAlfa == "DEALMACAFE" || $cAlfa == "TEALMACAFE" || $cAlfa == "DEDESARROL" ) {
          ?>
					<script>
						document.forms['frgrm']['cCccCdAnt'].value = "<?php echo $xRDC['ccccdant'] ?>";
					</script><?php
				}
      }
		} ?>
	</body>
</html>

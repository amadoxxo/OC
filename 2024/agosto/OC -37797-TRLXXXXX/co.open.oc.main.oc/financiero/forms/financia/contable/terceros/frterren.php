<?php
  /**
  	* Reporte Tercero.
  	* --- Descripcion: Permite Crear reportes a Terceros.
  	* @author
  	* @package opencomex
  	* @version 001
  	*/
  include("../../../../libs/php/utility.php"); ?>
  <html>
    <head>
  	 <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
  		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
  		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
  		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
  		<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
  		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
  		<script language="javascript">
    		function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
    			document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
    			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
    	  }
        
  			function f_Valida_Dv(){
          var kModo = '<?php echo $_COOKIE['kModo'] ?>';
  			  if(document.forms['frgrm']['cTdiId'].value == ''){
  			   document.forms['frgrm']['nTerDV'].value = '';
  			  }
        }
  		  
        function f_Links(xLink,xSwitch,xSecuencia) {
  				var zX    = screen.width;
  				var zY    = screen.height;
  				switch (xLink) {
  					 case "cPaiId":
  						if (xSwitch == "VALID") {
  							var zRuta  = "frpai052.php?gWhat=VALID&gFunction=cPaiId&cPaiId="+document.frgrm.cPaiId.value.toUpperCase()+"";
  							parent.fmpro.location = zRuta;
  						} else {
  		  				var zNx     = (zX-600)/2;
  							var zNy     = (zY-250)/2;
  							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
  							var zRuta   = "frpai052.php?gWhat=WINDOW&gFunction=cPaiId&cPaiId="+document.frgrm.cPaiId.value.toUpperCase()+"";
  							zWindow = window.open(zRuta,"zWindow",zWinPro);
  					  	zWindow.focus();
  						}
  				  break;
  				  case "cPaiId1":
  						if (xSwitch == "VALID") {
  							var zRuta  = "frpai052.php?gWhat=VALID&gFunction=cPaiId1&cPaiId="+document.frgrm.cPaiId1.value.toUpperCase()+"";
  							parent.fmpro.location = zRuta;
  						} else {
  		  				var zNx     = (zX-600)/2;
  							var zNy     = (zY-250)/2;
  							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
  							var zRuta   = "frpai052.php?gWhat=WINDOW&gFunction=cPaiId1&cPaiId="+document.frgrm.cPaiId1.value.toUpperCase()+"";
  							zWindow = window.open(zRuta,"zWindow",zWinPro);
  					  	zWindow.focus();
  						}
  				  break;
  				  case "cDepId":
  						if (xSwitch == "VALID") {
  							var zRuta  = "frdep054.php?gWhat=VALID&gFunction=cDepId&cDepId="+document.forms['frgrm']['cDepId'].value.toUpperCase()+
  							                                                           "&cPaiId="+document.forms['frgrm']['cPaiId'].value.toUpperCase();
  							parent.fmpro.location = zRuta;
  						} else {
  		  				var zNx     = (zX-600)/2;
  							var zNy     = (zY-250)/2;
  							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
  							var zRuta   = "frdep054.php?gWhat=WINDOW&gFunction=cDepId&cDepId="+document.forms['frgrm']['cDepId'].value.toUpperCase()+
  							                                                           "&cPaiId="+document.forms['frgrm']['cPaiId'].value.toUpperCase();
  							zWindow = window.open(zRuta,"zWindow",zWinPro);
  					  	zWindow.focus();
  						}
  				  break;
  				  case "cDepId1":
  						if (xSwitch == "VALID") {
  							var zRuta  = "frdep054.php?gWhat=VALID&gFunction=cDepId1&cDepId="+document.forms['frgrm']['cDepId1'].value.toUpperCase()+
  							                                                           "&cPaiId="+document.forms['frgrm']['cPaiId1'].value.toUpperCase();
  							parent.fmpro.location = zRuta;
  						} else {
  		  				var zNx     = (zX-600)/2;
  							var zNy     = (zY-250)/2;
  							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
  							var zRuta   = "frdep054.php?gWhat=WINDOW&gFunction=cDepId1&cDepId="+document.forms['frgrm']['cDepId1'].value.toUpperCase()+
  							                                                           "&cPaiId="+document.forms['frgrm']['cPaiId1'].value.toUpperCase();
  							zWindow = window.open(zRuta,"zWindow",zWinPro);
  					  	zWindow.focus();
  						}
  				  break;
  				  case "cCiuId":
  						if (xSwitch == "VALID") {
  							var zRuta  = "frciu055.php?gWhat=VALID&gFunction=cCiuId&cCiuId="+document.forms['frgrm']['cCiuId'].value.toUpperCase()+
  							                                                           "&cPaiId="+document.forms['frgrm']['cPaiId'].value.toUpperCase()+
  							                                                           "&cDepId="+document.forms['frgrm']['cDepId'].value.toUpperCase();
  							parent.fmpro.location = zRuta;
  						} else {
  		  				var zNx     = (zX-600)/2;
  							var zNy     = (zY-250)/2;
  							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
  							var zRuta   = "frciu055.php?gWhat=WINDOW&gFunction=cCiuId&CiuId="+document.forms['frgrm']['cCiuId'].value.toUpperCase()+
  							                                                           "&cPaiId="+document.forms['frgrm']['cPaiId'].value.toUpperCase()+
  							                                                           "&cDepId="+document.forms['frgrm']['cDepId'].value.toUpperCase();
  							zWindow = window.open(zRuta,"zWindow",zWinPro);
  					  	zWindow.focus();
  						}
  				  break;
  				  case "cCiuId1":
  						if (xSwitch == "VALID") {
  							var zRuta  = "frciu055.php?gWhat=VALID&gFunction=cCiuId1&cCiuId="+document.forms['frgrm']['cCiuId1'].value.toUpperCase()+
  							                                                           "&cPaiId="+document.forms['frgrm']['cPaiId1'].value.toUpperCase()+
  							                                                           "&cDepId="+document.forms['frgrm']['cDepId1'].value.toUpperCase();
  							parent.fmpro.location = zRuta;
  						} else {
  		  				var zNx     = (zX-600)/2;
  							var zNy     = (zY-250)/2;
  							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
  							var zRuta   = "frciu055.php?gWhat=WINDOW&gFunction=cCiuId1&CiuId="+document.forms['frgrm']['cCiuId1'].value.toUpperCase()+
  							                                                           "&cPaiId="+document.forms['frgrm']['cPaiId1'].value.toUpperCase()+
  							                                                           "&cDepId="+document.forms['frgrm']['cDepId1'].value.toUpperCase();
  							zWindow = window.open(zRuta,"zWindow",zWinPro);
  					  	zWindow.focus();
  						}
  				  break;
  					case "cTdiId":
  						if (xSwitch == "VALID") {
  							var zRuta  = "frpar109.php?gWhat=VALID&gFunction=cTdiId&cTdiId="+document.frgrm.cTdiId.value.toUpperCase()+"&cTerId="+document.frgrm.cTerId.value.toUpperCase();
  							parent.fmpro.location = zRuta;
  						} else {
  		  				var zNx     = (zX-600)/2;
  							var zNy     = (zY-250)/2;
  							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
  							var zRuta   = "frpar109.php?gWhat=WINDOW&gFunction=cTdiId&cTdiId="+document.frgrm.cTdiId.value.toUpperCase()+"&cTerId="+document.frgrm.cTerId.value.toUpperCase();
  							zWindow = window.open(zRuta,"zWindow",zWinPro);
  					  	zWindow.focus();
  						}
  				  break;
  				  case "cGruId":
  						if (xSwitch == "VALID") {
  							var zRuta  = "frpar139.php?gWhat=VALID&gFunction=cGruId&cGruId="+document.frgrm.cGruId.value.toUpperCase()+"";
  							parent.fmpro.location = zRuta;
  						} else {
  		  				var zNx     = (zX-600)/2;
  							var zNy     = (zY-250)/2;
  							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
  							var zRuta   = "frpar139.php?gWhat=WINDOW&gFunction=cGruId&cGruId="+document.frgrm.cGruId.value.toUpperCase()+"";
  							zWindow = window.open(zRuta,"zWindow",zWinPro);
  					  	zWindow.focus();
  						}
  				  break;
  				  case "cCliTpCto":
  						if (xSwitch == "VALID") {
  							var zRuta  = "frter119.php?gWhat=VALID&gFunction=cCliTpCto&gCliTpCto="+document.frgrm.cCliTpCto.value.toUpperCase()+"";
  							parent.fmpro.location = zRuta;
  						} else {
  		  				var zNx     = (zX-600)/2;
  							var zNy     = (zY-250)/2;
  							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
  							var zRuta   = "frter119.php?gWhat=WINDOW&gFunction=cCliTpCto&gCliTpCto="+document.frgrm.cCliTpCto.value.toUpperCase()+"";
  							zWindow = window.open(zRuta,"zWindow",zWinPro);
  					  	zWindow.focus();
  						}
  				  break;
  	 			}
  			}
  
  			function fnValidaSelect(nIndex,cCheckB) {
  			  if (nIndex != "0") {
  			    
  			    document.forms['frgrm'][cCheckB].checked = true;
  			    document.forms['frgrm'][cCheckB].value = "SI";
  			  } else {
  			    document.forms['frgrm'][cCheckB].checked = false;
  			    document.forms['frgrm'][cCheckB].value = "NO"; 
  			  }
  			}    
        
        function f_GenSql(){
          if (document.forms['frgrm']['vChCli'].value  == "NO" &&
              document.forms['frgrm']['vChProC'].value == "NO" &&
              document.forms['frgrm']['vChProE'].value == "NO" &&
              document.forms['frgrm']['vChEmp'].value  == "NO" &&
              document.forms['frgrm']['vChSoc'].value  == "NO" &&
              document.forms['frgrm']['vChEfi'].value  == "NO" &&
              document.forms['frgrm']['vChOtr'].value  == "NO" &&
              document.forms['frgrm']['vChCliVenCo'].value  == "NO") {
            alert ("Debe seleccionar al menos un tipo de Clasificacion.\n");
          } else {
            document.forms['frgrm'].action='frterreg.php';
            document.forms['frgrm'].target='fmpro';
            document.forms['frgrm'].submit();
          }
        }

        function fnGenSqlReporteCuentasTerceros(){
          if (document.forms['frgrm']['vChCli'].value  == "NO" &&
              document.forms['frgrm']['vChProC'].value == "NO" &&
              document.forms['frgrm']['vChProE'].value == "NO" &&
              document.forms['frgrm']['vChEmp'].value  == "NO" &&
              document.forms['frgrm']['vChSoc'].value  == "NO" &&
              document.forms['frgrm']['vChEfi'].value  == "NO" &&
              document.forms['frgrm']['vChOtr'].value  == "NO" &&
              document.forms['frgrm']['vChCliVenCo'].value  == "NO") {
            alert ("Debe seleccionar al menos un tipo de Clasificacion.\n");
          } else {
            document.forms['frgrm'].action='frrepctg.php';
            document.forms['frgrm'].target='fmpro';
            document.forms['frgrm'].submit();
          }
        }
        
        function f_LimpiarConsulta(){
          
          document.forms['frgrm']['cTpeId'].value        = "";
          document.forms['frgrm']['cTdiId'].value        = "";
          document.forms['frgrm']['cTdiDes'].value       = "";
          document.forms['frgrm']['cTerId'].value        = "";
          document.forms['frgrm']['nTerDV'].value        = "";
          document.forms['frgrm']['oExcTerId'].checked   = false;
          document.forms['frgrm']['oExcTerId'].value     = "NO";
          document.forms['frgrm']['cTerNom'].value       = "";
          document.forms['frgrm']['oExcTerNom'].checked  = false;
          document.forms['frgrm']['oExcTerNom'].value    = "NO";
          document.forms['frgrm']['cTerNomC'].value      = "";
          document.forms['frgrm']['oExcTerNomC'].checked = false;
          document.forms['frgrm']['oExcTerNomC'].value   = "NO";
          document.forms['frgrm']['cPaiId'].value        = "";
          document.forms['frgrm']['cPaiDes'].value       = "";
          document.forms['frgrm']['cDepId'].value        = "";
          document.forms['frgrm']['cDepDes'].value       = "";
          document.forms['frgrm']['cCiuId'].value        = "";
          document.forms['frgrm']['cCiuDes'].value       = "";
          document.forms['frgrm']['cGruId'].value        = "";
          document.forms['frgrm']['cGruDes'].value       = "";
          document.forms['frgrm']['cPaiId1'].value       = "";
          document.forms['frgrm']['cPaiDes1'].value      = "";
          document.forms['frgrm']['cDepId1'].value       = "";
          document.forms['frgrm']['cDepDes1'].value      = "";
          document.forms['frgrm']['cCiuId1'].value       = "";
          document.forms['frgrm']['cCiuDes1'].value      = "";
          document.forms['frgrm']['cTerFPa'].value       = "";
          document.forms['frgrm']['cTerMedP'].value      = "";
          document.forms['frgrm']['cEstado'].value       = "ACTIVO";
          
          document.forms['frgrm']['vChProC'].checked  = false;
          document.forms['frgrm']['vChProC'].value    = "NO";
          
          document.forms['frgrm']['vChCli'].checked   = false;
          document.forms['frgrm']['vChCli'].value    	= "NO";
          
          document.forms['frgrm']['vChProE'].checked  = false;
          document.forms['frgrm']['vChProE'].value    = "NO";
          
          document.forms['frgrm']['vChEmp'].checked  = false;
          document.forms['frgrm']['vChEmp'].value    = "NO";
          
          document.forms['frgrm']['vChSoc'].checked  = false;
          document.forms['frgrm']['vChSoc'].value    = "NO";
          
          document.forms['frgrm']['vChEfi'].checked  = false;
          document.forms['frgrm']['vChEfi'].value    = "NO";
          
          document.forms['frgrm']['vChOtr'].checked  = false;
          document.forms['frgrm']['vChOtr'].value    = "NO";
          
          document.forms['frgrm']['vChCliVenCo'].checked  = false;
          document.forms['frgrm']['vChCliVenCo'].value    = "NO";
          
          document.forms['frgrm']['oCliReIva'].disabled = false;
          document.forms['frgrm']['oCliReIva'].checked  = false;
          document.forms['frgrm']['oCliReIva'].value    = "NO";
                                        
          document.forms['frgrm']['oCliReCom'].disabled= false;
          document.forms['frgrm']['oCliReCom'].checked = false;
          document.forms['frgrm']['oCliReCom'].value   = "NO";
                                        
          document.forms['frgrm']['oCliReg'].disabled  = false;
          document.forms['frgrm']['oCliReg'].checked   = false;
          document.forms['frgrm']['oCliReg'].value     = "NO";
                                        
          document.forms['frgrm']['oCliGc'].disabled   = false;
          document.forms['frgrm']['oCliGc'].checked    = false;
          document.forms['frgrm']['oCliGc'].value      = "NO";
                                        
          document.forms['frgrm']['oCliNrp'].disabled  = false;
          document.forms['frgrm']['oCliNrp'].checked   = false;
          document.forms['frgrm']['oCliNrp'].value     = "NO";
                                        
          document.forms['frgrm']['oCliNrpai'].disabled= false;
          document.forms['frgrm']['oCliNrpai'].checked = false;
          document.forms['frgrm']['oCliNrpai'].value   = "NO";
                                        
          document.forms['frgrm']['oCliNrpif'].disabled= false;
          document.forms['frgrm']['oCliNrpif'].checked = false;
          document.forms['frgrm']['oCliNrpif'].value   = "NO";

          document.forms['frgrm']['oCliNrpNsr'].disabled= false;
          document.forms['frgrm']['oCliNrpNsr'].checked = false;
          document.forms['frgrm']['oCliNrpNsr'].value   = "NO";

          document.forms['frgrm']['oCliAr'].disabled   = false;
          document.forms['frgrm']['oCliAr'].checked    = false;
          document.forms['frgrm']['oCliAr'].value      = "NO";
                                        
          document.forms['frgrm']['oCliArAre'].disabled = false;
          document.forms['frgrm']['oCliArAre'].checked  = false;
          document.forms['frgrm']['oCliArAre'].value    = "NO";
                                        
          document.forms['frgrm']['oCliArAiv'].disabled = false;
          document.forms['frgrm']['oCliArAiv'].checked  = false;
          document.forms['frgrm']['oCliArAiv'].value    = "NO";
                                        
          document.forms['frgrm']['oCliArAic'].disabled = false;
          document.forms['frgrm']['oCliArAic'].checked  = false;
          document.forms['frgrm']['oCliArAic'].value    = "NO";
                                        
          document.forms['frgrm']['oCliArAcr'].disabled = false;
          document.forms['frgrm']['oCliArAcr'].checked  = false;
          document.forms['frgrm']['oCliArAcr'].value    = "NO";
                                        
          document.forms['frgrm']['cCliArAis'].disabled = false;
          document.forms['frgrm']['cCliArAis'].value    = "";
                                        
          document.forms['frgrm']['oCliNsrr'].disabled  = false;
          document.forms['frgrm']['oCliNsrr'].checked   = false;
          document.forms['frgrm']['oCliNsrr'].value     = "NO";
                                        
          document.forms['frgrm']['oCliNsriv'].disabled = false;
          document.forms['frgrm']['oCliNsriv'].checked  = false;
          document.forms['frgrm']['oCliNsriv'].value    = "NO";
                                        
          document.forms['frgrm']['oCliNsrcr'].disabled = false;
          document.forms['frgrm']['oCliNsrcr'].checked  = false;
          document.forms['frgrm']['oCliNsrcr'].value    = "NO";
                                        
          document.forms['frgrm']['oCliArr'].disabled   = false;
          document.forms['frgrm']['oCliArr'].checked    = false;
          document.forms['frgrm']['oCliArr'].value      = "NO";
                                        
          document.forms['frgrm']['oCliAriva'].disabled = false;
          document.forms['frgrm']['oCliAriva'].checked  = false;
          document.forms['frgrm']['oCliAriva'].value    = "NO";
                                        
          document.forms['frgrm']['oCliArcr'].disabled  = false;
          document.forms['frgrm']['oCliArcr'].checked   = false;
          document.forms['frgrm']['oCliArcr'].value     = "NO";
                                        
          document.forms['frgrm']['oCliArrI'].disabled  = false;
          document.forms['frgrm']['oCliArrI'].checked   = false;
          document.forms['frgrm']['oCliArrI'].value     = "NO";
                                        
          document.forms['frgrm']['cCliArrIs'].disabled = false;
          document.forms['frgrm']['cCliArrIs'].value    = "";
                                        
          document.forms['frgrm']['oCliNsrri'].disabled = false;
          document.forms['frgrm']['oCliNsrri'].checked  = false;
          document.forms['frgrm']['oCliNsrri'].value    = "NO";
                                        
          document.forms['frgrm']['oCliPci'].disabled   = false;
          document.forms['frgrm']['oCliPci'].checked    = false;
          document.forms['frgrm']['oCliPci'].value      = "NO";

          document.forms['frgrm']['oCliNsOfe'].disabled = false;
          document.forms['frgrm']['oCliNsOfe'].checked  = false;
          document.forms['frgrm']['oCliNsOfe'].value    = "NO";

          f_Habilita('oCliArrI'); 
          f_Habilita('oCliArAic');
          f_Habilita('oCliReIva'); 
          f_Habilita('oCliNrp'); 
          f_Habilita('oCliAr');
        }
        
        function f_Radio(xRadio,xValor){
          document.forms['frgrm'][xRadio].value=xValor;
        }
        
        function f_Check(xValue,xCh){   
          if (xValue) {
            document.forms['frgrm'][xCh].value="SI";
          }else{
            document.forms['frgrm'][xCh].value="NO";
          }
        }
        
        function f_Habilita(xOpcion) {
          if (document.forms['frgrm']['cBuscar'].value == "AND") {
            switch (xOpcion) {
              case "oCliReIva":
                if (document.forms['frgrm'][xOpcion].checked == true) {
                  document.getElementById('oCliReCom').disabled = false;
                  document.getElementById('oCliReSim').disabled = false;
                  document.forms['frgrm'][xOpcion].value        = "SI";              
                } else {
                  document.getElementById('oCliReCom').disabled = true;
                  document.getElementById('oCliReSim').disabled = true;
                  document.forms['frgrm'][xOpcion].value        = "NO";              
                }
                document.getElementById('oCliReCom').checked = false;
                document.getElementById('oCliReSim').checked = false;
                document.forms['frgrm']['oCliReg'].value     = "";
                
                document.forms['frgrm']['oCliAr'].disabled   = false;
                document.forms['frgrm']['oCliNrp'].disabled  = false;
                document.forms['frgrm']['oCliArr'].disabled  = false;
                document.forms['frgrm']['oCliAriva'].disabled= false;
                document.forms['frgrm']['oCliArcr'].disabled = false;
                document.forms['frgrm']['oCliArrI'].disabled = false;
                document.forms['frgrm']['oCliPci'].disabled  = false;  
                document.forms['frgrm']['oCliGc'].disabled   = false;           
              break;
              case "oCliReCom":  
                document.forms['frgrm']['oCliReg'].value     = "COMUN";
                document.forms['frgrm']['oCliReSim'].checked = false;
                document.forms['frgrm']['oCliAr'].disabled   = false;
                document.forms['frgrm']['oCliNrp'].disabled  = false;
                document.forms['frgrm']['oCliArr'].disabled  = false;
                document.forms['frgrm']['oCliAriva'].disabled= false;
                document.forms['frgrm']['oCliArcr'].disabled = false;
                document.forms['frgrm']['oCliArrI'].disabled = false;
                document.forms['frgrm']['oCliPci'].disabled  = false;
              break;
              case "oCliReSim":
                document.forms['frgrm']['oCliReg'].value     = "SIMPLIFICADO";
                document.forms['frgrm']['oCliGc'].checked    = false;
                document.forms['frgrm']['oCliReCom'].checked = false;
                
                document.forms['frgrm']['oCliAr'].checked     = false;
                document.forms['frgrm']['oCliAr'].value       = "NO"; 
                document.forms['frgrm']['oCliAr'].disabled    = true;
                document.getElementById('oCliArAre').disabled = true;
                document.getElementById('oCliArAiv').disabled = true;
                document.getElementById('oCliArAic').disabled = true;
                document.getElementById('oCliArAcr').disabled = true;
                document.getElementById('cCliArAis').disabled = true;
                document.forms['frgrm']['cCliArAis'].value    = ""; 
                            
                document.forms['frgrm']['oCliArAre'].checked = false;
                document.forms['frgrm']['oCliArAre'].value   = "NO";
                document.forms['frgrm']['oCliArAiv'].checked = false;
                document.forms['frgrm']['oCliArAiv'].value   = "NO";
                document.forms['frgrm']['oCliArAic'].checked = false;
                document.forms['frgrm']['oCliArAic'].value   = "NO";
                document.forms['frgrm']['oCliArAcr'].checked = false;
                document.forms['frgrm']['oCliArAcr'].value   = "NO";
                
                document.forms['frgrm']['oCliNrp'].checked     = false;
                document.forms['frgrm']['oCliNrp'].value       = "NO";            
                document.forms['frgrm']['oCliNrp'].disabled    = true;
                document.getElementById('oCliNrpai').disabled  = true;
                document.getElementById('oCliNrpif').disabled  = true;
                document.getElementById('oCliNrpNsr').disabled = true;
                document.forms['frgrm']['oCliNsrr'].disabled   = false;
                document.forms['frgrm']['oCliNsriv'].disabled  = false;
                document.forms['frgrm']['oCliNsrri'].disabled  = false;
                document.forms['frgrm']['oCliNsrcr'].disabled  = false;
                document.forms['frgrm']['oCliNrpai'].checked   = false;
                document.forms['frgrm']['oCliNrpai'].value     = "NO";
                document.forms['frgrm']['oCliNrpif'].checked   = false;
                document.forms['frgrm']['oCliNrpif'].value     = "NO";
                document.forms['frgrm']['oCliNrpNsr'].checked  = false;
                document.forms['frgrm']['oCliNrpNsr'].value    = "NO";
                
                document.forms['frgrm']['oCliArr'].checked  = false;
                document.forms['frgrm']['oCliArr'].value    = "NO";
                document.forms['frgrm']['oCliArr'].disabled = true;
                
                document.forms['frgrm']['oCliAriva'].checked  = false;
                document.forms['frgrm']['oCliAriva'].value    = "NO";
                document.forms['frgrm']['oCliAriva'].disabled = true;
                
                document.forms['frgrm']['oCliArrI'].checked   = false;
                document.forms['frgrm']['oCliArrI'].value     = "NO";
                document.forms['frgrm']['oCliArrI'].disabled  = true;
                document.getElementById('cCliArrIs').disabled = true;
                document.forms['frgrm']['cCliArrIs'].value    = "";
                
                document.forms['frgrm']['oCliPci'].checked  = false;
                document.forms['frgrm']['oCliPci'].value    = "NO";
                document.forms['frgrm']['oCliPci'].disabled = true;    
              break;
              case "oCliGc": 
              case "oCliPci": 
                if (document.forms['frgrm'][xOpcion].checked == true) {
                  document.forms['frgrm'][xOpcion].value = "SI";
  
                  document.forms['frgrm']['oCliReIva'].checked  = true;
                  document.forms['frgrm']['oCliReIva'].value    = "SI";
                  document.forms['frgrm']['oCliReIva'].disabled = false;               
                  document.getElementById('oCliReCom').checked  = true;
                  document.getElementById('oCliReSim').checked  = false;
                  document.getElementById('oCliReCom').disabled = false;
                  document.getElementById('oCliReSim').disabled = false;
                  document.forms['frgrm']['oCliReg'].value      = "COMUN"; 
  
                  document.forms['frgrm']['oCliAr'].disabled    = false;
                  document.forms['frgrm']['oCliNrp'].disabled   = false;
                  document.forms['frgrm']['oCliArr'].disabled   = false;
                  document.forms['frgrm']['oCliAriva'].disabled = false;
                  document.forms['frgrm']['oCliArcr'].disabled  = false;
                  document.forms['frgrm']['oCliArrI'].disabled  = false;
                  document.forms['frgrm']['oCliPci'].disabled   = false;
                } else {
                  document.forms['frgrm'][xOpcion].value = "NO";              
                }           
              break;
              case "oCliNrp":
                if (document.forms['frgrm'][xOpcion].checked == true) {
                  document.getElementById('oCliNrpai').disabled  = false;
                  document.getElementById('oCliNrpif').disabled  = false;
                  document.getElementById('oCliNrpNsr').disabled = false;

                  document.forms['frgrm'][xOpcion].value        = "SI";   
  
                  document.forms['frgrm']['oCliReIva'].checked  = false;
                  document.forms['frgrm']['oCliReIva'].value    = "NO"; 
                  document.forms['frgrm']['oCliReIva'].disabled = true;
                  document.getElementById('oCliReCom').disabled = true;
                  document.getElementById('oCliReSim').disabled = true;
                  document.getElementById('oCliReCom').checked  = false;
                  document.getElementById('oCliReSim').checked  = false;
                  document.forms['frgrm']['oCliReg'].value      = "";
                  
                  document.forms['frgrm']['oCliGc'].checked     = false;
                  document.forms['frgrm']['oCliGc'].disabled    = true;
  
                  document.forms['frgrm']['oCliAr'].checked     = false;
                  document.forms['frgrm']['oCliAr'].value       = "NO"; 
                  document.forms['frgrm']['oCliAr'].disabled    = true;
                  document.getElementById('oCliArAre').disabled = true;
                  document.getElementById('oCliArAiv').disabled = true;
                  document.getElementById('oCliArAic').disabled = true;
                  document.getElementById('oCliArAcr').disabled = true;
                  document.getElementById('cCliArAis').disabled = true;
                  document.forms['frgrm']['cCliArAis'].value    = "";          
                  document.forms['frgrm']['oCliArAre'].checked  = false;
                  document.forms['frgrm']['oCliArAre'].value    = "NO";
                  document.forms['frgrm']['oCliArAiv'].checked  = false;
                  document.forms['frgrm']['oCliArAiv'].value    = "NO";
                  document.forms['frgrm']['oCliArAic'].checked  = false;
                  document.forms['frgrm']['oCliArAic'].value    = "NO";
                  document.forms['frgrm']['oCliArAcr'].checked  = false;
                  document.forms['frgrm']['oCliArAcr'].value    = "NO";
                  
                  document.forms['frgrm']['oCliNsrr'].checked  = false;
                  document.forms['frgrm']['oCliNsrr'].value    = "NO";
                  document.forms['frgrm']['oCliNsrr'].disabled = true;
                  
                  document.forms['frgrm']['oCliNsriv'].checked  = false;
                  document.forms['frgrm']['oCliNsriv'].value    = "NO";
                  document.forms['frgrm']['oCliNsriv'].disabled = true;
  
                  document.forms['frgrm']['oCliNsrri'].checked  = false;
                  document.forms['frgrm']['oCliNsrri'].value    = "NO";
                  document.forms['frgrm']['oCliNsrri'].disabled = true;
  
                  document.forms['frgrm']['oCliNsrcr'].checked  = false;
                  document.forms['frgrm']['oCliNsrcr'].value    = "NO";
                  document.forms['frgrm']['oCliNsrcr'].disabled = true;
  
                  document.forms['frgrm']['oCliArr'].checked  = false;
                  document.forms['frgrm']['oCliArr'].value    = "NO";
                  document.forms['frgrm']['oCliArr'].disabled = true;
                  
                  document.forms['frgrm']['oCliAriva'].checked  = false;
                  document.forms['frgrm']['oCliAriva'].value    = "NO";
                  document.forms['frgrm']['oCliAriva'].disabled = true;
                  
                  document.forms['frgrm']['oCliArcr'].checked  = false;
                  document.forms['frgrm']['oCliArcr'].value    = "NO";
                  document.forms['frgrm']['oCliArcr'].disabled = true;
  
                  document.forms['frgrm']['oCliArrI'].checked   = false;
                  document.forms['frgrm']['oCliArrI'].value     = "NO";
                  document.forms['frgrm']['oCliArrI'].disabled  = true;
                  document.getElementById('cCliArrIs').disabled = true;
                  document.forms['frgrm']['cCliArrIs'].value    = "";
              
                  document.forms['frgrm']['oCliPci'].checked  = false;
                  document.forms['frgrm']['oCliPci'].value    = "NO";
                  document.forms['frgrm']['oCliPci'].disabled = true; 
                } else {
                  document.getElementById('oCliNrpai').disabled  = true;
                  document.getElementById('oCliNrpif').disabled  = true;
                  document.getElementById('oCliNrpNsr').disabled = true;

                  document.forms['frgrm'][xOpcion].value        = "NO";
                  document.forms['frgrm']['oCliReIva'].disabled = false;
                  document.forms['frgrm']['oCliGc'].disabled    = false;
                  document.forms['frgrm']['oCliAr'].disabled    = false;
                  document.forms['frgrm']['oCliNsrr'].disabled  = false;
                  document.forms['frgrm']['oCliNsriv'].disabled = false;
                  document.forms['frgrm']['oCliNsrri'].disabled = false;
                  document.forms['frgrm']['oCliNsrcr'].disabled = false;
                  document.forms['frgrm']['oCliArr'].disabled   = false;
                  document.forms['frgrm']['oCliAriva'].disabled = false;
                  document.forms['frgrm']['oCliArcr'].disabled  = false;
                  document.forms['frgrm']['oCliArrI'].disabled  = false;
                  document.forms['frgrm']['oCliPci'].disabled   = false;
                }
                document.forms['frgrm']['oCliNrpai'].checked  = false;
                document.forms['frgrm']['oCliNrpai'].value    = "NO";
                document.forms['frgrm']['oCliNrpif'].checked  = false;
                document.forms['frgrm']['oCliNrpif'].value    = "NO";
                document.forms['frgrm']['oCliNrpNsr'].checked = false;
                document.forms['frgrm']['oCliNrpNsr'].value   = "NO";
              break;
              case "oCliAr":
                if (document.forms['frgrm'][xOpcion].checked == true) {
                  document.getElementById('oCliArAre').disabled = false;
                  document.getElementById('oCliArAiv').disabled = false;
                  document.getElementById('oCliArAic').disabled = false;
                  document.getElementById('oCliArAcr').disabled = false;
                  document.forms['frgrm'][xOpcion].value        = "SI";              
                } else {
                  document.getElementById('oCliArAre').disabled = true;
                  document.getElementById('oCliArAiv').disabled = true;
                  document.getElementById('oCliArAic').disabled = true;
                  document.getElementById('oCliArAcr').disabled = true;
                  document.getElementById('cCliArAis').disabled = true;
                  document.forms['frgrm']['cCliArAis'].value    = "";
                  document.forms['frgrm'][xOpcion].value        = "NO";              
                }
                document.forms['frgrm']['oCliArAre'].checked = false;
                document.forms['frgrm']['oCliArAre'].value   = "NO";
                document.forms['frgrm']['oCliArAiv'].checked = false;
                document.forms['frgrm']['oCliArAiv'].value   = "NO";
                document.forms['frgrm']['oCliArAic'].checked = false;
                document.forms['frgrm']['oCliArAic'].value   = "NO";
                document.forms['frgrm']['oCliArAcr'].checked = false;
                document.forms['frgrm']['oCliArAcr'].value   = "NO";
              break;
              case "oCliArrI":
                if (document.forms['frgrm'][xOpcion].checked == true) {
                  document.getElementById('cCliArrIs').disabled = false;
                  document.forms['frgrm'][xOpcion].value        = "SI";               
                } else {
                  document.getElementById('cCliArrIs').disabled = true;
                  document.forms['frgrm']['cCliArrIs'].value    = "";
                  document.forms['frgrm'][xOpcion].value        = "NO";              
                }             
              break;
              case "oCliArAic":
                if (document.forms['frgrm'][xOpcion].checked == true) {
                  document.getElementById('cCliArAis').disabled = false;
                  document.forms['frgrm'][xOpcion].value        = "SI";
                } else {
                  document.getElementById('cCliArAis').disabled = true;
                  document.forms['frgrm']['cCliArAis'].value    = "";
                  document.forms['frgrm'][xOpcion].value        = "NO";              
                }             
              break;
              default:
                if (document.forms['frgrm'][xOpcion].checked == true) {
                  document.forms['frgrm'][xOpcion].value = "SI";
                } else {
                  document.forms['frgrm'][xOpcion].value = "NO";              
                }             
              break;
            }
          }
        }
        
        function f_Valida_Pais(xPais,xCampo){
          switch(xCampo){
            case "cPaiId":
              if(xPais == 'CO'){
                 document.getElementById('IdDep').disabled = false;
                 document.getElementById('IdDep').href="javascript:f_Links('cDepId','WINDOW')";
                 document.forms['frgrm']['cDepId'].disabled = false;
                 document.getElementById('IdCiu').disabled=false;
                 document.getElementById('IdCiu').href="javascript:f_Links('cCiuId','WINDOW')";
                 document.forms['frgrm']['cCiuId'].disabled = false;
              }else{
                  document.getElementById('IdDep').disabled = true;
                  document.getElementById('IdDep').href="#";
                  document.forms['frgrm']['cDepId'].disabled = true;
                  document.getElementById('IdCiu').disabled  = true;
                  document.getElementById('IdCiu').href="#";
                  document.forms['frgrm']['cCiuId'].disabled = true;
                }
            break;
            case "cPaiId1":
            if(xPais == 'CO'){
                 document.getElementById('IdDep1').disabled=false;
                 document.getElementById('IdDep1').href="javascript:f_Links('cDepId1','WINDOW')";
                 document.forms['frgrm']['cDepId1'].disabled = false;
                 document.getElementById('IdCiu1').disabled=false;
                 document.getElementById('IdCiu1').href="javascript:f_Links('cCiuId1','WINDOW')";
                 document.forms['frgrm']['cCiuId1'].disabled = false;
              }else{
                  document.getElementById('IdDep1').disabled=true;
                  document.getElementById('IdDep1').href="#";
                  document.forms['frgrm']['cDepId1'].disabled = true;
                  document.getElementById('IdCiu1').disabled=true;
                  document.getElementById('IdCiu1').href="#";
                  document.forms['frgrm']['cCiuId1'].disabled = true;
               }
               break;
          }
        }
  	  </script>
  	</head>
  	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
      <center>
        <table border ="0" cellpadding="0" cellspacing="0" width="740">
          <tr>
            <td>
              <fieldset>
                <legend>Reporte <?php echo $_COOKIE['kProDes'] ?></legend>
                <form name = 'frgrm' action = 'frterreg.php' method = 'post' target='fmpro'>
                  <center>
                    <table border = '0' cellpadding = '0' cellspacing = '0' width='720'>  
                      <?php $zCol = f_Format_Cols(36);
                      echo $zCol;?>
                      <tr>
                        <td Class = "clase08" colspan = "10" height="25">Consultar Terceros que cumplan con:<br></td>
                        <td Class = "clase08" colspan = "9" height="25"><label><input type="radio" name = "oBuscar" onclick="javascript:f_Radio('cBuscar','OR');" onchange="javascript:f_Radio('cBuscar','OR');f_LimpiarConsulta();">Cualquier Criterio</label><br></td>
                        <td Class = "clase08" colspan = "17" height="25">
                            <input type="hidden" name="cBuscar" value="">
                            <label><input type="radio" name = "oBuscar" onclick="javascript:f_Radio('cBuscar','AND');" onchange="javascript:f_Radio('cBuscar','AND');f_LimpiarConsulta();">Todos de los Criterios</label><br>
                        </td>
                      </tr>
                      <tr>
                        <td Class = "clase08" colspan = "36">
                          <fieldset>
                            <legend>Clasificaci&oacute;n</legend>
                            <table border = '0' cellpadding = '0' cellspacing = '0' width='700'>  
                              <?php $zCol = f_Format_Cols(35);
                              echo $zCol;?>
                              <tr>
                                <td Class = "clase08" colspan = "9" height="25"><label><input type="checkbox" name = "vChCli"  value="NO" onclick="javascript:f_Check(this.checked,'vChCli');">Cliente</label><br></td>
                                <td Class = "clase08" colspan = "9" height="25"><label><input type="checkbox" name = "vChProC" value="NO" onclick="javascript:f_Check(this.checked,'vChProC');">Proveedor Cliente</label><br></td>
                                <td Class = "clase08" colspan = "9" height="25"><label><input type="checkbox" name = "vChProE" value="NO" onclick="javascript:f_Check(this.checked,'vChProE');">Proveedor Empresa</label><br></td>
                                <td Class = "clase08" colspan = "8" height="25"><label><input type="checkbox" name = "vChEmp"  value="NO" onclick="javascript:f_Check(this.checked,'vChEmp');">Empleado</label><br></td>
                              </tr>
                              <tr>
                                <td Class = "clase08" colspan = "9" height="25"><label><input type="checkbox" name = "vChSoc" value="NO" onclick="javascript:f_Check(this.checked,'vChSoc');" >Socio</label><br></td>
                                <td Class = "clase08" colspan = "9" height="25"><label><input type="checkbox" name = "vChEfi" value="NO" onclick="javascript:f_Check(this.checked,'vChEfi');">E.Financiera</label><br></td>
                                <td Class = "clase08" colspan = "9" height="25"><label><input type="checkbox" name = "vChOtr" value="NO" onclick="javascript:f_Check(this.checked,'vChOtr');">Otro</label><br></td>
                                <td Class = "clase08" colspan = "8" height="25"><label><input type="checkbox" name = "vChCliVenCo" value="NO" onclick="javascript:f_Check(this.checked,'vChCliVenCo');">Vendedor</label><br></td>
                              </tr>
                            </table> 
                          </fieldset>
                        </td>
                      </tr>
                      <tr>
                        <td Class = "clase08" colspan = "36">
                          <fieldset>
                            <legend>Datos Generales</legend>
                            <table border = '0' cellpadding = '0' cellspacing = '0' width='700'>  
                              <?php $zCol = f_Format_Cols(35);
                              echo $zCol;?>
                              <tr>
                                <td Class = "clase08" colspan = "9" height="25">Tipo de Persona</td>
                                <td Class = "clase08" colspan = "26">
                								  <select class="letrase" size="1" name="cTpeId" style = "width:520">
                                    <option value = "" selected>-- SELECCIONE --</option>
                										<option value = "PUBLICA" >ENTIDAD PUBLICA</option>
                										<option value = "JURIDICA">PERSONA JURIDICA</option>
                										<option value = "NATURAL" >PERSONA NATURAL</option>
                                  </select>
              									</td>
            									</tr>
            									<tr>
                                <td Class = "clase08" colspan = "9" height="25">
                                  <a href = "javascript:document.frgrm.cTdiId.value  = '';
                                                        document.frgrm.cTdiDes.value = '';
                                                        f_Valida_Dv(this.value);
                                                        f_Links('cTdiId','VALID');" id="IdTdi">Tipo de Documento</a>
                                </td>
                								<td Class = "clase08" colspan = "4">
                                  <input type = 'text' Class = 'letra' style = 'width:80' name = 'cTdiId'
                  							 				 onBlur = "javascript:f_Valida_Dv(this.value);
                  																						this.value=this.value.toUpperCase();
                  																		        f_Links('cTdiId','VALID');
                  																		        this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                  						 					 onFocus="javascript:f_Valida_Dv(this.value);
                  																	         document.frgrm.cTdiId.value  = '';
                																		  		   document.frgrm.cTdiDes.value = '';
                													                   this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                						 		</td>
                							 	<td Class = 'clase08' colspan = '22'>
                                  <input type = 'text' Class = 'letra' style = 'width:440' name = 'cTdiDes' readonly>
                                </td>
                              </tr>
              								<tr>
              								  <td Class = 'clase08' colspan = "9" height="25">No Identificaci&oacute;n</td>
                                <td Class = 'clase08' colspan = "23"> 
                                  <input type = 'text' Class = 'letra' style = 'width:460' name = "cTerId">
                                  <input type = 'hidden' name = "nTerDV">
              								  </td>
            										<td Class = 'clase08' colspan = "3">
            										  <label><input type="checkbox" name = "oExcTerId" value="NO" onclick="javascript:f_Check(this.checked,'oExcTerId');">Exacto</label>
            										</td>
            									</tr>
  														<tr>
                                <td Class = "clase08" colspan = "9" height="25">Nombre o Razon Social</td>
  															<td Class = "clase08" colspan = "23">
                                  <input type = "text" Class = "letra" name = "cTerNom" style = "width:460">
  															</td>
  															<td Class = 'clase08' colspan = "3">
                                  <label><input type="checkbox" name = "oExcTerNom" value="NO" onclick="javascript:f_Check(this.checked,'oExcTerNom');">Exacto</label>
                                </td>
  														</tr>
  														<tr>
                                <td Class = "clase08" colspan = "9" height="25">Nombre Comercial</td>
  															<td Class = "clase08" colspan = "23">
                                  <input type = "text" Class = "letra" name = "cTerNomC" style = "width:460">
                                </td>
                                <td Class = 'clase08' colspan = "3">
                                  <label><input type="checkbox" name = "oExcTerNomC" value="NO" onclick="javascript:f_Check(this.checked,'oExcTerNomC');">Exacto</label>
                                </td>
                              </tr>
          									  <tr>
                                <td Class = "clase08" colspan = "9" height="25">
          										 	  <a href = "javascript:document.forms['frgrm']['cPaiId'].value  = '';
            																			  		document.forms['frgrm']['cPaiDes'].value = '';
            																			  		document.forms['frgrm']['cDepId'].value  = '';
            																			  		document.forms['frgrm']['cDepDes'].value = '';
            																			  		document.forms['frgrm']['cCiuId'].value  = '';
            																			  		document.forms['frgrm']['cCiuDes'].value = '';
            																					  f_Links('cPaiId','VALID'); " id="IdPai">Pais Domicilio</a>
            										</td>
            										<td Class = "clase08" colspan = "4">
            												<input type = 'text' Class = 'letra' style = 'width:80' name = 'cPaiId'
            							 						onBlur = "javascript:this.value=this.value.toUpperCase();
            																			         f_Links('cPaiId','VALID');
            																			         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
            							 						onFocus = "javascript:document.forms['frgrm']['cPaiId'].value  = '';
                																			  	  document.forms['frgrm']['cPaiDes'].value = '';
                																			  	  document.forms['frgrm']['cDepId'].value  = '';
                																			  	  document.forms['frgrm']['cDepDes'].value = '';
                																			  	  document.forms['frgrm']['cCiuId'].value  = '';
                																			  	  document.forms['frgrm']['cCiuDes'].value = '';
          														                      this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
            										</td>
            										<td Class = "clase08" colspan = "22">
            										  <input type = 'text' Class = 'letra' style = 'width:440' name = 'cPaiDes' readonly>
            										</td>
            									</tr>
                              <tr>
                                <td Class = "clase08" colspan = "9" height="25">
          										 	  <a href = "javascript:document.forms['frgrm']['cDepId'].value  = '';
                                                        document.forms['frgrm']['cDepDes'].value = '';
                                                        document.forms['frgrm']['cCiuId'].value  = '';
                                                        document.forms['frgrm']['cCiuDes'].value = '';
                                                        f_Links('cDepId','WINDOW')" id="IdDep">Departamento Domicilio</a>
            										</td>
            										<td Class = "clase08" colspan = "4">
            											<input type = 'text' Class = 'letra' style = 'width:80' name = 'cDepId'
            							 						onBlur = "javascript:this.value=this.value.toUpperCase();
            																			         f_Links('cDepId','VALID');
            																			         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
            							 						onFocus = "javascript:document.forms['frgrm']['cDepId'].value  = '';
                																			  		document.forms['frgrm']['cDepDes'].value = '';
                																			  		document.forms['frgrm']['cCiuId'].value  = '';
                																			  		document.forms['frgrm']['cCiuDes'].value = '';
          														                      this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
  							 			          </td>
                                <td Class = "clase08" colspan = "22">
            											<input type = 'text' Class = 'letra' style = 'width:440' name = 'cDepDes' readonly>
            										</td>
            									</tr>
            									<tr>
            									  <td Class = "clase08" colspan = "9" height="25">
          										 	  <a href = "javascript:document.forms['frgrm']['cCiuId'].value  = '';
            																			  		document.forms['frgrm']['cCiuDes'].value = '';
            																					  f_Links('cCiuId','WINDOW')" id="IdCiu">Ciudad Domicilio</a>
            										</td>
            										<td Class = "clase08" colspan = "4">
            										  <input type = 'text' Class = 'letra' style = 'width:80' name = 'cCiuId'
            							 						   onBlur = "javascript:this.value=this.value.toUpperCase();
            																			         f_Links('cCiuId','VALID');
            																			         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
            							 						   onFocus = "javascript:document.forms['frgrm']['cCiuId'].value  = '';
            																			  		    document.forms['frgrm']['cCiuDes'].value = '';
          														                      this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
          										  </td>
                                <td Class = "clase08" colspan = "22">
                                  <input type = 'text' Class = 'letra' style = 'width:440' name = 'cCiuDes' readonly>
                                </td>
          										</tr>
          										<tr>
          										  <td Class = "clase08" colspan = "9" height="25">
                                  <a href = "javascript:document.frgrm.cGruId.value  = '';
                                                  document.frgrm.cGruDes.value = '';
                                                  f_Links('cGruId','VALID')" id="IdGru">Grupo de clientes</a>
                                </td>
                                <td Class = "clase08" colspan = "4">
                                  <input type = 'text' Class = 'letra' style = 'width:80' name = 'cGruId'
                                    onBlur = "javascript:this.value=this.value.toUpperCase();
                                                   f_Links('cGruId','VALID');
                                                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                    onFocus = "javascript:document.frgrm.cGruId.value  = '';
                                                    document.frgrm.cGruDes.value = '';
                                                    this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                                </td>
                                <td Class = "clase08" colspan = "22">
                                  <input type = 'text' Class = 'letra' style = 'width:440' name = 'cGruDes' readonly>
                                </td>
                              </tr>
                              <tr>
                                <td Class = "clase08" colspan = "9" height="25">
                                  <a href = "javascript:document.forms['frgrm']['cPaiId1'].value  = '';
                                                        document.forms['frgrm']['cPaiDes1'].value = '';
                                                        document.forms['frgrm']['cDepId1'].value  = '';
                                                        document.forms['frgrm']['cDepDes1'].value = '';
                                                        document.forms['frgrm']['cCiuId1'].value  = '';
                                                        document.forms['frgrm']['cCiuDes1'].value = '';
                                                        f_Links('cPaiId1','VALID');" id="IdPai1">Pais Correspondencia</a>
                                </td>
                                <td Class = "clase08" colspan = "4">
                                  <input type = 'text' Class = 'letra' style = 'width:80' name = 'cPaiId1'
                                    onBlur = "javascript:this.value=this.value.toUpperCase();
                                                        f_Links('cPaiId1','VALID');
                                                        this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                    onFocus = "javascript:document.forms['frgrm']['cPaiId1'].value  = '';
                                                        document.forms['frgrm']['cPaiDes1'].value = '';
                                                        document.forms['frgrm']['cDepId1'].value  = '';
                                                        document.forms['frgrm']['cDepDes1'].value = '';
                                                        document.forms['frgrm']['cCiuId1'].value  = '';
                                                        document.forms['frgrm']['cCiuDes1'].value = '';
                                                        this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                                </td>
                                <td Class = "clase08" colspan = "22">
                                  <input type = 'text' Class = 'letra' style = 'width:440' name = 'cPaiDes1' readonly>
                                </td>
                              </tr>
                              <tr>
                                <td Class = "clase08" colspan = "9" height="25">
                                  <a href = "javascript:document.forms['frgrm']['cDepId1'].value  = '';
                                                        document.forms['frgrm']['cDepDes1'].value = '';
                                                        document.forms['frgrm']['cCiuId1'].value  = '';
                                                        document.forms['frgrm']['cCiuDes1'].value = '';
                                                        f_Links('cDepId1','WINDOW')" id="IdDep1">Departamento Correspondencia</a>
                                </td>
                                <td Class = "clase08" colspan = "4">   
                                  <input type = 'text' Class = 'letra' style = 'width:80' name = 'cDepId1'
                                      onBlur = "javascript:this.value=this.value.toUpperCase();
                                                           f_Links('cDepId1','VALID');
                                                           this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                      onFocus = "javascript:document.forms['frgrm']['cDepId1'].value  = '';
                                                            document.forms['frgrm']['cDepDes1'].value = '';
                                                            document.forms['frgrm']['cCiuId1'].value  = '';
                                                            document.forms['frgrm']['cCiuDes1'].value = '';
                                                            this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                                </td>
                                <td Class = "clase08" colspan = "22">
                                  <input type = 'text' Class = 'letra' style = 'width:440' name = 'cDepDes1' readonly>
                                </td>
                              </tr>
                              <tr>
                                <td Class = "clase08" colspan = "9" height="25">
                                  <a href = "javascript:document.forms['frgrm']['cCiuId1'].value  = '';
                                                        document.forms['frgrm']['cCiuDes1'].value = '';
                                                        f_Links('cCiuId1','WINDOW')" id="IdCiu1">Ciudad Correspondencia</a>
                                </td>
                                <td Class = "clase08" colspan = "4">
                                  <input type = 'text' Class = 'letra' style = 'width:80' name = 'cCiuId1'
                                      onBlur = "javascript:this.value=this.value.toUpperCase();
                                                           f_Links('cCiuId1','VALID');
                                                           this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                      onFocus = "javascript:document.forms['frgrm']['cCiuId1'].value  = '';
                                                            document.forms['frgrm']['cCiuDes1'].value = '';
                                                            this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                                </td>
                                <td Class = "clase08" colspan = "22">  
                                  <input type = 'text' Class = 'letra' style = 'width:440' name = 'cCiuDes1' readonly>
                                </td>
                              </tr>
                              <tr>
                                <td Class = "clase08" colspan = "9" height="25">Forma de Pago</td>
                                <td Class = "clase08" colspan = "26">
                                  <select class="letrase" size="1" name="cTerFPa" style = "width:520">
                                    <option value = "" selected>-- SELECCIONE --</option>
                                    <option value = "CONTADO" >CONTADO</option>
                                    <option value = "CREDITO">CREDITO</option>
                                  </select>
                                </td>
                              </tr>
                              <tr>
                                <td Class = "clase08" colspan = "9" height="25">Medio de Pago</td>
                                <td Class = "clase08" colspan = "26">  
                                  <select class="letrase" size="1" name="cTerMedP" style = "width:520">
                                    <option value = "" selected>-- SELECCIONE --</option>
                                    <option value = "EFECTIVO" >EFECTIVO</option>
                                    <option value = "CHEQUE">CHEQUE</option>
                                    <option value = "TRANSFERENCIA">TRANSFERENCIA</option>
                                  </select>
                                </td>
                              </tr>
                              <tr>
                                <td Class = "clase08" colspan = "9" height="25">Estado</td>
                                <td Class = "clase08" colspan = "26">  
                                  <select class="letrase" size="1" name="cEstado" style = "width:520">
                                    <option value = "ACTIVO" selected>ACTIVO</option>
                                    <option value = "INACTIVO">INACTIVO</option>
                                    <option value = "">AMBOS</option>
                                  </select>
                                </td>
                              </tr>  
                            </table>
                          </fieldset>
                        </td>
                      </tr>
                      <tr>
                        <td Class = "clase08" colspan = "36">
                          <fieldset>
                            <legend>Responsabilidad Inscrita para el Tercero </legend>
                            <table border = '0' cellpadding = '0' cellspacing = '0' width='700'>  
                              <?php $zCol = f_Format_Cols(35);
                              echo $zCol;?>
                              <tr>
                                <td colspan="35">
                                  <center>
                                    <fieldset>
                                      <legend>Condiciones Tributarias</legend>
                                      <table border = '0' cellpadding = '0' cellspacing = '0' width='680'>
                                        <?php $zCol = f_Format_Cols(34);
                                        echo $zCol;?>
                                        <tr>
                                          <td Class = "clase08" colspan = "10" height="30"><label><input type="checkbox" name = "oCliReIva" onclick="javascript:f_Habilita(this.name);">Responsable IVA</label></td>
                                          <td Class = "clase08" colspan = "24" height="30">
                                            <label><input type="checkbox" name = "oCliReg" id="oCliReCom" value="" onclick="javascript:f_Habilita(this.id);">R&eacute;gimen com&uacute;n</label>
                                            &nbsp;<label><input type="checkbox" name = "oCliReg" id="oCliReSim" value="" onclick="javascript:f_Habilita(this.id);">R&eacute;gimen Simplificado (No Responsable IVA)</label>
                                          </td>
                                        </tr>
                                        <tr>
                                          <td Class = "clase08" colspan = "34" height="30"><label><input type="checkbox" name = "oCliGc" onclick="javascript:f_Habilita(this.name);">Gran Contribuyente</label></td>
                                        </tr>
                                        <tr>
                                          <td Class = "clase08" colspan = "10" height="30"><label><input type="checkbox" name = "oCliNrp" onclick="javascript:f_Habilita(this.name);">No Residente en el Pa&iacute;s</label></td>
                                          <td Class = "clase08" colspan = "24" height="30">
                                            <label><input type="checkbox" name = "oCliNrpai" id = "oCliNrpai" onclick="javascript:f_Habilita(this.name);">Aplica IVA</label>
                                            &nbsp;&nbsp;&nbsp;<label><input type="checkbox" name = "oCliNrpif" id = "oCliNrpif" onclick="javascript:f_Habilita(this.name);">Aplica Gravamen Financiero</label>
                                            &nbsp;&nbsp;&nbsp;<label><input type="checkbox" name = "oCliNrpNsr" id = "oCliNrpNsr" onclick="javascript:f_Habilita(this.name);">No Sujeto RETEFTE por Renta</label>
                                          </td>
                                        </tr>
                                        <tr>
                                          <td Class = "clase08" colspan = "10" height="30"><label><input type="checkbox" name = "oCliAr" onclick="javascript:f_Habilita(this.name);">Autorretenedor</label></td>
                                          <td Class = "clase08" colspan = "12" height="30">
                                            <label><input type="checkbox" name = "oCliArAre" id = "oCliArAre" onclick="javascript:f_Habilita(this.name);">Renta</label>
                                            &nbsp;<label><input type="checkbox" name = "oCliArAiv" id = "oCliArAiv" onclick="javascript:f_Habilita(this.name);">IVA</label>
                                            &nbsp;<label><input type="checkbox" name = "oCliArAic" id = "oCliArAic" onclick="javascript:f_Habilita(this.name);">ICA</label>
                                            &nbsp;<label><input type="checkbox" name = "oCliArAcr" id = "oCliArAcr" onclick="javascript:f_Habilita(this.name);">CREE</label>
                                          </td>
                                          <td Class = "clase08" colspan = "12" height="30">
                                            <select Class = "letrase" style = "width:120" name = "cCliArAis" id ="cCliArAis">
                                              <option value="">Ica x Sucursales</option>
                                              <?php //Busco sucrsales
                                                $qSucDes = "SELECT DISTINCT sucidxxx, sucdesxx FROM $cAlfa.fpar0008 WHERE regestxx = \"ACTIVO\" ORDER BY sucdesxx";
                                                $xSucDes  = f_MySql("SELECT","",$qSucDes,$xConexion01,"");
                                                while ($xRSD = mysql_fetch_array($xSucDes)){ ?>
                                                  <option value="<?php echo $xRSD['sucidxxx'] ?>"><?php echo $xRSD['sucdesxx'] ?></option>
                                              <?php } ?>   
                                            </select>
                                          </td>
                                        </tr>
                                        <tr>
                                          <td Class = "clase08" colspan = "34" height="30"><label><input type="checkbox" name = "oCliNsrr" onclick="javascript:f_Habilita(this.name);">No Sujeto RETEFTE por Renta</label></td>
                                        </tr>
                                        <tr>
                                          <td Class = "clase08" colspan = "34" height="30"><label><input type="checkbox" name = "oCliNsriv" onclick="javascript:f_Habilita(this.name);">No Sujeto RETEFTE por IVA</label></td>
                                        </tr>
                                        <tr>
                                          <td Class = "clase08" colspan = "34" height="30"><label><label><input type="checkbox" name = "oCliNsrcr" onclick="javascript:f_Habilita(this.name);">No Sujeto Retenci&oacute;n CREE</label></td>
                                        </tr>
                                        <tr>
                                          <td Class = "clase08" colspan = "34" height="30"><label><input type="checkbox" name = "oCliArr" onclick="javascript:f_Habilita(this.name);">Agente Retenedor en Renta</label></td>
                                        </tr>
                                        <tr>
                                          <td Class = "clase08" colspan = "34" height="30"><label><input type="checkbox" name = "oCliAriva" onclick="javascript:f_Habilita(this.name);">Agente Retenedor en IVA</label></td>
                                        </tr>
                                        <tr>
                                          <td Class = "clase08" colspan = "34" height="30"><label><input type="checkbox" name = "oCliArcr" onclick="javascript:f_Habilita(this.name);">Agente Retenedor CREE</label></td>
                                        </tr>
                                        <tr>
                                          <td Class = "clase08" colspan = "10" height="30"><label><input type="checkbox" name = "oCliArrI" id = "oCliArrI" onclick="javascript:f_Habilita(this.name);">Agente Retenedor ICA en</label></td>
                                          <td Class = "clase08" colspan = "24" height="30">
                                            <select Class = "letrase" style = "width:120" name = "cCliArrIs" id="cCliArrIs">
                                              <option value="">Ica x Sucursales</option>
                                              <?php //Busco sucrsales
                                                $qSucDes = "SELECT DISTINCT sucidxxx, sucdesxx FROM $cAlfa.fpar0008 WHERE regestxx = \"ACTIVO\" ORDER BY sucdesxx";
                                                $xSucDes  = f_MySql("SELECT","",$qSucDes,$xConexion01,"");
                                                while ($xRSD = mysql_fetch_array($xSucDes)){ ?>
                                                  <option value="<?php echo $xRSD['sucidxxx'] ?>"><?php echo $xRSD['sucdesxx'] ?></option>
                                              <?php } ?>   
                                            </select>
                                          </td>
                                        </tr>
                                        <tr>
                                          <td Class = "clase08" colspan = "34" height="30"><label><input type="checkbox" name = "oCliNsrri" onclick="javascript:f_Habilita(this.name);">No Sujeto a Retenci&oacute;n ICA</label></td>
                                        </tr>                                     
                                        <tr>
                                          <td Class = "clase08" colspan = "34" height="30"><label><input type="checkbox" name = "oCliPci" onclick="javascript:f_Habilita(this.name);">Proveedor Comercializadora Internacional</label></td>
                                        </tr>
                                        <tr>
                                          <td Class = "clase08" colspan = "34" height="30"><label><input type="checkbox" name = "oCliNsOfe">No Sujeto a Expedir Factura de Venta o Documento Equivalente</label></td>
                                        </tr>
                                      </table>
                                    </fieldset>
                                  </center>
                                </td>
                              </tr>                             
                            </table>
                          </fieldset>
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
  			<table border="0" cellpadding="0" cellspacing="0" width="740">
  				<tr height="21">
            <?php
            switch ($kMysqlDb) {
							case "SIACOSIA":
							case "DESIACOSIP":
							case "TESIACOSIP": ?>
                <td width="307" height="21"></td>
              <?php
              break;
              default: ?>
                <td width="467" height="21"></td>
              <?php
              break;
            } ?>
  					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_remove_bg.gif" style="cursor:pointer" onClick = "javascript:document.forms['frgrm']['oBuscar'][0].checked = true;f_Radio('cBuscar','OR');f_LimpiarConsulta();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Limpiar</td>
  					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:f_GenSql()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Generar</td>
            <?php
						switch ($kMysqlDb) {
							case "SIACOSIA":
							case "DESIACOSIP":
							case "TESIACOSIP": ?>
  					    <td width="160" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_new4-038_160.gif" style="cursor:pointer" onClick = "javascript:fnGenSqlReporteCuentasTerceros();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cuentas Terceros</td>
            <?php
              break;
            } ?>
  					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
  				</tr>
  			</table>
  		</center>
  		<script language = "javascript">
  		  document.forms['frgrm']['oBuscar'][0].checked = true;
        f_Radio('cBuscar','OR');
  		  f_LimpiarConsulta();
  		</script>
  		<br>
  	</body>
  </html>
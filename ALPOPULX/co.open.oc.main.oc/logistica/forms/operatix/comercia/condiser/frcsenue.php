<?php
  /**
   * Nuevo Condiciones de Servicio.
   * --- Descripcion: Permite Crear una Nueva Condicion de Servicio.
   * @author juan.trujillo@openits.co
   * @package opencomex
   * @version 001
   */

  include("../../../../../financiero/libs/php/utility.php");

  // Se calcula el consecutivo
  $nAnioActual = date('Y');
  $qCondServ  = "SELECT ";
  $qCondServ .= "cseidxxx, ";
  $qCondServ .= "csecscxx, ";
  $qCondServ .= "regfcrex ";
  $qCondServ .= "FROM $cAlfa.lpar0152 ";
  $qCondServ .= "WHERE ";
  $qCondServ .= "regfcrex LIKE \"$nAnioActual%\" ";
  $qCondServ .= "ORDER BY ABS(csecscxx) DESC ";
  $qCondServ .= "LIMIT 0,1";
  $xCondServ  = f_MySql("SELECT","",$qCondServ,$xConexion01,"");
  if (mysql_num_rows($xCondServ) > 0) {
    $vCondServ = mysql_fetch_array($xCondServ);

    $nAnioActual  = substr($nAnioActual, -2);
    $nConsecutivo = $vCondServ['csecscxx'] + 1;
    $cIdCondServ  = $nAnioActual . str_pad($nConsecutivo,4,"0",STR_PAD_LEFT);
  } else {
    $nAnioActual  = substr($nAnioActual, -2);
    $nConsecutivo = 1;
    $cIdCondServ  = $nAnioActual . str_pad("1",4,"0",STR_PAD_LEFT);
  }

?>
<html>
  <head>
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
    <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
    <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
    <script languaje = "javascript">
      function fnRetorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
      }

      function fnLinks(xLink,xSwitch,xCodOrgVenta='') {
        var zX    = screen.width;
        var zY    = screen.height;
        switch (xLink) {
          // Cliente
          case "cCliId":
            if (xSwitch == "VALID") {
              var zRuta  = "frcse150.php?gWhat=VALID&gFunction=cCliId&gCliId="+document.forms['frgrm']['cCliId'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frcse150.php?gWhat=WINDOW&gFunction=cCliId&gCliId="+document.forms['frgrm']['cCliId'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cCliNom":
            if (xSwitch == "VALID") {
              var zRuta  = "frcse150.php?gWhat=VALID&gFunction=cCliNom&gCliNom="+document.forms['frgrm']['cCliNom'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frcse150.php?gWhat=WINDOW&gFunction=cCliNom&gCliNom="+document.forms['frgrm']['cCliNom'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          // Condicion comercial
          case "cCcoIdOc":
            if (document.forms['frgrm']['cCliId'].value != "") {
              if (xSwitch == "VALID") {
                var zRuta  = "frcse151.php?gWhat=VALID" + 
                                          "&gFunction=cCcoIdOc" + 
                                          "&gCcoIdOc="+document.forms['frgrm']['cCcoIdOc'].value.toUpperCase() +
                                          "&gCliId="+document.forms['frgrm']['cCliId'].value;
                parent.fmpro.location = zRuta;
              } else {
                var zNx     = (zX-600)/2;
                var zNy     = (zY-250)/2;
                var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
                var zRuta   = "frcse151.php?gWhat=WINDOW" +
                                          "&gFunction=cCcoIdOc" + 
                                          "&gCcoIdOc="+document.forms['frgrm']['cCcoIdOc'].value.toUpperCase() +
                                          "&gCliId="+document.forms['frgrm']['cCliId'].value;
                zWindow = window.open(zRuta,"zWindow",zWinPro);
                zWindow.focus();
              }
            } else {
              alert('Debe seleccionar el cliente para poder cosultar las condiciones comerciales,\nVerifique.');
            }
          break;
          // Servicio
          case "cSerSap":
            if (xSwitch == "VALID") {
              var zRuta  = "frcse011.php?gWhat=VALID&gFunction=cSerSap&gSerSap="+document.forms['frgrm']['cSerSap'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frcse011.php?gWhat=WINDOW&gFunction=cSerSap&gSerSap="+document.forms['frgrm']['cSerSap'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cSerDes":
            if (xSwitch == "VALID") {
              var zRuta  = "frcse011.php?gWhat=VALID&gFunction=cSerDes&gSerDes="+document.forms['frgrm']['cSerDes'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frcse011.php?gWhat=WINDOW&gFunction=cSerDes&gSerDes="+document.forms['frgrm']['cSerDes'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          // Unidad Facturable
          case "cUfaId":
            if (xSwitch == "VALID") {
              var zRuta  = "frcse006.php?gWhat=VALID&gFunction=cUfaId&gUfaId="+document.forms['frgrm']['cUfaId'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frcse006.php?gWhat=WINDOW&gFunction=cUfaId&gUfaId="+document.forms['frgrm']['cUfaId'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cUfaDes":
            if (xSwitch == "VALID") {
              var zRuta  = "frcse006.php?gWhat=VALID&gFunction=cUfaDes&gUfaDes="+document.forms['frgrm']['cUfaDes'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frcse006.php?gWhat=WINDOW&gFunction=cUfaDes&gUfaDes="+document.forms['frgrm']['cUfaDes'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          // Objeto Facturable
          case "cObfId":
            if (xSwitch == "VALID") {
              var zRuta  = "frcse004.php?gWhat=VALID&gFunction=cObfId&gObfId="+document.forms['frgrm']['cObfId'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frcse004.php?gWhat=WINDOW&gFunction=cObfId&gObfId="+document.forms['frgrm']['cObfId'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cObfDes":
            if (xSwitch == "VALID") {
              var zRuta  = "frcse004.php?gWhat=VALID&gFunction=cObfDes&gObfDes="+document.forms['frgrm']['cObfDes'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frcse004.php?gWhat=WINDOW&gFunction=cObfDes&gObfDes="+document.forms['frgrm']['cObfDes'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          // Forma de Cobro
          case "cFcoId":
            if (xSwitch == "VALID") {
              var zRuta  = "frcse130.php?gWhat=VALID&gFunction=cFcoId&gFcoId="+document.forms['frgrm']['cFcoId'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frcse130.php?gWhat=WINDOW&gFunction=cFcoId&gFcoId="+document.forms['frgrm']['cFcoId'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cFcoDes":
            if (xSwitch == "VALID") {
              var zRuta  = "frcse130.php?gWhat=VALID&gFunction=cFcoDes&gFcoDes="+document.forms['frgrm']['cFcoDes'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frcse130.php?gWhat=WINDOW&gFunction=cFcoDes&gFcoDes="+document.forms['frgrm']['cFcoDes'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          // Subservicios
          case "cSubId":
            if (document.forms['frgrm']['cSerSap'].value != "") {
              if (xSwitch == "VALID") {
                var zRuta  = "frcse012.php?gWhat=VALID"+
                                          "&gFunction=cSubId"+
                                          "&gSerSap="+document.forms['frgrm']['cSerSap'].value.toUpperCase()+
                                          "&gSubId="+document.forms['frgrm']['cSubId'].value.toUpperCase();
                parent.fmpro.location = zRuta;
              } else {
                var zNx     = (zX-600)/2;
                var zNy     = (zY-250)/2;
                var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
                var zRuta   = "frcse012.php?gWhat=WINDOW"+
                                          "&gFunction=cSubId"+
                                          "&gSerSap="+document.forms['frgrm']['cSerSap'].value.toUpperCase()+
                                          "&gSubId="+document.forms['frgrm']['cSubId'].value.toUpperCase();
                zWindow = window.open(zRuta,"zWindow",zWinPro);
                zWindow.focus();
              }
            } else {
              alert('Debe seleccionar el servicio para poder cosultar los subservicios,\nVerifique.');
            }
          break;
          case 'cSubDes':
            if (document.forms['frgrm']['cSerSap'].value != "") {
              if (xSwitch == "VALID") {
                var zRuta  = "frcse012.php?gWhat=VALID&gFunction=cSerSap&gSubDes="+document.forms['frgrm']['cSerSap'].value.toUpperCase();
                parent.fmpro.location = zRuta;
              } else {
                var zNx     = (zX-600)/2;
                var zNy     = (zY-250)/2;
                var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
                var zRuta   = "frcse012.php?gWhat=WINDOW&gFunction=cSerSap&gSubDes="+document.forms['frgrm']['cSerSap'].value.toUpperCase();
                zWindow = window.open(zRuta,"zWindow",zWinPro);
                zWindow.focus();
              }
            } else {
              alert('Debe seleccionar el servicio para poder cosultar los subservicios,\nVerifique.');
            }
          break;
          // Organizacion de Venta
          case 'cOrganizacionVenta':
            var zNx     = (zX-580)/2;
            var zNy     = (zY-500)/2;
            var zWinPro = 'width=580,scrollbars=1,height=500,left='+zNx+',top='+zNy;
            var zRuta   = 'frcseovn.php?&gCseOrgVenta='+document.forms['frgrm']['cCseOrgVenta'].value +
                                        '&gTipo=1';

            zWindow2    = window.open(zRuta,'zWindow2',zWinPro);
            zWindow2.focus();
          break;
          // Oficina de Venta
          case 'cOficinanVenta':
            var zNx     = (zX-580)/2;
            var zNy     = (zY-500)/2;
            var zWinPro = 'width=580,scrollbars=1,height=500,left='+zNx+',top='+zNy;
            var zRuta   = 'frcseovn.php?&gCseOfiVenta='+document.forms['frgrm']['cCseOfiVenta_'+xCodOrgVenta].value +
                                       '&gCodOrgVenta='+xCodOrgVenta +
                                       '&gTipo=2';

            zWindow2    = window.open(zRuta,'zWindow2',zWinPro);
            zWindow2.focus();
          break;
        }
      }

      function fnCargarGrillas() {
        var cParametro = "1^"+document.forms['frgrm']['cCseOrgVenta'].value;

        var cRuta = "frcsegri.php?gParametro="+cParametro;

        parent.fmpro.location = cRuta;
      }

      function fnCargarOrganizacionVentas() {
        var cRuta = "frcsegri.php?gTipo=1&gCseId=<?php echo $cCseId ?>" +
                                 "&gCseOrgVenta="+document.forms['frgrm']['cCseOrgVenta'].value;
        parent.fmpro.location = cRuta;
      }

      function fnCargarOficinaVentas() {
        var cCodRuta = '';
        var cCodigos = document.forms['frgrm']['cCseOrgVenta'].value;
        
        if (cCodigos != "") {
          var mCodigos = cCodigos.split('~');

          for (let i = 0; i < mCodigos.length; i++) {
            if (mCodigos[i] != "") {
              if (document.forms['frgrm']['cCseOfiVenta_'+mCodigos[i]].value != undefined) {
                var cCodigosOficina = document.forms['frgrm']['cCseOfiVenta_'+mCodigos[i]].value;
                cCodRuta += cCodigosOficina+"|";
              }
            }
          }
        }

        var cRuta = "frcsegri.php?gTipo=2&gCseId=<?php echo $cCseId ?>" +
                                "&gCseOrgVenta="+document.forms['frgrm']['cCseOrgVenta'].value +
                                "&gCseOfiVenta="+cCodRuta;
        parent.fmpro.location = cRuta;
      }


      function fnEliminarOrganizacionVenta(valor) {
        if (confirm('ELIMINAR LA ORGANIZACION DE VENTA '+valor+'?')) {
          var ruta = "frcseovg.php?cCseId=<?php echo $cCseId ?>&tipsave=2&cIntId="+valor+"&cCseOrgVenta="+document.forms['frgrm']['cCseOrgVenta'].value;
          parent.fmpro.location = ruta;
        }
      }

      function fnEliminarOficiaVenta(valor,xCodOrg) {
        if (confirm('ELIMINAR LA OFICINA DE VENTA '+valor+'?')) {
          var cCodRuta = '';
        
          if (document.forms['frgrm']['cCseOfiVenta_'+xCodOrg].value != undefined) {
            var cCodigosOficina = document.forms['frgrm']['cCseOfiVenta_'+xCodOrg].value;
            cCodRuta = cCodigosOficina;
          }

          var ruta = "frcseovg.php?cCseId=<?php echo $cCseId ?>" +
                                  "&tipsave=4" +
                                  "&cIntId="+valor + 
                                  "&cCseOrgVenta="+xCodOrg +
                                  "&cCseOfiVenta="+cCodRuta;

          parent.fmpro.location = ruta;
        }
      }

      function fnAplicaCalculo(value) {
        document.getElementById('idOficinasVentas').innerHTML      = '';
        document.getElementById('idInputOficinasVentas').innerHTML = '';
        document.forms['frgrm']['cCseOrgVenta'].value              = "";

        if (value.checked == true) {
          document.getElementById('idOrganizacion').style.display = "none";
          document.getElementById('idOficina').style.display      = "none";
        } else {
          document.getElementById('idOrganizacion').style.display = "block";
          document.getElementById('idOficina').style.display      = "block";
          document.getElementById('idOrganizacion').style         = "width:600";
          document.getElementById('idOficina').style              = "width:600";
          fnCargarOrganizacionVentas();
        }
      }

      function fnOcultarMostrarTarifa(xTarifa) {
        switch (xTarifa) {
          case "000":
            //  Se ocultan los dos elementos Fieldset que identifican la tarifa
            document.getElementById("Tarifa_001").style.display = "none";
            document.getElementById("Tarifa_002").style.display = "none";
            document.getElementById("Tarifa_003").style.display = "none";
            document.getElementById("Tarifa_004").style.display = "none";
            document.getElementById("Tarifa_005").style.display = "none";
            document.getElementById("Tarifa_006").style.display = "none";
            document.getElementById("Tarifa_007").style.display = "none";
            document.getElementById("Tarifa_008").style.display = "none";
            document.getElementById("Tarifa_009").style.display = "none";
            document.getElementById("Tarifa_010").style.display = "none";
          break;
          case "001":
            document.forms['frgrm']['nVlrFi001'].value = "";
            document.getElementById("Tarifa_001").style.display = "block";
          break;
          case "002":
            document.forms['frgrm']['nTarifa002'].value = "";
            document.forms['frgrm']['nMini002'].value   = "";
            document.getElementById("Tarifa_002").style.display = "block";
          break;
          case "003":
            document.forms['frgrm']['nNivel003'].value = "";
            document.getElementById("Tarifa_003").style.display = "block";
          break;
          case "004":
            document.forms['frgrm']['nUnidMin004'].value = "";
            document.forms['frgrm']['nNivel004'].value   = "";
            document.getElementById("Tarifa_004").style.display = "block";
          break;
          case "005":
            document.forms['frgrm']['nNivel005'].value = "";
            document.getElementById("Tarifa_005").style.display = "block";
          break;
          case "006":
            document.forms['frgrm']['nVlrMin006'].value = "";
            document.forms['frgrm']['nNivel006'].value  = "";
            document.getElementById("Tarifa_006").style.display = "block";
          break;
          case "007":
            document.forms['frgrm']['nNivel007'].value = "";
            document.getElementById("Tarifa_007").style.display = "block";
          break;
          case "008":
            document.forms['frgrm']['nTarFi008'].value = "";
            document.getElementById("Tarifa_008").style.display = "block";
          break;
          case "009":
            document.forms['frgrm']['nPorcen009'].value = "";
            document.getElementById("Tarifa_009").style.display = "block";
          break;
          case "010":
            document.forms['frgrm']['nPorcen010'].value = "";
            document.forms['frgrm']['nMini010'].value   = "";
            document.getElementById("Tarifa_010").style.display = "block";
          break;
        }
      }

      function fnGridNiveles(xTarVal) {
        var tb2 = document.getElementById('Grid_Niveles_'+xTarVal);
        var lastRow = tb2.rows.length;
        if (lastRow==0){
          var niveles=document.forms['frgrm']['nNivel'+xTarVal].value;
          for(i=0; i<niveles; i++){
            var tbl       = document.getElementById('Grid_Niveles_'+xTarVal);
            var lastRow   = tbl.rows.length;
            var iteration = lastRow+1;
            var TR        = tbl.insertRow(lastRow);
            var lRow      = iteration-1;
            var vTarLiIn  = 'vTarLiIn' + iteration;
            var vTarLiSu  = 'vTarLiSu' + iteration;
            var vTarVlr   = 'vTarVlr'  + iteration;
            var vTarEst   = 'vTarEst'  + iteration;
            var vBtnFC    = 'vBtnFC'   + iteration;
            var TD_xAll   = TR.insertCell(0);
            TD_xAll.innerHTML = "<input type = 'text'   Class = 'letra' style = 'width:120;text-align:right' name = "+vTarLiIn+">"+
                                "<input type = 'text'   Class = 'letra' style = 'width:120;text-align:right' name = "+vTarLiSu+" >"+
                                "<input type = 'text'   Class = 'letra' style = 'width:120;text-align:right' name = "+vTarVlr+" onBlur = \"javascript:f_FixFloat(this);\" >";

            document.forms['frgrm']['gIdFC'].value = iteration;
            if(i==niveles-1){
              document.forms['frgrm']['vTarLiSu'+(i+1)].readOnly=true;
            }
            if(i==0){
              document.forms['frgrm']['vTarLiIn'+(i+1)].value="0";
              document.forms['frgrm']['vTarLiIn'+(i+1)].readOnly=true;
            }
          }
        }else{
          alert("Limpie la Grilla para Generar una Nueva.")
        }
      }

      function fnDeleteRowGrid(xTarVal) {
				var tbl = document.getElementById('Grid_Niveles_'+xTarVal);
				var lastRow = tbl.rows.length;
				var lastRow1 = tbl.rows.length;
				if(lastRow1>0){
					for(i=0; i<lastRow1; i++){
					  tbl.deleteRow(lastRow - 1);
				    document.forms['frgrm']['gIdFC'].value = lastRow - 1;
				    lastRow=lastRow-1;
				  }
				  document.forms['frgrm']['nNivel003'].value=0;
				  document.forms['frgrm']['nNivel004'].value=0;
				  document.forms['frgrm']['nNivel005'].value=0;
				  document.forms['frgrm']['nNivel006'].value=0;
				  document.forms['frgrm']['nNivel007'].value=0;
				}
	    }

      function fnGridVerNiveles(xLimIn,xLimSup,xPorcen,xTarVal) {
			  var tbl       = document.getElementById('Grid_Niveles_'+xTarVal);
			  var lastRow   = tbl.rows.length;

			  var iteration = lastRow+1;
			  var TR        = tbl.insertRow(lastRow);
			  var lRow      = iteration-1;
			  var vTarLiIn  = 'vTarLiIn' + iteration;
   			var vTarLiSu  = 'vTarLiSu' + iteration;
   			var vTarVlr   = 'vTarVlr'  + iteration;
		    var vTarEst   = 'vTarEst'  + iteration;
			  var vBtnFC    = 'vBtnFC'   + iteration;
			  var TD_xAll   = TR.insertCell(0);
   				  TD_xAll.innerHTML = "<input type = 'text'   Class = 'letra' style = 'width:120;text-align:right' name = "+vTarLiIn+" value="+xLimIn+">"+
   					  								  "<input type = 'text'   Class = 'letra' style = 'width:120;text-align:right' name = "+vTarLiSu+" value="+xLimSup+">"+
   					  								  "<input type = 'text'   Class = 'letra' style = 'width:120;text-align:right' name = "+vTarVlr+" value="+xPorcen+" onBlur = \"javascript:f_FixFloat(this);\" >";
   														  "<input type = 'text'   Class = 'letra' style = 'width:060' name = "+vTarEst+" onKeyUp = 'javascript:uEnter(this.event,this.name)'>"+
   														  "<input type = 'button' Class = 'letra' style = 'width:020' name = "+vBtnFC+"  value = '..' onClick = 'javascript:fnDeleteRowGrid(this.value)'>";
   			document.forms['frgrm']['gIdFC'].value = iteration;

   			var niveles=document.forms['frgrm']['nNivel'+xTarVal].value;
   			if (iteration == niveles) {
   			  //alert(niveles);
   			  document.forms['frgrm']['vTarLiSu'+niveles].readOnly=true;
   			}
      }

      function uEnter(e,xName) {
				var code;
				if (!e) {
					var e = window.event;
				}
				if (e.keyCode) {
					code = e.keyCode;
				} else {
					if (e.which) {
						code = e.which;
					}
				}
				if (code == 13){
					if (xName == 'vTarEst'+eval(document.forms['frgrm']['gIdFC'].value)) {
						fnGridNiveles();
					}
				}
			}
    </script>
  </head>
  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
    <center>
      <table border="0" cellpadding="0" cellspacing="0" width="600">
        <tr>
          <td>
            <fieldset>
              <legend><?php echo $_COOKIE['kMenDes'] ?></legend>
              <form name = 'frgrm' action = 'frcsegra.php' method = 'post' target='fmpro'>
						 	  <input type = "hidden" name = "gIdFC"    value = "0">
                <center>
                  <table border="0" cellpadding="0" cellspacing="0" width="600">
                    <?php $nCol = f_Format_Cols(30); echo $nCol; ?>
                    <!-- Seccion 1 -->
                    <tr>
                      <td class="clase08" colspan="4">Id<br>
                        <input type = 'text' Class = 'letra' style = 'width:80' name = "cCseId" value="<?php echo $cIdCondServ ?>" readonly>
                        <input type = 'hidden' Class = 'letra' style = 'width:80' name = "cCseCsc" value="<?php echo $nConsecutivo ?>">
                      </td>
                      <td Class = "clase08" colspan="5">
                        <a href = "javascript:document.forms['frgrm']['cCliId'].value  = '';
                                              document.forms['frgrm']['cCliNom'].value = '';
                                              document.forms['frgrm']['cCliDV'].value  = '';
                                              document.forms['frgrm']['cCliSap'].value = '';
                                              document.forms['frgrm']['cCcoIdOc'].value = '';
                                              fnLinks('cCliId','VALID')" id = "lCliId">Nit</a><br>
                        <input type = 'text' Class = 'letra' style = 'width:100' name = 'cCliId' maxlength="20"
                          onBlur = "javascript:this.value=this.value.toUpperCase();
                                              fnLinks('cCliId','VALID');
                                              this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                          onFocus = "javascript:document.forms['frgrm']['cCliId'].value  = '';
                                                document.forms['frgrm']['cCliNom'].value = '';
                                                document.forms['frgrm']['cCliDV'].value  = '';
                                                document.forms['frgrm']['cCliSap'].value = '';
                                                document.forms['frgrm']['cCcoIdOc'].value = '';
                                                this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                      <td class="clase08" colspan="1">Dv<br>
                        <input type = "text" Class = "letra" style = "width:020;text-align:center" name = "cCliDV" readonly>
                      </td>
                      <td class="clase08" colspan="10">Cliente<br>
                        <input type = 'text' Class = 'letra' style = 'width:200' name = "cCliNom"
                          onBlur = "javascript:this.value=this.value.toUpperCase();
                                                fnLinks('cCliNom','VALID');
                                                this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                          onFocus="javascript:document.forms['frgrm']['cCliId'].value  ='';
                                              document.forms['frgrm']['cCliNom'].value = '';
                                              document.forms['frgrm']['cCliDV'].value  = '';
                                              document.forms['frgrm']['cCliSap'].value = '';
                                              document.forms['frgrm']['cCcoIdOc'].value = '';
                                              this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                      <td class="clase08" colspan="4">C&oacute;digo SAP<br>
                        <input type = 'text' Class = 'letra' style = 'width:80' name = "cCliSap" readonly>
                      </td>
                      <td class="clase08" colspan="6">
                        <a href = "javascript:document.forms['frgrm']['cCcoIdOc'].value = '';
                                              fnLinks('cCcoIdOc','VALID')" id="idCcoIdOf">Condici&oacute;n Comercial</a><br>
                          <input type = 'text' Class = 'letra' style = 'width:120' name = 'cCcoIdOc'
                            onBlur = "javascript:fnLinks('cCcoIdOc','VALID');
                                                this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                            onFocus="javascript:document.forms['frgrm']['cCcoIdOc'].value = '';
                                                this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                    </tr>
                    <!-- Seccion 2 -->
                    <tr>
                      <td Class = "clase08" colspan="9">
                          <a href = "javascript:document.frgrm.cSerSap.value  = '';
                                                document.frgrm.cSerDes.value = '';
                                                fnLinks('cSerSap','VALID');" id = "idSerSap">C&oacute;digo SAP</a><br>
                          <input type = 'text' Class = 'letra' style = 'width:180' name = 'cSerSap'
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                fnLinks('cSerSap','VALID');
                                                this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                            onFocus="javascript:document.frgrm.cSerSap.value = '';
                                                document.frgrm.cSerDes.value = '';
                                                this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                      <td class="clase08" colspan="1">&nbsp;<br>
                        <input type = "text" Class = "letra" style = "width:020;text-align:center" readonly>
                      </td>
                      <td class="clase08" colspan="20">Servicio<br>
                        <input type = 'text' Class = 'letra' style = 'width:400' name = "cSerDes"
                          onBlur = "javascript:this.value=this.value.toUpperCase();
                                              fnLinks('cSerDes','VALID');
                                              this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                          onFocus="javascript:document.forms['frgrm']['cSerSap'].value ='';
                                              document.forms['frgrm']['cSerDes'].value = '';
                                              this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                    </tr>
                    <!-- Seccion 3 -->
                    <tr>
                      <td Class = "clase08" colspan="30">
                        <fieldset>
                          <legend>Subservicio</legend>
                          <table border = '0' cellpadding = '0' cellspacing = '0' width='560'>
                            <?php $zCol = f_Format_Cols(28);
                            echo $zCol;?>
                            <tr>
                              <td Class = "clase08" colspan="8">
                                  <a href = "javascript:document.frgrm.cSubId.value  = '';
                                                        document.frgrm.cSubDes.value = '';
                                                        fnLinks('cSubId','VALID');" id = "idSubId">Id</a><br>
                                  <input type = 'text' Class = 'letra' style = 'width:160' name = 'cSubId'
                                    onBlur = "javascript:this.value=this.value.toUpperCase();
                                                        fnLinks('cSubId','VALID');
                                                        this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                    onFocus="javascript:document.frgrm.cSubId.value  = '';
                                                        document.frgrm.cSubDes.value = '';
                                                        this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                              </td>
                              <td class="clase08" colspan="1">&nbsp;<br>
                                <input type = "text" Class = "letra" style = "width:020;text-align:center" readonly>
                              </td>
                              <td class="clase08" colspan="19">Subservicio<br>
                                <input type = 'text' Class = 'letra' style = 'width:390' name = "cSubDes"
                                  onBlur = "javascript:this.value=this.value.toUpperCase();
                                                      fnLinks('cSubDes','VALID');
                                                      this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                  onFocus="javascript:document.forms['frgrm']['cUfaId'].value ='';
                                                      document.forms['frgrm']['cSubDes'].value = '';
                                                      this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                              </td>
                            </tr>
                          </table>
                        </fieldset>
                      </td>
                    </tr>
                    <!-- Seccion 4 -->
                    <tr>
                      <td Class = "clase08" colspan="30">
                        <fieldset>
                          <legend>C&aacute;lculo</legend>
                          <table border = '0' cellpadding = '0' cellspacing = '0' width='560'>
                            <?php $zCol = f_Format_Cols(28);
                            echo $zCol;?>
                            <tr>
                              <td Class = "clase08" colspan="8">
                                  <a href = "javascript:document.frgrm.cUfaId.value  = '';
                                                        document.frgrm.cUfaDes.value = '';
                                                        fnLinks('cUfaId','VALID');" id = "idUfId">Id</a><br>
                                  <input type = 'text' Class = 'letra' style = 'width:160' name = 'cUfaId'
                                    onBlur = "javascript:this.value=this.value.toUpperCase();
                                                        fnLinks('cUfaId','VALID');
                                                        this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                    onFocus="javascript:document.frgrm.cUfaId.value  = '';
                                                        document.frgrm.cUfaDes.value = '';
                                                        this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                              </td>
                              <td class="clase08" colspan="1">&nbsp;<br>
                                <input type = "text" Class = "letra" style = "width:020;text-align:center" readonly>
                              </td>
                              <td class="clase08" colspan="19">Unidad Facturable<br>
                                <input type = 'text' Class = 'letra' style = 'width:390' name = "cUfaDes"
                                  onBlur = "javascript:this.value=this.value.toUpperCase();
                                                      fnLinks('cUfaDes','VALID');
                                                      this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                  onFocus="javascript:document.forms['frgrm']['cUfaId'].value ='';
                                                      document.forms['frgrm']['cUfaDes'].value = '';
                                                      this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                              </td>
                            </tr>
                            <tr>
                              <td Class = "clase08" colspan="8">
                                  <a href = "javascript:document.frgrm.cObfId.value  = '';
                                                        document.frgrm.cObfDes.value = '';
                                                        fnLinks('cObfId','VALID');" id = "idObfId">Id</a><br>
                                  <input type = 'text' Class = 'letra' style = 'width:160' name = 'cObfId'
                                    onBlur = "javascript:this.value=this.value.toUpperCase();
                                                        fnLinks('cObfId','VALID');
                                                        this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                    onFocus="javascript:document.frgrm.cObfId.value  = '';
                                                        document.frgrm.cObfDes.value = '';
                                                        this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                              </td>
                              <td class="clase08" colspan="1">&nbsp;<br>
                                <input type = "text" Class = "letra" style = "width:020;text-align:center" readonly>
                              </td>
                              <td class="clase08" colspan="19">Objeto Facturable<br>
                                <input type = 'text' Class = 'letra' style = 'width:390' name = "cObfDes"
                                  onBlur = "javascript:this.value=this.value.toUpperCase();
                                                      fnLinks('cObfDes','VALID');
                                                      this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                  onFocus="javascript:document.forms['frgrm']['cObfId'].value ='';
                                                      document.forms['frgrm']['cObfDes'].value = '';
                                                      this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                              </td>
                            </tr>
                          </table>
                        </fieldset>
                      </td>
                    </tr>
                    <!-- Seccion 5 -->
                    <tr>
                      <td Class = "clase08" colspan="30">
                        <fieldset>
                          <legend>Forma de Cobro</legend>
                          <table border = '0' cellpadding = '0' cellspacing = '0' width='560'>
                            <?php $zCol = f_Format_Cols(28);
                            echo $zCol;?>
                            <tr>
                              <td Class = "clase08" colspan="8">
                                  <a href = "javascript:document.frgrm.cFcoId.value  = '';
                                                        document.frgrm.cFcoDes.value = '';
                                                        fnLinks('cFcoId','VALID');
                                                        fnOcultarMostrarTarifa('000');" id = "idFcoId">Id</a><br>
                                  <input type = 'text' Class = 'letra' style = 'width:160' name = 'cFcoId'
                                    onBlur = "javascript:this.value=this.value.toUpperCase();
                                                        fnLinks('cFcoId','VALID');
                                                        fnOcultarMostrarTarifa('000');
                                                        this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                    onFocus="javascript:document.frgrm.cFcoId.value  = '';
                                                        document.frgrm.cFcoDes.value = '';
                                                        this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                              </td>
                              <td class="clase08" colspan="1">&nbsp;<br>
                                <input type = "text" Class = "letra" style = "width:020;text-align:center" readonly>
                              </td>
                              <td class="clase08" colspan="19">Forma de Cobro<br>
                                <input type = 'text' Class = 'letra' style = 'width:390' name = "cFcoDes"
                                  onBlur = "javascript:this.value=this.value.toUpperCase();
                                                      fnLinks('cFcoDes','VALID');
                                                      fnOcultarMostrarTarifa('000');
                                                      this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                  onFocus="javascript:document.forms['frgrm']['cFcoId'].value ='';
                                                      document.forms['frgrm']['cFcoDes'].value = '';
                                                      this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                              </td>
                            </tr>
                          </table>
                        </fieldset>
                      </td>
                    </tr>
                    <!-- Seccion 6 -->
                    <tr>
                      <td Class = "clase08" colspan="30">
                        <fieldset id="Tarifa_001">
                          <legend><b>Valor Fijo</b></legend>
                            <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:560">
                              <?php $zCol = f_Format_Cols(28); echo $zCol; ?>
                              <tr>
                                <td Class = "name" colspan = "4">Valor Fijo<br>
                                  <input type = "text" Class = "letra" style = "width:080;text-align:right" name = "nVlrFi001" value = "">
                                </td>
                              </tr>
                            </table>
                        </fieldset>

                        <fieldset id="Tarifa_002">
                          <legend><b>Tarifa por Cantidad con M&iacute;nima</b></legend>
                            <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:560">
                              <?php $zCol = f_Format_Cols(28); echo $zCol; ?>
                              <tr>
                                <td Class = "name" colspan = "4">Tarifa<br>
                                  <input type = "text" Class = "letra" style = "width:080;text-align:right" name = "nTarifa002" value = "">
                                </td>
                                <td Class = "name" colspan = "4">M&iacute;nima<br>
                                  <input type = "text" Class = "letra" style = "width:080;text-align:right" name = "nMini002" value = ""
                                    onkeyup="javascript:f_FixFloat(this);this.value=Math.round(this.value);"
                                    onblur="javascript:f_FixFloat(this);this.value=Math.round(this.value);">
                                </td>
                              </tr>
                            </table>
                        </fieldset>

                        <fieldset id="Tarifa_003">
                          <legend><b>Tarifa Escalonada por Unidad</b></legend>
                            <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:560">
                              <?php $zCol = f_Format_Cols(28); echo $zCol; ?>
                              <tr>
                                <td Class = "name" colspan = "17">Niveles <br>
                                  <input type = "text" Class = "letra" style = "width:080;text-align:right" name = "nNivel003" value = ""
                                    onblur="javascript:f_FixFloat(this);" >
                                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type=button value="Generar" name="generar003" onclick="javascript:fnGridNiveles('003');" id="generar003">&nbsp;&nbsp;&nbsp;
                                    <input type=button value="Limpiar" name="limpiar003" onclick="javascript:fnDeleteRowGrid('003');" id="limpiar003">
                                </td>
                              </tr>
                            </table>

                            <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:560">
                              <?php $zCol = f_Format_Cols(28); echo $zCol; ?>
                              <tr>
                                <td Class = "name" colspan = "7" align="left"><br>
                                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rango Inferior
                                </td>
                                <td Class = "name" colspan = "6" align="left"><br>
                                  Rango Superior
                                </td>
                                <td Class = "name" colspan = "8" align="left"><br>
                                  &nbsp;&nbsp;&nbsp;Tarifa
                                </td>
                              </tr>
                            </table>
                            <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:560" id = "Grid_Niveles_003"></table>
                        </fieldset>

                        <fieldset id="Tarifa_004">
                          <legend><b>Tarifa Escalonada con M&iacute;nima por Unidad</b></legend>
                            <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:560">
                              <?php $zCol = f_Format_Cols(28); echo $zCol; ?>
                              <tr>
                                <td Class = "name" colspan = "6">Unidades Min<br>
                                  <input type = "text" Class = "letra" style = "width:080;text-align:right" name = "nUnidMin004" value="">
                                </td>
                                <td Class = "name" colspan = "17">Niveles <br>
                                  <input type = "text" Class = "letra" style = "width:080;text-align:right" name = "nNivel004" value=""
                                    onblur="javascript:f_FixFloat(this);" >
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type=button value="Generar" name="generar004" onclick="javascript:fnGridNiveles('004');" id="generar004">&nbsp;&nbsp;&nbsp;
                                    <input type=button value="Limpiar" name="limpiar004" onclick="javascript:fnDeleteRowGrid('004');" id="limpiar004">
                                </td>
                              </tr>
                            </table>

                            <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:560">
                              <?php $zCol = f_Format_Cols(28); echo $zCol; ?>
                              <tr>
                                <td Class = "name" colspan = "7" align="left"><br>
                                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rango Inferior
                                </td>
                                <td Class = "name" colspan = "6" align="left"><br>
                                  Rango Superior
                                </td>
                                <td Class = "name" colspan = "8" align="left"><br>
                                  &nbsp;&nbsp;&nbsp;Tarifa
                                </td>
                              </tr>
                            </table>
                            <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:560" id = "Grid_Niveles_004"></table>
                        </fieldset>

                        <fieldset id="Tarifa_005">
                          <legend><b>Tarifa Escalonada por Valor</b></legend>
                            <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:560">
                              <?php $zCol = f_Format_Cols(28); echo $zCol; ?>
                              <tr>
                                <td Class = "name" colspan = "17">Niveles <br>
                                  <input type = "text" Class = "letra" style = "width:080;text-align:right" name = "nNivel005" value=""
                                    onblur="javascript:f_FixFloat(this);" >
                                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type=button value="Generar" name="generar005" onclick="javascript:fnGridNiveles('005');" id="generar005">&nbsp;&nbsp;&nbsp;
                                    <input type=button value="Limpiar" name="limpiar005" onclick="javascript:fnDeleteRowGrid('005');" id="limpiar005">
                                </td>
                              </tr>
                            </table>

                            <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:560">
                              <?php $zCol = f_Format_Cols(28); echo $zCol; ?>
                              <tr>
                                <td Class = "name" colspan = "7" align="left"><br>
                                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rango Inferior
                                </td>
                                <td Class = "name" colspan = "6" align="left"><br>
                                  Rango Superior
                                </td>
                                <td Class = "name" colspan = "8" align="left"><br>
                                  &nbsp;&nbsp;&nbsp;Tarifa
                                </td>
                              </tr>
                            </table>
                            <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:560" id = "Grid_Niveles_005"></table>
                        </fieldset>

                        <fieldset id="Tarifa_006">
                          <legend><b>Tarifa Escalonada con M&iacute;nima por Valor</b></legend>
                            <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:560">
                              <?php $zCol = f_Format_Cols(28); echo $zCol; ?>
                              <tr>
                                <td Class = "name" colspan = "6">Valor Min<br>
                                  <input type = "text" Class = "letra" style = "width:080;text-align:right" name = "nVlrMin006" value="">
                                </td>
                                <td Class = "name" colspan = "17">Niveles <br>
                                  <input type = "text" Class = "letra" style = "width:080;text-align:right" name = "nNivel006" value=""
                                    onblur="javascript:f_FixFloat(this);" >
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type=button value="Generar" name="generar006" onclick="javascript:fnGridNiveles('006');" id="generar006">&nbsp;&nbsp;&nbsp;
                                    <input type=button value="Limpiar" name="limpiar006" onclick="javascript:fnDeleteRowGrid('006');" id="limpiar006">
                                </td>
                              </tr>
                            </table>

                            <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:560">
                              <?php $zCol = f_Format_Cols(28); echo $zCol; ?>
                              <tr>
                                <td Class = "name" colspan = "7" align="left"><br>
                                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rango Inferior
                                </td>
                                <td Class = "name" colspan = "6" align="left"><br>
                                  Rango Superior
                                </td>
                                <td Class = "name" colspan = "8" align="left"><br>
                                  &nbsp;&nbsp;&nbsp;Tarifa
                                </td>
                              </tr>
                            </table>
                            <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:560" id = "Grid_Niveles_006"></table>
                        </fieldset>

                        <fieldset id="Tarifa_007">
                          <legend><b>Tarifa Fija Escalonada</b></legend>
                            <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:560">
                              <?php $zCol = f_Format_Cols(28); echo $zCol; ?>
                              <tr>
                                <td Class = "name" colspan = "17">Niveles <br>
                                  <input type = "text" Class = "letra" style = "width:080;text-align:right" name = "nNivel007" value=""
                                    onblur="javascript:f_FixFloat(this);" >
                                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type=button value="Generar" name="generar007" onclick="javascript:fnGridNiveles('007');" id="generar007">&nbsp;&nbsp;&nbsp;
                                    <input type=button value="Limpiar" name="limpiar007" onclick="javascript:fnDeleteRowGrid('007');" id="limpiar007">
                                </td>
                              </tr>
                            </table>

                            <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:560">
                              <?php $zCol = f_Format_Cols(28); echo $zCol; ?>
                              <tr>
                                <td Class = "name" colspan = "7" align="left"><br>
                                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rango Inferior
                                </td>
                                <td Class = "name" colspan = "6" align="left"><br>
                                  Rango Superior
                                </td>
                                <td Class = "name" colspan = "8" align="left"><br>
                                  &nbsp;&nbsp;&nbsp;Tarifa
                                </td>
                              </tr>
                            </table>
                            <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:560" id = "Grid_Niveles_007"></table>
                        </fieldset>

                        <fieldset id="Tarifa_008">
                          <legend><b>Tarifa Fija</b></legend>
                            <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:560">
                              <?php $zCol = f_Format_Cols(28); echo $zCol; ?>
                              <tr>
                                <td Class = "name" colspan = "4">Tarifa Fija<br>
                                  <input type = "text" Class = "letra" style = "width:080;text-align:right" name = "nTarFi008" value = "">
                                </td>
                              </tr>
                            </table>
                        </fieldset>

                        <fieldset id="Tarifa_009">
                          <legend><b>Tarifa Porcentual</b></legend>
                            <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:560">
                              <?php $zCol = f_Format_Cols(28); echo $zCol; ?>
                              <tr>
                                <td Class = "name" colspan = "4">%<br>
                                  <input type = "text" Class = "letra" style = "width:080;text-align:right" name = "nPorcen009" value=""
                                  onblur="javascript:f_FixFloat(this);">
                                </td>
                              </tr>
                            </table>
                        </fieldset>

                        <fieldset id="Tarifa_010">
                          <legend><b>Tarifa Porcentual con M&iacute;nima</b></legend>
                            <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:560">
                              <?php $zCol = f_Format_Cols(28); echo $zCol; ?>
                              <tr>
                                <td Class = "name" colspan = "4">%<br>
                                  <input type = "text" Class = "letra" style = "width:080;text-align:right" name = "nPorcen010" value=""
                                  onblur="javascript:f_FixFloat(this);">
                                </td>
                                <td Class = "name" colspan = "4">M&iacute;nima<br>
                                  <input type = "text" Class = "letra" style = "width:080;text-align:right" name = "nMini010" value=""
                                    onkeyup="javascript:f_FixFloat(this);this.value=Math.round(this.value);"
                                    onblur="javascript:f_FixFloat(this);this.value=Math.round(this.value);">
                                </td>
                              </tr>
                            </table>
                        </fieldset>
                      </td>
                    </tr>
                    <!-- Seccion 7 -->
                    <tr>
                      <td Class = "clase08" colspan = "20"><br><br>Aplica C&aacute;lculo a Nivel Nacional?<br>
                        <input type="checkbox" style="width:380;margin-top:-13px" name="cCseAcnn" Class = "letra" onchange="javascript:fnAplicaCalculo(this)"/>
                      </td>
                    </tr>
                    <!-- Seccion 8 -->
                    <tr >
                      <td Class = "clase08" colspan="30" id="idOrganizacion">
                        <fieldset>
                          <input type = 'hidden' name = 'cCseOrgVenta'>
                          <legend>Organizaci&oacute;n de Ventas</legend>
                          <div id = 'overDivOrgVenta'></div>
                        </fieldset>
                      </td>
                    </tr>
                    <!-- Seccion 9 -->
                    <tr >
                      <td Class = "clase08" colspan="30" id="idOficina">
                        <div class="idInputOficinasVentas" id="idInputOficinasVentas">
                        </div>
                        <div id="idOficinasVentas">
                        </div>
                      </td>
                    </tr>
                    <!-- Seccion 10 -->
                    <tr>
                      <td Class = "clase08" colspan = "30"><br>Observaci&oacute;n
                        <textarea Class = 'letra' style = 'width:600;height:40' name = 'cCseObs'></textarea>
                      </td>
                    </tr>
                    <!-- Seccion 11 -->
                    <tr>
                      <td Class = "clase08" colspan = "7">Creado<br>
                        <input type = "text" Class = "letra"  style = "width:140;text-align:center"  name = "dFecCre"  value = "<?php echo date('Y-m-d') ?>" readonly>
                      </td>
                      <td Class = "clase08" colspan = "5">Hora<br>
                        <input type = 'text' Class = 'letra' style = "width:100;text-align:center" name = "dHorCre"  value = "<?php echo date('H:i:s') ?>" readonly>
                      </td>
                      <td Class = "clase08" colspan = "7">Modificado<br>
                        <input type = "text" Class = "letra"  style = "width:140;text-align:center"  name = "dFecMod"  value = "<?php echo date('Y-m-d') ?>" readonly>
                      </td>
                      <td Class = "clase08" colspan = "5">Hora<br>
                        <input type = 'text' Class = 'letra' style = "width:100;text-align:center" name = "dHorMod"  value = "<?php echo date('H:i:s') ?>" readonly>
                      </td>
                      <td Class = "clase08" colspan = "6">Estado<br>
                        <input type = "text" Class = "letra" style = "width:120;text-align:center" name = "cEstado"  value = "ACTIVO"
                                onblur = "javascript:this.value=this.value.toUpperCase();fnValidacEstado();
                                        this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
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
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
            <?php break;
            default: ?>
              <td width="418" height="21"></td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:document.forms['frgrm'].submit();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
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
          fnOcultarMostrarTarifa('000');
          fnCargarGrillas();
        </script>
        <?php
      break;
      case "EDITAR":
      case "VER":
        ?>
        <script languaje = "javascript">
          fnOcultarMostrarTarifa('000');
        </script>
        <?php
        fnCargaData($cCseId);
        ?>
        <script languaje = "javascript">
          fnCargarGrillas();

          <?php 
            // Consulta oficinas de venta
            $qOficina  = "SELECT ";
            $qOficina .= "cseidxxx, ";
            $qOficina .= "orvsapxx, ";
            $qOficina .= "ofvsapxx ";
            $qOficina .= "FROM $cAlfa.lpar0154 ";
            $qOficina .= "WHERE ";
            $qOficina .= "cseidxxx = \"$cCseId\"";
            $xOficina  = f_MySql("SELECT","",$qOficina,$xConexion01,"");

            $mOficinas = array();
            $cOficina  = "";
            if (mysql_num_rows($xOficina) > 0) {
              while ($xRSS = mysql_fetch_array($xOficina)) {
                $mOficinas[$xRSS['orvsapxx']][] = $xRSS['ofvsapxx'];
              }
            }

            foreach ($mOficinas as $key => $value) {
              $cOficina = implode("~", $mOficinas[$key]); 
              ?>
              setTimeout(function(){   
                document.forms['frgrm']['cCseOfiVenta_'+'<?php echo $key ?>'].value = '<?php echo $cOficina ?>';
              }, 1000);
              <?php
            }
          ?>
        setTimeout(function(){
          fnCargarOficinaVentas();
        }, 1500);
        </script>
        <?php

        if ($_COOKIE['kModo'] == "VER") { ?>
          <script>
            for (x=0;x<document.forms['frgrm'].elements.length;x++) {
              document.forms['frgrm'].elements[x].readOnly = true;
              document.forms['frgrm'].elements[x].onfocus  = "";
              document.forms['frgrm'].elements[x].onblur   = "";
            }

            document.getElementById('lCliId').href    = 'javascript: alert("No permitido")';
            document.getElementById('idCcoIdOf').href = 'javascript: alert("No permitido")';
            document.getElementById('idSerSap').href  = 'javascript: alert("No permitido")';
            document.getElementById('idSubId').href    = 'javascript: alert("No permitido")';
            document.getElementById('idUfId').href    = 'javascript: alert("No permitido")';
            document.getElementById('idFcoId').href    = 'javascript: alert("No permitido")';
            document.getElementById('idObfId').href   = 'javascript: alert("No permitido")';

            document.forms['frgrm']['cCseAcnn'].disabled = true;
          </script>
          <?php 
        }
      break;
      default:
        //No hace nada
      break;
    }

    function fnCargaData($cCseId) {
      global $cAlfa; global $xConexion01;

      $qCondiServ  = "SELECT *, lpar0006.*, lpar0004.* ";
      $qCondiServ .= "FROM $cAlfa.lpar0152 ";
      $qCondiServ .= "LEFT JOIN $cAlfa.lpar0006 ON $cAlfa.lpar0152.ufaidxxx = $cAlfa.lpar0006.ufaidxxx ";
      $qCondiServ .= "LEFT JOIN $cAlfa.lpar0004 ON $cAlfa.lpar0152.obfidxxx = $cAlfa.lpar0004.obfidxxx ";
      $qCondiServ .= "WHERE ";
      $qCondiServ .= "cseidxxx = \"$cCseId\" LIMIT 0,1";
      $xCondiServ  = f_MySql("SELECT","",$qCondiServ,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qCondiServ."~".mysql_num_rows($xCondiServ)."~".mysql_error($xConexion01));
      if (mysql_num_rows($xCondiServ) > 0) {
        $vCondiServ  = mysql_fetch_array($xCondiServ);

        // Consulta cliente
        $qCliente  = "SELECT ";
        $qCliente .= "cliidxxx, ";
        $qCliente .= "clinomxx, ";
        $qCliente .= "clisapxx ";
        $qCliente .= "FROM $cAlfa.lpar0150 ";
        $qCliente .= "WHERE ";
        $qCliente .= "cliidxxx = \"{$vCondiServ['cliidxxx']}\" LIMIT 0,1";
        $xCliente  = f_MySql("SELECT","",$qCliente,$xConexion01,"");
        $vCliente  = mysql_fetch_array($xCliente);

        // Consulta Servicio
        $qServicio  = "SELECT ";
        $qServicio .= "sersapxx, ";
        $qServicio .= "serdesxx, ";
        $qServicio .= "regestxx ";
        $qServicio .= "FROM $cAlfa.lpar0011 ";
        $qServicio .= "WHERE ";
        $qServicio .= "sersapxx = \"{$vCondiServ['sersapxx']}\" LIMIT 0,1";
        $xServicio  = f_MySql("SELECT","",$qServicio,$xConexion01,"");
        $vServicio  = mysql_fetch_array($xServicio);

        // Consulta Condiciones de Servicio - Subservicios
        $qSubservicio  = "SELECT ";
        $qSubservicio .= "cseidxxx, ";
        $qSubservicio .= "$cAlfa.lpar0153.sersapxx AS sersapxx, ";
        $qSubservicio .= "$cAlfa.lpar0153.subidxxx AS subidxxx, ";
        $qSubservicio .= "$cAlfa.lpar0012.subdesxx AS subdesxx ";
        $qSubservicio .= "FROM $cAlfa.lpar0153 ";
        $qSubservicio .= "LEFT JOIN $cAlfa.lpar0012 ON $cAlfa.lpar0153.sersapxx = $cAlfa.lpar0012.sersapxx AND $cAlfa.lpar0153.subidxxx = $cAlfa.lpar0012.subidxxx  ";
        $qSubservicio .= "WHERE ";
        $qSubservicio .= "cseidxxx = \"{$vCondiServ['cseidxxx']}\" AND ";
        $qSubservicio .= "$cAlfa.lpar0153.sersapxx = \"{$vCondiServ['sersapxx']}\"";
        $xSubservicio  = f_MySql("SELECT","",$qSubservicio,$xConexion01,"");
        $vSubservicio  = array();
        if (mysql_num_rows($xSubservicio) > 0) {
          $vSubservicio = mysql_fetch_array($xSubservicio);
        }

        // Consulta organizacion de venta
        $cOrganizacion  = "";
        $qOrganizacion  = "SELECT ";
        $qOrganizacion .= "cseidxxx, ";
        $qOrganizacion .= "orvsapxx, ";
        $qOrganizacion .= "ofvsapxx ";
        $qOrganizacion .= "FROM $cAlfa.lpar0154 ";
        $qOrganizacion .= "WHERE ";
        $qOrganizacion .= "cseidxxx = \"{$vCondiServ['cseidxxx']}\"";
        $xOrganizacion  = f_MySql("SELECT","",$qOrganizacion,$xConexion01,"");

        $mOrganizacion = array();
        if (mysql_num_rows($xOrganizacion) > 0) {
          while ($xRSS = mysql_fetch_array($xOrganizacion)) {
            if (!array_key_exists($xRSS['orvsapxx'], $mOrganizacion)){
              $cOrganizacion .= $xRSS['orvsapxx'] . "~";
              $mOrganizacion[$xRSS['orvsapxx']] = $xRSS['orvsapxx'];
            }
          }
          $cOrganizacion = rtrim($cOrganizacion, "~");
        }

        ?>
        <script language = "javascript">
          document.forms['frgrm']['cCseId'].value      = "<?php echo $vCondiServ['cseidxxx'] ?>";
          document.forms['frgrm']['cCliId'].value      = "<?php echo $vCliente['cliidxxx'] ?>";
          document.forms['frgrm']['cCliDV'].value      = "<?php echo gendv($vCliente['cliidxxx'])?>";
          document.forms['frgrm']['cCliNom'].value     = "<?php echo $vCliente['clinomxx'] ?>";
          document.forms['frgrm']['cCliSap'].value     = "<?php echo $vCliente['clisapxx'] ?>";
          document.forms['frgrm']['cCcoIdOc'].value    = "<?php echo $vCondiServ['ccoidocx'] ?>";
          document.forms['frgrm']['cSerSap'].value     = "<?php echo $vCondiServ['sersapxx'] ?>";
          document.forms['frgrm']['cSerDes'].value     = "<?php echo $vServicio['serdesxx'] ?>";
          document.forms['frgrm']['cSubId'].value      = "<?php echo $vSubservicio['subidxxx'] ?>";
          document.forms['frgrm']['cSubDes'].value     = "<?php echo $vSubservicio['subdesxx'] ?>";
          document.forms['frgrm']['cUfaId'].value      = "<?php echo $vCondiServ['ufaidxxx'] ?>";
          document.forms['frgrm']['cUfaDes'].value     = "<?php echo $vCondiServ['ufadesxx'] ?>";
          document.forms['frgrm']['cObfId'].value      = "<?php echo $vCondiServ['obfidxxx'] ?>";
          document.forms['frgrm']['cObfDes'].value     = "<?php echo $vCondiServ['obfdesxx'] ?>";
          document.forms['frgrm']['cCseOrgVenta'].value = "<?php echo $cOrganizacion ?>";
          document.forms['frgrm']['cCseObs'].value     = "<?php echo $vCondiServ['cseobsxx'] ?>";
          document.forms['frgrm']['dFecCre'].value     = "<?php echo $vCondiServ['regfcrex'] ?>";
          document.forms['frgrm']['dHorCre'].value     = "<?php echo $vCondiServ['reghcrex'] ?>";
          document.forms['frgrm']['dFecMod'].value     = "<?php echo $vCondiServ['regfmodx'] ?>";
          document.forms['frgrm']['dHorMod'].value     = "<?php echo $vCondiServ['reghmodx'] ?>";
          document.forms['frgrm']['cEstado'].value     = "<?php echo $vCondiServ['regestxx'] ?>";

          if ("<?php echo $vCondiServ['cseacnnx'] ?>" == "SI") {
            document.forms['frgrm']['cCseAcnn'].checked = true;
            document.getElementById('idOrganizacion').style.display = "none";
            document.getElementById('idOficina').style.display      = "none";
          } else {
            document.forms['frgrm']['cCseAcnn'].checked = false;
          }
        </script>
        <?php

        // Se consultan la tarifa con la condicion de servicio
        $qTarifa  = "SELECT $cAlfa.lpar0131.*, ";
        $qTarifa .= "$cAlfa.lpar0130.fcodesxx ";
        $qTarifa .= "FROM $cAlfa.lpar0131 ";
        $qTarifa .= "LEFT JOIN $cAlfa.lpar0130 ON $cAlfa.lpar0131.fcoidxxx = $cAlfa.lpar0130.fcoidxxx ";
        $qTarifa .= "WHERE ";
        $qTarifa .= "cseidxxx = \"{$vCondiServ['cseidxxx']}\" limit 0,1 ";
        $xTarifa  = f_MySql("SELECT","",$qTarifa,$xConexion01,"");
        if (mysql_num_rows($xTarifa) > 0) {
          $vTarifa = mysql_fetch_array($xTarifa); ?>

          <script language = "javascript">
            document.forms['frgrm']['cFcoId'].value  = "<?php echo $vTarifa['fcoidxxx'] ?>";
            document.forms['frgrm']['cFcoDes'].value = "<?php echo $vTarifa['fcodesxx'] ?>";
          </script>
          <?php

          // Cargar valores de la Tarifa
          $cCadena = explode("|",$vTarifa['tardetxx']);
          switch ($vTarifa['fcoidxxx']) {
            case "001":
              ?>
              <script language = "javascript">
                fnOcultarMostrarTarifa("<?php echo $vTarifa['fcoidxxx'] ?>");
                document.forms['frgrm']['nVlrFi001'].value = "<?php echo $cCadena[1] ?>";
              </script>
            <?php break;
            case "002":
              $cCad002 = explode("~",$cCadena[1]);
              ?>
              <script language = "javascript">
                fnOcultarMostrarTarifa("<?php echo $vTarifa['fcoidxxx'] ?>");
                document.forms['frgrm']['nTarifa002'].value = "<?php echo $cCad002[0] ?>";
                document.forms['frgrm']['nMini002'].value   = "<?php echo $cCad002[1] ?>";
              </script>
            <?php break;
            case "003": ?>
              <script language = "javascript">
                fnOcultarMostrarTarifa("<?php echo $vTarifa['fcoidxxx'] ?>");
                <?php
                $cCadena3 = explode("!",$cCadena[1]);
                $zNum=0;
                for($i=1; $i<count($cCadena3); $i++){
                  if($cCadena3[$i]!=""){
                    $zNum=$zNum+1;
                  }
                }
                if($_COOKIE['kModo']=="VER"){
                ?>
                  document.getElementById('generar003').style.visibility="hidden";
                  document.getElementById('limpiar003').style.visibility="hidden";
                <?php
                }
                ?>
                document.forms['frgrm']['nNivel003'].value = "<?php echo $zNum ?>";
              </script>
              <?php

              for($i=1; $i<count($cCadena3); $i++){
                if($cCadena3[$i]!="") {
                  $zInterno=explode("^",$cCadena3[$i]);?>
                  <script language = "javascript">
                    fnGridVerNiveles("<?php echo $zInterno[0]; ?>","<?php echo $zInterno[1]; ?>","<?php echo $zInterno[2]; ?>","003");
                  </script>
                <?php }
              } ?>
            <?php break;
            case "004":
              ?>
              <script language = "javascript">
                fnOcultarMostrarTarifa("<?php echo $vTarifa['fcoidxxx'] ?>");
                <?php
                $cCadena2 = explode("~",$cCadena[1]);
                $cCadena3 = explode("!",$cCadena2[1]);
                $zNum=0;
                for($i=1; $i<count($cCadena3); $i++){
                  if($cCadena3[$i]!=""){
                    $zNum=$zNum+1;
                  }
                }
                if($_COOKIE['kModo']=="VER"){
                ?>
                  document.getElementById('generar004').style.visibility="hidden";
                  document.getElementById('limpiar004').style.visibility="hidden";
                <?php
                }
                ?>
                document.forms['frgrm']['nUnidMin004'].value = "<?php echo $cCadena2[0] ?>";
                document.forms['frgrm']['nNivel004'].value   = "<?php echo $zNum ?>";
              </script>
              <?php

              for($i=1; $i<count($cCadena3); $i++){
                if($cCadena3[$i]!="") {
                  $zInterno=explode("^",$cCadena3[$i]);?>
                  <script language = "javascript">
                    fnGridVerNiveles("<?php echo $zInterno[0]; ?>","<?php echo $zInterno[1]; ?>","<?php echo $zInterno[2]; ?>","004");
                  </script>
                <?php }
              } ?>
            <?php break;
            case "005": ?>
              <script language = "javascript">
                fnOcultarMostrarTarifa("<?php echo $vTarifa['fcoidxxx'] ?>");
                <?php
                $cCadena3 = explode("!",$cCadena[1]);
                $zNum=0;
                for($i=1; $i<count($cCadena3); $i++){
                  if($cCadena3[$i]!=""){
                    $zNum=$zNum+1;
                  }
                }
                if($_COOKIE['kModo']=="VER"){
                ?>
                  document.getElementById('generar005').style.visibility="hidden";
                  document.getElementById('limpiar005').style.visibility="hidden";
                <?php
                }
                ?>
                document.forms['frgrm']['nNivel005'].value = "<?php echo $zNum ?>";
              </script>
              <?php

              for($i=1; $i<count($cCadena3); $i++){
                if($cCadena3[$i]!="") {
                  $zInterno=explode("^",$cCadena3[$i]);?>
                  <script language = "javascript">
                    fnGridVerNiveles("<?php echo $zInterno[0]; ?>","<?php echo $zInterno[1]; ?>","<?php echo $zInterno[2]; ?>","005");
                  </script>
                <?php }
              } ?>
            <?php break;
            case "006":
              ?>
              <script language = "javascript">
                fnOcultarMostrarTarifa("<?php echo $vTarifa['fcoidxxx'] ?>");
                <?php
                $cCadena2 = explode("~",$cCadena[1]);
                $cCadena3 = explode("!",$cCadena2[1]);
                $zNum=0;
                for($i=1; $i<count($cCadena3); $i++){
                  if($cCadena3[$i]!=""){
                    $zNum=$zNum+1;
                  }
                }
                if($_COOKIE['kModo']=="VER"){
                ?>
                  document.getElementById('generar006').style.visibility="hidden";
                  document.getElementById('limpiar006').style.visibility="hidden";
                <?php
                }
                ?>
                document.forms['frgrm']['nVlrMin006'].value = "<?php echo $cCadena2[0] ?>";
                document.forms['frgrm']['nNivel006'].value = "<?php echo $zNum ?>";
              </script>
              <?php

              for($i=1; $i<count($cCadena3); $i++){
                if($cCadena3[$i]!="") {
                  $zInterno=explode("^",$cCadena3[$i]);?>
                  <script language = "javascript">
                    fnGridVerNiveles("<?php echo $zInterno[0]; ?>","<?php echo $zInterno[1]; ?>","<?php echo $zInterno[2]; ?>","006");
                  </script>
                <?php }
              } ?>
            <?php break;
            case "007": ?>
              <script language = "javascript">
                fnOcultarMostrarTarifa("<?php echo $vTarifa['fcoidxxx'] ?>");
                <?php
                $cCadena3 = explode("!",$cCadena[1]);
                $zNum=0;
                for($i=1; $i<count($cCadena3); $i++){
                  if($cCadena3[$i]!=""){
                    $zNum=$zNum+1;
                  }
                }
                if($_COOKIE['kModo']=="VER"){
                ?>
                  document.getElementById('generar007').style.visibility="hidden";
                  document.getElementById('limpiar007').style.visibility="hidden";
                <?php
                }
                ?>
                document.forms['frgrm']['nNivel007'].value = "<?php echo $zNum ?>";
              </script>
              <?php

              for($i=1; $i<count($cCadena3); $i++){
                if($cCadena3[$i]!="") {
                  $zInterno=explode("^",$cCadena3[$i]);?>
                  <script language = "javascript">
                    fnGridVerNiveles("<?php echo $zInterno[0]; ?>","<?php echo $zInterno[1]; ?>","<?php echo $zInterno[2]; ?>","007");
                  </script>
                <?php }
              } ?>
            <?php break;
            case "008":
              ?>
              <script language = "javascript">
                fnOcultarMostrarTarifa("<?php echo $vTarifa['fcoidxxx'] ?>");
                document.forms['frgrm']['nTarFi008'].value = "<?php echo $cCadena[1] ?>";
              </script>
            <?php break;
            case "009":
              ?>
              <script language = "javascript">
                fnOcultarMostrarTarifa("<?php echo $vTarifa['fcoidxxx'] ?>");
                document.forms['frgrm']['nPorcen009'].value = "<?php echo $cCadena[1] ?>";
              </script>
            <?php break;
            case "010":
              $cCad010 = explode("~",$cCadena[1]);
              ?>
              <script language = "javascript">
                fnOcultarMostrarTarifa("<?php echo $vTarifa['fcoidxxx'] ?>");
                document.forms['frgrm']['nPorcen010'].value = "<?php echo $cCad010[0] ?>";
                document.forms['frgrm']['nMini010'].value   = "<?php echo $cCad010[1] ?>";
              </script>
            <?php break;
          }
        }
      }
    }
    ?>
  </body>
</html>

<?php
  /**
   * Formulario Nueva Certificacion.
   * --- Descripcion: Permite Crear una Nueva Certificacion.
   * @author Juan Jose Trujillo Ch. juan.trujillo@openits.co
   * @package opencomex
   * @version 001
   */

  include("../../../../../financiero/libs/php/utility.php");
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
      /**
       * Permite salir del formulario.
       */
      function fnSalir() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
      }

      /**
       * Permite abrir los valid/windows para las diferentes parametricas.
       */
      function fnLinks(xLink, xSwitch, nSecuencia = "", xGrid = "") {
        var nX = screen.width;
        var nY = screen.height;
        switch (xLink) {
          // Comprobante
          case "cComPre":
            if (xSwitch == "VALID") {
              var zRuta  = "frped117.php?gWhat=VALID" +
                                        "&gFunction=cComPre" +
                                        "&gComPre="+document.forms['frgrm']['cComPre'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var nNx     = (nX-600)/2;
              var nNy     = (nY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
              var zRuta   = "frped117.php?gWhat=WINDOW" +
                                        "&gFunction=cComPre" +
                                        "&gComPre="+document.forms['frgrm']['cComPre'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          // Cliente
          case "cCliId":
            if (xSwitch == "VALID") {
              var zRuta  = "frped150.php?gWhat=VALID" +
                                        "&gFunction=cCliId" +
                                        "&gCliId="+document.forms['frgrm']['cCliId'].value;
              parent.fmpro.location = zRuta;
            } else {
              var nNx     = (nX-600)/2;
              var nNy     = (nY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
              var zRuta   = "frped150.php?gWhat=WINDOW" +
                                        "&gFunction=cCliId" +
                                        "&gCliId="+document.forms['frgrm']['cCliId'].value;
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cCliNom":
            if (xSwitch == "VALID") {
              var zRuta  = "frped150.php?gWhat=VALID"+
                                        "&gFunction=cCliNom"+
                                        "&gCliNom="+document.forms['frgrm']['cCliNom'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var nNx     = (nX-600)/2;
              var nNy     = (nY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
              var zRuta   = "frped150.php?gWhat=WINDOW"+
                                        "&gFunction=cCliNom"+
                                        "&gCliNom="+document.forms['frgrm']['cCliNom'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          // Organizacion de venta
          case "cOrvSap":
            if (xSwitch == "VALID") {
              var zRuta  = "frped001.php?gWhat=VALID"+
                                        "&gFunction=cOrvSap"+
                                        "&gOrvSap="+document.forms['frgrm']['cOrvSap'+xGrid+nSecuencia].value.toUpperCase()+
                                        "&gGrid="      +xGrid +
                                        "&gSecuencia=" +nSecuencia;
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (nX-600)/2;
              var zNy     = (nY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frped001.php?gWhat=WINDOW"+
                                        "&gFunction=cOrvSap"+
                                        "&gOrvSap="+document.forms['frgrm']['cOrvSap'+xGrid+nSecuencia].value.toUpperCase()+
                                        "&gGrid="      +xGrid +
                                        "&gSecuencia=" +nSecuencia;
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cOrvDes":
            if (xSwitch == "VALID") {
              var zRuta  = "frped001.php?gWhat=VALID"+
                                        "&gFunction=cOrvDes"+
                                        "&gOrvDes="    +document.forms['frgrm']['cOrvDes'+xGrid+nSecuencia].value.toUpperCase() +
                                        "&gGrid="      +xGrid +
                                        "&gSecuencia=" +nSecuencia;
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (nX-600)/2;
              var zNy     = (nY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frped001.php?gWhat=WINDOW"+
                                        "&gFunction=cOrvDes"+
                                        "&gOrvDes="    +document.forms['frgrm']['cOrvDes'+xGrid+nSecuencia].value.toUpperCase() +
                                        "&gGrid="      +xGrid +
                                        "&gSecuencia=" +nSecuencia;
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          // Oficina de venta
          case "cOfvSap":
            // Valida que haya seleccionado Organizacion de Venta
            var nSwitch = 1;
            if (xGrid != "") {
              if (document.forms['frgrm']['cOrvSap'+xGrid+nSecuencia].value == "") {
                nSwitch = 0;
              }
            }

            if (nSwitch == 1) {
              if (xSwitch == "VALID") {
                var gOrvSap = (xGrid != "") ? document.forms['frgrm']['cOrvSap'+xGrid+nSecuencia].value.toUpperCase() : "";
                var zRuta   = "frped002.php?gWhat=VALID"+
                                          "&gFunction=cOfvSap"+
                                          "&gOrvSap="    +gOrvSap+
                                          "&gOfvSap="    +document.forms['frgrm']['cOfvSap'+xGrid+nSecuencia].value.toUpperCase()+
                                          "&gGrid="      +xGrid +
                                          "&gSecuencia=" +nSecuencia;
                parent.fmpro.location = zRuta;
              } else {
                var zNx     = (nX-600)/2;
                var zNy     = (nY-250)/2;
                var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;

                var gOrvSap = (xGrid != "") ? document.forms['frgrm']['cOrvSap'+xGrid+nSecuencia].value.toUpperCase() : "";
                var zRuta   = "frped002.php?gWhat=WINDOW"+
                                          "&gFunction=cOfvSap"+
                                          "&gOrvSap="    +gOrvSap+
                                          "&gOfvSap="    +document.forms['frgrm']['cOfvSap'+xGrid+nSecuencia].value.toUpperCase()+
                                          "&gGrid="      +xGrid +
                                          "&gSecuencia=" +nSecuencia;
                zWindow = window.open(zRuta,"zWindow",zWinPro);
                zWindow.focus();
              }
            } else {
              alert('Debe seleccionar la organizacion de venta,\nVerifique.');
            }
          break;
          case "cOfvDes":
            var nSwitch = 1;
            if (xGrid != "") {
              if (document.forms['frgrm']['cOrvSap'+xGrid+nSecuencia].value == "") {
                nSwitch = 0;
              }
            }

            // Valida que haya seleccionado Organizacion de Venta
            if (nSwitch == 1) {
              if (xSwitch == "VALID") {
                var gOrvSap = (xGrid != "") ? document.forms['frgrm']['cOrvSap'+xGrid+nSecuencia].value.toUpperCase() : "";
                var zRuta  = "frped002.php?gWhat=VALID"+
                                          "&gFunction=cOfvDes"+
                                          "&gOrvSap="    +gOrvSap+
                                          "&gOfvDes="    +document.forms['frgrm']['cOfvDes'+xGrid+nSecuencia].value.toUpperCase()+
                                          "&gGrid="      +xGrid +
                                          "&gSecuencia=" +nSecuencia;
                parent.fmpro.location = zRuta;
              } else {
                var zNx     = (nX-600)/2;
                var zNy     = (nY-250)/2;
                var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;

                var gOrvSap = (xGrid != "") ? document.forms['frgrm']['cOrvSap'+xGrid+nSecuencia].value.toUpperCase() : "";
                var zRuta   = "frped002.php?gWhat=WINDOW"+
                                          "&gFunction=cOfvDes"+
                                          "&gOrvSap="    +gOrvSap+
                                          "&gOfvDes="    +document.forms['frgrm']['cOfvDes'+xGrid+nSecuencia].value.toUpperCase()+
                                          "&gGrid="      +xGrid +
                                          "&gSecuencia=" +nSecuencia;
                zWindow = window.open(zRuta,"zWindow",zWinPro);
                zWindow.focus();
              }
            } else {
              alert('Debe seleccionar la organizacion de venta,\nVerifique.');
            }
          break;
          // Certificacion del cliente
          case "cCerComCsc":
            var nSwitch = 0;
              var cMsj = "";
              if(document.forms['frgrm']['cCliId'].value == ""){
                nSwitch = 1;
                cMsj += 'Debe Seleccionar un Cliente para Consultar la Certificacion,\n';
              }

              if (nSwitch == 0) {
                if (xSwitch == "VALID") {
                  var zRuta = "frlcca00.php?gModo="      +xSwitch+"&gFunction="+xLink +
                                          "&gCliId="     +document.forms['frgrm']['cCliId'].value +
                                          "&gComConso="  +document.forms['frgrm']['cComConso'].value +
                                          "&gOfvSap="    +document.forms['frgrm']['cOfvSap'].value +
                                          "&gGrid="      +xGrid +
                                          "&gSecuencia=" +nSecuencia +
                                          "&gOrigen=NUEVO";
                  parent.fmpro.location = zRuta;
                } else if(xSwitch == "WINDOW") {
                  var nNx      = (nX-500)/2;
                  var nNy      = (nY-250)/2;
                  var zWinPro  = "width=500,scrollbars=1,height=250,left="+nNx+",top="+nNy;
                  var zRuta = "frlcca00.php?gModo="      +xSwitch+"&gFunction="+xLink+
                                          "&gCliId="     +document.forms['frgrm']['cCliId'].value + 
                                          "&gComConso="  +document.forms['frgrm']['cComConso'].value + 
                                          "&gOfvSap="    +document.forms['frgrm']['cOfvSap'].value + 
                                          "&gGrid="      +xGrid +
                                          "&gSecuencia=" +nSecuencia + 
                                          "&gOrigen=NUEVO";
                  zWindow = window.open(zRuta,xLink,zWinPro);
                  zWindow.focus();
                } else if (xSwitch == "EXACT") {
                  var zRuta = "frlcca00.php?gModo=EXACT&gFunction=" + xLink +
                                          "&gCliId="     +document.frgrm['cCliId'].value +
                                          "&gComConso="  +document.frgrm['cComConso'].value +
                                          "&gOfvSap="    +document.frgrm['cOfvSap'].value +
                                          "&gCerId="     +document.frgrm['cCerId'+xGrid+nSecuencia].value +
                                          "&cPerAno="    +document.frgrm['cCerAno'+xGrid+nSecuencia].value +
                                          "&gGrid="      +xGrid +
                                          "&gSecuencia=" +nSecuencia + 
                                          "&gOrigen=NUEVO";
                  parent.fmpro.location = zRuta;
                }
              } else {
                document.forms['frgrm']['cCerId'+xGrid+nSecuencia].value = "";
                alert(cMsj + 'Verifique.');
              }
          break;
          // Centro Logistico
          case "cCloDes":
            if (document.forms['frgrm']['cOrvSap'+xGrid+nSecuencia].value != "" && document.forms['frgrm']['cOfvSap'+xGrid+nSecuencia].value != "") {
              if (xSwitch == "VALID") {
                var zRuta  = "frped003.php?gWhat=VALID"+
                                          "&gFunction=cCloDes"+
                                          "&gOrvSap="    +document.forms['frgrm']['cOrvSap'+xGrid+nSecuencia].value.toUpperCase()+
                                          "&gOfvSap="    +document.forms['frgrm']['cOfvSap'+xGrid+nSecuencia].value.toUpperCase()+
                                          "&gCloDes="    +document.forms['frgrm']['cCloDes'+xGrid+nSecuencia].value.toUpperCase()+
                                          "&gGrid="      +xGrid +
                                          "&gSecuencia=" +nSecuencia;
                parent.fmpro.location = zRuta;
              } else {
                var zNx     = (nX-600)/2;
                var zNy     = (nY-250)/2;
                var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
                var zRuta   = "frped003.php?gWhat=WINDOW"+
                                          "&gFunction=cCloDes"+
                                          "&gOrvSap="    +document.forms['frgrm']['cOrvSap'+xGrid+nSecuencia].value.toUpperCase()+
                                          "&gOfvSap="    +document.forms['frgrm']['cOfvSap'+xGrid+nSecuencia].value.toUpperCase()+
                                          "&gCloDes="    +document.forms['frgrm']['cCloDes'+xGrid+nSecuencia].value.toUpperCase()+
                                          "&gGrid="      +xGrid +
                                          "&gSecuencia=" +nSecuencia;
                zWindow = window.open(zRuta,"zWindow",zWinPro);
                zWindow.focus();
              }
            } else {
              alert('Debe seleccionar la organizacion de venta y la oficina de venta,\nVerifique.');
            }
          break;
          // Sector
          case "cSecDes":
            if (xSwitch == "VALID") {
              var zRuta  = "frped009.php?gWhat=VALID"+
                                        "&gFunction=cSecDes"+
                                        "&gSecDes="    +document.forms['frgrm']['cSecDes'+xGrid+nSecuencia].value.toUpperCase()+
                                        "&gGrid="      +xGrid +
                                        "&gSecuencia=" +nSecuencia;
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (nX-600)/2;
              var zNy     = (nY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frped009.php?gWhat=WINDOW"+
                                        "&gFunction=cSecDes"+
                                        "&gSecDes="    +document.forms['frgrm']['cSecDes'+xGrid+nSecuencia].value.toUpperCase()+
                                        "&gGrid="      +xGrid +
                                        "&gSecuencia=" +nSecuencia;
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          // Canal Distribucion
          case "cCdiDes":
            if (xSwitch == "VALID") {
              var zRuta  = "frped008.php?gWhat=VALID" +
                                        "&gFunction=cCdiDes" +
                                        "&gCdiDes="    +document.forms['frgrm']['cCdiDes'+xGrid+nSecuencia].value.toUpperCase()+
                                        "&gGrid="      +xGrid +
                                        "&gSecuencia=" +nSecuencia;
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (nX-600)/2;
              var zNy     = (nY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frped008.php?gWhat=WINDOW" +
                                        "&gFunction=cCdiDes" +
                                        "&gCdiDes="    +document.forms['frgrm']['cCdiDes'+xGrid+nSecuencia].value.toUpperCase()+
                                        "&gGrid="      +xGrid +
                                        "&gSecuencia=" +nSecuencia;
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          // Tipo deposito
          case "cTdeDes":
            if (xSwitch == "VALID") {
              var zRuta  = "frped007.php?gWhat=VALID"+
                                        "&gFunction=cTdeDes"+
                                        "&gTdeDes="    +document.forms['frgrm']['cTdeDes'+xGrid+nSecuencia].value.toUpperCase()+
                                        "&gGrid="      +xGrid +
                                        "&gSecuencia=" +nSecuencia;
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (nX-600)/2;
              var zNy     = (nY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frped007.php?gWhat=WINDOW"+
                                        "&gFunction=cTdeDes"+
                                        "&gTdeDes="    +document.forms['frgrm']['cTdeDes'+xGrid+nSecuencia].value.toUpperCase()+
                                        "&gGrid="      +xGrid +
                                        "&gSecuencia=" +nSecuencia;
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          // Deposito
          case "cDepNum":
            if (document.forms['frgrm']['cCliId'].value == '') {
              alert("Debe seleccionar el cliente para poder consultar los Depositos,\nVerifique.")
            } else {
              if (xSwitch == "VALID") {
                var zRuta  = "frped155.php?gWhat=VALID" +
                              "&gFunction=cDepNum" +
                              "&gCliId="     +document.forms['frgrm']['cCliId'].value +
                              "&gDepNum="    +document.forms['frgrm']['cDepNum'+xGrid+nSecuencia].value.toUpperCase()+
                              "&gTdeId="     +document.forms['frgrm']['cTdeId'+xGrid+nSecuencia].value.toUpperCase()+
                              "&gGrid="      +xGrid +
                              "&gSecuencia=" +nSecuencia;
                parent.fmpro.location = zRuta;
              } else {
                var zNx     = (nX-600)/2;
                var zNy     = (nY-250)/2;
                var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
                var zRuta   = "frped155.php?gWhat=WINDOW" +
                              "&gFunction=cDepNum" +
                              "&gCliId="     +document.forms['frgrm']['cCliId'].value +
                              "&gDepNum="    +document.forms['frgrm']['cDepNum'+xGrid+nSecuencia].value.toUpperCase()+
                              "&gTdeId="     +document.forms['frgrm']['cTdeId'+xGrid+nSecuencia].value.toUpperCase()+
                              "&gGrid="      +xGrid +
                              "&gSecuencia=" +nSecuencia;
                zWindow = window.open(zRuta,"zWindow",zWinPro);
                zWindow.focus();
              }
            }
          break;
          // Oferta (Condicion) Comercial 
          case "cCcoIdOc":
            if (document.forms['frgrm']['cCliId'].value != "") {
              if (xSwitch == "VALID") {
                var zRuta  = "frped151.php?gWhat=VALID" + 
                                          "&gFunction=cCcoIdOc" + 
                                          "&gCliId="     +document.forms['frgrm']['cCliId'].value +
                                          "&gCcoIdOc="   +document.forms['frgrm']['cCcoIdOc'+xGrid+nSecuencia].value.toUpperCase() +
                                          "&gGrid="      +xGrid +
                                          "&gSecuencia=" +nSecuencia;
                parent.fmpro.location = zRuta;
              } else {
                var zNx     = (nX-600)/2;
                var zNy     = (nY-250)/2;
                var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
                var zRuta   = "frped151.php?gWhat=WINDOW" +
                                          "&gFunction=cCcoIdOc" + 
                                          "&gCliId="   +document.forms['frgrm']['cCliId'].value +
                                          "&gCcoIdOc=" +document.forms['frgrm']['cCcoIdOc'+xGrid+nSecuencia].value.toUpperCase() +
                                          "&gGrid="    +xGrid +
                                        "&gSecuencia=" +nSecuencia;
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
              var zRuta  = "frped011.php?gWhat=VALID"+
                                        "&gFunction=cSerSap"+
                                        "&gSerSap="    +document.forms['frgrm']['cSerSap'+xGrid+nSecuencia].value.toUpperCase()+
                                        "&gGrid="      +xGrid +
                                        "&gSecuencia=" +nSecuencia;
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (nX-600)/2;
              var zNy     = (nY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frped011.php?gWhat=WINDOW"+
                                        "&gFunction=cSerSap"+
                                        "&gSerSap="    +document.forms['frgrm']['cSerSap'+xGrid+nSecuencia].value.toUpperCase()+
                                        "&gGrid="      +xGrid +
                                        "&gSecuencia=" +nSecuencia;
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          // Subservicio
          case "cSubDes":
            // Valida que haya seleccionado Organizacion de Venta
            var nSwitch = 1;
            if (xGrid == "_Det" && document.forms['frgrm']['cSerSap'+xGrid+nSecuencia].value == "") {
              nSwitch = 0;
            }

            if (nSwitch == 1) {
              if (xSwitch == "VALID") {
                var zRuta  = "frped012.php?gWhat=VALID"+
                                          "&gFunction=cSubDes"+
                                          "&gSerSap="    +document.forms['frgrm']['cSerSap'+xGrid+nSecuencia].value.toUpperCase()+
                                          "&gSubDes="    +document.forms['frgrm']['cSubDes'+xGrid+nSecuencia].value.toUpperCase()+
                                          "&gGrid="      +xGrid +
                                          "&gSecuencia=" +nSecuencia;
                parent.fmpro.location = zRuta;
              } else {
                var zNx     = (nX-600)/2;
                var zNy     = (nY-250)/2;
                var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
                var zRuta   = "frped012.php?gWhat=WINDOW"+
                                          "&gFunction=cSubDes"+
                                          "&gSerSap="    +document.forms['frgrm']['cSerSap'+xGrid+nSecuencia].value.toUpperCase()+
                                          "&gSubDes="    +document.forms['frgrm']['cSubDes'+xGrid+nSecuencia].value.toUpperCase()+
                                          "&gGrid="      +xGrid +
                                          "&gSecuencia=" +nSecuencia;
                zWindow = window.open(zRuta,"zWindow",zWinPro);
                zWindow.focus();
              }
            } else {
              alert('Debe seleccionar el Servicio\nVerifique.');
            }
          break;
          // Objeto Facturable
          case "cObfId":
            if (xSwitch == "VALID") {
              var zRuta  = "frped004.php?gWhat=VALID"+
                                        "&gFunction=cObfId"+
                                        "&gObfId="     +document.forms['frgrm']['cObfId'+xGrid+nSecuencia].value.toUpperCase()+
                                        "&gGrid="      +xGrid +
                                        "&gSecuencia=" +nSecuencia;
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (nX-600)/2;
              var zNy     = (nY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frped004.php?gWhat=WINDOW"+
                                        "&gFunction=cObfId"+
                                        "&gObfId="     +document.forms['frgrm']['cObfId'+xGrid+nSecuencia].value.toUpperCase()+
                                        "&gGrid="      +xGrid +
                                        "&gSecuencia=" +nSecuencia;
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          // Unidad Facturable
          case "cUfaId":
            if (xSwitch == "VALID") {
              var zRuta  = "frped006.php?gWhat=VALID"+
                                        "&gFunction=cUfaId"+
                                        "&gUfaId="     +document.forms['frgrm']['cUfaId'+xGrid+nSecuencia].value.toUpperCase()+
                                        "&gGrid="      +xGrid +
                                        "&gSecuencia=" +nSecuencia;
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (nX-600)/2;
              var zNy     = (nY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frped006.php?gWhat=WINDOW"+
                                        "&gFunction=cUfaId"+
                                        "&gUfaId="     +document.forms['frgrm']['cUfaId'+xGrid+nSecuencia].value.toUpperCase()+
                                        "&gGrid="      +xGrid +
                                        "&gSecuencia=" +nSecuencia;
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          // Codigo CEBE
          case "cCebCod":
            if (nSecuencia != "") {
              if (xSwitch == "VALID") {
                var zRuta  = "frped010.php?gWhat=VALID" +
                                          "&gFunction=cCebCod" +
                                          "&gCebCod="    +document.forms['frgrm']['cCebCod'+xGrid+nSecuencia].value +
                                          "&gGrid="      +xGrid +
                                          "&gSecuencia=" +nSecuencia;
                parent.fmpro.location = zRuta;
              } else {
                var zNx     = (nX-600)/2;
                var zNy     = (nY-250)/2;
                var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
                var zRuta   = "frped010.php?gWhat=WINDOW" +
                                          "&gFunction=cCebCod" +
                                          "&gCebCod="    +document.forms['frgrm']['cCebCod'+xGrid+nSecuencia].value +
                                          "&gGrid="      +xGrid +
                                        "&gSecuencia="   +nSecuencia;
                zWindow = window.open(zRuta,"zWindow",zWinPro);
                zWindow.focus();
              }
            }
          break;
        }
      }

      /**
       * Permite habilitar o deshabilitar el campo de Consecutivo dependiendo del tipo de comprobante MANUAL/AUTOMATICO.
       */
      function fnHabilitaConsecutivo(xComTCo) {
        if (document.forms['frgrm']['cComPre'].value == "") {
          alert("Debe Seleccionar el Prefijo,\nVerifique.")
          document.forms['frgrm']['cComCsc'].blur();
        } else {
          if (document.forms['frgrm']['cComTCo'].value == "AUTOMATICO") {
            document.forms['frgrm']['cComCsc'].value    = "";
            document.forms['frgrm']['cComCsc'].readOnly = true;
          } else {
            document.forms['frgrm']['cComCsc'].readOnly = false;
          }
        }
      }

      /**
       * Permite ocultar y mostrar los objetos cuando se presiona en siguiente o anterior.
       */
      function fnMostrarOcultarObjetos(xStep, xAccion) {
        switch (xStep) {
          case "1":
            document.getElementById("idTitulo").innerHTML = 'Wizard de Pedido - Paso 1';

            document.getElementById("Datos_del_Comprobante").style.display="block";
            document.getElementById("Datos_del_Cliente").style.display="block";
            document.getElementById("fieldset_de_Certificaciones_Automatico").style.display="none";
            document.getElementById("fieldset_de_Certificaciones_Manual").style.display="none";
            document.getElementById("fieldset_de_Detalle_Certificaciones").style.display="none";

            // Mostrar - Ocultar botones
            document.getElementById("idBtnPaso1").style.display="block";
            document.getElementById("idBtnPaso2").style.display="none";
            document.getElementById("idBtnPaso3").style.display="none";
          break;
          case "2":
            var nSwitch = 0;
            if (xAccion == "siguiente") {
              var cValidaciones = fnValidaciones(xStep);
              if (cValidaciones != "") {
                nSwitch = 1;
                cValidaciones += "Verifique."
                alert(cValidaciones);
              }
            }

            // Valida si el cliente cambio para limpiar los campos del paso 2 y 3 ya que dependen del cliente seleccionado
            if (document.forms['frgrm']['cCliId_Oculto'].value != "" && document.forms['frgrm']['cCliId_Oculto'].value != document.forms['frgrm']['cCliId'].value) {
              fnChangeTipoPedido();
            }
            // Almacena el Nit del cliente en el campo oculto
            document.forms['frgrm']['cCliId_Oculto'].value = document.forms['frgrm']['cCliId'].value;

            if (nSwitch == 0) {
              document.getElementById("idTitulo").innerHTML = 'Wizard de Pedido - Paso 2';

              document.getElementById("Datos_del_Comprobante").style.display="none";
              document.getElementById("Datos_del_Cliente").style.display="none";
              document.getElementById("fieldset_de_Detalle_Certificaciones").style.display="none";

              // Si el tipo de pedido es AUTOMATICO o MANUAL se pinta una grilla diferente en el paso 2
              if (document.forms['frgrm']['cComTPed'].value == "AUTOMATICA") {
                document.getElementById("fieldset_de_Certificaciones_Automatico").style.display="block";
                document.getElementById("fieldset_de_Certificaciones_Manual").style.display="none";
                fnAddNewRowCertificacionAuto('Grid_Certificacion_Auto', 'SIGUIENTE');
              } else if(document.forms['frgrm']['cComTPed'].value == "MANUAL") {
                document.getElementById("fieldset_de_Certificaciones_Manual").style.display="block";
                document.getElementById("fieldset_de_Certificaciones_Automatico").style.display="none";
                fnAddNewRowCertificacionManual('Grid_Certificacion_Manual', 'SIGUIENTE');
              }

              // Si el Consolidado del paso 1 es SI permite agregar varias certificaciones
              if (document.forms['frgrm']['cComConso'].value == "SI") {
                document.getElementById("idAddNewRowCertificacion").style.display="block";
              } else {
                document.getElementById("idAddNewRowCertificacion").style.display="none";
              }

              // Mostrar - Ocultar botones
              document.getElementById("idBtnPaso1").style.display="none";
              document.getElementById("idBtnPaso2").style.display="block";
              document.getElementById("idBtnPaso3").style.display="none";
            }
          break;
          case "3":
            var nSwitch = 0;
            if (xAccion == "siguiente") {
              var cValidaciones = fnValidaciones(xStep, document.forms['frgrm']['cComTPed'].value);
              if (cValidaciones != "") {
                nSwitch = 1;
                cValidaciones += "Verifique."
                alert(cValidaciones);
              }
            }

            if (nSwitch == 0) {
              document.getElementById("idTitulo").innerHTML = 'Wizard de Pedido - Paso 3';

              document.getElementById("Datos_del_Comprobante").style.display="none";
              document.getElementById("Datos_del_Cliente").style.display="none";

              // Carga las grillas del detalle
              if (document.forms['frgrm']['cComTPed'].value == "AUTOMATICA") {
                fnCargarGrillaSubServicio();
                document.getElementById("fieldset_de_Certificaciones_Automatico").style.display="none";
              } else if(document.forms['frgrm']['cComTPed'].value == "MANUAL") {
                fnAddNewRowDetalleCerticacion('Grid_Detalle_Certificacion', 'SIGUIENTE');
                document.getElementById("fieldset_de_Certificaciones_Manual").style.display="none";
              }

              document.getElementById("fieldset_de_Detalle_Certificaciones").style.display="block";

              // Mostrar - Ocultar botones
              document.getElementById("idBtnPaso1").style.display="none";
              document.getElementById("idBtnPaso2").style.display="none";
              document.getElementById("idBtnPaso3").style.display="block";
            }
          break;
    
        }
      }

      /**
       * Permite adicionar una nueva grilla en la seccion de certificacion Automatica paso 2.
       */
      function fnAddNewRowCertificacionAuto(xTabla, cTipoAccion = '') {
        var cGrid      = document.getElementById(xTabla);
        var nLastRow   = cGrid.rows.length;
        var nSecuencia = nLastRow+1;
        var cReadonly  = 'readonly';

        var nIncluir = 0;
        if (cTipoAccion == 'SIGUIENTE' && nSecuencia > 1) {
          nIncluir = 1;
        }

        if (nIncluir == 0) {
          var cTableRow   = cGrid.insertRow(nLastRow);
          var cCerId      = 'cCerId_Certi'    + nSecuencia; // Id de la Certificacion
          var cCerAno     = 'cCerAno_Certi'   + nSecuencia; // Anio de la Certificacion
          var cCerComCsc  = 'cCerComCsc_Certi'+ nSecuencia; // Consecutivo de la Certificacion
          var cMifId      = 'cMifId_Certi'    + nSecuencia; // Id de la MIF
          var cMifidAno   = 'cMifIdAno_Certi' + nSecuencia; // Anio de la MIF
          var cMifComCsc  = 'cMifComCsc_Certi'+ nSecuencia; // Consecutivo de la MIF
          var cOrvSap     = 'cOrvSap_Certi'   + nSecuencia; // Codigo de la Organizacion de Venta (Oculto)
          var cOfvSap     = 'cOfvSap_Certi'   + nSecuencia; // Codigo de la Oficina de Venta (Oculto)
          var cOfvDes     = 'cOfvDes_Certi'   + nSecuencia; // Descripcion de la Oficina de Venta
          var cCerFde     = 'cCerFde_Certi'   + nSecuencia; // Fecha Vigencia Desde
          var cCerFha     = 'cCerFha_Certi'   + nSecuencia; // Fecha Vigencia Hasta
          var cDepNum     = 'cDepNum_Certi'   + nSecuencia; // Numero de Deposito
          var cCcoIdOc    = 'cCcoIdOc_Certi'  + nSecuencia; // Id Oferta Comercial
          var oBtnDel     = 'oBtnDel_Certi'   + nSecuencia; // Boton de Borrar Row

          TD_xAll = cTableRow.insertCell(0);
          TD_xAll.style.width  = "20px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:20;text-align:left' name = 'cSec_Certi"+nSecuencia+"' id = 'cSec_Certi"+nSecuencia+"' value = '"+nSecuencia+"' readonly >";

          // Informacion de la Certificacion
          TD_xAll = cTableRow.insertCell(1);
          TD_xAll.style.width  = "200px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:200;text-align:left' name = '"+cCerComCsc+"' id = '"+cCerComCsc+"' onKeydown = 'javascript:if(event.keyCode == 13){ fnLinks(\"cCerComCsc\",\"VALID\",\""+nSecuencia+"\",\"_Certi\") }' >";

          TD_xAll = cTableRow.insertCell(2);
          TD_xAll.innerHTML    = "<input type = 'hidden' name = '"+cCerId+"'>";

          TD_xAll = cTableRow.insertCell(3);
          TD_xAll.innerHTML    = "<input type = 'hidden' name = '"+cCerAno+"'>";

          // Informacion de la MIF
          TD_xAll = cTableRow.insertCell(4);
          TD_xAll.style.width  = "200px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:200;text-align:left' name = '"+cMifComCsc+"' id = '"+cMifComCsc+"' "+cReadonly+">";

          TD_xAll = cTableRow.insertCell(5);
          TD_xAll.innerHTML    = "<input type = 'hidden' name = '"+cMifId+"'>";

          TD_xAll = cTableRow.insertCell(6);
          TD_xAll.innerHTML    = "<input type = 'hidden' name = '"+cMifidAno+"'>";
          // FIN Informacion de la MIF

          TD_xAll = cTableRow.insertCell(7);
          TD_xAll.innerHTML    = "<input type = 'hidden' name = '"+cOrvSap+"'>";

          TD_xAll = cTableRow.insertCell(8);
          TD_xAll.innerHTML    = "<input type = 'hidden' name = '"+cOfvSap+"'>";

          TD_xAll = cTableRow.insertCell(9);
          TD_xAll.style.width  = "160px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:160;text-align:left' name = '"+cOfvDes+"' id = '"+cOfvDes+"' "+cReadonly+">";

          TD_xAll = cTableRow.insertCell(10);
          TD_xAll.style.width  = "140px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:140;text-align:left' name = '"+cCerFde+"' id = '"+cCerFde+"' "+cReadonly+">";

          TD_xAll = cTableRow.insertCell(11);
          TD_xAll.style.width  = "140px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:140;text-align:left' name = '"+cCerFha+"' id = '"+cCerFha+"' "+cReadonly+">";

          TD_xAll = cTableRow.insertCell(12);
          TD_xAll.style.width  = "160px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:160;text-align:left' name = '"+cDepNum+"' id = '"+cDepNum+"' "+cReadonly+">";

          TD_xAll = cTableRow.insertCell(13);
          TD_xAll.style.width  = "160px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:160;text-align:left' name = '"+cCcoIdOc+"' id = '"+cCcoIdOc+"' "+cReadonly+">";

          TD_xAll = cTableRow.insertCell(14);
          TD_xAll.style.width  = "20px";
          if ('<?php echo $_COOKIE['kModo'] ?>' != "VER") {
            TD_xAll.innerHTML    = "<input type = 'button' style = 'width:020;text-align:center' name = "+oBtnDel+" id = "+oBtnDel+" value = 'X' "+
                                    "onClick = 'javascript:fnDeleteRowCertificacionAuto(this.value,\""+nSecuencia+"\",\""+xTabla+"\");'>";
          } else {
            TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:20;text-align:left' disabled >";
          }
          document.forms['frgrm']['nSecuencia_Cert'].value = nSecuencia;
        }
      }

      /**
       * Permite adicionar una nueva grilla en la seccion de certificacion Manual paso 2.
       */
      function fnAddNewRowCertificacionManual(xTabla, cTipoAccion = '') {
        var cGrid      = document.getElementById(xTabla);
        var nLastRow   = cGrid.rows.length;
        var nSecuencia = nLastRow+1;

        var nIncluir = 0;
        if (cTipoAccion == 'SIGUIENTE' && nSecuencia > 1) {
          nIncluir = 1;
        }

        if (nIncluir == 0) {
          var cTableRow   = cGrid.insertRow(nLastRow);
          var cOrvSap     = 'cOrvSap_Certi'   + nSecuencia; // Codigo de la Organizacion de Venta (Oculto)
          var cOrvDes     = 'cOrvDes_Certi'   + nSecuencia; // Descripcion de la Organizacion de Venta
          var cOfvSap     = 'cOfvSap_Certi'   + nSecuencia; // Codigo de la Oficina de Venta (Oculto)
          var cOfvDes     = 'cOfvDes_Certi'   + nSecuencia; // Descripcion de la Oficina de Venta
          var cCloSap     = 'cCloSap_Certi'   + nSecuencia; // Codigo del Centro Logistico (Oculto)
          var cCloDes     = 'cCloDes_Certi'   + nSecuencia; // Descripcion del Centro Logistico
          var cSecSap     = 'cSecSap_Certi'   + nSecuencia; // Codigo del Sector (Oculto)
          var cSecDes     = 'cSecDes_Certi'   + nSecuencia; // Descripcion del Sector
          var cCdiSap     = 'cCdiSap_Certi'   + nSecuencia; // Codigo del Canal Distribucion (Oculto)
          var cCdiDes     = 'cCdiDes_Certi'   + nSecuencia; // Descripcion del Canal Distribucion
          var cTdeId      = 'cTdeId_Certi'    + nSecuencia; // Codigo del Canal Distribucion (Oculto)
          var cTdeDes     = 'cTdeDes_Certi'   + nSecuencia; // Descripcion del Canal Distribucion
          var cDepNum     = 'cDepNum_Certi'   + nSecuencia; // Numero de Deposito
          var cCerFde     = 'cCerFde_Certi'   + nSecuencia; // Fecha Vigencia Desde
          var cCerFha     = 'cCerFha_Certi'   + nSecuencia; // Fecha Vigencia Hasta
          var cCcoIdOc    = 'cCcoIdOc_Certi'  + nSecuencia; // Id Oferta Comercial
          var oBtnDel     = 'oBtnDel_Certi'   + nSecuencia; // Boton de Borrar Row

          TD_xAll = cTableRow.insertCell(0);
          TD_xAll.style.width  = "20px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:20;text-align:left' name = 'cSec_Certi"+nSecuencia+"' id = 'cSec_Certi"+nSecuencia+"' value = '"+nSecuencia+"' readonly >";

          // Datos parametricos
          TD_xAll = cTableRow.insertCell(1);
          TD_xAll.innerHTML    = "<input type = 'hidden' name = '"+cOrvSap+"'>";

          TD_xAll = cTableRow.insertCell(2);
          TD_xAll.style.width  = "120px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:120;text-align:left' name = '"+cOrvDes+"' id = '"+cOrvDes+"' onKeydown = 'javascript:if(event.keyCode == 13){ fnLinks(\"cOrvDes\",\"VALID\",\""+nSecuencia+"\",\"_Certi\") }' >";

          TD_xAll = cTableRow.insertCell(3);
          TD_xAll.innerHTML    = "<input type = 'hidden' name = '"+cOfvSap+"'>";

          TD_xAll = cTableRow.insertCell(4);
          TD_xAll.style.width  = "120px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:120;text-align:left' name = '"+cOfvDes+"' id = '"+cOfvDes+"' onKeydown = 'javascript:if(event.keyCode == 13){ fnLinks(\"cOfvDes\",\"VALID\",\""+nSecuencia+"\",\"_Certi\") }' >";

          TD_xAll = cTableRow.insertCell(5);
          TD_xAll.innerHTML    = "<input type = 'hidden' name = '"+cCloSap+"'>";

          TD_xAll = cTableRow.insertCell(6);
          TD_xAll.style.width  = "120px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:120;text-align:left' name = '"+cCloDes+"' id = '"+cCloDes+"' onKeydown = 'javascript:if(event.keyCode == 13){ fnLinks(\"cCloDes\",\"VALID\",\""+nSecuencia+"\",\"_Certi\") }' >";

          TD_xAll = cTableRow.insertCell(7);
          TD_xAll.innerHTML    = "<input type = 'hidden' name = '"+cSecSap+"'>";

          TD_xAll = cTableRow.insertCell(8);
          TD_xAll.style.width  = "120px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:120;text-align:left' name = '"+cSecDes+"' id = '"+cSecDes+"' onKeydown = 'javascript:if(event.keyCode == 13){ fnLinks(\"cSecDes\",\"VALID\",\""+nSecuencia+"\",\"_Certi\") }' >";

          TD_xAll = cTableRow.insertCell(9);
          TD_xAll.innerHTML    = "<input type = 'hidden' name = '"+cCdiSap+"'>";

          TD_xAll = cTableRow.insertCell(10);
          TD_xAll.style.width  = "120px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:120;text-align:left' name = '"+cCdiDes+"' id = '"+cCdiDes+"' onKeydown = 'javascript:if(event.keyCode == 13){ fnLinks(\"cCdiDes\",\"VALID\",\""+nSecuencia+"\",\"_Certi\") }' >";

          TD_xAll = cTableRow.insertCell(11);
          TD_xAll.innerHTML    = "<input type = 'hidden' name = '"+cTdeId+"'>";

          TD_xAll = cTableRow.insertCell(12);
          TD_xAll.style.width  = "120px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:120;text-align:left' name = '"+cTdeDes+"' id = '"+cTdeDes+"' onKeydown = 'javascript:if(event.keyCode == 13){ fnLinks(\"cTdeDes\",\"VALID\",\""+nSecuencia+"\",\"_Certi\") }' >";
          // FIN Datos parametricos

          TD_xAll = cTableRow.insertCell(13);
          TD_xAll.style.width  = "120px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:120;text-align:left' name = '"+cDepNum+"' id = '"+cDepNum+"' onKeydown = 'javascript:if(event.keyCode == 13){ fnLinks(\"cDepNum\",\"VALID\",\""+nSecuencia+"\",\"_Certi\") }' >";

          TD_xAll = cTableRow.insertCell(14);
          TD_xAll.style.width  = "100px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:100;text-align:left' name = '"+cCerFde+"' id = '"+cCerFde+"' onblur='javascript:chDate(this);' onKeydown = 'javascript:show_calendar(\"frgrm."+cCerFde+"\")' >";

          TD_xAll = cTableRow.insertCell(15);
          TD_xAll.style.width  = "100px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:100;text-align:left' name = '"+cCerFha+"' id = '"+cCerFha+"' onblur='javascript:chDate(this);' onKeydown = 'javascript:show_calendar(\"frgrm."+cCerFha+"\")' >";

          TD_xAll = cTableRow.insertCell(16);
          TD_xAll.style.width  = "120px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:120;text-align:left' name = '"+cCcoIdOc+"' id = '"+cCcoIdOc+"' onKeydown = 'javascript:if(event.keyCode == 13){ fnLinks(\"cCcoIdOc\",\"VALID\",\""+nSecuencia+"\",\"_Certi\") }' >";

          TD_xAll = cTableRow.insertCell(17);
          TD_xAll.style.width  = "20px";
          if ('<?php echo $_COOKIE['kModo'] ?>' != "VER") {
            TD_xAll.innerHTML    = "<input type = 'button' style = 'width:020;text-align:center' name = "+oBtnDel+" id = "+oBtnDel+" value = 'X' "+
                                    "onClick = 'javascript:fnDeleteRowCertificacionManual(this.value,\""+nSecuencia+"\",\""+xTabla+"\");'>";
          } else {
            TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:20;text-align:left' disabled >";
          }

          document.forms['frgrm']['nSecuencia_Cert'].value = nSecuencia;
        }
      }

      /**
       * Permite adicionar una nueva grilla en la seccion de detalle certificacion Automatica Paso 3.
       */
      function fnAddNewRowDetalleCerticacion(xTabla, cTipoAccion = '') {
        var cGrid      = document.getElementById(xTabla);
        var nLastRow   = cGrid.rows.length;
        var nSecuencia = nLastRow+1;

        var nIncluir = 0;
        if (cTipoAccion == 'SIGUIENTE' && nSecuencia > 1) {
          nIncluir = 1;
        }

        var cDisabled = "";
        if (cTipoAccion == "MANUAL" || document.forms['frgrm']['cComTPed'].value == "MANUAL") {
          var cDisabled = "disabled";
        }

        if (nIncluir == 0) {
          var cTableRow   = cGrid.insertRow(nLastRow);
          var cCSeId      = 'cCSeId_Det'     + nSecuencia; // Numero de condicion de servicio
          var cCerdEst    = 'cCerdEst_Det'   + nSecuencia; // Estatus del servicio en la certificacion
          var cSerSap     = 'cSerSap_Det'    + nSecuencia; // Codigo sap servicio en la certificacion
          var cSerDes     = 'cSerDes_Det'    + nSecuencia; // Descripcion del servicio en la certificacion
          var cSubId      = 'cSubId_Det'     + nSecuencia; // Codigo subservicio en la certificacion (Oculto)
          var cSubDes     = 'cSubDes_Det'    + nSecuencia; // Descripcion subservicio en la certificacion
          var cObfId      = 'cObfId_Det'     + nSecuencia; // Id objeto facturable en la certificacion
          var cUfaId      = 'cUfaId_Det'     + nSecuencia; // Id unidad facturable en la certificacion
          var cCebId      = 'cCebId_Det'     + nSecuencia; // Id CEBE en la certificacion
          var cCebCod     = 'cCebCod_Det'    + nSecuencia; // Codigo CEBE en la certificacion
          var cCebDes     = 'cCebDes_Det'    + nSecuencia; // Descripcion CEBE en la certificacion
          var cBase       = 'cBase_Det'      + nSecuencia; // Base del subservicio en la certificacion
          var cTarifa     = 'cTarifa_Det'    + nSecuencia; // Tarifa del pedido
          var cCalculo    = 'cCalculo_Det'   + nSecuencia; // Calculo del pedido
          var cMinima     = 'cMinima_Det'    + nSecuencia; // Minima del pedido
          var cVlrPedido  = 'cVlrPedido_Det' + nSecuencia; // Valor del pedido
          var oBtnDel     = 'oBtnDel_Det'    + nSecuencia; // Boton de Borrar Row

          TD_xAll = cTableRow.insertCell(0);
          TD_xAll.style.width  = "20px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:20;text-align:left' name = 'cSec_Det"+nSecuencia+"' id = 'cSec_Det"+nSecuencia+"' value = '"+nSecuencia+"' readonly >";

          TD_xAll = cTableRow.insertCell(1);
          TD_xAll.style.width  = "100px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:100;text-align:left' name = '"+cCSeId+"' id = '"+cCSeId+"' "+cDisabled+" >";

          TD_xAll = cTableRow.insertCell(2);
          TD_xAll.style.width  = "80px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:80;text-align:left' name = '"+cCerdEst+"' id = '"+cCerdEst+"' "+cDisabled+" >";

          TD_xAll = cTableRow.insertCell(3);
          TD_xAll.style.width  = "80px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:80;text-align:left' name = '"+cSerSap+"' id = '"+cSerSap+"' onKeydown = 'javascript:if(event.keyCode == 13){ fnLinks(\"cSerSap\",\"VALID\",\""+nSecuencia+"\",\"_Det\") }' >";

          TD_xAll = cTableRow.insertCell(4);
          TD_xAll.style.width  = "100px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:100;text-align:left' name = '"+cSerDes+"' id = '"+cSerDes+"' readonly >";

          TD_xAll = cTableRow.insertCell(5);
          TD_xAll.innerHTML    = "<input type = 'hidden' name = '"+cSubId+"'>";

          TD_xAll = cTableRow.insertCell(6);
          TD_xAll.style.width  = "100px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:100;text-align:left' name = '"+cSubDes+"' id = '"+cSubDes+"' onKeydown = 'javascript:if(event.keyCode == 13){ fnLinks(\"cSubDes\",\"VALID\",\""+nSecuencia+"\",\"_Det\") }' >";

          TD_xAll = cTableRow.insertCell(7);
          TD_xAll.style.width  = "100px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:100;text-align:left' name = '"+cObfId+"' id = '"+cObfId+"' onKeydown = 'javascript:if(event.keyCode == 13){ fnLinks(\"cObfId\",\"VALID\",\""+nSecuencia+"\",\"_Det\") }' >";

          TD_xAll = cTableRow.insertCell(8);
          TD_xAll.style.width  = "80px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:80;text-align:left' name = '"+cUfaId+"' id = '"+cUfaId+"' onKeydown = 'javascript:if(event.keyCode == 13){ fnLinks(\"cUfaId\",\"VALID\",\""+nSecuencia+"\",\"_Det\") }' >";

          TD_xAll = cTableRow.insertCell(9);
          TD_xAll.innerHTML    = "<input type = 'hidden' name = '"+cCebId+"'>";

          TD_xAll = cTableRow.insertCell(10);
          TD_xAll.style.width  = "80px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:80;text-align:left' name = '"+cCebCod+"' id = '"+cCebCod+"' onKeydown = 'javascript:if(event.keyCode == 13){ fnLinks(\"cCebCod\",\"VALID\",\""+nSecuencia+"\",\"_Det\") }' >";

          TD_xAll = cTableRow.insertCell(11);
          TD_xAll.style.width  = "100px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:100;text-align:left' name = '"+cCebDes+"' id = '"+cCebDes+"' readonly >";

          TD_xAll = cTableRow.insertCell(12);
          TD_xAll.style.width  = "80px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:80;text-align:left' name = '"+cBase+"' id = '"+cBase+"' >";

          TD_xAll = cTableRow.insertCell(13);
          TD_xAll.style.width  = "60px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:60;text-align:left' name = '"+cTarifa+"' id = '"+cTarifa+"' >";

          TD_xAll = cTableRow.insertCell(14);
          TD_xAll.style.width  = "60px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:60;text-align:left' name = '"+cCalculo+"' id = '"+cCalculo+"' >";
        
          TD_xAll = cTableRow.insertCell(15);
          TD_xAll.style.width  = "60px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:60;text-align:left' name = '"+cMinima+"' id = '"+cMinima+"' >";
        
          TD_xAll = cTableRow.insertCell(16);
          TD_xAll.style.width  = "80px";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:80;text-align:left' name = '"+cVlrPedido+"' id = '"+cVlrPedido+"' onBlur = \"javascript:fnCalTotal();\">";

          TD_xAll = cTableRow.insertCell(17);
          TD_xAll.style.width  = "20px";
          if ((cTipoAccion == "MANUAL" || document.forms['frgrm']['cComTPed'].value == "MANUAL") && '<?php echo $_COOKIE['kModo'] ?>' != "VER") {
            TD_xAll.innerHTML    = "<input type = 'button' style = 'width:020;text-align:center' name = "+oBtnDel+" id = "+oBtnDel+" value = 'X' "+
                                  "onClick = 'javascript:fnDeleteRowDetalleCertificacionAuto(this.value,\""+nSecuencia+"\",\""+xTabla+"\");'>";
          } else {
            TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:20;text-align:left' disabled >";
          }

          document.forms['frgrm']['nSecuencia_Det'].value = nSecuencia;
        }
      }

      /**
       * Carga la grilla de servicios automaticos cuando se carga la MIF.
       */
      function fnCargarGrillaSubServicio() {
        var cCerIds  = "";
        var cAnioIds = "";
        for(var i=1;i<=document.forms['frgrm']['nSecuencia_Cert'].value;i++){
          cCerIds  += document.forms['frgrm']['cCerId_Certi'+i].value + "~";
          cAnioIds += document.forms['frgrm']['cCerAno_Certi'+i].value + "~";
        }
        cCerIds  = cCerIds.substring(0, cCerIds.length - 1);
        cAnioIds = cAnioIds.substring(0, cAnioIds.length - 1);

        var cRuta = "frpedgri.php?gCliId="+document.forms['frgrm']['cCliId'].value+
                                "&gCerIds="+cCerIds+
                                "&gAnioIds="+cAnioIds+
                                "&gTipo=1";

        parent.fmpro.location = cRuta;
      }

      /**
       * Permite eliminar una grilla de la seccion de certificacion paso 2 - Automatica.
       */
      function fnDeleteRowCertificacionAuto(xNumRow,xSecuencia,xTabla) {
        var cGrid = document.getElementById(xTabla);
        var nLastRow = cGrid.rows.length;
        if (xNumRow == "X") {
          if (confirm("Realmente Desea Eliminar La Secuencia ["+ xSecuencia +"]?")){ 
            if(xSecuencia < nLastRow){
              var j=0;
              for(var i=xSecuencia;i<nLastRow;i++){
                j = parseFloat(i)+1;

                document.forms['frgrm']['cSec_Certi'    + i].value     = i;
                document.forms['frgrm']['cCerId_Certi' + i].value      = document.forms['frgrm']['cCerId_Certi' + j].value;
                document.forms['frgrm']['cCerAno_Certi' + i].value     = document.forms['frgrm']['cCerAno_Certi' + j].value;
                document.forms['frgrm']['cCerComCsc_Certi'  + i].value = document.forms['frgrm']['cCerComCsc_Certi' + j].value;
                document.forms['frgrm']['cMifId_Certi' + i].value      = document.forms['frgrm']['cMifId_Certi' + j].value;
                document.forms['frgrm']['cMifIdAno_Certi'  + i].value  = document.forms['frgrm']['cMifIdAno_Certi' + j].value;
                document.forms['frgrm']['cMifComCsc_Certi' + i].value  = document.forms['frgrm']['cMifComCsc_Certi' + j].value;
                document.forms['frgrm']['cOrvSap_Certi' + i].value     = document.forms['frgrm']['cOrvSap_Certi' + j].value;
                document.forms['frgrm']['cOfvSap_Certi' + i].value     = document.forms['frgrm']['cOfvSap_Certi' + j].value;
                document.forms['frgrm']['cOfvDes_Certi' + i].value     = document.forms['frgrm']['cOfvDes_Certi' + j].value;
                document.forms['frgrm']['cCerFde_Certi' + i].value     = document.forms['frgrm']['cCerFde_Certi' + j].value;
                document.forms['frgrm']['cCerFha_Certi' + i].value     = document.forms['frgrm']['cCerFha_Certi' + j].value;
                document.forms['frgrm']['cDepNum_Certi' + i].value     = document.forms['frgrm']['cDepNum_Certi' + j].value;
                document.forms['frgrm']['cCcoIdOc_Certi' + i].value    = document.forms['frgrm']['cCcoIdOc_Certi' + j].value;
              }
            }
            cGrid.deleteRow(nLastRow - 1);
            document.forms['frgrm']['nSecuencia_Cert'].value = nLastRow - 1;
          }
        }
      }

        /**
       * Permite eliminar una grilla de la seccion de certificacion paso 2 - Manual.
       */
      function fnDeleteRowCertificacionManual(xNumRow,xSecuencia,xTabla) {
        var cGrid = document.getElementById(xTabla);
        var nLastRow = cGrid.rows.length;
        if (xNumRow == "X") {
          if (confirm("Realmente Desea Eliminar La Secuencia ["+ xSecuencia +"]?")){ 
            if(xSecuencia < nLastRow){
              var j=0;
              for(var i=xSecuencia;i<nLastRow;i++){
                j = parseFloat(i)+1;

                document.forms['frgrm']['cSec_Certi'    + i].value  = i;
                document.forms['frgrm']['cOrvSap_Certi' + i].value  = document.forms['frgrm']['cOrvSap_Certi' + j].value;
                document.forms['frgrm']['cOrvDes_Certi' + i].value  = document.forms['frgrm']['cOrvDes_Certi' + j].value;
                document.forms['frgrm']['cOfvSap_Certi' + i].value  = document.forms['frgrm']['cOfvSap_Certi' + j].value;
                document.forms['frgrm']['cOfvDes_Certi' + i].value  = document.forms['frgrm']['cOfvDes_Certi' + j].value;
                document.forms['frgrm']['cCloSap_Certi' + i].value  = document.forms['frgrm']['cCloSap_Certi' + j].value;
                document.forms['frgrm']['cCloDes_Certi' + i].value  = document.forms['frgrm']['cCloDes_Certi' + j].value;
                document.forms['frgrm']['cSecSap_Certi' + i].value  = document.forms['frgrm']['cSecSap_Certi' + j].value;
                document.forms['frgrm']['cSecDes_Certi' + i].value  = document.forms['frgrm']['cSecDes_Certi' + j].value;
                document.forms['frgrm']['cCdiSap_Certi' + i].value  = document.forms['frgrm']['cCdiSap_Certi' + j].value;
                document.forms['frgrm']['cCdiDes_Certi' + i].value  = document.forms['frgrm']['cCdiDes_Certi' + j].value;
                document.forms['frgrm']['cTdeId_Certi' + i].value   = document.forms['frgrm']['cTdeId_Certi' + j].value;
                document.forms['frgrm']['cTdeDes_Certi' + i].value  = document.forms['frgrm']['cTdeDes_Certi' + j].value;
                document.forms['frgrm']['cDepNum_Certi' + i].value  = document.forms['frgrm']['cDepNum_Certi' + j].value;
                document.forms['frgrm']['cCerFde_Certi' + i].value  = document.forms['frgrm']['cCerFde_Certi' + j].value;
                document.forms['frgrm']['cCerFha_Certi' + i].value  = document.forms['frgrm']['cCerFha_Certi' + j].value;
                document.forms['frgrm']['cCcoIdOc_Certi' + i].value = document.forms['frgrm']['cCcoIdOc_Certi' + j].value;            
              }
            }
            cGrid.deleteRow(nLastRow - 1);
            document.forms['frgrm']['nSecuencia_Cert'].value = nLastRow - 1;
          }
        }
      }

      /**
       * Permite eliminar una grilla de la seccion de certificacion paso 2.
       */
      function fnDeleteRowDetalleCertificacionAuto(xNumRow,xSecuencia,xTabla) {
        var cGrid = document.getElementById(xTabla);
        var nLastRow = cGrid.rows.length;
        if (xNumRow == "X") {
          if (confirm("Realmente Desea Eliminar La Secuencia ["+ xSecuencia +"]?")){ 
            if(xSecuencia < nLastRow){
              var j=0;
              for(var i=xSecuencia;i<nLastRow;i++){
                j = parseFloat(i)+1;

                document.forms['frgrm']['cSec_Certi'    + i].value  = i;
                document.forms['frgrm']['cCSeId_Det' + i].value     = document.forms['frgrm']['cCSeId_Det' + j].value;
                document.forms['frgrm']['cCerdEst_Det' + i].value   = document.forms['frgrm']['cCerdEst_Det' + j].value;
                document.forms['frgrm']['cSerSap_Det'  + i].value   = document.forms['frgrm']['cSerSap_Det' + j].value;
                document.forms['frgrm']['cSerDes_Det' + i].value    = document.forms['frgrm']['cSerDes_Det' + j].value;
                document.forms['frgrm']['cSubId_Det'  + i].value    = document.forms['frgrm']['cSubId_Det' + j].value;
                document.forms['frgrm']['cSubDes_Det' + i].value    = document.forms['frgrm']['cSubDes_Det' + j].value;
                document.forms['frgrm']['cDepNum_Det' + i].value    = document.forms['frgrm']['cDepNum_Det' + j].value;
                document.forms['frgrm']['cObfId_Det' + i].value     = document.forms['frgrm']['cObfId_Det' + j].value;
                document.forms['frgrm']['cUfaId_Det' + i].value     = document.forms['frgrm']['cUfaId_Det' + j].value;
                document.forms['frgrm']['cCebId_Det' + i].value     = document.forms['frgrm']['cCebId_Det' + j].value;
                document.forms['frgrm']['cCebDes_Det' + i].value    = document.forms['frgrm']['cCebDes_Det' + j].value;
                document.forms['frgrm']['cBase_Det' + i].value      = document.forms['frgrm']['cBase_Det' + j].value;
                document.forms['frgrm']['cTarifa_Det' + i].value    = document.forms['frgrm']['cTarifa_Det' + j].value;
                document.forms['frgrm']['cCalculo_Det' + i].value   = document.forms['frgrm']['cCalculo_Det' + j].value;
                document.forms['frgrm']['cMinima_Det' + i].value    = document.forms['frgrm']['cMinima_Det' + j].value;
                document.forms['frgrm']['cVlrPedido_Det' + i].value = document.forms['frgrm']['cVlrPedido_Det' + j].value;
              }
            }
            cGrid.deleteRow(nLastRow - 1);
            document.forms['frgrm']['nSecuencia_Det'].value = nLastRow - 1;
          }
        }
      }

      /**
       * Imprime el Excel de la certificacion seleccionada en el paso 2.
       */
      function fnImprimirCertificacion(xCerId, xAnio) {
        parent.fmpro.location = "../certifix/frcerprn.php?cCerId="+xCerId+"&cAnio="+xAnio;
      }

      /**
       * Valida si cambia el tipo de pedido para eliminar todos los registros de una grilla.
       */
      function fnChangeTipoPedido() {
        document.getElementById('Grid_Certificacion_Auto').innerHTML    = "";
        document.getElementById('Grid_Certificacion_Manual').innerHTML  = "";
        document.getElementById('Grid_Detalle_Certificacion').innerHTML = "";
      }

      /**
       * Valida las fechas seleccionadas.
       */
      function chDate(fld){
        var val = fld.value;

        if (val.length > 0) {
          var ok = 1;
          if (val.length < 10) {
            alert('Formato de Fecha debe ser aaaa-mm-dd');
            fld.value = '';
            fld.focus();
            ok = 0;
          }
          
          if (val.substr(4,1) == '-' && val.substr(7,1) == '-' && ok == 1) {
            var anio = val.substr(0,4);
            var mes  = val.substr(5,2);
            var dia  = val.substr(8,2);
            if (mes.substr(0,1) == '0'){
              mes = mes.substr(1,1);
            }
            
            if (dia.substr(0,1) == '0'){
              dia = dia.substr(1,1);
            }
            
            if(mes > 12){
              alert('El mes debe ser menor a 13');
              fld.value = '';
              fld.focus();
            }
            
            if (dia > 31){
              alert('El dia debe ser menor a 32');
              fld.value = '';
              fld.focus();
            }
            
            var aniobi = 28;
            
            if(anio % 4 ==  0){
              aniobi = 29;
            }
            
            if (mes == 4 || mes == 6 || mes == 9 || mes == 11){
              if (dia < 1 || dia > 30){
                alert('El dia debe ser menor a 31, dia queda en 30');
                fld.value = val.substr(0,8)+'30';
              }
            }
            
            if (mes == 1 || mes == 3 || mes == 5 || mes == 7 || mes == 8 || mes == 10 || mes == 12){
              if (dia < 1 || dia > 32){
                alert('El dia debe ser menor a 32');
                fld.value = '';
                fld.focus();
              }
            }

            if(mes == 2 && aniobi == 28 && dia > 28 ){
              alert('El dia debe ser menor a 29');
              fld.value = '';
              fld.focus();
            }
            
            if(mes == 2 && aniobi == 29 && dia > 29){
              alert('El dia debe ser menor a 30');
              fld.value = '';
              fld.focus();
            }
          } else{
            if(val.length > 0){
              alert('Fecha erronea, verifique');
            }
            
            fld.value = '';
            fld.focus();
          }
        }
      }

      /**
       * Validaciones del formulario.
       */
      function fnValidaciones(xStep, xTipoPedido) {
        var cMjs = "";
        switch (xStep) {
          case "2":
            // Validaciones del paso 1
            if (document.forms['frgrm']['cComId'].value == "") {
              cMjs += "Debe seleccionar el Id del Comprobante,\n";
            } else {
              // Si el tipo de comprobante es Manual debe ingresar el Consecutivo
              if (document.forms['frgrm']['cComTCo'].value == "MANUAL" && document.forms['frgrm']['cComCsc'].value == '') {
                cMjs += "El Consecutivo del Pedido no puede ser vacio,\n";
              }
            }

            if (document.forms['frgrm']['cOfvSap'].value == "") {
              cMjs += "Debe seleccionar la Oficina de Venta,\n";
            }

            if (document.forms['frgrm']['cCliId'].value == "") {
              cMjs += "Debe seleccionar el Cliente,\n";
            }

            if (document.forms['frgrm']['dComFec'].value == "") {
              cMjs += "Debe seleccionar la Fecha de creacion,\n";
            }
          break;
          case "3":
            // Validaciones del paso 2 - AUTOMATICA
            if (xTipoPedido == "AUTOMATICA") {
              for(var i=1;i<=document.forms['frgrm']['nSecuencia_Cert'].value;i++){
                if (document.forms['frgrm']['cCerComCsc_Certi' + i].value == "") {
                  cMjs += "La Certificacion no puede ser vacia. Secuencia ["+i+"],\n";
                }
              }
            }

            // Validaciones del paso 2 - MANUAL
            if (xTipoPedido == "MANUAL") {
              for(var i=1;i<=document.forms['frgrm']['nSecuencia_Cert'].value;i++){
                if (document.forms['frgrm']['cOrvSap_Certi' + i].value == "") {
                  cMjs += "La Organizacion de Venta no puede ser vacia. Secuencia ["+i+"],\n";
                }

                if (document.forms['frgrm']['cOfvSap_Certi' + i].value == "") {
                  cMjs += "La Oficina de Venta no puede ser vacia. Secuencia ["+i+"],\n";
                }

                if (document.forms['frgrm']['cCloSap_Certi' + i].value == "") {
                  cMjs += "El Centro Logistico no puede ser vacio. Secuencia ["+i+"],\n";
                }

                if (document.forms['frgrm']['cSecSap_Certi' + i].value == "") {
                  cMjs += "El Sector no puede ser vacio. Secuencia ["+i+"],\n";
                }

                if (document.forms['frgrm']['cCdiSap_Certi' + i].value == "") {
                  cMjs += "El Canal de Distribucion no puede ser vacio. Secuencia ["+i+"],\n";
                }

                if (document.forms['frgrm']['cTdeId_Certi' + i].value == "") {
                  cMjs += "El Tipo de Deposito no puede ser vacio. Secuencia ["+i+"],\n";
                }

                if (document.forms['frgrm']['cDepNum_Certi' + i].value == "") {
                  cMjs += "El Numero de Deposito no puede ser vacio. Secuencia ["+i+"],\n";
                }

                if (document.forms['frgrm']['cCcoIdOc_Certi' + i].value == "") {
                  cMjs += "La Oferta Comercial no puede ser vacia. Secuencia ["+i+"],\n";
                }
              }
            }
          break;
        }

        return cMjs;
      }

      /**
       * Permite generar la vista previa el Pedido.
       */
      function fnVistaPrevia() {
        var cRuta = 'frpedpre.php';
        document.forms['frgrm'].action = cRuta;
			  document.forms['frgrm'].target = '_blank';
        document.forms['frgrm'].submit();
      }

      /**
       * Permite realizar el Pedido.
       */
      function fnGuardar() {
        document.forms['frgrm'].action='frpedgra.php';
        document.forms['frgrm'].target='fmpro';
        document.forms['frgrm'].submit();
      }

      /**
       * Realiza el calulo automatico del valor Total del Pedido
       */
      function fnCalTotal() {
        // Sumando los valores de la girllla
        var nTotPedido = 0;

        for (let n = 1; n <= parseInt(document.forms['frgrm']['nSecuencia_Det'].value); n++) {
          console.log(n);
          // Total del Pedido por grilla
          if (document.forms['frgrm']['cVlrPedido_Det'+n].value != "") {
            nTotPedido = nTotPedido + parseFloat(document.forms['frgrm']['cVlrPedido_Det'+n].value);
          }
        }

        document.forms['frgrm']['nTotPedido'].value = nTotPedido;
      }

    </script>
  </head>
  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
    <center>
      <table border="0" cellpadding="0" cellspacing="0" width="1200">
        <tr>
          <td>
            <form name = "frestado" action = "frpedgra.php" method = "post" target="fmpro">
              <input type = "hidden" name = "cPedId" value = "">
              <input type = "hidden" name = "cAnio"  value = "">
            </form>

            <form name = "frgrm" action = "frpedgra.php" method = "post" target="fmpro">
              <input type = "hidden" name = "cPedId"          value = "<?php echo $cPedId ?>">
              <input type = "hidden" name = "cAnio"           value = "<?php echo $cAnio ?>">
              <input type = "hidden" name = "cRegEst"         value = "">
              <input type = "hidden" name = "nSecuencia_Cert" value = "<?php echo $_POST['nSecuencia_Cert']  ?>">
              <input type = "hidden" name = "nSecuencia_Det"  value = "<?php echo $_POST['nSecuencia_Det']  ?>">

              <fieldset>
                <legend style="color:#FF0000" id="idTitulo">Wizard de Pedido - Paso 1</legend>

                <!-- Paso - 1 -->
                <!-- Datos generales -->
                <fieldset id="Datos_del_Comprobante">
                  <legend><b>Ciudad y Tipo de Pedido</b></legend>
                  <center>
                    <table border="0" cellpadding="0" cellspacing="0" width="1200">
                      <?php $nCol = f_Format_Cols(60); echo $nCol; ?>
                      <tr>
                        <!-- Fila 1 -->
                        <td Class="clase08" colspan="1">Id<br>
                          <input type = "text" Class = "letra" style = "width:20" name = "cComId" readonly>
                        </td>
                        <td Class="clase08" colspan="4">
                          <a href = "javascript:document.forms['frgrm']['cComPre'].value = '';
                                                document.forms['frgrm']['cComId'].value  = '';
                                                document.forms['frgrm']['cComCod'].value = '';
                                                document.forms['frgrm']['cComTCo'].value = '';
                                                document.forms['frgrm']['cComCsc'].value = '';
                                                document.forms['frgrm']['cComCco'].value = '';
                                                fnLinks('cComPre','VALID')" id = "id_href_cCompre">Prefijo</a><br>
                          <input type = 'text' Class = 'letra' style = 'width:80' name = 'cComPre' maxlength="3"
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                fnLinks('cComPre','VALID');
                                                this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                            onFocus = "javascript:document.forms['frgrm']['cComPre'].value = '';
                                                  document.forms['frgrm']['cComId'].value  = '';
                                                  document.forms['frgrm']['cComCod'].value = '';
                                                  document.forms['frgrm']['cComTCo'].value = '';
                                                  document.forms['frgrm']['cComCsc'].value = '';
                                                  document.forms['frgrm']['cComCco'].value = '';
                                                  this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                          <input type="hidden" name="cComCod">
                          <input type="hidden" name="cComTCo">
                          <input type="hidden" name="cComCco">
                        </td>
                        <td class="clase08" colspan="8">Consecutivo<br>
                          <input type = "text" Class = "letra" style = "width:160" name = "cComCsc" maxlength="6"
                            onBlur = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                            onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'
                                                fnHabilitaConsecutivo(cComTCo);">
                          <input type = "hidden" name = "cComCsc2" readonly>
                        </td>

                        <td Class = "clase08" colspan="7">
                          <a href = "javascript:document.forms['frgrm']['cOfvSap'].value = '';
                                                document.forms['frgrm']['cOfvDes'].value = '';
                                                fnLinks('cOfvSap','VALID')" id = "id_href_cOfvSap">Cod. SAP</a><br>
                          <input type = 'text' Class = 'letra' style = 'width:140' name = 'cOfvSap' maxlength="2"
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                fnLinks('cOfvSap','VALID');
                                                this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                            onFocus = "javascript:document.forms['frgrm']['cOfvSap'].value = '';
                                                  document.forms['frgrm']['cOfvDes'].value = '';
                                                  this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                        </td>
                        <td class="clase08" colspan="1">&nbsp; <br>
                          <input type = "text" Class = "letra" style = "width:20;text-align:center" readonly>
                        </td>
                        <td class="clase08" colspan="17">Oficina Ventas<br>
                          <input type = 'text' Class = 'letra' style = 'width:340' name = "cOfvDes"
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                  fnLinks('cOfvDes','VALID');
                                                  this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                            onFocus="javascript:document.forms['frgrm']['cOfvSap'].value ='';
                                                document.forms['frgrm']['cOfvDes'].value = '';
                                                document.forms['frgrm']['cCloDes'].value = '';
                                                this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                        </td>
                        <td Class = "name" colspan = "8">Tipo de Pedido<br>
                          <select Class = "letrase" name = "cComTPed" value = "<?php if($_POST['cComTPed'] == ""){echo "AUTOMATICA";}else{echo $_POST['cComTPed'];} ?>" style = "width:160" onchange="javascript:fnChangeTipoPedido()">
                            <option value = "AUTOMATICA">AUTOMATICA</option>
                            <option value = "MANUAL">MANUAL</option>
                          </select>
                          <script language="javascript">
                            document.forms['frgrm']['cComTPed'].value="<?php if($_POST['cComTPed'] == ""){echo "AUTOMATICA";}else{echo $_POST['cComTPed'];} ?>";
                          </script>
                        </td>
                        <input type = "hidden" name = "cComTPed_Hd">
                        <td Class = "name" colspan = "8">Tipo de Consecutivo<br>
                          <select Class = "letrase" name = "cComTipCsc" style = "width:160">
                            <option value = "PREFACTURA" selected >PREFACTURA</option>
                          </select>
                        </td>
                        <td Class = "name" colspan = "6"><a href="#" id="id_href_dComFec">Fecha Creaci&oacute;n</a><br>
                          <input type = "text" style = "width:120;text-align:center" name = "dComFec" readonly>
                          <script language="javascript">
                              document.forms['frgrm']['dComFec'].value = "<?php echo $_POST['dComFec'] ?>";
                              document.getElementById('id_href_dComFec').href = "javascript:show_calendar('frgrm.dComFec')";
                          </script>
                        </td>
                      </tr>
								    </table>
                  </center>
                </fieldset>

                <!-- Datos del cliente -->
                <fieldset id="Datos_del_Cliente">
                  <legend><b>Datos del Cliente</b></legend>
                  <center>
                    <table border = "0" cellpadding = "0" cellspacing = "0" width = "1200">
                      <?php $cCols = f_Format_Cols(60); echo $cCols; ?>
                      <tr>
                        <td Class="clase08" colspan="6">
                          <a href = "javascript:document.forms['frgrm']['cCliId'].value  = '';
                                                document.forms['frgrm']['cCliNom'].value = '';
                                                document.forms['frgrm']['cCliDV'].value  = '';
                                                document.forms['frgrm']['cCliSap'].value  = '';
                                                fnLinks('cCliId','VALID')" id = "id_href_CliId">Nit</a><br>
                          <input type = "text" Class = "letra" style = "width:120" name = "cCliId" maxlength="20"
                            onBlur = "javascript:fnLinks('cCliId','VALID');
                                                this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                            onFocus = "javascript:document.forms['frgrm']['cCliId'].value  = '';
                                                  document.forms['frgrm']['cCliNom'].value = '';
                                                  document.forms['frgrm']['cCliDV'].value  = '';
                                                  document.forms['frgrm']['cCliSap'].value = '';
                                                  this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                        </td>
                        <input type = "hidden" name = "cCliId_Oculto">
                        <td class="clase08" colspan="7">Cod. Sap<br>
                          <input type = "text" Class = "letra" style = "width:140;" name = "cCliSap" readonly>
                        </td>
                        <td class="clase08" colspan="1">Dv<br>
                          <input type = "text" Class = "letra" style = "width:20;text-align:center" name = "cCliDV" readonly>
                        </td>
                        <td class="clase08" colspan="37">Cliente<br>
                          <input type = "text" Class = "letra" style = "width:740" name = "cCliNom"
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                  fnLinks('cCliNom','VALID');
                                                  this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                            onFocus="javascript:document.forms['frgrm']['cCliId'].value  ='';
                                                document.forms['frgrm']['cCliNom'].value = '';
                                                document.forms['frgrm']['cCliDV'].value  = '';
                                                document.forms['frgrm']['cCliSap'].value = '';
                                                this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                        </td>
                        <td class="clase08" colspan="1"><br>
                          <input type = "text" Class = "letra" style = "width:20;text-align:center" readonly>
                        </td>
                        <td Class = "name" colspan = "8">Consolidado<br>
                          <select Class = "letrase" name = "cComConso" value = "" style = "width:160">
                            <option value = "SI" selected>SI</option>
                            <option value = "NO">NO</option>
                          </select>
                        </td>
                      </tr>
                    </table>
                  </center>
                </fieldset>

                <!-- Paso - 2 -->
                <!-- Grilla de Certificaciones Automaticos -->
                <fieldset id="fieldset_de_Certificaciones_Automatico">
                  <legend><b>Relaci&oacute;n de Certificaciones <?php echo $_COOKIE['kModo']  ?></b></legend>
                  <center>
                    <table border = "0" cellpadding = "0" cellspacing = "0" width = "1200">
                      <?php $cCols = f_Format_Cols(60); echo $cCols; ?>
                      <tr>
                        <td colspan="60" class= "clase08" align="right">
                          <?php if ($_COOKIE['kModo'] != "VER") { ?>
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_create-dir_bg.gif" id="idAddNewRowCertificacion" onClick = "javascript:fnAddNewRowCertificacionAuto('Grid_Certificacion_Auto','NUEVO')" style = "cursor:pointer" title="Adicionar">
                          <?php } ?>
                        </td>
                      </tr>
                      <tr>
                        <td class = "clase08" colspan="01" align="center">Sec</td>
                        <td class = "clase08" colspan="10" align="center">Certificaci&oacute;n</td>
                        <td class = "clase08" colspan="10" align="center">M.I.F</td>
                        <td class = "clase08" colspan="08" align="center">Oficinas Ventas</td>
                        <td class = "clase08" colspan="07" align="center">Fecha Desde</td>
                        <td class = "clase08" colspan="07" align="center">Fecha Hasta</td>
                        <td class = "clase08" colspan="08" align="center">No. Deposito</td>
                        <td class = "clase08" colspan="08" align="center">Oferta Comercial</td>
                        <td class = "clase08" colspan="01" align="right">&nbsp;</td>
                      </tr>
                    </table>
                    <table border = "0" cellpadding = "0" cellspacing = "0" width = "1200" id = "Grid_Certificacion_Auto"></table>
                  </center>
                </fieldset>

                <!-- Grilla de Certificaciones Manual -->
                <fieldset id="fieldset_de_Certificaciones_Manual">
                  <legend><b>Relaci&oacute;n de Certificaciones</b></legend>
                  <center>
                    <table border = "0" cellpadding = "0" cellspacing = "0" width = "1200">
                      <?php $cCols = f_Format_Cols(60); echo $cCols; ?>
                      <tr>
                        <td colspan="60" class= "clase08" align="right">
                          <?php if ($_COOKIE['kModo'] != "VER") { ?>
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_create-dir_bg.gif" onClick = "javascript:fnAddNewRowCertificacionManual('Grid_Certificacion_Manual','NUEVO')" style = "cursor:pointer" title="Adicionar">
                          <?php } ?>
                        </td>
                      </tr>
                      <tr>
                        <td class = "clase08" colspan="01" align="center">Sec</td>
                        <td class = "clase08" colspan="06" align="center">Organizaci&oacute;n Ventas</td>
                        <td class = "clase08" colspan="06" align="center">Oficina Ventas</td>
                        <td class = "clase08" colspan="06" align="center">Centro Log&iacute;stico</td>
                        <td class = "clase08" colspan="06" align="center">Sector</td>
                        <td class = "clase08" colspan="06" align="center">Canal</td>
                        <td class = "clase08" colspan="06" align="center">Tipo Deposito</td>
                        <td class = "clase08" colspan="06" align="center">No. Deposito</td>
                        <td class = "clase08" colspan="05" align="center">Fecha Desde</td>
                        <td class = "clase08" colspan="05" align="center">Fecha Hasta</td>
                        <td class = "clase08" colspan="06" align="center">Oferta Comercial</td>
                        <td class = "clase08" colspan="01" align="right">&nbsp;</td>
                      </tr>
                    </table>
                    <table border = "0" cellpadding = "0" cellspacing = "0" width = "1200" id = "Grid_Certificacion_Manual"></table>
                  </center>
                </fieldset>

                <!-- Paso - 3 -->
                <!-- Grilla de Detalle Certificaciones -->
                <fieldset id="fieldset_de_Detalle_Certificaciones">
                  <legend><b>Relaci&oacute;n de Certificaciones</b></legend>
                  <center>
                    <table border = "0" cellpadding = "0" cellspacing = "0" width = "1200">
                      <?php $cCols = f_Format_Cols(60); echo $cCols; ?>
                      <tr>
                        <td colspan="60" class= "clase08" align="right">
                          <?php if ($_COOKIE['kModo'] != "VER") { ?>
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_create-dir_bg.gif" onClick = "javascript:fnAddNewRowDetalleCerticacion('Grid_Detalle_Certificacion','MANUAL')" style = "cursor:pointer" title="Adicionar">
                          <?php } ?>
                        </td>
                      </tr>
                      <tr>
                        <td class = "clase08" colspan="01" align="center">Sec</td>
                        <td class = "clase08" colspan="05" align="center">No. Cond Servicio</td>
                        <td class = "clase08" colspan="04" align="center">Estatus</td>
                        <td class = "clase08" colspan="04" align="center">Cod. Sap Servicio</td>
                        <td class = "clase08" colspan="05" align="center">Descripci&oacute;n</td>
                        <td class = "clase08" colspan="05" align="center">Sub-servicio</td>
                        <td class = "clase08" colspan="05" align="center">Objeto Facturable</td>
                        <td class = "clase08" colspan="04" align="center">Unidad Facturable</td>
                        <td class = "clase08" colspan="04" align="center">CEBE</td>
                        <td class = "clase08" colspan="05" align="center">Descripci&oacute;n Cebe</td>
                        <td class = "clase08" colspan="04" align="center">Base</td>
                        <td class = "clase08" colspan="03" align="center">Tarifa</td>
                        <td class = "clase08" colspan="03" align="center">Calculo</td>
                        <td class = "clase08" colspan="03" align="center">M&iacute;nima</td>
                        <td class = "clase08" colspan="04" align="center">Vr. Pedido</td>
                        <td class = "clase08" colspan="01" align="right">&nbsp;</td>
                      </tr>
                    </table>
                    <table border = "0" cellpadding = "0" cellspacing = "0" width = "1200" id = "Grid_Detalle_Certificacion"></table>

                    <br><br>
                    <table border = "0" cellpadding = "0" cellspacing = "0" width = "1200">
                      <?php $cCols = f_Format_Cols(60); echo $cCols; ?>
                      <tr>
                        <td Class = "name" colspan = "47">Observaciones Generales [Maximo 200 Caracteres]<br>
                          <textarea Class = "letrata" name="cPedObs" style="width:940;height:35;overflow:auto"
                            onBlur = "javascript:this.value=this.value.toUpperCase();">
                          </textarea>
                          <script language="javascript">
                            document.forms['frgrm']['cPedObs'].value="<?php echo $_POST['cPedObs'] ?>";
                          </script>
                        </td>
                        <td Class = "name" colspan = "02">&nbsp;</td>
                        <td Class = "name" colspan = "05">TOTAL PEDIDO</td>
                        <td Class = "name" colspan = "05">
                          <input type = "text" Class = "letra" style = "width:100;" name = "nTotPedido" readonly>
                        </td>
                      </tr>
                    </table>
                  </center>
                </fieldset>

              </fieldset>
            </form>
          </td>
        </tr>
      </table>
    </center>
    <center>
      <table border="0" cellpadding="0" cellspacing="0" width="1200">
        <tr id="idBtnPaso1">
          <?php if ($_COOKIE['kModo'] != "VER") { ?>
            <td width="1018" height="21"></td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/siguiente.gif" style="cursor:pointer"
              onClick = "javascript:fnMostrarOcultarObjetos('2', 'siguiente');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Siguiente
            </td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand"
              onClick ="javascript:fnSalir()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
            </td>
          <?php } ?>
        </tr>

        <tr id="idBtnPaso2">
          <?php if ($_COOKIE['kModo'] != "VER") { ?>
            <td width="1018" height="21"></td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/anterior.gif" style="cursor:pointer"
              onClick = "javascript:fnMostrarOcultarObjetos('1');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Anterior</td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/siguiente.gif" style="cursor:pointer"
              onClick = "javascript:fnMostrarOcultarObjetos('3', 'siguiente');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Siguiente
            </td>
          <?php } ?>
        </tr>

        <tr id="idBtnPaso3">
          <?php if ($_COOKIE['kModo'] != "VER") { ?>
            <td width="836" height="21"></td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/anterior.gif" style="cursor:pointer"
            onClick = "javascript:fnMostrarOcultarObjetos('2');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Anterior</td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_print_bg.gif" style="cursor:pointer"
                onClick = "javascript:fnVistaPrevia()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;V. Previa</td>

            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:fnGuardar();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand"
              onClick ="javascript:fnSalir()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
            </td>
          <?php } else { ?>
            <td width="1109" height="21"></td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand"
              onClick ="javascript:fnSalir()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
            </td>
          <?php } ?>
        </tr>


      </table>
    </center>

    <!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
    <?php switch ($_COOKIE['kModo']) {
      case "NUEVO":
        ?>
        <script languaje = "javascript">
          fnMostrarOcultarObjetos("1");
        </script>
        <?php
      break;
      case "EDITAR":
        fnCargaData($cPedId,$cAnio);

        ?>
        <script languaje = "javascript">
          fnMostrarOcultarObjetos("1");

          // Deshabilito los campos de cabecera
          document.forms['frgrm']['cComPre'].readOnly   = true;
          document.forms['frgrm']['cComPre'].onfocus    = "";
          document.forms['frgrm']['cComPre'].onblur     = "";
          document.forms['frgrm']['cComCsc'].readOnly   = true;
          document.forms['frgrm']['cComCsc'].onfocus    = "";
          document.forms['frgrm']['cComCsc'].onblur     = "";
          document.forms['frgrm']['cOfvSap'].readOnly   = true;
          document.forms['frgrm']['cOfvSap'].onfocus    = "";
          document.forms['frgrm']['cOfvSap'].onblur     = "";
          document.forms['frgrm']['cOfvDes'].readOnly   = true;
          document.forms['frgrm']['cOfvDes'].onfocus    = "";
          document.forms['frgrm']['cOfvDes'].onblur     = "";
          document.forms['frgrm']['cCliId'].readOnly    = true;
          document.forms['frgrm']['cCliId'].onfocus     = "";
          document.forms['frgrm']['cCliId'].onblur      = "";
          document.forms['frgrm']['cCliNom'].readOnly   = true;
          document.forms['frgrm']['cCliNom'].onfocus    = "";
          document.forms['frgrm']['cCliNom'].onblur     = "";

          document.forms['frgrm']['cComTPed'].disabled   = true;
          document.forms['frgrm']['cComTipCsc'].disabled = true;
          document.forms['frgrm']['cComConso'].disabled  = true;

          // Deshabilito los link de los Valid/Windows
          document.getElementById('id_href_cCompre').removeAttribute('href');
          document.getElementById('id_href_CliId').removeAttribute('href');
          document.getElementById('id_href_cOfvSap').removeAttribute('href');
          document.getElementById('id_href_dComFec').removeAttribute('href');
        </script>
      <?php      
      break;
      case "VER": 
        fnCargaData($cPedId,$cAnio);
        ?>
        <script languaje = "javascript">
          for (x=0;x<document.forms['frgrm'].elements.length;x++) {
            document.forms['frgrm'].elements[x].readOnly = true;
            document.forms['frgrm'].elements[x].onfocus  = "";
            document.forms['frgrm'].elements[x].onblur   = "";
          }
          document.forms['frgrm']['cComTPed'].disabled   = true;
          document.forms['frgrm']['cComTipCsc'].disabled = true;
          document.forms['frgrm']['cComConso'].disabled  = true;

          // Deshabilita los link de los Valid/Windows
          document.getElementById('id_href_cCompre').removeAttribute('href');
          document.getElementById('id_href_CliId').removeAttribute('href');
          document.getElementById('id_href_cOfvSap').removeAttribute('href');
          document.getElementById('id_href_dComFec').removeAttribute('href');
        </script>
      <?php
      break;
    }

    function fnCargaData($gPedId, $gAnio){
      global $cAlfa; global $xConexion01;
      
      // Consulta la informacion de Cabecera
      $qPedido  = "SELECT ";
      $qPedido .= "$cAlfa.lpca$gAnio.*, ";
      $qPedido .= "IF($cAlfa.lpar0150.clinomxx != \"\",$cAlfa.lpar0150.clinomxx,REPLACE(CONCAT($cAlfa.lpar0150.clinom1x,\" \",$cAlfa.lpar0150.clinom2x,\" \",$cAlfa.lpar0150.cliape1x,\" \",$cAlfa.lpar0150.cliape2x), \"  \", \" \")) AS clinomxx, ";
      $qPedido .= "$cAlfa.lpar0150.clisapxx ";
      $qPedido .= "FROM $cAlfa.lpca$gAnio ";
      $qPedido .= "LEFT JOIN $cAlfa.lpar0150 ON $cAlfa.lpca$gAnio.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
      $qPedido .= "WHERE ";
      $qPedido .= "pedidxxx = \"$gPedId\" LIMIT 0,1";
      $xPedido  = f_MySql("SELECT","",$qPedido,$xConexion01,"");
      // echo $qPedido;

      if (mysql_num_rows($xPedido) > 0) {
        $vPedido = mysql_fetch_array($xPedido);

        // Consulta la oficina de venta de cabecera
        $qOfiVenta  = "SELECT ";
        $qOfiVenta .= "ofvsapxx, ";
        $qOfiVenta .= "ofvdesxx ";
        $qOfiVenta .= "FROM $cAlfa.lpar0002 ";
        $qOfiVenta .= "WHERE ";
        $qOfiVenta .= "ofvsapxx = \"{$vPedido['ofvsapxx']}\" LIMIT 0,1 ";
        $xOfiVenta  = f_MySql("SELECT","",$qOfiVenta,$xConexion01,"");
        $vOfiVenta = array();
        if (mysql_num_rows($xOfiVenta) > 0) {
          $vOfiVenta = mysql_fetch_array($xOfiVenta);
        }

        // Consulta la informacion de Detalle
        $qDetPedido  = "SELECT ";
        $qDetPedido .= "$cAlfa.lpde$gAnio.* ";
        $qDetPedido .= "FROM $cAlfa.lpde$gAnio ";
        $qDetPedido .= "WHERE ";
        $qDetPedido .= "pedidxxx = \"$gPedId\"";
        $xDetPedido  = f_MySql("SELECT","",$qDetPedido,$xConexion01,"");
        ?>
        <script language = "javascript">
          // Paso 1
          document.forms['frgrm']['cComId'].value     = "<?php echo $vPedido['comidxxx'] ?>";
          document.forms['frgrm']['cComCod'].value    = "<?php echo $vPedido['comcodxx'] ?>";
          document.forms['frgrm']['cComPre'].value    = "<?php echo $vPedido['comprexx'] ?>";
          document.forms['frgrm']['cComCsc'].value    = "<?php echo $vPedido['comcscxx'] ?>";
          document.forms['frgrm']['cOfvSap'].value    = "<?php echo $vOfiVenta['ofvsapxx'] ?>";
          document.forms['frgrm']['cOfvDes'].value    = "<?php echo $vOfiVenta['ofvdesxx'] ?>";
          document.forms['frgrm']['cComTPed'].value   = "<?php echo $vPedido['pedtipxx'] ?>";
          document.forms['frgrm']['cComTPed_Hd'].value= "<?php echo $vPedido['pedtipxx'] ?>";
          document.forms['frgrm']['dComFec'].value    = "<?php echo $vPedido['comfecxx'] ?>";
          document.forms['frgrm']['cCliId'].value     = "<?php echo $vPedido['cliidxxx'] ?>";
          document.forms['frgrm']['cCliDV'].value     = "<?php echo gendv($vPedido['cliidxxx']) ?>";
          document.forms['frgrm']['cCliNom'].value    = "<?php echo $vPedido['clinomxx'] ?>";
          document.forms['frgrm']['cCliSap'].value    = "<?php echo $vPedido['clisapxx'] ?>";
          document.forms['frgrm']['cComConso'].value  = "<?php echo $vPedido['pedconso'] ?>";
          document.forms['frgrm']['cPedObs'].value    = "<?php echo $vPedido['pedobsxx'] ?>";
          document.forms['frgrm']['nTotPedido'].value = "<?php echo $vPedido['pedvlrxx'] ?>";

          <?php
          if (mysql_num_rows($xDetPedido) > 0) {
            $nCountDet1 = 0;
            $nCountDet2 = 0;
            $nCountDet3 = 0;
            while ($xDP = mysql_fetch_array($xDetPedido)) { 
              ?>
              // Paso 2 - Automatica
              if ('<?php echo $vPedido['pedtipxx'] ?>' == "AUTOMATICA") {
                document.getElementById("fieldset_de_Certificaciones_Automatico").style.display ="block";
                document.getElementById("fieldset_de_Certificaciones_Manual").style.display     ="none";

                <?php 
                $nCountDet1++;
                if (($xDP['ceridxxx'] != "" || $xDP['ceridxxx'] != 0) && $xDP['ceranoxx'] != "") {
                  // Consulta la Certificacion
                  $cAnioCert = $xDP['ceranoxx'];
                  $qCertificacion  = "SELECT ";
                  $qCertificacion .= "lcca$cAnioCert.comidxxx, ";
                  $qCertificacion .= "lcca$cAnioCert.comcodxx, ";
                  $qCertificacion .= "lcca$cAnioCert.comprexx, ";
                  $qCertificacion .= "lcca$cAnioCert.comcscxx, ";
                  $qCertificacion .= "lcca$cAnioCert.comcsc2x ";
                  $qCertificacion .= "FROM $cAlfa.lcca$cAnioCert ";
                  $qCertificacion .= "WHERE ";
                  $qCertificacion .= "$cAlfa.lcca$cAnioCert.ceridxxx = \"{$xDP['ceridxxx']}\" LIMIT 0,1 ";
                  $xCertificacion  = f_MySql("SELECT","",$qCertificacion,$xConexion01,"");
                  // echo $qCertificacion . " ~ " . mysql_num_rows($xCertificacion);
                  $vCertificacion = array();
                  if (mysql_num_rows($xCertificacion) > 0) {
                    $vCertificacion = mysql_fetch_array($xCertificacion);
                  }

                  // Consulta la M.I.F
                  $cAnioMif = $xDP['mifidano'];
                  $qMatriz  = "SELECT ";
                  $qMatriz .= "lmca$cAnioMif.comidxxx, ";
                  $qMatriz .= "lmca$cAnioMif.comcodxx, ";
                  $qMatriz .= "lmca$cAnioMif.comprexx, ";
                  $qMatriz .= "lmca$cAnioMif.comcscxx, ";
                  $qMatriz .= "lmca$cAnioMif.comcsc2x ";
                  $qMatriz .= "FROM $cAlfa.lmca$cAnioMif ";
                  $qMatriz .= "WHERE ";
                  $qMatriz .= "$cAlfa.lmca$cAnioMif.mifidxxx = \"{$xDP['mifidxxx']}\" LIMIT 0,1 ";
                  $xMatriz  = f_MySql("SELECT","",$qMatriz,$xConexion01,"");
                  // echo $qMatriz . " ~ " . mysql_num_rows($xMatriz);
                  //f_Mensaje(__FILE__,__LINE__,$qMatriz." ~ ".mysql_num_rows($xMatriz));

                  $vMatriz = array();
                  if (mysql_num_rows($xMatriz) > 0) {
                    $vMatriz = mysql_fetch_array($xMatriz);
                  }

                  // Consultando la Oficina de Venta
                  $qOfiVenta  = "SELECT ";
                  $qOfiVenta .= "ofvsapxx, ";
                  $qOfiVenta .= "ofvdesxx ";
                  $qOfiVenta .= "FROM $cAlfa.lpar0002 ";
                  $qOfiVenta .= "WHERE ";
                  $qOfiVenta .= "ofvsapxx = \"{$xDP['ofvsapxx']}\" LIMIT 0,1 ";
                  $xOfiVenta  = f_MySql("SELECT","",$qOfiVenta,$xConexion01,"");
                  $vOfiVenta  = array();
                  if (mysql_num_rows($xOfiVenta) > 0) {
                    $vOfiVenta = mysql_fetch_array($xOfiVenta);
                  }
                  ?>

                  // Pintando la grila Automatica
                  fnAddNewRowCertificacionAuto('Grid_Certificacion_Auto', '');
                  document.forms['frgrm']['cCerId_Certi'+'<?php echo $nCountDet1 ?>'].value     = '<?php echo $xDP['ceridxxx'] ?>';
                  document.forms['frgrm']['cCerAno_Certi'+'<?php echo $nCountDet1 ?>'].value    = '<?php echo $xDP['ceranoxx'] ?>';
                  document.forms['frgrm']['cCerComCsc_Certi'+'<?php echo $nCountDet1 ?>'].value = '<?php echo $vCertificacion['comidxxx'] ."-". $vCertificacion['comprexx'] ."-". $vCertificacion['comcscxx'] ?>';
                  document.forms['frgrm']['cMifId_Certi'+'<?php echo $nCountDet1 ?>'].value     = '<?php echo $xDP['mifidxxx'] ?>';
                  document.forms['frgrm']['cMifIdAno_Certi'+'<?php echo $nCountDet1 ?>'].value  = '<?php echo $xDP['mifidano'] ?>';
                  document.forms['frgrm']['cMifComCsc_Certi'+'<?php echo $nCountDet1 ?>'].value = '<?php echo $vMatriz['comidxxx'] ."-". $vMatriz['comprexx'] ."-". $vMatriz['comcscxx'] ?>';
                  document.forms['frgrm']['cOfvSap_Certi'+'<?php echo $nCountDet1 ?>'].value    = '<?php echo $xDP['ofvsapxx'] ?>';
                  document.forms['frgrm']['cOfvDes_Certi'+'<?php echo $nCountDet1 ?>'].value    = '<?php echo $vOfiVenta['ofvdesxx'] ?>';
                  document.forms['frgrm']['cCerFde_Certi'+'<?php echo $nCountDet1 ?>'].value    = '<?php echo $xDP['cerfdexx'] ?>';
                  document.forms['frgrm']['cCerFha_Certi'+'<?php echo $nCountDet1 ?>'].value    = '<?php echo $xDP['cerfhaxx'] ?>';
                  document.forms['frgrm']['cDepNum_Certi'+'<?php echo $nCountDet1 ?>'].value    = '<?php echo $xDP['depnumxx'] ?>';
                  document.forms['frgrm']['cCcoIdOc_Certi'+'<?php echo $nCountDet1 ?>'].value   = '<?php echo $xDP['ccoidocx'] ?>';
                  <?php 
                }
                ?>
              } else {
                // Paso 2 - Manual
                document.getElementById("fieldset_de_Certificaciones_Manual").style.display     = "block";
                document.getElementById("fieldset_de_Certificaciones_Automatico").style.display = "none";

                <?php 
                if ($xDP['orvsap2x'] != "" && $xDP['ofvsap2x'] != "") {
                  $nCountDet2++;

                  // Consultando la Organizacion de Venta
                  $qOrgVenta  = "SELECT ";
                  $qOrgVenta .= "orvsapxx, ";
                  $qOrgVenta .= "orvdesxx ";
                  $qOrgVenta .= "FROM $cAlfa.lpar0001 ";
                  $qOrgVenta .= "WHERE ";
                  $qOrgVenta .= "orvsapxx = \"{$xDP['orvsap2x']}\" LIMIT 0,1 ";
                  $xOrgVenta  = f_MySql("SELECT","",$qOrgVenta,$xConexion01,"");
                  $vOrgVenta  = array();
                  if (mysql_num_rows($xOrgVenta) > 0) {
                    $vOrgVenta = mysql_fetch_array($xOrgVenta);
                  }

                  // Consultando la Oficina de Venta
                  $qOfiVenta  = "SELECT ";
                  $qOfiVenta .= "ofvsapxx, ";
                  $qOfiVenta .= "ofvdesxx ";
                  $qOfiVenta .= "FROM $cAlfa.lpar0002 ";
                  $qOfiVenta .= "WHERE ";
                  $qOfiVenta .= "orvsapxx = \"{$xDP['orvsap2x']}\" AND ";
                  $qOfiVenta .= "ofvsapxx = \"{$xDP['ofvsap2x']}\" LIMIT 0,1 ";
                  $xOfiVenta  = f_MySql("SELECT","",$qOfiVenta,$xConexion01,"");
                  $vOfiVenta  = array();
                  if (mysql_num_rows($xOfiVenta) > 0) {
                    $vOfiVenta = mysql_fetch_array($xOfiVenta);
                  }

                  // Consultando el Centro Logistico
                  $qCentroLog  = "SELECT ";
                  $qCentroLog .= "closapxx, ";
                  $qCentroLog .= "clodesxx ";
                  $qCentroLog .= "FROM $cAlfa.lpar0003 ";
                  $qCentroLog .= "WHERE ";
                  $qCentroLog .= "orvsapxx = \"{$xDP['orvsap2x']}\" AND ";
                  $qCentroLog .= "ofvsapxx = \"{$xDP['ofvsap2x']}\" AND ";
                  $qCentroLog .= "closapxx = \"{$xDP['closapxx']}\" LIMIT 0,1 ";
                  $xCentroLog  = f_MySql("SELECT","",$qCentroLog,$xConexion01,"");
                  $vCentroLog  = array();
                  if (mysql_num_rows($xCentroLog) > 0) {
                    $vCentroLog = mysql_fetch_array($xCentroLog);
                  }

                  // Consultando el Sector
                  $qSector  = "SELECT ";
                  $qSector .= "secsapxx, ";
                  $qSector .= "secdesxx ";
                  $qSector .= "FROM $cAlfa.lpar0009 ";
                  $qSector .= "WHERE ";
                  $qSector .= "secsapxx = \"{$xDP['secsapxx']}\" LIMIT 0,1 ";
                  $xSector  = f_MySql("SELECT","",$qSector,$xConexion01,"");
                  $vSector  = array();
                  if (mysql_num_rows($xSector) > 0) {
                    $vSector = mysql_fetch_array($xSector);
                  }

                  // Consultando el Canal de Distribucion
                  $qCanalDist  = "SELECT ";
                  $qCanalDist .= "cdisapxx, ";
                  $qCanalDist .= "cdidesxx ";
                  $qCanalDist .= "FROM $cAlfa.lpar0008 ";
                  $qCanalDist .= "WHERE ";
                  $qCanalDist .= "cdisapxx = \"{$xDP['cdisapxx']}\" LIMIT 0,1 ";
                  $xCanalDist  = f_MySql("SELECT","",$qCanalDist,$xConexion01,"");
                  $vCanalDist  = array();
                  if (mysql_num_rows($xCanalDist) > 0) {
                    $vCanalDist = mysql_fetch_array($xCanalDist);
                  }

                  // Consultando el Tipo de Deposito
                  $qTipoDep  = "SELECT ";
                  $qTipoDep .= "tdeidxxx, ";
                  $qTipoDep .= "tdedesxx ";
                  $qTipoDep .= "FROM $cAlfa.lpar0007 ";
                  $qTipoDep .= "WHERE ";
                  $qTipoDep .= "tdeidxxx = \"{$xDP['tdeidxxx']}\" LIMIT 0,1 ";
                  $xTipoDep  = f_MySql("SELECT","",$qTipoDep,$xConexion01,"");
                  $vTipoDep  = array();
                  if (mysql_num_rows($xTipoDep) > 0) {
                    $vTipoDep = mysql_fetch_array($xTipoDep);
                  }
                  ?>

                  // Pintando la grila Manual
                  fnAddNewRowCertificacionManual('Grid_Certificacion_Manual', '');
                  document.forms['frgrm']['cOrvSap_Certi'+'<?php echo $nCountDet2 ?>'].value   = '<?php echo $vOrgVenta['orvsapxx'] ?>';
                  document.forms['frgrm']['cOrvDes_Certi'+'<?php echo $nCountDet2 ?>'].value   = '<?php echo $vOrgVenta['orvdesxx'] ?>';
                  document.forms['frgrm']['cOfvSap_Certi'+'<?php echo $nCountDet2 ?>'].value   = '<?php echo $vOfiVenta['ofvsapxx'] ?>';
                  document.forms['frgrm']['cOfvDes_Certi'+'<?php echo $nCountDet2 ?>'].value   = '<?php echo $vOfiVenta['ofvdesxx'] ?>';
                  document.forms['frgrm']['cCloSap_Certi'+'<?php echo $nCountDet2 ?>'].value   = '<?php echo $vCentroLog['closapxx'] ?>';
                  document.forms['frgrm']['cCloDes_Certi'+'<?php echo $nCountDet2 ?>'].value   = '<?php echo $vCentroLog['clodesxx'] ?>';
                  document.forms['frgrm']['cSecSap_Certi'+'<?php echo $nCountDet2 ?>'].value   = '<?php echo $vSector['secsapxx'] ?>';
                  document.forms['frgrm']['cSecDes_Certi'+'<?php echo $nCountDet2 ?>'].value   = '<?php echo $vSector['secdesxx'] ?>';
                  document.forms['frgrm']['cCdiSap_Certi'+'<?php echo $nCountDet2 ?>'].value   = '<?php echo $vCanalDist['cdisapxx'] ?>';
                  document.forms['frgrm']['cCdiDes_Certi'+'<?php echo $nCountDet2 ?>'].value   = '<?php echo $vCanalDist['cdidesxx'] ?>';
                  document.forms['frgrm']['cTdeId_Certi'+'<?php echo $nCountDet2 ?>'].value    = '<?php echo $vTipoDep['tdeidxxx'] ?>';
                  document.forms['frgrm']['cTdeDes_Certi'+'<?php echo $nCountDet2 ?>'].value   = '<?php echo $vTipoDep['tdedesxx'] ?>';
                  document.forms['frgrm']['cDepNum_Certi'+'<?php echo $nCountDet2 ?>'].value   = '<?php echo $xDP['depnum2x'] ?>';
                  document.forms['frgrm']['cCerFde_Certi'+'<?php echo $nCountDet2 ?>'].value   = '<?php echo $xDP['pedfdexx'] ?>';
                  document.forms['frgrm']['cCerFha_Certi'+'<?php echo $nCountDet2 ?>'].value   = '<?php echo $xDP['pedfhaxx'] ?>';
                  document.forms['frgrm']['cCcoIdOc_Certi'+'<?php echo $nCountDet2 ?>'].value  = '<?php echo $xDP['ccoidoc2'] ?>';
                  <?php 
                }
                ?>
              }
              <?php
              // Paso 3
              if ($xDP['sersapxx'] != "" && $xDP['subidxxx'] != "") { 
                $nCountDet3++;

                // Consulta el Servicio
                $qServicio  = "SELECT ";
                $qServicio .= "sersapxx, ";
                $qServicio .= "serdesxx ";
                $qServicio .= "FROM $cAlfa.lpar0011 ";
                $qServicio .= "WHERE ";
                $qServicio .= "sersapxx = \"{$xDP['sersapxx']}\" LIMIT 0,1 ";
                $xServicio  = f_MySql("SELECT","",$qServicio,$xConexion01,"");
                $vServicio  = array();
                if (mysql_num_rows($xServicio) > 0) {
                  $vServicio = mysql_fetch_array($xServicio);
                }

                // Consulta el Subservicio
                $qSubServicio  = "SELECT ";
                $qSubServicio .= "sersapxx, ";
                $qSubServicio .= "subidxxx, ";
                $qSubServicio .= "subdesxx ";
                $qSubServicio .= "FROM $cAlfa.lpar0012 ";
                $qSubServicio .= "WHERE ";
                $qSubServicio .= "sersapxx = \"{$xDP['sersapxx']}\" AND ";
                $qSubServicio .= "subidxxx = \"{$xDP['subidxxx']}\" LIMIT 0,1 ";
                $xSubServicio  = f_MySql("SELECT","",$qSubServicio,$xConexion01,"");
                $vSubServicio  = array();
                if (mysql_num_rows($xSubServicio) > 0) {
                  $vSubServicio = mysql_fetch_array($xSubServicio);
                }
              
                // Consulta el Codigo CEBE
                $qCebe  = "SELECT ";
                $qCebe .= "cebidxxx, ";
                $qCebe .= "cebcodxx, ";
                $qCebe .= "cebdesxx ";
                $qCebe .= "FROM $cAlfa.lpar0010 ";
                $qCebe .= "WHERE ";
                $qCebe .= "cebidxxx = \"{$xDP['cebidxxx']}\" LIMIT 0,1 ";
                $xCebe  = f_MySql("SELECT","",$qCebe,$xConexion01,"");
                $vCebe  = array();
                if (mysql_num_rows($xCebe) > 0) {
                  $vCebe = mysql_fetch_array($xCebe);
                }
                ?>

                fnAddNewRowDetalleCerticacion('Grid_Detalle_Certificacion', '');
                document.forms['frgrm']['cCSeId_Det'+'<?php echo $nCountDet3 ?>'].value     = '<?php echo $xDP['cseidxxx'] ?>';
                document.forms['frgrm']['cCerdEst_Det'+'<?php echo $nCountDet3 ?>'].value   = '<?php echo $xDP['cerdestx'] ?>';
                document.forms['frgrm']['cSerSap_Det'+'<?php echo $nCountDet3 ?>'].value    = '<?php echo $vServicio['sersapxx'] ?>';
                document.forms['frgrm']['cSerDes_Det'+'<?php echo $nCountDet3 ?>'].value    = '<?php echo $vServicio['serdesxx'] ?>';
                document.forms['frgrm']['cSubId_Det'+'<?php echo $nCountDet3 ?>'].value     = '<?php echo $vSubServicio['subidxxx'] ?>';
                document.forms['frgrm']['cSubDes_Det'+'<?php echo $nCountDet3 ?>'].value    = '<?php echo $vSubServicio['subdesxx'] ?>';
                document.forms['frgrm']['cObfId_Det'+'<?php echo $nCountDet3 ?>'].value     = '<?php echo $xDP['obfidxxx'] ?>';
                document.forms['frgrm']['cUfaId_Det'+'<?php echo $nCountDet3 ?>'].value     = '<?php echo $xDP['ufaidxxx'] ?>';
                document.forms['frgrm']['cCebId_Det'+'<?php echo $nCountDet3 ?>'].value     = '<?php echo $vCebe['cebidxxx'] ?>';
                document.forms['frgrm']['cCebCod_Det'+'<?php echo $nCountDet3 ?>'].value    = '<?php echo $vCebe['cebcodxx'] ?>';
                document.forms['frgrm']['cCebDes_Det'+'<?php echo $nCountDet3 ?>'].value    = '<?php echo $vCebe['cebdesxx'] ?>';
                document.forms['frgrm']['cBase_Det'+'<?php echo $nCountDet3 ?>'].value      = '<?php echo $xDP['pedbasex'] ?>';
                document.forms['frgrm']['cTarifa_Det'+'<?php echo $nCountDet3 ?>'].value    = '<?php echo $xDP['pedtarix'] ?>';
                document.forms['frgrm']['cCalculo_Det'+'<?php echo $nCountDet3 ?>'].value   = '<?php echo $xDP['pedcalcu'] ?>';
                document.forms['frgrm']['cMinima_Det'+'<?php echo $nCountDet3 ?>'].value    = '<?php echo $xDP['pedminix'] ?>';
                document.forms['frgrm']['cVlrPedido_Det'+'<?php echo $nCountDet3 ?>'].value = '<?php echo $xDP['pedvlrxx'] ?>';
               
                <?php
              }
            }
          }
          ?>
        </script>
        <?php
      }

    }
    ?>
    
    </body>
</html>

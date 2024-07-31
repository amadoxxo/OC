<?php
  /**
   * Formulario Nuevo Ticket.
   * --- Descripcion: Permite Crear una Nuevo Ticket
   * @author Elian Amado. elian.amado@openits.co
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
      function fnRetorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
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
              var zRuta  = "frcer117.php?gWhat=VALID" +
                                        "&gFunction=cComPre" +
                                        "&gComPre="+document.forms['frgrm']['cComPre'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var nNx     = (nX-600)/2;
              var nNy     = (nY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
              var zRuta   = "frcer117.php?gWhat=WINDOW" +
                                        "&gFunction=cComPre" +
                                        "&gComPre="+document.forms['frgrm']['cComPre'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          // Cliente
          case "cCliId":
            fnLimpiarCamposCabecera();

            if (xSwitch == "VALID") {
              var zRuta  = "frcer150.php?gWhat=VALID" +
                                        "&gFunction=cCliId" +
                                        "&gCliId="+document.forms['frgrm']['cCliId'].value;
              parent.fmpro.location = zRuta;
            } else {
              var nNx     = (nX-600)/2;
              var nNy     = (nY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
              var zRuta   = "frcer150.php?gWhat=WINDOW" +
                                        "&gFunction=cCliId" +
                                        "&gCliId="+document.forms['frgrm']['cCliId'].value;
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cCliNom":
            if (xSwitch == "VALID") {
              var zRuta  = "frcer150.php?gWhat=VALID"+
                                        "&gFunction=cCliNom"+
                                        "&gCliNom="+document.forms['frgrm']['cCliNom'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var nNx     = (nX-600)/2;
              var nNy     = (nY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
              var zRuta   = "frcer150.php?gWhat=WINDOW"+
                                        "&gFunction=cCliNom"+
                                        "&gCliNom="+document.forms['frgrm']['cCliNom'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          // MIF
          case "cMifComCsc":
            var nSwitch = 0;
            if(document.forms['frgrm']['cMifComCsc'].value != ""){
              if(document.forms['frgrm']['cMifComCsc'].value.length < 1){
                nSwitch = 1;
                alert('Debe Digitar al Menos Un Digito de la M.I.F,\nVerifique.');
              }

              var cMsj = "";
              if(document.forms['frgrm']['cCliId'].value == ""){
                nSwitch = 1;
                cMsj += 'Debe Seleccionar un Cliente para Consultar la M.I.F,\n';
              }

              if(document.forms['frgrm']['cComPre'].value == ""){
                nSwitch = 1;
                cMsj += 'Debe Seleccionar el Prefijo de la Certificacion,\n';
              }

              if (nSwitch == 0) {
                if (xSwitch == "VALID") {
                  var zRuta = "frlmca00.php?gModo="   +xSwitch+"&gFunction="+xLink+
                                          "&cPerAno=" +document.forms['frgrm']['cPerAno'].value +
                                          "&gComCsc=" +document.forms['frgrm']['cMifComCsc'].value +
                                          "&gCliId="  +document.forms['frgrm']['cCliId'].value +
                                          "&gComPre=" +document.forms['frgrm']['cComPre'].value +
                                          "&gOrigen=NUEVO";
                  parent.fmpro.location = zRuta;
                } else if(xSwitch == "WINDOW") {
                  var nNx      = (nX-500)/2;
                  var nNy      = (nY-250)/2;
                  var zWinPro  = "width=500,scrollbars=1,height=250,left="+nNx+",top="+nNy;
                  var zRuta = "frlmca00.php?gModo="   +xSwitch+"&gFunction="+xLink+
                                          "&cPerAno=" +document.forms['frgrm']['cPerAno'].value +
                                          "&gComCsc=" +document.forms['frgrm']['cMifComCsc'].value + 
                                          "&gCliId="  +document.forms['frgrm']['cCliId'].value + 
                                          "&gComPre=" +document.forms['frgrm']['cComPre'].value +
                                          "&gOrigen=NUEVO";
                  zWindow = window.open(zRuta,xLink,zWinPro);
                  zWindow.focus();
                } else if (xSwitch == "EXACT") {
                  var zRuta = "frlmca00.php?gModo=EXACT&gFunction=" + xLink +
                                          "&cPerAno=" +document.forms['frgrm']['cPerAno'].value +
                                          "&gComCod=" +document.frgrm['cMifComCod'].value +
                                          "&gComCsc=" +document.frgrm['cMifComCsc'].value +
                                          "&gComCsc2="+document.frgrm['cMifComCsc2'].value +
                                          "&gCliId="  +document.frgrm['cCliId'].value +
                                          "&gComPre=" +document.forms['frgrm']['cComPre'].value +
                                          "&gOrigen=NUEVO";
                  parent.fmpro.location = zRuta;
                }
              } else {
                document.forms['frgrm']['cMifComCsc'].value = "";
                alert(cMsj + 'Verifique.');
              }
            }
          break;
          // Deposito
          case "cDepNum":
            if (document.forms['frgrm']['cCliId'].value == '') {
              alert("Debe Seleccionar el Cliente para Consultar los Depositos,\nVerifique.");
              document.forms['frgrm']['cDepNum'].value = '';
            } else {
              if (xSwitch == "VALID") {
                var zRuta = "frcer155.php?gWhat=VALID" +
                                          "&gFunction=cDepNum" +
                                          "&gDepNum="+document.forms['frgrm']['cDepNum'].value +
                                          "&gCliId="+document.forms['frgrm']['cCliId'].value +
                                          "&gOrigen=NUEVO";
                parent.fmpro.location = zRuta;
              } else {
                var nNx     = (nX-600)/2;
                var nNy     = (nY-250)/2;
                var zWinPro = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
                var zRuta   = "frcer155.php?gWhat=WINDOW" +
                                            "&gFunction=cDepNum" +
                                            "&gDepNum="+document.forms['frgrm']['cDepNum'].value +
                                            "&gCliId="+document.forms['frgrm']['cCliId'].value +
                                            "&gOrigen=NUEVO";
                zWindow = window.open(zRuta,"zWindow",zWinPro);
                zWindow.focus();
              }
            }
          break;
          // Canal Distribucion
          case "cCdiSap":
            if (xSwitch == "VALID") {
              var zRuta  = "frcer008.php?gWhat=VALID" +
                                        "&gFunction=cCdiSap" +
                                        "&gCdiSap="+document.forms['frgrm']['cCdiSap'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (nX-600)/2;
              var zNy     = (nY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frcer008.php?gWhat=WINDOW" +
                                        "&gFunction=cCdiSap" +
                                        "&gCdiSap="+document.forms['frgrm']['cCdiSap'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cCdiDes":
            if (xSwitch == "VALID") {
              var zRuta  = "frcer008.php?gWhat=VALID" +
                                        "&gFunction=cCdiDes" +
                                        "&gCdiDes="+document.forms['frgrm']['cCdiDes'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (nX-600)/2;
              var zNy     = (nY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frcer008.php?gWhat=WINDOW" +
                                        "&gFunction=cCdiDes" +
                                        "&gCdiDes="+document.forms['frgrm']['cCdiDes'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          // Servicios
          case "cSerSap":
            if (nSecuencia != "") {
              document.forms['frgrm']['cSubId'+nSecuencia].value = "";
              document.forms['frgrm']['cSubDes'+nSecuencia].value = "";

              if (xSwitch == "VALID") {
                var zRuta  = "frcer011.php?gWhat=VALID" +
                                          "&gFunction=cSerSap" +
                                          "&gSecuencia="+nSecuencia +
                                          "&gSerSap="+document.forms['frgrm']['cSerDes'+nSecuencia].value;
                parent.fmpro.location = zRuta;
              } else {
                var zNx     = (nX-600)/2;
                var zNy     = (nY-250)/2;
                var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
                var zRuta   = "frcer011.php?gWhat=WINDOW" +
                                          "&gFunction=cSerSap" +
                                          "&gSecuencia="+nSecuencia +
                                          "&gSerSap="+document.forms['frgrm']['cSerDes'+nSecuencia].value;
                zWindow = window.open(zRuta,"zWindow",zWinPro);
                zWindow.focus();
              }
            }
          break;
          // Subservicios
          case "cSubId":
            if (nSecuencia != "") {
              if (document.forms['frgrm']['cSerSap'+nSecuencia].value != "") {
                if (xSwitch == "VALID") {
                  var zRuta  = "frcer012.php?gWhat=VALID" +
                                            "&gFunction=cSubId" +
                                            "&gSecuencia="+nSecuencia +
                                            "&gSerSap="+document.forms['frgrm']['cSerSap'+nSecuencia].value +
                                            "&gSubId="+document.forms['frgrm']['cSubDes'+nSecuencia].value;
                  parent.fmpro.location = zRuta;
                } else {
                  var zNx     = (nX-600)/2;
                  var zNy     = (nY-250)/2;
                  var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
                  var zRuta   = "frcer012.php?gWhat=WINDOW" +
                                            "&gFunction=cSubId" +
                                            "&gSecuencia="+nSecuencia +
                                            "&gSerSap="+document.forms['frgrm']['cSerSap'+nSecuencia].value +
                                            "&gSubId="+document.forms['frgrm']['cSubDes'+nSecuencia].value;
                  zWindow = window.open(zRuta,"zWindow",zWinPro);
                  zWindow.focus();
                }
              } else {
                alert("Debe Seleccionar un Servicio,\nVerifique.");
              }
            }
          break;
          // Unidad Facturable
          case "cUfaId":
            var nSwitch = 0;
            if (nSecuencia != "") {
              if (xGrid == "" && document.forms['frgrm']['cSerSap'+nSecuencia].value == "") {
                nSwitch = 1;
              }

              if (nSwitch == 0) {
                if (xSwitch == "VALID") {
                  var zRuta  = "frcer006.php?gWhat=VALID" +
                                            "&gFunction=cUfaId" +
                                            "&gGrid="+xGrid +
                                            "&gSecuencia="+nSecuencia +
                                            "&gUfaId="+document.forms['frgrm']['cUfaDes'+xGrid+nSecuencia].value;
                  parent.fmpro.location = zRuta;
                } else {
                  var zNx     = (nX-600)/2;
                  var zNy     = (nY-250)/2;
                  var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
                  var zRuta   = "frcer006.php?gWhat=WINDOW" +
                                            "&gFunction=cUfaId" +
                                            "&gGrid="+xGrid +
                                            "&gSecuencia="+nSecuencia +
                                            "&gUfaId="+document.forms['frgrm']['cUfaDes'+xGrid+nSecuencia].value;
                  zWindow = window.open(zRuta,"zWindow",zWinPro);
                  zWindow.focus();
                }
              } else {
                alert("Debe Seleccionar un Servicio,\nVerifique.");
              }
            }
          break;
          // Codigo CEBE
          case "cCebCod":
            if (nSecuencia != "") {
              if (xSwitch == "VALID") {
                var zRuta  = "frcer010.php?gWhat=VALID" +
                                          "&gFunction=cCebCod" +
                                          "&gSecuencia="+nSecuencia +
                                          "&gCebCod="+document.forms['frgrm']['cCebCod'+nSecuencia].value;
                parent.fmpro.location = zRuta;
              } else {
                var zNx     = (nX-600)/2;
                var zNy     = (nY-250)/2;
                var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
                var zRuta   = "frcer010.php?gWhat=WINDOW" +
                                          "&gFunction=cCebCod" +
                                          "&gSecuencia="+nSecuencia +
                                          "&gCebCod="+document.forms['frgrm']['cCebCod'+nSecuencia].value;
                zWindow = window.open(zRuta,"zWindow",zWinPro);
                zWindow.focus();
              }
            }
          break;
          // Codigo CEBE Descripcion
          case "cCebDes":
            if (nSecuencia != "") {
              if (xSwitch == "VALID") {
                var zRuta  = "frcer010.php?gWhat=VALID" +
                                          "&gFunction=cCebDes" +
                                          "&gSecuencia="+nSecuencia +
                                          "&gCebDes="+document.forms['frgrm']['cCebDes'+nSecuencia].value;
                parent.fmpro.location = zRuta;
              } else {
                var zNx     = (nX-600)/2;
                var zNy     = (nY-250)/2;
                var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
                var zRuta   = "frcer010.php?gWhat=WINDOW" +
                                          "&gFunction=cCebDes" +
                                          "&gSecuencia="+nSecuencia +
                                          "&gCebDes="+document.forms['frgrm']['cCebDes'+nSecuencia].value;
                zWindow = window.open(zRuta,"zWindow",zWinPro);
                zWindow.focus();
              }
            }
          break;
          // Objeto Facturable
          case "cObfId":
            if (nSecuencia != "") {
              if (xSwitch == "VALID") {
                var zRuta  = "frcer004.php?gWhat=VALID" +
                                          "&gFunction=cObfId" +
                                          "&gGrid="+xGrid +
                                          "&gSecuencia="+nSecuencia +
                                          "&gObfId="+document.forms['frgrm']['cObfDes'+xGrid+nSecuencia].value;
                parent.fmpro.location = zRuta;
              } else {
                var zNx     = (nX-600)/2;
                var zNy     = (nY-250)/2;
                var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
                var zRuta   = "frcer004.php?gWhat=WINDOW" +
                                          "&gFunction=cObfId" +
                                          "&gGrid="+xGrid +
                                          "&gSecuencia="+nSecuencia +
                                          "&gObfId="+document.forms['frgrm']['cObfDes'+xGrid+nSecuencia].value;
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
       * Valida el tipo de certificacion seleccionado para habilitar y deshabilitar campos dependiendo si es MANUAL/AUTOMATICO.
       */
      function fnCambiaTipoCertificacion(value) {
        if (value == "AUTOMATICA") {
          document.forms['frgrm']['cPerAno'].disabled    = false;
          document.forms['frgrm']['cMifComCsc'].disabled = false;
          document.forms['frgrm']['dVigDesde'].readOnly  = true;
          document.forms['frgrm']['dVigHasta'].readOnly  = true;
          document.forms['frgrm']['cDepNum'].disabled    = true;
          document.forms['frgrm']['cTipoDep'].readOnly   = true;
          document.getElementById('id_href_dVigDesde').removeAttribute('href');
          document.getElementById('id_href_dVigHasta').removeAttribute('href');
          document.getElementById('id_href_DepNum').removeAttribute('href');

          document.getElementById('serviciosAutomaticos').style.display = "block";
          document.getElementById('serviciosManual').style.display = "none";
          document.getElementById('Grid_Servicios').innerHTML = "";
          document.getElementById('Grid_Certificacion').innerHTML = "";
          document.forms['frgrm']['nSecuencia_Servicio'].value = 0;

        } else {
          document.forms['frgrm']['cPerAno'].disabled    = true;
          document.forms['frgrm']['cMifComCsc'].disabled = true;
          document.forms['frgrm']['dVigDesde'].readOnly  = false;
          document.forms['frgrm']['dVigHasta'].readOnly  = false;
          document.forms['frgrm']['cDepNum'].disabled    = false;
          document.forms['frgrm']['cTipoDep'].readOnly   = false;
          document.getElementById('id_href_dVigDesde').setAttribute('href', "javascript:show_calendar(\"frgrm.dVigDesde\")");
          document.getElementById('id_href_dVigHasta').setAttribute('href', "javascript:show_calendar(\"frgrm.dVigHasta\")");
          document.getElementById('id_href_DepNum').setAttribute('href', "javascript:document.forms[\"frgrm\"][\"cDepNum\"].value = \"\"; document.forms[\"frgrm\"][\"cTipoDep\"].value = \"\";fnLinks(\"cDepNum\",\"VALID\")");
        
          document.getElementById('serviciosAutomaticos').style.display = "none";
          document.getElementById('overDivSubServicios').innerHTML = "";
          document.getElementById('serviciosManual').style.display = "block";
          fnAddNewRowServicio('Grid_Servicios');
          document.getElementById('Grid_Certificacion').innerHTML = "";
          document.forms['frgrm']['nSecuencia_Certificacion'].value = 0;
        }

        fnLimpiarCamposCabecera();
      }

      /**
       * Limpia los campos de cabecera.
       */
      function fnLimpiarCamposCabecera() {
        document.forms['frgrm']['cMifId'].value      = "";
        document.forms['frgrm']['cMifComId'].value   = "";
        document.forms['frgrm']['cMifComCod'].value  = "";
        document.forms['frgrm']['cMifComCsc'].value  = "";
        document.forms['frgrm']['cMifComCsc2'].value = "";
        document.forms['frgrm']['dVigDesde'].value   = "";
        document.forms['frgrm']['dVigHasta'].value   = "";
        document.forms['frgrm']['cDepNum'].value     = "";
        document.forms['frgrm']['cTipoDep'].value    = "";
        document.forms['frgrm']['cCcoIdOc'].value    = "";
        document.forms['frgrm']['cOrvSap'].value     = "";
        document.forms['frgrm']['cOrvDes'].value     = "";
        document.forms['frgrm']['cOfvSap'].value     = "";
        document.forms['frgrm']['cOfvDes'].value     = "";
        document.forms['frgrm']['cCloSap'].value     = "";
        document.forms['frgrm']['cCloDes'].value     = "";
        document.forms['frgrm']['cSecSap'].value     = "";
        document.forms['frgrm']['cSecDes'].value     = "";
      }

      /**
       * Permite adicionar una nueva grilla en la seccion de servicios.
       */
      function fnAddNewRowServicio(xTabla) {
        var cGrid      = document.getElementById(xTabla);
        var nLastRow   = cGrid.rows.length;
        var nSecuencia = nLastRow+1;
        var cTableRow  = cGrid.insertRow(nLastRow);
        var cSerCodigo = 'cSerSap' + nSecuencia; // Codigo del Servicio
        var cSerDescri = 'cSerDes' + nSecuencia; // Descripcion del Servicio
        var cSubCodigo = 'cSubId'  + nSecuencia; // Codigo del Subservicio
        var cSubDescri = 'cSubDes' + nSecuencia; // Descripcion del Subservicio
        var cUnidadId  = 'cUfaId'  + nSecuencia; // Codigo del Subservicio
        var cUnidadDes = 'cUfaDes' + nSecuencia; // Descripcion del Subservicio
        var oBtnDel    = 'oBtnDel' + nSecuencia; // Boton de Borrar Row
        
        TD_xAll = cTableRow.insertCell(0);
        TD_xAll.style.width  = "20px";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:20;text-align:left' name = 'cSec"+nSecuencia+"' id = 'cSec"+nSecuencia+"' value = '"+nSecuencia+"' readonly >";

        TD_xAll = cTableRow.insertCell(1);
        TD_xAll.style.width  = "20px";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:020;text-align:left' name = '"+cSerDescri+"' id = '"+cSerDescri+"' onKeydown = 'javascript:if(event.keyCode == 13){ fnLinks(\"cSerSap\",\"VALID\",\""+nSecuencia+"\") }' >";
        
        TD_xAll = cTableRow.insertCell(2);
        TD_xAll.innerHTML    = "<input type = 'hidden' name = '"+cSerCodigo+"'>";

        TD_xAll = cTableRow.insertCell(3);
        TD_xAll.style.width  = "20px";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:020;text-align:left' name = '"+cSubDescri+"' id = '"+cSubDescri+"' onKeydown = 'javascript:if(event.keyCode == 13){ fnLinks(\"cSubId\",\"VALID\",\""+nSecuencia+"\") }'>";

        TD_xAll = cTableRow.insertCell(4);
        TD_xAll.innerHTML    = "<input type = 'hidden' name = '"+cSubCodigo+"'>";

        TD_xAll = cTableRow.insertCell(5);
        TD_xAll.style.width  = "20px";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:020;text-align:left' name = '"+cUnidadDes+"' id = '"+cUnidadDes+"' onKeydown = 'javascript:if(event.keyCode == 13){ fnLinks(\"cUfaId\",\"VALID\",\""+nSecuencia+"\") }'>";

        TD_xAll = cTableRow.insertCell(6);
        TD_xAll.innerHTML    = "<input type = 'hidden' name = '"+cUnidadId+"'>";

        TD_xAll = cTableRow.insertCell(7);
        TD_xAll.style.width  = "20px";
        TD_xAll.innerHTML    = "<input type = 'button' style = 'width:020;text-align:center' name = "+oBtnDel+" id = "+oBtnDel+" value = 'X' "+
                                "onClick = 'javascript:fnDeleteRowServicio(this.value,\""+nSecuencia+"\",\""+xTabla+"\");'>";
                                
        document.forms['frgrm']['nSecuencia_Servicio'].value = nSecuencia;
      }

      /**
       * Permite eliminar una grilla de la seccion de servicios.
       */
      function fnDeleteRowServicio(xNumRow,xSecuencia,xTabla) {
        var cGrid = document.getElementById(xTabla);
        var nLastRow = cGrid.rows.length;
        if (xNumRow == "X") {
          if (confirm("Realmente Desea Eliminar La Secuencia ["+ xSecuencia +"]?")){ 
            if(xSecuencia < nLastRow){
              var j=0;
              for(var i=xSecuencia;i<nLastRow;i++){
                j = parseFloat(i)+1;

                document.forms['frgrm']['cSec'    + i].value = i;
                document.forms['frgrm']['cSerSap' + i].value = document.forms['frgrm']['cSerSap' + j].value;
                document.forms['frgrm']['cSerDes' + i].value = document.forms['frgrm']['cSerDes' + j].value;
                document.forms['frgrm']['cSubId'  + i].value = document.forms['frgrm']['cSubId' + j].value;
                document.forms['frgrm']['cSubDes' + i].value = document.forms['frgrm']['cSubDes' + j].value;
                document.forms['frgrm']['cUfaId'  + i].value = document.forms['frgrm']['cUfaId' + j].value;
                document.forms['frgrm']['cUfaDes' + i].value = document.forms['frgrm']['cUfaDes' + j].value;
              }
            }
            cGrid.deleteRow(nLastRow - 1);
            document.forms['frgrm']['nSecuencia_Servicio'].value = nLastRow - 1;

            // Se valida cual es la grid de certificacion asociada al servicio que se debe eliminar
            var nSecuencia = document.forms['frgrm']['nSecuencia_Certificacion'].value;
            for(var i=1; i<=nSecuencia;i++){
              if (document.forms['frgrm']['cIndexServ' + i].value == xSecuencia) {
                fnDeleteRowCertificacion('X', i, 'Grid_Certificacion');
                break;
              }
            }
          }
        }
      }

      /**
       * Habilita la seccion de servicios automaticos cuando se carga la MIF.
       */
      function fnHabilitaServicios(xValidaExisteSubservicio = '') {
        fnCargarGrillaSubServicio(xValidaExisteSubservicio);
        document.getElementById('serviciosAutomaticos').style.display = "block";
        document.getElementById('Grid_Certificacion').innerHTML = "";
      }

      /**
       * Permite adicionar una nueva grilla en la seccion de certificacion.
       */
      function fnAddNewRowCertificacion(xTabla, xIndiceServ = '', xTipoCertificacion = '') {
        var cGrid      = document.getElementById(xTabla);
        var nLastRow   = cGrid.rows.length;
        var nSecuencia = nLastRow+1;
        var cTableRow  = cGrid.insertRow(nLastRow);
        var cCerdId      = 'cCerdId'       + nSecuencia; // Id del Detalle
        var cDesMaterial = 'cDesMaterial'  + nSecuencia; // Descripcion del Material
        var cCodSap      = 'cCodSapSer'    + nSecuencia; // Codigo SAP Servicio
        var cSubId       = 'cSubId_Certi'  + nSecuencia; // Id Subservicio
        var cObfId       = 'cObfId_Certi'  + nSecuencia; // Objeto Facturable ID
        var cObfDes      = 'cObfDes_Certi' + nSecuencia; // Objeto Facturable Descripcion
        var cUfaId       = 'cUfaId_Certi'  + nSecuencia; // Unidad Facturable ID
        var cUfaDes      = 'cUfaDes_Certi' + nSecuencia; // Unidad Facturable Descripcion
        var cCebId       = 'cCebId'        + nSecuencia; // Cebe ID
        var cCebCod      = 'cCebCod'       + nSecuencia; // Cebe Codigo
        var cCebDes      = 'cCebDes'       + nSecuencia; // Cebe Descripcion
        var cBase        = 'cBase'         + nSecuencia; // Base
        var cCondicion   = 'cCondicion'    + nSecuencia; // Condicion
        var cStatus      = 'cStatus'       + nSecuencia; // Status
        var oBtnDel      = 'oBtnDel_Certi' + nSecuencia; // Boton de Borrar Row

        var cEstado = (xTipoCertificacion == '') ? 'AUTOMATICO' : 'TRANSACCIONAL';

        TD_xAll = cTableRow.insertCell(0);
        TD_xAll.innerHTML    = "<input type = 'hidden' name = '"+cCerdId+"'>";

        TD_xAll = cTableRow.insertCell(1);
        TD_xAll.style.width  = "20px";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:20;text-align:left' name = 'cSec_Certi"+nSecuencia+"' id = 'cSec_Certi"+nSecuencia+"' value = '"+nSecuencia+"' readonly >";

        TD_xAll = cTableRow.insertCell(2);
        TD_xAll.innerHTML    = "<input type = 'hidden' name = 'cIndexServ"+nSecuencia+"' value = '"+xIndiceServ+"'>";

        TD_xAll = cTableRow.insertCell(3);
        TD_xAll.style.width  = "140px";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:140;text-align:left' name = '"+cDesMaterial+"' id = '"+cDesMaterial+"' >";

        TD_xAll = cTableRow.insertCell(4);
        TD_xAll.innerHTML    = "<input type = 'hidden' name = 'cTipoCerti"+nSecuencia+"' value = '"+xTipoCertificacion+"'>";

        TD_xAll = cTableRow.insertCell(5);
        TD_xAll.innerHTML    = "<input type = 'hidden' name = '"+cSubId+"'>";

        TD_xAll = cTableRow.insertCell(6);
        TD_xAll.style.width  = "60px";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:60;text-align:left' name = '"+cCodSap+"' id = '"+cCodSap+"' >";

        TD_xAll = cTableRow.insertCell(7);
        TD_xAll.innerHTML    = "<input type = 'hidden' name = '"+cObfId+"'>";
    
        TD_xAll = cTableRow.insertCell(8);
        TD_xAll.style.width  = "120px";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:120;text-align:left' name = '"+cObfDes+"' id = '"+cObfDes+"' onKeydown = 'javascript:if(event.keyCode == 13){ fnLinks(\"cObfId\",\"VALID\",\""+nSecuencia+"\",\"_Certi\") }' >";

        TD_xAll = cTableRow.insertCell(9);
        TD_xAll.innerHTML    = "<input type = 'hidden' name = '"+cUfaId+"'>";

        TD_xAll = cTableRow.insertCell(10);
        TD_xAll.style.width  = "120px";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:120;text-align:left' name = '"+cUfaDes+"' id = '"+cUfaDes+"' onKeydown = 'javascript:if(event.keyCode == 13){ fnLinks(\"cUfaId\",\"VALID\",\""+nSecuencia+"\",\"_Certi\") }' >";
      
        TD_xAll = cTableRow.insertCell(11);
        TD_xAll.innerHTML    = "<input type = 'hidden' name = '"+cCebId+"'>";

        TD_xAll = cTableRow.insertCell(12);
        TD_xAll.style.width  = "60px";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:60;text-align:left' name = '"+cCebCod+"' id = '"+cCebCod+"' onKeydown = 'javascript:if(event.keyCode == 13){ fnLinks(\"cCebCod\",\"VALID\",\""+nSecuencia+"\") }' >";

        TD_xAll = cTableRow.insertCell(13);
        TD_xAll.style.width  = "140px";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:140;text-align:left' name = '"+cCebDes+"' id = '"+cCebDes+"' onKeydown = 'javascript:if(event.keyCode == 13){ fnLinks(\"cCebDes\",\"VALID\",\""+nSecuencia+"\") }' >";

        TD_xAll = cTableRow.insertCell(14);
        TD_xAll.style.width  = "80px";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:80;text-align:right' name = '"+cBase+"' id = '"+cBase+"' >";

        TD_xAll = cTableRow.insertCell(15);
        TD_xAll.style.width  = "100px";

        var selectCondicion = "<select Class = 'letrase' name = '"+cCondicion+"' style = 'width:100'>";
              selectCondicion += "<option value = 'HABILITADO'>HABILITADO</option>";
              selectCondicion += "<option value = 'DESHABILITADO'>DESHABILITADO</option>";
            selectCondicion += "</select>";

        TD_xAll.innerHTML    = selectCondicion;

        TD_xAll = cTableRow.insertCell(16);
        TD_xAll.style.width  = "80px";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:80;text-align:left' name = '"+cStatus+"' id = '"+cStatus+"' value = '"+cEstado+"' onKeyUp = 'javascript:if(event.keyCode == 13){ fnAddNewRowCertificacion(\"Grid_Certificacion\",\"\",\"MANUAL\") }' >";
      
        TD_xAll = cTableRow.insertCell(17);
        TD_xAll.style.width  = "20px";
        TD_xAll.innerHTML    = "<input type = 'button' style = 'width:020;text-align:center' name = "+oBtnDel+" id = "+oBtnDel+" value = 'X' "+
                                "onClick = 'javascript:fnDeleteRowCertificacion(this.value,\""+nSecuencia+"\",\""+xTabla+"\");'>";

        document.forms['frgrm']['nSecuencia_Certificacion'].value = nSecuencia;

        if(xTipoCertificacion == "MANUAL") {
          document.forms['frgrm']['cCodSapSer' + nSecuencia].readOnly = true;
        }
      }

      /**
       * Elimina todos los registros de certificacion de la grilla.
       */
      function fnBorrarCertificacion(xTabla){
        document.getElementById(xTabla).innerHTML = "";
      }

      /**
       * Permite eliminar una grilla de la seccion de certificacion.
       */
      function fnDeleteRowCertificacion(xNumRow,xSecuencia,xTabla) {
        var cGrid = document.getElementById(xTabla);
        var nLastRow = cGrid.rows.length;
        if (xNumRow == "X") {
          if(xSecuencia < nLastRow){
            var j=0;
            for(var i=xSecuencia;i<nLastRow;i++){
              j = parseFloat(i)+1;

              document.forms['frgrm']['cSec_Certi'    + i].value = i;
              document.forms['frgrm']['cCerdId'       + i].value = document.forms['frgrm']['cCerdId'  + j].value;
              document.forms['frgrm']['cIndexServ'    + i].value = document.forms['frgrm']['cIndexServ'    + j].value;
              document.forms['frgrm']['cDesMaterial'  + i].value = document.forms['frgrm']['cDesMaterial'  + j].value;
              document.forms['frgrm']['cCodSapSer'    + i].value = document.forms['frgrm']['cCodSapSer'    + j].value;
              document.forms['frgrm']['cSubId_Certi'  + i].value = document.forms['frgrm']['cSubId_Certi'  + j].value;
              document.forms['frgrm']['cObfId_Certi'  + i].value = document.forms['frgrm']['cObfId_Certi'  + j].value;
              document.forms['frgrm']['cObfDes_Certi' + i].value = document.forms['frgrm']['cObfDes_Certi' + j].value;
              document.forms['frgrm']['cUfaId_Certi'  + i].value = document.forms['frgrm']['cUfaId_Certi'  + j].value;
              document.forms['frgrm']['cUfaDes_Certi' + i].value = document.forms['frgrm']['cUfaDes_Certi' + j].value;
              document.forms['frgrm']['cCebId'        + i].value = document.forms['frgrm']['cCebId'        + j].value;
              document.forms['frgrm']['cCebCod'       + i].value = document.forms['frgrm']['cCebCod'       + j].value;
              document.forms['frgrm']['cCebDes'       + i].value = document.forms['frgrm']['cCebDes'       + j].value;
              document.forms['frgrm']['cBase'         + i].value = document.forms['frgrm']['cBase'         + j].value;
              document.forms['frgrm']['cCondicion'    + i].value = document.forms['frgrm']['cCondicion'    + j].value;
              document.forms['frgrm']['cStatus'       + i].value = document.forms['frgrm']['cStatus'       + j].value;
              document.forms['frgrm']['cTipoCerti'    + i].value = document.forms['frgrm']['cTipoCerti'    + j].value;
            }
          }

          cGrid.deleteRow(nLastRow - 1);
          document.forms['frgrm']['nSecuencia_Certificacion'].value = nLastRow - 1;

          // Habilita o deshabilita los campos de la grilla dependiendo del tipo de certificacion
          for (var j = 1; j <= (nLastRow - 1); j++) {
            if (document.forms['frgrm']['cStatus' + j].value == "AUTOMATICO" || document.forms['frgrm']['cTipoCerti' + j].value == "TRANSACCIONAL") {
              fnActivarDesactivarCamposCertificacion(j, true);
            } else {
              fnActivarDesactivarCamposCertificacion(j, false);
            }
          }
        }
      }

      /**
       * Carga la grilla de servicios automaticos cuando se carga la MIF.
       */
      function fnCargarGrillaSubServicio(xValidaExisteSubservicio) {
        var cRuta = "frcergri.php?gCcoIdOc="+document.forms['frgrm']['cCcoIdOc'].value+
                                "&gCliId="+document.forms['frgrm']['cCliId'].value+
                                "&gDepNum="+document.forms['frgrm']['cDepNum'].value+
                                "&gMifId="+document.forms['frgrm']['cMifId'].value+
                                "&gAnio="+document.forms['frgrm']['cPerAno'].value+
                                "&gCerId="+document.forms['frgrm']['cCerId'].value+
                                "&gTipo=1"+
                                "&gValidaExisteSubservicio="+xValidaExisteSubservicio;

        parent.fmpro.location = cRuta;
      }

      /**
       * Carga una nueva grilla en la seccion de certificacion cuando se selecciona un servicio.
       * 
       * Cuando el parametro xTipoCertificacion es igual a vacio el servicio viene de la MIF
       */
      function fnAgregarServicio(oCheck, xIndice, xTipoCertificacion = '') {

        if (oCheck == true) {
          var nSecuenciaActual = document.forms['frgrm']['nSecuencia_Certificacion'].value;

          if (xTipoCertificacion == "TRANSACCIONAL") {
            // Valida si se esta cambiando el servicio de la grid para no crearlo nuevamente
            var nExisteGrid = 0;
            for(var i=1; i<=nSecuenciaActual;i++){
              if (document.forms['frgrm']['cIndexServ' + i].value == xIndice) {
                nExisteGrid = 1
                break;
              }
            }

            if (nExisteGrid == 0) {
              fnAddNewRowCertificacion('Grid_Certificacion', xIndice, 'TRANSACCIONAL');
              var nSecuencia = document.forms['frgrm']['nSecuencia_Certificacion'].value;
            } else {
              var nSecuencia = document.forms['frgrm']['nSecuencia_Certificacion'].value;
              // Se valida cual es la grid de certificacion asociada al servicio para obtener el indice y asignar los valores
              for(var i=1; i<=nSecuencia;i++){
                if (document.forms['frgrm']['cIndexServ' + i].value == xIndice) {
                  nSecuencia = i;
                  break;
                }
              }
            }
          } else {
            fnAddNewRowCertificacion('Grid_Certificacion', xIndice, 'MIF');
            var nSecuencia = document.forms['frgrm']['nSecuencia_Certificacion'].value;
          }
        
          // Asigna los valores de la grilla de servicios a la grilla de certificacion
          document.forms['frgrm']['cDesMaterial'  + nSecuencia].value = document.forms['frgrm']['cSubDes'   + xIndice].value;
          document.forms['frgrm']['cCodSapSer'    + nSecuencia].value = document.forms['frgrm']['cSerSap'   + xIndice].value;
          document.forms['frgrm']['cSubId_Certi'  + nSecuencia].value = document.forms['frgrm']['cSubId'    + xIndice].value;
          document.forms['frgrm']['cUfaId_Certi'  + nSecuencia].value = document.forms['frgrm']['cUfaId'    + xIndice].value;
          document.forms['frgrm']['cUfaDes_Certi' + nSecuencia].value = document.forms['frgrm']['cUfaDes'   + xIndice].value;
          document.forms['frgrm']['cTipoCerti'    + nSecuencia].value = (xTipoCertificacion == "") ? "MIF" : xTipoCertificacion;
          document.forms['frgrm']['cStatus'       + nSecuencia].value = (xTipoCertificacion == "TRANSACCIONAL") ? "TRANSACCIONAL" : "AUTOMATICO";

          if (xTipoCertificacion == "") {
            document.forms['frgrm']['cObfId_Certi'  + nSecuencia].value = document.forms['frgrm']['cObfId'    + xIndice].value;
            document.forms['frgrm']['cObfDes_Certi' + nSecuencia].value = document.forms['frgrm']['cObfDes'   + xIndice].value;
            document.forms['frgrm']['cBase'         + nSecuencia].value = document.forms['frgrm']['cBaseServ' + xIndice].value;
          }
          
          fnActivarDesactivarCamposCertificacion(nSecuencia, true);
          if (xTipoCertificacion != "") {
            document.forms['frgrm']['cObfDes_Certi' + nSecuencia].readOnly = false;
            document.forms['frgrm']['cBase' + nSecuencia].readOnly = false;
          }

        } else {
          var nSecuencia = document.forms['frgrm']['nSecuencia_Certificacion'].value;

          // Se valida cual es la grid de certificacion asociada al servicio que se debe eliminar
          for(var i=1; i<=nSecuencia;i++){
            if (document.forms['frgrm']['cIndexServ' + i].value == xIndice) {
              fnDeleteRowCertificacion('X', i, 'Grid_Certificacion');
              break;
            }
          }
        }
      }

      /**
       * Permite habilitar o deshabilitar los campos de la seccion de certificacion.
       */
      function fnActivarDesactivarCamposCertificacion(xIndice, xAccion) {
        if (xAccion == true) {
          document.forms['frgrm']['cDesMaterial'  + xIndice].readOnly = true;
          document.forms['frgrm']['cCodSapSer'    + xIndice].readOnly = true;
          document.forms['frgrm']['cObfDes_Certi' + xIndice].readOnly = true;
          document.forms['frgrm']['cUfaDes_Certi' + xIndice].readOnly = true;
          document.forms['frgrm']['cBase'         + xIndice].readOnly = true;
          document.forms['frgrm']['cStatus'       + xIndice].readOnly = true;
          document.forms['frgrm']['oBtnDel_Certi' + xIndice].disabled = true;
        } else {
          document.forms['frgrm']['cDesMaterial'  + xIndice].readOnly = false;
          document.forms['frgrm']['cCodSapSer'    + xIndice].readOnly = false;
          document.forms['frgrm']['cObfDes_Certi' + xIndice].readOnly = false;
          document.forms['frgrm']['cUfaDes_Certi' + xIndice].readOnly = false;
          document.forms['frgrm']['cBase'         + xIndice].readOnly = false;
          document.forms['frgrm']['cStatus'       + xIndice].readOnly = false;
          document.forms['frgrm']['oBtnDel_Certi' + xIndice].disabled = false;
        }
      }


    </script>
  </head>
  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
    <center>
      <table border="0" cellpadding="0" cellspacing="0" width="700">
        <tr>
          <td>
            <fieldset>
              <legend><?php echo $_COOKIE['kMenDes'] ?></legend>
              <form name = "frestado" action = "frcergra.php" method = "post" target="fmpro">
                <input type = "hidden" name = "cCerId"       value = "">
                <input type = "hidden" name = "cAnio"        value = "">
              </form>
              <form name = "frgrm" action = "frcergra.php" method = "post" target="fmpro">
                <input type = "hidden" name = "cCerId"       value = "<?php echo $cCerId ?>">
                <input type = "hidden" name = "cAnio"        value = "<?php echo $cAnio ?>">
                <input type = "hidden" name = "cRegEst"      value = "">
                <input type = "hidden" name = "nSecuencia_Servicio" value = "">
                <input type = "hidden" name = "nSecuencia_Certificacion" value = "">

                <center>
                  <table border="0" cellpadding="0" cellspacing="0" width="700">
                    <?php $nCol = f_Format_Cols(35); echo $nCol; ?>
                    <!-- Seccion 1 -->
                    <!-- Fila 1 -->
                    <tr>
                      <td Class="clase08" colspan="1">Id<br>
                        <input type = "text" Class = "letra" style = "width:20" name = "cComId" value="<?php echo $cComId ?>" readonly>
                      </td>
                      <td Class="clase08" colspan="3">Prefijo<br>
                        <input type = 'text' Class = 'letra' style = 'width:60' name = 'cComPre' value="<?php echo $cComPre ?>" readonly>
                        <input type="hidden" name="cComCod">
                        <input type="hidden" name="cComTCo">
                        <input type="hidden" name="cComCco">
                      </td>
                      <td class="clase08" colspan="7">Consecutivo<br>
                        <input type = "text" Class = "letra" style = "width:140" name = "cComCsc" value="<?php echo $cComCsc ?>" readonly>
                        <input type = "hidden" name = "cComCsc2" readonly>
                      </td>
                      <td class="clase08" colspan="1"><br>
                        <input type = "text" Class = "letra" style = "width:20;" readonly>
                      </td>
                      <td Class="clase08" colspan="5">Nit<br>
                        <input type = "text" Class = "letra" style = "width:100" name = "cCliId" value="<?php echo $cCliId ?>" readonly>
                      </td>
                      <td class="clase08" colspan="1">Dv<br>
                        <input type = "text" Class = "letra" style = "width:20;text-align:center" name = "cCliDV" value="<?php echo gendv($cCliId) ?>" readonly>
                      </td>
                      <td class="clase08" colspan="12">Cliente<br>
                        <input type = "text" Class = "letra" style = "width:240" name = "cCliNom" value="<?php echo $cCliNom ?>" readonly>
                      </td>
                      <td class="clase08" colspan="5">Fecha<br>
                        <input type="text" Class = "letra" name="dComFec" style = "width:100;text-align:center" value = "<?php echo date('Y-m-d') ?>" readonly>
                      </td>
                    </tr>

                    <!-- Fila 2 -->
                    <tr>
                      <td class = "clase08" colspan="5">Creado por<br>
                        <input type="text" Class = "letra" name="" style = "width:100" value = "" readonly>
                      </td>
                      <td Class="clase08" colspan="15"><br>
                        <input type = "text" Class = "letra" style = "width:300" name = "" value = "" readonly>
                      </td>
                      <td Class="clase08" colspan="15"><br>
                        <input type = "text" Class = "letra" style = "width:300" name = "" value = "" readonly>
                      </td>
                    </tr>

                    <!-- Fila 3 -->
                    <tr>
                      <td class="clase08" colspan="35">Asunto<br>
                        <input type = "text" Class = "letra" style = "width:700" name = "" value = "">
                      </td>
                    </tr>

                    <!-- Fila 4 -->
                    <tr>
                      <td class="clase08" colspan="5">Tipo<br>
                        <input type = "text" Class = "letra" style = "width:100" name = "cSecSap" readonly>
                      </td>
                      <td class="clase08" colspan="1"><br>
                        <input type = "text" Class = "letra" style = "width:20;" readonly>
                      </td>
                      <td class="clase08" colspan="15"><br>
                        <input type = 'text' Class = 'letra' style = 'width:300' name = 'cCdiSap' maxlength="2"
                          onBlur = "javascript:fnLinks('cCdiSap','VALID');
                                              this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                          onFocus = "javascript:document.forms['frgrm']['cCdiSap'].value  = '';
                                                document.forms['frgrm']['cCdiDes'].value = '';
                                                this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                      <td class = "clase08" colspan="7">Prioridad<br>
                        <select name = "cPerAno" style = "width:140">
                          <?php for($i=$vSysStr['logistica_ano_instalacion_modulo'];$i<=date('Y');$i++){ ?>
                            <option value="<?php echo $i ?>"><?php echo $i ?></option>
                          <?php  } ?>
                        </select>
                      </td>
                      <td class = "clase08" colspan="7">Estado<br>
                        <select name = "cPerAno" style = "width:140">
                          <?php for($i=$vSysStr['logistica_ano_instalacion_modulo'];$i<=date('Y');$i++){ ?>
                            <option value="<?php echo $i ?>"><?php echo $i ?></option>
                          <?php  } ?>
                        </select>
                      </td>
                    </tr>

                    <!-- Servicios - MANUAL -->
                    <!-- <tr>
                      <td Class = "clase08" colspan="34">
                        <fieldset id = "serviciosManual">
                          <legend>Responsables Asignados al Tipo de Ticket</legend>
                          <table border = '0' cellpadding = '0' cellspacing = '0' width='680'>
                            <?php $nCol = f_Format_Cols(34); echo $nCol;?>
                            <tr>
                              <td colspan="34" class= "clase08" align="right">
                                <?php if ($_COOKIE['kModo'] != "VER") { ?>
                                  <img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_create-dir_bg.gif" onClick = "javascript:fnAddNewRowServicio('Grid_Servicios')" style = "cursor:pointer" title="Adicionar">
                                <?php } ?>
                              </td>
                            </tr>
                            <tr>
                              <td class = "clase08" colspan="15" align="center">Identificaci&oacute;n</td>
                              <td class = "clase08" colspan="17" align="center">nombre</td>
                              <td class = "clase08" colspan="01" align="right">&nbsp;</td>
                            </tr>
                          </table>
                          <table border = "0" cellpadding = "0" cellspacing = "0" width = "680" id = "Grid_Servicios"></table>
                        </fieldset>
                      </td>
                    </tr> -->

                    <!-- Responsables Asignados al Tipo de Ticket -->
                    <tr>
                      <td Class = "clase08" colspan="35">
                        <fieldset id = "serviciosAutomaticos">
                          <legend>Responsables Asignados al Tipo de Ticket</legend>

                          <input type = 'hidden' name = 'cSubservicios'>
                          <input type = 'hidden' name = 'cSubserNoMarcados'>
                          <input type = 'hidden' name = 'nIndexSubser'>
                          <div id = 'overDivSubServicios'></div>
                        </fieldset>
                      </td>
                    </tr>

                    <!-- Certificacion -->
                    <!-- <tr>
                      <td Class = "clase08" colspan="49">
                        <fieldset id = "certificacionManual">
                          <legend>Certificaci&oacute;n</legend>
                          <table border = '0' cellpadding = '0' cellspacing = '0' width='940'>
                            <?php $nCol = f_Format_Cols(35); echo $nCol;?>
                            <tr>
                              <td colspan="47" class= "clase08" align="right">
                                <?php if ($_COOKIE['kModo'] != "VER") { ?>
                                  <img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_create-dir_bg.gif" onClick = "javascript:fnAddNewRowCertificacion('Grid_Certificacion','','MANUAL')" style = "cursor:pointer" title="Adicionar">
                                <?php } ?>
                              </td>
                            </tr>
                            <tr>
                              <td class = "clase08" colspan="01" align="center">Item</td>
                              <td class = "clase08" colspan="07" align="center">Descripci&oacute;n Material</td>
                              <td class = "clase08" colspan="03" align="center">Cod Sap</td>
                              <td class = "clase08" colspan="06" align="center">Objeto Facturable</td>
                              <td class = "clase08" colspan="06" align="center">Unidad Facturable</td>
                              <td class = "clase08" colspan="03" align="center">Cod Cebe</td>
                              <td class = "clase08" colspan="07" align="center">Descripci&oacute;n Corta</td>
                              <td class = "clase08" colspan="04" align="center">Base</td>
                              <td class = "clase08" colspan="05" align="center">Condici&oacute;n</td>
                              <td class = "clase08" colspan="04" align="center">Status</td>
                              <td class = "clase08" colspan="01" align="right">&nbsp;</td>
                            </tr>
                          </table>
                          <table border = "0" cellpadding = "0" cellspacing = "0" width = "900" id = "Grid_Certificacion"></table>
                        </fieldset>
                      </td>
                    </tr> -->

                    <!-- Correos en Copia -->
                    <tr>
                      <td Class = "clase08" colspan = "35">Correos en Copia (separados por coma)<br>
                        <input type="text" Class = "letra" name = "" id = "" style = 'width:700'>
                      </td>
                    </tr>
                    
                    <!-- Contenido -->
                    <tr>
                      <td Class = "clase08" colspan = "35">Contenido<br>
                          <textarea Class="letra" name="cCerObs" style="width:700;height:100;" onblur="javascript:this.value=this.value.toUpperCase();"></textarea>
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
      <table border="0" cellpadding="0" cellspacing="0" width="700">
        <tr height="21">
          <?php switch ($_COOKIE['kModo']) {
            case "VER": ?>
              <td width="889" height="21"></td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
            <?php break;
            default: ?>
              <td width="798" height="21"></td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:document.forms['frgrm'].submit();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
            <?php break;
          } ?>
        </tr>
      </table>
    </center>
    <?php
    switch ($_COOKIE['kModo']) {
      case "NUEVO":
        ?>
        <script languaje = "javascript">
          fnAddNewRowServicio('Grid_Servicios');
          fnCambiaTipoCertificacion(document.forms['frgrm']['cCerTip'].value);
        </script>
        <?php
      break;
      case "EDITAR":
        fnCargaData($cCerId,$cAnio);

        ?>
        <script languaje = "javascript">
          // Deshabilito los campos de cabecera
          document.forms['frgrm']['cComPre'].readOnly   = true;
          document.forms['frgrm']['cComPre'].onfocus    = "";
          document.forms['frgrm']['cComPre'].onblur     = "";
          document.forms['frgrm']['cComCsc'].readOnly   = true;
          document.forms['frgrm']['cComCsc'].onfocus    = "";
          document.forms['frgrm']['cComCsc'].onblur     = "";
          document.forms['frgrm']['cCliId'].readOnly    = true;
          document.forms['frgrm']['cCliId'].onfocus     = "";
          document.forms['frgrm']['cCliId'].onblur      = "";
          document.forms['frgrm']['cCliNom'].readOnly   = true;
          document.forms['frgrm']['cCliNom'].onfocus    = "";
          document.forms['frgrm']['cCliNom'].onblur     = "";
          document.forms['frgrm']['cCerTip'].disabled   = true;
          document.forms['frgrm']['dVigDesde'].readOnly = true;
          document.forms['frgrm']['dVigHasta'].readOnly = true;
          document.forms['frgrm']['cDepNum'].readOnly   = true;
          document.forms['frgrm']['cDepNum'].onfocus    = "";
          document.forms['frgrm']['cDepNum'].onblur     = "";
          document.forms['frgrm']['cTipoDep'].readOnly  = true;
          document.forms['frgrm']['cCdiSap'].readOnly   = true;
          document.forms['frgrm']['cCdiSap'].onfocus    = "";
          document.forms['frgrm']['cCdiSap'].onblur     = "";
          document.forms['frgrm']['cCdiDes'].readOnly   = true;
          document.forms['frgrm']['cCdiDes'].onfocus    = "";
          document.forms['frgrm']['cCdiDes'].onblur     = "";
          document.forms['frgrm']['cCerTipMe'].readOnly = true;

          // Deshabilito los link de los Valid/Windows
          document.getElementById('id_href_cCompre').removeAttribute('href');
          document.getElementById('id_href_CliId').removeAttribute('href');
          document.getElementById('id_href_DepNum').removeAttribute('href');
          document.getElementById('id_href_dVigDesde').removeAttribute('href');
          document.getElementById('id_href_dVigHasta').removeAttribute('href');
          document.getElementById('id_href_CdiSap').removeAttribute('href');
        </script>
      <?php      
      break;
      case "VER": 
        fnCargaData($cCerId,$cAnio);
        ?>
        <script languaje = "javascript">
          for (x=0;x<document.forms['frgrm'].elements.length;x++) {
            document.forms['frgrm'].elements[x].readOnly = true;
            document.forms['frgrm'].elements[x].onfocus  = "";
            document.forms['frgrm'].elements[x].onblur   = "";
          }
          document.forms['frgrm']['cCerTip'].disabled = true;
          document.forms['frgrm']['cPerAno'].disabled = true;

          // Deshabilita los link de los Valid/Windows
          document.getElementById('id_href_cCompre').removeAttribute('href');
          document.getElementById('id_href_CliId').removeAttribute('href');
          document.getElementById('id_href_DepNum').removeAttribute('href');
          document.getElementById('id_href_dVigDesde').removeAttribute('href');
          document.getElementById('id_href_dVigHasta').removeAttribute('href');
          document.getElementById('id_href_CdiSap').removeAttribute('href');
        </script>
      <?php
      break;
    }

    function fnCargaData($gCerId, $gAnio){
      global $cAlfa; global $xConexion01;

      // Consulta la informacion del comprobante seleccionado
      $qCertificacion  = "SELECT ";
      $qCertificacion .= "$cAlfa.lcca$gAnio.*, ";
      $qCertificacion .= "$cAlfa.lcca$gAnio.cliidxxx,";
      $qCertificacion .= "$cAlfa.lpar0150.clisapxx, ";
      $qCertificacion .= "IF($cAlfa.lpar0150.clinomxx != \"\",$cAlfa.lpar0150.clinomxx,REPLACE(CONCAT($cAlfa.lpar0150.clinom1x,\" \",$cAlfa.lpar0150.clinom2x,\" \",$cAlfa.lpar0150.cliape1x,\" \",$cAlfa.lpar0150.cliape2x), \"  \", \" \")) AS clinomxx, ";
      $qCertificacion .= "$cAlfa.lpar0008.cdisapxx, ";
      $qCertificacion .= "$cAlfa.lpar0008.cdidesxx ";
      $qCertificacion .= "FROM $cAlfa.lcca$gAnio ";
      $qCertificacion .= "LEFT JOIN $cAlfa.lpar0150 ON $cAlfa.lcca$gAnio.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
      $qCertificacion .= "LEFT JOIN $cAlfa.lpar0008 ON $cAlfa.lcca$gAnio.cdisapxx = $cAlfa.lpar0008.cdisapxx ";
      $qCertificacion .= "WHERE ";
      $qCertificacion .= "$cAlfa.lcca$gAnio.ceridxxx = \"$gCerId\" LIMIT 0,1 ";
      $xCertificacion  = f_MySql("SELECT","",$qCertificacion,$xConexion01,"");
      // echo $qCertificacion . " - " .  mysql_num_rows($xCertificacion);
      if (mysql_num_rows($xCertificacion) > 0) {
        $vCertificacion = mysql_fetch_array($xCertificacion);

        // Consulta la MIF en caso de haber sido guardada
        $vMatrizInsu = array();
        if ($vCertificacion['mifidxxx'] != "") {
          $cAnioMif = $vCertificacion['mifidano'];
          $qMatrizInsu  = "SELECT ";
          $qMatrizInsu .= "$cAlfa.lmca$cAnioMif.* ";
          $qMatrizInsu .= "FROM $cAlfa.lmca$cAnioMif ";
          $qMatrizInsu .= "WHERE ";
          $qMatrizInsu .= "$cAlfa.lmca$cAnioMif.mifidxxx = {$vCertificacion['mifidxxx']} LIMIT 0,1";
          $xMatrizInsu  = f_MySql("SELECT","",$qMatrizInsu,$xConexion01,"");
          // echo $qMatrizInsu . " - " .  mysql_num_rows($xMatrizInsu);
          if (mysql_num_rows($xMatrizInsu) > 0) {
            $vMatrizInsu = mysql_fetch_array($xMatrizInsu);
          }
        }

        // Consulta la información del Depósito
        $vDeposito  = array();
        $qDeposito  = "SELECT ";
        $qDeposito .= "lpar0155.depnumxx, ";
        $qDeposito .= "lpar0155.ccoidocx, ";
        $qDeposito .= "lpar0007.tdeidxxx, ";
        $qDeposito .= "lpar0007.tdedesxx, ";
        $qDeposito .= "lpar0001.orvsapxx, ";
        $qDeposito .= "lpar0001.orvdesxx, ";
        $qDeposito .= "lpar0002.ofvsapxx, ";
        $qDeposito .= "lpar0002.ofvdesxx, ";
        $qDeposito .= "lpar0003.closapxx, ";
        $qDeposito .= "lpar0003.clodesxx, ";
        $qDeposito .= "lpar0009.secsapxx, ";
        $qDeposito .= "lpar0009.secdesxx, ";
        $qDeposito .= "lpar0155.regestxx ";
        $qDeposito .= "FROM $cAlfa.lpar0155 ";                        
        $qDeposito .= "LEFT JOIN $cAlfa.lpar0007 ON $cAlfa.lpar0155.tdeidxxx = $cAlfa.lpar0007.tdeidxxx ";
        $qDeposito .= "LEFT JOIN $cAlfa.lpar0001 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0001.orvsapxx ";
        $qDeposito .= "LEFT JOIN $cAlfa.lpar0002 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0002.orvsapxx AND $cAlfa.lpar0155.ofvsapxx = $cAlfa.lpar0002.ofvsapxx ";
        $qDeposito .= "LEFT JOIN $cAlfa.lpar0003 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0003.orvsapxx AND $cAlfa.lpar0155.ofvsapxx = $cAlfa.lpar0003.ofvsapxx AND $cAlfa.lpar0155.closapxx = $cAlfa.lpar0003.closapxx ";
        $qDeposito .= "LEFT JOIN $cAlfa.lpar0009 ON $cAlfa.lpar0155.secsapxx = $cAlfa.lpar0009.secsapxx ";
        $qDeposito .= "WHERE ";
        $qDeposito .= "lpar0155.depnumxx = \"{$vCertificacion['depnumxx']}\"";
        $xDeposito  = f_MySql("SELECT","",$qDeposito,$xConexion01,"");
        if (mysql_num_rows($xDeposito) > 0) {
          $vDeposito = mysql_fetch_array($xDeposito);
        }

        // Consultando informacion del detalle de la certificacion
        $qCertifiDet  = "SELECT ";
        $qCertifiDet .= "$cAlfa.lcde$gAnio.*, ";
        $qCertifiDet .= "$cAlfa.lpar0004.obfidxxx, ";
        $qCertifiDet .= "$cAlfa.lpar0004.obfdesxx, ";
        $qCertifiDet .= "$cAlfa.lpar0006.ufaidxxx, ";
        $qCertifiDet .= "$cAlfa.lpar0006.ufadesxx, ";
        $qCertifiDet .= "$cAlfa.lpar0010.cebidxxx, ";
        $qCertifiDet .= "$cAlfa.lpar0010.cebcodxx, ";
        $qCertifiDet .= "$cAlfa.lpar0010.cebdesxx, ";
        $qCertifiDet .= "IF($cAlfa.lcde$gAnio.sersapxx != \"\",$cAlfa.lpar0011.serdesxx,\"\") AS serdesxx ";
        $qCertifiDet .= "FROM $cAlfa.lcde$gAnio ";
        $qCertifiDet .= "LEFT JOIN $cAlfa.lpar0006 ON $cAlfa.lcde$gAnio.ufaidxxx = $cAlfa.lpar0006.ufaidxxx ";
        $qCertifiDet .= "LEFT JOIN $cAlfa.lpar0004 ON $cAlfa.lcde$gAnio.obfidxxx = $cAlfa.lpar0004.obfidxxx ";
        $qCertifiDet .= "LEFT JOIN $cAlfa.lpar0010 ON $cAlfa.lcde$gAnio.cebidxxx = $cAlfa.lpar0010.cebidxxx ";
        $qCertifiDet .= "LEFT JOIN $cAlfa.lpar0011 ON $cAlfa.lcde$gAnio.sersapxx = $cAlfa.lpar0011.sersapxx ";
        $qCertifiDet .= "WHERE ";
        $qCertifiDet .= "$cAlfa.lcde$gAnio.ceridxxx = \"$gCerId\" ";
        $xCertifiDet  = f_MySql("SELECT","",$qCertifiDet,$xConexion01,"");
        // echo $qCertifiDet;

        ?>
        <script language = "javascript">
          fnCambiaTipoCertificacion('<?php echo $vCertificacion['certipxx'] ?>');

          document.forms['frgrm']['cComId'].value       = "<?php echo $vCertificacion['comidxxx'] ?>";
          document.forms['frgrm']['cComCod'].value      = "<?php echo $vCertificacion['comcodxx'] ?>";
          document.forms['frgrm']['cComPre'].value      = "<?php echo $vCertificacion['comprexx'] ?>";
          document.forms['frgrm']['cComCsc'].value      = "<?php echo $vCertificacion['comcscxx'] ?>";
          document.forms['frgrm']['cCliId'].value       = "<?php echo $vCertificacion['cliidxxx'] ?>";
          document.forms['frgrm']['cCliDV'].value       = "<?php echo gendv($vCertificacion['cliidxxx']) ?>";
          document.forms['frgrm']['cCliNom'].value      = "<?php echo $vCertificacion['clinomxx'] ?>";
          document.forms['frgrm']['cCliSap'].value      = "<?php echo $vCertificacion['clisapxx'] ?>";
          document.forms['frgrm']['dComFec'].value      = "<?php echo $vCertificacion['comfecxx'] ?>";
          document.forms['frgrm']['tComHCre'].value     = "<?php echo $vCertificacion['comhorxx'] ?>";
          document.forms['frgrm']['cCerTip'].value      = "<?php echo $vCertificacion['certipxx'] ?>";
          document.forms['frgrm']['cRegEst'].value      = "<?php echo $vCertificacion['regestxx'] ?>";

          // Informacion de la MIF
          document.forms['frgrm']['cPerAno'].value      = "<?php echo $vCertificacion['mifidano'] ?>";
          document.forms['frgrm']['cMifId'].value       = "<?php echo $vCertificacion['mifidxxx'] ?>";
          document.forms['frgrm']['cMifComId'].value    = "<?php echo $vMatrizInsu['comidxxx'] ?>";
          document.forms['frgrm']['cMifComCod'].value   = "<?php echo $vMatrizInsu['comcodxx'] ?>";
          document.forms['frgrm']['cMifComCsc'].value   = "<?php echo $vMatrizInsu['comcscxx'] ?>";
          document.forms['frgrm']['cMifComCsc2'].value  = "<?php echo $vMatrizInsu['comcsc2x'] ?>";
          document.forms['frgrm']['dVigDesde'].value    = "<?php echo $vCertificacion['cerfdexx'] ?>";
          document.forms['frgrm']['dVigHasta'].value    = "<?php echo $vCertificacion['cerfhaxx'] ?>";
          // Informacion del Deposito
          document.forms['frgrm']['cDepNum'].value      = "<?php echo $vDeposito['depnumxx'] ?>";
          document.forms['frgrm']['cDepNum_hidd'].value = "<?php echo $vDeposito['depnumxx'] ?>";
          document.forms['frgrm']['cTipoDep'].value     = "<?php echo $vDeposito['tdedesxx'] ?>";
          document.forms['frgrm']['cCcoIdOc'].value     = "<?php echo $vDeposito['ccoidocx'] ?>";
          document.forms['frgrm']['cOrvSap'].value      = "<?php echo $vDeposito['orvsapxx'] ?>";
          document.forms['frgrm']['cOrvDes'].value      = "<?php echo $vDeposito['orvdesxx'] ?>";
          document.forms['frgrm']['cOfvSap'].value      = "<?php echo $vDeposito['ofvsapxx'] ?>";
          document.forms['frgrm']['cOfvDes'].value      = "<?php echo $vDeposito['ofvdesxx'] ?>";
          document.forms['frgrm']['cCloSap'].value      = "<?php echo $vDeposito['closapxx'] ?>";
          document.forms['frgrm']['cCloDes'].value      = "<?php echo $vDeposito['closapxx'] ?>";
          document.forms['frgrm']['cSecSap'].value      = "<?php echo $vDeposito['secsapxx'] ?>";
          document.forms['frgrm']['cSecDes'].value      = "<?php echo $vDeposito['secdesxx'] ?>";
          document.forms['frgrm']['cCdiSap'].value      = "<?php echo $vCertificacion['cdisapxx'] ?>";
          document.forms['frgrm']['cCdiDes'].value      = "<?php echo $vCertificacion['cdidesxx'] ?>";
          document.forms['frgrm']['cCerTipMe'].value    = "<?php echo $vCertificacion['certipme'] ?>";
          document.forms['frgrm']['cCerObs'].value      = "<?php echo $vCertificacion['cerobsxx'] ?>";

          // Grilla Datos de Servicios a Certificar - AUTOMATICOS
          if ("<?php echo $vCertificacion['certipxx'] ?>" == "AUTOMATICA") {
            fnHabilitaServicios('SI');
          }

          // Grilla Datos de Servicios y Certificacion  - MANUAL
          <?php 
          $nCountCert = 0;
          $nCountServ = 0;
          if (mysql_num_rows($xCertifiDet) > 0) {
            while ($xRCD = mysql_fetch_array($xCertifiDet)) {
              $nCountCert++;
              
              if ($xRCD['cerdestx'] != "AUTOMATICO") {
                if ($xRCD['sersapxx'] != "") {
                  $nCountServ++;
                  if ($nCountServ > 1) { ?>
                    fnAddNewRowServicio('Grid_Servicios');
                  <?php } ?>
                  document.forms['frgrm']['cSerSap'+'<?php echo $nCountServ ?>'].value = '<?php echo $xRCD['sersapxx'] ?>';
                  document.forms['frgrm']['cSerDes'+'<?php echo $nCountServ ?>'].value = '<?php echo $xRCD['serdesxx'] ?>';
                  document.forms['frgrm']['cSubId'+'<?php echo $nCountServ ?>'].value  = '<?php echo $xRCD['subidxxx'] ?>';
                  document.forms['frgrm']['cSubDes'+'<?php echo $nCountServ ?>'].value = '<?php echo $xRCD['subdesxx'] ?>';
                  document.forms['frgrm']['cUfaId'+'<?php echo $nCountServ ?>'].value  = '<?php echo $xRCD['ufaidxxx'] ?>';
                  document.forms['frgrm']['cUfaDes'+'<?php echo $nCountServ ?>'].value = '<?php echo $xRCD['ufadesxx'] ?>';
                  <?php 
                  if ($_COOKIE['kModo'] == "VER") { ?>
                    document.forms['frgrm']['oBtnDel'+'<?php echo $nCountServ ?>'].disabled = true;
                  <?php }
                } 
                ?>

                // Asigna los valores a la grilla de Certificacion
                fnAddNewRowCertificacion('Grid_Certificacion', '<?php echo $nCountCert ?>', '<?php echo $xRCD['cerdorix'] ?>');
                setTimeout(function(){
                  document.forms['frgrm']['cCerdId'+'<?php echo $nCountCert ?>'].value       = '<?php echo $xRCD['cerdidxx'] ?>';
                  document.forms['frgrm']['cDesMaterial'+'<?php echo $nCountCert ?>'].value  = '<?php echo $xRCD['subdesxx'] ?>';
                  document.forms['frgrm']['cIndexServ'+'<?php echo $nCountCert ?>'].value    = '<?php echo $nCountServ ?>';
                  document.forms['frgrm']['cCodSapSer'+'<?php echo $nCountCert ?>'].value    = '<?php echo $xRCD['sersapxx'] ?>';
                  document.forms['frgrm']['cSubId_Certi'+'<?php echo $nCountCert ?>'].value  = '<?php echo $xRCD['subidxxx'] ?>';
                  document.forms['frgrm']['cObfId_Certi'+'<?php echo $nCountCert ?>'].value  = '<?php echo $xRCD['obfidxxx'] ?>';
                  document.forms['frgrm']['cObfDes_Certi'+'<?php echo $nCountCert ?>'].value = '<?php echo $xRCD['obfdesxx'] ?>';
                  document.forms['frgrm']['cUfaId_Certi'+'<?php echo $nCountCert ?>'].value  = '<?php echo $xRCD['ufaidxxx'] ?>';
                  document.forms['frgrm']['cUfaDes_Certi'+'<?php echo $nCountCert ?>'].value = '<?php echo $xRCD['ufadesxx'] ?>';
                  document.forms['frgrm']['cCebId'+'<?php echo $nCountCert ?>'].value        = '<?php echo $xRCD['cebidxxx'] ?>';
                  document.forms['frgrm']['cCebCod'+'<?php echo $nCountCert ?>'].value       = '<?php echo $xRCD['cebcodxx'] ?>';
                  document.forms['frgrm']['cCebDes'+'<?php echo $nCountCert ?>'].value       = '<?php echo $xRCD['cebdesxx'] ?>';
                  document.forms['frgrm']['cBase'+'<?php echo $nCountCert ?>'].value         = '<?php echo $xRCD['basexxxx'] ?>';
                  document.forms['frgrm']['cCondicion'+'<?php echo $nCountCert ?>'].value    = '<?php echo $xRCD['cerdconx'] ?>';
                  document.forms['frgrm']['cStatus'+'<?php echo $nCountCert ?>'].value       = '<?php echo $xRCD['cerdestx'] ?>';
                  document.forms['frgrm']['cTipoCerti'+'<?php echo $nCountCert ?>'].value    = '<?php echo $xRCD['cerdorix'] ?>';
                }, 500);

                <?php if ($_COOKIE['kModo'] == "VER") { ?>
                  fnActivarDesactivarCamposCertificacion('<?php echo $nCountCert ?>', true);
                <?php }
              }
            }
          }
          ?>
        </script>
      <?php
      }
    } ?>
  </body>
</html>

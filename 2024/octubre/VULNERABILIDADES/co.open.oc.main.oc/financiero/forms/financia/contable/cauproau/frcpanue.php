<?php
  namespace openComex;
  /**
   * Comprobante P-28 (Causaciones Automaticas).
   * --- Descripcion: Permite Crear Nueva Causacion .
   * @author Alexander Gordillo <alexanderg@repremundo.com.co>
   * @version 001
   */
  ini_set('error_reporting', E_ERROR);
  ini_set("display_errors","1");

  /* Cargo a gUsrId con el vvalor de la COOKIE*/
  include("../../../../libs/php/utility.php");
  include("../../../../libs/php/uticonta.php");
?>
<html>
  <head>
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
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
          case "cComCod":
            if (xSwitch == "VALID") {
              var cPathUrl = "frcpa117.php?gModo="+xSwitch+"&gFunction="+xLink+
                                         "&gComCod="+document.forms['frgrm']['cComCod'].value.toUpperCase();
              //alert(cPathUrl);
              parent.fmpro.location = cPathUrl;
            } else {
              var nNx      = (nX-500)/2;
              var nNy      = (nY-250)/2;
              var cWinOpt  = "width=500,scrollbars=1,height=250,left="+nNx+",top="+nNy;
              var cPathUrl = "frcpa117.php?gModo="+xSwitch+"&gFunction="+xLink+
                                         "&gComCod="+document.forms['frgrm']['cComCod'].value.toUpperCase();
              cWindow = window.open(cPathUrl,xLink,cWinOpt);
              cWindow.focus();
            }
          break;
          case "cCcoId":
            var cCcoId = "";
            switch (xType) {
              case "GRID": cCcoId = document.forms['frgrm']['cCcoId'+xSecuencia].value.toUpperCase(); break;
              default:     cCcoId = document.forms['frgrm']['cCcoId'].value.toUpperCase();            break;
            }
            if (xSwitch == "VALID") {
              var cPathUrl = "frcpa116.php?gModo="+xSwitch+"&gFunction="+xLink+
                                         "&gCcoId="+cCcoId+
                                         "&gType="+xType+
                                         "&gSecuencia="+xSecuencia+
                                         "&gComId="+document.forms['frgrm']['cComId'].value.toUpperCase()+
                                         "&gComCod="+document.forms['frgrm']['cComCod'].value.toUpperCase();
              //alert(cPathUrl);
              parent.fmpro.location = cPathUrl;
            } else {
              var nNx      = (nX-300)/2;
              var nNy      = (nY-250)/2;
              var cWinOpt  = "width=300,scrollbars=1,height=250,left="+nNx+",top="+nNy;
              var cPathUrl = "frcpa116.php?gModo="+xSwitch+"&gFunction="+xLink+
                                          "&gCcoId="+cCcoId+
                                          "&gType="+xType+
                                          "&gSecuencia="+xSecuencia+
                                          "&gComId="+document.forms['frgrm']['cComId'].value.toUpperCase()+
                                          "&gComCod="+document.forms['frgrm']['cComCod'].value.toUpperCase();
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
                var cPathUrl = "frcpa120.php?gModo="+xSwitch+"&gFunction="+xLink+
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
                var cPathUrl = "frcpa120.php?gModo="+xSwitch+"&gFunction="+xLink+
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
          case "cComCsc":
            var nBand = 0; var cMsj = "";

            if (document.forms['frgrm']['cComCsc'].value != "") {
              if ((document.forms['frgrm']['cComId'].value.toUpperCase() == "" || document.forms['frgrm']['cComCod'].value.toUpperCase() == "") && document.forms['frgrm']['cComTco'].value.toUpperCase() != "AUTOMATICO") {
                  nBand = 1;
                  cMsj += "Debe Seleccionar el Comprobante.\n";
              }

              if (nBand == 0) {
                if (document.forms['frgrm']['cTerIdB'].value != "" && cComTco != "AUTOMATICO") {
                  var cPathUrl = "frcpacsc.php?&gTerId="+document.forms['frgrm']['cTerIdB'].value+
                                              "&gComId="+document.forms['frgrm']['cComId'].value.toUpperCase()+
                                              "&gComCod="+document.forms['frgrm']['cComCod'].value.toUpperCase()+
                                              "&gComCsc="+document.forms['frgrm']['cComCsc'].value.toUpperCase()+
                                              "&gComTco="+document.forms['frgrm']['cComTco'].value.toUpperCase();
                  //alert(cPathUrl);
                  parent.fmpro.location = cPathUrl;
                }
              } else {
                alert (cMsj + "Verifique.");
              }
            }
          break;
          case "cTerId":
          case "cTerIdB":
          case "cTerNom":
          case "cTerNomB":

            var nBand = 0; var cMsj = "";

            var cComId  = document.forms['frgrm']['cComId'].value.toUpperCase();
            var cComCod = document.forms['frgrm']['cComCod'].value.toUpperCase();
            var cComCsc = document.forms['frgrm']['cComCsc'].value.toUpperCase();
            var cComTco = document.forms['frgrm']['cComTco'].value.toUpperCase();

            if (xLink == "cTerId" || xLink == "cTerNom") {
              var cTerTip = document.forms['frgrm']['cTerTip'].value.toUpperCase();
              var cTerId  = document.forms['frgrm']['cTerId'].value.toUpperCase();
              var cTerNom = document.forms['frgrm']['cTerNom'].value.toUpperCase();
            } else if (xLink == "cTerIdB" || xLink == "cTerNomB") {
              var cTerTip = document.forms['frgrm']['cTerTipB'].value.toUpperCase();
              var cTerId  = document.forms['frgrm']['cTerIdB'].value.toUpperCase();
              var cTerNom = document.forms['frgrm']['cTerNomB'].value.toUpperCase();

              if(document.forms['frgrm']['nSecuencia_DO'].value == 1 &&
                  document.forms['frgrm']['cSucId_DO1'].value  != '' &&
                  document.forms['frgrm']['cDocId_DO1'].value  != '' &&
                  document.forms['frgrm']['cDocSuf_DO1'].value != '' &&
                  document.forms['frgrm']['cTerId_DO1'].value == document.forms['frgrm']['cTerId'].value) {
                  document.forms['frgrm']['cTerTipB_DO1'].value = '';
                  document.forms['frgrm']['cTerIdB_DO1'].value = '';
              }

              if ((cComId == "" || cComCod == "" || cComCsc == "") && cComTco != "AUTOMATICO") {
                nBand = 1;
                cMsj += "Debe Seleccionar el Comprobante y Digitar el No. de la Factura.\n";
                // document.forms['frgrm']['cTerTipB'].value  = "";
                document.forms['frgrm']['cTerIdB'].value   = "";
                document.forms['frgrm']['cTerNomB'].value  = "";
              }
            }

            if (nBand == 0) {

              if (xSwitch == "VALID") {
                var cPathUrl = "frcpa150.php?gModo="+xSwitch+"&gFunction="+xLink+
                                          "&gTerTip="+cTerTip+
                                          "&gTerId=" +cTerId+
                                          "&gTerNom="+cTerNom+
                                          "&gComId="+cComId+
                                          "&gComCod="+cComCod+
                                          "&gComCsc="+cComCsc+
                                          "&gComTco="+cComTco;
                //alert(cPathUrl);
                parent.fmpro.location = cPathUrl;
              } else {
                var nNx      = (nX-600)/2;
                var nNy      = (nY-250)/2;
                var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
                var cPathUrl = "frcpa150.php?gModo="+xSwitch+"&gFunction="+xLink+
                                           "&gTerTip="+cTerTip+
                                           "&gTerId=" +cTerId+
                                           "&gTerNom="+cTerNom+
                                           "&gComId="+cComId+
                                           "&gComCod="+cComCod+
                                           "&gComCsc="+cComCsc+
                                           "&gComTco="+cComTco;
                cWindow = window.open(cPathUrl,xLink,cWinOpt);
                cWindow.focus();
              }
              f_Calcular_Total_Iva_Total_base();
            } else {
              alert (cMsj + "Verifique.");
            }
          break;

          /* Funciones de la Grilla */
          case "cDocId_DO":

            var nSwicht = 0;
            var cMsj = "";

            if (document.forms['frgrm']['cDocId_DO' +xSecuencia].value == "") {
              document.forms['frgrm']['cSucId_DO' +xSecuencia].value = "";
              document.forms['frgrm']['cDocSuf_DO'+xSecuencia].value = "";
            }

            //Si el Do ha Cambiado en el Item, Limpio Todo el Row
            if (document.forms['frgrm']['cSucId_DO' +xSecuencia].id != document.forms['frgrm']['cSucId_DO' +xSecuencia].value &&
                document.forms['frgrm']['cDocId_DO' +xSecuencia].id != document.forms['frgrm']['cDocId_DO' +xSecuencia].value &&
                document.forms['frgrm']['cDocSuf_DO'+xSecuencia].id != document.forms['frgrm']['cDocSuf_DO'+xSecuencia].value) {

              /*
                document.forms['frgrm']['cSucId_DO'   +xSecuencia].value = "";
                document.forms['frgrm']['cDocId_DO'   +xSecuencia].value = "";
                document.forms['frgrm']['cDocSuf_DO'  +xSecuencia].value = "";
                document.forms['frgrm']['cTerId_DO'   +xSecuencia].value = "";
                document.forms['frgrm']['cTerNom_DO'  +xSecuencia].value = "";
                document.forms['frgrm']['cTerTip_DO'  +xSecuencia].value = "";
                document.forms['frgrm']['cTerTipB_DO' +xSecuencia].value = "";
                document.forms['frgrm']['cTerIdB_DO'  +xSecuencia].value = "";
                document.forms['frgrm']['cDocFec_DO'  +xSecuencia].value = "";
                document.forms['frgrm']['cCcoId_DO'   +xSecuencia].value = "";
                document.forms['frgrm']['nVlrPro_DO'  +xSecuencia].value = "";
              */

              if(document.forms['frgrm']['cSucId_DO' +xSecuencia].id != '' &&
                 document.forms['frgrm']['cDocId_DO' +xSecuencia].id != '' &&
                 document.forms['frgrm']['cDocSuf_DO'+xSecuencia].id != '') {
                f_Borrar_Conceptos_Do(document.forms['frgrm']['cSucId_DO'+xSecuencia].id,document.forms['frgrm']['cDocId_DO'+xSecuencia].id,document.forms['frgrm']['cDocSuf_DO'+xSecuencia].id);
              }
            }

            //Validando datos cabecera
            if (document.forms['frgrm']['cComId'].value.length    > 0 &&
                document.forms['frgrm']['cComCod'].value.length   > 0 &&
                document.forms['frgrm']['cCcoId'].value.length    > 0 &&
                document.forms['frgrm']['cTerId'].value.length    > 0 &&
                document.forms['frgrm']['cTerIdB'].value.length    > 0 &&
                ( document.forms['frgrm']['nComVlr01'].value.length > 0 || 
                  /* Permitir cargar DO  PARA GRUMALCO SI NO SE HA INGRESADO LA BASE TOTAL O EL TOTAL IVA */
                  ('<?php echo $kDf[3] ?>' == 'DEGRUMALCO' || '<?php echo $kDf[3] ?>' == 'TEGRUMALCO' || '<?php echo $kDf[3] ?>' == 'GRUMALCO') ||
                  ('<?php echo $kDf[3] ?>' == 'DEDSVSASXX' || '<?php echo $kDf[3] ?>' == 'TEDSVSASXX' || '<?php echo $kDf[3] ?>' == 'DSVSASXX') 
                ) && 
                document.forms['frgrm']['cTipPro'].value.length    > 0 ) {
              //No hace nada
            } else{
              nSwicht = 1;
              cMsj += "No Hay Datos de Cabecera del Comprobante Digitados.\n";
            }

            //Validando si aplica tasa pactada
            if (document.forms['frgrm']['cCliTp'].value.length <= 0 && document.forms['frgrm']['cCliTpApl'].checked == true) {
              nSwicht = 1;
              cMsj += "Selecciono que Aplica Tasa Pactada, pero el Valor de la Tasa Pactada es Menor a Cero (0).\n";
            }

            //Validando si aplica tasa de pago
            if (document.forms['frgrm']['cCliTpag'].value.length <= 0 && document.forms['frgrm']['cCliTpagApl'].checked == true) {
              nSwicht = 1;
              cMsj += "Selecciono que Aplica Tasa de Pago, pero el Valor de la Tasa de Pago es Menor a Cero (0).\n";
            }

            //Validando si aplica base A.I.U
            if (document.forms['frgrm']['nAiuVlr01'].value.length <= 0 && document.forms['frgrm']['cAiuApl'].checked == true) {
              nSwicht = 1;
              cMsj += "Selecciono que Aplica Base A.I.U, pero el Valor de la Base A.I.U es Menor a Cero (0).\n";
            }

            if (document.forms['frgrm']['cAiuApl'].checked == true &&
                parseFloat(document.forms['frgrm']['nAiuVlr01'].value) > parseFloat(document.forms['frgrm']['nComVlr01'].value)) {
              nSwicht = 1;
              cMsj += "La Base A.I.U No Debe Ser Mayor a la Base del Comprobante.\n";
            }


            if (nSwicht == 0) {

              f_Borrar_Tabla('Grid_Comprobante');

              if (!(document.forms['frgrm']['cSucId_DO' +xSecuencia].id != document.forms['frgrm']['cSucId_DO' +xSecuencia].value &&
                   document.forms['frgrm']['cDocId_DO' +xSecuencia].id != document.forms['frgrm']['cDocId_DO' +xSecuencia].value &&
                   document.forms['frgrm']['cDocSuf_DO'+xSecuencia].id != document.forms['frgrm']['cDocSuf_DO'+xSecuencia].value) ) {
                if (xSecuencia > 0){
                  document.forms['frgrm']['cSucId_DO' +xSecuencia].id = "";
                  document.forms['frgrm']['cDocId_DO' +xSecuencia].id = "";
                  document.forms['frgrm']['cDocSuf_DO'+xSecuencia].id = "";
                }
              }

              if ((document.forms['frgrm']['cSucId_DO' +xSecuencia].id != document.forms['frgrm']['cSucId_DO' +xSecuencia].value &&
                   document.forms['frgrm']['cDocId_DO' +xSecuencia].id != document.forms['frgrm']['cDocId_DO' +xSecuencia].value &&
                   document.forms['frgrm']['cDocSuf_DO'+xSecuencia].id != document.forms['frgrm']['cDocSuf_DO'+xSecuencia].value) ||
                  document.forms['frgrm']['cDocId_DO'+xSecuencia].id == '') {

                var nNx      = (nX-500)/2;
                var nNy      = (nY-250)/2;
                var cWinOpt  = "width=500,scrollbars=1,height=250,left="+nNx+",top="+nNy;
                var cPathUrl = "frcpafrm.php?gModo="+xSwitch+"&gFunction="+xLink+
                               "&gTerTip="   +document.forms.frgrm['cTerTip'].value.toUpperCase()+
                               "&gTerId="    +document.forms.frgrm['cTerId'].value.toUpperCase()+
                               "&gTerTipB="  +document.forms.frgrm['cTerTipB'].value.toUpperCase()+
                               "&gTerIdB="   +document.forms.frgrm['cTerIdB'].value.toUpperCase()+
                               "&gDo="   +document.forms.frgrm['cDocId_DO'+xSecuencia].value.toUpperCase()+
                               "&gSecuencia="+xSecuencia;
                document.forms['frgrm']['cDocId_DO' +xSecuencia].value = "";
                //alert(cPathUrl);
                cWindow = window.open(cPathUrl,xLink,cWinOpt);
                cWindow.focus();
              }
            } else {
              alert(cMsj + "Verifique.");
            }
          break;
          case "cCcoId_CCO":

            var nSwicht = 0;
            var cMsj = "";

            if (document.forms['frgrm']['cCcoId_CCO'   +xSecuencia].id != document.forms['frgrm']['cCcoId_CCO'+xSecuencia].value) {
                document.forms['frgrm']['cCcoId_CCO'   +xSecuencia].value = "";
                document.forms['frgrm']['cCcoDes_CCO'  +xSecuencia].value = "";
                document.forms['frgrm']['cSucId_CCO'   +xSecuencia].value = "";
                document.forms['frgrm']['cDocId_CCO'   +xSecuencia].value = "";
                document.forms['frgrm']['cDocSuf_CCO'  +xSecuencia].value = "";
                document.forms['frgrm']['nVlrBaiu_CCO' +xSecuencia].value = "";
                document.forms['frgrm']['nVlrBase_CCO' +xSecuencia].value = "";
                document.forms['frgrm']['nVlrIva_CCO'  +xSecuencia].value = "";
                document.forms['frgrm']['nVlr_CCO'     +xSecuencia].value = "";
                document.forms['frgrm']['cCtoVrl02_CCO'+xSecuencia].value = "";
            }

            if (document.forms.frgrm['cTipPro'].value == 'VALOR' && f_Verificar_Do() == false) {
              nSwicht = 1;
              cMsj += "Debe Seleccionar al Menos un DO.\n";
            }

            if (!(document.forms['frgrm']['cComId'].value.length  > 0 &&
                document.forms['frgrm']['cComCod'].value.length   > 0 &&
                document.forms['frgrm']['cCcoId'].value.length    > 0 &&
                document.forms['frgrm']['cTerId'].value.length    > 0 &&
                document.forms['frgrm']['cTerIdB'].value.length   > 0 &&
                document.forms['frgrm']['nComVlr01'].value.length > 0)) {
              nSwicht = 1;
              cMsj += "No Hay Datos de Cabecera del Comprobante Digitados.\n";
            }

            //Validando si aplica tasa pactada
            if (document.forms['frgrm']['cCliTp'].value.length <= 0 && document.forms['frgrm']['cCliTpApl'].checked == true) {
              nSwicht = 1;
              cMsj += "Selecciono que Aplica Tasa Pactada, pero el Valor de la Tasa Pactada es Menor a Cero (0).\n";
            }

            //Validando si aplica tasa de pago
            if (document.forms['frgrm']['cCliTpag'].value.length <= 0 && document.forms['frgrm']['cCliTpagApl'].checked == true) {
              nSwicht = 1;
              cMsj += "Selecciono que Aplica Tasa de Pago, pero el Valor de la Tasa de Pago es Menor a Cero (0).\n";
            }

            if (nSwicht == 0) {

              f_Borrar_Tabla('Grid_Comprobante');

              if (document.forms['frgrm']['cCcoId_CCO'   +xSecuencia].id != document.forms['frgrm']['cCcoId_CCO'+xSecuencia].value ||
                  document.forms['frgrm']['cCcoId_CCO'   +xSecuencia].id == '') {
                //Si el Concepto ha Cambiado en el Item, Limpio Todo el Row
                var cCcoId = document.forms['frgrm']['cCcoId_CCO'+xSecuencia].value.toUpperCase();

                /*** Si la variable Categoriacion Conceptos Factuacion esta encendida se habilita el menu de Categoria Conceptos***/
                if('<?php echo $vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI' ?>'){
                  var nNx      = (nX-750)/2;
                  var nNy      = (nY-300)/2;
                  var cWinOpt  = "width=750,scrollbars=1,height=300,left="+nNx+",top="+nNy;
                }else{
                  var nNx      = (nX-500)/2;
                  var nNy      = (nY-250)/2;
                  var cWinOpt  = "width=500,scrollbars=1,height=250,left="+nNx+",top="+nNy;
                }
                var cPathUrl = "frccofrm.php?gModo="+xSwitch+"&gFunction="+xLink+
                               "&gTerTipB="  +document.forms.frgrm['cTerTipB'].value.toUpperCase()+
                               "&gTerIdB="   +document.forms.frgrm['cTerIdB'].value.toUpperCase()+
                               "&gComId="    +document.forms.frgrm['cComId'].value.toUpperCase()+
                               "&gComCod="   +document.forms.frgrm['cComCod'].value.toUpperCase()+
                               "&gCcoId="    +cCcoId+
                               "&gSecuencia="+xSecuencia+
                               "&gCaso=2";  //Envio Caso = 2; en el marco se utiliza para saber que script redireccionar
                //alert(cPathUrl);

                cWindow = window.open(cPathUrl,xLink,cWinOpt);
                cWindow.focus();
              }
            } else {
              alert(cMsj + "Verifique.");
            }
          break;
          case "cCtoId":

            var nSwicht = 0;
            var cMsj = "";

            if (document.forms['frgrm']['cComId'].value.length    > 0 &&
                document.forms['frgrm']['cComCod'].value.length   > 0 &&
                document.forms['frgrm']['cCcoId'].value.length    > 0 &&
                ( document.forms['frgrm']['nComVlr01'].value.length > 0 || 
                  /* Permitir cargar DO  PARA GRUMALCO SI NO SE HA INGRESADO LA BASE TOTAL O EL TOTAL IVA */
                  ('<?php echo $kDf[3] ?>' == 'DEGRUMALCO' || '<?php echo $kDf[3] ?>' == 'TEGRUMALCO' || '<?php echo $kDf[3] ?>' == 'GRUMALCO')  ||
                  ('<?php echo $kDf[3] ?>' == 'DEDSVSASXX' || '<?php echo $kDf[3] ?>' == 'TEDSVSASXX' || '<?php echo $kDf[3] ?>' == 'DSVSASXX') 
                ) && 
                document.forms['frgrm']['cTerId'].value.length    > 0 ) {
            } else {
              nSwicht = 1;
              cMsj += "No Hay Datos de Cabecera del Comprobante Digitados.\n";
            }

            //Validando si aplica tasa pactada
            if (document.forms['frgrm']['cCliTp'].value.length <= 0 && document.forms['frgrm']['cCliTpApl'].checked == true) {
              nSwicht = 1;
              cMsj += "Selecciono que Aplica Tasa Pactada, pero el Valor de la Tasa Pactada es Menor a Cero (0).\n";
            }

            //Validando si aplica tasa de pago
            if (document.forms['frgrm']['cCliTpag'].value.length <= 0 && document.forms['frgrm']['cCliTpagApl'].checked == true) {
              nSwicht = 1;
              cMsj += "Selecciono que Aplica Tasa de Pago, pero el Valor de la Tasa de Pago es Menor a Cero (0).\n";
            }

            if (nSwicht == 0) {

              //Si el Concepto ha Cambiado en el Item, Limpio Todo el Row
              if (document.forms['frgrm']['cCtoId'+xSecuencia].id != document.forms['frgrm']['cCtoId'+xSecuencia].value) {
                document.forms['frgrm']['cInvLin'  +xSecuencia].value = "";
                document.forms['frgrm']['cInvGru'  +xSecuencia].value = "";
                document.forms['frgrm']['cInvPro'  +xSecuencia].value = "";
                document.forms['frgrm']['nInvCos'  +xSecuencia].value = "";
                document.forms['frgrm']['nInvCan'  +xSecuencia].value = "";
                document.forms['frgrm']['cInvBod'  +xSecuencia].value = "";
                document.forms['frgrm']['cInvUbi'  +xSecuencia].value = "";
                document.forms['frgrm']['cCtoDes'  +xSecuencia].value = "";
                document.forms['frgrm']['cComObs'  +xSecuencia].value = "";
                document.forms['frgrm']['cComIdC'  +xSecuencia].value = "";
                document.forms['frgrm']['cComCodC' +xSecuencia].value = "";
                document.forms['frgrm']['cComCscC' +xSecuencia].value = "";
                document.forms['frgrm']['cComSeqC' +xSecuencia].value = "";
                document.forms['frgrm']['cCcoId'   +xSecuencia].value = "";
                document.forms['frgrm']['cSccId'   +xSecuencia].value = "";
                document.forms['frgrm']['cComCtoC' +xSecuencia].value = "";
                document.forms['frgrm']['nComBRet' +xSecuencia].value = "";
                document.forms['frgrm']['nComBIva' +xSecuencia].value = "";
                document.forms['frgrm']['nComIva'  +xSecuencia].value = "";
                document.forms['frgrm']['nComVlr'  +xSecuencia].value = "";
                document.forms['frgrm']['nComVlrNF'+xSecuencia].value = "";
                document.forms['frgrm']['cComMov'  +xSecuencia].value = "";
                document.forms['frgrm']['cComNit'  +xSecuencia].value = "";
                document.forms['frgrm']['cTerTip'  +xSecuencia].value = "";
                document.forms['frgrm']['cTerId'   +xSecuencia].value = "";
                document.forms['frgrm']['cTerTipB' +xSecuencia].value = "";
                document.forms['frgrm']['cTerIdB'  +xSecuencia].value = "";
                document.forms['frgrm']['cPucId'   +xSecuencia].value = "";
                document.forms['frgrm']['cPucDet'  +xSecuencia].value = "";
                document.forms['frgrm']['cPucTer'  +xSecuencia].value = "";
                document.forms['frgrm']['nPucBRet' +xSecuencia].value = "";
                document.forms['frgrm']['nPucRet'  +xSecuencia].value = "";
                document.forms['frgrm']['cPucNat'  +xSecuencia].value = "";
                document.forms['frgrm']['cPucInv'  +xSecuencia].value = "";
                document.forms['frgrm']['cPucCco'  +xSecuencia].value = "";
                document.forms['frgrm']['cPucDoSc' +xSecuencia].value = "";
                document.forms['frgrm']['cPucTipEj'+xSecuencia].value = "";
                document.forms['frgrm']['cComVlr1' +xSecuencia].value = "";
                document.forms['frgrm']['cComVlr2' +xSecuencia].value = "";
                document.forms['frgrm']['cComFac'  +xSecuencia].value = "";
                document.forms['frgrm']['cSucId'   +xSecuencia].value = "";
                document.forms['frgrm']['cDocId'   +xSecuencia].value = "";
                document.forms['frgrm']['cDocSuf'  +xSecuencia].value = "";
                //Campos de intermediacion de pago
                document.forms['frgrm']['cComIdCB'    +xSecuencia].value = "";
                document.forms['frgrm']['cComCodCB'   +xSecuencia].value = "";
                document.forms['frgrm']['cComCscCB'   +xSecuencia].value = "";
                document.forms['frgrm']['cComSeqCB'   +xSecuencia].value = "";
                document.forms['frgrm']['cCtoIdInp'   +xSecuencia].value = "";
                document.forms['frgrm']['cPucIdInp'   +xSecuencia].value = "";
                document.forms['frgrm']['cPucDetInp'  +xSecuencia].value = "";
                document.forms['frgrm']['cPucTerInp'  +xSecuencia].value = "";
                document.forms['frgrm']['nPucBRetInp' +xSecuencia].value = "";
                document.forms['frgrm']['nPucRetInp'  +xSecuencia].value = "";
                document.forms['frgrm']['cPucNatInp'  +xSecuencia].value = "";
                document.forms['frgrm']['cPucInvInp'  +xSecuencia].value = "";
                document.forms['frgrm']['cPucCcoInp'  +xSecuencia].value = "";
                document.forms['frgrm']['cPucDoScInp' +xSecuencia].value = "";
                document.forms['frgrm']['cPucTipEjInp'+xSecuencia].value = "";
                document.forms['frgrm']['cComVlr1Inp' +xSecuencia].value = "";
                document.forms['frgrm']['cComVlr2Inp' +xSecuencia].value = "";

                document.forms['frgrm']['nComBRet'+xSecuencia].disabled = true;
                document.forms['frgrm']['nComBIva'+xSecuencia].disabled = true;
                document.forms['frgrm']['nComIva' +xSecuencia].disabled = true;

                document.getElementById('cComSeq' +xSecuencia).style.color = "#000000";
                document.getElementById('cCtoDes' +xSecuencia).style.color = "#000000";
              }


              if (document.forms['frgrm']['cCtoId'+1].id == "" &&
                  document.forms['frgrm']['cCtoId'+1].value == "") {
                f_Enabled_Combos();
                document.forms['frgrm'].target="fmpro";
                document.forms['frgrm'].action="frcpaaut.php";
                document.forms['frgrm'].submit();
                document.forms['frgrm'].action="frcpagra.php";
                f_Disabled_Combos();
              } else {
                if (xSwitch == "VALID") {
                  /*** frccofrm --> Marco para la ventana del validWindows de Conceptos contables ***/
                  var cPathUrl = "frccofrm.php?gModo="+xSwitch+"&gFunction="+xLink+
                                               "&gComId="+document.forms['frgrm']['cComId'].value.toUpperCase()+
                                               "&gComCod="+document.forms['frgrm']['cComCod'].value.toUpperCase()+
                                               "&gCtoId="+document.forms['frgrm']['cCtoId'+xSecuencia].value.toUpperCase()+
                                               "&gSecuencia="+xSecuencia+
                                               "&gCaso=1";  //Envio Caso = 1; en el marco se utiliza para saber que script redireccionar
                  //alert(cPathUrl);
                  parent.fmpro.location = cPathUrl;
                } else {
                  /*** Si la variable Categoriacion Conceptos Factuacion esta encendida se habilita el menu de Categoria Conceptos***/
                  if('<?php echo $vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI' ?>'){
                    var nNx      = (nX-750)/2;
                    var nNy      = (nY-400)/2;
                    var cWinOpt  = "width=750,scrollbars=1,height=400,left="+nNx+",top="+nNy;
                  }else{
                    var nNx      = (nX-450)/2;
                    var nNy      = (nY-250)/2;
                    var cWinOpt  = "width=450,scrollbars=1,height=250,left="+nNx+",top="+nNy;
                  }

                  var cPathUrl = "frccofrm.php?gModo="+xSwitch+"&gFunction="+xLink+
                                               "&gComId="+document.forms['frgrm']['cComId'].value.toUpperCase()+
                                               "&gComCod="+document.forms['frgrm']['cComCod'].value.toUpperCase()+
                                               "&gCtoId="+document.forms['frgrm']['cCtoId'+xSecuencia].value.toUpperCase()+
                                               "&gSecuencia="+xSecuencia+
                                               "&gCaso=1";  //Envio Caso = 1; en el marco se utiliza para saber que script redireccionar
                  //alert(cPathUrl);
                  cWindow = window.open(cPathUrl,xLink,cWinOpt);
                  cWindow.focus();
                }
              }

            } else {
              alert(cMsj + "Verifique.");
            }
          break;
          case "cComCscC":
            if (document.forms['frgrm']['cCtoId'+xSecuencia].value.length > 0) {

              switch (document.forms['frgrm']['cPucDet'+xSecuencia].value) {
                case "D": // Cuenta de DO's
                  if (document.forms['frgrm']['cComCscC'+xSecuencia].id != (document.forms['frgrm']['cComIdC'  + xSecuencia].value + "~" + document.forms['frgrm']['cComCodC' + xSecuencia].value + "~" + document.forms['frgrm']['cComCscC' + xSecuencia].value + "~" + document.forms['frgrm']['cComSeqC' + xSecuencia].value)) {
                    document.forms['frgrm']['cCcoId' +xSecuencia].value = document.forms['frgrm']['cCcoId'].value;
                    document.forms['frgrm']['cSccId' +xSecuencia].value = document.forms['frgrm']['cSccId'].value;
                    document.forms['frgrm']['cSucId' +xSecuencia].value = document.forms['frgrm']['cSccId_SucId'].value;
                    document.forms['frgrm']['cDocId' +xSecuencia].value = document.forms['frgrm']['cSccId_DocId'].value;
                    document.forms['frgrm']['cDocSuf'+xSecuencia].value = document.forms['frgrm']['cSccId_DocSuf'].value;
                  }
                break;
                default:
                  document.forms['frgrm']['cCcoId' +xSecuencia].value = document.forms['frgrm']['cCcoId'].value;
                  document.forms['frgrm']['cSccId' +xSecuencia].value = document.forms['frgrm']['cSccId'].value;
                  document.forms['frgrm']['cSucId' +xSecuencia].value = document.forms['frgrm']['cSccId_SucId'].value;
                  document.forms['frgrm']['cDocId' +xSecuencia].value = document.forms['frgrm']['cSccId_DocId'].value;
                  document.forms['frgrm']['cDocSuf'+xSecuencia].value = document.forms['frgrm']['cSccId_DocSuf'].value;
                break;
              }

              switch (document.forms['frgrm']['cPucDet'+xSecuencia].value) {
                case "N": // Cuenta no Detalla
                case "C": // Cuentas por Cobrar
                case "P": // Cuentas por Pagar
                case "D": // Cuenta de DO's
                  var nNx      = ((nX-500)/2);
                  var nNy      = (nY-200)/2;
                  var cWinOpt  = 'width=500,scrollbars=1,height=200,left='+nNx+',top='+nNy;
                  var cPathUrl = "frcpafrm.php?gTerTip="+document.forms.frgrm['cTerTip'+xSecuencia].value.toUpperCase()+
                                             "&gTerId="+document.forms.frgrm['cTerId'+xSecuencia].value.toUpperCase()+
                                             "&gTerTipB="+document.forms.frgrm['cTerTipB'+xSecuencia].value.toUpperCase()+
                                             "&gTerIdB="+document.forms.frgrm['cTerIdB'+xSecuencia].value.toUpperCase()+
                                             "&gPucId="+document.forms.frgrm['cPucId'+xSecuencia].value.toUpperCase()+
                                             "&gPucDet="+document.forms.frgrm['cPucDet'+xSecuencia].value.toUpperCase()+
                                             "&gPucTipEj="+document.forms.frgrm['cPucTipEj'+xSecuencia].value.toUpperCase()+
                                             "&gComNit="+document.forms.frgrm['cComNit'+xSecuencia].value.toUpperCase()+
                                             "&gSecuencia="+xSecuencia;
                  cWindow = window.open(cPathUrl,xLink,cWinOpt);
                  cWindow.focus();
                break;
              }

            } else {
              alert("No Hay Concepto Seleccionado en la Secuencia "+xSecuencia+", Verifique.");
            }
          break;
        }
      }

      function f_Verificar_Do() {
        var nEncontro = 0;
        for(var i=1; i<=document.forms['frgrm']['nSecuencia_DO'].value; i++){
          if(document.forms['frgrm']['cSucId_DO' +i].value != '' &&
             document.forms['frgrm']['cDocId_DO' +i].value != '' &&
             document.forms['frgrm']['cDocSuf_DO'+i].value != '') {
            nEncontro = 1;
          }
        }

        if(nEncontro == 1) {
          return true;
        } else {
          return false;
        }
      }

      function f_Activa_Csc() { // Activa el campo Factura dependiendo de la parametrizacion del comprobante
        switch (document.forms['frgrm']['cComTco'].value) {
          case "AUTOMATICO":
            document.forms['frgrm']['cComCsc'].readOnly = true;
          break;
          default:
            document.forms['frgrm']['cComCsc'].readOnly = false;
          break;
        }
      }

      function f_Enter(xTabla,e,xName) {
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
          switch (xTabla) {
           case "Grid_Comprobante":
            if (xName == "nComVlr"+parseFloat(document.forms['frgrm']['nSecuencia'].value) || xName == "nComVlrNF"+parseFloat(document.forms['frgrm']['nSecuencia'].value)) {
              f_Add_New_Row_Comprobante();
            }
           break;
           case "Grid_Dos":
            if (xName == "nVlrPro_DO"+parseFloat(document.forms['frgrm']['nSecuencia_DO'].value)) {
              f_Add_New_Row_Dos();
              f_Borrar_Tabla('Grid_Comprobante');
            }
           break;
           case "Grid_Conceptos":
            if (xName == "nVlr_CCO"+parseFloat(document.forms['frgrm']['nSecuencia_CCO'].value)) {
              f_Add_New_Row_Conceptos();
              f_Borrar_Tabla('Grid_Comprobante');
            }
           break;
          }
        }
      }

      function f_Delete_Row(xTabla,xNumRow,xSecuencia) {
        var cGrid = document.getElementById(xTabla);
        var nLastRow = cGrid.rows.length;
        if (nLastRow > 1 && xNumRow == "X"){
          if (confirm("Realmente Desea Eliminar la Secuencia?")){
            switch (xTabla) {
             case "Grid_Comprobante":
               if(xSecuencia < nLastRow){
                 var j=0;
                 for(var i=xSecuencia;i<nLastRow;i++){
                  j = parseFloat(i)+1;
                    document.forms['frgrm']['cComSeq'  + i].value = f_Str_Pad(i,3,"0","STR_PAD_LEFT"); // Secuencia
                    document.forms['frgrm']['cCtoId'   + i].value = document.forms['frgrm']['cCtoId'   + j].value;
                    document.forms['frgrm']['cCtoId'   + i].id    = document.forms['frgrm']['cCtoId'   + j].value;
                    document.forms['frgrm']['cCtoDes'  + i].value = document.forms['frgrm']['cCtoDes'  + j].value;
                    document.forms['frgrm']['cInvLin'  + i].value = document.forms['frgrm']['cInvLin'  + j].value;
                    document.forms['frgrm']['cInvGru'  + i].value = document.forms['frgrm']['cInvGru'  + j].value;
                    document.forms['frgrm']['cInvPro'  + i].value = document.forms['frgrm']['cInvPro'  + j].value;
                    document.forms['frgrm']['nInvCan'  + i].value = document.forms['frgrm']['nInvCan'  + j].value;
                    document.forms['frgrm']['nInvCos'  + i].value = document.forms['frgrm']['nInvCos'  + j].value;
                    document.forms['frgrm']['cInvBod'  + i].value = document.forms['frgrm']['cInvBod'  + j].value;
                    document.forms['frgrm']['cInvUbi'  + i].value = document.forms['frgrm']['cInvUbi'  + j].value;
                    document.forms['frgrm']['cComObs'  + i].value = document.forms['frgrm']['cComObs'  + j].value;
                    document.forms['frgrm']['cComIdC'  + i].value = document.forms['frgrm']['cComIdC'  + j].value;
                    document.forms['frgrm']['cComCodC' + i].value = document.forms['frgrm']['cComCodC' + j].value;
                    document.forms['frgrm']['cComCscC' + i].value = document.forms['frgrm']['cComCscC' + j].value;
                    document.forms['frgrm']['cComSeqC' + i].value = document.forms['frgrm']['cComSeqC' + j].value;
                    document.forms['frgrm']['cCcoId'   + i].value = document.forms['frgrm']['cCcoId'   + j].value;
                    document.forms['frgrm']['cSccId'   + i].value = document.forms['frgrm']['cSccId'   + j].value;
                    document.forms['frgrm']['nComBRet' + i].value = document.forms['frgrm']['nComBRet' + j].value;
                    document.forms['frgrm']['nComBIva' + i].value = document.forms['frgrm']['nComBIva' + j].value;
                    document.forms['frgrm']['nComIva'  + i].value = document.forms['frgrm']['nComIva'  + j].value;
                    document.forms['frgrm']['nComVlr'  + i].value = document.forms['frgrm']['nComVlr'  + j].value;
                    document.forms['frgrm']['nComVlrNF'+ i].value = document.forms['frgrm']['nComVlrNF'+ j].value;
                    document.forms['frgrm']['cComMov'  + i].value = document.forms['frgrm']['cComMov'  + j].value;
                    document.forms['frgrm']['cComNit'  + i].value = document.forms['frgrm']['cComNit'  + j].value;
                    document.forms['frgrm']['cTerTip'  + i].value = document.forms['frgrm']['cTerTip'  + j].value;
                    document.forms['frgrm']['cTerId'   + i].value = document.forms['frgrm']['cTerId'   + j].value;
                    document.forms['frgrm']['cTerTipB' + i].value = document.forms['frgrm']['cTerTipB' + j].value;
                    document.forms['frgrm']['cTerIdB'  + i].value = document.forms['frgrm']['cTerIdB'  + j].value;
                    document.forms['frgrm']['cPucId'   + i].value = document.forms['frgrm']['cPucId'   + j].value;
                    document.forms['frgrm']['cPucDet'  + i].value = document.forms['frgrm']['cPucDet'  + j].value;
                    document.forms['frgrm']['cPucTer'  + i].value = document.forms['frgrm']['cPucTer'  + j].value;
                    document.forms['frgrm']['nPucBRet' + i].value = document.forms['frgrm']['nPucBRet' + j].value;
                    document.forms['frgrm']['nPucRet'  + i].value = document.forms['frgrm']['nPucRet'  + j].value;
                    document.forms['frgrm']['cPucNat'  + i].value = document.forms['frgrm']['cPucNat'  + j].value;
                    document.forms['frgrm']['cPucInv'  + i].value = document.forms['frgrm']['cPucInv'  + j].value;
                    document.forms['frgrm']['cPucCco'  + i].value = document.forms['frgrm']['cPucCco'  + j].value;
                    document.forms['frgrm']['cPucDoSc' + i].value = document.forms['frgrm']['cPucDoSc' + j].value;
                    document.forms['frgrm']['cPucTipEj'+ i].value = document.forms['frgrm']['cPucTipEj'+ j].value;
                    document.forms['frgrm']['cComVlr1' + i].value = document.forms['frgrm']['cComVlr1' + j].value;
                    document.forms['frgrm']['cComVlr2' + i].value = document.forms['frgrm']['cComVlr2' + j].value;
                    document.forms['frgrm']['cComFac'  + i].value = document.forms['frgrm']['cComFac'  + j].value;
                    document.forms['frgrm']['cSucId'   + i].value = document.forms['frgrm']['cSucId'   + j].value;
                    document.forms['frgrm']['cDocId'   + i].value = document.forms['frgrm']['cDocId'   + j].value;
                    document.forms['frgrm']['cDocSuf'  + i].value = document.forms['frgrm']['cDocSuf'  + j].value;

                    //Campos de intermediacion de pago
                    document.forms['frgrm']['cComIdCB'    + i].value = document.forms['frgrm']['cComIdCB'    + j].value;
                    document.forms['frgrm']['cComCodCB'   + i].value = document.forms['frgrm']['cComCodCB'   + j].value;
                    document.forms['frgrm']['cComCscCB'   + i].value = document.forms['frgrm']['cComCscCB'   + j].value;
                    document.forms['frgrm']['cComSeqCB'   + i].value = document.forms['frgrm']['cComSeqCB'   + j].value;
                    document.forms['frgrm']['cCtoIdInp'   + i].value = document.forms['frgrm']['cCtoIdInp'   + j].value;
                    document.forms['frgrm']['cPucIdInp'   + i].value = document.forms['frgrm']['cPucIdInp'   + j].value;
                    document.forms['frgrm']['cPucDetInp'  + i].value = document.forms['frgrm']['cPucDetInp'  + j].value;
                    document.forms['frgrm']['cPucTerInp'  + i].value = document.forms['frgrm']['cPucTerInp'  + j].value;
                    document.forms['frgrm']['nPucBRetInp' + i].value = document.forms['frgrm']['nPucBRetInp' + j].value;
                    document.forms['frgrm']['nPucRetInp'  + i].value = document.forms['frgrm']['nPucRetInp'  + j].value;
                    document.forms['frgrm']['cPucNatInp'  + i].value = document.forms['frgrm']['cPucNatInp'  + j].value;
                    document.forms['frgrm']['cPucInvInp'  + i].value = document.forms['frgrm']['cPucInvInp'  + j].value;
                    document.forms['frgrm']['cPucCcoInp'  + i].value = document.forms['frgrm']['cPucCcoInp'  + j].value;
                    document.forms['frgrm']['cPucDoScInp' + i].value = document.forms['frgrm']['cPucDoScInp' + j].value;
                    document.forms['frgrm']['cPucTipEjInp'+ i].value = document.forms['frgrm']['cPucTipEjInp'+ j].value;
                    document.forms['frgrm']['cComVlr1Inp' + i].value = document.forms['frgrm']['cComVlr1Inp' + j].value;
                    document.forms['frgrm']['cComVlr2Inp' + i].value = document.forms['frgrm']['cComVlr2Inp' + j].value;

                    document.forms['frgrm']['nComVlr'  + i].disabled = document.forms['frgrm']['nComVlr'  + j].disabled;
                    document.forms['frgrm']['nComVlrNF'+ i].disabled = document.forms['frgrm']['nComVlrNF'+ j].disabled;
                    document.forms['frgrm']['nComBRet' + i].disabled = document.forms['frgrm']['nComBRet' + j].disabled;
                    document.forms['frgrm']['nComBIva' + i].disabled = document.forms['frgrm']['nComBIva' + j].disabled;
                    document.forms['frgrm']['nComIva'  + i].disabled = document.forms['frgrm']['nComIva'  + j].disabled;

                    document.getElementById('cComSeq'  + i).style.color = document.getElementById('cComSeq'  + j).style.color;
                    document.getElementById('cCtoDes'  + i).style.color = document.getElementById('cCtoDes'  + j).style.color;
                 }
               }
               cGrid.deleteRow(nLastRow - 1);
               document.forms['frgrm']['nSecuencia'].value = nLastRow - 1;

               f_Cuadre_Debitos_Creditos();
             break;
             case "Grid_Dos":
               var cSucId  = document.forms['frgrm']['cSucId_DO' +xSecuencia].value;
               var cDocId  = document.forms['frgrm']['cDocId_DO' +xSecuencia].value;
               var cDocSuf = document.forms['frgrm']['cDocSuf_DO'+xSecuencia].value;
               if(xSecuencia < nLastRow){
                 var j=0;
                 for(var i=xSecuencia;i<nLastRow;i++){
                   j = parseFloat(i)+1;
                   document.forms['frgrm']['cSucId_DO'   + i].value = document.forms['frgrm']['cSucId_DO'   + j].value; // Sucursal
                   document.forms['frgrm']['cDocId_DO'   + i].value = document.forms['frgrm']['cDocId_DO'   + j].value; // DO
                   document.forms['frgrm']['cDocSuf_DO'  + i].value = document.forms['frgrm']['cDocSuf_DO'  + j].value; // Sufijo

                   document.forms['frgrm']['cSucId_DO'   + i].id = document.forms['frgrm']['cSucId_DO'   + j].value; // Sucursal
                   document.forms['frgrm']['cDocId_DO'   + i].id = document.forms['frgrm']['cDocId_DO'   + j].value; // DO
                   document.forms['frgrm']['cDocSuf_DO'  + i].id = document.forms['frgrm']['cDocSuf_DO'  + j].value; // Sufijo

                   document.forms['frgrm']['cTerId_DO'   + i].value = document.forms['frgrm']['cTerId_DO'   + j].value; // Id del Tercero
                   document.forms['frgrm']['cTerNom_DO'  + i].value = document.forms['frgrm']['cTerNom_DO'  + j].value; // Nombre del Tercero
                   document.forms['frgrm']['cTerTip_DO'  + i].value = document.forms['frgrm']['cTerTip_DO'  + j].value; // Hidden (Tipo de Tercero)
                   document.forms['frgrm']['cTerTipB_DO' + i].value = document.forms['frgrm']['cTerTipB_DO' + j].value; // Hidden (Tipo de Tercero Dos)
                   document.forms['frgrm']['cTerIdB_DO'  + i].value = document.forms['frgrm']['cTerIdB_DO'  + j].value; // Hidden (Id del Tercero Dos)
                   document.forms['frgrm']['cDocFec_DO'  + i].value = document.forms['frgrm']['cDocFec_DO'  + j].value; // Fecha
                   document.forms['frgrm']['cCcoId_DO'   + i].value = document.forms['frgrm']['cCcoId_DO'   + j].value; // Centro de Costos
                   document.forms['frgrm']['nVlrPro_DO'  + i].value = document.forms['frgrm']['nVlrPro_DO'  + j].value; // Valor Prorrateo
                 }
               }
               cGrid.deleteRow(nLastRow - 1);
               document.forms['frgrm']['nSecuencia_DO'].value = nLastRow - 1;

               //Se borran los conceptos donde se encuentra el DO
               f_Borrar_Conceptos_Do(cSucId,cDocId,cDocSuf);
               f_Borrar_Tabla('Grid_Comprobante');
               f_Asignar_Base_Conceptos();
             break;
             case "Grid_Conceptos":
               if(xSecuencia < nLastRow){
                 var j=0;
                 for(var i=xSecuencia;i<nLastRow;i++){
                   j = parseFloat(i)+1;
                   document.forms['frgrm']['cCcoId_CCO'   + i].value = document.forms['frgrm']['cCcoId_CCO'   + j].value; // Id del Concepto
                   document.forms['frgrm']['cCcoId_CCO'   + i].id    = document.forms['frgrm']['cCcoId_CCO'   + j].value; // Id del Concepto

                   document.forms['frgrm']['cCcoDes_CCO'  + i].value = document.forms['frgrm']['cCcoDes_CCO'  + j].value; // Descripcion del Concepto
                   document.forms['frgrm']['cSucId_CCO'   + i].value = document.forms['frgrm']['cSucId_CCO'   + j].value; // Sucursal
                   document.forms['frgrm']['cDocId_CCO'   + i].value = document.forms['frgrm']['cDocId_CCO'   + j].value; // DO
                   document.forms['frgrm']['cDocSuf_CCO'  + i].value = document.forms['frgrm']['cDocSuf_CCO'  + j].value; // Sufijo
                   document.forms['frgrm']['nVlrBaiu_CCO' + i].value = document.forms['frgrm']['nVlrBaiu_CCO' + j].value; // Valor Base AIU
                   document.forms['frgrm']['nVlrBase_CCO' + i].value = document.forms['frgrm']['nVlrBase_CCO' + j].value; // Valor Base
                   document.forms['frgrm']['nVlrIva_CCO'  + i].value = document.forms['frgrm']['nVlrIva_CCO'  + j].value; // Valor Iva
                   document.forms['frgrm']['nVlr_CCO'     + i].value = document.forms['frgrm']['nVlr_CCO'     + j].value; // Valor
                   document.forms['frgrm']['cCtoVrl02_CCO'+ i].value = document.forms['frgrm']['cCtoVrl02_CCO'+ j].value; // Calculo Automatico del Iva
                 }
               }
               cGrid.deleteRow(nLastRow - 1);
               document.forms['frgrm']['nSecuencia_CCO'].value = nLastRow - 1;

               f_Borrar_Tabla('Grid_Comprobante');
               f_Asignar_Base_Conceptos();
             break;
            }
            f_Calcular_Total_Iva_Total_base();
          }
        } else {
          alert("No se Pueden Eliminar Todas las Secuencias, Verifique.");
        }
      }

      function f_Insert_Row(xTabla,xSecuencia) {
        switch (xTabla) {
          case "Grid_Comprobante":
          break;
          case "Grid_Dos":
          break;
          case "Grid_Conceptos":

            if(document.forms['frgrm']['cCcoId_CCO'  + document.forms['frgrm']['nSecuencia_CCO'].value].value != '') {
             f_Add_New_Row_Conceptos();
            }

            var j=0;
            for(var i=document.forms['frgrm']['nSecuencia_CCO'].value;i>xSecuencia;i--){
              j = parseFloat(i)-1;
              document.forms['frgrm']['cCcoId_CCO'   + i].id    = document.forms['frgrm']['cCcoId_CCO'   + j].id; // Id del Concepto
              document.forms['frgrm']['cCcoId_CCO'   + i].value = document.forms['frgrm']['cCcoId_CCO'   + j].value; // Id del Concepto
              document.forms['frgrm']['cSucId_CCO'   + i].value = document.forms['frgrm']['cSucId_CCO'   + j].value; // DO
              document.forms['frgrm']['cDocId_CCO'   + i].value = document.forms['frgrm']['cDocId_CCO'   + j].value; // DO
              document.forms['frgrm']['cDocSuf_CCO'  + i].value = document.forms['frgrm']['cDocSuf_CCO'  + j].value; // DO
              document.forms['frgrm']['nVlrBaiu_CCO' + i].value = document.forms['frgrm']['nVlrBaiu_CCO' + j].value; // Valor Base AIU
              document.forms['frgrm']['nVlrBase_CCO' + i].value = document.forms['frgrm']['nVlrBase_CCO' + j].value; // Valor Base
              document.forms['frgrm']['nVlrIva_CCO'  + i].value = document.forms['frgrm']['nVlrIva_CCO'  + j].value; // Valor Iva
              document.forms['frgrm']['nVlr_CCO'     + i].value = document.forms['frgrm']['nVlr_CCO'     + j].value; // Valor
              document.forms['frgrm']['cCtoVrl02_CCO'+ i].value = document.forms['frgrm']['cCtoVrl02_CCO'+ j].value; // Calculo Automatico del Iva
            }

            document.forms['frgrm']['cCcoId_CCO'   + xSecuencia].id    = "";
            document.forms['frgrm']['cCcoId_CCO'   + xSecuencia].value = "";
            document.forms['frgrm']['cCcoDes_CCO'  + xSecuencia].value = "";
            document.forms['frgrm']['cSucId_CCO'   + xSecuencia].value = "";
            document.forms['frgrm']['cDocId_CCO'   + xSecuencia].value = "";
            document.forms['frgrm']['cDocSuf_CCO'  + xSecuencia].value = "";
            document.forms['frgrm']['nVlrBaiu_CCO' + xSecuencia].value = "";
            document.forms['frgrm']['nVlrBase_CCO' + xSecuencia].value = "";
            document.forms['frgrm']['nVlrIva_CCO'  + xSecuencia].value = "";
            document.forms['frgrm']['nVlr_CCO'     + xSecuencia].value = "";
            document.forms['frgrm']['cCtoVrl02_CCO'+ xSecuencia].value = "";
          break;
        }
      }

      function f_Valores_Automaticos(xSecuencia) {
        if (document.forms['frgrm']['cPucInv'+xSecuencia].value != "I") { // Pregunto si la cuenta es diferente a inventarios
          var nSumValor = 0;
          if (document.forms['frgrm']['cComVlr1'+xSecuencia].value == "SI") { // Para evitar el error en la sumatoria
            if (document.forms['frgrm']['nComVlr01'].value == "") { // Por si el usuario no digito nada en valor
              document.forms['frgrm']['nComVlr01'].value = 0;
            }
            nSumValor += parseFloat(document.forms['frgrm']['nComVlr01'].value);
          }
          if (document.forms['frgrm']['cComVlr2'+xSecuencia].value == "SI") { // Para evitar el error en la sumatoria
            if (document.forms['frgrm']['nComVlr02'].value == "") { // Por si el usuario no digito nada en valor
              document.forms['frgrm']['nComVlr02'].value = 0;
            }
            nSumValor += parseFloat(document.forms['frgrm']['nComVlr02'].value);
          }
          // Sigo Sumando

          //Las Retenciones y el IVA se calculan automaticamente si la ejecucion de la cuenta es LOCAL o AMBAS
          if  (document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "L" || document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "") {
            if (document.forms['frgrm']['cComVlr1'+xSecuencia].value == "SI" || document.forms['frgrm']['cComVlr2'+xSecuencia].value == "SI") {
              if (document.forms['frgrm']['nPucRet'+xSecuencia].value > 0) { // Es una retencion
                document.forms['frgrm']['nComBIva'+xSecuencia].disabled = true; document.forms['frgrm']['nComBIva'+xSecuencia].value = "";
                document.forms['frgrm']['nComIva'+xSecuencia].disabled  = true; document.forms['frgrm']['nComIva'+xSecuencia].value  = "";
                document.forms['frgrm']['nComBRet'+xSecuencia].value = parseFloat(nSumValor);
                document.forms['frgrm']['nComBRet'+xSecuencia].disabled = false;
                f_Calcula_Retencion(xSecuencia);
              } else { // Es un IVA.
                document.forms['frgrm']['nComBRet'+xSecuencia].disabled = true; document.forms['frgrm']['nComBRet'+xSecuencia].value = "";
                document.forms['frgrm']['nComVlr'+xSecuencia].value = parseFloat(nSumValor);
                //Si es el tipo de ejecucion es AMBAS se asigna tambien el valor aL valor NIIF
                document.forms['frgrm']['nComVlrNF'+xSecuencia].value = (document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "") ? parseFloat(nSumValor) : "";
                // Nuevos valores para base de iva y valor del iva.
                document.forms['frgrm']['nComBIva'+xSecuencia].disabled = false;
                document.forms['frgrm']['nComIva'+xSecuencia].disabled  = false;
                f_Cacula_BaseIva_e_Iva("Base_mas_Iva",xSecuencia);
              }
            }
          } else if (document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "N") {
            //Para la ejecucion NIIF no aplican retenciones, ni IVA
            document.forms['frgrm']['nComBRet'+xSecuencia].disabled = true; document.forms['frgrm']['nComBRet'+xSecuencia].value = "";
            document.forms['frgrm']['nComBIva'+xSecuencia].disabled = true; document.forms['frgrm']['nComBIva'+xSecuencia].value = "";
            document.forms['frgrm']['nComIva' +xSecuencia].disabled = true; document.forms['frgrm']['nComIva' +xSecuencia].value = "";
            document.forms['frgrm']['nComVlr'  +xSecuencia].value = "";
            if (document.forms['frgrm']['cComVlr1'+xSecuencia].value == "SI" || document.forms['frgrm']['cComVlr2'+xSecuencia].value == "SI") {
              document.forms['frgrm']['nComVlrNF'+xSecuencia].value = parseFloat(nSumValor);
              if (document.forms['frgrm']['nPucRet'+xSecuencia].value > 0) { // Es una retencion
                //No Hace Nada
              } else { // Es un IVA, se debe digitar base Iva, no se calcula Iva
                document.forms['frgrm']['nComBIva'+xSecuencia].disabled = false;
                document.forms['frgrm']['nComBIva'+xSecuencia].value    = document.forms['frgrm']['nComVlrNF'+xSecuencia].value;
              }
            }
          }
        }
      }

      function f_Calcular_Total_Iva_Total_base(){
        //** CALCULAMOS LA BASE EN BASE AL CONCEPTO CARGADO AUTOMATICAMENTE **
        if (('<?php echo $kDf[3] ?>' == 'DEGRUMALCO' || '<?php echo $kDf[3] ?>' == 'TEGRUMALCO' || '<?php echo $kDf[3] ?>' == 'GRUMALCO') ||
            ('<?php echo $kDf[3] ?>' == 'DEDSVSASXX' || '<?php echo $kDf[3] ?>' == 'TEDSVSASXX' || '<?php echo $kDf[3] ?>' == 'DSVSASXX')
          )  {

          var tabla_conceptos = document.getElementById('Grid_Conceptos');
          var filas = tabla_conceptos.getElementsByTagName('tr');
          var nTotalBase  = 0;
          var nTotalIva   = 0;
          for (var i = 1; i <= filas.length; i++) {
            if (!isNaN(parseFloat(document.forms['frgrm']['nVlrBase_CCO'+i].value))){
              nTotalBase += parseFloat(document.forms['frgrm']['nVlrBase_CCO'+i].value);
            }
            if (!isNaN(parseFloat(document.forms['frgrm']['nVlrIva_CCO'+i].value))){
              nTotalIva  += parseFloat(document.forms['frgrm']['nVlrIva_CCO'+i].value);
            }
          }
          if (!isNaN(nTotalBase)){
            document.getElementsByName('nComVlr01')[0].value = nTotalBase;
          }
          if (!isNaN(nTotalIva)){
            document.getElementsByName('nComVlr02')[0].value = nTotalIva;
          }
        }else{
          /* No se hace nada para las demas bases de datos */
        }

        /***FIN DE CALCULO DE LA BASE SEGUN EN CONCEPTO CARGADO **/
      }

      function f_Calcula_Retencion(xSecuencia) {
        if  (document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "L" || document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "") {
          document.forms['frgrm']['nComBIva'+xSecuencia].disabled = true;
          document.forms['frgrm']['nComIva'+xSecuencia].disabled  = true;
          var nRetencion = 0;
          if (document.forms['frgrm']['nPucRet'+xSecuencia].value > 0) {
            var nRound = (document.forms['frgrm']['nComBRet'+xSecuencia].value.indexOf(".") > 0) ? 2 : 0;

            nRetencion = parseFloat(document.forms['frgrm']['nPucRet'+xSecuencia].value/100);
            document.forms['frgrm']['nComVlr'  +xSecuencia].value = f_RoundValor((parseFloat(document.forms['frgrm']['nComBRet'+xSecuencia].value) * parseFloat(nRetencion)),nRound);
            //Si es el tipo de ejecucion es AMBAS se asigna tambien el valor aL valor NIIF
            document.forms['frgrm']['nComVlrNF'+xSecuencia].value = (document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "") ? document.forms['frgrm']['nComVlr'+xSecuencia].value : "";
          }
        }
      }

      function f_Valida_Base_Retencion(xSecuencia) {
        //Las Retenciones y el IVA se calculan si la ejecucion de la cuenta es LOCAL o AMBAS
        if  (document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "L" || document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "") {
          if (document.forms['frgrm']['cComVlr1'+xSecuencia].value == "SI" || document.forms['frgrm']['cComVlr2'+xSecuencia].value == "SI") {
            if (document.forms['frgrm']['nPucRet'+xSecuencia].value > 0) { // Es una retencion
              if (f_RoundValor(parseFloat(document.forms['frgrm']['nComBRet'+xSecuencia].value)) < f_RoundValor(parseFloat(document.forms['frgrm']['nPucBRet'+xSecuencia].value))) {
                alert("La Base de Retencion ["+f_RoundValor(parseFloat(document.forms['frgrm']['nComBRet'+xSecuencia].value))+"] es Menor a la Base de Retencion ["+f_RoundValor(parseFloat(document.forms['frgrm']['nPucBRet'+xSecuencia].value))+"] Parametrizada en la Cuenta PUC ["+document.forms['frgrm']['cPucId'+xSecuencia].value+"].");
              }
            } else { // Es un IVA.
              if (f_RoundValor(parseFloat(document.forms['frgrm']['nComBIva'+xSecuencia].value)) < f_RoundValor(parseFloat(document.forms['frgrm']['nPucBRet'+xSecuencia].value))) {
                alert("La Base de Retencion ["+f_RoundValor(parseFloat(document.forms['frgrm']['nComBIva'+xSecuencia].value))+"] es Menor a la Base de Retencion ["+f_RoundValor(parseFloat(document.forms['frgrm']['nPucBRet'+xSecuencia].value))+"] Parametrizada en la Cuenta PUC ["+document.forms['frgrm']['cPucId'+xSecuencia].value+"].");
              }
            }
          }
        }
      }

      function f_Cacula_BaseIva_e_Iva(xTipo,xSecuencia) {
        if  (document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "L" || document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "") {
          document.forms['frgrm']['nComBRet'+xSecuencia].disabled = true;
          switch (xTipo) {
            case "BaseIva":
              var nRound = (document.forms['frgrm']['nComBIva'+xSecuencia].value.indexOf(".") > 0) ? 2 : 0;

              if ("<?php echo $vSysStr['system_financiero_calcular_iva_segun_concepto'] == 'SI' ?>") {
                if (document.forms['frgrm']['cComVlr1'+xSecuencia].value == "SI" && document.forms['frgrm']['cComVlr2'+xSecuencia].value == "SI") {
                  document.forms['frgrm']['nComIva'  +xSecuencia].value = f_RoundValor((parseFloat(document.forms['frgrm']['nComBIva'+xSecuencia].value*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100))),nRound);
                }else{
                  document.forms['frgrm']['nComIva'  +xSecuencia].value = 0;
                }
              }else{
                document.forms['frgrm']['nComIva'  +xSecuencia].value = f_RoundValor((parseFloat(document.forms['frgrm']['nComBIva'+xSecuencia].value*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100))),nRound);
              }

              document.forms['frgrm']['nComVlr'  +xSecuencia].value = f_RoundValor((parseFloat(document.forms['frgrm']['nComBIva'+xSecuencia].value) + parseFloat(document.forms['frgrm']['nComIva'+xSecuencia].value)),nRound);
              //Si es el tipo de ejecucion es AMBAS se asigna tambien el valor aL valor NIIF
              document.forms['frgrm']['nComVlrNF'+xSecuencia].value = (document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "") ? document.forms['frgrm']['nComVlr'+xSecuencia].value : "";
            break;
            case "VlrIva":
              var nRound = ((document.forms['frgrm']['nComBIva'+xSecuencia].value.indexOf(".") > 0) || (document.forms['frgrm']['nComIva'+xSecuencia].value.indexOf(".") > 0)) ? 2 : 0;

              document.forms['frgrm']['nComVlr'  +xSecuencia].value = f_RoundValor((parseFloat(document.forms['frgrm']['nComBIva'+xSecuencia].value) + parseFloat(document.forms['frgrm']['nComIva'+xSecuencia].value)),nRound);
              //Si es el tipo de ejecucion es AMBAS se asigna tambien el valor aL valor NIIF
              document.forms['frgrm']['nComVlrNF'+xSecuencia].value = (document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "") ? document.forms['frgrm']['nComVlr'+xSecuencia].value : "";
            break;
            case "Base_mas_Iva":
              var nRound = (document.forms['frgrm']['nComVlr01'].value.indexOf(".") > 0) ? 2 : 0;

              document.forms['frgrm']['nComBIva'+xSecuencia].value = document.forms['frgrm']['nComVlr01'].value;
              if ("<?php echo $vSysStr['system_financiero_calcular_iva_segun_concepto'] == 'SI' ?>") {
                if (document.forms['frgrm']['cComVlr1'+xSecuencia].value == "SI" && document.forms['frgrm']['cComVlr2'+xSecuencia].value == "SI") {
                  document.forms['frgrm']['nComIva'+xSecuencia].value  = f_RoundValor((parseFloat(document.forms['frgrm']['nComVlr01'].value*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100))),nRound);
                }else{
                  document.forms['frgrm']['nComIva'+xSecuencia].value  = 0;
                }
              }else{
                document.forms['frgrm']['nComIva'+xSecuencia].value  = f_RoundValor((parseFloat(document.forms['frgrm']['nComVlr01'].value*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100))),nRound);
              }

            break;
          }
        } else if (document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "N") {
          switch (xTipo) {
            case "BaseIva":
              document.forms['frgrm']['nComVlrNF'+xSecuencia].value = document.forms['frgrm']['nComBIva'+xSecuencia].value
            break;
            case "BaseNiif":
              if (document.forms['frgrm']['cComVlr1'+xSecuencia].value == "SI" || document.forms['frgrm']['cComVlr2'+xSecuencia].value == "SI") {
                if (document.forms['frgrm']['nPucRet'+xSecuencia].value > 0) { // Es una retencion
                  //No Hace Nada
                } else { // Es un IVA, se debe digitar base Iva, no se calcula Iva
                  document.forms['frgrm']['nComBIva'+xSecuencia].value    = document.forms['frgrm']['nComVlrNF'+xSecuencia].value;
                }
              }
            break;
          }
        }
      }

      function f_Cuadre_Debitos_Creditos() {
        document.forms['frgrm']['nDebitos'].value    = 0;
        document.forms['frgrm']['nCreditos'].value   = 0;
        document.forms['frgrm']['nDiferencia'].value = 0;

        /*** Se carga dinamicamente los valor de debito y credito de cabecera. ***/
        if (('<?php echo $kDf[3] ?>' == 'DEGRUMALCO' || '<?php echo $kDf[3] ?>' == 'TEGRUMALCO' || '<?php echo $kDf[3] ?>' == 'GRUMALCO') ||
            ('<?php echo $kDf[3] ?>' == 'DEDSVSASXX' || '<?php echo $kDf[3] ?>' == 'TEDSVSASXX' || '<?php echo $kDf[3] ?>' == 'DSVSASXX')
          )  {
          var nTotalBase  = 0;
          var nTotalIva   = 0;

          for (i=0;i<document.forms['frgrm']['nSecuencia'].value;i++) {
            if (!isNaN(parseFloat(document.forms['frgrm']['nComBIva'+(i+1)].value))){
              nTotalBase += parseFloat(document.forms['frgrm']['nComBIva'+(i+1)].value);
            }
            if (!isNaN(parseFloat(document.forms['frgrm']['nComIva'+(i+1)].value))){
              nTotalIva  += parseFloat(document.forms['frgrm']['nComIva'+(i+1)].value);
            }
          }

          if (!isNaN(nTotalBase)){
            document.forms['frgrm']['nComVlr01'].value = nTotalBase;
            // document.getElementsByName('nComVlr01')[0].value = nTotalBase;
          }
          if (!isNaN(nTotalIva)){
            document.forms['frgrm']['nComVlr02'].value = nTotalIva;
            // document.getElementsByName('nComVlr02')[0].value = nTotalIva;
          }
        }

        //Recorro la grilla para determinar el tipo de ejecucion del comprobante
        //Si hay tipos de ejecucion LOCAL o AMBAS deben sumarse para los Debitos y Creditos el nComVlr
        //Si solo hay tipos de ejecucion NIIF deben sumarse para los Debitos y Creditos el nComVlrNF
        var nCanEjeLoc = 0;
        for (i=0;i<document.forms['frgrm']['nSecuencia'].value;i++) {
          if (document.forms['frgrm']['cPucTipEj'+(i+1)].value == "L" || document.forms['frgrm']['cPucTipEj'+(i+1)].value == "") {
            nCanEjeLoc++;
          }
        }
        var cCamEje = (nCanEjeLoc > 0) ? "nComVlr" : "nComVlrNF";

        for (i=0;i<document.forms['frgrm']['nSecuencia'].value;i++) {
          if (document.forms['frgrm']['nComVlr'+(i+1)].value == "") { // Para evitar el error en la sumatoria
            document.forms['frgrm']['nComVlr'+(i+1)].value = 0;
          }
          if (document.forms['frgrm']['nComVlrNF'+(i+1)].value == "") { // Para evitar el error en la sumatoria
            document.forms['frgrm']['nComVlrNF'+(i+1)].value = 0;
          }
          switch(document.forms['frgrm']['cComMov'+(i+1)].value) {
            case "D":
              document.forms['frgrm']['nDebitos'].value  = f_RoundValor(parseFloat(document.forms['frgrm']['nDebitos'].value) + parseFloat(document.forms['frgrm'][cCamEje+(i+1)].value));
            break;
            case "C":
              document.forms['frgrm']['nCreditos'].value = f_RoundValor(parseFloat(document.forms['frgrm']['nCreditos'].value) + parseFloat(document.forms['frgrm'][cCamEje+(i+1)].value));
            break;
          }
        }
        document.forms['frgrm']['nDiferencia'].value = f_RoundValor(parseFloat(document.forms['frgrm']['nDebitos'].value) - parseFloat(document.forms['frgrm']['nCreditos'].value));
      }

      function f_Enabled_Combos() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        document.forms['frgrm']['cTerTip'].disabled=false;
        document.forms['frgrm']['cTerTipB'].disabled=false;
      }

      function f_Disabled_Combos() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        document.forms['frgrm']['cTerTip'].disabled=true;
        document.forms['frgrm']['cTerTipB'].disabled=true;
      }

      function f_Add_New_Row_Comprobante() {

        var cGrid      = document.getElementById("Grid_Comprobante");
        var nLastRow   = cGrid.rows.length;
        var nSecuencia = nLastRow+1;
        var cTableRow  = cGrid.insertRow(nLastRow);

        var cComSeq      = 'cComSeq'     + nSecuencia; // Secuencia
        var cCtoId       = 'cCtoId'      + nSecuencia; // Id del Concepto
        var cCtoDes      = 'cCtoDes'     + nSecuencia; // Descripcion del Concepto
        var cInvLin      = 'cInvLin'     + nSecuencia; // Hidden (Inventario - Linea)
        var cInvGru      = 'cInvGru'     + nSecuencia; // Hidden (Inventario - Grupo)
        var cInvPro      = 'cInvPro'     + nSecuencia; // Hidden (Inventario - Producto)
        var nInvCan      = 'nInvCan'     + nSecuencia; // Hidden (Inventario - Cantidad)
        var nInvCos      = 'nInvCos'     + nSecuencia; // Hidden (Inventario - Costo Unitario)
        var cInvBod      = 'cInvBod'     + nSecuencia; // Hidden (Inventario - Bodega)
        var cInvUbi      = 'cInvUbi'     + nSecuencia; // Hidden (Inventario - Ubicacion)
        var cComObs      = 'cComObs'     + nSecuencia; // Observacion del Comprobante
        var cComIdC      = 'cComIdC'     + nSecuencia; // Id Comprobante Cruce
        var cComCodC     = 'cComCodC'    + nSecuencia; // Codigo Comprobante Cruce
        var cComCscC     = 'cComCscC'    + nSecuencia; // Consecutivo Comprobante Cruce
        var cComSeqC     = 'cComSeqC'    + nSecuencia; // Secuencia Comprobante Cruce
        var cCcoId       = 'cCcoId'      + nSecuencia; // Centro de Costos
        var cSccId       = 'cSccId'      + nSecuencia; // Sub Centro de Costos
        var cComCtoC     = 'cComCtoC'    + nSecuencia; // Hidden (Concepto Comprobante Cruce)
        var nComBRet     = 'nComBRet'    + nSecuencia; // Base de Retencion
        var nComBIva     = 'nComBIva'    + nSecuencia; // Base de Iva
        var nComIva      = 'nComIva'     + nSecuencia; // Valor del Iva
        var nComVlr      = 'nComVlr'     + nSecuencia; // Valor del Comprobante
        var nComVlrNF    = 'nComVlrNF'   + nSecuencia; // Valor NIIF del Comprobante
        var cComMov      = 'cComMov'     + nSecuencia; // Movimiento Debito o Credito
        var oBtnDel      = 'oBtnDel'     + nSecuencia; // Boton de Borrar Row
        var cComNit      = 'cComNit'     + nSecuencia; // Hidden (Nit que va para SIIGO)
        var cTerTip      = 'cTerTip'     + nSecuencia; // Hidden (Tipo de Tercero)
        var cTerId       = 'cTerId'      + nSecuencia; // Hidden (Id del Tercero)
        var cTerTipB     = 'cTerTipB'    + nSecuencia; // Hidden (Tipo de Tercero Dos)
        var cTerIdB      = 'cTerIdB'     + nSecuencia; // Hidden (Id del Tercero Dos)
        var cPucId       = 'cPucId'      + nSecuencia; // Hidden (La Cuenta Contable)
        var cPucDet      = 'cPucDet'     + nSecuencia; // Hidden (Detalle de la Cuenta)
        var cPucTer      = 'cPucTer'     + nSecuencia; // Hidden (Cuenta de Terceros?)
        var nPucBRet     = 'nPucBRet'    + nSecuencia; // Hidden (Base de Retencion de la Cuenta)
        var nPucRet      = 'nPucRet'     + nSecuencia; // Hidden (Porcentaje de Retencion de la Cuenta)
        var cPucNat      = 'cPucNat'     + nSecuencia; // Hidden (Naturaleza de la Cuenta)
        var cPucInv      = 'cPucInv'     + nSecuencia; // Hidden (Cuenta de Inventarios?)
        var cPucCco      = 'cPucCco'     + nSecuencia; // Hidden (Para esta Cuenta Aplica Centro de Costo?)
        var cPucDoSc     = 'cPucDoSc'    + nSecuencia; // Hidden (Aplica DO para Subcentro de Costo?)
        var cPucTipEj    = 'cPucTipEj'   + nSecuencia; // Hidden (Tipo de Ejecucion(L-Local,N-Niif,vacio-Ambas))
        var cComVlr1     = 'cComVlr1'    + nSecuencia; // Hidden (Valor Uno)
        var cComVlr2     = 'cComVlr2'    + nSecuencia; // Hidden (Valor Dos)
        var cComFac      = 'cComFac'     + nSecuencia; // Hidden (Valor SI Indica si es un Concepto Papa)
        var cSucId       = 'cSucId'      + nSecuencia; // Hidden (Sucursal)
        var cDocId       = 'cDocId'      + nSecuencia; // Hidden (Do)
        var cDocSuf      = 'cDocSuf'     + nSecuencia; // Hidden (Sufijo)
        //Campos para Intemediacion de Pago
        //Estos guardan la informacion del conepto papa
        //Al momento de guardar la causacion estos son los valores se utilizaran para hacer las validaciones de la secuencia
        var cComIdCB     = 'cComIdCB'    + nSecuencia; // Hidden (Id Comprobante Cruce Dos Intermediacion de Pago)
        var cComCodCB    = 'cComCodCB'   + nSecuencia; // Hidden (Codigo Comprobante Cruce Dos Intermediacion de Pago)
        var cComCscCB    = 'cComCscCB'   + nSecuencia; // Hidden (Consecutivo Comprobante Cruce Dos Intermediacion de Pago)
        var cComSeqCB    = 'cComSeqCB'   + nSecuencia; // Hidden (Secuencia Comprobante Cruce Dos Intermediacion de Pago)
        var cCtoIdInp    = 'cCtoIdInp'   + nSecuencia; // Hidden (Cuenta de Retencion Intermediacion de Pago)
        var cPucIdInp    = 'cPucIdInp'   + nSecuencia; // Hidden (Concepto de Retencion Intermediacion de Pago)
        var cPucDetInp   = 'cPucDetInp'  + nSecuencia; // Hidden (Detalle de la Cuenta Concepto Papa Intermediacion de Pago)
        var cPucTerInp   = 'cPucTerInp'  + nSecuencia; // Hidden (Cuenta de Terceros? Concepto Papa Intermediacion de Pago)
        var nPucBRetInp  = 'nPucBRetInp' + nSecuencia; // Hidden (Base de Retencion de la Cuenta Concepto Papa Intermediacion de Pago)
        var nPucRetInp   = 'nPucRetInp'  + nSecuencia; // Hidden (Porcentaje de Retencion de la Cuenta Concepto Papa Intermediacion de Pago)
        var cPucNatInp   = 'cPucNatInp'  + nSecuencia; // Hidden (Naturaleza de la Cuenta Concepto Papa Intermediacion de Pago)
        var cPucInvInp   = 'cPucInvInp'  + nSecuencia; // Hidden (Cuenta de Inventarios? Concepto Papa Intermediacion de Pago)
        var cPucCcoInp   = 'cPucCcoInp'  + nSecuencia; // Hidden (Para esta Cuenta Aplica Centro de Costo? Concepto Papa Intermediacion de Pago)
        var cPucDoScInp  = 'cPucDoScInp' + nSecuencia; // Hidden (Aplica DO para Subcentro de Costo? Concepto Papa Intermediacion de Pago)
        var cPucTipEjInp = 'cPucTipEjInp'+ nSecuencia; // Hidden (Tipo de Ejecucion(L-Local,N-Niif,vacio-Ambas) Concepto Papa Intermediacion de Pago)
        var cComVlr1Inp  = 'cComVlr1Inp' + nSecuencia; // Hidden (Valor Uno Concepto Papa Intermediacion de Pago)
        var cComVlr2Inp  = 'cComVlr2Inp' + nSecuencia; // Hidden (Valor Dos Concepto Papa Intermediacion de Pago)

        var TD_xAll = cTableRow.insertCell(0);
        TD_xAll.innerHTML = "<input type = 'text'   Class = 'letra' style = 'width:040;text-align:center' name = "+cComSeq+" id = "+cComSeq+"  value = "+f_Str_Pad(nSecuencia,3,"0","STR_PAD_LEFT")+"  readonly>"+
                            "<input type = 'text'   Class = 'letra' style = 'width:080;text-align:center' name = "+cCtoId+"  id = '' maxlength='10' "+
                              "onBlur = 'javascript:this.value=this.value.toUpperCase();f_Links(\"cCtoId\",\"VALID\",\""+nSecuencia+"\");f_Cuadre_Debitos_Creditos()'>"+
                            "<input type = 'text'   Class = 'letra' style = 'width:100' name = "+cCtoDes+" id = "+cCtoDes+" readonly>"+
                            "<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+cInvLin+" readonly>"+
                            "<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+cInvGru+" readonly>"+
                            "<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+cInvPro+" readonly>"+
                            "<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+nInvCan+" readonly>"+
                            "<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+nInvCos+" readonly>"+
                            "<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+cInvBod+" readonly>"+
                            "<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+cInvUbi+" readonly>"+
                            "<input type = 'text'   Class = 'letra' style = 'width:100' name = "+cComObs+" maxlength= '<?php echo ($vSysStr['financiero_longitud_observaciones_grilla'] > 255 ) ? 255 : (($vSysStr['financiero_longitud_observaciones_grilla'] < 1) ? 1 : $vSysStr['financiero_longitud_observaciones_grilla']);?>' "+
                              "onBlur = 'javascript:this.value=this.value.toUpperCase()'>"+
                            "<input type = 'text'   Class = 'letra' style = 'width:020;color:#FF0000;font-weight:bold' name = "+cComIdC+" readonly>"+
                            "<input type = 'text'   Class = 'letra' style = 'width:040;color:#FF0000;font-weight:bold;text-align:center' name = "+cComCodC+" readonly>"+
                            "<input type = 'text'   Class = 'letra' style = 'width:060;color:#FF0000;font-weight:bold;text-align:right'  name = "+cComCscC+" id = '' maxlength='10' readonly>"+
                            "<input type = 'text'   Class = 'letra' style = 'width:040;color:#FF0000;font-weight:bold;text-align:center' name = "+cComSeqC+" readonly>"+
                            "<input type = 'text'   Class = 'letra' style = 'width:060;color:#FF0000;font-weight:bold;text-align:center' name = "+cCcoId+" maxlength='10' "+
                              "onBlur = 'javascript:this.value=this.value.toUpperCase();f_Links(\"cCcoId\",\"VALID\",\""+nSecuencia+"\",\"GRID\");'>"+
                            "<input type = 'text'   Class = 'letra' style = 'width:060;color:#FF0000;font-weight:bold;text-align:center' name = "+cSccId+" maxlength='20' "+
                              "onBlur = 'javascript:this.value=this.value.toUpperCase();f_Links(\"cSccId\",\"VALID\",\""+nSecuencia+"\",\"GRID\");'>"+
                            "<input type = 'hidden' Class = 'letra' style = 'width:020;color:#FF0000' name = "+cComCtoC+" readonly>"+
                            "<input type = 'text'   Class = 'letra' style = 'width:60;text-align:right' name = "+nComBRet+" maxlength = '10' disabled "+
                              "onKeyUp = 'javascript:this.value=f_ValDec(this.value);if (this.value.substr(-1) != \".\") { f_Calcula_Retencion(\""+nSecuencia+"\");f_Cuadre_Debitos_Creditos(); }' "+
                              "onBlur = 'javascript:if (this.value.substr(-1) == \".\") { this.value = this.value.substring(0, this.value.length-1); } f_ValDec(this.value);f_Calcula_Retencion(\""+nSecuencia+"\");f_Cuadre_Debitos_Creditos();f_Valida_Base_Retencion(\""+nSecuencia+"\")'>"+
                            "<input type = 'text'   Class = 'letra' style = 'width:60;text-align:right' name = "+nComBIva+" maxlength = '10' disabled "+
                              "onKeyUp = 'javascript:this.value=f_ValDec(this.value);'"+
                              "onBlur = 'javascript:if (this.value.substr(-1) == \".\") { this.value = this.value.substring(0, this.value.length-1); } f_ValDec(this.value);f_Cacula_BaseIva_e_Iva(\"BaseIva\",\""+nSecuencia+"\");f_Cuadre_Debitos_Creditos();f_Valida_Base_Retencion(\""+nSecuencia+"\")'>"+
                            "<input type = 'text'   Class = 'letra' style = 'width:60;text-align:right' name = "+nComIva+" maxlength = '10' disabled "+
                              "onKeyUp = 'javascript:this.value=f_ValDec(this.value);'"+
                              "onBlur = 'javascript:if (this.value.substr(-1) == \".\") { this.value = this.value.substring(0, this.value.length-1); } f_ValDec(this.value);f_Cacula_BaseIva_e_Iva(\"VlrIva\",\""+nSecuencia+"\");f_Cuadre_Debitos_Creditos();f_Valida_Base_Retencion(\""+nSecuencia+"\")'>"+
                            "<input type = 'text'   Class = 'letra' style = 'width:80;text-align:right' name = "+nComVlr+" maxlength = '10' "+
                              "onKeyUp = 'javascript:this.value=f_ValDec(this.value);if (this.value.substr(-1) != \".\") { f_Cuadre_Debitos_Creditos(); } f_Enter(\"Grid_Comprobante\",event,this.name);'"+
                              "onBlur = 'javascript:if (this.value.substr(-1) == \".\") { this.value = this.value.substring(0, this.value.length-1); } f_ValDec(this.value);f_Cuadre_Debitos_Creditos();f_Valida_Base_Retencion(\""+nSecuencia+"\")'>"+
                            "<input type = 'text'   Class = 'letra' style = 'width:80;text-align:right' name = "+nComVlrNF+" maxlength='10' "+
                              "onKeyUp = 'javascript:this.value=f_ValDec(this.value);if (this.value.substr(-1) != \".\") { f_Cacula_BaseIva_e_Iva(\"BaseNiif\",\""+nSecuencia+"\");f_Cuadre_Debitos_Creditos(); } f_Enter(event,this.name);'"+
                              "onBlur = 'javascript:if (this.value.substr(-1) == \".\") { this.value = this.value.substring(0, this.value.length-1); } f_ValDec(this.value);f_Cuadre_Debitos_Creditos()'>"+
                            "<input type = 'text'   Class = 'letra' style = 'width:020' name = "+cComMov+" maxlength='1' readonly "+
                              "onKeyUp = 'javascript:this.value=this.value.toUpperCase();"+
                              "if(this.value != \"D\" && this.value != \"C\"){this.value=\"\";alert(\"El Movimiento Debe Ser Debito o Credito, Verifique\")};f_Cuadre_Debitos_Creditos()'>"+
                            "<input type = 'button' Class = 'letra' style = 'width:020;text-align:center' id = "+oBtnDel+" value = 'X' "+
                              "onClick = 'javascript:f_Delete_Row(\"Grid_Comprobante\",this.value,\""+nSecuencia+"\")'>"+
                            "<input type = 'hidden' name = "+cComNit+"      value = '' readonly>"+
                            "<input type = 'hidden' name = "+cTerTip+"      value = '' readonly>"+
                            "<input type = 'hidden' name = "+cTerId+"       value = '' readonly>"+
                            "<input type = 'hidden' name = "+cTerTipB+"     value = '' readonly>"+
                            "<input type = 'hidden' name = "+cTerIdB+"      value = '' readonly>"+
                            "<input type = 'hidden' name = "+cPucId+"       value = '' readonly>"+
                            "<input type = 'hidden' name = "+cPucDet+"      value = '' readonly>"+
                            "<input type = 'hidden' name = "+cPucTer+"      value = '' readonly>"+
                            "<input type = 'hidden' name = "+nPucBRet+"     value = '' readonly>"+
                            "<input type = 'hidden' name = "+nPucRet+"      value = '' readonly>"+
                            "<input type = 'hidden' name = "+cPucNat+"      value = '' readonly>"+
                            "<input type = 'hidden' name = "+cPucInv+"      value = '' readonly>"+
                            "<input type = 'hidden' name = "+cPucCco+"      value = '' readonly>"+
                            "<input type = 'hidden' name = "+cPucDoSc+"     value = '' readonly>"+
                            "<input type = 'hidden' name = "+cPucTipEj+"    value = '' readonly>"+
                            "<input type = 'hidden' name = "+cComVlr1+"     value = '' readonly>"+
                            "<input type = 'hidden' name = "+cComVlr2+"     value = '' readonly>"+
                            "<input type = 'hidden' name = "+cComFac+"      value = '' readonly>"+
                            "<input type = 'hidden' name = "+cSucId+"       value = '' readonly>"+
                            "<input type = 'hidden' name = "+cDocId+"       value = '' readonly>"+
                            "<input type = 'hidden' name = "+cDocSuf+"      value = '' readonly>"+
                            //Campos Intermediacion de pago
                            "<input type = 'hidden' name = "+cComIdCB+"     value = '' readonly>"+
                            "<input type = 'hidden' name = "+cComCodCB+"    value = '' readonly>"+
                            "<input type = 'hidden' name = "+cComCscCB+"    value = '' readonly>"+
                            "<input type = 'hidden' name = "+cComSeqCB+"    value = '' readonly>"+
                            "<input type = 'hidden' name = "+cCtoIdInp+"    value = '' readonly>"+
                            "<input type = 'hidden' name = "+cPucIdInp+"    value = '' readonly>"+
                            "<input type = 'hidden' name = "+cPucDetInp+"   value = '' readonly>"+
                            "<input type = 'hidden' name = "+cPucTerInp+"   value = '' readonly>"+
                            "<input type = 'hidden' name = "+nPucBRetInp+"  value = '' readonly>"+
                            "<input type = 'hidden' name = "+nPucRetInp+"   value = '' readonly>"+
                            "<input type = 'hidden' name = "+cPucNatInp+"   value = '' readonly>"+
                            "<input type = 'hidden' name = "+cPucInvInp+"   value = '' readonly>"+
                            "<input type = 'hidden' name = "+cPucCcoInp+"   value = '' readonly>"+
                            "<input type = 'hidden' name = "+cPucDoScInp+"  value = '' readonly>"+
                            "<input type = 'hidden' name = "+cPucTipEjInp+" value = '' readonly>"+
                            "<input type = 'hidden' name = "+cComVlr1Inp+"  value = '' readonly>"+
                            "<input type = 'hidden' name = "+cComVlr2Inp+"  value = '' readonly>";

        document.forms['frgrm']['nSecuencia'].value = nSecuencia;
      }

      function f_Add_New_Row_Dos() {

        var cGrid      = document.getElementById("Grid_Dos");
        var nLastRow   = cGrid.rows.length;
        var nSecuencia = nLastRow+1;
        var cTableRow  = cGrid.insertRow(nLastRow);

        var cSucId_DO   = 'cSucId_DO'   + nSecuencia; // Suc
        var cDocId_DO   = 'cDocId_DO'   + nSecuencia; // DO
        var cDocSuf_DO  = 'cDocSuf_DO'  + nSecuencia; // Sufijo
        var cTerId_DO   = 'cTerId_DO'   + nSecuencia; // Id del Tercero
        var cTerNom_DO  = 'cTerNom_DO'  + nSecuencia; // Nombre del Tercero
        var cTerTip_DO  = 'cTerTip_DO'  + nSecuencia; // Hidden (Tipo de Tercero)
        var cTerTipB_DO = 'cTerTipB_DO' + nSecuencia; // Hidden (Tipo de Tercero Dos)
        var cTerIdB_DO  = 'cTerIdB_DO'  + nSecuencia; // Hidden (Id del Tercero Dos)
        var cDocFec_DO  = 'cDocFec_DO'  + nSecuencia; // Fecha
        var cCcoId_DO   = 'cCcoId_DO'   + nSecuencia; // Centro de Costos
        var nVlrPro_DO  = 'nVlrPro_DO'  + nSecuencia; // Valor Prorrateo
        var oBtnDel     = 'oBtnDel_DO'  + nSecuencia; // Boton de Borrar Row

        var TD_xAll = cTableRow.insertCell(0);
        TD_xAll.innerHTML = "<input type = 'text'   Class = 'letra' style = 'width:040;text-align:center' name = "+cSucId_DO+" readonly>"+
                            "<input type = 'text'   Class = 'letra' style = 'width:160;text-align:center' name = "+cDocId_DO+" id='' "+
                            "onBlur = 'javascript:this.value=this.value.toUpperCase();f_Links(\"cDocId_DO\",\"VALID\",\""+nSecuencia+"\");'>"+
                            "<input type = 'text'   Class = 'letra' style = 'width:040;text-align:center' name = "+cDocSuf_DO+" readonly>"+
                            "<input type = 'text'   Class = 'letra' style = 'width:100;text-align:center' name = "+cTerId_DO+" readonly>"+
                            "<input type = 'text'   Class = 'letra' style = 'width:380' name = "+cTerNom_DO+" readonly>"+
                            "<input type = 'text'   Class = 'letra' style = 'width:080;text-align:center' name = "+cDocFec_DO+" readonly>"+
                            "<input type = 'text'   Class = 'letra' style = 'width:060;text-align:center' name = "+cCcoId_DO+" readonly>"+
                            "<input type = 'text'   Class = 'letra' style = 'width:100;text-align:right' name = "+nVlrPro_DO+" maxlength = '3' "+
                            "onFocus = 'javascript:f_Asignar_Valor(this.value,\"cValAux01\")'"+
                            "onKeyUp = 'javascript:if(this.value!=\"\"){this.value=Math.round(this.value)}f_Enter(\"Grid_Dos\",event,this.name)'"+
                            "onBlur = 'javascript:if (this.value.substr(-1) == \".\") { this.value = this.value.substring(0, this.value.length-1); } f_ValDec(this.value);f_Maximo_Valor(this.value,100,this);f_Comparar_Valor(this.value,\"cValAux01\");'>"+
                            "<input type = 'button' Class = 'letra' style = 'width:020;text-align:center' id = "+oBtnDel+" value = 'X' "+
                            "onClick = 'javascript:f_Delete_Row(\"Grid_Dos\",this.value,\""+nSecuencia+"\")'>"+
                            "<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+cTerTip_DO+" readonly>"+
                            "<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+cTerTipB_DO+" readonly>"+
                            "<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+cTerIdB_DO+" readonly>";

        document.forms['frgrm']['nSecuencia_DO'].value = nSecuencia;
        f_Activar_Prorrateo();
      }

      function f_Add_New_Row_Conceptos() {
        var cGrid      = document.getElementById("Grid_Conceptos");
        var nLastRow   = cGrid.rows.length;
        var nSecuencia = nLastRow+1;
        var cTableRow  = cGrid.insertRow(nLastRow);

        var cCcoId_CCO   = 'cCcoId_CCO'   + nSecuencia; // Id del Concepto
        var cCcoDes_CCO  = 'cCcoDes_CCO'  + nSecuencia; // Descripcion del Concepto
        var cSucId_CCO   = 'cSucId_CCO'   + nSecuencia; // Sucursal
        var cDocId_CCO   = 'cDocId_CCO'   + nSecuencia; // Do
        var cDocSuf_CCO  = 'cDocSuf_CCO'  + nSecuencia; // Sufijo
        var nVlrBaiu_CCO = 'nVlrBaiu_CCO' + nSecuencia; // Valor Base AIU
        var nVlrBase_CCO = 'nVlrBase_CCO' + nSecuencia; // Valor Base
        var nVlrIva_CCO  = 'nVlrIva_CCO'  + nSecuencia; // Valor Iva
        var nVlr_CCO     = 'nVlr_CCO'     + nSecuencia; // Valor
        var cCtoVrl02_CCO= 'cCtoVrl02_CCO'+ nSecuencia; // Calculo Automatico del Iva
        var oBtnDel      = 'oBtnDel_CCO'  + nSecuencia; // Boton de Borrar Row


        if ("<?php echo $cAlfa ?>" == "SIACOSIA" || "<?php echo $cAlfa ?>" == "TESIACOSIP" || "<?php echo $cAlfa ?>" == "DESIACOSIP") {
          var nTamDes = 280;
          var nValores= 80;
        } else {
          var nTamDes = 300;
          var nValores= 100;
        }

        var TD_xAll = cTableRow.insertCell(0);
        TD_xAll.innerHTML = "<input type = 'text'   Class = 'letra' style = 'width:100;text-align:center' name = "+cCcoId_CCO+" id='' "+
                            "onBlur = 'javascript:this.value=this.value.toUpperCase();f_Links(\"cCcoId_CCO\",\"VALID\",\""+nSecuencia+"\");'>"+
                            "<input type = 'text'   Class = 'letra' style = 'width:"+nTamDes+"' name = "+cCcoDes_CCO+" readonly>"+
                            "<input type = 'text'   Class = 'letra' style = 'width:060;text-align:center' name = "+cSucId_CCO+"  readonly>"+
                            "<input type = 'text'   Class = 'letra' style = 'width:160;text-align:center' name = "+cDocId_CCO+"  readonly>"+
                            "<input type = 'text'   Class = 'letra' style = 'width:040;text-align:center' name = "+cDocSuf_CCO+" readonly>";

        //Base AAUI
        if ("<?php echo $cAlfa ?>" == "SIACOSIA" || "<?php echo $cAlfa ?>" == "TESIACOSIP" || "<?php echo $cAlfa ?>" == "DESIACOSIP") {
          TD_xAll.innerHTML+= "<input type = 'text'   Class = 'letra' style = 'width:"+nValores+";text-align:right' name = "+nVlrBaiu_CCO+" maxlength = '10' "+
                              "onFocus = 'javascript:f_Asignar_Valor(this.value,\""+nVlrBaiu_CCO+"\")'"+
                              "onKeyUp = 'javascript:this.value=f_Redondear(this.value)'"+
                              "onBlur = 'javascript:if (this.value.substr(-1) == \".\") { this.value = this.value.substring(0, this.value.length-1); } f_ValDec(this.value);f_Cacula_BaseIva_e_Iva_Conceptos(\"nVlrBase_CCO\",\""+nSecuencia+"\");f_Comparar_Valor(this.value,\""+nVlrBaiu_CCO+"\")'>";
        } else {
          TD_xAll.innerHTML+= "<input type = 'hidden' name = "+nVlrBaiu_CCO+" value=''>";
        }

        //Base
        TD_xAll.innerHTML+= "<input type = 'text'   Class = 'letra' style = 'width:"+nValores+";text-align:right' name = "+nVlrBase_CCO+" maxlength = '10' "+
                            "onFocus = 'javascript:f_Asignar_Valor(this.value,\""+nVlrBase_CCO+"\");f_Comparar_Base_Aiu(\""+nSecuencia+"\");'"+
                            "onKeyUp = 'javascript:this.value=f_Redondear(this.value)'"+
                            "onBlur = 'javascript:if (this.value.substr(-1) == \".\") { this.value = this.value.substring(0, this.value.length-1); } f_ValDec(this.value);f_Cacula_BaseIva_e_Iva_Conceptos(\"nVlrBase_CCO\",\""+nSecuencia+"\");f_Comparar_Valor(this.value,\""+nVlrBase_CCO+"\")'>";

        //Iva
        TD_xAll.innerHTML+= "<input type = 'text'   Class = 'letra' style = 'width:"+nValores+";text-align:right' name = "+nVlrIva_CCO+" maxlength = '10' "+
                            "onFocus = 'javascript:f_Asignar_Valor(this.value,\""+nVlrIva_CCO+"\")'"+
                            "onKeyUp = 'javascript:this.value=f_Redondear(this.value)'"+
                            "onBlur = 'javascript:if (this.value.substr(-1) == \".\") { this.value = this.value.substring(0, this.value.length-1); } f_ValDec(this.value);f_Cacula_BaseIva_e_Iva_Conceptos(\"nVlrIva_CCO\",\""+nSecuencia+"\");f_Comparar_Valor(this.value,\""+nVlrIva_CCO+"\")'>";

        //Valor
        TD_xAll.innerHTML+= "<input type = 'text'   Class = 'letra' style = 'width:"+nValores+";text-align:right' name = "+nVlr_CCO+" maxlength = '10' "+
                            "onKeyUp = 'javascript:this.value=f_Redondear(this.value);f_Enter(\"Grid_Conceptos\",event,this.name)'"+
                            "onBlur = 'javascript:if (this.value.substr(-1) == \".\") { this.value = this.value.substring(0, this.value.length-1); } f_ValDec(this.value);'>";

        //Boton
        TD_xAll.innerHTML+= "<input type = 'button' Class = 'letra' style = 'width:020;text-align:center' id = "+oBtnDel+" value = 'X' "+
                            "onClick = 'javascript:f_Delete_Row(\"Grid_Conceptos\",this.value,\""+nSecuencia+"\")'>"+
                            "<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+cCtoVrl02_CCO+" readonly>";

        document.forms['frgrm'][nVlrBaiu_CCO].readOnly  = (document.forms['frgrm']['cAiuApl'].value == "SI") ? false : true;
        document.forms['frgrm']['nSecuencia_CCO'].value = nSecuencia;
        f_Asignar_Base_Conceptos();
      }

      function f_Redondear(xValor) {

        if (document.forms['frgrm']['cCliTpApl'].checked == true || document.forms['frgrm']['cCliTpagApl'].checked == true  || '<?php echo $vSysStr['financiero_permitir_decimales_causaciones_automaticas'] ?>' == 'SI') {
          this.value=Math.round(parseFloat(this.value) * 100)/100;
        } else {
          this.value=Math.round(this.value)
        }

        return xValor;
      }

      function f_Borrar_Tabla(xTabla){
         var cGrid = document.getElementById(xTabla);
         switch (xTabla) {
           case "Grid_Comprobante":
             for(i=1; i<=document.forms['frgrm']['nSecuencia'].value; i++) {
              var x = cGrid.rows.length;
              cGrid.deleteRow(x - 1);
             }
             f_Add_New_Row_Comprobante();
           break;
           case "Grid_Dos":
             for(i=1; i<=document.forms['frgrm']['nSecuencia_DO'].value; i++) {
              var x = cGrid.rows.length;
              cGrid.deleteRow(x - 1);
             }
             f_Add_New_Row_Dos();
           break;
           case "Grid_Conceptos":
             for(i=1; i<=document.forms['frgrm']['nSecuencia_CCO'].value; i++) {
               var x = cGrid.rows.length;
               cGrid.deleteRow(x - 1);
             }
             f_Add_New_Row_Conceptos();
           break;
         }
      }

      function f_Cargar_Grillas(xFunction){
        var cPathUrl = "gFunction="+xFunction+
                       "&gTerId="        +document.forms['frgrm']['cTerId'].value            +
                       "&gTerIdB="       +document.forms['frgrm']['cTerIdB'].value           +
                       "&gTerTipB="      +document.forms['frgrm']['cTerTipB'].value          +
                       "&gSecuencia_DO=" +document.forms['frgrm']['nSecuencia_DO'].value     +
                       "&gSucId_DO1="    +document.forms['frgrm']['cSucId_DO1'].value        +
                       "&gDocId_DO1="    +document.forms['frgrm']['cDocId_DO1'].value        +
                       "&gDocSuf_DO1="   +document.forms['frgrm']['cDocSuf_DO1'].value       +
                       "&gTerId_DO1="    +document.forms['frgrm']['cTerId_DO1'].value        +
                       "&gSecuencia_CCO="+document.forms['frgrm']['nSecuencia_CCO'].value    +
                       "&gCcoId_CCO1="   +document.forms['frgrm']['cCcoId_CCO1'].value       +
                       "&gComId="        +document.forms.frgrm['cComId'].value.toUpperCase() +
                       "&gComCod="       +document.forms.frgrm['cComCod'].value.toUpperCase()+
                       "&gTipPro="       +document.forms.frgrm['cTipPro'].value.toUpperCase();

        if (xFunction == "cTerId" || xFunction == "cTerNom") {
          cPathUrl = "frgridos.php?" + cPathUrl;
          parent.fmpro2.location = cPathUrl;
        } else if (xFunction == "cTerIdB" || xFunction == "cTerNomB") {
          cPathUrl = "frgricco.php?" + cPathUrl;
          parent.fmpro2.location = cPathUrl;
        }
      }

      function f_Activar_Prorrateo(){
        for(var i=0; i<document.forms['frgrm']['nSecuencia_DO'].value; i++) {
          if (document.forms['frgrm']['cTipPro'].value == "PORCENTAJE") { // Para evitar el error en la sumatoria
            document.forms['frgrm']['nVlrPro_DO'+(i+1)].readOnly = false;
            document.forms['frgrm']['nVlrPro_DO'+(i+1)].onFocus  = function () { f_Asignar_Valor(this.value,"cValAux01"); }
            document.forms['frgrm']['nVlrPro_DO'+(i+1)].onKeyUp  = function () { if(this.value!=""){this.value=Math.round(this.value)} f_Enter("Grid_Dos",event,this.name); }
            document.forms['frgrm']['nVlrPro_DO'+(i+1)].onBlur   = function () { if (this.value.substr(-1) == ".") { this.value = this.value.substring(0, this.value.length-1); } f_ValDec(this.value);f_Maximo_Valor(this.value,100,this);f_Comparar_Valor(this.value,"cValAux01"); }
          } else {
            document.forms['frgrm']['nVlrPro_DO'+(i+1)].readOnly = true;
            document.forms['frgrm']['nVlrPro_DO'+(i+1)].onFocus  = "";
            document.forms['frgrm']['nVlrPro_DO'+(i+1)].onKeyUp   = "";
            document.forms['frgrm']['nVlrPro_DO'+(i+1)].onBlur   = "";
            document.forms['frgrm']['nVlrPro_DO'+(i+1)].value = "";
          }
        }
      }

      function f_Cambiar_Prorrateo(){
        //Si el valor cambia debe borrarse toda grilla de conceptos y volver a cargar el primer concepto del proveedor
        f_Borrar_Tabla("Grid_Conceptos");
        f_Cargar_Grillas('cTerIdB');
      }

      function f_Maximo_Valor(xValor,xMaximo,xCampo){
        if(xValor < 0 || xValor > xMaximo) {
          alert("El Valor No Debe Ser Menor que Cero ni Mayor a "+xMaximo);
          xCampo.value = "";
          xCampo.focus();
        }
      }

      function f_Asignar_Valor(xValor,xCampo) {
        document.forms['frgrm'][xCampo].value ='';
        document.forms['frgrm'][xCampo].value =xValor;
      }

      function f_Comparar_Valor(xValor,xCampo) {
        if (xValor != document.forms['frgrm'][xCampo].value){
          f_Borrar_Tabla('Grid_Comprobante');
        }
      }

      function f_Comparar_Base_Aiu(xSecuencia) {
        var nSwicht = 0;
        if (document.forms['frgrm']['cAiuApl'].value == "SI") {
          if (document.forms['frgrm']['nVlrBase_CCO'+xSecuencia].value == "" && document.forms['frgrm']['nVlrBaiu_CCO'+xSecuencia].value == "") {
            alert("Debe Digitar la Base A.I.U. del Concepto.");
            nSwicht = 1;
            document.forms['frgrm']['nVlrBaiu_CCO'+xSecuencia].focus();
          }
        }

        if (nSwicht == 0) {
          f_Asignar_Valor(document.forms['frgrm']['nVlrBase_CCO'+xSecuencia].value,"cValAux01");
        }
      }

      function fnIsDecimalNumber(xValue){
        if (!isNaN(xValue)){
          if (xValue % 1 == 0) {
            // alert ("Es un numero entero");
            return false;
          } else {
            // alert ("Es un numero decimal");
            return true;
          }
        }
      }

      function f_Cacula_BaseIva_e_Iva_Conceptos(xTipo,xSecuencia) {
        switch (xTipo) {
          case "nVlrBase_CCO":
            var nBaseIva = (document.forms['frgrm']['cAiuApl'].value == "SI") ? ((document.forms['frgrm']['nVlrBaiu_CCO'+xSecuencia].value != "") ? document.forms['frgrm']['nVlrBaiu_CCO'+xSecuencia].value : 0) : ((document.forms['frgrm']['nVlrBase_CCO'+xSecuencia].value != "") ? document.forms['frgrm']['nVlrBase_CCO'+xSecuencia].value : 0);
            //Validando si la base tiene decimales
            if (document.forms['frgrm']['cCtoVrl02_CCO'+xSecuencia].value == 'SI') {
              if (document.forms['frgrm']['cCliTpApl'].checked == true || document.forms['frgrm']['cCliTpagApl'].checked == true) {
                document.forms['frgrm']['nVlrIva_CCO'+xSecuencia].value = Math.round(parseFloat(nBaseIva*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100)) * 100)/100;
              } else {
                document.forms['frgrm']['nVlrIva_CCO'+xSecuencia].value = Math.round(parseFloat(nBaseIva*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100)));
              }
            } else {
              document.forms['frgrm']['nVlrIva_CCO'+xSecuencia].value = 0;
            }
            var nBase = (document.forms['frgrm']['nVlrBase_CCO'+xSecuencia].value != "") ? document.forms['frgrm']['nVlrBase_CCO'+xSecuencia].value : 0;
            var nIva  = (document.forms['frgrm']['nVlrIva_CCO'+xSecuencia].value != "") ? document.forms['frgrm']['nVlrIva_CCO'+xSecuencia].value : 0;

            if (document.forms['frgrm']['cCliTpApl'].checked == true || document.forms['frgrm']['cCliTpagApl'].checked == true) {
              document.forms['frgrm']['nVlr_CCO'+xSecuencia].value = Math.round((parseFloat(nBase) + parseFloat(nIva)) * 100)/100;
            } else if ('<?php echo $vSysStr['financiero_permitir_decimales_causaciones_automaticas'] ?>' == 'SI' && fnIsDecimalNumber(parseFloat(nBase))) {
              document.forms['frgrm']['nVlr_CCO'+xSecuencia].value = Math.round((parseFloat(nBase) + parseFloat(nIva)) * 100)/100;
            } else {
              document.forms['frgrm']['nVlr_CCO'+xSecuencia].value = Math.round(parseFloat(nBase) + parseFloat(nIva));
            }
            f_Calcular_Total_Iva_Total_base();
          break;
          case "nVlrIva_CCO":
            var nBase = (document.forms['frgrm']['nVlrBase_CCO'+xSecuencia].value != "") ? document.forms['frgrm']['nVlrBase_CCO'+xSecuencia].value : 0;
            var nIva  = (document.forms['frgrm']['nVlrIva_CCO'+xSecuencia].value != "") ? document.forms['frgrm']['nVlrIva_CCO'+xSecuencia].value : 0;
            if (document.forms['frgrm']['cCliTpApl'].checked == true || document.forms['frgrm']['cCliTpagApl'].checked == true || '<?php echo $vSysStr['financiero_permitir_decimales_causaciones_automaticas'] ?>' == 'SI') {
              document.forms['frgrm']['nVlr_CCO'+xSecuencia].value = Math.round((parseFloat(nBase) + parseFloat(nIva)) * 100)/100;
            } else if ('<?php echo $vSysStr['financiero_permitir_decimales_causaciones_automaticas'] ?>' == 'SI' && fnIsDecimalNumber(parseFloat(nBase) + parseFloat(nIva))) { 
              document.forms['frgrm']['nVlr_CCO'+xSecuencia].value = Math.round((parseFloat(nBase) + parseFloat(nIva)) * 100)/100;
            } else {
              document.forms['frgrm']['nVlr_CCO'+xSecuencia].value = Math.round(parseFloat(nBase) + parseFloat(nIva));
            }
            f_Calcular_Total_Iva_Total_base();
          break;
        }
      }

      function f_Asignar_Base_Conceptos() {
        var xSecuencia = 1;
        if(document.forms['frgrm']['nSecuencia_CCO'].value == 1 && document.forms['frgrm']['cCcoId_CCO'+xSecuencia].value != '') {
          if (document.forms['frgrm']['nComVlr01'].value == "") {
            document.forms['frgrm']['nVlrBase_CCO' +xSecuencia].value = 0;
            document.forms['frgrm']['nVlrBaiu_CCO' +xSecuencia].value = 0;
          } else {
            document.forms['frgrm']['nVlrBase_CCO' +xSecuencia].value = document.forms['frgrm']['nComVlr01'].value;
            document.forms['frgrm']['nVlrBaiu_CCO' +xSecuencia].value = (document.forms['frgrm']['cAiuApl'].value == "SI") ? document.forms['frgrm']['nAiuVlr01'].value : "";
          }
          if (document.forms['frgrm']['cCtoVrl02_CCO'+xSecuencia].value == 'SI') {
            if (document.forms['frgrm']['nComVlr02'].value == "") {
              document.forms['frgrm']['nVlrIva_CCO'+xSecuencia].value = 0;
            } else {
              document.forms['frgrm']['nVlrIva_CCO'+xSecuencia].value = (document.forms['frgrm']['cAiuApl'].value == "SI") ? document.forms['frgrm']['nAiuVlr02'].value : document.forms['frgrm']['nComVlr02'].value;
            }
          } else {
            document.forms['frgrm']['nVlrIva_CCO' +xSecuencia].value = 0;
          }

          var nBase = parseFloat(document.forms['frgrm']['nVlrBase_CCO' +xSecuencia].value) + parseFloat(document.forms['frgrm']['nVlrIva_CCO' +xSecuencia].value);
          if (document.forms['frgrm']['cCliTpApl'].checked == true || document.forms['frgrm']['cCliTpagApl'].checked == true) {
            document.forms['frgrm']['nVlr_CCO' +xSecuencia].value = Math.round(nBase * 100)/100;
          } else if ('<?php echo $vSysStr['financiero_permitir_decimales_causaciones_automaticas'] ?>' == 'SI' && fnIsDecimalNumber(nBase)) {
            document.forms['frgrm']['nVlr_CCO' +xSecuencia].value = Math.round(nBase * 100)/100;
          } else {
            document.forms['frgrm']['nVlr_CCO' +xSecuencia].value = Math.round(nBase);
          }
        }
      }

      function f_Borrar_Conceptos_Do(xSucId,xDocId,xDocSuf) {

        //Busco cuantos DO hay en la grilla
        var nBan = 0;
        for(var nSecuencia=1; nSecuencia<=document.forms['frgrm']['nSecuencia_DO'].value; nSecuencia++) {
          if(document.forms['frgrm']['cSucId_DO' +nSecuencia].value != '' &&
             document.forms['frgrm']['cDocId_DO' +nSecuencia].value != '' &&
             document.forms['frgrm']['cDocSuf_DO'+nSecuencia].value != '') {
            nBan++;
          }
        }

        if(nBan > 0) {
          for(var nSecuencia=1; nSecuencia<=document.forms['frgrm']['nSecuencia_CCO'].value; nSecuencia++) {
            if(xSucId  == document.forms['frgrm']['cSucId_CCO' +nSecuencia].value &&
               xDocId  == document.forms['frgrm']['cDocId_CCO' +nSecuencia].value &&
               xDocSuf == document.forms['frgrm']['cDocSuf_CCO'+nSecuencia].value) {
              //borrando fila
              var cGrid = document.getElementById("Grid_Conceptos");
              var nLastRow = cGrid.rows.length;

              if(nSecuencia < nLastRow){
                var j=0;
                for(var i=nSecuencia;i<nLastRow;i++){
                  j = parseFloat(i)+1;
                  document.forms['frgrm']['cCcoId_CCO'   + i].value = document.forms['frgrm']['cCcoId_CCO'   + j].value; // Id del Concepto
                  document.forms['frgrm']['cCcoDes_CCO'  + i].value = document.forms['frgrm']['cCcoDes_CCO'  + j].value; // Descripcion del Concepto
                  document.forms['frgrm']['cSucId_CCO'   + i].value = document.forms['frgrm']['cSucId_CCO'   + j].value; // DO
                  document.forms['frgrm']['cDocId_CCO'   + i].value = document.forms['frgrm']['cDocId_CCO'   + j].value; // DO
                  document.forms['frgrm']['cDocSuf_CCO'  + i].value = document.forms['frgrm']['cDocSuf_CCO'  + j].value; // DO
                  document.forms['frgrm']['nVlrBaiu_CCO' + i].value = document.forms['frgrm']['nVlrBaiu_CCO' + j].value; // Valor Base AIU
                  document.forms['frgrm']['nVlrBase_CCO' + i].value = document.forms['frgrm']['nVlrBase_CCO' + j].value; // Valor Base
                  document.forms['frgrm']['nVlrIva_CCO'  + i].value = document.forms['frgrm']['nVlrIva_CCO'  + j].value; // Valor Iva
                  document.forms['frgrm']['nVlr_CCO'     + i].value = document.forms['frgrm']['nVlr_CCO'     + j].value; // Valor
                  document.forms['frgrm']['cCtoVrl02_CCO'+ i].value = document.forms['frgrm']['cCtoVrl02_CCO'+ j].value; // Calculo Automatico del Iva
                }
              }
              cGrid.deleteRow(nLastRow - 1);
              document.forms['frgrm']['nSecuencia_CCO'].value = nLastRow - 1;
            }
          }
          if (document.forms['frgrm']['nSecuencia_CCO'].value == 0){
            f_Cambiar_Prorrateo();
          }
        }
        if(nBan == 0) {
          f_Cambiar_Prorrateo();
        }
      }

      function f_DatosAdicionales(xCampo) {
        switch(xCampo) {
          case "cCliTpApl":
            if(document.forms['frgrm'][xCampo].checked == true) {
              document.forms['frgrm'][xCampo].value = "SI";
              //la tasa Pactada y la tasa de Pago son excluyentes
              document.forms['frgrm']['cCliTpagApl'].checked = false;
              document.forms['frgrm']['cCliTpagApl'].value   = "NO";
              document.forms['frgrm']['cCliTpag'].value      = "";
            } else {
              document.forms['frgrm'][xCampo].value = "NO";
            }
          break;
          case "cCliTpagApl":
            if(document.forms['frgrm'][xCampo].checked == true) {
              document.forms['frgrm'][xCampo].value = "SI";
              //la tasa Pactada y la tasa de Pago son excluyentes
              document.forms['frgrm']['cCliTpApl'].checked = false;
              document.forms['frgrm']['cCliTpApl'].value   = "NO";
            } else {
              document.forms['frgrm'][xCampo].value     = "NO";
              document.forms['frgrm']['cCliTpag'].value = "";
            }
          break;
          case "cAiuApl":
            if(document.forms['frgrm'][xCampo].checked == true) {
              if (document.forms['frgrm']['nComVlr01'].value.length > 0) {
                document.forms['frgrm'][xCampo].value = "SI";
                document.forms['frgrm']['nAiuVlr01'].readOnly = false;
                document.forms['frgrm']['nAiuVlr02'].readOnly = false;
                for(var nSecuencia=1; nSecuencia<=document.forms['frgrm']['nSecuencia_CCO'].value; nSecuencia++) {
                  document.forms['frgrm']['nVlrBaiu_CCO' + nSecuencia].value = ""; // Valor Base AIU
                  document.forms['frgrm']['nVlrBaiu_CCO' + nSecuencia].readOnly = false; // Valor Base AIU
                }
                f_Borrar_Tabla('Grid_Comprobante');
              } else {
                document.forms['frgrm'][xCampo].checked = false;
                document.forms['frgrm'][xCampo].value   = "NO";
                document.forms['frgrm']['nAiuVlr01'].value = "";
                document.forms['frgrm']['nAiuVlr02'].value = "";
                document.forms['frgrm']['nAiuVlr01'].readOnly = true;
                document.forms['frgrm']['nAiuVlr02'].readOnly = true;
                alert("Para Habilitar la Base A.I.U, debe Digitar la Base Total del Comprobante, Verifique.");
              }
            } else {
              document.forms['frgrm'][xCampo].value      = "NO";
              document.forms['frgrm']['nAiuVlr01'].value = "";
              document.forms['frgrm']['nAiuVlr02'].value = "";
              document.forms['frgrm']['nAiuVlr01'].readOnly = true;
              document.forms['frgrm']['nAiuVlr02'].readOnly = true;
              for(var nSecuencia=1; nSecuencia<=document.forms['frgrm']['nSecuencia_CCO'].value; nSecuencia++) {
                document.forms['frgrm']['nVlrBaiu_CCO' + nSecuencia].value    = ""; // Valor Base AIU
                document.forms['frgrm']['nVlrBaiu_CCO' + nSecuencia].readOnly = true; // Valor Base AIU
              }
              f_Borrar_Tabla('Grid_Comprobante');
            }
          break;
          case "cComIntPa":
           document.forms['frgrm'][xCampo].value = (document.forms['frgrm'][xCampo].checked == true) ? "SI" : "NO";
           f_Borrar_Tabla('Grid_Comprobante');
          break;
          default: //NO hace nada
          break;
        }

        if (xCampo != "cComIntPa") {
          if (document.forms['frgrm']['cCliTpApl'].checked == true || document.forms['frgrm']['cCliTpagApl'].checked == true) {
            document.forms['frgrm']['nComVlr02'].value=Math.round(parseFloat(document.forms['frgrm']['nComVlr01'].value*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100)) * 100)/100;
          } else if ('<?php echo $vSysStr['financiero_permitir_decimales_causaciones_automaticas'] ?>' == 'SI' && fnIsDecimalNumber(parseFloat(document.forms['frgrm']['nComVlr01'].value))) {
            document.forms['frgrm']['nComVlr02'].value=Math.round(parseFloat(document.forms['frgrm']['nComVlr01'].value*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100)) * 100)/100;
          } else {
            document.forms['frgrm']['nComVlr02'].value=Math.round(parseFloat(document.forms['frgrm']['nComVlr01'].value*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100)));
          }
        }
      }

      function f_Enviar() {
        f_Enabled_Combos();
        document.forms['frgrm']['Btn_Guardar'].disabled=true;
        document.forms['frgrm']['nTimesSave'].value++;
        document.forms['frgrm'].submit();
        f_Disabled_Combos();
      }
    </script>
  </head>
  <body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
    <!-- PRIMERO PINTO EL FORMULARIO -->
    <center>
      <table border ="0" cellpadding="0" cellspacing="0" width="980">
        <tr>
          <td>
            <form name = "frgrm" action = "frcpagra.php" method = "post" target="fmpro">
              <fieldset>
                <legend>Nueva <?php echo $_COOKIE['kProDes'] ?></legend>
                <input type = "hidden" name = "nSecuencia" value = "0">
                <input type = "hidden" name = "nSecuencia_DO" value = "0">
                <input type = "hidden" name = "nSecuencia_CCO" value = "0">
                <input type = "hidden" name = "nTimesSave" value = "0">
                <input type = "hidden" name = "dComFec_Ant" value = "">
                <input type = "hidden" name = "cComTco" value = ""> <!-- Tipo de Consecutivo para el comprobante (MANUAL/AUTOMATICO) -->
                <input type = "hidden" name = "cComCco" value = ""> <!-- Control Consecutivo para el comprobante (MENSUAL/ANUAL/INDEFINIDO) -->
                <input type = "hidden" name = "cComLot" value = ""> <!-- Lote de transmision de Aduacarga -->
                <input type = "hidden" name = "cValAux01" value = ""> <!-- Campo utilizado para comparar si los valore de un campo han cambiado -->
                <input type = "hidden" name = "cValAux02" value = ""> <!-- Campo utilizado para comparar si los valore de un campo han cambiado -->
                <input type = "hidden" name = "dComBpo" value = ""> <!-- Campo utilizado para inidicar si el proceso se realizo desde BPO -->

                <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:1000">
                  <?php $cCols = f_Format_Cols(50); echo $cCols; ?>
                  <tr>
                    <td Class = "name" colspan = "1">Id<br>
                      <input type = "text" Class = "letra" style = "width:20" name = "cComId" value = "P" readonly>
                    </td>
                    <td Class = "name" colspan = "2">
                      <a href = "javascript:document.forms['frgrm']['cComCod'].value='';
                                            document.forms['frgrm']['cComDes'].value='';
                                            f_Links('cComCod','VALID')" id="id_href_cComCod">Cod</a><br>
                      <input type = "text" Class = "letra" style = "width:40;text-align:center" name = "cComCod" value = ""
                        onfocus="javascript:this.value='';
                                            document.forms['frgrm']['cComDes'].value='';
                                            this.style.background='#00FFFF'"
                        onblur = "javascript:f_Links('cComCod','VALID');
                                             this.style.background='#FFFFFF';
                                             document.forms['frgrm']['cComDes'].focus();">
                    </td>
                    <td Class = "name" colspan = "15">Descripcion<br>
                      <input type = "text" Class = "letra" style = "width:300" name = "cComDes" readonly>
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
                    </td>
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
                    <?php if ($_COOKIE['kModo'] == "ANTERIOR" && $vSysStr['financiero_permitir_digitar_fecha_periodo_anterior'] == 'NO') { ?>
                      <td Class = "name" colspan = "4">Fecha<br>
                        <select Class = "letrase" name = "dComFec" style = "width:80">
                        </select>
                      </td>
                    <?php } else { ?>
                      <td Class = "name" colspan = "4">
                        <a href='javascript:show_calendar("frgrm.dComFec")' id="id_href_dComFec">Fecha</a><br>
                        <input type = "text" Class = "letra" style = "width:80;text-align:center"
                          name = "dComFec" value = "<?php echo date('Y-m-d') ?>" onBlur = "javascript:f_Date(this)">
                      </td>
                    <?php } ?>
                    <td Class = "name" colspan = "3">Hora<br>
                      <input type = "text" Class = "letra" style = "width:60;text-align:center"
                        name = "tRegHCre" value = "<?php echo date('H:i:s') ?>" readonly>
                    </td>
                    <td Class = "name" colspan = "5">Factura<br>
                      <input type = "text" Class = "letra" style = "width:100;text-align:center" name = "cComCsc" <?php echo (($vSysStr['financiero_permitir_caracteres_alfanumericos_consecutivo_manual']) == 'NO') ? "onblur = \"javascript:f_FixFloat(this);\"" : '' ?> maxlength = "<?php echo (($vSysStr['financiero_digitos_consecutivo_manual']+0) > 0) ? $vSysStr['financiero_digitos_consecutivo_manual'] : 20 ?>"
                        onfocus = "javascript:this.value='';this.style.background='#00FFFF'"
                        onblur = "javascript:this.style.background='#FFFFFF';f_Links('cComCsc','VALID');">
                    </td>
                    <td Class = "name" colspan = "6">Consecutivo<br>
                      <?php if (f_InList($cAlfa,"UPSXXXXX","DEUPSXXXXX","TEUPSXXXXX")) { ?>
                        <input type = "hidden" name = "cComCsc2" readonly>
                        <input type = "text" Class = "letra" style = "width:120;text-align:center" name = "cComCsc3" readonly>
                      <?php } else { ?>
                        <input type = "text" Class = "letra" style = "width:120;text-align:center" name = "cComCsc2" readonly>
                        <input type = "hidden" name = "cComCsc3" readonly>
                      <?php } ?>
                    </td>
                    <td Class = "name" colspan = "4">
                      <a href='javascript:show_calendar("frgrm.dComVen")' id="id_href_dComVen">Vencimiento</a><br>
                      <input type = "text" Class = "letra" style = "width:80;text-align:center"
                        name = "dComVen" value = "<?php echo date('Y-m-d') ?>" onBlur = "javascript:f_Date(this)">
                    </td>
                  </tr>
                  <tr>
                    <td Class = "name" colspan = "5">Tipo Tercero<br>
                      <select Class = "letrase" name = "cTerTip" style = "width:100" disabled>
                        <option value = 'CLICLIXX' selected>CLIENTE</option>
                        <option value = 'CLIPROCX'>PROVEEDORC</option>
                        <option value = 'CLIPROEX'>PROVEEDORE</option>
                        <option value = 'CLIEFIXX'>E. FINANCIERA</option>
                        <option value = 'CLISOCXX'>SOCIO</option>
                        <option value = 'CLIEMPXX'>EMPLEADO</option>
                        <option value = 'CLIOTRXX'>OTROS</option>
                      </select>
                    </td>
                    <td Class = "name" colspan = "4">
                      <a href = "javascript:document.forms['frgrm']['cTerId'].value   = '';
                                            document.forms['frgrm']['cTerNom'].value  = '';
                                            document.forms['frgrm']['cTerDV'].value   = '';
                                            document.forms['frgrm']['cTerReg'].value  = '';
                                            document.forms['frgrm']['cTerArr'].value  = '';
                                            document.forms['frgrm']['cTerAcr'].value  = '';
                                            document.forms['frgrm']['cTerArrI'].value = '';
                                            document.forms['frgrm']['cTerPci'].value  = '';
                                            document.forms['frgrm']['cCliAecId'].value  = '';
                                            document.forms['frgrm']['cCliAecDes'].value = '';
                                            document.forms['frgrm']['cCliAecRet'].value = '';
                                            f_Borrar_Tabla('Grid_Comprobante');
                                            f_Links('cTerId','VALID')" id="id_href_cTerId">Nit</a><br>
                      <input type = "text" Class = "letra" style = "width:80;text-align:center" name = "cTerId"
                        onfocus="javascript:document.forms['frgrm']['cTerId'].value   = '';
                                            document.forms['frgrm']['cTerNom'].value  = '';
                                            document.forms['frgrm']['cTerDV'].value   = '';
                                            document.forms['frgrm']['cTerReg'].value  = '';
                                            document.forms['frgrm']['cTerArr'].value  = '';
                                            document.forms['frgrm']['cTerAcr'].value  = '';
                                            document.forms['frgrm']['cTerArrI'].value = '';
                                            document.forms['frgrm']['cTerPci'].value  = '';
                                            document.forms['frgrm']['cCliAecId'].value  = '';
                                            document.forms['frgrm']['cCliAecDes'].value = '';
                                            document.forms['frgrm']['cCliAecRet'].value = '';
                                            f_Borrar_Tabla('Grid_Comprobante');
                                            this.style.background='#00FFFF'"
                        onBlur = "javascript:this.value=this.value.toUpperCase();
                                            f_Links('cTerId','VALID');
                                            this.style.background='#FFFFFF'">
                    </td>
                    <td Class = "name" colspan = "1">Dv<br>
                      <input type = "text" Class = "letra" style = "width:20;text-align:center" name = "cTerDV" readonly>
                    </td>
                    <td Class = "name" colspan = "15">Cliente<br>
                      <input type = "text" Class = "letra" style = "width:300" name = "cTerNom"
                        onfocus="javascript:document.forms['frgrm']['cTerReg'].value  = '';
                                            document.forms['frgrm']['cTerArr'].value  = '';
                                            document.forms['frgrm']['cTerAcr'].value  = '';
                                            document.forms['frgrm']['cTerArrI'].value = '';
                                            document.forms['frgrm']['cTerPci'].value  = '';
                                            document.forms['frgrm']['cCliAecId'].value  = '';
                                            document.forms['frgrm']['cCliAecDes'].value = '';
                                            document.forms['frgrm']['cCliAecRet'].value = '';
                                            f_Borrar_Tabla('Grid_Comprobante');
                                            this.style.background='#00FFFF'"
                        onBlur = "javascript:this.value=this.value.toUpperCase();
                                            f_Links('cTerNom','VALID');
                                            this.style.background='#FFFFFF'">
                    </td>
                    <td Class = "name" colspan = "1"><br>
                      <input type = "text" Class = "letra" style = "width:20;text-align:center" name = "cTab01" readonly>
                    </td>
                    <td Class = "name" colspan = "5">Tipo Tercero<br>
                      <select Class = "letrase" name = "cTerTipB" style = "width:100" disabled>
                        <option value = 'CLICLIXX'>CLIENTE</option>
                        <option value = 'CLIPROCX' selected>PROVEEDORC</option>
                        <option value = 'CLIPROEX'>PROVEEDORE</option>
                        <option value = 'CLIEFIXX'>E. FINANCIERA</option>
                        <option value = 'CLISOCXX'>SOCIO</option>
                        <option value = 'CLIEMPXX'>EMPLEADO</option>
                        <option value = 'CLIOTRXX'>OTROS</option>
                      </select>
                    </td>
                    <td Class = "name" colspan = "4">
                      <a href = "javascript:document.forms['frgrm']['cTerIdB'].value   = '';
                                            document.forms['frgrm']['cTerNomB'].value  = '';
                                            document.forms['frgrm']['cTerDVB'].value   = '';
                                            document.forms['frgrm']['cProReg'].value   = '';
                                            document.forms['frgrm']['cProRegSt'].value   = '';
                                            document.forms['frgrm']['cProArAre'].value = '';
                                            document.forms['frgrm']['cProArAcr'].value = '';
                                            document.forms['frgrm']['cProArIva'].value = '';
                                            document.forms['frgrm']['cProArIca'].value = '';
                                            document.forms['frgrm']['cProSucIca'].value= '';
                                            document.forms['frgrm']['cProAecId'].value  = '';
                                            document.forms['frgrm']['cProAecDes'].value = '';
                                            document.forms['frgrm']['cProAecRet'].value = '';
                                            f_Borrar_Tabla('Grid_Comprobante');
                                            f_Borrar_Tabla('Grid_Conceptos');
                                            f_Links('cTerIdB','VALID')" id="id_href_cTerIdB">Nit</a><br>
                      <input type = "text" Class = "letra" style = "width:80;text-align:center" name = "cTerIdB"
                        onfocus="javascript:document.forms['frgrm']['cTerIdB'].value   = '';
                                            document.forms['frgrm']['cTerNomB'].value  = '';
                                            document.forms['frgrm']['cTerDVB'].value   = '';
                                            document.forms['frgrm']['cProReg'].value   = '';
                                            document.forms['frgrm']['cProRegSt'].value   = '';
                                            document.forms['frgrm']['cProArAre'].value = '';
                                            document.forms['frgrm']['cProArAcr'].value = '';
                                            document.forms['frgrm']['cProArIva'].value = '';
                                            document.forms['frgrm']['cProArIca'].value = '';
                                            document.forms['frgrm']['cProSucIca'].value= '';
                                            document.forms['frgrm']['cProAecId'].value  = '';
                                            document.forms['frgrm']['cProAecDes'].value = '';
                                            document.forms['frgrm']['cProAecRet'].value = '';
                                            f_Borrar_Tabla('Grid_Comprobante');
                                            f_Borrar_Tabla('Grid_Conceptos');
                                            this.style.background='#00FFFF'"
                        onBlur = "javascript:this.value=this.value.toUpperCase();
                                             f_Links('cTerIdB','VALID');
                                             this.style.background='#FFFFFF'">
                    </td>
                    <td Class = "name" colspan = "1">Dv<br>
                      <input type = "text" Class = "letra" style = "width:20;text-align:center" name = "cTerDVB" readonly>
                    </td>
                    <td Class = "name" colspan = "14">Tercero<br>
                      <input type = "text" Class = "letra" style = "width:280" name = "cTerNomB"
                        onfocus="javascript:document.forms['frgrm']['cProReg'].value   = '';
                                            document.forms['frgrm']['cProRegSt'].value   = '';
                                            document.forms['frgrm']['cProArAre'].value = '';
                                            document.forms['frgrm']['cProArAcr'].value = '';
                                            document.forms['frgrm']['cProArIva'].value = '';
                                            document.forms['frgrm']['cProArIca'].value = '';
                                            document.forms['frgrm']['cProSucIca'].value= '';
                                            document.forms['frgrm']['cProAecId'].value  = '';
                                            document.forms['frgrm']['cProAecDes'].value = '';
                                            document.forms['frgrm']['cProAecRet'].value = '';
                                            f_Borrar_Tabla('Grid_Comprobante');
                                            f_Borrar_Tabla('Grid_Conceptos');
                                            this.style.background='#00FFFF'"
                        onBlur = "javascript:this.value=this.value.toUpperCase();
                                             f_Links('cTerNomB','VALID');
                                             this.style.background='#FFFFFF'">
                    </td>
                  </tr>
                  <tr>
                    <td Class = "name" colspan = "28">Observaciones Generales<br>
                      <input type = "text" Class = "letra" style = "width:560" name = "cComObs" maxlength="200" value = ""
                        onBlur = "javascript:this.value=this.value.toUpperCase()">
                    </td>
                    <td Class = "name" colspan = "5">Tipo Prorrateo<br>
                      <select Class = "letra" style = "width:100" name = "cTipPro" onchange="javascript:f_Cambiar_Prorrateo();f_Activar_Prorrateo();f_Borrar_Tabla('Grid_Comprobante')">
                        <option value="VALOR" selected>VALOR</option>
                        <option value="PORCENTAJE">PORCENTAJE</option>
                      </select>
                    </td>
                    <td Class = "name" colspan = "5">Tasa Cambio<br>
                      <input type = "text" Class = "letra" style = "width:100;text-align:right" name = "nTasaCambio" value="<?php echo f_Buscar_Tasa_Cambio(date('Y-m-d'),"USD"); ?>"
                                        onKeyUp = "javascript:this.value=f_ValDec(this.value);"
                                        onFocus = "javascript:this.style.background='#00FFFF';"
                                        onBlur  = "javascript:this.style.background='#FFFFFF'; if (this.value.substr(-1) == '.') { this.value = this.value.substring(0, this.value.length-1); } this.value=f_ValDec(this.value);this.style.background='#FFFFFF';">

                    </td>
                    <td Class = "name" colspan = "6"
                      onmouseover="javascript:status='Valor Uno'"
                      onmouseout ="javascript:status=''">Base Total<br>
                      <input type = "text" Class = "letra" style = "width:120;text-align:right" name = "nComVlr01" maxlength = "10"
                        onfocus = "javascript:this.style.background='#00FFFF'"
                        onblur  = "javascript:this.style.background='#FFFFFF'
                                                if (this.value.substr(-1) == '.') { this.value = this.value.substring(0, this.value.length-1); } 
                                                f_ValDec(this.value);
                                                if (document.forms['frgrm']['cCliTpApl'].checked == true || document.forms['frgrm']['cCliTpagApl'].checked == true) {
                                                  document.forms['frgrm']['nComVlr02'].value=Math.round(parseFloat(this.value*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100)) * 100)/100;
                                                } else if ('<?php echo $vSysStr['financiero_permitir_decimales_causaciones_automaticas'] ?>' == 'SI' && fnIsDecimalNumber(this.value)){ 
                                                  document.forms['frgrm']['nComVlr02'].value=Math.round(parseFloat(this.value*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100)) * 100)/100;
                                                } else {
                                                  document.forms['frgrm']['nComVlr02'].value=Math.round(parseFloat(this.value*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100)));
                                                }
                                                f_Asignar_Base_Conceptos();"
                        onkeyup = "javascript:this.value=f_ValDec(this.value);
                                              if (this.value.substr(-1) != '.') {
                                                if (document.forms['frgrm']['cCliTpApl'].checked == true || document.forms['frgrm']['cCliTpagApl'].checked == true) {
                                                  document.forms['frgrm']['nComVlr02'].value=Math.round(parseFloat(this.value*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100)) * 100)/100;
                                                } else if ('<?php echo $vSysStr['financiero_permitir_decimales_causaciones_automaticas'] ?>' == 'SI' && fnIsDecimalNumber(this.value)){ 
                                                  document.forms['frgrm']['nComVlr02'].value=Math.round(parseFloat(this.value*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100)) * 100)/100;
                                                } else {
                                                  document.forms['frgrm']['nComVlr02'].value=Math.round(parseFloat(this.value*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100)));
                                                }
                                                f_Asignar_Base_Conceptos();
                                              }">
                    </td>
                    <td Class = "name" colspan = "6"
                      onmouseover="javascript:status='Valor Dos'"
                      onmouseout ="javascript:status=''">Iva<br>
                      <input type = "text" Class = "letra" style = "width:120;text-align:right" name = "nComVlr02" maxlength = "10"
                        onfocus="javascript:this.style.background='#00FFFF'"
                        onblur = "javascript:if (this.value.substr(-1) == '.') { this.value = this.value.substring(0, this.value.length-1); } f_ValDec(this.value);
                                            this.style.background='#FFFFFF'">
                    </td>
                  </tr>
                  <tr>
                    <td colspan = "50">
                      <fieldset>
                        <legend>Datos Adicionales Causaci&oacute;n</legend>
                        <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:980">
                          <?php $cCols = f_Format_Cols(49); echo $cCols; ?>
                          <tr>
                            <!-- Condiciones Especiales Siaco -->
                            <?php if (f_InList($cAlfa,"SIACOSIA","TESIACOSIP","DESIACOSIP")) { ?>
                              <td Class = "name" colspan = "04">Tasa Pactada<br>
                                <input type = 'text' Class = 'letra' style = 'width:080;text-align:right' name = 'cCliTp' readonly>
                              </td>
                              <td Class = 'name' colspan = '02'><br>
                                <input type='checkbox' name = 'cCliTpApl' value = "NO" onClick = 'javascript:f_DatosAdicionales(this.name);'>
                              </td>
                              <td Class = "name" colspan = "04">Tasa de Pago<br>
                                <input type = 'text' Class = 'letra' style = 'width:080;text-align:right' name = 'cCliTpag' maxlength = "10"
                                        onKeyUp = "javascript:this.value=f_ValDec(this.value);"
                                        onFocus = "javascript:this.style.background='#00FFFF';"
                                        onBlur  = "javascript:this.style.background='#FFFFFF'; if (this.value.substr(-1) == '.') { this.value = this.value.substring(0, this.value.length-1); } this.value=f_ValDec(this.value);this.style.background='#FFFFFF';">
                              </td>
                              <td Class = 'name' colspan = '02'><br>
                                <input type='checkbox' name = 'cCliTpagApl' value = "NO" onClick = 'javascript:f_DatosAdicionales(this.name);'>
                              </td>
                              <td Class = "name" colspan = "04">Base A.I.U<br>
                                <input type = "text" Class = "letra" style = "width:80;text-align:right" name = "nAiuVlr01" maxlength = "10" readonly
                                  onfocus = "javascript:this.style.background='#00FFFF'"
                                  onblur  = "javascript:this.style.background='#FFFFFF'
                                                        if (this.value.substr(-1) == '.') { this.value = this.value.substring(0, this.value.length-1); } 
                                                        f_ValDec(this.value);
                                                        if (document.forms['frgrm']['cCliTpApl'].checked == true || document.forms['frgrm']['cCliTpagApl'].checked == true) {
                                                          document.forms['frgrm']['nAiuVlr02'].value=Math.round(parseFloat(this.value*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100)) * 100)/100;
                                                        } else if ('<?php echo $vSysStr['financiero_permitir_decimales_causaciones_automaticas'] ?>' == 'SI' && fnIsDecimalNumber(parseFloat(this.value))) {
                                                          document.forms['frgrm']['nAiuVlr02'].value=Math.round(parseFloat(this.value*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100)) * 100)/100;
                                                        } else {
                                                          document.forms['frgrm']['nAiuVlr02'].value=Math.round(parseFloat(this.value*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100)));
                                                        }
                                                        f_Asignar_Base_Conceptos();"
                                  onkeyup = "javascript:this.value=f_ValDec(this.value);
                                                        if (this.value.substr(-1) != '.') {
                                                          if (document.forms['frgrm']['cCliTpApl'].checked == true || document.forms['frgrm']['cCliTpagApl'].checked == true) {
                                                            document.forms['frgrm']['nAiuVlr02'].value=Math.round(parseFloat(this.value*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100)) * 100)/100;
                                                          } else if ('<?php echo $vSysStr['financiero_permitir_decimales_causaciones_automaticas'] ?>' == 'SI' && fnIsDecimalNumber(parseFloat(this.value))) {
                                                            document.forms['frgrm']['nAiuVlr02'].value=Math.round(parseFloat(this.value*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100)) * 100)/100;
                                                          }else {
                                                            document.forms['frgrm']['nAiuVlr02'].value=Math.round(parseFloat(this.value*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100)));
                                                          }
                                                          f_Asignar_Base_Conceptos();
                                                        }">
                                <input type = "hidden" name = "nAiuVlr02">
                              </td>
                              <td Class = 'name' colspan = '02'><br>
                                <input type='checkbox' name = 'cAiuApl' value = "NO" onClick = 'javascript:f_DatosAdicionales(this.name);'>
                              </td>
                              <td Class = "name" colspan = "04">&nbsp;</td>
                            <?php } ?>
                            <!-- Fin Condiciones Especiales Siaco -->
                            <!---  Nueva opcion de intermediacion de pago
                            Segun la norma.
                            Para cumplir con esta norma se desarrollo la siguiente logica en el sistema:
                            1. Se realiza el calculo de las retenciones segun la condiciones tributarias de cliente y proveedor.
                            2. Si el usuario selecciono el check de Intermediacion de Pago el sistema debe asignar a las retenciones
                               el concepto y cuenta del servicio al que se le aplican las retenciones, esto porque las retenciones no las paga
                               la agencia, sino nuestro cliente, debemos descontarlas del servicio y la cuenta por pagar, y no debemos
                               registrarlas como retenciones en el movimiento contable.
                            3. Si la variable del sistema financiero_consolidar_impuestos_causaciones_automaticas esta activa, y el usuario
                               selecciona intermediacion de pago, el sistema no tiene en cuenta la parametrizacion de la variable, ya que
                               debe calcular y mostrar las retenciones por DO y servicio, para descontar a cada servicio el valor de las retenciones.
                            4. Estas retenciones, que ahora se convierten en descuentos al servicio, se mostraran en el movimiento del DO y
                               en la factura como un ajustes al servicio, es decir se descontaran del servicio prinicipal y solo se mostrara un
                               registro en el movimiento del DO y en el paso 3 de la factura.  -->
                            <td Class = 'name' colspan = '<?php echo (f_InList($cAlfa,"SIACOSIA","TESIACOSIP","DESIACOSIP")) ? "27" : "49" ?>'>
                              <?php echo (f_InList($cAlfa,"SIACOSIA","TESIACOSIP","DESIACOSIP")) ? "<br>" : ""; ?>
                              <label><input type='checkbox' name = 'cComIntPa' value = "NO" onClick = 'javascript:f_DatosAdicionales(this.name)'>Intermediaci&oacute;n de Pago</label>

                              <?php if (!f_InList($cAlfa,"SIACOSIA","TESIACOSIP","DESIACOSIP")) { ?>
                                <input type = 'hidden' name = 'cCliTp'      value = "">
                                <input type = 'hidden' name = 'cCliTpApl'   value = "NO">
                                <input type = 'hidden' name = 'cCliTpag'    value = "">
                                <input type = 'hidden' name = 'cCliTpagApl' value = "NO">
                                <input type = 'hidden' name = 'cAiuApl'     value = "NO">
                                <input type = 'hidden' name = 'nAiuVlr01'   value = "">
                                <input type = 'hidden' name = 'nAiuVlr02'   value = "">
                              <?php } ?>
                            </td>
                          </tr>
                        </table>
                      </fieldset>
                    </td>
                  </tr>
                  <tr>
                    <td colspan = "50">
                      <fieldset>
                        <legend>Datos Tributarios del Cliente</legend>
                        <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:980">
                          <?php $cCols = f_Format_Cols(49); echo $cCols; ?>
                          <tr>
                            <td Class = "name" colspan = "03">Actividad<br>
                              <input type = 'text' Class = 'letra' style = 'width:060' name = 'cCliAecId' readonly>
                            </td>
                            <td Class = 'name' colspan = '16'>Econ&oacute;mica<br>
                              <input type = 'text' Class = 'letra' style = 'width:320' name = 'cCliAecDes' readonly>
                            </td>
                            <td Class = 'name' colspan = '05'>% Ret. CREE<br>
                              <input type = 'text' Class = 'letra' style = 'width:100;text-align:right' name = 'cCliAecRet' readonly>
                            </td>
                            <td Class = "name" colspan = "05">Regimen<br>
                              <input type = "text" Class = "letra" style = "width:100" name = "cTerReg" readonly>
                            </td>
                            <td Class = "name" colspan = "05">Rete Renta<br>
                              <input type = "text" Class = "letra" style = "width:100" name = "cTerArr" readonly>
                            </td>
                            <td Class = "name" colspan = "05">Rete CREE<br>
                              <input type = "text" Class = "letra" style = "width:100" name = "cTerAcr" readonly>
                            </td>
                            <td Class = "name" colspan = "05">Rete ICA<br>
                              <input type = "text" Class = "letra" style = "width:100" name = "cTerArrI" readonly>
                            </td>
                            <td Class = "name" colspan = "05">Proveedor CI<br>
                              <input type = "text" Class = "letra" style = "width:100" name = "cTerPci" readonly>
                            </td>
                          </tr>
                        </table>
                      </fieldset>
                    </td>
                  </tr>
                  <tr>
                    <td colspan = "50">
                      <fieldset>
                        <legend>Datos Tributarios del Proveedor</legend>
                        <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:980">
                          <?php $cCols = f_Format_Cols(49); echo $cCols; ?>
                          <tr>
                            <td Class = "name" colspan = "03">Actividad<br>
                              <input type = 'text' Class = 'letra' style = 'width:60' name = 'cProAecId' readonly>
                            </td>
                            <td Class = 'name' colspan = '06'>Econ&oacute;mica<br>
                              <input type = 'text' Class = 'letra' style = 'width:120' name = 'cProAecDes' readonly>
                            </td>
                            <td Class = 'name' colspan = '04'>% Ret. CREE<br>
                              <input type = 'text' Class = 'letra' style = 'width:080;text-align:right' name = 'cProAecRet' readonly>
                            </td>
                            <td Class = "name" colspan = "05">Regimen<br>
                              <input type = "text" Class = "letra" style = "width:100" name = "cProReg" readonly>
                            </td>
                            <td Class = 'name' colspan = '05'>Reg. Simple Trib<br>
                                <input type = 'text' Class = 'letra' style = 'width:100' name = 'cProRegSt' readonly>
                              </td>
                            <td Class = "name" colspan = "05">Auto Rete Renta<br>
                              <input type = "text" Class = "letra" style = "width:100" name = "cProArAre" readonly>
                            </td>
                            <td Class = "name" colspan = "05">Auto Rete CREE<br>
                              <input type = "text" Class = "letra" style = "width:100" name = "cProArAcr" readonly>
                            </td>
                            <td Class = "name" colspan = "05">Auto Rete IVA<br>
                              <input type = "text" Class = "letra" style = "width:100" name = "cProArIva" readonly>
                            </td>
                            <td Class = "name" colspan = "05">Auto Rete ICA<br>
                              <input type = "text" Class = "letra" style = "width:100" name = "cProArIca" readonly>
                            </td>
                            <td Class = "name" colspan = "06">Sucursales<br>
                              <select Class = "letra" style = "width:120" name = "cProSucIca" id = "cProSucIca" onchange="javascript:f_Borrar_Tabla('Grid_Comprobante');">
                                <option value="">[ SELECCIONE ]</option>
                                <?php
                                //Buscando Sucursales
                                $qSuc  = "SELECT sucidxxx, sucdesxx ";
                                $qSuc .= "FROM $cAlfa.fpar0008 ";
                                $qSuc .= "WHERE regestxx = \"ACTIVO\" ";
                                $qSuc .= "ORDER BY sucdesxx";
                                $xSuc  = f_MySql("SELECT","",$qSuc,$xConexion01,"");
                                while ($xRS = mysql_fetch_array($xSuc)) { ?>
                                  <option value="<?php echo $xRS['sucidxxx'] ?>"><?php echo $xRS['sucdesxx'] ?></option>
                                <?php }  ?>
                              </select>
                            </td>
                          </tr>
                        </table>
                      </fieldset>
                    </td>
                  </tr>
                </table>
                <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:1000">
                  <?php $cCols = f_Format_Cols(50); echo $cCols; ?>
                  <tr>
                    <td colspan = "50">
                      <fieldset>
                        <legend>Seleccionar DOs</legend>
                        <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:980">
                          <?php $cCols = f_Format_Cols(49); echo $cCols; ?>
                          <tr>
                            <td colspan = "2"  class = "name"><center>Suc.</center></td>
                            <td colspan = "8"  class = "name"><center>DO</center></td>
                            <td colspan = "2"  class = "name"><center>Suf.</center></td>
                            <td colspan = "5"  class = "name"><center>Nit</center></td>
                            <td colspan = "19"  class = "name"><center>Cliente</center></td>
                            <td colspan = "4"  class = "name"><center>Fecha</center></td>
                            <td colspan = "3"  class = "name"><center>CC</center></td>
                            <td colspan = "5"  class = "name"><center>% Prorrateo</center></td>
                            <td colspan = "1"  class = "name" align = "right"></td>
                          </tr>
                        </table>
                        <center>
                          <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:980" id = "Grid_Dos">
                          </table>
                        </center>
                      </fieldset>
                    </td>
                  </tr>
                  <tr>
                    <td colspan = "50">
                      <fieldset>
                        <legend>Seleccionar Conceptos Contables</legend>
                        <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:980">
                          <?php $cCols = f_Format_Cols(49); echo $cCols; ?>
                          <?php if (f_InList($cAlfa,"SIACOSIA","TESIACOSIP","DESIACOSIP")) { ?>
                            <tr>
                              <td colspan = "5"  class = "name"><center>Concepto</center></td>
                              <td colspan = "14" class = "name"><center>Descripci&oacute;n</center></td>
                              <td colspan = "3"  class = "name"><center>Suc.</center></td>
                              <td colspan = "8"  class = "name"><center>DO</center></td>
                              <td colspan = "2"  class = "name"><center>Suf.</center></td>
                              <td colspan = "4"  class = "name"><center>Base A.I.U</center></td>
                              <td colspan = "4"  class = "name"><center>Base Iva</center></td>
                              <td colspan = "4"  class = "name"><center>Valor Iva</center></td>
                              <td colspan = "4"  class = "name"><center>Valor</center></td>
                              <td colspan = "1"  class = "name" align = "right"></td>
                            </tr>
                          <?php } else {?>
                            <tr>
                              <td colspan = "5"  class = "name"><center>Concepto</center></td>
                              <td colspan = "15" class = "name"><center>Descripci&oacute;n</center></td>
                              <td colspan = "3"  class = "name"><center>Suc.</center></td>
                              <td colspan = "8"  class = "name"><center>DO</center></td>
                              <td colspan = "2"  class = "name"><center>Suf.</center></td>
                              <td colspan = "5"  class = "name"><center>Base Iva</center></td>
                              <td colspan = "5"  class = "name"><center>Valor Iva</center></td>
                              <td colspan = "5"  class = "name"><center>Valor</center></td>
                              <td colspan = "1"  class = "name" align = "right"></td>
                            </tr>
                          <?php } ?>
                        </table>
                        <center>
                          <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:980" id = "Grid_Conceptos">
                          </table>
                        </center>
                      </fieldset>
                    </td>
                  </tr>
                  <tr><td colspan = "50"><hr></td></tr>
                </table>
                <center>
                  <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:980">
                    <?php $cCols = f_Format_Cols(49); echo $cCols; ?>
                    <tr>
                      <td colspan = "2"  class = "name"><center>Sq</center></td>
                      <td colspan = "4"  class = "name"><center>Cto.</center></td>
                      <td colspan = "5"  class = "name"><center>Descripcion</center></td>
                      <td colspan = "5"  class = "name"><center>Observacion</center></td>
                      <td colspan = "8"  class = "name"><center>Doc. Cruce</center></td>
                      <td colspan = "3"  class = "name"><center>CC</center></td>
                      <td colspan = "3"  class = "name"><center>SC</center></td>
                      <td colspan = "3"  class = "name"><center>Base Ret.</center></td>
                      <td colspan = "3"  class = "name"><center>Base Iva</center></td>
                      <td colspan = "3"  class = "name"><center>Vlr. Iva</center></td>
                      <td colspan = "4"  class = "name"><center>Valor Local</center></td>
                      <td colspan = "4"  class = "name"><center>Valor NIIF</center></td>
                      <td colspan = "1"  class = "name"><center>M</center></td>
                      <td colspan = "1"  class = "name" align = "right"></td>
                    </tr>
                  </table>
                  <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:980" id = "Grid_Comprobante"></table>
                </center>
                <br>
                <center>
                  <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:980">
                    <?php $cCols = f_Format_Cols(49); echo $cCols; ?>
                    <tr>
                      <td Class = "name" colspan = "5">Estado<br>
                        <input type = "text" Class = "letra" style = "width:100;text-align:center"
                          name = "cRegEst" value = "ACTIVO" readonly>
                      </td>
                      <td Class = "name" colspan = "5">Modificado<br>
                        <input type = "text" Class = "letra" style = "width:100;text-align:center"
                          name = "dRegFMod" value = "<?php echo date('Y-m-d') ?>" readonly>
                      </td>
                      <td Class = "name" colspan = "29"></td>
                      <td Class = "name" colspan = "5">Debitos<br>
                        <input type = "text" Class = "letra" style = "width:100;text-align:right"
                          name = "nDebitos" value = "0" readonly>
                      </td>
                      <td Class = "name" colspan = "5">Creditos<br>
                        <input type = "text" Class = "letra" style = "width:100;text-align:right"
                          name = "nCreditos" value = "0" readonly>
                      </td>
                    </tr>
                    <tr>
                      <td Class = "name" colspan = "39"></td>
                      <td Class = "name" colspan = "5">Diferencia D-C</td>
                      <td Class = "name" colspan = "5">
                        <input type = "text" Class = "letra" style = "width:100;text-align:right;color:#FF0000;font-weight:bold"
                          name = "nDiferencia" value = "0" readonly>
                      </td>
                    </tr>
                  </table>
                </center>
              </fieldset>
              <center>
                <table border="0" cellpadding="0" cellspacing="0" width="980">
                  <tr height="21">
                    <?php switch ($_COOKIE['kModo']) {
                      case "VER": ?>
                        <td width="889" height="21"></td>
                        <td width="91" height="21" Class="name">
                          <input type="button" name="Btn_Salir" value="Salir" style = "width:95;height:21;" onClick = "javascript:f_Retorna()">
                        </td>
                      <?php break;
                      default: ?>
                        <td width="798" height="21"></td>
                        <td width="91" height="21" Class="name" >
                          <input type="button" name="Btn_Guardar" value="Guardar" style = "width:95;height:21;"
                            onclick = "javascript:f_Enviar();"></td>
                        <td width="91" height="21" Class="name" >
                          <input type="button" name="Btn_Salir" value="Salir" style = "width:95;height:21;"
                            onClick = "javascript:f_Retorna()">
                        </td>
                      <?php break;
                    } ?>
                  </tr>
                </table>
              </center>
            </form>
          </td>
        </tr>
      </table>
    </center>

    <!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
    <?php switch ($_COOKIE['kModo']) {
      case "NUEVO": ?>
        <script languaje = "javascript">
          f_Add_New_Row_Comprobante();
          f_Add_New_Row_Dos();
          f_Add_New_Row_Conceptos();
          f_Activar_Prorrateo();
          f_Links('cComCod','VALID');
        </script>
      <?php break;
      case "ANTERIOR": ?>
        <script languaje = "javascript">
          f_Add_New_Row_Comprobante();
          f_Add_New_Row_Dos();
          f_Add_New_Row_Conceptos();
          f_Activar_Prorrateo();
          f_Links('cComCod','VALID');
        </script>
      <?php break;
      case "BORRAR":
        f_Carga_Data($gComId,$gComCod,$gComCsc,$gComCsc2,$gComFec); ?>
        <script languaje = "javascript">
          document.forms['frgrm']['dComFec_Ant'].value = document.forms['frgrm']['dComFec'].value;

          //Si es Manual se habilita el Consecutivo
          if (document.forms['frgrm']['cComTco'].value == "MANUAL") {
            document.forms['frgrm']['cComCsc'].readOnly = false;
          } else {
            document.forms['frgrm']['cComCsc'].readOnly = true;
            document.forms['frgrm']['cComCsc'].onfocus  = "";
            document.forms['frgrm']['cComCsc'].onblur   = "";
          }

          document.forms['frgrm']['cComCsc2'].readOnly = true;
          document.forms['frgrm']['cComCsc2'].onfocus  = "";
          document.forms['frgrm']['cComCsc2'].onblur   = "";

          document.forms['frgrm']['cComCsc3'].readOnly = true;
          document.forms['frgrm']['cComCsc3'].onfocus  = "";
          document.forms['frgrm']['cComCsc3'].onblur   = "";

          document.forms['frgrm'].target="fmpro";
          document.forms['frgrm'].action="frcpadel.php";
          document.forms['frgrm'].submit();
          document.forms['frgrm']['Btn_Guardar'].disabled = true;
          document.forms['frgrm'].action="frcpagra.php";
        </script>
      <?php break;
      case "VERIFICAR":
        f_Carga_Data($gComId,$gComCod,$gComCsc,$gComCsc2,$gComFec); ?>
        <script languaje = "javascript">
          document.forms['frgrm']['dComFec_Ant'].value = document.forms['frgrm']['dComFec'].value;

          //Si es Manual se habilita el Consecutivo
          if (document.forms['frgrm']['cComTco'].value == "MANUAL") {
            document.forms['frgrm']['cComCsc'].readOnly = false;
          } else {
            document.forms['frgrm']['cComCsc'].readOnly = true;
            document.forms['frgrm']['cComCsc'].onfocus  = "";
            document.forms['frgrm']['cComCsc'].onblur   = "";
          }

          document.forms['frgrm']['cComCsc2'].readOnly = true;
          document.forms['frgrm']['cComCsc2'].onfocus  = "";
          document.forms['frgrm']['cComCsc2'].onblur   = "";

          document.forms['frgrm']['cComCsc3'].readOnly = true;
          document.forms['frgrm']['cComCsc3'].onfocus  = "";
          document.forms['frgrm']['cComCsc3'].onblur   = "";

          for (i=0;i<parent.fmwork.document.forms['frgrm']['nSecuencia'].value;i++) {
            if (parent.fmwork.document.forms['frgrm']['cComIdC' +(i+1)].value == parent.fmwork.document.forms['frgrm']['cComId'].value  &&
                parent.fmwork.document.forms['frgrm']['cComCodC'+(i+1)].value == parent.fmwork.document.forms['frgrm']['cComCod'].value &&
                parent.fmwork.document.forms['frgrm']['cComCscC'+(i+1)].value == parent.fmwork.document.forms['frgrm']['cComCsc'].value &&
                parent.fmwork.document.forms['frgrm']['cComSeqC'+(i+1)].value == "001") {
                //parent.fmwork.document.forms['frgrm']['cCcoId'  +(i+1)].value == parent.fmwork.document.forms['frgrm']['cCcoId'].value) {
              parent.fmwork.document.forms['frgrm']['cComIdC' +(i+1)].value = "";
              parent.fmwork.document.forms['frgrm']['cComCodC'+(i+1)].value = "";
              parent.fmwork.document.forms['frgrm']['cComCscC'+(i+1)].value = "";
              parent.fmwork.document.forms['frgrm']['cComSeqC'+(i+1)].value = "";
              //parent.fmwork.document.forms['frgrm']['cCcoId'  +(i+1)].value = "";
            }
          }
        </script>
      <?php break;
      case "VER":
        f_Carga_Data($gComId,$gComCod,$gComCsc,$gComCsc2,$gComFec); ?>
        <script languaje = "javascript">
          for (x=0;x<document.forms['frgrm'].elements.length;x++) {
            document.forms['frgrm'].elements[x].readOnly = true;
            document.forms['frgrm'].elements[x].onfocus  = "";
            document.forms['frgrm'].elements[x].onblur   = "";
            document.forms['frgrm'].elements[x].disabled = true;
          }
          document.forms['frgrm']['Btn_Salir'].disabled = false;
          document.getElementById('id_href_cComCod').href  = "javascript:alert('Opcion no Permitida en este Modo, Verifique.')";
          document.getElementById('id_href_cCcoId').href   = "javascript:alert('Opcion no Permitida en este Modo, Verifique.')";
          document.getElementById('id_href_cSccId').href   = "javascript:alert('Opcion no Permitida en este Modo, Verifique.')";
          document.getElementById('id_href_dComFec').href  = "javascript:alert('Opcion no Permitida en este Modo, Verifique.')";
          document.getElementById('id_href_dComVen').href  = "javascript:alert('Opcion no Permitida en este Modo, Verifique.')";
          document.getElementById('id_href_cTerId').href   = "javascript:alert('Opcion no Permitida en este Modo, Verifique.')";
          document.getElementById('id_href_cTerIdB').href  = "javascript:alert('Opcion no Permitida en este Modo, Verifique.')";
        </script>
      <?php break;
    } ?>

    <?php function f_Carga_Data($xComId,$xComCod,$xComCsc,$xComCsc2,$xComFec) {
      global $xConexion01; global $cAlfa; global $vSysStr;

      // Cantidad de decimales permitidos en la causacion
      $nDec = 0;
      if ($vSysStr['financiero_permitir_decimales_causaciones_automaticas'] == "SI") {
        $nDec = 2;
      }

      //$xAno = date("Y");
      $xAno = substr($xComFec,0,4);

      // Traigo los datos de la cabecera.
      $qConCab  = "SELECT * ";
      $qConCab .= "FROM $cAlfa.fcoc$xAno ";
      $qConCab .= "WHERE ";
      $qConCab .= "comidxxx = \"$xComId\"  AND ";
      $qConCab .= "comcodxx = \"$xComCod\" AND ";
      $qConCab .= "comcscxx = \"$xComCsc\" AND ";
      $qConCab .= "comcsc2x = \"$xComCsc2\" LIMIT 0,1";
      $xConCab  = f_MySql("SELECT","",$qConCab,$xConexion01,"");
      //f_Mensaje(__FILE__,__LINE__,$qConCab." ~ ".mysql_num_rows($xConCab));
      $vConCab  = mysql_fetch_array($xConCab);

      //Trayendo datos de sucursal Auto Rete ICA y tipo de prorrateo
      $cProSucIca = ""; $cTipPro = "";
      if ($vConCab['comobs2x'] != '') {
        $mAux = array();
        $mAux = explode("~",$vConCab['comobs2x']);
        $cProSucIca = $mAux[1];
        $cTipPro    = $mAux[2];
        $cTasPac    = $mAux[3];
        $cTasaPago  = $mAux[4];
        $cBaseAiu   = $mAux[5];
        $cIvaAiu    = $mAux[6];
      }

      // Traigo los datos del detalle.
      $qConDet  = "SELECT * ";
      $qConDet .= "FROM $cAlfa.fcod$xAno ";
      $qConDet .= "WHERE ";
      $qConDet .= "comidxxx = \"$xComId\"  AND ";
      $qConDet .= "comcodxx = \"$xComCod\" AND ";
      $qConDet .= "comcscxx = \"$xComCsc\" AND ";
      $qConDet .= "comcsc2x = \"$xComCsc2\" ORDER BY ABS(comseqxx)";
      $xConDet = f_MySql("SELECT","",$qConDet,$xConexion01,"");
      //f_Mensaje(__FILE__,__LINE__,$qConDet." ~ ".mysql_num_rows($xConDet));

      // Busco la descripcion del comprobante.
      $qComDes  = "SELECT comdesxx,comtcoxx ";
      $qComDes .= "FROM $cAlfa.fpar0117 ";
      $qComDes .= "WHERE ";
      $qComDes .= "comidxxx = \"$xComId\"  AND ";
      $qComDes .= "comcodxx = \"$xComCod\" LIMIT 0,1";
      $xComDes  = f_MySql("SELECT","",$qComDes,$xConexion01,"");
      //f_Mensaje(__FILE__,__LINE__,$qComDes." ~ ".mysql_num_rows($xComDes));
      $vComDes  = mysql_fetch_array($xComDes);
      $vConCab['comdesxx'] = ($vComDes['comdesxx'] == "") ? "COMPROBANTE SIN DESCRIPCION" : $vComDes['comdesxx'];
      $vConCab['comtcoxx'] = $vComDes['comtcoxx'];

      // Busco la descripcion del centro de costos.
      $qCcoDes  = "SELECT ccodesxx ";
      $qCcoDes .= "FROM $cAlfa.fpar0116 ";
      $qCcoDes .= "WHERE ";
      $qCcoDes .= "ccoidxxx = \"{$vConCab['ccoidxxx']}\" LIMIT 0,1";
      $xCcoDes  = f_MySql("SELECT","",$qCcoDes,$xConexion01,"");
      $vCcoDes  = mysql_fetch_array($xCcoDes);
      $vConCab['ccodesxx'] = ($vCcoDes['ccodesxx'] == "") ? "CENTRO DE COSTO SIN DESCRIPCION" : $vCcoDes['ccodesxx'];

      // Busco el nombre del tercero cliente.
      $qCliNom  = "SELECT * ";
      $qCliNom .= "FROM $cAlfa.SIAI0150 ";
      $qCliNom .= "WHERE ";
      $qCliNom .= "CLIIDXXX = \"{$vConCab['teridxxx']}\" LIMIT 0,1";
      $xCliNom  = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
      $vCliNom  = mysql_fetch_array($xCliNom);
      $vConCab['ternomxx'] = ($vCliNom['CLINOMXX'] != "") ? $vCliNom['CLINOMXX'] : trim($vCliNom['CLIAPE1X']." ".$vCliNom['CLIAPE2X']." ".$vCliNom['CLINOM1X']." ".$vCliNom['CLINOM2X']);
      $vConCab['tertpxxx'] = (($vCliNom['CLITPXXX']+0) > 0) ? ($vCliNom['CLITPXXX']+0) : "";

      //Inicio Busqueda de Datos Tributarios del Cliente //
      //Regimen Cliente
      $cTerReg = "";
      if($vCliNom['CLIGCXXX'] == 'SI') {
        $cTerReg = "G. CONTRIBUYENTE";
      } elseif($vCliNom['CLINRPXX'] == 'SI') {
        $cTerReg = "NO RESIDENTE";
      } elseif($vCliNom['CLIRECOM'] == 'SI') {
        $cTerReg = "COMUN";
      } elseif($vCliNom['CLIRESIM'] == 'SI') {
        $cTerReg = "SIMPLIFICADO";
      }
      $vConCab['TERREGCX']=$cTerReg;

      if ($vCliNom['CLIARRXX'] == 'SI') { $vConCab['CLIARRXX']="SI"; } else {$vConCab['CLIARRXX']="NO"; } ;
      if ($vCliNom['CLIARRIX'] == 'SI') { $vConCab['CLIARRIX']="SI"; } else {$vConCab['CLIARRIX']="NO"; } ;
      if ($vCliNom['CLIPCIXX'] == 'SI') { $vConCab['CLIPCIXX']="SI"; } else {$vConCab['CLIPCIXX']="NO"; } ;

      //Actividad economica del Cliente
      $qCliAec = "SELECT * ";
      $qCliAec.= "FROM $cAlfa.SIAI0101 ";
      $qCliAec.= "WHERE AECIDXXX =\"{$vCliNom['AECIDXXX']}\" LIMIT 0,1";
      $xCliAec = f_MySql("SELECT","",$qCliAec,$xConexion01,"");
      $xRCA = mysql_fetch_array($xCliAec);

      //Fin Busqueda de Datos Tributarios del Cliente //

      // Busco el nombre del tercero proveedor.
      $qProNom  = "SELECT * ";
      $qProNom .= "FROM $cAlfa.SIAI0150 ";
      $qProNom .= "WHERE ";
      $qProNom .= "CLIIDXXX = \"{$vConCab['terid2xx']}\" LIMIT 0,1";
      $xProNom  = f_MySql("SELECT","",$qProNom,$xConexion01,"");
      $vProNom  = mysql_fetch_array($xProNom);
      $vConCab['ternom2x'] = ($vProNom['CLINOMXX'] != "") ? $vProNom['CLINOMXX'] : trim($vProNom['CLIAPE1X']." ".$vProNom['CLIAPE2X']." ".$vProNom['CLINOM1X']." ".$vProNom['CLINOM2X']);

      //Inicio Busqueda de Datos Tributarios del Proveedor //
      //Regimen Cliente
      $cProReg = "";
      if($vProNom['CLIGCXXX'] == 'SI') {
        $cProReg = "G. CONTRIBUYENTE";
      } elseif($vProNom['CLINRPXX'] == 'SI') {
        $cProReg = "NO RESIDENTE";
      } elseif($vProNom['CLIRECOM'] == 'SI') {
        $cProReg = "COMUN";
      } elseif($vProNom['CLIRESIM'] == 'SI') {
        $cProReg = "SIMPLIFICADO";
      }
      $vConCab['TERREGPX']=$cProReg;

      if ($vProNom['CLIARRXX'] == 'SI') { $vConCab['CLIREGST']="SI"; } else {$vConCab['CLIREGST']="NO"; } ;
      if ($vProNom['CLIARARE'] == 'SI') { $vConCab['CLIARARE']="SI"; } else {$vConCab['CLIARARE']="NO"; } ;
      if ($vProNom['CLIARAIV'] == 'SI') { $vConCab['CLIARAIV']="SI"; } else {$vConCab['CLIARAIV']="NO"; } ;
      if ($vProNom['CLIARAIC'] == 'SI') { $vConCab['CLIARAIC']="SI"; } else {$vConCab['CLIARAIC']="NO"; } ;
      if ($vProNom['CLIARACR'] == 'SI') { $vConCab['CLIARACR']="SI"; } else {$vConCab['CLIARACR']="NO"; } ;

      //Actividad economica del Cliente
      $qProAec = "SELECT * ";
      $qProAec.= "FROM $cAlfa.SIAI0101 ";
      $qProAec.= "WHERE AECIDXXX =\"{$vProNom['AECIDXXX']}\" LIMIT 0,1";
      $xProAec = f_MySql("SELECT","",$qProAec,$xConexion01,"");
      $xRPA = mysql_fetch_array($xProAec);
      //Fin Busqueda de Datos Tributarios del Cliente //
      ?>
      <script language = "javascript">
        document.forms['frgrm']['cComTco'].value     = "<?php echo $vConCab['comtcoxx'] ?>";
        document.forms['frgrm']['cComId'].value      = "<?php echo $vConCab['comidxxx'] ?>";
        document.forms['frgrm']['cComCod'].value     = "<?php echo $vConCab['comcodxx'] ?>";
        document.forms['frgrm']['cComDes'].value     = "<?php echo $vConCab['comdesxx'] ?>";
        document.forms['frgrm']['cCcoId'].value      = "<?php echo $vConCab['ccoidxxx'] ?>";
        document.forms['frgrm']['cSccId'].value      = "<?php echo $vConCab['sccidxxx'] ?>";
        document.forms['frgrm']['dComFec'].value     = "<?php echo $vConCab['comfecxx'] ?>";
        document.forms['frgrm']['cComCsc'].value     = "<?php echo $vConCab['comcscxx'] ?>";
        document.forms['frgrm']['cComCsc2'].value    = "<?php echo $vConCab['comcsc2x'] ?>";
        document.forms['frgrm']['cComCsc3'].value    = "<?php echo $vConCab['comcsc3x'] ?>";
        document.forms['frgrm']['dComVen'].value     = "<?php echo $vConCab['comfecve'] ?>";
        document.forms['frgrm']['cTerTip'].value     = "<?php echo $vConCab['tertipxx'] ?>";
        document.forms['frgrm']['cTerId'].value      = "<?php echo $vConCab['teridxxx'] ?>";
        document.forms['frgrm']['cTerDV'].value      = "<?php echo f_Digito_Verificacion($vConCab['teridxxx']) ?>";
        document.forms['frgrm']['cTerNom'].value     = "<?php echo $vConCab['ternomxx'] ?>";
        document.forms['frgrm']['cTerTipB'].value    = "<?php echo $vConCab['tertip2x'] ?>";
        document.forms['frgrm']['cTerIdB'].value     = "<?php echo $vConCab['terid2xx'] ?>";
        document.forms['frgrm']['cTerDVB'].value     = "<?php echo f_Digito_Verificacion($vConCab['terid2xx']) ?>";
        document.forms['frgrm']['cTerNomB'].value    = "<?php echo $vConCab['ternom2x'] ?>";
        document.forms['frgrm']['cTerReg'].value     = "<?php echo $vConCab['TERREGCX'] ?>";
        document.forms['frgrm']['cTerArr'].value     = "<?php echo $vConCab['CLIARRXX'] ?>";
        document.forms['frgrm']['cTerAcr'].value     = "<?php echo $vConCab['CLIARCRX'] ?>";
        document.forms['frgrm']['cTerArrI'].value    = "<?php echo $vConCab['CLIARRIX'] ?>";
        document.forms['frgrm']['cTerPci'].value     = "<?php echo $vConCab['CLIPCIXX'] ?>";
        document.forms['frgrm']['cCliAecId'].value   = '<?php echo $vCliNom['AECIDXXX'] ?>';
        document.forms['frgrm']['cCliAecDes'].value  = '<?php echo $xRCA['AECDESXX']    ?>';
        document.forms['frgrm']['cCliAecRet'].value  = '<?php echo $xRCA['AECRETXX']+0  ?>';
        document.forms['frgrm']['cProReg'].value     = "<?php echo $vConCab['TERREGPX'] ?>";
        document.forms['frgrm']['cProRegSt'].value   = "<?php echo $vConCab['CLIREGST'] ?>";
        document.forms['frgrm']['cProArAre'].value   = "<?php echo $vConCab['CLIARARE'] ?>";
        document.forms['frgrm']['cProArAcr'].value   = "<?php echo $vConCab['CLIARACR'] ?>";
        document.forms['frgrm']['cProArIva'].value   = "<?php echo $vConCab['CLIARAIV'] ?>";
        document.forms['frgrm']['cProArIca'].value   = "<?php echo $vConCab['CLIARAIC'] ?>";
        document.forms['frgrm']['cProAecId'].value   = "<?php echo $vProNom['AECIDXXX'] ?>";
        document.forms['frgrm']['cProAecDes'].value  = "<?php echo $xRPA['AECDESXX']    ?>";
        document.forms['frgrm']['cProAecRet'].value  = "<?php echo $xRPA['AECRETXX']+0  ?>";
        document.forms['frgrm']['dComBpo'].value     = "<?php echo $vConCab['combpoxx'] ?>";
        
        //Tasa Pactada
        var chCliTpApl = "";
        document.forms['frgrm']['cCliTp'].value      = "<?php echo ($cTasPac != "") ? $cTasPac : $vConCab['tertpxxx'] ?>";
        if (parseFloat("<?php echo ($cTasPac+0) ?>") > 0) {
          chCliTpApl = "SI";
          document.forms['frgrm']['cCliTpApl'].value    = "SI";
          document.forms['frgrm']['cCliTpApl'].checked  = true;
          document.forms['frgrm']['cCliTpApl'].disabled = false;
          //La tasa pactada y la tasa de pago son excluyentes
          document.forms['frgrm']['cCliTpagApl'].value    = "NO";
          document.forms['frgrm']['cCliTpagApl'].checked  = false;
          document.forms['frgrm']['cCliTpag'].value       = "";
        } else {
          document.forms['frgrm']['cCliTpApl'].value    = "NO";
          document.forms['frgrm']['cCliTpApl'].checked  = false;
          if (parseFloat("<?php echo ($vConCab['tertpxxx']+0) ?>") > 0) { //Si el cliente tiene parametrizada tasa pactada
            document.forms['frgrm']['cCliTpApl'].disabled = false;
          } else {
            document.forms['frgrm']['cCliTpApl'].disabled = true;
          }
        }

        //Tasa Pago
        //La tasa pactada y la tasa de pago son excluyentes
        if (chCliTpApl != "SI") {
          document.forms['frgrm']['cCliTpag'].value      = "<?php echo ($cTasaPago != "") ? $cTasaPago : "" ?>";
          if (parseFloat("<?php echo ($cTasaPago+0) ?>") > 0) {
            document.forms['frgrm']['cCliTpagApl'].value    = "SI";
            document.forms['frgrm']['cCliTpagApl'].checked  = true;
            //La tasa pactada y la tasa de pago son excluyentes
            document.forms['frgrm']['cCliTpApl'].value    = "NO";
            document.forms['frgrm']['cCliTpApl'].checked  = false;
          } else {
            document.forms['frgrm']['cCliTpagApl'].value    = "NO";
            document.forms['frgrm']['cCliTpagApl'].checked  = false;
          }
        }

        if (document.forms['frgrm']['cCliTpApl'].checked == true || document.forms['frgrm']['cCliTpagApl'].checked == true) {
          //Convierto a Dolares con la tasa del Dia
          document.forms['frgrm']['nComVlr01'].value   = f_RoundValor(("<?php echo $vConCab['comvlr01']/$vConCab['tcatasax'] ?>") * 100) / 100;
          document.forms['frgrm']['nComVlr02'].value   = f_RoundValor(("<?php echo $vConCab['comvlr02']/$vConCab['tcatasax'] ?>") * 100) / 100;
        } else {
          document.forms['frgrm']['nComVlr01'].value   = f_RoundValor("<?php echo $vConCab['comvlr01'] ?>");
          document.forms['frgrm']['nComVlr02'].value   = f_RoundValor("<?php echo $vConCab['comvlr02'] ?>");
        }

        document.forms['frgrm']['nAiuVlr01'].value      = "<?php echo ($cBaseAiu != "") ? $cBaseAiu : "" ?>";
        document.forms['frgrm']['nAiuVlr02'].value      = "<?php echo ($cIvaAiu  != "") ? $cIvaAiu : "" ?>";
        if (parseFloat("<?php echo ($cBaseAiu+0) ?>") > 0) {
          document.forms['frgrm']['cAiuApl'].value      = "SI";
          document.forms['frgrm']['cAiuApl'].checked    = true;
          document.forms['frgrm']['nAiuVlr01'].readOnly = false;
        } else {
          document.forms['frgrm']['cAiuApl'].value      = "NO";
          document.forms['frgrm']['cAiuApl'].checked    = false;
          document.forms['frgrm']['nAiuVlr01'].readOnly = true;
        }

        document.forms['frgrm']['cComObs'].value     = "<?php echo $vConCab['comobsxx'] ?>";
        document.forms['frgrm']['cProSucIca'].value  = "<?php echo $cProSucIca ?>";
        document.forms['frgrm']['cTipPro'].value     = "<?php echo $cTipPro ?>";
        document.forms['frgrm']['nTasaCambio'].value = "<?php echo round($vConCab['tcatasax'],2) ?>";
        document.forms['frgrm']['cComLot'].value     = "<?php echo $vConCab['comlotxx'] ?>";
        document.forms['frgrm']['cRegEst'].value     = "<?php echo $vConCab['regestxx'] ?>";
        document.forms['frgrm']['dRegFMod'].value    = "<?php echo $vConCab['regfmodx'] ?>";
        
        //Intermediacion de Pago
        document.forms['frgrm']['cComIntPa'].value   = ("<?php echo $vConCab['comintpa'] ?>" == "SI") ? "SI" : "NO";
        document.forms['frgrm']['cComIntPa'].checked = ("<?php echo $vConCab['comintpa'] ?>" == "SI") ? true : false;
      </script>
      <?php  //Trayendo datos de Grilla de DO's
      if ($vConCab['comfpxxx'] != '') {
        $mAux = array();
        $mAux = explode("|",$vConCab['comfpxxx']);
        for ($n=0; $n<count($mAux); $n++) {
          if ($mAux[$n] != '') {
            $mAux01 = array();
            $mAux01 = explode("~",$mAux[$n]);   ?>
            <script languaje = "javascript">
              f_Add_New_Row_Dos();
              var i = document.forms['frgrm']['nSecuencia_DO'].value;
              document.forms['frgrm']['cSucId_DO'   + i].value = "<?php echo $mAux01[0] ?>";
              document.forms['frgrm']['cDocId_DO'   + i].value = "<?php echo $mAux01[1] ?>";
              document.forms['frgrm']['cDocSuf_DO'  + i].value = "<?php echo $mAux01[2] ?>";
              document.forms['frgrm']['cTerId_DO'   + i].value = "<?php echo $mAux01[3] ?>";
              document.forms['frgrm']['cTerNom_DO'  + i].value = "<?php echo $mAux01[4] ?>";
              document.forms['frgrm']['cTerTip_DO'  + i].value = "<?php echo $mAux01[5] ?>";
              document.forms['frgrm']['cTerTipB_DO' + i].value = "<?php echo $mAux01[6] ?>";
              document.forms['frgrm']['cTerIdB_DO'  + i].value = "<?php echo $mAux01[7] ?>";
              document.forms['frgrm']['cDocFec_DO'  + i].value = "<?php echo $mAux01[8] ?>";
              document.forms['frgrm']['cCcoId_DO'   + i].value = "<?php echo $mAux01[9] ?>";
              document.forms['frgrm']['nVlrPro_DO'  + i].value = "<?php echo $mAux01[10] ?>";
            </script>
          <?php }
        }
      } ?>

      <?php  //Trayendo datos de Grilla de Conceptos Contables Papas
      if ($vConCab['commemod'] != '') {
        $mAux = array();
        $mAux = explode("|",$vConCab['commemod']);
        for ($n=0; $n<count($mAux); $n++) {
          if ($mAux[$n] != '') {
            $mAux01 = array();
            $mAux01 = explode("~",$mAux[$n]);
            ?>
            <script languaje = "javascript">
              f_Add_New_Row_Conceptos();
              var i = document.forms['frgrm']['nSecuencia_CCO'].value;
              document.forms['frgrm']['cCcoId_CCO'   + i].value = "<?php echo $mAux01[0] ?>";
              document.forms['frgrm']['cCcoDes_CCO'  + i].value = "<?php echo $mAux01[1] ?>";
              document.forms['frgrm']['cSucId_CCO'   + i].value = "<?php echo $mAux01[2] ?>";
              document.forms['frgrm']['cDocId_CCO'   + i].value = "<?php echo $mAux01[3] ?>";
              document.forms['frgrm']['cDocSuf_CCO'  + i].value = "<?php echo $mAux01[4] ?>";
              if (document.forms['frgrm']['cCliTpApl'].checked  == true || document.forms['frgrm']['cCliTpagApl'].checked  == true) {
                //Convierto a dolares
                document.forms['frgrm']['nVlrBaiu_CCO' + i].value = "<?php echo $mAux01[9]/round($vConCab['tcatasax'],2) ?>";
                document.forms['frgrm']['nVlrBase_CCO' + i].value = "<?php echo $mAux01[5]/round($vConCab['tcatasax'],2) ?>";
                document.forms['frgrm']['nVlrIva_CCO'  + i].value = "<?php echo $mAux01[6]/round($vConCab['tcatasax'],2) ?>";
                if (document.forms['frgrm']['cCliTpApl'].checked  == true) {
                  document.forms['frgrm']['nVlr_CCO'     + i].value = "<?php echo $mAux01[7]/($cTasPac+0) ?>";
                } else {
                  document.forms['frgrm']['nVlr_CCO'     + i].value = "<?php echo $mAux01[7]/($cTasaPago+0) ?>";
                }
              } else {
                document.forms['frgrm']['nVlrBaiu_CCO' + i].value = "<?php echo $mAux01[9] ?>";
                document.forms['frgrm']['nVlrBase_CCO' + i].value = "<?php echo $mAux01[5] ?>";
                document.forms['frgrm']['nVlrIva_CCO'  + i].value = "<?php echo $mAux01[6] ?>";
                document.forms['frgrm']['nVlr_CCO'     + i].value = "<?php echo $mAux01[7] ?>";
              }
              document.forms['frgrm']['cCtoVrl02_CCO'+ i].value = "<?php echo $mAux01[8] ?>";
            </script>
          <?php }
        }
      } ?>
      <script languaje = "javascript">
        f_Activar_Prorrateo();
      </script>
      <?php // Empienzo a Pintar Grilla
      if (mysql_num_rows($xConDet) > 0) { // Pregunto si hay registros en detalle GRM01002 para pintar
        while ($xRCD = mysql_fetch_array($xConDet)) {
          // Busco la descripcion del concepto
          $qCtoCon  = "SELECT $cAlfa.fpar0119.*,$cAlfa.fpar0115.* ";
          $qCtoCon .= "FROM $cAlfa.fpar0119,$cAlfa.fpar0115 ";
          $qCtoCon .= "WHERE ";
          $qCtoCon .= "$cAlfa.fpar0119.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
          $qCtoCon .= "$cAlfa.fpar0119.ctoidxxx = \"{$xRCD['ctoidxxx']}\" AND ";
          $qCtoCon .= "$cAlfa.fpar0119.pucidxxx = \"{$xRCD['pucidxxx']}\" LIMIT 0,1";
          $xCtoCon  = f_MySql("SELECT","",$qCtoCon,$xConexion01,"");
          //f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
          if (mysql_num_rows($xCtoCon) > 0) {
            $vCtoCon = mysql_fetch_array($xCtoCon);
            $vCtoCon['comdesxx'] = $vCtoCon['ctodesxp'];
          } else {
            // Busco la descripcion del concepto en la tabla de Papas
            $qCtoCon  = "SELECT $cAlfa.fpar0121.*,$cAlfa.fpar0115.* ";
            $qCtoCon .= "FROM $cAlfa.fpar0121,$cAlfa.fpar0115 ";
            $qCtoCon .= "WHERE ";
            $qCtoCon .= "$cAlfa.fpar0121.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
            $qCtoCon .= "$cAlfa.fpar0121.ctoidxxx = \"{$xRCD['ctoidxxx']}\" AND ";
            $qCtoCon .= "$cAlfa.fpar0121.pucidxxx = \"{$xRCD['pucidxxx']}\" LIMIT 0,1";
            $xCtoCon  = f_MySql("SELECT","",$qCtoCon,$xConexion01,"");
            //f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
            if (mysql_num_rows($xCtoCon) > 0) {
              $vCtoCon = mysql_fetch_array($xCtoCon);
              $vCtoCon['comdesxx'] = $vCtoCon['ctodesxx'];
              //Si es un concepto padre debe marcarse el campo cComFac de la grilla con SI
              $xRCD['comfacxx'] = 'SI';
            } else {
              $vCtoCon['comdesxx'] = "CONCEPTO SIN DESCRIPCION";
            }
          }

          $cColor = "#000000";
          if ($vConCab['comintpa'] == "SI" && $xRCD['ctoidinp'] != "") {
            $cColor = "#0000FF";
            /**
             * Es un concepto de retencion de intermediacion de pago
             * Se deben buscar la parametrizacion de esta cuentas
             */
            $qCtoConInp  = "SELECT $cAlfa.fpar0119.*,$cAlfa.fpar0115.* ";
            $qCtoConInp .= "FROM $cAlfa.fpar0119,$cAlfa.fpar0115 ";
            $qCtoConInp .= "WHERE ";
            $qCtoConInp .= "$cAlfa.fpar0119.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
            $qCtoConInp .= "$cAlfa.fpar0119.ctoidxxx = \"{$xRCD['ctoidinp']}\" AND ";
            $qCtoConInp .= "$cAlfa.fpar0119.pucidxxx = \"{$xRCD['pucidinp']}\" LIMIT 0,1";
            $xCtoConInp  = f_MySql("SELECT","",$qCtoConInp,$xConexion01,"");
            // f_Mensaje(__FILE__,__LINE__,$qCtoConInp." ~ ".mysql_num_rows($xCtoConInp));
            if (mysql_num_rows($xCtoConInp) > 0) {
              $vCtoConInp = mysql_fetch_array($xCtoConInp);
              $vCtoConInp['comdesxx'] = $vCtoConInp['ctodesxp'];

              ##Datos del concepto Papa##
              $cPucDetInp   = $vCtoCon['pucdetxx'];
              $cPucTerInp   = $vCtoCon['pucterxx'];
              $nPucBRetInp  = $vCtoCon['pucbaret'];
              $nPucRetInp   = $vCtoCon['pucretxx'];
              $cPucNatInp   = $vCtoCon['pucnatxx'];
              $cPucInvInp   = $vCtoCon['pucinvxx'];
              $cPucCcoInp   = $vCtoCon['puccccxx'];
              $cPucDoScInp  = $vCtoCon['pucdoscc'];
              $cComVlr1Inp  = $vCtoCon['ctovlr01'];
              $cComVlr2Inp  = $vCtoCon['ctovlr02'];

              $vCtoCon['comdesxx'] = $vCtoCon['comdesxx']." ({$vCtoConInp['comdesxx']})";
              $vCtoCon['pucdetxx'] = $vCtoConInp['pucdetxx'];
              $vCtoCon['pucterxx'] = $vCtoConInp['pucterxx'];
              $vCtoCon['pucbaret'] = $vCtoConInp['pucbaret'];
              $vCtoCon['pucretxx'] = $vCtoConInp['pucretxx'];
              $vCtoCon['pucnatxx'] = $vCtoConInp['pucnatxx'];
              $vCtoCon['pucinvxx'] = $vCtoConInp['pucinvxx'];
              $vCtoCon['puccccxx'] = $vCtoConInp['puccccxx'];
              $vCtoCon['pucdoscc'] = $vCtoConInp['pucdoscc'];
              $vCtoCon['ctovlr01'] = $vCtoConInp['ctovlr01'];
              $vCtoCon['ctovlr02'] = $vCtoConInp['ctovlr02'];
              $xRCD['comvlr01']    = $xRCD['comvlr03'];
            }
          }
          ?>
          <script languaje = "javascript">
            f_Add_New_Row_Comprobante();
            document.forms['frgrm']['cComSeq'+document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comseqxx'] ?>";
            document.forms['frgrm']['cCtoId' +document.forms['frgrm']['nSecuencia'].value].id    = "<?php echo $xRCD['ctoidxxx'] ?>";
            document.forms['frgrm']['cCtoId' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['ctoidxxx'] ?>";
            document.forms['frgrm']['cCtoDes'+document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vCtoCon['comdesxx'] ?>";
            if ("<?php echo $vCtoCon['pucinvxx'] ?>" == "I") {
              document.forms['frgrm']['cInvLin'+document.forms['frgrm']['nSecuencia'].value].value = "<?php echo substr($xRCD['proidxxx'],0,3) ?>";
              document.forms['frgrm']['cInvGru'+document.forms['frgrm']['nSecuencia'].value].value = "<?php echo substr($xRCD['proidxxx'],3,4) ?>";
              document.forms['frgrm']['cInvPro'+document.forms['frgrm']['nSecuencia'].value].value = "<?php echo substr($xRCD['proidxxx'],7,6) ?>";
              document.forms['frgrm']['nInvCan'+document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comcanxx'] ?>";
              document.forms['frgrm']['nInvCos'+document.forms['frgrm']['nSecuencia'].value].value = "<?php echo ($xRCD['comvlrxx']/$xRCD['comcanxx']) ?>";
              document.forms['frgrm']['cInvBod'+document.forms['frgrm']['nSecuencia'].value].value = "";
              document.forms['frgrm']['cInvUbi'+document.forms['frgrm']['nSecuencia'].value].value = "";
            }
            document.forms['frgrm']['cComObs' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comobsxx'] ?>";
            document.forms['frgrm']['cComIdC' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comidcxx'] ?>";
            document.forms['frgrm']['cComCodC'+document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comcodcx'] ?>";
            document.forms['frgrm']['cComCscC'+document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comcsccx'] ?>";
            document.forms['frgrm']['cComSeqC'+document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comseqcx'] ?>";
            document.forms['frgrm']['cComCscC'+document.forms['frgrm']['nSecuencia'].value].id    = "<?php echo $xRCD['comidcxx']."~".$xRCD['comcodcx']."~".$xRCD['comcsccx']."~".$xRCD['comseqcx'] ?>";
            document.forms['frgrm']['cCcoId'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['ccoidxxx'] ?>";
            document.forms['frgrm']['cSccId'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['sccidxxx'] ?>";
            document.forms['frgrm']['cComCtoC'+document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comctocx'] ?>";

            if ("<?php echo $vCtoCon['ctovlr01'] ?>" == "SI" || "<?php echo $vCtoCon['ctovlr02'] ?>" == "SI") {
              if ("<?php echo $vCtoCon['pucretxx'] ?>" > 0) {
                document.forms['frgrm']['nComBRet'+document.forms['frgrm']['nSecuencia'].value].value = "<?php if($xRCD['comvlr01'] > 0){echo round($xRCD['comvlr01'],$nDec);}else{echo "";} ?>";
                document.forms['frgrm']['nComBRet'+document.forms['frgrm']['nSecuencia'].value].disabled = false;
              } else {
                document.forms['frgrm']['nComBIva'+document.forms['frgrm']['nSecuencia'].value].value = "<?php if($xRCD['comvlr01'] > 0){echo round($xRCD['comvlr01'],$nDec);}else{echo "";} ?>";
                document.forms['frgrm']['nComIva' +document.forms['frgrm']['nSecuencia'].value].value = "<?php if($xRCD['comvlr02'] > 0){echo round($xRCD['comvlr02'],$nDec);}else{echo "";} ?>";
                document.forms['frgrm']['nComBIva'+document.forms['frgrm']['nSecuencia'].value].disabled = false;
                document.forms['frgrm']['nComIva' +document.forms['frgrm']['nSecuencia'].value].disabled = false;
              }
            }
            document.forms['frgrm']['nComVlr'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php if($xRCD['comvlrxx'] > 0){echo round($xRCD['comvlrxx'],$nDec);}else{echo "";} ?>";
            document.forms['frgrm']['nComVlrNF'+document.forms['frgrm']['nSecuencia'].value].value = "<?php if($xRCD['comvlrnf'] > 0){echo round($xRCD['comvlrnf'],$nDec);}else{echo "";} ?>";
            document.forms['frgrm']['cComMov'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['commovxx'] ?>";
            document.forms['frgrm']['cComNit'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vCtoCon['ctonitxx'] ?>";
            document.forms['frgrm']['cTerTip'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['tertipxx']    ?>";
            document.forms['frgrm']['cTerId'   +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['teridxxx']    ?>";
            document.forms['frgrm']['cTerTipB' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['tertip2x']    ?>";
            document.forms['frgrm']['cTerIdB'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['terid2xx']    ?>";
            document.forms['frgrm']['cPucId'   +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['pucidxxx']    ?>";
            document.forms['frgrm']['cPucDet'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vCtoCon['pucdetxx'] ?>";
            document.forms['frgrm']['cPucTer'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vCtoCon['pucterxx'] ?>";
            document.forms['frgrm']['nPucBRet' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vCtoCon['pucbaret'] ?>";
            document.forms['frgrm']['nPucRet'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vCtoCon['pucretxx'] ?>";
            document.forms['frgrm']['cPucNat'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vCtoCon['pucnatxx'] ?>";
            document.forms['frgrm']['cPucInv'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vCtoCon['pucinvxx'] ?>";
            document.forms['frgrm']['cPucCco'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vCtoCon['puccccxx'] ?>";
            document.forms['frgrm']['cPucDoSc' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vCtoCon['pucdoscc'] ?>";
            document.forms['frgrm']['cPucTipEj'+document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['puctipej']    ?>";
            document.forms['frgrm']['cComVlr1' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vCtoCon['ctovlr01'] ?>";
            document.forms['frgrm']['cComVlr2' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vCtoCon['ctovlr02'] ?>";
            document.forms['frgrm']['cComFac'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comfacxx']    ?>";
            document.forms['frgrm']['cSucId'   +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['sucidxxx']    ?>";
            document.forms['frgrm']['cDocId'   +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['docidxxx']    ?>";
            document.forms['frgrm']['cDocSuf'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['docsufxx']    ?>";

            //Campos de intermediacion de pago
            document.forms['frgrm']['cComIdCB'    +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comidc2x'] ?>";
            document.forms['frgrm']['cComCodCB'   +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comcodc2'] ?>";
            document.forms['frgrm']['cComCscCB'   +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comcscc2'] ?>";
            document.forms['frgrm']['cComSeqCB'   +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comseqc2'] ?>";
            document.forms['frgrm']['cCtoIdInp'   +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['ctoidinp'] ?>";
            document.forms['frgrm']['cPucIdInp'   +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['pucidinp'] ?>";
            document.forms['frgrm']['cPucDetInp'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $cPucDetInp       ?>";
            document.forms['frgrm']['cPucTerInp'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $cPucTerInp       ?>";
            document.forms['frgrm']['nPucBRetInp' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $nPucBRetInp      ?>";
            document.forms['frgrm']['nPucRetInp'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $nPucRetInp       ?>";
            document.forms['frgrm']['cPucNatInp'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $cPucNatInp       ?>";
            document.forms['frgrm']['cPucInvInp'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $cPucInvInp       ?>";
            document.forms['frgrm']['cPucCcoInp'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $cPucCcoInp       ?>";
            document.forms['frgrm']['cPucDoScInp' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $cPucDoScInp      ?>";
            document.forms['frgrm']['cPucTipEjInp'+document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['puctipej'] ?>";
            document.forms['frgrm']['cComVlr1Inp' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $cComVlr1Inp      ?>";
            document.forms['frgrm']['cComVlr2Inp' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $cComVlr2Inp      ?>";

            //Color para indicar si la secuencia es una retencion de intemediacion de pago
            document.getElementById('cComSeq'+document.forms['frgrm']['nSecuencia'].value).style.color = "<?php echo $cColor ?>";
            document.getElementById('cCtoDes'+document.forms['frgrm']['nSecuencia'].value).style.color = "<?php echo $cColor ?>";

            if (document.forms['frgrm']['cSccId_SucId'].value == "" && document.forms['frgrm']['cSccId'].value == "<?php echo $xRCD['sccidxxx'] ?>") {
              document.forms['frgrm']['cSccId_SucId'].value  = "<?php echo $xRCD['sucidxxx'] ?>";
              document.forms['frgrm']['cSccId_DocId'].value  = "<?php echo $xRCD['docidxxx'] ?>";
              document.forms['frgrm']['cSccId_DocSuf'].value = "<?php echo $xRCD['docsufxx'] ?>";
            }

            //Habilitando Grilla segun tipo de ejecucion
            switch ("<?php echo $xRCD['puctipej'] ?>") {
              case "L": //Tipo ejecucion Local
                document.forms['frgrm']['nComVlr'  +document.forms['frgrm']['nSecuencia'].value].disabled = false;
                document.forms['frgrm']['nComVlrNF'+document.forms['frgrm']['nSecuencia'].value].disabled = true;
              break;
              case "N": //Ejecucion NIIF
                document.forms['frgrm']['nComVlr'  +document.forms['frgrm']['nSecuencia'].value].disabled = true;
                document.forms['frgrm']['nComVlrNF'+document.forms['frgrm']['nSecuencia'].value].disabled = false;
              break;
              default: //Ambas
                document.forms['frgrm']['nComVlr'  +document.forms['frgrm']['nSecuencia'].value].disabled = false;
                document.forms['frgrm']['nComVlrNF'+document.forms['frgrm']['nSecuencia'].value].disabled = false;
              break;
            }

            if  ("<?php echo $vCtoCon['puctipej'] ?>" == "L" || "<?php echo $vCtoCon['puctipej'] ?>" == "") {
              if ("<?php echo $vCtoCon['ctovlr01'] ?>" == "SI" || "<?php echo $vCtoCon['ctovlr02'] ?>" == "SI") {
                if ("<?php echo $vCtoCon['pucretxx'] ?>" > 0) { // Es una retencion
                  document.forms['frgrm']['nComBIva' +document.forms['frgrm']['nSecuencia'].value].disabled = true;
                  document.forms['frgrm']['nComIva'  +document.forms['frgrm']['nSecuencia'].value].disabled = true;
                  document.forms['frgrm']['nComBRet' +document.forms['frgrm']['nSecuencia'].value].disabled = false;
                } else { // Es un IVA.
                  document.forms['frgrm']['nComBRet' +document.forms['frgrm']['nSecuencia'].value].disabled = true;
                  document.forms['frgrm']['nComBIva' +document.forms['frgrm']['nSecuencia'].value].disabled = false;
                  document.forms['frgrm']['nComIva'  +document.forms['frgrm']['nSecuencia'].value].disabled = false;
                }
              }
            } else if ("<?php echo $vCtoCon['puctipej'] ?>" == "N") {
              //Para la ejecucion NIIF no aplican retenciones, ni IVA
              document.forms['frgrm']['nComBRet'+document.forms['frgrm']['nSecuencia'].value].disabled = true;
              document.forms['frgrm']['nComBIva'+document.forms['frgrm']['nSecuencia'].value].disabled = true;
              document.forms['frgrm']['nComIva' +document.forms['frgrm']['nSecuencia'].value].disabled = true;
              if ("<?php echo $vCtoCon['ctovlr01'] ?>" == "SI" || "<?php echo $vCtoCon['ctovlr02'] ?>" == "SI") {
                if (document.forms['frgrm']['nPucRet'+document.forms['frgrm']['nSecuencia'].value].value > 0) { // Es una retencion
                  //No Hace Nada
                } else { // Es un IVA, se debe digitar base Iva, no se calcula Iva
                  document.forms['frgrm']['nComBIva'+document.forms['frgrm']['nSecuencia'].value].disabled = false;
                }
              }
            }
            //Habilitando Grilla segun tipo de ejecucion
          </script>
        <?php } ?>
        <script languaje = "javascript">
          f_Cuadre_Debitos_Creditos();
        </script>
      <?php }
    } ?>
  </body>
</html>

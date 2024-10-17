<?php
  namespace openComex;
  /**
   * Nueva Matriz de Insumos Facturables.
   * --- Descripcion: Permite Crear una Nueva Matriz de Insumos Facturables.
   * @author juan.trujillo@openits.co
   * @package opencomex
   * @version 001
   */

  include("../../../../../financiero/libs/php/utility.php");
  include("../../../../libs/php/utimifxx.php");

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
            document.forms['frgrm']['cDepNum'].value    = "";
            document.forms['frgrm']['cTipoDep'].value   = "";
            document.forms['frgrm']['cPerFacDes'].value = "";
            document.forms['frgrm']['cCcoIdOc'].value   = "";
            fnHabilitaSubServicio("NO");

            if (xSwitch == "VALID") {
              var zRuta  = "frmif150.php?gWhat=VALID&gFunction=cCliId&gCliId="+document.forms['frgrm']['cCliId'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frmif150.php?gWhat=WINDOW&gFunction=cCliId&gCliId="+document.forms['frgrm']['cCliId'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cCliNom":
            document.forms['frgrm']['cDepNum'].value    = "";
            document.forms['frgrm']['cTipoDep'].value   = "";
            document.forms['frgrm']['cPerFacDes'].value = "";
            document.forms['frgrm']['cCcoIdOc'].value   = "";
            fnHabilitaSubServicio("NO");

            if (xSwitch == "VALID") {
              var zRuta  = "frmif150.php?gWhat=VALID"+
                                        "&gFunction=cCliNom"+
                                        "&gCliNom="+document.forms['frgrm']['cCliNom'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frmif150.php?gWhat=WINDOW"+
                                        "&gFunction=cCliNom"+
                                        "&gCliNom="+document.forms['frgrm']['cCliNom'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cComPre":
            if (xSwitch == "VALID") {
              var zRuta  = "frmif117.php?gWhat=VALID&gFunction=cComPre&gComPre="+document.forms['frgrm']['cComPre'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frmif117.php?gWhat=WINDOW&gFunction=cComPre&gComPre="+document.forms['frgrm']['cComPre'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cDepNum":
            if (document.forms['frgrm']['cCliId'].value == '') {
              alert("Debe seleccionar el cliente para poder consultar los Depositos,\nVerifique.")
            } else {
              if (xSwitch == "VALID") {
                var zRuta  = "frmif155.php?gWhat=VALID" +
                              "&gFunction=cDepNum" +
                              "&gCliId="+document.forms['frgrm']['cCliId'].value +
                              "&gDepNum="+document.forms['frgrm']['cDepNum'].value.toUpperCase();
                parent.fmpro.location = zRuta;
              } else {
                var zNx     = (zX-600)/2;
                var zNy     = (zY-250)/2;
                var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
                var zRuta   = "frmif155.php?gWhat=WINDOW" +
                              "&gFunction=cDepNum" +
                              "&gCliId="+document.forms['frgrm']['cCliId'].value +
                              "&gDepNum="+document.forms['frgrm']['cDepNum'].value.toUpperCase();
                zWindow = window.open(zRuta,"zWindow",zWinPro);
                zWindow.focus();
              }
            }
          break;
        }
      }

      function fnHabilitaNoMif(xComTCo) {
        if (document.forms['frgrm']['cComPre'].value  == '') {
          alert("Debe seleccionar primero un Prefijo, verifique ")
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

      function fnHabilitaSubServicio(valor, xDepNum) {
        if (valor == "NO") {
          document.getElementById('idSubServicio').style.display = "none";
        } else {
          fnCargarGrillaSubServicio();
          document.getElementById('idSubServicio').style.display = "block";
        }
      }

      function fnCargarGrillaSubServicio() {
        var cRuta = "frmifgri.php?gCcoIdOc="+document.forms['frgrm']['cCcoIdOc'].value+
                                "&gCliId="+document.forms['frgrm']['cCliId'].value+
                                "&gDepNum="+document.forms['frgrm']['cDepNum'].value+
                                "&gMifId="+document.forms['frgrm']['cMifId'].value+
                                "&gAnio="+document.forms['frgrm']['cAnio'].value+
                                "&gTipo=1";

        parent.fmpro.location = cRuta;
      }

      function fnValidaEstado(){
        var zEstado = document.forms['frgrm']['cEstado'].value.toUpperCase();
        if(zEstado == 'E' || zEstado == 'EN' || zEstado == 'ENP' || zEstado == 'ENPR' || zEstado == 'ENPRO' || zEstado == 'ENPROC' || zEstado == 'ENPROCE' || zEstado == 'ENPROCES'  || zEstado == 'ENPROCESO'){
          zEstado = 'ENPROCESO';
        } else {
          if(zEstado == 'C' || zEstado == 'CE' || zEstado == 'CER' || zEstado == 'CERR' || zEstado == 'CERRA' || zEstado == 'CERRAD' || zEstado == 'CERRADO') {
            zEstado = 'CERRADO';
          } else {
            zEstado = '';
          }
        }
        document.forms['frgrm']['cEstado'].value = zEstado;
      }

      /**
       * Permite seleccionar todos los subservicios y guardar los valores en el campo oculto.
       */
      function fnCheckTodos(value, xCantidad) {
        for (i=0;i<xCantidad;i++) {
          document.forms['frgrm']['cCheckSub'+i].checked = value.checked;
        }

        if (value.checked == false) {
          document.forms['frgrm']['cSubservicios'].value = "";

          var cSubserNoMarcados = "";
          for (i=0;i<xCantidad;i++) {
            cSubserNoMarcados += document.forms['frgrm']['cSerId'+i].value + document.forms['frgrm']['cSubId'+i].value + "|";
          }

          document.forms['frgrm']['cSubserNoMarcados'].value = cSubserNoMarcados.substr(0, cSubserNoMarcados.length - 1);
        } else {
          fnCambiarCheck(value, xCantidad, '');
        }
      }

      /**
       * Permite asignar el valor del subservicio seleccionado en el campo oculto.
       */
      function fnCambiarCheck(value, xCantidad, xIndice) {
        document.forms['frgrm']['cSubservicios'].value = "";
        document.forms['frgrm']['cSubserNoMarcados'].value = "";

        var cSubservicios = "";
        var cSubserNoMarcados = "";
        for (i=0;i<xCantidad;i++) {
          if (document.forms['frgrm']['cCheckSub'+i].checked == true) {
            cSubservicios += document.forms['frgrm']['cSerId'+i].value + "~" + document.forms['frgrm']['cSubId'+i].value + "|";
          } else {
            cSubserNoMarcados += document.forms['frgrm']['cSerId'+i].value + "~" + document.forms['frgrm']['cSubId'+i].value + "|";
          }
        }

        document.forms['frgrm']['cSubservicios'].value = cSubservicios;
        document.forms['frgrm']['cSubserNoMarcados'].value = cSubserNoMarcados;
      }
    </script>
  </head>
  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
    <center>
      <table border="0" cellpadding="0" cellspacing="0" width="620">
        <tr>
          <td>
            <fieldset>
              <legend><?php echo $_COOKIE['kMenDes'] ?></legend>
              <form name = "frestado" action = "frmifgra.php" method = "post" target="fmpro">
                <input type = "hidden" name = "cMifId"       value = "">
                <input type = "hidden" name = "cAnio"        value = "">
              </form>
              <form name = 'frgrm' action = 'frmifgra.php' method = 'post' target='fmpro'>
                <input type = 'hidden' name = 'cMifId'       value = "<?php echo $cMifId ?>">
                <input type = 'hidden' name = 'cAnio'        value = "<?php echo $cAnio ?>">
                <center>
                  <table border="0" cellpadding="0" cellspacing="0" width="620">
                    <?php $nCol = f_Format_Cols(31); echo $nCol; ?>
                    <!-- Seccion 1 -->
                    <tr>
                      <td Class = "clase08" colspan="3">
                        <a href = "javascript:document.forms['frgrm']['cComPre'].value = '';
                                              document.forms['frgrm']['cComId'].value  = '';
                                              document.forms['frgrm']['cComCod'].value = '';
                                              document.forms['frgrm']['cComTCo'].value = '';
                                              document.forms['frgrm']['cComCsc'].value = '';
                                              document.forms['frgrm']['cComCco'].value = '';
                                              fnLinks('cComPre','VALID')" id = "idPrefijo">Prefijo</a><br>
                        <input type = 'text' Class = 'letra' style = 'width:60' name = 'cComPre' maxlength="3"
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
                        <input type="hidden" name="cComId">
                        <input type="hidden" name="cComCod">
                        <input type="hidden" name="cComTCo">
                        <input type="hidden" name="cComCco">
                      </td>
                      <td class="clase08" colspan="8">No. M.I.F<br>
                        <input type = 'text' Class = 'letra' style = 'width:160' name = "cComCsc" maxlength="6"
                          onBlur = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                          onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'
                                              fnHabilitaNoMif(cComTCo);">
                        <input type = "hidden" name = "cComCsc2" readonly>
                      </td>
                      <td Class = "clase08" colspan="5">
                        <a href = "javascript:document.forms['frgrm']['cCliId'].value  = '';
                                              document.forms['frgrm']['cCliNom'].value = '';
                                              document.forms['frgrm']['cCliDV'].value  = '';
                                              document.forms['frgrm']['cCliSap'].value = '';
                                              fnLinks('cCliId','VALID')" id = "lCliId">Nit</a><br>
                        <input type = 'text' Class = 'letra' style = 'width:100' name = 'cCliId' maxlength="20"
                          onBlur = "javascript:this.value=this.value.toUpperCase();
                                              fnLinks('cCliId','VALID');
                                              this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                          onFocus = "javascript:document.forms['frgrm']['cCliId'].value  = '';
                                                document.forms['frgrm']['cCliNom'].value = '';
                                                document.forms['frgrm']['cCliDV'].value  = '';
                                                document.forms['frgrm']['cCliSap'].value = '';
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
                                              this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                      <td class="clase08" colspan="4">C&oacute;digo SAP<br>
                        <input type = 'text' Class = 'letra' style = 'width:80' name = "cCliSap" readonly>
                      </td>
                    </tr>

                    <!-- Seccion 2 -->
                    <tr>
                      <td class="clase08" colspan="6">
                      <a href = "javascript:document.forms['frgrm']['cDepNum'].value      = '';
                                              document.forms['frgrm']['cTipoDep'].value   = '';
                                              document.forms['frgrm']['cPerFacDes'].value = '';
                                              document.forms['frgrm']['cCcoIdOc'].value   = '';
                                              fnLinks('cDepNum','VALID')" id = "lDepNum">Dep&oacute;sito</a><br>
                        <input type = 'text' Class = 'letra' style = 'width:120' name = 'cDepNum' maxlength="20"
                          onBlur = "javascript:this.value=this.value.toUpperCase();
                                              fnLinks('cDepNum','VALID');
                                              this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                          onFocus = "javascript:document.forms['frgrm']['cDepNum'].value    = '';
                                                document.forms['frgrm']['cTipoDep'].value   = '';
                                                document.forms['frgrm']['cPerFacDes'].value = '';
                                                document.forms['frgrm']['cCcoIdOc'].value   = '';
                                                this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                        <input type="hidden" name="cCcoIdOc">
                      </td>
                      <td class="clase08" colspan="10">Tipo Deposito<br>
                        <input type = 'text' Class = 'letra' style = 'width:200' name = "cTipoDep" readonly>
                      </td>
                      <td class="clase08" colspan="5">Periodicidad<br>
                        <input type = 'text' Class = 'letra' style = 'width:100' name = "cPerFacDes" readonly>
                      </td>
                      <td class="clase08" colspan = "5">
                        <a href='javascript:show_calendar("frgrm.dDesde")' id="id_href_dDesde">Fecha Desde</a><br>
                        <input type="text" name="dDesde" style = "width:100;height:15;text-align:center">
                      </td>
                      <td class="clase08" colspan = "5">
                        <a href='javascript:show_calendar("frgrm.dHasta")' id="id_href_dHasta">Fecha Hasta</a><br>
                        <input type="text" name="dHasta" style = "width:100;height:15;text-align:center">
                      </td>
                    </tr>

                    <!-- Seccion 3 -->
                    <tr>
                      <td Class = "clase08" colspan="31">
                        <fieldset id="idSubServicio">
                          <input type = 'hidden' name = 'cSubservicios'>
                          <input type = 'hidden' name = 'cSubserNoMarcados'>
                          <input type = 'hidden' name = 'nIndexSubser'>
                          <legend>Subservicios</legend>
                          <div id = 'overDivSubServicios'></div>
                        </fieldset>
                      </td>
                    </tr>

                    <!-- Seccion 4 -->
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
                      <td Class = "clase08" colspan = "7">Estado<br>
                        <input type = "text" Class = "letra" style = "width:140;text-align:center" name = "cEstado"  value = "ENPROCESO"
                                onblur = "javascript:this.value=this.value.toUpperCase();fnValidaEstado();
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
      <table border="0" cellpadding="0" cellspacing="0" width="620">
        <tr height="21">
          <?php switch ($_COOKIE['kModo']) {
            case "VER": ?>
              <td width="529" height="21"></td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
            <?php break;
            default: ?>
              <td width="438" height="21"></td>
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
          document.forms['frgrm']['cEstado'].readOnly  = true;
          document.getElementById('idSubServicio').style.display = "none";
        </script>
        <?php
      break;
      case "EDITAR":
        fnCargaData($cMifId,$cAnio);

        ?>
        <script languaje = "javascript">
          // Deshabilito los campos
          for (x=0;x<document.forms['frgrm'].elements.length;x++) {
            document.forms['frgrm'].elements[x].readOnly = true;
            document.forms['frgrm'].elements[x].onfocus  = "";
            document.forms['frgrm'].elements[x].onblur   = "";
          }

          // Deshabilito los link de los Valid/Windows
          document.getElementById('idPrefijo').removeAttribute('href');
          document.getElementById('lCliId').removeAttribute('href');
          document.getElementById('lDepNum').removeAttribute('href');
          document.getElementById('id_href_dDesde').removeAttribute('href');
          document.getElementById('id_href_dHasta').removeAttribute('href');
        </script>
      <?php      
      break;
      case "VER": 
        fnCargaData($cMifId,$cAnio);
        ?>
        <script languaje = "javascript">
          for (x=0;x<document.forms['frgrm'].elements.length;x++) {
            document.forms['frgrm'].elements[x].readOnly = true;
            document.forms['frgrm'].elements[x].onfocus  = "";
            document.forms['frgrm'].elements[x].onblur   = "";
          }
          // Deshabilito los link de los Valid/Windows
          document.getElementById('idPrefijo').removeAttribute('href');
          document.getElementById('lCliId').removeAttribute('href');
          document.getElementById('lDepNum').removeAttribute('href');
          document.getElementById('id_href_dDesde').removeAttribute('href');
          document.getElementById('id_href_dHasta').removeAttribute('href');
        </script>
      <?php
      break;
    }
    function fnCargaData($gMifId,$gAnio){
      // Se instancia la clase de Matriz de Insumos Facturables
      $ObjcMatrizInsumosFacturables = new cMatrizInsumosFacturables();

      // Se obtiene la informacion de la M.I.F
      $pArrayDatos = array();
      $pArrayDatos['cMifId'] = $gMifId;
      $pArrayDatos['cAnio']  = $gAnio;

      $mReturnMif = $ObjcMatrizInsumosFacturables->fnCargarDataMIF($pArrayDatos);
      $mData = $mReturnMif[1];
      ?>
      <script language = "javascript">
        document.forms['frgrm']['cComId'].value     = "<?php echo $mData['comidxxx'] ?>";
        document.forms['frgrm']['cComCod'].value    = "<?php echo $mData['comcodxx'] ?>";
        document.forms['frgrm']['cComPre'].value    = "<?php echo $mData['comprexx'] ?>";
        document.forms['frgrm']['cComCsc'].value    = "<?php echo $mData['comcscxx'] ?>";
        document.forms['frgrm']['cCliId'].value     = "<?php echo $mData['cliidxxx'] ?>";
        document.forms['frgrm']['cCliDV'].value     = "<?php echo gendv($mData['cliidxxx']) ?>";
        document.forms['frgrm']['cCliNom'].value    = "<?php echo $mData['climonxx'] ?>";
        document.forms['frgrm']['cCliSap'].value    = "<?php echo $mData['clisapxx'] ?>";
        document.forms['frgrm']['cDepNum'].value    = "<?php echo $mData['depnumxx'] ?>";
        document.forms['frgrm']['cCcoIdOc'].value   = "<?php echo $mData['ccoidocx'] ?>";
        document.forms['frgrm']['cTipoDep'].value   = "<?php echo $mData['tdedesxx'] ?>";
        document.forms['frgrm']['cPerFacDes'].value = "<?php echo $mData['pfadesxx'] ?>";
        document.forms['frgrm']['dDesde'].value     = "<?php echo $mData['miffdexx'] ?>";
        document.forms['frgrm']['dHasta'].value     = "<?php echo $mData['miffhaxx'] ?>";
        document.forms['frgrm']['dFecCre'].value    = "<?php echo $mData['regfcrex'] ?>";
        document.forms['frgrm']['dHorCre'].value    = "<?php echo $mData['reghcrex'] ?>";
        document.forms['frgrm']['dFecMod'].value    = "<?php echo $mData['regfmodx'] ?>";
        document.forms['frgrm']['dHorMod'].value    = "<?php echo $mData['reghmodx'] ?>";
        document.forms['frgrm']['cEstado'].value    = "<?php echo $mData['regestxx'] ?>";
        fnCargarGrillaSubServicio();
      </script> 
    <?php } ?>
  </body>
</html>




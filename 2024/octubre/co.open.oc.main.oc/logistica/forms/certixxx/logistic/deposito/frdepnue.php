<?php
  namespace openComex;
  /**
   * Nuevo Deposito.
   * --- Descripcion: Permite Crear una Nuevo Deposito.
   * @author juan.trujillo@openits.co
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

      function fnLinks(xLink,xSwitch,xCodOrgVenta='') {
        var zX    = screen.width;
        var zY    = screen.height;
        switch (xLink) {
          // Tipo deposito
          case "cTdeId":
            if (xSwitch == "VALID") {
              var zRuta  = "frdep007.php?gWhat=VALID"+
                                        "&gFunction=cTdeId"+
                                        "&gTdeId="+document.forms['frgrm']['cTdeId'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frdep007.php?gWhat=WINDOW"+
                                        "&gFunction=cTdeId"+
                                        "&gTdeId="+document.forms['frgrm']['cTdeId'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cTdeDes":
            if (xSwitch == "VALID") {
              var zRuta  = "frdep007.php?gWhat=VALID"+
                                        "&gFunction=cTdeDes"+
                                        "&gTdeDes="+document.forms['frgrm']['cTdeDes'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frdep007.php?gWhat=WINDOW"+
                                        "&gFunction=cTdeDes"+
                                        "&gTdeDes="+document.forms['frgrm']['cTdeDes'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          // Cliente
          case "cCliId":
            if (xSwitch == "VALID") {
              var zRuta  = "frdep150.php?gWhat=VALID"+
                                        "&gFunction=cCliId"+
                                        "&gCliId="+document.forms['frgrm']['cCliId'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frdep150.php?gWhat=WINDOW"+
                                        "&gFunction=cCliId"+
                                        "&gCliId="+document.forms['frgrm']['cCliId'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cCliNom":
            if (xSwitch == "VALID") {
              var zRuta  = "frdep150.php?gWhat=VALID"+
                                        "&gFunction=cCliNom"+
                                        "&gCliNom="+document.forms['frgrm']['cCliNom'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frdep150.php?gWhat=WINDOW"+
                                        "&gFunction=cCliNom"+
                                        "&gCliNom="+document.forms['frgrm']['cCliNom'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          // Condicion comercial
          case "cCcoIdOc":
            if (document.forms['frgrm']['cCliId'].value != "") {
              if (xSwitch == "VALID") {
                var zRuta  = "frdep151.php?gWhat=VALID" + 
                                          "&gFunction=cCcoIdOc" + 
                                          "&gCcoIdOc="+document.forms['frgrm']['cCcoIdOc'].value.toUpperCase() +
                                          "&gCliId="+document.forms['frgrm']['cCliId'].value;
                parent.fmpro.location = zRuta;
              } else {
                var zNx     = (zX-600)/2;
                var zNy     = (zY-250)/2;
                var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
                var zRuta   = "frdep151.php?gWhat=WINDOW" +
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
          // Organizacion de venta
          case "cOrvSap":
            if (xSwitch == "VALID") {
              var zRuta  = "frdep001.php?gWhat=VALID"+
                                        "&gFunction=cOrvSap"+
                                        "&gOrvSap="+document.forms['frgrm']['cOrvSap'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frdep001.php?gWhat=WINDOW"+
                                        "&gFunction=cOrvSap"+
                                        "&gOrvSap="+document.forms['frgrm']['cOrvSap'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cOrvDes":
            if (xSwitch == "VALID") {
              var zRuta  = "frdep001.php?gWhat=VALID"+
                                        "&gFunction=cOrvDes"+
                                        "&gOrvDes="+document.forms['frgrm']['cOrvDes'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frdep001.php?gWhat=WINDOW"+
                                        "&gFunction=cOrvDes"+
                                        "&gOrvDes="+document.forms['frgrm']['cOrvDes'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          // Oficina de venta
          case "cOfvSap":
            if (document.forms['frgrm']['cOrvSap'].value != "") {
              if (xSwitch == "VALID") {
                var zRuta  = "frdep002.php?gWhat=VALID"+
                                          "&gFunction=cOfvSap"+
                                          "&gOrvSap="+document.forms['frgrm']['cOrvSap'].value.toUpperCase()+
                                          "&gOfvSap="+document.forms['frgrm']['cOfvSap'].value.toUpperCase();
                parent.fmpro.location = zRuta;
              } else {
                var zNx     = (zX-600)/2;
                var zNy     = (zY-250)/2;
                var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
                var zRuta   = "frdep002.php?gWhat=WINDOW"+
                                          "&gFunction=cOfvSap"+
                                          "&gOrvSap="+document.forms['frgrm']['cOrvSap'].value.toUpperCase()+
                                          "&gOfvSap="+document.forms['frgrm']['cOfvSap'].value.toUpperCase();
                zWindow = window.open(zRuta,"zWindow",zWinPro);
                zWindow.focus();
              }
            } else {
              alert('Debe seleccionar la organizacion de venta,\nVerifique.');
            }
          break;
          case "cOfvDes":
            if (document.forms['frgrm']['cOrvSap'].value != "") {
              if (xSwitch == "VALID") {
                var zRuta  = "frdep002.php?gWhat=VALID"+
                                          "&gFunction=cOfvDes"+
                                          "&gOrvSap="+document.forms['frgrm']['cOrvSap'].value.toUpperCase()+
                                          "&gOfvDes="+document.forms['frgrm']['cOfvDes'].value.toUpperCase();
                parent.fmpro.location = zRuta;
              } else {
                var zNx     = (zX-600)/2;
                var zNy     = (zY-250)/2;
                var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
                var zRuta   = "frdep002.php?gWhat=WINDOW"+
                                          "&gFunction=cOfvDes"+
                                          "&gOrvSap="+document.forms['frgrm']['cOrvSap'].value.toUpperCase()+
                                          "&gOfvDes="+document.forms['frgrm']['cOfvDes'].value.toUpperCase();
                zWindow = window.open(zRuta,"zWindow",zWinPro);
                zWindow.focus();
              }
            } else {
              alert('Debe seleccionar la organizacion de venta,\nVerifique.');
            }
          break;
          // Centro Logistico
          case "cCloSap":
            if (document.forms['frgrm']['cOrvSap'].value != "" && document.forms['frgrm']['cOfvSap'].value != "") {
              if (xSwitch == "VALID") {
                var zRuta  = "frdep003.php?gWhat=VALID"+
                                          "&gFunction=cCloSap"+
                                          "&gOrvSap="+document.forms['frgrm']['cOrvSap'].value.toUpperCase()+
                                          "&gOfvSap="+document.forms['frgrm']['cOfvSap'].value.toUpperCase()+
                                          "&gCloSap="+document.forms['frgrm']['cCloSap'].value.toUpperCase();
                parent.fmpro.location = zRuta;
              } else {
                var zNx     = (zX-600)/2;
                var zNy     = (zY-250)/2;
                var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
                var zRuta   = "frdep003.php?gWhat=WINDOW"+
                                          "&gFunction=cCloSap"+
                                          "&gOrvSap="+document.forms['frgrm']['cOrvSap'].value.toUpperCase()+
                                          "&gOfvSap="+document.forms['frgrm']['cOfvSap'].value.toUpperCase()+
                                          "&gCloSap="+document.forms['frgrm']['cCloSap'].value.toUpperCase();
                zWindow = window.open(zRuta,"zWindow",zWinPro);
                zWindow.focus();
              }
            } else {
              alert('Debe seleccionar la organizacion de venta y la oficina de venta,\nVerifique.');
            }
          break;
          case "cCloDes":
            if (document.forms['frgrm']['cOrvSap'].value != "" && document.forms['frgrm']['cOfvSap'].value != "") {
              if (xSwitch == "VALID") {
                var zRuta  = "frdep003.php?gWhat=VALID"+
                                          "&gFunction=cCloDes"+
                                          "&gOrvSap="+document.forms['frgrm']['cOrvSap'].value.toUpperCase()+
                                          "&gOfvSap="+document.forms['frgrm']['cOfvSap'].value.toUpperCase()+
                                          "&gCloDes="+document.forms['frgrm']['cCloDes'].value.toUpperCase();
                parent.fmpro.location = zRuta;
              } else {
                var zNx     = (zX-600)/2;
                var zNy     = (zY-250)/2;
                var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
                var zRuta   = "frdep003.php?gWhat=WINDOW"+
                                          "&gFunction=cCloDes"+
                                          "&gOrvSap="+document.forms['frgrm']['cOrvSap'].value.toUpperCase()+
                                          "&gOfvSap="+document.forms['frgrm']['cOfvSap'].value.toUpperCase()+
                                          "&gCloDes="+document.forms['frgrm']['cCloDes'].value.toUpperCase();
                zWindow = window.open(zRuta,"zWindow",zWinPro);
                zWindow.focus();
              }
            } else {
              alert('Debe seleccionar la organizacion de venta y la oficina de venta,\nVerifique.');
            }
          break;
          // Sector
          case "cSecSap":
            if (xSwitch == "VALID") {
              var zRuta  = "frdep009.php?gWhat=VALID"+
                                        "&gFunction=cSecSap"+
                                        "&gSecSap="+document.forms['frgrm']['cSecSap'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frdep009.php?gWhat=WINDOW"+
                                        "&gFunction=cSecSap"+
                                        "&gSecSap="+document.forms['frgrm']['cSecSap'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cSecDes":
            if (xSwitch == "VALID") {
              var zRuta  = "frdep009.php?gWhat=VALID"+
                                        "&gFunction=cSecDes"+
                                        "&gSecDes="+document.forms['frgrm']['cSecDes'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frdep009.php?gWhat=WINDOW"+
                                        "&gFunction=cSecDes"+
                                        "&gSecDes="+document.forms['frgrm']['cSecDes'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
        }
      }
    </script>
  </head>
  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
    <center>
      <table border="0" cellpadding="0" cellspacing="0" width="520">
        <tr>
          <td>
            <fieldset>
              <legend><?php echo $_COOKIE['kMenDes'] ?></legend>
              <form name = 'frgrm' action = 'frdepgra.php' method = 'post' target='fmpro'>
                <center>
                  <table border="0" cellpadding="0" cellspacing="0" width="520">
                    <?php $nCol = f_Format_Cols(27); echo $nCol; ?>
                    <!-- Seccion 1 -->
                    <tr>
                      <td class="clase08" colspan="9">N&uacute;mero Dep&oacute;sito<br>
                        <input type = 'text' Class = 'letra' style = 'width:180' name = "cDepNum" maxlength="15">
                      </td>
                      <td Class = "clase08" colspan="5">
                        <a href = "javascript:document.forms['frgrm']['cTdeId'].value  = '';
                                              document.forms['frgrm']['cTdeDes'].value = '';
                                              fnLinks('cTdeId','VALID')" id = "idTipoDep">Id</a><br>
                        <input type = 'text' Class = 'letra' style = 'width:100' name = 'cTdeId' maxlength="2"
                          onBlur = "javascript:this.value=this.value.toUpperCase();
                                              fnLinks('cTdeId','VALID');
                                              this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                          onFocus = "javascript:document.forms['frgrm']['cTdeId'].value  = '';
                                                document.forms['frgrm']['cTdeDes'].value = '';
                                                this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                      <td class="clase08" colspan="12">Tipo Dep&oacute;sito<br>
                        <input type = 'text' Class = 'letra' style = 'width:240' name = "cTdeDes"
                          onBlur = "javascript:this.value=this.value.toUpperCase();
                                                fnLinks('cTdeDes','VALID');
                                                this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                          onFocus="javascript:document.forms['frgrm']['cTdeId'].value  ='';
                                              document.forms['frgrm']['cTdeDes'].value = '';
                                              this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                    </tr>
                    <!-- Seccion 2 -->
                    <tr>
                      <td Class = "clase08" colspan="6">
                        <a href = "javascript:document.forms['frgrm']['cCliId'].value  = '';
                                              document.forms['frgrm']['cCliNom'].value = '';
                                              document.forms['frgrm']['cCliDV'].value  = '';
                                              document.forms['frgrm']['cCliSap'].value = '';
                                              document.forms['frgrm']['cCcoIdOc'].value = '';
                                              fnLinks('cCliId','VALID')" id = "idCliId">Nit</a><br>
                        <input type = 'text' Class = 'letra' style = 'width:120' name = 'cCliId' maxlength="20"
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
                      <td class="clase08" colspan="12">Cliente<br>
                        <input type = 'text' Class = 'letra' style = 'width:240' name = "cCliNom"
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
                      <td class="clase08" colspan="7">C&oacute;digo SAP<br>
                        <input type = 'text' Class = 'letra' style = 'width:140' name = "cCliSap" readonly>
                      </td>
                    </tr>
                    <!-- Seccion 3 -->
                    <tr>
                      <td Class = "clase08" colspan="13">
                        <a href = "javascript:document.forms['frgrm']['cCcoIdOc'].value = '';
                                              fnLinks('cCcoIdOc','VALID')" id = "ifOfertaCom">Id Oferta Comercial</a><br>
                        <input type = 'text' Class = 'letra' style = 'width:260' name = 'cCcoIdOc' maxlength="20"
                          onBlur = "javascript:this.value=this.value.toUpperCase();
                                              fnLinks('cCcoIdOc','VALID');
                                              this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                          onFocus = "javascript:document.forms['frgrm']['cCcoIdOc'].value = '';
                                                this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                      <td Class = "clase08" colspan = "13">Periodicidad<br>
                        <select name="cPfaId" id="cPfaId" style = "width:260">
                          <option value="">[SELECCIONE]</option>
                          <?php 
                            $qPeriocidad  = "SELECT ";
                            $qPeriocidad .= "pfaidxxx, ";
                            $qPeriocidad .= "pfadesxx ";
                            $qPeriocidad .= "FROM $cAlfa.lpar0005 ";                        
                            $qPeriocidad .= "WHERE ";
                            $qPeriocidad .= "regestxx = \"ACTIVO\" ";
                            $qPeriocidad .= "ORDER BY pfaidxxx ";
                            $xPeriocidad  = f_MySql("SELECT","",$qPeriocidad,$xConexion01,"");
                            // f_Mensaje(__FILE__, __LINE__,$qPeriocidad."~".mysql_num_rows($xPeriocidad));
                            if (mysql_num_rows($xPeriocidad) > 0) {
                              while($xRPE = mysql_fetch_array($xPeriocidad)) { ?>
                                <option value="<?php echo $xRPE['pfaidxxx'] ?>"><?php echo $xRPE['pfadesxx'] ?></option>
                              <?php }
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                    <!-- Seccion 4 -->
                    <tr>
                      <td Class = "clase08" colspan="5">
                        <a href = "javascript:document.forms['frgrm']['cOrvSap'].value = '';
                                              document.forms['frgrm']['cOrvDes'].value = '';
                                              document.forms['frgrm']['cOfvSap'].value = '';
                                              document.forms['frgrm']['cOfvDes'].value = '';
                                              document.forms['frgrm']['cCloSap'].value = '';
                                              document.forms['frgrm']['cCloDes'].value = '';
                                              fnLinks('cOrvSap','VALID')" id = "idOrvSap">Cod. SAP</a><br>
                        <input type = 'text' Class = 'letra' style = 'width:100' name = 'cOrvSap' maxlength="2"
                          onBlur = "javascript:this.value=this.value.toUpperCase();
                                              fnLinks('cOrvSap','VALID');
                                              this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                          onFocus = "javascript:document.forms['frgrm']['cOrvSap'].value = '';
                                                document.forms['frgrm']['cOrvDes'].value = '';
                                                document.forms['frgrm']['cOfvSap'].value = '';
                                                document.forms['frgrm']['cOfvDes'].value = '';
                                                document.forms['frgrm']['cCloSap'].value = '';
                                                document.forms['frgrm']['cCloDes'].value = '';
                                                this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                      <td class="clase08" colspan="8">Organizaci&oacute;n Ventas<br>
                        <input type = 'text' Class = 'letra' style = 'width:160' name = "cOrvDes"
                          onBlur = "javascript:this.value=this.value.toUpperCase();
                                                fnLinks('cOrvDes','VALID');
                                                this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                          onFocus="javascript:document.forms['frgrm']['cOrvSap'].value ='';
                                              document.forms['frgrm']['cOrvDes'].value = '';
                                              document.forms['frgrm']['cOfvSap'].value = '';
                                              document.forms['frgrm']['cOfvDes'].value = '';
                                              document.forms['frgrm']['cCloSap'].value = '';
                                              document.forms['frgrm']['cCloDes'].value = '';
                                              this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>

                      <td Class = "clase08" colspan="5">
                        <a href = "javascript:document.forms['frgrm']['cOfvSap'].value = '';
                                              document.forms['frgrm']['cOfvDes'].value = '';
                                              document.forms['frgrm']['cCloSap'].value = '';
                                              document.forms['frgrm']['cCloDes'].value = '';
                                              fnLinks('cOfvSap','VALID')" id = "idOfvSap">Cod. SAP</a><br>
                        <input type = 'text' Class = 'letra' style = 'width:100' name = 'cOfvSap' maxlength="2"
                          onBlur = "javascript:this.value=this.value.toUpperCase();
                                              fnLinks('cOfvSap','VALID');
                                              this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                          onFocus = "javascript:document.forms['frgrm']['cOfvSap'].value = '';
                                                document.forms['frgrm']['cOfvDes'].value = '';
                                                document.forms['frgrm']['cCloSap'].value = '';
                                                document.forms['frgrm']['cCloDes'].value = '';
                                                this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                      <td class="clase08" colspan="8">Oficina Ventas<br>
                        <input type = 'text' Class = 'letra' style = 'width:160' name = "cOfvDes"
                          onBlur = "javascript:this.value=this.value.toUpperCase();
                                                fnLinks('cOfvDes','VALID');
                                                this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                          onFocus="javascript:document.forms['frgrm']['cOfvSap'].value ='';
                                              document.forms['frgrm']['cOfvDes'].value = '';
                                              document.forms['frgrm']['cCloSap'].value = '';
                                              document.forms['frgrm']['cCloDes'].value = '';
                                              this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>

                    </tr>
                    <!-- Seccion 5 -->
                    <tr>
                      <td Class = "clase08" colspan="5">
                        <a href = "javascript:document.forms['frgrm']['cCloSap'].value = '';
                                              document.forms['frgrm']['cCloDes'].value = '';
                                              fnLinks('cCloSap','VALID')" id = "idCentroLog">Cod. SAP</a><br>
                        <input type = 'text' Class = 'letra' style = 'width:100' name = 'cCloSap' maxlength="2"
                          onBlur = "javascript:this.value=this.value.toUpperCase();
                                              fnLinks('cCloSap','VALID');
                                              this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                          onFocus = "javascript:document.forms['frgrm']['cCloSap'].value = '';
                                                document.forms['frgrm']['cCloDes'].value = '';
                                                this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                      <td class="clase08" colspan="8">Centro Log&iacute;stico<br>
                        <input type = 'text' Class = 'letra' style = 'width:160' name = "cCloDes"
                          onBlur = "javascript:this.value=this.value.toUpperCase();
                                                fnLinks('cCloDes','VALID');
                                                this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                          onFocus="javascript:document.forms['frgrm']['cCloSap'].value ='';
                                              document.forms['frgrm']['cCloDes'].value = '';
                                              this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                      <td Class = "clase08" colspan="5">
                        <a href = "javascript:document.forms['frgrm']['cSecSap'].value = '';
                                              document.forms['frgrm']['cSecDes'].value = '';
                                              fnLinks('cSecSap','VALID')" id = "idSector">Cod. SAP</a><br>
                        <input type = 'text' Class = 'letra' style = 'width:100' name = 'cSecSap' maxlength="2"
                          onBlur = "javascript:this.value=this.value.toUpperCase();
                                              fnLinks('cSecSap','VALID');
                                              this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                          onFocus = "javascript:document.forms['frgrm']['cSecSap'].value = '';
                                                document.forms['frgrm']['cSecDes'].value = '';
                                                this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                      <td class="clase08" colspan="8">Sector<br>
                        <input type = 'text' Class = 'letra' style = 'width:160' name = "cSecDes"
                          onBlur = "javascript:this.value=this.value.toUpperCase();
                                                fnLinks('cSecDes','VALID');
                                                this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                          onFocus="javascript:document.forms['frgrm']['cSecSap'].value ='';
                                              document.forms['frgrm']['cSecDes'].value = '';
                                              this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                    </tr>
                    <!-- Seccion 9 -->
                    <tr>
                      <td Class = "clase08" colspan = "6">Creado<br>
                        <input type = "text" Class = "letra"  style = "width:120;text-align:center"  name = "dFecCre"  value = "<?php echo date('Y-m-d') ?>" readonly>
                      </td>
                      <td Class = "clase08" colspan = "4">Hora<br>
                        <input type = 'text' Class = 'letra' style = "width:80;text-align:center" name = "dHorCre"  value = "<?php echo date('H:i:s') ?>" readonly>
                      </td>
                      <td Class = "clase08" colspan = "6">Modificado<br>
                        <input type = "text" Class = "letra"  style = "width:120;text-align:center"  name = "dFecMod"  value = "<?php echo date('Y-m-d') ?>" readonly>
                      </td>
                      <td Class = "clase08" colspan = "4">Modificado<br>
                        <input type = "text" Class = "letra"  style = "width:80;text-align:center"  name = "dHorMod"  value = "<?php echo date('H:i:s') ?>" readonly>
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
      <table border="0" cellpadding="0" cellspacing="0" width="520">
        <tr height="21">
          <?php switch ($_COOKIE['kModo']) {
            case "VER": ?>
              <td width="429" height="21"></td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
            <?php break;
            default: ?>
              <td width="338" height="21"></td>
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
        //No hace nada
      break;
      case "EDITAR":
      case "VER":
        fnCargaData($cDepNum);

        if ($_COOKIE['kModo'] == "VER") { ?>
          <script>
            for (x=0;x<document.forms['frgrm'].elements.length;x++) {
              document.forms['frgrm'].elements[x].readOnly = true;
              document.forms['frgrm'].elements[x].onfocus  = "";
              document.forms['frgrm'].elements[x].onblur   = "";
            }
            document.getElementById('cPfaId').disabled = true;

            document.getElementById('idTipoDep').href   = 'javascript: alert("No permitido")';
            document.getElementById('idCliId').href     = 'javascript: alert("No permitido")';
            document.getElementById('ifOfertaCom').href = 'javascript: alert("No permitido")';
            document.getElementById('idOrvSap').href    = 'javascript: alert("No permitido")';
            document.getElementById('idOfvSap').href    = 'javascript: alert("No permitido")';
            document.getElementById('idCentroLog').href = 'javascript: alert("No permitido")';
            document.getElementById('idSector').href    = 'javascript: alert("No permitido")';
          </script>
          <?php 
        } else { ?>
          <script>
            document.forms['frgrm'].elements['cDepNum'].readOnly = true;
          </script>
          <?php 
        }
      break;
      default:
        //No hace nada
      break;
    }

    function fnCargaData($cDepNum) {
      global $cAlfa; global $xConexion01;

      $qDeposito  = "SELECT *, ";
      $qDeposito .= "$cAlfa.lpar0150.cliidxxx, ";
      $qDeposito .= "IF($cAlfa.lpar0150.clinomxx != \"\",$cAlfa.lpar0150.clinomxx,(TRIM(CONCAT($cAlfa.lpar0150.clinomxx,\" \",$cAlfa.lpar0150.clinom1x,\" \",$cAlfa.lpar0150.clinom2x,\" \",$cAlfa.lpar0150.cliape1x,\" \",$cAlfa.lpar0150.cliape2x)))) AS clinomxx, ";
      $qDeposito .= "$cAlfa.lpar0150.clisapxx, ";
      $qDeposito .= "$cAlfa.lpar0007.tdedesxx, ";
      $qDeposito .= "$cAlfa.lpar0001.orvdesxx, ";
      $qDeposito .= "$cAlfa.lpar0009.secdesxx ";
      $qDeposito .= "FROM $cAlfa.lpar0155 ";
      $qDeposito .= "LEFT JOIN $cAlfa.lpar0150 ON $cAlfa.lpar0155.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
      $qDeposito .= "LEFT JOIN $cAlfa.lpar0007 ON $cAlfa.lpar0155.tdeidxxx = $cAlfa.lpar0007.tdeidxxx ";
      $qDeposito .= "LEFT JOIN $cAlfa.lpar0001 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0001.orvsapxx ";
      $qDeposito .= "LEFT JOIN $cAlfa.lpar0009 ON $cAlfa.lpar0155.secsapxx = $cAlfa.lpar0009.secsapxx ";
      $qDeposito .= "WHERE ";
      $qDeposito .= "depnumxx = \"$cDepNum\" LIMIT 0,1";
      $xDeposito  = f_MySql("SELECT","",$qDeposito,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qDeposito."~".mysql_num_rows($xDeposito)."~".mysql_error($xConexion01));
      if (mysql_num_rows($xDeposito) > 0) {
        $vDeposito = mysql_fetch_array($xDeposito);

        // Consulta oficina de venta
        $qOfiVenta  = "SELECT ";
        $qOfiVenta .= "ofvsapxx, ";
        $qOfiVenta .= "ofvdesxx, ";
        $qOfiVenta .= "regestxx ";
        $qOfiVenta .= "FROM $cAlfa.lpar0002 ";
        $qOfiVenta .= "WHERE ";
        $qOfiVenta .= "orvsapxx = \"{$vDeposito['orvsapxx']}\" AND ";
        $qOfiVenta .= "ofvsapxx = \"{$vDeposito['ofvsapxx']}\" LIMIT 0,1";
        $xOfiVenta  = f_MySql("SELECT","",$qOfiVenta,$xConexion01,"");
        $vOfiVenta  = mysql_fetch_array($xOfiVenta);

        // Consulta centro logistico
        $qCentroLog  = "SELECT ";
        $qCentroLog .= "closapxx, ";
        $qCentroLog .= "clodesxx, ";
        $qCentroLog .= "regestxx ";
        $qCentroLog .= "FROM $cAlfa.lpar0003 ";
        $qCentroLog .= "WHERE ";
        $qCentroLog .= "orvsapxx = \"{$vDeposito['orvsapxx']}\" AND ";
        $qCentroLog .= "ofvsapxx = \"{$vDeposito['ofvsapxx']}\" AND ";
        $qCentroLog .= "closapxx = \"{$vDeposito['closapxx']}\" LIMIT 0,1";
        $xCentroLog  = f_MySql("SELECT","",$qCentroLog,$xConexion01,"");
        $vCentroLog  = mysql_fetch_array($xCentroLog);

        ?>
        <script language = "javascript">
          document.forms['frgrm']['cDepNum'].value  = "<?php echo $vDeposito['depnumxx'] ?>";
          document.forms['frgrm']['cCliId'].value   = "<?php echo $vDeposito['cliidxxx'] ?>";
          document.forms['frgrm']['cCliDV'].value   = "<?php echo gendv($vDeposito['cliidxxx'])?>";
          document.forms['frgrm']['cCliNom'].value  = "<?php echo $vDeposito['clinomxx'] ?>";
          document.forms['frgrm']['cCliSap'].value  = "<?php echo $vDeposito['clisapxx'] ?>";
          document.forms['frgrm']['cTdeId'].value   = "<?php echo $vDeposito['tdeidxxx'] ?>";
          document.forms['frgrm']['cTdeDes'].value  = "<?php echo $vDeposito['tdedesxx'] ?>";
          document.forms['frgrm']['cCcoIdOc'].value = "<?php echo $vDeposito['ccoidocx'] ?>";
          document.forms['frgrm']['cPfaId'].value   = "<?php echo $vDeposito['pfaidxxx'] ?>";
          document.forms['frgrm']['cOrvSap'].value  = "<?php echo $vDeposito['orvsapxx'] ?>";
          document.forms['frgrm']['cOrvDes'].value  = "<?php echo $vDeposito['orvdesxx'] ?>";
          document.forms['frgrm']['cOfvSap'].value  = "<?php echo $vDeposito['ofvsapxx'] ?>";
          document.forms['frgrm']['cOfvDes'].value  = "<?php echo $vOfiVenta['ofvdesxx'] ?>";          
          document.forms['frgrm']['cCloSap'].value  = "<?php echo $vDeposito['closapxx'] ?>";
          document.forms['frgrm']['cCloDes'].value  = "<?php echo $vCentroLog['clodesxx'] ?>";
          document.forms['frgrm']['cSecSap'].value  = "<?php echo $vDeposito['secsapxx'] ?>";
          document.forms['frgrm']['cSecDes'].value  = "<?php echo $vDeposito['secdesxx'] ?>";
          document.forms['frgrm']['dFecCre'].value  = "<?php echo $vDeposito['regfcrex'] ?>";
          document.forms['frgrm']['dHorCre'].value  = "<?php echo $vDeposito['reghcrex'] ?>";
          document.forms['frgrm']['dFecMod'].value  = "<?php echo $vDeposito['regfmodx'] ?>";
          document.forms['frgrm']['dHorMod'].value  = "<?php echo $vDeposito['reghmodx'] ?>";
          document.forms['frgrm']['cEstado'].value  = "<?php echo $vDeposito['regestxx'] ?>";
        </script>
        <?php
      }
    }
    ?>
  </body>
</html>




<?php
  namespace openComex;
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
          // Subservicios
          case 'cSubservicios':
            if (document.forms['frgrm']['cSerSap'].value != "") {
              var zSerSap = document.forms['frgrm']['cSerSap'].value.toUpperCase();
              var zNx     = (zX-580)/2;
              var zNy     = (zY-500)/2;
              var zWinPro = 'width=580,scrollbars=1,height=500,left='+zNx+',top='+zNy;
              var zRuta   = 'frcsessn.php?gSerSap='+zSerSap+'&gCseSubServ='+document.forms['frgrm']['cCseSubServ'].value;
              zWindow2    = window.open(zRuta,'zWindow2',zWinPro);
              zWindow2.focus();
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
        var cParametro = "1^"+document.forms['frgrm']['cCseSubServ'].value +
                        "|2^"+document.forms['frgrm']['cCseOrgVenta'].value;

        var cRuta = "frcsegri.php?gCseId=<?php echo $cCseId ?>" + 
                                "&gSerSap="+document.forms['frgrm']['cSerSap'].value +
                                "&gParametro="+cParametro;

        parent.fmpro.location = cRuta;
      }

      function fnCargarSubservicios() {
        var cRuta = "frcsegri.php?gTipo=1&gCseId=<?php echo $cCseId ?>" +
                                 "&gSerSap="+document.forms['frgrm']['cSerSap'].value +
                                 "&gCseSubServ="+document.forms['frgrm']['cCseSubServ'].value;
        parent.fmpro.location = cRuta;
      }

      function fnCargarOrganizacionVentas() {
        var cRuta = "frcsegri.php?gTipo=2&gCseId=<?php echo $cCseId ?>" +
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

        var cRuta = "frcsegri.php?gTipo=3&gCseId=<?php echo $cCseId ?>" +
                                "&gCseOrgVenta="+document.forms['frgrm']['cCseOrgVenta'].value +
                                "&gCseOfiVenta="+cCodRuta;
        parent.fmpro.location = cRuta;
      }

      function fnEliminarSubservicio(valor) {
        if (confirm('ELIMINAR EL SUBSERVICIO '+valor+'?')) {
          var ruta = "frcsessg.php?cCseId=<?php echo $cCseId ?>&tipsave=2&cIntId="+valor+"&cCseSubServ="+document.forms['frgrm']['cCseSubServ'].value;
          parent.fmpro.location = ruta;
        }
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
                          <input type = 'hidden' name = 'cCseSubServ'>
                          <legend>Subservicio</legend>
                          <div id = 'overDivSubServ'></div>
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
                      <td Class = "clase08" colspan = "20"><br><br>Aplica C&aacute;lculo a Nivel Nacional?<br>
                        <input type="checkbox" style="width:380;margin-top:-13px" name="cCseAcnn" Class = "letra" onchange="javascript:fnAplicaCalculo(this)"/>
                      </td>
                    </tr>
                    <!-- Seccion 6 -->
                    <tr >
                      <td Class = "clase08" colspan="30" id="idOrganizacion">
                        <fieldset>
                          <input type = 'hidden' name = 'cCseOrgVenta'>
                          <legend>Organizaci&oacute;n de Ventas</legend>
                          <div id = 'overDivOrgVenta'></div>
                        </fieldset>
                      </td>
                    </tr>
                    <!-- Seccion 7 -->
                    <tr >
                      <td Class = "clase08" colspan="30" id="idOficina">
                        <div class="idInputOficinasVentas" id="idInputOficinasVentas">
                        </div>
                        <div id="idOficinasVentas">
                        </div>
                      </td>
                    </tr>
                    <!-- Seccion 8 -->
                    <tr>
                      <td Class = "clase08" colspan = "30"><br>Observaci&oacute;n
                        <textarea Class = 'letra' style = 'width:600;height:40' name = 'cCseObs'></textarea>
                      </td>
                    </tr>
                    <!-- Seccion 9 -->
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
          fnCargarGrillas();
        </script>
        <?php
      break;
      case "EDITAR":
      case "VER":
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
            document.getElementById('idUfId').href    = 'javascript: alert("No permitido")';
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

        // Consulta subservicio
        $cSubservicio  = "";
        $qSubservicio  = "SELECT ";
        $qSubservicio .= "cseidxxx, ";
        $qSubservicio .= "sersapxx, ";
        $qSubservicio .= "subidxxx ";
        $qSubservicio .= "FROM $cAlfa.lpar0153 ";
        $qSubservicio .= "WHERE ";
        $qSubservicio .= "cseidxxx = \"{$vCondiServ['cseidxxx']}\" AND ";
        $qSubservicio .= "sersapxx = \"{$vCondiServ['sersapxx']}\"";
        $xSubservicio  = f_MySql("SELECT","",$qSubservicio,$xConexion01,"");
        if (mysql_num_rows($xSubservicio) > 0) {
          while ($xRSS = mysql_fetch_array($xSubservicio)) {
            $cSubservicio .= $xRSS['subidxxx'] . "~";
          }
          $cSubservicio = rtrim($cSubservicio, "~");
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
          document.forms['frgrm']['cCseSubServ'].value = "<?php echo $cSubservicio ?>";
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
      }
    }
    ?>
  </body>
</html>

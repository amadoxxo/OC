<?php
  namespace openComex;
  /**
	 * --- Descripcion: Formulario con los filtros para generar el reporte de Condiciones Comerciales
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
	 * @package openComex
   * @version 001
	 */
  include("../../../../../financiero/libs/php/utility.php");
?>
<html>
	<head>
		<title>Reportes</title>
	  <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
   	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
   	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
   	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
   	<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
   	<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
	  <script languaje = 'javascript'>
  		function fnRetorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
  	  }

			function fnLinks(xLink,xSwitch) {
				var zX    = screen.width;
				var zY    = screen.height;
				switch (xLink) {
				  case "cCliId":
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
              var zRuta  = "frccc150.php?gWhat=VALID&gFunction=cCliNom&gCliNom="+document.forms['frgrm']['cCliNom'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frccc150.php?gWhat=WINDOW&gFunction=cCliNom&gCliNom="+document.forms['frgrm']['cCliNom'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
	 			}
			}

			function fnGenerar() {
        var nSwitch = 0;
        var dDesde = document.forms['frgrm']['dDesde'].value;
        var dHasta = document.forms['frgrm']['dHasta'].value;
        var ini    = dDesde.replace('-','');
        var fin    = dHasta.replace('-','');
        var fsi    = '<?php echo date('Y-m-d') ?>';
        var fsis   = fsi.replace('-','');
        var fsis1  = fsis.replace('-','');
        var ini2   = ini.replace('-','');
        var fin2   = fin.replace('-','');

        inii = 1 * ini2;
        fini = 1 * fin2;
        fsi2 = 1 * fsis1;

        if (document.forms['frgrm']['dDesde'].value == "" || document.forms['frgrm']['dDesde'].value == "0000-00-00" ||
          document.forms['frgrm']['dHasta'].value == "" || document.forms['frgrm']['dHasta'].value == "0000-00-00")
        {
				  alert("El Rango de Fechas es Obligatorio, Verifique.");
          nSwitch = 1;
        }

        if (fini < inii){
          alert('Fecha Desde no pude ser Mayor a la Fecha Hasta, Verifique.');
          document.forms['frgrm']['dHasta'].focus();
          nSwitch = 1;
        }

        var cTipo = 0;
  		  for (i=0;i<2;i++){
  		    if (document.forms['frgrm']['rTipo'][i].checked == true){
  		  	  cTipo = i+1;
  		      break;
  		    }
  		  }

        if (nSwitch == 0) {
					var zRuta = 'frcccreg.php?gEstado='+document.forms['frgrm']['cEstado'].value+
                      '&gTipo='+document.forms['frgrm']['rTipo'].value+
                      '&gCliId='+document.forms['frgrm']['cCliId'].value+
                      '&gCliNom='+document.forms['frgrm']['cCliNom'].value+
                      '&gDesde='+document.forms['frgrm']['dDesde'].value+
                      '&gHasta='+document.forms['frgrm']['dHasta'].value;

          if(cTipo == 2){        			  	
            parent.fmpro.location = zRuta;
          }else{
            var zX      = screen.width;
            var zY      = screen.height;
            var zNx     = 0;  				
            var zNy     = 0;
            var zWinPro = "width="+zX+",scrollbars=1,height="+zY+",resizable=YES,left="+zNx+",top="+zNy;
            zWindow     = window.open(zRuta,'zWinTrp',zWinPro);
            zWindow.focus();
          }
        }
			}

      function chDate(fld){
        var val = fld.value;
        if (val.length > 0){
          var ok = 1;

          if (val.length < 10){
            alert('Formato de Fecha debe ser aaaa-mm-dd');
            fld.value = '';
            fld.focus();
            ok = 0;
          }

          if(val.substr(4,1) == '-' && val.substr(7,1) == '-' && ok == 1){
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
          }else{
            if(val.length > 0){
              alert('Fecha erronea, verifique');
            }
            fld.value = '';
            fld.focus();
          }
        }
      }
	  </script>
  </head>
	<body>
		<form name='frgrm' method="POST">
      <center>
        <table width="420" cellspacing="0" cellpadding="0" border="0"><tr><td>
          <fieldset>
            <legend>Generacion de Reportes </legend>
            <table border = '0' cellpadding = '0' cellspacing = '0' width='420'>
              <?php $zCol = f_Format_Cols(21);
              echo $zCol;?>
              <tr>
                <td class="name" colspan = "7"><br>Desplegar en:</td>
                <td class="name" colspan = "7"><br>
                  <input type="radio" name="rTipo" value="1" checked>Pantalla
                </td>
                <td class="name" colspan = "5"><br>
                  <input type="radio" name="rTipo" value="2">Excel
                </td>
              </tr>
              <tr>
                <td Class = "name" colspan = "4"><br>
                  <a href = "javascript:document.forms['frgrm']['cCliId'].value = '';
                                        document.forms['frgrm']['cCliNom'].value = '';
                                        document.forms['frgrm']['cCliDV'].value  = '';
                                        fnLinks('cCliId','VALID')"><label>Nit</label></a><br>
                  <input type = "text" Class = "letra" style = "width:080;text-align:center" name = "cCliId"
                        onBlur = "javascript:this.value=this.value.toUpperCase();
                                              fnLinks('cCliId','VALID');
                                              this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                        onFocus = "javascript:document.forms['frgrm']['cCliId'].value = '';
                                            document.forms['frgrm']['cCliNom'].value  = '';
                                            document.forms['frgrm']['cCliDV'].value   = '';
                                            this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                </td>
                <td Class = "name" colspan = "1"><br><label>Dv</label><br>
                  <input type = "text" Class = "letra" style = "width:020;text-align:center" name = "cCliDV" readonly>
                </td>
                <td Class = "name" colspan = "16"><br><label>Cliente</label><br>
                  <input type = "text" Class = "letra" style = "width:320" name = "cCliNom"
                        onBlur = "javascript:this.value=this.value.toUpperCase();
                                              fnLinks('cCliNom','VALID');
                                              this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                        onFocus = "javascript:document.forms['frgrm']['cCliId'].value = '';
                                            document.forms['frgrm']['cCliNom'].value  = '';
                                            document.forms['frgrm']['cCliDV'].value   = '';
                                            this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                </td>
                <td colspan="0">
                  <input type="hidden" name="cCliSap">
                </td>
              </tr>
              <tr>
                <td Class = "name" colspan = "3"><br>Estado:</td>
                <td class="name" colspan = "6"><br>
                  <select Class = "letrase" name = "cEstado" style = "width:120">
                    <option value = "TODOS" selected>TODOS</option>
                    <option value = "ACTIVO">ACTIVO</option>
                    <option value = "INACTIVO">INACTIVO</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td class="name" colspan = "6"><br>Rango De Fechas:</td>
                <td class="name" colspan = "2"><br>
                  <a href='javascript:show_calendar("frgrm.dDesde")' id="id_href_dDesde">Del</a>
                </td>
                <td class="name" colspan = "5"><br>
                  <input type="text" name="dDesde" style = "width:100;text-align:center"
                      onblur="javascript:chDate(this);">
                </td>
                <td class="name" colspan = "3"><br>
                  <center><a href='javascript:show_calendar("frgrm.dHasta")' id="id_href_dHasta">Al</a></center>
                </td>
                <td class="name" colspan = "5"><br>
                  <input type="text" name="dHasta" style = "width:100;text-align:center"
                    onblur="javascript:chDate(this);">
                </td>
              </tr>
            </table>
          </fieldset>
        <center>
        <table border="0" cellpadding="0" cellspacing="0" width="420">
          <tr height="21">
            <td width="238" height="21"></td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = 'javasript:fnGenerar()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Generar</td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
          </tr>
        </table>
      </center>
    </form>
  </body>
</html>
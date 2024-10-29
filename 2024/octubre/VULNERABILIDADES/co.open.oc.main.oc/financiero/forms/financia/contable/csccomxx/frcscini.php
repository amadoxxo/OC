<?php
  namespace openComex;
   /**
   * Imprime Consecutivos Comprobantes.
   * --- Descripcion: Permite Imprimir Consecutivos Comprobantes.
   * @author Yulieth Campos <ycampos@opentecnologia.com.co>
   * @version 002
   */

  include("../../../../libs/php/utility.php");

  #Busco los comprobantes del sistema
  $qDatExt  = "SELECT DISTINCT comidxxx ";
  $qDatExt .= "FROM $cAlfa.fpar0117 ";
  $qDatExt .= "WHERE ";
  $qDatExt .= "regestxx = \"ACTIVO\" ORDER BY comidxxx";
  $xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");


//Consulto procesos bg
  $cAno = date('Y');

  $dHoy = date('Y-m-d');

  $qSysProbg  = "SELECT * ";
  $qSysProbg .= "FROM $cBeta.sysprobg ";
  $qSysProbg .= "WHERE ";
  $qSysProbg .= "DATE(regdcrex) =\"$dHoy\" AND ";
  $qSysProbg .= "regusrxx = \"$kUser\" AND ";
  $qSysProbg .= "pbadbxxx = \"$cAlfa\" AND ";
  $qSysProbg .= "pbamodxx = \"FACTURACION\" AND ";
  $qSysProbg .= "pbatinxx = \"CSCCOMPROBANTES\" ";
  $qSysProbg .= "ORDER BY regdcrex DESC";
  $xSysProbg  = f_MySql("SELECT", "", $qSysProbg, $xConexion01, "");

  $mArcProBg = array();

  while ($xRB = mysql_fetch_array($xSysProbg)) {
    $vArchivos = explode("~", trim($xRB['pbaexcxx'], "~"));
    for ($nA = 0; $nA < count($vArchivos); $nA++) {
      $nInd_mArcProBg = count($mArcProBg);
      $cRuta = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $vArchivos[$nA];
      if ($vArchivos[$nA] != "" && file_exists($cRuta)) {
        $mArcProBg[$nInd_mArcProBg]['pbaexcxx'] = $vArchivos[$nA];
      } else {
        $mArcProBg[$nInd_mArcProBg]['pbaexcxx'] = "";
      }

      $mArcProBg[$nInd_mArcProBg]['pbaidxxx'] = $xRB['pbaidxxx'];
      $mArcProBg[$nInd_mArcProBg]['regunomx'] = $xRB['regunomx'];

      if ($xRB['regestxx'] != "INACTIVO") {
        $nTieEst = round(((strtotime(date('Y-m-d H:i:s')) - strtotime($xRB['regdinix'])) / ($xRB['pbatxixx'] * $xRB['pbacrexx'])), 2) . "&#37";
      } else {
        $nTieEst = "";
      }
      
      $mArcProBg[$nInd_mArcProBg]['pbarespr'] = $xRB['pbarespr'];
      $mArcProBg[$nInd_mArcProBg]['pbaerrxx'] = $xRB['pbaerrxx'];
      $mArcProBg[$nInd_mArcProBg]['regestxx'] = ($xRB['regdinix'] != "0000-00-00 00:00:00" && $xRB['regdfinx'] == "0000-00-00 00:00:00") ? "EN PROCESO" : $xRB['regestxx'];

      $mPost = f_Explode_Array($xRB['pbapostx'], "|", "~");
      for ($nP = 0; $nP < count($mPost); $nP++) {
        if ($mPost[$nP][0] != "") {
          $mArcProBg[$nInd_mArcProBg][$mPost[$nP][0]] = $mPost[$nP][1];
        }
      }
    }
  }
//Consulto procesos bg
?>
<html>
  <head>
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
    <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
    <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
    <script language="javascript">
      function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        parent.fmwork.location='<?php echo $cPlesk_Forms_Directory ?>/frproces.php';
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
      }

      function fnCargarComprobantes(xSecuencia) {

        if (xSecuencia == document.forms['frgrm']['nSecuencia'].value) {
          //Se muestra la ventana de seriales
          var x = screen.width;
          var y = screen.height;
          var nx = (x - 500) / 2;
          var ny = (y - 400) / 2;
          var str = 'width=500,scrollbars=1,height=400,left=' + nx + ',top=' + ny;

          fnMarcarComprobantes();
          document.forms['frnav']['cComId'].value     = document.forms['frgrm']['cComId'].value;
          document.forms['frnav']['cComCod'].value    = document.forms['frgrm']['cComCod'+xSecuencia].value;
          document.forms['frnav']['nSecuencia'].value = xSecuencia;

          msg = window.open('', 'wincom', str);
          document.forms['frnav'].action = 'frpar117.php';
          document.forms['frnav'].target = 'wincom';
          document.forms['frnav'].submit();
          msg.focus();
        }
      }

      function fnMarcarComprobantes() {
        document.forms['frnav']['cComprobantes'].value="|";
        switch (document.forms['frgrm']['nSecuencia'].value) {
          case "1":
            if (document.forms['frgrm']['cComId1'].value != "" && document.forms['frgrm']['cComCod1'].value != "") {
              document.forms['frnav']['cComprobantes'].value += document.forms['frgrm']['cComId1'].value+"~"+document.forms['frgrm']['cComCod1'].value+"|";
            }
          break;
          default:
            for (i=0;i<document.forms['frgrm']['nSecuencia'].value;i++) {
              if (document.forms['frgrm']['cComId'+(i+1)].value != "" && document.forms['frgrm']['cComCod'+(i+1)].value != "") {
                document.forms['frnav']['cComprobantes'].value += document.forms['frgrm']['cComId'+(i+1)].value+"~"+document.forms['frgrm']['cComCod'+(i+1)].value+"|";
              }
            }
          break;
        }
      }

      function fnAdicionar(){
        if (document.forms['frgrm']['cComId'+document.forms['frgrm']['nSecuencia'].value].value != "" &&
            document.forms['frgrm']['cComCod'+document.forms['frgrm']['nSecuencia'].value].value != "") {
          fnAddComprobante();
        }
        fnCargarComprobantes(document.forms['frgrm']['nSecuencia'].value);
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

      /*Carga los comprobantes*/
      function fnAddComprobante() {
        var cGrid      = document.getElementById("Grid_Comprobante");
        var nLastRow   = cGrid.rows.length;
        var nSecuencia = nLastRow+1;
        var cTableRow  = cGrid.insertRow(nLastRow);
        var cComId     ='cComId'  +nSecuencia; // Codigo Comprobante
        var cComCod    ='cComCod' +nSecuencia; // Codigo Comprobante
        var cComDes    ='cComDes' +nSecuencia; // Descripcion Comprobante

        var TD_xAll = cTableRow.insertCell(0);
        TD_xAll.innerHTML   = "<input type = 'text' Class = 'letra' style = 'width:030;text-align:left' name = "+cComId+" id = "+cComId+" readonly>";

        TD_xAll = cTableRow.insertCell(1);
        TD_xAll.innerHTML   = "<input type = 'text' Class = 'letra' name = "+cComCod+" id = "+cComCod+" style = 'width:040;text-align:center' " +
                              "onBlur = 'javascript:this.value=this.value.toUpperCase(); "+
                              "javascript:fnCargarComprobantes(\""+nSecuencia+"\")'>";

        TD_xAll = cTableRow.insertCell(2);
        TD_xAll.innerHTML   = "<input type = 'text' Class = 'letra' style = 'width:240;text-align:left' name = "+cComDes+" id = "+cComDes+" onKeyUp='javascript:fnEnter(event,this.name,\"Grid_Comprobante\");' readonly>";

        TD_xAll = cTableRow.insertCell(3);
        TD_xAll.innerHTML   = "<input type='button' value='X' style = 'width:020;text-align:center;cursor:pointer' id = "+nSecuencia+"  onClick = 'javascript:fnDeleteRow(this.value,\""+nSecuencia+"\",\"Grid_Comprobante\");' title='Eliminar'>";

        document.forms['frgrm']['nSecuencia'].value = nSecuencia;
      }

      function fnEnter(e,xName,xGrilla) {
        var nSecuencia = document.forms['frgrm']['nSecuencia'].value;
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
        if (code == 13) {
          switch (xGrilla) {
            case "Grid_Comprobante":
              if (xName == 'cComDes'+eval(document.forms['frgrm']['nSecuencia'].value)) {
                fnAddComprobante();
              }
            break;
          }
        }
      }

      function fnDeleteRow(xNumRow,xSecuencia,xTabla) {
				switch (xTabla) {
					case "Grid_Comprobante":
						var cGrid = document.getElementById(xTabla);
						var nLastRow = cGrid.rows.length;
						if (nLastRow > 1 && xNumRow == "X") {
							if (confirm("Realmente Desea Eliminar el Comprobante "+document.forms['frgrm']['cComId'+xSecuencia].value+"-"+document.forms['frgrm']['cComCod'+xSecuencia].value+"?")){
					  		if(xSecuencia < nLastRow){
		            	var j=0;
		             	for(var i=xSecuencia;i<nLastRow;i++){
		           	  	j = parseFloat(i)+1;
				            document.forms['frgrm']['cComId'   + i].value = document.forms['frgrm']['cComId'   + j].value;
				            document.forms['frgrm']['cComCod'  + i].value = document.forms['frgrm']['cComCod'  + j].value;
				            document.forms['frgrm']['cComDes'  + i].value = document.forms['frgrm']['cComDes'  + j].value;
		             	}
		           	}
		           	cGrid.deleteRow(nLastRow - 1);
		           	document.forms['frgrm']['nSecuencia'].value = nLastRow - 1;
					  	}
						} else {
							alert("No se Pueden Eliminar Todas las Secuencias, Verifique.");
						}
					break;
					default: //No hace nada
					break;
				}
			}

      /*FUNCION DE SELECT PARA CONSULTA*/
      function fnGenSql()  {
        var band = 0;
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

        if(fini > fsi2 ){
           alert('Fecha Final no puede ser mayor a la Fecha de Hoy,verifique');
           document.forms['frgrm']['dDesde'].focus();
           band = 1;
        }

        if (fini < inii){
          alert('Fecha Final es Menor a Inicial,verifique');
          document.forms['frgrm']['dHasta'].focus();
          band = 1;
        }

        if(band != 1){
          if (document.forms['frgrm']['dDesde'].value.length >   0   &&
              document.forms['frgrm']['dHasta'].value.length >   0   &&
              band == 0){
            var cTipo = 0;
            for (i=0;i<3;i++){
              if (document.forms['frgrm']['rTipo'][i].checked == true){
                cTipo = i+1;
                break;
              }
            }

            if (cTipo != 2) {
              document.forms['frgrm']['cEjProBg'].checked = false;
              document.forms['frgrm']['cEjProBg'].value = "NO";
            }

            fnMarcarComprobantes();
            document.forms['frnav']['cTipo'].value    = cTipo;
            document.forms['frnav']['cComId'].value   = document.forms['frgrm']['cComId'].value;
            document.forms['frnav']['dDesde'].value   = document.forms['frgrm']['dDesde'].value;
            document.forms['frnav']['dHasta'].value   = document.forms['frgrm']['dHasta'].value;
            document.forms['frnav']['cComIdc'].value  = document.forms['frgrm']['cComIdc'].value;
            document.forms['frnav']['cComCodc'].value = document.forms['frgrm']['cComCodc'].value;
            document.forms['frnav']['cComCscc'].value = document.forms['frgrm']['cComCscc'].value;
            document.forms['frnav']['cOrd'].value     = document.forms['frgrm']['cOrd'].value;
            document.forms['frnav']['cEjProBg'].value = document.forms['frgrm']['cEjProBg'].value;

            if(cTipo == 2){
              document.forms['frnav'].action = 'frcscprn.php';
              document.forms['frnav'].target = 'fmpro';
              document.forms['frnav'].submit();
            }else{
              var zX      = screen.width;
              var zY      = screen.height;
              var zNx     = (zX-30)/2;
              var zNy     = (zY-100)/2;
              var zNy2    = (zY-100);
              var zWinPro = "width="+zX+",scrollbars=1,height="+zNy2+",left="+1+",top="+50;
              var cNomVen = 'zWinTrp'+Math.ceil(Math.random()*1000);
              msg = window.open('', cNomVen, zWinPro);
              document.forms['frnav'].action = 'frcscprn.php';
              document.forms['frnav'].target = cNomVen;
              document.forms['frnav'].submit();
              msg.focus();
            }
          } else {
            alert("Verifique Tipo de Comprobante, Rango de Fechas, o Documento Cruce.  No Pueden ser Vacios");
          }
        }
      }

      function fnHabilitarProBg(cTipo){
        if(cTipo == 2){
          document.getElementById('EjProBg').style.display = '';
        } else{
          document.forms['frgrm']['cEjProBg'].checked = false;
          document.forms['frgrm']['cEjProBg'].value = "NO";
          document.getElementById('EjProBg').style.display = 'none';
        }
      }

      function fnRecargar() {
        parent.fmwork.location = "<?php echo substr($_SERVER['PHP_SELF'], (strrpos($_SERVER['PHP_SELF'], "/") + 1), strlen($_SERVER['PHP_SELF'])) ?>";
      }

      function fnDescargar(xArchivo){
        parent.fmwork.location = "frgendoc.php?cRuta="+xArchivo;
      }
    </script>
  </head>
  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
    <form name='frnav' action='fpar0117.php' method="POST" target = "fmpro">
      <input type = "hidden" name = "cTipo">
      <input type = "hidden" name = "cComId">
      <input type = "hidden" name = "cComCod">
      <input type = "hidden" name = "dDesde">
      <input type = "hidden" name = "dHasta">
      <input type = "hidden" name = "cComIdc">
      <input type = "hidden" name = "cComCodc">
      <input type = "hidden" name = "cComCscc">
      <input type = "hidden" name = "cOrd">
      <input type = "hidden" name = "nSecuencia">
      <input type = "hidden" name = "cEjProBg">
      <textarea name="cComprobantes" id="cComprobantes"></textarea>
      <script languaje = "javascript">
        document.getElementById('cComprobantes').style.display="none";
      </script>
    </form>
    <center>
      <table border ="0" cellpadding="0" cellspacing="0" width="460">
        <tr>
          <td>
            <form name='frgrm' action='frcscprn.php' method="POST" target = "fmpro">
              <input type ="hidden" name ="nSecuencia" value=""  >
              <center>
                <fieldset>
                  <legend>Consulta Consecutivos de Comprobantes </legend>
                  <table border="2" cellspacing="0" cellpadding="0" width="500">
                    <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="heigth:25">
                      <td class="name"><center><h4><br>REPORTE CONSECUTIVOS DE COMPROBANTES</h4></center></td>
                    </tr>
                  </table>
                  <table border = '0' cellpadding = '0' cellspacing = '0' width='500'>
                    <?php $nCol = f_Format_Cols(21);
                    echo $nCol;?>
                    <tr>
                      <td class="name" colspan = "7"><br>Desplegar en:</td>
                      <td class="name" colspan = "5"><br>
                        <input type="radio" name="rTipo" value="1" checked onclick="fnHabilitarProBg(this.value)">Pantalla
                      </td>
                      <td class="name" colspan = "5"><br>
                         <input type="radio" name="rTipo" value="2" onclick="fnHabilitarProBg(this.value)">Excel
                      </td>
                      <td class="name" colspan = "4"><br>
                        <input type="radio" name="rTipo" value="3" onclick="fnHabilitarProBg(this.value)">Pdf
                      </td>
                    </tr>
                    <tr>
                      <td class="name" colspan = "7"><br>Ordenar Por:</td>
                      <td Class = "name" colspan = "14"><br>
                        <select Class = "letrase" name = "cOrd" style = "width:140">
                          <option value = 'comidxxx' selected>DOCUMENTO</option>
                          <option value = 'comcsc2x'>CONSECUTIVO</option>
                          <option value = 'regfcrex'>CREADO</option>
                          <option value = 'regusrxx'>USUARIO</option>
                          <option value = 'regestxx'>ESTADO</option>
                      </select>
                    </td>
                   </tr>
                   <tr>
                     <td class="name" colspan = "7"><br>Tipo Comprobante:</td>
                     <td Class = "name" colspan = "14"><br>
                        <select Class = "letrase" name = "cComId" style = "width:140">
                          <option value = '' selected>TODOS</option>
                          <?php while ($xRDE = mysql_fetch_array($xDatExt)) { ?>
                           <option value = '<?php echo $xRDE['comidxxx'] ?>'><?php echo $xRDE['comidxxx'] ?></option>
                          <?php } ?>
                        </select>
                      </td>
                   </tr>
                 </table>
                 <table border = '0' cellpadding = '0' cellspacing = '0' width='500' id="tblComprobantes">
                   <?php $nCol = f_Format_Cols(21);
                   echo $nCol;?>
                   <tr>
                     <td class="name" colspan = "8"><br></td>
                     <td class="name" colspan = "13"><br>
                       <table border = "0" cellpadding = "0" cellspacing = "0" width="330">
                         <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
                           <td class="name" style = "width:030;padding:2px">Id</td>
                           <td class="name" style = "width:040;padding:2px">Cod</td>
                           <td class="name" style = "width:300;padding:2px">Descripci&oacute;n</td>
                           <td class="name" style = "width:030;padding:2px;text-align:center"><img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_create-dir_bg.gif" onClick ="javascript:fnAdicionar()" style = "cursor:hand" alt="Adicionar Comprobante"></td>
                         </tr>
                       </table>
                        <table border = "0" cellpadding = "0" cellspacing = "0"  width="280" id = "Grid_Comprobante"></table>
                        <script languaje = "javascript">
                          fnAddComprobante();
                        </script>
                      </td>
                   </tr>
                </table>
                <table border = '0' cellpadding = '0' cellspacing = '0' width='500'>
                  <?php $nCol = f_Format_Cols(21);
                  echo $nCol;?>
                   <tr>
                     <td class="name" colspan = "8"><br>Rango De Fechas:</td>
                     <td class="name" colspan = "1"><br>
                       <a href='javascript:show_calendar("frgrm.dDesde")' id="id_href_dDesde">De</a>
                     </td>
                     <td class="name" colspan = "5"><br>
                        <input type="text" name="dDesde" style = "width:135;text-align:center"
                           onblur="javascript:chDate(this);">
                     </td>
                     <td class="name" colspan = "1"><br>
                       <center><a href='javascript:show_calendar("frgrm.dHasta")' id="id_href_dHasta">A</a></center>
                     </td>
                     <td class="name" colspan = "6"><br>
                        <input type="text" name="dHasta" style = "width:140;text-align:center"
                          onblur="javascript:chDate(this);">
                     </td>
                   </tr>
                   <tr>
                     <td class="name" colspan = "8"><br>Documento Cruce:</td>
                     <td Class = "name" colspan = "4"><br>Comprobante<br>
                        <input type = "text" Class = "letra" style = "width:110;text-align:center" name = "cComIdc"
                          onfocus="javascript:document.forms['frgrm']['cComIdc'].value  = '';
                                              document.forms['frgrm']['cComCodc'].value = '';
                                              document.forms['frgrm']['cComCscc'].value  = '';
                                              this.style.background='#00FFFF'"
                          onBlur = "javascript:this.value=this.value.toUpperCase();
                                               this.style.background='#FFFFFF'">
                      </td>
                      <td Class = "name" colspan = "2"><br>C&oacute;digo<br>
                        <input type = "text" Class = "letra" style = "width:60;text-align:center" name = "cComCodc" maxlength="4">
                      </td>
                      <td Class = "name" colspan = "7"><br>Consecutivo<br>
                        <input type = "text" Class = "letra" style = "width:165" name = "cComCscc"
                          onBlur = "javascript:this.value=this.value.toUpperCase();">
                    </td>
                    </tr>
                    <tr id="EjProBg" style="display: none">
                      <td Class = "name" colspan = "25"><br>
                        <label><input type="checkbox" name="cEjProBg" value ="SI" onclick="javascript:if(this.checked == true) { this.value = 'SI'} else { this.value = 'NO'}" checked>Ejecutar Proceso en Background</label>
                      </td>
                  </tr>
                  </table>
                  <!-- Grilla de comprobante -->
                </fieldset>
                <center>
                  <table border="0" cellpadding="0" cellspacing="0" width="430">
                    <tr height="21">
                      <td width="250" height="21"></td>
                      <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:hand" onClick = 'javasript:fnGenSql()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Generar</td>
                      <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
                    </tr>
                  </table>
                </center>
              </form>
          </td>
        </tr>
      </table>
    </center>
    <?php if (count($mArcProBg) > 0) { ?>
		<center>
			<table border="0" cellpadding="0" cellspacing="0" width="500">
				<tr>
					<td Class="name" colspan="19"><br>
						<fieldset>
							<legend>Reportes Generados. Fecha [<?php echo date('Y-m-d'); ?>]</legend>
							<label>
								<table border="0" cellspacing="1" cellpadding="0" width="500">
									<tr bgcolor='<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="height:20px">
										<td align="center"><strong>Usuario</strong></td>
										<td align="center"><strong>Periodo</strong></td>
										<td align="center"><strong>Tipo</strong></td>
										<td align="center"><strong>Doc. Cruce</strong></td>
										<td align="center"><strong>Resultado</strong></td>
										<td align="center"><strong>Estado</strong></td>
										<td align="center"><img src="<?php echo $cPlesk_Skin_Directory ?>/b_cambest.gif" onClick="javascript:fnRecargar()" style="cursor:pointer" title="Recargar"></td>
									</tr>
									<?php for ($i = 0; $i < count($mArcProBg); $i++) {
										$cColor = "{$vSysStr['system_row_impar_color_ini']}";
										if ($i % 2 == 0) {
											$cColor = "{$vSysStr['system_row_par_color_ini']}";
										}
									?>
										<tr bgcolor="<?php echo $cColor ?>">
											<td style="padding:2px"><?php echo $mArcProBg[$i]['regunomx']; ?></td>
											<td style="padding:2px"><?php echo "De " . $mArcProBg[$i]['dDesde'] . " A " . $mArcProBg[$i]['dHasta']; ?></td>
											<td style="padding:2px"><?php echo $mArcProBg[$i]['cComId']?></td>
											<td style="padding:2px"><?php echo $mArcProBg[$i]['cComCodc']?></td>
											<td style="padding:2px"><?php echo $mArcProBg[$i]['pbarespr']; ?></td>
											<td style="padding:2px"><?php echo $mArcProBg[$i]['regestxx']; ?></td>
											<td>
                        <?php if ($mArcProBg[$i]['pbaexcxx'] != "") { ?>
                          <a href = "javascript:fnDescargar('<?php echo $mArcProBg[$i]['pbaexcxx']; ?>')">
                            Descargar
                          </a>
                        <?php } ?>
                        <?php if ($mArcProBg[$i]['pbaerrxx'] != "") { ?>
                          <a href = "javascript:alert('<?php echo str_replace(array("<br>","'",'"'),array("\n"," "," "),$mArcProBg[$i]['pbaerrxx']) ?>')">
                            Ver
                          </a>
                        <?php } ?>
                      </td>
										</tr>
									<?php } ?>
								</table>
							</label>
						</fieldset>
					</td>
				</tr>
			</table>
		</center>
	<?php } ?>
  </body>
</html>

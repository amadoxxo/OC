<?php
/**
	 * Proceso Autorización Modificar Campos Pedidos
	 * --- Descripcion: Permite Crear un Nueva Autorización para Modificar Campos Pedidos.
	 * @author Elian Amado <elian.amado@openits.co>
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
    <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
    <script language="javascript">
      function fnRetorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
      }
      
      function fnLinks(xLink, xSwitch, xSecuencia = "") {
        var zX    = screen.width;
        var zY    = screen.height;
        switch (xLink){
          // Cliente
          case "cCliId":
            if (xSwitch == "VALID") {
              var cRuta  = "framc150.php?gWhat=VALID"+
                                        "&gFunction=cCliId"+
                                        "&gSecuencia="+xSecuencia+
                                        "&gCliId="+document.forms['frgrm']['cCliId'+xSecuencia].value;
              parent.fmpro.location = cRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var cRuta   = "framc150.php?gWhat=WINDOW"+
                                        "&gFunction=cCliId"+
                                        "&gSecuencia="+xSecuencia+
                                        "&gCliId="+document.forms['frgrm']['cCliId'+xSecuencia].value;
              zWindow = window.open(cRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
            break;
          case "cPedComCsc":
            var nSwitch = 0;
            if (document.forms['frgrm']['cCliId'+xSecuencia].value == "") {
              nSwitch = 1;
              alert('Debe Seleccionar un Cliente para Consultar el Pedido,\n');
            }

            if (nSwitch == 0) {
              if (xSwitch == "VALID") {
                    var zRuta = "framcped.php?gWhat="+xSwitch+
                                            "&gFunction="+xLink+
                                            "&gSecuencia="+xSecuencia+
                                            "&gPedComCsc="+document.forms['frgrm']['cPedComCsc'+xSecuencia].value +
                                            "&gCliId=" +document.forms['frgrm']['cCliId'+xSecuencia].value;
                    parent.fmpro.location = zRuta;
              } else if(xSwitch == "WINDOW") {
                var nNx      = (zX-500)/2;
                var nNy      = (zY-250)/2;
                var zWinPro  = "width=500,scrollbars=1,height=250,left="+nNx+",top="+nNy;
                var zRuta = "framcped.php?gWhat="+xSwitch+
                                        "&gFunction="+xLink+
                                        "&gSecuencia="+xSecuencia+
                                        "&gPedComCsc="+document.forms['frgrm']['cPedComCsc'+xSecuencia].value +
                                        "&gCliId=" +document.forms['frgrm']['cCliId'+xSecuencia].value;
                zWindow = window.open(zRuta,xLink,zWinPro);
                zWindow.focus();
              }
            }
          break;
        }
      }
      
      function f_Marca() {//Marca y Desmarca los registros seleccionados en la tabla de Conceptos de Cobro
        if (document.forms['frgrm']['nCheckAll'].checked == true){
          if (document.forms['frgrm']['nRecords'].value == 1){
            document.forms['frgrm']['cCheck'].checked=true;
          } else {
              if (document.forms['frgrm']['nRecords'].value > 1){
                for (i=0;i<document.forms['frgrm']['cCheck'].length;i++){
                  document.forms['frgrm']['cCheck'][i].checked = true;
                }
              }
          }
        } else {
          if (document.forms['frgrm']['nRecords'].value == 1){
            document.forms['frgrm']['cCheck'].checked=false;
          } else {
              if (document.forms['frgrm']['nRecords'].value > 1){
                for (i=0;i<document.forms['frgrm']['cCheck'].length;i++){
                  document.forms['frgrm']['cCheck'][i].checked = false;
                }
              }
            }
        }
      }
      
      function f_Carga_Data() {
          document.forms['frgrm']['cComMemo'].value="|";
          switch (document.forms['frgrm']['nRecords'].value) {
            case "1":
              if (document.forms['frgrm']['cCheck'].checked == true) {
                document.forms['frgrm']['cComMemo'].value += document.forms['frgrm']['cCheck'].id+"|";
              }
            break;
            default:
              if (document.forms['frgrm']['cCheck'] !== undefined) {
                for (i=0;i<document.forms['frgrm']['cCheck'].length;i++) {
                  if (document.forms['frgrm']['cCheck'][i].checked == true) {
                    document.forms['frgrm']['cComMemo'].value += document.forms['frgrm']['cCheck'][i].id+"|";
                  }
                }
              }
            break;
          }
          if (document.forms['frgrm']['cComMemo'].value == "|"){
            document.forms['frgrm']['cComMemo'].value = "";
          }
      }
      
      function fnAddNewRow() {
        var cGrid      = document.getElementById("Grid");
        var nLastRow   = cGrid.rows.length;
        var nSecuencia = nLastRow+1;
        var cTableRow  = cGrid.insertRow(nLastRow);
        var cAutSeq    = 'cAutSeq'  + nSecuencia;
        var cSucId     = 'cSucId'   + nSecuencia;
        var cPedIds    = 'cPedIds'  + nSecuencia;
        var cAnio      = 'cAnio'    + nSecuencia;
        // Campos del cliente
        var cCliId     = 'cCliId'  + nSecuencia; // NIT del Cliente
        var cCliDV     = 'cCliDV'  + nSecuencia; // 
        var cCliSap    = 'cCliSap' + nSecuencia; // Codigo SAP del Cliente
        var cCliNom    = 'cCliNom' + nSecuencia; // Nombre o Razón Social del Cliente
        var oBtnDel    = 'oBtnDel' + nSecuencia; // Boton de Borrar Row
        
        //Campos del No. de Pedido
        var cPedComCsc  = 'cPedComCsc' + nSecuencia;
        
        var TD_xAll = cTableRow.insertCell(0);
        TD_xAll.style.width  = "70px";
        TD_xAll.style.border = "1px solid #E6E6E6";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:070px;border:0;text-align:center;padding:2px' name = '"+cAutSeq+"' id = '"+cAutSeq+"' value = '"+f_Str_Pad(nSecuencia,3,"0","STR_PAD_LEFT")+"'readonly>";
        
        TD_xAll = cTableRow.insertCell(1);
        TD_xAll.style.width  = "190px";
        TD_xAll.style.border = "1px solid #E6E6E6";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:190px;border:0;text-align:center;padding:2px' name = '"+cCliId+"' id = '"+cCliId+"' " +
                                      "onBlur = 'javascript:fnLinks(\"cCliId\",\"VALID\", \""+nSecuencia+"\");' >";
        
        TD_xAll = cTableRow.insertCell(2);
        TD_xAll.style.width  = "40px";
        TD_xAll.style.border = "1px solid #E6E6E6";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:040px;border:0;text-align:center;padding:2px' name = '"+cCliDV+"' id = '"+cCliDV+"' readonly>";
        
        TD_xAll = cTableRow.insertCell(3);
        TD_xAll.style.width  = "210px";
        TD_xAll.style.border = "1px solid #E6E6E6";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:210px;border:0;text-align:center;padding:2px' name = '"+cCliSap+"' id = '"+cCliSap+"' readonly>";
        
        TD_xAll = cTableRow.insertCell(4);
        TD_xAll.style.width  = "350px";
        TD_xAll.style.border = "1px solid #E6E6E6";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:350px;border:0;text-align:center;padding:2px' name = '"+cCliNom+"' id = '"+cCliNom+"' readonly>";
        
        TD_xAll = cTableRow.insertCell(5);
        TD_xAll.style.width  = "190px";
        TD_xAll.style.border = "1px solid #E6E6E6";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:190px;border:0;text-align:center;padding:2px' name = '"+cPedComCsc+"' id = '"+cPedComCsc+"' " +
                                      "onBlur = 'javascript:fnLinks(\"cPedComCsc\", \"VALID\", \""+nSecuencia+"\");' > " +
                                      "<input type = 'hidden' name = '"+cPedIds+"' id = '"+cPedIds+"' readonly> " +
                                      "<input type = 'hidden' name = '"+cAnio+"' id = '"+cAnio+"' readonly>";
        
        TD_xAll = cTableRow.insertCell(6);
        TD_xAll.style.width  = "20px";
        TD_xAll.innerHTML    = "<input type = 'button' style = 'width:020px;text-align:center' id = "+nSecuencia+" value = 'X' "+
                                      "onClick = 'javascript:fnDeleteRow(this.value,\""+nSecuencia+"\",\"Grid\", this);'>";
        document.forms['frgrm']['nSecuencia'].value = nSecuencia;
      }
      
      function fnAddSubservicio(xId,xRow,xChek) {
    
        var cGrid        = document.getElementById("Grid_Ip");
        var nLastRow     = cGrid.rows.length;
        var nSecuencia   = nLastRow+1;
        var cTableRow    = cGrid.insertRow(nLastRow);
        var cNumPedido   = 'cNumPedido'   + nSecuencia;
        var cCliente     = 'cCliente'     + nSecuencia;
        var cNIT         = 'cNIT'         + nSecuencia;
        var cCodSap      = 'cCodSap'      + nSecuencia;
        var cServicio    = 'cServicio'    + nSecuencia;
        var cSubServicio = 'cSubServicio' + nSecuencia;
        var cSubCerId    = 'cSubCerId'    + nSecuencia;
        var cCheck       = 'cCheck'       + nSecuencia;
        var cPedId       = 'cPedId'       + nSecuencia;
        var cAnioIds     = 'cAnioIds'     + nSecuencia;
        var cObservacion = 'cObservacion' + nSecuencia;
        
        TD_xAll = cTableRow.insertCell(0);
        TD_xAll.style.width  = "140px";
        TD_xAll.style.border = "1px solid #E6E6E6";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:140;border:0;text-align:center;padding:2px' name = '"+cNumPedido+"' id = '"+cNumPedido+"' readonly>";
        
        TD_xAll = cTableRow.insertCell(1);
        TD_xAll.style.width  = "300px";
        TD_xAll.style.border = "1px solid #E6E6E6";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:300;border:0;text-align:left;padding:2px' name = '"+cCliente+"' id = '"+cCliente+"' readonly>" +
                                "<input type='text' name = '"+cNIT+"' id = '"+cNIT+"' readonly>";
        
        TD_xAll = cTableRow.insertCell(2);
        TD_xAll.style.width  = "160px";
        TD_xAll.style.border = "1px solid #E6E6E6";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:160;border:0;text-align:center;padding:2px' name = '"+cCodSap+"' id = '"+cCodSap+"' readonly>";
        
        TD_xAll = cTableRow.insertCell(3);
        TD_xAll.style.width  = "260px";
        TD_xAll.style.border = "1px solid #E6E6E6";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:260;border:0;text-align:left;padding:2px' name = '"+cServicio+"' id = '"+cServicio+"' readonly>";
        
        TD_xAll = cTableRow.insertCell(4);
        TD_xAll.style.width  = "260px";
        TD_xAll.style.border = "1px solid #E6E6E6";
        TD_xAll.innerHTML    = "<input  type = 'text' class = 'letra' style = 'width:260;border:0;text-align:left;padding:2px' name = '"+cSubServicio+"' id = '"+cSubServicio+"' readonly> " +
                                "<input type = 'text' name = '"+cSubCerId+"'    id = '"+cSubCerId+"'    readonly> " +
                                "<input type = 'text' name = '"+cObservacion+"' id = '"+cObservacion+"' readonly> " +
                                "<input type = 'text' name = '"+cAnioIds+"'     id = '"+cAnioIds+"'     readonly> " +
                                "<input type = 'text' name = '"+cPedId+"'       id = '"+cPedId+"'       readonly>";
        
        TD_xAll = cTableRow.insertCell(5);
        TD_xAll.style.width     = "20px";
        TD_xAll.style.textAlign = "center";
        TD_xAll.innerHTML       = "<input type='checkbox' name='cCheck'  value = '"+cCheck+"' id='"+xId+"'"+((xChek) == true ? " checked" : "")+">";
        
        document.forms['frgrm']['nSecuencia_Ip'].value = nSecuencia;
      }
      
      function fnDeleteRow(xNumRow,xSecuencia,xTabla) {
        var cGrid = document.getElementById(xTabla);
        var nLastRow = cGrid.rows.length;
        if (nLastRow > 1 && xNumRow == "X") {
          if (confirm("Realmente Desea Eliminar La Secuencia ["+xSecuencia+"]?")){ 
            if(xSecuencia < nLastRow){
              var j=0;
              for(var i=xSecuencia;i<nLastRow;i++){
                j = parseFloat(i)+1;
                document.forms['frgrm']['cAutSeq'    + i].value = f_Str_Pad(i,3,"0","STR_PAD_LEFT"); 
                document.forms['frgrm']['cCliId'     + i].value = document.forms['frgrm']['cCliId'     + j].value; 
                document.forms['frgrm']['cCliDV'     + i].value = document.forms['frgrm']['cCliDV'     + j].value; 
                document.forms['frgrm']['cCliSap'    + i].value = document.forms['frgrm']['cCliSap'    + j].value;
                document.forms['frgrm']['cCliNom'    + i].value = document.forms['frgrm']['cCliNom'    + j].value;
                document.forms['frgrm']['cPedComCsc' + i].value = document.forms['frgrm']['cPedComCsc' + j].value;
                document.forms['frgrm']['cPedIds'    + i].value = document.forms['frgrm']['cPedIds'    + j].value;
                document.forms['frgrm']['cAnio'   + i].value = document.forms['frgrm']['cAnio'   + j].value;
              }
            }
            cGrid.deleteRow(nLastRow - 1);
            document.forms['frgrm']['nSecuencia'].value = nLastRow - 1;
          }
        } else {
          alert("No se Pueden Eliminar Todas las Secuencias, Verifique.");
        }
      }
      
      function fnBorrarTodos(){
        document.getElementById("Grid").innerHTML = "";
        document.forms['frgrm']['nSecuencia'].value  = 0;
        fnAddNewRow();
      }
      
      function fnValidaciones(){
        if (!document.forms['frgrm']['cObvsPed'].value) {
          alert('El campo de observaciones es obligatorio');
        } else {
          document.forms['frgrm'].target = 'fmpro';
          document.forms['frgrm'].action = 'fraec20g.php';
          document.forms['frgrm']['cModo'].value = 'VALIDARDO';
          document.forms['frgrm'].submit();
        }
      }
      
      function f_VolverAtras () {
        f_Carga_Data();
        document.forms['frgrm'].target             = 'fmwork';
        document.forms['frgrm'].action             = 'framcnue.php';
        document.forms['frgrm']['cStep'].value     = '1';
        document.forms['frgrm']['cStep_Ant'].value = '2';
        document.forms['frgrm'].submit();
      }
      
      function f_Mostrar_u_Ocultar_Objetos(xStep) {
        // Oculto campos de valor FOB y cantidad de formularios que solo aplican para EXPORTACIONES y DTA's.
        switch (xStep) {
          case "1":
            document.getElementById("Grid_Paso1").style.display="block";
            document.getElementById("Grid_Paso2").style.display="none";
          break;
          case "2":
            document.getElementById("Grid_Paso1").style.display="none";
            document.getElementById("Grid_Paso2").style.display="block";
          break;
        }
      }
    
      function fnGuardar() {
        f_Carga_Data();
        document.forms['frgrm'].target='fmpro';
        document.forms['frgrm'].action='fraecgra.php';
        document.forms['frgrm']['nTimesSave'].value++;
        document.forms['frgrm'].submit();
      }
      
    </script>
	</head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="1160">
				<tr>
					<td>
            <fieldset>
              <legend><font color="red"><?php echo (($_COOKIE['kModo'] == 'MASIVA' ) ? "Nueva": $_COOKIE['kModo'])." ".$_COOKIE['kProDes'] ?></font></legend>
              <form name = 'frgrm' action = 'fraecgra.php' method = 'post' target='fmpro'>
                <input type = "hidden" name = "cStep"         value = "<?php echo $_POST['cStep'] ?>">
                <input type = "hidden" name = "cStep_Ant"     value = "<?php echo $_POST['cStep_Ant'] ?>">
                <input type = "hidden" name = "cTarExc"       value = "">
                <input type = "hidden" name = "nRecords"      value = "<?php echo $_POST['nRecords'] ?>">
                <input type = "hidden" name = "nTimesSave"    value = "0">
                <input type = "hidden" name = "nSecuencia"    value = "">
                <input type = "hidden" name = "nSecuencia_Ip" value = "">
                <input type = "hidden" name = "cModo"         value = "">
                <textarea name = "cComMemo"  id = "cComMemo"><?php  echo $_POST['cComMemo'] ?></textarea>
                <textarea name = "cTramites" id = "cTramites"><?php echo $_POST['cTramites'] ?></textarea>
                <center>
                <script languaje = "javascript">
                  document.getElementById("cTramites").style.display="none";
                  document.getElementById("cComMemo").style.display ="none";
                </script>
                
                <div id="Grid_Paso1">
                  <?php
                    if ($_POST['cStep'] == "") {
                      $_POST['cStep'] = "1";
                    }
                  ?>
                  <fieldset id="Grid_Paso1">
                    <legend><b>Seleccione los Pedidos</b></legend>
                    <center>
                        <table border = '0' cellpadding = '0' cellspacing = '0' width='1160'>
                          <?php $nCol = f_Format_Cols(58); echo $nCol;?>
                          <tr height="25px">
                            <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="04" align="left">&nbsp;&nbsp;Suc</td>
                            <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="10" align="center">&nbsp;&nbsp;Nit</td>
                            <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="02" align="center">Dv</td>
                            <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="11" align="center">Cod SAP</td>
                            <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="17" align="center">&nbsp;&nbsp;Raz&oacute;n Social</td>
                            <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="12" align="center">&nbsp;&nbsp;&nbsp;No. Pedido</td>
                            <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="02" align="right">
                              <img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_create-dir_bg.gif" onClick = "javascript:fnAddNewRow()" style = "cursor:pointer" title="Adicionar">
                              <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_drop.png" onClick = "javascript:fnBorrarTodos()" style = "cursor:pointer" title="Eliminar Todos">
                            </td>
                          </tr>
                        </table>
                        <table border = "0" cellpadding = "0" cellspacing = "0" width = "1160" id = "Grid"></table>
                    </center>
                    <script languaje = "javascript">
                      fnAddNewRow();
                    </script>
                  </fieldset>
                  <fieldset>
                    <legend>
                      <b>Observaciones</b>
                    </legend>
                    <textarea name="cObvsPed" id="cObvsPed"></textarea>
                  </fieldset>
                </div>

                <fieldset id="Grid_Paso2">
                  <legend><b>Seleccione los Servicios en que Desea Modificar los Valores</b></legend>
                  <center>
                    <table border = '0' cellpadding = '0' cellspacing = '0' width='1160'>
                      <?php $nCol = f_Format_Cols(58); echo $nCol;?>
                      <tr height="25px">
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="08" align="left">&nbsp;&nbsp;No. Pedido</td>
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="15" align="center">&nbsp;&nbsp;&nbsp;Cliente</td>
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="08" align="center">&nbsp;&nbsp;&nbsp;Cod SAP</td>
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="13" align="center">&nbsp;&nbsp;&nbsp;Servicio</td>
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="13" align="center">&nbsp;&nbsp;Subservicio</td>
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="01" align = "center"><input type="checkbox" name="nCheckAll" onClick = "javascript:f_Marca()"></td>
                      </tr>
                    </table>
                    <table border = "0" cellpadding = "0" cellspacing = "0" width = "1160" id = "Grid_Ip"></table>
                  </center>
                </fieldset>
                
                </center>
              </form>
            </fieldset>
          </td>
        </tr>
      </table>
		</center>
		<center>
		<?php switch ($_POST['cStep']) {
      case "1": ?>
        <table border="0" cellpadding="0" cellspacing="0" id='bnt_Paso1' width="1160">
          <tr>
            <td width="978" height="21"></td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/siguiente.gif" style="cursor:pointer"
              onClick = "javascript:fnValidaciones();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Siguiente
            </td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand"
              onClick ="javascript:fnRetorna();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
            </td>
          </tr>
        </table>
      <?php 
      break;
      case "2": ?>
        <table border="0" cellpadding="0" cellspacing="0" id='bnt_Paso2' width="1160">
          <tr>
            <td width="887" height="21"></td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/anterior.gif" style="cursor:pointer"
              onClick = "javascript:f_VolverAtras();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Anterior</td>
            <td width="91" height="21" Class="name">
              <input type="button" name="name" value="Guardar" style = "background:url(<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif);width:91;height:21;border:0px;font-weight:bold;color:#555555;"
                onclick = "javascript:fnGuardar()"></td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand"
              onClick ="javascript:fnRetorna();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
            </td>
          </tr>
        </table>
        <?php
      break;
    } ?>
    </center>
		<!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
		<script languaje = "javascript">
      f_Mostrar_u_Ocultar_Objetos("<?php echo $_POST['cStep'] ?>");
    </script>
		<?php
      if ($_POST['cStep'] == "2") {

        $mDataDetalle = array();
        $cTraSel = "";
        for ($i=0; $i<$_POST['nSecuencia']; $i++) {

          if ($_POST['cPedComCsc' .($i+1)] != "") {
            
            $cTraSel .= "{$_POST['cPedIds'.($i+1)]}~{$_POST['cAnio'.($i+1)]}|";

            $cAnio   = "{$_POST['cAnio'.($i+1)]}";
            
            $qPedidoDet  = "SELECT ";
            $qPedidoDet .= "$cAlfa.lpca$cAnio.pedidxxx, ";
            $qPedidoDet .= "$cAlfa.lpca$cAnio.comidxxx, ";
            $qPedidoDet .= "$cAlfa.lpca$cAnio.comcodxx, ";
            $qPedidoDet .= "$cAlfa.lpca$cAnio.comprexx, ";
            $qPedidoDet .= "$cAlfa.lpca$cAnio.comcscxx, ";
            $qPedidoDet .= "$cAlfa.lpca$cAnio.comcsc2x, ";
            $qPedidoDet .= "$cAlfa.lpca$cAnio.cliidxxx, ";
            $qPedidoDet .= "$cAlfa.lpar0150.clinomxx, ";
            $qPedidoDet .= "$cAlfa.lpde$cAnio.*, ";
            $qPedidoDet .= "$cAlfa.lpar0011.sersapxx, ";
            $qPedidoDet .= "$cAlfa.lpar0011.serdesxx, ";
            $qPedidoDet .= "$cAlfa.lpar0012.subidxxx, ";
            $qPedidoDet .= "$cAlfa.lpar0012.subdesxx ";
            $qPedidoDet .= "FROM $cAlfa.lpde$cAnio ";
            $qPedidoDet .= "LEFT JOIN $cAlfa.lpca$cAnio ON $cAlfa.lpde$cAnio.pedidxxx = $cAlfa.lpca$cAnio.pedidxxx ";
            $qPedidoDet .= "LEFT JOIN $cAlfa.lpar0011 ON $cAlfa.lpde$cAnio.sersapxx = $cAlfa.lpar0011.sersapxx ";
            $qPedidoDet .= "LEFT JOIN $cAlfa.lpar0012 ON $cAlfa.lpde$cAnio.sersapxx = $cAlfa.lpar0012.sersapxx AND $cAlfa.lpde$cAnio.subidxxx = $cAlfa.lpar0012.subidxxx ";
            $qPedidoDet .= "LEFT JOIN $cAlfa.lpar0150 ON $cAlfa.lpca$cAnio.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
            $qPedidoDet .= "WHERE ";
            $qPedidoDet .= "$cAlfa.lpde$cAnio.pedidxxx = \"{$_POST['cPedIds'.($i+1)]}\" ";
          }
          $xPedidoDet = f_MySql("SELECT","",$qPedidoDet,$xConexion01,"");
          // f_Mensaje(__FILE__,__LINE__,$qPedidoDet." ~ ".mysql_num_rows($xPedidoDet));
          if (mysql_num_rows($xPedidoDet) > 0) {
            while ($xRPD = mysql_fetch_array($xPedidoDet)) {
              $nInd_mDataDetalle = count($mDataDetalle);
              $mDataDetalle[$nInd_mDataDetalle] = $xRPD;
            }
          } 
        } 
        
        //Matriz para verificar si ya habia sido marcado
        $vCheckMarcados = array();
        $vCheckMarcados = explode("|",$_POST['cComMemo']);
        
        for ($i=0; $i<count($mDataDetalle); $i++) {
            
          $cId = $mDataDetalle[$i]['comidxxx']."~".$mDataDetalle[$i]['cliidxxx']."~".$mDataDetalle[$i]['subidxxx'];
          
          if (count($vCheckMarcados) > 0) {
            $mDataDetalle[$i]['excluida'] = false;
            if (in_array($cId, $vCheckMarcados) == true) {
              $mDataDetalle[$i]['excluida'] = true;
            }
          } ?>
          <script languaje = "javascript">
            fnAddSubservicio("<?php echo $cId ?>","<?php echo count($mDataDetalle) ?>","<?php echo $mDataDetalle[$i]['excluida'] ?>");
            
            document.forms['frgrm']['cNumPedido'   + document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo "{$mDataDetalle[$i]['comidxxx']}-{$mDataDetalle[$i]['comprexx']}-{$mDataDetalle[$i]['comcscxx']}"; ?>";
            document.forms['frgrm']['cCliente'     + document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo "{$mDataDetalle[$i]['clinomxx']} [{$mDataDetalle[$i]['cliidxxx']}]"; ?>";
            document.forms['frgrm']['cCodSap'      + document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo "{$mDataDetalle[$i]['sersapxx']}"; ?>";
            document.forms['frgrm']['cServicio'    + document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo "{$mDataDetalle[$i]['serdesxx']}"; ?>";
            document.forms['frgrm']['cSubServicio' + document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo "{$mDataDetalle[$i]['subdesxx']}"; ?>";
            document.forms['frgrm']['cSubCerId'    + document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo "{$mDataDetalle[$i]['subidxxx']}"; ?>";
            document.forms['frgrm']['cNIT'         + document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo "{$mDataDetalle[$i]['cliidxxx']}"; ?>";
            document.forms['frgrm']['cPedId'       + document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo "{$mDataDetalle[$i]['pedidxxx']}"; ?>";
            document.forms['frgrm']['cAnioIds'     + document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo "{$_POST['cAnio'.($i+1)]}"; ?>";
            document.forms['frgrm']['cObservacion' + document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo "{$_POST['cObvsPed']}"; ?>";
          </script>
        <?php }  ?>
        <script languaje="javascript">
          document.forms['frgrm']['cTramites'].value  = "<?php echo $cTraSel ?>";
          document.forms['frgrm']['nSecuencia'].value = "<?php echo $_POST['nSecuencia'] ?>";
          document.forms['frgrm']['nRecords'].value   = "<?php echo count($mDataDetalle) ?>";
        </script>
        <?php
      }
		?>
	</body>
</html>

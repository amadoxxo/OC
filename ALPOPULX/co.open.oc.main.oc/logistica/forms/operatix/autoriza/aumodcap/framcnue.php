<?php
/**
	 * Proceso Autorización Modificar Campos Pedidos
	 * --- Descripcion: Permite Crear un Nueva Autorización para Modificar Campos Pedidos.
	 * @author Elian Amado <elian.amado@openits.co>
	 * @version 001
	 */
	include("../../../../../libs/php/utility.php");
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
      
      function f_Carga_Data() { //Arma cadena para guardar en campo matriz de la sys00121
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
        var cAutSeq    = 'cAutSeq' + nSecuencia;
        var cSucId     = 'cSucId'  + nSecuencia;
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
                                      "onBlur = 'javascript:fnLinks(\"cPedComCsc\", \"VALID\", \""+nSecuencia+"\");' >";
        
        TD_xAll = cTableRow.insertCell(6);
        TD_xAll.style.width  = "20px";
        TD_xAll.innerHTML    = "<input type = 'button' style = 'width:020px;text-align:center' id = "+nSecuencia+" value = 'X' "+
                                      "onClick = 'javascript:fnDeleteRow(this.value,\""+nSecuencia+"\",\"Grid\", this);'>";
        document.forms['frgrm']['nSecuencia'].value = nSecuencia;
      }
      
      function f_Add_New_Row_Ip(xId,xRow,xChek) {
    
        var cGrid      = document.getElementById("Grid_Ip");
        var nLastRow   = cGrid.rows.length;
        var nSecuencia = nLastRow+1;
        var cTableRow  = cGrid.insertRow(nLastRow);
        var cTramite   = 'cTramite'   + nSecuencia;
        var cImportador= 'cImportador'+ nSecuencia;
        var cFacturara = 'cFacturara' + nSecuencia;
        var cServicio  = 'cServicio'  + nSecuencia;
        var cTarifa    = 'cTarifa'    + nSecuencia;
        var cCheck     = 'cCheck'     + nSecuencia;
        
        TD_xAll = cTableRow.insertCell(0);
        TD_xAll.style.width  = "120px";
        TD_xAll.style.border = "1px solid #E6E6E6";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:120;border:0;text-align:left;padding:2px' name = '"+cTramite+"' id = '"+cTramite+"' readonly>";
        
        TD_xAll = cTableRow.insertCell(1);
        TD_xAll.style.width  = "300px";
        TD_xAll.style.border = "1px solid #E6E6E6";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:300;border:0;text-align:left;padding:2px' name = '"+cImportador+"' id = '"+cImportador+"' readonly>";
        
        TD_xAll = cTableRow.insertCell(2);
        TD_xAll.style.width  = "280px";
        TD_xAll.style.border = "1px solid #E6E6E6";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:280;border:0;text-align:left;padding:2px' name = '"+cFacturara+"' id = '"+cFacturara+"' readonly>";
        
        TD_xAll = cTableRow.insertCell(3);
        TD_xAll.style.width  = "340px";
        TD_xAll.style.border = "1px solid #E6E6E6";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:340;border:0;text-align:left;padding:2px' name = '"+cServicio+"' id = '"+cServicio+"' readonly>";
        
        TD_xAll = cTableRow.insertCell(4);
        TD_xAll.style.width  = "80px";
        TD_xAll.style.border = "1px solid #E6E6E6";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:80;border:0;text-align:center;padding:2px' name = '"+cTarifa+"' id = '"+cTarifa+"' readonly>";
        
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
      
      function f_Valida(){//Valida datos de formulario, para poder pintar conceptos de Cobro a excluir
        document.forms['frgrm'].target = 'fmpro';
        document.forms['frgrm'].action = 'fraec20g.php';
        document.forms['frgrm']['cModo'].value = 'VALIDARDO';
        document.forms['frgrm'].submit();
      }
      
      function f_VolverAtras () {
        f_Carga_Data();
        document.forms['frgrm'].target             = 'fmwork';
        document.forms['frgrm'].action             = 'frmasnue.php';
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
                
                <fieldset id="Grid_Paso1"><?php if($_POST['cStep'] == "")	{ $_POST['cStep'] = "1"; } ?>
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
                  <textarea name="" id=""></textarea>
                </fieldset>

                <fieldset id="Grid_Paso2">
                  <legend><b>Seleccione los Conceptos de Cobro a Excluir</b></legend>
                  <center>
                    <b>Nota:</b> Debe seleccionar los Ingresos Propios que desea excluir<br><br>
                    <table border = '0' cellpadding = '0' cellspacing = '0' width='1160'>
                      <?php $nCol = f_Format_Cols(58); echo $nCol;?>
                      <tr height="25px">
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="06" align="left">&nbsp;&nbsp;Tramite</td>
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="15" align="left">&nbsp;&nbsp;&nbsp;Importador</td>
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="15" align="left">&nbsp;&nbsp;&nbsp;Facturar a</td>
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="17" align="left">&nbsp;&nbsp;&nbsp;Servicio</td>
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="04" align="left">&nbsp;&nbsp;Tipo Tarifa</td>
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
              onClick = "javascript:f_Valida();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Siguiente
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
      if ($_POST['cStep'] == "1" && $_POST['cStep_Ant'] == "2") {
        
        //Trayendo la secuencia maxima para ese id de transaccion
        $mTramites = f_Explode_Array($_POST['cTramites'],"|","~");
        $nBand = 0;
        
        for ($i=0; $i<count($mTramites); $i++) {
          if ($mTramites[$i][0] != "") {
            $qTramite  = "SELECT * ";
            $qTramite .= "FROM $cAlfa.sys00121 ";
            $qTramite .= "WHERE ";
            $qTramite .= "sucidxxx  = \"{$mTramites[$i][0]}\" AND ";
            $qTramite .= "docidxxx  = \"{$mTramites[$i][1]}\" AND ";
            $qTramite .= "docsufxx  = \"{$mTramites[$i][2]}\" AND ";
            $qTramite .= "regestxx != \"INACTIVO\" LIMIT 0,1 ";
            $xTramite  = f_MySql("SELECT","",$qTramite,$xConexion01,"");
            // f_Mensaje(__FILE__,__LINE__,$qTramite." ~ ".mysql_num_rows($xTramite));
            if (mysql_num_rows($xTramite) > 0) {
              $vTramites = mysql_fetch_array($xTramite);
              
              //Busco la el nombre del cliente
              $qDatCli  = "SELECT ";
              $qDatCli .= "$cAlfa.SIAI0150.*, ";
              $qDatCli .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
              $qDatCli .= "FROM $cAlfa.SIAI0150 ";
              $qDatCli .= "WHERE ";
              $qDatCli .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$vTramites['cliidxxx']}\" LIMIT 0,1";
              $xDatCli  = f_MySql("SELECT","",$qDatCli,$xConexion01,"");
              if(mysql_num_rows($xDatCli) > 0) {
                $xRDC = mysql_fetch_array($xDatCli);
                $vTramites['clinomxx'] = $xRDC['CLINOMXX'];
              } else {
                $vTramites['clinomxx'] = "CLIENTE SIN NOMBRE";
              }
              
              if ($mTramites[$i][3] == "SI") {
                $qDatCli  = "SELECT ";
                $qDatCli .= "$cAlfa.SIAI0150.*, ";
                $qDatCli .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
                $qDatCli .= "FROM $cAlfa.SIAI0150 ";
                $qDatCli .= "WHERE ";
                $qDatCli .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$mTramites[$i][4]}\" LIMIT 0,1";
                $xDatCli  = f_MySql("SELECT","",$qDatCli,$xConexion01,"");
                if(mysql_num_rows($xDatCli) > 0) {
                  $xRDC = mysql_fetch_array($xDatCli);
                  $vTramites['ternoint'] = $xRDC['CLINOMXX'];
                } else {
                  $vTramites['ternoint'] = "CLIENTE SIN NOMBRE";
                }
              }
              
              if ($nBand == 0) {
                $nBand = 1;
              } else { ?>
                <script languaje = "javascript">
                  fnAddNewRow();
                </script>
              <?php } ?>
              <script languaje = "javascript">
                document.forms['frgrm']['cSucId' + "<?php echo ($i+1) ?>"].value = "<?php echo $vTramites['sucidxxx']; ?>";
                document.forms['frgrm']['cDocId' + "<?php echo ($i+1) ?>"].value = "<?php echo $vTramites['docidxxx']; ?>"; 
                document.forms['frgrm']['cDocSuf'+ "<?php echo ($i+1) ?>"].value = "<?php echo $vTramites['docsufxx']; ?>";
                document.forms['frgrm']['cDocTip'+ "<?php echo ($i+1) ?>"].value = "<?php echo $vTramites['doctipxx']; ?>";
                document.forms['frgrm']['cCliId' + "<?php echo ($i+1) ?>"].value = "<?php echo $vTramites['cliidxxx']; ?>"; 
                document.forms['frgrm']['cCliDv' + "<?php echo ($i+1) ?>"].value = "<?php echo f_Digito_Verificacion($vTramites['cliidxxx']); ?>"; 
                document.forms['frgrm']['cCliNom'+ "<?php echo ($i+1) ?>"].value = "<?php echo $vTramites['clinomxx']; ?>";
                
                if ("<?php echo $mTramites[$i][3] ?>" == "SI") {
                  document.forms['frgrm']['cTerIdInt' +"<?php echo ($i+1) ?>"].value = '<?php echo $mTramites[$i][4]  ?>';
                  document.forms['frgrm']['cTerDVInt' +"<?php echo ($i+1) ?>"].value = '<?php echo f_Digito_Verificacion($mTramites[$i][4]) ?>';
                  document.forms['frgrm']['cTerNomInt'+"<?php echo ($i+1) ?>"].value = '<?php echo $vTramites['ternoint']  ?>';
                  document.forms['frgrm']['cCcAplFa'  +"<?php echo ($i+1) ?>"].value = '<?php echo $mTramites[$i][3]  ?>';
                  document.forms['frgrm']['cTerIdInt' +"<?php echo ($i+1) ?>"].readOnly = false;
                }
                
                if ("<?php echo $vTramites['doctipxx']; ?>" == "REGISTRO") {
                  var cBgColor = "#FF0000";
                  var cColor   = "#FFFFFF";
                } else {
                  var cBgColor = "#FFFFFF";
                  var cColor   = "#000000";
                }
  
                document.getElementById('cAutSeq' + "<?php echo ($i+1) ?>").style.background = cBgColor;
                document.getElementById('cAutSeq' + "<?php echo ($i+1) ?>").style.color = cColor;
                document.getElementById('cSucId' + "<?php echo ($i+1) ?>").style.background = cBgColor;
                document.getElementById('cSucId' + "<?php echo ($i+1) ?>").style.color = cColor;
                document.getElementById('cDocId' + "<?php echo ($i+1) ?>").style.background = cBgColor;
                document.getElementById('cDocId' + "<?php echo ($i+1) ?>").style.color = cColor;
                document.getElementById('cDocSuf'+ "<?php echo ($i+1) ?>").style.background = cBgColor;
                document.getElementById('cDocSuf'+ "<?php echo ($i+1) ?>").style.color = cColor;
                document.getElementById('cDocTip'+ "<?php echo ($i+1) ?>").style.background = cBgColor;
                document.getElementById('cDocTip'+ "<?php echo ($i+1) ?>").style.color = cColor;
                document.getElementById('cCliId' + "<?php echo ($i+1) ?>").style.background = cBgColor;
                document.getElementById('cCliId' + "<?php echo ($i+1) ?>").style.color = cColor;
                document.getElementById('cCliDv' + "<?php echo ($i+1) ?>").style.background = cBgColor;
                document.getElementById('cCliDv' + "<?php echo ($i+1) ?>").style.color = cColor;
                document.getElementById('cCliNom'+ "<?php echo ($i+1) ?>").style.background = cBgColor;
                document.getElementById('cCliNom'+ "<?php echo ($i+1) ?>").style.color = cColor;
                
                document.getElementById('cTerIdInt'+ "<?php echo ($i+1) ?>").style.background = cBgColor;
                document.getElementById('cTerIdInt'+ "<?php echo ($i+1) ?>").style.color = cColor;
                document.getElementById('cTerDVInt'+ "<?php echo ($i+1) ?>").style.background = cBgColor;
                document.getElementById('cTerDVInt'+ "<?php echo ($i+1) ?>").style.color = cColor;
                document.getElementById('cTerNomInt'+ "<?php echo ($i+1) ?>").style.background = cBgColor;
                document.getElementById('cTerNomInt'+ "<?php echo ($i+1) ?>").style.color = cColor;
                
              </script>
              <?php 
            }
          }
        }
      }

      //Paso 2
      if ($_POST['cStep'] == "2") {
        $mTarifas = array();
        $cTraSel = "";
        for ($i=0; $i<$_POST['nSecuencia']; $i++) {
          if ($_POST['cSucId' .($i+1)] != "" && $_POST['cDocId' .($i+1)] != "" && $_POST['cDocSuf' .($i+1)] != "") {
            
            $cTraSel .= "{$_POST['cSucId'.($i+1)]}~{$_POST['cDocId'.($i+1)]}~{$_POST['cDocSuf'.($i+1)]}~{$_POST['cCcAplFa'.($i+1)]}~{$_POST['cTerIdInt'.($i+1)]}|";
            
            $qTramite  = "SELECT * ";
            $qTramite .= "FROM $cAlfa.sys00121 ";
            $qTramite .= "WHERE ";
            $qTramite .= "sucidxxx  = \"{$_POST['cSucId' .($i+1)]}\" AND ";
            $qTramite .= "docidxxx  = \"{$_POST['cDocId' .($i+1)]}\" AND ";
            $qTramite .= "docsufxx  = \"{$_POST['cDocSuf'.($i+1)]}\" AND ";
            $qTramite .= "regestxx != \"INACTIVO\" LIMIT 0,1 ";
            $xTramite  = f_MySql("SELECT","",$qTramite,$xConexion01,"");
            // f_Mensaje(__FILE__,__LINE__,$qTramite." ~ ".mysql_num_rows($xTramite));
            if (mysql_num_rows($xTramite) > 0) {
              $vTramite = mysql_fetch_array($xTramite);
              
              $vTramite['teridint'] = "";
              $vTramite['ternoint'] = "";
              
              $vTramite['tarclixx'] = $vTramite['cliidxxx'];
              $vTramite['tartipxx'] = "CLIENTE";
                
              //Busco la el nombre del cliente
              $qDatCli  = "SELECT ";
              $qDatCli .= "$cAlfa.SIAI0150.*, ";
              $qDatCli .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
              $qDatCli .= "FROM $cAlfa.SIAI0150 ";
              $qDatCli .= "WHERE ";
              $qDatCli .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$vTramite['cliidxxx']}\" LIMIT 0,1";
              $xDatCli  = f_MySql("SELECT","",$qDatCli,$xConexion01,"");
              if(mysql_num_rows($xDatCli) > 0) {
                $xRDC = mysql_fetch_array($xDatCli);
                $vTramite['clinomxx'] = $xRDC['CLINOMXX'];
              } else {
                $vTramite['clinomxx'] = "CLIENTE SIN NOMBRE";
              }
              
              // Verifica si tiene asociacion por grupo
              $qConCom  = "SELECT $cAlfa.fpar0151.gtaidxxx, $cAlfa.fpar0151.cccaplfa ";
              $qDatCcc .= "IF($cAlfa.fpar0151.gtaidxxx <> \"\",IF($cAlfa.fpar0111.gtadesxx <> \"\",$cAlfa.fpar0111.gtadesxx,\"GRUPO TARIFAS SIN DESCRIPCION\"),\"\") AS gtadesxx ";
              $qConCom .= "FROM $cAlfa.fpar0151 ";
              $qDatCcc .= "LEFT JOIN $cAlfa.fpar0111 ON $cAlfa.fpar0151.gtaidxxx = $cAlfa.fpar0111.gtaidxxx ";
              $qConCom .= "WHERE ";
              $qConCom .= "$cAlfa.fpar0151.cliidxxx = \"{$vTramite['cliidxxx']}\" AND  ";
              $qConCom .= "$cAlfa.fpar0151.regestxx = \"ACTIVO\" LIMIT 0,1";
              $xConCom  = f_MySql("SELECT","",$qConCom,$xConexion01,"");
              $cCcAplFa = "";
              if (mysql_num_rows($xConCom) > 0) {
                $xRCC = mysql_fetch_array($xConCom);
                if ($xRCC['gtaidxxx'] <> "") {
                  $vTramite['tarclixx'] = $xRCC['gtaidxxx'];
                  $vTramite['tartipxx'] = "GRUPO";
                } 
              }
              
              if ($_POST['cCcAplFa'.($i+1)] == "SI") {
                
                $vTramite['tarclixx'] = $_POST['cTerIdInt'.($i+1)];
                $vTramite['tartipxx'] = "CLIENTE";
                
                $vTramite['teridint'] = $_POST['cTerIdInt'.($i+1)];
                $vTramite['ccaplfax'] = $_POST['cCcAplFa' .($i+1)];
                
                $qDatCli  = "SELECT ";
                $qDatCli .= "$cAlfa.SIAI0150.*, ";
                $qDatCli .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
                $qDatCli .= "FROM $cAlfa.SIAI0150 ";
                $qDatCli .= "WHERE ";
                $qDatCli .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$vTramite['teridint']}\" LIMIT 0,1";
                $xDatCli  = f_MySql("SELECT","",$qDatCli,$xConexion01,"");
                if(mysql_num_rows($xDatCli) > 0) {
                  $xRDC = mysql_fetch_array($xDatCli);
                  $vTramite['ternoint'] = $xRDC['CLINOMXX'];
                } else {
                  $vTramite['ternoint'] = "CLIENTE SIN NOMBRE";
                }
                    
                $qConCom  = "SELECT $cAlfa.fpar0151.gtaidxxx, $cAlfa.fpar0151.cccaplfa ";
                $qDatCcc .= "IF($cAlfa.fpar0151.gtaidxxx <> \"\",IF($cAlfa.fpar0111.gtadesxx <> \"\",$cAlfa.fpar0111.gtadesxx,\"GRUPO TARIFAS SIN DESCRIPCION\"),\"\") AS gtadesxx ";
                $qConCom .= "FROM $cAlfa.fpar0151 ";
                $qDatCcc .= "LEFT JOIN $cAlfa.fpar0111 ON $cAlfa.fpar0151.gtaidxxx = $cAlfa.fpar0111.gtaidxxx ";
                $qConCom .= "WHERE ";
                $qConCom .= "$cAlfa.fpar0151.cliidxxx = \"{$_POST['cTerIdInt'.($i+1)]}\" AND  ";
                $qConCom .= "$cAlfa.fpar0151.regestxx = \"ACTIVO\" LIMIT 0,1";
                $xConCom  = f_MySql("SELECT","",$qConCom,$xConexion01,"");
                $cCcAplFa = "";
                if (mysql_num_rows($xConCom) > 0) {
                  $xRCC = mysql_fetch_array($xConCom);
                  if ($xRCC['gtaidxxx'] <> "") {
                    $vTramite['tarclixx'] = $xRCC['gtaidxxx'];
                    $vTramite['tartipxx'] = "GRUPO";
                  } 
                }
              }
              
              ##Fin Traigo Datos Adicionales del Do ##
              ##Traigo Tarifas parametrizadas al cliente para excluir Conceptos de Cobro al momento de facturar##
              $qTarifas  = "SELECT ";
              $qTarifas .= "$cAlfa.fpar0131.seridxxx, ";
              $qTarifas .= "IF($cAlfa.fpar0129.serdespx != \"\", $cAlfa.fpar0129.serdespx,$cAlfa.fpar0129.serdesxx) AS serdesxx, ";
              $qTarifas .= "$cAlfa.fpar0131.fcotptxx, ";
              $qTarifas .= "$cAlfa.fpar0131.fcotpixx  ";
              $qTarifas .= "FROM $cAlfa.fpar0131 ";
              $qTarifas .= "LEFT JOIN $cAlfa.fpar0129 ON $cAlfa.fpar0131.seridxxx = $cAlfa.fpar0129.seridxxx ";
              $qTarifas .= "WHERE ";
              $qTarifas .= "$cAlfa.fpar0131.cliidxxx = \"{$vTramite['tarclixx']}\" AND ";
              $qTarifas .= "$cAlfa.fpar0131.fcotptxx = \"{$vTramite['doctepxx']}\" AND ";
              $qTarifas .= "$cAlfa.fpar0131.fcotpixx = \"{$vTramite['doctepid']}\" AND ";
              $qTarifas .= "$cAlfa.fpar0131.sucidxxx LIKE \"%{$vTramite['sucidxxx']}%\" AND ";
              $qTarifas .= "$cAlfa.fpar0131.fcotopxx LIKE \"%{$vTramite['doctipxx']}%\" AND ";
              $qTarifas .= "$cAlfa.fpar0131.fcomtrxx LIKE \"%{$vTramite['docmtrxx']}%\" AND ";
              $qTarifas .= "$cAlfa.fpar0131.tartipxx = \"{$vTramite['tartipxx']}\"      AND ";
              $qTarifas .= "$cAlfa.fpar0131.regestxx = \"ACTIVO\" ";
              $xTarifas  = f_MySql("SELECT","",$qTarifas,$xConexion01,"");
              // f_Mensaje(__FILE__,__LINE__,$qTarifas." ~ ".mysql_num_rows($xTarifas));
              while ($xRT = mysql_fetch_array($xTarifas)) {
                $cExcluida = false;
                $mTarifaExcluidas = array();
                $mTarifaExcluidas = f_Explode_Array($vTramite['doctexxx'],"|","~"); 
                
                for($nE=0;$nE<count($mTarifaExcluidas);$nE++){//Exploto campo donde se guardan conceptos excluidos para facturacion.
                  if($mTarifaExcluidas[$nE][0] <> ""){
                    if($mTarifaExcluidas[$nE][0] == $xRT['fcotptxx'] && $mTarifaExcluidas[$nE][2] == $xRT['seridxxx']){
                      $cExcluida = true;
                    }
                  }
                }
                
                $nInd_mTarifas = count($mTarifas);
                $mTarifas[$nInd_mTarifas] = $xRT;
                $mTarifas[$nInd_mTarifas]['sucidxxx'] = $vTramite['sucidxxx'];
                $mTarifas[$nInd_mTarifas]['docidxxx'] = $vTramite['docidxxx'];
                $mTarifas[$nInd_mTarifas]['docsufxx'] = $vTramite['docsufxx'];
                $mTarifas[$nInd_mTarifas]['cliidxxx'] = $vTramite['cliidxxx'];
                $mTarifas[$nInd_mTarifas]['clinomxx'] = $vTramite['clinomxx'];
                $mTarifas[$nInd_mTarifas]['doctipxx'] = $vTramite['doctipxx'];
                $mTarifas[$nInd_mTarifas]['ccaplfax'] = $vTramite['ccaplfax'];
                $mTarifas[$nInd_mTarifas]['teridint'] = $vTramite['teridint'];
                $mTarifas[$nInd_mTarifas]['ternoint'] = $vTramite['ternoint'];
                $mTarifas[$nInd_mTarifas]['excluida'] = $cExcluida;
              }
            } 
          } 
        } ## for ($i=0; $i<$_POST['nSecuencia']; $i++) { ##
        
        //Matriz para verificar si ya habia sido marcado
        $vCheckMarcados = array();
        $vCheckMarcados = explode("|",$_POST['cComMemo']);
        
        for ($i=0; $i<count($mTarifas); $i++) {
            
          $cId = $mTarifas[$i]['fcotptxx']."~".$mTarifas[$i]['fcotpixx']."~".$mTarifas[$i]['seridxxx']."~".$mTarifas[$i]['sucidxxx']."~".$mTarifas[$i]['docidxxx']."~".$mTarifas[$i]['docsufxx']."~".$mTarifas[$i]['ccaplfax']."~".$mTarifas[$i]['teridint'];
          
          if (count($vCheckMarcados) > 0) {
            $mTarifas[$i]['excluida'] = false;
            if (in_array($cId, $vCheckMarcados) == true) {
              $mTarifas[$i]['excluida'] = true;
            }
          } ?>
          <script languaje = "javascript">
            f_Add_New_Row_Ip("<?php echo $cId ?>","<?php echo count($mTarifas) ?>","<?php echo $mTarifas[$i]['excluida'] ?>");
            
            document.forms['frgrm']['cTramite'   + document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo "{$mTarifas[$i]['sucidxxx']}-{$mTarifas[$i]['docidxxx']}-{$mTarifas[$i]['docsufxx']}"; ?>";
            document.forms['frgrm']['cImportador'+ document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo "{$mTarifas[$i]['clinomxx']} [{$mTarifas[$i]['cliidxxx']}]"; ?>";
            document.forms['frgrm']['cFacturara' + document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo (($mTarifas[$i]['teridint'] != "") ? "[{$mTarifas[$i]['teridint']}] {$mTarifas[$i]['ternoint']}" : "") ?>";
            document.forms['frgrm']['cServicio'  + document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo "[{$mTarifas[$i]['seridxxx']}] {$mTarifas[$i]['serdesxx']}"; ?>";
            document.forms['frgrm']['cTarifa'    + document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo "{$mTarifas[$i]['fcotptxx']}"; ?>"; 
          </script>
        <?php }  ?>
        <script languaje="javascript">
          document.forms['frgrm']['cTramites'].value  = "<?php echo $cTraSel ?>";
          document.forms['frgrm']['nSecuencia'].value = "<?php echo $_POST['nSecuencia'] ?>";
          document.forms['frgrm']['nRecords'].value   = "<?php echo count($mTarifas) ?>";
        </script>
        <?php
      }
		?>
	</body>
</html>

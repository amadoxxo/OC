<?php
  namespace openComex;
/**
	 * Proceso Autorizacion Excluir Conceptos de Pagos a Terceros
	 * --- Descripcion: Permite Crear un Nueva autorizacion para Excluir conceptos de Pagos a Terceros para Facturacion.
	 * @author Johana Arboleda Ramos <jarboleda@opentecnologia.com.co>
	 * @version 001
	 */
	include("../../../../libs/php/utility.php");
?>
<html>
  <head>
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
    <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
    <script language="javascript">
      function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
      }

      function f_Links(xLink,xSwitch,xSecuencia,xGrid) {
        var zX    = screen.width;
        var zY    = screen.height;
        switch (xLink){
          case "cDocId":
            var nSwitch = 0; var cMsj = "";
            if (document.forms['frgrm']['cDocId'+xSecuencia].value != "") {
              if (xSwitch == "VALID") {
                var cRuta  = "freptdos.php?gModo=VALID&gFunction="+xLink+
                             "&gDocId="+document.forms['frgrm']['cDocId'+xSecuencia].value.toUpperCase()+
                             "&gSecuencia="+xSecuencia;
                // alert(cRuta);
                parent.fmpro.location = cRuta;
              } else {
                var zNx     = (zX-800)/2;
                var zNy     = (zY-300)/2;
                var zWinPro = 'width=800,scrollbars=1,height=350,left='+zNx+',top='+zNy;
                var cRuta   = "freptfrm.php?gModo=WINDOW&gArchivo=freptdos.php&gFunction="+xLink+
                              "&gDocId="+document.forms['frgrm']['cDocId'+xSecuencia].value.toUpperCase()+
                              "&gSecuencia="+xSecuencia;
                // alert(cRuta);
                zWindow = window.open(cRuta,"zWindow",zWinPro);
                zWindow.focus();
              }
            } else {
              alert("Digite el Numero del DO.");
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
                if ("<?php echo $vSysStr['system_habilitar_liquidacion_do_facturacion'] ?>" == "SI") {
                  document.forms['frgrm']['cComMemo'].value += document.forms['frgrm']['cCheck'].id+"~"+document.forms['frgrm']['cEnviarA1'].value+"|";
                } else {
                  document.forms['frgrm']['cComMemo'].value += document.forms['frgrm']['cCheck'].id+"|";
                }
              }
            break;
            default:
              if (document.forms['frgrm']['nRecords'].value > 1) {
                for (i=0;i<document.forms['frgrm']['cCheck'].length;i++) {
                  if (document.forms['frgrm']['cCheck'][i].checked == true) {
                    if ("<?php echo $vSysStr['system_habilitar_liquidacion_do_facturacion'] ?>" == "SI") {
                      document.forms['frgrm']['cComMemo'].value += document.forms['frgrm']['cCheck'][i].id+"~"+document.forms['frgrm']['cEnviarA'+ (i+1)].value+"|";
                    } else {
                      document.forms['frgrm']['cComMemo'].value += document.forms['frgrm']['cCheck'][i].id+"|";
                    }
                  }
                }
              }
            break;
          }
          if (document.forms['frgrm']['cComMemo'].value == "|"){
            document.forms['frgrm']['cComMemo'].value = "";
          }
        }

        function f_Add_New_Row_Do() {

          var cGrid      = document.getElementById("Grid_Do");
          var nLastRow   = cGrid.rows.length;
          var nSecuencia = nLastRow+1;
          var cTableRow  = cGrid.insertRow(nLastRow);
          var cDocSeq  = 'cDocSeq'+ nSecuencia; // Hidden: Sucursal del DO
          var cSucId   = 'cSucId' + nSecuencia; // Hidden: Sucursal del DO
          var cDocId   = 'cDocId' + nSecuencia; // Numero del DO
          var cDocSuf  = 'cDocSuf'+ nSecuencia; // Hidden: Sufijo del DO
          var cDocTip  = 'cDocTip'+ nSecuencia;
          var cCliId   = 'cCliId' + nSecuencia;
          var cCliDv   = 'cCliDv' + nSecuencia;
          var cCliNom  = 'cCliNom'+ nSecuencia; //Estado
          var oBtnDel  = 'oBtnDel'+ nSecuencia; // Boton de Borrar Row

          var TD_xAll = cTableRow.insertCell(0);
          TD_xAll.style.width  = "40px";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:040;border:0;text-align:center;padding:2px' name = '"+cDocSeq+"' id = '"+cDocSeq+"' value = '"+f_Str_Pad(nSecuencia,3,"0","STR_PAD_LEFT")+"'readonly>";

          TD_xAll = cTableRow.insertCell(1);
          TD_xAll.style.width  = "40px";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:040;border:0;text-align:center;padding:2px' name = '"+cSucId+"' id = '"+cSucId+"' readonly>";

          TD_xAll = cTableRow.insertCell(2);
          TD_xAll.style.width  = "140px";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:140;border:0;text-align:left;padding:2px'  name = '"+cDocId+"' id = '"+cDocId+"'  "+
                              "onBlur = 'javascript:this.value=this.value.toUpperCase(); "+
                                        "f_Links(\"cDocId\",\"VALID\",\""+nSecuencia+"\")'>";

          TD_xAll = cTableRow.insertCell(3);
          TD_xAll.style.width  = "40px";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:040;border:0;text-align:center;padding:2px' name = '"+cDocSuf+"' id = '"+cDocSuf+"' readonly>";

          TD_xAll = cTableRow.insertCell(4);
          TD_xAll.style.width  = "120px";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:120;border:0;text-align:center;padding:2px' name = '"+cDocTip+"' id = '"+cDocTip+"' readonly>";

          TD_xAll = cTableRow.insertCell(5);
          TD_xAll.style.width  = "100px";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:100;border:0;text-align:center;padding:2px' name = '"+cCliId+"' id = '"+cCliId+"' readonly>";

          TD_xAll = cTableRow.insertCell(6);
          TD_xAll.style.width  = "20px";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:020;border:0;text-align:center;padding:2px' name = '"+cCliDv+"' id = '"+cCliDv+"' readonly>";

          TD_xAll = cTableRow.insertCell(7);
          TD_xAll.style.width  = "460px";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:460;border:0;text-align:left;padding:2px' name = '"+cCliNom+"' id = '"+cCliNom+"' onKeyUp='javascript:f_Enter(event,this.name,\"Grid_Do\");' readonly>";

          TD_xAll = cTableRow.insertCell(8);
          TD_xAll.style.width  = "20px";
          TD_xAll.innerHTML    = "<input type = 'button' style = 'width:020;text-align:center' id = "+nSecuencia+" value = 'X' "+
                                  "onClick = 'javascript:f_Delete_Row(this.value,\""+nSecuencia+"\",\"Grid_Do\", this);'>";

          document.forms['frgrm']['nSecuencia'].value = nSecuencia;
        }

        function f_Add_New_Row_Pcc(xId,xValue,xRow,xChek) {

          //xId    -> comprobante con secuencia
          //xValue -> Si hay que marcar Comprobante por grupos (Cartas bancarias)

          var cGrid      = document.getElementById("Grid_Pcc");
          var nLastRow   = cGrid.rows.length;
          var nSecuencia = nLastRow+1;
          var cTableRow  = cGrid.insertRow(nLastRow);

          var cCtoId     = 'cCtoId'     + nSecuencia;
          var cTramite   = 'cTramite'   + nSecuencia;
          var cServicio  = 'cServicio'  + nSecuencia;
          var cDocFuente = 'cDocFuente' + nSecuencia;
          var cDocInf    = 'cDocInf'    + nSecuencia;
          var cComVlr    = 'cComVlr'    + nSecuencia;
          var cEnviarA   = 'cEnviarA'   + nSecuencia;
          var cComMov    = 'cComMov'    + nSecuencia;
          var cCheck     = 'cCheck'     + nSecuencia;


          var TD_xAll = cTableRow.insertCell(0);
          TD_xAll.style.width  = "80px";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:080;border:0;text-align:center;padding:2px' name = '"+cCtoId+"' id = '"+cCtoId+"' readonly>";

          TD_xAll = cTableRow.insertCell(1);
          TD_xAll.style.width  = "120px";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:120;border:0;text-align:left;padding:2px' name = '"+cTramite+"' id = '"+cTramite+"' readonly>";

          TD_xAll = cTableRow.insertCell(2);
          TD_xAll.style.width  = "<?php echo ($vSysStr['system_habilitar_liquidacion_do_facturacion'] == "SI") ? "260px" : "360px" ?>";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:<?php echo ($vSysStr['system_habilitar_liquidacion_do_facturacion'] == "SI") ? "260px" : "360px" ?>;border:0;text-align:left;padding:2px' name = '"+cServicio+"' id = '"+cServicio+"' readonly>";

          TD_xAll = cTableRow.insertCell(3);
          TD_xAll.style.width  = "<?php echo ($vSysStr['system_habilitar_liquidacion_do_facturacion'] == "SI") ? "160px" : "220px" ?>";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:<?php echo ($vSysStr['system_habilitar_liquidacion_do_facturacion'] == "SI") ? "160px" : "220px" ?>;border:0;text-align:left;padding:2px' name = '"+cDocFuente+"' id = '"+cDocFuente+"' readonly>";

          TD_xAll = cTableRow.insertCell(4);
          TD_xAll.style.width  = "60px";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:60;border:0;text-align:left;padding:2px' name = '"+cDocInf+"' id = '"+cDocInf+"' readonly>";

          TD_xAll = cTableRow.insertCell(5);
          TD_xAll.style.width  = "80px";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:80;border:0;text-align:right;padding:2px' name = '"+cComVlr+"' id = '"+cComVlr+"' >";

          TD_xAll = cTableRow.insertCell(6);
          TD_xAll.style.width  = "20px";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:20;border:0;text-align:center;padding:2px' name = '"+cComMov+"' id = '"+cComMov+"' readonly>";

          var nCol = 7;
          if ("<?php echo $vSysStr['system_habilitar_liquidacion_do_facturacion'] ?>" == "SI") {
            TD_xAll = cTableRow.insertCell(nCol);
            TD_xAll.style.width  = "160px";
            TD_xAll.style.border = "1px solid #E6E6E6";
            TD_xAll.innerHTML    = "<select  class = 'letra'  style = 'width:160;border:0;padding:2px' name = '"+cEnviarA+"' id = '"+cEnviarA+"'>"+
                                  //  "<option value=''>[SELECCIONE]</option>"+
                                   "<option value='NOAPLICA' selected>COBRAR EN OTRA FACTURA</option>"+
                                   "<option value='COSTOS'>COSTO DO</option>"+
                                   "<option value='GASTOS'>TARIFA INTEGRAL</option>"+
                                   // "<option value='INGRESOS'>INGRESOS</option>"+
                                   "<option value='FINANCIACION'>FINANCIACION X LIQUIDAR</option>"+
                                   "</select>";
            nCol++;
          }

          TD_xAll = cTableRow.insertCell(nCol);
          TD_xAll.style.width     = "20px";
          TD_xAll.style.textAlign = "center";
          TD_xAll.innerHTML       = "<input type='checkbox' name='cCheck'  value = '"+xValue+"' id='"+xId+"'"+((xChek) == true ? " checked" : "")+" onclick='javascript:f_Marcar_Iguales(this.id,this.checked)'>";

          document.forms['frgrm']['nSecuencia_Pcc'].value = nSecuencia;
        }


        function f_Marcar_Iguales(xId,xValor) {
          switch (document.forms['frgrm']['nSecuencia_Pcc'].value) {
            case "1":
              //No hace nada porque existe solo uno
            break;
            default:
              for (i=0;i<document.forms['frgrm']['cCheck'].length;i++) {
                if (document.forms['frgrm']['cCheck'][i].id == xId) {
                  document.forms['frgrm']['cCheck'][i].checked = xValor;
                }
              }
            break;
          }
        }

        function f_Enter(e,xName,xGrilla) {
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
              case "Grid_Do":
                if (xName == 'cCliNom'+eval(document.forms['frgrm']['nSecuencia'].value)) {
                  if (document.forms["frgrm"][xName].value !== '' ) {
                    f_Add_New_Row_Do();
                  } else {
                    alert("Seleccione un DO antes de Adicionar una nueva Fila.");
                  }
                }
              break;
            }
          }
        }

        function f_Delete_Row(xNumRow,xSecuencia,xTabla) {
          switch (xTabla) {
            case "Grid_Do":
              var cGrid = document.getElementById(xTabla);
              var nLastRow = cGrid.rows.length;
              if (nLastRow > 1 && xNumRow == "X") {
                if (confirm("Realmente Desea Eliminar el DO ["+document.forms["frgrm"]["cSucId"+xSecuencia].value+"-"+document.forms["frgrm"]["cDocId"+xSecuencia].value+"-"+document.forms['frgrm']['cDocSuf'+xSecuencia].value+"]?")){
                  if(xSecuencia < nLastRow){
                    var j=0;
                    for(var i=xSecuencia;i<nLastRow;i++){
                      j = parseFloat(i)+1;
                      document.forms['frgrm']['cDocSeq'+ i].value = f_Str_Pad(i,3,"0","STR_PAD_LEFT");
                      document.forms['frgrm']['cSucId' + i].value = document.forms['frgrm']['cSucId' + j].value;
                      document.forms['frgrm']['cDocId' + i].value = document.forms['frgrm']['cDocId' + j].value;
                      document.forms['frgrm']['cDocSuf'+ i].value = document.forms['frgrm']['cDocSuf'+ j].value;
                      document.forms['frgrm']['cDocTip'+ i].value = document.forms['frgrm']['cDocTip'+ j].value;
                      document.forms['frgrm']['cCliId' + i].value = document.forms['frgrm']['cCliId' + j].value;
                      document.forms['frgrm']['cCliDv' + i].value = document.forms['frgrm']['cCliDv' + j].value;
                      document.forms['frgrm']['cCliNom'+ i].value = document.forms['frgrm']['cCliNom'+ j].value;
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

        function fnBorrarDos(){
          document.getElementById("Grid_Do").innerHTML = "";
          document.forms['frgrm']['nSecuencia'].value  = 0;
          f_Add_New_Row_Do();
        }

        function fnPegarDo() {
          var nSecuencia = document.forms['frgrm']['nSecuencia'].value;
          var nX    = screen.width;
          var nY    = screen.height;
          var nAncho = 550;
          var nAlto  = 250;
          var nNx      = (nX-nAncho)/2;
          var nNy      = (nY-nAlto)/2;
          var cWinOpt  = "width="+nAncho+",scrollbars=1,height="+nAlto+",left="+nNx+",top="+nNy;
          var cPathUrl = "freptfrm.php?gFunction=PegarDo&gArchivo=freptcpd.php"+
                         "&gSecuencia="+document.forms['frgrm']['nSecuencia'].value;
          cWindow = window.open(cPathUrl,"PegarDo",cWinOpt);
          cWindow.focus();
        }

        function f_Valida(){//Valida datos de formulario, para poder pintar conceptos de Cobro a excluir
          document.forms['frgrm'].target = 'fmpro';
          document.forms['frgrm'].action = 'frept20g.php';
          document.forms['frgrm']['cModo'].value = 'VALIDARDO';
          document.forms['frgrm'].submit();
        }

        function f_VolverAtras() {
          f_Carga_Data();
          document.forms['frgrm'].target             = 'fmwork';
          document.forms['frgrm'].action             = 'freptmas.php';
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
        f_Carga_Data("SI");
        document.forms['frgrm'].target='fmpro';
        document.forms['frgrm'].action='freptgra.php';
        document.forms['frgrm']['nTimesSave'].value++;
        document.forms['frgrm'].submit();
      }

    </script>
  </head>
  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
    <center>
      <table border ="0" cellpadding="0" cellspacing="0" width="1000">
        <tr>
          <td>
            <fieldset>
              <legend><font color="red"><?php echo (($_COOKIE['kModo'] == 'MASIVA' ) ? "Nueva": $_COOKIE['kModo'])." ".$_COOKIE['kProDes'] ?></font></legend>
              <form name = 'frgrm' action = 'freptgra.php' method = 'post' target='fmpro'>
                <input type = "hidden" name = "cStep"         value = "<?php echo $_POST['cStep'] ?>">
                <input type = "hidden" name = "cStep_Ant"     value = "<?php echo $_POST['cStep_Ant'] ?>">
                <input type = "hidden" name = "cTarExc"       value = "">
                <input type = "hidden" name = "nRecords"      value = "<?php echo $_POST['nRecords'] ?>">
                <input type = "hidden" name = "nTimesSave"    value = "0">
                <input type = "hidden" name = "nSecuencia"    value = "">
                <input type = "hidden" name = "nSecuencia_Pcc" value = "">
                <input type = "hidden" name = "cModo"         value = "">
                <textarea name = "cComMemo"  id = "cComMemo"><?php  echo $_POST['cComMemo'] ?></textarea>
                <textarea name = "cTramites" id = "cTramites"><?php echo $_POST['cTramites'] ?></textarea>
                <center>
                <script languaje = "javascript">
                  document.getElementById("cTramites").style.display="none";
                  document.getElementById("cComMemo").style.display ="none";
                </script>

                <fieldset id="Grid_Paso1"><?php if($_POST['cStep'] == "") { $_POST['cStep'] = "1"; } ?>
                  <legend><b>Seleccione los Tramites</b></legend>
                  <center>
                    <table border = '0' cellpadding = '0' cellspacing = '0' width='1000'>
                      <?php $nCol = f_Format_Cols(50); echo $nCol;?>
                      <tr height="25px">
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="02" align="left">&nbsp;&nbsp;Seq.</td>
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="02" align="left">&nbsp;&nbsp;Suc</td>
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="07" align="left">&nbsp;&nbsp;Do</td>
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="02" align="left">&nbsp;&nbsp;Suf</td>
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="06" align="left">&nbsp;&nbsp;Operaci&oacute;n</td>
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="05" align="left">&nbsp;&nbsp;Nit</td>
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="01" align="left">Dv</td>
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="22" align="left">&nbsp;&nbsp;&nbsp;Importador</td>
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="03" align="right">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/page_go.png" onClick = "javascript:fnPegarDo()" style = "cursor:pointer" title="Pegar DOs">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_drop.png" onClick = "javascript:fnBorrarDos()" style = "cursor:pointer" title="Eliminar Todos">
                        </td>
                      </tr>
                    </table>
                    <table border = "0" cellpadding = "0" cellspacing = "0" width = "980" id = "Grid_Do"></table>
                  </center>
                  <script languaje = "javascript">
                    f_Add_New_Row_Do();
                  </script>
                </fieldset>

                <fieldset id="Grid_Paso2">
                  <legend><b>Seleccione los Conceptos de Cobro a Excluir</b></legend>
                  <center>
                    <b>Nota:</b> Debe seleccionar los Pagos a Terceros que desea excluir<br><br>
                    <table border = '0' cellpadding = '0' cellspacing = '0' width='980'>
                      <?php $nCol = f_Format_Cols(49); echo $nCol;?>
                      <tr height="25px">
                      	<td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="04" align="left">&nbsp;Concepto</td>
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="06" align="left">&nbsp;Tramite</td>
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="<?php echo ($vSysStr['system_habilitar_liquidacion_do_facturacion'] == "SI") ? "14" : "19" ?>" align="left">&nbsp;Servicio</td>
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="<?php echo ($vSysStr['system_habilitar_liquidacion_do_facturacion'] == "SI") ? "08" : "11" ?>" align="left">&nbsp;Doc Fuente</td>
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="03" align="left">&nbsp;Doc Inf.</td>
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="04" align="left">&nbsp;Valor</td>
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="01" align="left">&nbsp;M</td>
                        <?php if ($vSysStr['system_habilitar_liquidacion_do_facturacion'] == "SI") { ?>
                       	  <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="08" align="left">&nbsp;Enviar a</td>
                       	<?php } ?>
                        <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="01" align = "center"><input type="checkbox" name="nCheckAll" onClick = "javascript:f_Marca()"></td>
                    </table>
                    <table border = "0" cellpadding = "0" cellspacing = "0" width = "980" id = "Grid_Pcc"></table>
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
        <table border="0" cellpadding="0" cellspacing="0" id='bnt_Paso1' width="1000">
          <tr>
            <td width="818" height="21"></td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/siguiente.gif" style="cursor:pointer"
              onClick = "javascript:f_Valida();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Siguiente
            </td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand"
              onClick ="javascript:f_Retorna();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
            </td>
          </tr>
        </table>
      <?php
      break;
      case "2": ?>
        <table border="0" cellpadding="0" cellspacing="0" id='bnt_Paso2' width="1000">
          <tr>
            <td width="727" height="21"></td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/anterior.gif" style="cursor:pointer"
              onClick = "javascript:f_VolverAtras();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Anterior</td>
            <td width="91" height="21" Class="name">
              <input type="button" name="name" value="Guardar" style = "background:url(<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif);width:91;height:21;border:0px;font-weight:bold;color:#555555;"
                onclick = "javascript:fnGuardar()"></td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand"
              onClick ="javascript:f_Retorna();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
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
            $qTramite .= "regestxx  = \"ACTIVO\" LIMIT 0,1 ";
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

              if ($nBand == 0) {
                $nBand = 1;
              } else { ?>
                <script languaje = "javascript">
                  f_Add_New_Row_Do();
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
                if ("<?php echo $vTramites['doctipxx']; ?>" == "REGISTRO") {
                  document.getElementById('cDocSeq' + "<?php echo ($i+1) ?>").style.background = "Red";
                  document.getElementById('cDocSeq' + "<?php echo ($i+1) ?>").style.color = "#FFFFFF";
                  document.getElementById('cSucId' + "<?php echo ($i+1) ?>").style.background = "Red";
                  document.getElementById('cSucId' + "<?php echo ($i+1) ?>").style.color = "#FFFFFF";
                  document.getElementById('cDocId' + "<?php echo ($i+1) ?>").style.background = "Red";
                  document.getElementById('cDocId' + "<?php echo ($i+1) ?>").style.color = "#FFFFFF";
                  document.getElementById('cDocSuf'+ "<?php echo ($i+1) ?>").style.background = "Red";
                  document.getElementById('cDocSuf'+ "<?php echo ($i+1) ?>").style.color = "#FFFFFF";
                  document.getElementById('cDocTip'+ "<?php echo ($i+1) ?>").style.background = "Red";
                  document.getElementById('cDocTip'+ "<?php echo ($i+1) ?>").style.color = "#FFFFFF";
                  document.getElementById('cCliId' + "<?php echo ($i+1) ?>").style.background = "Red";
                  document.getElementById('cCliId' + "<?php echo ($i+1) ?>").style.color = "#FFFFFF";
                  document.getElementById('cCliDv' + "<?php echo ($i+1) ?>").style.background = "Red";
                  document.getElementById('cCliDv' + "<?php echo ($i+1) ?>").style.color = "#FFFFFF";
                  document.getElementById('cCliNom'+ "<?php echo ($i+1) ?>").style.background = "Red";
                  document.getElementById('cCliNom'+ "<?php echo ($i+1) ?>").style.color = "#FFFFFF";
                } else {
                  document.getElementById('cDocSeq'+ "<?php echo ($i+1) ?>").style.background = "#FFFFFF";
                  document.getElementById('cDocSeq'+ "<?php echo ($i+1) ?>").style.color = "#000000";
                  document.getElementById('cSucId' + "<?php echo ($i+1) ?>").style.background = "#FFFFFF";
                  document.getElementById('cSucId' + "<?php echo ($i+1) ?>").style.color = "#000000";
                  document.getElementById('cDocId' + "<?php echo ($i+1) ?>").style.background = "#FFFFFF";
                  document.getElementById('cDocId' + "<?php echo ($i+1) ?>").style.color = "#000000";
                  document.getElementById('cDocSuf'+ "<?php echo ($i+1) ?>").style.background = "#FFFFFF";
                  document.getElementById('cDocSuf'+ "<?php echo ($i+1) ?>").style.color = "#000000";
                  document.getElementById('cDocTip'+ "<?php echo ($i+1) ?>").style.background = "#FFFFFF";
                  document.getElementById('cDocTip'+ "<?php echo ($i+1) ?>").style.color = "#000000";
                  document.getElementById('cCliId' + "<?php echo ($i+1) ?>").style.background = "#FFFFFF";
                  document.getElementById('cCliId' + "<?php echo ($i+1) ?>").style.color = "#000000";
                  document.getElementById('cCliDv' + "<?php echo ($i+1) ?>").style.background = "#FFFFFF";
                  document.getElementById('cCliDv' + "<?php echo ($i+1) ?>").style.color = "#000000";
                }
              </script>
              <?php
            }
          }
        }
      }
      //Paso 2
      if ($_POST['cStep'] == "2") { ?>
        <script language="javascript">
          document.forms['frgrm'].target = "fmpro";
          document.forms['frgrm'].action = "freptptb.php";
          document.forms['frgrm'].submit();
          document.forms['frgrm'].target = "fmwork";
          document.forms['frgrm'].action = "freptmas.php";
        </script>
      <?php }
    ?>
  </body>
</html>

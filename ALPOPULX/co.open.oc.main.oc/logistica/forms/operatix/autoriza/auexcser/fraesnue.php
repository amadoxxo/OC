<?php

/**
 * Proceso Autorizacion Excluir Servicios
 * --- Descripcion: Permite Crear un Nueva autorizacion para Excluir Servicios.
 * @author cristian.perdomo@openits.co
 * @version 001
 */
include("../../../../../financiero/libs/php/utility.php");
?>
<html>

<head>
  <LINK rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
  <LINK rel='stylesheet' href='<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
  <LINK rel='stylesheet' href='<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
  <LINK rel='stylesheet' href='<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
  <script languaje='javascript' src='<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
  <script language="javascript">
    function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
      document.location = "<?php echo $_COOKIE['kIniAnt'] ?>";
      parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
    }

    function f_Links(xLink, xSwitch, xSecuencia) {
      var zX = screen.width;
      var zY = screen.height;
      switch (xLink) {
        case "cCliId":
          var nSwitch = 0;
          var cMsj = "";
          if (document.forms['frgrm']['cCliId' + xSecuencia].value != "") {
            if (xSwitch == "VALID") {
              var cRuta = "fraec121.php?gWhat=VALID" +
                "&gFunction=cCliId" +
                "&gSecuencia=" + xSecuencia +
                "&gCliId=" + document.forms['frgrm']['cCliId' + xSecuencia].value;
              parent.fmpro.location = cRuta;
            } else {
              var zNx = (zX - 600) / 2;
              var zNy = (zY - 250) / 2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left=' + zNx + ',top=' + zNy;
              var cRuta = "fraec121.php?gWhat=WINDOW" +
                "&gFunction=cCliId" +
                "&gSecuencia=" + xSecuencia +
                "&gCliId=" + document.forms['frgrm']['cCliId' + xSecuencia].value;
              zWindow = window.open(cRuta, "zWindow", zWinPro);
              zWindow.focus();
            }
          }
          break;
        case "cCerComCsc":

          var nSwitch = 0;
          var cMsj = "";
          if (document.forms['frgrm']['cCliId' + xSecuencia].value != "") {
            if (xSwitch == "VALID") {
              var cRuta = "frlcca00.php?gModo=VALID&gFunction=cCerComCsc" +
                "&gCliId=" + document.forms['frgrm']['cCliId' + xSecuencia].value.toUpperCase() +
                "&gSecuencia=" + xSecuencia +
                "&gOrigen=NUEVO";
              // alert(cRuta);
              parent.fmpro.location = cRuta;
            } else {

              var zNx = (zX - 800) / 2;
              var zNy = (zY - 300) / 2;
              var zWinPro = 'width=800,scrollbars=1,height=350,left=' + zNx + ',top=' + zNy;

              var cRuta = "frlcca00.php?gModo=WINDOW&gFunction=cCerComCsc" +
                "&gCliId=" + document.forms['frgrm']['cCliId' + xSecuencia].value.toUpperCase() +
                "&gSecuencia=" + xSecuencia;
              // alert(cRuta);
              zWindow = window.open(cRuta, "zWindow", zWinPro);
              zWindow.focus();
            }
          }
      }
    }

    function f_Marca() { //Marca y Desmarca los registros seleccionados en la tabla de Conceptos de Cobro
      if (document.forms['frgrm']['nCheckAll'].checked == true) {
        if (document.forms['frgrm']['nRecords'].value == 1) {
          document.forms['frgrm']['cCheck'].checked = true;
        } else {
          if (document.forms['frgrm']['nRecords'].value > 1) {
            for (i = 0; i < document.forms['frgrm']['cCheck'].length; i++) {
              document.forms['frgrm']['cCheck'][i].checked = true;
            }
          }
        }
      } else {
        if (document.forms['frgrm']['nRecords'].value == 1) {
          document.forms['frgrm']['cCheck'].checked = false;
        } else {
          if (document.forms['frgrm']['nRecords'].value > 1) {
            for (i = 0; i < document.forms['frgrm']['cCheck'].length; i++) {
              document.forms['frgrm']['cCheck'][i].checked = false;
            }
          }
        }
      }
    }

    function f_Carga_Data() { //Arma cadena para guardar en campo matriz de la sys00121
      document.forms['frgrm']['cComMemo'].value = "|";
      switch (document.forms['frgrm']['nRecords'].value) {
        case "1":
          if (document.forms['frgrm']['cCheck'].checked == true) {
            document.forms['frgrm']['cComMemo'].value += document.forms['frgrm']['cCheck'].id + "|";
          }
          break;
        default:
          if (document.forms['frgrm']['cCheck'] !== undefined) {
            for (i = 0; i < document.forms['frgrm']['cCheck'].length; i++) {
              if (document.forms['frgrm']['cCheck'][i].checked == true) {
                document.forms['frgrm']['cComMemo'].value += document.forms['frgrm']['cCheck'][i].id + "|";
              }
            }
          }
          break;
      }
      if (document.forms['frgrm']['cComMemo'].value == "|") {
        document.forms['frgrm']['cComMemo'].value = "";
      }
    }

    function f_Add_New_Row() {

      var cGrid = document.getElementById("Grid");
      var nLastRow = cGrid.rows.length;
      var nSecuencia = nLastRow + 1;
      var cTableRow = cGrid.insertRow(nLastRow);

      var cAutSeq  = 'cAutSeq' + nSecuencia; //secuencia
      var cCliId = 'cCliId' + nSecuencia; // Nit Cliente
      var cCliDv = 'cCliDv' + nSecuencia; // Dv
      var cCliSap = 'cCliSap' + nSecuencia; // Cod SAP 
      var cCliNom = 'cCliNom' + nSecuencia; // Cod SAP 

      //Campos del No. de certificacion
      var cCerComCsc = 'cCerComCsc' + nSecuencia; // Numero de certificacion
      var vCerIds = 'vCerIds' + nSecuencia; // id de certificado
      var vAnio = 'vAnio' + nSecuencia; // año certificado

      var oBtnDel = 'oBtnDel' + nSecuencia; // Boton de Borrar Row

      TD_xAll = cTableRow.insertCell(0);
      TD_xAll.style.width = "40px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML = "<input type = 'text' class = 'letra' style = 'width:040px;border:0;text-align:center;padding:2px' name = '" + cAutSeq + "' id = '" + cAutSeq + "' value = '"+f_Str_Pad(nSecuencia,3,"0","STR_PAD_LEFT")+"'readonly>";

      TD_xAll = cTableRow.insertCell(1);
      TD_xAll.style.width = "200px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML = "<input type = 'text' class = 'letra' style = 'width:200px;border:0;text-align:left;padding:2px'  name = '" + cCliId + "' id = '" + cCliId + "'  " +
        "onBlur = 'javascript:this.value=this.value.toUpperCase(); " +
        "f_Links(\"cCliId\",\"VALID\",\"" + nSecuencia + "\")'>";

      TD_xAll = cTableRow.insertCell(2);
      TD_xAll.style.width = "40px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML = "<input type = 'text' class = 'letra' style = 'width:040px;border:0;text-align:center;padding:2px' name = '" + cCliDv + "' id = '" + cCliDv + "' readonly>";

      TD_xAll = cTableRow.insertCell(3);
      TD_xAll.style.width = "160px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML = "<input type = 'text' class = 'letra' style = 'width:160px;border:0;text-align:center;padding:2px' name = '" + cCliSap + "' id = '" + cCliSap + "' readonly>";

      TD_xAll = cTableRow.insertCell(4);
      TD_xAll.style.width = "440px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML = "<input type = 'text' class = 'letra' style = 'width:440px;border:0;text-align:center;padding:2px' name = '" + cCliNom + "' id = '" + cCliNom + "' readonly>";

      TD_xAll = cTableRow.insertCell(5);
      TD_xAll.style.width = "240px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML = "<input type='text' class='letra' style='width:240px;border:0;text-align:left;padding:2px' name='" + cCerComCsc + "' id='" + cCerComCsc + "' onBlur='javascript:this.value=this.value.toUpperCase();f_Links(\"cCerComCsc\",\"VALID\",\"" + nSecuencia + "\")'>" +
        "<input type='hidden' name='" + vCerIds + "' id='" + vCerIds + "' readonly>" +
        "<input type='hidden' name='" + vAnio + "' id='" + vAnio + "' readonly>";

      TD_xAll = cTableRow.insertCell(6);
      TD_xAll.style.width = "20px";
      TD_xAll.innerHTML = "<input type = 'button' style = 'width:020px;text-align:center' id = " + nSecuencia + " value = 'X' " + "onClick = 'javascript:f_Delete_Row(this.value,\"" + nSecuencia + "\",\"Grid\", this);'>";

      document.forms['frgrm']['nSecuencia'].value = nSecuencia;
    }

    function f_Add_New_Row_Ip(xId, xRow, xChek) {

      var cGrid = document.getElementById("Grid_Es");
      var nLastRow = cGrid.rows.length;
      var nSecuencia = nLastRow + 1;
      var cTableRow = cGrid.insertRow(nLastRow);
      var cTramite = 'cCertificacion' + nSecuencia;
      var cImportador = 'cCliente' + nSecuencia;
      var cFacturara = 'cCodSap' + nSecuencia;
      var cSubCerId = 'cSubCerId' + nSecuencia;
      var cServicio = 'cServicio' + nSecuencia;
      var cSubServicio = 'cSubServicio' + nSecuencia;
      var cNit = 'cNit' + nSecuencia;
      var cObservacion = 'cObservacion' + nSecuencia;
      var cCertiId = 'cCertiId' + nSecuencia;
      var cAnioCer = 'cAnioCer' + nSecuencia;
      var cBase = 'cBase' + nSecuencia;
      var cCheck = 'cCheck' + nSecuencia;

      TD_xAll = cTableRow.insertCell(0);
      TD_xAll.style.width = "140px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML = "<input type = 'text' class = 'letra' style = 'width:140;border:0;text-align:left;padding:2px' name = '" + cTramite + "' id = '" + cTramite + "' readonly>";

      TD_xAll = cTableRow.insertCell(1);
      TD_xAll.style.width = "200px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML = "<input type = 'text' class = 'letra' style = 'width:200;border:0;text-align:left;padding:2px' name = '" + cImportador + "' id = '" + cImportador + "' readonly>";

      TD_xAll = cTableRow.insertCell(2);
      TD_xAll.style.width = "160px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML = "<input type = 'text' class = 'letra' style = 'width:160;border:0;text-align:left;padding:2px' name = '" + cFacturara + "' id = '" + cFacturara + "' readonly>";

      TD_xAll = cTableRow.insertCell(3);
      TD_xAll.style.width = "260px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML = "<input type = 'text' class = 'letra' style = 'width:260;border:0;text-align:left;padding:2px' name = '" + cServicio + "' id = '" + cServicio + "' readonly>";

      TD_xAll = cTableRow.insertCell(4);
      TD_xAll.style.width = "260px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML = "<input type = 'text' class = 'letra' style = 'width:260;border:0;text-align:center;padding:2px' name = '" + cSubServicio + "' id = '" + cSubServicio + "' readonly>" + "<input type='hidden' name='" + cSubCerId + "' id='" + cSubCerId + "' readonly>" + "<input type='hidden' name='" + cNit + "' id='" + cNit + "' readonly>" + "<input type='hidden' name='" + cObservacion + "' id='" + cObservacion + "' readonly>" + "<input type='hidden' name='" + cCertiId + "' id='" + cCertiId + "' readonly>" + "<input type='hidden' name='" + cAnioCer + "' id='" + cAnioCer + "' readonly>";

      TD_xAll = cTableRow.insertCell(5);
      TD_xAll.style.width = "100px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML = "<input type = 'text' class = 'letra' style = 'width:100;border:0;text-align:center;padding:2px' name = '" + cBase + "' id = '" + cBase + "' readonly>";

      TD_xAll = cTableRow.insertCell(6);
      TD_xAll.style.width = "10px";
      TD_xAll.style.textAlign = "center";
      TD_xAll.innerHTML = "<input type='checkbox' name='"+cCheck+"'  value = '" + cCheck + "' id='" + xId + "'" + ((xChek) == true ? " checked" : "") + ">";

      document.forms['frgrm']['nSecuencia_Ip'].value = nSecuencia;
    }

    function f_Enter(e, xName, xGrilla) {
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
          case "Grid":
            if (xName == 'cTerNomInt' + eval(document.forms['frgrm']['nSecuencia'].value)) {
              if (document.forms["frgrm"]['cDocId' + eval(document.forms['frgrm']['nSecuencia'].value)].value !== '') {
                f_Add_New_Row();
              } else {
                alert("LLene los datos antes de Adicionar una nueva Fila.");
              }
            }
            break;
        }
      }
    }

    function f_Delete_Row(xNumRow, xSecuencia, xTabla) {
      var cGrid = document.getElementById(xTabla);
      var nLastRow = cGrid.rows.length;
      if (nLastRow > 1 && xNumRow == "X") {
        if (confirm("Realmente Desea Eliminar La Secuencia [" + xSecuencia + "]?")) {
          if (xSecuencia < nLastRow) {
            var j = 0;
            for (var i = xSecuencia; i < nLastRow; i++) {
              j = parseFloat(i) + 1;
              //document.forms['frgrm']['cAutSeq'    + i].value = f_Str_Pad(i,3,"0","STR_PAD_LEFT"); 
              document.forms['frgrm']['cCliId' + i].value = document.forms['frgrm']['cCliId' + j].value;
              document.forms['frgrm']['cCliDV' + i].value = document.forms['frgrm']['cCliDV' + j].value;
              document.forms['frgrm']['cCliSap' + i].value = document.forms['frgrm']['cCliSap' + j].value;
              document.forms['frgrm']['cCliNom' + i].value = document.forms['frgrm']['cCliNom' + j].value;
              document.forms['frgrm']['cCerComCsc' + i].value = document.forms['frgrm']['cPedComCsc' + j].value;
            }
          }
          cGrid.deleteRow(nLastRow - 1);
          document.forms['frgrm']['nSecuencia'].value = nLastRow - 1;
        }
      } else {
        alert("No se Pueden Eliminar Todas las Secuencias, Verifique.");
      }
    }

    function fnBorrarCert() {
      document.getElementById("Grid").innerHTML = "";
      document.forms['frgrm']['nSecuencia'].value = 0;
      f_Add_New_Row();
    }

    function fnPegarDo() {
      var nSecuencia = document.forms['frgrm']['nSecuencia'].value;
      var nX = screen.width;
      var nY = screen.height;
      var nAncho = 550;
      var nAlto = 250;
      var nNx = (nX - nAncho) / 2;
      var nNy = (nY - nAlto) / 2;
      var cWinOpt = "width=" + nAncho + ",scrollbars=1,height=" + nAlto + ",left=" + nNx + ",top=" + nNy;
      var cPathUrl = "fraecfrm.php?gFunction=PegarDo&gArchivo=fraeccpd.php" +
        "&gSecuencia=" + document.forms['frgrm']['nSecuencia'].value;
      cWindow = window.open(cPathUrl, "PegarDo", cWinOpt);
      cWindow.focus();
    }

    function f_Valida() { //Valida datos de formulario, para poder pintar conceptos de Cobro a excluir
      /* for ($i=0; $i<$_POST['nSecuencia']; $i++) {
        
      } */
     var formulario = document.forms['frgrm'];
     var observaciones = formulario['cObvsCer'].value;
      if (!observaciones) {
        alert('El campo de observaciones es obligatorio');
      }else{
        document.forms['frgrm'].target = 'fmpro';
        document.forms['frgrm'].action = 'fraec20g.php';
        document.forms['frgrm']['cModo'].value = 'VALIDARDO';
        document.forms['frgrm'].submit();
      }
      
    }

    function f_VolverAtras() {
      f_Carga_Data();
      document.forms['frgrm'].target = 'fmwork';
      document.forms['frgrm'].action = 'fraesnue.php';
      document.forms['frgrm']['cStep'].value = '1';
      document.forms['frgrm']['cStep_Ant'].value = '2';
      document.forms['frgrm'].submit();
    }

    function f_Mostrar_u_Ocultar_Objetos(xStep) {
      // Oculto campos de valor FOB y cantidad de formularios que solo aplican para EXPORTACIONES y DTA's.
      switch (xStep) {
        case "1":
          document.getElementById("Grid_Paso1").style.display = "block";
          document.getElementById("Grid_Paso2").style.display = "none";
          document.getElementById("Grid_Paso3").style.display = "none";
          document.getElementById("Grid_Paso4").style.display = "none";
          break;
        case "2":
          document.getElementById("Grid_Paso1").style.display = "none";
          document.getElementById("Grid_Paso2").style.display = "block";
          document.getElementById("Grid_Paso3").style.display = "none";
          document.getElementById("Grid_Paso4").style.display = "none";
          break;
        case "3"://editar
          document.getElementById("Grid_Paso1").style.display = "none";
          document.getElementById("Grid_Paso2").style.display = "none";
          document.getElementById("Grid_Paso3").style.display = "block";
          document.getElementById("Grid_Paso4").style.display = "none";
          break;
        case "4"://ver
          document.getElementById("Grid_Paso1").style.display = "none";
          document.getElementById("Grid_Paso2").style.display = "none";
          document.getElementById("Grid_Paso3").style.display = "none";
          document.getElementById("Grid_Paso4").style.display = "block";
          break;
      }
    }

    function fnGuardar() {
      f_Carga_Data();
      document.forms['frgrm'].target = 'fmpro';
      document.forms['frgrm'].action = 'fraesgra.php';
      document.forms['frgrm']['nTimesSave'].value++;
      document.forms['frgrm'].submit();
    }
  </script>
</head>

<body topmargin=0 leftmargin=0 margnwidth=0 marginheight=0 style='margin-right:0'>
  <center>
    <table border="0" cellpadding="0" cellspacing="0" width="1160">
      <tr>
        <td>
          <fieldset>
            <legend>
              <font color="red"><?php echo (($_COOKIE['kModo'] == 'MASIVA') ? "Nueva" : $_COOKIE['kModo']) . " " . $_COOKIE['kProDes'] ?></font>
            </legend>
            <form name='frgrm' action='fraesgra.php' method='post' target='fmpro'>
              <input type="hidden" name="cStep" value="<?php echo $_POST['cStep'] ?>">
              <input type="hidden" name="cStep_Ant" value="<?php echo $_POST['cStep_Ant'] ?>">
              <input type="hidden" name="cTarExc" value="">
              <input type="hidden" name="nRecords" value="<?php echo $_POST['nRecords'] ?>">
              <input type="hidden" name="nTimesSave" value="0">
              <input type="hidden" name="nSecuencia" value="">
              <input type="hidden" name="nSecuencia_Ip" value="">
              <input type="hidden" name="cModo" value="">
              
              <textarea name="cComMemo" id="cComMemo"><?php echo $_POST['cComMemo'] ?></textarea>
              <textarea name="cTramites" id="cTramites"><?php echo $_POST['cTramites'] ?></textarea>
              <center>
                <script languaje="javascript">
                  document.getElementById("cTramites").style.display = "none";
                  document.getElementById("cComMemo").style.display = "none";
                </script>

                <div id="Grid_Paso1">
                  
                  <?php 
                  if ($_POST['cStep'] == "") {
                          $_POST['cStep'] = "1";
                        } ?>
                  <fieldset>
                    <legend><b>Seleccione las Certificaciones</b></legend>
                    <center>
                      <table border='0' cellpadding='0' cellspacing='0' width='1160'>
                        <?php $nCol = f_Format_Cols(58);
                        echo $nCol; ?>
                        <tr height="25px">
                          <td bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>" class="clase08" colspan="02" align="center">&nbsp;&nbsp;Suc</td>
                          <td bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>" class="clase08" colspan="10" align="center">&nbsp;&nbsp;Nit</td>
                          <td bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>" class="clase08" colspan="02" align="center">Dv</td>
                          <td bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>" class="clase08" colspan="08" align="center">Cod SAP</td>
                          <td bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>" class="clase08" colspan="22" align="center">&nbsp;&nbsp;Razon Social</td>
                          <td bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>" class="clase08" colspan="12" align="center">&nbsp;&nbsp;&nbsp;No. Certificacion</td>
                          <td bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>" class="clase08" colspan="02" align="right">
                            <img src="<?php echo $cPlesk_Skin_Directory ?>/btn_create-dir_bg.gif" onClick="javascript:f_Add_New_Row()" style="cursor:pointer" title="Agregar">
                            <img src="<?php echo $cPlesk_Skin_Directory ?>/b_drop.png" onClick="javascript:fnBorrarCert()" style="cursor:pointer" title="Eliminar Todos">
                          </td>
                        </tr>
                      </table>
                      <table border="0" cellpadding="0" cellspacing="0" width="1160" id="Grid"></table>
                    </center>
                    <script languaje="javascript">
                      f_Add_New_Row();
                    </script>
                  </fieldset>
                  <fieldset>
                    <legend><b>Observaciones</b></legend>
                    <textarea name="cObvsCer" id="cObvsCer"></textarea>
                  </fieldset>

                </div>                              

                <fieldset id="Grid_Paso2">
                  <legend><b>Seleccione los Servicios a Excluir</b></legend>
                  <center>
                    <table border='0' cellpadding='0' cellspacing='0' width='1160'>
                      <?php $nCol = f_Format_Cols(58);
                      echo $nCol; ?>
                      <tr height="25px">
                        <td bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>" class="clase08" colspan="08" align="left">&nbsp;&nbsp;No. Certificacion</td>
                        <td bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>" class="clase08" colspan="10" align="left">&nbsp;&nbsp;&nbsp;Cliente</td>
                        <td bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>" class="clase08" colspan="08" align="left">&nbsp;&nbsp;&nbsp;Cod SAP</td>
                        <td bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>" class="clase08" colspan="13" align="left">&nbsp;&nbsp;&nbsp;Servicio</td>
                        <td bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>" class="clase08" colspan="15" align="left">&nbsp;&nbsp;Subservicio</td>
                        <td bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>" class="clase08" colspan="03" align="left">&nbsp;&nbsp;Base</td>
                        <td bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>" class="clase08" colspan="01" align="left"><input type="checkbox" name="nCheckAll" onClick="javascript:f_Marca()"></td>
                      </tr>
                    </table>
                    <table border="0" cellpadding="0" cellspacing="0" width="1160" id="Grid_Es"></table>
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
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/siguiente.gif" style="cursor:pointer" onClick="javascript:f_Valida();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Siguiente
            </td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand" onClick="javascript:f_Retorna();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
            </td>
          </tr>
        </table>
      <?php
        break;
      case "2": ?>
        <table border="0" cellpadding="0" cellspacing="0" id='bnt_Paso2' width="1160">
          <tr>
            <td width="887" height="21"></td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/anterior.gif" style="cursor:pointer" onClick="javascript:f_VolverAtras();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Anterior</td>
            <td width="91" height="21" Class="name">
              <input type="button" name="name" value="Guardar" style="background:url(<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif);width:91;height:21;border:0px;font-weight:bold;color:#555555;" onclick="javascript:fnGuardar()">
            </td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand" onClick="javascript:f_Retorna();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
            </td>
          </tr>
        </table>
    <?php
        break;
    } ?>
  </center>
  <!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
  <script languaje="javascript">
    f_Mostrar_u_Ocultar_Objetos("<?php echo $_POST['cStep'] ?>");
  </script>
  <?php
    if ($_POST['cStep'] == "2") {

      $mDataDetalles = array();
      $cTraSel = "";
      for ($i=0; $i<$_POST['nSecuencia']; $i++) {

        if ($_POST['cCerComCsc'.($i+1)] != "") {
          
          $cTraSel .= "{$_POST['vCerIds'.($i+1)]}~{$_POST['vAnio'.($i+1)]}|";
      
          $vCerIds  = "{$_POST['vCerIds'.($i+1)]}";
          $cAnio = "{$_POST['vAnio'.($i+1)]}";
      
          // Consulta la información de detalle de la certificación
          $qCertifiDet  = "SELECT ";
          $qCertifiDet .= "$cAlfa.lcca$cAnio.comidxxx, ";
          $qCertifiDet .= "$cAlfa.lcca$cAnio.comcodxx, ";
          $qCertifiDet .= "$cAlfa.lcca$cAnio.comprexx, ";
          $qCertifiDet .= "$cAlfa.lcca$cAnio.comcscxx, ";
          $qCertifiDet .= "$cAlfa.lcca$cAnio.comcsc2x, ";
          $qCertifiDet .= "$cAlfa.lcca$cAnio.cliidxxx, ";
          $qCertifiDet .= "$cAlfa.lcca$cAnio.comfecxx, ";
          $qCertifiDet .= "$cAlfa.lpar0150.clinomxx, ";
          $qCertifiDet .= "$cAlfa.lcde$cAnio.*, ";
          $qCertifiDet .= "$cAlfa.lpar0011.sersapxx, ";
          $qCertifiDet .= "$cAlfa.lpar0011.serdesxx ";
          $qCertifiDet .= "FROM $cAlfa.lcde$cAnio ";
          $qCertifiDet .= "LEFT JOIN $cAlfa.lcca$cAnio ON $cAlfa.lcde$cAnio.ceridxxx = $cAlfa.lcca$cAnio.ceridxxx ";
          $qCertifiDet .= "LEFT JOIN $cAlfa.lpar0011 ON $cAlfa.lcde$cAnio.sersapxx = $cAlfa.lpar0011.sersapxx ";
          $qCertifiDet .= "LEFT JOIN $cAlfa.lpar0150 ON $cAlfa.lcca$cAnio.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
          $qCertifiDet .= "WHERE ";
          $qCertifiDet .= "$cAlfa.lcde$cAnio.ceridxxx = $vCerIds";
      
          // Depuración
          //var_dump($qCertifiDet);
      }
      $xCertifiDet  = f_MySql("SELECT","",$qCertifiDet,$xConexion01,"");
        // f_Mensaje(__FILE__,__LINE__,$qTramite." ~ ".mysql_num_rows($xTramite));
        if (mysql_num_rows($xCertifiDet) > 0) {
          while ($xRT = mysql_fetch_array($xCertifiDet)) {
            $nInd_mDataDetalle = count($mDataDetalles);
            $mDataDetalles[$nInd_mDataDetalle] = $xRT;
          }
        } 
      } 
      
      //Matriz para verificar si ya habia sido marcado
      $vCheckMarcados = array();
      $vCheckMarcados = explode("|",$_POST['cComMemo']);
      
      for ($i=0; $i<count($mDataDetalles); $i++) {
          
        $cId = $mDataDetalles[$i]['comidxxx']."~".$mDataDetalles[$i]['cliidxxx']."~".$mDataDetalles[$i]['subidxxx'];
        
        if (count($vCheckMarcados) > 0) {
          $mDataDetalles[$i]['excluida'] = false;
          if (in_array($cId, $vCheckMarcados) == true) {
            $mDataDetalles[$i]['excluida'] = true;
          }
        } ?>
        <script languaje = "javascript">
          f_Add_New_Row_Ip("<?php echo $cId ?>","<?php echo count($mDataDetalles) ?>","<?php echo $mDataDetalles[$i]['excluida'] ?>");
          
          document.forms['frgrm']['cCertificacion'   + document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo "{$mDataDetalles[$i]['comidxxx']}-{$mDataDetalles[$i]['comprexx']}-{$mDataDetalles[$i]['comcscxx']}"; ?>";
          document.forms['frgrm']['cCliente'+ document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo "{$mDataDetalles[$i]['clinomxx']} [{$mDataDetalles[$i]['cliidxxx']}]"; ?>";
          document.forms['frgrm']['cCodSap' + document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo "{$mDataDetalles[$i]['sersapxx']}"; ?>";
          document.forms['frgrm']['cSubCerId'    + document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo "{$mDataDetalles[$i]['subidxxx']}"; ?>";
          document.forms['frgrm']['cServicio'    + document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo "{$mDataDetalles[$i]['serdesxx']}"; ?>";
          document.forms['frgrm']['cSubServicio'    + document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo "{$mDataDetalles[$i]['subdesxx']}"; ?>";
          document.forms['frgrm']['cNit'    + document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo "{$mDataDetalles[$i]['cliidxxx']}"; ?>";
          document.forms['frgrm']['cObservacion'    + document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo "{$_POST['cObvsCer']}"; ?>";
          document.forms['frgrm']['cCertiId'    + document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo "{$mDataDetalles[$i]['ceridxxx']}"; ?>";
          document.forms['frgrm']['cAnioCer'    + document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo substr($mDataDetalles[$i]['comfecxx'], 0, 4); ?>";
          document.forms['frgrm']['cAnioCer'    + document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo substr($mDataDetalles[$i]['comfecxx'], 0, 4); ?>";
          
          document.forms['frgrm']['cBase'    + document.forms['frgrm']['nSecuencia_Ip'].value].value = "<?php echo "{$mDataDetalles[$i]['basexxxx']}"; ?>"; 
        </script>
      <?php }  ?>
      <script languaje="javascript">
        document.forms['frgrm']['cTramites'].value  = "<?php echo $cTraSel ?>";
        document.forms['frgrm']['nSecuencia'].value = "<?php echo $_POST['nSecuencia'] ?>";
        document.forms['frgrm']['nRecords'].value   = "<?php echo count($mDataDetalles) ?>";
      </script>
      <?php
    }
  ?>
</body>

</html>
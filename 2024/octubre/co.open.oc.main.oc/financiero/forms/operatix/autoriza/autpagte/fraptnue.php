<?php 
  namespace openComex;
  /**
   * Formulario Autorizacion Pagos a Terceros.
   * --- Descripcion: Pemirte guardar las Observaciones a los DO's seleccionados.
   * @author Juan Jose Hernandez <juan.hernandez@openits.co>
   * @version 001
   */

  include("../../../../libs/php/utility.php");
?>
<html>
<head>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
    <script language = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
    <script language = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
    <script language = 'javascript'>
    
    function fnRetorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
      document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
      parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
    }

    function fnLinks(xLink,xSwitch,xSecuencia,xGrid) {
      var zX    = screen.width;
      var zY    = screen.height;
      switch (xLink){
        case "cDocId":
          var nSwitch = 0; var cMsj = "";

          if (document.forms['frgrm']['cDocId'+xSecuencia].value != "") {
            if (xSwitch == "VALID") {           
              var cRuta  = "frapt121.php?gModo=VALID&gFunction="+xLink+
                           "&gDocId="+document.forms['frgrm']['cDocId'+xSecuencia].value.toUpperCase()+
                           "&gSecuencia="+xSecuencia;
              // alert(cRuta);
              parent.fmpro.location = cRuta;
            } else {
              var zNx     = (zX-800)/2;
              var zNy     = (zY-300)/2;
              var zWinPro = 'width=800,scrollbars=1,height=350,left='+zNx+',top='+zNy;
              var cRuta   = "fraptfrm.php?gModo=WINDOW&gArchivo=frapt121.php&gFunction="+xLink+
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

    function fnValidateExist(cSuc, cDo, cSuf) {
      var cGrid    = document.getElementById("Grid_Do");
      var nLastRow = cGrid.rows.length;
      var bExiste  = false;

      for(let i=0; i<nLastRow; i++) {
        var cSucId = cGrid.rows[i].cells[1].children['cSucId'+(i+1)].value;
        var cDocId = cGrid.rows[i].cells[2].children['cDocId'+(i+1)].value;
        var cDocSuf = cGrid.rows[i].cells[3].children['cDocSuf'+(i+1)].value;
        if(cSuc == cSucId && cDo == cDocId && cSuf == cDocSuf) {
          bExiste = true;
        }
      }

      return bExiste;
    }

    function fnAddNewRowDo() {
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
      TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:040;border:0;text-align:center' name = '"+cDocSeq+"' id = '"+cDocSeq+"' value = '"+f_Str_Pad(nSecuencia,3,"0","STR_PAD_LEFT")+"'readonly>";
      
      TD_xAll = cTableRow.insertCell(1);
      TD_xAll.style.width  = "40px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:040;border:0;text-align:center' name = '"+cSucId+"' id = '"+cSucId+"' readonly>";
      
      TD_xAll = cTableRow.insertCell(2);
      TD_xAll.style.width  = "140px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:140;border:0;text-align:left'  name = '"+cDocId+"' id = '"+cDocId+"'  "+
                          "onBlur = 'javascript:this.value=this.value.toUpperCase(); "+
                                    "fnLinks(\"cDocId\",\"VALID\",\""+nSecuencia+"\")'>";

      TD_xAll = cTableRow.insertCell(3);
      TD_xAll.style.width  = "40px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:040;border:0;text-align:center' name = '"+cDocSuf+"' id = '"+cDocSuf+"' readonly>";
      
      TD_xAll = cTableRow.insertCell(4);
      TD_xAll.style.width  = "120px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:120;border:0;text-align:center' name = '"+cDocTip+"' id = '"+cDocTip+"' readonly>";
      
      TD_xAll = cTableRow.insertCell(5);
      TD_xAll.style.width  = "100px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:100;border:0;text-align:center' name = '"+cCliId+"' id = '"+cCliId+"' readonly>";
      
      TD_xAll = cTableRow.insertCell(6);
      TD_xAll.style.width  = "20px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:020;border:0;text-align:center' name = '"+cCliDv+"' id = '"+cCliDv+"' readonly>";
      
      TD_xAll = cTableRow.insertCell(7);
      TD_xAll.style.width  = "260px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:260;border:0;text-align:left' name = '"+cCliNom+"' id = '"+cCliNom+"' onKeyUp='javascript:fnEnter(event,this.name,\"Grid_Do\");' readonly>";
      
      TD_xAll = cTableRow.insertCell(8);
      TD_xAll.style.width  = "20px";
      TD_xAll.innerHTML    = "<input type = 'button' style = 'width:020;text-align:center' id = "+nSecuencia+" value = 'X' "+
                              "onClick = 'javascript:fnDeleteRow(this.value,\""+nSecuencia+"\",\"Grid_Do\", this);'>";
                              
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
          case "Grid_Do":
            if (xName == 'cCliNom'+eval(document.forms['frgrm']['nSecuencia'].value)) {
              if (document.forms["frgrm"][xName].value !== '' ) {
                fnAddNewRowDo();
              } else {
                alert("Seleccione un DO antes de Adicionar una nueva Fila.");
              }
            }
          break;
        }
      }
    }
    
    function fnDeleteRow(xNumRow,xSecuencia,xTabla) {        
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
    
    function fnPegarDo() {
      var nSecuencia = document.forms['frgrm']['nSecuencia'].value;
      var nX    = screen.width;
      var nY    = screen.height;
      var nAncho = 550;
      var nAlto  = 250;
      var nNx      = (nX-nAncho)/2;
      var nNy      = (nY-nAlto)/2;
      var cWinOpt  = "width="+nAncho+",scrollbars=1,height="+nAlto+",left="+nNx+",top="+nNy;
      var cPathUrl = "fraptfrm.php?gFunction=PegarDo&gArchivo=fraptcpd.php"+
                    "&gSecuencia="+document.forms['frgrm']['nSecuencia'].value;
      cWindow = window.open(cPathUrl,"PegarDo",cWinOpt);
      cWindow.focus();
    }
    
    function fnGuardar(){
      var nSwitch = 0;  
      var cMsj = "\n";

      if(document.forms['frgrm']['cObs'].value == '') {
        alert("Debe Diligenciar La Observacion.");
        return;
      }

      if (confirm("Esta Seguro de Autorizar los Pagos a Terceros de los DO Seleccionados?")) {
        document.forms['frgrm'].submit();
      }
    }
    
    function fnBorrarDos(){
      document.getElementById("Grid_Do").innerHTML = "";
      fnAddNewRowDo();
    }
  </script>
  </head>
    <body topmargin = 0 leftmargin = 0 marginwidth = 0 marginheight = 0 style = 'margin-right:0'>
    <center>
      <table border ="0" cellpadding="0" cellspacing="0" width="800">
        <tr>
          <td>
            <fieldset>
              <!-- se pinta el fielset con el proceso y la descripcion de la cookie -->
              <legend>Nuevo <?php echo $_COOKIE['kProDes'] ?></legend>
              <form name = 'frgrm' action = 'fraptgra.php' method = 'post' target='fmpro'>
                <input type = "hidden" name = "nSecuencia"  value = "">
                <center>
                  <table border = '0' cellpadding = '0' cellspacing = '0' width='780'>
                    <?php $nCol = f_Format_Cols(39); echo $nCol;?>
                    <tr>
                      <td Class = "name" colspan = "41">Observaci&oacute;n<br>
                        <textarea type = "text" Class = "letra" name = "cObs" style = "width:800;height:25px;margin-bottom: 10px;"
                          onFocus="javascript:this.style.background='#00FFFF';"
                          onblur ="javascript:this.style.background='#FFFFFF'"></textarea>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="40" class= "clase08" align="right">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/page_go.png" onClick = "javascript:fnPegarDo()" style = "cursor:pointer" title="Pegar DOs">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_drop.png" onClick = "javascript:fnBorrarDos()" style = "cursor:pointer" title="Eliminar Todos">
                      </td>                       
                    </tr>
                    <tr>
                      <td class = "clase08" colspan="02" align="left">Seq.</td>                                                         
                      <td class = "clase08" colspan="02" align="left">Suc</td>                                                          
                      <td class = "clase08" colspan="07" align="left">Do</td>
                      <td class = "clase08" colspan="02" align="left">Suf</td>
                      <td class = "clase08" colspan="06" align="left">&nbsp;&nbsp;Operaci&oacute;n</td>                                                                     
                      <td class = "clase08" colspan="05" align="left">&nbsp;&nbsp;Nit</td>
                      <td class = "clase08" colspan="01" align="left">&nbsp;&nbsp;Dv</td>
                      <td class = "clase08" colspan="12" align="left">&nbsp;&nbsp;&nbsp;Importador</td>
                      <td class = "clase08" colspan="02" align="right">&nbsp;</td>                       
                    </tr>
                  </table>
                  <table border = "0" cellpadding = "0" cellspacing = "0" width = "800" id = "Grid_Do"></table>
                </center>
              </form>
            </fieldset>
          </td>
        </tr>
      </table>
    
      <table border="0" cellpadding="0" cellspacing="0" width="800">
        <tr height="21">
              <td width="618" height="21">&nbsp;</td>
              <td width="91" height="21" class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer"
                onClick = "javascript:fnGuardar();" style = "cursor:hand" id="IdImg">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Autorizar</td>
              <td width="91" height="21" class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>          
        </tr>
      </table>
    </center>
    <script languaje = "javascript">
      fnAddNewRowDo();
    </script>
  </body>        
  </body> 
</html>
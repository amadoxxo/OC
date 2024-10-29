<?php 
  namespace openComex;

  /**
   * Formulario Cambio de Fecha Prefactura (Legalizada).
   * --- Descripcion: Permite Buscar Prefacturas Legalizadas Por Anio y Cambiar su Fecha.
   * @author Cristian Camilo Segura V <cristian.segura@open-eb.co>
   * @package Opencomex
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
        case "cComCsc":
          var nSwitch = 0; var cMsj = "";
          if (document.forms['frgrm']['cComCsc'+xSecuencia].value != "") {
            if (xSwitch == "VALID") {      
              var cRuta  = "frafcocx.php?gModo=VALID&gFunction="+xLink+
                           "&gComCsc="+document.forms['frgrm']['cComCsc'+xSecuencia].value.toUpperCase()+
                           "&gSecuencia="+xSecuencia+
                           "&gPreAnio="+document.forms['frgrm']['cPreAnio'].value+
                           "&gProvien=frfplnue";
              // alert(cRuta);
              parent.fmpro.location = cRuta;
            } else {
              // alert(cRuta);
              var zNx     = (zX-800)/2;
              var zNy     = (zY-300)/2;
              var zWinPro = 'width=800,scrollbars=1,height=350,left='+zNx+',top='+zNy;
              var cRuta   = "frfcofrm.php?gModo=WINDOW&gArchivo=frafcocx.php&gFunction="+xLink+
                            "&gComCsc="+document.forms['frgrm']['cComCsc'+xSecuencia].value.toUpperCase()+
                            "&gSecuencia="+xSecuencia+
                            "&gPreAnio="+document.forms['frgrm']['cPreAnio'].value+
                            "&gProvien=frfplnue";
              zWindow = window.open(cRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          } else {
            alert("Digite el Consecutivo de la PREFACTURA.")
          }
        break;
      }
    }
    
    function fnAddNewRowPre() {
      
      var cGrid      = document.getElementById("Grid_Pre");
      var nLastRow   = cGrid.rows.length;
      var nSecuencia = nLastRow+1;
      var cTableRow  = cGrid.insertRow(nLastRow);
      var cDocSeq    = 'cDocSeq'+ nSecuencia; 

      var cComId     = 'cComId' + nSecuencia; 
      var cComCod    = 'cComCod' + nSecuencia; 
      var cComCsc    = 'cComCsc'+ nSecuencia; 
      var cComCsc2  = 'cComCsc2'+ nSecuencia; 
      var cComFech   = 'cComFech'+ nSecuencia; 
      var cTerId     = 'cTerId' + nSecuencia;
      var cTerIdDv   = 'cTerIdDv' + nSecuencia;
      var cCliNom    = 'cCliNom'+ nSecuencia; 

      var oBtnDel  = 'oBtnDel'+ nSecuencia; // Boton de Borrar Row
      
      var TD_xAll = cTableRow.insertCell(0);
      TD_xAll.style.width  = "50px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:050;border:0;text-align:center' name = '"+cDocSeq+"' id = '"+cDocSeq+"' value = '"+f_Str_Pad(nSecuencia,3,"0","STR_PAD_LEFT")+"'readonly>";
      
      TD_xAll = cTableRow.insertCell(1);
      TD_xAll.style.width  = "40px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:040;border:0;text-align:center' name = '"+cComId+"' id = '"+cComId+"' readonly>";
      
      TD_xAll = cTableRow.insertCell(2);
      TD_xAll.style.width  = "50px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:50;border:0;text-align:left'  name = '"+cComCod+"' id = '"+cComCod+"' readonly >";
                                    
      TD_xAll = cTableRow.insertCell(3);
      TD_xAll.style.width  = "100px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:100;border:0;text-align:center' name = '"+cComCsc+"' id = '"+cComCsc+"'  "+ 
                             "onBlur = 'javascript:this.value=this.value.toUpperCase(); "+
                             "fnLinks(\"cComCsc\",\"VALID\",\""+nSecuencia+"\")'>" +
                             //nuevo campo       
                             "<input type = 'hidden' class = 'letra' style = 'width:100;border:0;text-align:center' name = '"+cComCsc2+"' id = '"+cComCsc2+"' readonly>";
              
      TD_xAll = cTableRow.insertCell(4);
      TD_xAll.style.width  = "90px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:90;border:0;text-align:center' name = '"+cComFech+"' id = '"+cComFech+"' readonly>";
      
      TD_xAll = cTableRow.insertCell(5);
      TD_xAll.style.width  = "90px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:90;border:0;text-align:center' name = '"+cTerId+"' id = '"+cTerId+"' readonly>";
      
      TD_xAll = cTableRow.insertCell(6);
      TD_xAll.style.width  = "25px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:025;border:0;text-align:center' name = '"+cTerIdDv+"' id = '"+cTerIdDv+"' readonly>";
      
      TD_xAll = cTableRow.insertCell(7);
      TD_xAll.style.width  = "335px";
      TD_xAll.style.border = "1px solid #E6E6E6";
      TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:335;border:0;text-align:left' name = '"+cCliNom+"' id = '"+cCliNom+"' onKeyUp='javascript:f_Enter(event,this.name,\"Grid_Pre\");' readonly>";
      
      TD_xAll = cTableRow.insertCell(8);
      TD_xAll.style.width  = "20px";
      TD_xAll.innerHTML    = "<input type = 'button' style = 'width:020;text-align:center' id = "+nSecuencia+" value = 'X' "+
                              "onClick = 'javascript:f_Delete_Row(this.value,\""+nSecuencia+"\",\"Grid_Pre\", this);'>";
                              
      document.forms['frgrm']['nSecuencia'].value = nSecuencia;
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
          case "Grid_Pre":
            if (xName == 'cCliNom'+eval(document.forms['frgrm']['nSecuencia'].value)) {
              if (document.forms["frgrm"][xName].value !== '' ) {
                fnAddNewRowPre();
              } else {
                alert("Seleccione una PREFACTURA Antes de Adicionar una Nueva Fila.");
              }
            }
          break;
        }
      }
    }
    
    function f_Delete_Row(xNumRow,xSecuencia,xTabla) {        
      switch (xTabla) {
        case "Grid_Pre": 
          var cGrid = document.getElementById(xTabla);
          var nLastRow = cGrid.rows.length;
          if (nLastRow > 1 && xNumRow == "X") {
            if (confirm("Realmente Desea Eliminar la PREFACTURA ["+document.forms["frgrm"]["cComId"+xSecuencia].value+"-"+document.forms["frgrm"]["cComCod"+xSecuencia].value+"-"+document.forms['frgrm']['cComCsc'+xSecuencia].value+"]?")){ 
              if(xSecuencia < nLastRow){
                var j=0;
                for(var i=xSecuencia;i<nLastRow;i++){
                  j = parseFloat(i)+1;
                  document.forms['frgrm']['cDocSeq'+ i].value = f_Str_Pad(i,3,"0","STR_PAD_LEFT"); 
                  document.forms['frgrm']['cComId' + i].value = document.forms['frgrm']['cComId' + j].value; 
                  document.forms['frgrm']['cComCod' + i].value = document.forms['frgrm']['cComCod' + j].value; 
                  document.forms['frgrm']['cComCsc'+ i].value = document.forms['frgrm']['cComCsc'+ j].value;
                  document.forms['frgrm']['cComCsc2'+ i].value = document.forms['frgrm']['cComCsc2'+ j].value;
                  document.forms['frgrm']['cComFech'+ i].value = document.forms['frgrm']['cComFech'+ j].value;
                  document.forms['frgrm']['cTerId' + i].value = document.forms['frgrm']['cTerId' + j].value; 
                  document.forms['frgrm']['cTerIdDv' + i].value = document.forms['frgrm']['cTerIdDv' + j].value; 
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
    
    function fnPegarF() {
      var nSecuencia = document.forms['frgrm']['nSecuencia'].value;
      var nX    = screen.width;
      var nY    = screen.height;
      var nAncho = 550;
      var nAlto  = 250;
      var nNx      = (nX-nAncho)/2;
      var nNy      = (nY-nAlto)/2;
      var cWinOpt  = "width="+nAncho+",scrollbars=1,height="+nAlto+",left="+nNx+",top="+nNy;
      var cPathUrl = "frfcofrm.php?gFunction=PegarF&gArchivo=frfcoccp.php"+
                     "&gSecuencia="+document.forms['frgrm']['nSecuencia'].value+
                     "&gPreAnio="+document.forms['frgrm']['cPreAnio'].value+
                     "&gProvien=frfplnue";
      cWindow = window.open(cPathUrl,"PegarF",cWinOpt);
      cWindow.focus();
    }

    function fnBorrarF(){
      document.getElementById("Grid_Pre").innerHTML = "";
      fnAddNewRowPre();
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
              <legend>Nuevo <?php echo $_COOKIE['kMenDes'] ?></legend>
              <form name = 'frgrm' action = 'frfplgra.php' method = 'post' target='fmpro'>
                <input type = "hidden" name = "nSecuencia"  value = "">
                <center>
                  <table border = '0' cellpadding = '0' cellspacing = '0' width='800'>
                    <?php $nCol = f_Format_Cols(40);
                    echo $nCol;?>
                    <tr>
                      <td Class = 'name' colspan = '5'><a href='javascript:show_calendar("frgrm.dFecNue")' id="id_href_dFecNue">NUEVA FECHA</a><br>
                        <input type = 'text' style = 'width:100;text-align: left' name = 'dFecNue' onBlur = "javascript:f_Date(this)">                    
                      </td>
                      <td class="name" colspan = "31">OBSERVACI&Oacute;N<br>
                        <input type = "text" style="width:620" name="cObserv">
                      </td>
                      <td colspan = "4" class ='name'>A&Ntilde;O<br>
                        <select name = "cPreAnio" style="width:80;height:20">
                          <?php for($i=$vSysStr['financiero_ano_instalacion_modulo'];$i<=date('Y');$i++){ ?>
                            <option value="<?php echo $i ?>"<?php echo ($i==date('Y') ? " selected" : "") ?>><?php echo $i ?></option>
                          <?php  } ?>
                        </select>
                      </td>
                    </tr>
                  </table>
                  <br>
                  <table border = '0' cellpadding = '0' cellspacing = '0' width='800'>
                    <?php $nCol = f_Format_Cols(39); echo $nCol;?>
                    <tr>
                      <td colspan="40" class= "clase08" align="right">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/page_go.png" onClick = "javascript:fnPegarF()" style = "cursor:pointer" title="Pegar PREFACTURA">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_drop.png" onClick = "javascript:fnBorrarF()" style = "cursor:pointer" title="Eliminar Todos">
                      </td>                       
                    </tr>
                    <tr>
                      <td class = "clase08" colspan="02" align="left">SEQ.</td>                                                         
                      <td class = "clase08" colspan="02" align="left">&nbsp;&nbsp;ID</td>                                                          
                      <td class = "clase08" colspan="03" align="left">&nbsp;&nbsp;COD</td>
                      <td class = "clase08" colspan="05" align="left">PREFACTURA</td>
                      <td class = "clase08" colspan="05" align="left">FECHA</td> 
                      <td class = "clase08" colspan="04" align="left">NIT</td>
                      <td class = "clase08" colspan="01" align="left">&nbsp;&nbsp;DV</td>
                      <td class = "clase08" colspan="15" align="left">&nbsp;&nbsp;&nbsp;IMPORTADOR</td>
                      <td class = "clase08" colspan="02" align="right">&nbsp;</td>
                    </tr>
                  </table>
                  <table border = "0" cellpadding = "0" cellspacing = "0" width = "800" id = "Grid_Pre"></table>
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
            style = "cursor:hand" id="IdImg" onclick="javascript:document.forms['frgrm'].submit();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
          </td>
          <td width="91" height="21" class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>          
        </tr>
      </table>
    </center>
    <script languaje = "javascript">
      fnAddNewRowPre();
    </script>
  </body>        
  </body> 
</html>
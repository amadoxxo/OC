<?php
  namespace openComex;
/**
	 * Proceso Autorizacion Excluir Conceptos de Cobro
	 * --- Descripcion: Permite Crear un Nueva autorizacion para Excluir conceptos de Cobro para Facturacion.
	 * @author Yulieth Campos <ycampos@opentecnologia.com.co>
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
                var cRuta  = "fraec121.php?gModo=VALID&gFunction="+xLink+
                             "&gDocId="+document.forms['frgrm']['cDocId'+xSecuencia].value.toUpperCase()+
                             "&gSecuencia="+xSecuencia;
                // alert(cRuta);
                parent.fmpro.location = cRuta;
              } else {
                var zNx     = (zX-800)/2;
                var zNy     = (zY-300)/2;
                var zWinPro = 'width=800,scrollbars=1,height=350,left='+zNx+',top='+zNy;
                var cRuta   = "fraecfrm.php?gModo=WINDOW&gArchivo=fraec121.php&gFunction="+xLink+
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
          case "cTerIdInt":
          case "cTerNomInt":
          
            if (document.forms['frgrm']['cCcAplFa'+xSecuencia].value == "SI") {
              if (xSwitch == "VALID") {
                var cRuta = "fraecint.php?gModo="+xSwitch+"&gFunction="+xLink+
                                          "&gTerId="+document.forms['frgrm']['cCliId'+xSecuencia].value+
                                          "&gTerIdInt="+document.forms['frgrm'][xLink+xSecuencia].value+
                                          "&gSecuencia="+xSecuencia;
                //alert(cRuta);
                parent.fmpro.location = cRuta;
              } else {
                var zNx      = (zX-600)/2;
                var zNy      = (zY-250)/2;
                var cWinOpt  = "width=600,scrollbars=1,height=250,left="+zNy+",top="+zNy;
                var cRuta = "fraecint.php?gModo="+xSwitch+"&gFunction="+xLink+
                                           "&gTerId="+document.forms['frgrm']['cCliId'+xSecuencia].value+
                                           "&gTerIdInt="+document.forms['frgrm'][xLink+xSecuencia].value+
                                           "&gSecuencia="+xSecuencia;
                cWindow = window.open(cRuta,xLink,cWinOpt);
                cWindow.focus();
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
          var cCliNom  = 'cCliNom'+ nSecuencia; 
          var oBtnDel  = 'oBtnDel'+ nSecuencia; // Boton de Borrar Row
          
          //Campos de Factura A, cuando para el importador aplica la condicion comercial "Aplicar tarifas del Facturar a"
          var cTerIdInt   = 'cTerIdInt' + nSecuencia;
          var cTerDVInt   = 'cTerDVInt' + nSecuencia;
          var cTerNomInt  = 'cTerNomInt'+ nSecuencia;
          var cCcAplFa    = 'cCcAplFa'  + nSecuencia;
          
          var TD_xAll = cTableRow.insertCell(0);
          TD_xAll.style.width  = "40px";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:040px;border:0;text-align:center;padding:2px' name = '"+cDocSeq+"' id = '"+cDocSeq+"' value = '"+f_Str_Pad(nSecuencia,3,"0","STR_PAD_LEFT")+"'readonly>";
          
          TD_xAll = cTableRow.insertCell(1);
          TD_xAll.style.width  = "40px";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:040px;border:0;text-align:center;padding:2px' name = '"+cSucId+"' id = '"+cSucId+"' readonly>";
          
          TD_xAll = cTableRow.insertCell(2);
          TD_xAll.style.width  = "140px";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:140px;border:0;text-align:left;padding:2px'  name = '"+cDocId+"' id = '"+cDocId+"'  "+
                              "onBlur = 'javascript:this.value=this.value.toUpperCase(); "+
                                        "f_Links(\"cDocId\",\"VALID\",\""+nSecuencia+"\")'>";
                                        
          TD_xAll = cTableRow.insertCell(3);
          TD_xAll.style.width  = "40px";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:040px;border:0;text-align:center;padding:2px' name = '"+cDocSuf+"' id = '"+cDocSuf+"' readonly>";
          
          TD_xAll = cTableRow.insertCell(4);
          TD_xAll.style.width  = "120px";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:120px;border:0;text-align:center;padding:2px' name = '"+cDocTip+"' id = '"+cDocTip+"' readonly>";
          
          TD_xAll = cTableRow.insertCell(5);
          TD_xAll.style.width  = "100px";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:100px;border:0;text-align:center;padding:2px' name = '"+cCliId+"' id = '"+cCliId+"' readonly>";
          
          TD_xAll = cTableRow.insertCell(6);
          TD_xAll.style.width  = "20px";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:020px;border:0;text-align:center;padding:2px' name = '"+cCliDv+"' id = '"+cCliDv+"' readonly>";
          
          TD_xAll = cTableRow.insertCell(7);
          TD_xAll.style.width  = "220px";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:220px;border:0;text-align:left;padding:2px' name = '"+cCliNom+"' id = '"+cCliNom+"' readonly>";
          
          TD_xAll = cTableRow.insertCell(8);
          TD_xAll.style.width  = "100px";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:100px;border:0;text-align:left;padding:2px'  name = '"+cTerIdInt+"' id = '"+cTerIdInt+"' readonly "+ 
                                 "onBlur = 'javascript:this.value=this.value.toUpperCase(); "+
                                 "f_Links(\"cTerIdInt\",\"VALID\",\""+nSecuencia+"\")'>";
          
          TD_xAll = cTableRow.insertCell(9);
          TD_xAll.style.width  = "20px";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:020px;border:0;text-align:center;padding:2px' name = '"+cTerDVInt+"' id = '"+cTerDVInt+"' readonly>";
          
          TD_xAll = cTableRow.insertCell(10);
          TD_xAll.style.width  = "240px";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:240px;border:0;text-align:left;padding:2px' name = '"+cTerNomInt+"' id = '"+cTerNomInt+"' onKeyUp='javascript:f_Enter(event,this.name,\"Grid_Do\");' readonly>"+
                                 "<input type = 'hidden' name = '"+cCcAplFa+"' id = '"+cCcAplFa+"' readonly>";
          
          TD_xAll = cTableRow.insertCell(11);
          TD_xAll.style.width  = "20px";
          TD_xAll.innerHTML    = "<input type = 'button' style = 'width:020px;text-align:center' id = "+nSecuencia+" value = 'X' "+
                                  "onClick = 'javascript:f_Delete_Row(this.value,\""+nSecuencia+"\",\"Grid_Do\", this);'>";
                                  
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
                if (xName == 'cTerNomInt'+eval(document.forms['frgrm']['nSecuencia'].value)) {
                  if (document.forms["frgrm"]['cDocId'+eval(document.forms['frgrm']['nSecuencia'].value)].value !== '' ) {
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
                      document.forms['frgrm']['cDocSeq'   + i].value = f_Str_Pad(i,3,"0","STR_PAD_LEFT"); 
                      document.forms['frgrm']['cSucId'    + i].value = document.forms['frgrm']['cSucId'    + j].value; 
                      document.forms['frgrm']['cDocId'    + i].value = document.forms['frgrm']['cDocId'    + j].value; 
                      document.forms['frgrm']['cDocSuf'   + i].value = document.forms['frgrm']['cDocSuf'   + j].value;
                      document.forms['frgrm']['cDocTip'   + i].value = document.forms['frgrm']['cDocTip'   + j].value;
                      document.forms['frgrm']['cCliId'    + i].value = document.forms['frgrm']['cCliId'    + j].value; 
                      document.forms['frgrm']['cCliDv'    + i].value = document.forms['frgrm']['cCliDv'    + j].value; 
                      document.forms['frgrm']['cCliNom'   + i].value = document.forms['frgrm']['cCliNom'   + j].value;
                      document.forms['frgrm']['cTerIdInt' + i].value = document.forms['frgrm']['cTerIdInt' + j].value; 
                      document.forms['frgrm']['cTerDVInt' + i].value = document.forms['frgrm']['cTerDVInt' + j].value; 
                      document.forms['frgrm']['cTerNomInt'+ i].value = document.forms['frgrm']['cTerNomInt'+ j].value;
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
          var cPathUrl = "fraecfrm.php?gFunction=PegarDo&gArchivo=fraeccpd.php"+
                         "&gSecuencia="+document.forms['frgrm']['nSecuencia'].value;
          cWindow = window.open(cPathUrl,"PegarDo",cWinOpt);
          cWindow.focus();
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
                	<legend><b>Seleccione los Tramites</b></legend>
                	<center>
                      <table border = '0' cellpadding = '0' cellspacing = '0' width='1160'>
                        <?php $nCol = f_Format_Cols(58); echo $nCol;?>
                        <tr height="25px">
                          <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="02" align="left">&nbsp;&nbsp;Seq.</td>                                                         
                          <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="02" align="left">&nbsp;&nbsp;Suc</td>                                                          
                          <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="08" align="left">&nbsp;&nbsp;Do</td>
                          <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="02" align="left">&nbsp;&nbsp;Suf</td>
                          <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="06" align="left">&nbsp;&nbsp;Operaci&oacute;n</td>                                                                     
                          <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="05" align="left">&nbsp;&nbsp;Nit</td>
                          <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="01" align="left">Dv</td>
                          <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="12" align="left">&nbsp;&nbsp;&nbsp;Importador</td>
                          <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="05" align="left">&nbsp;&nbsp;Nit</td>
                          <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="01" align="left">Dv</td>
                          <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="12" align="left">&nbsp;&nbsp;&nbsp;Facturar A</td>
                          <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="02" align="right">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/page_go.png" onClick = "javascript:fnPegarDo()" style = "cursor:pointer" title="Pegar DOs">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_drop.png" onClick = "javascript:fnBorrarDos()" style = "cursor:pointer" title="Eliminar Todos">
                          </td>                       
                        </tr>
                      </table>
                      <table border = "0" cellpadding = "0" cellspacing = "0" width = "1160" id = "Grid_Do"></table>
                  </center>
									<script languaje = "javascript">
										f_Add_New_Row_Do();
									</script>
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
                
                 <b>Nota:</b> El Facturar a Solo Aplica para Clientes que tienen parametrizada la Condicion Comercial "Aplicar tarifas del Facturar a".<br>
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
  						onClick ="javascript:f_Retorna();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
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
  
                document.getElementById('cDocSeq' + "<?php echo ($i+1) ?>").style.background = cBgColor;
                document.getElementById('cDocSeq' + "<?php echo ($i+1) ?>").style.color = cColor;
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

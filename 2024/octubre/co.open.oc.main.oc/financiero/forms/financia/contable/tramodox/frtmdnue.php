<?php
  namespace openComex;
	/**
	 * Trasladar DO
	 * @author Julio López <julio.lopez@opencomex.com>
	 * @package opencomex
	 */
  include("../../../../libs/php/utility.php");
  
  /**
	 *  Cookie fija
	 */
	$kDf = explode("~",$_COOKIE["kDatosFijos"]);
	$kMysqlHost = $kDf[0];
	$kMysqlUser = $kDf[1];
	$kMysqlPass = $kDf[2];
	$kMysqlDb   = $kDf[3];
	$kUser      = $kDf[4];
	$kLicencia  = $kDf[5];
  $swidth     = $kDf[6];
  
  //Buscando sucursales ICA
  $qSuc  = "SELECT DISTINCT sucidxxx ";
  $qSuc .= "FROM $cAlfa.fpar0008 ";
  $qSuc .= "WHERE regestxx = \"ACTIVO\" ";
  $qSuc .= "ORDER BY sucdesxx";
  $xSuc  = f_MySql("SELECT","",$qSuc,$xConexion01,"");
  $cSucIca = "<option value=''></option>";
  while ($xRS = mysql_fetch_array($xSuc)) {
    $cSucIca .= "<option value='".$xRS['sucidxxx']."'>".$xRS['sucidxxx']."</option>";
  }
  ?>

  <html>
  	<title>Trasladar DO</title>
  	<head>
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
      <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
      <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
    </head>
  	<body>

     	<script language = 'javascript'>

    		function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
    		  document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
    		  parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
    		}

        function f_Links(xLink,xSwitch,xIteration) {
          var nX    = screen.width;
          var nY    = screen.height;
          switch (xLink) {
            case "cComCod":
              if (xSwitch == "VALID") {
                var cPathUrl = "frtmd117.php?gModo="+xSwitch+"&gFunction="+xLink+
                                          "&gComCod="+document.forms['frnav']['cComCod'].value.toUpperCase()+"&gComId="+document.forms['frnav']['cComId'].value.toUpperCase();
                //alert(cPathUrl);
                parent.fmpro.location = cPathUrl;
              } else {
                var nNx      = (nX-500)/2;
                var nNy      = (nY-250)/2;
                var cWinOpt  = "width=500,scrollbars=1,height=250,left="+nNx+",top="+nNy;
                var cPathUrl = "frtmd117.php?gModo="+xSwitch+"&gFunction="+xLink+
                                          "&gComCod="+document.forms['frnav']['cComCod'].value.toUpperCase()+"&gComId="+document.forms['frnav']['cComId'].value.toUpperCase();
                cWindow = window.open(cPathUrl,xLink,cWinOpt);
                cWindow.focus();
              }
            break;
            case "cDocNroOri":
            case "cDocNroDes":
              if (xSwitch == "VALID") {
                var cPathUrl  = "frtmd121.php?gWhat=VALID&gFunction="+xLink+
                                "&gDocNro="+document.forms['frnav'][xLink].value.toUpperCase();
                parent.fmpro.location = cPathUrl;
              } else {
                var nNx     = (nX-400)/2;
                var nNy     = (nY-250)/2;
                var cWinOpt = 'width=400,scrollbars=1,height=250,left='+nNx+',top='+nNy;
                var cPathUrl  = "frtmd121.php?gWhat=WINDOW&gFunction="+xLink+
                                "&gDocNro="+document.forms['frnav'][xLink].value.toUpperCase();
                cWindow = window.open(cPathUrl,"cWindow",cWinOpt);
                cWindow.focus();
              }
            break;
          }
        }

        function f_Marca() {//Marca y Desmarca los registros seleccionados en la tabla de Conceptos de Cobro
          if (document.forms['frnav']['nCheckAll'].checked == true){
            if (document.forms['frnav']['nSecuencia'].value == 1){
              document.forms['frnav']['cCheck'].checked=true;
            } else {
                if (document.forms['frnav']['nSecuencia'].value > 1){
                  for (i=0;i<document.forms['frnav']['cCheck'].length;i++){
                    document.forms['frnav']['cCheck'][i].checked = true;
                  }
                }
            }
          } else {
              if (document.forms['frnav']['nSecuencia'].value == 1){
                document.forms['frnav']['cCheck'].checked=false;
              } else {
                  if (document.forms['frnav']['nSecuencia'].value > 1){
                    for (i=0;i<document.forms['frnav']['cCheck'].length;i++){
                      document.forms['frnav']['cCheck'][i].checked = false;
                    }
                  }
              }
            }
        }

        function f_Delete_Row_All() {
          var cGrid = document.getElementById("Grid_Comprobantes");
          var nLastRow = cGrid.rows.length;
          
          for (i=1;i<=nLastRow;i++) {
            var nLastRow01 = cGrid.rows.length;
            cGrid.deleteRow(nLastRow01 - 1);
          }
          document.forms['frnav']['nSecuencia'].value = 0;				
        }

        //Grilla de pagos a terceros excluidos enviados a FINANCIACION
        function f_Add_New_Row(xId,xChek) {
          
          var cGrid      = document.getElementById("Grid_Comprobantes");
          var nLastRow   = cGrid.rows.length;
          var nSecuencia = nLastRow+1;
          var cTableRow  = cGrid.insertRow(nLastRow);
          var cTerId   = 'cTerId_'   + nSecuencia; //Nit
          var cTerNom  = 'cTerNom_'  + nSecuencia; //Cliente
          var cCcoDes  = 'cCcoDes_'  + nSecuencia; //Sede
          var cComId   = 'cComId_'   + nSecuencia; //Tipo Comprobante
          var cComCod  = 'cComCod_'  + nSecuencia; //(Hidden) Codigo Comprobante
          var cComCsc  = 'cComCsc_'  + nSecuencia; //(Hidden) Consecutivo Uno
          var cComCsc2 = 'cComCsc2_' + nSecuencia; //(Hidden) Consecutivo Dos
          var cComSeq  = 'cComSeq_'  + nSecuencia; //(Hidden) Secuencia
          var cNumCom  = 'cNumCom_'  + nSecuencia; //Numero Comprobante
          var cNumFac  = 'cNumFac_'  + nSecuencia; //Numero Factura
          var cComFec  = 'cComFec_'  + nSecuencia; //Fecha Comprobante
          var cCtoDes  = 'cCtoDes_'  + nSecuencia; //Concepto
          var cComVlr01= 'cComVlr01_'+ nSecuencia; //Valor Sin Iva
          var cComVlr  = 'cComVlr_'  + nSecuencia; //Valor con Iva
          var cTerId2  = 'cTerId2_'  + nSecuencia; // Nit Proveedor
          var cTerNom2 = 'cTerNom2_' + nSecuencia; //Proveedor
          var cSucIca  = 'cSucIca_'  + nSecuencia; //Sucursal Ica
          var cComMov  = 'cComMov_'  + nSecuencia; //Movimiento
          
          var cCheck   = 'cCheck'   + nSecuencia; 
          
          var TD_xAll = cTableRow.insertCell(0);
          TD_xAll.style.width           = "60px";
          //TD_xAll.style.border          = "1px solid #E6E6E6";
          TD_xAll.style.backgroundColor = "#FFFFFF";
          //TD_xAll.style.fontFamily      = "arial";
          TD_xAll.style.fontSize        = "8pt";
          TD_xAll.style.className       = "letra";
          TD_xAll.style.textAlign       = "left";
          TD_xAll.style.padding         = "2px 2px 2px 2px";
          TD_xAll.id                    = cTerId;
  				TD_xAll.innerHTML             = "&nbsp;";
          
          var TD_xAll = cTableRow.insertCell(1);
          TD_xAll.style.width           = "140px";
          //TD_xAll.style.border          = "1px solid #E6E6E6";
          TD_xAll.style.backgroundColor = "#FFFFFF";
          //TD_xAll.style.fontFamily      = "arial";
          TD_xAll.style.fontSize        = "8pt";
          TD_xAll.style.className       = "letra";
          TD_xAll.style.textAlign       = "left";
          TD_xAll.style.padding         = "2px 2px 2px 2px";
          TD_xAll.id                    = cTerNom;
  				TD_xAll.innerHTML             = "&nbsp;";

          var TD_xAll = cTableRow.insertCell(2);
          TD_xAll.style.width           = "80px";
          //TD_xAll.style.border          = "1px solid #E6E6E6";
          TD_xAll.style.backgroundColor = "#FFFFFF";
          //TD_xAll.style.fontFamily      = "arial";
          TD_xAll.style.fontSize        = "8pt";
          TD_xAll.style.className       = "letra";
          TD_xAll.style.textAlign       = "left";
          TD_xAll.style.padding         = "2px 2px 2px 2px";
          TD_xAll.id                    = cCcoDes;
  				TD_xAll.innerHTML             = "&nbsp;";

          var TD_xAll = cTableRow.insertCell(3);
          TD_xAll.style.width           = "80px";
          //TD_xAll.style.border          = "1px solid #E6E6E6";
          TD_xAll.style.backgroundColor = "#FFFFFF";
          //TD_xAll.style.fontFamily      = "arial";
          TD_xAll.style.fontSize        = "8pt";
          TD_xAll.style.className       = "letra";
          TD_xAll.style.textAlign       = "center";
          TD_xAll.style.padding         = "2px 2px 2px 2px";
          TD_xAll.id                    = cComId;
  				TD_xAll.innerHTML             = "&nbsp;";

          var TD_xAll = cTableRow.insertCell(4);
          TD_xAll.style.width           = "80px";
          //TD_xAll.style.border          = "1px solid #E6E6E6";
          TD_xAll.style.backgroundColor = "#FFFFFF";
          //TD_xAll.style.fontFamily      = "arial";
          TD_xAll.style.fontSize        = "8pt";
          TD_xAll.style.className       = "letra";
          TD_xAll.style.textAlign       = "center";
          TD_xAll.style.padding         = "2px 2px 2px 2px";
          TD_xAll.id                    = cNumCom;
  				TD_xAll.innerHTML             = "&nbsp;";

          var TD_xAll = cTableRow.insertCell(5);
          TD_xAll.style.width           = "80px";
          //TD_xAll.style.border          = "1px solid #E6E6E6";
          TD_xAll.style.backgroundColor = "#FFFFFF";
          //TD_xAll.style.fontFamily      = "arial";
          TD_xAll.style.fontSize        = "8pt";
          TD_xAll.style.className       = "letra";
          TD_xAll.style.textAlign       = "center";
          TD_xAll.style.padding         = "2px 2px 2px 2px";
          TD_xAll.id                    = cNumFac;
  				TD_xAll.innerHTML             = "&nbsp;";

          var TD_xAll = cTableRow.insertCell(6);
          TD_xAll.style.width           = "80px";
          //TD_xAll.style.border          = "1px solid #E6E6E6";
          TD_xAll.style.backgroundColor = "#FFFFFF";
          //TD_xAll.style.fontFamily      = "arial";
          TD_xAll.style.fontSize        = "8pt";
          TD_xAll.style.className       = "letra";
          TD_xAll.style.textAlign       = "center";
          TD_xAll.style.padding         = "2px 2px 2px 2px";
          TD_xAll.id                    = cComFec;
  				TD_xAll.innerHTML             = "&nbsp;";

          var TD_xAll = cTableRow.insertCell(7);
          TD_xAll.style.width           = "80px";
          //TD_xAll.style.border          = "1px solid #E6E6E6";
          TD_xAll.style.backgroundColor = "#FFFFFF";
          //TD_xAll.style.fontFamily      = "arial";
          TD_xAll.style.fontSize        = "8pt";
          TD_xAll.style.className       = "letra";
          TD_xAll.style.textAlign       = "left";
          TD_xAll.style.padding         = "2px 2px 2px 2px";
          TD_xAll.id                    = cCtoDes;
  				TD_xAll.innerHTML             = "&nbsp;";

          var TD_xAll = cTableRow.insertCell(8);
          TD_xAll.style.width           = "80px";
          //TD_xAll.style.border          = "1px solid #E6E6E6";
          TD_xAll.style.backgroundColor = "#FFFFFF";
          //TD_xAll.style.fontFamily      = "arial";
          TD_xAll.style.fontSize        = "8pt";
          TD_xAll.style.className       = "letra";
          TD_xAll.style.textAlign       = "right";
          TD_xAll.style.padding         = "2px 2px 2px 2px";
          TD_xAll.id                    = cComVlr01;
  				TD_xAll.innerHTML             = "&nbsp;";

          var TD_xAll = cTableRow.insertCell(9);
          TD_xAll.style.width           = "80px";
          //TD_xAll.style.border          = "1px solid #E6E6E6";
          TD_xAll.style.backgroundColor = "#FFFFFF";
          //TD_xAll.style.fontFamily      = "arial";
          TD_xAll.style.fontSize        = "8pt";
          TD_xAll.style.className       = "letra";
          TD_xAll.style.textAlign       = "right";
          TD_xAll.style.padding         = "2px 2px 2px 2px";
          TD_xAll.id                    = cComVlr;
  				TD_xAll.innerHTML             = "&nbsp;";

          var TD_xAll = cTableRow.insertCell(10);
          TD_xAll.style.width           = "60px";
          //TD_xAll.style.border          = "1px solid #E6E6E6";
          TD_xAll.style.backgroundColor = "#FFFFFF";
          //TD_xAll.style.fontFamily      = "arial";
          TD_xAll.style.fontSize        = "8pt";
          TD_xAll.style.className       = "letra";
          TD_xAll.style.textAlign       = "left";
          TD_xAll.style.padding         = "2px 2px 2px 2px";
          TD_xAll.id                    = cTerId2;
  				TD_xAll.innerHTML             = "&nbsp;";

          var TD_xAll = cTableRow.insertCell(11);
          TD_xAll.style.width           = "140px";
          //TD_xAll.style.border          = "1px solid #E6E6E6";
          TD_xAll.style.backgroundColor = "#FFFFFF";
          //TD_xAll.style.fontFamily      = "arial";
          TD_xAll.style.fontSize        = "8pt";
          TD_xAll.style.className       = "letra";
          TD_xAll.style.textAlign       = "left";
          TD_xAll.style.padding         = "2px 2px 2px 2px";
          TD_xAll.id                    = cTerNom2;
  				TD_xAll.innerHTML             = "&nbsp;";

          var TD_xAll = cTableRow.insertCell(12);
          TD_xAll.style.width           = "60px";
          //TD_xAll.style.border          = "1px solid #E6E6E6";
          TD_xAll.style.backgroundColor = "#FFFFFF";
          //TD_xAll.style.fontFamily      = "arial";
          TD_xAll.style.fontSize        = "8pt";
          TD_xAll.style.className       = "letra";
          TD_xAll.style.textAlign       = "left";
          TD_xAll.style.padding         = "2px 2px 2px 2px";
  				TD_xAll.innerHTML             = "<select Class = 'letra' style = 'width:60px' name = "+cSucIca+" id = "+cSucIca+"><?php echo $cSucIca ?></select>";

          var TD_xAll = cTableRow.insertCell(13);
          TD_xAll.style.width           = "20px";
          //TD_xAll.style.border          = "1px solid #E6E6E6";
          TD_xAll.style.backgroundColor = "#FFFFFF";
          //TD_xAll.style.fontFamily      = "arial";
          TD_xAll.style.fontSize        = "8pt";
          TD_xAll.style.className       = "letra";
          TD_xAll.style.textAlign       = "center";
          TD_xAll.style.padding         = "2px 2px 2px 2px";
          TD_xAll.id                    = cComMov;
  				TD_xAll.innerHTML             = "&nbsp;";

          TD_xAll = cTableRow.insertCell(14);
          TD_xAll.style.width           = "20px";
          TD_xAll.style.backgroundColor = "#FFFFFF";
          TD_xAll.style.textAlign       = "right";
          TD_xAll.innerHTML             = "<input type='checkbox' name='cCheck' id='"+xId+"'"+((xChek) == true ? " checked" : "")+">";
          
          document.forms['frnav']['nSecuencia'].value = nSecuencia;
        }

        function fnCargarPagos() {
          //Validando que haya seleccionado DO origen
          if (document.forms['frnav']['cSucIdOri'].value != "" && document.forms['frnav']['cDocNroOri'].value != "" && document.forms['frnav']['cDocSufOri'].value != "") {
            f_Delete_Row_All();
            document.forms['frnav'].target="fmpro";
            document.forms['frnav'].action="frtmdcom.php";
            document.forms['frnav'].submit();
          } else {
            alert("Debe Seleccionar el Do de Origen, Verifique.");
          }
        }

        function f_Cambiar_Valor(xCheck) {
          if (xCheck.checked == true) {
            xCheck.value = "SI";
          } else {
            xCheck.value = "NO";
          }
        }

        function f_Carga_Data() { //Arma cadena para guardar en campo matriz de la sys00121
          document.forms['frnav']['cComMemo'].value="|";
          switch (document.forms['frnav']['nSecuencia'].value) {
            case "1":
              if (document.forms['frnav']['cCheck'].checked == true) {
                document.forms['frnav']['cComMemo'].value += document.forms['frnav']['cCheck'].id+"~"+document.forms['frnav']['cSucIca_1'].value+"|";
              }
            break;
            default:
              if (document.forms['frnav']['nSecuencia'].value > 1) {
                for (i=0;i<document.forms['frnav']['cCheck'].length;i++) {
                  if (document.forms['frnav']['cCheck'][i].checked == true) {
                    document.forms['frnav']['cComMemo'].value += document.forms['frnav']['cCheck'][i].id+"~"+document.forms['frnav']['cSucIca_' + (i+1)].value+"|";
                  }
                }
              }
            break;
          }
          if (document.forms['frnav']['cComMemo'].value == "|"){
            document.forms['frnav']['cComMemo'].value = "";
          }
        }

        function f_TrasladarDo(){
          var nSwitch = 0;
					var cMsj    = "";

					if (document.forms['frnav']['cSucIdOri'].value == "" ||
						  document.forms['frnav']['cDocNroOri'].value == "" ||
							document.forms['frnav']['cDocSufOri'].value == "") {
						nSwitch = 1;
						cMsj += "Debe Seleccionar el DO Origen.\n";
					}

					if (document.forms['frnav']['cSucIdDes'].value == "" ||
						  document.forms['frnav']['cDocNroDes'].value == "" ||
							document.forms['frnav']['cDocSufDes'].value == "") {
						nSwitch = 1;
						cMsj += "Debe Seleccionar el DO Destino.\n";
					}

          if (nSwitch == 0) {
            if (confirm("Esta Seguro de Trasladar el Movimiento Seccionado del DO ["+document.forms['frnav']['cSucIdOri'].value+"-"+document.forms['frnav']['cDocNroOri'].value+"-"+document.forms['frnav']['cDocSufOri'].value+"] al DO ["+document.forms['frnav']['cSucIdDes'].value+"-"+document.forms['frnav']['cDocNroDes'].value+"-"+document.forms['frnav']['cDocSufDes'].value+"] ?")) {
              f_CreaCookie('kModo','TRASLADAR');
              f_Carga_Data();
              document.forms['frnav'].target='fmpro';
              document.forms['frnav'].action='frtmdgra.php';
              document.forms['frnav']['nTimesSave'].value++;
              document.forms['frnav'].submit();
            }
          } else {
						alert(cMsj+"Verifique.");
					}
        }
      </script>
      <center>
        <table width="600" cellspacing="0" cellpadding="0" border="0">
        	<tr>
          	<td>
        		  <fieldset>
          			<legend>Trasladar DO</legend>
								<form name = "frnav" action = "frtmdgra.php" method = "post" target = "fmpro">
                  <input type = "hidden" name = "cComTco"     value = ""> <!-- Tipo de Consecutivo para el comprobante (MANUAL/AUTOMATICO) -->
                  <input type = "hidden" name = "cComCco"     value = ""> <!-- Control Consecutivo para el comprobante (MENSUAL/ANUAL/INDEFINIDO) -->
                  <input type = "hidden" name = "nTimesSave"  value = "0">
                  <input type = "hidden" name = "dComFec_Ant" value = "">
                  <input type = "hidden" name = "nSecuencia" value = "">
                  <textarea name = "cComMemo"  id = "cComMemo"><?php  echo $_POST['cComMemo'] ?></textarea>
                  <script languaje = "javascript">
                    document.getElementById("cComMemo").style.display ="none";
                  </script>

        	  			<center>
                  <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:1140">
                    <?php $cCols = f_Format_Cols(57); echo $cCols; ?>
                    <tr>
                      <td Class = "name" colspan = "1">Id<br>
                        <input type = "text" Class = "letra" style = "width:20" name = "cComId" value = "" readonly>
                      </td>
                      <td Class = "name" colspan = "2">
                        <a href = "javascript:document.forms['frnav']['cComId'].value='';
                                              document.forms['frnav']['cComCod'].value='';
                                              document.forms['frnav']['cComDes'].value='';
                                              f_Links('cComCod','VALID')" id="id_href_cComCod">Cod</a><br>
                        <input type = "text" Class = "letra" style = "width:40;text-aling:center" name = "cComCod" value = ""
                          onfocus="javascript:this.value='';
                                              document.forms['frnav']['cComDes'].value='';
                                              this.style.background='#00FFFF'"
                          onblur = "javascript:f_Links('cComCod','VALID');
                                              this.style.background='#FFFFFF';
                                              document.forms['frnav']['cComDes'].focus();">
                      </td>
                      <td Class = "name" colspan = "13">Descripcion<br>
                        <input type = "text" Class = "letra" style = "width:260" name = "cComDes" readonly>
                      </td>
                      <td Class = "name" colspan = "25">Observacion Traslado<br>
                        <input type = "text" Class = "letra" style = "width:500" name = "cComObs" maxlength="200" value = ""
                          onBlur = "javascript:this.value=this.value.toUpperCase()">
                      </td>  
                      <?php if (($_COOKIE['kModo'] == "ANTERIOR" || $_POST['cPeriodo'] == 'ANTERIOR') && $vSysStr['financiero_permitir_digitar_fecha_periodo_anterior'] == 'NO' ) { ?>
                        <td Class = "name" colspan = "4">Fecha<br>
                          <select Class = "letrase" name = "dComFec" style = "width:80">
                          </select>
                        </td>
                      <?php } else { ?>
                        <td Class = "name" colspan = "4">
                          <a href='javascript:show_calendar("frnav.dComFec")' id="id_href_dComFec">Fecha</a><br>
                          <input type = "text" Class = "letra" style = "width:80;text-aling:center"
                            name = "dComFec" value = "<?php echo date('Y-m-d') ?>" onBlur = "javascript:f_Date(this)">
                        </td>
                      <?php } ?>
                      <td Class = "name" colspan = "3">Hora<br>
                        <input type = "text" Class = "letra" style = "width:60;text-aling:center"
                          name = "tRegHCre" value = "<?php echo date('H:i:s') ?>" readonly>
                      </td>
                      <td Class = "name" colspan = "4">
                        <a href='javascript:show_calendar("frnav.dComVen")' id="id_href_dComVen">Vencimiento</a><br>
                        <input type = "text" Class = "letra" style = "width:80;text-aling:center"
                          name = "dComVen" value = "<?php echo date('Y-m-d') ?>" onBlur = "javascript:f_Date(this)">
                      </td>
                      <td Class = "name" colspan = "5">Tasa Cambio<br>
                        <input type = "text" Class = "letra" style = "width:100;text-align:right;" name = "nTasaCambio" value="<?php echo f_Buscar_Tasa_Cambio(date('Y-m-d'),"USD"); ?>"
                          onKeyUp = "javascript:this.value=f_ValDec(this.value);"
                          onFocus = "javascript:this.style.background='#00FFFF';"
                          onBlur  = "javascript:if (this.value.substr(-1) == '.') { this.value = this.value.substring(0, this.value.length-1); } this.value=f_ValDec(this.value);this.style.background='#FFFFFF';">
                      </td> 
                    </tr>                          
                    <tr>
                      
                    </tr>
                    <tr>
                      <td Class = "name" colspan = "4"><a href = "javascript: document.forms['frnav']['cSucIdOri'].value='';
                                                document.forms['frnav']['cDocTipOri'].value='';
                                                document.forms['frnav']['cDocNroOri'].value='';
                                                document.forms['frnav']['cDocSufOri'].value='';
                                                document.forms['frnav']['cCliNomOri'].value='';
                                                f_Delete_Row_All();
                                                f_Links('cDocNroOri','VALID');" id="idOri">Do Origen</a><br>
                        <input type="text"   class="letra" name="cSucIdOri"  style="width:80;text-aling:center" readonly>
                      </td>
                      <td Class = "name" colspan = "4"><br>
                        <input type="text"   class="letra" name="cDocTipOri" style="width:80;text-aling:center" readonly>
                      </td>
                      <td Class = "name" colspan = "5"><br>
                        <input type="text"   class="letra" name="cDocNroOri" style="width:100;text-aling:center"
                          onBlur = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>';
                                    f_Links('cDocNroOri','VALID');"
                          onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>';
                                    document.forms['frnav']['cSucIdOri'].value='';
                                    document.forms['frnav']['cDocTipOri'].value='';
                                    document.forms['frnav']['cDocNroOri'].value='';
                                    document.forms['frnav']['cDocSufOri'].value='';
                                    document.forms['frnav']['cCliNomOri'].value='';
                                    f_Delete_Row_All();">                        
                      </td>
                      <td Class = "name" colspan = "2"><br>
                        <input type="text"   class="letra" name="cDocSufOri" style="width:40;text-aling:center" readonly>
                      </td>
                      <td Class = "name" colspan = "14"><br>
                        <input type="text"   class="letra" name="cCliNomOri" style="width:280" readonly>
                      </td>
                      <td Class = "name" colspan = "4"><a href = "javascript: document.forms['frnav']['cSucIdDes'].value='';
                                                document.forms['frnav']['cDocTipDes'].value='';
                                                document.forms['frnav']['cDocNroDes'].value='';
                                                document.forms['frnav']['cDocSufDes'].value='';
                                                document.forms['frnav']['cCliNomDes'].value='';
                                                f_Links('cDocNroDes','VALID');" id="idDes">Do Destino</a><br>
                        <input type="text"   class="letra" name="cSucIdDes"  style="width:80;text-aling:center" readonly>
                      </td>
                      <td Class = "name" colspan = "4"><br>
                        <input type="text"   class="letra" name="cDocTipDes" style="width:80;text-aling:center" readonly>
                      </td>
                      <td Class = "name" colspan = "5"><br>
                        <input type="text"   class="letra" name="cDocNroDes" style="width:100"
                          onBlur = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>';
                                    f_Links('cDocNroDes','VALID');"
                          onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>';
                                    document.forms['frnav']['cSucIdDes'].value='';
                                    document.forms['frnav']['cDocTipDes'].value='';
                                    document.forms['frnav']['cDocNroDes'].value='';
                                    document.forms['frnav']['cDocSufDes'].value='';
                                    document.forms['frnav']['cCliNomDes'].value='';">                        
                      </td>
                      <td Class = "name" colspan = "2"><br>
                        <input type="text"   class="letra" name="cDocSufDes" style="width:40;text-aling:center" readonly>
                      </td>
                      <td Class = "name" colspan = "13"><br>
                        <input type="text"   class="letra" name="cCliNomDes" style="width:260" readonly>
                      </td>
                    </tr>
                    <tr><td colspan = "57"><hr></td></tr>
                  </table>
                  <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:1140">
                    <?php $cCols = f_Format_Cols(57); echo $cCols; ?>
                    <tr>
                      <td colspan = "52">
                        <label><input type="checkbox" name="chkCerrarDo" value="SI" onclick="javascript:f_Cambiar_Valor(this)">&nbsp;&nbsp;<b>Cerrar DO Origen Sino Tiene Movimiento Contable.</b></label>
                        <script type="text/javascript">
                          document.forms['frnav']['chkCerrarDo'].value   = "SI";
                          document.forms['frnav']['chkCerrarDo'].checked = true;
                        </script>
                      </td>
                    </tr>
                  </table>
                  <table border = '0' cellpadding = '0' cellspacing = '0' width='1140'>
                    <tr bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>">
                      <td class = "clase08" aling="left" style="width:60px">&nbsp;Nit</td>     
                      <td class = "clase08" aling="left" style="width:140px">&nbsp;&nbsp;Cliente</td>                                                          
                      <td class = "clase08" aling="left" style="width:80px">&nbsp;Sede</td>
                      <td class = "clase08" aling="left" style="width:80px">Tipo Comprobante</td>
                      <td class = "clase08" aling="left" style="width:80px">N&uacute;mero Comprobante</td>                                                                     
                      <td class = "clase08" aling="left" style="width:80px">N&uacute;mero Factura</td>
                      <td class = "clase08" aling="left" style="width:80px">Fecha Comprobante</td>
                      <td class = "clase08" aling="left" style="width:80px">Concepto</td>
                      <td class = "clase08" aling="left" style="width:80px">Valor sin Iva</td>
                      <td class = "clase08" aling="left" style="width:80px">Valor con Iva</td>
                      <td class = "clase08" aling="left" style="width:60px">Nit</td>
                      <td class = "clase08" aling="left" style="width:140px">Proveedor</td>
                      <td class = "clase08" aling="left" style="width:60px">Suc.ICA</td>
                      <td class = "clase08" aling="left" style="width:20px">M</td>
                      <td bgcolor = "<?php echo $vSysStr['system_row_title_color_ini'] ?>" class = "clase08" colspan="01" aling = "center">
                        <input type="checkbox" name="nCheckAll" onClick = "javascript:f_Marca()" checked>
                      </td>
                    </tr>
                  </table>
                  <table border = "0" cellpadding = "0" cellspacing = "2" id = "Grid_Comprobantes"></table>
        	  	 		</center>
								</form>
              </fieldset>
  		  		</td>
  		  	</tr>
  		  </table>
  		</center>
			<center>
				<table border="0" cellpadding="0" cellspacing="0" width="1160">
					<tr height="21">
						<td width="978" height="21"></td>
						<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_New ?>/btn_ok_bg.gif" style="cursor:hand"
						  onClick = "javascript:f_TrasladarDo();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Trasladar
						</td>
						<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_New ?>/btn_cancel_bg.gif" style="cursor:hand"
						  onClick = "javascript:f_Retorna()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
						</td>
					</tr>
				</table>
			</center>
  	</body>
  </html>

  <!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
  <?php switch ($_COOKIE['kModo']) {
    case "NUEVO":
    case "ANTERIOR": ?>
      <script languaje = "javascript">
        f_Links('cComCod','VALID');
      </script>
    <?php break;
    default: 
      //No hace nada
    break;
  } ?>

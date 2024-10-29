<?php
  namespace openComex;
/**
	 * Listado DO y Cambio de Nit de Cliente y Proveedor
	 * @author Johana Arboleda Ramos <jarboleda@opentecnologia.com.co>
	 * @package openComex
	 */
	include("../../../../libs/php/utility.php");
	
  $kDf = explode("~",$_COOKIE["kDatosFijos"]);
  $kMysqlDb = $kDf[3];

	/**
	 * Preparando variables cuando se cambia de cliente
	 */
	if($gTerId == '') {
	 $gTerTip=$cTerTip;
   $gTerId=$cTerId;
   $gTerTipB=$cTerTipB;
   $gTerIdB=$cTerIdB;
   $gSecuencia=$nSecGrilla;
	}
	
	?>
<html>
  <title>Do's Cliente</title>
	<head>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
		</script>
		<script language="javascript">
			function f_Todas(){
				for (i=0;i<document.forms['frgrm']['nSecuencia'].value;i++) {
	  	    if (document.frgrm["chAll"].checked == true) {
	  	    	document.frgrm["ch"+i].checked = true;
	  	    } else {
	  	    	document.frgrm["ch"+i].checked = false;
	  	    }
				}
			}
			
  		function f_Aceptar(xSecuencia){
  	  	var nBan = 0;
  	  	var nCon = 0;
  			var cTerTip  = document.forms['frgrm']['cTerTip'].value.toUpperCase();
        var cTerId   = document.forms['frgrm']['cTerId'].value.toUpperCase();
        var cTerNom  = document.forms['frgrm']['cTerNom'].value.toUpperCase();
        var cTerTipB = document.forms['frgrm']['cTerTip'].value.toUpperCase();
        var cTerIdB  = document.forms['frgrm']['cTerId'].value.toUpperCase();

        if(parent.window.opener.document.forms['frgrm']['cTipPro'].value == 'VALOR') {
	        //Creando matriz con id de conceptos
	        var mConceptos =  new Array();
	        var nInd = 0;
	        for (var j=1;j<=parent.window.opener.document.forms['frgrm']['nSecuencia_CCO'].value;j++) {
		        if (parent.window.opener.document.forms['frgrm']['cCcoId_CCO'+j].value != '') {
		          nCon = 0;
		          for(var n=0; n<mConceptos.length; n++){
		            if(mConceptos[n] == parent.window.opener.document.forms['frgrm']['cCcoId_CCO'+j].value) {
		              nCon = 1;
		            }
		          }
		          if(nCon == 0) {
		            mConceptos[nInd] = parent.window.opener.document.forms['frgrm']['cCcoId_CCO'+j].value;
		            nInd++;
		          } 
		        }
	        }
        }
        
        if (cTerTip != '' && cTerId != '' && cTerTipB != '' && cTerIdB != '') {
	  		  for (var i=0;i<document.forms['frgrm']['nSecuencia'].value;i++) {
	  		    if (document.frgrm["ch"+i].checked == true) {
	  		        var mMatriz = document.frgrm["ch"+i].id.split("~");

		  		      //Si el DO No esta en la girlla se agrega, de lo contrario no hace nada
		  		      nCon = 0;
		  		      for(var j=1; j<=parent.window.opener.document.forms['frgrm']['nSecuencia_DO'].value; j++) {
			  		    	if (parent.window.opener.document.forms['frgrm']['cSucId_DO' +j].value == mMatriz[0] &&
			  		    			parent.window.opener.document.forms['frgrm']['cDocId_DO' +j].value == mMatriz[1] &&
			  		    			parent.window.opener.document.forms['frgrm']['cDocSuf_DO'+j].value == mMatriz[2]) {
			  		    		nCon = 1;
			  		    	}
		  		      }

		  		      if (nCon == 0) {
			  		    	if (xSecuencia > parent.window.opener.document.forms['frgrm']['nSecuencia_DO'].value) {			  		        
		  		        	parent.window.opener.f_Add_New_Row_Dos();
		  		        }
		    		      parent.window.opener.document.forms['frgrm']['cSucId_DO'   +xSecuencia].id    = mMatriz[0];
		    		      parent.window.opener.document.forms['frgrm']['cSucId_DO'   +xSecuencia].value = mMatriz[0];
		    		      parent.window.opener.document.forms['frgrm']['cDocId_DO'   +xSecuencia].id    = mMatriz[1];
		              parent.window.opener.document.forms['frgrm']['cDocId_DO'   +xSecuencia].value = mMatriz[1];
		              parent.window.opener.document.forms['frgrm']['cDocSuf_DO'  +xSecuencia].id    = mMatriz[2];
		              parent.window.opener.document.forms['frgrm']['cDocSuf_DO'  +xSecuencia].value = mMatriz[2];		                          
		              parent.window.opener.document.forms['frgrm']['cTerId_DO'   +xSecuencia].value = cTerId;
		              parent.window.opener.document.forms['frgrm']['cTerNom_DO'  +xSecuencia].value = cTerNom;
		              parent.window.opener.document.forms['frgrm']['cTerTip_DO'  +xSecuencia].value = cTerTip;
		              parent.window.opener.document.forms['frgrm']['cTerTipB_DO' +xSecuencia].value = cTerTipB;
		              parent.window.opener.document.forms['frgrm']['cTerIdB_DO'  +xSecuencia].value = cTerIdB;        
		              parent.window.opener.document.forms['frgrm']['cDocFec_DO'  +xSecuencia].value = mMatriz[3];
		              parent.window.opener.document.forms['frgrm']['cCcoId_DO'   +xSecuencia].value = mMatriz[4];
		              parent.window.opener.document.forms['frgrm']['nVlrPro_DO'  +xSecuencia].value = "";
		              nBan = 1;
		  		        xSecuencia++; 

			  		      if(parent.window.opener.document.forms['frgrm']['cTipPro'].value == 'VALOR') {
				  		      for(var n=0; n<mConceptos.length; n++){
		  	              var nPosFin = 0;
		  	              var nCon = 0;
		  	              var cAnt = "";
		  	              for (j=1;j<=parent.window.opener.document.forms['frgrm']['nSecuencia_CCO'].value;j++) {
			  	              //Busco si el DO aparece en la grilla de conceptos para ese concepto
			  	              if(parent.window.opener.document.forms['frgrm']['cCcoId_CCO'+j].value == mConceptos[n]){
				  	            	nPosFin = j;
		  	                  if(parent.window.opener.document.forms['frgrm']['cSucId_CCO' +j].value == mMatriz[0] &&
			  	                	 parent.window.opener.document.forms['frgrm']['cDocId_CCO' +j].value == mMatriz[1] &&
			  	                	 parent.window.opener.document.forms['frgrm']['cDocSuf_CCO'+j].value == mMatriz[2]) {
			  	                	nCon = 1;
		  	                  }
		  	                }
		  	              }
		  	              if(nCon == 0) {
			  	              if(parent.window.opener.document.forms['frgrm']['cSucId_CCO' +nPosFin].value == '' &&
				  	            	 parent.window.opener.document.forms['frgrm']['cDocId_CCO' +nPosFin].value == '' &&
				  	            	 parent.window.opener.document.forms['frgrm']['cDocSuf_CCO'+nPosFin].value == ''){
				  	            	nSecuencia = parseInt(nPosFin);
			  	              } else {
				  	              //el DO no esa asociado a ese concepto, por lo tanto hay que agregarlo
				  	            	nSecuencia = parseInt(nPosFin) + 1;
			                    parent.window.opener.f_Insert_Row("Grid_Conceptos",nSecuencia);
			  	              }
		  
		                    parent.window.opener.document.forms['frgrm']['cCcoId_CCO'   +nSecuencia].id    = parent.window.opener.document.forms['frgrm']['cCcoId_CCO'   +nPosFin].id;
		                    parent.window.opener.document.forms['frgrm']['cCcoId_CCO'   +nSecuencia].value = parent.window.opener.document.forms['frgrm']['cCcoId_CCO'   +nPosFin].value;        
		                    parent.window.opener.document.forms['frgrm']['cCcoDes_CCO'  +nSecuencia].value = parent.window.opener.document.forms['frgrm']['cCcoDes_CCO'  +nPosFin].value;
		                    parent.window.opener.document.forms['frgrm']['cSucId_CCO'   +nSecuencia].value = mMatriz[0];
		                    parent.window.opener.document.forms['frgrm']['cDocId_CCO'   +nSecuencia].value = mMatriz[1];
		                    parent.window.opener.document.forms['frgrm']['cDocSuf_CCO'  +nSecuencia].value = mMatriz[2];
		                    parent.window.opener.document.forms['frgrm']['nVlrBaiu_CCO' +nSecuencia].value = "";
		                    parent.window.opener.document.forms['frgrm']['nVlrBase_CCO' +nSecuencia].value = "";
		                    parent.window.opener.document.forms['frgrm']['nVlrIva_CCO'  +nSecuencia].value = "";
		                    parent.window.opener.document.forms['frgrm']['nVlr_CCO'     +nSecuencia].value = "";
		                    parent.window.opener.document.forms['frgrm']['cCtoVrl02_CCO'+nSecuencia].value = parent.window.opener.document.forms['frgrm']['cCtoVrl02_CCO'+nPosFin].value;
		  	              }	  	              
		  	            }
			  		      }
			  		   }	        	
	  		    }
	  		  }
	  		  if(nBan == 1){
		  			parent.window.opener.f_Asignar_Base_Conceptos();
	  			  parent.window.close();
	  		  } else {
	  	  		  alert('Debe Seleccionar un DO, o los DO marcados ya fueron seleccionados.');
	  		  }
        } else {
        	alert('Debe Seleccionar el Cliente y el Proveedor.');
        }
			}
		
      function f_Links(xLink,xSwitch,xSecuencia,xType) {
        var nX    = screen.width;
        var nY    = screen.height;
        switch (xLink) {
          case "cTerId":
          case "cTerNom":
        	  var cTerTip = document.forms['frgrm']['cTerTip'].value.toUpperCase();
            var cTerId  = document.forms['frgrm']['cTerId'].value.toUpperCase();
            var cTerNom = document.forms['frgrm']['cTerNom'].value.toUpperCase();
          
            if (xSwitch == "VALID") {
          	  var cPathUrl = "frcpacli.php?gModo="+xSwitch+"&gFunction="+xLink+
                             "&gTerTip="+cTerTip+
                             "&gTerId="+cTerId+
                             "&gTerNom="+cTerNom;
              //alert(cPathUrl);
              parent.framepro.location = cPathUrl;
            } else {
              var nNx      = (nX-600)/2;
              var nNy      = (nY-250)/2;
              var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
              var cPathUrl = "frcpacli.php?gModo="+xSwitch+"&gFunction="+xLink+
                             "&gTerTip="+cTerTip+
                             "&gTerId="+cTerId+
                             "&gTerNom="+cTerNom;
              //alert(cPathUrl);
              cWindow = window.open(cPathUrl,xLink,cWinOpt);
              cWindow.focus();
            }
          break;
        }
      }
      
      function f_Enabled_Combos() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        document.forms['frgrm']['cTerTip'].disabled  = false;
        document.forms['frgrm']['cTerTipB'].disabled = false;
      }
        
      function f_Disabled_Combos() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        document.forms['frgrm']['cTerTip'].disabled  = true;
        document.forms['frgrm']['cTerTipB'].disabled = true;
      }
  	</script>
  </head>

	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
	<form name = "frgrm" action = "frcpados.php" method = "post" target="framework">
	<input type="hidden" name = "nSecuencia" value="0">
	<input type="hidden" name = "nSecGrilla" value="<?php echo $gSecuencia ?>">
	 <center>
	    <table border = "0" cellpadding = "0" cellspacing= "0" width = "460">
        <tr>
          <td>
            <fieldset>
              <legend>Cambio de Nit de Cliente y Proveedor</legend>
                <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:460">
                  <?php $cCols = f_Format_Cols(23); echo $cCols; ?>
                  <tr>
                    <td Class = "name" colspan = "5">Tipo Tercero<br>
                      <select Class = "letrase" name = "cTerTip" style = "width:100" disabled>
                        <option value = 'CLICLIXX' selected>CLIENTE</option>
                        <option value = 'CLIPROCX'>PROVEEDORC</option>
                        <option value = 'CLIPROEX'>PROVEEDORE</option>
                        <option value = 'CLIEFIXX'>E. FINANCIERA</option>
                        <option value = 'CLISOCXX'>SOCIO</option>
                        <option value = 'CLIEMPXX'>EMPLEADO</option>
                        <option value = 'CLIOTRXX'>OTROS</option>
                      </select>
                    </td>
                    <td Class = "name" colspan = "4">
                      <a <?php echo ($vSysStr['financiero_causaciones_automaticas_permitir_do_diferentes_clientes'] == 'NO') ? "href=\"#\" onClick=\"alert('El Cliente no Puede ser Diferente al que esta en el Comprobante!')\"" : "href = \"javascript:document.forms['frgrm']['cTerId'].value   = '';
                                            document.forms['frgrm']['cTerNom'].value  = '';
                                            document.forms['frgrm']['cTerDV'].value   = '';
                                            f_Links('cTerId','VALID')\" "?> id="id_href_cTerId">Nit</a><br>
                      <input type = "text" Class = "letra" style = "width:80;text-align:center" name = "cTerId" 
                        <?php echo ($vSysStr['financiero_causaciones_automaticas_permitir_do_diferentes_clientes'] == 'NO') ? 'readOnly' :  "onfocus=\"javascript:document.forms['frgrm']['cTerId'].value   = '';
                                            document.forms['frgrm']['cTerNom'].value  = '';
                                            document.forms['frgrm']['cTerDV'].value   = '';
                                            this.style.background='#00FFFF'\"
                        onBlur = \"javascript:this.value=this.value.toUpperCase();
                                             f_Links('cTerId','VALID');
                                             this.style.background='#FFFFFF'\""?>>
                    </td>
                    <td Class = "name" colspan = "1">Dv<br>
                      <input type = "text" Class = "letra" style = "width:20;text-align:center" name = "cTerDV" readonly>
                    </td>
                    <td Class = "name" colspan = "13">Cliente<br>
                      <input type = "text" Class = "letra" style = "width:260" name = "cTerNom"
                        <?php echo ($vSysStr['financiero_causaciones_automaticas_permitir_do_diferentes_clientes'] == 'NO') ? 'readOnly' : "onfocus=\"javascript:document.forms['frgrm']['cTerId'].value   = '';
                                            document.forms['frgrm']['cTerNom'].value  = '';
                                            document.forms['frgrm']['cTerDV'].value   = '';
                                            this.style.background='#00FFFF'\"
                        onBlur = \"javascript:this.value=this.value.toUpperCase();
                                             f_Links('cTerNom','VALID');
                                             this.style.background='#FFFFFF'\" "?>>
                    </td>
                  </tr>
                  
                  <tr>
                    <td Class = "name" colspan = "5">Tipo Tercero<br>
                      <select Class = "letrase" name = "cTerTipB" style = "width:100" disabled>
                        <option value = 'CLICLIXX'>CLIENTE</option>
                        <option value = 'CLIPROCX' selected>PROVEEDORC</option>
                        <option value = 'CLIPROEX'>PROVEEDORE</option>
                        <option value = 'CLIEFIXX'>E. FINANCIERA</option>
                        <option value = 'CLISOCXX'>SOCIO</option>
                        <option value = 'CLIEMPXX'>EMPLEADO</option>
                        <option value = 'CLIOTRXX'>OTROS</option>
                      </select>
                    </td>
                    <td Class = "name" colspan = "4">Nit<br>
                      <input type = "text" Class = "letra" style = "width:80;text-align:center" name = "cTerIdB" readOnly>
                    </td>
                    <td Class = "name" colspan = "1">Dv<br>
                      <input type = "text" Class = "letra" style = "width:20;text-align:center" name = "cTerDVB" readonly>
                    </td>
                    <td Class = "name" colspan = "13">Tercero<br>
                      <input type = "text" Class = "letra" style = "width:260" name = "cTerNomB" readOnly>
                    </td>
                  </tr>  
                </table>  
            </fieldset>
          
				  	<fieldset>
					  	<legend>Listado Documentos Cruce</legend>
						 	  <center>
									<table border = "1" cellpadding = "0" cellspacing = "0" width="460">
									<tr bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>">
											<td Class = "name" width = "050"><center>SUC</center></td>
											<td Class = "name" width = "100"><center>DO</center></td>
											<td Class = "name" width = "050"><center>SUFIJO</center></td>
											<td Class = "name" width = "100"><center>FECHA</center></td>
											<td Class = "name" width = "060"><center>CC</center></td>
											<td Class = "name" width = "080"><center>ESTADO</center></td>
											<td Class = "name" width = "020"><center><input type = 'checkbox' name = 'chAll' onClick="javascript:f_Todas()"></input></center></td>
										</tr>
										<?php
										    $qCliDo  = "SELECT sucidxxx, docidxxx, docsufxx, ccoidxxx, regestxx, doctipxx, regfcrex ";
										    $qCliDo .= "FROM $cAlfa.sys00121 ";
										    $qCliDo .= "WHERE ";
										    $qCliDo .= "cliidxxx = \"$gTerId\" AND ";
										    $qCliDo .= "regestxx = \"ACTIVO\" ";
                        if (!empty($gDo)){
                          $qCliDo .= "AND docidxxx LIKE \"%{$gDo}%\" ";
                        }
										    $qCliDo .= "ORDER BY ABS(docidxxx), ABS(docsufxx) ";
                        $xCliDo  = f_MySql("SELECT","",$qCliDo,$xConexion01,"");
                        $qCantidad = mysql_num_rows($xCliDo);
                        //f_Mensaje(__FILE__,__LINE__,$qCliDo." ~ ".mysql_num_rows($xCliDo));
                        
                        $y = 0;
                        $nCanReg = 0;
										    while ($xRCD = mysql_fetch_array($xCliDo)){
										      $cFecApe = "";
    											switch ($xRCD['doctipxx']){
	    											case "IMPORTACION":
			  										case "TRANSITO":  //Busco en la SIAI0200
	    											  $qFecApe  = "SELECT DOIAPEXX ";
	    											  $qFecApe .= "FROM $cAlfa.SIAI0200 ";
	    											  $qFecApe .= "WHERE ";
	    											  $qFecApe .= "DOIIDXXX = \"{$xRCD['docidxxx']}\" AND ";
	    											  $qFecApe .= "DOISFIDX = \"{$xRCD['docsufxx']}\" AND ";
	    											  $qFecApe .= "ADMIDXXX = \"{$xRCD['sucidxxx']}\" LIMIT 0,1 ";
	    											  $xFecApe  = f_MySql("SELECT","",$qFecApe,$xConexion01,"");
	    											  //f_Mensaje(__FILE__,__LINE__,$qFecApe." ~ ".mysql_num_rows($xFecApe));
	    											  $xRFA = mysql_fetch_array($xFecApe);
	    											  
	    											  $xRCD['regfcrex'] = $xRFA['DOIAPEXX'];
	    											break; 
														default:
															//No hace nada
														break;   											 
    											} ?>
 					                  <tr bgcolor="<?php echo $vSysStr['system_row_impar_color_ini'] ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_impar_color_ini'] ?>')">
                              <td Class = "letra8" align="center"><?php echo $xRCD['sucidxxx'] ?></td>
                              <td Class = "letra8" align="center"><?php echo $xRCD['docidxxx'] ?></td>
                              <td Class = "letra8" align="center"><?php echo $xRCD['docsufxx'] ?></td>
                              <td Class = "letra8" align="center"><?php echo ($xRCD['regfcrex'] == "0000-00-00" || $xRCD['regfcrex'] == "") ? "&nbsp;" : $xRCD['regfcrex'] ?></td>
                              <td Class = "letra8" align="center"><?php echo $xRCD['ccoidxxx'] ?></td>
                              <td Class = "letra8" align="center"><?php echo $xRCD['regestxx'] ?></td>
                              <td Class = "letra8"><input type = 'checkbox' name = 'ch<?php echo $nCanReg ?>' id="<?php echo $xRCD['sucidxxx']."~".$xRCD['docidxxx']."~".$xRCD['docsufxx']."~".$xRCD['regfcrex']."~".$xRCD['ccoidxxx'] ?>" <?php echo ($qCantidad == 1)? 'checked' : '' ?>></td>
    										    </tr>
                			     <?php $nCanReg++;
										    } ?>
										    <script language="javascript">
										    	document.forms['frgrm']['nSecuencia'].value = '<?php echo $nCanReg ?>';
                		    </script>
										</table>
								</center>
			 	    </fieldset>
					</td>
				</tr>
		 	</table>
		  </center>
		</form>
		<center>
			<table border="0" cellpadding="0" cellspacing="0" width="460">
				<tr height="21">
				 	<td width="278" height="21"></td>
					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer"
						onClick = "javascript:f_Enabled_Combos();f_Aceptar('<?php echo $gSecuencia ?>');f_Disabled_Combos()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Aceptar
					</td>
					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer"
						onClick = "javascript:parent.window.close()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
					</td>
				</tr>
			</table>
			<br>
		</center>
		
		<?php
      $qTerId  = "SELECT ";
      $qTerId .= "$cAlfa.SIAI0150.CLIIDXXX, ";
      $qTerId .= "$cAlfa.SIAI0150.$gTerTip, ";
      $qTerId .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
      $qTerId .= "FROM $cAlfa.SIAI0150 ";
      $qTerId .= "WHERE ";
      $qTerId .= "$cAlfa.SIAI0150.$gTerTip = \"SI\" AND ";
      $qTerId .= "$cAlfa.SIAI0150.CLIIDXXX = \"$gTerId\" AND ";
      $qTerId .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" ORDER BY $cAlfa.SIAI0150.CLIIDXXX LIMIT 0,1";
      $xTerId  = f_MySql("SELECT","",$qTerId,$xConexion01,"");
      $vTerId  = mysql_fetch_array($xTerId);
      //f_Mensaje(__FILE__,__LINE__,$qTerId." ~ ".mysql_num_rows($xTerId));

      $qTerIdB  = "SELECT ";
      $qTerIdB .= "$cAlfa.SIAI0150.CLIIDXXX, ";
      $qTerIdB .= "$cAlfa.SIAI0150.$gTerTipB, ";
      $qTerIdB .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
      $qTerIdB .= "FROM $cAlfa.SIAI0150 ";
      $qTerIdB .= "WHERE ";
      $qTerIdB .= "$cAlfa.SIAI0150.$gTerTipB = \"SI\" AND ";
      $qTerIdB .= "$cAlfa.SIAI0150.CLIIDXXX = \"$gTerIdB\" AND ";
      $qTerIdB .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" ORDER BY $cAlfa.SIAI0150.CLIIDXXX LIMIT 0,1";
      $xTerIdB  = f_MySql("SELECT","",$qTerIdB,$xConexion01,"");
      $vTerIdB  = mysql_fetch_array($xTerIdB);
      //f_Mensaje(__FILE__,__LINE__,$qTerIdB." ~ ".mysql_num_rows($xTerIdB));
    ?>            
    
    <script languaje = "javascript">
      document.forms['frgrm']['cTerTip'].value  = "<?php echo $gTerTip ?>";
      document.forms['frgrm']['cTerId'].value   = "<?php echo $vTerId['CLIIDXXX'] ?>";
      document.forms['frgrm']['cTerDV'].value   = "<?php echo f_Digito_Verificacion($gTerId) ?>";
      document.forms['frgrm']['cTerNom'].value  = "<?php echo $vTerId['CLINOMXX'] ?>";
      
      document.forms['frgrm']['cTerTipB'].value = "<?php echo $gTerTipB ?>";
      document.forms['frgrm']['cTerIdB'].value  = "<?php echo $vTerIdB['CLIIDXXX'] ?>";   
      document.forms['frgrm']['cTerDVB'].value  = "<?php echo f_Digito_Verificacion($gTerIdB) ?>";
      document.forms['frgrm']['cTerNomB'].value = "<?php echo $vTerIdB['CLINOMXX'] ?>";
      <?php 
        if ($qCantidad == 1){
          echo "f_Enabled_Combos();f_Aceptar('{$gSecuencia}');f_Disabled_Combos();\n";
        }
      ?>
    </script>
    
    <?php 
    if ($nCanReg == 0) {
      f_Mensaje(__FILE__,__LINE__," No Se Encontraron DOs para el Cliente.");
      ?>
      <script languaje = "javascript">
        parent.window.close()
      </script>
      <?php
    }
    ?>
	</body>
</html>
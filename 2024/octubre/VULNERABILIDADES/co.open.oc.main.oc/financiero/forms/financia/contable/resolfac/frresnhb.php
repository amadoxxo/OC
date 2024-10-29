<?php
  namespace openComex;
/**
	 * Proceso Sucursales.
	 * --- Descripcion: Permite Crear una Sucursal.
	 * @author
	 * @package emisioncero
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
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
		<script language="javascript">
  		function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
  				document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
  				parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
  	  }

		  function f_EnabledCombos() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				//document.forms['frgrm']['cCcoId'].disabled =false;
				//document.forms['frgrm']['cComId'].disabled = false;
				//document.forms['frgrm']['cComCod'].disabled = false;
		  }

		  function f_DisabledCombos() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				//document.forms['frgrm']['cCcoId'].disabled =true;
				//document.forms['frgrm']['cComId'].disabled = true;
		  }

			function f_Valida_Estado(){
       	var zEstado = document.forms['frgrm']['cEstado'].value.toUpperCase();
       	if(zEstado == 'A' || zEstado == 'AC' || zEstado == 'ACT' || zEstado == 'ACTI' || zEstado == 'ACTIV' || zEstado == 'ACTIVO'){
       		zEstado = 'ACTIVO';
       	} else {
       		if(zEstado == 'I' || zEstado == 'IN' || zEstado == 'INA' || zEstado == 'INAC' || zEstado == 'INACT' || zEstado == 'INACTI' || zEstado == 'INACTIV' || zEstado == 'INACTIVO') {
       			zEstado = 'INACTIVO';
       		} else {
       			zEstado = '';
       		}
       	}
       	document.forms['frgrm']['cEstado'].value = zEstado;
    	}

    	function f_Links(xLink,xSwitch,xIteration) {
				var zX    = screen.width;
				var zY    = screen.height;
				switch (xLink){
					case "cCcoId":
						if (xSwitch == "VALID") {
							var zRuta  = "frpar116.php?gWhat=VALID&gFunction=cCcoId&cCcoId="+document.frgrm.cCcoId.value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
		  				var zNx     = (zX-600)/2;
							var zNy     = (zY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frpar116.php?gWhat=WINDOW&gFunction=cCcoId&cCcoId="+document.frgrm.cCcoId.value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
					  	zWindow.focus();
						}
				  break;
				  case "cResIdH":
			     if (xSwitch == "VALID") {
             var zRuta  = "frres138.php?gWhat=VALID&gFunction=cResIdH&gResIdH="+
							             document.forms['frgrm']['cResIdH'].value.toUpperCase();
						 parent.fmpro.location = zRuta;

					 } else {
						 var zNx     = (zX-500)/2;
						 var zNy     = (zY-250)/2;
						 var zWinPro = "width=500,scrollbars=1,height=250,left="+zNx+",top="+zNy;
					   var zRuta   = "frres138.php?gWhat=WINDOW&gFunction=cResIdH&gResIdH="+
						                 document.forms['frgrm']['cResIdH'].value.toUpperCase();
						 zWindow = window.open(zRuta,"zWindow",zWinPro);
			       zWindow.focus();
					 }
				 break;
		    }
			}

			function f_Carga_Data() {
			  var band=0;
	  	  document.frgrm.cComMemo.value="|";
	  	  switch (document.frgrm.gIteration.value) {
  			  case "1":
  				  if (document.frgrm.oCheck.checked == true) {
  					  document.frgrm.cComMemo.value += document.frgrm.oCheck.value+"|";
   					}
  				break;
  				default:
  					var zSw_Prv = 0;
  					for (i=0;i<document.frgrm.oCheck.length;i++) {
  						if (document.frgrm.oCheck[i].checked == true && band==0) {
  							document.frgrm.cComMemo.value += document.frgrm.oCheck[i].value+"|";
  						}
  					}
  				break;
  			}
	  	}

	  	function validar(e){
        t=(document.all)?e.keyCode:e.which;
        //patron=/\w/;
        patron= /[A-Za-z]/;
        return patron.test(String.fromCharCode(t));
        //onkeypress="return validar(event);">
      }

	  </script>
	</head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="300">
				<tr>
					<td>
				  	<fieldset>
					  	<legend>Nuevo <?php echo $_COOKIE['kProDes'] ?></legend>
						 	<form name = 'frgrm' action = 'frresgrh.php' method = 'post' target='fmpro'>
						 	  <input type="hidden" name="gIteration" value="0" >
						 	  <input type="hidden" name="cComMemo" value="" >
						 	  <input type="hidden" name="cComMemoApl" value="" >

						 		<input type = "hidden" name = "cResTipH" >
						 		<input type = "hidden" name = "dFecFinH" >
						 		<input type = "hidden" name = "cFacIniH" >
						 		<input type = "hidden" name = "cFacFinH" >
							 	<center>
                  <fieldset>
					  	    <legend>Resolusion para Habilitar</legend>
  							 	  <table border = "0" cellpadding = "0" cellspacing = "0" width="380">
    					 		    <?php $nCol = f_Format_Cols(19);
      							 	echo $nCol; ?>
    					 		    <tr>
    										<td Class = "name" colspan = "10"><a href = "javascript:document.forms['frgrm']['cResIdH'].value  = '';
  																							f_Links('cResIdH','WINDOW')">Resolucion</a><br>
    									 	  <input type = "text" Class = "letra" name = "cResIdH" style = "width:190" maxlength="20"
    									 	    onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>';"
  		                      onBlur = "javascript:this.value=this.value.toUpperCase();
  																							 f_Links('cResIdH','WINDOW');
  													   				           this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>';">
    										</td>
    										<td Class = "name" colspan = "9">Clase<br>
    									    <input type = "text" Class = "letra" name = "cResClaH" style = "width:190" maxlength="13" readonly>
    								    </td>
                      </tr>
    								</table>
                  </fieldset>

    							<table border = "0" cellpadding = "0" cellspacing = "0" width="400">
  					 		    <?php $nCol = f_Format_Cols(20);
    							 	echo $nCol;
  					 		    $zviene = $_COOKIE['kModo'];
  					 		    /*
  					 			  if ($_COOKIE['kModo'] == "NUEVOB"){ ?>
                      <tr>
                        <td Class = "name"colspan = "2">
  											  <a href = "javascript:document.forms['frgrm']['cComId'].value  = '';
  																							f_Links('cComId','WINDOW')">Comp.</a><br>
  												<input type = "text" readonly Class = "letra" style = "width:40" name = "cComId" value="<?php echo $_POST['cCliId'] ?>"
  										      onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>';"
  		                      onBlur = "javascript:this.value=this.value.toUpperCase();
  													   				           this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>';"> </td>
  											<td Class = "name"colspan = "2">Cod.<br>
  											  <input type = "text" readonly Class = "letra" style = "width:40" name = "cComCod" value="<?php echo $_POST['cCliId'] ?>"
  										     	onFocus="javascript: this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>';"
  		                      onBlur = "javascript:this.value=this.value.toUpperCase();
  																		          this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>';">
  											</td>
  									<?php }	else{ ?>
  									  <tr>
  											<td Class = "name"colspan = "2">Comp.<br>
  											  <input type = "text" readonly Class = "letra" style = "width:40" name = "cComId" value="<?php echo $_POST['cCliId'] ?>">
  											</td>
  											<td Class = "name"colspan = "2">Cod.<br>
  											  <input type = "text" readonly Class = "letra" style = "width:40" name = "cComCod" value="<?php echo $_POST['cCliId'] ?>">
  											</td>
  									<?php }
  									*/?>
  					 		    <tr>
  										<td Class = "name" colspan = "10">Resolucion<br>
  									 	  <input type = "text" Class = "letra" name = "cResId" style = "width:200" maxlength="20">
  										</td>
  										<td Class = "name" colspan = "3">Prefijo<br>
  										  <input type = "text" Class = "letra" name = "cResPre" style = "width:60" maxlength="6"
  										    onblur = "javascript:this.value=this.value.toUpperCase();" >
  									  </td>
  										<td Class = "name" width="7">Clase<br>
  									    <input type = "text" Class = "letra" name = "cComCla" style = "width:140" maxlength="13" value="HABILITACION" readonly>
  								    </td>
                    </tr>
  								</table>
  								<table border = "0" cellpadding = "0" cellspacing = "0" width="400">
  					 		    <?php $nCol = f_Format_Cols(20);
    							 	echo $nCol;?>
  									<tr>
  										<td Class = "name" colspan = "10">
    									  <a href="javascript:show_calendar('frgrm.dResFde')" id="iFde" >Rige Desde</a><br>
    										<input type = "text" Class = "letra" style = "width:200;text-align:center" name = "dResFde"
  									      onBlur = "javascript:f_Date(this)">
    									</td>
    									<td Class = "name" colspan = "10">
    									  <a href="javascript:show_calendar('frgrm.dResFha')" id="iFha" >Rige Hasta</a><br>
    										<input type = "text" Class = "letra" style = "width:200;text-align:center" name = "dResFha"
  									      onBlur = "javascript:f_Date(this)">
    									</td>
    								</tr>
    								<tr>
  									 	<td Class = "name" colspan = "10">Factura Inicial<br>
  									 	  <input type = "hidden" name = "cRDeOld">
  									  	<input type = "text" Class = "letra"  style = "width:200;text-align:center"  name = "cResDes"
  									 		  onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>';"
  									      onblur = "javascript:this.value=this.value.toUpperCase();
  									                           this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'">
  									  </td>
  									  <td Class = "name" colspan = "10">Factura Final<br>
  									    <input type = "hidden" name = "cRHaOld">
  											<input type = "text" Class = "letra" style = "width:200;text-align:center" name = "cResHas"
  									 		  onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>';"
  									      onblur = "javascript:this.value=this.value.toUpperCase();
  									                           this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>';">
  										</td>
  									</tr>
  									<tr>
  									 	<td Class = "name" colspan = "10">Dias Aviso<br>
  									 	 	<input type = "text" Class = "letra"  style = "width:200;text-align:center"  name = "cDiasAv"
  									 		  onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'";
  									      onblur = "javascript:this.value=this.value.toUpperCase();
  									                           this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'">
  									  </td>
  									  <td Class = "name" colspan = "10">Cosecutivos Aviso<br>
  									    <input type = "text" Class = "letra" style = "width:200;text-align:center" name = "cCscAvi"
  									 		  onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>';"
  									      onblur = "javascript:this.value=this.value.toUpperCase();
  									                           this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>';">
  										</td>
  									</tr>
										<?php
										switch($cAlfa) {
											case "TESIACOSIP":
											case "DESIACOSIP":
											case "SIACOSIA":
											?>
												<tr>
													<td Class = "name" colspan = "10">Vigencia en Meses<br>
														<input type = "text" Class = "letra"  style = "width:200"  name = "cResVigMe"
															onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'";
															onblur = "javascript:this.value=this.value.toUpperCase();
																				uFixInt(this);
																			 	this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'">
													</td>
												</tr>
											<?php
											break;
											default:
												// no hace nada
											break;
										}
										?>
 									</table>


								  <fieldset>
					  	      <legend>Comprobantes de Facturacion</legend>
									  <table border = '1' cellpadding = '0' cellspacing = '0' width='380'>
  							 			<?php $nCol = f_Format_Cols(19);
  							 			echo $nCol;?>
  									  <tr>
  								      <td Class = "name" colspan = "4" align="center">Comprobante</td>
  										  <td Class = "name" colspan = "3" align="center">Codigo</td>
  										  <td Class = "name" colspan = "9" align="center">Descripcion</td>
  										  <td Class = "name" colspan = "3" align="center">Seleccione</td>
  									  </tr>
  									  <?php
                      $qSqlCom  = "SELECT * ";
                			$qSqlCom .= "FROM $cAlfa.fpar0117 ";
                			$qSqlCom .= "WHERE ";
                			$qSqlCom .= "comidxxx = \"F\" ";
                			$qSqlCom .= "ORDER BY comidxxx,comcodxx ";
                			$xSqlCom  = f_MySql("SELECT","",$qSqlCom,$xConexion01,"");

                			while ($zRCom = mysql_fetch_array($xSqlCom)) {?>
    									  <script>
    									   document.forms['frgrm']['gIteration'].value ++;
    									  </script>
    									  <tr>
    								      <td Class = "name" colspan = "4" align="center"><?php echo $zRCom['comidxxx'] ?></td>
    										  <td Class = "name" colspan = "3" align="center"><?php echo str_pad($zRCom['comcodxx'],3,"0",STR_PAD_LEFT) ?></td>
    										  <td Class = "name" colspan = "9" ><?php echo $zRCom['comdesxx'] ?></td>
    										  <td Class = "name" colspan = "3" align="center">
    												<input type="checkbox" name="oCheck" value="<?php echo $zRCom['comidxxx'].'~'.$zRCom['comcodxx'] ?>" onclick="javascript:f_Carga_Data();" >
    										  </td>
    									  </tr>
    									  <?php
                			}?>
									  </table>
									</fieldset>


    							<table border = "0" cellpadding = "0" cellspacing = "0" width="400">
                    <?php $nCol = f_Format_Cols(20);
							 			echo $nCol; ?>
  									<tr>
								   		<td Class = "name" colspan = "4">Fecha Cre<br>
									   		<input type = "text" Class = "letra"  style = "width:80;text-align:center"  name = "dFecCre" value = "<?php echo date('Y-m-d') ?>" readonly>
								    	</td>
								    	<td Class = 'name' colspan = "4">Hora Cre<br>
										 		<input type = 'text' Class = 'letra' style = "width:80;text-align:center" name = "cHorCre" value = "<?php echo date('H:i:s') ?>" readonly>
											</td>
								   		<td Class = "name" colspan = "4">Fecha Mod<br>
									   		<input type = "text" Class = "letra"  style = "width:80;text-align:center"  name = "dFecMod" value = "<?php echo date('Y-m-d') ?>" readonly>
								    	</td>
								    	<td Class = 'name' colspan = "4">Hora Mod<br>
										 		<input type = 'text' Class = 'letra' style = "width:80;text-align:center" name = "cHorMod" value = "<?php echo date('H:i:s') ?>" readonly>
											</td>
											<td Class = "name" colspan = "4">Estado<br>
										 		<input type = "text" Class = "letra" style = "width:80;text-align:center" name = "cEstado"  value = "ACTIVO"
  										    onblur = "javascript:this.value=this.value.toUpperCase();f_Valida_Estado();
  																             this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
  												onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
											</td>
										</tr>
  								</table>
								</center>
			 	      </form>
						</fieldset>
					</td>
				</tr>
		 	</table>
		</center>
		<center>
			<table border="0" cellpadding="0" cellspacing="0" width="380">
				<tr height="21">
					<?php switch ($_COOKIE['kModo']) {
						case "VER": ?>
							<td width="289" height="21"></td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
						<?php break;
						default: ?>
							<td width="198" height="21"></td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:f_EnabledCombos();document.forms['frgrm'].submit();f_DisabledCombos()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
						<?php break;
			  	} ?>
				</tr>
			</table>
		</center>
		<!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
		<?php
		switch ($_COOKIE['kModo']) {
			case "NUEVOB":
				?>
				<script languaje = "javascript">
					document.forms['frgrm']['cEstado'].readOnly  = true;
				</script>
				<?php
				$cSqlRes  = "SELECT * ";
  		  $cSqlRes .= "FROM $cAlfa.fpar0138 ";
  		  $cSqlRes .= "WHERE ";
  		  $cSqlRes .= "resclaxx = \"HABILITACION\" AND ";
  		  $cSqlRes .= "regestxx = \"ACTIVO\" ";
  		  //$cSqlRes .= "residxxx = \"$xResId\" AND ";
  		  //$cSqlRes .= "comidxxx = \"$xComId\" AND ";
  		  //$cSqlRes .= "comcodxx = \"$xComCod\" AND ";
  		  //$cSqlRes .= "restipxx = \"$xTipo\" LIMIT 0,1";
  		  $zCrsRes = f_MySql("SELECT","",$cSqlRes,$xConexion01,"");

  		  while ($zRRes = mysql_fetch_array($zCrsRes)){
  		    $zComPro=explode("|",$zRRes['rescomxx']);
    		  /* comprobantes contables */
    		  $e=0;

    		  $qSqlCom  = "SELECT * ";
    			$qSqlCom .= "FROM $cAlfa.fpar0117 ";
    			$qSqlCom .= "WHERE ";
    			$qSqlCom .= "comidxxx = \"F\" ";
    			$qSqlCom .= "ORDER BY comidxxx,comcodxx ";
    			$xSqlCom  = f_MySql("SELECT","",$qSqlCom,$xConexion01,"");

    			while ($zRCom = mysql_fetch_array($xSqlCom)) {
    			  for($i=0; $i<count($zComPro); $i++){
    			    if($zComPro[$i]!=""){
    			      $zComCad=explode("~",$zComPro[$i]);
    			      if($zRCom['comidxxx']==$zComCad[0] and $zRCom['comcodxx']==$zComCad[1]) {
    			        ?>
    			        <script>
    			          document.forms['frgrm']['oCheck']['<?php echo $e ?>'].disabled=true;
    			        </script>
    			      <?php
                }
    			    }
    			  }
    			  $e++;
    			}
  		  }
			break;
			case "EDITAR":
				f_CargaData($gResId,$gTipo,$gResCla,$gResPre);
				?>
				<script languaje = "javascript">
					document.forms['frgrm']['cResId'  ].readOnly  = true;
					document.forms['frgrm']['cResPre' ].readOnly  = true;
					document.forms['frgrm']['cComCla' ].readOnly  = true;
					document.forms['frgrm']['dFecCre' ].readOnly  = true;
					document.forms['frgrm']['cEstado' ].readOnly  = true;
				</script>
				<?php

				$cSqlRes  = "SELECT * ";
  		  $cSqlRes .= "FROM $cAlfa.fpar0138 ";
  		  $cSqlRes .= "WHERE ";
  		  $cSqlRes .= "residxxx != \"$gResId\" AND ";
  		  $cSqlRes .= "resprexx = \"$gResPre\" AND ";
  		  $cSqlRes .= "resclaxx = \"$gResCla\" AND ";
  		  $cSqlRes .= "regestxx = \"ACTIVO\" ";
  		  //$cSqlRes .= "comidxxx = \"$xComId\" AND ";
  		  //$cSqlRes .= "comcodxx = \"$xComCod\" AND ";
  		  //$cSqlRes .= "restipxx = \"$xTipo\" LIMIT 0,1";
  		  $zCrsRes = f_MySql("SELECT","",$cSqlRes,$xConexion01,"");

  		  while ($zRRes = mysql_fetch_array($zCrsRes)){
  		    $zComPro=explode("|",$zRRes['rescomxx']);
    		  /* comprobantes contables */
    		  $e=0;

    		  $qSqlCom  = "SELECT * ";
    			$qSqlCom .= "FROM $cAlfa.fpar0117 ";
    			$qSqlCom .= "WHERE ";
    			$qSqlCom .= "comidxxx = \"F\" ";
    			$qSqlCom .= "ORDER BY comidxxx,comcodxx ";
    			$xSqlCom  = f_MySql("SELECT","",$qSqlCom,$xConexion01,"");

    			while ($zRCom = mysql_fetch_array($xSqlCom)) {
    			  for($i=0; $i<count($zComPro); $i++){
    			    if($zComPro[$i]!=""){
    			      $zComCad=explode("~",$zComPro[$i]);
    			      if($zRCom['comidxxx']==$zComCad[0] and $zRCom['comcodxx']==$zComCad[1]) {
    			        ?>
    			        <script>
    			          document.forms['frgrm']['oCheck']['<?php echo $e ?>'].disabled=true;
    			        </script>
    			      <?php
                }
    			    }
    			  }
    			  $e++;
    			}
  		  }
			break;
			case "VER":
				f_CargaData($gResId,$gTipo,$gResCla,$gResPre); ?>
				<script languaje = "javascript">
					//document.forms['frgrm']['cCcoId'].readOnly	 = true;
					for (x=0;x<document.forms['frgrm'].elements.length;x++) {
            document.forms['frgrm'].elements[x].readOnly = true;
            document.forms['frgrm'].elements[x].onfocus  = "";
            document.forms['frgrm'].elements[x].onblur   = "";
            document.forms['frgrm'].elements[x].disabled = true;
            document.forms['frgrm'].elements[x].style.fontWeight = "bold";
          }

          document.getElementById('iFde').disabled=true;
				 	document.getElementById('iFde').href="#";
				 	document.getElementById('iFha').disabled=true;
				 	document.getElementById('iFha').href="#";
				</script>
			<?php break;
		} ?>

		<?php function f_CargaData($xResId,$xTipo,$xResCla,$xResPre) {
		  global $cAlfa; global $xConexion01;

		  $cSqlRes  = "SELECT * ";
		  $cSqlRes .= "FROM $cAlfa.fpar0138 ";
		  $cSqlRes .= "WHERE ";
		  $cSqlRes .= "residxxx = \"$xResId\" AND ";
		  $cSqlRes .= "resprexx = \"$xResPre\" AND ";
		  $cSqlRes .= "resclaxx = \"$xResCla\" AND ";
		  //$cSqlRes .= "comidxxx = \"$xComId\" AND ";
		  //$cSqlRes .= "comcodxx = \"$xComCod\" AND ";
		  $cSqlRes .= "restipxx = \"$xTipo\" LIMIT 0,1";
		  $zCrsRes = f_MySql("SELECT","",$cSqlRes,$xConexion01,"");

		  //f_Mensaje(__FILE__,__LINE__,"{$cSqlRes}");

		  while ($zRRes = mysql_fetch_array($zCrsRes)){
		?>
				<script language = "javascript">
				  document.forms['frgrm']['cResIdH'].value  = "<?php echo $zRRes['residhxx'] ?>";
				  document.forms['frgrm']['cResClaH'].value = "<?php echo $zRRes['resclahx'] ?>";

					document.forms['frgrm']['cResId'].value   = "<?php echo $zRRes['residxxx'] ?>";
					document.forms['frgrm']['cResPre'].value  = "<?php echo $zRRes['resprexx'] ?>";
					//document.forms['frgrm']['cComId'].value   = "<?php echo $zRRes['comidxxx'] ?>";
					//document.forms['frgrm']['cComCod'].value  = "<?php echo $zRRes['comcodxx'] ?>";
				 	document.forms['frgrm']['dResFde'].value  = "<?php echo $zRRes['resfdexx'] ?>";
				 	document.forms['frgrm']['dResFha'].value  = "<?php echo $zRRes['resfhaxx'] ?>";
				 	document.forms['frgrm']['cRDeOld'].value  = "<?php echo $zRRes['resdesxx'] ?>";
				 	document.forms['frgrm']['cResDes'].value  = "<?php echo $zRRes['resdesxx'] ?>";
				 	document.forms['frgrm']['cResHas'].value  = "<?php echo $zRRes['reshasxx'] ?>";
				 	document.forms['frgrm']['cRHaOld'].value  = "<?php echo $zRRes['reshasxx'] ?>";
				 	document.forms['frgrm']['cDiasAv'].value  = "<?php echo $zRRes['resdiasa'] ?>";
				 	document.forms['frgrm']['cCscAvi'].value  = "<?php echo $zRRes['rescscax'] ?>";
					<?php
					switch($cAlfa) {
						case "TESIACOSIP":
						case "DESIACOSIP":
						case "SIACOSIA":
						?>
							document.forms['frgrm']['cResVigMe'].value  = "<?php echo $zRRes['resvigme'] ?>";
						<?php
						break;
						default:
							// no hace nada
						break;
					}
					?>
				 	document.forms['frgrm']['cComMemo'].value = "<?php echo $zRRes['rescomxx'] ?>";
			 		document.forms['frgrm']['dFecCre'].value  = "<?php echo $zRRes['regfcrex'] ?>";
				 	document.forms['frgrm']['cHorCre'].value  = "<?php echo $zRRes['reghcrex'] ?>";
				 	document.forms['frgrm']['dFecMod'].value  = "<?php echo $zRRes['regfmodx'] ?>";
				 	document.forms['frgrm']['cHorMod'].value  = "<?php echo $zRRes['reghmodx'] ?>";

				 	document.forms['frgrm']['cEstado'].value  = "<?php echo $zRRes['regestxx'] ?>";
				</script>
		  <?php
			 	$zComPro=explode("|",$zRRes['rescomxx']);
  		  /* comprobantes contables */
  		  $e=0;

  		  $qSqlCom  = "SELECT * ";
  			$qSqlCom .= "FROM $cAlfa.fpar0117 ";
  			$qSqlCom .= "WHERE ";
  			$qSqlCom .= "comidxxx = \"F\" ";
  			$qSqlCom .= "ORDER BY comidxxx,comcodxx ";
  			$xSqlCom  = f_MySql("SELECT","",$qSqlCom,$xConexion01,"");

  			while ($zRCom = mysql_fetch_array($xSqlCom)) {
  			  for($i=0; $i<count($zComPro); $i++){
  			    if($zComPro[$i]!=""){
  			      $zComCad=explode("~",$zComPro[$i]);
  			      if($zRCom['comidxxx']==$zComCad[0] and $zRCom['comcodxx']==$zComCad[1]) {
  			        ?>
  			        <script>
  			          document.forms['frgrm']['oCheck']['<?php echo $e ?>'].checked=true;
  			        </script>
  			      <?php
              }
  			    }
  			  }
  			  $e++;
  			}
		  }
		} ?>
	</body>
</html>

<?php 
  namespace openComex;
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
   	
	   	function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
			document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
			}
		
	   

   	  function uImprimir() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
     	  var zX      = screen.width;
				var zY      = screen.height;
				var zNx     = (zX-900)/2;
				var zNy     = (zY-500)/2;
				var zWinPro = "width=900,scrollbars=1,height=500,left="+zNx+",top="+zNy;
				if(document.forms['frgrm']['vSucId'].value.length > 0 &&
				   document.forms['frgrm']['vDocTip'].value.length > 0 &&
				   document.forms['frgrm']['vDocNro'].value.length > 0){
					var zRuta = "frmdoprn.php?gSucId="+document.forms['frgrm']['vSucId'].value+
																  "&gDocTip="+document.frgrm.cComCsc.value.toUpperCase()
																  "&gDocId="+document.forms['frgrm']['vDocNro'].value;
					zWindow = window.open(zRuta,'zWinTrp',zWinPro);
				} else {
					alert("El Numero del DO esta Vacio, Verifique");
				}
	  	}

		  	
		function f_Links(xLink,xSwitch,xIteration) {
			
			var zX    = screen.width;
			var zY    = screen.height;
			switch (xLink){
			
				case "cDocId":
					if (xSwitch == "VALID") {						
						var zRuta  = "frautres.php?gWhat=VALID&gFunction=cDocId&cDocId="+
						document.frgrm.cDocId.value.toUpperCase()+"";
						parent.fmpro.location = zRuta;
					} else {
	  				var zNx     = (zX-600)/2;
						var zNy     = (zY-250)/2;
						var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
						var zRuta   = "frautres.php?gWhat=WINDOW&gFunction=cDocId&cDocId="+
						document.frgrm.cDocId.value.toUpperCase()+"";
						zWindow = window.open(zRuta,"zWindow",zWinPro);
				  	zWindow.focus();
					}
			  break;
			   }
		}
			</script>
  </head>
		<body topmargin = 0 leftmargin = 0 marginwidth = 0 marginheight = 0 style = 'margin-right:0'>
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="300">
				<tr>
					<td>
				  	<fieldset>
				  		<!-- se pinta el fielset con el proceso y la descripcion de la cookie -->
				  		 
					  	<legend>Nuevo <?php echo $_COOKIE['kProDes'] ?></legend>
					  	 
						 	<form name = 'frgrm' action = 'fraregra.php' method = 'post' target='fmpro'>
							 	<center>
      							<table border = '0' cellpadding = '0' cellspacing = '0' width='400'>
  							 			<!-- la funcion format me multiplica por 20 el parametro que recibe -->
											<?php $nCol = f_Format_Cols(20);
  							 			echo $nCol;?>
  							 			<!-- los <td> tiene que sumar elm parmetro del parametro del format_cols -->
  							 			<tr>  							 			
  											<td Class = 'clase08' colspan = '2'> SUC <br>
  												<input type = 'text' Class = 'letra' style = 'width:40' name = 'cSucId' readonly>
  							 				</td>  							 			 																		
  											<td Class = "clase08" colspan = "6">
  												<a href = "javascript:document.frgrm.cSucId.value  = '';
  																							document.frgrm.cDocId.value = '';
  																							document.frgrm.cDocSuf.value = '';
  																							document.frgrm.cDocFsr.value = '';
  																							document.frgrm.cCliId.value = '';
  																							document.frgrm.cCliDV.value = '';  																							
  																			  		  document.frgrm.cCliNom.value = '';  																			  		  
  																			  		  if(document.forms.frgrm.cDocId.value != ''){
  							 																	f_Links('cDocId','VALID');
  																			          this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'
  							 																}else{
  							 																	alert('Debe Digitar al Menos Dos Digitos del DO');
  							 																}
  																			  		  "
  																							 id="IdDoc"> DO </a><br>
  												<input type = 'text' Class = 'letra' style = 'width:120' name = 'cDocId' maxlength="10"
  							 						onBlur = "javascript:f_FixFloat(this);
  							 																if(document.forms.frgrm.cDocId.value != ''){
  							 																	f_Links('cDocId','VALID');
  																			          this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'
  							 																}else{
  							 																	alert('Debe Digitar al Menos Dos Digitos del DO');
  							 																}
  																			         "
  										    	onFocus="javascript:document.frgrm.cSucId.value  = '';
  										    											document.frgrm.cDocId.value = '';
  																			  		  document.frgrm.cDocSuf.value = '';
  																			  		  document.frgrm.cDocFsr.value = '';
  																			  		  document.frgrm.cCliId.value = '';
  																			  		  document.frgrm.cCliDV.value = ''; 
  																			  		  document.frgrm.cCliNom.value = '';  																			  		    																			  		  
  														                  this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
  											</td> 										
  											<td Class = 'clase08' colspan = '2'>SUF<br>
  												<input type = 'text' Class = 'letra' style = 'width:40' name = 'cDocSuf' readonly>
  											</td>
  											<td Class = 'clase08' colspan = '10'>RESOLUCION<br>
  												<input type = 'text' Class = 'letra' style = 'width:200' name = 'cDocFsr' readonly>
  							 				</td>												  											 											
  										</tr>
  										<tr>  											   										
	  										<td Class = 'clase08' colspan = '4'> NIT <br>
  												<input type = 'text' Class = 'letra' style = 'width:80' name = 'cCliId' readonly>
  											</td>
  											<td Class = "clase08" colspan = "1"> Dv <br>
  												<input type = "text" Class = "letra" style = "width:20" name = "cCliDV" readonly>
  											</td>
  											<td Class = "clase08" colspan = "15"> IMPORTADOR <br>
  												<input type = "text" Class = "letra" style = "width:300" name = "cCliNom" readonly>
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
			<table border="0" cellpadding="0" cellspacing="0" width="400">
				<tr height="21">
							<td width="218" height="21"></td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer"
							 onClick = "javascript:document.forms['frgrm'].submit();"
													style = "cursor:hand" title="Legalizar Formulario" id="IdImg">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Autorizar</td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>			  	
				</tr>
			</table>
		</center>
		
		<!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
		
		
		
		<?php //switch ($_COOKIE['kModo']) {
			//case "NUEVO":
				?>
				<!-- <script language = "javascript">
				// Estado Permanece ReadOnly
					document.forms['frgrm']['cEstado'].readOnly  = true;
				</script>
				 -->
				 	<!-- switch ($_COOKIE['kModo']) {
			    case "NUEVO":
				  $qUsrNom = "SELECT USRNOMXX ";
				  $qUsrNom .= "FROM $cAlfa.SIAI0003 ";
				  $qUsrNom .= "WHERE USRIDXXX = \"{$_COOKIE['kUsrId']}\" LIMIT 0,1";
				  $xUsrNom  = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
				  $cUsrNom = "USUARIO SIN NOMBRE";
				  while ($xRUN = mysql_fetch_array($xUsrNom)) {
					$cUsrNom = trim($xRUN['USRNOMXX']);
					?>
					<script language = "javascript">
					document.forms['frgrm']['cUsrId'].value  = "<?php //echo $_COOKIE['kUsrId'] ?>";
					document.forms['frgrm']['cUsrNom'].value = "<?php //echo $cUsrNom ?>";
					</script>	-->				
				  <?php
        // }
		//	break;
		
		?>

		<?php  function f_CargaData($xSerId) {
			/*Darle la ruta de La BD y la conexion 
			 * para que no se pierda al cargar los datos*/
		  global $cAlfa; global $xConexion01;

		  /* TRAIGO DATOS DE FORMULARIOS*/
     	$qFoiDat  = "SELECT * ";
			$qFoiDat .= "FROM $cAlfa.ffoi0000 ";
	    $qFoiDat .= "WHERE ";
	    $qFoiDat .= "ccoidxxx = \"$xSerId\" LIMIT 0,1";
			$xDatCco  = f_MySql("SELECT","",$qFoiDat,$xConexion01,"");
			//f_mensaje(__FILE__,__LINE__,$qFoiDat);
			

		 /* EMPIEZO A RECORRER EL CURSOR DE CABECERA */
			while ($vFioDat = mysql_fetch_array($xFoDat)) {
		?>
				<script language = "javascript">
					document.forms['frgrm']['cSucId'].value    = "<?php echo $vFioDat['sucidxxx'] ?>";
		      document.forms['frgrm']['cDocId'].value = "<?php echo $vFioDat['docidxxx'] ?>";
				 	document.forms['frgrm']['cDocSuf'].value   = "<?php echo $vFioDat['docsufxx'] ?>";
				 	document.forms['frgrm']['cDocFsr'].value   = "<?php echo $vFioDat['docfsrxx'] ?>";
				 	document.forms['frgrm']['cCliId'].value    = "<?php echo $vFioDat['cliidxxx'] ?>";
				 	document.forms['frgrm']['cCliDV'].value	   = "<?php echo f_Digito_Verificacion($vFioDat['CLIIDXXX']) ?>";
				 	document.forms['frgrm']['cCliNom'].value   = "<?php echo $vFioDat['CLINOMXX'] ?>";
				</script>
			<?php }
		} ?>
	</body>        
  </body> 
</html>
<?php
  namespace openComex;
   /**
	 * Observaciones Formularios al Gasto.
	 * --- Descripcion: Permite guardar las Observaciones de los Formularios que son asignados al Gasto.
	 * @author Johana Arboleda Ramos <dp1@opentecnologia.com.co>
	 * @version 002
	 */
	include("../../../../libs/php/utility.php"); ?>
	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
	<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
	<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
	<script language="javascript">
	  function f_Inser_Observ() {
		  var nSwicht = 0;
		  var cMsj = "\n";

		  if(document.forms['frgrm']['cGofId'].value == "") {
			  nSwicht = 1;
			  cMsj += "Debe Seleccionar un Grupo de Observacion.\n";
			}

		  if(document.forms['frgrm']['cObservPopUp'].value == "") {
			  nSwicht = 1;
			  cMsj += "Debe Digitar una Observacion.\n";
			}
		  
		  if(nSwicht == 0) {
		    window.opener.document.forms['frgrm']['cGofId'].value=document.forms['frgrm']['cGofId'].value;
		    window.opener.document.forms['frgrm']['cObserv'].value=document.forms['frgrm']['cObservPopUp'].value;
		    window.opener.f_Enviar_Form_Ya();
		    window.close();
		  } else {
				alert(cMsj + "Verifique.");
			}
	  }
	  
	  function f_Links(xLink,xSwitch) {
			var nX    = screen.width;
			var nY    = screen.height;
			switch (xLink){
				case "cGofId":
						var nNx     = (nX-400)/2;
						var nNy     = (nY-250)/2;
						var cWinPro = "width=400,scrollbars=1,height=250,left="+nNx+",top="+nNy;
						var cRuta   = "frpar123.php?gWhat=WINDOW&gFunction=cGofId&gGofId="+document.forms['frgrm']['cGofId'].value.toUpperCase();
						zWindow = window.open(cRuta,"zWindow",cWinPro);
				  	zWindow.focus();
			  break;
			}
		}
	</script>
	<form name="frgrm" action="frforg35.php" method="POST">
	<center>
	<table width="400" cellspacing="0" cellpadding="0" border="0">
	 	<tr>
	 	  <td>
	      <fieldset>
	        <legend>Observaciones:</legend>
	       		<center>
	 	       		<table border = '0' cellpadding = '0' cellspacing = '0' width='400'>
		  		 			<?php $nCol = f_Format_Cols(20);
		  		 			echo $nCol;?>
	 	       			<tr>
	  							<td Class = "clase08" colspan = "03">
	  								<a href = "javascript:document.frgrm.cGofId.value  = '';
	  															  		  document.frgrm.cGofDes.value = '';
	  																			f_Links('cGofId','VALID')" id="IdObs">Id</a><br>
	  								<input type = 'text' Class = 'letra' style = 'width:60' name = 'cGofId' maxlength="10"
	  			 						onBlur = "javascript:this.value=this.value.toUpperCase();
	  															         f_Links('cGofId','VALID');
	  															         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
	  						    	onFocus="javascript:document.frgrm.cGofId.value  = '';
	  															  		  document.frgrm.cGofDes.value = '';
	  										                  this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
	  							</td>
	  							<td Class = 'clase08' colspan = '17'>Grupo de Observaci&oacute;n<br>
	  								<input type = 'text' Class = 'letra' style = 'width:340' name = 'cGofDes' readonly>
	  							</td>
	  						</tr>
	              <tr>
	                <td colspan = '20'>Observaci&oacute;n<br>
	                  <textarea style = 'width:400' rows=6 name=cObservPopUp></textarea>
	                </td>
	              </tr>
	            </table>
	 	       	</center>
	   	  </fieldset>
	   	  <table border="0" cellpadding="0" cellspacing="0" width="400">
					<tr height="21">
						<td width="218" height="21" Class="name" ></td>
						<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:hand" onClick = "javascript:f_Inser_Observ();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
						<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = "javascript:window.close();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
				 </tr>
				</table>
	   	</td>
	  </tr>
	</table>
	</center>
	</form>
	</body>
	</html>
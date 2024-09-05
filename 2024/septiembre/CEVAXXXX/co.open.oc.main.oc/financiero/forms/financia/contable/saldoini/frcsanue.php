<?php
	/**
	* Creacion y/o Actualizacion de saldos desde un txt delimitado por tabulaciones, edición de saldos
	* @author Fabián Sierra pineda <fabian.sierra@opentecnologia.com.co>
	* @version 001
	*/
	include("../../../../libs/php/utility.php");
  include("../../../../libs/php/uticonta.php");
  
?>
<html>
	<head>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
    <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
    <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
		<script languaje = 'javascript'>
		  function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
  			document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
  			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
  	  }
  	  
  	  function fnGuardar(xAction) {
  	    document.forms['frgrm']['Btn_Guardar'].disabled=true;
        document.forms['frgrm']['nTimesSave'].value++;
        document.forms['frgrm'].action = xAction;
			  document.forms['frgrm'].submit();
  	  }
  	  
  	  function fnDownLoad() {
  	  	document.forms['frgrm'].action = "frcsaexc.php";
			  document.forms['frgrm'].submit();
  	  }
  	  
  	  function f_Links(xLink,xSwitch) {
        var nX    = screen.width;
        var nY    = screen.height;
        switch (xLink) {
          case "cComCod":
            if (xSwitch == "VALID") {
              var cPathUrl = "frcsa117.php?gModo="+xSwitch+"&gFunction="+xLink+
                                         "&gComCod="+document.forms['frgrm']['cComCod'].value.toUpperCase();
              //alert(cPathUrl);
              parent.fmpro.location = cPathUrl;
            } else {
              var nNx      = (nX-500)/2;
              var nNy      = (nY-250)/2;
              var cWinOpt  = "width=500,scrollbars=1,height=250,left="+nNx+",top="+nNy;
              var cPathUrl = "frcsa117.php?gModo="+xSwitch+"&gFunction="+xLink+
                                         "&gComCod="+document.forms['frgrm']['cComCod'].value.toUpperCase();
              cWindow = window.open(cPathUrl,xLink,cWinOpt);
              cWindow.focus();
            }
          break;
          case "cCcoId":
            if (xSwitch == "VALID") {
              var cPathUrl = "frcsa116.php?gModo="+xSwitch+"&gFunction="+xLink+
                                         "&gCcoId="+document.forms['frgrm']['cCcoId'].value.toUpperCase();
              //alert(cPathUrl);
              parent.fmpro.location = cPathUrl;
            } else {
              var nNx      = (nX-300)/2;
              var nNy      = (nY-250)/2;
              var cWinOpt  = "width=300,scrollbars=1,height=250,left="+nNx+",top="+nNy;
              var cPathUrl = "frcsa116.php?gModo="+xSwitch+"&gFunction="+xLink+
                                          "&gCcoId="+document.forms['frgrm']['cCcoId'].value.toUpperCase();
              //alert(cPathUrl);
              cWindow = window.open(cPathUrl,xLink,cWinOpt);
              cWindow.focus();
            }
          break;
          case "cSccId":
            if (xSwitch == "VALID") {
              var cPathUrl = "frcsa120.php?gModo="+xSwitch+"&gFunction="+xLink+
                                         "&gCcoId="+document.forms['frgrm']['cCcoId'].value.toUpperCase()+
                                         "&gSccId="+document.forms['frgrm']['cSccId'].value.toUpperCase();
              //alert(cPathUrl);
              parent.fmpro.location = cPathUrl;
            } else {
              var nNx      = (nX-300)/2;
              var nNy      = (nY-250)/2;
              var cWinOpt  = "width=300,scrollbars=1,height=250,left="+nNx+",top="+nNy;
              var cPathUrl = "frcsa120.php?gModo="+xSwitch+"&gFunction="+xLink+
                                          "&gCcoId="+document.forms['frgrm']['cCcoId'].value.toUpperCase()+
                                         "&gSccId="+document.forms['frgrm']['cSccId'].value.toUpperCase();
              cWindow = window.open(cPathUrl,xLink,cWinOpt);
              cWindow.focus();
            }
          break;
          case "cTerId":  
          case "cTerNom":
            if (xSwitch == "VALID") {
              var cPathUrl = "frcsa150.php?gModo="+xSwitch+"&gFunction="+xLink+
                                        "&gTerId="+document.forms['frgrm'][xLink].value.toUpperCase();
              //alert(cPathUrl);
              parent.fmpro.location = cPathUrl;
            } else {
              var nNx      = (nX-600)/2;
              var nNy      = (nY-250)/2;
              var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
              var cPathUrl = "frcsa150.php?gModo="+xSwitch+"&gFunction="+xLink+
                                         "&gTerId="+document.forms['frgrm'][xLink].value.toUpperCase();
               
              cWindow = window.open(cPathUrl,xLink,cWinOpt);
              cWindow.focus();
            }
          break;
          case "cPucId":
          if (xSwitch == "VALID") {
            var cPathUrl  = "frcsa115.php?gWhat="+xSwitch+
                                            "&gFunction="+xLink+
                                            "&cPucId="+document.forms['frgrm']['cPucId'].value.toUpperCase();
            parent.fmpro.location = cPathUrl;
            } else {
              var nNx      = (nX-600)/2;
              var nNy      = (nY-250)/2;
              var cWinOpt  = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
              var cPathUrl = "frcsa115.php?gWhat="+xSwitch+
                                            "&gFunction="+xLink+
                                            "&cPucId="+document.forms['frgrm']['cPucId'].value.toUpperCase();
                                            
              cWindow = window.open(cPathUrl,xLink,cWinOpt);
              cWindow.focus();
            }
          break;
        }
      }
		</script>
	</head>
	<body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
	  <?php
	  switch ($_COOKIE['kModo']) {
      case "SUBIR": ?>
  		  <!-- Formulario para subir los saldos -->
    		<center>
    		  <table border ="0" cellpadding="0" cellspacing="0">
    		    <tr>
    					<td>
    					  <form name = "frgrm" enctype='multipart/form-data' action = "frcsactg.php" method = "post" target="fmpro">
    					    <input type = "hidden" name = "nTimesSave" value = "0">
    					    <table border ="0" cellpadding="0" cellspacing="0">
    								<tr>
    									<td>
    										<fieldset>
    										  <legend>Carga de <?php echo $_COOKIE['kProDes'] ?></legend>
    											<table border = "0" cellpadding = "0" cellspacing = "0" width="380">
    												<?php $nCol = f_Format_Cols(19); echo $nCol; ?>
                            <tr>
                              <td Class="name" colspan="19">Tipo de saldo<br>
                                <select name="cTipSaldo" style="width: 380">
                                  <option value="CxC">CUENTAS POR COBRAR</option>
                                  <option value="CxP">CUENTAS POR PAGAR</option>
                                </select>
                            </tr>
                            <?php
                              $cExtPer = "application/vnd.ms-excel,";
                            ?>
                            <tr>
                              <td Class="name" colspan="19">
                                <br>
                                <label><input type = "radio" Class = "letra" name = "cSalTip" value="SALDOINICIAL" checked> Saldo Inicial</label>
                                <label><input type = "radio" Class = "letra" name = "cSalTip" value="CRUZARSALDO"> Cruzar Saldo</label>
                              </td>
                            </tr>
                            <tr>
                              <td Class="name" colspan="19"><br>Archivo<br>
                                <input type = "file" Class = "letra" style = "width:380" name = "cArcPla" accept="<?php echo $cExtPer ?>">
    													</td>
    												</tr>
                            <tr>
                              <td colspan = "30">
                                <span style="color:#0046D5">Extensiones permitidas: .xls, .xlsx</span><br>
                              </td>
                            </tr> 
    												<tr>
    													<td Class="name" colspan="19"><br>
    														<a href = "javascript:fnDownLoad('0')">Descargar Formato</a>
    													</td>
    												</tr>
    												<tr>
                              <td Class="letra" colspan="19"><br>
                                <b>Recomendaciones:</b><br>
                                Debe exportar el archivo Excel a un archivo TXT delimitado por tabulaciones.<br><br>
                              </td>
                            </tr>
    							        </table>
    										</fieldset>
    										<table border = "0" cellpadding = "0" cellspacing = "0" width="400">
    											<tr height="21">
    												<td width="218" height="21">&nbsp;</td>
    												<td width="91" height="21" Class="name" >
    													<input type="button" name="Btn_Guardar" id="Btn_Guardar" value="Subir" Class = "name"  style = "width:91;height:21;background:url(<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif) no-repeat;border:0px"
    														onclick = "javascript:fnGuardar('frcsactg.php')">
    												</td>
    										  	<td width="91" height="21" Class="name" >
    										  		<input type="button" value="Salir" Class = "name"  style = "width:91;height:21;background:url(<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif) no-repeat;border:0px"
    														onClick = "javascript:f_Retorna()">
    										  	</td>
    										  </tr>
    										</table>
    									</td>
    								</tr>
    							</table>																		
    						</form>
    					</td>
    				</tr>
    			</table>
    		</center>
		  <?php break;
      case "NUEVO":
      case "EDITAR":
      case "VER": ?>
        <center>
          <table border ="0" cellpadding="0" cellspacing="0" width="460">
            <tr>
              <td>
                <form name = "frgrm" action = "frcsagra.php" method = "post" target="fmpro">
                  <input type = "hidden" name = "cComIdS"     value = "">
                  <input type = "hidden" name = "cComCodS"    value = "">
                  <input type = "hidden" name = "cComCscS"    value = "">
                  <input type = "hidden" name = "cComCsc2S"   value = "">
                  <input type = "hidden" name = "dComFecS"    value = "">
                  <input type = "hidden" name = "nTimesSave"  value = "0">
                  <input type = "hidden" name = "cComId_Old"  value = "">
                  <input type = "hidden" name = "cComCod_Old" value = "">
                  <input type = "hidden" name = "cComCsc_Old" value = "">
                  <input type = "hidden" name = "cComSeq_Old" value = "">
                  <input type = "hidden" name = "cTerId_Old"  value = "">
                  <input type = "hidden" name = "cPucId_Old"  value = "">
                  <input type = "hidden" name = "cPucMov_Old"  value = "">
                  <input type = "hidden" name = "dComFec_old"  value = "">
                  <input type = "hidden" name = "dComVen_Old"  value = "">
                  <input type = "hidden" name = "cCcoId_Old"  value = "">
                  <input type = "hidden" name = "cSccId_Old"  value = "">
                  <input type = "hidden" name = "cComVlr_Old"  value = "">
                  <input type = "hidden" name = "cComVlrNf_Old"  value = "">
                  <input type = "hidden" name = "cComObs"  value = "">
                  <fieldset>
                    <legend><?php echo $_COOKIE['kMenDes'] ?></legend>
                    <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:460">
                      <?php $cCols = f_Format_Cols(23); echo $cCols; ?>
                      <tr>
                        <td Class = "name" colspan = "1">Id<br>
                          <input type = "text" Class = "letra" style = "width:20" name = "cComId" readonly>
                        </td>
                        <td Class = "name" colspan = "2">
                          <a href = "javascript:document.forms['frgrm']['cComCod'].value='';
                                                document.forms['frgrm']['cComDes'].value='';
                                                f_Links('cComCod','VALID')" id="id_href_cComCod">Cod</a><br>
                          <input type = "text" Class = "letra" style = "width:40;text-align:center" name = "cComCod" value = ""
                                 onfocus="javascript:this.value='';
                                                     document.forms['frgrm']['cComDes'].value='';
                                                     this.style.background='#00FFFF'"
                                 onblur = "javascript:f_Links('cComCod','VALID');
                                                      this.style.background='#FFFFFF';
                                                      document.forms['frgrm']['cComDes'].focus();">
                        </td>
                        <td Class = "name" colspan = "13">Descripcion<br>
                          <input type = "text" Class = "letra" style = "width:260" name = "cComDes" readonly>
                        </td>
                        <td Class = "name" colspan = "7">Consecutivo<br>
                          <input type = "text" Class = "letra" style = "width:140;text-align:center" name = "cComCsc" <?php echo (($vSysStr['financiero_permitir_caracteres_alfanumericos_consecutivo_manual']) == 'NO') ? "onblur = \"javascript:f_FixFloat(this);\"" : '' ?> maxlength = "<?php echo (($vSysStr['financiero_digitos_consecutivo_manual']+0) > 0) ? $vSysStr['financiero_digitos_consecutivo_manual'] : 20 ?>"
                        </td>
                      </tr>
                      <tr>
                        <td Class = "name" colspan = "7">
                          <a href = "javascript:document.forms['frgrm']['cTerId'].value  = '';
                                                document.forms['frgrm']['cTerNom'].value = '';
                                                document.forms['frgrm']['cTerDV'].value  = '';
                                                f_Links('cTerId','VALID')" id="id_href_cTerId">Cliente/Proveedor</a><br>
                          <input type = "text" Class = "letra" style = "width:140;text-align:center" name = "cTerId"
                          onfocus="javascript:document.forms['frgrm']['cTerId'].value  = '';
                                              document.forms['frgrm']['cTerNom'].value = '';
                                              document.forms['frgrm']['cTerDV'].value  = '';
                                              this.style.background='#00FFFF'"
                          onBlur = "javascript:this.value=this.value.toUpperCase();
                                               f_Links('cTerId','VALID');
                                               this.style.background='#FFFFFF'">
                        </td>
                        <td Class = "name" colspan = "1"><br>
                          <input type = "text" Class = "letra" style = "width:20;text-align:center" name = "cTerDV" readonly>
                        </td>
                        <td Class = "name" colspan = "14"><br>
                          <input type = "text" Class = "letra" style = "width:280" name = "cTerNom"
                          onfocus="javascript:document.forms['frgrm']['cTerId'].value  = '';
                                              document.forms['frgrm']['cTerNom'].value = '';
                                              document.forms['frgrm']['cTerDV'].value  = '';
                                              this.style.background='#00FFFF'"
                          onBlur = "javascript:this.value=this.value.toUpperCase();
                                               f_Links('cTerNom','VALID');
                                               this.style.background='#FFFFFF'">
                        </td>
                        <td Class = "name" colspan = "1"><br>
                          <input type = "text" Class = "letra" style = "width:20;text-align:center" readonly>
                        </td>
                      </tr>
                      <tr>
                        <td Class = "name" colspan = "6">
                          <a href = "javascript:document.forms['frgrm']['cCcoId'].value='';
                                                document.forms['frgrm']['cSccId'].value='';
                                                f_Links('cCcoId','VALID');" id="id_href_cCcoId">Centro Costo</a><br>
                          <input type = "text" Class = "letra" style = "width:120;text-align:center" name = "cCcoId" maxlength = "10"
                          onfocus="javascript:this.value='';
                                              document.forms['frgrm']['cSccId'].value='';
                                              this.style.background='#00FFFF'"
                          onblur = "javascript:f_Links('cCcoId','VALID');
                                                 this.style.background='#FFFFFF';">
                        </td>
                        <td Class = "name" colspan = "1"><br>
                          <input type = "text" Class = "letra" style = "width:20;text-align:center" readonly>
                        </td>
                        <td Class = "name" colspan = "6">
                          <a href = "javascript:document.forms['frgrm']['cSccId'].value='';
                                                f_Links('cSccId','VALID');" id="id_href_cSccId">Sub Centro</a><br>
                          <input type = "text" Class = "letra" style = "width:120;text-align:center" name = "cSccId" maxlength = "20"
                          onfocus="javascript:this.value='';
                                              this.style.background='#00FFFF'"
                          onblur = "javascript:f_Links('cSccId','VALID');
                                               this.style.background='#FFFFFF';">
                        </td>
                        <td Class = "name" colspan = "5">
                           <a href='javascript:show_calendar("frgrm.dComFec")' id="id_href_dComFec">Fecha</a><br>
                           <input type = "text" Class = "letra" style = "width:100;text-align:center"
                           name = "dComFec" onBlur = "javascript:f_Date(this)">
                        </td>
                        <td Class = "name" colspan = "5">
                          <a href='javascript:show_calendar("frgrm.dComVen")' id="id_href_dComVen">Vencimiento</a><br>
                          <input type = "text" Class = "letra" style = "width:100;text-align:center"
                          name = "dComVen" onBlur = "javascript:f_Date(this)">
                        </td>
                      </tr>
                      <tr>
                        <td Class = "name" colspan = "4">
                          <a href = "javascript:document.forms['frgrm']['cPucId'].value  = '';
                                                document.forms['frgrm']['cPucDes'].value = '';
                                                f_Links('cPucId','VALID')" id="id_href_IdCta">Cuenta</a><br>
                          <input type = "text" Class = "letra" style = "width:080;text-align:center" name = "cPucId" maxlength = "10"
                          onfocus="javascript:document.forms['frgrm']['cPucId'].value  = '';
                                              document.forms['frgrm']['cPucDes'].value = '';
                                              this.style.background='#00FFFF'"
                          onBlur = "javascript:this.value=this.value.toUpperCase();
                                              f_Links('cPucId','VALID');
                                              this.style.background='#FFFFFF'">
                        </td>
                        <td Class = "name" colspan = "10">Descripcion<br>
                          <input type = "text" Class = "letra" style = "width:200" name = "cPucDes" readonly>
                        </td>
                        <td Class = "name" colspan = "4">Mov<br>
                          <select Class = "letrase" name = "cPucMov" style = "width:80">
                            <option value="D">DEBITO</option>
                            <option value="C">CREDITO</option>
                          </select>
                        </td>
                        <td Class = "name" colspan = "5">Valor<br>
                          <input type = "text" Class = "letra" style = "width:100;text-align:right" name = "nValor">
                        </td>
                      </tr>
                    </table>
                  </fieldset>
                  <center>
                  <table border = "0" cellpadding = "0" cellspacing = "0" width="460">
                      <tr height="21">
                        <?php switch ($_COOKIE['kModo']) {
                          case "VER": ?>
                            <td width="369" height="21">&nbsp;</td>
                            <td width="91" height="21" Class="name" >
                              <input type="button" name = "Salir" value="Salir" Class = "name"  style = "width:91;height:21;background:url(<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif) no-repeat;border:0px"
                                onClick = "javascript:f_Retorna()">
                            </td>
                          <?php break;
                          default: ?>
                            <td width="278" height="21">&nbsp;</td>
                            <td width="91" height="21" Class="name" >
                              <input type="button" name="Btn_Guardar" id="Btn_Guardar" value="Guardar" Class = "name"  style = "width:91;height:21;background:url(<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif) no-repeat;border:0px"
                                onclick = "javascript:fnGuardar('frcsagra.php')">
                            </td>
                            <td width="91" height="21" Class="name" >
                              <input type="button" value="Salir" Class = "name"  style = "width:91;height:21;background:url(<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif) no-repeat;border:0px"
                                onClick = "javascript:f_Retorna()">
                            </td>
                          <?php break;
                           } ?>
                      </tr>
                    </table>
                  </center>  
                </form>
              </td>
            </tr>
          </table>
        </center>
      <?php break;
    } ?>
           
    <!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
    <?php switch ($_COOKIE['kModo']) {
      case "VER":
        f_Carga_Data($gComId,$gComCod,$gComCsc,$gComCsc2,$gComFec); ?>
        <script languaje = "javascript">
          for (x=0;x<document.forms['frgrm'].elements.length;x++) {
            if (document.forms['frgrm'].elements[x].name != "Salir") {
              document.forms['frgrm'].elements[x].readOnly = true;
              document.forms['frgrm'].elements[x].onfocus  = "";
              document.forms['frgrm'].elements[x].onblur   = "";
              document.forms['frgrm'].elements[x].disabled = true;
            }
          }
          
          document.getElementById('id_href_cComCod').href  = "javascript:alert('Opcion no Permitida.')";
          document.getElementById('id_href_cCcoId').href   = "javascript:alert('Opcion no Permitida.')";
          document.getElementById('id_href_cSccId').href   = "javascript:alert('Opcion no Permitida.')";
          document.getElementById('id_href_dComFec').href  = "javascript:alert('Opcion no Permitida.')";
          document.getElementById('id_href_dComVen').href  = "javascript:alert('Opcion no Permitida.')";
          document.getElementById('id_href_IdCta').href    = "javascript:alert('Opcion no Permitida.')";
          document.getElementById('id_href_cTerId').href   = "javascript:alert('Opcion no Permitida.')";
        </script>
      <?php break;
      case "EDITAR":
        f_Carga_Data($gComId,$gComCod,$gComCsc,$gComCsc2,$gComFec);
      break;
    } ?>

    <?php 
    function f_Carga_Data($xComId,$xComCod,$xComCsc,$xComCsc2,$xComFec) {
      global $xConexion01; global $cAlfa;

      $xAno = substr($xComFec,0,4);

      // Traigo los datos de la cabecera.
      $qConCab  = "SELECT * ";
      $qConCab .= "FROM $cAlfa.fcod$xAno ";
      $qConCab .= "WHERE ";
      $qConCab .= "comidxxx = \"$xComId\"  AND ";
      $qConCab .= "comcodxx = \"$xComCod\" AND ";
      $qConCab .= "comcscxx = \"$xComCsc\" AND ";
      $qConCab .= "comcsc2x = \"$xComCsc2\" LIMIT 0,1";
      $xConCab  = f_MySql("SELECT","",$qConCab,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qConCab." ~ ".mysql_num_rows($xConCab));
      $vConCab  = mysql_fetch_array($xConCab);

      //Busco descripcion de la cuenta
      $qPucId  = "SELECT pucdesxx ";
      $qPucId .= "FROM $cAlfa.fpar0115 ";
      $qPucId .= "WHERE ";
      $qPucId .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = \"{$vConCab['pucidxxx']}\" AND ";
      $qPucId .= "regestxx = \"ACTIVO\" LIMIT 0,1";
      $xPucId  = f_MySql("SELECT","",$qPucId,$xConexion01,"");
      //f_Mensaje(__FILE__,__LINE__,$qPucId." ~ ".mysql_num_rows($xPucId));
      $vPucDes = mysql_fetch_array($xPucId);
      $vConCab['pucdesxx'] = ($vPucDes['pucdesxx'] == "") ? "CUENTA SIN DESCRIPCION" : $vPucDes['pucdesxx'];
            
      // Busco la descripcion del comprobante.
      $qComDes  = "SELECT comdesxx,comtcoxx ";
      $qComDes .= "FROM $cAlfa.fpar0117 ";
      $qComDes .= "WHERE ";
      $qComDes .= "comidxxx = \"{$vConCab['comidcxx']}\"  AND ";
      $qComDes .= "comcodxx = \"{$vConCab['comcodcx']}\" LIMIT 0,1";
      $xComDes  = f_MySql("SELECT","",$qComDes,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qComDes." ~ ".mysql_num_rows($xComDes));
      $vComDes  = mysql_fetch_array($xComDes);
      $vConCab['comdesxx'] = ($vComDes['comdesxx'] == "") ? "COMPROBANTE SIN DESCRIPCION" : $vComDes['comdesxx'];
      $vConCab['comtcoxx'] = $vComDes['comtcoxx'];

      // Busco la descripcion del centro de costos.
      $qCcoDes  = "SELECT ccodesxx ";
      $qCcoDes .= "FROM $cAlfa.fpar0116 ";
      $qCcoDes .= "WHERE ";
      $qCcoDes .= "ccoidxxx = \"{$vConCab['ccoidxxx']}\" LIMIT 0,1";
      $xCcoDes  = f_MySql("SELECT","",$qCcoDes,$xConexion01,"");
      $vCcoDes  = mysql_fetch_array($xCcoDes);
      $vConCab['ccodesxx'] = ($vCcoDes['ccodesxx'] == "") ? "CENTRO DE COSTO SIN DESCRIPCION" : $vCcoDes['ccodesxx'];

      // Busco el nombre del tercero cliente.
      $qCliNom  = "SELECT IF(CLINOMXX != \"\",CLINOMXX,CONCAT(CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X)) AS CLINOMXX ";
      $qCliNom .= "FROM $cAlfa.SIAI0150 ";
      $qCliNom .= "WHERE ";
      $qCliNom .= "CLIIDXXX = \"{$vConCab['teridxxx']}\" LIMIT 0,1";
      $xCliNom  = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
      $vCliNom  = mysql_fetch_array($xCliNom);

      // Busco el nombre del tercero proveedor.
      $qProNom  = "SELECT IF(CLINOMXX != \"\",CLINOMXX,CONCAT(CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X)) AS CLINOMXX ";
      $qProNom .= "FROM $cAlfa.SIAI0150 ";
      $qProNom .= "WHERE ";
      $qProNom .= "CLIIDXXX = \"{$vConCab['terid2xx']}\" LIMIT 0,1";
      $xProNom  = f_MySql("SELECT","",$qProNom,$xConexion01,"");
      $vProNom  = mysql_fetch_array($xProNom);
      ?>
      <script language = "javascript">
        document.forms['frgrm']['cComIdS'].value     = "<?php echo $vConCab['comidxxx'] ?>";
        document.forms['frgrm']['cComCodS'].value    = "<?php echo $vConCab['comcodxx'] ?>";
        document.forms['frgrm']['cComCscS'].value    = "<?php echo $vConCab['comcscxx'] ?>";
        document.forms['frgrm']['cComCsc2S'].value   = "<?php echo $vConCab['comcsc2x'] ?>";
        document.forms['frgrm']['dComFecS'].value    = "<?php echo $vConCab['comfecxx'] ?>";
        document.forms['frgrm']['cComId'].value      = "<?php echo $vConCab['comidcxx'] ?>";
        document.forms['frgrm']['cComCod'].value     = "<?php echo $vConCab['comcodcx'] ?>";
        document.forms['frgrm']['cComCsc'].value     = "<?php echo $vConCab['comcsccx'] ?>";
        document.forms['frgrm']['cTerId'].value      = "<?php echo $vConCab['teridxxx'] ?>";
        document.forms['frgrm']['cTerDV'].value      = "<?php echo f_Digito_Verificacion($vConCab['teridxxx']) ?>";
        document.forms['frgrm']['cTerNom'].value     = "<?php echo $vCliNom['CLINOMXX'] ?>";
        document.forms['frgrm']['cComDes'].value     = "<?php echo $vConCab['comdesxx'] ?>";
        document.forms['frgrm']['cCcoId'].value      = "<?php echo $vConCab['ccoidxxx'] ?>";
        document.forms['frgrm']['cSccId'].value      = "<?php echo $vConCab['sccidxxx'] ?>";
        document.forms['frgrm']['dComFec'].value     = "<?php echo $vConCab['comfecxx'] ?>";
        document.forms['frgrm']['dComVen'].value     = "<?php echo $vConCab['comfecve'] ?>";
        document.forms['frgrm']['cPucId'].value      = "<?php echo $vConCab['pucidxxx'] ?>";
        document.forms['frgrm']['cPucDes'].value     = "<?php echo $vConCab['pucdesxx'] ?>";
        document.forms['frgrm']['cPucMov'].value     = "<?php echo $vConCab['commovxx'] ?>";
        document.forms['frgrm']['nValor'].value      = "<?php echo $vConCab['comvlrxx']+0 ?>";
        document.forms['frgrm']['cComObs'].value     = "<?php echo $vConCab['comobsxx'] ?>";
        //Documento cruce anterior
        document.forms['frgrm']['cComId_Old'].value  = "<?php echo $vConCab['comidcxx'] ?>";
        document.forms['frgrm']['cComCod_Old'].value = "<?php echo $vConCab['comcodcx'] ?>";
        document.forms['frgrm']['cComCsc_Old'].value = "<?php echo $vConCab['comcsccx'] ?>";
        document.forms['frgrm']['cComSeq_Old'].value = "<?php echo $vConCab['comseqcx'] ?>";
        document.forms['frgrm']['cTerId_Old'].value  = "<?php echo $vConCab['teridxxx'] ?>";
        document.forms['frgrm']['cPucId_Old'].value  = "<?php echo $vConCab['pucidxxx'] ?>";
        document.forms['frgrm']['cPucMov_Old'].value = "<?php echo $vConCab['commovxx'] ?>";
        document.forms['frgrm']['dComFec_old'].value = "<?php echo $vConCab['comfecxx'] ?>";
        document.forms['frgrm']['dComVen_Old'].value = "<?php echo $vConCab['comfecve'] ?>";
        document.forms['frgrm']['cCcoId_Old'].value  = "<?php echo $vConCab['ccoidxxx'] ?>";
        document.forms['frgrm']['cSccId_Old'].value  = "<?php echo $vConCab['sccidxxx'] ?>";
        document.forms['frgrm']['cComVlr_Old'].value    = "<?php echo $vConCab['comvlrxx'] ?>";
        document.forms['frgrm']['cComVlrNf_Old'].value  = "<?php echo $vConCab['comvlrnf'] ?>";
      </script>
        <?php } ?>
	</body>
</html>
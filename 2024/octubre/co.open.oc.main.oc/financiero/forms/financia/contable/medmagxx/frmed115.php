<?php 
  namespace openComex;
  include("../../../../libs/php/utility.php"); 
?>
<!--
  Los Parametros que Recibo son :
  $gSearch = Un dato para filtrar la consulta con el WHERE
  $gFields = A que campos del formulario voy a devolver los datos
  $gWhat   = Que voy a hacer, una ventana o una validacion
-->
<?php if (!empty($gWhat) && !empty($gFunction)) {
  //f_Mensaje(__FILE__,__LINE__,str_replace("~","%",$gQuery));
?>
  <html>
    <head>
      <title>Param&eacute;trica de Cuentas</title>
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
      <script languaje = 'javascript'>
        function f_Marca() {
          if (document.forms['frgrm']['oCheckAll'].checked == true){
            if (document.forms['frgrm']['nRecords'].value == 1){
             document.forms['frgrm']['oCheck'].checked=true;
            } else {
              if (document.forms['frgrm']['nRecords'].value > 1){
                for (i=0;i<document.forms['frgrm']['oCheck'].length;i++){
                 document.forms['frgrm']['oCheck'][i].checked = true;
                }
              }
            }
          } else {
            if (document.forms['frgrm']['nRecords'].value == 1){
             document.forms['frgrm']['oCheck'].checked=false;
            } else {
              if (document.forms['frgrm']['nRecords'].value > 1){
                for (i=0;i<document.forms['frgrm']['oCheck'].length;i++){
                 document.forms['frgrm']['oCheck'][i].checked = false;
                }
              }
            }
          }
        }
	      function f_Carga_Grilla(xRecords) {
	        switch (xRecords) {
	          case "1":
	            if (document.forms['frgrm']['oCheck'].checked == true) {
	              var zMatriz = document.forms['frgrm']['oCheck'].id.split('~');
	              window.opener.document.forms['frgrm']['cPucId' +<?php echo $nSecuencia ?>].value  = zMatriz[0];
	              window.opener.document.forms['frgrm']['cPucDes' +<?php echo $nSecuencia ?>].value = zMatriz[1];
	            }
	          break;
	          default:
	            var zCheckOn = 0; // Variable para saber cuantos check vienen seleccioonados
	            for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
	              if (document.forms['frgrm']['oCheck'][i].checked == true) {
	                zCheckOn++;
	              }
	            }
	              
	            if (zCheckOn == 1) {  // Si selecciono un check
	              var zSwPrv = 0; // Switch de Primera Vez
	              for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
	                if (document.forms['frgrm']['oCheck'][i].checked == true) {
	                  if (zSwPrv == 0) { // Si es la primera vez que entre cambio el estado del switch
	                    zSwPrv = 1;
	                  } else { // Si no es la primera vez que entro adiciono una row
	                    window.opener.f_Add_New_Row_Cuenta();
	                  }
	                  var zMatriz = document.frgrm.oCheck[i].id.split('~');
	                  window.opener.document.forms['frgrm']['cPucId' +<?php echo $nSecuencia ?>].value = zMatriz[0];
	                  window.opener.document.forms['frgrm']['cPucDes' +<?php echo $nSecuencia ?>].value = zMatriz[1];
	                }
	              }
	            }
	            /* Si selecciono varios checks verifico que este parado en la ultima row */
	            if (zCheckOn > 1) {
	              if (document.forms['frgrm']['nSecuencia'].value == window.opener.document.forms['frgrm']['nSecuencia'].value) {
	                var zSwPrv = 0; // Switch de Primera Vez
	                  for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
	                    if (document.forms['frgrm']['oCheck'][i].checked == true) {
	                      if (zSwPrv == 0) { // Si es la primera vez que entre cambio el estado del switch
	                        zSwPrv = 1;
	                      } else { // Si no es la primera vez que entro adiciono una row
	                        window.opener.f_Add_New_Row_Cuenta();
	                      }
	                      var zMatriz = document.forms['frgrm']['oCheck'][i].id.split('~');
	                      window.opener.document.forms['frgrm']['cPucId' +window.opener.document.forms['frgrm']['nSecuencia'].value].value = zMatriz[0];
	                      window.opener.document.forms['frgrm']['cPucDes' +window.opener.document.forms['frgrm']['nSecuencia'].value].value = zMatriz[1];
	                    }
	                  }
	                } else {
	                  alert('Solo se Puede Ingresar Multiples Registros si esta Ubicado en la Ultima Posicion de los Items');
	                }
	            }
	            break;
	          }
	          /* Funciones que Debo Llamar en mi Papa */
	          window.close();
	      }
	    </script>
    </head>
    <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
    <center>
      <table border ="0" cellpadding="0" cellspacing="0" width="300">
        <tr>
          <td>
            <fieldset>
              <legend>Param&eacute;trica de Cuentas</legend>
              <form name = "frgrm" action = "" method = "post" target = "fmpro">
                <input type = 'hidden' name = 'cCadena' value = '<?php echo $cCadena ?>' style='width:500px' readonly>
                <input type = "hidden" name = "nSecuencia" value = "<?php echo $nSecuencia ?>">
                <input type = "hidden" name = "oCheckOn"   value = "0">
                <input type = "hidden" name = "nRecords" value = "">
                <?php
                  switch ($gWhat) {
                    case "WINDOW":
                      /**
		                   * Codigo para buscar las cuentas no mayores
		                   */
		                  $cCueSel = "";
		                  
		                  /**
		                   * Busco las cuentas donde pucgruxx este solo una vez
		                  */
		                  $qCueSel  = "SELECT pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx,COUNT(CONCAT(pucgruxx,pucctaxx)) AS contaxxx ";
		                  $qCueSel .= "FROM $cAlfa.fpar0115 ";
		                  $qCueSel .= "WHERE regestxx = \"ACTIVO\" ";
		                  $qCueSel .= "GROUP BY pucgruxx";
		                  $xCueSel  = f_MySql("SELECT","",$qCueSel,$xConexion01,"");
		                  $cPucAux0 = "";
		                  while ($zRow = mysql_fetch_array($xCueSel)){
		                    if($zRow['contaxxx']==1){
		                      $cCueSel .= "\"{$zRow['pucgruxx']}{$zRow['pucctaxx']}{$zRow['pucsctax']}{$zRow['pucauxxx']}{$zRow['pucsauxx']}\",";
		                    }else{
		                      $cPucAux0 .= "\"{$zRow['pucgruxx']}\",";
		                    }
		                  }
		                  $cPucAux0 = substr($cPucAux0,0,strlen($cPucAux0)-1);
		                  
		                  /**
		                   * Busco las cuentas donde pucgruxx,pucctaxx este solo una vez
		                  */
		                  $qCueSel  = "SELECT pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx,COUNT(CONCAT(pucgruxx,pucctaxx)) AS contaxxx ";
		                  $qCueSel .= "FROM $cAlfa.fpar0115 ";
		                  $qCueSel .= "WHERE ";
		                  $qCueSel .= "CONCAT(pucctaxx,pucsctax,pucauxxx,pucsauxx) <> \"00000000\" AND ";
		                  $qCueSel .= "pucgruxx IN ($cPucAux0) AND ";
		                  $qCueSel .= "regestxx = \"ACTIVO\" ";
		                  $qCueSel .= "GROUP BY pucgruxx,pucctaxx";
		                  $xCueSel  = f_MySql("SELECT","",$qCueSel,$xConexion01,"");
		                  $cPucAux1 = "";
		                  while ($zRow = mysql_fetch_array($xCueSel)){
		                    if($zRow['contaxxx']==1){
		                      $cCueSel .= "\"{$zRow['pucgruxx']}{$zRow['pucctaxx']}{$zRow['pucsctax']}{$zRow['pucauxxx']}{$zRow['pucsauxx']}\",";
		                    }else{
		                      $cPucAux1 .= "\"{$zRow['pucgruxx']}{$zRow['pucctaxx']}\",";
		                    }
		                  }
		                  $cPucAux1 = substr($cPucAux1,0,strlen($cPucAux1)-1);
		                  
		                                        
		                  /**
		                   * Busco las cuentas donde pucgruxx,pucctaxx,pucsctax este solo una vez
		                  */                     
		                  $qCueSel  = "SELECT pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx,COUNT(CONCAT(pucgruxx,pucctaxx,pucsctax)) AS contaxxx ";
		                  $qCueSel .= "FROM $cAlfa.fpar0115 ";
		                  $qCueSel .= "WHERE ";
		                  $qCueSel .= "CONCAT(pucsctax,pucauxxx,pucsauxx) <> \"000000\" AND ";
		                  $qCueSel .= "CONCAT(pucgruxx,pucctaxx) IN ($cPucAux1) AND ";
		                  $qCueSel .= "regestxx = \"ACTIVO\" ";
		                  $qCueSel .= "GROUP BY pucgruxx,pucctaxx,pucsctax";
		                  $xCueSel  = f_MySql("SELECT","",$qCueSel,$xConexion01,"");
		                  $cPucAux2 = "";
		                  while ($zRow = mysql_fetch_array($xCueSel)){
		                    if($zRow['contaxxx']==1){
		                      $cCueSel .= "\"{$zRow['pucgruxx']}{$zRow['pucctaxx']}{$zRow['pucsctax']}{$zRow['pucauxxx']}{$zRow['pucsauxx']}\",";
		                  }else{
		                      $cPucAux2 .= "\"{$zRow['pucgruxx']}{$zRow['pucctaxx']}{$zRow['pucsctax']}\",";
		                    }
		                  }
		                  $cPucAux2 = substr($cPucAux2,0,strlen($cPucAux2)-1);
		                  
		                  /**
		                   * Busco las cuentas donde pucgruxx,pucctaxx,pucsctax,pucauxxx este solo una vez
		                  */ 
		                  $qCueSel  = "SELECT pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx,COUNT(CONCAT(pucgruxx,pucctaxx,pucsctax)) AS contaxxx ";
		                  $qCueSel .= "FROM $cAlfa.fpar0115 ";
		                  $qCueSel .= "WHERE ";
		                  $qCueSel .= "CONCAT(pucauxxx,pucsauxx) <> \"0000\" AND ";
		                  $qCueSel .= "CONCAT(pucgruxx,pucctaxx,pucsctax) IN ($cPucAux2) AND ";
		                  $qCueSel .= "regestxx = \"ACTIVO\" ";
		                  $qCueSel .= "GROUP BY pucgruxx,pucctaxx,pucsctax,pucauxxx";
		                  $xCueSel  = f_MySql("SELECT","",$qCueSel,$xConexion01,"");
		                  $cPucAux3 = "";
		                  while ($zRow = mysql_fetch_array($xCueSel)){
		                    if($zRow['contaxxx']==1){
		                      $cCueSel .= "\"{$zRow['pucgruxx']}{$zRow['pucctaxx']}{$zRow['pucsctax']}{$zRow['pucauxxx']}{$zRow['pucsauxx']}\",";
		                    }else{
		                      $cPucAux3 .= "\"{$zRow['pucgruxx']}{$zRow['pucctaxx']}{$zRow['pucsctax']}{$zRow['pucauxxx']}\",";
		                    }
		                  }
		                  $cPucAux3 = substr($cPucAux3,0,strlen($cPucAux3)-1);
		                 
		                  /**
		                     * Busco las cuentas donde pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx este solo una vez
		                   */
		                  $qCueSel  = "SELECT pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx,COUNT(CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx)) AS contaxxx ";
		                  $qCueSel .= "FROM $cAlfa.fpar0115 ";
		                  $qCueSel .= "WHERE ";
		                   $qCueSel .= "pucsauxx <> \"00\" AND ";
		                  $qCueSel .= "CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx) IN ($cPucAux3) AND ";
		                  $qCueSel .= "regestxx = \"ACTIVO\" ";
		                  $qCueSel .= "GROUP BY pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx";
		                  $xCueSel  = f_MySql("SELECT","",$qCueSel,$xConexion01,"");
		                  while ($zRow = mysql_fetch_array($xCueSel)){
		                    if($zRow['contaxxx']==1){
		                      $cCueSel .= "\"{$zRow['pucgruxx']}{$zRow['pucctaxx']}{$zRow['pucsctax']}{$zRow['pucauxxx']}{$zRow['pucsauxx']}\",";
		                    }
		                  }
		                  
		                  $cCueSel = substr($cCueSel,0,strlen($cCueSel)-1);
		                  /**
		                   * Fin de busqueda de las cuentas no mayores 
		                  */
		                  
		                  $qPucDes   = "SELECT *,CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) as cuentaxx ";
                      $qPucDes  .= "FROM $cAlfa.fpar0115 ";
                      $qPucDes  .= "WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) LIKE \"%$gPucId%\" AND ";
                      $qPucDes  .= "CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) IN ($cCueSel) AND ";
                      $qPucDes  .= "regestxx = \"ACTIVO\" ORDER BY ABS(cuentaxx)";
                      
                      $xPucDes  = f_MySql("SELECT","",$qPucDes,$xConexion01,"");

                      if (mysql_num_rows($xPucDes) > 0) { ?>
                        <center>
                          <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                            <tr>
                              <td widht ="020" bgcolor="#D6DFF7" Class = "name"><center><input type="checkbox" name="oCheckAll" onClick = 'javascript:f_Marca();'></center></td>
                              <td widht ="100" bgcolor="#D6DFF7" Class = "name"><center>CUENTA</center></td>
                              <td widht ="280" bgcolor="#D6DFF7" Class = "name"><center>NOMBRE</center></td>
                              <td widht ="050" bgcolor="#D6DFF7" Class = "name"><center>RETENCION</center></td>
                              <td widht ="050" bgcolor="#D6DFF7" Class = "name"><center>ESTADO</center></td>
                            </tr>
                            <?php 
                            $nPucId = 0;
                            while ($zRow = mysql_fetch_array($xPucDes)) {
                              $nPucId ++; ?>
                              <tr>
                                <td class = 'name'><center><input type = 'checkbox' style = 'width:20' name="oCheck"
                                     id = "<?php echo $zRow['cuentaxx']."~".$zRow['pucdesxx']?>"></center></td>
                                <td class= "name"><?php echo $zRow['cuentaxx'] ?></td>                                    
                                <td class= "name"><?php echo $zRow['pucdesxx'] ?></td>
                                <td class= "name" align="right"><?php echo $zRow['pucretxx'] ?></td>
                                <td class= "name"><?php echo $zRow['regestxx'] ?></td>
                              </tr>
                           <?php } ?>
                          </table>
                          <table border="0" cellpadding="0" cellspacing="0" width="500">
                            <tr height="21">
                              <td width="409" height="21"></td>
                              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:hand" onclick="javascript:f_Carga_Grilla('<?php echo mysql_num_rows($xPucDes) ?>')" readonly>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
                            </tr>
                          </table>
                          <script languaje = "javascript">
                            document.forms['frgrm']['nRecords'].value = '<?php echo $nPucId; ?>';
                          </script>
                        </center>
                      <?php } else {
                        f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");?>
                        <script languaje = "javascript">
                          window.opener.document.forms['frgrm']['cPucId' +<?php echo $nSecuencia ?>].value  = '';
                          window.opener.document.forms['frgrm']['cPucDes' +<?php echo $nSecuencia ?>].value = '';
                        </script>
                      <?php
                      }
                    break;
                    case "VALID":
                    break;
                  }
                ?>
              </form>
            </fieldset>
          </td>
        </tr>
      </table>
    </center>
  </body>
</html>
<?php } else {
  f_Mensaje(__FILE__,__LINE__,"No se Recibieron Parametros Completos");
} ?>
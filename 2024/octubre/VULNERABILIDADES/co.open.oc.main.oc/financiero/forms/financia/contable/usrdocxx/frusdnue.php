<?php
  namespace openComex;
/**
	 * Proceso Concepto Contable.
	 * --- Descripcion: Permite Crear un Nuevo Concepto Contables.
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
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
		<script language="javascript">
  		function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
  				document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
  				parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
  	  }

	  </script>
	</head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="300">
				<tr>
					<td>
					  <fieldset>
					  	<legend><?php echo ucfirst(strtolower($_COOKIE['kModo']))." ".$_COOKIE['kProDes'] ?></legend>
						 	<form name = 'frgrm' action = 'frusdgra.php' method = 'post' target='fmpro'>
						 		<input type="hidden" name="gIteration" value="0">
                <input type="hidden" name="nSecuencia" value="0">
							 	<center>
							 	  <fieldset>
                    <legend><b>Datos Generales</b></legend>
      							<table border = '0' cellpadding = '0' cellspacing = '0' width='500'>
  							 			<?php $nCol = f_Format_Cols(25);
  							 			echo $nCol;?>
									    <tr>
  								      <td Class = "name" colspan = "5">C&oacute;digo<br>
  												<input type = "text" Class = "letra" style = "width:100" name = "cUsrId" readonly>
  										  </td>
  										  <td Class = "name" colspan = "20">Descripci&oacute;n<br>
  												 <input type = "text" Class = "letra" style = "width:400" name = "cUsrNom" readonly>
  										  </td>
  									  </tr>
									  </table>
									</fieldset>

									<fieldset>
					  	      <legend><b>Comprobantes</b></legend>
									  <table border = '1' cellpadding = '0' cellspacing = '0' width='500'>
  							 			<?php $nCol = f_Format_Cols(25);
  							 			echo $nCol;?>
  									  <tr>
  								      <td Class = "name" colspan = "4" align="center">
  												Comprobante
  										  </td>
  										  <td Class = "name" colspan = "4" align="center">
  												Codigo
  										  </td>
  										  <td Class = "name" colspan = "13" align="center">
  												Descripcio&oacute;n
  										  </td>
  										  <td Class = "name" colspan = "4" align="center">
  												Seleccione
  										  </td>
  									  </tr>
  									  <?php
                      $qSqlCom  = "SELECT comidxxx,comcodxx,comdesxx ";
                			$qSqlCom .= "FROM $cAlfa.fpar0117 ";
                			$qSqlCom .= "WHERE regestxx = \"ACTIVO\" ";
                			$qSqlCom .= "ORDER BY comidxxx,comcodxx ";
                			$xSqlCom  = f_MySql("SELECT","",$qSqlCom,$xConexion01,"");
                			$i=1;
                			while ($xRSC = mysql_fetch_array($xSqlCom)) {
                				?>
    									  <script>
    									   document.forms['frgrm']['gIteration'].value ++;
    									  </script>
    									  <tr>
    								      <td Class = "name" colspan = "4" align="center">
                            <div style="margin-left:5px"><?php echo $xRSC['comidxxx'] ?></div>
    										  </td>
    										  <td Class = "name" colspan = "4" align="center">
    												<?php echo str_pad($xRSC['comcodxx'],3,"0",STR_PAD_LEFT) ?>
    										  </td>
    										  <td colspan = "13" align="left">
    												<div style="margin-left:5px"><?php echo $xRSC['comdesxx'] ?></div>
    										  </td>
    										  <td Class = "name" colspan = "4" align="center">
    												<input type="checkbox" name="oCheck<?php echo $i ?>" value="<?php echo $xRSC['comidxxx'].'~'.$xRSC['comcodxx'] ?>">
    										  </td>
    									  </tr>
    									  <?php
    									  $i++;
                			}?>
									  </table>
									</fieldset>

                  <?php if ($cAlfa == "GRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "DEGRUMALCO") { ?>
                    <fieldset>
                      <legend><b>Comprobantes autorizados para realizar traslado de DO a DO</b></legend>
                      <table border = '1' cellpadding = '0' cellspacing = '0' width='500'>
                        <?php $nCol = f_Format_Cols(25);
                        echo $nCol;?>
                        <tr>
                          <td Class = "name" colspan = "4" align="center">
                            Comprobante
                          </td>
                          <td Class = "name" colspan = "17" align="center">
                            Descripcio&oacute;n
                          </td>
                          <td Class = "name" colspan = "4" align="center">
                            Seleccione
                          </td>
                        </tr>
                        <?php
                        $qSqlCom  = "SELECT comidxxx,comtipxx ";
                        $qSqlCom .= "FROM $cAlfa.fpar0117 ";
                        $qSqlCom .= "WHERE ";
                        $qSqlCom .= "comidxxx != \"F\" AND ";
                        $qSqlCom .= "comidxxx != \"X\" AND ";
                        $qSqlCom .= "regestxx = \"ACTIVO\" ";
                        $qSqlCom .= "GROUP BY comidxxx,comtipxx ";
                        $qSqlCom .= "ORDER BY comidxxx ";
                        $xSqlCom  = f_MySql("SELECT","",$qSqlCom,$xConexion01,"");
                        $i=1;
                        while ($xRSC = mysql_fetch_array($xSqlCom)) {
                          $xRSC['comdesxx'] = $xRSC['comtipxx'];
                          if ($xRSC['comidxxx'] == "C" && $xRSC['comtipxx'] == "") {
                            $xRSC['comdesxx'] = "NOTA CREDITO";
                          }
                          if ($xRSC['comidxxx'] == "D" && $xRSC['comtipxx'] == "") {
                            $xRSC['comdesxx'] = "NOTA DEBITO";
                          }
                          if ($xRSC['comidxxx'] == "M" && $xRSC['comtipxx'] == "") {
                            $xRSC['comdesxx'] = "CAJA MENOR";
                          }
                          if ($xRSC['comidxxx'] == "R" && $xRSC['comtipxx'] == "") {
                            $xRSC['comdesxx'] = "RECIBO DE CAJA";
                          }
                          if ($xRSC['comidxxx'] == "G" && $xRSC['comtipxx'] == "") {
                            $xRSC['comdesxx'] = "EGRESO";
                          }
                          if ($xRSC['comtipxx'] == "CPE") {
                            $xRSC['comdesxx'] = "CAUSACION PROVEEDOR EMPRESA";
                          }
                          if ($xRSC['comtipxx'] == "CPC") {
                            $xRSC['comdesxx'] = "CAUSACION PROVEEDOR CLIENTE";
                          }
                          if ($xRSC['comtipxx'] == "RCM") {
                            $xRSC['comdesxx'] = "REEMBOLSO DE CAJA MENOR";
                          }
                          ?>
                          <script>
                          document.forms['frgrm']['nSecuencia'].value ++;
                          </script>
                          <tr>
                            <td Class = "name" colspan = "4" align="center">
                              <div style="margin-left:5px"><?php echo $xRSC['comidxxx'] ?></div>
                            </td>
                            <td colspan = "17" align="left">
                              <div style="margin-left:5px"><?php echo $xRSC['comdesxx'] ?></div>
                            </td>
                            <td Class = "name" colspan = "4" align="center">
                              <input type="checkbox" name="oCheckTr<?php echo $i ?>" value="<?php echo $xRSC['comidxxx'].'~'.$xRSC['comtipxx'] ?>">
                            </td>
                          </tr>
                          <?php
                          $i++;
                        }?>
                      </table>
                    </fieldset>
                  <?php } ?>
								</center>
			 	      </form>
						</fieldset>
					</td>
				</tr>
		 	</table>
		</center>
		<center>
			<table border="0" cellpadding="0" cellspacing="0" width="500">
				<tr height="21">
					<?php switch ($_COOKIE['kModo']) {
						case "VER": ?>
							<td width="409" height="21"></td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
						<?php break;
						default: ?>
							<td width="318" height="21"></td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:document.forms['frgrm'].submit();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
						<?php break;
			  	} ?>
				</tr>
			</table>
		</center>
		<!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
		<?php
		switch ($_COOKIE['kModo']) {
			case "NUEVO":
			break;
			case "EDITAR":
        f_CargaData($gUsrId);
      break;
			case "VER":
				?>
				<script languaje = "javascript">
				  for (x=0;x<document.forms['frgrm'].elements.length;x++) {
            document.forms['frgrm'].elements[x].readOnly = true;
            document.forms['frgrm'].elements[x].onfocus  = "";
            document.forms['frgrm'].elements[x].onblur   = "";
            document.forms['frgrm'].elements[x].disabled = true;
          }
				</script>
			  <?php
			  f_CargaData($gUsrId);
			break;
		} ?>

		<?php
		function f_CargaData($xUsrId) {
		  global $xConexion01; global $cAlfa;
		  
	  	/**
	  	 * Buscando datos del usuario
	  	 */
	  	
	  	$qUsrDat  = "SELECT ";
			$qUsrDat .= "$cAlfa.SIAI0003.USRIDXXX,";
			$qUsrDat .= "$cAlfa.SIAI0003.USRNOMXX,";
      $qUsrDat .= "$cAlfa.SIAI0003.USRDOCXX,";
      $qUsrDat .= "$cAlfa.SIAI0003.USRDOCTR ";
			$qUsrDat .= "FROM $cAlfa.SIAI0003 ";
			$qUsrDat .= "WHERE ";
			$qUsrDat .= "$cAlfa.SIAI0003.USRIDXXX = \"$xUsrId\" LIMIT 0,1 ";
			$xUsrDat  = f_MySql("SELECT","",$qUsrDat,$xConexion01,""); 
			$xRUD = mysql_fetch_array($xUsrDat); 
			// f_Mensaje(__FILE__,__LINE__,$qUsrDat);
			
			?>

			<script language = "javascript">
			  document.forms['frgrm']['cUsrId'].value   = "<?php echo $xRUD['USRIDXXX'] ?>";
			  document.forms['frgrm']['cUsrNom'].value  = "<?php echo $xRUD['USRNOMXX'] ?>";
			</script>
			<?php 
			
			$mUsrDoc=explode("|",$xRUD['USRDOCXX']);
		  /* comprobantes contables */
		  $e=1;
		  $qSqlCom  = "SELECT comidxxx,comcodxx,comdesxx ";
			$qSqlCom .= "FROM $cAlfa.fpar0117 ";
			$qSqlCom .= "WHERE regestxx = \"ACTIVO\" ";
			$qSqlCom .= "ORDER BY comidxxx,comcodxx ";
			$xSqlCom  = f_MySql("SELECT","",$qSqlCom,$xConexion01,"");

			while ($xRSC = mysql_fetch_array($xSqlCom)) {
			  for($i=0; $i<count($mUsrDoc); $i++){
			    if($mUsrDoc[$i]!=""){
			      $zComCad=explode("~",$mUsrDoc[$i]);
			      if($xRSC['comidxxx']==$zComCad[0] and $xRSC['comcodxx']==$zComCad[1]){?>
			        <script>
			          document.forms['frgrm']['oCheck<?php echo $e ?>'].checked=true;
			        </script>
			      <?php }
			    }
			  }
			  $e++;
      } 
      
      if ($cAlfa == "GRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "DEGRUMALCO") {
        $mUsrDocTR=explode("|",$xRUD['USRDOCTR']);
        $e=1;
        $qSqlCom  = "SELECT comidxxx,comtipxx ";
        $qSqlCom .= "FROM $cAlfa.fpar0117 ";
        $qSqlCom .= "WHERE ";
        $qSqlCom .= "comidxxx != \"F\" AND ";
        $qSqlCom .= "comidxxx != \"X\" AND ";
        $qSqlCom .= "regestxx = \"ACTIVO\" ";
        $qSqlCom .= "GROUP BY comidxxx,comtipxx ";
        $qSqlCom .= "ORDER BY comidxxx ";
        $xSqlCom  = f_MySql("SELECT","",$qSqlCom,$xConexion01,"");

        while ($xRSC = mysql_fetch_array($xSqlCom)) {
          for($i=0; $i<count($mUsrDocTR); $i++){
            if($mUsrDocTR[$i]!=""){
              $zComCad=explode("~",$mUsrDocTR[$i]);
              if($xRSC['comidxxx']==$zComCad[0] and $xRSC['comtipxx']==$zComCad[1]){?>
                <script>
                  document.forms['frgrm']['oCheckTr<?php echo $e ?>'].checked=true;
                </script>
              <?php }
            }
          }
          $e++;
        } 
      }
		} ?>
	</body>
</html>
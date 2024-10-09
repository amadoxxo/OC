<?php
  namespace openComex;
/**
	* 
	* Descripcion: Formulario para Ver Cuentas .
	* @author Jhon Escobar<jhonescobar990317@gmail.com>
	* @package openComex
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
  
  $qSysProbg = "SELECT * ";
  $qSysProbg .= "FROM $cBeta.sysprobg ";
  $qSysProbg .= "WHERE ";
  $qSysProbg .= "pbaidxxx = \"$gPbaId\" LIMIT 0,1";
  $xSysProbg = f_MySql("SELECT", "", $qSysProbg, $xConexion01, "");
  
  $vSysProbg = mysql_fetch_array($xSysProbg);
  $mPost = f_Explode_Array($vSysProbg['pbapostx'], "|", "~");
  for ($nP = 0; $nP < count($mPost); $nP++) {
    if ($mPost[$nP][0] != "") {
      $mArcProBg[$nInd_mArcProBg][$mPost[$nP][0]] = $mPost[$nP][1];
    }
  }

  
?>
<html>
	<head>
	<title>Ver Filtros de Cuentas</title>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/estilo.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/general.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/layout.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/custom.css'>
		<script language = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New ?>/utility.js'></script>
		<script language = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory_New ?>/date_picker.js"></script>
		<script language="javascript">
			function fnRetorna(){
				window.close();
			}
  	</script>
	</head>
	<body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="400">
				<tr>
					<td>
				  	<fieldset>
					  	<legend><b>Ver Filtros de Cuentas</b></legend><br>
							<center>
								<table cellspacing = "0" cellpadding = "1" border = "1" width = "400">
									<tr>
										<td widht ="140" bgcolor="#D6DFF7" Class = "name"><center>Cuenta</center></td>
										<td widht ="260" bgcolor="#D6DFF7" Class = "name"><center>Nombre</center></td>
									</tr>
									<?php 
										$vPucId = explode(", ",$mArcProBg[$nInd_mArcProBg]['cPucId']);
										$cPucId = "\"".implode("\",\"", $vPucId)."\"";
									
										$qPucDes  = "SELECT *,CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) as cuentaxx ";
										$qPucDes .= "FROM $cAlfa.fpar0115 ";
										$qPucDes .= "WHERE ";
										$qPucDes .= "CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) IN ($cPucId) ";
										$qPucDes .= "ORDER BY ABS(cuentaxx)";
										$xPucDes  = f_MySql("SELECT","",$qPucDes,$xConexion01,"");
										while ($xRPD = mysql_fetch_array($xPucDes)) {
											?>
											<tr>
												<td class= "name"><?php echo $xRPD['cuentaxx'] ?></td>                                    
												<td class= "name"><?php echo $xRPD['pucdesxx'] ?></td>
											</tr>
											<?php 
										} 
									?>
								</table>
							</center>
						</fieldset>
            <center>
              <table border="0" cellpadding="0" cellspacing="0" width="400">
                <tr height="21">
                  <td width="300" height="21"></td>
                  <td width="89" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand"
                      onClick = "javascript:fnRetorna();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
                  </td>
                </tr>
              </table>
            </center>
					</td>
				</tr>
			</table>
		</center>		
	</body>
</html>
<?php
  namespace openComex;
  /**
  * Parametrica de Cuentas Contables
  * Este programa permite Mostrar el Plan Unico de Cuentas 
  * @author  openTecnologia - Desarrollo
  * @package openComex
  * @version 3.0.0
  **/

  include("../../../../libs/php/utility.php");

  if ($gWhat !="" && $gFunction !="") {

?>
	<html>
		<head>
			<title>Parametrica de Cuentas</title>
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
	   	<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/programs/estilo.css">

	  </head>
	  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>

	  <center>
			<table border ="0" cellpadding="0" cellspacing="0" width="300">
				<tr>
					<td>
						<fieldset>
			   			<legend>Parametrica de Cuentas</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php	  							
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
	  						
	  							switch ($gWhat) {
	  								case "WINDOW":
	  								  //f_Mensaje(__FILE__,__LINE__,"Entre");	  								  
	  								  $qPucDes   = "SELECT *,CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) as cuentaxx ";
	  								  $qPucDes  .= "FROM $cAlfa.fpar0115 ";
	  								  $qPucDes  .= "WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) LIKE \"%$cPucId%\" AND ";
	  								  $qPucDes  .= "CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) IN ($cCueSel) AND ";
	  								  $qPucDes  .= "regestxx = \"ACTIVO\" ORDER BY ABS(cuentaxx)";	  								  
	  								  $xPucDes  = f_MySql("SELECT","",$qPucDes,$xConexion01,"");
	  								  //f_Mensaje(__FILE__,__LINE__,mysql_num_rows($xPucDes));
	  								  
	  									if ($xPucDes && mysql_num_rows($xPucDes) > 0) {
	  									  //f_Mensaje(__FILE__,__LINE__,"Entre");
	  									  ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width ="500">
														<tr>
															<td widht ="060" bgcolor="#D6DFF7" Class = "name"><center>CUENTA</center></td>
															<td widht ="370" bgcolor="#D6DFF7" Class = "name"><center>NOMBRE</center></td>
															<td widht ="020" bgcolor="#D6DFF7" Class = "name"><center>RETENCION</center></td>
															<td widht ="050" bgcolor="#D6DFF7" Class = "name"><center>ESTADO</center></td>

														</tr>
														<?php while ($zRow = mysql_fetch_array($xPucDes)) {
															if (mysql_num_rows($xPucDes) > 1) { ?>
																<tr>
																	<td style="width:060" class= "name">
																		<a href = "javascript:window.opener.document.forms['frgrm']['<?php echo $gFunction ?>'].value ='<?php echo $zRow['cuentaxx']?>';
																                          window.close()"><?php echo $zRow['cuentaxx'] ?></a></td>
																	<td width ="370" class= "name"><?php echo $zRow['pucdesxx'] ?></td>
																	<td width ="020" class= "name"><?php echo $zRow['pucretxx'] ?></td>
																	<td width ="050" class= "name"><?php echo $zRow['regestxx'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
																	window.opener.document.forms['frgrm']['<?php echo $gFunction ?>'].value  = '<?php echo $zRow['cuentaxx'] ?>';
																	window.close();
																</script>
															<?php }
														} ?>
													</table>
												</center>
	  									<?php	} else {
	  										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");?>
												<script languaje="javascript">
													window.opener.document.forms['frgrm']['<?php echo $gFunction ?>'].value  = '';
													window.close();
												</script>
											<?php }
	  								break;
	  								case "VALID":
	  								  //f_Mensaje(__FILE__,__LINE__,"Entre");
	  								  $qPucDes   = "SELECT *,CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) as cuentaxx ";
	  								  $qPucDes  .= "FROM $cAlfa.fpar0115 ";
	  								  $qPucDes  .= "WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"$cPucId\" AND ";
	  								  $qPucDes  .= "CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) IN ($cCueSel) AND ";
	  								  $qPucDes  .= "regestxx = \"ACTIVO\" ORDER BY ABS(cuentaxx)";
	  								  $xPucDes  = f_MySql("SELECT","",$qPucDes,$xConexion01,"");

	  									if ($xPucDes && mysql_num_rows($xPucDes) > 0) {
	  										while ($zRow = mysql_fetch_array($xPucDes)) { ?>
													<script languaje = "javascript">
	      	    							parent.fmwork.document.forms['frgrm']['<?php echo $gFunction ?>'].value  = '<?php echo $zRow['cuentaxx'] ?>';
														window.close();
													</script>
	      	      				<?php break;
	  										}
	  									} else {
	  									  //f_Mensaje(__FILE__,__LINE__,"Entre");
	  									  ?>
												<script languaje = "javascript">
													parent.fmwork.f_Links('<?php echo $gFunction ?>','WINDOW');
													window.close();
												</script>
	  									<?php }
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
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
			<title>Param&eacute;trica de Director de Cuenta</title>
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
	   	<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">

	  </head>
	  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
	  <center>
			<table border ="0" cellpadding="0" cellspacing="0" width="300">
				<tr>
					<td>
						<fieldset>
			   			<legend>Param&eacute;trica de Director de Cuenta</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gWhat) {
	  								case "WINDOW":
	  								  $qConCob  = "SELECT pucidxxx,ctoidxxx,regestxx, ";
	  									$qConCob .= "IF(ctodesxx <> \"\",ctodesxx,IF(ctodesxp <> \"\",ctodesxp,IF(ctodesxg <> \"\",ctodesxg,IF(ctodesxr <> \"\",ctodesxr,IF(ctodesxl <> \"\",ctodesxl,IF(ctodesxf <> \"\",ctodesxf,IF(ctodesxm <> \"\",ctodesxm,\"CONCEPTO SIN DESCRIPCION\"))))))) AS ctodesxx ";
	  									$qConCob .= "FROM $cAlfa.fpar0119 ";
	  									$qConCob .= "WHERE ";
	  									switch ($gFunction) {
	  										case "cCotId":
    	  									$qConCob .= "ctoidxxx LIKE \"%$gCotId%\" AND ";
    	  								break;
    	  								case "cCotDes":
    	  									$qConCob .= "IF(ctodesxx <> \"\",ctodesxx,IF(ctodesxp <> \"\",ctodesxp,IF(ctodesxg <> \"\",ctodesxg,IF(ctodesxr <> \"\",ctodesxr,IF(ctodesxl <> \"\",ctodesxl,IF(ctodesxf <> \"\",ctodesxf,IF(ctodesxm <> \"\",ctodesxm,\"CONCEPTO SIN DESCRIPCION\"))))))) LIKE \"%$gCotDes%\" AND ";
    	  								break;
	  									}
	  									$qConCob .= "ctopccxx = \"SI\" AND ";
    	  							$qConCob .= "regestxx = \"ACTIVO\" ORDER BY ctodesxx";
    	  							$xConCob = f_MySql("SELECT","",$qConCob,$xConexion01,"");
                      //f_Mensaje(__FILE__,__LINE__,$qConCob);
	  									if (mysql_num_rows($xConCob) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
														<tr>
															<td widht = "060" Class = "name"><center>Id</center></td>
															<td widht = "350" Class = "name"><center>Concepto</center></td>
															<td widht = "050" Class = "name"><center>Estado</center></td>
														</tr>
														<?php while ($zRow = mysql_fetch_array($xConCob)) {
															if (mysql_num_rows($xConCob) > 1) { ?>
																<tr>
																	<td width = "060" class= "name">
																		<a href = "javascript:
															        switch ('<?php echo $gFunction ?>') {
									                     case 'cCotId':
						                           case 'cCotDes':
    																		window.opener.document.forms['frgrm']['cCotId'].value  = '<?php echo $zRow['ctoidxxx'] ?>';
    																		window.opener.document.forms['frgrm']['cCotDes'].value = '<?php echo $zRow['ctodesxx'] ?>';
    																		window.opener.document.forms['frgrm']['cPucId'].value  = '<?php echo $zRow['pucidxxx'] ?>';
    																	 break;
															        }
															        window.close()"><?php echo $zRow['ctoidxxx'] ?></a>
																	</td>
																	<td width = "350" class= "name"><?php echo $zRow['ctodesxx'] ?>&nbsp;</td>
																	<td width = "050" class= "name"><?php echo $zRow['regestxx'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje = "javascript">
																switch ("<?php echo $gFunction ?>") {
        												    case "cCotId":
        												    case "cCotDes":
																	window.opener.document.forms['frgrm']['cCotId'].value   = "<?php echo $zRow['ctoidxxx'] ?>";
																	window.opener.document.forms['frgrm']['cCotDes'].value  = "<?php echo $zRow['ctodesxx'] ?>";
																	window.opener.document.forms['frgrm']['cPucId'].value   = '<?php echo $zRow['pucidxxx'] ?>';
																}
																	window.close();
																</script>
															<?php }
														} ?>
													</table>
												</center>
	  									<?php	} else {
	  										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");?>
												<script languaje = "javascript">
												switch ("<?php echo $gFunction ?>") {
												    case "cCotId":
												    case "cCotDes":
													window.opener.document.forms['frgrm']['cCotId'].value   = "";
													window.opener.document.forms['frgrm']['cCotDes'].value  = "";
													window.opener.document.forms['frgrm']['cPucId'].value  = "";
												}
													window.close();
												</script>
											<?php
	  									}
	  								break;
	  								case "VALID":
	  									$qConCob  = "SELECT pucidxxx,ctoidxxx,regestxx, ";
	  									$qConCob .= "IF(ctodesxx <> \"\",ctodesxx,IF(ctodesxp <> \"\",ctodesxp,IF(ctodesxg <> \"\",ctodesxg,IF(ctodesxr <> \"\",ctodesxr,IF(ctodesxl <> \"\",ctodesxl,IF(ctodesxf <> \"\",ctodesxf,IF(ctodesxm <> \"\",ctodesxm,\"CONCEPTO SIN DESCRIPCION\"))))))) AS ctodesxx ";
	  									$qConCob .= "FROM $cAlfa.fpar0119 ";
	  									$qConCob .= "WHERE ";
	  									switch ($gFunction) {
	  										case "cCotId":
    	  									$qConCob .= "ctoidxxx = \"$gCotId\" AND ";
    	  								break;
    	  								case "cCotDes":
    	  									$qConCob .= "IF(ctodesxx <> \"\",ctodesxx,IF(ctodesxp <> \"\",ctodesxp,IF(ctodesxg <> \"\",ctodesxg,IF(ctodesxr <> \"\",ctodesxr,IF(ctodesxl <> \"\",ctodesxl,IF(ctodesxf <> \"\",ctodesxf,IF(ctodesxm <> \"\",ctodesxm,\"CONCEPTO SIN DESCRIPCION\"))))))) = \"$gCotDes\" AND ";
    	  								break;
	  									}
	  									$qConCob .= "ctopccxx = \"SI\" AND ";
    	  							$qConCob .= "regestxx = \"ACTIVO\" ORDER BY ctodesxx";
    	  							//f_Mensaje(__FILE__,__LINE__,$qConCob);

	  									if (mysql_num_rows($xConCob) > 0) {
	  										while ($zRow = mysql_fetch_array($xConCob)) { ?>
  												<script languaje = "javascript">
  												 switch ("<?php echo $gFunction ?>") {
												    case "cCotId":
												    case "cCotDes":
  														parent.fmwork.document.forms['frgrm']['cCotId'].value   = "<?php echo $zRow['ctoidxxx'] ?>";
  														parent.fmwork.document.forms['frgrm']['cCotDes'].value  = "<?php echo $zRow['ctodesxx'] ?>";
  														parent.fmwork.document.forms['frgrm']['cPucId'].value   = '<?php echo $zRow['pucidxxx'] ?>';
  													break;
  												 }
													</script>
												<?php }
	  									} else { ?>
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
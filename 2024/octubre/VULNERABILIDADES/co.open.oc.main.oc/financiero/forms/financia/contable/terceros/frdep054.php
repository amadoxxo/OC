<?php
  namespace openComex;
	include("../../../../libs/php/utility.php");
?>
<?php if ($gWhat != "" && $gFunction != "") { ?>
	<html>
		<head>
			<title>Parametrica Departamentos</title>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
		</script>
	  </head>
	  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>

	  <center>
			<table border ="0" cellpadding="0" cellspacing="0" width="300">
				<tr>
					<td>
						<fieldset>
			   			<legend>Param&eacute;trica Departamentos</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  						switch ($gWhat) {
	  								case "WINDOW":

	  									$qDepDes  = "SELECT * FROM $cAlfa.SIAI0054 WHERE PAIIDXXX = \"$cPaiId\" AND DEPIDXXX LIKE \"%$cDepId%\" AND REGESTXX = \"ACTIVO\" ";
	  									$xDepDes = f_MySql("SELECT","",$qDepDes,$xConexion01,"");
	  									//f_Mensaje(__FILE__,__LINE__,$qDepDes."window" );



	  									if ($xDepDes && mysql_num_rows($xDepDes) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
														<tr>
															<td widht = "050" Class = "name"><center>ID</center></td>
															<td widht = "400" Class = "name"><center>DEPARTAMENTO</center></td>
															<td widht = "050" Class = "name"><center>ESTADO</center></td>
														</tr>
														<?php while ($xDD = mysql_fetch_array($xDepDes)) {
															if (mysql_num_rows($xDepDes) > 1) { ?>
																<tr>
																<?php
														    switch ($gFunction) {
  														    case "cDepId":
      														  ?>
      																	<td width = "050" class= "name">
      																		<a href = "javascript:window.opener.document.forms['frgrm']['cDepId'].value  ='<?php echo $xDD['DEPIDXXX']?>';
      																													window.opener.document.forms['frgrm']['cDepDes'].value ='<?php echo $xDD['DEPDESXX']?>';
      																													close()"><?php echo $xDD['DEPIDXXX'] ?></a></td>
      																	<td width = "400" class= "name"> <?php echo $xDD['DEPDESXX'] ?></td>
      																	<td width = "050" class= "name"> <?php echo $xDD['REGESTXX'] ?></td>
      																</tr>
      															<?php
															    break;
															    case "cDepId1":
      														  ?>
      																	<td width = "050" class= "name">
      																		<a href = "javascript:window.opener.document.forms['frgrm']['cDepId1'].value  ='<?php echo $xDD['DEPIDXXX']?>';
      																													window.opener.document.forms['frgrm']['cDepDes1'].value ='<?php echo $xDD['DEPDESXX']?>';
      																													close()"><?php echo $xDD['DEPIDXXX'] ?></a></td>
      																	<td width = "400" class= "name"> <?php echo $xDD['DEPDESXX'] ?></td>
      																	<td width = "050" class= "name"> <?php echo $xDD['REGESTXX'] ?></td>
      																</tr>
      															<?php
															    break;
														    }
														   } else {
														        switch ($gFunction) {
  														        case "cDepId":
          														   ?>
          																<script languaje="javascript">
          																	window.opener.document.forms['frgrm']['cDepId'].value  = '<?php echo $xDD['DEPIDXXX'] ?>';
          																	window.opener.document.forms['frgrm']['cDepDes'].value = '<?php echo $xDD['DEPDESXX'] ?>';
          																	close();
          																</script>
        															 <?php
        															 break;
        															 case "cDepId1":
          														   ?>
          																<script languaje="javascript">
          																	window.opener.document.forms['frgrm']['cDepId1'].value  = '<?php echo $xDD['DEPIDXXX'] ?>';
          																	window.opener.document.forms['frgrm']['cDepDes1'].value = '<?php echo $xDD['DEPDESXX'] ?>';
          																	close();
          																</script>
        															   <?php
        															 break;
														         }
														      }
														} ?>
													</table>
												</center>
	  									<?php	} else {
	  										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");
	  									}
	  								break;

	  								case "VALID":

											$qDepDes  = "SELECT * FROM $cAlfa.SIAI0054 WHERE PAIIDXXX = \"$cPaiId\" AND DEPIDXXX LIKE \"%$cDepId%\" AND REGESTXX = \"ACTIVO\" ";
	  									$xDepDes = f_MySql("SELECT","",$qDepDes,$xConexion01,"");
	  									//f_Mensaje(__FILE__,__LINE__,$qDepDes."valid" );


	  									if ($xDepDes && mysql_num_rows($xDepDes) > 0) {
	  										while ($xDD = mysql_fetch_array($xDepDes)) {
	  										  switch ($gFunction) {
  													case "cDepId":
      	  										?>
      													<script languaje = "javascript">
      														parent.fmwork.document.forms['frgrm']['cDepId'].value  = '<?php echo $xDD['DEPIDXXX'] ?>';
      														parent.fmwork.document.forms['frgrm']['cDepDes'].value = '<?php echo $xDD['DEPDESXX'] ?>';
      														close();
      													</script>
      	      	      				<?php
    	      	      				break;
    	      	      				case "cDepId1":
      	  										?>
      													<script languaje = "javascript">
      														parent.fmwork.document.forms['frgrm']['cDepId1'].value  = '<?php echo $xDD['DEPIDXXX'] ?>';
      														parent.fmwork.document.forms['frgrm']['cDepDes1'].value = '<?php echo $xDD['DEPDESXX'] ?>';
      														close();
      													</script>
      	      	      				<?php
    	      	      				break;
	  										   }
	  										}
	  									} else { ?>
												<script languaje = "javascript">
												  //parent.fmwork.document.forms['frgrm']['cDepId'].value  = "";
													parent.fmwork.f_Links('<?php echo $gFunction ?>','WINDOW');
													close();
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
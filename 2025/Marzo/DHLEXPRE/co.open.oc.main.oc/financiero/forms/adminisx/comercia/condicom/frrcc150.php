<!--
	Los Parametros que Recibo son :
	$gSearch = Un dato para filtrar la consulta con el WHERE
	$gFields = A que campos del formulario voy a devolver los datos
	$gWhat   = Que voy a hacer, una ventana o una validacion
 */ -->

<?php
  include("../../../../libs/php/utility.php");

  if ($gWhat != "" && $gFunction != "") { ?>
	<html>
		<head>
			<title>Parametrica de Clientes por Nit</title>
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
			   			<legend>Parametrica de Clientes</legend>
	  					<form name = "frnav" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gWhat) {
	  								case "WINDOW":
	  									$qCliDat  = "SELECT ";
                      $qCliDat .= "CLIIDXXX,";
                      $qCliDat .= "CONCAT(CLINOMXX,\" \",CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X) AS CLINOMXX,";
                      $qCliDat .= "REGESTXX ";
                      $qCliDat .= "FROM $cAlfa.SIAI0150 ";
                      $qCliDat .= "WHERE ";
                      $qCliDat .= "CLIIDXXX LIKE \"%$gCliId%\" ";
                      switch ($gFunction) {
	  										case "cCliId":
                          //No hace nada
                        break;
	  										case "cCliIdFac":
                          //No hace nada
                        break;
	  										case "cCliIdCom":
                          $qCliDat .= "AND CLIVENCO = \"SI\" ";
                        break;
                      }
                      if ($vSysStr['financiero_ver_terceros_inactivos_reportes'] != "SI") {
												$qCliDat .= "AND REGESTXX = \"ACTIVO\" ";
                      }
                      $qCliDat .= "ORDER BY CLINOMXX ";
                      $xCliDat = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
											// f_Mensaje(__FILE__,__LINE__,$qCliDat."~".mysql_num_rows($xCliDat));
	  									if ($xCliDat && mysql_num_rows($xCliDat) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
														<tr>
															<td widht = "050" Class = "name"><center>NIT</center></td>
															<td widht = "400" Class = "name"><center>NOMBRE</center></td>
															<td widht = "050" Class = "name"><center>ESTADO</center></td>
														</tr>
														<?php while ($xRCD = mysql_fetch_array($xCliDat)) {
															if (mysql_num_rows($xCliDat) > 1) {
                                switch ($gFunction) {
                                  case "cCliId":
                                    ?>
    																<tr>
    																	<td width = "050" class= "name">
    																		<a href = "javascript:window.opener.document.forms['frnav']['cCliId'].value    ='<?php echo $xRCD['CLIIDXXX']?>';
    																                          window.opener.document.forms['frnav']['cCliNom'].value   ='<?php echo $xRCD['CLINOMXX'] ?>';
    																                          window.opener.document.forms['frnav']['cCliDv'].value    ='<?php echo f_Digito_Verificacion($xRCD['CLIIDXXX'])?>';
    																													window.close()"><?php echo $xRCD['CLIIDXXX'] ?></a></td>
    																	<td width = "400" class= "name"> <?php echo $xRCD['CLINOMXX'] ?></td>
    																	<td width = "050" class= "name"> <?php echo $xRCD['REGESTXX'] ?></td>
    																</tr>
                                    <?php
                                  break;
                                  case "cCliIdFac":
                                    ?>
                                    <tr>
                                      <td width = "050" class= "name">
                                        <a href = "javascript:window.opener.document.forms['frnav']['cCliIdFac'].value    ='<?php echo $xRCD['CLIIDXXX']?>';
                                                              window.opener.document.forms['frnav']['cCliNomFac'].value   ='<?php echo $xRCD['CLINOMXX'] ?>';
                                                              window.opener.document.forms['frnav']['cCliDvFac'].value    ='<?php echo f_Digito_Verificacion($xRCD['CLIIDXXX'])?>';
                                                              window.close()"><?php echo $xRCD['CLIIDXXX'] ?></a></td>
                                      <td width = "400" class= "name"> <?php echo $xRCD['CLINOMXX'] ?></td>
                                      <td width = "050" class= "name"> <?php echo $xRCD['REGESTXX'] ?></td>
                                    </tr>
                                    <?php
                                  break;
                                  case "cCliIdCom":
                                    ?>
                                    <tr>
                                      <td width = "050" class= "name">
                                        <a href = "javascript:window.opener.document.forms['frnav']['cCliIdCom'].value    ='<?php echo $xRCD['CLIIDXXX']?>';
                                                              window.opener.document.forms['frnav']['cCliNomCom'].value   ='<?php echo $xRCD['CLINOMXX'] ?>';
                                                              window.opener.document.forms['frnav']['cCliDvCom'].value    ='<?php echo f_Digito_Verificacion($xRCD['CLIIDXXX'])?>';
                                                              window.close()"><?php echo $xRCD['CLIIDXXX'] ?></a></td>
                                      <td width = "400" class= "name"> <?php echo $xRCD['CLINOMXX'] ?></td>
                                      <td width = "050" class= "name"> <?php echo $xRCD['REGESTXX'] ?></td>
                                    </tr>
                                    <?php
                                  break;
                                }
                              } else {
                                switch ($gFunction) {
                                  case "cCliId":
                                    ?>
      															<script languaje="javascript">
      																window.opener.document.forms['frnav']['cCliId'].value     = '<?php echo $xRCD['CLIIDXXX'] ?>';
      																window.opener.document.forms['frnav']['cCliNom'].value    = '<?php echo $xRCD['CLINOMXX'] ?>';
      																window.opener.document.forms['frnav']['cCliDv'].value     = '<?php echo f_Digito_Verificacion($xRCD['CLIIDXXX'])?>';
      																window.close();
      															</script>
                                    <?php
                                  break;
                                  case "cCliIdFac":
                                    ?>
                                    <script languaje="javascript">
                                      window.opener.document.forms['frnav']['cCliIdFac'].value     = '<?php echo $xRCD['CLIIDXXX'] ?>';
                                      window.opener.document.forms['frnav']['cCliNomFac'].value    = '<?php echo $xRCD['CLINOMXX'] ?>';
                                      window.opener.document.forms['frnav']['cCliDvFac'].value     = '<?php echo f_Digito_Verificacion($xRCD['CLIIDXXX'])?>';
                                      window.close();
                                    </script>
                                    <?php
                                  break;
                                  case "cCliIdCom":
                                    ?>
                                    <script languaje="javascript">
                                      window.opener.document.forms['frnav']['cCliIdCom'].value     = '<?php echo $xRCD['CLIIDXXX'] ?>';
                                      window.opener.document.forms['frnav']['cCliNomCom'].value    = '<?php echo $xRCD['CLINOMXX'] ?>';
                                      window.opener.document.forms['frnav']['cCliDvCom'].value     = '<?php echo f_Digito_Verificacion($xRCD['CLIIDXXX'])?>';
                                      window.close();
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
                        switch ($gFunction) {
                          case "cCliId":
                            ?>
                            <script languaje="javascript">
                              window.opener.document.forms['frnav']['cCliId'].value   = '';
                              window.opener.document.forms['frnav']['cCliNom'].value  = '';
                              window.opener.document.forms['frnav']['cCliDv'].value   = '';
                              window.close();
                            </script>
                            <?php
                          break;
                          case "cCliIdFac":
                            ?>
                            <script languaje="javascript">
                              window.opener.document.forms['frnav']['cCliIdFac'].value  = '';
                              window.opener.document.forms['frnav']['cCliNomFac'].value = '';
                              window.opener.document.forms['frnav']['cCliDvFac'].value  = '';
                              window.close();
                            </script>
                            <?php
                          break;
                          case "cCliIdCom":
                            ?>
                            <script languaje="javascript">
                              window.opener.document.forms['frnav']['cCliIdCom'].value  = '';
                              window.opener.document.forms['frnav']['cCliNomCom'].value = '';
                              window.opener.document.forms['frnav']['cCliDvCom'].value  = '';
                              window.close();
                            </script>
                            <?php
                          break;
                        }
	  									}
	  								break;
	  								case "VALID":
                      $qCliDat  = "SELECT ";
                      $qCliDat .= "CLIIDXXX,";
                      $qCliDat .= "CONCAT(CLINOMXX,\" \",CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X) AS CLINOMXX,";
                      $qCliDat .= "REGESTXX ";
                      $qCliDat .= "FROM $cAlfa.SIAI0150 ";
                      $qCliDat .= "WHERE ";
                      $qCliDat .= "CLIIDXXX = \"$gCliId\" ";
                      switch ($gFunction) {
	  										case "cCliId":
                          //No hace nada
                        break;
	  										case "cCliIdFac":
                          //No hace nada
                        break;
	  										case "cCliIdCom":
                          $qCliDat .= "AND CLIVENCO = \"SI\" ";
                        break;
                      }
                      if ($vSysStr['financiero_ver_terceros_inactivos_reportes'] != "SI") {
												$qCliDat .= "AND REGESTXX = \"ACTIVO\" ";
                      }
                      $qCliDat .= "ORDER BY CLINOMXX ";
	  									$xCliDat = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
											// f_Mensaje(__FILE__,__LINE__,$qCliDat."~".mysql_num_rows($xCliDat));
	  									if ($xCliDat && mysql_num_rows($xCliDat) > 0) {
	  										while ($xRCD = mysql_fetch_array($xCliDat)) {
                          switch ($gFunction) {
                            case "cCliId":
                              ?>
                              <script languaje = "javascript">
                                parent.fmwork.document.forms['frnav']['cCliId'].value  		= '<?php echo $xRCD['CLIIDXXX'] ?>';
                                parent.fmwork.document.forms['frnav']['cCliNom'].value 		= '<?php echo $xRCD['CLINOMXX'] ?>';
                                parent.fmwork.document.forms['frnav']['cCliDv'].value  		= '<?php echo f_Digito_Verificacion($xRCD['CLIIDXXX'])?>';
                              </script>
                              <?php
                            break;
                            case "cCliIdFac":
                              ?>
                              <script languaje = "javascript">
                                parent.fmwork.document.forms['frnav']['cCliIdFac'].value   = '<?php echo $xRCD['CLIIDXXX'] ?>';
                                parent.fmwork.document.forms['frnav']['cCliNomFac'].value  = '<?php echo $xRCD['CLINOMXX'] ?>';
                                parent.fmwork.document.forms['frnav']['cCliDvFac'].value   = '<?php echo f_Digito_Verificacion($xRCD['CLIIDXXX'])?>';
                              </script>
                              <?php
                            break;
                            case "cCliIdCom":
                              ?>
                              <script languaje = "javascript">
                                parent.fmwork.document.forms['frnav']['cCliIdCom'].value   = '<?php echo $xRCD['CLIIDXXX'] ?>';
                                parent.fmwork.document.forms['frnav']['cCliNomCom'].value  = '<?php echo $xRCD['CLINOMXX'] ?>';
                                parent.fmwork.document.forms['frnav']['cCliDvCom'].value   = '<?php echo f_Digito_Verificacion($xRCD['CLIIDXXX'])?>';
                              </script>
                              <?php
                            break;
                          }
                          break;
	  										}
	  									} else { ?>
												<script languaje = "javascript">
													parent.fmwork.f_Links('<?php echo $gFunction ?>','WINDOW');
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

<?php
  namespace openComex;
	/**
    * Window Parametrica de Do's de Importacion
    * Parametros:
    * $gWhat = Modo
    * $gFuncion = cDocId - cDocTra - cDoiPed
    *
    */
	include("../../../../libs/php/utility.php"); ?>
	<script languaje="javascript">
		function f_Datos_Comprobante(xComId,xComCod,xComCsc,xComCsc2,xComFec,xRegEst,xRegFCre,xTipCom){
    	document.cookie="kModo=VER;path="+"/";
    	document.cookie="kIniAnt=frdetprn.php;path="+"/";
    	switch(xComId){
    		case "R":
    	  	var zRuta   = '../../../../forms/financia/contable/recibosc/frrecnue.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gComFec='+xComFec;
    	  break;
    	  case 'L':
    	  	if(xTipCom == 'PAGOIMPUESTOS'){
    	    	var zRuta   = '../../../../forms/financia/contable/carbcoxx/frcbanue.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gComFec='+xComFec;
    	    } else {
    	    	var zRuta   = '../../../../forms/financia/contable/ajustesx/frajunue.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gComFec='+xComFec;
    	    }
    	  break;
    	  case 'P':
    	  	if(xTipCom == 'CPE'){
    	    	var zRuta   = '../../../../forms/financia/contable/comprase/frcpenue.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gComFec='+xComFec;
    	    } else if(xTipCom == 'RCM'){
    	    	var zRuta   = '../../../../forms/financia/contable/cajameno/frcamcau.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gRegFCre='+xComFec;
    	    } else {
    	    	var zRuta   = '../../../../forms/financia/contable/comprast/frcomnue.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gComFec='+xComFec;
    	    }
    	  break;
    	  case 'C':
    	  	if(xTipCom == 'AJUSTE'){
    	    	var zRuta   = '../../../../forms/financia/contable/ajustesx/frajunue.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gComFec='+xComFec;
    	    } else {
						if('<?php echo $vSysStr['system_activar_openetl'] ?>' == 'SI'){
							var zRuta   = '../../../../forms/financia/contable/notacrev/frnocnue.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gComFec='+xComFec;
						} else {
							var zRuta   = '../../../../forms/financia/contable/notacrex/frnocnue.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gComFec='+xComFec;
						}
    	    }
    	  break;
    	  case 'G':
    	  	var zRuta   = '../../../../forms/financia/contable/egresosx/fregrnue.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gComFec='+xComFec;
    	  break;
				case 'D':
          if(xTipCom == 'AJUSTE'){
            var zRuta   = '../../../../forms/financia/contable/ajustesx/frajunue.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gComFec='+xComFec;
    	    } else {
						var zRuta   = '../../../../forms/financia/contable/notadebv/frnodnue.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gComFec='+xComFec;
    	    }
				break;
    	  case 'N':
    	  	var zRuta   = '../../../../forms/financia/contable/ajustesx/frajunue.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gComFec='+xComFec;
    	  break;
    	  case 'F':
    	  	var zRuta   = '../../../../forms/financia/contable/facturax/frfacnue.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gRegFCre='+xRegFCre;
    	  break;
    	}

    	var zX    = screen.width;
    	var zY    = screen.height;
    	var zNx     = (zX-1200)/2;
    	var zNy     = (zY-800)/2;
    	window.moveTo(zNx,zNy);
    	window.resizeTo('1200','800');
			window.location=zRuta;
    }
	</script>
	<html>
		<head>
			<title>Comprobantes Contables</title>
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
	   	<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
	  </head>
	  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
		<?php
		$mDatos = array();
	  if ($xComFec == "") {
			f_Mensaje(__FILE__,__LINE__,"La Fecha del Comprobante No Es Valida.");
		} else {
			$nAno = substr($xComFec, 0, 4);
	  	$qEstPro = "SELECT $cAlfa.fcoc$nAno.* ";
	  	$qEstPro .= "FROM $cAlfa.fcoc$nAno ";
	  	$qEstPro .= "WHERE ";
	  	$qEstPro .= "$cAlfa.fcoc$nAno.comidxxx= \"$xComId\" AND ";
	  	$qEstPro .= "$cAlfa.fcoc$nAno.comcodxx= \"$xComCod\" AND ";
	  	$qEstPro .= "$cAlfa.fcoc$nAno.comcscxx= \"$xComCsc\" ";
	  	$xEstPro = f_MySql("SELECT","",$qEstPro,$xConexion01,"");
	  	if (mysql_num_rows($xEstPro) > 0) {
	  		while ($xREP = mysql_fetch_array($xEstPro)) {
	  			$mDatos[count($mDatos)] = $xREP;
	  		}
	  	}
		}

 		if (count($mDatos) > 0) {
			if (count($mDatos) > 1) { ?>
				<center>
					<table border ="0" cellpadding="0" cellspacing="0" width="300">
						<tr>
							<td>
								<fieldset>
									<legend>Comprobantes Contables</legend>
							  	<form name = "frnav" action = "" method = "post" target = "fmpro">
					  				<center>
					  					<table cellspacing = "0" cellpadding = "1" border = "1" width = "300">
					  						<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
					  							<td widht = "150" Class = "name"><center>comcsc2x</center></td>
					  							<td widht = "150" Class = "name"><center>regfcrex</center></td>
					  						</tr>
												<?php for ($i = 0; $i < count($mDatos); $i++) {?>
													<tr>
														<td width = "150" class= "name"><?php echo ($mDatos[$i]['comcsc2x'] != "")?"<a href=\"javascript:f_Datos_Comprobante('$xComId','$xComCod','$xComCsc','{$mDatos[$i]['comcsc2x']}','$xComFec','$xRegEst','{$mDatos[$i]['regfcrex']}','$xTipCom');\">$xComId-$xComCod-$xComCsc-{$mDatos[$i]['comcsc2x']}</a>": "&nbsp;"; ?></td>
														<td width = "150" class = "name"><center><?php echo $mDatos[$i]['regfcrex'] ?></center></td>
													</tr>
												<?php } ?>
											</table>
										</center>
									</form>
								</fieldset>
							</td>
						</tr>
					</table>
				</center>
			<?php  } else { ?>
				<script languaje="javascript">
					f_Datos_Comprobante('<? echo $xComId ?>','<? echo $xComCod ?>','<? echo $xComCsc ?>','<? echo $mDatos[0]['comcsc2x'] ?>','<? echo $xComFec ?>','<? echo $xRegEst ?>','<? echo $mDatos[0]['regfcrex'] ?>','<? echo $xTipCom ?>');
				</script>
			<?php }
		} else {
	  	f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros"); ?>
  		<script languaje="javascript">
  			window.close();
  		</script>
  	<?php } ?>
	</body>
</html>

<?php 
  namespace openComex;
	include("../../../../libs/php/utility.php");
	include("../../../../libs/php/utifcsia.php");
	
	switch($rTipo){
		case "1":
			?>
			<html>
				<head>
					<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/estilo.css'>
          <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/general.css'>
          <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/layout.css'>
          <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/custom.css'>
          <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New ?>/date_picker.js'></script>
          <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New ?>/utility.js'></script>
          <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New ?>/ajax.js'></script>
          <link rel="stylesheet" type="text/css" href="../../../../../programs/gwtext/resources/css/ext-all.css">
          <script type="text/javascript" src="../../../../../programs/gwtext/adapter/ext/ext-base.js"></script>
          <script type="text/javascript" src="../../../../../programs/gwtext/ext-all.js"></script>
          <script language="JavaScript" src="../../../../../programs/gwtext/conexijs/loading/loading.js"></script>	
					<script language="javascript">
						function salir(){
							window.close();
						}
					</script>	
				</head>
				<body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0" onLoad="init();">
        	<script> 
          uLoad();
          var ld=(document.all);  
          var ns4=document.layers; 
          var ns6=document.getElementById&&!document.all; 
          var ie4=document.all;  
                
          function init() { 
          	if(ns4){ld.visibility="hidden";} 
            else if (ns6||ie4) {
           		Ext.MessageBox.updateProgress(1,'100% completed');
              Ext.MessageBox.hide();
            }
          } 
        </script>
        
      <?php
      ob_flush();
    	flush(); 
		break;
	}
	
	$vFiltros = array();
	
	/**
	 * Switch para Vericar la Validacion de Datos
	 */
	$nSwitch = 0;
	
	/**
	 * Variable para guardar los mensajes de error
	 */
	$cMsj = ""; 
	
	/**
	 * Validando que el Rango de Fechas no se vacio
	 */
	if($_POST['dFecIni'] == "" || $_POST['dFecFin'] == ""){
		$nSwitch = 1;
		$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
		$cMsj .= "La Fecha de Levante De o Hasta No Pueden Ser Vacios. \n";
	}
	
	/**
	 * Validando que exista el Importador
	 */
	if($_POST['cCliId'] != ""){
		$qDatImp  = "SELECT CLIIDXXX, ";
		$qDatImp .= "IF(CLINOMCX <> \"\",CLINOMCX,IF(CLINOMXX <> \"\",CLINOMXX,CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X))) AS CLINOMXX ";
		$qDatImp .= "FROM $cAlfa.SIAI0150 ";
		$qDatImp .= "WHERE ";
		$qDatImp .= "CLIIDXXX = \"{$_POST['cCliId']}\" AND ";
		$qDatImp .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
		$xDatImp  = f_MySql("SELECT","",$qDatImp,$xConexion01,"");
		$vDatImp = mysql_fetch_array($xDatImp);
		
		if (mysql_num_rows($xDatImp) == 0) {
			$nSwitch = 1;
			$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			$cMsj .= "No Existe el Importador[{$_POST['cCliId']}] en la Base de Datos. ";
		} 
	}
		
	/**
	 * Validando que Exista la Sucursal
	 */
	if($_POST['cSucId'] != ""){
		$qDatSuc  = "SELECT * ";
		$qDatSuc .= "FROM $cAlfa.SIAI0119 ";
		$qDatSuc .= "WHERE ";
		$qDatSuc .= "LINIDXXX = \"{$_POST['cSucId']}\" AND ";
		$qDatSuc .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
		$xDatSuc  = f_MySql("SELECT","",$qDatSuc,$xConexion01,"");
		$vDatSuc = mysql_fetch_array($xDatSuc);
		if (mysql_num_rows($xDatSuc) == 0) {
			$nSwitch = 1;
			$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			$cMsj .= "No Existe la Sucursal[{$_POST['cSucId']}] en la Base de Datos. \n";
		}
	}
							
	/**
	* Validando que exista el Tipo de Bien
	*/
	if($_POST['cTdbId'] != ""){
		$qDatTipBie  = "SELECT * ";
	 	$qDatTipBie .= "FROM $cAlfa.SIAI0231 ";
	 	$qDatTipBie .= "WHERE ";
	 	$qDatTipBie .= "TDBIDXXX = \"{$_POST['cTdbId']}\" AND ";
	 	$qDatTipBie .= "CLIIDXXX = \"{$_POST['cCliId']}\" AND ";
	 	$qDatTipBie .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
	 	$xDatTipBie  = f_MySql("SELECT","",$qDatTipBie,$xConexion01,"");
		$vDatTipBie = mysql_fetch_array($xDatTipBie);
	 	if (mysql_num_rows($xDatTipBie) == 0) {
			$nSwitch = 1;
			$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			$cMsj .= "No Existe el Tipo de Bien[{$_POST['cTdbId']}] en la Base de Datos. \n";
	 	}
 	}
						 	
	/**
	* Validando que Exista el Modo de Transporte
	*/
	if($_POST['cMtrId'] != ""){
		$qDatModTra  = "SELECT * ";
		$qDatModTra .= "FROM $cAlfa.SIAI0120 ";
		$qDatModTra .= "WHERE ";
		$qDatModTra .= "MTRIDXXX = \"{$_POST['cMtrId']}\" AND ";
		$qDatModTra .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
		$xDatModTra  = f_MySql("SELECT","",$qDatModTra,$xConexion01,"");
		$vDatModTra = mysql_fetch_array($xDatModTra);
		if (mysql_num_rows($xDatModTra) == 0) {
			$nSwitch = 1;
			$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			$cMsj .= "No Existe el Modo de Transporte[{$_POST['cMtrId']}] en la Base de Datos. \n";
		}
	}
	
	if($nSwitch == 0){
		/**
		 * Carga el Vector con los filtros
		 */
		$vFiltros['CLIIDXXX'] = $_POST['cCliId'];
		$vFiltros['LINIDXXX'] = $_POST['cSucId'];
		$vFiltros['TDBIDXXX'] = $_POST['cTdbId'];
		$vFiltros['MTRIDXXX'] = $_POST['cMtrId'];
		$vFiltros['DOILEVDE'] = $_POST['dFecIni'];
		$vFiltros['DOILEVHA'] = $_POST['dFecFin'];
		
		$utifcsia = new cReportes();
		$mReturnReporte = $utifcsia->fnReporteNestlexItem($vFiltros);
		if($mReturnReporte[0] == "true"){
			$mReporteNestlexItem = $mReturnReporte[1];
		}else{
			$nSwitch = 1;
			for($nR=1;$nR<count($mReturnReporte);$nR++){
				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= $mReturnReporte[$nR]."\n";
			}
		}
	}
	
	switch($rTipo){
		case "1":  ?>
			<center>
			<!--Filtros de las columnas-->
				<table border="1" cellspacing="0" cellpadding="0" width="98%" align=center style="margin:5px">
        	<tr bgcolor = "white" height="20">
          	<td colspan="12" align="left" style="padding-left:5px;padding-top:5px">
            	<font size="3">
              	<b>REPORTE NESTLE</b><br>
             </font>
            	<b>Rango de Fechas (Levante): </b><?php echo "Desde {$_POST['dFecIni']} Hasta {$_POST['dFecFin']}"; ?><br>
            	<?php if($_POST['cCliId'] != ""){ ?>
            		<b>Importador: </b><?php echo "{$vDatImp['CLINOMXX']} [{$vDatImp['CLIIDXXX']}]"; ?><br>
            	<?php } ?>
            	<?php if($_POST['cSucId'] != ""){ ?>
            		<b>Sucursal: </b><?php echo "{$vDatSuc['LINDESXX']}"; ?><br>
            	<?php } ?>
            	<?php if($_POST['cTdbId'] != ""){ ?>
            		<b>Tipo de Bien: </b><?php echo "{$vDatTipBie['TDBDESXX']}"; ?><br>
            	<?php } ?>
            	<?php if($_POST['cMtrId'] != ""){ ?>
            		<b>Modo de Transporte: </b><?php echo "{$vDatModTra['MTRDESXX']}"; ?><br>
            	<?php } ?>
             </td>
           </tr>
       	</table>
      	<table border = 1 cellpadding = 0 cellspacing = 0 width = "98%">	
        	<tr bgcolor="#96ADEB"> 
						<td class = "name" align="center" bgcolor = "#96ADEB" style="padding-left:3px;padding-right:3px">IMPORTADOR</td>
						<td class = "name" align="center" bgcolor = "#96ADEB" style="padding-left:3px;padding-right:3px">ADMINISTRACION</td>
						<td class = "name" align="center" bgcolor = "#96ADEB" style="padding-left:3px;padding-right:3px">DO</td>
						<td class = "name" align="center" bgcolor = "#96ADEB" style="padding-left:3px;padding-right:3px">MODO DE TRANSPORTE</td>
						<td class = "name" align="center" bgcolor = "#96ADEB" style="padding-left:3px;padding-right:3px">CODIGO PRODUCTO</td>
						<td class = "name" align="center" bgcolor = "#96ADEB" style="padding-left:3px;padding-right:3px">DESCRIPCION SEGUN FACTURA</td>
						<td class = "name" align="center" bgcolor = "#96ADEB" style="padding-left:3px;padding-right:3px">UNIDAD DE NEGOCIO</td>
						<td class = "name" align="center" bgcolor = "#96ADEB" style="padding-left:3px;padding-right:3px;width:120px">FECHA DE LEVANTE</td>
						<td class = "name" align="center" bgcolor = "#96ADEB" style="padding-left:3px;padding-right:3px;width:120px">PAGOS A TERCEROS</td>
						<td class = "name" align="center" bgcolor = "#96ADEB" style="padding-left:3px;padding-right:3px;width:120px">PESO</td>
						<td class = "name" align="center" bgcolor = "#96ADEB" style="padding-left:3px;padding-right:3px;width:120px">COSTO PROMEDIO X KILO</td>
					</tr>
					<?php if($nSwitch == 0){
						for($nR=0;$nR<count($mReporteNestlexItem);$nR++){
							if(($nR % 2) == 0){
								$bgcolor = "#E5E5E5";
							}else{
								$bgcolor = "#FFFFFF";
							} ?>
							<tr bgcolor="<?php echo $bgcolor?>"> 
								<td align="left"   style="padding-left:3px;padding-right:3px"><?php echo (($mReporteNestlexItem[$nR]['CLINOMXX'] != "") ? $mReporteNestlexItem[$nR]['CLINOMXX'] : "&nbsp;")?></td>
								<td align="center" style="padding-left:3px;padding-right:3px"><?php echo (($mReporteNestlexItem[$nR]['ADMIDXXX'] != "") ? $mReporteNestlexItem[$nR]['ADMIDXXX'] : "&nbsp;")?></td>
								<td align="center" style="padding-left:3px;padding-right:3px"><?php echo (($mReporteNestlexItem[$nR]['DOIIDXXX'] != "") ? $mReporteNestlexItem[$nR]['DOIIDXXX'] : "&nbsp;")?></td>
								<td align="center" style="padding-left:3px;padding-right:3px"><?php echo (($mReporteNestlexItem[$nR]['MTRDESXX'] != "") ? $mReporteNestlexItem[$nR]['MTRDESXX'] : "&nbsp;")?></td>
								<td align="center" style="padding-left:3px;padding-right:3px"><?php echo (($mReporteNestlexItem[$nR]['PROIDXXX'] != "") ? $mReporteNestlexItem[$nR]['PROIDXXX'] : "&nbsp;")?></td>
								<td align="left"   style="padding-left:3px;padding-right:3px"><?php echo (($mReporteNestlexItem[$nR]['PRODESFA'] != "") ? $mReporteNestlexItem[$nR]['PRODESFA'] : "&nbsp;")?></td>
								<td align="left"   style="padding-left:3px;padding-right:3px"><?php echo (($mReporteNestlexItem[$nR]['TDBDESXX'] != "") ? $mReporteNestlexItem[$nR]['TDBDESXX'] : "&nbsp;")?></td>
								<td align="center" style="padding-left:3px;padding-right:3px"><?php echo (($mReporteNestlexItem[$nR]['DOIMYLEV'] != "") ? $mReporteNestlexItem[$nR]['DOIMYLEV'] : "&nbsp;")?></td>
								<td align="right"  style="padding-left:3px;padding-right:3px"><?php echo (($mReporteNestlexItem[$nR]['PAGTERIT'] >  "") ? number_format($mReporteNestlexItem[$nR]['PAGTERIT'],2,',','') : "&nbsp;")?></td>
								<td align="right"  style="padding-left:3px;padding-right:3px"><?php echo (($mReporteNestlexItem[$nR]['LIMPNEXX'] >  "") ? number_format($mReporteNestlexItem[$nR]['LIMPNEXX'],2,',','') : "&nbsp;")?></td>
								<td align="right"  style="padding-left:3px;padding-right:3px"><?php echo (($mReporteNestlexItem[$nR]['COSTOITE'] >  "") ? number_format($mReporteNestlexItem[$nR]['COSTOITE'],2,',','') : "&nbsp;")?></td>
							</tr>
						<?php }
					} ?>
				</table>
			</center>
			</body>
			</html>
		<?php break;
		case "2":
			$cData  = "REPORTE NESTLE\n";
      $cData .= "Rango de Fechas (Levante): Desde {$_POST['dFecIni']} Hasta {$_POST['dFecFin']}\n";
      if($_POST['cCliId'] != ""){
      	$cData .= "Importador: [{$vDatImp['CLIIDXXX']}] {$vDatImp['CLINOMXX']}\n";
      }
      if($_POST['cSucId'] != ""){
      	$cData .= "Sucursal: [{$vDatSuc['LINIDXXX']}] {$vDatSuc['LINDESXX']}\n";
      }
      if($_POST['cTdbId'] != ""){
      	$cData .= "Tipo de Bien: [{$vDatTipBie['TDBIDXXX']}] {$vDatTipBie['TDBDESXX']}\n";
      }
      if($_POST['cMtrId'] != ""){
      	$cData .= "Modo de Transporte: [{$vDatTipBie['MTRIDXXX']}] {$vDatTipBie['MTRDESXX']}\n";
      }
			$cData .= "\n";
			$cData .= "IMPORTADOR"."\t";
			$cData .= "ADMINISTRACION"."\t";
			$cData .= "DO"."\t";
			$cData .= "MODO DE TRANSPORTE"."\t";
			$cData .= "CODIGO PRODUCTO"."\t";
			$cData .= "DESCRIPCION SEGUN FACTURA"."\t";
			$cData .= "UNIDAD DE NEGOCIO"."\t";
			$cData .= "FECHA DE LEVANTE"."\t";
			$cData .= "PAGOS A TERCEROS"."\t";
			$cData .= "PESO"."\t";
			$cData .= "COSTO PROMEDIO X KILO"."\n";
						
			if ($mReporteNestlexItem == 0) {
				//No se encontraron registros
				$cData .="No se Encontraron Registros.";	
			}
						
			for($nR=0;$nR<count($mReporteNestlexItem);$nR++){
				$cData .= $mReporteNestlexItem[$nR]['CLINOMXX']."\t";
				$cData .= $mReporteNestlexItem[$nR]['ADMIDXXX']."\t";
				$cData .= $mReporteNestlexItem[$nR]['DOIIDXXX']."\t";
				$cData .= $mReporteNestlexItem[$nR]['MTRDESXX']."\t";
				$cData .= $mReporteNestlexItem[$nR]['PROIDXXX']."\t";
				$cData .= $mReporteNestlexItem[$nR]['PRODESFA']."\t";
				$cData .= $mReporteNestlexItem[$nR]['TDBDESXX']."\t";
				$cData .= $mReporteNestlexItem[$nR]['DOIMYLEV']."\t";
				$cData .= number_format($mReporteNestlexItem[$nR]['PAGTERIT'],2,',','')."\t";
				$cData .= number_format($mReporteNestlexItem[$nR]['LIMPNEXX'],2,',','')."\t";
				$cData .= number_format($mReporteNestlexItem[$nR]['COSTOITE'],2,',','')."\n";
			}
						
			$cFileName = "Reporte_Nestle_".$kUser."_".date('YmdHis').".xls";
			$cFileDownload = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/".$cFileName;
				   
			$fp=fopen($cFileDownload,"w");
			fwrite($fp,$cData);
			fclose($fp);
						 
			if (file_exists($cFileDownload)){
				chmod($cFileDownload,intval("0777",8)); ?>
				<script languaje = "javascript">
					parent.fmpro3.location = 'frdownload.php?cRuta=<?php echo $cFileName ?>';
				</script>
			<?php } else {
				$nSwitch = 1;
				$cMsj = "No se encontro el archivo $cFileDownload, Favor Comunicar este Error a openTecnologia S.A.";
			}
			##Fin Generando el archivo de excel
		break;
	}			
	
	if($nSwitch == 1){
		f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique"); ?>
		<script languaje="javascript">
			window.close();
		</script>
	<?php } ?>

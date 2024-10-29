<?php
  namespace openComex;
  /**
	 * Imprime Reporte de Auditoria Rentabilidad.
	 * @author Johana Arboleda <dp5@opentecnologia.com.co> 
	 */

	ini_set('error_reporting', E_ERROR);
  ini_set("display_errors","1");
  
  ini_set("memory_limit","512M");
  set_time_limit(0);
  
  include("../../../../libs/php/utility.php");
  include("../../../../libs/php/utiliqdo.php");
	
	$mMeses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");

 	//Se busca desde el año anterior a la creacion del DO
	$nAnoIni = (($cAnoN-1) <  $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : ($cAnoN-1);
	
	//Busco descripcion sucursal comercial
  $qSucCom  = "SELECT ";
  $qSucCom .= "SIAI0119.LINIDXXX,";
  $qSucCom .= "SIAI0119.LINDESXX ";
  $qSucCom .= "FROM $cAlfa.SIAI0119 ";
  $qSucCom .= "WHERE ";
  $qSucCom .= "SUCCOMXX = \"SI\" AND ";
  $qSucCom .= "REGESTXX = \"ACTIVO\"";
  $xSucCom  = f_MySql("SELECT","",$qSucCom,$xConexion01,"");
  $mSucCom = array();
  while ($xRSC = mysql_fetch_array($xSucCom)) {
	 $mSucCom[$xRSC['LINIDXXX']] = $xRSC['LINDESXX'];
	}
	
 //Busco descripcion sucursal operativa
  $qSucOpe  = "SELECT ";
  $qSucOpe .= "$cAlfa.fpar0008.ccoidxxx,";
  $qSucOpe .= "$cAlfa.fpar0008.sucidxxx,";
  $qSucOpe .= "$cAlfa.fpar0008.sucdesxx ";
  $qSucOpe .= "FROM $cAlfa.fpar0008 ";
  $qSucOpe .= "WHERE ";
  $qSucOpe .= "$cAlfa.fpar0008.regestxx = \"ACTIVO\"";
  $xSucOpe  = f_MySql("SELECT","",$qSucOpe,$xConexion01,"");
  $mSucOpe = array();
  $mDatCoi = array();
  while ($xRSO = mysql_fetch_array($xSucOpe)) {
	  $mSucOpe[$xRSO['sucidxxx']] = $xRSO['sucdesxx'];
	  $mDatCoi[$xRSO['ccoidxxx']]['contador']++;
    $mDatCoi[$xRSO['ccoidxxx']]['sucidxxx'] = $xRSO['sucidxxx'];
	}
	
	//Buscando descripcion sucursal comercial
	$qDatExt  = "SELECT  ";
	$qDatExt .= "SIAI0119.LINIDXXX,";
	$qDatExt .= "SIAI0119.LINDESXX ";
	$qDatExt .= "FROM $cAlfa.SIAI0119 ";
	$qDatExt .= "WHERE ";
	$qDatExt .= "SUCCOMXX = \"SI\" AND ";
	$qDatExt .= "REGESTXX = \"ACTIVO\" ORDER BY LINIDXXX";
	$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
	//f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));
	$mSucCom = array();
	while ($xRDDE = mysql_fetch_array($xDatExt)) {
	  $mSucCom[$xRDDE['LINIDXXX']] = $xRDDE['LINDESXX'];
	}
	
	if($cVenId<>""){
    //Busco nombre del vendedor
    $qNomVen  = "SELECT ";
		$qNomVen .= "SIAI0150.CLIIDXXX,";
    $qNomVen .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) <> \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS CLINOMXX ";
		$qNomVen .= "FROM $cAlfa.SIAI0150 ";
		$qNomVen .= "WHERE ";
		$qNomVen .= "CLIVENCO = \"SI\" AND ";
		$qNomVen .= "CLIIDXXX = \"$gVenId\" AND ";
		$qNomVen .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
		$xNomVen  = f_MySql("SELECT","",$qNomVen,$xConexion01,"");
		if(mysql_num_rows($xNomVen) == 0){
		  $cNomVen = "VENDEDOR SIN NOMBRE";
		}else{
		  $xRNV = mysql_fetch_array($xNomVen);
		  $cVenNom = $xRNV['CLINOMXX']." [".$xRNV['CLIIDXXX']."]";
		}
	}
	
	switch ($cTipo) {
		case 1: // PINTA POR PANTALLA// ?>
  		<html>
				<head><title>Auditoria Rentabilidad</title>
				<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New  ?>/estilo.css'>
				<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New  ?>/general.css'>
				<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New  ?>/layout.css'>
				<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New  ?>/custom.css'>
				<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New  ?>/date_picker.js'></script>
				<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New  ?>/utility.js'></script>
				<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New  ?>/ajax.js'></script>
				<link rel="stylesheet" type="text/css" href="../../../../../programs/gwtext/resources/css/ext-all.css">
		  	<script type="text/javascript" src="../../../../../programs/gwtext/adapter/ext/ext-base.js"></script>
		  	<script type="text/javascript" src="../../../../../programs/gwtext/ext-all.js"></script>
		  	<script language="JavaScript" src="../../../../../programs/gwtext/conexijs/loading/loading.js"></script>
		
				<script language="javascript">
				  function f_Imprimir(xComId,xComCod,xComCsc,xComCsc2,xRegFCre) { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
		
				      document.cookie="kModo=VER;path="+"/";
				      
				      var cRuta = "../../../financia/contable/facturax/frfacnue.php?gComId="+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gRegFCre='+xRegFCre;
		            
		          var nX    = screen.width;
					    var nY    = screen.height;
					    var nNx      = (nX-1024)/2;
							var nNy      = (nY-768)/2;
							var cWinOpt  = "width=1024,scrollbars=1,height=768,left="+nNx+",top="+nNy;
							var cWinId = "winfac"+Math.ceil(Math.random()*1000);
							cWindow = window.open(cRuta,cWinId,cWinOpt);
				  		cWindow.focus();
		      	}
		
			  	function f_Movimiento_Do(xComId, cComCod, xSucId, xDocTip, xDocId, xDocSuf, xPucId, xCcoId, xCliId, xRegFCre)  { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
		
		    	  if(xSucId.length   > 0 ||
						   xDocTip.length  > 0 ||
						   xDocId.length  > 0 ||
						   xDocSuf.length  > 0 ||
						   xCliId.length   > 0 ||
						   xRegFCre.length > 0 ){
		
						   var cRuta = "../../../financia/contable/movimido/frmdoprn.php?"+
								           "gComId="+xComId+
				                   "&gComCod="+cComCod+
				                   "&gSucId="+xSucId+
				                   "&gDocTip="+xDocTip+
				                   "&gDocId="+xDocId+
				                   "&gDocSuf="+xDocSuf+
				                   "&gPucId="+xPucId+
				                   "&gCcoId="+xCcoId+
				                   "&gCliId="+xCliId+
				                   "&gRegFCre="+xRegFCre+
				                   "&gMov=CONCEPTO"+
				                   "&gPyG=1";            
		          
						  var nX      = screen.width;
		  				var nY      = screen.height;
		  				var nNx     = 0;  				
		  				var nNy     = 0;
		  				var cWinOpt = "width="+nX+",scrollbars=1,resizable=YES,height="+nY+",left="+nNx+",top="+nNy;
		  				var cNomVen = 'zWinTrp'+Math.ceil(Math.random()*1000);
		  				cWindow = window.open(cRuta,cNomVen,cWinOpt);
		  				cWindow.focus();	
						} else {
							alert("El Numero del DO esta Vacio, Verifique");
						}
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
		    ?>
				<center>
				<table width="95%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border: 1px solid black;">
					<tr bgcolor="#0B610B">
						<td class="name"><center><h2 style="margin-bottom:5px;color:#FFFFFF">AUDITORIA RENTABILIDAD</h2></center></td>
					</tr>
	        <?php 
	        if($cLinId<>""){ 
	      	 echo "<tr><td style=\"border-bottom: hidden;font-size:14px;padding:5px\"><b>SUCURSAL COMERCIAL:</b>&nbsp;&nbsp;".(($mSucCom[$cLinId])?$mSucCom[$cLinId]:"SUCURSAL SIN DESCRIPCION")."</td></tr>";
	      	}
	      	if($cVenId<>""){
	          echo "<tr><td style=\"border-bottom: hidden;font-size:14px;padding:5px\"><b>VENDEDOR:</b>&nbsp;&nbsp;".$cVenNom."</td></tr>";
	      	}
	      	if($cEstId<>""){
	          echo "<tr><td style=\"border-bottom: hidden;font-size:14px;padding:5px\"><b>ESTADO DO:</b>&nbsp;&nbsp;".$cEstId."</td></tr>";
	      	}
	      	if($cTipOpe<>""){
	          echo "<tr><td style=\"border-bottom: hidden;font-size:14px;padding:5px\"><b>TIPO OPERACI&Oacute;N:</b>&nbsp;&nbsp;".$cTipOpe."</td></tr>";
	      	}
					
					if($cAplCom == "NO"){
						$cFecFin = "$cAnoN-$cMesFinN-".date ('d', mktime (0, 0, 0, $cMesFinN + 1, 0, $cAnoN));
						echo "<tr><td style=\"border-bottom: hidden;font-size:14px;padding:5px\"><b>RANGO DE FECHAS:</b>&nbsp;&nbsp;DEL $cAnoN-$cMesIniN-01 AL $cFecFin</td></tr>";
					}else{
						echo "<tr><td style=\"border-bottom: hidden;font-size:14px;padding:5px\"><b>A&Ntilde;OS COMPARADOS:</b>&nbsp;&nbsp;$cAnoIni - $cAnoFin</td></tr>";
						if ($cMesIni <> "" && $cMesFin <> ""){
							echo "<tr><td style=\"border-bottom: hidden;font-size:14px;padding:5px\"><b>RANGO DE MESES:</b>&nbsp;&nbsp;$cMesIni - $cMesFin</td></tr>";
						}
					}
				?>
				<tr><td style="padding:5px"><b>FECHA Y HORA DE CONSULTA:</b>&nbsp;&nbsp; <?php echo date('Y-m-d')."-".date('H:i:s') ?></td></tr>
			</table>
		<?php break;
		case 2:
			
			$header .= 'AUDITORIA RENTABILIDAD\n';
			$header .= "\n";
			$data = "";
			$title = "AUDITORIA RENTABILIDAD_{$_COOKIE['kUsrId']}_".date('YmdHis').".xls";
			
			$data .= '<table width="2000" cellpadding="0" cellspacing="0" border="0">';
			$data .= '<tr>';
			$data .= '<td bgcolor="#0B610B" colspan="12"><center><h2 style="margin-bottom:5px;color:#FFFFFF">AUDITORIA RENTABILIDAD</h2></center></td>';
			$data .= '</tr>';
			
			if($cLinId<>""){ 
			 $data .= '<tr><td colspan="12" style=";font-size:14px;padding:5px"><b>SUCURSAL COMERCIAL:</b>'.(($mSucCom[$cLinId])?$mSucCom[$cLinId]:"SUCURSAL SIN DESCRIPCION").'</td></tr>';
			}
			if($cVenId<>""){
			  $data .= '<tr><td colspan="12" style=";font-size:14px;padding:5px"><b>VENDEDOR:</b>'.$cVenNom.'</td></tr>';
			}
			if($cEstId<>""){
			  $data .= '<tr><td colspan="12" style=";font-size:14px;padding:5px"><b>ESTADO DO:</b>'.$cEstId.'</td></tr>';
			}
			if($cTipOpe<>""){
			  $data .= '<tr><td colspan="12" style=";font-size:14px;padding:5px"><b>TIPO OPERACI&Oacute;N:</b>'.$cTipOpe.'</td></tr>';
			}
			
			if($cAplCom == "NO"){
			  $cFecFin = "$cAnoN-$cMesFinN-".date ('d', mktime (0, 0, 0, $cMesFinN + 1, 0, $cAnoN));
			  $data .= '<tr><td colspan="13" style=";font-size:14px;padding:5px"><b>RANGO DE FECHAS:</b>DEL '.$cAnoN.'-'.$cMesIniN.'-01 AL '.$cFecFin.'</td></tr>';
			}else{
			  $data .= '<tr><td colspan="13" style=";font-size:14px;padding:5px"><b>A&Ntilde;OS COMPARADOS:</b>'.$cAnoIni.' - '.$cAnoFin.'</td></tr>';
			  if ($cMesIni <> "" && $cMesFin <> ""){
			    $data .= '<tr><td colspan="13" style=";font-size:14px;padding:5px"><b>RANGO DE MESES:</b>'.$cMesIni.' - '.$cMesFin.'</td></tr>';
			  }
			}
			$data .= '<tr><td colspan="12" style="padding:5px"><b>FECHA Y HORA DE CONSULTA:</b>'.date('Y-m-d').'-'.date('H:i:s').'</td></tr>';
			$data .= '</table>';
		break;
	}
	
	$vCtoPcc = array(); $vCtoAnt = array();
	  
  //Buscano conceptos de causaciones automaticas
  $qCAyP121 = "SELECT DISTINCT $cAlfa.fpar0121.pucidxxx, $cAlfa.fpar0121.ctoidxxx FROM $cAlfa.fpar0121 WHERE $cAlfa.fpar0121.regestxx = \"ACTIVO\"";
  $xCAyP121 = f_MySql("SELECT","",$qCAyP121,$xConexion01,"");
  //f_Mensaje(__FILE__,__LINE__,$qCAyP121."~".mysql_num_rows($xCAyP121));
  while($xRCP121 = mysql_fetch_array($xCAyP121)) {
  	$vCtoPcc[count($vCtoPcc)] = "{$xRCP121['pucidxxx']}~{$xRCP121['ctoidxxx']}";
  }
  
  //Buscando conceptos
  $qCtoAntyPCC = "SELECT DISTINCT $cAlfa.fpar0119.ctoantxx, $cAlfa.fpar0119.ctopccxx, $cAlfa.fpar0119.pucidxxx, $cAlfa.fpar0119.ctoidxxx FROM $cAlfa.fpar0119 WHERE ($cAlfa.fpar0119.ctoantxx = \"SI\" OR $cAlfa.fpar0119.ctopccxx = \"SI\") AND $cAlfa.fpar0119.regestxx = \"ACTIVO\"";
  $xCtoAntyPCC = f_MySql("SELECT","",$qCtoAntyPCC,$xConexion01,"");
  //f_Mensaje(__FILE__,__LINE__,$qCtoAntyPCC."~".mysql_num_rows($xCtoAntyPCC));
  while($xRCAP = mysql_fetch_array($xCtoAntyPCC)) {
  	if ($xRCAP['ctoantxx'] == "SI") {
  		$vCtoAnt[count($vCtoAnt)] = "{$xRCAP['pucidxxx']}~{$xRCAP['ctoidxxx']}";
  	}
		if ($xRCAP['ctopccxx'] == "SI") {
  		$vCtoPcc[count($vCtoPcc)] = "{$xRCAP['pucidxxx']}~{$xRCAP['ctoidxxx']}";
  	}
  }
	
	//Buscar los comprobante de Nota credito
	$vComAnu = array(); //comidxxx~comcodxxx
	$qAjustes  = "SELECT ";
  $qAjustes .= "CONCAT(comidxxx,\"~\",comcodxx) AS comidxxx ";
  $qAjustes .= "FROM $cAlfa.fpar0117 ";
  $qAjustes .= "WHERE ";
  $qAjustes .= "comidxxx = \"C\" AND ";
  $qAjustes .= "comtipxx != \"AJUSTES\" ";
  $xAjustes = f_MySql("SELECT","",$qAjustes,$xConexion01,"");
  $cAjustes = "";
  while ($xRDB = mysql_fetch_array($xAjustes)) {
  	$vComAnu[] = "{$xRDB['comidxxx']}";
  }
  $cAjustes = substr($cAjustes,0,strlen($cAjustes)-1); 
	
	//Colmas hay que dejar quemados unos comprobantes COLMASXX, DECOLMASXX, TECOLMASXX
	//L~044, L~024, L~020, L~016
	$vComAnuCol = array();
	if ($cAlfa == "COLMASXX" || $cAlfa == "DECOLMASXX" || $cAlfa == "TECOLMASXX") {
		$vComAnuCol[] = "L~044";
		$vComAnuCol[] = "L~024";
		$vComAnuCol[] = "L~020";
		$vComAnuCol[] = "L~016";
	}
	
  //Buscar los comprobante de ajuste
  $vComAju = array(); //comidxxx~comcodxxx
  $qAjustes  = "SELECT ";
  $qAjustes .= "CONCAT(comidxxx,\"~\",comcodxx) AS comidxxx ";
  $qAjustes .= "FROM $cAlfa.fpar0117 ";
  $qAjustes .= "WHERE ";
  $qAjustes .= "comtipxx = \"AJUSTES\" ";
  $xAjustes = f_MySql("SELECT","",$qAjustes,$xConexion01,"");
  $cAjustes = "";
  while ($xRDB = mysql_fetch_array($xAjustes)) {
    $vComAju[] = "{$xRDB['comidxxx']}";
  }
  
	/**
	 * Buscando las cuentas del impuesto financiero
	 * Se debe discriminar el 4xmil por del DO y no mostrarse el 4xmil de toda la factura
	 */
	$qCtoIF  = "SELECT fpar0119.*,fpar0115.* "; // Aqui no aplica la busqueda contra la fpar0121
	$qCtoIF .= "FROM $cAlfa.fpar0119,$cAlfa.fpar0115 ";
	$qCtoIF .= "WHERE ";
	$qCtoIF .= "fpar0119.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
	$qCtoIF .= "fpar0119.ctocomxx LIKE \"%F~%\" AND ";
	$qCtoIF .= "fpar0119.ctoclaxf = \"IMPUESTOFINANCIERO\" AND ";
	$qCtoIF .= "fpar0119.regestxx = \"ACTIVO\" LIMIT 0,1";
	$xCtoIF  = f_MySql("SELECT","",$qCtoIF,$xConexion01,"");
	//f_Mensaje(__FILE__,__LINE__,$qCtoIF." ~ ".mysql_num_rows($xCtoIF));
	$xRCIF = mysql_fetch_array($xCtoIF); 

  $qDatDoi  = "SELECT ";	
	$qDatDoi .= "comidxxx, ";
  $qDatDoi .= "comcodxx, ";
  $qDatDoi .= "ccoidxxx, ";
  $qDatDoi .= "pucidxxx, ";
	$qDatDoi .= "sucidxxx, ";
  $qDatDoi .= "docidxxx, ";
	$qDatDoi .= "docsufxx, ";
	$qDatDoi .= "cliidxxx, ";
	$qDatDoi .= "regestxx, ";
	$qDatDoi .= "succomxx, ";
	$qDatDoi .= "docvenxx, ";
	$qDatDoi .= "regfcrex, ";
	$qDatDoi .= "doctipxx, ";
	$qDatDoi .= "SUBSTRING(regfcrex,1,4) AS docanoxx ";
	$qDatDoi .= "FROM $cAlfa.sys00121 ";
	$qDatDoi .= "WHERE ";
	if($cLinId<>""){ 
	 $qDatDoi .= "succomxx = \"$cLinId\" AND ";
	}
	
	if($cSucId<>""){ 
	 $qDatDoi .= "sucidxxx = \"$cSucId\" AND ";
	}
	
	if($cVenId<>""){
    $qDatDoi .= "docvenxx = \"$cVenId\" AND ";
	}
	if($cEstId<>""){
    $qDatDoi .= "regestxx = \"$cEstId\" AND ";
	} else {
	  $qDatDoi .= "regestxx IN (\"ACTIVO\",\"FACTURADO\") AND ";
	}
	if($cTipOpe<>""){
    $qDatDoi .= "doctipxx = \"$cTipOpe\" AND ";
	}
	if($cAplCom == "NO"){
	//$qDatDoi .= "docidxxx IN (\"20010073144\",\"22010010305\",\"22010030464\",\"24010010055\",\"2202009054\",\"24040070144\") ";
		$qDatDoi .= "regfcrex BETWEEN \"$cAnoN-$cMesIniN-01\" AND \"$cAnoN-$cMesFinN-".date ('d', mktime (0, 0, 0, $cMesFinN + 1, 0, $cAnoN))."\" ";
	} else {
		$nAnoIni = $cAnoIni;
	  $nAnoFin = (($cAnoFin + 1) > date('Y'))?date('Y'):($cAnoFin + 1);
	  
	  if ($cMesIni <> "" && $cMesFin <> ""){
	    $qDatDoi .= "($cAlfa.sys00121.regfcrex BETWEEN \"$cAnoIni-$cMesIni-01\" AND \"$cAnoIni-$cMesFin-".date ('d', mktime (0, 0, 0, $cMesFin + 1, 0, $cAnoIni))."\" OR $cAlfa.sys00121.regfcrex BETWEEN \"$cAnoFin-$cMesIni-01\" AND \"$cAnoFin-$cMesFin-".date ('d', mktime (0, 0, 0, $cMesFin + 1, 0, $cAnoFin))."\") ";
	  } else {
	    $qDatDoi .= "SUBSTRING($cAlfa.sys00121.regfcrex,1,4) BETWEEN \"$cAnoIni\" AND \"$cAnoFin\" ";
	  }
	}
	$qDatDoi .= "ORDER BY docanoxx, succomxx, doctipxx, docidxxx, docsufxx";
	$xDatDoi = f_MySql("SELECT","",$qDatDoi,$xConexion01,"");
	//echo $qDatDoi."~".mysql_num_rows($xDatDoi)."<br><br>";
	
	$j = 0; 
  $cSucAux   = "";
  $cAno      = "";  
  $nValFacT  = 0; //Sumatoria Valor Facturado
  $nValCosT  = 0; //Sumatoria Valor Costo
  $nInoT     = 0; //Sumatoria Ino
    
  $nValFacS  = 0; //Sumatoria por sucursal Valor Facturado
  $nValCosS  = 0; //Sumatoria por sucursal Valor Costo
  $nInoS     = 0; //Sumatoria por sucursal Ino
  $nConSuc   = 0; //Do por Sucursal
    
  $mTotales = array(); //Matriz totales por año
  $mTotSuc  = array(); //Matriz con total de Do por sucursal
  $mSucTotal= array();//Matriz con los totales por sucursal
    
  $nActivos    = 0; //Numero de Do activos
  $nFacturados = 0; //Numero de Do facturados
  $nTotalDo    = 0; //Numero total de Do 
  
  /*$mTotales[$cAnoN][0] = 0;
  $mTotales[$cAnoN][1] = 0;
  $mTotales[$cAnoN][2] = 0;*/
  
  $vTotalDos = array(); //Control para contar los DO
  
  if ($cAplCom == "NO") {
		$mTotales[$cAnoN][0] = 0;
		$mTotales[$cAnoN][1] = 0;
		$mTotales[$cAnoN][2] = 0;
	}else{
		$mTotales[$cAnoIni][0] = 0;
		$mTotales[$cAnoIni][1] = 0;
		$mTotales[$cAnoIni][2] = 0;
	 
		$mTotales[$cAnoFin][0] = 0;
		$mTotales[$cAnoFin][1] = 0;
		$mTotales[$cAnoFin][2] = 0;
	}
	
	while ($xRDD = mysql_fetch_array($xDatDoi)) {
		//Buscando los valores
		#Busco el nombre del cliente
	 	$qCliNom  = "SELECT ";
	 	$qCliNom .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) <> \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
	 	$qCliNom .= "FROM $cAlfa.SIAI0150 ";
	 	$qCliNom .= "WHERE ";
	 	$qCliNom .= "CLIIDXXX = \"{$xRDD['cliidxxx']}\" LIMIT 0,1";
	 	$xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
	 	if (mysql_num_rows($xCliNom) > 0) {
	  	$xRCN = mysql_fetch_array($xCliNom);
	   	$xRDD['clinomxx'] = $xRCN['clinomxx'];
	 	} else {
	  	$xRDD['clinomxx'] = "CLIENTE SIN NOMBRE";
	 	}
	 	mysql_free_result($xCliNom);
		
	 	#Busco el nombre del vendedor
   	$qCliVen  = "SELECT ";
   	$qCliVen .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) <> \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS nomvenxx ";
   	$qCliVen .= "FROM $cAlfa.SIAI0150 ";
   	$qCliVen .= "WHERE ";
   	$qCliVen .= "CLIVENCO = \"SI\" AND ";
   	$qCliVen .= "CLIIDXXX = \"{$xRDD['docvenxx']}\" LIMIT 0,1";
   	$xCliVen = f_MySql("SELECT","",$qCliVen,$xConexion01,"");
   	if (mysql_num_rows($xCliVen) > 0) {
    	$xRCN = mysql_fetch_array($xCliVen);
     	$xRDD['nomvenxx'] = $xRCN['nomvenxx'];
   	} else {
    	$xRDD['nomvenxx'] = "VENDEDOR SIN NOMBRE";
   	}
		mysql_free_result($xCliVen);
		
		//variables de pagos a terceros y anticipos
		$cFactura = ""; //Factruas del DO
   	$cCanFac  = ""; //Documentos de cancelacion
   	$nPT      = 0;  //Valor Costo = Pagos Terceros
   	$nPTFac   = 0;  //Pagos Terceros Facturados
   	$nAnt     = 0;  //Anticipos
   	$nIP      = 0;  //Ingresos Propios
   	$nCosyGas = 0;  //Costos y Gastos
   	$nImpFac = 0;   //Impuestos de las facturas
   	unset($vFacturas);
   	$vFactura = array(); //Facturas del DO
   	
   	unset($vImpFas);
   	$vImpFas = array();
		
		//variables para Ingresos Propios y Gastos del DO
		unset($mCocDat);
		unset($mCocAux);
		unset($mMovCom);
		unset($mIngxDo);
		unset($mCyGxDo);
		unset($vImpFin);
		
		$mCocDat = array(); //Matriz retornada
		$mCocAux = array(); //Ingresos propios del DO
		$mMovCom = array(); //Ingresos, costos y gastos de todos los sufijos de DO
		$mIngxDo = array(); //Ingresos por DO que no se encontraron en el primer select
		$mCyGxDo = array(); //Costos y gastos del DO
		$nSinDatos = 0; //Esta variable hace referencia a la cantidad de costos y gastos a los que no se le pueden identificar la sucursal y sufijo al que pertenecen
	
  	//select para buscar la descripcion el concepto de la F
		$vImpFin = array(); $nCal4xMil = 0; 
		
		$vPagAnu = array(); $mPagDo = array(); $v4xMil = array();
		
		for ($cPerAno=$nAnoIni;$cPerAno<=date('Y');$cPerAno++) {
			
			//datos cabecera
			//Se deben buscar las facturas de ese DO para verificar despues si tuvieron o no 4xmil e incluirlo en el reporte
			//Trayendo facturas del DO
			$qCocDat  = "SELECT ";
			$qCocDat .= "$cAlfa.fcoc$cPerAno.comidxxx, "; 
			$qCocDat .= "$cAlfa.fcoc$cPerAno.comcodxx, ";
			$qCocDat .= "$cAlfa.fcoc$cPerAno.comcscxx, ";
			$qCocDat .= "$cAlfa.fcoc$cPerAno.comcsc2x, ";
			$qCocDat .= "$cAlfa.fcoc$cPerAno.comfecxx, ";
			$qCocDat .= "$cAlfa.fcoc$cPerAno.comifxxx, ";
			$qCocDat .= "$cAlfa.fcoc$cPerAno.comfpxxx, ";
			$qCocDat .= "$cAlfa.fcoc$cPerAno.commemod ";
			$qCocDat .= "FROM $cAlfa.fcoc$cPerAno ";
			$qCocDat .= "WHERE ";
			$qCocDat .= "$cAlfa.fcoc$cPerAno.comidxxx = \"F\" AND ";
			$qCocDat .= "$cAlfa.fcoc$cPerAno.comfpxxx LIKE \"%{$xRDD['docidxxx']}~{$xRDD['docsufxx']}%\" AND ";
			$qCocDat .= "$cAlfa.fcoc$cPerAno.regestxx IN (\"ACTIVO\",\"PROVISIONAL\") ";
			$xCocDat = mysql_query($qCocDat,$xConexion01);
			//echo $qCocDat."~".mysql_num_rows($xCocDat)."<br>";
			//f_Mensaje(__FILE__,__LINE__,$qCocDat."~".mysql_num_rows($xCocDat));
			while ($xRMdo = mysql_fetch_array($xCocDat)) {
				
				##Exploto el campo comfpxxx para hacer la busqueda exacta del Do y poder mostrar las facturas, ya que en el select se busca con un LIKE##
				##en algunos casos trae facturas que no correponde al Do##
				unset($mFacDo); 
				$mFacDo = array();
				$mFacDo = f_explode_array($xRMdo['comfpxxx'],"|","~");
				for($x=0;$x<count($mFacDo);$x++){
					$nIncluir = 0; //Varible que indica si el registro es del DO o NO y si se debe tener en cuenta
					if ($vSysStr['financiero_asignar_centro_de_costo_de_sucursal_comercial_a_do'] == 'SI') {
						//comparo do y sufijo
						if($mFacDo[$x][2] == $xRDD['docidxxx'] && $mFacDo[$x][3] == $xRDD['docsufxx']) {
							$nIncluir = 1;
						}
					} else {
						//comparo sucursal, do y sufijo
						if($mFacDo[$x][15] == $xRDD['sucidxxx'] && $mFacDo[$x][2] == $xRDD['docidxxx'] && $mFacDo[$x][3] == $xRDD['docsufxx']){
							$nIncluir = 1;
						}
					}
					
					if ($nIncluir == 1) {
						if(in_array("{$xRMdo['comidxxx']}~{$xRMdo['comcodxx']}~{$xRMdo['comcscxx']}~{$xRMdo['comcsc2x']}~{$xRMdo['comfecxx']}", $vFactura) == false) {
							$vFactura[] = "{$xRMdo['comidxxx']}~{$xRMdo['comcodxx']}~{$xRMdo['comcscxx']}~{$xRMdo['comcsc2x']}~{$xRMdo['comfecxx']}";
						}
					}
				}
				##Fin Exploto el campo comfpxxx para hacer la busqueda exacta del Do y poder mostrar las facturas, ya que en el select se busca con un LIKE##
				
				//Trayendo 4xmil por DO
				$nAcuPcc4xmil = 0; $nImpFin = 0; 
				if ($xRMdo['commemod'] != "") {
					$mPcc = f_Explode_Array($xRMdo['commemod'],"|","~");
					for ($nP=0; $nP < count($mPcc); $nP++) {
						$vDo = explode("-",$mPcc[$nP][14]);
						$nIncluir = 0; $cDo = "";
						if ($vSysStr['financiero_asignar_centro_de_costo_de_sucursal_comercial_a_do'] == 'SI') {
							if ($xRDD['docidxxx'] == $vDo[1] && $xRDD['docsufxx'] == $vDo[2]) {
								$nIncluir = 1;
								$cDo = "{$vDo[1]}-{$vDo[2]}";
							}
						} else {
							if ($xRDD['docidxxx'] == $vDo[1] && $xRDD['docsufxx'] == $vDo[2]) {
								$nIncluir = 1;
								$cDo = "{$vDo[1]}-{$vDo[2]}";
							}
						}
						$n4xmil = 0;
						if (($nP+1) == count($mPcc)) {
							$n4xmil = $xRMdo['comifxxx'] - $nAcuPcc4xmil;
							$vImpFin["{$xRMdo['comidxxx']}~{$xRMdo['comcodxx']}~{$xRMdo['comcscxx']}~{$xRMdo['comcsc2x']}-{$cDo}"] += $n4xmil; 
						} else {
							$n4xmil = ($mPcc[$nP][7] > 0) ? round($mPcc[$nP][7] * 4/1000) : round($mPcc[$nP][22] * 4/1000);
							$vImpFin["{$xRMdo['comidxxx']}~{$xRMdo['comcodxx']}~{$xRMdo['comcscxx']}~{$xRMdo['comcsc2x']}-{$cDo}"] +=  $n4xmil;
							$nAcuPcc4xmil += $n4xmil;
						}
						
						if ($nIncluir == 1) {
							$nImpFin += $n4xmil;
							if(in_array("{$xRMdo['comidxxx']}~{$xRMdo['comcodxx']}~{$xRMdo['comcscxx']}~{$xRMdo['comcsc2x']}~{$xRMdo['comfecxx']}", $vFactura) == false) {
								$vFactura[] = "{$xRMdo['comidxxx']}~{$xRMdo['comcodxx']}~{$xRMdo['comcscxx']}~{$xRMdo['comcsc2x']}~{$xRMdo['comfecxx']}";
							}
						}							
					}
				}
				//4xmil de la factura
				$v4xMil["{$xRMdo['comidxxx']}~{$xRMdo['comcodxx']}~{$xRMdo['comcscxx']}~{$xRMdo['comcsc2x']}"] = $nImpFin;

			}
			mysql_free_result($xCocDat);
			
			//datos detalle
		  $qSqlMdo  = "SELECT *, ";
			$qSqlMdo .= "SUBSTRING(pucidxxx,1,1) AS DIGCUEXX ";
		  $qSqlMdo .= "FROM $cAlfa.fcod$cPerAno ";
			$qSqlMdo .= "WHERE ";
		  $qSqlMdo .= "((comcsccx  = \"{$xRDD['docidxxx']}\" AND comseqcx  = \"{$xRDD['docsufxx']}\") OR ";
			$qSqlMdo .= "(sccidxxx = \"{$xRDD['docidxxx']}\" AND SUBSTRING(pucidxxx,1,1) IN (\"4\",\"5\",\"6\",\"7\"))) AND ";
			$qSqlMdo .= "regestxx IN (\"ACTIVO\",\"PROVISIONAL\") ";
			$qSqlMdo .= "ORDER BY comidxxx,comcodxx,comcscxx ";
			$xCrsMdo = mysql_query($qSqlMdo,$xConexion01);
			//echo $qSqlMdo."~".mysql_num_rows($xCrsMdo)."<br>";
			
			while ($xRMdo = mysql_fetch_array($xCrsMdo)) {
				$nIncluir = 0; //Varible que indica si el registro es del DO o NO y si se debe tener en cuenta
				if ($xRMdo['sucidxxx'] != "" && $xRMdo['docidxxx'] != "" && $xRMdo['docsufxx'] != "") {
					//si el pago tiene los campos de sucursal, do y sufijo digitados se hace la comparacion con estos campos
					if ($xRMdo['sucidxxx'] == $xRDD['sucidxxx'] && $xRMdo['docidxxx'] == $xRDD['docidxxx'] && $xRMdo['docsufxx'] == $xRDD['docsufxx']) {
						$nIncluir = 1;
					}
				} else {
					//Comparando por el centro de costo
					if ($xRMdo['ccoidxxx'] == $xRDD['ccoidxxx']) {
						$nIncluir = 1;
					}
				}

				if ($nIncluir == 1) {
					//si el concepto es de pagos a terceros o anticipo
					if (in_array("{$xRMdo['pucidxxx']}~{$xRMdo['ctoidxxx']}", $vCtoAnt) == true || in_array("{$xRMdo['pucidxxx']}~{$xRMdo['ctoidxxx']}", $vCtoPcc) == true) {
							
						//Para que se un anticipo, debe ser un recibo de caja con movimiento credito 
            //o, ser un ajuste y ser un concepto de anticipo
            // echo "{$xRMdo['comidxxx']}-{$xRMdo['comcodxx']}-{$xRMdo['comcscxx']}-{$xRMdo['comcsc2x']} -> ".(($xRMdo['commovxx'] == "D") ? $xRMdo['comvlrxx'] : ($xRMdo['comvlrxx']*-1))."<br>";
            // echo "{$xRMdo['pucidxxx']}~{$xRMdo['ctoidxxx']} --> ".in_array("{$xRMdo['pucidxxx']}~{$xRMdo['ctoidxxx']}", $vCtoAnt)." == true && {$xRMdo['comidxxx']}~{$xRMdo['comcodxx']} -->".in_array("{$xRMdo['comidxxx']}~{$xRMdo['comcodxx']}", $vComAju)." == true) || (".$xRMdo['comidxxx']." == \"R\" && ".$xRMdo['commovxx']." == \"C\")<br><br>";
            if ((in_array("{$xRMdo['pucidxxx']}~{$xRMdo['ctoidxxx']}", $vCtoAnt) == true && in_array("{$xRMdo['comidxxx']}~{$xRMdo['comcodxx']}", $vComAju) == true) || 
                ($xRMdo['comidxxx'] == "R" && $xRMdo['commovxx'] == "C")) {
						  if ($xRMdo['comfacxx'] != "") { //si el anticipo fue facturado
								$nAnt += ($xRMdo['commovxx'] == "D") ? $xRMdo['comvlrxx'] : ($xRMdo['comvlrxx']*-1);  //Anticipos
							}
						} elseif (in_array("{$xRMdo['pucidxxx']}~{$xRMdo['ctoidxxx']}", $vCtoPcc) == true || in_array("{$xRMdo['pucidxxx']}~{$xRMdo['ctoidxxx']}", $vCtoAnt) == true) {
              //Se incluye la condicion de que se un anticipo, para aquellos conceptos que marcaron como anticipos 
              //y que no son una R ni un ajuste y en el movimiento del DO se muestra en pagos a terceros
              
						  $nBan = 0;
              
							//Se debe sumar solo si los comprobantes no son de anulacion de factura comidxxx~comcodxx
              if (in_array("{$xRMdo['comidxxx']}~{$xRMdo['comcodxx']}", $vComAnu) == true) {
                //Si es un documento de nota credito se debe verificar que sea de anualcion de factura
                //Si es de anulacion de factura no se suma el 4xmil, los otros conceptos se anulan con los demas registros
                $qFacAnu  = "SELECT DISTINCT comidxxx, comcodxx, comcscxx, comcsc2x, comobs2x ";
                $qFacAnu .= "FROM $cAlfa.fcoc$cPerAno ";
                $qFacAnu .= "WHERE ";
                $qFacAnu .= "comidxxx = \"{$xRMdo['comidxxx']}\" AND ";
                $qFacAnu .= "comcodxx = \"{$xRMdo['comcodxx']}\" AND ";
                $qFacAnu .= "comcscxx = \"{$xRMdo['comcscxx']}\" AND ";
                $qFacAnu .= "comcsc2x = \"{$xRMdo['comcsc2x']}\" AND ";
                $qFacAnu .= "comobs2x LIKE \"%ANULADA%\" AND ";
                $qFacAnu .= "regestxx = \"ACTIVO\"";
                $xFacAnu = mysql_query($qFacAnu,$xConexion01);
                // echo $qFacAnu."~".mysql_num_rows($xFacAnu)."<br>";
                if (mysql_num_rows($xFacAnu) > 0) {
                  $nBan = 1;
                  $vFacAnu = mysql_fetch_array($xFacAnu);
                  $vFac = explode("~", $vFacAnu['comobs2x']);
                  $cFacAnu = "\"{$vFac[1]}-{$vFac[2]}-{$vFac[3]}-{$vFac[4]}\",";
                }
              }

              if (in_array("{$xRMdo['comidxxx']}~{$xRMdo['comcodxx']}", $vComAnuCol) == true) {
                $nBan = 1;
                $cFacAnu = "\"{$xRMdo['comidxxx']}-{$xRMdo['comcodxx']}-{$xRMdo['comcscxx']}-{$xRMdo['comcsc2x']}\",";
              }
              
              if ($nBan == 0) {
                $mPagDo[count($mPagDo)] = $xRMdo;
              }
						}	
					} elseif ($xRMdo['DIGCUEXX'] == "4" || $xRMdo['DIGCUEXX'] == "5" || $xRMdo['DIGCUEXX'] == "6" || $xRMdo['DIGCUEXX'] == "7") {
						//Si el concepto es un ingreso propio, costo o gasto	
						//Verificando si es el 4xmil
						if ($xRMdo['pucidxxx'] == $xRCIF['pucidxxx']) {
							$nCal4xMil++;
							//El 4xmil debe Discriminarse por DO
							if ($vSysStr['financiero_asignar_centro_de_costo_de_sucursal_comercial_a_do'] == 'SI') {
								//comparo do y sufijo
								$cDo = "{$xRDD['docidxxx']}-{$xRDD['docsufxx']}";
							} else {
								//comparo do y sufijo	
								$cDo = "{$xRDD['docidxxx']}-{$xRDD['docsufxx']}";
							}
							$xRMdo['comvlrxx'] = $vImpFin["{$xRMdo['comidxxx']}~{$xRMdo['comcodxx']}~{$xRMdo['comcscxx']}~{$xRMdo['comcsc2x']}-{$cDo}"];
						}
						
						if (($xRMdo['comvlrxx']+0) <= 0) {
							$nIncluir = 0;
						}
						
						//Buscando si ese comprobante y secuencia ya se incluyeron en el select superior
						if ($nIncluir == 1) {
							
							//Todos los registros
							$mMovCom[count($mMovCom)] = $xRMdo;
							
							if ($xRMdo['sucidxxx'] != "" && $xRMdo['docidxxx'] != "" && $xRMdo['docsufxx'] != "") {
								//si el pago tiene los campos de sucursal, do y sufijo digitados se hace la comparacion con estos campos
								if ($xRMdo['sucidxxx'] == $xRDD['sucidxxx'] && $xRMdo['docidxxx'] == $xRDD['docidxxx'] && $xRMdo['docsufxx'] == $xRDD['docsufxx']) {
									$mCyGxDo[count($mCyGxDo)] = $xRMdo;
								}
							} else {
								if ($xRMdo['DIGCUEXX'] == "5" || $xRMdo['DIGCUEXX'] == "6" || $xRMdo['DIGCUEXX'] == "7") {
									$nSinDatos++;
								} else {
									//Para las cuentas de ingresos propios solo se incluyen las que corresponden a las facturas iniciales
									$mIngxDo[count($mIngxDo)] = $xRMdo;
								}
							}
						}
					} elseif ($xRMdo['comctocx'] != "") {
						//Impuesto en la factura
						$nImpFac = ($xRMdo['commovxx'] == "D") ? $xRMdo['comvlrxx'] : ($xRMdo['comvlrxx']*-1);
					}
				}
			}
			mysql_free_result($xCrsMdo);

			if ($cFacAnu != "") {
				//Busco las factuas anuladas con los comprobantes de anulacion de COLMAS
				$cFacAnu = substr($cFacAnu, 0, -1);
				$qFacAnu  = "SELECT DISTINCT comidcxx, comcodcx, comcsccx ";
				$qFacAnu .= "FROM $cAlfa.fcod$cPerAno ";
				$qFacAnu .= "WHERE ";
				$qFacAnu .= "comidcxx = \"F\" AND ";
				$qFacAnu .= "CONCAT(comidxxx,\"-\",comcodxx,\"-\",comcscxx,\"-\",comcsc2x) IN ($cFacAnu) AND ";
				$qFacAnu .= "regestxx = \"ACTIVO\"";
				$xFacAnu = mysql_query($qFacAnu,$xConexion01);
				//echo $qFacAnu."~".mysql_num_rows($xFacAnu)."<br>";
			
				while ($xRFA = mysql_fetch_array($xFacAnu)) {
					$vPagAnu[count($vPagAnu)] = "{$xRFA['comidcxx']}-{$xRFA['comcodcx']}-{$xRFA['comcsccx']}-{$xRFA['comcsccx']}";
				}
				mysql_free_result($xFacAnu);
			}
		} ##for ($cPerAno=$nAnoIni;$cPerAno<=date('Y');$cPerAno++) {## 
		
		
		//Sumatoria pagos a terceros
		for ($nP=0; $nP<count($mPagDo); $nP++) {
			if (in_array("{$mPagDo[$nP]['comfacxx']}", $vPagAnu) == false) {
				$nPT += ($mPagDo[$nP]['commovxx'] == "D") ? $mPagDo[$nP]['comvlrxx'] : ($mPagDo[$nP]['comvlrxx']*-1);  //Valor Costo = Pagos Terceros
				if ($mPagDo[$nP]['comfacxx'] != "") { //si pago ya fue facturado
					$nPTFac  += ($mPagDo[$nP]['commovxx'] == "D") ? $mPagDo[$nP]['comvlrxx'] : ($mPagDo[$nP]['comvlrxx']*-1);  //Valor Costo = Pagos Terceros
				}
			}
		}
		
		//Si existe un solo costo o gasto que no puede ser asociado a un sufijo en especifico se debe mostrar 
		//en el sufijo uno todo el movimiento contable de todos los DO, de lo contrario, se mostrar en cada sufijo su pyg correspondiente
		if ($nSinDatos > 0) {
			if ($xRDD['docsufxx'] == "001" || $xRDD['docsufxx'] == "01" || $xRDD['docsufxx'] == "1") {
				$mCocDat = array_merge($mCocAux, $mMovCom);	
			} else {
				$mCocDat = array_merge($mCocAux, $mIngxDo);	
			}			
		} else {
			$mCocDat = array_merge($mCocAux, $mIngxDo);
			$mCocDat = array_merge($mCocDat, $mCyGxDo);
		}
		
		for($i=0; $i<count($mCocDat); $i++) {
			if ($mCocDat[$i]['DIGCUEXX'] == "5" || $mCocDat[$i]['DIGCUEXX'] == "6" || $mCocDat[$i]['DIGCUEXX'] == "7") {
				$nCosyGas += ($mCocDat[$i]['commovxx'] == "D") ? ($mCocDat[$i]['comvlrxx']*-1) : $mCocDat[$i]['comvlrxx']; //Costos
			} elseif ($mCocDat[$i]['DIGCUEXX'] == "4") {
				$nIP    += ($mCocDat[$i]['commovxx'] == "D") ? ($mCocDat[$i]['comvlrxx']*-1) : $mCocDat[$i]['comvlrxx']; //Ingresos Propios
			}
		}
		
		//Busando Documento que cancela la factura
		unset($vAxuFac); $nImpFin = 0;
		for ($nF=0; $nF<count($vFactura); $nF++) {
			$vAxuFac = explode("~", $vFactura[$nF]);
			
			if (in_array("{$vAxuFac[0]}-{$vAxuFac[1]}-{$vAxuFac[2]}-{$vAxuFac[3]}",$vPagAnu) == true) { //Factua Anulada
				$vFactura[$nF] = "";
			} else {
					
				$nImpFin += $v4xMil["{$vAxuFac[0]}~{$vAxuFac[1]}~{$vAxuFac[2]}~{$vAxuFac[3]}"];
					
				#Buscando el comprobante que cancela la factura
	      #Buscando si la factura tienen cuantas por cobrar y cuentas por pagar para no buscar el documento que cancela factura
				$qCxC  = "SELECT DISTINCT comidxxx,comcodxx,comcscxx ";
				$qCxC .= "FROM $cAlfa.fcxc0000 ";
		    $qCxC .= "WHERE ";
		    $qCxC .= "comidxxx = \"{$vAxuFac[0]}\" AND ";
		    $qCxC .= "comcodxx = \"{$vAxuFac[1]}\" AND ";
		    $qCxC .= "comcscxx = \"{$vAxuFac[2]}\"";
		    $xCxC  = mysql_query($qCxC,$xConexion01);
		    
		    $qCxP  = "SELECT DISTINCT comidxxx,comcodxx,comcscxx ";
	      $qCxP .= "FROM $cAlfa.fcxp0000 ";
	      $qCxP .= "WHERE ";
	      $qCxP .= "comidxxx = \"{$vAxuFac[0]}\" AND ";
	      $qCxP .= "comcodxx = \"{$vAxuFac[1]}\" AND ";
	      $qCxP .= "comcscxx = \"{$vAxuFac[2]}\"";
	      $xCxP  = mysql_query($qCxP,$xConexion01);
	      
	      if (mysql_num_rows($xCxC) == 0 && mysql_num_rows($xCxP) == 0) {
	        #Busco el documento que cancela la factura
	        $nAno01 = substr($vAxuFac[4],0,4);
	        $cTabDet = $mTabDet[$nAno01];
	        $qFcod  = "SELECT ";
		      $qFcod .= "$cAlfa.fcod$nAno01.comidxxx, ";
		      $qFcod .= "$cAlfa.fcod$nAno01.comcodxx, ";
		      $qFcod .= "$cAlfa.fcod$nAno01.comcscxx, ";
		      $qFcod .= "$cAlfa.fcod$nAno01.regfcrex ";
		      $qFcod .= "FROM $cAlfa.fcod$nAno01 ";
		      $qFcod .= "WHERE ";
		      $qFcod .= "$cAlfa.fcod$nAno01.comidxxx != \"F\" AND ";
		      $qFcod .= "$cAlfa.fcod$nAno01.comidcxx = \"{$vAxuFac[0]}\" AND ";
		      $qFcod .= "$cAlfa.fcod$nAno01.comcodcx = \"{$vAxuFac[1]}\" AND ";
		      $qFcod .= "$cAlfa.fcod$nAno01.comcsccx = \"{$vAxuFac[2]}\" AND ";
		      $qFcod .= "$cAlfa.fcod$nAno01.regestxx = \"ACTIVO\" ";
		      $qFcod .= "ORDER BY $cAlfa.fcod$nAno01.regfcrex DESC LIMIT 0,1";
		      $xFcod  = mysql_query($qFcod,$xConexion01);
					//f_Mensaje(__FILE__, __LINE__, $qFcod."~".mysql_num_rows($xFcod));
		      while ($xRF = mysql_fetch_array($xFcod)) {
		        $cCanFac = "{$xRF['comidxxx']}-{$xRF['comcodxx']}-{$xRF['comcscxx']}, ";	
		      }
					mysql_free_result($xFcod);			    
	      }
				mysql_free_result($xCxC);
				mysql_free_result($xCxP);
      }
		}
		
		$nAno = substr($xRDD['regfcrex'], 0,4);
		switch ($cTipo) {
			case 1: // PINTA POR PANTALLA// 
			 	if ($cAno <> $nAno) {
			  	if($cAno <> "") { ?>
			  		<tr bgcolor="#E3F6CE">
							<td class="name" colspan="5" style="padding-left:5px;padding-right:5px;text-align:right">TOTALES <?php echo $mSucOpe[$cSucAux] ?></td>
							<td class="name" style="padding-left:5px;padding-right:5px;text-align:right"><?php echo number_format($nValFacS,0,',','.') ?></td>
							<td class="name" style="padding-left:5px;padding-right:5px;text-align:right"><?php echo number_format($nValCosS,0,',','.') ?></td>
							<td class="name" style="padding-left:5px;padding-right:5px;text-align:right"><?php echo number_format($nInoS,0,',','.') ?></td>
							<td class="name" colspan="5" style="padding-left:5px;padding-right:5px;text-align:right">&nbsp;</td>
						</tr>
						<tr bgcolor="#0B610B">
							<td class="name" colspan="5" style="color:#FFFFFF;padding-left:5px;padding-right:5px;text-align:right">TOTALES</td>
							<td class="name" style="color:#FFFFFF;padding-left:5px;padding-right:5px;text-align:right"><?php echo number_format($nValFacT,0,',','.') ?></td>
							<td class="name" style="color:#FFFFFF;padding-left:5px;padding-right:5px;text-align:right"><?php echo number_format($nValCosT,0,',','.') ?></td>
							<td class="name" style="color:#FFFFFF;padding-left:5px;padding-right:5px;text-align:right"><?php echo number_format($nInoT,0,',','.') ?></td>
							<td class="name" colspan="5" style="padding-left:5px;padding-right:5px;text-align:right">&nbsp;</td>
						</tr>
			  		<?php
						//subtotal 
					 	$mSucTotal[$cSucAux]['succomxx'] = $cSucAux;
					 	$mSucTotal[$cSucAux]['vlrfacsx'] = $nValFacS;
					 	$mSucTotal[$cSucAux]['vlrcossx'] = $nValCosS;
					 	$mSucTotal[$cSucAux]['vlrinosx'] = $nInoS;
					 	$mTotSuc[$cSucAux] = $nConSuc;
			
			 			$mTotales[$cAno][0] = ($nTotalDo > 0)?$nTotalDo:0;
						$mTotales[$cAno][1] = ($nActivos > 0)?$nActivos:0;
					 	$mTotales[$cAno][2] = ($nFacturados > 0)?$nFacturados:0;
						$mTotales[$cAno][3] = $mTotSuc;
			 
					  $j = 0; 
					  $cSucAux   = "";
					  $cAno      = "";  
					  $nValFacT  = 0; //Sumatoria Valor Facturado
					  $nValCosT  = 0; //Sumatoria Valor Costo
					 	$nInoT     = 0; //Sumatoria Ino
					    
					 	$nValFacS  = 0; //Sumatoria por sucursal Valor Facturado
					 	$nValCosS  = 0; //Sumatoria por sucursal Valor Costo
					 	$nInoS     = 0; //Sumatoria por sucursal Ino
					 	$nConSuc   = 0; //Do por Sucursal
			    
			 			$mTotSuc  = array(); //Matriz con total de Do por sucursal
			 			$mSucTotal= array(); //Matriz con los totales por sucursal
			    
			 			$nActivos    = 0; //Numero de Do activos
			 			$nFacturados = 0; //Numero de Do facturados
			 			$nTotalDo    = 0; //Numero total de Do 
						?>
						</table>
					<?php } ?>
					</br>
					</br>
					<table width="95%" cellpadding="1" cellspacing="1" border="0">
						<tr bgcolor="#0B610B"" style="vertical-align:center;text-align:center">
							<td class="name" colspan="13"><center><h2 style="margin-bottom:5px;color:#FFFFFF">A&Ntilde;O <?php echo $nAno ?></h2></center></td>
						</tr>
						<tr bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>" style="vertical-align:center;text-align:center">
							<td class="name" width="100">DO</td>
							<td class="name" width="150">TIPO DE OPERACION</td>
							<td class="name" width="120">NIT</td>
							<td class="name">CLIENTE</td>
							<td class="name" width="100">ESTADO</td>
							<td class="name" width="120">VALOR FACTURADO</td>
							<td class="name" width="120">VALOR COSTO</td>
							<td class="name" width="120">INO</td>
							<td class="name">FACTURA DE VENTA</td>
							<td class="name">DOCUMENTO CANCELA FACTURA</td>
							<!--<td class="name" width="80">SUCURSAL</td>-->
							<td class="name" width="80">SUC COMERCIAL</td>
							<td class="name" width="80">SUC OPERATIVA</td>
							<td class="name">VENDEDOR</td>
						</tr> 
				 <?php
				 $cAno = $nAno;
				}
			
			  if (($j % 2) == 0){
			  	$color = '#FFFFFF';
				} else {
			  	$color = '#E5E5E5';
				}   
			
				$nImpFin = ($nCal4xMil > 0) ? 0 : $nImpFin; //si ya eta incluido en los ip, no se suma
				$nValFac = $nPTFac + $nIP + $nImpFin;
        $nValCos = $nPT + $nImpFin - $nCosyGas;
				$nIno    = $nIP + $nCosyGas;  
			
				if($cSucAux == "") {
			 		$cSucAux = $xRDD['succomxx'];
				}
				if($cSucAux <> $xRDD['succomxx'] || $cAno <> $nAno){ //subtotal ?>
					<tr bgcolor="#E3F6CE">
						<td class="name" colspan="5" style="padding-left:5px;padding-right:5px;text-align:right">TOTALES <?php echo $mSucOpe[$cSucAux] ?></td>
						<td class="name" style="padding-left:5px;padding-right:5px;text-align:right"><?php echo number_format($nValFacS,0,',','.') ?></td>
						<td class="name" style="padding-left:5px;padding-right:5px;text-align:right"><?php echo number_format($nValCosS,0,',','.') ?></td>
						<td class="name" style="padding-left:5px;padding-right:5px;text-align:right"><?php echo number_format($nInoS,0,',','.') ?></td>
						<td class="name" colspan="5" style="padding-left:5px;padding-right:5px;text-align:right">&nbsp;</td>
					</tr>
				<?php 
			 		//subtotal              
			 		$mSucTotal[$cSucAux]['succomxx'] = $cSucAux;
				 	$mSucTotal[$cSucAux]['vlrfacsx'] = $nValFacS;
				 	$mSucTotal[$cSucAux]['vlrcossx'] = $nValCosS;
				 	$mSucTotal[$cSucAux]['vlrinosx'] = $nInoS;									 
				 	$mTotSuc[$cSucAux] = $nConSuc;
			 
			 		$cSucAux = $xRDD['succomxx'];
			   	$nValFacS  = 0; 
			   	$nValCosS  = 0; 
			   	$nInoS     = 0; 
			   	$nConSuc   = 0;
				} 
			
				$nValFacS  += $nValFac; 
				$nValCosS  += $nValCos; 
				$nInoS     += $nIno; 
			
				$nValFacT  += $nValFac; 
				$nValCosT  += $nValCos; 
				$nInoT     += $nIno;
			
				/**
         * Si el DO esta activo, se cuenta sin importar que no tenga ningun
         * valor facturado, valor costo o ino
         */
        if ($xRDD['regestxx'] == "ACTIVO") {
          $nActivos++;
          $nTotalDo++;
          $nConSuc++;
        }
        
        if ($xRDD['regestxx'] == "FACTURADO") {
          /**
           * Se cuenta si tiene valor facturado, valor costo o ino
           */
          if ($nValFac != 0 || $nValCos != 0 || $nIno != 0) {
            $nFacturados++;
            $nTotalDo++;
            $nConSuc++;
          }
        }
			
				unset($vAxuFac);
				for ($nF=0; $nF<count($vFactura); $nF++) {
					if ($vFactura[$nF] != "") {
						$vAxuFac = explode("~", $vFactura[$nF]);
						$cFactura .= "<a href = |javascript:f_Imprimir(~{$vAxuFac[0]}~,~{$vAxuFac[1]}~,~{$vAxuFac[2]}~,~{$vAxuFac[3]}~,~{$vAxuFac[4]}~)|>{$vAxuFac[0]}-{$vAxuFac[1]}-{$vAxuFac[2]}</a>, ";
					}
				}
				$cFactura = substr($cFactura, 0, -2);
				
				$cFac    = str_replace("|","\"",$cFactura);
				$cFac    = str_replace("~","'",$cFac);
				$cCanFac  = substr($cCanFac, 0, -2);
				
				$cColDif = (($nValFac - $nValCos) != $nIno) ? "color: blue" : "color: black";
				$cStyle  = ($nIno < 0)?";color:red !important;":"";
				?>
				<tr style="background-color:<?php echo $color ?><?php echo $cStyle ?>">
					<td class="letra7" style="padding-left:5px;padding-right:5px;">
					 <?php echo "<a href = \"javascript:f_Movimiento_Do('{$xRDD['comidxxx']}', '{$xRDD['comcodxx']}', '{$xRDD['sucidxxx']}', '{$xRDD['doctipxx']}', '{$xRDD['docidxxx']}', '{$xRDD['docsufxx']}', '{$xRDD['pucidxxx']}', '{$xRDD['ccoidxxx']}', '{$xRDD['cliidxxx']}', '{$xRDD['regfcrex']}')\">{$xRDD['docidxxx']}-{$xRDD['docsufxx']}</a>" ?>
					</td>
					<td class="letra7" style="padding-left:5px;padding-right:5px;"><?php echo $xRDD['doctipxx'] ?></td>
					<td class="letra7" style="padding-left:5px;padding-right:5px;"><?php echo $xRDD['cliidxxx'] ?></td>
					<td class="letra7" style="padding-left:5px;padding-right:5px;"><?php echo $xRDD['clinomxx'] ?></td>
					<td class="letra7" style="padding-left:5px;padding-right:5px;"><?php echo $xRDD['regestxx'] ?></td>
					<td class="letra7" style="padding-left:5px;padding-right:5px;text-align:right"><?php echo number_format($nValFac,0,',','.') ?></td>
					<td class="letra7" style="padding-left:5px;padding-right:5px;text-align:right;<?php echo $cColDif ?>"><?php echo number_format($nValCos,0,',','.') ?></td>
					<td class="letra7" style="padding-left:5px;padding-right:5px;text-align:right"><?php echo number_format($nIno,0,',','.') ?></td>
					<td class="letra7" style="padding-left:5px;padding-right:5px;"><?php echo ($cFac<>"")?$cFac:"&nbsp;" ?></td>
					<td class="letra7" style="padding-left:5px;padding-right:5px;"><?php echo ($cCanFac<>"")?$cCanFac:"&nbsp;" ?></td>
					<td class="letra7" style="padding-left:5px;padding-right:5px;text-align:center"><?php echo ($mSucCom[$xRDD['succomxx']])?$mSucCom[$xRDD['succomxx']]:(($xRDD['succomxx'])?$xRDD['succomxx']:"&nbsp;") ?></td>
					<td class="letra7" style="padding-left:5px;padding-right:5px;text-align:center"><?php echo ($mSucOpe[$xRDD['sucidxxx']])?$mSucOpe[$xRDD['sucidxxx']]:(($xRDD['sucidxxx'])?$xRDD['sucidxxx']:"&nbsp;")  ?></td>
					<td class="letra7" style="padding-left:5px;padding-right:5px;"><?php echo $xRDD['nomvenxx'] ?></td>
				</tr>
				<?php $j++;
			break;
			case 2:
				if ($cAno <> $nAno) {
			  	if($cAno <> "") {
						$data .= '<tr bgcolor="#E3F6CE">';
						$data .= '<td colspan="5" style="border:1px solid;padding-left:5px;padding-right:5px;text-align:right">TOTALES '.$mSucOpe[$cSucAux].'</td>';
						$data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;text-align:right">'.number_format($nValFacS,0,',','.').'</td>';
						$data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;text-align:right">'.number_format($nValCosS,0,',','.').'</td>';
						$data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;text-align:right">'.number_format($nInoS,0,',','.').'</td>';
						$data .= '<td colspan="5" style="border:1px solid;padding-left:5px;padding-right:5px;text-align:right">&nbsp;</td>';
						$data .= '</tr>'; 
						
						$data .= '<tr bgcolor="#0B610B">';
							$data .= '<td class="name" colspan="5" style="color:#FFFFFF;padding-left:5px;padding-right:5px;text-align:right">TOTALES</td>';
							$data .= '<td class="name" style="color:#FFFFFF;padding-left:5px;padding-right:5px;text-align:right">'.number_format($nValFacT,0,',','.').'</td>';
							$data .= '<td class="name" style="color:#FFFFFF;padding-left:5px;padding-right:5px;text-align:right">'.number_format($nValCosT,0,',','.').'</td>';
							$data .= '<td class="name" style="color:#FFFFFF;padding-left:5px;padding-right:5px;text-align:right">'.number_format($nInoT,0,',','.').'</td>';
							$data .= '<td class="name" colspan="5" style="padding-left:5px;padding-right:5px;text-align:right">&nbsp;</td>';
						$data .= '</tr>';
						//subtotal 
						$mSucTotal[$cSucAux]['succomxx'] = $cSucAux;
						$mSucTotal[$cSucAux]['vlrfacsx'] = $nValFacS;
						$mSucTotal[$cSucAux]['vlrcossx'] = $nValCosS;
						$mSucTotal[$cSucAux]['vlrinosx'] = $nInoS;
						$mTotSuc[$cSucAux] = $nConSuc;
			
						$mTotales[$cAno][0] = ($nTotalDo > 0)?$nTotalDo:0;
						$mTotales[$cAno][1] = ($nActivos > 0)?$nActivos:0;
						$mTotales[$cAno][2] = ($nFacturados > 0)?$nFacturados:0;
						$mTotales[$cAno][3] = $mTotSuc;
			 
						$j = 0; 
						$cSucAux   = "";
						$cAno      = "";  
						$nValFacT  = 0; //Sumatoria Valor Facturado
						$nValCosT  = 0; //Sumatoria Valor Costo
						$nInoT     = 0; //Sumatoria Ino
					    
						$nValFacS  = 0; //Sumatoria por sucursal Valor Facturado
						$nValCosS  = 0; //Sumatoria por sucursal Valor Costo
						$nInoS     = 0; //Sumatoria por sucursal Ino
						$nConSuc   = 0; //Do por Sucursal
			    
			 			$mTotSuc  = array(); //Matriz con total de Do por sucursal
			 			$mSucTotal= array(); //Matriz con los totales por sucursal
			    
			 			$nActivos    = 0; //Numero de Do activos
			 			$nFacturados = 0; //Numero de Do facturados
			 			$nTotalDo    = 0; //Numero total de Do 
					}
						
					$data .='</br></br></tabla><table width="95%" cellpadding="1" cellspacing="1" border="0">';
					$data .= '<tr>';
						$data .= '<td class="name" colspan="5" style="padding-left:5px;padding-right:5px;text-align:right">&nbsp;</td>';
					$data .= '</tr>';
					$data .= '<tr bgcolor="#0B610B"" style="vertical-align:center;text-align:center">';
					$data .= '<td class="name" colspan="13"><center><h2 style="margin-bottom:5px;color:#FFFFFF">A&Ntilde;O'.$nAno.'</h2></center></td>';
					$data .= '</tr>';
					$data .= '<tr style="vertical-align:center;text-align:center">';
					$data .= '<td bgcolor="'.$vSysStr['system_row_title_color_ini'].'" style="border:1px solid;" width="150">DO</td>';
					$data .= '<td bgcolor="'.$vSysStr['system_row_title_color_ini'].'" style="border:1px solid;" width="150">TIPO DE OPERACION</td>';
					$data .= '<td bgcolor="'.$vSysStr['system_row_title_color_ini'].'" style="border:1px solid;" width="120">NIT</td>';
					$data .= '<td bgcolor="'.$vSysStr['system_row_title_color_ini'].'" style="border:1px solid;">CLIENTE</td>';
					$data .= '<td bgcolor="'.$vSysStr['system_row_title_color_ini'].'" style="border:1px solid;" width="100">ESTADO</td>';
					$data .= '<td bgcolor="'.$vSysStr['system_row_title_color_ini'].'" style="border:1px solid;" width="120">VALOR FACTURADO</td>';
					$data .= '<td bgcolor="'.$vSysStr['system_row_title_color_ini'].'" style="border:1px solid;" width="120">VALOR COSTO</td>';
					$data .= '<td bgcolor="'.$vSysStr['system_row_title_color_ini'].'" style="border:1px solid;" width="120">INO</td>';
					$data .= '<td bgcolor="'.$vSysStr['system_row_title_color_ini'].'" style="border:1px solid;">FACTURA DE VENTA</td>';
					$data .= '<td bgcolor="'.$vSysStr['system_row_title_color_ini'].'" style="border:1px solid;">DOCUMENTO CANCELA FACTURA</td>';
					$data .= '<td bgcolor="'.$vSysStr['system_row_title_color_ini'].'" style="border:1px solid;" width="80">SUC COMERCIAL</td>';
					$data .= '<td bgcolor="'.$vSysStr['system_row_title_color_ini'].'" style="border:1px solid;" width="80">SUC OPERATIVA</td>';
					$data .= '<td bgcolor="'.$vSysStr['system_row_title_color_ini'].'" style="border:1px solid;">VENDEDOR</td>';
					$data .= '</tr>';
				
				 $cAno = $nAno;
				}

				if (($j % 2) == 0){
					$color = '#FFFFFF';
				} else {
					$color = '#E5E5E5';
				}
				            
				$nImpFin = ($nCal4xMil > 0) ? 0 : $nImpFin; //si ya eta incluido en los ip, no se suma
				$nValFac = $nPTFac + $nIP + $nImpFin;
        $nValCos = $nPT + $nImpFin - $nCosyGas;
				$nIno    = $nIP + $nCosyGas;  
			
				if($cSucAux == "") {
			 		$cSucAux = $xRDD['succomxx'];
				}
				if($cSucAux <> $xRDD['succomxx']){ //subtotal
					$data .= '<tr bgcolor="#E3F6CE">';
					$data .= '<td colspan="5" style="border:1px solid;padding-left:5px;padding-right:5px;text-align:right">TOTALES '.$mSucOpe[$cSucAux].'</td>';
					$data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;text-align:right">'.number_format($nValFacS,0,',','.').'</td>';
					$data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;text-align:right">'.number_format($nValCosS,0,',','.').'</td>';
					$data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;text-align:right">'.number_format($nInoS,0,',','.').'</td>';
					$data .= '<td colspan="5" style="border:1px solid;padding-left:5px;padding-right:5px;text-align:right">&nbsp;</td>';
					$data .= '</tr>';
					//subtotal              
					$mSucTotal[$cSucAux]['succomxx'] = $cSucAux;
				 	$mSucTotal[$cSucAux]['vlrfacsx'] = $nValFacS;
				 	$mSucTotal[$cSucAux]['vlrcossx'] = $nValCosS;
				 	$mSucTotal[$cSucAux]['vlrinosx'] = $nInoS;									 
				 	$mTotSuc[$cSucAux] = $nConSuc;
			 
			 		$cSucAux = $xRDD['succomxx'];
			   	$nValFacS  = 0; 
			   	$nValCosS  = 0; 
			   	$nInoS     = 0; 
			   	$nConSuc   = 0;
				} 
			
				$nValFacS  += $nValFac; 
				$nValCosS  += $nValCos; 
				$nInoS     += $nIno; 
			
				$nValFacT  += $nValFac; 
				$nValCosT  += $nValCos; 
				$nInoT     += $nIno;
			
				/**
         * Si el DO esta activo, se cuenta sin importar que no tenga ningun
         * valor facturado, valor costo o ino
         */
        if ($xRDD['regestxx'] == "ACTIVO") {
          $nActivos++;
          $nTotalDo++;
          $nConSuc++;
        }
        
        if ($xRDD['regestxx'] == "FACTURADO") {
          /**
           * Se cuenta si tiene valor facturado, valor costo o ino
           */
          if ($nValFac != 0 || $nValCos != 0 || $nIno != 0) {
            $nFacturados++;
            $nTotalDo++;
            $nConSuc++;
          }
        }
			
				unset($vAxuFac);
				for ($nF=0; $nF<count($vFactura); $nF++) {
					if ($vFactura[$nF] != "") {
						$vAxuFac = explode("~", $vFactura[$nF]);
						$cFactura .= "{$vAxuFac[0]}-{$vAxuFac[1]}-{$vAxuFac[2]}";
					}
				}
				//$cFactura = substr($cFactura, 0, -2);
				
				$cFac    = str_replace("|","\"",$cFactura);
				$cFac    = str_replace("~","'",$cFac);
				$cCanFac  = $cCanFac;//substr($cCanFac, 0, -2);
				
				$cColDif = (($nValFac - $nValCos) != $nIno) ? "color: blue" : "color: black";
				$cStyle  = ($nIno < 0)?";color:red !important;":"";
				            
				$data .= '<tr>';
				$data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;">'."{$xRDD['docidxxx']}-{$xRDD['docsufxx']}".'</td>';
				$data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;">'.$xRDD['doctipxx'].'</td>';
				$data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;">'.$xRDD['cliidxxx'].'</td>';
				$data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;">'.$xRDD['clinomxx'].'</td>';
				$data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;">'.$xRDD['regestxx'].'</td>';
				$data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;text-align:right">'.number_format($nValFac,0,',','.').'</td>';
				$data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;text-align:right;'.$cColDif.'">'.number_format($nValCos,0,',','.').'</td>';
				$data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;text-align:right">'.number_format($nIno,0,',','.').'</td>';
				$data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;">'.(($cFac<>"")?$cFac:"&nbsp;").'</td>';
				$data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;">'.(($cCanFac<>"")?$cCanFac:"&nbsp;").'</td>';
				$data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;text-align:center">'.(($mSucCom[$xRDD['succomxx']])?$mSucCom[$xRDD['succomxx']]:(($xRDD['succomxx'])?$xRDD['succomxx']:"&nbsp;")).'</td>';
				$data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;text-align:center">'.(($mSucCom[$xRDD['sucidxxx']])?$mSucCom[$xRDD['sucidxxx']]:(($xRDD['sucidxxx'])?$xRDD['sucidxxx']:"&nbsp;")).'</td>';
				$data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;">'.$xRDD['nomvenxx'].'</td>';
				$data .= '</tr>';
				$j++;
			
			break;
		}
  }

	$mSucTotal[$cSucAux]['succomxx'] = $cSucAux;
 	$mSucTotal[$cSucAux]['vlrfacsx'] = $nValFacS;
 	$mSucTotal[$cSucAux]['vlrcossx'] = $nValCosS;
 	$mSucTotal[$cSucAux]['vlrinosx'] = $nInoS;
 	$mTotSuc[$cSucAux] = $nConSuc;

	$mTotales[$cAno][0] = ($nTotalDo > 0)?$nTotalDo:0;
	$mTotales[$cAno][1] = ($nActivos > 0)?$nActivos:0;
 	$mTotales[$cAno][2] = ($nFacturados > 0)?$nFacturados:0;
	$mTotales[$cAno][3] = $mTotSuc;
  
	switch ($cTipo) {
		case 1: // PINTA POR PANTALLA//
		//if( $cAplCom == "NO" ) {
		?>
		<tr bgcolor="#E3F6CE">
			<td class="name" colspan="5" style="padding-left:5px;padding-right:5px;text-align:right">TOTALES <?php echo $mSucOpe[$cSucAux] ?></td>
			<td class="name" style="padding-left:5px;padding-right:5px;text-align:right"><?php echo number_format($nValFacS,0,',','.') ?></td>
			<td class="name" style="padding-left:5px;padding-right:5px;text-align:right"><?php echo number_format($nValCosS,0,',','.') ?></td>
			<td class="name" style="padding-left:5px;padding-right:5px;text-align:right"><?php echo number_format($nInoS,0,',','.') ?></td>
			<td class="name" colspan="5" style="padding-left:5px;padding-right:5px;text-align:right">&nbsp;</td>
		</tr>
		<tr>
			<td class="name" colspan="5" style="padding-left:5px;padding-right:5px;text-align:right">&nbsp;</td>
		</tr>
      <tr bgcolor="#0B610B">
        <td class="name" colspan="5" style="color:#FFFFFF;padding-left:5px;padding-right:5px;text-align:right">TOTALES</td>
        <td class="name" style="color:#FFFFFF;padding-left:5px;padding-right:5px;text-align:right"><?php echo number_format($nValFacT,0,',','.') ?></td>
        <td class="name" style="color:#FFFFFF;padding-left:5px;padding-right:5px;text-align:right"><?php echo number_format($nValCosT,0,',','.') ?></td>
        <td class="name" style="color:#FFFFFF;padding-left:5px;padding-right:5px;text-align:right"><?php echo number_format($nInoT,0,',','.') ?></td>
        <td class="name" colspan="5" style="padding-left:5px;padding-right:5px;text-align:right">&nbsp;</td>
      </tr>
    </table>
    <br><br>
  	<table width="300" cellpadding="1" cellspacing="1" border="0">
      <tr>
         <td bgcolor="#0B610B" class="name" style="color:#FFFFFF;padding-left:5px;padding-right:5px;text-align:left;font-weight:bold">&nbsp;</td>
         <?php foreach ($mTotales as $cKeyAno => $cValueAno) { ?>
           <td bgcolor="#0B610B" class="name" style="color:#FFFFFF;padding-left:5px;padding-right:5px;text-align:right"><h3><?php echo $cKeyAno ?></h3></td>
         <?php } ?>
     </tr> 
     <tr>
         <td bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>" class="name" style="padding-left:5px;padding-right:5px;text-align:left;font-weight:bold">DO APERTURADOS</td>
         <?php foreach ($mTotales as $cKeyAno => $cValueAno) { ?>
           <td bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>" class="name" style="padding-left:5px;padding-right:5px;text-align:right"><?php echo $mTotales[$cKeyAno][0] ?></td>
         <?php } ?>
     </tr> 
     <tr>
       <td bgcolor="#FFFFFF" class="name" style="padding-left:5px;padding-right:5px;text-align:left;font-weight:bold">DO ACTIVOS</td>
       <?php foreach ($mTotales as $cKeyAno => $cValueAno) { ?>
         <td bgcolor="#FFFFFF" class="name" style="padding-left:5px;padding-right:5px;text-align:right"><?php echo $mTotales[$cKeyAno][1] ?></td>
       <?php } ?>
     </tr> 
     <tr>
       <td bgcolor="#E5E5E5" class="name" style="padding-left:5px;padding-right:5px;text-align:left;font-weight:bold">DO FACTURADOS</td>
       <?php foreach ($mTotales as $cKeyAno => $cValueAno) { ?>
         <td bgcolor="#E5E5E5" class="name" style="padding-left:5px;padding-right:5px;text-align:right"><?php echo $mTotales[$cKeyAno][2] ?></td>
       <?php } ?>
     </tr> 
     <?php 
     $y = 0;
		if ($cAplCom == "NO") {
			foreach ($mTotales as $cKeyAno => $cValueAno) { 
				foreach ($mTotales[$cKeyAno][3] as $cSuc => $cValueSuc) { 
					if (($y % 2) == 0){
					$color = '#FFFFFF'; 
			} else {
				$color = '#E5E5E5';
			}
			$y++; ?>
			<tr>
			<td bgcolor="<?php echo $color ?>" class="name" style="padding-left:5px;padding-right:5px;text-align:left;font-weight:bold">DO SUCURSAL <?php echo ($mSucOpe[$cSuc])?$mSucOpe[$cSuc]:(($cSuc)?$cSuc:"&nbsp;") ?></td>
			<td bgcolor="<?php echo $color ?>" class="name" style="padding-left:5px;padding-right:5px;text-align:right"><?php echo $mTotales[$cKeyAno][3][$cSuc] ?></td>
			</tr>
			<?php }
			}
		} else {
			$mTotSuc = array();
			foreach ($mTotales as $cKeyAno => $cValueAno) { 
				foreach ($mTotales[$cKeyAno][3] as $cKey => $cValue) {
					$mTotSuc[$cKey][$cKeyAno] = $cValue;
				}
			}
			foreach ($mTotSuc as $cKey => $cValue) { 
				if (($y % 2) == 0){
					$color = '#FFFFFF'; 
				} else {
					$color = '#E5E5E5';
				}
				$y++; ?>
				<tr>
				<td bgcolor="<?php echo $color ?>" class="name" style="padding-left:5px;padding-right:5px;text-align:left;font-weight:bold">DO SUCURSAL <?php echo ($mSucOpe[$cKey])?$mSucOpe[$cKey]:(($cKey)?$cKey:"&nbsp;") ?></td>
				<td bgcolor="<?php echo $color ?>" class="name" style="padding-left:5px;padding-right:5px;text-align:right"><?php echo ($mTotSuc[$cKey][$cAnoIni] > 0)?$mTotSuc[$cKey][$cAnoIni]:"&nbsp;" ?></td>
				<td bgcolor="<?php echo $color ?>" class="name" style="padding-left:5px;padding-right:5px;text-align:right"><?php echo ($mTotSuc[$cKey][$cAnoFin] > 0)?$mTotSuc[$cKey][$cAnoFin]:"&nbsp;" ?></td>
				</tr>
			<?php 
			} 
		} ?>
  </table>
	</center>	
	</body>
	</html>
  <?php break;
  case 2:
	// PINTA POR EXCEL//
		$data .= '<tr bgcolor="#E3F6CE">';
		$data .= '<td colspan="5" style="border:1px solid;padding-left:5px;padding-right:5px;text-align:right">TOTALES '.$mSucOpe[$cSucAux].'</td>';
		$data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;text-align:right">'.number_format($nValFacS,0,',','.').'</td>';
		$data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;text-align:right">'.number_format($nValCosS,0,',','.').'</td>';
		$data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;text-align:right">'.number_format($nInoS,0,',','.').'</td>';
		$data .= '<td colspan="5" style="border:1px solid;padding-left:5px;padding-right:5px;text-align:right">&nbsp;</td>';
		$data .= '</tr>';
		
		$data .= '<tr>';
			$data .= '<td class="name" colspan="5" style="padding-left:5px;padding-right:5px;text-align:right">&nbsp;</td>';
		$data .= '</tr>';
		
		$data .= '<tr bgcolor="#0B610B">';
		$data .= '<td class="name" colspan="5" style="color:#FFFFFF;padding-left:5px;padding-right:5px;text-align:right">TOTALES</td>';
		$data .= '<td class="name" style="color:#FFFFFF;padding-left:5px;padding-right:5px;text-align:right">'.number_format($nValFacT,0,',','.').'</td>';
		$data .= '<td class="name" style="color:#FFFFFF;padding-left:5px;padding-right:5px;text-align:right">'.number_format($nValCosT,0,',','.').'</td>';
		$data .= '<td class="name" style="color:#FFFFFF;padding-left:5px;padding-right:5px;text-align:right">'.number_format($nInoT,0,',','.').'</td>';
		$data .= '<td class="name" colspan="5" style="padding-left:5px;padding-right:5px;text-align:right">&nbsp;</td>';
		$data .= '</tr>';
			
		$data .= '<tr><td colspan="12"></td></tr>';
			$data .= '<tr>';
				$n = 1;
				$data .= '<td  bgcolor="#0B610B" style="border:1px solid;color:#FFFFFF;padding-left:5px;padding-right:5px;text-align:left;font-weight:bold"></td>';
				foreach ($mTotales as $cKeyAno => $cValueAno) {
					$n++;
					$data .= '<td bgcolor="#0B610B" style="border:1px solid;color:#FFFFFF;padding-left:5px;padding-right:5px;text-align:right"><h3>'.$cKeyAno.'</h3></td>';
				}
			$data .= '<td colspan="'.(12-$n).'"></td>';      	  
		$data .= '</tr>'; 
		$data .= '<tr>';
			$data .= '<td bgcolor="'.$vSysStr['system_row_title_color_ini'].'" style="border:1px solid;padding-left:5px;padding-right:5px;text-align:left;font-weight:bold">DO APERTURADOS</td>';
				foreach ($mTotales as $cKeyAno => $cValueAno) {
					$data .= '<td bgcolor="'.$vSysStr['system_row_title_color_ini'].'" style="border:1px solid;padding-left:5px;padding-right:5px;text-align:right">'.$mTotales[$cKeyAno][0].'</td>';
				} 
			$data .= '<td colspan="'.(12-$n).'"></td>';      	  
		$data .= '</tr>'; 
			$data .= '<tr>';
				$data .= '<td bgcolor="#FFFFFF" style="border:1px solid;padding-left:5px;padding-right:5px;text-align:left;font-weight:bold">DO ACTIVOS</td>';
				foreach ($mTotales as $cKeyAno => $cValueAno) {
					$data .= '<td bgcolor="#FFFFFF" style="border:1px solid;padding-left:5px;padding-right:5px;text-align:right">'.$mTotales[$cKeyAno][1].'</td>';
				}
			$data .= '<td colspan="'.(12-$n).'"></td>';
		$data .= '</tr>'; 
		$data .= '<tr>';
			$data .= '<td bgcolor="#FFFFFF" style="border:1px solid;padding-left:5px;padding-right:5px;text-align:left;font-weight:bold">DO FACTURADOS</td>';
				foreach ($mTotales as $cKeyAno => $cValueAno) {
					$data .= '<td bgcolor="#FFFFFF" style="border:1px solid;padding-left:5px;padding-right:5px;text-align:right">'.$mTotales[$cKeyAno][2].'</td>';
				}
			$data .= '<td colspan="'.(12-$n).'"></td>';
			$data .= '</tr>'; 

			$y = 0;
			if ($cAplCom == "NO") {
				foreach ($mTotales as $cKeyAno => $cValueAno) { 
  	     foreach ($mTotales[$cKeyAno][3] as $cSuc => $cValueSuc) { 
  	       if (($y % 2) == 0){
              $color = '#FFFFFF'; 
            } else {
              $color = '#FFFFFF';
            }
      		  $y++; 
      	 $data .= '<tr>';
    	     $data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;text-align:left;font-weight:bold">DO SUCURSAL '.(($mSucOpe[$cSuc])?$mSucOpe[$cSuc]:(($cSuc)?$cSuc:"")).'</td>';
      	   $data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;text-align:right">'.$mTotales[$cKeyAno][3][$cSuc].'</td>';
      	   $data .= '<td colspan="'.(13-$n).'"></td>';
      	$data .= '</tr>';
    	 }
  	   }
  	 } else {
    	  $mTotSuc = array();
    	  foreach ($mTotales as $cKeyAno => $cValueAno) { 
      	   foreach ($mTotales[$cKeyAno][3] as $cKey => $cValue) {
      	     $mTotSuc[$cKey][$cKeyAno] = $cValue;
      	   }
      	}
      	foreach ($mTotSuc as $cKey => $cValue) { 
      	   if (($y % 2) == 0){
              $color = '#FFFFFF'; 
            } else {
              $color = '#FFFFFF';
            }
      		  $y++; 
    	   $data .= '<tr>';
      	   $data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;text-align:left;font-weight:bold">DO SUCURSAL '.(($mSucOpe[$cKey])?$mSucOpe[$cKey]:(($cKey)?$cKey:"")).'</td>';
      	   $data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;text-align:right">'.(($mTotSuc[$cKey][$cAnoIni] > 0)?$mTotSuc[$cKey][$cAnoIni]:"").'</td>';
      	   $data .= '<td style="border:1px solid;padding-left:5px;padding-right:5px;text-align:right">'.(($mTotSuc[$cKey][$cAnoFin] > 0)?$mTotSuc[$cKey][$cAnoFin]:"").'</td>';
      	   $data .= '<td colspan="'.(13-$n).'"></td>';
      	 $data .= '</tr>';
    	 } 
      }
		$data .= '</table>';

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // required for certain browsers
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"".basename($title)."\";");
		
		print $data;
  break;
  }  
?>

<?php
  /**
	 * Imprime Reporte Inventario de Formularios.
	 * --- Descripcion: Permite Imprimir Reporte Inventario de Formularios.
	 * @author Yulieth Campos <ycampos@opentecnologia.com.co>
	 */

	ini_set("memory_limit","512M");
  set_time_limit(0);

  date_default_timezone_set("America/Bogota");

  /**
   * Variable que indica si se debe seguir ejecutando el proceso de la interface despues de cargar los datos en las tablas temporales
   * @var Number
   */
  $cEjePro = 0;

  /**
   * Nombre(s) de los archivos en excel generados
   */
  $cNomFile = "";

  $nSwitch = 0; 	// Variable para la Validacion de los Datos
  $cMsj = "\n"; 	// Variable para Guardar los Errores de las Validaciones

  /**
   * Cuando se ejecuta desde el cron debe armarse la cookie para incluir los utilitys
   */
	if ($_SERVER["SERVER_PORT"] == "") {
		$vArg = explode(",", $argv[1]);

    if ($vArg[0] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
      $cMsj .= "El parametro Id del Proceso no puede ser vacio.\n";
    }

    if ($vArg[1] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
      $cMsj .= "El parametro de la Cookie no puede ser vacio.\n";
    }

    if ($nSwitch == 0) {
      $_COOKIE["kDatosFijos"] = $vArg[1];

      # Librerias
      include("{$OPENINIT['pathdr']}/opencomex/config/config.php");
      include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/utility.php");
      include("{$OPENINIT['pathdr']}/opencomex/libs/php/utiprobg.php");

      /**
       * Buscando el ID del proceso
       */
      $qProBg = "SELECT * ";
      $qProBg .= "FROM $cBeta.sysprobg ";
      $qProBg .= "WHERE ";
      $qProBg .= "pbaidxxx= \"{$vArg[0]}\" AND ";
      $qProBg .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
      $xProBg = f_MySql("SELECT", "", $qProBg, $xConexion01, "");
      if (mysql_num_rows($xProBg) == 0) {
        $xRPB = mysql_fetch_array($xProBg);
        $nSwitch = 1;
        $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
        $cMsj .= "El Proceso en Background [{$vArg[0]}] No Existe o ya fue Procesado.\n";
      } else {
        $xRB = mysql_fetch_array($xProBg);

        /**
         * Reconstruyendo Post
         */
        $mPost = f_Explode_Array($xRB['pbapostx'], "|", "~");
        for ($nP = 0; $nP < count($mPost); $nP++) {
          if ($mPost[$nP][0] != "") {
            $_POST[$mPost[$nP][0]] = $mPost[$nP][1];
          }
        }
      }
		}
  }

  /**
   * Subiendo el archivo al sistema
   */
  if ($_SERVER["SERVER_PORT"] != "") {
    # Librerias
    include("../../../../../config/config.php");
    include("../../../../libs/php/utility.php");
    include("../../../../../libs/php/utiprobg.php");
  }

  if ($_SERVER["SERVER_PORT"] != "") {
    $cEjProBg = ($cEjProBg != "SI") ? "NO" : $cEjProBg;
	}

	if ($_SERVER["SERVER_PORT"] == "") {
		$cTipo	 = $_POST['cTipo'];
		$cPtoId  = $_POST['cPtoId'];
		$dDesde  = $_POST['dDesde'];
		$dHasta  = $_POST['dHasta'];
	}

	if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
		$cEjePro = 1;

    $strPost  = "|cTipo~".$cTipo;
    $strPost .= "|cPtoId~".$cPtoId;
    $strPost .= "|dDesde~".$dDesde;
		$strPost .= "|dHasta~".$dHasta;
		$nRegistros = 0;

    $vParBg['pbadbxxx'] = $cAlfa;                         //Base de Datos
    $vParBg['pbamodxx'] = "FACTURACION";                  //Modulo
    $vParBg['pbatinxx'] = "INVENTARIODEFORMULARIOS";      //Tipo Interface
    $vParBg['pbatinde'] = "INVENTARIO DE FORMULARIOS";    //Descripcion Tipo de Interfaz
    $vParBg['admidxxx'] = "";                             //Sucursal
    $vParBg['doiidxxx'] = "";                             //Do
    $vParBg['doisfidx'] = "";                             //Sufijo
    $vParBg['cliidxxx'] = "";                             //Nit
    $vParBg['clinomxx'] = "";                             //Nombre Importador
    $vParBg['pbapostx'] = $strPost;												//Parametros para reconstruir Post
    $vParBg['pbatabxx'] = "";                             //Tablas Temporales
    $vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];    //Script
    $vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];        //cookie
    $vParBg['pbacrexx'] = $nRegistros;                    //Cantidad Registros
    $vParBg['pbatxixx'] = 1;                              //Tiempo Ejecucion x Item en Segundos
    $vParBg['pbaopcxx'] = "";                             //Opciones
    $vParBg['regusrxx'] = $kUser;                         //Usuario que Creo Registro
  
    #Incluyendo la clase de procesos en background
    $ObjProBg = new cProcesosBackground();
    $mReturnProBg = $ObjProBg->fnCrearProcesoBackground($vParBg);
  
    #Imprimiendo resumen de todo ok.
    if ($mReturnProBg[0] == "true") {
      f_Mensaje(__FILE__, __LINE__, "Proceso en Background Agendado con Exito."); ?>
      <script languaje = "javascript">
          parent.fmwork.fnRecargar();
      </script>
    <?php 
    } else {
      $nSwitch = 1;
      for ($nR = 1; $nR < count($mReturnProBg); $nR++) {
        $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
        $cMsj .= $mReturnProBg[$nR] . "\n";
      }
      f_Mensaje(__FILE__, __LINE__, $cMsj."Verifique.");
    }
  }

  $fec  = date('Y-m-d'); $cMes = "";
  switch (substr($fec,5,2)){
  	case "01": $cMes="ENERO";       break;
    case "02": $cMes="FEBRERO";     break;
    case "03": $cMes="MARZO";       break;
    case "04": $cMes="ABRIL";       break;
    case "05": $cMes="MAYO";        break;
    case "06": $cMes="JUNIO";       break;
    case "07": $cMes="JULIO";       break;
    case "08": $cMes="AGOSTO";      break;
    case "09": $cMes="SEPTIEMBRE";  break;
    case "10": $cMes="OCTUBRE";     break;
    case "11": $cMes="NOVIEMBRE";   break;
    case "12": $cMes="DICIEMBRE";   break;
  }//switch (substr($fec,5,2)){

  $nAno = substr($dDesde,0,4);

	if ($cEjePro == 0) {
		if ($nSwitch == 0) {

			$qDetFor  = "SELECT ";
			$qDetFor .= "$cAlfa.ffod$nAno.comidxxx, ";
			$qDetFor .= "$cAlfa.ffod$nAno.comcodxx, ";
			$qDetFor .= "$cAlfa.ffod$nAno.comcscxx, ";
			$qDetFor .= "$cAlfa.ffod$nAno.comcsc2x, ";
			$qDetFor .= "$cAlfa.ffoc$nAno.commovxx, ";
			$qDetFor .= "$cAlfa.ffod$nAno.ptoidxxx, ";
			$qDetFor .= "$cAlfa.ffod$nAno.comfecxx, ";
			$qDetFor .= "$cAlfa.ffoc$nAno.comtraxx, ";
			$qDetFor .= "SUM($cAlfa.ffod$nAno.comcanxx) AS unidades, ";
			$qDetFor .= "$cAlfa.ffod$nAno.comvlrxx, ";
			$qDetFor .= "$cAlfa.fpar0132.ptodesxx ";
			$qDetFor .= "FROM $cAlfa.ffod$nAno ";
			$qDetFor .= "LEFT JOIN $cAlfa.fpar0132 ON $cAlfa.ffod$nAno.ptoidxxx = $cAlfa.fpar0132.ptoidxxx ";
			$qDetFor .= "LEFT JOIN $cAlfa.ffoc$nAno ON $cAlfa.ffod$nAno.comidxxx = $cAlfa.ffoc$nAno.comidxxx AND ";
			$qDetFor .= "$cAlfa.ffod$nAno.comcodxx = $cAlfa.ffoc$nAno.comcodxx AND ";
			$qDetFor .= "$cAlfa.ffod$nAno.comcscxx = $cAlfa.ffoc$nAno.comcscxx AND ";
			$qDetFor .= "$cAlfa.ffod$nAno.comcsc2x = $cAlfa.ffoc$nAno.comcsc2x AND ";
			$qDetFor .= "$cAlfa.ffod$nAno.comcsc2x = $cAlfa.ffoc$nAno.comcsc2x ";
			$qDetFor .= "WHERE ";
			if($cPtoId != "") {
				$qDetFor .= "$cAlfa.ffod$nAno.ptoidxxx = \"$cPtoId\" AND ";
			}
			$qDetFor .= "$cAlfa.ffod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND ";
			$qDetFor .= "$cAlfa.ffoc$nAno.commovxx IN (\"IINICIAL\",\"COMPRAL\",\"REINTEGRO\",\"ADIRECTOR\") AND  ";
			$qDetFor .= "$cAlfa.ffod$nAno.regestxx = \"ACTIVO\" ";
			$qDetFor .= "GROUP BY $cAlfa.ffod$nAno.comidxxx,$cAlfa.ffod$nAno.comcodxx, $cAlfa.ffod$nAno.comcscxx, $cAlfa.ffod$nAno.comcsc2x, $cAlfa.ffod$nAno.ptoidxxx ";
			$qDetFor .= "ORDER BY $cAlfa.ffod$nAno.ptoidxxx ASC, $cAlfa.ffod$nAno.comfecxx ASC ";
			$xDetFor  = f_MySql("SELECT","",$qDetFor,$xConexion01,"");
			//echo $qDetFor."~".mysql_num_rows($xDetFor);

			$nFilDet  = mysql_num_rows($xDetFor);
			$mInvForT = array();
			while($xRDF = mysql_fetch_array($xDetFor)){
				// if($xRDF['commovxx'] == "COMPRAL" || $xRDF['commovxx'] == "IINICIAL" || $xRDF['commovxx'] == "REINTEGRO"  ){
				$nInd_mInvForT = count($mInvForT);
				$mInvForT[$nInd_mInvForT]['comidaxx'] = $xRDF['comidxxx'];
				$mInvForT[$nInd_mInvForT]['comcodax'] = $xRDF['comcodxx'];
				$mInvForT[$nInd_mInvForT]['comcscax'] = $xRDF['comcscxx'];
				$mInvForT[$nInd_mInvForT]['comcsca2'] = $xRDF['comcsc2x'];
				$mInvForT[$nInd_mInvForT]['commovxx'] = $xRDF['commovxx'];
				$mInvForT[$nInd_mInvForT]['comvlrxx'] = $xRDF['comvlrxx'];
				$mInvForT[$nInd_mInvForT]['ptoidxxx'] = $xRDF['ptoidxxx'];
				$mInvForT[$nInd_mInvForT]['unidades'] = $xRDF['unidades'];
				$mInvForT[$nInd_mInvForT]['ptodesxx'] = $xRDF['ptodesxx'];
				$mInvForT[$nInd_mInvForT]['regestxx'] = $xRDF['commovxx'];
				$mInvForT[$nInd_mInvForT]['comfecxx'] = $xRDF['comfecxx'];
			}

  		##Fin Traigo de detalle de Formularios todos los comprobantes de COMPRA DE FORMULARIOS O INVENTARIO INICIAL DE FORMULARIOS##

			##Recorro Matriz para Reoordenar los registros e imprimir bloques por producto de Formularios##
			// $mInvFor = f_Sort_Array_By_Field($mInvForT,"ptoidxxx","ASC_AZ");
			$mInvFor = f_ordenar_array_bidimensional($mInvForT,'ptoidxxx',SORT_ASC,'comfecxx',SORT_ASC);

  		##Fin Recorro Matriz para Reoordenar los registros e imprimir bloques por producto de Formularios##

			switch ($cTipo) {
				case 1:
					// PINTA POR PANTALLA// ?>
					<html>
						<head>
							<title>Reporte Inventario de Formularios</title>
							<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
							<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
							<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
							<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
						</head>
						<body>
							<div id="loading" style="background: white;position: absolute;left: 45%;top: 45%;padding: 2px;height: auto;border: 1px solid #ccc;">
								<div style="background: white;color: #444;font: bold 13px tahoma, arial, helvetica;padding: 10px;margin: 0;height: auto;">
										<img src="<?php echo $cPlesk_Skin_Directory ?>/loading.gif" width="32" height="32" style="margin-right:8px;float:left;vertical-align:top;"/>
										openComex<br>
										<span style="font: normal 10px arial, tahoma, sans-serif;">Cargando...</span>
								</div>
							</div>
							<form name = 'frgrm' action='frinpgrf.php' method="POST">
								<center>
									<table border="1" cellspacing="0" cellpadding="0" width="1500" align=center style="margin:5px">
										<tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">
											<td class="name" colspan="12" align="left">
												<font size="3">
													<b>REPORTE INVENTARIO DE FORMULARIOS<BR>
													<!--TIPO DE PRODUCTO: <?php echo $cPtoId." - ".$cPtoDes ?><br>-->
													PERIODO: <?php echo " ".$dDesde." - ".$dHasta ?><br>
													FECHA Y HORA DE CONSULTA: <?php echo " ".$cMes." ".substr($fec,8,2)." "."DE ".substr($fec,0,4)." "."- ".date('H:i:s') ?><br>
													</b>
												</font>
											</td>
										</tr>
										<tr height="20">
											<td style="background-color:#0B610B" class="letra8" align="center" width="180px" rowspan ="2"><b><font color=white>Documento</font></b></td>
											<td style="background-color:#0B610B" class="letra8" align="center" width="180px" rowspan ="2"><b><font color=white>Motivo</font></b></td>
											<td style="background-color:#0B610B" class="letra8" align="center" colspan ="3"><b><font color=white>Movimiento de Entrada</font></b></td>
											<td style="background-color:#0B610B" class="letra8" align="center" colspan ="3"><b><font color=white>Movimiento de Salida</font></b></td>
											<td style="background-color:#0B610B" class="letra8" align="center" colspan ="2"><b><font color=white>Saldo</font></b></td>
										</tr>
										<tr height="20">
											<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Unidades</font></b></td>
											<td style="background-color:#0B610B" class="letra8" align="center" width="168px"><b><font color=white>Valor Unitario</font></b></td>
											<td style="background-color:#0B610B" class="letra8" align="center" width="168px"><b><font color=white>Valor</font></b></td>
											<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Unidades</font></b></td>
											<td style="background-color:#0B610B" class="letra8" align="center" width="168px"><b><font color=white>Valor Unitario</font></b></td>
											<td style="background-color:#0B610B" class="letra8" align="center" width="168px"><b><font color=white>Valor</font></b></td>
											<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Unidades</font></b></td>
											<td style="background-color:#0B610B" class="letra8" align="center" width="168px"><b><font color=white>Valor</font></b></td>
										</tr>
										<?php
										$cPtoAux = "";
										$cPtoAux2 = "";
										for($i=0;$i<count($mInvFor);$i++){

											switch($mInvFor[$i]['regestxx']){
												case "COMPRAL":
													$mInvFor[$i]['commovxx'] = "COMPRA FORMULARIOS";
												break;
												case "IINICIAL":
													$mInvFor[$i]['commovxx'] = "INVENTARIO INICIAL";
												break;
												case "REINTEGRO":
													$mInvFor[$i]['commovxx'] = "REINTEGRO";
												break;
												case "ADIRECTOR":
													$mInvFor[$i]['commovxx'] = "ASIGNACION A  ADMINISTRADOR";
												break;
												default:
													//No hace nada
												break;
											}

											$nUnidE  = 0; $nVlUniE = 0;
											$nUnidD  = 0; $nVlUniD = 0;
											$nVlUniD = 0; $nValorD = 0;

											switch($mInvFor[$i]['regestxx']){
												case "COMPRAL":
												case "IINICIAL":
												case "REINTEGRO":
													$nUnidE      = $mInvFor[$i]['unidades'];
													$nVlUniE     = $mInvFor[$i]['comvlrxx'];
													$nValorE     = $mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
													$nTUnidE    += $mInvFor[$i]['unidades'];
													$nTValorE   += $mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
													$nSaldoC    += $mInvFor[$i]['unidades'];
													$nSaldoV    += $mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
													$nTotUnidE  += $mInvFor[$i]['unidades'];
													$nTotValorE += $mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
													$mInvFor[$i]['comtraxx'] = "ENTRADA";
												break;
												case "ADIRECTOR":
													$nUnidD      = $mInvFor[$i]['unidades'];
													$nVlUniD     = $mInvFor[$i]['comvlrxx'];
													$nValorD     = $mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
													$nTUnidD    += $mInvFor[$i]['unidades'];
													$nValorE    = 0;
													$nSaldoC    -= $mInvFor[$i]['unidades'];
													$nSaldoV    -= $mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
													$nTotUnidS  += $mInvFor[$i]['unidades'];
													$nTotValorS += $mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
													$mInvFor[$i]['comtraxx'] = "SALIDA";
												break;
											}

											if($cPtoAux2 != $mInvFor[$i]['ptoidxxx']){
												if($cPtoAux2 != ""){
													$cPtoAux2 = $mInvFor[$i]['ptoidxxx'];
													?>
													<tr height="20" style="padding-left:4px;padding-right:4px">
														<td style="background-color:#E3F6CE" class="letra7" align="right" colspan="2"><b>Total</b></td>
														<td style="background-color:#E3F6CE" class="letra7" align="center" colspan="1"><b><?php echo ($nToUnidE!="")?number_format($nToUnidE,0,',','.'):"0" ?></b></td>
														<td style="background-color:#E3F6CE" class="letra7" align="right" colspan="1"><b>&nbsp;</b></td>
														<td style="background-color:#E3F6CE" class="letra7" align="right" colspan="1"><b><?php echo ($nToValorE!="")?number_format($nToValorE,0,',','.'):"0" ?></b></td>
														<td style="background-color:#E3F6CE" class="letra7" align="center" colspan="1"><b><?php echo ($nToUnidS!="")?number_format($nToUnidS,0,',','.'):"0" ?></b></td>
														<td style="background-color:#E3F6CE" class="letra7" align="right" colspan="1"><b>&nbsp;</b></td>
														<td style="background-color:#E3F6CE" class="letra7" align="right"><b><?php echo ($nToValorS!="")?number_format($nToValorS,0,',','.'):"0" ?></b></td>
														<td style="background-color:#E3F6CE" class="letra7" align="right" colspan="1"><b><?php echo($nTSaldoC!="")?number_format($nTSaldoC,0,',','.'):"0"?></b></td>
														<td style="background-color:#E3F6CE" class="letra7" align="right" colspan="1"><b><?php echo($nTSaldoV!="")?number_format($nTSaldoV,0,',','.'):"0"?></b></td>
													</tr>
													<?php
													$nTotSaldoC += $nTSaldoC;
													$nTotSaldoV += $nTSaldoV;
													$nToUnidE  = 0;
													$nToUnidS  = 0;
													$nToValorE = 0;
													$nToValorE = 0;
													$nToValorS = 0;
													$nTSaldoC  = 0;
													$nTSaldoV  = 0;
												}

												if($cPtoAux2 == ""){
													$cPtoAux2 = $mInvFor[$i]['ptoidxxx'];
												}
											}//if($cPtoAux2 != $xRIF['ptoidxxx']){

											switch($mInvFor[$i]['regestxx']){
													case "COMPRAL":
													case "IINICIAL":
													case "REINTEGRO":
														$nToUnidE  +=($mInvFor[$i]['unidades'] != ""?$mInvFor[$i]['unidades']:"0");
														$nToValorE +=$mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
														$nTSaldoC  += $mInvFor[$i]['unidades'];
														$nTSaldoV  += $mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
														$mInvFor[$i]['comtraxx'] = "ENTRADA";
													break;
													case "ADIRECTOR":
														$nToUnidS  +=($mInvFor[$i]['unidades'] != ""?$mInvFor[$i]['unidades']:"0");
														$nToValorS +=$mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
														$nTSaldoC  -=$mInvFor[$i]['unidades'];
														$nTSaldoV  -=$mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
														$mInvFor[$i]['comtraxx'] = "SALIDA";
													break;
												}
												##Fin Impresion de total por cuenta##

											if($cPtoAux != $mInvFor[$i]['ptoidxxx']){

												$cPtoAux = $mInvFor[$i]['ptoidxxx'];
												$mSalFor = explode("~",fnSaldoxTipoFormulario($mInvFor[$i]['ptoidxxx'],$dDesde));
												$nSalForV = $mSalFor[1];
												$nSalForC = $mSalFor[0];
												// f_Mensaje(__FILE__,__LINE__,$cPtoAux);
												?>
												<tr height="20" style="padding-left:4px;padding-right:4px">
													<td style="background-color:#0B610B" class="letra7" align="left" colspan="5"><b><font color=white>Tipo de Producto: <?php echo $mInvFor[$i]['ptoidxxx']."-".$mInvFor[$i]['ptodesxx'] ?></font></b></td>
													<td style="background-color:#0B610B" class="letra7" align="right" colspan="3"><b><font color=white>Saldo Anterior</font></b></td>
													<td style="background-color:#0B610B" class="letra7" align="right" colspan="1"><b><font color=white><?php echo ($mSalFor[0]!="")?$mSalFor[0]:"0" ?></font></b></td>
													<td style="background-color:#0B610B" class="letra7" align="right" colspan="1"><b><font color=white><?php echo ($mSalFor[1]!="")?number_format($mSalFor[1],0,',','.'):"0" ?></font></b></td>
												</tr>
												<?php
												$nTSaldoC = $nTSaldoC + $nSalForC;
												$nTSaldoV = $nTSaldoV + $nSalForV;
											}
											?>
												<tr bgcolor = "white" height="20"   style="padding-left:4px;padding-right:4px">
													<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $mInvFor[$i]['comidaxx']."-".$mInvFor[$i]['comcodax']."-".$mInvFor[$i]['comcscax'] ?></td>
													<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $mInvFor[$i]['commovxx'] ?></td>
													<td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo ($mInvFor[$i]['comtraxx']=="ENTRADA")?$nUnidE:"0" ?></td>
													<td class="letra7" align="right"  style = "color:<?php echo $zColorPro ?>"><?php echo ($nVlUniE>0)?number_format($nVlUniE,0,',','.'):"0" ?></td>
													<td class="letra7" align="right"  style = "color:<?php echo $zColorPro ?>"><?php echo ($nValorE>0)?number_format($nValorE,0,',','.'):"0" ?></td>
													<td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo ($mInvFor[$i]['comtraxx']=="SALIDA")?$nUnidD:"0" ?></td>
													<td class="letra7" align="right"  style = "color:<?php echo $zColorPro ?>"><?php echo ($nVlUniD>0)?number_format($nVlUniD,0,',','.'):"0" ?></td>
													<td class="letra7" align="right"  style = "color:<?php echo $zColorPro ?>"><?php echo ($nValorD>0)?number_format($nValorD,0,',','.'):"0" ?></td>
													<td class="letra7" align="right"  style = "color:<?php echo $zColorPro ?>"><?php echo ($nTSaldoC!="")?number_format($nTSaldoC,0,',','.'):"0" ?></td>
													<td class="letra7" align="right"  style = "color:<?php echo $zColorPro ?>"><?php echo ($nTSaldoV!="")?number_format($nTSaldoV,0,',','.'):"0" ?></td>
												</tr>
												<?php
										}
										?>
										<tr height="20" style="padding-left:4px;padding-right:4px">
											<td style="background-color:#E3F6CE" class="letra7" align="right" colspan="2"><b>Total</b></td>
											<td style="background-color:#E3F6CE" class="letra7" align="center" colspan="1"><b><?php echo ($nToUnidE!="")?number_format($nToUnidE,0,',','.'):"0" ?></b></td>
											<td style="background-color:#E3F6CE" class="letra7" align="right" colspan="1"><b>&nbsp;</b></td>
											<td style="background-color:#E3F6CE" class="letra7" align="right" colspan="1"><b><?php echo ($nToValorE!="")?number_format($nToValorE,0,',','.'):"0" ?></b></td>
											<td style="background-color:#E3F6CE" class="letra7" align="center" colspan="1"><b><?php echo ($nToUnidS!="")?number_format($nToUnidS,0,',','.'):"0" ?></b></td>
											<td style="background-color:#E3F6CE" class="letra7" align="right" colspan="1"><b>&nbsp;</b></td>
											<td style="background-color:#E3F6CE" class="letra7" align="right"><b><?php echo ($nToValorS!="")?number_format($nToValorS,0,',','.'):"0" ?></b></td>
											<td style="background-color:#E3F6CE" class="letra7" align="right" colspan="1"><b><?php echo ($nTSaldoC!="")?number_format($nTSaldoC,0,',','.'):"0" ?></b></td>
											<td style="background-color:#E3F6CE" class="letra7" align="right" colspan="1"><b><?php echo ($nTSaldoV!="")?number_format($nTSaldoV,0,',','.'):"0" ?></b></td>
										</tr>
										<tr height="20" style="padding-left:4px;padding-right:4px">
											<td style="background-color:#0B610B" class="letra7" align="left" colspan="2"><b><font color=white>Saldo Final</font></b></td>
											<td style="background-color:#0B610B" class="letra7" align="center" colspan="1"><b><font color=white><?php echo ($nTotUnidE!="")?number_format($nTotUnidE,0,',','.'):"0" ?></font></b></td>
											<td style="background-color:#0B610B" class="letra7" align="right" colspan="1"><b><font color=white>&nbsp;</font></b></td>
											<td style="background-color:#0B610B" class="letra7" align="right" colspan="1"><b><font color=white><?php echo ($nTotValorE!="")?number_format($nTotValorE,0,',','.'):"0" ?></font></b></td>
											<td style="background-color:#0B610B" class="letra7" align="center" colspan="1"><b><font color=white><?php echo ($nTotUnidS!="")?number_format($nTotUnidS,0,',','.'):"0" ?></font></b></td>
											<td style="background-color:#0B610B" class="letra7" align="right" colspan="1"><b><font color=white>&nbsp;</font></b></td>
											<td style="background-color:#0B610B" class="letra7" align="right"><b><font color=white><?php echo ($nTotValorS!="")?number_format($nTotValorS,0,',','.'):"0" ?></font></b></td>
											<td style="background-color:#0B610B" class="letra7" align="right" colspan="1"><b><font color=white><?php echo (($nTotSaldoC+$nTSaldoC)!="")?number_format(($nTotSaldoC+$nTSaldoC),0,',','.'):"0" ?></font></b></td>
											<td style="background-color:#0B610B" class="letra7" align="right" colspan="1"><b><font color=white><?php echo (($nTotSaldoV+$nTSaldoV)!="")?number_format(($nTotSaldoV+$nTSaldoV),0,',','.'):"0" ?></font></b></td>
										</tr>
									</table>
								</center>
							</form>
							<script type="text/javascript">document.getElementById('loading').style.display="none";</script>
						</body>
					</html>
				<?php
				break;
				case 2:
					// PINTA POR EXCEL //
					// if($nFilInv > 0){
					if($nFilDet > 0){

						$data = '';
						$cNomFile = "INVENTARIO_FORMULARIOS_".$_COOKIE['kUsrId'].date("YmdHis").".xls";

						if ($_SERVER["SERVER_PORT"] != "") {
							$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;
						} else {
							$cFile = "{$OPENINIT['pathdr']}/opencomex/" . $vSysStr['system_download_directory'] . "/" . $cNomFile;
						}

						$fOp = fopen($cFile, 'a');

						$nColSpan = 10;

						$data .= '<table width="1024px" cellpadding="1" cellspacing="1" border="1" style="font-family:arial;font-size:12px;border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">';
						$data .= '<tr>';
						$data .= '<td colspan="'.$nColSpan.'" style="font-size:14px;font-weight:bold"><center>REPORTE INVENTARIO DE FORMULARIOS</td>';
						$data .= '</tr>';
						$data .= '<tr>';
						$data .= '<td colspan="'.$nColSpan.'"><B><center>'."PERIODO DE: "." ".$dDesde." "."A: "." ".$dHasta.'</center></B></td>';
						$data .= '</tr>';
						$data .= '<tr>';
						$data .= '<td colspan="'.$nColSpan.'" style="font-size:12px;font-weight:bold">FECHA Y HORA DE CONSULTA: '.$cMes." ".substr($fec,8,2)." "."DE ".substr($fec,0,4)." "."- ".date('H:i:s').'</td>';
						$data .= '</tr>';
						$data .= '<tr>';
						$data .= '<td style="font-size:12px;font-weight:bold" rowspan ="2"><center>DOCUMENTO</td>';
						$data .= '<td style="font-size:12px;font-weight:bold" rowspan = "2"><center>MOTIVO</td>';
						$data .= '<td colspan="3" style="font-size:12px;font-weight:bold"><center>MOVIMIENTO DE ENTRADA</td>';
						$data .= '<td colspan="3" style="font-size:12px;font-weight:bold"><center>MOVIMIENTO DE SALIDA</td>';
						$data .= '<td colspan="2" style="font-size:12px;font-weight:bold"s><center>SALDO</td>';
						$data .= '</tr>';

						$data .= '<tr>';
						$data .= '<td style="font-size:12px;font-weight:bold"><center>UNIDADES</td>';
						$data .= '<td style="font-size:12px;font-weight:bold"><center>VALOR UNITARIO</td>';
						$data .= '<td style="font-size:12px;font-weight:bold"><center>VALOR</td>';
						$data .= '<td style="font-size:12px;font-weight:bold"><center>UNIDADES</td>';
						$data .= '<td style="font-size:12px;font-weight:bold"><center>VALOR UNITARIO</td>';
						$data .= '<td style="font-size:12px;font-weight:bold"><center>VALOR</td>';
						$data .= '<td style="font-size:12px;font-weight:bold"><center>UNIDADES</td>';
						$data .= '<td style="font-size:12px;font-weight:bold"><center>VALOR</td>';
						$data .= '</tr>';

						$cPtoAux = "";
						$cPtoAux2 = "";
						for($i=0;$i<count($mInvFor);$i++){
						//while ($xRIF = mysql_fetch_array($xInvFor)) {
							switch($mInvFor[$i]['regestxx']){
								case "COMPRAL":
									$mInvFor[$i]['commovxx'] = "COMPRA FORMULARIOS";
								break;
								case "IINICIAL":
									$mInvFor[$i]['commovxx'] = "INVENTARIO INICIAL";
								break;
								case "REINTEGRO":
									$mInvFor[$i]['commovxx'] = "REINTEGRO";
								break;
								case "ADIRECTOR":
									$mInvFor[$i]['commovxx'] = "ASIGNACION A  ADMINISTRADOR";
								break;
							}//switch($xRIF['regestxx']){

							$nUnidE  = 0;
							$nVlUniE = 0;
							$nValorE = 0;
							$nUnidD  = 0;
							$nVlUniD = 0;
							$nVlUniD = 0;
							$nValorD = 0;

							switch($mInvFor[$i]['regestxx']){
								case "COMPRAL":
								case "IINICIAL":
								case "REINTEGRO":
									$nUnidE      = $mInvFor[$i]['unidades'];
									$nVlUniE     = $mInvFor[$i]['comvlrxx'];
									$nValorE     = $mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
									$nTUnidE    += $mInvFor[$i]['unidades'];
									$nTValorE   += $mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
									$nSaldoC    += $mInvFor[$i]['unidades'];
									$nSaldoV    += $mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
									$nTotUnidE  += $mInvFor[$i]['unidades'];
									$nTotValorE += $mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
									$mInvFor[$i]['comtraxx'] = "ENTRADA";
								break;
								case "ADIRECTOR":
									$nUnidD      = $mInvFor[$i]['unidades'];
									$nVlUniD     = $mInvFor[$i]['comvlrxx'];
									$nValorD     = $mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
									$nTUnidD    += $mInvFor[$i]['unidades'];
									$nSaldoC    -= $mInvFor[$i]['unidades'];
									$nSaldoV    -= $mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
									$nTotUnidS  += $mInvFor[$i]['unidades'];
									$nTotValorS += $mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
									$mInvFor[$i]['comtraxx'] = "SALIDA";
								break;
							}//switch($xRIF['regestxx']){

							if($cPtoAux2 != $mInvFor[$i]['ptoidxxx']){
								if($cPtoAux2 != ""){
									$cPtoAux2 = $mInvFor[$i]['ptoidxxx'];

									$data .= '<tr>';
									$data .= '<td colspan="2" style="font-size:12px;font-weight:bold">Total</td>';
									$data .= '<td align="right" style="font-size:12px;font-weight:bold">'.(($nToUnidE!="")?number_format($nToUnidE,0,',',''):"0").'</td>';
									$data .= '<td align="right" style="font-size:12px;font-weight:bold">'."".'</td>';
									$data .= '<td align="right" style="font-size:12px;font-weight:bold">'.(($nToValorE!="")?number_format($nToValorE,0,',',''):"0").'</td>';
									$data .= '<td align="right" style="font-size:12px;font-weight:bold">'.(($nToUnidS!="")?number_format($nToUnidS,0,',',''):"0").'</td>';
									$data .= '<td align="right" style="font-size:12px;font-weight:bold">'."".'</td>';
									$data .= '<td align="right" style="font-size:12px;font-weight:bold">'.(($nToValorS!="")?number_format($nToValorS,0,',',''):"0").'</td>';
									$data .= '<td align="right" style="font-size:12px;font-weight:bold">'.(($nTSaldoC!="")?number_format($nTSaldoC,0,',',''):"0").'</td>';
									$data .= '<td align="right" style="font-size:12px;font-weight:bold">'.(($nTSaldoV!="")?number_format($nTSaldoV,0,',',''):"0").'</td>';
									$data .= '</tr>';

									$nTotSaldoC += $nTSaldoC;
									$nTotSaldoV += $nTSaldoV;
									$nToUnidE  = 0;
									$nToUnidS  = 0;
									$nValorD   = 0;
									$nVlUniD   = 0;
									$nToValorE = 0;
									$nToValorE = 0;
									$nToValorS = 0;
									$nTSaldoC  = 0;
									$nTSaldoV  = 0;

								}//if($cPtoAux2 != ""){

								if($cPtoAux2 == ""){
									$cPtoAux2 = $mInvFor[$i]['ptoidxxx'];
								}
							}//if($cPtoAux2 != $xRIF['ptoidxxx']){

							switch($mInvFor[$i]['regestxx']){
								case "COMPRAL":
								case "IINICIAL":
								case "REINTEGRO":
									$nToUnidE  +=($mInvFor[$i]['unidades'] != ""?$mInvFor[$i]['unidades']:"0");
									$nToValorE +=$mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
									$nTSaldoC  += $mInvFor[$i]['unidades'];
									$nTSaldoV  += $mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
									$mInvFor[$i]['comtraxx'] = "ENTRADA";
								break;
								case "ADIRECTOR":
									$nToUnidS  +=($mInvFor[$i]['unidades'] != ""?$mInvFor[$i]['unidades']:"0");
									$nToValorS +=$mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
									$nTSaldoC  -=$mInvFor[$i]['unidades'];
									$nTSaldoV  -=$mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
									$mInvFor[$i]['comtraxx'] = "SALIDA";
								break;
							}// switch($mInvFor[$i]['regestxx']){

							if($cPtoAux != $mInvFor[$i]['ptoidxxx']){
								$cPtoAux = $mInvFor[$i]['ptoidxxx'];
								$mSalFor = explode("~",fnSaldoxTipoFormulario($mInvFor[$i]['ptoidxxx'],$dDesde));
								$nSalForV = $mSalFor[1];
								$nSalForC = $mSalFor[0];

								$data .= '<tr>';
								$data .= '<td colspan="5" style="font-size:12px;font-weight:bold">Tipo de Producto:'.$mInvFor[$i]['ptoidxxx']."-".$mInvFor[$i]['ptodesxx'].'</td>';
								$data .= '<td colspan="3" align="right" style="font-size:12px;font-weight:bold">Saldo Anterior</td>';
								$data .= '<td align="right" style="font-size:12px;font-weight:bold">'.(($mSalFor[0]!="")?$mSalFor[0]:"0").'</td>';
								$data .= '<td align="right" style="font-size:12px;font-weight:bold">'.(($mSalFor[1]!="")?number_format($mSalFor[1],0,',',''):"0").'</td>';
								$data .= '</tr>';

								$nTSaldoC = $nTSaldoC + $nSalForC;
								$nTSaldoV = $nTSaldoV + $nSalForV;
							}

							$data .= '<tr>';
							$data .= '<td style="font-size:12px">'.$mInvFor[$i]['comidaxx']."-".$mInvFor[$i]['comcodax']."-".$mInvFor[$i]['comcscax'].'</td>';
							$data .= '<td style="font-size:12px">'.$mInvFor[$i]['commovxx'].'</td>';
							$data .= '<td style="font-size:12px">'.(($mInvFor[$i]['comtraxx']=="ENTRADA")?$nUnidE:"0").'</td>';
							$data .= '<td align="left" style="font-size:12px">'.(($nVlUniE>0)?number_format($nVlUniE,0,',',''):"0").'</td>';
							$data .= '<td align="right" style="font-size:12px">'.(($nValorE>0)?number_format($nValorE,0,',',''):"0").'</td>';
							$data .= '<td align="right" style="font-size:12px">'.(($mInvFor[$i]['comtraxx']=="SALIDA")?$nUnidD:"0").'</td>';
							$data .= '<td align="right" style="font-size:12px">'.(($nVlUniD>0)?number_format($nVlUniD,0,',',''):"0").'</td>';
							$data .= '<td align="right" style="font-size:12px">'.(($nValorD>0)?number_format($nValorD,0,',',''):"0").'</td>';
							$data .= '<td align="right" style="font-size:12px">'.(($nTSaldoC!="")?number_format($nTSaldoC,0,',',''):"0").'</td>';
							$data .= '<td align="right" style="font-size:12px">'.(($nTSaldoV!="")?number_format($nTSaldoV,0,',',''):"0").'</td>';
							$data .= '</tr>';
						}

						$data .= '<tr>';
						$data .= '<td colspan="2" style="font-size:12px;font-weight:bold">Total</td>';
						$data .= '<td align="right" style="font-size:12px;font-weight:bold">'.(($nToUnidE!="")?number_format($nToUnidE,0,',',''):"0").'</td>';
						$data .= '<td align="right" style="font-size:12px;font-weight:bold">'."".'</td>';
						$data .= '<td align="right" style="font-size:12px;font-weight:bold">'.(($nToValorE!="")?number_format($nToValorE,0,',',''):"0").'</td>';
						$data .= '<td align="right" style="font-size:12px;font-weight:bold">'.(($nToUnidS!="")?number_format($nToUnidS,0,',',''):"0").'</td>';
						$data .= '<td align="right" style="font-size:12px;font-weight:bold">'."".'</td>';
						$data .= '<td align="right" style="font-size:12px;font-weight:bold">'.(($nToValorS!="")?number_format($nToValorS,0,',',''):"0").'</td>';
						$data .= '<td align="right" style="font-size:12px;font-weight:bold">'.(($nTSaldoC!="")?number_format($nTSaldoC,0,',',''):"0").'</td>';
						$data .= '<td align="right" style="font-size:12px;font-weight:bold">'.(($nTSaldoV!="")?number_format($nTSaldoV,0,',',''):"0").'</td>';
						$data .= '</tr>';

						$data .= '<tr>';
						$data .= '<td colspan="2" style="font-size:12px;font-weight:bold">Saldo Final</td>';
						$data .= '<td align="right" style="font-size:12px;font-weight:bold">'.(($nTotUnidE!="")?number_format($nTotUnidE,0,',',''):"0").'</td>';
						$data .= '<td align="right" style="font-size:12px;font-weight:bold">'."".'</td>';
						$data .= '<td align="right" style="font-size:12px;font-weight:bold">'.(($nTotValorE!="")?number_format($nTotValorE,0,',',''):"0").'</td>';
						$data .= '<td align="right" style="font-size:12px;font-weight:bold">'.(($nTotUnidS!="")?number_format($nTotUnidS,0,',',''):"0").'</td>';
						$data .= '<td align="right" style="font-size:12px;font-weight:bold">'."".'</td>';
						$data .= '<td align="right" style="font-size:12px;font-weight:bold">'.(($nTotValorS!="")?number_format($nTotValorS,0,',',''):"0").'</td>';
						$data .= '<td align="right" style="font-size:12px;font-weight:bold">'.((($nTotSaldoC+$nTSaldoC)!="")?number_format($nTotSaldoC+$nTSaldoC,0,',',''):"0").'</td>';
						$data .= '<td align="right" style="font-size:12px;font-weight:bold">'.((($nTotSaldoV+$nTSaldoV)!="")?number_format($nTotSaldoV+$nTSaldoV,0,',',''):"0").'</td>';
						$data .= '</tr>';
						$data .= '</table>';

						fwrite($fOp, $data);
						fclose($fOp);

						if (file_exists($cFile)) {
              // Obtener la ruta absoluta del archivo
              $cAbsolutePath = realpath($cFile);
              $cAbsolutePath = substr($cAbsolutePath,0,strrpos($cAbsolutePath, '/'));
              if (in_array(realpath($cAbsolutePath), $vSystem_Path_Authorized)) {
                if ($_SERVER["SERVER_PORT"] != "") {
                  header('Content-Type: application/octet-stream');
                  header("Content-Disposition: attachment; filename=\"".basename($cNomFile)."\";");
                  header('Expires: 0');
                  header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                  header("Cache-Control: private",false); // required for certain browsers
                  header('Pragma: public');
  
                  ob_clean();
                  flush();
                  readfile($cFile);
                  exit;
                }
              }
						} else {
							$nSwitch = 1;
							if ($_SERVER["SERVER_PORT"] != "") {
								f_Mensaje(__FILE__, __LINE__, "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
							} else {
								$cMsj .= "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.";
							}
						}

					} else {
						if ($_SERVER["SERVER_PORT"] != "") {
              f_Mensaje(__FILE__,__LINE__,"No se encontraron registros.");
            } else {
              $cMsj .= "No se encontraron registros.";
            }
					}
				break;
				case 3 :
					if($nFilDet > 0){

						$cRoot = $_SERVER['DOCUMENT_ROOT'];

						define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
						require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');

						class PDF extends FPDF {
							function Header() {
								global $cRoot; global $cPlesk_Skin_Directory;
								global $cAlfa; global $cTipoCta; global $cMes; global $fec; global $cTerId; global $nPag; global $cTpTer; global $dDesde; global $dHasta;

								switch($cAlfa){
									case "INTERLOG":
										$this->SetXY(13,7);
										$this->Cell(42,28,'',1,0,'C');
										$this->Cell(213,28,'',1,0,'C');

										// Dibujo //
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/MaryAire.jpg',14,8,40,25);
										$this->SetFont('verdana','',16);
										$this->SetXY(55,15);
										$this->Cell(213,8,"INVENTARIO DE FORMULARIOS",0,0,'C');
										$this->Ln(8);
										$this->SetFont('verdana','',12);
										$this->SetX(55);
										$this->Cell(213,6,"DE: "." ".$dDesde." "."A: "." ".$dHasta,0,0,'C');
										$this->Ln(15);
										$this->SetX(13);
									break;
									default:
										$this->SetXY(13,7);
										$this->Cell(255,15,'',1,0,'C');
										$this->SetFont('verdana','',16);
										$this->SetXY(13,8);
										$this->Cell(255,8,"INVENTARIO DE FORMULARIOS",0,0,'C');
										$this->Ln(8);
										$this->SetFont('verdana','',12);
										$this->SetX(13);
										$this->Cell(255,6,"DE: "." ".$dDesde." "."A: "." ".$dHasta,0,0,'C');
										$this->Ln(10);
										$this->SetX(13);
									break;
								}

								if($this->PageNo() > 1 && $nPag ==1){

										$this->SetFont('verdana','B',7);
										$this->SetWidths(array('25','25','60','60','45'));
										$this->SetAligns(array('L','L','L','R','R'));
										$this->SetX(13);
										$this->Row(array("Documento",
																		"Motivo",
																		"Movimiento de Entrada",
																		"Movimiento de Salida",
																		"Saldo"));
										$this->SetAligns(array('L','L','L','R','R'));

									$this->SetX(13);

								}

							}
							function Footer() {
								$this->SetY(-10);
								$this->SetFont('verdana','',6);
								$this->Cell(0,5,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
							}

							function SetWidths($w) {
								//Set the array of column widths
								$this->widths=$w;
							}

							function SetAligns($a){
								//Set the array of column alignments
								$this->aligns=$a;
							}

							function Row($data){
								//Calculate the height of the row
								$nb=0;
								for($i=0;$i<count($data);$i++)
										$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
								$h=4*$nb;
								//Issue a page break first if needed
								$this->CheckPageBreak($h);
								//Draw the cells of the row
								for($i=0;$i<count($data);$i++)
								{
										$w=$this->widths[$i];
										$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
										//Save the current position
										$x=$this->GetX();
										$y=$this->GetY();
										//Draw the border
										$this->Rect($x,$y,$w,$h);
										//Print the text
										$this->MultiCell($w,4,$data[$i],0,$a);
										//Put the position to the right of the cell
										$this->SetXY($x+$w,$y);
								}
								//Go to the next line
								$this->Ln($h);
							}

							function CheckPageBreak($h){
								//If the height h would cause an overflow, add a new page immediately
								if($this->GetY()+$h>$this->PageBreakTrigger)
										$this->AddPage($this->CurOrientation);
							}

							function NbLines($w,$txt){
								//Computes the number of lines a MultiCell of width w will take
								$cw=&$this->CurrentFont['cw'];
								if($w==0)
										$w=$this->w-$this->rMargin-$this->x;
								$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
								$s=str_replace("\r",'',$txt);
								$nb=strlen($s);
								if($nb>0 and $s[$nb-1]=="\n")
										$nb--;
								$sep=-1;
								$i=0;
								$j=0;
								$l=0;
								$nl=1;
								while($i<$nb){
									$c=$s[$i];
									if($c=="\n"){
										$i++;
										$sep=-1;
										$j=$i;
										$l=0;
										$nl++;
										continue;
									}
									if($c==' ')
											$sep=$i;
									$l+=$cw[$c];
									if($l>$wmax){
										if($sep==-1){
											if($i==$j)
													$i++;
										}
										else
												$i=$sep+1;
										$sep=-1;
										$j=$i;
										$l=0;
										$nl++;
									}
									else
											$i++;
								}
								return $nl;
							}

						}

						$pdf = new PDF('L','mm','Letter');
						$pdf->AddFont('verdana','','');
						$pdf->AddFont('verdana','B','');
						$pdf->AliasNbPages();
						$pdf->SetMargins(0,0,0);

						$pdf->AddPage();

						$pdf->Ln(7);
						$pdf->SetFont('verdana','B',7);


						$pdf->SetFillColor(11,97,11);
						$pdf->SetTextColor(255);
						$pdf->SetFont('verdana','B',7);
						$pdf->SetX(13);
						$pdf->Cell(25,5,"Documento",1,0,'C',1);
						$pdf->Cell(25,5,"Motivo",1,0,'C',1);
						$pdf->Cell(80,5,"Movimiento de Entrada",1,0,'C',1);
						$pdf->Cell(80,5,"Movimiento de Salida",1,0,'C',1);
						$pdf->Cell(45,5,"Saldo",1,0,'C',1);
						$pdf->Ln(5);
						$pdf->SetX(13);
						$pdf->Cell(50,5,"",1,0,'C',1);
						$pdf->Cell(26,5,"Unidades",1,0,'C',1);
						$pdf->Cell(27,5,"Valor Unitario",1,0,'C',1);
						$pdf->Cell(27,5,"Valor",1,0,'C',1);
						$pdf->Cell(26,5,"Unidades",1,0,'C',1);
						$pdf->Cell(27,5,"Valor Unitario",1,0,'C',1);
						$pdf->Cell(27,5,"Valor",1,0,'C',1);
						$pdf->Cell(22,5,"Unidades",1,0,'C',1);
						$pdf->Cell(23,5,"Saldo",1,0,'C',1);
						$pdf->Ln(5);
						$pdf->SetTextColor(0);

						$cPtoAux = "";
						$cPtoAux2 = "";
						for($i=0;$i<count($mInvFor);$i++){
							switch($mInvFor[$i]['regestxx']){
								case "COMPRAL":
									$mInvFor[$i]['commovxx'] = "COMPRA FORMULARIOS";
								break;
								case "IINICIAL":
									$mInvFor[$i]['commovxx'] = "INVENTARIO INICIAL";
								break;
								case "REINTEGRO":
									$mInvFor[$i]['commovxx'] = "REINTEGRO";
								break;
								case "ADIRECTOR":
									$mInvFor[$i]['commovxx'] = "ASIGNACION A  ADMINISTRADOR";
								break;
							}//switch($xRIF['regestxx']){

							$nUnidE  = 0;
							$nVlUniE = 0;
							$nValorE = 0;
							$nUnidD  = 0;
							$nVlUniD = 0;
							$nVlUniD = 0;
							$nValorD = 0;

							switch($mInvFor[$i]['regestxx']){
								case "COMPRAL":
								case "IINICIAL":
								case "REINTEGRO":
									$nUnidE      = $mInvFor[$i]['unidades'];
									$nVlUniE     = $mInvFor[$i]['comvlrxx'];
									$nValorE     = $mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
									$nTUnidE    += $mInvFor[$i]['unidades'];
									$nTValorE   += $mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
									$nSaldoC    += $mInvFor[$i]['unidades'];
									$nSaldoV    += $mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
									$nTotUnidE  += $mInvFor[$i]['unidades'];
									$nTotValorE += $mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
									$mInvFor[$i]['comtraxx'] = "ENTRADA";
								break;
								case "ADIRECTOR":
									$nUnidD      = $mInvFor[$i]['unidades'];
									$nVlUniD     = $mInvFor[$i]['comvlrxx'];
									$nValorD     = $mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
									$nTUnidD    += $mInvFor[$i]['unidades'];
									$nTValorE    = 0;
									$nSaldoC    -= $mInvFor[$i]['unidades'];
									$nSaldoV    -= $mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
									$nTotUnidS  += $mInvFor[$i]['unidades'];
									$nTotValorS += $mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
									$mInvFor[$i]['comtraxx'] = "SALIDA";
								break;
							}//switch($xRIF['regestxx']){

							if($cPtoAux2 != $mInvFor[$i]['ptoidxxx']){
								if($cPtoAux2 != ""){
									$cPtoAux2 = $mInvFor[$i]['ptoidxxx'];

									$pdf->SetFillColor(227,246,206);
									$pdf->SetX(13);
									$pdf->SetFont('verdana','B',6);
									$pdf->Cell(50,5,"TOTAL",1,0,'R',1);
									$pdf->Cell(26,5,($nToUnidE!="")?number_format($nToUnidE,0,',','.'):"0",1,0,'C',1);
									$pdf->Cell(27,5,"",1,0,'R',1);
									$pdf->Cell(27,5,($nToValorE!="")?number_format($nToValorE,0,',','.'):"0",1,0,'R',1);
									$pdf->Cell(26,5,($nToUnidS!="")?number_format($nToUnidS,0,',','.'):"0",1,0,'C',1);
									$pdf->Cell(27,5,"",1,0,'R',1);
									$pdf->Cell(27,5,($nToValorS!="")?number_format($nToValorS,0,',','.'):"0",1,0,'R',1);
									$pdf->Cell(22,5,($nTSaldoC!="")?number_format($nTSaldoC,0,',','.'):"0",1,0,'C',1);
									$pdf->Cell(23,5,($nTSaldoV!="")?number_format($nTSaldoV,0,',','.'):"0",1,0,'R',1);
									$pdf->Ln(5);

									$nTotSaldoC += $nTSaldoC;
									$nTotSaldoV += $nTSaldoV;
									$nToUnidE  = 0;
									$nToUnidS  = 0;
									$nToValorE = 0;
									$nToValorS = 0;
									$nTSaldoC  = 0;
									$nTSaldoV  = 0;
									$nValorD   = 0;
								}//	if($cPtoAux2 != ""){

								if($cPtoAux2 == ""){
									$cPtoAux2 = $mInvFor[$i]['ptoidxxx'];
								}
							}//if($cPtoAux2 != $xRIF['ptoidxxx']){

							switch($mInvFor[$i]['regestxx']){
								case "COMPRAL":
								case "IINICIAL":
								case "REINTEGRO":
									$nToUnidE  +=($mInvFor[$i]['unidades'] != ""?$mInvFor[$i]['unidades']:"0");
									$nToValorE +=$mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
									$nTSaldoC  += $mInvFor[$i]['unidades'];
									$nTSaldoV  += $mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
									$mInvFor[$i]['comtraxx'] = "ENTRADA";
								break;
								case "ADIRECTOR":
									$nToUnidS  +=($mInvFor[$i]['unidades'] != ""?$mInvFor[$i]['unidades']:"0");
									$nToValorS +=$mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
									$nTSaldoC  -=$mInvFor[$i]['unidades'];
									$nTSaldoV  -=$mInvFor[$i]['unidades']*$mInvFor[$i]['comvlrxx'];
									$mInvFor[$i]['comtraxx'] = "SALIDA";
								break;
							}
							##Fin Impresion de total por cuenta##

							if($cPtoAux != $mInvFor[$i]['ptoidxxx']){
								$cPtoAux = $mInvFor[$i]['ptoidxxx'];
								$mSalFor = explode("~",fnSaldoxTipoFormulario($mInvFor[$i]['ptoidxxx'],$dDesde));
								$nSalForV = $mSalFor[1];
								$nSalForC = $mSalFor[0];

								$pdf->SetFillColor(11,97,11);
								$pdf->SetTextColor(255);
								$pdf->SetX(13);
								$pdf->SetFont('verdana','B',6);
								$pdf->Cell(156,5,"TIPO DE PRODUCTO: {$mInvFor[$i]['ptoidxxx']} - {$mInvFor[$i]['ptodesxx']}",1,0,'L',1);
								$pdf->Cell(54,5,"SALDO ANTERIOR",1,0,'R',1);
								$pdf->Cell(22,5,($mSalFor[0]>0)?$mSalFor[0]:"0",1,0,'C',1);
								$pdf->Cell(23,5,($mSalFor[1]>0)?number_format($mSalFor[1],0,',','.'):"0",1,0,'R',1);
								$pdf->Ln(5);

								$nTSaldoC = $nTSaldoC + $nSalForC;
								$nTSaldoV = $nTSaldoV + $nSalForV;
							}

							$pdf->SetX(13);
							$pdf->SetTextColor();
							$pdf->SetWidths(array('25','25','26','27','27','26','27','27','22','23'));
							$pdf->SetAligns(array('L','L','C','R','R','C','R','R','C','R'));
							$pdf->SetFont('verdana','',7);
							$pdf->Row(array($mInvFor[$i]['comidaxx']."-".$mInvFor[$i]['comcodax']."-".$mInvFor[$i]['comcscax'],
														$mInvFor[$i]['commovxx'],
														($mInvFor[$i]['comtraxx']=="ENTRADA")?$nUnidE:"0",
														($nVlUniE>0)?number_format($nVlUniE,0,',','.'):"0",
														($nValorE>0)?number_format($nValorE,0,',','.'):"0",
														($mInvFor[$i]['comtraxx']=="SALIDA")?$nUnidD:"0",
														($nVlUniD>0)?number_format($nVlUniD,0,',','.'):"0",
														($nValorD>0)?number_format($nValorD,0,',','.'):"0",
														($nTSaldoC!="")?number_format($nTSaldoC,0,',','.'):"0",
														($nTSaldoV!="")?number_format($nTSaldoV,0,',','.'):"0"));
						}

						$pdf->SetFillColor(227,246,206);
						$pdf->SetX(13);
						$pdf->SetFont('verdana','B',7);
						$pdf->Cell(50,5,"TOTAL",1,0,'R',1);
						$pdf->Cell(26,5,($nToUnidE!="")?number_format($nToUnidE,0,',','.'):"0",1,0,'C',1);
						$pdf->Cell(27,5,"",1,0,'R',1);
						$pdf->Cell(27,5,($nToValorE!="")?number_format($nToValorE,0,',','.'):"0",1,0,'R',1);
						$pdf->Cell(26,5,($nToUnidS!="")?number_format($nToUnidS,0,',','.'):"0",1,0,'C',1);
						$pdf->Cell(27,5,"",1,0,'R',1);
						$pdf->Cell(27,5,($nToValorE!="")?number_format($nToValorE,0,',','.'):"0",1,0,'R',1);
						$pdf->Cell(22,5,($nTSaldoC!="")?number_format($nTSaldoC,0,',','.'):"0",1,0,'C',1);
						$pdf->Cell(23,5,($nTSaldoV!="")?number_format($nTSaldoV,0,',','.'):"0",1,0,'R',1);
						$pdf->Ln(5);

						$pdf->SetX(13);
						$pdf->SetFillColor(11,97,11);
						$pdf->SetTextColor(255);
						$pdf->SetFont('verdana','B',6);
						$pdf->SetX(13);
						$pdf->Cell(50,5,"SALDO FINAL",1,0,'R',1);
						$pdf->Cell(26,5,($nTotUnidE!="")?number_format($nTotUnidE,0,',','.'):"0",1,0,'C',1);
						$pdf->Cell(27,5,"",1,0,'R',1);
						$pdf->Cell(27,5,($nTotValorE!="")?number_format($nTotValorE,0,',','.'):"0",1,0,'R',1);
						$pdf->Cell(26,5,($nTotUnidS!="")?number_format($nTotUnidS,0,',','.'):"0",1,0,'C',1);
						$pdf->Cell(27,5,"",1,0,'R',1);
						$pdf->Cell(27,5,($nTotValorS!="")?number_format($nTotValorS,0,',','.'):"0",1,0,'R',1);
						$pdf->Cell(22,5,($nTotSaldoC+$nTSaldoC!="")?number_format($nTotSaldoC+$nTSaldoC,0,',','.'):"0",1,0,'C',1);
						$pdf->Cell(23,5,($nTotSaldoV+$nTSaldoV!="")?number_format($nTotSaldoV+$nTSaldoV,0,',','.'):"0",1,0,'R',1);

						$nPag = 0;
						$pdf->Ln(5);
						$pdf->SetX(13);
						$pdf->SetFont('verdana','B',8);
						$pdf->Cell(50,5,"FECHA Y HORA DE CONSULTA:",0,0,'L');
						$pdf->SetFont('verdana','',8);
						$pdf->Cell(205,5,date('Y-m-d').' - '.date('H:i:s'),0,0,'L');

						$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";

						$pdf->Output($cFile);

						if (file_exists($cFile)){
							chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
						} else {
							f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
						}

						echo "<html><script>document.location='$cFile';</script></html>";

					}else{
						f_Mensaje(__FILE__,__LINE__,"No se Generaron registros");
					}

				break;
			}//switch ($cTipo) {
		}//if ($nSwitch == 0) {
	}//if ($cEjePro == 0) {

	function fnSaldoxTipoFormulario($xPtoId,$xFecha){
		global $xConexion01; global $cAlfa; global $vSysStr;

		$Tounid = 0; $nSaldo = 0;

		for($i=($vSysStr['financiero_ano_instalacion_modulo']+0); $i<=substr($xFecha,0,4); $i++){

			$qSalDet  = "SELECT ";
			$qSalDet .= "SUM(IF($cAlfa.ffoc$i.comtraxx = \"ENTRADA\" || $cAlfa.ffoc$i.comtraxx = \"COMPRAL\"  || $cAlfa.ffoc$i.comtraxx = \"REINTEGRO\", $cAlfa.ffod$i.comcanxx, ($cAlfa.ffod$i.comcanxx * -1) ) ) AS cantidad, ";
			$qSalDet .= "SUM(IF($cAlfa.ffoc$i.comtraxx = \"ENTRADA\" || $cAlfa.ffoc$i.comtraxx = \"COMPRAL\"  || $cAlfa.ffoc$i.comtraxx = \"REINTEGRO\", ($cAlfa.ffod$i.comcanxx * $cAlfa.ffod$i.comvlrxx),(($cAlfa.ffod$i.comcanxx * $cAlfa.ffod$i.comvlrxx) * -1 ))) AS valortot ";
			$qSalDet .= "FROM $cAlfa.ffod$i ";
			$qSalDet .= "LEFT JOIN $cAlfa.ffoc$i ON $cAlfa.ffod$i.comidxxx = $cAlfa.ffoc$i.comidxxx AND ";
			$qSalDet .= "$cAlfa.ffod$i.comcodxx = $cAlfa.ffoc$i.comcodxx AND ";
			$qSalDet .= "$cAlfa.ffod$i.comcscxx = $cAlfa.ffoc$i.comcscxx AND ";
			$qSalDet .= "$cAlfa.ffod$i.comcsc2x = $cAlfa.ffoc$i.comcsc2x AND ";
			$qSalDet .= "$cAlfa.ffod$i.comcsc2x = $cAlfa.ffoc$i.comcsc2x ";
			$qSalDet .= "WHERE ";
			$qSalDet .= "$cAlfa.ffod$i.ptoidxxx = \"$xPtoId\" AND ";
			$qSalDet .= "$cAlfa.ffoc$i.commovxx IN (\"IINICIAL\", \"COMPRAL\", \"REINTEGRO\", \"ADIRECTOR\") AND ";
			$qSalDet .= "$cAlfa.ffod$i.comfecxx < \"$xFecha\" AND ";
			$qSalDet .= "$cAlfa.ffod$i.regestxx = \"ACTIVO\" ";
			$qSalDet .= "GROUP BY $cAlfa.ffod$i.ptoidxxx ";

			$xSalDet  = f_MySql("SELECT","",$qSalDet,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qSalDet."~".mysql_num_rows($xSalDet));
			##Fin Traigo de detalle de Formularios todos los comprobantes de COMPRA DE FORMULARIOS o INVENTARIO INCIAL,REINTEGRO, ASIGNACION A ADMINISTRADOR##
			if(mysql_num_rows($xSalDet) > 0){
				$vSalDet = mysql_fetch_array($xSalDet);
				$Tounid += $vSalDet['cantidad'];
				$nSaldo += $vSalDet['valortot'];
			}//if(mysql_num_rows($xSalDet) > 0){
		}//for($i=$nAnioI;$i<=$nAnioF;$i++){

		return "{$Tounid}~{$nSaldo}";
	}
?>
<?php
  if ($_SERVER["SERVER_PORT"] == "") {

    /**
     * Se ejecuto por el proceso en background
     * Actualizo el campo de resultado y nombre del archivo
     */
    $vParBg['pbarespr'] = ($nSwitch == 0) ? "EXITOSO" : "FALLIDO";  //Resultado Proceso
    $vParBg['pbaexcxx'] = ($nFilDet > 0 ? $cNomFile : "");  //Nombre Archivos Excel
    $vParBg['pbaerrxx'] = $cMsj;                                    //Errores al ejecutar el Proceso
    $vParBg['regdfinx'] = date('Y-m-d H:i:s');                      //Fecha y Hora Fin Ejecucion Proceso
    $vParBg['pbaidxxx'] = $vArg[0];                                 //id Proceso

    #Incluyendo la clase de procesos en background
    $ObjProBg = new cProcesosBackground();
    $mReturnProBg = $ObjProBg->fnFinalizarProcesoBackground($vParBg);

    #Imprimiendo resumen de todo ok.
    if ($mReturnProBg[0] == "false") {
      $nSwitch = 1;
      for ($nR = 1; $nR < count($mReturnProBg); $nR++) {
        $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
        $cMsj .= $mReturnProBg[$nR] . "\n";
      }
    }
  } // fin del if ($_SERVER["SERVER_PORT"] == "") 
?>

<?php
  /**
  * Imprime Informe Fiscal Operaciones.
  *  --- Descripcion: Permite Imprimir Informe Fiscal Operaciones.
  * @author José Luis Aroca <jose.aroca@opentecnologia.com.co>
  */

  ini_set('error_reporting', E_ERROR);
  ini_set("display_errors", "1");

  set_time_limit(0);
  ini_set("memory_limit", "512M");

  // Cantidad de Registros para reiniciar conexion
  define("_NUMREG_",100);

  /**
   * Variable para saber si hay o no errores de validacion.
   * @var number
   */
  $nSwitch = 0;

  /**
   * Variable para concatenar los errores de validacion
   * @var string
   */
  $cMsj = "";

	/**
	 * Variable que indica si se debe seguir ejecutando el proceso de la interface despues de cargar los datos en las tablas temporales
	 * @var Number
	 */
  $cEjePro = 0;
  
  /**
   * Nombre(s) de los archivos en excel generados
   */
  $cNomArc = "";
  
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
      include("{$OPENINIT['pathdr']}/opencomex/libs/php/uticones.php");
      include("{$OPENINIT['pathdr']}/opencomex/libs/php/uticoval.php");
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
		include("../../../../config/config.php");
		include("../../../../libs/php/utility.php");
    include("../../../../../libs/php/utiprobg.php");
    include("../../../../libs/php/uticones.php");
    include("../../../../libs/php/uticoval.php");
  }
  
	/**
	 *  Cookie fija
	 */
	$kDf = explode("~", $_COOKIE["kDatosFijos"]);
	$kMysqlHost = $kDf[0];
	$kMysqlUser = $kDf[1];
	$kMysqlPass = $kDf[2];
	$kMysqlDb   = $kDf[3];
	$kUser      = $kDf[4];
	$kLicencia  = $kDf[5];
	$swidth     = $kDf[6];

  $cSystemPath = OC_DOCUMENTROOT;

	if ($_SERVER["SERVER_PORT"] != "") {
		$cEjProBg = ($cEjProBg != "SI") ? "NO" : $cEjProBg;
  }

  if ($_SERVER["SERVER_PORT"] == "") {
		$rTipo     = $_POST['rTipo'];
		$cUsrId    = $_POST['cUsrId'];
		$dDesde    = $_POST['dDesde'];
		$dHasta    = $_POST['dHasta'];
		$cEjProBg  = $_POST['cEjProBg'];
  }
  
  if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
		$cEjePro = 1;

		$strPost = "rTipo~" 		. $rTipo . 
							"|cUsrId~" 		. $cUsrId . 
							"|dDesde~" 		. $dDesde . 
							"|dHasta~" 		. $dHasta . 
							"|cEjProBg~" 	. $cEjProBg;

		$vParBg['pbadbxxx'] = $cAlfa;                                         	//Base de Datos
		$vParBg['pbamodxx'] = "FACTURACION";                                  	//Modulo
		$vParBg['pbatinxx'] = "INFORMEFISCAL";                                  //Tipo Interface
		$vParBg['pbatinde'] = "INFORME FISCAL OPERACIONES";                     //Descripcion Tipo de Interfaz
		$vParBg['admidxxx'] = "";                                             	//Sucursal
		$vParBg['doiidxxx'] = "";                                             	//Do
		$vParBg['doisfidx'] = "";                                             	//Sufijo
		$vParBg['cliidxxx'] = "";                                             	//Nit
		$vParBg['clinomxx'] = "";                                             	//Nombre Importador
		$vParBg['pbapostx'] = $strPost;																					//Parametros para reconstruir Post
		$vParBg['pbatabxx'] = "";                                               //Tablas Temporales
		$vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];                    	//Script
		$vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];                        	//cookie
		$vParBg['pbacrexx'] = 0;                                              	//Cantidad Registros
		$vParBg['pbatxixx'] = 1;                                              	//Tiempo Ejecucion x Item en Segundos
		$vParBg['pbaopcxx'] = "";                                             	//Opciones
		$vParBg['regusrxx'] = $kUser;                                         	//Usuario que Creo Registro

    #Incluyendo la clase de procesos en background
		$ObjProBg = new cProcesosBackground();
		$mReturnProBg = $ObjProBg->fnCrearProcesoBackground($vParBg);

    #Imprimiendo resumen de todo ok.
		if ($mReturnProBg[0] == "true") {
			f_Mensaje(__FILE__, __LINE__, "Proceso en Background Agendado con Exito."); ?>
			<script languaje = "javascript">
					parent.fmwork.fnRecargar();
			</script>
		<?php } else {
			$nSwitch = 1;
			for ($nR = 1; $nR < count($mReturnProBg); $nR++) {
				$cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
				$cMsj .= $mReturnProBg[$nR] . "\n";
			}
			f_Mensaje(__FILE__, __LINE__, $cMsj."Verifique.");
		}
  }
  
  if ($cEjePro == 0) {
		if ($nSwitch == 0) {
      //Proceso
      if ($dDesde == "" || $dHasta == "") {
        $dDesde = $vSysStr['financiero_ano_instalacion_modulo']."-01-01";
        $dHasta = date('Y-m-d');
      }

      $nAnoIni = date('Y', strtotime($dDesde));
      $nAnoFin = date('Y', strtotime($dHasta));

      if( $nAnoIni != $nAnoFin){
        $nSwitch = 1;
        $cMsj .= "Fecha Desde y Fecha Hasta Deben Ser del Mismo Año\n";
      }

      $cAno = $nAnoIni;
      $qCodDat  = "SELECT ";
      $qCodDat .= "$cAlfa.fcod$cAno.comidxxx, ";
      $qCodDat .= "$cAlfa.fcod$cAno.comcodxx, ";
      $qCodDat .= "$cAlfa.fcod$cAno.comcscxx, ";
      $qCodDat .= "$cAlfa.fcod$cAno.comcsc2x, ";
      $qCodDat .= "$cAlfa.fcod$cAno.teridxxx, ";
      $qCodDat .= "$cAlfa.fcod$cAno.terid2xx, ";
      $qCodDat .= "SUM($cAlfa.fcod$cAno.comvlrxx) AS comvlrxx, ";
      $qCodDat .= "SUM($cAlfa.fcod$cAno.comvlr01) AS comvlr01, ";
      $qCodDat .= "$cAlfa.fcod$cAno.regestxx, ";
      $qCodDat .= "$cAlfa.fcoc$cAno.comdipxx, ";
      $qCodDat .= "$cAlfa.fcoc$cAno.regfcrex, ";
      $qCodDat .= "$cAlfa.fcoc$cAno.regusrxx ";
      $qCodDat .= "FROM $cAlfa.fcod$cAno ";
      $qCodDat .= "LEFT JOIN $cAlfa.fcoc$cAno ON $cAlfa.fcod$cAno.comidxxx = $cAlfa.fcoc$cAno.comidxxx AND $cAlfa.fcod$cAno.comcodxx = $cAlfa.fcoc$cAno.comcodxx AND $cAlfa.fcod$cAno.comcscxx = $cAlfa.fcoc$cAno.comcscxx AND $cAlfa.fcod$cAno.comcsc2x = $cAlfa.fcoc$cAno.comcsc2x ";
      $qCodDat .= "WHERE ";
      $qCodDat .= "fcod$cAno.comidxxx = \"F\" AND ";
      $qCodDat .= "fcod$cAno.comctocx = \"IP\" AND ";
      $qCodDat .= "fcod$cAno.regestxx IN (\"ACTIVO\",\"INACTIVO\") AND ";
      $qCodDat .= "fcoc$cAno.regfcrex BETWEEN \"$dDesde\" AND \"$dHasta\" ";
      if(strlen($cUsrId) > 0){
        $qCodDat .= "AND $cAlfa.fcoc$cAno.regusrxx = \"$cUsrId\" ";
      }
      $qCodDat .= "GROUP BY fcod$cAno.comidxxx, fcod$cAno.comcodxx, fcod$cAno.comcscxx, fcod$cAno.comcsc2x ";
      $qCodDat .= "ORDER BY fcoc$cAno.comdipxx ASC, fcod$cAno.regfcrex ASC, fcod$cAno.regusrxx ASC ";

      // echo $qCodDat;

      $xCodDat  = f_MySql("SELECT","",$qCodDat,$xConexion01,"");
      $mCodDat  = array();

      if( mysql_num_rows($xCodDat) > 0 ){
        $nCanReg = 0;
        while ($xRCD = mysql_fetch_array($xCodDat)) {

          $nCanReg++;
          if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = fnReiniciarConexion(); }

          $qClientes  = "SELECT ";
          $qClientes .= "CLIIDXXX, ";
          $qClientes .= "IF(CLINOMXX != \"\",CLINOMXX,IF((TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X))) != \"\",(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X))),\"SIN NOMBRE\")) AS CLINOMXX ";
          $qClientes .= "FROM $cAlfa.SIAI0150 ";
          $qClientes .= "WHERE ";
          $qClientes .= "CLIIDXXX = \"".$xRCD['teridxxx']."\" LIMIT 0,1 ";
          $xClientes  = f_MySql("SELECT","",$qClientes,$xConexion01,"");
          $vClientes  = mysql_fetch_array($xClientes);
          $xRCD['clinomxx'] = $vClientes['CLINOMXX'];

          $qUsuarios  = "SELECT ";
          $qUsuarios .= "USRIDXXX, ";
          $qUsuarios .= "USRNOMXX ";
          $qUsuarios .= "FROM $cAlfa.SIAI0003 ";
          $qUsuarios .= "WHERE ";
          $qUsuarios .= "USRIDXXX = \"".$xRCD['regusrxx']."\" LIMIT 0,1 ";
          $xUsuarios  = f_MySql("SELECT","",$qUsuarios,$xConexion01,"");
          $vUsuarios  = mysql_fetch_array($xUsuarios);
          $xRCD['usrnomxx'] =  $vUsuarios['USRNOMXX'];

          $mCodDat[count($mCodDat)] = $xRCD;
        }
      }else{
        $nSwitch = 1;
        $cMsj .= "No se Encontraron Registros Para Generar el Reporte.\n";
      }

      $nTotales = 0;
      $nTotaIva = 0;
      $cRegUser = "";
      $cComDIp  = "";
      $cComFec  = "";
      $nCierre  = 0;
      //FIN Proceso
    }
  }
	if ($cEjePro == 0) {
    if($nSwitch == 0){
      switch($rTipo) {
        case 1: // Reporte por Pantalla
          ?>
          <html>
            <head>
              <title>Informe Fiscal Operaciones </title>
              <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
              <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
              <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
              <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
              <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">

              <script language="javascript" src="<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
              <script language="javascript" src="<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
            </head>
            <body>
              <center>
                <table width="90%" cellpadding="0" cellspacing="0" border="0">
                  <tr>
                    <td class="name"><center><img width="100" style="right: 5vw;margin-top: 20px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logomalco.jpg"></td>
                  </tr>
                  <tr>
                    <td class="name" width="20%" style="text-align:center;">
                      <br>
                      <h3>
                        INFORME FISCAL OPERACIONES<br>
                        AGENCIA DE ADUANAS MARIO LONDO&Ntilde;O S.A. NIVEL 1<br>
                        <?php echo utf8_decode("NIT: {$vSysStr['financiero_nit_agencia_aduanas']}-".f_Digito_Verificacion($vSysStr['financiero_nit_agencia_aduanas'])); ?>
                      </h3>
                    </td>
                  </tr>
                </table>
                <?php
                foreach($mCodDat as $sKeyCod => $aValCod) {
                  if($cRegUser != $aValCod['regusrxx'] || $cComDIp != $aValCod['comdipxx'] || $cComFec !=  $aValCod['regfcrex']){
                    $cRegUser = $aValCod['regusrxx'];
                    $cComDIp  = $aValCod['comdipxx'];
                    $cComFec  = $aValCod['regfcrex'];
                    ?>
                    <table width="90%" cellpadding="1" cellspacing="1" border="0">
                    <thead>
                      <tr>
                        <td class="name" colspan="7" style="text-align:center;">
                          <h3>
                            FACTURAS ELABORADAS POR <?php echo $aValCod['usrnomxx']; ?><br>
                            CON LA IP: <?php echo $cComDIp; ?> EN LA FECHA (A/M/D): <?php echo str_replace("-","/",$cComFec); ?>
                          </h3>
                        </td>
                      </tr>
                      <tr>
                        <th width="7%"> <center>NRO</center></th>
                        <th width="14%"><center>FACTURA</center></th>
                        <th width="14%"><center>NIT</center></th>
                        <th width="28%"><center>CLIENTE</center></th>
                        <th width="14%"><center>VLR FACTURA</center></th>
                        <th width="14%"><center>VLR IVA</center></th>
                        <th width="7%"> <center>ANULADA</center></th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php
                  } ?>
                  <tr>
                    <td><?php echo $aValCod['comcodxx'] ?></td>
                    <td><?php echo $aValCod['comcscxx'] ?></td>
                    <td><?php echo ($aValCod['teridxxx']."-".f_Digito_Verificacion($aValCod['teridxxx'])) ?></td>
                    <td><?php echo $aValCod['clinomxx'] ?></td>
                    <td style="text-align:right"><?php echo number_format($aValCod['comvlrxx']) ?></td>
                    <td style="text-align:right"><?php echo number_format($aValCod['comvlr01']) ?></td>
                    <td style="text-align:center"><?php echo ($aValCod['regestxx'] == 'ACTIVO' ? 'NO': 'SI') ?></td>
                  </tr>

                  <?php
                  $nTotales += $aValCod['comvlrxx'];
                  $nTotaIva += $aValCod['comvlr01'];

                  $nCierre =  0;
                  if($sKeyCod+1 == count($mCodDat) ){
                    $nCierre =  1;
                  } elseif (isset($mCodDat[$sKeyCod+1])) {
                    if($cRegUser != $mCodDat[$sKeyCod+1]['regusrxx'] || $cComDIp  != $mCodDat[$sKeyCod+1]['comdipxx'] || $cComFec  != $mCodDat[$sKeyCod+1]['regfcrex']){
                      $nCierre =  1;
                    }
                  }

                  if($nCierre == 1){
                    ?>
                        <tr style="font-size:14px;font-weight:bold">
                          <td colspan="3">&nbsp;</td>
                          <td style="text-align:center"><strong>TOTALES</strong></td>
                          <td style="text-align:right"><?php echo number_format($nTotales) ?></td>
                          <td style="text-align:right"><?php echo number_format($nTotaIva) ?></td>
                          <td>&nbsp;</td>
                        </tr>
                      </tbody>
                    </table>
                    <?php
                    $nTotales = 0;
                    $nTotaIva = 0;
                  }
                } ?>
              </center>
            </body>
          </html>
          <?php
        break;
        case 2: // Reporte por excel

          $header .= 'INFORME FISCAL OPERACIONES'."\n";
          $header .= "\n";
          $cNomFile = "INFORME_FISCAL_OPERACIONES_".$_COOKIE['kUsrId'].date("YmdHis").".xls";

          if ($_SERVER["SERVER_PORT"] != "") {
            $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;
          } else {
            $cFile = "{$OPENINIT['pathdr']}/opencomex/" . $vSysStr['system_download_directory'] . "/" . $cNomFile;
          }
          
          if (file_exists($cFile)) {
            unlink($cFile);
          }
    
          $fOp = fopen($cFile, 'a');

          $cData  = '<table width="1024" border="1">';
          $cData .= '<tr>';
          $cData .= '<td class="name" colspan="7" style="font-size:18px;font-weight:bold"><center>INFORME FISCAL OPERACIONES<br>AGENCIA DE ADUANAS MARIO LONDO&Ntilde;O S.A. NIVEL 1<br>'.utf8_decode("NIT: {$vSysStr['financiero_nit_agencia_aduanas']}-".f_Digito_Verificacion($vSysStr['financiero_nit_agencia_aduanas'])).' </center></td>';
          $cData .= '</tr>';
          fwrite($fOp,$cData);

          foreach($mCodDat as $sKeyCod => $aValCod){
            $cData = "";
            if($cRegUser != $aValCod['regusrxx'] || $cComDIp != $aValCod['comdipxx'] || $cComFec !=  $aValCod['regfcrex']){
              $cRegUser = $aValCod['regusrxx'];
              $cComDIp  = $aValCod['comdipxx'];
              $cComFec  = $aValCod['regfcrex'];

              $cData .= '<tr>';
              $cData .= '<td class="name" colspan="7" style="font-size:18px;font-weight:bold"><center> FACTURAS ELABORADAS POR '.$aValCod['usrnomxx'].'<br>CON LA IP: '.$cComDIp.' EN LA FECHA(A/M/D): '.str_replace("-","/",$cComFec).' </center></td>';
              $cData .= '</tr>';
              $cData .= '<tr>';
              $cData .= '<td class="name" width="7%"><b><center>NRO</center></b></td>';
              $cData .= '<td class="name" width="14%"><b><center>FACTURA</center></b></td>';
              $cData .= '<td class="name" width="14%"><b><center>NIT</center></b></td>';
              $cData .= '<td class="name" width="28%"><b><center>CLIENTE</center></b></td>';
              $cData .= '<td class="name" width="14%"><b><center>VLR FACTURA</center></b></td>';
              $cData .= '<td class="name" width="14%"><b><center>VLR IVA</center></b></td>';
              $cData .= '<td class="name" width="7%"><b><center>ANULADA</center></b></td>';
              $cData .= '</tr>';
            }

            $cData .= '<tr>';
            $cData .= '<td class="letra7" style="mso-number-format:\'\@\'" align="left">'.$aValCod['comcodxx'].'</td>';
            $cData .= '<td class="letra7" style="mso-number-format:\'\@\'" align="left">'.$aValCod['comcscxx'].'</td>';
            $cData .= '<td class="letra7" style="mso-number-format:\'\@\'" align="left">'.$aValCod['teridxxx']."-".f_Digito_Verificacion($aValCod['teridxxx']).'</td>';
            $cData .= '<td class="letra7" style="mso-number-format:\'\@\'" align="left">'.$aValCod['clinomxx'].'</td>';
            $cData .= '<td class="letra7" style="mso-number-format:\'\@\'" align="right">'.number_format($aValCod['comvlrxx']).'</td>';
            $cData .= '<td class="letra7" style="mso-number-format:\'\@\'" align="right">'.number_format($aValCod['comvlr01']).'</td>';
            $cData .= '<td class="letra7" style="mso-number-format:\'\@\'" align="center">'.($aValCod['regestxx'] == 'ACTIVO' ? 'NO': 'SI').'</td>';
            $cData .= '</tr>';

            $nTotales += $aValCod['comvlrxx'];
            $nTotaIva += $aValCod['comvlr01'];

            $nCierre =  0;
            if($sKeyCod+1 == count($mCodDat) ){
              $nCierre =  1;
            } elseif (isset($mCodDat[$sKeyCod+1])) {
              if($cRegUser != $mCodDat[$sKeyCod+1]['regusrxx'] || $cComDIp  != $mCodDat[$sKeyCod+1]['comdipxx'] || $cComFec  != $mCodDat[$sKeyCod+1]['regfcrex']){
                $nCierre =  1;
              }
            }

            if($nCierre == 1){
              $cData .= '<tr style="font-size:18px;font-weight:bold">';
              $cData .= '<td colspan="3">&nbsp;</td>';
              $cData .= '<td style="text-align:center"><strong>TOTALES</strong></td>';
              $cData .= '<td style="text-align:right">'.number_format($nTotales).'</td>';
              $cData .= '<td style="text-align:right">'.number_format($nTotaIva).'</td>';
              $cData .= '<td >&nbsp;</td>';
              $cData .= '</tr>';

              $nTotales = 0;
              $nTotaIva = 0;
            }

            fwrite($fOp,$cData);
          }

          $cData = '</table>';
          fwrite($fOp,$cData);
          fclose($fOp);

          if (file_exists($cFile)) {
            // Obtener la ruta absoluta del archivo
            $cAbsolutePath = realpath($cFile);
            $cAbsolutePath = substr($cAbsolutePath,0,strrpos($cAbsolutePath, '/'));

            chmod($cFile, intval($vSysStr['system_permisos_archivos'], 8));
            $cDownLoadFilename = $cDownLoadFilename !== null ? $cDownLoadFilename : basename($cFile);
            if (in_array(realpath($cAbsolutePath), $vSystem_Path_Authorized)) {
              if ($_SERVER["SERVER_PORT"] != "") {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . $cDownLoadFilename);
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($cFile));
  
                ob_clean();
                flush();
                readfile($cFile);
                exit;
              } else {
                $cNomArc = $cNomFile;
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
        break;
        case 3: // Reporte en PDF.
          define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
          require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');

          $nPosX = 5;
          $nPosY = 7;

          class PDF extends FPDF {
            function Header() {
              global $cRoot; global $cPlesk_Skin_Directory; global $vSysStr; global $nPosX; global $nPosY;
              global $cAlfa; global $cTitulo2; global $nRegCon; global $nPag; global $sIP; global $sUsuario;

              $this->SetFont('verdana','B',10);
              $this->SetXY($nPosX,$nPosY+5);
              $this->Cell(200,5,"INFORME FISCAL OPERACIONES",0,0,'C');
              $this->SetXY($nPosX,$nPosY+10);
              $this->Cell(200,5,utf8_decode("AGENCIA DE ADUANAS MARIO LONDOÑO S.A. NIVEL 1"),0,0,'C');
              $this->SetXY($nPosX,$nPosY+15);
              $this->Cell(200,5, utf8_decode("NIT: {$vSysStr['financiero_nit_agencia_aduanas']}-".f_Digito_Verificacion($vSysStr['financiero_nit_agencia_aduanas'])),0,0,'C');
              $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomalco.jpg', $nPosX+156, $nPosY+2,30,21);
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
              for($i=0;$i<count($data);$i++) {
                  $w=$this->widths[$i];
                  $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                  //Save the current position
                  $x=$this->GetX();
                  $y=$this->GetY();
                  //Draw the border
                  // $this->Rect($x,$y,$w,$h);
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
              if($this->GetY()+$h>$this->PageBreakTrigger){
                $this->AddPage($this->CurOrientation);
              }
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

          $pdf = new PDF('P','mm','Letter');
          $pdf->AddFont('verdana','','');
          $pdf->AddFont('verdana','B','');
          $pdf->AliasNbPages();
          $pdf->SetMargins(0,0,0);

          $pdf->AddPage();

          $pdf->SetXY($nPosX,$nPosY+30);

          foreach($mCodDat as $sKeyCod => $aValCod){
            if($cRegUser != $aValCod['regusrxx'] || $cComDIp != $aValCod['comdipxx'] || $cComFec !=  $aValCod['regfcrex']){
              $cRegUser = $aValCod['regusrxx'];
              $cComDIp  = $aValCod['comdipxx'];
              $cComFec  = $aValCod['regfcrex'];

              $pdf->SetFont('verdana','B',8);
              $pdf->SetWidths(array(206));
              $pdf->SetAligns(array("C"));

              $pdf->setX($nPosX);
              $pdf->Row(array("FACTURAS ELABORADAS POR " . $aValCod['usrnomxx'] . " CON LA IP: ".$cComDIp." EN LA FECHA(A/M/D): ".str_replace("-","/",$cComFec)));
              $pdf->Ln();
              $pdf->SetWidths(array(10,22,22,82,26,22,22));
              $pdf->SetAligns(array("C","C","C","C","C","C","C"));

              // Encabezado de tabla
              $pdf->setX($nPosX);
              $pdf->Row(array("NRO","FACTURA","NIT","CLIENTE","VLR FACTURA","VLR IVA","ANULADA"));
              $pdf->Ln();
              $pdf->SetFont('verdana','',8);
              $pdf->SetAligns(array("L","L","L","L","R","R","C"));
            }

            $pdf->setX($nPosX);
            $pdf->Row(array($aValCod['comcodxx'],
                            $aValCod['comcscxx'],
                            $aValCod['teridxxx']."-".f_Digito_Verificacion($aValCod['teridxxx']),
                            $aValCod['clinomxx'],
                            number_format($aValCod['comvlrxx']),
                            number_format($aValCod['comvlr01']),
                            ($aValCod['regestxx'] == 'ACTIVO' ? 'NO': 'SI')
            ));

            $nTotales += $aValCod['comvlrxx'];
            $nTotaIva += $aValCod['comvlr01'];

            $nCierre =  0;
            if($sKeyCod+1 == count($mCodDat) ){
              $nCierre =  1;
            } elseif (isset($mCodDat[$sKeyCod+1])) {
              if($cRegUser != $mCodDat[$sKeyCod+1]['regusrxx'] || $cComDIp  != $mCodDat[$sKeyCod+1]['comdipxx'] || $cComFec  != $mCodDat[$sKeyCod+1]['regfcrex']){
                $nCierre =  1;
              }
            }

            if($nCierre == 1){
              $pdf->SetWidths(array(140,22,22,22));
              $pdf->SetAligns(array("C","R","R","C"));

              $pdf->SetFont('verdana','B',8);
              $pdf->setXY($nPosX,$pdf->getY()+2);
              $pdf->Row(array("TOTALES", number_format($nTotales), number_format($nTotaIva), ''));

              $pdf->setXY($nPosX,$pdf->GetY()+5);

              $nTotales = 0;
              $nTotaIva = 0;
            }
          }

          $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$kUser."_".date("YmdHis").".pdf";

          $pdf->Output($cFile);

          if (file_exists($cFile)){
            chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
          } else {
            f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
          }

          echo "<html><script>document.location='$cFile';</script></html>";
        break;
      }
    }else{
      f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique");
    }
  }

  if ($_SERVER["SERVER_PORT"] == "") {
		/**
		 * Se ejecuto por el proceso en background
		 * Actualizo el campo de resultado y nombre del archivo
		 */
		$vParBg['pbarespr'] = ($nSwitch == 0) ? "EXITOSO" : "FALLIDO";  //Resultado Proceso
		$vParBg['pbaexcxx'] = $cNomArc;                                 //Nombre Archivos Excel
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
	} 

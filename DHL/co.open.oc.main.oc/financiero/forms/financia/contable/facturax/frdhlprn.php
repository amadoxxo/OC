<?php
  /**
	 * Imprime Factura de VentaAGENCIA ADUANAS DHL EXPRESS.
	 * --- Descripcion: Permite Imprimir Factura de VentaAGENCIA ADUANAS DHL EXPRESS.
	 * @author Juan Jose Trujillo <juan.trujillo@open-eb.co>
	 * @version 001
	 */
  
  ini_set('error_reporting', E_ERROR);
  ini_set("display_errors","1");
  date_default_timezone_set('America/Bogota');
  
  include("../../../../libs/php/utility.php");
  include("../../../../libs/php/utiliqdo.php");
  
  //Generacion del codigo QR
  require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/phpqrcode/qrlib.php');
  
  define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
	require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');
  
  $nSwitch=0;

  // Validacion de Comprobante Repetido
  $mPrints = f_Explode_Array($prints,"|","~");
  $cAno =  substr($mPrints[0][4],0,4);
  // Busco la resolucion en la tabla GRM00138.
  $qResFac  = "SELECT rescomxx ";
  $qResFac .= "FROM $cAlfa.fpar0138 ";
  $qResFac .= "WHERE ";
  $qResFac .= "rescomxx LIKE \"%{$mPrints[0][0]}~{$mPrints[0][1]}%\" AND ";
  $qResFac .= "regestxx = \"ACTIVO\" LIMIT 0,1";
  $xResFac  = f_MySql("SELECT","",$qResFac,$xConexion01,"");
	//f_Mensaje(__FILE__,__LINE__,$qResFac." ~ ".mysql_num_rows($xResFac));
  $mResFac = mysql_fetch_array($xResFac);
  // Fin de Busco la resolucion en la tabla GRM00138.

	// Armo la variable con los codigos de los comprobantes de facturacion para la actualizacion del consecutivo despues de grabar la factura.
  $mCodCom = f_Explode_Array($mResFac['rescomxx'],"|","~");
  $cCodigos_Comprobantes = "";
  for ($i=0;$i<count($mCodCom);$i++) {
		$cCodigos_Comprobantes .= "\"";
		$cCodigos_Comprobantes .= "{$mCodCom[$i][1]}";
		$cCodigos_Comprobantes .= "\"";
		if ($i < (count($mCodCom) -1)) { 
			$cCodigos_Comprobantes .= ","; 
		}
  }
  // Fin de Armo la variable con los codigos de los comprobantes de facturacion para la actualizacion del consecutivo despues de grabar la factura.

  $qValCsc  = "SELECT comidxxx,comcodxx,comcscxx,comcsc2x ";
	$qValCsc .= "FROM $cAlfa.fcoc$cAno ";
	$qValCsc .= "WHERE ";
	$qValCsc .= "comidxxx = \"{$mPrints[0][0]}\"  AND ";
	$qValCsc .= "comcodxx IN ($cCodigos_Comprobantes) AND ";
	$qValCsc .= "comcscxx = \"{$mPrints[0][2]}\"";
	$xValCsc  = f_MySql("SELECT","",$qValCsc,$xConexion01,"");
	//f_Mensaje(__FILE__,__LINE__,$qValCsc." ~ ".mysql_num_rows($xValCsc));
	if (mysql_num_rows($xValCsc) > 1) {
		$nSwitch = 1;
		f_Mensaje(__FILE__,__LINE__,"El Documento [{$mPrints[0][0]}-{$mPrints[0][1]}-{$mPrints[0][2]}] No se puede Imprimir porque su Consecutivo se Encuentra Repetido en el Modulo de Contabilidad, Verifique");
	}
  // Fin de Validacion de Comprobante Repetido

  $qTexCli = "SELECT ";
  $qTexCli .= "tfvtitxx, ";
  $qTexCli .= "tfvcontx ";
  $qTexCli .= "FROM $cAlfa.zdex0011";
	$xTexCli  = f_MySql("SELECT","",$qTexCli,$xConexion01,"");
  if (mysql_num_rows($xTexCli) > 0) {
    $vTexCli = mysql_fetch_array($xTexCli);
  }

  $vMemo=explode("|",$prints);
  $permisos=0;
  $zCadPer="|";
  $resolucion=0;
  $zCadRes="|";
  ///////////////////////
  $fomularios=0;
  $zCadFor="";
  ///////////////////////
  $diferencia=0;

  $paso=0;
  for($u=0; $u<count($vMemo); $u++) {
		if ($vMemo[$u]!=""){
			$zMatriz=explode("~",$vMemo[$u]);
      ////// CABECERA 1001 /////
			$qCocDat  = "SELECT * ";
			$qCocDat .= "FROM $cAlfa.fcoc$cAno ";
			$qCocDat .= "WHERE $cAlfa.fcoc$cAno.comidxxx = \"{$zMatriz[0]}\" AND ";
			$qCocDat .= "$cAlfa.fcoc$cAno.comcodxx = \"{$zMatriz[1]}\" AND ";
			$qCocDat .= "$cAlfa.fcoc$cAno.comcscxx = \"{$zMatriz[2]}\" AND ";
			$qCocDat .= "$cAlfa.fcoc$cAno.comcsc2x = \"{$zMatriz[3]}\" LIMIT 0,1";
			//f_Mensaje(__FILE__,__LINE__,$qCocDat);
			$xCocDat  = f_MySql("SELECT","",$qCocDat,$xConexion01,"");
			$nFilCoc  = mysql_num_rows($xCocDat);
			if ($nFilCoc > 0) {
				$vCocDat = mysql_fetch_array($xCocDat);
				if($vCocDat['comprnxx']=="IMPRESO" && $vCocDat['regestxx'] != "INACTIVO" && $_COOKIE['kModo'] != "VERFACTURA"){
					$zFac=$zMatriz[0].$zMatriz[1]."-".$zMatriz[2]."|";
					$zCadPer .=$zFac;
					$permisos=1;
				}
			}
		}
  }

  if($permisos==1){
	  $nSwitch=1;
		f_Mensaje(__FILE__,__LINE__,"Las Siguientes Facturas No tienen Permiso de Impresion [$zCadPer], Verifique.");?>
		<form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
		<script languaje = "javascript">
			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
			document.forms['frgrm'].submit();
		</script>
		<?php
  }

  if($fomularios==1){
		$nSwitch=1;
		f_Mensaje(__FILE__,__LINE__,"Las Siguientes Facturas Presentan Inconsistencias con Formularios: \n $zCadFor --- Verifique --- ");?>
		<form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
		<script languaje = "javascript">
			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
			document.forms['frgrm'].submit();
		</script>
		<?php
  }

  if($resolucion==1){
		$nSwitch=1;
		f_Mensaje(__FILE__,__LINE__,"Las Siguientes Facturas no Tienen Resolucion Activa [$zCadRes], Verifique."); ?>
		<form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
		<script languaje = "javascript">
			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
			document.forms['frgrm'].submit();
		</script>
		<?php
  }

  $mPrn = explode("|",$prints);
  for ($nn=0;$nn<count($mPrn);$nn++) {
    if (strlen($mPrn[$nn]) > 0) {
      $vComp = explode("~",$mPrn[$nn]);
      $cComId   = $vComp[0];
      $cComCod  = $vComp[1];
      $cComCsc  = $vComp[3];
      $cComCsc2 = $vComp[3];
      $cRegFCre = $vComp[4];
      $cAno     =  substr($cRegFCre,0,4);
    }
  }
  
  // Codigo para actualizar campo de impresion
  if($vCocDat['regestxx'] != "INACTIVO" && $_COOKIE['kModo'] != "VERFACTURA") {
    $mUpdPrn = array(array('NAME'=>'comprnxx','VALUE'=>'IMPRESO'  ,'CHECK'=>'SI'),
                      array('NAME'=>'comidxxx','VALUE'=>$cComId    ,'CHECK'=>'WH'),
                      array('NAME'=>'comcodxx','VALUE'=>$cComCod   ,'CHECK'=>'WH'),
                      array('NAME'=>'comcscxx','VALUE'=>$cComCsc   ,'CHECK'=>'WH'),
                      array('NAME'=>'comcsc2x','VALUE'=>$cComCsc2  ,'CHECK'=>'WH'));
    if (f_MySql("UPDATE","fcoc$cAno",$mUpdPrn,$xConexion01,$cAlfa)){
    }else{
      $nSwitch = 1;
    }
  }
  // Codigo para actualizar campo de impresion

  ////// CABECERA 1001 /////
  $qCocDat  = "SELECT ";
  $qCocDat .= "$cAlfa.fcoc$cAno.*, ";
  $qCocDat .= "IF($cAlfa.fpar0008.sucidxxx != \"\",$cAlfa.fpar0008.sucidxxx,\"SUCURSAL SIN ID\") AS sucidxxx, ";
  $qCocDat .= "IF($cAlfa.fpar0008.sucdesxx != \"\",$cAlfa.fpar0008.sucdesxx,\"SUCURSAL SIN DESCRIPCION\") AS sucdesxx, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,\"CLIENTE SIN NOMBRE\") AS CLINOMCX, ";
  $qCocDat .= "$cAlfa.A.CLINRPXX, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,\" SIN NOMBRE\") AS CLINOMXX, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.CLIDIRXX != \"\",$cAlfa.SIAI0150.CLIDIRXX,\"SIN DIRECCION\") AS CLIDIRXX, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.CLITELXX != \"\",$cAlfa.SIAI0150.CLITELXX,\"SIN TELEFONO\") AS CLITELXX, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.CLICONTX != \"\",$cAlfa.SIAI0150.CLICONTX,\"SIN RESPONSABLE\") AS CLICONTX, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.PAIIDXXX != \"\",$cAlfa.SIAI0150.PAIIDXXX,\"\") AS PAIIDXXX, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.DEPIDXXX != \"\",$cAlfa.SIAI0150.DEPIDXXX,\"\") AS DEPIDXXX, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.CIUIDXXX != \"\",$cAlfa.SIAI0150.CIUIDXXX,\"\") AS CIUIDXXX ";
  $qCocDat .= "FROM $cAlfa.fcoc$cAno ";
  $qCocDat .= "LEFT JOIN $cAlfa.fpar0008 ON $cAlfa.fcoc$cAno.ccoidxxx = $cAlfa.fpar0008.ccoidxxx ";
  $qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcoc$cAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
  $qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 AS A ON $cAlfa.fcoc$cAno.terid2xx = $cAlfa.A.CLIIDXXX ";
  $qCocDat .= "WHERE $cAlfa.fcoc$cAno.comidxxx = \"$cComId\" AND ";
  $qCocDat .= "$cAlfa.fcoc$cAno.comcodxx = \"$cComCod\" AND ";
  $qCocDat .= "$cAlfa.fcoc$cAno.comcscxx = \"$cComCsc\" AND ";
  $qCocDat .= "$cAlfa.fcoc$cAno.comcsc2x = \"$cComCsc2\" LIMIT 0,1";
  $xCocDat  = f_MySql("SELECT","",$qCocDat,$xConexion01,"");
  if (mysql_num_rows($xCocDat) > 0) {
    $vCocDat  = mysql_fetch_array($xCocDat);
  }

  ##Traigo Ciudad del Facturado A ##
	$qCiuFac  = "SELECT CIUDESXX ";
	$qCiuFac .= "FROM $cAlfa.SIAI0055 ";
	$qCiuFac .= "WHERE ";
	$qCiuFac .= "$cAlfa.SIAI0055.PAIIDXXX = \"{$vCocDat['PAIIDXXX']}\" AND ";
	$qCiuFac .= "$cAlfa.SIAI0055.DEPIDXXX = \"{$vCocDat['DEPIDXXX']}\" AND ";
	$qCiuFac .= "$cAlfa.SIAI0055.CIUIDXXX = \"{$vCocDat['CIUIDXXX']}\" AND ";
	$qCiuFac .= "$cAlfa.SIAI0055.REGESTXX = \"ACTIVO\" LIMIT 0,1";
	$xCiuFac  = f_MySql("SELECT","",$qCiuFac,$xConexion01,"");
	// f_Mensaje(__FILE__,__LINE__,$qCiuFac."~".mysql_num_rows($xCiuFac));
	if (mysql_num_rows($xCiuFac) > 0) {
		$vCiuFac = mysql_fetch_array($xCiuFac);
  }
  ##Fin Traigo Ciudad del Facturado A ##
  
  ##Traigo Pais del Facturado A ##
  $qPaisFac  = "SELECT $cAlfa.SIAI0052.PAIDESXX ";
  $qPaisFac .= "FROM $cAlfa.SIAI0052 ";
  $qPaisFac .= "WHERE ";
  $qPaisFac .= "$cAlfa.SIAI0052.PAIIDXXX = \"{$vCocDat['PAIIDXXX']}\" LIMIT 0,1 ";
  $xPaisFac  = f_MySql("SELECT","",$qPaisFac,$xConexion01,"");
  if (mysql_num_rows($xPaisFac) > 0) {
    $vPaisFac = mysql_fetch_array($xPaisFac);
  }
  ##Fin Traigo Pais del Facturado A ##

  $cCamVlr = ($vCocDat['CLINRPXX'] == "SI") ? "comvlrme" : "comvlrxx";

  $cDocId  = "";
  $cDocSuf = "";
  $cDosInc = array();
  $vDocPed = array();
  $mDoiId = explode("|",$vCocDat['comfpxxx']);
  for ($i=0;$i<count($mDoiId);$i++) {
    if($mDoiId[$i] != ""){
      $vDoiId  = explode("~",$mDoiId[$i]);
      if($cDocId == "") {
        $cDocId  = $vDoiId[2];
        $cDocSuf = $vDoiId[3];
        $cSucId  = $vDoiId[15];
      }

      if (!in_array($vDoiId[2], $cDosInc)) {
        $cDosInc[] = $vDoiId[2];
      }

      ##Busco Do en la sys00121 Tabla de Do's Financieros y traigo Tipo de Operacion ##
      $vDceDat  = array();
      $qDceDat  = "SELECT doctipxx, docpedxx ";
      $qDceDat .= "FROM $cAlfa.sys00121 ";
      $qDceDat .= "WHERE ";
      $qDceDat .= "sucidxxx = \"{$vDoiId[15]}\" AND ";
      $qDceDat .= "docidxxx = \"{$vDoiId[2]}\" AND ";
      $qDceDat .= "docsufxx = \"{$vDoiId[3]}\" ";
      $xDceDat = f_MySql("SELECT","",$qDceDat,$xConexion01,"");
      if (mysql_num_rows($xDceDat) > 0) {
        $vDceDat = mysql_fetch_array($xDceDat);
      }
      ##Fin Busco Do en la sys00121 Tabla de Do's Financieros y traigo Tipo de Operacion ##

      switch ($vDceDat['doctipxx']) {
        case "IMPORTACION":
        case "TRANSITO":
          ##Traigo Datos de la SIAI0200 DATOS DEL DO ##
          $qDoiDat = "SELECT ";
          $qDoiDat .= "$cAlfa.SIAI0200.DOIPEDXX ";
          $qDoiDat .= "FROM $cAlfa.SIAI0200 ";
          $qDoiDat .= "WHERE ";
          $qDoiDat .= "$cAlfa.SIAI0200.DOIIDXXX = \"{$vDoiId[2]}\" AND ";
          $qDoiDat .= "$cAlfa.SIAI0200.DOISFIDX = \"{$vDoiId[3]}\" AND ";
          $qDoiDat .= "$cAlfa.SIAI0200.ADMIDXXX = \"{$vDoiId[15]}\" LIMIT 0,1";
          $xDoiDat = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
          $vDoiDat = mysql_fetch_array($xDoiDat);

          if ($vDoiDat['DOIPEDXX'] != "") {
            $vDocPed[] = "P.O: ".$vDoiDat['DOIPEDXX'];
          }
          ##Fin Traigo Datos de la SIAI0200 DATOS DEL DO ##
          break;
        case "EXPORTACION":
          ## Consulto Datos de Do en Exportaciones tabla siae0199 ##
          $qDexDat = "SELECT dexpedxx ";
          $qDexDat .= "FROM $cAlfa.siae0199 ";
          $qDexDat .= "WHERE ";
          $qDexDat .= "$cAlfa.siae0199.dexidxxx = \"{$vDoiId[2]}\" AND ";
          $qDexDat .= "$cAlfa.siae0199.admidxxx = \"{$vDoiId[15]}\" ";
          $xDexDat = f_MySql("SELECT","",$qDexDat,$xConexion01,"");
          $vDexDat = mysql_fetch_array($xDexDat);

          if ($vDexDat['dexpedxx'] != "") {
            $vDocPed[] = "P.O: ".$vDexDat['dexpedxx'];
          }
          ##Fin Cargo Variables para Impresion de Datos de Do ##
          break;
        case "OTROS":
          if ($vDceDat['docpedxx'] != "") {
            $vDocPed[] = "P.O: ".$vDceDat['docpedxx'];
          }
          break;
      }
    }
  }

  ## Informacion de resolucion de facturacion ##
  $qResDat  = "SELECT * ";
  $qResDat .= "FROM $cAlfa.fpar0138 ";
  $qResDat .= "WHERE ";
  $qResDat .= "rescomxx LIKE \"%{$cComId}~{$cComCod}%\" AND ";
  $qResDat .= "regestxx = \"ACTIVO\" LIMIT 0,1";
  $xResDat  = f_MySql("SELECT","",$qResDat,$xConexion01,"");
  if (mysql_num_rows($xResDat) > 0) {
    $vResDat = mysql_fetch_array($xResDat);
  }
  ## Fin Informacion de resolucion de facturacion ##

  ## Nombre del usuario de Creacion ##
  $qUsrNom  = "SELECT USRNOMXX ";
  $qUsrNom .= "FROM $cAlfa.SIAI0003 ";
  $qUsrNom .= "WHERE ";
  $qUsrNom .= "USRIDXXX = \"{$vCocDat['regusrxx']}\" LIMIT 0,1 ";
  $xUsrNom  = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qUsrNom."~".mysql_num_rows($xUsrNom));
  $vUsrNom  = mysql_fetch_array($xUsrNom);
  ## Fin Nombre del usuario de Creacion ##

  ##Trayendo Datos de Do Dependiendo del Tipo de Operacion ##
  $vDatDo = f_Datos_Operativos_Do($cSucId, $cDocId, $cDocSuf);
  $vDceDat = $vDatDo['decdatxx'];
  $cTasCam = $vDatDo['tascamxx']; //Tasa de Cambio
  $cDocTra = $vDatDo['doctraxx']; //Documento de Transporte
  $cBultos = $vDatDo['bultosxx']; //Bultos
  $cPesBru = $vDatDo['pesbruxx']; //Peso Bruto
  $cPesNet = $vDatDo['pesnetxx']; //Peso Neto
  $nValAdu = $vDatDo['valaduxx']; //Valor en aduana
  $cOpera  = $vDatDo['operaxxx']; //texto valor CIF: o FOB: segun el tipo operacion del tramite
  $cPedido = $vDatDo['pedidoxx']; //Pedido
  $cAduana = $vDatDo['aduanaxx']; //Descripcion Aduana
  $cNomVen = $vDatDo['nomvenxx']; //Nombre Vendedor
  $cOrdCom = $vDatDo['ordcomxx']; //Orden de Compra
  $cPaiOri = $vDatDo['paiorixx']; //Pais de Origen
  $dReaArr = $vDatDo['fecrearr']; //Fecha real de arribo
  $cCiuOri = $vDatDo['ciuorixx']; //Ciudad Origen
  $cCiuDes = $vDatDo['ciudesxx']; //Ciudad Destino
  $cFecTra = $vDatDo['dgefdtxx']; //Fecha Doc. Transporte
	$cDoitra = $vDatDo['tradesxx']; //Transportadora
	$cCodEmb = $vDatDo['temidxxx']; //Codigo Embalaje
	$cDgeTrm = $vDatDo['dgetrmxx']; //TRM de la primera declaracion
  ###Fin Trayendo Datos de Do Dependiendo del Tipo de Operacion ##

  ##Traigo los Documentos que estan marcados como PAGO IMPUESTOS##
  $qDatCom  = "SELECT ";
  $qDatCom .= "ctoidxxx, ";
  $qDatCom .= "pucidxxx ";
  $qDatCom .= "FROM $cAlfa.fpar0119 ";
  $qDatCom .= "WHERE ";
  $qDatCom .= "(ctoptaxg = \"SI\" OR ctoptaxl = \"SI\") AND ";
  $qDatCom .= "regestxx = \"ACTIVO\" ";
  $xDatCom  = f_MySql("SELECT","",$qDatCom,$xConexion01,"");
  $vComImp = array();
  //f_Mensaje(__FILE__,__LINE__,"$qDatCom~".mysql_num_rows($xDatCom));
  while($xRDC = mysql_fetch_array($xDatCom)){
    $nInd_mComImp = count($vComImp);
    $vComImp[] = $xRDC['ctoidxxx']."~".$xRDC['pucidxxx'];
  }
  ##Fin Traigo los Documentos que estan marcados como PAGO IMPUESTOS##

  ##Traigo el codigo de la Unidad de medida por Concepto
  $qCtoCon  = "SELECT ctoidxxx, ctoclapr, cceidxxx, umeidxxx, ctochald ";
  $qCtoCon .= "FROM $cAlfa.fpar0121 ";
  $xCtoCon  = mysql_query($qCtoCon, $xConexion01);
  //f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
  while ($xRC = mysql_fetch_assoc($xCtoCon)) {
    $vCtoCon["{$xRC['ctoidxxx']}"] = $xRC;
  }
  ##Fin Traigo el codigo de la Unidad de medida por Concepto

	## Codigo para imprimir los ingresos para terceros
  $mIT = f_Explode_Array($vCocDat['commemod'],"|","~");
  $mIngTer = array();
  for ($i=0;$i<count($mIT);$i++) {
    if ($mIT[$i][1] != "") {
      $nSwitch_Encontre_Concepto = 0;
      for ($j=0;$j<count($mIngTer);$j++) {
        if ($mIngTer[$j][1] == $mIT[$i][1]) {
          $nSwitch_Encontre_Concepto = 1;
          $mIngTer[$j][7] += $mIT[$i][7]; // Acumulo el valor de ingreso para tercero.
          $mIngTer[$j][15] += $mIT[$i][15]; // Acumulo base de iva.
          $mIngTer[$j][16] += $mIT[$i][16]; // Acumulo valor del iva.
          $mIngTer[$j][20] += $mIT[$i][20]; // Acumulo el valor de ingreso para tercero en Dolares.
          $mIngTer[$j][100] = ((strlen($mIngTer[$j][100]) + strlen($mIT[$i][5]) + 1) < 50) ? $mIngTer[$j][100]."/".$mIT[$i][5] : $mIngTer[$j][100];
          $mIngTer[$j][100] = (substr($mIngTer[$j][100],0,1) == "/") ? substr($mIngTer[$j][100],1,strlen($mIngTer[$j][100])) : $mIngTer[$j][100];
          $mIngTer[$j][101] = ($vCtoCon["{$mIT[$i][1]}"]['umeidxxx'] != '') ? $vCtoCon["{$mIT[$i][1]}"]['umeidxxx'] : "A9"; // Unidad de medida

          $j = count($mIngTer); // Me salgo del FOR cuando encuentro el concepto.
        }
      }

      if ($nSwitch_Encontre_Concepto == 0) { // No encontre el ingreso para tercero en la matrix $mIngTer
        // Si es un pago de impuestos se cambia la descripcion
        if (in_array("{$mIT[$i][1]}~{$mIT[$i][9]}", $vComImp)) {
          $mIT[$i][2] = 'TRIBUTOS ADUANEROS';
        }
        $nInd_mIngTer = count($mIngTer);
        $mIngTer[$nInd_mIngTer] = $mIT[$i]; // Ingreso el registro como nuevo.
        $mIngTer[$nInd_mIngTer][100] = ((strlen($mIngTer[$nInd_mIngTer][100]) + strlen($mIT[$i][5]) + 1) < 50) ? $mIngTer[$nInd_mIngTer][100]."/".$mIT[$i][5] : $mIngTer[$nInd_mIngTer][100];
        $mIngTer[$nInd_mIngTer][100] = (substr($mIngTer[$nInd_mIngTer][100],0,1) == "/") ? substr($mIngTer[$nInd_mIngTer][100],1,strlen($mIngTer[$nInd_mIngTer][100])) : $mIngTer[$nInd_mIngTer][100];
        $mIngTer[$nInd_mIngTer][101] = ($vCtoCon["{$mIT[$i][1]}"]['umeidxxx'] != '') ? $vCtoCon["{$mIT[$i][1]}"]['umeidxxx'] : "A9"; // Unidad de medida
      }
    }
  }
  ## Fin de Codigo para imprimir los ingresos para terceros

  ## Se obtienen los Ingresos Propios por Concepto de Facturacion ##
  $mDatIP  = array();
  $mPCC    = array();

  // DETALLE 1002
  $qCodDat  = "SELECT DISTINCT ";
  $qCodDat .= "$cAlfa.fcod$cAno.* ";
  $qCodDat .= "FROM $cAlfa.fcod$cAno ";
  $qCodDat .= "WHERE $cAlfa.fcod$cAno.comidxxx = \"$cComId\" AND ";
  $qCodDat .= "$cAlfa.fcod$cAno.comcodxx = \"$cComCod\" AND ";
  $qCodDat .= "$cAlfa.fcod$cAno.comcscxx = \"$cComCsc\" AND ";
  $qCodDat .= "$cAlfa.fcod$cAno.comcsc2x = \"$cComCsc2\" ORDER BY ABS($cAlfa.fcod$cAno.comseqxx) ASC ";
  $xCodDat  = f_MySql("SELECT","",$qCodDat,$xConexion01,"");

  $mCodDat = array();
  if (mysql_num_rows($xCodDat) > 0) {
    // Cargo la Matriz con los ROWS del Cursor
    $iA=0;
    $mPCC = array(); $mIP = array();
    while ($xRCD = mysql_fetch_array($xCodDat)) {
      $mCodDat[$iA] = $xRCD;
      $iA++;
      if($xRCD['comctocx'] == "PCC") {
        $cObs    = "";
        $cCan    = "";
        $nCan    = 0;
        $cAplCan = "";
        $nComObs_PCC = stripos($xRCD['comobsxx'], "[");
        if($nComObs_PCC > 0){
          $mAuxCan = explode("CANTIDAD:",substr($xRCD['comobsxx'],$nComObs_PCC,strlen($xRCD['comobsxx'])));
          $cCan = "";
          if(count($mAuxCan) > 1) {
            $cCan    = str_replace(array(",","]"," "), "", $mAuxCan[1]);
            $nCan    = $cCan;
            $cAplCan = "SI";
          } 
          $cObs = substr(substr($xRCD['comobsxx'],0,$nComObs_PCC),0,70);
        }else{
          $cObs = substr($xRCD['comobsxx'],0,70);
        }
              
        $mPCC[$xRCD['ctoidxxx']]['comctocx']  = $xRCD['comctocx'];
        $mPCC[$xRCD['ctoidxxx']]['pucidxxx']  = $xRCD['pucidxxx'];
        $mPCC[$xRCD['ctoidxxx']]['comvlrxx'] += $xRCD['comvlrxx'];
        $mPCC[$xRCD['ctoidxxx']]['ctoidxxx']  = $xRCD['ctoidxxx'];
        $mPCC[$xRCD['ctoidxxx']]['comobsxx']  = ($mPCC[$xRCD['ctoidxxx']]['comobsxx'] == "")?$cObs:$mPCC[$xRCD['ctoidxxx']]['comobsxx'];
        $mPCC[$xRCD['ctoidxxx']]['comcanxx'] += $nCan;
        $mPCC[$xRCD['ctoidxxx']]['comcanap']  = ($mPCC[$xRCD['ctoidxxx']]['comcanap'] == "SI")?$mPCC[$xRCD['ctoidxxx']]['comcanap']:$cAplCan;
      }
      
      //Agrupando por Concepto
      if($xRCD['comctocx'] == "IP") {
        //Inicializo variable para agrupar
        $nSwitch_Encontre_Concepto = 0;
    
        $vDatosIp = array();
        $vDatosIp = f_Cantidad_Ingreso_Propio($xRCD['comobsxx'],'',$xRCD['sucidxxx'],$xRCD['docidxxx'],$xRCD['docsufxx']);
        //Los IP se agrupan por Sevicio
        for($j=0;$j<count($mIP);$j++){
          if($mIP[$j]['ctoidxxx'] == $xRCD['ctoidxxx'] && $mIP[$j]['seridxxx'] == $xRCD['seridxxx']){
            $nSwitch_Encontre_Concepto = 1;

            $mIP[$j]['comvlrxx'] += $xRCD['comvlrxx'];
            $mIP[$j]['comvlrme'] += $xRCD['comvlrme'];
            $mIP[$j]['compivax']  = $xRCD['compivax']; // Porcentaje IVA
            $mIP[$j]['comvlr01'] += $xRCD['comvlr01']; // Valor Iva
            //Cantidad FE
            $mIP[$j]['canfexxx'] += $vDatosIp[1];

            //Cantidad por condicion especial
            for ($nP=0; $nP<count($vDatosIp[3]); $nP++) {
              $mIP[$j]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] += $vDatosIp[3][$nP]['valpdfxx'];
            }
          }
        }

        if ($nSwitch_Encontre_Concepto == 0) {
          $nInd_mConData = count($mIP);
          $mIP[$nInd_mConData] = $xRCD;
          $mIP[$nInd_mConData]['comobsxx'] = $vDatosIp[0];
          $mIP[$nInd_mConData]['canfexxx'] = $vDatosIp[1];
          $mIP[$nInd_mConData]['unidadfe'] = $vDatosIp[2];

          for ($nP=0; $nP<count($vDatosIp[3]); $nP++) {
            $mIP[$nInd_mConData]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] = $vDatosIp[3][$nP]['valpdfxx'];
          }
        }
      }
    }// Fin While

    foreach ($mIP as $cKey => $mValores) {
      $mDatIP[] = $mValores;
    }
  
    foreach ($mPCC as $cKey => $mValores) {
      $mDatPCC[] = $mValores;
    }
    // Fin de Cargo la Matriz con los ROWS del Cursor
  }
  
  ##Traigo la Forma de Pago##
  $vCodFormPago = explode("~", $vCocDat['comobs2x']);
  $cFormaPago = "";
  if ($vCodFormPago[14] != "") {
    //Buscando descripcion
    $cFormaPago = ($vCodFormPago[14] == 1) ? "CONTADO" : "CREDITO";
  }
  ##Fin Traigo la Forma de Pago##

  ##Traigo el Medio de Pago##
  $cMedioPago = "";
  if ($vCodFormPago[15] != "") {
    $qMedPag  = "SELECT mpadesxx ";
    $qMedPag .= "FROM $cAlfa.fpar0155 ";
    $qMedPag .= "WHERE mpaidxxx = \"{$vCodFormPago[15]}\" AND ";
    $qMedPag .= "regestxx = \"ACTIVO\" LIMIT 0,1";
    $xMedPag  = f_MySql("SELECT","",$qMedPag,$xConexion01,"");
    if (mysql_num_rows($xMedPag) > 0) {
      $vMedPag = mysql_fetch_array($xMedPag);
      $cMedioPago = $vMedPag['mpadesxx'];
    }
  }
  ##Fin Traigo el Medio de Pago##

  if($nSwitch == 0){
    class PDF extends FPDF {
      function Header() {
        global $cAlfa;      global $cRoot;      global $cPlesk_Skin_Directory;        global $vSysStr;    global $_COOKIE;
        global $vCocDat;    global $vResDat;    global $cDocId;     global $cDocTra;  global $vCiuFac;    global $nValAdu; 
        global $cFormaPago; global $vMedPag;    global $vPaisFac;   global $cTasCam;  global $cMedioPago;

        $posy = 5;
        $posx = 9;

        //Contenedor Principal 
        $this->SetFillColor(255, 204, 0);
        $this->Rect($posx,$posy,198,270,'DF');
        
        //Contenedor Datos Factura
        $this->SetFillColor(255, 255, 255);
        $this->Rect($posx+5,$posy+2,188,37,'DF');

        //logo
        $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_dhl_express.jpg', $posx+8, $posy+5, 50, 15);

        //Contenedor Datos Ofe
        $cNitAduana  = number_format($vSysStr['financiero_nit_agencia_aduanas'], 0, '', '.')."-";
        $cNitAduana .= f_Digito_Verificacion($vSysStr['financiero_nit_agencia_aduanas']);
        $this->setXY($posx+7, $posy+20);
        $this->SetFont('Arial','B',8);
        $this->Cell(50,4,"NIT ".$cNitAduana,0,0,'C');
        $this->Ln(4.5);
        $this->setX($posx+7);
        $this->SetFont('Arial','',8);
        $this->Cell(50,4,utf8_decode('Carrera 85D # 46A 38'),0,0,'C');
        $this->Ln(4.5);
        $this->setX($posx+7);
        $this->Cell(50,4,utf8_decode('Teléfono: 7477777'),0,0,'C');
        $this->Ln(4.5);
        $this->setX($posx+7);
        $this->Cell(50,4,utf8_decode('BOGOTA, D.C'),0,0,'C');

        //Resolucion DIAN
        //Traigo numero de Meses entre Desde y Hasta
        $dFechaInicial  = date_create($vResDat['resfdexx']);
        $dFechaFinal    = date_create($vResDat['resfhaxx']);
        $nDiferencia    = date_diff($dFechaInicial, $dFechaFinal);
        $nMesesVigencia = ( $nDiferencia->y * 12 ) + $nDiferencia->m;

        $this->setXY($posx+75, $posy+5);
        $this->SetFont('Arial','B',7);
        $this->Cell(50,4,utf8_decode('AGENCIA DE ADUANAS DHL EXPRESSCOLOMBIA LTDA NIVEL 1'),0,0,'C');
        $this->Ln(4);
        $this->SetFont('Arial','',7);
        $this->setX($posx+75);
        $this->Cell(50,4,utf8_decode("Resolución DIAN No. ").$vResDat['residxxx'],0,0,'C');
        $this->Ln(4);
        $this->setX($posx+75);
        $this->Cell(50,4,utf8_decode("Del ". $vResDat['resfdexx'] ." DEL No. ". $vResDat['resprexx'].$vResDat['resdesxx'] ." AL No. ". $vResDat['resprexx'].$vResDat['reshasxx']), 0, 0, 'C');
        $this->Ln(4);
        $this->setX($posx+75);
        $this->Cell(50,4,utf8_decode("Vigencia ". $nMesesVigencia ." meses"), 0, 0, 'C');
        $this->Ln(4);
        $this->setX($posx+75);
        $this->SetFont('Arial','B',7);
        $this->Cell(50,4,utf8_decode("FACTURACIÓN ELECTRÓNICA"), 0, 0, 'C');
  
        $this->Ln(5);
        $this->setX($posx+75);
        $this->SetFont('Arial','B',7);
        $this->Cell(50,4,utf8_decode("REGIMEN COMÚN"), 0, 0, 'C');
        $this->Ln(4);
        $this->setX($posx+75);
        $this->Cell(50,4,utf8_decode("AUTORRETENEDORES SEGÚN RESOLUCIÓN"), 0, 0, 'C');
        $this->Ln(4);
        $this->setX($posx+75);
        $this->Cell(50,4,utf8_decode("No. 3654 DEL 28 DE ABRIL DEL 2008"), 0, 0, 'C');

        //Tabla de Datos Factura
        //Rectangulo Rojo
        $this->SetFillColor(128, 0, 0);
        $this->RoundedRect($posx+143, $posy+2, 50, 5, 1, '1234','F');
        $this->setXY($posx+143, $posy+3);
        $this->SetFont('Arial','B',7);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(50,4,utf8_decode("FACTURA ELECTRÓNICA DE VENTA No."),0,0,'C');

        //Rectangulo Gris
        //Numero de Documento
        $this->SetFillColor(206, 210, 225);
        $this->RoundedRect($posx+143, $posy+7, 50, 5, 1, '34','F');
        $this->setXY($posx+143,$posy+8);
        $this->SetFont('Arial','B',8);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(50,4,"No. ".$vResDat['resprexx']."-".$vCocDat['comcscxx'],0,0,'C');

        //Rectangulo Rojo FECHA
        $this->SetFillColor(128, 0, 0);
        $this->RoundedRect($posx+143, $posy+12, 50, 4, 1, '1234','F');
        $this->setXY($posx+143,$posy+12);
        $this->SetFont('Arial','B',7);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(50,4,"FECHA DOCUMENTO",0,0,'C');
        $this->SetTextColor(0, 0, 0);

        //Rectangulo Gris FECHA
        $this->SetFillColor(206, 210, 225);
        $this->RoundedRect($posx+143,$posy+16, 50, 9, 1, '34','F');

        //Separadores color Gris de los valores de la Fecha del Documento
        $this->SetDrawColor(255, 255, 255);
        $this->SetLineWidth(0.4);
        $this->Line($posx+143,$posy+20,$posx+192.8,$posy+20);
        $this->Line($posx+156.5,$posy+16.3,$posx+156.5,$posy+25);
        $this->Line($posx+169,$posy+16.3,$posx+169,$posy+25);
        $this->Line($posx+181.5,$posy+16.3,$posx+181.5,$posy+25);

        $this->setXY($posx+143,$posy+16);
        $this->SetFont('Arial','B',6);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(15,4,utf8_decode("DÍA"),0,0,'C');
        $this->setX($posx+155);
        $this->Cell(15,4,utf8_decode("MES"),0,0,'C');
        $this->setX($posx+168);
        $this->Cell(15,4,utf8_decode("AÑO"),0,0,'C');
        $this->setX($posx+180);
        $this->Cell(15,4,utf8_decode("HORA"),0,0,'C');

        //Valores Fecha de Documento
        $this->setXY($posx+143, $posy+21);
        $this->SetFont('Arial','',8);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(15,4,date('d', strtotime($vCocDat['comfecxx'])),0,0,'C');
        $this->setX($posx+155);
        $this->Cell(15,4,date('m', strtotime($vCocDat['comfecxx'])),0,0,'C');
        $this->setX($posx+168);
        $this->Cell(15,4,date('Y', strtotime($vCocDat['comfecxx'])),0,0,'C');
        $this->setX($posx+180);
        $this->Cell(15,4,date('H:i', strtotime($vCocDat['reghcrex'])),0,0,'C');

        //Rectangulo Rojo VENCIMIENTO
        $this->SetFillColor(128, 0, 0);
        $this->RoundedRect($posx+143, $posy+25, 50, 4, 1, '1234','F');
        $this->setXY($posx+143, $posy+25);
        $this->SetFont('Arial','B',7);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(50,4,"FECHA VENCIMIENTO",0,0,'C');
        $this->SetTextColor(0, 0, 0);

        //Rectangulo Gris VENCIMIENTO
        $this->SetFillColor(206, 210, 225);
        $this->RoundedRect($posx+143, $posy+29, 50, 9.5, 1, '34','F');

        //Separadores color Gris de los valores de la Fecha del Documento
        $this->SetDrawColor(255, 255, 255);
        $this->SetLineWidth(0.4);
        $this->Line($posx+143,$posy+33,$posx+192.8,$posy+33);
        $this->Line($posx+156.5,$posy+29.3,$posx+156.5,$posy+38);
        $this->Line($posx+169,$posy+29.3,$posx+169,$posy+38);
        $this->Line($posx+181.5,$posy+29.3,$posx+181.5,$posy+38);

        $this->setXY($posx+143,$posy+29);
        $this->SetFont('Arial','B',6);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(15,4,utf8_decode("DÍA"),0,0,'C');
        $this->setX($posx+155);
        $this->Cell(15,4,utf8_decode("MES"),0,0,'C');
        $this->setX($posx+168);
        $this->Cell(15,4,utf8_decode("AÑO"),0,0,'C');
        $this->setX($posx+180);
        $this->Cell(15,4,utf8_decode("HORA"),0,0,'C');

        //Valores Fecha de Documento
        $this->setXY($posx+143, $posy+34);
        $this->SetFont('Arial','',8);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(15,4,date('d', strtotime($vCocDat['comfecve'])),0,0,'C');
        $this->setX($posx+155);
        $this->Cell(15,4,date('m', strtotime($vCocDat['comfecve'])),0,0,'C');
        $this->setX($posx+168);
        $this->Cell(15,4,date('Y', strtotime($vCocDat['comfecve'])),0,0,'C');
        $this->setX($posx+180);
        $this->Cell(15,4,'00:00',0,0,'C');

        /*****  Datos Cliente FC *****/
        $posy = $this->GetY()+7;
        $this->SetFillColor(255, 255, 255);
        $this->RoundedRect($posx+4,$posy, 189, 30, 2, '1234','F');

        //Columna 1
        $this->setXY($posx+7,$posy+1);
        $this->SetFont('Arial','B',7);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(20, 4,utf8_decode("SEÑORES:"),0,0,'L');
        $this->SetFont('Arial','',7);
        $this->Cell(150,4,utf8_decode($vCocDat['CLINOMXX']),0,0,'L');
        $this->Ln(4);
        $this->setX($posx+7);
        $this->SetFont('Arial','B',7);
        $this->Cell(20,4,utf8_decode("DIRECCIÓN:"),0,0,'L');
        $this->SetFont('Arial','',7);
        $this->MultiCell(108,3,utf8_decode($vCocDat['CLIDIRXX']),0,'L');
        $this->Ln(1);
        $this->setX($posx+7);
        $this->SetFont('Arial','B',7);
        $this->Cell(20,4,"CIUDAD:",0,0,'L');
        $this->SetFont('Arial','',7);
        $this->Cell(55,4,utf8_decode($vCiuFac['CIUDESXX']),0,0,'L');
        $this->Ln(4);
        $this->setX($posx+7);
        $this->SetFont('Arial','B',7);
        $this->Cell(20,4,"NIT:",0,0,'L');
        $this->SetFont('Arial','',7);
        $this->Cell(45,4,number_format($vCocDat['terid2xx'], 0, '', '.'). "-" .f_Digito_Verificacion($vCocDat['terid2xx']), 0, 0, 'L');
        $this->Ln(4);
        $this->setX($posx+7);
        $this->SetFont('Arial','B',7);
        $this->Cell(20, 4,"TELEFONO:",0,0,'L');
        $this->SetFont('Arial','',7);
        $this->Cell(55,4,$vCocDat['CLITELXX'],0,0,'L');

        //Columna 2
        $posx += 135;
        $this->setXY($posx,$posy+1);
        $this->SetFont('Arial','B',7);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(27,4,"DO:",0,0,'L');
        $this->SetFont('Arial','',7);
        $this->Cell(150,4,$cDocId,0,0,'L');
        $this->Ln(4);
        $this->setX($posx);
        $this->SetFont('Arial','B',7);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(27,4,"GUIA AEREA:",0,0,'L');
        $this->SetFont('Arial','',7);
        $this->Cell(150,4,$cDocTra,0,0,'L');
        $this->Ln(4);
        $this->setX($posx);
        $this->SetFont('Arial','B',7);
        $this->Cell(27,4,"VALOR CIF:",0,0,'L');
        $this->Ln(0.5);
        $this->setX($posx+27);
        $this->SetFont('Arial','',7);
        $this->MultiCell(30,3,number_format($nValAdu, 0, '.', ','),0,'L');
        $this->Ln(1);
        $this->setX($posx);
        $this->SetFont('Arial','b',7);
        $this->Cell(27,4,"TASA DE CAMBIO:",0,0,'L');
        $this->SetFont('Arial','',7);
        $this->Cell(50,4,number_format($cTasCam, 0, '.', ','),0,0,'L');
        $this->Ln(4);
        $this->setX($posx);
        $this->SetFont('Arial','B',7);
        $this->Cell(27,4,"FORMA DE PAGO:",0,0,'L');
        $this->Ln(0.5);
        $this->setX($posx+27);
        $this->SetFont('Arial','',7);
        $this->MultiCell(30,3,$cFormaPago,0,'L');
        $this->Ln(1);
        $this->setX($posx);
        $this->SetFont('Arial','B',7);
        $this->Cell(27,4,"MEDIO DE PAGO:",0,0,'L');
        $this->SetFont('Arial','',7);
        $this->Cell(30,4,$cMedioPago,0,0,'L');

        //Cabecera de los Conceptos
        $posy = $posy+33;
        $posx = 13;
        $this->SetFillColor(128, 0, 0);
        $this->RoundedRect($posx,$posy, 189, 5, 1, '1234', 'F');

        $this->setXY($posx,$posy+1);
        $this->SetFont('Arial','B',7);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(9,4,"ITEM",0,0,'L');
        $this->Cell(25,4,"COD. PRODUCTO",0,0,'L');
        $this->Cell(78,4,utf8_decode("DESCRIPCIÓN"),0,0,'C');
        $this->Cell(15,4,"UNIDAD",0,0,'L');
        $this->Cell(12,4,"CANTIDAD",0,0,'C');
        $this->Cell(23,4,"VR. UNITARIO",0,0,'R');
        $this->Cell(25,4,"VALOR",0,0,'R');

        $this->nPosYIni = $posy+7;

        //Rectangulo que contiene los conceptos
        $posy = $this->GetY()+4;
        $this->SetFillColor(255, 255, 255);
        $this->Rect($posx,$posy,189,150,'F');
        $this->SetDrawColor(0,0,0);

        if ($vCocDat['regestxx'] == "INACTIVO") {
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/facturaanulada.jpg',33,85,145,145);
        }
  
        if ($_COOKIE['kModo'] == "VERFACTURA"){
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/copianovalida.jpg',33,85,145,145);
        }

        $this->Line($posx+165,$posy,$posx+165,$posy+121,'F');
        $this->Line($posx,$posy+90,$posx+189,$posy+90);
  		}//Function Header

			function Footer(){
        global $vCocDat; global $dir; global $vSysStr; global $vUsrNom; global $cPlesk_Skin_Directory; global $_COOKIE;
        global $_SERVER; global $vTexCli;

        $posy = 175;
        $posx = 13;

        $this->SetLineWidth(0.4);
        $this->Line($posx+123,$posy-1,$posx+123,$posy+30);
        $this->setXY($posx+1,$posy); 
        $this->SetFont('Arial','B',7);
        $this->Cell(25,4,"OBSERVACIONES: ",0,0,'L');
        $this->SetFont('Arial','',8);
        $this->MultiCell(100,4,"",0,'L');

        $this->setXY($posx+125,$posy); 
        $this->SetFont('Arial','B',7);
        $this->Cell(25,5,"TOTAL",0,0,'L');
        $this->SetFont('Arial','',8);
        $this->Cell(33,5,"",0,0,'R');
        $this->ln(4);
        $this->setX($posx+125); 
        $this->SetFont('Arial','B',7);
        $this->Cell(25,5,"IVA 19 %",0,0,'L');
        $this->SetFont('Arial','',8);
        $this->Cell(33,5,"",0,0,'R');
        $this->ln(4);
        $this->setX($posx+125); 
        $this->SetFont('Arial','B',7);
        $this->Cell(25,5,"RETEIVA",0,0,'L');
        $this->SetFont('Arial','',8);
        $this->Cell(33,5,"",0,0,'R');
        $this->ln(4);
        $this->setX($posx+125); 
        $this->SetFont('Arial','B',7);
        $this->Cell(25,5,"RETEICA",0,0,'L');
        $this->SetFont('Arial','',8);
        $this->Cell(33,5,"",0,0,'R');
        $this->ln(4);
        $this->setX($posx+125); 
        $this->SetFont('Arial','B',7);
        $this->Cell(25,5,"TOTAL",0,0,'L');
        $this->SetFont('Arial','',8);
        $this->Cell(33,5,"",0,0,'R');
        $this->ln(4);
        $this->setX($posx+125); 
        $this->SetFont('Arial','B',7);
        $this->Cell(25,5,"TOTAL A PAGAR",0,0,'L');
        $this->SetFont('Arial','',8);
        $this->Cell(33,5,"",0,0,'R');
        $this->ln(4);
        $this->setX($posx+125); 
        $this->SetFont('Arial','B',7);
        $this->Cell(25,5,"SALDO A SU FAVOR",0,0,'L');
        $this->SetFont('Arial','',8);
        $this->Cell(33,5,"",0,0,'R');

        $this->Line($posx,$posy+30,$posx+189,$posy+30);

        $posy += 30;
        $this->setXY($posx,$posy); 
        $this->SetTextColor(255, 51, 51);
        $this->SetFont('Arial','B',7);
        $this->Cell(25,5, $vTexCli['tfvtitxx'],0,0,'L');
        $this->SetTextColor(0, 0, 0);
        
        $posy += 35;
        $this->setXY($posx+55,$posy);
        $this->SetFont('Arial','B',7);
        $this->Cell(60,3,utf8_decode("REPRESENTACIÓN GRAFICA DE LA FACTURA"),0,0,'C');
        $this->Ln(5);

        if ($vCocDat['compceqr'] != "" && $vCocDat['compcesv'] != "") {
          $this->setX($posx+45);
          $this->SetFont('Arial', '', 7);
          $this->Cell(30,3,utf8_decode("Firma Electrónica:"),0,0,'L');
          $this->Ln(4);
          $this->setX($posx+45);
          $this->SetFont('Arial', '', 6.5);
          $this->MultiCell(80,3,$vCocDat['compcesv'],0,'J');
          $this->Ln(2);
          $this->setX($posx+45);
          $this->SetFont('Arial', 'B', 7);
          $this->Cell(50,3,utf8_decode("FECHA Y HORA DE VALIDACIÓN DIAN:"),0,0,'L');
          $this->SetFont('Arial', '', 7);
          $this->Cell(30,3,substr($vCocDat['compcevd'],0,16),0,0,'L');
        }

        //Contenedor Contáctenos
        $this->SetFillColor(255, 255, 255);            
        $this->Rect($posx+131, $posy, 58, 28, 'DF');
        $this->SetFont('Arial','B',8);
        $this->setXY($posx+133.5,$posy+3);
        $this->Cell(50,3,utf8_decode("¿Tiene dudas sobre este documento?"),0,0,'L');
        $this->Ln(7);
        $this->setX($posx+131);
        $this->SetFont('Arial','B',7);
        $this->Cell(50,3,utf8_decode("Línea Nacional 018000183345 Opc 3"),0,0,'L');
        $this->Ln(4);
        $this->setX($posx+131);
        $this->SetFont('Arial','B',7);
        $this->Cell(50,3,utf8_decode("Línea de atención en Bogotá 6017477777 Opc 3"),0,0,'L');
        $this->Ln(4);
        $this->setX($posx+131);
        $this->SetFont('Arial','B',7);
        $this->Cell(50,3,utf8_decode("Correo Electrónico: DHLcobranzasCO@dhl.com"),0,0,'L');
        $this->Ln(4);
        $this->setX($posx+131);
        $this->SetFont('Arial','U',7);
        $this->Cell(50,3,utf8_decode("https://aduanasdhlexpress.dhl.com"),0,0,'L');
      }

      function Setwidths($w) {
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
          //$this->Rect($x,$y,$w,$h);
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

      function TextWithDirection($x, $y, $txt, $direction='U') {
        if ($direction=='R')
            $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',1,0,0,1,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
        elseif ($direction=='L')
            $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',-1,0,0,-1,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
        elseif ($direction=='U')
            $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',0,1,-1,0,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
        elseif ($direction=='D')
            $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',0,-1,1,0,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
        else
            $s=sprintf('BT %.2F %.2F Td (%s) Tj ET',$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
        if ($this->ColorFlag)
            $s='q '.$this->TextColor.' '.$s.' Q';
        $this->_out($s);
      }

      function RoundedRect($x, $y, $w, $h, $r, $corners = '1234', $style = '') {
        $k = $this->k;
        $hp = $this->h;
        if($style=='F')
            $op='f';
        elseif($style=='FD' || $style=='DF')
            $op='B';
        else
            $op='S';
        $MyArc = 4/3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));
  
        $xc = $x+$w-$r;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));
        if (strpos($corners, '2')===false)
            $this->_out(sprintf('%.2F %.2F l', ($x+$w)*$k,($hp-$y)*$k ));
        else
            $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
  
        $xc = $x+$w-$r;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
        if (strpos($corners, '3')===false)
            $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-($y+$h))*$k));
        else
            $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
  
        $xc = $x+$r;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
        if (strpos($corners, '4')===false)
            $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-($y+$h))*$k));
        else
            $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
  
        $xc = $x+$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
        if (strpos($corners, '1')===false)
        {
            $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$y)*$k ));
            $this->_out(sprintf('%.2F %.2F l',($x+$r)*$k,($hp-$y)*$k ));
        }
        else
            $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
      }
  
      function _Arc($x1, $y1, $x2, $y2, $x3, $y3) {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
          $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
      }
    }
    
    $pdf=new PDF('P','mm','Letter');
		$pdf->AddFont('verdana','','verdana.php');
		$pdf->AddFont('verdanab','','verdanab.php');
		$pdf->SetFont('verdana','',8);
		$pdf->SetMargins(0,0,0);
    $pdf->SetAutoPageBreak(0,0);

    $pdf->AddPage();
    $posy	     = $pdf->nPosYIni;
    $posx	     = 13;
    $posfin    = 160;
    $nCounItem = 0;
    $nTotalPcc = 0;
    $nTotalIP  = 0;

    // $mDatIP = array_merge($mDatIP, $mDatIP, $mDatIP, $mDatIP, $mDatIP);
    // $mDatIP = array_merge($mDatIP, $mDatIP, $mDatIP, $mDatIP, $mDatIP);
    // $mDatIP = array_merge($mDatIP, $mDatIP, $mDatIP, $mDatIP, $mDatIP);

    ## Imprimo los pagos a terceros ##
    if(count($mIngTer) > 0){
      $pdf->setXY($posx+8,$posy);
      $pdf->SetFont('arial','B',7);
      $pdf->Cell(140,5,"PAGOS A TERCEROS",0,0,'L');
      $pdf->SetFont('arial','',7);
      $posy+=5;

      $pdf->SetWidths(array(8,23,80,15,12,25,25));
      $pdf->SetAligns(array("C","C","L","C","C","R","R"));
      $pdf->setXY($posx,$posy);

      for($i=0;$i<count($mIngTer);$i++){
        if($posy > $posfin){
          $pdf->AddPage();
          $posx	= 13;
          $posy = $pdf->nPosYIni;
          $pdf->setXY($posx,$posy);
        }

        //Consulto la descripcion de la Unidad de medida
        $qUniMedi  = "SELECT umedesxx ";
        $qUniMedi .= "FROM $cAlfa.fpar0157 ";
        $qUniMedi .= "WHERE ";
        $qUniMedi .= "umeidxxx = \"{$mIngTer[$i][101]}\" LIMIT 0,1";
        $xUniMedi  = mysql_query($qUniMedi, $xConexion01);
        //f_Mensaje(__FILE__,__LINE__,$qUniMedi." ~ ".mysql_num_rows($xUniMedi));
        while ($xRUM = mysql_fetch_assoc($xUniMedi)) {
          $cUniMedi = $xRUM['umedesxx'];
        }

        $cComObs     = explode("^",$mIngTer[$i][2]);
        $cComObs_PCC = str_replace("CANTIDAD", "CANT", $cComObs[0]);

        $nTotalPcc += $mIngTer[$i][7];
        $nCounItem++;

        $pdf->SetFont('Arial','',7);
        $pdf->setX($posx);
        $pdf->Row(array(
          $nCounItem,
          $mIngTer[$i][1], 
          utf8_decode($cComObs_PCC),
          $cUniMedi,
          "1",
          number_format($mIngTer[$i][7],0,',','.'),
          number_format($mIngTer[$i][7],0,',','.'))
        );
        $posy += 4;
      }//for($i=0;$i<count($mIngTer);$i++){
      
      if($posy > $posfin){
        $pdf->AddPage();
        $posx	= 13;
        $posy = $pdf->nPosYIni;
        $pdf->setXY($posx,$posy);
      }

      for ($i=0;$i<count($mDatPCC);$i++) {
        if($posy > $posfin){
          $pdf->AddPage();
          $posx	= 13;
          $posy = $pdf->nPosYIni;
          $pdf->setXY($posx,$posy);
        }

        if($mDatPCC[$i]['comctocx'] == 'PCC'){
          $cCan = 1;
          if ($mDatPCC[$i]['comcanap'] == "SI"){
            $cCan = "[CANT: ".number_format($mDatPCC[$i]['comcanxx'],0,'.',',')."]";
          }

          $nTotalPcc += $mDatPCC[$i]['comvlrxx'];
          $nCounItem++;

          $pdf->SetFont('Arial','',7);
          $pdf->setX($posx);
          $pdf->Row(array(
            $nCounItem,
            $mDatPCC[$i]['ctoidxxx'], 
            str_replace("CANTIDAD","CANT",$mDatPCC[$i]['comobsxx']),
            "TARIFA",
            $cCan,
            number_format($mDatPCC[$i]['comvlrxx'],0,',','.'),
            number_format($mDatPCC[$i]['comvlrxx'],0,',','.'))
          );
        }//if($mDatPCC[$i]['comctocx'] == 'PCC'){
        $posy += 4;
      }//for ($i=0;$i<count($mDatPCC);$i++) {
      $posy += 6;
    }//if(count($mIngTer) > 0){
    ## Fin Imprimo los pagos a terceros ##

    if($posy > $posfin){
      $pdf->AddPage();
      $posx	= 13;
      $posy = $pdf->nPosYIni;
      $pdf->setXY($posx,$posy);
    }

    ##Imprimo Ingresos Propios##
    if(count($mDatIP) > 0){
      $pdf->setXY($posx+8,$posy);
      $pdf->SetFont('Arial','B',7);
      $pdf->Cell(40,5,utf8_decode("INGRESOS PROPIOS"),0,0,'L');
      $pdf->SetFont('arial','',7);
      $posy+=5;

      $pdf->SetWidths(array(8,23,80,15,12,25,25));
      $pdf->SetAligns(array("C","C","L","C","C","R","R"));
      $pdf->setXY($posx,$posy);

      for ($k=0;$k<count($mDatIP);$k++) {
        if($posy > $posfin){
          $pdf->AddPage();
          $posx	= 13;
          $posy = $pdf->nPosYIni;
          $pdf->setXY($posx,$posy);
        }

        if($mDatIP[$k]['comctocx'] == 'IP'){
          //Consulto la descripcion de la Unidad de medida
          $qUniMedi  = "SELECT umedesxx ";
          $qUniMedi .= "FROM $cAlfa.fpar0157 ";
          $qUniMedi .= "WHERE ";
          $qUniMedi .= "umeidxxx = \"{$mDatIP[$k]['unidadfe']}\" LIMIT 0,1";
          $xUniMedi  = mysql_query($qUniMedi, $xConexion01);
          //f_Mensaje(__FILE__,__LINE__,$qUniMedi." ~ ".mysql_num_rows($xUniMedi));
          while ($xRUM = mysql_fetch_assoc($xUniMedi)) {
            $cUniMedi = $xRUM['umedesxx'];
          }

          $nTotalIP += $mDatIP[$k]['comvlrxx'];
          $nValorUni = ($mDatIP[$k]['unidadfe'] != "A9" && $mDatIP[$k]['canfexxx'] > 0) ? $mDatIP[$k]['comvlrxx']/$mDatIP[$k]['canfexxx'] : $mDatIP[$k]['comvlrxx'];
          $nCounItem++;

          $pdf->SetFont('Arial','',7);
          $pdf->setX($posx);
          $pdf->Row(array(
            $nCounItem,
            $mDatIP[$k]['ctoidxxx'],
            utf8_decode(trim($mDatIP[$k]['comobsxx'])),
            $cUniMedi,
            number_format($mDatIP[$k]['canfexxx'],0,'.',','),
            number_format($nValorUni,0,',','.'),
            number_format($mDatIP[$k]['comvlrxx'],0,',','.'))
          );
        }//if($mDatIP[$k]['comctocx'] == 'IP'){
        $posy += 4;
      }//for ($k=0;$k<count($mDatIP);$k++) {
    }//if(count($mDatIP) > 0){
    ##Fin Imprimo Ingresos Propios##

    $pdf->SetFont('Arial', '', 7);
    $pdf->setXY($posx,$posy);
    $pdf->Cell(8,4,$nCounItem,'T',0,'C');
  
    if($posy > $posfin){
      $pdf->AddPage();
      $posx	= 13;
      $posy = $pdf->nPosYIni;
      $pdf->setXY($posx,$posy);
    }

    if (count($vDocPed) > 0) {
      $posy += 10;
      $pdf->SetFont('Arial', '', 7);
      $pdf->setXY($posx+10,$posy);
      for ($i=0; $i <count($vDocPed); $i++) {
        if($posy > ($posfin+5)){
          $pdf->AddPage();
          $posx	= 13;
          $posy = $pdf->nPosYIni;
          $pdf->setXY($posx+10,$posy);
        }
        $pdf->setXY($posx+10,$posy);
        $pdf->Cell(150, 4,utf8_decode($vDocPed[$i]), 0, 0, 'L');
        $posy += 4;
      }
      $posy -= 4;
    }

    if(count($cDosInc) > 1) {
      $posy += 10;
      if($posy > $posfin){
        $pdf->AddPage();
        $posx	= 13;
        $posy = $pdf->nPosYIni;
        $pdf->setXY($posx,$posy);
      }

      $pdf->SetFont('Arial', 'B', 7);
      $pdf->setXY($posx+10,$posy);
      $pdf->Cell(40, 4,"Numeros DO incluidos en esta factura", 0, 0, 'L');
      $posy += 3;
      $pdf->SetFont('Arial', '', 7);
      $pdf->setXY($posx+10,$posy);
      $pdf->MultiCell(155, 4, implode(", ", $cDosInc), 0, 'L');
    }

    if($posy > $posfin){
      $pdf->AddPage();
      $posx	= 13;
      $posy = $pdf->nPosYIni;
      $pdf->setXY($posx,$posy);
    }

    ### Calculo los subtotales ###
    $nSubTotal = $nTotalPcc + $nTotalIP;

    ##Busco valor de IVA ##
    $nIva = 0;
    for ($k=0;$k<count($mCodDat);$k++) {
      if($mCodDat[$k]['comctocx'] == 'IVAIP'){
        $nIva += $mCodDat[$k][$cCamVlr];
      }
    }
    ##Fin Busco Valor de IVA ##

    ##Busco Valor de RET.IVA ##
    $nTotRteIva = 0;
    for ($k=0;$k<count($mCodDat);$k++) {
      if($mCodDat[$k]['comctocx'] == 'RETIVA'){
        $nTotRteIva += $mCodDat[$k][$cCamVlr];
      }
    }
    ##Fin Busco Valor de RET.IVA ##

    ##Busco Valor de RET.ICA ##
    $nTotRteIca = 0;
    for ($k=0;$k<count($mCodDat);$k++) {
      if($mCodDat[$k]['comctocx'] == 'RETICA'){
        $nTotRteIca += $mCodDat[$k][$cCamVlr];
      }
    }
    ##Fin Busco Valor de RET.ICA ##

    $nTotalPagar = 0;
    $nSaldoFavor = 0;
    $nTotAnt     = 0;
    for($k=0;$k<count($mCodDat);$k++){
      if($mCodDat[$k]['comctocx'] == 'CD' && strpos($mCodDat[$k]['comobsxx'],'ANTICIPOS') > 0) {
        $nTotAnt += $mCodDat[$k]['comvlrxx'];
      }

      if($mCodDat[$k]['comctocx'] == 'SS' || $mCodDat[$k]['comctocx'] == 'SC'){
        if($mCodDat[$k]['comctocx'] == "SC"){
          $nSaldoFavor += abs($mCodDat[$k][$cCamVlr]);
        } else {
          $nTotalPagar += $mCodDat[$k][$cCamVlr];
        }
      }
    }

    // Nota para las observaciones
    $vCocDat['comobsxx'] .= utf8_decode(" La firma de terceros en representación del cliente, implica la aceptación de este documento. Código de actividad económica CIIU 5229. GEN - BASE PARA CALCULAR IVA Y RETEICA SOBRE EL VALOR DE INGRESOS PROPIOS: "). number_format($nTotalIP, 0, '.', ',');

    // Imprimo los valores Totales
    $posx	= 13;
    $posy = 175;

    $nTotalFactura = ($nSubTotal + $nIva) - ($nTotRteIva + $nTotRteIca);

    $pdf->SetFont('Arial', '', 7);
    $pdf->setXY($posx+163, $posy);
    $pdf->Cell(25,4, number_format($nSubTotal, 0, '.', ','), 0, 0, 'R');
    $pdf->ln(4);
    $pdf->setX($posx+163);
    $pdf->Cell(25,4, number_format($nIva, 0, '.', ','), 0, 0, 'R');
    $pdf->ln(4);
    $pdf->setX($posx+163);
    $pdf->Cell(25,4, number_format($nTotRteIva, 0, '.', ','), 0, 0, 'R');
    $pdf->ln(4);
    $pdf->setX($posx+163);
    $pdf->Cell(25,4, number_format($nTotRteIca, 0, '.', ','), 0, 0, 'R');
    $pdf->ln(4);
    $pdf->setX($posx+163);
    $pdf->Cell(25,4, number_format($nTotalFactura, 0, '.', ','), 0, 0, 'R');
    $pdf->ln(4);
    $pdf->setX($posx+163);
    $pdf->Cell(25,4, number_format($nTotalPagar, 0, '.', ','), 0, 0, 'R');
    $pdf->ln(4);
    $pdf->setX($posx+163);
    $pdf->Cell(25,4, number_format($nSaldoFavor, 0, '.', ','), 0, 0, 'R');

    // Observaciones
    $pdf->setXY($posx+1,$posy+5);
    $pdf->SetFont('Arial', '', 8);
    $pdf->MultiCell(120,4, $vCocDat['comobsxx'],0,'L');
    $pdf->SetFont('Arial', '', 7);
    if($nTotAnt > 0) {
      $pdf->ln(1);
      $pdf->setX($posx+1);
      $pdf->SetFont('Arial', 'B', 7);
      $pdf->Cell(25,3, "ANTICIPOS:",0,'L');
      $pdf->SetFont('Arial', '', 7);
      $pdf->Cell(25,3, number_format($nTotAnt, 0, '.', ','),0,'L');
    }

    if($cDgeTrm && $cDgeTrm != '') {
      $pdf->ln(3);
      $pdf->setX($posx+1);
      $pdf->SetFont('Arial', 'B', 7);
      $pdf->Cell(25,3, "TASA DE CAMBIO:",0,'L');
      $pdf->SetFont('Arial', '', 7);
      $pdf->Cell(25,3, number_format($cDgeTrm, 0, '.', ','),0,'L');
    }

    // Texto estimado cliente
    $posx	= 13;
    $posy = 210;
    $pdf->SetFont('Arial', '', 8);
    $pdf->setXY($posx, $posy);
    $cContenido = str_replace(array("// ", " //", " // "), "\n", $vTexCli['tfvcontx']);
    $pdf->MultiCell(187,4,$cContenido,0,'J');
		$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
		$pdf->Output($cFile);

		if (file_exists($cFile)){
			chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
		} else {
			f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
		}

		echo "<html><script>document.location='$cFile';</script></html>";
  }

  /**
   *
   * @param $dataURI
   * @return array|bool
   */
  function getImage($dataURI){
    $img = explode(',',$dataURI,2);
    $pic = 'data://text/plain;base64,'.$img[1];
    $type = explode("/", explode(':', substr($dataURI, 0, strpos($dataURI, ';')))[1])[1]; // get the image type
    if ($type=="png"||$type=="jpeg"||$type=="gif") return array($pic, $type);
    return false;
  }

  /**
   * Metodo para dar formato a la fecha, ej: 2016-12-31 -> 31 de diciembre del 2017
   * @param Fecha $xFecha
   */
  function f_Fecha_Letras($xFecha){
    if ($xFecha==''){
      $xFecfor='';
    }else{
      $fano = substr ($xFecha, 0, 4);
      $fdia = substr ($xFecha, 8, 2);
      $fmes_antes = substr ($xFecha, 5, 2);
      if($fmes_antes=='01')
      $fmes="ENE";
      if($fmes_antes=='02')
      $fmes="FEB";
      if($fmes_antes=='03')
      $fmes="MAR";
      if($fmes_antes=='04')
      $fmes="ABR";
      if($fmes_antes=='05')
      $fmes="MAY";
      if($fmes_antes=='06')
      $fmes="JUN";
      if($fmes_antes=='07')
      $fmes="JUL";
      if($fmes_antes=='08')
      $fmes="AGO";
      if($fmes_antes=='09')
      $fmes="SEP";
      if($fmes_antes=='10')
      $fmes="OCT";
      if($fmes_antes=='11')
      $fmes="NOV";
      if($fmes_antes=='12')
      $fmes="DIC";
      $xFecFor= $fmes." ".$fdia."/".$fano;
    }

    return ($xFecFor);
  }

?>

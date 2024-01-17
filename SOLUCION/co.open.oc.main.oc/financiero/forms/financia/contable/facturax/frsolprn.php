<?php
  /**
	 * Imprime Factura de Venta SOLUCIONES ADUANERAS.
	 * --- Descripcion: Permite Imprimir Factura de Venta SOLUCIONES ADUANERAS.
	 * @author Juan Jose Trujillo <juan.trujillo@open-eb.co>
	 * @version 001
	 */
  
  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");
  // date_default_timezone_set('America/Bogota');
  
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
				$vCocDat  = mysql_fetch_array($xCocDat);
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
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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
  $qCocDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,\"CLIENTE SIN NOMBRE\") AS CLINOMXX, ";
  $qCocDat .= "$cAlfa.A.CLINRPXX, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.CLIDIRXX != \"\",$cAlfa.SIAI0150.CLIDIRXX,\"SIN DIRECCION\") AS CLIDIRXX, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.CLITELXX != \"\",$cAlfa.SIAI0150.CLITELXX,\"SIN TELEFONO\") AS CLITELXX, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.CLICONTX != \"\",$cAlfa.SIAI0150.CLICONTX,\"SIN RESPONSABLE\") AS CLICONTX, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.PAIIDXXX != \"\",$cAlfa.SIAI0150.PAIIDXXX,\"\") AS PAIIDXXX, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.DEPIDXXX != \"\",$cAlfa.SIAI0150.DEPIDXXX,\"\") AS DEPIDXXX, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.CIUIDXXX != \"\",$cAlfa.SIAI0150.CIUIDXXX,\"\") AS CIUIDXXX, ";
  $qCocDat .= "IF($cAlfa.A.CLINOMXX != \"\",$cAlfa.A.CLINOMXX,\" SIN NOMBRE\") AS CLIINTXX, ";
  $qCocDat .= "IF($cAlfa.A.CLIDIRXX != \"\",$cAlfa.A.CLIDIRXX,\" SIN DIRECCION\") AS CLIDIRIX, ";
  $qCocDat .= "IF($cAlfa.A.CLITELXX != \"\",$cAlfa.A.CLITELXX,\" SIN TELEFONO\") AS CLITELIX, ";
  $qCocDat .= "IF($cAlfa.A.PAIIDXXX != \"\",$cAlfa.A.PAIIDXXX,\"\") AS PAIIDIXX, ";
  $qCocDat .= "IF($cAlfa.A.DEPIDXXX != \"\",$cAlfa.A.DEPIDXXX,\"\") AS DEPIDIXX, ";
  $qCocDat .= "IF($cAlfa.A.CIUIDXXX != \"\",$cAlfa.A.CIUIDXXX,\"\") AS CIUIDIXX ";
  $qCocDat .= "FROM $cAlfa.fcoc$cAno ";
  $qCocDat .= "LEFT JOIN $cAlfa.fpar0008 ON $cAlfa.fcoc$cAno.ccoidxxx = $cAlfa.fpar0008.ccoidxxx ";
  $qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcoc$cAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
  $qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 AS A ON $cAlfa.fcoc$cAno.terid2xx = $cAlfa.A.CLIIDXXX ";
  $qCocDat .= "WHERE $cAlfa.fcoc$cAno.comidxxx = \"$cComId\" AND ";
  $qCocDat .= "$cAlfa.fcoc$cAno.comcodxx = \"$cComCod\" AND ";
  $qCocDat .= "$cAlfa.fcoc$cAno.comcscxx = \"$cComCsc\" AND ";
  $qCocDat .= "$cAlfa.fcoc$cAno.comcsc2x = \"$cComCsc2\" LIMIT 0,1";

  $xCocDat  = f_MySql("SELECT","",$qCocDat,$xConexion01,"");
  $nFilCoc  = mysql_num_rows($xCocDat);
  if ($nFilCoc > 0) {
    $vCocDat  = mysql_fetch_array($xCocDat);
    //$result = array_merge($vCocDat,$vCocDat);
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

  ////// DETALLE 1002 /////
  $qCodDat  = "SELECT DISTINCT ";
  $qCodDat .= "$cAlfa.fcod$cAno.*, ";
  $qCodDat .= "$cAlfa.sys00121.docmtrxx AS docmtrxx ";
  $qCodDat .= "FROM $cAlfa.fcod$cAno ";
  $qCodDat .= "LEFT JOIN $cAlfa.sys00121 ON $cAlfa.fcod$cAno.comcsccx = $cAlfa.sys00121.docidxxx AND $cAlfa.fcod$cAno.comseqcx = $cAlfa.sys00121.docsufxx  ";
  $qCodDat .= "WHERE $cAlfa.fcod$cAno.comidxxx = \"$cComId\" AND ";
  $qCodDat .= "$cAlfa.fcod$cAno.comcodxx = \"$cComCod\" AND ";
  $qCodDat .= "$cAlfa.fcod$cAno.comcscxx = \"$cComCsc\" AND ";
  $qCodDat .= "$cAlfa.fcod$cAno.comcsc2x = \"$cComCsc2\" ORDER BY ABS($cAlfa.fcod$cAno.comseqxx) ASC ";
  $xCodDat  = f_MySql("SELECT","",$qCodDat,$xConexion01,"");
  $nFilCod  = mysql_num_rows($xCodDat);
  if($nFilCod > 0) {
    // Cargo la Matriz con los ROWS del Cursor
    $iA=0;
    while ($xRCD = mysql_fetch_array($xCodDat)) {
      $mCodDat[$iA] = $xRCD;
      $iA++;
    }
  }

  $cDocId  = "";
  $cDocSuf = "";
  $mDoiId = explode("|",$vCocDat['comfpxxx']);
  for ($i=0;$i<count($mDoiId);$i++) {
    if($mDoiId[$i] != ""){
      $vDoiId  = explode("~",$mDoiId[$i]);
      $cDocId  = $vDoiId[2];
      $cDocSuf = $vDoiId[3];
      $cSucId  = $vDoiId[15];
      $i = count($mDoiId);
    }
  }

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
  ###Fin Trayendo Datos de Do Dependiendo del Tipo de Operacion ##

  $qSucDat  = "SELECT ";
  $qSucDat .= "sucdesxx ";
  $qSucDat .= "FROM $cAlfa.fpar0008 ";
  $qSucDat .= "WHERE ";
  $qSucDat .= "sucidxxx = \"{$vDceDat['sucidxxx']}\" AND ";
  $qSucDat .= "regestxx = \"ACTIVO\" ";
  $xSucDat  = f_MySql("SELECT","",$qSucDat,$xConexion01,"");
  $nFilSuc  = mysql_num_rows($xSucDat);
  if ($nFilSuc > 0) {
    $vSucDat = mysql_fetch_array($xSucDat);
  }

  $qUsrDat  = "SELECT ";
  $qUsrDat .= "IF(USRNOMXX != \"\",USRNOMXX,\"USUARIO SIN NOMBRE\") AS USRNOMXX ";
  $qUsrDat .= "FROM $cAlfa.SIAI0003 ";
  $qUsrDat .= "WHERE ";
  $qUsrDat .= "USRIDXXX = \"{$vCocDat['regusrxx']}\" LIMIT 0,1 ";
  $xUsrDat  = f_MySql("SELECT","",$qUsrDat,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qUsrDat."~".mysql_num_rows($xUsrDat));
  $nFilUsr  = mysql_num_rows($xUsrDat);
  if ($nFilUsr > 0) {
    $vUsrDat = mysql_fetch_array($xUsrDat);
  }
  
  $qResDat  = "SELECT * ";
  $qResDat .= "FROM $cAlfa.fpar0138 ";
  $qResDat .= "WHERE ";
  $qResDat .= "rescomxx LIKE \"%{$cComId}~{$cComCod}%\" AND ";
  $qResDat .= "regestxx = \"ACTIVO\" LIMIT 0,1";
  $xResDat  = f_MySql("SELECT","",$qResDat,$xConexion01,"");
  $nFilRes  = mysql_num_rows($xResDat);
  if ($nFilRes > 0) {
    $vResDat = mysql_fetch_array($xResDat);
  }

  ##Agrupo los Pagos a Terceros por Concepto##
  $mCto = array();
  $mDec = f_Explode_Array($vCocDat['commemod'],"|","~");

  ##Traigo los Documentos que estan marcados como PAGOIMPUESTOS##
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
  ##Fin Traigo los Documentos que estan marcados como PAGOIMPUESTOS##
  
  for ($i=0;$i<count($mDec);$i++) {
    if ($mDec[$i][1] != "") {
      $mComObs_PCC = stripos($mDec[$i][2],"[");
  
      $nSwitch_Encontre_Concepto = 0;
      if (in_array("{$mDec[$i][1]}~{$mDec[$i][9]}", $vComImp) == false) {
        for ($j=0;$j<count($mCto);$j++) {
          if ($mCto[$j][1] == $mDec[$i][1]) {
            $nSwitch_Encontre_Concepto = 1;
            $mCto[$j][7] += $mDec[$i][7]; // Acumulo el valor de ingreso para tercero.
            $mCto[$j][15] += $mDec[$i][15]; // Acumulo base de iva.
            $mCto[$j][16] += $mDec[$i][16]; // Acumulo valor del iva.
            $mCto[$j][20] += $mDec[$i][20]; // Acumulo el valor de ingreso para tercero en Dolares.

            // Si el PCC es generado desde un Egreso (G) se envia el Doc. Inf. digitado en el comprobante.
            if ($mDec[$i][3] == "G") {
              $mCto[$j][100] = ((strlen($mCto[$j][100]) + strlen($mDec[$i][21]) + 1) < 50) ? $mCto[$j][100]."/".$mDec[$i][21] : $mCto[$j][100];
              $mCto[$j][100] = (substr($mCto[$j][100],0,1) == "/") ? substr($mCto[$j][100],1,strlen($mCto[$j][100])) : $mCto[$j][100];
            } else {
              $mCto[$j][100] = ((strlen($mCto[$j][100]) + strlen($mDec[$i][5]) + 1) < 50) ? $mCto[$j][100]."/".$mDec[$i][5] : $mCto[$j][100];
              $mCto[$j][100] = (substr($mCto[$j][100],0,1) == "/") ? substr($mCto[$j][100],1,strlen($mCto[$j][100])) : $mCto[$j][100];
            }
            $j = count($mCto); // Me salgo del FOR cuando encuentro el concepto.
          }
        }
      }

      if ($nSwitch_Encontre_Concepto == 0) { // No encontre el ingreso para tercero en la matrix $mCto
        $nInd_mCto = count($mCto);
        $mCto[$nInd_mCto] = $mDec[$i]; // Ingreso el registro como nuevo.

        // Si el PCC es generado desde un Egreso (G) se envia el Doc. Inf. digitado en el comprobante.
        if ($mDec[$i][3] == "G") {
          $mCto[$nInd_mCto][100] = ((strlen($mCto[$nInd_mCto][100]) + strlen($mDec[$i][21]) + 1) < 50) ? $mCto[$nInd_mCto][100]."/".$mDec[$i][21] : $mCto[$nInd_mCto][100];
          $mCto[$nInd_mCto][100] = (substr($mCto[$nInd_mCto][100],0,1) == "/") ? substr($mCto[$nInd_mCto][100],1,strlen($mCto[$nInd_mCto][100])) : $mCto[$nInd_mCto][100];
        } else {
          $mCto[$nInd_mCto][100] = ((strlen($mCto[$nInd_mCto][100]) + strlen($mDec[$i][5]) + 1) < 50) ? $mCto[$nInd_mCto][100]."/".$mDec[$i][5] : $mCto[$nInd_mCto][100];
          $mCto[$nInd_mCto][100] = (substr($mCto[$nInd_mCto][100],0,1) == "/") ? substr($mCto[$nInd_mCto][100],1,strlen($mCto[$nInd_mCto][100])) : $mCto[$nInd_mCto][100];
        }
      }
    }
  }
  ##FIN Agrupo los Pagos a Terceros por Concepto##

  /*****Agrupo los Ingresos Propios por Concepto de Facturacion *****/
  $mMatriz = array();
  $mDatIP  = array();
  $mPCC    = array();
  for($i=0;$i<count($mCodDat);$i++){
    if($mCodDat[$i]['comctocx'] == "PCC") {
      $cObs    = "";
      $cCan    = "";
      $nCan    = 0;
      $cAplCan = "";
      $nComObs_PCC = stripos($mCodDat[$i]['comobsxx'], "[");
      if($nComObs_PCC > 0){
        $mAuxCan = explode("CANTIDAD:",substr($mCodDat[$i]['comobsxx'],$nComObs_PCC,strlen($mCodDat[$i]['comobsxx'])));
        $cCan = "";
        if(count($mAuxCan) > 1) {
          $cCan    = str_replace(array(",","]"," "), "", $mAuxCan[1]);
          $nCan    = $cCan;
          $cAplCan = "SI";
        }
        $cObs = substr(substr($mCodDat[$i]['comobsxx'],0,$nComObs_PCC),0,70);
      }else{
        $cObs = substr($mCodDat[$i]['comobsxx'],0,70);
      }

      $mPCC[$mCodDat[$i]['ctoidxxx']]['comctocx']  = $mCodDat[$i]['comctocx'];
      $mPCC[$mCodDat[$i]['ctoidxxx']]['pucidxxx']  = $mCodDat[$i]['pucidxxx'];
      $mPCC[$mCodDat[$i]['ctoidxxx']]['comvlrxx'] += $mCodDat[$i]['comvlrxx'];
      $mPCC[$mCodDat[$i]['ctoidxxx']]['ctoidxxx']  = $mCodDat[$i]['ctoidxxx'];
      $mPCC[$mCodDat[$i]['ctoidxxx']]['comobsxx']  = ($mPCC[$mCodDat[$i]['ctoidxxx']]['comobsxx'] == "")?$cObs:$mPCC[$mCodDat[$i]['ctoidxxx']]['comobsxx'];
      $mPCC[$mCodDat[$i]['ctoidxxx']]['comcanxx'] += $nCan;
      $mPCC[$mCodDat[$i]['ctoidxxx']]['comcanap']  = ($mPCC[$mCodDat[$i]['ctoidxxx']]['comcanap'] == "SI")?$mPCC[$mCodDat[$i]['ctoidxxx']]['comcanap']:$cAplCan;

    }elseif($mCodDat[$i]['comctocx'] == "IP") {
      $nSwitch_Encontre_Concepto = 0;

      //Agrupando por Concepto
      //Trayendo descripcion concepto, cantidad y unidad
      $mComObs_IP = f_Explode_Array($mCodDat[$i]['comobsxx'],"|","~");

      $vDatosIp = array();
      $vDatosIp = f_Cantidad_Ingreso_Propio($mCodDat[$i]['comobsxx'],'',$mCodDat[$i]['sucidxxx'],$mCodDat[$i]['docidxxx'],$mCodDat[$i]['docsufxx']);

      //Los IP se agrupan por Sevicio
      for($j=0;$j<count($mDatIP);$j++){
        if($mDatIP[$j]['ctoidxxx'] == $mCodDat[$i]['ctoidxxx'] && $mDatIP[$j]['seridxxx'] == $mCodDat[$i]['seridxxx']){
          $nSwitch_Encontre_Concepto = 1;

          $mDatIP[$j]['comvlrxx'] += $mCodDat[$i]['comvlrxx'];
          $mDatIP[$j]['comvlrme'] += $mCodDat[$i]['comvlrme'];
          $mDatIP[$j]['compivax']  = $mCodDat[$i]['compivax']; // Porcentaje IVA
          $mDatIP[$j]['comvlr01'] += $mCodDat[$i]['comvlr01']; // Valor Iva
          
          //Cantidad FE
          $mDatIP[$j]['canfexxx'] += $vDatosIp[1];

          //Cantidad por condicion especial
          for ($nP=0; $nP<count($vDatosIp[3]); $nP++) {
            $mDatIP[$j]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] = $vDatosIp[3][$nP]['valpdfxx'];
          }
        }
      }

      if ($nSwitch_Encontre_Concepto == 0) {
        $nInd_mConData = count($mDatIP);
        $mDatIP[$nInd_mConData] = $mCodDat[$i];
        $mDatIP[$nInd_mConData]['comobsxx'] = $vDatosIp[0];
        $mDatIP[$nInd_mConData]['canfexxx'] = $vDatosIp[1];
        $mDatIP[$nInd_mConData]['unidadfe'] = $vDatosIp[2];

        for ($nP=0; $nP<count($vDatosIp[3]); $nP++) {
          $mDatIP[$nInd_mConData]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] = $vDatosIp[3][$nP]['valpdfxx'];
        }
      }
    }
  }

  foreach ($mPCC as $cKey => $mValores) {
    $mMatriz[] = $mValores;
  }
  /*****Fin Agrupo Ingresos Propios por concepto de Faaturacion *****/
  
  ##Traigo la Forma de Pago##
  $vCodFormPago = explode("~", $vCocDat['comobs2x']);
  $cFormaPag = "";
  if ($vCodFormPago[14] != "") {
    //Buscando descripcion
    $cFormaPag = ($vCodFormPago[14] == 1) ? "CONTADO" : "CREDITO";
  }
  ##FIN Traigo la Forma de Pago##

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
    }
  }
  ##FIN Traigo el Medio de Pago##

  // echo "<pre>";
  // print_r($mCto);
  // die();

  ##Busco los conceptos que son de 4xMil y que en el campo ctoclaxf sean igual a IMPUESTOFINANCIERO para imprimirlo en el bloque de ingresos propios##
  if($nSwitch == 0){
		
    class PDF extends FPDF {

      function Header() {        
        global $cAlfa;   global $cRoot;   global $cPlesk_Skin_Directory;        global $vSysStr; global $_COOKIE;
        global $vCocDat; global $vResDat; global $cDocId;     global $cDocTra;  global $vCiuFac; global $cBultos; 
        global $cPesBru; global $cCodEmb; global $cFormaPag;  global $vMedPag;

        if ($vCocDat['regestxx'] == "INACTIVO") {
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/facturaanulada.jpg',10,50,180,180);
        }
  
        if ($_COOKIE['kModo'] == "VERFACTURA"){
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/copianovalida.jpg',10,50,180,180);
        }

        $posx = 10;
        $posy = 10;
        //logo
        $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logosoluciones.jpg', $posx+1, $posy, 50);

        //Nombre de la agencia
        $this->setXY($posx, $posy);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(183, 3, utf8_decode("AGENCIA DE ADUANAS SOLUCIONES ADUANERAS S.A.S NIVEL 2"), 0, 0, 'C');
        $this->ln(4);
        $this->setX($posx);
        $this->SetFont('Arial', '', 7);
        $cNitAduana  = number_format($vSysStr['financiero_nit_agencia_aduanas'], 0, '', '.')."-";
        $cNitAduana .= f_Digito_Verificacion($vSysStr['financiero_nit_agencia_aduanas']);
        $this->Cell(195, 3, "NIT. " . $cNitAduana, 0, 0, 'C');
        $this->ln(4);
        $this->setX($posx);
        $this->Cell(195, 3, utf8_decode("CL 43 39 39BRR EL ROSARIO"), 0, 0, 'C');
        $this->ln(4);
        $this->setX($posx);
        $this->Cell(195, 3, "3133900", 0, 0, 'C');
        $this->ln(4);
        $this->setX($posx);
        $this->Cell(195, 3, "Barranquilla, Colombia", 0, 0, 'C');

        //Numero de la factura
        $this->setXY($posx + 145, $posy);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(50, 6, utf8_decode("FACTURA DE VENTA ELECTRÓNICA"), 1, 0, 'C');
        $this->ln(6);
        $this->setX($posx + 145);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(50, 9, $vResDat['resprexx']." ".$vCocDat['comcscxx'], 1, 0, 'C');

        //Resolucion DIAN 
        //Traigo numero de Meses entre Desde y Hasta
        $dFechaInicial = date_create($vResDat['resfdexx']);
        $dFechaFinal = date_create($vResDat['resfhaxx']);
        $nDiferencia = date_diff($dFechaInicial, $dFechaFinal);
        $nMesesVigencia = ( $nDiferencia->y * 12 ) + $nDiferencia->m;

        $cResolucion  = "Resolución de Facturación Electrónica No. ".$vResDat['residxxx'];
        $cResolucion .= " del ".substr($vResDat['resfdexx'], 0, 4)."/".substr($vResDat['resfdexx'], 5, 2)."/".substr($vResDat['resfdexx'], 8, 2);
        $cResolucion .= " del ".$vResDat['resprexx'].$vResDat['resdesxx']." al ".$vResDat['resprexx'].$vResDat['reshasxx']." Vigencia: ". $nMesesVigencia ." Meses";

        //Resolucion
        $this->setXY($posx, $posy + 10);
        $this->SetFont('Arial', '', 7);
        $this->TextWithDirection(6, 207, utf8_decode($cResolucion), 'U');

        //Recuadro de la derecha - Datos del adquiriente
        $this->ln(15);
        $posy = $this->GetY();
        $this->setX($posx);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(15, 4, utf8_decode("CLIENTE:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7);
        $this->MultiCell(45, 4, utf8_decode($vCocDat['CLIINTXX']), 0, 'L');
        $this->setX($posx);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(15, 4, utf8_decode("NIT:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7);
        $this->MultiCell(45, 4, number_format($vCocDat['terid2xx'], 0, '', '.'). "-" .f_Digito_Verificacion($vCocDat['terid2xx']), 0, 'L');
        $this->setX($posx);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(15, 4, utf8_decode("DIRECCIÓN:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7);
        $this->MultiCell(45, 4, utf8_decode($vCocDat['CLIDIRIX']), 0, 'L');
        $this->setX($posx);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(15, 4, utf8_decode("TELÉFONO:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7);
        $this->MultiCell(45, 4, $vCocDat['CLITELXX'], 0, 'L');
        $this->setX($posx);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(15, 4, utf8_decode("CIUDAD:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7);
        $this->MultiCell(45, 4, $vCiuFac['CIUDESXX'], 0, 'L');
        $posyy = $this->GetY();

        //Recuadro del centro - Informacion adicional
        $this->setXY($posx + 65, $posy);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(15, 5, utf8_decode("GUIA/BL:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7);
        $this->MultiCell(45, 5, $cDocTra, 0, 'L');
        $this->setX($posx + 65);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(15, 5, utf8_decode("PESO(KG):"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7);
        $this->MultiCell(45, 5, number_format($cPesBru, 2, ',', '.'), 0, 'L');
        $this->setX($posx + 65);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(15, 5, utf8_decode("D.O. No:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7);
        $this->MultiCell(45, 5, $cDocId, 0, 'L');
        $this->setX($posx + 65);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(15, 5, utf8_decode("PIEZAS:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7);
        $this->MultiCell(45, 5, number_format($cBultos, 0), 0, 'L');
        $posyy = $this->GetY() > $posyy ? $this->GetY() : $posyy;

        //Cuadro izquierdo
        $this->setXY($posx + 130, $posy);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(35, 5, utf8_decode("FECHA EMISIÓN:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7);
        $this->MultiCell(30, 5, $vCocDat['comfecxx'], 0, 'R');
        $this->setX($posx + 130);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(35, 5, utf8_decode("HORA GENERACIÓN:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7);
        $this->MultiCell(30, 5, $vCocDat['reghcrex'], 0, 'R');
        $this->setX($posx + 130);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(35, 5, utf8_decode("FECHA DE VENCIMIENTO:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7);
        $this->MultiCell(30, 5, $vCocDat['comfecve'], 0, 'R');
        $this->setX($posx + 130);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(35, 5, utf8_decode("FORMA DE PAGO:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7);
        $this->MultiCell(30, 5, utf8_decode($cFormaPag), 0, 'R');
        $this->setX($posx + 130);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(35, 5, utf8_decode("MEDIO DE PAGO:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7);
        $this->MultiCell(30, 5, $vMedPag['mpadesxx'], 0, 'R');
        $posyy = $this->GetY() > $posyy ? $this->GetY() : $posyy;
        //Pinto los rectangulos
        $this->Rect($posx, $posy, 60, $posyy - $posy);
        $this->Rect($posx + 65, $posy, 60, $posyy - $posy);
        $this->Rect($posx + 130, $posy, 65, $posyy - $posy);
        $this->setXY($posx, $posyy);

        $this->Ln(4);
  		}//Function Header

			function Footer(){
        global $cNomCopia; global $vCocDat; global $nPagina; global $dir; global $vSysStr;

        $posx	= 10;
        $posy = 242;

        ### Firma electronica - CUFE - QR ###
				$this->setXY($posx+143,$posy);
        $this->SetFont('Arial','B',6);
        $this->Cell(30,3, utf8_decode("FECHA Y HORA VALIDACIÒN DIAN: ").substr($vCocDat['compcevd'],0,16),0,0,'L');

        $this->setXY($posx + 180, $posy + 2);
        $this->Cell(15, 5, "CUFE:", 0, 0, 'R');
        $this->ln(4);
        $this->setX($posx+140);
        $this->SetFont('Arial','',6);
        $this->MultiCell(55,2.2,$vCocDat['compcecu'],0,'R');

        if ($vCocDat['compceqr'] != "") {
          $cFileQR = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/QR_".$_COOKIE['kUsrId']."_".date("YmdHis").".png";
          QRcode::png($vCocDat['compceqr'], $cFileQR, "H", 10, 1);
          $this->Image($cFileQR,$posx+155,$posy+12,25,25);
        }
      
        $this->setXY($posx+5,$posy);
        $this->SetFont('Arial','B',7);
				$this->Cell(14,5,utf8_decode("REPRESENTACIÓN IMPRESA DE LA FACTURA ELECTRÓNICA"),0,0,'L');
				$this->Ln(4);
				$this->setX($posx+5);
				$this->Cell(14,5,utf8_decode("Firma Electrónica"),0,0,'L');
				$this->Ln(4);
        $this->SetFont('Arial','',5);
				$this->setX($posx+5);
        $this->MultiCell(150,2, $vCocDat['compcesv'],0,'L');
        
        $posy += 28;
        $this->setXY($posx+80,$posy);
        $this->SetFont('arial','B',8);
        $this->Cell(30,5,$cNomCopia,0,0,'C');
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
    }
    
    $pdf=new PDF('P','mm','Letter');
		$pdf->AddFont('verdana','','verdana.php');
		$pdf->AddFont('verdanab','','verdanab.php');
		$pdf->SetFont('verdana','',8);
		$pdf->SetMargins(0,0,0);
    $pdf->SetAutoPageBreak(0,0);

    for($y=1; $y<=1; $y++){
      $pdf->AddPage();
      $cNomCopia = "";
      $nPagina = 1;

      ##Codigo Para impresion de Copias de Factura ##
			switch($y){
        case 1:
          $cNomCopia = "ORIGINAL";
        break;
        case 2:
          $cNomCopia = "COPIA";
        break;
      }
      ##Codigo Para impresion de Copias de Factura ##

      $posy	    = $pdf->GetY()+3; 
      $posx	    = 10;
      $posfin   = 230;
      $posRect  = $posy-2;
      
      // $mDatIP = array_merge($mDatIP, $mDatIP, $mDatIP, $mDatIP, $mDatIP);
      // $mDatIP = array_merge($mDatIP, $mDatIP, $mDatIP, $mDatIP, $mDatIP);
      // $mDatIP = array_merge($mDatIP, $mDatIP, $mDatIP, $mDatIP, $mDatIP);

      ## Impresion de titulo pagos a terceros
      $pdf->setXY($posx,$posy);
      $pdf->SetFont('arial','B',8);
      $pdf->Cell(140,5, "PAGOS A TERCEROS",0,0,'L');

      ## Impresion de Titulos
      $pdf->setXY($posx,$posy+5);
      $pdf->SetFont('arial','B',7);
      $pdf->Cell(25,4,utf8_decode("CÓDIGO"),0,0,'C');
      $pdf->Cell(70,4,utf8_decode("DESCRIPCIÓN"),0,0,'C');
      $pdf->Cell(20,4,"CANTIDAD",0,0,'C');
      $pdf->MultiCell(30,4,"FACTURA\nGASTOS",0,'C');
      $pdf->setXY($posx+145,$posy+5);
      $pdf->MultiCell(25,4,"VLR\nUNITARIO",0,'C');
      $pdf->setXY($posx+170,$posy+5);
      $pdf->Cell(25,4,"VLR TOTAL",0,0,'C');
      $py = $posy+13;

      $TotPcc = 0;
      if(count($mCto) > 0 || count($mMatriz) > 0){

        //Se imprimen los Ingresos por Terceros
        $pdf->SetWidths(array(25,70,20,30,25,25));
        $pdf->SetAligns(array("C","L","C","C","R","R"));
        $pdf->setXY($posx,$py);

        for ($i=0;$i<count($mCto);$i++) {
          if($py > $posfin){
            $pdf->Rect($posx,$posy,195,($posfin-$posRect));
            $pdf->AddPage();
            $nPagina++; 
            $py = $posy;
            $pdf->setXY($posx,$py);
          }

          $TotPcc += $mCto[$i][7];
          $cComObs_PCCA = explode("^",$mCto[$i][2]);
          // if (substr_count($cComObs_PCCA[0],"DIAN") > 0 ) { // Encontre la palabra DIAN de pago de impuestos.
          //   $cObs = "TRIBUTOS ADUANEROS";
          // }else{
            $cObs = $cComObs_PCCA[0];
          // }
          $pdf->SetFont('arial','',7);
          $pdf->setX($posx);
          $pdf->Row(array("",//trim($mCto[$i][1]),
                          trim($cObs), 
                          "1",
                          $mCto[$i][100],
                          number_format($mCto[$i][7],0,'.',','),
                          number_format($mCto[$i][7],0,'.',',')));
          $py += 4;
        }

        for ($k=0;$k<count($mMatriz);$k++) {
          $cComObs_PCCA = explode("^",$mMatriz[$k]['comobsxx']);
          $TotPcc += $mMatriz[$k]['comvlrxx'];

          $pdf->SetFont('arial','',7);
          $pdf->setX($posx);
          $pdf->Row(array("",//trim($mMatriz[$k]['ctoidxxx']),
                          trim($cComObs_PCCA[0]), 
                          ($mMatriz[$k]['comcanxx'] != 0) ? $mMatriz[$k]['comcanxx'] : "1",
                          "",
                          number_format($mMatriz[$k]['comvlrxx'],0,'.',','),
                          number_format($mMatriz[$k]['comvlrxx'],0,'.',',')));
          $py += 4;
        }

        if($py > $posfin){
          $pdf->Rect($posx,$posy,195,($posfin-$posRect));
          $pdf->AddPage();
          $nPagina++; 
          $py = $posy;
          $pdf->setXY($posx,$py);
        }
      }

      if($py > $posfin){
        $pdf->Rect($posx,$posy,195,($posfin-$posRect));
        $pdf->AddPage();
        $nPagina++; 
        $py = $posy;
        $pdf->setXY($posx,$py);
      }

      $pdf->setXY($posx+120,$py+5);
      $pdf->SetFont('arial','B',8);
      $pdf->Cell(50,3, "TOTAL PAGOS A TERCEROS",0,0,'L');
      $pdf->Cell(25,3, number_format($TotPcc, 0,'.',','),0,0,'R');
      $py += 10;

      $TotalIP = 0;
      if (count($mDatIP) > 0) {
        /*****Imprimir Ingresos Propios agrupados por Concepto *****/
        $pdf->SetFont('arial','B',8);
        $pdf->setXY($posx,$py);
        $pdf->Cell(30,3,"SERVICIOS GRAVADOS CON IVA",0,0,'L');
        $py += 5;

        $pdf->SetWidths(array(25,70,20,30,25,25));
        $pdf->SetAligns(array("C","L","C","C","R","R"));
        $pdf->setXY($posx,$py);

        for ($k=0;$k<count($mDatIP);$k++) {
          if($mDatIP[$k]['comctocx'] == 'IP'){
            if($py > $posfin){
              $pdf->Rect($posx,$posy,195,($posfin-$posRect));
              $pdf->AddPage();
              $nPagina++; 
              $py = $posy;
              $pdf->setXY($posx,$py);
            }

            $cValor = "";
            foreach ($mDatIP[$k]['itemcanx'] as $cKey => $cValue) {
              if ($cKey == "CONTENEDORES_DE_20") {
                $cValor .= " CONTENEDORES DE 20: (".number_format($cValue,0,'.',',').')';
              } elseif ($cKey == "CONTENEDORES_DE_40") {
                $cValor .= " CONTENEDORES DE 40: (".number_format($cValue,0,'.',',').')';
              }elseif ($cKey == "UNIDADES_DE_CARGA_SUELTA") {
                $cValor .= " UNIDADES DE CARGA SUELTA: (".number_format($cValue,0,'.',',').')';
              } else {
                $cValor .= " ".$cKey.": ".$cValue;
              }
            }

            $TotalIP += $mDatIP[$k]['comvlrxx'];
            $nValorUnitario = ($mDatIP[$k]['unidadfe'] != "A9" && $mDatIP[$k]['canfexxx'] > 0) ? ($mDatIP[$k]['comvlrxx']/$mDatIP[$k]['canfexxx']) : $mDatIP[$k]['comvlrxx'];

            $pdf->SetFont('arial','',7);
            $pdf->setX($posx);
            $pdf->Row(array($mDatIP[$k]['ctoidxxx'],
                            $mDatIP[$k]['comobsxx'],
                            $mDatIP[$k]['canfexxx'],
                            $cValor, 
                            number_format($nValorUnitario,0,'.',','),
                            number_format($mDatIP[$k]['comvlrxx'],0,'.',',')));
            $py += 4;
          }
        }
      }

      if($py > $posfin){
        $pdf->Rect($posx,$posy,195,($posfin-$posRect));
        $pdf->AddPage();
        $nPagina++; 
        $py = $posy;
        $pdf->setXY($posx,$py);
      }

      $pdf->setXY($posx+5,$py+5);
      $pdf->SetFont('arial','B',8);
      $pdf->Cell(115,5, "SUBTOTAL",0,0,'L');
      $pdf->Cell(50,5, "TOTAL INGRESOS PROPIOS",0,0,'L');
      $pdf->Cell(25,5, number_format($TotalIP, 0,'.',','),0,0,'R');
      $py += 5;

      if($py > $posfin){
        $pdf->Rect($posx,$posy,195,181-$posRect);
        $pdf->AddPage();
        $nPagina++; 
        $py = $posy;
      }

      $pdf->Rect($posx,$posy,195,181-$posRect);

      ##Bloque que acumula Retefuente por valor de porcentaje ##
      $mRetFte  = array();
      $nTotRfte = 0;
      $nPorRfte = 0;
      for ($k=0;$k<count($mCodDat);$k++) {
        if($mCodDat[$k]['comctocx'] == 'RETFTE'){
          $qPucDat  = "SELECT $cAlfa.fpar0115.pucretxx ";
          $qPucDat .= "FROM $cAlfa.fpar0115 ";
          $qPucDat .= "WHERE ";
          $qPucDat .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = \"{$mCodDat[$k]['pucidxxx']}\" LIMIT 0,1 ";
          $xPucDat  = f_MySql("SELECT","",$qPucDat,$xConexion01,"");
          $nFilPuc  = mysql_num_rows($xPucDat);
          if($nFilPuc > 0){
            //f_Mensaje(__FILE__,__LINE__,$qPucDat);
            while($xRPD = mysql_fetch_array($xPucDat)){
              $nSwitch_Encontre_Porcentaje = 0;
              for ($j=0;$j<count($mRetFte);$j++) {
                if($mRetFte[$j]['pucretxx'] == $xRPD['pucretxx']){
                  $nSwitch_Encontre_Porcentaje = 1;
                  $mRetFte[$j]['comvlrxx'] += $mCodDat[$k]['comvlrxx'];
                  $mRetFte[$j]['basexxxx'] += $mCodDat[$k]['comvlr01'];
                  //Almaceno el valor de la RteFte
                  $nTotRfte += $mCodDat[$k]['comvlrxx'];
                  $nPorRfte += $xRPD['pucretxx'];
                }
              }
              if ($nSwitch_Encontre_Porcentaje == 0) {
                $nInd_mRetFte = count($mRetFte);
                $mRetFte[$nInd_mRetFte]['tipretxx'] = "FUENTE";
                $mRetFte[$nInd_mRetFte]['pucretxx'] = $xRPD['pucretxx'];
                $mRetFte[$nInd_mRetFte]['basexxxx'] = $mCodDat[$k]['comvlr01'];
                $mRetFte[$nInd_mRetFte]['comvlrxx'] = $mCodDat[$k]['comvlrxx'];
                //Almaceno el valor y el porcentaje de RteFte
                $nTotRfte = $mCodDat[$k]['comvlrxx'];
                $nPorRfte = $xRPD['pucretxx'];
              }
            }//while($xRPD = mysql_fetch_array($xPucDat)){
          }//if($nFilPuc > 0){
        }//if($mCodDat[$k]['comctocx'] == 'RETFTE'){        
      }

      ### Calculo los subtotales - anticipos - saldo a favor ###
      $nSubTotal = $TotPcc + $TotalIP;

      $cNegativo = "";
      $nTotAnt = 0;
      for ($k=0;$k<count($mCodDat);$k++) {
        if($mCodDat[$k]['comctocx'] == 'CD' && strpos($mCodDat[$k]['comobsxx'],'ANTICIPOS') > 0){
          $nTotAnt += $mCodDat[$k]['comvlrxx'];
        }
      }

      $nTotPag = 0;
      for ($k=0;$k<count($mCodDat);$k++) {
        if($mCodDat[$k]['comctocx'] == 'SS' || $mCodDat[$k]['comctocx'] == 'SC'){
          if($mCodDat[$k]['comctocx'] == 'SC'){
            $cNegativo = "-";
          }
          $nTotPag += $mCodDat[$k]['comvlrxx'];
        }
      }
      
      $nAnticipoReal = $nTotAnt;
      if($cNegativo == "-") {
        $nSaldoFavor   = $nTotPag;
        $nTotAnt = $nTotAnt + ($nTotPag*-1);
        $nTotPag = 0;
      }
      
      ### Imprimo Subtotales ##
      $posx	= 10;
      $posy = 183;

      $pdf->SetFont('Arial', '', 7);
      $pdf->SetTextColor(100, 100, 100);
      $pdf->setXY($posx + 115, $posy);
      $pdf->Cell(40, 5, "Subtotal", 0, 0, 'L');
      $pdf->Cell(40, 5, number_format($nSubTotal, 0, '.', ','), 0, 0, 'R');
      $pdf->ln(5);
      $pdf->setX($posx + 115);
      $pdf->Cell(40, 5, "IVA", 0, 0, 'L');
      $pdf->Cell(40, 5, number_format($vCocDat['comivaxx'], 0, '.', ','), 0, 0, 'R');
      $pdf->ln(5);
      $pdf->setX($posx + 115);
      $pdf->Cell(40, 5, "Retefuente ".number_format($nPorRfte, 2, '.', ',') ."%", 0, 0, 'L');
      $pdf->Cell(40, 5, number_format($nTotRfte, 0, '.', ','), 0, 0, 'R');
      $pdf->ln(5);
      $pdf->setX($posx + 115);
      $pdf->Cell(40, 5, "Anticipo", 0, 0, 'L');
      $pdf->Cell(40, 5, number_format($nTotAnt, 0, '.', ','), 0, 0, 'R');
      $pdf->ln(5);
      $pdf->setX($posx + 115);
      $pdf->Cell(40, 5, "Total a Pagar", 0, 0, 'L');
      $pdf->Cell(40, 5, number_format($nTotPag, 0, '.', ','), 0, 0, 'R');
      $pdf->ln(5);

      $nToltalPagar = ($nSaldoFavor > 0) ? $nSaldoFavor : $nTotPag;
      $alinea       = explode("~",f_Words(f_Cifra_Php(abs($nToltalPagar),'PESO'),100));

      $cValorLetras = "";
      for ($n=0;$n<count($alinea);$n++) {
        $cValorLetras .= $alinea[$n];
      }

      $pdf->SetFillColor(217, 217, 217);
      $pdf->SetTextColor(0, 0, 0);
      $pdf->Rect($posx, $posy, 115, 15);
      $pdf->Rect($posx, $posy + 15, 115, 10, 'DF');
      $pdf->Rect($posx + 115, $posy, 80, $pdf->GetY() - $posy);
      $pdf->setXY($posx, $posy);
      $pdf->MultiCell(115, 3.3, utf8_decode("OBSERVACIONES: ".$vCocDat['comobsxx']), 0, 'L');
      $pdf->SetFont('Arial', 'I', 8);
      $pdf->setXY($posx, $posy + 15);
      $pdf->SetTextColor(0, 0, 0);
      $pdf->Cell(35, 5, "ANTICIPO REAL", 'T', 0, 'L');
      $pdf->Cell(80, 5, number_format($nAnticipoReal, 0, '.', ','), 'T', 0, 'R');
      $pdf->ln(5);
      $pdf->setX($posx);
      $pdf->Cell(35, 5, "SALDO A FAVOR DEL CLIENTE", 0, 0, 'L');
      $pdf->Cell(80, 5, number_format($nSaldoFavor, 0, '.', ','), 0, 0, 'R');
      $pdf->setXY($posx, $posy + 25);
      $pdf->SetFont('Arial', '', 6);
      $pdf->SetFont('Arial', 'IB', 7);
      $pdf->MultiCell(195, 5, utf8_decode("SON: " . $cValorLetras), 1, 'L', true);
      $pdf->setX($posx);

      $pdf->SetFont('Arial', '', 6.5);
      $pdf->MultiCell(195, 4, utf8_decode("LA MORA EN EL PAGO OCASIONARÁ INTERESES SOBRE LOS SALDOS A LA TASA MÁS ALTA PERMITIDA SIN PERJUICIO DE LAS CONDICIONES EJECUTIVAS PERTINENTES"), 1, 'L');
      $pdf->setX($posx);
      $cNotaFinal_1  = "LAS MERCANCIAS VIAJAN POR CUENTA Y RIESGO DE NUESTROS CLIENTES Y NO ASEGURAMOS LAS MISMAS DE NO MEDIAR ORDEN EXPRESA POR ESCRITO EN TODAS LAS OPERACIONES DE TRANSPORTE NUESTRA RESPONSABILIDAD NO PODRÁ EXCEDER NINGÚN CASO A LA QUE ASUME ";
      $cNotaFinal_1 .= "FRENTE A NOSOTROS LAS COMPAÑÍAS DE NAVEGACIÓN, AEREA Y TRANSPORTE POR CARRETERA O CUALQUIER OTRO INERMEDIARIO QUE INTERVENGA EN EL TRANSCURSO DEL TRANSPORTE. LOS FLETES QUEDAN SUBORDINADOS A LAS FACTURACIONES DE LAS TARIFAS AÉREAS MARÍTIMAS Y TERERESTRES.";
      $pdf->MultiCell(195, 3.5, utf8_decode($cNotaFinal_1), 1, 'L');
      $cNotaFinal_2 = "Esta factura se asimila en todos sus efectos a una letra de cambio. Art. 774 y 776 del Código de Comercio Colombiano. Paguese mediante Transferencia a Cuenta Cte Banco de Occidente No. 808-08439-6 a nombre de AGENCIA DE ADUANAS SOLUCIONES ADUANERAS S.A.S NIVEL 2";
      $pdf->setX($posx);
      $pdf->MultiCell(195, 3.5, utf8_decode($cNotaFinal_2), 1, 'L');

    }
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
?>
<?php
 /**
 * Imprimir Cheque y Comprobante para Interlogistica
 * Este programa permite Imprimir el Comprobante de egreso y el cheque para Interlogistica
 * @author  openTecnologia - Desarrollo
 * @package openComex
 * @version 3.0.0
 */
  include("../../../../libs/php/utility.php");

  ##Switch para incluir fuente y clase pdf segun base de datos ##
  switch($cAlfa){
  	case "COLMASXX":
  		define('FPDF_FONTPATH',"../../../../../fonts/");
  		require("../../../../../forms/fpdf.php");
  	break;
  	default:
  		define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
  		require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');
  	break;
  }
  ##Fin Switch para incluir fuente y clase pdf segun base de datos ##

  $pdf = new FPDF('P','mm','Letter');
  $pdf->AddFont('verdana','','verdana.php');
  $pdf->AddFont('verdanab','','verdanab.php');
  $pdf->AddFont('otfon1','','otfon1.php');
  $pdf->SetFont('verdana','',8);
  $pdf->SetMargins(0,0,0);
  $pdf->SetAutoPageBreak(0,0);

  // Cargo en una Matriz El/Los Comprobantes Seleccionados Para Imprimir
  $mPrn = explode("|",$prints);

  // Variable para la impresion de los dos comprobantes por hoja
  $nMedias = 0;

  // Defino las globales de la conexion y de la base de datos
  global $xConexion01; global $cAlfa;
  for ($nn=0;$nn<count($mPrn);$nn++) {
    if (strlen($mPrn[$nn]) > 0) {
      $vComp    = explode("~",$mPrn[$nn]);
  		$cComId   = $vComp[0];
  		$cComCod  = $vComp[1];
  		$cComCsc  = $vComp[2];
  		$cComCsc2 = $vComp[3];
  		$cComFec  = $vComp[4];
  		$cAno     = substr($cComFec,0,4);

  		$nMedias++;

  		////// CABECERA 1001 /////
  		$qCocDat  = "SELECT ";
  		$qCocDat .= "$cAlfa.fcoc$cAno.*, ";
  		$qCocDat .= "IF($cAlfa.fpar0117.comdesxx <> \"\",$cAlfa.fpar0117.comdesxx,\"COMPROBANTE SIN DESCRIPCION\") AS comdesxx, ";
  		$qCocDat .= "IF($cAlfa.fpar0117.pucidxxx <> \"\",$cAlfa.fpar0117.pucidxxx,\"COMPROBANTE SIN DESCRIPCION\") AS pucidxxx, ";
  		$qCocDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX, ";
  		$qCocDat .= "IF($cAlfa.A.CLINOMXX <> \"\",$cAlfa.A.CLINOMXX,CONCAT($cAlfa.A.CLINOM1X,' ',$cAlfa.A.CLINOM2X,' ',$cAlfa.A.CLIAPE1X,' ',$cAlfa.A.CLIAPE2X)) AS PRONOMXX, ";
  		$qCocDat .= "IF($cAlfa.B.CLINOMXX <> \"\",$cAlfa.B.CLINOMXX,CONCAT($cAlfa.B.CLINOM1X,' ',$cAlfa.B.CLINOM2X,' ',$cAlfa.B.CLIAPE1X,' ',$cAlfa.B.CLIAPE2X)) AS GIRNOMXX, ";
  		$qCocDat .= "IF($cAlfa.SIAI0003.USRNOMXX <> \"\",$cAlfa.SIAI0003.USRNOMXX,\"USUARIO SIN NOMBRE\") AS USRNOMXX ";
  		$qCocDat .= "FROM $cAlfa.fcoc$cAno ";
  		$qCocDat .= "LEFT JOIN $cAlfa.fpar0117 ON $cAlfa.fcoc$cAno.comidxxx = $cAlfa.fpar0117.comidxxx AND $cAlfa.fcoc$cAno.comcodxx = $cAlfa.fpar0117.comcodxx ";
      $qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcoc$cAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
      $qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 AS A ON $cAlfa.fcoc$cAno.terid2xx = $cAlfa.A.CLIIDXXX ";
      $qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 AS B ON $cAlfa.fcoc$cAno.terid3xx = $cAlfa.B.CLIIDXXX ";
      $qCocDat .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.fcoc$cAno.regusrxx = $cAlfa.SIAI0003.USRIDXXX ";
  		$qCocDat .= "WHERE $cAlfa.fcoc$cAno.comidxxx = \"$cComId\" AND ";
  		$qCocDat .= "$cAlfa.fcoc$cAno.comcodxx = \"$cComCod\" AND ";
  		$qCocDat .= "$cAlfa.fcoc$cAno.comcscxx = \"$cComCsc\" AND ";
  		$qCocDat .= "$cAlfa.fcoc$cAno.comcsc2x = \"$cComCsc2\" LIMIT 0,1";
  		//f_Mensaje(__FILE__,__LINE__,$qCocDat);

  		$xCocDat  = f_MySql("SELECT","",$qCocDat,$xConexion01,"");
  		$nFilCoc  = mysql_num_rows($xCocDat);
  		if ($nFilCoc > 0) {
  		  $vCocDat  = mysql_fetch_array($xCocDat);
  		  $zProDV = f_Genera_Dv($vCocDat['terid2xx']);
  		}
  		//////////////////////////////////////////////////////////////////////////

  		////// DETALLE 1002 /////
  		$qCodDat  = "SELECT DISTINCT ";
      $qCodDat .= "$cAlfa.fcod$cAno.*, ";
      $qCodDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
      $qCodDat .= "FROM $cAlfa.fcod$cAno ";
      $qCodDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcod$cAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
  		$qCodDat .= "WHERE $cAlfa.fcod$cAno.comidxxx = \"$cComId\" AND ";
  		$qCodDat .= "$cAlfa.fcod$cAno.comcodxx = \"$cComCod\" AND ";
  		$qCodDat .= "$cAlfa.fcod$cAno.comcscxx = \"$cComCsc\" AND ";
   		$qCodDat .= "$cAlfa.fcod$cAno.comcsc2x = \"$cComCsc2\" ORDER BY ABS($cAlfa.fcod$cAno.comseqxx) ASC";
  		$xCodDat  = f_MySql("SELECT","",$qCodDat,$xConexion01,"");
  		// f_Mensaje(__FILE__,__LINE__,$qCodDat);

      $nFilCod  = mysql_num_rows($xCodDat);
      if ($nFilCod > 0) {
        $i = 0;
        $mCodDat = array();
        while ($xRCD = mysql_fetch_array($xCodDat)) {
        $nInd_mCodDat = count($mCodDat);
          $mCodDat[$nInd_mCodDat] = $xRCD;
          $iA++;
          $zComDoc 	= trim($xRCD['comcsccx']);
          if ($zComDoc != $zComDocA)	{
            $zArrDoc[$i][0] =	$zComDocA;
            $zArrDoc[$i][1] = $zSubTot;
            $zComDocA       = $zComDoc;
            $zSubTot        = 0;
            $i++;
          }
          $zSubTot += 0+(($xRCD['comvlrxx'] > 0) ? $xRCD['comvlrxx'] : $xRCD['comvlrnf']);
        }
        $zArrDoc[$i][0] =	$zComDocA;
        $zArrDoc[$i][1] = $zSubTot;
      }
		  ///		CARGAR LA MATRIZ PARA COMPARAR LUEGO CON EL CAMPO MEMO DE LA fcoc$xAno		///
		  ////////////////////////////////////////////////////////////////////////////////////////
		  ///	BUSCO EN LA MATRIZ $zMatrizAce LAS DECLARACIONES DE LOS DO EN LA MATRIZ $zArrDoc ///
      for ($i=1;$i<count($zArrDoc);$i++) {
        $zCont = 0;
        for ($j=0;$j<count($zMatrizAce);$j++) {
          if ($zArrDoc[$i][0] == $zMatrizAce[$j]['docidxxx'])	{
            $zCont ++;
          }
        }
        $zArrDoc[$i][2] = $zCont;
      }

      for ($i=1;$i<count($zArrDoc);$i++) {
        $zCont = 0;
        for ($j=0;$j<count($zMatrizAce);$j++) {
          if ($zArrDoc[$i][0] == $zMatrizAce[$j]['docidxxx'])	{
            $zCont ++;
          }
        }
        $zArrDoc[$i][2] = $zCont;
      }

  		//////////////////////////////////////////////////////////////////////////

      $cude = 0;
      if ($nFilCoc > 0 && $nFilCod > 0) {
        // # DE PAGINAS //
        $cuantas = 1;
        $cude = $nFilCod-1;
        $m12  = $cude - 12;

        if($m12 > 0) {
          $resta = intval($m12 / 12);
          $cuantas+=$resta;
          $resta2 = ($resta*12)-$m12;
          if ($resta2 > 0){
            $cuantas++;
          }
        }

        $posx=100;
        $posx1=25;
        $posx2=37;
        $posx3=82;
        $posx4=86;
        $posx5=92;
        $posx6=110;
        $posxcr=155;
        $posxdb=125;

        $posy=120;
        $posy1=85;
        $posy2=110;
        $posy4=105;
        $posy3=95;
        $posyt=115;
        
        // Siguiente Pagina //
        $pdf->AddPage();

        $pdf->setXY(150,10);
        $pdf->SetFont('otfon1','',12);

 
				//Girado A: //
        $pdf->setXY(35,23);
        $pdf->Cell(90,3,"{$vCocDat['GIRNOMXX']}");
 
        $pdf->SetFont('otfon1','',12);
        $pdf->setXY($posx1,$posy1);
        $pdf->Cell(30,3,$vCocDat['comidxxx']."-".$vCocDat['comcodxx'].'-'.$vCocDat['comcscxx'].'-'.$vCocDat['ccoidxxx'].'-'.$vCocDat['comdesxx']);
        $pdf->setXY(146,$posy1);
        $pdf->Cell(30,3,$vCocDat['comfecve'],0,0,"R");

        $pdf->SetFont('otfon1','',12);
        $pdf->setXY($posx1,$posy4);
        $pdf->Cell(120,3,"Beneficiario: ".''. $vCocDat['CLINOMXX']);
        $pdf->setXY(135,$posy4);
        $pdf->Cell(120,3,"Consecutivo: ".(($vCocDat['comcsc3x'] != "") ? $vCocDat['comcsc3x'] : $vCocDat['comcsc2x']));

        $pdf->setXY($posx1,$posy2);
        $pdf->Cell(37,3,"Girado A:".''.substr($vCocDat['GIRNOMXX'],0,20).'-'.substr($vCocDat['terid3xx'],0,10).'-'.$zProDV);

        ////// TITULOS DE LA GRILLA //////
        $pdf->SetFont('otfon1','',12);
        $pdf->setXY($posx1,$posyt);
        $pdf->Cell(15,3,'ITEM');

        $pdf->SetFont('otfon1','',12);
        $pdf->setXY($posx2,$posyt);
        $pdf->Cell(15,3,'DOC.CRUCE');

        $pdf->SetFont('otfon1','',12);
        $pdf->setXY($posx3,$posyt);
        $pdf->Cell(15,3,'CUENTA');

        $pdf->SetFont('otfon1','',12);
        $pdf->setXY($posx6,$posyt);
        $pdf->Cell(20,3,'# DEL DO');

        $pdf->SetFont('otfon1','',12);
        $pdf->setXY($posxcr,$posyt);
        $pdf->Cell(30,3,'CREDITO',0,0,'R');

        $pdf->SetFont('otfon1','',12);
        $pdf->setXY($posxdb,$posyt);
        $pdf->Cell(30,3,'DEBITO',0,0,'R');

        $nCan = 0;
        for ($k=0;$k<count($mCodDat);$k++) {
          $total=0;
          //If para mostrar en la grilla el valor de credito y debito ///
          if ($mCodDat[$k]['commovxx'] == 'C') {
            if ($mCodDat[$k]['pucidxxx'] == $vCocDat['pucidxxx'])	{
              $total += ($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf'];
            }
          }

          if ($nCan == 30) {
            $pdf->AddPage();
            $posy=20;
            $nCan = 0;
          }
          
          $nCan++;
          $j++;
          
          $pdf->SetFont('otfon1','',12);
          $pdf->setXY($posx1,$posy);
          $pdf->Cell(10,3,str_pad($j,3,'0',STR_PAD_LEFT));
          $pdf->setXY($posx2,$posy);
          $pdf->Cell(20,3,($mCodDat[$k]['comidcxx']."-".$mCodDat[$k]['comcodcx']."-".$mCodDat[$k]['comcsccx']."-".$mCodDat[$k]['comseqcx']));

          $pdf->setXY($posx3,$posy);
          $pdf->cell(20,3,substr($mCodDat[$k]['pucidxxx'],0,20));

          $pdf->setXY($posx4,$posy);

          $pdf->setXY($posx6,$posy);
          $pdf->Cell(30,3,substr($mCodDat[$k]['comcscxx'],0,26));

          // If para mostrar en la grilla el valor de credito y debito ///
          $nComVlr = ($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf'];
          if ($mCodDat[$k]['commovxx'] == 'C') {
            $pdf->setXY($posxcr,$posy); //Debito //
            $pdf->Cell(30,3,((strpos(($nComVlr+0),'.') > 0) ? number_format(($nComVlr+0),2,',','.') : number_format(($nComVlr+0),0,',','.')),0,0,'R');
          } else {
            $pdf->setXY($posxdb,$posy);  //Credito //
            $pdf->Cell(30,3,((strpos(($nComVlr+0),'.') > 0) ? number_format(($nComVlr+0),2,',','.') : number_format(($nComVlr+0),0,',','.')),0,0,'R');
          }

          $posy+=4;
        }

        $pdf->setXY(95,14);
        $pdf->SetFont('otfon1','',12);
        $vMtzTar = explode("-",$vCocDat['comfecve']);
        $pdf->Cell(40,4,$vMtzTar[0].'     '.$vMtzTar[1].'  '.$vMtzTar[2],0,0,"R");
        switch($cAlfa){
          case "DEACODEXXX":
          case "TEACODEXXX":
          case "ACODEXXX":
            $pdf->Cell(30,4,number_format($total,2,'.',','),0,0,'R');
            break;
          default:
            $pdf->Cell(30,4,'$'.number_format($total,2,'.',','),0,0,'R');
            break;
        }
        

        $pdf->setXY(135,$posy2);
        $pdf->SetFont('otfon1','',12);
        $pdf->Cell(30,3,"Valor".'$'. number_format($total,2,'.',','),0,0,'L');
        $pdf->SetFont('otfon1','',12);
        $vObs = trim(f_Cifra_Php($total,'PESO'));
        $alinea = explode("~",f_Words($vObs,120));
        $py=33;

        if (count($alinea) > 3){
          $pdf->SetTextColor(255,0,0);
          $pdf->setXY(30,$py-2);
          $pdf->Cell(150,3,"Resultado superara las tres lineas,verifique");
          $pdf->SetTextColor(0,0,0);
        }	else	{
          for ($n=0;$n<count($alinea);$n++)	{
          	if ($alinea[$n] <> "") {
	            $pdf->setXY(35,$py-2);
	            $pdf->Cell(140,3,$alinea[$n]);
	            $py +=6;
          	}
          }
        }
        ////////////////////////////////////////////////////////////

        $posy+=4;
				$pdf->setXY(25, $posy);
        $pdf->SetFont('otfon1','',9);
				$pdf->Cell(25,3,substr($vCocDat['USRNOMXX'],0,20));
  		}
  		else {
				$pdf->AddPage();
				$pdf->Cell(100,20,"recibo incompleto verifique (2)");
  		}
  	}
  }

  $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
	$pdf->Output($cFile);

  if (file_exists($cFile)){
    chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
  } else {
    f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicarse con openTecnologia S.A.");
  }

	echo "<html><script>document.location='$cFile';</script></html>";
?>

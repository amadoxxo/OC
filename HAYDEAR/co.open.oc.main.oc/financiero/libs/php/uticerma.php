<?php

  /**
   * utiprofa.php : Utility de Clases de Certificado de Mandato
   *
   * Este script contiene la colecciones de clases para los Procesos de Certificado de Mandato
   * @author Hair Zabala C <hair.zabala@opentecnologia.com.co>
   * @package openComex
   */

  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");

  define("_NUMREG_",100);

  /*** Se define la libreria y la fuente para la creacón del archivo PDF. ***/
  $vBuscar = array("/produccion","/pruebas","/desarrollo");
  $vReempl = array("","","");
  
  if ($_SERVER["SERVER_PORT"] != "") {
    $cPath = $_SERVER['DOCUMENT_ROOT'];
  }else{
    $cPath = str_replace($vBuscar,$vReempl,$OPENINIT['pathdr']);
  }
  define('FPDF_FONTPATH',$cPath.$cSystem_Fonts_Directory.'/');
  require($cPath.$cSystem_Class_Directory.'/fpdf/fpdf.php');

  class cCertificadoMandato {

    /**
     * Metodo que genera el pdf del certificado de mandato
     */
    function fnGenerarCertificadoMandato($pArrayParametros) {

      global $xConexion01; global $cAlfa; global $vSysStr; global $kUser; global $nBan; global $cPlesk_Skin_Directory; global $cPath; global $OPENINIT;

      /**
       *Recibe como Parametro una Matriz con las siguientes posiciones:
       *
       *$pArrayParametros['DATOSXXX'] Data del certificado de origen
       *$pArrayParametros['RESDATXX'] Resolucion data
       *$pArrayParametros['RESIDXXX'] Id de la resolucion
       *$pArrayParametros['COCDATXX'] Data cabecera del comprobante.
       *$pArrayParametros['TIPOXXXX'] Certificado de mandata o intermediacion
       */

      /**
       * Variables para reemplazar caracteres especiales
       * @var array
       */
      $cBuscar = array('"',"'",chr(13),chr(10),chr(27),chr(9));
      $cReempl = array('\"',"\'"," "," "," "," ");

      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var number
       */
      $nSwitch = 0;

      /**
       * Variable para hacer el retorno.
       * @var array
       */
      $mReturn    = array();
      $mReturn[0] = "";

      /**
       * Instanciando Objetos para el Guardado de Errores
       */
      $objGuardarError = new cEstructurasCertificadoMandato();

      /*echo "<pre>";
      print_r($pArrayParametros);
      echo "</pre>";*/

      $mDatos  = $pArrayParametros['DATOSXXX'];
      $vResDat = $pArrayParametros['RESDATXX'];
      $vResId  = $pArrayParametros['RESIDXXX'];
      $vCocDat = $pArrayParametros['COCDATXX'];
      $gTipo   = $pArrayParametros['TIPOXXXX'];

      // $mDatos = array_merge($mDatos,$mDatos);
      // $mDatos = array_merge($mDatos,$mDatos);
      // $mDatos = array_merge($mDatos,$mDatos);

      /*** Traigo Ciudad del Facturado A ***/
      $qCiuDat  = "SELECT CIUDESXX ";
      $qCiuDat .= "FROM $cAlfa.SIAI0055 ";
      $qCiuDat .= "WHERE ";
      $qCiuDat .= "$cAlfa.SIAI0055.PAIIDXXX = \"{$vCocDat['PAIIDXXX']}\" AND ";
      $qCiuDat .= "$cAlfa.SIAI0055.DEPIDXXX = \"{$vCocDat['DEPIDXXX']}\" AND ";
      $qCiuDat .= "$cAlfa.SIAI0055.CIUIDXXX = \"{$vCocDat['CIUIDXXX']}\" AND ";
      $qCiuDat .= "$cAlfa.SIAI0055.REGESTXX = \"ACTIVO\" ";
      $xCiuDat  = f_MySql("SELECT","",$qCiuDat,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qCiuDat."~".mysql_num_rows($xCiuDat));
      $vCiuDes = mysql_fetch_array($xCiuDat);
      /*** Fin Traigo Ciudad del Facturado A ***/

      /*** Creación del PDF. ***/
      switch ($cAlfa) {
        case "DEALMAVIVA":
    	  case "TEALMAVIVA":
    	  case "ALMAVIVA":
          $pdf = new PDF('P','mm','Letter');
    		break;
    		default:
    		  $pdf = new PDF('L','mm','Letter');
    		break;
    	}

      $pdf->AddFont('verdana','','verdana.php');
    	$pdf->AddFont('verdanab','','verdanab.php');
    	$pdf->SetFont('verdana','',8);
    	$pdf->AliasNbPages();
    	$pdf->SetMargins(0,0,0);
    	$pdf->SetAutoPageBreak(false);

    	/*** Agrego una nueva pagina ***/
    	$pdf->AddPage();

      $nPosY = 10;  /*** Posicion en Y ***/
    	$nBan  = 1;
      $nPosX = 5;
      $nPosYFin = 185;

      switch ($cAlfa) {
        case "DEALMAVIVA":
    	  case "TEALMAVIVA":
    	  case "ALMAVIVA":

    		  $pdf->SetFont('verdana','',9);
    			$pdf->setXY($nPosX+5,$nPosY+5);
    		  $pdf->Cell(200,5,"El Contador General y el Revisor Fiscal de Almacenes Generales de Deposito Almaviva S.A.",0,0,'C');
    			$pdf->SetFont('verdana','',9);
    			$pdf->setXY($nPosX+5,$nPosY+10);
    		  $pdf->Cell(205,5,"en cumplimiento a lo establecido en el articulo 3o del Decreto 1514 de 1998",0,0,'C');
    		  $pdf->SetFont('verdanab','',12);
    			$pdf->setXY($nPosX+5,$nPosY+18);
    		  $pdf->Cell(205,5,"CERTIFICAN",0,0,'C');
    			$pdf->SetFont('verdana','',9);
    			$pdf->setXY($nPosX+5,$nPosY+24);
    		  $pdf->Cell(205,5,"Que de acuerdo con registros contables ,la Almacenadora ".utf8_decode("efectuó")." los siguientes pagos,en cumplimiento",0,0,'C');
    		  $pdf->SetFont('verdana','',9);
    			$pdf->setXY($nPosX+5,$nPosY+29);
    		  $pdf->Cell(205,5,"del contrato de mandato suscrito con ALIMENTOS FINCA S.A.S Nit 860.004.828 y que le ".utf8_decode("facturó"),0,0,'C');
    		  $pdf->SetFont('verdana','',9);
    			$pdf->setXY($nPosX+5,$nPosY+34);
    		  $pdf->Cell(205,5,"mediante el siguiente documento",0,0,'C');

          /*** Buscando datos del primer DO ***/
          $mDoiId = explode("|",$vCocDat['comfpxxx']);
          for ($i=0;$i<count($mDoiId);$i++) {
            if($mDoiId[$i] != "") {
              $vDoiId  = explode("~",$mDoiId[$i]);
              if($cDocId == "") {
                $cDocId  = $vDoiId[2];
                $cDocSuf = $vDoiId[3];
                $cSucId  = $vDoiId[15];
                $cDocPec = $vDoiId[7];
              }
              $dFecMay = ($dFecMay > substr($vDoiId[6],0,4)) ? substr($vDoiId[6],0,4) : $dFecMay;
            }//if($mDoiId[$i] != ""){
          }//for ($i=0;$i<count($mDoiId);$i++) {

    			$pdf->setXY($nPosX+5,$nPosY+40);
    	    $pdf->Cell(205,5,$vResDat['resprexx'].$vCocDat['comcscxx']." DO "."$cDocId"." - "."PEDIDO ".utf8_decode($cDocPec),0,0,'C');
    	    $pdf->SetFont('verdanab','',9);
    			$nPosY = 18;

    	    $pdf->setXY($nPosX+5,$nPosY+42);
    	    $pdf->Line($nPosX+6,$nPosY+47,$nPosX+32,$nPosY+47);
    	    $pdf->Cell(146,5,"DESCRIPCION",0,0,'L');

    			$pdf->Line($nPosX+168,$nPosY+47,$nPosX+174,$nPosY+47);
    		  $pdf->Cell(24,5,"IVA",0,0,'R');

    			$pdf->Line($nPosX+187,$nPosY+47,$nPosX+198,$nPosY+47);
    		  $pdf->Cell(24,5,"VALOR",0,0,'R');

    			$nPosY+=50;
    			$pdf->setY($nPosY);
    			$pdf->SetWidths(array(146,24,24));
    			$pdf->SetAligns(array("L","R","R"));

    			for($i=0; $i<count($mDatos); $i++) {

    	    	if($nPosY > $nPosYFin){
    	      	$pdf->AddPage();
    	        $nPosY = 32;
    	      }

    	      $pdf->SetFont('verdana','',9);
    				$pdf->setX($nPosX+5);
    				$pdf->SetFont('verdana','',9);
    				$pdf->Row(array(trim($mDatos[$i]['concepto'])."  FACT ".$mDatos[$i]['document']."- ".$mDatos[$i]['ternomxx']." NIT ".utf8_decode(trim($mDatos[$i]['teridxxx'])),
    												number_format($mDatos[$i]['ivaxxxxx'],2,',','.') ,
    												number_format($mDatos[$i]['totalxxx'],2,',','.')));

    				$nTotIva += $mDatos[$i]['ivaxxxxx'];
    	      $nTotFac += $mDatos[$i]['totalxxx'];

    			}

    			$nBan = 0;
    	  	$pdf->SetFont('verdanab','',9);
    			$nPosY = $pdf-> getY()+2;
    			$pdf->setY($nPosY);
    			$pdf->setXY($nPosX+5,$nPosY);
    	  	$pdf->Cell(146,5,"TOTAL ",0,0,'R');

    			$nPosY =$pdf-> getY();
    	  	$pdf->Line($nPosX+155,$nPosY,$nPosX+174,$nPosY);

    	  	$nPosY =$pdf-> getY();
    			$pdf->Line($nPosX+180,$nPosY,$nPosX+198,$nPosY);
    	  	$pdf->Cell(24,5,number_format($nTotIva,0,',','.'),0,0,'R');
    			$pdf->Cell(24,5,number_format($nTotFac,0,',','.'),0,0,'R');

    			$cLeyenda = utf8_decode("Esta certificacion se expide a los "). f_FormatFecActa(date('Y-m-d')).".";
    			$pdf->SetFont('verdana','',10);
    			$nPosY =$pdf-> getY()+10;
    			$pdf->setXY($nPosX+5,$nPosY);
    			$pdf->MultiCell(146,5,$cLeyenda,0,'J');
    			$pdf->SetFont('verdanab','',9);

    			$nPosY =$pdf-> getY()+20;
    			$pdf->setXY($nPosX+5,$nPosY);
    			$pdf->Cell(100,5,"---------------------------------------------",0,0,'J');
    			$cLeyenda1  = "MARIA NUBIA PINILLA \n";
    			$cLeyenda1 .= "Contador General \n";
    			$cLeyenda1 .= "T.P. No 94938-T";
    			$pdf->SetFont('verdanab','',9);
    			$nPosY = $pdf-> getY()+4;
    			$pdf->setXY($nPosX+5,$nPosY);
    			$pdf->MultiCell(100,5,$cLeyenda1,0,'J');

    			if($gFirma =="SI"){
    				$nPosY =$pdf-> getY()-19;
    				$pdf->setXY($nPosX+5,$nPosY);
    				$pdf->Cell(194,5,"---------------------------------------------",0,0,'R');
    				$cLeyenda2  = "RONALD ANDRES VIASUS AGUILAR \n";
      			$cLeyenda2 .= "REVISOR FISCAL DE ALMAVIVIA  S.A \n";
      			$cLeyenda2 .= "T.P. No 15.376 \n";
    				$cLeyenda2 .= "MIEMBRO DE KPMG LTDA.";
      			$pdf->SetFont('verdanab','',9);
    				$nPosY = $pdf-> getY()+4;
    				$pdf->setXY($nPosX+105,$nPosY);
      			$pdf->MultiCell(95,5,$cLeyenda2,0,'R');
    			}
    		break;
    		default:
          ##Switch para imprimir LOGO##
          switch($cAlfa){
            case "DEINTERLOG":
            case "TEINTERLOG":
            case "INTERLOG":
              $pdf->Image($cPath.$cPlesk_Skin_Directory.'/MaryAire.jpg',10,2,45,25);
            break;
            case "DECOLMASXX":
            case "TECOLMASXX":
            // case "DEDESARROL": //ALMACAFE
            case "COLMASXX":
              $pdf->Image($cPath.$cPlesk_Skin_Directory.'/colmas.jpg',10,10,40,15);
              $pdf->SetFont('verdanab','',6);
              $pdf->SetTextColor(129,129,133);
              $pdf->setXY(10,21);
              $pdf->Cell(28,10,$vSysStr['financiero_nit_agencia_aduanas'].'-'.f_Digito_Verificacion($vSysStr['financiero_nit_agencia_aduanas']),0,0,'L');
            	$pdf->SetTextColor(0,0,0);
            break;
            case "CASTANOX":
            case "TECASTANOX":
            case "DECASTANOX":
              $pdf->Image($cPath.$cPlesk_Skin_Directory.'/logomartcam.jpg',13,6,35,19);
            break;
            case "ALMACAFE": //ALMACAFE
            case "TEALMACAFE": //ALMACAFE
            case "DEALMACAFE": //ALMACAFE
              $pdf->Image($cPath.$cPlesk_Skin_Directory.'/logoalmacafe.jpg',12,8,35,15);
            break;
            case "ADIMPEXX": //ADIMPEXX
            case "TEADIMPEXX": //ADIMPEXX
            case "DEADIMPEXX": //ADIMPEXX
              $pdf->Image($cPath.$cPlesk_Skin_Directory.'/logoadimpex4.jpg',12,11,36,8);
              $pdf->Image($cPath.$cPlesk_Skin_Directory.'/logoadimpex5.jpg',255,00,25,20);
            break;
            case "GRUPOALC": //GRUPOALC
            case "TEGRUPOALC": //GRUPOALC
            case "DEGRUPOALC": //GRUPOALC
              $pdf->Image($cPath.$cPlesk_Skin_Directory.'/logoalc.jpg',5,7,40,15);
            break;
            case "DEALADUANA":
            case "TEALADUANA":
            case "ALADUANA":
              if ($gTipo == "CERTIFICADO") {
                $pdf->Image($cPath.$cPlesk_Skin_Directory.'/logoaladuana.jpg',10,2,30,20);
              }
            break;
            case "DEFEDEXEXP":
            case "TEFEDEXEXP":
            case "FEDEXEXP":
              $pdf->Image($cPath.$cPlesk_Skin_Directory.'/logofedexexp.jpg',7,2,33);
            break;
            case "FENIXSAS":
            case "TEFENIXSAS":
            case "DEFENIXSAS":
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logofenix.jpg',7,10,38);
            break;
            case "DEEXPORCOM":
            case "TEEXPORCOM":
            case "EXPORCOM":
              $pdf->Image($cPath.$cPlesk_Skin_Directory.'/logoexporcomex.jpg',7,4,33);
            break;
            case "HAYDEARX":
            case "DEHAYDEARX":
            case "TEHAYDEARX":
              $pdf->Image($cPath.$cPlesk_Skin_Directory.'/logohaydear.jpeg',7,4,46,20);
            break;
          }
          ##Switch para imprimir LOGO##

          switch($cAlfa){
            //ROLDAN
            case "ROLDANLO":
            case "TEROLDANLO":
            case "DEROLDANLO":
              $nPosY += 27;
              $pdf->SetFont('verdanab','',9);
              $pdf->setXY($nPosX,$nPosY);
              if ($gTipo == "CERTIFICADOINT") {
                $pdf->Cell(270,10,"INTERMEDIACION DE PAGO",0,0,'C');
              } else {
                $pdf->Cell(270,10,"CERTIFICADO DE MANDATO",0,0,'C');
              }
            break;
            default:
              $pdf->SetFont('verdanab','',9);
              $pdf->setXY($nPosX,$nPosY);
              if ($gTipo == "CERTIFICADOINT") {
                $pdf->Cell(270,10,"CERTIFICADO DE INTERMEDIACION DE PAGO",0,0,'C');
              } else {
                $pdf->Cell(270,10,"CERTIFICADO DE RETENCIONES POR PAGOS A TERCEROS",0,0,'C');
              }
            break;
          }

          switch($cAlfa){
            case "SIACOSIA":
            case "TESIACOSIP":
            case "DESIACOSIP":
              //Introduccion para siaco
              $cLeyenda="El suscrito Revisor Fiscal de la Agencia de Aduanas Siaco S.A.S Nivel 1 certifica que los valores relacionados a continuacion hacen parte de los costos o gastos y de los impuestos descontables de nuestro cliente {$vCocDat['CLINOMXX']} Nit. ".$vCocDat['teridxxx']."-".f_Digito_Verificacion($vCocDat['teridxxx']). " correspondiente a lo facturado el ". $this->fnFechaLetras($vCocDat['comfecxx']).", por lo tanto no se han tomado como deducciones o impuestos descontables por parte de la Agencia de Aduanas, igualmente certificamos que se han practicado y pagado las retenciones de acuerdo a la normatividad vigente.";

              $nPosY += 15;
              $pdf->SetFont('verdana','',10);
              $pdf->setXY($nPosX+5,$nPosY);
              $pdf->MultiCell(260,5,$cLeyenda,0,'J');
              //fin
              $nPosY += 25;
            break;
            case "DECOLMASXX":
            case "TECOLMASXX":
            // case "DEDESARROL": //ALMACAFE
            case "COLMASXX":
              $nPosY += 5;
              //Introduccion para colmas
              $cLeyenda  = "El suscrito Revisor Fiscal y/o Contador Publico de la Agencia de Aduanas Colmas S.A.S. Nivel 1  certifica que los valores relacionados ";
              $cLeyenda .= "a continuacion hacen parte de los costos o gastos y de los impuestos descontables de nuestro cliente {$vCocDat['CLINOMXX']} ";
              $cLeyenda .= "Nit. ".$vCocDat['teridxxx']."-".f_Digito_Verificacion($vCocDat['teridxxx']). " correspondiente a lo facturado el ". $this->fnFechaLetras($vCocDat['comfecxx']).", ";
              $cLeyenda .= "por lo tanto no se han tomado como deducciones o impuestos descontables por parte de la Agencia de Aduanas, ";
              $cLeyenda .= "igualmente certificamos que se han practicado y pagado las retenciones de acuerdo a la normatividad vigente.";
              $nPosY += 15;
              $pdf->SetFont('verdana','',10);
              $pdf->setXY($nPosX+5,$nPosY);
              $pdf->MultiCell(260,5,$cLeyenda,0,'J');
              //fin
              $nPosY += 25;
            break;
            case "DEADIMPEXX":
            case "TEADIMPEXX":
            case "ADIMPEXX":
              $nPosY += 5;
              //Introduccion para Adimpex
              $cLeyenda  = "El área de contabilidad de la compañía AGENCIA DE ADUANAS ADUANAMIENTOS IMPORTACIONES Y EXPORTACIONES S.A.S NIVEL 2 ";
              $cLeyenda .= "con Nit 830.032.263-9 de acuerdo con el artículo 3 del decreto 1514 de 1998, la Agencia a efectuado pagos a terceros en ";
              $cLeyenda .= "calidad de mandatario por cuenta del cliente {$vCocDat['CLINOMXX']} con Nit ".$vCocDat['teridxxx']."-".f_Digito_Verificacion($vCocDat['teridxxx']).", Así: ";
              $nPosY += 15;
              $pdf->SetFont('verdana','',10);
              $pdf->setXY($nPosX+5,$nPosY);
              $pdf->MultiCell(260,5,utf8_decode($cLeyenda),0,'J');
              //fin
              $nPosY += 25;
            break;
            case "DEFEDEXEXP":
            case "TEFEDEXEXP":
            case "FEDEXEXP":
              //Introduccion para Fedex
              $cLeyenda1  = "Con el fin de acreditar el cumplimiento de lo dispuesto en el artículo 1.6.1.4.9 del decreto único reglamentario del 2016, bajo la gravedad de juramento.";
              $cLeyenda2  = "Que, para efectos de soportar los respectivos costos, deducciones o impuestos descontables o devoluciones a que tenga ";
              $cLeyenda2 .= "derecho el mandante, se relaciona a continuación el concepto y la cuantía en los que se incurrieron en la celebración del contrato de mandato.";
              
              $nPosY += 14;
              $pdf->SetFont('verdana','',8);
              $pdf->setXY($nPosX+5,$nPosY);
              $pdf->MultiCell(260,4,utf8_decode($cLeyenda1),0,'C');
              $nPosY += 6;
              $pdf->SetFont('verdanab','',8);
              $pdf->setXY($nPosX+5,$nPosY);
              $pdf->Cell(260,4,"CERTIFICO",0,0,'C');
              $nPosY += 6;
              $pdf->SetFont('verdana','',8);
              $pdf->setXY($nPosX+5,$nPosY);
              $pdf->MultiCell(260,4,utf8_decode($cLeyenda2),0,'J');
              //fin
              $nPosY += 10;
            break;
            default:
              $nPosY += 12;
            break;
          }

    			//Datos del cliente
    	    switch($cAlfa){
    	    	case "ROLDANLO":
    	    	case "TEROLDANLO":
    	    	case "DEROLDANLO":
    					$pdf->setXY($nPosX,$nPosY);
    				  $pdf->SetFont('verdanab','',7);
    				  $pdf->Cell(10,4,"Ciudad:",0,0,'J');
    				  $pdf->setXY($nPosX,$nPosY+4);
    				  $pdf->Cell(10,4,utf8_decode("Dirección:"),0,0,'J');
    					$pdf->setXY($nPosX,$nPosY+8);
    				  $pdf->Cell(10,4,utf8_decode("Teléfono:"),0,0,'J');
    				  $pdf->setXY($nPosX,$nPosY+12);
    				  $pdf->Cell(28,4,"Factura de Venta:",0,0,'J');
    				  $pdf->setXY($nPosX,$nPosY+16);
              $pdf->Cell(10,4,"Fecha Factura de Venta:",0,0,'J');
              $pdf->setXY($nPosX,$nPosY+20);
    			    $pdf->Cell(16,4,utf8_decode("Número D.O.: "),0,0,'J');

    				  $pdf->SetFont('verdana','',7);
    					switch($vCocDat['ccoidxxx']){
    			    	//Datos de ROLDAN por Sucursal
    			    	case "01":
    			    		$pdf->setXY($nPosX+35,$nPosY);
    						  $pdf->Cell(100,4,utf8_decode("BOGOTÁ-COLOMBIA"),0,0,'J');
    						  $pdf->setXY($nPosX+35,$nPosY+4);
    						  $pdf->Cell(100,4,strtoupper("Cr 100 25B 40"),0,0,'J');
    							$pdf->setXY($nPosX+35,$nPosY+8);
    						  $pdf->Cell(100,4,"4042904",0,0,'J');
    	    			break;
    			    	case "02":
    						  $pdf->setXY($nPosX+35,$nPosY);
    						  $pdf->Cell(100,4,"BARRANQUILLA-COLOMBIA",0,0,'J');
    						  $pdf->setXY($nPosX+35,$nPosY+4);
    						  $pdf->Cell(100,4,strtoupper("Cr 30 Av. Hamburgo Ed. Administrativo Zona Franca Ps 2"),0,0,'J');
    						  $pdf->setXY($nPosX+35,$nPosY+8);
    						  $pdf->Cell(100,4,"3447648 - 3447649",0,0,'J');
    	    			break;
    			    	case "03":
    						  $pdf->setXY($nPosX+35,$nPosY);
    						  $pdf->Cell(100,4,"BUENAVENTURA-COLOMBIA",0,0,'J');
    						  $pdf->setXY($nPosX+35,$nPosY+4);
    						  $pdf->Cell(100,4,strtoupper("Cl 8 3 50 Of 302/303 Ed Roldan"),0,0,'J');
    						  $pdf->setXY($nPosX+35,$nPosY+8);
    						  $pdf->Cell(100,4,"2433624 - 2408077",0,0,'J');
    	    			break;
    			    	case "04":
    			    		$pdf->setXY($nPosX+35,$nPosY);
    						  $pdf->Cell(100,4,"CALI-COLOMBIA",0,0,'J');
    						  $pdf->setXY($nPosX+35,$nPosY+4);
    						  $pdf->Cell(100,4,strtoupper("Cl 10 4 47 Of 503"),0,0,'J');
    						  $pdf->setXY($nPosX+35,$nPosY+8);
    						  $pdf->Cell(100,4,"8822860 - 8822171",0,0,'J');
    	    			break;
    			    	case "05":
    			    		$pdf->setXY($nPosX+35,$nPosY);
    						  $pdf->Cell(100,4,"CARTAGENA-COLOMBIA",0,0,'J');
    						  $pdf->setXY($nPosX+35,$nPosY+4);
    						  $pdf->Cell(100,4,strtoupper("Manga Cr 27 29 43 Unidad 4 Zona Franca"),0,0,'J');
    						  $pdf->setXY($nPosX+35,$nPosY+8);
    						  $pdf->Cell(100,4,"6609298",0,0,'J');
    	    			break;
    			    	case "06":
    			    		$pdf->setXY($nPosX+35,$nPosY);
    						  $pdf->Cell(100,4,"MANIZALES-COLOMBIA",0,0,'J');
    						  $pdf->setXY($nPosX+35,$nPosY+4);
    						  $pdf->Cell(100,4,strtoupper("Cr 23 63 15 Of 405 Ed. El Castillo"),0,0,'J');
                  $pdf->setXY($nPosX+35,$nPosY+8);
                  $pdf->Cell(100,4,"8862450",0,0,'J');
    	    			break;
    			    	case "07":
    			    		$pdf->setXY($nPosX+35,$nPosY);
    						  $pdf->Cell(100,4,utf8_decode("MEDELLÍN-COLOMBIA"),0,0,'J');
    						  $pdf->setXY($nPosX+35,$nPosY+4);
    						  $pdf->Cell(100,4,strtoupper("Cr 43A 1A Sur 69 Of 703 Ed. Tempo"),0,0,'J');
    						  $pdf->setXY($nPosX+35,$nPosY+8);
    						  $pdf->Cell(100,4,"3520687",0,0,'J');
    	    			break;
    			    	case "08":
    			    		$pdf->setXY($nPosX+35,$nPosY);
    						  $pdf->Cell(100,4,"PEREIRA-COLOMBIA",0,0,'J');
    						  $pdf->setXY($nPosX+35,$nPosY+4);
    						  $pdf->Cell(100,4,strtoupper(utf8_decode("Cr 8 20 67 Of 403 Ed. BANCO UNIÓN COLOMBIANO")),0,0,'J');
    						  $pdf->setXY($nPosX+35,$nPosY+8);
    						  $pdf->Cell(100,4,"3240923",0,0,'J');
    	    			break;
    			    	case "09":
    			    		$pdf->setXY($nPosX+35,$nPosY);
    						  $pdf->Cell(100,4,"SANTA MARTA-COLOMBIA",0,0,'J');
    						  $pdf->setXY($nPosX+35,$nPosY+4);
    						  $pdf->Cell(100,4,strtoupper("Cl 10C 1C 51 "),0,0,'J');
    						  $pdf->setXY($nPosX+35,$nPosY+8);
    						  $pdf->Cell(100,4,"4214299",0,0,'J');
    	    			break;
    			    	case "12":
    			    		$pdf->setXY($nPosX+35,$nPosY);
    						  $pdf->Cell(100,4,"IPIALES-COLOMBIA",0,0,'J');
    						  $pdf->setXY($nPosX+35,$nPosY+4);
    						  $pdf->Cell(100,4,strtoupper("Cr 6 14 33 Of 402 Ed. Bastidas"),0,0,'J');
    						  $pdf->setXY($nPosX+35,$nPosY+8);
    						  $pdf->Cell(100,4,"7732715",0,0,'J');
    	    			break;
    						default: //No hace nada
    						break;
    	    	  }

              //Para Roldan se imprime el prefijo de la factura y se completa con ceros el consecutivo
              //Se busca la resolucion con la que se guardo la factura
              if ($vResId["{$vCocDat['residxxx']}~{$vCocDat['resprexx']}~{$vCocDat['restipxx']}"]['resdesxx'] != "") {
                $cPrefijo  = $vResId["{$vCocDat['residxxx']}~{$vCocDat['resprexx']}~{$vCocDat['restipxx']}"]['resprexx'];
                $cLongitud = strlen($vResId["{$vCocDat['residxxx']}~{$vCocDat['resprexx']}~{$vCocDat['restipxx']}"]['resdesxx']);
              } else { //Si no hay registro, se usa la que tiene actualmente el comprobante
                $cPrefijo  = $vResDat["{$vCocDat['comidxxx']}~{$vCocDat['comcodxx']}"]['resprexx'];
                $cLongitud = strlen($vResDat["{$vCocDat['comidxxx']}~{$vCocDat['comcodxx']}"]['resdesxx']);
              }
              $cFactura = trim((($cPrefijo != "") ? $cPrefijo."-" : "").str_pad($vCocDat['comcscxx'],$cLongitud,"0",STR_PAD_LEFT));

              $pdf->setXY($nPosX+35,$nPosY+12);
              $pdf->Cell(100,4,$cFactura,0,0,'J');
              $pdf->setXY($nPosX+35,$nPosY+16);
              $pdf->Cell(100,4,$vCocDat['comfecxx'],0,0,'J');

              //Buscando datos del primer DO
              $mDoiId = explode("|",$vCocDat['comfpxxx']);
              for ($i=0;$i<count($mDoiId);$i++) {
                if($mDoiId[$i] != "") {
                  $vDoiId  = explode("~",$mDoiId[$i]);
                  if($cDocId == "") {
                    $cDocId  = $vDoiId[2];
                    $cDocSuf = $vDoiId[3];
                    $cSucId  = $vDoiId[15];
                    $cDocPec = $vDoiId[7];
                  }
                  $dFecMay = ($dFecMay > substr($vDoiId[6],0,4)) ? substr($vDoiId[6],0,4) : $dFecMay;
                }//if($mDoiId[$i] != ""){
              }//for ($i=0;$i<count($mDoiId);$i++) {

              //Valida si el Facturar a es DSV SOLUTIONS S.A.S. para imprimir el nombre y el Nit
              if ($vCocDat['terid2xx'] == "860046509") {
                $qNomClix  = "SELECT ";
                $qNomClix .= "$cAlfa.SIAI0150.CLIIDXXX, ";
                $qNomClix .= "$cAlfa.SIAI0150.PAIIDXXX, ";
                $qNomClix .= "$cAlfa.SIAI0150.DEPIDXXX, ";
                $qNomClix .= "$cAlfa.SIAI0150.CIUIDXXX, ";
                $qNomClix .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX, ";
                $qNomClix .= "$cAlfa.SIAI0150.CLIDIRXX, ";
                $qNomClix .= "$cAlfa.SIAI0150.CLITELXX ";
                $qNomClix .= "FROM $cAlfa.SIAI0150 ";
                $qNomClix .= "WHERE ";
                $qNomClix .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$vCocDat['terid2xx']}\" AND ";
                $qNomClix .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
                $xNomClix  = f_MySql("SELECT","",$qNomClix,$xConexion01,"");
                if (mysql_num_rows($xNomClix) > 0) {
                  $vNomClix = mysql_fetch_array($xNomClix);
                  $vCocDat['CLINOMXX'] = $vNomClix['CLINOMXX'];
                  $vCocDat['teridxxx'] = $vNomClix['CLIIDXXX'];
                  $vCocDat['CLIDIRXX'] = $vNomClix['CLIDIRXX'];
                  $vCocDat['CLITELXX'] = $vNomClix['CLITELXX'];

                  /*** Ciudad ***/
                  $qCiudad  = "SELECT CIUDESXX ";
                  $qCiudad .= "FROM $cAlfa.SIAI0055 ";
                  $qCiudad .= "WHERE ";
                  $qCiudad .= "PAIIDXXX = \"{$vNomClix['PAIIDXXX']}\" AND ";
                  $qCiudad .= "DEPIDXXX = \"{$vNomClix['DEPIDXXX']}\" AND ";
                  $qCiudad .= "CIUIDXXX = \"{$vNomClix['CIUIDXXX']}\" LIMIT 0,1";
                  $xCiudad  = f_MySql("SELECT","",$qCiudad,$xConexion01,"");

                  if (mysql_num_rows($xCiudad) > 0) {
                    $vCiudad  = mysql_fetch_array($xCiudad);
                    $vCiuDes['CIUDESXX'] = $vCiudad['CIUDESXX'];
                  }
                }
              }
              //Fin Valida si el Facturar a es DSV SOLUTIONS S.A.S. para imprimir el nombre y el Nit

              //Numero del DO
              $pdf->setXY($nPosX+35,$nPosY+20);
              $pdf->Cell(100,4,$cDocId."-".$cDocSuf,0,0,'J');

    	    		//Datos del cliente para ROLDAN
    	    		$pdf->setXY($nPosX+128,$nPosY);
    			    $pdf->SetFont('verdanab','',7);
    			    $pdf->Cell(16,4,"Cliente:",0,0,'J');
    			    $pdf->SetFont('verdana','',7);
    			    $pdf->Cell(125,4,$vCocDat['CLINOMXX'],0,0,'J');

    					$pdf->setXY($nPosX+128,$nPosY+4);
    			    $pdf->SetFont('verdanab','',7);
    			    $pdf->Cell(16,4,"NIT: ",0,0,'J');
    			    $pdf->SetFont('verdana','',7);
    			    $pdf->Cell(125,4,substr($vCocDat['teridxxx']."-".f_Digito_Verificacion($vCocDat['teridxxx']),0,75),0,0,'J');

    			    $pdf->setXY($nPosX+128,$nPosY+8);
    			    $pdf->SetFont('verdanab','',7);
    			    $pdf->Cell(16,4,utf8_decode("Dirección:"),0,0,'J');
    			    $pdf->SetFont('verdana','',7);
    			    $pdf->Cell(125,4,$vCocDat['CLIDIRXX'],0,0,'J');

    					$pdf->setXY($nPosX+128,$nPosY+12);
    			    $pdf->SetFont('verdanab','',7);
    			    $pdf->Cell(16,4,utf8_decode("Teléfono:"),0,0,'J');
    			    $pdf->SetFont('verdana','',7);
    			    $pdf->Cell(125,4,$vCocDat['CLITELXX'],0,0,'J');

    					$pdf->setXY($nPosX+128,$nPosY+16);
    			    $pdf->SetFont('verdanab','',7);
    			    $pdf->Cell(16,4,"Ciudad: ",0,0,'J');
    			    $pdf->SetFont('verdana','',7);
    			    $pdf->Cell(125,4,$vCiuDes['CIUDESXX'],0,0,'J');

    					$pdf->setXY($nPosX+128,$nPosY+20);
    			    $pdf->SetFont('verdanab','',7);
              $pdf->Cell(16,4,"Pedido: ",0,0,'J');
              $pdf->setXY($nPosX+144,$nPosY+20);
    			    $pdf->SetFont('verdana','',7);
    			    $pdf->MultiCell(125,3.5,$cDocPec,0,'L');

    			    $nPosY += 28;
    	    	break;
    	    	default:
    	    		$pdf->setXY(5,$nPosY);
    			    $pdf->SetFont('verdanab','',7);
    			    $pdf->Cell(12,10,"Cliente:",0,0,'L');
    			    $pdf->SetFont('verdana','',7);
    			    $pdf->Cell(168,10,$vCocDat['CLINOMXX'],0,0,'L');
    			    $pdf->SetFont('verdanab','',7);
    			    $pdf->Cell(10,10,"NIT: ",0,0,'L');
    			    $pdf->SetFont('verdana','',7);
    			    $pdf->Cell(30,10,$vCocDat['teridxxx']."-".f_Digito_Verificacion($vCocDat['teridxxx']),0,0,'L');
    			    $pdf->SetFont('verdanab','',7);
    			    $pdf->Cell(30,10,"FECHA IMPRESION : ",0,0,'L');
    			    $pdf->SetFont('verdana','',7);
    			    $pdf->Cell(20,10,date('Y-m-d'),0,0,'R');
    			    $nPosY += 8;
    	    	break;
          }

          switch($cAlfa){
            case "ROLDANLO":
            case "DEROLDANLO":
            case "TEROLDANLO":
              $nCellDo  = 25;
              $nCellFac = 18;
              $nPosxFac = 30;
            break;
            default:
              $nCellDo  = 28;
              $nCellFac = 15;
              $nPosxFac = 33;
            break;
          }

    	    $pdf->setXY(5,$nPosY);
    		  $pdf->SetFont('verdanab','',6);
    		  $pdf->Cell($nCellDo,10,"D.O.",0,0,'C');
    		  $pdf->Rect(5,$nPosY+2,$nCellDo,5);
    		  $pdf->setXY($nPosxFac,$nPosY);
    		  $pdf->SetFont('verdanab','',6);
    		  $pdf->Cell($nCellFac,10,"FACTURA",0,0,'C');
    		  $pdf->Rect($nPosxFac,$nPosY+2,$nCellFac,5);
    		  $pdf->setXY(48,$nPosY);
    		  $pdf->SetFont('verdanab','',6);

    			switch($cAlfa){
            case "ROLDANLO":
            case "DEROLDANLO":
            case "TEROLDANLO":
              $pdf->Cell(15,10,"NIT",0,0,'C');
    				break;
            default:
        		  $pdf->Cell(15,10,"TERCERO",0,0,'C');
            break;
          }

    		  $pdf->Rect(48,$nPosY+2,15,5);
    		  $pdf->setXY(63,$nPosY);
    		  $pdf->SetFont('verdanab','',6);
    			switch($cAlfa){
            case "ROLDANLO":
            case "DEROLDANLO":
            case "TEROLDANLO":
              $pdf->Cell(30,10,"PROVEEDOR",0,0,'C');
    				break;
            default:
        		  $pdf->Cell(30,10,"NOMBRE",0,0,'C');
            break;
          }

    		  $pdf->Rect(63,$nPosY+2,30,5);
    		  $pdf->setXY(93,$nPosY);
    		  $pdf->SetFont('verdanab','',6);
    			switch($cAlfa){
            case "ROLDANLO":
            case "DEROLDANLO":
            case "TEROLDANLO":
              $pdf->Cell(20,10,"FACTURA",0,0,'C');
    				break;
            default:
        		  $pdf->Cell(20,10,"DOCUMENTO",0,0,'C');
            break;
          }

    		  $pdf->Rect(93,$nPosY+2,20,5);
    		  $pdf->setXY(113,$nPosY);
    		  $pdf->SetFont('verdanab','',6);
    		  $pdf->Cell(15,10,"FECHA",0,0,'C');
    		  $pdf->Rect(113,$nPosY+2,15,5);
    		  $pdf->setXY(128,$nPosY);
    		  $pdf->SetFont('verdanab','',6);
    		  $pdf->Cell(29,10,"CONCEPTO",0,0,'C');
    		  $pdf->Rect(128,$nPosY+2,29,5);
    		  $pdf->setXY(157,$nPosY);
    		  $pdf->SetFont('verdanab','',6);

    		  switch($cAlfa){
            case "ROLDANLO":
            case "DEROLDANLO":
            case "TEROLDANLO":
              $pdf->Cell(18,10,"VALOR",0,0,'C');
    				break;
            default:
        		 $pdf->Cell(18,10,"COSTO",0,0,'C');
            break;
          }

    		  $pdf->Rect(157,$nPosY+2,18,5);
    		  $pdf->setXY(175,$nPosY);
    		  $pdf->SetFont('verdanab','',6);
    		  $pdf->Cell(15,10,"IVA",0,0,'C');
    		  $pdf->Rect(175,$nPosY+2,15,5);
    		  $pdf->setXY(190,$nPosY);
    		  $pdf->SetFont('verdanab','',6);
    		  $pdf->Cell(20,10,"TOTAL",0,0,'C');
    		  $pdf->Rect(190,$nPosY+2,20,5);
    		  $pdf->setXY(210,$nPosY);
    		  $pdf->SetFont('verdanab','',6);
    		  $pdf->Cell(15,10,"TIPO",0,0,'C');
    		  $pdf->Rect(210,$nPosY+2,15,5);
    		  $pdf->setXY(225,$nPosY);
    		  $pdf->SetFont('verdanab','',6);
    		  $pdf->Cell(20,10,"VALOR BASE",0,0,'C');
    		  $pdf->Rect(225,$nPosY+2,20,5);
    		  $pdf->setXY(245,$nPosY);
    		  $pdf->SetFont('verdanab','',6);
    		  $pdf->Cell(10,10,"%",0,0,'C');
    		  $pdf->Rect(245,$nPosY+2,10,5);
    		  $pdf->setXY(255,$nPosY);
    		  $pdf->SetFont('verdanab','',6);
    		  $pdf->Cell(20,10,"VALOR",0,0,'C');
    		  $pdf->Rect(255,$nPosY+2,20,5);
    	    $nPosY += 7;

          // Se pinta el detalle de los Items del Certificado
          switch($cAlfa){
            case "ROLDANLO":
            case "DEROLDANLO":
            case "TEROLDANLO":
              $pdf->SetWidths(array(25,18,15,30,20,15,29,18,15,20));
              $pdf->SetAligns(array("C","C","L","L","C","C","L","R","R","R"));

              for($i=0; $i<count($mDatos); $i++) {
                
                $nLinePro = $pdf->NbLines(30, trim($mDatos[$i]['ternomxx']));
                $nLineCto = $pdf->NbLines(29, trim($mDatos[$i]['concepto']));
                $nLineTot = (max($nLinePro, $nLineCto)*4)-1;
                $pdf->setY($nPosY);

                if(($pdf->getY()+$nLineTot) > 139){
                  $pdf->AddPage();
                  $nPosY = 57;
                  $pdf->setY($nPosY);
                }

                $n = 0;
                $nCantRte = 1;

                $mRetenciones = array();
                $mRetenciones = $mDatos[$i]['retencio'];

                if (count($mRetenciones) > 0) {
                  $n += (4*count($mRetenciones));
                  $nCantRte = count($mRetenciones);
                }

                $n = ($n != 0)?$n:4;

                if ($mDatos[$i]['docidxxx'] != "") {
                  $mDatos[$i]['docidxxx'] = $mDatos[$i]['sucidxxx']."-".$mDatos[$i]['docidxxx']."-".$mDatos[$i]['docsufxx'];
                }

                $pdf->setX(5);
                $pdf->SetFont('verdana','',5.5);
                $pdf->Row(array(
                  $mDatos[$i]['docidxxx'],
                  $mDatos[$i]['facturax'],
                  trim($mDatos[$i]['teridxxx']),
                  trim($mDatos[$i]['ternomxx']),
                  $mDatos[$i]['document'],
                  $mDatos[$i]['comfecxx'],
                  trim($mDatos[$i]['concepto']),
                  number_format($mDatos[$i]['costoxxx'],0,',','.'),
                  number_format($mDatos[$i]['ivaxxxxx'],0,',','.'),
                  number_format($mDatos[$i]['totalxxx'],0,',','.')
                ));

                $nPyFinal1 = $pdf->getY();
                $nAltoRect = ($pdf->getY()-$nPosY);
                $nRect     = ($n > $nAltoRect) ? 4 : $nAltoRect/$nCantRte;
                $nPosY2    = $nPosY;

                if (count($mRetenciones) > 0) {
                  for ($y = 0; $y < count($mRetenciones); $y++) {
                    $pdf->setXY(210,$nPosY2);
                    $pdf->Cell(15,4,$mRetenciones[$y]['retenxxx'],0,0,'L');
                    $pdf->Rect(210,$nPosY2,15,$nRect);
    
                    $pdf->setXY(225,$nPosY2);
                    $pdf->Cell(20,4,number_format($mRetenciones[$y]['comvlr01'],0,',','.'),0,0,'R');
                    $pdf->Rect(225,$nPosY2,20,$nRect);
    
                    $pdf->setXY(245,$nPosY2);
                    $pdf->Cell(10,4,number_format($mRetenciones[$y]['pucretxx'],3,',','.'),0,0,'C');
                    $pdf->Rect(245,$nPosY2,10,$nRect);
    
                    $nRetencion = round($mRetenciones[$y]['comvlrxx']);
                    if($mRetenciones[$y]['retenxxx'] == 'ReteCree'){
                      $nTotRCre += $nRetencion;
                    }
                    if($mRetenciones[$y]['retenxxx'] == 'Retefuente'){
                      $nTotRfte += $nRetencion;
                    }
                    if($mRetenciones[$y]['retenxxx'] == 'ReteIva'){
                      $nTotRIva += $nRetencion;
                    }
                    if($mRetenciones[$y]['retenxxx'] == 'ReteIca'){
                      $nTotRIca += $nRetencion;
                    }
    
                    $pdf->setXY(255,$nPosY2);
                    $pdf->Cell(20,4,number_format($nRetencion,0,',','.'),0,0,'R');
                    $pdf->Rect(255,$nPosY2,20,$nRect);
                    $nPosY2 += $nRect;
                  }
                }else{
                  $pdf->setXY(210,$nPosY);
                  $pdf->Cell(15,4,"",0,0,'L');
                  $pdf->Rect(210,$nPosY,15,$nRect);
    
                  $pdf->setXY(225,$nPosY);
                  $pdf->Cell(20,4,"",0,0,'R');
                  $pdf->Rect(225,$nPosY,20,$nRect);
    
                  $pdf->setXY(245,$nPosY);
                  $pdf->Cell(10,4,"",0,0,'C');
                  $pdf->Rect(245,$nPosY,10,$nRect);
    
                  $pdf->setXY(255,$nPosY);
                  $pdf->Cell(20,4,"",0,0,'R');
                  $pdf->Rect(255,$nPosY,20,$nRect);
                }

                $nPosyFinal = ($nPyFinal1 > $nPosY2) ? $nPyFinal1 : $nPosY2;

                $pdf->Rect(5,$nPosY,205,($nPosyFinal-$nPosY));
                $pdf->Line(30,$nPosY,30,$nPosyFinal);
                $pdf->Line(48,$nPosY,48,$nPosyFinal);
                $pdf->Line(63,$nPosY,63,$nPosyFinal);
                $pdf->Line(93,$nPosY,93,$nPosyFinal);
                $pdf->Line(113,$nPosY,113,$nPosyFinal);
                $pdf->Line(128,$nPosY,128,$nPosyFinal);
                $pdf->Line(157,$nPosY,157,$nPosyFinal);
                $pdf->Line(175,$nPosY,175,$nPosyFinal);
                $pdf->Line(190,$nPosY,190,$nPosyFinal);

                $nTotCos += $mDatos[$i]['costoxxx'];
                $nTotIva += $mDatos[$i]['ivaxxxxx'];
                $nTotFac += $mDatos[$i]['totalxxx'];

                $nPosY = $nPosyFinal;
              }

              // Se pinta una nueva fila con la informacion del GMF
              if ($vCocDat['comifxxx'] > 0) {
                $pdf->setY($nPosY);
                $pdf->setX(5);
                $pdf->SetFont('verdana','',5.5);
                $pdf->Row(array(
                  $cSucId . "-" . $cDocId . "-" . $cDocSuf,
                  $cFactura,
                  $vSysStr['roldanlo_nit_banco_reporte_pcc'],
                  $vSysStr['roldanlo_nombre_banco_reporte_pcc'],
                  $cFactura,
                  $vCocDat['comfecxx'],
                  "GMF",
                  number_format($vCocDat['comifxxx'],0,',','.'),
                  "0",
                  number_format($vCocDat['comifxxx'],0,',','.')
                ));

                $nPosyFinal = $pdf->getY();
                $pdf->Rect(5,$nPosY,205,($nPosyFinal-$nPosY));
                $pdf->Line(30,$nPosY,30,$nPosyFinal);
                $pdf->Line(48,$nPosY,48,$nPosyFinal);
                $pdf->Line(63,$nPosY,63,$nPosyFinal);
                $pdf->Line(93,$nPosY,93,$nPosyFinal);
                $pdf->Line(113,$nPosY,113,$nPosyFinal);
                $pdf->Line(128,$nPosY,128,$nPosyFinal);
                $pdf->Line(157,$nPosY,157,$nPosyFinal);
                $pdf->Line(175,$nPosY,175,$nPosyFinal);
                $pdf->Line(190,$nPosY,190,$nPosyFinal);
                $pdf->Line(225,$nPosY,225,$nPosyFinal);
                $pdf->Line(245,$nPosY,245,$nPosyFinal);
                $pdf->Line(255,$nPosY,255,$nPosyFinal);
                $pdf->Line(275,$nPosY,275,$nPosyFinal);
                $nPosY = $pdf->getY();

                $nTotFac += $vCocDat['comifxxx'];
                $nTotCos += $vCocDat['comifxxx'];
              }

            break;
            default:
              for($i=0; $i<count($mDatos); $i++) {
                if(($nPosYFin - $nPosY) <= 0){
                  $pdf->AddPage();
                  $nPosY = 32;
                } 

                $n = 0;

                $mRetenciones = array();
                $mRetenciones = $mDatos[$i]['retencio'];

                if (count($mRetenciones) > 0) {
                  $n += (4*count($mRetenciones));
                }

                $n = ($n != 0)?$n:4;

                /**
                 * GPOS-1792
                 * Para aduanera se debe mostrar el pedido en la columna del DO
                 */
                $vNitCertificados = explode(",", $vSysStr['aduanera_nit_mostrar_pedido_certificado_pagos_terceros']);//Nit a los que se le imprimie el pedido en aduanera
                if(in_array($mDatos[$i]['cliidxxx'], $vNitCertificados) == true) {
                  $mDatos[$i]['docidxxx'] = substr(trim($mDatos[$i]['docidxxx']." ".$mDatos[$i]['docpedxx']),0,20);
                }

                $pdf->SetFont('verdana','',6);
                $pdf->setXY(5,$nPosY);
                $pdf->Cell(28,4,$mDatos[$i]['docidxxx'],0,0,'L');
                $pdf->Rect(5,$nPosY,28,$n);

                $vNitsCli = explode(",",$vSysStr['siacosia_incluir_prefijo_certificado_pcc']);
                if (($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") && in_array($mDatos[$i]['cliidxxx'], $vNitsCli)) {
                  // Se obtiene el prefijo de la factura
                  $vResPref = explode("~", $mDatos[$i]['residxxx']);

                  $pdf->setXY(33,$nPosY);
                  $pdf->Cell(15,4, substr($vResPref[1] . "-" . $mDatos[$i]['facturax'],0,10),0,0,'C');
                  $pdf->Rect(33,$nPosY,15,$n);
                } else {
                  $pdf->setXY(33,$nPosY);
                  $pdf->Cell(15,4,substr($mDatos[$i]['facturax'],0,10),0,0,'C');
                  $pdf->Rect(33,$nPosY,15,$n);
                }

                $pdf->setXY(48,$nPosY);
                $pdf->Cell(15,4,trim($mDatos[$i]['teridxxx']),0,0,'L');
                $pdf->Rect(48,$nPosY,15,$n);

                $pdf->setXY(63,$nPosY);
                $pdf->Cell(30,4,substr(trim($mDatos[$i]['ternomxx']),0,20),0,0,'L');
                $pdf->Rect(63,$nPosY,30,$n);

                $pdf->setXY(93,$nPosY);
                $pdf->Cell(20,4,$mDatos[$i]['document'],0,0,'C');
                $pdf->Rect(93,$nPosY,20,$n);

                $pdf->setXY(113,$nPosY);
                $pdf->Cell(15,4,$mDatos[$i]['comfecxx'],0,0,'C');
                $pdf->Rect(113,$nPosY,15,$n);

                $pdf->setXY(128,$nPosY);
                $pdf->Cell(29,4,substr(trim($mDatos[$i]['concepto']),0,20),0,0,'L');
                $pdf->Rect(128,$nPosY,29,$n);

                $pdf->setXY(157,$nPosY);

                $nDec = (strpos(($mDatos[$i]['costoxxx']+0),'.') > 0) ? 2 : 0;
                $pdf->Cell(18,4,number_format($mDatos[$i]['costoxxx'],$nDec,',','.'),0,0,'R');
                $pdf->Rect(157,$nPosY,18,$n);

                $pdf->setXY(175,$nPosY);
                $nDec = (strpos(($mDatos[$i]['ivaxxxxx']+0),'.') > 0) ? 2 : 0;
                $pdf->Cell(15,4,number_format($mDatos[$i]['ivaxxxxx'],$nDec,',','.'),0,0,'R');
                $pdf->Rect(175,$nPosY,15,$n);

                $pdf->setXY(190,$nPosY);
                $nDec = (strpos(($mDatos[$i]['totalxxx']+0),'.') > 0) ? 2 : 0;
                $pdf->Cell(20,4,number_format($mDatos[$i]['totalxxx'],$nDec,',','.'),0,0,'R');
                $pdf->Rect(190,$nPosY,20,$n);

                if (count($mRetenciones) > 0) {
                  $nPosY2 = $nPosY;
    
                  for($y = 0; $y < count($mRetenciones); $y++){
                    $pdf->setXY(210,$nPosY2);
                    $pdf->Cell(15,4,$mRetenciones[$y]['retenxxx'],0,0,'L');
                    $pdf->Rect(210,$nPosY2,15,4);
    
                    $pdf->setXY(225,$nPosY2);
                    $nDec = (strpos(($mRetenciones[$y]['comvlr01']+0),'.') > 0) ? 2 : 0;
                    $pdf->Cell(20,4,number_format($mRetenciones[$y]['comvlr01'],$nDec,',','.'),0,0,'R');
                    $pdf->Rect(225,$nPosY2,20,4);
    
                    $pdf->setXY(245,$nPosY2);
                    $pdf->Cell(10,4,number_format($mRetenciones[$y]['pucretxx'],3,',','.'),0,0,'C');
                    $pdf->Rect(245,$nPosY2,10,4);
    
                    $nRetencion = round($mRetenciones[$y]['comvlrxx']);
                    if($mRetenciones[$y]['retenxxx'] == 'ReteCree'){
                      $nTotRCre += $nRetencion;
                    }
                    if($mRetenciones[$y]['retenxxx'] == 'Retefuente'){
                      $nTotRfte += $nRetencion;
                    }
                    if($mRetenciones[$y]['retenxxx'] == 'ReteIva'){
                      $nTotRIva += $nRetencion;
                    }
                    if($mRetenciones[$y]['retenxxx'] == 'ReteIca'){
                      $nTotRIca += $nRetencion;
                    }
    
                    $pdf->setXY(255,$nPosY2);
                    $nDec = (strpos(($nRetencion+0),'.') > 0) ? 2 : 0;
                    $pdf->Cell(20,4,number_format($nRetencion,$nDec,',','.'),0,0,'R');
                    $pdf->Rect(255,$nPosY2,20,4);
                    $nPosY2 += 4;
                  }
                  $nPosY = $nPosY2-4;
                }else{
                  $pdf->setXY(210,$nPosY);
                  $pdf->Cell(15,4,"",0,0,'L');
                  $pdf->Rect(210,$nPosY,15,4);
    
                  $pdf->setXY(225,$nPosY);
                  $pdf->Cell(20,4,"",0,0,'R');
                  $pdf->Rect(225,$nPosY,20,4);
    
                  $pdf->setXY(245,$nPosY);
                  $pdf->Cell(10,4,"",0,0,'C');
                  $pdf->Rect(245,$nPosY,10,4);
    
                  $pdf->setXY(255,$nPosY);
                  $pdf->Cell(20,4,"",0,0,'R');
                  $pdf->Rect(255,$nPosY,20,4);
                }
    
                $nPosY += 4;
    
                $nTotCos += $mDatos[$i]['costoxxx'];
                $nTotIva += $mDatos[$i]['ivaxxxxx'];
                $nTotFac += $mDatos[$i]['totalxxx'];
              }
            break;   
          }
          //Fin Pinto el detalle de los Items del Certificado

    	    $nBan = 0;
    	  	$nPosY -= 2;
    	  	$pdf->setXY(5,$nPosY);
    	  	$pdf->SetFont('verdanab','',6);
    	  	$pdf->Cell(152,10,"TOTAL PAGOS A TERCEROS",0,0,'C');
    	  	$pdf->Rect(5,$nPosY+2,152,6);
    	  	$pdf->setXY(157,$nPosY);
    	  	$pdf->SetFont('verdanab','',6);
          $nDec = (strpos(($nTotCos+0),'.') > 0) ? 2 : 0;
          $pdf->Cell(18,10,"$".number_format($nTotCos,$nDec,',','.'),0,0,'R');
    	  	$pdf->Rect(157,$nPosY+2,18,6);
    	  	$pdf->setXY(175,$nPosY);
    	  	$pdf->SetFont('verdanab','',6);
          $nDec = (strpos(($nTotIva+0),'.') > 0) ? 2 : 0;
    	  	$pdf->Cell(15,10,"$".number_format($nTotIva,$nDec,',','.'),0,0,'R');
    	  	$pdf->Rect(175,$nPosY+2,15,6);
    	  	$pdf->setXY(190,$nPosY);
    	  	$pdf->SetFont('verdanab','',6);
          $nDec = (strpos(($nTotFac+0),'.') > 0) ? 2 : 0;
    	  	$pdf->Cell(20,10,"$".number_format($nTotFac,$nDec,',','.'),0,0,'R');
    	  	$pdf->Rect(190,$nPosY+2,20,6);
          $pdf->Rect(210,$nPosY+2,65,6);
          $nPosY += 10;

    	  	if($nTotRfte != 0){
            switch($cAlfa){
              case "ROLDANLO":
              case "DEROLDANLO":
              case "TEROLDANLO":
                if($pdf->getY() > 136){
                  $pdf->AddPage();
                  $nPosY = 40;
                }
              break;
              default:
                if(($nPosYFin - $nPosY) <= 0){
                  $pdf->AddPage();
                  $nPosY = 30;
                } 
              break;
            }

    	    	$pdf->setXY(5,$nPosY);
    	    	$pdf->SetFont('verdanab','',6);
    	   		$pdf->Cell(240,10,utf8_decode("TOTAL RETENCIÓN EN LA FUENTE"),0,0,'L');
    	    	$pdf->Rect(5,$nPosY+2,240,6);
    	    	$pdf->setXY(245,$nPosY);
    	    	$pdf->SetFont('verdanab','',6);
            $nDec = (strpos(($nTotRfte+0),'.') > 0) ? 2 : 0;
    	    	$pdf->Cell(30,10,"$".number_format($nTotRfte,$nDec,',','.'),0,0,'R');
    	    	$pdf->Rect(245,$nPosY+2,30,6);
    	    	$nPosY += 6;
    	  	}

          if($nTotRCre != 0){
            switch($cAlfa){
              case "ROLDANLO":
              case "DEROLDANLO":
              case "TEROLDANLO":
                if($pdf->getY() > 136){
                  $pdf->AddPage();
                  $nPosY = 40;
                }
              break;
              default:
                if(($nPosYFin - $nPosY) <= 0){
                  $pdf->AddPage();
                  $nPosY = 30;
                } 
              break;
            }

    	  		$pdf->setXY(5,$nPosY);
    	  		$pdf->SetFont('verdanab','',6);
    	  		$pdf->Cell(240,10,utf8_decode("TOTAL RETENCIÓN CREE"),0,0,'L');
    	  		$pdf->Rect(5,$nPosY+2,240,6);
    	  		$pdf->setXY(245,$nPosY);
    	  		$pdf->SetFont('verdanab','',6);
            $nDec = (strpos(($nTotRCre+0),'.') > 0) ? 2 : 0;
    	  		$pdf->Cell(30,10,"$".number_format($nTotRCre,$nDec,',','.'),0,0,'R');
    	  		$pdf->Rect(245,$nPosY+2,30,6);
    	  		$nPosY += 6;
    	  	}

    	  	if($nTotRIva != 0){
            switch($cAlfa){
              case "ROLDANLO":
              case "DEROLDANLO":
              case "TEROLDANLO":
                if($pdf->getY() > 136){
                  $pdf->AddPage();
                  $nPosY = 40;
                }
              break;
              default:
                if(($nPosYFin - $nPosY) <= 0){
                  $pdf->AddPage();
                  $nPosY = 30;
                } 
              break;
            }

    	    	$pdf->setXY(5,$nPosY);
    	    	$pdf->SetFont('verdanab','',6);
    	    	$pdf->Cell(240,10,utf8_decode("TOTAL RETENCIÓN IVA"),0,0,'L');
    	    	$pdf->Rect(5,$nPosY+2,240,6);
    	    	$pdf->setXY(245,$nPosY);
    	    	$pdf->SetFont('verdanab','',6);
            $nDec = (strpos(($nTotRIva+0),'.') > 0) ? 2 : 0;
    	    	$pdf->Cell(30,10,"$".number_format($nTotRIva,$nDec,',','.'),0,0,'R');
    	    	$pdf->Rect(245,$nPosY+2,30,6);
    	    	$nPosY += 6;
          }

    	  	if($nTotRIca != 0){
            switch($cAlfa){
              case "ROLDANLO":
              case "DEROLDANLO":
              case "TEROLDANLO":
                if($pdf->getY() > 136){
                  $pdf->AddPage();
                  $nPosY = 40;
                }
              break;
              default:
                if(($nPosYFin - $nPosY) <= 0){
                  $pdf->AddPage();
                  $nPosY = 30;
                }
              break;
            }

    	    	$pdf->setXY(5,$nPosY);
    	    	$pdf->SetFont('verdanab','',6);
    	    	$pdf->Cell(240,10,utf8_decode("TOTAL RETENCIÓN ICA"),0,0,'L');
    	    	$pdf->Rect(5,$nPosY+2,240,6);
    	    	$pdf->setXY(245,$nPosY);
    	    	$pdf->SetFont('verdanab','',6);
            $nDec = (strpos(($nTotRIca+0),'.') > 0) ? 2 : 0;
    	    	$pdf->Cell(30,10,"$".number_format($nTotRIca,$nDec,',','.'),0,0,'R');
    	    	$pdf->Rect(245,$nPosY+2,30,6);
    	    	$nPosY += 6;
    	  	}

          $nPosY += 5;
    	  	if(($nPosYFin - $nPosY) <= 0){
    	    	$pdf->AddPage();
    	    	$nPosY = 40;
    	  	}

          if ($cAlfa == "FEDEXEXP" || $cAlfa == "DEFEDEXEXP" || $cAlfa == "TEFEDEXEXP") {
            $cLeyenda3  = "Actuando en mi calidad de Contador Público, Rafael Ricardo Buitrago Naranjo con Cédula de ciudadanía 80.196.154 de Bogotá, Tarjeta Profesional 179422-T, ";
					  $cLeyenda3 .= "en representación de la empresa AGENCIA DE ADUANAS FEDEX EXPRESS COLOMBIA S.A.S. NIVEL 2 con NIT: 901.106.968-9 quien actúa como mandatario, suscribo la presente certificación.";

            $pdf->setXY(5,$nPosY-2);
            $pdf->SetFont('verdana','',8);
            $pdf->MultiCell(265,4,utf8_decode($cLeyenda3),0,'J');
            $nPosY += 14;
          }

    	  	//Para imprimir la leyenda de ciaco
    	  	switch($cAlfa){
    	  		case "SIACOSIA":
    	  		case "TESIACOSIP":
    	  		case "DESIACOSIP":

    	  			//22 lineas para Firma
    	  			if($nPosY > 163){
    	  				$pdf->AddPage();
    	  				$nPosY = 30;
    	  			}

    	  			//Introduccion para siaco
    	  			$cLeyenda= utf8_decode("La presente certificacion se expide a los "). f_FormatFecActa(date('Y-m-d'));
    	  			$pdf->SetFont('verdana','',10);
    	  			$pdf->setXY(10,$nPosY);
    	  			$pdf->MultiCell(260,5,$cLeyenda,0,'J');

						  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/firma_revisor_fiscal_certificados_siaco.jpg',21,$nPosY+4,48,28);

    	  			$cLeyenda1="Jose Wilson Gonzalez Marin \n";
    	  			$cLeyenda1.="S. Revisor Fiscal \n";
    	  			$cLeyenda1.="TP-42796-T";
    	  			$pdf->SetFont('verdanab','',10);
    	  			$pdf->Line(10,$nPosY+29,80,$nPosY+29);
    	  			$pdf->setXY(10,$nPosY+30);
    	  			$pdf->MultiCell(260,5,$cLeyenda1,0,'J');
    					$pdf->SetY(-10);
    					$pdf->SetFont('verdana','',6);
    					$pdf->Cell(0,5,'PAGINA '.$pdf->PageNo().' DE {nb}',0,0,'C');

    	  			//fin
    	  		break;
    	  		case "ROLDANLO":
    	  		case "DEROLDANLO":
    	  		case "TEROLDANLO":
    					if($nPosY > 163){
    	  				$pdf->AddPage();
    	  				$nPosY = 40;
    	  			}

    					$cLeyenda= utf8_decode("LAS RETENCIONES EN LA FUENTE SE APLICARON DE ACUERDO A LO ESTABLECIDO EN EL DECRETO 2775/83 ");
    	  			$pdf->SetFont('verdana','',7);
    	  			$pdf->setXY($nPosX,$nPosY);
    	  			$pdf->MultiCell(270,10,$cLeyenda,0,'C');
              if ($cRegEst != "PROVISIONAL") {
    	          $pdf->Image($cPath.$cPlesk_Skin_Directory.'/firmarevisorroldan.jpg',120,$nPosY+7,40,15);
              }

    					$cLeyenda1="JONATÁN DAVID VANEGAS MARÍN \n";
    	  			$pdf->SetFont('verdanab','',8);
    	  			$pdf->setXY($nPosX,$nPosY+21);
              $pdf->MultiCell(270,5,utf8_decode($cLeyenda1),0,'C');

              $cLeyenda1 ="REVISOR FISCAL\n";              
              $cLeyenda1.="C.C. No. 1.026.277.610\n";
              $cLeyenda1.="TP-200295-T\n";
              $cLeyenda1.="Miembro de O.G.C. Contadores Asociados SAS.";
              $pdf->SetFont('verdana','',8);
              $pdf->setXY($nPosX,$nPosY+26);
              $pdf->MultiCell(270,5,$cLeyenda1,0,'C');

              $nPosY += 41;
    	  			$pdf->SetFont('verdana','',7);
    	  			$pdf->setXY($nPosX,$nPosY);
    	  			$pdf->Cell(40,5,utf8_decode("CON VALIDEZ DE UNA FIRMA ELECTRÓNICA"),0,0,'L');

    				break;
            case "GRUMALCO":
            case "DEGRUMALCO":
            case "TEGRUMALCO":
              $nPosY += 3;
              $nPosYCon = (trim($vSysStr['financiero_contador_certificado_de_pagos_a_terceros'])       == "") ? $nPosY : $nPosY+4;
              $nPosYRev = (trim($vSysStr['financiero_revisor_fiscal_certificado_de_pagos_a_terceros']) == "") ? $nPosY : $nPosY+4;
              // $pdf->Line(5,$nPosY+2,80,$nPosY+2);
              // $pdf->setXY(5,$nPosY+1);
              // // $pdf->SetFont('verdanab','',6);
              // // $pdf->Cell(240,10,$vSysStr['financiero_contador_certificado_de_pagos_a_terceros'],0,0,'L');
              // $pdf->setXY(5,$nPosYCon);
              // // $pdf->SetFont('verdanab','',6);
              // // $pdf->Cell(240,10,"CONTADOR",0,0,'L');
              // // $pdf->Line(180,$nPosY+2,260,$nPosY+2);
              // $pdf->setXY(180,$nPosY+1);
              // $pdf->SetFont('verdanab','',6);
              // $pdf->Cell(240,10,$vSysStr['financiero_revisor_fiscal_certificado_de_pagos_a_terceros'],0,0,'L');
              // $pdf->setXY(180,$nPosYRev);
              // $pdf->SetFont('verdanab','',6);
              // $pdf->Cell(240,10,"REVISOR FISCAL ",0,0,'L');
              // $pdf->Image($cPath.$cPlesk_Skin_Directory.'/firma_revisor_fiscal_malco.jpg',5,$nPosYCon+1,84,25);
              //FOOTER
              $pdf->SetY(-10);
              $pdf->SetFont('verdana','',6);
              $pdf->Cell(0,5,'PAGINA '.$pdf->PageNo().' DE {nb}',0,0,'C');
            break;
            case "DEETRANSPT":
            case "TEETRANSPT":
            case "ETRANSPT":
              $nPosY += 5;
              $nPosYCon = (trim($vSysStr['financiero_contador_certificado_de_pagos_a_terceros'])       == "") ? $nPosY : $nPosY+4;
              $nPosYRev = (trim($vSysStr['financiero_revisor_fiscal_certificado_de_pagos_a_terceros']) == "") ? $nPosY : $nPosY+4;

              $nPosYFirma = (trim($vSysStr['financiero_revisor_fiscal_certificado_de_pagos_a_terceros']) == "") ? $nPosYRev-11 : $nPosYRev-15;
              $pdf->Image($cPath.$cPlesk_Skin_Directory.'/revisorfiscal_etranspt.jpg',197,$nPosYFirma,45,20);

              $pdf->Line(5,$nPosY+2,80,$nPosY+2);
              $pdf->setXY(5,$nPosY+1);
              $pdf->SetFont('verdanab','',6);
              $pdf->Cell(240,10,$vSysStr['financiero_contador_certificado_de_pagos_a_terceros'],0,0,'L');
              $pdf->setXY(5,$nPosYCon);
              $pdf->SetFont('verdanab','',6);
              $pdf->Cell(240,10,"CONTADOR",0,0,'L');
              $pdf->Line(180,$nPosY+2,260,$nPosY+2);
              $pdf->setXY(180,$nPosY+1);
              $pdf->SetFont('verdanab','',6);
              $pdf->Cell(240,10,$vSysStr['financiero_revisor_fiscal_certificado_de_pagos_a_terceros'],0,0,'L');
              $pdf->setXY(180,$nPosYRev);
              $pdf->SetFont('verdanab','',6);
              $pdf->Cell(240,10,"REVISOR FISCAL",0,0,'L');

              $pdf->SetY(-10);
    					$pdf->SetFont('verdana','',6);
    					$pdf->Cell(0,5,'PAGINA '.$pdf->PageNo().' DE {nb}',0,0,'C');
            break;
            case "DECOLMASXX":
            case "TECOLMASXX":
            case "COLMASXX":
              $nPosY += 3;
              $nPosYCon = (trim($vSysStr['financiero_contador_certificado_de_pagos_a_terceros'])       == "") ? $nPosY : $nPosY+4;

              $pdf->Line(5,$nPosY+2,80,$nPosY+2);
              $pdf->setXY(5,$nPosY+1);
              $pdf->SetFont('verdanab','',6);
              $pdf->Cell(240,10,$vSysStr['financiero_contador_certificado_de_pagos_a_terceros'],0,0,'L');
              $pdf->setXY(5,$nPosYCon);
              $pdf->SetFont('verdanab','',6);
              $pdf->Cell(240,10,"CONTADOR",0,0,'L');
              $pdf->Line(180,$nPosY+2,260,$nPosY+2);
              $pdf->setXY(180,$nPosY);
              $pdf->SetFont('verdanab','',6);
              $pdf->Cell(240,10,"REVISOR FISCAL",0,0,'L');

              $pdf->SetY(-10);
              $pdf->SetFont('verdana','',6);
              $pdf->Cell(0,5,'PAGINA '.$pdf->PageNo().' DE {nb}',0,0,'C');
            break;
            case "FEDEXEXP":
            case "DEFEDEXEXP":
            case "TEFEDEXEXP":
    	  			if($nPosY > 169){
    	  				$pdf->AddPage();
    	  				$nPosY = 30;
    	  			}

              $nPosYCon = $nPosY+12;
              $nPosYRev = $nPosY+12;
  
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/firma_contador_fedex.jpg',5,$nPosYCon-14,45);
              $pdf->Line(5,$nPosYCon+2,80,$nPosYCon+2);
              $pdf->setXY(5,$nPosYCon);
              $pdf->SetFont('verdanab','',6);
              $pdf->Cell(240,10,"CONTADOR",0,0,'L');
              $pdf->setXY(5,$nPosYCon+4);
              $pdf->SetFont('verdana','',6);
              $pdf->Cell(240,10,"Rafael Ricardo Buitrago Naranjo",0,0,'L');
              $pdf->setXY(5,$nPosYCon+7);
              $pdf->Cell(240,10,"CC. 80196154",0,0,'L');
              $pdf->setXY(5,$nPosYCon+10);
              $pdf->Cell(240,10,"Tarjeta Profesional 179422-T",0,0,'L');
  
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/firma_representante_legal_fedex.jpg',178,$nPosYRev-17,35);
              $pdf->Line(180,$nPosYRev+2,260,$nPosYRev+2);
              $pdf->setXY(180,$nPosYRev);
              $pdf->SetFont('verdanab','',6);
              $pdf->Cell(240,10,"REPRESENTANTE LEGAL",0,0,'L');
              $pdf->setXY(180,$nPosYRev+4);
              $pdf->SetFont('verdana','',6);
              $pdf->Cell(240,10,"Eduardo Alfonso Garrido",0,0,'L');
              $pdf->setXY(180,$nPosYRev+7);
              $pdf->Cell(240,10,"CC. 8532627",0,0,'L');
            break;
            case "FENIXSAS":
            case "DEFENIXSAS":
            case "TEFENIXSAS":

              $nPosY += 16;
              $nPosYCon = (trim($vSysStr['financiero_contador_certificado_de_pagos_a_terceros'])       == "") ? $nPosY : $nPosY+4;
              $nPosYRev = (trim($vSysStr['financiero_revisor_fiscal_certificado_de_pagos_a_terceros']) == "") ? $nPosY : $nPosY+4;

              $pdf->Line(5,$nPosY+2,80,$nPosY+2);
              $pdf->setXY(5,$nPosY+1);
              $pdf->SetFont('verdanab','',6);
              $pdf->Cell(240,10,$vSysStr['financiero_contador_certificado_de_pagos_a_terceros'],0,0,'L');
              $pdf->setXY(5,$nPosYCon);
              $pdf->SetFont('verdanab','',6);
              $pdf->Cell(240,10,"CONTADOR",0,0,'L');
              $pdf->Line(180,$nPosY+2,260,$nPosY+2);
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/firma_revisor_fiscal_fenix.jpg',205,$nPosY-15,35);
              $pdf->setXY(180,$nPosY+1);
              $pdf->SetFont('verdanab','',6);
              $pdf->Cell(240,10,$vSysStr['financiero_revisor_fiscal_certificado_de_pagos_a_terceros'],0,0,'L');
              $pdf->setXY(180,$nPosYRev);
              $pdf->SetFont('verdanab','',6);
              $pdf->Cell(240,10,"REVISOR FISCAL",0,0,'L');

              $pdf->SetY(-10);
              $pdf->SetFont('verdana','',6);
              $pdf->Cell(0,5,'PAGINA '.$pdf->PageNo().' DE {nb}',0,0,'C');
            break;
            case "MIRCANAX":
            case "DEMIRCANAX":
            case "TEMIRCANAX":

              $nPosY += 13;

              $pdf->Line(180,$nPosY+2,260,$nPosY+2);
              $pdf->setXY(180,$nPosY+1);
              $pdf->SetFont('verdanab','',6);
              $pdf->Cell(240,10,"NELSON A TRIANA CERVERA",0,0,'L');
              $pdf->setXY(180,$nPosY+4);
              $pdf->Cell(240,10,"REVISOR FISCAL",0,0,'L');
              $pdf->setXY(180,$nPosY+7);
              $pdf->Cell(240,10,"CC 14.228.600",0,0,'L');
              $pdf->setXY(180,$nPosY+10);
              $pdf->Cell(240,10,"TP 40590-T",0,0,'L');

              $pdf->SetY(-10);
              $pdf->SetFont('verdana','',6);
              $pdf->Cell(0,5,'PAGINA '.$pdf->PageNo().' DE {nb}',0,0,'C');
            break;
            default:

              $nPosY += 5;
              $nPosYCon = (trim($vSysStr['financiero_contador_certificado_de_pagos_a_terceros'])       == "") ? $nPosY : $nPosY+4;
              $nPosYRev = (trim($vSysStr['financiero_revisor_fiscal_certificado_de_pagos_a_terceros']) == "") ? $nPosY : $nPosY+4;

              $pdf->Line(5,$nPosY+2,80,$nPosY+2);
              $pdf->setXY(5,$nPosY+1);
              $pdf->SetFont('verdanab','',6);
              $pdf->Cell(240,10,$vSysStr['financiero_contador_certificado_de_pagos_a_terceros'],0,0,'L');
              $pdf->setXY(5,$nPosYCon);
              $pdf->SetFont('verdanab','',6);
              $pdf->Cell(240,10,"CONTADOR",0,0,'L');
              $pdf->Line(180,$nPosY+2,260,$nPosY+2);
              $pdf->setXY(180,$nPosY+1);
              $pdf->SetFont('verdanab','',6);
              $pdf->Cell(240,10,$vSysStr['financiero_revisor_fiscal_certificado_de_pagos_a_terceros'],0,0,'L');
              $pdf->setXY(180,$nPosYRev);
              $pdf->SetFont('verdanab','',6);
              $pdf->Cell(240,10,"REVISOR FISCAL",0,0,'L');

              $pdf->SetY(-10);
    					$pdf->SetFont('verdana','',6);
    					$pdf->Cell(0,5,'PAGINA '.$pdf->PageNo().' DE {nb}',0,0,'C');
    	  		break;
    	  	}
    		break;///fin break
      }
      
      if ($_SERVER["SERVER_PORT"] != "") {
        $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
      } else {
        $cFile = $OPENINIT['pathdr']."/opencomex/".$vSysStr['system_download_directory']."/pdf_".date("YmdHis").".pdf";
      }

      
      $pdf->Output($cFile);

    	if (file_exists($cFile)){
        chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
      } else {
        $nSwitch = 1;
        $mReturn[count($mReturn)] = "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.";
      }

      if ($nSwitch == 0) {
        $mReturn[0] = "true";
        $mReturn[1] = $cFile;
      } else {
        $mReturn[0] = "false";
      }

      return $mReturn;
    } ## function fnConsultarDatos($pArrayParametros) { ##

    /**
     * Metodo que genera el pdf del certificado de mandato Notas
     */
    function fnGenerarCertificadoMandatoNotas($pArrayParametros) {

      global $xConexion01; global $cAlfa; global $vSysStr; global $kUser; global $nBan; global $cPlesk_Skin_Directory; global $cPath; global $OPENINIT;

      /**
       *Recibe como Parametro una Matriz con las siguientes posiciones:
       *
       *$pArrayParametros['DATOSXXX'] Data del certificado de origen
       *$pArrayParametros['RESDATXX'] Resolucion data
       *$pArrayParametros['RESIDXXX'] Id de la resolucion
       *$pArrayParametros['COCDATXX'] Data cabecera del comprobante.
       *$pArrayParametros['TIPOXXXX'] Certificado de mandata o intermediacion
       *$pArrayParametros['TIPONOTA'] Tipo de Nota (NC/ND)
       */

      /**
       * Variables para reemplazar caracteres especiales
       * @var array
       */
      $cBuscar = array('"',"'",chr(13),chr(10),chr(27),chr(9));
      $cReempl = array('\"',"\'"," "," "," "," ");

      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var number
       */
      $nSwitch = 0;

      /**
       * Variable para hacer el retorno.
       * @var array
       */
      $mReturn    = array();
      $mReturn[0] = "";

      /**
       * Instanciando Objetos para el Guardado de Errores
       */
      $objGuardarError = new cEstructurasCertificadoMandato();

      /*echo "<pre>";
      print_r($pArrayParametros);
      echo "</pre>";*/

      $mDatos    = $pArrayParametros['DATOSXXX'];
      $vResDat   = $pArrayParametros['RESDATXX'];
      $vResId    = $pArrayParametros['RESIDXXX'];
      $vCocDat   = $pArrayParametros['COCDATXX'];
      $gTipo     = $pArrayParametros['TIPOXXXX'];
      $gTipoNota = $pArrayParametros['TIPONOTA'];

      // $mDatos = array_merge($mDatos,$mDatos);
      // $mDatos = array_merge($mDatos,$mDatos);
      // $mDatos = array_merge($mDatos,$mDatos);

      /*** Traigo Ciudad del Facturado A ***/
      $qCiuDat  = "SELECT CIUDESXX ";
      $qCiuDat .= "FROM $cAlfa.SIAI0055 ";
      $qCiuDat .= "WHERE ";
      $qCiuDat .= "$cAlfa.SIAI0055.PAIIDXXX = \"{$vCocDat['PAIIDXXX']}\" AND ";
      $qCiuDat .= "$cAlfa.SIAI0055.DEPIDXXX = \"{$vCocDat['DEPIDXXX']}\" AND ";
      $qCiuDat .= "$cAlfa.SIAI0055.CIUIDXXX = \"{$vCocDat['CIUIDXXX']}\" AND ";
      $qCiuDat .= "$cAlfa.SIAI0055.REGESTXX = \"ACTIVO\" ";
      $xCiuDat  = f_MySql("SELECT","",$qCiuDat,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qCiuDat."~".mysql_num_rows($xCiuDat));
      $vCiuDes = mysql_fetch_array($xCiuDat);
      /*** Fin Traigo Ciudad del Facturado A ***/

      /*** Creación del PDF. ***/
      $pdf = new PDF('L','mm','Letter');

      $pdf->AddFont('verdana','','verdana.php');
    	$pdf->AddFont('verdanab','','verdanab.php');
    	$pdf->SetFont('verdana','',8);
    	$pdf->AliasNbPages();
    	$pdf->SetMargins(0,0,0);
    	$pdf->SetAutoPageBreak(false);

    	/*** Agrego una nueva pagina ***/
    	$pdf->AddPage();

      $nPosY = 10;  /*** Posicion en Y ***/
    	$nBan  = 1;
      $nPosX = 5;
      $nPosYFin = 185;

      switch ($cAlfa) {
    		default:
          switch($cAlfa){
            default:
              $pdf->SetFont('verdanab','',9);
              $pdf->setXY($nPosX,$nPosY);
              if ($gTipo == "CERTIFICADOINT") {
                $pdf->Cell(270,10,"CERTIFICADO DE INTERMEDIACION DE PAGO",0,0,'C');
              } else {
                $pdf->Cell(270,10,"CERTIFICADO DE RETENCIONES POR PAGOS A TERCEROS",0,0,'C');
              }
            break;
          }

          switch($cAlfa){
            default:
              $nPosY += 12;
            break;
          }

    			//Datos del cliente
    	    switch($cAlfa){
    	    	default:
    	    		$pdf->setXY(5,$nPosY);
    			    $pdf->SetFont('verdanab','',7);
    			    $pdf->Cell(12,10,"Cliente:",0,0,'L');
    			    $pdf->SetFont('verdana','',7);
    			    $pdf->Cell(168,10,$vCocDat['CLINOMXX'],0,0,'L');
    			    $pdf->SetFont('verdanab','',7);
    			    $pdf->Cell(10,10,"NIT: ",0,0,'L');
    			    $pdf->SetFont('verdana','',7);
    			    $pdf->Cell(30,10,$vCocDat['teridxxx']."-".f_Digito_Verificacion($vCocDat['teridxxx']),0,0,'L');
    			    $pdf->SetFont('verdanab','',7);
    			    $pdf->Cell(30,10,"FECHA IMPRESION : ",0,0,'L');
    			    $pdf->SetFont('verdana','',7);
    			    $pdf->Cell(20,10,date('Y-m-d'),0,0,'R');
    			    $nPosY += 8;
    	    	break;
          }

    	    $pdf->setXY(5,$nPosY);
    		  $pdf->SetFont('verdanab','',6);
    		  $pdf->Cell(20,10,"DO",0,0,'C');
    		  $pdf->Rect(5,$nPosY+2,20,5);
    		  $pdf->setXY(25,$nPosY);
    		  $pdf->SetFont('verdanab','',6);
    		  $pdf->Cell(15,10,"DOC AFEC",0,0,'C');
    		  $pdf->Rect(25,$nPosY+2,15,5);

    		  $pdf->setXY(40,$nPosY);
    		  $pdf->SetFont('verdanab','',6);
    		  $pdf->Cell(13,10,$gTipoNota,0,0,'C');
    		  $pdf->Rect(40,$nPosY+2,13,5);
    		  $pdf->setXY(53,$nPosY);
    		  $pdf->SetFont('verdanab','',6);

    			switch($cAlfa){
            default:
        		  $pdf->Cell(15,10,"TERCERO",0,0,'C');
            break;
          }

    		  $pdf->Rect(53,$nPosY+2,15,5);
    		  $pdf->setXY(68,$nPosY);
    		  $pdf->SetFont('verdanab','',6);
    			switch($cAlfa){
            default:
        		  $pdf->Cell(30,10,"NOMBRE",0,0,'C');
            break;
          }

    		  $pdf->Rect(68,$nPosY+2,30,5);
    		  $pdf->setXY(98,$nPosY);
    		  $pdf->SetFont('verdanab','',6);
    			switch($cAlfa){
            default:
        		  $pdf->Cell(20,10,"DOCUMENTO",0,0,'C');
            break;
          }

    		  $pdf->Rect(98,$nPosY+2,20,5);
    		  $pdf->setXY(118,$nPosY);
    		  $pdf->SetFont('verdanab','',6);
    		  $pdf->Cell(15,10,"FECHA",0,0,'C');
    		  $pdf->Rect(118,$nPosY+2,15,5);
    		  $pdf->setXY(133,$nPosY);
    		  $pdf->SetFont('verdanab','',6);
    		  $pdf->Cell(27,10,"CONCEPTO",0,0,'C');
    		  $pdf->Rect(133,$nPosY+2,27,5);
    		  $pdf->setXY(160,$nPosY);
    		  $pdf->SetFont('verdanab','',6);

    		  switch($cAlfa){
            default:
        		 $pdf->Cell(15,10,"COSTO",0,0,'C');
            break;
          }

    		  $pdf->Rect(160,$nPosY+2,15,5);
    		  $pdf->setXY(175,$nPosY);
    		  $pdf->SetFont('verdanab','',6);
    		  $pdf->Cell(15,10,"IVA",0,0,'C');
    		  $pdf->Rect(175,$nPosY+2,15,5);
    		  $pdf->setXY(190,$nPosY);
    		  $pdf->SetFont('verdanab','',6);
    		  $pdf->Cell(20,10,"TOTAL",0,0,'C');
    		  $pdf->Rect(190,$nPosY+2,20,5);
    		  $pdf->setXY(210,$nPosY);
    		  $pdf->SetFont('verdanab','',6);
    		  $pdf->Cell(15,10,"TIPO",0,0,'C');
    		  $pdf->Rect(210,$nPosY+2,15,5);
    		  $pdf->setXY(225,$nPosY);
    		  $pdf->SetFont('verdanab','',6);
    		  $pdf->Cell(20,10,"VALOR BASE",0,0,'C');
    		  $pdf->Rect(225,$nPosY+2,20,5);
    		  $pdf->setXY(245,$nPosY);
    		  $pdf->SetFont('verdanab','',6);
    		  $pdf->Cell(10,10,"%",0,0,'C');
    		  $pdf->Rect(245,$nPosY+2,10,5);
    		  $pdf->setXY(255,$nPosY);
    		  $pdf->SetFont('verdanab','',6);
    		  $pdf->Cell(20,10,"VALOR",0,0,'C');
    		  $pdf->Rect(255,$nPosY+2,20,5);
    	    $nPosY += 7;

          
          for($i=0; $i<count($mDatos); $i++) {

            if(($nPosYFin - $nPosY) <= 0){
            	$pdf->AddPage();
              switch($cAlfa){
                default:
                  $nPosY = 32;
                break;
              }
            }

            $n = 0;

            $mRetenciones = array();
            $mRetenciones = $mDatos[$i]['retencio'];

            if (count($mRetenciones) > 0) {
            	$n += (4*count($mRetenciones));
            }

    	      $n = ($n <> 0)?$n:4;

            /**
             * GPOS-1792
             * Para aduanera se debe mostrar el pedido en la columna del DO
             */
            $vNitCertificados = explode(",", $vSysStr['aduanera_nit_mostrar_pedido_certificado_pagos_terceros']);//Nit a los que se le imprimie el pedido en aduanera
            if(in_array($mDatos[$i]['cliidxxx'], $vNitCertificados) == true) {
              $mDatos[$i]['docidxxx'] = substr(trim($mDatos[$i]['docidxxx']." ".$mDatos[$i]['docpedxx']),0,20);
            }

    	    	$pdf->SetFont('verdana','',6);
    	      $pdf->setXY(5,$nPosY);
    	      $pdf->Cell(20,4,$mDatos[$i]['docidxxx'],0,0,'L');
    	      $pdf->Rect(5,$nPosY,20,$n);

            $pdf->setXY(25,$nPosY);
            $pdf->Cell(15,4,$mDatos[$i]['docafexx'],0,0,'C');
            $pdf->Rect(25,$nPosY,15,$n);

    	      $pdf->setXY(40,$nPosY);
    	      $pdf->Cell(13,4,$mDatos[$i]['facturax'],0,0,'C');
            $pdf->Rect(40,$nPosY,13,$n);
            
    	      $pdf->setXY(53,$nPosY);
    	      $pdf->Cell(15,4,trim($mDatos[$i]['teridxxx']),0,0,'L');
    	      $pdf->Rect(53,$nPosY,15,$n);

    	      $pdf->setXY(68,$nPosY);
    	      $pdf->Cell(30,4,substr(trim($mDatos[$i]['ternomxx']),0,20),0,0,'L');
    	      $pdf->Rect(68,$nPosY,30,$n);

    	      $pdf->setXY(98,$nPosY);
    	      $pdf->Cell(20,4,$mDatos[$i]['document'],0,0,'C');
    	      $pdf->Rect(98,$nPosY,20,$n);

    	      $pdf->setXY(118,$nPosY);
    	      $pdf->Cell(15,4,$mDatos[$i]['comfecxx'],0,0,'C');
    	      $pdf->Rect(118,$nPosY,15,$n);

    	      $pdf->setXY(133,$nPosY);
    	      $pdf->Cell(27,4,substr(trim($mDatos[$i]['concepto']),0,18),0,0,'L');
    	      $pdf->Rect(133,$nPosY,27,$n);

    	      $pdf->setXY(160,$nPosY);
    	      $pdf->Cell(15,4,number_format($mDatos[$i]['costoxxx'],0,',','.'),0,0,'R');
    	      $pdf->Rect(160,$nPosY,15,$n);

    	      $pdf->setXY(175,$nPosY);
    	      $pdf->Cell(15,4,number_format($mDatos[$i]['ivaxxxxx'],0,',','.'),0,0,'R');
    	      $pdf->Rect(175,$nPosY,15,$n);

    	      $pdf->setXY(190,$nPosY);
    	      $pdf->Cell(20,4,number_format($mDatos[$i]['totalxxx'],0,',','.'),0,0,'R');
    	      $pdf->Rect(190,$nPosY,20,$n);


    	      if (count($mRetenciones) > 0) {
    	      	$nPosY2 = $nPosY;

    	        for ($y = 0; $y < count($mRetenciones); $y++){
    	        	$pdf->setXY(210,$nPosY2);
    	          $pdf->Cell(15,4,$mRetenciones[$y]['retenxxx'],0,0,'L');
    	          $pdf->Rect(210,$nPosY2,15,4);

    	          $pdf->setXY(225,$nPosY2);
    	          $pdf->Cell(20,4,number_format($mRetenciones[$y]['comvlr01'],0,',','.'),0,0,'R');
    	          $pdf->Rect(225,$nPosY2,20,4);

    	          $pdf->setXY(245,$nPosY2);
    	          $pdf->Cell(10,4,number_format($mRetenciones[$y]['pucretxx'],3,',','.'),0,0,'C');
    	          $pdf->Rect(245,$nPosY2,10,4);

    	          $nRetencion = round($mRetenciones[$y]['comvlrxx']);
    	          if($mRetenciones[$y]['retenxxx'] == 'ReteCree'){
    	          	$nTotRCre += $nRetencion;
    	          }
    	          if($mRetenciones[$y]['retenxxx'] == 'Retefuente'){
    							$nTotRfte += $nRetencion;
    						}
    	  				if($mRetenciones[$y]['retenxxx'] == 'ReteIva'){
    	  					$nTotRIva += $nRetencion;
    	  				}
    	  				if($mRetenciones[$y]['retenxxx'] == 'ReteIca'){
    	  					$nTotRIca += $nRetencion;
    	  				}

    	          $pdf->setXY(255,$nPosY2);
    	          $pdf->Cell(20,4,number_format($nRetencion,0,',','.'),0,0,'R');
    	          $pdf->Rect(255,$nPosY2,20,4);
    	          $nPosY2 += 4;
    	       	}
    	        $nPosY = $nPosY2-4;
    	      }else{
    	      	$pdf->setXY(210,$nPosY);
    	        $pdf->Cell(15,4,"",0,0,'L');
    	        $pdf->Rect(210,$nPosY,15,4);

    	        $pdf->setXY(225,$nPosY);
    	        $pdf->Cell(20,4,"",0,0,'R');
    	        $pdf->Rect(225,$nPosY,20,4);

    	        $pdf->setXY(245,$nPosY);
    	        $pdf->Cell(10,4,"",0,0,'C');
    	        $pdf->Rect(245,$nPosY,10,4);

    	        $pdf->setXY(255,$nPosY);
    	        $pdf->Cell(20,4,"",0,0,'R');
    	        $pdf->Rect(255,$nPosY,20,4);
    	      }

    	      $nPosY += 4;

    	      $nTotCos += $mDatos[$i]['costoxxx'];
    	      $nTotIva += $mDatos[$i]['ivaxxxxx'];
    	      $nTotFac += $mDatos[$i]['totalxxx'];
          }

    	    $nBan = 0;
    	  	$nPosY -= 2;
    	  	$pdf->setXY(5,$nPosY);
    	  	$pdf->SetFont('verdanab','',6);
    	  	$pdf->Cell(155,10,"TOTAL PAGOS A TERCEROS",0,0,'C');
    	  	$pdf->Rect(5,$nPosY+2,155,6);
    	  	$pdf->setXY(160,$nPosY);
    	  	$pdf->SetFont('verdanab','',6);
    	  	$pdf->Cell(15,10,number_format($nTotCos,0,',','.'),0,0,'R');
    	  	$pdf->Rect(160,$nPosY+2,15,6);
    	  	$pdf->setXY(175,$nPosY);
    	  	$pdf->SetFont('verdanab','',6);
    	  	$pdf->Cell(15,10,number_format($nTotIva,0,',','.'),0,0,'R');
    	  	$pdf->Rect(175,$nPosY+2,15,6);
    	  	$pdf->setXY(190,$nPosY);
    	  	$pdf->SetFont('verdanab','',6);
    	  	$pdf->Cell(20,10,number_format($nTotFac,0,',','.'),0,0,'R');
    	  	$pdf->Rect(190,$nPosY+2,20,6);
    	  	$pdf->Rect(210,$nPosY+2,65,6);
    	  	$nPosY += 10;

    	  	if(($nPosYFin - $nPosY) <= 0){
    	       $pdf->AddPage();
             switch($cAlfa){
               default:
                 $nPosY = 30;
               break;
             }
    	    }

    	  	if($nTotRfte != 0){
    	    	$pdf->setXY(5,$nPosY);
    	    	$pdf->SetFont('verdanab','',6);
    	   		$pdf->Cell(240,10,utf8_decode("TOTAL RETENCIÓN EN LA FUENTE"),0,0,'L');
    	    	$pdf->Rect(5,$nPosY+2,240,6);
    	    	$pdf->setXY(245,$nPosY);
    	    	$pdf->SetFont('verdanab','',6);
    	    	$pdf->Cell(30,10,number_format($nTotRfte,0,',','.'),0,0,'R');
    	    	$pdf->Rect(245,$nPosY+2,30,6);
    	    	$nPosY += 6;
    	  	}

    	  	if(($nPosYFin - $nPosY) <= 0){
    	  		$pdf->AddPage();
            switch($cAlfa){
              default:
                $nPosY = 30;
              break;
            }
    	  	}

    	  	if($nTotRCre != 0){
    	  		$pdf->setXY(5,$nPosY);
    	  		$pdf->SetFont('verdanab','',6);
    	  		$pdf->Cell(240,10,utf8_decode("TOTAL RETENCIÓN CREE"),0,0,'L');
    	  		$pdf->Rect(5,$nPosY+2,240,6);
    	  		$pdf->setXY(245,$nPosY);
    	  		$pdf->SetFont('verdanab','',6);
    	  		$pdf->Cell(30,10,number_format($nTotRCre,0,',','.'),0,0,'R');
    	  		$pdf->Rect(245,$nPosY+2,30,6);
    	  		$nPosY += 6;
    	  	}

    	  	if(($nPosYFin - $nPosY) <= 0){
    	    	$pdf->AddPage();
            switch($cAlfa){
              default:
                $nPosY = 30;
              break;
            }
    	  	}

    	  	if($nTotRIva != 0){
    	    	$pdf->setXY(5,$nPosY);
    	    	$pdf->SetFont('verdanab','',6);
    	    	$pdf->Cell(240,10,utf8_decode("TOTAL RETENCIÓN IVA"),0,0,'L');
    	    	$pdf->Rect(5,$nPosY+2,240,6);
    	    	$pdf->setXY(245,$nPosY);
    	    	$pdf->SetFont('verdanab','',6);
    	    	$pdf->Cell(30,10,number_format($nTotRIva,0,',','.'),0,0,'R');
    	    	$pdf->Rect(245,$nPosY+2,30,6);
    	    	$nPosY += 6;
    	  	}

    	  	if(($nPosYFin - $nPosY) <= 0){
    	  	  $pdf->AddPage();
    	  	  switch($cAlfa){
              default:
                $nPosY = 30;
              break;
            }
    	  	}

    	  	if($nTotRIca != 0){
    	    	$pdf->setXY(5,$nPosY);
    	    	$pdf->SetFont('verdanab','',6);
    	    	$pdf->Cell(240,10,utf8_decode("TOTAL RETENCIÓN ICA"),0,0,'L');
    	    	$pdf->Rect(5,$nPosY+2,240,6);
    	    	$pdf->setXY(245,$nPosY);
    	    	$pdf->SetFont('verdanab','',6);
    	    	$pdf->Cell(30,10,number_format($nTotRIca,0,',','.'),0,0,'R');
    	    	$pdf->Rect(245,$nPosY+2,30,6);
    	    	$nPosY += 6;
    	  	}

    	  	$nPosY += 10;
    	  	if(($nPosYFin - $nPosY) <= 0){
    	    	$pdf->AddPage();
    	    	$nPosY = 40;
    	  	}

    	  	//Para imprimir la leyenda de ciaco
    	  	switch($cAlfa){
            case "GRUMALCO":
            case "DEGRUMALCO":
            case "TEGRUMALCO":
              $nPosY += 3;
              $nPosYCon = (trim($vSysStr['financiero_contador_certificado_de_pagos_a_terceros'])       == "") ? $nPosY : $nPosY+4;
              $nPosYRev = (trim($vSysStr['financiero_revisor_fiscal_certificado_de_pagos_a_terceros']) == "") ? $nPosY : $nPosY+4;
              // $pdf->Line(5,$nPosY+2,80,$nPosY+2);
              // $pdf->setXY(5,$nPosY+1);
              // // $pdf->SetFont('verdanab','',6);
              // // $pdf->Cell(240,10,$vSysStr['financiero_contador_certificado_de_pagos_a_terceros'],0,0,'L');
              // $pdf->setXY(5,$nPosYCon);
              // // $pdf->SetFont('verdanab','',6);
              // // $pdf->Cell(240,10,"CONTADOR",0,0,'L');
              // // $pdf->Line(180,$nPosY+2,260,$nPosY+2);
              // $pdf->setXY(180,$nPosY+1);
              // $pdf->SetFont('verdanab','',6);
              // $pdf->Cell(240,10,$vSysStr['financiero_revisor_fiscal_certificado_de_pagos_a_terceros'],0,0,'L');
              // $pdf->setXY(180,$nPosYRev);
              // $pdf->SetFont('verdanab','',6);
              // $pdf->Cell(240,10,"REVISOR FISCAL ",0,0,'L');
              // $pdf->Image($cPath.$cPlesk_Skin_Directory.'/firma_revisor_fiscal_malco.jpg',5,$nPosYCon+1,84,25);
              //FOOTER
              $pdf->SetY(-10);
              $pdf->SetFont('verdana','',6);
              $pdf->Cell(0,5,'PAGINA '.$pdf->PageNo().' DE {nb}',0,0,'C');
            break;
    	  	}
    		break;///fin break
      }
      
      if ($_SERVER["SERVER_PORT"] != "") {
        $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
      } else {
        $cFile = $OPENINIT['pathdr']."/opencomex/".$vSysStr['system_download_directory']."/pdf_".date("YmdHis").".pdf";
      }

      
      $pdf->Output($cFile);

    	if (file_exists($cFile)){
        chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
      } else {
        $nSwitch = 1;
        $mReturn[count($mReturn)] = "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.";
      }

      if ($nSwitch == 0) {
        $mReturn[0] = "true";
        $mReturn[1] = $cFile;
      } else {
        $mReturn[0] = "false";
      }

      return $mReturn;
    } ## function fnGenerarCertificadoMandatoNotas($pArrayParametros) { ##
    
      //Funcion que retorna los contactos a quien debe enviarseles el correo
    function fnContactos($xTerId2) {
      global $cAlfa; global $xConexion01; global $vSysStr; global $cPath;

      //Buscado los destinarios del Correo
      //Si el Cliente del DO tiene parametrizados contactos
      //debe realizarse la busqueda de los contactos y enviar el correo
      $qContactos  = "SELECT ";
      $qContactos .= "$cAlfa.sys00122.conidxxx, ";
      $qContactos .= "$cAlfa.SIAI0150.CLIEMAXX AS cliemaxx  ";
      $qContactos .= "FROM $cAlfa.sys00122 ";
      $qContactos .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.sys00122.conidxxx = $cAlfa.SIAI0150.CLIIDXXX ";
      $qContactos .= "WHERE ";
      $qContactos .= "$cAlfa.sys00122.cliidxxx = \"$xTerId2\" AND  ";
      $qContactos .= "$cAlfa.sys00122.iacefxxx = \"SI\" AND  ";
      $qContactos .= "$cAlfa.sys00122.regestxx = \"ACTIVO\" AND  ";
      $qContactos .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" AND  ";
      $qContactos .= "$cAlfa.SIAI0150.CLIEMAXX != \"\"";
      $xContactos  = f_MySql("SELECT","",$qContactos,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qContactos."~".mysql_num_rows($xContactos));
      $vCorreos = array();
      while($xRC  = mysql_fetch_array($xContactos)) {
        if (in_array($xRC['cliemaxx'],$vCorreos) == false) {
          $vCorreos[count($vCorreos)] = $xRC['cliemaxx'];
        }
      }

      $qEmaAdi = "SELECT $cAlfa.SIAI0150.CLIPCECN ";
      $qEmaAdi .= "FROM $cAlfa.SIAI0150 ";
      $qEmaAdi .= "WHERE ";
      $qEmaAdi .= "$cAlfa.SIAI0150.CLIIDXXX = \"$xTerId2\" AND ";
      $qEmaAdi .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" LIMIT 0,1";
      $xEmaAdi = f_MySql("SELECT","",$qEmaAdi,$xConexion01,"");
      $vEmaAdi = mysql_fetch_array($xEmaAdi);

      $vCorreosAdicionales = explode(',',$vEmaAdi['CLIPCECN']);
      for ($i=0; $i <count($vCorreosAdicionales); $i++) {
        if ($vCorreosAdicionales[$i] != ''){
          if (in_array($vCorreosAdicionales[$i],$vCorreos) == false) {
            $vCorreos[count($vCorreos)] = trim($vCorreosAdicionales[$i]);
          }
        }
      }

      return $vCorreos;
    }

    /** Funcion para el envio de la factura por correo electronico, recibe como parametros
     * @param $xFile    -> Ruta del archivo de la factura
     * @param $xComCsc  -> Consecutivo de la factura
     * @param $xTerId2  -> Facturar a, a quien se le facturo
     */
    function fnEnviarFactura($xFile,$xComId,$xComCod,$xComCsc,$xComCsc2,$xTerId2,$xTerNom2,$xTramite,$xPedido) {
      global $cAlfa; global $xConexion01; global $vSysStr; global $cPath;
  
      $nSwitch = 0; $vMsj = array();
  
      $cMailerUsr   = $vSysStr['system_financiero_correo_factura_user'];
      $cMailerPass  = $vSysStr['system_financiero_correo_factura_pas'];
      $cMailerSmtp  = $vSysStr['system_financiero_correo_factura_smtp'];
      $cMailerPto   = $vSysStr['system_financiero_correo_factura_puerto'];
  
      //Enviar de Factura por correo
      $vReturnCorreos = $this->fnContactos($xTerId2);
  
      $vCorreos = array();
      for ($nC=0; $nC<count($vReturnCorreos); $nC++) {
        if ($vReturnCorreos[$nC] != "") {
          $vAxuCor = explode(",", $vReturnCorreos[$nC]);
          for ($nA=0; $nA<count($vAxuCor); $nA++) {
            $vCorreos[] = trim($vAxuCor[$nA]);
          }
        }
      }
  
      if (count($vCorreos) > 0) {
  
        $cDominio = "opentecnologia.com.co";
  
        $cSubject = utf8_decode("Certificado de mandato correspondiente a la factura No. $xComId-$xComCod-$xComCsc de AGENCIA DE ADUANAS MARIO LONDOÑO S.A. - NIT. 890.902.266-2");
  
        $cMessage  = "Se&ntilde;ores,<br>";
        $cMessage .= "<b>$xTerNom2</b><br><br>";
        $cMessage .= "Adjunto estamos enviando copia virtual de su <b>Certificado de Retenciones por Pagos a Terceros</b> ";
        $cMessage .= "correspondiente a la Factura Electr&oacute;nica No. $xComId-$xComCod-$xComCsc  en formato PDF, ";
        $cMessage .= "este documento es de car&aacute;cter informativo. <br><br>";
        $cMessage .= "Su Factura Electr&oacute;nica le llegar&aacute; al correo que usted indic&oacute; registrar en la base de datos ";
        $cMessage .= "de Agencia de Aduanas Mario Londo&ntilde;o, en caso de no haber realizado el registro agradecemos informarlo. ";
        $cMessage .= "<br><br>";
        $cMessage .= "Si tiene alguna observaci&oacute;n frente a esta factura, por favor comunicarse directamente con su asesor.";
        $cMessage .= "<br><br>";
        $cMessage .= "<b><font color='red'>Correo autogenerado, por favor no lo responda.</font></b>";
        $cMessage .= "<br><br>";
  
        $cFrom = "Facturacion Malco <no-reply@$cDominio>";
        $cHeaders = "From: $cFrom";
  
        // boundary
        $semi_rand = md5(time());
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
  
        // headers for attachment
        $cHeaders .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
  
        // multipart boundary
        $cMessage = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"iso-8859-1\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $cMessage . "\n\n";
        $cMessage .= "--{$mime_boundary}\n";
  
        for ($nA=0; $nA<count($xFile); $nA++) {
          if(filesize($xFile[$nA]['ruta']) <= (1024*1024)){
            $file = fopen($xFile[$nA]['ruta'],"rb");
            $data = fread($file,filesize($xFile[$nA]['ruta']));
            fclose($file);
            $data = chunk_split(base64_encode($data));
            $name = $xFile[$nA]['archivo'];
            $cMessage .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$name\"\n";
            $cMessage .= "Content-Disposition: attachment;\n" . " filename=\"$name\"\n";
            $cMessage .= "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
            $cMessage .= "--{$mime_boundary}\n";
  
          } else {
            $nSwitch = 1;
            $vMsj[count($vMsj)] = "Para la Factura [$xComId-$xComCod-$xComCsc] Archivo Adjunto [$xFile[$nA]['archivo']] Supera Limite de Tamano [1024]. Favor Comunicar este Error a openTecnologia S.A.\n";
          }
        }
        
        // send
        //Enviando correos a los contactos y director(es) de Cuenta del o los Do
        for ($nC=0; $nC<count($vCorreos); $nC++) {
          $xMail = mail($vCorreos[$nC], $cSubject, $cMessage, $cHeaders);
          if(!$xMail){
            $nSwitch = 1;
            $vMsj[count($vMsj)] = "Para la Factura [$xComId-$xComCod-$xComCsc] Error al Enviar Correo al destinatario [{$vCorreos[$nC]}]. Favor Comunicar este Error a openTecnologia S.A.\n";
          }
          $cCorreos .= "{$vCorreos[$nC]}, ";
        }
        $cCorreos = substr($cCorreos, 0, strlen($cCorreos)-2);
  
        /*$mail = new PHPMailer();
        $mail->Mailer = "smtp";
        $mail->Port = $cMailerPto;
        $mail->SMTPSecure = "ssl";
        $mail->Host = $cMailerSmtp;
        $mail->SMTPAuth = true;
        $mail->Username = $cMailerUsr;
        $mail->Password = $cMailerPass;
        $mail->From = $cDominio;
        $mail->FromName = $cFrom;
  
        $mail->Timeout = 30;
        $mail->Subject = $cSubject;
        $mail->ContentType = "text/html";
        $mail->Body = $cMessage;
  
        for ($nA=0; $nA<count($xFile); $nA++) {
          if(filesize($xFile[$nA]['ruta']) <= (1024*1024)){
            $mail->AddAttachment($xFile[$nA]['ruta'],$xFile[$nA]['archivo']);
          } else {
            $nSwitch = 1;
            $vMsj[count($vMsj)] = "Para la Factura [$xComId-$xComCod-$xComCsc] Archivo Adjunto [$xFile[$nA]['archivo']] Supera Limite de Tamano [1024]. Favor Comunicar este Error a openTecnologia S.A.\n";
          }
        }
  
        // send
        if ($nSwitch == 0) {
          $cCorreos = "";
          for ($nC=0; $nC<count($vCorreos); $nC++) {
            if ($vCorreos[$nC] != "") {
              $mail->AddAddress($vCorreos[$nC]);
              //Enviando correos a los contactos y director(es) de Cuenta del o los Do
              if ($mail->Send()) {
                //Enviado con Exito
              } else {
                $nSwitch = 1;
                $vMsj[count($vMsj)] = "Para la Factura [$xComId-$xComCod-$xComCsc] Error al Enviar Correo al destinatario [{$vCorreos[$nC]}]. Favor Comunicar este Error a openTecnologia S.A.\n";
                //echo $mail->ErrorInfo;
              }
  
              $mail->ClearAddresses();
              $cCorreos .= "{$vCorreos[$nC]}, ";
            }
          }
          $cCorreos = substr($cCorreos, 0, strlen($cCorreos)-2);
        }*/
  
        if($nSwitch == 0) {
          $nSwitch = 2;
          $vMsj[count($vMsj)] = "Se Envio con Exito el Certificado de Mandato Correspondiente a la Factura [$xComId-$xComCod-$xComCsc] a los Siguientes Correos:\n$cCorreos.\n";
        }
      }
  
      if ($nSwitch == 1 || $nSwitch == 2) {
        return $vMsj;
      }
    }

    function fnFechaLetras($xFecha){
      if ($xFecha==''){
        $xFecfor='';
      }else{
        $fano = substr ($xFecha, 0, 4);
        $fdia = substr ($xFecha, 8, 2);
        $fmes_antes = substr ($xFecha, 5, 2);
        if($fmes_antes=='01')
          $fmes="Enero";
        if($fmes_antes=='02')
          $fmes="Febrero";
        if($fmes_antes=='03')
          $fmes="Marzo";
        if($fmes_antes=='04')
          $fmes="Abril";
        if($fmes_antes=='05')
          $fmes="Mayo";
        if($fmes_antes=='06')
          $fmes="Junio";
        if($fmes_antes=='07')
          $fmes="Julio";
        if($fmes_antes=='08')
          $fmes="Agosto";
        if($fmes_antes=='09')
          $fmes="Septiembre";
        if($fmes_antes=='10')
          $fmes="Octubre";
        if($fmes_antes=='11')
          $fmes="Noviembre";
        if($fmes_antes=='12')
          $fmes="Diciembre";
        $xFecFor= ($fdia+0)." de ".$fmes." de ".$fano;
      }
      return ($xFecFor);
    }

  } ## class cCertificadoMandato { ##

  class PDF extends FPDF {
    function Header() {
		  global $cPlesk_Skin_Directory; global $nBan; global $nPosY; global $dFecDes; global $dFecHas; global $mCocDat; global $cCiuDat; global $cAlfa; global $vSysStr;
      global $gTipo; global $cPath;

      if ($cAlfa == "ROLDANLO" || $cAlfa == "DEROLDANLO" || $cAlfa == "TEROLDANLO") {
        $this->Image($cPath.$cPlesk_Skin_Directory.'/logoroldan_r.jpg',-2,-7,140);
        $this->Image($cPath.$cPlesk_Skin_Directory.'/logoroldan_razonsocial.jpg',95,4,90);
      }

      if($this->PageNo() > 1 && $nBan == 1) {
        switch($cAlfa){
          case "DEINTERLOG":
			    case "TEINTERLOG":
			    case "INTERLOG":
            $this->Image($cPath.$cPlesk_Skin_Directory.'/MaryAire.jpg',10,10,45,15);
          break;
			    case "DECOLMASXX":
			    case "TECOLMASXX":
			    // case "DEDESARROL": //ALMACAFE
			    case "COLMASXX":
            $this->Image($cPath.$cPlesk_Skin_Directory.'/colmas.jpg',10,10,40,15);
			      $this->SetFont('verdanab','',6);
						$this->SetTextColor(129,129,133);
						$this->setXY(10,21);
						$this->Cell(28,10,$vSysStr['financiero_nit_agencia_aduanas'].'-'.f_Digito_Verificacion($vSysStr['financiero_nit_agencia_aduanas']),0,0,'L');
						$this->SetTextColor(0,0,0);
          break;
          case "CASTANOX":
          case "TECASTANOX":
          case "DECASTANOX":
            $this->Image($cPath.$cPlesk_Skin_Directory.'/logomartcam.jpg',13,6,35,19);
          break;
        }

        switch($cAlfa){
          case "ROLDANLO":
          case "DEROLDANLO":
          case "TEROLDANLO":
            $nPosY = 40;
            $this->SetFont('verdanab','',9);
            $this->setXY(5,$nPosY);
            if ($gTipo == "CERTIFICADOINT") {
              $this->Cell(270,10,"INTERMEDIACION DE PAGO",0,0,'C');
            } else {
              $this->Cell(270,10,"CERTIFICADO DE MANDATO",0,0,'C');
            }
          break;
          default:
            $nPosY = 15;
            $this->SetFont('verdanab','',9);
            $this->setXY(5,$nPosY);
            if ($gTipo == "CERTIFICADOINT") {
              $this->Cell(270,10,"CERTIFICADO DE INTERMEDIACION DE PAGO",0,0,'C');
            } else {
              $this->Cell(270,10,"CERTIFICADO DE RETENCIONES POR PAGOS A TERCEROS",0,0,'C');
            }
          break;
        }
        ##Fin Switch para imprimir LOGO##

        switch($cAlfa){
          case "ROLDANLO":
          case "DEROLDANLO":
          case "TEROLDANLO":
            $nCellDo  = 25;
            $nCellFac = 18;
            $nPosxFac = 30;
          break;
          default:
            $nCellDo  = 25;
            $nCellFac = 15;
            $nPosxFac = 33;
          break;
        }

	      $nPosY += 10;
	      $this->setXY(5,$nPosY);
				$this->SetFont('verdanab','',6);
				$this->Cell($nCellDo,10,"D.O.",0,0,'C');
				$this->Rect(5,$nPosY+2,$nCellDo,5);
				$this->setXY($nPosxFac,$nPosY);
				$this->SetFont('verdanab','',6);
				$this->Cell($nCellFac,10,"FACTURA",0,0,'C');
				$this->Rect($nPosxFac,$nPosY+2,$nCellFac,5);
				$this->setXY(48,$nPosY);
				$this->SetFont('verdanab','',6);

				switch($cAlfa){
          case "ROLDANLO":
			    case "DEROLDANLO":
			    case "TEROLDANLO":
            $this->Cell(15,10,"NIT",0,0,'C');
					break;
			    default:
            $this->Cell(15,10,"TERCERO",0,0,'C');
			    break;
        }

				$this->Rect(48,$nPosY+2,15,5);
				$this->setXY(63,$nPosY);
				$this->SetFont('verdanab','',6);

        switch($cAlfa){
          case "ROLDANLO":
			    case "DEROLDANLO":
			    case "TEROLDANLO":
            $this->Cell(30,10,"PROVEEDOR",0,0,'C');
					break;
			    default:
            $this->Cell(30,10,"NOMBRE",0,0,'C');
          break;
        }

        $this->Rect(63,$nPosY+2,30,5);
				$this->setXY(93,$nPosY);
				$this->SetFont('verdanab','',6);

        switch($cAlfa){
          case "ROLDANLO":
			    case "DEROLDANLO":
			    case "TEROLDANLO":
            $this->Cell(20,10,"FACTURA",0,0,'C');
          break;
			    default:
            $this->Cell(20,10,"DOCUMENTO",0,0,'C');
          break;
        }

        $this->Rect(93,$nPosY+2,20,5);
        $this->setXY(113,$nPosY);
        $this->SetFont('verdanab','',6);
        $this->Cell(15,10,"FECHA",0,0,'C');
        $this->Rect(113,$nPosY+2,15,5);
        $this->setXY(128,$nPosY);
        $this->SetFont('verdanab','',6);
        $this->Cell(29,10,"CONCEPTO",0,0,'C');
        $this->Rect(128,$nPosY+2,29,5);
        $this->setXY(157,$nPosY);
        $this->SetFont('verdanab','',6);

				switch($cAlfa){
          case "ROLDANLO":
			    case "DEROLDANLO":
			    case "TEROLDANLO":
            $this->Cell(18,10,"VALOR",0,0,'C');
          break;
			    default:
            $this->Cell(18,10,"COSTO",0,0,'C');
          break;
        }

				$this->Rect(157,$nPosY+2,18,5);
				$this->setXY(175,$nPosY);
				$this->SetFont('verdanab','',6);
				$this->Cell(15,10,"IVA",0,0,'C');
				$this->Rect(175,$nPosY+2,15,5);
				$this->setXY(190,$nPosY);
				$this->SetFont('verdanab','',6);
				$this->Cell(20,10,"TOTAL",0,0,'C');
				$this->Rect(190,$nPosY+2,20,5);
				$this->setXY(210,$nPosY);
				$this->SetFont('verdanab','',6);
				$this->Cell(15,10,"TIPO",0,0,'C');
				$this->Rect(210,$nPosY+2,15,5);
				$this->setXY(225,$nPosY);
				$this->SetFont('verdanab','',6);
				$this->Cell(20,10,"VALOR BASE",0,0,'C');
				$this->Rect(225,$nPosY+2,20,5);
				$this->setXY(245,$nPosY);
				$this->SetFont('verdanab','',6);
				$this->Cell(10,10,"%",0,0,'C');
				$this->Rect(245,$nPosY+2,10,5);
				$this->setXY(255,$nPosY);
				$this->SetFont('verdanab','',6);
				$this->Cell(20,10,"VALOR",0,0,'C');
				$this->Rect(255,$nPosY+2,20,5);
      }

      if ($cAlfa == "ROLDANLO" || $cAlfa == "DEROLDANLO" || $cAlfa == "TEROLDANLO") {
        $this->Image($cPath.$cPlesk_Skin_Directory.'/piepaginaroldan.jpg',28,199,220);
        $this->SetFont('verdana','',7);
        $this->setXY(5,210);
        $this->Cell(40,5,$this->PageNo().' de {nb}',0,'C');
      }

    }///Fin Header

		function Footer() {
		  /*$this->SetY(-10);
			$this->SetFont('verdana','',6);
			$this->Cell(0,5,'PAGINA '.$this->PageNo().' DE {nb}',0,0,'C');*/
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

  }

  class cEstructurasCertificadoMandato{

    /**
     * Metodo que se encarga de Crear las Estructuras de las Tablas
     */
    function fnCrearEstructurasCertificadoMandato($pParametros){

      global $xConexion01; global $cAlfa; global $vSysStr; global $kUser; global $cPath;

      /**
       *Recibe como Parametro un vector con las siguientes posiciones:
       *$pArrayParametros['TIPOESTU] //TIPO DE ESTRUCTURA
       */

      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var number
       */
      $nSwitch = 0;

      /**
       * Matriz para Retornar Valores
       */
      $mReturn = array();

      /**
       * Reservando Primera Posición para retorna true o false
       */
      $mReturn[0] = "";

      /**
       * Llamando Metodo que hace conexion
       */
      $mReturnConexionTM = $this->fnConectarDBCertificadoMandato();
      if($mReturnConexionTM[0] == "true"){
        $xConexionTM = $mReturnConexionTM[1];
      }else{
        $nSwitch = 1;
        for($nR=1;$nR<count($mReturnConexionTM);$nR++){
          $mReturn[count($mReturn)] = $mReturnConexionTM[$nR];
        }
      }

      /**
       * Borrando tablas antiguas
       */
      //$this->fnBorrarEstructurasCertificadoMandato();

      /**
       * Random para Nombre de la Tabla
       */
      $cTabCar  = mt_rand(1000000000, 9999999999);

      switch($pParametros['TIPOESTU']){
        case "ERRORES":
          $cTabla = "memerror".$cTabCar;

          $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
          $qNewTab .= "LINEAIDX INT(11) NOT NULL AUTO_INCREMENT,"; //LINEA
          $qNewTab .= "LINEAERR VARCHAR(10) NOT NULL,";           //LINEA DEL ARCHIVO
          $qNewTab .= "TIPOERRX VARCHAR(50) NOT NULL,";           //TIPO DE ERROR
          $qNewTab .= "DESERROR TEXT NOT NULL,";                  //DESCRIPCION DEL ERROR
          $qNewTab .= " PRIMARY KEY (LINEAIDX)) ENGINE=MyISAM ";
          $xNewTab  = mysql_query($qNewTab,$xConexionTM);
          //f_Mensaje(__FILE__,__LINE__,$qNewTab);

          if(!$xNewTab) {
            $nSwitch = 1;
            $mReturn[count($mReturn)] = "Error al Crear Tabla Temporal de Errores.".mysql_error($xConexionTM);
          }
        break;
        default:
          $nSwitch = 1;
          $mReturn[count($mReturn)] = "No se Recibio Tipo de Estructura a Crear";
        break;
      }

      if($nSwitch == 0){
        $mReturn[0] = "true"; $mReturn[1] = $cTabla;  $mReturn[2] = $vFieldsExcluidos;
      }else{
        $mReturn[0] = "false";
      }
      return $mReturn;
    } ## function fnCrearEstructurasCertificadoMandato($pParametros){ ##

    /**
     * Metodo que se encarga de Borrar las Estructuras de las Tablas
     */
    function fnBorrarEstructurasCertificadoMandato() {
      global $xConexion01; global $cAlfa; global $vSysStr; global $kUser; global $cPath;

      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var number
       */
      $nSwitch = 0;

      /**
       * Matriz para Retornar Valores
       */
      $mReturn = array();

      /**
       * Reservando Primera Posición para retorna true o false
       */
      $mReturn[0] = "";

      /**
       * Llamando Metodo que hace conexion
       */
      $mReturnConexionTM = $this->fnConectarDBCertificadoMandato();
      if($mReturnConexionTM[0] == "true"){
        $xConexionTM = $mReturnConexionTM[1];
      }else{
        $nSwitch = 1;
        for($nR=1;$nR<count($mReturnConexionTM);$nR++){
          $mReturn[count($mReturn)] = $mReturnConexionTM[$nR];
        }
      }

      $qDroTab  = "SELECT table_schema,table_name ";
      $qDroTab .= "FROM information_schema.TABLES ";
      $qDroTab .= "WHERE ";
      $qDroTab .= "table_schema = \"$cAlfa\" AND ";
      $qDroTab .= "table_name LIKE 'mem_______________' AND (UNIX_TIMESTAMP(now()) - UNIX_TIMESTAMP(create_time)) > (2*60*60)";
      $xDroTab  = mysql_query($qDroTab,$xConexionTM);
      while($xRDT = mysql_fetch_array($xDroTab)){
        $qDrop  = "DROP TABLE IF EXISTS $cAlfa.{$xRDT['table_name']} ";
        $xDrop  = mysql_query($qDrop,$xConexionTM);
      }
      mysql_free_result($xDroTab);

    }##function fnBorrarEstructurasCertificadoMandato() {##

    /**
     * Metodo que realiza la conexion
     */
    function fnConectarDBCertificadoMandato(){
      global $cAlfa; global $cPath;

      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var number
       */
      $nSwitch = 0;

      /**
       * Matriz para Retornar Valores
       */
      $mReturn = array();

      /**
       * Reservo Primera Posicion para retorna true o false
       */
      $mReturn[0] = "";

      $xConexion99 = mysql_connect(OC_SERVER,OC_USERROBOT,OC_PASSROBOT) or die("El Sistema no Logro Conexion con ".OC_SERVER);
      if($xConexion99){
        $nSwitch = 0;
      }else{
        $nSwitch = 1;
        $mReturn[count($mReturn)] = "El Sistema no Logro Conexion con ".OC_SERVER;
      }

      if($nSwitch == 0){
        $mReturn[0] = "true"; $mReturn[1] = $xConexion99;
      }else{
        $mReturn[0] = "false";
      }
      return $mReturn;
    }##function fnConectarDBCertificadoMandato(){##

    /**
     * Metodo que realiza el reinicio de la conexion
     */
    function fnReiniciarConexionDBCertificadoMandato($pConexion){
      global $cHost;  global $cUserHost;  global $cPassHost; global $cPath;

      mysql_close($pConexion);
      // $xConexion01 = mysql_connect($cHost,$cUserHost,$cPassHost,TRUE);
      $xConexion01 = mysql_connect(OC_SERVER,OC_USERROBOT,OC_PASSROBOT,TRUE);

      return $xConexion01;
    }##function fnReiniciarConexionDBCertificadoMandato(){##

    /**
     * Metodo que se encarga de Guardar los Errores Generados por los Metodos de Interfaces
     */
    function fnGuardarErrorCertificadoMandato($pArrayParametros){
      global $xConexion01; global $cAlfa; global $vSysStr; global $kUser; global $cPath;

      /**
       * Recibe como parametro un vector con los siguientes campos
       * $pArrayParametros['TABLAERR']  //TABLA ERROR
       * $pArrayParametros['LINEAERR']  //LINEA ERROR
       * $pArrayParametros['TIPOERRX']  //TIPO DE ERROR
       * $pArrayParametros['DESERROR']  //DESCRIPCION DEL ERROR
       * $pArrayParametros['MOSTRARX']  //INDICA SI SE DEBE PINTAR O NO EL ERROR.  EN SI O VACIO SE PINTA.
       */

       /**
         * Variables para reemplazar caracteres especiales
         * @var array
         */
      $cBuscar = array('"',"'",chr(13),chr(10),chr(27),chr(9));
      $cReempl = array('\"',"\'"," "," "," "," ");

      if($pArrayParametros['TABLAERR'] != ""){

        $qInsert  = "INSERT INTO $cAlfa.{$pArrayParametros['TABLAERR']} (";
        $qInsert .= "LINEAERR,";
        $qInsert .= "TIPOERRX,";
        $qInsert .= "DESERROR) VALUES (";
        $qInsert .= "\"{$pArrayParametros['LINEAERR']}\",";
        $qInsert .= "\"{$pArrayParametros['TIPOERRX']}\",";
        $qInsert .= "\"".str_replace($cBuscar,$cReempl,$pArrayParametros['DESERROR'])."\")";
        $nQueryTimeStart = microtime(true); $xInsert = mysql_query($qInsert,$xConexion01);
        $nQueryTime = (microtime(true) - $nQueryTimeStart); $objGuardarError->fnMysqlQueryInfoCertificadoMandato($xConexion01,$nQueryTime);
      }
    }##function fnGuardarErrorCertificadoMandato($pParametros){##

    /**
     * Metodo para capturar la informacion del motor de DB asosciada al query
     */
    function fnMysqlQueryInfoCertificadoMandato($xConexion,$xQueryTime) {

      global $cAlfa; global $_SERVER; global $kDf;

      if(count($kDf) == 0){
        $kDf[3] = $cAlfa;
        $kDf[4] = "OPENCOMEX";
      }

      $xMysqlInfo = mysql_info($xConexion);

      ereg("Changed: ([0-9]*)",$xMysqlInfo,$vChanged);
      ereg("Deleted: ([0-9]*)",$xMysqlInfo,$vDeleted);
      ereg("Duplicates: ([0-9]*)",$xMysqlInfo,$vDuplicates);
      ereg("Records: ([0-9]*)",$xMysqlInfo,$vRecords);
      ereg("Rows matched: ([0-9]*)",$xMysqlInfo,$vRows_matched);
      ereg("Skipped: ([0-9]*)",$xMysqlInfo,$vSkipped);
      ereg("Warnings: ([0-9]*)",$xMysqlInfo,$vWarnings);

      $cQueryInfo  = "|";
      $cQueryInfo .= "Changed~{$vChanged[1]}|";
      $cQueryInfo .= "Deleted~{$vDeleted[1]}|";
      $cQueryInfo .= "Duplicates~{$vDuplicates[1]}|";
      $cQueryInfo .= "Records~{$vRecords[1]}|";
      $cQueryInfo .= "Rows matched~{$vRows_matched[1]}|";
      $cQueryInfo .= "Skipped~{$vSkipped[1]}|";
      $cQueryInfo .= "Warnings~{$vWarnings[1]}|";
      $cQueryInfo .= "Affected Rows~".mysql_affected_rows($xConexion)."|";
      $cQueryInfo .= "Query Time~".number_format($xQueryTime,2)."|";
      $cQueryInfo .= "Error Number~".mysql_errno($xConexion)."|";
      $cQueryInfo .= "Error Description~".mysql_error($xConexion)."|";

      $cIP = "";
      $cHost = "";
      if ($_SERVER['HTTP_CLIENT_IP'] != "") {
        $cIP   = $_SERVER['HTTP_CLIENT_IP'];
        $cHost = $_SERVER['HTTP_VIA'];
      }elseif ($_SERVER['HTTP_X_FORWARDED_FOR'] != "") {
        $cIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
        $cHost = $_SERVER['HTTP_VIA'];
      }else{
        $cIP = $_SERVER['REMOTE_ADDR'];
        $cHost = $_SERVER['HTTP_VIA'];
      }

      if ($cHost == "") {
        $cHost = $cIP;
      }

      $copenComex  = "|";
      $copenComex .= "{$kDf[4]}~";
      $copenComex .= "{$_SERVER['PHP_SELF']}~";
      $copenComex .= "$cIP~";
      $copenComex .= "$cHost~";
      $copenComex .= "{$kDf[3]}~";
      $copenComex .= date("Y-m-d")."~";
      $copenComex .= date("H:i:s");
      $copenComex .= "|";
      $xopenComex = mysql_query("SET @opencomex = \"$copenComex\"",$xConexion);
      $xQueryInfo = mysql_query("SET @mysqlinfo = \"$cQueryInfo\"",$xConexion);
    } ## function f_Mysql_Query_Info($xConexion,$xQueryTime) {

  }
  
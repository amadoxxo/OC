<?php
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

  function count_float($nFlotante){
    $whole = floor($nFlotante);
    $fraction = $nFlotante - $whole;

    return strlen($fraction) - 2;
  }

  class PDF extends FPDF {
    function Header(){
      global $cCliId;
      global $gSerTop;
      global $cAlfa;
      global $cTarTip;
      global $xConexion01;
      global $cPlesk_Skin_Directory;


      if ($cTarTip == "CLIENTE") {
				$qSqlCli  = "SELECT ";
				$qSqlCli .= "$cAlfa.SIAI0150.*, ";
				$qSqlCli .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
				$qSqlCli .= "FROM $cAlfa.SIAI0150 ";
				$qSqlCli .= "WHERE ";
				$qSqlCli .= "$cAlfa.SIAI0150.CLIIDXXX = \"$cCliId\" LIMIT 0,1";
	      $xSqlCli  = f_MySql("SELECT","",$qSqlCli,$xConexion01,"");

	      $zRCli = mysql_fetch_array($xSqlCli);

	      $qConDat  = "SELECT * ";
	      $qConDat .= "FROM $cAlfa.fpar0151 ";
	      $qConDat .= "WHERE ";
	      $qConDat .= "$cAlfa.fpar0151.cliidxxx = \"$cCliId\" LIMIT 0,1 ";
	      $xConDat  = f_MySql("SELECT","",$qConDat,$xConexion01,"");
	      $vConDat = mysql_fetch_array($xConDat);

	      //$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/GRM11.jpg',0,0,210,270);

	      if($gSerTop!="TODOS"){
	        $txt="REPORTE TARIFAS POR CLIENTE ($gSerTop)";
	      } else {
	        $txt="REPORTE TARIFAS POR CLIENTE";
	      }
      } else {
        //Busco Grupo de Tarifas
        $qGruTar  = "SELECT ";
        $qGruTar .= "gtadesxx ";
        $qGruTar .= "FROM $cAlfa.fpar0111 ";
        $qGruTar .= "WHERE ";
        $qGruTar .= "$cAlfa.fpar0111.gtaidxxx = \"$cCliId\" LIMIT 0,1";
        $xGruTar  = f_MySql("SELECT","",$qGruTar,$xConexion01,"");
        //f_Mensaje(__FILE__,__LINE__,$qGruTar."~".mysql_num_rows($xGruTar));

        $xRGT = mysql_fetch_array($xGruTar);

        if($gSerTop!="TODOS"){
          $txt="REPORTE TARIFAS POR GRUPO ($gSerTop)";
        } else {
          $txt="REPORTE TARIFAS POR GRUPO";
        }
      }

      $this->SetFont('arial','B',10);
      $this->setY(24);
			if ($cAlfa == "TRLXXXXX" || $cAlfa == "TETRLXXXXX" || $cAlfa == "DETRLXXXXX") {
				$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logobma1.jpg',10,10,55,15);
				$this->setY(25);
				$this->Cell(190,5,"OFERTA AGENCIAMIENTO ADUANERO",0,0,'C');
				$this->setY(30);
	      $this->MultiCell(190,10," ",'B','C');
				$this->setY(35);
				$this->SetFont('arial','',7);

				if ($cTarTip == "CLIENTE") {
		      $this->setY(42);
		    	$this->Cell(100,3,'CLIENTE: '.$zRCli['CLINOMXX']);
		    	$this->Cell(50,3,'NIT: '.$zRCli['CLIIDXXX']."-".f_Digito_Verificacion($zRCli['CLIIDXXX']));
		    	$this->Cell(50,3,'TELEFONO: '.$zRCli['CLITELXX']);
		    	$this->setY(42);
		    	$this->SetFont('arial','B',8);
		      $this->MultiCell(190,5,'','B','L');
	      } else {
	        $this->setY(42);
	        $this->Cell(100,3,'GRUPO: '.$xRGT['gtadesxx']);
	        $this->Cell(50,3,'ID: '.$cCliId);
	        $this->Cell(50,3,'');
	        $this->setY(42);
	        $this->SetFont('arial','B',8);
	        $this->MultiCell(190,5,"",'B','L');
	      }
			} else {
        switch($cAlfa) {
          case "TEMELYAKXX":
          case "DEMELYAKXX":
          case "MELYAKXX":
            $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomelyak.jpg', 83, 8, 40, 15);
          break;
          case "TEFEDEXEXP":
          case "DEFEDEXEXP":
          case "FEDEXEXP":
            $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logofedexexp.jpg', 83, 8, 40);
          break;
          case "TEEXPORCOM":
          case "DEEXPORCOM":
          case "EXPORCOM":
            $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoexporcomex.jpg', 83, 8, 40);
          break;
          case "HAYDEARX":
          case "DEHAYDEARX":
          case "TEHAYDEARX":
            $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logohaydear.jpeg', 83, 8, 50, 20);
          break;
          case "CONNECTA":
          case "DECONNECTA":
          case "TECONNECTA":
            $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoconnecta.jpg', 83, 8, 28, 18);
          break;
          default:
            // no hace nada
          break;
        }
        $this->MultiCell(190,5,'TARIFAS','','L');
        $this->setY(30);
        $this->MultiCell(190,5,$txt,'','C');

        if ($cTarTip == "CLIENTE") {
          $this->setY(35);
          $this->MultiCell(190,5,"VIGENCIA DESDE : ".$vConDat['cccfdcxx']." HASTA: ".$vConDat['cccfhcxx'],'B','C');
          $this->SetFont('arial','',7);
          $this->setY(42);
          $this->Cell(100,3,'CLIENTE: '.$zRCli['CLINOMXX']);
          $this->Cell(50,3,'NIT: '.$zRCli['CLIIDXXX']."-".f_Digito_Verificacion($zRCli['CLIIDXXX']));
          $this->Cell(50,3,'TELEFONO: '.$zRCli['CLITELXX']);
          ## Insercción de CODIGO SAP ##
          switch($cAlfa) {
            case "TEALMACAFE":
            case "DEALMACAFE":
            case "ALMACAFE":
              $this->setY(47);
              $this->Cell(50,3,'CODIGO SAP: '.$zRCli['CLISAPXX']);
              $this->setY(47);
            break;
            default:
              $this->setY(42);
            break;
          }
          ## FIN Insercción de CODIGO SAP ##
          $this->SetFont('arial','B',8);
          $this->MultiCell(190,5,'','B','L');
        } else {
          $this->setY(35);
          $this->MultiCell(190,5,"",'B','C');
          $this->SetFont('arial','',7);
          $this->setY(42);
          $this->Cell(100,3,'GRUPO: '.$xRGT['gtadesxx']);
          $this->Cell(50,3,'ID: '.$cCliId);
          $this->Cell(50,3,'');
          ## Insercción de CODIGO SAP ##
          switch($cAlfa) {
            case "TEALMACAFE":
            case "DEALMACAFE":
            case "ALMACAFE":
              $this->setY(47);
              $this->Cell(50,3,'CODIGO SAP: '.$zRCli['CLISAPXX']);
              $this->setY(47);
            break;
            default:
              $this->setY(42);
            break;
          }
          ## FIN Insercción de CODIGO SAP ##
          $this->SetFont('arial','B',8);
          $this->MultiCell(190,5,"",'B','L');
        }
	    }
    	$this->Ln(5);
		}

    function Footer(){
      global $cAlfa;
      global $xConexion01;

      $qSqlUsr = "SELECT * FROM $cAlfa.SIAI0003 WHERE USRIDXXX = \"{$_COOKIE['kUsrId']}\" LIMIT 0,1";
      $xSqlUsr = f_MySql("SELECT","",$qSqlUsr,$xConexion01,"");
      $xRUsr = mysql_fetch_array($xSqlUsr);
      //Posicion: a 1,5 cm del final
      $this->SetY(-25);
	    //Arial italic 8
      $this->SetFont('Arial','I',6);

			//Numero de pagina
      $this->MultiCell(0,10,$xRUsr['USRNOMXX'],0,'R',0);

      //Posicion: a 1,5 cm del final
      $this->SetY(-15);
      //Arial italic 8
      $this->SetFont('Arial','I',6);
      $this->SetFont('Arial','I',8);
      $this->MultiCell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,'C',0);
    }
  }

  $pdf=new PDF();
  $pdf->AliasNbPages();
  $pdf->AddPage('P');   // L para poner la Hoja Horizontal, por default es P

  //Select General
  $qSqlCab  = "SELECT $cAlfa.fpar0131.*, ";
  $qSqlCab .= "$cAlfa.fpar0129.pucaftex AS pucaftec, ";
  $qSqlCab .= "$cAlfa.fpar0129.pucrftex AS pucrftec, ";
  $qSqlCab .= "IF($cAlfa.fpar0131.serdespc <> \"\",$cAlfa.fpar0131.serdespc,IF($cAlfa.fpar0129.serdespx <> \"\",$cAlfa.fpar0129.serdespx,IF($cAlfa.fpar0129.serdesxx <> \"\", $cAlfa.fpar0129.serdesxx, \"CONCEPTO SIN DESCRIPCION\"))) AS serdesxx, ";
  $qSqlCab .= "IF($cAlfa.fpar0130.fcodesxx <> \"\", $cAlfa.fpar0130.fcodesxx, \"FORMA DE COBRO SIN DESCRIPCION\") AS fcodesxx  ";
  $qSqlCab .= "FROM $cAlfa.fpar0131 ";
  $qSqlCab .= "LEFT JOIN $cAlfa.fpar0129 ON $cAlfa.fpar0131.seridxxx = $cAlfa.fpar0129.seridxxx ";
  $qSqlCab .= "LEFT JOIN $cAlfa.fpar0130 ON $cAlfa.fpar0131.fcoidxxx = $cAlfa.fpar0130.fcoidxxx ";
  $qSqlCab .= "WHERE $cAlfa.fpar0131.cliidxxx = \"$cCliId\" ";

  // Verifico si marcaron TODOS en tipo de OPERACION
  if($gSerTop != "TODOS"){
		$qSqlCab .= "AND $cAlfa.fpar0131.fcotopxx = \"$gSerTop\" ";
  }

  // Verifico si marcaron TODOS en ESTADO
  if($gOrdBy != "TODOS"){
		$qSqlCab .= " AND $cAlfa.fpar0131.regestxx = \"$gOrdBy\" ";
 	}

 	//$qSqlCab .= "ORDER BY $cAlfa.fpar0131.fcotptxx, CONVERT($cAlfa.fpar0131.fcoidxxx,signed) ASC ";
  $qSqlCab .= "ORDER BY $cAlfa.fpar0131.fcotopxx, $cAlfa.fpar0131.fcotptxx, CONVERT($cAlfa.fpar0131.fcotpixx,signed) ASC ";
  $xSqlCab  = f_MySql("SELECT","",$qSqlCab,$xConexion01,"");

	// Cargo la matriz principal con las tarifas del cliente
  $i=0;
  while ($zRCab = mysql_fetch_array($xSqlCab)) {
    $zMatrizTra[$i] = $zRCab;
    $i++;
  }

  /* Seleccion de Tablas para ALPOPULX y DEMAS*/
  $cTabPry 		= "fpar0142";
  $cTabProLpr = "fpar0143";
  $cCamProId  = "proidxxx";
  $cCamProDes = "prodesxx";
	if ($kMysqlDb == "ALPOPULX" || $kMysqlDb == "TEALPOPULP" || $kMysqlDb == "TEALPOPULX" || $kMysqlDb == "DEALPOPULX") {
		$cTabPry 		= "siai1101";
  	$cTabProLpr = "zalpo003";
  	$cCamProId  = "lpridxxx";
  	$cCamProDes = "lprdesxx";
	}
  /* Seleccion de Tablas para ALPOPULX y DEMAS*/


  /* Recorro la Matriz para Traer Datos Externos */
	for ($i=0;$i<count($zMatrizTra);$i++) {

		/**
		 * Johana Arboleda
		 * 2013-07-11 11:22
		 * Buscando las cuentas de retencion en la fuente y autoretencion en la fuente,
		 * solo se muestran si son diferentes a las cuentas de retencion del concepto
		 */
		if ($zMatrizTra[$i]['pucrftec'] != "") {
			if ($zMatrizTra[$i]['pucrftex'] != "") {
				/* Traigo Descripcion de la Cuenta cPucRfte en la fpar0115 */
				$qSqlCta1 = "SELECT pucdesxx,pucretxx FROM $cAlfa.fpar0115 WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$zMatrizTra[$i]['pucrftex']}\" LIMIT 0,1";
				$xSqlCta1 = f_MySql("SELECT","",$qSqlCta1,$xConexion01,"");
				while ($zRCta1 = mysql_fetch_array($xSqlCta1)) {
					$zMatrizTra[$i]['pucdesxx'] = (trim($zRCta1['pucdesxx']) == "") ? "CUENTA SIN DESCRIPCION" : trim($zRCta1['pucdesxx']);
					$zMatrizTra[$i]['pucretxx'] = $zRCta1['pucretxx']+0;
				}
			}
		}

		if ($zMatrizTra[$i]['pucaftec'] != "") {
			if ($zMatrizTra[$i]['pucaftex'] != "") {
				/* Traigo Descripcion de la Cuenta cPucRfte en la fpar0115 */
				$qSqlCta1 = "SELECT pucdesxx,pucretxx FROM $cAlfa.fpar0115 WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$zMatrizTra[$i]['pucaftex']}\" LIMIT 0,1";
				$xSqlCta1 = f_MySql("SELECT","",$qSqlCta1,$xConexion01,"");
				while ($zRCta1 = mysql_fetch_array($xSqlCta1)) {
					$zMatrizTra[$i]['pucadesx'] = (trim($zRCta1['pucdesxx']) == "") ? "CUENTA SIN DESCRIPCION" : trim($zRCta1['pucdesxx']);
					$zMatrizTra[$i]['pucaretx'] = $zRCta1['pucretxx']+0;
				}
			}
		}

		//$zMatrizTra[$i]['popdesxx'] = "PRUEBA HERNAN GORDILLO";
		/* Traigo el Nombre del producto o proyecto */
		switch($zMatrizTra[$i]['fcotptxx']){
	  	case "PROYECTO":
	  		/* Traigo el PROYECTO */
				$qSqlPry = "SELECT prydesxx  FROM $cAlfa.$cTabPry WHERE pryidxxx  = \"{$zMatrizTra[$i]['fcotpixx']}\" LIMIT 0,1";
				$xSqlPry  = f_MySql("SELECT","",$qSqlPry,$xConexion01,"");
				if (mysql_num_rows($xSqlPry) > 0) {
					while ($zRPry = mysql_fetch_array($xSqlPry)) {
						$zMatrizTra[$i]['popdesxx'] = $zRPry['prydesxx'];
					}
				} else {
					$zMatrizTra[$i]['popdesxx'] = "PROYECTO SIN DESCRIPCION";
				}
				/* Fin Traigo el PROYECTO */
	  	break;
	  	case "PRODUCTO":

	  		/* Traigo el PRODUCTO */
				$qSqlPry = "SELECT $cCamProDes FROM $cAlfa.$cTabProLpr WHERE $cCamProId = \"{$zMatrizTra[$i]['fcotpixx']}\" LIMIT 0,1";
				$xSqlPry  = f_MySql("SELECT","",$qSqlPry,$xConexion01,"");
				if (mysql_num_rows($xSqlPry) > 0) {
					while ($zRPry = mysql_fetch_array($xSqlPry)) {
						$zMatrizTra[$i]['popdesxx'] = $zRPry["$cCamProDes"];
					}
				} else {
					$zMatrizTra[$i]['popdesxx'] = "PRODUCTO SIN DESCRIPCION";
				}
				/* Fin Traigo el PRODUCTO */
	  	break;
	  	default:
	  	break;
	  }
	  /*Fin Traigo el Nombre del producto o proyecto */

	  /** Descripcion de la moneda ***/
	  $qMoneda  = "SELECT MONIDXXX, ";
    $qMoneda .= "MONDESXX ";
    $qMoneda .= "FROM $cAlfa. SIAI0111 ";
    $qMoneda .= "WHERE MONIDXXX = \"{$zMatrizTra[$i]['monidxxx']}\" AND ";
    $qMoneda .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
    $xMoneda  = f_MySql("SELECT","",$qMoneda,$xConexion01,"");
    //f_Mensaje(__FILE__,__LINE__,$qMoneda."~".mysql_num_rows($xMoneda));
    if (mysql_num_rows($xMoneda) > 0){
      $xRM = mysql_fetch_array($xMoneda);
      $zMatrizTra[$i]['mondesxxx'] = $xRM['MONDESXX'];
    }else{
      $zMatrizTra[$i]['mondesxxx'] = "MONEDA SIN DESCRIPCION";
    }
    /** Fin Descripcion de la moneda ***/
	}
	/* Fin de Recorro la Matriz para Traer Datos Externos */

				$cTitTop = "";
				$cTitTpt = "";
				$cTitTpi = "";

  			$pdf->SetFont('arial','',7);
        for ($i=0;$i<count($zMatrizTra);$i++) {

          if($pdf->GetY() > 260) {
            $pdf->AddPage();
          }
        	/// PINTO TITULOS POR OPERACION
        	$pdf->SetFont('arial','B',10);
			    $pdf->SetFillColor(190);
			    $pdf->setX(10);
        	if ($i==0) {

		        //$pdf->Ln(1);
		        //$pdf->MultiCell(187,5,"Operacion: ".$zMatrizTra[$i]['fcotopxx']." Tarifa por: ".$zMatrizTra[$i]['fcotptxx'],'','L',1);
		        if ($zMatrizTra[$i]['fcotptxx'] == "GENERAL") {
		        	$pdf->MultiCell(187,5,$zMatrizTra[$i]['fcotopxx']."  |  ".$zMatrizTra[$i]['fcotptxx'],'','L',1);
		        } else {
		        	$pdf->MultiCell(187,5,$zMatrizTra[$i]['fcotopxx']."  |  ".$zMatrizTra[$i]['fcotptxx']."  |  ".$zMatrizTra[$i]['fcotpixx']." - ".$zMatrizTra[$i]['popdesxx'],'','L',1);
		        }
		        $pdf->Ln(1);
        	} else {

        		$cTitTop = $zMatrizTra[$i-1]['fcotopxx'];
						$cTitTpt = $zMatrizTra[$i-1]['fcotptxx'];
						$cTitTpi = $zMatrizTra[$i-1]['fcotpixx'];

        		if (($cTitTop != $zMatrizTra[$i]['fcotopxx']) || ($cTitTpt != $zMatrizTra[$i]['fcotptxx']) || ($cTitTpi != $zMatrizTra[$i]['fcotpixx'])) {
			        //$pdf->Ln(1);
			        //$pdf->MultiCell(187,5,"Operacion: ".$zMatrizTra[$i]['fcotopxx']." Tarifa por: ".$zMatrizTra[$i]['fcotptxx'],'','L',1);
	        		if ($zMatrizTra[$i]['fcotptxx'] == "GENERAL") {
			        	$pdf->MultiCell(187,5,$zMatrizTra[$i]['fcotopxx']."  |  ".$zMatrizTra[$i]['fcotptxx'],'','L',1);
			        } else {
			        	$pdf->MultiCell(187,5,$zMatrizTra[$i]['fcotopxx']."  |  ".$zMatrizTra[$i]['fcotptxx']."  |  ".$zMatrizTra[$i]['fcotpixx']." - ".$zMatrizTra[$i]['popdesxx'],'','L',1);
			        }
			        $pdf->Ln(1);
        		}
        	}

	        $pdf->SetFont('arial','',7);
       	  ///PINTO LOS CONCEPTOS CON SUS FORMAS DE COBRO///
          $pdf->Ln(1);
          $zSuc   = str_replace('~', '-', $zMatrizTra[$i]['sucidxxx']);
          $zModTra= str_replace('~', '-', $zMatrizTra[$i]['fcomtrxx']);

          $pdf->SetFillColor(240);

          $pdf->setX(15);
          $pdf->MultiCell(182,3,"CONCEPTO DE COBRO: ".$zMatrizTra[$i]['seridxxx']." - ".$zMatrizTra[$i]['serdesxx']."     |     ESTADO: ".$zMatrizTra[$i]['regestxx'],'','L',1);
          $pdf->Ln(1);

          $pdf->setX(20);
          $pdf->MultiCell(177,3,"SUCURSALES: ".$zSuc,'','L',1);
          $pdf->Ln(1);

          $pdf->setX(25);
          $pdf->MultiCell(172,3,"MODOS TRANSPORTE: ".$zModTra,'','L',1);
          $pdf->Ln(1);

          $pdf->setX(30);
          $pdf->MultiCell(167,3,"MONEDA: [".$zMatrizTra[$i]['monidxxx']."]  ".$zMatrizTra[$i]['mondesxxx'],'','L',1);
          $pdf->Ln(3);

          $pdf->setX(22);

          if($zMatrizTra[$i]['fcoidxxx']=='100' || $zMatrizTra[$i]['fcoidxxx']=='102' ||
             $zMatrizTra[$i]['fcoidxxx']=='200' || $zMatrizTra[$i]['fcoidxxx']=='201' ||
             $zMatrizTra[$i]['fcoidxxx']=='300' || $zMatrizTra[$i]['fcoidxxx']=='301' ||
             $zMatrizTra[$i]['fcoidxxx']=='400' || $zMatrizTra[$i]['fcoidxxx']=='500' ||
          	 $zMatrizTra[$i]['fcoidxxx']=='502' || $zMatrizTra[$i]['fcoidxxx']=='127' ||
             $zMatrizTra[$i]['fcoidxxx']=='146' || $zMatrizTra[$i]['fcoidxxx']=='150' ||
             $zMatrizTra[$i]['fcoidxxx']=='152' || $zMatrizTra[$i]['fcoidxxx']=='163' ||
             $zMatrizTra[$i]['fcoidxxx']=='240' || $zMatrizTra[$i]['fcoidxxx']=='174' ||
             $zMatrizTra[$i]['fcoidxxx']=='250' || $zMatrizTra[$i]['fcoidxxx']=='251' ||
             $zMatrizTra[$i]['fcoidxxx']=='1122' || $zMatrizTra[$i]['fcoidxxx']=='401' ||
             $zMatrizTra[$i]['fcoidxxx']=='1143' || $zMatrizTra[$i]['fcoidxxx']=='1144'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCad100 = explode("|",$zMatrizTra[$i]['fcotarxx']);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Fijo: '.number_format($nCad100[1], 0, '', '.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
         	}

         	if($zMatrizTra[$i]['fcoidxxx']=='139') {

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad139 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Cantidad DIM por Grupo : '.$nCad139[0]);
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Cantidad DAV por Grupo : '.$nCad139[1]);
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Fijo por Grupo: '.number_format($nCad139[2], 0, '', '.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Cantidad de Grupos que no se cobran: '.$nCad139[3]);
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
         	}

         	if($zMatrizTra[$i]['fcoidxxx']=='101' || $zMatrizTra[$i]['fcoidxxx']=='203' ||
         	   $zMatrizTra[$i]['fcoidxxx']=='210' || $zMatrizTra[$i]['fcoidxxx']=='302' ||
         	   $zMatrizTra[$i]['fcoidxxx']=='306' || $zMatrizTra[$i]['fcoidxxx']=='126' ||
         	   $zMatrizTra[$i]['fcoidxxx']=='137' || $zMatrizTra[$i]['fcoidxxx']=='173' ||
             $zMatrizTra[$i]['fcoidxxx']=='249' || $zMatrizTra[$i]['fcoidxxx']=='1142'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad101 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'% : '.$nCad101[0]);
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Minima: '.number_format($nCad101[1], 0, '', '.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
         	}

          if( $zMatrizTra[$i]['fcoidxxx']=='103' || $zMatrizTra[$i]['fcoidxxx']=='202' || 
              $zMatrizTra[$i]['fcoidxxx']=='1110'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad103 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor: '.number_format($nCad103[0], 0,'', '.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Horas: '.number_format($nCad103[1], 0,'', '.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Adicional: '.number_format($nCad103[2],0,'','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
         	}

          if( $zMatrizTra[$i]['fcoidxxx']=='1123' || $zMatrizTra[$i]['fcoidxxx']=='1125'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad1123 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Unidades Minimas: '.number_format($nCad1123[0], 0,'', '.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor x Unidades Minimas: '.number_format($nCad1123[1], 0,'', '.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Adicional x Unidad Adicional: '.number_format($nCad1123[2],0,'','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
           }
           
           if( $zMatrizTra[$i]['fcoidxxx']=='1126'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad1126 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Cobro Adicional: '.number_format($nCad1126[0], 0,'', '.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Fijo: '.number_format($nCad1126[1], 0,'', '.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
					 }

					 if( $zMatrizTra[$i]['fcoidxxx']=='1129'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad1129 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Hoja Principal: '.number_format($nCad1129[0], 0,'', '.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Hoja Adicional: '.number_format($nCad1129[1], 0,'', '.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
         	}

          if( $zMatrizTra[$i]['fcoidxxx']=='1131' || $zMatrizTra[$i]['fcoidxxx']=='1136'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad1131 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,utf8_decode('Valor Fijo Depósito Habilitado: ').number_format($nCad1131[0], 0,'', '.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Fijo Descargue Directo: '.number_format($nCad1131[1], 0,'', '.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
         	}

          if( $zMatrizTra[$i]['fcoidxxx']=='1133'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad1133 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Fijo DIM: '.number_format($nCad1133[0], 0,'', '.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Fijo DAV: '.number_format($nCad1133[1], 0,'', '.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
         	}

          if( $zMatrizTra[$i]['fcoidxxx']=='1134'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad1134 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Porcentaje Sobre Tributos: '.number_format($nCad1134[0], 0,'', '.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
         	}

          if( $zMatrizTra[$i]['fcoidxxx']=='1135'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad1135 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'% Cif: '.$nCad1135[0]);
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Fijo: '.number_format($nCad1135[1], 0,'', '.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
         	}

        	if($zMatrizTra[$i]['fcoidxxx']=='104' || $zMatrizTra[$i]['fcoidxxx']=='204' ||
             $zMatrizTra[$i]['fcoidxxx']=='303' || $zMatrizTra[$i]['fcoidxxx']=='313' ||
             $zMatrizTra[$i]['fcoidxxx']=='504'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad104 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Tarifa Inicial: '.number_format($nCad104[0],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Unidades Inciales: '.$nCad104[1]);
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Tarifa despues de Inciales: '.number_format($nCad104[2],0,'','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
         	}

         	if($zMatrizTra[$i]['fcoidxxx']=='105' || $zMatrizTra[$i]['fcoidxxx']=='209'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad105 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Parcial: '.number_format($nCad105[0],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Fijo de Cobro Por Primer Parcial: '.number_format($nCad105[1],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'% de Cobro Valor CIF/FOB Adicional: '.$nCad105[2]);
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='106'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad106 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'% Valor Cif: '.$nCad106[0]);
            $pdf->Ln(3);
            $pdf->setX(22);
            $pdf->Cell(20,3,'Declaraciones',1,0,'C');
            $pdf->Cell(20,3,'Minima Variable',1,0,'C');
            $pdf->Ln(3);
            $zNiveles=explode("!",$nCad106[1]);
            for($e=0; $e<count($zNiveles); $e++){
							if($zNiveles[$e]!=""){
								$zInterno=explode("^",$zNiveles[$e]);
								$pdf->setX(22);
								$pdf->Cell(20,3,$zInterno[0],1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[1],0,'','.'),1,0,'R');
                $pdf->Ln(3);
              }
					  }
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='138'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad138 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->Cell(20,3,'Limite Inferior',1,0,'C');
            $pdf->Cell(20,3,'Limite Superior',1,0,'C');
            $pdf->Cell(20,3,'Valor Fijo $COP',1,0,'C');
            $pdf->Ln(3);
            $zNiveles=explode("!",$nCad138[1]);
            for($e=0; $e<count($zNiveles); $e++){
              if($zNiveles[$e]!=""){
                $zInterno=explode("^",$zNiveles[$e]);
                $pdf->setX(22);
                $pdf->Cell(20,3,number_format($zInterno[0],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[1],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,$zInterno[2],1,0,'R');
                $pdf->Ln(3);
              }
            }
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='107' ||
             $zMatrizTra[$i]['fcoidxxx']=='135' ||
             $zMatrizTra[$i]['fcoidxxx']=='159' ||
             $zMatrizTra[$i]['fcoidxxx']=='1138' ||
             $zMatrizTra[$i]['fcoidxxx']=='205' ||
             $zMatrizTra[$i]['fcoidxxx']=='304'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad107 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Minima: '.number_format($nCad107[0],0,'','.'));
            $pdf->Ln(3);
            $pdf->setX(22);
            $pdf->Cell(20,3,'Limite Inferior',1,0,'C');
            $pdf->Cell(20,3,'Limite Superior',1,0,'C');
            $pdf->Cell(20,3,'Porcentaje',1,0,'C');
            $pdf->Ln(3);
            $zNiveles=explode("!",$nCad107[1]);
            for($e=0; $e<count($zNiveles); $e++){
					    if($zNiveles[$e]!=""){
					      $zInterno=explode("^",$zNiveles[$e]);
					      $pdf->setX(22);
					      $pdf->Cell(20,3,number_format($zInterno[0],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[1],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[2],count_float($zInterno[2]),',','.'),1,0,'R');
                $pdf->Ln(3);
              }
					  }
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='1130'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena  = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad1130 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,utf8_decode('Mínima Deposito Habilitado: ').number_format($nCad1130[0],0,'','.'));
            $pdf->Ln(1);
            $pdf->setX(22);
            $pdf->MultiCell(80,3,utf8_decode('Mínima Descargue Directo: ').number_format($nCad1130[2],0,'','.'));
            $pdf->Ln(3);
            $pdf->setX(22);
            $pdf->Cell(20,3,'Limite Inferior',1,0,'C');
            $pdf->Cell(20,3,'Limite Superior',1,0,'C');
            $pdf->Cell(20,3,'Porcentaje',1,0,'C');
            $pdf->Ln(3);
            $zNiveles=explode("!",$nCad1130[1]);
            for($e=0; $e<count($zNiveles); $e++){
              if($zNiveles[$e]!=""){
                $zInterno=explode("^",$zNiveles[$e]);
                $pdf->setX(22);
                $pdf->Cell(20,3,number_format($zInterno[0],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[1],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[2],count_float($zInterno[2]),',','.'),1,0,'R');
                $pdf->Ln(3);
              }
            }
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='1132'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena  = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad1132 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,utf8_decode('Valor Adicional x Ítem: ').number_format($nCad1132[0],0,'','.'));
            $pdf->Ln(1);
            $pdf->setX(22);
            $pdf->MultiCell(80,3,utf8_decode('Cantidad Mínima Seriales: ').number_format($nCad1132[2],0,'','.'));
            $pdf->Ln(1);
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Adicional Seriales: '.number_format($nCad1132[3],0,'','.'));
            $pdf->Ln(3);
            $pdf->setX(22);
            $pdf->Cell(20,3,'Limite Inferior',1,0,'C');
            $pdf->Cell(20,3,'Limite Superior',1,0,'C');
            $pdf->Cell(20,3,'Valor Fijo',1,0,'C');
            $pdf->Ln(3);
            $zNiveles=explode("!",$nCad1132[1]);
            for($e=0; $e<count($zNiveles); $e++){
              if($zNiveles[$e]!=""){
                $zInterno=explode("^",$zNiveles[$e]);
                $pdf->setX(22);
                $pdf->Cell(20,3,number_format($zInterno[0],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[1],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[2],count_float($zInterno[2]),',','.'),1,0,'R');
                $pdf->Ln(3);
              }
            }
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='108' ||
             $zMatrizTra[$i]['fcoidxxx']=='135' ||
             $zMatrizTra[$i]['fcoidxxx']=='206'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad108 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Minima: '.number_format($nCad108[0],0,'','.'));
            $pdf->Ln(3);
            $pdf->setX(22);
            $pdf->Cell(20,3,'Limite Inferior',1,0,'C');
            $pdf->Cell(20,3,'Limite Superior',1,0,'C');
            $pdf->Cell(20,3,'Porcentaje',1,0,'C');
            $pdf->Ln(3);
            $zNiveles=explode("!",$nCad108[1]);
            for($e=0; $e<count($zNiveles); $e++){
						  if($zNiveles[$e]!=""){
						    $zInterno=explode("^",$zNiveles[$e]);
						    $pdf->setX(22);
						    $pdf->Cell(20,3,number_format($zInterno[0],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[1],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[2],count_float($zInterno[2]),',','.'),1,0,'R');

                $pdf->Ln(3);
              }
						}
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='109' || $zMatrizTra[$i]['fcoidxxx']=='207' || $zMatrizTra[$i]['fcoidxxx']=='305' || $zMatrizTra[$i]['fcoidxxx']=='134' || $zMatrizTra[$i]['fcoidxxx']=='234'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

              $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
              $nCad109 = explode("~",$nCadena[1]);
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'% CIF para Granel: '.$nCad109[0]);
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'Contenedor 20: '.number_format($nCad109[1],0,'','.'));
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'Contenedor 40: '.number_format($nCad109[2],0,'','.'));
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'Carga Suelta: '.number_format($nCad109[3],0,'','.'));
              $pdf->SetFont('arial','',7);
              $pdf->Ln(3);

          }

					if($zMatrizTra[$i]['fcoidxxx']=='153'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

              $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
              $nCad153 = explode("~",$nCadena[1]);
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'Contenedor 20: '.number_format($nCad153[0],0,'','.'));
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'Contenedor 40: '.number_format($nCad153[1],0,'','.'));
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'Furgon: '.number_format($nCad153[2],0,'','.'));
              $pdf->SetFont('arial','',7);
              $pdf->Ln(3);

          }

					if($zMatrizTra[$i]['fcoidxxx']=='155' || $zMatrizTra[$i]['fcoidxxx']=='281'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

              $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
              $nCad155 = explode("~",$nCadena[1]);
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'Valor Base: '.number_format($nCad155[0],0,'','.'));
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'% Aplica: '. ($nCad155[1]));
              $pdf->SetFont('arial','',7);
              $pdf->Ln(3);

          }

          if($zMatrizTra[$i]['fcoidxxx']=='156' || $zMatrizTra[$i]['fcoidxxx']=='237'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

              $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
              $nCad156 = explode("~",$nCadena[1]);
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'Minima: '.number_format($nCad156[0],0,'','.'));
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'Contenedor 20: '. number_format($nCad156[1],0,'','.'));
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'Contenedor 40: '. number_format($nCad156[2],0,'','.'));
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'Unidades Carga Suelta: '. number_format($nCad156[3],0,'','.'));
              $pdf->SetFont('arial','',7);
              $pdf->Ln(3);

          }

          if($zMatrizTra[$i]['fcoidxxx']=='165' || $zMatrizTra[$i]['fcoidxxx']=='242'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

              $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
              $nCad165 = explode("~",$nCadena[1]);
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'Minima: '.number_format($nCad165[0],0,'','.'));
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'SimCard: '. number_format($nCad165[1],0,'','.'));
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'Terminales: '. number_format($nCad165[2],0,'','.'));
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'Tablet: '. number_format($nCad165[3],0,'','.'));
              $pdf->setX(22);
              $pdf->MultiCell(80,3,utf8_decode('Módem: '). number_format($nCad165[4],0,'','.'));
              $pdf->SetFont('arial','',7);
              $pdf->Ln(3);

          }

          if($zMatrizTra[$i]['fcoidxxx']=='166' || $zMatrizTra[$i]['fcoidxxx']=='243'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

              $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
              $nCad166 = explode("~",$nCadena[1]);
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'Minima: '.number_format($nCad166[0],0,'','.'));
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'Valor x Hora: '. number_format($nCad166[1],0,'','.'));
              $pdf->SetFont('arial','',7);
              $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='244'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

              $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
              $nCad244 = explode("~",$nCadena[1]);
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'Minima: '.number_format($nCad244[0],0,'','.'));
              $pdf->setX(22);
              $pdf->MultiCell(80,3,utf8_decode('Valor x Declaración: '). number_format($nCad244[1],0,'','.'));
              $pdf->SetFont('arial','',7);
              $pdf->Ln(3);
          }
          if($zMatrizTra[$i]['fcoidxxx']=='1100' || $zMatrizTra[$i]['fcoidxxx']=='271'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

              $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
              $nCad1100 = explode("~",$nCadena[1]);
              $pdf->setX(22);
              $pdf->MultiCell(80,3,utf8_decode('Valor del Trámite: '). number_format($nCad1100[0],0,'','.'));
              $pdf->SetFont('arial','',7);
              $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='1101' || $zMatrizTra[$i]['fcoidxxx']=='272'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

              $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
              $nCad1101 = explode("~",$nCadena[1]);
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'Tasa Pactada: '.number_format($nCad1101[0],0,'','.'));
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'%: '. $nCad1101[1]);
              $pdf->setX(22);
              $pdf->MultiCell(80,3,utf8_decode('Mínima: '). number_format($nCad1101[2],0,'','.'));
              $pdf->SetFont('arial','',7);
              $pdf->Ln(3);

          }

          if($zMatrizTra[$i]['fcoidxxx']=='1103' || $zMatrizTra[$i]['fcoidxxx']=='274'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

              $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
              $nCad1103 = explode("~",$nCadena[1]);
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'Tarifa: '.number_format($nCad1103[0],0,'','.'));
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'%: '. $nCad1103[1]);
              $pdf->SetFont('arial','',7);
              $pdf->Ln(3);

          }
          if($zMatrizTra[$i]['fcoidxxx']=='1104'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

              $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
              $nCad1104 = explode("~",$nCadena[1]);
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'%: '. $nCad1104[0]);
              $pdf->SetFont('arial','',7);
              $pdf->Ln(3);

          }
          if($zMatrizTra[$i]['fcoidxxx']=='1105'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

              $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
              $nCad1105 = explode("~",$nCadena[1]);
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'%: '. $nCad1105[0]);
              $pdf->setX(22);
              $pdf->MultiCell(80,3,utf8_decode('Mínima: '). number_format($nCad1105[1],0,'','.'));
              $pdf->setX(22);
              $pdf->MultiCell(80,3,utf8_decode('Máxima: '). number_format($nCad1105[2],0,'','.'));
              $pdf->SetFont('arial','',7);
              $pdf->Ln(3);
          }
          if($zMatrizTra[$i]['fcoidxxx']=='1114'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

              $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
              $nCad1114 = explode("~",$nCadena[1]);
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'% CIF: '. $nCad1114[0]);
              $pdf->setX(22);
              $pdf->MultiCell(80,3,utf8_decode('Mínima: '). number_format($nCad1114[1],0,'','.'));
              $pdf->setX(22);
              $pdf->MultiCell(80,3,utf8_decode('Ítems Iniciales: '). number_format($nCad1114[2],0,'','.'));
              $pdf->setX(22);
              $pdf->MultiCell(80,3,utf8_decode('Vlr. Ítem Adicional: '). number_format($nCad1114[3],0,'','.'));
              $pdf->SetFont('arial','',7);
              $pdf->Ln(3);
          }
          if($zMatrizTra[$i]['fcoidxxx']=='1115'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

              $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
              $nCad1115 = explode("~",$nCadena[1]);
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'Cant. Horas x Bloque: '. number_format($nCad1115[0],0,'','.'));
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'Valor Bloque: '. number_format($nCad1115[1],0,'','.'));
              $pdf->SetFont('arial','',7);
              $pdf->Ln(3);
          }
          if($zMatrizTra[$i]['fcoidxxx']=='1116'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

              $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
              $nCad1116 = explode("~",$nCadena[1]);
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'% CIF: '. $nCad1116[0]);              
              $pdf->setX(22);
              $pdf->MultiCell(80,3,utf8_decode('Mínima: '). number_format($nCad1116[1],0,'','.'));
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'Valor Adicional: '. number_format($nCad1116[2],0,'','.'));
              $pdf->SetFont('arial','',7);
              $pdf->Ln(3);
          }
          if($zMatrizTra[$i]['fcoidxxx']=='1119'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad1119 = explode("~",$nCadena[1]);
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'% CIF: '. $nCad1119[0]);              
            $pdf->setX(22);
            $pdf->MultiCell(80,3,utf8_decode('Mínima: '). number_format($nCad1119[1],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Contenedor 20: '. number_format($nCad1119[2],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Contenedor 40: '. number_format($nCad1119[3],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Descargue Directo: '. number_format($nCad1119[4],0,'','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }
          if($zMatrizTra[$i]['fcoidxxx']=='1120'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $pdf->Ln(3);
            $nCad1120 = explode("~",$nCadena[1]);
            $pdf->setX(22);
            $pdf->Cell(80,3,'CONTENEDORES DE 20');
            $pdf->Ln(3);
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'% CIF: '. $nCad1120[0]);              
            $pdf->setX(22);
            $pdf->MultiCell(80,3,utf8_decode('Mínima: '). number_format($nCad1120[1],0,'','.'));
            $pdf->Ln(3);
            $pdf->setX(22);
            $pdf->Cell(80,3,'CONTENEDORES DE 40');
            $pdf->Ln(3);
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'% CIF: '.$nCad1120[2]);              
            $pdf->setX(22);
            $pdf->MultiCell(80,3,utf8_decode('Mínima: '). number_format($nCad1120[3],0,'','.'));
            $pdf->Ln(3);
            $pdf->setX(22);
            $pdf->Cell(80,3,'CARGA SUELTA');
            $pdf->Ln(3);
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'% CIF: '. $nCad1120[4]);              
            $pdf->setX(22);
            $pdf->MultiCell(80,3,utf8_decode('Mínima: '). number_format($nCad1120[5],0,'','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }
          if($zMatrizTra[$i]['fcoidxxx']=='1106'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad1106 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,utf8_decode('Valor Mínima: ').number_format($nCad1106[0],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,utf8_decode('Descuento: ').number_format($nCad1106[2],0,'','.'));
            $pdf->Ln(3);
            $pdf->setX(22);
            $pdf->Cell(20,3,'Limite Inferior',1,0,'C');
            $pdf->Cell(20,3,'Limite Superior',1,0,'C');
            $pdf->Cell(20,3,'Porcentaje',1,0,'C');
            $pdf->Ln(3);
            $zNiveles=explode("!",$nCad1106[1]);
            for($e=0; $e<count($zNiveles); $e++){
              if($zNiveles[$e]!=""){
                $zInterno=explode("^",$zNiveles[$e]);
                $pdf->setX(22);
                $pdf->Cell(20,3,number_format($zInterno[0],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[1],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[2],count_float($zInterno[2]),',','.'),1,0,'R');
                $pdf->Ln(3);
              }
            }
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='1107'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            //$nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            //$nCad1107 = explode("~",$nCadena[1]);

            $nCad1107 = f_Explode_Array($zMatrizTra[$i]['fcotarxx'],"|","~");

            $pdf->setX(22);
            $pdf->Ln(3);
            $pdf->setX(22);
            $pdf->Cell(40,3,'Cobro por Unidad de Carga',1,0,'C');
            $pdf->Cell(20,3,'%Cif',1,0,'C');
            $pdf->Cell(20,3,'Minima',1,0,'C');
            $pdf->Ln(3);

            $pdf->setX(22);
			      $pdf->Cell(40,3,"Contenedor",1,0,'L');
            $pdf->Cell(20,3,number_format($nCad1107[0][0],2,',','.'),1,0,'R');
            $pdf->Cell(20,3,number_format($nCad1107[0][1],0,',','.'),1,0,'R');
            $pdf->Ln(3);

            $pdf->setX(22);
			      $pdf->Cell(40,3,"Granel",1,0,'L');
            $pdf->Cell(20,3,number_format($nCad1107[1][0],2,',','.'),1,0,'R');
            $pdf->Cell(20,3,number_format($nCad1107[1][1],0,',','.'),1,0,'R');
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='1108'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad1108 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            //$pdf->MultiCell(80,3,'% CIF: '.number_format($nCad1108[0],0,'','.'));
            $pdf->MultiCell(80,3,'% CIF: '.$nCad1108[0]);
            $pdf->Ln(3);
            $pdf->setX(22);
            $pdf->Cell(20,3,'Limite Inferior',1,0,'C');
            $pdf->Cell(20,3,'Limite Superior',1,0,'C');
            $pdf->Cell(20,3,utf8_decode('Valor Mínima'),1,0,'C');
            $pdf->Ln(3);
            $zNiveles=explode("!",$nCad1108[1]);
            for($e=0; $e<count($zNiveles); $e++){
              if($zNiveles[$e]!=""){
                $zInterno=explode("^",$zNiveles[$e]);
                $pdf->setX(22);
                $pdf->Cell(20,3,number_format($zInterno[0],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[1],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[2],0,'','.'),1,0,'R');
                $pdf->Ln(3);
              }
            }
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='1109'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad119 = explode("~",$nCadena[1]);
            $pdf->setX(22);

            $pdf->Ln(3);
            $pdf->setX(22);
            $pdf->Cell(20,3,'Nivel',1,0,'C');
            $pdf->Cell(20,3,'% CIF',1,0,'C');
            $pdf->Cell(20,3,utf8_decode('Mínima'),1,0,'C');
            $pdf->Ln(3);
            $zNiveles=explode("!",$nCad119[1]);

            for($e=0; $e<count($zNiveles); $e++){
              if($zNiveles[$e]!=""){
                $zInterno=explode("^",$zNiveles[$e]);
                $pdf->setX(22);
                $pdf->Cell(20,3,number_format($zInterno[0],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[1],2,',','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[2],0,'','.'),1,0,'R');
                $pdf->Ln(3);
              }
            }
            $pdf->Ln(3);
          }


          if($zMatrizTra[$i]['fcoidxxx']=='276'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

              $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
              $nCad276 = explode("~",$nCadena[1]);
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'%: '. $nCad276[0]);
              $pdf->setX(22);
              $pdf->MultiCell(80,3,utf8_decode('Mínima: '). number_format($nCad276[1],0,'','.'));
              $pdf->setX(22);
              $pdf->MultiCell(80,3,utf8_decode('Descuento: '). number_format($nCad276[2],0,'','.'));
              $pdf->SetFont('arial','',7);
              $pdf->Ln(3);

          }

          if( $zMatrizTra[$i]['fcoidxxx']=='1111' || $zMatrizTra[$i]['fcoidxxx']=='1118' || $zMatrizTra[$i]['fcoidxxx']=='277' ||
              $zMatrizTra[$i]['fcoidxxx']=='278' || $zMatrizTra[$i]['fcoidxxx']=='310' || $zMatrizTra[$i]['fcoidxxx']=='311'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

              $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
              $nCad1111 = explode("~",$nCadena[1]);
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'%: '. $nCad1111[0]);
              $pdf->SetFont('arial','',7);
              $pdf->Ln(3);

          }

          if(($zMatrizTra[$i]['fcoidxxx']=='1112')){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(150,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad1112 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Tonelada: '.number_format($nCad1112[0],0,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,utf8_decode('Mínima: ').number_format($nCad1112[1],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,utf8_decode('Máxima: ').number_format($nCad1112[2],0,'','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='1113'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad1113 = explode("~",$nCadena[1]);
            $pdf->setX(22);

            $pdf->Ln(3);
            $pdf->setX(22);
            $pdf->Cell(20,3,'Nivel',1,0,'C');
            $pdf->Cell(20,3,'% CIF',1,0,'C');
            $pdf->Cell(20,3,utf8_decode('Mínima'),1,0,'C');
            $pdf->Cell(20,3,utf8_decode('Máxima'),1,0,'C');
            $pdf->Ln(3);
            $zNiveles=explode("!",$nCad1113[1]);

            for($e=0; $e<count($zNiveles); $e++){
              if($zNiveles[$e]!=""){
                $zInterno=explode("^",$zNiveles[$e]);
                $pdf->setX(22);
                $pdf->Cell(20,3,number_format($zInterno[0],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[1],2,',','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[2],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[3],0,'','.'),1,0,'R');
                $pdf->Ln(3);
              }
            }
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='157' || $zMatrizTra[$i]['fcoidxxx']=='158'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad157 = explode("~",$nCadena[1]);
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Minima: '.number_format($nCad157[0],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'% Valor Factura: '. number_format($nCad157[1],0,'','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='160' || $zMatrizTra[$i]['fcoidxxx']=='238'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad160 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->Cell(20,3,'Limite Inferior',1,0,'C');
            $pdf->Cell(20,3,'Limite Superior',1,0,'C');
            $pdf->Cell(20,3,'Valor',1,0,'C');
            $pdf->Ln(3);
            $zNiveles=explode("!",$nCad160[0]);
            for($e=0; $e<count($zNiveles); $e++){
              if($zNiveles[$e]!=""){
                $zInterno=explode("^",$zNiveles[$e]);
                $pdf->setX(22);
                $pdf->Cell(20,3,number_format($zInterno[0],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[1],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[2],0,'','.'),1,0,'R');
                $pdf->Ln(3);
              }
            }
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='161' || $zMatrizTra[$i]['fcoidxxx']=='162' ||
             $zMatrizTra[$i]['fcoidxxx']=='239' || $zMatrizTra[$i]['fcoidxxx']=='167' ||
             $zMatrizTra[$i]['fcoidxxx']=='245' || $zMatrizTra[$i]['fcoidxxx']=='269' ||
             $zMatrizTra[$i]['fcoidxxx']=='1102'|| $zMatrizTra[$i]['fcoidxxx']=='273'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad160 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Minima: '.number_format($nCad160[0],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,utf8_decode('Valor Máxima: ').number_format($nCad160[1],0,'','.'));
            $pdf->Ln(3);
            $pdf->setX(22);
            $pdf->Cell(20,3,'Limite Inferior',1,0,'C');
            $pdf->Cell(20,3,'Limite Superior',1,0,'C');
            if($zMatrizTra[$i]['fcoidxxx']=='1102' || $zMatrizTra[$i]['fcoidxxx']=='273'){
              $pdf->Cell(20,3,'Valor Fijo',1,0,'C');
            }else{
              $pdf->Cell(20,3,'Porcentaje',1,0,'C');
            }
            $pdf->Ln(3);
            $zNiveles=explode("!",$nCad160[2]);
            for($e=0; $e<count($zNiveles); $e++){
              if($zNiveles[$e]!=""){
                $zInterno=explode("^",$zNiveles[$e]);
                $pdf->setX(22);
                $pdf->Cell(20,3,number_format($zInterno[0],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[1],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[2],count_float($zInterno[2]),',','.'),1,0,'R');
                $pdf->Ln(3);
              }
            }
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='110' || $zMatrizTra[$i]['fcoidxxx']=='208'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad110 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Carga Suelta: '.number_format($nCad110[0],0,'','.'));
            $pdf->Ln(3);
            $pdf->setX(22);
            $pdf->Cell(20,3,'Nivel Inferior',1,0,'C');
            $pdf->Cell(20,3,'Nivel Superior',1,0,'C');
            $pdf->Cell(20,3,'Valor $COP',1,0,'C');
            $pdf->Ln(3);
            $zNiveles=explode("!",$nCad110[1]);
            for($e=0; $e<count($zNiveles); $e++){
					    if($zNiveles[$e]!=""){
					      $zInterno=explode("^",$zNiveles[$e]);
					      $pdf->setX(22);
					      $pdf->Cell(20,3,number_format($zInterno[0],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[1],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[2],0,',','.'),1,0,'R');
                $pdf->Ln(3);
              }
					  }
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='1127'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad1127 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Minima: '.number_format($nCad1127[0],0,'','.'));
            $pdf->Ln(3);
            $pdf->setX(22);
            $pdf->Cell(20,3,'Limite Inferior',1,0,'C');
            $pdf->Cell(20,3,'Limite Superior',1,0,'C');
            $pdf->Cell(20,3,'Valor por Unidad',1,0,'C');
            $pdf->Ln(3);
            $zNiveles=explode("!",$nCad1127[1]);
            for($e=0; $e<count($zNiveles); $e++){
					    if($zNiveles[$e]!=""){
					      $zInterno=explode("^",$zNiveles[$e]);
					      $pdf->setX(22);
					      $pdf->Cell(20,3,number_format($zInterno[0],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[1],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[2],0,',','.'),1,0,'R');
                $pdf->Ln(3);
              }
					  }
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='1128' || $zMatrizTra[$i]['fcoidxxx']=='282'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

              $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
              $nCad1128 = explode("~",$nCadena[1]);
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'%: '. $nCad1128[0]);
              $pdf->SetFont('arial','',7);
              $pdf->Ln(3);

          }

          if($zMatrizTra[$i]['fcoidxxx']=='111'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad111 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor: '.number_format($nCad111[0],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Piezas: '.number_format($nCad111[1],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Vlr. Adicional por Pieza: '.number_format($nCad111[2],0,'','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='112' || $zMatrizTra[$i]['fcoidxxx']=='1117' ){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad112 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Contenedor 20: '.number_format($nCad112[0],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Contenedor 40: '.number_format($nCad112[1],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Unidades Carga Suelta: '.number_format($nCad112[2],0,'','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='113'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad113 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'% Cif: '.number_format($nCad113[0],2,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Minina Normal: '.number_format($nCad113[1],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Minima Incrementada: '.number_format($nCad113[2],0,'','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='114'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad114 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Fijo: '.number_format($nCad114[0],2,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Minima: '.number_format($nCad114[1],2,',','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

        	if($zMatrizTra[$i]['fcoidxxx']=='115'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad115 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Contenedor 20: '.number_format($nCad115[0],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Contenedor 40: '.number_format($nCad115[1],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Contenedor 40 HC: '.number_format($nCad115[2],0,'','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

          if(($zMatrizTra[$i]['fcoidxxx']=='118') || ($zMatrizTra[$i]['fcoidxxx']=='211') ||
             ($zMatrizTra[$i]['fcoidxxx']=='312')){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(150,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad118 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'% Cif: '.number_format($nCad118[0],2,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Minima: '.number_format($nCad118[1],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Maxima: '.number_format($nCad118[2],0,'','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

        	if(($zMatrizTra[$i]['fcoidxxx']=='116') || ($zMatrizTra[$i]['fcoidxxx']=='117') ||
        	 	 ($zMatrizTra[$i]['fcoidxxx']=='119') || ($zMatrizTra[$i]['fcoidxxx']=='133') ||
        	 	 ($zMatrizTra[$i]['fcoidxxx']=='147') || ($zMatrizTra[$i]['fcoidxxx']=='148') ||
        	 	 ($zMatrizTra[$i]['fcoidxxx']=='236') || ($zMatrizTra[$i]['fcoidxxx']=='149') ||
						 ($zMatrizTra[$i]['fcoidxxx']=='154') || ($zMatrizTra[$i]['fcoidxxx']=='164') ||
             ($zMatrizTra[$i]['fcoidxxx']=='241') || ($zMatrizTra[$i]['fcoidxxx']=='246') ||
             ($zMatrizTra[$i]['fcoidxxx']=='309') || ($zMatrizTra[$i]['fcoidxxx']=='257') ||
             ($zMatrizTra[$i]['fcoidxxx']=='258') || ($zMatrizTra[$i]['fcoidxxx']=='259') ||
             ($zMatrizTra[$i]['fcoidxxx']=='188') || ($zMatrizTra[$i]['fcoidxxx']=='189') ||
             ($zMatrizTra[$i]['fcoidxxx']=='260') || ($zMatrizTra[$i]['fcoidxxx']=='261') ||
             ($zMatrizTra[$i]['fcoidxxx']=='190') || ($zMatrizTra[$i]['fcoidxxx']=='191') ||
						 ($zMatrizTra[$i]['fcoidxxx']=='1121') || ($zMatrizTra[$i]['fcoidxxx']=='280') ||
						 ($zMatrizTra[$i]['fcoidxxx']=='1124') || ($zMatrizTra[$i]['fcoidxxx']=='1137') ||
             ($zMatrizTra[$i]['fcoidxxx']=='1139') || ($zMatrizTra[$i]['fcoidxxx']=='1140') ||
             ($zMatrizTra[$i]['fcoidxxx']=='1141') || ($zMatrizTra[$i]['fcoidxxx']=='1145')){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad119 = explode("~",$nCadena[1]);
            $pdf->setX(22);

            if (($zMatrizTra[$i]['fcoidxxx']=='148') || ($zMatrizTra[$i]['fcoidxxx']=='236') ||
            	 ($zMatrizTra[$i]['fcoidxxx']=='149') || ($zMatrizTra[$i]['fcoidxxx']=='154')){
              $pdf->setX(22);
              $pdf->MultiCell(80,3,'Minima: '.number_format(($nCad119[0]),0,'','.'));
            }
            if (($zMatrizTra[$i]['fcoidxxx']=='190') || ($zMatrizTra[$i]['fcoidxxx']=='260')){
              $pdf->setX(22);
              $pdf->MultiCell(80,3,utf8_decode('Máxima: ').number_format(($nCad119[0]),0,'','.'));
            }
            if ($zMatrizTra[$i]['fcoidxxx']=='1137') {
              $pdf->setX(22);
              $pdf->MultiCell(80,3,utf8_decode('Valor Adicional por Ítem: ').number_format(($nCad119[0]),0,'','.'));
            }
            $pdf->Ln(3);
            $pdf->setX(22);
            if($zMatrizTra[$i]['fcoidxxx'] =='191' || $zMatrizTra[$i]['fcoidxxx'] =='261'){
              $pdf->Cell(20,3,'Cantidad',1,0,'C');
              $pdf->Cell(20,3,'Valor',1,0,'C');
            }else{
              $pdf->Cell(20,3,'Limite Inferior',1,0,'C');
              $pdf->Cell(20,3,'Limite Superior',1,0,'C');
            }

            if($zMatrizTra[$i]['fcoidxxx']!='191' && $zMatrizTra[$i]['fcoidxxx'] !='261'){
              $pdf->Cell(20,3,'Valor por Unidad',1,0,'C');
            }
            $pdf->Ln(3);
            $zNiveles=explode("!",$nCad119[1]);
            for($e=0; $e<count($zNiveles); $e++){
					    if($zNiveles[$e]!=""){
					      $zInterno=explode("^",$zNiveles[$e]);
					      $pdf->setX(22);
					      $pdf->Cell(20,3,number_format($zInterno[0],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[1],0,'','.'),1,0,'R');
                if($zMatrizTra[$i]['fcoidxxx']!='191' && $zMatrizTra[$i]['fcoidxxx'] !='261'){
                $pdf->Cell(20,3,number_format($zInterno[2],0,',','.'),1,0,'R');
              }
                $pdf->Ln(3);
              }
					  }
            $pdf->Ln(3);
          }

          if(($zMatrizTra[$i]['fcoidxxx']=='192') || ($zMatrizTra[$i]['fcoidxxx']=='262') ||
             ($zMatrizTra[$i]['fcoidxxx']=='193') || ($zMatrizTra[$i]['fcoidxxx']=='263')) {

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCadena192 = explode("~",$nCadena[1]);
            $pdf->setX(22);

            $pdf->Ln(3);
            $pdf->setX(22);
            $pdf->Cell(20,3,'Contenedores de 20',0,0,'L');
            $pdf->Ln(3);
            $pdf->setX(22);
            $pdf->Cell(20,3,'Limite Inferior',1,0,'C');
            $pdf->Cell(20,3,'Limite Superior',1,0,'C');
            $pdf->Cell(20,3,'Valor por Unidad',1,0,'C');
            $pdf->Ln(3);
            $zNiveles=explode("!",$nCadena192[1]);
            for($e=0; $e<count($zNiveles); $e++){
              if($zNiveles[$e]!=""){
                $zInterno=explode("^",$zNiveles[$e]);
                $pdf->setX(22);
                $pdf->Cell(20,3,number_format($zInterno[0],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[1],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[2],0,',','.'),1,0,'R');
                $pdf->Ln(3);
              }
            }
            // Contenedores de 40
            $pdf->setX(22);
            $pdf->Ln(3);
            $pdf->setX(22);
            $pdf->Cell(20,3,'Contenedores de 40',0,0,'L');
            $pdf->Ln(3);
            $pdf->setX(22);
            $pdf->Cell(20,3,'Limite Inferior',1,0,'C');
            $pdf->Cell(20,3,'Limite Superior',1,0,'C');
            $pdf->Cell(20,3,'Valor por Unidad',1,0,'C');
            $pdf->Ln(3);
            $zNiveles=explode("!",$nCadena192[2]);
            for($e=0; $e<count($zNiveles); $e++){
              if($zNiveles[$e]!=""){
                $zInterno=explode("^",$zNiveles[$e]);
                $pdf->setX(22);
                $pdf->Cell(20,3,number_format($zInterno[0],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[1],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[2],0,',','.'),1,0,'R');
                $pdf->Ln(3);
              }
            }

          }

          if(($zMatrizTra[$i]['fcoidxxx']=='194') || ($zMatrizTra[$i]['fcoidxxx']=='264') ||
             ($zMatrizTra[$i]['fcoidxxx']=='195') || ($zMatrizTra[$i]['fcoidxxx']=='265')){

           $pdf->SetFont('arial','B',6);
           $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

           $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
           $nCad194 = explode("~",$nCadena[1]);

           $pdf->Ln(3);
           $pdf->setX(22);
           $pdf->Cell(22,3,'Contenedores de 20',1,0,'C');
           $pdf->Cell(22,3,'Contenedores de 40',1,0,'C');
           $pdf->Ln(3);
           $pdf->setX(22);
           $pdf->Cell(22,3,number_format($nCad194[0],0,'','.'),1,0,'R');
           $pdf->Cell(22,3,number_format($nCad194[1],0,'','.'),1,0,'R');
           $pdf->SetFont('arial','',7);
           $pdf->Ln(3);
          }

          if(($zMatrizTra[$i]['fcoidxxx']=='196') || ($zMatrizTra[$i]['fcoidxxx']=='266')){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad196 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'% : '.$nCad196[0]);
            $pdf->setX(22);
            $pdf->MultiCell(80,3,utf8_decode('Máxima: ').number_format($nCad196[1], 0, '', '.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }
          if(($zMatrizTra[$i]['fcoidxxx']=='198')){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad198 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'% : '.$nCad198[0]);
            $pdf->setX(22);
            $pdf->MultiCell(80,3,utf8_decode('Sobre Comisión: ').$nCad198[1]);
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

          if(($zMatrizTra[$i]['fcoidxxx']=='267')){

           $pdf->SetFont('arial','B',6);
           $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

           $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
           $nCad267 = explode("~",$nCadena[1]);

           $pdf->Ln(3);
           $pdf->setX(22);
           $pdf->Cell(25,3,'Valor Toneladas',1,0,'C');
           $pdf->Cell(25,3,'Minima',1,0,'C');
           $pdf->Ln(3);
           $pdf->setX(22);
           $pdf->Cell(22,3,number_format($nCad267[0],0,'','.'),1,0,'R');
           $pdf->Cell(22,3,number_format($nCad267[1],0,'','.'),1,0,'R');
           $pdf->SetFont('arial','',7);
           $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='268'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCad268 = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'% FOB: '.number_format($nCad268[1], count_float($nCad268[1]), ',', '.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
         	}

          if($zMatrizTra[$i]['fcoidxxx']=='197'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad103 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor: '.number_format($nCad103[0], 0,'', '.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Horas: '.number_format($nCad103[1], 0,'', '.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Adicional: '.number_format($nCad103[2],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Maxima: '.number_format($nCad103[3],0,'','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

					if(($zMatrizTra[$i]['fcoidxxx']=='130') || ($zMatrizTra[$i]['fcoidxxx']=='131') || ($zMatrizTra[$i]['fcoidxxx']=='132') || ($zMatrizTra[$i]['fcoidxxx']=='187')){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad119 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->Ln(3);
            $pdf->setX(22);
            $pdf->Cell(20,3,'Limite Inferior',1,0,'C');
            $pdf->Cell(20,3,'Limite Superior',1,0,'C');
            $pdf->Cell(20,3,'Valor Fijo',1,0,'C');
            $pdf->Ln(3);
            $zNiveles=explode("!",$nCad119[1]);
            for($e=0; $e<count($zNiveles); $e++){
					    if($zNiveles[$e]!=""){
					      $zInterno=explode("^",$zNiveles[$e]);
					      $pdf->setX(22);
					      $pdf->Cell(20,3,number_format($zInterno[0],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[1],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[2],0,',','.'),1,0,'R');
                $pdf->Ln(3);
              }
					  }
            $pdf->Ln(3);
          }

        	if($zMatrizTra[$i]['fcoidxxx']=='120' || $zMatrizTra[$i]['fcoidxxx']=='212' ||
          	 $zMatrizTra[$i]['fcoidxxx']=='320' || $zMatrizTra[$i]['fcoidxxx']=='213' ||
          	 $zMatrizTra[$i]['fcoidxxx']=='199'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);
            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad120 = explode("~",$nCadena[1]);
            $pdf->Ln(3);
          }

        	if($zMatrizTra[$i]['fcoidxxx']=='121'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCad121 = explode("|",$zMatrizTra[$i]['fcotarxx']);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'% Cif: '.number_format($nCad121[1],2,',','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
         	}

        	if($zMatrizTra[$i]['fcoidxxx']=='122'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            //$nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            //$nCad122 = explode("~",$nCadena[1]);

            $nCad122 = f_Explode_Array($zMatrizTra[$i]['fcotarxx'],"|","~");

            $pdf->setX(22);
            $pdf->Ln(3);
            $pdf->setX(22);
            $pdf->Cell(40,3,'Tarifa Vinculada Intercompañias',1,0,'C');
            $pdf->Cell(20,3,'%Cif',1,0,'C');
            $pdf->Cell(20,3,'Minina',1,0,'C');
            $pdf->Ln(3);

            $pdf->setX(22);
			      $pdf->Cell(40,3,"Plena",1,0,'L');
            $pdf->Cell(20,3,number_format($nCad122[0][0],2,',','.'),1,0,'R');
            $pdf->Cell(20,3,number_format($nCad122[0][1],0,',','.'),1,0,'R');
            $pdf->Ln(3);

            $pdf->setX(22);
			      $pdf->Cell(40,3,"Vinculada Deposito",1,0,'L');
            $pdf->Cell(20,3,number_format($nCad122[1][0],2,',','.'),1,0,'R');
            $pdf->Cell(20,3,number_format($nCad122[1][1],0,',','.'),1,0,'R');
            $pdf->Ln(3);

            $pdf->setX(22);
			      $pdf->Cell(40,3,"Vinculada Agente Carga",1,0,'L');
            $pdf->Cell(20,3,number_format($nCad122[2][0],2,',','.'),1,0,'R');
            $pdf->Cell(20,3,number_format($nCad122[2][1],0,',','.'),1,0,'R');
            $pdf->Ln(6);
          }

        	if($zMatrizTra[$i]['fcoidxxx']=='123'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad121 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Tarifa Principal: '.number_format($nCad121[0],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Unidades Iniciales: '.number_format($nCad121[1],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Tarifa Despues de Inicial: '.number_format($nCad121[2],0,'','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

        	if($zMatrizTra[$i]['fcoidxxx']=='124'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad121 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Base: '.number_format($nCad121[0],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Minima: '.number_format($nCad121[1],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'%Cif: '.number_format($nCad121[2],2,',','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

        	if($zMatrizTra[$i]['fcoidxxx']=='125'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad125 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Minima: '.number_format($nCad125[0],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'%Cif: '.number_format($nCad125[1],2,',','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

					if($zMatrizTra[$i]['fcoidxxx']=='129'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad125 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'%Cif: '.number_format($nCad125[0],2,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Fijo Parcial: '.number_format($nCad125[1],0,'','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

        	if($zMatrizTra[$i]['fcoidxxx']=='209'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad209 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Parcial: '.number_format($nCad209[0],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Fijo de Cobro por Primer Parcial: '.number_format($nCad209[1],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'% de Cobro Valor FOB Adicional: '.number_format($nCad209[2],2,',','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

        	if($zMatrizTra[$i]['fcoidxxx']=='210'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad210 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Porcentaje: '.number_format($nCad210[0],count_float($nCad210[0]),',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Minima: '.number_format($nCad210[1],0,'','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

					if($zMatrizTra[$i]['fcoidxxx']=='128'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad103 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Parcial: '.number_format($nCad103[0], 0,'', '.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Fijo de Cobro por Primer Parcial: '.number_format($nCad103[1], 0,'', '.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor x Serial Adicional: '.number_format($nCad103[2],0,'','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
         	}

          if($zMatrizTra[$i]['fcoidxxx']=='141'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad141 = explode("~",$nCadena[1]);
            $vCanDec = explode(".",$nCad141[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'% Cif: '.$nCad141[0]);
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Tasa Negociada: '.number_format($nCad141[1],count($vCanDec),',','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

					if($zMatrizTra[$i]['fcoidxxx']=='142' || $zMatrizTra[$i]['fcoidxxx']=='176' ||
             $zMatrizTra[$i]['fcoidxxx']=='253' || $zMatrizTra[$i]['fcoidxxx']=='279'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad142 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Unidades Iniciales: '		.$nCad142[0]);
						$pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Inicial: '				.$nCad142[1]);
						$pdf->setX(22);
            $pdf->MultiCell(80,3,'Unidades Adicionales: '	.$nCad142[2]);
						$pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Adicional: '			.$nCad142[3]);
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

					if($zMatrizTra[$i]['fcoidxxx']=='143' || $zMatrizTra[$i]['fcoidxxx']=='151'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad143 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor por Unidad: '		.number_format($nCad143[0],0,'','.'));
						$pdf->setX(22);
            $pdf->MultiCell(80,3,'Minima: '							.number_format($nCad143[1],0,'','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

					if($zMatrizTra[$i]['fcoidxxx']=='144'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad144 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Fijo por Unidad: '		.$nCad144[0]);
						$pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Minimo: '							.$nCad144[1]);
						$pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Maximo: '							.$nCad144[2]);
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

					if($zMatrizTra[$i]['fcoidxxx']=='145'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
						$nCad145 = f_Explode_Array($nCadena[1],"~","^");
						$pdf->Ln(1);
						$pdf->setX(22);
            $pdf->MultiCell(80,3,'Carga Suelta');
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Tarifa Inicial: '												.number_format($nCad145[0][0],0,'','.'));
						$pdf->setX(22);
            $pdf->MultiCell(80,3,'Unidades Iniciales: '										.$nCad145[0][1]);
						$pdf->setX(22);
            $pdf->MultiCell(80,3,'Tarifa despues de Unidades Iniciales: '	.number_format($nCad145[0][2],0,'','.'));
						$pdf->Ln(1);
						$pdf->setX(22);
            $pdf->MultiCell(80,3,'Contenedores de 20');
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Tarifa Inicial: '												.number_format($nCad145[1][0],0,'','.'));
						$pdf->setX(22);
            $pdf->MultiCell(80,3,'Unidades Iniciales: '										.$nCad145[1][1]);
						$pdf->setX(22);
            $pdf->MultiCell(80,3,'Tarifa despues de Unidades Iniciales: '	.number_format($nCad145[1][2],0,'','.'));
						$pdf->Ln(1);
						$pdf->setX(22);
            $pdf->MultiCell(80,3,'Contenedores de 40');
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Tarifa Inicial: '												.number_format($nCad145[2][0],0,'','.'));
						$pdf->setX(22);
            $pdf->MultiCell(80,3,'Unidades Iniciales: '										.$nCad145[2][1]);
						$pdf->setX(22);
            $pdf->MultiCell(80,3,'Tarifa despues de Unidades Iniciales: '	.number_format($nCad145[2][2],0,'','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='235'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad235 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Minima: '              .number_format($nCad235[4],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'% Cif Para Granel: '   .$nCad235[0]);
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Contenedor 20: '       .number_format($nCad235[1],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Contenedor 40: '       .number_format($nCad235[2],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Carga Suelta: '        .number_format($nCad235[3],0,'','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='168' || $zMatrizTra[$i]['fcoidxxx']=='247' ||
             $zMatrizTra[$i]['fcoidxxx']=='308' || $zMatrizTra[$i]['fcoidxxx']=='175' ||
             $zMatrizTra[$i]['fcoidxxx']=='252'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad168 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Unidad: '.number_format($nCad168[0],2,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Minima: '.number_format($nCad168[1],2,',','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='169'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad169 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Hora Diurna: '.number_format($nCad169[0],2,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Hora Nocturna: '.number_format($nCad169[1],2,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Hora Dominical: '.number_format($nCad169[2],2,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Hora Festiva: '.number_format($nCad169[3],2,',','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='170' || $zMatrizTra[$i]['fcoidxxx']=='248'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad170 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Fijo: '.number_format($nCad170[0],2,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Minima: '.number_format($nCad170[1],2,',','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='171'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad171 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Sufijo 001: '.number_format($nCad171[0],2,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Sufijo 002: '.number_format($nCad171[1],2,',','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='172'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad172 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Hasta Valor CIF: '.number_format($nCad172[0],2,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Fijo: '.number_format($nCad172[1],2,',','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='177'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad177 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Minima: '.number_format($nCad177[0],2,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Hora Ordinaria: '.number_format($nCad177[1],2,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Hora Festiva: '.number_format($nCad177[2],2,',','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='178'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad178 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Contenedor 20: '.number_format($nCad178[0],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Contenedor 40: '.number_format($nCad178[1],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Unidades Carga Suelta: '.number_format($nCad178[2],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Minima Contenedor 20: '.number_format($nCad178[3],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Minima Contenedor 40: '.number_format($nCad178[4],0,'','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Minima Unidades Carga Suelta: '.number_format($nCad178[5],0,'','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='179' || $zMatrizTra[$i]['fcoidxxx']=='254'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad179 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'% Variable: '.number_format($nCad179[0],2,',','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
         	}

          if($zMatrizTra[$i]['fcoidxxx']=='180'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad180 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Cantidades Iniciales: '.number_format(($nCad180[0]+0),0,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Inicial: '.number_format(($nCad180[1]+0),2,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'% Adicional: '.number_format(($nCad180[2]+0),2,',','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
         	}

          if($zMatrizTra[$i]['fcoidxxx']=='181'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad181 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Dias del Intervalo: '.number_format(($nCad181[0]+0),0,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'% CIF: '.number_format(($nCad181[1]+0),2,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Minima: '.number_format(($nCad181[2]+0),2,',','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
         	}

          if($zMatrizTra[$i]['fcoidxxx']=='182' || $zMatrizTra[$i]['fcoidxxx']=='255'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad182 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Tonelada: '.number_format(($nCad182[0]+0),2,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Cantidad Maxima de Toneladas: '.number_format(($nCad182[1]+0),0,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'% Adicional: '.number_format(($nCad182[2]+0),2,',','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
         	}

          if($zMatrizTra[$i]['fcoidxxx']=='183'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad183 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Estiba: '.number_format(($nCad183[0]+0),2,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Minima Contenedores de 20: '.number_format(($nCad183[1]+0),2,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Minima Contenedores de 40: '.number_format(($nCad183[2]+0),2,',','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
         	}

          if($zMatrizTra[$i]['fcoidxxx']=='184'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad184 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Quincena Vehiculo: '.number_format(($nCad184[0]+0),2,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Quincena Camioneta: '.number_format(($nCad184[1]+0),2,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Quincena Camion: '.number_format(($nCad184[2]+0),2,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Quincena Montacarga: '.number_format(($nCad184[3]+0),2,',','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
         	}

          if($zMatrizTra[$i]['fcoidxxx']=='185'){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad185 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Mes Vehiculo: '.number_format(($nCad185[0]+0),2,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Mes Camioneta: '.number_format(($nCad185[1]+0),2,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Mes Camion: '.number_format(($nCad185[2]+0),2,',','.'));
            $pdf->setX(22);
            $pdf->MultiCell(80,3,'Valor Mes Montacarga: '.number_format(($nCad185[3]+0),2,',','.'));
            $pdf->SetFont('arial','',7);
            $pdf->Ln(3);
         	}

          if($zMatrizTra[$i]['fcoidxxx']=='186' || $zMatrizTra[$i]['fcoidxxx']=='256' ){

            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad186 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->MultiCell(80,3,'% CIF: '.number_format(($nCad186[0]+0),2,',','.'));
            $pdf->Ln(3);
            $pdf->setX(22);
            $pdf->Cell(20,3,'Limite Inferior',1,0,'C');
            $pdf->Cell(20,3,'Limite Superior',1,0,'C');
            $pdf->Cell(20,3,'Valor Fijo',1,0,'C');
            $pdf->Ln(3);
            $zNiveles=explode("!",$nCad186[1]);
            for($e=0; $e<count($zNiveles); $e++){
              if($zNiveles[$e]!=""){
                $zInterno=explode("^",$zNiveles[$e]);
                $pdf->setX(22);
                $pdf->Cell(20,3,number_format($zInterno[0],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[1],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[2],0,'','.'),1,0,'R');
                $pdf->Ln(3);
              }
            }
            $pdf->Ln(3);
          }

          if($zMatrizTra[$i]['fcoidxxx']=='275'){
            $pdf->SetFont('arial','B',6);
            $pdf->MultiCell(80,3,$zMatrizTra[$i]['fcoidxxx'].' - '.$zMatrizTra[$i]['fcodesxx']);

            $nCadena = explode("|",$zMatrizTra[$i]['fcotarxx']);
            $nCad107 = explode("~",$nCadena[1]);

            $pdf->setX(22);
            $pdf->Cell(20,3,'Limite Inferior',1,0,'C');
            $pdf->Cell(20,3,'Limite Superior',1,0,'C');
            $pdf->Cell(20,3,'Valor Fijo $USD',1,0,'C');
            $pdf->Ln(3);
            $zNiveles=explode("!",$nCad107[1]);
            for($e=0; $e<count($zNiveles); $e++){
              if($zNiveles[$e]!=""){
                $zInterno=explode("^",$zNiveles[$e]);
                $pdf->setX(22);
                $pdf->Cell(20,3,number_format($zInterno[0],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,number_format($zInterno[1],0,'','.'),1,0,'R');
                $pdf->Cell(20,3,$zInterno[2],1,0,'R');
                $pdf->Ln(3);
              }
            }
            $pdf->Ln(3);
         }
          //Datos tributarios

          if ($zMatrizTra[$i]['pucrftex'] != "" || $zMatrizTra[$i]['pucaftex'] != "") {
          	$pdf->setX(15);
          	$pdf->SetFont('arial','B',6);
          	$pdf->Cell(170,3,'DATOS TRIBUTARIOS',0,0,'L');
          	$pdf->Ln(3);

          	if ($zMatrizTra[$i]['pucrftex'] != "") {
          		$pdf->setX(15);
          		$pdf->SetFont('arial','',6);
          		$pdf->Cell(170,3,'Retencion en la Fuente por Cliente - Concepto: ['.$zMatrizTra[$i]['pucrftex'].'] '.$zMatrizTra[$i]['pucdesxx'].' - '.$zMatrizTra[$i]['pucretxx'].'%',0,0,'L');
          		$pdf->Ln(3);
          	}

          	if ($zMatrizTra[$i]['pucaftex'] != "") {
          		$pdf->setX(15);
          		$pdf->SetFont('arial','',6);
          		$pdf->Cell(170,3,'Autoretencion en la Fuente por Cliente - Concepto: ['.$zMatrizTra[$i]['pucaftex'].'] '.$zMatrizTra[$i]['pucadesx'].' - '.$zMatrizTra[$i]['pucaretx'].'%',0,0,'L');
          		$pdf->Ln(3);
          	}
          }
          $pdf->Ln(3);
        } ##for ($i=0;$i<count($zMatrizTra);$i++) {##


	$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
	$pdf->Output($cFile);

	if (file_exists($cFile)){
    chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
  } else {
    f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
  }
	echo "<html><script>document.location='$cFile';</script></html>";
  ?>

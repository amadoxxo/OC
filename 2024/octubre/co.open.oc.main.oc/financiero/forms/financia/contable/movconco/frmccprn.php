<?php
  namespace openComex;
  use FPDF;

  /**
	 * Imprime Analisis de Cuentas.
	 * --- Descripcion: Permite Imprimir Estado de cuentas(por Cobrar / por Pagar).
	 * @author Yulieth Campos <ycampos@opentecnologia.com.co>
	 */

  include("../../../../libs/php/utility.php");
  /*$cDate = date('Y-m-d');
  $i = substr($cDate,0,4);*/
  $cADesde = substr($dDesde,0,4);
  $cAHasta = substr($dHasta,0,4);
  //f_Mensaje(__FILE__,__LINE__," tipo $cTipo Cta $cTipoCta NIT $cTerId");
  //f_Mensaje(__FILE__,__LINE__,"  tipo cliente $cTipTer");

  $fec  = date('Y-m-d');
	 $cMes = "";
	  switch (substr($fec,5,2)){
	    case "01":
	      $cMes="ENERO";
	      break;
	    case "02":
	      $cMes="FEBRERO";
	      break;
	    case "03":
	     $cMes="MARZO";
	     break;
	    case "04":
	     $cMes="ABRIL";
	     break;
	    case "05":
	     $cMes="MAYO";
	     break;
	    case "06":
	     $cMes="JUNIO";
	     break;
	    case "07":
	     $cMes="JULIO";
	     break;
	    case "08":
	     $cMes="AGOSTO";
	     break;
	    case "09":
	     $cMes="SEPTIEMBRE";
	     break;
	    case "10":
	     $cMes="OCTUBRE";
	     break;
	    case "11":
	     $cMes="NOVIEMBRE";
	     break;
	    case "12":
	     $cMes="DICIEMBRE";
	     break;
	  }


  $iA=0;
  $mFacPer = array();
	for($cPerAno=$cADesde;$cPerAno<=$cAHasta;$cPerAno++){
		$qFacPer  = "SELECT CONCAT($cAlfa.siai1100.sucidxxx,$cAlfa.siai1100.succodbx) as sucursal_generadora, ";
		$qFacPer .= "$cAlfa.fpar0008.sucidxxx, ";
		$qFacPer .= "$cAlfa.fpar0008.sucdesxx, ";
		$qFacPer .= "$cAlfa.fcoc$cPerAno.comealpo as estado, ";
		$qFacPer .= "$cAlfa.fcod$cPerAno.pucidxxx as cuenta, ";
		$qFacPer .= "$cAlfa.fcod$cPerAno.ctoidxxx as concepto, ";
		$qFacPer .= "$cAlfa.fpar0129.serdesxx as descripcion, ";
		$qFacPer .= "IF($cAlfa.fpar0151.cccsurxx <> CONCAT($cAlfa.siai1100.sucidxxx,$cAlfa.siai1100.succodbx),$cAlfa.fpar0151.cccsurxx,\"\") as intersucursales, ";
		$qFacPer .= "SUM(IF($cAlfa.fcod$cPerAno.commovxx=\"D\",$cAlfa.fcod$cPerAno.comvlrxx,0)) as debitos, ";
		$qFacPer .= "SUM(IF($cAlfa.fcod$cPerAno.commovxx=\"C\",$cAlfa.fcod$cPerAno.comvlrxx,0)) as creditos ";
		$qFacPer .= "FROM $cAlfa.fcod$cPerAno ";
		$qFacPer .= "LEFT JOIN $cAlfa.fcoc$cPerAno ON $cAlfa.fcod$cPerAno.comidxxx = $cAlfa.fcoc$cPerAno.comidxxx AND $cAlfa.fcod$cPerAno.comcodxx = $cAlfa.fcoc$cPerAno.comcodxx AND $cAlfa.fcod$cPerAno.comcscxx = $cAlfa.fcoc$cPerAno.comcscxx AND $cAlfa.fcod$cPerAno.comcsc2x = $cAlfa.fcoc$cPerAno.comcsc2x ";
		$qFacPer .= "LEFT JOIN $cAlfa.fpar0117 ON $cAlfa.fcod$cPerAno.comidxxx = $cAlfa.fpar0117.comidxxx AND $cAlfa.fcod$cPerAno.comcodxx = $cAlfa.fpar0117.comcodxx ";
		$qFacPer .= "LEFT JOIN $cAlfa.siai1100 ON $cAlfa.fpar0117.sucidxxx = $cAlfa.siai1100.admidxxx ";
		$qFacPer .= "LEFT JOIN $cAlfa.fpar0151 ON $cAlfa.fcoc$cPerAno.terid2xx = $cAlfa.fpar0151.cliidxxx ";
		$qFacPer .= "LEFT JOIN $cAlfa.fpar0129 ON $cAlfa.fcod$cPerAno.ctoidxxx = $cAlfa.fpar0129.ctoidxxx ";
		$qFacPer .= "LEFT JOIN $cAlfa.fpar0008 ON $cAlfa.fcod$cPerAno.ccoidxxx = $cAlfa.fpar0008.ccoidxxx ";
		$qFacPer .= "WHERE ";
		if($cComeAlpo <> ""){
			$qFacPer .= "$cAlfa.fcoc$cPerAno.comealpo = \"$cComeAlpo\" ";
		}else{
			$qFacPer .= "$cAlfa.fcoc$cPerAno.comealpo IN (\"CONTABILIZADO\",\"ANULADO\") ";
		}
		$qFacPer .= "AND $cAlfa.fcoc$cPerAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" ";
		if($cCcoId <> ""){
			$qFacPer .= " AND $cAlfa.fcod$cPerAno.ccoidxxx = \"$cCcoId\" ";
		}
		$qFacPer .= "GROUP BY sucursal_generadora,intersucursales,$cAlfa.fcod$cPerAno.ctoidxxx ";
		$qFacPer .= "ORDER BY sucursal_generadora,intersucursales,$cAlfa.fcod$cPerAno.ctoidxxx ";

		//f_Mensaje(__FILE__,__LINE__,$qFacPer);
		$xFacPer  = f_MySql("SELECT","",$qFacPer,$xConexion01,"");
		// Cargo la Matriz con los ROWS del Cursor
		while ($xRFP = mysql_fetch_array($xFacPer)) {
			/**
			 * Cargando concepto si no existe en la fpar0129
			 */
			if($xRFP['descripcion'] == ""){
				$qFacCto  = "SELECT * ";
				$qFacCto .= "FROM $cAlfa.fpar0119 ";
				$qFacCto .= "WHERE ";
				$qFacCto .= "$cAlfa.fpar0119.ctoidxxx = {$xRFP['concepto']} LIMIT 0,1 ";
				$xFacCto  = f_MySql("SELECT","",$qFacCto,$xConexion01,"");
				$vFacCto  = mysql_fetch_array($xFacCto);
				$nFilFac  = mysql_num_rows($xFacCto);
				if($nFilFac > 0){
					$xRFP['descripcion'] = $vFacCto['ctodesxf'];
				}else{
				  $qDesPuc  = "SELECT * ";
				  $qDesPuc .= "FROM $cAlfa.fpar0115 ";
				  $qDesPuc .= "WHERE ";
				  $qDesPuc .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = {$xRFP['concepto']} LIMIT 0,1 ";
				  //f_Mensaje(__FILE__,__LINE__,$qDesPuc);
				  $xDesPuc  = f_MySql("SELECT","",$qDesPuc,$xConexion01,"");
				  $vDesPuc = mysql_fetch_array($xDesPuc);
				  $nDesPuc  = mysql_num_rows($xDesPuc);
				  if($nDesPuc > 0){
					 $xRFP['descripcion'] = $vDesPuc['pucdesxx'];
				  }
				}

			}
			/**
			* Buscando el primer DO
			*/
			$mDo = f_Explode_Array($xRFP['comfpxxx'],"|","~");
			$xRFP['doidxxxx'] = $mDo[0][15]."-".$mDo[0][2]."-".$mDo[0][3];
			$mFacPer[$iA] = $xRFP;
			$iA++;
		}
	}

  switch ($cTipo) {
	case 1:
	// PINTA POR PANTALLA//
	?>
	<html>
		<head><title>Movimiento Contable Consolidado</title>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
		<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
	  </head>
	<body>
  <?php if (count($mFacPer)>0) { ?>
    <center>
			<fieldset>
				<legend><h4> Resultado consulta</h4></legend>
				<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border: 1px solid black;">
					<tr>
						<td class="name"><center><h3 style="margin-bottom:5px">MOVIMIENTO CONTABLE CONSOLIDADO</h3></center></td>
					</tr>
					<?php if($cCcoId <>"") { ?>
						<tr>
							<td style="border-bottom: hidden;font-size:14px;font-weight:bold" class="name"><center>SUCURSAL: <?php echo "[ ".$mFacPer[0]['sucidxxx']." ]  ".$mFacPer[0]['sucdesxx'] ?></center></td>
						</tr>
					<?php } ?>
					<tr>
						<td style="border-bottom: hidden;font-size:14px;font-weight:bold" class="name"><center>PERIODO: <?php echo "DE  ".$dDesde." A  ".$dHasta ?></center><br></td>
					</tr>
				</table>
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding:5px;border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
         <tr>
            <td><b>FECHA Y HORA DE CONSULTA:</b>&nbsp;&nbsp; <?php echo date('Y-m-d')."-".date('H:i:s') ?></td>
            <td align="right"><b>TOTAL NUMERO DE REGISTROS:</b>&nbsp;&nbsp; <?php echo count($mFacPer) ?></td>
          </tr>
        </table>
	        <table width="100%" cellpadding="1" cellspacing="1" border="1">
						<tr bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>" style="vertical-align:center;text-align:center">
							<td class="name" rowspan=2>SUCURSAL GENERADORA</td>
							<td class="name" colspan=2>CUENTA CONTABLE</td>
							<td class="name" rowspan=2>INTERSUCURSALES</td>
							<td class="name" colspan=2>MOVIMIENTO</td>
							<td class="name" rowspan=2>ESTADO</td>
						</tr>
						<tr bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>" style="vertical-align:center;text-align:center">
							<td class="name">CODIGO</td>
							<td class="name">DESCRIPCION</td>
							<td class="name">DEBITO</td>
							<td class="name">CREDITO</td>
						</tr>
						<?php
						$color = '#D5D5D5';
						$nTotDeb = 0;
						$nTotCre = 0;
						for($j=0;$j<count($mFacPer);$j++){
							$nTotDeb += $mFacPer[$j]['debitos'];
							$nTotCre += $mFacPer[$j]['creditos'];
							?>
							<tr bgcolor="<?php echo $color ?>">
								<td class="letra7" style="padding-left:5px;padding-right:5px;text-align:center"><?php echo $mFacPer[$j]['sucursal_generadora'] ?></td>
								<td class="letra7" style="padding-left:5px;padding-right:5px;text-align:center"><?php echo $mFacPer[$j]['cuenta'] ?></td>
								<td class="letra7" style="padding-left:5px;padding-right:5px;"><?php echo $mFacPer[$j]['descripcion'] ?>&nbsp;</td>
								<td class="letra7" style="padding-left:5px;padding-right:5px;text-align:center"><?php echo $mFacPer[$j]['intersucursales'] ?>&nbsp;</td>
								<td class="letra7" style="padding-left:5px;padding-right:5px;text-align:right""><?php echo number_format($mFacPer[$j]['debitos'],0,',','.') ?></td>
								<td class="letra7" style="padding-left:5px;padding-right:5px;text-align:right""><?php echo number_format($mFacPer[$j]['creditos'],0,',','.') ?></td>
								<td class="letra7" style="padding-left:5px;padding-right:5px;"><?php echo $mFacPer[$j]['estado'] ?></td>
							</tr>
						<?php } ?>
						<tr>
							<td class="name"colspan="4" style="padding-left:5px;padding-right:5px;text-align:right">TOTALES:</td>
							<td class="name"style="padding-left:5px;padding-right:5px;text-align:right"><?php echo number_format($nTotDeb,0,',','.') ?></td>
							<td class="name"style="padding-left:5px;padding-right:5px;text-align:right"><?php echo number_format($nTotCre,0,',','.') ?></td>
							<td class="name">&nbsp;</td>
						</tr>
	      	</table>
			</fieldset>
		</center>
	<?php } else {  echo "No se Generaron Registros.";  }   ?>
	</body>
	</html>
  <?php
  break;
  case 2:
	// PINTA POR EXCEL//
 	if (count($mFacPer) > 0) {

 		$header .= 'MOVIMIENTO CONTABLE CONSOLIDADO\n';
	  $header .= "\n";
		$data = '';
		$title = 'MOVIMIENTO CONTABLE CONSOLIDADO.xls';

			$data .= '<table width="1200" cellpadding="0" cellspacing="0" border="1">';
			$data .= '<tr>';
			$data .= '<td colspan="7" class="name"><center><h3 style="margin-bottom:5px">MOVIMIENTO CONTABLE CONSOLIDADO</h3></center></td>';
			$data .= '</tr>';
			if($cCcoId <>"") {
				$data .= '<tr>';
				$data .= '<td colspan="7" style="font-size:14px;font-weight:bold" class="name"><center>SUCURSAL: [ '.$mFacPer[0]['sucidxxx'].' ]  '.$mFacPer[0]['sucdesxx'].'</center></td>';
				$data .= '</tr>';
			}
			$data .= '<tr>';
			$data .= '<td colspan="7" style="font-size:14px;font-weight:bold" class="name"><center>PERIODO: DE  '.$dDesde.' A  '.$dHasta.'</center></td>';
			$data .= '</tr>';
			$data .= '<tr>';
			$data .= '<td colspan="4"><b>FECHA Y HORA DE CONSULTA:</b>  '.date('Y-m-d').'-'.date('H:i:s').'</td>';
			$data .= '<td colspan="3" align="right"><b>TOTAL NUMERO DE REGISTROS:</b> '.count($mFacPer).'</td>';
			$data .= '</tr>';
			$data .= '<tr style="vertical-align:center;text-align:center;font-weight:bold">';
			$data .= '<td rowspan=2>SUCURSAL GENERADORA</td>';
			$data .= '<td colspan=2>CUENTA CONTABLE</td>';
			$data .= '<td rowspan=2>INTERSUCURSALES</td>';
			$data .= '<td colspan=2>MOVIMIENTO</td>';
			$data .= '<td rowspan=2>ESTADO</td>';
			$data .= '</tr>';
			$data .= '<tr style="vertical-align:center;text-align:center;font-weight:bold">';
			$data .= '<td class="name">CODIGO</td>';
			$data .= '<td class="name">DESCRIPCION</td>';
			$data .= '<td class="name">DEBITO</td>';
			$data .= '<td class="name">CREDITO</td>';
			$data .= '</tr>';

			$nTotDeb = 0;
			$nTotCre = 0;
			for($j=0;$j<count($mFacPer);$j++){
				$nTotDeb += $mFacPer[$j]['debitos'];
				$nTotCre += $mFacPer[$j]['creditos'];
				$data .= '<tr>';
				$data .= '<td style="padding-left:5px;padding-right:5px;text-align:center">'.$mFacPer[$j]['sucursal_generadora'].'</td>';
				$data .= '<td style="padding-left:5px;padding-right:5px;text-align:center">'.$mFacPer[$j]['cuenta'].'</td>';
				$data .= '<td style="padding-left:5px;padding-right:5px;">'.$mFacPer[$j]['descripcion'].'</td>';
				$data .= '<td style="padding-left:5px;padding-right:5px;text-align:center">'.$mFacPer[$j]['intersucursales'].'</td>';
				$data .= '<td style="padding-left:5px;padding-right:5px;text-align:rigth">'.number_format($mFacPer[$j]['debitos'],0,',','').'</td>';
				$data .= '<td style="padding-left:5px;padding-right:5px;text-align:right"">'.number_format($mFacPer[$j]['debitos'],0,',','').'</td>';
				$data .= '<td style="padding-left:5px;padding-right:5px;text-align:right"">'.number_format($mFacPer[$j]['creditos'],0,',','').'</td>';
				$data .= '<td style="padding-left:5px;padding-right:5px;">'.$mFacPer[$j]['estado'].'</td>';
				$data .= '</tr>';
			}
			$data .= '<tr>';
			$data .= '<td class="name"colspan="4" style="padding-left:5px;padding-right:5px;text-align:right;font-weight:bold;font-size:16px;">TOTALES:</td>';
			$data .= '<td class="name"style="padding-left:5px;padding-right:5px;text-align:right;font-size:16px;font-weight:bold;">'.number_format($nTotDeb,0,',','').'</td>';
			$data .= '<td class="name"style="padding-left:5px;padding-right:5px;text-align:right;font-size:16px;font-weight:bold;">'.number_format($nTotCre,0,',','').'</td>';
			$data .= '<td class="name"></td>';
			$data .= '</tr>';

			$data .= '<tr>';
			$data .= '<td colspan="7"></td>';
			$data .= '</tr>';
			$data .= '<tr>';
			$data .= '<td colspan="3" style="padding-left:5px;padding-right:5px;font-weight:bold">Vo.Bo. FACTURACION</td>';
			$data .= '<td colspan="4" style="padding-left:5px;padding-right:5px;font-weight:bold">Vo.Bo. CONTABILIDAD</td>';
			$data .= '</tr>';
			$data .= '<td colspan="3" height="50" style="padding-left:5px;padding-right:5px;font-weight:bold"></td>';
			$data .= '<td colspan="4" style="padding-left:5px;padding-right:5px;font-weight:bold"></td>';
			$data .= '</tr>';
		$data .= '<td colspan="3" style="padding-left:5px;padding-right:5px;font-weight:bold">Nombre</td>';
			$data .= '<td colspan="4" style="padding-left:5px;padding-right:5px;font-weight:bold">Nombre</td>';
			$data .= '</tr>';
			$data .= '</table>';

      header("Pragma: public");
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Cache-Control: private",false); // required for certain browsers
      header("Content-type: application/octet-stream");
      header("Content-Disposition: attachment; filename=\"".basename($title)."\";");

			print $data;
	 } else {  echo "No se Generaron Registros.";  }
  break;
  	case 3 :
    /* PINTA POR PDF */
		if (count($mFacPer) > 0) {

			 $cAddr = "";
		  if ($cAlfa == "DESARROL" || $cAlfa == "PRUEBASX"){
		    $cAddr = "../";
		  }

		  $cRoot = $_SERVER['DOCUMENT_ROOT'];

			$cSucId = $mFacPer[0]['sucidxxx'];
			$cSucDes = $mFacPer[0]['sucdesxx'];

			define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
		  require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');

		  class PDF extends FPDF {
				function Header() {
					global $cRoot; global $cPlesk_Skin_Directory;
					global $cAlfa; global $cTipoCta; global $dDesde; global $dHasta; global $nPag; global $cSucId; global $cSucDes; global $cCcoId;

				  if($cAlfa == "ALPOPULX" || $cAlfa == "DESARROL" || $cAlfa == "PRUEBASX"){

				  	$this->SetXY(13,7);
				  	$this->Cell(72,28,'',1,0,'C');
				  	$this->Cell(183,28,'',1,0,'C');

	 					// Dibujo //
	 					$this->Image($cRoot.$cPlesk_Skin_Directory.'/alpopul1.jpg',14,8,70,25);

						$this->SetFont('verdana','',16);
						$this->SetXY(85,12);
						$this->Cell(183,8,'MOVIMIENTO CONTABLE CONSOLIDADO',0,0,'C');
						$this->Ln(8);
						$this->SetFont('verdana','',12);
						if($cCcoId <>"") {
							$this->SetX(85);
							$this->Cell(183,6,'SUCURSAL: [ '.$cSucId.' ]  '.$cSucDes,0,0,'C');
							$this->Ln(5);
						}
						$this->SetX(85);
						$this->Cell(183,6,'PERIODO: DE  '.$dDesde.' A  '.$dHasta,0,0,'C');
						$this->Ln(15);
						$this->SetX(13);
				  }else{
				  	$this->SetXY(13,7);
					  $this->Cell(255,22,'',1,0,'C');

					  $this->SetFont('verdana','',16);
						$this->SetXY(13,8);
						$this->Cell(255,8,'MOVIMIENTO CONTABLE CONSOLIDADO',0,0,'C');
						$this->Ln(8);
						$this->SetFont('verdana','',12);
						if($cCcoId <>"") {
							$this->SetX(13);
							$this->Cell(255,6,'SUCURSAL: [ '.$cSucId.' ]  '.$cSucDes,0,0,'C');
							$this->Ln(5);
						}
						$this->SetX(13);
						$this->Cell(255,6,'PERIODO: DE  '.$dDesde.' A  '.$dHasta,0,0,'C');
						$this->Ln(10);
						$this->SetX(13);
				  }

				  if($this->PageNo() > 1 && $nPag ==1){
						$this->SetX(13);
						$this->SetX(13);
						$this->SetFont('verdana','B',6);
						$this->Cell(35,10,"SUCURSAL GENERADORA",1,0,'C');
						$this->Cell(110,5,"CUENTA CONTABLE",1,0,'C');
						$this->Cell(25,10,"INTERSUCURSALES",1,0,'C');
						$this->Cell(60,5,"MOVIMIENTO",1,0,'C');
						$this->Cell(25,10,"ESTADO",1,0,'C');

						$nPosY = $this->GetY() + 5;
						$this->SetXY(48,$nPosY);
						$this->Cell(30,5,"CODIGO",1,0,'C');
						$this->Cell(80,5,"DESCRIPCION",1,0,'C');

						$this->SetXY(183,$nPosY);
						$this->Cell(30,5,"DEBITO",1,0,'C');
						$this->Cell(30,5,"CREDITO",1,0,'C');
						$this->Ln(5);

						$this->SetFont('verdana','',6);
						$this->SetWidths(array('35','30','80','25','30','30','25'));
						$this->SetAligns(array('C','C','L','C','R','R','C'));
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
			    $h=3*$nb;
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
			        $this->MultiCell($w,3,$data[$i],0,$a);
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

			$pdf->SetX(13);
			$pdf->SetFont('verdana','B',6);
			$pdf->Cell(40,5,"FECHA Y HORA DE CONSULTA:",0,0,'L');
			$pdf->SetFont('verdana','',8);
			$pdf->Cell(78,5,date('Y-m-d')."-".date('H:i:s'),0,0,'L');
			$pdf->SetFont('verdana','B',6);
			$pdf->Cell(107,5,"TOTAL NUMERO DE REGISTROS:",0,0,'R');
			$pdf->SetFont('verdana','',8);
			$pdf->Cell(30,5,count($mFacPer),0,0,'C');


			$pdf->Ln(8);
			$pdf->SetX(13);
			$pdf->SetFont('verdana','B',6);
			$pdf->Cell(35,10,"SUCURSAL GENERADORA",1,0,'C');
			$pdf->Cell(110,5,"CUENTA CONTABLE",1,0,'C');
			$pdf->Cell(25,10,"INTERSUCURSALES",1,0,'C');
			$pdf->Cell(60,5,"MOVIMIENTO",1,0,'C');
			$pdf->Cell(25,10,"ESTADO",1,0,'C');

			$nPosY = $pdf->GetY() + 5;
			$pdf->SetXY(48,$nPosY);
			$pdf->Cell(30,5,"CODIGO",1,0,'C');
			$pdf->Cell(80,5,"DESCRIPCION",1,0,'C');

			$pdf->SetXY(183,$nPosY);
			$pdf->Cell(30,5,"DEBITO",1,0,'C');
			$pdf->Cell(30,5,"CREDITO",1,0,'C');
			$pdf->Ln(5);

				$nPag=0;

				$nTotDeb = 0;
				$nTotCre = 0;
				$pdf->SetFont('verdana','',6);
				$pdf->SetWidths(array('35','30','80','25','30','30','25'));
				$pdf->SetAligns(array('C','C','L','C','R','R','C'));
				for($j=0;$j<count($mFacPer);$j++){
					$nTotDeb += $mFacPer[$j]['debitos'];
					$nTotCre += $mFacPer[$j]['creditos'];
					$pdf->SetX(13);
						$pdf->Row(array($mFacPer[$j]['sucursal_generadora'],
										 				$mFacPer[$j]['cuenta'],
										 				$mFacPer[$j]['descripcion'],
										 				$mFacPer[$j]['intersucursales'],
										 				number_format($mFacPer[$j]['debitos'],0,',','.'),
										 				number_format($mFacPer[$j]['creditos'],0,',','.'),
										 				$mFacPer[$j]['estado']));
				}


			$nPag=0;

			$pdf->SetX(13);
			$pdf->SetFont('verdana','B',6);
			$pdf->Cell(170,5,"TOTALES",1,0,'R');
			$pdf->Cell(30,5,number_format($nTotDeb,0,',','.'),1,0,'R');
			$pdf->Cell(30,5,number_format($nTotCre,0,',','.'),1,0,'R');
			$pdf->Cell(25,5,"",1,0,'C');
			$pdf->Ln(10);

			$pdf->SetX(13);
			$pdf->SetFont('verdana','B',6);
			$pdf->Cell(128,5,"Vo.Bo. FACTURACION:",0,0,'L');
			$pdf->Cell(127,5,"Vo.Bo. CONTABILIDAD:",0,0,'L');
			$pdf->Ln(10);
			$pdf->SetX(13);
			$pdf->Line(13,$pdf->GetY(),50,$pdf->GetY());
			$pdf->SetX(141);
			$pdf->Line(141,$pdf->GetY(),191,$pdf->GetY());
			$pdf->SetX(13);
			$pdf->Cell(128,5,"Nombre",0,0,'L');
			$pdf->Cell(127,5,"Nombre",0,0,'L');

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
  }
?>
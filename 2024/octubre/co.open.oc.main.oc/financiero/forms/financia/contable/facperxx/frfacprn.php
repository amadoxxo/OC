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

		/**INICIO SQL**/
		$qFacPer  = "SELECT DISTINCT ";
		$qFacPer .= "$cAlfa.fcoc$cPerAno.comidxxx,";
		$qFacPer .= "$cAlfa.fcoc$cPerAno.comcodxx,";
		$qFacPer .= "$cAlfa.fcoc$cPerAno.comcscxx,";
		$qFacPer .= "$cAlfa.fcoc$cPerAno.comcsc2x,";
		$qFacPer .= "$cAlfa.fcoc$cPerAno.comfecxx,";
		$qFacPer .= "$cAlfa.fcoc$cPerAno.comfecve,";
		$qFacPer .= "$cAlfa.fcoc$cPerAno.comfcaxx,";
		$qFacPer .= "$cAlfa.fcoc$cPerAno.comealpo,";
		$qFacPer .= "$cAlfa.fcoc$cPerAno.comfpxxx,";
		$qFacPer .= "$cAlfa.fcoc$cPerAno.teridxxx,";
		$qFacPer .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,\"CLIENTE SIN NOMBRE\") AS clinomxx, ";
		///sALDO A FAVOR
		$qFacPer .= "SUM(IF($cAlfa.fcod$cPerAno.comctocx='SC',$cAlfa.fcod$cPerAno.comvlrxx,0)) as saldofav, ";
		///sALDO A CARGO
		$qFacPer .= "SUM(IF($cAlfa.fcod$cPerAno.comctocx='SS',$cAlfa.fcod$cPerAno.comvlrxx,0)) as saldocar ";

		$qFacPer .= "FROM $cAlfa.fcoc$cPerAno ";
		$qFacPer .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcoc$cPerAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
		$qFacPer .= "LEFT JOIN $cAlfa.fcod$cPerAno ON $cAlfa.fcod$cPerAno.comidxxx = $cAlfa.fcoc$cPerAno.comidxxx AND $cAlfa.fcod$cPerAno.comcodxx = $cAlfa.fcoc$cPerAno.comcodxx AND $cAlfa.fcod$cPerAno.comcscxx = $cAlfa.fcoc$cPerAno.comcscxx AND $cAlfa.fcod$cPerAno.comcsc2x = $cAlfa.fcoc$cPerAno.comcsc2x ";

		$qFacPer .= "WHERE $cAlfa.fcoc$cPerAno.comidxxx = \"F\" AND ";
		$qFacPer .= "$cAlfa.fcoc$cPerAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" ";
		if($cCcoId != ""){
			$qFacPer .= " AND $cAlfa.fcoc$cPerAno.ccoidxxx = \"$cCcoId\" ";
		}

		//Valida si el tipo de Facturacion es Estandar
		if($cFacId == "ESTANDAR"){
			$qFacPer .= "AND $cAlfa.fcoc$cPerAno.comobs2x NOT LIKE \"%PEDIDOSAP%\" ";
			if($cEstId != ""){
				$qFacPer .= "AND $cAlfa.fcoc$cPerAno.comealpo = \"$cEstId\" ";
			}else {
				$qFacPer .= "AND $cAlfa.fcoc$cPerAno.comealpo IN (\"CONTABILIZADO\",\"ANULADO\") ";
			}

		//Valida si el tipo de Facturacion es por Pedido
		}elseif($cFacId == "PEDIDO"){
			$qFacPer .= "AND $cAlfa.fcoc$cPerAno.comobs2x LIKE \"%PEDIDOSAP%\" ";
			if($cEstId != ""){
				$qFacPer .= "AND $cAlfa.fcoc$cPerAno.comealpo = \"$cEstId\" ";
			}else {
				$qFacPer .= "AND $cAlfa.fcoc$cPerAno.comealpo IN (\"ACTIVO\",\"PENDIENTE\",\"FACTURADO\",\"RECHAZADO\",\"NOTA_CREDITO\") ";
			}
		}

		if($cTerId != ""){
			$qFacPer .= " AND $cAlfa.fcoc$cPerAno.teridxxx = \"$cTerId\" ";
		}

		$qFacPer .= "GROUP BY $cAlfa.fcoc$cPerAno.comidxxx,$cAlfa.fcoc$cPerAno.comcodxx,$cAlfa.fcoc$cPerAno.comcscxx,$cAlfa.fcoc$cPerAno.comcsc2x ";
		$qFacPer .= "ORDER BY $cAlfa.fcoc$cPerAno.teridxxx,$cAlfa.fcoc$cPerAno.comidxxx,ABS($cAlfa.fcoc$cPerAno.comcodxx),ABS($cAlfa.fcoc$cPerAno.comcscxx),ABS($cAlfa.fcoc$cPerAno.comcsc2x) ASC ";

		//f_Mensaje(__FILE__,__LINE__,$qFacPer);
		$xFacPer  = f_MySql("SELECT","",$qFacPer,$xConexion01,"");

		// Cargo la Matriz con los ROWS del Cursor
		while ($xRFP = mysql_fetch_array($xFacPer)) {
			/**
			* Buscando el primer DO
			*/
			$mDo = f_Explode_Array($xRFP['comfpxxx'],"|","~");
			$xRFP['doidxxxx'] = $mDo[0][15]."-".$mDo[0][2]."-".$mDo[0][3];
			//Buscando Operacion
			$qTipOpe = "SELECT doctipxx FROM $cAlfa.sys00121 WHERE sucidxxx = \"{$mDo[0][15]}\"  AND docidxxx = \"{$mDo[0][2]}\" AND docsufxx= \"{$mDo[0][3]}\" LIMIT 0,1";
			$xTipOpe  = f_MySql("SELECT","",$qTipOpe,$xConexion01,"");
			$xRTO = mysql_fetch_array($xTipOpe);
			$xRFP['doctipxx'] = ($xRTO['doctipxx'] != "")?$xRTO['doctipxx']:"OPERACION SIN NOMBRE";
			$mFacPer[$iA] = $xRFP;
			$iA++;
		}
	}

  switch ($cTipo) {
	case 1:
	// PINTA POR PANTALLA//
	?>
	<html>
		<head><title>Facturas Emitidas para un Periodo</title>
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
						<td class="name"><center><h3 style="margin-bottom:5px">FACTURAS EMITIDAS</h3></center></td>
					</tr>
					<tr>
						<td style="border-bottom: hidden;font-size:14px;font-weight:bold" class="name"><center>PERIODO: <?php echo "DE  ".$dDesde." A  ".$dHasta ?></center><br></td>
					</tr>
				</table>
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding:5px;border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
         <tr>
            <td><b>FECHA Y HORA DE CONSULTA:</b>&nbsp;&nbsp; <?php echo date('Y-m-d')."-".date('H:i:s') ?></td>
            <td align="right"><b>TOTAL NUMERO DE FACTURAS:</b>&nbsp;&nbsp; <?php echo count($mFacPer) ?></td>
          </tr>
        </table>
	        <table width="100%" cellpadding="1" cellspacing="1" border="1">
	        	<tr bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>" style="vertical-align:center;text-align:center">
							<td class="name" rowspan="02" width="100">NIT</td>
							<td class="name" rowspan="02">NOMBRE CLIENTE</td>
							<td class="name" rowspan="02">FACTURA</td>
							<td class="name" rowspan="02">FECHA</td>
							<td class="name" rowspan="02">DO</td>
							<td class="name" rowspan="02">OPERACION</td>
							<td class="name" colspan="02">RESULTADOS</td>
							<?php if($cFacId == "PEDIDO"){ ?>
								<td class="name" rowspan="02">ESTADO PEDIDO</td>
							<?php }else{ ?>
								<td class="name" rowspan="02">ESTADO</td>
							<?php } ?>
						</tr>
						<tr bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>" style="vertical-align:center;text-align:center">
							<td class="name">A CARGO</td>
							<td class="name">A FAVOR</td>
						</tr>

						<?php
						//Pinta cxc o cxp si el Movimiento es Debito
						$color = '#D5D5D5';
						$nTotFav = 0;
						$nTotCar = 5;
						for($j=0;$j<count($mFacPer);$j++){
							$nTotFav += $mFacPer[$j]['saldofav'];
							$nTotCar += $mFacPer[$j]['saldocar'];
							?>
								<tr bgcolor="<?php echo $color ?>">
									<td class="letra7" style="padding-left:5px;padding-right:5px;"><?php echo $mFacPer[$j]['teridxxx'] ?></td>
									<td class="letra7" style="padding-left:5px;padding-right:5px;"><?php echo $mFacPer[$j]['clinomxx'] ?></td>
									<td class="letra7" style="padding-left:5px;padding-right:5px;"><?php echo $mFacPer[$j]['comidxxx']."-".$mFacPer[$j]['comcodxx']."-".$mFacPer[$j]['comcscxx'] ?></td>
									<td class="letra7" style="padding-left:5px;padding-right:5px;"><?php echo $mFacPer[$j]['comfecxx'] ?></td>
									<td class="letra7" style="padding-left:5px;padding-right:5px;"><?php echo $mFacPer[$j]['doidxxxx'] ?></td>
									<td class="letra7" style="padding-left:5px;padding-right:5px;"><?php echo $mFacPer[$j]['doctipxx'] ?></td>
									<td class="letra7" style="padding-left:5px;padding-right:5px;text-align:right"><?php echo number_format($mFacPer[$j]['saldocar'],0,',','.') ?></td>
									<td class="letra7" style="padding-left:5px;padding-right:5px;text-align:right"><?php echo number_format($mFacPer[$j]['saldofav'],0,',','.') ?></td>
									<td class="letra7" style="padding-left:5px;padding-right:5px;"><?php echo str_replace("_", " ", $mFacPer[$j]['comealpo']) ?></td>
								</tr>
						<?php } ?>
						<tr>
							<td class="name" colspan="6" style="padding-left:5px;padding-right:5px;text-align:right">TOTALES:</td>
							<td class="name" style="padding-left:5px;padding-right:5px;text-align:right"><?php echo number_format($nTotCar,0,',','.') ?></td>
							<td class="name" style="padding-left:5px;padding-right:5px;text-align:right"><?php echo number_format($nTotFav,0,',','.') ?></td>
							<td class="name" style="padding-left:5px;padding-right:5px;">&nbsp;</td>
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

 		$header .= 'FACTURAS EMITIDAS\n';
	  $header .= "\n";
		$data = '';
		$title = 'FACTURAS EMITIDAS.xls';

			$data .= '<table width="1200" cellpadding="0" cellspacing="0" border="1">';
			$data .= '<tr>';
			$data .= '<td colspan="9"><center><h3 style="margin-bottom:5px">FACTURAS EMITIDAS</h3></center></td>';
			$data .= '</tr>';
			$data .= '<tr>';
			$data .= '<td colspan="9" style="font-size:14px;font-weight:bold"><center>PERIODO: DE  '.$dDesde.' A  '.$dHasta.'</center><br></td>';
			$data .= '</tr>';
			$data .= '<tr>';
			$data .= '<td colspan="4"><b>FECHA Y HORA DE CONSULTA:</b>  '.date('Y-m-d').'-'.date('H:i:s').'</td>';
			$data .= '<td colspan="4" align="right"><b>TOTAL NUMERO DE FACTURAS:</b> '.count($mFacPer).'</td>';
			$data .= '</tr>';
			$data .= '<tr style="vertical-align:center;text-align:center;font-weight:bold">';
			$data .= '<td rowspan="02" width="100">NIT</td>';
			$data .= '<td rowspan="02">NOMBRE CLIENTE</td>';
			$data .= '<td rowspan="02">FACTURA</td>';
			$data .= '<td rowspan="02">FECHA</td>';
			$data .= '<td rowspan="02">DO</td>';
			$data .= '<td rowspan="02">OPERACION</td>';
			$data .= '<td colspan="02">RESULTADOS</td>';
			if($cFacId == "PEDIDO"){
				$data .= '<td rowspan="02">ESTADO PEDIDO</td>';
			}else{
				$data .= '<td rowspan="02">ESTADO</td>';
			}
			$data .= '</tr>';
			$data .= '<tr style="vertical-align:center;text-align:center;font-weight:bold">';
			$data .= '<td>A CARGO</td>';
			$data .= '<td>A FAVOR</td>';
			$data .= '</tr>';

			//Pinta cxc o cxp si el Movimiento es Debito
			$color = '#D5D5D5';
			$nTotFav = 0;
			$nTotCar = 0;
			for($j=0;$j<count($mFacPer);$j++){
			$nTotFav += $mFacPer[$j]['saldofav'];
			$nTotCar += $mFacPer[$j]['saldocar'];
				$data .= '<tr>';
				$data .= '<td style="padding-left:5px;padding-right:5px;">'.$mFacPer[$j]['teridxxx'].'</td>';
				$data .= '<td style="padding-left:5px;padding-right:5px;">'.$mFacPer[$j]['clinomxx'].'</td>';
				$data .= '<td style="padding-left:5px;padding-right:5px;">'.$mFacPer[$j]['comidxxx'].'-'.$mFacPer[$j]['comcodxx'].'-'.$mFacPer[$j]['comcscxx'].'</td>';
				$data .= '<td style="padding-left:5px;padding-right:5px;">'.$mFacPer[$j]['comfecxx'].'</td>';
				$data .= '<td style="padding-left:5px;padding-right:5px;">'.$mFacPer[$j]['doidxxxx'].'</td>';
				$data .= '<td style="padding-left:5px;padding-right:5px;">'.$mFacPer[$j]['doctipxx'].'</td>';
				$data .= '<td style="padding-left:5px;padding-right:5px;text-align:right">'.number_format($mFacPer[$j]['saldocar'],0,',','').'</td>';
				$data .= '<td style="padding-left:5px;padding-right:5px;text-align:right">'.number_format($mFacPer[$j]['saldofav'],0,',','').'</td>';
				$data .= '<td style="padding-left:5px;padding-right:5px;">'.str_replace("_", " ", $mFacPer[$j]['comealpo']).'</td>';
				$data .= '</tr>';
			}
			$data .= '<tr>';
			$data .= '<td colspan="5" style="padding-left:5px;padding-right:5px;text-align:right;font-weight:bold">TOTALES:</td>';
			$data .= '<td style="padding-left:5px;padding-right:5px;text-align:right;font-weight:bold">'.number_format($nTotCar,0,',','').'</td>';
			$data .= '<td style="padding-left:5px;padding-right:5px;text-align:right;font-weight:bold">'.number_format($nTotFav,0,',','').'</td>';
			$data .= '<td style="padding-left:5px;padding-right:5px;"></td>';
			$data .= '</tr>';
			$data .= '<tr>';
			$data .= '<td colspan="9"></td>';
			$data .= '</tr>';
			$data .= '<tr>';
			$data .= '<td colspan="9"></td>';
			$data .= '</tr>';
			$data .= '<tr>';
			$data .= '<td style="padding-left:5px;padding-right:5px;;font-weight:bold;text-align:right">REVISO:</td>';
			$data .= '<td colspan="7"  height="50" style="padding-left:5px;padding-right:5px;"></td>';
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

		  class PDF extends FPDF {
				function Header() {
					global $cRoot; global $cPlesk_Skin_Directory;
					global $cAlfa; global $cTipoCta; global $dDesde; global $dHasta; global $cTerId; global $nPag;

				  if($cAlfa == "ALPOPULX" || $cAlfa == "DESARROL" || $cAlfa == "PRUEBASX"){

				  	$this->SetXY(13,7);
				  	$this->Cell(72,28,'',1,0,'C');
				  	$this->Cell(183,28,'',1,0,'C');

	 					// Dibujo //
	 					$this->Image($cRoot.$cPlesk_Skin_Directory.'/alpopul1.jpg',14,8,70,25);

						$this->SetFont('verdana','',16);
						$this->SetXY(85,15);
						$this->Cell(183,8,'FACTURAS EMITIDAS',0,0,'C');
						$this->Ln(8);
						$this->SetFont('verdana','',12);
						$this->SetX(85);
						$this->Cell(183,6,'PERIODO: DE  '.$dDesde.' A  '.$dHasta,0,0,'C');
						$this->Ln(15);
						$this->SetX(13);
				  }else{
				  	$this->SetXY(13,7);
					  $this->Cell(255,15,'',1,0,'C');

					  $this->SetFont('verdana','',16);
						$this->SetXY(13,8);
						$this->Cell(255,8,'FACTURAS EMITIDAS',0,0,'C');
						$this->Ln(8);
						$this->SetFont('verdana','',12);
						$this->SetX(13);
						$this->Cell(255,6,'PERIODO: DE  '.$dDesde.' A  '.$dHasta,0,0,'C');
						$this->Ln(10);
						$this->SetX(13);
				  }

				  if($this->PageNo() > 1 && $nPag ==1){
						$this->SetX(13);
						$this->SetFont('verdana','B',6);
						$this->Cell(20,10,"NIT",1,0,'C');
						$this->Cell(60,10,"NOMBRE CLIENTE",1,0,'C');
						$this->Cell(30,10,"FACTURA",1,0,'C');
						$this->Cell(20,10,"FECHA",1,0,'C');
						$this->Cell(25,10,"DO",1,0,'C');
						$this->Cell(20,10,"OPERACION",1,0,'C');
						$this->Cell(50,5,"RESULTADOS",1,0,'C');
						$this->Cell(25,10,"ESTADO",1,0,'C');

						$this->SetXY(188,$this->GetY()+5);
						$this->Cell(25,5,"A CARGO",1,0,'C');
						$this->Cell(25,5,"A FAVOR",1,0,'C');
						$this->Ln(5);

						$this->SetFont('verdana','',6);
						$this->SetWidths(array('20','60','30','20','25','20','25','25','25'));
						$this->SetAligns(array('L','L','L','C','C','R','R','C'));
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
			$pdf->Cell(107,5,"TOTAL NUMERO DE FACTURAS:",0,0,'R');
			$pdf->SetFont('verdana','',8);
			$pdf->Cell(30,5,count($mFacPer),0,0,'C');


			$pdf->Ln(8);
			$pdf->SetX(13);
			$pdf->SetFont('verdana','B',6);
			$pdf->Cell(20,10,"NIT",1,0,'C');
			$pdf->Cell(60,10,"NOMBRE CLIENTE",1,0,'C');
			$pdf->Cell(30,10,"FACTURA",1,0,'C');
			$pdf->Cell(20,10,"FECHA",1,0,'C');
			$pdf->Cell(25,10,"DO",1,0,'C');
			$pdf->Cell(20,10,"OPERACION",1,0,'C');
			$pdf->Cell(50,5,"RESULTADOS",1,0,'C');
			if($cFacId == "PEDIDO"){
				$pdf->Cell(25,10,"ESTADO PEDIDO",1,0,'C');
			}else{
				$pdf->Cell(25,10,"ESTADO",1,0,'C');
			}

			$pdf->SetXY(188,$pdf->GetY()+5);
			$pdf->Cell(25,5,"A CARGO",1,0,'C');
			$pdf->Cell(25,5,"A FAVOR",1,0,'C');
			$pdf->Ln(5);


				$nPag=0;

				$nTotFav = 0;
				$nTotCar = 0;
				$pdf->SetFont('verdana','',6);
				$pdf->SetWidths(array('20','60','30','20','25','20','25','25','25'));
				$pdf->SetAligns(array('L','L','L','C','C','R','R','C'));
				for($j=0;$j<count($mFacPer);$j++){
					$nTotFav += $mFacPer[$j]['saldofav'];
					$nTotCar += $mFacPer[$j]['saldocar'];
					$pdf->SetX(13);
						$pdf->Row(array($mFacPer[$j]['teridxxx'],
										 				$mFacPer[$j]['clinomxx'],
										 				$mFacPer[$j]['comidxxx'].'-'.$mFacPer[$j]['comcodxx'].'-'.$mFacPer[$j]['comcscxx'],
										 				$mFacPer[$j]['comfecxx'],
										 				$mFacPer[$j]['doidxxxx'],
										 				$mFacPer[$j]['doctipxx'],
										 				number_format($mFacPer[$j]['saldocar'],0,',','.'),
										 				number_format($mFacPer[$j]['saldofav'],0,',','.'),
										 				str_replace("_", " ", $mFacPer[$j]['comealpo'])));
				}

			$nPag=0;

			$pdf->SetX(13);
			$pdf->SetFont('verdana','B',6);
			$pdf->Cell(175,5,"TOTALES",1,0,'R');
			$pdf->Cell(25,5,number_format($nTotCar,0,',','.'),1,0,'R');
			$pdf->Cell(25,5,number_format($nTotFav,0,',','.'),1,0,'R');
			$pdf->Cell(25,5,"",1,0,'C');
			$pdf->Ln(10);

			$pdf->SetX(13);
			$pdf->SetFont('verdana','B',6);
			$pdf->Cell(255,5,"REVISADO POR:",0,0,'L');
			$pdf->Ln(10);
			$pdf->SetX(13);
			$pdf->Line(13,$pdf->GetY(),50,$pdf->GetY());

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
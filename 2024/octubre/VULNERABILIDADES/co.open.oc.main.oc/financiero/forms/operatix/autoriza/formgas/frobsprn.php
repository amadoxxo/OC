<?php
  namespace openComex;
  use FPDF;

  include("../../../../libs/php/utility.php");

	ini_set("memory_limit","512M");
	set_time_limit(0);
	
	date_default_timezone_set("America/Bogota");
	
	if ($gGofId <> "") {
		$qGruObs  = "SELECT * FROM $cAlfa.fpar0123 WHERE gofidxxx = \"$gGofId\" AND regestxx = \"ACTIVO\" LIMIT 0,1";
	  $xGruObs = f_MySql("SELECT","",$qGruObs,$xConexion01,"");
	  	if (mysql_num_rows($xGruObs) > 0) {
	  		$xRGO = mysql_fetch_array($xGruObs);
	  	}
	}

	$qFoiDat  = "SELECT DISTINCT  ";
	$qFoiDat .= "$cAlfa.ffob0000.*, ";					
	$qFoiDat .= "IF($cAlfa.SIAI0003.USRNOMXX <> \"\",$cAlfa.SIAI0003.USRNOMXX,\"DIRECTOR SIN NOMBRE\") AS usrnomxx, ";
	$qFoiDat .= "IF($cAlfa.fpar0123.gofdesxx <> \"\",$cAlfa.fpar0123.gofdesxx,\"SIN DESCRIPCION\") AS gofdesxx ";
  $qFoiDat .= "FROM $cAlfa.ffob0000 ";
  $qFoiDat .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.ffob0000.diridxxx = $cAlfa.SIAI0003.USRIDXXX ";
  $qFoiDat .= "LEFT JOIN $cAlfa.fpar0123 ON $cAlfa.ffob0000.gofidxxx = $cAlfa.fpar0123.gofidxxx AND $cAlfa.fpar0123.goftipxx = \"FORMULARIOS\" ";
	$qFoiDat .= "WHERE " ;
	$qFoiDat .= "$cAlfa.ffob0000.regfcrex BETWEEN \"$gDesde\" AND \"$gHasta\" AND ";
	$qFoiDat .= "$cAlfa.ffob0000.gofidxxx LIKE \"%$gGofId%\" ";
  $qFoiDat .= "ORDER BY ABS($cAlfa.ffob0000.obscscxx) ASC ";
	$xFoiDat  = f_MySql("SELECT","",$qFoiDat,$xConexion01,"");
	//f_Mensaje(__FILE__,__LINE__,mysql_num_rows($xFoiDat));
					

	switch ($cTipo) {
		case 1:
		  // PINTA POR PANTALLA// ?>
		  <html>
        <head>
          <title>Reporte de Observaciones Formulario</title>
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
          <form name = 'frgrm' action='frobsprn.php' method="POST">
              <table border="1" cellspacing="0" cellpadding="0" align=center style="margin:5px" width="98%">
                <tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">
                  <td class="name" colspan="8" align="left">
                  	<center>
	                    <font size="3">
	                      <b>REPORTE DE OBSERVACIONES FORMULARIO<br>
													<?php echo "DESDE ".$gDesde." HASTA ".$gHasta ?><br>
													<?php if($gGofId<>""){ ?>
	                       			<br>GRUPO: <?php echo "[".$gGofId."] ".$xRGO['gofdesxx'] ?><br>
	                       		<?php 
														} 
													?>
	                      </b>
	                    </font>
                    </center>
                  </td>
                </tr>
                <tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">
                  <td class="name" colspan="8" align="left">
                  	<center>
	                    <font size="2">
	                      <b>TOTAL REGISTROS EN LA CONSULTA [<?php echo mysql_num_rows($xFoiDat) ?>]<br>
	                    </font>
                    </center>
                  </td>
                </tr>
                <tr height="20">
                	<td style="background-color:<?php echo $vSysStr['system_row_title_color_ini'] ?>" class="name" align="center" width="060px"><b>Csc.</b></td>
                	<td style="background-color:<?php echo $vSysStr['system_row_title_color_ini'] ?>" class="name" align="center" width="100px"><b>Director</b></td>
                	<td style="background-color:<?php echo $vSysStr['system_row_title_color_ini'] ?>" class="name" align="center" width="180px"><b>Nombre</b></td>
                	<td style="background-color:<?php echo $vSysStr['system_row_title_color_ini'] ?>" class="name" align="center" width="060px"><b>Grupo</b></td>
                	<td style="background-color:<?php echo $vSysStr['system_row_title_color_ini'] ?>" class="name" align="center" width="180px"><b>Descripci&oacute;n</b></td>
                  <td style="background-color:<?php echo $vSysStr['system_row_title_color_ini'] ?>" class="name" align="center"><b>Observaci&oacute;n</b></td>
                  <td style="background-color:<?php echo $vSysStr['system_row_title_color_ini'] ?>" class="name" align="center" width="120px"><b>Formulario</b></td>
                  <td style="background-color:<?php echo $vSysStr['system_row_title_color_ini'] ?>" class="name" align="center" width="150px"><b>Do</b></td>
                </tr>
               <?php
                while ($xRFD = mysql_fetch_array($xFoiDat)) {
                  $zColorPro = "#000000"; ?>
                  <tr bgcolor = "white" height="20"   style="padding-left:4px;padding-right:4px">
                  	<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $xRFD['obscscxx'] ?></td>
                  	<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $xRFD['diridxxx'] ?></td>
                  	<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $xRFD['usrnomxx'] ?></td>
                  	<td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo $xRFD['gofidxxx'] ?></td>
                  	<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $xRFD['gofdesxx'] ?></td>
                  	<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $xRFD['obsobsxx'] ?></td>
                  	<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $xRFD['seridxxx'] ?></td>
                  	<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $xRFD['docsucxx']."-".$xRFD['docnroxx']."-".$xRFD['docsufxx'] ?></td>
                  </tr>
               <?php } ?>
              </table> 
          </form>
          <script type="text/javascript">document.getElementById('loading').style.display="none";</script>
        </body>
      </html>
 		<?php
  	break;
  	case 2:
			// PINTA POR EXCEL //Reporte de Observaciones Formulario
		  $header .= 'Reporte de Observaciones Formulario'."\n";
		  $header .= "\n";
			$data = '';
			$title = "Reporte de Observaciones Formulario.xls";

      $data .= '<table border="1" cellspacing="0" cellpadding="0" align=center style="margin:5px" width="1150">';
	      $data .= '<tr height="20">';
		      $data .= '<td colspan="8" align="left">';
		      	$data .= 'REPORTE DE OBSERVACIONES FORMULARIO';
		      $data .= '</td>';
		    $data .= '</tr>';
	      $data .= '<tr height="20">';
		      $data .= '<td colspan="8" align="left">';
					$data .= 'DESDE '.$gDesde.' HASTA '.$gHasta;
					$data .= '</td>';
	      $data .= '</tr>';
				if($gGofId<>""){
					$data .= '<tr height="20">';
			      $data .= '<td colspan="8" align="left">';
							$data .= 'GRUPO: ['.$gGofId.'] '.$xRGO['gofdesxx'];
						$data .= '</td>';
		      $data .= '</tr>';
	      } 
	      $data .= '<tr height="20">';
		      $data .= '<td colspan="8" align="left">';
		      	$data .= 'TOTAL REGISTROS EN LA CONSULTA ['.mysql_num_rows($xFoiDat).']';
		      $data .= '</td>';
	      $data .= '</tr>';
	      $data .= '<tr height="20">';
		      $data .= '<td style="background-color:'.$vSysStr['system_row_title_color_ini'].'" align="center" width="060px"><b>Csc.</b></td>';
		      $data .= '<td style="background-color:'.$vSysStr['system_row_title_color_ini'].'" align="center" width="100px"><b>Director</b></td>';
		      $data .= '<td style="background-color:'.$vSysStr['system_row_title_color_ini'].'" align="center" width="180px"><b>Nombre</b></td>';
		      $data .= '<td style="background-color:'.$vSysStr['system_row_title_color_ini'].'" align="center" width="060px"><b>Grupo</b></td>';
		      $data .= '<td style="background-color:'.$vSysStr['system_row_title_color_ini'].'" align="center" width="180px"><b>Descripci&oacute;n</b></td>';
		      $data .= '<td style="background-color:'.$vSysStr['system_row_title_color_ini'].'" align="center" width="300px"><b>Observaci&oacute;n</b></td>';
		      $data .= '<td style="background-color:'.$vSysStr['system_row_title_color_ini'].'" align="center" width="120px"><b>Formulario</b></td>';
		      $data .= '<td style="background-color:'.$vSysStr['system_row_title_color_ini'].'" align="center" width="150px"><b>Do</b></td>';
	      $data .= '</tr>';
	     	while ($xRFD = mysql_fetch_array($xFoiDat)) {
		      $zColorPro = "#000000";
		      $data .= '<tr height="20">';
			      $data .= '<td align="left">'.$xRFD['obscscxx'].'</td>';
			      $data .= '<td align="left">'.$xRFD['diridxxx'].'</td>';
			      $data .= '<td align="left">'.$xRFD['usrnomxx'].'</td>';
			      $data .= '<td align="center">'.$xRFD['gofidxxx'].'</td>';
			      $data .= '<td align="left">'.$xRFD['gofdesxx'].'</td>';
			      $data .= '<td align="left">'.$xRFD['obsobsxx'].'</td>';
			      $data .= '<td align="left">'.$xRFD['seridxxx'].'</td>';
			      $data .= '<td align="left">'.$xRFD['docsucxx']."-".$xRFD['docnroxx']."-".$xRFD['docsufxx'].'</td>';
		      $data .= '</tr>';
	      }
      $data .= '</table>'; 

			if ($data == "") {
	  		$data = "\n(0) REGISTROS!\n";
			}

      header("Pragma: public");
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Cache-Control: private",false); // required for certain browsers
      header("Content-type: application/octet-stream");
      header("Content-Disposition: attachment; filename=\"".basename($title)."\";");
      
      print $data;
		break;
		case 3 :
    /* PINTA POR PDF */
		if (mysql_num_rows($xFoiDat) > 0) {
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

      $nCount  = mysql_num_rows($xFoiDat);
      $cGofDes = $xRGO['gofdesxx'];
      
		  class PDF extends FPDF {
				function Header() {
					global $cRoot; global $cPlesk_Skin_Directory;
					global $cAlfa;  global $gDesde;  global $gHasta; 
					global $gGofId; global $cGofDes; global $nCount;

			  	$this->SetFont('verdana','',12);
					$this->SetXY(13,7);
					$this->Cell(190,8,"REPORTE DE OBSERVACIONES FORMULARIO",0,0,'C');
					$this->Ln(6);
					$this->SetX(13);
					$this->SetFont('verdana','B',8);
					$this->Cell(190,8,"DESDE $gDesde HASTA $gHasta",0,0,'C');
					$this->Ln(5);
					if($gGofId<>""){
  					$this->SetFont('verdana','',8);
  					$this->SetX(13);
  					$this->Cell(190,6,'GRUPO: ['.$gGofId.'] '.$cGofDes,0,0,'C');
  					$this->Ln(5);
  					$n = 25;  					
					} else {
						$n = 20;
					}

					$this->SetFont('verdana','',8);
  				$this->SetX(13);
  				$this->Cell(190,6,'TOTAL REGISTROS EN LA CONSULTA ['.$nCount.']',0,0,'C');

  				$this->SetXY(13,6);
				 	$this->Cell(190,$n,'',1,0,'C');
				  	
					$this->SetXY(13,$n+5);

					if($this->PageNo() > 1){
      			$this->SetFillColor(214,223,247);
      			$this->SetTextColor(0);
      			$this->SetFont('verdana','B',6);
      			$this->SetX(13);
      			$this->Cell(10,5,"Csc.",1,0,'C',1);
      			$this->Cell(20,5,"Director",1,0,'C',1);
      			$this->Cell(32,5,"Nombre",1,0,'C',1);
      			$this->Cell(10,5,"Grupo",1,0,'C',1);
      			$this->Cell(32,5,"Descripcion",1,0,'C',1);
      			$this->Cell(36,5,"Observacion",1,0,'C',1);
      			$this->Cell(22,5,"Formulario",1,0,'C',1);
      			$this->Cell(28,5,"Do",1,0,'C',1);

      			$this->SetFillColor(255);
      			$this->SetTextColor(0);

          	$this->Ln(5);
      			$this->SetX(13);
      			$this->SetFont('verdana','',6);
      			$this->SetWidths(array('10','20','32','10','32','36','22','28'));
      			$this->SetAligns(array('C','L','L','C','L','L','L','L'));
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

			$pdf = new PDF('P','mm','Letter');
			$pdf->AddFont('verdana','','');
			$pdf->AddFont('verdana','B','');
			$pdf->AliasNbPages();
			$pdf->SetMargins(0,0,0);

			$pdf->AddPage();
			$pdf->SetFillColor(214,223,247);
      $pdf->SetTextColor(0);
      $pdf->SetFont('verdana','B',6);
      $pdf->SetX(13);
      $pdf->Cell(10,5,"Csc.",1,0,'C',1);
      $pdf->Cell(20,5,"Director",1,0,'C',1);
      $pdf->Cell(32,5,"Nombre",1,0,'C',1);
      $pdf->Cell(10,5,"Grupo",1,0,'C',1);
      $pdf->Cell(32,5,"Descripcion",1,0,'C',1);
      $pdf->Cell(36,5,"Observacion",1,0,'C',1);
      $pdf->Cell(22,5,"Formulario",1,0,'C',1);
      $pdf->Cell(28,5,"Do",1,0,'C',1);

      $pdf->SetFillColor(255);
      $pdf->SetTextColor(0);

          $pdf->Ln(5);
      $pdf->SetX(13);
      $pdf->SetFont('verdana','',6);
      $pdf->SetWidths(array('10','20','32','10','32','36','22','28'));
      $pdf->SetAligns(array('C','L','L','C','L','L','L','L'));
			$pdf->SetX(13);
			
			while ($xRFD = mysql_fetch_array($xFoiDat)) {
			  	$pdf->SetX(13);
	        $pdf->Row(array($xRFD['obscscxx'],
									 			  $xRFD['diridxxx'],
									 				$xRFD['usrnomxx'],
									 				$xRFD['gofidxxx'],
									 				$xRFD['gofdesxx'],
									 				$xRFD['obsobsxx'],
									 				$xRFD['seridxxx'],
									 				$xRFD['docsucxx']."-".$xRFD['docnroxx']."-".$xRFD['docsufxx']));
	    }
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
	}//Fin Switch
	?>


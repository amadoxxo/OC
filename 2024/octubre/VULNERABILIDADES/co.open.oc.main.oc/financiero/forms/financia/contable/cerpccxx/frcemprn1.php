<?php
  namespace openComex;
  /**
	 * Imprime Certificado Mensual de Retenciones x Pagos a Terceros.
	 * --- Descripcion: Permite Imprimir Certificado Mensual de Retenciones x Pagos a Terceros.
	 * @author Yulieth Campos <ycampos@opentecnologia.com.co>
	 * @version 002
	 */
  include("../../../../libs/php/utility.php");
  $cNewYear = date("Y");

  /***** CABECERA 1001 *****/
	$qCocDat  = "SELECT ";
	$qCocDat .= "$cAlfa.fcoc$cNewYear.*, ";
	$qCocDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
	$qCocDat .= "FROM $cAlfa.fcoc$cNewYear ";
  $qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcoc$cNewYear.terid2xx = $cAlfa.SIAI0150.CLIIDXXX ";
	$qCocDat .= "WHERE ";
	$qCocDat .= "$cAlfa.fcoc$cNewYear.comidxxx = \"F\" AND ";
	$qCocDat .= "$cAlfa.fcoc$cNewYear.terid2xx = \"$gTerId\" AND ";
	$qCocDat .= "$cAlfa.fcoc$cNewYear.comperxx = \"$gAnio$gMes\" AND ";
	$qCocDat .= "$cAlfa.fcoc$cNewYear.regestxx = \"ACTIVO\" ORDER BY $cAlfa.fcoc$cNewYear.comidxxx,$cAlfa.fcoc$cNewYear.comcodxx,$cAlfa.fcoc$cNewYear.comcscxx,$cAlfa.fcoc$cNewYear.comfecxx";
	//f_Mensaje(__FILE__,__LINE__,$qCocDat);
	$xCocDat  = f_MySql("SELECT","",$qCocDat,$xConexion01,"");
	//f_Mensaje(__FILE__,__LINE__,$qCocDat);
	$xCocDat  = f_MySql("SELECT","",$qCocDat,$xConexion01,"");
	$nFilCoc = mysql_num_rows($xCocDat);
	$mCocDat = array();
	if($nFilCoc > 0){
		while($xRCD = mysql_fetch_array($xCocDat)){
			$nInd_mCocDat = count($mCocDat);
			$mCocDat[$nInd_mCocDat]= $xRCD;
		}
	}
  /*****Fin select a CABECERA *****/


  
	/*****Cargo matriz con Do *****/
	
	/*****Cargo matriz con Do *****/
	//while($xRCD = mysql_fetch_array($xCocDat)){
	//$mFactura  = array();
/*for($h=0;$h<count($mCocDat);$h++){
		$mDo = f_Explode_Array($mCocDat[$h]['commemod'],"|","~");
		$mDoiId    = array();
		$mConcepto = array();
		  for ($i=0;$i<count($mDo);$i++) {
		    if ($mDo[$i][14] != "") {
		        $nSwitch_Encontre_Do = 0;
		        for ($j=0;$j<count($mDoiId);$j++) {
		          if ($mDoiId[$j][14] == $mDo[$i][14]) {
			            if($mDoiId[$j][1] == $mDo[$i][1]){
			            $nSwitch_Encontre_Do = 1;
			            $mDoiId[$j][7] += $mDo[$i][7]; // Acumulo el valor de ingreso para tercero.
			          }
			         }
		        }
		        if ($nSwitch_Encontre_Do == 0) { // No encontre el ingreso para tercero en la matrix $mDoiId
		          $nInd_mDoiId = count($mDoiId);
		          $mDoiId[$nInd_mDoiId] = $mDo[$i]; // Ingreso el registro como nuevo.
		          $nInd_mConcepto = count($mConcepto);
		          $mConcepto[$nInd_mConcepto]['cCtoId']   = $mDo[$i][1];
		          $mConcepto[$nInd_mConcepto]['cCtoDes']  = $mDo[$i][2];
		          //$mConcepto[$nInd_mconcepto]['cFactura'] = $mCocDat[$h]['comidxxx']."-".$mCocDat[$h]['comcodxx']."-".$mCocDat[$h]['comcscxx']."-".$mCocDat[$h]['comcsc2x'];
		        }
		      }
		    }
	 $cCliNom =	$mCocDat[$h]['CLINOMXX'];    
	 }*/

	  $mMatriz = array();
		for($i=0;$i<count($mCocDat);$i++){
			$mDo = f_Explode_Array($mCocDat[$i]['commemod'],"|","~");
			$cFactura = $mCocDat[$i]['comidxxx']."-".$mCocDat[$i]['comcodxx']."-".$mCocDat[$i]['comcscxx']."-".$mCocDat[$i]['comcsc2x'];
			for($j=0;$j<count($mDo);$j++){
			 if ($mDo[$j][14] != "") {
			 	$nInd_mMatriz = count($mMatriz);
			 	$mMatriz[$nInd_mMatriz]= $mDo[$j];
				$mMatriz[$nInd_mMatriz][100] = $cFactura; 
					
			 }
		  }
		 $cClinom = $mCocDat[$i]['CLINOMXX']; 
		}
		
		$mConcepto = array();
		for($i=0;$i<count($mMatriz);$i++){
			if($mMatriz[$i][14] != "") {
				$nSwitch_Encontre_Concepto = 0;
				for($j=0;$j<count($mConcepto);$j++){
					if($mConcepto[$j][100] == $mMatriz[$i][100]){
						if($mConcepto[$j][14] == $mMatriz[$i][14]){
							if($mConcepto[$j][1] == $mMatriz[$i][1]){
								$nSwitch_Encontre_Concepto = 1;
								$mConcepto[$j][7] += $mMatriz[$i][7]; 
							}
						}
					}
				}
				if($nSwitch_Encontre_Concepto == 0){
					$nInd_mConcepto = count($mConcepto);
					$mConcepto[$nInd_mConcepto] = $mMatriz[$i];
				}
			}
		}
		
		
		for($i=0;$i<count($mConcepto);$i++){
			echo $mConcepto[$i][14]."-".$mConcepto[$i][1]."-".$mConcepto[$i][2]."-".$mConcepto[$i][100]."<br>";	
		}
		
		


?>

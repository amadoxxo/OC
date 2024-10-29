<?php
  namespace openComex;
/**
	 * Cargando datos el primer concepto del proveedor 
	 * @author Johana Arboleda Ramos <jarboleda@opentecnologia.com.co>
	 * @package openComex
	 */
  include("../../../../libs/php/utility.php");
	
	if($gSecuencia_DO  == 1 && $gSucId_DO1 != '' && $gDocId_DO1 != '' && $gDocSuf_DO1 != '') { ?> 
		<script languaje = "javascript">                 
			var xSecuencia = 1;                     
			parent.fmwork.document.forms['frgrm']['cTerTipB_DO' +xSecuencia].value = parent.fmwork.document.forms['frgrm']['cTerTipB'].value;
			parent.fmwork.document.forms['frgrm']['cTerIdB_DO'  +xSecuencia].value = parent.fmwork.document.forms['frgrm']['cTerIdB'].value;
		</script>
	<?php } 
	
	if($gTerIdB <> '') {  
	
	  if($gTipPro == 'VALOR') {
	   $nCanDo = $gSecuencia_DO;
	  } else {
	   $nCanDo = 1;
	  }
	
		$qDatExt  = "SELECT CLICTOXX ";
		$qDatExt .= "FROM $cAlfa.SIAI0150 ";
		$qDatExt .= "WHERE ";
		$qDatExt .= "$gTerTipB = \"SI\" AND ";
		$qDatExt .= "CLIIDXXX = \"$gTerIdB\" AND ";
		$qDatExt .= "REGESTXX = \"ACTIVO\"  LIMIT 0,1";
		$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
		// f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));
		$xRDE = mysql_fetch_array($xDatExt);
    $mCcoId = explode("|",$xRDE['CLICTOXX']);
    $nCont=0;
		for ($nC=0; $nC<count($mCcoId); $nC++) {
      if ($mCcoId[$nC] <> "") {
        $nCont++;
      }
    }
    //Si hay mas de un concepto y la variable esta en NO, no se carga automaticamente el primer concepto 
    $nBand = ($nCont > 1 && $vSysStr['financiero_carga_concepto_causacion_automatica'] == "NO") ? 1 : 0;
    
    if($nBand==0){
      for ($nC=0; $nC<count($mCcoId); $nC++) {
      	if ($mCcoId[$nC] <> "") {
      		$mAux01 = explode("~",$mCcoId[$nC]);
      		$qCliCco  = "SELECT ctoidxxx, ctodesxx, ctovlr02 ";
      		$qCliCco .= "FROM $cAlfa.fpar0121 ";
      		$qCliCco .= "WHERE ";
      		$qCliCco .= "ctocomxx LIKE \"%|$gComId~$gComCod%\" AND ";
      		$qCliCco .= "ctoidxxx LIKE \"{$mAux01[0]}\" AND ";
      		$qCliCco .= "regestxx = \"ACTIVO\" ";
      		$qCliCco .= "ORDER BY ctoidxxx LIMIT 0,1";
      		$xCliCco  = f_MySql("SELECT","",$qCliCco,$xConexion01,"");
      		//f_Mensaje(__FILE__,__LINE__,$qCliCco." ~ ".mysql_num_rows($xCliCco));
      		if (mysql_num_rows($xCliCco) > 0) {
      			$xRCC = mysql_fetch_array($xCliCco); 
      			$i = 1; ?> 
              <script languaje = "javascript">
                if ('<?php echo $i ?>' > parent.fmwork.document.forms['frgrm']['nSecuencia_CCO'].value) {
              	  parent.fmwork.f_Add_New_Row_Conceptos();
                }           
                parent.fmwork.document.forms['frgrm']['cCcoId_CCO<?php echo $i ?>'].id       = '<?php echo $xRCC['ctoidxxx']?>';
                parent.fmwork.document.forms['frgrm']['cCcoId_CCO<?php echo $i ?>'].value    = '<?php echo $xRCC['ctoidxxx']?>';        
                parent.fmwork.document.forms['frgrm']['cCcoDes_CCO<?php echo $i ?>'].value   = '<?php echo $xRCC['ctodesxx']?>';
                parent.fmwork.document.forms['frgrm']['cSucId_CCO<?php echo $i?>'].value     = ('<?php echo $gTipPro?>' == 'VALOR') ? parent.fmwork.document.forms['frgrm']['cSucId_DO<?php echo $i?>'].value  : "";
                parent.fmwork.document.forms['frgrm']['cDocId_CCO<?php echo $i?>'].value     = ('<?php echo $gTipPro?>' == 'VALOR') ? parent.fmwork.document.forms['frgrm']['cDocId_DO<?php echo $i?>'].value  : "";
                parent.fmwork.document.forms['frgrm']['cDocSuf_CCO<?php echo $i?>'].value    = ('<?php echo $gTipPro?>' == 'VALOR') ? parent.fmwork.document.forms['frgrm']['cDocSuf_DO<?php echo $i?>'].value : "";
                parent.fmwork.document.forms['frgrm']['nVlrBase_CCO<?php echo $i ?>'].value  = '';
                parent.fmwork.document.forms['frgrm']['nVlrIva_CCO<?php echo $i ?>'].value   = '';
                parent.fmwork.document.forms['frgrm']['nVlr_CCO<?php echo $i ?>'].value      = '';
                parent.fmwork.document.forms['frgrm']['cCtoVrl02_CCO<?php echo $i ?>'].value = '<?php echo $xRCC['ctovlr02']?>';
                
              </script>
            <?php
      		  for ($i=2; $i<=$nCanDo; $i++) { ?> 
      				<script languaje = "javascript">
      				  if(parent.fmwork.document.forms['frgrm']['cSucId_DO<?php echo $i?>'].value  != '' &&
      						 parent.fmwork.document.forms['frgrm']['cDocId_DO<?php echo $i?>'].value  != '' &&
      						 parent.fmwork.document.forms['frgrm']['cDocSuf_DO<?php echo $i?>'].value != ''){
      					  if ('<?php echo $i ?>' > parent.fmwork.document.forms['frgrm']['nSecuencia_CCO'].value) {
      						  parent.fmwork.f_Add_New_Row_Conceptos();
                  }						      
      						parent.fmwork.document.forms['frgrm']['cCcoId_CCO<?php echo $i ?>'].id       = '<?php echo $xRCC['ctoidxxx']?>';
      						parent.fmwork.document.forms['frgrm']['cCcoId_CCO<?php echo $i ?>'].value    = '<?php echo $xRCC['ctoidxxx']?>';        
      						parent.fmwork.document.forms['frgrm']['cCcoDes_CCO<?php echo $i ?>'].value   = '<?php echo $xRCC['ctodesxx']?>';
      						parent.fmwork.document.forms['frgrm']['cSucId_CCO<?php echo $i?>'].value     = ('<?php echo $gTipPro?>' == 'VALOR') ? parent.fmwork.document.forms['frgrm']['cSucId_DO<?php echo $i?>'].value  : "";
      						parent.fmwork.document.forms['frgrm']['cDocId_CCO<?php echo $i?>'].value     = ('<?php echo $gTipPro?>' == 'VALOR') ? parent.fmwork.document.forms['frgrm']['cDocId_DO<?php echo $i?>'].value  : "";
      						parent.fmwork.document.forms['frgrm']['cDocSuf_CCO<?php echo $i?>'].value    = ('<?php echo $gTipPro?>' == 'VALOR') ? parent.fmwork.document.forms['frgrm']['cDocSuf_DO<?php echo $i?>'].value : "";
      						parent.fmwork.document.forms['frgrm']['nVlrBase_CCO<?php echo $i ?>'].value  = '';
      						parent.fmwork.document.forms['frgrm']['nVlrIva_CCO<?php echo $i ?>'].value   = '';
      						parent.fmwork.document.forms['frgrm']['nVlr_CCO<?php echo $i ?>'].value      = '';
      						parent.fmwork.document.forms['frgrm']['cCtoVrl02_CCO<?php echo $i ?>'].value = '<?php echo $xRCC['ctovlr02']?>';
      				  }
      				</script>
      			<?php } ?>
      			<script languaje = "javascript">
      			  parent.fmwork.f_Asignar_Base_Conceptos();
      			</script>
            <?php $nC = count($mCcoId); 							 
      		}						
      	}
      }
		}
	}
?>
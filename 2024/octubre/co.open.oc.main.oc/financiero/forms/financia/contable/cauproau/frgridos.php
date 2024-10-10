<?php
  namespace openComex;
/**
	 * Cargando datos de DO si el tercero tiene solo un DO y no hay nada en la grilla
	 * @author Johana Arboleda Ramos <jarboleda@opentecnologia.com.co>
	 * @package openComex
	 */
  include("../../../../libs/php/utility.php");
	
	if($gTerId <> '') {      
		//Busco si tiene un DO
		$qCliDo  = "SELECT sucidxxx, docidxxx, docsufxx, ccoidxxx, regestxx, doctipxx, regfcrex ";
		$qCliDo .= "FROM $cAlfa.sys00121 ";
		$qCliDo .= "WHERE ";
		$qCliDo .= "cliidxxx = \"$gTerId\" AND ";
		$qCliDo .= "regestxx = \"ACTIVO\" ";
		$qCliDo .= "ORDER BY sucidxxx, docidxxx, docsufxx ";
		$xCliDo  = f_MySql("SELECT","",$qCliDo,$xConexion01,"");
		//f_Mensaje(__FILE__,__LINE__,$qCliDo." ~ ".mysql_num_rows($xCliDo));
		if (mysql_num_rows($xCliDo) == 1) {
			$xRCD = mysql_fetch_array($xCliDo);
			
			$cFecApe = "";
			switch ($xRCD['doctipxx']){
				case "EXPORTACION": 
				  $xRCD['regfcrex'] = $xRCD['regfcrex'];
				break;
				default: //Busco en la SIAI0200
					$qFecApe  = "SELECT DOIAPEXX ";
					$qFecApe .= "FROM $cAlfa.SIAI0200 ";
					$qFecApe .= "WHERE ";
					$qFecApe .= "DOIIDXXX = \"{$xRCD['docidxxx']}\" AND ";
					$qFecApe .= "DOISFIDX = \"{$xRCD['docsufxx']}\" AND ";
					$qFecApe .= "ADMIDXXX = \"{$xRCD['sucidxxx']}\" LIMIT 0,1 ";
					$xFecApe  = f_MySql("SELECT","",$qFecApe,$xConexion01,"");
					//f_Mensaje(__FILE__,__LINE__,$qFecApe." ~ ".mysql_num_rows($xFecApe));
					$xRFA = mysql_fetch_array($xFecApe);
					$xRCD['regfcrex'] = $xRFA['DOIAPEXX'];
				break;                           
			} ?>
			<script languaje = "javascript">
				var xSecuencia = 1;
				parent.fmwork.document.forms['frgrm']['cSucId_DO'   +xSecuencia].id    = '<?php echo $xRCD['sucidxxx'] ?>';
				parent.fmwork.document.forms['frgrm']['cSucId_DO'   +xSecuencia].value = '<?php echo $xRCD['sucidxxx'] ?>';
				parent.fmwork.document.forms['frgrm']['cDocId_DO'   +xSecuencia].id    = '<?php echo $xRCD['docidxxx'] ?>';
		    parent.fmwork.document.forms['frgrm']['cDocId_DO'   +xSecuencia].value = '<?php echo $xRCD['docidxxx'] ?>';
		    parent.fmwork.document.forms['frgrm']['cDocSuf_DO'  +xSecuencia].id    = '<?php echo $xRCD['docsufxx'] ?>';
		    parent.fmwork.document.forms['frgrm']['cDocSuf_DO'  +xSecuencia].value = '<?php echo $xRCD['docsufxx'] ?>';				        
				parent.fmwork.document.forms['frgrm']['cTerId_DO'   +xSecuencia].value = parent.fmwork.document.forms['frgrm']['cTerId'].value;
				parent.fmwork.document.forms['frgrm']['cTerNom_DO'  +xSecuencia].value = parent.fmwork.document.forms['frgrm']['cTerNom'].value;
				parent.fmwork.document.forms['frgrm']['cTerTip_DO'  +xSecuencia].value = parent.fmwork.document.forms['frgrm']['cTerTip'].value;
				parent.fmwork.document.forms['frgrm']['cTerTipB_DO' +xSecuencia].value = parent.fmwork.document.forms['frgrm']['cTerTipB'].value;
				parent.fmwork.document.forms['frgrm']['cTerIdB_DO'  +xSecuencia].value = parent.fmwork.document.forms['frgrm']['cTerIdB'].value;
				parent.fmwork.document.forms['frgrm']['cDocFec_DO'  +xSecuencia].value = '<?php echo $xRCD['regfcrex']?>';
				parent.fmwork.document.forms['frgrm']['cCcoId_DO'   +xSecuencia].value = '<?php echo $xRCD['ccoidxxx']?>';
				parent.fmwork.document.forms['frgrm']['nVlrPro_DO'  +xSecuencia].value = '';
			</script>
		  <?php 
		  if($gTerIdB <> '' && $gSecuencia_CCO  == 1 && $gCcoId_CCO1 <> '') { ?> 
        <script languaje = "javascript">                 
          var xSecuencia = 1;                     
          parent.fmwork.document.forms['frgrm']['cSucId_CCO' +xSecuencia].value = '<?php echo (($gTipPro == 'VALOR') ? $xRCD['sucidxxx'] : "") ?>';
          parent.fmwork.document.forms['frgrm']['cDocId_CCO' +xSecuencia].value = '<?php echo (($gTipPro == 'VALOR') ? $xRCD['docidxxx'] : "") ?>';
          parent.fmwork.document.forms['frgrm']['cDocSuf_CCO'+xSecuencia].value = '<?php echo (($gTipPro == 'VALOR') ? $xRCD['docsufxx'] : "") ?>';
        </script>
      <?php }
		}
	}
?>
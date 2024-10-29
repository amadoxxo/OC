<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");
?>
<!--
	Los Parametros que Recibo son :
	$gSearch = Un dato para filtrar la consulta con el WHERE
	$gFields = A que campos del formulario voy a devolver los datos
	$gWhat   = Que voy a hacer, una ventana o una validacion -->
<?php
  if ($gWhat != "" && $gFunction != "") { 
    switch ($gFunction) {
    	case "cDocNro":
    	 $cTitVen = "Documentos de Comercio Exterior";
    	break;
    	default:
    	 $cTitVen = "Seriales de Formularios";
    	break;
    }
  
  ?>
  	<html>
  		<head>
  			<title><?php echo $cTitVen ?></title>
  			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
  			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
  			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
  			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
  			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
  	   	<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
  	  </head>
  	  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
  	  <center>
  			<table border ="0" cellpadding="0" cellspacing="0" width="300">
  				<tr>
  					<td>
  						<fieldset>
  			   			<legend><?php echo $cTitVen ?></legend>
  	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
  	  						<?php switch ($gWhat) {
  	  							case "VALID":
	  	  						  switch ($gFunction) {
									      case "cDocNro":
									        /* Primero lo Busco en OPENCOMEX */
		                      $qDatDoi  = "SELECT DISTINCT ";
		                      $qDatDoi .= "docsucxx, ";
		                      $qDatDoi .= "docnroxx, ";
		                      $qDatDoi .= "docsufxx  ";
		                      $qDatDoi .= "FROM $cAlfa.ffob0000 ";
												  $qDatDoi .= "WHERE " ;
													$qDatDoi .= "$cAlfa.ffob0000.obstipxx = \"LIBERARFAC\" AND ";
												  $qDatDoi .= "$cAlfa.ffob0000.docnroxx = \"$gDocNro\"  ";
												  $xDatDoi = f_MySql("SELECT","",$qDatDoi,$xConexion01,"");
									      break;
									      default:
									      	 /* Primero lo Busco en OPENCOMEX */
		                      $qDatDoi  = "SELECT DISTINCT ";
		                      $qDatDoi .= "seridxxx  ";
		                      $qDatDoi .= "FROM $cAlfa.ffob0000 ";
												  $qDatDoi .= "WHERE " ;
													$qDatDoi .= "$cAlfa.ffob0000.obstipxx = \"LIBERARFAC\" AND ";
												  $qDatDoi .= "$cAlfa.ffob0000.seridxxx = \"$gSerId\" ";
                          $xDatDoi = f_MySql("SELECT","",$qDatDoi,$xConexion01,"");
                        break;
									    }
  	  								//f_Mensaje(__FILE__,__LINE__,$qDatDoi."~".mysql_num_rows($xDatDoi));
  	  								if (mysql_num_rows($xDatDoi) == 1) {
  											$xMtzDo = mysql_fetch_array($xDatDoi); 
	  	  								switch ($gFunction) {
	                        case "cDocNro": ?>
	                          <script languaje = "javascript">
		                          parent.fmwork.document.forms['frgrm']['cSucId'].value   = "<?php echo $xMtzDo['docsucxx']?>";
		                          parent.fmwork.document.forms['frgrm']['cDocNro'].value  = "<?php echo $xMtzDo['docnroxx']?>";
		                          parent.fmwork.document.forms['frgrm']['cDocSuf'].value  = "<?php echo $xMtzDo['docsufxx']?>";
		                        </script>
	                        <?php break;
	                        default: ?>
	                          <script languaje = "javascript">
                              parent.fmwork.document.forms['frgrm']['cSerId'].value   = "<?php echo $xMtzDo['seridxxx']?>";
                            </script>
	                        <?php break;
	                      } ?>  	  									
  	  								<?php } else { ?>
  	  								  <script languaje = "javascript">
  	     	    					  parent.fmwork.f_Links('<?php echo $gFunction ?>','WINDOW');
  												window.close();
  											</script>
  	  								<?php }
  	  							break;
  	  							case "WINDOW":
  	  									/* Traigo DO's de Importacion de OPENCOMEX */
  	  						    switch ($gFunction) {
                        case "cDocNro":
									        /* Primero lo Busco en OPENCOMEX */
		                      $qDatDoi  = "SELECT DISTINCT ";
		                      $qDatDoi .= "docsucxx, ";
		                      $qDatDoi .= "docnroxx, ";
		                      $qDatDoi .= "docsufxx  ";
		                      $qDatDoi .= "FROM $cAlfa.ffob0000 ";
												  $qDatDoi .= "WHERE " ;
													$qDatDoi .= "$cAlfa.ffob0000.obstipxx = \"LIBERARFAC\" AND ";
												  $qDatDoi .= "$cAlfa.ffob0000.docnroxx LIKE \"%$gDocNro%\"  ";
												  $xDatDoi = f_MySql("SELECT","",$qDatDoi,$xConexion01,"");
									      break;
									      default:
									      	 /* Primero lo Busco en OPENCOMEX */
		                      $qDatDoi  = "SELECT DISTINCT ";
		                      $qDatDoi .= "seridxxx  ";
		                      $qDatDoi .= "FROM $cAlfa.ffob0000 ";
												  $qDatDoi .= "WHERE " ;
													$qDatDoi .= "$cAlfa.ffob0000.obstipxx = \"LIBERARFAC\" AND ";
												  $qDatDoi .= "$cAlfa.ffob0000.seridxxx LIKE \"%$gSerId%\" ";
                          $xDatDoi = f_MySql("SELECT","",$qDatDoi,$xConexion01,"");
                        break;
                      }
			  							//f_Mensaje(__FILE__,__LINE__,$qDatDoi."~".mysql_num_rows($xDatDoi));

	  									if (mysql_num_rows($xDatDoi) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "300">
					    						  <?php 
				  									switch ($gFunction) {
			                        case "cDocNro": ?>
			                          <tr>
		                              <td widht = "050" Class = "name"><center>Suc.</center></td>
		                              <td widht = "200" Class = "name"><center>Documento</center></td>
		                              <td widht = "050" Class = "name"><center>Sufijo</center></td>
		                            </tr>
			                        <?php break;
			                        default: ?>
			                         <tr>
                                  <td widht = "300" Class = "name"><center>Serial</center></td>
                                </tr>
			                        <?php break;
			                      } ?>
			                      <?php while ($xRDoc = mysql_fetch_array($xDatDoi)) {
	                            switch ($gFunction) {
	                              case "cDocNro": ?>
	                                <tr>
		                                <td width = "050" class= "name"><?php echo $xRDoc['docsucxx'] ?></td>
		                                <td width = "200" class= "name">
		                                  <a href = "javascript:window.opener.document.forms['frgrm']['cSucId'].value   ='<?php echo $xRDoc['docsucxx'] ?>';
		                                                        window.opener.document.forms['frgrm']['cDocNro'].value  ='<?php echo $xRDoc['docnroxx'] ?>';
		                                                        window.opener.document.forms['frgrm']['cDocSuf'].value  ='<?php echo $xRDoc['docsufxx'] ?>';
		                                                        window.close()"><?php echo $xRDoc['docnroxx'] ?></a></td>
		                                <td width = "050" class= "name"><?php echo $xRDoc['docsufxx'] ?></td>
		                              </tr>
	                              <?php break;
	                              default: ?>
	                               <tr>
                                    <td width = "300" class= "name">
                                      <a href = "javascript:window.opener.document.forms['frgrm']['cSerId'].value   ='<?php echo $xRDoc['seridxxx'] ?>';
                                                            window.close()"><?php echo $xRDoc['seridxxx'] ?></a></td>
                                  </tr>
	                              <?php break;
	                            } ?>			                      
														<?php } ?>
													</table>
												</center>
	  									<?php	} else {
	  										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");
	  									}
  	  							break;
  	  						}?>
  	  					</form>
  	  				</fieldset>
  	  			</td>
  	  		</tr>
  	  	</table>
  	  </center>
  	</body>
  </html>
<?php } else {
	f_Mensaje(__FILE__,__LINE__,"No se Recibieron Parametros Completos");
} ?>
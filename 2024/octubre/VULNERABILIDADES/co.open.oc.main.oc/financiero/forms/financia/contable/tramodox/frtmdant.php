<?php
  namespace openComex;
/**
 * Proceso Periodos Anteriores.
 * Este programa permite realizar una consulta de los Periodos Anteriores Abiertos para Cargar El Combo de Fechas, Se Utiliza para crear un Comprobante Contable de un Periodo Anterior.
 * @author Hernan Gordillo (tecnosmart.proyectos@gmail.com)
 * @package emisioncero
 * @version 001
*/
?>
<html>
	<head>
	  <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
		<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
	</head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
		<script languaje = "javascript">
			//parent.fmwork.forms['frnav']['dComFec'].options.length = 0;
			parent.fmwork.frnav.dComFec.options.length = 0;
			//parent.fmwork.forms['frnav']['dComFec'].options.length = 0;
		</script>
		<?php
			include("../../../../libs/php/utility.php");
			$cPerAno  = date("Y");
			$cPerMes  = date("m");
			$qPerAnt  = "SELECT peranoxx,permesxx ";
			$qPerAnt .= "FROM $cAlfa.fpar0122 ";
			$qPerAnt .= "WHERE ";
			$qPerAnt .= "comidxxx =  \"$gComId\"  AND ";
			$qPerAnt .= "comcodxx =  \"$gComCod\" AND ";
			$qPerAnt .= "peranoxx <= \"$cPerAno\" AND ";
			$qPerAnt .= "regestxx =  \"ABIERTO\" ORDER BY peranoxx DESC,permesxx DESC";
			$xPerAnt  = f_MySql("SELECT","",$qPerAnt,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qPerAnt." ~ ".mysql_num_rows($xPerAnt));
			//$vCcoDes  = mysql_fetch_array($xCcoDes);
			?>
      <script languaje = "javascript">
        parent.fmwork.document.frnav.dComFec.options[0] = new Option('-- SELECCIONE --','');
      </script>
  		<?php
			$i = 1;
			if (mysql_num_rows($xPerAnt) > 0) {

  			while ($xRPA = mysql_fetch_array($xPerAnt)) {
  				if ($xRPA['peranoxx'].$xRPA['permesxx'] < $cPerAno.$cPerMes) { ?>
	  				<script languaje = "javascript">
	  				  parent.fmwork.document.frnav.dComFec.options[<?php echo $i ?>] = new Option('<?php echo $xRPA['peranoxx'].'-'.$xRPA['permesxx'].'-'.date( "d", mktime(0, 0, 0, $xRPA['permesxx'] + 1, 0, $xRPA['peranoxx']))?>','<?php echo $xRPA['peranoxx'].'-'.$xRPA['permesxx'].'-'.date( "d", mktime(0, 0, 0, $xRPA['permesxx'] + 1, 0, $xRPA['peranoxx']))?>');
	  				</script>
	  				<?php $i++;
  				}
  			}
			} else {
			  f_Mensaje(__FILE__,__LINE__,"No se encontraron Periodos Anteriores en estado [ABIERTO], Verifique! ");
			}
		?>
	</body>
</html>
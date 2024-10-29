<?php
  namespace openComex;
  /**
   * Marco Ventanas Conceptos Contables - Causacion Automatica a Terceros
   * @author Hair Zabala <hair.zabala@opencomex.com>
   * @package openComex
   */
  include("../../../../libs/php/utility.php");
	
	/**
	 * @Parametro: $gCaso
	 * 1: Ventana ValidWindows de Conceptos Contables
	 * 2: Ventana ValidWindows Conceptos Contablaes Causacion automatica.
	 * */
	switch($gCaso){
		case "1":
			$cRuta = "frcpa119.php?gModo=$gModo&gFunction=$gFunction&gComId=$gComId&gComCod=$gComCod&gCtoId=$gCtoId&gSecuencia=$gSecuencia";
		break;
		case "2":
			$cRuta = "frcpacco.php?gModo=$gModo&gFunction=$gFunction&gComId=$gComId&gComCod=$gComCod&gCcoId=$gCcoId&gSecuencia=$gSecuencia&gTerTipB=$gTerTipB&gTerIdB=$gTerIdB";
		break;
	}
  ?>
<html>
  <frameset rows = "0,0,0,0,*" border = "0" framespacing = "0" frameborder = "0">
    <frame name = "fmpro"    frameborder = "0" border = "0" framespacing = "0" marginheight = "7" marginwidth = "7" noresize scrolling = "no">
    <frame name = "fmpro2"   frameborder = "0" border = "0" framespacing = "0" marginheight = "7" marginwidth = "7" noresize scrolling = "no">
    <frame name = "fmpro3"   frameborder = "0" border = "0" framespacing = "0" marginheight = "7" marginwidth = "7" noresize scrolling = "no">
    <frame name = "fmpro4"   frameborder = "0" border = "0" framespacing = "0" marginheight = "7" marginwidth = "7" noresize scrolling = "no">
    <frame src = "<?php echo $cRuta; ?>" name = "fmwork" frameborder = "0" border = "0" framespacing = "0" marginheight = "0" marginwidth = "0" noresize scrolling = "Yes">
  </frameset>
</html>
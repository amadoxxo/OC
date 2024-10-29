<?php
  namespace openComex;
  /**
   * Marco Ventanas Conceptos Contables - Terceros
   * @author Stefany Bravo <stefany.bravo@opentencologia.com>
   * @package openComex
   */
  include("../../../../libs/php/utility.php");
	
	/**
	 * @Parametro: $gCaso
	 * 1: Ventana ValidWindows de Conceptos Contables
	 * 2: Ventana ValidWindows Conceptos Contablaes Causacion automatica.
	 * */
	
		
			$cRuta = "frpar121.php?gCliCto=$gCliCto&gCampo=$gCampo";
	
// f_Mensaje(__FILE__,__LINE__,$gCliCto."-".$gCampo);
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
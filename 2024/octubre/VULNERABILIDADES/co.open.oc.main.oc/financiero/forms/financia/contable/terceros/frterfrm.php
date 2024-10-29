<?php
  namespace openComex;
	/**
   * Crear Cuentas Bancarias desde Terceros.
   * @author Hair Zabala C. <hair.zabala@opentecnologia.com.co>
   * @package openComex
   */
  $cTitulo = "Cuentas Bancarias Terceros";
  if($kModo01 == "NUEVO"){
    $cRuta   = "../ctabante/frcbanue.php?kModo01=".$kModo01."&gTerId=".$gTerId."&gOrigen=".$gOrigen;
  }else{
    $cRuta   = "../ctabante/frcbanue.php?kModo01=".$kModo01."&gTerId=".$gTerId."&gOrigen=".$gOrigen."&cBanCta=".$cBanCta;
  }
?>

<html>
  <head>
    <title><?php echo $cTitulo ?></title>
  </head>
  <frameset rows = "0,0,0,0,*" border = "0" framespacing = "0" frameborder = "0">
    <frame name = "fmpro"    frameborder = "0" border = "0" framespacing = "0" marginheight = "7" marginwidth = "7" noresize scrolling = "no">
    <frame name = "fmpro2"   frameborder = "0" border = "0" framespacing = "0" marginheight = "7" marginwidth = "7" noresize scrolling = "no">
    <frame name = "fmpro3"   frameborder = "0" border = "0" framespacing = "0" marginheight = "7" marginwidth = "7" noresize scrolling = "no">
    <frame name = "fmpro4"   frameborder = "0" border = "0" framespacing = "0" marginheight = "7" marginwidth = "7" noresize scrolling = "no">
    <frame src = "<?php echo $cRuta; ?>" name = "fmwork" frameborder = "0" border = "0" framespacing = "0" marginheight = "0" marginwidth = "0" noresize scrolling = "auto">
  </frameset>
</html>


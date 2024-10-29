<?php
  namespace openComex;
/**
   * Ver Detalle Procesos en Backgroun de Interfaces
   * --- Descripcion: Ver Detalle Procesos en Backgroun de Interfaces
   * @author Ricardo Alonso Rincón Vega <ricardo.rincon@opentecnologia.com.co>
   * @package openComex
   */
  $cUrl = "frapbver.php?gIdProc=".$gIdProc
?>
<html>
  <head>
    <title>Ver Resultado</title>
  </head>
  <frameset rows = "0,0,0,0,*" border = "0" framespacing = "0" frameborder = "0">
    <frame name = "fmpro"    frameborder = "0" border = "0" framespacing = "0" marginheight = "7" marginwidth = "7" noresize scrolling = "no">
    <frame name = "fmpro2"   frameborder = "0" border = "0" framespacing = "0" marginheight = "7" marginwidth = "7" noresize scrolling = "no">
    <frame name = "fmpro3"   frameborder = "0" border = "0" framespacing = "0" marginheight = "7" marginwidth = "7" noresize scrolling = "no">
    <frame name = "fmpro4"   frameborder = "0" border = "0" framespacing = "0" marginheight = "7" marginwidth = "7" noresize scrolling = "no">
    <frame src = "<?php echo $cUrl ?>" name = "fmwork" frameborder = "0" border = "0" framespacing = "0" marginheight = "0" marginwidth = "0" noresize scrolling = "auto">
  </frameset>
</html>

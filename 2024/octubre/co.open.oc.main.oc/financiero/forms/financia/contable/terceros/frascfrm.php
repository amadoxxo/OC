<?php
  namespace openComex;
/**
   * frame Correos Notificacion Rechazo Revisor Fiscal
   * --- Descripcion: frame Correos Notificacion Rechazo Revisor Fiscal
   * @author Camilo Dulce
   * @package openComex
   */
   
  $cUrl  =  "frteracn.php?gTerId=$gTerId";
  
?>
<html>
  <head>
    <title>Asignar Correos</title>
  </head>
  <frameset rows = "0,0,0,0,*" border = "0" framespacing = "0" frameborder = "0">
    <frame name = "fmpro"    frameborder = "0" border = "0" framespacing = "0" marginheight = "7" marginwidth = "7" noresize scrolling = "no">
    <frame name = "fmpro2"   frameborder = "0" border = "0" framespacing = "0" marginheight = "7" marginwidth = "7" noresize scrolling = "no">
    <frame name = "fmpro3"   frameborder = "0" border = "0" framespacing = "0" marginheight = "7" marginwidth = "7" noresize scrolling = "no">
    <frame name = "fmpro4"   frameborder = "0" border = "0" framespacing = "0" marginheight = "7" marginwidth = "7" noresize scrolling = "no">
    <frame src = "<?php echo $cUrl ?>" name = "fmwork" frameborder = "0" border = "0" framespacing = "0" marginheight = "0" marginwidth = "0" noresize scrolling = "auto">
  </frameset>
</html>
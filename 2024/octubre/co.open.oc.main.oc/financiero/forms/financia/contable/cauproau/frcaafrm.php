<?php
  namespace openComex;
  /**
   * Marco Ajuste Anulacion Automatico
   * @author Camilo Dulce <camilo.dulce@open-eb.co>
   * @package openComex
   */
  $cUrl  = "frcaanue.php?gComId=$gComId";
  $cUrl .= "&gComCod=$gComCod&gComCsc=$gComCsc&gComCsc2=$gComCsc2&gComFec=$gComFec&gRegEst=$gRegEst";
  $title = "Ajuste de Anulacion Automatica";
  ?>
<html>
  <head>
    <title><?php echo $title ?></title>
  </head>
  <frameset rows = "0,0,0,0,*" border = "0" framespacing = "0" frameborder = "0">
    <frame name = "fmpro"    frameborder = "0" border = "0" framespacing = "0" marginheight = "7" marginwidth = "7" noresize scrolling = "no">
    <frame name = "fmpro2"   frameborder = "0" border = "0" framespacing = "0" marginheight = "7" marginwidth = "7" noresize scrolling = "no">
    <frame name = "fmpro3"   frameborder = "0" border = "0" framespacing = "0" marginheight = "7" marginwidth = "7" noresize scrolling = "no">
    <frame name = "fmpro4"   frameborder = "0" border = "0" framespacing = "0" marginheight = "7" marginwidth = "7" noresize scrolling = "no">
    <frame src = "<?php echo $cUrl ?>" name = "fmwork" frameborder = "0" border = "0" framespacing = "0" marginheight = "0" marginwidth = "0" noresize scrolling = "auto">
  </frameset>
</html>
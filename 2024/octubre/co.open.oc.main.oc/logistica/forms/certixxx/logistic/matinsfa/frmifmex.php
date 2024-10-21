<?php
  namespace openComex;
  /**
   * Marco Ver Anexos para el módulo de lógistica, se usa en M.I.F, Certificacion y Pedido.
   * @author Juan Hernandez <juan.hernandez@openits.co>
   * @package opencomex
   * @version 001
   */
  $cUrl  =  "frmifcoi.php";
  $cUrl .=  "?gTipOri=$gTipOri";
  $cUrl .=  "&nCagId=$nCagId";
  $cUrl .=  "&dFecCrea=$dFecCrea";
?>
<frameset rows = "0,0,0,0,*" border = "0" framespacing = "0" frameborder = "0">
  <frame name = "fmpro"    frameborder = "0" border = "0" framespacing = "0" marginheight = "7" marginwidth = "7" noresize scrolling = "no">
  <frame name = "fmpro2"   frameborder = "0" border = "0" framespacing = "0" marginheight = "7" marginwidth = "7" noresize scrolling = "no">
  <frame name = "fmpro3"   frameborder = "0" border = "0" framespacing = "0" marginheight = "7" marginwidth = "7" noresize scrolling = "no">
  <frame name = "fmpro4"   frameborder = "0" border = "0" framespacing = "0" marginheight = "7" marginwidth = "7" noresize scrolling = "no">
  <frame src = "<?php echo $cUrl ?>" name = "fmwork" frameborder = "0" border = "0" framespacing = "0" marginheight = "0" marginwidth = "0" noresize scrolling = "auto">
</frameset>

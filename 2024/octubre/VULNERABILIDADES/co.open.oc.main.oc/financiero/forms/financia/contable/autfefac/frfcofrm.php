<?php
  namespace openComex;

  /**
   * Valid/Window Prefacturas Disponibles.
   * --- Descripcion: Permite Enlazar con el Archivo frafcocx.php para Renderizar Las prefacturas Disponibles.
   * @author Cristian Camilo Segura V <cristian.segura@open-eb.co>
   * @package Opencomex
   */
  
  switch ($gFunction) {
  case "PegarF":
  case "cComCsc":
    $cRuta  = "$gArchivo?gModo=$gModo&gFunction=$gFunction";
    $cRuta .= "&gComCsc=$gComCsc";
    $cRuta .= "&gSecuencia=$gSecuencia";
    $cRuta .= "&gPreAnio=$gPreAnio";
    $cRuta .= "&gProvien=$gProvien";
  break;
  default:
  break;
}
?>
<html>
  <head>
    <title></title>
  </head>
  <frameset rows="*,0" border=0 framespacing=0 frameborder=0>
    <frame src="<?php echo $cRuta ?>"
      name="framework" 
      frameborder=0 
      border=0 
      framespacing=0 
      marginheight=0 
      marginwidth=0 
      scrolling="Si" 
      noresize>
    <frame src="" 
      name="framepro" 
      frameborder=0 
      border=0 
      framespacing=0 
      marginheight=0 
      marginwidth=0 
      scrolling="No" 
      noresize>
  </frameset>
</html>
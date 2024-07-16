<?php 
  switch ($gFunction) {
  case "PegarDo":
  case "cDocId":
    $cRuta  = "$gArchivo?gModo=$gModo&gFunction=$gFunction";
    $cRuta .= "&gDocId=$gDocId";
    $cRuta .= "&gDosSuc=$gDosSuc";
    $cRuta .= "&gSecuencia=$gSecuencia";
  break;
  default:
    //No hace nada
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
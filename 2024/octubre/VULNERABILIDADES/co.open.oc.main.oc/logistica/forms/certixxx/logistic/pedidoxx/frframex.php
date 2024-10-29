<?php
  namespace openComex;
  /**
   * Marco Consulta Inducida tracking de Pedido.
   * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
   * @package opencomex
   * @version 001
   */

  $cParametros  = "cPeriodos="    .$_POST['cPeriodos']."&";
  $cParametros .= "dDesde="       .$_POST['dDesde']."&";
  $cParametros .= "dHasta="       .$_POST['dHasta']."&";
  $cParametros .= "cCcoId="       .$_POST['cCcoId']."&";
  $cParametros .= "cUsrId="       .$_POST['cUsrId']."&";
  $cParametros .= "cEstadoDian="  .$_POST['cEstadoDian']."&";
  $cParametros .= "cConsecutivo=" .$_POST['cConsecutivo']."&";
  $cParametros .= "cDo="          .$_POST['cDo']."&";
  $cParametros .= "cTerId="       .$_POST['cTerId']."&";
  $cParametros .= "cTerId2="      .$_POST['cTerId2'];
  ?>
  <frameset rows="*,0" border=0 framespacing=0 frameborder=0>
    <frame src="frpedcoi.php?<?php echo $cParametros ?>"
      name="fmwork"
      frameborder=0
      border=0
      framespacing=0 
      marginheight=0
      marginwidth=0
      scrolling="Yes"
      noresize
    >

    <frame src="" 
      name="fmpro"
      frameborder=0
      border=0
      framespacing=0
      marginheight=0
      marginwidth=0
      scrolling="No"
      noresize
    >
  </frameset>
<?php
  namespace openComex;
  /**
   * Marco Consulta Inducida tracking de Mis Tickets.
   * @author Cristian Perdomo <cristian.perdomo@openits.co>
   * @package opencomex
   * @version 001
   */
  $cParametros   = "cPeriodos=".$_POST['cPeriodos'] ."&";
  $cParametros  .= "dDesde="   .$_POST['dDesde']    ."&";
  $cParametros  .= "dHasta="   .$_POST['dHasta']    ."&";
  $cParametros  .= "cTicket="  .$_POST['cTicket']   ."&";
  $cParametros  .= "cTiAsun="  .$_POST['cTiAsun']   ."&";
  $cParametros  .= "cCerId="   .$_POST['cCerId']    ."&";
  $cParametros  .= "cPerAno="  .$_POST['cPerAno']   ."&";
  $cParametros  .= "cCliId="   .$_POST['cCliId']    ."&";
  $cParametros  .= "cUsrId="   .$_POST['cUsrId']    ."&";
  $cParametros  .= "cResId="   .$_POST['cResId']    ."&";
  $cParametros  .= "cTipId="   .$_POST['cTipId']    ."&";
  $cParametros  .= "cPriori="  .$_POST['cPriori']   ."&";
  $cParametros  .= "cStatus="  .$_POST['cStatus'];
  ?>
  <frameset rows="*,0" border=0 framespacing=0 frameborder=0>
    <frame src="frmticoi.php?<?php echo $cParametros ?>"
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
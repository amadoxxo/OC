<?php
  namespace openComex;
  /**
 * Marco Consulta Inducida tracking de facturacion.
 * @author Juan José Hernandez <juan.hernandez@openits.co>
 * @package openComex
 * @version 001
 */

$cPar ="cPeriodos=" .$_POST['cPeriodos']."&";
$cPar.="dDesde="    .$_POST['dDesde']."&";
$cPar.="dHasta="    .$_POST['dHasta']."&";
$cPar.="cUsrId="    .$_POST['cUsrId']."&";
$cPar.="cCcoId="    .$_POST['cCcoId']."&";
$cPar.="cDo="       .$_POST['cDo'];
?>
<frameset rows="*,0" border=0 framespacing=0 frameborder=0>
  <frame src="fraptcoi.php?<?php echo $cPar ?>"
    name="fmwork"
    frameborder=0
    border=0
    framespacing=0 
    marginheight=0
    marginwidth=0
    scrolling="Yes"
    noresize>
    
  <frame src="" 
    name="fmpro"
    frameborder=0
    border=0
    framespacing=0
    marginheight=0
    marginwidth=0
    scrolling="No"
    noresize>
</frameset>
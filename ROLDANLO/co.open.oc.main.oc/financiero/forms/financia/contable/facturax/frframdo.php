<?
/**
 * Marco Consulta Inducida tracking de facturacion
 * @author Elian Amado <elian.amado@openits.co>
 * @package openComex
 */

$cPar ="cPeriodos="    .$_POST['cPeriodos']."&";
$cPar.="dDesde="       .$_POST['dDesde']."&";
$cPar.="dHasta="       .$_POST['dHasta']."&";
$cPar.="cCcoId="       .$_POST['cCcoId']."&";
$cPar.="cUsrId="       .$_POST['cUsrId']."&";
$cPar.="cEstadoDian="  .$_POST['cEstadoDian']."&";
$cPar.="cConsecutivo=" .$_POST['cConsecutivo']."&";
$cPar.="cDo="          .$_POST['cDo']."&";
$cPar.="cTerId="       .$_POST['cTerId']."&";
$cPar.="cTerId2="      .$_POST['cTerId2'];
?>
<frameset rows="*,0" border=0 framespacing=0 frameborder=0>
  <frame src="frdoicoi.php?<?php echo $cPar ?>"
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
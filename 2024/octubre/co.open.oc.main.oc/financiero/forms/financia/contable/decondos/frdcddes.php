<?php
  namespace openComex;

/**
 * Graba Desbloquear Consecutivo 2
 * Este programa permite Desbloquear el consecutivo 2
 * @author Jeison Escobar Villanueva <opencomex@opencomex.com>
 * @package openComex
 */
include("../../../../libs/php/utility.php");
include("../../../../libs/php/uticscxx.php");

$cMen = ""; //Variable Mensaje

$nAno = substr($gPeriodo, 0, 4);
$nMes = substr($gPeriodo, -2, 2);
$cConsecutivos = new cConsecutivosContables();

$vDatos['cComId']  = $gComId;
$vDatos['cComCod'] = $gComCod;
$vDatos['cAno']    = $nAno;
$vDatos['cMes']    = $nMes;
$mRetorna = $cConsecutivos->fnDesbloquearConsecutivo($vDatos);

if ($mRetorna[0] == "false") {
    for ($nR = 1; $nR < count($mRetorna); $nR++) {
    	$vAuxMen = explode("~", $mRetorna[$nR]);
    	$cMen .=  $vAuxMen[1]. "\n";
    }
		$cMen = "Se presentaron los siguientes errores al desbloquear consecutivo:\n\n".$cMen;
    f_Mensaje(__FILE__, __LINE__, $cMen);
} else {
	
	for ($nR = 1; $nR < count($mRetorna); $nR++) {
  	$vAuxMen = explode("~", $mRetorna[$nR]);
  	$cMen .=  $vAuxMen[1]. "\n";
  }
	
	if ($cMen != "") {
		$cMen = "Se Realizaron las Siguientes Acciones, por favor verificar los comprobantes actualizados:\n\n".$cMen;
	} else {
		$cMen = "El Comprobante $gComId-$gComCod No se Encuentra Bloquedado.";
	}
	f_Mensaje(__FILE__, __LINE__, $cMen);
} ?>

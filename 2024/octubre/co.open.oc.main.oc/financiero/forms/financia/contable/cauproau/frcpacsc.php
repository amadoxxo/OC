<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");
  
if ($gComId != "" && $gComCod != "" && $gComCsc != "" && $gTerId != "") {
												
	/**
   * Validando que el consecutivo digitado no se encuentre en el sistema
   */
	$nExiste = 0; $cComId = "";
	//si el comprobante es de ejecucion MANUAL
	if ($gComTco != "AUTOMATICA") {
		//Valido que el comprobante no este duplicado en el sistema.
		 $qValCsc  = "SELECT * ";
		 $qValCsc .= "FROM $cAlfa.fcoc".date("Y")." ";
		 $qValCsc .= "WHERE ";
		 $qValCsc .= "comidxxx = \"$gComId\"   AND ";
		 $qValCsc .= "comcodxx = \"$gComCod\"  AND ";
		 $qValCsc .= "comcscxx = \"$gComCsc\"  AND ";
		 $qValCsc .= "terid2xx = \"$gTerId\"   AND ";
		 $qValCsc .= "regestxx = \"ACTIVO\" LIMIT 0,1";
		 $xValCsc  = f_MySql("SELECT","",$qValCsc,$xConexion01,"");
		 //f_Mensaje(__FILE__,__LINE__,$qValCsc." ~ ".mysql_num_rows($xValCsc));
		 if (mysql_num_rows($xValCsc) > 0) {
				$xRVC = mysql_fetch_array($xValCsc);
				$nExiste = 1;
				$cComId = "{$xRVC['comidxxx']}-{$xRVC['comcodxx']}-{$xRVC['comcscxx']}-{$xRVC['comcsc2x']}";
				f_Mensaje(__FILE__,__LINE__,"El Comprobante $gComId-$gComCod-$gComCsc ya existe para este Proveedor en el documento $cComId, Verifique.\n");
		 }
	} 
}?>
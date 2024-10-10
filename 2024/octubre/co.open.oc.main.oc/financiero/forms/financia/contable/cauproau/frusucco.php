<?php
namespace openComex;
include("../../../../libs/php/utility.php");
?>

<script language="javascript">
  parent.fmwork.document.forms['frgrm']['cUsrId'].options.length = 0;
  parent.fmwork.document.forms['frgrm']['cUsrId'].options[0] = new Option('USUARIOS','ALL');
</script>
<?php
  /***** Sql para Buscar Periodos Abiertos *****/
  $zSqlPdo  = "SELECT CONCAT(peranoxx,permesxx) AS periodo ";
  $zSqlPdo .= "FROM $cAlfa.fpar0122 ";
  $zSqlPdo .= "WHERE ";
  $zSqlPdo .= "comidxxx = \"P\"  AND ";
  $zSqlPdo .= "regestxx = \"ABIERTO\" ";
  $zSqlPdo .= "GROUP BY peranoxx,permesxx";
  $zCrsPdo  = f_MySql("SELECT","",$zSqlPdo,$xConexion01,"");
  //f_Mensaje(__FILE__,__LINE__,$zSqlPdo."~".mysql_num_rows($zCrsPdo));
  $cPerAbi = "";
  while ($zRPdo = mysql_fetch_array($zCrsPdo)) {
   $cPerAbi .= "\"".$zRPdo['periodo']."\"".",";
  }
  $cPerAbi = substr($cPerAbi,0,(strlen($cPerAbi)-1));
  /***** Fin de Sql para Buscar Periodos Abiertos *****/

  $cCcoIdAux = explode("~",$gCcoId);
  
  $mSqlUsr = array();
	for ($iAno=substr($gDesde,0,4);$iAno<=substr($gHasta,0,4);$iAno++) { // Recorro desde el a�o de inicio hasta e a�o de fin de la consulta
		$qSqlUsr  = "SELECT DISTINCT regusrxx ";
		$qSqlUsr .= "FROM $cAlfa.fcoc$iAno ";
		$qSqlUsr .= "WHERE ";
		$qSqlUsr .= "comidxxx = \"P\"  AND ";
		$qSqlUsr .= "comperxx IN ($cPerAbi) AND ";
		if ($cCcoIdAux[1] <> "") {
		  $qSqlUsr .= "ccoidxxx = \"{$cCcoIdAux[1]}\" AND ";
		}
		
		if (($_COOKIE["kUsrId"] == 'ADMIN' || $cUsrInt == "SI") || ($cAlfa != 'DEOPENWORK' && $cAlfa != 'OPENWORK' && $cAlfa != 'TEOPENWORK')) {
			$qSqlUsr .= "$cAlfa.fcoc$iAno.regusrxx <> \"\" ";
		} else {
			$qSqlUsr .= "$cAlfa.fcoc$iAno.regusrxx = \"{$_COOKIE["kUsrId"]}\" ";?>
			 <script language="javascript">
	  		parent.fmwork.document.forms['frgrm']['cUsrId'].remove(0);
	 		 </script>
	 	<?php
		}
		
		$qSqlUsr .= "GROUP BY regusrxx ";
		$qSqlUsr .= "ORDER BY regusrxx ";
		$xSqlUsr = f_MySql("SELECT","",$qSqlUsr,$xConexion01,"");
		//f_Mensaje(__FILE__,__LINE__,$qSqlUsr."~".mysql_num_rows($xSqlUsr));
		
		while ($xRSU = mysql_fetch_array($xSqlUsr)) {
		  $mSqlUsr[count($mSqlUsr)] = $xRSU;
		}
	} ## for ($iAno=substr($gDesde,0,4);$iAno<=substr($gHasta,0,4);$iAno++) { ##
	                        
	if (count($mSqlUsr) > 0) {
		$mMatrizUsr = array();
		$i = 0;
		for ($j=0;$j<count($mSqlUsr);$j++) {
			$qUsrNom  = "SELECT USRIDXXX,USRNOMXX FROM $cAlfa.SIAI0003 WHERE USRIDXXX = \"{$mSqlUsr[$j]['regusrxx']}\" LIMIT 0,1";
			$xUsrNom = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
			
			if (mysql_num_rows($xUsrNom) > 0) {
				while ($xRUN = mysql_fetch_array($xUsrNom)) {
					$mMatrizUsr[$i]['usridxxx'] = $mSqlUsr[$j]['regusrxx'];
					$mMatrizUsr[$i]['usrnomxx'] = $xRUN['USRNOMXX'];
					$i++;
				}
			} else {
				$mMatrizUsr[$i]['usridxxx'] = $mSqlUsr[$j]['regusrxx'];
				$mMatrizUsr[$i]['usrnomxx'] = $xSqlUsr[$i]['regusrxx']."_SIN NOMBRE_".($i+1);
				$i++;
			}
		}
	}
	
	$mMatrizUsr = f_Sort_Array_By_Field($mMatrizUsr,"usrnomxx","ASC_AZ");
	for ($i=0;$i<count($mMatrizUsr);$i++) { ?>
	 <script language="javascript">
	  parent.fmwork.document.forms['frgrm']['cUsrId'].options[<?php echo ($i+1) ?>] = new Option('<?php echo $mMatrizUsr[$i]['usrnomxx'] ?>','<?php echo $mMatrizUsr[$i]['usridxxx']?>');
	 </script>
	<?php } ?>
	
	<script language="javascript">
  parent.fmwork.document.forms['frgrm']['cUsrId'].value = "<?php echo $gUsrId ?>";
  </script>